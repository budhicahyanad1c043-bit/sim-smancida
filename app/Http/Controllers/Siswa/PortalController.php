<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use Illuminate\Support\Facades\Auth;

class PortalController extends Controller
{
    public function dashboard()
    {
        $siswa = Auth::user()->siswa;

        if (!$siswa) {
            return view('dashboard.siswa')->with('error', 'Data profil siswa tidak ditemukan.');
        }

        // Rekapitulasi Status Kehadiran Harian
        $summary = Absensi::where('siswa_id', $siswa->id)
            ->where('tipe', 'harian')
            ->selectRaw("
                SUM(CASE WHEN status = 'Hadir' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN status = 'Izin' THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN status = 'Sakit' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN status = 'Alpa' THEN 1 ELSE 0 END) as alpa
            ")
            ->first();

        // Riwayat 10 Absensi Harian Terakhir
        $recentAbsensi = Absensi::where('siswa_id', $siswa->id)
            ->where('tipe', 'harian')
            ->latest('tanggal')
            ->take(10)
            ->get();

        return view('dashboard.siswa', compact('siswa', 'summary', 'recentAbsensi'));
    }
}