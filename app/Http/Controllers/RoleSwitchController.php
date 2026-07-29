<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoleSwitchController extends Controller
{
    public function switch(Request $request)
    {
        $request->validate([
            'role' => 'required|string',
        ]);

        $user = auth()->user();

        // Pastikan user memang memiliki role tersebut
        if ($user->hasRole($request->role)) {
            session(['active_role' => $request->role]);

            // Redirect ke dashboard sesuai role yang dipilih
            return match ($request->role) {
                'admin'     => redirect()->route('admin.users.index'),
                'walikelas' => redirect()->route('walikelas.dashboard'),
                'guru'      => redirect()->route('guru.dashboard'),
                default     => redirect()->route('dashboard'),
            };
        }

        return back()->with('error', 'Anda tidak memiliki akses ke role tersebut.');
    }
}