@extends('professional.layout.layout')

@section('title', 'Add Event Details | Yuktione')

@section('content')
<div class="mx-auto max-w-4xl space-y-5">
    <header class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-brand-700">Event details / Create</p>
            <h1 class="mt-1 text-2xl font-semibold text-brand-950">Add event details</h1>
            <p class="mt-1 text-sm text-text-muted">Add the schedule, event format, description and images customers need.</p>
        </div>
        <a href="{{ route('professional.event-details.index') }}" class="inline-flex min-h-10 items-center justify-center rounded-control border border-border px-4 text-sm font-semibold text-text-secondary hover:bg-surface-muted">Back to details</a>
    </header>

    @if($availableEvents->isEmpty())
        <aside class="rounded-panel border border-warning/20 bg-warning-surface p-4 text-sm text-warning" role="status">
            <p class="font-semibold">No events available for details</p>
            <p class="mt-1">Create an event first, or all your existing events may already have details.</p>
            <a href="{{ route('professional.events.create') }}" class="mt-3 inline-flex min-h-9 items-center rounded-control bg-white px-3 text-xs font-semibold text-warning hover:bg-amber-50">Create event</a>
        </aside>
    @endif

    <section class="overflow-hidden rounded-panel border border-divider bg-white shadow-card">
        <div class="border-b border-divider px-4 py-4 sm:px-6">
            <h2 class="text-sm font-semibold text-brand-950">Event information</h2>
            <p class="mt-1 text-xs text-text-muted">Selecting your event fills in its type, date, price and description as a starting point.</p>
        </div>
        <form action="{{ route('professional.event-details.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5 p-4 sm:p-6">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-1.5 sm:col-span-2">
                    <label for="event_id" class="block text-xs font-medium text-text-primary">Event <span class="text-error">*</span></label>
                    <select id="event_id" name="event_id" required class="h-10 w-full rounded-control border border-border bg-white px-3 text-sm focus:border-brand-primary focus:outline-none focus:ring-4 focus:ring-brand-100">
                        <option value="">Choose an event</option>
                        @foreach($availableEvents as $event)
                            <option value="{{ $event->id }}"
                                data-starting-date="{{ $event->date ? \Carbon\Carbon::parse($event->date)->format('Y-m-d') : '' }}"
                                data-starting-fees="{{ $event->starting_fees ?? '' }}"
                                data-description="{{ $event->short_description ?? '' }}"
                                data-mini-heading="{{ $event->mini_heading ?? '' }}"
                                @selected((string) old('event_id', request('event_id')) === (string) $event->id)>
                                {{ $event->heading }} — {{ $event->date ? \Carbon\Carbon::parse($event->date)->format('M d, Y') : 'No date' }}
                            </option>
                        @endforeach
                    </select>
                    @error('event_id')<p class="text-xs text-error" role="alert">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-1.5 sm:col-span-2">
                    <label for="banner_image" class="block text-xs font-medium text-text-primary">Banner images <span class="text-error">*</span></label>
                    <input type="file" id="banner_image" name="banner_image[]" multiple accept="image/jpeg,image/png,image/webp" required class="block w-full rounded-control border border-border bg-white px-3 py-2 text-xs text-text-secondary file:mr-3 file:rounded-control file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-brand-800">
                    <p class="text-[11px] text-text-muted">JPG, PNG or WebP, up to 2 MB per image.</p>
                    @error('banner_image.0')<p class="text-xs text-error" role="alert">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-1.5">
                    <label for="event_type" class="block text-xs font-medium text-text-primary">Event type <span class="text-error">*</span></label>
                    <input type="text" id="event_type" name="event_type" value="{{ old('event_type') }}" maxlength="255" placeholder="Workshop, webinar, conference" required class="h-10 w-full rounded-control border border-border px-3 text-sm focus:border-brand-primary focus:outline-none focus:ring-4 focus:ring-brand-100">
                    @error('event_type')<p class="text-xs text-error" role="alert">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-1.5">
                    <label for="starting_date" class="block text-xs font-medium text-text-primary">Starting date <span class="text-error">*</span></label>
                    <input type="date" id="starting_date" name="starting_date" value="{{ old('starting_date') }}" required class="h-10 w-full rounded-control border border-border px-3 text-sm focus:border-brand-primary focus:outline-none focus:ring-4 focus:ring-brand-100">
                    @error('starting_date')<p class="text-xs text-error" role="alert">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-1.5">
                    <label for="starting_fees" class="block text-xs font-medium text-text-primary">Starting fee (₹) <span class="text-error">*</span></label>
                    <input type="number" id="starting_fees" name="starting_fees" min="0" step="0.01" value="{{ old('starting_fees') }}" required class="h-10 w-full rounded-control border border-border px-3 text-sm focus:border-brand-primary focus:outline-none focus:ring-4 focus:ring-brand-100">
                    @error('starting_fees')<p class="text-xs text-error" role="alert">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-1.5">
                    <label for="event_mode" class="block text-xs font-medium text-text-primary">Event mode <span class="text-error">*</span></label>
                    <select id="event_mode" name="event_mode" required class="h-10 w-full rounded-control border border-border bg-white px-3 text-sm focus:border-brand-primary focus:outline-none focus:ring-4 focus:ring-brand-100">
                        <option value="">Choose mode</option>
                        <option value="online" @selected(old('event_mode') === 'online')>Online</option>
                        <option value="offline" @selected(old('event_mode') === 'offline')>In person</option>
                    </select>
                    @error('event_mode')<p class="text-xs text-error" role="alert">{{ $message }}</p>@enderror
                </div>
                <div id="city_field" @class(['space-y-1.5', 'hidden' => old('event_mode') !== 'offline'])>
                    <label for="city" class="block text-xs font-medium text-text-primary">City <span class="text-error">*</span></label>
                    <input type="text" id="city" name="city" maxlength="255" value="{{ old('city') }}" placeholder="Enter city" @required(old('event_mode') === 'offline') class="h-10 w-full rounded-control border border-border px-3 text-sm focus:border-brand-primary focus:outline-none focus:ring-4 focus:ring-brand-100">
                    @error('city')<p class="text-xs text-error" role="alert">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-1.5 sm:col-span-2">
                    <label for="event_details" class="block text-xs font-medium text-text-primary">Event description <span class="text-error">*</span></label>
                    <textarea id="event_details" name="event_details" rows="6" required placeholder="Describe the agenda and what participants can expect." class="w-full rounded-control border border-border px-3 py-2.5 text-sm leading-6 focus:border-brand-primary focus:outline-none focus:ring-4 focus:ring-brand-100">{{ old('event_details') }}</textarea>
                    @error('event_details')<p class="text-xs text-error" role="alert">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-1.5 sm:col-span-2">
                    <label for="event_gallery" class="block text-xs font-medium text-text-primary">Gallery images <span class="font-normal text-text-muted">(optional)</span></label>
                    <input type="file" id="event_gallery" name="event_gallery[]" multiple accept="image/jpeg,image/png,image/webp" class="block w-full rounded-control border border-border bg-white px-3 py-2 text-xs text-text-secondary file:mr-3 file:rounded-control file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-brand-800">
                    <p class="text-[11px] text-text-muted">JPG, PNG or WebP, up to 2 MB per image.</p>
                    @error('event_gallery.0')<p class="text-xs text-error" role="alert">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="flex flex-col-reverse gap-2 border-t border-divider pt-4 sm:flex-row sm:justify-end">
                <a href="{{ route('professional.event-details.index') }}" class="inline-flex min-h-10 items-center justify-center rounded-control border border-border px-4 text-sm font-semibold text-text-secondary hover:bg-surface-muted">Cancel</a>
                <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-control bg-brand-primary px-5 text-sm font-semibold text-white hover:bg-brand-800">Save event details</button>
            </div>
        </form>
    </section>
