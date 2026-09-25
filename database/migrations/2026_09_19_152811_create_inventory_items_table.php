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
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_code', 50)->unique();
            $table->string('name', 150);
            $table->string('generic_name', 150)->nullable();
            $table->enum('category', ['medicine', 'vaccine'])->index();
            $table->string('dosage_form', 100)->nullable();
            $table->string('unit_of_measure', 50);
            $table->unsignedInteger('minimum_stock_alert')->default(20);
            $table->boolean('is_cold_chain')->default(false);
            $table->string('storage_temperature_note', 100)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
