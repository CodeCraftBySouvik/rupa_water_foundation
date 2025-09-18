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
        Schema::table('inspections', function (Blueprint $table) {
             // Add new enum column 'repairing'
            $table->enum('repairing', ['Floor', 'Machine'])->nullable()->after('checked_date');

            // Rename column 'tap_glass_condition' to 'tap_condition'
            $table->renameColumn('tap_glass_condition', 'tap_condition');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
             // Drop the new column
            $table->dropColumn('repairing');

            // Rename back the column
            $table->renameColumn('tap_condition', 'tap_glass_condition');
        });
    }
};
