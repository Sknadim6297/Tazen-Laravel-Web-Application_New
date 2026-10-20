@props([
    'id' => 'panel-confirm-dialog',
    'title' => 'Please confirm',
    'confirmLabel' => 'Confirm',
    'cancelLabel' => 'Cancel',
])

<dialog id="{{ $id }}" {{ $attributes->class('w-full max-w-md rounded-panel border border-border bg-surface p-0 text-text-primary shadow-popover backdrop:bg-brand-950/40') }}>
    <form method="dialog" class="p-6">
        <h2 class="font-display text-card-title font-semibold text-brand-secondary">{{ $title }}</h2>
        <div class="mt-3 text-sm leading-6 text-text-secondary">
            {{ $slot }}
        </div>
        <div class="mt-6 flex justify-end gap-2">
            <x-tazen-v2.ui.button type="submit" variant="outline" value="cancel">{{ $cancelLabel }}</x-tazen-v2.ui.button>
            <x-tazen-v2.ui.button type="submit" variant="danger" value="confirm">{{ $confirmLabel }}</x-tazen-v2.ui.button>
        </div>
    </form>
</dialog>
