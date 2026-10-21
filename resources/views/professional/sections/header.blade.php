@php
    use App\Models\Profile;
    use App\Models\ProfessionalOtherInformation;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\DB;
    $professional = Auth::guard('professional')->user();
    $profile = $professional ? Profile::where('professional_id', $professional->id)->first() : null;
@endphp

@php
    // Ensure notification-related variables exist to avoid undefined variable errors
    // when the view is rendered without an authenticated professional.
    $newBookings = $newBookings ?? 0;
    $rescheduleCount = $rescheduleCount ?? 0;
    $rescheduleNotifications = $rescheduleNotifications ?? collect();
    $hasServices = $hasServices ?? false;
    $hasRates = $hasRates ?? false;
    $hasAvailability = $hasAvailability ?? false;
    $hasRequestedServices = $hasRequestedServices ?? false;
    $totalNotifications = $totalNotifications ?? 0;
@endphp

<header class="sticky top-0 z-30 flex min-h-18 items-center justify-between gap-4 border-b border-border bg-surface/95 px-4 py-3 backdrop-blur sm:px-6 lg:px-8">
    <div class="flex min-w-0 items-center gap-3">
        <button
            type="button"
            data-panel-open
            aria-expanded="false"
            aria-controls="professional-panel-navigation"
            class="inline-flex size-11 shrink-0 items-center justify-center rounded-control border border-border text-text-secondary hover:bg-surface-muted focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring lg:hidden"
            aria-label="Open navigation"
        >
            <x-tazen-v2.panel.icon name="menu" />
        </button>
        <div class="min-w-0">
            <p class="text-xs font-medium uppercase tracking-wide text-text-muted">Yuktione</p>
            <h1 class="truncate text-base font-semibold text-brand-secondary sm:text-lg">Professional workspace</h1>
        </div>
    </div>

    <div class="flex shrink-0 items-center gap-2 sm:gap-3">
        @if($professional)
            @php
                // Check for new bookings in the last 24 hours
                $professionalId = Auth::guard('professional')->id();
                $yesterday = \Carbon\Carbon::now()->subDay();
                $newBookings = \App\Models\Booking::where('professional_id', $professionalId)
                    ->where('created_at', '>=', $yesterday)
                    ->count();
                
                // Check for reschedule notifications (for notification icon only)
                $rescheduleNotifications = DB::table('notifications')
                    ->where('notifiable_type', 'App\Models\Professional')
                    ->where('notifiable_id', $professionalId)
                    ->where('type', 'App\Notifications\AppointmentRescheduled')
                    ->whereNull('read_at')
                    ->get();
                $rescheduleCount = $rescheduleNotifications->count();
                
                // Check if professional has services
                $hasServices = \App\Models\ProfessionalService::where('professional_id', $professionalId)->exists();
                
                // Check if professional has rates configured
                $hasRates = \App\Models\Rate::where('professional_id', $professionalId)->exists();
                
                // Check if professional has availability defined
                $hasAvailability = \App\Models\Availability::where('professional_id', $professionalId)->exists();
                
                // Check if professional has requested services (other information)
                $hasRequestedServices = \App\Models\ProfessionalOtherInformation::where('professional_id', $professionalId)->exists();
                
                // Get unread booking chats with booking details
                try {
                    // Get unread additional service notifications for display
                    $unreadNotifications = collect();
                    if ($professional) {
                        $unreadNotifications = $professional->unreadNotifications()
                            ->where('type', 'App\Notifications\AdditionalServiceNotification')
                            ->orderBy('created_at', 'desc')
                            ->get();
                    }

                    $unreadBookingChats = \App\Models\BookingChat::whereHas('booking', function($q) use ($professionalId) {
                            $q->where('professional_id', $professionalId);
                        })
                        ->where('sender_type', 'customer')
                        ->where('is_read', false)
                        ->where('is_system_message', false)
                        ->select('booking_id', DB::raw('COUNT(*) as unread_count'), DB::raw('MAX(created_at) as last_message_at'))
                        ->groupBy('booking_id')
                        ->orderBy('last_message_at', 'desc')
                        ->get();
                    
                    // Load booking relationships separately to avoid groupBy issues
                    if ($unreadBookingChats->isNotEmpty()) {
                        $bookingIds = $unreadBookingChats->pluck('booking_id')->toArray();
                        $bookings = \App\Models\Booking::with(['user', 'service'])
                            ->whereIn('id', $bookingIds)
                            ->get()
                            ->keyBy('id');
                        
                        // Attach booking data to each chat group
                        foreach ($unreadBookingChats as $chatGroup) {
                            $chatGroup->booking = $bookings->get($chatGroup->booking_id);
                        }
                    }
                    
                    $unreadChatCount = $unreadBookingChats->sum('unread_count');
                    
                    // Merge all notifications with timestamps for proper sorting
                    $allNotifications = collect();
                    
                    // Add additional service notifications
                    foreach($unreadNotifications as $notification) {
                        $allNotifications->push([
                            'type' => 'service_notification',
                            'timestamp' => $notification->created_at,
                            'data' => $notification
                        ]);
                    }
                    
                    // Add reschedule notifications
                    foreach($rescheduleNotifications as $notification) {
                        $allNotifications->push([
                            'type' => 'reschedule_notification',
                            'timestamp' => \Carbon\Carbon::parse($notification->created_at),
                            'data' => $notification
                        ]);
                    }
                    
                    // Add chat notifications
                    foreach($unreadBookingChats as $chatGroup) {
                        $allNotifications->push([
                            'type' => 'chat_notification',
                            'timestamp' => \Carbon\Carbon::parse($chatGroup->last_message_at),
                            'data' => $chatGroup
                        ]);
                    }
                    
                    // Sort all notifications by timestamp (newest first)
                    $allNotifications = $allNotifications->sortByDesc('timestamp');
                    
                } catch (\Exception $e) {
                    $unreadBookingChats = collect();
                    $unreadChatCount = 0;
                    $unreadNotifications = collect();
                    $rescheduleNotifications = collect();
                    $allNotifications = collect();
                    // Log error for debugging
                    \Log::error('Professional notification fetch error: ' . $e->getMessage());
                }
                
                // Count unread additional service notifications
                $unreadNotificationsCount = $unreadNotifications ? $unreadNotifications->count() : 0;
                
                // Count total notifications (including all notification types)
                $totalNotifications = $newBookings + $rescheduleCount + $unreadNotificationsCount + $unreadChatCount;
                if (!$hasServices) $totalNotifications++;
                if (!$hasRates) $totalNotifications++;
                if (!$hasAvailability) $totalNotifications++;
                if (!$hasRequestedServices) $totalNotifications++;

                // Debug: Log notification counts for troubleshooting
                \Log::info('Professional Notification Debug', [
                    'professional_id' => $professionalId,
                    'unread_notifications_count' => $unreadNotificationsCount,
                    'total_notifications' => $totalNotifications,
                    'new_bookings' => $newBookings,
                    'reschedule_count' => $rescheduleCount,
                    'unread_chat_count' => $unreadChatCount
                ]);
            @endphp
            
            @php
                $profileImage = $profile && $profile->photo ? $profile->photo : null;
                $imageUrl = $profileImage && file_exists(public_path('storage/' . $profileImage))
                    ? asset('storage/' . $profileImage)
                    : asset('default.jpg');
            @endphp
            <button
                type="button"
                class="notification-icon relative inline-flex size-11 items-center justify-center rounded-control border border-border text-text-secondary transition hover:bg-surface-muted focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring"
                id="notificationIcon"
                aria-label="Open notifications"
                aria-haspopup="dialog"
                aria-controls="notificationModal"
            >
                <x-tazen-v2.panel.icon name="bell" />
                @if($totalNotifications > 0)
                    <span class="notification-badge absolute -right-1 -top-1 inline-flex min-w-5 items-center justify-center rounded-full bg-error px-1 text-xs font-semibold leading-5 text-white">{{ $totalNotifications }}</span>
                @endif
            </button>

            <a
                href="{{ route('professional.admin-chat.index') }}"
                id="adminChatIcon"
                class="relative inline-flex size-11 items-center justify-center rounded-control border border-border text-text-secondary transition hover:bg-surface-muted focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring"
                title="Chat with Admin"
                aria-label="Chat with admin"
            >
                <x-tazen-v2.panel.icon name="chat" />
                <span class="chat-badge absolute -right-1 -top-1 hidden min-w-5 items-center justify-center rounded-full bg-brand-primary px-1 text-xs font-semibold leading-5 text-white" id="adminChatBadge">0</span>
            </a>

            <div class="relative" data-panel-dropdown>
                <button
                    type="button"
                    data-panel-dropdown-trigger
                    aria-expanded="false"
                    aria-label="Open professional profile menu"
                    class="flex min-h-11 items-center gap-2 rounded-control p-1.5 text-left hover:bg-surface-muted focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring sm:gap-3 sm:pl-2"
                >
                    <img src="{{ $imageUrl }}" alt="" class="size-9 rounded-full border border-border object-cover">
                    <span class="hidden min-w-0 sm:block">
                        <span class="block max-w-36 truncate text-sm font-semibold text-text-primary">{{ $professional->name }}</span>
                        <span class="block text-xs text-text-muted">Professional</span>
                    </span>
                    <i class="fas fa-chevron-down hidden text-xs text-text-muted sm:inline" aria-hidden="true"></i>
                </button>
                <div
                    data-panel-dropdown-panel
                    class="absolute right-0 top-full z-40 mt-2 hidden w-52 rounded-panel border border-border bg-surface p-2 shadow-popover"
                >
                    <a href="{{ route('professional.profile.index') }}" class="block rounded-control px-3 py-2 text-sm text-text-secondary hover:bg-surface-muted hover:text-text-primary">
                        Profile settings
                    </a>
                    <a href="{{ route('professional.logout') }}" class="block rounded-control px-3 py-2 text-sm font-medium text-error hover:bg-error-surface">
                        Sign out
                    </a>
                </div>
            </div>
        @endif
    </div>
