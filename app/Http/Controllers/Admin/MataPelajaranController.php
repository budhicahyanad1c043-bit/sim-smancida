<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;

class MataPelajaranController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $mapels = MataPelajaran::when($search, function ($query, $search) {
            return $query->where('nama_mapel', 'like', "%{$search}%")
                        ->orWhere('kode_mapel', 'like', "%{$search}%");
        })
        ->orderBy('nama_mapel', 'asc') // Tetap terurut A-Z
        ->paginate(10)
        ->withQueryString(); // Memastikan parameter search tetap terbawa saat ganti halaman pagination

        return view('admin.mapel.index', compact('mapels'));
    }

    public function create()
    {
        return view('admin.mapel.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_mapel' => 'required|string|unique:mata_pelajarans,kode_mapel|max:20',
            'nama_mapel' => 'required|string|max:255',
            'deskripsi'  => 'nullable|string',
        ]);

        MataPelajaran::create($request->all());

        return redirect()->route('admin.mapel.index')->with('success', 'Mata Pelajaran berhasil ditambahkan!');
    }

    public function edit(MataPelajaran $mapel)
    {
        return view('admin.mapel.edit', compact('mapel'));
    }

    public function update(Request $request, MataPelajaran $mapel)
    {
        $request->validate([
            'kode_mapel' => 'required|string|max:20|unique:mata_pelajarans,kode_mapel,' . $mapel->id,
            'nama_mapel' => 'required|string|max:255',
            'deskripsi'  => 'nullable|string',
        ]);

        $mapel->update($request->all());

        return redirect()->route('admin.mapel.index')->with('success', 'Mata Pelajaran berhasil diperbarui!');
    }

    public function destroy(MataPelajaran $mapel)
    {
        $mapel->delete();
        return redirect()->route('admin.mapel.index')->with('success', 'Mata Pelajaran berhasil dihapus!');
    }
}