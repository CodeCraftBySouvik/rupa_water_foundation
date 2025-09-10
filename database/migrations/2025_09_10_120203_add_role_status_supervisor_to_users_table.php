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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['ho', 'supervisor', 'employee', 'complaint'])->default('employee')->after('password');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('role');
            $table->unsignedBigInteger('supervisor_id')->nullable()->after('status');

            $table->foreign('supervisor_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['supervisor_id']);
            $table->dropColumn(['role', 'status', 'supervisor_id']);
        });
    }
};