</header>

<!-- Notification Modal -->
<div
    id="notificationModal"
    class="notification-modal fixed inset-0 z-50 hidden overflow-y-auto bg-brand-950/50 p-4"
    role="dialog"
    aria-modal="true"
    aria-hidden="true"
    aria-labelledby="professional-notifications-title"
>
    <div class="notification-modal-content mx-auto mt-8 max-h-[calc(100vh-4rem)] w-full max-w-2xl overflow-hidden rounded-panel border border-border bg-surface shadow-popover sm:mt-12">
        <div class="notification-modal-header flex items-center justify-between border-b border-divider px-5 py-4">
            <h2 id="professional-notifications-title" class="text-lg font-semibold text-brand-secondary">Notifications</h2>
            <button type="button" class="notification-modal-close inline-flex size-10 items-center justify-center rounded-control text-text-secondary hover:bg-surface-muted" aria-label="Close notifications">
                <x-tazen-v2.panel.icon name="close" />
            </button>
        </div>
        <div class="notification-modal-body max-h-[calc(100vh-10rem)] space-y-3 overflow-y-auto p-4 sm:p-5">
            {{-- All Notifications Sorted by Timestamp (Newest First) --}}
            @if($allNotifications && $allNotifications->count() > 0)
                @foreach($allNotifications as $notificationItem)
                    @if($notificationItem['type'] == 'service_notification')
                        @php
                            $notification = $notificationItem['data'];
                            $data = $notification->data;
                            $serviceId = $data['additional_service_id'] ?? null;
                            $serviceName = $data['service_name'] ?? 'Additional Service';
                            $type = $data['type'] ?? 'update';
                            $icon = $data['icon'] ?? 'fas fa-bell';
                            $color = $data['color'] ?? '#168a80';
                        @endphp
                        <div class="notification-item service-notification rounded-control border border-border bg-surface-muted p-4" data-notification-id="{{ $notification->id }}">
                            <div class="notification-icon-wrapper mb-2 text-brand-primary">
                                <i class="{{ $icon }}"></i>
                            </div>
                            <div class="notification-content text-sm text-text-secondary">
                                <h5 class="font-semibold text-text-primary">{{ $data['title'] ?? 'Service Update' }}</h5>
                                <p class="mt-1">{{ $data['message'] ?? 'You have a new update regarding an additional service.' }}</p>
                                <p class="mt-1 text-xs text-text-muted"><small>Service: {{ $serviceName }} - {{ $notification->created_at->diffForHumans() }}</small></p>
                                <div class="button-container mt-3 flex flex-wrap gap-2">
                                    @if($serviceId)
                                        <a href="{{ route('professional.additional-services.show', $serviceId) }}" class="notification-btn view-btn rounded-control bg-brand-primary px-3 py-2 text-xs font-semibold text-white hover:bg-brand-800">
                                            <i class="fas fa-eye"></i> View Details
                                        </a>
                                    @endif
                                    <button type="button" class="notification-btn mark-read-btn rounded-control border border-border px-3 py-2 text-xs font-semibold text-text-secondary hover:bg-surface" onclick="markProfessionalNotificationAsRead('{{ $notification->id }}')">
                                        <i class="fas fa-check"></i> Mark as Read
                                    </button>
                                </div>
                            </div>
                        </div>
                    @elseif($notificationItem['type'] == 'chat_notification')
                        @php
                            $chatGroup = $notificationItem['data'];
                            $booking = $chatGroup->booking ?? null;
                            if (!$booking) continue;
                            
                            $customerName = optional($booking->user)->name ?? 'Customer';
                            $serviceName = $booking->service_name ?? (optional($booking->service)->name ?? 'Service');
                        @endphp
                        <div class="notification-item chat-notification rounded-control border border-border bg-surface-muted p-4">
                            <div class="notification-icon-wrapper mb-2 text-brand-primary">
                                <i class="fas fa-message"></i>
                            </div>
                            <div class="notification-content text-sm text-text-secondary">
                                <h5 class="font-semibold text-text-primary">New Message from {{ $customerName }}</h5>
                                <p class="mt-1">{{ $chatGroup->unread_count }} unread message(s) for Booking #{{ $booking->id }}</p>
                                <p class="mt-1 text-xs text-text-muted"><small>Service: {{ $serviceName }} - {{ \Carbon\Carbon::parse($chatGroup->last_message_at)->diffForHumans() }}</small></p>
                                <div class="button-container mt-3 flex flex-wrap gap-2">
                                    <a href="{{ route('professional.chat.open', $booking->id) }}" class="notification-btn view-btn rounded-control bg-brand-primary px-3 py-2 text-xs font-semibold text-white hover:bg-brand-800">
                                        <i class="fas fa-comments"></i> Open Chat
                                    </a>
                                </div>
                            </div>
                        </div>
                    @elseif($notificationItem['type'] == 'reschedule_notification')
                        @php
                            $notification = $notificationItem['data'];
                            $data = json_decode($notification->data, true);
                        @endphp
                        <div class="notification-item reschedule rounded-control border border-border bg-surface-muted p-4">
                            <div class="notification-icon-wrapper mb-2 text-brand-primary">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="notification-content text-sm text-text-secondary">
                                <h5 class="font-semibold text-text-primary">Appointment Rescheduled</h5>
                                <p class="mt-1"><strong>{{ $data['customer_name'] ?? 'Customer' }}</strong> rescheduled their appointment</p>
                                <p class="mt-1 text-xs text-text-muted"><small>Service: {{ $data['service_name'] ?? 'N/A' }} - {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</small></p>
                                <div class="button-container mt-3 flex flex-wrap gap-2">
                                    <a href="{{ route('professional.booking.index') }}" class="notification-btn view-btn rounded-control bg-brand-primary px-3 py-2 text-xs font-semibold text-white hover:bg-brand-800">View Bookings</a>
                                    <button type="button" class="notification-btn mark-read-btn rounded-control border border-border px-3 py-2 text-xs font-semibold text-text-secondary hover:bg-surface" onclick="markProfessionalNotificationAsRead('{{ $notification->id }}')">Mark as Read</button>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            @endif
            
            {{-- Static Notifications (New Bookings & Profile Completion) --}}
            @if($newBookings > 0)
                <div class="notification-item new rounded-control border border-border bg-surface-muted p-4">
                    <div class="notification-icon-wrapper mb-2 text-brand-primary">
                        <i class="fas fa-calendar-plus"></i>
                    </div>
                    <div class="notification-content text-sm text-text-secondary">
                        <h5 class="font-semibold text-text-primary">New Booking Alert!</h5>
                        <p class="mt-1">You have {{ $newBookings }} new booking(s) in the last 24 hours.</p>
                        <div class="button-container mt-3 flex flex-wrap gap-2">
                            <a href="{{ route('professional.booking.index') }}" class="notification-btn view-btn rounded-control bg-brand-primary px-3 py-2 text-xs font-semibold text-white hover:bg-brand-800">View Bookings</a>
                        </div>
                    </div>
                </div>
            @endif
            
            @if(!$hasServices)
                <div class="notification-item warning rounded-control border border-warning/30 bg-warning-surface p-4 text-sm text-text-secondary">
                    <h5 class="font-semibold text-warning">Complete Your Profile</h5>
                    <p class="mt-1">Add your professional services to start accepting bookings.</p>
                    <div class="button-container mt-3 flex flex-wrap gap-2">
                        <a href="{{ route('professional.service.index') }}" class="notification-btn view-btn rounded-control bg-brand-primary px-3 py-2 text-xs font-semibold text-white hover:bg-brand-800">Add Services</a>
                    </div>
                </div>
            @endif
            
            @if(!$hasRates)
                <div class="notification-item warning rounded-control border border-warning/30 bg-warning-surface p-4 text-sm text-text-secondary">
                    <h5 class="font-semibold text-warning">Configure Your Rates</h5>
                    <p class="mt-1">Set up your pricing rates to start accepting bookings.</p>
                    <div class="button-container mt-3 flex flex-wrap gap-2">
                        <a href="{{ route('professional.rate.index') }}" class="notification-btn view-btn rounded-control bg-brand-primary px-3 py-2 text-xs font-semibold text-white hover:bg-brand-800">Configure Rates</a>
                    </div>
                </div>
            @endif
            
            @if(!$hasAvailability)
                <div class="notification-item warning rounded-control border border-warning/30 bg-warning-surface p-4 text-sm text-text-secondary">
                    <h5 class="font-semibold text-warning">Set Your Availability</h5>
                    <p class="mt-1">Define your availability schedule to start accepting bookings.</p>
                    <div class="button-container mt-3 flex flex-wrap gap-2">
                        <a href="{{ route('professional.availability.index') }}" class="notification-btn view-btn rounded-control bg-brand-primary px-3 py-2 text-xs font-semibold text-white hover:bg-brand-800">Set Availability</a>
                    </div>
                </div>
            @endif
            
            @if(!$hasRequestedServices)
                <div class="notification-item warning rounded-control border border-warning/30 bg-warning-surface p-4 text-sm text-text-secondary">
                    <h5 class="font-semibold text-warning">Add Other Information</h5>
                    <p class="mt-1">Complete your profile with additional service details and specializations.</p>
                    <div class="button-container mt-3 flex flex-wrap gap-2">
                        <a href="{{ route('professional.requested_services.index') }}" class="notification-btn view-btn rounded-control bg-brand-primary px-3 py-2 text-xs font-semibold text-white hover:bg-brand-800">Add Information</a>
                    </div>
                </div>
            @endif
            
            @if($totalNotifications == 0)
                <div class="notification-empty rounded-control bg-surface-muted px-4 py-10 text-center text-sm text-text-secondary">
                    <i class="fas fa-check-circle"></i>
                    <p>No notifications available</p>
                    <p>You're all caught up! No new notifications at this time.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
/**
 * Enhanced Header JavaScript for Professional Panel
 */
class ProfessionalHeaderManager {
    constructor() {
        this.header = document.querySelector('.header');
        this.searchBox = document.querySelector('.search-box');
        this.searchInput = document.querySelector('.search-box input');
        this.searchToggle = document.querySelector('.search-toggle');
        this.notificationIcon = document.getElementById('notificationIcon');
        this.notificationModal = document.getElementById('notificationModal');
        this.closeModalBtn = document.querySelector('.notification-modal-close');
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.handleScroll();
        this.initKeyboardShortcuts();
    }
    
    bindEvents() {
        // Mobile search toggle
        if (this.searchToggle) {
            this.searchToggle.addEventListener('click', () => {
                this.toggleMobileSearch();
            });
        }
        
        // Search input events
        if (this.searchInput) {
            this.searchInput.addEventListener('focus', () => {
                this.onSearchFocus();
            });
            
            this.searchInput.addEventListener('blur', () => {
                this.onSearchBlur();
            });
            
            this.searchInput.addEventListener('input', (e) => {
                this.onSearchInput(e.target.value);
            });
        }
        
        // Notification modal events
        if (this.notificationIcon) {
            this.notificationIcon.addEventListener('click', () => {
                this.openNotificationModal();
            });
        }
        
        if (this.closeModalBtn) {
            this.closeModalBtn.addEventListener('click', () => {
                this.closeNotificationModal();
            });
        }
        
        // Close modal when clicking outside
        if (this.notificationModal) {
            window.addEventListener('click', (event) => {
                if (event.target === this.notificationModal) {
                    this.closeNotificationModal();
                }
            });
        }
        
        // Scroll event for header effects
        window.addEventListener('scroll', () => {
            this.handleScroll();
        });
        
        // Resize event for responsive adjustments
        window.addEventListener('resize', () => {
            this.handleResize();
        });
        
        // Close mobile search on outside click
        document.addEventListener('click', (e) => {
            this.handleOutsideClick(e);
        });
    }
    
    toggleMobileSearch() {
        if (this.searchBox) {
            const isVisible = this.searchBox.classList.contains('mobile-visible');
            
            if (!isVisible) {
                this.searchBox.classList.add('mobile-visible');
                this.searchToggle.innerHTML = '<i class="fas fa-times"></i>';
                this.searchToggle.style.background = '#e74c3c';
                this.searchToggle.style.color = 'white';
                
                setTimeout(() => {
                    this.searchInput?.focus();
                }, 300);
            } else {
                this.searchBox.classList.remove('mobile-visible');
                this.searchToggle.innerHTML = '<i class="fas fa-search"></i>';
                this.searchToggle.style.background = '';
                this.searchToggle.style.color = '';
            }
        }
    }
    
    onSearchFocus() {
        this.header?.classList.add('search-focused');
    }
    
    onSearchBlur() {
        setTimeout(() => {
            this.header?.classList.remove('search-focused');
        }, 200);
    }
    
    onSearchInput(value) {
        if (value.length > 0) {
            this.header?.classList.add('search-active');
        } else {
            this.header?.classList.remove('search-active');
        }
    }
    
    openNotificationModal() {
        if (this.notificationModal) {
            this.notificationModal.style.display = 'block';
            this.notificationModal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            this.closeModalBtn?.focus();
        }
    }
    
    closeNotificationModal() {
        if (this.notificationModal) {
            this.notificationModal.style.display = 'none';
            this.notificationModal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = 'auto';
            this.notificationIcon?.focus();
        }
    }
    
    handleScroll() {
        const scrollY = window.scrollY;
        
        if (scrollY > 10) {
            this.header?.classList.add('scrolled');
        } else {
            this.header?.classList.remove('scrolled');
        }
        
        // Hide mobile search when scrolling
        if (scrollY > 50 && this.searchBox?.classList.contains('mobile-visible')) {
            this.toggleMobileSearch();
        }
    }
    
    handleResize() {
        const width = window.innerWidth;
        
        // Hide mobile search on resize to desktop
        if (width > 768 && this.searchBox?.classList.contains('mobile-visible')) {
            this.searchBox.classList.remove('mobile-visible');
            this.searchToggle.innerHTML = '<i class="fas fa-search"></i>';
            this.searchToggle.style.background = '';
            this.searchToggle.style.color = '';
        }
    }
    
    handleOutsideClick(e) {
        // Close mobile search if clicking outside
        if (this.searchBox?.classList.contains('mobile-visible') && 
            !this.searchBox.contains(e.target) && 
            !this.searchToggle?.contains(e.target)) {
            this.toggleMobileSearch();
        }
    }
    
    initKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            if (this.notificationModal?.style.display === 'block') {
                if (e.key === 'Escape') {
                    this.closeNotificationModal();
                    return;
                }

                if (e.key === 'Tab') {
                    const focusable = Array.from(this.notificationModal.querySelectorAll(
                        'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
                    )).filter((element) => element.offsetParent !== null);
                    const first = focusable[0];
                    const last = focusable[focusable.length - 1];

                    if (e.shiftKey && document.activeElement === first) {
                        e.preventDefault();
                        last?.focus();
                    } else if (!e.shiftKey && document.activeElement === last) {
                        e.preventDefault();
                        first?.focus();
                    }
                }
            }
        });
    }
}

