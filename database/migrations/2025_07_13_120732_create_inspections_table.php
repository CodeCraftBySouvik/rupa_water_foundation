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
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained('locations')->onDelete('cascade');
            $table->foreignId('checked_by')->constrained('users')->onDelete('cascade');

            $table->date('checked_date');

            $table->enum('water_quality', ['good', 'poor'])->nullable();
            $table->enum('electric_available', ['yes', 'no'])->nullable();
            $table->enum('cooling_system', ['working', 'not working'])->nullable();
            $table->enum('cleanliness', ['clean', 'dirty'])->nullable();
            $table->enum('tap_glass_condition', ['present', 'not present'])->nullable();
            $table->enum('electric_meter_working', ['yes', 'no'])->nullable();
            $table->enum('compressor_condition', ['ok', 'not ok'])->nullable();
            $table->enum('light_availability', ['yes', 'no'])->nullable();
            $table->enum('filter_condition', ['ok', 'not ok'])->nullable();
            $table->enum('electric_usage_method', ['hooking', 'proper'])->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspections');
    }
};
