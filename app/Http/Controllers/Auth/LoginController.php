<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Process the login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // 1️⃣ Validate input
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        $credentials = $request->only('email', 'password');
        $remember    = $request->filled('remember');

        // 2️⃣ Attempt authentication, passing $remember
        if (! Auth::attempt($credentials, $remember)) {
            // Gagal login – kirim kembali error ke view
            throw ValidationException::withMessages([
                'email' => ['Kredensial tidak cocok.'],
            ]);
        }

        // 3️⃣ Regenerasi session ID (keamanan)
        $request->session()->regenerate();

        // 4️⃣ Redirect ke dashboard (atau halaman yang diminta)
        return redirect()->intended(route('home'));
    }
}