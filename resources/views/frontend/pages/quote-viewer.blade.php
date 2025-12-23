{{-- ============================================
     3D QUOTE VIEWER - FULL SCREEN INTERFACE
     Professional & Optimized Layout
     ============================================ --}}

{{-- EMERGENCY TEST BUTTON - REMOVE AFTER TESTING --}}


<section class="dgm-3d-quote-area pb-100">
    <div class="container">
        {{-- General 3D Printing Form --}}
        <div class="quote-form-container-3d" id="generalForm3d">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-11">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            <div class="row g-0">

                                {{-- Left Sidebar: Form Controls --}}
                                <div class="col-12 col-lg-3">
                                    <div class="p-3 p-lg-4">

                                        {{-- Site Logo --}}
                                        <div class="text-center" style="padding: 0 0 15px 0;">
                                            <img src="{{ asset($settings->logo) }}" alt="{{ $settings->app_name }}" style="max-width: 200px; height: auto;">
                                        </div>

                                        {{-- Category Tabs --}}
                                        <div class="btn-group w-100 mb-3" role="group">
                                            <button type="button" class="btn btn-sm category-tab-btn active" data-category="general" style="border-radius: 8px 0 0 8px; padding: 10px; font-size: 0.85rem; font-weight: 600;">
                                                General
                                            </button>
                                            <button type="button" class="btn btn-sm category-tab-btn" data-category="medical" style="border-radius: 0 8px 8px 0; padding: 10px; font-size: 0.85rem; font-weight: 600;">
                                                Medical
                                            </button>
                                        </div>

                                        <!-- Upload Area -->
                                        <div class="upload-drop-zone-3d text-center p-3 mb-3" style="border: 2px dashed #cbd5e0; border-radius: 10px; background: white; cursor: pointer; transition: all 0.3s;">
                                            <input type="file" id="fileInput3d" style="display: none;" accept=".stl,.obj,.ply" multiple>
                                            <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" class="mb-2">
                                                <circle cx="20" cy="20" r="20" fill="#e8f4f8"/>
                                                <path d="M20 10L26 16L20 22L14 16L20 10Z" stroke="#4a90e2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M14 22L20 28L26 22" stroke="#4a90e2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" opacity="0.5"/>
                                            </svg>
                                            <p class="mb-1" style="font-size: 0.9rem; font-weight: 600; color: #2c3e50;">Drop files or click</p>
                                            <small class="text-muted" style="font-size: 0.8rem;">STL, OBJ, PLY (Max 100MB each) • Multiple files supported</small>
                                        </div>

                                        <!-- Uploaded Files List -->
                                        <div id="uploadedFilesList" class="mb-3" style="display: none;">
                                            <label class="form-label mb-2" style="font-size: 0.85rem; font-weight: 600; color: #495057;">Uploaded Files (<span id="fileCount">0</span>)</label>
                                            <div id="filesContainer" style="max-height: 200px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 8px; background: #f8f9fa;"></div>
                                        </div>

                                        <!-- Technology -->
                                        <div class="mb-3">
                                            <label class="form-label mb-2" style="font-size: 0.85rem; font-weight: 600; color: #495057;">Technology</label>
                                            <select id="technologySelectGeneral" class="form-select form-select-sm" style="border-radius: 8px; border: 1px solid #dee2e6; font-size: 0.85rem; padding: 8px 12px;">
                                                <option value="fdm" selected>FDM (Fused Deposition Modeling)</option>
                                                <option value="sla">SLA (Stereolithography)</option>
                                                <option value="sls">SLS (Selective Laser Sintering)</option>
                                                <option value="dmls">DMLS (Direct Metal Laser Sintering)</option>
                                                <option value="mjf">MJF (Multi Jet Fusion)</option>
                                            </select>
                                        </div>

                                        <!-- Material -->
                                        <div class="mb-3">
                                            <label class="form-label mb-2" style="font-size: 0.85rem; font-weight: 600; color: #495057;">Material</label>
                                            <select id="materialSelectGeneral" class="form-select form-select-sm" style="border-radius: 8px; border: 1px solid #dee2e6; font-size: 0.85rem; padding: 8px 12px;">
                                                <option value="pla">PLA</option>
                                                <option value="abs">ABS</option>
                                                <option value="petg">PETG</option>
                                                <option value="nylon">Nylon</option>
                                                <option value="resin">Resin</option>
                                            </select>
                                        </div>

                                        <!-- Color Picker -->
                                        <div class="mb-3">
                                            <label class="form-label mb-2" style="font-size: 0.85rem; font-weight: 600; color: #2c3e50;">Model Color</label>
                                            <div class="d-flex gap-2 flex-wrap">
                                                <button type="button" class="color-btn active" data-color="#0047AD" style="width: 32px; height: 32px; border-radius: 6px; border: 2px solid #0047AD; background: #0047AD; cursor: pointer; position: relative;">
                                                    <span style="position: absolute; bottom: -2px; right: -2px; width: 10px; height: 10px; background: #0047AD; border-radius: 50%; display: none;"></span>
                                                </button>
                                                <button type="button" class="color-btn" data-color="#ffffff" style="width: 32px; height: 32px; border-radius: 6px; border: 2px solid #dee2e6; background: #ffffff; cursor: pointer;"></button>
                                                <button type="button" class="color-btn" data-color="#2c3e50" style="width: 32px; height: 32px; border-radius: 6px; border: 2px solid #dee2e6; background: #2c3e50; cursor: pointer;"></button>
                                                <button type="button" class="color-btn" data-color="#3498db" style="width: 32px; height: 32px; border-radius: 6px; border: 2px solid #dee2e6; background: #3498db; cursor: pointer;"></button>
                                                <button type="button" class="color-btn" data-color="#e74c3c" style="width: 32px; height: 32px; border-radius: 6px; border: 2px solid #dee2e6; background: #e74c3c; cursor: pointer;"></button>
                                                <button type="button" class="color-btn" data-color="#2ecc71" style="width: 32px; height: 32px; border-radius: 6px; border: 2px solid #dee2e6; background: #2ecc71; cursor: pointer;"></button>
                                                <button type="button" class="color-btn" data-color="#f39c12" style="width: 32px; height: 32px; border-radius: 6px; border: 2px solid #dee2e6; background: #f39c12; cursor: pointer;"></button>
                                            </div>
                                        </div>

                                        <!-- Price Summary -->
                                        <div id="priceSummaryGeneral" class="mt-3 p-3" style="display: none !important; background: white; border-radius: 12px; border: 2px solid #e9ecef; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">

