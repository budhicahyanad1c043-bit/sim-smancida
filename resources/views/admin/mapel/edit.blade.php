<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Mata Pelajaran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('admin.mapel.update', $mapel->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Kode Mapel</label>
                        <input type="text" name="kode_mapel" value="{{ old('kode_mapel', $mapel->kode_mapel) }}" class="w-full border-gray-300 rounded-md shadow-sm" required>
                        @error('kode_mapel') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Nama Mata Pelajaran</label>
                        <input type="text" name="nama_mapel" value="{{ old('nama_mapel', $mapel->nama_mapel) }}" class="w-full border-gray-300 rounded-md shadow-sm" required>
                        @error('nama_mapel') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Deskripsi (Opsional)</label>
                        <textarea name="deskripsi" class="w-full border-gray-300 rounded-md shadow-sm" rows="3">{{ old('deskripsi', $mapel->deskripsi) }}</textarea>
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('admin.mapel.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">Batal</a>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Perbarui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>