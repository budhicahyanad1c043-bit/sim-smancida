<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Rap2hpoutre\FastExcel\FastExcel;

class GuruController extends Controller
{
    public function index(Request $request)
    {
        $query = Guru::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_guru', 'like', "%{$search}%")
                ->orWhere('nip', 'like', "%{$search}%")
                ->orWhere('nik', 'like', "%{$search}%")
                ->orWhere('nuptk', 'like', "%{$search}%")
                ->orWhereHas('user', function ($u) use ($search) {
                    $u->where('email', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            });
        }

        $gurus = $query->latest()->paginate(10)->withQueryString();

        // Jika dipanggil via AJAX/Fetch, kirim komponen tabel saja
        if ($request->ajax()) {
            return view('admin.guru.partials.table', compact('gurus'))->render();
        }

        return view('admin.guru.index', compact('gurus'));
    }

    public function create()
    {
        return view('admin.guru.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'nip' => 'nullable|string|max:18|unique:gurus,nip',
            'nik' => 'nullable|string|max:16|unique:gurus,nik',
            'nuptk' => 'nullable|string|max:16|unique:gurus,nuptk',
            'tempat_lahir' => 'nullable|string|max:255',
            'tgl_lahir' => 'nullable|date',
            'gender' => 'required|in:L,P',
            'alamat' => 'nullable|string',
            'desa' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kabupaten' => 'nullable|string|max:255',
            'provinsi' => 'nullable|string|max:255',
            'kode_pos' => 'nullable|string|max:10',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make('password123'),
            ]);

            if (method_exists($user, 'syncRoles')) {
                $user->syncRoles(['guru']);
            }

