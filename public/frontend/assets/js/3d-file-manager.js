/**
 * 3D File Manager - Multi-file upload and pricing UI
 * Manages file list display and instant quote calculations
 */

console.log('📋 Loading File Manager...');

class FileManager {
    constructor(formType, viewer) {
        this.formType = formType; // 'General' or 'Medical'
        this.viewer = viewer;
        this.filesListId = `filesList${formType}`;
        this.fileCountId = `fileCount${formType}`;
        this.quoteIds = {
            totalFiles: `quoteTotalFiles${formType}`,
            totalVolume: `quoteTotalVolume${formType}`,
            materialCost: `quoteMaterialCost${formType}`,
            printTime: `quotePrintTime${formType}`,
            totalPrice: `quoteTotalPrice${formType}`
        };
    }

    updateFilesList() {
        const filesList = document.getElementById(this.filesListId);
        const fileCount = document.getElementById(this.fileCountId);

        if (!filesList || !fileCount) {
            console.warn('File list elements not found');
            return;
        }

        const files = this.viewer.getUploadedFiles();
        fileCount.textContent = files.length;

        if (files.length === 0) {
            filesList.innerHTML = `
                <div class="empty-state text-center p-5">
                    <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" class="mb-3" style="opacity: 0.3;">
                        <circle cx="24" cy="24" r="24" fill="#e0e0e0"/>
                        <path d="M24 14V34M14 24H34" stroke="#9e9e9e" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <p class="mb-0 text-muted">No files uploaded yet</p>
                </div>
            `;
            return;
        }

        filesList.innerHTML = files.map((fileData, index) => `
            <div class="file-item d-flex align-items-center justify-content-between p-3 border-bottom" style="transition: all 0.2s;" data-file-id="${fileData.id}">
                <div class="d-flex align-items-center flex-grow-1">
                    <div class="file-icon me-3" style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                        ${this.getFileExtension(fileData.file.name).toUpperCase()}
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold text-truncate" style="max-width: 200px;">${fileData.file.name}</div>
                        <small class="text-muted">
                            ${this.formatFileSize(fileData.file.size)} •
                            Volume: ${fileData.volume?.cm3?.toFixed(2) || 0} cm³
                        </small>
                    </div>
                </div>
                <button class="btn btn-sm btn-outline-danger remove-file-btn" data-file-id="${fileData.id}" style="border-radius: 6px;">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                    </svg>
                </button>
            </div>
        `).join('');

        // Attach remove handlers
        this.attachRemoveHandlers();
    }

