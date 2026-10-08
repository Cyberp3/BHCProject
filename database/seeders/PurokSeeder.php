<?php

namespace Database\Seeders;

use App\Models\Purok;
use Illuminate\Database\Seeder;

class PurokSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 22; $i++) {
            Purok::firstOrCreate(
                ['name' => "Purok {$i}"],
                ['description' => 'Barangay Tacunan, Davao City']
            );
        }
    }
}
