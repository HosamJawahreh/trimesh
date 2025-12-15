/**
 * 3D Quote System - Advanced 3D Model Viewer
 * Supports STL, OBJ, PLY file formats with interactive controls
 */

// Import Three.js and required loaders
import * as THREE from 'https://cdn.jsdelivr.net/npm/three@0.160.0/build/three.module.js';
import { OrbitControls } from 'https://cdn.jsdelivr.net/npm/three@0.160.0/examples/jsm/controls/OrbitControls.js';
import { STLLoader } from 'https://cdn.jsdelivr.net/npm/three@0.160.0/examples/jsm/loaders/STLLoader.js';
import { OBJLoader } from 'https://cdn.jsdelivr.net/npm/three@0.160.0/examples/jsm/loaders/OBJLoader.js';
import { PLYLoader } from 'https://cdn.jsdelivr.net/npm/three@0.160.0/examples/jsm/loaders/PLYLoader.js';

class ThreeDViewer {
    constructor(containerId, formType = 'general') {
        this.containerId = containerId;
        this.formType = formType;
        this.container = document.getElementById(containerId);
        this.scene = null;
        this.camera = null;
        this.renderer = null;
        this.controls = null;
        this.model = null;
        this.wireframeMode = false;
        this.autoRotate = false;
        this.animationId = null;

        this.init();
    }

    init() {
        if (!this.container) return;

        // Clear container
        this.container.innerHTML = '';

        // Scene setup
        this.scene = new THREE.Scene();
        this.scene.background = new THREE.Color(this.formType === 'general' ? 0x667eea : 0xff9800);

        // Camera setup
        this.camera = new THREE.PerspectiveCamera(
            45,
            this.container.clientWidth / this.container.clientHeight,
            0.1,
            10000
        );
        this.camera.position.set(0, 0, 100);

        // Renderer setup
        this.renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
        this.renderer.setSize(this.container.clientWidth, this.container.clientHeight);
        this.renderer.setPixelRatio(window.devicePixelRatio);
        this.container.appendChild(this.renderer.domElement);

        // Controls setup
        this.controls = new OrbitControls(this.camera, this.renderer.domElement);
        this.controls.enableDamping = true;
        this.controls.dampingFactor = 0.05;
        this.controls.screenSpacePanning = false;
        this.controls.minDistance = 10;
        this.controls.maxDistance = 500;

        // Lighting
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
        this.scene.add(ambientLight);

        const directionalLight1 = new THREE.DirectionalLight(0xffffff, 0.8);
        directionalLight1.position.set(1, 1, 1);
        this.scene.add(directionalLight1);

        const directionalLight2 = new THREE.DirectionalLight(0xffffff, 0.5);
        directionalLight2.position.set(-1, -1, -1);
        this.scene.add(directionalLight2);

        // Grid helper
        const gridHelper = new THREE.GridHelper(200, 20, 0xffffff, 0xffffff);
        gridHelper.material.opacity = 0.1;
        gridHelper.material.transparent = true;
        this.scene.add(gridHelper);

        // Axes helper
        const axesHelper = new THREE.AxesHelper(50);
        axesHelper.material.opacity = 0.3;
        axesHelper.material.transparent = true;
        this.scene.add(axesHelper);

        // Handle window resize
        window.addEventListener('resize', () => this.onWindowResize());

        // Start animation loop
        this.animate();
    }

    loadModel(file) {
        return new Promise((resolve, reject) => {
            const extension = file.name.split('.').pop().toLowerCase();
            const reader = new FileReader();

            reader.onload = (e) => {
                try {
                    let loader;
                    const arrayBuffer = e.target.result;

                    switch (extension) {
                        case 'stl':
                            loader = new STLLoader();
                            const geometry = loader.parse(arrayBuffer);
                            this.createMesh(geometry);
                            break;

                        case 'obj':
                            loader = new OBJLoader();
                            const text = new TextDecoder().decode(arrayBuffer);
                            const object = loader.parse(text);
                            this.addObject(object);
                            break;

                        case 'ply':
                            loader = new PLYLoader();
                            const plyGeometry = loader.parse(arrayBuffer);
                            this.createMesh(plyGeometry);
                            break;

                        default:
                            reject(new Error('Unsupported file format'));
                            return;
                    }

                    const modelInfo = this.analyzeModel();
                    resolve(modelInfo);
                } catch (error) {
                    reject(error);
                }
            };

            reader.onerror = () => reject(new Error('Failed to read file'));
            reader.readAsArrayBuffer(file);
        });
    }