    attachRemoveHandlers() {
        const removeButtons = document.querySelectorAll(`#${this.filesListId} .remove-file-btn`);
        removeButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const fileId = parseFloat(btn.getAttribute('data-file-id'));
                this.removeFile(fileId);
            });
        });
    }

    removeFile(fileId) {
        this.viewer.removeFile(fileId);
        this.updateFilesList();
        this.updateQuote();

        // Show notification
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #dc3545;
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            z-index: 9999;
            animation: slideIn 0.3s ease-out;
        `;
        notification.textContent = '✓ File removed';
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => notification.remove(), 300);
        }, 2000);
    }

    updateQuote() {
        console.log(`🎯 [${this.formType}] updateQuote() called`);
        console.log(`   Viewer uploadedFiles:`, this.viewer.uploadedFiles);

        // Get material and quality selections
        let material = 'pla';
        let quality = 'standard';

        if (this.formType === 'General') {
            const materialSelect = document.getElementById('materialSelectGeneral');
            const qualitySelect = document.getElementById('qualitySelectGeneral');
            material = materialSelect?.value || 'pla';
            quality = qualitySelect?.value || 'standard';
            console.log(`   General selects found: material=${!!materialSelect}, quality=${!!qualitySelect}`);
        } else if (this.formType === 'Medical') {
            const materialSelect = document.getElementById('materialSelectMedical');
            const qualitySelect = document.getElementById('qualitySelectMedical');
            material = materialSelect?.value || 'medical-resin';
            quality = qualitySelect?.value || 'high';
            console.log(`   Medical selects found: material=${!!materialSelect}, quality=${!!qualitySelect}`);
        }

        console.log(`   Material: ${material}, Quality: ${quality}`);

        const pricing = this.viewer.calculatePrice(material, quality);

        console.log(`📊 Pricing result:`, JSON.stringify(pricing, null, 2));

        // Update UI elements with safety checks
        const totalVolumeEl = document.getElementById(this.quoteIds.totalVolume);
        const printTimeEl = document.getElementById(this.quoteIds.printTime);
        const totalPriceEl = document.getElementById(this.quoteIds.totalPrice);

        console.log(`   Quote element IDs: totalVolume=${this.quoteIds.totalVolume}, printTime=${this.quoteIds.printTime}, totalPrice=${this.quoteIds.totalPrice}`);
        console.log(`   Elements found:`, {
            totalVolume: !!totalVolumeEl,
            printTime: !!printTimeEl,
            totalPrice: !!totalPriceEl
        });

        if (totalVolumeEl) {
            const volumeText = `${pricing.totalVolume.toFixed(2)} cm³`;
            totalVolumeEl.textContent = volumeText;
            console.log(`   ✅ Updated volume: ${volumeText}`);
        } else {
            console.warn(`   ⚠️ Volume element not found: #${this.quoteIds.totalVolume}`);
        }

        if (printTimeEl) {
            const timeText = `${pricing.printTime.toFixed(1)}h`;
            printTimeEl.textContent = timeText;
            console.log(`   ✅ Updated print time: ${timeText}`);
        } else {
            console.warn(`   ⚠️ Print time element not found: #${this.quoteIds.printTime}`);
        }

        if (totalPriceEl) {
            const priceText = `$${pricing.totalPrice.toFixed(2)}`;
            totalPriceEl.textContent = priceText;
            console.log(`   ✅ Updated total price: ${priceText}`);
        } else {
            console.warn(`   ⚠️ Total price element not found: #${this.quoteIds.totalPrice}`);
        }

        console.log(`✅ [${this.formType}] Quote update complete`);
    }

    getFileExtension(filename) {
        return filename.split('.').pop() || 'file';
    }

    formatFileSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(2) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }
}

console.log('📦 File Manager script loaded, waiting for viewersReady event...');

