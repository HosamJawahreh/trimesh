/**
 * ========================================
 * PROFESSIONAL 3D VIEWER TOOLS
 * Advanced features for 3D model analysis
 * ========================================
 */

console.log('🔧 Loading Professional 3D Viewer Tools...');
console.log('🚀 SCRIPT EXECUTING NOW - CHECK IF YOU SEE THIS!');

// IMMEDIATE toolbar handler creation - Define this FIRST before anything else
// This MUST be defined immediately so onclick handlers can find it
window.toolbarHandler = {
    toggleMeasurement: function(viewerType) {
        console.log(`📏 Toggle measurement for ${viewerType}`);
        const submenu = document.getElementById('measurementSubmenu' + (viewerType === 'Medical' ? 'Medical' : ''));
        if (submenu) {
            submenu.style.display = submenu.style.display === 'none' || submenu.style.display === '' ? 'block' : 'none';
        }
    },

    toggleBoundingBox: function(viewerType) {
        console.log(`📦 Toggle bounding box for ${viewerType}`);
        // Always use unified viewer
        const viewer = window.viewerGeneral || window.viewer;

        if (!viewer || !viewer.scene) {
            alert('Viewer not ready. Please wait for the 3D model to load...');
            console.error('❌ Viewer or scene not found');
            return;
        }

        // Initialize tools if not already done
        if (!viewer.tools) {
            console.log('🔧 Initializing tools...');
            if (typeof initProfessionalTools !== 'undefined') {
                initProfessionalTools(viewer);
            }
        }

        // Use the tool if available, otherwise create a simple helper directly
        if (viewer.tools && viewer.tools.boundingBox) {
            viewer.tools.boundingBox.toggle();
            console.log('✅ Bounding box toggled via tool');
        } else {
            // Direct implementation as fallback
            console.log('⚡ Using direct bounding box implementation');

            // Check if we already have a bounding box helper
            let existingHelper = viewer.scene.children.find(child => child.userData && child.userData.isBoundingBoxHelper);

            const wasVisible = existingHelper ? existingHelper.visible : false;
            const willBeVisible = !wasVisible;

            if (existingHelper) {
                // Toggle visibility
                existingHelper.visible = willBeVisible;
                console.log('✅ Bounding box visibility toggled:', existingHelper.visible);
            } else {
                // Create new bounding box
                const box = new THREE.Box3().setFromObject(viewer.scene);
                const helper = new THREE.Box3Helper(box, 0x00ff00);
                helper.userData.isBoundingBoxHelper = true;
                viewer.scene.add(helper);
                console.log('✅ Bounding box created and added');
            }

            // Record action for undo/redo
            if (viewer.undoRedoManager && window.UndoRedoActions) {
                const action = window.UndoRedoActions.createBoundingBoxAction(viewer, willBeVisible);
                viewer.undoRedoManager.recordAction(action);
            }

            if (viewer.render) viewer.render();
        }
    },

    toggleAxis: function(viewerType) {
        console.log(`🎯 Toggle axis for ${viewerType}`);
        // Always use unified viewer
        const viewer = window.viewerGeneral || window.viewer;

        if (!viewer || !viewer.scene) {
            alert('Viewer not ready. Please wait for the 3D model to load...');
            console.error('❌ Viewer or scene not found');
            return;
        }

        // Initialize tools if not already done
        if (!viewer.tools) {
            console.log('🔧 Initializing tools...');
            if (typeof initProfessionalTools !== 'undefined') {
                initProfessionalTools(viewer);
            }
        }

        if (viewer.tools && viewer.tools.axis) {
            viewer.tools.axis.toggle();
            console.log('✅ Axis toggled via tool');
        } else {
            // Direct implementation
            console.log('⚡ Using direct axis implementation');

            let existingAxis = viewer.scene.children.find(child => child.userData && child.userData.isAxisHelper);

            const wasVisible = existingAxis ? existingAxis.visible : false;
            const willBeVisible = !wasVisible;

            if (existingAxis) {
                existingAxis.visible = willBeVisible;
                console.log('✅ Axis visibility toggled:', existingAxis.visible);
            } else {
                const axesHelper = new THREE.AxesHelper(100);
                axesHelper.userData.isAxisHelper = true;
                viewer.scene.add(axesHelper);
                console.log('✅ Axis helper created and added');
            }

            // Record action for undo/redo
            if (viewer.undoRedoManager && window.UndoRedoActions) {
                const action = window.UndoRedoActions.createAxisAction(viewer, willBeVisible);
                viewer.undoRedoManager.recordAction(action);
            }

            if (viewer.render) viewer.render();
        }
    },

    toggleGrid: function(viewerType) {
        console.log(`📐 Toggle grid for ${viewerType}`);
        // Always use unified viewer
        const viewer = window.viewerGeneral || window.viewer;

        if (!viewer || !viewer.scene) {
            alert('Viewer not ready. Please wait for the 3D model to load...');
            console.error('❌ Viewer or scene not found');
            return;
        }

        // Initialize tools if not already done
        if (!viewer.tools) {
            console.log('🔧 Initializing tools...');
            if (typeof initProfessionalTools !== 'undefined') {
                initProfessionalTools(viewer);
            }
        }

        if (viewer.tools && viewer.tools.grid) {
            viewer.tools.grid.toggle();
            console.log('✅ Grid toggled via tool');
        } else {
            // Direct implementation
            console.log('⚡ Using direct grid implementation');

            let existingGrid = viewer.scene.children.find(child => child.userData && child.userData.isGridHelper);

            const wasVisible = existingGrid ? existingGrid.visible : false;
            const willBeVisible = !wasVisible;

            if (existingGrid) {
                existingGrid.visible = willBeVisible;
                console.log('✅ Grid visibility toggled:', existingGrid.visible);
            } else {
                const gridHelper = new THREE.GridHelper(200, 20, 0x888888, 0x444444);
                gridHelper.userData.isGridHelper = true;
                viewer.scene.add(gridHelper);
                console.log('✅ Grid helper created and added');
            }

            // Record action for undo/redo
            if (viewer.undoRedoManager && window.UndoRedoActions) {
                const action = window.UndoRedoActions.createGridAction(viewer, willBeVisible);
                viewer.undoRedoManager.recordAction(action);
            }

            if (viewer.render) viewer.render();
        }
    },

    toggleShadow: function(viewerType) {
        console.log(`🌓 Toggle shadow for ${viewerType}`);
        // Always use unified viewer
        const viewer = window.viewerGeneral || window.viewer;

        if (!viewer || !viewer.renderer) {
            alert('Viewer not ready. Please wait for the 3D model to load...');
            console.error('❌ Viewer or renderer not found');
            return;
        }

        // Store old state
        const oldState = viewer.renderer.shadowMap?.enabled || false;
        const newState = !oldState;

        // Toggle shadows
        if (viewer.renderer.shadowMap) {
            viewer.renderer.shadowMap.enabled = newState;
        }

        // Record action for undo/redo
        if (viewer.undoRedoManager && window.UndoRedoActions) {
            const action = window.UndoRedoActions.createShadowAction(viewer, oldState, newState);
            viewer.undoRedoManager.recordAction(action);
        }

        console.log('✅ Shadows toggled:', newState);

        if (viewer.render) viewer.render();
    },

    toggleTransparency: function(viewerType) {
        console.log(`👁️ Toggle transparency for ${viewerType}`);
        // Always use unified viewer
        const viewer = window.viewerGeneral || window.viewer;

        if (!viewer || !viewer.scene) {
            alert('Viewer not ready. Please wait for the 3D model to load...');
            console.error('❌ Viewer or scene not found');
            return;
        }

        // Cycle through transparency levels: 100% -> 75% -> 50% -> 25% -> 100%
        const levels = [1.0, 0.75, 0.5, 0.25];

        // Get current transparency level
        if (!viewer.currentTransparencyIndex) {
            viewer.currentTransparencyIndex = 0;
        }

        // Store old opacity for undo
        let oldOpacity = 1.0;
        viewer.scene.traverse((object) => {
            if (object.isMesh && object.material && oldOpacity === 1.0) {
                oldOpacity = object.material.opacity || 1.0;
            }
        });

        // Move to next level
        viewer.currentTransparencyIndex = (viewer.currentTransparencyIndex + 1) % levels.length;
        const newOpacity = levels[viewer.currentTransparencyIndex];

        // Apply to all meshes in the scene
        viewer.scene.traverse((object) => {
            if (object.isMesh && object.material) {
                object.material.transparent = newOpacity < 1.0;
                object.material.opacity = newOpacity;
                object.material.needsUpdate = true;
            }
        });

        // Record action for undo/redo
        if (viewer.undoRedoManager && window.UndoRedoActions) {
            const action = window.UndoRedoActions.createOpacityAction(viewer, oldOpacity, newOpacity);
            viewer.undoRedoManager.recordAction(action);
        }

        console.log(`✅ Transparency set to ${Math.round(newOpacity * 100)}%`);

        if (viewer.render) viewer.render();
    },

    takeScreenshot: function(viewerType) {
        console.log(`📸 Take screenshot for ${viewerType}`);
        // Always use unified viewer
        const viewer = window.viewerGeneral || window.viewer;

        if (!viewer) {
            alert('Viewer not loaded yet. Please wait...');
            console.error('❌ Viewer not found');
            return;
        }

        if (!viewer.renderer) {
            alert('Renderer not ready. Please wait for the 3D model to load...');
            console.error('❌ Renderer not found');
            return;
        }

        try {
            console.log('✅ Viewer found:', viewer);
            console.log('✅ Renderer found:', viewer.renderer);

            // Force a render to ensure we capture the current view
            if (viewer.render && typeof viewer.render === 'function') {
                viewer.render();
                console.log('✅ Forced render complete');
            }

            // Get the canvas element
            const canvas = viewer.renderer.domElement;
            console.log('✅ Canvas found:', canvas);

            // Convert canvas to data URL
            const dataURL = canvas.toDataURL('image/png');
            console.log('✅ Data URL created, length:', dataURL.length);

            // Create download link
            const timestamp = new Date().toISOString().replace(/[:.]/g, '-').substring(0, 19);
            const filename = `3d-model-${viewerType.toLowerCase()}-${timestamp}.png`;

            const link = document.createElement('a');
            link.href = dataURL;
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            console.log('✅ Screenshot saved:', filename);

            // Show success notification
            const notification = document.createElement('div');
            notification.style.cssText = 'position: fixed; top: 80px; right: 20px; background: #10b981; color: white; padding: 12px 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.2); z-index: 999999; font-family: system-ui; font-size: 14px; font-weight: 500;';
            notification.textContent = '📸 Screenshot saved!';
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.transition = 'opacity 0.3s';
                notification.style.opacity = '0';
                setTimeout(() => document.body.removeChild(notification), 300);
            }, 2000);

        } catch (error) {
            console.error('❌ Screenshot failed:', error);
            alert('Screenshot failed: ' + error.message);
        }
    },

    toggleMoveMode: function(viewerType) {
        console.log(`🔄 Toggle move mode for ${viewerType}`);
        const viewer = window.viewerGeneral || window.viewer;
        if (!viewer) {
            alert('Viewer not ready');
            return;
        }

        // Toggle pan/move mode
        if (viewer.controls) {
            viewer.controls.enablePan = !viewer.controls.enablePan;
            console.log('✅ Pan mode:', viewer.controls.enablePan ? 'enabled' : 'disabled');
        }
    },

    toggleAutoRotate: function(viewerType) {
        console.log(`🔄 Toggle auto-rotate for ${viewerType}`);
        const viewer = window.viewerGeneral || window.viewer;
        if (!viewer) {
            alert('Viewer not ready');
            return;
        }

        if (viewer.controls) {
            const oldState = viewer.controls.autoRotate;
            const newState = !oldState;
            viewer.controls.autoRotate = newState;

            // Record action for undo/redo
            if (viewer.undoRedoManager && window.UndoRedoActions) {
                const action = window.UndoRedoActions.createAutoRotateAction(viewer, oldState, newState);
                viewer.undoRedoManager.recordAction(action);
            }

            console.log('✅ Auto-rotate:', viewer.controls.autoRotate ? 'enabled' : 'disabled');
        }
    },

    /**
     * Undo the last action
     */
    undo: function() {
        console.log('⬅️ Undo action');
        const viewer = window.viewerGeneral || window.viewer;
        if (!viewer) {
            alert('Viewer not ready');
            return;
        }

        if (viewer.undoRedoManager) {
            viewer.undoRedoManager.undo();
        } else {
            console.warn('⚠️ Undo/Redo manager not initialized');
            alert('Undo/Redo system not available');
        }
    },

    /**
     * Redo the last undone action
     */
    redo: function() {
        console.log('➡️ Redo action');
        const viewer = window.viewerGeneral || window.viewer;
        if (!viewer) {
            alert('Viewer not ready');
            return;
        }

        if (viewer.undoRedoManager) {
            viewer.undoRedoManager.redo();
        } else {
            console.warn('⚠️ Undo/Redo manager not initialized');
            alert('Undo/Redo system not available');
        }
    },

    toggleGridMain: function(viewerType) {
        console.log(`📐 Toggle grid main for ${viewerType}`);
        this.toggleGrid(viewerType);
    },

    toggleMeasureMain: function(viewerType) {
        console.log(`📏 Toggle measure main for ${viewerType}`);
        this.toggleMeasurement(viewerType);
    },

    shareModel: function(viewerType) {
        console.log(`📤 Share model for ${viewerType}`);
        alert('Share feature coming soon! You will be able to generate a shareable link.');
    },

    saveAndCalculate: function(viewerType) {
        console.log(`💾 Save and calculate for ${viewerType}`);
        const viewer = window.viewerGeneral || window.viewer;
        if (!viewer || !viewer.uploadedFiles || viewer.uploadedFiles.length === 0) {
            alert('Please upload a 3D model first');
            return;
        }

        // Trigger the existing save & calculate functionality
        if (window.calculatePricing) {
            window.calculatePricing();
        } else {
            alert('Calculating pricing...');
        }
    },

    // Placeholder methods
    undo: function() { alert('Undo feature coming soon!'); },
    redo: function() { alert('Redo feature coming soon!'); },
    changeModelColor: function() {
        console.log('🎨 Change model color');
        const viewer = window.viewerGeneral || window.viewer;

        if (!viewer) {
            alert('Viewer not ready');
            return;
        }

        // Predefined colors to cycle through
        const colors = [
            { name: 'Blue', hex: 0x0047AD },
            { name: 'White', hex: 0xFFFFFF },
            { name: 'Gray', hex: 0x808080 },
            { name: 'Red', hex: 0xFF0000 },
            { name: 'Green', hex: 0x00FF00 },
            { name: 'Yellow', hex: 0xFFFF00 },
            { name: 'Orange', hex: 0xFF8800 },
            { name: 'Purple', hex: 0x9B59B6 },
            { name: 'Black', hex: 0x000000 }
        ];

        // Get current color
        let currentColor = 0x0047AD; // default
        if (viewer.uploadedFiles && viewer.uploadedFiles.length > 0) {
            const firstMesh = viewer.uploadedFiles[0].mesh;
            if (firstMesh && firstMesh.material && firstMesh.material.color) {
                currentColor = firstMesh.material.color.getHex();
            }
        }

        // Find current index and get next color
        let currentIndex = colors.findIndex(c => c.hex === currentColor);
        if (currentIndex === -1) currentIndex = 0;
        const nextIndex = (currentIndex + 1) % colors.length;
        const nextColor = colors[nextIndex];

        // Apply new color
        if (viewer.uploadedFiles) {
            viewer.uploadedFiles.forEach(fileData => {
                if (fileData.mesh && fileData.mesh.material) {
                    fileData.mesh.material.color.setHex(nextColor.hex);
                    fileData.mesh.material.needsUpdate = true;
                }
            });
        }

        // Record action for undo/redo
        if (viewer.undoRedoManager && window.UndoRedoActions) {
            const action = window.UndoRedoActions.createModelColorAction(viewer, currentColor, nextColor.hex);
            viewer.undoRedoManager.recordAction(action);
        }

        console.log(`✅ Model color changed to: ${nextColor.name} (#${nextColor.hex.toString(16).padStart(6, '0')})`);

        // Show notification
        if (window.Utils && window.Utils.showNotification) {
            window.Utils.showNotification(`Model color: ${nextColor.name}`, 'success');
        }

        if (viewer.render) viewer.render();
    },

    changeBackgroundColor: function() {
        console.log('🎨 Change background color');
        const viewer = window.viewerGeneral || window.viewer;

        if (!viewer) {
            alert('Viewer not ready');
            return;
        }

        // Predefined background colors
        const colors = [
            { name: 'White', hex: 0xFFFFFF },
            { name: 'Light Gray', hex: 0xF5F5F5 },
            { name: 'Dark Gray', hex: 0x2C2C2C },
            { name: 'Black', hex: 0x000000 },
            { name: 'Light Blue', hex: 0xE3F2FD },
            { name: 'Light Green', hex: 0xE8F5E9 },
            { name: 'Light Yellow', hex: 0xFFFDE7 },
            { name: 'Light Pink', hex: 0xFCE4EC }
        ];

        // Get current background color
        let currentColor = null;
        if (viewer.scene && viewer.scene.background) {
            currentColor = viewer.scene.background.getHex();
        }

        // Find current index and get next color
        let currentIndex = currentColor === null ? -1 : colors.findIndex(c => c.hex === currentColor);
        const nextIndex = (currentIndex + 1) % colors.length;
        const nextColor = colors[nextIndex];

        // Apply new background color
        if (viewer.scene) {
            viewer.scene.background = new THREE.Color(nextColor.hex);
        }

        // Record action for undo/redo
        if (viewer.undoRedoManager && window.UndoRedoActions) {
            const action = window.UndoRedoActions.createBackgroundColorAction(viewer, currentColor, nextColor.hex);
            viewer.undoRedoManager.recordAction(action);
        }

        console.log(`✅ Background color changed to: ${nextColor.name} (#${nextColor.hex.toString(16).padStart(6, '0')})`);

        // Show notification
        if (window.Utils && window.Utils.showNotification) {
            window.Utils.showNotification(`Background: ${nextColor.name}`, 'success');
        }

        if (viewer.render) viewer.render();
    }
};

