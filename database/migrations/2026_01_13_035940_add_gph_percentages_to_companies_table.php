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
            $table->integer('gph_late_1_percent')->default(75)->after('late_fee_interval_minutes');
            $table->integer('gph_late_2_percent')->default(70)->after('gph_late_1_percent');
            $table->integer('gph_late_3_percent')->default(65)->after('gph_late_2_percent');
            $table->integer('gph_late_4_percent')->default(0)->after('gph_late_3_percent'); // Setengah Hari
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'gph_late_1_percent',
                'gph_late_2_percent',
                'gph_late_3_percent',
                'gph_late_4_percent',
            ]);
        });
    }
};
