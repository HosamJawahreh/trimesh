/**
 * Main Quote Application
 * Handles UI interactions and coordinates between viewer and quote manager
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize components
    let viewer3D = null;
    let quoteManager = null;
    let currentFileData = null;

    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    // Initialize viewer
    const viewerContainer = document.getElementById('viewer3d');
    if (viewerContainer) {
        viewer3D = new ModelViewer3D(viewerContainer, {
            backgroundColor: 0x1a1a1a,
            modelColor: 0x3498db,
            autoRotate: false
        });
    }

    // Initialize quote manager
    quoteManager = new QuoteManager({
        csrfToken: csrfToken,
        onFileAdded: handleFileAdded,
        onFileAnalyzed: handleFileAnalyzed,
        onFileRemoved: handleFileRemoved,
        onTotalUpdated: handleTotalUpdated,
        onError: handleError
    });

    // Load materials into dropdown
    loadMaterialsDropdown();

    // Setup event listeners
    setupEventListeners();

    /**
     * Setup all event listeners
     */
    function setupEventListeners() {
        // File input
        const fileInput = document.getElementById('fileInput');
        const dropZone = document.getElementById('dropZone');

        if (fileInput) {
            fileInput.addEventListener('change', handleFileSelect);
        }

        if (dropZone) {
            dropZone.addEventListener('click', () => fileInput?.click());
            dropZone.addEventListener('dragover', handleDragOver);
            dropZone.addEventListener('dragleave', handleDragLeave);
            dropZone.addEventListener('drop', handleDrop);
        }

        // Viewer controls
        document.getElementById('btnResetView')?.addEventListener('click', () => {
            viewer3D?.resetView();
        });

        document.getElementById('btnToggleWireframe')?.addEventListener('click', () => {
            viewer3D?.toggleWireframe();
        });

        document.getElementById('btnToggleRotate')?.addEventListener('click', () => {
            viewer3D?.toggleAutoRotate();
        });

        // Default material change
        document.getElementById('defaultMaterial')?.addEventListener('change', (e) => {
            quoteManager.setDefaultMaterial(e.target.value);
        });

        // Submit quote
        document.getElementById('btnSubmitQuote')?.addEventListener('click', handleSubmitQuote);
    }

    /**
     * Load materials into dropdown
     */
    async function loadMaterialsDropdown() {
        try {
            const materials = await quoteManager.loadMaterials();
            const select = document.getElementById('defaultMaterial');
            
            if (select && materials.length > 0) {
                select.innerHTML = materials.map(mat => 
                    `<option value="${mat.material}">${mat.display_name} - $${mat.price_per_cm3}/cm³</option>`
                ).join('');
                
                quoteManager.setDefaultMaterial(materials[0].material);
            }
        } catch (error) {
            console.error('Failed to load materials:', error);
        }
    }

    /**
     * Handle file selection
     */
    async function handleFileSelect(e) {
        const files = Array.from(e.target.files);
        await processFiles(files);
        e.target.value = ''; // Reset input
    }

    /**
     * Handle drag over
     */
    function handleDragOver(e) {
        e.preventDefault();
        e.stopPropagation();
        e.currentTarget.classList.add('dragover');
    }

    /**
     * Handle drag leave
     */
    function handleDragLeave(e) {
        e.preventDefault();
        e.stopPropagation();
        e.currentTarget.classList.remove('dragover');
    }

    /**
     * Handle file drop
     */
    async function handleDrop(e) {
        e.preventDefault();
        e.stopPropagation();
        e.currentTarget.classList.remove('dragover');

        const files = Array.from(e.dataTransfer.files);
        await processFiles(files);
    }

    /**
     * Process multiple files
     */
    async function processFiles(files) {
        for (const file of files) {
            await processFile(file);
        }
    }

    /**
     * Process single file
     */
    async function processFile(file) {
        try {
            // Show upload progress
            showProgress(true);

            // Upload file
            const fileData = await quoteManager.uploadFile(file);
            
            if (!fileData) {
                showProgress(false);
                return;
            }

            // Load and analyze in 3D viewer
            const geometryData = await viewer3D.loadModel(file);
            
            if (geometryData) {
                // Send analysis data to backend
                await quoteManager.analyzeFile(fileData.id, geometryData);
                
                // Show model info
                displayModelInfo(geometryData);
            }

            showProgress(false);

        } catch (error) {
            console.error('File processing error:', error);
            handleError('Failed to process file: ' + error.message);
            showProgress(false);
        }
    }

    /**
     * Handle file added to quote
     */
    function handleFileAdded(fileData) {
        addFileToList(fileData);
        updateFileCount();
        hideEmptyState();
    }

    /**
     * Handle file analyzed
     */
    function handleFileAnalyzed(fileData) {
        updateFileInList(fileData);
        currentFileData = fileData;
    }

    /**
     * Handle file removed
     */
    function handleFileRemoved(fileData) {
        removeFileFromList(fileData.id);
        updateFileCount();
        
        if (quoteManager.getFiles().length === 0) {
            showEmptyState();
            viewer3D?.clear();
            hideModelInfo();
            document.getElementById('totalPrice')?.classList.add('d-none');
        }
    }

    /**
     * Handle total updated
     */
    function handleTotalUpdated(total, fileCount) {
        const totalElement = document.getElementById('totalAmount');
        const priceContainer = document.getElementById('totalPrice');
        
        if (totalElement) {
            totalElement.textContent = `$${total.toFixed(2)}`;
        }

        if (priceContainer) {
            if (fileCount > 0) {
                priceContainer.classList.remove('d-none');
            } else {
                priceContainer.classList.add('d-none');
            }
        }
    }

    /**
     * Handle errors
     */
    function handleError(message) {
        // You can integrate with your preferred notification system
        alert(message);
    }

    /**
     * Add file to list
     */
    function addFileToList(fileData) {
        const filesList = document.getElementById('filesList');
        
        const fileItem = document.createElement('div');
        fileItem.className = 'list-group-item file-item';
        fileItem.id = `file-${fileData.id}`;
        fileItem.innerHTML = `
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-file-earmark-3d me-2 text-primary"></i>
                        <strong class="file-name">${fileData.name}</strong>
                    </div>
                    <div class="file-details small text-muted">
                        <span class="status-badge badge bg-secondary">Analyzing...</span>
                    </div>
                    <div class="file-pricing mt-2 d-none">
                        <div class="row g-2 small">
                            <div class="col-6">
                                <label class="form-label mb-1">Material</label>
                                <select class="form-select form-select-sm material-select" data-file-id="${fileData.id}">
                                    <option value="">Loading...</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label mb-1">Quantity</label>
                                <input type="number" class="form-control form-control-sm quantity-input" data-file-id="${fileData.id}" value="1" min="1" max="1000">
                            </div>
                        </div>
                        <div class="mt-2">
                            <strong class="text-primary">Price: <span class="file-price">$0.00</span></strong>
                        </div>
                    </div>
                </div>
                <button class="btn btn-sm btn-outline-danger ms-2 btn-remove" data-file-id="${fileData.id}">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;

        filesList.appendChild(fileItem);

        // Add event listeners
        fileItem.querySelector('.btn-remove')?.addEventListener('click', (e) => {
            const fileId = parseInt(e.currentTarget.dataset.fileId);
            if (confirm('Remove this file?')) {
                quoteManager.removeFile(fileId);
            }
        });

        // Load materials into file's dropdown
        loadFileMaterialsDropdown(fileData.id);
    }

    /**
     * Update file in list after analysis
     */
    function updateFileInList(fileData) {
        const fileItem = document.getElementById(`file-${fileData.id}`);
        if (!fileItem) return;

        const statusBadge = fileItem.querySelector('.status-badge');
        const pricingSection = fileItem.querySelector('.file-pricing');
        const priceSpan = fileItem.querySelector('.file-price');

        if (statusBadge) {
            statusBadge.className = 'status-badge badge bg-success';
            statusBadge.textContent = 'Ready';
        }

        if (pricingSection) {
            pricingSection.classList.remove('d-none');
        }

        if (priceSpan && fileData.pricing) {
            priceSpan.textContent = `$${fileData.pricing.total_price.toFixed(2)}`;
        }

        // Setup material change handler
        const materialSelect = fileItem.querySelector('.material-select');
        if (materialSelect) {
            materialSelect.value = fileData.material;
            materialSelect.addEventListener('change', async (e) => {
                const fileId = parseInt(e.currentTarget.dataset.fileId);
                const quantityInput = fileItem.querySelector('.quantity-input');
                await quoteManager.updateFileMaterial(fileId, e.target.value, parseInt(quantityInput.value));
            });
        }

        // Setup quantity change handler
        const quantityInput = fileItem.querySelector('.quantity-input');
        if (quantityInput) {
            quantityInput.addEventListener('change', async (e) => {
                const fileId = parseInt(e.currentTarget.dataset.fileId);
                const materialSelect = fileItem.querySelector('.material-select');
                await quoteManager.updateFileMaterial(fileId, materialSelect.value, parseInt(e.target.value));
            });
        }
    }

    /**
     * Load materials for file dropdown
     */
    async function loadFileMaterialsDropdown(fileId) {
        const materials = quoteManager.materials;
        const fileItem = document.getElementById(`file-${fileId}`);
        const select = fileItem?.querySelector('.material-select');
        
        if (select && materials.length > 0) {
            select.innerHTML = materials.map(mat => 
                `<option value="${mat.material}">${mat.display_name}</option>`
            ).join('');
            select.value = quoteManager.currentMaterial;
        }
    }

    /**
     * Remove file from list
     */
    function removeFileFromList(fileId) {
        const fileItem = document.getElementById(`file-${fileId}`);
        if (fileItem) {
            fileItem.remove();
        }
    }

    /**
     * Display model information
     */
    function displayModelInfo(geometryData) {
        const infoCard = document.getElementById('modelInfoCard');
        if (!infoCard) return;

        infoCard.classList.remove('d-none');

        document.getElementById('infoVolume').textContent = `${geometryData.volume_cm3.toFixed(2)} cm³`;
        document.getElementById('infoWidth').textContent = `${geometryData.width_mm.toFixed(1)} mm`;
        document.getElementById('infoHeight').textContent = `${geometryData.height_mm.toFixed(1)} mm`;
        document.getElementById('infoDepth').textContent = `${geometryData.depth_mm.toFixed(1)} mm`;
    }

    /**
     * Hide model information
     */
    function hideModelInfo() {
        document.getElementById('modelInfoCard')?.classList.add('d-none');
    }

    /**
     * Update file count
     */
    function updateFileCount() {
        const count = quoteManager.getFiles().length;
        const badge = document.getElementById('fileCount');
        if (badge) {
            badge.textContent = `${count} file${count !== 1 ? 's' : ''}`;
        }
    }

    /**
     * Show/hide empty state
     */
    function hideEmptyState() {
        document.getElementById('emptyState')?.classList.add('d-none');
    }

    function showEmptyState() {
        const emptyState = document.getElementById('emptyState');
        if (emptyState) {
            emptyState.classList.remove('d-none');
        }
    }

    /**
     * Show/hide progress
     */
    function showProgress(show) {
        const progress = document.getElementById('uploadProgress');
        if (progress) {
            if (show) {
                progress.classList.remove('d-none');
            } else {
                progress.classList.add('d-none');
            }
        }
    }

    /**
     * Handle quote submission
     */
    async function handleSubmitQuote() {
        const customerInfo = {
            customer_name: document.getElementById('customerName')?.value,
            customer_email: document.getElementById('customerEmail')?.value,
            customer_phone: document.getElementById('customerPhone')?.value,
            customer_notes: document.getElementById('customerNotes')?.value
        };

        const result = await quoteManager.submitQuote(customerInfo);

        if (result) {
            // Show success modal
            document.getElementById('quoteNumber').textContent = result.quote_number;
            const modal = new bootstrap.Modal(document.getElementById('successModal'));
            modal.show();
        }
    }
});
