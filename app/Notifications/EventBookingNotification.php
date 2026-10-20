<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\EventBooking;
use App\Models\User;
use App\Models\AllEvent;

class EventBookingNotification extends Notification
{
    use Queueable;

    protected $booking;
    protected $user;
    protected $event;

    /**
     * Create a new notification instance.
     */
    public function __construct(EventBooking $booking, ?User $user = null, ?AllEvent $event = null)
    {
        $this->booking = $booking;
        $this->user = $user;
        $this->event = $event;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'event_booking',
            'booking_id' => $this->booking->id,
            'event_id' => $this->booking->event_id,
            'event_name' => $this->booking->event_name ?? optional($this->event)->heading,
            'event_date' => $this->booking->event_date,
            'location' => $this->booking->location,
            'event_type' => $this->booking->type,
            'persons' => $this->booking->persons,
            'phone' => $this->booking->phone,
            'total_price' => $this->booking->total_price,
            'payment_status' => $this->booking->payment_status,
            'customer_name' => optional($this->user)->name,
            'customer_email' => optional($this->user)->email,
            'created_at' => $this->booking->created_at,
        ];
    }
}