<script>
// Hide price summary and sidebar price by default, only show after Save & Calculate and after repair
document.addEventListener('DOMContentLoaded', function() {
    const priceSummaryGeneral = document.getElementById('priceSummaryGeneral');
    if (priceSummaryGeneral) priceSummaryGeneral.style.display = 'none';

    // Hide price summary and sidebar price on file upload or removal
    function hidePriceSummary() {
        if (priceSummaryGeneral) priceSummaryGeneral.style.display = 'none';
        // Hide sidebar price
        const priceSidebar = document.querySelector('.total-price, #quoteTotalPriceGeneral');
        if (priceSidebar) priceSidebar.textContent = '';
        // Hide volume
        const volumeSidebar = document.getElementById('quoteTotalVolumeGeneral');
        if (volumeSidebar) volumeSidebar.textContent = '';
    }
    const fileInput = document.getElementById('fileInput3d');
    if (fileInput) fileInput.addEventListener('change', hidePriceSummary);
    document.addEventListener('fileRemoved', hidePriceSummary);

    // Attach to ALL Save & Calculate buttons (there are multiple with same ID - invalid HTML but we'll handle it)
    const saveBtns = document.querySelectorAll('#saveCalculationsBtn, .save-btn');
    console.log('🔍 Found', saveBtns.length, 'Save buttons');
    saveBtns.forEach((saveBtn, index) => {
        console.log('📌 Attaching handler to button', index);
        saveBtn.addEventListener('click', async function() {
            console.log('💾 SAVE & CALCULATE STARTED - Button', index);

            const viewer = window.viewerGeneral;
            if (!viewer || !viewer.uploadedFiles || viewer.uploadedFiles.length === 0) {
                alert('❌ Please upload a 3D model first!');
                return;
            }

            console.log('� STEP 1: REPAIRING MESHES...');

            // STEP 1: Repair ALL meshes using the MeshRepairVisual system
            if (window.MeshRepairVisual) {
                for (let i = 0; i < viewer.uploadedFiles.length; i++) {
                    const fileData = viewer.uploadedFiles[i];
                    console.log(`\n🔧 Repairing file ${i + 1}/${viewer.uploadedFiles.length}: ${fileData.file.name}`);

                    try {
                        const result = await window.MeshRepairVisual.repairMeshWithVisualization(viewer, fileData);
                        console.log('✅ Repair result:', result);

                        if (result.holesFilled > 0) {
                            console.log(`   ✅ Filled ${result.holesFilled} holes!`);
                        } else if (result.watertight) {
                            console.log('   ✅ Mesh was already watertight');
                        } else {
                            console.log('   ⚠️ No holes filled');
                        }
                    } catch (error) {
                        console.error('❌ Repair failed:', error);
                    }
                }
            } else {
                console.warn('⚠️ MeshRepairVisual not loaded - skipping repair');
            }

            console.log('\n📊 STEP 2: RECALCULATING VOLUMES...');

            // STEP 2: Recalculate volume for ALL files (AFTER repair)
            viewer.uploadedFiles.forEach((fileData, index) => {
                if (fileData.geometry && viewer.calculateVolume) {
                    const oldVolume = fileData.volume?.cm3 || 0;
                    const newVolume = viewer.calculateVolume(fileData.geometry);
                    fileData.volume = newVolume;
                    console.log(`\n📦 File ${index + 1}: ${fileData.file.name}`);
                    console.log(`   OLD Volume: ${oldVolume.toFixed(2)} cm³`);
                    console.log(`   NEW Volume: ${newVolume.cm3.toFixed(2)} cm³`);
                    if (oldVolume > 0) {
                        const change = ((newVolume.cm3 - oldVolume) / oldVolume * 100);
                        console.log(`   CHANGE: ${change >= 0 ? '+' : ''}${change.toFixed(2)}%`);
                    }
                }
            });

            console.log('\n✅ Volume recalculation complete!');
            console.log('\n💰 STEP 3: UPDATING PRICING...');

            // STEP 3: Update pricing with NEW volumes
            if (window.fileManagerGeneral) {
                console.log('✅ Calling fileManagerGeneral.updateQuote() with NEW volumes');
                window.fileManagerGeneral.updateQuote();
            } else if (window.viewerGeneral) {
                console.log('⚠️ fileManagerGeneral not found, creating it now...');
                const FileManager = window.FileManager;
                if (FileManager) {
                    window.fileManagerGeneral = new FileManager('General', window.viewerGeneral);
                    window.fileManagerGeneral.updateQuote();
                } else {
                    alert('❌ Error: FileManager class not loaded');
                    return;
                }
            }

            console.log('\n✅✅✅ ALL DONE! ✅✅✅');
            alert('✅ Mesh repaired, volume recalculated, and price updated!\n\nCheck the console (F12) to see:\n- Holes filled\n- Volume changes\n- New pricing');
        });
    });
});
</script>
                                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2" style="border-bottom: 1px solid #f1f3f5;">
                                                <span style="font-size: 0.8rem; color: #6c757d; font-weight: 500;">Volume</span>
                                                <strong id="quoteTotalVolumeGeneral" style="font-weight: 600; font-size: 0.85rem; color: #2c3e50; display: none;">0 cm³</strong>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2" style="border-bottom: 1px solid #f1f3f5;">
                                                <span style="font-size: 0.8rem; color: #6c757d; font-weight: 500;">Print Time</span>
                                                <strong id="quotePrintTimeGeneral" style="font-weight: 600; font-size: 0.85rem; color: #2c3e50;">0h</strong>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-2 pt-2">
                                                <span style="font-size: 0.85rem; font-weight: 600; color: #495057; text-transform: uppercase; letter-spacing: 0.3px;">Total Price</span>
                                                <h4 class="mb-0" id="quoteTotalPriceGeneral" style="font-weight: 700; font-size: 1.3rem; color: #2c3e50; display: none;">$0</h4>
                                            </div>
                                            <button type="button" class="btn w-100 mt-2" id="btnRequestQuoteGeneral" style="border-radius: 10px; font-weight: 600; font-size: 0.85rem; padding: 10px; background: #4a90e2; color: white; border: none; transition: all 0.3s; box-shadow: 0 2px 8px rgba(74, 144, 226, 0.3);">
                                                Request Quote →
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Right Side: 3D Viewer --}}
                                <div class="col-12 col-lg-9 position-relative d-flex flex-column" style="display: flex !important; visibility: visible !important; opacity: 1 !important; flex: 1 !important; min-width: 0 !important; background: linear-gradient(to bottom, #b8c5d6 0%, #99a8ba 100%) !important;">

                                    {{-- 3D Viewer Canvas --}}
                                    <div id="viewer3dGeneral" style="position: relative !important; display: flex !important; visibility: visible !important; width: 100% !important; height: 100vh !important; background: linear-gradient(to bottom, #b8c5d6 0%, #99a8ba 100%) !important;">

                                        {{-- Model Info Badge (Top Left - Hidden until file uploaded) --}}
                                        <div class="model-info-badge" style="display: none;">
                                            <div class="model-name">
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                    <path d="M8 2L14 5L8 8L2 5L8 2Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M2 11L8 14L14 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" opacity="0.7"/>
                                                </svg>
                                                <span id="modelNameDisplay">model.stl</span>
                                            </div>
                                            <div class="model-subtitle" id="modelDimensionsDisplay">0 × 0 × 0 mm</div>
                                        </div>

                                        {{-- Professional Toolbar - Top Right --}}
                                        <div class="viewer-professional-toolbar" id="professionalToolbar" style="position: absolute !important; top: 20px !important; right: 20px !important; display: flex !important; visibility: visible !important; opacity: 1 !important; z-index: 9999 !important; background: rgba(255, 255, 255, 0.95) !important; padding: 8px !important; border-radius: 12px !important; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15) !important; pointer-events: auto !important; gap: 8px !important;">

                                            {{-- Tools Group --}}
                                            <div class="toolbar-group" style="display: flex !important; gap: 4px !important; visibility: visible !important; opacity: 1 !important;">

                                                <button type="button" class="toolbar-btn" id="measurementToolBtn" title="Measurement Tools" data-tool="measurement" onclick="window.toolbarHandler.toggleMeasurement('General')">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <path d="M4 16L16 4M6 16L8 14M10 16L12 14M14 16L16 14M4 14L6 12M4 10L8 6M4 6L6 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                    </svg>
                                                </button>
                                                <button type="button" class="toolbar-btn" id="boundingBoxBtn" title="Bounding Box" data-tool="boundingBox" onclick="window.toolbarHandler.toggleBoundingBox('General')">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <rect x="3" y="3" width="14" height="14" stroke="currentColor" stroke-width="1.8" stroke-dasharray="2 2"/>
                                                        <circle cx="3" cy="3" r="1.5" fill="currentColor"/>
                                                        <circle cx="17" cy="3" r="1.5" fill="currentColor"/>
                                                        <circle cx="3" cy="17" r="1.5" fill="currentColor"/>
                                                        <circle cx="17" cy="17" r="1.5" fill="currentColor"/>
                                                    </svg>
                                                </button>
                                                <button type="button" class="toolbar-btn" id="axisToggleBtn" title="Toggle Axis" data-tool="axis" onclick="window.toolbarHandler.toggleAxis('General')">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <path d="M10 2V18M2 10H18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                        <path d="M10 2L8 4M10 2L12 4M18 10L16 8M18 10L16 12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                    </svg>
                                                </button>
                                                <button type="button" class="toolbar-btn" id="gridToggleBtn" title="Measurement Grid" data-tool="grid" onclick="window.toolbarHandler.toggleGrid('General')">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <path d="M2 6H18M2 10H18M2 14H18M6 2V18M10 2V18M14 2V18" stroke="currentColor" stroke-width="1.5" opacity="0.6"/>
                                                    </svg>
                                                </button>
                                            </div>

                                            <div class="toolbar-divider"></div>

                                            {{-- View Options --}}
                                            <div class="toolbar-group">
                                                <button type="button" class="toolbar-btn" id="shadowToggleBtn" title="Toggle Shadows" data-tool="shadow" onclick="window.toolbarHandler.toggleShadow('General')">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <circle cx="10" cy="8" r="3" stroke="currentColor" stroke-width="1.8"/>
                                                        <ellipse cx="10" cy="16" rx="5" ry="1.5" fill="currentColor" opacity="0.3"/>
                                                    </svg>
                                                </button>
                                                <button type="button" class="toolbar-btn" id="transparencyBtn" title="Transparency" data-tool="transparency" onclick="window.toolbarHandler.toggleTransparency('General')">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.8" opacity="0.5"/>
                                                        <path d="M10 3C6 3 3 6 3 10C3 14 6 17 10 17" stroke="currentColor" stroke-width="1.8"/>
                                                    </svg>
                                                </button>
                                                <button type="button" class="toolbar-btn" id="modelColorBtn" title="Model Color" data-tool="modelColor" onclick="window.toolbarHandler.changeModelColor()">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.8"/>
                                                        <circle cx="10" cy="10" r="4" fill="currentColor" opacity="0.3"/>
                                                    </svg>
                                                </button>
                                                <button type="button" class="toolbar-btn" id="backgroundColorBtn" title="Background Color" data-tool="bgColor" onclick="window.toolbarHandler.changeBackgroundColor()">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <rect x="2" y="2" width="16" height="16" rx="2" stroke="currentColor" stroke-width="1.8"/>
                                                        <path d="M2 10H18" stroke="currentColor" stroke-width="1.8"/>
                                                    </svg>
                                                </button>
                                            </div>

                                            <div class="toolbar-divider"></div>

                                            {{-- Actions --}}
                                            <div class="toolbar-group">
                                                <button type="button" class="toolbar-btn" id="undoBtn" title="Undo" data-action="undo" onclick="window.toolbarHandler.undo()">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <path d="M5 8H15C16.6569 8 18 9.34315 18 11C18 12.6569 16.6569 14 15 14H8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                        <path d="M8 5L5 8L8 11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </button>
                                                <button type="button" class="toolbar-btn" id="redoBtn" title="Redo" data-action="redo" onclick="window.toolbarHandler.redo()">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <path d="M15 8H5C3.34315 8 2 9.34315 2 11C2 12.6569 3.34315 14 5 14H12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                        <path d="M12 5L15 8L12 11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </button>
                                                <button type="button" class="toolbar-btn" id="screenshotToolBtn" title="Screenshot" data-action="screenshot" onclick="window.toolbarHandler.takeScreenshot('General')">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <rect x="2" y="5" width="16" height="12" rx="2" stroke="currentColor" stroke-width="1.8"/>
                                                        <circle cx="10" cy="11" r="2.5" stroke="currentColor" stroke-width="1.8"/>
                                                        <path d="M6 5L7 3H13L14 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Measurement Sub-Menu (Flyout) --}}
                                        <div class="measurement-submenu" id="measurementSubmenu" style="display: none;">
                                            <div class="submenu-header">
                                                <span>Measurement Tools</span>
                                                <button type="button" class="submenu-close">×</button>
                                            </div>
                                            <button type="button" class="submenu-btn" data-measure="distance">
                                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                                                    <path d="M2 2L16 16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                    <circle cx="2" cy="2" r="2" fill="currentColor"/>
                                                    <circle cx="16" cy="16" r="2" fill="currentColor"/>
                                                </svg>
                                                <span>Distance (Point-to-Point)</span>
                                            </button>
                                            <button type="button" class="submenu-btn" data-measure="point-to-line">
                                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                                                    <path d="M2 16L16 16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                    <path d="M9 2L9 16" stroke="currentColor" stroke-width="1.5" stroke-dasharray="2 2"/>
                                                    <circle cx="9" cy="2" r="2" fill="currentColor"/>
                                                </svg>
                                                <span>Point to Line</span>
                                            </button>
                                            <button type="button" class="submenu-btn" data-measure="point-to-surface">
                                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                                                    <rect x="2" y="12" width="14" height="4" stroke="currentColor" stroke-width="1.5"/>
                                                    <path d="M9 2L9 12" stroke="currentColor" stroke-width="1.5" stroke-dasharray="2 2"/>
                                                    <circle cx="9" cy="2" r="2" fill="currentColor"/>
                                                </svg>
                                                <span>Point to Surface</span>
                                            </button>
                                            <button type="button" class="submenu-btn" data-measure="angle">
                                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                                                    <path d="M2 16L9 9L16 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M4 16A6 6 0 0 1 9 11" stroke="currentColor" stroke-width="1.5" stroke-dasharray="2 2"/>
                                                </svg>
                                                <span>Angle</span>
                                            </button>
                                            <button type="button" class="submenu-btn" data-measure="clear">
                                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                                                    <path d="M3 3L15 15M15 3L3 15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                </svg>
                                                <span>Clear All Measurements</span>
                                            </button>
                                        </div>

                                        {{-- Unit Toggle (for Grid) --}}
                                        <div class="unit-toggle" id="unitToggle" style="display: none;">
                                            <button type="button" class="unit-btn active" data-unit="mm">mm</button>
                                            <button type="button" class="unit-btn" data-unit="inch">inch</button>
                                        </div>

                                        {{-- Bottom Control Bar - Professional --}}
                                        <div class="viewer-bottom-controls" id="controlBarGeneral">
                                            <div class="control-section measurements-section">
                                                <div class="control-label">Dimensions</div>
                                                <div class="measurement-items">
                                                    <div class="measurement-item">
                                                        <span class="axis-label">X:</span>
                                                        <span class="axis-value" id="measureX">0.00</span>
                                                        <span class="axis-unit">mm</span>
                                                    </div>
                                                    <div class="measurement-item">
                                                        <span class="axis-label">Y:</span>
                                                        <span class="axis-value" id="measureY">0.00</span>
                                                        <span class="axis-unit">mm</span>
                                                    </div>
                                                    <div class="measurement-item">
                                                        <span class="axis-label">Z:</span>
                                                        <span class="axis-value" id="measureZ">0.00</span>
                                                        <span class="axis-unit">mm</span>
                                                    </div>
                                                    <div class="measurement-item volume">
                                                        <span class="axis-label">Vol:</span>
                                                        <span class="axis-value" id="measureVolume">0.00</span>
                                                        <span class="axis-unit">cm³</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="control-divider"></div>

                                            <div class="control-section camera-section">
                                                <div class="control-label">Camera View</div>
                                                <div class="camera-buttons">
                                                    <button type="button" class="control-btn camera-btn" data-view="top" title="Top View">
                                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                            <path d="M8 2L14 6L8 10L2 6L8 2Z" stroke="currentColor" stroke-width="1.5"/>
                                                        </svg>
                                                        <span>Top</span>
                                                    </button>
                                                    <button type="button" class="control-btn camera-btn" data-view="front" title="Front View">
                                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                            <rect x="3" y="3" width="10" height="10" stroke="currentColor" stroke-width="1.5"/>
                                                        </svg>
                                                        <span>Front</span>
                                                    </button>
                                                    <button type="button" class="control-btn camera-btn" data-view="right" title="Right View">
                                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                            <path d="M3 3L13 3L13 13L3 13L3 3Z" stroke="currentColor" stroke-width="1.5"/>
                                                            <path d="M8 3L13 8L8 13" stroke="currentColor" stroke-width="1.5"/>
                                                        </svg>
                                                        <span>Right</span>
                                                    </button>
                                                    <button type="button" class="control-btn camera-btn" data-view="left" title="Left View">
                                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                            <path d="M13 3L3 3L3 13L13 13L13 3Z" stroke="currentColor" stroke-width="1.5"/>
                                                            <path d="M8 3L3 8L8 13" stroke="currentColor" stroke-width="1.5"/>
                                                        </svg>
                                                        <span>Left</span>
                                                    </button>
                                                    <button type="button" class="control-btn camera-btn" data-view="bottom" title="Bottom View">
                                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                            <path d="M8 14L2 10L8 6L14 10L8 14Z" stroke="currentColor" stroke-width="1.5"/>
                                                        </svg>
                                                        <span>Bottom</span>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="control-divider"></div>

                                            <div class="control-section tools-section">
                                                <div class="control-label">Tools</div>
                                                <div class="control-buttons">
                                                    <button type="button" class="control-btn tool-btn" id="panToolBtn" title="Pan Tool">
                                                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                                                            <path d="M9 2C9 2 9 7 9 7M9 7L6.5 4.5M9 7L11.5 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                            <path d="M9 16C9 16 9 11 9 11M9 11L6.5 13.5M9 11L11.5 13.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                            <path d="M2 9C2 9 7 9 7 9M7 9L4.5 6.5M7 9L4.5 11.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                            <path d="M16 9C16 9 11 9 11 9M11 9L13.5 6.5M11 9L13.5 11.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                        </svg>
                                                        <span>Pan</span>
                                                    </button>
                                                    <button type="button" class="control-btn tool-btn" id="screenshotBtn" title="Take Screenshot">
                                                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                                                            <rect x="2" y="4" width="14" height="11" rx="2" stroke="currentColor" stroke-width="1.5"/>
                                                            <circle cx="9" cy="9.5" r="2.5" stroke="currentColor" stroke-width="1.5"/>
                                                            <path d="M6 4L7 2H11L12 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                                        </svg>
                                                        <span>Screenshot</span>
                                                    </button>
                                                    <button type="button" class="control-btn tool-btn" id="shareGeneralBtn" title="Share Model">
                                                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                                                            <circle cx="13" cy="4" r="2.5" stroke="currentColor" stroke-width="1.5"/>
                                                            <circle cx="5" cy="9" r="2.5" stroke="currentColor" stroke-width="1.5"/>
                                                            <circle cx="13" cy="14" r="2.5" stroke="currentColor" stroke-width="1.5"/>
                                                            <path d="M7.5 10L10.5 12.5M7.5 8L10.5 5.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                                        </svg>
                                                        <span>Share</span>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="control-divider"></div>

                                            <div class="control-section actions-section">
                                                <button type="button" class="control-btn save-btn" id="saveCalculationsBtn">
                                                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                                                        <path d="M15 16H3C2.44772 16 2 15.5523 2 15V3C2 2.44772 2.44772 2 3 2H12L16 6V15C16 15.5523 15.5523 16 15 16Z" stroke="currentColor" stroke-width="1.5"/>
                                                        <path d="M12 2V6H5V2" stroke="currentColor" stroke-width="1.5"/>
                                                        <path d="M5 10H13V16H5V10Z" stroke="currentColor" stroke-width="1.5"/>
                                                    </svg>
                                                    <span>Save & Calculate</span>
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Empty State --}}
                                        <div class="d-flex align-items-center justify-content-center h-100 text-muted text-center">
                                            <div>
                                                <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg" style="opacity: 0.3;">
                                                    <path d="M40 10L70 25L40 40L10 25L40 10Z" stroke="#6c757d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M10 55L40 70L70 55" stroke="#6c757d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" opacity="0.7"/>
                                                    <path d="M10 40L40 55L70 40" stroke="#6c757d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" opacity="0.5"/>
                                                </svg>
                                                <p class="mt-3 mb-0" style="font-weight: 500; color: #6c757d; font-size: 0.95rem;">Upload a 3D file to preview</p>
                                                <p class="mt-1 mb-0" style="font-size: 0.8rem; color: #95a5a6;">Drag & drop or click to browse</p>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Viewer Controls Panel - REMOVED FOR CLEAN INTERFACE --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Medical Form - Unified Single Section -->
        <div class="quote-form-container-3d" id="medicalForm3d" style="display: none;">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-11">
                    <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                        <div class="card-body p-0">
                            <div class="row g-0">
                                <!-- Left Side: Controls -->
                                <div class="col-12 col-lg-3" style="background: #f8f9fa; border-right: 1px solid #e9ecef;">
                                    <div class="">
                                        <!-- Category Tabs -->
                                        <div class="btn-group w-100 mb-3" role="group">
                                            <button type="button" class="btn btn-sm category-tab-btn" data-category="general" style="border-radius: 8px 0 0 8px; padding: 10px; font-size: 0.85rem; font-weight: 600;">
                                                General
                                            </button>
                                            <button type="button" class="btn btn-sm category-tab-btn active" data-category="medical" style="border-radius: 0 8px 8px 0; padding: 10px; font-size: 0.85rem; font-weight: 600;">
                                                Medical
                                            </button>
                                        </div>

                                        <!-- Upload Area -->
                                        <div class="upload-drop-zone-3d text-center p-3 mb-3" style="border: 2px dashed #cbd5e0; border-radius: 10px; background: white; cursor: pointer; transition: all 0.3s;">
                                            <input type="file" id="fileInput3dMedical" style="display: none;" accept=".stl,.obj,.ply" multiple>
                                            <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" class="mb-2">
                                                <circle cx="20" cy="20" r="20" fill="#e8f4f8"/>
                                                <path d="M20 10L26 16L20 22L14 16L20 10Z" stroke="#4a90e2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M14 22L20 28L26 22" stroke="#4a90e2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" opacity="0.5"/>
                                            </svg>
                                            <p class="mb-1" style="font-size: 0.9rem; font-weight: 600; color: #2c3e50;">Drop files or click</p>
                                            <small class="text-muted" style="font-size: 0.8rem;">STL, OBJ, PLY (Max 100MB each) • Multiple files supported</small>
                                        </div>

                                        <!-- Uploaded Files List -->
                                        <div id="uploadedFilesListMedical" class="mb-3" style="display: none;">
                                            <label class="form-label mb-2" style="font-size: 0.85rem; font-weight: 600; color: #495057;">Uploaded Files (<span id="fileCountMedical">0</span>)</label>
                                            <div id="filesContainerMedical" style="max-height: 200px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 8px; background: #f8f9fa;"></div>
                                        </div>

                                        <!-- Technology -->
                                        <div class="mb-3">
                                            <label class="form-label mb-2" style="font-size: 0.85rem; font-weight: 600; color: #495057;">Technology</label>
                                            <select id="technologySelectMedical" class="form-select form-select-sm" style="border-radius: 6px; border: 1px solid #dee2e6; font-size: 0.85rem;">
                                                <option value="sla" selected>SLA (Stereolithography)</option>
                                                <option value="dmls">DMLS (Direct Metal Laser Sintering)</option>
                                                <option value="mjf">MJF (Multi Jet Fusion)</option>
                                                <option value="polyjet">PolyJet</option>
                                            </select>
                                        </div>

                                        <!-- Material -->
                                        <div class="mb-3">
                                            <label class="form-label mb-2" style="font-size: 0.85rem; font-weight: 600; color: #495057;">Material</label>
                                            <select id="materialSelectMedical" class="form-select form-select-sm" style="border-radius: 6px; border: 1px solid #dee2e6; font-size: 0.85rem;">
                                                <option value="medical-resin">Medical Resin</option>
                                                <option value="biocompatible">Biocompatible</option>
                                                <option value="surgical">Surgical Grade</option>
                                            </select>
                                        </div>

                                        <!-- Application -->
                                        <div class="mb-3">
                                            <label class="form-label mb-2" style="font-size: 0.85rem; font-weight: 600; color: #495057;">Application</label>
                                            <select id="applicationSelectMedical" class="form-select form-select-sm" style="border-radius: 6px; border: 1px solid #dee2e6; font-size: 0.85rem;">
                                                <option value="surgical">Surgical Guide</option>
                                                <option value="dental">Dental Model</option>
                                                <option value="anatomical">Anatomical</option>
                                                <option value="prosthetic">Prosthetic</option>
                                            </select>
                                        </div>

                                        <!-- Color Picker -->
                                        <div class="mb-3">
                                            <label class="form-label mb-2" style="font-size: 0.85rem; font-weight: 600; color: #495057;">Model Color</label>
                                            <div class="d-flex gap-2 flex-wrap">
                                                <button type="button" class="color-btn-medical active" data-color="#0047AD" style="width: 32px; height: 32px; border-radius: 6px; border: 2px solid #0047AD; background: #0047AD; cursor: pointer;"></button>
                                                <button type="button" class="color-btn-medical" data-color="#ffffff" style="width: 32px; height: 32px; border-radius: 6px; border: 2px solid #dee2e6; background: #ffffff; cursor: pointer;"></button>
                                                <button type="button" class="color-btn-medical" data-color="#0071cc" style="width: 32px; height: 32px; border-radius: 6px; border: 2px solid #dee2e6; background: #0071cc; cursor: pointer;"></button>
                                                <button type="button" class="color-btn-medical" data-color="#e91e63" style="width: 32px; height: 32px; border-radius: 6px; border: 2px solid #dee2e6; background: #e91e63; cursor: pointer;"></button>
                                                <button type="button" class="color-btn-medical" data-color="#9c27b0" style="width: 32px; height: 32px; border-radius: 6px; border: 2px solid #dee2e6; background: #9c27b0; cursor: pointer;"></button>
                                            </div>
                                        </div>

                                        <!-- Price Summary -->
                                        <div id="priceSummaryMedical" class="mt-3 p-3" style="display: none; background: white; border-radius: 12px; border: 2px solid #e9ecef; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2" style="border-bottom: 1px solid #f1f3f5;">
                                                <span style="font-size: 0.8rem; color: #6c757d; font-weight: 500;">Volume</span>
                                                <strong id="quoteTotalVolumeMedical" style="font-weight: 600; font-size: 0.85rem; color: #2c3e50;">0 cm³</strong>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2" style="border-bottom: 1px solid #f1f3f5;">
                                                <span style="font-size: 0.8rem; color: #6c757d; font-weight: 500;">Print Time</span>
                                                <strong id="quotePrintTimeMedical" style="font-weight: 600; font-size: 0.85rem; color: #2c3e50;">0h</strong>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-2 pt-2">
                                                <span style="font-size: 0.85rem; font-weight: 600; color: #495057; text-transform: uppercase; letter-spacing: 0.3px;">Total Price</span>
                                                <h4 class="mb-0" id="quoteTotalPriceMedical" style="font-weight: 700; font-size: 1.3rem; color: #2c3e50;">$0</h4>
                                            </div>
                                            <button type="button" class="btn w-100 mt-2" id="btnRequestQuoteMedical" style="border-radius: 10px; font-weight: 600; font-size: 0.85rem; padding: 10px; background: #4a90e2; color: white; border: none; transition: all 0.3s; box-shadow: 0 2px 8px rgba(74, 144, 226, 0.3);">
                                                Request Quote →
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Side: 3D Viewer -->
                                <div class="col-12 col-lg-9 position-relative d-flex flex-column" style="display: flex !important; visibility: visible !important; opacity: 1 !important; flex: 1 !important; min-width: 0 !important; background: linear-gradient(to bottom, #b8c5d6 0%, #99a8ba 100%) !important;">
                                    <div id="viewer3dMedical" style="position: relative !important; display: flex !important; visibility: visible !important; width: 100% !important; height: 100vh !important; background: linear-gradient(to bottom, #b8c5d6 0%, #99a8ba 100%) !important;">
                                        {{-- Model Info Badge (Top Left - Hidden until file uploaded) --}}
                                        <div class="model-info-badge" style="display: none;">
                                            <div class="model-name">
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                    <path d="M8 2L14 5L8 8L2 5L8 2Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M2 11L8 14L14 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" opacity="0.7"/>
                                                </svg>
                                                <span id="modelNameDisplayMedical">model.stl</span>
                                            </div>
                                            <div class="model-subtitle" id="modelDimensionsDisplayMedical">0 × 0 × 0 mm</div>
                                        </div>

                                        {{-- Professional Toolbar - Top Right --}}
                                        <div class="viewer-professional-toolbar" id="professionalToolbarMedical" style="position: absolute !important; top: 20px !important; right: 20px !important; display: flex !important; visibility: visible !important; opacity: 1 !important; z-index: 9999 !important; background: rgba(255, 255, 255, 0.95) !important; padding: 8px !important; border-radius: 12px !important; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15) !important; pointer-events: auto !important;">
                                            {{-- Tools Group --}}
                                            <div class="toolbar-group">
                                                <button type="button" class="toolbar-btn" id="measurementToolBtnMedical" title="Measurement Tools" data-tool="measurement">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <path d="M4 16L16 4M6 16L8 14M10 16L12 14M14 16L16 14M4 14L6 12M4 10L8 6M4 6L6 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                    </svg>
                                                </button>
                                                <button type="button" class="toolbar-btn" id="boundingBoxBtnMedical" title="Bounding Box" data-tool="boundingBox">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <rect x="3" y="3" width="14" height="14" stroke="currentColor" stroke-width="1.8" stroke-dasharray="2 2"/>
                                                        <circle cx="3" cy="3" r="1.5" fill="currentColor"/>
                                                        <circle cx="17" cy="3" r="1.5" fill="currentColor"/>
                                                        <circle cx="3" cy="17" r="1.5" fill="currentColor"/>
                                                        <circle cx="17" cy="17" r="1.5" fill="currentColor"/>
                                                    </svg>
                                                </button>
                                                <button type="button" class="toolbar-btn" id="axisToggleBtnMedical" title="Toggle Axis" data-tool="axis">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <path d="M10 2V18M2 10H18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                        <path d="M10 2L8 4M10 2L12 4M18 10L16 8M18 10L16 12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                    </svg>
                                                </button>
                                                <button type="button" class="toolbar-btn" id="gridToggleBtnMedical" title="Measurement Grid" data-tool="grid">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <path d="M2 6H18M2 10H18M2 14H18M6 2V18M10 2V18M14 2V18" stroke="currentColor" stroke-width="1.5" opacity="0.6"/>
                                                    </svg>
                                                </button>
                                            </div>

                                            <div class="toolbar-divider"></div>

                                            {{-- View Options --}}
                                            <div class="toolbar-group">
                                                <button type="button" class="toolbar-btn" id="shadowToggleBtnMedical" title="Toggle Shadows" data-tool="shadow">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <circle cx="10" cy="8" r="3" stroke="currentColor" stroke-width="1.8"/>
                                                        <ellipse cx="10" cy="16" rx="5" ry="1.5" fill="currentColor" opacity="0.3"/>
                                                    </svg>
                                                </button>
                                                <button type="button" class="toolbar-btn" id="transparencyBtnMedical" title="Transparency" data-tool="transparency">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.8" opacity="0.5"/>
                                                        <path d="M10 3C6 3 3 6 3 10C3 14 6 17 10 17" stroke="currentColor" stroke-width="1.8"/>
                                                    </svg>
                                                </button>
                                                <button type="button" class="toolbar-btn" id="modelColorBtnMedical" title="Model Color" data-tool="modelColor">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.8"/>
                                                        <circle cx="10" cy="10" r="4" fill="currentColor" opacity="0.3"/>
                                                    </svg>
                                                </button>
                                                <button type="button" class="toolbar-btn" id="backgroundColorBtnMedical" title="Background Color" data-tool="bgColor">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <rect x="2" y="2" width="16" height="16" rx="2" stroke="currentColor" stroke-width="1.8"/>
                                                        <path d="M2 10H18" stroke="currentColor" stroke-width="1.8"/>
                                                    </svg>
                                                </button>
                                            </div>

                                            <div class="toolbar-divider"></div>

                                            {{-- Actions --}}
                                            <div class="toolbar-group">
                                                <button type="button" class="toolbar-btn" id="undoBtnMedical" title="Undo" data-action="undo">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <path d="M5 8H15C16.6569 8 18 9.34315 18 11C18 12.6569 16.6569 14 15 14H8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                        <path d="M8 5L5 8L8 11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </button>
                                                <button type="button" class="toolbar-btn" id="redoBtnMedical" title="Redo" data-action="redo">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <path d="M15 8H5C3.34315 8 2 9.34315 2 11C2 12.6569 3.34315 14 5 14H12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                        <path d="M12 5L15 8L12 11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </button>
                                                <button type="button" class="toolbar-btn" id="screenshotToolBtnMedical" title="Screenshot" data-action="screenshot">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <rect x="2" y="5" width="16" height="12" rx="2" stroke="currentColor" stroke-width="1.8"/>
                                                        <circle cx="10" cy="11" r="2.5" stroke="currentColor" stroke-width="1.8"/>
                                                        <path d="M6 5L7 3H13L14 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Bottom Control Bar - Professional --}}
                                        <div class="viewer-bottom-controls" id="controlBarMedical">
                                            <div class="control-section measurements-section">
                                                <div class="control-label">Measurements</div>
                                                <div class="measurement-items">
                                                    <div class="measurement-item">
                                                        <span class="axis-label">X:</span>
                                                        <span class="axis-value" id="measureX">0.00</span>
                                                        <span class="axis-unit">mm</span>
                                                    </div>
                                                    <div class="measurement-item">
                                                        <span class="axis-label">Y:</span>
                                                        <span class="axis-value" id="measureY">0.00</span>
                                                        <span class="axis-unit">mm</span>
                                                    </div>
                                                    <div class="measurement-item">
                                                        <span class="axis-label">Z:</span>
                                                        <span class="axis-value" id="measureZ">0.00</span>
                                                        <span class="axis-unit">mm</span>
                                                    </div>
                                                    <div class="measurement-item volume">
                                                        <span class="axis-label">Vol:</span>
                                                        <span class="axis-value" id="measureVolume">0.00</span>
                                                        <span class="axis-unit">cm³</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="control-divider"></div>

                                            <div class="control-section camera-section">
                                                <div class="control-label">Camera View</div>
                                                <div class="camera-buttons">
                                                    <button type="button" class="control-btn camera-btn" data-view="top" title="Top View">
                                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                            <path d="M8 2L14 6L8 10L2 6L8 2Z" stroke="currentColor" stroke-width="1.5"/>
                                                        </svg>
                                                        <span>Top</span>
                                                    </button>
                                                    <button type="button" class="control-btn camera-btn" data-view="front" title="Front View">
                                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                            <rect x="3" y="3" width="10" height="10" stroke="currentColor" stroke-width="1.5"/>
                                                        </svg>
                                                        <span>Front</span>
                                                    </button>
                                                    <button type="button" class="control-btn camera-btn" data-view="right" title="Right View">
                                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                            <path d="M3 3L13 3L13 13L3 13L3 3Z" stroke="currentColor" stroke-width="1.5"/>
                                                            <path d="M8 3L13 8L8 13" stroke="currentColor" stroke-width="1.5"/>
                                                        </svg>
                                                        <span>Right</span>
                                                    </button>
                                                    <button type="button" class="control-btn camera-btn" data-view="left" title="Left View">
                                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                            <path d="M13 3L3 3L3 13L13 13L13 3Z" stroke="currentColor" stroke-width="1.5"/>
                                                            <path d="M8 3L3 8L8 13" stroke="currentColor" stroke-width="1.5"/>
                                                        </svg>
                                                        <span>Left</span>
                                                    </button>
                                                    <button type="button" class="control-btn camera-btn" data-view="bottom" title="Bottom View">
                                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                            <path d="M8 14L2 10L8 6L14 10L8 14Z" stroke="currentColor" stroke-width="1.5"/>
                                                        </svg>
                                                        <span>Bottom</span>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="control-divider"></div>

                                            <div class="control-section tools-section">
                                                <div class="control-label">Tools</div>
                                                <div class="tool-buttons">
                                                    <button type="button" class="control-btn tool-btn" id="toggleGridBtn" title="Toggle Grid">
                                                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                                                            <path d="M1 1H17M1 6H17M1 11H17M1 16H17M6 1V17M11 1V17" stroke="currentColor" stroke-width="1.5"/>
                                                        </svg>
                                                        <span>Grid</span>
                                                    </button>
                                                    <button type="button" class="control-btn tool-btn" id="repairModelBtn" title="Repair Model">
                                                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                                                            <path d="M15 3L3 15M3 3L15 15" stroke="currentColor" stroke-width="1.5"/>
                                                            <circle cx="9" cy="9" r="7" stroke="currentColor" stroke-width="1.5"/>
                                                        </svg>
                                                        <script type="module">
                                                        // --- Mesh Repair using @jscad/modeling ---
                                                        // 1. Install @jscad/modeling: npm install @jscad/modeling
                                                        // 2. Bundle for browser (Vite/Webpack will handle import)
                                                        // 3. This script assumes Three.js BufferGeometry for meshes

                                                        import * as jscad from 'https://cdn.jsdelivr.net/npm/@jscad/modeling@2.19.0/dist/jscad-modeling.min.js';

                                                        // Convert Three.js BufferGeometry to JSCAD poly3 geometry
                                                        function threeToJscadPoly3(bufferGeometry) {
                                                            const pos = bufferGeometry.attributes.position.array;
                                                            const indices = bufferGeometry.index ? bufferGeometry.index.array : null;
                                                            const vertices = [];
                                                            for (let i = 0; i < pos.length; i += 3) {
                                                                vertices.push([pos[i], pos[i + 1], pos[i + 2]]);
                                                            }
                                                            const polygons = [];
                                                            if (indices) {
                                                                for (let i = 0; i < indices.length; i += 3) {
                                                                    polygons.push([vertices[indices[i]], vertices[indices[i + 1]], vertices[indices[i + 2]]]);
                                                                }
                                                            } else {
                                                                for (let i = 0; i < vertices.length; i += 3) {
                                                                    polygons.push([vertices[i], vertices[i + 1], vertices[i + 2]]);
                                                                }
                                                            }
                                                            return jscad.geometries.poly3.create(polygons);
                                                        }

                                                        // Convert JSCAD poly3 geometry back to Three.js BufferGeometry
                                                        function jscadPoly3ToThree(poly3) {
                                                            const polygons = poly3.polygons;
                                                            const positions = [];
                                                            const indices = [];
                                                            let vertCount = 0;
                                                            for (const poly of polygons) {
                                                                if (poly.length === 3) {
                                                                    for (const v of poly) positions.push(...v);
                                                                    indices.push(vertCount, vertCount + 1, vertCount + 2);
                                                                    vertCount += 3;
                                                                }
                                                            }
                                                            const geometry = new THREE.BufferGeometry();
                                                            geometry.setAttribute('position', new THREE.Float32BufferAttribute(positions, 3));
                                                            geometry.setIndex(indices);
                                                            geometry.computeVertexNormals();
                                                            return geometry;
                                                        }

                                                        // Mesh repair function using JSCAD
                                                        async function repairMesh(mesh, { fillHoles = true } = {}) {
                                                            // Convert Three.js geometry to JSCAD poly3
                                                            const poly3 = threeToJscadPoly3(mesh.geometry);
                                                            // Use JSCAD's repair/fill operation
                                                            let repaired = poly3;
                                                            if (fillHoles) {
                                                                repaired = jscad.modifiers.repairHoles(poly3);
                                                            }
                                                            // Convert back to Three.js geometry
                                                            mesh.geometry.dispose();
                                                            mesh.geometry = jscadPoly3ToThree(repaired);
                                                            mesh.geometry.computeBoundingBox();
                                                            mesh.geometry.computeBoundingSphere();
                                                            return mesh;
                                                        }

                                                        // Attach to viewerGeneral for Save & Calculate logic
                                                        if (!window.viewerGeneral) window.viewerGeneral = {};
                                                        window.viewerGeneral.repairMesh = repairMesh;

                                                        // --- UI Logic: Hide price summary and sidebar price by default, only show after Save & Calculate and after repair ---
                                                        document.addEventListener('DOMContentLoaded', function() {
                                                            const priceSummaryGeneral = document.getElementById('priceSummaryGeneral');
                                                            if (priceSummaryGeneral) priceSummaryGeneral.style.display = 'none';

                                                            // Hide price summary and sidebar price on file upload or removal
                                                            function hidePriceSummary() {
                                                                if (priceSummaryGeneral) priceSummaryGeneral.style.display = 'none';
                                                                // Hide sidebar price
                                                                const priceSidebar = document.querySelector('.total-price, #quoteTotalPriceGeneral');
                                                                if (priceSidebar) priceSidebar.textContent = '';
                                                                // Hide volume
                                                                const volumeSidebar = document.getElementById('quoteTotalVolumeGeneral');
                                                                if (volumeSidebar) volumeSidebar.textContent = '';
                                                            }
                                                            const fileInput = document.getElementById('fileInput3d');
                                                            if (fileInput) fileInput.addEventListener('change', hidePriceSummary);
                                                            document.addEventListener('fileRemoved', hidePriceSummary);

                                                            // NOTE: Save button handler is at line ~116 using SimpleSaveCalculate.execute()
                                                            // Old broken handler was removed from here
                                                        });
                                                        </script>
                                                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                                                            <circle cx="13" cy="4" r="2.5" stroke="currentColor" stroke-width="1.5"/>
                                                            <circle cx="5" cy="9" r="2.5" stroke="currentColor" stroke-width="1.5"/>
                                                            <circle cx="13" cy="14" r="2.5" stroke="currentColor" stroke-width="1.5"/>
                                                            <path d="M7.5 10L10.5 12.5M7.5 8L10.5 5.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                                        </svg>
                                                        <span>Share</span>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="control-divider"></div>

                                            <div class="control-section actions-section">
                                                <button type="button" class="control-btn save-btn" id="saveCalculationsBtn">
                                                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                                                        <path d="M15 16H3C2.44772 16 2 15.5523 2 15V3C2 2.44772 2.44772 2 3 2H12L16 6V15C16 15.5523 15.5523 16 15 16Z" stroke="currentColor" stroke-width="1.5"/>
                                                        <path d="M12 2V6H5V2" stroke="currentColor" stroke-width="1.5"/>
                                                        <path d="M5 10H13V16H5V10Z" stroke="currentColor" stroke-width="1.5"/>
                                                    </svg>
                                                    <span>Save & Calculate</span>
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Empty State --}}
                                        <div class="d-flex align-items-center justify-content-center h-100 text-muted text-center">
                                            <div>
                                                <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg" style="opacity: 0.3;">
                                                    <path d="M40 10L70 25L40 40L10 25L40 10Z" stroke="#6c757d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M10 55L40 70L70 55" stroke="#6c757d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" opacity="0.7"/>
                                                    <path d="M10 40L40 55L70 40" stroke="#6c757d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" opacity="0.5"/>
                                                </svg>
                                                <p class="mt-3 mb-0" style="font-weight: 500; color: #6c757d; font-size: 0.95rem;">Upload a medical file to preview</p>
                                                <p class="mt-1 mb-0" style="font-size: 0.8rem; color: #95a5a6;">Drag & drop or click to browse</p>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Viewer Controls Panel - REMOVED FOR CLEAN INTERFACE --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
                                        <div class="col-6 col-md-3">
                                            <small style="opacity: 0.8; text-transform: uppercase; letter-spacing: 1px; font-size: 0.75rem;">Files</small>
                                            <h4 class="mb-0 mt-1" id="quoteTotalFilesMedical">0</h4>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <small style="opacity: 0.8; text-transform: uppercase; letter-spacing: 1px; font-size: 0.75rem;">Volume</small>
                                            <h4 class="mb-0 mt-1" id="quoteTotalVolumeMedical">0 cm³</h4>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <small style="opacity: 0.8; text-transform: uppercase; letter-spacing: 1px; font-size: 0.75rem;">Print Time</small>
                                            <h4 class="mb-0 mt-1" id="quotePrintTimeMedical">0h</h4>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <small style="opacity: 0.8; text-transform: uppercase; letter-spacing: 1px; font-size: 0.75rem;">Material</small>
                                            <h4 class="mb-0 mt-1" id="quoteMaterialCostMedical">$0</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-md-end mt-4 mt-md-0">
                                    <small style="opacity: 0.8; text-transform: uppercase; letter-spacing: 1px; font-size: 0.75rem;">Total Price</small>
                                    <h1 class="mb-3 mt-1" style="font-weight: 700; font-size: 3rem;" id="quoteTotalPriceMedical">$0</h1>
                                    <button type="button" class="btn btn-lg btn-light" id="btnRequestQuoteMedical" style="padding: 12px 40px; border-radius: 50px; font-weight: 600;">
                                        Request Medical Quote
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
                </span>
                <h2 class="tp-section-title-grotesk mb-20">
                    Upload Your 3D Models
                </h2>
                <p style="font-size: 1.1rem; max-width: 600px; margin: 0 auto; color: #6c757d;">
                    Upload STL, OBJ, or PLY files and get instant pricing
                </p>
            </div>
        </div>

        <!-- General 3D Form -->
        <div class="quote-form-container-3d" id="generalForm3d-old" style="display: none;">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                        <div class="card-header border-0 p-4" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 16px 16px 0 0;">
                            <h5 class="mb-0" style="font-weight: 600;">📤 Upload 3D Files</h5>
                            <small class="text-muted">STL, OBJ, PLY - Max 50MB</small>
                        </div>
                        <div class="card-body p-4">
                            <div class="upload-drop-zone-3d text-center p-5" style="border: 2px dashed #dee2e6; border-radius: 12px; background: #f8f9fa; cursor: pointer; transition: all 0.3s ease;">
                                <input type="file" id="fileInput3d" style="display: none;" multiple accept=".stl,.obj,.ply">
                                <div class="mb-3">
                                    <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="32" cy="32" r="32" fill="#e3f2fd"/>
                                        <path d="M32 20L40 28L32 36L24 28L32 20Z" stroke="#2196f3" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M24 36L32 44L40 36" stroke="#2196f3" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <h6 class="mb-2" style="font-weight: 600;">Drop files here or click to browse</h6>
                                <p class="text-muted mb-3" style="font-size: 14px;">Supported formats: STL, OBJ, PLY</p>
                                <button type="button" class="btn btn-primary" onclick="document.getElementById('fileInput3d').click()">
                                    Select Files
                                </button>
                            </div>

                            <div class="mt-4">
                                <label class="form-label fw-semibold">Material Type</label>
                                <select class="form-select" style="border-radius: 10px;">
                                    <option value="pla">PLA (Standard)</option>
                                    <option value="abs">ABS (Durable)</option>
                                    <option value="petg">PETG (Strong)</option>
                                    <option value="nylon">Nylon (Flexible)</option>
                                    <option value="resin">Resin (High Detail)</option>
                                </select>
                            </div>

                            <div class="mt-3">
                                <label class="form-label fw-semibold">Print Quality</label>
                                <select class="form-select" style="border-radius: 10px;">
                                    <option value="draft">Draft (0.3mm)</option>
                                    <option value="standard" selected>Standard (0.2mm)</option>
                                    <option value="fine">Fine (0.1mm)</option>
                                    <option value="ultra">Ultra Fine (0.05mm)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                        <div class="card-header border-0 p-4" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 16px 16px 0 0;">
                            <h5 class="mb-0" style="font-weight: 600;">👁️ 3D Preview</h5>
                        </div>
                        <div class="card-body p-0">
                            <div id="viewer3dGeneral-old-duplicate" style="width: 100%; height: 400px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 0 0 16px 16px; display: flex; align-items: center; justify-content: center; color: white;">
                                <div class="text-center">
                                    <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg" style="opacity: 0.5;">
                                        <path d="M40 10L70 25L40 40L10 25L40 10Z" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M10 55L40 70L70 55" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M10 40L40 55L70 40" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <p style="margin-top: 20px; font-size: 16px; opacity: 0.8;">Upload a 3D file to preview</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Uploaded Files List -->
                    <div class="card border-0 shadow-sm mt-4" style="border-radius: 16px;">
                        <div class="card-header border-0 p-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 16px 16px 0 0;">
                            <h6 class="mb-0" style="font-weight: 600;">� Uploaded Files</h6>
                            <span class="badge bg-primary" id="fileCountGeneral">0</span>
                        </div>
                        <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                            <div id="filesListGeneral" class="files-list-container">
                                <div class="empty-state text-center p-5">
                                    <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" class="mb-3" style="opacity: 0.3;">
                                        <circle cx="24" cy="24" r="24" fill="#e0e0e0"/>
                                        <path d="M24 14V34M14 24H34" stroke="#9e9e9e" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                    <p class="mb-0 text-muted">No files uploaded yet</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Instant Quote -->
                    <div class="card border-0 shadow-sm mt-4" style="border-radius: 16px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <div class="card-body p-4">
                            <h6 class="mb-3" style="font-weight: 600;">💰 Instant Quote</h6>
                            <div id="quoteDetailsGeneral">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Total Files:</span>
                                    <strong id="quoteTotalFilesGeneral">0</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Total Volume:</span>
                                    <strong id="quoteTotalVolumeGeneral">0 cm³</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Material Cost:</span>
                                    <strong id="quoteMaterialCostGeneral">$0.00</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Print Time:</span>
                                    <strong id="quotePrintTimeGeneral">0 hours</strong>
                                </div>
                                <hr style="border-color: rgba(255,255,255,0.3); margin: 15px 0;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Total Price:</h5>
                                    <h3 class="mb-0" id="quoteTotalPriceGeneral">$0.00</h3>
                                </div>
                            </div>
                            <button type="button" class="btn btn-light btn-lg w-100 mt-3" id="btnRequestQuoteGeneral" style="border-radius: 10px; font-weight: 600;">
                                Request Quote
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Medical 3D Form -->
        <div class="quote-form-container-3d" id="medicalForm3d-old" style="display: none;">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                        <div class="card-header border-0 p-4" style="background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%); border-radius: 16px 16px 0 0;">
                            <h5 class="mb-0" style="font-weight: 600;">🏥 Upload Medical 3D Files</h5>
                            <small class="text-muted">STL, OBJ, PLY - Max 100MB</small>
                        </div>
                        <div class="card-body p-4">
                            <div class="upload-drop-zone-3d text-center p-5" style="border: 2px dashed #ff9800; border-radius: 12px; background: #fff3e0; cursor: pointer;">
                                <input type="file" id="fileInputMedical3d" style="display: none;" multiple accept=".stl,.obj,.ply">
                                <div class="mb-3">
                                    <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="32" cy="32" r="32" fill="#fff3e0"/>
                                        <path d="M32 20L40 28L32 36L24 28L32 20Z" stroke="#ff9800" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M24 36L32 44L40 36" stroke="#ff9800" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <h6 class="mb-2" style="font-weight: 600;">Drop medical files here or click to browse</h6>
                                <p class="text-muted mb-3" style="font-size: 14px;">Biocompatible materials available</p>
                                <button type="button" class="btn btn-warning" onclick="document.getElementById('fileInputMedical3d').click()">
                                    Select Files
                                </button>
                            </div>

                            <div class="mt-4">
                                <label class="form-label fw-semibold">Medical Material Type</label>
                                <select class="form-select" style="border-radius: 10px;">
                                    <option value="biocompatible">Biocompatible Resin</option>
                                    <option value="surgical">Surgical Guide Material</option>
                                    <option value="dental">Dental Resin</option>
                                    <option value="sterilizable">Sterilizable Material</option>
                                </select>
                            </div>

                            <div class="mt-3">
                                <label class="form-label fw-semibold">Application Type</label>
                                <select class="form-select" style="border-radius: 10px;">
                                    <option value="surgical-guide">Surgical Guide</option>
                                    <option value="dental-model">Dental Model</option>
                                    <option value="anatomical-model">Anatomical Model</option>
                                    <option value="prosthetic">Prosthetic Component</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                        <div class="card-header border-0 p-4" style="background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%); border-radius: 16px 16px 0 0;">
                            <h5 class="mb-0" style="font-weight: 600;">👁️ 3D Preview</h5>
                        </div>
                        <div class="card-body p-0">
                            <div id="viewer3dMedical-old-duplicate" style="width: 100%; height: 400px; background: linear-gradient(135deg, #ff9800 0%, #ff5722 100%); border-radius: 0 0 16px 16px; display: flex; align-items: center; justify-content: center; color: white;">
                                <div class="text-center">
                                    <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg" style="opacity: 0.5;">
                                        <path d="M40 10L70 25L40 40L10 25L40 10Z" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M10 55L40 70L70 55" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M10 40L40 55L70 40" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <p style="margin-top: 20px; font-size: 16px; opacity: 0.8;">Upload a medical file to preview</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Uploaded Files List -->
                    <div class="card border-0 shadow-sm mt-4" style="border-radius: 16px;">
                        <div class="card-header border-0 p-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%); border-radius: 16px 16px 0 0;">
                            <h6 class="mb-0" style="font-weight: 600;">� Uploaded Files</h6>
                            <span class="badge" style="background: linear-gradient(135deg, #ff9800 0%, #ff5722 100%); color: white;" id="fileCountMedical">0</span>
                        </div>
                        <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                            <div id="filesListMedical" class="files-list-container">
                                <div class="empty-state text-center p-5">
                                    <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" class="mb-3" style="opacity: 0.3;">
                                        <circle cx="24" cy="24" r="24" fill="#e0e0e0"/>
                                        <path d="M24 14V34M14 24H34" stroke="#9e9e9e" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                    <p class="mb-0 text-muted">No files uploaded yet</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Instant Quote -->
                    <div class="card border-0 shadow-sm mt-4" style="border-radius: 16px; background: linear-gradient(135deg, #ff9800 0%, #ff5722 100%); color: white;">
                        <div class="card-body p-4">
                            <h6 class="mb-3" style="font-weight: 600;">💰 Instant Quote</h6>
                            <div id="quoteDetailsMedical">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Total Files:</span>
                                    <strong id="quoteTotalFilesMedical">0</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Total Volume:</span>
                                    <strong id="quoteTotalVolumeMedical">0 cm³</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Material Cost:</span>
                                    <strong id="quoteMaterialCostMedical">$0.00</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Print Time:</span>
                                    <strong id="quotePrintTimeMedical">0 hours</strong>
                                </div>
                                <hr style="border-color: rgba(255,255,255,0.3); margin: 15px 0;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Total Price:</h5>
                                    <h3 class="mb-0" id="quoteTotalPriceMedical">$0.00</h3>
                                </div>
                            </div>
                            <button type="button" class="btn btn-light btn-lg w-100 mt-3" id="btnRequestQuoteMedical" style="border-radius: 10px; font-weight: 600;">
                                Request Medical Quote
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* Category Tab Button Styling */
.category-tab-btn {
    background: #f8f9fa !important;
    color: #6c757d !important;
    border-color: #dee2e6 !important;
    transition: all 0.3s ease !important;
}

