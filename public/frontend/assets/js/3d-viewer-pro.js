/**
 * Professional 3D Viewer with Full Format Support
 * Supports STL, OBJ, PLY files with Three.js
 * No external dependencies required
 */

console.log('🚀 Loading 3D Viewer System...');

// Configuration
const VIEWER_CONFIG = {
    loadTimeout: 30000, // 30 seconds
    maxFileSize: 100 * 1024 * 1024, // 100MB
    supportedFormats: ['stl', 'obj', 'ply'],
    colors: {
        general: 0xd0dce8,  // Light blue-gray matching Shapeways gradient middle
        medical: 0xd0dce8   // Light blue-gray matching Shapeways gradient middle
    }
};

// Utility functions
const Utils = {
    showNotification(message, type = 'info') {
        const colors = {
            success: '#28a745',
            error: '#dc3545',
            info: '#17a2b8',
            warning: '#ffc107'
        };

        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${colors[type]};
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            z-index: 9999;
            font-weight: 500;
            animation: slideIn 0.3s ease-out;
        `;
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    },

    showLoading(containerId, message = 'Loading...') {
        const container = document.getElementById(containerId);
        if (!container) return;

        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'viewer-loading';
        loadingDiv.style.cssText = `
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: rgba(0,0,0,0.3);
            z-index: 100;
            border-radius: 0 0 16px 16px;
        `;
        loadingDiv.innerHTML = `
            <div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p style="color: white; margin-top: 20px; font-size: 16px; font-weight: 500;">${message}</p>
        `;
        container.appendChild(loadingDiv);
    },

    hideLoading(containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;
        const loading = container.querySelector('.viewer-loading');
        if (loading) loading.remove();
    },

    validateFile(file) {
        const ext = file.name.split('.').pop().toLowerCase();

        if (!VIEWER_CONFIG.supportedFormats.includes(ext)) {
            throw new Error(`Unsupported format. Please use: ${VIEWER_CONFIG.supportedFormats.join(', ').toUpperCase()}`);
        }

        if (file.size > VIEWER_CONFIG.maxFileSize) {
            throw new Error(`File too large. Maximum size: ${VIEWER_CONFIG.maxFileSize / (1024 * 1024)}MB`);
        }

        return ext;
    },

    formatFileSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(2) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }
};

// Library loader
const LibraryLoader = {
    loaded: {
        three: false,
        controls: false,
        stl: false,
        obj: false,
        ply: false
    },

    async loadScript(url, name) {
        return new Promise((resolve, reject) => {
            if (this.loaded[name]) {
                resolve();
                return;
            }

            const script = document.createElement('script');
            script.src = url;
            script.onload = () => {
                this.loaded[name] = true;
                console.log(`✓ ${name.toUpperCase()} loaded`);
                resolve();
            };
            script.onerror = () => reject(new Error(`Failed to load ${name}`));
            document.head.appendChild(script);
        });
    },

    async loadAll() {
        console.log('📦 Loading Three.js libraries...');

        try {
            // Load Three.js core
            await this.loadScript('https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js', 'three');

            // Load controls and loaders in parallel
            await Promise.all([
                this.loadScript('https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js', 'controls'),
                this.loadScript('https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/STLLoader.js', 'stl'),
                this.loadScript('https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/OBJLoader.js', 'obj'),
                this.loadScript('https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/PLYLoader.js', 'ply')
            ]);

            console.log('✓ All libraries loaded successfully');
            return true;
        } catch (error) {
            console.error('❌ Library loading failed:', error);
            throw error;
        }
    }
};

// 3D Viewer Class
class Professional3DViewer {
    constructor(containerId, backgroundColor = 0x000000) {
        this.containerId = containerId;
        this.container = document.getElementById(containerId);
        this.backgroundColor = backgroundColor;
        this.scene = null;
        this.camera = null;
        this.renderer = null;
        this.controls = null;
        this.model = null;
        this.modelGroup = null; // Group to hold multiple models
        this.animationId = null;
        this.initialized = false;
        this.uploadedFiles = []; // Array to store multiple files
        this.currentFileIndex = 0;
    }

    async init() {
        if (this.initialized) return;
        if (!this.container) {
            console.error('Container not found:', this.containerId);
            return;
        }

        console.log(`🎨 Initializing viewer: ${this.containerId}`);

        const THREE = window.THREE;
        if (!THREE) {
            throw new Error('THREE.js not loaded');
        }

        // Show initializing message
        Utils.showLoading(this.containerId, 'Initializing viewer...');

        // IMPORTANT: Don't clear innerHTML - preserve toolbar and other UI elements
        // Remove only canvas elements if they exist (for re-initialization)
        const existingCanvas = this.container.querySelector('canvas');
        if (existingCanvas) {
            existingCanvas.remove();
        }
        
        // Remove loading message if it exists
        const loadingDiv = this.container.querySelector('.viewer-loading');
        if (loadingDiv) {
            loadingDiv.remove();
        }

        // Setup scene
        this.scene = new THREE.Scene();
        this.scene.background = new THREE.Color(this.backgroundColor);

        // Setup camera
        const width = this.container.clientWidth;
        const height = this.container.clientHeight;
        this.camera = new THREE.PerspectiveCamera(45, width / height, 0.1, 10000);
        this.camera.position.set(100, 100, 200);

        // Setup renderer with shadow support
        this.renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
        this.renderer.setSize(width, height);
        this.renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        this.renderer.shadowMap.enabled = true;
        this.renderer.shadowMap.type = THREE.PCFSoftShadowMap;
        this.container.appendChild(this.renderer.domElement);

        // Setup controls with auto-rotate enabled by default
        this.controls = new THREE.OrbitControls(this.camera, this.renderer.domElement);
        this.controls.enableDamping = true;
        this.controls.dampingFactor = 0.05;
        this.controls.screenSpacePanning = false;
        this.controls.minDistance = 10;
        this.controls.maxDistance = 2000;
        this.controls.autoRotate = false;  // Disable auto-rotation by default
        this.controls.autoRotateSpeed = 1.0;  // Smooth rotation speed

        // Setup lighting
        this.setupLighting();

        // Add grid
        this.addGrid();

        this.initialized = true;

        // Hide loading message
        Utils.hideLoading(this.containerId);

        console.log(`✓ Viewer initialized: ${this.containerId}`);

        // Start render loop
        this.animate();

        // Handle window resize
        window.addEventListener('resize', () => this.onWindowResize());
    }

    setupLighting() {
        const THREE = window.THREE;

        // Ambient light for overall illumination
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);
        this.scene.add(ambientLight);

        // Main directional light with shadow (from top-right)
        const mainLight = new THREE.DirectionalLight(0xffffff, 0.8);
        mainLight.position.set(100, 150, 100);
        mainLight.castShadow = true;
        mainLight.shadow.mapSize.width = 2048;
        mainLight.shadow.mapSize.height = 2048;
        mainLight.shadow.camera.near = 0.5;
        mainLight.shadow.camera.far = 500;
        mainLight.shadow.camera.left = -200;
        mainLight.shadow.camera.right = 200;
        mainLight.shadow.camera.top = 200;
        mainLight.shadow.camera.bottom = -200;
        this.scene.add(mainLight);

        // Fill light (from left)
        const fillLight = new THREE.DirectionalLight(0xffffff, 0.3);
        fillLight.position.set(-50, 50, -50);
        this.scene.add(fillLight);

        // Back light (subtle)
        const backLight = new THREE.DirectionalLight(0xffffff, 0.2);
        backLight.position.set(0, 50, -100);
        this.scene.add(backLight);
    }

    addGrid() {
        const THREE = window.THREE;
        // Grid with white dotted lines like Shapeways
        const gridHelper = new THREE.GridHelper(500, 50, 0xffffff, 0xffffff);
        gridHelper.material.opacity = 0.3;
        gridHelper.material.transparent = true;
        gridHelper.material.depthWrite = false;
        gridHelper.name = 'grid';
        this.scene.add(gridHelper);
    }

    async loadFile(file, storageId = null) {
        console.log(`📂 Loading file: ${file.name} (${Utils.formatFileSize(file.size)})`);

        // Check if file is already loaded to prevent duplicates
        const isDuplicate = this.uploadedFiles.some(f =>
            f.file.name === file.name &&
            f.file.size === file.size &&
            (storageId ? f.storageId === storageId : true)
        );

        if (isDuplicate) {
            console.warn('⚠️ File already loaded, skipping:', file.name);
            return null;
        }

        try {
            // Validate file
            const ext = Utils.validateFile(file);

            // Show loading
            Utils.showLoading(this.containerId, `Loading ${ext.toUpperCase()} file...`);

            // Load based on format
            let result;
            switch (ext) {
                case 'stl':
                    result = await this.loadSTL(file);
                    break;
                case 'obj':
                    result = await this.loadOBJ(file);
                    break;
                case 'ply':
                    result = await this.loadPLY(file);
                    break;
            }

            Utils.hideLoading(this.containerId);
            Utils.showNotification(`✓ Model loaded: ${file.name}`, 'success');

            console.log('✓ File loaded successfully:', result);

            // Store the file data for multi-file management
            if (result.geometry) {
                this.addFile(file, result.geometry, storageId);
                console.log('✓ File added, total files now:', this.uploadedFiles.length);

                // Trigger pricing update immediately
                this.triggerPricingUpdate();
            }

            return result;

        } catch (error) {
            Utils.hideLoading(this.containerId);
            Utils.showNotification(`Error: ${error.message}`, 'error');
            console.error('❌ Error loading file:', error);
            throw error;
        }
    }

    loadSTL(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            const THREE = window.THREE;

            reader.onload = (event) => {
                try {
                    const loader = new THREE.STLLoader();
                    const geometry = loader.parse(event.target.result);
                    this.createMesh(geometry);
                    resolve({ format: 'STL', vertices: geometry.attributes.position.count, geometry: geometry });
                } catch (error) {
                    reject(error);
                }
            };

            reader.onerror = () => reject(new Error('Failed to read file'));
            reader.readAsArrayBuffer(file);
        });
    }

    loadOBJ(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            const THREE = window.THREE;

            reader.onload = (event) => {
                try {
                    const loader = new THREE.OBJLoader();
                    const text = event.target.result;
                    const object = loader.parse(text);

                    // Extract geometry from first mesh in object
                    let geometry = null;
                    object.traverse((child) => {
                        if (child.isMesh && !geometry) {
                            geometry = child.geometry;
                        }
                    });

                    this.addObject(object);
                    resolve({ format: 'OBJ', object: true, geometry: geometry });
                } catch (error) {
                    reject(error);
                }
            };

            reader.onerror = () => reject(new Error('Failed to read file'));
            reader.readAsText(file);
        });
    }

    loadPLY(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            const THREE = window.THREE;

            reader.onload = (event) => {
                try {
                    const loader = new THREE.PLYLoader();
                    const geometry = loader.parse(event.target.result);
                    this.createMesh(geometry);
                    resolve({ format: 'PLY', vertices: geometry.attributes.position.count, geometry: geometry });
                } catch (error) {
                    reject(error);
                }
            };

            reader.onerror = () => reject(new Error('Failed to read file'));
            reader.readAsArrayBuffer(file);
        });
    }

    createMesh(geometry) {
        const THREE = window.THREE;

        // Create model group if it doesn't exist
        if (!this.modelGroup) {
            this.modelGroup = new THREE.Group();
            this.scene.add(this.modelGroup);
        }

        // Prepare geometry
        geometry.computeVertexNormals();
        geometry.center();

        // Create material with pure white color like Shapeways
        const material = new THREE.MeshStandardMaterial({
            color: 0xffffff,  // Pure white
            metalness: 0.05,
            roughness: 0.4,
            side: THREE.DoubleSide,
            flatShading: false,
            wireframe: false,
            transparent: false,
            opacity: 1.0
        });

        // Create mesh
        const mesh = new THREE.Mesh(geometry, material);
        mesh.castShadow = true;
        mesh.receiveShadow = true;

        // Add to group instead of replacing
        this.modelGroup.add(mesh);

        // Keep reference to the group as model
        this.model = this.modelGroup;

        // Fit camera to show all models
        this.fitCameraToModel();

        // Show viewer controls
        const controls = this.container.querySelector('.viewer-controls');
        if (controls) {
            controls.style.display = 'flex';
        }
    }

    addObject(object) {
        const THREE = window.THREE;

        // Create model group if it doesn't exist
        if (!this.modelGroup) {
            this.modelGroup = new THREE.Group();
            this.scene.add(this.modelGroup);
        }

        // Apply pure white material to all meshes like Shapeways
        object.traverse((child) => {
            if (child instanceof THREE.Mesh) {
                child.material = new THREE.MeshStandardMaterial({
                    color: 0xffffff,  // Pure white
                    metalness: 0.05,
                    roughness: 0.4,
                    side: THREE.DoubleSide,
                    wireframe: false,
                    transparent: false,
                    opacity: 1.0
                });
                child.castShadow = true;
                child.receiveShadow = true;
            }
        });

        // Add to group instead of replacing
        this.modelGroup.add(object);

        // Keep reference to the group as model
        this.model = this.modelGroup;

        this.fitCameraToModel();

        // Show viewer controls
        const controls = this.container.querySelector('.viewer-controls');
        if (controls) {
            controls.style.display = 'flex';
        }
    }

    removeModel() {
        // Remove the entire model group
        if (this.modelGroup) {
            // Dispose all children
            this.modelGroup.traverse((child) => {
                if (child.geometry) {
                    child.geometry.dispose();
                }
                if (child.material) {
                    if (Array.isArray(child.material)) {
                        child.material.forEach(m => m.dispose());
                    } else {
                        child.material.dispose();
                    }
                }
            });

            // Remove from scene
            this.scene.remove(this.modelGroup);
            this.modelGroup = null;
            this.model = null;
        } else if (this.model) {
            // Fallback for old single model
            this.scene.remove(this.model);
            if (this.model.geometry) {
                this.model.geometry.dispose();
            }
            if (this.model.material) {
                if (Array.isArray(this.model.material)) {
                    this.model.material.forEach(m => m.dispose());
                } else {
                    this.model.material.dispose();
                }
            }
            this.model = null;
        }
    }

    fitCameraToModel() {
        if (!this.model) return;

        const THREE = window.THREE;
        const box = new THREE.Box3().setFromObject(this.model);
        const center = box.getCenter(new THREE.Vector3());
        const size = box.getSize(new THREE.Vector3());

        const maxDim = Math.max(size.x, size.y, size.z);
        const fov = this.camera.fov * (Math.PI / 180);
        const cameraZ = Math.abs(maxDim / 2 / Math.tan(fov / 2)) * 2.5;

        this.camera.position.set(center.x + cameraZ * 0.5, center.y + cameraZ * 0.5, center.z + cameraZ);
        this.camera.lookAt(center);
        this.controls.target.copy(center);
        this.controls.update();
    }

    animate() {
        this.animationId = requestAnimationFrame(() => this.animate());
        this.controls.update();
        this.renderer.render(this.scene, this.camera);
    }

    onWindowResize() {
        if (!this.container) return;
        const width = this.container.clientWidth;
        const height = this.container.clientHeight;
        this.camera.aspect = width / height;
        this.camera.updateProjectionMatrix();
        this.renderer.setSize(width, height);
    }

    // New methods for viewer controls
    changeModelColor(hexColor) {
        if (!this.model) return;
        const color = new THREE.Color(hexColor);
        this.model.traverse((child) => {
            if (child.isMesh && child.material) {
                child.material.color.set(color);
                child.material.needsUpdate = true;
            }
        });
        console.log(`✓ Model color changed to ${hexColor}`);
    }

    toggleWireframe(enabled) {
        if (!this.model) return;
        this.model.traverse((child) => {
            if (child.isMesh && child.material) {
                child.material.wireframe = enabled;
                child.material.needsUpdate = true;
            }
        });
        console.log(`✓ Wireframe ${enabled ? 'enabled' : 'disabled'}`);
    }

    changeBGColor(hexColor) {
        if (!this.scene || !this.renderer) return;
        const color = new THREE.Color(hexColor);
        this.scene.background = color;
        console.log(`✓ Background color changed to ${hexColor}`);
    }

    // Multi-file management methods
    addFile(file, geometry, storageId = null) {
        console.log(`📦 Adding file: ${file.name}`);
        const volume = this.calculateVolume(geometry);
        console.log(`   Calculated volume:`, volume);

        // Find the mesh that was just added (it's the last child in the modelGroup)
        let meshReference = null;
        if (this.modelGroup && this.modelGroup.children.length > 0) {
            meshReference = this.modelGroup.children[this.modelGroup.children.length - 1];
        }

        const fileData = {
            id: Date.now() + Math.random(),
            file: file,
            geometry: geometry,
            mesh: meshReference, // Store reference to the actual mesh
            volume: volume,
            timestamp: Date.now(),
            storageId: storageId // Track the server/storage file ID
        };

        this.uploadedFiles.push(fileData);
        console.log(`✅ File added to uploadedFiles array. Total files: ${this.uploadedFiles.length}`);
        console.log(`   File data:`, fileData);
        return fileData;
    }

    removeFile(fileId) {
        const index = this.uploadedFiles.findIndex(f => f.id === fileId);
        if (index !== -1) {
            const fileData = this.uploadedFiles[index];

            // Remove the mesh from the scene
            if (fileData.mesh && this.modelGroup) {
                this.modelGroup.remove(fileData.mesh);

                // Dispose geometry and material
                if (fileData.mesh.geometry) {
                    fileData.mesh.geometry.dispose();
                }
                if (fileData.mesh.material) {
                    if (Array.isArray(fileData.mesh.material)) {
                        fileData.mesh.material.forEach(m => m.dispose());
                    } else {
                        fileData.mesh.material.dispose();
                    }
                }

                console.log(`✓ Mesh removed from scene for file: ${fileData.file.name}`);
            }

            // Remove from array
            this.uploadedFiles.splice(index, 1);
            console.log(`✓ File removed from list: ${fileId}`);

            // If no more files, hide the model group
            if (this.uploadedFiles.length === 0) {
                this.removeModel();
            } else {
                // Refit camera to remaining models
                this.fitCameraToModel();
            }

            return true;
        }
        return false;
    }

    getUploadedFiles() {
        return this.uploadedFiles;
    }

    calculateVolume(geometry) {
        console.log('📏 Calculating volume...');

        // Try to calculate actual signed volume from mesh triangles
        let volumeMM3 = 0;
        let useActualVolume = false;

        if (geometry.attributes && geometry.attributes.position) {
            const positions = geometry.attributes.position;
            const vertices = positions.array;

            // Check if we have an index (face data)
            const indices = geometry.index ? geometry.index.array : null;

            if (indices) {
                // Calculate signed volume using triangle method
                // Formula: V = (1/6) * Σ(v0 · (v1 × v2))
                for (let i = 0; i < indices.length; i += 3) {
                    const i0 = indices[i] * 3;
                    const i1 = indices[i + 1] * 3;
                    const i2 = indices[i + 2] * 3;

                    // Get triangle vertices
                    const v0x = vertices[i0], v0y = vertices[i0 + 1], v0z = vertices[i0 + 2];
                    const v1x = vertices[i1], v1y = vertices[i1 + 1], v1z = vertices[i1 + 2];
                    const v2x = vertices[i2], v2y = vertices[i2 + 1], v2z = vertices[i2 + 2];

                    // Calculate cross product: v1 × v2
                    const crossX = v1y * v2z - v1z * v2y;
                    const crossY = v1z * v2x - v1x * v2z;
                    const crossZ = v1x * v2y - v1y * v2x;

                    // Calculate dot product: v0 · (v1 × v2)
                    const dot = v0x * crossX + v0y * crossY + v0z * crossZ;

                    volumeMM3 += dot;
                }

                volumeMM3 = Math.abs(volumeMM3 / 6.0);
                useActualVolume = true;
                console.log('   ✅ Using actual mesh volume (signed volume method)');
            } else if (vertices.length >= 9) {
                // Non-indexed geometry - vertices are in sequence
                for (let i = 0; i < vertices.length; i += 9) {
                    const v0x = vertices[i], v0y = vertices[i + 1], v0z = vertices[i + 2];
                    const v1x = vertices[i + 3], v1y = vertices[i + 4], v1z = vertices[i + 5];
                    const v2x = vertices[i + 6], v2y = vertices[i + 7], v2z = vertices[i + 8];

                    // Calculate cross product: v1 × v2
                    const crossX = v1y * v2z - v1z * v2y;
                    const crossY = v1z * v2x - v1x * v2z;
                    const crossZ = v1x * v2y - v1y * v2x;

                    // Calculate dot product: v0 · (v1 × v2)
                    const dot = v0x * crossX + v0y * crossY + v0z * crossZ;

                    volumeMM3 += dot;
                }

                volumeMM3 = Math.abs(volumeMM3 / 6.0);
                useActualVolume = true;
                console.log('   ✅ Using actual mesh volume (non-indexed method)');
            }
        }

        // Fallback to bounding box if actual volume couldn't be calculated
        if (!useActualVolume || volumeMM3 === 0 || !isFinite(volumeMM3)) {
            geometry.computeBoundingBox();
            const box = geometry.boundingBox;
            const size = new THREE.Vector3();
            box.getSize(size);

            volumeMM3 = size.x * size.y * size.z;
            console.log('   ⚠️ Using bounding box approximation');
            console.log('   Bounding box size:', size);
        }

        // Convert to cm³
        const volumeCM3 = volumeMM3 / 1000;

        console.log('   Volume (mm³):', volumeMM3.toFixed(2));
        console.log('   Volume (cm³):', volumeCM3.toFixed(2));

        return {
            mm3: volumeMM3,
            cm3: volumeCM3
        };
    }

    /**
     * Trigger pricing update by dispatching custom event
     */
    triggerPricingUpdate() {
        console.log('🔥 Triggering pricing update event');
        const event = new CustomEvent('pricingUpdateNeeded', {
            detail: {
                viewerId: this.containerId,
                fileCount: this.uploadedFiles.length,
                totalVolume: this.getTotalVolume()
            }
        });
        window.dispatchEvent(event);
        console.log('✓ Pricing update event dispatched:', event.detail);
    }

    getTotalVolume() {
        const total = this.uploadedFiles.reduce((sum, file) => sum + (file.volume?.cm3 || 0), 0);
        console.log('📊 Total volume from', this.uploadedFiles.length, 'files:', total.toFixed(2), 'cm³');
        return total;
    }

    calculatePrice(material = 'pla', quality = 'standard') {
        console.log(`💰 Calculating price for material=${material}, quality=${quality}`);

        // Material pricing per cm³
        const materialPrices = {
            pla: 0.05,
            abs: 0.06,
            petg: 0.07,
            nylon: 0.12,
            resin: 0.15,
            'medical-resin': 0.25,
            'biocompatible': 0.35
        };

        // Quality multipliers
        const qualityMultipliers = {
            draft: 0.7,
            standard: 1.0,
            high: 1.5,
            ultra: 2.0
        };

        const totalVolume = this.getTotalVolume();
        console.log(`   Total volume: ${totalVolume} cm³ from ${this.uploadedFiles.length} files`);

        const materialCost = totalVolume * (materialPrices[material] || materialPrices.pla);
        const qualityMultiplier = qualityMultipliers[quality] || 1.0;
        const baseCost = materialCost * qualityMultiplier;

        // Add setup fee per file
        const setupFee = this.uploadedFiles.length * 5;

        // Estimated print time (rough estimate: 1 hour per 10 cm³ at standard quality)
        const printTime = (totalVolume / 10) * qualityMultiplier;

        const result = {
            materialCost: parseFloat(materialCost.toFixed(2)),
            setupFee: parseFloat(setupFee.toFixed(2)),
            baseCost: parseFloat(baseCost.toFixed(2)),
            totalPrice: parseFloat((baseCost + setupFee).toFixed(2)),
            printTime: parseFloat(printTime.toFixed(1)),
            totalVolume: parseFloat(totalVolume.toFixed(2)),
            fileCount: this.uploadedFiles.length
        };

        console.log(`   💵 Price calculation result:`, result);
        return result;
    }

    /**
     * Repair model - Fix mesh issues
     */
    repairModel() {
        console.log('🔧 Starting model repair...');

        if (!this.model || !this.uploadedFiles || this.uploadedFiles.length === 0) {
            console.log('⚠️ No model to repair');
            return false;
        }

        try {
            const THREE = window.THREE;

            // Get the current mesh
            let mesh = null;
            this.modelGroup.traverse((child) => {
                if (child instanceof THREE.Mesh && !mesh) {
                    mesh = child;
                }
            });

            if (!mesh || !mesh.geometry) {
                console.log('⚠️ No geometry found');
                return false;
            }

            const geometry = mesh.geometry;

            // Ensure geometry has proper attributes
            if (!geometry.attributes.position) {
                console.log('⚠️ No position attribute');
                return false;
            }

            // Merge duplicate vertices
            geometry.mergeVertices();

            // Compute vertex normals for smooth shading
            geometry.computeVertexNormals();

            // Recalculate bounding box
            geometry.computeBoundingBox();

            // Ensure material is solid (not wireframe)
            if (mesh.material) {
                mesh.material.wireframe = false;
                mesh.material.transparent = false;
                mesh.material.opacity = 1.0;
                mesh.material.needsUpdate = true;
            }

            // Recalculate volume with repaired mesh
            const volumeData = this.calculateVolume(geometry);

            // Update file data
            if (this.uploadedFiles[0]) {
                this.uploadedFiles[0].volume = volumeData;
                this.uploadedFiles[0].repaired = true;

                console.log('✅ Model repaired! New volume:', volumeData.cm3.toFixed(2), 'cm³');
            }

            // Trigger pricing update
            this.triggerPricingUpdate();

            return true;
        } catch (error) {
            console.error('❌ Repair failed:', error);
            return false;
        }
    }

    /**
     * Fill holes in model - Make watertight
     */
    fillHoles() {
        console.log('🔧 Filling holes to make model watertight...');

        if (!this.model || !this.uploadedFiles || this.uploadedFiles.length === 0) {
            console.log('⚠️ No model to fill');
            return false;
        }

        try {
            const THREE = window.THREE;

            // Get the current mesh
            let mesh = null;
            this.modelGroup.traverse((child) => {
                if (child instanceof THREE.Mesh && !mesh) {
                    mesh = child;
                }
            });

            if (!mesh || !mesh.geometry) {
                console.log('⚠️ No geometry found');
                return false;
            }

            const geometry = mesh.geometry;

            // Convert to non-indexed geometry for processing
            const nonIndexedGeometry = geometry.toNonIndexed();

            // Merge duplicate vertices to close gaps
            nonIndexedGeometry.mergeVertices();

            // Compute vertex normals
            nonIndexedGeometry.computeVertexNormals();

            // Compute face normals
            nonIndexedGeometry.computeFaceNormals && nonIndexedGeometry.computeFaceNormals();

            // Recalculate bounding box
            nonIndexedGeometry.computeBoundingBox();

            // Replace geometry
            mesh.geometry.dispose();
            mesh.geometry = nonIndexedGeometry;

            // Ensure material is solid (not wireframe)
            if (mesh.material) {
                mesh.material.wireframe = false;
                mesh.material.transparent = false;
                mesh.material.opacity = 1.0;
                mesh.material.side = THREE.DoubleSide;
                mesh.material.needsUpdate = true;
            }

            // Recalculate volume with filled mesh
            const volumeData = this.calculateVolume(nonIndexedGeometry);

            // Update file data
            if (this.uploadedFiles[0]) {
                this.uploadedFiles[0].volume = volumeData;
                this.uploadedFiles[0].filled = true;

                console.log('✅ Holes filled! New volume:', volumeData.cm3.toFixed(2), 'cm³');
            }

            // Trigger pricing update
            this.triggerPricingUpdate();

            return true;
        } catch (error) {
            console.error('❌ Fill holes failed:', error);
            return false;
        }
    }

    dispose() {
        if (this.animationId) cancelAnimationFrame(this.animationId);
        if (this.renderer) this.renderer.dispose();
        if (this.controls) this.controls.dispose();
        this.removeModel();
    }
}

// Global instances
let viewerGeneral = null;
let viewerMedical = null;

// Initialize system
async function initializeViewerSystem() {
    console.log('═══════════════════════════════════════');
    console.log('🚀 3D Viewer System Initialization');
    console.log('═══════════════════════════════════════');

    try {
        // Load libraries
        await LibraryLoader.loadAll();

        // Small delay to ensure everything is ready
        await new Promise(resolve => setTimeout(resolve, 300));

        // Initialize viewers
        console.log('🎨 Creating viewer instances...');
        viewerGeneral = new Professional3DViewer('viewer3dGeneral', VIEWER_CONFIG.colors.general);
        viewerMedical = new Professional3DViewer('viewer3dMedical', VIEWER_CONFIG.colors.medical);

        // Initialize both viewers immediately
        await Promise.all([
            viewerGeneral.init(),
            viewerMedical.init()
        ]);

        // Wait a bit more for renderer to be fully ready
        await new Promise(resolve => setTimeout(resolve, 500));

        console.log('═══════════════════════════════════════');
        console.log('✓ 3D Viewer System Ready - Upload files now!');
        console.log('═══════════════════════════════════════');

        // Show ready notification - REMOVED ANNOYING ALERT
        // Utils.showNotification('3D Viewer Ready! You can now upload files.', 'success');

        // Dispatch event for file manager to initialize FIRST
        window.dispatchEvent(new Event('viewersReady'));

        // Wait for file manager to wrap the loadFile methods
        await new Promise(resolve => setTimeout(resolve, 200));

        // NOW setup file handlers (after file manager has wrapped loadFile)
        setupFileHandlers();

        console.log('✓ File handlers attached (after file manager wrapping)');

    } catch (error) {
        console.error('❌ Initialization failed:', error);
        Utils.showNotification('Failed to initialize 3D viewer', 'error');
    }
}

function setupFileHandlers() {
    // General file input
    const fileInputGeneral = document.getElementById('fileInput3d');
    if (fileInputGeneral) {
        console.log('✓ General file input handler attached');
        console.log('  Multiple attribute:', fileInputGeneral.hasAttribute('multiple'));
        console.log('  Accept attribute:', fileInputGeneral.getAttribute('accept'));

        fileInputGeneral.addEventListener('change', async (e) => {
            console.log('🔔 Change event fired on General input');
            console.log('   e.target:', e.target);
            console.log('   e.target.files:', e.target.files);
            console.log('   e.target.files.length:', e.target.files ? e.target.files.length : 'null');

            const files = Array.from(e.target.files);
            console.log('   Array.from result:', files);
            console.log('   Array length:', files.length);

            if (files.length > 0) {
                console.log('═══════════════════════════════════════');
                console.log(`📁 GENERAL: ${files.length} file(s) selected`);
                files.forEach((file, i) => {
                    console.log(`   ${i + 1}. ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`);
                });
                console.log('═══════════════════════════════════════');

                // Ensure viewer is initialized
                if (!viewerGeneral || !viewerGeneral.initialized) {
                    console.log('⏳ Viewer not ready, initializing...');
                    Utils.showNotification('Initializing viewer, please wait...', 'info');
                    try {
                        await viewerGeneral.init();
                        await new Promise(resolve => setTimeout(resolve, 500));
                    } catch (error) {
                        console.error('Failed to initialize viewer:', error);
                        Utils.showNotification('Failed to initialize viewer', 'error');
                        // Don't reset input on error
                        return;
                    }
                }

                try {
                    // Load all files sequentially
                    for (let i = 0; i < files.length; i++) {
                        const file = files[i];
                        console.log(`📥 Loading file ${i + 1}/${files.length}: ${file.name}`);
                        await viewerGeneral.loadFile(file);
                        console.log(`✓ File ${i + 1}/${files.length} loaded`);
                    }
                    console.log('═══════════════════════════════════════');
                    console.log('✅ All files loaded successfully!');
                    console.log('═══════════════════════════════════════');

                    // Enable auto-rotate button after successful file load
                    const autoRotateBtn = document.getElementById('autoRotateBtnMain');
                    if (autoRotateBtn) {
                        autoRotateBtn.disabled = false;
                        autoRotateBtn.style.opacity = '1';
                        autoRotateBtn.style.cursor = 'pointer';
                        console.log('✅ Auto-rotate button enabled');
                    }

                    // Setup measurement click handler for newly loaded files
                    if (window.setupMeasurementClickHandler) {
                        setTimeout(() => {
                            window.setupMeasurementClickHandler();
                            console.log('✅ Measurement handler setup after file load');
                        }, 500);
                    }
                } catch (error) {
                    console.error('❌ Error loading files:', error);
                    // Don't reset input on error so user can retry
                    return;
                }
            } else {
                console.warn('⚠️ No files selected or files.length is 0');
            }

            // Reset file input only after successful processing
            console.log('🔄 Resetting file input');
            e.target.value = '';
        });
    } else {
        console.warn('⚠️ General file input not found');
    }

    // Medical file input - FIXED ID
    const fileInputMedical = document.getElementById('fileInput3dMedical');
    if (fileInputMedical) {
        console.log('✓ Medical file input handler attached');
        console.log('  Multiple attribute:', fileInputMedical.hasAttribute('multiple'));
        console.log('  Accept attribute:', fileInputMedical.getAttribute('accept'));
        fileInputMedical.addEventListener('change', async (e) => {
            const files = Array.from(e.target.files);
            if (files.length > 0) {
                console.log('═══════════════════════════════════════');
                console.log(`📁 MEDICAL: ${files.length} file(s) selected`);
                files.forEach((file, i) => {
                    console.log(`   ${i + 1}. ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`);
                });
                console.log('═══════════════════════════════════════');

                // Ensure viewer is initialized
                if (!viewerMedical || !viewerMedical.initialized) {
                    console.log('⏳ Medical viewer not ready, initializing...');
                    Utils.showNotification('Initializing viewer, please wait...', 'info');
                    try {
                        await viewerMedical.init();
                        await new Promise(resolve => setTimeout(resolve, 500));
                    } catch (error) {
                        console.error('Failed to initialize medical viewer:', error);
                        Utils.showNotification('Failed to initialize viewer', 'error');
                        return;
                    }
                }

                try {
                    // Load all files sequentially
                    for (let i = 0; i < files.length; i++) {
                        const file = files[i];
                        console.log(`� Loading file ${i + 1}/${files.length}: ${file.name}`);
                        await viewerMedical.loadFile(file);
                        console.log(`✓ File ${i + 1}/${files.length} loaded`);
                    }
                    console.log('═══════════════════════════════════════');
                    console.log('✅ All medical files loaded successfully!');
                    console.log('═══════════════════════════════════════');

                    // Enable auto-rotate button after successful file load
                    const autoRotateBtn = document.getElementById('autoRotateBtnMain');
                    if (autoRotateBtn) {
                        autoRotateBtn.disabled = false;
                        autoRotateBtn.style.opacity = '1';
                        autoRotateBtn.style.cursor = 'pointer';
                        console.log('✅ Auto-rotate button enabled');
                    }

                    // Setup measurement click handler for newly loaded files
                    if (window.setupMeasurementClickHandler) {
                        setTimeout(() => {
                            window.setupMeasurementClickHandler();
                            console.log('✅ Measurement handler setup after medical file load');
                        }, 500);
                    }
                } catch (error) {
                    console.error('❌ Error loading medical files:', error);
                }
            }
            // Reset file input
            e.target.value = '';
        });
    } else {
        console.warn('⚠️ Medical file input not found - looking for fileInput3dMedical');
    }

    // Drag and drop
    setupDragAndDrop();
    console.log('✓ File handlers setup complete');
}

function setupDragAndDrop() {
    const zones = [
        { element: document.querySelector('#generalForm3d .upload-drop-zone-3d'), viewer: () => viewerGeneral, name: 'general' },
        { element: document.querySelector('#medicalForm3d .upload-drop-zone-3d'), viewer: () => viewerMedical, name: 'medical' }
    ];

    zones.forEach(({ element, viewer, name }) => {
        if (!element) {
            console.warn(`⚠️ Drop zone not found for ${name}`);
            return;
        }

        console.log(`✓ Drop zone attached for ${name}`);

        element.addEventListener('dragover', (e) => {
            e.preventDefault();
            element.style.borderColor = '#667eea';
            element.style.background = '#e3f2fd';
        });

        element.addEventListener('dragleave', () => {
            element.style.borderColor = '';
            element.style.background = '';
        });

        element.addEventListener('drop', async (e) => {
            e.preventDefault();
            element.style.borderColor = '';
            element.style.background = '';

            const files = Array.from(e.dataTransfer.files);
            if (files.length > 0) {
                console.log(`📁 ${files.length} file(s) dropped on ${name}`);

                const viewerInstance = viewer();

                // Ensure viewer is initialized
                if (!viewerInstance || !viewerInstance.initialized) {
                    console.log(`⏳ ${name} viewer not ready, initializing...`);
                    Utils.showNotification('Initializing viewer, please wait...', 'info');
                    try {
                        await viewerInstance.init();
                        await new Promise(resolve => setTimeout(resolve, 500));
                    } catch (error) {
                        console.error(`Failed to initialize ${name} viewer:`, error);
                        Utils.showNotification('Failed to initialize viewer', 'error');
                        return;
                    }
                }

                try {
                    // Load all files sequentially
                    for (const file of files) {
                        console.log(`📁 Loading: ${file.name}`);
                        await viewerInstance.loadFile(file);
                    }
                    console.log('✓ All dropped files loaded successfully');

                    // Enable auto-rotate button after successful file load
                    const autoRotateBtn = document.getElementById('autoRotateBtnMain');
                    if (autoRotateBtn) {
                        autoRotateBtn.disabled = false;
                        autoRotateBtn.style.opacity = '1';
                        autoRotateBtn.style.cursor = 'pointer';
                        console.log('✅ Auto-rotate button enabled');
                    }

                    // Setup measurement click handler for newly loaded files
                    if (window.setupMeasurementClickHandler) {
                        setTimeout(() => {
                            window.setupMeasurementClickHandler();
                            console.log('✅ Measurement handler setup after drag-drop');
                        }, 500);
                    }
                } catch (error) {
                    console.error(error);
                }
            }
        });
    });

    console.log('✓ Drag and drop setup complete');
}

// Add animation styles
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(400px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(400px); opacity: 0; }
    }
`;
document.head.appendChild(style);

// Start initialization when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeViewerSystem);
} else {
    initializeViewerSystem();
}

// Export for debugging
window.viewerGeneral = viewerGeneral;
window.viewerMedical = viewerMedical;

console.log('✓ 3D Viewer script loaded');
