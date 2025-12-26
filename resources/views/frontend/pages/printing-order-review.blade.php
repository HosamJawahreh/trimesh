@extends('frontend.layouts.master')

@section('title', 'Review Printing Order')

@section('content')
<div class="printing-order-review-area py-5" style="background: linear-gradient(to bottom, #e8f0f7 0%, #d5e3f0 100%); min-height: calc(100vh - 100px);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                {{-- Page Header --}}
                <div class="text-center mb-4">
                    <h3 class="fw-bold mb-2" style="color: #2d3748;">
                        <i class="fas fa-clipboard-check me-2" style="color: #4a90e2;"></i>
                        Review Your Order
                    </h3>
                </div>

                <form action="{{ route('printing-order.store') }}" method="POST" id="printingOrderForm">
                    @csrf

                    {{-- Order Summary Card --}}
                    <div class="card shadow-sm mb-3" style="border: none; border-radius: 12px; background: rgba(255,255,255,0.95);">
                        <div class="card-header" style="padding: 15px; background: #fff; border-bottom: 2px solid #e9ecef;">
                            <h5 class="mb-0" style="color: #2d3748;">
                                <i class="fas fa-cube me-2" style="color: #4a90e2;"></i>
                                Order Summary
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-3 col-6">
                                    <div class="p-3 rounded text-center" style="background: #f8f9fa; border: 1px solid #e9ecef;">
                                        <small class="text-muted d-block">Viewer Type</small>
                                        <strong class="d-block mt-1">{{ ucfirst($quoteData['viewer_type'] ?? 'General') }}</strong>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="p-3 rounded text-center" style="background: #f8f9fa; border: 1px solid #e9ecef;">
                                        <small class="text-muted d-block">Total Files</small>
                                        <strong class="d-block mt-1">{{ $quoteData['total_files'] ?? 0 }}</strong>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="p-3 rounded text-center" style="background: #e8f5e9; border: 1px solid #c8e6c9;">
                                        <small class="text-muted d-block">Total Volume</small>
                                        <strong id="totalVolumeDisplay" class="d-block mt-1 text-success">
                                            @php
                                                $totalVolume = 0;
                                                if (isset($quoteData['files']) && is_array($quoteData['files'])) {
                                                    foreach ($quoteData['files'] as $file) {
                                                        $totalVolume += (float)($file['volume'] ?? 0);
                                                    }
                                                }
                                            @endphp
                                            {{ number_format($totalVolume, 2) }} cm³
                                        </strong>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="p-3 rounded text-center" style="background: #fff3e0; border: 1px solid #ffe0b2;">
                                        <small class="text-muted d-block">Total Price</small>
                                        <strong id="totalPriceDisplay" class="d-block mt-1 text-warning fs-5">
                                            @php
                                                $totalPrice = 0;
                                                if (isset($quoteData['files']) && is_array($quoteData['files'])) {
                                                    foreach ($quoteData['files'] as $file) {
                                                        $totalPrice += (float)($file['price'] ?? 0);
                                                    }
                                                }
                                            @endphp
                                            ${{ number_format($totalPrice, 2) }}
                                        </strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Files List Card --}}
                    <div class="card shadow-sm mb-3" style="border: none; border-radius: 12px; background: rgba(255,255,255,0.95);">
                        <div class="card-header" style="padding: 15px; background: #fff; border-bottom: 2px solid #e9ecef;">
                            <h5 class="mb-0" style="color: #2d3748;">
                                <i class="fas fa-file-alt me-2" style="color: #4a90e2;"></i>
                                Files ({{ count($quoteData['files'] ?? []) }})
                            </h5>
                        </div>
                        <div class="card-body p-3">
                            @if(isset($quoteData['files']) && count($quoteData['files']) > 0)
                                <div class="list-group">
                                    @foreach($quoteData['files'] as $index => $file)
                                    <div class="list-group-item file-item" style="border-radius: 8px; margin-bottom: 8px; border: 1px solid #e9ecef; background: #fff; padding: 12px;">
                                        {{-- File Header Row --}}
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="file-number" style="width: 24px; height: 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                    <strong style="color: white; font-size: 0.7rem;">{{ $index + 1 }}</strong>
                                                </div>
                                                <div class="file-icon-mini" style="width: 28px; height: 28px; background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%); border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-cube text-white" style="font-size: 0.7rem;"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0" style="color: #2d3748; font-size: 0.85rem; font-weight: 600;">{{ $file['name'] ?? 'File ' . ($index + 1) }}</h6>
                                                    <small class="text-muted" style="font-size: 0.7rem;">
                                                        <i class="fas fa-cube me-1"></i><span class="file-volume-{{ $index }}">{{ number_format((float)($file['volume'] ?? 0), 2) }}</span> cm³
                                                        <span class="mx-1">•</span>
                                                        <i class="fas fa-dollar-sign me-1"></i><span class="file-price-{{ $index }}">{{ number_format((float)($file['price'] ?? 0), 2) }}</span>
                                                    </small>
                                                </div>
                                            </div>
                                            
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="openSimpleFileModal('Review', {{ $index }})" style="border-radius: 6px; padding: 4px 12px; font-size: 0.75rem;">
                                                <i class="fas fa-cog me-1"></i> Settings
                                            </button>
                                        </div>
                                        
                                        {{-- Controls Row --}}
                                        <div class="d-flex gap-3 align-items-center flex-wrap">
                                            <div class="d-flex align-items-center gap-2">
                                                <label class="mb-0 text-dark" style="font-size: 0.85rem; font-weight: 600;">Scale:</label>
                                                <div class="d-flex align-items-center gap-1">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="adjustScaleQuantity('scale-{{ $index }}', -25)" style="width: 32px; height: 32px; padding: 0; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fas fa-minus" style="font-size: 0.7rem;"></i>
                                                    </button>
                                                    <div class="d-flex align-items-center" style="position: relative;">
                                                        <input type="number" 
                                                               class="form-control form-control-sm text-center" 
                                                               id="scale-{{ $index }}" 
                                                               value="100" 
                                                               min="25" 
                                                               max="500" 
                                                               step="25"
                                                               oninput="updateFileCalculations({{ $index }})"
                                                               style="width: 90px; height: 32px; font-size: 0.95rem; font-weight: 600; color: #2d3748; border: 2px solid #dee2e6; border-radius: 6px; -moz-appearance: textfield; padding-right: 24px;">
                                                        <span style="position: absolute; right: 8px; pointer-events: none; font-size: 0.85rem; font-weight: 600; color: #6c757d;">%</span>
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="adjustScaleQuantity('scale-{{ $index }}', 25)" style="width: 32px; height: 32px; padding: 0; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fas fa-plus" style="font-size: 0.7rem;"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <div class="d-flex align-items-center gap-2">
                                                <label class="mb-0 text-dark" style="font-size: 0.85rem; font-weight: 600;">Quantity:</label>
                                                <div class="d-flex align-items-center gap-1">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="adjustScaleQuantity('quantity-{{ $index }}', -1)" style="width: 32px; height: 32px; padding: 0; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fas fa-minus" style="font-size: 0.7rem;"></i>
                                                    </button>
                                                    <input type="number" 
                                                           class="form-control form-control-sm text-center" 
                                                           id="quantity-{{ $index }}" 
                                                           value="1" 
                                                           min="1" 
                                                           max="100" 
                                                           step="1"
                                                           oninput="updateFileCalculations({{ $index }})"
                                                           style="width: 65px; height: 32px; font-size: 0.95rem; font-weight: 600; color: #2d3748; border: 2px solid #dee2e6; border-radius: 6px; -moz-appearance: textfield;">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="adjustScaleQuantity('quantity-{{ $index }}', 1)" style="width: 32px; height: 32px; padding: 0; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fas fa-plus" style="font-size: 0.7rem;"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <div class="ms-auto px-2 py-1" style="background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%); border-radius: 6px; border: 1px solid #a5d6a7;">
                                                <small class="d-block text-center" style="font-size: 0.6rem; color: #2e7d32; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;">Total</small>
                                                <strong class="file-total-price-{{ $index }} d-block text-center" style="color: #1b5e20; font-size: 0.95rem; font-weight: 800; line-height: 1;">${{ number_format((float)($file['price'] ?? 0), 2) }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-2"></i>
                                    <p class="text-muted">No files found</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Additional Notes Card --}}
                    <div class="card shadow-sm mb-3" style="border: none; border-radius: 12px; background: rgba(255,255,255,0.95);">
                        <div class="card-header" style="padding: 15px; background: #fff; border-bottom: 2px solid #e9ecef;">
                            <h5 class="mb-0" style="color: #2d3748;">
                                <i class="fas fa-comment-dots me-2" style="color: #4a90e2;"></i>
                                Additional Notes (Optional)
                            </h5>
                        </div>
                        <div class="card-body p-3">
                            <textarea
                                name="customer_note"
                                class="form-control"
                                rows="3"
                                placeholder="Add any special instructions or notes for your order..."
                                style="border-radius: 8px; border: 1px solid #dee2e6;"
                            ></textarea>
                            <small class="text-muted mt-2 d-block">Maximum 1000 characters</small>
                        </div>
                    </div>

                    {{-- Shipping Address Card --}}
                    @auth
                    <div class="card shadow-sm mb-3" style="border: none; border-radius: 12px; background: rgba(255,255,255,0.95);">
                        <div class="card-header" style="padding: 15px; background: #fff; border-bottom: 2px solid #e9ecef; cursor: pointer;" onclick="toggleShippingInfo()">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0" style="color: #2d3748;">
                                    <i class="fas fa-shipping-fast me-2" style="color: #4a90e2;"></i>
                                    Shipping Information
                                    <small class="text-muted" style="font-size: 0.75rem; font-weight: normal;">(Optional)</small>
                                </h5>
                                <i class="fas fa-chevron-down" id="shippingToggleIcon" style="transition: transform 0.3s; color: #4a90e2;"></i>
                            </div>
                        </div>
                        <div class="card-body p-4" id="shippingInfoContent" style="display: none;">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">First Name</label>
                                    <input type="text" name="shipping_first_name" class="form-control" value="{{ auth()->user()->first_name ?? '' }}" style="border-radius: 8px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Last Name</label>
                                    <input type="text" name="shipping_last_name" class="form-control" value="{{ auth()->user()->last_name ?? '' }}" style="border-radius: 8px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Email</label>
                                    <input type="email" name="shipping_email" class="form-control" value="{{ auth()->user()->email ?? '' }}" style="border-radius: 8px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Phone</label>
                                    <input type="tel" name="shipping_phone" class="form-control" style="border-radius: 8px;">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Address</label>
                                    <input type="text" name="shipping_address" class="form-control" placeholder="Street address" style="border-radius: 8px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">City</label>
                                    <input type="text" name="shipping_city" class="form-control" style="border-radius: 8px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Province/State</label>
                                    <input type="text" name="shipping_province" class="form-control" style="border-radius: 8px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">ZIP/Postal Code</label>
                                    <input type="text" name="shipping_zip_code" class="form-control" style="border-radius: 8px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Country</label>
                                    <input type="text" name="shipping_country" class="form-control" style="border-radius: 8px;">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Payment Method Card --}}
                    <div class="card shadow-sm mb-3" style="border: none; border-radius: 12px; background: rgba(255,255,255,0.95);">
                        <div class="card-header" style="padding: 15px; background: #fff; border-bottom: 2px solid #e9ecef;">
                            <h5 class="mb-0" style="color: #2d3748;">
                                <i class="fas fa-credit-card me-2" style="color: #4a90e2;"></i>
                                Payment Method
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="form-check p-3 h-100" style="border: 2px solid #e9ecef; border-radius: 8px; cursor: pointer;" onclick="selectPaymentMethod('cash_on_delivery')">
                                        <input class="form-check-input" type="radio" name="payment_method" value="cash_on_delivery" id="codRadio" checked>
                                        <label class="form-check-label fw-bold d-flex flex-column" for="codRadio" style="cursor: pointer;">
                                            <span class="d-flex align-items-center mb-2">
                                                <i class="fas fa-money-bill-wave me-2 text-success"></i>
                                                Cash on Delivery
                                            </span>
                                            <small class="text-muted">Pay when you receive</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check p-3 h-100" style="border: 2px solid #e9ecef; border-radius: 8px; cursor: pointer;" onclick="selectPaymentMethod('bank_transfer')">
                                        <input class="form-check-input" type="radio" name="payment_method" value="bank_transfer" id="bankRadio">
                                        <label class="form-check-label fw-bold d-flex flex-column" for="bankRadio" style="cursor: pointer;">
                                            <span class="d-flex align-items-center mb-2">
                                                <i class="fas fa-university me-2 text-primary"></i>
                                                Bank Transfer
                                            </span>
                                            <small class="text-muted">Transfer to bank</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check p-3 h-100" style="border: 2px solid #e9ecef; border-radius: 8px; cursor: pointer;" onclick="selectPaymentMethod('paypal')">
                                        <input class="form-check-input" type="radio" name="payment_method" value="paypal" id="paypalRadio">
                                        <label class="form-check-label fw-bold d-flex flex-column" for="paypalRadio" style="cursor: pointer;">
                                            <span class="d-flex align-items-center mb-2">
                                                <i class="fab fa-paypal me-2 text-info"></i>
                                                PayPal
                                            </span>
                                            <small class="text-muted">Pay securely online</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endauth

                    @guest
                    {{-- Login/Register Prompt --}}
                    <div class="card shadow-sm mb-3" style="border: none; border-radius: 12px; background: rgba(255,255,255,0.95);">
                        <div class="card-body p-4 text-center">
                            <i class="fas fa-user-lock fa-3x text-primary mb-3"></i>
                            <h5 class="mb-3">Please Login to Continue</h5>
                            <p class="text-muted mb-4">You need to be logged in to submit a printing order</p>
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="{{ route('login') }}" class="btn btn-primary px-4" style="border-radius: 8px;">
                                    <i class="fas fa-sign-in-alt me-1"></i> Login
                                </a>
                                <a href="{{ route('register') }}" class="btn btn-outline-primary px-4" style="border-radius: 8px;">
                                    <i class="fas fa-user-plus me-1"></i> Register
                                </a>
                            </div>
                        </div>
                    </div>
                    @endguest

                    {{-- Hidden Fields --}}
                    <input type="hidden" name="quote_id" value="{{ $quoteData['quote_id'] ?? '' }}">
                    <input type="hidden" name="viewer_type" value="{{ $quoteData['viewer_type'] ?? 'general' }}">
                    <input type="hidden" name="viewer_link" value="{{ $quoteData['viewer_link'] ?? '' }}">
                    <input type="hidden" name="total_price" value="{{ $totalPrice ?? 0 }}">
                    <input type="hidden" name="total_volume" value="{{ $totalVolume ?? 0 }}">
                    <input type="hidden" name="total_files" value="{{ $quoteData['total_files'] ?? 0 }}">
                    <input type="hidden" name="technology" value="{{ $quoteData['technology'] ?? '' }}">
                    <input type="hidden" name="material" value="{{ $quoteData['material'] ?? '' }}">
                    <input type="hidden" name="color" value="{{ $quoteData['color'] ?? '' }}">
                    <input type="hidden" name="quality" value="{{ $quoteData['quality'] ?? '' }}">
                    <input type="hidden" name="files_data" value="{{ json_encode($quoteData['files'] ?? []) }}">

                    {{-- Action Buttons --}}
                    @auth
                    <div class="d-flex gap-2 justify-content-between">
                        <a href="{{ route('quote') }}?restore={{ $quoteData['quote_id'] ?? '' }}&viewer={{ $quoteData['viewer_type'] ?? 'general' }}" class="btn btn-outline-secondary" style="border-radius: 8px;">
                            <i class="fas fa-arrow-left me-1"></i>
                            Back to Editor
                        </a>
                        <button type="submit" class="btn btn-success px-4" style="border-radius: 8px; background: linear-gradient(135deg, #4caf50 0%, #45a049 100%); border: none;">
                            <i class="fas fa-paper-plane me-1"></i>
                            Submit Order
                        </button>
                    </div>
                    @endauth
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Use the same simple modal from quote-viewer --}}
<div id="simpleFileModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 999999; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; overflow-y: auto;">
    <div style="background: white; border-radius: 12px; width: 90%; max-width: 500px; box-shadow: 0 10px 40px rgba(0,0,0,0.3); max-height: 90vh; display: flex; flex-direction: column;">
        <div style="background: #f8f9fa; color: #2d3748; padding: 16px 20px; border-radius: 12px 12px 0 0; border-bottom: 2px solid #e9ecef; flex-shrink: 0;">
            <h5 style="margin: 0; font-weight: 600; font-size: 1rem;">
                <i class="fas fa-cog me-2" style="color: #4a90e2;"></i>
                <span id="simpleModalFileName">File Settings</span>
            </h5>
        </div>
        <div style="padding: 20px; overflow-y: auto; flex: 1;">
            <div style="margin-bottom: 14px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px; color: #495057; font-size: 0.875rem;">Technology</label>
                <select id="simpleTechSelect" style="width: 100%; padding: 8px 10px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 0.875rem;">
                    <!-- Options will be populated based on viewer type -->
                </select>
            </div>
            <div style="margin-bottom: 14px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px; color: #495057; font-size: 0.875rem;">Material</label>
                <select id="simpleMaterialSelect" style="width: 100%; padding: 8px 10px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 0.875rem;">
                    <!-- Options will be populated based on viewer type -->
                </select>
            </div>
            <div style="margin-bottom: 14px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px; color: #495057; font-size: 0.875rem;">Model Color</label>
                <div id="simpleColorPicker" style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <!-- Color buttons will be populated by JS -->
                </div>
            </div>
            <div style="margin-bottom: 0; padding: 10px; background: #f8f9fa; border-radius: 6px; border: 1px solid #dee2e6;">
                <label style="display: flex; align-items: center; cursor: pointer; margin: 0;">
                    <input type="checkbox" id="applyToAllToggle" style="width: 16px; height: 16px; margin-right: 8px; cursor: pointer;">
                    <span style="font-weight: 600; color: #495057; font-size: 0.875rem;">Apply these settings to all files</span>
                </label>
            </div>
        </div>
        <div style="padding: 12px 20px; border-top: 1px solid #dee2e6; display: flex; justify-content: space-between; gap: 10px; flex-shrink: 0;">
            <button onclick="closeSimpleModal()" style="background: #6c757d; color: white; border: none; padding: 8px 20px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 0.875rem;">
                Close
            </button>
            <button onclick="saveFileSettings()" style="background: #4a90e2; color: white; border: none; padding: 8px 20px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 0.875rem;">
                <i class="fas fa-save me-1"></i>
                Save Changes
            </button>
        </div>
    </div>
