<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Minimal session login for the admin. No registration, no password reset.
 * Replaces the AuthenticatesUsers trait of Laravel 5 (hand-written, so there is no package to carry).
 */
class LoginController extends Controller
{
    /**
     * Show the login form (resources/views/auth/login.blade.php).
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle the submitted login form.
     */
    public function login(Request $request): RedirectResponse
    {
        // Validate the form; on failure Laravel redirects back with the errors
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Auth::attempt looks up the user by email, checks the hashed password, and logs the user in
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // New session id after login, to prevent session fixation
            $request->session()->regenerate();

            // Go to the page the user wanted before being sent to the login, else the dashboard
            return redirect()->intended(route('admin.dashboard'));
        }

        // Wrong credentials: back to the form with the email kept and one error message
        return back()->withInput($request->only('email'))->withErrors(['email' => 'These credentials do not match our records.']);
    }

    /**
     * Log the user out and go back to the home page.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        // Drop the session data and renew the CSRF token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
