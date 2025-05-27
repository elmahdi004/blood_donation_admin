<?php

namespace App\Http\Controllers;

use App\Models\AvailableSlot;
use Illuminate\Http\Request;

class AvailableSlotController extends Controller
{
    public function index(Request $request)
    {
        $bloodBank = $request->user();
        $slots = $bloodBank->slots()->latest()->get();
        return response()->json($slots);
    }

    public function store(Request $request)
    {
        $bloodBank = $request->user();
        $validated = $request->validate([
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'max_appointments' => 'required|integer|min:1',
        ]);
        $slot = $bloodBank->slots()->create($validated);
        return response()->json($slot, 201);
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