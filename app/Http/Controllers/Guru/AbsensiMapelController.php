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
        // 1. Validasi Input Dasar + GPS & Device ID
        $request->validate([
            'kelas_id'  => 'required',
            'mapel_id'  => 'required',
            'tanggal'   => 'required',
            'absensi'   => 'required|array',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'device_id' => 'required|string',
        ], [
            'latitude.required'  => 'Gagal! Lokasi GPS tidak terdeteksi.',
            'longitude.required' => 'Gagal! Lokasi GPS tidak terdeteksi.',
            'device_id.required' => 'Gagal! Identitas perangkat tidak valid.',
        ]);

        $user = auth()->user();

        // -------------------------------------------------------------
        // VALIDASI 1: LOCK DEVICE (1 Perangkat untuk 1 Guru)
        // -------------------------------------------------------------
        $incomingDeviceId = $request->input('device_id');

        // Jika device_token di DB masih kosong, kunci ke perangkat saat ini
        if (!$user->device_token) {
            $user->forceFill(['device_token' => $incomingDeviceId])->save();
        } 
        // Jika berbeda dengan device_token yang terdaftar
        else if ($user->device_token !== $incomingDeviceId) {
            return redirect()->back()->with('error', 'Gagal Absen! Akun Anda sudah terdaftar di HP/Laptop lain. Harap gunakan perangkat utama Anda.');
        }

        // -------------------------------------------------------------
        // VALIDASI 2: GEOFENCING (Batas Jarak GPS Sekolah)
        // -------------------------------------------------------------

        $lokasi = \App\Models\Pengaturan::first();

        // Koordinat Sekolah & Radius Toleransi (Bisa diatur via .env)
        // $sekolahLat = env('SEKOLAH_LATITUDE', -6.200000); 
        // $sekolahLng = env('SEKOLAH_LONGITUDE', 106.816666);
        // $maxRadius  = env('SEKOLAH_RADIUS_METER', 50); // Maksimal 50 meter
        // Ambil setting lokasi dari tabel pengaturans
        $sekolahLat = (float) (\App\Models\Pengaturan::where('key', 'latitude')->value('value') ?? -6.8700621);
        $sekolahLng = (float) (\App\Models\Pengaturan::where('key', 'longitude')->value('value') ?? 106.7723601);
        $maxRadius  = (float) (\App\Models\Pengaturan::where('key', 'radius')->value('value') ?? 100);

        $jarak = $this->hitungJarakHaversine(
            $request->latitude, 
            $request->longitude, 
            $sekolahLat, 
            $sekolahLng
        );

        if ($jarak > $maxRadius) {
            return redirect()->back()->with('error', "Gagal Absen! Anda berada di luar area sekolah/kelas. Jarak Anda: " . round($jarak) . " meter dari lokasi sekolah.");
        }

        // -------------------------------------------------------------
        // PROSES SIMPAN ABSENSI
        // -------------------------------------------------------------
        $tanggal = Carbon::parse($request->tanggal)->format('Y-m-d');
        $guruId = $user->guru->id ?? Guru::where('user_id', $user->id)->value('id');

        foreach ($request->absensi as $siswaId => $status) {
            $keterangan = $request->keterangan[$siswaId] ?? null;

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
        ])->with('success', 'Data absensi berhasil disimpan (Lokasi & Perangkat Terverifikasi)!');
    }

    public function rekap(Request $request)
    {
        $request->validate([
            'tanggal_mulai'   => 'required',
            'tanggal_selesai' => 'required',
            'kelas_id'        => 'required',
            'mapel_id'        => 'required',
        ]);

        $tglMulai   = Carbon::parse($request->tanggal_mulai)->format('Y-m-d');
        $tglSelesai = Carbon::parse($request->tanggal_selesai)->format('Y-m-d');

        $kelas = Kelas::find($request->kelas_id);
        $mapel = MataPelajaran::find($request->mapel_id);

        $siswas = Siswa::where('kelas_id', $request->kelas_id)
            ->orderBy('nama_siswa', 'asc')
            ->get();

        $rekapRaw = Absensi::where('kelas_id', $request->kelas_id)
            ->where('mata_pelajaran_id', $request->mapel_id)
            ->whereBetween('tanggal', [$tglMulai, $tglSelesai])
            ->get();

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

    public function reset(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required',
            'mapel_id' => 'required',
            'tanggal'  => 'required|date',
        ]);

        $tanggal = Carbon::parse($request->tanggal)->format('Y-m-d');

        $deleted = Absensi::where('kelas_id', $request->kelas_id)
            ->where('mata_pelajaran_id', $request->mapel_id)
            ->whereDate('tanggal', $tanggal)
            ->delete();

        if ($deleted) {
            return redirect()->route('guru.absensi.index', [
                'kelas_id' => $request->kelas_id,
                'mapel_id' => $request->mapel_id,
                'tanggal'  => $tanggal,
            ])->with('success', 'Data absensi untuk tanggal ' . Carbon::parse($tanggal)->translatedFormat('d F Y') . ' berhasil direset!');
        }

        return redirect()->back()->with('error', 'Tidak ada data absensi yang ditemukan untuk direset.');
    }

    /**
     * Rumus Haversine untuk Menghitung Jarak GPS (Meter)
     */
    private function hitungJarakHaversine($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Radius Bumi dalam satuan Meter

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}