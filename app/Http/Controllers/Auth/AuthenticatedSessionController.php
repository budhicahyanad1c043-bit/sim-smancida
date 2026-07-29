<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Reset Cache Permission Spatie saat Login
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->load('roles');

        if ($user->hasRole('admin')) {
            return redirect()->intended(route('admin.dashboard'));
        }
        if ($user->hasRole('walikelas')) {
            return redirect()->intended(route('walikelas.dashboard'));
        }
        if ($user->hasRole('guru')) {
            return redirect()->intended(route('guru.dashboard'));
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
