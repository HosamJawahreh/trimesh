/**
 * Professional Lighting Control System
 * Controls: Light Intensity, Shadow Intensity, Light Rotation (Azimuth)
 * Features: Toggle buttons with collapsible panels
 */

(function() {
    'use strict';

    class LightingController {
        constructor() {
            this.viewer = null;
            this.lightIntensity = 0.9;
            this.shadowIntensity = 1.0;
            this.lightAzimuth = 45; // Rotation angle in degrees
            this.isInitialized = false;

            // Panel visibility states
            this.lightPanelVisible = false;
            this.shadowPanelVisible = false;
            this.rotationPanelVisible = false;

            console.log('💡 Lighting Controller created');
        }

        /**
         * Initialize controls when viewer is ready
         */
        init(viewer) {
            if (!viewer) {
                console.error('❌ Viewer not provided to lighting controller');
                return false;
            }

            this.viewer = viewer;

            // Get initial values from viewer
            if (viewer.lightIntensity !== undefined) {
                this.lightIntensity = viewer.lightIntensity;
            }
            if (viewer.shadowIntensity !== undefined) {
                this.shadowIntensity = viewer.shadowIntensity;
            }

            // Setup all controls
            this.setupLightIntensityControl();
            this.setupShadowIntensityControl();
            this.setupLightRotationControl();

            // Setup click-outside handler
            this.setupClickOutsideHandler();

            this.isInitialized = true;
            console.log('✅ Lighting controller initialized');
            return true;
        }

        /**
         * Toggle Light Intensity Panel
         */
        toggleLightPanel() {
            const panel = document.getElementById('lightIntensityPanel');
            const btn = document.getElementById('lightIntensityBtn');

            if (!panel || !btn) return;

            // Close other panels
            this.closeShadowPanel();
            this.closeRotationPanel();

            // Toggle this panel
            this.lightPanelVisible = !this.lightPanelVisible;
            panel.style.display = this.lightPanelVisible ? 'block' : 'none';

            // Update button active state
            if (this.lightPanelVisible) {
                btn.classList.add('active');
                console.log('💡 Light intensity panel opened');
            } else {
                btn.classList.remove('active');
                console.log('💡 Light intensity panel closed');
            }
        }

        /**
         * Toggle Shadow Intensity Panel
         */
        toggleShadowPanel() {
            const panel = document.getElementById('shadowIntensityPanel');
            const btn = document.getElementById('shadowIntensityBtn');

            if (!panel || !btn) return;

            // Close other panels
            this.closeLightPanel();
            this.closeRotationPanel();

            // Toggle this panel
            this.shadowPanelVisible = !this.shadowPanelVisible;
            panel.style.display = this.shadowPanelVisible ? 'block' : 'none';

            // Update button active state
            if (this.shadowPanelVisible) {
                btn.classList.add('active');
                console.log('🌑 Shadow intensity panel opened');
            } else {
                btn.classList.remove('active');
                console.log('�� Shadow intensity panel closed');
            }
        }

        /**
         * Toggle Light Rotation Panel
         */
        toggleRotationPanel() {
            const panel = document.getElementById('lightRotationPanel');
            const btn = document.getElementById('lightRotationBtn');

            if (!panel || !btn) return;

            // Close other panels
            this.closeLightPanel();
            this.closeShadowPanel();

            // Toggle this panel
            this.rotationPanelVisible = !this.rotationPanelVisible;
            panel.style.display = this.rotationPanelVisible ? 'block' : 'none';

            // Update button active state
            if (this.rotationPanelVisible) {
                btn.classList.add('active');
                console.log('🔄 Light rotation panel opened');
            } else {
                btn.classList.remove('active');
                console.log('🔄 Light rotation panel closed');
            }
        }

        /**
         * Close Light Panel
         */
        closeLightPanel() {
            const panel = document.getElementById('lightIntensityPanel');
            const btn = document.getElementById('lightIntensityBtn');

            if (panel && btn) {
                this.lightPanelVisible = false;
                panel.style.display = 'none';
                btn.classList.remove('active');
            }
        }

        /**
         * Close Shadow Panel
         */
        closeShadowPanel() {
            const panel = document.getElementById('shadowIntensityPanel');
            const btn = document.getElementById('shadowIntensityBtn');

            if (panel && btn) {
                this.shadowPanelVisible = false;
                panel.style.display = 'none';
                btn.classList.remove('active');
            }
        }

        /**
         * Close Rotation Panel
         */
        closeRotationPanel() {
            const panel = document.getElementById('lightRotationPanel');
            const btn = document.getElementById('lightRotationBtn');

            if (panel && btn) {
                this.rotationPanelVisible = false;
                panel.style.display = 'none';
                btn.classList.remove('active');
            }
        }

        /**
         * Close all panels
         */
        closeAllPanels() {
            this.closeLightPanel();
            this.closeShadowPanel();
            this.closeRotationPanel();
        }

        /**
         * Setup click-outside handler to close panels when clicking elsewhere
         */
        setupClickOutsideHandler() {
            document.addEventListener('click', (event) => {
                const lightPanel = document.getElementById('lightIntensityPanel');
                const shadowPanel = document.getElementById('shadowIntensityPanel');
                const rotationPanel = document.getElementById('lightRotationPanel');

                const lightBtn = document.getElementById('lightIntensityBtn');
                const shadowBtn = document.getElementById('shadowIntensityBtn');
                const rotationBtn = document.getElementById('lightRotationBtn');

                // Check if click is outside all panels and buttons
                const clickedOutside =
                    !lightPanel?.contains(event.target) && !lightBtn?.contains(event.target) &&
                    !shadowPanel?.contains(event.target) && !shadowBtn?.contains(event.target) &&
                    !rotationPanel?.contains(event.target) && !rotationBtn?.contains(event.target);

                if (clickedOutside) {
                    this.closeAllPanels();
                }
            });
        }

        /**
         * Setup Light Intensity Control
         */
        setupLightIntensityControl() {
            const slider = document.getElementById('lightIntensitySlider');
            const valueDisplay = document.getElementById('lightIntensityValue');

            if (!slider) {
                console.warn('⚠️ Light intensity slider not found');
                return;
            }

            // Input event for real-time updates
            slider.addEventListener('input', (e) => {
                const intensity = parseFloat(e.target.value);
                this.setLightIntensity(intensity);

                // Update value display
                if (valueDisplay) {
                    const percentage = Math.round((intensity / 2) * 100);
                    valueDisplay.textContent = `${percentage}%`;
                }

                // Update slider gradient
                const gradientPercentage = (intensity / 2) * 100;
                slider.style.background = `linear-gradient(to right, #f39c12 0%, #f39c12 ${gradientPercentage}%, #e0e0e0 ${gradientPercentage}%, #e0e0e0 100%)`;
            });

            // Initialize
            slider.value = this.lightIntensity;
            if (valueDisplay) {
                const percentage = Math.round((this.lightIntensity / 2) * 100);
                valueDisplay.textContent = `${percentage}%`;
            }

            console.log('✅ Light intensity control ready');
        }

        /**
         * Setup Shadow Intensity Control
         */
        setupShadowIntensityControl() {
            const slider = document.getElementById('shadowIntensitySlider');
            const valueDisplay = document.getElementById('shadowIntensityValue');

            if (!slider) {
                console.warn('⚠️ Shadow intensity slider not found');
                return;
            }

            // Input event for real-time updates
            slider.addEventListener('input', (e) => {
                const intensity = parseFloat(e.target.value);
                this.setShadowIntensity(intensity);

                // Update value display
                if (valueDisplay) {
                    const percentage = Math.round(intensity * 100);
                    valueDisplay.textContent = `${percentage}%`;
                }

                // Update slider gradient
                const gradientPercentage = intensity * 100;
                slider.style.background = `linear-gradient(to right, #7f8c8d 0%, #7f8c8d ${gradientPercentage}%, #e0e0e0 ${gradientPercentage}%, #e0e0e0 100%)`;
            });

            // Initialize
            slider.value = this.shadowIntensity;
            if (valueDisplay) {
                const percentage = Math.round(this.shadowIntensity * 100);
                valueDisplay.textContent = `${percentage}%`;
            }

            console.log('✅ Shadow intensity control ready');
        }

        /**
         * Setup Light Rotation Control
         */
        setupLightRotationControl() {
            const slider = document.getElementById('lightRotationSlider');
            const valueDisplay = document.getElementById('lightRotationValue');

            if (!slider) {
                console.warn('⚠️ Light rotation slider not found');
                return;
            }

            // Input event for real-time updates
            slider.addEventListener('input', (e) => {
                const angle = parseFloat(e.target.value);
                this.setLightRotation(angle);

                // Update value display
                if (valueDisplay) {
                    valueDisplay.textContent = `${angle}°`;
                }

                // Update slider gradient
                const gradientPercentage = (angle / 360) * 100;
                slider.style.background = `linear-gradient(to right, #9b59b6 0%, #9b59b6 ${gradientPercentage}%, #e0e0e0 ${gradientPercentage}%, #e0e0e0 100%)`;
            });

            // Initialize
            slider.value = this.lightAzimuth;
            if (valueDisplay) {
                valueDisplay.textContent = `${this.lightAzimuth}°`;
            }

            console.log('✅ Light rotation control ready');
        }

        /**
         * Set light intensity
         */
        setLightIntensity(intensity) {
            if (!this.viewer || !this.viewer.mainLight) {
                console.warn('⚠️ Viewer or main light not available');
                return;
            }

            this.lightIntensity = intensity;
            this.viewer.mainLight.intensity = intensity;

            // Update fill light proportionally (40% of main light)
            if (this.viewer.fillLight) {
                this.viewer.fillLight.intensity = intensity * 0.4;
            }

            // Force render update
            if (this.viewer.renderer && this.viewer.scene && this.viewer.camera) {
                this.viewer.renderer.render(this.viewer.scene, this.viewer.camera);
            }

            console.log(`💡 Light intensity set to ${intensity} (${Math.round((intensity/2)*100)}%)`);
        }

        /**
         * Set shadow intensity
         */
        setShadowIntensity(intensity) {
            if (!this.viewer || !this.viewer.mainLight) {
                console.warn('⚠️ Viewer or main light not available');
                return;
            }

            this.shadowIntensity = intensity;

            // Update shadow bias (lower = darker shadows)
            if (this.viewer.mainLight.shadow) {
                this.viewer.mainLight.shadow.bias = -0.001 * (1 - intensity);
            }

            // Traverse scene to update mesh shadow properties
            if (this.viewer.scene) {
                this.viewer.scene.traverse((object) => {
                    if (object.isMesh) {
                        object.castShadow = intensity > 0;
                        object.receiveShadow = intensity > 0;
                    }
                });
            }

            // Enable/disable shadow map
            if (this.viewer.renderer && this.viewer.renderer.shadowMap) {
                this.viewer.renderer.shadowMap.enabled = intensity > 0;
            }

            // Force render update
            if (this.viewer.renderer && this.viewer.scene && this.viewer.camera) {
                this.viewer.renderer.render(this.viewer.scene, this.viewer.camera);
            }

            console.log(`🌑 Shadow intensity set to ${intensity} (${Math.round(intensity*100)}%)`);
        }

        /**
         * Set light rotation (azimuth angle)
         */
        setLightRotation(angle) {
            if (!this.viewer || !this.viewer.mainLight) {
                console.warn('⚠️ Viewer or main light not available');
                return;
            }

            this.lightAzimuth = angle;

            // Convert angle to radians
            const azimuthRad = (angle * Math.PI) / 180;

            // Position light in circular path around model
            const distance = 100; // Distance from center
            const elevation = 50; // Height above model

            const x = Math.cos(azimuthRad) * distance;
            const z = Math.sin(azimuthRad) * distance;

            this.viewer.mainLight.position.set(x, elevation, z);

            // Point light at model center
            if (this.viewer.controls && this.viewer.controls.target) {
                this.viewer.mainLight.target.position.copy(this.viewer.controls.target);
            }

            // Update fill light with offset
            if (this.viewer.fillLight) {
                const fillAngle = azimuthRad + Math.PI / 4; // 45° offset
                const fillDistance = distance * 0.7;
                const fillX = Math.cos(fillAngle) * fillDistance;
                const fillZ = Math.sin(fillAngle) * fillDistance;
                this.viewer.fillLight.position.set(fillX, elevation * 0.8, fillZ);
            }

            // Force render update
            if (this.viewer.renderer && this.viewer.scene && this.viewer.camera) {
                this.viewer.renderer.render(this.viewer.scene, this.viewer.camera);
            }

            console.log(`🔄 Light rotation set to ${angle}° (azimuth: ${azimuthRad.toFixed(2)} rad)`);
        }

        /**
         * Get current light intensity
         */
        getLightIntensity() {
            return this.lightIntensity;
        }

        /**
         * Get current shadow intensity
         */
        getShadowIntensity() {
            return this.shadowIntensity;
        }

        /**
         * Get current light rotation
         */
        getLightRotation() {
            return this.lightAzimuth;
        }

        /**
         * Reset all lighting controls to defaults
         */
        reset() {
            this.setLightIntensity(0.9);
            this.setShadowIntensity(1.0);
            this.setLightRotation(45);

            // Update UI
            const lightSlider = document.getElementById('lightIntensitySlider');
            const shadowSlider = document.getElementById('shadowIntensitySlider');
            const rotationSlider = document.getElementById('lightRotationSlider');

            if (lightSlider) lightSlider.value = 0.9;
            if (shadowSlider) shadowSlider.value = 1.0;
            if (rotationSlider) rotationSlider.value = 45;

            console.log('🔄 Lighting controls reset to defaults');
        }
    }

    // Create global instance
    window.lightingController = new LightingController();

    // Auto-initialize when viewers are ready
    window.addEventListener('viewersReady', () => {
        console.log('🎬 Viewers ready, initializing lighting controller...');

        const viewer = window.viewerGeneral || window.viewer;
        if (viewer) {
            window.lightingController.init(viewer);
        } else {
            console.error('❌ No viewer found for lighting controller');
        }
    });

    console.log('✅ Lighting Controller module loaded');
})();
