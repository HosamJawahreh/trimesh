/**
 * ========================================
 * ENHANCED SAVE & CALCULATE
 * With Auto Mesh Analysis and Repair
 * ========================================
 */

console.log('💾 Loading Enhanced Save & Calculate System...');

window.EnhancedSaveCalculate = {
    isProcessing: false,
    
    async execute(viewerId = 'general') {
        if (this.isProcessing) {
            console.warn('⚠️ Already processing...');
            return;
        }

        this.isProcessing = true;
        const viewer = viewerId === 'general' ? window.viewerGeneral : window.viewerMedical;
        
        if (!viewer || !viewer.uploadedFiles || viewer.uploadedFiles.length === 0) {
            this.showNotification('Please upload a 3D model first', 'warning');
            this.isProcessing = false;
            return;
        }

        try {
            console.log('🚀 Starting enhanced save & calculate...');
            this.showProgressModal();
            
            // Step 1: Analyze all meshes
            await this.updateProgress('Analyzing meshes...', 20);
            const analysisResults = [];
            
            for (const fileData of viewer.uploadedFiles) {
                if (fileData.mesh && viewer.tools && viewer.tools.analyzer) {
                    const analysis = await viewer.tools.analyzer.analyzeMesh(fileData.mesh);
                    analysisResults.push({ fileName: fileData.fileName, analysis });
                    
                    // Show analysis
                    viewer.tools.analyzer.showAnalysisPanel(analysis);
                }
            }
            
            // Step 2: Repair meshes if needed
            await this.updateProgress('Repairing meshes...', 40);
            const repairResults = [];
            
            for (const fileData of viewer.uploadedFiles) {
                if (fileData.mesh) {
                    const needsRepair = analysisResults.find(r => 
                        r.fileName === fileData.fileName && 
                        (!r.analysis.isWatertight || r.analysis.holes > 0)
                    );
                    
                    if (needsRepair && viewer.tools && viewer.tools.analyzer) {
                        console.log(`🔧 Repairing: ${fileData.fileName}`);
                        await viewer.tools.analyzer.repairMesh(fileData.mesh);
                        repairResults.push({ 
                            fileName: fileData.fileName, 
                            repaired: true,
                            holes: needsRepair.analysis.holes
                        });
                    }
                }
            }
            
            // Step 3: Recalculate volumes
            await this.updateProgress('Calculating volumes...', 60);
            let totalVolume = 0;
            
            for (const fileData of viewer.uploadedFiles) {
                if (fileData.mesh && viewer.calculateVolume) {
                    const volume = viewer.calculateVolume(fileData.mesh);
                    fileData.volume = volume;
                    totalVolume += volume;
                    console.log(`📐 Volume (${fileData.fileName}): ${volume.toFixed(2)} cm³`);
                }
            }
            
            // Step 4: Calculate pricing
            await this.updateProgress('Calculating pricing...', 80);
            
            // Get selected technology and material
            const technology = document.getElementById(`technologySelect${viewerId === 'general' ? 'General' : 'Medical'}`)?.value || 'fdm';
            const material = document.getElementById(`materialSelect${viewerId === 'general' ? 'General' : 'Medical'}`)?.value || 'pla';
            
            // Calculate price (simplified formula - adjust based on your pricing model)
            const pricePerCm3 = this.getPricePerCm3(technology, material);
            const totalPrice = totalVolume * pricePerCm3;
            const printTime = this.estimatePrintTime(totalVolume, technology);
            
            // Step 5: Update UI
            await this.updateProgress('Updating interface...', 95);
            
            // Update volume display
            const volumeDisplay = document.getElementById(`quoteTotalVolume${viewerId === 'general' ? 'General' : 'Medical'}`);
            if (volumeDisplay) {
                volumeDisplay.textContent = `${totalVolume.toFixed(2)} cm³`;
                volumeDisplay.style.display = 'block';
            }
            
            // Update price display
            const priceDisplay = document.getElementById(`quoteTotalPrice${viewerId === 'general' ? 'General' : 'Medical'}`);
            if (priceDisplay) {
                priceDisplay.textContent = `$${totalPrice.toFixed(2)}`;
                priceDisplay.style.display = 'block';
            }
            
            // Update print time
            const timeDisplay = document.getElementById(`quotePrintTime${viewerId === 'general' ? 'General' : 'Medical'}`);
            if (timeDisplay) {
                timeDisplay.textContent = printTime;
            }
            
            // Show price summary
            const priceSummary = document.getElementById(`priceSummary${viewerId === 'general' ? 'General' : 'Medical'}`);
            if (priceSummary) {
                priceSummary.style.display = 'block';
            }
            
            // Step 6: Show results
            await this.updateProgress('Complete!', 100);
            
            setTimeout(() => {
                this.hideProgressModal();
                this.showResultsModal({
                    totalVolume,
                    totalPrice,
                    printTime,
                    filesProcessed: viewer.uploadedFiles.length,
                    analysisResults,
                    repairResults
                });
            }, 500);
            
            console.log('✅ Enhanced save & calculate complete');
            
        } catch (error) {
            console.error('❌ Error in save & calculate:', error);
            this.hideProgressModal();
            this.showNotification('Error processing model. Please try again.', 'error');
        } finally {
            this.isProcessing = false;
        }
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
    
    // Override the save button handler
    const setupEnhancedHandler = () => {
        const saveBtns = document.querySelectorAll('#saveCalculationsBtn, #saveCalculationsBtnMain');
        
        saveBtns.forEach(btn => {
            btn.addEventListener('click', async (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                // Determine which viewer
                const viewerId = btn.closest('[id*="General"]') || btn.closest('.quote-form-container-3d:not([style*="display: none"])') 
                    ? 'general' 
                    : 'medical';
                
                await window.EnhancedSaveCalculate.execute(viewerId);
            }, true); // Use capture phase to override other handlers
        });
        
        console.log(`✅ Enhanced handler attached to ${saveBtns.length} button(s)`);
    };
    
    // Setup immediately and after viewer ready
    setTimeout(setupEnhancedHandler, 1000);
    window.addEventListener('viewersReady', setupEnhancedHandler);
});

console.log('✅ Enhanced Save & Calculate System loaded');
