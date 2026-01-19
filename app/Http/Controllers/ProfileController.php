<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function index()
    {
        return view('pages.profile.index');
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'image.image' => 'File harus berupa gambar valid.',
            'image.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
            'image.uploaded' => 'Gagal mengupload gambar. Ukuran mungkin terlalu besar.'
        ]);

        // Handle image upload (Priority: Cropped > File)
        if ($request->filled('cropped_image')) {
            try {
                // Delete old image
                if ($user->image_url && Storage::disk('public')->exists($user->image_url)) {
                    Storage::disk('public')->delete($user->image_url);
                }

                // Process Base64
                $image_parts = explode(";base64,", $request->input('cropped_image'));
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1] ?? 'png'; // default to png if unknown
                $image_base64 = base64_decode($image_parts[1]);

                $filename = 'profile_' . $user->id . '_' . time() . '.' . $image_type;
                $path = 'profile_images/' . $filename;
                
                Storage::disk('public')->put($path, $image_base64);
                $user->image_url = $path;
                
            } catch (\Exception $e) {
                return redirect()->route('profile.index')->with('error', 'Gagal memproses foto crop: ' . $e->getMessage());
            }
        } elseif ($request->hasFile('image')) {
            try {
                // Delete old image if exists
                if ($user->image_url && Storage::disk('public')->exists($user->image_url)) {
                    Storage::disk('public')->delete($user->image_url);
                }

                // Store new image with custom name
                $file = $request->file('image');
                $filename = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('profile_images', $filename, 'public');
                
                // Update path
                $user->image_url = $path;
            } catch (\Exception $e) {
                return redirect()->route('profile.index')->with('error', 'Gagal mengupload foto: ' . $e->getMessage());
            }
        }

        $user->name = $request->name;
        $user->save();

        return redirect()->route('profile.index')->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password baru minimal 8 karakter.',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('profile.index')->with('success', 'Password berhasil diubah!');
    }
}