    createMesh(geometry) {
        // Remove previous model
        if (this.model) {
            this.scene.remove(this.model);
            this.model.geometry.dispose();
            this.model.material.dispose();
        }

        geometry.computeVertexNormals();
        geometry.center();

        const material = new THREE.MeshPhongMaterial({
            color: this.formType === 'general' ? 0x667eea : 0xff9800,
            specular: 0x111111,
            shininess: 200,
            flatShading: false,
        });

        this.model = new THREE.Mesh(geometry, material);
        this.scene.add(this.model);

        this.fitCameraToModel();
    }

    addObject(object) {
        if (this.model) {
            this.scene.remove(this.model);
        }

        object.traverse((child) => {
            if (child instanceof THREE.Mesh) {
                child.material = new THREE.MeshPhongMaterial({
                    color: this.formType === 'general' ? 0x667eea : 0xff9800,
                    specular: 0x111111,
                    shininess: 200,
                });
            }
        });

        this.model = object;
        this.scene.add(this.model);
        this.fitCameraToModel();
    }

    fitCameraToModel() {
        if (!this.model) return;

        const box = new THREE.Box3().setFromObject(this.model);
        const center = box.getCenter(new THREE.Vector3());
        const size = box.getSize(new THREE.Vector3());

        const maxDim = Math.max(size.x, size.y, size.z);
        const fov = this.camera.fov * (Math.PI / 180);
        let cameraZ = Math.abs(maxDim / 2 / Math.tan(fov / 2));
        cameraZ *= 1.5; // Add some padding

        this.camera.position.set(center.x, center.y, center.z + cameraZ);
        this.camera.lookAt(center);
        this.controls.target.copy(center);
        this.controls.update();
    }

    analyzeModel() {
        if (!this.model) return null;

        const box = new THREE.Box3().setFromObject(this.model);
        const size = box.getSize(new THREE.Vector3());

        let volume = 0;
        this.model.traverse((child) => {
            if (child instanceof THREE.Mesh && child.geometry) {
                const geometry = child.geometry;
                if (geometry.index) {
                    // Calculate volume for indexed geometry
                    const positions = geometry.attributes.position.array;
                    const indices = geometry.index.array;

                    for (let i = 0; i < indices.length; i += 3) {
                        const i1 = indices[i] * 3;
                        const i2 = indices[i + 1] * 3;
                        const i3 = indices[i + 2] * 3;

                        const v1 = new THREE.Vector3(positions[i1], positions[i1 + 1], positions[i1 + 2]);
                        const v2 = new THREE.Vector3(positions[i2], positions[i2 + 1], positions[i2 + 2]);
                        const v3 = new THREE.Vector3(positions[i3], positions[i3 + 1], positions[i3 + 2]);

                        volume += this.signedVolumeOfTriangle(v1, v2, v3);
                    }
                }
            }
        });

        return {
            width: size.x.toFixed(2),
            height: size.y.toFixed(2),
            depth: size.z.toFixed(2),
            volume: Math.abs(volume).toFixed(2),
        };
    }

    signedVolumeOfTriangle(p1, p2, p3) {
        return p1.dot(p2.cross(p3)) / 6.0;
    }

    toggleWireframe() {
        this.wireframeMode = !this.wireframeMode;
        if (this.model) {
            this.model.traverse((child) => {
                if (child instanceof THREE.Mesh) {
                    child.material.wireframe = this.wireframeMode;
                }
            });
        }
    }

    toggleAutoRotate() {
        this.autoRotate = !this.autoRotate;
        this.controls.autoRotate = this.autoRotate;
    }

    resetView() {
        if (this.model) {
            this.fitCameraToModel();
        } else {
            this.camera.position.set(0, 0, 100);
            this.camera.lookAt(0, 0, 0);
            this.controls.target.set(0, 0, 0);
            this.controls.update();
        }
    }

    onWindowResize() {
        if (!this.container) return;

        this.camera.aspect = this.container.clientWidth / this.container.clientHeight;
        this.camera.updateProjectionMatrix();
        this.renderer.setSize(this.container.clientWidth, this.container.clientHeight);
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
        if (this.controls) {
            this.controls.dispose();
        }
    }
}

