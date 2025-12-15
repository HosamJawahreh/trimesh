/**
 * 3D Model Viewer with Three.js
 * Supports STL, OBJ, and PLY files
 */

class ModelViewer3D {
    constructor(containerElement, options = {}) {
        this.container = containerElement;
        this.options = {
            backgroundColor: options.backgroundColor || 0x1a1a1a,
            gridColor: options.gridColor || 0x444444,
            modelColor: options.modelColor || 0x3498db,
            wireframeColor: options.wireframeColor || 0xffffff,
            enableGrid: options.enableGrid !== false,
            enableAxes: options.enableAxes !== false,
            autoRotate: options.autoRotate || false,
            ...options
        };

        this.scene = null;
        this.camera = null;
        this.renderer = null;
        this.controls = null;
        this.currentModel = null;
        this.wireframeModel = null;
        this.showWireframe = false;
        this.lights = [];

        this.init();
    }

    init() {
        // Create scene
        this.scene = new THREE.Scene();
        this.scene.background = new THREE.Color(this.options.backgroundColor);

        // Create camera
        const width = this.container.clientWidth;
        const height = this.container.clientHeight;
        this.camera = new THREE.PerspectiveCamera(45, width / height, 0.1, 10000);
        this.camera.position.set(100, 100, 100);

        // Create renderer
        this.renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
        this.renderer.setSize(width, height);
        this.renderer.setPixelRatio(window.devicePixelRatio);
        this.renderer.shadowMap.enabled = true;
        this.renderer.shadowMap.type = THREE.PCFSoftShadowMap;
        this.container.appendChild(this.renderer.domElement);

        // Add lights
        this.setupLights();

        // Add grid
        if (this.options.enableGrid) {
            const gridHelper = new THREE.GridHelper(200, 20, this.options.gridColor, this.options.gridColor);
            this.scene.add(gridHelper);
        }

        // Add axes
        if (this.options.enableAxes) {
            const axesHelper = new THREE.AxesHelper(100);
            this.scene.add(axesHelper);
        }

        // Add controls
        this.controls = new THREE.OrbitControls(this.camera, this.renderer.domElement);
        this.controls.enableDamping = true;
        this.controls.dampingFactor = 0.05;
        this.controls.screenSpacePanning = false;
        this.controls.minDistance = 1;
        this.controls.maxDistance = 1000;
        this.controls.autoRotate = this.options.autoRotate;
        this.controls.autoRotateSpeed = 2.0;

        // Handle window resize
        window.addEventListener('resize', () => this.onWindowResize(), false);

        // Start animation loop
        this.animate();
    }

    setupLights() {
        // Ambient light
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
        this.scene.add(ambientLight);
        this.lights.push(ambientLight);

        // Directional light 1
        const dirLight1 = new THREE.DirectionalLight(0xffffff, 0.8);
        dirLight1.position.set(100, 100, 100);
        dirLight1.castShadow = true;
        this.scene.add(dirLight1);
        this.lights.push(dirLight1);

        // Directional light 2
        const dirLight2 = new THREE.DirectionalLight(0xffffff, 0.4);
        dirLight2.position.set(-100, 100, -100);
        this.scene.add(dirLight2);
        this.lights.push(dirLight2);

        // Hemisphere light
        const hemiLight = new THREE.HemisphereLight(0xffffff, 0x444444, 0.4);
        this.scene.add(hemiLight);
        this.lights.push(hemiLight);
    }

    onWindowResize() {
        const width = this.container.clientWidth;
        const height = this.container.clientHeight;

        this.camera.aspect = width / height;
        this.camera.updateProjectionMatrix();

        this.renderer.setSize(width, height);
    }

    animate() {
        requestAnimationFrame(() => this.animate());

        this.controls.update();
        this.renderer.render(this.scene, this.camera);
    }

    /**
     * Load a 3D model file
     */
    async loadModel(file) {
        return new Promise((resolve, reject) => {
            const fileExtension = file.name.split('.').pop().toLowerCase();
            const reader = new FileReader();

            reader.onload = (event) => {
                const arrayBuffer = event.target.result;

                try {
                    let geometry;

                    switch (fileExtension) {
                        case 'stl':
                            geometry = this.loadSTL(arrayBuffer);
                            break;
                        case 'obj':
                            geometry = this.loadOBJ(new TextDecoder().decode(arrayBuffer));
                            break;
                        case 'ply':
                            geometry = this.loadPLY(arrayBuffer);
                            break;
                        default:
                            reject(new Error('Unsupported file format'));
                            return;
                    }

                    if (geometry) {
                        this.displayModel(geometry);
                        const analysis = this.analyzeGeometry(geometry);
                        resolve(analysis);
                    } else {
                        reject(new Error('Failed to load geometry'));
                    }

                } catch (error) {
                    reject(error);
                }
            };

            reader.onerror = () => reject(new Error('File reading failed'));
            reader.readAsArrayBuffer(file);
        });
    }

    loadSTL(arrayBuffer) {
        const loader = new THREE.STLLoader();
        return loader.parse(arrayBuffer);
    }

    loadOBJ(text) {
        const loader = new THREE.OBJLoader();
        const object = loader.parse(text);
        
        // Extract geometry from first mesh
        let geometry = null;
        object.traverse((child) => {
            if (child instanceof THREE.Mesh && !geometry) {
                geometry = child.geometry;
            }
        });

        return geometry;
    }

