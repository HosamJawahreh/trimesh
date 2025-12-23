/**
 * ========================================
 * ENHANCED SAVE & CALCULATE
 * With Auto Mesh Analysis and Repair
 * VERSION: 4.0 - SERVER-SIDE REPAIR WITH COLOR PRESERVATION
 * ========================================
 */

console.log('💾 ===== ENHANCED SAVE & CALCULATE V4.0 LOADED =====');
console.log('💾 WITH PYMESHFIX + COLOR PRESERVATION - TIMESTAMP:', new Date().toISOString());
console.log('💾 If you see V4.0, the NEW JavaScript with server-side repair is loaded!');
console.log('💾 If you see V3 or lower, HARD REFRESH (Ctrl + Shift + R) NOW!');

window.EnhancedSaveCalculate = {
    version: '4.0',
    isProcessing: false,
    serverSideRepairAvailable: false,
    useServerSideRepair: true, // Default to server-side if available

    /**
     * Check if server-side mesh repair service is available
     */
    async checkServerRepairStatus() {
        try {
            // Add cache-busting and no-cache headers
            const response = await fetch('/api/mesh/status?_=' + Date.now(), {
                cache: 'no-cache',
                headers: {
                    'Cache-Control': 'no-cache, no-store, must-revalidate',
                    'Pragma': 'no-cache',
                    'Expires': '0'
                }
            });
            if (response.ok) {
                const data = await response.json();
                this.serverSideRepairAvailable = data.available === true;
                console.log('🔧 Server-side mesh repair:', this.serverSideRepairAvailable ? 'AVAILABLE ✅' : 'UNAVAILABLE ❌');
                console.log('🔧 Server response:', data);
                return this.serverSideRepairAvailable;
            } else {
                console.error('🔧 Server status check failed:', response.status, response.statusText);
                this.serverSideRepairAvailable = false;
            }
        } catch (error) {
            console.warn('⚠️ Server-side repair check failed:', error.message);
            this.serverSideRepairAvailable = false;
        }
        return false;
    },

    /**
     * Repair mesh using server-side Python service (production-grade)
     */
    async repairMeshServerSide(fileData, viewerId = 'general') {
        try {
            console.log(`🌐 Server-side repair starting for: ${fileData.file.name}`);

            // Check if file has a storage ID (already uploaded to database)
            let fileId = fileData.storageId || fileData.id;
            console.log('📋 File ID:', fileId);

            // If file is NOT in database yet, upload it first
            if (!fileId || !fileId.startsWith('file_')) {
                console.log('📤 File not in database yet, uploading first...');

                // Convert file to base64
                const arrayBuffer = await fileData.file.arrayBuffer();
                const bytes = new Uint8Array(arrayBuffer);
                let binary = '';
                for (let i = 0; i < bytes.length; i++) {
                    binary += String.fromCharCode(bytes[i]);
                }
                const base64Data = btoa(binary);

                // Upload to server
                const uploadResponse = await fetch('/api/3d-files/store', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        file: base64Data,
                        fileName: fileData.file.name,
                        metadata: JSON.stringify({
                            size: fileData.file.size,
                            type: fileData.file.type,
                            uploadedFrom: 'enhanced-save-calculate'
                        })
                    })
                });

                if (!uploadResponse.ok) {
                    throw new Error('Failed to upload file to server');
                }

                const uploadResult = await uploadResponse.json();
                if (!uploadResult.success) {
                    throw new Error(uploadResult.message || 'Upload failed');
                }

                fileId = uploadResult.fileId;
                fileData.storageId = fileId; // Store for future use
                console.log('✅ File uploaded to server with ID:', fileId);

                // Update URL to include this file
                if (window.fileStorageManager) {
                    window.fileStorageManager.updateURL(fileId);
                }
            }

            // Analyze mesh first
            this.updateProgress('Analyzing mesh on server...', 30);

            let analyzeResponse;

            if (fileId && fileId.startsWith('file_')) {
                // File is already in database - use file_id parameter
                console.log('💾 Using file ID from database:', fileId);

                const formData = new FormData();
                formData.append('file_id', fileId);
                formData.append('aggressive', 'true');

                analyzeResponse = await fetch('/api/mesh/analyze', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                    }
                });
            } else {
                // File needs to be uploaded - use file upload
                console.log('📤 Uploading file to server:', fileData.file.name, 'size:', fileData.file.size);

                const formData = new FormData();
                formData.append('file', fileData.file);
                formData.append('aggressive', 'true');

                analyzeResponse = await fetch('/api/mesh/analyze', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                    }
                });
            }

            console.log('📥 Analyze response status:', analyzeResponse.status, analyzeResponse.statusText);

            if (!analyzeResponse.ok) {
                const errorData = await analyzeResponse.json().catch(() => ({}));
                console.error('❌ Analysis error response:', errorData);
                throw new Error(`Analysis failed: ${analyzeResponse.statusText} - ${JSON.stringify(errorData)}`);
            }

            const analysis = await analyzeResponse.json();
            console.log('📊 Server analysis result:', analysis);

            // Show recommendations if any
            if (analysis.recommendations && analysis.recommendations.length > 0) {
                console.log('💡 Recommendations:', analysis.recommendations);
            }

            // Check if repair is needed
            if (analysis.analysis.is_watertight) {
                console.log('✓ Mesh is already watertight - no repair needed');
                return {
                    repaired: false,
                    watertight: true,
                    volume_cm3: analysis.analysis.volume_cm3,
                    quality_score: 100,
                    server_side: true
                };
            }

            // Perform repair
            this.updateProgress(`Repairing mesh (${analysis.analysis.holes_count} holes)...`, 50);

            let repairResponse;

            if (fileId && fileId.startsWith('file_')) {
                // File is already in database - use file_id parameter
                console.log('💾 Repairing using file ID from database:', fileId);

                const repairFormData = new FormData();
                repairFormData.append('file_id', fileId);
                repairFormData.append('aggressive', 'true');
                repairFormData.append('save_result', 'true'); // Save to database for admin logs

                repairResponse = await fetch('/api/mesh/repair', {
                    method: 'POST',
                    body: repairFormData
                });
            } else {
                // File needs to be uploaded - use file upload
                console.log('📤 Repairing by uploading file:', fileData.file.name);

                const repairFormData = new FormData();
                repairFormData.append('file', fileData.file);
                repairFormData.append('aggressive', 'true');
                repairFormData.append('save_result', 'true'); // Save to database for admin logs

                repairResponse = await fetch('/api/mesh/repair', {
                    method: 'POST',
                    body: repairFormData
                });
            }

            if (!repairResponse.ok) {
                throw new Error(`Repair failed: ${repairResponse.statusText}`);
            }

            const repairResult = await repairResponse.json();
            console.log('✅ Server repair complete:', repairResult);

            return {
                repaired: true,
                original_volume_cm3: repairResult.original_stats.volume_cm3,
                repaired_volume_cm3: repairResult.repaired_stats.volume_cm3,
                holes_filled: repairResult.repair_summary.holes_filled,
                quality_score: repairResult.quality_score,
                volume_change_cm3: repairResult.volume_change_cm3,
                volume_change_percent: repairResult.volume_change_percent,
                watertight: repairResult.repaired_stats.is_watertight,
                server_side: true
            };

        } catch (error) {
            console.error('❌ Server-side repair error:', error);
            throw error;
        }
    },

    /**
     * Show server repair results in UI
     */
    showServerRepairResults(results) {
        const hasRepairs = results.some(r => r.repaired);
        const totalHolesFilled = results.reduce((sum, r) => sum + (r.holes_filled || 0), 0);
        const avgQuality = results.reduce((sum, r) => sum + (r.quality_score || 0), 0) / results.length;

        let message = '';
        let type = 'info';

        if (hasRepairs) {
            const totalChange = results.reduce((sum, r) => sum + Math.abs(r.volume_change_cm3 || 0), 0);
            message = `
                <strong>Server-side repair complete!</strong><br>
                • Holes filled: ${totalHolesFilled}<br>
                • Volume change: ${totalChange.toFixed(4)} cm³<br>
                • Quality score: ${avgQuality.toFixed(1)}/100 (${this.getQualityRating(avgQuality)})
            `;
            type = 'success';
        } else {
            message = 'All meshes are watertight - no repairs needed';
            type = 'success';
        }

        if (window.showToolbarNotification) {
            showToolbarNotification(message, type, 6000);
        }
    },

    /**
     * Get quality rating from score
     */
    getQualityRating(score) {
        if (score >= 90) return 'Excellent';
        if (score >= 70) return 'Good';
        if (score >= 50) return 'Fair';
        return 'Poor';
    },

    /**
     * Save quote to database with file IDs and pricing
     */
    async saveQuoteToDatabase(viewer, viewerId, totalVolume, totalPrice) {
        try {
            console.log('📊 Preparing quote data for database...');

            // Get file IDs from viewer's uploaded files
            const fileIds = [];
            const pricingBreakdown = [];

            if (viewer.uploadedFiles && viewer.uploadedFiles.length > 0) {
                for (const fileData of viewer.uploadedFiles) {
                    // Get file ID from storage or generate one
                    let fileId = fileData.storageId || fileData.id;

                    if (!fileId || !fileId.startsWith('file_')) {
                        console.warn('⚠️ File missing storage ID, attempting to get from storage manager...');

                        // Try to get from file storage manager
                        if (window.fileStorageManager && window.fileStorageManager.currentFileId) {
                            fileId = window.fileStorageManager.currentFileId;
                        } else {
                            console.error('❌ Cannot save quote - files not properly stored');
                            throw new Error('Files must be saved to storage before creating quote');
                        }
                    }

                    fileIds.push(fileId);

                    // Add pricing breakdown for this file
                    pricingBreakdown.push({
                        file_id: fileId,
                        file_name: fileData.file?.name || 'Unknown',
                        volume_cm3: fileData.volume || 0,
                        price: fileData.price || 0,
                    });
                }
            }

            if (fileIds.length === 0) {
                throw new Error('No files with valid IDs found');
            }

            console.log('📋 File IDs for quote:', fileIds);

            // Get current settings (material, color, quality, etc.)
            const viewerSuffix = viewerId === 'general' ? '' : 'Medical';
            const material = document.getElementById(`quoteMaterial${viewerSuffix}`)?.value || 'PLA';
            const color = document.getElementById(`quoteColor${viewerSuffix}`)?.value || 'White';
            const quality = document.getElementById(`quoteQuality${viewerSuffix}`)?.value || 'Standard';
            const quantity = parseInt(document.getElementById(`quoteQuantity${viewerSuffix}`)?.value || '1');

            // Get customer info if available (from form)
            const customerName = document.getElementById(`customerName${viewerSuffix}`)?.value || null;
            const customerEmail = document.getElementById(`customerEmail${viewerSuffix}`)?.value || null;
            const customerPhone = document.getElementById(`customerPhone${viewerSuffix}`)?.value || null;

            // Prepare quote data
            const quoteData = {
                file_ids: fileIds,
                total_volume_cm3: totalVolume,
                total_price: totalPrice,
                material: material,
                color: color,
                quality: quality,
                quantity: quantity,
                pricing_breakdown: pricingBreakdown,
                customer_name: customerName,
                customer_email: customerEmail,
                customer_phone: customerPhone,
                form_type: viewerId,
                notes: 'Auto-saved from Save & Calculate'
            };

            console.log('📤 Sending quote data to server:', quoteData);

            // Send to API
            const response = await fetch('/api/quotes/store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(quoteData)
            });

            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`API error: ${response.status} - ${errorText}`);
            }

            const result = await response.json();
            console.log('✅ Quote API response:', result);

            return result;

        } catch (error) {
            console.error('❌ Error saving quote to database:', error);
            throw error;
        }
    },

    async execute(viewerId = 'general') {
        if (this.isProcessing) {
            console.warn('⚠️ Already processing...');
            return;
        }

        this.isProcessing = true;
        const viewer = viewerId === 'general' ? window.viewerGeneral : window.viewerMedical;

        console.log('🔍 Checking viewer state:', {
            viewer: !!viewer,
            initialized: viewer?.initialized,
            uploadedFiles: viewer?.uploadedFiles,
            filesLength: viewer?.uploadedFiles?.length
        });

        // Check if viewer exists and is initialized
        if (!viewer) {
            console.error('❌ Viewer not found');
            this.showNotification('Viewer not initialized. Please refresh the page.', 'error');
            this.isProcessing = false;
            return;
        }

        // Check if files are uploaded
        const hasFiles = viewer.uploadedFiles && viewer.uploadedFiles.length > 0;
        if (!hasFiles) {
            console.warn('⚠️ No files uploaded');
            this.showNotification('Please upload a 3D model first', 'warning');
            this.isProcessing = false;
            return;
        }

        try {
            console.log('🚀 Starting enhanced save & calculate...');
            this.showProgressModal();

            // Check if server-side repair is available
            await this.updateProgress('Checking repair services...', 10);
            await this.checkServerRepairStatus();

            // Step 1: Analyze and Repair meshes
            await this.updateProgress('Analyzing meshes...', 20);
            const analysisResults = [];
            const repairResults = [];
            let useServerRepair = this.serverSideRepairAvailable && this.useServerSideRepair;

            // Try server-side repair first if available
            if (useServerRepair) {
                console.log('🌐 Using server-side mesh repair (production-grade)');
                try {
                    for (const fileData of viewer.uploadedFiles) {
                        const serverResult = await this.repairMeshServerSide(fileData, viewerId);
                        repairResults.push({
                            fileName: fileData.file.name,
                            ...serverResult
                        });

                        // Store server-calculated volume for later use
                        fileData.serverVolume = serverResult.repaired_volume_cm3 || serverResult.volume_cm3;
                    }

                    this.showServerRepairResults(repairResults);

                } catch (serverError) {
                    console.error('⚠️ Server-side repair failed, falling back to client-side:', serverError);
                    useServerRepair = false;
                    repairResults.length = 0; // Clear results
                }
            }

            // Fallback to client-side repair if server-side not available or failed
            if (!useServerRepair && window.MeshRepairVisual) {
                console.log('💻 Using client-side mesh repair (fallback)');
                try {
                    for (const fileData of viewer.uploadedFiles) {
                        if (fileData.mesh && fileData.mesh.geometry) {
                            console.log(`🔍 Analyzing: ${fileData.file.name}`);
                            const analysis = window.MeshRepairVisual.analyzeGeometry(fileData.mesh.geometry);
                            analysisResults.push({
                                file: fileData.file.name,
                                analysis: analysis
                            });
                            console.log(`   📊 Analysis result:`, analysis);
                        }
                    }
                } catch (analysisError) {
                    console.warn('⚠️ Analysis encountered error:', analysisError);
                }

                // Repair meshes with visual feedback
                await this.updateProgress('Repairing meshes...', 40);

                if (analysisResults.length > 0) {
                    try {
                        for (let i = 0; i < viewer.uploadedFiles.length; i++) {
                            const fileData = viewer.uploadedFiles[i];
                            const analysis = analysisResults[i];

                            console.log(`🔧 Processing: ${fileData.file.name}`);
                            console.log(`   Analysis: ${JSON.stringify(analysis.analysis)}`);

                            if (fileData.mesh && analysis) {
                                // Always try to repair if there are open edges (even if holes estimate is 0)
                                if (analysis.analysis.openEdges > 0 || analysis.analysis.holes > 0) {
                                    console.log(`🔧 Repairing: ${fileData.file.name}`);
                                    console.log(`   Holes: ${analysis.analysis.holes}, Open edges: ${analysis.analysis.openEdges}`);

                                    const result = await window.MeshRepairVisual.repairMeshWithVisualization(
                                        viewer,
                                        fileData
                                    );

                                    repairResults.push({
                                        fileName: fileData.file.name,
                                        ...result
                                    });

                                    console.log(`   ✅ Repair result:`, result);

                                    // CRITICAL CHECK: Verify geometry was updated
                                    if (result.repaired && result.holesFilled > 0) {
                                        console.log(`   🔍 VERIFYING REPAIR:`);
                                        console.log(`      fileData.geometry exists: ${!!fileData.geometry}`);
                                        console.log(`      fileData.mesh.geometry updated: ${fileData.mesh.geometry === fileData.geometry}`);
                                        if (fileData.geometry) {
                                            console.log(`      Repaired geometry vertices: ${fileData.geometry.attributes.position.count}`);
                                        }
                                    }
                                } else {
                                    console.log(`   ✓ ${fileData.file.name} is watertight - no repair needed`);
                                    repairResults.push({
                                        fileName: fileData.file.name,
                                        repaired: false,
                                        holesFound: 0,
                                        holesFilled: 0,
                                        watertight: true
                                    });
                                }
                            }
                        }

                        // Show summary notification
                        const totalFilled = repairResults.reduce((sum, r) => sum + (r.holesFilled || 0), 0);
                        const totalFound = repairResults.reduce((sum, r) => sum + (r.holesFound || 0), 0);
                        const hasErrors = repairResults.some(r => r.error);

                        console.log(`📊 Repair summary: Found ${totalFound} holes, filled ${totalFilled}`);

                        if (window.showToolbarNotification) {
                            if (hasErrors) {
                                showToolbarNotification(
                                    `Mesh appears damaged. Using original geometry for calculation. Consider repairing mesh in 3D software.`,
                                    'warning',
                                    7000
                                );
                            } else if (totalFilled > 0) {
                                showToolbarNotification(
                                    `Repaired ${totalFilled} holes across ${repairResults.length} files. Repaired areas shown in green/cyan.`,
                                    'success',
                                    5000
                                );
                            } else if (totalFound > 0) {
                                showToolbarNotification(
                                    `Found ${totalFound} holes but could not repair them automatically. Using original geometry.`,
                                    'warning',
                                    5000
                                );
                            } else {
                                showToolbarNotification(
                                    `All meshes are watertight - no repairs needed.`,
                                    'success',
                                    3000
                                );
                            }
                        }
                    } catch (repairError) {
                        console.error('❌ Repair encountered error:', repairError);
                        console.error('Stack:', repairError.stack);
                    }
                } else {
                    console.log('   ℹ️ No analysis performed');
                }
            }

            // Step 3: Calculate volumes (AFTER repair, using server volume if available)
            await this.updateProgress('Calculating volumes...', 60);
            let totalVolume = 0;

            console.log('📐 Starting volume calculation (AFTER repair)...');

            for (const fileData of viewer.uploadedFiles) {
                try {
                    let volume = 0;

                    // PRIORITY: Use server-calculated volume if available (most accurate)
                    if (fileData.serverVolume) {
                        console.log(`📐 Using server-calculated volume for: ${fileData.file?.name}`);
                        volume = fileData.serverVolume;
                        fileData.volume = { cm3: volume, mm3: volume * 1000 };
                        totalVolume += volume;
                        console.log(`   ✅ Server volume: ${volume.toFixed(4)} cm³ (production-grade)`);
                        continue;
                    }

                    // FALLBACK: Calculate from geometry (client-side or original)
                    const geometry = fileData.geometry || (fileData.mesh && fileData.mesh.geometry);

                    if (!geometry) {
                        console.warn(`⚠️ No geometry found for ${fileData.file?.name || 'unknown file'}`);
                        continue;
                    }

                    console.log(`📐 Calculating volume for: ${fileData.file?.name}`);
                    console.log(`   🔍 DEBUGGING GEOMETRY SOURCE:`);
                    console.log(`      fileData.geometry exists: ${!!fileData.geometry}`);
                    console.log(`      Using repaired geometry: ${!!fileData.geometry}`);
                    console.log(`   Geometry has ${geometry.attributes.position.count} vertices`);
                    console.log(`   Indexed: ${!!geometry.index}`);

                    // Try using viewer's calculateVolume method (pass GEOMETRY, not mesh!)
                    if (viewer.calculateVolume && typeof viewer.calculateVolume === 'function') {
                        console.log(`   Using viewer.calculateVolume method`);
                        volume = viewer.calculateVolume(geometry);
                    }
                    // Fallback: Calculate volume directly from geometry
                    else {
                        console.log(`   Using fallback volume calculation method`);
                        volume = this.calculateMeshVolume(geometry);
                    }

                    // Handle return value - could be object {cm3, mm3} or just number
                    if (typeof volume === 'object' && volume !== null) {
                        if (volume.cm3) {
                            fileData.volume = volume;
                            totalVolume += volume.cm3;
                            console.log(`   ✅ Volume: ${volume.cm3.toFixed(2)} cm³ (${volume.mm3.toFixed(2)} mm³)`);
                        } else {
                            console.warn(`   ⚠️ Invalid volume object:`, volume);
                        }
                    } else if (typeof volume === 'number' && !isNaN(volume)) {
                        fileData.volume = { cm3: volume, mm3: volume * 1000 };
                        totalVolume += volume;
                        console.log(`   ✅ Volume: ${volume.toFixed(2)} cm³ (${(volume * 1000).toFixed(2)} mm³)`);
                    } else {
                        console.warn(`   ⚠️ Invalid volume value:`, volume);
                    }

                } catch (volumeError) {
                    console.error(`❌ Error calculating volume for ${fileData.file?.name}:`, volumeError);
                    console.error('Error stack:', volumeError.stack);
                    // Continue with other files
                }
            }

            console.log(`📊 Total volume calculated: ${totalVolume.toFixed(2)} cm³`);

            // If no volume calculated, show error
            if (totalVolume === 0) {
                throw new Error('Could not calculate model volume. The geometry may be invalid or files may not be loaded properly.');
            }

            // Step 4: Calculate pricing
            await this.updateProgress('Calculating pricing...', 80);

            // Get selected technology and material
            const techSelect = document.getElementById(`technologySelect${viewerId === 'general' ? 'General' : 'Medical'}`);
            const matSelect = document.getElementById(`materialSelect${viewerId === 'general' ? 'General' : 'Medical'}`);

            const technology = techSelect?.value || 'fdm';
            const material = matSelect?.value || 'pla';

            console.log(`💰 Pricing calculation:`);
            console.log(`   Technology: ${technology} (from dropdown: ${techSelect?.value})`);
            console.log(`   Material: ${material} (from dropdown: ${matSelect?.value})`);
            console.log(`   Volume (REPAIRED): ${totalVolume.toFixed(2)} cm³`);

            // Calculate price based on technology, material, and NEW volume (includes repairs)
            const pricePerCm3 = this.getPricePerCm3(technology, material);
            console.log(`   📊 Looking up price for [${technology}][${material}]`);
            console.log(`   📊 Price per cm³: $${pricePerCm3.toFixed(2)}`);

            const totalPrice = totalVolume * pricePerCm3;
            const printTime = this.estimatePrintTime(totalVolume, technology);

            console.log(`   ✅ FINAL CALCULATION:`);
            console.log(`      ${totalVolume.toFixed(2)} cm³ × $${pricePerCm3.toFixed(2)}/cm³ = $${totalPrice.toFixed(2)}`);
            console.log(`   Print time: ${printTime}`);

            // Step 5: Update UI
            await this.updateProgress('Updating interface...', 95);

            const viewerSuffix = viewerId === 'general' ? 'General' : 'Medical';

            // Update ALL volume displays (there are multiple)
            const volumeDisplays = document.querySelectorAll(`#quoteTotalVolume${viewerSuffix}`);
            volumeDisplays.forEach(display => {
                if (display) {
                    display.textContent = `${totalVolume.toFixed(2)} cm³`;
                    display.style.display = 'block';
                }
            });

            // Also try the sidebar variant
            const volumeSidebar = document.getElementById(`quoteTotalVolume${viewerSuffix}`);
            if (volumeSidebar) {
                volumeSidebar.textContent = `${totalVolume.toFixed(2)} cm³`;
                volumeSidebar.style.display = 'block';
            }

            // Update ALL price displays (there are multiple)
            const priceDisplays = document.querySelectorAll(`#quoteTotalPrice${viewerSuffix}`);
            priceDisplays.forEach(display => {
                if (display) {
                    display.textContent = `$${totalPrice.toFixed(2)}`;
                    display.style.display = 'block';
                }
            });

            // Also try the sidebar variant
            const priceSidebar = document.getElementById(`quoteTotalPrice${viewerSuffix}`);
            if (priceSidebar) {
                priceSidebar.textContent = `$${totalPrice.toFixed(2)}`;
                priceSidebar.style.display = 'block';
            }

            // Update print time
            const timeDisplay = document.getElementById(`quotePrintTime${viewerSuffix}`);
            if (timeDisplay) {
                timeDisplay.textContent = printTime;
            }

            // Show price summary section
            const priceSummary = document.getElementById(`priceSummary${viewerSuffix}`);
            if (priceSummary) {
                priceSummary.style.display = 'block';
            }

            console.log(`✅ UI updated:`);
            console.log(`   Volume displays updated: ${volumeDisplays.length} elements`);
            console.log(`   Price displays updated: ${priceDisplays.length} elements`);
            console.log(`   Volume: ${totalVolume.toFixed(2)} cm³`);
            console.log(`   Price: $${totalPrice.toFixed(2)}`);

            // Step 6: Save Quote to Database
            await this.updateProgress('Saving quote...', 95);

            try {
                console.log('💾 Saving quote to database...');
                const quoteData = await this.saveQuoteToDatabase(viewer, viewerId, totalVolume, totalPrice);

                if (quoteData && quoteData.success) {
                    console.log('✅ Quote saved successfully:', quoteData.data);
                    console.log('🔗 Viewer Link:', quoteData.data.viewer_link);
                    console.log('📋 Quote Number:', quoteData.data.quote_number);

                    // Show success notification with quote number
                    this.showNotification(
                        `Quote ${quoteData.data.quote_number} saved successfully!<br>View in <a href="${quoteData.data.viewer_link}" target="_blank">viewer</a>`,
                        'success'
                    );
                } else {
                    console.warn('⚠️ Quote save returned non-success:', quoteData);
                }
            } catch (saveError) {
                console.error('❌ Failed to save quote to database:', saveError);
                // Don't fail the whole process if quote save fails
                this.showNotification('Calculation complete, but failed to save to logs', 'warning');
            }

            // Step 7: Complete
            await this.updateProgress('Complete!', 100);

            setTimeout(() => {
                this.hideProgressModal();
                // No results modal - user can see details in the sidebar/form
                console.log('✅ Calculation complete. Results shown in sidebar.');
            }, 500);

            console.log('✅ Enhanced save & calculate complete');

        } catch (error) {
            console.error('❌ Error in save & calculate:', error);
            console.error('Error stack:', error.stack);
            console.error('Error details:', {
                message: error.message,
                name: error.name,
                viewer: !!viewer,
                files: viewer?.uploadedFiles?.length
            });
            this.hideProgressModal();

            // More helpful error message
            let errorMsg = 'Error processing model. ';
            if (!viewer) {
                errorMsg += 'Viewer not loaded.';
            } else if (!viewer.uploadedFiles || viewer.uploadedFiles.length === 0) {
                errorMsg += 'No files uploaded.';
            } else {
                errorMsg += 'Please check console for details.';
            }

            this.showNotification(errorMsg, 'error');
        } finally {
            this.isProcessing = false;
        }
    },

    /**
     * Calculate volume from mesh geometry (fallback method)
     */
    calculateMeshVolume(geometry) {
        if (!geometry || !geometry.attributes || !geometry.attributes.position) {
            console.warn('Invalid geometry for volume calculation');
            return 0;
        }

        const position = geometry.attributes.position;
        const vertices = position.array;
        let volume = 0;

        // Calculate volume using signed volume of triangles
        for (let i = 0; i < vertices.length; i += 9) {
            const v1 = [vertices[i], vertices[i + 1], vertices[i + 2]];
            const v2 = [vertices[i + 3], vertices[i + 4], vertices[i + 5]];
            const v3 = [vertices[i + 6], vertices[i + 7], vertices[i + 8]];

            // Signed volume of tetrahedron formed by origin and triangle
            volume += this.signedVolumeOfTriangle(v1, v2, v3);
        }

        // Convert to cm³ (assuming units are mm)
        const volumeCm3 = Math.abs(volume) / 1000;

        console.log(`Calculated volume: ${volumeCm3.toFixed(2)} cm³`);
        return volumeCm3;
    },

    /**
     * Calculate signed volume of triangle
     */
    signedVolumeOfTriangle(p1, p2, p3) {
        return (p1[0] * p2[1] * p3[2] + p2[0] * p3[1] * p1[2] + p3[0] * p1[1] * p2[2] -
                p1[0] * p3[1] * p2[2] - p2[0] * p1[1] * p3[2] - p3[0] * p2[1] * p1[2]) / 6.0;
    },

    getPricePerCm3(technology, material) {
        // Pricing matrix (adjust based on your business model)
        const pricing = {
            fdm: { pla: 0.5, abs: 0.6, petg: 0.7, nylon: 1.2 },
            sla: { resin: 2.5, 'medical-resin': 4.0 },
            sls: { nylon: 3.5 },
            dmls: { titanium: 15.0, steel: 12.0 },
            mjf: { nylon: 3.0 }
        };

        return pricing[technology]?.[material] || 1.0;
    },

    estimatePrintTime(volume, technology) {
        // Simplified print time estimation
        const speedFactors = {
            fdm: 0.5,  // hours per cm³
            sla: 0.3,
            sls: 0.4,
            dmls: 1.0,
            mjf: 0.35
        };

        const hours = volume * (speedFactors[technology] || 0.5);

        if (hours < 1) {
            return `${Math.ceil(hours * 60)} min`;
        } else {
            return `${hours.toFixed(1)}h`;
        }
    },

    showProgressModal() {
        // Remove existing modal
        document.getElementById('progressModal')?.remove();

        const modal = document.createElement('div');
        modal.id = 'progressModal';
        modal.innerHTML = `
            <div class="progress-modal-overlay">
                <div class="progress-modal-content">
                    <h3>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="spin-icon">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" stroke-dasharray="60" stroke-dashoffset="20"/>
                        </svg>
                        Processing Model
                    </h3>
                    <div class="progress-bar-container">
                        <div class="progress-bar" id="progressBar"></div>
                    </div>
                    <p id="progressText">Initializing...</p>
                </div>
            </div>
            <style>
                .progress-modal-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(0, 0, 0, 0.8);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 10000;
                    backdrop-filter: blur(5px);
                }
                .progress-modal-content {
                    background: white;
                    padding: 40px;
                    border-radius: 16px;
                    min-width: 400px;
                    max-width: 500px;
                    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                }
                .progress-modal-content h3 {
                    margin: 0 0 24px 0;
                    font-size: 24px;
                    color: #2c3e50;
                    display: flex;
                    align-items: center;
                    gap: 12px;
                }
                .spin-icon {
                    animation: spin 1s linear infinite;
                }
                @keyframes spin {
                    from { transform: rotate(0deg); }
                    to { transform: rotate(360deg); }
                }
                .progress-bar-container {
                    background: #f0f0f0;
                    height: 8px;
                    border-radius: 4px;
                    overflow: hidden;
                    margin-bottom: 16px;
                }
                .progress-bar {
                    background: linear-gradient(90deg, #4a90e2, #357abd);
                    height: 100%;
                    width: 0%;
                    transition: width 0.3s ease;
                    border-radius: 4px;
                }
                #progressText {
                    color: #666;
                    font-size: 14px;
                    margin: 0;
                    text-align: center;
                }
            </style>
        `;

        document.body.appendChild(modal);
    },

    updateProgress(text, percent) {
        return new Promise(resolve => {
            const progressBar = document.getElementById('progressBar');
            const progressText = document.getElementById('progressText');

            if (progressBar) progressBar.style.width = `${percent}%`;
            if (progressText) progressText.textContent = text;

            setTimeout(resolve, 300);
        });
    },

    hideProgressModal() {
        document.getElementById('progressModal')?.remove();
    },

    showResultsModal(results) {
        // Remove existing modal
        document.getElementById('resultsModal')?.remove();

        const repairedFiles = results.repairResults.filter(r => r.repaired).length;

        const modal = document.createElement('div');
        modal.id = 'resultsModal';
        modal.innerHTML = `
            <div class="results-modal-overlay">
                <div class="results-modal-content">
                    <div class="results-header">
                        <svg width="48" height="48" viewBox="0 0 48 48" fill="none">
                            <circle cx="24" cy="24" r="22" fill="#4caf50" fill-opacity="0.1"/>
                            <path d="M14 24L20 30L34 16" stroke="#4caf50" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <h2>Processing Complete!</h2>
                    </div>

                    <div class="results-grid">
                        <div class="result-card">
                            <div class="result-icon">📦</div>
                            <div class="result-value">${results.filesProcessed}</div>
                            <div class="result-label">Files Processed</div>
                        </div>
                        <div class="result-card">
                            <div class="result-icon">📐</div>
                            <div class="result-value">${results.totalVolume.toFixed(2)} cm³</div>
                            <div class="result-label">Total Volume</div>
                        </div>
                        <div class="result-card highlight">
                            <div class="result-icon">💰</div>
                            <div class="result-value">$${results.totalPrice.toFixed(2)}</div>
                            <div class="result-label">Estimated Price</div>
                        </div>
                        <div class="result-card">
                            <div class="result-icon">⏱️</div>
                            <div class="result-value">${results.printTime}</div>
                            <div class="result-label">Print Time</div>
                        </div>
                    </div>

                    ${repairedFiles > 0 ? `
                    <div class="repair-info">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M10 4V10L14 12" stroke="#ff9800" stroke-width="2" stroke-linecap="round"/>
                            <circle cx="10" cy="10" r="8" stroke="#ff9800" stroke-width="2"/>
                        </svg>
                        <span><strong>${repairedFiles}</strong> file(s) repaired and optimized</span>
                    </div>
                    ` : ''}

                    <div class="results-actions">
                        <button type="button" class="btn-secondary" onclick="document.getElementById('resultsModal').remove()">
                            Close
                        </button>
                        <button type="button" class="btn-primary" onclick="window.EnhancedSaveCalculate.requestQuote()">
                            Request Quote →
                        </button>
                    </div>
                </div>
            </div>
            <style>
                .results-modal-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(0, 0, 0, 0.8);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 10001;
                    backdrop-filter: blur(5px);
                    animation: fadeIn 0.3s ease;
                }
                @keyframes fadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
                .results-modal-content {
                    background: white;
                    padding: 40px;
                    border-radius: 20px;
                    max-width: 600px;
                    width: 90%;
                    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                    animation: slideUp 0.3s ease;
                }
                @keyframes slideUp {
                    from {
                        transform: translateY(30px);
                        opacity: 0;
                    }
                    to {
                        transform: translateY(0);
                        opacity: 1;
                    }
                }
                .results-header {
                    text-align: center;
                    margin-bottom: 32px;
                }
                .results-header svg {
                    margin-bottom: 16px;
                }
                .results-header h2 {
                    margin: 0;
                    font-size: 28px;
                    color: #2c3e50;
                }
                .results-grid {
                    display: grid;
                    grid-template-columns: repeat(2, 1fr);
                    gap: 16px;
                    margin-bottom: 24px;
                }
                .result-card {
                    background: #f8f9fa;
                    padding: 20px;
                    border-radius: 12px;
                    text-align: center;
                    transition: transform 0.2s;
                }
                .result-card:hover {
                    transform: translateY(-4px);
                }
                .result-card.highlight {
                    background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
                    color: white;
                }
                .result-icon {
                    font-size: 32px;
                    margin-bottom: 8px;
                }
                .result-value {
                    font-size: 24px;
                    font-weight: 700;
                    margin-bottom: 4px;
                }
                .result-card.highlight .result-label {
                    color: rgba(255, 255, 255, 0.9);
                }
                .result-label {
                    font-size: 13px;
                    color: #6c757d;
                    font-weight: 500;
                }
                .repair-info {
                    background: #fff3e0;
                    padding: 16px;
                    border-radius: 8px;
                    display: flex;
                    align-items: center;
                    gap: 12px;
                    margin-bottom: 24px;
                    border-left: 4px solid #ff9800;
                }
                .repair-info span {
                    color: #e65100;
                    font-size: 14px;
                }
                .results-actions {
                    display: flex;
                    gap: 12px;
                    justify-content: flex-end;
                }
                .btn-secondary, .btn-primary {
                    padding: 12px 24px;
                    border-radius: 8px;
                    font-weight: 600;
                    font-size: 15px;
                    cursor: pointer;
                    border: none;
                    transition: all 0.2s;
                }
                .btn-secondary {
                    background: #f0f0f0;
                    color: #666;
                }
                .btn-secondary:hover {
                    background: #e0e0e0;
                }
                .btn-primary {
                    background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
                    color: white;
                    box-shadow: 0 4px 12px rgba(74, 144, 226, 0.3);
                }
                .btn-primary:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 6px 16px rgba(74, 144, 226, 0.4);
                }
                @media (max-width: 768px) {
                    .results-grid {
                        grid-template-columns: 1fr;
                    }
                }
            </style>
        `;

        document.body.appendChild(modal);
    },

    requestQuote() {
        document.getElementById('resultsModal')?.remove();
        // Trigger the quote request form/modal
        const quoteBtn = document.querySelector('#btnRequestQuoteGeneral, #btnRequestQuoteMedical');
        if (quoteBtn) {
            quoteBtn.click();
        }
    },

    showNotification(message, type = 'info') {
        if (window.Utils && window.Utils.showNotification) {
            window.Utils.showNotification(message, type);
        } else {
            alert(message);
        }
    }
};

