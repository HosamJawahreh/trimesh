/**
 * Enhanced 3D Viewer with Upload Progress
 * Supports STL, OBJ, PLY files with Three.js
 */

// Check if Three.js is already loaded
let threeLoaded = false;
let loadersLoaded = false;

// Load Three.js and loaders
async function loadThreeLibraries() {
    if (threeLoaded && loadersLoaded) {
        return Promise.resolve();
    }

    return new Promise((resolve, reject) => {
        // Load Three.js core
        const threeScript = document.createElement('script');
        threeScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js';
        threeScript.onload = () => {
            console.log('✓ Three.js core loaded');
            threeLoaded = true;

            // Load OrbitControls
            const controlsScript = document.createElement('script');
            controlsScript.src = 'https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js';
            controlsScript.onload = () => {
                console.log('✓ OrbitControls loaded');

                // Load STLLoader
                const stlScript = document.createElement('script');
                stlScript.src = 'https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/STLLoader.js';
                stlScript.onload = () => {
                    console.log('✓ STLLoader loaded');
                    loadersLoaded = true;
                    resolve();
                };
                stlScript.onerror = reject;
                document.head.appendChild(stlScript);
            };
            controlsScript.onerror = reject;
            document.head.appendChild(controlsScript);
        };
        threeScript.onerror = reject;
        document.head.appendChild(threeScript);
    });
}

// Show loading spinner
function showLoading(containerId, message = 'Loading...') {
    const container = document.getElementById(containerId);
    if (!container) return;

    container.innerHTML = `
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; color: white;">
            <div class="spinner-border" role="status" style="width: 3rem; height: 3rem; margin-bottom: 20px;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <h5 style="margin: 0; font-weight: 500;">${message}</h5>
        </div>
    `;
}

// Show error message
function showError(containerId, message) {
    const container = document.getElementById(containerId);
    if (!container) return;

    container.innerHTML = `
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; color: white;">
            <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" style="opacity: 0.7; margin-bottom: 20px;">
                <circle cx="32" cy="32" r="28" stroke="white" stroke-width="3"/>
                <path d="M32 20V36M32 44V46" stroke="white" stroke-width="3" stroke-linecap="round"/>
            </svg>
            <h5 style="margin: 0; font-weight: 500;">${message}</h5>
        </div>
    `;
}

// 3D Viewer Class
class Enhanced3DViewer {
    constructor(containerId, bgColor = 0x667eea) {
        this.containerId = containerId;
        this.container = document.getElementById(containerId);
        this.bgColor = bgColor;
        this.scene = null;
        this.camera = null;
        this.renderer = null;
        this.controls = null;
        this.model = null;
        this.animationId = null;
        this.initialized = false;
    }

    async initialize() {
        if (this.initialized) {
            console.log('Viewer already initialized:', this.containerId);
            return;
        }

        if (!this.container) {
            console.error('Container not found:', this.containerId);
            return;
        }

        console.log('Initializing viewer:', this.containerId);

        const THREE = window.THREE;
        if (!THREE) {
            console.error('THREE is not loaded!');
            return;
        }

        // Clear container
        this.container.innerHTML = '';

        // Create scene
        this.scene = new THREE.Scene();
        this.scene.background = new THREE.Color(this.bgColor);

        // Create camera
        const width = this.container.clientWidth;
        const height = this.container.clientHeight;
        this.camera = new THREE.PerspectiveCamera(50, width / height, 0.1, 10000);
        this.camera.position.set(0, 0, 200);

        // Create renderer
        this.renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
        this.renderer.setSize(width, height);
        this.renderer.setPixelRatio(window.devicePixelRatio);
        this.container.appendChild(this.renderer.domElement);

        // Add controls
        this.controls = new THREE.OrbitControls(this.camera, this.renderer.domElement);
        this.controls.enableDamping = true;
        this.controls.dampingFactor = 0.05;
        this.controls.minDistance = 10;
        this.controls.maxDistance = 1000;

        // Add lights
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.7);
        this.scene.add(ambientLight);