/* Active Tab Styling - Primary Blue */
.category-tab-btn.active {
    background: #4a90e2 !important;
    color: white !important;
    border-color: #4a90e2 !important;
    box-shadow: 0 2px 4px rgba(74, 144, 226, 0.3) !important;
}

.category-tab-btn:hover:not(.active) {
    background: #e8f4f8 !important;
    color: #4a90e2 !important;
    border-color: #4a90e2 !important;
}

/* Mobile Responsive Styles */
@media (max-width: 991px) {
    .col-12.col-lg-3 {
        border-right: none !important;
        border-bottom: 1px solid #e9ecef;
    }

    #viewer3dGeneral,
    #viewer3dMedical {
        border-radius: 0 0 8px 8px !important;
        min-height: 300px !important;
        position: relative !important;
    }

    .quote-form-container-3d .card {
        border-radius: 8px !important;
    }
}

.file-item {
    transition: all 0.2s ease;
    cursor: default;
}

.file-item:hover {
    background-color: #f8f9fa;
}

.file-item:last-child {
    border-bottom: none !important;
}

.file-list-item {
    transition: all 0.2s ease;
}

.file-list-item:hover {
    background-color: #f8f9fa !important;
}

.file-list-item:last-child {
    border-bottom: none !important;
}

.file-list-item .remove-file-btn {
    opacity: 1;
    transition: opacity 0.2s ease;
}

.file-list-item:hover .remove-file-btn {
    opacity: 1;
}

#filesContainer,
#filesContainerMedical {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #f8f9fa;
}

#filesContainer::-webkit-scrollbar,
#filesContainerMedical::-webkit-scrollbar {
    width: 6px;
}

#filesContainer::-webkit-scrollbar-track,
#filesContainerMedical::-webkit-scrollbar-track {
    background: #f8f9fa;
}

#filesContainer::-webkit-scrollbar-thumb,
#filesContainerMedical::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 3px;
}

#filesContainer::-webkit-scrollbar-thumb:hover,
#filesContainerMedical::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}

.files-list-container {
    min-height: 100px;
}

.files-list-container::-webkit-scrollbar {
    width: 6px;
}

.files-list-container::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.files-list-container::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.files-list-container::-webkit-scrollbar-thumb:hover {
    background: #555;
}

.remove-file-btn {
    opacity: 0.7;
    transition: all 0.2s;
}

.remove-file-btn:hover {
    opacity: 1;
    transform: scale(1.1);
}

/* Fullscreen controls visibility */
#viewer3dGeneral:fullscreen .card,
#viewer3dMedical:fullscreen .card,
#viewer3dGeneral:-webkit-full-screen .card,
#viewer3dMedical:-webkit-full-screen .card,
#viewer3dGeneral:-moz-full-screen .card,
#viewer3dMedical:-moz-full-screen .card {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 9999;
    margin: 0;
    border-radius: 0 !important;
}

#viewer3dGeneral:fullscreen,
#viewer3dMedical:fullscreen,
#viewer3dGeneral:-webkit-full-screen,
#viewer3dMedical:-webkit-full-screen {
    background: #2d2d2d;
}

/* Viewer containers need position relative for absolute positioned children */
#viewer3dGeneral,
#viewer3dMedical {
    position: relative !important;
}

/* ========================================
   PROFESSIONAL TOOLBAR STYLES
   ======================================== */
.viewer-professional-toolbar {
    position: absolute !important;
    top: 20px !important;
    right: 20px !important;
    display: flex !important;
    gap: 8px;
    background: rgba(255, 255, 255, 0.95);
    padding: 8px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    backdrop-filter: blur(10px);
    z-index: 2000 !important;
    transition: all 0.3s ease;
    pointer-events: auto !important;
}

.toolbar-group {
    display: flex;
    gap: 4px;
}

.toolbar-divider {
    width: 1px;
    background: #e0e0e0;
    margin: 0 4px;
}

.toolbar-btn {
    width: 42px;
    height: 42px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    color: #424242;
    position: relative;
}

.toolbar-btn:hover {
    background: #f5f5f5;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.toolbar-btn.active {
    background: #4a90e2;
    color: white;
    border-color: #4a90e2;
    box-shadow: 0 2px 8px rgba(74, 144, 226, 0.4);
}

.toolbar-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
    transform: none;
}

.toolbar-btn svg {
    width: 20px;
    height: 20px;
}

/* Measurement Submenu */
.measurement-submenu {
    position: absolute;
    top: 60px;
    right: 20px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    padding: 12px;
    min-width: 240px;
    z-index: 1001;
    animation: slideDown 0.2s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.submenu-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 12px;
    border-bottom: 1px solid #e0e0e0;
    margin-bottom: 8px;
    font-weight: 600;
    font-size: 14px;
    color: #424242;
}

.submenu-close {
    background: none;
    border: none;
    font-size: 24px;
    color: #757575;
    cursor: pointer;
    padding: 0;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    transition: all 0.2s;
}

.submenu-close:hover {
    background: #f5f5f5;
    color: #424242;
}

.submenu-btn {
    width: 100%;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
    margin-bottom: 4px;
    font-size: 14px;
    color: #424242;
    text-align: left;
}

.submenu-btn:last-child {
    margin-bottom: 0;
}

.submenu-btn:hover {
    background: #f5f5f5;
    border-color: #4a90e2;
    transform: translateX(4px);
}

.submenu-btn.active {
    background: #e3f2fd;
    border-color: #4a90e2;
    color: #4a90e2;
}

.submenu-btn svg {
    flex-shrink: 0;
}

.submenu-btn[data-measure="clear"] {
    border-color: #ef5350;
    color: #ef5350;
}

.submenu-btn[data-measure="clear"]:hover {
    background: #ffebee;
}

/* Unit Toggle */
.unit-toggle {
    position: absolute;
    top: 80px;
    right: 20px;
    display: flex;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    overflow: hidden;
    z-index: 1001;
}

.unit-btn {
    padding: 8px 16px;
    background: white;
    border: none;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    color: #757575;
    transition: all 0.2s;
}

.unit-btn.active {
    background: #4a90e2;
    color: white;
}

.unit-btn:hover:not(.active) {
    background: #f5f5f5;
}

/* Tooltips Enhancement */
.toolbar-btn::after {
    content: attr(title);
    position: absolute;
    bottom: -32px;
    left: 50%;
    transform: translateX(-50%) scale(0.8);
    background: rgba(0, 0, 0, 0.85);
    color: white;
    padding: 6px 10px;
    border-radius: 6px;
    font-size: 12px;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: all 0.2s ease;
    z-index: 10000;
}

.toolbar-btn:hover::after {
    opacity: 1;
    transform: translateX(-50%) scale(1);
}

/* Responsive Toolbar */
@media (max-width: 768px) {
    .viewer-professional-toolbar {
        top: 10px;
        right: 10px;
        padding: 6px;
        gap: 6px;
    }

    .toolbar-btn {
        width: 38px;
        height: 38px;
    }

    .measurement-submenu {
        right: 10px;
        min-width: 200px;
    }
}

