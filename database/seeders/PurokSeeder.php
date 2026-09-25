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
        //
        $puroks = [
            ['name' => 'Purok 1', 'description' => 'Barangay Tacunan, Davao City'],
            ['name' => 'Purok 2', 'description' => 'Barangay Tacunan, Davao City'],
            ['name' => 'Purok 3', 'description' => 'Barangay Tacunan, Davao City'],
            ['name' => 'Purok 4', 'description' => 'Barangay Tacunan, Davao City'],
            ['name' => 'Purok 5', 'description' => 'Barangay Tacunan, Davao City'],
            ['name' => 'Purok 6', 'description' => 'Barangay Tacunan, Davao City'],
            ['name' => 'Purok 7', 'description' => 'Barangay Tacunan, Davao City'],
            ['name' => 'Purok 8', 'description' => 'Barangay Tacunan, Davao City'],
            ['name' => 'Purok 9', 'description' => 'Barangay Tacunan, Davao City'],
            ['name' => 'Purok 10', 'description' => 'Barangay Tacunan, Davao City'],
            ['name' => 'Purok 11', 'description' => 'Barangay Tacunan, Davao City'],
            ['name' => 'Purok 12', 'description' => 'Barangay Tacunan, Davao City'],
        ];

        foreach ($puroks as $purok) {
            Purok::firstOrCreate(['name' => $purok['name']], $purok);
        }
    }
}