// File Manager Class
class FileManager {
    constructor(formType = 'general') {
        this.formType = formType;
        this.files = [];
        this.maxFileSize = formType === 'medical' ? 100 * 1024 * 1024 : 50 * 1024 * 1024; // 100MB for medical, 50MB for general
        this.allowedExtensions = ['.stl', '.obj', '.ply'];
    }

    addFile(file) {
        if (!this.validateFile(file)) {
            return false;
        }

        this.files.push({
            id: Date.now() + Math.random(),
            file: file,
            name: file.name,
            size: file.size,
            type: file.type,
        });

        return true;
    }

    validateFile(file) {
        const extension = '.' + file.name.split('.').pop().toLowerCase();

        if (!this.allowedExtensions.includes(extension)) {
            alert(`Invalid file type. Allowed types: ${this.allowedExtensions.join(', ')}`);
            return false;
        }

        if (file.size > this.maxFileSize) {
            alert(`File size exceeds maximum allowed size of ${this.maxFileSize / (1024 * 1024)}MB`);
            return false;
        }

        return true;
    }

    removeFile(fileId) {
        this.files = this.files.filter(f => f.id !== fileId);
    }

    getFiles() {
        return this.files;
    }

    clear() {
        this.files = [];
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    let currentViewer = null;
    let currentFileManager = null;
    let currentForm = 'general';

    // Initialize General Form
    function initGeneralForm() {
        currentForm = 'general';
        currentViewer = new ThreeDViewer('viewer3d', 'general');
        currentFileManager = new FileManager('general');

        setupFormControls('general');
    }

    // Initialize Medical Form
    function initMedicalForm() {
        currentForm = 'medical';
        currentViewer = new ThreeDViewer('viewer3dMedical', 'medical');
        currentFileManager = new FileManager('medical');

        setupFormControls('medical');
    }

    // Setup form controls
    function setupFormControls(formType) {
        const suffix = formType === 'general' ? '' : 'Medical';

        // File input
        const fileInput = document.getElementById(`fileInput${suffix}`);
        const dropZone = document.getElementById(`dropZone${suffix}`);
        const btnBrowse = document.getElementById(`btnBrowse${suffix}`);

        // Drag and drop
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('dragover');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            handleFiles(e.dataTransfer.files, formType);
        });

        dropZone.addEventListener('click', () => {
            fileInput.click();
        });

        btnBrowse.addEventListener('click', (e) => {
            e.stopPropagation();
            fileInput.click();
        });

        fileInput.addEventListener('change', (e) => {
            handleFiles(e.target.files, formType);
        });

        // Viewer controls
        const btnReset = document.getElementById(`btnResetView${suffix}`);
        const btnWireframe = document.getElementById(`btnToggleWireframe${suffix}`);
        const btnRotate = document.getElementById(`btnToggleRotate${suffix}`);

        btnReset?.addEventListener('click', () => {
            currentViewer?.resetView();
        });

        btnWireframe?.addEventListener('click', () => {
            currentViewer?.toggleWireframe();
            btnWireframe.classList.toggle('active');
        });

        btnRotate?.addEventListener('click', () => {
            currentViewer?.toggleAutoRotate();
            btnRotate.classList.toggle('active');
        });

