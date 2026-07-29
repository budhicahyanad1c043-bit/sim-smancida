<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Data Jam Pelajaran') }}
                </h2>
                <p class="text-sm text-gray-600">Kelola alokasi waktu dan urutan jam pelajaran sekolah.</p>
            </div>
            <div class="mt-4 md:mt-0">
                <button onclick="openModal('create')" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition">
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
        importModalOpen: false,
        deleteActionUrl: '',
        deleteItemName: '',
        
        confirmDelete(url, name) {
            this.deleteActionUrl = url;
            this.deleteItemName = name;
            this.deleteModalOpen = true;
        }
    }" class="space-y-4">
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
                
                <!-- Alert Notifikasi Success -->
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                <!-- Tabel Data Jam Pelajaran -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Ke-</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama / Keterangan</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Mulai</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Selesai</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($jamPelajarans ?? [] as $jam)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                            {{ $jam->jam_ke }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ $jam->nama ?? 'Jam Ke-' . $jam->jam_ke }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded-md font-mono">
                                                {{ \Carbon\Carbon::parse($jam->jam_mulai)->format('H:i') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded-md font-mono">
                                                {{ \Carbon\Carbon::parse($jam->jam_selesai)->format('H:i') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <div class="flex justify-center space-x-2">
                                                <!-- Edit Button -->
                                                <button onclick="editModal({{ $jam }})" class="text-amber-600 hover:text-amber-900 bg-amber-50 p-2 rounded-lg hover:bg-amber-100 transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </button>

                                                <!-- Tombol Pemicu Modal Hapus Modern -->
                                                <button type="button" 
                                                    @click="confirmDelete('{{ route('admin.jam-pelajaran.destroy', $jam->id) }}', '{{ addslashes($jam->nama) }}')"
                                                    class="p-1.5 text-gray-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" 
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
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                            Belum ada data jam pelajaran. Silakan tambahkan data baru.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Form (Tambah & Edit) -->
        <div id="jamModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 p-6 relative">
                <h3 id="modalTitle" class="text-lg font-bold text-gray-900 mb-4">Tambah Jam Pelajaran</h3>
                
                <form id="jamForm" action="{{ route('admin.jam-pelajaran.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">

                    <!-- Jam Ke -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jam Ke-</label>
                        <input type="number" name="jam_ke" id="jam_ke" required min="1" placeholder="Contoh: 1" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    </div>

                    <!-- Nama / Keterangan -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan / Label (Opsional)</label>
                        <input type="text" name="nama" id="nama" placeholder="Contoh: Jam Ke-1 / Istirahat" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    </div>

                    <!-- Jam Mulai & Jam Selesai -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai</label>
                            <input type="time" name="jam_mulai" id="jam_mulai" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jam Selesai</label>
                            <input type="time" name="jam_selesai" id="jam_selesai" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        </div>
                    </div>

                <!-- Action Buttons (Diperbaiki jarak dan jalurnya) -->
                    <div class="mt-8 flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" onclick="closeModal()" class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all shadow-sm">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div x-cloak 
                x-show="deleteModalOpen" 
                class="fixed inset-0 z-50 overflow-y-auto" 
                aria-labelledby="modal-title" 
                role="dialog" 
                aria-modal="true">
            
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Backdrop Blur -->
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

                <!-- Modal Dialog -->
                <div x-show="deleteModalOpen" 
                        x-transition:enter="ease-out duration-300" 
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                        x-transition:leave="ease-in duration-200" 
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                        class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full p-6">
                    
                    <div class="sm:flex sm:items-start gap-4">
                        <!-- Icon Warning Badge -->
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
                                    Apakah Anda yakin ingin menghapus data Jam Pelajaran <span class="font-bold text-gray-800" x-text="deleteItemName"></span>? Tindakan ini tidak dapat dibatalkan dan akan menghapus seluruh data jam tersebut.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
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
                                    class="w-full sm:w-auto px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-rose-100 transition duration-150">
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