<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Data Guru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('admin.guru.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Nama Lengkap & Gelar</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="w-full border-gray-300 rounded-md shadow-sm" required>
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Email (Untuk Login)</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="w-full border-gray-300 rounded-md shadow-sm" required>
                        @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">NIP (Opsional)</label>
                        <input type="text" name="nip" value="{{ old('nip') }}" class="w-full border-gray-300 rounded-md shadow-sm">
                        @error('nip') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Jenis Kelamin</label>
                        <select name="gender" class="w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-Laki</option>
                            <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Alamat</label>
                        <textarea name="alamat" class="w-full border-gray-300 rounded-md shadow-sm" rows="3">{{ old('alamat') }}</textarea>
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('admin.guru.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">Batal</a>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>