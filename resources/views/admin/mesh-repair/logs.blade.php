@extends('core::layout.app')

@section('title', 'Mesh Repair Logs')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0">
                <i class="fas fa-list"></i> Mesh Repair Logs
            </h1>
            <p class="text-muted">View and filter repair history</p>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('admin.mesh-repair.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
            <a href="{{ route('admin.mesh-repair.export') }}" class="btn btn-success">
                <i class="fas fa-download"></i> Export CSV
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.mesh-repair.logs') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="">All</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Date From</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Date To</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Search File</label>
                            <input type="text" name="search" class="form-control" placeholder="Filename..." value="{{ request('search') }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Min Quality</label>
                            <input type="number" name="quality_min" class="form-control" min="0" max="100" value="{{ request('quality_min') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Max Quality</label>
                            <input type="number" name="quality_max" class="form-control" min="0" max="100" value="{{ request('quality_max') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Apply Filters
                                </button>
                                <a href="{{ route('admin.mesh-repair.logs') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Repairs Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Repair Records ({{ $repairs->total() }} total)
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>File</th>
                            <th>Date & Time</th>
                            <th>Status</th>
                            <th>Original Vol.</th>
                            <th>Repaired Vol.</th>
                            <th>Change</th>
                            <th>Holes</th>
                            <th>Quality</th>
                            <th>Time</th>
                            <th>Watertight</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($repairs as $repair)
                            <tr>
                                <td>#{{ $repair->id }}</td>
                                <td>
                                    <small title="{{ $repair->file->filename ?? 'N/A' }}">
                                        {{ Str::limit($repair->file->filename ?? 'N/A', 20) }}
                                    </small>
                                </td>
                                <td>
                                    <small>
                                        {{ $repair->created_at->format('Y-m-d') }}<br>
                                        {{ $repair->created_at->format('H:i:s') }}
                                    </small>
                                </td>
                                <td>
                                    @if($repair->status === 'completed')
                                        <span class="badge badge-success">Completed</span>
                                    @elseif($repair->status === 'failed')
                                        <span class="badge badge-danger">Failed</span>
                                    @elseif($repair->status === 'processing')
                                        <span class="badge badge-info">Processing</span>
                                    @else
                                        <span class="badge badge-warning">Pending</span>
                                    @endif
                                </td>
                                <td>{{ number_format($repair->original_volume_cm3, 4) }}</td>
                                <td>{{ number_format($repair->repaired_volume_cm3, 4) }}</td>
                                <td>
                                    <span class="badge badge-{{ $repair->volume_change >= 0 ? 'success' : 'warning' }}">
                                        {{ number_format($repair->volume_change, 4) }}
                                    </span>
                                </td>
                                <td>{{ $repair->holes_filled }}</td>
                                <td>
                                    <span class="badge badge-{{ $repair->quality_score >= 90 ? 'success' : ($repair->quality_score >= 70 ? 'info' : ($repair->quality_score >= 50 ? 'warning' : 'danger')) }}">
                                        {{ number_format($repair->quality_score, 1) }}
                                    </span>
                                </td>
                                <td>
                                    <small>{{ number_format($repair->repair_time_seconds, 2) }}s</small>
                                </td>
                                <td>
                                    @if($repair->is_watertight)
                                        <i class="fas fa-check text-success"></i>
                                    @else
                                        <i class="fas fa-times text-danger"></i>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.mesh-repair.show', $repair->id) }}"
                                       class="btn btn-sm btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.mesh-repair.destroy', $repair->id) }}"
                                          style="display: inline;"
                                          onsubmit="return confirm('Delete this repair record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center text-muted">
                                    No repairs found matching your filters
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $repairs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
