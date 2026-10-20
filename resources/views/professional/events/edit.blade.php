@extends('professional.layout.layout')

@section('title', 'Edit Event | Yuktione')

@section('content')
<div class="mx-auto max-w-4xl space-y-5">
    <header class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="min-w-0">
            <p class="text-xs font-semibold uppercase tracking-wide text-brand-700">Events / Edit</p>
            <h1 class="mt-1 break-words text-2xl font-semibold text-brand-950">{{ $event->heading }}</h1>
            <p class="mt-1 text-sm text-text-muted">Update your event information. Changes may be reviewed again before publication.</p>
        </div>
        <a href="{{ route('professional.events.show', $event) }}" class="inline-flex min-h-10 items-center justify-center rounded-control border border-border bg-white px-4 text-sm font-semibold text-text-secondary hover:bg-surface-muted">View event</a>
    </header>

    @if($event->status === 'rejected' && $event->admin_notes)
        <aside class="rounded-panel border border-warning/20 bg-warning-surface p-4 text-sm text-warning" role="status">
            <h2 class="font-semibold">Admin feedback</h2>
            <p class="mt-1 whitespace-pre-line">{{ $event->admin_notes }}</p>
        </aside>
    @endif

    <section class="overflow-hidden rounded-panel border border-divider bg-white shadow-card">
        <div class="border-b border-divider px-4 py-4 sm:px-6">
            <h2 class="text-sm font-semibold text-brand-950">Update event information</h2>
            <p class="mt-1 text-xs text-text-muted">The event date, type, fee, description and image can be updated here.</p>
        </div>
        <form action="{{ route('professional.events.update', $event) }}" method="POST" enctype="multipart/form-data" class="space-y-5 p-4 sm:p-6">
            @csrf
            @method('PUT')
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2 sm:col-span-2">
                    <p class="block text-xs font-medium text-text-primary">Current event image</p>
                    @if($event->card_image && \Illuminate\Support\Facades\Storage::disk('public')->exists($event->card_image))
                        <img src="{{ asset('storage/' . $event->card_image) }}" alt="{{ $event->heading }}" class="aspect-[16/7] max-h-64 w-full rounded-control object-cover">
                    @else
                        <div class="grid aspect-[16/7] max-h-64 place-items-center rounded-control bg-brand-50 text-xs text-text-muted">No event image available</div>
                    @endif
                    <label for="card_image" class="block text-xs font-medium text-text-primary">Replace image (optional)</label>
                    <input type="file" id="card_image" name="card_image" accept="image/jpeg,image/png,image/gif" aria-describedby="card_image_help" class="block w-full rounded-control border border-border bg-white px-3 py-2 text-xs text-text-secondary file:mr-3 file:rounded-control file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-brand-800 focus:border-brand-primary focus:outline-none focus:ring-4 focus:ring-brand-100">
                    <p id="card_image_help" class="text-[11px] text-text-muted">Leave empty to keep this image. JPEG, PNG or GIF, up to 2 MB.</p>
                    @error('card_image')<p class="text-xs text-error" role="alert">{{ $message }}</p>@enderror
                    <div id="imagePreview" hidden><img id="previewImg" src="" alt="Replacement event image preview" class="max-h-64 w-full rounded-control object-contain"></div>
                </div>
                <div class="space-y-1.5">
                    <label for="heading" class="block text-xs font-medium text-text-primary">Event name <span class="text-error">*</span></label>
                    <input type="text" id="heading" name="heading" maxlength="150" value="{{ old('heading', $event->heading) }}" required class="h-10 w-full rounded-control border border-border px-3 text-sm focus:border-brand-primary focus:outline-none focus:ring-4 focus:ring-brand-100">
                    @error('heading')<p class="text-xs text-error" role="alert">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-1.5">
                    <label for="mini_heading" class="block text-xs font-medium text-text-primary">Event type <span class="text-error">*</span></label>
                    <input type="text" id="mini_heading" name="mini_heading" maxlength="100" value="{{ old('mini_heading', $event->mini_heading) }}" required class="h-10 w-full rounded-control border border-border px-3 text-sm focus:border-brand-primary focus:outline-none focus:ring-4 focus:ring-brand-100">
                    @error('mini_heading')<p class="text-xs text-error" role="alert">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-1.5">
                    <label for="date" class="block text-xs font-medium text-text-primary">Event date <span class="text-error">*</span></label>
                    <input type="date" id="date" name="date" min="{{ now()->addDay()->toDateString() }}" value="{{ old('date', $event->date) }}" required class="h-10 w-full rounded-control border border-border px-3 text-sm focus:border-brand-primary focus:outline-none focus:ring-4 focus:ring-brand-100">
                    @error('date')<p class="text-xs text-error" role="alert">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-1.5">
                    <label for="time" class="block text-xs font-medium text-text-primary">Event time <span class="text-error">*</span></label>
                    <input type="time" id="time" name="time" value="{{ old('time', $event->time) }}" required class="h-10 w-full rounded-control border border-border px-3 text-sm focus:border-brand-primary focus:outline-none focus:ring-4 focus:ring-brand-100">
                    @error('time')<p class="text-xs text-error" role="alert">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-1.5">
                    <label for="starting_fees" class="block text-xs font-medium text-text-primary">Starting fee (₹) <span class="text-error">*</span></label>
                    <input type="number" id="starting_fees" name="starting_fees" min="0" step="0.01" value="{{ old('starting_fees', $event->starting_fees) }}" required class="h-10 w-full rounded-control border border-border px-3 text-sm focus:border-brand-primary focus:outline-none focus:ring-4 focus:ring-brand-100">
                    @error('starting_fees')<p class="text-xs text-error" role="alert">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-1.5 sm:col-span-2">
                    <label for="short_description" class="block text-xs font-medium text-text-primary">Description <span class="text-error">*</span></label>
                    <textarea id="short_description" name="short_description" rows="5" maxlength="1000" required class="w-full rounded-control border border-border px-3 py-2.5 text-sm leading-6 focus:border-brand-primary focus:outline-none focus:ring-4 focus:ring-brand-100">{{ old('short_description', $event->short_description) }}</textarea>
                    @error('short_description')<p class="text-xs text-error" role="alert">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="flex flex-col-reverse gap-2 border-t border-divider pt-4 sm:flex-row sm:justify-end">
                <a href="{{ route('professional.events.show', $event) }}" class="inline-flex min-h-10 items-center justify-center rounded-control border border-border px-4 text-sm font-semibold text-text-secondary hover:bg-surface-muted">Cancel</a>
                <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-control bg-brand-primary px-5 text-sm font-semibold text-white hover:bg-brand-800">{{ $event->status === 'rejected' ? 'Resubmit for review' : 'Save changes' }}</button>
            </div>
        </form>
    </section>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('card_image')?.addEventListener('change', (event) => {
    const file = event.target.files?.[0];
    const preview = document.getElementById('imagePreview');
    const image = document.getElementById('previewImg');
    if (!file || !preview || !image) {
        if (preview) preview.hidden = true;
        return;
    }
    image.src = URL.createObjectURL(file);
    preview.hidden = false;
});
</script>
@endsection
