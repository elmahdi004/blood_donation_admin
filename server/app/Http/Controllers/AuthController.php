<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\BloodBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'telephone' => 'required|string|regex:/^[0-9]{10}$/',
            'date_de_naissance' => 'required|date|before:-18 years',
            'groupe_sanguin' => 'required|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'adresse' => 'required|string|min:5',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
            'telephone' => $validated['telephone'],
            'date_de_naissance' => $validated['date_de_naissance'],
            'groupe_sanguin' => $validated['groupe_sanguin'],
            'adresse' => $validated['adresse'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Inscription réussie',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // First, try to find an admin user
        $user = User::where('email', $request->email)
                   ->where('type', 'admin')
                   ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'message' => 'Connexion réussie',
                'user' => $user,
                'token' => $token,
                'type' => 'admin'
            ]);
        }

        // If not an admin, try blood bank
        $bloodBank = BloodBank::where('email', $request->email)->first();

        if ($bloodBank && Hash::check($request->password, $bloodBank->password)) {
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

            $token = $bloodBank->createToken('auth_token')->plainTextToken;
            return response()->json([
                'message' => 'Connexion réussie',
                'user' => $bloodBank,
                'token' => $token,
                'type' => 'blood_bank'
            ]);
        }

        throw ValidationException::withMessages([
            'email' => ['Les identifiants fournis sont incorrects.'],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie'
        ]);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function mobileLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Try to find a regular user (donor or receiver)
        $user = User::where('email', $request->email)
                   ->where('type', 'utilisateur')
                   ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            $token = $user->createToken('mobile_token')->plainTextToken;
            return response()->json([
                'message' => 'Connexion réussie',
                'user' => $user,
                'token' => $token,
                'type' => $user->type
            ]);
        }

        throw ValidationException::withMessages([
            'email' => ['Les identifiants fournis sont incorrects.'],
        ]);
    }
} 