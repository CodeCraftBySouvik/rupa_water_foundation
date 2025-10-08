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
            $table->enum('repairing', ['Floor', 'Machine'])->nullable()->after('checked_date');
        });

        // Rename only if old column exists
        if (Schema::hasColumn('inspections', 'tap_glass_condition')) {
            Schema::table('inspections', function (Blueprint $table) {
                $table->renameColumn('tap_glass_condition', 'tap_condition');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            $table->dropColumn('repairing');
        });

        if (Schema::hasColumn('inspections', 'tap_condition')) {
            Schema::table('inspections', function (Blueprint $table) {
                $table->renameColumn('tap_condition', 'tap_glass_condition');
            });
        }
    }
};
