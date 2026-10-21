@extends('professional.layout.layout')

@section('title', 'Additional Service Details')

@section('styles')
<style>.show-shell{display:flex}.show-hero{display:flex}.show-grid{display:grid}.show-card__head{display:flex}.show-card__body{display:flex}.custom-modal{display:none}.custom-modal.show{display:flex}</style>
@endsection

@section('content')
<div class="additional-service-show min-h-full bg-slate-50 px-4 py-6 text-slate-900 sm:px-6 lg:px-8">
    <div class="show-shell mx-auto max-w-7xl flex-col gap-6">
        <section class="show-hero flex-col gap-4 rounded-2xl border border-brand-100 bg-gradient-to-br from-white via-brand-50 to-white p-6 shadow-card sm:flex-row sm:items-center sm:justify-between">
            <div class="hero-meta">
                <span class="hero-eyebrow"><i class="fas fa-layer-group"></i>Details</span>
                <h1 class="mt-2 text-2xl font-bold tracking-tight text-brand-950 sm:text-3xl">Additional Service Details</h1>
                <ul class="hero-breadcrumb">
                    <li><a href="{{ route('professional.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('professional.additional-services.index') }}">Additional Services</a></li>
                    <li class="active" aria-current="page">Details</li>
                </ul>
            </div>
            <div class="status-pills">
                @if($additionalService->admin_status === 'pending')
                    <span class="status-pill pending"><i class="fas fa-hourglass-half"></i>Pending Review</span>
                @elseif($additionalService->admin_status === 'approved')
                    <span class="status-pill approved"><i class="fas fa-badge-check"></i>Approved</span>
                @elseif($additionalService->admin_status === 'rejected')
                    <span class="status-pill rejected"><i class="fas fa-times-circle"></i>Rejected</span>
                @endif
                @if($additionalService->payment_status === 'paid')
                    <span class="status-pill paid"><i class="fas fa-wallet"></i>Paid</span>
                @endif
            </div>
        </section>

        <div class="show-grid gap-6 lg:grid-cols-2">
            <article class="show-card overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-card">
                <header class="show-card__head border-b border-slate-100 px-5 py-4">
                    <h2 class="font-semibold text-brand-950"><i class="fas fa-clipboard-list"></i> Service Overview</h2>
                </header>
                <div class="show-card__body flex-col gap-5 p-5">
                    <div class="info-grid grid gap-4 sm:grid-cols-2">
                        <div class="info-block">
                            <label>Service Name</label>
                            <p>{{ $additionalService->service_name }}</p>
                        </div>
                        <div class="info-block">
                            <label>Consultation Status</label>
                            <p>
                                @if($additionalService->consulting_status === 'pending')
                                    <span class="status-pill pending">Pending</span>
                                @elseif($additionalService->consulting_status === 'in_progress')
                                    <span class="status-pill info">In Progress</span>
                                @elseif($additionalService->consulting_status === 'done')
                                    <span class="status-pill approved">Completed</span>
                                @endif
                            </p>
                        </div>
                        @if($additionalService->delivery_date)
                            <div class="info-block">
                                <label>Delivery Date</label>
                                <p><i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($additionalService->delivery_date)->format('M d, Y') }}</p>
                            </div>
                        @endif
                        <div class="info-block">
                            <label>Created</label>
                            <p>{{ $additionalService->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>

                    <div class="description-card">
                        <strong><i class="fas fa-align-left"></i> Reason for Additional Service</strong>
                        <p>{{ $additionalService->reason }}</p>
                    </div>

                    <div class="price-breakdown rounded-xl border border-brand-100 bg-brand-50/60 p-5">
                        <h3><i class="fas fa-coins"></i> Price Breakdown</h3>
                        @php
                            $effectiveBasePrice = $additionalService->getEffectiveBasePrice();
                            $cgst = $additionalService->cgst ?? ($effectiveBasePrice * 0.09);
                            $sgst = $additionalService->sgst ?? ($effectiveBasePrice * 0.09);
                            $totalGst = $cgst + $sgst;
                            $effectiveTotalPrice = $additionalService->getEffectiveTotalPrice();
                        @endphp
                        <div class="price-row">
                            <span class="price-label">Base Price</span>
                            <span class="price-value">₹{{ number_format($effectiveBasePrice, 2) }}</span>
                        </div>
                        <div class="price-row">
                            <span class="price-label">GST (18%)</span>
                            <span class="price-value">₹{{ number_format($totalGst, 2) }} <small>(CGST {{ number_format($cgst, 2) }}, SGST {{ number_format($sgst, 2) }})</small></span>
                        </div>
                        <div class="price-row">
                            <span class="price-label">Negotiation Status</span>
                            <span class="price-value">
                                @if($additionalService->negotiation_status === 'none')
                                    No Negotiation
                                @elseif($additionalService->negotiation_status === 'user_negotiated')
                                    Awaiting Response
                                @else
                                    Completed
                                @endif
                            </span>
                        </div>
                        <div class="price-row">
                            <span class="price-label">Negotiated Values</span>
                            <span class="price-value">
                                @if($additionalService->user_negotiated_price)
                                    Customer: ₹{{ number_format($additionalService->user_negotiated_price, 2) }}<br>
                                @endif
                                @if($additionalService->admin_final_negotiated_price)
                                    Final: ₹{{ number_format($additionalService->admin_final_negotiated_price, 2) }}
                                @else
                                    <small>No admin update yet</small>
                                @endif
                            </span>
                        </div>
                        <div class="price-row">
                            <span class="price-label">Final Price</span>
                            <span class="price-value total">₹{{ number_format($effectiveTotalPrice, 2) }}</span>
                        </div>
                    </div>

                    @if($additionalService->negotiation_status === 'admin_responded')
                        <div class="alert-card success">
                            <strong><i class="fas fa-handshake"></i> Negotiation Completed</strong>
                            <p>
                                Original Price: ₹{{ number_format($additionalService->total_price, 2) }} → New Price: ₹{{ number_format($additionalService->final_price, 2) }}
                                <br>
                                <span>{{ $additionalService->admin_negotiation_response }}</span>
                            </p>
                        </div>
                    @elseif($additionalService->negotiation_status === 'user_negotiated')
                        <div class="alert-card warning">
                            <strong><i class="fas fa-comments"></i> Customer Price Negotiation Pending</strong>
                            <p>Requested Price: ₹{{ number_format($additionalService->user_negotiated_price, 2) }}</p>
                            <p>{{ $additionalService->user_negotiation_reason }}</p>

                            <form id="negotiationResponseForm" class="form-card">
                                @csrf
                                <h4><i class="fas fa-reply"></i> Respond to Negotiation</h4>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="professional_final_price">Your Final Price (₹)</label>
                                        <input type="number" id="professional_final_price" name="professional_final_price" class="form-control" step="0.01" min="{{ $additionalService->base_price * 0.5 }}" value="{{ $additionalService->user_negotiated_price }}" required>
                                        <div class="price-error" style="display:none"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="professional_response">Your Response</label>
                                        <textarea id="professional_response" name="professional_response" class="form-control" rows="4" maxlength="1000" placeholder="Explain your final decision..." required></textarea>
                                        <small class="form-text">Max 1000 characters</small>
                                    </div>
                                </div>
                                <button type="submit" class="action-btn success">
                                    <i class="fas fa-paper-plane"></i> Send Response
                                </button>
                            </form>
                        </div>
                    @endif

                    @if($additionalService->admin_status === 'approved' && $additionalService->negotiation_status !== 'user_negotiated')
                        <form id="priceUpdateForm" class="form-card">
                            @csrf
                            <h4><i class="fas fa-edit"></i> Update Service Price</h4>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="new_price">New Price (₹)</label>
                                    <input type="number" id="new_price" name="new_price" class="form-control" step="0.01" min="{{ $additionalService->base_price * 0.5 }}" value="{{ $additionalService->final_price }}" required>
                                    <div class="price-error" style="display:none"></div>
                                    <small class="form-text">Minimum price enforced automatically.</small>
                                </div>
                                <div class="form-group">
                                    <label for="price_reason">Reason for Update</label>
                                    <textarea id="price_reason" name="reason" class="form-control" rows="4" maxlength="1000" placeholder="Explain why you're updating the price..." required></textarea>
                                    <small class="form-text">Customers will be notified of your reasoning.</small>
                                </div>
                            </div>
                            <button type="submit" class="action-btn primary">
                                <i class="fas fa-save"></i> Update Price
                            </button>
                        </form>
                    @endif

                    @if($additionalService->admin_status === 'rejected' && $additionalService->admin_reason)
                        <div class="alert-card danger">
                            <strong><i class="fas fa-times-circle"></i> Rejection Reason</strong>
                            <p>{{ $additionalService->admin_reason }}</p>
                        </div>
                    @endif
                </div>
            </article>

            <div class="show-sidebar space-y-6">
                <section class="info-aside-card rounded-2xl border border-brand-100 bg-white p-5 shadow-card">
                    <h3 class="mb-4 font-semibold text-brand-950"><i class="fas fa-user-circle"></i> Customer Information</h3>
                    <div class="info-grid grid gap-4 sm:grid-cols-2">
                        <div class="info-block">
                            <label>Customer Name</label>
                            <p>{{ $additionalService->user->name }}</p>
                        </div>
                        <div class="info-block">
                            <label>Email</label>
                            <p>{{ $additionalService->user->email }}</p>
                        </div>
                        <div class="info-block">
                            <label>Phone</label>
                            <p>{{ $additionalService->user->phone }}</p>
                        </div>
                    </div>
                </section>

                <section class="info-aside-card rounded-2xl border border-brand-100 bg-white p-5 shadow-card">
                    <h3 class="mb-4 font-semibold text-brand-950"><i class="fas fa-calculator"></i> Payment & Commission</h3>
                    <div class="info-grid grid gap-4 sm:grid-cols-2">
                        <div class="info-block">
                            <label>Payment Status</label>
                            <p>
                                @if($additionalService->payment_status === 'pending')
                                    <span class="status-pill pending">Pending</span>
                                @elseif($additionalService->payment_status === 'paid')
                                    <span class="status-pill paid">Paid</span>
                                @elseif($additionalService->payment_status === 'failed')
                                    <span class="status-pill rejected">Failed</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    @php
                        $professional = $additionalService->professional;
                        $finalPrice = $additionalService->final_price;
                        $serviceRequestMargin = $professional->service_request_margin ?? 0;
                        $serviceRequestOffset = $professional->service_request_offset ?? 0;
                        $platformCommission = ($finalPrice * $serviceRequestMargin) / 100;
                        $professionalEarning = $finalPrice - $platformCommission;
                    @endphp
                    <div class="commission-grid">
                        <div class="commission-card">
                            <span>Platform Margin</span>
                            <strong>{{ number_format($serviceRequestMargin, 2) }}%</strong>
                            <small>Applied on service price</small>
                        </div>
                        <div class="commission-card danger">
                            <span>Commission Amount</span>
                            <strong>₹{{ number_format($platformCommission, 2) }}</strong>
                            <small>Deducted by platform</small>
                        </div>
                        <div class="commission-card success">
                            <span>Your Earnings</span>
                            <strong>₹{{ number_format($professionalEarning, 2) }}</strong>
                            <small>After commission</small>
                        </div>
                    </div>
                    @if($additionalService->payment_status === 'paid')
                        <div class="alert-card success">
                            <strong><i class="fas fa-check-circle"></i> Payment Received</strong>
                            <p>You will receive ₹{{ number_format($professionalEarning, 2) }} ({{ number_format(100 - $serviceRequestMargin, 2) }}% of ₹{{ number_format($finalPrice, 2) }})</p>
                        </div>
                    @endif
                    @if($additionalService->professional_payment_status === 'processed')
                        <div class="alert-card">
                            <strong><i class="fas fa-money-bill-wave"></i> Payment Released</strong>
                            <p>Processed on {{ $additionalService->professional_payment_processed_at->format('M d, Y h:i A') }}</p>
                            @if($additionalService->payment_transaction_id)
                                <p>Transaction ID: {{ $additionalService->payment_transaction_id }}</p>
                            @endif
                        </div>
                    @endif
                </section>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<div class="custom-modal" id="startConsultationModal">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">🚀 Start Consultation</h5>
            <button type="button" class="modal-close" onclick="closeModal('startConsultationModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="startConsultationForm">
            <div class="modal-body">
                <div class="alert-card">
                    <strong><i class="fas fa-info-circle"></i> Heads up!</strong>
                    <p>This will mark the consultation as "In Progress" and notify the customer.</p>
                </div>
                <div class="form-group">
                    <label for="consultation_notes">Consultation Notes (Optional)</label>
                    <textarea class="form-control" id="consultation_notes" name="consultation_notes" rows="4" placeholder="Add any initial notes or instructions..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="action-btn outline" onclick="closeModal('startConsultationModal')">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" class="action-btn success">
                    <i class="fas fa-play"></i> Start Consultation
                </button>
            </div>
        </form>
    </div>
</div>

<div class="custom-modal" id="markCompletedModal">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">✅ Mark Consultation Completed</h5>
            <button type="button" class="modal-close" onclick="closeModal('markCompletedModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="markCompletedForm">
            <div class="modal-body">
                <div class="alert-card success">
                    <strong><i class="fas fa-check-circle"></i> Confirmation</strong>
                    <p>This action notifies the customer to confirm the consultation completion.</p>
                </div>
                <div class="form-group">
                    <label for="completion_notes">Completion Summary *</label>
                    <textarea class="form-control" id="completion_notes" name="completion_notes" rows="4" placeholder="Provide a summary of the work completed..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="action-btn outline" onclick="closeModal('markCompletedModal')">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" class="action-btn success">
                    <i class="fas fa-check-circle"></i> Mark Completed
                </button>
            </div>
        </form>
    </div>
</div>

<div class="custom-modal" id="updateDeliveryModal">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">📅 Update Delivery Date</h5>
            <button type="button" class="modal-close" onclick="closeModal('updateDeliveryModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="updateDeliveryForm">
            <div class="modal-body">
                @if($additionalService->delivery_date)
                    <div class="alert-card">
                        <strong>Current Delivery Date:</strong>
                        <p>{{ \Carbon\Carbon::parse($additionalService->delivery_date)->format('M d, Y') }}</p>
                    </div>
                @endif
                <div class="form-group">
                    <label for="new_delivery_date">New Delivery Date *</label>
                    <input type="date" class="form-control" id="new_delivery_date" name="delivery_date" min="{{ date('Y-m-d') }}" value="{{ $additionalService->delivery_date }}" required>
                </div>
                <div class="form-group">
                    <label for="delivery_notes">Reason for Date Change *</label>
                    <textarea class="form-control" id="delivery_notes" name="delivery_notes" rows="3" placeholder="Explain why the delivery date is being changed..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="action-btn outline" onclick="closeModal('updateDeliveryModal')">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" class="action-btn warning">
                    <i class="fas fa-calendar"></i> Update Date
                </button>
            </div>
        </form>
    </div>
</div>

<div class="custom-modal" id="setDeliveryDateModal">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">📅 Set Delivery Date</h5>
            <button type="button" class="modal-close" onclick="closeModal('setDeliveryDateModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="setDeliveryDateForm">
            <div class="modal-body">
                <div class="alert-card">
                    <strong><i class="fas fa-info-circle"></i> Note</strong>
                    <p>Customers and admin will be notified once you set a delivery date.</p>
                </div>
                <div class="form-group">
                    <label for="delivery_date">Delivery Date *</label>
                    <input type="date" class="form-control" id="delivery_date" name="delivery_date" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                </div>
                <div class="form-group">
                    <label for="delivery_reason">Reason/Notes *</label>
                    <textarea class="form-control" id="delivery_reason" name="delivery_reason" rows="3" placeholder="Explain the delivery plan..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="action-btn outline" onclick="closeModal('setDeliveryDateModal')">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" class="action-btn info">
                    <i class="fas fa-calendar-plus"></i> Set Date
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    const currentServiceId = {{ $additionalService->id }};

    function openModal(modalId) {
        const $modal = $('#' + modalId);
        $modal.addClass('show');
        $('body').css('overflow', 'hidden');
        setTimeout(() => {
            const $firstInput = $modal.find('input:not([type="hidden"]), textarea').first();
            if ($firstInput.length) $firstInput.focus();
        }, 120);
    }

    function closeModal(modalId) {
        $('#' + modalId).removeClass('show');
        $('body').css('overflow', 'auto');
    }

    window.closeModal = closeModal;

    $('.custom-modal').on('click', function(e) {
        if ($(e.target).hasClass('custom-modal')) {
            closeModal(this.id);
        }
    });

    function showButtonLoading($btn, loadingText) {
        const originalText = $btn.html();
        $btn.data('original-text', originalText);
        $btn.prop('disabled', true).html(`<i class="fas fa-spinner fa-spin"></i> ${loadingText}`);
        return originalText;
    }

    function hideButtonLoading($btn) {
        const originalText = $btn.data('original-text');
        $btn.prop('disabled', false).html(originalText);
    }

    $(document).on('click', '.start-consultation', function(e) {
        e.preventDefault();
        openModal('startConsultationModal');
    });

    $('#startConsultationForm').submit(function(e) {
        e.preventDefault();
        const $submitBtn = $(this).find('button[type="submit"]');
        showButtonLoading($submitBtn, 'Starting...');

        $.ajax({
            url: `/professional/additional-services/${currentServiceId}/start-consultation`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                consultation_notes: $('#consultation_notes').val()
            },
            success: function(response) {
                if (response.success) {
                    closeModal('startConsultationModal');
                    toastr?.success(response.message || 'Consultation started successfully!');
                    setTimeout(() => location.reload(), 1200);
                } else {
                    toastr?.error(response.message || 'Failed to start consultation');
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'An error occurred. Please try again.';
                toastr?.error(message);
            },
            complete: function() {
                hideButtonLoading($submitBtn);
            }
        });
    });

    $(document).on('click', '.mark-completed', function(e) {
        e.preventDefault();
        openModal('markCompletedModal');
    });

    $('#markCompletedForm').submit(function(e) {
        e.preventDefault();
        const $submitBtn = $(this).find('button[type="submit"]');
        showButtonLoading($submitBtn, 'Completing...');

        $.ajax({
            url: `/professional/additional-services/${currentServiceId}/mark-completed`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                completion_notes: $('#completion_notes').val()
            },
            success: function(response) {
                if (response.success) {
                    closeModal('markCompletedModal');
                    toastr?.success(response.message || 'Consultation marked as completed!');
                    setTimeout(() => location.reload(), 1200);
                } else {
                    toastr?.error(response.message || 'Failed to mark consultation as completed');
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    Object.values(errors).forEach(function(error) {
                        const message = Array.isArray(error) ? error[0] : error;
                        toastr?.error(message);
                    });
                } else {
                    toastr?.error(xhr.responseJSON?.message || 'An error occurred. Please try again.');
                }
            },
            complete: function() {
                hideButtonLoading($submitBtn);
            }
        });
    });

    $(document).on('click', '.set-delivery-date', function(e) {
        e.preventDefault();
        openModal('setDeliveryDateModal');
    });

    $('#setDeliveryDateForm').submit(function(e) {
        e.preventDefault();
        const $submitBtn = $(this).find('button[type="submit"]');
        showButtonLoading($submitBtn, 'Setting...');

        $.ajax({
            url: `/professional/additional-services/${currentServiceId}/set-delivery-date`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                delivery_date: $('#delivery_date').val(),
                delivery_reason: $('#delivery_reason').val()
            },
            success: function(response) {
                if (response.success) {
                    closeModal('setDeliveryDateModal');
                    toastr?.success(response.message || 'Delivery date set successfully!');
                    setTimeout(() => location.reload(), 1200);
                } else {
                    toastr?.error(response.message || 'Failed to set delivery date');
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    Object.values(errors).forEach(function(error) {
                        const message = Array.isArray(error) ? error[0] : error;
                        toastr?.error(message);
                    });
                } else {
                    toastr?.error(xhr.responseJSON?.message || 'An error occurred. Please try again.');
                }
            },
            complete: function() {
                hideButtonLoading($submitBtn);
            }
        });
    });

    $(document).on('click', '.update-delivery', function(e) {
        e.preventDefault();
        openModal('updateDeliveryModal');
    });

    $('#updateDeliveryForm').submit(function(e) {
        e.preventDefault();
        const $submitBtn = $(this).find('button[type="submit"]');
        showButtonLoading($submitBtn, 'Updating...');

        $.ajax({
            url: `/professional/additional-services/${currentServiceId}/update-delivery-date`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                delivery_date: $('#new_delivery_date').val(),
                delivery_notes: $('#delivery_notes').val()
            },
            success: function(response) {
                if (response.success) {
                    closeModal('updateDeliveryModal');
                    toastr?.success(response.message || 'Delivery date updated successfully!');
                    setTimeout(() => location.reload(), 1200);
                } else {
                    toastr?.error(response.message || 'Failed to update delivery date');
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    Object.values(errors).forEach(function(error) {
                        const message = Array.isArray(error) ? error[0] : error;
                        toastr?.error(message);
                    });
                } else {
                    toastr?.error(xhr.responseJSON?.message || 'An error occurred. Please try again.');
                }
            },
            complete: function() {
                hideButtonLoading($submitBtn);
            }
        });
    });

    $(document).on('click', '.complete-consultation', function(e) {
        e.preventDefault();
        const $btn = $(this);
        if (confirm('Confirm marking this consultation as completed? The customer will be notified.')) {
            showButtonLoading($btn, 'Completing...');
            $.ajax({
                url: `/professional/additional-services/${currentServiceId}/complete-consultation`,
                type: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        toastr?.success(response.message || 'Consultation completed successfully!');
                        setTimeout(() => location.reload(), 1200);
                    } else {
                        toastr?.error(response.message || 'Failed to complete consultation');
                    }
                },
                error: function(xhr) {
                    toastr?.error(xhr.responseJSON?.message || 'An error occurred. Please try again.');
                },
                complete: function() {
                    hideButtonLoading($btn);
                }
            });
        }
    });

    function validatePrice(priceInput, minPrice) {
        const price = parseFloat(priceInput.val());
        const minimum = parseFloat(minPrice);
        const errorDiv = priceInput.siblings('.price-error');
        if (price < minimum) {
            errorDiv.text('Price cannot be below the allowed minimum').show();
            priceInput.addClass('is-invalid');
            return false;
        }
        errorDiv.hide();
        priceInput.removeClass('is-invalid');
        return true;
    }

    $('#professional_final_price, #new_price').on('input change', function() {
        const minPrice = $(this).attr('min');
        validatePrice($(this), minPrice);
    });

    $('#negotiationResponseForm').submit(function(e) {
        e.preventDefault();
        const priceInput = $('#professional_final_price');
        const minPrice = priceInput.attr('min');
        if (!validatePrice(priceInput, minPrice)) { return false; }
        const $submitBtn = $(this).find('button[type="submit"]');
        showButtonLoading($submitBtn, 'Sending...');

        $.ajax({
            url: `/professional/additional-services/${currentServiceId}/respond-negotiation`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                professional_final_price: priceInput.val(),
                professional_response: $('#professional_response').val(),
                min_price: minPrice
            },
            success: function(response) {
                if (response.success) {
                    toastr?.success(response.message || 'Negotiation response sent!');
                    setTimeout(() => location.reload(), 1200);
                } else {
                    toastr?.error(response.message || 'Failed to send negotiation response');
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    Object.values(errors).forEach(function(error) {
                        const message = Array.isArray(error) ? error[0] : error;
                        toastr?.error(message);
                    });
                } else {
                    toastr?.error(xhr.responseJSON?.message || 'An error occurred. Please try again.');
                }
            },
            complete: function() {
                hideButtonLoading($submitBtn);
            }
        });
    });

    $('#priceUpdateForm').submit(function(e) {
        e.preventDefault();
        const priceInput = $('#new_price');
        const minPrice = priceInput.attr('min');
        if (!validatePrice(priceInput, minPrice)) { return false; }
        const $submitBtn = $(this).find('button[type="submit"]');
        showButtonLoading($submitBtn, 'Updating...');

        $.ajax({
            url: `/professional/additional-services/${currentServiceId}/update-price`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                new_price: priceInput.val(),
                reason: $('#price_reason').val(),
                min_price: minPrice
            },
            success: function(response) {
                if (response.success) {
                    toastr?.success(response.message || 'Service price updated successfully!');
                    setTimeout(() => location.reload(), 1200);
                } else {
                    toastr?.error(response.message || 'Failed to update service price');
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    Object.values(errors).forEach(function(error) {
                        const message = Array.isArray(error) ? error[0] : error;
                        toastr?.error(message);
                    });
                } else {
                    toastr?.error(xhr.responseJSON?.message || 'An error occurred. Please try again.');
                }
            },
            complete: function() {
                hideButtonLoading($submitBtn);
            }
        });
    });
});
</script>
@endsection