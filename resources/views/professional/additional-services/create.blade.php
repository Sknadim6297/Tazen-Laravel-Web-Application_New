@extends('professional.layout.layout')

@section('title', 'Create Additional Service')

@section('content')
<div class="mx-auto w-full max-w-5xl space-y-6">
    <header class="flex flex-col gap-5 rounded-panel border border-border bg-white p-5 shadow-card sm:flex-row sm:items-center sm:justify-between sm:p-6">
        <div>
            <nav aria-label="Breadcrumb" class="mb-3 text-sm text-text-muted">
                <ol class="flex flex-wrap items-center gap-2">
                    <li><a class="hover:text-brand-primary" href="{{ route('professional.dashboard') }}">Dashboard</a></li>
                    <li aria-hidden="true">/</li>
                    <li><a class="hover:text-brand-primary" href="{{ route('professional.additional-services.index') }}">Additional Services</a></li>
                    <li aria-hidden="true">/</li>
                    <li class="font-medium text-text-primary" aria-current="page">Create</li>
                </ol>
            </nav>
            <p class="mb-1 text-xs font-semibold uppercase tracking-[0.16em] text-brand-primary">Service management</p>
            <h1 class="text-2xl font-bold tracking-tight text-text-primary sm:text-3xl">Create an additional service</h1>
            <p class="mt-2 text-sm leading-6 text-text-secondary">Choose a booking, describe the extra work, and set its base price.</p>
        </div>
        <a href="{{ route('professional.additional-services.index') }}" class="inline-flex min-h-11 shrink-0 items-center justify-center gap-2 rounded-control border border-border px-4 py-2.5 text-sm font-semibold text-text-secondary transition hover:bg-surface-muted">
            <i class="fas fa-arrow-left" aria-hidden="true"></i> Back to services
        </a>
    </header>

    <article class="overflow-hidden rounded-panel border border-border bg-white shadow-card">
        <header class="border-b border-border px-5 py-4 sm:px-6">
            <h2 class="text-lg font-semibold text-text-primary">Service details</h2>
            <p class="mt-1 text-sm text-text-muted">GST-inclusive price preview is calculated automatically.</p>
        </header>
        <div class="p-5 sm:p-6">
            <form id="additionalServiceForm" novalidate>
                @csrf

                <div id="formErrors" class="mb-5 hidden rounded-control border border-error/30 bg-error-surface p-4 text-sm text-error" role="alert" aria-live="polite">
                    <p class="font-semibold">Please check the following fields:</p>
                    <ul id="formErrorList" class="mt-2 list-inside list-disc space-y-1"></ul>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div class="space-y-1.5">
                        <label for="booking_id" class="block text-sm font-medium text-text-secondary">Select booking <span class="text-error">*</span></label>
                        <select class="select2 min-h-11 w-full rounded-control border border-border bg-white px-3 py-2 text-sm text-text-primary shadow-sm focus:border-brand-primary focus:outline-none focus:ring-2 focus:ring-brand-primary/20" id="booking_id" name="booking_id" required aria-describedby="bookingHelp bookingError">
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
                        <small id="bookingHelp" class="block text-xs leading-5 text-text-muted">Only bookings assigned to your professional account are available.</small>
                        <p id="bookingError" class="field-error hidden text-xs font-medium text-error"></p>
                    </div>

                    <div class="space-y-1.5">
                        <label for="service_name" class="block text-sm font-medium text-text-secondary">Service name <span class="text-error">*</span></label>
                        <input type="text" class="min-h-11 w-full rounded-control border border-border px-3 py-2 text-sm text-text-primary shadow-sm placeholder:text-text-muted focus:border-brand-primary focus:outline-none focus:ring-2 focus:ring-brand-primary/20" id="service_name" name="service_name" placeholder="Enter service name" maxlength="255" required aria-describedby="serviceNameError">
                        <p id="serviceNameError" class="field-error hidden text-xs font-medium text-error"></p>
                    </div>
                </div>

                <div class="mt-5 space-y-1.5">
                    <label for="reason" class="block text-sm font-medium text-text-secondary">Reason for additional service <span class="text-error">*</span></label>
                    <textarea class="w-full rounded-control border border-border px-3 py-2 text-sm text-text-primary shadow-sm placeholder:text-text-muted focus:border-brand-primary focus:outline-none focus:ring-2 focus:ring-brand-primary/20" id="reason" name="reason" rows="5" placeholder="Explain why this additional service is needed..." required aria-describedby="reasonError"></textarea>
                    <p id="reasonError" class="field-error hidden text-xs font-medium text-error"></p>
                </div>

                <div class="mt-5 grid gap-5 md:grid-cols-2">
                    <div class="space-y-1.5">
                        <label for="base_price" class="block text-sm font-medium text-text-secondary">Base price (₹) <span class="text-error">*</span></label>
                        <input type="number" class="min-h-11 w-full rounded-control border border-border px-3 py-2 text-sm text-text-primary shadow-sm placeholder:text-text-muted focus:border-brand-primary focus:outline-none focus:ring-2 focus:ring-brand-primary/20" id="base_price" name="base_price" min="0" step="0.01" placeholder="0.00" required aria-describedby="priceHelp basePriceError">
                        <small id="priceHelp" class="block text-xs leading-5 text-text-muted">Amount before taxes are applied.</small>
                        <p id="basePriceError" class="field-error hidden text-xs font-medium text-error"></p>
                    </div>

                    <div class="rounded-panel border border-brand-100 bg-brand-50 p-5">
                        <h3 class="mb-3 text-sm font-semibold text-text-primary">Price breakdown</h3>
                        <dl class="space-y-2 text-sm">
                            <div class="flex items-center justify-between gap-3"><dt class="text-text-secondary">Base price</dt><dd class="font-medium text-text-primary" id="display_base_price">₹0.00</dd></div>
                            <div class="flex items-center justify-between gap-3"><dt class="text-text-secondary">CGST (9%)</dt><dd class="font-medium text-text-primary" id="display_cgst">₹0.00</dd></div>
                            <div class="flex items-center justify-between gap-3"><dt class="text-text-secondary">SGST (9%)</dt><dd class="font-medium text-text-primary" id="display_sgst">₹0.00</dd></div>
                            <div class="flex items-center justify-between gap-3 border-t border-brand-200 pt-3 font-semibold"><dt class="text-text-primary">Total price</dt><dd class="text-brand-primary" id="display_total_price">₹0.00</dd></div>
                        </dl>
                    </div>
                </div>

                @if(!$booking && $bookings->isEmpty())
                    <div class="mt-5 rounded-control border border-warning/30 bg-warning-surface p-4 text-sm text-warning" role="status">
                        No eligible bookings are available for an additional service. A non-cancelled booking is required.
                    </div>
                @endif

                @if($booking)
                    <div class="mt-5 rounded-control border border-brand-200 bg-brand-50 p-4 text-sm text-text-secondary">
                        <h3 class="font-semibold text-text-primary">Selected booking</h3>
                        <dl class="mt-3 grid gap-2 sm:grid-cols-2">
                            <div><dt class="inline font-medium">Customer:</dt> <dd class="inline">{{ $booking->customer_name }}</dd></div>
                            <div><dt class="inline font-medium">Service:</dt> <dd class="inline">{{ $booking->service_name }}</dd></div>
                            <div><dt class="inline font-medium">Booking date:</dt> <dd class="inline">{{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}</dd></div>
                            <div><dt class="inline font-medium">Amount:</dt> <dd class="inline">₹{{ number_format($booking->amount, 2) }}</dd></div>
                        </dl>
                    </div>
                @endif

                <footer class="mt-6 flex flex-col-reverse gap-3 border-t border-border pt-5 sm:flex-row sm:items-center sm:justify-between">
                    <span class="text-xs text-text-muted">Fields marked with <span class="text-error">*</span> are required.</span>
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('professional.additional-services.index') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-control border border-border px-4 py-2 text-sm font-semibold text-text-secondary transition hover:bg-surface-muted">Cancel</a>
                        <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-control bg-brand-primary px-5 py-2 text-sm font-semibold text-white transition hover:bg-brand-800 disabled:cursor-not-allowed disabled:opacity-60" id="submitBtn" @disabled(!$booking && $bookings->isEmpty())>
                            <i class="fas fa-save" aria-hidden="true"></i> Create service
                        </button>
                    </div>
                </footer>
            </form>
        </div>
    </article>
</div>
@endsection

@section('scripts')
<script>
(() => {
    const form = document.getElementById('additionalServiceForm');
    const priceInput = document.getElementById('base_price');
    const submitButton = document.getElementById('submitBtn');
    const errorPanel = document.getElementById('formErrors');
    const errorList = document.getElementById('formErrorList');
    const errorTargets = {
        booking_id: 'bookingError',
        service_name: 'serviceNameError',
        reason: 'reasonError',
        base_price: 'basePriceError',
    };

    $('#booking_id').select2({
        placeholder: 'Search and select a booking...',
        allowClear: true,
        width: '100%',
        ajax: {
            url: @json(route('professional.additional-services.get-bookings')),
            dataType: 'json',
            delay: 250,
            data: (params) => ({ search: params.term }),
            processResults: (items) => ({
                results: (items || []).map((booking) => {
                    const date = booking.booking_date
                        ? new Date(booking.booking_date).toLocaleDateString(undefined, { dateStyle: 'medium' })
                        : '';
                    return {
                        id: booking.id,
                        text: [booking.customer_name, booking.service_name, date].filter(Boolean).join(' - '),
                    };
                }),
            }),
            cache: true,
        },
    });

    function updatePricePreview() {
        const basePrice = Number.parseFloat(priceInput.value) || 0;
        const tax = basePrice * 0.09;
        document.getElementById('display_base_price').textContent = `₹${basePrice.toFixed(2)}`;
        document.getElementById('display_cgst').textContent = `₹${tax.toFixed(2)}`;
        document.getElementById('display_sgst').textContent = `₹${tax.toFixed(2)}`;
        document.getElementById('display_total_price').textContent = `₹${(basePrice + tax * 2).toFixed(2)}`;
    }

    function clearValidationErrors() {
        errorPanel.classList.add('hidden');
        errorList.replaceChildren();
        Object.entries(errorTargets).forEach(([field, id]) => {
            const element = document.getElementById(id);
            element.textContent = '';
            element.classList.add('hidden');
            document.getElementById(field)?.removeAttribute('aria-invalid');
        });
    }

    function showValidationErrors(errors) {
        clearValidationErrors();
        Object.entries(errors).forEach(([field, messages]) => {
            const message = Array.isArray(messages) ? messages[0] : messages;
            const errorId = errorTargets[field];
            if (errorId) {
                document.getElementById(errorId).textContent = message;
                document.getElementById(errorId).classList.remove('hidden');
                document.getElementById(field)?.setAttribute('aria-invalid', 'true');
            }
            const item = document.createElement('li');
            item.textContent = message;
            errorList.appendChild(item);
        });
        errorPanel.classList.toggle('hidden', errorList.childElementCount === 0);
        errorPanel.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    form.addEventListener('input', clearValidationErrors);
    form.addEventListener('change', clearValidationErrors);
    priceInput.addEventListener('input', updatePricePreview);

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearValidationErrors();
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin" aria-hidden="true"></i> Creating...';

        try {
            const response = await fetch(@json(route('professional.additional-services.store')), {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: new FormData(form),
            });
            const result = await response.json();

            if (!response.ok || !result.success) {
                if (result.errors) {
                    showValidationErrors(result.errors);
                } else {
                    window.YuktioneToast?.error(result.message || 'Unable to create the service.');
                }
                return;
            }

            window.YuktioneToast?.success(result.message || 'Additional service created.');
            if (result.redirect) window.location.assign(result.redirect);
        } catch (error) {
            console.error('Additional service creation failed.', error);
            window.YuktioneToast?.error('Unable to create the service. Please try again.');
        } finally {
            submitButton.disabled = false;
            submitButton.innerHTML = '<i class="fas fa-save" aria-hidden="true"></i> Create service';
        }
    });

    updatePricePreview();
})();
</script>
@endsection
