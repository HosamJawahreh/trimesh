/**
 * ========================================
 * PROFESSIONAL UNDO/REDO SYSTEM
 * Complete history management for 3D viewer
 * ========================================
 */

console.log('🔄 Loading Professional Undo/Redo Manager...');

/**
 * UndoRedoManager - Comprehensive undo/redo system for 3D viewer
 * Tracks all actions: mesh operations, visual changes, transformations, etc.
 */
class UndoRedoManager {
    constructor(viewer) {
        this.viewer = viewer;
        this.undoStack = [];
        this.redoStack = [];
        this.maxStackSize = 50; // Maximum number of actions to remember
        this.isExecuting = false; // Prevent recursive action tracking
        this.storageKey = 'trimesh_undo_redo_history';
        this.viewerStateKey = 'trimesh_viewer_state';

        console.log('✅ UndoRedoManager initialized');
        this.loadFromStorage(); // Load saved history
        this.setupKeyboardShortcuts();
        this.updateUIState();
    }

    /**
     * Setup keyboard shortcuts for undo/redo
     */
    setupKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Ctrl+Z or Cmd+Z for undo
            if ((e.ctrlKey || e.metaKey) && e.key === 'z' && !e.shiftKey) {
                e.preventDefault();
                this.undo();
            }
            // Ctrl+Shift+Z or Ctrl+Y or Cmd+Shift+Z for redo
            else if (((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'z') ||
                     (e.ctrlKey && e.key === 'y')) {
                e.preventDefault();
                this.redo();
            }
        });
        console.log('⌨️ Keyboard shortcuts enabled: Ctrl+Z (undo), Ctrl+Shift+Z/Ctrl+Y (redo)');
    }

    /**
     * Record an action to the undo stack
     * @param {Object} action - Action object with type, data, undo, and redo functions
     */
    recordAction(action) {
        if (this.isExecuting) {
            return; // Prevent recording actions during undo/redo execution
        }

        // Validate action structure
        if (!action.type || !action.undo || !action.redo) {
            console.error('❌ Invalid action structure:', action);
            return;
        }

        // Add timestamp
        action.timestamp = Date.now();
        action.id = `action_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;

        // Add to undo stack
        this.undoStack.push(action);

        // Limit stack size
        if (this.undoStack.length > this.maxStackSize) {
            this.undoStack.shift();
        }

        // Clear redo stack when new action is recorded
        this.redoStack = [];

        console.log(`📝 Action recorded: ${action.type}`, action);
        this.saveToStorage(); // Persist to localStorage
        this.updateUIState();
    }

    /**
     * Save history to localStorage
     */
    saveToStorage() {
        try {
            // We can't serialize functions, so we save metadata only
            const historyData = {
                undoStack: this.undoStack.map(action => ({
                    type: action.type,
                    timestamp: action.timestamp,
                    id: action.id,
                    data: action.data
                })),
                redoStack: this.redoStack.map(action => ({
                    type: action.type,
                    timestamp: action.timestamp,
                    id: action.id,
                    data: action.data
                })),
                savedAt: Date.now()
            };

            localStorage.setItem(this.storageKey, JSON.stringify(historyData));
            console.log('💾 History saved to localStorage');
        } catch (error) {
            console.warn('⚠️ Failed to save history to localStorage:', error);
        }
    }

    /**
     * Load history from localStorage and restore viewer state
     */
    loadFromStorage() {
        try {
            const stored = localStorage.getItem(this.storageKey);
            if (!stored) {
                console.log('📂 No saved history found');
                return;
            }

            const historyData = JSON.parse(stored);
            const ageMinutes = (Date.now() - historyData.savedAt) / 1000 / 60;

            // Only load if less than 24 hours old
            if (ageMinutes > 1440) {
                console.log('⏰ Saved history too old, clearing...');
                localStorage.removeItem(this.storageKey);
                return;
            }

            console.log(`📂 Loaded history from localStorage (${Math.round(ageMinutes)} minutes old)`);
            console.log(`   Undo actions: ${historyData.undoStack.length}`);
            console.log(`   Redo actions: ${historyData.redoStack.length}`);

            // We'll reconstruct actions when needed
            // For now, just log what was saved
            this.savedHistoryData = historyData;
        } catch (error) {
            console.warn('⚠️ Failed to load history from localStorage:', error);
        }
    }

    /**
     * Clear storage
     */
    clearStorage() {
        try {
            localStorage.removeItem(this.storageKey);
            console.log('🗑️ History cleared from localStorage');
        } catch (error) {
            console.warn('⚠️ Failed to clear storage:', error);
        }
    }

    /**
     * Undo the last action
     */
    async undo() {
        if (this.undoStack.length === 0) {
            console.log('⚠️ Nothing to undo');
            this.showNotification('Nothing to undo', 'info');
            return;
        }

        this.isExecuting = true;

        try {
            const action = this.undoStack.pop();
            console.log(`⬅️ Undoing: ${action.type}`, action);

            // Execute undo function
            await action.undo();

            // Move to redo stack
            this.redoStack.push(action);

            // Limit redo stack size
            if (this.redoStack.length > this.maxStackSize) {
                this.redoStack.shift();
            }

            this.showNotification(`Undone: ${action.type}`, 'success');
            console.log('✅ Undo successful');
        } catch (error) {
            console.error('❌ Undo failed:', error);
            this.showNotification('Undo failed', 'error');
        } finally {
            this.isExecuting = false;
            this.saveToStorage(); // Save after undo
            this.updateUIState();
            this.refreshViewer();
        }
    }

    /**
     * Redo the last undone action
     */
    async redo() {
        if (this.redoStack.length === 0) {
            console.log('⚠️ Nothing to redo');
            this.showNotification('Nothing to redo', 'info');
            return;
        }

        this.isExecuting = true;

        try {
            const action = this.redoStack.pop();
            console.log(`➡️ Redoing: ${action.type}`, action);

            // Execute redo function
            await action.redo();

            // Move back to undo stack
            this.undoStack.push(action);

            this.showNotification(`Redone: ${action.type}`, 'success');
            console.log('✅ Redo successful');
        } catch (error) {
            console.error('❌ Redo failed:', error);
            this.showNotification('Redo failed', 'error');
        } finally {
            this.isExecuting = false;
            this.saveToStorage(); // Save after redo
            this.updateUIState();
            this.refreshViewer();
        }
    }

    /**
     * Clear all history
     */
    clear() {
        this.undoStack = [];
        this.redoStack = [];
        this.clearStorage();
        this.updateUIState();
        console.log('🗑️ Undo/Redo history cleared');
    }

    /**
     * Get current state info
     */
    getState() {
        return {
            canUndo: this.undoStack.length > 0,
            canRedo: this.redoStack.length > 0,
            undoCount: this.undoStack.length,
            redoCount: this.redoStack.length,
            lastAction: this.undoStack[this.undoStack.length - 1]?.type || null
        };
    }

    /**
     * Update UI buttons state
     */
    updateUIState() {
        const state = this.getState();

        // Update undo button
        const undoBtn = document.getElementById('undoBtn');
        if (undoBtn) {
            // Don't disable, just style differently
            undoBtn.classList.toggle('disabled', !state.canUndo);
            undoBtn.title = state.canUndo ? `Undo: ${state.lastAction} (Ctrl+Z)` : 'Nothing to undo (Ctrl+Z)';
            undoBtn.style.opacity = state.canUndo ? '1' : '0.5';
            undoBtn.style.cursor = state.canUndo ? 'pointer' : 'not-allowed';
        }

        // Update redo button
        const redoBtn = document.getElementById('redoBtn');
        if (redoBtn) {
            // Don't disable, just style differently
            redoBtn.classList.toggle('disabled', !state.canRedo);
            redoBtn.title = state.canRedo ? `Redo (Ctrl+Shift+Z)` : 'Nothing to redo (Ctrl+Shift+Z)';
            redoBtn.style.opacity = state.canRedo ? '1' : '0.5';
            redoBtn.style.cursor = state.canRedo ? 'pointer' : 'not-allowed';
        }

        // Update counter if exists
        const counter = document.getElementById('undoRedoCounter');
        if (counter) {
            counter.textContent = `${state.undoCount}/${state.redoCount}`;
        }
    }

    /**
     * Refresh the viewer
     */
    refreshViewer() {
        if (this.viewer && this.viewer.renderer && this.viewer.scene && this.viewer.camera) {
            this.viewer.renderer.render(this.viewer.scene, this.viewer.camera);
        }
    }

    /**
     * Show notification
     */
    showNotification(message, type = 'info') {
        if (window.Utils && window.Utils.showNotification) {
            window.Utils.showNotification(message, type);
        } else {
            // Fallback notification
            const notification = document.createElement('div');
            const colors = {
                success: '#10b981',
                error: '#ef4444',
                info: '#3b82f6',
                warning: '#f59e0b'
            };
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${colors[type] || colors.info};
                color: white;
                padding: 12px 20px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.2);
                z-index: 999999;
                font-family: system-ui;
                font-size: 14px;
                font-weight: 500;
                animation: slideIn 0.3s ease-out;
            `;
            notification.textContent = message;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease-in';
                setTimeout(() => document.body.removeChild(notification), 300);
            }, 2000);
        }
    }
}

