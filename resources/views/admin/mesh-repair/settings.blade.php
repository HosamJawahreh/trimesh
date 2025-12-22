@extends('core::layout.app')

@section('title', 'Mesh Repair Settings')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0">
                <i class="fas fa-cog"></i> Mesh Repair Settings
            </h1>
            <p class="text-muted">Configure mesh repair service parameters</p>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('admin.mesh-repair.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <!-- Service Configuration -->
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Service Configuration</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.mesh-repair.settings.update') }}">
                        @csrf
                        
                        <div class="form-group">
                            <label for="service_url">Service URL</label>
                            <input type="url" 
                                   class="form-control @error('service_url') is-invalid @enderror" 
                                   id="service_url" 
                                   name="service_url" 
                                   value="{{ old('service_url', $config['service_url']) }}" 
                                   required>
                            <small class="form-text text-muted">
                                Python mesh repair service endpoint (e.g., http://localhost:8001)
                            </small>
                            @error('service_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="timeout">Request Timeout (seconds)</label>
                            <input type="number" 
                                   class="form-control @error('timeout') is-invalid @enderror" 
                                   id="timeout" 
                                   name="timeout" 
                                   value="{{ old('timeout', $config['timeout']) }}" 
                                   min="30" 
                                   max="600" 
                                   required>
                            <small class="form-text text-muted">
                                Maximum time to wait for repair response (30-600 seconds)
                            </small>
                            @error('timeout')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="max_file_size">Maximum File Size (bytes)</label>
                            <input type="number" 
                                   class="form-control @error('max_file_size') is-invalid @enderror" 
                                   id="max_file_size" 
                                   name="max_file_size" 
                                   value="{{ old('max_file_size', $config['max_file_size']) }}" 
                                   min="1048576" 
                                   max="1073741824" 
                                   required>
                            <small class="form-text text-muted">
                                Maximum upload size: {{ number_format($config['max_file_size'] / 1024 / 1024, 0) }} MB
                                (1 MB to 1 GB)
                            </small>
                            @error('max_file_size')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Settings
                            </button>
                            <button type="reset" class="btn btn-secondary">
                                <i class="fas fa-undo"></i> Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Quality Thresholds -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quality Thresholds</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Quality scoring is automatic based on repair results
                    </div>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Rating</th>
                                <th>Score Range</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="table-success">
                                <td><strong>Excellent</strong></td>
                                <td>90-100</td>
                                <td>Fully watertight, manifold, minimal volume change</td>
                            </tr>
                            <tr class="table-info">
                                <td><strong>Good</strong></td>
                                <td>70-89</td>
                                <td>Watertight but minor issues, acceptable for printing</td>
                            </tr>
                            <tr class="table-warning">
                                <td><strong>Fair</strong></td>
                                <td>50-69</td>
                                <td>Some holes remain, may need manual review</td>
                            </tr>
                            <tr class="table-danger">
                                <td><strong>Poor</strong></td>
                                <td>0-49</td>
                                <td>Significant issues, requires attention</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Info Sidebar -->
        <div class="col-md-4">
            <!-- Service Status -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Service Status</h6>
                </div>
                <div class="card-body">
                    <div id="service-status" class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Checking...</span>
                        </div>
                        <p class="mt-2 mb-0">Checking service health...</p>
                    </div>
                </div>
            </div>

            <!-- Documentation -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Documentation</h6>
                </div>
                <div class="card-body">
                    <h6 class="font-weight-bold">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="/MESH_REPAIR_DEPLOYMENT_GUIDE.md" target="_blank">
                                <i class="fas fa-book"></i> Deployment Guide
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="/SERVER_SIDE_MESH_REPAIR_COMPLETE.md" target="_blank">
                                <i class="fas fa-file-alt"></i> Implementation Details
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="/python-mesh-service/README.md" target="_blank">
                                <i class="fas fa-code"></i> API Documentation
                            </a>
                        </li>
                    </ul>

                    <hr>

                    <h6 class="font-weight-bold">Helpful Commands</h6>
                    <div class="small">
                        <p class="mb-1"><code>docker-compose ps</code></p>
                        <p class="text-muted">Check services status</p>

                        <p class="mb-1"><code>docker-compose logs mesh-repair</code></p>
                        <p class="text-muted">View service logs</p>

                        <p class="mb-1"><code>docker-compose restart mesh-repair</code></p>
                        <p class="text-muted">Restart service</p>
                    </div>
                </div>
            </div>

            <!-- System Info -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">System Information</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td><strong>Algorithm:</strong></td>
                            <td>pymeshfix (MeshFix)</td>
                        </tr>
                        <tr>
                            <td><strong>Volume Calc:</strong></td>
                            <td>Signed Tetrahedron</td>
                        </tr>
                        <tr>
                            <td><strong>Framework:</strong></td>
                            <td>FastAPI + Trimesh</td>
                        </tr>
                        <tr>
                            <td><strong>Max Workers:</strong></td>
                            <td>2 (configurable)</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Check service health on page load
async function checkServiceHealth() {
    try {
        const response = await fetch('/api/mesh/status');
        const data = await response.json();
        
        const statusDiv = document.getElementById('service-status');
        
        if (data.available) {
            statusDiv.innerHTML = `
                <div class="text-success">
                    <i class="fas fa-check-circle fa-3x"></i>
                    <h5 class="mt-3">Service Online</h5>
                    <p class="text-muted mb-0">Responding normally</p>
                </div>
            `;
        } else {
            statusDiv.innerHTML = `
                <div class="text-danger">
                    <i class="fas fa-times-circle fa-3x"></i>
                    <h5 class="mt-3">Service Offline</h5>
                    <p class="text-muted mb-0">Not responding</p>
                </div>
            `;
        }
    } catch (error) {
        document.getElementById('service-status').innerHTML = `
            <div class="text-warning">
                <i class="fas fa-exclamation-triangle fa-3x"></i>
                <h5 class="mt-3">Error</h5>
                <p class="text-muted mb-0">${error.message}</p>
            </div>
        `;
    }
}

// Check on load
document.addEventListener('DOMContentLoaded', checkServiceHealth);

// Auto-refresh every 30 seconds
setInterval(checkServiceHealth, 30000);
</script>
@endpush
