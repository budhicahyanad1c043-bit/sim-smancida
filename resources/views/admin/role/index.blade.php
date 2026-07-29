<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Kelola Role & Hak Akses') }}
            </h2>
            <a href="{{ route('admin.role.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md font-bold text-sm">
                + Tambah Role
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-3">No</th>
                            <th class="p-3">Nama Role</th>
                            <th class="p-3">Hak Akses (Permissions)</th>
                            <th class="p-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roles as $index => $role)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-3">{{ $roles->firstItem() + $index }}</td>
                                <td class="p-3 font-bold text-indigo-600">{{ $role->name }}</td>
                                <td class="p-3">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse ($role->permissions as $perm)
                                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                                {{ $perm->name }}
                                            </span>
                                        @empty
                                            <span class="text-gray-400 text-xs italic">Belum ada permission</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="p-3 text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('admin.role.edit', $role->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white text-xs px-3 py-1 rounded">Edit</a>
                                        @if($role->name !== 'admin')
                                            <form action="{{ route('admin.role.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus role ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-xs px-3 py-1 rounded">Hapus</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-4 text-center text-gray-500">Belum ada data Role.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $roles->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>