</div>

<style>
.list-group-item {
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

/* Stop animation when hovering over file item */
.file-item:hover {
    background: #ffffff !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    border-color: #4a90e2 !important;
}

/* Prevent child elements from triggering hover animations */
.file-item:hover * {
    animation-play-state: paused !important;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.simple-color-btn {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    border: 2px solid #dee2e6;
    cursor: pointer;
    transition: all 0.2s;
}

.simple-color-btn.active {
    border-color: #4a90e2;
    border-width: 3px;
    transform: scale(1.1);
}

.simple-color-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

/* Input styling for scale and quantity */
input[type="number"].form-control {
    background-color: #ffffff !important;
    color: #2d3748 !important;
}

input[type="number"].form-control:focus {
    border-color: #4a90e2 !important;
    box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.25) !important;
    background-color: #ffffff !important;
}

input[type="number"].form-control::placeholder {
    color: #6c757d;
}

/* Hide default number input arrows since we have custom buttons */
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none !important;
    appearance: none !important;
    margin: 0 !important;
    display: none !important;
}

input[type="number"] {
    -moz-appearance: textfield !important;
    appearance: textfield !important;
}

/* Additional specificity for scale and quantity inputs */
input[id^="scale-"]::-webkit-inner-spin-button,
input[id^="scale-"]::-webkit-outer-spin-button,
input[id^="quantity-"]::-webkit-inner-spin-button,
input[id^="quantity-"]::-webkit-outer-spin-button {
    -webkit-appearance: none !important;
    display: none !important;
}