// Function to mark professional notification as read
function markProfessionalNotificationAsRead(notificationId) {
    fetch('/professional/notifications/' + notificationId + '/mark-as-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            notification_id: notificationId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the notification from the modal
            const notificationItem = document.querySelector(`[data-notification-id="${notificationId}"]`);
            if (notificationItem) {
                notificationItem.style.opacity = '0.5';
                notificationItem.style.pointerEvents = 'none';
                
                // Fade out and remove
                setTimeout(() => {
                    notificationItem.remove();
                    updateProfessionalNotificationBadge();
                }, 300);
            }
        } else {
            console.error('Failed to mark notification as read');
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
    });
}

// Function to update professional notification badge count
function updateProfessionalNotificationBadge() {
    const remainingNotifications = document.querySelectorAll('.notification-item:not([style*="opacity: 0.5"])').length;
    const badge = document.querySelector('.notification-badge');
    
    if (remainingNotifications === 0) {
        if (badge) badge.style.display = 'none';
        
        // Show empty state if modal is open
        const modalBody = document.querySelector('.notification-modal-body');
        if (modalBody && modalBody.querySelector('.notification-empty') === null) {
            modalBody.innerHTML = `
                <div class="notification-empty">
                    <i class="fas fa-check-circle"></i>
                    <p>No notifications available</p>
                    <p>You're all caught up! No new notifications at this time.</p>
                </div>
            `;
        }
    } else {
        if (badge) {
            badge.textContent = remainingNotifications;
            badge.style.display = 'inline-block';
        }
    }
}

// Admin Chat Manager
class AdminChatManager {
    constructor() {
        this.chatIcon = document.getElementById('adminChatIcon');
        this.chatBadge = document.getElementById('adminChatBadge');
        this.init();
    }
    
    init() {
        this.checkUnreadMessages();
        // Check for unread messages every 30 seconds
        setInterval(() => {
            this.checkUnreadMessages();
        }, 30000);
    }
    
    checkUnreadMessages() {
        fetch('{{ route("professional.admin-chat.unread-count") }}', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.updateChatBadge(data.unread_count);
            }
        })
        .catch(error => {
            console.error('Error checking unread messages:', error);
        });
    }
    
    updateChatBadge(count) {
        if (count > 0) {
            this.chatBadge.textContent = count > 99 ? '99+' : count;
            this.chatBadge.style.display = 'flex';
            this.chatIcon.classList.add('has-unread');
        } else {
            this.chatBadge.style.display = 'none';
            this.chatIcon.classList.remove('has-unread');
        }
    }
}

// Initialize header manager when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new ProfessionalHeaderManager();
    new AdminChatManager();
});
</script>
