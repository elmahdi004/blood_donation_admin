<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Demande;
use App\Models\User;
use App\Models\Proposition;
use Illuminate\Support\Facades\Auth;

class DemandeController extends Controller
{
    public function index(Request $request)
    {
        $query = Demande::query();

        // Filtering
        if ($request->has('groupe_sanguin') && $request->groupe_sanguin) {
            $query->where('groupe_sanguin', $request->groupe_sanguin);
        }
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Pagination (default 10 per page)
        return $query->paginate($request->get('per_page', 10));
    }

    public function userDemandes($userId)
    {
        $user = \App\Models\User::findOrFail($userId);
        $demandes = $user->demandes()->latest()->get();
        return response()->json($demandes);
    }

    // New method to get authenticated user's requests
    public function getUserRequests()
    {
        // return Proposition::with('demande')->get();
        $userId = Auth::id();
        $requests = Demande::where('user_id', $userId)
            ->with([
                'propositions.user'
                // 'user:id,nom,prenom,email,telephone',
                // 'propositions' => function($query) {
                //     $query->with(['user:id,nom,prenom,email,telephone'])
                //           ->latest();
                // }
            ])
            ->latest()
            ->get();
        
        return response()->json($requests);
    }

    // New method to create a blood request
    public function createBloodRequest(Request $request)
    {
        $request->validate([
            'groupe_sanguin' => 'required|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'hopital' => 'required|string|max:255',
            'adresse_hopital' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $demande = Demande::create([
            'user_id' => Auth::id(),
            'groupe_sanguin' => $request->groupe_sanguin,
            'hopital' => $request->hopital,
            'adresse_hopital' => $request->adresse_hopital,
            'notes' => $request->notes,
            'status' => 'en_attente',
        ]);

        return response()->json($demande, 201);
    }

    // New method to delete a blood request
    public function deleteBloodRequest($id)
    {
        $demande = Demande::findOrFail($id);
        
        // Check if the request belongs to the authenticated user
        if ($demande->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $demande->delete();
        return response()->json(['message' => 'Request deleted successfully']);
    }
}
