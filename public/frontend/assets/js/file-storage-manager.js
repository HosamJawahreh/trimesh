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

            // Save to IndexedDB (local storage)
            const transaction = this.db.transaction([this.storeName], 'readwrite');
            const objectStore = transaction.objectStore(this.storeName);
            const request = objectStore.add(fileRecord);

            return new Promise((resolve, reject) => {
                request.onsuccess = () => {
                    this.currentFileId = fileId;
                    console.log('✅ File saved to IndexedDB:', fileId);
                    console.log('⏰ Expires in 72 hours:', new Date(expiryTime));
                    this.updateURL(fileId);
                    
                    // Also save to server for global sharing
                    this.saveToServer(fileRecord).catch(err => {
                        console.warn('⚠️ Could not save to server, sharing will be local only:', err.message);
                    });
                    
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
     * Save file to server for global sharing
     */
    async saveToServer(fileRecord) {
        try {
            // Convert ArrayBuffer to base64
            const base64Data = this.arrayBufferToBase64(fileRecord.fileData);
            
            const response = await fetch('/api/3d-files/store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({
                    file: base64Data,
                    fileName: fileRecord.fileName,
                    cameraState: JSON.stringify(fileRecord.edits?.camera),
                    metadata: JSON.stringify(fileRecord.metadata)
                })
            });

            const result = await response.json();
            
            if (result.success) {
                console.log('✅ File also saved to server for global sharing');
                // Store server file ID mapping
                this.serverFileId = result.fileId;
                return result;
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('❌ Failed to save to server:', error);
            throw error;
        }
    }

    /**
     * Convert ArrayBuffer to Base64
     */
    arrayBufferToBase64(buffer) {
        let binary = '';
        const bytes = new Uint8Array(buffer);
        const len = bytes.byteLength;
        for (let i = 0; i < len; i++) {
            binary += String.fromCharCode(bytes[i]);
        }
        return btoa(binary);
    }

    /**
     * Load file from IndexedDB or Server
     */
    async loadFile(fileId) {
        try {
            // First try loading from IndexedDB (local)
            const transaction = this.db.transaction([this.storeName], 'readonly');
            const objectStore = transaction.objectStore(this.storeName);
            const request = objectStore.get(fileId);

            return new Promise(async (resolve, reject) => {
                request.onsuccess = async () => {
                    const fileRecord = request.result;

                    if (!fileRecord) {
                        // Not found locally, try loading from server
                        console.log('📡 File not found locally, trying server...');
                        const serverFile = await this.loadFromServer(fileId).catch(() => null);
                        if (serverFile) {
                            console.log('✅ File loaded from server');
                            this.currentFileId = fileId;
                            resolve(serverFile);
                        } else {
                            console.warn('⚠️ File not found locally or on server:', fileId);
                            resolve(null);
                        }
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
     * Load file from server
     */
    async loadFromServer(fileId) {
        try {
            const response = await fetch(`/api/3d-files/${fileId}`);
            const result = await response.json();

            if (!result.success) {
                throw new Error(result.message);
            }

            // Convert base64 back to ArrayBuffer
            const binary = atob(result.fileData);
            const bytes = new Uint8Array(binary.length);
            for (let i = 0; i < binary.length; i++) {
                bytes[i] = binary.charCodeAt(i);
            }
            const arrayBuffer = bytes.buffer;

            // Create file record in same format as IndexedDB
            const fileRecord = {
                id: result.fileId,
                fileName: result.fileName,
                fileData: arrayBuffer,
                uploadTime: result.uploadTime,
                expiryTime: result.expiryTime,
                edits: {
                    camera: result.cameraState,
                    transformations: [],
                    repairs: [],
                    measurements: []
                },
                metadata: result.metadata || {}
            };

            // Optionally save to local IndexedDB for caching
            try {
                const transaction = this.db.transaction([this.storeName], 'readwrite');
                const objectStore = transaction.objectStore(this.storeName);
                objectStore.add(fileRecord);
                console.log('💾 File cached locally from server');
            } catch (e) {
                // Ignore if already exists
            }

            return fileRecord;
        } catch (error) {
            console.error('❌ Failed to load from server:', error);
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
                request.onsuccess = async () => {
                    console.log('✅ Edits saved locally:', editType);
                    
                    // If it's a camera edit, also sync to server
                    if (editType === 'camera' && this.serverFileId) {
                        await this.syncCameraToServer(fileId, editData);
                    }
                    
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
     * Sync camera state to server
     */
    async syncCameraToServer(fileId, cameraState) {
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            if (!csrfToken) {
                console.warn('⚠️ No CSRF token found, skipping server sync');
                return;
            }

            const response = await fetch(`/api/3d-files/${fileId}/camera`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ cameraState })
            });

            const result = await response.json();
            if (result.success) {
                console.log('☁️ Camera state synced to server');
            }
        } catch (error) {
            console.warn('⚠️ Failed to sync camera to server:', error);
            // Don't throw - local save already succeeded
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
