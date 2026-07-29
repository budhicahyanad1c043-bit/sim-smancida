<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen User & Role') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <!-- Notifikasi Sukses -->
        @if (session('success'))
            <div class="p-4 mb-4 text-sm text-emerald-800 rounded-2xl bg-emerald-50 border border-emerald-200" role="alert">
                <span class="font-bold">Berhasil!</span> {{ session('success') }}
            </div>
        @endif

        <!-- Grid Top Bar: Tambah Role & Pencarian -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Card Tambah Role Baru -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Tambah Role Baru
                </h3>
                <form action="{{ route('admin.roles.store') }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="text" name="name" placeholder="Nama Role (ex: bendahara)" required
                        class="flex-1 text-xs border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50/50 py-2 px-3">
                    <button type="submit" 
                        class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold px-4 py-2 rounded-xl transition-all">
                        Simpan
                    </button>
                </form>
            </div>

            <!-- Card Pencarian User -->
            <div class="md:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center">
                <form action="{{ route('admin.users.index') }}" method="GET" class="w-full flex gap-3 items-center">
                    <div class="flex-1 relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email user..." 
                            class="w-full text-xs border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50/50 py-2.5 px-3 pl-9">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white text-xs font-semibold px-5 py-2.5 rounded-xl transition-all">
                        Cari
                    </button>
                </form>
            </div>

        </div>

        <!-- Tabel Daftar User & Pengaturan Role -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                            <th class="py-3.5 px-5">Pengguna</th>
                            <th class="py-3.5 px-5">Email</th>
                            <th class="py-3.5 px-5">Role Saat Ini</th>
                            <th class="py-3.5 px-5 text-right">Aksi Ubah Role</th>
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
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-100">
                                            {{ $r->name }}
                                        </span>
                                    @empty
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-gray-100 text-gray-500">
                                            Tanpa Role
                                        </span>
                                    @endforelse
                                </td>
                                <td class="py-3.5 px-5 text-right">
                                    <!-- Form Ubah Multiple Roles -->
                                    <form action="{{ route('admin.users.update-role', $user) }}" method="POST" class="flex items-center justify-end gap-3">
                                        @csrf
                                        @method('PUT')

                                        <!-- Container Checkbox Role -->
                                        <div class="flex flex-wrap items-center gap-2 max-w-md justify-end">
                                            @foreach ($roles as $role)
                                                <label class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-medium border cursor-pointer transition-all select-none
                                                    {{ $user->hasRole($role->name) ? 'bg-indigo-50 border-indigo-200 text-indigo-700 font-semibold' : 'bg-gray-50 border-gray-200 text-gray-600 hover:bg-gray-100' }}">
                                                    
                                                    <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                                        {{ $user->hasRole($role->name) ? 'checked' : '' }}
                                                        class="w-3.5 h-3.5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                                    
                                                    <span>{{ ucfirst($role->name) }}</span>
                                                </label>
                                            @endforeach
                                        </div>

                                        <!-- Tombol Simpan -->
                                        <button type="submit" 
                                            class="bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white text-[11px] font-semibold px-3 py-1.5 rounded-xl transition-all shadow-sm flex-shrink-0">
                                            Simpan
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-gray-400">
                                    Tidak ada data user ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t border-gray-100">
                {{ $users->links() }}
            </div>
        </div>

    </div>
</x-app-layout>