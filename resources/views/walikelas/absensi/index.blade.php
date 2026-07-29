<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Absensi Harian Kelas') }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">Kelola dan catat kehadiran harian siswa kelas binaan</p>
            </div>
            
            @if (isset($siswas) && count($siswas) > 0)
                <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Total: {{ count($siswas) }} Siswa
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Alert Notifikasi -->
            @if (session('success'))
                <div class="flex items-center p-4 text-emerald-800 bg-emerald-50 rounded-xl border border-emerald-200 shadow-sm" role="alert">
                    <svg class="flex-shrink-0 w-5 h-5 me-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <div class="text-sm font-medium">{{ session('success') }}</div>
                </div>
            @endif

            <!-- Card Filter / Tanggal Sesi -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-all hover:shadow-md">
                <form action="{{ route('walikelas.absensi.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                    
                    <div class="md:col-span-5">
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Tanggal Sesi Harian</label>
                        <input type="date" name="tanggal" value="{{ $tanggal ?? date('Y-m-d') }}" 
                            class="w-full text-sm border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50/50 focus:bg-white transition-all py-2.5">
                    </div>

                    <div class="md:col-span-5">
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Kelas Binaan</label>
                        <input type="text" value="{{ $kelas->nama_kelas ?? 'Kelas Binaan' }}" readonly
                            class="w-full text-sm border-gray-200 rounded-xl bg-gray-100 text-gray-600 cursor-not-allowed font-semibold py-2.5">
                    </div>

                    <div class="md:col-span-2">
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-semibold py-2.5 px-4 rounded-xl shadow-sm hover:shadow transition-all duration-150 flex items-center justify-center gap-2 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            Filter
                        </button>
                    </div>
                    <!-- Tombol Aksi Cetak PDF -->
                    <div class="flex items-center gap-2">
                        <a href="{{ route('walikelas.absensi.pdf', ['bulan' => date('m', strtotime($tanggal)), 'tahun' => date('Y', strtotime($tanggal))]) }}" 
                        target="_blank"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 bg-gradient-to-r from-rose-500 to-rose-600 hover:from-rose-600 hover:to-rose-700 active:scale-[0.98] text-white text-xs font-bold rounded-xl shadow-sm hover:shadow-md transition-all duration-150">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            <span>Cetak Rekap PDF</span>
                        </a>
                    </div>
                </form>
            </div>

            <!-- Card Tabel Presensi Harian -->
            @if (isset($siswas) && count($siswas) > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <form action="{{ route('walikelas.absensi.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
                        <input type="hidden" name="tanggal" value="{{ $tanggal }}">

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50/80 border-b border-gray-100 text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        <th class="py-4 px-6 text-center w-16">No</th>
                                        <th class="py-4 px-6">NISN</th>
                                        <th class="py-4 px-6">Nama Lengkap Siswa</th>
                                        <th class="py-4 px-6 text-center">Opsi Kehadiran</th>
                                        <th class="py-4 px-6">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-sm">
                                    @foreach ($siswas as $index => $siswa)
                                        @php
                                            $dataAbsensi = $existingAbsensi->get($siswa->id);
                                            $currentStatus = $dataAbsensi ? ucfirst(strtolower($dataAbsensi->status)) : 'Hadir';
                                            $currentKeterangan = $dataAbsensi ? $dataAbsensi->keterangan : '';
                                        @endphp
                                        <tr class="hover:bg-indigo-50/30 transition-colors">
                                            <td class="py-4 px-6 text-center font-medium text-gray-400">{{ $index + 1 }}</td>
                                            <td class="py-4 px-6 font-mono text-xs text-gray-500">{{ $siswa->nisn }}</td>
                                            <td class="py-4 px-6 font-semibold text-gray-800">{{ $siswa->nama_siswa }}</td>
                                            <td class="py-4 px-6">
                                                <div class="flex items-center justify-center gap-2">
                                                    <div>
                                                        <input type="radio" id="h-hadir-{{ $siswa->id }}" name="absensi[{{ $siswa->id }}]" value="Hadir" {{ $currentStatus === 'Hadir' ? 'checked' : '' }} class="peer hidden">
                                                        <label for="h-hadir-{{ $siswa->id }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold border border-gray-200 text-gray-600 bg-white peer-checked:bg-emerald-500 peer-checked:text-white peer-checked:border-emerald-500 cursor-pointer transition-all flex items-center gap-1.5 hover:bg-gray-50 select-none">
                                                            <span class="w-2 h-2 rounded-full bg-emerald-500 peer-checked:bg-white"></span> Hadir
                                                        </label>
                                                    </div>

                                                    <div>
                                                        <input type="radio" id="h-izin-{{ $siswa->id }}" name="absensi[{{ $siswa->id }}]" value="Izin" {{ $currentStatus === 'Izin' ? 'checked' : '' }} class="peer hidden">
                                                        <label for="h-izin-{{ $siswa->id }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold border border-gray-200 text-gray-600 bg-white peer-checked:bg-sky-500 peer-checked:text-white peer-checked:border-sky-500 cursor-pointer transition-all flex items-center gap-1.5 hover:bg-gray-50 select-none">
                                                            <span class="w-2 h-2 rounded-full bg-sky-500 peer-checked:bg-white"></span> Izin
                                                        </label>
                                                    </div>

                                                    <div>
                                                        <input type="radio" id="h-sakit-{{ $siswa->id }}" name="absensi[{{ $siswa->id }}]" value="Sakit" {{ $currentStatus === 'Sakit' ? 'checked' : '' }} class="peer hidden">
                                                        <label for="h-sakit-{{ $siswa->id }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold border border-gray-200 text-gray-600 bg-white peer-checked:bg-amber-500 peer-checked:text-white peer-checked:border-amber-500 cursor-pointer transition-all flex items-center gap-1.5 hover:bg-gray-50 select-none">
                                                            <span class="w-2 h-2 rounded-full bg-amber-500 peer-checked:bg-white"></span> Sakit
                                                        </label>
                                                    </div>

                                                    <div>
                                                        <input type="radio" id="h-alpa-{{ $siswa->id }}" name="absensi[{{ $siswa->id }}]" value="Alpa" {{ $currentStatus === 'Alpa' ? 'checked' : '' }} class="peer hidden">
                                                        <label for="h-alpa-{{ $siswa->id }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold border border-gray-200 text-gray-600 bg-white peer-checked:bg-rose-500 peer-checked:text-white peer-checked:border-rose-500 cursor-pointer transition-all flex items-center gap-1.5 hover:bg-gray-50 select-none">
                                                            <span class="w-2 h-2 rounded-full bg-rose-500 peer-checked:bg-white"></span> Alpa
                                                        </label>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-6">
                                                <input type="text" name="keterangan[{{ $siswa->id }}]" value="{{ $currentKeterangan }}" placeholder="Catatan (opsional)..." class="w-full text-xs border-gray-200 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50/50 focus:bg-white transition-all py-1.5 px-3">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Bottom Bar -->
                        <div class="p-4 bg-gray-50/80 border-t border-gray-100 flex items-center justify-between">
                            <span class="text-xs text-gray-500 font-medium hidden sm:inline-block">Simpan data absensi harian kelas binaan.</span>
                            <button type="submit" class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-md hover:shadow-lg transition-all duration-150 flex items-center justify-center gap-2 text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Simpan Absensi Harian
                            </button>
                        </div>
                    </form>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>