input[id^="scale-"],
input[id^="quantity-"] {
    -moz-appearance: textfield !important;
}
</style>

<script>
// Store files data globally
const filesData = @json($quoteData['files'] ?? []);
const viewerType = '{{ $quoteData['viewer_type'] ?? 'general' }}';
let currentFileIndex = -1;

// Adjust scale or quantity value with +/- buttons
function adjustScaleQuantity(inputId, change) {
    const input = document.getElementById(inputId);
    if (!input) return;
    
    let currentValue = parseFloat(input.value) || 0;
    const min = parseFloat(input.min) || 0;
    const max = parseFloat(input.max) || Infinity;
    const step = parseFloat(input.step) || 1;
    
    // Add or subtract the change value
    let newValue = currentValue + change;
    
    // Ensure the value stays within bounds
    newValue = Math.max(min, Math.min(max, newValue));
    
    // Round to nearest step for cleaner values
    if (step > 0) {
        newValue = Math.round(newValue / step) * step;
    }
    
    input.value = newValue;
    
    // Trigger the input event to update calculations
    input.dispatchEvent(new Event('input', { bubbles: true }));
}

// Payment method selection
function selectPaymentMethod(method) {
    // Remove active styling from all payment options
    document.querySelectorAll('.form-check').forEach(el => {
        el.style.borderColor = '#e9ecef';
        el.style.background = '#ffffff';
    });
    
    // Add active styling to selected option
    const selectedOption = document.querySelector(`input[value="${method}"]`).closest('.form-check');
    selectedOption.style.borderColor = '#4a90e2';
    selectedOption.style.background = '#f0f7ff';
    
    // Check the radio button
    document.querySelector(`input[value="${method}"]`).checked = true;
}

