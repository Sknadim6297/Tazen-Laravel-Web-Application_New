@props(['posts' => null])

@php
    $recentPosts = collect($posts ?? []);
    $resolveBlogImage = static function ($path): ?string {
        if (!filled($path)) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $parts = parse_url($path);
            $scheme = strtolower($parts['scheme'] ?? '');
            $host = strtolower($parts['host'] ?? '');
            $urlPath = $parts['path'] ?? '';

            if (!in_array($scheme, ['http', 'https'], true)) {
                return null;
            }

            if (!in_array($host, ['localhost', '127.0.0.1'], true) || !str_starts_with($urlPath, '/storage/')) {
                return $path;
            }

            $path = substr($urlPath, strlen('/storage/'));
        }

        $path = ltrim($path, '/');
        $storagePath = str_starts_with($path, 'storage/')
            ? substr($path, strlen('storage/'))
            : $path;

        if (is_file(public_path($path))) {
            return asset($path);
        }

        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($storagePath)
            && is_file(public_path('storage/' . $storagePath))) {
            return asset('storage/' . $storagePath);
        }

        return null;
    };
@endphp

<section aria-labelledby="yuktione-recent-blogs-title" class="bg-surface-muted py-14 sm:py-18 lg:py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-5 sm:mb-10 sm:flex-row sm:items-end sm:justify-between">
            <div class="max-w-2xl">
                <p class="mb-3 text-sm font-semibold uppercase text-brand-primary">Ideas and guidance</p>
                <h2 id="yuktione-recent-blogs-title" class="font-display text-3xl font-semibold leading-tight text-brand-secondary sm:text-4xl">
                    Recent Blogs
                </h2>
                <p class="mt-3 text-base leading-7 text-text-secondary">Read the latest articles from Yuktione.</p>
            </div>

            <x-tazen-v2.ui.button href="{{ route('blog.index') }}" variant="outline" size="md" class="shrink-0">
                View all blogs
            </x-tazen-v2.ui.button>
        </div>

        @if ($recentPosts->isEmpty())
            <div class="rounded-card border border-border bg-surface px-5 py-8 text-center sm:px-8">
                <p class="text-base font-medium text-text-primary">No published blogs are available right now.</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-3 lg:gap-6">
                @foreach ($recentPosts as $post)
                    @php
                        $blog = $post->blog;
                        $title = $blog?->title;
                        $detailUrl = filled($title) ? route('blog.show', \Illuminate\Support\Str::slug($title)) : null;
                        $imageUrl = $resolveBlogImage($post->image);
                    @endphp

                    @if (filled($title) && $detailUrl)
                        <article aria-labelledby="yuktione-blog-{{ $post->id }}" class="group flex h-full flex-col overflow-hidden rounded-card border border-border bg-surface shadow-card transition-shadow hover:shadow-popover">
                            @if ($imageUrl)
                                <a href="{{ $detailUrl }}" tabindex="-1" aria-hidden="true" class="block overflow-hidden">
                                    <img
                                        src="{{ $imageUrl }}"
                                        alt=""
                                        width="800"
                                        height="500"
                                        loading="lazy"
                                        class="aspect-16/10 w-full object-cover transition-transform duration-300 group-hover:scale-[1.02]"
                                    >
                                </a>
                            @else
                                <div aria-hidden="true" class="flex aspect-16/10 items-center justify-center bg-linear-to-br from-brand-700 to-brand-400">
                                    <span class="rounded-full border border-white/70 bg-white/10 px-4 py-2 text-sm font-semibold text-white">Yuktione</span>
                                </div>
                            @endif

                            <div class="flex flex-1 flex-col p-5 sm:p-6">
                                @if ($post->published_at)
                                    <time datetime="{{ $post->published_at->toDateString() }}" class="text-xs font-medium text-text-muted">
                                        {{ $post->published_at->format('M j, Y') }}
                                    </time>
                                @endif

                                <h3 id="yuktione-blog-{{ $post->id }}" class="mt-2 font-display text-card-title font-semibold leading-snug text-brand-secondary">
                                    {{ $title }}
                                </h3>

                                @if (filled($blog->description_short))
                                    <p class="mt-2 line-clamp-3 text-sm leading-6 text-text-secondary">{{ $blog->description_short }}</p>
                                @endif

                                <a href="{{ $detailUrl }}" class="mt-auto inline-flex min-h-10 items-center gap-2 pt-4 text-sm font-semibold text-brand-primary underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring">
                                    Read more
                                    <span aria-hidden="true">&rarr;</span>
                                </a>
                            </div>
                        </article>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</section>