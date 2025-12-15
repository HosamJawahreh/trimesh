/**
 * Simple 3D Viewer for STL, OBJ, PLY files
 * Uses Three.js from CDN
 */

// Load Three.js dynamically
function loadThreeJS() {
    return new Promise((resolve, reject) => {
        if (window.THREE) {
            resolve();
            return;
        }

        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/three@0.160.0/build/three.min.js';
        script.onload = () => {
            console.log('Three.js loaded');
            loadLoaders().then(resolve).catch(reject);
        };
        script.onerror = reject;
        document.head.appendChild(script);
    });
}

// Load additional loaders
function loadLoaders() {
    return Promise.all([
        loadScript('https://cdn.jsdelivr.net/npm/three@0.160.0/examples/js/controls/OrbitControls.js'),
        loadScript('https://cdn.jsdelivr.net/npm/three@0.160.0/examples/js/loaders/STLLoader.js'),
        loadScript('https://cdn.jsdelivr.net/npm/three@0.160.0/examples/js/loaders/OBJLoader.js'),
        loadScript('https://cdn.jsdelivr.net/npm/three@0.160.0/examples/js/loaders/PLYLoader.js')
    ]);
}

function loadScript(src) {
    return new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.src = src;
        script.onload = resolve;
        script.onerror = reject;
        document.head.appendChild(script);
    });
}

class Simple3DViewer {
    constructor(containerId, color = 0x667eea) {
        this.containerId = containerId;
        this.container = document.getElementById(containerId);
        this.color = color;
        this.scene = null;
        this.camera = null;
        this.renderer = null;
        this.controls = null;
        this.model = null;
        this.animationId = null;
    }

    async init() {
        if (!this.container) {
            console.error('Container not found:', this.containerId);
            return;
        }

        // Clear container
        this.container.innerHTML = '';

        const THREE = window.THREE;

        // Scene
        this.scene = new THREE.Scene();
        this.scene.background = new THREE.Color(this.color);

        // Camera
        this.camera = new THREE.PerspectiveCamera(
            45,
            this.container.clientWidth / this.container.clientHeight,
            0.1,
            10000
        );
        this.camera.position.set(0, 0, 100);

        // Renderer
        this.renderer = new THREE.WebGLRenderer({ antialias: true });
        this.renderer.setSize(this.container.clientWidth, this.container.clientHeight);
        this.renderer.setPixelRatio(window.devicePixelRatio);
        this.container.appendChild(this.renderer.domElement);

        // Controls
        this.controls = new THREE.OrbitControls(this.camera, this.renderer.domElement);
        this.controls.enableDamping = true;
        this.controls.dampingFactor = 0.05;

        // Lights
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
        this.scene.add(ambientLight);

        const directionalLight1 = new THREE.DirectionalLight(0xffffff, 0.8);
        directionalLight1.position.set(1, 1, 1);
        this.scene.add(directionalLight1);

        const directionalLight2 = new THREE.DirectionalLight(0xffffff, 0.5);
        directionalLight2.position.set(-1, -1, -1);
        this.scene.add(directionalLight2);

        // Grid
        const gridHelper = new THREE.GridHelper(200, 20, 0xffffff, 0xffffff);
        gridHelper.material.opacity = 0.1;
        gridHelper.material.transparent = true;
        this.scene.add(gridHelper);

        // Start animation
        this.animate();

        console.log('Viewer initialized:', this.containerId);
    }

    async loadFile(file) {
        const THREE = window.THREE;
        const extension = file.name.split('.').pop().toLowerCase();

        return new Promise((resolve, reject) => {
            const reader = new FileReader();

            reader.onload = (e) => {
                try {
                    const data = e.target.result;

                    if (extension === 'stl') {
                        const loader = new THREE.STLLoader();
                        const geometry = loader.parse(data);
                        this.createMesh(geometry);
                        resolve({ success: true, file: file.name });
                    } else if (extension === 'obj') {
                        const loader = new THREE.OBJLoader();
                        const text = new TextDecoder().decode(data);
                        const object = loader.parse(text);
                        this.addObject(object);
                        resolve({ success: true, file: file.name });
                    } else if (extension === 'ply') {
                        const loader = new THREE.PLYLoader();
                        const geometry = loader.parse(data);
                        this.createMesh(geometry);
                        resolve({ success: true, file: file.name });
                    } else {
                        reject(new Error('Unsupported file format: ' + extension));
                    }
                } catch (error) {
                    console.error('Error loading file:', error);
                    reject(error);
                }
            };

            reader.onerror = () => reject(new Error('Failed to read file'));
            reader.readAsArrayBuffer(file);
        });
    }

