<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Guru;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardKSController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $dataMonitoring = $this->getRealtimeData($tanggal);

        return view('kepala-sekolah.dashboard', compact('dataMonitoring', 'tanggal'));
    }

    /**
     * Endpoint API JSON untuk Auto-Refresh Realtime tanpa reload halaman
     */
    public function realtimeData(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $data = $this->getRealtimeData($tanggal);

        return response()->json([
            'status' => 'success',
            'updated_at' => Carbon::now()->translatedFormat('H:i:s WIB'),
            'data' => $data
        ]);
    }

    /**
     * Query untuk mengambil status kehadiran guru di tiap kelas
     */
    private function getRealtimeData($tanggal)
    {
        $kelases = Kelas::orderBy('nama_kelas', 'asc')->get();

        $dataMonitoring = $kelases->map(function ($kelas) use ($tanggal) {
            // Ambil absensi terbaru di kelas ini pada tanggal terpilih
            $absensiTerakhir = Absensi::with(['guru.user', 'mataPelajaran'])
                ->where('kelas_id', $kelas->id)
                ->whereDate('tanggal', $tanggal)
                ->latest('updated_at')
                ->first();

            // Hitung ringkasan status siswa di kelas tersebut (jika guru sudah mengabsen)
            $ringkasanSiswa = null;
            if ($absensiTerakhir) {
                $allAbsensiSesi = Absensi::where('kelas_id', $kelas->id)
                    ->where('mata_pelajaran_id', $absensiTerakhir->mata_pelajaran_id)
                    ->whereDate('tanggal', $tanggal)
                    ->get();

                $ringkasanSiswa = [
                    'hadir' => $allAbsensiSesi->where('status', 'Hadir')->count(),
                    'izin'  => $allAbsensiSesi->where('status', 'Izin')->count(),
                    'sakit' => $allAbsensiSesi->where('status', 'Sakit')->count(),
                    'alpa'  => $allAbsensiSesi->where('status', 'Alpa')->count(),
                    'total' => $allAbsensiSesi->count(),
                ];
            }

            return [
                'kelas_id'      => $kelas->id,
                'nama_kelas'    => $kelas->nama_kelas,
                'sudah_diabsen' => $absensiTerakhir ? true : false,
                'guru_nama'     => $absensiTerakhir->guru->nama_guru ?? ($absensiTerakhir->guru->user->name ?? '-'),
                'mapel_nama'    => $absensiTerakhir->mataPelajaran->nama_mapel ?? '-',
                'waktu_absen'   => $absensiTerakhir ? Carbon::parse($absensiTerakhir->updated_at)->format('H:i') : '-',
                'ringkasan'     => $ringkasanSiswa,
            ];
        });

        // Hitung total statistik ringkas
        $totalKelas = $kelases->count();
        $kelasAktif = $dataMonitoring->where('sudah_diabsen', true)->count();
        $kelasKosong = $totalKelas - $kelasAktif;

        return [
            'total_kelas'  => $totalKelas,
            'kelas_aktif'  => $kelasAktif,
            'kelas_kosong' => $kelasKosong,
            'list_kelas'   => $dataMonitoring,
        ];
    }
}