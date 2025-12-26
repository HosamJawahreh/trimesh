@php
    $user = auth()->user();
@endphp
<div class="tp-my-acount-wrap mb-30">
    <div class="d-sm-flex align-items-center justify-content-between flex-wrap gap-5">
        <h3 class="tp-my-acount-title mb-5 mb-sm-0">{{ __('frontend.personal_information') }}</h3>
        <a href="{{ route('user.profile') }}" class="ss-shop-btn ms-auto">{{ __('frontend.edit_info') }}</a>
    </div>

    <div class="tp-my-acount-content mt-20">
        <div class="tp-my-acount-item d-flex align-items-center flex-wrap mb-20">
            <span class="tp-my-acount-label">{{ __('frontend.name') }}</span>
            <span class="tp-my-acount-value">{{ $user->name }}</span>
        </div>
        <div class="tp-my-acount-item d-flex align-items-center flex-wrap mb-20">
            <span class="tp-my-acount-label">{{ __('frontend.email') }}</span>
            <div class="tp-my-acount-value">
                {{ $user->email }}
                @if ($user->email_verified_at)
                    <span class="text-success"> ({{ __('frontend.verified') }})</span>
                @else
                    <span class="text-danger"> ({{ __('frontend.not_verified') }})</span>
                    <form action="{{ route('user.user-verification.resend') }}" method="POST" class="d-inline-block">
                        @csrf
                        @method('POST')
                        <button type="submit" class="text-primary ms-2 text-decoration-underline">
                            {{ __('frontend.send_verification_email') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
        <div class="tp-my-acount-item d-flex align-items-center flex-wrap mb-20">
            <span class="tp-my-acount-label">{{ __('frontend.username') }}</span>
            <span class="tp-my-acount-value">{{ $user->username }}</span>
        </div>
        <div class="tp-my-acount-item d-flex align-items-center flex-wrap mb-20">
            <span class="tp-my-acount-label">{{ __('frontend.phone') }}</span>
            <span class="tp-my-acount-value">{{ $user->phone }}</span>
        </div>
        <div class="tp-my-acount-item d-flex align-items-center flex-wrap mb-20">
            <span class="tp-my-acount-label">{{ __('frontend.address') }}</span>
            <span class="tp-my-acount-value">{{ $user->address }}</span>
        </div>
        <div class="tp-my-acount-item d-flex align-items-center flex-wrap mb-20">
            <span class="tp-my-acount-label">{{ __('frontend.zip_code') }}</span>
            <span class="tp-my-acount-value">{{ $user->zip_code }}</span>
        </div>
    </div>
 </div>

 {{-- Printing Orders Section --}}
 @if(isset($printingOrders) && $printingOrders->count() > 0)
 <div class="tp-my-acount-wrap mt-30">
    <div class="d-sm-flex align-items-center justify-content-between flex-wrap gap-5">
        <h3 class="tp-my-acount-title mb-3 mb-sm-0">3D Printing Orders</h3>
        <a href="{{ route('user.printing-orders.index') }}" class="ss-shop-btn ms-auto">View All Orders</a>
    </div>

    <div class="table-responsive mt-20">
        <table class="table table-hover">
            <thead style="background: #f8f9fa;">
                <tr>
                    <th>Order #</th>
                    <th>Date</th>
                    <th>Files</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($printingOrders as $order)
                <tr>
                    <td><strong>{{ $order->order_number }}</strong></td>
                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                    <td>{{ $order->total_files }}</td>
                    <td class="text-success"><strong>${{ number_format($order->total_price + $order->shipping_charge, 2) }}</strong></td>
                    <td>
                        <span class="badge bg-{{ 
                            $order->status === 'completed' ? 'success' : 
                            ($order->status === 'delivered' ? 'success' : 
                            ($order->status === 'out_for_delivery' ? 'info' :
                            ($order->status === 'cancelled' ? 'danger' : 
                            ($order->status === 'printing' ? 'primary' : 
                            ($order->status === 'on_hold' ? 'secondary' : 'warning'))))) 
                        }}">
                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-{{ $order->payment_status === 'success' ? 'success' : ($order->payment_status === 'rejected' ? 'danger' : 'warning') }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('user.printing-orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary me-1" title="View Details">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('user.printing-orders.invoice', $order->id) }}" class="btn btn-sm btn-outline-success" target="_blank" title="View Invoice">
                            <i class="fas fa-file-invoice"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
 </div>
 @endif