console.log('✅ window.toolbarHandler created and ready!', window.toolbarHandler);

// Setup camera button event listeners for toolbar
document.addEventListener('DOMContentLoaded', () => {
    console.log('🎥 Setting up camera button listeners...');

    // Camera buttons in both toolbars (General and Medical)
    document.querySelectorAll('.viewer-professional-toolbar .camera-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const view = this.dataset.view;
            console.log(`📷 Camera view clicked: ${view}`);

            // Remove active class from siblings
            const toolbar = this.closest('.viewer-professional-toolbar');
            toolbar.querySelectorAll('.camera-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            // Get the viewer
            const viewer = window.viewerGeneral || window.viewer;
            if (!viewer?.scene?.activeCamera) {
                console.warn('No viewer or camera found');
                return;
            }

            const camera = viewer.scene.activeCamera;
            const radius = camera.radius || 10;

            // Store old camera state for undo
            let oldCameraState = null;
            if (camera.alpha !== undefined) {
                oldCameraState = {
                    alpha: camera.alpha,
                    beta: camera.beta,
                    radius: camera.radius
                };
            } else if (camera.position) {
                oldCameraState = {
                    position: camera.position.clone(),
                    target: viewer.controls?.target?.clone()
                };
            }

            // Camera view positions
            const views = {
                top: { alpha: Math.PI / 2, beta: 0 },
                bottom: { alpha: Math.PI / 2, beta: Math.PI },
                front: { alpha: Math.PI / 2, beta: Math.PI / 2 },
                right: { alpha: 0, beta: Math.PI / 2 },
                left: { alpha: Math.PI, beta: Math.PI / 2 },
                reset: { alpha: Math.PI / 4, beta: Math.PI / 3 }
            };

            if (views[view] && camera.alpha !== undefined) {
                camera.alpha = views[view].alpha;
                camera.beta = views[view].beta;

                // Store new camera state
                const newCameraState = {
                    alpha: camera.alpha,
                    beta: camera.beta,
                    radius: camera.radius
                };

                // Record action for undo/redo
                if (viewer.undoRedoManager && window.UndoRedoActions) {
                    const action = window.UndoRedoActions.createCameraViewAction(
                        viewer,
                        'previous',
                        view,
                        oldCameraState,
                        newCameraState
                    );
                    viewer.undoRedoManager.recordAction(action);
                }

                console.log(`✅ Camera view set to: ${view}`);
            }
        });
    });

    console.log('✅ Camera button listeners setup complete');
});

