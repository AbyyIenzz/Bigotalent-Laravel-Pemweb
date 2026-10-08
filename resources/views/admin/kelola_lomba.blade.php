<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kelola Mata Lomba</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded" role="status">{{ session('success') }}</div>
            @endif

            <section class="p-6 bg-white shadow sm:rounded-lg">
                <h3 class="text-lg font-bold mb-4 text-blue-600">Tambah Mata Lomba Baru</h3>
                <form action="{{ route('admin.lomba.store') }}" method="POST">
                    @csrf
                    <label for="nama_lomba" class="sr-only">Nama mata lomba</label>
                    <input id="nama_lomba" type="text" name="nama_lomba" value="{{ old('nama_lomba') }}" placeholder="Nama Mata Lomba (Contoh: Web Design)" class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm w-full mb-3" required>
                    @error('nama_lomba') <p class="mb-3 text-sm text-red-600">{{ $message }}</p> @enderror
                    <label for="deskripsi" class="sr-only">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" placeholder="Deskripsi Singkat" class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm w-full mb-3">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi') <p class="mb-3 text-sm text-red-600">{{ $message }}</p> @enderror
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Tambah Mata Lomba
                    </button>
                </form>
            </section>

            <section class="p-6 bg-white shadow sm:rounded-lg overflow-x-auto">
                <h3 class="text-lg font-bold mb-4 text-gray-800">Daftar Mata Lomba</h3>
                <table class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100 text-left">
                            <th class="border border-gray-300 p-3">Nama Lomba</th>
                            <th class="border border-gray-300 p-3">Deskripsi</th>
                            <th class="border border-gray-300 p-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lombas as $lomba)
                            <tr class="hover:bg-gray-50">
                                <td class="border border-gray-300 p-3 font-semibold">{{ $lomba->nama_lomba }}</td>
                                <td class="border border-gray-300 p-3 text-gray-600">{{ $lomba->deskripsi ?: '—' }}</td>
                                <td class="border border-gray-300 p-3 text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('admin.lomba.edit', $lomba) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm">Edit</a>
                                        <form action="{{ route('admin.lomba.destroy', $lomba) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus lomba ini? Pendaftaran terkait juga akan terhapus.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="border border-gray-300 p-3 text-center text-gray-500">Belum ada mata lomba. Gunakan form di atas untuk menambahkan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </section>
        </div>
    </div>
</x-app-layout>