// Initialize file managers when viewers are ready
window.addEventListener('viewersReady', () => {
    console.log('🎉 viewersReady event received!');
    console.log('   window.viewerGeneral:', !!window.viewerGeneral);
    console.log('   window.viewerMedical:', !!window.viewerMedical);

    if (window.viewerGeneral) {
        window.fileManagerGeneral = new FileManager('General', window.viewerGeneral);
        console.log('✅ General file manager created');
        console.log('   window.fileManagerGeneral:', !!window.fileManagerGeneral);

        // Update UI on file upload - wrap the loadFile method
        const originalLoadFile = window.viewerGeneral.loadFile.bind(window.viewerGeneral);
        window.viewerGeneral.loadFile = async function(file) {
            console.log('📥 File manager intercepting loadFile for:', file.name);
            console.log('   Uploaded files before:', window.viewerGeneral.uploadedFiles.length);
            const result = await originalLoadFile(file);
            console.log('   Uploaded files after:', window.viewerGeneral.uploadedFiles.length);
            console.log('   All uploaded files:', window.viewerGeneral.getUploadedFiles());
            console.log('✓ File loaded, updating UI...');

            // Small delay to ensure everything is ready
            setTimeout(() => {
                window.fileManagerGeneral.updateFilesList();
                window.fileManagerGeneral.updateQuote();
                console.log('✅ UI update complete');
            }, 100);

            return result;
        };

        // Listen to material/quality/infill changes
        console.log('🎛️ Attaching change listeners for General controls...');
        const materialSelectGeneral = document.getElementById('materialSelectGeneral');
        const qualitySelectGeneral = document.getElementById('qualitySelectGeneral');
        const infillSelectGeneral = document.getElementById('infillSelectGeneral');

        console.log('   materialSelectGeneral:', !!materialSelectGeneral);
        console.log('   qualitySelectGeneral:', !!qualitySelectGeneral);
        console.log('   infillSelectGeneral:', !!infillSelectGeneral);

        if (materialSelectGeneral) {
            materialSelectGeneral.addEventListener('change', () => {
                console.log('💰 General material changed to:', materialSelectGeneral.value);
                if (window.fileManagerGeneral) {
                    window.fileManagerGeneral.updateQuote();
                } else {
                    console.error('fileManagerGeneral not available!');
                }
            });
            console.log('   ✓ Material change listener attached');
        } else {
            console.warn('   ⚠️ materialSelectGeneral not found!');
        }

        if (qualitySelectGeneral) {
            qualitySelectGeneral.addEventListener('change', () => {
                console.log('💰 General quality changed to:', qualitySelectGeneral.value);
                if (window.fileManagerGeneral) {
                    window.fileManagerGeneral.updateQuote();
                } else {
                    console.error('fileManagerGeneral not available!');
                }
            });
            console.log('   ✓ Quality change listener attached');
        } else {
            console.warn('   ⚠️ qualitySelectGeneral not found!');
        }

        if (infillSelectGeneral) {
            infillSelectGeneral.addEventListener('change', () => {
                console.log('💰 General infill changed to:', infillSelectGeneral.value);
                if (window.fileManagerGeneral) {
                    window.fileManagerGeneral.updateQuote();
                } else {
                    console.error('fileManagerGeneral not available!');
                }
            });
            console.log('   ✓ Infill change listener attached');
        } else {
            console.warn('   ⚠️ infillSelectGeneral not found!');
        }

        // Request quote button
        const btnRequestQuoteGeneral = document.getElementById('btnRequestQuoteGeneral');
        if (btnRequestQuoteGeneral) {
            btnRequestQuoteGeneral.addEventListener('click', () => {
                const files = window.viewerGeneral.getUploadedFiles();
                if (files.length === 0) {
                    alert('Please upload at least one file to request a quote.');
                    return;
                }

                // Here you can implement the actual quote request logic
                // For now, just show a success message
                alert(`Quote request submitted!\n\nFiles: ${files.length}\nTotal Volume: ${window.viewerGeneral.getTotalVolume().toFixed(2)} cm³\n\nWe'll contact you shortly with a detailed quote.`);
            });
        }
    }

    if (window.viewerMedical) {
        window.fileManagerMedical = new FileManager('Medical', window.viewerMedical);
        console.log('✓ Medical file manager ready');

        // Update UI on file upload - wrap the loadFile method
        const originalLoadFileMedical = window.viewerMedical.loadFile.bind(window.viewerMedical);
        window.viewerMedical.loadFile = async function(file) {
            console.log('📥 Medical file manager intercepting loadFile for:', file.name);
            console.log('   Uploaded files before:', window.viewerMedical.uploadedFiles.length);
            const result = await originalLoadFileMedical(file);
            console.log('   Uploaded files after:', window.viewerMedical.uploadedFiles.length);
            console.log('   All uploaded files:', window.viewerMedical.getUploadedFiles());
            console.log('✓ Medical file loaded, updating UI...');

            // Small delay to ensure everything is ready
            setTimeout(() => {
                window.fileManagerMedical.updateFilesList();
                window.fileManagerMedical.updateQuote();
                console.log('✅ Medical UI update complete');
            }, 100);

            return result;
        };

        // Listen to material/quality/application changes
        console.log('🎛️ Attaching change listeners for Medical controls...');
        const materialSelectMedical = document.getElementById('materialSelectMedical');
        const qualitySelectMedical = document.getElementById('qualitySelectMedical');
        const applicationSelectMedical = document.getElementById('applicationSelectMedical');

        console.log('   materialSelectMedical:', !!materialSelectMedical);
        console.log('   qualitySelectMedical:', !!qualitySelectMedical);
        console.log('   applicationSelectMedical:', !!applicationSelectMedical);

        if (materialSelectMedical) {
            materialSelectMedical.addEventListener('change', () => {
                console.log('💰 Medical material changed to:', materialSelectMedical.value);
                if (window.fileManagerMedical) {
                    window.fileManagerMedical.updateQuote();
                } else {
                    console.error('fileManagerMedical not available!');
                }
            });
            console.log('   ✓ Material change listener attached');
        } else {
            console.warn('   ⚠️ materialSelectMedical not found!');
        }

        if (qualitySelectMedical) {
            qualitySelectMedical.addEventListener('change', () => {
                console.log('💰 Medical quality changed to:', qualitySelectMedical.value);
                if (window.fileManagerMedical) {
                    window.fileManagerMedical.updateQuote();
                } else {
                    console.error('fileManagerMedical not available!');
                }
            });
            console.log('   ✓ Quality change listener attached');
        } else {
            console.warn('   ⚠️ qualitySelectMedical not found!');
        }

        if (applicationSelectMedical) {
            applicationSelectMedical.addEventListener('change', () => {
                console.log('💰 Medical application changed to:', applicationSelectMedical.value);
                if (window.fileManagerMedical) {
                    window.fileManagerMedical.updateQuote();
                } else {
                    console.error('fileManagerMedical not available!');
                }
            });
            console.log('   ✓ Application change listener attached');
        } else {
            console.warn('   ⚠️ applicationSelectMedical not found!');
        }

        // Request quote button
        const btnRequestQuoteMedical = document.getElementById('btnRequestQuoteMedical');
        if (btnRequestQuoteMedical) {
            btnRequestQuoteMedical.addEventListener('click', () => {
                const files = window.viewerMedical.getUploadedFiles();
                if (files.length === 0) {
                    alert('Please upload at least one file to request a quote.');
                    return;
                }

                alert(`Medical Quote request submitted!\n\nFiles: ${files.length}\nTotal Volume: ${window.viewerMedical.getTotalVolume().toFixed(2)} cm³\n\nOur medical specialists will review your files and contact you shortly.`);
            });
        }
    }
});