// Toggle shipping information
function toggleShippingInfo() {
    const content = document.getElementById('shippingInfoContent');
    const icon = document.getElementById('shippingToggleIcon');
    
    if (content.style.display === 'none' || content.style.display === '') {
        content.style.display = 'block';
        icon.style.transform = 'rotate(180deg)';
    } else {
        content.style.display = 'none';
        icon.style.transform = 'rotate(0deg)';
    }
}

// Update file calculations based on scale and quantity
function updateFileCalculations(fileIndex) {
    console.log(`Updating calculations for file ${fileIndex}`);
    
    const scaleInput = document.getElementById(`scale-${fileIndex}`);
    const quantityInput = document.getElementById(`quantity-${fileIndex}`);
    
    if (!scaleInput || !quantityInput) {
        console.error('Input elements not found');
        return;
    }
    
    const scale = parseFloat(scaleInput.value) || 100;
    const quantity = parseInt(quantityInput.value) || 1;
    
    console.log(`Scale: ${scale}%, Quantity: ${quantity}`);
    
    // Get original file data
    const file = filesData[fileIndex];
    if (!file) {
        console.error('File data not found');
        return;
    }
    
    // Store original values if not already stored
    if (!file.originalPrice) file.originalPrice = parseFloat(file.price) || 0;
    if (!file.originalVolume) file.originalVolume = parseFloat(file.volume) || 0;
    
    const basePrice = file.originalPrice;
    const baseVolume = file.originalVolume;
    
    console.log(`Base price: ${basePrice}, Base volume: ${baseVolume}`);
    
    // Calculate scale factor (scale is in percentage)
    const scaleFactor = scale / 100;
    
    // Volume scales with cube of the scale factor (3D scaling)
    const newVolume = baseVolume * Math.pow(scaleFactor, 3);
    
    // Price scales proportionally with volume
    const scaledPrice = (basePrice / baseVolume) * newVolume;
    
    // Total price with quantity
    const totalPrice = scaledPrice * quantity;
    
    console.log(`New volume: ${newVolume}, Scaled price: ${scaledPrice}, Total: ${totalPrice}`);
    
    // Update display
    const volumeEl = document.querySelector(`.file-volume-${fileIndex}`);
    const priceEl = document.querySelector(`.file-price-${fileIndex}`);
    const totalPriceEl = document.querySelector(`.file-total-price-${fileIndex}`);
    
    if (volumeEl) volumeEl.textContent = newVolume.toFixed(2);
    if (priceEl) priceEl.textContent = scaledPrice.toFixed(2);
    if (totalPriceEl) totalPriceEl.textContent = '$' + totalPrice.toFixed(2);
    
    // Update filesData array with new values
    filesData[fileIndex].volume = newVolume;
    filesData[fileIndex].price = scaledPrice;
    filesData[fileIndex].quantity = quantity;
    filesData[fileIndex].scale = scale;
    
    // Update hidden form field
    const filesDataInput = document.querySelector('input[name="files_data"]');
    if (filesDataInput) {
        filesDataInput.value = JSON.stringify(filesData);
    }
    
    // Recalculate total price
    updateTotalPrice();
}

