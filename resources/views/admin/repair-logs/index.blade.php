@extends('core::layout.app')

@section('content')

<div class="pb-12">
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">🔧 Mesh Repair Logs</h2>
                <div>
                    <a href="{{ route('admin.mesh-repair.dashboard') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-icon-wrapper rounded-circle bg-primary text-white" style="width: 50px; height: 50px;">
                                <i class="fas fa-tools fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 text-muted">Total Repairs</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_repairs']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-icon-wrapper rounded-circle bg-success text-white" style="width: 50px; height: 50px;">
                                <i class="fas fa-fill-drip fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 text-muted">Holes Filled</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_holes_filled']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-icon-wrapper rounded-circle bg-info text-white" style="width: 50px; height: 50px;">
                                <i class="fas fa-check-circle fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 text-muted">Watertight</h6>
                            <h3 class="mb-0">{{ number_format($stats['watertight_achieved']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-icon-wrapper rounded-circle bg-warning text-white" style="width: 50px; height: 50px;">
                                <i class="fas fa-percentage fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 text-muted">Avg Volume Change</h6>
                            <h3 class="mb-0">{{ number_format($stats['avg_volume_change'], 2) }}%</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Repair Logs Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Recent Repairs</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Filename</th>
                            <th>Date</th>
                            <th>Holes Filled</th>
                            <th>Original Volume</th>
                            <th>Repaired Volume</th>
                            <th>Change %</th>
                            <th>Watertight</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td><strong>#{{ $log->id }}</strong></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-cube text-primary me-2"></i>
                                    <span class="text-truncate" style="max-width: 200px;" title="{{ $log->filename }}">
                                        {{ $log->filename }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <small>{{ $log->created_at->format('M d, Y') }}</small><br>
                                <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $log->holes_filled }} holes</span>
                            </td>
                            <td>{{ number_format($log->original_volume_cm3, 2) }} cm³</td>
                            <td>{{ number_format($log->repaired_volume_cm3, 2) }} cm³</td>
                            <td>
                                <span class="badge {{ abs($log->volume_change_percent) < 5 ? 'bg-success' : 'bg-warning' }}">
                                    {{ $log->volume_change_percent > 0 ? '+' : '' }}{{ number_format($log->volume_change_percent, 2) }}%
                                </span>
                            </td>
                            <td>
                                @if($log->watertight_achieved)
                                    <span class="badge bg-success"><i class="fas fa-check"></i> Yes</span>
                                @else
                                    <span class="badge bg-danger"><i class="fas fa-times"></i> No</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.repair-logs.show', $log->id) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.repair-logs.destroy', $log->id) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this log?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>No repair logs found yet.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($logs->hasPages())
        <div class="card-footer bg-white">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>

@endsection
