<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Mata Pelajaran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('admin.mapel.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Kode Mapel</label>
                        <input type="text" name="kode_mapel" value="{{ old('kode_mapel') }}" placeholder="Contoh: MAT-10" class="w-full border-gray-300 rounded-md shadow-sm" required>
                        @error('kode_mapel') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Nama Mata Pelajaran</label>
                        <input type="text" name="nama_mapel" value="{{ old('nama_mapel') }}" placeholder="Contoh: Matematika" class="w-full border-gray-300 rounded-md shadow-sm" required>
                        @error('nama_mapel') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Deskripsi (Opsional)</label>
                        <textarea name="deskripsi" class="w-full border-gray-300 rounded-md shadow-sm" rows="3">{{ old('deskripsi') }}</textarea>
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('admin.mapel.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">Batal</a>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>