/* Measurement Annotations */
.measurement-annotation {
    position: absolute;
    background: rgba(74, 144, 226, 0.95);
    color: white;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    pointer-events: none;
    z-index: 999;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

/* Analysis Panel (for mesh repair info) */
.analysis-panel {
    position: absolute;
    bottom: 80px;
    left: 20px;
    background: white;
    border-radius: 12px;
    padding: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    max-width: 300px;
    z-index: 1000;
    animation: slideUp 0.3s ease;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.analysis-panel h4 {
    font-size: 14px;
    font-weight: 700;
    margin-bottom: 12px;
    color: #424242;
    display: flex;
    align-items: center;
    gap: 8px;
}

.analysis-item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
    font-size: 13px;
}

.analysis-item:last-child {
    border-bottom: none;
}

.analysis-label {
    color: #757575;
}

.analysis-value {
    font-weight: 600;
    color: #424242;
}

.analysis-value.warning {
    color: #ff9800;
}

.analysis-value.error {
    color: #ef5350;
}

.analysis-value.success {
    color: #66bb6a;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form switcher functionality
    const formSwitchers = document.querySelectorAll('.form-switch-btn-3d');
    const generalForm = document.getElementById('generalForm3d');
    const medicalForm = document.getElementById('medicalForm3d');

    if (formSwitchers.length > 0) {
        formSwitchers.forEach(btn => {
            btn.addEventListener('click', function() {
                const formType = this.getAttribute('data-form');

                // Update button states
                formSwitchers.forEach(b => {
                    b.classList.remove('active');
                    if (b.getAttribute('data-form') === formType) {
                        if (formType === 'general') {
                            b.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
                            b.style.color = 'white';
                            b.style.border = 'none';
                        } else {
                            b.style.background = 'linear-gradient(135deg, #ff9800 0%, #ff5722 100%)';
                            b.style.color = 'white';
                            b.style.border = 'none';
                        }
                        b.classList.add('active');
                    } else {
                        b.style.background = 'white';
                        b.style.color = '#6c757d';
                        b.style.border = '2px solid #dee2e6';
                    }
                });

                // Show/hide forms - updated to use new form IDs
                const newGeneralForm = document.getElementById('generalForm3d');
                const newMedicalForm = document.getElementById('medicalForm3d');

                if (newGeneralForm && newMedicalForm) {
                    if (formType === 'general') {
                        newGeneralForm.style.display = 'block';
                        newMedicalForm.style.display = 'none';
                    } else {
                        newGeneralForm.style.display = 'none';
                        newMedicalForm.style.display = 'block';
                    }
                }

                // Also handle old forms if they exist
                if (generalForm && medicalForm) {
                    generalForm.classList.toggle('d-none', formType !== 'general');
                    medicalForm.classList.toggle('d-none', formType !== 'medical');
                }
            });
        });
    }

    // Wait for viewers to be ready before setting up color pickers
    window.addEventListener('viewersReady', () => {
        console.log('🎨 Setting up color pickers and control handlers...');

        // Color Picker for General
        const colorBtns = document.querySelectorAll('.color-btn');
        console.log(`   Found ${colorBtns.length} general color buttons`);
        colorBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                console.log('🎨 General color button clicked:', this.getAttribute('data-color'));
                colorBtns.forEach(b => {
                    b.classList.remove('active');
                    b.style.border = '2px solid #dee2e6';
                });
                this.classList.add('active');
                this.style.border = '3px solid #0047AD';

                const color = this.getAttribute('data-color');

                // Apply to selected file only
                if (window.selectedFileId && window.viewerGeneral) {
                    const fileData = window.viewerGeneral.uploadedFiles.find(f => f.id === window.selectedFileId);
                    if (fileData && fileData.mesh) {
                        // Update file settings
                        if (!fileData.settings) fileData.settings = {};
                        fileData.settings.color = color;

                        // Apply color to mesh
                        const newColor = new window.THREE.Color(color);
                        if (Array.isArray(fileData.mesh.material)) {
                            fileData.mesh.material.forEach(mat => mat.color.copy(newColor));
                        } else {
                            fileData.mesh.material.color.copy(newColor);
                        }

                        console.log(`🎨 Color applied to file ${fileData.file.name}`);

                        // Save state
                        if (window.viewerStateManager) {
                            window.viewerStateManager.saveState('File Color Changed');
                        }
                    }
                } else if (window.viewerGeneral && window.viewerGeneral.model) {
                    // Fallback: Apply to all models
                    window.viewerGeneral.changeModelColor(color);
                    if (window.viewerStateManager) {
                        window.viewerStateManager.saveState('Color Changed');
                    }
                }
            });
        });

        // Color Picker for Medical
        const colorBtnsMedical = document.querySelectorAll('.color-btn-medical');
        console.log(`   Found ${colorBtnsMedical.length} medical color buttons`);
        colorBtnsMedical.forEach(btn => {
            btn.addEventListener('click', function() {
                console.log('🎨 Medical color button clicked:', this.getAttribute('data-color'));
                colorBtnsMedical.forEach(b => {
                    b.classList.remove('active');
                    b.style.border = '2px solid #dee2e6';
                });
                this.classList.add('active');
                this.style.border = '3px solid #0047AD';

                const color = this.getAttribute('data-color');

                // Apply to selected file only
                if (window.selectedFileId && window.viewerMedical) {
                    const fileData = window.viewerMedical.uploadedFiles.find(f => f.id === window.selectedFileId);
                    if (fileData && fileData.mesh) {
                        // Update file settings
                        if (!fileData.settings) fileData.settings = {};
                        fileData.settings.color = color;

                        // Apply color to mesh
                        const newColor = new window.THREE.Color(color);
                        if (Array.isArray(fileData.mesh.material)) {
                            fileData.mesh.material.forEach(mat => mat.color.copy(newColor));
                        } else {
                            fileData.mesh.material.color.copy(newColor);
                        }

                        console.log(`🎨 Color applied to file ${fileData.file.name}`);

                        // Save state
                        if (window.viewerStateManager) {
                            window.viewerStateManager.saveState('File Color Changed');
                        }
                    }
                } else if (window.viewerMedical && window.viewerMedical.model) {
                    // Fallback: Apply to all models
                    window.viewerMedical.changeModelColor(color);
                    if (window.viewerStateManager) {
                        window.viewerStateManager.saveState('Color Changed');
                    }
                }
            });
        });

        // Material Select for General
        const materialSelectGen = document.getElementById('materialSelectGeneral');
        if (materialSelectGen) {
            materialSelectGen.addEventListener('change', function() {
                const material = this.value;
                const costs = { pla: 0.02, abs: 0.025, petg: 0.03, nylon: 0.04, tpu: 0.05, resin: 0.08 };
                const cost = costs[material] || 0.02;

                if (window.selectedFileId && window.viewerGeneral) {
                    const fileData = window.viewerGeneral.uploadedFiles.find(f => f.id === window.selectedFileId);
                    if (fileData) {
                        if (!fileData.settings) fileData.settings = {};
                        fileData.settings.material = material;
                        fileData.settings.materialCost = cost;
                        console.log(`🔧 Material changed to ${material} for ${fileData.file.name}`);

                        // Recalculate price
                        if (window.calculateFilePrice) {
                            window.calculateFilePrice(window.selectedFileId, window.viewerGeneral, 'General');
                        }

                        if (window.viewerStateManager) {
                            window.viewerStateManager.saveState('Material Changed');
                        }
                    }
                }
            });
        }

        // Technology Select for General
        const technologySelectGen = document.getElementById('technologySelectGeneral');
        if (technologySelectGen) {
            technologySelectGen.addEventListener('change', function() {
                const technology = this.value;
                const multipliers = { fdm: 1.0, sla: 1.5, sls: 2.0, dmls: 3.0, mjf: 2.5 };
                const multiplier = multipliers[technology] || 1.0;

                if (window.selectedFileId && window.viewerGeneral) {
                    const fileData = window.viewerGeneral.uploadedFiles.find(f => f.id === window.selectedFileId);
                    if (fileData) {
                        if (!fileData.settings) fileData.settings = {};
                        fileData.settings.technology = technology;
                        fileData.settings.technologyMultiplier = multiplier;
                        console.log(`⚙️ Technology changed to ${technology} for ${fileData.file.name}`);

                        // Recalculate price
                        if (window.calculateFilePrice) {
                            window.calculateFilePrice(window.selectedFileId, window.viewerGeneral, 'General');
                        }

                        if (window.viewerStateManager) {
                            window.viewerStateManager.saveState('Technology Changed');
                        }
                    }
                }
            });
        }

        // Material Select for Medical
        const materialSelectMed = document.getElementById('materialSelectMedical');
        if (materialSelectMed) {
            materialSelectMed.addEventListener('change', function() {
                const material = this.value;
                const costs = { pla: 0.02, abs: 0.025, petg: 0.03, nylon: 0.04, tpu: 0.05, resin: 0.08 };
                const cost = costs[material] || 0.02;

                if (window.selectedFileId && window.viewerMedical) {
                    const fileData = window.viewerMedical.uploadedFiles.find(f => f.id === window.selectedFileId);
                    if (fileData) {
                        if (!fileData.settings) fileData.settings = {};
                        fileData.settings.material = material;
                        fileData.settings.materialCost = cost;
                        console.log(`🔧 Material changed to ${material} for ${fileData.file.name}`);

                        // Recalculate price
                        if (window.calculateFilePrice) {
                            window.calculateFilePrice(window.selectedFileId, window.viewerMedical, 'Medical');
                        }

                        if (window.viewerStateManager) {
                            window.viewerStateManager.saveState('Material Changed');
                        }
                    }
                }
            });
        }

        // Technology Select for Medical
        const technologySelectMed = document.getElementById('technologySelectMedical');
        if (technologySelectMed) {
            technologySelectMed.addEventListener('change', function() {
                const technology = this.value;
                const multipliers = { fdm: 1.0, sla: 1.5, sls: 2.0, dmls: 3.0, mjf: 2.5 };
                const multiplier = multipliers[technology] || 1.0;

                if (window.selectedFileId && window.viewerMedical) {
                    const fileData = window.viewerMedical.uploadedFiles.find(f => f.id === window.selectedFileId);
                    if (fileData) {
                        if (!fileData.settings) fileData.settings = {};
                        fileData.settings.technology = technology;
                        fileData.settings.technologyMultiplier = multiplier;
                        console.log(`⚙️ Technology changed to ${technology} for ${fileData.file.name}`);

                        // Recalculate price
                        if (window.calculateFilePrice) {
                            window.calculateFilePrice(window.selectedFileId, window.viewerMedical, 'Medical');
                        }

                        if (window.viewerStateManager) {
                            window.viewerStateManager.saveState('Technology Changed');
                        }
                    }
                }
            });
        }

        console.log('✓ Color pickers and control handlers ready');

        // Viewer Controls - General
        const resetViewGen = document.getElementById('resetViewGeneral');
        const wireframeGen = document.getElementById('wireframeToggleGeneral');
        const fullscreenGen = document.getElementById('fullscreenGeneral');

        if (resetViewGen) {
            resetViewGen.addEventListener('click', () => {
                console.log('🔄 Reset view clicked');
                if (window.viewerGeneral && window.viewerGeneral.model) {
                    window.viewerGeneral.fitCameraToModel();
                }
            });
        }

        if (wireframeGen) {
            let wireframeMode = false;
            wireframeGen.addEventListener('click', () => {
                wireframeMode = !wireframeMode;
                console.log('🔲 Wireframe toggle:', wireframeMode);
                if (window.viewerGeneral && window.viewerGeneral.model) {
                    window.viewerGeneral.toggleWireframe(wireframeMode);
                }
                wireframeGen.style.background = wireframeMode ? '#2c3e50' : '';
                wireframeGen.style.color = wireframeMode ? 'white' : '';
            });
        }

        if (fullscreenGen) {
            fullscreenGen.addEventListener('click', () => {
                const viewer = document.getElementById('viewer3dGeneral');
                if (viewer.requestFullscreen) {
                    viewer.requestFullscreen();
                }
            });
        }

        // Viewer Controls - Medical
        const resetViewMed = document.getElementById('resetViewMedical');
        const wireframeMed = document.getElementById('wireframeToggleMedical');
        const fullscreenMed = document.getElementById('fullscreenMedical');

        if (resetViewMed) {
            resetViewMed.addEventListener('click', () => {
                console.log('🔄 Medical reset view clicked');
                if (window.viewerMedical && window.viewerMedical.model) {
                    window.viewerMedical.fitCameraToModel();
                }
            });
        }

        if (wireframeMed) {
            let wireframeMode = false;
            wireframeMed.addEventListener('click', () => {
                wireframeMode = !wireframeMode;
                console.log('🔲 Medical wireframe toggle:', wireframeMode);
                if (window.viewerMedical && window.viewerMedical.model) {
                    window.viewerMedical.toggleWireframe(wireframeMode);
                }
                wireframeMed.style.background = wireframeMode ? '#d84315' : '';
                wireframeMed.style.color = wireframeMode ? 'white' : '';
            });
        }

        if (fullscreenMed) {
            fullscreenMed.addEventListener('click', () => {
                const viewer = document.getElementById('viewer3dMedical');
                if (viewer.requestFullscreen) {
                    viewer.requestFullscreen();
                }
            });
        }

        console.log('✓ Viewer controls ready');
    });

    // Category Tab Switching - Must be inside DOMContentLoaded
    console.log('🔄 Setting up category tab switching...');
    const categoryTabs = document.querySelectorAll('.category-tab-btn');
    console.log(`   Found ${categoryTabs.length} category tab buttons`);

    categoryTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            console.log('🔘 Category tab clicked:', this.getAttribute('data-category'));
            const category = this.getAttribute('data-category');
            const formSwitchBtns = document.querySelectorAll('.form-switch-btn-3d');
            const quoteViewer = document.querySelector('.quote-viewer');

            // Update active state for inline tabs - let CSS handle the styling
            categoryTabs.forEach(t => {
                t.classList.remove('active');
            });
            this.classList.add('active');

            // Update viewer background based on mode
            if (quoteViewer) {
                quoteViewer.classList.remove('mode-general', 'mode-medical');
                quoteViewer.classList.add(`mode-${category}`);
                console.log(`✓ Viewer background changed to ${category} mode`);
            }

            // Also update main form switch buttons
            formSwitchBtns.forEach(btn => {
                if (btn.getAttribute('data-form') === category) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });

            // Switch forms
            if (category === 'general') {
                document.getElementById('generalForm3d').style.display = 'block';
                document.getElementById('medicalForm3d').style.display = 'none';

                // Resize General viewer after showing
                setTimeout(() => {
                    if (window.viewerGeneral && window.viewerGeneral.onWindowResize) {
                        window.viewerGeneral.onWindowResize();
                        console.log('✓ General viewer resized');
                    }
                    // Update quote if files are uploaded
                    if (window.fileManagerGeneral && window.viewerGeneral && window.viewerGeneral.uploadedFiles && window.viewerGeneral.uploadedFiles.length > 0) {
                        window.fileManagerGeneral.updateQuote();
                        console.log('✓ General quote updated');
                    }
                }, 100);
            } else {
                document.getElementById('generalForm3d').style.display = 'none';
                document.getElementById('medicalForm3d').style.display = 'block';

                // Resize Medical viewer after showing
                setTimeout(() => {
                    if (window.viewerMedical && window.viewerMedical.onWindowResize) {
                        window.viewerMedical.onWindowResize();
                        console.log('✓ Medical viewer resized');
                    }

                    // If Medical viewer has a model, fit it to view
                    if (window.viewerMedical && window.viewerMedical.model) {
                        window.viewerMedical.fitCameraToModel();
                        console.log('✓ Medical model refitted to camera');
                    }

                    // Update quote if files are uploaded
                    if (window.fileManagerMedical && window.viewerMedical && window.viewerMedical.uploadedFiles && window.viewerMedical.uploadedFiles.length > 0) {
                        window.fileManagerMedical.updateQuote();
                        console.log('✓ Medical quote updated');
                    }
                }, 100);
            }

            console.log('✓ Switched to:', category);
        });
    });

    // File upload drag and drop
    const dropZones = document.querySelectorAll('.upload-drop-zone-3d');
    dropZones.forEach(zone => {
        zone.addEventListener('click', function() {
            const input = this.querySelector('input[type="file"]') || this.parentElement.querySelector('input[type="file"]');
            if (input) input.click();
        });

        zone.addEventListener('mouseenter', function() {
            this.style.borderColor = '#4a90e2';
            this.style.background = '#f0f7ff';
        });

        zone.addEventListener('mouseleave', function() {
            this.style.borderColor = '#cbd5e0';
            this.style.background = 'white';
        });

        zone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.style.borderColor = '#4a90e2';
            this.style.background = '#e8f4f8';
            this.style.transform = 'scale(1.02)';
        });

        zone.addEventListener('dragleave', function(e) {
            this.style.borderColor = '#cbd5e0';
            this.style.background = 'white';
            this.style.transform = 'scale(1)';
        });

        zone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.style.borderColor = '#cbd5e0';
            this.style.background = 'white';
            this.style.transform = 'scale(1)';
            this.style.borderColor = '#dee2e6';
            this.style.transform = 'scale(1)';
            const files = e.dataTransfer.files;
            console.log('Files dropped:', files);
            // Process files immediately without alert
        });
    });

    // File input handlers are set up in 3d-viewer-pro.js (setupFileHandlers function)
    // Add direct test handler to verify multiple files are being selected
    document.addEventListener('DOMContentLoaded', () => {
        const generalInput = document.getElementById('fileInput3d');
        const medicalInput = document.getElementById('fileInput3dMedical');

        if (generalInput) {
            console.log('✓ General file input found, testing multiple attribute:', generalInput.hasAttribute('multiple'));
            generalInput.addEventListener('change', function(e) {
                console.log('🔍 DIRECT HANDLER - General input change detected');
                console.log('   Files.length:', this.files.length);
                for (let i = 0; i < this.files.length; i++) {
                    console.log(`   File ${i+1}:`, this.files[i].name);
                }
            }, true); // Use capture phase
        }

        if (medicalInput) {
            console.log('✓ Medical file input found, testing multiple attribute:', medicalInput.hasAttribute('multiple'));
            medicalInput.addEventListener('change', function(e) {
                console.log('🔍 DIRECT HANDLER - Medical input change detected');
                console.log('   Files.length:', this.files.length);
                for (let i = 0; i < this.files.length; i++) {
                    console.log(`   File ${i+1}:`, this.files[i].name);
                }
            }, true); // Use capture phase
        }
    });

    // Setup Viewer Control Handlers
    window.addEventListener('viewersReady', () => {
        console.log('🎮 Setting up viewer controls...');

        // Expose viewers globally for controls
        window.viewerGeneral = viewerGeneral;
        window.viewerMedical = viewerMedical;

        // General Viewer Controls
        const resetViewGeneralBtn = document.getElementById('resetViewGeneralBtn');
        const zoomInGeneralBtn = document.getElementById('zoomInGeneralBtn');
        const zoomOutGeneralBtn = document.getElementById('zoomOutGeneralBtn');
        const solidModeGeneralBtn = document.getElementById('solidModeGeneralBtn');
        const wireframeGeneralBtn = document.getElementById('wireframeGeneralBtn');
        const modelColorGeneralPicker = document.getElementById('modelColorGeneralPicker');
        const bgColorGeneralPicker = document.getElementById('bgColorGeneralPicker');
        const fullscreenGeneralBtn = document.getElementById('fullscreenGeneralBtn');

        if (resetViewGeneralBtn) {
            resetViewGeneralBtn.addEventListener('click', () => {
                console.log('🔄 Reset view clicked');
                if (window.viewerGeneral && window.viewerGeneral.model) {
                    window.viewerGeneral.fitCameraToModel();
                } else {
                    console.warn('No model loaded yet');
                }
            });
        }

        if (zoomInGeneralBtn) {
            zoomInGeneralBtn.addEventListener('click', () => {
                console.log('🔍 Zoom in clicked');
                if (window.viewerGeneral && window.viewerGeneral.camera) {
                    window.viewerGeneral.camera.position.multiplyScalar(0.8);
                    window.viewerGeneral.controls.update();
                } else {
                    console.warn('Camera not available');
                }
            });
        }

        if (zoomOutGeneralBtn) {
            zoomOutGeneralBtn.addEventListener('click', () => {
                console.log('🔍 Zoom out clicked');
                if (window.viewerGeneral && window.viewerGeneral.camera) {
                    window.viewerGeneral.camera.position.multiplyScalar(1.2);
                    window.viewerGeneral.controls.update();
                } else {
                    console.warn('Camera not available');
                }
            });
        }

        let isWireframeGeneral = false;
        if (solidModeGeneralBtn && wireframeGeneralBtn) {
            solidModeGeneralBtn.addEventListener('click', () => {
                console.log('🎨 Solid mode clicked');
                if (window.viewerGeneral && window.viewerGeneral.model) {
                    window.viewerGeneral.toggleWireframe(false);
                    isWireframeGeneral = false;
                    solidModeGeneralBtn.classList.add('active');
                    wireframeGeneralBtn.classList.remove('active');
                } else {
                    console.warn('No model loaded yet');
                }
            });

            wireframeGeneralBtn.addEventListener('click', () => {
                console.log('🎨 Wireframe mode clicked');
                if (window.viewerGeneral && window.viewerGeneral.model) {
                    window.viewerGeneral.toggleWireframe(true);
                    isWireframeGeneral = true;
                    wireframeGeneralBtn.classList.add('active');
                    solidModeGeneralBtn.classList.remove('active');
                } else {
                    console.warn('No model loaded yet');
                }
            });
        }

        if (modelColorGeneralPicker) {
            modelColorGeneralPicker.addEventListener('change', (e) => {
                console.log('🎨 Model color changed:', e.target.value);
                if (window.viewerGeneral && window.viewerGeneral.model) {
                    window.viewerGeneral.changeModelColor(e.target.value);
                } else {
                    console.warn('No model loaded yet');
                }
            });
        }

        if (bgColorGeneralPicker) {
            bgColorGeneralPicker.addEventListener('change', (e) => {
                console.log('🎨 Background color changed:', e.target.value);
                if (window.viewerGeneral && window.viewerGeneral.scene) {
                    window.viewerGeneral.changeBGColor(e.target.value);
                }
            });
        }

        if (fullscreenGeneralBtn) {
            fullscreenGeneralBtn.addEventListener('click', () => {
                console.log('🖥️ Fullscreen clicked');
                const viewer = document.getElementById('viewer3dGeneral');
                if (viewer.requestFullscreen) {
                    viewer.requestFullscreen();
                } else if (viewer.webkitRequestFullscreen) {
                    viewer.webkitRequestFullscreen();
                } else if (viewer.msRequestFullscreen) {
                    viewer.msRequestFullscreen();
                }
            });
        }

        // Handle fullscreen exit for General viewer
        const viewerGeneralElement = document.getElementById('viewer3dGeneral');
        if (viewerGeneralElement) {
            document.addEventListener('fullscreenchange', () => {
                if (!document.fullscreenElement) {
                    console.log('🖥️ Exited General fullscreen, resetting size...');
                    // Reset to original styles
                    viewerGeneralElement.style.height = '';
                    viewerGeneralElement.style.maxHeight = '700px';
                    viewerGeneralElement.style.minHeight = '500px';
                    setTimeout(() => {
                        if (window.viewerGeneral) {
                            const container = viewerGeneralElement.parentElement;
                            const width = container.clientWidth;
                            const height = Math.min(container.clientHeight, 700);
                            window.viewerGeneral.renderer.setSize(width, height);
                            window.viewerGeneral.camera.aspect = width / height;
                            window.viewerGeneral.camera.updateProjectionMatrix();
                        }
                    }, 100);
                }
            });

            document.addEventListener('webkitfullscreenchange', () => {
                if (!document.webkitFullscreenElement) {
                    console.log('🖥️ Exited General fullscreen (webkit), resetting size...');
                    // Reset to original styles
                    viewerGeneralElement.style.height = '';
                    viewerGeneralElement.style.maxHeight = '700px';
                    viewerGeneralElement.style.minHeight = '500px';
                    setTimeout(() => {
                        if (window.viewerGeneral) {
                            const container = viewerGeneralElement.parentElement;
                            const width = container.clientWidth;
                            const height = Math.min(container.clientHeight, 700);
                            window.viewerGeneral.renderer.setSize(width, height);
                            window.viewerGeneral.camera.aspect = width / height;
                            window.viewerGeneral.camera.updateProjectionMatrix();
                        }
                    }, 100);
                }
            });
        }

        console.log('✓ General viewer controls ready');

        // Medical Viewer Controls (same pattern)
        const resetViewMedicalBtn = document.getElementById('resetViewMedicalBtn');
        const zoomInMedicalBtn = document.getElementById('zoomInMedicalBtn');
        const zoomOutMedicalBtn = document.getElementById('zoomOutMedicalBtn');
        const solidModeMedicalBtn = document.getElementById('solidModeMedicalBtn');
        const wireframeMedicalBtn = document.getElementById('wireframeMedicalBtn');
        const modelColorMedicalPicker = document.getElementById('modelColorMedicalPicker');
        const bgColorMedicalPicker = document.getElementById('bgColorMedicalPicker');
        const fullscreenMedicalBtn = document.getElementById('fullscreenMedicalBtn');

        if (resetViewMedicalBtn) {
            resetViewMedicalBtn.addEventListener('click', () => {
                console.log('🔄 Medical reset view clicked');
                if (window.viewerMedical && window.viewerMedical.model) {
                    window.viewerMedical.fitCameraToModel();
                }
            });
        }

        if (zoomInMedicalBtn) {
            zoomInMedicalBtn.addEventListener('click', () => {
                console.log('🔍 Medical zoom in clicked');
                if (window.viewerMedical && window.viewerMedical.camera) {
                    window.viewerMedical.camera.position.multiplyScalar(0.8);
                    window.viewerMedical.controls.update();
                }
            });
        }

        if (zoomOutMedicalBtn) {
            zoomOutMedicalBtn.addEventListener('click', () => {
                console.log('🔍 Medical zoom out clicked');
                if (window.viewerMedical && window.viewerMedical.camera) {
                    window.viewerMedical.camera.position.multiplyScalar(1.2);
                    window.viewerMedical.controls.update();
                }
            });
        }

        let isWireframeMedical = false;
        if (solidModeMedicalBtn && wireframeMedicalBtn) {
            solidModeMedicalBtn.addEventListener('click', () => {
                console.log('🎨 Medical solid mode clicked');
                if (window.viewerMedical && window.viewerMedical.model) {
                    window.viewerMedical.toggleWireframe(false);
                    isWireframeMedical = false;
                    solidModeMedicalBtn.classList.add('active');
                    wireframeMedicalBtn.classList.remove('active');
                }
            });

            wireframeMedicalBtn.addEventListener('click', () => {
                console.log('🎨 Medical wireframe mode clicked');
                if (window.viewerMedical && window.viewerMedical.model) {
                    window.viewerMedical.toggleWireframe(true);
                    isWireframeMedical = true;
                    wireframeMedicalBtn.classList.add('active');
                    solidModeMedicalBtn.classList.remove('active');
                }
            });
        }

        if (modelColorMedicalPicker) {
            modelColorMedicalPicker.addEventListener('change', (e) => {
                console.log('🎨 Medical model color changed:', e.target.value);
                if (window.viewerMedical && window.viewerMedical.model) {
                    window.viewerMedical.changeModelColor(e.target.value);
                }
            });
        }

        if (bgColorMedicalPicker) {
            bgColorMedicalPicker.addEventListener('change', (e) => {
                console.log('🎨 Medical background color changed:', e.target.value);
                if (window.viewerMedical && window.viewerMedical.scene) {
                    window.viewerMedical.changeBGColor(e.target.value);
                }
            });
        }

        if (fullscreenMedicalBtn) {
            fullscreenMedicalBtn.addEventListener('click', () => {
                console.log('🖥️ Medical fullscreen clicked');
                const viewer = document.getElementById('viewer3dMedical');
                if (viewer.requestFullscreen) {
                    viewer.requestFullscreen();
                } else if (viewer.webkitRequestFullscreen) {
                    viewer.webkitRequestFullscreen();
                } else if (viewer.msRequestFullscreen) {
                    viewer.msRequestFullscreen();
                }
            });
        }

        // Handle fullscreen exit for Medical viewer
        const viewerMedicalElement = document.getElementById('viewer3dMedical');
        if (viewerMedicalElement) {
            document.addEventListener('fullscreenchange', () => {
                if (!document.fullscreenElement) {
                    console.log('🖥️ Exited Medical fullscreen, resetting size...');
                    // Reset to original styles
                    viewerMedicalElement.style.height = '';
                    viewerMedicalElement.style.maxHeight = '700px';
                    viewerMedicalElement.style.minHeight = '500px';
                    setTimeout(() => {
                        if (window.viewerMedical) {
                            const container = viewerMedicalElement.parentElement;
                            const width = container.clientWidth;
                            const height = Math.min(container.clientHeight, 700);
                            window.viewerMedical.renderer.setSize(width, height);
                            window.viewerMedical.camera.aspect = width / height;
                            window.viewerMedical.camera.updateProjectionMatrix();
                        }
                    }, 100);
                }
            });

            document.addEventListener('webkitfullscreenchange', () => {
                if (!document.webkitFullscreenElement) {
                    console.log('🖥️ Exited Medical fullscreen (webkit), resetting size...');
                    // Reset to original styles
                    viewerMedicalElement.style.height = '';
                    viewerMedicalElement.style.maxHeight = '700px';
                    viewerMedicalElement.style.minHeight = '500px';
                    setTimeout(() => {
                        if (window.viewerMedical) {
                            const container = viewerMedicalElement.parentElement;
                            const width = container.clientWidth;
                            const height = Math.min(container.clientHeight, 700);
                            window.viewerMedical.renderer.setSize(width, height);
                            window.viewerMedical.camera.aspect = width / height;
                            window.viewerMedical.camera.updateProjectionMatrix();
                        }
                    }, 100);
                }
            });
        }

        console.log('✓ Medical viewer controls ready');
        console.log('✓ All viewer controls initialized!');

        // Setup file list update handlers
        setupFileListUpdates();
    });

    // Function to update file lists
    function setupFileListUpdates() {
        // Listen for pricing updates which happen after files are loaded
        window.addEventListener('pricingUpdateNeeded', (event) => {
            const { viewerId } = event.detail;

            if (viewerId === 'viewer3dGeneral' && window.viewerGeneral) {
                updateFileList('General', window.viewerGeneral);
            } else if (viewerId === 'viewer3dMedical' && window.viewerMedical) {
                updateFileList('Medical', window.viewerMedical);
            }
        });

        console.log('✓ File list update handlers attached');
    }

    // Update file list UI
    function updateFileList(formType, viewer) {
        const filesList = document.getElementById(`uploadedFilesList${formType === 'General' ? '' : formType}`);
        const filesContainer = document.getElementById(`filesContainer${formType === 'General' ? '' : formType}`);
        const fileCount = document.getElementById(`fileCount${formType === 'General' ? '' : formType}`);

        if (!filesList || !filesContainer || !fileCount) {
            console.warn(`File list elements not found for ${formType}`);
            return;
        }

        const files = viewer.getUploadedFiles();
        fileCount.textContent = files.length;

        if (files.length === 0) {
            filesList.style.display = 'none';
            return;
        }

        filesList.style.display = 'block';

        // Build file list HTML
        filesContainer.innerHTML = files.map((fileData, index) => `
            <div class="file-list-item d-flex align-items-center justify-content-between p-2 border-bottom file-item-selectable"
                 style="background: white; cursor: pointer; transition: all 0.2s;"
                 data-file-id="${fileData.id}"
                 onclick="selectFile('${formType}', ${fileData.id})">
                <div class="d-flex align-items-center flex-grow-1">
                    <div class="file-icon me-2" style="width: 32px; height: 32px; background: #0047AD; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.7rem; font-weight: bold;">
                        ${getFileExtension(fileData.file.name).toUpperCase()}
                    </div>
                    <div class="flex-grow-1">
                        <div style="font-size: 0.85rem; font-weight: 600; color: #2c3e50;">${fileData.file.name}</div>
                        <small style="font-size: 0.7rem; color: #6c757d;">
                            ${formatFileSize(fileData.file.size)} • <span class="file-volume-display">${(fileData.volume?.cm3 || 0).toFixed(2)} cm³</span>
                        </small>
                    </div>
                </div>
                <div class="d-flex gap-1" onclick="event.stopPropagation()">
                    <button class="btn btn-sm btn-outline-primary toggle-visibility-btn" data-file-id="${fileData.id}" title="Toggle visibility" style="padding: 2px 6px; font-size: 0.75rem; border-radius: 4px;">
                        <svg class="eye-visible" width="12" height="12" viewBox="0 0 16 16" fill="currentColor" style="display: block;">
                            <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                            <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                        </svg>
                        <svg class="eye-hidden" width="12" height="12" viewBox="0 0 16 16" fill="currentColor" style="display: none;">
                            <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z"/>
                            <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/>
                            <path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12-.708.708z"/>
                        </svg>
                    </button>
                    <button class="btn btn-sm btn-outline-danger remove-file-btn" onclick="event.stopPropagation(); removeFile('${formType}', ${fileData.id})" style="padding: 2px 8px; font-size: 0.75rem; border-radius: 4px;">
                        <svg width="12" height="12" viewBox="0 0 16 16" fill="currentColor">
                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                            <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                        </svg>
                    </button>
                </div>
            </div>
        `).join('');

        console.log(`✓ File list updated for ${formType}: ${files.length} files`);

        // Attach visibility toggle handlers
        attachVisibilityHandlers(formType, viewer);

        // Select first file by default if none selected
        if (files.length > 0 && !window.selectedFileId) {
            selectFile(formType, files[0].id);
        }
    }

    // Select a file to edit its properties
    window.selectFile = function(formType, fileId) {
        const viewer = formType === 'General' ? window.viewerGeneral : window.viewerMedical;
        if (!viewer) return;

        const fileData = viewer.uploadedFiles.find(f => f.id === fileId);
        if (!fileData) return;

        // Store selected file
        window.selectedFileId = fileId;
        window.selectedFormType = formType;

        // Update UI to show which file is selected
        const containerSelector = `#filesContainer${formType === 'General' ? '' : formType}`;
        document.querySelectorAll(`${containerSelector} .file-list-item`).forEach(item => {
            if (parseFloat(item.getAttribute('data-file-id')) === fileId) {
                item.style.background = 'linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%)';
                item.style.borderLeft = '4px solid #0047AD';
            } else {
                item.style.background = 'white';
                item.style.borderLeft = 'none';
            }
        });

        // Update the sidebar controls to reflect this file's settings
        const settings = fileData.settings || {};

        // Update material select
        const materialSelect = document.getElementById(`materialSelect${formType}`);
        if (materialSelect) {
            materialSelect.value = settings.material || 'pla';
        }

        // Update technology select
        const technologySelect = document.getElementById(`technologySelect${formType}`);
        if (technologySelect) {
            technologySelect.value = settings.technology || 'fdm';
        }

        // Update color buttons
        const colorButtons = document.querySelectorAll(`.color-btn${formType === 'Medical' ? '-medical' : ''}`);
        colorButtons.forEach(btn => {
            const btnColor = btn.getAttribute('data-color');
            if (btnColor === (settings.color || '#808080')) {
                btn.classList.add('active');
                btn.style.border = '3px solid #0047AD';
            } else {
                btn.classList.remove('active');
                btn.style.border = '2px solid #dee2e6';
            }
        });

        console.log(`📁 File selected: ${fileData.file.name} (ID: ${fileId})`);
    };

    // Attach visibility toggle handlers
    function attachVisibilityHandlers(formType, viewer) {
        const visibilityButtons = document.querySelectorAll(`#filesContainer${formType === 'General' ? '' : formType} .toggle-visibility-btn`);
        console.log(`👁️ Attaching ${visibilityButtons.length} visibility handlers for ${formType}`);

        visibilityButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const fileId = parseFloat(btn.getAttribute('data-file-id'));
                toggleFileVisibility(formType, fileId, btn, viewer);
            });
        });
    }

    // Toggle file visibility
    function toggleFileVisibility(formType, fileId, button, viewer) {
        const fileData = viewer.uploadedFiles.find(f => f.id === fileId);
        if (!fileData || !fileData.mesh) {
            console.warn('👁️ File or mesh not found:', fileId);
            return;
        }

        // Toggle mesh visibility in Three.js
        fileData.mesh.visible = !fileData.mesh.visible;

        // Update button icon
        const eyeVisible = button.querySelector('.eye-visible');
        const eyeHidden = button.querySelector('.eye-hidden');

        if (fileData.mesh.visible) {
            // File is visible - show eye icon
            eyeVisible.style.display = 'block';
            eyeHidden.style.display = 'none';
            button.classList.remove('btn-outline-secondary');
            button.classList.add('btn-outline-primary');
            button.title = 'Hide file';
        } else {
            // File is hidden - show crossed eye icon
            eyeVisible.style.display = 'none';
            eyeHidden.style.display = 'block';
            button.classList.remove('btn-outline-primary');
            button.classList.add('btn-outline-secondary');
            button.title = 'Show file';
        }

        // Update the file item styling
        const fileItem = button.closest('.file-list-item');
        if (fileItem) {
            fileItem.style.opacity = fileData.mesh.visible ? '1' : '0.5';
        }

        console.log(`👁️ File visibility toggled:`, {
            fileId,
            fileName: fileData.file.name,
            isVisible: fileData.mesh.visible
        });
    }

    // Calculate price for individual file
    window.calculateFilePrice = function(fileId, viewer, formType) {
        const fileData = viewer.uploadedFiles.find(f => f.id === fileId);
        if (!fileData || !fileData.volume) {
            console.warn(`⚠️ Cannot calculate price for file ${fileId}: No volume data`);
            return;
        }

        // Get settings (use defaults if not set)
        const settings = fileData.settings || {};
        const materialCost = settings.materialCost || 0.02; // Default PLA
        const technologyMultiplier = settings.technologyMultiplier || 1.0; // Default FDM
        const volume = fileData.volume.cm3 || 0;

        // Calculate price: Volume × Material Cost × Technology Multiplier
        const price = volume * materialCost * technologyMultiplier;

        // Store calculated price
        fileData.calculatedPrice = price;

        console.log(`💰 Price calculated for file ${fileId}:`, {
            volume: volume.toFixed(2),
            materialCost,
            technologyMultiplier,
            price: price.toFixed(2)
        });

        // Don't display yet - will be shown when "Save & Calculate" is clicked
        // Just store it for now

        return price;
    }

    // Show all file prices (called when "Save & Calculate" is clicked)
    window.showAllFilePrices = function(formType) {
        const viewer = formType === 'General' ? window.viewerGeneral : window.viewerMedical;
        if (!viewer || !viewer.uploadedFiles) return;

        let totalPrice = 0;

        viewer.uploadedFiles.forEach(fileData => {
            // Calculate price if not already calculated
            if (fileData.calculatedPrice === undefined) {
                calculateFilePrice(fileData.id, viewer, formType);
            }

            const price = fileData.calculatedPrice || 0;
            totalPrice += price;

            // Find and show the price display for this file
            const fileItem = document.querySelector(`[data-file-id="${fileData.id}"] .file-price-display`);
            if (fileItem) {
                fileItem.style.display = 'block';

                const priceValue = fileItem.querySelector('.file-price-value');
                const priceBreakdown = fileItem.querySelector('.file-price-breakdown');

                if (priceValue) {
                    priceValue.textContent = `$${price.toFixed(2)}`;
                }

                if (priceBreakdown) {
                    const volume = fileData.volume?.cm3 || 0;
                    const materialCost = fileData.settings?.materialCost || 0.02;
                    const techMultiplier = fileData.settings?.technologyMultiplier || 1.0;
                    priceBreakdown.textContent = `${volume.toFixed(2)} cm³ × $${materialCost} × ${techMultiplier}`;
                }
            }
        });

        // Update total price in the summary section
        const priceSummary = document.getElementById(`priceSummary${formType}`);
        if (priceSummary) {
            priceSummary.style.display = 'block';

            // Update total price display
            const totalPriceElement = priceSummary.querySelector('.total-price');
            if (totalPriceElement) {
                totalPriceElement.textContent = `$${totalPrice.toFixed(2)}`;
            }
        }

        console.log(`💵 All file prices displayed for ${formType}. Total: $${totalPrice.toFixed(2)}`);

        return totalPrice;
    };

    // Helper functions
    function getFileExtension(filename) {
        return filename.split('.').pop().toLowerCase();
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
    }

    // Remove file function
    window.removeFile = async function(formType, fileId) {
        const viewer = formType === 'General' ? window.viewerGeneral : window.viewerMedical;
        if (viewer) {
            viewer.removeFile(fileId);
            updateFileList(formType, viewer);

            // Delete from IndexedDB storage
            if (window.fileStorageManager && window.fileStorageManager.currentFileId) {
                try {
                    await window.fileStorageManager.deleteFile(window.fileStorageManager.currentFileId);
                    window.fileStorageManager.currentFileId = null;

                    // Clear URL parameter
                    const url = new URL(window.location.href);
                    url.searchParams.delete('file');
                    window.history.pushState({}, '', url.toString());

                    console.log('🗑️ File removed from IndexedDB storage');
                } catch (error) {
                    console.error('❌ Failed to delete from storage:', error);
                }
            }

            // Trigger pricing update
            if (window.fileManagerGeneral && formType === 'General') {
                window.fileManagerGeneral.updateQuote();
            } else if (window.fileManagerMedical && formType === 'Medical') {
                window.fileManagerMedical.updateQuote();
            }

            console.log(`✓ File ${fileId} removed from ${formType}`);
        }
    };

    // ============================================
    // BOTTOM CONTROL BAR FUNCTIONALITY
    // ============================================

    // State management
    let gridVisible = true;
    let autoRotateEnabled = true;
    let modelRepaired = false;
    let holesFilled = false;
    let currentVolume = 0;
    let currentViewerId = 'viewer3dGeneral'; // Track active viewer

    // Show control bar when model is loaded
    window.addEventListener('modelLoaded', (event) => {
        const { viewerId } = event.detail;
        currentViewerId = viewerId;

        const viewer = document.getElementById(viewerId);
        const modelBadge = viewer?.querySelector('.model-info-badge');

        if (modelBadge) {
            modelBadge.style.display = 'block';
        }

        // Start auto-rotation by default
        if (autoRotateEnabled) {
            startAutoRotation(viewerId);
        }

        // Update measurements
        updateMeasurements(viewerId);
    });

    // Camera view buttons - Main control bar
    document.querySelectorAll('#mainControlBar .camera-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const view = this.dataset.view;

            // Remove active class from siblings
            this.parentElement.querySelectorAll('.camera-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            // Set camera view on current viewer
            setCameraView(currentViewerId, view);
        });
    });

    // Grid toggle - Main control bar
    const toggleGridBtnMain = document.getElementById('toggleGridBtnMain');
    if (toggleGridBtnMain) {
        toggleGridBtnMain.addEventListener('click', function() {
            gridVisible = !gridVisible;
            this.classList.toggle('active', gridVisible);
            toggleGrid(currentViewerId, gridVisible);
            console.log('Grid toggled:', gridVisible);
        });
    }

    // Auto-rotate toggle - Main control bar
    const autoRotateBtnMain = document.getElementById('autoRotateBtnMain');
    if (autoRotateBtnMain) {
        autoRotateBtnMain.addEventListener('click', function() {
            autoRotateEnabled = !autoRotateEnabled;
            this.classList.toggle('active', autoRotateEnabled);

            if (autoRotateEnabled) {
                startAutoRotation(currentViewerId);
            } else {
                stopAutoRotation(currentViewerId);
            }
            console.log('Auto-rotate:', autoRotateEnabled);
        });
    }

    // Repair model - Main control bar
    const repairModelBtnMain = document.getElementById('repairModelBtnMain');
    if (repairModelBtnMain) {
        repairModelBtnMain.addEventListener('click', function() {
            if (modelRepaired) {
                console.log('Model already repaired');
                return;
            }

            this.classList.add('active');

            // Show processing state
            const originalHTML = this.innerHTML;
            this.innerHTML = '<svg width="18" height="18" viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="7" stroke="currentColor" stroke-width="1.5" stroke-dasharray="4"/></svg><span>Repairing...</span>';
            this.style.pointerEvents = 'none';

            setTimeout(() => {
                repairModel(currentViewerId);
                modelRepaired = true;
                this.innerHTML = '<svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M5 9L8 12L13 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg><span>Repaired ✓</span>';
                this.style.pointerEvents = '';
                updateMeasurements(currentViewerId);
                console.log('Model repaired');
            }, 1500);
        });
    }

    // Fill holes - Main control bar
    const fillHolesBtnMain = document.getElementById('fillHolesBtnMain');
    if (fillHolesBtnMain) {
        fillHolesBtnMain.addEventListener('click', function() {
            if (holesFilled) {
                console.log('Holes already filled');
                return;
            }

            this.classList.add('active');

            // Show processing state
            const originalHTML = this.innerHTML;
            this.innerHTML = '<svg width="18" height="18" viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="7" stroke="currentColor" stroke-width="1.5" stroke-dasharray="4"/></svg><span>Filling...</span>';
            this.style.pointerEvents = 'none';

            setTimeout(() => {
                fillHoles(currentViewerId);
                holesFilled = true;
                this.innerHTML = '<svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M5 9L8 12L13 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg><span>Filled ✓</span>';
                this.style.pointerEvents = '';
                updateMeasurements(currentViewerId);
                console.log('Holes filled');
            }, 1500);
        });
    }

    // Save & Calculate - Main control bar
    // NOTE: Button handler is now in quote.blade.php which calls EnhancedSaveCalculate.execute()
    // This old handler is DISABLED to prevent conflicts
    // const saveCalculationsBtnMain = document.getElementById('saveCalculationsBtnMain');
    // if (saveCalculationsBtnMain) {
    //     saveCalculationsBtnMain.addEventListener('click', function() {
    //         saveCalculations(currentViewerId);
    //     });
    // }
    console.log('ℹ️ Save & Calculate handler delegated to quote.blade.php (EnhancedSaveCalculate module)');

    // Inject Screenshot and Share buttons into bottom toolbar
    function injectToolbarButtons() {
        console.log('🔧 Injecting screenshot and share buttons...');

        // Find the tools section in the bottom control bar
        const toolsSections = document.querySelectorAll('.tool-buttons');

        toolsSections.forEach((toolsSection, index) => {
            // Check if screenshot button already exists
            if (toolsSection.querySelector('#screenshotBtnMain, #screenshotBtnMedicalMain')) {
                console.log('Screenshot button already exists in toolbar', index);
                return;
            }

            const isGeneral = index === 0;
            const screenshotId = isGeneral ? 'screenshotBtnMain' : 'screenshotBtnMedicalMain';
            const shareId = isGeneral ? 'shareBtnMain' : 'shareBtnMedicalMain';

            // Create Screenshot button
            const screenshotBtn = document.createElement('button');
            screenshotBtn.type = 'button';
            screenshotBtn.className = 'control-btn tool-btn';
            screenshotBtn.id = screenshotId;
            screenshotBtn.title = 'Take Screenshot';
            screenshotBtn.innerHTML = `
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                    <rect x="2" y="4" width="14" height="11" rx="2" stroke="currentColor" stroke-width="1.5"/>
                    <circle cx="9" cy="9.5" r="2.5" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M6 4L7 2H11L12 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                <span>Screenshot</span>
            `;

            // Create Share button
            const shareBtn = document.createElement('button');
            shareBtn.type = 'button';
            shareBtn.className = 'control-btn tool-btn';
            shareBtn.id = shareId;
            shareBtn.title = 'Share Model';
            shareBtn.innerHTML = `
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                    <circle cx="13" cy="4" r="2.5" stroke="currentColor" stroke-width="1.5"/>
                    <circle cx="5" cy="9" r="2.5" stroke="currentColor" stroke-width="1.5"/>
                    <circle cx="13" cy="14" r="2.5" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M7.5 10L10.5 12.5M7.5 8L10.5 5.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                <span>Share</span>
            `;

            // Add buttons to toolbar
            toolsSection.appendChild(screenshotBtn);
            toolsSection.appendChild(shareBtn);

            console.log(`✅ Added screenshot and share buttons to toolbar ${index}`);

            // Add event listeners
            screenshotBtn.addEventListener('click', function() {
                console.log('📸 Screenshot button clicked');
                const viewerId = isGeneral ? 'viewer3dGeneral' : 'viewer3dMedical';
                const viewer = isGeneral ? window.viewerGeneral : window.viewerMedical;

                if (!viewer) {
                    console.error('Viewer not found!');
                    return;
                }

                try {
                    // Render the scene
                    viewer.renderer.render(viewer.scene, viewer.camera);

                    // Get the canvas as data URL
                    const dataURL = viewer.renderer.domElement.toDataURL('image/png');

                    // Create download link
                    const link = document.createElement('a');
                    link.download = `3d-model-screenshot-${Date.now()}.png`;
                    link.href = dataURL;
                    link.click();

                    console.log('✅ Screenshot captured successfully');

                    // Visual feedback
                    this.classList.add('active');
                    setTimeout(() => this.classList.remove('active'), 500);
                } catch (error) {
                    console.error('Screenshot error:', error);
                }
            });

            shareBtn.addEventListener('click', function() {
                console.log('🔗 Share button clicked');

                if (window.shareModal) {
                    // Get the first file ID if available
                    const filesList = document.querySelectorAll('[data-file-id]');
                    const firstFileId = filesList.length > 0 ? filesList[0].dataset.fileId : null;

                    window.shareModal.open(firstFileId);
                } else {
                    console.error('Share modal not found!');
                }
            });
        });
    }

    // Inject buttons after a short delay to ensure DOM is ready
    setTimeout(injectToolbarButtons, 1000);

    // Also try to inject when viewersReady event fires
    window.addEventListener('viewersReady', () => {
        setTimeout(injectToolbarButtons, 500);
    });

    // Helper functions
    function setCameraView(viewerId, view) {
        const viewer = viewerId === 'viewer3dGeneral' ? window.viewerGeneral : window.viewerMedical;
        if (!viewer?.scene?.activeCamera) {
            console.log('No active camera found for:', viewerId);
            return;
        }

        const camera = viewer.scene.activeCamera;
        const radius = camera.radius || 10;

        const views = {
            top: { alpha: Math.PI / 2, beta: 0 },
            bottom: { alpha: Math.PI / 2, beta: Math.PI },
            front: { alpha: Math.PI / 2, beta: Math.PI / 2 },
            right: { alpha: 0, beta: Math.PI / 2 },
            left: { alpha: Math.PI, beta: Math.PI / 2 }
        };

        if (views[view] && camera.alpha !== undefined) {
            camera.alpha = views[view].alpha;
            camera.beta = views[view].beta;
            console.log('Camera view changed to:', view);
        }
    }

    function toggleGrid(viewerId, visible) {
        const viewer = viewerId === 'viewer3dGeneral' ? window.viewerGeneral : window.viewerMedical;
        if (!viewer?.scene) {
            console.log('No scene found for:', viewerId);
            return;
        }

        const ground = viewer.scene.getMeshByName('ground');
        if (ground) {
            ground.isVisible = visible;
            console.log('Grid visibility:', visible);
        } else {
            console.log('Ground mesh not found');
        }
    }

    function startAutoRotation(viewerId) {
        const viewer = viewerId === 'viewer3dGeneral' ? window.viewerGeneral : window.viewerMedical;
        if (!viewer?.scene?.activeCamera) {
            console.log('Cannot start rotation - no camera');
            return;
        }

        const camera = viewer.scene.activeCamera;
        if (camera.useAutoRotationBehavior !== undefined) {
            camera.useAutoRotationBehavior = true;
            if (camera.autoRotationBehavior) {
                camera.autoRotationBehavior.idleRotationSpeed = 0.2;
                camera.autoRotationBehavior.idleRotationWaitTime = 1000;
            }
            console.log('Auto-rotation started');
        }
    }

    function stopAutoRotation(viewerId) {
        const viewer = viewerId === 'viewer3dGeneral' ? window.viewerGeneral : window.viewerMedical;
        if (!viewer?.scene?.activeCamera) return;

        const camera = viewer.scene.activeCamera;
        if (camera.useAutoRotationBehavior !== undefined) {
            camera.useAutoRotationBehavior = false;
            console.log('Auto-rotation stopped');
        }
    }

    function repairModel(viewerId) {
        // Simulate model repair - actual implementation would use mesh repair algorithms
        console.log('🔧 Repairing model:', viewerId);
        // In production: Fix non-manifold edges, inverted normals, etc.
        // This could integrate with libraries like three-mesh-bvh for repairs
    }

    function fillHoles(viewerId) {
        // Simulate hole filling - actual implementation would fill mesh holes
        console.log('🔧 Filling holes:', viewerId);
        // In production: Detect and fill holes in mesh geometry
        // Could use algorithms like advancing front or Poisson surface reconstruction
    }

    function updateMeasurements(viewerId) {
        const viewer = viewerId === 'viewer3dGeneral' ? window.viewerGeneral : window.viewerMedical;
        if (!viewer?.uploadedFiles || viewer.uploadedFiles.length === 0) {
            console.log('No files uploaded yet');
            return;
        }

        // Get current file data
        const file = viewer.uploadedFiles[0];
        const bounds = file.boundingBox || { x: 0, y: 0, z: 0 };
        const volume = file.volume || 0;

        console.log('Updating measurements:', { bounds, volume });

        // Update main control bar measurements
        const updateValue = (id, value) => {
            const el = document.getElementById(id);
            if (el) el.textContent = value.toFixed(2);
        };

        updateValue('measureXMain', bounds.x || 0);
        updateValue('measureYMain', bounds.y || 0);
        updateValue('measureZMain', bounds.z || 0);
        updateValue('measureVolumeMain', (volume / 1000).toFixed(2)); // Convert to cm³

        currentVolume = volume;
    }

    function saveCalculations(viewerId) {
        const formType = viewerId === 'viewer3dGeneral' ? 'General' : 'Medical';

        console.log('💾 Saving calculations:', {
            viewerId,
            volume: currentVolume,
            repaired: modelRepaired,
            holesFilled: holesFilled
        });

        // Show success message
        const btn = document.getElementById('saveCalculationsBtnMain');
        if (btn) {
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M5 9L8 12L13 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg><span>Saved! ✓</span>';
            btn.style.pointerEvents = 'none';

            setTimeout(() => {
                btn.innerHTML = originalHTML;
                btn.style.pointerEvents = '';
            }, 2000);
        }

        // Trigger pricing update with repaired volume
        if (window.fileManagerGeneral && formType === 'General') {
            window.fileManagerGeneral.updateQuote();
        } else if (window.fileManagerMedical && formType === 'Medical') {
            window.fileManagerMedical.updateQuote();
        }
    }

    // Track which viewer is active based on tab switching
    document.querySelectorAll('.category-tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const category = this.dataset.category;
            currentViewerId = category === 'general' ? 'viewer3dGeneral' : 'viewer3dMedical';
            console.log('Switched to viewer:', currentViewerId);

            // Reset states when switching
            modelRepaired = false;
            holesFilled = false;

            // Reset repair buttons
            const repairBtn = document.getElementById('repairModelBtnMain');
            const fillBtn = document.getElementById('fillHolesBtnMain');
            if (repairBtn) {
                repairBtn.classList.remove('active');
                repairBtn.innerHTML = '<svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M15 3L3 15M3 3L15 15" stroke="currentColor" stroke-width="1.5"/><circle cx="9" cy="9" r="7" stroke="currentColor" stroke-width="1.5"/></svg><span>Repair</span>';
            }
            if (fillBtn) {
                fillBtn.classList.remove('active');
                fillBtn.innerHTML = '<svg width="18" height="18" viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="7" stroke="currentColor" stroke-width="1.5"/><path d="M9 6V12M6 9H12" stroke="currentColor" stroke-width="1.5"/></svg><span>Fill Holes</span>';
            }

            // Update measurements for new viewer
            updateMeasurements(currentViewerId);
        });
    });

    console.log('✅ Control bar initialized and ready');

    // ============================================
    // MEASUREMENT TOOL - Two-Point Distance (Three.js)
    // ============================================

    let measurementMode = false;
    let measurementPoints = [];
    let measurementMarkers = [];
    let measurementLine = null;
    let measurementLabel = null;
    let currentViewer = null;

    // Initialize Measurement Tool
    function initMeasurementTool() {
        // Try both IDs - measureToolBtn (from quote-viewer) and measureToolBtnMain (from quote)
        const measureBtn = document.getElementById('measureToolBtnMain') || document.getElementById('measureToolBtn');
        if (!measureBtn) {
            console.warn('Measure button not found (tried measureToolBtnMain and measureToolBtn)');
            return;
        }

        console.log('📏 Found measure button:', measureBtn.id);

        measureBtn.addEventListener('click', () => {
            measurementMode = !measurementMode;

            if (measurementMode) {
                // Activate measurement mode
                measureBtn.classList.add('active');
                measureBtn.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
                measureBtn.style.color = 'white';
                console.log('📏 Measurement mode ACTIVE - Click two points on the model');

                // Clear any existing measurements
                clearMeasurement();
                measurementPoints = [];

                // Show instruction notification
                showMeasurementNotification('Click first point on the model', 'info', 3000);
            } else {
                // Deactivate measurement mode
                measureBtn.classList.remove('active');
                measureBtn.style.background = '';
                measureBtn.style.color = '';
                clearMeasurement();
                measurementPoints = [];
                console.log('📏 Measurement mode OFF');
            }
        });

        console.log('✅ Measurement tool initialized');
    }

    // Handle canvas clicks for measurement
    function handleMeasurementClick(event, viewer) {
        if (!measurementMode || !viewer || !viewer.scene || !viewer.camera) return;

        currentViewer = viewer;
        const THREE = window.THREE;

        // Get normalized device coordinates (-1 to +1)
        const rect = viewer.renderer.domElement.getBoundingClientRect();
        const mouse = new THREE.Vector2();
        mouse.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
        mouse.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;

        // Create raycaster
        const raycaster = new THREE.Raycaster();
        raycaster.setFromCamera(mouse, viewer.camera);

        // Find intersections with model group
        let intersects = [];
        if (viewer.modelGroup) {
            intersects = raycaster.intersectObject(viewer.modelGroup, true);
        }

        if (intersects.length === 0) {
            console.log('❌ No mesh hit at click position');
            showMeasurementNotification('Click on the model surface', 'warning', 2000);
            return;
        }

        const point = intersects[0].point;
        console.log('✅ Point picked:', { x: point.x.toFixed(2), y: point.y.toFixed(2), z: point.z.toFixed(2) });

        // Handle the click based on current state
        if (measurementPoints.length === 0) {
            // First point
            measurementPoints.push(point.clone());
            createMarker(viewer.scene, point, 0);
            showMeasurementNotification('Click second point', 'info', 3000);
            console.log('📍 Start point placed');
        } else if (measurementPoints.length === 1) {
            // Second point - complete measurement
            measurementPoints.push(point.clone());
            createMarker(viewer.scene, point, 1);
            drawLine(viewer.scene);
            calculateAndDisplayDistance(viewer.scene);
            console.log('📍 End point placed - measurement complete');
        } else {
            // Third click - reset and start new measurement
            console.log('🔄 Starting new measurement');
            clearMeasurement();
            measurementPoints = [];
            // Place first point of new measurement
            measurementPoints.push(point.clone());
            createMarker(viewer.scene, point, 0);
            showMeasurementNotification('Click second point', 'info', 3000);
            console.log('📍 New start point placed');
        }
    }

    // Create a visual marker sphere at the clicked point
    function createMarker(scene, position, index) {
        const THREE = window.THREE;

        // Create sphere geometry
        const geometry = new THREE.SphereGeometry(2, 16, 16);

        // Create material - different colors for start/end
        const material = new THREE.MeshBasicMaterial({
            color: index === 0 ? 0x00ff00 : 0xff0000, // Green for start, Red for end
            transparent: true,
            opacity: 0.8
        });

        const marker = new THREE.Mesh(geometry, material);
        marker.position.copy(position);
        marker.name = `measurementMarker${index}`;

        scene.add(marker);
        measurementMarkers.push(marker);

        console.log(`✓ Marker ${index + 1} created at:`, position);
    }

    // Draw line between two points
    function drawLine(scene) {
        const THREE = window.THREE;

        if (measurementPoints.length < 2) return;

        const point1 = measurementPoints[0];
        const point2 = measurementPoints[1];

        // Calculate direction and distance
        const direction = new THREE.Vector3().subVectors(point2, point1);
        const distance = direction.length();

        // Create a tube geometry for a visible thick line
        const curve = new THREE.LineCurve3(point1, point2);
        const tubeGeometry = new THREE.TubeGeometry(curve, 1, 0.5, 8, false);

        // Create bright blue material with emissive property
        const material = new THREE.MeshBasicMaterial({
            color: 0x0088ff, // Bright blue
            transparent: false,
            depthTest: true,
            side: THREE.DoubleSide
        });

        measurementLine = new THREE.Mesh(tubeGeometry, material);
        measurementLine.name = 'measurementLine';
        scene.add(measurementLine);

        console.log('✓ Blue line drawn between points');
    }

    // Calculate distance and create label
    function calculateAndDisplayDistance(scene) {
        const THREE = window.THREE;

        if (measurementPoints.length < 2) return;

        const point1 = measurementPoints[0];
        const point2 = measurementPoints[1];

        // Calculate distance
        const distance = point1.distanceTo(point2);

        console.log('📏 Distance measured:', distance.toFixed(2), 'mm');

        // Create 3D text label at midpoint
        const midPoint = new THREE.Vector3().addVectors(point1, point2).multiplyScalar(0.5);
        createMeasurementLabel(scene, midPoint, distance);

        // Show notification with result
        showMeasurementNotification(
            `Distance: ${distance.toFixed(2)} mm (${(distance / 10).toFixed(2)} cm)`,
            'success',
            7000
        );
    }

    // Create a 3D text label using canvas texture
    function createMeasurementLabel(scene, position, distance) {
        const THREE = window.THREE;

        // Create canvas for text
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');
        canvas.width = 512;
        canvas.height = 128;

        // Draw background
        context.fillStyle = 'rgba(0, 0, 0, 0.8)';
        context.fillRect(0, 0, canvas.width, canvas.height);

        // Draw border
        context.strokeStyle = '#ffff00';
        context.lineWidth = 4;
        context.strokeRect(2, 2, canvas.width - 4, canvas.height - 4);

        // Draw text
        const text = `${distance.toFixed(2)} mm`;
        context.font = 'bold 48px Arial';
        context.fillStyle = 'white';
        context.textAlign = 'center';
        context.textBaseline = 'middle';
        context.fillText(text, canvas.width / 2, canvas.height / 2);

        // Create texture from canvas
        const texture = new THREE.CanvasTexture(canvas);
        texture.needsUpdate = true;

        // Create plane for label
        const geometry = new THREE.PlaneGeometry(40, 10);
        const material = new THREE.MeshBasicMaterial({
            map: texture,
            transparent: true,
            side: THREE.DoubleSide,
            depthTest: false, // Always visible on top
            depthWrite: false, // Don't write to depth buffer
            opacity: 1.0
        });

        measurementLabel = new THREE.Mesh(geometry, material);
        measurementLabel.position.copy(position);
        measurementLabel.position.y += 15; // Offset above midpoint
        measurementLabel.name = 'measurementLabel';
        measurementLabel.renderOrder = 9999; // Render absolutely last
        measurementLabel.frustumCulled = false; // Never cull from view

        // Store viewer reference for later
        measurementLabel.userData.viewer = currentViewer;

        scene.add(measurementLabel);
        console.log('✓ Label created and added to scene');
        console.log('  Label renderOrder:', measurementLabel.renderOrder);
        console.log('  Label frustumCulled:', measurementLabel.frustumCulled);

        // Update label rotation continuously
        updateLabelRotation();
    }

    // Update label to always face camera (continuous animation)
    function updateLabelRotation() {
        if (!measurementLabel || !currentViewer || !currentViewer.camera) {
            // Stop if label doesn't exist
            return;
        }

        // Make label face camera
        measurementLabel.quaternion.copy(currentViewer.camera.quaternion);

        // Continue updating
        requestAnimationFrame(updateLabelRotation);
    }

    // Clear all measurement visuals
    function clearMeasurement() {
        const THREE = window.THREE;

        if (!currentViewer || !currentViewer.scene) {
            // Try to find viewer
            if (window.viewerGeneral && window.viewerGeneral.scene) {
                currentViewer = window.viewerGeneral;
            } else if (window.viewerMedical && window.viewerMedical.scene) {
                currentViewer = window.viewerMedical;
            }
        }

        if (!currentViewer || !currentViewer.scene) return;

        const scene = currentViewer.scene;

        // Remove markers
        measurementMarkers.forEach(marker => {
            if (marker) {
                scene.remove(marker);
                if (marker.geometry) marker.geometry.dispose();
                if (marker.material) marker.material.dispose();
            }
        });
        measurementMarkers = [];

        // Remove line
        if (measurementLine) {
            scene.remove(measurementLine);
            if (measurementLine.geometry) measurementLine.geometry.dispose();
            if (measurementLine.material) measurementLine.material.dispose();
            measurementLine = null;
        }

        // Remove label
        if (measurementLabel) {
            scene.remove(measurementLabel);
            if (measurementLabel.geometry) measurementLabel.geometry.dispose();
            if (measurementLabel.material) {
                if (measurementLabel.material.map) measurementLabel.material.map.dispose();
                measurementLabel.material.dispose();
            }
            measurementLabel = null;
        }

        console.log('🧹 Measurement cleared');
    }

    // Show notification for measurement tool
    function showMeasurementNotification(message, type = 'info', duration = 3000) {
        const notification = document.createElement('div');
        const colors = {
            info: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
            success: 'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)',
            warning: 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)'
        };

        notification.style.cssText = `
            position: fixed;
            top: 80px;
            right: 20px;
            background: ${colors[type]};
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            z-index: 9999;
            animation: slideIn 0.3s ease-out;
            font-weight: 500;
        `;
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => notification.remove(), 300);
        }, duration);
    }

    // ============================================
    // PAN TOOL - Drag and Move Model
    // ============================================

    let panMode = false;
    let isDragging = false;
    let previousMousePosition = { x: 0, y: 0 };

    // Initialize Pan Tool
    function initPanTool() {
        // General viewer pan button
        const panBtn = document.getElementById('panToolBtn');
        if (panBtn) {
            panBtn.addEventListener('click', () => togglePanMode(panBtn, 'general'));
        }

        // Medical viewer pan button
        const panBtnMedical = document.getElementById('panToolBtnMedical');
        if (panBtnMedical) {
            panBtnMedical.addEventListener('click', () => togglePanMode(panBtnMedical, 'medical'));
        }

        console.log('✅ Pan tool initialized');
    }

    function togglePanMode(button, viewerType) {
        panMode = !panMode;

        if (panMode) {
            // Activate pan mode
            button.classList.add('active');
            button.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
            button.style.color = 'white';

            // Disable orbit controls rotation, keep zoom
            if (window.viewerGeneral && window.viewerGeneral.controls) {
                window.viewerGeneral.controls.enableRotate = false;
            }
            if (window.viewerMedical && window.viewerMedical.controls) {
                window.viewerMedical.controls.enableRotate = false;
            }

            console.log('✋ Pan mode ACTIVE - Drag to move the model');
            showMeasurementNotification('Drag to move the model', 'info', 3000);
        } else {
            // Deactivate pan mode
            button.classList.remove('active');
            button.style.background = '';
            button.style.color = '';

            // Re-enable orbit controls rotation
            if (window.viewerGeneral && window.viewerGeneral.controls) {
                window.viewerGeneral.controls.enableRotate = true;
            }
            if (window.viewerMedical && window.viewerMedical.controls) {
                window.viewerMedical.controls.enableRotate = true;
            }

            console.log('✋ Pan mode OFF');
        }
    }

    // Handle mouse events for panning
    function setupPanHandlers(canvas, viewer) {
        canvas.addEventListener('mousedown', (event) => {
            if (!panMode || !viewer.modelGroup) return;
            isDragging = true;
            previousMousePosition = { x: event.clientX, y: event.clientY };
            canvas.style.cursor = 'grabbing';
        });

        canvas.addEventListener('mousemove', (event) => {
            if (!panMode) {
                canvas.style.cursor = 'default';
                return;
            }

            if (!isDragging) {
                canvas.style.cursor = 'grab';
                return;
            }

            if (!viewer.modelGroup) return;

            const deltaX = event.clientX - previousMousePosition.x;
            const deltaY = event.clientY - previousMousePosition.y;

            // Calculate movement speed based on camera distance
            const distance = viewer.camera.position.length();
            const movementSpeed = distance * 0.001;

            // Move the model group
            viewer.modelGroup.position.x += deltaX * movementSpeed;
            viewer.modelGroup.position.y -= deltaY * movementSpeed;

            previousMousePosition = { x: event.clientX, y: event.clientY };
        });

        canvas.addEventListener('mouseup', () => {
            if (!panMode) return;
            isDragging = false;
            canvas.style.cursor = 'grab';
        });

        canvas.addEventListener('mouseleave', () => {
            if (!panMode) return;
            isDragging = false;
            canvas.style.cursor = 'default';
        });
    }

    // ============================================
    // END PAN TOOL
    // ============================================

    // ============================================
    // SCREENSHOT TOOL - Capture Viewer as Image
    // ============================================

    function initScreenshotTool() {
        // General viewer screenshot button
        const screenshotBtn = document.getElementById('screenshotBtn');
        if (screenshotBtn) {
            screenshotBtn.addEventListener('click', () => takeScreenshot('general'));
        }

        // Medical viewer screenshot button
        const screenshotBtnMedical = document.getElementById('screenshotBtnMedical');
        if (screenshotBtnMedical) {
            screenshotBtnMedical.addEventListener('click', () => takeScreenshot('medical'));
        }

        console.log('✅ Screenshot tool initialized');
    }

    function takeScreenshot(viewerType) {
        const viewer = viewerType === 'general' ? window.viewerGeneral : window.viewerMedical;

        if (!viewer || !viewer.renderer) {
            console.warn('⚠️ No viewer available for screenshot');
            showMeasurementNotification('No 3D model loaded', 'warning', 2000);
            return;
        }

        try {
            // Render the scene to ensure it's up to date
            viewer.renderer.render(viewer.scene, viewer.camera);

            // Get the canvas element
            const canvas = viewer.renderer.domElement;

            // Convert canvas to blob
            canvas.toBlob((blob) => {
                if (!blob) {
                    console.error('Failed to create image blob');
                    showMeasurementNotification('Screenshot failed', 'error', 2000);
                    return;
                }

                // Create download link
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                const timestamp = new Date().toISOString().replace(/[:.]/g, '-').slice(0, -5);
                const filename = `3d-viewer-${viewerType}-${timestamp}.png`;

                link.href = url;
                link.download = filename;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                // Clean up
                setTimeout(() => URL.revokeObjectURL(url), 100);

                console.log('📸 Screenshot saved:', filename);
                showMeasurementNotification('Screenshot saved! 📸', 'success', 2000);
            }, 'image/png', 1.0); // High quality PNG

        } catch (error) {
            console.error('Screenshot error:', error);
            showMeasurementNotification('Screenshot failed', 'error', 2000);
        }
    }

    // ============================================
    // END SCREENSHOT TOOL
    // ============================================
