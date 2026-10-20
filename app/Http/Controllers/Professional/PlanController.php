<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\ProfessionalPlanPurchase;
use App\Models\Admin;
use App\Notifications\PlanPurchaseNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

    class PlanController extends Controller
    {
        public function index()
        {
            $professionalId = Auth::id();
            $allPlans = Plan::active()->orderBy('order')->get();
            
            // Get the currently active plan (not expired, successful payment)
            $currentPlan = ProfessionalPlanPurchase::getActivePlanForProfessional($professionalId);
            
            // Get all purchased plans (successful payment, regardless of expiry)
            $allPurchasedPlans = ProfessionalPlanPurchase::where('professional_id', $professionalId)
                ->where('payment_status', 'success')
                ->get();

            $pendingRequests = ProfessionalPlanPurchase::where('professional_id', $professionalId)
                ->where('payment_status', 'pending')
                ->orderBy('created_at', 'desc')
                ->get();

            $subscriptionHistory = ProfessionalPlanPurchase::where('professional_id', $professionalId)
                ->orderBy('created_at', 'desc')
                ->get();
            
            // Get the plan with the highest display order from all purchases
            $highestPlanOrder = 0;
            if ($allPurchasedPlans->count() > 0) {
                $highestPlanOrder = $allPurchasedPlans
                    ->map(function($purchase) use ($allPlans) {
                        // Find the plan object to get its order
                        $plan = $allPlans->firstWhere('name', $purchase->plan_name);
                        return $plan ? $plan->order : 0;
                    })
                    ->max();
            }
            
            // CASE 3: User has an expired plan (show all plans)
            if ($allPurchasedPlans->count() > 0 && !$currentPlan) {
                // User has purchased plans but none are currently active (all expired)
                $plans = $allPlans; // Show all plans
                $hasExpiredPlan = true;
            }
            // CASE 1: User has an active plan (show plans with higher order)
            elseif ($currentPlan) {
                $currentPlanOrder = $allPlans->firstWhere('name', $currentPlan->plan_name)?->order ?? 0;
                $plans = $allPlans->filter(function($plan) use ($currentPlanOrder) {
                    return $plan->order > $currentPlanOrder;
                });
                $hasExpiredPlan = false;
            }
            // CASE 2: User has multiple purchased plans (show plans with higher order than max)
            elseif ($highestPlanOrder > 0) {
                $plans = $allPlans->filter(function($plan) use ($highestPlanOrder) {
                    return $plan->order > $highestPlanOrder;
                });
                $hasExpiredPlan = false;
            }
            // No plans purchased - show all
            else {
                $plans = $allPlans;
                $hasExpiredPlan = false;
            }

            return view('professional.plans.index', compact(
                'plans', 
                'currentPlan', 
                'hasExpiredPlan',
                'allPurchasedPlans',
                'pendingRequests',
                'subscriptionHistory'
            ));
        }

        /**
         * Show purchase confirmation page
         */
        public function purchase($id)
        {
            $plan = Plan::active()->findOrFail($id);
            $professional = Auth::user();

            return view('professional.plans.purchase', compact('plan', 'professional'));
        }

        /**
         * Process the plan purchase
         */
        public function processPurchase(Request $request, $id)
        {
            try {
                $plan = Plan::active()->findOrFail($id);
                $professional = Auth::user();

                $validator = \Validator::make($request->all(), [
                    'payment_method' => 'required|in:razorpay,stripe,manual',
                    'payment_screenshot' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
                ]);

                if ($validator->fails()) {
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'errors' => $validator->errors()
                        ], 422);
                    }
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                // Calculate end date if validity is set
                $startDate = Carbon::now();
                $endDate = null;
                if ($plan->validity_days) {
                    $endDate = Carbon::now()->addDays($plan->validity_days);
                }

            // Handle screenshot upload for manual payment
            $screenshotPath = null;
            if ($request->payment_method === 'manual' && $request->hasFile('payment_screenshot')) {
                $screenshotPath = $request->file('payment_screenshot')->store('payment_screenshots', 'public');
            }

            if ($request->payment_method === 'manual') {
                $startDate = null;
                $endDate = null;
            }

            // Create purchase record
            $purchase = ProfessionalPlanPurchase::create([
                'professional_id' => $professional->id,
                'plan_id' => $plan->id,
                'plan_name' => $plan->name,
                'price' => $plan->price,
                'features' => $plan->features,
                'lead_limit' => $plan->lead_limit,
                'leads_used' => 0,
                'payment_status' => 'pending',
                'payment_method' => $request->payment_method,
                'payment_screenshot' => $screenshotPath,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            // Notify all admins about the purchase
            $admins = Admin::all();
            foreach ($admins as $admin) {
                $admin->notify(new PlanPurchaseNotification($purchase));
            }

            // Redirect to payment gateway based on method
            if ($request->payment_method === 'razorpay') {
                return $this->initiateRazorpayPayment($purchase);
            } elseif ($request->payment_method === 'stripe') {
                return $this->initiateStripePayment($purchase);
            } else {
                // Manual payment - wait for admin approval
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'redirect' => route('professional.plans.success', $purchase->id),
                        'message' => 'Your purchase request has been submitted. Please wait for admin approval.'
                    ]);
                }
                
                return redirect()->route('professional.plans.success', $purchase->id)
                    ->with('info', 'Your purchase request has been submitted. Please wait for admin approval.');
            }
            } catch (\Exception $e) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'An error occurred while processing your purchase. Please try again.'
                    ], 500);
                }
                return redirect()->back()->with('error', 'An error occurred while processing your purchase. Please try again.');
            }
        }

        /**
         * Initiate Razorpay payment
         */
        private function initiateRazorpayPayment($purchase)
        {
            try {
                // Check Razorpay credentials
                $razorpayKey = config('services.razorpay.key');
                $razorpaySecret = config('services.razorpay.secret');
                
                if (!$razorpayKey || !$razorpaySecret) {
                    return redirect()->back()->with('error', 'Payment gateway configuration error. Please contact support.');
                }

                // Razorpay integration
                $api = new \Razorpay\Api\Api($razorpayKey, $razorpaySecret);

                $orderData = [
                    'receipt' => 'plan_' . $purchase->id . '_' . time(),
                    'amount' => $purchase->price * 100, // Amount in paise
                    'currency' => 'INR',
                    'notes' => [
                        'purchase_id' => $purchase->id,
                        'plan_name' => $purchase->plan_name,
                        'professional_id' => $purchase->professional_id,
                    ]
                ];

                $razorpayOrder = $api->order->create($orderData);
                
                // Razorpay order created successfully (logging removed)
                
                return view('professional.plans.razorpay', [
                    'purchase' => $purchase,
                    'order' => $razorpayOrder,
                    'razorpayKey' => $razorpayKey,
                ]);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Payment initialization failed: ' . $e->getMessage());
            }
        }

        /**
         * Initiate Stripe payment
         */
        private function initiateStripePayment($purchase)
        {
            // Stripe integration - to be implemented
            return redirect()->back()->with('info', 'Stripe payment coming soon!');
        }

        /**
         * Handle Razorpay payment callback
         */
        public function razorpayCallback(Request $request)
        {
            try {
                $validator = \Validator::make($request->all(), [
                    'razorpay_payment_id' => 'required',
                    'razorpay_order_id' => 'required',
                    'razorpay_signature' => 'required',
                    'purchase_id' => 'required|exists:professional_plan_purchases,id',
                ]);

                if ($validator->fails()) {
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Validation failed',
                            'errors' => $validator->errors()
                        ], 422);
                    }
                    return redirect()->back()->withErrors($validator);
                }

                $purchase = ProfessionalPlanPurchase::findOrFail($request->purchase_id);

                // Verify Razorpay signature
                $api = new \Razorpay\Api\Api(config('services.razorpay.key'), config('services.razorpay.secret'));

                $attributes = [
                    'razorpay_order_id' => $request->razorpay_order_id,
                    'razorpay_payment_id' => $request->razorpay_payment_id,
                    'razorpay_signature' => $request->razorpay_signature
                ];

                $api->utility->verifyPaymentSignature($attributes);

                // Payment successful
                $purchase->update([
                    'payment_status' => 'success',
                    'payment_id' => $request->razorpay_payment_id,
                ]);

                // Expire any existing active plans for this professional now that payment is successful
                ProfessionalPlanPurchase::where('professional_id', $purchase->professional_id)
                    ->where('id', '!=', $purchase->id)
                    ->where('payment_status', 'success')
                    ->where(function($q) {
                        $q->whereNull('end_date')
                        ->orWhere('end_date', '>', Carbon::now());
                    })
                    ->update(['end_date' => Carbon::now()]);

                if (!$purchase->start_date) {
                    $purchase->start_date = Carbon::now();
                }

                if (!$purchase->end_date && $purchase->plan && $purchase->plan->validity_days) {
                    $purchase->end_date = Carbon::now()->addDays($purchase->plan->validity_days);
                }

                $purchase->save();

                // Razorpay payment verified successfully (logging removed)

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'status' => 'success',
                        'message' => 'Payment successful! Your plan is now active.',
                        'redirect' => route('professional.plans.success', $purchase->id)
                    ]);
                }

                return redirect()->route('professional.plans.success', $purchase->id)
                    ->with('success', 'Payment successful! Your plan is now active.');

            } catch (\Razorpay\Api\Errors\SignatureVerificationError $e) {
                if (isset($purchase)) {
                    $purchase->update(['payment_status' => 'failed']);
                }

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Payment verification failed. Please contact support.'
                    ], 400);
                }

                return redirect()->route('professional.plans.failed', $purchase->id ?? 0)
                    ->with('error', 'Payment verification failed. Please contact support.');

            } catch (\Exception $e) {
                if (isset($purchase)) {
                    $purchase->update(['payment_status' => 'failed']);
                }

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'An error occurred during payment processing. Please contact support.'
                    ], 500);
                }

                return redirect()->route('professional.plans.index')
                    ->with('error', 'An error occurred during payment processing. Please contact support.');
            }
        }

        /**
         * Show payment success page
         */
        public function success($id)
        {
            $purchase = ProfessionalPlanPurchase::with('plan')
                ->where('professional_id', Auth::id())
                ->findOrFail($id);

            return view('professional.plans.success', compact('purchase'));
        }

        /**
         * Show payment failed page
         */
        public function failed($id)
        {
            $purchase = ProfessionalPlanPurchase::with('plan')
                ->where('professional_id', Auth::id())
                ->findOrFail($id);

            return view('professional.plans.failed', compact('purchase'));
        }

        /**
         * Show current active plan
         */
        public function myPlan()
        {
            $purchase = ProfessionalPlanPurchase::getActivePlanForProfessional(Auth::id());

            $pendingRequests = ProfessionalPlanPurchase::where('professional_id', Auth::id())
                ->where('payment_status', 'pending')
                ->orderBy('created_at', 'desc')
                ->get();

            $subscriptionHistory = ProfessionalPlanPurchase::where('professional_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get();

            return view('professional.plans.my-plan', compact('purchase', 'pendingRequests', 'subscriptionHistory'));
        }
    }
