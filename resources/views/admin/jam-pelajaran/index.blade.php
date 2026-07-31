<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Data Jam Pelajaran') }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">Kelola alokasi waktu dan urutan jam pelajaran sekolah.</p>
            </div>
            <div>
                <button onclick="openModal('create')" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none transition shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Jam Pelajaran
                </button>
            </div>
        </div>
    </x-slot>

    <div x-data="{ 
        deleteModalOpen: false,
        deleteActionUrl: '',
        deleteItemName: '',
        confirmDelete(url, name) {
            this.deleteActionUrl = url;
            this.deleteItemName = name;
            this.deleteModalOpen = true;
        }
    }" class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl shadow-xs flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <!-- TAB FILTER HARI BIASA VS JUMAT -->
            <div class="flex space-x-2 border-b border-gray-200 pb-3">
                <a href="{{ route('admin.jam-pelajaran.index', ['hari' => 'Lainnya']) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $hariFilter === 'Lainnya' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}">
                    Hari Biasa (Senin - Kamis & Sabtu)
                </a>
                <a href="{{ route('admin.jam-pelajaran.index', ['hari' => 'Jumat']) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $hariFilter === 'Jumat' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}">
                    Khusus Hari Jumat
                </a>
            </div>

            <!-- TABEL / LIST DATA -->
            <div class="bg-white overflow-hidden shadow-xs rounded-2xl border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50/75">
                            <tr>
                                <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Jam Ke-</th>
                                <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Kategori Hari</th>
                                <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Keterangan</th>
                                <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Waktu Mulai</th>
                                <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Waktu Selesai</th>
                                <th class="px-6 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($jamPelajarans as $jam)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 text-gray-700">
                                            {{ $jam->jam_ke }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs font-semibold">
                                        <span class="px-2.5 py-1 rounded-full {{ $jam->hari === 'Jumat' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-indigo-50 text-indigo-700 border border-indigo-200' }}">
                                            {{ $jam->hari === 'Jumat' ? 'Jumat' : 'Senin - Kamis & Sabtu' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">
                                        {{ $jam->nama ?? 'Jam Ke-' . $jam->jam_ke }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <span class="px-2.5 py-1 bg-blue-50 text-blue-700 rounded-md font-mono text-xs font-semibold">
                                            {{ \Carbon\Carbon::parse($jam->jam_mulai)->format('H:i') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <span class="px-2.5 py-1 bg-blue-50 text-blue-700 rounded-md font-mono text-xs font-semibold">
                                            {{ \Carbon\Carbon::parse($jam->jam_selesai)->format('H:i') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex justify-center space-x-2">
                                            <button onclick="editModal({{ $jam }})" class="text-amber-600 hover:text-amber-900 bg-amber-50 p-2 rounded-lg" title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>
                                            <button type="button" @click="confirmDelete('{{ route('admin.jam-pelajaran.destroy', $jam->id) }}', 'Jam Ke-{{ $jam->jam_ke }} ({{ $jam->hari }})')" class="p-2 text-rose-600 bg-rose-50 rounded-lg" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                        Belum ada data jam pelajaran untuk kategori ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- MODAL FORM -->
        <div id="jamModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/50 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 relative">
                <h3 id="modalTitle" class="text-lg font-bold text-gray-900 mb-4">Tambah Jam Pelajaran</h3>
                
                <form id="jamForm" action="{{ route('admin.jam-pelajaran.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">

                    <div class="space-y-4">
                        <!-- Pilihan Kategori Hari -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-1">Berlaku Untuk Hari</label>
                            <select name="hari" id="hari" class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                                <option value="Lainnya" {{ $hariFilter === 'Lainnya' ? 'selected' : '' }}>Hari Biasa (Senin - Kamis & Sabtu)</option>
                                <option value="Jumat" {{ $hariFilter === 'Jumat' ? 'selected' : '' }}>Khusus Hari Jumat</option>
                            </select>
                        </div>

                        <!-- Jam Ke -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-1">Jam Ke-</label>
                            <input type="number" name="jam_ke" id="jam_ke" required min="1" placeholder="Contoh: 1" class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:outline-none text-sm">
                        </div>

                        <!-- Nama / Keterangan -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-1">Keterangan / Label (Opsional)</label>
                            <input type="text" name="nama" id="nama" placeholder="Contoh: Jam Ke-1 / Sholat Jumat" class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:outline-none text-sm">
                        </div>

                        <!-- Jam Mulai & Selesai -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-1">Jam Mulai</label>
                                <input type="time" name="jam_mulai" id="jam_mulai" required class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:outline-none text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-1">Jam Selesai</label>
                                <input type="time" name="jam_selesai" id="jam_selesai" required class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:outline-none text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-700 bg-gray-100">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-semibold">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModal(mode = 'create') {
            const modal = document.getElementById('jamModal');
            const form = document.getElementById('jamForm');
            const title = document.getElementById('modalTitle');
            const method = document.getElementById('formMethod');

            if (mode === 'create') {
                title.innerText = 'Tambah Jam Pelajaran';
                form.action = "{{ route('admin.jam-pelajaran.store') }}";
                method.value = 'POST';
                form.reset();
            }
            modal.classList.remove('hidden');
        }

        function editModal(data) {
            const modal = document.getElementById('jamModal');
            const form = document.getElementById('jamForm');
            const title = document.getElementById('modalTitle');
            const method = document.getElementById('formMethod');

            title.innerText = 'Edit Jam Pelajaran';
            let updateUrl = "{{ route('admin.jam-pelajaran.update', ':id') }}";
            form.action = updateUrl.replace(':id', data.id);
            method.value = 'PUT';

            document.getElementById('hari').value = data.hari || 'Lainnya';
            document.getElementById('jam_ke').value = data.jam_ke;
            document.getElementById('nama').value = data.nama || '';
            document.getElementById('jam_mulai').value = data.jam_mulai ? data.jam_mulai.substr(0,5) : '';
            document.getElementById('jam_selesai').value = data.jam_selesai ? data.jam_selesai.substr(0,5) : '';

            modal.classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('jamModal').classList.add('hidden');
        }
    </script>
</x-app-layout>