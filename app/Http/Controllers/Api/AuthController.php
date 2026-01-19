<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request) {
        \Illuminate\Support\Facades\Log::info('Login Hit for ' . $request->email);
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

        $user->load('roles');

        return response()->json([
            'message' => 'successful',
            'access_token' => $token,
            'user' => $user 
        ]);
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout Successful'
        ]);
    }

    public function registerFace(Request $request) {
        $request->validate([
            'face_embedded' => 'required|string',
        ]);

        $user = $request->user();
        $user->face_embedded = $request->face_embedded;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Face data registered successfully',
            'user' => $user
        ]);
    }

    // update fcm token
    public function updateFcmToken(Request $request) {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $user = $request->user();
        $user->fcm_token = $request->fcm_token;
        $user->save();

        return response()->json([
            'message' => 'Fcm token updated successfully',
        ], 200);
    }

    // upload profile image
    public function uploadProfileImage(Request $request) {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
        ]);

        $user = $request->user();

        // Delete old image if exists
        if ($user->image_url) {
            $oldImagePath = str_replace('/storage/', '', $user->image_url);
            if (Storage::disk('public')->exists($oldImagePath)) {
                Storage::disk('public')->delete($oldImagePath);
            }
        }

        // Store new image
        $image = $request->file('image');
        $imageName = 'profile_' . $user->id . '_' . time() . '.' . $image->getClientOriginalExtension();
        $imagePath = $image->storeAs('profile_images', $imageName, 'public');

        // Update user's image_url
        $user->image_url = '/storage/' . $imagePath;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Profile image uploaded successfully',
            'image_url' => $user->image_url,
            'user' => $user
        ], 200);
    }

    // get user profile
    public function getProfile(Request $request) {
        $user = $request->user();
        $user->load('roles');
        
        return response()->json([
            'status' => 'success',
            'user' => $user
        ], 200);
    }

    // delete profile image
    public function deleteProfileImage(Request $request) {
        $user = $request->user();

        if ($user->image_url) {
            $imagePath = str_replace('/storage/', '', $user->image_url);
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            
            $user->image_url = null;
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Profile image deleted successfully',
                'user' => $user
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'No profile image found'
        ], 404);
    }
}
