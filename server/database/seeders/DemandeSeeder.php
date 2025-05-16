<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemandeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = \App\Models\User::where('type', 'utilisateur')->get();
        foreach ($users as $user) {
            \App\Models\Demande::factory()->count(2)->create(['user_id' => $user->id]);
        }
    }
}
