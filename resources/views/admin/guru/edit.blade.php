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

                    <h3 class="text-lg font-semibold text-gray-900 border-b pb-2 mb-4">Informasi Utama</h3>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Nama Lengkap & Gelar <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_guru" value="{{ old('nama_guru', $guru->nama_guru) }}" class="w-full border-gray-300 rounded-md shadow-sm" required>
                        @error('nama_guru') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <h3 class="text-lg font-semibold text-gray-900 border-b pb-2 my-4">Identitas Guru</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">NIP</label>
                            <input type="text" name="nip" value="{{ old('nip', $guru->nip) }}" maxlength="18" class="w-full border-gray-300 rounded-md shadow-sm">
                            @error('nip') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">NIK</label>
                            <input type="text" name="nik" value="{{ old('nik', $guru->nik) }}" maxlength="16" class="w-full border-gray-300 rounded-md shadow-sm">
                            @error('nik') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">NUPTK</label>
                            <input type="text" name="nuptk" value="{{ old('nuptk', $guru->nuptk) }}" maxlength="16" class="w-full border-gray-300 rounded-md shadow-sm">
                            @error('nuptk') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $guru->tempat_lahir) }}" class="w-full border-gray-300 rounded-md shadow-sm">
                            @error('tempat_lahir') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Tanggal Lahir</label>
                            <input type="date" name="tgl_lahir" value="{{ old('tgl_lahir', $guru->tgl_lahir) }}" class="w-full border-gray-300 rounded-md shadow-sm">
                            @error('tgl_lahir') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Jenis Kelamin <span class="text-red-500">*</span></label>
                            <select name="gender" class="w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="L" {{ old('gender', $guru->gender) == 'L' ? 'selected' : '' }}>Laki-Laki</option>
                                <option value="P" {{ old('gender', $guru->gender) == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('gender') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold text-gray-900 border-b pb-2 my-4">Alamat Domisili</h3>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Alamat Lengkap</label>
                        <textarea name="alamat" class="w-full border-gray-300 rounded-md shadow-sm" rows="2">{{ old('alamat', $guru->alamat) }}</textarea>
                        @error('alamat') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Desa/Kelurahan</label>
                            <input type="text" name="desa" value="{{ old('desa', $guru->desa) }}" class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Kecamatan</label>
                            <input type="text" name="kecamatan" value="{{ old('kecamatan', $guru->kecamatan) }}" class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Kabupaten/Kota</label>
                            <input type="text" name="kabupaten" value="{{ old('kabupaten', $guru->kabupaten) }}" class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Provinsi</label>
                            <input type="text" name="provinsi" value="{{ old('provinsi', $guru->provinsi) }}" class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-bold mb-2">Kode Pos</label>
                            <input type="text" name="kode_pos" value="{{ old('kode_pos', $guru->kode_pos) }}" class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 border-t pt-4">
                        <a href="{{ route('admin.guru.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">Batal</a>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Perbarui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>