// Update total price for all files
function updateTotalPrice() {
    let totalPrice = 0;
    let totalVolume = 0;
    
    filesData.forEach((file, index) => {
        const quantity = parseInt(document.getElementById(`quantity-${index}`)?.value) || 1;
        const filePrice = parseFloat(file.price) || 0;
        const fileVolume = parseFloat(file.volume) || 0;
        
        totalPrice += filePrice * quantity;
        totalVolume += fileVolume * quantity;
    });
    
    console.log(`Total price: $${totalPrice.toFixed(2)}, Total volume: ${totalVolume.toFixed(2)} cm³`);
    
    // Update total price display - use the ID
    const priceElement = document.getElementById('totalPriceDisplay');
    if (priceElement) {
        priceElement.textContent = '$' + totalPrice.toFixed(2);
        console.log('Updated total price display');
    } else {
        console.error('Total price display element not found');
    }
    
    // Update total volume display
    const volumeElement = document.getElementById('totalVolumeDisplay');
    if (volumeElement) {
        volumeElement.textContent = totalVolume.toFixed(2) + ' cm³';
        console.log('Updated total volume display');
    } else {
        console.error('Total volume display element not found');
    }
    
    // Update hidden fields
    const totalPriceInput = document.querySelector('input[name="total_price"]');
    const totalVolumeInput = document.querySelector('input[name="total_volume"]');
    
    if (totalPriceInput) totalPriceInput.value = totalPrice.toFixed(2);
    if (totalVolumeInput) totalVolumeInput.value = totalVolume.toFixed(2);
}


