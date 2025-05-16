<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AvailableSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $centres = \App\Models\User::where('type', 'centre_de_don')->get();
        foreach ($centres as $centre) {
            \App\Models\AvailableSlot::factory()->count(5)->create(['centre_id' => $centre->id]);
        }
    }
}
