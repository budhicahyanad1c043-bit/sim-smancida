<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">Data Master Guru</h2>
                <p class="text-xs text-gray-500 mt-1">Kelola data tenaga pengajar dan otentikasi akun guru.</p>
            </div>
            <a href="{{ route('admin.guru.create') }}" 
               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl shadow-md shadow-indigo-100 transition duration-150">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Guru</span>
            </a>
        </div>
    </x-slot>

    <!-- Wrapper Alpine untuk Live Search & Modal Delete -->
    <div x-data="{ 
        search: '',
        deleteModalOpen: false,
        deleteActionUrl: '',
        deleteItemName: '',
        
        filterRow(el) {
            if (this.search === '') return true;
            return el.innerText.toLowerCase().includes(this.search.toLowerCase());
        },

        confirmDelete(url, name) {
            this.deleteActionUrl = url;
            this.deleteItemName = name;
            this.deleteModalOpen = true;
        }
    }" class="space-y-4">

        <!-- Bar Pencarian & Filter -->
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-xs flex items-center justify-between gap-4">
            <div class="relative flex-1 max-w-md">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input x-model="search" 
                       type="text" 
                       placeholder="Cari nama, NIP, atau email guru..." 
                       class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium text-gray-800 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition duration-150">
            </div>
            
            <span class="text-xs font-semibold text-gray-400 hidden sm:inline-block">
                Live Search Active
            </span>
        </div>

        <!-- Tabel Modern -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/70 border-b border-gray-100 text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            <th class="py-3.5 px-6">Guru</th>
                            <th class="py-3.5 px-6">NIP</th>
                            <th class="py-3.5 px-6">Gender</th>
                            <th class="py-3.5 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs text-gray-700">
                        @forelse($gurus as $guru)
                            @php
                                $namaGuru = $guru->nama ?? $guru->name ?? $guru->user->name ?? '-';
                            @endphp
                            <tr x-show="filterRow($el)" class="hover:bg-indigo-50/30 transition duration-150">
                                <!-- Nama & Email -->
                                <td class="py-3.5 px-6">
                                    <div class="flex items-center gap-3">
                                        <!-- Avatar Inisial -->
                                        <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 font-bold flex items-center justify-center flex-shrink-0">
                                            {{ strtoupper(substr($namaGuru, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900">
                                                {{ $namaGuru }}
                                            </div>
                                            <div class="text-[11px] text-gray-400">
                                                {{ $guru->email ?? $guru->user->email ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- NIP -->
                                <td class="py-3.5 px-6 font-mono font-medium text-gray-600">
                                    {{ $guru->nip ?? '-' }}
                                </td>

                                <!-- Gender Badge -->
                                <td class="py-3.5 px-6">
                                    @php
                                        $gender = strtolower($guru->gender ?? $guru->jenis_kelamin ?? '');
                                    @endphp

                                    @if($gender === 'l' || $gender === 'laki-laki' || $gender === 'male')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold bg-blue-50 text-blue-600 border border-blue-100">
                                            Laki-Laki
                                        </span>
                                    @elseif($gender === 'p' || $gender === 'perempuan' || $gender === 'female')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold bg-pink-50 text-pink-600 border border-pink-100">
                                            Perempuan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold bg-gray-50 text-gray-500 border border-gray-200">
                                            -
                                        </span>
                                    @endif
                                </td>

                                <!-- Aksi Buttons -->
                                <td class="py-3.5 px-6 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.guru.edit', $guru->id) }}" 
                                           class="p-2 rounded-xl text-indigo-600 hover:bg-indigo-50 transition" 
                                           title="Edit Data">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>

                                        <!-- Tombol Pemicu Modal Delete Modern -->
                                        <button type="button" 
                                                @click="confirmDelete('{{ route('admin.guru.destroy', $guru->id) }}', '{{ addslashes($namaGuru) }}')"
                                                class="p-2 rounded-xl text-rose-600 hover:bg-rose-50 transition" 
                                                title="Hapus Data">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-xs font-semibold text-gray-400">
                                    Belum ada data guru yang terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL KONFIRMASI HAPUS MODERN -->
        <div x-cloak 
             x-show="deleteModalOpen" 
             class="fixed inset-0 z-50 overflow-y-auto" 
             aria-labelledby="modal-title" 
             role="dialog" 
             aria-modal="true">
            
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Backdrop Blur -->
                <div x-show="deleteModalOpen" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0" 
                     x-transition:enter-end="opacity-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100" 
                     x-transition:leave-end="opacity-0" 
                     @click="deleteModalOpen = false" 
                     class="fixed inset-0 bg-gray-900/40 backdrop-blur-xs transition-opacity"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal Dialog -->
                <div x-show="deleteModalOpen" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full p-6">
                    
                    <div class="sm:flex sm:items-start gap-4">
                        <!-- Icon Danger Badge -->
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-2xl bg-rose-50 sm:mx-0">
                            <svg class="h-6 w-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        
                        <div class="mt-3 text-center sm:mt-0 sm:text-left">
                            <h3 class="text-base font-bold text-gray-900" id="modal-title">
                                Hapus Data Guru
                            </h3>
                            <div class="mt-2">
                                <p class="text-xs text-gray-500 leading-relaxed">
                                    Apakah Anda yakin ingin menghapus data <span class="font-bold text-gray-800" x-text="deleteItemName"></span>? Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="mt-6 sm:mt-5 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                        <button type="button" 
                                @click="deleteModalOpen = false" 
                                class="w-full sm:w-auto px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition duration-150">
                            Batal
                        </button>
                        
                        <form :action="deleteActionUrl" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full sm:w-auto px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-rose-100 transition duration-150">
                                Ya, Hapus Data.
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>