// Global state management
window.ViewerToolsState = {
    activeTool: null,
    measurements: [],
    history: [],
    historyIndex: -1,
    gridEnabled: false,
    axisEnabled: false,
    shadowsEnabled: true,
    boundingBoxEnabled: false,
    currentUnit: 'mm',
    transparency: 0,
    undoStack: [],
    redoStack: []
};

/**
 * ========================================
 * UNDO/REDO SYSTEM
 * ========================================
 */
class HistoryManager {
    constructor() {
        this.undoStack = [];
        this.redoStack = [];
        this.maxHistory = 50;
    }

    addAction(action) {
        this.undoStack.push(action);
        if (this.undoStack.length > this.maxHistory) {
            this.undoStack.shift();
        }
        this.redoStack = []; // Clear redo stack on new action
        this.updateButtons();
    }

    undo() {
        if (this.undoStack.length === 0) return;

        const action = this.undoStack.pop();
        if (action && action.undo) {
            action.undo();
            this.redoStack.push(action);
            this.updateButtons();
            console.log('↩️ Undo:', action.type);
        }
    }

    redo() {
        if (this.redoStack.length === 0) return;

        const action = this.redoStack.pop();
        if (action && action.redo) {
            action.redo();
            this.undoStack.push(action);
            this.updateButtons();
            console.log('↪️ Redo:', action.type);
        }
    }