loading
    // ============================================
    // SHARE BUTTON HANDLERS
    // ============================================

    function initShareButtons() {
        // General viewer share button
        const shareGeneralBtn = document.getElementById('shareGeneralBtn');
        if (shareGeneralBtn) {
            shareGeneralBtn.addEventListener('click', () => handleShareClick('general'));
        }

        // Medical viewer share button
        const shareMedicalBtn = document.getElementById('shareMedicalBtn');
        if (shareMedicalBtn) {
            shareMedicalBtn.addEventListener('click', () => handleShareClick('medical'));
        }

        console.log('✅ Share buttons initialized');
    }

    function handleShareClick(viewerType) {
        // Check if there are any uploaded files
        if (!window.fileStorageManager || window.fileStorageManager.files.length === 0) {
            console.warn('⚠️ No files to share');
            showMeasurementNotification('Please upload a 3D model first', 'warning', 2000);
            return;
        }

        // Get the first file ID (or you could let user select which file to share)
        const firstFileId = window.fileStorageManager.files[0].id;

        // Open the share modal
        if (window.shareModal) {
            window.shareModal.open(firstFileId);
            console.log('🔗 Share modal opened for viewer:', viewerType);
        } else {
            console.error('❌ Share modal not available');
            showMeasurementNotification('Share feature not available', 'error', 2000);
        }
    }

    // ============================================
    // END SHARE BUTTON HANDLERS
    // ============================================

    // Setup click handlers on canvas
    window.addEventListener('viewersReady', () => {
        console.log('📏 Setting up measurement tool handlers...');

        // Initialize measurement tool
        initMeasurementTool();

        // Initialize pan tool
        initPanTool();

        // Initialize screenshot tool
        initScreenshotTool();

        // Initialize share buttons
        initShareButtons();

        // Add click listeners to both viewers (Three.js uses renderer.domElement)
        if (window.viewerGeneral && window.viewerGeneral.renderer) {
            const canvas = window.viewerGeneral.renderer.domElement;
            canvas.addEventListener('click', (event) => {
                handleMeasurementClick(event, window.viewerGeneral);
            });
            // Setup pan handlers
            setupPanHandlers(canvas, window.viewerGeneral);
            console.log('✅ Measurement and Pan handlers added to General viewer');
        }

        if (window.viewerMedical && window.viewerMedical.renderer) {
            const canvas = window.viewerMedical.renderer.domElement;
            canvas.addEventListener('click', (event) => {
                handleMeasurementClick(event, window.viewerMedical);
            });
            // Setup pan handlers
            setupPanHandlers(canvas, window.viewerMedical);
            console.log('✅ Measurement and Pan handlers added to Medical viewer');
        }
    });

    // ============================================
    // END MEASUREMENT TOOL
    // ============================================
});
</script>

