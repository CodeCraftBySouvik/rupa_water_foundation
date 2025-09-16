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
        Schema::table('employee_zone_assignments', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign('employee_zone_assignments_zone_id_foreign');
             $table->string('zone_id', 255)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_zone_assignments', function (Blueprint $table) {
             $table->unsignedBigInteger('zone_id')->change();
             $table->unsignedBigInteger('zone_id')->change();
        });
    }
};