// Material price multipliers (relative to base price)
const materialPriceMultipliers = {
    'pla': 1.0,
    'abs': 1.1,
    'petg': 1.15,
    'nylon': 1.3,
    'resin': 1.25,
    'biocompatible_resin': 2.5
};

// Color price multipliers
const colorPriceMultipliers = {
    '#FFE5B4': 1.0,  // General - Peach
    '#F5F5DC': 1.2,  // Dental White
    '#FFB6C1': 1.2,  // Gum Pink
    '#E8F4F8': 1.15, // Clear
    '#0047AD': 1.0,  // Medical Blue
    '#ffffff': 1.0,  // White
    '#2c3e50': 1.0,  // Dark Gray
    '#3498db': 1.0,  // Light Blue
    '#e74c3c': 1.05, // Red
    '#2ecc71': 1.05, // Green
    '#f39c12': 1.05, // Orange
    '#9b59b6': 1.05  // Purple
};

// Modal functions
function openSimpleFileModal(formType, fileIndex) {
    const file = filesData[fileIndex];
    currentFileIndex = fileIndex;

    if (!file) {
        alert('File data not found');
        return;
    }

    // Determine viewer type first
    const isDental = (viewerType === 'dental' || viewerType === 'dental-viewer');
    const isGeneral = (viewerType === 'general');

    console.log('🔍 Opening modal for file:', file);
    console.log('  Viewer type:', viewerType);
    console.log('  Is Dental:', isDental);
    console.log('  Is General:', isGeneral);

    // Update modal content
    document.getElementById('simpleModalFileName').textContent = file.name || 'Unknown File';
    
    // Populate Technology options
    const techSelect = document.getElementById('simpleTechSelect');
    if (isDental) {
        techSelect.innerHTML = `<option value="sla">SLA / DLP (Stereolithography / Digital Light Processing)</option>`;
        techSelect.value = 'sla';
    } else if (isGeneral) {
        techSelect.innerHTML = `
            <option value="fdm">FDM (Fused Deposition Modeling)</option>
            <option value="sla">SLA (Stereolithography)</option>
            <option value="sls">SLS (Selective Laser Sintering)</option>
        `;
        techSelect.value = (file.technology || 'fdm').toLowerCase();
    } else {
        // Medical
        techSelect.innerHTML = `
            <option value="fdm">FDM (Fused Deposition Modeling)</option>
            <option value="sla">SLA (Stereolithography)</option>
            <option value="sls">SLS (Selective Laser Sintering)</option>
            <option value="dmls">DMLS (Direct Metal Laser Sintering)</option>
            <option value="mjf">MJF (Multi Jet Fusion)</option>
        `;
        techSelect.value = (file.technology || 'fdm').toLowerCase();
    }

    // Populate Material options
    const materialSelect = document.getElementById('simpleMaterialSelect');
    if (isDental) {
        materialSelect.innerHTML = `<option value="biocompatible_resin">Biocompatible Resins (Certified)</option>`;
        materialSelect.value = 'biocompatible_resin';
    } else if (isGeneral) {
        materialSelect.innerHTML = `
            <option value="pla">PLA</option>
            <option value="abs">ABS</option>
            <option value="petg">PETG</option>
            <option value="nylon">Nylon</option>
        `;
        materialSelect.value = (file.material || 'pla').toLowerCase();
    } else {
        // Medical
        materialSelect.innerHTML = `
            <option value="pla">PLA</option>
            <option value="abs">ABS</option>
            <option value="petg">PETG</option>
            <option value="nylon">Nylon</option>
            <option value="resin">Resin</option>
        `;
        materialSelect.value = (file.material || 'pla').toLowerCase();
    }

    // Populate Color options
    const colorPicker = document.getElementById('simpleColorPicker');
    if (!colorPicker) {
        console.error('❌ Color picker element not found!');
        return;
    }
    
    let colors = [];
    let defaultColor = '';
    
    if (isDental) {
        colors = ['#F5F5DC', '#FFB6C1', '#E8F4F8'];
        defaultColor = '#F5F5DC';
    } else if (isGeneral) {
        colors = ['#FFE5B4'];
        defaultColor = '#FFE5B4';
    } else {
        // Medical
        colors = ['#0047AD', '#ffffff', '#2c3e50', '#3498db', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6'];
        defaultColor = '#0047AD';
    }

    console.log('🎨 Selected colors for', viewerType, ':', colors);

    const fileColor = file.color || defaultColor;
    colorPicker.innerHTML = '';
    colors.forEach(color => {
        const btn = document.createElement('div');
        btn.className = 'simple-color-btn' + (color.toLowerCase() === fileColor.toLowerCase() ? ' active' : '');
        btn.style.backgroundColor = color;
        if (color === '#ffffff') {
            btn.style.border = '2px solid #dee2e6';
        }
        btn.dataset.color = color;
        btn.onclick = function() {
            document.querySelectorAll('.simple-color-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        };
        colorPicker.appendChild(btn);
    });

    // Reset apply to all toggle
    document.getElementById('applyToAllToggle').checked = false;

    // Show modal
    const modal = document.getElementById('simpleFileModal');
    if (!modal) {
        console.error('❌ Modal element not found!');
        alert('Error: Modal element not found');
        return;
    }
    
    console.log('✅ Opening modal...');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function saveFileSettings() {
    if (currentFileIndex < 0 || !filesData[currentFileIndex]) {
        alert('Invalid file index');
        return;
    }

    // Get updated values
    const technology = document.getElementById('simpleTechSelect').value;
    const material = document.getElementById('simpleMaterialSelect').value;
    const activeColorBtn = document.querySelector('.simple-color-btn.active');
    const color = activeColorBtn ? activeColorBtn.dataset.color : filesData[currentFileIndex].color;
    const applyToAll = document.getElementById('applyToAllToggle').checked;

    // Function to recalculate price for a file based on material/color changes
    function recalculatePrice(fileData, oldMaterial, oldColor, newMaterial, newColor) {
        const currentPrice = parseFloat(fileData.price) || 0;
        if (currentPrice === 0) return 0;

        // Calculate original base price (remove old multipliers)
        const oldMaterialMultiplier = materialPriceMultipliers[oldMaterial.toLowerCase()] || 1.0;
        const oldColorMultiplier = colorPriceMultipliers[oldColor] || 1.0;
        const basePrice = currentPrice / (oldMaterialMultiplier * oldColorMultiplier);

        // Apply new multipliers
        const newMaterialMultiplier = materialPriceMultipliers[newMaterial.toLowerCase()] || 1.0;
        const newColorMultiplier = colorPriceMultipliers[newColor] || 1.0;
        const newPrice = basePrice * newMaterialMultiplier * newColorMultiplier;

        return newPrice;
    }

    if (applyToAll) {
        // Apply to all files
        let totalPrice = 0;
        filesData.forEach((file, index) => {
            const oldMaterial = file.material || 'pla';
            const oldColor = file.color || '#FFE5B4';
            
            file.technology = technology;
            file.material = material;
            file.color = color;
            
            // Recalculate price for each file
            file.price = recalculatePrice(file, oldMaterial, oldColor, material, color);
            totalPrice += file.price;
        });

        // Update total price in hidden field
        document.querySelector('input[name="total_price"]').value = totalPrice.toFixed(2);
        
        console.log('✅ Settings applied to all files. New total price:', totalPrice.toFixed(2));
    } else {
        // Apply to current file only
        const oldMaterial = filesData[currentFileIndex].material || 'pla';
        const oldColor = filesData[currentFileIndex].color || '#FFE5B4';
        
        filesData[currentFileIndex].technology = technology;
        filesData[currentFileIndex].material = material;
        filesData[currentFileIndex].color = color;
        
        // Recalculate price for this file
        filesData[currentFileIndex].price = recalculatePrice(
            filesData[currentFileIndex], 
            oldMaterial, 
            oldColor, 
            material, 
            color
        );

        // Recalculate total price
        const totalPrice = filesData.reduce((sum, file) => sum + (parseFloat(file.price) || 0), 0);
        document.querySelector('input[name="total_price"]').value = totalPrice.toFixed(2);

        console.log('✅ Settings applied to file:', currentFileIndex, 'New price:', filesData[currentFileIndex].price.toFixed(2));
    }

    // Update the hidden form field
    document.querySelector('input[name="files_data"]').value = JSON.stringify(filesData);

    // Update the global hidden fields (use first file's settings as default)
    document.querySelector('input[name="technology"]').value = filesData[0]?.technology || technology;
    document.querySelector('input[name="material"]').value = filesData[0]?.material || material;
    document.querySelector('input[name="color"]').value = filesData[0]?.color || color;

    // Update UI - refresh the file list to show new prices
    updateFileListDisplay();

    // Show success message
    const saveBtn = event.target;
    const originalText = saveBtn.innerHTML;
    saveBtn.innerHTML = '<i class="fas fa-check me-1"></i>Saved!';
    saveBtn.style.background = '#4caf50';

    setTimeout(() => {
        saveBtn.innerHTML = originalText;
        saveBtn.style.background = '#4a90e2';
        closeSimpleModal();
    }, 1000);
}

// Function to update the file list display with new prices
function updateFileListDisplay() {
    // Update the price display in the order summary
    const totalPrice = filesData.reduce((sum, file) => sum + (parseFloat(file.price) || 0), 0);
    const priceElement = document.querySelector('.text-success.fw-bold.fs-5');
    if (priceElement) {
        priceElement.textContent = '$' + totalPrice.toFixed(2);
    }

    // Update individual file prices in the list
    filesData.forEach((file, index) => {
        const fileCard = document.querySelector(`[onclick="openSimpleFileModal('General', ${index})"]`)?.closest('.list-group-item');
        if (fileCard) {
            const priceSpan = fileCard.querySelector('.text-success.fw-bold');
            if (priceSpan) {
                priceSpan.textContent = '$' + (parseFloat(file.price) || 0).toFixed(2);
            }
        }
    });
}

function closeSimpleModal() {
    const modal = document.getElementById('simpleFileModal');
    modal.style.display = 'none';
    document.body.style.overflow = '';
}

// Close on backdrop click
document.getElementById('simpleFileModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeSimpleModal();
    }
});

// Close on ESC key
document.addEventListener('keydown', function(e) {
    const modal = document.getElementById('simpleFileModal');
    if (e.key === 'Escape' && modal && modal.style.display !== 'none') {
        closeSimpleModal();
    }
});

// Initialize page - set up file data and calculate initial totals
document.addEventListener('DOMContentLoaded', function() {
    console.log('Page loaded, initializing...');
    
    // Initialize filesData with scale and quantity from inputs
    filesData.forEach((file, index) => {
        const scaleInput = document.getElementById(`scale-${index}`);
        const quantityInput = document.getElementById(`quantity-${index}`);
        
        if (scaleInput) file.scale = parseFloat(scaleInput.value) || 100;
        if (quantityInput) file.quantity = parseInt(quantityInput.value) || 1;
        
        // Store original values
        if (!file.originalPrice) file.originalPrice = parseFloat(file.price) || 0;
        if (!file.originalVolume) file.originalVolume = parseFloat(file.volume) || 0;
    });
    
    // Update hidden form field
    const filesDataInput = document.querySelector('input[name="files_data"]');
    if (filesDataInput) {
        filesDataInput.value = JSON.stringify(filesData);
    }
    
    // Calculate initial totals
    updateTotalPrice();
    
    console.log('Initialization complete', filesData);
});

// Disable submit button after clicking
document.getElementById('printingOrderForm').addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Submitting...';
});
</script>
@endsection
