@php
    $everydayTools = [
        [
            'title' => 'Birth Chart',
            'text' => 'Discover your unique cosmic blueprint.',
            'tone' => 'lavender',
            'accent' => 'purple',
            'art' => 'chart',
        ],
        [
            'title' => 'Daily Horoscope',
            'text' => 'Get personalised insights for your day.',
            'tone' => 'mint',
            'accent' => 'teal',
            'art' => 'sun',
        ],
        [
            'title' => 'Mood Tracker',
            'text' => 'Understand your moods better.',
            'tone' => 'blush',
            'accent' => 'pink',
            'art' => 'mood',
        ],
        [
            'title' => 'Goal Planner',
            'text' => 'Turn your dreams into actionable steps.',
            'tone' => 'blue',
            'accent' => 'purple',
            'art' => 'target',
        ],
        [
            'title' => 'Tarot Reading',
            'text' => 'Find guidance for today and beyond.',
            'tone' => 'cream',
            'accent' => 'orange',
            'art' => 'tarot',
        ],
        [
            'title' => 'Compatibility',
            'text' => 'Explore your connection with others.',
            'tone' => 'mint',
            'accent' => 'teal',
            'art' => 'hearts',
        ],
    ];
@endphp

<section class="yk-tools" aria-labelledby="yk-tools-title">
    <div class="yk-tools__inner">
        <header class="yk-tools__head">
            <div class="yk-tools__copy">
                <p class="yk-tools__eyebrow">Tools for everyday clarity</p>
                <h2 class="yk-tools__title" id="yk-tools-title">
                    <span class="yk-tools__heading-navy">Simple tools.</span>
                    <span class="yk-tools__heading-lavender">Real insights.</span>
                </h2>
                <p class="yk-tools__lead">Explore our easy-to-use tools designed to bring more clarity, confidence and balance to your everyday life.</p>
            </div>
            <span class="yk-tools__all">View All Tools <span aria-hidden="true">→</span></span>
            <svg class="yk-tools__leaves" viewBox="0 0 180 150" aria-hidden="true" focusable="false">
                <path d="M96 148c8-46 22-84 48-124" fill="none" stroke="#7d9a68" stroke-width="2.2" stroke-linecap="round"></path>
                <path d="M138 18c16 18 16 42 2 60-22-8-36-32-32-56 10-2 20-4 30-4Z" fill="#9d81db" opacity=".85"></path>
                <path d="M128 58c22 6 40 22 44 46-26 2-44-10-56-28 2-8 6-14 12-18Z" fill="#8aaf74"></path>
                <path d="M118 96c18 12 26 32 20 54-22-4-36-20-40-40 6-6 12-10 20-14Z" fill="#b7a4e4"></path>
                <path d="M108 128c12 10 14 26 6 40-16-8-24-22-20-36 4-2 8-3 14-4Z" fill="#7ea36a"></path>
            </svg>
        </header>

        <ul class="yk-tools__grid">
            @foreach ($everydayTools as $tool)
                <li>
                    <article class="yk-tools__card yk-tools__card--{{ $tool['tone'] }}">
                        <div class="yk-tools__art">
                            <svg viewBox="0 0 160 160" aria-hidden="true" focusable="false">
                                @switch($tool['art'])
                                    @case('chart')
                                        <circle cx="80" cy="82" r="62" fill="#E6DCFF"></circle>
                                        <circle cx="80" cy="82" r="54" fill="#F8F4FF" stroke="#6E52C0" stroke-width="3"></circle>
                                        <circle cx="80" cy="82" r="40" fill="none" stroke="#C9B6F0" stroke-width="1.6"></circle>
                                        <g stroke="#6E52C0" stroke-width="1.7" stroke-linecap="round">
                                            <path d="M80 32v8M80 124v8M30 82h8M122 82h8"></path>
                                            <path d="M44 46l6 6M110 112l6 6M116 46l-6 6M50 112l-6 6"></path>
                                        </g>
                                        <circle cx="80" cy="46" r="2.2" fill="#6E52C0"></circle>
                                        <circle cx="116" cy="82" r="2.2" fill="#6E52C0"></circle>
                                        <circle cx="80" cy="118" r="2.2" fill="#6E52C0"></circle>
                                        <circle cx="44" cy="82" r="2.2" fill="#6E52C0"></circle>
                                        <path d="M92 64a20 20 0 1 0 2 36 15 15 0 1 1-2-36Z" fill="#6A4EBA"></path>
                                        <path d="M112 42l1.6 3.4 3.4 1.6-3.4 1.6L112 54l-1.6-3.4-3.4-1.6 3.4-1.6Z" fill="#9D81DB"></path>
                                        @break
                                    @case('sun')
                                        <g stroke="#F0B429" stroke-width="3.2" stroke-linecap="round">
                                            <path d="M80 22v16M80 102v12M40 62H24M136 62h-16"></path>
                                            <path d="M50 32l10 10M110 32l-10 10M50 92l10-10M110 92l-10-10"></path>
                                        </g>
                                        <circle cx="80" cy="62" r="28" fill="#FFD15C"></circle>
                                        <circle cx="80" cy="62" r="18" fill="#FFE7A0"></circle>
                                        <ellipse cx="78" cy="118" rx="34" ry="16" fill="#E7F0EA"></ellipse>
                                        <ellipse cx="54" cy="108" rx="20" ry="14" fill="#fff"></ellipse>
                                        <ellipse cx="86" cy="114" rx="26" ry="16" fill="#fff"></ellipse>
                                        <ellipse cx="112" cy="106" rx="18" ry="13" fill="#fff"></ellipse>
                                        <ellipse cx="70" cy="100" rx="14" ry="10" fill="#fff"></ellipse>
                                        @break
                                    @case('mood')
                                        <path d="M34 78c2-22 22-32 36-18 6-16 28-20 40-4 16 20 4 46-38 68C42 120 32 98 34 78Z" fill="#7EAA72"></path>
                                        <path d="M126 74c-2-20-20-28-32-14-4-14-24-18-34-2-14 18-2 40 34 58 28-16 34-28 32-42Z" fill="#8FB67C"></path>
                                        <path d="M80 128C46 104 34 82 42 64c8-16 26-16 34 0 2-8 10-16 22-16 16 0 26 12 26 26 0 22-16 40-44 54Z" fill="#F7A8BC"></path>
                                        <path d="M80 120c-24-18-32-34-26-48 6-12 18-12 24 2 2-8 10-14 18-14 12 0 20 10 20 20 0 16-12 30-36 40Z" fill="#F8C3D0"></path>
                                        <circle cx="68" cy="78" r="2.4" fill="#C45274"></circle>
                                        <circle cx="92" cy="78" r="2.4" fill="#C45274"></circle>
                                        <path d="M70 90c4 5 16 5 20 0" fill="none" stroke="#C45274" stroke-width="2" stroke-linecap="round"></path>
                                        <path d="M118 48c-6 1-10 6-10 12 8-1 14-6 14-12-1 0-3 0-4 0Z" fill="#F28AA6"></path>
                                        <path d="M40 46c6 1 10 6 10 12-8-1-14-6-14-12 1 0 3 0 4 0Z" fill="#F28AA6"></path>
                                        @break
                                    @case('target')
                                        <circle cx="74" cy="88" r="50" fill="#DDD4FF"></circle>
                                        <circle cx="74" cy="88" r="36" fill="#B7A4EE"></circle>
                                        <circle cx="74" cy="88" r="22" fill="#F4F0FF"></circle>
                                        <circle cx="74" cy="88" r="10" fill="#6E52C0"></circle>
                                        <circle cx="74" cy="88" r="4" fill="#fff"></circle>
                                        <path d="M126 36 92 70" fill="none" stroke="#5C3FA8" stroke-width="4.5" stroke-linecap="round"></path>
                                        <path d="M78 86l20-26 2 16z" fill="#5C3FA8"></path>
                                        <path d="M122 30l8 10M132 40l-12 6" fill="none" stroke="#7A62C4" stroke-width="2.4" stroke-linecap="round"></path>
                                        <path d="M46 40l2 5 5 2-5 2-2 5-2-5-5-2 5-2Z" fill="#9D81DB"></path>
                                        @break
                                    @case('tarot')
                                        <g transform="rotate(-14 62 86)">
                                            <rect x="28" y="36" width="68" height="98" rx="10" fill="#F4E4C4" stroke="#E2C48A" stroke-width="2"></rect>
                                            <rect x="36" y="44" width="52" height="82" rx="6" fill="none" stroke="#D2B06A" stroke-width="1.5"></rect>
                                            <circle cx="62" cy="84" r="10" fill="none" stroke="#D2B06A" stroke-width="1.5"></circle>
                                        </g>
                                        <g transform="rotate(8 98 82)">
                                            <rect x="64" y="28" width="70" height="102" rx="10" fill="#3A2870"></rect>
                                            <rect x="72" y="36" width="54" height="86" rx="6" fill="none" stroke="#D4C4F6" stroke-width="1.4"></rect>
                                            <path d="M104 62a14 14 0 1 0 1 26 11 11 0 1 1-1-26Z" fill="#F6E7A8"></path>
                                            <path d="M84 52l1.2 2.6 2.6 1.2-2.6 1.2L84 60l-1.2-2.6-2.6-1.2 2.6-1.2Z" fill="#F6E7A8"></path>
                                            <path d="M118 96l1 2.2 2.2 1-2.2 1-1 2.2-1-2.2-2.2-1 2.2-1Z" fill="#F6E7A8"></path>
                                        </g>
                                        <path d="M28 40l1.4 3 3 1.4-3 1.4-1.4 3-1.4-3-3-1.4 3-1.4Z" fill="#E2A04A"></path>
                                        <path d="M136 28l1.2 2.4 2.4 1.2-2.4 1.2-1.2 2.4-1.2-2.4-2.4-1.2 2.4-1.2Z" fill="#E2A04A"></path>
                                        @break
                                    @case('hearts')
                                        <ellipse cx="80" cy="84" rx="62" ry="30" fill="none" stroke="#C5D4F0" stroke-width="1.6" stroke-dasharray="3 5"></ellipse>
                                        <circle cx="22" cy="78" r="3" fill="#9D81DB"></circle>
                                        <circle cx="138" cy="90" r="3" fill="#F28AA6"></circle>
                                        <circle cx="80" cy="52" r="2.4" fill="#9D81DB"></circle>
                                        <circle cx="86" cy="116" r="2.4" fill="#F28AA6"></circle>
                                        <path d="M58 96C40 82 34 68 40 58c6-10 16-8 20 2 2-6 8-12 16-10 8 2 12 12 8 22-6 14-16 22-26 24Z" fill="#F7A8BC"></path>
                                        <path d="M104 108c-16-12-22-26-16-36 5-8 14-8 18 2 2-6 8-10 14-8 8 2 12 10 8 18-6 14-14 22-24 24Z" fill="#C9B6F2"></path>
                                        <path d="M30 108l1.2 2.6 2.6 1.2-2.6 1.2-1.2 2.6-1.2-2.6-2.6-1.2 2.6-1.2Z" fill="#9D81DB"></path>
                                        <path d="M132 48l1.2 2.6 2.6 1.2-2.6 1.2L132 56l-1.2-2.6-2.6-1.2 2.6-1.2Z" fill="#F28AA6"></path>
                                        @break
                                @endswitch
                            </svg>
                        </div>
                        <h3 class="yk-tools__name">{{ $tool['title'] }}</h3>
                        <p class="yk-tools__text">{{ $tool['text'] }}</p>
                        <span class="yk-tools__cta yk-tools__cta--{{ $tool['accent'] }}">
                            Get Started
                            <span class="yk-tools__arrow" aria-hidden="true">
                                <svg viewBox="0 0 28 28" focusable="false">
                                    <circle cx="14" cy="14" r="12.2" fill="none" stroke="currentColor" stroke-width="1.4"></circle>
                                    <path d="M10.5 14h7M14.8 10.6 18.2 14l-3.4 3.4" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </span>
                        </span>
                    </article>
                </li>
            @endforeach
        </ul>
    </div>
</section>
