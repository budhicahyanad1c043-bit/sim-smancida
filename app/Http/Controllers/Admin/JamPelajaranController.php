<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JamPelajaran; // Import Model JamPelajaran
use Illuminate\Http\Request;

class JamPelajaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mengambil semua data jam pelajaran diurutkan berdasarkan jam_ke
        $jamPelajarans = JamPelajaran::orderBy('jam_ke', 'asc')->paginate(10);
        return view('admin.jam-pelajaran.index', compact('jamPelajarans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Karena menggunakan Modal di halaman index, method ini bisa diabaikan
        return redirect()->route('admin.jam-pelajaran.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi Input
        $validated = $request->validate([
            'jam_ke'      => 'required|numeric|unique:jam_pelajarans,jam_ke',
            'nama'        => 'nullable|string|max:255',
            'jam_mulai'   => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
        ], [
            'jam_ke.required'     => 'Jam ke- wajib diisi.',
            'jam_ke.unique'       => 'Jam ke- tersebut sudah ada.',
            'jam_mulai.required'  => 'Jam mulai wajib diisi.',
            'jam_selesai.required'=> 'Jam selesai wajib diisi.',
            'jam_selesai.after'   => 'Jam selesai harus setelah jam mulai.',
        ]);

        // Simpan Data
        JamPelajaran::create($validated);

        return redirect()->route('admin.jam-pelajaran.index')
                         ->with('success', 'Jam pelajaran berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Jika butuh response JSON untuk kebutuhan AJAX/Modal:
        $jamPelajaran = JamPelajaran::findOrFail($id);
        return response()->json($jamPelajaran);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $jamPelajaran = JamPelajaran::findOrFail($id);

        // Validasi Input (Unique diabaikan untuk ID yang sedang di-edit)
        $validated = $request->validate([
            'jam_ke'      => 'required|numeric|unique:jam_pelajarans,jam_ke,' . $jamPelajaran->id,
            'nama'        => 'nullable|string|max:255',
            'jam_mulai'   => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
        ], [
            'jam_ke.required'     => 'Jam ke- wajib diisi.',
            'jam_ke.unique'       => 'Jam ke- tersebut sudah ada.',
            'jam_mulai.required'  => 'Jam mulai wajib diisi.',
            'jam_selesai.required'=> 'Jam selesai wajib diisi.',
            'jam_selesai.after'   => 'Jam selesai harus setelah jam mulai.',
        ]);

        // Update Data
        $jamPelajaran->update($validated);

        return redirect()->route('admin.jam-pelajaran.index')
                         ->with('success', 'Jam pelajaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $jamPelajaran = JamPelajaran::findOrFail($id);
        $jamPelajaran->delete();

        return redirect()->route('admin.jam-pelajaran.index')
                         ->with('success', 'Jam pelajaran berhasil dihapus.');
    }
}