<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $centres = \App\Models\User::where('type', 'centre_de_don')->get();
        foreach ($centres as $centre) {
            \App\Models\Stock::factory()->count(4)->create(['centre_id' => $centre->id]);
        }
    }
}