/**
 * Action Creators - Factory functions for creating trackable actions
 */
window.UndoRedoActions = {
    /**
     * Create action for toggling bounding box
     */
    createBoundingBoxAction(viewer, isVisible) {
        return {
            type: 'Toggle Bounding Box',
            data: { isVisible },
            undo: () => {
                const helper = viewer.scene.children.find(child =>
                    child.userData && child.userData.isBoundingBoxHelper
                );
                if (helper) {
                    helper.visible = !isVisible;
                }
            },
            redo: () => {
                const helper = viewer.scene.children.find(child =>
                    child.userData && child.userData.isBoundingBoxHelper
                );
                if (helper) {
                    helper.visible = isVisible;
                }
            }
        };
    },

    /**
     * Create action for toggling axis helper
     */
    createAxisAction(viewer, isVisible) {
        return {
            type: 'Toggle Axis Helper',
            data: { isVisible },
            undo: () => {
                const helper = viewer.scene.children.find(child =>
                    child.userData && child.userData.isAxisHelper
                );
                if (helper) {
                    helper.visible = !isVisible;
                }
            },
            redo: () => {
                const helper = viewer.scene.children.find(child =>
                    child.userData && child.userData.isAxisHelper
                );
                if (helper) {
                    helper.visible = isVisible;
                }
            }
        };
    },

    /**
     * Create action for toggling grid helper
     */
    createGridAction(viewer, isVisible) {
        return {
            type: 'Toggle Grid Helper',
            data: { isVisible },
            undo: () => {
                const helper = viewer.scene.children.find(child =>
                    child.userData && child.userData.isGridHelper
                );
                if (helper) {
                    helper.visible = !isVisible;
                }
            },
            redo: () => {
                const helper = viewer.scene.children.find(child =>
                    child.userData && child.userData.isGridHelper
                );
                if (helper) {
                    helper.visible = isVisible;
                }
            }
        };
    },

    /**
     * Create action for opacity change
     */
    createOpacityAction(viewer, oldOpacity, newOpacity) {
        return {
            type: 'Change Opacity',
            data: { oldOpacity, newOpacity },
            undo: () => {
                viewer.scene.traverse((object) => {
                    if (object.isMesh && object.material) {
                        object.material.transparent = oldOpacity < 1.0;
                        object.material.opacity = oldOpacity;
                        object.material.needsUpdate = true;
                    }
                });
            },
            redo: () => {
                viewer.scene.traverse((object) => {
                    if (object.isMesh && object.material) {
                        object.material.transparent = newOpacity < 1.0;
                        object.material.opacity = newOpacity;
                        object.material.needsUpdate = true;
                    }
                });
            }
        };
    },

    /**
     * Create action for camera position change
     */
    createCameraAction(viewer, oldPosition, oldTarget, newPosition, newTarget) {
        return {
            type: 'Camera Movement',
            data: { oldPosition, oldTarget, newPosition, newTarget },
            undo: () => {
                viewer.camera.position.copy(oldPosition);
                if (viewer.controls && viewer.controls.target) {
                    viewer.controls.target.copy(oldTarget);
                    viewer.controls.update();
                }
            },
            redo: () => {
                viewer.camera.position.copy(newPosition);
                if (viewer.controls && viewer.controls.target) {
                    viewer.controls.target.copy(newTarget);
                    viewer.controls.update();
                }
            }
        };
    },

    /**
     * Create action for mesh color change
     */
    createColorAction(viewer, mesh, oldColor, newColor) {
        return {
            type: 'Change Color',
            data: { meshId: mesh.uuid, oldColor, newColor },
            undo: () => {
                if (mesh && mesh.material) {
                    mesh.material.color.setHex(oldColor);
                    mesh.material.needsUpdate = true;
                }
            },
            redo: () => {
                if (mesh && mesh.material) {
                    mesh.material.color.setHex(newColor);
                    mesh.material.needsUpdate = true;
                }
            }
        };
    },

    /**
     * Create action for mesh deletion
     */
    createDeleteMeshAction(viewer, mesh, parent) {
        return {
            type: 'Delete Mesh',
            data: { meshId: mesh.uuid, fileName: mesh.userData?.fileName },
            undo: () => {
                if (parent) {
                    parent.add(mesh);
                } else if (viewer.scene) {
                    viewer.scene.add(mesh);
                }
            },
            redo: () => {
                if (mesh.parent) {
                    mesh.parent.remove(mesh);
                }
            }
        };
    },

    /**
     * Create action for mesh addition
     */
    createAddMeshAction(viewer, mesh, parent) {
        return {
            type: 'Add Mesh',
            data: { meshId: mesh.uuid, fileName: mesh.userData?.fileName },
            undo: () => {
                if (mesh.parent) {
                    mesh.parent.remove(mesh);
                }
            },
            redo: () => {
                if (parent) {
                    parent.add(mesh);
                } else if (viewer.scene) {
                    viewer.scene.add(mesh);
                }
            }
        };
    },

    /**
     * Create action for mesh transformation (position, rotation, scale)
     */
    createTransformAction(viewer, mesh, oldTransform, newTransform) {
        return {
            type: 'Transform Mesh',
            data: {
                meshId: mesh.uuid,
                oldTransform: { ...oldTransform },
                newTransform: { ...newTransform }
            },
            undo: () => {
                if (mesh) {
                    mesh.position.copy(oldTransform.position);
                    mesh.rotation.copy(oldTransform.rotation);
                    mesh.scale.copy(oldTransform.scale);
                }
            },
            redo: () => {
                if (mesh) {
                    mesh.position.copy(newTransform.position);
                    mesh.rotation.copy(newTransform.rotation);
                    mesh.scale.copy(newTransform.scale);
                }
            }
        };
    },

    /**
     * Create action for wireframe toggle
     */
    createWireframeAction(viewer, mesh, oldWireframe, newWireframe) {
        return {
            type: 'Toggle Wireframe',
            data: { meshId: mesh.uuid, oldWireframe, newWireframe },
            undo: () => {
                if (mesh && mesh.material) {
                    mesh.material.wireframe = oldWireframe;
                    mesh.material.needsUpdate = true;
                }
            },
            redo: () => {
                if (mesh && mesh.material) {
                    mesh.material.wireframe = newWireframe;
                    mesh.material.needsUpdate = true;
                }
            }
        };
    },

    /**
     * Create action for auto-rotate toggle
     */
    createAutoRotateAction(viewer, oldState, newState) {
        return {
            type: 'Toggle Auto-Rotate',
            data: { oldState, newState },
            undo: () => {
                if (viewer.controls) {
                    viewer.controls.autoRotate = oldState;
                }
            },
            redo: () => {
                if (viewer.controls) {
                    viewer.controls.autoRotate = newState;
                }
            }
        };
    },

    /**
     * Create action for mesh repair
     */
    createMeshRepairAction(viewer, originalMesh, repairedMesh, fileData) {
        return {
            type: 'Mesh Repair',
            data: {
                fileName: fileData.file?.name,
                originalVolume: fileData.originalVolume,
                repairedVolume: fileData.volume?.cm3
            },
            undo: () => {
                if (originalMesh && repairedMesh.parent) {
                    const parent = repairedMesh.parent;
                    parent.remove(repairedMesh);
                    parent.add(originalMesh);
                    fileData.mesh = originalMesh;
                }
            },
            redo: () => {
                if (repairedMesh && originalMesh.parent) {
                    const parent = originalMesh.parent;
                    parent.remove(originalMesh);
                    parent.add(repairedMesh);
                    fileData.mesh = repairedMesh;
                }
            }
        };
    },

    /**
     * Create action for model color change
     */
    createModelColorAction(viewer, oldColor, newColor) {
        return {
            type: 'Change Model Color',
            data: { oldColor, newColor },
            undo: () => {
                // Restore old color to all meshes
                if (viewer.uploadedFiles) {
                    viewer.uploadedFiles.forEach(fileData => {
                        if (fileData.mesh && fileData.mesh.material) {
                            fileData.mesh.material.color.setHex(oldColor);
                            fileData.mesh.material.needsUpdate = true;
                        }
                    });
                }
                // Update current color if tools exist
                if (viewer.tools && viewer.tools.colorTool) {
                    viewer.tools.colorTool.currentModelColor = oldColor;
                }
            },
            redo: () => {
                // Apply new color to all meshes
                if (viewer.uploadedFiles) {
                    viewer.uploadedFiles.forEach(fileData => {
                        if (fileData.mesh && fileData.mesh.material) {
                            fileData.mesh.material.color.setHex(newColor);
                            fileData.mesh.material.needsUpdate = true;
                        }
                    });
                }
                // Update current color if tools exist
                if (viewer.tools && viewer.tools.colorTool) {
                    viewer.tools.colorTool.currentModelColor = newColor;
                }
            }
        };
    },

    /**
     * Create action for background color change
     */
    createBackgroundColorAction(viewer, oldColor, newColor) {
        return {
            type: 'Change Background Color',
            data: { oldColor, newColor },
            undo: () => {
                if (viewer.scene) {
                    if (oldColor === null) {
                        viewer.scene.background = null;
                    } else {
                        viewer.scene.background = new THREE.Color(oldColor);
                    }
                }
                // Update current color if tools exist
                if (viewer.tools && viewer.tools.colorTool) {
                    viewer.tools.colorTool.currentBgColor = oldColor;
                }
            },
            redo: () => {
                if (viewer.scene) {
                    if (newColor === null) {
                        viewer.scene.background = null;
                    } else {
                        viewer.scene.background = new THREE.Color(newColor);
                    }
                }
                // Update current color if tools exist
                if (viewer.tools && viewer.tools.colorTool) {
                    viewer.tools.colorTool.currentBgColor = newColor;
                }
            }
        };
    },

    /**
     * Create action for shadow toggle
     */
    createShadowAction(viewer, oldState, newState) {
        return {
            type: 'Toggle Shadows',
            data: { oldState, newState },
            undo: () => {
                if (viewer.renderer && viewer.renderer.shadowMap) {
                    viewer.renderer.shadowMap.enabled = oldState;
                }
            },
            redo: () => {
                if (viewer.renderer && viewer.renderer.shadowMap) {
                    viewer.renderer.shadowMap.enabled = newState;
                }
            }
        };
    },

    /**
     * Create action for camera view change
     */
    createCameraViewAction(viewer, oldView, newView, oldCameraState, newCameraState) {
        return {
            type: `Camera View: ${newView}`,
            data: { oldView, newView, oldCameraState, newCameraState },
            undo: () => {
                const camera = viewer.scene?.activeCamera || viewer.camera;
                if (camera && oldCameraState) {
                    if (camera.alpha !== undefined) {
                        // ArcRotateCamera
                        camera.alpha = oldCameraState.alpha;
                        camera.beta = oldCameraState.beta;
                        camera.radius = oldCameraState.radius;
                    } else {
                        // PerspectiveCamera
                        camera.position.copy(oldCameraState.position);
                        if (viewer.controls && oldCameraState.target) {
                            viewer.controls.target.copy(oldCameraState.target);
                            viewer.controls.update();
                        }
                    }
                }
            },
            redo: () => {
                const camera = viewer.scene?.activeCamera || viewer.camera;
                if (camera && newCameraState) {
                    if (camera.alpha !== undefined) {
                        // ArcRotateCamera
                        camera.alpha = newCameraState.alpha;
                        camera.beta = newCameraState.beta;
                        camera.radius = newCameraState.radius;
                    } else {
                        // PerspectiveCamera
                        camera.position.copy(newCameraState.position);
                        if (viewer.controls && newCameraState.target) {
                            viewer.controls.target.copy(newCameraState.target);
                            viewer.controls.update();
                        }
                    }
                }
            }
        };
    }
};

