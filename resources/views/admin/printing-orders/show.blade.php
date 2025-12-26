@extends('core::layout.app')

@section('content')

<div class="pb-12">
    <!-- Header -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0">Order {{ $order->order_number }}</h2>
                    <small class="text-muted">Created {{ $order->created_at->format('F d, Y \a\t h:i A') }}</small>
                </div>
                <div>
                    <a href="{{ route('admin.printing-orders.invoice', $order->id) }}" class="btn btn-success me-2" target="_blank">
                        <i class="fas fa-file-invoice"></i> View Invoice
                    </a>
                    <a href="{{ route('admin.printing-orders.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Orders
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Status Progress Bar -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="mb-4"><i class="fas fa-tasks me-2"></i>Order Progress</h6>
                    @php
                        $statuses = [
                            'pending' => ['label' => 'Pending', 'icon' => 'clock'],
                            'processing' => ['label' => 'Processing', 'icon' => 'cogs'],
                            'printing' => ['label' => 'Printing', 'icon' => 'print'],
                            'out_for_delivery' => ['label' => 'Out for Delivery', 'icon' => 'truck'],
                            'delivered' => ['label' => 'Delivered', 'icon' => 'box-check'],
                            'completed' => ['label' => 'Completed', 'icon' => 'check-circle']
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
                        <div class="progress mb-4" style="height: 8px; background: #e9ecef;">
                            <div class="progress-bar bg-{{ $order->status === 'cancelled' ? 'danger' : ($order->status === 'on_hold' ? 'warning' : 'success') }}" 
                                 role="progressbar" 
                                 style="width: {{ $progress }}%;" 
                                 aria-valuenow="{{ $progress }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                            </div>
                        </div>
                        
                        <div class="row text-center">
                            @foreach($statuses as $statusKey => $statusInfo)
                                @php
                                    $stepIndex = array_search($statusKey, $statusOrder);
                                    $isActive = $statusKey === $order->status;
                                    $isCompleted = $currentStatusIndex > $stepIndex && $currentStatusIndex >= 0;
                                    $isFuture = $currentStatusIndex < $stepIndex || $currentStatusIndex < 0;
                                @endphp
                                <div class="col">
                                    <div class="progress-step {{ $isActive ? 'active' : ($isCompleted ? 'completed' : 'future') }}">
                                        <div class="progress-icon mb-2">
                                            <div class="icon-circle {{ $isActive ? 'bg-primary text-white' : ($isCompleted ? 'bg-success text-white' : 'bg-light text-muted') }}" 
                                                 style="width: 48px; height: 48px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; border: 3px solid {{ $isActive ? '#0d6efd' : ($isCompleted ? '#198754' : '#dee2e6') }};">
                                                <i class="fas fa-{{ $statusInfo['icon'] }} fa-lg"></i>
                                            </div>
                                        </div>
                                        <div class="progress-label">
                                            <strong class="{{ $isActive ? 'text-primary' : ($isCompleted ? 'text-success' : 'text-muted') }}" style="font-size: 0.85rem;">
                                                {{ $statusInfo['label'] }}
                                            </strong>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        @if($order->status === 'cancelled')
                            <div class="alert alert-danger mt-3 mb-0">
                                <i class="fas fa-times-circle me-2"></i><strong>Order Cancelled</strong>
                            </div>
                        @elseif($order->status === 'on_hold')
                            <div class="alert alert-warning mt-3 mb-0">
                                <i class="fas fa-pause-circle me-2"></i><strong>Order On Hold</strong>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Order Items -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-cube me-2"></i>Order Items</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>File Name</th>
                                    <th>Technology</th>
                                    <th>Material</th>
                                    <th>Color</th>
                                    <th>Scale</th>
                                    <th>Qty</th>
                                    <th>Volume</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->files_data as $file)
                                <tr>
                                    <td>
                                        <i class="fas fa-file-alt me-2 text-primary"></i>
                                        <strong>{{ $file['name'] ?? 'Unnamed File' }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ strtoupper($file['technology'] ?? 'N/A') }}</span>
                                    </td>
                                    <td>{{ ucfirst($file['material'] ?? 'N/A') }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span style="display: inline-block; width: 20px; height: 20px; background-color: {{ $file['color'] ?? '#cccccc' }}; border: 2px solid #dee2e6; border-radius: 4px;"></span>
                                            <small>{{ $file['color'] ?? 'N/A' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $file['scale'] ?? 100 }}%</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $file['quantity'] ?? 1 }}</span>
                                    </td>
                                    <td>{{ number_format($file['volume'] ?? 0, 2) }} cm³</td>
                                    <td><strong>${{ number_format($file['price'] ?? 0, 2) }}</strong></td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6"></td>
                                    <td><strong>Subtotal:</strong></td>
                                    <td><strong>${{ number_format($order->total_price, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <td colspan="6"></td>
                                    <td><strong>Shipping:</strong></td>
                                    <td><strong>${{ number_format($order->shipping_charge, 2) }}</strong></td>
                                </tr>
                                <tr class="table-active">
                                    <td colspan="6"></td>
                                    <td><strong>Total:</strong></td>
                                    <td><strong class="text-success">${{ number_format($order->total_price + $order->shipping_charge, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Viewer Link -->
            @if($order->viewer_link)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-link me-2"></i>3D Viewer Link</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1"><strong>Viewer Type:</strong> <span class="badge bg-info">{{ ucfirst($order->viewer_type) }}</span></p>
                            <p class="mb-0 text-muted small">{{ $order->viewer_link }}?viewer={{ $order->viewer_type }}</p>
                        </div>
                        <a href="{{ $order->viewer_link }}?viewer={{ $order->viewer_type }}" target="_blank" class="btn btn-primary">
                            <i class="fas fa-external-link-alt me-1"></i> Open Viewer
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Shipping Address -->
            @if($order->address)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-shipping-fast me-2"></i>Shipping Address</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>{{ $order->address->shipping_first_name }} {{ $order->address->shipping_last_name }}</strong></p>
                            <p class="mb-1">{{ $order->address->shipping_address }}</p>
                            <p class="mb-1">{{ $order->address->shipping_city }}, {{ $order->address->shipping_province }} {{ $order->address->shipping_zip_code }}</p>
                            <p class="mb-1">{{ $order->address->shipping_country }}</p>
                            <p class="mb-1"><i class="fas fa-phone me-2"></i>{{ $order->address->shipping_phone }}</p>
                            <p class="mb-0"><i class="fas fa-envelope me-2"></i>{{ $order->address->shipping_email }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Customer Note -->
            @if($order->customer_note)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-comment me-2"></i>Customer Note</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $order->customer_note }}</p>
                </div>
            </div>
            @endif

            <!-- Admin Notes -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Admin Notes</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.printing-orders.update-notes', $order->id) }}" method="POST">
                        @csrf
                        <textarea name="notes" class="form-control mb-3" rows="4" placeholder="Add internal notes about this order...">{{ $order->notes }}</textarea>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Save Notes
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Customer Info -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Customer</h5>
                </div>
                <div class="card-body">
                    @if($order->user)
                        <p class="mb-2"><strong>{{ $order->user->name }}</strong></p>
                        <p class="mb-2"><i class="fas fa-envelope me-2"></i>{{ $order->user->email }}</p>
                        @if($order->user->phone)
                        <p class="mb-0"><i class="fas fa-phone me-2"></i>{{ $order->user->phone }}</p>
                        @endif
                    @else
                        <p class="mb-0 text-muted">Guest Order</p>
                    @endif
                </div>
            </div>

            <!-- Order Status -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>Order Status</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.printing-orders.update-status', $order->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Current Status</label>
                            <select name="status" class="form-select">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="printing" {{ $order->status == 'printing' ? 'selected' : '' }}>Printing</option>
                                <option value="out_for_delivery" {{ $order->status == 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                                <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                <option value="on_hold" {{ $order->status == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-check me-1"></i>Update Status
                        </button>
                    </form>
                </div>
            </div>

            <!-- Payment Info -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i>Payment</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.printing-orders.update-payment-status', $order->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <input type="text" class="form-control" value="{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Payment Status</label>
                            <select name="payment_status" class="form-select">
                                <option value="pending" {{ $order->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="success" {{ $order->payment_status == 'success' ? 'selected' : '' }}>Paid</option>
                                <option value="rejected" {{ $order->payment_status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="refund" {{ $order->payment_status == 'refund' ? 'selected' : '' }}>Refunded</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-check me-1"></i>Update Payment
                        </button>
                    </form>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Viewer Type:</span>
                        <strong>{{ ucfirst($order->viewer_type) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Files:</span>
                        <strong>{{ $order->total_files }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Volume:</span>
                        <strong>{{ number_format($order->total_volume, 2) }} cm³</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Quality:</span>
                        <strong>{{ ucfirst($order->quality ?? 'Standard') }}</strong>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow-sm border-danger">
                <div class="card-header bg-white">
                    <h5 class="mb-0 text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Danger Zone</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.printing-orders.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this order? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-trash me-1"></i>Delete Order
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.order-progress-tracker .progress-step {
    transition: all 0.3s ease;
}

.order-progress-tracker .progress-step .icon-circle {
    transition: all 0.3s ease;
    margin: 0 auto;
}

.order-progress-tracker .progress-step.active .icon-circle {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
    }
    50% {
        box-shadow: 0 4px 20px rgba(13, 110, 253, 0.5);
    }
}

.order-progress-tracker .progress-step.completed .icon-circle:hover {
    transform: scale(1.1);
}

.order-progress-tracker .progress-label {
    margin-top: 8px;
}
</style>

@endsection
