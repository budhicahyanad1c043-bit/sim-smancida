<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Jadwal Pelajaran Kelas') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- TAMPILAN MOBILE (Card + Tab Hari) -->
            <div class="block lg:hidden" x-data="{ activeTab: 'Senin' }">
                <!-- Tab Navigation -->
                <div class="flex overflow-x-auto space-x-2 border-b border-gray-200 pb-2 mb-4 scrollbar-none">
                    @foreach($hariList as $hari)
                        <button 
                            @click="activeTab = '{{ $hari }}'"
                            :class="activeTab === '{{ $hari }}' ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-700'"
                            class="px-4 py-2 rounded-lg font-medium text-sm whitespace-nowrap transition-colors">
                            {{ $hari }}
                        </button>
                    @endforeach
                </div>

                <!-- Tab Content -->
                @foreach($hariList as $hari)
                    <div x-show="activeTab === '{{ $hari }}'" class="space-y-3">
                        @forelse($jadwal->get($hari, []) as $item)
                            <div class="p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="font-semibold text-gray-800 text-base">{{ $item->mapel->nama_mapel ?? '-' }}</h3>
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-600">
                                        {{ $item->jamPelajaran->waktu_mulai ?? '00:00' }} - {{ $item->jamPelajaran->waktu_selesai ?? '00:00' }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500">Pengajar: <span class="font-medium text-gray-700">{{ $item->guru->nama ?? 'Belum ditentukan' }}</span></p>
                            </div>
                        @empty
                            <div class="p-6 text-center bg-white rounded-xl text-gray-400 text-sm shadow-sm">
                                Tidak ada jadwal pelajaran pada hari {{ $hari }}.
                            </div>
                        @endforelse
                    </div>
                @endforeach
            </div>

            <!-- TAMPILAN PC / DESKTOP (Grid 6 Kolom Hari) -->
            <div class="hidden lg:grid lg:grid-cols-6 gap-4">
                @foreach($hariList as $hari)
                    <div class="bg-gray-100/70 rounded-xl p-3 border border-gray-200">
                        <div class="text-center font-bold text-gray-700 py-2 bg-white rounded-lg shadow-sm border border-gray-100 mb-3">
                            {{ $hari }}
                        </div>
                        <div class="space-y-3">
                            @forelse($jadwal->get($hari, []) as $item)
                                <div class="p-3 bg-white rounded-lg border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                                    <span class="text-[11px] font-medium text-emerald-600 block mb-1">
                                        {{ $item->jamPelajaran->waktu_mulai ?? '00:00' }} - {{ $item->jamPelajaran->waktu_selesai ?? '00:00' }}
                                    </span>
                                    <h4 class="font-semibold text-xs text-gray-800 leading-tight mb-2">{{ $item->mapel->nama_mapel ?? '-' }}</h4>
                                    <div class="pt-2 border-t border-gray-100 text-[11px] text-gray-500 truncate" title="{{ $item->guru->nama ?? 'Belum ditentukan' }}">
                                        {{ $item->guru->nama ?? 'Belum ditentukan' }}
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-6 text-xs text-gray-400">
                                    Libur
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>