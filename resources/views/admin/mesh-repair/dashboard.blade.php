@extends('core::layout.app')

@section('title', 'Mesh Repair Dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">
                <i class="fas fa-cube"></i> Mesh Repair System
            </h1>
            <p class="text-muted">Production-grade mesh repair monitoring and statistics</p>
        </div>
    </div>

    <!-- Service Status Alert -->
    <div class="row mb-4">
        <div class="col-12">
            @if($serviceAvailable)
                <div class="alert alert-success d-flex align-items-center">
                    <i class="fas fa-check-circle me-2"></i>
                    <div>
                        <strong>Service Online</strong> - Python mesh repair service is available and responding
                    </div>
                </div>
            @else
                <div class="alert alert-danger d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <div>
                        <strong>Service Offline</strong> - Python mesh repair service is not responding. Check logs and configuration.
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <!-- Total Repairs -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Repairs
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_repairs']) }}</div>
                            <div class="text-xs text-muted mt-1">
                                Success Rate: {{ $stats['success_rate'] }}%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tools fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Repairs -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Today
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['today_repairs'] }}</div>
                            <div class="text-xs text-muted mt-1">
                                This Week: {{ $stats['week_repairs'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Average Quality -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Avg Quality Score
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['average_quality'], 1) }}/100
                            </div>
                            <div class="text-xs text-muted mt-1">
                                @if($stats['average_quality'] >= 90) Excellent
                                @elseif($stats['average_quality'] >= 70) Good
                                @elseif($stats['average_quality'] >= 50) Fair
                                @else Needs Attention
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Holes Filled -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Holes Filled
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_holes_filled']) }}</div>
                            <div class="text-xs text-muted mt-1">
                                Watertight: {{ $stats['watertight_achieved'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-fill-drip fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Quality Distribution -->
        <div class="col-xl-6 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Quality Distribution</h6>
                </div>
                <div class="card-body">
                    <canvas id="qualityChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Daily Repair Trends -->
        <div class="col-xl-6 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Repair Trends (30 Days)</h6>
                </div>
                <div class="card-body">
                    <canvas id="trendChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="row mb-4">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Performance Metrics</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="border-left-success p-3">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Successful Repairs
                                </div>
                                <div class="h4 mb-0">{{ number_format($stats['successful_repairs']) }}</div>
                                <div class="progress mt-2" style="height: 5px;">
                                    <div class="progress-bar bg-success" role="progressbar"
                                         style="width: {{ $stats['success_rate'] }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="border-left-danger p-3">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Failed Repairs
                                </div>
                                <div class="h4 mb-0">{{ number_format($stats['failed_repairs']) }}</div>
                                <div class="progress mt-2" style="height: 5px;">
                                    <div class="progress-bar bg-danger" role="progressbar"
                                         style="width: {{ $stats['total_repairs'] > 0 ? ($stats['failed_repairs'] / $stats['total_repairs'] * 100) : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="border-left-info p-3">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Average Repair Time
                                </div>
                                <div class="h4 mb-0">{{ number_format($stats['average_time'], 1) }}s</div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="border-left-warning p-3">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Avg Volume Change
                                </div>
                                <div class="h4 mb-0">{{ number_format($stats['average_volume_change'], 4) }} cm³</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.mesh-repair.logs') }}" class="btn btn-primary btn-block mb-2">
                        <i class="fas fa-list"></i> View Repair Logs
                    </a>
                    <a href="{{ route('admin.mesh-repair.settings') }}" class="btn btn-secondary btn-block mb-2">
                        <i class="fas fa-cog"></i> Configuration
                    </a>
                    <a href="{{ route('admin.mesh-repair.export') }}" class="btn btn-success btn-block mb-2">
                        <i class="fas fa-download"></i> Export Statistics
                    </a>
                    <button class="btn btn-info btn-block" onclick="checkServiceHealth()">
                        <i class="fas fa-heartbeat"></i> Check Service Health
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Repairs -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Repairs</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>File</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Quality</th>
                                    <th>Holes Filled</th>
                                    <th>Volume Change</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentRepairs as $repair)
                                    <tr>
                                        <td>#{{ $repair->id }}</td>
                                        <td>
                                            <small>{{ $repair->file->filename ?? 'N/A' }}</small>
                                        </td>
                                        <td>
                                            <small>{{ $repair->created_at->format('Y-m-d H:i') }}</small>
                                        </td>
                                        <td>
                                            @if($repair->status === 'completed')
                                                <span class="badge badge-success">Completed</span>
                                            @elseif($repair->status === 'failed')
                                                <span class="badge badge-danger">Failed</span>
                                            @else
                                                <span class="badge badge-warning">{{ ucfirst($repair->status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $repair->quality_score >= 90 ? 'success' : ($repair->quality_score >= 70 ? 'info' : 'warning') }}">
                                                {{ number_format($repair->quality_score, 1) }}/100
                                            </span>
                                        </td>
                                        <td>{{ $repair->holes_filled }}</td>
                                        <td>
                                            <small>{{ number_format($repair->volume_change, 4) }} cm³</small>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.mesh-repair.show', $repair->id) }}"
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">No repairs yet</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.mesh-repair.logs') }}" class="btn btn-outline-primary">
                            View All Repairs
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// Quality Distribution Chart
const qualityData = @json($qualityDistribution);
new Chart(document.getElementById('qualityChart'), {
    type: 'doughnut',
    data: {
        labels: qualityData.map(d => d.rating.charAt(0).toUpperCase() + d.rating.slice(1)),
        datasets: [{
            data: qualityData.map(d => d.count),
            backgroundColor: ['#28a745', '#17a2b8', '#ffc107', '#dc3545']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'right'
            }
        }
    }
});

// Daily Trends Chart
const trendData = @json($dailyTrends);
new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: trendData.map(d => d.date),
        datasets: [{
            label: 'Repairs',
            data: trendData.map(d => d.count),
            borderColor: '#4e73df',
            backgroundColor: 'rgba(78, 115, 223, 0.1)',
            tension: 0.3
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Check service health
async function checkServiceHealth() {
    try {
        const response = await fetch('/api/mesh/status');
        const data = await response.json();

        if (data.available) {
            alert('✅ Service is healthy and responding!\n\nService URL: ' + data.service_url);
        } else {
            alert('❌ Service is not available. Check configuration.');
        }
    } catch (error) {
        alert('❌ Error checking service: ' + error.message);
    }
}
</script>
@endpush
