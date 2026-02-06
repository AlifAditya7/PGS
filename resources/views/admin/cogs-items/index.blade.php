<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Kelola Item COGS') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ editingItem: null }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Form Tambah/Edit --}}
                <div class="md:col-span-1">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                        <h3 class="text-lg font-bold mb-4" x-text="editingItem ? 'Edit Item' : 'Tambah Item Baru'"></h3>
                        
                        <form :action="editingItem ? '{{ url('admin/cogs-items') }}/' + editingItem.id : '{{ route('admin.cogs-items.store') }}'" method="POST">
                            @csrf
                            <template x-if="editingItem"><input type="hidden" name="_method" value="PUT"></template>
                            
                            <div class="space-y-4">
                                <div>
                                    <x-input-label :value="__('Nama Item')" />
                                    <x-text-input name="name" x-model="editingItem ? editingItem.name : ''" type="text" class="block mt-1 w-full" required />
                                </div>
                                <div>
                                    <x-input-label :value="__('Harga Dasar (Rp)')" />
                                    <x-text-input name="price" x-model="editingItem ? editingItem.price : ''" type="number" class="block mt-1 w-full" required />
                                </div>
                                <div class="flex space-x-2">
                                    <x-primary-button class="flex-1 justify-center" x-text="editingItem ? 'Update' : 'Simpan'"></x-primary-button>
                                    <template x-if="editingItem">
                                        <button type="button" @click="editingItem = null" class="px-4 py-2 bg-gray-500 text-white rounded-md text-xs uppercase font-semibold">Batal</button>
                                    </template>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Daftar Item --}}
                <div class="md:col-span-2">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr class="border-b dark:border-gray-700">
                                        <th class="py-2">Nama Item</th>
                                        <th class="py-2">Harga Dasar</th>
                                        <th class="py-2 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $item)
                                    <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="py-3 font-bold">{{ $item->name }}</td>
                                        <td class="py-3 text-blue-600 font-mono">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td class="py-3 text-right flex justify-end space-x-3">
                                            <button @click="editingItem = {{ json_encode($item) }}" class="text-blue-500 hover:underline">Edit</button>
                                            <form action="{{ route('admin.cogs-items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus item ini?')">
                                                @csrf @method('DELETE')
                                                <button class="text-red-500 hover:underline">Hapus</button>
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
        </div>
    </div>
</x-app-layout>
