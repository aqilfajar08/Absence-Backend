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
        Schema::create('qr_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('generated_by')->constrained('users')->onDelete('cascade')->comment('Security/Admin who generated the QR');
            $table->string('code')->unique()->comment('Unique QR code string');
            $table->date('valid_date')->comment('Date this QR code is valid for');
            $table->time('generated_at_time')->comment('Time when QR was generated');
            $table->timestamp('expires_at')->comment('Expiration timestamp (end of day)');
            $table->boolean('is_active')->default(true)->comment('Whether QR is still active');
            $table->timestamps();
            
            // Index for faster lookups
            $table->index(['code', 'valid_date', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qr_codes');
    }
};