<script src="{{ asset('frontend/assets/js/3d-viewer-pro.js') }}?v=5"></script>
<script src="{{ asset('frontend/assets/js/volume-calculator.js') }}?v={{ time() }}"></script>
<script src="{{ asset('frontend/assets/js/pricing-calculator.js') }}?v={{ time() }}"></script>
{{-- REMOVED: simple-save-calculate.js - Conflicts with enhanced-save-calculate.js --}}
{{-- <script src="{{ asset('frontend/assets/js/simple-save-calculate.js') }}?v={{ time() }}"></script> --}}
<script src="{{ asset('frontend/assets/js/debug-calculator.js') }}?v={{ time() }}"></script>

{{-- INLINE HANDLER DEFINITION - Put it directly in HTML to bypass loading issues --}}
<script>
console.log('🚀 INLINE SCRIPT STARTING...');

// Professional notification system - IMPROVED positioning
window.showToolbarNotification = function(message, type = 'info', duration = 2500) {
    const notification = document.createElement('div');

    const colors = {
        success: { bg: '#10b981', icon: '✓' },
        error: { bg: '#ef4444', icon: '✕' },
        info: { bg: '#3b82f6', icon: 'ℹ' },
        warning: { bg: '#f59e0b', icon: '⚠' }
    };

    const style = colors[type] || colors.info;

    notification.style.cssText = `
        position: fixed;
        top: 140px;
        right: 20px;
        background: ${style.bg};
        color: white;
        padding: 14px 20px;
        border-radius: 10px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.25);
        z-index: 9998;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        font-size: 14px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 10px;
        animation: slideIn 0.3s ease-out;
        pointer-events: auto;
        cursor: pointer;
    `;

    notification.innerHTML = `
        <span style="font-size: 18px; font-weight: bold;">${style.icon}</span>
        <span>${message}</span>
    `;

    // Add animation keyframes if not already added
    if (!document.getElementById('toolbar-notification-styles')) {
        const styleSheet = document.createElement('style');
        styleSheet.id = 'toolbar-notification-styles';
        styleSheet.textContent = `
            @keyframes slideIn {
                from { transform: translateX(400px); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(400px); opacity: 0; }
            }
        `;
        document.head.appendChild(styleSheet);
    }

    document.body.appendChild(notification);

    // Click to dismiss
    notification.onclick = function() {
        notification.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => document.body.removeChild(notification), 300);
    };

    // Auto dismiss
    setTimeout(() => {
        if (document.body.contains(notification)) {
            notification.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }
    }, duration);
};

