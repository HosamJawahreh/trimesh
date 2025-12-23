@extends('core::layout.app')

@section('content')

<div class="pb-12">
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">🔧 Repair Log Details #{{ $log->id }}</h2>
                <div>
                    <a href="{{ route('admin.repair-logs.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Logs
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- General Information -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> General Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="text-muted"><strong>Log ID:</strong></td>
                            <td>#{{ $log->id }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted"><strong>Filename:</strong></td>
                            <td>
                                <i class="fas fa-cube text-primary"></i> {{ $log->filename }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted"><strong>Repair Method:</strong></td>
                            <td><span class="badge bg-info">{{ $log->repair_method }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted"><strong>Date & Time:</strong></td>
                            <td>{{ $log->created_at->format('F d, Y \a\t H:i:s') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted"><strong>Watertight Achieved:</strong></td>
                            <td>
                                @if($log->watertight_achieved)
                                    <span class="badge bg-success"><i class="fas fa-check"></i> Yes</span>
                                @else
                                    <span class="badge bg-danger"><i class="fas fa-times"></i> No</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Mesh Statistics -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Mesh Statistics</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="text-muted"><strong>Holes Filled:</strong></td>
                            <td><span class="badge bg-info">{{ $log->holes_filled }} holes</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted"><strong>Original Vertices:</strong></td>
                            <td>{{ number_format($log->original_vertices) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted"><strong>Repaired Vertices:</strong></td>
                            <td>
                                {{ number_format($log->repaired_vertices) }}
                                <small class="text-success">
                                    (+{{ number_format($log->repaired_vertices - $log->original_vertices) }})
                                </small>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted"><strong>Original Faces:</strong></td>
                            <td>{{ number_format($log->original_faces) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted"><strong>Repaired Faces:</strong></td>
                            <td>
                                {{ number_format($log->repaired_faces) }}
                                <small class="text-success">
                                    (+{{ number_format($log->repaired_faces - $log->original_faces) }})
                                </small>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Volume Analysis -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-cube"></i> Volume Analysis</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center p-3">
                                <h6 class="text-muted">Original Volume</h6>
                                <h3 class="text-primary">{{ number_format($log->original_volume_cm3, 4) }}</h3>
                                <small>cm³</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3">
                                <h6 class="text-muted">Repaired Volume</h6>
                                <h3 class="text-success">{{ number_format($log->repaired_volume_cm3, 4) }}</h3>
                                <small>cm³</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3">
                                <h6 class="text-muted">Volume Change</h6>
                                <h3 class="{{ $log->volume_change_cm3 > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $log->volume_change_cm3 > 0 ? '+' : '' }}{{ number_format($log->volume_change_cm3, 4) }}
                                </h3>
                                <small>cm³</small>
                                <div class="mt-2">
                                    <span class="badge {{ abs($log->volume_change_percent) < 5 ? 'bg-success' : 'bg-warning' }}">
                                        {{ $log->volume_change_percent > 0 ? '+' : '' }}{{ number_format($log->volume_change_percent, 2) }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Volume Change Visualization -->
                    <div class="progress mt-3" style="height: 30px;">
                        @php
                            $originalPercent = 50;
                            $changePercent = ($log->volume_change_percent / 100) * 50;
                        @endphp
                        <div class="progress-bar bg-primary" role="progressbar" 
                             style="width: {{ $originalPercent }}%">
                            Original
                        </div>
                        <div class="progress-bar {{ $log->volume_change_cm3 > 0 ? 'bg-success' : 'bg-danger' }}" 
                             role="progressbar" 
                             style="width: {{ abs($changePercent) }}%">
                            Change
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- File Paths -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-folder"></i> File Paths</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted"><strong>Original File:</strong></label>
                        <div class="input-group">
                            <input type="text" class="form-control" value="{{ $log->original_file_path }}" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('{{ $log->original_file_path }}')">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted"><strong>Repaired File:</strong></label>
                        <div class="input-group">
                            <input type="text" class="form-control" value="{{ $log->repaired_file_path }}" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('{{ $log->repaired_file_path }}')">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Repair Notes -->
        @if($log->repair_notes)
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-sticky-note"></i> Repair Notes</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $log->repair_notes }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Path copied to clipboard!');
    });
}
</script>

@endsection
