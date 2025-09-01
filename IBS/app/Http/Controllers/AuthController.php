<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        // If already signed in, send to correct home
        if (Auth::check()) {
            return redirect()->route(
                (Auth::user()->role ?? 'client') === 'admin' ? 'dashboard' : 'client.home'
            );
        }
        // default client context
        return view('auth.login', ['context' => 'client']);
    }

    // NEW: show the admin login page (same view, different context)
    public function showAdminLogin()
    {
        if (Auth::check()) {
            return redirect()->route(
                (Auth::user()->role ?? 'client') === 'admin' ? 'dashboard' : 'client.home'
            );
        }
        return view('auth.login', ['context' => 'admin']);
    }

    public function login(Request $request)
    {
        $context = $request->input('context', 'client');

        $creds = $request->validate([
            'email' => ['required','email'],
            'password' => ['required','string'],
        ]);

        if (Auth::attempt($creds, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $role = Auth::user()->role ?? 'client';

            // if user came from the Admin login page but isn't an admin, block politely
            if ($context === 'admin' && $role !== 'admin') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()
                    ->withErrors(['email' => 'This page is for admins only. Please use the client login.'])
                    ->onlyInput('email');
            }

            if ($role === 'admin') {
                return redirect()->intended(route('dashboard'));
            }

            // clients: ignore any stored intended admin URL
            $request->session()->forget('url.intended');
            return redirect()->route('client.home');
        }

        return back()->withErrors(['email' => 'Invalid credentials'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'You have been logged out.');
    }
}