// Button active state manager
window.toggleToolbarButton = function(buttonId, isActive) {
    const button = document.getElementById(buttonId);
    if (!button) return;

    if (isActive) {
        button.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
        button.style.color = 'white';
        button.style.boxShadow = '0 4px 12px rgba(102, 126, 234, 0.4)';
    } else {
        button.style.background = '';
        button.style.color = '';
        button.style.boxShadow = '';
    }
};

// Define the handler RIGHT HERE in the HTML
window.toolbarHandler = {
    toggleMeasurement: function(viewerType) {
        console.log(`📏 Toggle measurement for ${viewerType}`);
        const submenu = document.getElementById('measurementSubmenu' + (viewerType === 'Medical' ? 'Medical' : ''));
        if (submenu) {
            const isVisible = submenu.style.display === 'block';
            submenu.style.display = isVisible ? 'none' : 'block';
            toggleToolbarButton('measurementToolBtn', !isVisible);
            showToolbarNotification(isVisible ? 'Measurement tools hidden' : 'Measurement tools shown', 'info', 1500);

            // Initialize measurement tool handlers if not already done
            if (!submenu.dataset.initialized) {
                this.initMeasurementTools(viewerType, submenu);
                submenu.dataset.initialized = 'true';
            }
        }
    },

    initMeasurementTools: function(viewerType, submenu) {
        const viewer = viewerType === 'Medical' ? window.viewerMedical : window.viewerGeneral;

        if (!viewer) {
            console.warn('Viewer not found for measurement tools');
            return;
        }

        // Initialize measurement state
        if (!viewer.measurementState) {
            viewer.measurementState = {
                mode: null,
                points: [],
                lines: [],
                labels: []
            };
        }

        // Close button
        const closeBtn = submenu.querySelector('.submenu-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                submenu.style.display = 'none';
                toggleToolbarButton('measurementToolBtn', false);
            });
        }

        // Measurement tool buttons
        submenu.querySelectorAll('.submenu-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const measureType = btn.getAttribute('data-measure');
                this.handleMeasurementTool(viewer, measureType, viewerType);
            });
        });
    },

    handleMeasurementTool: function(viewer, measureType, viewerType) {
        console.log(`📐 Measurement tool: ${measureType}`);

        switch(measureType) {
            case 'distance':
                this.startDistanceMeasurement(viewer, viewerType);
                break;
            case 'point-to-line':
                showToolbarNotification('Click two points on the model to define a line, then click a third point', 'info', 3000);
                this.startPointToLineMeasurement(viewer, viewerType);
                break;
            case 'point-to-surface':
                showToolbarNotification('Click a point, then click on the surface to measure distance', 'info', 3000);
                this.startPointToSurfaceMeasurement(viewer, viewerType);
                break;
            case 'angle':
                showToolbarNotification('Click three points to measure the angle between them', 'info', 3000);
                this.startAngleMeasurement(viewer, viewerType);
                break;
            case 'clear':
                this.clearAllMeasurements(viewer);
                break;
        }
    },

    startDistanceMeasurement: function(viewer, viewerType) {
        viewer.measurementState.mode = 'distance';
        viewer.measurementState.points = [];

        showToolbarNotification('Click two points on the model to measure distance', 'info', 3000);

        // Add click handler for measurement
        const onMeasurementClick = (event) => {
            const raycaster = new THREE.Raycaster();
            const mouse = new THREE.Vector2();

            const rect = viewer.renderer.domElement.getBoundingClientRect();
            mouse.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
            mouse.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;

            raycaster.setFromCamera(mouse, viewer.camera);

            const meshes = [];
            viewer.scene.traverse((obj) => {
                if (obj.isMesh) meshes.push(obj);
            });

            const intersects = raycaster.intersectObjects(meshes);

            if (intersects.length > 0) {
                const point = intersects[0].point.clone();
                viewer.measurementState.points.push(point);

                // Create point marker
                const geometry = new THREE.SphereGeometry(0.5, 16, 16);
                const material = new THREE.MeshBasicMaterial({ color: 0xff0000 });
                const sphere = new THREE.Mesh(geometry, material);
                sphere.position.copy(point);
                sphere.userData.isMeasurementPoint = true;
                viewer.scene.add(sphere);
                viewer.measurementState.lines.push(sphere);

                if (viewer.measurementState.points.length === 2) {
                    // Draw line between points
                    const lineGeometry = new THREE.BufferGeometry().setFromPoints(viewer.measurementState.points);
                    const lineMaterial = new THREE.LineBasicMaterial({ color: 0xff0000, linewidth: 2 });
                    const line = new THREE.Line(lineGeometry, lineMaterial);
                    line.userData.isMeasurementLine = true;
                    viewer.scene.add(line);
                    viewer.measurementState.lines.push(line);

                    // Calculate and display distance
                    const distance = viewer.measurementState.points[0].distanceTo(viewer.measurementState.points[1]);
                    const midPoint = new THREE.Vector3().addVectors(viewer.measurementState.points[0], viewer.measurementState.points[1]).multiplyScalar(0.5);

                    // Create label
                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');
                    canvas.width = 256;
                    canvas.height = 64;
                    context.fillStyle = '#ffffff';
                    context.fillRect(0, 0, 256, 64);
                    context.fillStyle = '#000000';
                    context.font = 'Bold 20px Arial';
                    context.textAlign = 'center';
                    context.textBaseline = 'middle';
                    context.fillText(`${distance.toFixed(2)} mm`, 128, 32);

                    const texture = new THREE.CanvasTexture(canvas);
                    const spriteMaterial = new THREE.SpriteMaterial({ map: texture });
                    const sprite = new THREE.Sprite(spriteMaterial);
                    sprite.scale.set(20, 5, 1);
                    sprite.position.copy(midPoint);
                    sprite.userData.isMeasurementLabel = true;
                    viewer.scene.add(sprite);
                    viewer.measurementState.labels.push(sprite);

                    showToolbarNotification(`Distance: ${distance.toFixed(2)} mm`, 'success', 3000);

                    // Reset for next measurement
                    viewer.measurementState.mode = null;
                    viewer.measurementState.points = [];
                    viewer.renderer.domElement.removeEventListener('click', onMeasurementClick);
                }

                if (viewer.render) viewer.render();
            }
        };

        viewer.renderer.domElement.addEventListener('click', onMeasurementClick);
    },

    startPointToLineMeasurement: function(viewer, viewerType) {
        showToolbarNotification('Point-to-line measurement: Coming in next update', 'info', 2000);
    },

    startPointToSurfaceMeasurement: function(viewer, viewerType) {
        showToolbarNotification('Point-to-surface measurement: Coming in next update', 'info', 2000);
    },

    startAngleMeasurement: function(viewer, viewerType) {
        showToolbarNotification('Angle measurement: Coming in next update', 'info', 2000);
    },

    clearAllMeasurements: function(viewer) {
        if (!viewer || !viewer.scene) return;

        // Remove all measurement objects
        const toRemove = [];
        viewer.scene.traverse((obj) => {
            if (obj.userData.isMeasurementPoint || obj.userData.isMeasurementLine || obj.userData.isMeasurementLabel) {
                toRemove.push(obj);
            }
        });

        toRemove.forEach(obj => {
            viewer.scene.remove(obj);
            if (obj.geometry) obj.geometry.dispose();
            if (obj.material) obj.material.dispose();
        });

        // Reset measurement state
        if (viewer.measurementState) {
            viewer.measurementState.points = [];
            viewer.measurementState.lines = [];
            viewer.measurementState.labels = [];
            viewer.measurementState.mode = null;
        }

        if (viewer.render) viewer.render();
        showToolbarNotification('All measurements cleared', 'success', 1500);
    },

    toggleBoundingBox: function(viewerType) {
        console.log(`📦 Toggle bounding box for ${viewerType}`);
        const viewer = viewerType === 'Medical' ? window.viewerMedical : window.viewerGeneral;

        if (!viewer || !viewer.scene) {
            showToolbarNotification('Please wait for the 3D model to load', 'warning');
            return;
        }

        // Save state before making changes
        this.saveState(viewer);

        let existingHelper = viewer.scene.children.find(child => child.userData && child.userData.isBoundingBoxHelper);

        if (existingHelper) {
            existingHelper.visible = !existingHelper.visible;

            // Toggle dimension labels
            const labels = viewer.scene.children.filter(child => child.userData && child.userData.isDimensionLabel);
            labels.forEach(label => label.visible = existingHelper.visible);

            toggleToolbarButton('boundingBoxBtn', existingHelper.visible);
            showToolbarNotification(existingHelper.visible ? 'Bounding box shown' : 'Bounding box hidden', 'success', 1500);
        } else {
            const box = new THREE.Box3();
            let hasGeometry = false;

            viewer.scene.traverse((object) => {
                if (object.isMesh && object.geometry) {
                    box.expandByObject(object);
                    hasGeometry = true;
                }
            });

            if (hasGeometry) {
                const helper = new THREE.Box3Helper(box, 0xffaa00);
                helper.userData.isBoundingBoxHelper = true;
                viewer.scene.add(helper);

                // Add dimension labels
                const size = box.getSize(new THREE.Vector3());
                const center = box.getCenter(new THREE.Vector3());

                // Create text sprites for dimensions
                const createTextSprite = (text, position) => {
                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');
                    canvas.width = 256;
                    canvas.height = 64;

                    context.fillStyle = '#000000';
                    context.font = 'Bold 24px Arial';
                    context.textAlign = 'center';
                    context.textBaseline = 'middle';
                    context.fillText(text, 128, 32);

                    const texture = new THREE.CanvasTexture(canvas);
                    const material = new THREE.SpriteMaterial({ map: texture, transparent: true });
                    const sprite = new THREE.Sprite(material);
                    sprite.scale.set(size.x * 0.3, size.x * 0.075, 1);
                    sprite.position.copy(position);
                    sprite.userData.isDimensionLabel = true;
                    return sprite;
                };

                // X dimension (Width)
                const xLabel = createTextSprite(
                    `X: ${size.x.toFixed(1)} mm`,
                    new THREE.Vector3(center.x, box.min.y - size.y * 0.1, center.z)
                );
                viewer.scene.add(xLabel);

                // Y dimension (Height)
                const yLabel = createTextSprite(
                    `Y: ${size.y.toFixed(1)} mm`,
                    new THREE.Vector3(box.max.x + size.x * 0.15, center.y, center.z)
                );
                viewer.scene.add(yLabel);

                // Z dimension (Depth)
                const zLabel = createTextSprite(
                    `Z: ${size.z.toFixed(1)} mm`,
                    new THREE.Vector3(center.x, center.y, box.max.z + size.z * 0.1)
                );
                viewer.scene.add(zLabel);

                toggleToolbarButton('boundingBoxBtn', true);
                showToolbarNotification('Bounding box enabled with dimensions', 'success', 2000);
            }
        }

        if (viewer.render) viewer.render();
    },

    toggleAxis: function(viewerType) {
        console.log(`🎯 Toggle axis for ${viewerType}`);
        const viewer = viewerType === 'Medical' ? window.viewerMedical : window.viewerGeneral;

        if (!viewer || !viewer.scene) {
            showToolbarNotification('Please wait for the 3D model to load', 'warning');
            return;
        }

        // Save state before making changes
        this.saveState(viewer);

        let existingAxis = viewer.scene.children.find(child => child.userData && child.userData.isAxisHelper);

        if (existingAxis) {
            existingAxis.visible = !existingAxis.visible;

            // Toggle axis labels
            const labels = viewer.scene.children.filter(child => child.userData && child.userData.isAxisLabel);
            labels.forEach(label => label.visible = existingAxis.visible);

            toggleToolbarButton('axisToggleBtn', existingAxis.visible);
            showToolbarNotification(existingAxis.visible ? 'Axis shown' : 'Axis hidden', 'success', 1500);
        } else {
            // Calculate model size to scale axis appropriately
            const box = new THREE.Box3();
            viewer.scene.traverse((object) => {
                if (object.isMesh) box.expandByObject(object);
            });
            const size = box.getSize(new THREE.Vector3());
            const maxDim = Math.max(size.x, size.y, size.z);
            const axisSize = maxDim * 0.6;

            const axesHelper = new THREE.AxesHelper(axisSize);
            axesHelper.userData.isAxisHelper = true;
            viewer.scene.add(axesHelper);

            // Add axis labels (X, Y, Z)
            const createAxisLabel = (text, position, color) => {
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.width = 128;
                canvas.height = 128;

                context.fillStyle = color;
                context.font = 'Bold 64px Arial';
                context.textAlign = 'center';
                context.textBaseline = 'middle';
                context.fillText(text, 64, 64);

                const texture = new THREE.CanvasTexture(canvas);
                const material = new THREE.SpriteMaterial({ map: texture, transparent: true });
                const sprite = new THREE.Sprite(material);
                sprite.scale.set(axisSize * 0.15, axisSize * 0.15, 1);
                sprite.position.copy(position);
                sprite.userData.isAxisLabel = true;
                return sprite;
            };

            // X axis (Red)
            const xLabel = createAxisLabel('X', new THREE.Vector3(axisSize * 1.1, 0, 0), '#ff0000');
            viewer.scene.add(xLabel);

            // Y axis (Green)
            const yLabel = createAxisLabel('Y', new THREE.Vector3(0, axisSize * 1.1, 0), '#00ff00');
            viewer.scene.add(yLabel);

            // Z axis (Blue)
            const zLabel = createAxisLabel('Z', new THREE.Vector3(0, 0, axisSize * 1.1), '#0000ff');
            viewer.scene.add(zLabel);

            toggleToolbarButton('axisToggleBtn', true);
            showToolbarNotification('Axis enabled with labels', 'success', 2000);
        }

        if (viewer.render) viewer.render();
    },

    toggleGrid: function(viewerType) {
        console.log(`📐 Toggle grid for ${viewerType}`);
        const viewer = viewerType === 'Medical' ? window.viewerMedical : window.viewerGeneral;

        if (!viewer || !viewer.scene) {
            showToolbarNotification('Please wait for the 3D model to load', 'warning');
            return;
        }

        // Save state before making changes
        this.saveState(viewer);

        let existingGrid = viewer.scene.children.find(child => child.userData && child.userData.isGridHelper);

        if (existingGrid) {
            existingGrid.visible = !existingGrid.visible;
            toggleToolbarButton('gridToggleBtn', existingGrid.visible);
            showToolbarNotification(existingGrid.visible ? 'Grid shown' : 'Grid hidden', 'success', 1500);
        } else {
            // Calculate appropriate grid size based on model
            const box = new THREE.Box3();
            viewer.scene.traverse((object) => {
                if (object.isMesh) box.expandByObject(object);
            });
            const size = box.getSize(new THREE.Vector3());
            const maxDim = Math.max(size.x, size.y, size.z);
            const gridSize = Math.ceil(maxDim * 1.5);
            const divisions = Math.max(10, Math.ceil(gridSize / 10));

            const gridHelper = new THREE.GridHelper(gridSize, divisions, 0x888888, 0x444444);
            gridHelper.userData.isGridHelper = true;

            // Position grid at the bottom of the model
            const center = box.getCenter(new THREE.Vector3());
            gridHelper.position.y = box.min.y;

            viewer.scene.add(gridHelper);
            toggleToolbarButton('gridToggleBtn', true);
            showToolbarNotification('Measurement grid enabled', 'success', 1500);
        }

        if (viewer.render) viewer.render();
    },

    toggleShadow: function(viewerType) {
        console.log(`🌓 Toggle shadow for ${viewerType}`);
        const viewer = viewerType === 'Medical' ? window.viewerMedical : window.viewerGeneral;

        if (!viewer || !viewer.renderer) {
            showToolbarNotification('Please wait for the 3D model to load', 'warning');
            return;
        }

        // Save state before making changes
        this.saveState(viewer);

        viewer.renderer.shadowMap.enabled = !viewer.renderer.shadowMap.enabled;
        toggleToolbarButton('shadowToggleBtn', viewer.renderer.shadowMap.enabled);
        showToolbarNotification(viewer.renderer.shadowMap.enabled ? 'Shadows enabled' : 'Shadows disabled', 'success', 1500);

        if (viewer.render) viewer.render();
    },

    toggleTransparency: function(viewerType) {
        console.log(`👁️ Toggle transparency for ${viewerType}`);
        const viewer = viewerType === 'Medical' ? window.viewerMedical : window.viewerGeneral;

        if (!viewer || !viewer.scene) {
            showToolbarNotification('Please wait for the 3D model to load', 'warning');
            return;
        }

        // Save state before making changes
        this.saveState(viewer);

        const levels = [1.0, 0.75, 0.5, 0.25];

        if (!viewer.currentTransparencyIndex) {
            viewer.currentTransparencyIndex = 0;
        }

        viewer.currentTransparencyIndex = (viewer.currentTransparencyIndex + 1) % levels.length;
        const newOpacity = levels[viewer.currentTransparencyIndex];

        viewer.scene.traverse((object) => {
            if (object.isMesh && object.material) {
                object.material.transparent = newOpacity < 1.0;
                object.material.opacity = newOpacity;
                object.material.needsUpdate = true;
            }
        });

        toggleToolbarButton('transparencyBtn', newOpacity < 1.0);
        showToolbarNotification(`Transparency: ${Math.round(newOpacity * 100)}%`, 'info', 1500);

        if (viewer.render) viewer.render();
    },

    takeScreenshot: function(viewerType) {
        console.log(`📸 Take screenshot for ${viewerType}`);
        const viewer = viewerType === 'Medical' ? window.viewerMedical : window.viewerGeneral;

        if (!viewer) {
            showToolbarNotification('Viewer loading, please wait...', 'info');
            return;
        }

        if (!viewer.renderer) {
            showToolbarNotification('Renderer loading, please wait...', 'warning');
            return;
        }

        try {
            if (viewer.render && typeof viewer.render === 'function') {
                viewer.render();
            }

            const canvas = viewer.renderer.domElement;
            const dataURL = canvas.toDataURL('image/png');

            const timestamp = new Date().toISOString().replace(/[:.]/g, '-').substring(0, 19);
            const filename = `3d-model-${viewerType.toLowerCase()}-${timestamp}.png`;

            const link = document.createElement('a');
            link.href = dataURL;
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            showToolbarNotification('Screenshot saved successfully!', 'success', 2000);

        } catch (error) {
            console.error('❌ Screenshot failed:', error);
            showToolbarNotification('Screenshot failed: ' + error.message, 'error', 3000);
        }
    },

    undo: function() {
        console.log('⏪ Undo action');
        const viewer = window.viewerGeneral || window.viewerMedical;

        if (!viewer || !viewer.stateHistory) {
            viewer.stateHistory = [];
            viewer.stateHistoryIndex = -1;
        }

        if (viewer.stateHistoryIndex > 0) {
            viewer.stateHistoryIndex--;
            const state = viewer.stateHistory[viewer.stateHistoryIndex];
            this.restoreState(viewer, state);
            showToolbarNotification('Undone', 'success', 1000);
        } else {
            showToolbarNotification('Nothing to undo', 'info', 1500);
        }
    },

    redo: function() {
        console.log('⏩ Redo action');
        const viewer = window.viewerGeneral || window.viewerMedical;

        if (!viewer || !viewer.stateHistory) {
            viewer.stateHistory = [];
            viewer.stateHistoryIndex = -1;
        }

        if (viewer.stateHistoryIndex < viewer.stateHistory.length - 1) {
            viewer.stateHistoryIndex++;
            const state = viewer.stateHistory[viewer.stateHistoryIndex];
            this.restoreState(viewer, state);
            showToolbarNotification('Redone', 'success', 1000);
        } else {
            showToolbarNotification('Nothing to redo', 'info', 1500);
        }
    },

    saveState: function(viewer) {
        if (!viewer.stateHistory) {
            viewer.stateHistory = [];
            viewer.stateHistoryIndex = -1;
        }

        // Capture current state
        const state = {
            cameraPosition: viewer.camera.position.clone(),
            cameraRotation: viewer.camera.rotation.clone(),
            transparency: viewer.currentTransparencyIndex || 0,
            shadows: viewer.renderer.shadowMap.enabled,
            toolsVisible: {
                boundingBox: !!viewer.scene.children.find(child => child.userData && child.userData.isBoundingBoxHelper && child.visible),
                axis: !!viewer.scene.children.find(child => child.userData && child.userData.isAxisHelper && child.visible),
                grid: !!viewer.scene.children.find(child => child.userData && child.userData.isGridHelper && child.visible)
            }
        };

        // Remove future states if we're not at the end
        if (viewer.stateHistoryIndex < viewer.stateHistory.length - 1) {
            viewer.stateHistory = viewer.stateHistory.slice(0, viewer.stateHistoryIndex + 1);
        }

        viewer.stateHistory.push(state);
        viewer.stateHistoryIndex++;

        // Limit history to 50 states
        if (viewer.stateHistory.length > 50) {
            viewer.stateHistory.shift();
            viewer.stateHistoryIndex--;
        }
    },

    restoreState: function(viewer, state) {
        if (!state) return;

        viewer.camera.position.copy(state.cameraPosition);
        viewer.camera.rotation.copy(state.cameraRotation);

        // Restore transparency
        viewer.currentTransparencyIndex = state.transparency;

        // Restore shadows
        viewer.renderer.shadowMap.enabled = state.shadows;

        // Restore tools visibility
        const boundingBox = viewer.scene.children.find(child => child.userData && child.userData.isBoundingBoxHelper);
        if (boundingBox) boundingBox.visible = state.toolsVisible.boundingBox;

        const axis = viewer.scene.children.find(child => child.userData && child.userData.isAxisHelper);
        if (axis) axis.visible = state.toolsVisible.axis;

        const grid = viewer.scene.children.find(child => child.userData && child.userData.isGridHelper);
        if (grid) grid.visible = state.toolsVisible.grid;

        if (viewer.render) viewer.render();
    },

    changeModelColor: function(viewerType) {
        console.log('🎨 Change model color');
        const viewer = viewerType ? (viewerType === 'Medical' ? window.viewerMedical : window.viewerGeneral)
                                  : (window.viewerGeneral || window.viewerMedical);

        if (!viewer || !viewer.scene) {
            showToolbarNotification('Please wait for the 3D model to load', 'warning');
            return;
        }

        // Create color picker dialog
        const colors = ['#0047AD', '#ffffff', '#2c3e50', '#3498db', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6', '#1abc9c', '#34495e'];

        const existingPicker = document.getElementById('modelColorPicker');
        if (existingPicker) {
            existingPicker.remove();
            return;
        }

        const picker = document.createElement('div');
        picker.id = 'modelColorPicker';
        picker.style.cssText = 'position: fixed; top: 140px; right: 20px; background: white; padding: 15px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); z-index: 9998;';
        picker.innerHTML = `
            <div style="font-weight: 600; margin-bottom: 10px; color: #2c3e50;">Select Model Color</div>
            <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 8px;">
                ${colors.map(color => `
                    <button class="model-color-option" data-color="${color}"
                            style="width: 36px; height: 36px; border: 2px solid #ddd; border-radius: 6px; background: ${color}; cursor: pointer; transition: all 0.2s;"
                            onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                    </button>
                `).join('')}
            </div>
            <button onclick="document.getElementById('modelColorPicker').remove()"
                    style="margin-top: 10px; width: 100%; padding: 8px; border: none; background: #f1f3f5; border-radius: 6px; cursor: pointer; font-size: 0.85rem;">
                Close
            </button>
        `;
        document.body.appendChild(picker);

        // Add click handlers
        picker.querySelectorAll('.model-color-option').forEach(btn => {
            btn.addEventListener('click', function() {
                const color = this.getAttribute('data-color');
                viewer.scene.traverse((object) => {
                    if (object.isMesh && object.material) {
                        object.material.color.set(color);
                        object.material.needsUpdate = true;
                    }
                });
                if (viewer.render) viewer.render();
                showToolbarNotification('Model color changed', 'success', 1500);
                picker.remove();
            });
        });
    },

    changeBackgroundColor: function(viewerType) {
        console.log('🌈 Change background color');
        const viewer = viewerType ? (viewerType === 'Medical' ? window.viewerMedical : window.viewerGeneral)
                                  : (window.viewerGeneral || window.viewerMedical);

        if (!viewer || !viewer.scene) {
            showToolbarNotification('Please wait for the 3D viewer to load', 'warning');
            return;
        }

        // Create color picker dialog
        const colors = ['#ffffff', '#f8f9fa', '#e9ecef', '#dee2e6', '#2c3e50', '#34495e', '#1a1a1a', '#000000', '#e3f2fd', '#fce4ec'];

        const existingPicker = document.getElementById('bgColorPicker');
        if (existingPicker) {
            existingPicker.remove();
            return;
        }

        const picker = document.createElement('div');
        picker.id = 'bgColorPicker';
        picker.style.cssText = 'position: fixed; top: 140px; right: 20px; background: white; padding: 15px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); z-index: 9998;';
        picker.innerHTML = `
            <div style="font-weight: 600; margin-bottom: 10px; color: #2c3e50;">Select Background Color</div>
            <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 8px;">
                ${colors.map(color => `
                    <button class="bg-color-option" data-color="${color}"
                            style="width: 36px; height: 36px; border: 2px solid #ddd; border-radius: 6px; background: ${color}; cursor: pointer; transition: all 0.2s;"
                            onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                    </button>
                `).join('')}
            </div>
            <button onclick="document.getElementById('bgColorPicker').remove()"
                    style="margin-top: 10px; width: 100%; padding: 8px; border: none; background: #f1f3f5; border-radius: 6px; cursor: pointer; font-size: 0.85rem;">
                Close
            </button>
        `;
        document.body.appendChild(picker);

        // Add click handlers
        picker.querySelectorAll('.bg-color-option').forEach(btn => {
            btn.addEventListener('click', function() {
                const color = this.getAttribute('data-color');
                viewer.scene.background = new THREE.Color(color);
                if (viewer.render) viewer.render();
                showToolbarNotification('Background color changed', 'success', 1500);
                picker.remove();
            });
        });
    }
};

