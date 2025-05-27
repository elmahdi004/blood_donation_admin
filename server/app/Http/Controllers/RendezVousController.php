<?php

namespace App\Http\Controllers;

use App\Models\RendezVous;
use Illuminate\Http\Request;

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
            'date' => 'required|date',
            'status' => 'required|string|in:pending,confirmed,completed,cancelled',
        ]);
        $rendezVous = $bloodBank->rendezVousCentre()->create($validated);
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
} 