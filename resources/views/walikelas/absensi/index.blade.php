<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-bold text-xl sm:text-2xl text-gray-800 leading-tight">
                    {{ __('Absensi Harian Kelas') }}
                </h2>
                <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Kelola dan catat kehadiran harian siswa kelas binaan</p>
            </div>
            
            @if (isset($siswas) && count($siswas) > 0)
                <div class="inline-flex items-center w-fit px-3 py-1.5 rounded-xl text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100 shadow-2xs">
                    <svg class="w-4 h-4 mr-1.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Total: <strong class="ml-1 text-indigo-900">{{ count($siswas) }}</strong> Siswa
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">
            
            <!-- Alert Notifikasi -->
            @if (session('success'))
                <div class="flex items-center justify-between p-4 text-emerald-800 bg-emerald-50 rounded-2xl border border-emerald-200 shadow-2xs" role="alert">
                    <div class="flex items-center gap-2.5">
                        <svg class="flex-shrink-0 w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span class="text-xs sm:text-sm font-semibold">{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold">&times;</button>
                </div>
            @endif

            <!-- Card Filter / Tanggal Sesi & Akses Cetak -->
            <div class="bg-white rounded-2xl shadow-xs border border-gray-100 p-4 sm:p-5">
                <form action="{{ route('walikelas.absensi.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12 gap-3 items-end">
                    
                    <!-- Input Tanggal -->
                    <div class="md:col-span-4">
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Tanggal Sesi Harian</label>
                        <input type="date" name="tanggal" value="{{ $tanggal ?? date('Y-m-d') }}" 
                            class="w-full text-xs sm:text-sm border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50/50 focus:bg-white transition py-2.5 px-3 h-[42px]">
                    </div>

                    <!-- Input Kelas Binaan -->
                    <div class="md:col-span-4">
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Kelas Binaan</label>
                        <input type="text" value="{{ $kelas->nama_kelas ?? 'Kelas Binaan' }}" readonly
                            class="w-full text-xs sm:text-sm border-gray-200 rounded-xl bg-gray-100 text-gray-600 cursor-not-allowed font-semibold py-2.5 px-3 h-[42px]">
                    </div>

                    <!-- Group Tombol Filter & Cetak PDF -->
                    <div class="md:col-span-4 flex items-center gap-2">
                        <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-semibold rounded-xl shadow-xs hover:shadow transition flex items-center justify-center gap-2 text-xs sm:text-sm h-[42px] whitespace-nowrap px-3">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <span>Filter</span>
                        </button>

                        <a href="{{ route('walikelas.absensi.pdf', ['bulan' => date('m', strtotime($tanggal)), 'tahun' => date('Y', strtotime($tanggal))]) }}" 
                        target="_blank"
                        class="flex-1 inline-flex items-center justify-center gap-2 bg-rose-500 hover:bg-rose-600 active:bg-rose-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition h-[42px] whitespace-nowrap px-3">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            <span>Cetak PDF</span>
                        </a>
                    </div>

                </form>
            </div>

            <!-- Form Presensi Harian Utama -->
            @if (isset($siswas) && count($siswas) > 0)
                <form action="{{ route('walikelas.absensi.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
                    <input type="hidden" name="tanggal" value="{{ $tanggal }}">

                    <!-- ================= TAMPILAN MOBILE (Card List) ================= -->
                    <div class="block lg:hidden space-y-3">
                        @foreach ($siswas as $index => $siswa)
                            @php
                                $dataAbsensi = $existingAbsensi->get($siswa->id);
                                $currentStatus = $dataAbsensi ? ucfirst(strtolower($dataAbsensi->status)) : 'Hadir';
                                $currentKeterangan = $dataAbsensi ? $dataAbsensi->keterangan : '';
                            @endphp
                            <div class="p-4 bg-white rounded-2xl border border-gray-100 shadow-xs space-y-3">
                                <!-- Identitas Siswa -->
                                <div class="flex items-center justify-between border-b border-gray-100 pb-2.5">
                                    <div>
                                        <h4 class="font-bold text-gray-800 text-sm">{{ $siswa->nama_siswa }}</h4>
                                        <p class="text-[11px] font-mono text-gray-400">NISN: {{ $siswa->nisn }}</p>
                                    </div>
                                    <span class="px-2.5 py-1 bg-gray-50 text-gray-500 text-[10px] font-bold rounded-lg border border-gray-100">
                                        #{{ $index + 1 }}
                                    </span>
                                </div>

                                <!-- Opsi Kehadiran (Mobile Grid 4 Tombol) -->
                                <div class="grid grid-cols-4 gap-1.5">
                                    <!-- Hadir -->
                                    <div>
                                        <input type="radio" id="m-hadir-{{ $siswa->id }}" name="absensi[{{ $siswa->id }}]" value="Hadir" {{ $currentStatus === 'Hadir' ? 'checked' : '' }} class="peer hidden">
                                        <label for="m-hadir-{{ $siswa->id }}" class="py-2.5 rounded-xl text-xs font-bold text-center border border-gray-200 text-gray-600 bg-white peer-checked:bg-emerald-500 peer-checked:text-white peer-checked:border-emerald-500 cursor-pointer transition block select-none">
                                            Hadir
                                        </label>
                                    </div>

                                    <!-- Izin -->
                                    <div>
                                        <input type="radio" id="m-izin-{{ $siswa->id }}" name="absensi[{{ $siswa->id }}]" value="Izin" {{ $currentStatus === 'Izin' ? 'checked' : '' }} class="peer hidden">
                                        <label for="m-izin-{{ $siswa->id }}" class="py-2.5 rounded-xl text-xs font-bold text-center border border-gray-200 text-gray-600 bg-white peer-checked:bg-sky-500 peer-checked:text-white peer-checked:border-sky-500 cursor-pointer transition block select-none">
                                            Izin
                                        </label>
                                    </div>

                                    <!-- Sakit -->
                                    <div>
                                        <input type="radio" id="m-sakit-{{ $siswa->id }}" name="absensi[{{ $siswa->id }}]" value="Sakit" {{ $currentStatus === 'Sakit' ? 'checked' : '' }} class="peer hidden">
                                        <label for="m-sakit-{{ $siswa->id }}" class="py-2.5 rounded-xl text-xs font-bold text-center border border-gray-200 text-gray-600 bg-white peer-checked:bg-amber-500 peer-checked:text-white peer-checked:border-amber-500 cursor-pointer transition block select-none">
                                            Sakit
                                        </label>
                                    </div>

                                    <!-- Alpa -->
                                    <div>
                                        <input type="radio" id="m-alpa-{{ $siswa->id }}" name="absensi[{{ $siswa->id }}]" value="Alpa" {{ $currentStatus === 'Alpa' ? 'checked' : '' }} class="peer hidden">
                                        <label for="m-alpa-{{ $siswa->id }}" class="py-2.5 rounded-xl text-xs font-bold text-center border border-gray-200 text-gray-600 bg-white peer-checked:bg-rose-500 peer-checked:text-white peer-checked:border-rose-500 cursor-pointer transition block select-none">
                                            Alpa
                                        </label>
                                    </div>
                                </div>

                                <!-- Input Catatan/Keterangan -->
                                <div>
                                    <input type="text" name="keterangan[{{ $siswa->id }}]" value="{{ $currentKeterangan }}" placeholder="Catatan khusus (opsional)..." class="w-full text-xs border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50/50 focus:bg-white transition py-2 px-3">
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- ================= TAMPILAN DESKTOP (Tabel Matriks) ================= -->
                    <div class="hidden lg:block bg-white rounded-2xl shadow-xs border border-gray-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50/80 border-b border-gray-100 text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        <th class="py-3.5 px-4 text-center w-12">No</th>
                                        <th class="py-3.5 px-4 w-32">NISN</th>
                                        <th class="py-3.5 px-4">Nama Lengkap Siswa</th>
                                        <th class="py-3.5 px-4 text-center w-96">Opsi Kehadiran</th>
                                        <th class="py-3.5 px-4">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-xs sm:text-sm">
                                    @foreach ($siswas as $index => $siswa)
                                        @php
                                            $dataAbsensi = $existingAbsensi->get($siswa->id);
                                            $currentStatus = $dataAbsensi ? ucfirst(strtolower($dataAbsensi->status)) : 'Hadir';
                                            $currentKeterangan = $dataAbsensi ? $dataAbsensi->keterangan : '';
                                        @endphp
                                        <tr class="hover:bg-indigo-50/20 transition">
                                            <td class="py-3 px-4 text-center font-medium text-gray-400">{{ $index + 1 }}</td>
                                            <td class="py-3 px-4 font-mono text-xs text-gray-500">{{ $siswa->nisn }}</td>
                                            <td class="py-3 px-4 font-semibold text-gray-800">{{ $siswa->nama_siswa }}</td>
                                            <td class="py-3 px-4">
                                                <div class="flex items-center justify-center gap-1.5">
                                                    <!-- Hadir -->
                                                    <div>
                                                        <input type="radio" id="h-hadir-{{ $siswa->id }}" name="absensi[{{ $siswa->id }}]" value="Hadir" {{ $currentStatus === 'Hadir' ? 'checked' : '' }} class="peer hidden">
                                                        <label for="h-hadir-{{ $siswa->id }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold border border-gray-200 text-gray-600 bg-white peer-checked:bg-emerald-500 peer-checked:text-white peer-checked:border-emerald-500 cursor-pointer transition flex items-center gap-1.5 hover:bg-gray-50 select-none">
                                                            <span class="w-2 h-2 rounded-full bg-emerald-500 peer-checked:bg-white"></span> Hadir
                                                        </label>
                                                    </div>

                                                    <!-- Izin -->
                                                    <div>
                                                        <input type="radio" id="h-izin-{{ $siswa->id }}" name="absensi[{{ $siswa->id }}]" value="Izin" {{ $currentStatus === 'Izin' ? 'checked' : '' }} class="peer hidden">
                                                        <label for="h-izin-{{ $siswa->id }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold border border-gray-200 text-gray-600 bg-white peer-checked:bg-sky-500 peer-checked:text-white peer-checked:border-sky-500 cursor-pointer transition flex items-center gap-1.5 hover:bg-gray-50 select-none">
                                                            <span class="w-2 h-2 rounded-full bg-sky-500 peer-checked:bg-white"></span> Izin
                                                        </label>
                                                    </div>

                                                    <!-- Sakit -->
                                                    <div>
                                                        <input type="radio" id="h-sakit-{{ $siswa->id }}" name="absensi[{{ $siswa->id }}]" value="Sakit" {{ $currentStatus === 'Sakit' ? 'checked' : '' }} class="peer hidden">
                                                        <label for="h-sakit-{{ $siswa->id }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold border border-gray-200 text-gray-600 bg-white peer-checked:bg-amber-500 peer-checked:text-white peer-checked:border-amber-500 cursor-pointer transition flex items-center gap-1.5 hover:bg-gray-50 select-none">
                                                            <span class="w-2 h-2 rounded-full bg-amber-500 peer-checked:bg-white"></span> Sakit
                                                        </label>
                                                    </div>

                                                    <!-- Alpa -->
                                                    <div>
                                                        <input type="radio" id="h-alpa-{{ $siswa->id }}" name="absensi[{{ $siswa->id }}]" value="Alpa" {{ $currentStatus === 'Alpa' ? 'checked' : '' }} class="peer hidden">
                                                        <label for="h-alpa-{{ $siswa->id }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold border border-gray-200 text-gray-600 bg-white peer-checked:bg-rose-500 peer-checked:text-white peer-checked:border-rose-500 cursor-pointer transition flex items-center gap-1.5 hover:bg-gray-50 select-none">
                                                            <span class="w-2 h-2 rounded-full bg-rose-500 peer-checked:bg-white"></span> Alpa
                                                        </label>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3 px-4">
                                                <input type="text" name="keterangan[{{ $siswa->id }}]" value="{{ $currentKeterangan }}" placeholder="Catatan (opsional)..." class="w-full text-xs border-gray-200 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50/50 focus:bg-white transition py-1.5 px-3">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Bottom Bar Action / Tombol Simpan -->
                    <div class="p-4 bg-white lg:bg-gray-50/80 rounded-2xl lg:rounded-2xl border border-gray-100 shadow-lg lg:shadow-xs flex items-center justify-between sticky bottom-4 z-10 backdrop-blur-md">
                        <span class="text-xs text-gray-500 font-medium hidden sm:inline-block">Simpan perubahan data absensi harian kelas binaan.</span>
                        <button type="submit" class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold py-3 sm:py-2.5 px-6 rounded-xl shadow-md transition flex items-center justify-center gap-2 text-xs sm:text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Absensi Harian</span>
                        </button>
                    </div>
                </form>
            @else
                <div class="p-12 text-center bg-white rounded-2xl border border-gray-100 shadow-xs">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <p class="text-xs font-medium text-gray-400">Tidak ada data siswa ditemukan di kelas binaan ini.</p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>