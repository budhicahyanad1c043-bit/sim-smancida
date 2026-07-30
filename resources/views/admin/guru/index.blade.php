<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">Data Master Guru</h2>
                <p class="text-xs text-gray-500 mt-1">Kelola data tenaga pengajar dan otentikasi akun guru.</p>
            </div>
            
            <div class="flex items-center gap-2">
                <!-- Tombol Import Excel -->
                <button @click="importModalOpen = true" 
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-xl shadow-md shadow-emerald-100 transition duration-150">
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

    <!-- Wrapper Alpine JS -->
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
    }" class="space-y-4 py-4 sm:py-6">

        <!-- Flash Alert Success -->
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" class="p-4 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-between text-emerald-800 transition-all">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span class="text-xs sm:text-sm font-semibold">{{ session('success') }}</span>
                </div>
                <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 p-1 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        <!-- Flash Alert Error -->
        @if(session('error') || $errors->any())
            <div x-data="{ show: true }" x-show="show" class="p-4 rounded-2xl bg-rose-50 border border-rose-100 flex items-start justify-between text-rose-800 transition-all">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-xl bg-rose-500/10 flex items-center justify-center text-rose-600 shrink-0 mt-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="text-xs sm:text-sm font-semibold space-y-1">
                        @if(session('error'))
                            <p>{{ session('error') }}</p>
                        @endif
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
                <button @click="show = false" class="text-rose-500 hover:text-rose-700 p-1 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

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
                Total: {{ method_exists($gurus, 'total') ? $gurus->total() : $gurus->count() }} Guru
            </span>
        </div>

        <!-- TAMPILAN MOBILE (Kartu / Card List) -->
        <div class="grid grid-cols-1 gap-3 md:hidden">
            @forelse($gurus as $guru)
                @php
                    $namaGuru = $guru->nama_guru ?? $guru->nama ?? $guru->name ?? $guru->user->name ?? '-';
                    $emailGuru = $guru->email ?? $guru->user->email ?? '-';
                    $gender = strtolower($guru->gender ?? $guru->jenis_kelamin ?? '');
                    $filterText = $namaGuru . ' ' . $guru->nip . ' ' . $emailGuru;
                @endphp
                <div x-show="filterRow('{{ addslashes($filterText) }}')" class="bg-white p-4 rounded-2xl border border-gray-100 shadow-xs space-y-3">
                    <div class="flex items-start justify-between gap-3 border-b border-gray-50 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 font-bold flex items-center justify-center shrink-0">
                                {{ strtoupper(substr($namaGuru, 0, 1)) }}
                            </div>
                            <div>
                                <h4 class="font-bold text-sm text-gray-900">{{ $namaGuru }}</h4>
                                <p class="text-xs text-gray-400">{{ $emailGuru }}</p>
                            </div>
                        </div>

                        <!-- Gender Badge -->
                        @if($gender === 'l' || $gender === 'laki-laki' || $gender === 'male')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-600 border border-blue-100">
                                Laki-Laki
                            </span>
                        @elseif($gender === 'p' || $gender === 'perempuan' || $gender === 'female')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-pink-50 text-pink-600 border border-pink-100">
                                Perempuan
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-gray-50 text-gray-500 border border-gray-200">
                                -
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center justify-between text-xs pt-1">
                        <div>
                            <span class="block text-[10px] uppercase font-bold text-gray-400">NIP</span>
                            <span class="font-mono font-semibold text-gray-700">{{ $guru->nip ?? '-' }}</span>
                        </div>

                        <div class="flex items-center gap-1">
                            <a href="{{ route('admin.guru.edit', $guru->id) }}" 
                               class="p-2 rounded-xl text-indigo-600 bg-indigo-50/60 hover:bg-indigo-100 transition" 
                               title="Edit Data">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>

                            <button type="button" 
                                    @click="confirmDelete('{{ route('admin.guru.destroy', $guru->id) }}', '{{ addslashes($namaGuru) }}')"
                                    class="p-2 rounded-xl text-rose-600 bg-rose-50/60 hover:bg-rose-100 transition" 
                                    title="Hapus Data">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white p-8 rounded-2xl border border-gray-100 text-center text-xs font-semibold text-gray-400">
                    Belum ada data guru yang terdaftar.
                </div>
            @endforelse
        </div>

        <!-- TAMPILAN DESKTOP (Tabel) -->
        <div class="hidden md:block bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
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
                                $namaGuru = $guru->nama_guru ?? $guru->nama ?? $guru->name ?? $guru->user->name ?? '-';
                                $emailGuru = $guru->email ?? $guru->user->email ?? '-';
                                $gender = strtolower($guru->gender ?? $guru->jenis_kelamin ?? '');
                                $filterText = $namaGuru . ' ' . $guru->nip . ' ' . $emailGuru;
                            @endphp
                            <tr x-show="filterRow('{{ addslashes($filterText) }}')" class="hover:bg-indigo-50/30 transition duration-150">
                                <!-- Nama & Email -->
                                <td class="py-3.5 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 font-bold flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($namaGuru, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900">
                                                {{ $namaGuru }}
                                            </div>
                                            <div class="text-[11px] text-gray-400">
                                                {{ $emailGuru }}
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
                                    <div class="flex items-center justify-center gap-1">
                                        <a href="{{ route('admin.guru.edit', $guru->id) }}" 
                                           class="p-2 rounded-xl text-indigo-600 hover:bg-indigo-50 transition" 
                                           title="Edit Data">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>

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

        <!-- PAGINATION -->
        @if(method_exists($gurus, 'hasPages') && $gurus->hasPages())
            <div class="bg-white px-4 py-3 rounded-2xl border border-gray-100 shadow-xs">
                {{ $gurus->links() }}
            </div>
        @endif

        <!-- MODAL IMPORT EXCEL -->
        <div x-cloak x-show="importModalOpen" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 p-4 text-center sm:p-0">
                <div x-show="importModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900/40 backdrop-blur-xs" @click="importModalOpen = false"></div>

                <div x-show="importModalOpen" x-transition.scale.origin.bottom
                     class="relative bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all w-full max-w-md p-5 sm:p-6 border border-gray-100 z-10">
                    
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                        <h3 class="text-base font-bold text-gray-800">Import Data Guru (Excel)</h3>
                        <button @click="importModalOpen = false" class="text-gray-400 hover:text-gray-600 p-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form action="{{ route('admin.guru.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Pilih File Excel (.xlsx / .csv)</label>
                            <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                                   class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer border border-gray-200 rounded-xl p-1">
                        </div>

                        <!-- Info Format Kolom Excel -->
                        <div class="bg-gray-50 p-3 rounded-xl border border-gray-100 space-y-1 text-[11px] text-gray-600">
                            <p class="font-bold text-gray-700">Format Kolom Header Excel:</p>
                            <p><code class="bg-gray-200 px-1 rounded">nama</code>, <code class="bg-gray-200 px-1 rounded">email</code>, <code class="bg-gray-200 px-1 rounded">nip</code>, <code class="bg-gray-200 px-1 rounded">gender</code> (L/P), <code class="bg-gray-200 px-1 rounded">alamat</code></p>
                            <p class="text-[10px] text-gray-400 italic mt-1">* Password otomatis diset: <span class="font-mono text-indigo-600 font-bold">password123</span></p>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                            <button type="button" @click="importModalOpen = false" class="w-full sm:w-auto px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-semibold transition-colors">
                                Batal
                            </button>
                            <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-md shadow-emerald-100 transition-colors">
                                Unggah & Import
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODAL KONFIRMASI HAPUS -->
        <div x-cloak x-show="deleteModalOpen" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 p-4 text-center sm:p-0">
                <div x-show="deleteModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900/40 backdrop-blur-xs" @click="deleteModalOpen = false"></div>

                <div x-show="deleteModalOpen" x-transition.scale.origin.bottom
                     class="relative bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all w-full max-w-md p-5 sm:p-6 border border-gray-100 z-10">
                    
                    <div class="flex items-start gap-4 mb-4">
                        <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-800">Hapus Data Guru</h3>
                            <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                                Apakah Anda yakin ingin menghapus data <span class="font-bold text-gray-800" x-text="deleteItemName"></span>? Tindakan ini tidak dapat dibatalkan.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-4 border-t border-gray-100">
                        <button type="button" @click="deleteModalOpen = false" class="w-full sm:w-auto px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition">
                            Batal
                        </button>
                        
                        <form :action="deleteActionUrl" method="POST" class="w-full sm:w-auto">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-rose-100 transition">
                                Ya, Hapus Data
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>