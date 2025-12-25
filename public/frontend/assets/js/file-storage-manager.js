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
        this.currentFileIds = []; // Array to store multiple file IDs
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
                    console.log('✅ File saved to IndexedDB:', fileId);
                    console.log('⏰ Expires in 72 hours:', new Date(expiryTime));
                    console.log('📤 Uploading to server...');

                    // CRITICAL: Upload to server FIRST, then update URL with server ID
                    this.saveToServer(fileRecord).then(result => {
                        console.log('✅ File successfully uploaded to server for global sharing');
                        console.log('   Server File ID:', result.fileId);
                        console.log('   🔗 This file can now be shared across browsers and devices!');

                        // ALWAYS use server's file ID (even if same as local)
                        this.currentFileId = result.fileId;
                        
                        // Only update URL if this file isn't already in the URL
                        // This prevents overwriting the viewer parameter during save & calculate
                        const currentUrl = new URL(window.location.href);
                        const filesParam = currentUrl.searchParams.get('files');
                        const fileParam = currentUrl.searchParams.get('file');
                        const fileAlreadyInUrl = (filesParam && filesParam.includes(result.fileId)) || 
                                                 (fileParam && fileParam === result.fileId);
                        
                        if (!fileAlreadyInUrl) {
                            this.updateURL(result.fileId);
                            console.log('✅ URL updated with server file ID:', result.fileId);
                        } else {
                            console.log('ℹ️ File already in URL, skipping URL update to preserve parameters');
                        }

                        // Update IndexedDB record with server file ID if different
                        if (result.fileId !== fileId) {
                            console.log('🔄 Syncing IndexedDB: local ID', fileId, '→ server ID', result.fileId);
                            const updateTransaction = this.db.transaction([this.storeName], 'readwrite');
                            const updateStore = updateTransaction.objectStore(this.storeName);
                            updateStore.delete(fileId).onsuccess = () => {
                                fileRecord.id = result.fileId; // Update the 'id' field (keyPath)
                                updateStore.add(fileRecord).onsuccess = () => {
                                    console.log('✅ IndexedDB synced with server file ID');
                                };
                            };
                        }

                        resolve(result.fileId); // Return SERVER file ID
                    }).catch(err => {
                        console.error('❌ Server upload failed:', err);
                        console.error('   Error message:', err.message);
                        console.warn('⚠️ Falling back to local-only storage');
                        console.warn('⚠️ File CANNOT be shared with other browsers');

                        // Fallback: use local ID if server fails
                        this.currentFileId = fileId;
                        
                        // Only update URL if this file isn't already in the URL
                        const currentUrl = new URL(window.location.href);
                        const filesParam = currentUrl.searchParams.get('files');
                        const fileParam = currentUrl.searchParams.get('file');
                        const fileAlreadyInUrl = (filesParam && filesParam.includes(fileId)) || 
                                                 (fileParam && fileParam === fileId);
                        
                        if (!fileAlreadyInUrl) {
                            this.updateURL(fileId);
                        } else {
                            console.log('ℹ️ File already in URL (fallback), skipping URL update to preserve parameters');
                        }

                        // Show user-visible warning
                        if (typeof showNotification === 'function') {
                            showNotification('⚠️ File saved locally only. Server upload failed - sharing may not work.', 'warning');
                        }

                        resolve(fileId); // Return local file ID as fallback
                    });
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
            console.log('📤 Uploading file to server...');
            console.log('   File name:', fileRecord.fileName);
            console.log('   File size:', (fileRecord.fileData.byteLength / 1024 / 1024).toFixed(2), 'MB');

            // Convert ArrayBuffer to base64
            const base64Data = this.arrayBufferToBase64(fileRecord.fileData);
            console.log('   Base64 length:', base64Data.length);

            console.log('   Making POST request to: /api/3d-files/store');

            const response = await fetch('/api/3d-files/store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    file: base64Data,
                    fileName: fileRecord.fileName,
                    cameraState: fileRecord.edits?.camera ? JSON.stringify(fileRecord.edits.camera) : null,
                    metadata: fileRecord.metadata ? JSON.stringify(fileRecord.metadata) : null
                })
            });

            console.log('   Response status:', response.status, response.statusText);

            if (!response.ok) {
                const errorText = await response.text();
                console.error('❌ Server response error:', response.status, errorText);
                throw new Error(`Server error: ${response.status} - ${errorText}`);
            }

            const result = await response.json();

            if (result.success) {
                console.log('☁️ File uploaded to server:', result.fileId);
                console.log('   Expires:', new Date(result.expiryTime));
                // Store server file ID mapping
                this.serverFileId = result.fileId;
                return result;
            } else {
                throw new Error(result.message || 'Upload failed');
            }
        } catch (error) {
            console.error('❌ Failed to save to server:', error);
            console.error('   Error details:', error.message);
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
        console.log('🔧 updateURL called with fileId:', fileId);
        console.trace('Call stack:'); // Show where this was called from
        
        // Only update URL if fileId is valid
        if (!fileId || fileId === 'null' || fileId === 'undefined') {
            console.warn('⚠️ Attempted to update URL with invalid fileId:', fileId);
            return;
        }

        // Add to currentFileIds array if not already there
        if (!this.currentFileIds.includes(fileId)) {
            this.currentFileIds.push(fileId);
        }

        const url = new URL(window.location.href);
        console.log('📍 Current URL before update:', window.location.href);

        // IMPORTANT: Preserve the viewer parameter if it exists
        const viewerParam = url.searchParams.get('viewer');
        console.log('🔍 Viewer param detected:', viewerParam);

        // Support both single file and multiple files
        if (this.currentFileIds.length === 1) {
            // Single file: use ?file=xxx (backward compatible)
            url.searchParams.set('file', fileId);
            url.searchParams.delete('files'); // Remove multi-file param if exists
        } else {
            // Multiple files: use ?files=xxx,yyy,zzz
            url.searchParams.set('files', this.currentFileIds.join(','));
            url.searchParams.delete('file'); // Remove single file param
        }

        // Restore viewer parameter if it was present
        if (viewerParam) {
            url.searchParams.set('viewer', viewerParam);
            console.log('✅ Viewer parameter preserved:', viewerParam);
        } else {
            console.warn('⚠️ NO VIEWER PARAMETER FOUND IN CURRENT URL!');
        }

        console.log('📍 New URL after update:', url.toString());
        window.history.pushState({ fileIds: this.currentFileIds }, '', url.toString());
        console.log('🔗 URL updated:', this.currentFileIds.length === 1 ? '1 file' : `${this.currentFileIds.length} files`);
    }

    /**
     * Get file ID(s) from URL
     */
    getFileIdFromURL() {
        const url = new URL(window.location.href);

        // Check for multiple files first
        const filesParam = url.searchParams.get('files');
        if (filesParam) {
            const fileIds = filesParam.split(',').filter(id => id.trim());
            this.currentFileIds = fileIds;
            return fileIds; // Return array
        }

        // Fallback to single file (backward compatible)
        const fileParam = url.searchParams.get('file');
        if (fileParam) {
            this.currentFileIds = [fileParam];
            return fileParam; // Return string for backward compatibility
        }

        return null;
    }

    /**
     * Generate shareable link
     */
    getShareableLink(fileId) {
        // Use current session's files if no specific fileId provided
        const fileIds = this.currentFileIds.length > 0 ? this.currentFileIds : (fileId ? [fileId] : [this.currentFileId]);

        // Validate file IDs
        const validFileIds = fileIds.filter(id => id && id !== 'null' && id !== 'undefined');

        if (validFileIds.length === 0) {
            console.error('❌ Cannot generate shareable link: No valid file IDs');
            return window.location.origin + window.location.pathname;
        }

        const url = new URL(window.location.origin + window.location.pathname);

        // Preserve viewer parameter if it exists in current URL
        const currentUrl = new URL(window.location.href);
        const viewerParam = currentUrl.searchParams.get('viewer');
        if (viewerParam) {
            url.searchParams.set('viewer', viewerParam);
            console.log('✅ Viewer parameter included in share link:', viewerParam);
        }

        if (validFileIds.length === 1) {
            // Single file: use ?file=xxx
            url.searchParams.set('file', validFileIds[0]);
            console.log('📋 Generated share link for 1 file');
        } else {
            // Multiple files: use ?files=xxx,yyy,zzz
            url.searchParams.set('files', validFileIds.join(','));
            console.log(`📋 Generated share link for ${validFileIds.length} files`);
        }

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
