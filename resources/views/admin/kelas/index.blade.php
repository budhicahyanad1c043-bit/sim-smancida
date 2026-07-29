<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-black text-2xl text-gray-800 tracking-tight">
                    {{ __('Kelola Data Kelas') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">Kelola informasi kelas, tahun ajaran, dan penugasan wali kelas.</p>
            </div>
        </div>
    </x-slot>

    <!-- State Alpine JS -->
    <div class="py-8" x-data="{ 
        openCreateModal: false, 
        openEditModal: false, 
        openDeleteModal: false, 
        editData: {}, 
        deleteUrl: '' 
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>

                <div x-data="{ show: true }" x-show="show" class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-100 flex items-center justify-between text-red-800 transition-all">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-red-500/10 flex items-center justify-center text-red-600 flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        @foreach ($errors->all() as $error)
                            <span class="text-sm font-semibold">{{ $error }}</span>
                        @endforeach
                    </div>
                    <button @click="show = false" class="text-red-500 hover:text-red-700 p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endif

            <!-- Flash Alert Success -->
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-between text-emerald-800 transition-all">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-600 flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="text-sm font-semibold">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endif

            <!-- Top Action Header -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0a2 2 0 012-2h2a2 2 0 012 2m-6 0v-4a2 2 0 012-2h2a2 2 0 012 2v4"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-800">Daftar Kelas</h3>
                        <p class="text-xs text-gray-400">Total {{ $kelases->count() }} kelas terdaftar</p>
                    </div>
                </div>
                <button @click="openCreateModal = true" 
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-md shadow-indigo-100 transition-all duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Kelas
                </button>
            </div>

            <!-- Tabel Data Kelas -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100 text-[11px] font-bold uppercase tracking-wider text-gray-400">
                                <th class="py-3.5 px-6">#</th>
                                <th class="py-3.5 px-6">Nama Kelas</th>
                                <th class="py-3.5 px-6">Tahun Ajaran</th>
                                <th class="py-3.5 px-6">Wali Kelas</th>
                                <th class="py-3.5 px-6">Jumlah Siswa</th>
                                <th class="py-3.5 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-xs">
                            @forelse ($kelases as $index => $kelas)
                                <tr class="hover:bg-gray-50/50 transition-colors duration-150">
                                    <td class="py-4 px-6 font-medium text-gray-400">{{ $index + 1 }}</td>
                                    <td class="py-4 px-6 font-bold text-gray-800">{{ $kelas->nama_kelas }}</td>
                                    <td class="py-4 px-6 text-gray-600">
                                        <span class="px-2.5 py-1 bg-gray-100 text-gray-700 rounded-lg text-[11px] font-semibold">
                                            {{ $kelas->tahun_ajaran }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        {{-- Menampilkan Nama Wali Kelas --}}
                                        @if($kelas->waliKelas)
                                            <div class="flex items-center gap-2">
                                                <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-[10px]">
                                                    {{ strtoupper(substr($kelas->waliKelas->nama ?? $kelas->waliKelas->name, 0, 1)) }}
                                                </div>
                                                <span class="font-semibold text-gray-700">
                                                    {{ $kelas->waliKelas->nama_guru ?? $kelas->waliKelas->nama ?? $kelas->waliKelas->name }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-gray-400 italic">Belum Ditentukan</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-[11px] font-semibold">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                                            {{ $kelas->siswas_count ?? 0 }} Siswa
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <!-- Edit Button -->
                                            <button @click="openEditModal = true; editData = { 
                                                id: {{ $kelas->id }}, 
                                                nama_kelas: '{{ $kelas->nama_kelas }}', 
                                                tahun_ajaran: '{{ $kelas->tahun_ajaran }}', 
                                                walikelas_id: '{{ $kelas->walikelas_id ?? '' }}' 
                                            }" 
                                            class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-xl transition-colors"
                                            title="Edit Kelas">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>

                                            <!-- Delete Trigger Button -->
                                            <button @click="openDeleteModal = true; deleteUrl = '{{ route('admin.kelas.destroy', $kelas->id) }}'" 
                                                    class="p-2 text-red-500 hover:bg-red-50 rounded-xl transition-colors" 
                                                    title="Hapus Kelas">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-gray-400">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-10 h-10 mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                            <p class="text-xs font-semibold">Belum ada data kelas.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- MODAL CREATE -->
            <div x-cloak x-show="openCreateModal" class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen px-4 p-4 text-center sm:p-0">
                    <div x-show="openCreateModal" 
                         x-transition:enter="ease-out duration-200"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-150"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs transition-opacity" 
                         @click="openCreateModal = false"></div>

                    <div x-show="openCreateModal"
                         x-transition:enter="ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="relative bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full p-6 border border-gray-100">
                        
                        <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-4">
                            <h3 class="text-base font-bold text-gray-800">Tambah Kelas Baru</h3>
                            <button @click="openCreateModal = false" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <form action="{{ route('admin.kelas.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Nama Kelas</label>
                                <input type="text" name="nama_kelas" class="w-full rounded-xl border-gray-200 text-xs focus:border-indigo-500 focus:ring-indigo-500 shadow-2xs" placeholder="Contoh: X RPL 1" required>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tahun Ajaran</label>
                                <input type="text" name="tahun_ajaran" class="w-full rounded-xl border-gray-200 text-xs focus:border-indigo-500 focus:ring-indigo-500 shadow-2xs" placeholder="Contoh: 2025/2026" required>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Wali Kelas</label>
                                <select name="walikelas_id" class="w-full rounded-xl border-gray-200 text-xs focus:border-indigo-500 focus:ring-indigo-500 shadow-2xs">
                                    <option value="">-- Pilih Wali Kelas (Opsional) --</option>
                                    @if(isset($gurus) && count($gurus) > 0)
                                        @foreach ($gurus as $guru)
                                            <option value="{{ $guru->id }}">{{ $guru->nama_guru }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                                <button type="button" @click="openCreateModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-semibold transition-colors">Batal</button>
                                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold shadow-md shadow-indigo-100 transition-colors">Simpan Data</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- MODAL EDIT -->
            <div x-cloak x-show="openEditModal" class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen px-4 p-4 text-center sm:p-0">
                    <div x-show="openEditModal" 
                         x-transition:enter="ease-out duration-200"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-150"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs transition-opacity" 
                         @click="openEditModal = false"></div>

                    <div x-show="openEditModal"
                         x-transition:enter="ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="relative bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full p-6 border border-gray-100">
                        
                        <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-4">
                            <h3 class="text-base font-bold text-gray-800">Edit Data Kelas</h3>
                            <button @click="openEditModal = false" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <form :action="`{{ url('admin/kelas') }}/${editData.id}`" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')

                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Nama Kelas</label>
                                <input type="text" name="nama_kelas" x-model="editData.nama_kelas" class="w-full rounded-xl border-gray-200 text-xs focus:border-indigo-500 focus:ring-indigo-500 shadow-2xs" required>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tahun Ajaran</label>
                                <input type="text" name="tahun_ajaran" x-model="editData.tahun_ajaran" class="w-full rounded-xl border-gray-200 text-xs focus:border-indigo-500 focus:ring-indigo-500 shadow-2xs" required>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Wali Kelas</label>
                                <select name="walikelas_id" x-model="editData.walikelas_id" class="w-full rounded-xl border-gray-200 text-xs focus:border-indigo-500 focus:ring-indigo-500 shadow-2xs">
                                    <option value="">-- Pilih Wali Kelas (Opsional) --</option>
                                    @if(isset($gurus) && count($gurus) > 0)
                                        @foreach ($gurus as $guru)
                                            <option value="{{ $guru->id }}">{{ $guru->nama_guru ?? $guru->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                                <button type="button" @click="openEditModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-semibold transition-colors">Batal</button>
                                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold shadow-md shadow-indigo-100 transition-colors">Perbarui Data</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- MODAL DELETE CONFIRMATION -->
            <div x-cloak x-show="openDeleteModal" class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen px-4 p-4 text-center sm:p-0">
                    <div x-show="openDeleteModal" 
                         x-transition:enter="ease-out duration-200"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-150"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs transition-opacity" 
                         @click="openDeleteModal = false"></div>

                    <div x-show="openDeleteModal"
                         x-transition:enter="ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="relative bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-md sm:w-full p-6 border border-gray-100">
                        
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-800">Hapus Data Kelas?</h3>
                                <p class="text-xs text-gray-500 mt-0.5">Tindakan ini tidak dapat dibatalkan. Apakah Anda yakin ingin menghapus kelas ini?</p>
                            </div>
                        </div>

                        <form :action="deleteUrl" method="POST" class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                            @csrf
                            @method('DELETE')
                            <button type="button" @click="openDeleteModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-semibold transition-colors">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-semibold shadow-md shadow-red-100 transition-colors">Ya, Hapus Data</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>