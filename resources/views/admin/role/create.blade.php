<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Role Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('admin.role.store') }}" method="POST">
                    @csrf

                    <div class="mb-6">
                        <label class="block text-gray-700 font-bold mb-2">Nama Role</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: tata_usaha" class="w-full border-gray-300 rounded-md shadow-sm" required>
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 font-bold mb-2">Pilih Hak Akses (Permissions)</label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 border p-4 rounded-md bg-gray-50">
                            @forelse ($permissions as $perm)
                                <label class="inline-flex items-center text-sm">
                                    <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ml-2 text-gray-700">{{ $perm->name }}</span>
                                </label>
                            @empty
                                <p class="text-xs text-gray-500 col-span-3">Belum ada permission terdaftar di sistem.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('admin.role.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">Batal</a>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Simpan Role</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>