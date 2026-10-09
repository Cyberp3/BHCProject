<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Drops the appointments table from the database.
     */
    public function up(): void
    {
        Schema::dropIfExists('appointments');
    }

    /**
     * Reverse the migrations.
     * Recreates the appointments table if you ever choose to restore the appointment system.
     */
    public function down(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('scheduled_by')->constrained('users')->restrictOnDelete();
            $table->date('appointment_date')->index();
            $table->time('appointment_time')->nullable();
            $table->string('service_type', 50);
            $table->string('purpose');
            $table->enum('status', ['scheduled', 'attended', 'cancelled', 'missed'])->default('scheduled');
            $table->text('status_notes')->nullable();
            $table->timestamps();
        });
    }
};
