<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReponseBesoinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $demandes = \App\Models\Demande::all();
        $users = \App\Models\User::where('type', 'utilisateur')->get();
        foreach ($demandes as $demande) {
            \App\Models\ReponseBesoin::factory()->count(1)->create([
                'demande_id' => $demande->demande_id,
                'user_id' => $users->random()->id
            ]);
        }
    }
}