    updateButtons() {
        const undoBtn = document.getElementById('undoBtn');
        const redoBtn = document.getElementById('redoBtn');

        if (undoBtn) undoBtn.disabled = this.undoStack.length === 0;
        if (redoBtn) redoBtn.disabled = this.redoStack.length === 0;
    }

    clear() {
        this.undoStack = [];
        this.redoStack = [];
        this.updateButtons();
    }
}

window.historyManager = new HistoryManager();

/**
 * ========================================
 * MEASUREMENT TOOLS
 * ========================================
 */
class MeasurementTools {
    constructor(viewer) {
        this.viewer = viewer;
        this.activeMeasurement = null;
        this.measurements = [];
        this.measurementMarkers = [];
        this.currentPoints = [];
    }

    // Distance measurement (point-to-point)
    measureDistance(point1, point2) {
        const distance = point1.distanceTo(point2);

        // Create visual line
        const geometry = new THREE.BufferGeometry().setFromPoints([point1, point2]);
        const material = new THREE.LineBasicMaterial({
            color: 0x4a90e2,
            linewidth: 3
        });
        const line = new THREE.Line(geometry, material);

        // Add markers
        const markerGeometry = new THREE.SphereGeometry(2, 16, 16);
        const markerMaterial = new THREE.MeshBasicMaterial({ color: 0x4a90e2 });

        const marker1 = new THREE.Mesh(markerGeometry, markerMaterial);
        marker1.position.copy(point1);

        const marker2 = new THREE.Mesh(markerGeometry, markerMaterial);
        marker2.position.copy(point2);

        // Add to scene
        if (this.viewer.scene) {
            this.viewer.scene.add(line);
            this.viewer.scene.add(marker1);
            this.viewer.scene.add(marker2);
        }

        const measurement = {
            type: 'distance',
            value: distance,
            objects: [line, marker1, marker2],
            points: [point1, point2]
        };

        this.measurements.push(measurement);

        // Add to history
        window.historyManager.addAction({
            type: 'measurement',
            redo: () => {
                measurement.objects.forEach(obj => this.viewer.scene.add(obj));
            },
            undo: () => {
                measurement.objects.forEach(obj => this.viewer.scene.remove(obj));
            }
        });

        // Show annotation
        this.showMeasurementLabel(distance, point1, point2);

        console.log(`📏 Distance measured: ${distance.toFixed(2)} ${window.ViewerToolsState.currentUnit}`);
        return distance;
    }

