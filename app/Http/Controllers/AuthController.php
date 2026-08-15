<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginPage()
    {
        if (Auth::check()) {

            $user = Auth::user();

            return match ($user->role) {

                'admin' => redirect('/admin/dashboard'),

                'supervisor' => redirect('/supervisor/dashboard'),

                'agent' => redirect('/agent/dashboard'),

                default => abort(403)

            };
        }

        return view('auth.login');
    }

    public function handleLogin(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt([
            'email' => $validated['email'],
            'password' => $validated['password'],
        ])) {

            return back()->withErrors([
                'email' => 'Invalid credentials.',
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        return match ($user->role) {

            'admin' => redirect('/admin/dashboard'),

            'supervisor' => redirect('/supervisor/dashboard'),

            'agent' => redirect('/agent/dashboard'),

            default => abort(403)

        };
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/login');
    }
}
