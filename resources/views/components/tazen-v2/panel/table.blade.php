@props(['headers' => []])

<div {{ $attributes->class('overflow-x-auto rounded-card border border-border') }}>
    <table class="min-w-full text-left text-sm">
        @if (count($headers) > 0)
            <thead class="bg-surface-muted text-text-secondary">
                <tr>
                    @foreach ($headers as $header)
                        <th scope="col" class="whitespace-nowrap px-4 py-3 font-semibold">{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
        @endif
        <tbody class="divide-y divide-divider bg-surface text-text-primary">
            {{ $slot }}
        </tbody>
    </table>
</div>
