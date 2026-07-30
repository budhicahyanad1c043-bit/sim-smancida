<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-gray-800">Data Siswa</h2>
                <p class="text-xs text-gray-500 mt-0.5">Kelola data seluruh siswa beserta informasi akademik dan personal.</p>
            </div>
            <div class="flex items-center gap-2">
                <!-- Tombol Import Excel -->
                <button type="button" @click="$dispatch('import-excel')" class="inline-flex items-center justify-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-xs rounded-xl shadow-xs transition gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    <span>Import Excel</span>
                </button>

                <!-- Tombol Tambah Siswa -->
                <a href="{{ route('admin.siswa.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-xs rounded-xl shadow-xs transition gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Tambah Siswa</span>
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Wrapper Alpine.js untuk Live Search, Filter & Modal -->
    <div x-data="{ 
        deleteModalOpen: false,
        importModalOpen: false,
        deleteActionUrl: '',
        deleteItemName: '',
        search: '{{ request('search') }}',
        kelasId: '{{ request('kelas_id') }}',
        sortBy: '{{ request('sort_by', 'created_at') }}',
        sortDirection: '{{ request('sort_direction', 'desc') }}',
        isLoading: false,
        timeout: null,

        confirmDelete(url, name) {
            this.deleteActionUrl = url;
            this.deleteItemName = name;
            this.deleteModalOpen = true;
        },

        // Function Live Search & Filter via Fetch AJAX
        fetchResults() {
            this.isLoading = true;
            clearTimeout(this.timeout);
            
            // Debounce 300ms agar server tidak overload saat mengetik cepat
            this.timeout = setTimeout(() => {
                const params = new URLSearchParams({
                    search: this.search,
                    kelas_id: this.kelasId,
                    sort_by: this.sortBy,
                    sort_direction: this.sortDirection
                });

                fetch(`{{ route('admin.siswa.index') }}?${params.toString()}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    // Ganti kontainer tabel dan pagination dengan data baru
                    document.getElementById('siswa-container').innerHTML = doc.getElementById('siswa-container').innerHTML;
                    this.isLoading = false;
                })
                .catch(err => {
                    console.error(err);
                    this.isLoading = false;
                });
            }, 300);
        }
    }" class="space-y-4">

        <!-- Flash Message Success -->
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold rounded-2xl flex items-center justify-between">
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
            </div>
        @endif

        <!-- Flash Message Error -->
        @if(session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold rounded-2xl flex items-center justify-between">
                <span>{{ session('error') }}</span>
                <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">&times;</button>
            </div>
        @endif

        <!-- Live Filter & Search Bar -->
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-xs flex flex-col sm:flex-row gap-3 justify-between items-center">
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                <!-- Live Search Input -->
                <div class="relative w-full sm:w-80">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <!-- Loading Spinner saat mengetik -->
                        <template x-if="isLoading">
                            <svg class="animate-spin h-4 w-4 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </template>
                        <template x-if="!isLoading">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </template>
                    </div>
                    <input 
                        type="text" 
                        x-model="search"
                        @input="fetchResults()"
                        placeholder="Cari Nama, NISN, NIS, NIK..." 
                        class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition"
                    >
                </div>

                <!-- Live Dropdown Filter Kelas -->
                <select 
                    x-model="kelasId" 
                    @change="fetchResults()" 
                    class="w-full sm:w-48 px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium focus:bg-white focus:border-indigo-500 cursor-pointer">
                    <option value="">Semua Kelas</option>
                    @foreach($kelases as $kelas)
                        <option value="{{ $kelas->id }}">
                            {{ $kelas->nama_kelas ?? $kelas->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Total Data Badge -->
            <div class="w-full sm:w-auto flex justify-end">
                <span class="text-xs font-medium text-gray-500 bg-gray-50 border border-gray-100 px-3 py-2 rounded-xl">
                    Total: <strong class="text-indigo-600 font-bold">{{ $siswas->total() }}</strong> Siswa
                </span>
            </div>
        </div>

        <!-- Wrapper Kontainer Data Siswa (Dynamic Area AJAX) -->
        <div id="siswa-container">
            <!-- 1. RESPONSIVE MOBILE VIEW (Card Layout - Terlihat di ukuran Screen HP < md) -->
            <div class="grid grid-cols-1 gap-3 md:hidden">
                @forelse($siswas as $siswa)
                    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-xs space-y-3">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="font-bold text-sm text-gray-800">{{ $siswa->nama_siswa }}</h3>
                                <div class="mt-1 flex items-center gap-2">
                                    <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 font-bold rounded-md text-[10px]">
                                        {{ $siswa->kelas->nama_kelas ?? $siswa->kelas->nama ?? 'Tanpa Kelas' }}
                                    </span>
                                    @if($siswa->gender === 'L')
                                        <span class="px-2 py-0.5 bg-blue-50 text-blue-600 font-bold rounded-md text-[10px]">Laki-laki</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-pink-50 text-pink-600 font-bold rounded-md text-[10px]">Perempuan</span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Tombol Aksi Mobile -->
                            <div class="flex items-center gap-1">
                                <a href="{{ route('admin.siswa.edit', $siswa->id) }}" class="p-2 text-gray-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <button type="button" 
                                        @click="confirmDelete('{{ route('admin.siswa.destroy', $siswa->id) }}', '{{ addslashes($siswa->nama_siswa) }}')"
                                        class="p-2 text-gray-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <hr class="border-gray-50">

                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <span class="text-gray-400 text-[10px] block">NISN / NIS</span>
                                <span class="font-semibold text-gray-700">{{ $siswa->nisn ?? '-' }} / {{ $siswa->nis ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-400 text-[10px] block">Status Akun</span>
                                @if($siswa->user)
                                    <span class="inline-flex items-center gap-1 text-emerald-600 font-semibold text-[11px]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Terhubung
                                    </span>
                                @else
                                    <span class="text-gray-400 italic text-[11px]">Belum Ada</span>
                                @endif
                            </div>
                            <div class="col-span-2">
                                <span class="text-gray-400 text-[10px] block">TTL</span>
                                <span class="font-medium text-gray-700">{{ $siswa->tempat_lahir ?? '-' }}, {{ $siswa->tgl_lahir ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 text-center text-gray-400">
                        <svg class="w-8 h-8 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <span class="text-xs font-medium">Data siswa tidak ditemukan</span>
                    </div>
                @endforelse
            </div>

            <!-- 2. TABLE DESKTOP VIEW (Terlihat pada Screen Desktop >= md) -->
            <div class="hidden md:block bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-gray-600 border-collapse">
                        <thead class="bg-gray-50/75 text-gray-400 uppercase font-bold text-[10px] tracking-wider border-b border-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-center w-12">NO</th>
                                <th class="px-4 py-3">
                                    <button @click="sortBy = 'nama_siswa'; sortDirection = (sortDirection === 'asc' ? 'desc' : 'asc'); fetchResults()" class="inline-flex items-center gap-1 hover:text-indigo-600 transition group">
                                        <span>SISWA & L/P</span>
                                    </button>
                                </th>
                                <th class="px-4 py-3">NISN / NIS</th>
                                <th class="px-4 py-3">
                                    <button @click="sortBy = 'kelas'; sortDirection = (sortDirection === 'asc' ? 'desc' : 'asc'); fetchResults()" class="inline-flex items-center gap-1 hover:text-indigo-600 transition group">
                                        <span>KELAS</span>
                                    </button>
                                </th>
                                <th class="px-4 py-3">TTL</th>
                                <th class="px-4 py-3">AKUN USER</th>
                                <th class="px-4 py-3 text-center w-24">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y text-xs divide-gray-100">
                            @forelse($siswas as $index => $siswa)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-4 py-3.5 text-center font-semibold text-gray-400">
                                        {{ $siswas->firstItem() + $index }}
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <div class="font-bold text-gray-800">{{ $siswa->nama_siswa }}</div>
                                        <div class="mt-1">
                                            @if($siswa->gender === 'L')
                                                <span class="px-2 py-0.5 bg-blue-50 text-blue-600 font-bold rounded-md text-[10px]">Laki-laki</span>
                                            @else
                                                <span class="px-2 py-0.5 bg-pink-50 text-pink-600 font-bold rounded-md text-[10px]">Perempuan</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <div class="font-semibold text-gray-700">NISN: {{ $siswa->nisn ?? '-' }}</div>
                                        <div class="text-gray-400 text-[11px] mt-0.5">NIS: {{ $siswa->nis ?? '-' }}</div>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 font-bold rounded-lg text-[11px]">
                                            {{ $siswa->kelas->nama_kelas ?? $siswa->kelas->nama ?? 'Belum Ada Kelas' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <div class="font-medium text-gray-700">{{ $siswa->tempat_lahir ?? '-' }}</div>
                                        <div class="text-gray-400 text-[11px] mt-0.5">{{ $siswa->tgl_lahir ?? '-' }}</div>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        @if($siswa->user)
                                            <span class="inline-flex items-center gap-1 text-emerald-600 font-semibold bg-emerald-50 px-2.5 py-1 rounded-lg text-[11px]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Terhubung
                                            </span>
                                        @else
                                            <span class="text-gray-400 italic text-[11px]">Belum Ada</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <a href="{{ route('admin.siswa.edit', $siswa->id) }}" class="p-1.5 text-gray-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                            <button type="button" 
                                                    @click="confirmDelete('{{ route('admin.siswa.destroy', $siswa->id) }}', '{{ addslashes($siswa->nama_siswa) }}')"
                                                    class="p-1.5 text-gray-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" 
                                                    title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                                        <div class="flex flex-col items-center justify-center space-y-1">
                                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                            </svg>
                                            <span class="text-xs font-medium">Data siswa tidak ditemukan</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination Container -->
            @if($siswas->hasPages())
                <div class="mt-4 px-4 py-3 border border-gray-100 bg-white rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="text-xs text-gray-500">
                        Menampilkan <span class="font-bold text-gray-700">{{ $siswas->firstItem() }}</span> - <span class="font-bold text-gray-700">{{ $siswas->lastItem() }}</span> dari <span class="font-bold text-gray-700">{{ $siswas->total() }}</span> siswa
                    </div>
                    <div>
                        {{ $siswas->links() }}
                    </div>
                </div>
            @endif
        </div>

        <!-- MODAL KONFIRMASI HAPUS -->
        <div x-cloak 
             x-show="deleteModalOpen" 
             class="fixed inset-0 z-50 overflow-y-auto" 
             aria-labelledby="modal-title" 
             role="dialog" 
             aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
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

                <div x-show="deleteModalOpen" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full p-6">
                    
                    <div class="sm:flex sm:items-start gap-4">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-2xl bg-rose-50 sm:mx-0">
                            <svg class="h-6 w-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        
                        <div class="mt-3 text-center sm:mt-0 sm:text-left">
                            <h3 class="text-base font-bold text-gray-900" id="modal-title">
                                Hapus Data Siswa
                            </h3>
                            <div class="mt-2">
                                <p class="text-xs text-gray-500 leading-relaxed">
                                    Apakah Anda yakin ingin menghapus data siswa <span class="font-bold text-gray-800" x-text="deleteItemName"></span>? Tindakan ini tidak dapat dibatalkan.
                                </p>
                            </div>
                        </div>
                    </div>

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
                                Ya, Hapus Data
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL IMPORT EXCEL -->
        <div x-cloak 
             style="display: none;"
             x-show="importModalOpen"
             @import-excel.window="importModalOpen = true"
             class="fixed inset-0 z-50 overflow-y-auto" 
             aria-labelledby="modal-title" 
             role="dialog" 
             aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="importModalOpen" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0" 
                     x-transition:enter-end="opacity-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100" 
                     x-transition:leave-end="opacity-0" 
                     @click="importModalOpen = false" 
                     class="fixed inset-0 bg-gray-900/40 backdrop-blur-xs transition-opacity"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="importModalOpen" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full p-6">
                    
                    <form action="{{ route('admin.siswa.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="sm:flex sm:items-start gap-4">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-2xl bg-emerald-50 sm:mx-0">
                                <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                            </div>
                            
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-base font-bold text-gray-900">
                                    Import Data Siswa
                                </h3>
                                <p class="text-xs text-gray-500 mt-1">
                                    Unggah file Excel (`.xlsx`, `.xls`, atau `.csv`) sesuai dengan struktur kolom yang tersedia.
                                </p>

                                <div class="mt-4">
                                    <input type="file" 
                                           name="file_excel" 
                                           required 
                                           accept=".xlsx, .xls, .csv"
                                           class="block w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer border border-gray-200 rounded-xl p-1">
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 sm:mt-5 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                            <button type="button" 
                                    @click="importModalOpen = false" 
                                    class="w-full sm:w-auto px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition duration-150">
                                Batal
                            </button>
                            
                            <button type="submit" 
                                    class="w-full sm:w-auto px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-emerald-100 transition duration-150">
                                Upload & Import
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>