<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-gray-800">Edit Data Siswa: {{ $siswa->nama_siswa }}</h2>
                <p class="text-xs text-gray-500 mt-0.5">Perbarui data siswa dengan teliti.</p>
            </div>
            <a href="{{ route('admin.siswa.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 font-semibold text-xs rounded-xl hover:bg-gray-200 transition">
                Kembali
            </a>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.siswa.update', $siswa->id) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Card 1: Data Akademik & Akun -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-xs space-y-4">
            <h3 class="text-sm font-bold text-indigo-600 uppercase tracking-wider">1. Data Akademik & Akun</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Nama Siswa *</label>
                    <input type="text" name="nama_siswa" value="{{ old('nama_siswa', $siswa->nama_siswa) }}" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Kelas</label>
                    <select name="kelas_id" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelases as $kelas)
                            <option value="{{ $kelas->id }}" {{ old('kelas_id', $siswa->kelas_id) == $kelas->id ? 'selected' : '' }}>{{ $kelas->nama_kelas ?? $kelas->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Hubungkan Akun User</label>
                    <select name="user_id" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium">
                        <option value="">-- Tanpa Akun User --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id', $siswa->user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Card 2: Data Pribadi -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-xs space-y-4">
            <h3 class="text-sm font-bold text-indigo-600 uppercase tracking-wider">2. Data Pribadi</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Jenis Kelamin *</label>
                    <select name="gender" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium">
                        <option value="L" {{ old('gender', $siswa->gender) == 'L' ? 'selected' : '' }}>Laki-Laki</option>
                        <option value="P" {{ old('gender', $siswa->gender) == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">NISN (10 Digit)</label>
                    <input type="text" name="nisn" maxlength="10" value="{{ old('nisn', $siswa->nisn) }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">NIK (16 Digit)</label>
                    <input type="text" name="nik" maxlength="16" value="{{ old('nik', $siswa->nik) }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">NIS Sekolah</label>
                    <input type="text" name="nis" maxlength="10" value="{{ old('nis', $siswa->nis) }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $siswa->tempat_lahir) }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Tanggal Lahir</label>
                    <input type="date" name="tgl_lahir" value="{{ old('tgl_lahir', $siswa->tgl_lahir) }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Agama</label>
                    <input type="text" name="agama" value="{{ old('agama', $siswa->agama) }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium">
                </div>
            </div>
        </div>

        <!-- Card 3: Data Orang Tua -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-xs space-y-4">
            <h3 class="text-sm font-bold text-indigo-600 uppercase tracking-wider">3. Data Orang Tua</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Nama Ayah</label>
                    <input type="text" name="nama_ayah" value="{{ old('nama_ayah', $siswa->nama_ayah) }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">NIK Ayah</label>
                    <input type="text" name="nik_ayah" maxlength="16" value="{{ old('nik_ayah', $siswa->nik_ayah) }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Nama Ibu</label>
                    <input type="text" name="nama_ibu" value="{{ old('nama_ibu', $siswa->nama_ibu) }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">NIK Ibu</label>
                    <input type="text" name="nik_ibu" maxlength="16" value="{{ old('nik_ibu', $siswa->nik_ibu) }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium">
                </div>
            </div>
        </div>

        <!-- Card 4: Alamat Domisili -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-xs space-y-4">
            <h3 class="text-sm font-bold text-indigo-600 uppercase tracking-wider">4. Alamat Tempat Tinggal</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-3">
                    <label class="block text-xs font-bold text-gray-700 mb-1">Alamat Jalan / RT / RW</label>
                    <textarea name="alamat" rows="2" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium">{{ old('alamat', $siswa->alamat) }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Desa / Kelurahan</label>
                    <input type="text" name="desa" value="{{ old('desa', $siswa->desa) }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Kecamatan</label>
                    <input type="text" name="kecamatan" value="{{ old('kecamatan', $siswa->kecamatan) }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Kabupaten / Kota</label>
                    <input type="text" name="kabupaten" value="{{ old('kabupaten', $siswa->kabupaten) }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Provinsi</label>
                    <input type="text" name="provinsi" value="{{ old('provinsi', $siswa->provinsi) }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Kode Pos</label>
                    <input type="text" name="kode_pos" value="{{ old('kode_pos', $siswa->kode_pos) }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium">
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md transition">
                Perbarui Data Siswa
            </button>
        </div>
    </form>
</x-app-layout>