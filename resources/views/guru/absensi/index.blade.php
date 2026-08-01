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

    <div class="py-4 sm:py-8">
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
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <form action="{{ route('guru.absensi.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="kelas_id" value="{{ $selectedKelas }}">
                        <input type="hidden" name="mapel_id" value="{{ $selectedMapel }}">
                        <input type="hidden" name="tanggal" value="{{ $tanggal }}">

                        <div class="divide-y divide-gray-100">
                            @foreach ($siswas as $index => $siswa)
                                @php
                                    $dataAbsensi = $existingAbsensi->get($siswa->id);
                                    // Default status = 'Hadir' jika belum diisi
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

                        <!-- Bottom Bar Simpan -->
                        <div class="p-3.5 sm:p-4 bg-gray-50/80 border-t border-gray-100 flex items-center justify-between">
                            <span class="text-xs text-gray-500 font-medium hidden sm:inline-block">Pastikan data yang dimasukkan sudah benar sebelum menyimpan.</span>
                            <button type="submit" class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold py-2.5 px-6 rounded-xl shadow-md hover:shadow-lg transition-all duration-150 flex items-center justify-center gap-2 text-xs sm:text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Simpan Data Absensi
                            </button>
                        </div>
                    </form>
                </div>
            @endif
            <!-- Card Filter Rekapitulasi Absensi -->
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

                <!-- Perhatikan penambahan data-no-loader dan target="_blank" -->
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
    </div>
    <!-- Tambahkan script ini di paling bawah file Blade -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const formRekap = document.getElementById('form-rekap-absensi');
            if (formRekap) {
                formRekap.addEventListener('submit', function (e) {
                    // Menghentikan script loading global agar modal loading tidak pernah muncul
                    e.stopPropagation();
                }, true); // `true` untuk menangkap event lebih awal (capture phase)
            }
        });
    </script>
</x-app-layout>