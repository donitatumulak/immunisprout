<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Show the Login Form
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle Login Request
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Attempt to log the user in
        if (Auth::attempt($credentials, $request->remember)) {
            
            // 1. Update the last_login timestamp
            $user = Auth::user();
            $user->update([
                'last_login' => now()
            ]);

            // 2. Regenerate session to prevent session fixation attacks
            $request->session()->regenerate();

            return redirect()->intended('dashboard')
                             ->with('success', 'Logged in successfully! Welcome back, ' . $user->username . '!');
        }

        // If login fails
        return back()->withErrors([
            'login_error' => 'Invalid username or password. Please try again.'
        ])->withInput();
    }

    /**
     * Handle Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Logged out successfully.');
    }
}