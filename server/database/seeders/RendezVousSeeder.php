<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RendezVousSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        $slots = \App\Models\AvailableSlot::all();
        $users = \App\Models\User::where('type', 'utilisateur')->get();
        foreach ($slots as $slot) {
            if (!$slot->is_booked && $faker->boolean(50)) {
                \App\Models\RendezVous::factory()->create([
                    'slot_id' => $slot->slot_id,
                    'user_id' => $users->random()->id,
                ]);
                $slot->is_booked = true;
                $slot->save();
            }
        }
    }
}
