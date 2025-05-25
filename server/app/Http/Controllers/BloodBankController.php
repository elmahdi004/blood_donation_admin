<?php

namespace App\Http\Controllers;

use App\Models\BloodBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class BloodBankController extends Controller
{
    public function index()
    {
        $bloodBanks = BloodBank::all();
        return response()->json($bloodBanks);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'region' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:blood_banks',
            'password' => 'required|string|min:8|confirmed',
            'opening_hours' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $bloodBank = BloodBank::create([
            ...$validated,
            'password' => Hash::make($validated['password']),
            'status' => 'pending'
        ]);

        return response()->json([
            'message' => 'Demande d\'inscription soumise avec succès. Veuillez attendre l\'approbation de l\'administrateur.',
            'blood_bank' => $bloodBank
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $bloodBank = BloodBank::where('email', $request->email)->first();

        if (!$bloodBank || !Hash::check($request->password, $bloodBank->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants fournis sont incorrects.'],
            ]);
        }

        if ($bloodBank->isPending()) {
            throw ValidationException::withMessages([
                'email' => ['Votre compte est en attente d\'approbation.'],
            ]);
        }

        if ($bloodBank->isRejected()) {
            throw ValidationException::withMessages([
                'email' => ['Votre compte a été rejeté. Veuillez contacter l\'administrateur.'],
            ]);
        }

        $token = $bloodBank->createToken('blood_bank_token')->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie',
            'blood_bank' => $bloodBank,
            'token' => $token
        ]);
    }

    public function updateStatus(Request $request, BloodBank $bloodBank)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected'])],
        ]);

        $bloodBank->update($validated);

        return response()->json([
            'message' => 'Statut du centre mis à jour avec succès',
            'blood_bank' => $bloodBank
        ]);
    }

    public function show(BloodBank $bloodBank)
    {
        return response()->json($bloodBank);
    }

    public function update(Request $request, BloodBank $bloodBank)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'region' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => [
                'required',
                'email',
                Rule::unique('blood_banks')->ignore($bloodBank->id)
            ],
            'opening_hours' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_active' => 'boolean'
        ]);

        $bloodBank->update($validated);

        return response()->json([
            'message' => 'Centre de don de sang modifié avec succès',
            'blood_bank' => $bloodBank
        ]);
    }

    public function destroy(BloodBank $bloodBank)
    {
        $bloodBank->delete();

        return response()->json([
            'message' => 'Centre de don de sang supprimé avec succès'
        ]);
    }
} 