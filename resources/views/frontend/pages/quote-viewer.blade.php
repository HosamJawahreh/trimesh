{{-- ============================================
     3D QUOTE VIEWER - FULL SCREEN INTERFACE
     Professional & Optimized Layout
     Version: 2025-12-24-21:00 - MODAL & EYE BUTTON FIX
     ============================================ --}}

{{-- EMERGENCY DIAGNOSTIC BUTTON --}}

<script>
function runEmergencyDiagnostic() {
    console.clear();
    console.log('%c=== EMERGENCY DIAGNOSTIC ===', 'font-size:20px; background:#ff0000; color:white; padding:10px;');

    // Test 1: Viewer objects
    console.log('\n1️⃣ VIEWER OBJECTS:');
    console.log('window.viewerGeneral:', window.viewerGeneral);
    console.log('window.viewerMedical:', window.viewerMedical);
    console.log('window.viewer:', window.viewer);

    // Test 2: Toolbar handler
    console.log('\n2️⃣ TOOLBAR HANDLER:');
    console.log('window.toolbarHandler:', window.toolbarHandler);
    if (window.toolbarHandler) {
        console.log('Methods:', Object.keys(window.toolbarHandler).slice(0, 10));
    }

    // Test 3: Try grid toggle
    console.log('\n3️⃣ TESTING GRID TOGGLE:');
    if (window.toolbarHandler && window.toolbarHandler._checkViewer) {
        window.toolbarHandler._checkViewer();
    }
    if (window.toolbarHandler && window.toolbarHandler.toggleGridMain) {
        window.toolbarHandler.toggleGridMain('General');
    } else {
        console.error('❌ toggleGridMain not found!');
    }

    // Test 4: Canvas check
    console.log('\n4️⃣ CANVAS CHECK:');
    if (window.viewerGeneral && window.viewerGeneral.renderer) {
        const canvas = window.viewerGeneral.renderer.domElement;
        console.log('Canvas:', canvas);
        console.log('Canvas size:', canvas.width, 'x', canvas.height);
    } else {
        console.error('❌ No renderer found!');
    }

    console.log('\n%c=== DIAGNOSTIC COMPLETE ===', 'font-size:16px; background:#00ff00; color:black; padding:10px;');
    alert('✅ Diagnostic complete! Check console (F12) for results.');
}
</script>

<section class="dgm-3d-quote-area pb-100" >
    <div class="container">
        {{-- General 3D Printing Form --}}
        <div class="quote-form-container-3d" id="generalForm3d" style="display: block;">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-11">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            <div class="row g-0">

                                {{-- Left Sidebar: Form Controls --}}
                                <div class="col-12 col-lg-3" style="display: none !important;">
                                    <div class="p-3 p-lg-4">

                                        {{-- Site Logo - Moved to top toolbar --}}
                                        {{-- <div class="text-center" style="padding: 0 0 15px 0;">
                                            <img src="{{ asset($settings->logo) }}" alt="{{ $settings->app_name }}" style="max-width: 200px; height: auto;">
                                        </div> --}}

                                        {{-- Category tabs removed - upload from bottom bar --}}
                                        {{-- Upload area hidden - use bottom bar upload button --}}

                                        <!-- Hidden file inputs for compatibility -->
                                        <input type="file" id="fileInput3d" style="display: none;" accept=".stl,.obj,.ply" multiple>

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

                                        <!-- Price Summary - Hidden, now shown in bottom bar -->
                                        <div id="priceSummaryGeneral" class="mt-3 p-3" style="display: none !important; background: white; border-radius: 12px; border: 2px solid #e9ecef; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">

<script>
// Global viewer type - Store at page load to prevent loss during URL changes
const GLOBAL_VIEWER_TYPE = (function() {
    const urlParams = new URLSearchParams(window.location.search);
    const viewerParam = urlParams.get('viewer') || 'general';
    console.log('🎯 GLOBAL_VIEWER_TYPE initialized:', viewerParam);
    return viewerParam;
})();