    createMesh(geometry) {
        const THREE = window.THREE;

        // Remove old model
        if (this.model) {
            this.scene.remove(this.model);
            if (this.model.geometry) this.model.geometry.dispose();
            if (this.model.material) this.model.material.dispose();
        }

        geometry.computeVertexNormals();
        geometry.center();

        const material = new THREE.MeshPhongMaterial({
            color: 0xffffff,
            specular: 0x111111,
            shininess: 200,
            flatShading: false
        });

        this.model = new THREE.Mesh(geometry, material);
        this.scene.add(this.model);

        this.fitCameraToModel();
        console.log('Mesh created and added to scene');
    }

    addObject(object) {
        const THREE = window.THREE;

        if (this.model) {
            this.scene.remove(this.model);
        }

        object.traverse((child) => {
            if (child instanceof THREE.Mesh) {
                child.material = new THREE.MeshPhongMaterial({
                    color: 0xffffff,
                    specular: 0x111111,
                    shininess: 200
                });
            }
        });

        this.model = object;
        this.scene.add(this.model);
        this.fitCameraToModel();
        console.log('Object added to scene');
    }

    fitCameraToModel() {
        if (!this.model) return;

        const THREE = window.THREE;
        const box = new THREE.Box3().setFromObject(this.model);
        const center = box.getCenter(new THREE.Vector3());
        const size = box.getSize(new THREE.Vector3());

        const maxDim = Math.max(size.x, size.y, size.z);
        const fov = this.camera.fov * (Math.PI / 180);
        let cameraZ = Math.abs(maxDim / 2 / Math.tan(fov / 2));
        cameraZ *= 1.5;

        this.camera.position.set(center.x, center.y, center.z + cameraZ);
        this.camera.lookAt(center);
        this.controls.target.copy(center);
        this.controls.update();
    }

    animate() {
        this.animationId = requestAnimationFrame(() => this.animate());
        this.controls.update();
        this.renderer.render(this.scene, this.camera);
    }

    dispose() {
        if (this.animationId) {
            cancelAnimationFrame(this.animationId);
        }
        if (this.renderer) {
            this.renderer.dispose();
        }
    }
}

// Global viewer instances
let generalViewer = null;
let medicalViewer = null;

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', async function() {
    console.log('Initializing 3D viewers...');

    try {
        // Load Three.js
        await loadThreeJS();
        console.log('Three.js and loaders loaded successfully');

        // Initialize viewers
        generalViewer = new Simple3DViewer('viewer3dGeneral', 0x667eea);
        await generalViewer.init();

        medicalViewer = new Simple3DViewer('viewer3dMedical', 0xff9800);
        await medicalViewer.init();

        console.log('Viewers initialized');

        // File input handlers
        const fileInput3d = document.getElementById('fileInput3d');
        const fileInputMedical3d = document.getElementById('fileInputMedical3d');

        if (fileInput3d) {
            fileInput3d.addEventListener('change', async function(e) {
                const files = this.files;
                if (files.length > 0) {
                    console.log('Loading file into general viewer:', files[0].name);
                    try {
                        const result = await generalViewer.loadFile(files[0]);
                        console.log('File loaded successfully:', result);
                        alert('File loaded: ' + result.file);
                    } catch (error) {
                        console.error('Error loading file:', error);
                        alert('Error loading file: ' + error.message);
                    }
                }
            });
        }

        if (fileInputMedical3d) {
            fileInputMedical3d.addEventListener('change', async function(e) {
                const files = this.files;
                if (files.length > 0) {
                    console.log('Loading file into medical viewer:', files[0].name);
                    try {
                        const result = await medicalViewer.loadFile(files[0]);
                        console.log('File loaded successfully:', result);
                        alert('File loaded: ' + result.file);
                    } catch (error) {
                        console.error('Error loading file:', error);
                        alert('Error loading file: ' + error.message);
                    }
                }
            });
        }

    } catch (error) {
        console.error('Error initializing 3D viewers:', error);
    }
});