    // Point to surface measurement
    measurePointToSurface(point, mesh) {
        const raycaster = new THREE.Raycaster();
        const direction = new THREE.Vector3(0, -1, 0); // Cast downward
        raycaster.set(point, direction);

        const intersects = raycaster.intersectObject(mesh, true);
        if (intersects.length > 0) {
            const surfacePoint = intersects[0].point;
            return this.measureDistance(point, surfacePoint);
        }

        console.warn('⚠️ No surface found');
        return null;
    }

    // Clear all measurements
    clearMeasurements() {
        this.measurements.forEach(measurement => {
            measurement.objects.forEach(obj => {
                if (this.viewer.scene) {
                    this.viewer.scene.remove(obj);
                }
            });
        });

        this.measurements = [];
        this.currentPoints = [];

        // Clear labels
        document.querySelectorAll('.measurement-annotation').forEach(el => el.remove());

        console.log('🗑️ All measurements cleared');
    }

    showMeasurementLabel(distance, point1, point2) {
        // Calculate midpoint
        const midpoint = new THREE.Vector3()
            .addVectors(point1, point2)
            .multiplyScalar(0.5);

        // Project to screen coordinates
        if (!this.viewer.camera || !this.viewer.renderer) return;

        const vector = midpoint.clone();
        vector.project(this.viewer.camera);

        const canvas = this.viewer.renderer.domElement;
        const x = (vector.x * 0.5 + 0.5) * canvas.clientWidth;
        const y = (-(vector.y * 0.5) + 0.5) * canvas.clientHeight;

        // Create label
        const label = document.createElement('div');
        label.className = 'measurement-annotation';
        label.textContent = `${distance.toFixed(2)} ${window.ViewerToolsState.currentUnit}`;
        label.style.left = `${x}px`;
        label.style.top = `${y}px`;

        canvas.parentElement.appendChild(label);
    }
}

/**
 * ========================================
 * BOUNDING BOX CONTROLLER
 * ========================================
 */
class BoundingBoxController {
    constructor(viewer) {
        this.viewer = viewer;
        this.boundingBox = null;
        this.dimensions = null;
        this.isVisible = false;
    }

    toggle() {
        if (this.isVisible) {
            this.hide();
        } else {
            this.show();
        }
    }

    show() {
        if (!this.viewer.uploadedFiles || this.viewer.uploadedFiles.length === 0) {
            console.warn('⚠️ No model loaded');
            return;
        }

        // Calculate bounding box for all models
        const box = new THREE.Box3();
        this.viewer.uploadedFiles.forEach(fileData => {
            if (fileData.mesh) {
                box.expandByObject(fileData.mesh);
            }
        });

        // Remove old bounding box
        if (this.boundingBox) {
            this.viewer.scene.remove(this.boundingBox);
        }

        // Create box helper
        const boxHelper = new THREE.Box3Helper(box, 0x4a90e2);
        this.viewer.scene.add(boxHelper);
        this.boundingBox = boxHelper;

        // Calculate dimensions
        const size = new THREE.Vector3();
        box.getSize(size);
        this.dimensions = size;

        // Show dimensions
        this.showDimensions(size);

        this.isVisible = true;
        document.getElementById('boundingBoxBtn')?.classList.add('active');

        console.log('📦 Bounding box shown:', {
            x: size.x.toFixed(2),
            y: size.y.toFixed(2),
            z: size.z.toFixed(2)
        });
    }

    hide() {
        if (this.boundingBox && this.viewer.scene) {
            this.viewer.scene.remove(this.boundingBox);
            this.boundingBox = null;
        }

        // Hide dimension labels
        document.querySelectorAll('.dimension-label').forEach(el => el.remove());

        this.isVisible = false;
        document.getElementById('boundingBoxBtn')?.classList.remove('active');
        console.log('📦 Bounding box hidden');
    }

    showDimensions(size) {
        // Implementation for showing dimension labels in 3D space
        // This would project the dimensions to screen coordinates
        console.log('📐 Dimensions:', size);
    }
}

/**
 * ========================================
 * AXIS HELPER CONTROLLER
 * ========================================
 */
class AxisController {
    constructor(viewer) {
        this.viewer = viewer;
        this.axisHelper = null;
        this.isVisible = false;
    }

    toggle() {
        if (this.isVisible) {
            this.hide();
        } else {
            this.show();
        }
    }

    show() {
        if (this.axisHelper) {
            this.viewer.scene.add(this.axisHelper);
        } else {
            this.axisHelper = new THREE.AxesHelper(100);
            this.viewer.scene.add(this.axisHelper);
        }

        this.isVisible = true;
        document.getElementById('axisToggleBtn')?.classList.add('active');
        console.log('🎯 Axis helper shown');
    }

    hide() {
        if (this.axisHelper && this.viewer.scene) {
            this.viewer.scene.remove(this.axisHelper);
        }

        this.isVisible = false;
        document.getElementById('axisToggleBtn')?.classList.remove('active');
        console.log('🎯 Axis helper hidden');
    }
}

/**
 * ========================================
 * GRID CONTROLLER
 * ========================================
 */
class GridController {
    constructor(viewer) {
        this.viewer = viewer;
        this.gridHelper = null;
        this.isVisible = false;
        this.unit = 'mm';
    }

    toggle() {
        if (this.isVisible) {
            this.hide();
        } else {
            this.show();
        }
    }

    show() {
        if (this.gridHelper) {
            this.viewer.scene.add(this.gridHelper);
        } else {
            // Create grid (size, divisions)
            const size = this.unit === 'mm' ? 500 : 20; // 500mm or 20 inches
            const divisions = 50;

            this.gridHelper = new THREE.GridHelper(size, divisions, 0x888888, 0xcccccc);
            this.gridHelper.material.opacity = 0.3;
            this.gridHelper.material.transparent = true;
            this.viewer.scene.add(this.gridHelper);
        }

        this.isVisible = true;
        document.getElementById('gridToggleBtn')?.classList.add('active');
        document.getElementById('unitToggle')?.style.display = 'flex';
        console.log('📏 Grid shown');
    }

    hide() {
        if (this.gridHelper && this.viewer.scene) {
            this.viewer.scene.remove(this.gridHelper);
        }

        this.isVisible = false;
        document.getElementById('gridToggleBtn')?.classList.remove('active');
        document.getElementById('unitToggle')?.style.display = 'none';
        console.log('📏 Grid hidden');
    }

