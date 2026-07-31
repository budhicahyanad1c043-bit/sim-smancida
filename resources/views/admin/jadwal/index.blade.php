<x-app-layout>
    <div x-data="{ 
        deleteModalOpen: false,
        modalOpen: false,
        activeTab: '{{ $haris[0] ?? 'Senin' }}',
        jamId: '',
        jamKe: '',
        hari: '',
        mapelId: '',
        guruId: '',
        jadwalId: null,
        deleteActionUrl: '',
        deleteItemName: '',

        openForm(jamId, jamKe, hari, mapelId = '', guruId = '', jadwalId = null) {
            this.jamId = jamId;
            this.jamKe = jamKe;
            this.hari = hari;
            this.mapelId = mapelId;
            this.guruId = guruId;
            this.jadwalId = jadwalId;
            this.modalOpen = true;
        },

        confirmDelete(url, name) {
            this.deleteActionUrl = url;
            this.deleteItemName = name;
            this.deleteModalOpen = true;
        }
    }" class="space-y-5">

        <x-slot name="header">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="font-bold text-xl text-gray-800">Jadwal Pelajaran</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Kelola alokasi jam mengajar dan mata pelajaran per kelas.</p>
                </div>
            </div>
        </x-slot>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold rounded-2xl flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold rounded-2xl flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 font-bold">&times;</button>
            </div>
        @endif

        <!-- Filter Pilih Kelas -->
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <form method="GET" action="{{ route('admin.jadwal.index') }}" class="flex items-center gap-3 w-full sm:w-auto">
                <label class="text-xs font-bold text-gray-700 whitespace-nowrap">Pilih Kelas:</label>
                <select name="kelas_id" onchange="this.form.submit()" class="w-full sm:w-56 px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium text-gray-800 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition cursor-pointer">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($kelases as $kelas)
                        <option value="{{ $kelas->id }}" {{ $kelasId == $kelas->id ? 'selected' : '' }}>
                            {{ $kelas->nama_kelas ?? $kelas->nama }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        @if($kelasId)
            <!-- ================= TAMPILAN MOBILE (Card + Tab Hari) ================= -->
            <div class="block lg:hidden space-y-4">
                <!-- Tab Hari (Scroll Horizontal) -->
                <div class="flex overflow-x-auto gap-2 pb-2 scrollbar-none border-b border-gray-200">
                    @foreach($haris as $hari)
                        <button type="button"
                                @click="activeTab = '{{ $hari }}'"
                                :class="activeTab === '{{ $hari }}' ? 'bg-indigo-600 text-white shadow-sm font-bold' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200 font-medium'"
                                class="px-4 py-2 rounded-xl text-xs whitespace-nowrap transition">
                            {{ $hari }}
                        </button>
                    @endforeach
                </div>

                <!-- Konten Per Hari -->
                @foreach($haris as $hari)
                    <div x-show="activeTab === '{{ $hari }}'" class="space-y-3">
                        @foreach($jamPelajarans as $jam)
                            @php
                                $jadwal = $jadwals[$jam->id][$hari] ?? null;
                            @endphp
                            <div class="p-3.5 bg-white rounded-2xl border border-gray-100 shadow-xs flex items-center justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 bg-gray-100 text-gray-700 text-[10px] font-bold rounded-md">
                                            {{ $jam->nama }}
                                        </span>
                                        <span class="text-[10px] text-gray-400 font-mono">
                                            {{ date('H:i', strtotime($jam->jam_mulai)) }} - {{ date('H:i', strtotime($jam->jam_selesai)) }}
                                        </span>
                                    </div>

                                    @if($jadwal)
                                        <h4 class="font-bold text-indigo-950 text-xs truncate">
                                            {{ $jadwal->mapel->nama_mapel ?? $jadwal->mapel->nama }}
                                        </h4>
                                        <p class="text-[11px] text-gray-500 mt-0.5 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            <span class="truncate">{{ $jadwal->guru->nama_guru ?? $jadwal->guru->nama ?? 'Belum ada guru' }}</span>
                                        </p>
                                    @else
                                        <p class="text-xs text-gray-400 italic">Belum terisi</p>
                                    @endif
                                </div>

                                <!-- Aksi Mobile -->
                                <div class="flex items-center gap-1 flex-shrink-0">
                                    @if($jadwal)
                                        <button type="button" 
                                                @click="openForm('{{ $jam->id }}', '{{ $jam->jam_ke }}', '{{ $hari }}', '{{ $jadwal->mapel_id }}', '{{ $jadwal->guru_id }}', '{{ $jadwal->id }}')" 
                                                class="p-2 text-amber-600 bg-amber-50 hover:bg-amber-100 rounded-xl transition" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <button type="button" 
                                                @click="confirmDelete('{{ route('admin.jadwal.destroy', $jadwal->id) }}', '{{ addslashes($jadwal->mapel->nama_mapel ?? $jadwal->mapel->nama ?? '') }}')"
                                                class="p-2 text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-xl transition" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    @else
                                        <button type="button" 
                                                @click="openForm('{{ $jam->id }}', '{{ $jam->jam_ke }}', '{{ $hari }}')" 
                                                class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold text-xs rounded-xl transition flex items-center gap-1">
                                            <span>+ Set</span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>

            <!-- ================= TAMPILAN DESKTOP / PC (Matriks Tabel) ================= -->
            <div class="hidden lg:block bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-center text-xs text-gray-600 border-collapse">
                        <thead class="bg-gray-50/80 text-gray-500 font-bold uppercase text-[10px] tracking-wider border-b border-gray-100">
                            <tr>
                                <th class="px-4 py-3.5 border-r border-gray-100 w-32">JAM / WAKTU</th>
                                @foreach($haris as $hari)
                                    <th class="px-4 py-3.5 border-r border-gray-100 min-w-[150px]">{{ $hari }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($jamPelajarans as $jam)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-3 py-3 border-r border-gray-100 bg-gray-50/30">
                                        <div class="font-bold text-gray-800 text-xs">{{ $jam->nama ?? 'Jam Ke-' . $jam->jam_ke }}</div>
                                        <div class="text-[10px] font-mono text-gray-400 mt-0.5">
                                            {{ date('H:i', strtotime($jam->jam_mulai)) }} - {{ date('H:i', strtotime($jam->jam_selesai)) }}
                                        </div>
                                    </td>

                                    @foreach($haris as $hari)
                                        @php
                                            $jadwal = $jadwals[$jam->id][$hari] ?? null;
                                        @endphp
                                        <td class="p-2 border-r border-gray-100 align-top">
                                            @if($jadwal)
                                                <div class="p-2.5 bg-indigo-50/70 hover:bg-indigo-50 border border-indigo-100 rounded-xl relative group text-left transition shadow-2xs">
                                                    <div class="font-bold text-indigo-950 text-xs leading-snug">
                                                        {{ $jadwal->mapel->nama_mapel ?? $jadwal->mapel->nama }}
                                                    </div>
                                                    <div class="text-[10px] text-indigo-600 mt-1 flex items-center gap-1 font-medium">
                                                        <svg class="w-3 h-3 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                        <span class="truncate">{{ $jadwal->guru->nama_guru ?? $jadwal->guru->nama ?? 'Belum ada guru' }}</span>
                                                    </div>

                                                    <!-- Tombol Aksi -->
                                                    <div class="mt-2 pt-1.5 border-t border-indigo-100/60 flex items-center justify-end gap-1">
                                                        <button type="button" 
                                                                @click="openForm('{{ $jam->id }}', '{{ $jam->jam_ke }}', '{{ $hari }}', '{{ $jadwal->mapel_id }}', '{{ $jadwal->guru_id }}', '{{ $jadwal->id }}')" 
                                                                class="p-1 text-amber-600 hover:bg-amber-100/80 rounded-lg transition" title="Edit">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                        </button>
                                                        <button type="button" 
                                                                @click="confirmDelete('{{ route('admin.jadwal.destroy', $jadwal->id) }}', '{{ addslashes($jadwal->mapel->nama_mapel ?? $jadwal->mapel->nama ?? '') }}')"
                                                                class="p-1 text-rose-600 hover:bg-rose-100/80 rounded-lg transition" title="Hapus">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            @else
                                                <button type="button" 
                                                        @click="openForm('{{ $jam->id }}', '{{ $jam->jam_ke }}', '{{ $hari }}')" 
                                                        class="w-full py-3.5 border-2 border-dashed border-gray-100 hover:border-indigo-300 hover:bg-indigo-50/30 rounded-xl text-gray-300 hover:text-indigo-600 transition flex items-center justify-center gap-1 group">
                                                    <span class="text-xs font-semibold group-hover:scale-105 transition-transform">+ Set Jadwal</span>
                                                </button>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="p-12 text-center bg-white rounded-2xl border border-gray-100 shadow-xs">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <p class="text-xs font-medium text-gray-400">Silakan pilih kelas terlebih dahulu untuk melihat dan mengelola jadwal pelajaran.</p>
            </div>
        @endif

        <!-- MODAL FORM ISI / EDIT JADWAL -->
        <div x-cloak x-show="modalOpen" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="modalOpen" @click="modalOpen = false" class="fixed inset-0 bg-gray-900/40 backdrop-blur-xs transition-opacity"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="modalOpen" class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full p-6">
                    <h3 class="text-base font-bold text-gray-900 mb-1">Set Jadwal Pelajaran</h3>
                    <p class="text-xs text-gray-500 mb-4">
                        Hari <strong x-text="hari" class="text-indigo-600"></strong> - Jam Ke-<strong x-text="jamKe" class="text-indigo-600"></strong>
                    </p>

                    <form action="{{ route('admin.jadwal.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="kelas_id" value="{{ $kelasId }}">
                        <input type="hidden" name="jam_pelajaran_id" :value="jamId">
                        <input type="hidden" name="hari" :value="hari">

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Mata Pelajaran</label>
                            <select name="mapel_id" x-model="mapelId" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium text-gray-800 focus:bg-white focus:border-indigo-500">
                                <option value="">-- Pilih Mapel --</option>
                                @foreach($mapels as $mapel)
                                    <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel ?? $mapel->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Guru Pengampu</label>
                            <select name="guru_id" x-model="guruId" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium text-gray-800 focus:bg-white focus:border-indigo-500">
                                <option value="">-- Pilih Guru --</option>
                                @foreach($gurus as $guru)
                                    <option value="{{ $guru->id }}">{{ $guru->nama_guru ?? $guru->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-6 flex justify-end gap-2">
                            <button type="button" @click="modalOpen = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-indigo-100 transition">
                                Simpan Jadwal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODAL KONFIRMASI HAPUS MODERN -->
        <div x-cloak x-show="deleteModalOpen" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="deleteModalOpen" @click="deleteModalOpen = false" class="fixed inset-0 bg-gray-900/40 backdrop-blur-xs transition-opacity"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="deleteModalOpen" class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full p-6">
                    <div class="sm:flex sm:items-start gap-4">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-2xl bg-rose-50 sm:mx-0">
                            <svg class="h-6 w-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        
                        <div class="mt-3 text-center sm:mt-0 sm:text-left">
                            <h3 class="text-base font-bold text-gray-900">Hapus Data Jadwal</h3>
                            <div class="mt-2">
                                <p class="text-xs text-gray-500 leading-relaxed">
                                    Apakah Anda yakin ingin menghapus data Jadwal <span class="font-bold text-gray-800" x-text="deleteItemName"></span>? Tindakan ini tidak dapat dibatalkan.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 sm:mt-5 flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
                        <button type="button" @click="deleteModalOpen = false" class="w-full sm:w-auto px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition">
                            Batal
                        </button>
                        
                        <form :action="deleteActionUrl" method="POST" class="inline">
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