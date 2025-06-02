<?php

namespace App\Http\Controllers;

use App\Models\Demande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\BloodRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BloodRequestController extends Controller
{
    public function index()
    {
        $requests = Demande::with('user:id,nom,prenom,telephone,groupe_sanguin')
            ->latest()
            ->get()
            ->map(function ($request) {
                return [
                    'id' => $request->demande_id,
                    'groupe_sanguin' => $request->groupe_sanguin,
                    'urgent' => $request->urgent,
                    'quantite' => $request->quantite,
                    'ville' => $request->ville,
                    'nom_hopital' => $request->nom_hopital,
                    'status' => $request->status,
                    'message' => $request->message,
                    'requester' => [
                        'name' => $request->user->nom . ' ' . $request->user->prenom,
                        'phone' => $request->user->telephone,
                        'blood_group' => $request->user->groupe_sanguin,
                    ],
                    'created_at' => $request->created_at->diffForHumans(),
                ];
            });

        return response()->json($requests);
    }

    public function store(Request $request)
    {
        try {
            Log::info('Received blood request:', $request->all());

            $request->validate([
                'groupe_sanguin' => 'required|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
                'urgent' => 'required|boolean',
                'quantite' => 'required|integer|min:1',
                'ville' => 'required|string|max:255',
                'nom_hopital' => 'required|string|max:255',
                'message' => 'nullable|string|max:500',
            ]);

            $demande = Demande::create([
                'user_id' => Auth::id(),
                'groupe_sanguin' => $request->groupe_sanguin,
                'urgent' => $request->urgent,
                'quantite' => $request->quantite,
                'ville' => $request->ville,
                'nom_hopital' => $request->nom_hopital,
                'message' => $request->message,
                'status' => 'pending'
            ]);

            Log::info('Blood request created successfully:', ['request' => $demande]);

            return response()->json([
                'message' => 'Blood request created successfully',
                'request' => $demande
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error:', ['errors' => $e->errors()]);
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating blood request:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Failed to create blood request',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 