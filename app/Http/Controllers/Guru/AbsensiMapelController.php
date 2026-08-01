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
use Illuminate\Support\Facades\DB;

class AbsensiMapelController extends Controller
{
    public function index(Request $request)
    {
        $kelases = Kelas::all();
        $mapels = MataPelajaran::orderBy('nama_mapel', 'asc')->get();
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

    public function rekap(Request $request)
    {
        $request->validate([
            'tanggal_mulai'   => 'required',
            'tanggal_selesai' => 'required',
            'kelas_id'        => 'required',
            'mapel_id'        => 'required',
        ]);

        // 1. Pastikan Format Tanggal Sesuai (YYYY-MM-DD)
        $tglMulai   = Carbon::parse($request->tanggal_mulai)->format('Y-m-d');
        $tglSelesai = Carbon::parse($request->tanggal_selesai)->format('Y-m-d');

        // 2. Ambil data Kelas dan Mata Pelajaran
        $kelas = Kelas::find($request->kelas_id);
        $mapel = MataPelajaran::find($request->mapel_id);

        // 3. Ambil daftar Siswa
        $siswas = Siswa::where('kelas_id', $request->kelas_id)
            ->orderBy('nama_siswa', 'asc')
            ->get();

        // 4. Query Rekapitulasi (PERBAIKAN: Gunakan 'mata_pelajaran_id')
        $rekapRaw = Absensi::where('kelas_id', $request->kelas_id)
            ->where('mata_pelajaran_id', $request->mapel_id)
            ->whereBetween('tanggal', [$tglMulai, $tglSelesai])
            ->get();

        // Grouping & Counting secara manual per siswa
        $rekap = [];
        foreach ($siswas as $siswa) {
            $absensiSiswa = $rekapRaw->where('siswa_id', $siswa->id);
            
            $rekap[$siswa->id] = (object) [
                'total_hadir' => $absensiSiswa->filter(fn($a) => strtolower($a->status) === 'hadir')->count(),
                'total_izin'  => $absensiSiswa->filter(fn($a) => strtolower($a->status) === 'izin')->count(),
                'total_sakit' => $absensiSiswa->filter(fn($a) => strtolower($a->status) === 'sakit')->count(),
                'total_alpa'  => $absensiSiswa->filter(fn($a) => strtolower($a->status) === 'alpa')->count(),
            ];
        }

        return view('guru.absensi.rekap-pdf', compact('siswas', 'rekap', 'kelas', 'mapel', 'request', 'tglMulai', 'tglSelesai'));
    }

}