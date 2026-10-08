<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Panel Admin
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <a href="{{ route('admin.lomba') }}" class="block p-6 bg-white shadow sm:rounded-lg hover:shadow-md transition">
                    <p class="text-sm font-medium text-gray-500">Mata Lomba</p>
                    <p class="mt-2 text-3xl font-bold text-blue-700">{{ $jumlahLomba }}</p>
                    <p class="mt-3 text-sm text-blue-600">Kelola lomba &rarr;</p>
                </a>
                <a href="{{ route('admin.siswa') }}" class="block p-6 bg-white shadow sm:rounded-lg hover:shadow-md transition">
                    <p class="text-sm font-medium text-gray-500">Akun Siswa</p>
                    <p class="mt-2 text-3xl font-bold text-blue-700">{{ $jumlahSiswa }}</p>
                    <p class="mt-3 text-sm text-blue-600">Kelola siswa &rarr;</p>
                </a>
                <a href="{{ route('admin.status') }}" class="block p-6 bg-white shadow sm:rounded-lg hover:shadow-md transition">
                    <p class="text-sm font-medium text-gray-500">Pendaftaran</p>
                    <p class="mt-2 text-3xl font-bold text-blue-700">{{ $jumlahPendaftaran }}</p>
                    <p class="mt-3 text-sm text-blue-600">Kelola status peserta &rarr;</p>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
