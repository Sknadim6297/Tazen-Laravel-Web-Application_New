@extends('professional.layout.layout')

@section('title', 'Create Additional Service')

@section('styles')
@vite('resources/css/tazen.css')
@endsection

@section('content')
<div class="additional-service-create min-h-full bg-slate-50 px-4 py-6 text-slate-900 sm:px-6 lg:px-8">
    <div class="create-shell mx-auto max-w-5xl space-y-6">
        <section class="create-hero flex flex-col gap-5 rounded-2xl border border-brand-100 bg-gradient-to-br from-white via-brand-50 to-white p-6 shadow-card sm:flex-row sm:items-center sm:justify-between">
            <div class="hero-meta">
                <span class="hero-eyebrow"><i class="fas fa-layer-group"></i>Create</span>
                <h1 class="mt-2 text-2xl font-bold tracking-tight text-brand-950 sm:text-3xl">New Additional Service</h1>
                <ul class="hero-breadcrumb">
                    <li><a href="{{ route('professional.dashboard') }}">Home</a></li>
                    <li><a href="{{ route('professional.additional-services.index') }}">Additional Services</a></li>
                    <li class="active" aria-current="page">Create</li>
                </ul>
            </div>
            <div class="hero-actions">
                <a href="{{ route('professional.additional-services.index') }}" class="btn-neutral">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </section>

        <article class="form-card overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-card">
            <header class="form-card__head border-b border-slate-100 px-6 py-5">
                <h2 class="text-lg font-semibold text-brand-950">Add New Additional Service</h2>
                <p>Link a booking, define the service scope, and preview the GST-inclusive pricing before submitting for review.</p>
            </header>
            <div class="form-card__body p-6">
                <form id="additionalServiceForm">
                    @csrf

                    <div class="form-grid grid gap-5 md:grid-cols-2">
                        <div class="form-group space-y-1">
                            <label for="booking_id">Select Booking <span class="text-danger">*</span></label>
                            <select class="form-control select2 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-800 shadow-sm focus:border-brand-500 focus:outline-none" id="booking_id" name="booking_id" required>
                                <option value="">Choose a booking...</option>
                                @if($booking)
                                    <option value="{{ $booking->id }}" selected>
                                        {{ $booking->customer_name }} - {{ $booking->service_name }} - {{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}
                                    </option>
                                @else
                                    @foreach($bookings as $bookingOption)
                                        <option value="{{ $bookingOption->id }}">
                                            {{ $bookingOption->customer_name }} - {{ $bookingOption->service_name }} - {{ \Carbon\Carbon::parse($bookingOption->booking_date)->format('M d, Y') }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <small class="form-text">Select the booking this additional service relates to.</small>
                        </div>

                        <div class="form-group space-y-1">
                            <label for="service_name">Service Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control w-full rounded-lg border border-slate-300 px-3 py-2 text-slate-800 shadow-sm placeholder:text-slate-400 focus:border-brand-500 focus:outline-none" id="service_name" name="service_name" placeholder="Enter service name" maxlength="255" required>
                            <small class="form-text">Provide a concise title for the additional service.</small>
                        </div>
                    </div>

                    <div class="form-group space-y-1">
                        <label for="reason">Reason for Additional Service <span class="text-danger">*</span></label>
                        <textarea class="form-control w-full rounded-lg border border-slate-300 px-3 py-2 text-slate-800 shadow-sm placeholder:text-slate-400 focus:border-brand-500 focus:outline-none" id="reason" name="reason" rows="5" placeholder="Explain why this additional service is needed..." required></textarea>
                        <small class="form-text">Share context for the customer and internal review.</small>
                    </div>

                    <div class="form-grid grid gap-5 md:grid-cols-2">
                        <div class="form-group space-y-1">
                            <label for="base_price">Base Price (₹) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control w-full rounded-lg border border-slate-300 px-3 py-2 text-slate-800 shadow-sm placeholder:text-slate-400 focus:border-brand-500 focus:outline-none" id="base_price" name="base_price" min="0" step="0.01" placeholder="0.00" required>
                            <small class="form-text">Amount before taxes are applied.</small>
                        </div>

                        <div class="price-card rounded-xl border border-brand-100 bg-brand-50/60 p-5">
                            <h6>Price Breakdown</h6>
                            <div class="price-row">
                                <span class="price-label">Base Price</span>
                                <span class="price-value" id="display_base_price">₹0.00</span>
                            </div>
                            <div class="price-row">
                                <span class="price-label">CGST (9%)</span>
                                <span class="price-value" id="display_cgst">₹0.00</span>
                            </div>
                            <div class="price-row">
                                <span class="price-label">SGST (9%)</span>
                                <span class="price-value" id="display_sgst">₹0.00</span>
                            </div>
                            <div class="price-row price-total">
                                <span class="price-label">Total Price</span>
                                <span class="price-value" id="display_total_price">₹0.00</span>
                            </div>
                        </div>
                    </div>

                    @if($booking)
                        <div class="booking-alert rounded-xl border border-info/20 bg-info-surface p-4 text-info">
                            <strong>Selected Booking Details</strong>
                            <span><strong>Customer:</strong> {{ $booking->customer_name }}</span>
                            <span><strong>Service:</strong> {{ $booking->service_name }}</span>
                            <span><strong>Booking Date:</strong> {{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}</span>
                            <span><strong>Amount:</strong> ₹{{ number_format($booking->amount, 2) }}</span>
                        </div>
                    @endif

                    <footer class="cta-footer mt-6 flex flex-col gap-4 border-t border-slate-100 pt-5 sm:flex-row sm:items-center sm:justify-between">
                        <span class="text-muted">Fields marked with <span class="text-danger">*</span> are required for submission.</span>
                        <div class="cta-actions flex flex-wrap gap-3">
                            <a href="{{ route('professional.additional-services.index') }}" class="btn btn-neutral inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 font-medium text-slate-700 hover:bg-slate-50">
                                <i class="fas fa-arrow-left"></i>
                                Back to List
                            </a>
                            <button type="submit" class="btn btn-primary inline-flex items-center gap-2 rounded-lg bg-brand-700 px-4 py-2 font-semibold text-white hover:bg-brand-800" id="submitBtn">
                                <i class="fas fa-save"></i>
                                Create Additional Service
                            </button>
                        </div>
                    </footer>
                </form>
            </div>
        </article>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#booking_id').select2({
        placeholder: 'Search and select a booking...'
        , allowClear: true,
        ajax: {
            url: '{{ route("professional.additional-services.get-bookings") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term
                };
            },
            processResults: function (data) {
                const items = (data || []).map(function(item) {
                    let dateText = '';
                    if (item.booking_date) {
                        try {
                            const d = new Date(item.booking_date);
                            dateText = d.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
                        } catch (e) {
                            dateText = item.booking_date;
                        }
                    }

                    const customer = item.customer_name || item.name || '';
                    const service = item.service_name || item.service || '';
                    const textParts = [customer, service];
                    if (dateText) textParts.push(dateText);

                    return {
                        id: item.id,
                        text: textParts.filter(Boolean).join(' - '),
                        raw: item
                    };
                });

                return { results: items };
            },
            cache: true
        }
    });

    $('#base_price').on('input', function() {
        calculatePrices();
    });

    function calculatePrices() {
        const basePrice = parseFloat($('#base_price').val()) || 0;
        const cgst = basePrice * 0.09;
        const sgst = basePrice * 0.09;
        const totalPrice = basePrice + cgst + sgst;

        $('#display_base_price').text('₹' + basePrice.toFixed(2));
        $('#display_cgst').text('₹' + cgst.toFixed(2));
        $('#display_sgst').text('₹' + sgst.toFixed(2));
        $('#display_total_price').text('₹' + totalPrice.toFixed(2));
    }

    $('#additionalServiceForm').submit(function(e) {
        e.preventDefault();

        const submitBtn = $('#submitBtn');
        const originalText = submitBtn.html();

        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creating...');

        $.ajax({
            url: '{{ route("professional.additional-services.store") }}',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    }
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    Object.values(errors).forEach(function(error) {
                        toastr.error(error[0]);
                    });
                } else if (xhr.responseJSON?.message) {
                    toastr.error(xhr.responseJSON.message);
                } else {
                    toastr.error('An error occurred. Please try again.');
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    calculatePrices();
});
</script>
@endsection