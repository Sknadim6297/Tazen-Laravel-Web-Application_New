<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use App\Models\Rate;
use App\Models\ProfessionalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $professionalId = Auth::guard('professional')->id();
        
        $rates = Rate::where('professional_id', $professionalId)
            ->whereHas('professionalService', fn ($query) => $query->where('professional_id', $professionalId))
            ->with(['professionalService.service', 'subService'])
            ->get();
            
        // Get professional services to show service names
        $professionalServices = ProfessionalService::where('professional_id', $professionalId)
            ->with('service')
            ->get()
            ->keyBy('service_id');
            
        $rateCount = Rate::where('professional_id', $professionalId)->count();

        return view('professional.rate.index', compact('rates', 'rateCount', 'professionalServices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $professionalId = Auth::guard('professional')->id();
        $professionalServices = ProfessionalService::where('professional_id', $professionalId)
            ->with('service', 'subServices')
            ->get();

        return view('professional.rate.create', compact('professionalServices'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'professional_service_id' => 'required|integer|exists:professional_services,id',
            'rateData' => 'required|array|min:1|max:100',
            'rateData.*.session_type' => 'required|string|in:One Time,Monthly,Quarterly,Free Hand',
            'rateData.*.sub_service_id' => 'nullable|exists:sub_services,id',
            'rateData.*.num_sessions' => 'required|integer|min:1|max:10000',
            'rateData.*.rate_per_session' => 'required|numeric|min:0|max:999999.99',
            'rateData.*.final_rate' => 'required|numeric|min:0|max:999999.99',
            'rateData.*.features' => 'nullable|array',
            'rateData.*.features.*' => 'nullable|string|max:255',
        ]);
        
        $professionalId = Auth::guard('professional')->id();
        $professionalServiceId = $validated['professional_service_id'];
        $professionalService = ProfessionalService::where('professional_id', $professionalId)
            ->with('subServices')
            ->findOrFail($professionalServiceId);
        $allowedSubServiceIds = array_map('intval', $professionalService->subServices->modelKeys());
        
        $combinations = [];
        foreach ($validated['rateData'] as $rate) {
            if ($rate['num_sessions'] * $rate['rate_per_session'] > 999999.99) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'The final rate cannot exceed ₹999,999.99.',
                ], 422);
            }

            if (!empty($rate['sub_service_id']) && !in_array((int) $rate['sub_service_id'], $allowedSubServiceIds, true)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'A selected sub-service does not belong to your service.',
                ], 422);
            }

            $key = $rate['session_type'] . '|' . ($rate['sub_service_id'] ?? 'null');
            if (isset($combinations[$key])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Duplicate session types with the same sub-service are not allowed in the same submission.',
                ], 422);
            }
            $combinations[$key] = true;
        }
        
        foreach ($validated['rateData'] as $rate) {
            $exists = Rate::where('professional_id', $professionalId)
                    ->where('professional_service_id', $professionalServiceId)
                ->where('session_type', $rate['session_type'])
                ->where('sub_service_id', $rate['sub_service_id'] ?? null)
                ->exists();
                
            if ($exists) {
                $subServiceMsg = isset($rate['sub_service_id']) ? ' with this sub-service' : '';
                return response()->json([
                    'status' => 'error',
                    'message' => 'You already have a rate for session type "' . $rate['session_type'] . '"' . $subServiceMsg . '.',
                ], 422);
            }
        }

        DB::beginTransaction();

        try {
            ProfessionalService::where('professional_id', $professionalId)
                ->lockForUpdate()
                ->findOrFail($professionalServiceId);

            foreach ($validated['rateData'] as $rate) {
                $duplicate = Rate::where('professional_id', $professionalId)
                    ->where('professional_service_id', $professionalServiceId)
                    ->where('session_type', $rate['session_type'])
                    ->where('sub_service_id', $rate['sub_service_id'] ?? null)
                    ->exists();

                if ($duplicate) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'A rate already exists for this session type and service.',
                    ], 422);
                }
            }

            foreach ($validated['rateData'] as $rate) {
                // Filter out empty features
                $features = isset($rate['features']) ? array_values(array_filter($rate['features'], function($feature) {
                    return !empty(trim($feature));
                })) : [];
                
                // Set to null if no valid features
                if (empty($features)) {
                    $features = null;
                }
                
                Rate::create([
                    'professional_id' => $professionalId,
                        'professional_service_id' => $professionalServiceId,
                    'session_type' => $rate['session_type'],
                    'sub_service_id' => $rate['sub_service_id'] ?? null,
                    'num_sessions' => $rate['num_sessions'],
                    'rate_per_session' => $rate['rate_per_session'],
                    'final_rate' => $rate['num_sessions'] * $rate['rate_per_session'],
                    'features' => $features,
                ]);
            }
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Rates added successfully.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Failed to create professional rates.', [
                'professional_id' => $professionalId,
                'professional_service_id' => $professionalServiceId,
                'exception' => $e,
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while adding the rates. Please try again.',
            ], 500);
        }
    }

    /**
     * Get already used session types for the logged in professional
     * Returns session types with their sub_service_id to properly check duplicates
     */
    public function getSessionTypes(Request $request)
    {
        $professionalId = Auth::guard('professional')->id();
        $validated = $request->validate([
            'professional_service_id' => 'required|integer',
        ]);
        $professionalService = ProfessionalService::where('professional_id', $professionalId)
            ->findOrFail($validated['professional_service_id']);

        $rates = Rate::where('professional_id', $professionalId)
            ->where('professional_service_id', $professionalService->id)
            ->select('session_type', 'sub_service_id')
            ->get()
            ->map(function($rate) {
                return [
                    'session_type' => $rate->session_type,
                    'sub_service_id' => $rate->sub_service_id
                ];
            })
            ->toArray();
        
        return response()->json([
            'status' => 'success',
            'rates' => $rates
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $rates = Rate::with('professionalService.service', 'subService')
            ->where('professional_id', Auth::guard('professional')->id())
            ->whereHas('professionalService', fn ($query) => $query->where('professional_id', Auth::guard('professional')->id()))
            ->findOrFail($id);
        $professionalService = $rates->professionalService;

        return view('professional.rate.edit', compact('rates', 'professionalService'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $professionalId = Auth::guard('professional')->id();
        $rate = Rate::where('professional_id', $professionalId)
            ->whereHas('professionalService', fn ($query) => $query->where('professional_id', $professionalId))
            ->findOrFail($id);
        $validated = $request->validate([
            'session_type' => 'required|string|in:One Time,Monthly,Quarterly,Free Hand',
            'num_sessions' => 'required|integer|min:1|max:10000',
            'rate_per_session' => 'required|numeric|min:0|max:999999.99',
            'features' => 'nullable|array',
            'features.*' => 'nullable|string|max:255',
        ]);

        // Filter out empty features
        if (isset($validated['features'])) {
            $validated['features'] = array_values(array_filter($validated['features'], function($feature) {
                return !empty(trim($feature));
            }));
            
            // If no valid features remain, set to null
            if (empty($validated['features'])) {
                $validated['features'] = null;
            }
        }

        if ($validated['num_sessions'] * $validated['rate_per_session'] > 999999.99) {
            return response()->json([
                'success' => false,
                'message' => 'The final rate cannot exceed ₹999,999.99.',
            ], 422);
        }

        $duplicate = Rate::where('professional_id', $rate->professional_id)
            ->where('professional_service_id', $rate->professional_service_id)
            ->where('session_type', $validated['session_type'])
            ->where('sub_service_id', $rate->sub_service_id)
            ->where('id', '!=', $rate->id)
            ->exists();
        if ($duplicate) {
            return response()->json([
                'success' => false,
                'message' => 'A rate already exists for this session type and service.',
            ], 422);
        }

        $validated['final_rate'] = $validated['num_sessions'] * $validated['rate_per_session'];
        $rate->update($validated);

        return response()->json(['success' => true, 'message' => 'Rate updated successfully.']);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $professionalId = Auth::guard('professional')->id();
        $rate = Rate::where('professional_id', $professionalId)
            ->whereHas('professionalService', fn ($query) => $query->where('professional_id', $professionalId))
            ->findOrFail($id);
        $rate->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Rate has been deleted successfully.'
        ]);
    }
}
