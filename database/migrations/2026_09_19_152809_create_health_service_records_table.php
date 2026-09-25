<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('health_service_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('service_type', 50)->index(); // family_planning, prenatal_care, immunization, cvd_screening, philpen, ntp, purok_kalusugan, bns_program
            $table->date('service_date');
            $table->text('complaint_or_reason')->nullable();
            $table->text('findings_and_notes')->nullable();
            $table->json('service_specific_data')->nullable();
            $table->date('next_follow_up_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_service_records');
    }
};