            Guru::create([
                'user_id' => $user->id,
                'nama_guru' => $request->name,
                'nip' => $request->nip,
                'nik' => $request->nik,
                'nuptk' => $request->nuptk,
                'tempat_lahir' => $request->tempat_lahir,
                'tgl_lahir' => $request->tgl_lahir,
                'gender' => $request->gender,
                'alamat' => $request->alamat,
                'desa' => $request->desa,
                'kecamatan' => $request->kecamatan,
                'kabupaten' => $request->kabupaten,
                'provinsi' => $request->provinsi,
                'kode_pos' => $request->kode_pos,
            ]);
        });

        return redirect()->route('admin.guru.index')->with('success', 'Data Guru berhasil ditambahkan!');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            $importedCount = 0;

            DB::transaction(function () use ($request, &$importedCount) {
                (new FastExcel)->import($request->file('file'), function ($line) use (&$importedCount) {
                    // Normalisasi key/header dari Excel ke huruf kecil tanpa spasi/underscore
                    $normalizedLine = [];
                    foreach ($line as $key => $value) {
                        $cleanKey = strtolower(str_replace([' ', '_', '-'], '', trim($key)));
                        $normalizedLine[$cleanKey] = is_string($value) ? trim($value) : $value;
                    }

                    // Ambil nilai berdasarkan nama header yang paling sering digunakan
                    $nama = $normalizedLine['nama'] ?? $normalizedLine['namaguru'] ?? $normalizedLine['name'] ?? null;
                    $email = $normalizedLine['email'] ?? $normalizedLine['mail'] ?? null;
                    $nip = $normalizedLine['nip'] ?? null;
                    $nik = $normalizedLine['nik'] ?? null;
                    $nuptk = $normalizedLine['nuptk'] ?? null;
                    $tempatLahir = $normalizedLine['tempatlahir'] ?? null;
                    $tglLahir = $normalizedLine['tgllahir'] ?? $normalizedLine['tanggallahir'] ?? null;
                    $gender = strtoupper((string)($normalizedLine['gender'] ?? $normalizedLine['jeniskelamin'] ?? 'L'));
                    $alamat = $normalizedLine['alamat'] ?? null;
                    $desa = $normalizedLine['desa'] ?? $normalizedLine['kelurahan'] ?? null;
                    $kecamatan = $normalizedLine['kecamatan'] ?? null;
                    $kabupaten = $normalizedLine['kabupaten'] ?? $normalizedLine['kota'] ?? null;
                    $provinsi = $normalizedLine['provinsi'] ?? null;
                    $kodePos = $normalizedLine['kodepos'] ?? null;

                    // Lewati jika nama atau email tidak ditemukan di baris ini
                    if (!$nama || !$email) {
                        return null;
                    }

                    // Format Jenis Kelamin (Ambil huruf depan L / P)
                    $genderFormatted = in_array(substr($gender, 0, 1), ['L', 'P']) ? substr($gender, 0, 1) : 'L';

                    // Format Tanggal Lahir (Jika dari format Excel/Carbon)
                    if ($tglLahir instanceof \DateTimeInterface) {
                        $tglLahir = $tglLahir->format('Y-m-d');
                    }

                    // 1. Buat atau cari User
                    $user = User::firstOrCreate(
                        ['email' => $email],
                        [
                            'name' => $nama,
                            'password' => Hash::make('password123'),
                        ]
                    );

                    if (method_exists($user, 'assignRole')) {
                        $user->assignRole('guru');
                    }

                    // 2. Buat atau update Data Guru
                    Guru::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'nama_guru' => $nama,
                            'nip' => $nip,
                            'nik' => $nik,
                            'nuptk' => $nuptk,
                            'tempat_lahir' => $tempatLahir,
                            'tgl_lahir' => $tglLahir,
                            'gender' => $genderFormatted,
                            'alamat' => $alamat,
                            'desa' => $desa,
                            'kecamatan' => $kecamatan,
                            'kabupaten' => $kabupaten,
                            'provinsi' => $provinsi,
                            'kode_pos' => $kodePos,
                        ]
                    );

                    $importedCount++;
                });
            });

            if ($importedCount === 0) {
                return redirect()->back()->with('error', 'Gagal mengimpor: Header file Excel tidak sesuai. Pastikan ada kolom "Nama" dan "Email".');
            }

            return redirect()->route('admin.guru.index')->with('success', "Berhasil mengimpor {$importedCount} data guru dari Excel!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimpor file: ' . $e->getMessage());
        }
    }

    public function edit(Guru $guru)
    {
        return view('admin.guru.edit', compact('guru'));
    }

    public function update(Request $request, Guru $guru)
    {
        $request->validate([
            'nama_guru' => 'required|string|max:255',
            'nip' => 'nullable|string|max:18|unique:gurus,nip,' . $guru->id,
            'nik' => 'nullable|string|max:16|unique:gurus,nik,' . $guru->id,
            'nuptk' => 'nullable|string|max:16|unique:gurus,nuptk,' . $guru->id,
            'tempat_lahir' => 'nullable|string|max:255',
            'tgl_lahir' => 'nullable|date',
            'gender' => 'required|in:L,P',
            'alamat' => 'nullable|string',
            'desa' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kabupaten' => 'nullable|string|max:255',
            'provinsi' => 'nullable|string|max:255',
            'kode_pos' => 'nullable|string|max:10',
        ]);

        $guru->update([
            'nama_guru' => $request->nama_guru,
            'nip' => $request->nip,
            'nik' => $request->nik,
            'nuptk' => $request->nuptk,
            'tempat_lahir' => $request->tempat_lahir,
            'tgl_lahir' => $request->tgl_lahir,
            'gender' => $request->gender,
            'alamat' => $request->alamat,
            'desa' => $request->desa,
            'kecamatan' => $request->kecamatan,
            'kabupaten' => $request->kabupaten,
            'provinsi' => $request->provinsi,
            'kode_pos' => $request->kode_pos,
        ]);

        if ($guru->user) {
            $guru->user->update(['name' => $request->nama_guru]);
        }

        return redirect()->route('admin.guru.index')->with('success', 'Data Guru berhasil diperbarui!');
    }

    public function destroy(Guru $guru)
    {
        DB::transaction(function () use ($guru) {
            if ($guru->user) {
                $guru->user->delete();
            }
            $guru->delete();
        });

        return redirect()->route('admin.guru.index')->with('success', 'Data Guru berhasil dihapus!');
    }
}