// Price display moved to right panel
document.addEventListener('DOMContentLoaded', function() {
    const priceSummaryGeneral = document.getElementById('priceSummaryGeneral');
    if (priceSummaryGeneral) priceSummaryGeneral.style.display = 'none';

    // Hide price from bottom bar initially
    const bottomPriceDisplay = document.getElementById('bottomPriceDisplay');
    if (bottomPriceDisplay) bottomPriceDisplay.classList.remove('show');

    // Hide files panel on mobile initially
    if (window.innerWidth <= 768) {
        const filesPanel = document.getElementById('rightFilesPanel');
        if (filesPanel) {
            filesPanel.style.display = 'none';
            console.log('📱 Mobile detected: Files panel hidden by default');
        }
    }

    // Handle window resize - hide files panel when switching to mobile
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            const filesPanel = document.getElementById('rightFilesPanel');
            if (filesPanel && window.innerWidth <= 768) {
                // On mobile, hide panel if not actively open
                if (!filesPanel.classList.contains('mobile-open')) {
                    filesPanel.style.display = 'none';
                }
            } else if (filesPanel && window.innerWidth > 768) {
                // On desktop, always show panel
                filesPanel.style.display = 'flex';
                filesPanel.classList.remove('mobile-open');
                document.body.style.overflow = '';
            }
        }, 250);
    });

    // ESC key to cancel active measurement tool
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            // Cancel measurement tool if active
            if (window.measurementManager && window.measurementManager.activeTool) {
                const viewer = window.viewerGeneral || window.viewer;
                if (viewer) {
                    window.measurementManager.cancelTool(viewer);
                    showToolbarNotification('Measurement tool cancelled', 'info');
                }
            }
            // Fallback to old handler
            else if (window.toolbarHandler && window.toolbarHandler.activeTool) {
                window.toolbarHandler.cancelActiveTool();
            }
        }
    });

    // Sync sidebar price to right panel
    function syncPriceToRightPanel() {
        const sidebarPrice = document.getElementById('quoteTotalPriceGeneral');
        const sidebarVolume = document.getElementById('quoteTotalVolumeGeneral');
        const rightPanelPrice = document.getElementById('rightPanelPrice');
        const rightPanelVolume = document.getElementById('rightPanelVolume');
        const rightPanelSummary = document.getElementById('rightPanelPriceSummary');
        const bottomPrice = document.getElementById('bottomTotalPrice');
        const bottomVolume = document.getElementById('bottomTotalVolume');
        const bottomDisplay = document.getElementById('bottomPriceDisplay');

        if (sidebarPrice && rightPanelPrice && rightPanelSummary) {
            const priceText = sidebarPrice.textContent || '$0.00';
            const volumeText = sidebarVolume ? (sidebarVolume.textContent || '0 cm³') : '0 cm³';

            // Update right panel
            rightPanelPrice.textContent = priceText;
            if (rightPanelVolume) rightPanelVolume.textContent = volumeText;

            // Also sync to bottom bar if it exists
            if (bottomPrice) bottomPrice.textContent = priceText;
            if (bottomVolume) bottomVolume.textContent = volumeText;

            // Show/hide right panel price summary based on price value
            if (sidebarPrice.style.display !== 'none' && priceText !== '$0' && priceText !== '$0.00') {
                rightPanelSummary.style.display = 'block';
                if (bottomDisplay) bottomDisplay.classList.add('show');
            } else {
                rightPanelSummary.style.display = 'none';
                if (bottomDisplay) bottomDisplay.classList.remove('show');
            }
        }
    }

    // Watch for price and volume changes
    const observer = new MutationObserver(syncPriceToRightPanel);
    const priceElement = document.getElementById('quoteTotalPriceGeneral');
    const volumeElement = document.getElementById('quoteTotalVolumeGeneral');

    if (priceElement) {
        observer.observe(priceElement, {
            childList: true,
            characterData: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['style']
        });
    }

    if (volumeElement) {
        observer.observe(volumeElement, {
            childList: true,
            characterData: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['style']
        });
    }

    // Initial sync
    syncPriceToRightPanel();

    // Connect right panel Request Quote button to sidebar button
    const rightPanelQuoteBtn = document.getElementById('rightPanelQuoteBtn');
    const sidebarQuoteBtn = document.getElementById('btnRequestQuoteGeneral');
    if (rightPanelQuoteBtn && sidebarQuoteBtn) {
        rightPanelQuoteBtn.addEventListener('click', function() {
            sidebarQuoteBtn.click();
        });
    }

    // Handle Request Quote button - Submit to review page
    if (sidebarQuoteBtn) {
        sidebarQuoteBtn.addEventListener('click', async function(e) {
            e.preventDefault();

            // Use the global viewer type set at page load
            const viewerType = GLOBAL_VIEWER_TYPE;
            console.log('🎯 REQUEST QUOTE - Current URL:', window.location.href);
            console.log('🎯 REQUEST QUOTE - Using GLOBAL viewer type:', viewerType);
            const viewerId = viewerType === 'medical' ? 'medical' : 'general';

            // Get the active viewer - dental uses viewerGeneral with different settings
            const viewer = viewerType === 'medical' ? window.viewerMedical || window.viewer : window.viewerGeneral || window.viewer;

            console.log('🔍 Selected viewer:', viewer ? 'Found' : 'NOT FOUND');
            console.log('🔍 Uploaded files:', viewer ? viewer.uploadedFiles?.length : 0);

            if (!viewer || !viewer.uploadedFiles || viewer.uploadedFiles.length === 0) {
                alert('Please upload and calculate files first!');
                return;
            }

            // Get pricing data
            const totalPriceEl = document.getElementById('quoteTotalPriceGeneral') || document.getElementById('quoteTotalPriceMedical');
            const totalVolumeEl = document.getElementById('quoteTotalVolumeGeneral') || document.getElementById('quoteTotalVolumeMedical');
            const totalFilesEl = document.getElementById('quoteTotalFilesGeneral') || document.getElementById('quoteTotalFilesMedical');

            const totalPrice = totalPriceEl ? parseFloat(totalPriceEl.textContent.replace(/[^0-9.]/g, '')) || 0 : 0;
            const totalVolume = totalVolumeEl ? parseFloat(totalVolumeEl.textContent.replace(/[^0-9.]/g, '')) || 0 : 0;
            const totalFiles = totalFilesEl ? parseInt(totalFilesEl.textContent) || 0 : viewer.uploadedFiles.length;

            if (totalPrice === 0) {
                alert('Please calculate the price first by clicking "Save & Calculate"!');
                return;
            }

            try {
                // Fetch the last saved quote from the database
                console.log('🔍 Fetching last saved quote for viewer:', viewerId);
                const response = await fetch(`/api/quotes/latest?form_type=${viewerId}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                    }
                });

                if (!response.ok) {
                    throw new Error('Could not fetch saved quote. Please try Save & Calculate again.');
                }

                const savedQuote = await response.json();
                console.log('✅ Retrieved saved quote:', savedQuote);

                // Get global settings from the saved quote
                const quoteData = savedQuote.data || {};
                const globalTechnology = quoteData.technology || 'fdm';
                const globalMaterial = quoteData.material || 'pla';
                const globalColor = quoteData.color || '#0047AD';
                const globalQuality = quoteData.quality || 'standard';
                const globalInfill = quoteData.infill || '20';
                const globalLayerHeight = quoteData.layer_height || '0.2';

                // Map the pricing breakdown to files data with actual prices
                const pricingBreakdown = quoteData.pricing_breakdown || [];

                console.log('📊 Pricing breakdown:', pricingBreakdown);

                const filesData = pricingBreakdown.map(item => {
                    const filePrice = parseFloat(item.price) || 0;
                    const fileVolume = parseFloat(item.volume_cm3) || 0;

                    console.log(`  File: ${item.file_name}, Volume: ${fileVolume}, Price: ${filePrice}`);

                    return {
                        name: item.file_name || 'Unknown',
                        technology: globalTechnology,
                        material: globalMaterial,
                        color: globalColor,
                        quality: globalQuality,
                        infill: globalInfill,
                        layer_height: globalLayerHeight,
                        volume: fileVolume,
                        price: filePrice
                    };
                });

                // Prepare quote data with actual total price from saved quote
                // IMPORTANT: Use GLOBAL_VIEWER_TYPE from page load, not from database
                const quoteData2 = {
                    quote_id: quoteData.id,
                    viewer_type: GLOBAL_VIEWER_TYPE, // Use the global constant set at page load
                    viewer_link: window.location.href,
                    total_price: parseFloat(quoteData.total_price) || totalPrice,
                    total_volume: parseFloat(quoteData.total_volume_cm3) || totalVolume,
                    total_files: pricingBreakdown.length || totalFiles,
                    technology: globalTechnology,
                    material: globalMaterial,
                    color: globalColor,
                    quality: globalQuality,
                    files: filesData
                };

                console.log('📦 Quote Data being sent to review:', quoteData2);
                console.log('🔍 VIEWER TYPE IN QUOTE DATA:', quoteData2.viewer_type);
                console.log('🔍 GLOBAL_VIEWER_TYPE:', GLOBAL_VIEWER_TYPE);

                // Send to server and redirect to review page
                const reviewResponse = await fetch('{{ route("printing-order.review") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ quote_data: quoteData2 })
                });

                if (reviewResponse.ok) {
                    // Redirect to review page
                    window.location.href = '{{ route("printing-order.review") }}';
                } else {
                    throw new Error('Failed to save quote data to session');
                }
            } catch (error) {
                console.error('❌ Error:', error);
                alert(error.message || 'An error occurred. Please try again.');
            }
        });
    }

    // Hide price summary and sidebar price on file upload or removal
    function hidePriceSummary() {
        if (priceSummaryGeneral) priceSummaryGeneral.style.display = 'none';
        // Hide sidebar price
        const priceSidebar = document.querySelector('.total-price, #quoteTotalPriceGeneral');
        if (priceSidebar) priceSidebar.textContent = '';
        // Hide volume
        const volumeSidebar = document.getElementById('quoteTotalVolumeGeneral');
        if (volumeSidebar) volumeSidebar.textContent = '';
        // Hide bottom bar price
        const bottomDisplay = document.getElementById('bottomPriceDisplay');
        if (bottomDisplay) bottomDisplay.classList.remove('show');
        // Hide right panel price summary
        const rightPanelSummary = document.getElementById('rightPanelPriceSummary');
        if (rightPanelSummary) rightPanelSummary.style.display = 'none';
    }
    const fileInput = document.getElementById('fileInput3d');
    if (fileInput) fileInput.addEventListener('change', hidePriceSummary);
    document.addEventListener('fileRemoved', hidePriceSummary);

    // Apply dental-specific settings if viewer is dental or dental-viewer
    function applyDentalSettings() {
        const urlParams = new URLSearchParams(window.location.search);
        const viewerType = urlParams.get('viewer');

        console.log('🦷 Checking viewer type:', viewerType);

        if (viewerType === 'dental' || viewerType === 'dental-viewer') {
            console.log('🦷 Applying dental-specific settings...');

            // Set Technology to SLA / DLP (already default in Medical form)
            const technologySelect = document.getElementById('technologySelectMedical');
            if (technologySelect) {
                technologySelect.value = 'sla'; // SLA / DLP
                console.log('✅ Technology set to: SLA / DLP');
            }

            // Set Material to Biocompatible resins (already default)
            const materialSelect = document.getElementById('materialSelectMedical');
            if (materialSelect) {
                materialSelect.value = 'biocompatible-resin';
                console.log('✅ Material set to: Biocompatible resins');
            }

            // Colors are already limited and certified (no changes needed)
            console.log('✅ Colors: Limited, certified (already set)');

            // Layer Height is already Fixed, validated (shown in UI as locked)
            console.log('✅ Layer Height: Fixed, validated (already set)');

            console.log('🦷 Dental settings applied successfully!');
        }
    }

    // Apply dental settings on load
    applyDentalSettings();

    // Reapply when viewer type changes (listen for URL changes)
    window.addEventListener('popstate', applyDentalSettings);

    // Attach to ALL Save & Calculate buttons (there are multiple with same ID - invalid HTML but we'll handle it)
    const saveBtns = document.querySelectorAll('#saveCalculationsBtn, .save-btn');
    console.log('🔍 Found', saveBtns.length, 'Save buttons');
    saveBtns.forEach((saveBtn, index) => {
        console.log('📌 Attaching handler to button', index);
        saveBtn.addEventListener('click', async function() {
            console.log('💾 SAVE & CALCULATE STARTED - Button', index);
            // Trigger sync after calculation
            setTimeout(syncPriceToRightPanel, 500);

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
                                            {{-- Pricing Section - Completely removed, now in bottom bar --}}
                                            <div style="display: none !important;">
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
                                </div>

                                {{-- Right Side: 3D Viewer --}}
                                <div class="col-12 col-lg-12 position-relative d-flex flex-column" style="display: flex !important; visibility: visible !important; opacity: 1 !important; flex: 1 !important; min-width: 0 !important; background: linear-gradient(to bottom, #b8c5d6 0%, #99a8ba 100%) !important;">

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

                                        {{-- NEW Right Side Files Panel --}}
                                        <div class="right-files-panel" id="rightFilesPanel" style="position: absolute !important; top: 100px !important; right: 20px !important; width: 320px !important; max-height: calc(100vh - 120px) !important; background: white !important; border-radius: 12px !important; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15) !important; overflow: hidden !important; display: flex !important; flex-direction: column !important; z-index: 9998 !important;">
                                            {{-- Panel Header --}}
                                            <div style="padding: 16px; border-bottom: 1px solid #e0e0e0; display: flex; justify-content: space-between; align-items: center;">
                                                <h6 style="margin: 0; font-weight: 600; font-size: 14px; color: #333;">Uploaded Files</h6>
                                                <div style="display: flex; gap: 8px; align-items: center;">
                                                    {{-- Mobile Close Button (visible only on mobile) --}}
                                                    <button type="button" class="mobile-close-files-btn" onclick="window.toolbarHandler.toggleMobileFilesPanel()" title="Close" style="display: none; background: #e0e0e0; border: none; cursor: pointer; padding: 6px 10px; color: #333; border-radius: 6px; font-size: 18px; font-weight: bold; line-height: 1; transition: all 0.2s;">
                                                        ×
                                                    </button>
                                                    {{-- Upload More Button --}}
                                                    <button type="button" id="uploadMoreFilesBtn" style="background: #1976D2; border: none; cursor: pointer; padding: 6px; color: white; border-radius: 6px; display: flex; align-items: center; justify-content: center; transition: all 0.2s;" title="Upload More Files" onclick="document.getElementById('fileInput3d').click()">
                                                        <svg width="18" height="18" viewBox="0 0 20 20" fill="none">
                                                            <path d="M10 5L10 15M5 10L15 10" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                            {{-- Files Container --}}
                                            <div id="rightFilesContainer" style="flex: 1; overflow-y: auto; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; margin: 12px 12px 0 12px;">
                                                <div style="text-align: center; padding: 40px 20px; color: #999;">
                                                    <svg width="48" height="48" viewBox="0 0 48 48" fill="none" style="margin: 0 auto 12px;">
                                                        <path d="M24 8L40 16L24 24L8 16L24 8Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M8 32L24 40L40 32" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M8 24L24 32L40 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                    <p style="margin: 0; font-size: 13px;">No files uploaded yet</p>
                                                </div>
                                            </div>

                                            {{-- Price Summary Section --}}
                                            <div id="rightPanelPriceSummary" style="display: none; padding: 12px; margin: 12px; background: #f5f7fa; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                                {{-- Volume and Price in one line --}}
                                                <div style="display: flex; align-items: center; justify-content: space-between; gap: 20px; margin-bottom: 12px;">
                                                    {{-- Volume --}}
                                                    <div style="flex: 1; text-align: center;">
                                                        <div style="font-size: 0.7rem; color: #6c757d; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">VOLUME</div>
                                                        <div id="rightPanelVolume" style="font-weight: 700; font-size: 1rem; color: #2c3e50;">0 cm³</div>
                                                    </div>

                                                    {{-- Price --}}
                                                    <div style="flex: 1; text-align: center;">
                                                        <div style="font-size: 0.7rem; color: #6c757d; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">TOTAL PRICE</div>
                                                        <div id="rightPanelPrice" style="font-weight: 700; font-size: 1.25rem; color: #4a90e2;">$0.00</div>
                                                    </div>
                                                </div>

                                                {{-- Horizontal line separator --}}
                                                <hr style="border: none; border-top: 1px solid #dee2e6; margin: 12px 0;">

                                                {{-- Request Quote Button on new line --}}
                                                <button type="button" id="rightPanelQuoteBtn" style="width: 100%; border-radius: 6px; font-weight: 600; font-size: 0.85rem; padding: 10px 16px; background: #4a90e2; color: white; border: none; transition: all 0.3s; cursor: pointer;" onmouseover="this.style.background='#357abd'" onmouseout="this.style.background='#4a90e2'">
                                                    Request Quote →
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Active Tool Status Bar --}}
                                        <div class="active-tool-status" id="activeToolStatus">
                                            <div class="tool-icon">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                    <path id="activeToolIcon" d="M4 16L16 4" stroke="white" stroke-width="2" stroke-linecap="round"/>
                                                </svg>
                                            </div>
                                            <div class="tool-name" id="activeToolName">Distance</div>
                                            <div class="tool-instruction" id="activeToolInstruction">Click two points on the model</div>
                                            <button class="cancel-tool" onclick="window.toolbarHandler.cancelActiveTool()">Cancel (ESC)</button>
                                        </div>

                                        {{-- Measurement Results Panel --}}
                                        <div class="measurement-results-panel" id="measurementResultsPanel">
                                            <div class="results-header">
                                                <div style="display: flex; align-items: center; gap: 8px;">
                                                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                                                        <path d="M3 15L15 3M5 15L7 13M9 15L11 13M13 15L15 13M3 13L5 11M3 9L7 5M3 5L5 3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                    </svg>
                                                    <span>Measurements</span>
                                                </div>
                                                <button type="button" class="results-close" onclick="window.toolbarHandler.closeMeasurementsPanel()">×</button>
                                            </div>
                                            <div class="results-body" id="measurementResultsList">
                                                <div class="no-measurements">
                                                    <svg width="40" height="40" viewBox="0 0 48 48" fill="none" style="opacity: 0.3; margin-bottom: 8px;">
                                                        <path d="M8 40L40 8M12 40L16 36M20 40L24 36M28 40L32 36M36 40L40 36M8 36L12 32M8 28L16 20M8 20L12 16M8 12L16 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                    </svg>
                                                    <p>No measurements yet</p>
                                                    <small>Click a measurement tool to start</small>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Professional Toolbar - Top Right --}}
                                        <div class="viewer-professional-toolbar" id="professionalToolbar" style="position: absolute !important; top: 0 !important; left: 0 !important; right: 0 !important; width: 100% !important; display: flex !important; visibility: visible !important; opacity: 1 !important; z-index: 9999 !important; background: rgba(255, 255, 255, 0.95) !important; padding: 8px !important; border-radius: 0 !important; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15) !important; pointer-events: auto !important; gap: 8px !important;">

                                            {{-- Logo Group --}}
                                            <div class="toolbar-group" style="display: flex !important; align-items: center !important; padding-right: 8px !important;">
                                                <a href="{{ url('/') }}" style="display: flex; align-items: center; text-decoration: none; cursor: pointer; transition: opacity 0.2s ease;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'" title="Go to Homepage">
                                                    <img src="{{ asset($settings->logo) }}" alt="{{ $settings->app_name }}" style="max-height: 36px; width: auto;">
                                                </a>
                                            </div>

                                            <div class="toolbar-divider"></div>

                                            {{-- Tools Group --}}
                                            <div class="toolbar-group" style="display: flex !important; gap: 4px !important; visibility: visible !important; opacity: 1 !important;">

                                                {{-- Measurement Tool with Dropdown --}}
                                                <div style="position: relative !important;">
                                                    <button type="button" class="toolbar-btn" id="measurementToolBtn" title="Measurement Tools" data-tool="measurement" onclick="window.toolbarHandler.toggleMeasurement('General')">
                                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                            <path d="M4 16L16 4M6 16L8 14M10 16L12 14M14 16L16 14M4 14L6 12M4 10L8 6M4 6L6 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                        </svg>
                                                    </button>
                                                    {{-- Measurement Sub-Menu (Dropdown under button) --}}
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
                                                        <button type="button" class="submenu-btn" data-measure="diameter">
                                                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                                                                <circle cx="9" cy="9" r="7" stroke="currentColor" stroke-width="1.5"/>
                                                                <path d="M2 9L16 9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                                <circle cx="2" cy="9" r="1.5" fill="currentColor"/>
                                                                <circle cx="16" cy="9" r="1.5" fill="currentColor"/>
                                                            </svg>
                                                            <span>Diameter</span>
                                                        </button>
                                                        <button type="button" class="submenu-btn" data-measure="area">
                                                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                                                                <rect x="2" y="2" width="14" height="14" stroke="currentColor" stroke-width="1.5"/>
                                                                <path d="M2 9L16 9M9 2L9 16" stroke="currentColor" stroke-width="1" opacity="0.3"/>
                                                            </svg>
                                                            <span>Area</span>
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
                                                    </div>
                                                </div>

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
                                                <button type="button" class="toolbar-btn" id="panToolBtn" title="Move Model - Drag to reposition" data-tool="pan" onclick="window.toolbarHandler.toggleMoveMode('General')">
                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                                        <path d="M13 5L13 11M13 11L10 8M13 11L16 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M13 19L13 13M13 13L10 16M13 13L16 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M5 13L11 13M11 13L8 10M11 13L8 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M19 13L13 13M13 13L16 10M13 13L16 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <circle cx="13" cy="13" r="2" fill="currentColor"/>
                                                    </svg>
                                                </button>
                                                <button type="button" class="toolbar-btn" id="autoRotateBtn" title="Auto-rotate Model" data-tool="autoRotate" onclick="window.toolbarHandler.toggleAutoRotate('General')">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <path d="M17 10C17 13.866 13.866 17 10 17C6.13401 17 3 13.866 3 10C3 6.13401 6.13401 3 10 3C12.0605 3 13.8792 3.91099 15 5.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                        <path d="M17 3V7H13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </button>
                                                {{-- Bottom Control Bar Functions - Now in Top Toolbar --}}
                                                <button type="button" class="toolbar-btn" id="toggleGridBtnMain" title="Toggle grid visibility" data-tool="gridMain" onclick="window.toolbarHandler.toggleGridMain('General')">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <rect x="3" y="3" width="5" height="5" stroke="currentColor" stroke-width="1.5"/>
                                                        <rect x="11" y="3" width="5" height="5" stroke="currentColor" stroke-width="1.5"/>
                                                        <rect x="3" y="11" width="5" height="5" stroke="currentColor" stroke-width="1.5"/>
                                                        <rect x="11" y="11" width="5" height="5" stroke="currentColor" stroke-width="1.5"/>
                                                    </svg>
                                                </button>
                                                <button type="button" class="toolbar-btn" id="measureToolBtnMain" title="Measure distance between two points" data-tool="measureMain" onclick="window.toolbarHandler.toggleMeasureMain('General')">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <path d="M2 2L18 18M2 2L2 18M18 2L18 18M2 18L18 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                                        <circle cx="2" cy="2" r="1.5" fill="currentColor"/>
                                                        <circle cx="18" cy="18" r="1.5" fill="currentColor"/>
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

                                            <div class="toolbar-divider"></div>

                                            {{-- Camera View Controls --}}
                                            <div class="toolbar-group">
                                                <button type="button" class="toolbar-btn camera-btn" data-view="top" title="Top View">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                                        <path d="M12 5L12 19M12 5L8 9M12 5L16 9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                    </svg>
                                                </button>
                                                <button type="button" class="toolbar-btn camera-btn active" data-view="front" title="Front View">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
                                                        <circle cx="12" cy="12" r="3" fill="currentColor"/>
                                                    </svg>
                                                </button>
                                                <button type="button" class="toolbar-btn camera-btn" data-view="right" title="Right View">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                                        <path d="M19 12L5 12M19 12L15 8M19 12L15 16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                    </svg>
                                                </button>
                                                <button type="button" class="toolbar-btn camera-btn" data-view="left" title="Left View">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                                        <path d="M5 12L19 12M5 12L9 8M5 12L9 16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                    </svg>
                                                </button>
                                                <button type="button" class="toolbar-btn camera-btn" data-view="bottom" title="Bottom View">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                                        <path d="M12 19L12 5M12 19L8 15M12 19L16 15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                    </svg>
                                                </button>
                                                <button type="button" class="toolbar-btn camera-btn" data-view="reset" title="Reset Camera">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                                        <path d="M12 4V1L8 5L12 9V6C15.31 6 18 8.69 18 12C18 13.01 17.75 13.97 17.3 14.8L18.76 16.26C19.54 15.03 20 13.57 20 12C20 7.58 16.42 4 12 4ZM12 18C8.69 18 6 15.31 6 12C6 10.99 6.25 10.03 6.7 9.2L5.24 7.74C4.46 8.97 4 10.43 4 12C4 16.42 7.58 20 12 20V23L16 19L12 15V18Z" fill="currentColor"/>
                                                    </svg>
                                                </button>
                                            </div>

                                            {{-- Divider --}}
                                            <div style="width: 1px; height: 32px; background: rgba(0,0,0,0.1); margin: 0 4px;"></div>

                                            {{-- Lighting Controls Group - Toggle Buttons --}}
                                            <div class="toolbar-group" style="position: relative;">
                                                <button type="button" class="toolbar-btn" id="lightIntensityBtn" title="Light Intensity" data-tool="lightIntensity" onclick="window.lightingController.toggleLightPanel()">
                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                                        <circle cx="12" cy="12" r="5" stroke="currentColor" stroke-width="2"/>
                                                        <path d="M12 1v3M12 20v3M1 12h3M20 12h3M4.22 4.22l2.12 2.12M17.66 17.66l2.12 2.12M4.22 19.78l2.12-2.12M17.66 6.34l2.12-2.12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                    </svg>
                                                </button>
                                                <button type="button" class="toolbar-btn" id="shadowIntensityBtn" title="Shadow Intensity" data-tool="shadowIntensity" onclick="window.lightingController.toggleShadowPanel()">
                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                                        <ellipse cx="12" cy="19" rx="8" ry="3" fill="currentColor" opacity="0.5"/>
                                                        <circle cx="12" cy="8" r="6" stroke="currentColor" stroke-width="2" fill="none"/>
                                                    </svg>
                                                </button>
                                                <button type="button" class="toolbar-btn" id="lightRotationBtn" title="Light Rotation" data-tool="lightRotation" onclick="window.lightingController.toggleRotationPanel()">
                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/>
                                                        <circle cx="12" cy="12" r="3" fill="currentColor"/>
                                                        <path d="M12 2L12 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                        <path d="M12 2 A10 10 0 0 1 17 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity="0.4"/>
                                                    </svg>
                                                </button>

                                                {{-- Light Intensity Panel --}}
                                                <div id="lightIntensityPanel" class="lighting-control-panel" style="display: none; position: absolute; top: 48px; left: 0; background: white; padding: 16px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 1000; min-width: 240px;">
                                                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                                                        <div style="display: flex; align-items: center; gap: 8px;">
                                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" style="color: #f39c12;">
                                                                <circle cx="12" cy="12" r="5" stroke="currentColor" stroke-width="2"/>
                                                                <path d="M12 1v3M12 20v3M1 12h3M20 12h3M4.22 4.22l2.12 2.12M17.66 17.66l2.12 2.12M4.22 19.78l2.12-2.12M17.66 6.34l2.12-2.12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                            </svg>
                                                            <span style="font-weight: 600; color: #333; font-size: 14px;">Light Intensity</span>
                                                        </div>
                                                        <button type="button" onclick="window.lightingController.toggleLightPanel()" style="background: none; border: none; cursor: pointer; color: #999; font-size: 22px; line-height: 1; padding: 0; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;" title="Close">&times;</button>
                                                    </div>
                                                    <div style="display: flex; align-items: center; gap: 12px;">
                                                        <input type="range" 
                                                               id="lightIntensitySlider" 
                                                               class="lighting-slider"
                                                               min="0" 
                                                               max="2" 
                                                               step="0.1" 
                                                               value="0.9"
                                                               title="Adjust Light Intensity (0-200%)"
                                                               style="flex: 1; height: 6px; border-radius: 3px; background: linear-gradient(to right, #f39c12 0%, #f39c12 45%, #e0e0e0 45%, #e0e0e0 100%); outline: none; -webkit-appearance: none; appearance: none; cursor: pointer;">
                                                        <span id="lightIntensityValue" style="font-weight: 700; color: #f39c12; min-width: 45px; text-align: right; font-size: 14px;">45%</span>
                                                    </div>
                                                </div>

                                                {{-- Shadow Intensity Panel --}}
                                                <div id="shadowIntensityPanel" class="lighting-control-panel" style="display: none; position: absolute; top: 48px; left: 0; background: white; padding: 16px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 1000; min-width: 240px;">
                                                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                                                        <div style="display: flex; align-items: center; gap: 8px;">
                                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" style="color: #7f8c8d;">
                                                                <ellipse cx="12" cy="19" rx="8" ry="3" fill="currentColor" opacity="0.5"/>
                                                                <circle cx="12" cy="8" r="6" stroke="currentColor" stroke-width="2" fill="none"/>
                                                            </svg>
                                                            <span style="font-weight: 600; color: #333; font-size: 14px;">Shadow Intensity</span>
                                                        </div>
                                                        <button type="button" onclick="window.lightingController.toggleShadowPanel()" style="background: none; border: none; cursor: pointer; color: #999; font-size: 22px; line-height: 1; padding: 0; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;" title="Close">&times;</button>
                                                    </div>
                                                    <div style="display: flex; align-items: center; gap: 12px;">
                                                        <input type="range" 
                                                               id="shadowIntensitySlider" 
                                                               class="lighting-slider"
                                                               min="0" 
                                                               max="1" 
                                                               step="0.05" 
                                                               value="1.0"
                                                               title="Adjust Shadow Intensity (0-100%)"
                                                               style="flex: 1; height: 6px; border-radius: 3px; background: linear-gradient(to right, #7f8c8d 0%, #7f8c8d 100%, #e0e0e0 100%, #e0e0e0 100%); outline: none; -webkit-appearance: none; appearance: none; cursor: pointer;">
                                                        <span id="shadowIntensityValue" style="font-weight: 700; color: #7f8c8d; min-width: 45px; text-align: right; font-size: 14px;">100%</span>
                                                    </div>
                                                </div>

                                                {{-- Light Rotation Panel --}}
                                                <div id="lightRotationPanel" class="lighting-control-panel" style="display: none; position: absolute; top: 48px; left: 0; background: white; padding: 16px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 1000; min-width: 240px;">
                                                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                                                        <div style="display: flex; align-items: center; gap: 8px;">
                                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" style="color: #9b59b6;">
                                                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/>
                                                                <circle cx="12" cy="12" r="3" fill="currentColor"/>
                                                                <path d="M12 2L12 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                                <path d="M12 2 A10 10 0 0 1 17 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity="0.4"/>
                                                            </svg>
                                                            <span style="font-weight: 600; color: #333; font-size: 14px;">Light Rotation</span>
                                                        </div>
                                                        <button type="button" onclick="window.lightingController.toggleRotationPanel()" style="background: none; border: none; cursor: pointer; color: #999; font-size: 22px; line-height: 1; padding: 0; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;" title="Close">&times;</button>
                                                    </div>
                                                    <div style="display: flex; align-items: center; gap: 12px;">
                                                        <input type="range" 
                                                               id="lightRotationSlider" 
                                                               class="lighting-slider"
                                                               min="0" 
                                                               max="360" 
                                                               step="5" 
                                                               value="45"
                                                               title="Rotate Light Source (0-360°)"
                                                               style="flex: 1; height: 6px; border-radius: 3px; background: linear-gradient(to right, #9b59b6 0%, #9b59b6 12.5%, #e0e0e0 12.5%, #e0e0e0 100%); outline: none; -webkit-appearance: none; appearance: none; cursor: pointer;">
                                                        <span id="lightRotationValue" style="font-weight: 700; color: #9b59b6; min-width: 45px; text-align: right; font-size: 14px;">45°</span>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Divider --}}
                                            <div style="width: 1px; height: 32px; background: rgba(0,0,0,0.1); margin: 0 4px;"></div>

                                            {{-- Share Button --}}
                                            <div class="toolbar-group">
                                                <button type="button" class="toolbar-btn" id="shareToolBtn" title="Share" data-action="share" onclick="window.toolbarHandler.shareModel()">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <circle cx="15" cy="5" r="2.5" stroke="currentColor" stroke-width="1.8"/>
                                                        <circle cx="5" cy="10" r="2.5" stroke="currentColor" stroke-width="1.8"/>
                                                        <circle cx="15" cy="15" r="2.5" stroke="currentColor" stroke-width="1.8"/>
                                                        <path d="M7.5 11L12.5 14M7.5 9L12.5 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                    </svg>
                                                </button>
                                            </div>

                                            {{-- Spacer to push Save button to the right --}}
                                            <div style="flex: 1;"></div>

                                            {{-- Mobile Burger Menu Button --}}
                                            <button type="button" class="toolbar-burger-menu" id="toolbarBurgerMenu" onclick="window.toolbarHandler.toggleMobileMenu()">
                                                <span></span>
                                                <span></span>
                                                <span></span>
                                            </button>

                                            {{-- Save & Continue Button (Far Right with padding) --}}
                                            <div class="toolbar-group" style="display: flex !important; visibility: visible !important; opacity: 1 !important; margin-left: auto !important; padding-right: 12px !important;">
                                                <button type="button" class="toolbar-btn-save-continue" id="saveCalculateToolBtn" title="Save & Continue" data-action="saveCalculate" onclick="window.toolbarHandler.saveAndCalculate('General')">
                                                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none" style="flex-shrink: 0;">
                                                        <path d="M16 18H4C3 18 2 17 2 16V4C2 3 3 2 4 2H13L18 7V16C18 17 17 18 16 18Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M14 18V11H6V18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M6 2V6H13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <circle cx="10" cy="14.5" r="1" fill="currentColor"/>
                                                    </svg>
                                                    <span style="font-weight: 600; font-size: 14px; margin-left: 8px;">Save & Continue</span>
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Mobile Menu Overlay --}}
                                        <div class="toolbar-mobile-menu" id="toolbarMobileMenu">
                                            {{-- Tools Section --}}
                                            <div class="toolbar-mobile-section">
                                                <div class="toolbar-mobile-section-title">Tools</div>
                                                <div class="toolbar-mobile-grid">
                                                    <button type="button" class="toolbar-mobile-btn" onclick="window.toolbarHandler.toggleMeasurement('General'); window.toolbarHandler.toggleMobileMenu();">
                                                        <svg width="24" height="24" viewBox="0 0 20 20" fill="none"><path d="M4 16L16 4M6 16L8 14M10 16L12 14M14 16L16 14M4 14L6 12M4 10L8 6M4 6L6 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                                        <span class="toolbar-mobile-btn-label">Measure</span>
                                                    </button>
                                                    <button type="button" class="toolbar-mobile-btn" onclick="window.toolbarHandler.toggleBoundingBox('General'); window.toolbarHandler.toggleMobileMenu();">
                                                        <svg width="24" height="24" viewBox="0 0 20 20" fill="none"><rect x="3" y="3" width="14" height="14" stroke="currentColor" stroke-width="1.8" stroke-dasharray="2 2"/></svg>
                                                        <span class="toolbar-mobile-btn-label">Bounding Box</span>
                                                    </button>
                                                    <button type="button" class="toolbar-mobile-btn" onclick="window.toolbarHandler.toggleAxis('General'); window.toolbarHandler.toggleMobileMenu();">
                                                        <svg width="24" height="24" viewBox="0 0 20 20" fill="none"><path d="M10 2V18M2 10H18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                                        <span class="toolbar-mobile-btn-label">Axis</span>
                                                    </button>
                                                    <button type="button" class="toolbar-mobile-btn" onclick="window.toolbarHandler.toggleGrid('General'); window.toolbarHandler.toggleMobileMenu();">
                                                        <svg width="24" height="24" viewBox="0 0 20 20" fill="none"><path d="M2 6H18M2 10H18M2 14H18M6 2V18M10 2V18M14 2V18" stroke="currentColor" stroke-width="1.5" opacity="0.6"/></svg>
                                                        <span class="toolbar-mobile-btn-label">Grid</span>
                                                    </button>
                                                    <button type="button" class="toolbar-mobile-btn" onclick="window.toolbarHandler.toggleMoveMode('General'); window.toolbarHandler.toggleMobileMenu();">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M13 5L13 11M13 11L10 8M13 11L16 8" stroke="currentColor" stroke-width="2"/></svg>
                                                        <span class="toolbar-mobile-btn-label">Move</span>
                                                    </button>
                                                </div>
                                            </div>

                                            {{-- View Options --}}
                                            <div class="toolbar-mobile-section">
                                                <div class="toolbar-mobile-section-title">View Options</div>
                                                <div class="toolbar-mobile-grid">
                                                    <button type="button" class="toolbar-mobile-btn" onclick="window.toolbarHandler.toggleShadow('General'); window.toolbarHandler.toggleMobileMenu();">
                                                        <svg width="24" height="24" viewBox="0 0 20 20" fill="none"><ellipse cx="10" cy="16" rx="6" ry="2" fill="currentColor" opacity="0.5"/><circle cx="10" cy="8" r="4" stroke="currentColor" stroke-width="1.8"/></svg>
                                                        <span class="toolbar-mobile-btn-label">Shadows</span>
                                                    </button>
                                                    <button type="button" class="toolbar-mobile-btn" onclick="window.toolbarHandler.toggleTransparency('General'); window.toolbarHandler.toggleMobileMenu();">
                                                        <svg width="24" height="24" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.8" opacity="0.5"/></svg>
                                                        <span class="toolbar-mobile-btn-label">Transparency</span>
                                                    </button>
                                                    <button type="button" class="toolbar-mobile-btn" onclick="window.toolbarHandler.changeModelColor(); window.toolbarHandler.toggleMobileMenu();">
                                                        <svg width="24" height="24" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.8"/><circle cx="10" cy="10" r="3" fill="currentColor"/></svg>
                                                        <span class="toolbar-mobile-btn-label">Color</span>
                                                    </button>
                                                    <button type="button" class="toolbar-mobile-btn" onclick="window.toolbarHandler.changeBackgroundColor(); window.toolbarHandler.toggleMobileMenu();">
                                                        <svg width="24" height="24" viewBox="0 0 20 20" fill="none"><rect x="2" y="2" width="16" height="16" rx="2" stroke="currentColor" stroke-width="1.8"/></svg>
                                                        <span class="toolbar-mobile-btn-label">Background</span>
                                                    </button>
                                                </div>
                                            </div>

                                            {{-- Actions --}}
                                            <div class="toolbar-mobile-section">
                                                <div class="toolbar-mobile-section-title">Actions</div>
                                                <div class="toolbar-mobile-grid">
                                                    <button type="button" class="toolbar-mobile-btn" onclick="window.toolbarHandler.undo(); window.toolbarHandler.toggleMobileMenu();">
                                                        <svg width="24" height="24" viewBox="0 0 20 20" fill="none"><path d="M4 8H14C16 8 18 10 18 12C18 14 16 16 14 16H10M4 8L7 5M4 8L7 11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                        <span class="toolbar-mobile-btn-label">Undo</span>
                                                    </button>
                                                    <button type="button" class="toolbar-mobile-btn" onclick="window.toolbarHandler.redo(); window.toolbarHandler.toggleMobileMenu();">
                                                        <svg width="24" height="24" viewBox="0 0 20 20" fill="none"><path d="M16 8H6C4 8 2 10 2 12C2 14 4 16 6 16H10M16 8L13 5M16 8L13 11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                        <span class="toolbar-mobile-btn-label">Redo</span>
                                                    </button>
                                                    <button type="button" class="toolbar-mobile-btn" onclick="window.toolbarHandler.takeScreenshot('General'); window.toolbarHandler.toggleMobileMenu();">
                                                        <svg width="24" height="24" viewBox="0 0 20 20" fill="none"><rect x="2" y="4" width="16" height="12" rx="2" stroke="currentColor" stroke-width="1.8"/><circle cx="10" cy="10" r="2.5" stroke="currentColor" stroke-width="1.8"/></svg>
                                                        <span class="toolbar-mobile-btn-label">Screenshot</span>
                                                    </button>
                                                    <button type="button" class="toolbar-mobile-btn" onclick="window.toolbarHandler.shareModel(); window.toolbarHandler.toggleMobileMenu();">
                                                        <svg width="24" height="24" viewBox="0 0 20 20" fill="none"><circle cx="15" cy="5" r="2.5" stroke="currentColor" stroke-width="1.8"/><circle cx="5" cy="10" r="2.5" stroke="currentColor" stroke-width="1.8"/><circle cx="15" cy="15" r="2.5" stroke="currentColor" stroke-width="1.8"/><path d="M7.5 11L12.5 14M7.5 9L12.5 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                                        <span class="toolbar-mobile-btn-label">Share</span>
                                                    </button>
                                                </div>
                                            </div>

                                            {{-- Camera Views --}}
                                            <div class="toolbar-mobile-section">
                                                <div class="toolbar-mobile-section-title">Camera Views</div>
                                                <div class="toolbar-mobile-grid">
                                                    <button type="button" class="toolbar-mobile-btn camera-btn" data-view="top" onclick="window.toolbarHandler.setCameraView('top'); window.toolbarHandler.toggleMobileMenu();">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 3L12 12M12 3L8 7M12 3L16 7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                                        <span class="toolbar-mobile-btn-label">Top</span>
                                                    </button>
                                                    <button type="button" class="toolbar-mobile-btn camera-btn" data-view="front" onclick="window.toolbarHandler.setCameraView('front'); window.toolbarHandler.toggleMobileMenu();">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="2" fill="currentColor"/></svg>
                                                        <span class="toolbar-mobile-btn-label">Front</span>
                                                    </button>
                                                    <button type="button" class="toolbar-mobile-btn camera-btn" data-view="right" onclick="window.toolbarHandler.setCameraView('right'); window.toolbarHandler.toggleMobileMenu();">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M21 12L12 12M21 12L17 8M21 12L17 16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                                        <span class="toolbar-mobile-btn-label">Right</span>
                                                    </button>
                                                    <button type="button" class="toolbar-mobile-btn camera-btn" data-view="left" onclick="window.toolbarHandler.setCameraView('left'); window.toolbarHandler.toggleMobileMenu();">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M3 12L12 12M3 12L7 8M3 12L7 16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                                        <span class="toolbar-mobile-btn-label">Left</span>
                                                    </button>
                                                    <button type="button" class="toolbar-mobile-btn camera-btn" data-view="reset" onclick="window.toolbarHandler.setCameraView('reset'); window.toolbarHandler.toggleMobileMenu();">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M3 12A9 9 0 1 1 12 21M3 12L7 8M3 12L7 16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                                        <span class="toolbar-mobile-btn-label">Reset</span>
                                                    </button>
                                                </div>
                                            </div>

                                            {{-- Save Button (Mobile) --}}
                                            <div class="toolbar-mobile-section">
                                                <button type="button" class="toolbar-btn-save-continue" style="width: 100%; justify-content: center;" onclick="window.toolbarHandler.saveAndCalculate('General'); window.toolbarHandler.toggleMobileMenu();">
                                                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none" style="flex-shrink: 0;">
                                                        <path d="M16 18H4C3 18 2 17 2 16V4C2 3 3 2 4 2H13L18 7V16C18 17 17 18 16 18Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M14 18V11H6V18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M6 2V6H13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <circle cx="10" cy="14.5" r="1" fill="currentColor"/>
                                                    </svg>
                                                    <span style="font-weight: 600; font-size: 14px; margin-left: 8px;">Save & Continue</span>
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Unit Toggle (for Grid) --}}
                                        <div class="unit-toggle" id="unitToggle" style="display: none;">
                                            <button type="button" class="unit-btn active" data-unit="mm">mm</button>
                                            <button type="button" class="unit-btn" data-unit="inch">inch</button>
                                        </div>

                                        {{-- Bottom Control Bar - REMOVED (Functions moved to top toolbar) --}}

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

                                        {{-- Floating File Upload Button (Mobile Only) --}}
                                        <button type="button" class="mobile-float-upload-btn" id="mobileFloatUploadBtn" onclick="window.toolbarHandler.toggleMobileFilesPanel()" title="Upload Files">
                                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                                                <path d="M12 5V19M5 12H19" stroke="white" stroke-width="3" stroke-linecap="round"/>
                                            </svg>
                                        </button>
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
/* Category Tab Button Styling */
                    <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                        <div class="card-body p-0">
                            <div class="row g-0">
                                <!-- Left Side: Controls -->
                                <div class="col-12 col-lg-3" style="display: none !important;">
                                    <div class="">
                                        {{-- Category tabs removed - upload from bottom bar --}}
                                        {{-- Upload area hidden - use bottom bar upload button --}}

                                        <!-- Hidden file inputs for compatibility -->
                                        <input type="file" id="fileInput3dMedical" style="display: none;" accept=".stl,.obj,.ply" multiple>

                                        <!-- Uploaded Files List -->
                                        <div id="uploadedFilesListMedical" class="mb-3" style="display: none;">
                                            <label class="form-label mb-2" style="font-size: 0.85rem; font-weight: 600; color: #495057;">Uploaded Files (<span id="fileCountMedical">0</span>)</label>
                                            <div id="filesContainerMedical" style="max-height: 200px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 8px; background: #f8f9fa;"></div>
                                        </div>

                                        <!-- Technology -->
                                        <div class="mb-3">
                                            <label class="form-label mb-2" style="font-size: 0.85rem; font-weight: 600; color: #495057;">Technology</label>
                                            <select id="technologySelectMedical" class="form-select form-select-sm" style="border-radius: 6px; border: 1px solid #dee2e6; font-size: 0.85rem;">
                                                <option value="sla" selected>SLA / DLP (Stereolithography / Digital Light Processing)</option>
                                            </select>
                                        </div>

                                        <!-- Material -->
                                        <div class="mb-3">
                                            <label class="form-label mb-2" style="font-size: 0.85rem; font-weight: 600; color: #495057;">Material</label>
                                            <select id="materialSelectMedical" class="form-select form-select-sm" style="border-radius: 6px; border: 1px solid #dee2e6; font-size: 0.85rem;">
                                                <option value="biocompatible-resin" selected>Biocompatible Resins (Class I)</option>
                                                <option value="dental-resin">Dental Resin (FDA Approved)</option>
                                                <option value="surgical-guide">Surgical Guide Resin</option>
                                                <option value="castable-resin">Castable Resin</option>
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

                                        <!-- Layer Height - Fixed & Validated -->
                                        <div class="mb-3">
                                            <label class="form-label mb-2" style="font-size: 0.85rem; font-weight: 600; color: #495057;">Layer Height</label>
                                            <div class="p-2" style="background: #f8f9fa; border-radius: 6px; border: 1px solid #dee2e6;">
                                                <div style="display: flex; align-items: center; gap: 8px;">
                                                    <i class="fas fa-lock" style="color: #6c757d; font-size: 0.75rem;"></i>
                                                    <span style="font-size: 0.85rem; font-weight: 600; color: #495057;">25-50 μm</span>
                                                    <span style="font-size: 0.7rem; color: #6c757d; margin-left: auto;">(Fixed, Validated)</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Color Picker -->
                                        <div class="mb-3">
                                            <label class="form-label mb-2" style="font-size: 0.85rem; font-weight: 600; color: #495057;">Model Color <span style="font-size: 0.7rem; color: #6c757d;">(Certified Only)</span></label>
                                            <div class="d-flex gap-2 flex-wrap">
                                                <button type="button" class="color-btn-medical active" data-color="#F5E6D3" style="width: 32px; height: 32px; border-radius: 6px; border: 2px solid #F5E6D3; background: #F5E6D3; cursor: pointer;" title="Dental White"></button>
                                                <button type="button" class="color-btn-medical" data-color="#FFE4C4" style="width: 32px; height: 32px; border-radius: 6px; border: 2px solid #dee2e6; background: #FFE4C4; cursor: pointer;" title="Gum Pink"></button>
                                                <button type="button" class="color-btn-medical" data-color="#FFFFFF" style="width: 32px; height: 32px; border-radius: 6px; border: 2px solid #dee2e6; background: #FFFFFF; cursor: pointer;" title="Clear"></button>
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
                                <div class="col-12 col-lg-12 position-relative d-flex flex-column" style="display: flex !important; visibility: visible !important; opacity: 1 !important; flex: 1 !important; min-width: 0 !important; background: linear-gradient(to bottom, #b8c5d6 0%, #99a8ba 100%) !important;">
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
                                        <div class="viewer-professional-toolbar" id="professionalToolbar" style="position: absolute !important; top: 0 !important; left: 0 !important; right: 0 !important; width: 100% !important; display: flex !important; visibility: visible !important; opacity: 1 !important; z-index: 9999 !important; background: rgba(255, 255, 255, 0.95) !important; padding: 8px !important; border-radius: 0 !important; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15) !important; pointer-events: auto !important; gap: 8px !important;">

                                            {{-- Logo Group --}}
                                            <div class="toolbar-group" style="display: flex !important; align-items: center !important; padding-right: 8px !important;">
                                                <a href="{{ url('/') }}" style="display: flex; align-items: center; text-decoration: none; cursor: pointer; transition: opacity 0.2s ease;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'" title="Go to Homepage">
                                                    <img src="{{ asset($settings->logo) }}" alt="{{ $settings->app_name }}" style="max-height: 36px; width: auto;">
                                                </a>
                                            </div>

                                            <div class="toolbar-divider"></div>

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
                                                <button type="button" class="toolbar-btn" id="panToolBtn" title="Move Model - Drag to reposition" data-tool="pan" onclick="window.toolbarHandler.toggleMoveMode('General')">
                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                                        <path d="M13 5L13 11M13 11L10 8M13 11L16 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M13 19L13 13M13 13L10 16M13 13L16 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M5 13L11 13M11 13L8 10M11 13L8 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M19 13L13 13M13 13L16 10M13 13L16 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <circle cx="13" cy="13" r="2" fill="currentColor"/>
                                                    </svg>
                                                </button>
                                                <button type="button" class="toolbar-btn" id="autoRotateBtn" title="Auto-rotate Model" data-tool="autoRotate" onclick="window.toolbarHandler.toggleAutoRotate('General')">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <path d="M17 10C17 13.866 13.866 17 10 17C6.13401 17 3 13.866 3 10C3 6.13401 6.13401 3 10 3C12.0605 3 13.8792 3.91099 15 5.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                        <path d="M17 3V7H13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </button>
                                                {{-- Bottom Control Bar Functions - Now in Top Toolbar --}}
                                                <button type="button" class="toolbar-btn" id="toggleGridBtnMain" title="Toggle grid visibility" data-tool="gridMain" onclick="window.toolbarHandler.toggleGridMain('General')">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <rect x="3" y="3" width="5" height="5" stroke="currentColor" stroke-width="1.5"/>
                                                        <rect x="11" y="3" width="5" height="5" stroke="currentColor" stroke-width="1.5"/>
                                                        <rect x="3" y="11" width="5" height="5" stroke="currentColor" stroke-width="1.5"/>
                                                        <rect x="11" y="11" width="5" height="5" stroke="currentColor" stroke-width="1.5"/>
                                                    </svg>
                                                </button>
                                                <button type="button" class="toolbar-btn" id="measureToolBtnMain" title="Measure distance between two points" data-tool="measureMain" onclick="window.toolbarHandler.toggleMeasureMain('General')">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <path d="M2 2L18 18M2 2L2 18M18 2L18 18M2 18L18 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                                        <circle cx="2" cy="2" r="1.5" fill="currentColor"/>
                                                        <circle cx="18" cy="18" r="1.5" fill="currentColor"/>
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

                                            <div class="toolbar-divider"></div>

                                            {{-- Camera View Controls --}}
                                            <div class="toolbar-group">
                                                <button type="button" class="toolbar-btn camera-btn" data-view="top" title="Top View">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                                        <path d="M12 5L12 19M12 5L8 9M12 5L16 9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                    </svg>
                                                </button>
                                                <button type="button" class="toolbar-btn camera-btn active" data-view="front" title="Front View">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
                                                        <circle cx="12" cy="12" r="3" fill="currentColor"/>
                                                    </svg>
                                                </button>
                                                <button type="button" class="toolbar-btn camera-btn" data-view="right" title="Right View">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                                        <path d="M19 12L5 12M19 12L15 8M19 12L15 16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                    </svg>
                                                </button>
                                                <button type="button" class="toolbar-btn camera-btn" data-view="left" title="Left View">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                                        <path d="M5 12L19 12M5 12L9 8M5 12L9 16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                    </svg>
                                                </button>
                                                <button type="button" class="toolbar-btn camera-btn" data-view="bottom" title="Bottom View">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                                        <path d="M12 19L12 5M12 19L8 15M12 19L16 15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                    </svg>
                                                </button>
                                                <button type="button" class="toolbar-btn camera-btn" data-view="reset" title="Reset Camera">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                                        <path d="M12 4V1L8 5L12 9V6C15.31 6 18 8.69 18 12C18 13.01 17.75 13.97 17.3 14.8L18.76 16.26C19.54 15.03 20 13.57 20 12C20 7.58 16.42 4 12 4ZM12 18C8.69 18 6 15.31 6 12C6 10.99 6.25 10.03 6.7 9.2L5.24 7.74C4.46 8.97 4 10.43 4 12C4 16.42 7.58 20 12 20V23L16 19L12 15V18Z" fill="currentColor"/>
                                                    </svg>
                                                </button>
                                            </div>

                                            {{-- Divider --}}
                                            <div style="width: 1px; height: 32px; background: rgba(0,0,0,0.1); margin: 0 4px;"></div>

                                            {{-- Action Buttons Group --}}
                                            <div class="toolbar-group" style="display: flex !important; gap: 4px !important; visibility: visible !important; opacity: 1 !important;">
                                                <button type="button" class="toolbar-btn" id="shareToolBtn" title="Share Model" data-action="share" onclick="window.toolbarHandler.shareModel('General')">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <circle cx="15" cy="4" r="3" stroke="currentColor" stroke-width="1.8"/>
                                                        <circle cx="5" cy="10" r="3" stroke="currentColor" stroke-width="1.8"/>
                                                        <circle cx="15" cy="16" r="3" stroke="currentColor" stroke-width="1.8"/>
                                                        <path d="M7.5 11.5L12.5 14.5M12.5 5.5L7.5 8.5" stroke="currentColor" stroke-width="1.8"/>
                                                    </svg>
                                                </button>
                                                <button type="button" class="toolbar-btn toolbar-btn-primary" id="saveCalculateToolBtn" title="Save & Calculate Pricing" data-action="saveCalculate" onclick="window.toolbarHandler.saveAndCalculate('General')">
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <path d="M16 18H4C3 18 2 17 2 16V4C2 3 3 2 4 2H13L18 7V16C18 17 17 18 16 18Z" stroke="currentColor" stroke-width="1.8"/>
                                                        <path d="M5 11H15V18H5V11Z" stroke="currentColor" stroke-width="1.8"/>
                                                        <path d="M14 2V6H6V2" stroke="currentColor" stroke-width="1.8"/>
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
                                        </div>

                                        {{-- Unit Toggle (for Grid) --}}
                                        <div class="unit-toggle" id="unitToggle" style="display: none;">
                                            <button type="button" class="unit-btn active" data-unit="mm">mm</button>
                                            <button type="button" class="unit-btn" data-unit="inch">inch</button>
                                        </div>

                                        {{-- Bottom Control Bar - REMOVED (Functions moved to top toolbar) --}}

                                        {{-- Mesh Repair Script --}}
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
/* ========================================
   ANIMATIONS
   ======================================== */
@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* HIDE BOTTOM CONTROL BAR - Moved to top toolbar */
.viewer-bottom-controls {
    display: none !important;
}

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

/* CACHE BUSTER - Updated: 2025-12-24 20:53 */
/* RIGHT PANEL NOW MATCHES LEFT PANEL DESIGN - Simple list layout */


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

/* Right Files Panel Styles */
.right-files-panel {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
}

.right-files-panel #rightFilesContainer::-webkit-scrollbar {
    width: 6px;
}

.right-files-panel #rightFilesContainer::-webkit-scrollbar-track {
    background: #f8f9fa;
    border-radius: 3px;
}

.right-files-panel #rightFilesContainer::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 3px;
}

.right-files-panel #rightFilesContainer::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}

/* Right Panel File Card - Beautiful Blue Design */
.right-file-card {
    background: #E3F2FD !important;
    border: 1px solid #BBDEFB !important;
    border-radius: 8px !important;
    padding: 12px !important;
    margin-bottom: 12px !important;
    cursor: grab !important;
    transition: all 0.2s ease !important;
    position: relative !important;
}

.right-file-card:hover {
    background: #BBDEFB !important;
    box-shadow: 0 2px 8px rgba(25, 118, 210, 0.2) !important;
    transform: translateY(-2px) !important;
}

.right-file-card:active {
    cursor: grabbing !important;
}

.right-file-card.dragging {
    opacity: 0.5 !important;
    cursor: grabbing !important;
}

/* File Card Header */
.right-file-header {
    display: flex !important;
    align-items: flex-start !important;
    gap: 0 !important;
    margin-bottom: 8px !important;
}

/* File Info */
.right-file-info {
    flex: 1 !important;
    min-width: 0 !important;
}

.right-file-name {
    font-weight: 600 !important;
    font-size: 13px !important;
    color: #1565C0 !important;
    margin: 0 0 4px 0 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
}

/* STL Icon Container - Small, inline with filename */
.right-file-icon {
    width: 20px !important;
    height: 20px !important;
    background: #1976D2 !important;
    border-radius: 4px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
}

.right-file-icon svg {
    width: 12px !important;
    height: 12px !important;
    color: white !important;
}

.right-file-details {
    display: flex !important;
    gap: 8px !important;
    font-size: 11px !important;
    color: #546E7A !important;
}

.right-file-detail {
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
}

.right-file-detail svg {
    width: 12px !important;
    height: 12px !important;
}

/* Action Buttons - No divider line */
.right-file-actions {
    display: flex !important;
    gap: 8px !important;
    margin-top: 8px !important;
}

.right-file-action-btn {
    flex: 1 !important;
    background: white !important;
    border: 1px solid #1976D2 !important;
    color: #1976D2 !important;
    border-radius: 5px !important;
    padding: 8px !important;
    font-size: 12px !important;
    font-weight: 500 !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 4px !important;
    min-width: 36px !important;
}

.right-file-action-btn:hover {
    background: #1976D2 !important;
    color: white !important;
}

.right-file-action-btn svg {
    width: 16px !important;
    height: 16px !important;
}

.right-file-action-btn.eye-btn {
    border-color: #1976D2 !important;
    color: #1976D2 !important;
}

.right-file-action-btn.eye-btn:hover {
    background: #1976D2 !important;
    color: white !important;
}

.right-file-action-btn.eye-btn.active {
    background: #E3F2FD !important;
}

.right-file-action-btn.download-btn {
    border-color: #2E7D32 !important;
    color: #2E7D32 !important;
}

.right-file-action-btn.download-btn:hover {
    background: #2E7D32 !important;
    color: white !important;
}

.right-file-action-btn.delete-btn {
    border-color: #D32F2F !important;
    color: #D32F2F !important;
}

.right-file-action-btn.delete-btn:hover {
    background: #D32F2F !important;
    color: white !important;
}

/* Modal Color Button Styles */
.modal-color-btn {
    position: relative;
}

.modal-color-btn.active::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 18px;
    font-weight: bold;
    text-shadow: 0 0 3px rgba(0,0,0,0.5);
}

.modal-color-btn[data-color="#ffffff"].active::after {
    color: #0047AD;
    text-shadow: none;
}

.modal-color-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

/* Right File Card Hover Effect */
.right-file-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.12) !important;
}

/* OLD MODAL CSS REMOVED - NOW USING SIMPLE MODAL */

.modal-open {
    overflow: hidden;
}

/* Fix z-index hierarchy when modal is open */
/* Modal backdrop should be above toolbar and right panel */
.modal-backdrop {
    z-index: 10000 !important;
}

/* Modal should be above backdrop */
.modal {
    z-index: 10001 !important;
}

/* Lower toolbar and right panel z-index when modal is open */
body.modal-open #professionalToolbar {
    z-index: 999 !important;
}

body.modal-open #rightFilesPanel {
    z-index: 998 !important;
}

/* Toggle Panel Button */
#toggleRightPanel:hover {
    background: rgba(0, 0, 0, 0.05) !important;
    border-radius: 4px;
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
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    width: 100% !important;
    display: flex !important;
    gap: 8px;
    background: rgba(255, 255, 255, 0.95);
    padding: 8px;
    border-radius: 0 !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    backdrop-filter: blur(10px);
    z-index: 9999 !important;
    transition: all 0.3s ease;
    pointer-events: auto !important;
    overflow: visible !important;
}

/* Mobile Burger Menu Button */
.toolbar-burger-menu {
    display: none;
    width: 48px;
    height: 48px;
    background: white;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    cursor: pointer;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 5px;
    transition: all 0.3s ease;
    z-index: 10001;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    padding: 12px;
}

.toolbar-burger-menu span {
    width: 24px;
    height: 3px;
    background: #2c3e50;
    border-radius: 3px;
    transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.toolbar-burger-menu.active {
    background: #f5f5f5;
    border-color: #2c3e50;
}

.toolbar-burger-menu.active span {
    background: #2c3e50;
}

.toolbar-burger-menu.active span:nth-child(1) {
    transform: rotate(45deg) translate(7px, 7px);
}

.toolbar-burger-menu.active span:nth-child(2) {
    opacity: 0;
    transform: translateX(-20px);
}

.toolbar-burger-menu.active span:nth-child(3) {
    transform: rotate(-45deg) translate(7px, -7px);
}

.toolbar-burger-menu:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    background: #fafafa;
}

.toolbar-burger-menu:active {
    transform: scale(0.95);
}

/* Mobile Menu Container */
.toolbar-mobile-menu {
    display: none;
    position: fixed;
    top: 60px;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(10px);
    z-index: 10000;
    overflow-y: auto;
    padding: 20px;
    animation: slideInDown 0.3s ease;
}

.toolbar-mobile-menu.active {
    display: block;
}

.toolbar-mobile-section {
    margin-bottom: 24px;
}

.toolbar-mobile-section-title {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    color: #666;
    margin-bottom: 12px;
    letter-spacing: 0.5px;
}

.toolbar-mobile-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
    gap: 12px;
}

.toolbar-mobile-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 16px 8px;
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    text-align: center;
}

.toolbar-mobile-btn:active {
    transform: scale(0.95);
    background: #f5f5f5;
}

.toolbar-mobile-btn.active {
    background: #4a90e2;
    color: white;
    border-color: #4a90e2;
}

.toolbar-mobile-btn svg {
    width: 24px;
    height: 24px;
}

.toolbar-mobile-btn-label {
    font-size: 11px;
    font-weight: 500;
    line-height: 1.2;
}

/* Floating Upload Button (Mobile Only) */
.mobile-float-upload-btn {
    display: none; /* Hidden on desktop */
    position: fixed;
    bottom: 24px;
    right: 24px;
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
    border: none;
    border-radius: 50%;
    box-shadow: 0 6px 20px rgba(74, 144, 226, 0.4);
    cursor: pointer;
    z-index: 9997;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    animation: floatBounce 2s ease-in-out infinite;
}

.mobile-float-upload-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 25px rgba(74, 144, 226, 0.5);
}

.mobile-float-upload-btn:active {
    transform: scale(0.95);
}

@keyframes floatBounce {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-8px);
    }
}

/* Mobile: Hide desktop toolbar items, show burger */
@media (max-width: 768px) {
    .viewer-professional-toolbar {
        padding: 8px 12px;
        justify-content: flex-start;
        gap: 12px;
    }

    .toolbar-burger-menu {
        display: flex !important;
    }

    /* Hide ALL toolbar items except logo and burger */
    .viewer-professional-toolbar .toolbar-group:not(:first-child),
    .viewer-professional-toolbar .toolbar-divider,
    .viewer-professional-toolbar .toolbar-btn,
    .viewer-professional-toolbar .toolbar-btn-save-continue,
    .viewer-professional-toolbar > div[style*="flex: 1"],
    .viewer-professional-toolbar > div[style*="flex:1"] {
        display: none !important;
    }

    /* Keep ONLY logo and burger visible */
    .viewer-professional-toolbar > .toolbar-group:first-child {
        display: flex !important;
    }

    .viewer-professional-toolbar .toolbar-burger-menu {
        display: flex !important;
    }

    /* Floating Upload Button - Show on mobile */
    .mobile-float-upload-btn {
        display: flex !important;
    }

    /* Show close button in files panel on mobile */
    .mobile-close-files-btn {
        display: block !important;
    }

    /* Right Files Panel - Hidden by default, full screen when open */
    .right-files-panel {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        height: 100vh !important;
        max-height: 100vh !important;
        border-radius: 0 !important;
        z-index: 10001 !important;
        transform: translateX(100%) !important;
        transition: transform 0.3s ease !important;
        display: none !important; /* Hidden by default */
    }

    .right-files-panel.mobile-open {
        transform: translateX(0) !important;
        display: flex !important; /* Show when open */
    }

    /* Model Info Badge - Adjust position */
    .model-info-badge {
        top: 70px !important;
        left: 10px !important;
        font-size: 12px !important;
    }

    /* Active Tool Status - Adjust for mobile */
    .active-tool-status {
        top: 70px !important;
        left: 10px !important;
        right: 10px !important;
        transform: none !important;
        padding: 10px 16px !important;
        font-size: 13px !important;
    }

    .active-tool-status .tool-instruction {
        display: none;
    }

    /* Measurement Results Panel - Mobile friendly */
    .measurement-results-panel {
        width: calc(100% - 20px) !important;
        max-width: 320px !important;
        left: 10px !important;
        top: 70px !important;
        max-height: calc(100vh - 90px) !important;
    }

    /* Unit Toggle - Mobile position */
    .unit-toggle {
        bottom: 80px !important;
        right: 10px !important;
    }

    /* Save button in mobile menu - full width */
    .toolbar-btn-save-continue {
        padding: 12px 20px !important;
    }
}

/* Tablet: Show some controls, optimize spacing */
@media (min-width: 769px) and (max-width: 1024px) {
    .viewer-professional-toolbar {
        gap: 6px;
        padding: 6px;
    }

    .toolbar-btn {
        width: 38px;
        height: 38px;
    }

    .toolbar-group {
        gap: 3px;
    }

    /* Hide less important buttons on tablet */
    #autoRotateBtn,
    #toggleGridBtnMain,
    #measureToolBtnMain {
        display: none !important;
    }

    /* Right Files Panel - Tablet optimization */
    .right-files-panel {
        width: 280px !important;
    }
}

/* Small phones - Extra adjustments */
@media (max-width: 480px) {
    .viewer-professional-toolbar {
        padding: 6px 10px;
    }

    .viewer-professional-toolbar img {
        max-height: 28px !important;
    }

    .toolbar-burger-menu {
        width: 36px;
        height: 36px;
    }

    .toolbar-mobile-section-title {
        font-size: 11px;
    }

    .toolbar-mobile-grid {
        grid-template-columns: repeat(auto-fill, minmax(70px, 1fr));
        gap: 10px;
    }

    .toolbar-mobile-btn {
        padding: 12px 6px;
    }

    .toolbar-mobile-btn svg {
        width: 20px;
        height: 20px;
    }

    .toolbar-mobile-btn-label {
        font-size: 10px;
    }

    /* Make right panel take more space on small phones */
    .right-files-panel {
        max-width: 100% !important;
        width: calc(100% - 20px) !important;
    }
}

/* Landscape mode on mobile */
@media (max-width: 768px) and (orientation: landscape) {
    .toolbar-mobile-menu {
        top: 50px;
        padding: 15px;
    }

    .toolbar-mobile-section {
        margin-bottom: 16px;
    }

    .right-files-panel {
        max-height: calc(100vh - 60px) !important;
    }
}

/* Touch device optimization */
@media (hover: none) and (pointer: coarse) {
    .toolbar-btn,
    .toolbar-mobile-btn {
        min-height: 44px;
        min-width: 44px;
    }

    .toolbar-btn:active {
        transform: scale(0.95);
    }
}

.toolbar-group {
    display: flex;
    gap: 4px;
    overflow: visible !important;
    position: relative;
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

.toolbar-btn-primary {
    background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%) !important;
    color: white !important;
    border-color: #357abd !important;
    font-weight: 600;
}

.toolbar-btn-primary:hover {
    background: linear-gradient(135deg, #357abd 0%, #2868a8 100%) !important;
    box-shadow: 0 6px 16px rgba(74, 144, 226, 0.4) !important;
}

/* Save & Continue Button - Premium Design */
.toolbar-btn-save-continue {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 24px;
    background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
    color: white;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    box-shadow: 0 4px 12px rgba(74, 144, 226, 0.35), 0 2px 4px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
}

.toolbar-btn-save-continue::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s ease;
}

.toolbar-btn-save-continue:hover::before {
    left: 100%;
}

.toolbar-btn-save-continue:hover {
    background: linear-gradient(135deg, #357abd 0%, #2868a8 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(74, 144, 226, 0.45), 0 4px 8px rgba(0, 0, 0, 0.15);
}

.toolbar-btn-save-continue:active {
    transform: translateY(0px);
    box-shadow: 0 2px 8px rgba(74, 144, 226, 0.35);
}

.toolbar-btn-save-continue svg {
    transition: transform 0.2s ease;
}

.toolbar-btn-save-continue:hover svg:first-child {
    transform: scale(1.1);
}

.toolbar-btn-save-continue:disabled {
    background: linear-gradient(135deg, #9ca3af 0%, #6b7280 100%);
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
    opacity: 0.6;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.toolbar-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
    transform: none;
}

/* Undo/Redo button specific styling */
#undoBtn svg,
#redoBtn svg {
    transition: color 0.3s ease, stroke 0.3s ease;
}

/* Disabled state - gray icons (using class instead of :disabled) */
#undoBtn.disabled svg,
#redoBtn.disabled svg {
    color: #95a5a6;
    stroke: #95a5a6;
}

/* Enabled state - black icons */
#undoBtn:not(.disabled) svg,
#redoBtn:not(.disabled) svg {
    color: #2c3e50;
    stroke: #2c3e50;
}

.toolbar-btn svg {
    width: 20px;
    height: 20px;
}

/* Active Tool Status Bar */
.active-tool-status {
    position: fixed;
    top: 60px;
    left: 50%;
    transform: translateX(-50%);
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 12px 24px;
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    z-index: 10001;
    display: none;
    align-items: center;
    gap: 12px;
    font-weight: 600;
    font-size: 14px;
    animation: slideInDown 0.3s ease;
    backdrop-filter: blur(10px);
}

.active-tool-status.visible {
    display: flex;
}

.active-tool-status .tool-icon {
    width: 24px;
    height: 24px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.active-tool-status .tool-name {
    font-size: 15px;
}

.active-tool-status .tool-instruction {
    font-size: 12px;
    opacity: 0.9;
    font-weight: 400;
    border-left: 2px solid rgba(255, 255, 255, 0.3);
    padding-left: 12px;
    margin-left: 4px;
}

.active-tool-status .cancel-tool {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    padding: 6px 12px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 12px;
    font-weight: 600;
    transition: all 0.2s;
    margin-left: 8px;
}

.active-tool-status .cancel-tool:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: scale(1.05);
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateX(-50%) translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }
}

/* Measurement Results Panel */
.measurement-results-panel {
    position: fixed;
    bottom: 80px;
    left: 20px;
    width: 320px;
    max-height: 400px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
    z-index: 10002;
    display: none;
    flex-direction: column;
    overflow: hidden;
    animation: slideInLeft 0.3s ease;
}

.measurement-results-panel.visible {
    display: flex;
}

@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-50px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Lighting Control Panels */
.lighting-control-panel {
    animation: fadeInScale 0.2s ease;
}

@keyframes fadeInScale {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.lighting-slider {
    -webkit-appearance: none;
    appearance: none;
    cursor: pointer;
    transition: all 0.2s ease;
}

/* Webkit browsers (Chrome, Safari, Edge) */
.lighting-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: white;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    border: 2px solid #4a90e2;
    transition: all 0.2s ease;
}

.lighting-slider::-webkit-slider-thumb:hover {
    transform: scale(1.15);
    box-shadow: 0 3px 10px rgba(0,0,0,0.4);
}

.lighting-slider::-webkit-slider-thumb:active {
    transform: scale(0.95);
}

/* Firefox */
.lighting-slider::-moz-range-thumb {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: white;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    border: 2px solid #4a90e2;
    transition: all 0.2s ease;
}

.lighting-slider::-moz-range-thumb:hover {
    transform: scale(1.15);
    box-shadow: 0 3px 10px rgba(0,0,0,0.4);
}

.lighting-slider:hover {
    height: 8px;
}

/* Lighting panel positioning */
#lightIntensityPanel {
    left: auto !important;
    right: auto !important;
}

#shadowIntensityPanel {
    left: auto !important;
    right: auto !important;
}

#lightRotationPanel {
    left: auto !important;
    right: auto !important;
}

@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-50px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.results-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 14px;
    background: #f8f9fa;
    border-bottom: 2px solid #e9ecef;
    font-weight: 700;
    font-size: 13px;
    color: #212529;
}

.results-close {
    background: #e9ecef;
    border: none;
    font-size: 18px;
    color: #495057;
    cursor: pointer;
    padding: 0;
    width: 26px;
    height: 26px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    transition: all 0.2s;
    font-weight: 700;
    line-height: 1;
}

.results-close:hover {
    background: #dee2e6;
    color: #212529;
    transform: rotate(90deg);
}

.results-body {
    flex: 1;
    overflow-y: auto;
    padding: 10px;
}

.no-measurements {
    text-align: center;
    padding: 25px 12px;
    color: #6c757d;
}

.no-measurements svg {
    width: 36px;
    height: 36px;
}

.no-measurements p {
    font-size: 12px;
    font-weight: 600;
    margin: 0 0 4px 0;
}

.no-measurements small {
    font-size: 10px;
    color: #adb5bd;
}

.measurement-item {
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 8px 10px;
    margin-bottom: 8px;
    transition: all 0.2s;
}

.measurement-item:hover {
    border-color: #4a90e2;
    box-shadow: 0 2px 8px rgba(74, 144, 226, 0.1);
}

.measurement-item-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 5px;
}

.measurement-type {
    display: flex;
    align-items: center;
    gap: 5px;
    font-weight: 600;
    font-size: 11px;
    color: #495057;
}

.measurement-type .type-icon {
    width: 16px;
    height: 16px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
}

.measurement-type.distance .type-icon { background: #ffebee; color: #f44336; }
.measurement-type.diameter .type-icon { background: #e8f5e9; color: #4caf50; }
.measurement-type.area .type-icon { background: #fff9c4; color: #fbc02d; }
.measurement-type.point-to-surface .type-icon { background: #f3e5f5; color: #9c27b0; }
.measurement-type.angle .type-icon { background: #e0f7fa; color: #00bcd4; }

.measurement-value {
    font-size: 15px;
    font-weight: 700;
    color: #212529;
    margin: 0;
    line-height: 1.2;
    word-break: break-all;
}

.measurement-unit {
    font-size: 10px;
    font-weight: 500;
    color: #6c757d;
    margin-left: 2px;
}

.measurement-delete {
    background: #ffebee;
    border: none;
    color: #f44336;
    padding: 4px 8px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 10px;
    font-weight: 600;
    transition: all 0.2s;
}

.measurement-delete:hover {
    background: #f44336;
    color: white;
    transform: scale(1.05);
}

/* Simplified Measurement Submenu */
.measurement-submenu {
    position: absolute !important;
    top: 100% !important;
    margin-top: 8px !important;
    left: 0 !important;
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    padding: 8px;
    min-width: 260px;
    z-index: 10000 !important;
    animation: slideDown 0.2s ease;
    border: 1px solid #e9ecef;
    display: none;
}

.measurement-submenu[style*="display: block"] {
    display: block !important;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.submenu-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    background: #4a90e2;
    border-radius: 8px;
    margin: -8px -8px 8px -8px;
    font-weight: 600;
    font-size: 14px;
    color: white;
}

.submenu-close {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    font-size: 20px;
    color: white;
    cursor: pointer;
    padding: 0;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    transition: all 0.2s;
    font-weight: 700;
}

.submenu-close:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: rotate(90deg);
}

.submenu-btn {
    width: 100%;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
    margin-bottom: 6px;
    font-size: 14px;
    font-weight: 500;
    color: #495057;
    text-align: left;
}

.submenu-btn::before {
    display: none;
}

.submenu-btn:hover::before {
    display: none;
}

.submenu-btn:last-child {
    margin-bottom: 0;
}

.submenu-btn:hover {
    background: #f8f9fa;
    border-color: #4a90e2;
    transform: translateX(4px);
}

.submenu-btn.active {
    background: #4a90e2;
    border-color: #4a90e2;
    color: white;
}

.submenu-btn.active svg {
    color: white;
}

.submenu-btn svg {
    flex-shrink: 0;
    transition: transform 0.2s ease;
}

.submenu-btn span {
    flex: 1;
    white-space: nowrap;
}

.submenu-btn:hover svg {
    transform: scale(1.1);
}

.submenu-btn[data-measure="clear"] {
    border-color: #dc3545;
    color: #dc3545;
    background: #fff5f5;
}

.submenu-btn[data-measure="clear"]:hover {
    background: #dc3545;
    color: white;
    transform: translateX(4px);
}

.submenu-btn[data-measure="clear"]:hover svg {
    color: white;
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
// ==========================================
// MODAL FUNCTIONS - DEFINED FIRST SO THEY'RE AVAILABLE EVERYWHERE
// ==========================================

function openSimpleFileModal(formType, fileId) {
    console.log('🔵 Simple modal called - formType:', formType, 'fileId:', fileId);

    const viewer = formType === 'General' ? window.viewerGeneral : window.viewer;
    if (!viewer) {
        console.error('❌ Viewer not found for formType:', formType);
        return;
    }
    console.log('✅ Viewer found:', viewer);

    const fileData = viewer.uploadedFiles.find(f => f.id === fileId);
    if (!fileData) {
        console.error('❌ File not found with id:', fileId);
        console.log('Available files:', viewer.uploadedFiles);
        return;
    }
    console.log('✅ File data found:', fileData);

    // Store for saving later
    window.simpleModalFileId = fileId;
    window.simpleModalFormType = formType;

    // Detect viewer type (check URL parameter and sessionStorage)
    const urlParams = new URLSearchParams(window.location.search);
    let viewerType = urlParams.get('viewer') || sessionStorage.getItem('viewerType') || 'general';

    // Normalize dental-viewer to dental
    if (viewerType === 'dental-viewer') {
        viewerType = 'dental';
    }

    const isDental = (viewerType === 'dental');
    console.log('🦷 Viewer type detected:', viewerType, '- Is Dental:', isDental);

    // Populate modal
    const fileNameEl = document.getElementById('simpleModalFileName');
    const techEl = document.getElementById('simpleTechSelect');
    const materialEl = document.getElementById('simpleMaterialSelect');
    const colorPickerEl = document.getElementById('simpleColorPicker');

    console.log('📝 Modal elements:', {fileNameEl, techEl, materialEl});

    if (fileNameEl) fileNameEl.textContent = fileData.file.name;

    // Update Technology options based on viewer type
    if (techEl) {
        if (isDental) {
            // Dental viewer - SLA/DLP only
            techEl.innerHTML = `
                <option value="sla">SLA / DLP (Stereolithography / Digital Light Processing)</option>
            `;
            techEl.value = 'sla';
        } else if (formType === 'General') {
            techEl.innerHTML = `
                <option value="fdm">FDM (Fused Deposition Modeling)</option>
                <option value="sla">SLA (Stereolithography)</option>
                <option value="sls">SLS (Selective Laser Sintering)</option>
            `;
            techEl.value = fileData.technology || 'fdm';
        } else {
            // Medical viewer
            techEl.innerHTML = `
                <option value="fdm">FDM (Fused Deposition Modeling)</option>
                <option value="sla">SLA (Stereolithography)</option>
                <option value="sls">SLS (Selective Laser Sintering)</option>
                <option value="dmls">DMLS (Direct Metal Laser Sintering)</option>
                <option value="mjf">MJF (Multi Jet Fusion)</option>
            `;
            techEl.value = fileData.technology || 'fdm';
        }
    }

    // Update Material options based on viewer type
    if (materialEl) {
        if (isDental) {
            // Dental viewer - Biocompatible resins only
            materialEl.innerHTML = `
                <option value="biocompatible_resin">Biocompatible Resins (Certified)</option>
            `;
            materialEl.value = 'biocompatible_resin';
        } else if (formType === 'General') {
            materialEl.innerHTML = `
                <option value="pla">PLA</option>
                <option value="abs">ABS</option>
                <option value="petg">PETG</option>
                <option value="nylon">Nylon</option>
            `;
            materialEl.value = fileData.material || 'pla';
        } else {
            // Medical viewer
            materialEl.innerHTML = `
                <option value="pla">PLA</option>
                <option value="abs">ABS</option>
                <option value="petg">PETG</option>
                <option value="nylon">Nylon</option>
                <option value="resin">Resin</option>
            `;
            materialEl.value = fileData.material || 'pla';
        }
    }

    // Update Color options based on viewer type
    if (colorPickerEl) {
        if (isDental) {
            // Dental viewer - Limited, certified colors only
            colorPickerEl.innerHTML = `
                <div class="simple-color-btn" data-color="#F5F5DC" style="width: 40px; height: 40px; background: #F5F5DC; border: 2px solid #dee2e6; border-radius: 8px; cursor: pointer; position: relative;" onclick="window.selectSimpleColor(this)" title="Dental White (Certified)"></div>
                <div class="simple-color-btn" data-color="#FFB6C1" style="width: 40px; height: 40px; background: #FFB6C1; border: 2px solid #dee2e6; border-radius: 8px; cursor: pointer; position: relative;" onclick="window.selectSimpleColor(this)" title="Gum Pink (Certified)"></div>
                <div class="simple-color-btn" data-color="#E8F4F8" style="width: 40px; height: 40px; background: #E8F4F8; border: 2px solid #dee2e6; border-radius: 8px; cursor: pointer; position: relative;" onclick="window.selectSimpleColor(this)" title="Clear (Certified)"></div>
            `;
        } else if (formType === 'General') {
            // General viewer - Cosmetic color only
            colorPickerEl.innerHTML = `
                <div class="simple-color-btn" data-color="#FFE5B4" style="width: 40px; height: 40px; background: #FFE5B4; border: 2px solid #dee2e6; border-radius: 8px; cursor: pointer; position: relative;" onclick="window.selectSimpleColor(this)"></div>
            `;
        } else {
            // Medical viewer - Multiple colors
            colorPickerEl.innerHTML = `
                <div class="simple-color-btn" data-color="#0047AD" style="width: 40px; height: 40px; background: #0047AD; border: 2px solid #0047AD; border-radius: 8px; cursor: pointer; position: relative;" onclick="window.selectSimpleColor(this)"></div>
                <div class="simple-color-btn" data-color="#ffffff" style="width: 40px; height: 40px; background: #ffffff; border: 2px solid #dee2e6; border-radius: 8px; cursor: pointer; position: relative;" onclick="window.selectSimpleColor(this)"></div>
                <div class="simple-color-btn" data-color="#2c3e50" style="width: 40px; height: 40px; background: #2c3e50; border: 2px solid #dee2e6; border-radius: 8px; cursor: pointer; position: relative;" onclick="window.selectSimpleColor(this)"></div>
                <div class="simple-color-btn" data-color="#3498db" style="width: 40px; height: 40px; background: #3498db; border: 2px solid #dee2e6; border-radius: 8px; cursor: pointer; position: relative;" onclick="window.selectSimpleColor(this)"></div>
                <div class="simple-color-btn" data-color="#e74c3c" style="width: 40px; height: 40px; background: #e74c3c; border: 2px solid #dee2e6; border-radius: 8px; cursor: pointer; position: relative;" onclick="window.selectSimpleColor(this)"></div>
                <div class="simple-color-btn" data-color="#2ecc71" style="width: 40px; height: 40px; background: #2ecc71; border: 2px solid #dee2e6; border-radius: 8px; cursor: pointer; position: relative;" onclick="window.selectSimpleColor(this)"></div>
                <div class="simple-color-btn" data-color="#f39c12" style="width: 40px; height: 40px; background: #f39c12; border: 2px solid #dee2e6; border-radius: 8px; cursor: pointer; position: relative;" onclick="window.selectSimpleColor(this)"></div>
                <div class="simple-color-btn" data-color="#9b59b6" style="width: 40px; height: 40px; background: #9b59b6; border: 2px solid #dee2e6; border-radius: 8px; cursor: pointer; position: relative;" onclick="window.selectSimpleColor(this)"></div>
            `;
        }
    }

    // Set active color
    const defaultDentalColor = '#F5F5DC'; // Dental White
    const defaultGeneralColor = '#FFE5B4'; // Beige
    const defaultMedicalColor = '#0047AD'; // Blue

    let currentColor;
    if (fileData.color) {
        currentColor = '#' + fileData.color.toString(16).padStart(6, '0');
    } else {
        if (isDental) {
            currentColor = defaultDentalColor;
        } else if (formType === 'General') {
            currentColor = defaultGeneralColor;
        } else {
            currentColor = defaultMedicalColor;
        }
    }

    document.querySelectorAll('.simple-color-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.dataset.color.toLowerCase() === currentColor.toLowerCase()) {
            btn.classList.add('active');
        }
    });
    console.log('✅ Color set to:', currentColor);

    // Show/hide Layer Height section based on viewer type
    const layerHeightSection = document.getElementById('simpleLayerHeightSection');
    if (layerHeightSection) {
        if (isDental) {
            layerHeightSection.style.display = 'block';
            console.log('🦷 Layer Height section shown for dental viewer');
        } else {
            layerHeightSection.style.display = 'none';
        }
    }

    // Show modal
    const modal = document.getElementById('simpleFileModal');
    console.log('📦 Modal element:', modal);

    if (!modal) {
        console.error('❌ Modal element not found!');
        return;
    }

    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    console.log('✅✅✅ Simple modal opened successfully!');
}

function closeSimpleModal() {
    console.log('🔴 Close modal called');
    const modal = document.getElementById('simpleFileModal');
    if (!modal) {
        console.error('❌ Modal element not found');
        return;
    }
    modal.style.display = 'none';
    document.body.style.overflow = '';
    console.log('✅ Simple modal closed');
}

function selectSimpleColor(btn) {
    console.log('🎨 Color selected:', btn.dataset.color);
    document.querySelectorAll('.simple-color-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    console.log('✅ Color activated');
}

function saveSimpleModal() {
    console.log('💾 Save modal called');
    const fileId = window.simpleModalFileId;
    const formType = window.simpleModalFormType;

    console.log('📝 Modal data:', {fileId, formType});

    if (!fileId || !formType) {
        console.error('❌ No file selected');
        alert('No file selected. Please try again.');
        return;
    }

    const viewer = formType === 'General' ? window.viewerGeneral : window.viewer;
    if (!viewer) {
        console.error('❌ Viewer not found for formType:', formType);
        alert('Viewer not found. Please try again.');
        return;
    }

    console.log('✅ Viewer found:', viewer);

    const fileData = viewer.uploadedFiles.find(f => f.id === fileId);
    if (!fileData) {
        console.error('❌ File not found with id:', fileId);
        console.log('Available files:', viewer.uploadedFiles);
        alert('File not found. Please try again.');
        return;
    }

    console.log('✅ File data found:', fileData);

    // Get values
    const technology = document.getElementById('simpleTechSelect').value;
    const material = document.getElementById('simpleMaterialSelect').value;
    const activeBtn = document.querySelector('.simple-color-btn.active');
    const colorHex = activeBtn ? activeBtn.dataset.color : '#0047AD';
    const colorValue = parseInt(colorHex.replace('#', ''), 16);

    console.log('📊 New values:', {technology, material, colorHex, colorValue});

    // Update file data
    fileData.technology = technology;
    fileData.material = material;
    fileData.color = colorValue;

    console.log('✅ File data updated');

    // Update mesh color in 3D viewer
    if (fileData.mesh && fileData.mesh.material) {
        fileData.mesh.material.color.setHex(colorValue);
        console.log('✅ 3D mesh color updated');
    } else {
        console.warn('⚠️ No mesh found to update color');
    }

    // Update UI
    if (typeof updateFileList === 'function') {
        updateFileList(formType, viewer);
        console.log('✅ UI updated');
    }

    console.log('💚 File settings saved successfully:', {technology, material, color: colorHex});

    // Close modal
    closeSimpleModal();
}

// Make functions globally available IMMEDIATELY
window.openFileSettingsModal = openSimpleFileModal;
window.openSimpleFileModal = openSimpleFileModal;
window.closeSimpleModal = closeSimpleModal;
window.selectSimpleColor = selectSimpleColor;
window.saveSimpleModal = saveSimpleModal;

console.log('✅✅✅ Modal functions registered EARLY:', {
    openFileSettingsModal: typeof window.openFileSettingsModal,
    openSimpleFileModal: typeof window.openSimpleFileModal,
    closeSimpleModal: typeof window.closeSimpleModal
});

// ==========================================
// MAIN APPLICATION CODE
// ==========================================

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

                // Apply to selected file only - use unified viewer
                const viewer = window.viewerGeneral || window.viewer;
                if (window.selectedFileId && viewer) {
                    const fileData = viewer.uploadedFiles.find(f => f.id === window.selectedFileId);
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
                } else if (viewer && viewer.model) {
                    // Fallback: Apply to all models
                    viewer.changeModelColor(color);
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

                const viewer = window.viewerGeneral || window.viewer;
                if (window.selectedFileId && viewer) {
                    const fileData = viewer.uploadedFiles.find(f => f.id === window.selectedFileId);
                    if (fileData) {
                        if (!fileData.settings) fileData.settings = {};
                        fileData.settings.material = material;
                        fileData.settings.materialCost = cost;
                        console.log(`🔧 Material changed to ${material} for ${fileData.file.name}`);

                        // Recalculate price
                        if (window.calculateFilePrice) {
                            window.calculateFilePrice(window.selectedFileId, viewer, 'Medical');
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

                const viewer = window.viewerGeneral || window.viewer;
                if (window.selectedFileId && viewer) {
                    const fileData = viewer.uploadedFiles.find(f => f.id === window.selectedFileId);
                    if (fileData) {
                        if (!fileData.settings) fileData.settings = {};
                        fileData.settings.technology = technology;
                        fileData.settings.technologyMultiplier = multiplier;
                        console.log(`⚙️ Technology changed to ${technology} for ${fileData.file.name}`);

                        // Recalculate price
                        if (window.calculateFilePrice) {
                            window.calculateFilePrice(window.selectedFileId, viewer, 'Medical');
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
                const viewer = window.viewerGeneral || window.viewer;
                if (viewer && viewer.model) {
                    viewer.fitCameraToModel();
                }
            });
        }

        if (wireframeMed) {
            let wireframeMode = false;
            wireframeMed.addEventListener('click', () => {
                wireframeMode = !wireframeMode;
                console.log('🔲 Medical wireframe toggle:', wireframeMode);
                const viewer = window.viewerGeneral || window.viewer;
                if (viewer && viewer.model) {
                    viewer.toggleWireframe(wireframeMode);
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

                // Resize viewer after showing (use unified viewer)
                setTimeout(() => {
                    const viewer = window.viewerGeneral || window.viewer;
                    if (viewer && viewer.onWindowResize) {
                        viewer.onWindowResize();
                        console.log('✓ Viewer resized');
                    }

                    // If viewer has a model, fit it to view
                    if (viewer && viewer.model) {
                        viewer.fitCameraToModel();
                        console.log('✓ Model refitted to camera');
                    }

                    // Update quote if files are uploaded (unified viewer)
                    const unifiedViewer = window.viewerGeneral || window.viewer;
                    if (window.fileManagerMedical && unifiedViewer && unifiedViewer.uploadedFiles && unifiedViewer.uploadedFiles.length > 0) {
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

        // Expose unified viewer globally for controls
        window.viewerGeneral = viewerGeneral;
        // Note: window.viewerMedical removed - both use window.viewerGeneral now
        window.viewer = viewerGeneral; // Alias for compatibility

        // Initialize undo/redo state history
        if (window.viewerGeneral && window.toolbarHandler && window.toolbarHandler.saveState) {
            // Initialize state history arrays
            window.viewerGeneral.stateHistory = [];
            window.viewerGeneral.stateHistoryIndex = -1;
            
            // Set initial button states to disabled
            const undoBtn = document.getElementById('undoBtn');
            const redoBtn = document.getElementById('redoBtn');
            if (undoBtn) {
                undoBtn.classList.add('disabled');
                undoBtn.style.opacity = '0.4';
                undoBtn.style.cursor = 'not-allowed';
                undoBtn.style.pointerEvents = 'none';
                const undoSvg = undoBtn.querySelector('svg');
                if (undoSvg) {
                    undoSvg.style.color = '#95a5a6';
                    undoSvg.style.stroke = '#95a5a6';
                }
            }
            if (redoBtn) {
                redoBtn.classList.add('disabled');
                redoBtn.style.opacity = '0.4';
                redoBtn.style.cursor = 'not-allowed';
                redoBtn.style.pointerEvents = 'none';
                const redoSvg = redoBtn.querySelector('svg');
                if (redoSvg) {
                    redoSvg.style.color = '#95a5a6';
                    redoSvg.style.stroke = '#95a5a6';
                }
            }
            
            // Save initial state after a brief delay to ensure viewer is fully ready
            setTimeout(() => {
                if (window.viewerGeneral && window.viewerGeneral.scene && window.viewerGeneral.camera) {
                    window.toolbarHandler.saveState(window.viewerGeneral);
                    console.log('✅ Initial state saved for undo/redo');
                }
            }, 500);
        }

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
                const viewer = window.viewerGeneral || window.viewer;
                if (viewer && viewer.model) {
                    viewer.fitCameraToModel();
                }
            });
        }

        if (zoomInMedicalBtn) {
            zoomInMedicalBtn.addEventListener('click', () => {
                console.log('🔍 Medical zoom in clicked');
                const viewer = window.viewerGeneral || window.viewer;
                if (viewer && viewer.camera) {
                    viewer.camera.position.multiplyScalar(0.8);
                    viewer.controls.update();
                }
            });
        }

        if (zoomOutMedicalBtn) {
            zoomOutMedicalBtn.addEventListener('click', () => {
                console.log('🔍 Medical zoom out clicked');
                const viewer = window.viewerGeneral || window.viewer;
                if (viewer && viewer.camera) {
                    viewer.camera.position.multiplyScalar(1.2);
                    viewer.controls.update();
                }
            });
        }

        let isWireframeMedical = false;
        if (solidModeMedicalBtn && wireframeMedicalBtn) {
            solidModeMedicalBtn.addEventListener('click', () => {
                console.log('🎨 Medical solid mode clicked');
                const viewer = window.viewerGeneral || window.viewer;
                if (viewer && viewer.model) {
                    viewer.toggleWireframe(false);
                    isWireframeMedical = false;
                    solidModeMedicalBtn.classList.add('active');
                    wireframeMedicalBtn.classList.remove('active');
                }
            });

            wireframeMedicalBtn.addEventListener('click', () => {
                console.log('🎨 Medical wireframe mode clicked');
                const viewer = window.viewerGeneral || window.viewer;
                if (viewer && viewer.model) {
                    viewer.toggleWireframe(true);
                    isWireframeMedical = true;
                    wireframeMedicalBtn.classList.add('active');
                    solidModeMedicalBtn.classList.remove('active');
                }
            });
        }

        if (modelColorMedicalPicker) {
            modelColorMedicalPicker.addEventListener('change', (e) => {
                console.log('🎨 Medical model color changed:', e.target.value);
                const viewer = window.viewerGeneral || window.viewer;
                if (viewer && viewer.model) {
                    viewer.changeModelColor(e.target.value);
                }
            });
        }

        if (bgColorMedicalPicker) {
            bgColorMedicalPicker.addEventListener('change', (e) => {
                console.log('🎨 Medical background color changed:', e.target.value);
                const viewer = window.viewerGeneral || window.viewer;
                if (viewer && viewer.scene) {
                    viewer.changeBGColor(e.target.value);
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
                        const viewer = window.viewerGeneral || window.viewer;
                        if (viewer) {
                            const container = viewerMedicalElement.parentElement;
                            const width = container.clientWidth;
                            const height = Math.min(container.clientHeight, 700);
                            viewer.renderer.setSize(width, height);
                            viewer.camera.aspect = width / height;
                            viewer.camera.updateProjectionMatrix();
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
                        const viewer = window.viewerGeneral || window.viewer;
                        if (viewer) {
                            const container = viewerMedicalElement.parentElement;
                            const width = container.clientWidth;
                            const height = Math.min(container.clientHeight, 700);
                            viewer.renderer.setSize(width, height);
                            viewer.camera.aspect = width / height;
                            viewer.camera.updateProjectionMatrix();
                        }
                    }, 100);
                }
            });
        }

        console.log('✓ Medical viewer controls ready');
        console.log('✓ All viewer controls initialized!');

        // Setup Medical toolbar buttons to use unified viewer
        setupMedicalToolbar();

        // Setup file list update handlers
        setupFileListUpdates();
    });

    // Setup Medical Toolbar Button Event Listeners
    function setupMedicalToolbar() {
        console.log('🔧 Setting up Medical toolbar buttons...');

        const viewer = window.viewerGeneral || window.viewer;
        if (!viewer) {
            console.warn('❌ No viewer found for Medical toolbar setup');
            return;
        }

        // Ensure window.toolbarHandler exists (should be defined in professional-tools.js)
        if (!window.toolbarHandler) {
            console.warn('❌ window.toolbarHandler not found, creating minimal version');
            window.toolbarHandler = {
                toggleMeasurement: function(type) { console.log('Measurement toggled'); },
                toggleBoundingBox: function(type) { console.log('Bounding box toggled'); },
                toggleAxis: function(type) { console.log('Axis toggled'); },
                toggleGrid: function(type) { console.log('Grid toggled'); },
                toggleMoveMode: function(type) { console.log('Move mode toggled'); },
                toggleAutoRotate: function(type) { console.log('Auto-rotate toggled'); },
                toggleGridMain: function(type) { console.log('Grid main toggled'); },
                toggleMeasureMain: function(type) { console.log('Measure main toggled'); },
                toggleShadow: function(type) { console.log('Shadow toggled'); },
                toggleTransparency: function(type) { console.log('Transparency toggled'); },
                takeScreenshot: function(type) { console.log('Screenshot taken'); },
                shareModel: function(type) { console.log('Share clicked'); },
                saveAndCalculate: function(type) { console.log('Save & Calculate clicked'); },
                undo: function() {
                    console.log('⏪ Undo action');
                    const viewer = window.viewerGeneral || window.viewer;

                    if (!viewer || !viewer.stateHistory) {
                        viewer.stateHistory = [];
                        viewer.stateHistoryIndex = -1;
                    }

                    if (viewer.stateHistoryIndex > 0) {
                        viewer.stateHistoryIndex--;
                        const state = viewer.stateHistory[viewer.stateHistoryIndex];
                        if (window.toolbarHandler.restoreState) {
                            window.toolbarHandler.restoreState(viewer, state);
                        }
                        showToolbarNotification('Undone', 'success', 1000);
                        
                        // Update button states
                        if (typeof updateUndoRedoButtons === 'function') {
                            updateUndoRedoButtons();
                        }
                    } else {
                        showToolbarNotification('Nothing to undo', 'info', 1500);
                    }
                },
                redo: function() {
                    console.log('⏩ Redo action');
                    const viewer = window.viewerGeneral || window.viewer;

                    if (!viewer || !viewer.stateHistory) {
                        viewer.stateHistory = [];
                        viewer.stateHistoryIndex = -1;
                    }

                    if (viewer.stateHistoryIndex < viewer.stateHistory.length - 1) {
                        viewer.stateHistoryIndex++;
                        const state = viewer.stateHistory[viewer.stateHistoryIndex];
                        if (window.toolbarHandler.restoreState) {
                            window.toolbarHandler.restoreState(viewer, state);
                        }
                        showToolbarNotification('Redone', 'success', 1000);
                        
                        // Update button states
                        if (typeof updateUndoRedoButtons === 'function') {
                            updateUndoRedoButtons();
                        }
                    } else {
                        showToolbarNotification('Nothing to redo', 'info', 1500);
                    }
                },
                changeModelColor: function() { console.log('Model color change'); },
                changeBackgroundColor: function() { console.log('BG color change'); },
                toggleMobileMenu: function() { console.log('Mobile menu toggle'); },
                setCameraView: function(view) { console.log('Camera view:', view); }
            };
        }

        // Add mobile menu toggle functionality
        if (!window.toolbarHandler.toggleMobileMenu) {
            window.toolbarHandler.toggleMobileMenu = function() {
                const mobileMenu = document.getElementById('toolbarMobileMenu');
                const burgerBtn = document.getElementById('toolbarBurgerMenu');
                
                if (mobileMenu && burgerBtn) {
                    const isActive = mobileMenu.classList.contains('active');
                    
                    if (isActive) {
                        mobileMenu.classList.remove('active');
                        burgerBtn.classList.remove('active');
                        document.body.style.overflow = '';
                    } else {
                        mobileMenu.classList.add('active');
                        burgerBtn.classList.add('active');
                        document.body.style.overflow = 'hidden';
                    }
                    
                    console.log('📱 Mobile menu toggled:', !isActive);
                }
            };
        }

        // Add mobile files panel toggle functionality
        if (!window.toolbarHandler.toggleMobileFilesPanel) {
            window.toolbarHandler.toggleMobileFilesPanel = function() {
                const filesPanel = document.getElementById('rightFilesPanel');
                const floatBtn = document.getElementById('mobileFloatUploadBtn');
                
                if (filesPanel) {
                    const isOpen = filesPanel.classList.contains('mobile-open');
                    
                    if (isOpen) {
                        // Close panel
                        filesPanel.classList.remove('mobile-open');
                        document.body.style.overflow = '';
                        
                        // On mobile, hide the panel completely when closed
                        if (window.innerWidth <= 768) {
                            setTimeout(() => {
                                if (!filesPanel.classList.contains('mobile-open')) {
                                    filesPanel.style.display = 'none';
                                }
                            }, 300); // Wait for animation to finish
                        }
                        
                        console.log('📁 Mobile files panel closed');
                    } else {
                        // Open panel
                        if (window.innerWidth <= 768) {
                            filesPanel.style.display = 'flex';
                        }
                        
                        // Small delay to allow display change before animation
                        setTimeout(() => {
                            filesPanel.classList.add('mobile-open');
                            document.body.style.overflow = 'hidden';
                        }, 10);
                        
                        console.log('📁 Mobile files panel opened');
                    }
                }
            };
        }

        // Add camera view setter if not exists
        if (!window.toolbarHandler.setCameraView) {
            window.toolbarHandler.setCameraView = function(view) {
                console.log('📷 Setting camera view:', view);
                const viewer = window.viewerGeneral || window.viewer;
                
                if (!viewer || !viewer.controls || !viewer.camera) {
                    console.warn('⚠️ Viewer not ready for camera change');
                    return;
                }

                // Save state before changing camera
                if (window.toolbarHandler.saveState) {
                    window.toolbarHandler.saveState(viewer);
                }

                const controls = viewer.controls;
                const camera = viewer.camera;
                
                // Reset camera position based on view
                const distance = 150;
                
                switch(view) {
                    case 'top':
                        camera.position.set(0, distance, 0);
                        break;
                    case 'bottom':
                        camera.position.set(0, -distance, 0);
                        break;
                    case 'front':
                        camera.position.set(0, 0, distance);
                        break;
                    case 'back':
                        camera.position.set(0, 0, -distance);
                        break;
                    case 'right':
                        camera.position.set(distance, 0, 0);
                        break;
                    case 'left':
                        camera.position.set(-distance, 0, 0);
                        break;
                    case 'reset':
                        camera.position.set(100, 100, 100);
                        break;
                }
                
                camera.lookAt(controls.target);
                controls.update();
                
                if (viewer.renderer) {
                    viewer.renderer.render(viewer.scene, camera);
                }
                
                console.log('📷 Camera view changed to:', view);
            };
        }

        // Add saveState function for undo/redo
        if (!window.toolbarHandler.saveState) {
            window.toolbarHandler.saveState = function(viewer) {
                if (!viewer.stateHistory) {
                    viewer.stateHistory = [];
                    viewer.stateHistoryIndex = -1;
                }

                const THREE = window.THREE;

                // Capture current state
                const state = {
                    cameraPosition: viewer.camera.position.clone(),
                    cameraRotation: viewer.camera.rotation.clone(),
                    transparency: viewer.currentTransparencyIndex || 0,
                    shadows: viewer.renderer.shadowMap.enabled,
                    backgroundColor: viewer.scene.background ? viewer.scene.background.getHex() : 0xffffff,
                    modelColors: [],
                    toolsVisible: {
                        boundingBox: !!viewer.scene.children.find(child => child.userData && child.userData.isBoundingBoxHelper && child.visible),
                        axis: !!viewer.scene.children.find(child => child.userData && child.userData.isAxisHelper && child.visible),
                        grid: !!viewer.scene.children.find(child => child.userData && child.userData.isGridHelper && child.visible)
                    }
                };

                // Capture model colors
                viewer.scene.traverse((object) => {
                    if (object.isMesh && object.material) {
                        state.modelColors.push({
                            uuid: object.uuid,
                            color: object.material.color.getHex()
                        });
                    }
                });

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

                // Update button states
                if (typeof updateUndoRedoButtons === 'function') {
                    updateUndoRedoButtons();
                }

                console.log(`💾 State saved (${viewer.stateHistoryIndex + 1}/${viewer.stateHistory.length})`);
            };
        }

        // Add restoreState function for undo/redo
        if (!window.toolbarHandler.restoreState) {
            window.toolbarHandler.restoreState = function(viewer, state) {
                if (!state) return;

                const THREE = window.THREE;

                // Restore camera
                viewer.camera.position.copy(state.cameraPosition);
                viewer.camera.rotation.copy(state.cameraRotation);

                // Restore transparency
                viewer.currentTransparencyIndex = state.transparency;

                // Restore shadows
                viewer.renderer.shadowMap.enabled = state.shadows;

                // Restore background color
                if (state.backgroundColor !== undefined && THREE) {
                    viewer.scene.background = new THREE.Color(state.backgroundColor);
                }

                // Restore model colors
                if (state.modelColors && state.modelColors.length > 0) {
                    viewer.scene.traverse((object) => {
                        if (object.isMesh && object.material) {
                            const savedColor = state.modelColors.find(c => c.uuid === object.uuid);
                            if (savedColor && THREE) {
                                object.material.color.setHex(savedColor.color);
                                object.material.needsUpdate = true;
                            }
                        }
                    });
                }

                // Restore tools visibility
                const boundingBox = viewer.scene.children.find(child => child.userData && child.userData.isBoundingBoxHelper);
                if (boundingBox) boundingBox.visible = state.toolsVisible.boundingBox;

                const axis = viewer.scene.children.find(child => child.userData && child.userData.isAxisHelper);
                if (axis) axis.visible = state.toolsVisible.axis;

                const grid = viewer.scene.children.find(child => child.userData && child.userData.isGridHelper);
                if (grid) grid.visible = state.toolsVisible.grid;

                // Update controls
                if (viewer.controls) {
                    viewer.controls.update();
                }

                // Render
                if (viewer.renderer && viewer.scene && viewer.camera) {
                    viewer.renderer.render(viewer.scene, viewer.camera);
                }

                console.log('✅ State restored');
            };
        }

        // Medical toolbar buttons (with Medical suffix)
        const medicalButtons = {
            'measurementToolBtnMedical': () => window.toolbarHandler.toggleMeasurement('General'),
            'boundingBoxBtnMedical': () => window.toolbarHandler.toggleBoundingBox('General'),
            'axisToggleBtnMedical': () => window.toolbarHandler.toggleAxis('General'),
            'gridToggleBtnMedical': () => window.toolbarHandler.toggleGrid('General'),
            'panToolBtnMedical': () => window.toolbarHandler.toggleMoveMode('General'),
            'autoRotateBtnMedical': () => window.toolbarHandler.toggleAutoRotate('General'),
            'toggleGridBtnMainMedical': () => window.toolbarHandler.toggleGridMain('General'),
            'measureToolBtnMainMedical': () => window.toolbarHandler.toggleMeasureMain('General'),
            'shadowToggleBtnMedical': () => window.toolbarHandler.toggleShadow('General'),
            'transparencyBtnMedical': () => window.toolbarHandler.toggleTransparency('General'),
            'modelColorBtnMedical': () => window.toolbarHandler.changeModelColor(),
            'backgroundColorBtnMedical': () => window.toolbarHandler.changeBackgroundColor(),
            'undoBtnMedical': () => window.toolbarHandler.undo(),
            'redoBtnMedical': () => window.toolbarHandler.redo(),
            'screenshotToolBtnMedical': () => window.toolbarHandler.takeScreenshot('General'),
            'shareToolBtnMedical': () => window.toolbarHandler.shareModel('General'),
            'saveCalculateToolBtnMedical': () => window.toolbarHandler.saveAndCalculate('General')
        };

        // Attach event listeners
        // DISABLED: This was removing onclick handlers and breaking buttons
        /*
        Object.keys(medicalButtons).forEach(btnId => {
            const btn = document.getElementById(btnId);
            if (btn) {
                // Remove inline onclick if exists to prevent double firing
                btn.removeAttribute('onclick');
                btn.addEventListener('click', medicalButtons[btnId]);
                console.log(`✅ ${btnId} listener attached`);
            } else {
                console.warn(`⚠️ Button not found: ${btnId}`);
            }
        });
        */
        console.log('✅ Using onclick handlers instead of addEventListener (Medical)');

        // Camera view buttons for Medical toolbar
        const medicalToolbar = document.getElementById('professionalToolbarMedical');
        if (medicalToolbar) {
            medicalToolbar.querySelectorAll('.camera-btn').forEach(btn => {
                const view = btn.dataset.view;
                btn.addEventListener('click', function() {
                    console.log(`📷 Camera view: ${view}`);

                    // Remove active from siblings
                    medicalToolbar.querySelectorAll('.camera-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    // Apply camera view
                    if (viewer?.scene?.activeCamera) {
                        const camera = viewer.scene.activeCamera;
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
                            console.log(`✅ Camera set to ${view}`);
                        }
                    }
                });
            });
            console.log('✅ Medical camera buttons initialized');
        }

        console.log('✅ Medical toolbar setup complete');
    }

    // Function to update file lists
    function setupFileListUpdates() {
        // Listen for pricing updates which happen after files are loaded
        window.addEventListener('pricingUpdateNeeded', (event) => {
            const { viewerId } = event.detail;

            // Use unified viewer for both General and Medical
            const viewer = window.viewerGeneral || window.viewer;

            if (viewerId === 'viewer3dGeneral' && viewer) {
                updateFileList('General', viewer);
            } else if (viewerId === 'viewer3dMedical' && viewer) {
                updateFileList('Medical', viewer);
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
                    <!-- Order Number -->
                    <div class="file-order-number" style="width: 24px; height: 24px; background: linear-gradient(135deg, #0047AD 0%, #003580 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.7rem; font-weight: bold; margin-right: 8px; flex-shrink: 0;">
                        ${index + 1}
                    </div>
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

        // Dispatch event to notify bottom bar of file count
        window.dispatchEvent(new CustomEvent('filesUploaded', {
            detail: { count: files.length, formType: formType }
        }));

        // Attach visibility toggle handlers
        attachVisibilityHandlers(formType, viewer);

        // Select first file by default if none selected
        if (files.length > 0 && !window.selectedFileId) {
            selectFile(formType, files[0].id);
        }

        // Update right panel files
        updateRightPanelFiles(formType, viewer);
    }

    // Update Right Panel Files with Beautiful Blue Cards
    function updateRightPanelFiles(formType, viewer) {
        console.log('🔵 updateRightPanelFiles called:', { formType, viewer, files: viewer?.uploadedFiles });

        if (formType !== 'General') {
            console.log('⚠️ Skipping right panel update - not General formType');
            return; // Only for General viewer for now
        }

        const rightFilesContainer = document.getElementById('rightFilesContainer');
        if (!rightFilesContainer) {
            console.error('❌ rightFilesContainer not found!');
            return;
        }

        const files = viewer.uploadedFiles || [];
        console.log('📁 Files to render in right panel:', files.length, files);

        if (files.length === 0) {
            console.log('📭 No files - showing empty state');
            rightFilesContainer.innerHTML = `
                <div style="text-align: center; padding: 40px 20px; color: #999;">
                    <svg width="48" height="48" viewBox="0 0 48 48" fill="none" style="margin: 0 auto 12px;">
                        <path d="M24 8L40 16L24 24L8 16L24 8Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M8 32L24 40L40 32" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M8 24L24 32L40 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <p style="margin: 0; font-size: 13px;">No files uploaded yet</p>
                </div>
            `;
            return;
        }

        // Build beautiful blue card list with order numbers
        rightFilesContainer.innerHTML = files.map((fileData, index) => `
            <div class="file-list-item d-flex align-items-center justify-content-between p-2 border-bottom"
                 style="background: white; cursor: pointer; transition: all 0.2s;"
                 data-file-id="${fileData.id}"
                 onclick="event.stopPropagation(); console.log('🎯 File card clicked!', '${formType}', ${fileData.id}); if(window.openSimpleFileModal) { window.openSimpleFileModal('${formType}', ${fileData.id}); } else { console.error('openSimpleFileModal not found'); }">
                <div class="d-flex align-items-center flex-grow-1">
                    <!-- Order Number -->
                    <div class="file-order-number" style="width: 24px; height: 24px; background: linear-gradient(135deg, #1976D2 0%, #1565C0 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.7rem; font-weight: bold; margin-right: 8px; flex-shrink: 0;">
                        ${index + 1}
                    </div>
                    <div class="file-icon me-2" style="width: 32px; height: 32px; background: #1976D2; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.7rem; font-weight: bold;">
                        ${getFileExtension(fileData.file.name).toUpperCase()}
                    </div>
                    <div class="flex-grow-1">
                        <div style="font-size: 0.85rem; font-weight: 600; color: #2c3e50;">${fileData.file.name}</div>
                        <small style="font-size: 0.7rem; color: #6c757d;">
                            ${formatFileSize(fileData.file.size)} • ${(fileData.volume?.cm3 || 0).toFixed(2)} cm³
                        </small>
                    </div>
                </div>
                <div class="d-flex gap-1" onclick="event.stopPropagation()">
                    {{-- Eye Icon - Toggle Visibility --}}
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
                    {{-- Download Icon --}}
                    <button class="btn btn-sm btn-outline-success" onclick="event.stopPropagation(); downloadFileFromRightPanel('${formType}', ${fileData.id})" title="Download file" style="padding: 2px 6px; font-size: 0.75rem; border-radius: 4px;">
                        <svg width="12" height="12" viewBox="0 0 16 16" fill="currentColor">
                            <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                            <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                        </svg>
                    </button>
                    {{-- Delete Icon --}}
                    <button class="btn btn-sm btn-outline-danger" onclick="event.stopPropagation(); removeFileFromRightPanel('${formType}', ${fileData.id})" title="Delete file" style="padding: 2px 6px; font-size: 0.75rem; border-radius: 4px;">
                        <svg width="12" height="12" viewBox="0 0 16 16" fill="currentColor">
                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                            <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                        </svg>
                    </button>
                </div>
            </div>
        `).join('');

        console.log('✅ Right panel HTML generated successfully:', rightFilesContainer.innerHTML.substring(0, 200));

        // Attach visibility handlers to right panel buttons
        const rightVisibilityButtons = document.querySelectorAll('#rightFilesContainer .toggle-visibility-btn');
        console.log(`👁️ Attaching ${rightVisibilityButtons.length} RIGHT panel visibility handlers for ${formType}`);

        rightVisibilityButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const fileId = parseFloat(btn.getAttribute('data-file-id'));
                toggleFileVisibility(formType, fileId, btn, viewer);
            });
        });

        // Attach drag-and-drop handlers
        attachRightPanelDragHandlers();
    }

    // Download file from right panel
    window.downloadFileFromRightPanel = function(formType, fileId) {
        const viewer = formType === 'General' ? window.viewerGeneral : window.viewer;
        if (!viewer) return;

        const fileData = viewer.uploadedFiles.find(f => f.id === fileId);
        if (!fileData || !fileData.file) return;

        // Create a download link and trigger it
        const url = URL.createObjectURL(fileData.file);
        const a = document.createElement('a');
        a.href = url;
        a.download = fileData.file.name;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    };

    // View file from right panel
    window.viewFileInRightPanel = function(formType, fileId) {
        selectFile(formType, fileId);
        // Scroll left sidebar to show the selected file
        const fileListItem = document.querySelector(`[data-file-id="${fileId}"]`);
        if (fileListItem && fileListItem.scrollIntoView) {
            fileListItem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    };

    // Remove file from right panel
    window.removeFileFromRightPanel = function(formType, fileId) {
        removeFile(formType, fileId);
    };

    // OLD MODAL CODE REMOVED - Using new simple modal at end of file
    // The openFileSettingsModal function is defined at the bottom with the modal HTML

    // OLD MODAL EVENT HANDLERS REMOVED - NOW USING SIMPLE MODAL

    // Drag and drop functionality for right panel
    function attachRightPanelDragHandlers() {
        const cards = document.querySelectorAll('.right-file-card');
        let draggedElement = null;

        cards.forEach(card => {
            card.addEventListener('dragstart', function(e) {
                draggedElement = this;
                this.classList.add('dragging');
                e.dataTransfer.effectAllowed = 'move';
            });

            card.addEventListener('dragend', function(e) {
                this.classList.remove('dragging');
                draggedElement = null;
            });

            card.addEventListener('dragover', function(e) {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';

                if (draggedElement && draggedElement !== this) {
                    const container = this.parentNode;
                    const allCards = Array.from(container.querySelectorAll('.right-file-card'));
                    const draggedIndex = allCards.indexOf(draggedElement);
                    const targetIndex = allCards.indexOf(this);

                    if (draggedIndex < targetIndex) {
                        this.parentNode.insertBefore(draggedElement, this.nextSibling);
                    } else {
                        this.parentNode.insertBefore(draggedElement, this);
                    }
                }
            });
        });
    }

    // Toggle right panel visibility
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('toggleRightPanel');
        const rightPanel = document.getElementById('rightFilesPanel');
        let isPanelVisible = true;

        if (toggleBtn && rightPanel) {
            toggleBtn.addEventListener('click', function() {
                isPanelVisible = !isPanelVisible;
                if (isPanelVisible) {
                    rightPanel.style.display = 'flex';
                    toggleBtn.innerHTML = `
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M5 10L15 10M10 5L10 15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    `;
                } else {
                    rightPanel.style.display = 'none';
                    toggleBtn.innerHTML = `
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M5 10L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    `;
                }
            });
        }
    });

    // Select a file to edit its properties
    window.selectFile = function(formType, fileId) {
        const viewer = window.viewerGeneral || window.viewer;
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

        // Update the button that was clicked
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

        // Sync with RIGHT panel eye button
        const rightEyeBtn = document.querySelector(`#rightFilesContainer .file-list-item[data-file-id="${fileId}"] .toggle-visibility-btn`);
        if (rightEyeBtn && rightEyeBtn !== button) {
            const rightEyeVisible = rightEyeBtn.querySelector('.eye-visible');
            const rightEyeHidden = rightEyeBtn.querySelector('.eye-hidden');

            if (fileData.mesh.visible) {
                rightEyeVisible.style.display = 'block';
                rightEyeHidden.style.display = 'none';
                rightEyeBtn.classList.remove('btn-outline-secondary');
                rightEyeBtn.classList.add('btn-outline-primary');
            } else {
                rightEyeVisible.style.display = 'none';
                rightEyeHidden.style.display = 'block';
                rightEyeBtn.classList.remove('btn-outline-primary');
                rightEyeBtn.classList.add('btn-outline-secondary');
            }
        }

        // Sync with LEFT panel eye button
        const leftEyeBtn = document.querySelector(`#filesContainer .file-list-item[data-file-id="${fileId}"] .toggle-visibility-btn`);
        if (leftEyeBtn && leftEyeBtn !== button) {
            const leftEyeVisible = leftEyeBtn.querySelector('.eye-visible');
            const leftEyeHidden = leftEyeBtn.querySelector('.eye-hidden');

            if (fileData.mesh.visible) {
                leftEyeVisible.style.display = 'block';
                leftEyeHidden.style.display = 'none';
                leftEyeBtn.classList.remove('btn-outline-secondary');
                leftEyeBtn.classList.add('btn-outline-primary');
            } else {
                leftEyeVisible.style.display = 'none';
                leftEyeHidden.style.display = 'block';
                leftEyeBtn.classList.remove('btn-outline-primary');
                leftEyeBtn.classList.add('btn-outline-secondary');
            }
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
        const viewer = window.viewerGeneral || window.viewer;
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
        const viewer = window.viewerGeneral || window.viewer;
        if (viewer) {
            viewer.removeFile(fileId);
            updateFileList(formType, viewer);

            // Delete from IndexedDB storage
            if (window.fileStorageManager && window.fileStorageManager.currentFileId) {
                try {
                    await window.fileStorageManager.deleteFile(window.fileStorageManager.currentFileId);
                    window.fileStorageManager.currentFileId = null;

                    // Clear file parameter but preserve viewer parameter
                    const url = new URL(window.location.href);
                    const viewerParam = url.searchParams.get('viewer');
                    url.searchParams.delete('file');
                    if (viewerParam) {
                        url.searchParams.set('viewer', viewerParam);
                    }
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

    // Inject Screenshot button into bottom toolbar (Share & Save moved to top toolbar)
    function injectToolbarButtons() {
        console.log('🔧 Injecting screenshot button (Share & Save moved to top toolbar)...');

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

            // Create Screenshot button only (Share and Save now in top toolbar)
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

            // Add button to toolbar
            toolsSection.appendChild(screenshotBtn);

            console.log(`✅ Added screenshot button to toolbar ${index}`);

            // Add event listener for screenshot
            screenshotBtn.addEventListener('click', function() {
                console.log('📸 Screenshot button clicked (bottom toolbar)');
                const viewerId = isGeneral ? 'viewer3dGeneral' : 'viewer3dMedical';
                const viewer = window.viewerGeneral || window.viewer;

                if (!viewer || !viewer.renderer || !viewer.scene || !viewer.camera) {
                    console.error('Viewer not ready for screenshot');
                    showToolbarNotification('Viewer not ready, please wait...', 'warning', 2000);
                    return;
                }

                try {
                    // Force a fresh render before capturing
                    viewer.renderer.render(viewer.scene, viewer.camera);

                    // Get the canvas element
                    const canvas = viewer.renderer.domElement;

                    // Create a new canvas with white background
                    const screenshotCanvas = document.createElement('canvas');
                    screenshotCanvas.width = canvas.width;
                    screenshotCanvas.height = canvas.height;
                    const ctx = screenshotCanvas.getContext('2d');

                    // Fill with white background
                    ctx.fillStyle = '#ffffff';
                    ctx.fillRect(0, 0, screenshotCanvas.width, screenshotCanvas.height);

                    // Draw the 3D viewer canvas on top
                    ctx.drawImage(canvas, 0, 0);

                    // Convert to data URL with high quality
                    const dataURL = screenshotCanvas.toDataURL('image/png', 1.0);

                    // Create download link
                    const timestamp = new Date().toISOString().replace(/[:.]/g, '-').substring(0, 19);
                    const filename = `3d-model-${isGeneral ? 'general' : 'medical'}-${timestamp}.png`;
                    const link = document.createElement('a');
                    link.download = filename;
                    link.href = dataURL;
                    link.click();

                    console.log('✅ Screenshot captured successfully:', filename);
                    showToolbarNotification('Screenshot saved! ✓', 'success', 2000);

                    // Visual feedback
                    this.classList.add('active');
                    setTimeout(() => this.classList.remove('active'), 500);
                } catch (error) {
                    console.error('Screenshot error:', error);
                    showToolbarNotification('Screenshot failed: ' + error.message, 'error', 3000);
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
        // Always use unified viewer
        const viewer = window.viewerGeneral || window.viewer;
        if (!viewer?.scene?.activeCamera) {
            console.log('No active camera found');
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
        // Always use unified viewer
        const viewer = window.viewerGeneral || window.viewer;
        if (!viewer?.scene) {
            console.log('No scene found');
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
        // Always use unified viewer
        const viewer = window.viewerGeneral || window.viewer;
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
        // Always use unified viewer
        const viewer = window.viewerGeneral || window.viewer;
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
        const viewer = window.viewerGeneral || window.viewer;
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
            // Try to find viewer (unified)
            currentViewer = window.viewerGeneral || window.viewer;
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

            // Disable orbit controls rotation, keep zoom (unified viewer)
            const viewer = window.viewerGeneral || window.viewer;
            if (viewer && viewer.controls) {
                viewer.controls.enableRotate = false;
            }

            console.log('✋ Pan mode ACTIVE - Drag to move the model');
            showMeasurementNotification('Drag to move the model', 'info', 3000);
        } else {
            // Deactivate pan mode
            button.classList.remove('active');
            button.style.background = '';
            button.style.color = '';

            // Re-enable orbit controls rotation (unified viewer)
            const viewer = window.viewerGeneral || window.viewer;
            if (viewer && viewer.controls) {
                viewer.controls.enableRotate = true;
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
        const viewer = window.viewerGeneral || window.viewer;

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

        // Initialize undo/redo system
        initUndoRedoSystem();

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

        // Medical viewer uses the same unified viewer
        const viewer = window.viewerGeneral || window.viewer;
        if (viewer && viewer.renderer) {
            const canvas = viewer.renderer.domElement;
            // Event listeners already added above for unified viewer
            console.log('✅ Medical viewer uses unified viewer - handlers already attached');
        }
    });

    // ============================================
    // UNDO/REDO SYSTEM INITIALIZATION
    // ============================================

    function initUndoRedoSystem() {
        console.log('⏪⏩ Initializing undo/redo system...');
        
        const viewer = window.viewerGeneral || window.viewer;
        
        if (!viewer) {
            console.warn('⚠️ No viewer available for undo/redo initialization');
            return;
        }

        console.log('✅ Viewer found for undo/redo:', !!viewer);
        console.log('✅ toolbarHandler exists:', !!window.toolbarHandler);
        console.log('✅ saveState method exists:', typeof window.toolbarHandler.saveState);

        // Initialize state history
        if (!viewer.stateHistory) {
            viewer.stateHistory = [];
            viewer.stateHistoryIndex = -1;
            console.log('📝 Initialized empty state history');
        }

        // Save initial state
        if (window.toolbarHandler && typeof window.toolbarHandler.saveState === 'function') {
            window.toolbarHandler.saveState(viewer);
            console.log('✅ Initial state saved. History length:', viewer.stateHistory.length);
            console.log('✅ Current index:', viewer.stateHistoryIndex);
        } else {
            console.error('❌ Cannot save initial state - saveState method not found!');
        }

        // Track camera movements for undo/redo
        if (viewer.controls) {
            let cameraChangeTimeout;
            viewer.controls.addEventListener('end', () => {
                // Debounce camera changes - only save state after user stops moving camera
                clearTimeout(cameraChangeTimeout);
                cameraChangeTimeout = setTimeout(() => {
                    if (window.toolbarHandler && typeof window.toolbarHandler.saveState === 'function') {
                        window.toolbarHandler.saveState(viewer);
                        updateUndoRedoButtons();
                        console.log('📷 Camera state saved');
                    }
                }, 500); // Wait 500ms after camera movement stops
            });
        }

        // Update button states initially
        updateUndoRedoButtons();

        console.log('✅ Undo/redo system initialized');
    }

    function updateUndoRedoButtons() {
        const viewer = window.viewerGeneral || window.viewer;
        
        if (!viewer || !viewer.stateHistory) {
            console.log('⚠️ updateUndoRedoButtons: No viewer or state history');
            return;
        }

        console.log(`📊 State: ${viewer.stateHistoryIndex + 1}/${viewer.stateHistory.length}`);

        const undoBtn = document.getElementById('undoBtn');
        const redoBtn = document.getElementById('redoBtn');

        // Update undo button
        if (undoBtn) {
            const undoSvg = undoBtn.querySelector('svg');
            if (viewer.stateHistoryIndex > 0) {
                undoBtn.classList.remove('disabled');
                undoBtn.style.opacity = '1';
                undoBtn.style.cursor = 'pointer';
                undoBtn.style.pointerEvents = 'auto';
                undoBtn.title = `Undo (${viewer.stateHistoryIndex} actions available)`;
                // Change icon color to black when active
                if (undoSvg) {
                    undoSvg.style.color = '#2c3e50';
                    undoSvg.style.stroke = '#2c3e50';
                }
                console.log('✅ Undo button ENABLED');
            } else {
                undoBtn.classList.add('disabled');
                undoBtn.style.opacity = '0.4';
                undoBtn.style.cursor = 'not-allowed';
                undoBtn.style.pointerEvents = 'none';
                undoBtn.title = 'Nothing to undo';
                // Change icon color to gray when disabled
                if (undoSvg) {
                    undoSvg.style.color = '#95a5a6';
                    undoSvg.style.stroke = '#95a5a6';
                }
            }
        }

        // Update redo button
        if (redoBtn) {
            const redoSvg = redoBtn.querySelector('svg');
            const redoAvailable = viewer.stateHistoryIndex < viewer.stateHistory.length - 1;
            if (redoAvailable) {
                redoBtn.classList.remove('disabled');
                redoBtn.style.opacity = '1';
                redoBtn.style.cursor = 'pointer';
                redoBtn.style.pointerEvents = 'auto';
                redoBtn.title = `Redo (${viewer.stateHistory.length - viewer.stateHistoryIndex - 1} actions available)`;
                // Change icon color to black when active
                if (redoSvg) {
                    redoSvg.style.color = '#2c3e50';
                    redoSvg.style.stroke = '#2c3e50';
                }
            } else {
                redoBtn.classList.add('disabled');
                redoBtn.style.opacity = '0.4';
                redoBtn.style.cursor = 'not-allowed';
                redoBtn.style.pointerEvents = 'none';
                redoBtn.title = 'Nothing to redo';
                // Change icon color to gray when disabled
                if (redoSvg) {
                    redoSvg.style.color = '#95a5a6';
                    redoSvg.style.stroke = '#95a5a6';
                }
            }
        }
    }

    // ============================================
    // END UNDO/REDO SYSTEM INITIALIZATION
    // ============================================

    // ============================================
    // END MEASUREMENT TOOL
    // ============================================
});
</script>

<script src="{{ asset('frontend/assets/js/3d-viewer-pro.js') }}?t={{ time() }}"></script>
<script src="{{ asset('frontend/assets/js/lighting-controller.js') }}?t={{ time() }}"></script>
<script src="{{ asset('frontend/assets/js/measurement-manager.js') }}?t={{ time() }}"></script>
<script src="{{ asset('frontend/assets/js/volume-calculator.js') }}?v={{ time() }}"></script>
<script src="{{ asset('frontend/assets/js/pricing-calculator.js') }}?v={{ time() }}"></script>
{{-- REMOVED: simple-save-calculate.js - Conflicts with enhanced-save-calculate.js --}}
{{-- <script src="{{ asset('frontend/assets/js/simple-save-calculate.js') }}?v={{ time() }}"></script> --}}
<script src="{{ asset('frontend/assets/js/debug-calculator.js') }}?v={{ time() }}"></script>

{{-- INLINE HANDLER DEFINITION - Put it directly in HTML to bypass loading issues --}}
<script>
console.log('🚀 INLINE SCRIPT STARTING...');

// Professional notification system - BOTTOM CENTER positioning
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
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        background: ${style.bg};
        color: white;
        padding: 16px 24px;
        border-radius: 12px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        z-index: 9998;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        font-size: 15px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 12px;
        animation: slideUp 0.3s ease-out;
        pointer-events: auto;
        cursor: pointer;
        min-width: 300px;
        justify-content: center;
    `;

    notification.innerHTML = `
        <span style="font-size: 20px; font-weight: bold;">${style.icon}</span>
        <span>${message}</span>
    `;

    // Add animation keyframes if not already added
    if (!document.getElementById('toolbar-notification-styles')) {
        const styleSheet = document.createElement('style');
        styleSheet.id = 'toolbar-notification-styles';
        styleSheet.textContent = `
            @keyframes slideUp {
                from { transform: translateX(-50%) translateY(100px); opacity: 0; }
                to { transform: translateX(-50%) translateY(0); opacity: 1; }
            }
            @keyframes slideDown {
                from { transform: translateX(-50%) translateY(0); opacity: 1; }
                to { transform: translateX(-50%) translateY(100px); opacity: 0; }
            }
        `;
        document.head.appendChild(styleSheet);
    }

    document.body.appendChild(notification);

    // Click to dismiss
    notification.onclick = function() {
        notification.style.animation = 'slideDown 0.3s ease-out';
        setTimeout(() => document.body.removeChild(notification), 300);
    };

    // Auto dismiss
    setTimeout(() => {
        if (document.body.contains(notification)) {
            notification.style.animation = 'slideDown 0.3s ease-out';
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
    activeTool: null,
    activeToolEventHandler: null,
    measurementCounter: 0,

    // Debugging helper to check viewer state
    _checkViewer: function() {
        console.log('🔍 Checking viewer state:');
        console.log('   window.viewerGeneral:', window.viewerGeneral);
        console.log('   window.viewerMedical:', window.viewerMedical);
        console.log('   window.viewer:', window.viewer);

        const viewer = window.viewerGeneral || window.viewer;
        if (viewer) {
            console.log('✅ Viewer found!');
            console.log('   - scene:', !!viewer.scene);
            console.log('   - camera:', !!viewer.camera);
            console.log('   - renderer:', !!viewer.renderer);
            console.log('   - initialized:', !!viewer.initialized);
            if (viewer.renderer && viewer.renderer.domElement) {
                console.log('   - canvas size:', viewer.renderer.domElement.width, 'x', viewer.renderer.domElement.height);
            }
        } else {
            console.error('❌ NO VIEWER FOUND!');
        }
        return viewer;
    },

    // Show measurement results panel
    showMeasurementsPanel: function() {
        const panel = document.getElementById('measurementResultsPanel');
        if (panel) {
            panel.classList.add('visible');
        }
    },

    // Hide measurement results panel
    hideMeasurementsPanel: function() {
        const panel = document.getElementById('measurementResultsPanel');
        if (panel) {
            panel.classList.remove('visible');
        }
    },

    // Close measurements panel and clear all
    closeMeasurementsPanel: function() {
        const viewer = window.viewerGeneral || window.viewer;
        if (viewer) {
            this.clearAllMeasurements(viewer);
        }
        this.hideMeasurementsPanel();

        // Close submenu too
        const submenu = document.getElementById('measurementSubmenu');
        if (submenu) {
            submenu.style.display = 'none';
            toggleToolbarButton('measurementToolBtn', false);
        }
    },

    // Add measurement to results panel
    addMeasurementResult: function(type, value, unit, objects) {
        this.measurementCounter++;
        const id = `measurement-${this.measurementCounter}`;

        const resultsList = document.getElementById('measurementResultsList');
        if (!resultsList) return;

        // Remove "no measurements" message if exists
        const noMeasurements = resultsList.querySelector('.no-measurements');
        if (noMeasurements) {
            noMeasurements.remove();
        }

        // Type configurations
        const typeConfig = {
            distance: { icon: '📏', label: 'Distance', color: '#f44336' },
            diameter: { icon: '⭕', label: 'Diameter', color: '#4caf50' },
            area: { icon: '▢', label: 'Area', color: '#fbc02d' },
            'point-to-surface': { icon: '⊥', label: 'Point to Surface', color: '#9c27b0' },
            angle: { icon: '∠', label: 'Angle', color: '#00bcd4' }
        };

        const config = typeConfig[type] || { icon: '📐', label: type, color: '#495057' };

        const item = document.createElement('div');
        item.className = 'measurement-item';
        item.id = id;
        item.dataset.measurementId = id;

        item.innerHTML = `
            <div class="measurement-item-header">
                <div class="measurement-type ${type}">
                    <div class="type-icon">${config.icon}</div>
                    <span>${config.label}</span>
                </div>
                <button class="measurement-delete" onclick="window.toolbarHandler.deleteMeasurement('${id}')">Delete</button>
            </div>
            <div class="measurement-value">
                ${value}<span class="measurement-unit">${unit}</span>
            </div>
        `;

        // Store reference to 3D objects
        item.dataset.objects = JSON.stringify(objects.map(obj => obj.uuid));

        resultsList.appendChild(item);
        this.showMeasurementsPanel();
    },

    // Delete specific measurement
    deleteMeasurement: function(measurementId) {
        const item = document.getElementById(measurementId);
        if (!item) return;

        // Get stored object UUIDs
        const objectUUIDs = JSON.parse(item.dataset.objects || '[]');

        // Remove objects from scene
        const viewer = window.viewerGeneral || window.viewer;
        if (viewer && viewer.scene) {
            objectUUIDs.forEach(uuid => {
                const obj = viewer.scene.getObjectByProperty('uuid', uuid);
                if (obj) {
                    viewer.scene.remove(obj);
                    if (obj.geometry) obj.geometry.dispose();
                    if (obj.material) {
                        if (obj.material.map) obj.material.map.dispose();
                        obj.material.dispose();
                    }
                }
            });
            if (viewer.render) viewer.render();
        }

        // Remove from list
        item.remove();

        // Check if list is empty
        const resultsList = document.getElementById('measurementResultsList');
        if (resultsList && resultsList.children.length === 0) {
            resultsList.innerHTML = `
                <div class="no-measurements">
                    <svg width="40" height="40" viewBox="0 0 48 48" fill="none" style="opacity: 0.3; margin-bottom: 8px;">
                        <path d="M8 40L40 8M12 40L16 36M20 40L24 36M28 40L32 36M36 40L40 36M8 36L12 32M8 28L16 20M8 20L12 16M8 12L16 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <p>No measurements yet</p>
                    <small>Click a measurement tool to start</small>
                </div>
            `;
        }
    },

    // Show active tool status bar
    showActiveToolStatus: function(toolName, instruction, iconPath) {
        const statusBar = document.getElementById('activeToolStatus');
        const nameEl = document.getElementById('activeToolName');
        const instructionEl = document.getElementById('activeToolInstruction');
        const iconEl = document.getElementById('activeToolIcon');

        if (statusBar && nameEl && instructionEl && iconEl) {
            nameEl.textContent = toolName;
            instructionEl.textContent = instruction;
            iconEl.setAttribute('d', iconPath);
            statusBar.classList.add('visible');
            this.activeTool = toolName.toLowerCase().replace(/\s+/g, '-');
        }
    },

    // Hide active tool status bar
    hideActiveToolStatus: function() {
        const statusBar = document.getElementById('activeToolStatus');
        if (statusBar) {
            statusBar.classList.remove('visible');
        }
        this.activeTool = null;
        this.activeToolEventHandler = null;
    },

    // Cancel active tool
    cancelActiveTool: function() {
        console.log('❌ Canceling active tool:', this.activeTool);
        const viewer = window.viewerGeneral || window.viewer;

        if (viewer && viewer.measurementState) {
            // Remove event listener if exists
            if (this.activeToolEventHandler && viewer.renderer) {
                viewer.renderer.domElement.removeEventListener('click', this.activeToolEventHandler);
            }

            // Reset measurement state
            viewer.measurementState.mode = null;
            viewer.measurementState.points = [];
        }

        // Remove active state from submenu buttons
        document.querySelectorAll('.submenu-btn.active').forEach(btn => {
            btn.classList.remove('active');
        });

        this.hideActiveToolStatus();
        showToolbarNotification('Tool canceled', 'info', 1500);
    },

    toggleMeasurement: function(viewerType) {
        console.log(`📏 Toggle measurement for ${viewerType}`);
        this._checkViewer();
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
        // Always use unified viewer
        const viewer = window.viewerGeneral || window.viewer;

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

                // Remove active class from all buttons
                submenu.querySelectorAll('.submenu-btn').forEach(b => b.classList.remove('active'));

                // Add active class to clicked button (except clear)
                if (measureType !== 'clear') {
                    btn.classList.add('active');
                }

                this.handleMeasurementTool(viewer, measureType, viewerType, btn);
            });
        });
    },

    handleMeasurementTool: function(viewer, measureType, viewerType, buttonEl) {
        console.log(`📐 Measurement tool: ${measureType}`);
        const THREE = window.THREE;

        if (!THREE) {
            console.error('❌ THREE.js not loaded!');
            showToolbarNotification('3D library not loaded yet', 'error');
            return;
        }

        // Use the new measurement manager
        if (window.measurementManager) {
            if (measureType === 'clear') {
                // Clear all measurements
                window.measurementManager.clearAllMeasurements(viewer);
                
                // Remove active from all buttons
                if (buttonEl) {
                    const submenu = buttonEl.closest('.measurement-submenu');
                    if (submenu) {
                        submenu.querySelectorAll('.submenu-btn').forEach(b => {
                            b.classList.remove('active');
                            b.style.background = '';
                            b.style.color = '';
                        });
                    }
                }
                
                // Hide active tool status
                this.hideActiveToolStatus();
                
                showToolbarNotification('All measurements cleared', 'success');
            } else {
                // Start new measurement with the selected tool
                window.measurementManager.selectTool(viewer, measureType, viewerType);
                
                // Show active tool status
                const toolInfo = {
                    distance: {
                        name: 'Distance Measurement',
                        instruction: 'Click two points on the model to measure distance',
                        icon: 'M2 2L16 16M2 2m0 0a2 2 0 110 0M16 16m0 0a2 2 0 110 0'
                    },
                    diameter: {
                        name: 'Diameter Measurement',
                        instruction: 'Click two points on opposite sides to measure diameter',
                        icon: 'M9 2a7 7 0 110 14a7 7 0 010-14M2 9L16 9'
                    },
                    area: {
                        name: 'Area Measurement',
                        instruction: 'Click 3+ points to define area. Click first point again to close.',
                        icon: 'M2 2h14v14H2z'
                    },
                    'point-to-surface': {
                        name: 'Point-to-Surface',
                        instruction: 'Click a point, then click the target surface',
                        icon: 'M9 2v10M9 2a2 2 0 110 0M2 12h14v4H2z'
                    },
                    angle: {
                        name: 'Angle Measurement',
                        instruction: 'Click three points: First point → Vertex (middle) → Third point',
                        icon: 'M2 16L9 9L16 16'
                    }
                };
                
                if (toolInfo[measureType]) {
                    this.showActiveToolStatus(
                        toolInfo[measureType].name,
                        toolInfo[measureType].instruction,
                        toolInfo[measureType].icon
                    );
                }
                
                showToolbarNotification(`${measureType.charAt(0).toUpperCase() + measureType.slice(1)} tool activated`, 'info');
            }
        } else {
            console.error('❌ Measurement Manager not found!');
            showToolbarNotification('Measurement system not ready', 'error');
        }
    },

    startDistanceMeasurement: function(viewer, viewerType) {
        const THREE = window.THREE;

        if (!THREE) {
            console.error('❌ THREE.js not loaded!');
            showToolbarNotification('3D library not loaded yet', 'error');
            return;
        }

        viewer.measurementState.mode = 'distance';
        viewer.measurementState.points = [];

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

                    showToolbarNotification(`Distance: ${distance.toFixed(2)} mm`, 'success', 3000);

                    // Add to results panel
                    window.toolbarHandler.addMeasurementResult(
                        'distance',
                        distance.toFixed(2),
                        'mm',
                        [sphere, line]
                    );

                    // Reset for next measurement
                    viewer.measurementState.mode = null;
                    viewer.measurementState.points = [];
                    viewer.renderer.domElement.removeEventListener('click', onMeasurementClick);

                    // Hide active tool status
                    window.toolbarHandler.hideActiveToolStatus();
                }

                if (viewer.render) viewer.render();
            }
        };

        // Store event handler reference
        this.activeToolEventHandler = onMeasurementClick;
        viewer.renderer.domElement.addEventListener('click', onMeasurementClick);
    },

    startDiameterMeasurement: function(viewer, viewerType) {
        const THREE = window.THREE;

        if (!THREE) {
            console.error('❌ THREE.js not loaded!');
            showToolbarNotification('3D library not loaded yet', 'error');
            return;
        }

        viewer.measurementState.mode = 'diameter';
        viewer.measurementState.points = [];

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
                const material = new THREE.MeshBasicMaterial({ color: 0x00ff00 });
                const sphere = new THREE.Mesh(geometry, material);
                sphere.position.copy(point);
                sphere.userData.isMeasurementPoint = true;
                viewer.scene.add(sphere);
                viewer.measurementState.lines.push(sphere);

                if (viewer.measurementState.points.length === 2) {
                    // Draw line between points (diameter)
                    const lineGeometry = new THREE.BufferGeometry().setFromPoints(viewer.measurementState.points);
                    const lineMaterial = new THREE.LineBasicMaterial({ color: 0x00ff00, linewidth: 2 });
                    const line = new THREE.Line(lineGeometry, lineMaterial);
                    line.userData.isMeasurementLine = true;
                    viewer.scene.add(line);
                    viewer.measurementState.lines.push(line);

                    // Calculate diameter and radius
                    const diameter = viewer.measurementState.points[0].distanceTo(viewer.measurementState.points[1]);
                    const radius = diameter / 2;

                    showToolbarNotification(`Diameter: ${diameter.toFixed(2)} mm, Radius: ${radius.toFixed(2)} mm`, 'success', 3000);

                    // Add to results panel
                    window.toolbarHandler.addMeasurementResult(
                        'diameter',
                        `Ø ${diameter.toFixed(2)} (R ${radius.toFixed(2)})`,
                        'mm',
                        [sphere, line]
                    );

                    // Reset
                    viewer.measurementState.mode = null;
                    viewer.measurementState.points = [];
                    viewer.renderer.domElement.removeEventListener('click', onMeasurementClick);

                    // Hide active tool status
                    window.toolbarHandler.hideActiveToolStatus();
                }

                if (viewer.render) viewer.render();
            }
        };

        // Store event handler reference
        this.activeToolEventHandler = onMeasurementClick;
        viewer.renderer.domElement.addEventListener('click', onMeasurementClick);
    },

    startAreaMeasurement: function(viewer, viewerType) {
        const THREE = window.THREE;

        if (!THREE) {
            console.error('❌ THREE.js not loaded!');
            showToolbarNotification('3D library not loaded yet', 'error');
            return;
        }

        viewer.measurementState.mode = 'area';
        viewer.measurementState.points = [];

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

                // Check if clicking near first point to close polygon
                if (viewer.measurementState.points.length >= 3) {
                    const distToFirst = point.distanceTo(viewer.measurementState.points[0]);
                    if (distToFirst < 2.0) {
                        // Close polygon and calculate area
                        const points = viewer.measurementState.points;

                        // Draw closing line
                        const lineGeometry = new THREE.BufferGeometry().setFromPoints([points[points.length - 1], points[0]]);
                        const lineMaterial = new THREE.LineBasicMaterial({ color: 0xffff00, linewidth: 2 });
                        const line = new THREE.Line(lineGeometry, lineMaterial);
                        line.userData.isMeasurementLine = true;
                        viewer.scene.add(line);
                        viewer.measurementState.lines.push(line);

                        // Calculate area using triangulation
                        let area = 0;
                        for (let i = 1; i < points.length - 1; i++) {
                            const v1 = new THREE.Vector3().subVectors(points[i], points[0]);
                            const v2 = new THREE.Vector3().subVectors(points[i + 1], points[0]);
                            const cross = new THREE.Vector3().crossVectors(v1, v2);
                            area += cross.length() / 2;
                        }

                        showToolbarNotification(`Area: ${area.toFixed(2)} mm²`, 'success', 3000);

                        // Collect all objects for deletion
                        const allObjects = [...viewer.measurementState.lines];

                        // Add to results panel
                        window.toolbarHandler.addMeasurementResult(
                            'area',
                            area.toFixed(2),
                            'mm²',
                            allObjects
                        );

                        // Reset
                        viewer.measurementState.mode = null;
                        viewer.measurementState.points = [];
                        viewer.renderer.domElement.removeEventListener('click', onMeasurementClick);

                        // Hide active tool status
                        window.toolbarHandler.hideActiveToolStatus();

                        if (viewer.render) viewer.render();
                        return;
                    }
                }

                viewer.measurementState.points.push(point);

                // Create point marker
                const geometry = new THREE.SphereGeometry(0.5, 16, 16);
                const material = new THREE.MeshBasicMaterial({ color: 0xffff00 });
                const sphere = new THREE.Mesh(geometry, material);
                sphere.position.copy(point);
                sphere.userData.isMeasurementPoint = true;
                viewer.scene.add(sphere);
                viewer.measurementState.lines.push(sphere);

                // Draw line to previous point
                if (viewer.measurementState.points.length > 1) {
                    const prevPoint = viewer.measurementState.points[viewer.measurementState.points.length - 2];
                    const lineGeometry = new THREE.BufferGeometry().setFromPoints([prevPoint, point]);
                    const lineMaterial = new THREE.LineBasicMaterial({ color: 0xffff00, linewidth: 2 });
                    const line = new THREE.Line(lineGeometry, lineMaterial);
                    line.userData.isMeasurementLine = true;
                    viewer.scene.add(line);
                    viewer.measurementState.lines.push(line);
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
        const THREE = window.THREE;

        if (!THREE) {
            console.error('❌ THREE.js not loaded!');
            showToolbarNotification('3D library not loaded yet', 'error');
            return;
        }

        viewer.measurementState.mode = 'pointToSurface';
        viewer.measurementState.points = [];

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
                const normal = intersects[0].face ? intersects[0].face.normal.clone() : new THREE.Vector3(0, 1, 0);

                viewer.measurementState.points.push({point, normal});

                // Create point marker
                const geometry = new THREE.SphereGeometry(0.5, 16, 16);
                const material = new THREE.MeshBasicMaterial({ color: 0xff00ff });
                const sphere = new THREE.Mesh(geometry, material);
                sphere.position.copy(point);
                sphere.userData.isMeasurementPoint = true;
                viewer.scene.add(sphere);
                viewer.measurementState.lines.push(sphere);

                if (viewer.measurementState.points.length === 2) {
                    const p1 = viewer.measurementState.points[0].point;
                    const p2 = viewer.measurementState.points[1].point;
                    const surfaceNormal = viewer.measurementState.points[1].normal;

                    // Calculate perpendicular distance from p1 to plane at p2
                    const vectorToPoint = new THREE.Vector3().subVectors(p1, p2);
                    const distance = Math.abs(vectorToPoint.dot(surfaceNormal));

                    // Find perpendicular point on surface
                    const perpPoint = new THREE.Vector3().copy(p1).addScaledVector(surfaceNormal, -vectorToPoint.dot(surfaceNormal));

                    // Draw perpendicular line
                    const lineGeometry = new THREE.BufferGeometry().setFromPoints([p1, perpPoint]);
                    const lineMaterial = new THREE.LineBasicMaterial({
                        color: 0xff00ff,
                        linewidth: 2,
                        dashSize: 1,
                        gapSize: 0.5
                    });
                    const line = new THREE.Line(lineGeometry, lineMaterial);
                    line.userData.isMeasurementLine = true;
                    viewer.scene.add(line);
                    viewer.measurementState.lines.push(line);

                    showToolbarNotification(`Perpendicular distance: ${distance.toFixed(2)} mm`, 'success', 3000);

                    // Collect all sphere objects
                    const allSpheres = viewer.measurementState.lines.filter(obj => obj.userData.isMeasurementPoint);

                    // Add to results panel
                    window.toolbarHandler.addMeasurementResult(
                        'point-to-surface',
                        distance.toFixed(2),
                        'mm',
                        [line, ...allSpheres]
                    );

                    // Reset
                    viewer.measurementState.mode = null;
                    viewer.measurementState.points = [];
                    viewer.renderer.domElement.removeEventListener('click', onMeasurementClick);

                    // Hide active tool status
                    window.toolbarHandler.hideActiveToolStatus();
                }

                if (viewer.render) viewer.render();
            }
        };

        // Store event handler reference
        this.activeToolEventHandler = onMeasurementClick;
        viewer.renderer.domElement.addEventListener('click', onMeasurementClick);
    },

    startAngleMeasurement: function(viewer, viewerType) {
        const THREE = window.THREE;

        if (!THREE) {
            console.error('❌ THREE.js not loaded!');
            showToolbarNotification('3D library not loaded yet', 'error');
            return;
        }

        viewer.measurementState.mode = 'angle';
        viewer.measurementState.points = [];

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
                const geometry = new THREE.SphereGeometry(0.8, 16, 16);
                const material = new THREE.MeshBasicMaterial({
                    color: viewer.measurementState.points.length === 2 ? 0xff0000 : 0x00ffff
                });
                const sphere = new THREE.Mesh(geometry, material);
                sphere.position.copy(point);
                sphere.userData.isMeasurementPoint = true;
                viewer.scene.add(sphere);
                viewer.measurementState.lines.push(sphere);

                // Draw line to previous point
                if (viewer.measurementState.points.length > 1) {
                    const prevPoint = viewer.measurementState.points[viewer.measurementState.points.length - 2];
                    const lineGeometry = new THREE.BufferGeometry().setFromPoints([prevPoint, point]);
                    const lineMaterial = new THREE.LineBasicMaterial({
                        color: 0x00ffff,
                        linewidth: 3,
                        transparent: false,
                        opacity: 1.0
                    });
                    const line = new THREE.Line(lineGeometry, lineMaterial);
                    line.userData.isMeasurementLine = true;
                    viewer.scene.add(line);
                    viewer.measurementState.lines.push(line);
                }

                if (viewer.measurementState.points.length === 3) {
                    // Calculate angle: points[1] is the vertex
                    const p1 = viewer.measurementState.points[0];
                    const vertex = viewer.measurementState.points[1];
                    const p2 = viewer.measurementState.points[2];

                    const v1 = new THREE.Vector3().subVectors(p1, vertex).normalize();
                    const v2 = new THREE.Vector3().subVectors(p2, vertex).normalize();

                    const angleRad = v1.angleTo(v2);
                    const angleDeg = THREE.MathUtils.radToDeg(angleRad);

                    showToolbarNotification(`Angle: ${angleDeg.toFixed(2)}° (${angleRad.toFixed(3)} rad)`, 'success', 3000);

                    // Collect all objects (make a copy of the array)
                    const allObjects = [...viewer.measurementState.lines];

                    // Add to results panel
                    window.toolbarHandler.addMeasurementResult(
                        'angle',
                        angleDeg.toFixed(2),
                        '°',
                        allObjects
                    );

                    // Reset
                    viewer.measurementState.mode = null;
                    viewer.measurementState.points = [];
                    viewer.renderer.domElement.removeEventListener('click', onMeasurementClick);

                    // Hide active tool status
                    window.toolbarHandler.hideActiveToolStatus();
                }

                if (viewer.render) viewer.render();
            }
        };

        // Store event handler reference
        this.activeToolEventHandler = onMeasurementClick;
        viewer.renderer.domElement.addEventListener('click', onMeasurementClick);
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
            if (obj.material) {
                if (obj.material.map) obj.material.map.dispose();
                obj.material.dispose();
            }
        });

        // Reset measurement state
        if (viewer.measurementState) {
            viewer.measurementState.points = [];
            viewer.measurementState.lines = [];
            viewer.measurementState.labels = [];
            viewer.measurementState.mode = null;
        }

        // Clear results panel
        const resultsList = document.getElementById('measurementResultsList');
        if (resultsList) {
            resultsList.innerHTML = `
                <div class="no-measurements">
                    <svg width="40" height="40" viewBox="0 0 48 48" fill="none" style="opacity: 0.3; margin-bottom: 8px;">
                        <path d="M8 40L40 8M12 40L16 36M20 40L24 36M28 40L32 36M36 40L40 36M8 36L12 32M8 28L16 20M8 20L12 16M8 12L16 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <p>No measurements yet</p>
                    <small>Click a measurement tool to start</small>
                </div>
            `;
        }

        // Remove active state from buttons
        document.querySelectorAll('.submenu-btn.active').forEach(btn => {
            btn.classList.remove('active');
        });

        if (viewer.render) viewer.render();
        showToolbarNotification('All measurements cleared', 'success', 1500);
    },

    toggleBoundingBox: function(viewerType) {
        console.log(`📦 Toggle bounding box for ${viewerType}`);
        const THREE = window.THREE;
        const viewer = window.viewerGeneral || window.viewer;

        if (!viewer || !viewer.scene) {
            showToolbarNotification('Please wait for the 3D model to load', 'warning');
            return;
        }

        if (!THREE) {
            console.error('❌ THREE.js not loaded!');
            showToolbarNotification('3D library not loaded yet', 'error');
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
        const THREE = window.THREE;
        const viewer = window.viewerGeneral || window.viewer;

        if (!viewer || !viewer.scene) {
            showToolbarNotification('Please wait for the 3D model to load', 'warning');
            return;
        }

        if (!THREE) {
            console.error('❌ THREE.js not loaded!');
            showToolbarNotification('3D library not loaded yet', 'error');
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
        const THREE = window.THREE;
        const viewer = window.viewerGeneral || window.viewer;

        if (!viewer || !viewer.scene) {
            showToolbarNotification('Please wait for the 3D model to load', 'warning');
            return;
        }

        if (!THREE) {
            console.error('❌ THREE.js not loaded!');
            showToolbarNotification('3D library not loaded yet', 'error');
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
        const viewer = window.viewerGeneral || window.viewer;

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
        const viewer = window.viewerGeneral || window.viewer;

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
        console.log('🔍 DEBUG: Checking viewer state...');
        console.log('   window.viewerGeneral:', window.viewerGeneral);
        console.log('   window.viewer:', window.viewer);

        const viewer = window.viewerGeneral || window.viewer;
        console.log('   Selected viewer:', viewer);

        if (!viewer) {
            console.error('❌ No viewer found!');
            showToolbarNotification('Viewer loading, please wait...', 'info');
            return;
        }

        console.log('   viewer.renderer:', viewer.renderer);
        console.log('   viewer.scene:', viewer.scene);
        console.log('   viewer.camera:', viewer.camera);

        if (!viewer.renderer || !viewer.scene || !viewer.camera) {
            console.error('❌ Viewer not fully initialized!');
            showToolbarNotification('Renderer loading, please wait...', 'warning');
            return;
        }

        try {
            // Get the canvas element
            const canvas = viewer.renderer.domElement;
            console.log('   canvas:', canvas);
            console.log('   canvas.width:', canvas.width);
            console.log('   canvas.height:', canvas.height);

            if (!canvas || canvas.width === 0 || canvas.height === 0) {
                throw new Error('Canvas has invalid dimensions: ' + canvas.width + 'x' + canvas.height);
            }

            // Force a fresh render before capturing
            viewer.renderer.render(viewer.scene, viewer.camera);

            // Create a new canvas with white background
            const screenshotCanvas = document.createElement('canvas');
            screenshotCanvas.width = canvas.width;
            screenshotCanvas.height = canvas.height;
            const ctx = screenshotCanvas.getContext('2d');

            // Fill with white background
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, screenshotCanvas.width, screenshotCanvas.height);

            // Draw the 3D viewer canvas on top
            ctx.drawImage(canvas, 0, 0);

            // Convert to data URL
            const dataURL = screenshotCanvas.toDataURL('image/png', 1.0);

            // Generate filename with timestamp
            const timestamp = new Date().toISOString().replace(/[:.]/g, '-').substring(0, 19);
            const filename = `3d-model-${viewerType.toLowerCase()}-${timestamp}.png`;

            // Create download link
            const link = document.createElement('a');
            link.href = dataURL;
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            // Show success message
            showToolbarNotification('Screenshot saved successfully! ✓', 'success', 2000);

            // Visual feedback on button
            const screenshotBtn = document.getElementById('screenshotToolBtn');
            if (screenshotBtn) {
                screenshotBtn.classList.add('active');
                setTimeout(() => screenshotBtn.classList.remove('active'), 500);
            }

            console.log('✅ Screenshot captured:', filename);

        } catch (error) {
            console.error('❌ Screenshot failed:', error);
            console.error('Error stack:', error.stack);
            showToolbarNotification('Screenshot failed: ' + error.message, 'error', 3000);
        }
    },

    undo: function() {
        console.log('⏪ Undo action');
        const viewer = window.viewerGeneral || window.viewer;

        if (!viewer || !viewer.stateHistory) {
            viewer.stateHistory = [];
            viewer.stateHistoryIndex = -1;
        }

        if (viewer.stateHistoryIndex > 0) {
            viewer.stateHistoryIndex--;
            const state = viewer.stateHistory[viewer.stateHistoryIndex];
            this.restoreState(viewer, state);
            showToolbarNotification('Undone', 'success', 1000);
            
            // Update button states
            if (typeof updateUndoRedoButtons === 'function') {
                updateUndoRedoButtons();
            }
        } else {
            showToolbarNotification('Nothing to undo', 'info', 1500);
        }
    },

    redo: function() {
        console.log('⏩ Redo action');
        const viewer = window.viewerGeneral || window.viewer;

        if (!viewer || !viewer.stateHistory) {
            viewer.stateHistory = [];
            viewer.stateHistoryIndex = -1;
        }

        if (viewer.stateHistoryIndex < viewer.stateHistory.length - 1) {
            viewer.stateHistoryIndex++;
            const state = viewer.stateHistory[viewer.stateHistoryIndex];
            this.restoreState(viewer, state);
            showToolbarNotification('Redone', 'success', 1000);
            
            // Update button states
            if (typeof updateUndoRedoButtons === 'function') {
                updateUndoRedoButtons();
            }
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
            backgroundColor: viewer.scene.background ? viewer.scene.background.getHex() : 0xffffff,
            modelColors: [],
            toolsVisible: {
                boundingBox: !!viewer.scene.children.find(child => child.userData && child.userData.isBoundingBoxHelper && child.visible),
                axis: !!viewer.scene.children.find(child => child.userData && child.userData.isAxisHelper && child.visible),
                grid: !!viewer.scene.children.find(child => child.userData && child.userData.isGridHelper && child.visible)
            }
        };

        // Capture model colors
        viewer.scene.traverse((object) => {
            if (object.isMesh && object.material) {
                state.modelColors.push({
                    uuid: object.uuid,
                    color: object.material.color.getHex()
                });
            }
        });

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

        // Update button states
        if (typeof updateUndoRedoButtons === 'function') {
            updateUndoRedoButtons();
        }

        console.log(`💾 State saved (${viewer.stateHistoryIndex + 1}/${viewer.stateHistory.length})`);
    },

    restoreState: function(viewer, state) {
        if (!state) return;

        const THREE = window.THREE;

        // Restore camera
        viewer.camera.position.copy(state.cameraPosition);
        viewer.camera.rotation.copy(state.cameraRotation);

        // Restore transparency
        viewer.currentTransparencyIndex = state.transparency;

        // Restore shadows
        viewer.renderer.shadowMap.enabled = state.shadows;

        // Restore background color
        if (state.backgroundColor !== undefined && THREE) {
            viewer.scene.background = new THREE.Color(state.backgroundColor);
        }

        // Restore model colors
        if (state.modelColors && state.modelColors.length > 0) {
            viewer.scene.traverse((object) => {
                if (object.isMesh && object.material) {
                    const savedColor = state.modelColors.find(c => c.uuid === object.uuid);
                    if (savedColor && THREE) {
                        object.material.color.setHex(savedColor.color);
                        object.material.needsUpdate = true;
                    }
                }
            });
        }

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
        const viewer = window.viewerGeneral || window.viewer;

        if (!viewer || !viewer.scene) {
            showToolbarNotification('Please wait for the 3D model to load', 'warning');
            return;
        }

        // Extended color palette with more options
        const colors = [
            '#0047AD', '#ffffff', '#2c3e50', '#3498db', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6', '#1abc9c', '#34495e',
            '#e67e22', '#95a5a6', '#16a085', '#27ae60', '#2980b9', '#8e44ad', '#c0392b', '#d35400', '#7f8c8d', '#bdc3c7',
            '#f1c40f', '#e74c3c', '#ecf0f1', '#34495e', '#ff6b6b', '#4ecdc4', '#45b7d1', '#ffa07a', '#98d8c8', '#6c5ce7'
        ];

        const existingPicker = document.getElementById('modelColorPicker');
        if (existingPicker) {
            existingPicker.remove();
            return;
        }

        // Get button position
        const colorBtn = document.getElementById('modelColorBtn');
        const btnRect = colorBtn ? colorBtn.getBoundingClientRect() : { left: 100, top: 60 };

        const picker = document.createElement('div');
        picker.id = 'modelColorPicker';
        picker.style.cssText = `position: fixed; top: ${btnRect.bottom + 10}px; left: ${btnRect.left}px; background: white; padding: 15px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); z-index: 99999; max-width: 320px;`;
        picker.innerHTML = `
            <div style="font-weight: 600; margin-bottom: 12px; color: #2c3e50; font-size: 0.9rem;">Select Model Color</div>
            <div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 8px; margin-bottom: 12px;">
                ${colors.map(color => `
                    <button class="model-color-option" data-color="${color}"
                            style="width: 40px; height: 40px; border: 2px solid ${color === '#ffffff' ? '#ddd' : color}; border-radius: 8px; background: ${color}; cursor: pointer; transition: all 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
                            onmouseover="this.style.transform='scale(1.15)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.2)'"
                            onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.1)'">
                    </button>
                `).join('')}
            </div>
            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #495057; margin-bottom: 8px;">Custom Color:</label>
                <div style="display: flex; gap: 8px; align-items: center;">
                    <input type="color" id="customModelColor" value="#808080"
                           style="width: 50px; height: 40px; border: 2px solid #ddd; border-radius: 8px; cursor: pointer;">
                    <button id="applyCustomModelColor"
                            style="flex: 1; padding: 10px; border: none; background: #0047AD; color: white; border-radius: 8px; cursor: pointer; font-size: 0.85rem; font-weight: 600; transition: background 0.2s;"
                            onmouseover="this.style.background='#003580'" onmouseout="this.style.background='#0047AD'">
                        Apply Custom
                    </button>
                </div>
            </div>
            <button onclick="document.getElementById('modelColorPicker').remove()"
                    style="width: 100%; padding: 10px; border: none; background: #f1f3f5; border-radius: 8px; cursor: pointer; font-size: 0.85rem; font-weight: 600; transition: background 0.2s;"
                    onmouseover="this.style.background='#e9ecef'" onmouseout="this.style.background='#f1f3f5'">
                Close
            </button>
        `;
        document.body.appendChild(picker);

        // Function to apply color
        const applyColor = (color) => {
            // Save state before making changes
            window.toolbarHandler.saveState(viewer);
            
            viewer.scene.traverse((object) => {
                if (object.isMesh && object.material) {
                    object.material.color.set(color);
                    object.material.needsUpdate = true;
                }
            });
            if (viewer.render) viewer.render();
            showToolbarNotification('Model color changed', 'success', 1500);
        };

        // Add click handlers for preset colors
        picker.querySelectorAll('.model-color-option').forEach(btn => {
            btn.addEventListener('click', function() {
                const color = this.getAttribute('data-color');
                applyColor(color);
                picker.remove();
            });
        });

        // Add handler for custom color
        const applyCustomBtn = picker.querySelector('#applyCustomModelColor');
        applyCustomBtn.addEventListener('click', function() {
            const customColor = picker.querySelector('#customModelColor').value;
            applyColor(customColor);
            picker.remove();
        });

        // Close when clicking outside
        setTimeout(() => {
            document.addEventListener('click', function closeColorPicker(e) {
                if (!picker.contains(e.target) && e.target !== colorBtn) {
                    picker.remove();
                    document.removeEventListener('click', closeColorPicker);
                }
            });
        }, 100);
    },

    changeBackgroundColor: function(viewerType) {
        console.log('🌈 Change background color');
        const THREE = window.THREE;
        const viewer = window.viewerGeneral || window.viewer;

        if (!viewer || !viewer.scene) {
            showToolbarNotification('Please wait for the 3D viewer to load', 'warning');
            return;
        }

        if (!THREE) {
            console.error('❌ THREE.js not loaded!');
            showToolbarNotification('3D library not loaded yet', 'error');
            return;
        }

        // Extended background color palette with gradients and solid colors
        const colors = [
            '#ffffff', '#f8f9fa', '#e9ecef', '#dee2e6', '#2c3e50', '#34495e', '#1a1a1a', '#000000', '#e3f2fd', '#fce4ec',
            '#f3e5f5', '#e8eaf6', '#e0f2f1', '#fff3e0', '#fbe9e7', '#eceff1', '#f1f8e9', '#fff9c4', '#b2dfdb', '#c5cae9',
            '#d1c4e9', '#f8bbd0', '#ffccbc', '#ffe0b2', '#c8e6c9', '#b3e5fc', '#dcedc8', '#e1bee7', '#ffecb3', '#d7ccc8'
        ];

        const existingPicker = document.getElementById('bgColorPicker');
        if (existingPicker) {
            existingPicker.remove();
            return;
        }

        // Get button position - FIXED: use correct button ID
        const bgColorBtn = document.getElementById('backgroundColorBtn');
        if (!bgColorBtn) {
            console.error('❌ Background color button not found!');
            showToolbarNotification('Button not found', 'error');
            return;
        }
        const btnRect = bgColorBtn.getBoundingClientRect();
        console.log('🎨 Button position:', btnRect);

        const picker = document.createElement('div');
        picker.id = 'bgColorPicker';
        picker.style.cssText = `position: fixed; top: ${btnRect.bottom + 10}px; left: ${btnRect.left}px; background: white; padding: 15px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); z-index: 99999; max-width: 320px;`;
        picker.innerHTML = `
            <div style="font-weight: 600; margin-bottom: 12px; color: #2c3e50; font-size: 0.9rem;">Select Background Color</div>
            <div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 8px; margin-bottom: 12px;">
                ${colors.map(color => `
                    <button class="bg-color-option" data-color="${color}"
                            style="width: 40px; height: 40px; border: 2px solid ${color === '#ffffff' ? '#ddd' : color}; border-radius: 8px; background: ${color}; cursor: pointer; transition: all 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
                            onmouseover="this.style.transform='scale(1.15)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.2)'"
                            onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.1)'">
                    </button>
                `).join('')}
            </div>
            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #495057; margin-bottom: 8px;">Custom Color:</label>
                <div style="display: flex; gap: 8px; align-items: center;">
                    <input type="color" id="customBgColor" value="#ffffff"
                           style="width: 50px; height: 40px; border: 2px solid #ddd; border-radius: 8px; cursor: pointer;">
                    <button id="applyCustomBgColor"
                            style="flex: 1; padding: 10px; border: none; background: #0047AD; color: white; border-radius: 8px; cursor: pointer; font-size: 0.85rem; font-weight: 600; transition: background 0.2s;"
                            onmouseover="this.style.background='#003580'" onmouseout="this.style.background='#0047AD'">
                        Apply Custom
                    </button>
                </div>
            </div>
            <button onclick="document.getElementById('bgColorPicker').remove()"
                    style="width: 100%; padding: 10px; border: none; background: #f1f3f5; border-radius: 8px; cursor: pointer; font-size: 0.85rem; font-weight: 600; transition: background 0.2s;"
                    onmouseover="this.style.background='#e9ecef'" onmouseout="this.style.background='#f1f3f5'">
                Close
            </button>
        `;
        document.body.appendChild(picker);

        // Function to apply background color
        const applyBgColor = (color) => {
            // Save state before making changes
            window.toolbarHandler.saveState(viewer);
            
            viewer.scene.background = new THREE.Color(color);
            if (viewer.render) viewer.render();
            showToolbarNotification('Background color changed', 'success', 1500);
        };

        // Add click handlers for preset colors
        picker.querySelectorAll('.bg-color-option').forEach(btn => {
            btn.addEventListener('click', function() {
                const color = this.getAttribute('data-color');
                applyBgColor(color);
                picker.remove();
            });
        });

        // Add handler for custom color
        const applyCustomBtn = picker.querySelector('#applyCustomBgColor');
        applyCustomBtn.addEventListener('click', function() {
            const customColor = picker.querySelector('#customBgColor').value;
            applyBgColor(customColor);
            picker.remove();
        });

        // Close when clicking outside
        setTimeout(() => {
            document.addEventListener('click', function closeBgColorPicker(e) {
                if (!picker.contains(e.target) && e.target !== bgColorBtn) {
                    picker.remove();
                    document.removeEventListener('click', closeBgColorPicker);
                }
            });
        }, 100);
    },

    // ============================================
    // MOVE/PAN TOOL - Drag models to reposition
    // ============================================
    toggleMoveMode: function(viewerType) {
        console.log(`🖐️ Toggle Move Mode for ${viewerType}`);

        // Use unified viewer
        const viewer = window.viewerGeneral || window.viewer;
        if (!viewer || !viewer.uploadedFiles || viewer.uploadedFiles.length === 0) {
            console.warn('⚠️ Cannot enable move mode: No file uploaded');
            showToolbarNotification('Please upload a 3D model first', 'warning', 2000);
            return;
        }

        // Toggle pan mode state
        window.panMode = !window.panMode;
        const panToolBtn = document.getElementById('panToolBtn');

        if (panToolBtn) {
            if (window.panMode) {
                panToolBtn.classList.add('active');
            } else {
                panToolBtn.classList.remove('active');
            }
        }

        console.log('👋 Pan mode:', window.panMode ? 'ENABLED ✅' : 'DISABLED ❌');
        console.log('   Viewer:', viewer ? 'Available ✅' : 'Not available ❌');
        console.log('   Files loaded:', viewer.uploadedFiles.length);

        // Attach/detach handlers directly to canvas
        if (window.panMode && viewer.renderer && viewer.renderer.domElement) {
            const canvas = viewer.renderer.domElement;
            console.log('🎯 Attaching pan handlers directly to canvas');

            // Store handlers for later removal
            if (!window.canvasPanHandlers) {
                window.canvasPanHandlers = {
                    pointerdown: (e) => this.handleCanvasMouseDown(e, viewer),
                    pointermove: (e) => this.handleCanvasMouseMove(e, viewer),
                    pointerup: (e) => this.handleCanvasMouseUp(e, viewer)
                };
            }

            // Use POINTER events (modern browsers)
            canvas.addEventListener('pointerdown', window.canvasPanHandlers.pointerdown);
            document.addEventListener('pointermove', window.canvasPanHandlers.pointermove);
            document.addEventListener('pointerup', window.canvasPanHandlers.pointerup);

            canvas.style.cursor = 'grab';
            console.log('✅ Canvas pan handlers attached');
            showToolbarNotification('Move mode enabled - Drag models to reposition', 'success', 2000);
        } else if (!window.panMode && window.canvasPanHandlers && viewer.renderer) {
            const canvas = viewer.renderer.domElement;
            console.log('🔌 Removing pan handlers from canvas');

            // Remove handlers
            canvas.removeEventListener('pointerdown', window.canvasPanHandlers.pointerdown);
            document.removeEventListener('pointermove', window.canvasPanHandlers.pointermove);
            document.removeEventListener('pointerup', window.canvasPanHandlers.pointerup);

            canvas.style.cursor = 'default';
            console.log('✅ Canvas pan handlers removed');
            showToolbarNotification('Move mode disabled', 'info', 1500);
        }

        // When pan mode is active, disable rotation but keep zoom
        if (viewer.controls) {
            viewer.controls.enableRotate = !window.panMode;
            viewer.controls.enablePan = false; // Always disable OrbitControls pan
            viewer.controls.enableZoom = true; // Keep zoom enabled always
        }
    },

    // Canvas-specific pan handlers
    handleCanvasMouseDown: function(e, viewer) {
        console.log('🖱️ CANVAS MOUSEDOWN - Pan drag starting');

        if (!viewer || !window.THREE) return;

        const canvas = viewer.renderer.domElement;
        const rect = canvas.getBoundingClientRect();
        const mouse = new window.THREE.Vector2(
            ((e.clientX - rect.left) / rect.width) * 2 - 1,
            -((e.clientY - rect.top) / rect.height) * 2 + 1
        );

        const raycaster = new window.THREE.Raycaster();
        raycaster.setFromCamera(mouse, viewer.camera);

        // Find all model meshes
        const meshes = [];
        if (viewer.uploadedFiles && viewer.uploadedFiles.length > 0) {
            viewer.uploadedFiles.forEach(fileData => {
                if (fileData.mesh) {
                    meshes.push(fileData.mesh);
                }
            });
        }

        console.log('   Meshes found for raycasting:', meshes.length);

        const intersects = raycaster.intersectObjects(meshes, true);
        console.log('   Intersections found:', intersects.length);

        if (intersects.length > 0) {
            let clickedObject = intersects[0].object;
            window.selectedModel = null;

            // Find which fileData mesh was clicked
            for (const fileData of viewer.uploadedFiles) {
                if (fileData.mesh === clickedObject) {
                    window.selectedModel = fileData.mesh;
                    break;
                }

                // Check if clickedObject is a descendant
                let parent = clickedObject.parent;
                while (parent) {
                    if (parent === fileData.mesh) {
                        window.selectedModel = fileData.mesh;
                        break;
                    }
                    if (parent.name === 'modelGroup' || parent.type === 'Group' && parent.parent?.type === 'Scene') {
                        break;
                    }
                    parent = parent.parent;
                }
                if (window.selectedModel) break;
            }

            if (!window.selectedModel) {
                console.log('   ❌ Could not find matching mesh');
                return;
            }

            window.isPanning = true;
            canvas.style.cursor = 'grabbing';

            // Highlight the selected model
            if (window.selectedModel.material) {
                window.originalMaterialEmissive = window.selectedModel.material.emissive
                    ? window.selectedModel.material.emissive.getHex()
                    : 0x000000;
                window.selectedModel.material.emissive = new window.THREE.Color(0x4488ff);
                window.selectedModel.material.emissiveIntensity = 0.3;
            }

            // Create drag plane
            const normal = new window.THREE.Vector3(0, 0, 1);
            normal.applyQuaternion(viewer.camera.quaternion);
            window.dragPlane = new window.THREE.Plane();
            window.dragPlane.setFromNormalAndCoplanarPoint(normal, window.selectedModel.position);

            // Calculate offset
            const intersectionPoint = new window.THREE.Vector3();
            raycaster.ray.intersectPlane(window.dragPlane, intersectionPoint);
            window.dragOffset = new window.THREE.Vector3();
            window.dragOffset.subVectors(window.selectedModel.position, intersectionPoint);

            console.log('👆 Drag started - Moving model:', window.selectedModel.name || 'unnamed');

            // Disable orbit controls while dragging
            if (viewer.controls) {
                viewer.controls.enabled = false;
            }
        }
    },

    handleCanvasMouseMove: function(e, viewer) {
        if (!window.isPanning || !window.selectedModel || !viewer || !viewer.camera) return;
        if (!window.THREE) return;

        const canvas = viewer.renderer.domElement;
        const rect = canvas.getBoundingClientRect();
        const mouse = new window.THREE.Vector2(
            ((e.clientX - rect.left) / rect.width) * 2 - 1,
            -((e.clientY - rect.top) / rect.height) * 2 + 1
        );

        const raycaster = new window.THREE.Raycaster();
        raycaster.setFromCamera(mouse, viewer.camera);

        // Find intersection with drag plane
        const intersectionPoint = new window.THREE.Vector3();
        if (raycaster.ray.intersectPlane(window.dragPlane, intersectionPoint)) {
            // Move model to new position
            window.selectedModel.position.copy(intersectionPoint).add(window.dragOffset);
        }

        e.preventDefault();
    },

    handleCanvasMouseUp: function(e, viewer) {
        if (window.isPanning && window.THREE) {
            // Remove highlight from selected model
            if (window.selectedModel && window.selectedModel.material) {
                window.selectedModel.material.emissive = new window.THREE.Color(window.originalMaterialEmissive || 0x000000);
                window.selectedModel.material.emissiveIntensity = 0;
            }

            window.isPanning = false;
            window.selectedModel = null;
            window.dragPlane = null;
            window.originalMaterialEmissive = null;

            console.log('✋ Drag ended - Model repositioned');

            if (viewer && viewer.renderer) {
                viewer.renderer.domElement.style.cursor = window.panMode ? 'grab' : 'default';
            }

            // Re-enable orbit controls
            if (viewer && viewer.controls) {
                viewer.controls.enabled = true;
            }
        }
    },

    // ============================================
    // AUTO-ROTATE TOGGLE
    // ============================================
    toggleAutoRotate: function(viewerType) {
        console.log(`🔄 Toggle Auto-Rotate for ${viewerType}`);

        // Use unified viewer
        const viewer = window.viewerGeneral || window.viewer;
        if (!viewer || !viewer.uploadedFiles || viewer.uploadedFiles.length === 0) {
            console.warn('⚠️ Cannot toggle auto-rotate: No file uploaded');
            showToolbarNotification('Please upload a 3D model first', 'warning', 2000);
            return;
        }

        // Toggle auto-rotate state
        if (!window.autoRotateEnabled) {
            window.autoRotateEnabled = {};
        }

        const isCurrentlyEnabled = window.autoRotateEnabled[viewerType] || false;
        window.autoRotateEnabled[viewerType] = !isCurrentlyEnabled;

        const autoRotateBtn = document.getElementById('autoRotateBtn');

        if (autoRotateBtn) {
            if (window.autoRotateEnabled[viewerType]) {
                autoRotateBtn.classList.add('active');
            } else {
                autoRotateBtn.classList.remove('active');
            }
        }

        // Enable/disable auto-rotation in controls
        if (viewer.controls) {
            viewer.controls.autoRotate = window.autoRotateEnabled[viewerType];
            viewer.controls.autoRotateSpeed = 2.0;
            console.log('🔄 Auto-rotate:', window.autoRotateEnabled[viewerType] ? 'ENABLED ✅' : 'DISABLED ❌');

            showToolbarNotification(
                window.autoRotateEnabled[viewerType] ? 'Auto-rotate enabled' : 'Auto-rotate disabled',
                'success',
                1500
            );
        }
    },

    // ============================================
    // GRID TOGGLE - From Bottom Control Bar
    // ============================================
    toggleGridMain: function(viewerType) {
        console.log(`🏁 Toggle Grid (Main) for ${viewerType}`);

        // Use unified viewer
        const viewer = window.viewerGeneral || window.viewer;
        if (!viewer || !viewer.scene) {
            console.warn('⚠️ Cannot toggle grid: Viewer not ready');
            showToolbarNotification('Please wait for the 3D viewer to load', 'warning', 2000);
            return;
        }

        // Toggle grid visibility state (unified for both viewers)
        if (!window.gridVisibleMain) {
            window.gridVisibleMain = { unified: true }; // Default visible
        }

        window.gridVisibleMain.unified = !window.gridVisibleMain.unified;

        // Update BOTH button states (General and Medical)
        const gridBtnGeneral = document.getElementById('toggleGridBtnMain');
        const gridBtnMedical = document.getElementById('toggleGridBtnMainMedical');

        [gridBtnGeneral, gridBtnMedical].forEach(btn => {
            if (btn) {
                if (window.gridVisibleMain.unified) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            }
        });

        // Find and toggle grid helper in the scene
        const ground = viewer.scene.children.find(child =>
            child.name === 'ground' ||
            child.type === 'GridHelper' ||
            child.userData?.isGridHelper
        );

        if (ground) {
            ground.visible = window.gridVisibleMain.unified;
            console.log('🏁 Grid visibility:', window.gridVisibleMain.unified ? 'SHOWN ✅' : 'HIDDEN ❌');

            showToolbarNotification(
                window.gridVisibleMain.unified ? 'Grid enabled' : 'Grid disabled',
                'success',
                1500
            );
        } else {
            console.warn('⚠️ Grid helper not found in scene');
            showToolbarNotification('Grid helper not found', 'warning', 1500);
        }
    },

    // ============================================
    // MEASURE TOOL - From Bottom Control Bar (Two-Point Distance)
    // ============================================
    toggleMeasureMain: function(viewerType) {
        console.log(`📏 Toggle Measure Tool (Main) for ${viewerType}`);

        const viewer = window.viewerGeneral || window.viewer;
        if (!viewer || !viewer.scene) {
            console.warn('⚠️ Cannot toggle measure tool: Viewer not ready');
            showToolbarNotification('Please wait for the 3D viewer to load', 'warning', 2000);
            return;
        }

        // Toggle measurement mode state
        if (!window.measurementModeMain) {
            window.measurementModeMain = {};
            window.measurementPointsMain = {};
            window.measurementMarkersMain = {};
            window.measurementLineMain = {};
            window.measurementLabelMain = {};
        }

        const isCurrentlyActive = window.measurementModeMain[viewerType] || false;
        window.measurementModeMain[viewerType] = !isCurrentlyActive;

        const measureBtn = document.getElementById('measureToolBtnMain');

        if (measureBtn) {
            if (window.measurementModeMain[viewerType]) {
                measureBtn.classList.add('active');
                showToolbarNotification('Measurement mode: Click first point on model', 'info', 3000);

                // Attach canvas click handler
                const canvas = viewer.renderer ? viewer.renderer.domElement : null;
                if (canvas && !canvas.dataset.measureHandlerAttached) {
                    canvas.addEventListener('click', (e) => this.handleMeasurementClickMain(e, viewer, viewerType));
                    canvas.dataset.measureHandlerAttached = 'true';
                }
            } else {
                measureBtn.classList.remove('active');
                showToolbarNotification('Measurement mode disabled', 'info', 1500);

                // Clear measurements
                this.clearMeasurementMain(viewer, viewerType);
            }
        }
    },

    handleMeasurementClickMain: function(event, viewer, viewerType) {
        if (!window.measurementModeMain || !window.measurementModeMain[viewerType]) return;

        const THREE = window.THREE;
        const canvas = viewer.renderer ? viewer.renderer.domElement : null;
        if (!canvas) {
            console.warn('⚠️ Canvas not found for measurement');
            return;
        }

        const rect = canvas.getBoundingClientRect();
        const mouse = new THREE.Vector2();
        mouse.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
        mouse.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;

        const raycaster = new THREE.Raycaster();
        raycaster.setFromCamera(mouse, viewer.camera);

        let intersects = [];
        if (viewer.modelGroup) {
            intersects = raycaster.intersectObjects(viewer.modelGroup.children, true);
        }

        if (intersects.length === 0) return;

        const point = intersects[0].point;

        if (!window.measurementPointsMain[viewerType]) {
            window.measurementPointsMain[viewerType] = [];
            window.measurementMarkersMain[viewerType] = [];
        }

        if (window.measurementPointsMain[viewerType].length === 0) {
            // First point
            window.measurementPointsMain[viewerType].push(point);
            this.createMeasurementMarkerMain(viewer, point, viewerType, 0);
            showToolbarNotification('First point set! Click second point', 'info', 2000);
        } else if (window.measurementPointsMain[viewerType].length === 1) {
            // Second point - calculate distance
            window.measurementPointsMain[viewerType].push(point);
            this.createMeasurementMarkerMain(viewer, point, viewerType, 1);
            this.drawMeasurementLineMain(viewer, viewerType);
            this.calculateDistanceMain(viewer, viewerType);
        } else {
            // Reset for new measurement
            this.clearMeasurementMain(viewer, viewerType);
            window.measurementPointsMain[viewerType] = [point];
            this.createMeasurementMarkerMain(viewer, point, viewerType, 0);
            showToolbarNotification('New measurement: Click second point', 'info', 2000);
        }
    },

    createMeasurementMarkerMain: function(viewer, position, viewerType, index) {
        const THREE = window.THREE;
        const geometry = new THREE.SphereGeometry(2, 16, 16);
        const material = new THREE.MeshBasicMaterial({
            color: index === 0 ? 0x00ff00 : 0xff0000,
            transparent: true,
            opacity: 0.8
        });
        const marker = new THREE.Mesh(geometry, material);
        marker.position.copy(position);
        viewer.scene.add(marker);

        if (!window.measurementMarkersMain[viewerType]) {
            window.measurementMarkersMain[viewerType] = [];
        }
        window.measurementMarkersMain[viewerType].push(marker);
    },

    drawMeasurementLineMain: function(viewer, viewerType) {
        const THREE = window.THREE;
        const points = window.measurementPointsMain[viewerType];
        if (points.length < 2) return;

        const curve = new THREE.LineCurve3(points[0], points[1]);
        const tubeGeometry = new THREE.TubeGeometry(curve, 1, 0.5, 8, false);
        const material = new THREE.MeshBasicMaterial({
            color: 0x0000ff,
            transparent: true,
            opacity: 0.8
        });
        const line = new THREE.Mesh(tubeGeometry, material);
        viewer.scene.add(line);
        window.measurementLineMain[viewerType] = line;
    },

    calculateDistanceMain: function(viewer, viewerType) {
        const THREE = window.THREE;
        const points = window.measurementPointsMain[viewerType];
        if (points.length < 2) return;

        const distance = points[0].distanceTo(points[1]);
        const midPoint = new THREE.Vector3().addVectors(points[0], points[1]).multiplyScalar(0.5);

        // Create label
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');
        canvas.width = 512;
        canvas.height = 128;

        context.fillStyle = 'rgba(0, 0, 0, 0.8)';
        context.fillRect(0, 0, canvas.width, canvas.height);
        context.strokeStyle = '#ffff00';
        context.lineWidth = 4;
        context.strokeRect(2, 2, canvas.width - 4, canvas.height - 4);

        const text = `${distance.toFixed(2)} mm`;
        context.font = 'bold 48px Arial';
        context.fillStyle = 'white';
        context.textAlign = 'center';
        context.textBaseline = 'middle';
        context.fillText(text, canvas.width / 2, canvas.height / 2);

        const texture = new THREE.CanvasTexture(canvas);
        const geometry = new THREE.PlaneGeometry(40, 10);
        const material = new THREE.MeshBasicMaterial({
            map: texture,
            transparent: true,
            side: THREE.DoubleSide
        });
        const label = new THREE.Mesh(geometry, material);
        label.position.copy(midPoint);
        label.position.y += 15;
        viewer.scene.add(label);
        window.measurementLabelMain[viewerType] = label;

        showToolbarNotification(`Distance: ${distance.toFixed(2)} mm`, 'success', 3000);
    },

    clearMeasurementMain: function(viewer, viewerType) {
        // Clear markers
        if (window.measurementMarkersMain[viewerType]) {
            window.measurementMarkersMain[viewerType].forEach(marker => viewer.scene.remove(marker));
            window.measurementMarkersMain[viewerType] = [];
        }

        // Clear line
        if (window.measurementLineMain[viewerType]) {
            viewer.scene.remove(window.measurementLineMain[viewerType]);
            window.measurementLineMain[viewerType] = null;
        }

        // Clear label
        if (window.measurementLabelMain[viewerType]) {
            viewer.scene.remove(window.measurementLabelMain[viewerType]);
            window.measurementLabelMain[viewerType] = null;
        }

        // Clear points
        if (window.measurementPointsMain[viewerType]) {
            window.measurementPointsMain[viewerType] = [];
        }
    },

    // ============================================
    // SHARE MODEL - From Top Toolbar
    // ============================================
    shareModel: function(viewerType) {
        console.log(`🔗 Share Model for ${viewerType}`);

        const viewer = window.viewerGeneral || window.viewer;
        if (!viewer || !viewer.uploadedFiles || viewer.uploadedFiles.length === 0) {
            console.warn('⚠️ Cannot share: No files uploaded');
            showToolbarNotification('Please upload a 3D model first', 'warning', 2000);
            return;
        }

        // Get the first file ID
        const firstFile = viewer.uploadedFiles[0];
        const fileId = firstFile.id;

        // Check if share modal exists
        if (window.shareModal) {
            console.log('✓ Opening share modal with file ID:', fileId);
            window.shareModal.open(fileId);
            showToolbarNotification('Opening share options...', 'info', 1500);
        } else {
            console.warn('⚠️ Share modal not found');

            // Fallback: Show share URL
            const currentUrl = window.location.origin + window.location.pathname;
            const shareUrl = `${currentUrl}?file=${fileId}`;

            // Copy to clipboard
            if (navigator.clipboard) {
                navigator.clipboard.writeText(shareUrl).then(() => {
                    showToolbarNotification('Share URL copied to clipboard! ✓', 'success', 3000);
                }).catch(() => {
                    showToolbarNotification('Share URL: ' + shareUrl, 'info', 5000);
                });
            } else {
                showToolbarNotification('Share URL: ' + shareUrl, 'info', 5000);
            }
        }
    },

    // ============================================
    // SAVE & CALCULATE - From Top Toolbar
    // ============================================
    saveAndCalculate: function(viewerType) {
        console.log(`💾 Save & Calculate for ${viewerType}`);

        const viewer = window.viewerGeneral || window.viewer;
        if (!viewer || !viewer.uploadedFiles || viewer.uploadedFiles.length === 0) {
            console.warn('⚠️ Cannot save: No files uploaded');
            showToolbarNotification('Please upload a 3D model first', 'warning', 2000);
            return;
        }

        const formType = viewerType === 'Medical' ? 'Medical' : 'General';

        // Visual feedback on button
        const saveBtn = document.getElementById('saveCalculateToolBtn');
        if (saveBtn) {
            saveBtn.style.pointerEvents = 'none';
            const originalHTML = saveBtn.innerHTML;
            saveBtn.innerHTML = `
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" style="animation: spin 1s linear infinite;">
                    <circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="2" stroke-dasharray="4"/>
                </svg>
            `;

            setTimeout(() => {
                saveBtn.innerHTML = originalHTML;
                saveBtn.style.pointerEvents = '';
            }, 2000);
        }

        // Check if EnhancedSaveCalculate module exists (from quote.blade.php)
        if (window.EnhancedSaveCalculate && typeof window.EnhancedSaveCalculate.execute === 'function') {
            console.log('✓ Using EnhancedSaveCalculate module');
            window.EnhancedSaveCalculate.execute();
            showToolbarNotification('Calculating pricing...', 'info', 2000);
        } else {
            console.log('✓ Using fallback calculation method');

            // Fallback: Calculate and display prices for all files
            let totalPrice = 0;
            let totalVolume = 0;

            viewer.uploadedFiles.forEach((fileData, index) => {
                const volume = fileData.volume?.cm3 || 0;
                const settings = fileData.settings || {};
                const materialCost = settings.materialCost || 0.02;
                const technologyMultiplier = settings.technologyMultiplier || 1.0;
                const price = volume * materialCost * technologyMultiplier;

                totalVolume += volume;
                totalPrice += price;

                console.log(`📦 File ${index + 1}: ${fileData.file.name}`);
                console.log(`   Volume: ${volume.toFixed(2)} cm³`);
                console.log(`   Price: $${price.toFixed(2)}`);
            });

            console.log(`\n💰 TOTAL PRICE: $${totalPrice.toFixed(2)}`);
            console.log(`📊 TOTAL VOLUME: ${totalVolume.toFixed(2)} cm³`);

            // Show results
            showToolbarNotification(
                `Total: $${totalPrice.toFixed(2)} | Volume: ${totalVolume.toFixed(2)} cm³`,
                'success',
                4000
            );

            // Update file manager if available
            if (window.fileManagerGeneral && formType === 'General') {
                window.fileManagerGeneral.updateQuote();
            } else if (window.fileManagerMedical && formType === 'Medical') {
                window.fileManagerMedical.updateQuote();
            }
        }
    }
};

console.log('✅ INLINE window.toolbarHandler created!', window.toolbarHandler);
console.log('Available methods:', Object.keys(window.toolbarHandler));

// ============================================
// ATTACH EVENT LISTENERS TO ALL TOOLBAR BUTTONS
// ============================================
// DISABLED: This section was removing onclick handlers and breaking all buttons
/*
console.log('🔗 Attaching event listeners to toolbar buttons...');

document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit for viewers to initialize
    setTimeout(() => {
        console.log('🎯 Initializing toolbar button click handlers...');

        // Helper function to safely attach click handlers
        function attachHandler(buttonId, handler, ...args) {
            const btn = document.getElementById(buttonId);
            if (btn) {
                // Remove any existing onclick
                btn.removeAttribute('onclick');

                // Add new click event listener
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log(`🖱️ Button clicked: ${buttonId}`);
                    try {
                        handler.apply(window.toolbarHandler, args);
                    } catch (error) {
                        console.error(`❌ Error in ${buttonId}:`, error);
                    }
                });
                console.log(`✅ Attached handler to: ${buttonId}`);
            } else {
                console.warn(`⚠️ Button not found: ${buttonId}`);
            }
        }

        // GENERAL VIEWER TOOLBAR BUTTONS
        attachHandler('measurementToolBtn', window.toolbarHandler.toggleMeasurement, 'General');
        attachHandler('boundingBoxBtn', window.toolbarHandler.toggleBoundingBox, 'General');
        attachHandler('axisToggleBtn', window.toolbarHandler.toggleAxis, 'General');
        attachHandler('gridToggleBtn', window.toolbarHandler.toggleGrid, 'General');
        attachHandler('panToolBtn', window.toolbarHandler.toggleMoveMode, 'General');
        attachHandler('autoRotateBtn', window.toolbarHandler.toggleAutoRotate, 'General');
        attachHandler('toggleGridBtnMain', window.toolbarHandler.toggleGridMain, 'General');
        attachHandler('measureToolBtnMain', window.toolbarHandler.toggleMeasureMain, 'General');
        attachHandler('shadowToggleBtn', window.toolbarHandler.toggleShadow, 'General');
        attachHandler('transparencyBtn', window.toolbarHandler.toggleTransparency, 'General');
        attachHandler('modelColorBtn', window.toolbarHandler.changeModelColor, 'General');
        attachHandler('backgroundColorBtn', window.toolbarHandler.changeBackgroundColor, 'General');
        attachHandler('undoBtn', window.toolbarHandler.undo);
        attachHandler('redoBtn', window.toolbarHandler.redo);
        attachHandler('screenshotToolBtn', window.toolbarHandler.takeScreenshot, 'General');
        attachHandler('shareToolBtn', window.toolbarHandler.shareModel, 'General');
        attachHandler('saveCalculateToolBtn', window.toolbarHandler.saveAndCalculate, 'General');

        // MEDICAL VIEWER TOOLBAR BUTTONS (if they exist)
        attachHandler('measurementToolBtnMedical', window.toolbarHandler.toggleMeasurement, 'General');
        attachHandler('boundingBoxBtnMedical', window.toolbarHandler.toggleBoundingBox, 'General');
        attachHandler('axisToggleBtnMedical', window.toolbarHandler.toggleAxis, 'General');
        attachHandler('gridToggleBtnMedical', window.toolbarHandler.toggleGrid, 'General');
        attachHandler('panToolBtnMedical', window.toolbarHandler.toggleMoveMode, 'General');
        attachHandler('autoRotateBtnMedical', window.toolbarHandler.toggleAutoRotate, 'General');
        attachHandler('toggleGridBtnMainMedical', window.toolbarHandler.toggleGridMain, 'General');
        attachHandler('measureToolBtnMainMedical', window.toolbarHandler.toggleMeasureMain, 'General');
        attachHandler('shadowToggleBtnMedical', window.toolbarHandler.toggleShadow, 'General');
        attachHandler('transparencyBtnMedical', window.toolbarHandler.toggleTransparency, 'General');
        attachHandler('modelColorBtnMedical', window.toolbarHandler.changeModelColor, 'General');
        attachHandler('backgroundColorBtnMedical', window.toolbarHandler.changeBackgroundColor, 'General');
        attachHandler('undoBtnMedical', window.toolbarHandler.undo);
        attachHandler('redoBtnMedical', window.toolbarHandler.redo);
        attachHandler('screenshotToolBtnMedical', window.toolbarHandler.takeScreenshot, 'General');
        attachHandler('shareToolBtnMedical', window.toolbarHandler.shareModel, 'General');
        attachHandler('saveCalculateToolBtnMedical', window.toolbarHandler.saveAndCalculate, 'General');

        console.log('✅ All toolbar button handlers attached!');
        console.log('📊 Total buttons processed: ~34');

        // Verify handlers are working
        console.log('🔍 Verification:');
        console.log('   window.toolbarHandler:', !!window.toolbarHandler);
        console.log('   window.viewerGeneral:', !!window.viewerGeneral);
        console.log('   Buttons ready!');
    }, 1000); // Wait 1 second for viewer initialization
});

console.log('✅ Event listener attachment script loaded!');
*/
console.log('✅ Using onclick handlers directly (addEventListener code disabled)');
</script>

{{-- DISABLED: External file was overwriting inline toolbar handler --}}
{{-- <script src="{{ asset('frontend/assets/js/3d-viewer-professional-tools.js') }}?t={{ time() }}"></script> --}}
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

{{-- Undo/Redo Manager System --}}
<script src="{{ asset('frontend/assets/js/undo-redo-manager.js') }}?v={{ time() }}"></script>

{{-- Mesh Repair with Visual Feedback --}}
<script src="{{ asset('frontend/assets/js/mesh-repair-visual.js') }}?v={{ time() }}"></script>

<script src="{{ asset('frontend/assets/js/enhanced-save-calculate.js') }}?v=20251226-{{ time() }}"></script>
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
                viewerGeneral: this.checkViewer(window.viewerGeneral, 'General (Unified)'),
                // viewerMedical removed - both use unified viewer now
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

            // Relaxed health check - only check essential properties
            const checks = {
                exists: !!viewer,
                scene: !!viewer.scene,
                hasRenderFunction: typeof viewer.render === 'function'
            };

            const healthy = Object.values(checks).every(c => c === true);

            if (!healthy) {
                console.warn(`❌ ${name} viewer unhealthy:`, checks);
            } else {
                console.log(`✅ ${name} viewer healthy`);
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
                console.log('🔧 Adding fallback calculateVolume to unified viewer...');
                this.addFallbackVolumeCalculation(window.viewerGeneral);
            }

            // viewerMedical removed - both use unified viewer now
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

{{-- BRAND NEW SIMPLE MODAL --}}
<div id="simpleFileModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 999999; background: rgba(0,0,0,0.5); align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; width: 90%; max-width: 500px; box-shadow: 0 10px 40px rgba(0,0,0,0.3);">
        <!-- Header -->
        <div style="padding: 20px 24px; border-bottom: 1px solid #e9ecef; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h5 style="margin: 0 0 4px 0; font-weight: 600; color: #2c3e50;">File Settings</h5>
                <p id="simpleModalFileName" style="margin: 0; font-size: 0.875rem; color: #6c757d;"></p>
            </div>
            <button onclick="window.closeSimpleModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #6c757d; padding: 0; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">&times;</button>
        </div>

        <!-- Body -->
        <div style="padding: 24px;">
            <!-- Technology -->
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-size: 0.85rem; font-weight: 600; color: #495057;">Technology</label>
                <select id="simpleTechSelect" style="width: 100%; padding: 8px 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 0.85rem;">
                    <option value="fdm">FDM (Fused Deposition Modeling)</option>
                    <option value="sla">SLA (Stereolithography)</option>
                    <option value="sls">SLS (Selective Laser Sintering)</option>
                    <option value="dmls">DMLS (Direct Metal Laser Sintering)</option>
                    <option value="mjf">MJF (Multi Jet Fusion)</option>
                </select>
            </div>

            <!-- Material -->
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-size: 0.85rem; font-weight: 600; color: #495057;">Material</label>
                <select id="simpleMaterialSelect" style="width: 100%; padding: 8px 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 0.85rem;">
                    <option value="pla">PLA</option>
                    <option value="abs">ABS</option>
                    <option value="petg">PETG</option>
                    <option value="nylon">Nylon</option>
                    <option value="resin">Resin</option>
                </select>
            </div>

            <!-- Color -->
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-size: 0.85rem; font-weight: 600; color: #2c3e50;">Model Color</label>
                <div id="simpleColorPicker" style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <div class="simple-color-btn" data-color="#0047AD" style="width: 40px; height: 40px; background: #0047AD; border: 2px solid #0047AD; border-radius: 8px; cursor: pointer; position: relative;" onclick="window.selectSimpleColor(this)"></div>
                    <div class="simple-color-btn" data-color="#ffffff" style="width: 40px; height: 40px; background: #ffffff; border: 2px solid #dee2e6; border-radius: 8px; cursor: pointer; position: relative;" onclick="window.selectSimpleColor(this)"></div>
                    <div class="simple-color-btn" data-color="#2c3e50" style="width: 40px; height: 40px; background: #2c3e50; border: 2px solid #dee2e6; border-radius: 8px; cursor: pointer; position: relative;" onclick="window.selectSimpleColor(this)"></div>
                    <div class="simple-color-btn" data-color="#3498db" style="width: 40px; height: 40px; background: #3498db; border: 2px solid #dee2e6; border-radius: 8px; cursor: pointer; position: relative;" onclick="window.selectSimpleColor(this)"></div>
                    <div class="simple-color-btn" data-color="#e74c3c" style="width: 40px; height: 40px; background: #e74c3c; border: 2px solid #dee2e6; border-radius: 8px; cursor: pointer; position: relative;" onclick="window.selectSimpleColor(this)"></div>
                    <div class="simple-color-btn" data-color="#2ecc71" style="width: 40px; height: 40px; background: #2ecc71; border: 2px solid #dee2e6; border-radius: 8px; cursor: pointer; position: relative;" onclick="window.selectSimpleColor(this)"></div>
                    <div class="simple-color-btn" data-color="#f39c12" style="width: 40px; height: 40px; background: #f39c12; border: 2px solid #dee2e6; border-radius: 8px; cursor: pointer; position: relative;" onclick="window.selectSimpleColor(this)"></div>
                    <div class="simple-color-btn" data-color="#9b59b6" style="width: 40px; height: 40px; background: #9b59b6; border: 2px solid #dee2e6; border-radius: 8px; cursor: pointer; position: relative;" onclick="window.selectSimpleColor(this)"></div>
                </div>
            </div>

            <!-- Layer Height (Dental only - fixed and validated) -->
            <div id="simpleLayerHeightSection" style="margin-bottom: 16px; display: none;">
                <label style="display: block; margin-bottom: 8px; font-size: 0.85rem; font-weight: 600; color: #495057;">
                    Layer Height
                    <span style="color: #28a745; font-size: 0.75rem; margin-left: 8px;">🔒 Fixed, Validated</span>
                </label>
                <input type="text" value="25-50 μm" readonly disabled style="width: 100%; padding: 8px 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 0.85rem; background: #f8f9fa; color: #6c757d; cursor: not-allowed;">
            </div>
        </div>

        <!-- Footer -->
        <div style="padding: 16px 24px; border-top: 1px solid #e9ecef; display: flex; justify-content: flex-end; gap: 12px;">
            <button onclick="window.closeSimpleModal()" style="padding: 8px 20px; border: 1px solid #dee2e6; background: white; color: #6c757d; border-radius: 8px; cursor: pointer; font-size: 14px;">Cancel</button>
            <button onclick="window.saveSimpleModal()" style="padding: 8px 20px; border: none; background: #1976D2; color: white; border-radius: 8px; cursor: pointer; font-size: 14px; font-weight: 500;">Apply Changes</button>
        </div>
    </div>
</div>

<style>
.simple-color-btn.active::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 20px;
    font-weight: bold;
    text-shadow: 0 0 3px rgba(0,0,0,0.5);
}
</style>

<script>
// Modal functions already defined at the top of the page
// Just add event listeners here

// Close on backdrop click
document.addEventListener('click', function(e) {
    const modal = document.getElementById('simpleFileModal');
    if (e.target === modal && window.closeSimpleModal) {
        window.closeSimpleModal();
    }
});

// Close on ESC key
document.addEventListener('keydown', function(e) {
    const modal = document.getElementById('simpleFileModal');
    if (e.key === 'Escape' && modal && modal.style.display === 'flex' && window.closeSimpleModal) {
        window.closeSimpleModal();
    }
});

// =====================================
// FILE RESTORATION FROM URL PARAMETERS
// =====================================
document.addEventListener('DOMContentLoaded', async function() {
    const urlParams = new URLSearchParams(window.location.search);
    const filesParam = urlParams.get('files');
    const viewerType = urlParams.get('viewer') || 'general';

    if (!filesParam) {
        console.log('📂 No files parameter found in URL');
        return;
    }

    console.log('🔄 Restoring files from URL:', filesParam);
    console.log('📺 Viewer type:', viewerType);

    // Parse file IDs from comma-separated string
    const fileIds = filesParam.split(',').map(id => id.trim()).filter(id => id);

    if (fileIds.length === 0) {
        console.log('⚠️ No valid file IDs found');
        return;
    }

    console.log('📋 File IDs to restore:', fileIds);

    // Get the viewer instance
    const viewer = viewerType === 'dental' ? window.viewerDental : window.viewerGeneral;

    if (!viewer) {
        console.error('❌ Viewer not initialized yet');
        // Retry after a short delay
        setTimeout(() => {
            const retryViewer = viewerType === 'dental' ? window.viewerDental : window.viewerGeneral;
            if (retryViewer) {
                restoreFiles(fileIds, retryViewer);
            } else {
                console.error('❌ Viewer still not available after retry');
            }
        }, 1000);
        return;
    }

    restoreFiles(fileIds, viewer);
});

async function restoreFiles(fileIds, viewer) {
    console.log('🔄 Starting file restoration...');

    for (const fileId of fileIds) {
        try {
            console.log(`📥 Fetching file: ${fileId}`);

            const response = await fetch(`/api/3d-files/${fileId}`);

            if (!response.ok) {
                console.error(`❌ Failed to fetch file ${fileId}:`, response.status);
                continue;
            }

            const data = await response.json();

            if (!data.success) {
                console.error(`❌ API error for file ${fileId}:`, data.message);
                continue;
            }

            console.log(`✅ File data received for ${data.fileName}`);

            // Decode base64 file data
            const fileData = data.fileData;
            const byteCharacters = atob(fileData);
            const byteNumbers = new Array(byteCharacters.length);
            for (let i = 0; i < byteCharacters.length; i++) {
                byteNumbers[i] = byteCharacters.charCodeAt(i);
            }
            const byteArray = new Uint8Array(byteNumbers);

            // Create a File object
            const file = new File([byteArray], data.fileName, {
                type: 'application/octet-stream',
                lastModified: data.uploadTime
            });

            console.log(`📦 Reconstructed file object:`, {
                name: file.name,
                size: file.size,
                type: file.type
            });

            // Load file into viewer
            await viewer.loadFile(file, data.fileId);
            console.log(`✅ File loaded into viewer: ${data.fileName}`);

        } catch (error) {
            console.error(`❌ Error restoring file ${fileId}:`, error);
        }
    }

    console.log('✅ File restoration complete');

    // Clean up URL (remove files parameter to prevent reloading, but preserve viewer parameter)
    const url = new URL(window.location);
    const viewerParam = url.searchParams.get('viewer');
    url.searchParams.delete('files');
    if (viewerParam) {
        url.searchParams.set('viewer', viewerParam);
    }
    window.history.replaceState({}, '', url);
}
</script>









