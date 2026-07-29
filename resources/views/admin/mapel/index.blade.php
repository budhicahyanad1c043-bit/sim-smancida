<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    {{ __('Data Master Mata Pelajaran') }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Kelola daftar mata pelajaran dan deskripsinya di dalam sistem akademik.
                </p>
            </div>
            <!-- Tombol Tambah Mapel -->
            <a href="{{ route('admin.mapel.create') }}" class="inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-medium px-4 py-2.5 rounded-xl text-sm transition shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Mapel
            </a>
        </div>
    </x-slot>

    <!-- Wrapper Alpine.js untuk State Modal Hapus -->
    <div x-data="{ 
        deleteModalOpen: false,
        deleteActionUrl: '',
        deleteItemName: '',
        
        confirmDelete(url, name) {
            this.deleteActionUrl = url;
            this.deleteItemName = name;
            this.deleteModalOpen = true;
        }
    }" class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Alert Success -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Card Utama -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                
                <!-- Action Bar: Search & Filter Form -->
                <div class="p-5 border-b border-slate-100 bg-slate-50/50">
                    <form action="{{ route('admin.mapel.index') }}" method="GET" class="flex flex-col sm:flex-row justify-between items-center gap-4">
                        <div class="relative w-full sm:w-80">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode atau nama mapel..." 
                                   class="w-full pl-9 pr-4 py-2 text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition shadow-sm">
                        </div>
                    </form>
                </div>

                <!-- Tabel Data -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <!-- Table Header -->
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-4 px-6 w-32">Kode</th>
                                <th class="py-4 px-6">Nama Mata Pelajaran</th>
                                <th class="py-4 px-6">Deskripsi</th>
                                <th class="py-4 px-6 text-center w-40">Aksi</th>
                            </tr>
                        </thead>

                        <!-- Table Body -->
                        <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                            @forelse ($mapels as $item)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="py-4 px-6">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                            {{ $item->kode_mapel }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 font-semibold text-slate-800">
                                        {{ $item->nama_mapel }}
                                    </td>
                                    <td class="py-4 px-6 text-slate-500 max-w-xs truncate">
                                        {{ $item->deskripsi ?? '-' }}
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <!-- Tombol Edit -->
                                            <a href="{{ route('admin.mapel.edit', $item->id) }}" title="Edit" class="inline-flex items-center justify-center p-2 text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            <!-- Tombol Pemicu Hapus Modern -->
                                            <button type="button" 
                                                    @click="confirmDelete('{{ route('admin.mapel.destroy', $item->id) }}', '{{ addslashes($item->nama_mapel) }}')" 
                                                    title="Hapus" 
                                                    class="inline-flex items-center justify-center p-2 text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-12 text-center text-slate-400">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                            </svg>
                                            <p class="text-sm font-medium">Belum ada data mata pelajaran.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Footer / Pagination Dynamic -->
                @if ($mapels->hasPages())
                    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $mapels->links() }}
                    </div>
                @endif

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
                     class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs transition-opacity"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal Content -->
                <div x-show="deleteModalOpen" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full p-6">
                    
                    <div class="sm:flex sm:items-start gap-4">
                        <!-- Icon Warning Badge -->
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-2xl bg-rose-50 sm:mx-0">
                            <svg class="h-6 w-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        
                        <div class="mt-3 text-center sm:mt-0 sm:text-left">
                            <h3 class="text-base font-bold text-slate-900" id="modal-title">
                                Hapus Mata Pelajaran
                            </h3>
                            <div class="mt-2">
                                <p class="text-xs text-slate-500 leading-relaxed">
                                    Apakah Anda yakin ingin menghapus mata pelajaran <span class="font-bold text-slate-800" x-text="deleteItemName"></span>? Data yang terhapus tidak dapat dikembalikan.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-6 sm:mt-5 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                        <button type="button" 
                                @click="deleteModalOpen = false" 
                                class="w-full sm:w-auto px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition duration-150">
                            Batal
                        </button>
                        
                        <form :action="deleteActionUrl" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full sm:w-auto px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-rose-100 transition duration-150">
                                Ya, Hapus Data
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>