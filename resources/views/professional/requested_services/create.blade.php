@extends('professional.layout.layout')

@section('styles')
@vite('resources/css/tazen.css')
@endsection

@section('content')
<div class="requested-create-page mx-auto w-full max-w-7xl px-4 py-8 text-text-primary sm:px-6 lg:px-8">
    <div class="requested-create-shell mx-auto w-full space-y-6">
        <section class="create-hero flex flex-col gap-5 rounded-panel border border-border bg-gradient-to-br from-white to-brand-50 p-6 shadow-card sm:flex-row sm:items-center sm:justify-between">
            <div class="hero-meta">
                <span class="hero-eyebrow inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-brand-700"><i class="fas fa-clipboard-list"></i>Information</span>
                <h1 class="text-2xl font-bold tracking-tight text-text-primary sm:text-3xl">Add Other Information</h1>
                <ul class="hero-breadcrumb mt-3 flex flex-wrap items-center gap-2 text-sm text-text-muted">
                    <li><a href="{{ route('professional.dashboard') }}">Home</a></li>
                    <li class="active" aria-current="page">Other Information</li>
                </ul>
            </div>
        </section>

        <section class="form-card rounded-panel border border-border bg-white p-5 shadow-card sm:p-7">
            <h2 class="text-xl font-semibold text-text-primary">Information Details</h2>
            <form id="serviceForm">
                @csrf
                <div class="form-group mb-5 space-y-2">
                    <label for="sub_heading" class="block text-sm font-medium text-text-secondary">Services Details *</label>
                    <textarea name="sub_heading" id="sub_heading" class="form-control w-full rounded-control border border-border bg-white px-3 py-2.5 text-sm text-text-primary shadow-sm placeholder:text-text-muted focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-200" placeholder="Describe the services you can offer..." required></textarea>
                </div>

                <div class="dynamic-toolbar mb-4 flex flex-wrap items-center justify-between gap-3">
                    <strong>Requested Services</strong>
                    <button type="button" id="addService" class="btn-neutral inline-flex items-center justify-center gap-2 rounded-control border border-border bg-white px-4 py-2 text-sm font-semibold text-text-secondary transition hover:bg-surface-muted">
                        <i class="fas fa-plus"></i> Add Service
                    </button>
                </div>
                <div id="requestedServicesContainer">
                    <div class="dynamic-group requested-service grid gap-4 rounded-control border border-border bg-surface-muted p-4 md:grid-cols-2">
                        <div class="form-group mb-5 space-y-2">
                            <label class="block text-sm font-medium text-text-secondary">Requested Service *</label>
                            <input type="text" name="requested_service[]" class="form-control w-full rounded-control border border-border bg-white px-3 py-2.5 text-sm text-text-primary shadow-sm placeholder:text-text-muted focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-200" placeholder="Add service you can offer" required>
                        </div>
                        <div class="form-group mb-5 space-y-2">
                            <label class="block text-sm font-medium text-text-secondary">Price *</label>
                            <input type="number" name="price[]" class="form-control w-full rounded-control border border-border bg-white px-3 py-2.5 text-sm text-text-primary shadow-sm placeholder:text-text-muted focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-200" step="0.01" placeholder="Enter price" required>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-5 space-y-2">
                    <label for="education_statement" class="block text-sm font-medium text-text-secondary">Education Statement</label>
                    <textarea name="education_statement" id="education_statement" class="form-control w-full rounded-control border border-border bg-white px-3 py-2.5 text-sm text-text-primary shadow-sm placeholder:text-text-muted focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-200" rows="4" placeholder="Share details about your education and credentials..."></textarea>
                </div>

                <div class="dynamic-toolbar mb-4 flex flex-wrap items-center justify-between gap-3">
                    <strong>Education</strong>
                    <button type="button" id="addEducation" class="btn-neutral inline-flex items-center justify-center gap-2 rounded-control border border-border bg-white px-4 py-2 text-sm font-semibold text-text-secondary transition hover:bg-surface-muted">
                        <i class="fas fa-plus"></i> Add Education
                    </button>
                </div>
                <div id="educationContainer">
                    <div class="dynamic-group education-entry grid gap-4 rounded-control border border-border bg-surface-muted p-4 md:grid-cols-2">
                        <div class="form-group mb-5 space-y-2">
                            <label class="block text-sm font-medium text-text-secondary">College Name</label>
                            <input type="text" name="college_name[]" class="form-control w-full rounded-control border border-border bg-white px-3 py-2.5 text-sm text-text-primary shadow-sm placeholder:text-text-muted focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-200" placeholder="Enter college name">
                        </div>
                        <div class="form-group mb-5 space-y-2">
                            <label class="block text-sm font-medium text-text-secondary">Degree</label>
                            <input type="text" name="degree[]" class="form-control w-full rounded-control border border-border bg-white px-3 py-2.5 text-sm text-text-primary shadow-sm placeholder:text-text-muted focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-200" placeholder="Enter degree">
                        </div>
                    </div>
                </div>

                <div class="form-actions flex flex-col-reverse gap-3 border-t border-divider pt-5 sm:flex-row sm:justify-end">
                    <a href="{{ route('professional.requested_services.index') }}" class="btn-outline inline-flex items-center justify-center gap-2 rounded-control border border-border bg-white px-4 py-2.5 text-sm font-semibold text-text-secondary transition hover:bg-surface-muted">
                        <i class="fas fa-arrow-left"></i>
                        Cancel
                    </a>
                    <button type="submit" class="btn-primary inline-flex items-center justify-center gap-2 rounded-control bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-800 focus:outline-none focus:ring-2 focus:ring-brand-300">
                        <i class="fas fa-save"></i>
                        Save
                    </button>
                </div>
            </form>
        </section>
    </div>
