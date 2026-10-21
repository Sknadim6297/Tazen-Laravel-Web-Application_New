@extends('professional.layout.layout')

@section('title', 'Dashboard | Yuktione')

@section('content')
    @php
        $professionalId = Auth::guard('professional')->id();
        $professional = Auth::guard('professional')->user();
        $today = \Carbon\Carbon::today();
        $yesterday = \Carbon\Carbon::now()->subDay();

        $bookingsQuery = \App\Models\Booking::where('professional_id', $professionalId);
        $totalBookings = (clone $bookingsQuery)->count();
        $newBookings = (clone $bookingsQuery)->where('created_at', '>=', $yesterday)->count();
        $activeClients = (clone $bookingsQuery)->distinct('user_id')->count('user_id');

        $totalRevenue = (clone $bookingsQuery)
            ->where('paid_status', 'paid')
            ->sum('amount');
        $marginRate = $professional->margin ?? 20;
        $actualEarnings = $totalRevenue * ((100 - $marginRate) / 100);

        $previousMonth = now()->subMonth();
        $previousEarningsRaw = (clone $bookingsQuery)
            ->where('paid_status', 'paid')
            ->whereBetween('paid_date', [$previousMonth->copy()->startOfMonth(), $previousMonth->copy()->endOfMonth()])
            ->sum('amount');
        $previousEarnings = $previousEarningsRaw * ((100 - $marginRate) / 100);
        $percentChange = $previousEarnings > 0
            ? (($actualEarnings - $previousEarnings) / $previousEarnings) * 100
            : 0;

        $pendingAppointments = \App\Models\Booking::where('professional_id', $professionalId)
            ->whereHas('timedates', fn ($query) => $query->where('status', 'pending'))
            ->with(['timedates' => fn ($query) => $query->where('status', 'pending')->orderBy('date')])
            ->get();
        $pendingCount = $pendingAppointments->sum(fn ($booking) => $booking->timedates->count());
        $todayPendingAppointments = \App\Models\Booking::where('professional_id', $professionalId)
            ->whereHas('timedates', fn ($query) => $query->where('status', 'pending')->where('date', $today->format('Y-m-d')))
            ->with(['timedates' => fn ($query) => $query->where('status', 'pending')->where('date', $today->format('Y-m-d'))])
            ->get();
        $todayPendingCount = $todayPendingAppointments->sum(fn ($booking) => $booking->timedates->count());
        $futurePendingAppointments = \App\Models\Booking::where('professional_id', $professionalId)
            ->whereHas('timedates', fn ($query) => $query->where('status', 'pending')->where('date', '>', $today->format('Y-m-d')))
            ->with(['timedates' => fn ($query) => $query->where('status', 'pending')->where('date', '>', $today->format('Y-m-d'))])
            ->get();
        $futurePendingCount = $futurePendingAppointments->sum(fn ($booking) => $booking->timedates->count());

        $hasServices = \App\Models\ProfessionalService::where('professional_id', $professionalId)->exists();
        $hasRates = \App\Models\Rate::where('professional_id', $professionalId)->exists();
        $hasAvailability = \App\Models\Availability::where('professional_id', $professionalId)->exists();
        $hasRequestedServices = \App\Models\ProfessionalOtherInformation::where('professional_id', $professionalId)->exists();

        $serviceRequestMargin = $professional->service_request_margin ?? 10;
        $negotiationOffset = $professional->service_request_offset ?? 20;
    @endphp

    <div class="space-y-6">
        <section class="professional-dashboard-hero overflow-hidden rounded-panel p-6 text-white shadow-card sm:p-8">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                <div class="max-w-2xl">
                    <p class="text-sm font-semibold text-white/80">Professional dashboard</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-tight sm:text-3xl">
                        Welcome back, {{ $professional->name }}
                    </h2>
                    <p class="mt-3 max-w-xl text-sm leading-6 text-white/85 sm:text-base">
                        Your bookings, earnings, and profile setup at a glance.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a
                        href="{{ route('professional.profile.index') }}"
                        class="inline-flex min-h-11 items-center justify-center rounded-control bg-white px-4 text-sm font-semibold text-brand-primary transition hover:bg-brand-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
                    >
                        Manage profile
                    </a>
                    <a
                        href="{{ route('professional.booking.index') }}"
                        class="inline-flex min-h-11 items-center justify-center rounded-control border border-white/40 px-4 text-sm font-semibold text-white transition hover:bg-white/10 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
                    >
                        View bookings
                    </a>
                </div>
            </div>
        </section>

        @if($newBookings > 0)
            <section class="flex gap-3 rounded-panel border border-info/20 bg-info-surface p-4 text-sm text-text-primary" role="status">
                <x-tazen-v2.panel.icon name="bell" class="mt-0.5 size-5 text-info" />
                <p>
                    <span class="font-semibold">New booking activity.</span>
                    {{ $newBookings }} booking(s) were created in the last 24 hours.
                    <a href="{{ route('professional.booking.index') }}" class="font-semibold text-brand-primary underline underline-offset-2">Review bookings</a>
                </p>
            </section>
        @endif

        @if(!$hasServices || !$hasRates || !$hasAvailability || !$hasRequestedServices)
            <section class="rounded-panel border border-warning/30 bg-warning-surface p-5" aria-labelledby="setup-heading">
                <div class="flex items-start gap-3">
                    <x-tazen-v2.panel.icon name="warning" class="mt-0.5 size-5 text-warning" />
                    <div class="min-w-0">
                        <h2 id="setup-heading" class="text-base font-semibold text-text-primary">Complete your professional setup</h2>
                        <p class="mt-1 text-sm text-text-secondary">Finish these sections to prepare your profile for bookings.</p>
                        <ul class="mt-4 flex flex-wrap gap-2">
                            @unless($hasServices)
                                <li><a href="{{ route('professional.service.index') }}" class="inline-flex min-h-10 items-center rounded-control border border-warning/30 bg-white px-3 text-sm font-medium text-brand-primary hover:bg-brand-50">Add services</a></li>
                            @endunless
                            @unless($hasRates)
                                <li><a href="{{ route('professional.rate.index') }}" class="inline-flex min-h-10 items-center rounded-control border border-warning/30 bg-white px-3 text-sm font-medium text-brand-primary hover:bg-brand-50">Set rates</a></li>
                            @endunless
                            @unless($hasAvailability)
                                <li><a href="{{ route('professional.availability.index') }}" class="inline-flex min-h-10 items-center rounded-control border border-warning/30 bg-white px-3 text-sm font-medium text-brand-primary hover:bg-brand-50">Set availability</a></li>
                            @endunless
                            @unless($hasRequestedServices)
                                <li><a href="{{ route('professional.requested_services.index') }}" class="inline-flex min-h-10 items-center rounded-control border border-warning/30 bg-white px-3 text-sm font-medium text-brand-primary hover:bg-brand-50">Other information</a></li>
                            @endunless
                        </ul>
                    </div>
                </div>
            </section>
        @endif

        <section aria-label="Professional performance">
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <x-tazen-v2.panel.metric-card
                    label="Total bookings"
                    :value="number_format($totalBookings)"
                    icon="calendar"
                    hint="All bookings assigned to you"
                />
                <x-tazen-v2.panel.metric-card
                    label="Your earnings"
                    :value="'₹' . number_format($actualEarnings, 2)"
                    icon="wallet"
                    :hint="$previousEarnings > 0 ? number_format(abs($percentChange), 1) . '% ' . ($percentChange >= 0 ? 'up' : 'down') . ' from last month' : 'No earnings recorded last month'"
                />
                <x-tazen-v2.panel.metric-card
                    label="Active clients"
                    :value="number_format($activeClients)"
                    icon="user"
                    hint="Unique customers with bookings"
                />
                <x-tazen-v2.panel.metric-card
                    label="Pending appointments"
                    :value="number_format($pendingCount)"
                    icon="clock"
                    :hint="$todayPendingCount . ' today · ' . $futurePendingCount . ' upcoming'"
                />
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-[minmax(0,1.4fr)_minmax(18rem,0.8fr)]">
            <div class="rounded-panel border border-border bg-surface p-5 shadow-card sm:p-6">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-brand-secondary">Commission & fees</h2>
                        <p class="mt-1 text-sm text-text-secondary">Current rates configured for your account.</p>
                    </div>
                    <x-tazen-v2.panel.icon name="wallet" class="size-6 text-brand-primary" />
                </div>
                <div class="mt-5 grid gap-3 sm:grid-cols-3">
                    <div class="rounded-control bg-surface-muted p-4">
                        <p class="text-sm font-medium text-text-secondary">Main margin</p>
                        <p class="mt-2 text-2xl font-semibold text-brand-secondary">{{ number_format($marginRate, 2) }}%</p>
                        <p class="mt-1 text-xs leading-5 text-text-muted">Platform commission on bookings</p>
                    </div>
                    <div class="rounded-control bg-surface-muted p-4">
                        <p class="text-sm font-medium text-text-secondary">Service request margin</p>
                        <p class="mt-2 text-2xl font-semibold text-brand-secondary">{{ number_format($serviceRequestMargin, 2) }}%</p>
                        <p class="mt-1 text-xs leading-5 text-text-muted">Commission on additional requests</p>
                    </div>
                    <div class="rounded-control bg-surface-muted p-4">
                        <p class="text-sm font-medium text-text-secondary">Negotiation offset</p>
                        <p class="mt-2 text-2xl font-semibold text-brand-secondary">{{ number_format($negotiationOffset, 2) }}%</p>
                        <p class="mt-1 text-xs leading-5 text-text-muted">Configured negotiation limit</p>
                    </div>
                </div>
            </div>

            <div class="rounded-panel border border-border bg-surface p-5 shadow-card sm:p-6">
                <h2 class="text-lg font-semibold text-brand-secondary">Quick links</h2>
                <p class="mt-1 text-sm text-text-secondary">Manage your professional workspace.</p>
                <div class="mt-4 grid gap-2">
                    <a href="{{ route('professional.additional-services.index') }}" class="flex min-h-11 items-center justify-between rounded-control border border-border px-3 text-sm font-medium text-text-primary hover:bg-surface-muted">
                        Additional services <x-tazen-v2.panel.icon name="arrow" class="size-4 text-brand-primary" />
                    </a>
                    <a href="{{ route('professional.billing.index') }}" class="flex min-h-11 items-center justify-between rounded-control border border-border px-3 text-sm font-medium text-text-primary hover:bg-surface-muted">
                        Billing <x-tazen-v2.panel.icon name="arrow" class="size-4 text-brand-primary" />
                    </a>
                    <a href="{{ route('professional.plans.my-plan') }}" class="flex min-h-11 items-center justify-between rounded-control border border-border px-3 text-sm font-medium text-text-primary hover:bg-surface-muted">
                        My plan <x-tazen-v2.panel.icon name="arrow" class="size-4 text-brand-primary" />
                    </a>
                </div>
            </div>
        </section>
    </div>
@endsection
