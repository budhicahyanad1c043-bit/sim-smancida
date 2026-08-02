<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-bold text-xl sm:text-2xl text-gray-800 leading-tight">
                    {{ __('Monitoring Realtime Kegiatan Guru') }}
                </h2>
                <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Pantau kehadiran guru dan aktivitas belajar-mengajar di kelas secara langsung</p>
            </div>
            
            <!-- Indicator Live & Auto Refresh Status -->
            <div class="flex items-center gap-2 self-start sm:self-auto">
                <span class="relative flex h-3 w-3">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                </span>
                <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200">
                    LIVE MONITORING
                </span>
                <span id="last-updated" class="text-xs text-gray-400 font-mono hidden sm:inline-block">
                    Updated: {{ date('H:i:s') }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8" x-data="realtimeMonitoring()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Control & Filter Bar -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <label class="text-xs font-bold text-gray-600 uppercase tracking-wider">Tanggal Monitoring:</label>
                    <input type="date" id="input-tanggal" value="{{ $tanggal }}" 
                           @change="fetchData()"
                           class="text-xs sm:text-sm border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50/50 py-2 px-3">
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    <button @click="fetchData()" class="text-xs font-semibold bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-xl transition-all flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-3.5 h-3.5" :class="loading ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Refresh Manual
                    </button>
                </div>
            </div>

            <!-- Card Metric Quick Summary -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Total Kelas -->
                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Kelas</p>
                        <h3 class="text-2xl font-black text-gray-800 mt-1" x-text="summary.total_kelas">{{ $dataMonitoring['total_kelas'] }}</h3>
                    </div>
                    <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                </div>

                <!-- Kelas Ada Guru (Aktif) -->
                <div class="bg-white rounded-2xl p-5 border border-emerald-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Sedang KBM (Terabsen)</p>
                        <h3 class="text-2xl font-black text-emerald-700 mt-1" x-text="summary.kelas_aktif">{{ $dataMonitoring['kelas_aktif'] }}</h3>
                    </div>
                    <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>

                <!-- Kelas belum Diabsen -->
                <div class="bg-white rounded-2xl p-5 border border-amber-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-amber-600 uppercase tracking-wider">Belum Mengabsen</p>
                        <h3 class="text-2xl font-black text-amber-700 mt-1" x-text="summary.kelas_kosong">{{ $dataMonitoring['kelas_kosong'] }}</h3>
                    </div>
                    <div class="p-3 bg-amber-50 text-amber-600 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>

            <!-- Grid Status Kelas Realtime -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <template x-for="item in listKelas" :key="item.kelas_id">
                    <div class="bg-white rounded-2xl border transition-all duration-300 overflow-hidden shadow-sm hover:shadow-md"
                         :class="item.sudah_diabsen ? 'border-emerald-200' : 'border-gray-200 opacity-80'">
                        
                        <!-- Header Card Kelas -->
                        <div class="p-4 flex items-center justify-between border-b"
                             :class="item.sudah_diabsen ? 'bg-emerald-50/50 border-emerald-100' : 'bg-gray-50 border-gray-100'">
                            <h4 class="font-bold text-base text-gray-800" x-text="item.nama_kelas"></h4>
                            
                            <span class="px-2.5 py-1 text-[10px] font-extrabold uppercase rounded-full tracking-wider"
                                  :class="item.sudah_diabsen ? 'bg-emerald-500 text-white shadow-xs' : 'bg-gray-200 text-gray-600'">
                                <span x-text="item.sudah_diabsen ? 'AKTIF (' + item.waktu_absen + ')' : 'BELUM ABSEN'"></span>
                            </span>
                        </div>

                        <!-- Detail Guru & Mapel -->
                        <div class="p-4 space-y-3">
                            <div>
                                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Guru Pengajar</p>
                                <p class="font-bold text-sm text-gray-800 mt-0.5 truncate" x-text="item.guru_nama"></p>
                            </div>

                            <div>
                                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Mata Pelajaran</p>
                                <p class="font-medium text-xs text-indigo-600 mt-0.5" x-text="item.mapel_nama"></p>
                            </div>

                            <!-- Ringkasan Kehadiran Siswa Jika Sudah Mengabsen -->
                            <template x-if="item.sudah_diabsen && item.ringkasan">
                                <div class="pt-2 border-t border-gray-100">
                                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-2">Kehadiran Siswa</p>
                                    <div class="grid grid-cols-4 gap-1 text-center">
                                        <div class="bg-emerald-50 p-1.5 rounded-lg border border-emerald-100">
                                            <span class="block text-xs font-bold text-emerald-700" x-text="item.ringkasan.hadir"></span>
                                            <span class="text-[9px] text-emerald-600 font-medium">Hadir</span>
                                        </div>
                                        <div class="bg-sky-50 p-1.5 rounded-lg border border-sky-100">
                                            <span class="block text-xs font-bold text-sky-700" x-text="item.ringkasan.izin"></span>
                                            <span class="text-[9px] text-sky-600 font-medium">Izin</span>
                                        </div>
                                        <div class="bg-amber-50 p-1.5 rounded-lg border border-amber-100">
                                            <span class="block text-xs font-bold text-amber-700" x-text="item.ringkasan.sakit"></span>
                                            <span class="text-[9px] text-amber-600 font-medium">Sakit</span>
                                        </div>
                                        <div class="bg-rose-50 p-1.5 rounded-lg border border-rose-100">
                                            <span class="block text-xs font-bold text-rose-700" x-text="item.ringkasan.alpa"></span>
                                            <span class="text-[9px] text-rose-600 font-medium">Alpa</span>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template x-if="!item.sudah_diabsen">
                                <div class="pt-2 border-t border-gray-100 text-center py-2">
                                    <span class="text-xs text-gray-400 italic">Guru belum mengirim data absensi untuk kelas ini</span>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

        </div>
    </div>

    <!-- Script Alpine.js untuk Auto Refresh Realtime (Setiap 10 Detik) -->
    <script>
        function realtimeMonitoring() {
            return {
                loading: false,
                summary: {
                    total_kelas: {{ $dataMonitoring['total_kelas'] }},
                    kelas_aktif: {{ $dataMonitoring['kelas_aktif'] }},
                    kelas_kosong: {{ $dataMonitoring['kelas_kosong'] }}
                },
                listKelas: @json($dataMonitoring['list_kelas']),
                
                init() {
                    // Auto Refresh tiap 10000ms (10 Detik)
                    setInterval(() => {
                        this.fetchData();
                    }, 10000);
                },

                fetchData() {
                    this.loading = true;
                    const tanggal = document.getElementById('input-tanggal').value;

                    fetch(`{{ route('kepsek.realtime-data') }}?tanggal=${tanggal}`)
                        .then(res => res.json())
                        .then(res => {
                            if (res.status === 'success') {
                                this.summary.total_kelas = res.data.total_kelas;
                                this.summary.kelas_aktif = res.data.kelas_aktif;
                                this.summary.kelas_kosong = res.data.kelas_kosong;
                                this.listKelas = res.data.list_kelas;

                                document.getElementById('last-updated').innerText = 'Updated: ' + res.updated_at;
                            }
                        })
                        .catch(err => console.error("Gagal mengambil data realtime:", err))
                        .finally(() => {
                            this.loading = false;
                        });
                }
            }
        }
    </script>
</x-app-layout>