// Listen for direct pricing update events from viewers
window.addEventListener('pricingUpdateNeeded', (event) => {
    console.log('🎯 Pricing update event received:', event.detail);

    const { viewerId, fileCount, totalVolume } = event.detail;

    console.log('   Checking file managers...');
    console.log('   window.fileManagerGeneral:', !!window.fileManagerGeneral);
    console.log('   window.fileManagerMedical:', !!window.fileManagerMedical);

    if (viewerId === 'viewer3dGeneral') {
        if (window.fileManagerGeneral) {
            console.log('→ Updating General pricing via event');
            window.fileManagerGeneral.updateFilesList();
            window.fileManagerGeneral.updateQuote();
        } else {
            console.error('❌ fileManagerGeneral NOT FOUND!');
            console.log('   Trying to create it now...');
            if (window.viewerGeneral) {
                window.fileManagerGeneral = new FileManager('General', window.viewerGeneral);
                console.log('   ✓ Created fileManagerGeneral');
                window.fileManagerGeneral.updateFilesList();
                window.fileManagerGeneral.updateQuote();
            }
        }
    } else if (viewerId === 'viewer3dMedical') {
        if (window.fileManagerMedical) {
            console.log('→ Updating Medical pricing via event');
            window.fileManagerMedical.updateFilesList();
            window.fileManagerMedical.updateQuote();
        } else {
            console.error('❌ fileManagerMedical NOT FOUND!');
            console.log('   Trying to create it now...');
            if (window.viewerMedical) {
                window.fileManagerMedical = new FileManager('Medical', window.viewerMedical);
                console.log('   ✓ Created fileManagerMedical');
                window.fileManagerMedical.updateFilesList();
                window.fileManagerMedical.updateQuote();
            }
        }
    } else {
        console.warn('⚠️ Unknown viewer ID:', viewerId);
    }
});

console.log('✓ File Manager loaded');
console.log('✓ Pricing update event listener attached');

