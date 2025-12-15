/**
 * FileStorageManager - Manages 3D file storage in browser IndexedDB
 * - Stores files locally (no server space used!)
 * - 72-hour automatic expiry
 * - Edit history tracking
 * - Camera state persistence
 * - Shareable link generation
 */

class FileStorageManager {
    constructor() {
        this.dbName = 'Trimesh3DStorage';
        this.dbVersion = 1;
        this.storeName = 'files';
        this.db = null;
        this.currentFileId = null;
        this.EXPIRY_HOURS = 72;
    }

    /**
     * Initialize IndexedDB
     */
    async init() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open(this.dbName, this.dbVersion);

            request.onerror = () => reject(request.error);
            request.onsuccess = () => {
                this.db = request.result;
                console.log('✅ IndexedDB initialized');
                this.cleanExpiredFiles(); // Clean on init
                resolve(this.db);
            };

            request.onupgradeneeded = (event) => {
                const db = event.target.result;

                // Create object store if it doesn't exist
                if (!db.objectStoreNames.contains(this.storeName)) {
                    const objectStore = db.createObjectStore(this.storeName, { keyPath: 'id' });
                    objectStore.createIndex('uploadTime', 'uploadTime', { unique: false });
                    objectStore.createIndex('expiryTime', 'expiryTime', { unique: false });
                    console.log('📦 IndexedDB object store created');
                }
            };
        });
    }

    /**
     * Generate unique file ID
     */
    generateFileId() {
        return 'file_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }

    /**
     * Save file to IndexedDB
     */
    async saveFile(fileData, fileName, geometry, mesh) {
        try {
            const fileId = this.generateFileId();
            const uploadTime = Date.now();
            const expiryTime = uploadTime + (this.EXPIRY_HOURS * 60 * 60 * 1000);

            // Create file object
            const fileRecord = {
                id: fileId,
                fileName: fileName,
                fileData: fileData, // ArrayBuffer or Blob
                uploadTime: uploadTime,
                expiryTime: expiryTime,
                edits: {
                    camera: null,
                    transformations: [],
                    repairs: [],
                    measurements: []
                },
                metadata: {
                    fileSize: fileData.size || fileData.byteLength,
                    fileType: fileName.split('.').pop().toUpperCase(),
                    vertexCount: geometry ? geometry.attributes.position.count : 0,
                    volume: null
                }
            };

            // Save to IndexedDB
            const transaction = this.db.transaction([this.storeName], 'readwrite');
            const objectStore = transaction.objectStore(this.storeName);
            const request = objectStore.add(fileRecord);

            return new Promise((resolve, reject) => {
                request.onsuccess = () => {
                    this.currentFileId = fileId;
                    console.log('✅ File saved to IndexedDB:', fileId);
                    console.log('⏰ Expires in 72 hours:', new Date(expiryTime));
                    this.updateURL(fileId);
                    resolve(fileId);
                };
                request.onerror = () => reject(request.error);
            });
        } catch (error) {
            console.error('❌ Error saving file:', error);
            throw error;
        }
    }

    /**
     * Load file from IndexedDB
     */
    async loadFile(fileId) {
        try {
            const transaction = this.db.transaction([this.storeName], 'readonly');
            const objectStore = transaction.objectStore(this.storeName);
            const request = objectStore.get(fileId);

            return new Promise((resolve, reject) => {
                request.onsuccess = () => {
                    const fileRecord = request.result;

                    if (!fileRecord) {
                        console.warn('⚠️ File not found:', fileId);
                        resolve(null);
                        return;
                    }

                    // Check if expired
                    if (Date.now() > fileRecord.expiryTime) {
                        console.warn('⏰ File expired:', fileId);
                        this.deleteFile(fileId);
                        resolve(null);
                        return;
                    }

                    console.log('✅ File loaded from IndexedDB:', fileId);
                    this.currentFileId = fileId;
                    resolve(fileRecord);
                };
                request.onerror = () => reject(request.error);
            });
        } catch (error) {
            console.error('❌ Error loading file:', error);
            throw error;
        }
    }

    /**
     * Update file edits
     */
    async updateEdits(fileId, editType, editData) {
        try {
            const fileRecord = await this.loadFile(fileId);
            if (!fileRecord) return false;

            // Add edit to history
            switch (editType) {
                case 'camera':
                    fileRecord.edits.camera = editData;
                    break;
                case 'transform':
                    fileRecord.edits.transformations.push({
                        timestamp: Date.now(),
                        data: editData
                    });
                    break;
                case 'repair':
                    fileRecord.edits.repairs.push({
                        timestamp: Date.now(),
                        type: editData.type
                    });
                    break;
                case 'measurement':
                    fileRecord.edits.measurements.push({
                        timestamp: Date.now(),
                        data: editData
                    });
                    break;
            }

            // Update in IndexedDB
            const transaction = this.db.transaction([this.storeName], 'readwrite');
            const objectStore = transaction.objectStore(this.storeName);
            const request = objectStore.put(fileRecord);

            return new Promise((resolve, reject) => {
                request.onsuccess = () => {
                    console.log('✅ Edits saved:', editType);
                    resolve(true);
                };
                request.onerror = () => reject(request.error);
            });
        } catch (error) {
            console.error('❌ Error updating edits:', error);
            return false;
        }
    }

    /**
     * Delete file from IndexedDB
     */
    async deleteFile(fileId) {
        try {
            const transaction = this.db.transaction([this.storeName], 'readwrite');
            const objectStore = transaction.objectStore(this.storeName);
            const request = objectStore.delete(fileId);

            return new Promise((resolve, reject) => {
                request.onsuccess = () => {
                    console.log('🗑️ File deleted:', fileId);
                    resolve(true);
                };
                request.onerror = () => reject(request.error);
            });
        } catch (error) {
            console.error('❌ Error deleting file:', error);
            return false;
        }
    }

    /**
     * Clean expired files (auto-cleanup)
     */
    async cleanExpiredFiles() {
        try {
            const transaction = this.db.transaction([this.storeName], 'readwrite');
            const objectStore = transaction.objectStore(this.storeName);
            const index = objectStore.index('expiryTime');
            const request = index.openCursor();

            let deletedCount = 0;
            const now = Date.now();

            request.onsuccess = (event) => {
                const cursor = event.target.result;
                if (cursor) {
                    if (cursor.value.expiryTime < now) {
                        objectStore.delete(cursor.value.id);
                        deletedCount++;
                    }
                    cursor.continue();
                } else {
                    if (deletedCount > 0) {
                        console.log(`🧹 Cleaned ${deletedCount} expired file(s)`);
                    }
                }
            };
        } catch (error) {
            console.error('❌ Error cleaning expired files:', error);
        }
    }

    /**
     * Get all stored files
     */
    async getAllFiles() {
        try {
            const transaction = this.db.transaction([this.storeName], 'readonly');
            const objectStore = transaction.objectStore(this.storeName);
            const request = objectStore.getAll();

            return new Promise((resolve, reject) => {
                request.onsuccess = () => {
                    const files = request.result.filter(file => Date.now() < file.expiryTime);
                    resolve(files);
                };
                request.onerror = () => reject(request.error);
            });
        } catch (error) {
            console.error('❌ Error getting files:', error);
            return [];
        }
    }

    /**
     * Update URL with file ID
     */
    updateURL(fileId) {
        // Only update URL if fileId is valid
        if (!fileId || fileId === 'null' || fileId === 'undefined') {
            console.warn('⚠️ Attempted to update URL with invalid fileId:', fileId);
            return;
        }

        const url = new URL(window.location.href);
        url.searchParams.set('file', fileId);
        window.history.pushState({ fileId }, '', url.toString());
    }

    /**
     * Get file ID from URL
     */
    getFileIdFromURL() {
        const url = new URL(window.location.href);
        return url.searchParams.get('file');
    }

    /**
     * Generate shareable link
     */
    getShareableLink(fileId) {
        const validFileId = fileId || this.currentFileId;

        // Validate file ID before generating link
        if (!validFileId || validFileId === 'null' || validFileId === 'undefined') {
            console.error('❌ Cannot generate shareable link: Invalid file ID');
            return window.location.origin + window.location.pathname;
        }

        const url = new URL(window.location.href);
        url.searchParams.set('file', validFileId);
        return url.toString();
    }

    /**
     * Get time remaining until expiry
     */
    getTimeRemaining(fileRecord) {
        const now = Date.now();
        const remaining = fileRecord.expiryTime - now;

        if (remaining <= 0) return 'Expired';

        const hours = Math.floor(remaining / (1000 * 60 * 60));
        const minutes = Math.floor((remaining % (1000 * 60 * 60)) / (1000 * 60));

        if (hours > 24) {
            const days = Math.floor(hours / 24);
            return `${days} day${days > 1 ? 's' : ''} remaining`;
        }
        return `${hours}h ${minutes}m remaining`;
    }

    /**
     * Save camera state
     */
    async saveCameraState(cameraData) {
        if (!this.currentFileId) return false;
        return await this.updateEdits(this.currentFileId, 'camera', cameraData);
    }

    /**
     * Save transformation
     */
    async saveTransformation(transformData) {
        if (!this.currentFileId) return false;
        return await this.updateEdits(this.currentFileId, 'transform', transformData);
    }

    /**
     * Save repair action
     */
    async saveRepair(repairType) {
        if (!this.currentFileId) return false;
        return await this.updateEdits(this.currentFileId, 'repair', { type: repairType });
    }

    /**
     * Save measurement
     */
    async saveMeasurement(measurementData) {
        if (!this.currentFileId) return false;
        return await this.updateEdits(this.currentFileId, 'measurement', measurementData);
    }
}

// Create global instance
window.fileStorageManager = new FileStorageManager();
