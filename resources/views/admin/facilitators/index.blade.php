<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Kelola Facilitator & Trainer') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ editingFac: null }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Form Tambah/Edit --}}
                <div class="md:col-span-1">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                        <h3 class="text-lg font-bold mb-4" x-text="editingFac ? 'Edit Facilitator' : 'Tambah Facilitator'"></h3>
                        
                        <form :action="editingFac ? '{{ url('admin/facilitators') }}/' + editingFac.id : '{{ route('admin.facilitators.store') }}'" method="POST">
                            @csrf
                            <template x-if="editingFac"><input type="hidden" name="_method" value="PUT"></template>
                            
                            <div class="space-y-4">
                                <div>
                                    <x-input-label :value="__('Nama Lengkap')" />
                                    <x-text-input name="name" x-model="editingFac ? editingFac.name : ''" type="text" class="block mt-1 w-full" required />
                                </div>
                                <div>
                                    <x-input-label :value="__('Spesialisasi')" />
                                    <x-text-input name="specialization" x-model="editingFac ? editingFac.specialization : ''" type="text" class="block mt-1 w-full" placeholder="Contoh: Auditor ISO" />
                                </div>
                                <div>
                                    <x-input-label :value="__('Rate / Fee (Rp)')" />
                                    <x-text-input name="price" x-model="editingFac ? editingFac.price : ''" type="number" class="block mt-1 w-full" required />
                                </div>
                                <div class="flex space-x-2">
                                    <x-primary-button class="flex-1 justify-center" x-text="editingFac ? 'Update' : 'Simpan'"></x-primary-button>
                                    <template x-if="editingFac">
                                        <button type="button" @click="editingFac = null" class="px-4 py-2 bg-gray-500 text-white rounded-md text-xs uppercase font-semibold">Batal</button>
                                    </template>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Daftar Facilitator --}}
                <div class="md:col-span-2">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr class="border-b dark:border-gray-700">
                                        <th class="py-2">Nama</th>
                                        <th class="py-2">Spesialisasi</th>
                                        <th class="py-2">Rate</th>
                                        <th class="py-2 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($facilitators as $fac)
                                    <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="py-3 font-bold">{{ $fac->name }}</td>
                                        <td class="py-3 text-gray-500">{{ $fac->specialization }}</td>
                                        <td class="py-3 text-green-600 font-mono text-xs">Rp {{ number_format($fac->price, 0, ',', '.') }}</td>
                                        <td class="py-3 text-right flex justify-end space-x-3">
                                            <button @click="editingFac = {{ json_encode($fac) }}" class="text-blue-500 hover:underline">Edit</button>
                                            <form action="{{ route('admin.facilitators.destroy', $fac->id) }}" method="POST" onsubmit="return confirm('Hapus data ini?')">
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
