<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use App\Models\Availability;
use App\Models\Professional;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AvailabilityController extends Controller
{
    public function index(Request $request)
    {
        $query = Availability::with('slots')
            ->where('professional_id', Auth::guard('professional')->id());

        if ($request->filled('search_month')) {
            $searchMonth = strtolower($request->search_month);
            $query->where('month', $searchMonth); 
        }

        $availability = $query->get();
        $availableMonths = Availability::where('professional_id', Auth::guard('professional')->id())
            ->pluck('month')
            ->unique()
            ->sort()
            ->values();

        return view('professional.availability.index', compact('availability', 'availableMonths'));
    }

    public function create()
    {
        return view('professional.availability.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'months' => 'required|array|min:1|max:12',
            'months.*' => 'required|string|date_format:Y-m|distinct',
            'session_duration' => 'required|integer|min:15|max:240',
            'weekly_slots' => 'required|string|max:20000',
        ]);

        $professionalId = Auth::guard('professional')->id();
        $weeklySlots = $this->validatedWeeklySlots($request->weekly_slots);
        $successfulMonths = [];
        $skippedMonths = [];

        try {
            DB::transaction(function () use (
                $validated,
                $weeklySlots,
                $professionalId,
                &$successfulMonths,
                &$skippedMonths
            ) {
                Professional::whereKey($professionalId)->lockForUpdate()->firstOrFail();
                foreach ($validated['months'] as $month) {
                    $exists = Availability::where('professional_id', $professionalId)
                        ->where('month', $month)
                        ->exists();

                    if ($exists) {
                        $skippedMonths[] = $month;
                        continue;
                    }

                    $availability = Availability::create([
                        'professional_id' => $professionalId,
                        'month' => $month,
                        'session_duration' => $validated['session_duration'],
                        'weekdays' => json_encode([]),
                    ]);
                    $this->createSlots($availability, $weeklySlots);
                    $successfulMonths[] = $month;
                }
            });
        } catch (\Throwable $exception) {
            Log::error('Failed to create professional availability.', [
                'professional_id' => $professionalId,
                'exception' => $exception,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Availability could not be saved. No changes were applied; please try again.',
            ], 500);
        }

        $message = '';
        if ($successfulMonths) {
            $message .= 'Availability created for ' . count($successfulMonths) . ' month(s). ';
        }
        if ($skippedMonths) {
            $message .= count($skippedMonths) . ' month(s) already had availability and were skipped. ';
        }

        return response()->json([
            'success' => true,
            'message' => trim($message),
            'redirect' => route('professional.availability.index')
        ]);
    }

    private function validatedWeeklySlots(string $json): array
    {
        $slots = json_decode($json, true);
        if (!is_array($slots) || !array_is_list($slots) || count($slots) === 0 || count($slots) > 100) {
            throw ValidationException::withMessages([
                'weekly_slots' => 'Add at least one valid time slot.',
            ]);
        }

        $validator = Validator::make($slots, [
            '*.weekday' => 'required|string|in:mon,tue,wed,thu,fri,sat,sun',
            '*.start_time' => 'required|date_format:H:i',
            '*.end_time' => 'required|date_format:H:i',
        ]);
        $validator->validate();

        $byDay = [];
        foreach ($slots as $index => $slot) {
            if ($slot['start_time'] >= $slot['end_time']) {
                throw ValidationException::withMessages([
                    'weekly_slots.' . $index . '.end_time' => 'The end time must be after the start time.',
                ]);
            }

            $byDay[$slot['weekday']][] = $slot;
        }

        foreach ($byDay as $daySlots) {
            usort($daySlots, fn (array $left, array $right) => strcmp($left['start_time'], $right['start_time']));
            $previousEnd = null;
            foreach ($daySlots as $slot) {
                if ($previousEnd !== null && $slot['start_time'] < $previousEnd) {
                    throw ValidationException::withMessages([
                        'weekly_slots' => 'Time slots on the same weekday cannot overlap.',
                    ]);
                }
                $previousEnd = $slot['end_time'];
            }
        }

        return $slots;
    }

    private function createSlots(Availability $availability, array $weeklySlots): void
    {
        foreach ($weeklySlots as $slot) {
            $availability->availabilitySlots()->create([
                'weekday' => $slot['weekday'],
                'start_time' => $slot['start_time'] . ':00',
                'end_time' => $slot['end_time'] . ':00',
            ]);
        }
    }

    public function show(Availability $availability)
    {
        if ($availability->professional_id != Auth::guard('professional')->id()) {
            abort(403);
        }
        
        return view('professional.availability.show', compact('availability'));
    }

    public function edit($id)
    {
        $availability = Availability::with('availabilitySlots')
                                  ->where('professional_id', Auth::guard('professional')->id())
                                  ->findOrFail($id);

        // Group slots by weekday
        $weeklySlots = [];
        foreach ($availability->availabilitySlots as $slot) {
            $weeklySlots[] = [
                'weekday' => $slot->weekday,
                'start_time' => Carbon::parse($slot->start_time)->format('H:i'),
                'end_time' => Carbon::parse($slot->end_time)->format('H:i'),
            ];
        }

        return view('professional.availability.edit', compact('availability', 'weeklySlots'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'months' => 'required|array|min:1|max:12',
            'months.*' => 'required|string|date_format:Y-m|distinct',
            'session_duration' => 'required|integer|min:15|max:240',
            'weekly_slots' => 'required|string|max:20000',
        ]);

        $professionalId = Auth::guard('professional')->id();
        $availability = Availability::where('professional_id', $professionalId)->findOrFail($id);
        $weeklySlots = $this->validatedWeeklySlots($request->weekly_slots);

        $selectedMonths = $validated['months'];
        $firstMonth = array_shift($selectedMonths);
        
        $successfulMonths = [];
        $skippedMonths = [];

        try {
            DB::transaction(function () use (
                $availability,
                $professionalId,
                $firstMonth,
                $selectedMonths,
                $weeklySlots,
                $validated,
                &$successfulMonths,
                &$skippedMonths
            ) {
                Professional::whereKey($professionalId)->lockForUpdate()->firstOrFail();
                $conflict = Availability::where('professional_id', $professionalId)
                    ->where('month', $firstMonth)
                    ->where('id', '!=', $availability->id)
                    ->exists();

                if ($conflict) {
                    $skippedMonths[] = $firstMonth;
                } else {
                    $availability->update([
                        'month' => $firstMonth,
                        'session_duration' => $validated['session_duration'],
                        'weekdays' => json_encode([]),
                    ]);
                    $availability->availabilitySlots()->delete();
                    $this->createSlots($availability, $weeklySlots);
                    $successfulMonths[] = $firstMonth;
                }

                foreach ($selectedMonths as $month) {
                    $exists = Availability::where('professional_id', $professionalId)
                        ->where('month', $month)
                        ->exists();

                    if ($exists) {
                        $skippedMonths[] = $month;
                        continue;
                    }

                    $newAvailability = Availability::create([
                        'professional_id' => $professionalId,
                        'month' => $month,
                        'session_duration' => $validated['session_duration'],
                        'weekdays' => json_encode([]),
                    ]);
                    $this->createSlots($newAvailability, $weeklySlots);
                    $successfulMonths[] = $month;
                }
            });
        } catch (\Exception $e) {
            Log::error('Failed to update professional availability.', [
                'availability_id' => $availability->id,
                'professional_id' => $professionalId,
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Availability could not be saved. No changes were applied; please try again.',
            ], 500);
        }

        $message = '';
        if ($successfulMonths) {
            $message .= 'Availability updated for ' . count($successfulMonths) . ' month(s). ';
        }
        if ($skippedMonths) {
            $message .= count($skippedMonths) . ' month(s) already had availability and were skipped. ';
        }

        return response()->json([
            'success' => true,
            'message' => trim($message),
            'redirect' => route('professional.availability.index')
        ]);
    }

    public function destroy($id)
    {
        $availability = Availability::where('professional_id', Auth::guard('professional')->id())
            ->findOrFail($id);

        try {
            // Delete associated slots first
            $availability->availabilitySlots()->delete();
            
            // Delete the availability
            $availability->delete();

            return response()->json([
                'success' => true,
                'message' => 'Availability deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to delete availability: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete availability'
            ], 500);
        }
    }
}