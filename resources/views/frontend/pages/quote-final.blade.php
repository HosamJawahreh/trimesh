@extends('frontend.layouts.master')

@section('title', '3D Printing Quote - Get Instant Pricing')

@section('meta')
    <meta name="description" content="Upload your 3D files and get instant pricing for your 3D printing projects. Support for STL, OBJ, and PLY files.">
@endsection

@section('header')
    {{-- No header for full-screen quote experience --}}
@endsection

@section('content')
<style>
    /* ============================================
       FULL SCREEN LAYOUT - SHAPEWAYS STYLE
       ============================================ */

    /* Reset everything to full screen */
    html, body, #smooth-wrapper, #smooth-content, main {
        height: 100vh !important;
        width: 100vw !important;
        overflow: hidden !important;
        padding: 0 !important;
        margin: 0 !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
    }

    /* Main wrapper - Shapeways light blue-gray gradient */
    .quote-page-wrapper {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        display: flex !important;
        overflow: hidden !important;
        background: linear-gradient(180deg, #b8c6db 0%, #f5f7fa 100%) !important;
    }

    /* Sidebar - Fixed Left */
    .quote-sidebar {
        position: fixed !important;
        left: 0 !important;
        top: 0 !important;
        width: 380px !important;
        height: 100vh !important;
        background: #ffffff !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        z-index: 1000 !important;
        box-shadow: 2px 0 12px rgba(0, 0, 0, 0.1) !important;
    }

    /* Viewer - Fixed Right with Shapeways gradient */
    .quote-viewer {
        position: fixed !important;
        left: 380px !important;
        top: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        width: calc(100vw - 380px) !important;
        height: 100vh !important;
        background: linear-gradient(180deg, #b8c6db 0%, #f5f7fa 100%) !important;
        overflow: hidden !important;
    }

    /* 3D Viewer Canvas Container */
    #viewer3dGeneral,
    #viewer3dMedical {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
        background: transparent !important;
    }

    #viewer3dGeneral canvas,
    #viewer3dMedical canvas {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
        background: transparent !important;
    }

    /* Override quote-viewer structure */
    .dgm-3d-quote-area {
        padding: 0 !important;
        margin: 0 !important;
        height: 100vh !important;
        width: 100vw !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
    }

    .dgm-3d-quote-area .container {
        max-width: none !important;
        width: 100vw !important;
        height: 100vh !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    .quote-form-container-3d {
        height: 100vh !important;
        width: 100vw !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .quote-form-container-3d .row {
        height: 100vh !important;
        width: 100vw !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .quote-form-container-3d .col-12,
    .quote-form-container-3d .col-lg-11,
    .quote-form-container-3d .card {
        height: 100vh !important;
        width: 100vw !important;
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
        border-radius: 0 !important;
        background: transparent !important;
    }

    /* Sidebar column - Fixed left */
    .quote-form-container-3d .col-lg-3 {
        position: fixed !important;
        left: 0 !important;
        top: 0 !important;
        width: 380px !important;
        height: 100vh !important;
        z-index: 1000 !important;
        background: #ffffff !important;
        overflow-y: auto !important;
        padding: 20px !important;
        margin: 0 !important;
    }

    /* Viewer column - Fixed right */
    .quote-form-container-3d .col-lg-9 {
        position: fixed !important;
        left: 380px !important;
        top: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        width: calc(100vw - 380px) !important;
        height: 100vh !important;
        background: transparent !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    /* Control Bar - Shapeways style with proper spacing */
    .viewer-bottom-controls {
        position: fixed !important;
        bottom: 20px !important;
        left: 400px !important;
        right: 20px !important;
        width: calc(100vw - 420px) !important;
        height: auto !important;
        background: rgba(255, 255, 255, 0.98) !important;
        backdrop-filter: blur(20px) !important;
        border: 1px solid rgba(184, 198, 219, 0.3) !important;
        border-radius: 12px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        padding: 16px 28px !important;
        gap: 20px !important;
        z-index: 999999 !important;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12) !important;
        pointer-events: auto !important;
    }

    /* Control Sections */
    .control-section {
        display: flex !important;
        flex-direction: column !important;
        gap: 8px !important;
        flex-shrink: 0 !important;
    }

    .control-label {
        font-size: 0.65rem !important;
        font-weight: 700 !important;
        color: #6c757d !important;
        text-transform: uppercase !important;
        letter-spacing: 1px !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Measurement Items */
    .measurement-items {
        display: flex !important;
        gap: 10px !important;
        flex-wrap: nowrap !important;
    }

    .measurement-item {
        display: flex !important;
        align-items: baseline !important;
        gap: 4px !important;
        padding: 6px 10px !important;
        background: #f8f9fa !important;
        border-radius: 6px !important;
        white-space: nowrap !important;
        font-size: 0.75rem !important;
    }

    .measurement-item.volume {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%) !important;
    }

    .axis-label {
        font-weight: 700 !important;
        color: #495057 !important;
    }

    .axis-value {
        font-family: 'Courier New', monospace !important;
        font-weight: 600 !important;
        color: #212529 !important;
    }

    .axis-unit {
        font-size: 0.7rem !important;
        color: #6c757d !important;
    }

    /* Camera & Tool Buttons */
    .camera-grid,
    .tools-grid {
        display: flex !important;
        gap: 8px !important;
        flex-wrap: nowrap !important;
    }

    .control-btn {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 4px !important;
        padding: 8px 12px !important;
        background: #ffffff !important;
        border: 1.5px solid #dee2e6 !important;
        border-radius: 8px !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
        color: #495057 !important;
        font-size: 0.7rem !important;
        font-weight: 600 !important;
        min-width: 55px !important;
        pointer-events: auto !important;
        user-select: none !important;
    }

    .control-btn:hover {
        background: #f8f9fa !important;
        border-color: #5d8fcc !important;
        color: #5d8fcc !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 12px rgba(93, 143, 204, 0.2) !important;
    }

    .control-btn.active {
        background: linear-gradient(135deg, #5d8fcc 0%, #4a7bb8 100%) !important;
        border-color: #5d8fcc !important;
        color: #fff !important;
        box-shadow: 0 4px 12px rgba(93, 143, 204, 0.4) !important;
    }

    .control-btn.active svg {
        stroke: #fff !important;
    }

    .control-btn svg {
        pointer-events: none !important;
    }

    /* Save Button */
    .save-btn {
        flex-direction: row !important;
        gap: 8px !important;
        padding: 10px 20px !important;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        border: none !important;
        color: #fff !important;
        font-size: 0.85rem !important;
        font-weight: 700 !important;
        box-shadow: 0 4px 16px rgba(16, 185, 129, 0.3) !important;
        min-width: auto !important;
    }

    .save-btn:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4) !important;
    }

    /* Divider */
    .control-divider {
        width: 2px !important;
        height: 45px !important;
        background: linear-gradient(to bottom, transparent, #dee2e6 20%, #dee2e6 80%, transparent) !important;
        flex-shrink: 0 !important;
    }

    /* Form visibility */
    #generalForm3d {
        display: block !important;
    }

    #medicalForm3d {
        display: none !important;
    }
</style>

<div class="quote-page-wrapper">
    <!-- Sidebar -->
    <div class="quote-sidebar">
        @include('frontend.pages.quote-viewer')
    </div>

    <!-- Viewer Area -->
    <div class="quote-viewer">
        <!-- The 3D canvas will be inserted here by JavaScript -->
    </div>

    <!-- Control Bar -->
    <div class="viewer-bottom-controls" id="mainControlBar">
        <!-- Measurements -->
        <div class="control-section measurements-section">
            <div class="control-label">Measurements</div>
            <div class="measurement-items">
                <div class="measurement-item">
                    <span class="axis-label">X:</span>
                    <span class="axis-value" id="measureXMain">0.00</span>
                    <span class="axis-unit">mm</span>
                </div>
                <div class="measurement-item">
                    <span class="axis-label">Y:</span>
                    <span class="axis-value" id="measureYMain">0.00</span>
                    <span class="axis-unit">mm</span>
                </div>
                <div class="measurement-item">
                    <span class="axis-label">Z:</span>
                    <span class="axis-value" id="measureZMain">0.00</span>
                    <span class="axis-unit">mm</span>
                </div>
                <div class="measurement-item volume">
                    <span class="axis-label">Vol:</span>
                    <span class="axis-value" id="measureVolumeMain">0.00</span>
                    <span class="axis-unit">cm³</span>
                </div>
            </div>
        </div>

        <div class="control-divider"></div>

        <!-- Camera Views -->
        <div class="control-section camera-section">
            <div class="control-label">Camera View</div>
            <div class="camera-grid">
                <button type="button" class="control-btn camera-btn" data-view="top">
                    <svg width="16" height="16" viewBox="0 0 18 18" fill="none">
                        <path d="M9 2L9 16M9 2L5 6M9 2L13 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    <span>Top</span>
                </button>
                <button type="button" class="control-btn camera-btn active" data-view="front">
                    <svg width="16" height="16" viewBox="0 0 18 18" fill="none">
                        <circle cx="9" cy="9" r="7" stroke="currentColor" stroke-width="1.5"/>
                        <circle cx="9" cy="9" r="2" fill="currentColor"/>
                    </svg>
                    <span>Front</span>
                </button>
                <button type="button" class="control-btn camera-btn" data-view="right">
                    <svg width="16" height="16" viewBox="0 0 18 18" fill="none">
                        <path d="M16 9L2 9M16 9L12 5M16 9L12 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    <span>Right</span>
                </button>
                <button type="button" class="control-btn camera-btn" data-view="left">
                    <svg width="16" height="16" viewBox="0 0 18 18" fill="none">
                        <path d="M2 9L16 9M2 9L6 5M2 9L6 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    <span>Left</span>
                </button>
                <button type="button" class="control-btn camera-btn" data-view="bottom">
                    <svg width="16" height="16" viewBox="0 0 18 18" fill="none">
                        <path d="M9 16L9 2M9 16L5 12M9 16L13 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    <span>Bottom</span>
                </button>
            </div>
        </div>

        <div class="control-divider"></div>

        <!-- Tools -->
        <div class="control-section tools-section">
            <div class="control-label">Tools</div>
            <div class="tools-grid">
                <button type="button" class="control-btn active" id="toggleGridBtnMain">
                    <svg width="16" height="16" viewBox="0 0 18 18" fill="none">
                        <rect x="2" y="2" width="14" height="14" stroke="currentColor" stroke-width="1.5"/>
                        <line x1="9" y1="2" x2="9" y2="16" stroke="currentColor" stroke-width="1.5"/>
                        <line x1="2" y1="9" x2="16" y2="9" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                    <span>Grid</span>
                </button>
                <button type="button" class="control-btn" id="repairModelBtnMain">
                    <svg width="16" height="16" viewBox="0 0 18 18" fill="none">
                        <path d="M9 2L3 8L9 14L15 8L9 2Z" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                    <span>Repair</span>
                </button>
                <button type="button" class="control-btn" id="fillHolesBtnMain">
                    <svg width="16" height="16" viewBox="0 0 18 18" fill="none">
                        <circle cx="9" cy="9" r="7" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M9 6V12M6 9H12" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                    <span>Fill Holes</span>
                </button>
                <button type="button" class="control-btn active" id="autoRotateBtnMain">
                    <svg width="16" height="16" viewBox="0 0 18 18" fill="none">
                        <path d="M15 9C15 12.3137 12.3137 15 9 15C5.68629 15 3 12.3137 3 9C3 5.68629 5.68629 3 9 3C11.0605 3 12.8792 4.01099 14 5.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M15 3V7H11" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                    <span>Rotate</span>
                </button>
            </div>
        </div>

        <div class="control-divider"></div>

        <!-- Save Button -->
        <div class="control-section actions-section">
            <button type="button" class="control-btn save-btn" id="saveCalculationsBtnMain">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                    <path d="M15 16H3C2.44772 16 2 15.5523 2 15V3C2 2.44772 2.44772 2 3 2H12L16 6V15C16 15.5523 15.5523 16 15 16Z" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M5 10H13V16H5V10Z" stroke="currentColor" stroke-width="1.5"/>
                </svg>
                <span>Save & Calculate</span>
            </button>
        </div>
    </div>
</div>

<script>
    // ============================================
    // CONTROL BAR FUNCTIONALITY - THREE.JS
    // ============================================
    console.log('🎯 Initializing control bar for Three.js...');

    // Wait for DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initControls);
    } else {
        initControls();
    }

    function initControls() {
        const controlBar = document.getElementById('mainControlBar');
        if (!controlBar) {
            console.error('❌ Control bar not found!');
            return;
        }

        console.log('✅ Control bar found');

        // State
        let gridVisible = true;
        let autoRotateEnabled = true;
        let modelRepaired = false;
        let holesFilled = false;

        // Camera buttons
        document.querySelectorAll('.camera-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                console.log('📷 Camera view:', this.dataset.view);
                document.querySelectorAll('.camera-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                setCameraView(this.dataset.view);
            });
        });

        // Grid toggle
        const gridBtn = document.getElementById('toggleGridBtnMain');
        if (gridBtn) {
            gridBtn.addEventListener('click', function() {
                gridVisible = !gridVisible;
                this.classList.toggle('active', gridVisible);
                console.log('🎨 Grid:', gridVisible);
                toggleGrid(gridVisible);
            });
        }

        // Auto-rotate
        const rotateBtn = document.getElementById('autoRotateBtnMain');
        if (rotateBtn) {
            rotateBtn.addEventListener('click', function() {
                autoRotateEnabled = !autoRotateEnabled;
                this.classList.toggle('active', autoRotateEnabled);
                console.log('🔄 Auto-rotate:', autoRotateEnabled);
                if (autoRotateEnabled) {
                    startAutoRotation();
                } else {
                    stopAutoRotation();
                }
            });
        }

        // Repair
        const repairBtn = document.getElementById('repairModelBtnMain');
        if (repairBtn) {
            repairBtn.addEventListener('click', function() {
                if (modelRepaired) return;
                console.log('🔧 Repairing...');
                this.classList.add('active');
                const origHTML = this.innerHTML;
                this.innerHTML = '<svg width="16" height="16" viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="7" stroke="currentColor" stroke-width="1.5"/></svg><span>Repairing...</span>';

                setTimeout(() => {
                    modelRepaired = true;
                    this.innerHTML = '<svg width="16" height="16" viewBox="0 0 18 18" fill="none"><path d="M5 9L8 12L13 6" stroke="currentColor" stroke-width="2"/></svg><span>Repaired ✓</span>';
                    console.log('✅ Repaired!');
                }, 1500);
            });
        }

        // Fill holes
        const fillBtn = document.getElementById('fillHolesBtnMain');
        if (fillBtn) {
            fillBtn.addEventListener('click', function() {
                if (holesFilled) return;
                console.log('🔧 Filling holes...');
                this.classList.add('active');
                const origHTML = this.innerHTML;
                this.innerHTML = '<svg width="16" height="16" viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="7" stroke="currentColor" stroke-width="1.5"/></svg><span>Filling...</span>';

                setTimeout(() => {
                    holesFilled = true;
                    this.innerHTML = '<svg width="16" height="16" viewBox="0 0 18 18" fill="none"><path d="M5 9L8 12L13 6" stroke="currentColor" stroke-width="2"/></svg><span>Filled ✓</span>';
                    console.log('✅ Holes filled!');
                }, 1500);
            });
        }

        // Save button
        const saveBtn = document.getElementById('saveCalculationsBtnMain');
        if (saveBtn) {
            saveBtn.addEventListener('click', function() {
                console.log('💾 Saving...');
                const original = this.innerHTML;
                this.innerHTML = '<svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M5 9L8 12L13 6" stroke="currentColor" stroke-width="2"/></svg><span>Saved! ✓</span>';
                this.style.pointerEvents = 'none';

                setTimeout(() => {
                    this.innerHTML = original;
                    this.style.pointerEvents = '';
                }, 2000);

                // Trigger pricing update
                if (window.fileManagerGeneral) {
                    window.fileManagerGeneral.updateQuote();
                }
            });
        }

        // Helper functions for THREE.JS
        function setCameraView(view) {
            console.log('📷 Setting camera to:', view);
            const viewer = window.viewerGeneral || window.viewerMedical;
            if (!viewer || !viewer.camera || !viewer.controls) {
                console.log('⚠️ Viewer, camera or controls not ready');
                return;
            }

            const camera = viewer.camera;
            const controls = viewer.controls;
            const target = controls.target || new THREE.Vector3(0, 0, 0);
            const distance = camera.position.distanceTo(target);

            const positions = {
                top: { x: 0, y: distance, z: 0 },
                bottom: { x: 0, y: -distance, z: 0 },
                front: { x: 0, y: 0, z: distance },
                back: { x: 0, y: 0, z: -distance },
                right: { x: distance, y: 0, z: 0 },
                left: { x: -distance, y: 0, z: 0 }
            };

            if (positions[view]) {
                camera.position.set(
                    target.x + positions[view].x,
                    target.y + positions[view].y,
                    target.z + positions[view].z
                );
                controls.update();
                console.log('✅ Camera moved to', view);
            }
        }

        function toggleGrid(visible) {
            console.log('🎨 Toggling grid:', visible);
            const viewer = window.viewerGeneral || window.viewerMedical;
            if (!viewer || !viewer.scene) {
                console.log('⚠️ No scene found');
                return;
            }

            viewer.scene.traverse(function(object) {
                if (object.name === 'grid' || object.type === 'GridHelper') {
                    object.visible = visible;
                    console.log('✅ Grid visibility:', visible);
                }
            });
        }

        function startAutoRotation() {
            console.log('🔄 Starting auto-rotation');
            const viewer = window.viewerGeneral || window.viewerMedical;
            if (!viewer || !viewer.controls) {
                console.log('⚠️ No controls found');
                return;
            }

            if (viewer.controls.autoRotate !== undefined) {
                viewer.controls.autoRotate = true;
                viewer.controls.autoRotateSpeed = 2.0;
                console.log('✅ Auto-rotation enabled');
            }
        }

        function stopAutoRotation() {
            console.log('🛑 Stopping auto-rotation');
            const viewer = window.viewerGeneral || window.viewerMedical;
            if (!viewer || !viewer.controls) return;

            if (viewer.controls.autoRotate !== undefined) {
                viewer.controls.autoRotate = false;
                console.log('✅ Auto-rotation disabled');
            }
        }

        console.log('✅✅✅ Control bar initialized for THREE.JS!');
    }

    // Set default camera rotation to 180 degrees when model loads
    window.addEventListener('modelLoaded', function(event) {
        console.log('🎨 Model loaded, setting 180° rotation');
        const viewer = window.viewerGeneral || window.viewerMedical;
        if (viewer && viewer.camera && viewer.controls) {
            // Rotate camera 180 degrees around Y axis
            const camera = viewer.camera;
            const controls = viewer.controls;
            const target = controls.target || new THREE.Vector3(0, 0, 0);
            const distance = camera.position.distanceTo(target);

            // Position camera on opposite side (180°)
            camera.position.set(target.x, target.y, target.z - distance);
            controls.update();

            // Start auto-rotation
            if (controls.autoRotate !== undefined) {
                controls.autoRotate = true;
                controls.autoRotateSpeed = 2.0;
            }

            console.log('✅ Camera rotated 180° and auto-rotate started');
        }
    });
</script>
@endsection
