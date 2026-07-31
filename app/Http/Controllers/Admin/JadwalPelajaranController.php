<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelajaran;
use App\Models\JamPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Guru;
use Illuminate\Http\Request;

class JadwalPelajaranController extends Controller
{
    public function index(Request $request)
    {
        $kelasId = $request->input('kelas_id');
        $kelases = Kelas::all();
        $jamPelajarans = JamPelajaran::orderBy('jam_ke')->get();
        $haris = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        // Ambil jadwal terstruktur berdasarkan Kelas jika dipilih
        $jadwals = [];
        if ($kelasId) {
            $rawJadwal = JadwalPelajaran::with(['mapel', 'guru'])
                ->where('kelas_id', $kelasId)
                ->get();

            foreach ($rawJadwal as $j) {
                $jadwals[$j->jam_pelajaran_id][$j->hari] = $j;
            }
        }

        $mapels = MataPelajaran::all();
        $gurus = Guru::all();

        return view('admin.jadwal.index', compact(
            'kelases', 'jamPelajarans', 'haris', 'jadwals', 'kelasId', 'mapels', 'gurus'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelas_id'         => 'required|exists:kelases,id',
            'jam_pelajaran_id' => 'required|exists:jam_pelajarans,id',
            'hari'             => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'mapel_id'         => 'required|exists:mata_pelajarans,id',
            'guru_id'          => 'nullable|exists:gurus,id',
        ]);

        // Validasi Bentrok Guru: Guru tidak boleh mengajar di 2 kelas berbeda pada hari & jam sama
        if ($request->guru_id) {
            $bentrok = JadwalPelajaran::where('guru_id', $request->guru_id)
                ->where('hari', $request->hari)
                ->where('jam_pelajaran_id', $request->jam_pelajaran_id)
                ->where('kelas_id', '!=', $request->kelas_id)
                ->first();

            if ($bentrok) {
                return redirect()->back()->with('error', 'Gagal! Guru tersebut sudah mengajar di kelas ' . $bentrok->kelas->nama_kelas . ' pada jam dan hari ini.');
            }
        }

        JadwalPelajaran::updateOrCreate(
            [
                'kelas_id'         => $request->kelas_id,
                'jam_pelajaran_id' => $request->jam_pelajaran_id,
                'hari'             => $request->hari,
            ],
            [
                'mapel_id' => $request->mapel_id,
                'guru_id'  => $request->guru_id,
            ]
        );

        return redirect()->back()->with('success', 'Jadwal pelajaran berhasil disimpan!');
    }

    public function destroy(JadwalPelajaran $jadwal)
    {
        $jadwal->delete();
        return redirect()->back()->with('success', 'Jadwal pelajaran berhasil dihapus!');
    }
}