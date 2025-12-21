/**
 * ========================================
 * SIMPLE & ACCURATE SAVE & CALCULATE
 * No mesh repair - just accurate calculation
 * ========================================
 */

console.log('💾 Loading Simple Save & Calculate System...');

window.SimpleSaveCalculate = {
    isProcessing: false,

    async execute(viewerId = 'general') {
        if (this.isProcessing) {
            console.warn('⚠️ Already processing...');
            return;
        }

        this.isProcessing = true;
        const viewer = viewerId === 'general' ? window.viewerGeneral : window.viewerMedical;

        console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        console.log('🚀 SAVE & CALCULATE STARTED');
        console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        try {
            // Validate viewer
            if (!viewer || !viewer.uploadedFiles || viewer.uploadedFiles.length === 0) {
                throw new Error('No 3D model uploaded. Please upload a file first.');
            }

            console.log(`✅ Viewer validated: ${viewer.uploadedFiles.length} file(s) uploaded`);

            // Show progress
            this.showProgress('Calculating volume...', 30);

            // Step 1: Calculate Volume using dedicated calculator
            const volumeResult = window.VolumeCalculator.calculateTotalVolume(viewer);
            
            if (volumeResult.cm3 === 0) {
                throw new Error('Could not calculate volume. The model may be invalid.');
            }

            console.log(`✅ Volume calculated: ${volumeResult.cm3.toFixed(2)} cm³`);

            // Step 2: Get selected technology and material
            this.showProgress('Reading selections...', 50);

            const viewerSuffix = viewerId === 'general' ? 'General' : 'Medical';
            const techSelect = document.getElementById(`technologySelect${viewerSuffix}`);
            const matSelect = document.getElementById(`materialSelect${viewerSuffix}`);

            const technology = techSelect?.value || 'fdm';
            const material = matSelect?.value || 'pla';

            console.log(`✅ Technology: ${technology}, Material: ${material}`);

            // Step 3: Calculate pricing
            this.showProgress('Calculating price...', 70);

            const pricingResult = window.PricingCalculator.calculatePrice(
                volumeResult.cm3,
                technology,
                material
            );

            const printTime = window.PricingCalculator.estimatePrintTime(
                volumeResult.cm3,
                technology
            );

            console.log(`✅ Price calculated: $${pricingResult.totalPrice.toFixed(2)}`);
            console.log(`✅ Print time: ${printTime}`);

            // Step 4: Update UI
            this.showProgress('Updating display...', 90);

            this.updateUI(viewerSuffix, {
                volume: volumeResult.cm3,
                price: pricingResult.totalPrice,
                printTime: printTime
            });

            console.log(`✅ UI updated successfully`);

            // Step 5: Complete
            this.showProgress('Complete!', 100);

            setTimeout(() => {
                this.hideProgress();
                
                // Show success notification
                if (window.showToolbarNotification) {
                    showToolbarNotification(
                        `✅ Calculation complete! Volume: ${volumeResult.cm3.toFixed(2)} cm³, Price: $${pricingResult.totalPrice.toFixed(2)}`,
                        'success',
                        5000
                    );
                }
            }, 500);

            console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            console.log('✅ SAVE & CALCULATE COMPLETED SUCCESSFULLY');
            console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        } catch (error) {
            console.error('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            console.error('❌ SAVE & CALCULATE FAILED');
            console.error('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            console.error('Error:', error.message);
            console.error('Stack:', error.stack);

            this.hideProgress();

            if (window.showToolbarNotification) {
                showToolbarNotification(
                    `❌ ${error.message}`,
                    'error',
                    5000
                );
            } else {
                alert(`Error: ${error.message}`);
            }
        } finally {
            this.isProcessing = false;
        }
    },

    /**
     * Update all UI elements with calculated values
     */
    updateUI(viewerSuffix, data) {
        console.log(`🎨 Updating UI for ${viewerSuffix}...`);

        // Update ALL volume displays
        const volumeText = `${data.volume.toFixed(2)} cm³`;
        const volumeSelectors = [
            `#quoteTotalVolume${viewerSuffix}`,
            `.quote-volume-${viewerSuffix.toLowerCase()}`
        ];

        let volumeUpdated = 0;
        volumeSelectors.forEach(selector => {
            const elements = document.querySelectorAll(selector);
            elements.forEach(el => {
                el.textContent = volumeText;
                el.style.display = 'block';
                volumeUpdated++;
            });
        });

        console.log(`   ✅ Updated ${volumeUpdated} volume displays`);

        // Update ALL price displays
        const priceText = `$${data.price.toFixed(2)}`;
        const priceSelectors = [
            `#quoteTotalPrice${viewerSuffix}`,
            `.quote-price-${viewerSuffix.toLowerCase()}`
        ];

        let priceUpdated = 0;
        priceSelectors.forEach(selector => {
            const elements = document.querySelectorAll(selector);
            elements.forEach(el => {
                el.textContent = priceText;
                el.style.display = 'block';
                priceUpdated++;
            });
        });

        console.log(`   ✅ Updated ${priceUpdated} price displays`);

        // Update print time
        const timeSelectors = [
            `#quotePrintTime${viewerSuffix}`,
            `.quote-time-${viewerSuffix.toLowerCase()}`
        ];

        let timeUpdated = 0;
        timeSelectors.forEach(selector => {
            const elements = document.querySelectorAll(selector);
            elements.forEach(el => {
                el.textContent = data.printTime;
                timeUpdated++;
            });
        });

        console.log(`   ✅ Updated ${timeUpdated} time displays`);

        // Show price summary section
        const priceSummary = document.getElementById(`priceSummary${viewerSuffix}`);
        if (priceSummary) {
            priceSummary.style.display = 'block';
        }

        console.log(`✅ UI update complete`);
    },

    /**
     * Show progress modal
     */
    showProgress(message, percent) {
        let modal = document.getElementById('saveCalculateProgress');
        
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'saveCalculateProgress';
            modal.innerHTML = `
                <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.8); display: flex; align-items: center; justify-content: center; z-index: 10000;">
                    <div style="background: white; padding: 40px; border-radius: 16px; min-width: 400px; text-align: center;">
                        <h3 style="margin: 0 0 20px 0; font-size: 24px; color: #2c3e50;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" style="display: inline-block; vertical-align: middle; animation: spin 1s linear infinite;">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" stroke-dasharray="60" stroke-dashoffset="20"/>
                            </svg>
                            Processing
                        </h3>
                        <div style="background: #f0f0f0; height: 8px; border-radius: 4px; overflow: hidden; margin-bottom: 16px;">
                            <div id="progressBar" style="background: linear-gradient(90deg, #4a90e2, #357abd); height: 100%; width: 0%; transition: width 0.3s;"></div>
                        </div>
                        <p id="progressText" style="margin: 0; color: #666;">Initializing...</p>
                    </div>
                </div>
                <style>
                    @keyframes spin {
                        from { transform: rotate(0deg); }
                        to { transform: rotate(360deg); }
                    }
                </style>
            `;
            document.body.appendChild(modal);
        }

        const progressBar = modal.querySelector('#progressBar');
        const progressText = modal.querySelector('#progressText');

        if (progressBar) progressBar.style.width = `${percent}%`;
        if (progressText) progressText.textContent = message;
    },

    /**
     * Hide progress modal
     */
    hideProgress() {
        const modal = document.getElementById('saveCalculateProgress');
        if (modal) {
            modal.remove();
        }
    }
};

console.log('✅ Simple Save & Calculate System loaded');
