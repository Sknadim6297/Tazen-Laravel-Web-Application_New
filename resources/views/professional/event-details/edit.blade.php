@extends('professional.layout.layout')

@section('title', 'Edit Event Details | Yuktione')

@section('content')
@php
    $bannerImages = $eventDetail->banner_image;
    if (is_string($bannerImages)) {
        $decodedBannerImages = json_decode($bannerImages, true);
        $bannerImages = is_array($decodedBannerImages) ? $decodedBannerImages : [];
    }
    $galleryImages = $eventDetail->event_gallery;
    if (is_string($galleryImages)) {
        $decodedGalleryImages = json_decode($galleryImages, true);
        $galleryImages = is_array($decodedGalleryImages) ? $decodedGalleryImages : [];
    }
    $bannerImages = is_array($bannerImages) ? $bannerImages : [];
    $galleryImages = is_array($galleryImages) ? $galleryImages : [];
@endphp
<div class="mx-auto max-w-4xl space-y-5">
    <header class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="min-w-0">
            <p class="text-xs font-semibold uppercase tracking-wide text-brand-700">Event details / Edit</p>
            <h1 class="mt-1 text-2xl font-semibold text-brand-950">{{ $eventDetail->event->heading ?? 'Edit event details' }}</h1>
            <p class="mt-1 text-sm text-text-muted">Update the event schedule, format, description and media.</p>
        </div>
        <a href="{{ route('professional.event-details.show', $eventDetail) }}" class="inline-flex min-h-10 items-center justify-center rounded-control border border-border px-4 text-sm font-semibold text-text-secondary hover:bg-surface-muted">View details</a>
    </header>

    <section class="overflow-hidden rounded-panel border border-divider bg-white shadow-card">
        <form action="{{ route('professional.event-details.update', $eventDetail) }}" method="POST" enctype="multipart/form-data" class="space-y-5 p-4 sm:p-6">
            @csrf
            @method('PUT')
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-1.5 sm:col-span-2">
                    <label class="block text-xs font-medium text-text-primary" for="event_name">Event</label>
                    <input type="hidden" name="event_id" value="{{ $eventDetail->event_id }}">
                    <input id="event_name" type="text" readonly value="{{ $eventDetail->event->heading ?? 'N/A' }}{{ $eventDetail->event?->date ? ' — ' . \Carbon\Carbon::parse($eventDetail->event->date)->format('M d, Y') : '' }}" class="h-10 w-full rounded-control border border-border bg-surface-muted px-3 text-sm text-text-secondary">
                    <p class="text-[11px] text-text-muted">The associated event cannot be changed here.</p>
                </div>
                <div class="space-y-1.5">
                    <label for="event_type" class="block text-xs font-medium text-text-primary">Event type <span class="text-error">*</span></label>
                    <input type="text" id="event_type" name="event_type" maxlength="255" value="{{ old('event_type', $eventDetail->event_type) }}" required class="h-10 w-full rounded-control border border-border px-3 text-sm focus:border-brand-primary focus:outline-none focus:ring-4 focus:ring-brand-100">
                    @error('event_type')<p class="text-xs text-error" role="alert">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-1.5">
                    <label for="starting_date" class="block text-xs font-medium text-text-primary">Starting date <span class="text-error">*</span></label>
                    <input type="date" id="starting_date" name="starting_date" value="{{ old('starting_date', $eventDetail->starting_date?->format('Y-m-d')) }}" required class="h-10 w-full rounded-control border border-border px-3 text-sm focus:border-brand-primary focus:outline-none focus:ring-4 focus:ring-brand-100">
                    @error('starting_date')<p class="text-xs text-error" role="alert">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-1.5">
                    <label for="starting_fees" class="block text-xs font-medium text-text-primary">Starting fee (₹) <span class="text-error">*</span></label>
                    <input type="number" id="starting_fees" name="starting_fees" min="0" step="0.01" value="{{ old('starting_fees', $eventDetail->starting_fees) }}" required class="h-10 w-full rounded-control border border-border px-3 text-sm focus:border-brand-primary focus:outline-none focus:ring-4 focus:ring-brand-100">
                    @error('starting_fees')<p class="text-xs text-error" role="alert">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-1.5">
                    <label for="event_mode" class="block text-xs font-medium text-text-primary">Event mode <span class="text-error">*</span></label>
                    <select id="event_mode" name="event_mode" required class="h-10 w-full rounded-control border border-border bg-white px-3 text-sm focus:border-brand-primary focus:outline-none focus:ring-4 focus:ring-brand-100">
                        <option value="">Choose mode</option>
                        <option value="online" @selected(old('event_mode', $eventDetail->event_mode) === 'online')>Online</option>
                        <option value="offline" @selected(old('event_mode', $eventDetail->event_mode) === 'offline')>In person</option>
                    </select>
                    @error('event_mode')<p class="text-xs text-error" role="alert">{{ $message }}</p>@enderror
                </div>
                <div id="city_field" @class(['space-y-1.5', 'hidden' => old('event_mode', $eventDetail->event_mode) !== 'offline'])>
                    <label for="city" class="block text-xs font-medium text-text-primary">City <span class="text-error">*</span></label>
                    <input type="text" id="city" name="city" maxlength="255" value="{{ old('city', $eventDetail->city) }}" placeholder="Enter city" @required(old('event_mode', $eventDetail->event_mode) === 'offline') class="h-10 w-full rounded-control border border-border px-3 text-sm focus:border-brand-primary focus:outline-none focus:ring-4 focus:ring-brand-100">
                    @error('city')<p class="text-xs text-error" role="alert">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-1.5 sm:col-span-2">
                    <label for="event_details" class="block text-xs font-medium text-text-primary">Event description <span class="text-error">*</span></label>
                    <textarea id="event_details" name="event_details" rows="6" required class="w-full rounded-control border border-border px-3 py-2.5 text-sm leading-6 focus:border-brand-primary focus:outline-none focus:ring-4 focus:ring-brand-100">{{ old('event_details', $eventDetail->event_details) }}</textarea>
                    @error('event_details')<p class="text-xs text-error" role="alert">{{ $message }}</p>@enderror
                </div>
            </div>

            @foreach([['label' => 'Banner images', 'name' => 'banner_image', 'images' => $bannerImages], ['label' => 'Gallery images', 'name' => 'event_gallery', 'images' => $galleryImages]] as $media)
                <section class="space-y-3 rounded-panel border border-divider p-4">
                    <div>
                        <h2 class="text-sm font-semibold text-brand-950">{{ $media['label'] }}</h2>
                        <p class="mt-1 text-[11px] text-text-muted">Upload new images to replace the current set. Leave empty to keep existing images.</p>
                    </div>
                    @if(count($media['images']))
                        <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                            @foreach($media['images'] as $image)
                                @if(\Illuminate\Support\Facades\Storage::disk('public')->exists($image))
                                    <img src="{{ asset('storage/' . $image) }}" alt="Current {{ strtolower($media['label']) }}" class="aspect-video w-full rounded-control object-cover">
                                @endif
                            @endforeach
                        </div>
                    @endif
                    <input type="file" id="{{ $media['name'] }}" name="{{ $media['name'] }}[]" multiple accept="image/jpeg,image/png,image/webp" class="block w-full rounded-control border border-border bg-white px-3 py-2 text-xs text-text-secondary file:mr-3 file:rounded-control file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-brand-800">
                    <p class="text-[11px] text-text-muted">JPG, PNG or WebP, up to 2 MB per image.</p>
                    @error($media['name'] . '.0')<p class="text-xs text-error" role="alert">{{ $message }}</p>@enderror
                </section>
            @endforeach

            <div class="flex flex-col-reverse gap-2 border-t border-divider pt-4 sm:flex-row sm:justify-end">
                <a href="{{ route('professional.event-details.show', $eventDetail) }}" class="inline-flex min-h-10 items-center justify-center rounded-control border border-border px-4 text-sm font-semibold text-text-secondary hover:bg-surface-muted">Cancel</a>
                <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-control bg-brand-primary px-5 text-sm font-semibold text-white hover:bg-brand-800">Save changes</button>
            </div>
        </form>
    </section>
</div>
@endsection

@section('scripts')
<script>
(() => {
    const modeSelect = document.getElementById('event_mode');
    const cityField = document.getElementById('city_field');
    const cityInput = document.getElementById('city');
    if (!modeSelect || !cityField || !cityInput) return;
    const toggleCity = () => {
        const offline = modeSelect.value === 'offline';
        cityField.classList.toggle('hidden', !offline);
        cityInput.required = offline;
    };
    modeSelect.addEventListener('change', toggleCity);
    toggleCity();
})();
</script>
@endsection
