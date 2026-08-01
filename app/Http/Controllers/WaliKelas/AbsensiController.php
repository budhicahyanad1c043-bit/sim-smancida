<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Guru;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Ambil ID Guru dari user yang login
        $guru = $user->guru ?? Guru::where('user_id', $user->id)->first();
        
        // Ambil kelas binaan Wali Kelas
        $kelas = Kelas::where('walikelas_id', $guru->id ?? null)->first();

        // Standardisasi format tanggal Y-m-d
        $tanggalInput = $request->input('tanggal', date('Y-m-d'));
        $tanggal = Carbon::parse($tanggalInput)->format('Y-m-d');

        $siswas = [];
        $existingAbsensi = collect();

        if ($kelas) {
            $siswas = Siswa::where('kelas_id', $kelas->id)
                ->orderBy('nama_siswa', 'asc')
                ->get();

            // Ambil data absensi harian yang tersimpan
            $existingAbsensi = Absensi::where('kelas_id', $kelas->id)
                ->where('tipe', 'harian')
                ->whereDate('tanggal', $tanggal)
                ->get()
                ->keyBy('siswa_id');
        }

        return view('walikelas.absensi.index', compact(
            'kelas', 'tanggal', 'siswas', 'existingAbsensi'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required',
            'tanggal'  => 'required',
            'absensi'  => 'required|array',
        ]);

        $tanggal = Carbon::parse($request->tanggal)->format('Y-m-d');
        $user = auth()->user();
        $guruId = $user->guru->id ?? Guru::where('user_id', $user->id)->value('id');

        foreach ($request->absensi as $siswaId => $status) {
            // Ambil keterangan per siswa secara aman
            $keterangan = $request->keterangan[$siswaId] ?? null;

            Absensi::updateOrCreate(
                [
                    'tanggal'  => $tanggal,
                    'kelas_id' => $request->kelas_id,
                    'siswa_id' => $siswaId,
                    'tipe'     => 'harian',
                ],
                [
                    'guru_id'           => $guruId,
                    'mata_pelajaran_id' => null, // Absensi harian tidak terikat mapel
                    'status'            => $status,
                    'keterangan'        => $keterangan,
                ]
            );
        }

        return redirect()->route('walikelas.absensi.index', [
            'tanggal' => $tanggal,
        ])->with('success', 'Data absensi harian berhasil disimpan!');
    }

    public function exportPdf(Request $request)
    {
        $user = auth()->user();
        $guru = $user->guru ?? Guru::where('user_id', $user->id)->first();
        $kelas = Kelas::where('walikelas_id', $guru->id ?? null)->first();

        if (!$kelas) {
            return redirect()->back()->with('error', 'Anda belum terdaftar sebagai Wali Kelas.');
        }

        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $siswas = Siswa::where('kelas_id', $kelas->id)->get();

        // Rekapitulasi absensi harian per siswa dalam bulan & tahun terpilih
        $rekap = Absensi::where('kelas_id', $kelas->id)
            ->where('tipe', 'harian')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get()
            ->groupBy('siswa_id');

        $pdf = Pdf::loadView('walikelas.absensi.pdf', compact('kelas', 'siswas', 'rekap', 'bulan', 'tahun', 'guru'));
        
        // Set kertas A4 Landscape/Portrait
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream("Laporan_Absensi_Harian_{$kelas->nama_kelas}_{$bulan}_{$tahun}.pdf");
    }

    
}