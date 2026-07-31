<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JamPelajaran;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JamPelajaranController extends Controller
{
    public function index(Request $request)
    {
        $hariFilter = $request->get('hari', 'Lainnya');

        $jamPelajarans = JamPelajaran::where('hari', $hariFilter)
            ->orderBy('jam_ke', 'asc')
            ->get();

        return view('admin.jam-pelajaran.index', compact('jamPelajarans', 'hariFilter'));
    }

    // --- METHOD STORE YANG HILANG/DI-PANGGIL OLEH ROUTE ---
    public function store(Request $request)
    {
        $validated = $request->validate([
            'hari'        => 'required|in:Lainnya,Jumat',
            'jam_ke'      => [
                'required',
                'numeric',
                Rule::unique('jam_pelajarans')->where(function ($query) use ($request) {
                    return $query->where('hari', $request->hari);
                }),
            ],
            'nama'        => 'nullable|string|max:255',
            'jam_mulai'   => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
        ], [
            'jam_ke.required'     => 'Jam ke- wajib diisi.',
            'jam_ke.unique'       => 'Jam ke- untuk hari/kategori tersebut sudah ada.',
            'jam_mulai.required'  => 'Jam mulai wajib diisi.',
            'jam_selesai.required'=> 'Jam selesai wajib diisi.',
            'jam_selesai.after'   => 'Jam selesai harus setelah jam mulai.',
        ]);

        JamPelajaran::create($validated);

        return redirect()->route('admin.jam-pelajaran.index', ['hari' => $request->hari])
                         ->with('success', 'Jam pelajaran berhasil ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $jamPelajaran = JamPelajaran::findOrFail($id);

        $validated = $request->validate([
            'hari'        => 'required|in:Lainnya,Jumat',
            'jam_ke'      => [
                'required',
                'numeric',
                Rule::unique('jam_pelajarans')->ignore($jamPelajaran->id)->where(function ($query) use ($request) {
                    return $query->where('hari', $request->hari);
                }),
            ],
            'nama'        => 'nullable|string|max:255',
            'jam_mulai'   => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
        ], [
            'jam_ke.required'     => 'Jam ke- wajib diisi.',
            'jam_ke.unique'       => 'Jam ke- untuk hari/kategori tersebut sudah ada.',
            'jam_mulai.required'  => 'Jam mulai wajib diisi.',
            'jam_selesai.required'=> 'Jam selesai wajib diisi.',
            'jam_selesai.after'   => 'Jam selesai harus setelah jam mulai.',
        ]);

        $jamPelajaran->update($validated);

        return redirect()->route('admin.jam-pelajaran.index', ['hari' => $request->hari])
                         ->with('success', 'Jam pelajaran berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $jamPelajaran = JamPelajaran::findOrFail($id);
        $hari = $jamPelajaran->hari;
        $jamPelajaran->delete();

        return redirect()->route('admin.jam-pelajaran.index', ['hari' => $hari])
                         ->with('success', 'Jam pelajaran berhasil dihapus.');
    }
}