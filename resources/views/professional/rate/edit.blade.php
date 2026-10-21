@extends('professional.layout.layout')

@section('styles')
@vite('resources/css/tazen.css')
@endsection

@section('content')
@php
    $isServiceRate = !$rates->sub_service_id;
    $isSubServiceRate = $rates->sub_service_id;
    $currentSubService = null;
    if ($isSubServiceRate && $professionalService && $professionalService->subServices) {
        $currentSubService = $professionalService->subServices->where('id', $rates->sub_service_id)->first();
    }
@endphp

<div class="content-wrapper rates-edit-page w-full text-text-primary mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="rates-shell mx-auto w-full space-y-6">
        <header class="rates-header flex flex-col gap-5 rounded-panel border border-border bg-gradient-to-br from-white to-brand-50 p-6 shadow-card sm:flex-row sm:items-center sm:justify-between">
            <div class="header-info">
                <span class="pretitle text-xs font-semibold uppercase tracking-[0.16em] text-brand-700">Rates & Packages</span>
                <h1 class="text-2xl font-bold tracking-tight text-text-primary sm:text-3xl">Edit Rate</h1>
                <p>Fine-tune your pricing details to keep your services competitive and clear for clients.</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('professional.rate.index') }}" class="header-btn inline-flex items-center justify-center gap-2 rounded-control border border-border bg-white px-4 py-2.5 text-sm font-semibold text-text-secondary transition hover:bg-surface-muted">
                    <i class="fas fa-arrow-left"></i>
                    Back to Rates
                </a>
            </div>
        </header>

        <section class="rates-card overflow-hidden rounded-panel border border-border bg-white shadow-card">
            <div class="rates-card__head border-b border-divider bg-surface-muted p-5 sm:p-7">
                <h2 class="text-xl font-semibold text-text-primary">Update Rate Information</h2>
                <p class="subtitle">
                    @if($professionalService)
                        Editing <strong>{{ $professionalService->service_name }}</strong>
                        @if($isSubServiceRate && $currentSubService)
                            <span class="dot-divider"></span>
                            <span>Sub-service: <strong>{{ $currentSubService->name }}</strong></span>
                        @endif
                    @else
                        Configure your rate details below.
                    @endif
                </p>
            </div>

            <div class="rates-card__body space-y-6 p-5 sm:p-7">
                <form id="rateForm" action="{{ route('professional.rate.update', $rates->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="professional_service_id" value="{{ $professionalService ? $professionalService->id : '' }}">
                    @if($isSubServiceRate)
                        <input type="hidden" name="sub_service_id" value="{{ $rates->sub_service_id }}">
                    @endif

                    @if($professionalService)
                        @if($isServiceRate)
                            <div class="rate-section service-section space-y-4 rounded-panel border border-border bg-surface-muted p-4 sm:p-5">
                                <div class="section-heading flex flex-col gap-1 border-b border-divider pb-4">
                                    <div>
                                        <h3>{{ $professionalService->service_name }}</h3>
                                        @if($professionalService->service_type)
                                            <small>{{ $professionalService->service_type }}</small>
                                        @endif
                                    </div>
                                    <span class="section-badge rounded-full bg-brand-100 px-3 py-1 text-xs font-semibold text-brand-800">Service level</span>
                                </div>

                                <div class="table-shell overflow-hidden rounded-control border border-border bg-white">
                                    <div class="table-responsive w-full overflow-x-auto">
                                        <table class="styled-table w-full min-w-[42rem] border-collapse text-left text-sm">
                                            <thead>
                                                <tr>
                                                    <th class="border-b border-divider px-4 py-3 align-top bg-brand-50 text-xs font-semibold uppercase tracking-wide text-text-secondary">Session Type</th>
                                                    <th class="border-b border-divider px-4 py-3 align-top bg-brand-50 text-xs font-semibold uppercase tracking-wide text-text-secondary">No. of Sessions</th>
                                                    <th class="border-b border-divider px-4 py-3 align-top bg-brand-50 text-xs font-semibold uppercase tracking-wide text-text-secondary">Rate Per Session (₹)</th>
                                                    <th class="border-b border-divider px-4 py-3 align-top bg-brand-50 text-xs font-semibold uppercase tracking-wide text-text-secondary">Final Rate (₹)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td data-label="Session Type" class="border-b border-divider px-4 py-3 align-top">
                                                        <select class="form-control session-type w-full rounded-control border border-border bg-white px-3 py-2.5 text-sm text-text-primary shadow-sm placeholder:text-text-muted focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-200" name="session_type">
                                                            <option value="One Time" {{ $rates->session_type == 'One Time' ? 'selected' : '' }}>One Time</option>
                                                            <option value="Monthly" {{ $rates->session_type == 'Monthly' ? 'selected' : '' }}>Monthly</option>
                                                            <option value="Quarterly" {{ $rates->session_type == 'Quarterly' ? 'selected' : '' }}>Quarterly</option>
                                                            <option value="Free Hand" {{ $rates->session_type == 'Free Hand' ? 'selected' : '' }}>Free Hand</option>
                                                        </select>
                                                    </td>
                                                    <td data-label="No. of Sessions" class="border-b border-divider px-4 py-3 align-top">
                                                        <input type="number" class="form-control num-sessions w-full rounded-control border border-border bg-white px-3 py-2.5 text-sm text-text-primary shadow-sm placeholder:text-text-muted focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-200" name="num_sessions" value="{{ $rates->num_sessions }}" min="1">
                                                    </td>
                                                    <td data-label="Rate Per Session (₹)" class="border-b border-divider px-4 py-3 align-top">
                                                        <input type="number" class="form-control rate-per-session w-full rounded-control border border-border bg-white px-3 py-2.5 text-sm text-text-primary shadow-sm placeholder:text-text-muted focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-200" name="rate_per_session" value="{{ $rates->rate_per_session }}" min="0" step="100">
                                                    </td>
                                                    <td data-label="Final Rate (₹)" class="border-b border-divider px-4 py-3 align-top">
                                                        <input type="number" class="form-control final-rate w-full rounded-control border border-border bg-white px-3 py-2.5 text-sm text-text-primary shadow-sm placeholder:text-text-muted focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-200" name="final_rate" value="{{ $rates->final_rate }}" readonly>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="rate-section sub-service-section space-y-4 rounded-panel border border-border bg-surface-muted p-4 sm:p-5">
                                <div class="section-heading flex flex-col gap-1 border-b border-divider pb-4">
                                    <div>
                                        <h3>{{ $professionalService->service_name }}</h3>
                                        @if($professionalService->service_type)
                                            <small>{{ $professionalService->service_type }}</small>
                                        @endif
                                    </div>
                                    <span class="section-badge rounded-full bg-brand-100 px-3 py-1 text-xs font-semibold text-brand-800">Sub-service level</span>
                                </div>

                                <div class="sub-service-banner">
                                    <div class="sub-service-title">
                                        <span class="label">Sub-service</span>
                                        <h4>{{ $currentSubService->name ?? 'Sub-Service' }}</h4>
                                    </div>
                                </div>

                                <div class="table-shell overflow-hidden rounded-control border border-border bg-white">
                                    <div class="table-responsive w-full overflow-x-auto">
                                        <table class="styled-table w-full min-w-[42rem] border-collapse text-left text-sm">
                                            <thead>
                                                <tr>
                                                    <th class="border-b border-divider px-4 py-3 align-top bg-brand-50 text-xs font-semibold uppercase tracking-wide text-text-secondary">Session Type</th>
                                                    <th class="border-b border-divider px-4 py-3 align-top bg-brand-50 text-xs font-semibold uppercase tracking-wide text-text-secondary">No. of Sessions</th>
                                                    <th class="border-b border-divider px-4 py-3 align-top bg-brand-50 text-xs font-semibold uppercase tracking-wide text-text-secondary">Rate Per Session (₹)</th>
                                                    <th class="border-b border-divider px-4 py-3 align-top bg-brand-50 text-xs font-semibold uppercase tracking-wide text-text-secondary">Final Rate (₹)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td data-label="Session Type" class="border-b border-divider px-4 py-3 align-top">
                                                        <select class="form-control session-type w-full rounded-control border border-border bg-white px-3 py-2.5 text-sm text-text-primary shadow-sm placeholder:text-text-muted focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-200" name="session_type">
                                                            <option value="One Time" {{ $rates->session_type == 'One Time' ? 'selected' : '' }}>One Time</option>
                                                            <option value="Monthly" {{ $rates->session_type == 'Monthly' ? 'selected' : '' }}>Monthly</option>
                                                            <option value="Quarterly" {{ $rates->session_type == 'Quarterly' ? 'selected' : '' }}>Quarterly</option>
                                                            <option value="Free Hand" {{ $rates->session_type == 'Free Hand' ? 'selected' : '' }}>Free Hand</option>
                                                        </select>
                                                    </td>
                                                    <td data-label="No. of Sessions" class="border-b border-divider px-4 py-3 align-top">
                                                        <input type="number" class="form-control num-sessions w-full rounded-control border border-border bg-white px-3 py-2.5 text-sm text-text-primary shadow-sm placeholder:text-text-muted focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-200" name="num_sessions" value="{{ $rates->num_sessions }}" min="1">
                                                    </td>
                                                    <td data-label="Rate Per Session (₹)" class="border-b border-divider px-4 py-3 align-top">
                                                        <input type="number" class="form-control rate-per-session w-full rounded-control border border-border bg-white px-3 py-2.5 text-sm text-text-primary shadow-sm placeholder:text-text-muted focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-200" name="rate_per_session" value="{{ $rates->rate_per_session }}" min="0" step="100">
                                                    </td>
                                                    <td data-label="Final Rate (₹)" class="border-b border-divider px-4 py-3 align-top">
                                                        <input type="number" class="form-control final-rate w-full rounded-control border border-border bg-white px-3 py-2.5 text-sm text-text-primary shadow-sm placeholder:text-text-muted focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-200" name="final_rate" value="{{ $rates->final_rate }}" readonly>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="features-section">
                            <h5><i class="fas fa-list-ul"></i> Package Features</h5>
                            <p class="helper-text">Highlight what this package includes to help customers choose confidently.</p>
                            <div id="featuresContainer">
                                @if($rates->features && count($rates->features) > 0)
                                    @foreach($rates->features as $feature)
                                        @if($feature)
                                            <div class="feature-item">
                                                <input type="text" name="features[]" class="form-control feature-input w-full rounded-control border border-border bg-white px-3 py-2.5 text-sm text-text-primary shadow-sm placeholder:text-text-muted focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-200" placeholder="Enter feature" value="{{ $feature }}">
                                                <button type="button" class="remove-feature" onclick="removeFeature(this)" aria-label="Remove feature">×</button>
                                            </div>
                                        @endif
                                    @endforeach
                                @else
                                    <div class="feature-item">
                                        <input type="text" name="features[]" class="form-control feature-input w-full rounded-control border border-border bg-white px-3 py-2.5 text-sm text-text-primary shadow-sm placeholder:text-text-muted focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-200" placeholder="Enter feature">
                                        <button type="button" class="remove-feature" onclick="removeFeature(this)" aria-label="Remove feature">×</button>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="add-feature" onclick="addFeature()">
                                <i class="fas fa-plus"></i>
                                Add Feature
                            </button>
                        </div>

                        <div class="form-footer flex flex-col-reverse gap-3 border-t border-divider pt-5 sm:flex-row sm:justify-end">
                            <a href="{{ route('professional.rate.index') }}" class="back-link">
                                <i class="fas fa-arrow-left"></i>
                                Back to Rates
                            </a>
                            <button type="submit" class="btn primary-btn inline-flex items-center justify-center gap-2 rounded-control bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-800 border border-border bg-white text-text-secondary hover:bg-surface-muted">
                                <i class="fas fa-save"></i>
                                Update Rate
                            </button>
                        </div>
                    @else
                        <div class="empty-state rounded-panel border border-dashed border-border bg-white p-8 text-center text-text-secondary">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>You don't have any services set up yet.</strong>
                            <p>Please set up your services first to manage rates.</p>
                            <a href="{{ route('professional.service.create') }}" class="btn inline-flex items-center justify-center gap-2 rounded-control border border-border bg-white px-4 py-2.5 text-sm font-semibold text-text-secondary transition hover:bg-surface-muted">
                                Create Service
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </section>
    </div>
