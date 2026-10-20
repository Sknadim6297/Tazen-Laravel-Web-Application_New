@props(['action', 'method' => 'GET'])

<form action="{{ $action }}" method="{{ $method }}" {{ $attributes->class('grid gap-4 rounded-card border border-border bg-surface p-4 shadow-card sm:grid-cols-2 lg:grid-cols-4') }}>
    {{ $slot }}
    <div class="flex items-end">
        <x-tazen-v2.ui.button type="submit" class="w-full">Apply filters</x-tazen-v2.ui.button>
    </div>
</form>