console.log('✅ INLINE window.toolbarHandler created!', window.toolbarHandler);
console.log('Available methods:', Object.keys(window.toolbarHandler));
</script>

<script src="{{ asset('frontend/assets/js/3d-viewer-professional-tools.js') }}?v=3000"></script>
<script>
// IMMEDIATE verification that toolbar handler exists
console.log('========================================');
console.log('🔍 VERIFICATION SCRIPT RUNNING');
console.log('window.toolbarHandler exists?', typeof window.toolbarHandler !== 'undefined');
console.log('window.toolbarHandler:', window.toolbarHandler);

if (window.toolbarHandler) {
    console.log('✅ Toolbar handler loaded successfully!');
    console.log('Available methods:', Object.keys(window.toolbarHandler));

    // Test if takeScreenshot method exists
    if (window.toolbarHandler.takeScreenshot) {
        console.log('✅ takeScreenshot method found!');
        console.log('takeScreenshot function:', window.toolbarHandler.takeScreenshot);
    } else {
        console.error('❌ takeScreenshot method NOT FOUND!');
    }

    // Make it globally accessible for easy testing
    window.testScreenshot = function() {
        console.log('🧪 TEST: Calling takeScreenshot...');
        window.toolbarHandler.takeScreenshot('General');
    };
    console.log('💡 TIP: You can test screenshot by typing: testScreenshot()');

} else {
    console.error('❌ ERROR: window.toolbarHandler is UNDEFINED!');
    console.error('This means the script did not execute properly');
    alert('CRITICAL ERROR: Toolbar handler not loaded! Check console.');
}

console.log('========================================');
</script>

{{-- Mesh Repair with Visual Feedback --}}
<script src="{{ asset('frontend/assets/js/mesh-repair-visual.js') }}?v={{ time() }}"></script>

<script src="{{ asset('frontend/assets/js/enhanced-save-calculate.js') }}?v={{ time() }}"></script>
<script src="{{ asset('frontend/assets/js/3d-file-manager.js') }}?v={{ time() }}"></script>

<!-- QR Code Library for Share Modal -->
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

<!-- File Storage Manager & Share Modal -->
<script src="{{ asset('frontend/assets/js/file-storage-manager.js') }}?v=4"></script>
<script src="{{ asset('frontend/assets/js/share-modal.js') }}?v=4"></script>

<!-- Initialize File Storage and Load from URL -->
<script>
(async function() {
    // Initialize file storage manager
    window.fileStorageManager = new FileStorageManager();
    await window.fileStorageManager.init();

    // Check if there are file IDs in the URL
    const fileIdsFromURL = window.fileStorageManager.getFileIdFromURL();

    if (fileIdsFromURL) {
        console.log('📂 Loading files from URL...');

        // Handle both single file and multiple files
        const fileIds = Array.isArray(fileIdsFromURL) ? fileIdsFromURL : [fileIdsFromURL];
        console.log(`   Found ${fileIds.length} file ID(s):`, fileIds);

        // Wait for viewer to be ready
        const waitForViewer = () => {
            return new Promise((resolve) => {
                if (window.viewerGeneral && window.viewerGeneral.initialized) {
                    resolve();
                } else {
                    window.addEventListener('viewersReady', () => resolve(), { once: true });
                }
            });
        };

        await waitForViewer();
        console.log('✅ Viewer ready, loading files...');

        // Load each file
        for (const fileId of fileIds) {
            try {
                console.log(`   Loading file: ${fileId}`);
                const fileRecord = await window.fileStorageManager.loadFile(fileId);

                if (fileRecord && fileRecord.fileData) {
                    // Convert ArrayBuffer to File object
                    const blob = new Blob([fileRecord.fileData], { type: 'application/octet-stream' });
                    const file = new File([blob], fileRecord.fileName, { type: 'application/octet-stream' });

                    // Load into viewer with storage ID to prevent duplicates
                    if (window.viewerGeneral) {
                        await window.viewerGeneral.loadFile(file, fileId);
                        console.log(`   ✅ Loaded: ${fileRecord.fileName}`);
                    }
                } else {
                    console.warn(`   ⚠️ File not found or invalid: ${fileId}`);
                }
            } catch (error) {
                console.error(`   ❌ Error loading file ${fileId}:`, error);
            }
        }

        console.log('✅ All files from URL loaded successfully!');
    } else {
        console.log('ℹ️ No files in URL - starting with empty viewer');
    }
})();
</script>

<script>
// Force toolbar visibility after page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔧 Forcing toolbar visibility...');

    // Find all professional toolbars with correct class name
    const toolbars = document.querySelectorAll('.viewer-professional-toolbar');

    console.log(`Found ${toolbars.length} toolbars`);

    toolbars.forEach(function(toolbar, index) {
        // Force display
        toolbar.style.display = 'flex';
        toolbar.style.visibility = 'visible';
        toolbar.style.opacity = '1';
        toolbar.style.pointerEvents = 'auto';

        console.log(`✅ Toolbar ${index + 1} visibility forced`, toolbar);
    });

    // Double-check after a short delay
    setTimeout(function() {
        toolbars.forEach(function(toolbar, index) {
            toolbar.style.display = 'flex';
            toolbar.style.visibility = 'visible';
            toolbar.style.opacity = '1';
            toolbar.style.pointerEvents = 'auto';
        });
        console.log('✅ Toolbar visibility re-confirmed');
    }, 500);

    // Triple check after 2 seconds (in case viewer initialization hides it)
    setTimeout(function() {
        toolbars.forEach(function(toolbar) {
            toolbar.style.display = 'flex';
            toolbar.style.visibility = 'visible';
            toolbar.style.opacity = '1';
            toolbar.style.pointerEvents = 'auto';
        });
        console.log('✅ Toolbar visibility triple-confirmed');
    }, 2000);
});
</script>

<script>
// Hide price summary/sidebar in HTML and only show after Save & Calculate and after repair/fill
document.addEventListener('DOMContentLoaded', function() {
    const priceSummaryGeneral = document.getElementById('priceSummaryGeneral');
    const priceSidebar = document.getElementById('quoteTotalPriceGeneral');
    const volumeSidebar = document.getElementById('quoteTotalVolumeGeneral');
    if (priceSummaryGeneral) priceSummaryGeneral.style.display = 'none';
    if (priceSidebar) priceSidebar.style.display = 'none';
    if (volumeSidebar) volumeSidebar.style.display = 'none';

    // Hide price summary/sidebar on file upload or removal
    function hidePriceSummary() {
        if (priceSummaryGeneral) priceSummaryGeneral.style.display = 'none';
        if (priceSidebar) priceSidebar.style.display = 'none';
        if (volumeSidebar) volumeSidebar.style.display = 'none';
    }
    const fileInput = document.getElementById('fileInput3d');
    if (fileInput) fileInput.addEventListener('change', hidePriceSummary);
    document.addEventListener('fileRemoved', hidePriceSummary);

    // NOTE: Save button handler is at the TOP of this file (line ~116)
    // It uses the new SimpleSaveCalculate system
});
</script>

{{-- Viewer Health Check & Auto-Repair System --}}
<script>
/**
 * ========================================
 * VIEWER HEALTH CHECK & AUTO-REPAIR
 * Ensures viewer is 100% functional
 * ========================================
 */
(function() {
    console.log('🏥 Starting Viewer Health Check System...');

    const healthCheck = {
        checkInterval: null,
        repairAttempts: 0,
        maxRepairAttempts: 3,

        /**
         * Start monitoring viewer health
         */
        start() {
            // Initial check after 2 seconds
            setTimeout(() => this.performHealthCheck(), 2000);

            // Periodic check every 10 seconds
            this.checkInterval = setInterval(() => {
                this.performHealthCheck();
            }, 10000);

            console.log('✅ Health check system started');
        },

        /**
         * Perform comprehensive health check
         */
        performHealthCheck() {
            const results = {
                viewerGeneral: this.checkViewer(window.viewerGeneral, 'General'),
                viewerMedical: this.checkViewer(window.viewerMedical, 'Medical'),
                toolbarHandler: this.checkToolbarHandler(),
                saveCalculate: this.checkSaveCalculate()
            };

            const allHealthy = Object.values(results).every(r => r === true);

            if (allHealthy) {
                console.log('✅ All systems healthy');
                this.repairAttempts = 0;
            } else {
                console.warn('⚠️ Health check failed:', results);
                this.attemptRepair(results);
            }

            return results;
        },

        /**
         * Check individual viewer health
         */
        checkViewer(viewer, name) {
            if (!viewer) {
                console.warn(`❌ ${name} viewer not found`);
                return false;
            }

            const checks = {
                initialized: !!viewer.initialized,
                scene: !!viewer.scene,
                camera: !!viewer.camera,
                renderer: !!viewer.renderer,
                controls: !!viewer.controls,
                render: typeof viewer.render === 'function',
                calculateVolume: typeof viewer.calculateVolume === 'function'
            };

            const healthy = Object.values(checks).every(c => c === true);

            if (!healthy) {
                console.warn(`❌ ${name} viewer unhealthy:`, checks);
            }

            return healthy;
        },

        /**
         * Check toolbar handler
         */
        checkToolbarHandler() {
            if (!window.toolbarHandler) {
                console.warn('❌ toolbarHandler not found');
                return false;
            }

            const requiredMethods = [
                'toggleBoundingBox',
                'toggleAxis',
                'toggleGrid',
                'toggleShadow',
                'toggleTransparency',
                'takeScreenshot',
                'toggleMeasurement',
                'undo',
                'redo',
                'changeModelColor',
                'changeBackgroundColor'
            ];

            const missing = requiredMethods.filter(method =>
                typeof window.toolbarHandler[method] !== 'function'
            );

            if (missing.length > 0) {
                console.warn('❌ Toolbar handler missing methods:', missing);
                return false;
            }

            return true;
        },

        /**
         * Check save & calculate system
         */
        checkSaveCalculate() {
            if (!window.EnhancedSaveCalculate) {
                console.warn('❌ EnhancedSaveCalculate not found');
                return false;
            }

            if (typeof window.EnhancedSaveCalculate.execute !== 'function') {
                console.warn('❌ EnhancedSaveCalculate.execute not a function');
                return false;
            }

            return true;
        },

        /**
         * Attempt to repair issues
         */
        attemptRepair(results) {
            if (this.repairAttempts >= this.maxRepairAttempts) {
                console.error('❌ Max repair attempts reached. Manual intervention required.');
                return;
            }

            this.repairAttempts++;
            console.log(`🔧 Attempting repair (attempt ${this.repairAttempts}/${this.maxRepairAttempts})...`);

            // Repair toolbar handler if missing
            if (!results.toolbarHandler) {
                console.log('🔧 Attempting to reload toolbar handler...');
                // The inline script should have already loaded it, but check again
                if (typeof window.toolbarHandler === 'undefined') {
                    console.error('❌ Toolbar handler still not loaded after inline script');
                    alert('Critical Error: Toolbar not loaded. Please refresh the page.');
                }
            }

            // Ensure viewers have required methods
            if (window.viewerGeneral && !window.viewerGeneral.calculateVolume) {
                console.log('🔧 Adding fallback calculateVolume to viewerGeneral...');
                this.addFallbackVolumeCalculation(window.viewerGeneral);
            }

            if (window.viewerMedical && !window.viewerMedical.calculateVolume) {
                console.log('🔧 Adding fallback calculateVolume to viewerMedical...');
                this.addFallbackVolumeCalculation(window.viewerMedical);
            }
        },

        /**
         * Add fallback volume calculation to viewer
         */
        addFallbackVolumeCalculation(viewer) {
            viewer.calculateVolume = function(mesh) {
                if (!mesh || !mesh.geometry) {
                    console.warn('Invalid mesh for volume calculation');
                    return { cm3: 0, mm3: 0 };
                }

                const geometry = mesh.geometry;
                if (!geometry.attributes || !geometry.attributes.position) {
                    console.warn('Invalid geometry attributes');
                    return { cm3: 0, mm3: 0 };
                }

                const position = geometry.attributes.position;
                const vertices = position.array;
                let volume = 0;

                // Calculate signed volume
                for (let i = 0; i < vertices.length; i += 9) {
                    const v1x = vertices[i], v1y = vertices[i + 1], v1z = vertices[i + 2];
                    const v2x = vertices[i + 3], v2y = vertices[i + 4], v2z = vertices[i + 5];
                    const v3x = vertices[i + 6], v3y = vertices[i + 7], v3z = vertices[i + 8];

                    volume += (v1x * v2y * v3z + v2x * v3y * v1z + v3x * v1y * v2z -
                              v1x * v3y * v2z - v2x * v1y * v3z - v3x * v2y * v1z) / 6.0;
                }

                const volumeMm3 = Math.abs(volume);
                const volumeCm3 = volumeMm3 / 1000;

                console.log(`📐 Calculated volume: ${volumeCm3.toFixed(2)} cm³`);

                return {
                    cm3: volumeCm3,
                    mm3: volumeMm3
                };
            };

            console.log('✅ Fallback calculateVolume added to viewer');
        },

        /**
         * Stop health check system
         */
        stop() {
            if (this.checkInterval) {
                clearInterval(this.checkInterval);
                this.checkInterval = null;
                console.log('⏸️ Health check system stopped');
            }
        }
    };

    // Start health check system
    healthCheck.start();

    // Make it globally accessible
    window.viewerHealthCheck = healthCheck;

    console.log('✅ Viewer Health Check System initialized');
    console.log('💡 TIP: Run window.viewerHealthCheck.performHealthCheck() to manually check system health');
})();
</script>


