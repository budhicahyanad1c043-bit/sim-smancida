<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen User & Role') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6" 
         x-data="{ 
            modalTambah: false, 
            modalEdit: false, 
            modalDelete: false, 
            editUser: { id: '', name: '', email: '' }, 
            deleteUser: { id: '', name: '' },
            editActionUrl: '',
            deleteActionUrl: '',
            openEdit(user) {
                this.editUser = user;
                this.editActionUrl = '{{ url('admin/users') }}/' + user.id;
                this.modalEdit = true;
            },
            openDelete(user) {
                this.deleteUser = user;
                this.deleteActionUrl = '{{ url('admin/users') }}/' + user.id;
                this.modalDelete = true;
            }
         }">

        <!-- Notifikasi Sukses -->
        @if (session('success'))
            <div class="p-4 mb-4 text-sm text-emerald-800 rounded-2xl bg-emerald-50 border border-emerald-200" role="alert">
                <span class="font-bold">Berhasil!</span> {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 mb-4 text-sm text-rose-800 rounded-2xl bg-rose-50 border border-rose-200">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Grid Top Bar -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-6">
            
            <!-- 1. Form Tambah Role -->
            <div class="md:col-span-4 bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-center">
                <span class="text-xs font-bold text-gray-400 tracking-wider uppercase mb-2">+ TAMBAH ROLE</span>
                <form action="{{ route('admin.roles.store') }}" method="POST" class="flex items-center gap-2">
                    @csrf
                    <input type="text" name="name" placeholder="Role baru..." class="w-full text-sm bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:border-indigo-500" required>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-xl transition">
                        Simpan
                    </button>
                </form>
            </div>

            <!-- 2. Tombol Tambah User Baru -->
            <div class="md:col-span-3 bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-center">
                <button @click="modalTambah = true" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2.5 px-4 rounded-xl flex items-center justify-center gap-2 transition text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    + Tambah User Baru
                </button>
            </div>

            <!-- 3. Form Pencarian -->
            <div class="md:col-span-5 bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex items-center">
                <form action="{{ route('admin.users.index') }}" method="GET" class="w-full flex items-center gap-2">
                    <div class="relative w-full">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email user..." class="w-full text-sm bg-gray-50 border border-gray-200 rounded-xl pl-9 pr-3 py-2 focus:outline-none focus:border-indigo-500">
                    </div>
                    <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium px-4 py-2 rounded-xl transition">
                        Cari
                    </button>
                </form>
            </div>

        </div>

        <!-- Tabel Daftar User -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                            <th class="py-3.5 px-5">Pengguna</th>
                            <th class="py-3.5 px-5">Email</th>
                            <th class="py-3.5 px-5">Role Saat Ini</th>
                            <th class="py-3.5 px-5">Kelola Role</th>
                            <th class="py-3.5 px-5 text-right">Aksi User</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs text-gray-700">
                        @forelse ($users as $user)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-3.5 px-5 font-semibold text-gray-900">
                                    {{ $user->name }}
                                </td>
                                <td class="py-3.5 px-5 text-gray-500">
                                    {{ $user->email }}
                                </td>
                                <td class="py-3.5 px-5">
                                    @forelse ($user->roles as $r)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-100">
                                            {{ $r->name }}
                                        </span>
                                    @empty
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-gray-100 text-gray-500">
                                            Tanpa Role
                                        </span>
                                    @endforelse
                                </td>
                                <td class="py-3.5 px-5">
                                    <form action="{{ route('admin.users.update-role', $user->id) }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            @foreach ($roles as $role)
                                                <label class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-medium border cursor-pointer select-none
                                                    {{ $user->hasRole($role->name) ? 'bg-indigo-50 border-indigo-200 text-indigo-700 font-semibold' : 'bg-gray-50 border-gray-200 text-gray-600' }}">
                                                    <input type="checkbox" name="roles[]" value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'checked' : '' }}
                                                        class="w-3 h-3 text-indigo-600 rounded">
                                                    <span>{{ ucfirst($role->name) }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-semibold px-2.5 py-1 rounded-lg">
                                            Simpan
                                        </button>
                                    </form>
                                </td>
                                <td class="py-3.5 px-5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Tombol Edit User -->
                                        <button @click="openEdit({{ json_encode(['id' => $user->id, 'name' => $user->name, 'email' => $user->email]) }})"
                                            class="p-1.5 text-amber-600 bg-amber-50 hover:bg-amber-100 rounded-lg transition-all" title="Edit User">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>

                                        <!-- Tombol Hapus User -->
                                        <button @click="openDelete({{ json_encode(['id' => $user->id, 'name' => $user->name]) }})"
                                            class="p-1.5 text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition-all" title="Hapus User">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-400">
                                    Tidak ada data user ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-gray-100">
                {{ $users->links() }}
            </div>
        </div>

        <!-- MODAL TAMBAH USER -->
        <div x-cloak x-show="modalTambah" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-xl" @click.away="modalTambah = false">
                <div class="flex justify-between items-center border-b pb-3">
                    <h3 class="font-bold text-gray-800 text-sm">Tambah Akun User Baru</h3>
                    <button @click="modalTambah = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>
                <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" required class="w-full text-xs border-gray-200 rounded-xl bg-gray-50/50 py-2 px-3 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Email</label>
                        <input type="email" name="email" required class="w-full text-xs border-gray-200 rounded-xl bg-gray-50/50 py-2 px-3 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Password</label>
                        <input type="password" name="password" required class="w-full text-xs border-gray-200 rounded-xl bg-gray-50/50 py-2 px-3 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Role Awal</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($roles as $r)
                                <label class="inline-flex items-center gap-1.5 text-xs text-gray-600 bg-gray-50 border p-1.5 rounded-lg cursor-pointer">
                                    <input type="checkbox" name="roles[]" value="{{ $r->name }}" class="w-3.5 h-3.5 text-indigo-600 rounded">
                                    <span>{{ ucfirst($r->name) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 pt-3 border-t">
                        <button type="button" @click="modalTambah = false" class="px-3 py-2 text-xs font-semibold bg-gray-100 rounded-xl text-gray-600">Batal</button>
                        <button type="submit" class="px-4 py-2 text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl">Simpan User</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL EDIT USER -->
        <div x-cloak x-show="modalEdit" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-xl" @click.away="modalEdit = false">
                <div class="flex justify-between items-center border-b pb-3">
                    <h3 class="font-bold text-gray-800 text-sm">Edit Data User</h3>
                    <button @click="modalEdit = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>
                <form :action="editActionUrl" method="POST" class="space-y-3">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" x-model="editUser.name" required class="w-full text-xs border-gray-200 rounded-xl bg-gray-50/50 py-2 px-3 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Email</label>
                        <input type="email" name="email" x-model="editUser.email" required class="w-full text-xs border-gray-200 rounded-xl bg-gray-50/50 py-2 px-3 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Password Baru <span class="text-[10px] text-gray-400 font-normal">(Kosongkan jika tidak diganti)</span></label>
                        <input type="password" name="password" placeholder="••••••••" class="w-full text-xs border-gray-200 rounded-xl bg-gray-50/50 py-2 px-3 focus:ring-indigo-500">
                    </div>
                    <div class="flex justify-end gap-2 pt-3 border-t">
                        <button type="button" @click="modalEdit = false" class="px-3 py-2 text-xs font-semibold bg-gray-100 rounded-xl text-gray-600">Batal</button>
                        <button type="submit" class="px-4 py-2 text-xs font-semibold bg-amber-600 hover:bg-amber-700 text-white rounded-xl">Update User</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL DELETE USER -->
        <div x-cloak x-show="modalDelete" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-sm w-full p-6 space-y-4 shadow-xl text-center" @click.away="modalDelete = false">
                <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h3 class="font-bold text-gray-800 text-sm">Konfirmasi Hapus User</h3>
                <p class="text-xs text-gray-500">Apakah Anda yakin ingin menghapus user <span class="font-bold text-gray-800" x-text="deleteUser.name"></span>? Tindakan ini tidak dapat dibatalkan.</p>
                <form :action="deleteActionUrl" method="POST" class="flex justify-center gap-2 pt-2">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="modalDelete = false" class="px-4 py-2 text-xs font-semibold bg-gray-100 rounded-xl text-gray-600">Batal</button>
                    <button type="submit" class="px-4 py-2 text-xs font-semibold bg-rose-600 hover:bg-rose-700 text-white rounded-xl">Ya, Hapus</button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>