        // Submit button
        const btnSubmit = document.getElementById(`btnSubmitQuote${suffix}`);
        btnSubmit?.addEventListener('click', () => {
            submitQuote(formType);
        });
    }

    // Handle file uploads
    async function handleFiles(files, formType) {
        const suffix = formType === 'general' ? '' : 'Medical';
        const filesList = document.getElementById(`filesList${suffix}`);
        const fileCount = document.getElementById(`fileCount${suffix}`);
        const emptyState = filesList.querySelector('.empty-state');

        for (let file of files) {
            if (currentFileManager.addFile(file)) {
                // Load first file into viewer
                if (currentFileManager.getFiles().length === 1) {
                    try {
                        const modelInfo = await currentViewer.loadModel(file);
                        updateModelInfo(modelInfo, formType);
                    } catch (error) {
                        console.error('Error loading model:', error);
                        alert('Failed to load 3D model. Please check the file format.');
                    }
                }

                // Add to list
                const fileItem = createFileItem(file, formType);
                if (emptyState) {
                    emptyState.remove();
                }
                filesList.appendChild(fileItem);
            }
        }

        fileCount.textContent = currentFileManager.getFiles().length;
    }

    // Create file item element
    function createFileItem(file, formType) {
        const suffix = formType === 'general' ? '' : 'Medical';
        const div = document.createElement('div');
        div.className = 'file-item';
        div.innerHTML = `
            <div class="d-flex align-items-center flex-grow-1">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-2">
                    <path d="M6 2L3 5V16C3 16.5304 3.21071 17.0391 3.58579 17.4142C3.96086 17.7893 4.46957 18 5 18H15C15.5304 18 16.0391 17.7893 16.4142 17.4142C16.7893 17.0391 17 16.5304 17 16V7L11 2H6Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M11 2V7H17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <div>
                    <div style="font-weight: 500; font-size: 14px;">${file.name}</div>
                    <div style="font-size: 12px; color: #6c757d;">${(file.size / 1024).toFixed(2)} KB</div>
                </div>
            </div>
            <button class="btn btn-sm btn-outline-danger" onclick="removeFile('${file.name}', '${formType}')">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2 4H14M12.6667 4V13.3333C12.6667 14 12 14.6667 11.3333 14.6667H4.66667C4 14.6667 3.33333 14 3.33333 13.3333V4M5.33333 4V2.66667C5.33333 2 6 1.33333 6.66667 1.33333H9.33333C10 1.33333 10.6667 2 10.6667 2.66667V4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        `;
        return div;
    }

    // Update model information display
    function updateModelInfo(info, formType) {
        const suffix = formType === 'general' ? '' : 'Medical';
        const modelInfo = document.getElementById(`modelInfo${suffix}`);

        if (info && modelInfo) {
            document.getElementById(`infoVolume${suffix}`).textContent = `${info.volume} mm³`;
            document.getElementById(`infoWidth${suffix}`).textContent = `${info.width} mm`;
            document.getElementById(`infoHeight${suffix}`).textContent = `${info.height} mm`;
            document.getElementById(`infoDepth${suffix}`).textContent = `${info.depth} mm`;
            modelInfo.classList.remove('d-none');
        }
    }

    // Submit quote
    function submitQuote(formType) {
        const suffix = formType === 'general' ? '' : 'Medical';
        const name = document.getElementById(`customerName${suffix}`).value;
        const email = document.getElementById(`customerEmail${suffix}`).value;

        if (!name || !email) {
            alert('Please fill in your name and email');
            return;
        }

        if (currentFileManager.getFiles().length === 0) {
            alert('Please upload at least one 3D file');
            return;
        }

        // Here you would send the data to your backend
        console.log('Submitting quote:', {
            formType,
            name,
            email,
            files: currentFileManager.getFiles(),
        });

        alert('Quote request submitted successfully! We will contact you soon.');
    }

    // Form switcher
    const formSwitchers = document.querySelectorAll('.form-switch-btn');
    formSwitchers.forEach(btn => {
        btn.addEventListener('click', function() {
            const formType = this.getAttribute('data-form');

            // Update button states
            formSwitchers.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            // Show/hide forms
            document.getElementById('generalForm').classList.toggle('d-none', formType !== 'general');
            document.getElementById('medicalForm').classList.toggle('d-none', formType !== 'medical');

            // Dispose old viewer and initialize new one
            if (currentViewer) {
                currentViewer.dispose();
            }

            if (formType === 'general') {
                initGeneralForm();
            } else {
                initMedicalForm();
            }
        });
    });

    // Initialize general form by default
    initGeneralForm();

    // Make removeFile function global
    window.removeFile = function(fileName, formType) {
        const fileManager = currentFileManager;
        const file = fileManager.getFiles().find(f => f.name === fileName);
        if (file) {
            fileManager.removeFile(file.id);

            // Update UI
            const suffix = formType === 'general' ? '' : 'Medical';
            const filesList = document.getElementById(`filesList${suffix}`);
            const fileCount = document.getElementById(`fileCount${suffix}`);

            // Rebuild files list
            filesList.innerHTML = '';
            if (fileManager.getFiles().length === 0) {
                const emptyState = document.createElement('div');
                emptyState.className = 'empty-state text-center p-5';
                emptyState.innerHTML = `
                    <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" class="mb-3">
                        <circle cx="24" cy="24" r="24" fill="#f5f5f5"/>
                        <path d="M24 16V32M16 24H32" stroke="#bdbdbd" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <p class="mb-0 text-muted">No files uploaded yet</p>
                `;
                filesList.appendChild(emptyState);
            } else {
                fileManager.getFiles().forEach(f => {
                    filesList.appendChild(createFileItem(f.file, formType));
                });
            }

            fileCount.textContent = fileManager.getFiles().length;
        }
    };
});
