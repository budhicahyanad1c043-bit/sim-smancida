<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="font-bold text-xl sm:text-2xl text-gray-800 leading-tight">
                    {{ __('Absensi Mata Pelajaran') }}
                </h2>
                <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Kelola dan catat kehadiran siswa per sesi mata pelajaran</p>
            </div>
            
            @if (isset($siswas) && count($siswas) > 0)
                <div class="inline-flex items-center self-start sm:self-auto px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Total: {{ count($siswas) }} Siswa
                </div>
            @endif
        </div>
    </x-slot>

    <!-- WRAPPER UTAMA DENGAN ALPINE X-DATA -->
    <div x-data="{ openResetModal: false }" class="py-4 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4 sm:space-y-6">
            
            <!-- Alert Notifikasi -->
            @if (session('success'))
                <div class="flex items-center p-3.5 sm:p-4 text-emerald-800 bg-emerald-50 rounded-xl border border-emerald-200 shadow-sm" role="alert">
                    <svg class="flex-shrink-0 w-4 h-4 sm:w-5 sm:h-5 me-2.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <div class="text-xs sm:text-sm font-medium">{{ session('success') }}</div>
                </div>
            @endif

            @if (session('error'))
                <div class="flex items-center p-3.5 sm:p-4 text-rose-800 bg-rose-50 rounded-xl border border-rose-200 shadow-sm" role="alert">
                    <svg class="flex-shrink-0 w-4 h-4 sm:w-5 sm:h-5 me-2.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <div class="text-xs sm:text-sm font-medium">{{ session('error') }}</div>
                </div>
            @endif

            <!-- Card Filter / Search Bar -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6 transition-all hover:shadow-md">
                <form action="{{ route('guru.absensi.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3 sm:gap-4 items-end">
                    
                    <div class="md:col-span-3">
                        <label class="block text-[11px] sm:text-xs font-bold text-gray-600 uppercase tracking-wider mb-1 sm:mb-2">Tanggal Sesi</label>
                        <input type="date" name="tanggal" value="{{ $tanggal ?? date('Y-m-d') }}" 
                            class="w-full text-xs sm:text-sm border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50/50 focus:bg-white transition-all py-2 sm:py-2.5 px-3">
                    </div>

                    <div class="md:col-span-3">
                        <label class="block text-[11px] sm:text-xs font-bold text-gray-600 uppercase tracking-wider mb-1 sm:mb-2">Pilih Kelas</label>
                        <select name="kelas_id" class="w-full text-xs sm:text-sm border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50/50 focus:bg-white transition-all py-2 sm:py-2.5 px-3" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach ($kelases as $k)
                                <option value="{{ $k->id }}" {{ (isset($selectedKelas) && $selectedKelas == $k->id) ? 'selected' : '' }}>
                                    {{ $k->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-4">
                        <label class="block text-[11px] sm:text-xs font-bold text-gray-600 uppercase tracking-wider mb-1 sm:mb-2">Mata Pelajaran</label>
                        <select name="mapel_id" class="w-full text-xs sm:text-sm border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50/50 focus:bg-white transition-all py-2 sm:py-2.5 px-3" required>
                            <option value="">-- Pilih Mapel --</option>
                            @foreach ($mapels as $m)
                                <option value="{{ $m->id }}" {{ (isset($selectedMapel) && $selectedMapel == $m->id) ? 'selected' : '' }}>
                                    {{ $m->nama_mapel }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2 mt-2 md:mt-0">
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-semibold py-2.5 px-4 rounded-xl shadow-sm hover:shadow transition-all duration-150 flex items-center justify-center gap-2 text-xs sm:text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- List Presensi Responsif (Single Component) -->
            @if (isset($siswas) && count($siswas) > 0)
            <form action="{{ route('guru.absensi.store') }}" method="POST" id="form-simpan-absensi">
                @csrf
                <!-- Hidden Inputs untuk GPS dan Device ID -->
                <input type="hidden" name="latitude" id="input_latitude">
                <input type="hidden" name="longitude" id="input_longitude">
                <input type="hidden" name="device_id" id="input_device_id">

                <input type="hidden" name="kelas_id" value="{{ $selectedKelas }}">
                <input type="hidden" name="mapel_id" value="{{ $selectedMapel }}">
                <input type="hidden" name="tanggal" value="{{ $tanggal }}">

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="divide-y divide-gray-100">
                        @foreach ($siswas as $index => $siswa)
                            @php
                                $dataAbsensi = $existingAbsensi->get($siswa->id);
                                $statusAwal = $dataAbsensi ? strtolower($dataAbsensi->status) : 'hadir';
                                $currentKeterangan = $dataAbsensi ? $dataAbsensi->keterangan : '';
                            @endphp

                            <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-indigo-50/20 transition-colors">
                                
                                <!-- Informasi Siswa -->
                                <div class="flex items-center gap-3 sm:w-1/3">
                                    <span class="w-7 h-7 rounded-full bg-gray-100 text-gray-500 font-bold text-xs flex items-center justify-center flex-shrink-0">
                                        {{ $index + 1 }}
                                    </span>
                                    <div class="min-w-0">
                                        <h4 class="font-bold text-sm sm:text-base text-gray-800 truncate">{{ $siswa->nama_siswa }}</h4>
                                        <p class="font-mono text-[11px] sm:text-xs text-gray-400 mt-0.5">NISN: {{ $siswa->nisn }}</p>
                                    </div>
                                </div>

                                <!-- Opsi Kehadiran (4 Tombol Radio) -->
                                <div class="grid grid-cols-4 gap-1.5 sm:gap-2 sm:w-auto">
                                    
                                    <!-- HADIR -->
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="absensi[{{ $siswa->id }}]" value="Hadir" 
                                            {{ $statusAwal === 'hadir' ? 'checked' : '' }} 
                                            class="peer sr-only">
                                        <div class="py-2 px-2 sm:px-3.5 rounded-xl text-xs font-semibold border border-gray-200 text-gray-600 bg-white 
                                            peer-checked:bg-emerald-500 peer-checked:text-white peer-checked:border-emerald-500 
                                            flex flex-col sm:flex-row items-center justify-center gap-1 sm:gap-1.5 transition-all shadow-sm hover:bg-gray-50 select-none">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500 peer-checked:bg-white transition-colors"></span>
                                            <span>Hadir</span>
                                        </div>
                                    </label>

                                    <!-- IZIN -->
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="absensi[{{ $siswa->id }}]" value="Izin" 
                                            {{ $statusAwal === 'izin' ? 'checked' : '' }} 
                                            class="peer sr-only">
                                        <div class="py-2 px-2 sm:px-3.5 rounded-xl text-xs font-semibold border border-gray-200 text-gray-600 bg-white 
                                            peer-checked:bg-sky-500 peer-checked:text-white peer-checked:border-sky-500 
                                            flex flex-col sm:flex-row items-center justify-center gap-1 sm:gap-1.5 transition-all shadow-sm hover:bg-gray-50 select-none">
                                            <span class="w-2 h-2 rounded-full bg-sky-500 peer-checked:bg-white transition-colors"></span>
                                            <span>Izin</span>
                                        </div>
                                    </label>

                                    <!-- SAKIT -->
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="absensi[{{ $siswa->id }}]" value="Sakit" 
                                            {{ $statusAwal === 'sakit' ? 'checked' : '' }} 
                                            class="peer sr-only">
                                        <div class="py-2 px-2 sm:px-3.5 rounded-xl text-xs font-semibold border border-gray-200 text-gray-600 bg-white 
                                            peer-checked:bg-amber-500 peer-checked:text-white peer-checked:border-amber-500 
                                            flex flex-col sm:flex-row items-center justify-center gap-1 sm:gap-1.5 transition-all shadow-sm hover:bg-gray-50 select-none">
                                            <span class="w-2 h-2 rounded-full bg-amber-500 peer-checked:bg-white transition-colors"></span>
                                            <span>Sakit</span>
                                        </div>
                                    </label>

                                    <!-- ALPA -->
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="absensi[{{ $siswa->id }}]" value="Alpa" 
                                            {{ $statusAwal === 'alpa' ? 'checked' : '' }} 
                                            class="peer sr-only">
                                        <div class="py-2 px-2 sm:px-3.5 rounded-xl text-xs font-semibold border border-gray-200 text-gray-600 bg-white 
                                            peer-checked:bg-rose-500 peer-checked:text-white peer-checked:border-rose-500 
                                            flex flex-col sm:flex-row items-center justify-center gap-1 sm:gap-1.5 transition-all shadow-sm hover:bg-gray-50 select-none">
                                            <span class="w-2 h-2 rounded-full bg-rose-500 peer-checked:bg-white transition-colors"></span>
                                            <span>Alpa</span>
                                        </div>
                                    </label>

                                </div>

                                <!-- Keterangan -->
                                <div class="sm:w-1/4">
                                    <input type="text" name="keterangan[{{ $siswa->id }}]" value="{{ $currentKeterangan }}" placeholder="Catatan (opsional)..." class="w-full text-xs border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50/50 focus:bg-white transition-all py-2 px-3">
                                </div>

                            </div>
                        @endforeach
                    </div>

                </div>

                <!-- Bottom Bar Simpan -->
                <div class="p-4 mt-2 bg-white lg:bg-gray-50/80 rounded-2xl lg:rounded-2xl border border-gray-100 shadow-lg lg:shadow-xs flex flex-col sm:flex-row items-center justify-between sticky bottom-4 z-10 backdrop-blur-md gap-3">
                    <span class="text-xs text-gray-500 font-medium hidden sm:inline-block">Pastikan Anda berada di lingkungan kelas/sekolah saat menyimpan.</span>
                    
                    <div class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto justify-end">
                        <!-- Tombol Pemicu Modal Reset -->
                        @if ($existingAbsensi->count() > 0)
                            <button type="button" 
                                    @click="openResetModal = true" 
                                    class="w-full sm:w-auto bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-semibold py-2.5 px-4 rounded-xl shadow-sm transition-all duration-150 flex items-center justify-center gap-1.5 text-xs sm:text-sm cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Reset Absen
                            </button>
                        @endif

                        <button type="button" 
                                id="btn-simpan-absensi"
                                onclick="validasiDanKirimAbsensi()"
                                class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold py-2.5 px-6 rounded-xl shadow-md hover:shadow-lg transition-all duration-150 flex items-center justify-center gap-2 text-xs sm:text-sm cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Simpan Data Absensi
                        </button>
                    </div>
                </div>
            </form>
            @endif

            <!-- Form Rekapitulasi Absensi -->
            <div class="bg-white rounded-2xl shadow-sm border border-indigo-100 p-4 sm:p-6 transition-all">
                <div class="flex items-center justify-between mb-4 border-b border-gray-100 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm sm:text-base text-gray-800">Rekapitulasi Absensi</h3>
                            <p class="text-[11px] sm:text-xs text-gray-400">Cetak/Lihat rekap presensi dalam periode tertentu</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('guru.absensi.rekap') }}" method="GET" id="form-rekap-absensi" target="_blank" data-no-loader class="grid grid-cols-1 md:grid-cols-12 gap-3 sm:gap-4 items-end">
                    <div class="md:col-span-3">
                        <label class="block text-[11px] sm:text-xs font-bold text-gray-600 uppercase tracking-wider mb-1 sm:mb-2">Dari Tanggal</label>
                        <input type="date" name="tanggal_mulai" value="{{ date('Y-m-01') }}" required 
                            class="w-full text-xs sm:text-sm border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50/50 focus:bg-white transition-all py-2 sm:py-2.5 px-3">
                    </div>

                    <div class="md:col-span-3">
                        <label class="block text-[11px] sm:text-xs font-bold text-gray-600 uppercase tracking-wider mb-1 sm:mb-2">Sampai Tanggal</label>
                        <input type="date" name="tanggal_selesai" value="{{ date('Y-m-d') }}" required 
                            class="w-full text-xs sm:text-sm border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50/50 focus:bg-white transition-all py-2 sm:py-2.5 px-3">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-[11px] sm:text-xs font-bold text-gray-600 uppercase tracking-wider mb-1 sm:mb-2">Kelas</label>
                        <select name="kelas_id" class="w-full text-xs sm:text-sm border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50/50 focus:bg-white transition-all py-2 sm:py-2.5 px-3" required>
                            <option value="">-- Kelas --</option>
                            @foreach ($kelases as $k)
                                <option value="{{ $k->id }}" {{ (request('kelas_id') == $k->id || (isset($selectedKelas) && $selectedKelas == $k->id)) ? 'selected' : '' }}>
                                    {{ $k->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-[11px] sm:text-xs font-bold text-gray-600 uppercase tracking-wider mb-1 sm:mb-2">Mapel</label>
                        <select name="mapel_id" class="w-full text-xs sm:text-sm border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50/50 focus:bg-white transition-all py-2 sm:py-2.5 px-3" required>
                            <option value="">-- Mapel --</option>
                            @foreach ($mapels as $m)
                                <option value="{{ $m->id }}" {{ (request('mapel_id') == $m->id || (isset($selectedMapel) && $selectedMapel == $m->id)) ? 'selected' : '' }}>
                                    {{ $m->nama_mapel }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2 mt-2 md:mt-0 flex gap-2">
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold py-2.5 px-3 rounded-xl shadow-sm hover:shadow transition-all duration-150 flex items-center justify-center gap-1.5 text-xs sm:text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            Cetak Rekap
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Konfirmasi Alpine.js (Di dalam cakupan x-data utama) -->
        <div x-show="openResetModal" 
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto" 
             aria-labelledby="modal-title" role="dialog" aria-modal="true">
            
            <!-- Backdrop Overlay -->
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="openResetModal = false"></div>

            <!-- Modal Panel Container -->
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="openResetModal"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100 z-10">
                    
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <!-- Icon Warning Rose -->
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-rose-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-rose-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                </svg>
                            </div>
                            
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3 class="text-base font-bold leading-6 text-gray-900" id="modal-title">
                                    Konfirmasi Reset Absensi
                                </h3>
                                <div class="mt-2">
                                    <p class="text-xs sm:text-sm text-gray-500">
                                        Apakah Anda yakin ingin menghapus seluruh catatan absensi tanggal <span class="font-bold text-gray-700">{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</span> untuk kelas ini? Data yang dihapus tidak dapat dikembalikan.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Action Buttons -->
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                        <form action="{{ route('guru.absensi.reset') }}" method="POST" class="w-full sm:w-auto">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="kelas_id" value="{{ $selectedKelas }}">
                            <input type="hidden" name="mapel_id" value="{{ $selectedMapel }}">
                            <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                            
                            <button type="submit" 
                                    class="inline-flex w-full justify-center rounded-xl bg-rose-600 px-4 py-2.5 text-xs sm:text-sm font-semibold text-white shadow-sm hover:bg-rose-500 transition-all cursor-pointer">
                                Ya, Reset Sekarang
                            </button>
                        </form>

                        <button type="button" 
                                @click="openResetModal = false" 
                                class="mt-3 sm:mt-0 inline-flex w-full justify-center rounded-xl bg-white px-4 py-2.5 text-xs sm:text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-all cursor-pointer">
                            Batal
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Script JavaScript untuk Geolocation, Device Fingerprint, & Handling Rekap -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Handling Rekap tanpa loader global
            const formRekap = document.getElementById('form-rekap-absensi');
            if (formRekap) {
                formRekap.addEventListener('submit', function (e) {
                    e.stopPropagation();
                }, true);
            }

            // 2. Generasi & Perekaman Token Device Unik ke LocalStorage
            let deviceId = localStorage.getItem('guru_device_id');
            if (!deviceId) {
                deviceId = 'DEV-' + Math.random().toString(36).substring(2, 15) + Date.now().toString(36);
                localStorage.setItem('guru_device_id', deviceId);
            }

            const deviceInput = document.getElementById('input_device_id');
            if (deviceInput) {
                deviceInput.value = deviceId;
            }
        });

        // 3. Fungsi Ambil GPS & Submit Form Absensi
        function validasiDanKirimAbsensi() {
            const btn = document.getElementById('btn-simpan-absensi');
            
            if (!navigator.geolocation) {
                alert("Browser Anda tidak mendukung fitur Geolocation / Pelacakan Lokasi.");
                return;
            }

            btn.disabled = true;
            btn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Mengecek Lokasi GPS...
            `;

            navigator.geolocation.getCurrentPosition(
                function (position) {
                    // Masukkan Lat & Long ke field Hidden
                    document.getElementById('input_latitude').value = position.coords.latitude;
                    document.getElementById('input_longitude').value = position.coords.longitude;

                    // Kirim Form
                    document.getElementById('form-simpan-absensi').submit();
                },
                function (error) {
                    btn.disabled = false;
                    btn.innerHTML = `
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Data Absensi
                    `;

                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            alert("Gagal Absen! Anda harus mengizinkan (Allow) akses lokasi GPS pada browser HP/Laptop Anda.");
                            break;
                        case error.POSITION_UNAVAILABLE:
                            alert("Gagal! Informasi lokasi GPS tidak ditemukan. Pastikan GPS HP aktif.");
                            break;
                        case error.TIMEOUT:
                            alert("Gagal! Waktu pengambilan lokasi habis. Silakan coba klik simpan kembali.");
                            break;
                    }
                },
                {
                    enableHighAccuracy: true, // Presisi tinggi
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        }
    </script>
</x-app-layout>