@extends('customer.layout.layout')

@section('title', 'Additional Service Details')

@section('styles')


<style>.custom-modal{display:none}.custom-modal.show{display:flex}</style>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <div class="page-title">
            <h3>Additional Service Details</h3>
            <p>Review your add-on request, track negotiation updates, and complete payment when you are satisfied with the offer.</p>
        </div>
        <ul class="breadcrumb">
            <li><a href="{{ route('user.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('user.additional-services.index') }}">Additional Services</a></li>
            <li class="active">Details</li>
        </ul>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-info-circle section-icon"></i>
                        Service Information
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="d-block"><strong>Service Name:</strong></label>
                            <p class="mb-0">{{ $additionalService->service_name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="d-block"><strong>Status:</strong></label>
                            <p class="mb-0">{{ $additionalService->status_text }}</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="d-block"><strong>Reason:</strong></label>
                        <p class="mb-0">{{ $additionalService->reason }}</p>
                    </div>

                    @php
                        // Get the original professional price using the model method
                        $originalProfessionalBasePrice = $additionalService->original_professional_price;
                        
                        // For Professional's Price Breakdown - always use original
                        $originalCGST = $originalProfessionalBasePrice * 0.09;
                        $originalSGST = $originalProfessionalBasePrice * 0.09;
                        $originalTotalGST = $originalCGST + $originalSGST;
                        $originalFinalPrice = $originalProfessionalBasePrice + $originalTotalGST;
                        
                        // Check if customer has negotiated
                        $hasNegotiation = in_array($additionalService->negotiation_status, ['user_negotiated', 'admin_responded']);
                        
                        if ($hasNegotiation) {
                            // For Negotiated Price Breakdown - show the negotiated amount
                            if ($additionalService->negotiation_status === 'admin_responded') {
                                // Professional accepted - show final negotiated price
                                $negotiatedBasePrice = $additionalService->admin_final_negotiated_price;
                            } else {
                                // Still pending - show customer's offer
                                $negotiatedBasePrice = $additionalService->user_negotiated_price;
                            }
                            
                            // Calculate discount and new price based on ORIGINAL price
                            $discountAmount = $originalProfessionalBasePrice - $negotiatedBasePrice;
                            $discountPercent = ($originalProfessionalBasePrice > 0) ? (($discountAmount / $originalProfessionalBasePrice) * 100) : 0;
                            
                            $negotiatedCGST = $negotiatedBasePrice * 0.09;
                            $negotiatedSGST = $negotiatedBasePrice * 0.09;
                            $negotiatedTotalGST = $negotiatedCGST + $negotiatedSGST;
                            $negotiatedFinalPrice = $negotiatedBasePrice + $negotiatedTotalGST;
                        }
                    @endphp

                    <!-- Professional's Price Breakdown -->
                    <div class="card mb-3 price-breakdown">
                        <div class="card-header">
                            <h6 class="mb-0">{{ getCurrencySymbol() }} Professional's Initial Price Breakdown</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless mb-0 price-table">
                                <tbody>
                                    <tr>
                                        <td><strong>Description</strong></td>
                                        <td class="text-end"><strong>Amount</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Base Price</strong></td>
                                        <td class="text-end"><strong>{{ getCurrencySymbol() }}{{ number_format(convertCurrency($originalProfessionalBasePrice), 2) }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><strong>Tax Breakdown</strong></td>
                                    </tr>
                                    <tr>
                                        <td>CGST 9%</td>
                                        <td class="text-end">{{ getCurrencySymbol() }}{{ number_format(convertCurrency($originalCGST), 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>SGST 9%</td>
                                        <td class="text-end">{{ getCurrencySymbol() }}{{ number_format(convertCurrency($originalSGST), 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total GST (18%)</strong></td>
                                        <td class="text-end"><strong>{{ getCurrencySymbol() }}{{ number_format(convertCurrency($originalTotalGST), 2) }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Final Amount</strong></td>
                                        <td class="text-end"><h5 class="mb-0"><strong>{{ getCurrencySymbol() }}{{ number_format(convertCurrency($originalFinalPrice), 2) }}</strong></h5></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($hasNegotiation)
                    <!-- Negotiated Price Breakdown -->
                    <div class="card mb-4 negotiated-price-card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-handshake"></i>
                                Negotiated Price Breakdown
                                @if($additionalService->negotiation_status === 'admin_responded')
                                    <span class="badge badge-paid ms-2">✅ Accepted</span>
                                @else
                                    <span class="badge badge-pending ms-2">⏳ Pending</span>
                                @endif
                            </h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless mb-0 price-table">
                                <tbody>
                                    <tr>
                                        <td><strong>Description</strong></td>
                                        <td class="text-end"><strong>Amount</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Base Price</td>
                                        <td class="text-end">{{ getCurrencySymbol() }}{{ number_format(convertCurrency($originalProfessionalBasePrice), 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Negotiation Discount ({{ number_format($discountPercent, 1) }}%)</td>
                                        <td class="text-end text-warning">− {{ getCurrencySymbol() }}{{ number_format(convertCurrency($discountAmount), 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>New Price After Discount</strong></td>
                                        <td class="text-end"><strong>{{ getCurrencySymbol() }}{{ number_format(convertCurrency($negotiatedBasePrice), 2) }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td>GST 18% (9%+9%)</td>
                                        <td class="text-end">{{ getCurrencySymbol() }}{{ number_format(convertCurrency($negotiatedTotalGST), 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Final Price Customer Pays</strong></td>
                                        <td class="text-end"><h5 class="mb-0 text-warning"><strong>{{ getCurrencySymbol() }}{{ number_format(convertCurrency($negotiatedFinalPrice), 2) }}</strong></h5></td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            <div class="mt-3 text-center">
                                @if($additionalService->negotiation_status === 'admin_responded')
                                    @php
                                        $totalSavings = $originalFinalPrice - $negotiatedFinalPrice;
                                    @endphp
                                    <small class="text-success d-block">✅ Negotiated price accepted - Ready for payment</small>
                                    <small class="text-success"><i class="fas fa-piggy-bank"></i> You save: <strong>{{ getCurrencySymbol() }}{{ number_format(convertCurrency($totalSavings), 2) }}</strong> total</small>
                                @else
                                    <small class="text-warning d-block">⏳ Your negotiated offer is pending professional review</small>
                                    <small class="d-block mt-1">Once accepted, you'll pay {{ getCurrencySymbol() }}{{ number_format(convertCurrency($negotiatedFinalPrice), 2) }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($additionalService->delivery_date)
                    <div class="mb-3">
                        <label class="d-block"><strong>Delivery Date:</strong></label>
                        <p class="mb-0">{{ \Carbon\Carbon::parse($additionalService->delivery_date)->format('M d, Y') }}</p>
                    </div>
                    @endif

                    @if($additionalService->negotiation_status !== 'none')
                    <div class="alert {{ $additionalService->negotiation_status === 'user_negotiated' ? 'alert-warning' : 'alert-info' }}">
                        <h5 class="mb-3">
                            @if($additionalService->negotiation_status === 'user_negotiated')
                                ⏳ Negotiation Pending Review
                            @else
                                ✅ Negotiation Accepted
                            @endif
                        </h5>
                        @if($additionalService->negotiation_status === 'user_negotiated')
                            @php
                                $negotiationHandler = $additionalService->professional_id ? 'professional' : 'admin';
                                $handlerLabel = $negotiationHandler === 'professional' ? 'Professional' : 'Admin';
                            @endphp
                            
                            <div class="mb-2">
                                <strong>Your Negotiated Offer:</strong> 
                                <span class="text-success">₹{{ number_format($additionalService->user_negotiated_price, 2) }}</span>
                            </div>
                            <div class="mb-2">
                                <strong>Your Reason:</strong> 
                                <p class="mb-0 text-muted">{{ $additionalService->user_negotiation_reason }}</p>
                            </div>
                            <p class="text-info mb-0">
                                <i class="fas fa-clock"></i> 
                                <strong>Status:</strong> Waiting for {{ strtolower($handlerLabel) }} response... We'll notify you once {{ strtolower($handlerLabel) }} reviews your negotiation.
                            </p>
                        @elseif($additionalService->negotiation_status === 'admin_responded')
                            @php
                                $modifierLabel = $additionalService->professional_id ? 'Professional' : 'Admin';
                                $originalBasePriceForSavings = $additionalService->base_price; // Use the same original price
                                $finalNegotiatedPrice = $additionalService->admin_final_negotiated_price;
                                $totalSavings = ($originalBasePriceForSavings - $finalNegotiatedPrice) * 1.18;
                                $savingsPercent = ($originalBasePriceForSavings > 0) ? (($originalBasePriceForSavings - $finalNegotiatedPrice) / $originalBasePriceForSavings * 100) : 0;
                            @endphp
                            
                            <div class="mb-2">
                                <strong>Your Base Price Offer:</strong> ₹{{ number_format($additionalService->user_negotiated_price, 2) }}
                            </div>
                            <div class="mb-2">
                                <strong>{{ $modifierLabel }}'s Base Price Final Offer:</strong> 
                                <span class="text-success">₹{{ number_format($finalNegotiatedPrice, 2) }}</span>
                            </div>
                            <div class="mb-2">
                                <strong>{{ $modifierLabel }}'s Response:</strong> 
                                <p class="mb-0 text-muted">{{ $additionalService->admin_negotiation_response }}</p>
                            </div>
                            <div class="mt-2">
                                <small class="text-success">✅ <strong>Good news!</strong> {{ $modifierLabel }} has responded to your negotiation. You can now proceed with payment at the updated price.</small>
                            </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">👨‍💼 Professional Information</h5>
                </div>
                <div class="card-body">
                    <div class="info-card">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <h6>👤 {{ $additionalService->professional->name }}</h6>
                                <p class="mb-1"><i class="fas fa-envelope me-2"></i>{{ $additionalService->professional->email }}</p>
                                <p class="mb-1"><i class="fas fa-phone me-2"></i>{{ $additionalService->professional->phone }}</p>
                                <p class="mb-0"><i class="fas fa-bookmark me-2"></i>Booking: #{{ $additionalService->booking_id }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">⚡ Quick Actions</h5>
                </div>
                <div class="card-body d-grid gap-3 quick-actions">
                    @if($additionalService->payment_status === 'pending')
                        @if($additionalService->negotiation_status === 'user_negotiated')
                            <button class="btn btn-secondary" disabled>
                                <i class="fas fa-clock"></i> 
                                Payment Pending Admin Review
                            </button>
                            <small class="text-muted text-center">
                                <i class="fas fa-info-circle"></i> 
                                Payment will be enabled once admin responds to your negotiation
                            </small>
                        @else
                            <button class="btn btn-success pay-now-btn" data-id="{{ $additionalService->id }}">
                                <i class="fas fa-credit-card"></i> 
                                Pay Now - ₹{{ number_format($additionalService->final_price, 2) }}
                            </button>
                        @endif
                    @endif
                    
                    @if($additionalService->canBeNegotiated())
                    <button class="btn btn-warning negotiate-btn" data-id="{{ $additionalService->id }}">
                        <i class="fas fa-handshake"></i> 
                        Negotiate Price
                    </button>
                    @endif
                    
                    @if($additionalService->consulting_status === 'done' && !$additionalService->customer_confirmed_at)
                    <button class="btn btn-primary confirm-completion-btn" data-id="{{ $additionalService->id }}">
                        <i class="fas fa-check-circle"></i> 
                        Confirm Completion
                    </button>
                    @endif

                    @if($additionalService->consulting_status === 'done' && $additionalService->payment_status === 'paid')
                    <hr class="my-2">
                    <div class="d-grid gap-2">
                        <a href="{{ route('user.additional-services.invoice', $additionalService->id) }}" class="btn btn-success">
                            <i class="fas fa-file-text"></i> View Invoice
                        </a>
                        <a href="{{ route('user.additional-services.invoice.pdf', $additionalService->id) }}" class="btn btn-outline-success" target="_blank">
                            <i class="fas fa-download"></i> Download PDF Invoice
                        </a>
                    </div>
                    @endif

                    <a href="{{ route('user.additional-services.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> 
                        Back to List
                    </a>
                </div>
            </div>

            @if($additionalService->isPaid())
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">💳 Payment Information</h5>
                </div>
                <div class="card-body">
                    <div class="info-card payment-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge badge-paid">✅ Payment Completed</span>
                            <h4 class="mb-0">₹{{ number_format($additionalService->final_price, 2) }}</h4>
                        </div>
                        @if($additionalService->payment_id)
                        <p class="mb-0">
                            <small class="opacity-75">Transaction ID</small><br>
                            <code>{{ $additionalService->payment_id }}</code>
                        </p>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Custom Negotiation Modal -->
<div class="custom-modal" id="negotiationModal">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">💰 Negotiate Service Price</h5>
            <button type="button" class="modal-close" onclick="closeModal('negotiationModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="negotiationForm">
            <div class="modal-body">
                @php
                    $professional = $additionalService->professional;
                    // Use professional->service_request_offset (percentage) for negotiation limit; default to 10%
                    $maxDiscountPercent = $professional->service_request_offset ?? 10;
                    // Effective base price (before GST)
                    $basePrice = $additionalService->getEffectiveBasePrice();
                    // Minimum allowed base price (e.g., 10% discount)
                    $minBasePrice = $minPrice ?? round($basePrice * (1 - ($maxDiscountPercent / 100)), 2);
                    // Final amounts including GST (18%) for display
                    $minFinal = round($minBasePrice * 1.18, 2);
                    $currentFinal = round($basePrice * 1.18, 2);
                @endphp
                
                <div class="alert alert-info">
                    <div class="row">
                        <div class="col-6">
                            <small class="d-block opacity-75">Current Base Price</small>
                            <strong>₹{{ number_format($basePrice, 2) }}</strong>
                            <small class="d-block text-muted">+ GST (18%): ₹{{ number_format($basePrice * 0.18, 2) }}</small>
                            <small class="d-block"><strong>Total: ₹{{ number_format($currentFinal, 2) }}</strong></small>
                        </div>
                        <div class="col-6">
                            <small class="d-block opacity-75">You can negotiate this service</small>
                            <strong class="text-success">💬 Make an offer</strong>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="negotiated_price" class="form-label">
                        <i class="fas fa-tag me-2"></i>Your Proposed Base Price (₹) *
                        <small class="text-muted d-block mt-1">
                            <i class="fas fa-info-circle"></i> 
                            You are negotiating the <strong>base price</strong> (currently ₹{{ number_format($basePrice, 2) }}). 
                            GST (18%) will be calculated on your negotiated price.
                        </small>
                    </label>
                    <input type="number" class="form-control form-control-lg" id="negotiated_price" name="negotiated_price" 
                           data-min-price="{{ $minBasePrice }}" 
                           data-max-price="{{ $basePrice }}"
                           min="{{ $minBasePrice }}"
                           max="{{ $basePrice }}" 
                           step="0.01" 
                           required
                           placeholder="Enter amount between ₹{{ number_format($minBasePrice, 2) }} - ₹{{ number_format($basePrice, 2) }}">
                    
                </div>
                
                <div class="mb-3">
                    <label for="negotiation_reason" class="form-label">
                        <i class="fas fa-comment me-2"></i>Reason for Negotiation *
                    </label>
                    <textarea class="form-control" id="negotiation_reason" name="negotiation_reason" 
                              rows="4" maxlength="1000" required 
                              placeholder="Please explain why you're requesting a price reduction..."></textarea>
                    <div class="form-text">
                        <span id="char-count">0</span>/1000 characters
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" onclick="closeModal('negotiationModal')">
                    <i class="fas fa-times me-2"></i>Cancel
                </button>
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-paper-plane me-2"></i>Submit Negotiation
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Custom Confirm Completion Modal -->
<div class="custom-modal" id="confirmCompletionModal">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">✅ Confirm Service Completion</h5>
            <button type="button" class="modal-close" onclick="closeModal('confirmCompletionModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="text-center mb-4">
                <div class="modal-illustration">
                    <i class="fas fa-check-circle modal-illustration-icon"></i>
                </div>
            </div>
            <h6 class="text-center mb-3">Service Completion Confirmation</h6>
            <p class="text-center">The professional has marked this consultation as completed. Do you confirm that the service has been delivered satisfactorily?</p>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Important:</strong> Once confirmed, this action cannot be undone and the service will be marked as fully completed.
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" onclick="closeModal('confirmCompletionModal')">
                <i class="fas fa-clock me-2"></i>Not Yet
            </button>
            <button type="button" class="btn btn-success" id="confirmCompletionBtn">
                <i class="fas fa-check-circle me-2"></i>Yes, Confirm Completion
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
$(document).ready(function() {
    let currentServiceId = {{ $additionalService->id }};

    // Custom Modal Functions
    function openModal(modalId) {
        document.getElementById(modalId).classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('show');
        document.body.style.overflow = 'auto';
    }

    // Make closeModal available globally
    window.closeModal = closeModal;

    // Close modal when clicking outside
    $('.custom-modal').click(function(e) {
        if (e.target === this) {
            closeModal(this.id);
        }
    });

    // Close modal with Escape key
    $(document).keydown(function(e) {
        if (e.keyCode === 27) {
            $('.custom-modal.show').each(function() {
                closeModal(this.id);
            });
        }
    });

    // Character count for negotiation reason
    $('#negotiation_reason').on('input', function() {
        const length = $(this).val().length;
        $('#char-count').text(length);
        
        if (length > 1000) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });

    // Pay Now functionality with enhanced UI feedback
    $('.pay-now-btn').click(function() {
        const serviceId = $(this).data('id');
        const $btn = $(this);
        const originalText = $btn.html();
        
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
        
        $.ajax({
            url: `/user/additional-services/${serviceId}/create-payment-order`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    const rkey = response.key || response.razorpay_key || '{{ config('services.razorpay.key') }}';
                    const ramount = response.amount || response.order_amount || 0; // amount in paise
                    const rcurrency = response.currency || 'INR';
                    const rorderId = response.order_id || response.orderId || response.order?.id || null;

                    if (!rorderId || !ramount) {
                        toastr.error('Invalid payment order returned from server. Please try again.');
                        $btn.prop('disabled', false).html(originalText);
                        return;
                    }

                    const options = {
                        key: rkey,
                        amount: ramount,
                        currency: rcurrency,
                        name: '{{ config('app.name') }}',
                        description: response.service_name || response.description || 'Additional Service Payment',
                        order_id: rorderId,
                        handler: function (razorpayResponse) {
                            handlePaymentSuccess(serviceId, razorpayResponse);
                        },
                        prefill: {
                            name: '{{ Auth::guard('user')->user()->name }}',
                            email: '{{ Auth::guard('user')->user()->email }}',
                            contact: '{{ Auth::guard('user')->user()->phone }}'
                        },
                        theme: {
                            color: '#667eea'
                        },
                        modal: {
                            ondismiss: function() {
                                toastr.info('Payment cancelled by user');
                                $btn.prop('disabled', false).html(originalText);
                            }
                        }
                    };
                    
                    const rzp = new Razorpay(options);
                    rzp.open();
                    
                    rzp.on('payment.failed', function (response) {
                        toastr.error('Payment failed: ' + response.error.description);
                    });
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                toastr.error('Failed to initiate payment. Please try again.');
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });

    function handlePaymentSuccess(serviceId, paymentResponse) {
        // Show success animation
        toastr.success('Payment initiated successfully! Verifying...');
        
        $.ajax({
            url: `/user/additional-services/${serviceId}/payment-success`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                razorpay_payment_id: paymentResponse.razorpay_payment_id,
                razorpay_order_id: paymentResponse.razorpay_order_id,
                razorpay_signature: paymentResponse.razorpay_signature
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    setTimeout(() => location.reload(), 2000);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                toastr.error('Payment verification failed. Please contact support.');
            }
        });
    }

    // Negotiate functionality with custom modal
    $('.negotiate-btn').click(function() {
        openModal('negotiationModal');
        // Reset form
        $('#negotiationForm')[0].reset();
        $('#char-count').text('0');
    });

    // Submit negotiation with enhanced feedback
    $('#negotiationForm').submit(function(e) {
        e.preventDefault();
        
        const $submitBtn = $(this).find('button[type="submit"]');
        const originalText = $submitBtn.html();
        
        $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Submitting...');
        
        $.ajax({
            url: `/user/additional-services/${currentServiceId}/negotiate`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                negotiated_price: $('#negotiated_price').val(),
                negotiation_reason: $('#negotiation_reason').val()
            },
            success: function(response) {
                if (response.success) {
                    closeModal('negotiationModal');
                    toastr.success(response.message);
                    setTimeout(() => location.reload(), 2000);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                // Handle validation errors
                const serverMessage = xhr.responseJSON?.message;
                const errors = xhr.responseJSON?.errors;
                
                if (serverMessage) {
                    toastr.error(serverMessage);
                } else if (errors) {
                    Object.values(errors).forEach(function(error) {
                        toastr.error(error[0]);
                    });
                } else {
                    toastr.error('An error occurred. Please try again.');
                }
            },
            complete: function() {
                $submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Confirm completion with custom modal
    $('.confirm-completion-btn').click(function() {
        // ensure currentServiceId is set (show page has it prefilled, but set from button for consistency)
        currentServiceId = $(this).data('id') || currentServiceId;
        console.log('Opening confirm modal for service:', currentServiceId);
        openModal('confirmCompletionModal');
    });

    $('#confirmCompletionBtn').click(function() {
        const $btn = $(this);
        const originalText = $btn.html();
        
        // Debug log
        console.log('Confirm completion clicked for serviceId:', currentServiceId);

        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Confirming...');
        
        $.ajax({
            url: `/user/additional-services/${currentServiceId}/confirm-consultation`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log('confirm-consultation success response:', response);
                if (response.success) {
                    closeModal('confirmCompletionModal');
                    toastr.success(response.message);
                    setTimeout(() => location.reload(), 2000);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                console.error('confirm-consultation error:', xhr);
                const serverMessage = xhr.responseJSON?.message;
                if (serverMessage) {
                    toastr.error(serverMessage);
                } else {
                    toastr.error('An error occurred. Please try again.');
                }
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Add smooth animations to cards
    $('.card').each(function(index) {
        $(this).css({
            'animation-delay': (index * 0.1) + 's'
        });
    });

    // Price input validation with real-time feedback
    $('#negotiated_price').on('input', function() {
        const value = parseFloat($(this).val());
        const minPrice = parseFloat($(this).data('min-price'));
        const max = parseFloat($(this).attr('max'));
        const $errorDiv = $('#price-error');
        
        // Real-time GST calculation display
        if (value && !isNaN(value)) {
            const finalAmount = value * 1.18; // Add 18% GST
            const savings = max - value; // Savings from current base
            const savingsPercent = ((savings / max) * 100).toFixed(2);
            
            // Update or create a real-time display
            let $display = $('#real-time-calc');
            if ($display.length === 0) {
                $display = $('<div id="real-time-calc" class="alert alert-success mt-2" style="border-left: 4px solid #198754;"></div>');
                $(this).after($display);
            }
            
            if (value >= minPrice && value <= max) {
                $display.html(`
                    <div class="text-center">
                        <small class="text-muted d-block">Your proposed base price</small>
                        <strong style="font-size: 1.1em;">₹${value.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</strong>
                        <hr class="my-2">
                        <small class="text-success d-block"><strong>Final amount you'll pay (with GST):</strong></small>
                        <strong class="text-success" style="font-size: 1.2em;">₹${finalAmount.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</strong>
                        <small class="d-block text-success mt-1"><i class="fas fa-piggy-bank"></i> You save: <strong>₹${savings.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})} (${savingsPercent}%)</strong></small>
                    </div>
                `).show();
            } else {
                $display.hide();
            }
        }
    });
});
</script>
@endsection