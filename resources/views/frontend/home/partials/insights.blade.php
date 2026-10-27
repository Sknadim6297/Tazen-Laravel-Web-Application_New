@php
    $insightCards = [
        [
            'badge' => 'Daily Horoscope',
            'title' => 'Your stars today',
            'text' => 'See what the universe has in store for you.',
            'tone' => 'lavender',
            'accent' => 'purple',
            'art' => 'chart',
            'cta' => 'Read Horoscope',
        ],
        [
            'badge' => 'Wellness Tip',
            'title' => 'A calmer mind today',
            'text' => 'Simple ways to feel more grounded.',
            'tone' => 'mint',
            'accent' => 'teal',
            'art' => 'meditate',
            'cta' => 'Read Tips',
        ],
        [
            'badge' => 'Relationships',
            'title' => 'Build stronger connections',
            'text' => 'Practical advice for healthier relationships.',
            'tone' => 'blush',
            'accent' => 'pink',
            'art' => 'hearts',
            'cta' => 'Read Advice',
        ],
        [
            'badge' => 'Expert Insight',
            'title' => 'Guidance from experts',
            'text' => 'Real advice for real questions.',
            'tone' => 'cream',
            'accent' => 'orange',
            'art' => 'expert',
            'cta' => 'Explore Insights',
            'url' => route('blog.index'),
        ],
    ];
@endphp

