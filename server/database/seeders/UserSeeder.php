<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::factory()->count(10)->create(['type' => 'utilisateur']);
        \App\Models\User::factory()->count(3)->create(['type' => 'centre_de_don']);
        \App\Models\User::factory()->count(2)->create(['type' => 'admin']);
    }
}
