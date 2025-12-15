<!-- 3D Quote Section -->
<section class="dgm-3d-quote-area pt-120 pb-120 black-bg-5">
    <div class="container container-1230">
        <!-- Section Header -->
        <div class="row mb-60">
            <div class="col-12 text-center">
                <span class="tp-section-subtitle subtitle-grey mb-15 text-white">
                    ✨ Instant 3D Printing Quote
                </span>
                <h2 class="tp-section-title-grotesk text-white mb-20">
                    Upload. Analyze. Quote.
                </h2>
                <p class="text-white" style="font-size: 1.1rem; max-width: 600px; margin: 0 auto; opacity: 0.8;">
                    Get instant pricing for your 3D models in seconds
                </p>
            </div>
        </div>

        <!-- Main Quote Form -->
        <div class="row g-4">
            <!-- Left Column - Upload & Files -->
            <div class="col-lg-5">
                <!-- Upload Card -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; background: rgba(255,255,255,0.05);">
                    <div class="card-header border-0 p-4" style="border-radius: 16px 16px 0 0; background: rgba(255,255,255,0.03);">
                        <div class="d-flex align-items-center">
                            <div class="icon-box-home me-3">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M21 15V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M17 8L12 3L7 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 3V15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div>
                                <h5 class="mb-0 text-white" style="font-weight: 600;">Upload 3D Files</h5>
                                <small class="text-white" style="opacity: 0.7;">STL, OBJ, PLY - Max 50MB</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <!-- Drop Zone -->
                        <div class="upload-drop-zone-home text-center" id="homeDropZone" style="cursor: pointer;" onclick="document.getElementById('homeFileInput').click();">
                            <input type="file" id="homeFileInput" style="display: none;" multiple accept=".stl,.obj,.ply" onchange="console.log('File selected:', this.files.length);">
                            <div class="mb-3">
                                <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="32" cy="32" r="32" fill="#e3f2fd"/>
                                    <path d="M32 20L40 28L32 36L24 28L32 20Z" stroke="#2196f3" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M24 36L32 44L40 36" stroke="#2196f3" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <h6 class="mb-2 text-white" style="font-weight: 600;">Drop files here</h6>
                            <p class="text-white" style="font-size: 14px; opacity: 0.7;">or click anywhere in this area</p>
                            <button type="button" class="btn btn-primary" id="homeBtnBrowse" onclick="event.stopPropagation(); document.getElementById('homeFileInput').click(); return false;">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-2">
                                    <path d="M14 10V12.6667C14 13.0203 13.8595 13.3594 13.6095 13.6095C13.3594 13.8595 13.0203 14 12.6667 14H3.33333C2.97971 14 2.64057 13.8595 2.39052 13.6095C2.14048 13.3594 2 13.0203 2 12.6667V10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M11.3333 5.33333L8 2L4.66667 5.33333" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M8 2V10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Select Files
                            </button>
                        </div>

                        <!-- Material Selection -->
                        <div class="mt-4">
                            <label class="form-label text-white fw-semibold">Default Material</label>
                            <select id="homeDefaultMaterial" class="form-select" style="border-radius: 10px;">
                                <option value="">Loading materials...</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Files List -->
                <div class="card border-0 shadow-sm" style="border-radius: 16px; background: rgba(255,255,255,0.05);">
                    <div class="card-header border-0 p-4 d-flex justify-content-between align-items-center" style="border-radius: 16px 16px 0 0; background: rgba(255,255,255,0.03);">
                        <h6 class="mb-0 text-white" style="font-weight: 600;">Uploaded Files</h6>
                        <span id="homeFileCount" class="badge bg-primary">0</span>
                    </div>
                    <div class="card-body p-0">
                        <div id="homeFilesList" class="files-list-home">
                            <div class="empty-state-home text-center p-5" id="homeEmptyState">
                                <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" class="mb-3">
                                    <circle cx="24" cy="24" r="24" fill="#f5f5f5"/>
                                    <path d="M24 16V32M16 24H32" stroke="#bdbdbd" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                                <p class="mb-0 text-muted">No files yet</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Viewer & Pricing -->
            <div class="col-lg-7">
                <!-- 3D Viewer -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; background: rgba(255,255,255,0.05);">
                    <div class="card-header border-0 p-4 d-flex justify-content-between align-items-center" style="border-radius: 16px 16px 0 0; background: rgba(255,255,255,0.03);">
                        <h6 class="mb-0 text-white" style="font-weight: 600;">3D Preview</h6>
                        <div class="viewer-controls-home">
                            <button class="btn btn-sm btn-outline-secondary" id="homeBtnResetView" title="Reset View">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1.33333 8H14.6667M14.6667 8L10 3.33333M14.6667 8L10 12.6667" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary ms-2" id="homeBtnToggleWireframe" title="Wireframe">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="2" y="2" width="12" height="12" rx="1" stroke="currentColor" stroke-width="1.5"/>
                                    <path d="M2 6H14M2 10H14M6 2V14M10 2V14" stroke="currentColor" stroke-width="1.5"/>
                                </svg>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary ms-2" id="homeBtnToggleRotate" title="Auto Rotate">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M14 8C14 11.3137 11.3137 14 8 14C4.68629 14 2 11.3137 2 8C2 4.68629 4.68629 2 8 2C9.84597 2 11.4857 2.88572 12.5 4.24998M12.5 4.24998V2M12.5 4.24998H10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div id="homeViewer3d" style="width: 100%; height: 400px; background: #fafafa; border-radius: 0 0 16px 16px;"></div>
                    </div>
                </div>

                <!-- Model Info -->
                <div class="card border-0 shadow-sm mb-4 d-none" style="border-radius: 16px; background: rgba(255,255,255,0.05);" id="homeModelInfo">
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-6 col-md-3">
                                <div class="text-center">
                                    <small class="text-white d-block mb-1" style="opacity: 0.7;">Volume</small>
                                    <strong id="homeInfoVolume" class="text-white">-</strong>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="text-center">
                                    <small class="text-white d-block mb-1" style="opacity: 0.7;">Width</small>
                                    <strong id="homeInfoWidth" class="text-white">-</strong>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="text-center">
                                    <small class="text-white d-block mb-1" style="opacity: 0.7;">Height</small>
                                    <strong id="homeInfoHeight" class="text-white">-</strong>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="text-center">
                                    <small class="text-white d-block mb-1" style="opacity: 0.7;">Depth</small>
                                    <strong id="homeInfoDepth" class="text-white">-</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Price Summary -->
                <div class="card border-0 shadow-sm" style="border-radius: 16px; background: rgba(255,255,255,0.05);">
                    <div class="card-header border-0 p-4" style="border-radius: 16px 16px 0 0; background: rgba(255,255,255,0.03);">
                        <h6 class="mb-0 text-white" style="font-weight: 600;">Price Summary</h6>
                    </div>
                    <div class="card-body p-4">
                        <div id="homePriceSummary">
                            <div class="empty-state-home text-center py-4">
                                <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" class="mb-3">
                                    <circle cx="24" cy="24" r="24" fill="#e3f2fd"/>
                                    <path d="M24 16V32M16 24H32" stroke="#2196f3" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                                <p class="mb-0 text-muted">Upload files to see pricing</p>
                            </div>
                        </div>

                        <!-- Total Price Display -->
                        <div id="homeTotalPrice" class="d-none">
                            <div class="total-price-box-home p-4 mb-4" style="background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%); border-radius: 12px;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0" style="color: #1a1a1a; font-weight: 600;">Total Price</h5>
                                    <h3 class="mb-0" id="homeTotalAmount" style="color: #2196f3; font-weight: 700;">$0.00</h3>
                                </div>
                            </div>

                            <!-- Quick Contact Form -->
                            <div class="contact-form-mini">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="homeCustomerName" placeholder="Your Name" style="border-radius: 10px;">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="email" class="form-control" id="homeCustomerEmail" placeholder="Email Address" style="border-radius: 10px;">
                                    </div>
                                    <div class="col-12">
                                        <input type="tel" class="form-control" id="homeCustomerPhone" placeholder="Phone Number" style="border-radius: 10px;">
                                    </div>
                                    <div class="col-12">
                                        <textarea class="form-control" id="homeCustomerNotes" rows="2" placeholder="Additional notes..." style="border-radius: 10px;"></textarea>
                                    </div>
                                    <div class="col-12">
                                        <button type="button" class="btn btn-primary w-100" id="homeBtnSubmitQuote" style="border-radius: 10px; padding: 12px;">
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-2">
                                                <path d="M18.3333 1.66663L9.16667 10.8333M18.3333 1.66663L12.5 18.3333L9.16667 10.8333M18.3333 1.66663L1.66667 7.49996L9.16667 10.8333" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            Get Instant Quote
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Success Modal -->
<div class="modal fade" id="homeSuccessModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-body text-center p-5">
                <div class="success-icon mb-4">
                    <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="40" cy="40" r="40" fill="url(#paint0_linear_success)" fill-opacity="0.2"/>
                        <path d="M25 40L35 50L55 30" stroke="url(#paint1_linear_success)" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                        <defs>
                            <linearGradient id="paint0_linear_success" x1="0" y1="0" x2="80" y2="80" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#3498db"/>
                                <stop offset="1" stop-color="#8e44ad"/>
                            </linearGradient>
                            <linearGradient id="paint1_linear_success" x1="25" y1="30" x2="55" y2="50" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#3498db"/>
                                <stop offset="1" stop-color="#8e44ad"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
                <h3 class="mb-3">Quote Submitted!</h3>
                <p class="text-muted mb-3">Your quote has been successfully submitted</p>
                <div class="quote-number-box mb-4">
                    <small class="text-muted d-block mb-1">Quote Number</small>
                    <strong id="homeQuoteNumber" style="font-size: 1.5rem; color: #1f2937;">-</strong>
                </div>
                <button type="button" class="btn-gradient" onclick="window.location.reload()">
                    Create New Quote
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Modern Card Styles */
.quote-card {
    background: #ffffff;
    border: 2px solid #e5e7eb;
    border-radius: 20px;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.quote-card:hover {
    border-color: #3498db;
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(52, 152, 219, 0.15);
}

.quote-card-header {
    padding: 1.5rem;
    border-bottom: 2px solid #f3f4f6;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
}

.quote-card-header h5, .quote-card-header h6 {
    color: #1a1a1a;
    margin: 0;
    font-weight: 700;
}

.quote-card-header small {
    color: #666;
    font-weight: 500;
}

.quote-card-body {
    padding: 2rem;
}

/* Icon Box */
.icon-box {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: linear-gradient(135deg, #3498db 0%, #8e44ad 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
}

/* Upload Drop Zone */
.upload-drop-zone {
    border: 3px dashed #3498db;
    border-radius: 16px;
    padding: 3rem 2rem;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
    background: linear-gradient(135deg, rgba(52, 152, 219, 0.05) 0%, rgba(142, 68, 173, 0.05) 100%);
}

.upload-drop-zone:hover, .upload-drop-zone.dragover {
    border-color: #8e44ad;
    background: linear-gradient(135deg, rgba(52, 152, 219, 0.1) 0%, rgba(142, 68, 173, 0.1) 100%);
    transform: scale(1.02);
}

.upload-drop-zone h6 {
    color: #1a1a1a;
    font-weight: 700;
    font-size: 18px;
}

.upload-drop-zone p {
    color: #666;
    font-size: 15px;
}

/* Modern Form Controls */
.form-label-modern {
    color: #1a1a1a;
    font-size: 15px;
    font-weight: 700;
    margin-bottom: 0.75rem;
    display: block;
}

.form-select-modern, .form-control-modern {
    background: #ffffff;
    border: 2px solid #d1d5db;
    border-radius: 12px;
    padding: 0.875rem 1.125rem;
    color: #1a1a1a;
    font-size: 15px;
    font-weight: 500;
    transition: all 0.3s ease;
    width: 100%;
}

.form-select-modern:focus, .form-control-modern:focus {
    background: #ffffff;
    border-color: #3498db;
    outline: none;
    box-shadow: 0 0 0 4px rgba(52, 152, 219, 0.15);
}

.form-control-modern::placeholder {
    color: #9ca3af;
    font-weight: 400;
}

/* Gradient Buttons */
.btn-gradient, .btn-gradient-sm {
    background: linear-gradient(135deg, #3498db 0%, #8e44ad 100%);
    border: none;
    color: white;
    padding: 1rem 2.5rem;
    border-radius: 12px;
    font-weight: 700;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
}

.btn-gradient-sm {
    padding: 0.75rem 1.75rem;
    font-size: 15px;
}

.btn-gradient:hover, .btn-gradient-sm:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(52, 152, 219, 0.4);
}

/* Icon Buttons */
.btn-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: #ffffff;
    border: 2px solid #e5e7eb;
    color: #1a1a1a;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-left: 0.5rem;
}

.btn-icon:hover {
    background: linear-gradient(135deg, #3498db 0%, #8e44ad 100%);
    border-color: #3498db;
    color: #ffffff;
    transform: scale(1.1);
}

/* Viewer Container */
.viewer-container {
    width: 100%;
    height: 450px;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    border-radius: 0 0 20px 20px;
    position: relative;
    border: 3px solid #e5e7eb;
    border-top: none;
}

/* Files List */
.files-list {
    max-height: 400px;
    overflow-y: auto;
}

.files-list::-webkit-scrollbar {
    width: 8px;
}

.files-list::-webkit-scrollbar-track {
    background: #f3f4f6;
}

.files-list::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #3498db 0%, #8e44ad 100%);
    border-radius: 4px;
}

.file-item-home {
    padding: 1.25rem;
    border-bottom: 2px solid #f3f4f6;
    transition: all 0.2s ease;
    background: #ffffff;
}

.file-item-home:hover {
    background: #f8f9fa;
}

.file-item-home:last-child {
    border-bottom: none;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 2rem;
    color: #666;
}

.empty-state svg {
    color: #3498db;
}

/* Info Box */
.info-box {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 12px;
    border: 2px solid #e5e7eb;
}

.info-box strong {
    color: #1a1a1a;
    font-size: 17px;
    font-weight: 700;
}

.info-box small {
    color: #666;
    font-weight: 600;
}

/* Total Price Box */
.total-price-box {
    padding: 2rem;
    background: linear-gradient(135deg, rgba(52, 152, 219, 0.1) 0%, rgba(142, 68, 173, 0.1) 100%);
    border-radius: 16px;
    border: 3px solid #3498db;
}

.total-price-box h5 {
    color: #1a1a1a;
    font-weight: 700;
}

.price-amount {
    background: linear-gradient(135deg, #3498db 0%, #8e44ad 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: 900;
    font-size: 2.5rem !important;
}

/* Badge Styles */
.badge {
    padding: 0.5rem 1rem;
    border-radius: 10px;
    font-weight: 700;
    font-size: 13px;
}

/* Background Decorations - Removed for clean look */
.bg-decoration-1, .bg-decoration-2, .bg-decoration-3 {
    display: none;
}

/* Responsive */
@media (max-width: 991px) {
    .viewer-container {
        height: 350px;
    }
}
</style>

@push('scripts')
<!-- Three.js and Loaders -->
<script src="https://cdn.jsdelivr.net/npm/three@0.160.0/build/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.160.0/examples/js/controls/OrbitControls.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.160.0/examples/js/loaders/STLLoader.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.160.0/examples/js/loaders/OBJLoader.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.160.0/examples/js/loaders/PLYLoader.js"></script>

<!-- Model Viewer -->
<script src="{{ asset('frontend/assets/js/model-viewer-3d.js') }}"></script>

<!-- Quote Manager -->
<script src="{{ asset('frontend/assets/js/quote-manager.js') }}"></script>

<!-- Home Quote App -->
<script>
// Initialize Home Quote System
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing quote system...');
    
    // Check if required scripts are loaded
    console.log('Checking dependencies:', {
        ModelViewer3D: typeof ModelViewer3D,
        QuoteManager: typeof QuoteManager,
        THREE: typeof THREE
    });
    
    if (typeof ModelViewer3D === 'undefined') {
        console.error('ModelViewer3D not loaded - check if model-viewer-3d.js is included');
    }
    
    if (typeof QuoteManager === 'undefined') {
        console.error('QuoteManager not loaded - check if quote-manager.js is included');
    }
    
    if (typeof THREE === 'undefined') {
        console.error('THREE.js not loaded - check if three.js is included');
    }

    // Simplified initialization - work even without 3D viewer
    let homeViewer3D = null;
    let homeQuoteManager = null;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    console.log('CSRF Token:', csrfToken ? 'Found' : 'Missing');

    // Try to initialize 3D Viewer
    const viewerContainer = document.getElementById('homeViewer3d');
    if (viewerContainer && typeof ModelViewer3D !== 'undefined') {
        try {
            homeViewer3D = new ModelViewer3D(viewerContainer, {
                backgroundColor: 0xfafafa,
                modelColor: 0x2196f3,
                autoRotate: false
            });
            console.log('3D Viewer initialized');
        } catch (error) {
            console.error('Failed to initialize 3D Viewer:', error);
        }
    }

    // Try to initialize Quote Manager
    if (typeof QuoteManager !== 'undefined') {
        try {
            homeQuoteManager = new QuoteManager({
                csrfToken: csrfToken,
                onFileAdded: handleFileAdded,
                onFileAnalyzed: handleFileAnalyzed,
                onFileRemoved: handleFileRemoved,
                onTotalUpdated: handleTotalUpdated,
                onError: (msg) => {
                    console.error('Quote Manager Error:', msg);
                    alert(msg);
                }
            });
            console.log('Quote Manager initialized');
            
            // Load materials
            loadMaterials();
        } catch (error) {
            console.error('Failed to initialize Quote Manager:', error);
        }
    }

    // Always setup event listeners for basic file input
    setupEventListeners();

    function setupEventListeners() {
        const fileInput = document.getElementById('homeFileInput');
        const dropZone = document.getElementById('homeDropZone');
        const browseBtn = document.getElementById('homeBtnBrowse');

        console.log('Setting up event listeners...');
        console.log('Elements found:', {
            fileInput: !!fileInput,
            dropZone: !!dropZone,
            browseBtn: !!browseBtn
        });

        if (!fileInput) {
            console.error('File input not found!');
            return;
        }

        // File input change event
        fileInput.addEventListener('change', function(e) {
            console.log('File input changed, files:', e.target.files.length);
            handleFileSelect(e);
        });

        // Browse button click
        if (browseBtn) {
            browseBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Browse button clicked, opening file dialog...');
                fileInput.click();
            });
            console.log('Browse button listener attached');
        } else {
            console.error('Browse button not found!');
        }

        // Drop zone events
        if (dropZone) {
            dropZone.addEventListener('click', function(e) {
                // Don't trigger if clicking the button
                if (!e.target.closest('#homeBtnBrowse')) {
                    console.log('Drop zone clicked');
                    fileInput.click();
                }
            });

            dropZone.addEventListener('dragover', function(e) {
                e.preventDefault();
                dropZone.classList.add('dragover');
                console.log('Drag over');
            });
            
            dropZone.addEventListener('dragleave', function() {
                dropZone.classList.remove('dragover');
            });
            
            dropZone.addEventListener('drop', function(e) {
                console.log('Files dropped');
                handleDrop(e);
            });
            
            console.log('Drop zone listeners attached');
        } else {
            console.error('Drop zone not found!');
        }

        // Viewer controls
        document.getElementById('homeBtnResetView')?.addEventListener('click', () => homeViewer3D?.resetView());
        document.getElementById('homeBtnToggleWireframe')?.addEventListener('click', () => homeViewer3D?.toggleWireframe());
        document.getElementById('homeBtnToggleRotate')?.addEventListener('click', () => homeViewer3D?.toggleAutoRotate());

        // Material selector
        document.getElementById('homeDefaultMaterial')?.addEventListener('change', (e) => {
            homeQuoteManager.setDefaultMaterial(e.target.value);
        });

        // Submit button
        document.getElementById('homeBtnSubmitQuote')?.addEventListener('click', handleSubmit);
    }

    async function loadMaterials() {
        if (!homeQuoteManager) {
            console.error('Quote Manager not initialized');
            return;
        }
        
        try {
            console.log('Loading materials...');
            const materials = await homeQuoteManager.loadMaterials();
            const select = document.getElementById('homeDefaultMaterial');

            if (select && materials.length > 0) {
                select.innerHTML = materials.map(mat =>
                    `<option value="${mat.material}">${mat.display_name} - $${mat.price_per_cm3}/cm³</option>`
                ).join('');
                homeQuoteManager.setDefaultMaterial(materials[0].material);
                console.log('Materials loaded:', materials.length);
            }
        } catch (error) {
            console.error('Failed to load materials:', error);
        }
    }

    async function handleFileSelect(e) {
        console.log('handleFileSelect called');
        const files = Array.from(e.target.files);
        console.log('Files selected:', files.length);
        
        if (files.length === 0) {
            console.log('No files selected');
            return;
        }
        
        if (!homeQuoteManager) {
            alert('Quote system not fully loaded. Please refresh the page and try again.');
            console.error('Quote Manager not available');
            return;
        }
        
        for (const file of files) {
            console.log('Processing file:', file.name, file.type, file.size);
            await processFile(file);
        }
        e.target.value = '';
    }

    async function handleDrop(e) {
        e.preventDefault();
        e.currentTarget.classList.remove('dragover');
        const files = Array.from(e.dataTransfer.files);
        for (const file of files) {
            await processFile(file);
        }
    }

    async function processFile(file) {
        try {
            const fileData = await homeQuoteManager.uploadFile(file);
            if (!fileData) return;

            const geometryData = await homeViewer3D.loadModel(file);
            if (geometryData) {
                await homeQuoteManager.analyzeFile(fileData.id, geometryData);
                displayModelInfo(geometryData);
            }
        } catch (error) {
            console.error('File processing error:', error);
            alert('Failed to process file: ' + error.message);
        }
    }

    function handleFileAdded(fileData) {
        addFileToList(fileData);
        updateFileCount();
        document.getElementById('homeEmptyState')?.classList.add('d-none');
    }

    function handleFileAnalyzed(fileData) {
        updateFileInList(fileData);
    }

    function handleFileRemoved(fileData) {
        document.getElementById(`home-file-${fileData.id}`)?.remove();
        updateFileCount();

        if (homeQuoteManager.getFiles().length === 0) {
            document.getElementById('homeEmptyState')?.classList.remove('d-none');
            homeViewer3D?.clear();
            document.getElementById('homeModelInfo')?.classList.add('d-none');
            document.getElementById('homeTotalPrice')?.classList.add('d-none');
        }
    }

    function handleTotalUpdated(total, count) {
        document.getElementById('homeTotalAmount').textContent = `$${total.toFixed(2)}`;
        document.getElementById('homeTotalPrice').classList.toggle('d-none', count === 0);
    }

    function addFileToList(fileData) {
        const filesList = document.getElementById('homeFilesList');
        const fileItem = document.createElement('div');
        fileItem.className = 'file-item-home';
        fileItem.id = `home-file-${fileData.id}`;
        fileItem.innerHTML = `
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center mb-2">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-2" style="color: #3498db;">
                            <path d="M10 4L14 8L10 12L6 8L10 4Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6 12L10 16L14 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <strong class="text-dark" style="font-size: 14px;">${fileData.name}</strong>
                    </div>
                    <span class="badge bg-secondary" style="font-size: 11px;">Analyzing...</span>
                </div>
                <button class="btn-icon btn-remove-home" data-file-id="${fileData.id}" style="margin: 0;">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 4L4 12M4 4L12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>
            <div class="file-pricing-home mt-2 d-none">
                <div class="row g-2">
                    <div class="col-8">
                        <select class="form-select-modern material-select-home" data-file-id="${fileData.id}" style="font-size: 12px; padding: 0.5rem;">
                            <option>Loading...</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <input type="number" class="form-control-modern quantity-input-home" data-file-id="${fileData.id}" value="1" min="1" style="font-size: 12px; padding: 0.5rem;">
                    </div>
                    <div class="col-12">
                        <strong class="text-primary file-price-home" style="font-size: 14px;">$0.00</strong>
                    </div>
                </div>
            </div>
        `;
        filesList.appendChild(fileItem);

        fileItem.querySelector('.btn-remove-home').addEventListener('click', (e) => {
            if (confirm('Remove this file?')) {
                homeQuoteManager.removeFile(parseInt(e.currentTarget.dataset.fileId));
            }
        });

        loadFileMaterials(fileData.id);
    }

    function updateFileInList(fileData) {
        const fileItem = document.getElementById(`home-file-${fileData.id}`);
        if (!fileItem) return;

        fileItem.querySelector('.badge').className = 'badge bg-success';
        fileItem.querySelector('.badge').textContent = 'Ready';
        fileItem.querySelector('.file-pricing-home').classList.remove('d-none');

        if (fileData.pricing) {
            fileItem.querySelector('.file-price-home').textContent = `$${fileData.pricing.total_price.toFixed(2)}`;
        }

        const materialSelect = fileItem.querySelector('.material-select-home');
        if (materialSelect) {
            materialSelect.value = fileData.material;
            materialSelect.addEventListener('change', async (e) => {
                const qty = parseInt(fileItem.querySelector('.quantity-input-home').value);
                await homeQuoteManager.updateFileMaterial(fileData.id, e.target.value, qty);
            });
        }

        const qtyInput = fileItem.querySelector('.quantity-input-home');
        if (qtyInput) {
            qtyInput.addEventListener('change', async (e) => {
                const mat = fileItem.querySelector('.material-select-home').value;
                await homeQuoteManager.updateFileMaterial(fileData.id, mat, parseInt(e.target.value));
            });
        }
    }

    async function loadFileMaterials(fileId) {
        const materials = homeQuoteManager.materials;
        const fileItem = document.getElementById(`home-file-${fileId}`);
        const select = fileItem?.querySelector('.material-select-home');

        if (select && materials.length > 0) {
            select.innerHTML = materials.map(mat =>
                `<option value="${mat.material}">${mat.display_name}</option>`
            ).join('');
            select.value = homeQuoteManager.currentMaterial;
        }
    }

    function displayModelInfo(data) {
        const card = document.getElementById('homeModelInfo');
        card?.classList.remove('d-none');
        document.getElementById('homeInfoVolume').textContent = `${data.volume_cm3.toFixed(2)} cm³`;
        document.getElementById('homeInfoWidth').textContent = `${data.width_mm.toFixed(1)} mm`;
        document.getElementById('homeInfoHeight').textContent = `${data.height_mm.toFixed(1)} mm`;
        document.getElementById('homeInfoDepth').textContent = `${data.depth_mm.toFixed(1)} mm`;
    }

    function updateFileCount() {
        const count = homeQuoteManager.getFiles().length;
        document.getElementById('homeFileCount').textContent = count;
    }

    async function handleSubmit() {
        const result = await homeQuoteManager.submitQuote({
            customer_name: document.getElementById('homeCustomerName')?.value,
            customer_email: document.getElementById('homeCustomerEmail')?.value,
            customer_phone: document.getElementById('homeCustomerPhone')?.value,
            customer_notes: document.getElementById('homeCustomerNotes')?.value
        });

        if (result) {
            document.getElementById('homeQuoteNumber').textContent = result.quote_number;
            const modal = new bootstrap.Modal(document.getElementById('homeSuccessModal'));
            modal.show();
        }
    }
});
</script>
@endpush