<section class="yk-insight" aria-labelledby="yk-insight-title">
    <div class="yk-insight__inner">
        <header class="yk-insight__head">
            <div class="yk-insight__copy">
                <p class="yk-insight__eyebrow">Today's insight</p>
                <h2 class="yk-insight__title" id="yk-insight-title">
                    <span class="yk-insight__heading-navy">Fresh perspectives for</span>
                    <span class="yk-insight__heading-lavender">a brighter you.</span>
                </h2>
                <p class="yk-insight__lead">Explore daily insights, featured content and expert advice to guide your day.</p>
            </div>
            <svg class="yk-insight__leaves" viewBox="0 0 180 150" aria-hidden="true" focusable="false">
                <path d="M92 146c10-48 26-86 52-122" fill="none" stroke="#7d9a68" stroke-width="2.2" stroke-linecap="round"></path>
                <path d="M136 16c18 16 18 40 4 58-22-6-38-30-34-54 10-2 20-4 30-4Z" fill="#b7a4e4"></path>
                <path d="M126 56c24 4 42 22 46 46-28 2-46-12-58-30 2-6 6-12 12-16Z" fill="#8aaf74"></path>
                <path d="M116 98c20 10 28 32 20 54-24-2-40-20-42-42 6-6 14-10 22-12Z" fill="#9d81db" opacity=".8"></path>
                <path d="M104 128c14 8 16 24 6 40-18-6-28-22-22-38 4-2 10-2 16-2Z" fill="#7ea36a"></path>
            </svg>
        </header>

        <ul class="yk-insight__grid">
            @foreach ($insightCards as $card)
                <li>
                    <article class="yk-insight__card yk-insight__card--{{ $card['tone'] }}">
                        <div class="yk-insight__body">
                            <p class="yk-insight__badge yk-insight__badge--{{ $card['accent'] }}">{{ $card['badge'] }}</p>
                            <h3 class="yk-insight__name">{{ $card['title'] }}</h3>
                            <p class="yk-insight__text">{{ $card['text'] }}</p>
                            @if (!empty($card['url']))
                                <a class="yk-insight__cta yk-insight__cta--{{ $card['accent'] }}" href="{{ $card['url'] }}">
                            @else
                                <span class="yk-insight__cta yk-insight__cta--{{ $card['accent'] }}">
                            @endif
                                {{ $card['cta'] }} <span aria-hidden="true">→</span>
                            @if (!empty($card['url']))
                                </a>
                            @else
                                </span>
                            @endif
                        </div>
                        <div class="yk-insight__art">
                            <svg viewBox="0 0 160 170" aria-hidden="true" focusable="false">
                                @switch($card['art'])
                                    @case('chart')
                                        <circle cx="82" cy="86" r="62" fill="#E4D9FF"></circle>
                                        <circle cx="82" cy="86" r="52" fill="#F8F5FF" stroke="#6E52C0" stroke-width="3"></circle>
                                        <circle cx="82" cy="86" r="38" fill="none" stroke="#CDBEF2" stroke-width="1.6"></circle>
                                        <g stroke="#6E52C0" stroke-width="1.7" stroke-linecap="round">
                                            <path d="M82 38v8M82 126v8M34 86h8M122 86h8"></path>
                                            <path d="M48 52l6 6M110 114l6 6M116 52l-6 6M54 114l-6 6"></path>
                                        </g>
                                        <path d="M96 66a22 22 0 1 0 2 40 16 16 0 1 1-2-40Z" fill="#6A4EBA"></path>
                                        <path d="M118 48l1.6 3.4 3.4 1.6-3.4 1.6-1.6 3.4-1.6-3.4-3.4-1.6 3.4-1.6Z" fill="#9D81DB"></path>
                                        @break
                                    @case('meditate')
                                        <path d="M16 78c18-10 26 16 8 30-14-4-20-16-8-30Z" fill="#8FB67C"></path>
                                        <path d="M144 72c-16-8-28 14-10 28 14-2 22-16 10-28Z" fill="#7EA36A"></path>
                                        <path d="M22 118c14 2 18 18 4 26-12-6-16-16-4-26Z" fill="#A9C992"></path>
                                        <path d="M138 114c-12 4-14 20-2 26 10-8 12-18 2-26Z" fill="#8FB67C"></path>
                                        <ellipse cx="80" cy="148" rx="36" ry="10" fill="#D5EADF"></ellipse>
                                        <path d="M48 128c12 18 52 18 64 0-18 8-46 8-64 0Z" fill="#3F7C72"></path>
                                        <path d="M66 112c2-28 26-32 28-4v18H66Z" fill="#2F6B62"></path>
                                        <path d="M66 116c-16 2-20 16-8 18 6-6 10-12 8-18Z" fill="#F4D3BE"></path>
                                        <path d="M94 116c16 2 20 16 8 18-6-6-10-12-8-18Z" fill="#F4D3BE"></path>
                                        <circle cx="80" cy="68" r="16" fill="#F6D5C0"></circle>
                                        <path d="M64 66c2-18 30-20 34 0-8-6-24-6-34 0Z" fill="#3C2C2A"></path>
                                        <path d="M72 70h5M85 70h5" stroke="#8A5A4A" stroke-width="1.6" stroke-linecap="round"></path>
                                        <path d="M76 78c2 2 6 2 8 0" fill="none" stroke="#C47A86" stroke-width="1.4" stroke-linecap="round"></path>
                                        @break
                                    @case('hearts')
                                        <path d="M58 118C34 98 28 74 40 60c10-12 24-8 28 6 4-16 22-20 34-6 16 18 6 44-44 58Z" fill="#F7A8BC"></path>
                                        <path d="M102 128c-20-14-28-32-18-46 8-12 22-10 26 4 4-12 18-14 26-2 12 16 2 38-34 44Z" fill="#CDB6F4"></path>
                                        <path d="M36 58c6 2 10 8 8 14-8-2-14-8-14-14 2 0 4 0 6 0Z" fill="#F28AA6"></path>
                                        <path d="M124 48l1.4 3 3 1.4-3 1.4-1.4 3-1.4-3-3-1.4 3-1.4Z" fill="#E95585"></path>
                                        <path d="M30 96l1.2 2.4 2.4 1.2-2.4 1.2-1.2 2.4-1.2-2.4-2.4-1.2 2.4-1.2Z" fill="#9D81DB"></path>
                                        @break
                                    @case('expert')
                                        <circle cx="80" cy="78" r="58" fill="#F8E7D4"></circle>
                                        <path d="M46 150c6-28 22-42 34-42s28 14 34 42H46Z" fill="#FFF7EF"></path>
                                        <path d="M58 118c8 10 36 10 44 0-6 16-38 16-44 0Z" fill="#F3D2BC"></path>
                                        <circle cx="80" cy="70" r="22" fill="#F6D4BE"></circle>
                                        <path d="M58 68c2-26 42-28 46 2-8 10-16 8-22 2-6 8-16 10-24-4Z" fill="#3A2A28"></path>
                                        <path d="M62 58c8-16 28-16 36 0" fill="none" stroke="#3A2A28" stroke-width="4" stroke-linecap="round"></path>
                                        <circle cx="72" cy="72" r="1.8" fill="#5C4038"></circle>
                                        <circle cx="90" cy="72" r="1.8" fill="#5C4038"></circle>
                                        <path d="M74 82c3 3 9 3 12 0" fill="none" stroke="#C46B78" stroke-width="1.6" stroke-linecap="round"></path>
                                        <rect x="54" y="112" width="52" height="36" rx="6" fill="#4E463F"></rect>
                                        <rect x="58" y="116" width="44" height="24" rx="3" fill="#F4F7FF"></rect>
                                        <path d="M66 124h20M66 130h14" stroke="#C9B6F2" stroke-width="2" stroke-linecap="round"></path>
                                        @break
                                @endswitch
                            </svg>
                        </div>
                    </article>
                </li>
            @endforeach
        </ul>
    </div>
</section>
