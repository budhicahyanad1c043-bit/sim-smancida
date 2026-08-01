<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use App\Models\Guru;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AbsensiMapelController extends Controller
{
    public function index(Request $request)
    {
        $kelases = Kelas::all();
        $mapels = MataPelajaran::all();

        $tanggalInput = $request->input('tanggal', date('Y-m-d'));
        $tanggal = Carbon::parse($tanggalInput)->format('Y-m-d');

        $selectedKelas = $request->input('kelas_id');
        $selectedMapel = $request->input('mapel_id');

        $siswas = [];
        $existingAbsensi = collect();

        if ($selectedKelas && $selectedMapel) {
            $siswas = Siswa::where('kelas_id', $selectedKelas)
            ->orderBy('nama_siswa', 'asc')
            ->get();

            // KUNCI PERBAIKAN: Gunakan 'mata_pelajaran_id' sesuai isi Model Absensi
            $existingAbsensi = Absensi::where('kelas_id', $selectedKelas)
                ->where('mata_pelajaran_id', $selectedMapel)
                ->whereDate('tanggal', $tanggal)
                ->get()
                ->keyBy('siswa_id');
        }

        return view('guru.absensi.index', compact(
            'kelases', 'mapels', 'tanggal', 
            'selectedKelas', 'selectedMapel', 
            'siswas', 'existingAbsensi'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required',
            'mapel_id' => 'required',
            'tanggal'  => 'required',
            'absensi'  => 'required|array',
        ]);

        $tanggal = Carbon::parse($request->tanggal)->format('Y-m-d');
        $user = auth()->user();
        $guruId = $user->guru->id ?? Guru::where('user_id', $user->id)->value('id');

        foreach ($request->absensi as $siswaId => $status) {

            $keterangan = $request->keterangan[$siswaId] ?? null;
            // KUNCI PERBAIKAN: Simpan ke kolom 'mata_pelajaran_id'
            Absensi::updateOrCreate(
                [
                    'tanggal'           => $tanggal,
                    'kelas_id'          => $request->kelas_id,
                    'mata_pelajaran_id' => $request->mapel_id,
                    'siswa_id'          => $siswaId,
                ],
                [
                    'guru_id'           => $guruId,
                    'status'            => $status,
                    'tipe'              => 'mapel',
                    'keterangan'        => $keterangan,
                ]
            );
        }

        return redirect()->route('guru.absensi.index', [
            'kelas_id' => $request->kelas_id,
            'mapel_id' => $request->mapel_id,
            'tanggal'  => $tanggal,
        ])->with('success', 'Data absensi mata pelajaran berhasil disimpan!');
    }
}