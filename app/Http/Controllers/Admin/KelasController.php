<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        // Eager loading waliKelas dan hitung jumlah siswa per kelas
        // Eager loading waliKelas dan hitung jumlah siswa per kelas
        $kelases = Kelas::with(['waliKelas'])->withCount('siswas')->latest()->paginate(10);

        // AMBIL MODEL GURU yang relasi user-nya memiliki role 'walikelas'
        $gurus = Guru::whereHas('user', function ($query) {
            $query->role('walikelas'); // Menggunakan Spatie Permission
        })->get();

        return view('admin.kelas.index', compact('kelases', 'gurus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kelas'   => 'required|string|max:255|unique:kelases,nama_kelas',
            'tahun_ajaran' => 'required|string|max:20',
            'walikelas_id' => 'nullable|exists:gurus,id',
        ]);

        Kelas::create($validated);

        return redirect()->route('admin.kelas.index')->with('success', 'Data kelas berhasil ditambahkan.');
    }

    public function update(Request $request, Kelas $kela)
    {
        $validated = $request->validate([
            'nama_kelas'   => 'required|string|max:255|unique:kelases,nama_kelas,' . $kela->id,
            'tahun_ajaran' => 'required|string|max:20',
            'walikelas_id' => 'nullable|exists:gurus,id',
        ]);

        $kela->update($validated);

        return redirect()->route('admin.kelas.index')->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kela)
    {
        $kela->delete();

        return redirect()->route('admin.kelas.index')->with('success', 'Data kelas berhasil dihapus.');
    }
}