        const directionalLight = new THREE.DirectionalLight(0xffffff, 0.8);
        directionalLight.position.set(1, 1, 1).normalize();
        this.scene.add(directionalLight);

        const directionalLight2 = new THREE.DirectionalLight(0xffffff, 0.5);
        directionalLight2.position.set(-1, -1, -1).normalize();
        this.scene.add(directionalLight2);

        // Add grid
        const gridHelper = new THREE.GridHelper(500, 20, 0xffffff, 0xffffff);
        gridHelper.material.opacity = 0.1;
        gridHelper.material.transparent = true;
        this.scene.add(gridHelper);

        this.initialized = true;
        console.log('✓ Viewer initialized:', this.containerId);

        // Start animation loop
        this.animate();
    }

    async loadSTL(file) {
        console.log('Loading STL file:', file.name);
        showLoading(this.containerId, 'Loading 3D model...');

        return new Promise((resolve, reject) => {
            const reader = new FileReader();

            reader.onload = (event) => {
                try {
                    const THREE = window.THREE;
                    const loader = new THREE.STLLoader();
                    const geometry = loader.parse(event.target.result);

                    console.log('STL parsed successfully');

                    // Remove old model
                    if (this.model) {
                        this.scene.remove(this.model);
                        if (this.model.geometry) this.model.geometry.dispose();
                        if (this.model.material) this.model.material.dispose();
                    }

                    // Compute normals and center
                    geometry.computeVertexNormals();
                    geometry.center();

                    // Create material
                    const material = new THREE.MeshPhongMaterial({
                        color: 0xffffff,
                        specular: 0x222222,
                        shininess: 50,
                        side: THREE.DoubleSide
                    });

                    // Create mesh
                    this.model = new THREE.Mesh(geometry, material);
                    this.scene.add(this.model);

                    console.log('✓ Model added to scene');

                    // Fit camera to model
                    this.fitCameraToModel();

                    // Clear loading message
                    this.container.querySelector('div[style*="position: absolute"]')?.remove();

                    resolve({
                        success: true,
                        vertices: geometry.attributes.position.count,
                        file: file.name
                    });
                } catch (error) {
                    console.error('Error parsing STL:', error);
                    showError(this.containerId, 'Failed to load model');
                    reject(error);
                }
            };

            reader.onerror = () => {
                const error = new Error('Failed to read file');
                console.error(error);
                showError(this.containerId, 'Failed to read file');
                reject(error);
            };

            reader.readAsArrayBuffer(file);
        });
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
        cameraZ *= 2.0; // Zoom out a bit

        this.camera.position.set(center.x + cameraZ * 0.5, center.y + cameraZ * 0.5, center.z + cameraZ);
        this.camera.lookAt(center);
        this.controls.target.copy(center);
        this.controls.update();

        console.log('✓ Camera fitted to model');
    }

    animate() {
        this.animationId = requestAnimationFrame(() => this.animate());

        if (this.controls) {
            this.controls.update();
        }

        if (this.renderer && this.scene && this.camera) {
            this.renderer.render(this.scene, this.camera);
        }
    }

    dispose() {
        if (this.animationId) {
            cancelAnimationFrame(this.animationId);
        }
        if (this.renderer) {
            this.renderer.dispose();
        }
        if (this.controls) {
            this.controls.dispose();
        }
    }
}

// Global viewer instances
let generalViewer3D = null;
let medicalViewer3D = null;

// Initialize everything when DOM is ready
document.addEventListener('DOMContentLoaded', async function() {
    console.log('=== Starting 3D Viewer Initialization ===');

    try {
        // Show loading in both viewers
        showLoading('viewer3dGeneral', 'Initializing 3D viewer...');
        showLoading('viewer3dMedical', 'Initializing 3D viewer...');

        // Load Three.js libraries
        console.log('Loading Three.js libraries...');
        await loadThreeLibraries();
        console.log('✓ All libraries loaded');

        // Wait a bit for libraries to be ready
        await new Promise(resolve => setTimeout(resolve, 500));

        // Initialize viewers
        console.log('Creating viewer instances...');
        generalViewer3D = new Enhanced3DViewer('viewer3dGeneral', 0x667eea);
        medicalViewer3D = new Enhanced3DViewer('viewer3dMedical', 0xff9800);

        await generalViewer3D.initialize();
        await medicalViewer3D.initialize();

        console.log('✓ Both viewers initialized successfully');

        // Setup file input handlers
        setupFileHandlers();

        console.log('=== 3D Viewer Initialization Complete ===');

    } catch (error) {
        console.error('Error during initialization:', error);
        showError('viewer3dGeneral', 'Initialization failed');
        showError('viewer3dMedical', 'Initialization failed');
    }
});

