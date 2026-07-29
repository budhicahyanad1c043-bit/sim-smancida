<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GuruController extends Controller
{
    public function index()
    {
        $gurus = Guru::with('user')->latest()->paginate(10);
        return view('admin.guru.index', compact('gurus'));
    }

    public function create()
    {
        return view('admin.guru.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'nip' => 'nullable|string|unique:gurus,nip',
            'gender' => 'required|in:L,P',
            'alamat' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make('password123'),
            ]);

            $user->syncRoles(['guru']);

            Guru::create([
                'user_id' => $user->id,
                'nama_guru' => $request->name,
                'nip' => $request->nip,
                'gender' => $request->gender,
                'alamat' => $request->alamat,
            ]);
        });

        return redirect()->route('admin.guru.index')->with('success', 'Data Guru berhasil ditambahkan!');
    }

    public function edit(Guru $guru)
    {
        return view('admin.guru.edit', compact('guru'));
    }

    public function update(Request $request, Guru $guru)
    {
        $request->validate([
            'nama_guru' => 'required|string|max:255',
            'nip' => 'nullable|string|unique:gurus,nip,' . $guru->id,
            'gender' => 'required|in:L,P',
        ]);

        $guru->update([
            'nama_guru' => $request->nama_guru,
            'nip' => $request->nip,
            'gender' => $request->gender,
            'alamat' => $request->alamat,
        ]);

        if ($guru->user) {
            $guru->user->update(['name' => $request->nama_guru]);
        }

        return redirect()->route('admin.guru.index')->with('success', 'Data Guru berhasil diperbarui!');
    }

    public function destroy(Guru $guru)
    {
        DB::transaction(function () use ($guru) {
            if ($guru->user) {
                $guru->user->delete();
            }
            $guru->delete();
        });

        return redirect()->route('admin.guru.index')->with('success', 'Data Guru berhasil dihapus!');
    }
}