<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class DonorController extends Controller
{
    public function index()
    {
        $donors = User::where('type', 'utilisateur')
            ->select([
                'id',
                'nom',
                'prenom',
                'email',
                'telephone',
                'groupe_sanguin',
                'adresse',
                'date_de_naissance',
                'created_at'
            ])
            ->get()
            ->map(function ($donor) {
                return [
                    'id' => $donor->id,
                    'nom' => $donor->nom,
                    'prenom' => $donor->prenom,
                    'email' => $donor->email,
                    'telephone' => $donor->telephone,
                    'groupe_sanguin' => $donor->groupe_sanguin,
                    'adresse' => $donor->adresse,
                    'age' => $donor->date_de_naissance->age,
                    'description' => "Donneur de sang {$donor->groupe_sanguin}",
                    'image' => null, // You can add profile image later
                ];
            });

        return response()->json($donors);
    }
} 