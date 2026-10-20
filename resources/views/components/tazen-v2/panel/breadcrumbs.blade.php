@props(['items' => []])

<nav {{ $attributes->class('text-sm') }} aria-label="Breadcrumb">
    <ol class="flex flex-wrap items-center gap-2 text-text-muted">
        @foreach ($items as $item)
            <li class="inline-flex items-center gap-2">
                @if (! $loop->first)
                    <span aria-hidden="true">/</span>
                @endif
                @if (! empty($item['url']) && ! $loop->last)
                    <a href="{{ $item['url'] }}" class="font-medium text-text-secondary hover:text-brand-primary">{{ $item['label'] }}</a>
                @else
                    <span @if ($loop->last) aria-current="page" @endif class="font-medium text-text-primary">{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
