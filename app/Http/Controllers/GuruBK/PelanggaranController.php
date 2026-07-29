<?php

namespace App\Http\Controllers\GuruBK;

use App\Http\Controllers\Controller;
use App\Models\Pelanggaran;
use App\Models\Siswa;
use Illuminate\Http\Request;

class PelanggaranController extends Controller
{
    public function index()
    {
        $pelanggarans = Pelanggaran::with('siswa')->latest()->paginate(10);
        return view('gurubk.pelanggaran.index', compact('pelanggarans'));
    }

    public function create()
    {
        $siswas = Siswa::all();
        return view('gurubk.pelanggaran.create', compact('siswas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'tanggal' => 'required|date',
            'catatan' => 'required|string',
            'poin_pelanggaran' => 'required|integer|min:1',
        ]);

        Pelanggaran::create($request->all());

        return redirect()->route('gurubk.pelanggaran.index')->with('success', 'Catatan pelanggaran berhasil disimpan.');
    }
}