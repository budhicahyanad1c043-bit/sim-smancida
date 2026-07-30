<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Rap2hpoutre\FastExcel\FastExcel; // Plugin rap2hpoutre

class GuruController extends Controller
{
    public function index()
    {
        $gurus = Guru::with('user')->latest()->paginate(10);
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
            'nip' => 'nullable|string|unique:gurus,nip',
            'gender' => 'required|in:L,P',
            'alamat' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make('password123'),
            ]);

            $user->syncRoles(['guru']);

            Guru::create([
                'user_id' => $user->id,
                'nama_guru' => $request->name,
                'nip' => $request->nip,
                'gender' => $request->gender,
                'alamat' => $request->alamat,
            ]);
        });

        return redirect()->route('admin.guru.index')->with('success', 'Data Guru berhasil ditambahkan!');
    }

    // Method baru untuk Fitur Import Excel
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            DB::transaction(function () use ($request) {
                (new FastExcel)->import($request->file('file'), function ($line) {
                    // Ambil data header dari Excel (case-insensitive fallback)
                    $nama = $line['nama'] ?? $line['Nama'] ?? $line['nama_guru'] ?? null;
                    $email = $line['email'] ?? $line['Email'] ?? null;
                    $nip = $line['nip'] ?? $line['NIP'] ?? null;
                    $gender = strtoupper(trim($line['gender'] ?? $line['Gender'] ?? $line['jenis_kelamin'] ?? 'L'));
                    $alamat = $line['alamat'] ?? $line['Alamat'] ?? null;

                    // Lewati jika nama atau email kosong
                    if (!$nama || !$email) {
                        return null;
                    }

                    // Hanya ambil 1 karakter awal (L atau P)
                    $genderFormatted = in_array(substr($gender, 0, 1), ['L', 'P']) ? substr($gender, 0, 1) : 'L';

                    // Buat atau cari user berdasarkan email
                    $user = User::firstOrCreate(
                        ['email' => $email],
                        [
                            'name' => $nama,
                            'password' => Hash::make('password123'),
                        ]
                    );

                    // Berikan role guru jika menggunakan Spatie Permission
                    if (method_exists($user, 'assignRole')) {
                        $user->assignRole('guru');
                    }

                    // Buat atau update data guru
                    Guru::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'nama_guru' => $nama,
                            'nip' => $nip,
                            'gender' => $genderFormatted,
                            'alamat' => $alamat,
                        ]
                    );
                });
            });

            return redirect()->route('admin.guru.index')->with('success', 'Import data guru dari Excel berhasil!');
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
            'nip' => 'nullable|string|unique:gurus,nip,' . $guru->id,
            'gender' => 'required|in:L,P',
        ]);

        $guru->update([
            'nama_guru' => $request->nama_guru,
            'nip' => $request->nip,
            'gender' => $request->gender,
            'alamat' => $request->alamat,
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