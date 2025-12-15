@extends('frontend.layouts.master')

@section('meta_title', 'Instant 3D Printing Quote')
@section('meta_description', 'Upload your 3D models and get instant price quotes for 3D printing')

@section('header')
   @include('frontend.layouts.headers.header-1', ['main_menu' => $main_menu ?? []])
@endsection

@section('content')
<div class="quote-system-wrapper py-5">
    <div class="container">
        <!-- Page Header -->
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h1 class="display-4 fw-bold mb-3">Instant 3D Printing Quote</h1>
                <p class="lead text-muted">Upload your 3D models and get real-time pricing</p>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Column - File Upload & List -->
            <div class="col-lg-5">
                <!-- File Upload Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-cloud-upload me-2"></i>Upload 3D Models</h5>
                    </div>
                    <div class="card-body">
                        <!-- Drag & Drop Zone -->
                        <div id="dropZone" class="border-2 border-dashed rounded-3 p-5 text-center bg-light position-relative" style="border-color: #dee2e6 !important; cursor: pointer;">
                            <input type="file" id="fileInput" class="d-none" multiple accept=".stl,.obj,.ply">
                            <i class="bi bi-file-earmark-arrow-up display-3 text-primary mb-3"></i>
                            <h5>Drag & Drop Files Here</h5>
                            <p class="text-muted mb-2">or click to browse</p>
                            <small class="text-muted">Supported: STL, OBJ, PLY (Max 50MB each)</small>
                        </div>

                        <!-- Upload Progress -->
                        <div id="uploadProgress" class="mt-3 d-none">
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Files List -->
                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-files me-2"></i>Uploaded Files</h5>
                        <span id="fileCount" class="badge bg-light text-dark">0 files</span>
                    </div>
                    <div class="card-body p-0">
                        <div id="filesList" class="list-group list-group-flush">
                            <div class="list-group-item text-center text-muted py-4" id="emptyState">
                                <i class="bi bi-inbox display-4 mb-2"></i>
                                <p>No files uploaded yet</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Material & Quantity Selector -->
                <div class="card shadow-sm mt-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="bi bi-palette me-2"></i>Default Material</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Material</label>
                            <select id="defaultMaterial" class="form-select">
                                <option value="">Loading materials...</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - 3D Viewer & Pricing -->
            <div class="col-lg-7">
                <!-- 3D Viewer Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-box me-2"></i>3D Preview</h5>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-light" id="btnResetView" title="Reset View">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </button>
                            <button type="button" class="btn btn-outline-light" id="btnToggleWireframe" title="Toggle Wireframe">
                                <i class="bi bi-grid-3x3"></i>
                            </button>
                            <button type="button" class="btn btn-outline-light" id="btnToggleRotate" title="Auto Rotate">
                                <i class="bi bi-arrow-repeat"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div id="viewer3d" style="width: 100%; height: 500px; background: #1a1a1a;"></div>
                    </div>
                </div>

                <!-- Model Info Card -->
                <div class="card shadow-sm mb-4 d-none" id="modelInfoCard">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Model Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6 col-md-3">
                                <small class="text-muted d-block">Volume</small>
                                <strong id="infoVolume">-</strong>
                            </div>
                            <div class="col-6 col-md-3">
                                <small class="text-muted d-block">Width</small>
                                <strong id="infoWidth">-</strong>
                            </div>
                            <div class="col-6 col-md-3">
                                <small class="text-muted d-block">Height</small>
                                <strong id="infoHeight">-</strong>
                            </div>
                            <div class="col-6 col-md-3">
                                <small class="text-muted d-block">Depth</small>
                                <strong id="infoDepth">-</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Price Summary Card -->
                <div class="card shadow-sm border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-calculator me-2"></i>Price Summary</h5>
                    </div>
                    <div class="card-body">
                        <div id="priceSummary">
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-currency-dollar display-4 mb-2"></i>
                                <p>Upload files to see pricing</p>
                            </div>
                        </div>
                        
                        <div id="totalPrice" class="d-none">
                            <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-3">
                                <h4 class="mb-0">Total:</h4>
                                <h3 class="mb-0 text-primary" id="totalAmount">$0.00</h3>
                            </div>
                            
                            <!-- Customer Info Form -->
                            <div class="mt-4">
                                <h6 class="mb-3">Contact Information (Optional)</h6>
                                <div class="mb-3">
                                    <input type="text" class="form-control" id="customerName" placeholder="Your Name">
                                </div>
                                <div class="mb-3">
                                    <input type="email" class="form-control" id="customerEmail" placeholder="Your Email">
                                </div>
                                <div class="mb-3">
                                    <input type="tel" class="form-control" id="customerPhone" placeholder="Your Phone">
                                </div>
                                <div class="mb-3">
                                    <textarea class="form-control" id="customerNotes" rows="3" placeholder="Additional notes or requirements"></textarea>
                                </div>
                                <button type="button" class="btn btn-primary btn-lg w-100" id="btnSubmitQuote">
                                    <i class="bi bi-send me-2"></i>Submit Quote Request
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-check-circle me-2"></i>Quote Submitted</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <i class="bi bi-check-circle-fill text-success display-1 mb-3"></i>
                <h4>Thank You!</h4>
                <p class="mb-0">Your quote has been submitted successfully.</p>
                <p class="text-muted">Quote Number: <strong id="quoteNumber"></strong></p>
                <p class="text-muted">We'll get back to you soon!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="window.location.reload()">Create New Quote</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer')
    @include('frontend.layouts.footers.footer-1', [
        'footer_menu_one' => $footer_menu_one ?? [],
        'footer_menu_two' => $footer_menu_two ?? [],
    ])
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<style>
#dropZone.dragover {
    background-color: #e7f3ff !important;
    border-color: #0d6efd !important;
}

.file-item {
    transition: all 0.2s;
}

.file-item:hover {
    background-color: #f8f9fa;
}

.viewer-loading {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 1.2rem;
}
</style>
@endpush

@push('scripts')
<!-- Three.js and Loaders -->
<script src="https://cdn.jsdelivr.net/npm/three@0.160.0/build/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.160.0/examples/js/controls/OrbitControls.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.160.0/examples/js/loaders/STLLoader.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.160.0/examples/js/loaders/OBJLoader.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.160.0/examples/js/loaders/PLYLoader.js"></script>

<!-- Custom Scripts -->
<script src="{{ asset('frontend/assets/js/model-viewer-3d.js') }}"></script>
<script src="{{ asset('frontend/assets/js/quote-manager.js') }}"></script>
<script src="{{ asset('frontend/assets/js/quote-app.js') }}"></script>
@endpush