/**
 * Initialize undo/redo manager for a viewer
 */
window.initUndoRedoManager = function(viewer) {
    if (!viewer) {
        console.error('❌ Cannot initialize undo/redo: viewer is null');
        return null;
    }

    if (viewer.undoRedoManager) {
        console.log('⚠️ Undo/Redo manager already initialized');
        return viewer.undoRedoManager;
    }

    viewer.undoRedoManager = new UndoRedoManager(viewer);
    console.log('✅ Undo/Redo manager initialized for viewer');

    return viewer.undoRedoManager;
};

// Auto-initialize for main viewers when they're ready
document.addEventListener('DOMContentLoaded', () => {
    console.log('🔄 Waiting for viewers to initialize undo/redo...');

    const checkAndInit = () => {
        let initialized = false;

        if (window.viewerGeneral && !window.viewerGeneral.undoRedoManager) {
            window.initUndoRedoManager(window.viewerGeneral);
            console.log('✅ Undo/Redo initialized for general viewer');
            initialized = true;
        }
        if (window.viewerMedical && !window.viewerMedical.undoRedoManager) {
            window.initUndoRedoManager(window.viewerMedical);
            console.log('✅ Undo/Redo initialized for medical viewer');
            initialized = true;
        }

        // Log status
        if (initialized) {
            console.log('🎉 Undo/Redo system ready! Try Ctrl+Z after making changes.');
        }

        // Ensure buttons are visible and styled correctly
        setTimeout(() => {
            const undoBtn = document.getElementById('undoBtn');
            const redoBtn = document.getElementById('redoBtn');

            if (undoBtn) {
                undoBtn.style.opacity = '0.5';
                undoBtn.style.cursor = 'not-allowed';
                console.log('✅ Undo button found and styled');
            } else {
                console.warn('⚠️ Undo button not found in DOM');
            }

            if (redoBtn) {
                redoBtn.style.opacity = '0.5';
                redoBtn.style.cursor = 'not-allowed';
                console.log('✅ Redo button found and styled');
            } else {
                console.warn('⚠️ Redo button not found in DOM');
            }
        }, 500);
    };

    // Check immediately
    setTimeout(checkAndInit, 1000);

    // Also check when viewers are ready
    window.addEventListener('viewersReady', checkAndInit);

    // Check periodically for the first 10 seconds
    let attempts = 0;
    const interval = setInterval(() => {
        attempts++;
        if (attempts > 10) {
            clearInterval(interval);
            return;
        }

        if ((window.viewerGeneral && !window.viewerGeneral.undoRedoManager) ||
            (window.viewerMedical && !window.viewerMedical.undoRedoManager)) {
            checkAndInit();
        }
    }, 1000);
});

console.log('✅ Undo/Redo Manager System loaded');

// Helper function for debugging
window.checkUndoRedoStatus = function() {
    const viewer = window.viewerGeneral || window.viewer;
    if (!viewer) {
        console.log('❌ No viewer found');
        return;
    }

    if (!viewer.undoRedoManager) {
        console.log('❌ Undo/Redo manager not initialized');
        console.log('💡 Initializing now...');
        window.initUndoRedoManager(viewer);
        return;
    }

    const state = viewer.undoRedoManager.getState();
    console.log('✅ Undo/Redo Manager Status:');
    console.log('   Can Undo:', state.canUndo);
    console.log('   Can Redo:', state.canRedo);
    console.log('   Undo Stack:', state.undoCount, 'actions');
    console.log('   Redo Stack:', state.redoCount, 'actions');
    console.log('   Last Action:', state.lastAction || 'None');
    console.log('\n📚 Undo Stack:', viewer.undoRedoManager.undoStack);
    console.log('📚 Redo Stack:', viewer.undoRedoManager.redoStack);
    console.log('\n💡 Try: Ctrl+Z to undo, Ctrl+Shift+Z to redo');
};

console.log('💡 Type checkUndoRedoStatus() in console to check the system status');
