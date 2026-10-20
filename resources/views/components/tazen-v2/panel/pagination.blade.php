@props(['paginator'])

@if ($paginator && $paginator->hasPages())
    <nav {{ $attributes->class('flex flex-wrap items-center justify-between gap-3') }} aria-label="Pagination">
        <p class="text-sm text-text-secondary">
            Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }}
        </p>
        <div class="flex flex-wrap gap-2">
            {{ $paginator->onEachSide(1)->links() }}
        </div>
    </nav>
@endif
