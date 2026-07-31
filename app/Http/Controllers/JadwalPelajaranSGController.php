<?php

namespace App\Http\Controllers;

use App\Models\JadwalPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalPelajaranSGController extends Controller
{
    // Untuk View Guru
    public function indexGuru()
    {
        // Mendapatkan ID guru dari user yang sedang login
        $guruId = Auth::user()->guru->id; 

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        $jadwal = JadwalPelajaran::with(['kelas', 'mapel', 'jamPelajaran'])
            ->where('guru_id', $guruId)
            ->get()
            ->groupBy('hari');

        return view('guru.jadwal-pelajaran.index', compact('jadwal', 'hariList'));
    }

    // Untuk View Siswa
    public function indexSiswa()
    {
        // Mendapatkan ID kelas dari siswa yang sedang login
        $kelasId = Auth::user()->siswa->kelas_id; 

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        $jadwal = JadwalPelajaran::with(['mapel', 'guru', 'jamPelajaran'])
            ->where('kelas_id', $kelasId)
            ->get()
            ->groupBy('hari');

        return view('siswa.jadwal-pelajaran.index', compact('jadwal', 'hariList'));
    }
}