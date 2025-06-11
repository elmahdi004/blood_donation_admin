<?php

namespace App\Http\Controllers;

use App\Models\IntentionDon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IntentionDonController extends Controller
{
    public function index()
    {
        return response()->json(IntentionDon::with('user')->latest()->get());
    }

    public function userIntentions($userId)
    {
        $user = User::findOrFail($userId);
        return response()->json($user->intentionDons()->latest()->get());
    }

    public function getUserIntentions(User $user)
    {
        $intentions = IntentionDon::with('user')
            ->latest('created_at')
            ->get()
            ->map(function ($intention) {
                return [
                    'id' => $intention->id,
                    'groupe_sanguin' => $intention->groupe_sanguin,
                    'date_disponibilite' => $intention->date_disponibilite,
                    'valide' => (bool) $intention->valide,
                    'notes' => $intention->notes,
                    'created_at' => $intention->created_at,
                    'user' => [
                        'id' => $intention->user->id,
                        'nom' => $intention->user->nom,
                        'prenom' => $intention->user->prenom,
                        'email' => $intention->user->email,
                        'telephone' => $intention->user->telephone,
                    ],
                ];
            });

        return response()->json($intentions);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'groupe_sanguin' => 'required|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'date_disponibilite' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $intention = IntentionDon::create([
                'user_id' => $request->user()->id,
                'groupe_sanguin' => $validated['groupe_sanguin'],
                'date_disponibilite' => $validated['date_disponibilite'],
                'notes' => $validated['notes'],
                'valide' => false,
            ]);

            DB::commit();
            return response()->json($intention, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create intention de don',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(IntentionDon $intentionDon)
    {
        if ($intentionDon->user_id !== request()->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            DB::beginTransaction();
            $intentionDon->delete();
            DB::commit();
            return response()->json(['message' => 'Intention de don supprimée avec succès']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete intention de don',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getAuthIntentions(){
        $auth = Auth::user();
        $intentions = IntentionDon::where('user_id', $auth->id)->get();
        return response()->json($intentions);
    }
} 