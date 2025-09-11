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
        Schema::table('zone_wise_locations', function (Blueprint $table) {
            // Rename column location_name → location_id
            $table->renameColumn('location_name', 'location_id');
             // Change type to unsignedBigInteger
             $table->unsignedBigInteger('location_id')->change();
             // Add new columns
            $table->string('location_number')->nullable()->after('location_id');
            $table->string('title')->nullable()->after('location_number');
            $table->integer('position')->nullable()->after('title');
            $table->date('opening_date')->nullable()->after('position');

            // Add foreign key constraint
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zone_wise_locations', function (Blueprint $table) {
             // Remove added columns
            $table->dropColumn(['location_number', 'title', 'position', 'opening_date']);

            // Rename column location_id back to location_name
            $table->renameColumn('location_id', 'location_name');
        });
    }
};
