<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // List users with optional filters
    public function index(Request $request)
    {
        $query = User::query();

        // Only load users with type 'utilisateur'
        $query->where('type', 'utilisateur');

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%$search%")
                  ->orWhere('prenom', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('telephone', 'like', "%$search%")
                  ->orWhere('adresse', 'like', "%$search%")
                  ;
            });
        }

        if ($groupe = $request->input('groupe_sanguin')) {
            $query->where('groupe_sanguin', $groupe);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $perPage = $request->input('per_page', 10);
        $users = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($users);
    }

    // Update user
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'prenom' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:users,email,' . $user->id,
            'telephone' => 'sometimes|string|max:20',
            'date_de_naissance' => 'sometimes|date',
            'groupe_sanguin' => 'sometimes|string',
            'adresse' => 'sometimes|string',
            'status' => 'sometimes|string|in:active,inactive,pending',
        ]);

        $user->update($validated);

        return response()->json(['message' => 'Utilisateur mis à jour', 'user' => $user]);
    }

    // Delete user
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'Utilisateur supprimé']);
    }
    public function show($id)
{
    $user = \App\Models\User::findOrFail($id);
    return response()->json([
        'nom' => $user->nom,
        'prenom' => $user->prenom,
        'email' => $user->email,
        'telephone' => $user->telephone,
        'groupe_sanguin' => $user->groupe_sanguin,
    ]);
}
} 