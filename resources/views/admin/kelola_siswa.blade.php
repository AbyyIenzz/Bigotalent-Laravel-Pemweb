<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Akun Siswa</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded" role="status">{{ session('success') }}</div>
            @endif

            <section class="p-6 bg-white shadow sm:rounded-lg">
                <h3 class="font-semibold text-sm mb-3 text-blue-600">Buat Akun Siswa Baru</h3>
                <form action="{{ route('admin.siswa.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="name" class="sr-only">Nama lengkap siswa</label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Nama Lengkap Siswa" class="border-gray-300 rounded-md w-full" required>
                            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="email" class="sr-only">Email siswa</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Email siswa" class="border-gray-300 rounded-md w-full" required>
                            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="password" class="sr-only">Password minimal 8 karakter</label>
                            <input id="password" type="password" name="password" placeholder="Password (minimal 8 karakter)" class="border-gray-300 rounded-md w-full" minlength="8" required>
                            @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <button type="submit" class="mt-3 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">+ Tambah Siswa</button>
                </form>
            </section>

            <section class="p-6 bg-white shadow sm:rounded-lg overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100 text-left">
                            <th class="border border-gray-300 p-3">Nama Siswa</th>
                            <th class="border border-gray-300 p-3">Email</th>
                            <th class="border border-gray-300 p-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswas as $siswa)
                            <tr class="hover:bg-gray-50">
                                <td class="border border-gray-300 p-3">{{ $siswa->name }}</td>
                                <td class="border border-gray-300 p-3">{{ $siswa->email }}</td>
                                <td class="border border-gray-300 p-3 text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('admin.siswa.edit', $siswa) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm">Edit</a>
                                        <form action="{{ route('admin.siswa.destroy', $siswa) }}" method="POST" onsubmit="return confirm('Yakin hapus akun {{ $siswa->name }}? Semua riwayat lombanya akan ikut terhapus!');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="border border-gray-300 p-3 text-center text-gray-500">Belum ada akun siswa.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </section>
        </div>
    </div>
</x-app-layout>
