/**
 * Quote Manager for 3D file uploads and pricing
 */

class QuoteManager {
    constructor(options = {}) {
        this.options = {
            apiBaseUrl: options.apiBaseUrl || '/api/quote',
            csrfToken: options.csrfToken || document.querySelector('meta[name="csrf-token"]')?.content,
            maxFiles: options.maxFiles || 10,
            maxFileSize: options.maxFileSize || 52428800, // 50MB
            onFileAdded: options.onFileAdded || (() => {}),
            onFileAnalyzed: options.onFileAnalyzed || (() => {}),
            onFileRemoved: options.onFileRemoved || (() => {}),
            onTotalUpdated: options.onTotalUpdated || (() => {}),
            onError: options.onError || ((error) => console.error(error)),
            ...options
        };

        this.quoteId = null;
        this.files = new Map();  // fileId => fileData
        this.materials = [];
        this.currentMaterial = 'pla';

        this.init();
    }

    async init() {
        try {
            await this.loadMaterials();
        } catch (error) {
            this.options.onError('Failed to load materials: ' + error.message);
        }
    }

    async loadMaterials() {
        const response = await fetch(`${this.options.apiBaseUrl}/materials`);
        const data = await response.json();

        if (data.success) {
            this.materials = data.data;
            return this.materials;
        } else {
            throw new Error('Failed to load materials');
        }
    }

    async uploadFile(file) {
        // Validate file
        if (!this.validateFile(file)) {
            return null;
        }

        // Check file limit
        if (this.files.size >= this.options.maxFiles) {
            this.options.onError(`Maximum ${this.options.maxFiles} files allowed`);
            return null;
        }

        try {
            const formData = new FormData();
            formData.append('file', file);
            if (this.quoteId) {
                formData.append('quote_id', this.quoteId);
            }

            const response = await fetch(`${this.options.apiBaseUrl}/upload`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.options.csrfToken,
                    'Accept': 'application/json',
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                if (!this.quoteId) {
                    this.quoteId = data.data.quote_id;
                }

                // Store file data
                const fileData = {
                    id: data.data.file_id,
                    name: data.data.file_name,
                    url: data.data.file_url,
                    type: data.data.file_type,
                    file: file,
                    status: 'uploaded',
                    analysis: null,
                    pricing: null,
                    material: this.currentMaterial,
                    quantity: 1
                };

                this.files.set(data.data.file_id, fileData);
                this.options.onFileAdded(fileData);

                return fileData;
            } else {
                throw new Error(data.message || 'Upload failed');
            }

        } catch (error) {
            this.options.onError('Upload error: ' + error.message);
            return null;
        }
    }

    async analyzeFile(fileId, geometryData) {
        const fileData = this.files.get(fileId);
        if (!fileData) {
            this.options.onError('File not found');
            return null;
        }

        try {
            const response = await fetch(`${this.options.apiBaseUrl}/analyze`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.options.csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    file_id: fileId,
                    volume_mm3: geometryData.volume_mm3,
                    width_mm: geometryData.width_mm,
                    height_mm: geometryData.height_mm,
                    depth_mm: geometryData.depth_mm,
                    surface_area_mm2: geometryData.surface_area_mm2 || 0,
                    material: fileData.material,
                    quantity: fileData.quantity
                })
            });

            const data = await response.json();

            if (data.success) {
                fileData.status = 'analyzed';
                fileData.analysis = data.data.geometry;
                fileData.pricing = {
                    unit_price: data.data.unit_price,
                    total_price: data.data.total_price,
                    breakdown: data.data.breakdown,
                    production: data.data.production
                };

                this.files.set(fileId, fileData);
                this.options.onFileAnalyzed(fileData);
                this.updateTotal();

                return fileData;
            } else {
                throw new Error(data.message || 'Analysis failed');
            }

        } catch (error) {
            this.options.onError('Analysis error: ' + error.message);
            return null;
        }
    }

    async updateFileMaterial(fileId, material, quantity = null) {
        const fileData = this.files.get(fileId);
        if (!fileData || !fileData.analysis) {
            return null;
        }

        fileData.material = material;
        if (quantity !== null) {
            fileData.quantity = quantity;
        }

        // Re-analyze with new material/quantity
        return await this.analyzeFile(fileId, fileData.analysis);
    }

    async removeFile(fileId) {
        try {
            const response = await fetch(`${this.options.apiBaseUrl}/file/${fileId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': this.options.csrfToken,
                    'Accept': 'application/json',
                }
            });

            const data = await response.json();

            if (data.success) {
                const fileData = this.files.get(fileId);
                this.files.delete(fileId);
                this.options.onFileRemoved(fileData);
                this.updateTotal();
                return true;
            } else {
                throw new Error(data.message || 'Delete failed');
            }

        } catch (error) {
            this.options.onError('Delete error: ' + error.message);
            return false;
        }
    }

    async submitQuote(customerInfo = {}) {
        if (this.files.size === 0) {
            this.options.onError('No files to submit');
            return null;
        }

        if (!this.quoteId) {
            this.options.onError('No quote ID');
            return null;
        }

        try {
            const response = await fetch(`${this.options.apiBaseUrl}/submit`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.options.csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    quote_id: this.quoteId,
                    ...customerInfo
                })
            });

            const data = await response.json();

            if (data.success) {
                return data.data;
            } else {
                throw new Error(data.message || 'Submission failed');
            }

        } catch (error) {
            this.options.onError('Submission error: ' + error.message);
            return null;
        }
    }

    validateFile(file) {
        const validExtensions = ['stl', 'obj', 'ply'];
        const extension = file.name.split('.').pop().toLowerCase();

        if (!validExtensions.includes(extension)) {
            this.options.onError(`Invalid file type. Allowed: ${validExtensions.join(', ')}`);
            return false;
        }

        if (file.size > this.options.maxFileSize) {
            const maxSizeMB = (this.options.maxFileSize / 1048576).toFixed(0);
            this.options.onError(`File too large. Maximum size: ${maxSizeMB}MB`);
            return false;
        }

        return true;
    }

    updateTotal() {
        let total = 0;
        this.files.forEach(fileData => {
            if (fileData.pricing) {
                total += fileData.pricing.total_price;
            }
        });

        this.options.onTotalUpdated(total, this.files.size);
    }

    getTotal() {
        let total = 0;
        this.files.forEach(fileData => {
            if (fileData.pricing) {
                total += fileData.pricing.total_price;
            }
        });
        return total;
    }

    getFiles() {
        return Array.from(this.files.values());
    }

    getFile(fileId) {
        return this.files.get(fileId);
    }

    setDefaultMaterial(material) {
        this.currentMaterial = material;
    }

    clear() {
        this.files.clear();
        this.quoteId = null;
        this.updateTotal();
    }
}

// Export for use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = QuoteManager;
}