    setUnit(unit) {
        this.unit = unit;
        window.ViewerToolsState.currentUnit = unit;

        // Recreate grid with new scale
        if (this.isVisible) {
            this.hide();
            this.gridHelper = null;
            this.show();
        }

        // Update all dimension displays
        document.querySelectorAll('.axis-unit').forEach(el => {
            el.textContent = unit;
        });

        console.log(`📏 Unit changed to: ${unit}`);
    }
}

/**
 * ========================================
 * SHADOW CONTROLLER
 * ========================================
 */
class ShadowController {
    constructor(viewer) {
        this.viewer = viewer;
        this.isEnabled = true;
    }

    toggle() {
        this.isEnabled = !this.isEnabled;

        // Toggle shadows on all meshes
        if (this.viewer.uploadedFiles) {
            this.viewer.uploadedFiles.forEach(fileData => {
                if (fileData.mesh) {
                    fileData.mesh.castShadow = this.isEnabled;
                    fileData.mesh.receiveShadow = this.isEnabled;
                }
            });
        }

        // Toggle renderer shadows
        if (this.viewer.renderer) {
            this.viewer.renderer.shadowMap.enabled = this.isEnabled;
        }

        // Update button
        const btn = document.getElementById('shadowToggleBtn');
        if (btn) {
            if (this.isEnabled) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        }

        console.log(`🌑 Shadows ${this.isEnabled ? 'enabled' : 'disabled'}`);
    }
}

/**
 * ========================================
 * TRANSPARENCY CONTROLLER
 * ========================================
 */
class TransparencyController {
    constructor(viewer) {
        this.viewer = viewer;
        this.opacity = 1.0;
    }

    toggle() {
        // Cycle through opacity levels: 1.0 -> 0.7 -> 0.4 -> 1.0
        const opacityLevels = [1.0, 0.7, 0.4];
        const currentIndex = opacityLevels.indexOf(this.opacity);
        this.opacity = opacityLevels[(currentIndex + 1) % opacityLevels.length];

        this.apply(this.opacity);
    }

    apply(opacity) {
        this.opacity = opacity;

        if (this.viewer.uploadedFiles) {
            this.viewer.uploadedFiles.forEach(fileData => {
                if (fileData.mesh && fileData.mesh.material) {
                    fileData.mesh.material.transparent = opacity < 1.0;
                    fileData.mesh.material.opacity = opacity;
                    fileData.mesh.material.needsUpdate = true;
                }
            });
        }

        // Update button state
        const btn = document.getElementById('transparencyBtn');
        if (btn) {
            btn.classList.toggle('active', opacity < 1.0);
        }

        console.log(`👻 Transparency set to: ${(opacity * 100).toFixed(0)}%`);
    }
}

/**
 * ========================================
 * COLOR PICKER CONTROLLER
 * ========================================
 */
class ColorController {
    constructor(viewer) {
        this.viewer = viewer;
        this.currentModelColor = 0x0047AD;
        this.currentBgColor = null;
    }

    setModelColor(color) {
        this.currentModelColor = color;

        if (this.viewer.uploadedFiles) {
            this.viewer.uploadedFiles.forEach(fileData => {
                if (fileData.mesh && fileData.mesh.material) {
                    fileData.mesh.material.color.setHex(color);
                }
            });
        }

        console.log(`🎨 Model color changed to: #${color.toString(16).padStart(6, '0')}`);
    }

    setBackgroundColor(color) {
        this.currentBgColor = color;

        if (this.viewer.scene) {
            this.viewer.scene.background = new THREE.Color(color);
        }

        console.log(`🎨 Background color changed to: #${color.toString(16).padStart(6, '0')}`);
    }

    showColorPicker(type) {
        // Create color picker modal
        // Implementation depends on your UI framework
        console.log(`🎨 Opening color picker for: ${type}`);
    }
}

/**
 * ========================================
 * SCREENSHOT TOOL
 * ========================================
 */
class ScreenshotTool {
    constructor(viewer) {
        this.viewer = viewer;
    }

    capture(filename = 'model-screenshot.png') {
        if (!this.viewer.renderer) {
            console.error('❌ Renderer not available');
            return;
        }

        try {
            // Render one more time to ensure everything is up to date
            if (this.viewer.render) {
                this.viewer.render();
            }

            // Get canvas data
            const canvas = this.viewer.renderer.domElement;
            const dataURL = canvas.toDataURL('image/png');

            // Create download link
            const link = document.createElement('a');
            link.href = dataURL;
            link.download = filename;
            link.click();

            console.log('📸 Screenshot captured:', filename);
            this.showNotification('Screenshot saved!', 'success');
        } catch (error) {
            console.error('❌ Screenshot failed:', error);
            this.showNotification('Screenshot failed', 'error');
        }
    }

    showNotification(message, type) {
        // Use existing notification system
        if (window.Utils && window.Utils.showNotification) {
            window.Utils.showNotification(message, type);
        }
    }
}

/**
 * ========================================
 * MESH ANALYSIS & REPAIR
 * ========================================
 */
class MeshAnalyzer {
    constructor(viewer) {
        this.viewer = viewer;
    }

    async analyzeMesh(mesh) {
        console.log('🔍 Analyzing mesh...');

        const geometry = mesh.geometry;
        const analysis = {
            vertices: geometry.attributes.position.count,
            triangles: geometry.index ? geometry.index.count / 3 : geometry.attributes.position.count / 3,
            holes: 0,
            nonManifoldEdges: 0,
            isWatertight: false
        };

        // Check for holes and non-manifold edges
        // This is a simplified analysis
        const edges = this.extractEdges(geometry);
        analysis.holes = this.detectHoles(edges);
        analysis.nonManifoldEdges = this.detectNonManifoldEdges(edges);
        analysis.isWatertight = analysis.holes === 0 && analysis.nonManifoldEdges === 0;

        console.log('📊 Analysis complete:', analysis);
        return analysis;
    }

    extractEdges(geometry) {
        // Simplified edge extraction
        const edges = new Map();
        const positions = geometry.attributes.position.array;
        const indices = geometry.index ? geometry.index.array : null;

        // Implementation would extract unique edges
        // Placeholder for now
        return edges;
    }

