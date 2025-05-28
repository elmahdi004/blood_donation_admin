<?php

namespace App\Http\Controllers;

use App\Models\AvailableSlot;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AvailableSlotController extends Controller
{
    public function index(Request $request)
    {
        $bloodBank = $request->user();

        $slots = DB::table('available_slots')
            ->leftJoin('rendez_vouses', 'available_slots.slot_id', '=', 'rendez_vouses.slot_id')
            ->where('available_slots.centre_id', $bloodBank->id)
            ->select(
                'available_slots.*',
                'rendez_vouses.rendez_vous_id as rendez_vous_id',
                'rendez_vouses.status as rendez_vous_status',
                'rendez_vouses.user_id as rendez_vous_user_id',
                'rendez_vouses.created_at as rendez_vous_created_at',
                'rendez_vouses.updated_at as rendez_vous_updated_at',
                'rendez_vouses.centre_id as rendez_vous_centre_id'
            )
            ->orderByDesc('available_slots.slot_id')
            ->get();

        return response()->json($slots);
    }

    public function store(Request $request)
    {
        $bloodBank = $request->user();
        $validated = $request->validate([
            'date' => 'required|date',
            'morning_start' => 'required',
            'morning_end' => 'required|after:morning_start',
            'morning_max' => 'required|integer|min:1',
            'evening_start' => 'required',
            'evening_end' => 'required|after:evening_start',
            'evening_max' => 'required|integer|min:1',
        ], [
            'evening_end.after' => 'The evening end time must be after the evening start time.',
        ]);

        $date = $validated['date'];
        $morningStart = Carbon::parse($date . ' ' . $validated['morning_start']);
        $morningEnd = Carbon::parse($date . ' ' . $validated['morning_end']);
        $morningDuration = $morningStart->diffInMinutes($morningEnd);
        $morningSlotDuration = $morningDuration / $validated['morning_max'];

        $eveningStart = Carbon::parse($date . ' ' . $validated['evening_start']);
        $eveningEnd = Carbon::parse($date . ' ' . $validated['evening_end']);
        $eveningDuration = $eveningStart->diffInMinutes($eveningEnd);
        $eveningSlotDuration = $eveningDuration / $validated['evening_max'];

        $slots = [];

        // Create morning slots
        for ($i = 0; $i < $validated['morning_max']; $i++) {
            $slotStart = $morningStart->copy()->addMinutes($i * $morningSlotDuration);
            $slotEnd = $slotStart->copy()->addMinutes($morningSlotDuration);
            $slots[] = [
                'date' => $date,
                'start_time' => $slotStart->format('H:i'),
                'end_time' => $slotEnd->format('H:i'),
                'max_appointments' => 1,
                'blood_bank_id' => $bloodBank->id,
            ];
        }

        // Create evening slots
        for ($i = 0; $i < $validated['evening_max']; $i++) {
            $slotStart = $eveningStart->copy()->addMinutes($i * $eveningSlotDuration);
            $slotEnd = $slotStart->copy()->addMinutes($eveningSlotDuration);
            $slots[] = [
                'date' => $date,
                'start_time' => $slotStart->format('H:i'),
                'end_time' => $slotEnd->format('H:i'),
                'max_appointments' => 1,
                'blood_bank_id' => $bloodBank->id,
            ];
        }

        AvailableSlot::insert($slots);
        return response()->json(['message' => 'Créneaux publiés avec succès'], 201);
    }

    public function update(Request $request, AvailableSlot $slot)
    {
        $validated = $request->validate([
            'date' => 'sometimes|date',
            'start_time' => 'sometimes',
            'end_time' => 'sometimes|after:start_time',
            'max_appointments' => 'sometimes|integer|min:1',
        ]);
        $slot->update($validated);
        return response()->json($slot);
    }

    public function destroy(AvailableSlot $slot)
    {
        $slot->delete();
        return response()->json(['message' => 'Créneau supprimé']);
    }
} 