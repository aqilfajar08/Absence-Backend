<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QrCode;
use Carbon\Carbon;
use Illuminate\Http\Request;

class QrCodeController extends Controller
{
    /**
     * Generate new QR code for today (Security/Admin only)
     */
    public function generate(Request $request)
    {
        $user = $request->user();

        // Check if user has permission (you can add role check here)
        // Example: if (!$user->hasRole('security') && !$user->hasRole('admin')) { ... }

        // Deactivate any existing QR codes for today
        QrCode::where('valid_date', Carbon::today())
            ->where('is_active', true)
            ->update(['is_active' => false]);

        // Generate new QR code
        $code = QrCode::generateUniqueCode();
        
        $qrCode = QrCode::create([
            'generated_by' => $user->id,
            'code' => $code,
            'valid_date' => Carbon::today(),
            'generated_at_time' => Carbon::now()->format('H:i:s'),
            'expires_at' => Carbon::today()->endOfDay(),
            'is_active' => true,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'QR code generated successfully',
            'data' => [
                'qr_code' => $qrCode->code,
                'valid_date' => $qrCode->valid_date->format('Y-m-d'),
                'expires_at' => $qrCode->expires_at->format('Y-m-d H:i:s'),
                'generated_by' => $user->name,
            ]
        ], 201);
    }

    /**
     * Validate QR code (for employee scanning)
     */
    public function validate(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        $qrCode = QrCode::where('code', $request->qr_code)->first();

        if (!$qrCode) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid QR code',
            ], 404);
        }

        if (!$qrCode->isValid()) {
            return response()->json([
                'status' => 'error',
                'message' => 'QR code has expired or is no longer active',
                'data' => [
                    'valid_date' => $qrCode->valid_date->format('Y-m-d'),
                    'expires_at' => $qrCode->expires_at->format('Y-m-d H:i:s'),
                    'is_active' => $qrCode->is_active,
                ]
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'QR code is valid',
            'data' => [
                'qr_code' => $qrCode->code,
                'valid_date' => $qrCode->valid_date->format('Y-m-d'),
                'generated_by' => $qrCode->generatedBy->name,
            ]
        ], 200);
    }

    /**
     * Get current active QR code
     */
    public function getCurrent(Request $request)
    {
        $qrCode = QrCode::where('valid_date', Carbon::today())
            ->where('is_active', true)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$qrCode) {
            return response()->json([
                'status' => 'error',
                'message' => 'No active QR code for today',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'qr_code' => $qrCode->code,
                'valid_date' => $qrCode->valid_date->format('Y-m-d'),
                'expires_at' => $qrCode->expires_at->format('Y-m-d H:i:s'),
                'generated_by' => $qrCode->generatedBy->name,
                'generated_at' => $qrCode->created_at->format('Y-m-d H:i:s'),
            ]
        ], 200);
    }

    /**
     * Deactivate current QR code (Security/Admin only)
     */
    public function deactivate(Request $request)
    {
        $qrCode = QrCode::where('valid_date', Carbon::today())
            ->where('is_active', true)
            ->first();

        if (!$qrCode) {
            return response()->json([
                'status' => 'error',
                'message' => 'No active QR code to deactivate',
            ], 404);
        }

        $qrCode->deactivate();

        return response()->json([
            'status' => 'success',
            'message' => 'QR code deactivated successfully',
        ], 200);
    }
}
