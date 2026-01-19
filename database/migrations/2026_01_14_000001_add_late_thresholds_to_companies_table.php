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
        Schema::table('companies', function (Blueprint $table) {
            $table->time('late_threshold_1')->default('08:30:00')->after('time_out');
            $table->time('late_threshold_2')->default('09:00:00')->after('late_threshold_1');
            $table->time('late_threshold_3')->default('12:00:00')->after('late_threshold_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['late_threshold_1', 'late_threshold_2', 'late_threshold_3']);
        });
    }
};