// Hook into existing Save & Calculate buttons
document.addEventListener('DOMContentLoaded', () => {
    console.log('🔗 Hooking enhanced save & calculate...');

    let handlersAttached = false;

    // Override the save button handler
    const setupEnhancedHandler = () => {
        if (handlersAttached) {
            console.log('⏭️ Handlers already attached, skipping...');
            return;
        }

        const saveBtns = document.querySelectorAll('#saveCalculationsBtn, #saveCalculationsBtnMain');

        if (saveBtns.length === 0) {
            console.log('⚠️ No save buttons found yet');
            return;
        }

        saveBtns.forEach(btn => {
            // Remove any existing listeners by cloning the element
            const newBtn = btn.cloneNode(true);
            btn.parentNode.replaceChild(newBtn, btn);

            newBtn.addEventListener('click', async (e) => {
                e.preventDefault();
                e.stopImmediatePropagation();

                console.log('💾 Save button clicked');

                // Determine which viewer
                const isGeneralVisible = !document.getElementById('generalForm3d')?.style.display ||
                                        document.getElementById('generalForm3d')?.style.display !== 'none';
                const viewerId = isGeneralVisible ? 'general' : 'medical';

                console.log('📍 Active viewer:', viewerId);

                await window.EnhancedSaveCalculate.execute(viewerId);
            });
        });

        handlersAttached = true;
        console.log(`✅ Enhanced handler attached to ${saveBtns.length} button(s)`);
    };

    // Setup after a delay to ensure DOM is ready
    setTimeout(setupEnhancedHandler, 1500);

    // Also setup after viewers are ready, but only if not already done
    window.addEventListener('viewersReady', () => {
        setTimeout(setupEnhancedHandler, 500);
    });
});

console.log('✅ Enhanced Save & Calculate System loaded');
