<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Rap2hpoutre\FastExcel\FastExcel;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $kelasId = $request->input('kelas_id');

        // Parameter Sorting
        $sortBy = $request->input('sort_by', 'created_at'); // Default sort berdasarkan created_at
        $sortDirection = $request->input('sort_direction', 'asc'); // Default direction 'asc'

        // Validasi kolom yang diizinkan untuk di-sort
        $allowedSorts = ['nama_siswa', 'kelas'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }
        $sortDirection = strtolower($sortDirection) === 'desc' ? 'desc' : 'asc';

        $siswas = Siswa::with(['kelas', 'user'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_siswa', 'like', "%{$search}%")
                      ->orWhere('nisn', 'like', "%{$search}%")
                      ->orWhere('nis', 'like', "%{$search}%")
                      ->orWhere('nik', 'like', "%{$search}%");
                });
            })
            ->when($kelasId, function ($query, $kelasId) {
                $query->where('kelas_id', $kelasId);
            })
            // Logika Sorting
            ->when($sortBy === 'nama_siswa', function ($query) use ($sortDirection) {
                $query->orderBy('nama_siswa', $sortDirection);
            })
            ->when($sortBy === 'kelas', function ($query) use ($sortDirection) {
                // Join ke tabel kelases untuk sorting berdasarkan nama kelas
                $query->leftJoin('kelases', 'siswas.kelas_id', '=', 'kelases.id')
                      ->orderBy('kelases.nama_kelas', $sortDirection)
                      ->select('siswas.*'); // Memastikan id yang diambil tetap id milik siswa
            })
            ->when(!in_array($sortBy, ['nama_siswa', 'kelas']), function ($query) use ($sortDirection) {
                $query->orderBy('created_at', $sortDirection);
            })
            ->paginate(10)
            ->withQueryString(); // Tetap mempertahankan search, kelas_id, dan sort di pagination

        $kelases = Kelas::all();

        return view('admin.siswa.index', compact('siswas', 'kelases', 'search', 'kelasId', 'sortBy', 'sortDirection'));
    }

    public function create()
    {
        $kelases = Kelas::all();
        // Ambil user ber-role siswa atau user yang belum terhubung ke siswa
        $users = User::doesntHave('siswa')->get(); 
        return view('admin.siswa.create', compact('kelases', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_siswa'   => 'required|string|max:255',
            'gender'       => 'required|in:L,P',
            'kelas_id'     => 'nullable|exists:kelases,id',
            'user_id'      => 'nullable|exists:users,id|unique:siswas,user_id',
            'nisn'         => 'nullable|string|size:10|unique:siswas,nisn',
            'nik'          => 'nullable|string|size:16|unique:siswas,nik',
            'nis'          => 'nullable|string|max:10|unique:siswas,nis',
            'tempat_lahir' => 'nullable|string|max:255',
            'tgl_lahir'    => 'nullable|date',
            'agama'        => 'nullable|string|max:100',
            'nama_ibu'     => 'nullable|string|max:255',
            'nik_ibu'      => 'nullable|string|size:16|unique:siswas,nik_ibu',
            'nama_ayah'    => 'nullable|string|max:255',
            'nik_ayah'     => 'nullable|string|size:16|unique:siswas,nik_ayah',
            'alamat'       => 'nullable|string',
            'desa'         => 'nullable|string|max:255',
            'kecamatan'    => 'nullable|string|max:255',
            'kabupaten'    => 'nullable|string|max:255',
            'provinsi'     => 'nullable|string|max:255',
            'kode_pos'     => 'nullable|string|max:10',
        ]);

        Siswa::create($validated);

        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil ditambahkan!');
    }

    public function edit(Siswa $siswa)
    {
        $kelases = Kelas::all();
        // User yang belum terhubung + user yang sedang dipakai siswa ini
        $users = User::whereDoesntHave('siswa')
            ->orWhere('id', $siswa->user_id)
            ->get();

        return view('admin.siswa.edit', compact('siswa', 'kelases', 'users'));
    }

    public function update(Request $request, Siswa $siswa)
    {
        $validated = $request->validate([
            'nama_siswa'   => 'required|string|max:255',
            'gender'       => 'required|in:L,P',
            'kelas_id'     => 'nullable|exists:kelases,id',
            'user_id'      => ['nullable', 'exists:users,id', Rule::unique('siswas', 'user_id')->ignore($siswa->id)],
            'nisn'         => ['nullable', 'string', 'size:10', Rule::unique('siswas', 'nisn')->ignore($siswa->id)],
            'nik'          => ['nullable', 'string', 'size:16', Rule::unique('siswas', 'nik')->ignore($siswa->id)],
            'nis'          => ['nullable', 'string', 'max:10', Rule::unique('siswas', 'nis')->ignore($siswa->id)],
            'tempat_lahir' => 'nullable|string|max:255',
            'tgl_lahir'    => 'nullable|date',
            'agama'        => 'nullable|string|max:100',
            'nama_ibu'     => 'nullable|string|max:255',
            'nik_ibu'      => 'nullable', 'string', 'size:16',
            'nama_ayah'    => 'nullable|string|max:255',
            'nik_ayah'     => 'nullable', 'string', 'size:16',
            'alamat'       => 'nullable|string',
            'desa'         => 'nullable|string|max:255',
            'kecamatan'    => 'nullable|string|max:255',
            'kabupaten'    => 'nullable|string|max:255',
            'provinsi'     => 'nullable|string|max:255',
            'kode_pos'     => 'nullable|string|max:10',
        ]);

        $siswa->update($validated);

        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil diperbarui!');
    }

    public function destroy(Siswa $siswa)
    {
        $siswa->delete();
        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil dihapus!');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            // Membaca file excel baris demi baris secara streaming
            (new FastExcel)->import($request->file('file_excel'), function ($row) {

                // Normalize Gender
                $rawGender = strtoupper(trim($row['gender'] ?? ''));
                $gender = null;

                if (in_array($rawGender, ['L', 'LAKI-LAKI', 'LAKI LAKI', 'MALE'])) {
                    $gender = 'L';
                } elseif (in_array($rawGender, ['P', 'PEREMPUAN', 'FEMALE'])) {
                    $gender = 'P';
                }
                
                // Cari ID kelas jika kolom 'kelas' diisi di excel
                $kelasId = null;
                if (!empty($row['kelas'])) {
                    $kelas = Kelas::where('nama_kelas', $row['kelas'])
                        ->orWhere('nama', $row['kelas'])
                        ->first();
                    $kelasId = $kelas?->id;
                }

                // Format tanggal lahir
                $tglLahir = null;
                if (!empty($row['tgl_lahir'])) {
                    $tglLahir = date('Y-m-d', strtotime($row['tgl_lahir']));
                }

                return Siswa::create([
                    'nama_siswa'   => $row['nama_siswa'],
                    'gender'       => strtoupper($row['gender']),
                    'kelas_id'     => $kelasId,
                    'nisn'         => !empty($row['nisn']) ? (string)$row['nisn'] : null,
                    'nis'          => !empty($row['nis']) ? (string)$row['nis'] : null,
                    'nik'          => !empty($row['nik']) ? (string)$row['nik'] : null,
                    'tempat_lahir' => $row['tempat_lahir'] ?? null,
                    'tgl_lahir'    => $tglLahir,
                    'agama'        => $row['agama'] ?? null,
                    'nama_ibu'     => $row['nama_ibu'] ?? null,
                    'nik_ibu'      => !empty($row['nik_ibu']) ? (string)$row['nik_ibu'] : null,
                    'nama_ayah'    => $row['nama_ayah'] ?? null,
                    'nik_ayah'     => !empty($row['nik_ayah']) ? (string)$row['nik_ayah'] : null,
                    'alamat'       => $row['alamat'] ?? null,
                    'desa'         => $row['desa'] ?? null,
                    'kecamatan'    => $row['kecamatan'] ?? null,
                    'kabupaten'    => $row['kabupaten'] ?? null,
                    'provinsi'     => $row['provinsi'] ?? null,
                    'kode_pos'     => !empty($row['kode_pos']) ? (string)$row['kode_pos'] : null,
                ]);
            });

            return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses file Excel: ' . $e->getMessage());
        }
    }
}
