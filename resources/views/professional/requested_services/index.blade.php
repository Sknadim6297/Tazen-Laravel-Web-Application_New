@extends('professional.layout.layout')

@section('styles')
@vite('resources/css/tazen.css')
@endsection

@section('content')
<div class="requested-services-page mx-auto w-full max-w-7xl px-4 py-8 text-text-primary sm:px-6 lg:px-8">
    <div class="requested-services-shell mx-auto w-full space-y-6">
        <section class="services-hero flex flex-col gap-5 rounded-panel border border-border bg-gradient-to-br from-white to-brand-50 p-6 shadow-card sm:flex-row sm:items-center sm:justify-between">
            <div class="hero-meta">
                <span class="hero-eyebrow inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-brand-700"><i class="fas fa-clipboard-list"></i>Information</span>
                <h1 class="text-2xl font-bold tracking-tight text-text-primary sm:text-3xl">Other Information</h1>
                <ul class="hero-breadcrumb mt-3 flex flex-wrap items-center gap-2 text-sm text-text-muted">
                    <li><a href="{{ route('professional.dashboard') }}">Home</a></li>
                    <li class="active" aria-current="page">Other Information</li>
                </ul>
            </div>
            <div>
                @if($serviceCount < 1)
                    <a href="{{ route('professional.requested_services.create') }}" class="btn btn-primary inline-flex items-center justify-center gap-2 rounded-control bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-800 focus:outline-none focus:ring-2 focus:ring-brand-300">
                        <i class="fas fa-plus-circle"></i>
                        Add Information
                    </a>
                @endif
            </div>
        </section>

        <section class="services-card overflow-hidden rounded-panel border border-border bg-white shadow-card">
            <header class="services-card__head flex flex-col gap-3 border-b border-divider p-5 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-xl font-semibold text-text-primary">Other Information List</h2>
                @if($serviceCount < 1)
                    <a href="{{ route('professional.requested_services.create') }}">
                        <i class="fas fa-plus-circle"></i>
                        Add Information
                    </a>
                @endif
            </header>
            <div class="services-card__body p-5">
                <div class="table-wrapper w-full overflow-x-auto">
                    <table class="data-table w-full min-w-[48rem] border-collapse text-left text-sm">
                        <thead>
                            <tr>
                                <th class="border-b border-divider px-4 py-3 align-top bg-brand-50 text-xs font-semibold uppercase tracking-wide text-text-secondary">Heading</th>
                                <th class="border-b border-divider px-4 py-3 align-top bg-brand-50 text-xs font-semibold uppercase tracking-wide text-text-secondary">Requested Services</th>
                                <th class="border-b border-divider px-4 py-3 align-top bg-brand-50 text-xs font-semibold uppercase tracking-wide text-text-secondary">Prices</th>
                                <th class="border-b border-divider px-4 py-3 align-top bg-brand-50 text-xs font-semibold uppercase tracking-wide text-text-secondary">Education Statement</th>
                                <th class="border-b border-divider px-4 py-3 align-top bg-brand-50 text-xs font-semibold uppercase tracking-wide text-text-secondary">Education</th>
                                <th class="border-b border-divider px-4 py-3 align-top bg-brand-50 text-xs font-semibold uppercase tracking-wide text-text-secondary">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requestedServices as $service)
                                @php
                                    $servicesList = json_decode($service->requested_service, true) ?? [];
                                    $pricesList = json_decode($service->price, true) ?? [];
                                    $educationList = json_decode($service->education, true) ?? [];
                                @endphp
                                <tr>
                                    <td class="border-b border-divider px-4 py-3 align-top">{{ $service->sub_heading }}</td>
                                    <td class="border-b border-divider px-4 py-3 align-top">
                                        <div class="service-list space-y-1">
                                            @foreach($servicesList as $item)
                                                <div>{{ $item ?? '-' }}</div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="border-b border-divider px-4 py-3 align-top">
                                        <div class="price-list space-y-1 font-medium">
                                            @foreach($pricesList as $price)
                                                <div>₹{{ number_format((float) $price, 2) }}</div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="border-b border-divider px-4 py-3 align-top">{{ $service->education_statement }}</td>
                                    <td class="border-b border-divider px-4 py-3 align-top">
                                        <div class="education-list space-y-1">
                                            @if(is_array($educationList) && isset($educationList['college_name']))
                                                @foreach($educationList['college_name'] as $index => $college)
                                                    <div>{{ $college ?? '-' }} - {{ $educationList['degree'][$index] ?? '-' }}</div>
                                                @endforeach
                                            @else
                                                <div>-</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="actions-cell whitespace-nowrap border-b border-divider px-4 py-3 align-top">
                                        <a href="{{ route('professional.requested_services.edit', $service->id) }}"
                                           class="action-btn edit inline-flex h-9 w-9 items-center justify-center rounded-control border border-border text-text-secondary transition hover:bg-brand-50 hover:text-brand-800"
                                           title="Edit"
                                           aria-label="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="javascript:void(0);"
                                           data-url="{{ route('professional.requested_services.destroy', $service->id) }}"
                                           data-id="{{ $service->id }}"
                                           class="action-btn delete delete-item inline-flex h-9 w-9 items-center justify-center rounded-control border border-border text-text-secondary transition hover:bg-brand-50 hover:text-brand-800"
                                           title="Delete"
                                           aria-label="Delete">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="border-b border-divider px-4 py-3 align-top">
                                        <div class="empty-state rounded-panel border border-dashed border-border bg-white p-8 text-center text-text-secondary">
                                            <i class="fas fa-info-circle"></i>
                                            <h5>No data found</h5>
                                            <p>Once you add information, the list will appear here.</p>
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