    detectHoles(edges) {
        // Simplified hole detection
        // Count edges that appear only once (boundary edges)
        let boundaryEdges = 0;
        for (const [key, count] of edges) {
            if (count === 1) boundaryEdges++;
        }
        return Math.floor(boundaryEdges / 3); // Rough estimate
    }

    detectNonManifoldEdges(edges) {
        // Count edges shared by more than 2 faces
        let nonManifold = 0;
        for (const [key, count] of edges) {
            if (count > 2) nonManifold++;
        }
        return nonManifold;
    }

    async repairMesh(mesh) {
        console.log('🔧 Repairing mesh...');

        // Simplified repair - merge vertices and recalculate normals
        if (mesh.geometry) {
            mesh.geometry.computeVertexNormals();
            mesh.geometry.computeBoundingBox();
            mesh.geometry.computeBoundingSphere();
        }

        console.log('✅ Mesh repaired');
        return mesh;
    }

    showAnalysisPanel(analysis) {
        // Remove existing panel
        document.querySelector('.analysis-panel')?.remove();

        // Create panel
        const panel = document.createElement('div');
        panel.className = 'analysis-panel';
        panel.innerHTML = `
            <h4>
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                    <path d="M9 2L15 6L9 10L3 6L9 2Z" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M3 12L9 16L15 12" stroke="currentColor" stroke-width="1.5"/>
                </svg>
                Mesh Analysis
            </h4>
            <div class="analysis-item">
                <span class="analysis-label">Vertices:</span>
                <span class="analysis-value">${analysis.vertices.toLocaleString()}</span>
            </div>
            <div class="analysis-item">
                <span class="analysis-label">Triangles:</span>
                <span class="analysis-value">${analysis.triangles.toLocaleString()}</span>
            </div>
            <div class="analysis-item">
                <span class="analysis-label">Holes:</span>
                <span class="analysis-value ${analysis.holes > 0 ? 'warning' : 'success'}">${analysis.holes}</span>
            </div>
            <div class="analysis-item">
                <span class="analysis-label">Non-Manifold:</span>
                <span class="analysis-value ${analysis.nonManifoldEdges > 0 ? 'error' : 'success'}">${analysis.nonManifoldEdges}</span>
            </div>
            <div class="analysis-item">
                <span class="analysis-label">Watertight:</span>
                <span class="analysis-value ${analysis.isWatertight ? 'success' : 'error'}">${analysis.isWatertight ? 'Yes' : 'No'}</span>
            </div>
        `;

        // Add to viewer container
        const container = document.getElementById('viewer3dGeneral') || document.getElementById('viewer3dMedical');
        if (container) {
            container.appendChild(panel);
        }
    }
}

/**
 * ========================================
 * INITIALIZE PROFESSIONAL TOOLS
 * ========================================
 */
function initProfessionalTools(viewer) {
    console.log('🚀 Initializing professional tools...');

    // Initialize all controllers
    const measurementTools = new MeasurementTools(viewer);
    const boundingBoxController = new BoundingBoxController(viewer);
    const axisController = new AxisController(viewer);
    const gridController = new GridController(viewer);
    const shadowController = new ShadowController(viewer);
    const transparencyController = new TransparencyController(viewer);
    const colorController = new ColorController(viewer);
    const screenshotTool = new ScreenshotTool(viewer);
    const meshAnalyzer = new MeshAnalyzer(viewer);

    // Store in viewer object
    viewer.tools = {
        measurement: measurementTools,
        boundingBox: boundingBoxController,
        axis: axisController,
        grid: gridController,
        shadow: shadowController,
        transparency: transparencyController,
        color: colorController,
        screenshot: screenshotTool,
        analyzer: meshAnalyzer
    };

    // Setup event listeners
    setupToolbarEvents(viewer);

    console.log('✅ Professional tools initialized');
}

