<?php

use App\Models\Purok;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        for ($i = 1; $i <= 22; $i++) {
            Purok::firstOrCreate(
                ['name' => "Purok {$i}"],
                ['description' => 'Barangay Tacunan, Davao City']
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Purok::whereIn('name', array_map(fn ($i) => "Purok {$i}", range(13, 22)))->delete();
    }
};