</div>


@endsection

@section('scripts')
<script>
    // Feature management functions
    function addFeature() {
        const container = document.getElementById('featuresContainer');
        const newFeature = document.createElement('div');
        newFeature.className = 'feature-item';
        newFeature.innerHTML = `
            <input type="text" name="features[]" class="form-control feature-input w-full rounded-control border border-border bg-white px-3 py-2.5 text-sm text-text-primary shadow-sm placeholder:text-text-muted focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-200" placeholder="Enter feature">
            <button type="button" class="remove-feature" onclick="removeFeature(this)" aria-label="Remove feature">×</button>
        `;
        container.appendChild(newFeature);
    }

    function removeFeature(button) {
        const featureItem = button.closest('.feature-item');
        const container = featureItem.closest('#featuresContainer');
        const featureItems = container.querySelectorAll('.feature-item');
        
        // Don't allow removing the last feature input
        if (featureItems.length > 1) {
            featureItem.remove();
        } else {
            // Just clear the input instead of removing it
            featureItem.querySelector('.feature-input').value = '';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const row = document.querySelector('tbody tr');
        if (!row) {
            return;
        }

        const calculateFinalRate = () => {
            const numSessions = parseInt(row.querySelector('.num-sessions').value || 0);
            const ratePerSession = parseInt(row.querySelector('.rate-per-session').value || 0);
            const finalRateInput = row.querySelector('.final-rate');
            finalRateInput.value = numSessions * ratePerSession;
        };

        row.querySelector('.num-sessions').addEventListener('input', calculateFinalRate);
        row.querySelector('.rate-per-session').addEventListener('input', calculateFinalRate);

        calculateFinalRate();
    });
</script>

<script>
    $(document).ready(function () {
        $('#rateForm').submit(function (e) {
            e.preventDefault();

            const form = this;
            const formData = new FormData(form);
            const submitBtn = $(form).find('button[type="submit"]');
            submitBtn.prop('disabled', true);

            $.ajax({
                url: $(form).attr('action'),
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                success: function (response) {
                    toastr.success("Rate updated successfully.");
                    setTimeout(() => {
                        window.location.href = "{{ route('professional.rate.index') }}";
                    }, 1500);
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        $.each(errors, function (key, value) {
                            toastr.error(value[0]);
                        });
                    } else {
                        toastr.error("An unexpected error occurred.");
                    }
                },
                complete: function () {
                    submitBtn.prop('disabled', false);
                }
            });
        });
    });
</script>
@endsection