</div>
@endsection

@section('scripts')
<script>
$('#addService').click(function () {
    $('#requestedServicesContainer').append(`
        <div class="dynamic-group requested-service grid gap-4 rounded-control border border-border bg-surface-muted p-4 md:grid-cols-2">
            <div class="form-group mb-5 space-y-2">
                <label class="block text-sm font-medium text-text-secondary">Requested Service *</label>
                <input type="text" name="requested_service[]" class="form-control w-full rounded-control border border-border bg-white px-3 py-2.5 text-sm text-text-primary shadow-sm placeholder:text-text-muted focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-200" placeholder="Add service you can offer" required>
            </div>
            <div class="form-group mb-5 space-y-2">
                <label class="block text-sm font-medium text-text-secondary">Price *</label>
                <input type="number" name="price[]" class="form-control w-full rounded-control border border-border bg-white px-3 py-2.5 text-sm text-text-primary shadow-sm placeholder:text-text-muted focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-200" placeholder="Enter price" step="0.01" required>
            </div>
            <div class="form-group mb-5 space-y-2">
                <button type="button" class="remove-service">Delete</button>
            </div>
        </div>
    `);
});

$('#addEducation').click(function () {
    $('#educationContainer').append(`
        <div class="dynamic-group education-entry grid gap-4 rounded-control border border-border bg-surface-muted p-4 md:grid-cols-2">
            <div class="form-group mb-5 space-y-2">
                <label class="block text-sm font-medium text-text-secondary">College Name</label>
                <input type="text" name="college_name[]" class="form-control w-full rounded-control border border-border bg-white px-3 py-2.5 text-sm text-text-primary shadow-sm placeholder:text-text-muted focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-200" placeholder="Enter college name" required>
            </div>
            <div class="form-group mb-5 space-y-2">
                <label class="block text-sm font-medium text-text-secondary">Degree</label>
                <input type="text" name="degree[]" class="form-control w-full rounded-control border border-border bg-white px-3 py-2.5 text-sm text-text-primary shadow-sm placeholder:text-text-muted focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-200" placeholder="Enter degree" required>
            </div>
            <div class="form-group mb-5 space-y-2">
                <button type="button" class="remove-education">Delete</button>
            </div>
        </div>
    `);
});

$(document).on('click', '.remove-service', function () {
    $(this).closest('.requested-service').remove();
});

$(document).on('click', '.remove-education', function () {
    $(this).closest('.education-entry').remove();
});

$('#serviceForm').submit(function(e) {
    e.preventDefault();
    let form = this;
    let formData = new FormData(form);

    $.ajax({
        url: "{{ route('professional.requested_services.store') }}",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            if (response.status=='success') {
                toastr.success(response.message);
                form.reset();
                setTimeout(() => {
                    window.location.href = "{{ route('professional.requested_services.index') }}";
                }, 1500);
            } else {
                toastr.error(response.message || "Something went wrong");
            }
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                $.each(errors, function(key, value) {
                    toastr.error(value[0]);
                });
            } else {
                toastr.error(xhr.responseJSON.message || "An unexpected error occurred");
            }
        }
    });
});
</script>
@endsection
