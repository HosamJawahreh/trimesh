@extends('frontend.layouts.master')

@section('title', 'Order Details - ' . $order->order_number)

@section('content')
<div class="printing-order-detail-area" style="background: #f8f9fa; min-height: calc(100vh - 100px); padding: 40px 0;">
    <div class="container">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h2 class="mb-2">Order #{{ $order->order_number }}</h2>
                        <small class="text-muted">Placed on {{ $order->created_at->format('F d, Y \a\t h:i A') }}</small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('user.printing-orders.invoice', $order->id) }}" class="btn btn-success" target="_blank">
                            <i class="fas fa-file-invoice me-1"></i> View Invoice
                        </a>
                        <a href="{{ route('user.dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Status Progress Bar -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0" style="border-radius: 12px;">
                    <div class="card-body p-4">
                        <h5 class="mb-4"><i class="fas fa-tasks me-2 text-primary"></i>Order Progress</h5>
                        @php
                            $statuses = [
                                'pending' => ['label' => 'Order Placed', 'icon' => 'check-circle', 'desc' => 'Your order has been received'],
                                'processing' => ['label' => 'Processing', 'icon' => 'cogs', 'desc' => 'Preparing your files'],
                                'printing' => ['label' => 'Printing', 'icon' => 'print', 'desc' => 'Your order is being printed'],
                                'out_for_delivery' => ['label' => 'Out for Delivery', 'icon' => 'truck', 'desc' => 'Package is on the way'],
                                'delivered' => ['label' => 'Delivered', 'icon' => 'box-check', 'desc' => 'Package has been delivered'],
                                'completed' => ['label' => 'Completed', 'icon' => 'check-double', 'desc' => 'Order completed']
                            ];
                            
                            $statusOrder = array_keys($statuses);
                            $currentStatusIndex = array_search($order->status, $statusOrder);
                            
                            // Handle special statuses
                            if ($order->status === 'cancelled') {
                                $currentStatusIndex = -1;
                            } elseif ($order->status === 'on_hold') {
                                $currentStatusIndex = -2;
                            }
                            
                            $totalSteps = count($statuses);
                            $progress = $currentStatusIndex >= 0 ? (($currentStatusIndex + 1) / $totalSteps) * 100 : 0;
                        @endphp

                        <div class="order-progress-tracker">
                            <!-- Progress Bar -->
                            <div class="progress mb-5" style="height: 10px; background: #e9ecef; border-radius: 10px;">
                                <div class="progress-bar bg-{{ $order->status === 'cancelled' ? 'danger' : ($order->status === 'on_hold' ? 'warning' : 'success') }}" 
                                     role="progressbar" 
                                     style="width: {{ $progress }}%; border-radius: 10px; transition: width 0.6s ease;" 
                                     aria-valuenow="{{ $progress }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                </div>
                            </div>
                            
                            <!-- Progress Steps -->
                            <div class="row text-center g-3">
                                @foreach($statuses as $statusKey => $statusInfo)
                                    @php
                                        $stepIndex = array_search($statusKey, $statusOrder);
                                        $isActive = $statusKey === $order->status;
                                        $isCompleted = $currentStatusIndex > $stepIndex && $currentStatusIndex >= 0;
                                        $isFuture = $currentStatusIndex < $stepIndex || $currentStatusIndex < 0;
                                    @endphp
                                    <div class="col-6 col-md-4 col-lg-2">
                                        <div class="progress-step {{ $isActive ? 'active' : ($isCompleted ? 'completed' : 'future') }}">
                                            <div class="progress-icon mb-2">
                                                <div class="icon-circle {{ $isActive ? 'bg-primary text-white' : ($isCompleted ? 'bg-success text-white' : 'bg-light text-muted') }}" 
                                                     style="width: 60px; height: 60px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; border: 3px solid {{ $isActive ? '#0d6efd' : ($isCompleted ? '#198754' : '#dee2e6') }}; transition: all 0.3s ease; box-shadow: {{ $isActive ? '0 4px 12px rgba(13, 110, 253, 0.3)' : '0 2px 4px rgba(0,0,0,0.1)' }};">
                                                    <i class="fas fa-{{ $statusInfo['icon'] }} fa-lg"></i>
                                                </div>
                                            </div>
                                            <div class="progress-label">
                                                <strong class="{{ $isActive ? 'text-primary' : ($isCompleted ? 'text-success' : 'text-muted') }}" style="font-size: 0.9rem;">
                                                    {{ $statusInfo['label'] }}
                                                </strong>
                                                <br>
                                                <small class="text-muted" style="font-size: 0.75rem;">
                                                    {{ $statusInfo['desc'] }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <!-- Special Status Alerts -->
                            @if($order->status === 'cancelled')
                                <div class="alert alert-danger mt-4 mb-0" style="border-radius: 8px;">
                                    <i class="fas fa-times-circle me-2"></i><strong>Order Cancelled</strong>
                                    <p class="mb-0 mt-2">This order has been cancelled. If you have any questions, please contact support.</p>
                                </div>
                            @elseif($order->status === 'on_hold')
                                <div class="alert alert-warning mt-4 mb-0" style="border-radius: 8px;">
                                    <i class="fas fa-pause-circle me-2"></i><strong>Order On Hold</strong>
                                    <p class="mb-0 mt-2">Your order is temporarily on hold. We'll contact you if any action is needed.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column - Order Details -->
            <div class="col-lg-8 mb-4">
                <!-- Order Items -->
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-white border-0" style="border-radius: 12px 12px 0 0; padding: 20px;">
                        <h5 class="mb-0"><i class="fas fa-cube me-2 text-primary"></i>Order Items ({{ $order->total_files }} files)</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead style="background: #f8f9fa;">
                                    <tr>
                                        <th style="padding: 15px;">File</th>
                                        <th>Tech/Material</th>
                                        <th>Color</th>
                                        <th class="text-center">Scale</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end" style="padding-right: 20px;">Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->files_data as $index => $file)
                                    <tr>
                                        <td style="padding: 15px;">
                                            <div class="d-flex align-items-center gap-2">
                                                <div style="width: 30px; height: 30px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                                    <strong style="color: white; font-size: 0.75rem;">{{ $index + 1 }}</strong>
                                                </div>
                                                <div>
                                                    <strong style="font-size: 0.9rem;">{{ $file['name'] ?? 'File ' . ($index + 1) }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ number_format($file['volume'] ?? 0, 2) }} cm³</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info" style="font-size: 0.7rem;">{{ strtoupper($file['technology'] ?? 'N/A') }}</span>
                                            <br>
                                            <small class="text-muted">{{ ucfirst($file['material'] ?? 'N/A') }}</small>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span style="width: 16px; height: 16px; background-color: {{ $file['color'] ?? '#ccc' }}; border: 2px solid #dee2e6; border-radius: 3px; display: inline-block;"></span>
                                                <small>{{ $file['color'] ?? 'N/A' }}</small>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary">{{ $file['scale'] ?? 100 }}%</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">{{ $file['quantity'] ?? 1 }}</span>
                                        </td>
                                        <td class="text-end" style="padding-right: 20px;">
                                            <strong class="text-success">${{ number_format($file['price'] ?? 0, 2) }}</strong>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot style="background: #f8f9fa;">
                                    <tr>
                                        <td colspan="5" class="text-end" style="padding: 15px;"><strong>Subtotal:</strong></td>
                                        <td class="text-end" style="padding: 15px 20px;"><strong>${{ number_format($order->total_price, 2) }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-end" style="padding: 15px;"><strong>Shipping:</strong></td>
                                        <td class="text-end" style="padding: 15px 20px;"><strong>${{ number_format($order->shipping_charge, 2) }}</strong></td>
                                    </tr>
                                    <tr style="background: #e7f3ff;">
                                        <td colspan="5" class="text-end" style="padding: 15px;"><strong style="font-size: 1.1rem;">Total:</strong></td>
                                        <td class="text-end" style="padding: 15px 20px;"><strong class="text-success" style="font-size: 1.1rem;">${{ number_format($order->total_price + $order->shipping_charge, 2) }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Viewer Link -->
                @if($order->viewer_link)
                <div class="card shadow-sm border-0" style="border-radius: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body p-4 text-white">
                        <h6 class="mb-3"><i class="fas fa-eye me-2"></i>3D Viewer</h6>
                        <p class="mb-3" style="opacity: 0.9;">View your 3D model in our interactive viewer</p>
                        <a href="{{ $order->viewer_link }}?viewer={{ $order->viewer_type }}" target="_blank" class="btn btn-light">
                            <i class="fas fa-external-link-alt me-1"></i> Open 3D Viewer
                        </a>
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Column - Additional Info -->
            <div class="col-lg-4">
                <!-- Order Summary -->
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-white border-0" style="border-radius: 12px 12px 0 0; padding: 20px;">
                        <h6 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Order Summary</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted d-block">Payment Method</small>
                            <strong>{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Payment Status</small>
                            <span class="badge bg-{{ $order->payment_status === 'success' ? 'success' : ($order->payment_status === 'rejected' ? 'danger' : 'warning') }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Shipping Method</small>
                            <strong>{{ ucfirst(str_replace('_', ' ', $order->shipping_method ?? 'Standard')) }}</strong>
                        </div>
                        <div>
                            <small class="text-muted d-block">Total Volume</small>
                            <strong>{{ number_format($order->total_volume, 2) }} cm³</strong>
                        </div>
                    </div>
                </div>

                <!-- Shipping Address -->
                @if($order->address)
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-white border-0" style="border-radius: 12px 12px 0 0; padding: 20px;">
                        <h6 class="mb-0"><i class="fas fa-shipping-fast me-2 text-primary"></i>Shipping Address</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-1"><strong>{{ $order->address->full_name }}</strong></p>
                        <p class="mb-1">{{ $order->address->address }}</p>
                        @if($order->address->address_2)
                            <p class="mb-1">{{ $order->address->address_2 }}</p>
                        @endif
                        <p class="mb-1">{{ $order->address->city }}, {{ $order->address->state }} {{ $order->address->zip_code }}</p>
                        <p class="mb-1">{{ $order->address->country }}</p>
                        @if($order->address->phone)
                            <p class="mb-0"><i class="fas fa-phone me-2"></i>{{ $order->address->phone }}</p>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Customer Note -->
                @if($order->customer_note)
                <div class="card shadow-sm border-0" style="border-radius: 12px;">
                    <div class="card-header bg-white border-0" style="border-radius: 12px 12px 0 0; padding: 20px;">
                        <h6 class="mb-0"><i class="fas fa-comment me-2 text-primary"></i>Your Note</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $order->customer_note }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
/* Order Progress Tracker */
.order-progress-tracker .progress-step {
    transition: all 0.3s ease;
}

.order-progress-tracker .icon-circle {
    transition: all 0.3s ease;
}

.order-progress-tracker .progress-step.active .icon-circle {
    animation: pulse 2s infinite;
}

.order-progress-tracker .progress-step.completed .icon-circle:hover {
    transform: scale(1.1);
    cursor: pointer;
}

@keyframes pulse {
    0%, 100% {
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
    }
    50% {
        box-shadow: 0 4px 20px rgba(13, 110, 253, 0.5);
    }
}
</style>

@endsection
