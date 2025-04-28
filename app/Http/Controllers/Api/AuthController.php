<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);
                
        $user = User::where('email', $request->email)->first();

        if(!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        if(!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'wrong password',
            ], 401);
        }

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'message' => 'successful',
            'access_token' => $token,
            'user' => $user 
        ]);
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete;

        return response()->json([
            'message' => 'Logout Successful'
        ]);
    }
}