function setupFileHandlers() {
    // General form file input
    const fileInput3d = document.getElementById('fileInput3d');
    if (fileInput3d) {
        console.log('✓ General file input found');

        fileInput3d.addEventListener('change', async function(e) {
            const files = this.files;
            if (files.length > 0) {
                const file = files[0];
                console.log('File selected:', file.name, 'Size:', file.size, 'Type:', file.type);

                // Check file extension
                const ext = file.name.split('.').pop().toLowerCase();
                if (ext !== 'stl') {
                    alert('Currently only STL files are supported. OBJ and PLY support coming soon!');
                    return;
                }

                try {
                    const result = await generalViewer3D.loadSTL(file);
                    console.log('✓ File loaded successfully:', result);

                    // Show success message
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });

                    Toast.fire({
                        icon: 'success',
                        title: `Model loaded: ${result.file}`
                    });
                } catch (error) {
                    console.error('Error loading file:', error);
                    alert('Error loading file: ' + error.message);
                }
            }
        });
    } else {
        console.warn('General file input not found');
    }

    // Medical form file input
    const fileInputMedical3d = document.getElementById('fileInputMedical3d');
    if (fileInputMedical3d) {
        console.log('✓ Medical file input found');

        fileInputMedical3d.addEventListener('change', async function(e) {
            const files = this.files;
            if (files.length > 0) {
                const file = files[0];
                console.log('Medical file selected:', file.name);

                const ext = file.name.split('.').pop().toLowerCase();
                if (ext !== 'stl') {
                    alert('Currently only STL files are supported. OBJ and PLY support coming soon!');
                    return;
                }

                try {
                    const result = await medicalViewer3D.loadSTL(file);
                    console.log('✓ Medical file loaded successfully:', result);

                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });

                    Toast.fire({
                        icon: 'success',
                        title: `Model loaded: ${result.file}`
                    });
                } catch (error) {
                    console.error('Error loading medical file:', error);
                    alert('Error loading file: ' + error.message);
                }
            }
        });
    } else {
        console.warn('Medical file input not found');
    }

    // Add drag and drop support
    setupDragAndDrop();
}

function setupDragAndDrop() {
    const dropZones = document.querySelectorAll('.upload-drop-zone-3d');

    dropZones.forEach(zone => {
        zone.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.style.borderColor = '#667eea';
            this.style.background = '#e3f2fd';
        });

        zone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.style.borderColor = '#dee2e6';
            this.style.background = '#f8f9fa';
        });

        zone.addEventListener('drop', async function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.style.borderColor = '#dee2e6';
            this.style.background = '#f8f9fa';

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const file = files[0];
                console.log('File dropped:', file.name);

                // Determine which viewer to use
                const isGeneral = this.closest('#generalForm3d') !== null;
                const viewer = isGeneral ? generalViewer3D : medicalViewer3D;

                const ext = file.name.split('.').pop().toLowerCase();
                if (ext !== 'stl') {
                    alert('Currently only STL files are supported.');
                    return;
                }

                try {
                    const result = await viewer.loadSTL(file);
                    console.log('✓ Dropped file loaded:', result);
                    alert(`Model loaded: ${result.file}\nVertices: ${result.vertices}`);
                } catch (error) {
                    console.error('Error loading dropped file:', error);
                    alert('Error loading file: ' + error.message);
                }
            }
        });
    });
}

// Make viewers globally accessible for debugging
window.generalViewer3D = generalViewer3D;
window.medicalViewer3D = medicalViewer3D;

console.log('3D Viewer script loaded');