</div>
@endsection

@section('scripts')
<script>
(() => {
    const eventSelect = document.getElementById('event_id');
    const modeSelect = document.getElementById('event_mode');
    const cityField = document.getElementById('city_field');
    const cityInput = document.getElementById('city');
    const fields = {
        type: document.getElementById('event_type'),
        date: document.getElementById('starting_date'),
        fees: document.getElementById('starting_fees'),
        details: document.getElementById('event_details'),
    };
    if (!eventSelect || !modeSelect || !cityField || !cityInput) return;

    const toggleCity = () => {
        const offline = modeSelect.value === 'offline';
        cityField.classList.toggle('hidden', !offline);
        cityInput.required = offline;
    };
    const fillFromEvent = (replaceValues = false) => {
        const option = eventSelect.selectedOptions[0];
        if (!option?.value) return;
        if (fields.type && (replaceValues || !fields.type.value)) fields.type.value = option.dataset.miniHeading || '';
        if (fields.date && (replaceValues || !fields.date.value)) fields.date.value = option.dataset.startingDate || '';
        if (fields.fees && (replaceValues || !fields.fees.value)) fields.fees.value = option.dataset.startingFees || '';
        if (fields.details && (replaceValues || !fields.details.value)) fields.details.value = option.dataset.description || '';
    };
    eventSelect.addEventListener('change', () => fillFromEvent(true));
    modeSelect.addEventListener('change', toggleCity);
    toggleCity();
    if (eventSelect.value) fillFromEvent();
})();
</script>
@endsection