    loadPLY(arrayBuffer) {
        const loader = new THREE.PLYLoader();
        return loader.parse(arrayBuffer);
    }

    displayModel(geometry) {
        // Remove old model
        if (this.currentModel) {
            this.scene.remove(this.currentModel);
            this.currentModel.geometry.dispose();
            this.currentModel.material.dispose();
        }

        if (this.wireframeModel) {
            this.scene.remove(this.wireframeModel);
            this.wireframeModel.geometry.dispose();
            this.wireframeModel.material.dispose();
        }

        // Compute normals and bounding box
        geometry.computeVertexNormals();
        geometry.computeBoundingBox();
        geometry.computeBoundingSphere();

        // Center geometry
        geometry.center();

        // Create material
        const material = new THREE.MeshPhongMaterial({
            color: this.options.modelColor,
            specular: 0x111111,
            shininess: 200,
            flatShading: false,
            side: THREE.DoubleSide
        });

        // Create mesh
        this.currentModel = new THREE.Mesh(geometry, material);
        this.currentModel.castShadow = true;
        this.currentModel.receiveShadow = true;
        this.scene.add(this.currentModel);

        // Create wireframe
        const wireframeGeometry = new THREE.WireframeGeometry(geometry);
        const wireframeMaterial = new THREE.LineBasicMaterial({
            color: this.options.wireframeColor,
            linewidth: 1
        });
        this.wireframeModel = new THREE.LineSegments(wireframeGeometry, wireframeMaterial);
        this.wireframeModel.visible = this.showWireframe;
        this.scene.add(this.wireframeModel);

        // Fit camera to model
        this.fitCameraToModel();
    }

    analyzeGeometry(geometry) {
        // Compute bounding box if not already computed
        if (!geometry.boundingBox) {
            geometry.computeBoundingBox();
        }

        const boundingBox = geometry.boundingBox;
        const size = new THREE.Vector3();
        boundingBox.getSize(size);

        // Calculate volume using signed volume of triangles
        const position = geometry.attributes.position;
        let volume = 0;

        for (let i = 0; i < position.count; i += 3) {
            const p1 = new THREE.Vector3().fromBufferAttribute(position, i);
            const p2 = new THREE.Vector3().fromBufferAttribute(position, i + 1);
            const p3 = new THREE.Vector3().fromBufferAttribute(position, i + 2);

            // Signed volume of tetrahedron
            volume += p1.dot(p2.cross(p3));
        }

        volume = Math.abs(volume) / 6.0;

        // Calculate surface area
        let surfaceArea = 0;
        for (let i = 0; i < position.count; i += 3) {
            const p1 = new THREE.Vector3().fromBufferAttribute(position, i);
            const p2 = new THREE.Vector3().fromBufferAttribute(position, i + 1);
            const p3 = new THREE.Vector3().fromBufferAttribute(position, i + 2);

            const v1 = p2.clone().sub(p1);
            const v2 = p3.clone().sub(p1);
            const cross = v1.cross(v2);
            surfaceArea += cross.length() / 2;
        }

        return {
            volume_mm3: volume,
            volume_cm3: volume / 1000,
            width_mm: size.x,
            height_mm: size.y,
            depth_mm: size.z,
            surface_area_mm2: surfaceArea,
            dimensions: {
                x: size.x,
                y: size.y,
                z: size.z
            }
        };
    }

    fitCameraToModel() {
        if (!this.currentModel) return;

        const box = new THREE.Box3().setFromObject(this.currentModel);
        const size = box.getSize(new THREE.Vector3());
        const center = box.getCenter(new THREE.Vector3());

        const maxDim = Math.max(size.x, size.y, size.z);
        const fov = this.camera.fov * (Math.PI / 180);
        let cameraZ = Math.abs(maxDim / 2 / Math.tan(fov / 2));
        cameraZ *= 1.5; // Add some padding

        this.camera.position.set(center.x + cameraZ, center.y + cameraZ, center.z + cameraZ);
        this.camera.lookAt(center);

        this.controls.target.copy(center);
        this.controls.update();
    }

    toggleWireframe() {
        this.showWireframe = !this.showWireframe;
        if (this.wireframeModel) {
            this.wireframeModel.visible = this.showWireframe;
        }
        if (this.currentModel) {
            this.currentModel.visible = !this.showWireframe;
        }
    }

    resetView() {
        if (this.currentModel) {
            this.fitCameraToModel();
        }
    }

    setModelColor(color) {
        if (this.currentModel) {
            this.currentModel.material.color.set(color);
        }
    }

    toggleAutoRotate() {
        this.controls.autoRotate = !this.controls.autoRotate;
    }

    clear() {
        if (this.currentModel) {
            this.scene.remove(this.currentModel);
            this.currentModel.geometry.dispose();
            this.currentModel.material.dispose();
            this.currentModel = null;
        }

        if (this.wireframeModel) {
            this.scene.remove(this.wireframeModel);
            this.wireframeModel.geometry.dispose();
            this.wireframeModel.material.dispose();
            this.wireframeModel = null;
        }
    }

    destroy() {
        window.removeEventListener('resize', this.onWindowResize);
        this.controls.dispose();
        this.renderer.dispose();
        this.container.removeChild(this.renderer.domElement);
    }
}

// Export for use in modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ModelViewer3D;
}
