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
                <button onclick="openModal('create')" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
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
            
            <!-- Alert Notifikasi Success -->
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl shadow-xs flex items-center gap-3" role="alert">
                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Mobile View (Tampilan Kartu untuk Layar Kecil) -->
            <div class="grid grid-cols-1 gap-4 md:hidden">
                @forelse($jamPelajarans as $jam)
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700">
                                Jam Ke-{{ $jam->jam_ke }}
                            </span>
                            <div class="flex items-center space-x-1">
                                <button onclick="editModal({{ $jam }})" class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <button type="button" 
                                    @click="confirmDelete('{{ route('admin.jam-pelajaran.destroy', $jam->id) }}', '{{ addslashes($jam->nama ?? 'Jam Ke-' . $jam->jam_ke) }}')"
                                    class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition" 
                                    title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-400 font-medium">Keterangan / Label</div>
                            <div class="text-sm font-semibold text-gray-800">{{ $jam->nama ?? 'Jam Ke-' . $jam->jam_ke }}</div>
                        </div>
                        <div class="flex items-center justify-between pt-1 text-xs">
                            <div>
                                <span class="text-gray-400">Mulai:</span>
                                <span class="ml-1 px-2 py-0.5 bg-blue-50 text-blue-700 rounded-md font-mono font-medium">
                                    {{ \Carbon\Carbon::parse($jam->jam_mulai)->format('H:i') }}
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-400">Selesai:</span>
                                <span class="ml-1 px-2 py-0.5 bg-blue-50 text-blue-700 rounded-md font-mono font-medium">
                                    {{ \Carbon\Carbon::parse($jam->jam_selesai)->format('H:i') }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white p-6 text-center text-gray-500 rounded-xl border border-gray-100">
                        Belum ada data jam pelajaran.
                    </div>
                @endforelse
            </div>

            <!-- PC/Desktop View (Tabel Utama) -->
            <div class="hidden md:block bg-white overflow-hidden shadow-xs rounded-2xl border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50/75">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Jam Ke-</th>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama / Keterangan</th>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Waktu Mulai</th>
                                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Waktu Selesai</th>
                                <th scope="col" class="px-6 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
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
                                            <button onclick="editModal({{ $jam }})" class="text-amber-600 hover:text-amber-900 bg-amber-50 p-2 rounded-lg hover:bg-amber-100 transition" title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>

                                            <button type="button" 
                                                @click="confirmDelete('{{ route('admin.jam-pelajaran.destroy', $jam->id) }}', '{{ addslashes($jam->nama ?? 'Jam Ke-' . $jam->jam_ke) }}')"
                                                class="p-2 text-rose-600 hover:text-rose-900 bg-rose-50 rounded-lg hover:bg-rose-100 transition" 
                                                title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                        Belum ada data jam pelajaran. Silakan tambahkan data baru.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Section Pagination -->
            @if($jamPelajarans->hasPages())
                <div class="pt-2">
                    {{ $jamPelajarans->links() }}
                </div>
            @endif

        </div>

        <!-- Modal Form (Tambah & Edit) -->
        <div id="jamModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/50 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 relative transform transition-all">
                <h3 id="modalTitle" class="text-lg font-bold text-gray-900 mb-4">Tambah Jam Pelajaran</h3>
                
                <form id="jamForm" action="{{ route('admin.jam-pelajaran.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">

                    <div class="space-y-4">
                        <!-- Jam Ke -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-1">Jam Ke-</label>
                            <input type="number" name="jam_ke" id="jam_ke" required min="1" placeholder="Contoh: 1" class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        </div>

                        <!-- Nama / Keterangan -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-1">Keterangan / Label (Opsional)</label>
                            <input type="text" name="nama" id="nama" placeholder="Contoh: Jam Ke-1 / Istirahat" class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        </div>

                        <!-- Jam Mulai & Jam Selesai -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-1">Jam Mulai</label>
                                <input type="time" name="jam_mulai" id="jam_mulai" required class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-1">Jam Selesai</label>
                                <input type="time" name="jam_selesai" id="jam_selesai" required class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-semibold hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition shadow-sm">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Confirm Hapus -->
        <div x-cloak 
             x-show="deleteModalOpen" 
             class="fixed inset-0 z-50 overflow-y-auto" 
             aria-labelledby="modal-title" 
             role="dialog" 
             aria-modal="true">
            
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="deleteModalOpen" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0" 
                     x-transition:enter-end="opacity-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100" 
                     x-transition:leave-end="opacity-0" 
                     @click="deleteModalOpen = false" 
                     class="fixed inset-0 bg-gray-900/40 backdrop-blur-xs transition-opacity"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="deleteModalOpen" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full p-6">
                    
                    <div class="sm:flex sm:items-start gap-4">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-2xl bg-rose-50 sm:mx-0">
                            <svg class="h-6 w-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        
                        <div class="mt-3 text-center sm:mt-0 sm:text-left">
                            <h3 class="text-base font-bold text-gray-900" id="modal-title">
                                Hapus Data Jam Pelajaran
                            </h3>
                            <div class="mt-2">
                                <p class="text-xs text-gray-500 leading-relaxed">
                                    Apakah Anda yakin ingin menghapus data Jam Pelajaran <span class="font-bold text-gray-800" x-text="deleteItemName"></span>? Tindakan ini tidak dapat dibatalkan.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 sm:mt-5 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                        <button type="button" 
                                @click="deleteModalOpen = false" 
                                class="w-full sm:w-auto px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition duration-150">
                            Batal
                        </button>
                        
                        <form :action="deleteActionUrl" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full sm:w-auto px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-xl shadow-xs transition duration-150">
                                Ya, Hapus Data
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Modal -->
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