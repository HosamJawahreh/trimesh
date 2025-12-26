@extends('frontend.layouts.master')

@section('title', 'My Printing Orders')

@section('content')
<div class="printing-orders-area" style="background: #f8f9fa; min-height: calc(100vh - 100px); padding: 40px 0;">
    <div class="container">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h2 class="mb-2">My 3D Printing Orders</h2>
                        <small class="text-muted">View and track all your printing orders</small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('quote.viewer') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> New Order
                        </a>
                        <a href="{{ route('user.dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders List -->
        @if($orders->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0" style="border-radius: 12px;">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                    <tr>
                                        <th style="padding: 15px; border: none;">Order #</th>
                                        <th style="border: none;">Date</th>
                                        <th style="border: none;">Files</th>
                                        <th style="border: none;">Total Amount</th>
                                        <th style="border: none;">Status</th>
                                        <th style="border: none;">Payment</th>
                                        <th class="text-center" style="border: none;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                    <tr>
                                        <td style="padding: 15px;">
                                            <strong style="color: #667eea;">{{ $order->order_number }}</strong>
                                        </td>
                                        <td>
                                            {{ $order->created_at->format('M d, Y') }}
                                            <br>
                                            <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $order->total_files }} files</span>
                                            <br>
                                            <small class="text-muted">{{ number_format($order->total_volume, 2) }} cm³</small>
                                        </td>
                                        <td>
                                            <strong class="text-success" style="font-size: 1.05rem;">${{ number_format($order->total_price + $order->shipping_charge, 2) }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ 
                                                $order->status === 'completed' ? 'success' : 
                                                ($order->status === 'delivered' ? 'success' : 
                                                ($order->status === 'out_for_delivery' ? 'info' :
                                                ($order->status === 'cancelled' ? 'danger' : 
                                                ($order->status === 'printing' ? 'primary' : 
                                                ($order->status === 'on_hold' ? 'secondary' : 'warning'))))) 
                                            }}" style="font-size: 0.85rem; padding: 6px 12px;">
                                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $order->payment_status === 'success' ? 'success' : ($order->payment_status === 'rejected' ? 'danger' : 'warning') }}" style="font-size: 0.85rem; padding: 6px 12px;">
                                                {{ ucfirst($order->payment_status) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('user.printing-orders.show', $order->id) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="View Details"
                                                   style="border-radius: 6px 0 0 6px;">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('user.printing-orders.invoice', $order->id) }}" 
                                                   class="btn btn-sm btn-outline-success" 
                                                   target="_blank" 
                                                   title="View Invoice"
                                                   style="border-radius: 0 6px 6px 0;">
                                                    <i class="fas fa-file-invoice"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                @if($orders->hasPages())
                <div class="mt-4 d-flex justify-content-center">
                    {{ $orders->links() }}
                </div>
                @endif
            </div>
        </div>
        @else
        <!-- Empty State -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 text-center" style="border-radius: 12px; padding: 60px 20px;">
                    <div style="font-size: 4rem; color: #dee2e6; margin-bottom: 20px;">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <h4 class="mb-3">No Orders Yet</h4>
                    <p class="text-muted mb-4">You haven't placed any 3D printing orders yet.</p>
                    <a href="{{ route('quote.viewer') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i> Create Your First Order
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
