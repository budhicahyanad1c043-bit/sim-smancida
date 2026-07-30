<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">Data Master Guru</h2>
                <p class="text-xs text-gray-500 mt-1">Kelola data tenaga pengajar dan otentikasi akun guru.</p>
            </div>
            
            <div class="flex items-center gap-2">
                <!-- Tombol Import Excel -->
                <button type="button" 
                        @click="$dispatch('import-excel')" 
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-xl shadow-md shadow-emerald-100 transition duration-150 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <span>Import Excel</span>
                </button>
    
                <!-- Tombol Tambah Guru -->
                <a href="{{ route('admin.guru.create') }}" 
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl shadow-md shadow-indigo-100 transition duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Guru</span>
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Wrap Alpine x-data -->
    <div x-data="{ 
            search: '',
            deleteModalOpen: false,
            importModalOpen: false,
            deleteActionUrl: '',
            deleteItemName: '',
            
            filterRow(text) {
                if (this.search === '') return true;
                return text.toLowerCase().includes(this.search.toLowerCase());
            },

            confirmDelete(url, name) {
                this.deleteActionUrl = url;
                this.deleteItemName = name;
                this.deleteModalOpen = true;
            }
        }">

        <div class="space-y-4 py-4 sm:py-6">
            <!-- Alert Success -->
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" class="p-4 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-between text-emerald-800">
                    <span class="text-xs sm:text-sm font-semibold">{{ session('success') }}</span>
                    <button @click="show = false" class="text-emerald-500 hover:text-emerald-700">&times;</button>
                </div>
            @endif

            <!-- Alert Error -->
            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" class="p-4 rounded-2xl bg-rose-50 border border-rose-100 flex items-center justify-between text-rose-800">
                    <span class="text-xs sm:text-sm font-semibold">{{ session('error') }}</span>
                    <button @click="show = false" class="text-rose-500 hover:text-rose-700">&times;</button>
                </div>
            @endif

            <!-- Filter Search Bar -->
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-xs flex items-center justify-between gap-4">
                <div class="relative flex-1 max-w-md">
                    <input x-model="search" type="text" placeholder="Cari nama, NIP, atau email..." class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </div>
            </div>

            <!-- ================= MOBILE VIEW (CARDS) ================= -->
            <div class="grid grid-cols-1 gap-3 md:hidden">
                @forelse($gurus as $guru)
                    @php $namaGuru = $guru->nama_guru ?? $guru->user->name ?? '-'; @endphp
                    <div x-show="filterRow('{{ addslashes($namaGuru . ' ' . $guru->nip) }}')" 
                         class="bg-white p-4 rounded-2xl border border-gray-100 shadow-xs space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h4 class="font-bold text-sm text-gray-900">{{ $namaGuru }}</h4>
                                <p class="text-xs text-gray-500 mt-0.5">NIP: <span class="font-mono text-gray-700">{{ $guru->nip ?? '-' }}</span></p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $guru->user->email ?? '-' }}</p>
                            </div>
                        </div>

                        <!-- Action Buttons Mobile -->
                        <div class="pt-2 border-t border-gray-100 grid grid-cols-2 gap-2">
                            <a href="{{ route('admin.guru.edit', $guru->id) }}" 
                               class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-xl transition duration-150">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                <span>Edit</span>
                            </a>
                            <button type="button" 
                                    @click="confirmDelete('{{ route('admin.guru.destroy', $guru->id) }}', '{{ addslashes($namaGuru) }}')" 
                                    class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-xl transition duration-150 cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <span>Hapus</span>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="bg-white p-6 rounded-2xl text-center text-xs text-gray-400">Belum ada data guru.</div>
                @endforelse

                <!-- Pagination Mobile -->
                <div class="pt-2">
                    {{ $gurus->links() }}
                </div>
            </div>

            <!-- ================= DESKTOP VIEW (TABLE) ================= -->
            <div class="hidden md:block bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/70 border-b border-gray-100 text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            <th class="py-3.5 px-6">Nama Guru</th>
                            <th class="py-3.5 px-6">NIP</th>
                            <th class="py-3.5 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs text-gray-700">
                        @forelse($gurus as $guru)
                            @php $namaGuru = $guru->nama_guru ?? $guru->user->name ?? '-'; @endphp
                            <tr x-show="filterRow('{{ addslashes($namaGuru . ' ' . $guru->nip) }}')" class="hover:bg-indigo-50/30 transition duration-150">
                                <td class="py-3.5 px-6 font-bold text-gray-900">
                                    <div>{{ $namaGuru }}</div>
                                    <div class="text-[11px] text-gray-400 font-normal">{{ $guru->user->email ?? '-' }}</div>
                                </td>
                                <td class="py-3.5 px-6 font-mono text-gray-600">{{ $guru->nip ?? '-' }}</td>
                                <td class="py-3.5 px-6">
                                    <!-- Action Buttons Desktop -->
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('admin.guru.edit', $guru->id) }}" 
                                           title="Edit Guru"
                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 hover:text-indigo-700 rounded-lg transition duration-150">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span>Edit</span>
                                        </a>

                                        <button type="button" 
                                                @click="confirmDelete('{{ route('admin.guru.destroy', $guru->id) }}', '{{ addslashes($namaGuru) }}')" 
                                                title="Hapus Guru"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 hover:text-rose-700 rounded-lg transition duration-150 cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            <span>Hapus</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-8 text-center text-xs text-gray-400">Belum ada data guru.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Pagination Desktop -->
                <div class="p-4 border-t border-gray-100">
                    {{ $gurus->links() }}
                </div>
            </div>

            <!-- ================= MODAL KONFIRMASI HAPUS ================= -->
            <div x-cloak x-show="deleteModalOpen" class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen px-4 p-4 text-center">
                    <div x-show="deleteModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900/40 backdrop-blur-xs" @click="deleteModalOpen = false"></div>

                    <div x-show="deleteModalOpen" x-transition.scale
                         class="relative bg-white rounded-2xl text-left overflow-hidden shadow-xl w-full max-w-sm p-6 border border-gray-100 z-10">
                        
                        <div class="text-center space-y-3">
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-rose-100 text-rose-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <h3 class="text-base font-bold text-gray-900">Hapus Data Guru</h3>
                            <p class="text-xs text-gray-500">Apakah Anda yakin ingin menghapus data <span class="font-bold text-gray-800" x-text="deleteItemName"></span>? Tindakan ini tidak dapat dibatalkan.</p>
                        </div>

                        <form :action="deleteActionUrl" method="POST" class="mt-5 flex gap-2">
                            @csrf
                            @method('DELETE')
                            <button type="button" @click="deleteModalOpen = false" class="w-full py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-semibold transition">Batal</button>
                            <button type="submit" class="w-full py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-semibold shadow-md shadow-rose-100 transition">Ya, Hapus</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ================= MODAL IMPORT EXCEL ================= -->
            <div x-cloak 
             style="display: none;"
             x-show="importModalOpen"
             @import-excel.window="importModalOpen = true"
             class="fixed inset-0 z-50 overflow-y-auto" 
             aria-labelledby="modal-title" 
             role="dialog" 
             aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4 p-4 text-center">
                    <div x-show="importModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900/40 backdrop-blur-xs" @click="importModalOpen = false"></div>

                    <div x-show="importModalOpen" x-transition.scale
                         class="relative bg-white rounded-2xl text-left overflow-hidden shadow-xl w-full max-w-md p-6 border border-gray-100 z-10">
                        
                        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                            <h3 class="text-base font-bold text-gray-800">Import Data Guru (Excel)</h3>
                            <button @click="importModalOpen = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                        </div>

                        <form action="{{ route('admin.guru.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Pilih File Excel (.xlsx / .csv)</label>
                                <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                                       class="w-full text-xs text-gray-500 border border-gray-200 rounded-xl p-2 focus:ring-2 focus:ring-emerald-500">
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-3">
                                <button type="button" @click="importModalOpen = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl text-xs font-semibold hover:bg-gray-200 transition">Batal</button>
                                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-xl text-xs font-semibold hover:bg-emerald-700 transition">Unggah & Import</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>