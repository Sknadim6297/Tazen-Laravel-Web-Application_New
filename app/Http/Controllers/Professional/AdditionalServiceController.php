<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use App\Models\AdditionalService;
use App\Models\Booking;
use App\Models\Professional;
use App\Models\User;
use App\Models\Admin;
use App\Notifications\AdditionalServiceNotification;
use App\Traits\AdditionalServiceNotificationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class AdditionalServiceController extends Controller
{
    use AdditionalServiceNotificationTrait;
    /**
     * Display a listing of additional services for the professional
     */
    public function index(Request $request)
    {
        $filterRules = [
            'search' => ['nullable', 'string', 'max:255'],
            'customer_id' => ['nullable', 'integer'],
            'payment_status' => ['nullable', 'in:pending,paid,failed'],
            'service_status' => ['nullable', 'in:completed,in_progress,ready_to_start,awaiting_delivery_date,awaiting_payment,rejected,under_negotiation,price_updated,pending_review,awaiting_admin,pending'],
            'date_range' => ['nullable', 'in:today,week,month,custom'],
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
        ];
        $filterValidator = \Illuminate\Support\Facades\Validator::make($request->query(), $filterRules);
        $filters = $request->only(array_keys($filterRules));
        foreach ($filterValidator->errors()->keys() as $invalidField) {
            unset($filters[$invalidField]);
        }
        $filterErrors = $filterValidator->errors()->all();

        $professionalId = Auth::guard('professional')->id();
        $servicesQuery = AdditionalService::with(['user', 'booking'])
            ->forProfessional($professionalId);

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $servicesQuery->where(function ($query) use ($search) {
                $query->where('service_name', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%")
                    ->orWhere('booking_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if (!empty($filters['customer_id'])) {
            $servicesQuery->where('user_id', $filters['customer_id']);
        }

        if (!empty($filters['payment_status'])) {
            $servicesQuery->where('payment_status', $filters['payment_status']);
        }

        if (!empty($filters['service_status'])) {
            $this->applyServiceStatusFilter($servicesQuery, $filters['service_status']);
        }

        if (!empty($filters['date_range'])) {
            $now = now();

            if ($filters['date_range'] === 'custom' && !empty($filters['from_date']) && !empty($filters['to_date'])) {
                $servicesQuery
                    ->whereDate('created_at', '>=', $filters['from_date'])
                    ->whereDate('created_at', '<=', $filters['to_date']);
            } elseif ($filters['date_range'] !== 'custom') {
                $startDate = match ($filters['date_range']) {
                    'today' => $now->copy()->startOfDay(),
                    'week' => $now->copy()->subDays(7)->startOfDay(),
                    'month' => $now->copy()->subMonth()->startOfDay(),
                };

                $servicesQuery->where('created_at', '>=', $startDate);
            }
        }

        $additionalServices = $servicesQuery
            ->latest()
            ->paginate(10);
        $additionalServices->appends($filters);

        $customers = \App\Models\User::query()
            ->whereIn('id', AdditionalService::forProfessional($professionalId)
                ->select('user_id')
                ->distinct())
            ->orderBy('name')
            ->get(['id', 'name']);
        $hasServices = AdditionalService::forProfessional($professionalId)->exists();

        return view('professional.additional-services.index', compact(
            'additionalServices',
            'customers',
            'filters',
            'filterErrors',
            'hasServices'
        ));
    }

    /**
     * Apply the same service-status definitions used by the listing badges.
     */
    private function applyServiceStatusFilter($query, string $status): void
    {
        $query->where(function ($statusQuery) use ($status) {
            switch ($status) {
                case 'completed':
                    $statusQuery->where('consulting_status', 'done')
                        ->whereNotNull('customer_confirmed_at');
                    break;
                case 'in_progress':
                    $statusQuery->where('consulting_status', 'in_progress');
                    break;
                case 'ready_to_start':
                    $statusQuery->where('admin_status', 'approved')
                        ->where('payment_status', 'paid')
                        ->where('delivery_date_set', true);
                    break;
                case 'awaiting_delivery_date':
                    $statusQuery->where('admin_status', 'approved')
                        ->where('payment_status', 'paid')
                        ->where(function ($query) {
                            $query->where('delivery_date_set', false)
                                ->orWhereNull('delivery_date_set');
                        });
                    break;
                case 'awaiting_payment':
                    $statusQuery->where('admin_status', 'approved')
                        ->where(function ($query) {
                            $query->where('payment_status', '!=', 'paid')
                                ->orWhereNull('payment_status');
                        });
                    break;
                case 'rejected':
                    $statusQuery->where('admin_status', 'rejected');
                    break;
                case 'under_negotiation':
                    $statusQuery->where('admin_status', 'pending')
                        ->where('negotiation_status', 'user_negotiated');
                    break;
                case 'price_updated':
                    $statusQuery->where('admin_status', 'pending')
                        ->where('negotiation_status', 'admin_responded');
                    break;
                case 'pending_review':
                    $statusQuery->where('admin_status', 'pending')
                        ->whereNotIn('negotiation_status', ['user_negotiated', 'admin_responded'])
                        ->where('professional_status', 'pending');
                    break;
                case 'awaiting_admin':
                    $statusQuery->where('admin_status', 'pending')
                        ->whereNotIn('negotiation_status', ['user_negotiated', 'admin_responded'])
                        ->where('professional_status', 'approved');
                    break;
                case 'pending':
                    $statusQuery->where('admin_status', 'pending')
                        ->whereNotIn('negotiation_status', ['user_negotiated', 'admin_responded'])
                        ->where(function ($query) {
                            $query->whereNull('professional_status')
                                ->orWhereNotIn('professional_status', ['pending', 'approved']);
                        });
                    break;
            }
        });
    }

    /**
     * Show the form for creating a new additional service
     */
    public function create(Request $request)
    {
        $bookingId = $request->get('booking_id');
        $booking = null;
        
        if ($bookingId) {
            $booking = Booking::with('user')
                ->where('id', $bookingId)
                ->where('professional_id', Auth::guard('professional')->id())
                ->where('status', '!=', 'cancelled')
                ->first();
            
            if (!$booking) {
                return redirect()->route('professional.additional-services.index')
                    ->with('error', 'Booking not found or you do not have permission to access it.');
            }
        }

        // Get all bookings for this professional for dropdown
        $bookings = Booking::with('user')
            ->where('professional_id', Auth::guard('professional')->id())
            ->where('status', '!=', 'cancelled')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('professional.additional-services.create', compact('booking', 'bookings'));
    }

    /**
     * Store a newly created additional service
     */
    public function store(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'service_name' => 'required|string|max:255',
            'reason' => 'required|string',
            'base_price' => 'required|numeric|min:0',
        ]);

        // Verify booking belongs to this professional
        $booking = Booking::where('id', $request->booking_id)
            ->where('professional_id', Auth::guard('professional')->id())
            ->where('status', '!=', 'cancelled')
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found or you do not have permission to access it.'
            ], 403);
        }

        try {
            DB::beginTransaction();

            $additionalService = new AdditionalService();
            $additionalService->professional_id = Auth::guard('professional')->id();
            $additionalService->user_id = $booking->user_id;
            $additionalService->booking_id = $request->booking_id;
            $additionalService->service_name = $request->service_name;
            $additionalService->reason = $request->reason;
            $additionalService->base_price = $request->base_price;
            $additionalService->original_professional_price = $request->base_price; // Store original price
            
            // Calculate GST and total price
            $basePrice = $request->base_price;
            $cgst = $basePrice * 0.09; // 9% CGST
            $sgst = $basePrice * 0.09; // 9% SGST
            $totalPrice = $basePrice + $cgst + $sgst; // Base + 18% GST
            
            $additionalService->cgst = $cgst;
            $additionalService->sgst = $sgst;
            $additionalService->igst = 0.00; // Usually 0 for intrastate
            $additionalService->total_price = $totalPrice;
            
            $additionalService->save();

            // Send enhanced notifications using the trait
            $this->sendNotificationWithLogging(
                $additionalService, 
                'new_service',
                "Professional {$additionalService->professional->name} has created a new additional service '{$additionalService->service_name}' for booking #{$additionalService->booking_id}. Amount: ₹" . number_format($additionalService->total_price, 2),
                [
                    'action' => 'service_created',
                    'booking_id' => $booking->id,
                    'base_price' => $additionalService->base_price,
                    'total_price' => $additionalService->total_price,
                    'cgst' => $additionalService->cgst,
                    'sgst' => $additionalService->sgst,
                ]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Additional service created successfully and notifications sent to all parties.',
                'redirect' => route('professional.additional-services.index')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the additional service: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show details of a specific additional service
     */
    public function show($id)
    {
        $additionalService = AdditionalService::with(['user', 'booking'])
            ->where('id', $id)
            ->where('professional_id', Auth::guard('professional')->id())
            ->firstOrFail();

        return view('professional.additional-services.show', compact('additionalService'));
    }

    /**
     * Mark consultation as completed
     */
    public function markConsultationCompleted(Request $request, $id)
    {
        $additionalService = AdditionalService::where('id', $id)
            ->where('professional_id', Auth::guard('professional')->id())
            ->firstOrFail();

        if (!$additionalService->canBeCompletedByProfessional()) {
            return response()->json([
                'success' => false,
                'message' => 'Consultation cannot be marked as completed at this time.'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $additionalService->update([
                'consulting_status' => 'done',
                'professional_completed_at' => now()
            ]);

            // Notify user
            $user = $additionalService->user;
            $user->notify(new AdditionalServiceNotification(
                $additionalService, 
                'consultation_completed'
            ));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Consultation marked as completed. User has been notified.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Set delivery date for the service
     */


    /**
     * Get bookings for AJAX dropdown
     */
    public function getBookings(Request $request)
    {
        $bookings = Booking::with('user')
            ->where('professional_id', Auth::guard('professional')->id())
            ->where('status', '!=', 'cancelled')
            ->when($request->search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('customer_name', 'like', "%{$search}%")
                      ->orWhere('service_name', 'like', "%{$search}%")
                      ->orWhere('id', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'text' => "#{$booking->id} - {$booking->customer_name} - {$booking->service_name}",
                    'customer_name' => $booking->customer_name,
                    'service_name' => $booking->service_name,
                    'booking_date' => $booking->booking_date,
                ];
            });

        return response()->json($bookings);
    }

    /**
     * Set delivery date for additional service
     */
    public function setDeliveryDate(Request $request, $id)
    {
        $request->validate([
            'delivery_date' => 'required|date|after:today',
            'delivery_reason' => 'required|string|max:1000',
        ]);

        $additionalService = AdditionalService::where('id', $id)
            ->where('professional_id', Auth::guard('professional')->id())
            ->firstOrFail();

        if (!$additionalService->canSetDeliveryDate()) {
            return response()->json([
                'success' => false,
                'message' => 'Delivery date cannot be set at this time.'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $additionalService->update([
                'delivery_date' => $request->delivery_date,
                'delivery_date_reason' => $request->delivery_reason,
                'delivery_date_set_by_professional_at' => now(),
                'delivery_date_set' => true,
                'can_complete_consultation' => false, // Will be updated when date passes
            ]);

            // Send enhanced notification
            $this->sendNotificationWithLogging(
                $additionalService,
                'delivery_date_set',
                "Professional has set delivery date to " . \Carbon\Carbon::parse($request->delivery_date)->format('M d, Y') . " for '{$additionalService->service_name}'",
                [
                    'action' => 'delivery_date_set',
                    'delivery_date' => $request->delivery_date,
                    'set_by' => 'professional',
                    'professional_name' => $additionalService->professional->name,
                ]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Delivery date set successfully. All parties have been notified.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update existing delivery date
     */
    public function updateDeliveryDate(Request $request, $id)
    {
        $request->validate([
            'delivery_date' => 'required|date|after:today',
            'delivery_notes' => 'required|string|max:1000',
        ]);

        $additionalService = AdditionalService::where('id', $id)
            ->where('professional_id', Auth::guard('professional')->id())
            ->firstOrFail();

        if (!$additionalService->delivery_date) {
            return response()->json([
                'success' => false,
                'message' => 'No delivery date has been set yet. Please set delivery date first.'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $additionalService->update([
                'delivery_date' => $request->delivery_date,
                'delivery_date_reason' => $request->delivery_notes,
                'delivery_date_modified_by_admin_at' => null, // Reset admin modification
                'can_complete_consultation' => false, // Will be updated when date passes
            ]);

            // Notify admin and user
            $admins = Admin::all();
            $user = $additionalService->user;

            foreach ($admins as $admin) {
                $admin->notify(new AdditionalServiceNotification(
                    $additionalService, 
                    'delivery_date_changed'
                ));
            }

            $user->notify(new AdditionalServiceNotification(
                $additionalService, 
                'delivery_date_changed'
            ));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Delivery date updated successfully. Admin and customer have been notified.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark consultation as completed
     */
    public function completeConsultation($id)
    {
        $additionalService = AdditionalService::where('id', $id)
            ->where('professional_id', Auth::guard('professional')->id())
            ->firstOrFail();

        if (!$additionalService->canCompleteConsultation()) {
            return response()->json([
                'success' => false,
                'message' => 'Consultation cannot be completed yet. Please wait until the delivery date has passed.'
            ], 400);
        }

        if ($additionalService->consulting_status === 'done') {
            return response()->json([
                'success' => false,
                'message' => 'Consultation has already been marked as completed.'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $additionalService->update([
                'consulting_status' => 'done',
                'professional_completed_at' => now(),
            ]);

            // Notify admin and user
            $admins = Admin::all();
            $user = $additionalService->user;

            foreach ($admins as $admin) {
                $admin->notify(new AdditionalServiceNotification(
                    $additionalService, 
                    'consultation_completed'
                ));
            }

            $user->notify(new AdditionalServiceNotification(
                $additionalService, 
                'consultation_completed'
            ));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Consultation marked as completed. Customer will be notified to confirm.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Respond to customer negotiation
     */
    public function respondToNegotiation(Request $request, $id)
    {
        $request->validate([
            'professional_final_price' => 'required|numeric|min:1',
            'professional_response' => 'required|string|max:1000',
            'min_price' => 'required|numeric|min:0'
        ]);

        $additionalService = AdditionalService::where('id', $id)
            ->where('professional_id', Auth::guard('professional')->id())
            ->firstOrFail();

        if ($additionalService->negotiation_status !== 'user_negotiated') {
            return response()->json([
                'success' => false,
                'message' => 'No active negotiation found for this service.'
            ], 400);
        }

        // Validate minimum price
        if ($request->professional_final_price < $request->min_price) {
            return response()->json([
                'success' => false,
                'message' => "Price cannot be below the allowed minimum"
            ], 422);
        }

        try {
            DB::beginTransaction();

            $additionalService->update([
                'admin_final_negotiated_price' => $request->professional_final_price,
                'admin_negotiation_response' => $request->professional_response,
                'negotiation_status' => 'admin_responded',
                'admin_reviewed_at' => now(),
                // Update the total price to reflect the negotiated amount
                'modified_total_price' => $request->professional_final_price,
                'price_modified_by_admin' => true,
                'price_modification_reason' => 'Professional responded to customer negotiation'
            ]);

            // Notify user
            $user = $additionalService->user;
            $message = "Professional has responded to your negotiation. Response: {$request->professional_response}";

            $user->notify(new AdditionalServiceNotification(
                $additionalService, 
                'negotiation_responded',
                $message
            ));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Negotiation response sent successfully to customer.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update service price directly (professional initiated)
     */
    public function updatePrice(Request $request, $id)
    {
        $request->validate([
            'new_price' => 'required|numeric|min:1',
            'reason' => 'required|string|max:1000',
            'min_price' => 'required|numeric|min:0'
        ]);

        $additionalService = AdditionalService::where('id', $id)
            ->where('professional_id', Auth::guard('professional')->id())
            ->firstOrFail();

        // Validate minimum price
        if ($request->new_price < $request->min_price) {
            return response()->json([
                'success' => false,
                'message' => "Price cannot be below the allowed minimum"
            ], 422);
        }

        try {
            DB::beginTransaction();

            $additionalService->update([
                'modified_total_price' => $request->new_price,
                'price_modified_by_admin' => true,
                'price_modification_reason' => $request->reason,
                'admin_reviewed_at' => now()
            ]);

            // Notify user
            $user = $additionalService->user;
            $message = "Service price has been updated by professional. New price: ₹{$request->new_price}. Reason: {$request->reason}";

            $user->notify(new AdditionalServiceNotification(
                $additionalService, 
                'price_updated',
                $message
            ));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Service price updated successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate invoice for a completed additional service
     */
    public function generateInvoice($id)
    {
        $service = AdditionalService::with(['user', 'professional', 'booking'])
            ->where('id', $id)
            ->where('professional_id', Auth::guard('professional')->id())
            ->where('consulting_status', 'done')
            ->where('payment_status', 'paid')
            ->first();

        if (!$service) {
            return redirect()->back()->with('error', 'Service not found or not eligible for invoice generation.');
        }

        // Prepare invoice data
        $invoiceNumber = 'INV-AS-' . str_pad($service->id, 6, '0', STR_PAD_LEFT) . '-' . date('Y');
        $invoiceDate = now()->format('d M, Y');
        
        // Get pricing details
        $pricing = [
            'base_price' => $service->getEffectiveBasePrice(),
            'cgst' => $service->cgst ?? 0,
            'sgst' => $service->sgst ?? 0,
            'igst' => $service->igst ?? 0,
            'total_price' => $service->getEffectiveTotalPrice()
        ];

        // Negotiation details
        $negotiationDetails = null;
        if ($service->negotiation_status !== 'none') {
            $negotiationDetails = [
                'negotiation_status' => $service->negotiation_status,
                'original_price' => $service->base_price,
                'user_negotiated_price' => $service->user_negotiated_price,
                'admin_final_price' => $service->admin_final_negotiated_price
            ];
        }

        return view('professional.additional-services.invoice', [
            'service' => $service,
            'customer' => $service->user,
            'professional' => $service->professional,
            'invoice_number' => $invoiceNumber,
            'invoice_date' => $invoiceDate,
            'pricing' => $pricing,
            'negotiation_details' => $negotiationDetails
        ]);
    }

    /**
     * Generate PDF invoice for a completed additional service
     */
    public function generatePdfInvoice($id)
    {
        $service = AdditionalService::with(['user', 'professional', 'booking'])
            ->where('id', $id)
            ->where('professional_id', Auth::guard('professional')->id())
            ->where('consulting_status', 'done')
            ->where('payment_status', 'paid')
            ->first();

        if (!$service) {
            return redirect()->back()->with('error', 'Service not found or not eligible for invoice generation.');
        }

        // Prepare invoice data
        $invoiceNumber = 'INV-AS-' . str_pad($service->id, 6, '0', STR_PAD_LEFT) . '-' . date('Y');
        $invoiceDate = now()->format('d M, Y');
        
        // Get pricing details
        $pricing = [
            'base_price' => $service->getEffectiveBasePrice(),
            'cgst' => $service->cgst ?? 0,
            'sgst' => $service->sgst ?? 0,
            'igst' => $service->igst ?? 0,
            'total_price' => $service->getEffectiveTotalPrice()
        ];

        // Negotiation details
        $negotiationDetails = null;
        if ($service->negotiation_status !== 'none') {
            $negotiationDetails = [
                'negotiation_status' => $service->negotiation_status,
                'original_price' => $service->base_price,
                'user_negotiated_price' => $service->user_negotiated_price,
                'admin_final_price' => $service->admin_final_negotiated_price
            ];
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('professional.additional-services.invoice-pdf', [
            'service' => $service,
            'customer' => $service->user,
            'professional' => $service->professional,
            'invoice_number' => $invoiceNumber,
            'invoice_date' => $invoiceDate,
            'pricing' => $pricing,
            'negotiation_details' => $negotiationDetails
        ]);

        return $pdf->download('invoice-' . $invoiceNumber . '.pdf');
    }

    /**
     * Mark a notification as read for professional
     */
    public function markNotificationAsRead(Request $request, $notificationId)
    {
        try {
            $professional = Auth::guard('professional')->user();
            
            // Find the notification for this professional
            $notification = $professional->notifications()->where('id', $notificationId)->first();
            
            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }
            
            // Mark as read
            $notification->markAsRead();
            
            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while marking notification as read'
            ], 500);
        }
    }
}