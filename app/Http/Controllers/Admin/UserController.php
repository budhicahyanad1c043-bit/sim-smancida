<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    // Menampilkan daftar user beserta rolenya
    public function index(Request $request)
    {
        $query = User::with('roles');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        $users = $query->paginate(10)->withQueryString();
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    // Mengubah / Menyesuaikan Multiple Roles milik User
    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'roles'   => 'nullable|array',
            'roles.*' => 'exists:roles,name',
        ]);

        // syncRoles akan otomatis memasukkan semua role yang dicentang/dipilih.
        // Jika tidak ada yang dicentang (null), role user akan dikosongkan.
        $user->syncRoles($request->roles ?? []);

        return redirect()->back()->with('success', "Role untuk {$user->name} berhasil diperbarui.");
    }

    // Menambah role baru ke dalam sistem
    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name|max:255',
        ]);

        Role::create(['name' => strtolower($request->name)]);

        return redirect()->back()->with('success', "Role baru '{$request->name}' berhasil ditambahkan.");
    }
}