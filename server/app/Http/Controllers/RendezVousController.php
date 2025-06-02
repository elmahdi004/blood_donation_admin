<?php

namespace App\Http\Controllers;

use App\Models\RendezVous;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RendezVousController extends Controller
{
    public function index(Request $request)
    {
        $bloodBank = $request->user();
        $rendezVous = $bloodBank->rendezVousCentre()->latest()->get();
        return response()->json($rendezVous);
    }

    public function store(Request $request)
    {
        $bloodBank = $request->user();
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'centre_id' => 'required|exists:users,id',
            'slot_id' => 'required|exists:available_slots,slot_id',
            // 'date' => 'required|date',
            'status' => 'required|string|in:pending,confirmed,completed,cancelled',
        ]);

        $rendezVous = RendezVous::create($validated);
        return response()->json($rendezVous, 201);
    }

    public function update(Request $request, RendezVous $rendezVous)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,confirmed,completed,cancelled',
        ]);
        $rendezVous->update($validated);
        return response()->json($rendezVous);
    }

    public function destroy(RendezVous $rendezVous)
    {
        $rendezVous->delete();
        return response()->json(['message' => 'Rendez-vous supprimé']);
    }

    public function getUserAppointments(User $user)
    {
        $appointments = RendezVous::with(['slot', 'centre'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($appointment) use ($user) {
                return [
                    'id' => $appointment->id,
                    'date' => $appointment->slot->date,
                    'status' => $appointment->status,
                    'start_time' => $appointment->slot->start_time,
                    'end_time' => $appointment->slot->end_time,
                    'blood_bank_name' => $appointment->centre->nom,
                    'user_name' => $user->nom,
                    'user_surname' => $user->prenom,
                ];
            });

        return response()->json($appointments);
    }

    public function cancelAppointment(Request $request, RendezVous $rendezVous)
    {
        // Check if the appointment belongs to the authenticated user
        if ($rendezVous->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Check if the appointment is in a cancellable state
        if (!in_array($rendezVous->status, ['pending', 'confirmed'])) {
            return response()->json([
                'message' => 'Cannot cancel appointment in ' . $rendezVous->status . ' state'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $rendezVous->update(['status' => 'cancelled']);

            DB::commit();
            return response()->json([
                'message' => 'Appointment cancelled successfully',
                'appointment' => $rendezVous
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to cancel appointment',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 