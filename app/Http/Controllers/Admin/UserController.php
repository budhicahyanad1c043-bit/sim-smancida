<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    // Menampilkan daftar user beserta rolenya
    public function index(Request $request)
    {
        $query = User::with(['roles', 'guru', 'siswa']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->paginate(10)->withQueryString();
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    // Menambah user baru beserta penetapan role & sinkronisasi data profil (Guru/Siswa)
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'password' => ['required', Rules\Password::defaults()],
            'roles'    => 'nullable|array',
            'roles.*'  => 'exists:roles,name',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Assign role jika ada yang dipilih
            if ($request->has('roles')) {
                $user->syncRoles($request->roles);
            }

            // Jika user diberi role 'guru', buatkan otomatis record skeleton di tabel gurus
            if ($user->hasRole('guru') && !$user->guru) {
                Guru::create([
                    'user_id'   => $user->id,
                    'nama_guru' => $user->name,
                    'gender'    => 'L', // default value
                ]);
            }
        });

        return redirect()->back()->with('success', 'User berhasil ditambahkan.');
    }

    // Mengubah data dasar User (Nama, Email, Password)
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
        ]);

        DB::transaction(function () use ($request, $user) {
            $data = [
                'name'  => $request->name,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            // Sinkronkan nama di tabel Guru jika user ini adalah Guru
            if ($user->guru) {
                $user->guru->update(['nama_guru' => $request->name]);
            }
        });

        return redirect()->back()->with('success', "Data user {$user->name} berhasil diperbarui.");
    }

    // Mengubah / Menyesuaikan Multiple Roles milik User
    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'roles'   => 'nullable|array',
            'roles.*' => 'exists:roles,name',
        ]);

        DB::transaction(function () use ($request, $user) {
            // syncRoles akan otomatis memasukkan semua role yang dicentang/dipilih.
            $user->syncRoles($request->roles ?? []);

            // Jika role guru ditambahkan dan data guru belum ada, buatkan otomatis
            if ($user->hasRole('guru') && !$user->guru) {
                Guru::create([
                    'user_id'   => $user->id,
                    'nama_guru' => $user->name,
                    'gender'    => 'L',
                ]);
            }
        });

        return redirect()->back()->with('success', "Role untuk {$user->name} berhasil diperbarui.");
    }

    // Menambah role baru ke dalam sistem
    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name|max:255',
        ]);

        Role::create([
            'name'       => strtolower(trim($request->name)),
            'guard_name' => 'web',
        ]);

        return redirect()->back()->with('success', "Role baru '{$request->name}' berhasil ditambahkan.");
    }

    // Menghapus User beserta data terkait (Guru / Siswa)
    public function destroy(User $user)
    {
        DB::transaction(function () use ($user) {
            if ($user->guru) {
                $user->guru->delete();
            }
            if ($user->siswa) {
                $user->siswa->delete();
            }
            $user->delete();
        });

        return redirect()->back()->with('success', 'User berhasil dihapus.');
    }
}