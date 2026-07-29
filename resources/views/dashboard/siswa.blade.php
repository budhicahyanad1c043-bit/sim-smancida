<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Portal Informasi Siswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Grid Ringkasan Statistik -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-green-500 text-white p-6 rounded-lg shadow">
                    <div class="text-sm">Total Hadir</div>
                    <div class="text-3xl font-bold">{{ $summary->hadir ?? 0 }}</div>
                </div>
                <div class="bg-blue-500 text-white p-6 rounded-lg shadow">
                    <div class="text-sm">Total Izin</div>
                    <div class="text-3xl font-bold">{{ $summary->izin ?? 0 }}</div>
                </div>
                <div class="bg-yellow-500 text-white p-6 rounded-lg shadow">
                    <div class="text-sm">Total Sakit</div>
                    <div class="text-3xl font-bold">{{ $summary->sakit ?? 0 }}</div>
                </div>
                <div class="bg-red-500 text-white p-6 rounded-lg shadow">
                    <div class="text-sm">Total Alpa</div>
                    <div class="text-3xl font-bold">{{ $summary->alpa ?? 0 }}</div>
                </div>
            </div>

            <!-- Tabel Riwayat Kehadiran -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4 text-gray-800">Riwayat Presensi Harian Terakhir</h3>
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-3">Tanggal</th>
                            <th class="p-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentAbsensi as $absensi)
                            <tr class="border-b">
                                <td class="p-3">{{ \Carbon\Carbon::parse($absensi->tanggal)->translatedFormat('d F Y') }}</td>
                                <td class="p-3 font-bold">
                                    <span class="px-3 py-1 rounded text-sm 
                                        {{ $absensi->status == 'Hadir' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $absensi->status == 'Izin' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $absensi->status == 'Sakit' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $absensi->status == 'Alpa' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ $absensi->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="p-4 text-center text-gray-500">Belum ada riwayat absensi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>