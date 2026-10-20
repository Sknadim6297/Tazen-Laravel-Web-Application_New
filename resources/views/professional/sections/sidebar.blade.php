@php
    $professionalNavigation = [
        [
            'title' => 'Workspace',
            'links' => [
                [
                    'label' => 'Dashboard',
                    'url' => route('professional.dashboard'),
                    'icon' => 'home',
                    'active' => request()->routeIs('professional.dashboard'),
                ],
                [
                    'label' => 'Messages',
                    'url' => route('professional.admin-chat.index'),
                    'icon' => 'chat',
                    'active' => request()->routeIs('professional.admin-chat.*'),
                ],
            ],
        ],
        [
            'title' => 'Profile & services',
            'links' => [
                [
                    'label' => 'Profile',
                    'url' => route('professional.profile.index'),
                    'icon' => 'user',
                    'active' => request()->routeIs('professional.profile.*'),
                ],
                [
                    'label' => 'Services',
                    'url' => route('professional.service.index'),
                    'icon' => 'plus',
                    'active' => request()->routeIs('professional.service.*'),
                ],
                [
                    'label' => 'Rates',
                    'url' => route('professional.rate.index'),
                    'icon' => 'wallet',
                    'active' => request()->routeIs('professional.rate.*'),
                ],
                [
                    'label' => 'Availability',
                    'url' => route('professional.availability.index'),
                    'icon' => 'clock',
                    'active' => request()->routeIs('professional.availability.*'),
                ],
                [
                    'label' => 'Other information',
                    'url' => route('professional.requested_services.index'),
                    'icon' => 'inbox',
                    'active' => request()->routeIs('professional.requested_services.*'),
                ],
            ],
        ],
        [
            'title' => 'Bookings & events',
            'links' => [
                [
                    'label' => 'All bookings',
                    'url' => route('professional.booking.index'),
                    'icon' => 'calendar',
                    'active' => request()->routeIs('professional.booking.*'),
                ],
                [
                    'label' => 'Additional services',
                    'url' => route('professional.additional-services.index'),
                    'icon' => 'plus',
                    'active' => request()->routeIs('professional.additional-services.*'),
                ],
                [
                    'label' => 'My events',
                    'url' => route('professional.events.index'),
                    'icon' => 'spark',
                    'active' => request()->routeIs('professional.events.*'),
                ],
                [
                    'label' => 'Event details',
                    'url' => route('professional.event-details.index'),
                    'icon' => 'inbox',
                    'active' => request()->routeIs('professional.event-details.*'),
                ],
                [
                    'label' => 'Event bookings',
                    'url' => route('professional.event-bookings.index'),
                    'icon' => 'calendar',
                    'active' => request()->routeIs('professional.event-bookings.*'),
                ],
                [
                    'label' => 'Reviews',
                    'url' => route('professional.reviews.index'),
                    'icon' => 'spark',
                    'active' => request()->routeIs('professional.reviews.*'),
                ],
            ],
        ],
        [
            'title' => 'Finance',
            'links' => [
                [
                    'label' => 'Billing',
                    'url' => route('professional.billing.index'),
                    'icon' => 'wallet',
                    'active' => request()->routeIs('professional.billing.*'),
                ],
                [
                    'label' => 'Browse plans',
                    'url' => route('professional.plans.index'),
                    'icon' => 'spark',
                    'active' => request()->routeIs('professional.plans.index'),
                ],
                [
                    'label' => 'My plan',
                    'url' => route('professional.plans.my-plan'),
                    'icon' => 'check',
                    'active' => request()->routeIs('professional.plans.my-plan'),
                ],
                [
                    'label' => 'Back to home',
                    'url' => route('home'),
                    'icon' => 'inbox',
                    'active' => false,
                ],
            ],
        ],
    ];
@endphp

<x-tazen-v2.panel.sidebar :items="$professionalNavigation" id="professional-panel-navigation">
    <x-slot:footer>
        <a
            href="{{ route('professional.logout') }}"
            class="flex min-h-11 items-center gap-3 rounded-control px-3 text-sm font-medium text-text-secondary transition-colors hover:bg-error-surface hover:text-error focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring"
        >
            <x-tazen-v2.panel.icon name="logout" class="size-5" />
            Sign out
        </a>
    </x-slot:footer>
</x-tazen-v2.panel.sidebar>
