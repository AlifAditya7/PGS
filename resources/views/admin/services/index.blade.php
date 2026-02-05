<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Kelola Katalog Layanan') }}
            </h2>
            <a href="{{ route('admin.services.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">Tambah Layanan</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">{{ session('success') }}</div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b dark:border-gray-700">
                                <th class="py-3 px-4">Nama Layanan</th>
                                <th class="py-3 px-4">Kategori</th>
                                <th class="py-3 px-4">Tipe</th>
                                <th class="py-3 px-4">Harga</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($services as $service)
                            <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm">
                                <td class="py-3 px-4 font-bold">{{ $service->name }}</td>
                                <td class="py-3 px-4 capitalize">{{ $service->category }}</td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded {{ $service->type == 'online' ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800' }}">
                                        {{ ucfirst($service->type) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">Rp {{ number_format($service->price, 0, ',', '.') }}</td>
                                <td class="py-3 px-4 flex justify-center space-x-2">
                                    <a href="{{ route('admin.services.edit', $service->id) }}" class="text-blue-500 hover:underline">Edit</a>
                                    <form action="{{ route('admin.services.destroy', $service->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus layanan ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
