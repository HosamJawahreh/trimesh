@extends('frontend.layouts.master')

@section('title', 'Order Submitted Successfully')

@section('content')
<div class="printing-order-success-area" style="background: #f8f9fa; min-height: calc(100vh - 100px); padding: 20px 0;">
    <div class="container-fluid">
        <div class="row">
            {{-- Left Side - Logo and Branding --}}
            <div class="col-lg-3 d-none d-lg-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: calc(100vh - 140px); border-radius: 0 20px 20px 0;">
                <div class="text-center text-white p-4">
                    @if(!empty($settings->logo))
                        <img src="{{ asset($settings->logo) }}" alt="{{ $settings->app_name }}" style="max-width: 180px; filter: brightness(0) invert(1); margin-bottom: 30px;">
                    @else
                        <h2 class="fw-bold mb-0" style="font-size: 2.5rem;">{{ $settings->app_name ?? '3D Print' }}</h2>
                    @endif
                    <div class="mt-4">
                        <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px; box-shadow: 0 8px 32px rgba(0,0,0,0.1);">
                            <i class="fas fa-check" style="font-size: 40px;"></i>
                        </div>
                        <h4 class="fw-bold mb-2">Order Confirmed!</h4>
                        <p style="opacity: 0.9; font-size: 0.9rem;">Your order has been successfully submitted</p>
                    </div>
                </div>
            </div>

            {{-- Right Side - Order Details --}}
            <div class="col-lg-9">
                <div class="row">
                    <div class="col-12">
                        {{-- Header --}}
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h3 class="fw-bold mb-1" style="color: #2d3748;">Order Confirmation</h3>
                                <p class="text-muted mb-0" style="font-size: 0.9rem;">Order #<strong style="color: #4a90e2;">{{ $order->order_number }}</strong></p>
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
                                @auth
                                    @if(auth()->user()->type === 'user')
                                        <a href="{{ route('user.printing-orders.invoice', $order->id) }}" class="btn btn-outline-success btn-sm" style="border-radius: 8px;" target="_blank">
                                            <i class="fas fa-file-invoice me-1"></i> Invoice
                                        </a>
                                        <a href="{{ route('user.dashboard') }}" class="btn btn-outline-primary btn-sm" style="border-radius: 8px;">
                                            <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                                        </a>
                                    @endif
                                @endauth
                                <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm" style="border-radius: 8px;">
                                    <i class="fas fa-home me-1"></i> Home
                                </a>
                                <a href="{{ route('quote') }}" class="btn btn-primary btn-sm" style="border-radius: 8px; background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%); border: none;">
                                    <i class="fas fa-plus me-1"></i> New Order
                                </a>
                            </div>
                        </div>

                        {{-- Order Status Progress Bar --}}
                        <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
                            <div class="card-body p-3">
                                <h6 class="mb-3"><i class="fas fa-tasks me-2 text-primary"></i>Order Progress</h6>
                                @php
                                    $statuses = [
                                        'pending' => ['label' => 'Order Placed', 'icon' => 'check-circle', 'desc' => 'Order received'],
                                        'processing' => ['label' => 'Processing', 'icon' => 'cogs', 'desc' => 'Preparing files'],
                                        'printing' => ['label' => 'Printing', 'icon' => 'print', 'desc' => 'Being printed'],
                                        'out_for_delivery' => ['label' => 'Out for Delivery', 'icon' => 'truck', 'desc' => 'On the way'],
                                        'delivered' => ['label' => 'Delivered', 'icon' => 'box-check', 'desc' => 'Delivered'],
                                        'completed' => ['label' => 'Completed', 'icon' => 'check-double', 'desc' => 'Order completed']
                                    ];
                                    
                                    $statusOrder = array_keys($statuses);
                                    $currentStatusIndex = array_search($order->status, $statusOrder);
                                    
                                    if ($order->status === 'cancelled') {
                                        $currentStatusIndex = -1;
                                    } elseif ($order->status === 'on_hold') {
                                        $currentStatusIndex = -2;
                                    }
                                    
                                    $totalSteps = count($statuses);
                                    $progress = $currentStatusIndex >= 0 ? (($currentStatusIndex + 1) / $totalSteps) * 100 : 0;
                                @endphp

                                <div class="order-progress-tracker">
                                    <div class="progress mb-3" style="height: 8px; background: #e9ecef; border-radius: 10px;">
                                        <div class="progress-bar bg-{{ $order->status === 'cancelled' ? 'danger' : ($order->status === 'on_hold' ? 'warning' : 'success') }}" 
                                             role="progressbar" 
                                             style="width: {{ $progress }}%; border-radius: 10px; transition: width 0.6s ease;" 
                                             aria-valuenow="{{ $progress }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    
                                    <div class="row text-center g-2">
                                        @foreach($statuses as $statusKey => $statusInfo)
                                            @php
                                                $stepIndex = array_search($statusKey, $statusOrder);
                                                $isActive = $statusKey === $order->status;
                                                $isCompleted = $currentStatusIndex > $stepIndex && $currentStatusIndex >= 0;
                                                $isFuture = $currentStatusIndex < $stepIndex || $currentStatusIndex < 0;
                                            @endphp
                                            <div class="col-4 col-md-2">
                                                <div class="progress-step {{ $isActive ? 'active' : ($isCompleted ? 'completed' : 'future') }}">
                                                    <div class="progress-icon mb-1">
                                                        <div class="icon-circle {{ $isActive ? 'bg-primary text-white' : ($isCompleted ? 'bg-success text-white' : 'bg-light text-muted') }}" 
                                                             style="width: 40px; height: 40px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; border: 2px solid {{ $isActive ? '#0d6efd' : ($isCompleted ? '#198754' : '#dee2e6') }}; transition: all 0.3s ease; box-shadow: {{ $isActive ? '0 2px 8px rgba(13, 110, 253, 0.3)' : '0 1px 3px rgba(0,0,0,0.1)' }}; {{ $isActive ? 'animation: pulse 2s infinite;' : '' }}">
                                                            <i class="fas fa-{{ $statusInfo['icon'] }}" style="font-size: 0.9rem;"></i>
                                                        </div>
                                                    </div>
                                                    <div class="progress-label">
                                                        <strong class="{{ $isActive ? 'text-primary' : ($isCompleted ? 'text-success' : 'text-muted') }}" style="font-size: 0.7rem;">
                                                            {{ $statusInfo['label'] }}
                                                        </strong>
                                                        <br>
                                                        <small class="text-muted" style="font-size: 0.6rem;">
                                                            {{ $statusInfo['desc'] }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    @if($order->status === 'cancelled')
                                        <div class="alert alert-danger mt-3 mb-0" style="border-radius: 8px; padding: 10px;">
                                            <i class="fas fa-times-circle me-2"></i><strong>Order Cancelled</strong>
                                        </div>
                                    @elseif($order->status === 'on_hold')
                                        <div class="alert alert-warning mt-3 mb-0" style="border-radius: 8px; padding: 10px;">
                                            <i class="fas fa-pause-circle me-2"></i><strong>Order On Hold</strong>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Stats Cards --}}
                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #4a90e2 !important; border-radius: 10px;">
                                    <div class="card-body p-3 d-flex align-items-center">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);">
                                            <i class="fas fa-file text-white"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block" style="font-size: 0.75rem;">Total Files</small>
                                            <h4 class="mb-0 fw-bold" style="color: #2d3748;">{{ $order->total_files }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #10b981 !important; border-radius: 10px;">
                                    <div class="card-body p-3 d-flex align-items-center">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                            <i class="fas fa-cube text-white"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block" style="font-size: 0.75rem;">Total Volume</small>
                                            <h4 class="mb-0 fw-bold" style="color: #2d3748; font-size: 1rem;">{{ number_format($order->total_volume, 2) }} cm³</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #f59e0b !important; border-radius: 10px;">
                                    <div class="card-body p-3 d-flex align-items-center">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                                            <i class="fas fa-dollar-sign text-white"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block" style="font-size: 0.75rem;">Total Amount</small>
                                            <h4 class="mb-0 fw-bold text-success" style="font-size: 1.1rem;">${{ number_format($order->total_price, 2) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Files List --}}
                            <div class="col-lg-8 mb-3">
                                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                                    <div class="card-body p-3">
                                        <h6 class="fw-bold mb-3" style="color: #2d3748; font-size: 0.95rem;">
                                            <i class="fas fa-th-list me-2 text-primary"></i>Order Items
                                        </h6>
                                        <div style="max-height: 400px; overflow-y: auto; padding-right: 5px;">
                                            @foreach($order->files_data as $index => $file)
                                            <div class="mb-2 p-2 border rounded" style="background: #f8f9fa; border-color: #e9ecef !important;">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div class="d-flex gap-2 flex-grow-1">
                                                        <div class="d-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 6px;">
                                                            <strong style="color: white; font-size: 0.75rem;">{{ $index + 1 }}</strong>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <strong style="font-size: 0.85rem; color: #2d3748;">{{ $file['name'] ?? 'File ' . ($index + 1) }}</strong>
                                                            <div class="d-flex gap-2 flex-wrap mt-1">
                                                                <span class="badge bg-primary" style="font-size: 0.65rem;">{{ $file['technology'] ?? 'N/A' }}</span>
                                                                <span class="badge bg-info" style="font-size: 0.65rem;">{{ $file['material'] ?? 'N/A' }}</span>
                                                                <span class="d-inline-flex align-items-center">
                                                                    <span style="width: 12px; height: 12px; background: {{ $file['color'] ?? '#ccc' }}; border: 1px solid #dee2e6; border-radius: 2px; display: inline-block;"></span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <strong class="text-success" style="font-size: 0.9rem; white-space: nowrap;">${{ number_format((float)($file['price'] ?? 0), 2) }}</strong>
                                                </div>
                                                <div class="d-flex gap-3 text-muted" style="font-size: 0.7rem; padding-left: 36px;">
                                                    <span><i class="fas fa-ruler me-1"></i>{{ number_format((float)($file['volume'] ?? 0), 2) }} cm³</span>
                                                    <span><i class="fas fa-expand-arrows-alt me-1"></i>{{ $file['scale'] ?? 100 }}%</span>
                                                    <span><i class="fas fa-clone me-1"></i>Qty: {{ $file['quantity'] ?? 1 }}</span>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Right Side Info --}}
                            <div class="col-lg-4">
                                {{-- Viewer Link --}}
                                @if($order->viewer_link)
                                <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px; border-left: 4px solid #8b5cf6 !important;">
                                    <div class="card-body p-3">
                                        <h6 class="fw-bold mb-2" style="color: #2d3748; font-size: 0.85rem;">
                                            <i class="fas fa-eye me-2 text-purple"></i>3D Viewer
                                        </h6>
                                        <a href="{{ $order->viewer_link }}?viewer={{ $order->viewer_type }}" target="_blank" class="btn btn-sm btn-outline-primary w-100" style="border-radius: 8px;">
                                            <i class="fas fa-external-link-alt me-1"></i> Open Viewer
                                        </a>
                                    </div>
                                </div>
                                @endif

                                {{-- Next Steps --}}
                                <div class="card border-0 shadow-sm" style="border-radius: 12px; background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);">
                                    <div class="card-body p-3">
                                        <h6 class="fw-bold mb-3" style="color: #2d3748; font-size: 0.85rem;">
                                            <i class="fas fa-clipboard-check me-2 text-info"></i>What's Next?
                                        </h6>
                                        <div class="d-flex flex-column gap-2">
                                            <div class="d-flex align-items-start">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 me-2" style="width: 32px; height: 32px; background: #3b82f6;">
                                                    <i class="fas fa-check text-white" style="font-size: 0.7rem;"></i>
                                                </div>
                                                <div>
                                                    <strong style="font-size: 0.8rem; color: #2d3748;">Order Review</strong>
                                                    <p class="mb-0 text-muted" style="font-size: 0.7rem;">Within 24 hours</p>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-start">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 me-2" style="width: 32px; height: 32px; background: #3b82f6;">
                                                    <i class="fas fa-envelope text-white" style="font-size: 0.7rem;"></i>
                                                </div>
                                                <div>
                                                    <strong style="font-size: 0.8rem; color: #2d3748;">Email Confirmation</strong>
                                                    <p class="mb-0 text-muted" style="font-size: 0.7rem;">Check your inbox</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom scrollbar for file list */
.card-body > div::-webkit-scrollbar {
    width: 6px;
}
.card-body > div::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}
.card-body > div::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}
.card-body > div::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Order Progress Tracker */
.order-progress-tracker .progress-step {
    transition: all 0.3s ease;
}

.order-progress-tracker .icon-circle {
    transition: all 0.3s ease;
}

.order-progress-tracker .progress-step.completed .icon-circle:hover {
    transform: scale(1.1);
    cursor: pointer;
}

@keyframes pulse {
    0%, 100% {
        box-shadow: 0 2px 8px rgba(13, 110, 253, 0.3);
    }
    50% {
        box-shadow: 0 2px 16px rgba(13, 110, 253, 0.5);
    }
}
</style>
@endsection
