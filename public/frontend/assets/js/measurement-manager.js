/**
 * Professional Measurement Manager
 * Handles all measurement types with proper point management and visual feedback
 */

(function() {
    'use strict';

    class MeasurementManager {
        constructor() {
            this.activeTool = null;
            this.measurementPoints = [];
            this.measurementLines = [];
            this.measurementLabels = [];
            this.measurements = [];
            this.currentMeasurementId = 0;
            this.areaPolygonMesh = null;

            // Simple blue color for all measurements
            this.colors = {
                distance: 0x4A90E2,
                diameter: 0x4A90E2,
                area: 0x4A90E2,
                'point-to-surface': 0x4A90E2,
                angle: 0x4A90E2
            };
        }

        /**
         * Start a new measurement tool
         * Clears previous measurements and visual elements
         */
        selectTool(viewer, measureType, viewerType) {
            console.log(`📐 Selecting measurement tool: ${measureType}`);

            // Clear all previous measurements and visual elements
            this.clearAllMeasurements(viewer);

            // Set the active tool
            this.activeTool = measureType;

            // Visual feedback - highlight active button
            this.updateButtonStates(measureType);

            // Show instruction based on tool type
            const instructions = {
                distance: 'Click two points on the model to measure distance',
                diameter: 'Click two points on opposite sides to measure diameter',
                area: 'Click 3+ points to define area. Click first point again to close.',
                'point-to-surface': 'Click a point, then click the target surface',
                angle: 'Click three points: First point → Vertex (middle) → Third point'
            };

            this.showInstruction(instructions[measureType] || 'Select points on the model');

            // Setup click handler for this measurement type
            this.setupClickHandler(viewer, measureType, viewerType);
        }

        /**
         * Update button visual states
         */
        updateButtonStates(activeMeasureType) {
            // Remove active class from all measurement buttons
            document.querySelectorAll('.submenu-btn[data-measure]').forEach(btn => {
                btn.classList.remove('active');
                btn.style.background = '';
                btn.style.color = '';
            });

            // Add active class and simple blue background to selected tool
            if (activeMeasureType) {
                document.querySelectorAll(`.submenu-btn[data-measure="${activeMeasureType}"]`).forEach(btn => {
                    btn.classList.add('active');
                    // Simple blue background for all tools
                    btn.style.background = '#4A90E2';
                    btn.style.color = 'white';
                });
            }
        }

        /**
         * Clear all measurements and visual elements
         */
        clearAllMeasurements(viewer) {
            console.log('🧹 Clearing all measurements and visual elements');

            const THREE = window.THREE;
            if (!THREE || !viewer || !viewer.scene) return;

            // Remove all measurement points (spheres)
            this.measurementPoints.forEach(point => {
                if (point.parent) {
                    point.parent.remove(point);
                }
                if (point.geometry) point.geometry.dispose();
                if (point.material) point.material.dispose();
            });
            this.measurementPoints = [];

            // Remove all measurement lines
            this.measurementLines.forEach(line => {
                if (line.parent) {
                    line.parent.remove(line);
                }
                if (line.geometry) line.geometry.dispose();
                if (line.material) line.material.dispose();
            });
            this.measurementLines = [];

            // Remove all labels
            this.measurementLabels.forEach(label => {
                if (label.element && label.element.parentNode) {
                    label.element.parentNode.removeChild(label.element);
                }
            });
            this.measurementLabels = [];

            // Remove area polygon if exists
            if (this.areaPolygonMesh) {
                if (this.areaPolygonMesh.parent) {
                    this.areaPolygonMesh.parent.remove(this.areaPolygonMesh);
                }
                if (this.areaPolygonMesh.geometry) this.areaPolygonMesh.geometry.dispose();
                if (this.areaPolygonMesh.material) this.areaPolygonMesh.material.dispose();
                this.areaPolygonMesh = null;
            }

            // Remove preview line if exists
            if (this.previewLine) {
                if (this.previewLine.parent) {
                    this.previewLine.parent.remove(this.previewLine);
                }
                if (this.previewLine.geometry) this.previewLine.geometry.dispose();
                if (this.previewLine.material) this.previewLine.material.dispose();
                this.previewLine = null;
            }

            // Remove ALL objects with isMeasurement flag from scene
            const objectsToRemove = [];
            viewer.scene.traverse((object) => {
                if (object.userData && object.userData.isMeasurement) {
                    objectsToRemove.push(object);
                }
            });

            objectsToRemove.forEach(obj => {
                if (obj.parent) {
                    obj.parent.remove(obj);
                }
                if (obj.geometry) obj.geometry.dispose();
                if (obj.material) {
                    if (Array.isArray(obj.material)) {
                        obj.material.forEach(mat => mat.dispose());
                    } else {
                        obj.material.dispose();
                    }
                }
            });

            // Clear measurements list
            this.measurements = [];
            this.currentMeasurementId = 0;

            // Update UI
            this.updateMeasurementsList();

            // Force render
            if (viewer.renderer) {
                viewer.renderer.render(viewer.scene, viewer.camera);
            }

            console.log('✅ All measurements, points, lines, and labels cleared from scene');
        }

        /**
         * Setup click handler for measurement tool
         */
        setupClickHandler(viewer, measureType, viewerType) {
            const THREE = window.THREE;
            if (!THREE) return;

            // Store reference for raycasting
            const raycaster = new THREE.Raycaster();
            const mouse = new THREE.Vector2();

            const canvas = viewer.renderer.domElement;

            // Remove previous handler if exists
            if (this.clickHandler) {
                canvas.removeEventListener('click', this.clickHandler);
            }

            // Create new click handler
            this.clickHandler = (event) => {
                // Get mouse position in normalized device coordinates
                const rect = canvas.getBoundingClientRect();
                mouse.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
                mouse.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;

                // Update raycaster
                raycaster.setFromCamera(mouse, viewer.camera);

                // Get intersections with model
                const intersects = raycaster.intersectObjects(viewer.scene.children, true);

                // Filter to only model meshes (not points, lines, etc)
                const modelIntersects = intersects.filter(intersect =>
                    intersect.object.isMesh &&
                    intersect.object.geometry &&
                    !intersect.object.userData.isMeasurement
                );

                if (modelIntersects.length > 0) {
                    const point = modelIntersects[0].point;
                    const normal = modelIntersects[0].face ? modelIntersects[0].face.normal : null;

                    this.handlePointClick(viewer, measureType, point, normal);
                }
            };

            // Attach handler
            canvas.addEventListener('click', this.clickHandler);

            console.log(`✅ Click handler setup for ${measureType}`);
        }

        /**
         * Handle point click based on measurement type
         */
        handlePointClick(viewer, measureType, point, normal) {
            const THREE = window.THREE;

            switch(measureType) {
                case 'distance':
                case 'diameter':
                    this.addPoint(viewer, point, measureType);

                    if (this.measurementPoints.length === 2) {
                        // Calculate distance
                        const p1 = this.measurementPoints[0].position;
                        const p2 = this.measurementPoints[1].position;
                        const distance = p1.distanceTo(p2);

                        // Draw line
                        this.drawLine(viewer, p1, p2, measureType);

                        // Add label
                        const midPoint = new THREE.Vector3().addVectors(p1, p2).multiplyScalar(0.5);
                        const label = measureType === 'diameter' ?
                            `Ø ${distance.toFixed(2)} mm` :
                            `${distance.toFixed(2)} mm`;
                        this.addLabel(viewer, midPoint, label, measureType);

                        // Save measurement
                        this.saveMeasurement(measureType, distance, 'mm');

                        // Complete this measurement
                        this.completeMeasurement(viewer, measureType);
                    }
                    break;

                case 'angle':
                    this.addPoint(viewer, point, measureType);

                    if (this.measurementPoints.length === 3) {
                        // Calculate angle between three points
                        const p1 = this.measurementPoints[0].position;
                        const vertex = this.measurementPoints[1].position;
                        const p3 = this.measurementPoints[2].position;

                        // Draw lines connecting the points
                        this.drawLine(viewer, p1, vertex, measureType);
                        this.drawLine(viewer, vertex, p3, measureType);

                        // Calculate angle
                        const v1 = new THREE.Vector3().subVectors(p1, vertex).normalize();
                        const v2 = new THREE.Vector3().subVectors(p3, vertex).normalize();
                        const angleRad = v1.angleTo(v2);
                        const angleDeg = THREE.MathUtils.radToDeg(angleRad);

                        // Add label at vertex
                        this.addLabel(viewer, vertex, `∠ ${angleDeg.toFixed(1)}°`, measureType);

                        // Save measurement
                        this.saveMeasurement('angle', angleDeg, '°');

                        // Complete this measurement
                        this.completeMeasurement(viewer, measureType);
                    }
                    break;

                case 'area':
                    this.addPoint(viewer, point, measureType);

                    // Need at least 3 points for area
                    if (this.measurementPoints.length >= 3) {
                        // Check if user clicked near first point to close polygon
                        const firstPoint = this.measurementPoints[0].position;
                        const distToFirst = point.distanceTo(firstPoint);

                        if (distToFirst < 5 && this.measurementPoints.length > 3) {
                            // Close polygon
                            this.calculateAndDrawArea(viewer);
                            this.completeMeasurement(viewer, measureType);
                        } else {
                            // Draw line to previous point
                            if (this.measurementPoints.length > 1) {
                                const prevPoint = this.measurementPoints[this.measurementPoints.length - 2].position;
                                this.drawLine(viewer, prevPoint, point, measureType);
                            }

                            // Show preview line to first point if we have 3+ points
                            if (this.measurementPoints.length >= 3) {
                                this.showAreaPreview(viewer);
                            }
                        }
                    }
                    break;

                case 'point-to-surface':
                    this.addPoint(viewer, point, measureType);

                    if (this.measurementPoints.length === 2) {
                        const p1 = this.measurementPoints[0].position;
                        const p2 = this.measurementPoints[1].position;
                        const distance = p1.distanceTo(p2);

                        // Draw perpendicular line
                        this.drawLine(viewer, p1, p2, measureType, true); // dashed

                        // Add label
                        const midPoint = new THREE.Vector3().addVectors(p1, p2).multiplyScalar(0.5);
                        this.addLabel(viewer, midPoint, `⊥ ${distance.toFixed(2)} mm`, measureType);

                        // Save measurement
                        this.saveMeasurement('point-to-surface', distance, 'mm');

                        // Complete this measurement
                        this.completeMeasurement(viewer, measureType);
                    }
                    break;
            }
        }

        /**
         * Add measurement point (sphere) to scene
         */
        addPoint(viewer, position, measureType) {
            const THREE = window.THREE;
            const color = this.colors[measureType] || 0x4A90E2;

            // Create sphere at point
            const geometry = new THREE.SphereGeometry(0.5, 16, 16);
            const material = new THREE.MeshBasicMaterial({ color: color });
            const sphere = new THREE.Mesh(geometry, material);
            sphere.position.copy(position);
            sphere.userData.isMeasurement = true;

            viewer.scene.add(sphere);
            this.measurementPoints.push(sphere);

            // Render
            viewer.renderer.render(viewer.scene, viewer.camera);

            console.log(`📍 Point added at (${position.x.toFixed(2)}, ${position.y.toFixed(2)}, ${position.z.toFixed(2)})`);
        }

        /**
         * Draw line between two points
         */
        drawLine(viewer, p1, p2, measureType, dashed = false) {
            const THREE = window.THREE;
            const color = this.colors[measureType] || 0x4A90E2;

            const points = [p1.clone(), p2.clone()];
            const geometry = new THREE.BufferGeometry().setFromPoints(points);

            const material = dashed ?
                new THREE.LineDashedMaterial({ color: color, dashSize: 1, gapSize: 0.5, linewidth: 2 }) :
                new THREE.LineBasicMaterial({ color: color, linewidth: 2 });

            const line = new THREE.Line(geometry, material);
            if (dashed) line.computeLineDistances();
            line.userData.isMeasurement = true;

            viewer.scene.add(line);
            this.measurementLines.push(line);

            // Render
            viewer.renderer.render(viewer.scene, viewer.camera);
        }

        /**
         * Add 3D label above measurement
         */
        addLabel(viewer, position, text, measureType) {
            const labelDiv = document.createElement('div');
            labelDiv.className = 'measurement-label';
            labelDiv.textContent = text;
            labelDiv.style.cssText = `
                position: absolute;
                background: rgba(74, 144, 226, 0.95);
                color: white;
                padding: 4px 8px;
                border-radius: 4px;
                font-size: 12px;
                font-weight: bold;
                pointer-events: none;
                z-index: 1000;
                white-space: nowrap;
                box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            `;

            document.body.appendChild(labelDiv);

            this.measurementLabels.push({
                element: labelDiv,
                position: position.clone(),
                viewer: viewer
            });

            // Update label position
            this.updateLabelPosition(labelDiv, position, viewer);
        }

        /**
         * Update label screen position
         */
        updateLabelPosition(labelDiv, position, viewer) {
            const canvas = viewer.renderer.domElement;
            const rect = canvas.getBoundingClientRect();

            const pos = position.clone().project(viewer.camera);
            const x = (pos.x * 0.5 + 0.5) * rect.width + rect.left;
            const y = (-pos.y * 0.5 + 0.5) * rect.height + rect.top;

            labelDiv.style.left = `${x}px`;
            labelDiv.style.top = `${y - 20}px`;
        }

        /**
         * Calculate and draw area polygon
         */
        calculateAndDrawArea(viewer) {
            const THREE = window.THREE;

            if (this.measurementPoints.length < 3) return;

            // Get points
            const points = this.measurementPoints.map(p => p.position.clone());

            // Close the polygon by connecting last to first
            const lastPoint = points[points.length - 1];
            const firstPoint = points[0];
            this.drawLine(viewer, lastPoint, firstPoint, 'area');

            // Calculate area using Shoelace formula (for 3D points projected to best-fit plane)
            // For simplicity, we'll project to XY plane
            let area = 0;
            for (let i = 0; i < points.length; i++) {
                const p1 = points[i];
                const p2 = points[(i + 1) % points.length];
                area += (p1.x * p2.y - p2.x * p1.y);
            }
            area = Math.abs(area) / 2;

            // Create filled polygon mesh with blue color
            const shape = new THREE.Shape();
            points.forEach((p, i) => {
                if (i === 0) shape.moveTo(p.x, p.y);
                else shape.lineTo(p.x, p.y);
            });

            const geometry = new THREE.ShapeGeometry(shape);
            const material = new THREE.MeshBasicMaterial({
                color: 0x4A90E2,
                transparent: true,
                opacity: 0.3,
                side: THREE.DoubleSide
            });

            this.areaPolygonMesh = new THREE.Mesh(geometry, material);
            this.areaPolygonMesh.userData.isMeasurement = true;

            // Position at average Z
            const avgZ = points.reduce((sum, p) => sum + p.z, 0) / points.length;
            this.areaPolygonMesh.position.z = avgZ;

            viewer.scene.add(this.areaPolygonMesh);

            // Add label at center
            const center = new THREE.Vector3();
            points.forEach(p => center.add(p));
            center.multiplyScalar(1 / points.length);

            this.addLabel(viewer, center, `Area: ${area.toFixed(2)} mm²`, 'area');

            // Save measurement
            this.saveMeasurement('area', area, 'mm²');

            // Render
            viewer.renderer.render(viewer.scene, viewer.camera);
        }

        /**
         * Show area preview (dashed line to first point)
         */
        showAreaPreview(viewer) {
            // Remove old preview if exists
            if (this.previewLine) {
                viewer.scene.remove(this.previewLine);
                if (this.previewLine.geometry) this.previewLine.geometry.dispose();
                if (this.previewLine.material) this.previewLine.material.dispose();
            }

            const lastPoint = this.measurementPoints[this.measurementPoints.length - 1].position;
            const firstPoint = this.measurementPoints[0].position;

            const THREE = window.THREE;
            const points = [lastPoint.clone(), firstPoint.clone()];
            const geometry = new THREE.BufferGeometry().setFromPoints(points);
            const material = new THREE.LineDashedMaterial({
                color: 0x4A90E2,
                dashSize: 1,
                gapSize: 0.5,
                opacity: 0.5,
                transparent: true
            });

            this.previewLine = new THREE.Line(geometry, material);
            this.previewLine.computeLineDistances();
            this.previewLine.userData.isMeasurement = true;

            viewer.scene.add(this.previewLine);
            viewer.renderer.render(viewer.scene, viewer.camera);
        }

        /**
         * Save measurement to history
         */
        saveMeasurement(type, value, unit) {
            const measurement = {
                id: ++this.currentMeasurementId,
                type: type,
                value: value,
                unit: unit,
                timestamp: new Date()
            };

            this.measurements.push(measurement);
            this.updateMeasurementsList();

            console.log('💾 Measurement saved:', measurement);
        }

        /**
         * Update measurements list UI
         */
        updateMeasurementsList() {
            const container = document.getElementById('measurementResultsList');
            if (!container) return;

            if (this.measurements.length === 0) {
                container.innerHTML = `
                    <div class="no-measurements">
                        <svg width="48" height="48" viewBox="0 0 48 48" fill="none">
                            <path d="M8 8L40 40M12 8L16 12M20 8L24 12M28 8L32 12M36 8L40 12M8 12L12 8M8 20L16 12M8 28L24 12" stroke="#ccc" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <p>No measurements yet</p>
                        <small>Click a measurement tool to start</small>
                    </div>
                `;
                return;
            }

            const icons = {
                distance: '📏',
                diameter: '⭕',
                area: '▭',
                'point-to-surface': '📍',
                angle: '∠'
            };

            container.innerHTML = this.measurements.map(m => `
                <div class="measurement-item" style="padding: 10px; border-bottom: 1px solid #eee; border-left: 3px solid #4A90E2;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <span style="font-size: 18px;">${icons[m.type]}</span>
                            <strong style="margin-left: 8px;">${m.type.charAt(0).toUpperCase() + m.type.slice(1)}</strong>
                        </div>
                        <span style="font-size: 16px; font-weight: bold; color: #4A90E2;">
                            ${m.value.toFixed(2)} ${m.unit}
                        </span>
                    </div>
                </div>
            `).join('');

            // Show measurement panel
            const panel = document.getElementById('measurementResultsPanel');
            if (panel) {
                panel.style.display = 'block';
            }
        }

        /**
         * Complete current measurement
         */
        completeMeasurement(viewer, measureType) {
            console.log(`✅ ${measureType} measurement complete`);

            // Keep the tool active so user can make more measurements
            // Clear points for next measurement
            this.measurementPoints = [];

            // Show success message
            this.showInstruction(`${measureType} measurement saved! Click to measure again or select another tool.`);
        }

        /**
         * Show instruction message
         */
        showInstruction(message) {
            // Update status bar or show notification
            const statusBar = document.querySelector('.active-tool-status');
            if (statusBar) {
                const instructionEl = statusBar.querySelector('.tool-instruction');
                if (instructionEl) {
                    instructionEl.textContent = message;
                }
            }

            console.log(`💡 ${message}`);
        }

        /**
         * Cancel active tool
         */
        cancelTool(viewer) {
            if (this.clickHandler) {
                const canvas = viewer.renderer.domElement;
                canvas.removeEventListener('click', this.clickHandler);
                this.clickHandler = null;
            }

            this.activeTool = null;
            this.updateButtonStates(null);

            console.log('❌ Tool cancelled');
        }

        /**
         * Update labels on camera movement
         */
        updateLabelsOnRender(viewer) {
            this.measurementLabels.forEach(label => {
                if (label.viewer === viewer) {
                    this.updateLabelPosition(label.element, label.position, viewer);
                }
            });
        }
    }

    // Create global instance
    window.measurementManager = new MeasurementManager();

    console.log('✅ Measurement Manager initialized');
})();
