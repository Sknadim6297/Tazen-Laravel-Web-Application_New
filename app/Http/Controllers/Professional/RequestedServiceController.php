<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use App\Models\Professional;
use App\Models\ProfessionalOtherInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RequestedServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $requestedServices = ProfessionalOtherInformation::where('professional_id', Auth::guard('professional')->id())->get();
        
        $serviceCount = ProfessionalOtherInformation::where('professional_id', Auth::guard('professional')->id())->count();
        return view('professional.requested_services.index', compact('requestedServices', 'serviceCount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('professional.requested_services.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $professionalId = Auth::guard('professional')->id();
        if (ProfessionalOtherInformation::where('professional_id', $professionalId)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Other Information already exists. Edit the existing entry instead.',
            ], 409);
        }

        $validated = $this->validateInformation($request);
        $created = DB::transaction(function () use ($professionalId, $validated) {
            Professional::whereKey($professionalId)->lockForUpdate()->firstOrFail();
            if (ProfessionalOtherInformation::where('professional_id', $professionalId)->exists()) {
                return false;
            }

            $this->saveInformation(new ProfessionalOtherInformation(), $validated, $professionalId);

            return true;
        });

        if (!$created) {
            return response()->json([
                'status' => 'error',
                'message' => 'Other Information already exists. Edit the existing entry instead.',
            ], 409);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Other Information added successfully.',
        ]);
    }

    public function show(string $id) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $info = ProfessionalOtherInformation::where('professional_id', Auth::guard('professional')->id())
            ->findOrFail($id);
        $services = json_decode($info->requested_service, true);
        $prices = json_decode($info->price, true);
        $education = json_decode($info->education, true);
        $services = is_array($services) ? $services : [];
        $prices = is_array($prices) ? $prices : [];
        $education = is_array($education) ? $education : [];
        $colleges = is_array($education['college_name'] ?? null) ? $education['college_name'] : [];
        $degrees = is_array($education['degree'] ?? null) ? $education['degree'] : [];

        return view('professional.requested_services.edit', compact(
            'info',
            'services',
            'prices',
            'colleges',
            'degrees'
        ));
    }
    public function update(Request $request, $id)
    {
        $info = ProfessionalOtherInformation::where('professional_id', Auth::guard('professional')->id())
            ->findOrFail($id);
        $validated = $this->validateInformation($request);
        $this->saveInformation($info, $validated, $info->professional_id);

        return response()->json([
            'status' => 'success',
            'message' => 'Other Information updated successfully.',
        ]);
    }

/**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $requestedService = ProfessionalOtherInformation::where('professional_id', Auth::guard('professional')->id())
            ->findOrFail($id);
        $requestedService->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Other Information deleted successfully.'
        ]);
    }

    private function validateInformation(Request $request): array
    {
        $validated = $request->validate([
            'sub_heading' => 'required|string|max:255',
            'requested_service' => 'required|array|min:1|max:20',
            'requested_service.*' => 'required|string|max:255',
            'price' => 'required|array|min:1|max:20',
            'price.*' => 'required|numeric|min:0|max:999999999.99',
            'education_statement' => 'nullable|string|max:5000',
            'college_name' => 'nullable|array|max:20',
            'college_name.*' => 'nullable|string|max:255',
            'degree' => 'nullable|array|max:20',
            'degree.*' => 'nullable|string|max:255',
        ]);

        $serviceKeys = array_keys($validated['requested_service']);
        $priceKeys = array_keys($validated['price']);
        sort($serviceKeys);
        sort($priceKeys);
        if ($serviceKeys !== $priceKeys) {
            throw ValidationException::withMessages([
                'price' => 'Each requested service must have exactly one price.',
            ]);
        }

        $collegeNames = $validated['college_name'] ?? [];
        $degrees = $validated['degree'] ?? [];
        $collegeKeys = array_keys($collegeNames);
        $degreeKeys = array_keys($degrees);
        sort($collegeKeys);
        sort($degreeKeys);
        if ($collegeKeys !== $degreeKeys) {
            throw ValidationException::withMessages([
                'education' => 'Each education entry must include both a college and a degree.',
            ]);
        }

        ksort($validated['requested_service']);
        ksort($validated['price']);
        ksort($collegeNames);
        ksort($degrees);
        $validated['college_name'] = array_values($collegeNames);
        $validated['degree'] = array_values($degrees);
        $validated['requested_service'] = array_values($validated['requested_service']);
        $validated['price'] = array_values($validated['price']);

        return $validated;
    }

    private function saveInformation(
        ProfessionalOtherInformation $info,
        array $validated,
        int $professionalId
    ): ProfessionalOtherInformation {
        $info->professional_id = $professionalId;
        $info->sub_heading = $validated['sub_heading'];
        $info->requested_service = json_encode(array_values($validated['requested_service']));
        $info->price = json_encode(array_values($validated['price']));
        $info->education_statement = $validated['education_statement'] ?? null;
        $info->education = json_encode([
            'college_name' => $validated['college_name'],
            'degree' => $validated['degree'],
        ]);
        $info->save();

        return $info;
    }
}
