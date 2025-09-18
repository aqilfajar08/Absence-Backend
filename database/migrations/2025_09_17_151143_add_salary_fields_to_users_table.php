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
            $table->decimal('gaji_pokok', 15, 2)->nullable()->after('fcm_token')->comment('Basic Salary (GP)');
            $table->decimal('tunjangan', 15, 2)->nullable()->after('gaji_pokok')->comment('Allowance (TJ)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['gaji_pokok', 'tunjangan']);
        });
    }
};
