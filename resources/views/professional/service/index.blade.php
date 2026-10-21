@extends('professional.layout.layout')

@section('styles')
@vite('resources/css/tazen.css')
@endsection

@section('content')
<div class="services-index-page mx-auto w-full max-w-7xl px-4 py-8 text-text-primary sm:px-6 lg:px-8">
    <div class="services-shell mx-auto w-full space-y-6">
        <section class="services-hero flex flex-col gap-5 rounded-panel border border-border bg-gradient-to-br from-white to-brand-50 p-6 shadow-card sm:flex-row sm:items-center sm:justify-between">
            <div class="hero-meta">
                <span class="hero-eyebrow inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-brand-700"><i class="fas fa-briefcase"></i>Services</span>
                <h1 class="text-2xl font-bold tracking-tight text-text-primary sm:text-3xl">All Services</h1>
                <ul class="hero-breadcrumb mt-3 flex flex-wrap items-center gap-2 text-sm text-text-muted">
                    <li><a href="{{ route('professional.dashboard') }}">Home</a></li>
                    <li class="active" aria-current="page">All Services</li>
                </ul>
            </div>
            @if(count($services) == 0)
                <a href="{{ route('professional.service.create') }}" class="btn btn-primary inline-flex items-center justify-center gap-2 rounded-control bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-800 focus:outline-none focus:ring-2 focus:ring-brand-300">
                    <i class="fas fa-plus-circle"></i>
                    Add Service
                </a>
            @else
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle"></i> You can only add one service. Edit your existing service below.
                </div>
            @endif
        </section>

        <section class="services-card overflow-hidden rounded-panel border border-border bg-white shadow-card">
            <header class="services-card__head flex flex-col gap-3 border-b border-divider p-5 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-xl font-semibold text-text-primary">Service List</h2>
            </header>
            <div class="services-card__body p-5">
                <div class="table-wrapper w-full overflow-x-auto">
                    <table class="data-table w-full min-w-[48rem] border-collapse text-left text-sm">
                        <thead>
                            <tr>
                                <th class="border-b border-divider px-4 py-3 align-top bg-brand-50 text-xs font-semibold uppercase tracking-wide text-text-secondary">Service Category</th>
                                <th class="border-b border-divider px-4 py-3 align-top bg-brand-50 text-xs font-semibold uppercase tracking-wide text-text-secondary">Sub-Services</th>
                                <th class="border-b border-divider px-4 py-3 align-top bg-brand-50 text-xs font-semibold uppercase tracking-wide text-text-secondary">Session Type</th>
                                <th class="border-b border-divider px-4 py-3 align-top bg-brand-50 text-xs font-semibold uppercase tracking-wide text-text-secondary">Tags</th>
                                <th class="border-b border-divider px-4 py-3 align-top bg-brand-50 text-xs font-semibold uppercase tracking-wide text-text-secondary">Client Requirements</th>
                                <th class="border-b border-divider px-4 py-3 align-top bg-brand-50 text-xs font-semibold uppercase tracking-wide text-text-secondary">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($services as $service)
                                <tr>
                                    <td data-label="Service Category" class="border-b border-divider px-4 py-3 align-top">{{ $service->service?->name ?? $service->service_name ?? 'Service' }}</td>
                                    <td data-label="Sub-Services" class="border-b border-divider px-4 py-3 align-top">
                                        @if($service->subServices->count() > 0)
                                            @foreach($service->subServices as $subService)
                                                <span class="badge badge-info mr-1 inline-flex rounded-full bg-info-surface px-2.5 py-1 text-xs font-medium text-info">{{ $subService->name }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted text-text-muted">No specific sub-services</span>
                                        @endif
                                    </td>
                                    <td data-label="Session Type" class="border-b border-divider px-4 py-3 align-top">
                                        <span class="badge badge-success inline-flex rounded-full bg-success-surface px-2.5 py-1 text-xs font-medium text-success">Online Session</span>
                                    </td>
                                    <td data-label="Tags" class="border-b border-divider px-4 py-3 align-top">{{ $service->tags }}</td>
                                    <td data-label="Client Requirements" class="border-b border-divider px-4 py-3 align-top">{{ $service->requirements }}</td>
                                    <td data-label="Actions" class="actions-cell whitespace-nowrap border-b border-divider px-4 py-3 align-top">
                                        <div>
                                            <a href="{{ route('professional.service.edit', $service->id) }}" class="action-btn edit inline-flex h-9 w-9 items-center justify-center rounded-control border border-border text-text-secondary transition hover:bg-brand-50 hover:text-brand-800" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="javascript:void(0)" data-url="{{ route('professional.service.destroy', $service->id) }}" class="action-btn delete delete-item inline-flex h-9 w-9 items-center justify-center rounded-control border border-border text-text-secondary transition hover:bg-brand-50 hover:text-brand-800" title="Delete">
                                                <i class="far fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="border-b border-divider px-4 py-3 align-top">
                                        <div class="empty-state rounded-panel border border-dashed border-border bg-white p-8 text-center text-text-secondary">
                                            <i class="fas fa-info-circle"></i>
                                            <p>No services added yet. Click "Add Service" button above to create your service.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).on('click', '.delete-item', function(e) {
    e.preventDefault();
    const url = $(this).data('url');

    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: url,
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Deleted!', response.message || 'Service has been deleted.', 'success')
                            .then(() => window.location.reload());
                    } else {
                        Swal.fire('Error!', response.message || 'Failed to delete service.', 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error!', xhr.responseJSON?.message || 'An error occurred while deleting the service.', 'error');
                }
            });
        }
    });
});
</script>
@endsection