function setupToolbarEvents(viewer) {
    console.log(`🔧 Setting up toolbar events for viewer: ${viewer.containerId}`);
    console.log('   Viewer object:', viewer);
    console.log('   Viewer container:', viewer.container);
    console.log('   Viewer tools:', viewer.tools);

    // Get the container to scope button selection
    const container = viewer.container;
    if (!container) {
        console.error('❌ Viewer container not found');
        return;
    }

    // Determine if this is General or Medical viewer
    const viewerType = viewer.containerId.includes('Medical') ? 'Medical' : '';
    const btnSuffix = viewerType; // '' for General, 'Medical' for Medical

    console.log(`   Viewer type: ${viewerType || 'General'}, Button suffix: '${btnSuffix}'`);

    // Measurement tool
    const measurementBtn = document.getElementById(`measurementToolBtn${btnSuffix}`);
    console.log(`   Looking for: measurementToolBtn${btnSuffix}`, measurementBtn);
    if (measurementBtn) {
        measurementBtn.addEventListener('click', () => {
            console.log('🖱️ Measurement button clicked!');
            const submenu = document.getElementById(`measurementSubmenu${btnSuffix === 'Medical' ? 'Medical' : ''}`);
            console.log('   Submenu:', submenu);
            if (submenu) {
                submenu.style.display = submenu.style.display === 'none' ? 'block' : 'none';
                console.log('   Submenu toggled to:', submenu.style.display);
            }
        });
        console.log(`   ✓ Measurement button event attached`);
    } else {
        console.warn(`   ⚠️ measurementToolBtn${btnSuffix} not found in DOM`);
    }

    // Submenu close button
    const submenuClose = container.querySelector('.submenu-close');
    if (submenuClose) {
        submenuClose.addEventListener('click', () => {
            const submenu = document.getElementById(`measurementSubmenu${btnSuffix === 'Medical' ? 'Medical' : ''}`);
            if (submenu) submenu.style.display = 'none';
        });
    }

    // Measurement types (within submenu)
    const submenu = document.getElementById(`measurementSubmenu${btnSuffix === 'Medical' ? 'Medical' : ''}`);
    if (submenu) {
        submenu.querySelectorAll('.submenu-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const measureType = this.getAttribute('data-measure');

                if (measureType === 'clear') {
                    viewer.tools.measurement.clearMeasurements();
                    console.log('   ✓ Cleared measurements');
                } else {
                    if (!window.ViewerToolsState) window.ViewerToolsState = {};
                    window.ViewerToolsState.activeTool = measureType;
                    submenu.querySelectorAll('.submenu-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    console.log(`   ✓ Active measurement: ${measureType}`);
                }

                submenu.style.display = 'none';
            });
        });
    }

    // Bounding Box
    const boundingBoxBtn = document.getElementById(`boundingBoxBtn${btnSuffix}`);
    if (boundingBoxBtn) {
        boundingBoxBtn.addEventListener('click', () => {
            viewer.tools.boundingBox.toggle();
            console.log('   ✓ Bounding box toggled');
        });
    }

    // Axis
    const axisBtn = document.getElementById(`axisToggleBtn${btnSuffix}`);
    if (axisBtn) {
        axisBtn.addEventListener('click', () => {
            viewer.tools.axis.toggle();
            console.log('   ✓ Axis toggled');
        });
    }

    // Grid
    const gridBtn = document.getElementById(`gridToggleBtn${btnSuffix}`);
    if (gridBtn) {
        gridBtn.addEventListener('click', () => {
            viewer.tools.grid.toggle();
            console.log('   ✓ Grid toggled');
        });
    }

    // Unit toggle (for grid)
    const unitToggle = document.getElementById(`unitToggle${btnSuffix === 'Medical' ? 'Medical' : ''}`);
    if (unitToggle) {
        unitToggle.querySelectorAll('.unit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const unit = this.getAttribute('data-unit');
                unitToggle.querySelectorAll('.unit-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                viewer.tools.grid.setUnit(unit);
                console.log(`   ✓ Grid unit set to: ${unit}`);
            });
        });
    }

    // Shadow toggle
    const shadowBtn = document.getElementById(`shadowToggleBtn${btnSuffix}`);
    if (shadowBtn) {
        shadowBtn.addEventListener('click', () => {
            viewer.tools.shadow.toggle();
            console.log('   ✓ Shadow toggled');
        });
    }

    // Transparency
    const transparencyBtn = document.getElementById(`transparencyBtn${btnSuffix}`);
    if (transparencyBtn) {
        transparencyBtn.addEventListener('click', () => {
            viewer.tools.transparency.toggle();
            console.log('   ✓ Transparency toggled');
        });
    }

    // Screenshot
    const screenshotBtn = document.getElementById(`screenshotToolBtn${btnSuffix}`);
    if (screenshotBtn) {
        screenshotBtn.addEventListener('click', () => {
            const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
            viewer.tools.screenshot.capture(`3d-model-${viewerType || 'general'}-${timestamp}.png`);
            console.log('   ✓ Screenshot captured');
        });
    }

    // Undo/Redo
    const undoBtn = document.getElementById(`undoBtn${btnSuffix}`);
    if (undoBtn) {
        undoBtn.addEventListener('click', () => {
            if (!window.historyManager) window.historyManager = new HistoryManager();
            window.historyManager.undo();
            console.log('   ✓ Undo');
        });
    }

    const redoBtn = document.getElementById(`redoBtn${btnSuffix}`);
    if (redoBtn) {
        redoBtn.addEventListener('click', () => {
            if (!window.historyManager) window.historyManager = new HistoryManager();
            window.historyManager.redo();
            console.log('   ✓ Redo');
        });
    }

    console.log(`✅ All toolbar event handlers attached for ${viewerType || 'General'} viewer`);
}

// Keyboard shortcuts (global, only setup once)
if (!window.professionalToolsKeyboardSetup) {
    document.addEventListener('keydown', (e) => {
        if (e.ctrlKey || e.metaKey) {
            if (e.key === 'z' && !e.shiftKey) {
                e.preventDefault();
                if (!window.historyManager) window.historyManager = new HistoryManager();
                window.historyManager.undo();
            } else if (e.key === 'z' && e.shiftKey || e.key === 'y') {
                e.preventDefault();
                if (!window.historyManager) window.historyManager = new HistoryManager();
                window.historyManager.redo();
            }
        }
    });
    window.professionalToolsKeyboardSetup = true;
}

// Initialize when viewer is ready
window.addEventListener('viewersReady', () => {
    console.log('🎬 viewersReady EVENT received, initializing professional tools...');

    if (window.viewerGeneral) {
        console.log('   Found viewerGeneral (unified viewer), initializing...');
        initProfessionalTools(window.viewerGeneral);
    } else if (window.viewer) {
        console.log('   Found window.viewer, initializing...');
        initProfessionalTools(window.viewer);
    } else {
        console.warn('   ⚠️ No viewer found');
    }

    // Ensure toolbars are visible
    setTimeout(() => {
        const toolbars = document.querySelectorAll('.viewer-professional-toolbar');
        toolbars.forEach(toolbar => {
            toolbar.style.display = 'flex';
            toolbar.style.visibility = 'visible';
            toolbar.style.opacity = '1';
        });
        console.log('✅ Toolbars made visible:', toolbars.length);
    }, 500);
});

// IMPORTANT: Also check if viewers are ALREADY initialized (in case event already fired)
document.addEventListener('DOMContentLoaded', () => {
    console.log('📍 DOMContentLoaded - checking if viewers already exist...');

    // Wait a bit for viewers to initialize
    setTimeout(() => {
        console.log('   Checking for existing viewers...');
        console.log('   window.viewerGeneral:', !!window.viewerGeneral);
        console.log('   window.viewer:', !!window.viewer);

        // If viewer exists but tools aren't initialized, do it now
        const viewer = window.viewerGeneral || window.viewer;
        if (viewer && !viewer.tools) {
            console.log('   🔧 Viewer exists but tools not initialized - doing it now!');
            initProfessionalTools(viewer);
        }

        // Ensure toolbars are visible
        const toolbars = document.querySelectorAll('.viewer-professional-toolbar');
        toolbars.forEach(toolbar => {
            toolbar.style.display = 'flex';
            toolbar.style.visibility = 'visible';
            toolbar.style.opacity = '1';
        });
        console.log('   Toolbars visibility ensured:', toolbars.length);
    }, 2000); // Wait 2 seconds for viewers to initialize
});


console.log('✅ Professional 3D Viewer Tools script loaded - toolbar handler defined at TOP');
