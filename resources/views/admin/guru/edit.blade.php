<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Data Guru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('admin.guru.update', $guru->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Nama Lengkap & Gelar</label>
                        <input type="text" name="nama_guru" value="{{ old('nama_guru', $guru->nama_guru) }}" class="w-full border-gray-300 rounded-md shadow-sm" required>
                        @error('nama_guru') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">NIP</label>
                        <input type="text" name="nip" value="{{ old('nip', $guru->nip) }}" class="w-full border-gray-300 rounded-md shadow-sm">
                        @error('nip') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Jenis Kelamin</label>
                        <select name="gender" class="w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="L" {{ old('gender', $guru->gender) == 'L' ? 'selected' : '' }}>Laki-Laki</option>
                            <option value="P" {{ old('gender', $guru->gender) == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Alamat</label>
                        <textarea name="alamat" class="w-full border-gray-300 rounded-md shadow-sm" rows="3">{{ old('alamat', $guru->alamat) }}</textarea>
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('admin.guru.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">Batal</a>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Perbarui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>