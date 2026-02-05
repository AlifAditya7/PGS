<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Layanan') }}: {{ $service->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('admin.services.update', $service->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <x-input-label for="name" :value="__('Nama Layanan')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="$service->name" required />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="category" :value="__('Kategori')" />
                                <select name="category" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="consulting" {{ $service->category == 'consulting' ? 'selected' : '' }}>Consulting</option>
                                    <option value="auditing" {{ $service->category == 'auditing' ? 'selected' : '' }}>Auditing</option>
                                    <option value="training" {{ $service->category == 'training' ? 'selected' : '' }}>Training</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="type" :value="__('Tipe Pelaksanaan')" />
                                <select name="type" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="online" {{ $service->type == 'online' ? 'selected' : '' }}>Online</option>
                                    <option value="offline" {{ $service->type == 'offline' ? 'selected' : '' }}>Offline</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <x-input-label for="price" :value="__('Harga (Rp)')" />
                            <x-text-input id="price" name="price" type="number" class="mt-1 block w-full" :value="(int)$service->price" required />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Deskripsi')" />
                            <textarea name="description" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ $service->description }}</textarea>
                        </div>

                        <div x-data="{ benefits: {{ json_encode($service->benefits ?? ['']) }} }">
                            <x-input-label :value="__('Benefits')" />
                            <template x-for="(benefit, index) in benefits" :key="index">
                                <div class="flex mt-2">
                                    <x-text-input name="benefits[]" x-model="benefits[index]" type="text" class="block w-full" />
                                    <button type="button" @click="benefits.splice(index, 1)" class="ml-2 text-red-500">×</button>
                                </div>
                            </template>
                            <button type="button" @click="benefits.push('')" class="mt-2 text-xs text-blue-500">+ Tambah Benefit</button>
                        </div>

                        <div x-data="{ activities: {{ json_encode($service->activities ?? ['']) }} }">
                            <x-input-label :value="__('Rincian Kegiatan (Itinerary)')" />
                            <template x-for="(activity, index) in activities" :key="index">
                                <div class="flex mt-2">
                                    <x-text-input name="activities[]" x-model="activities[index]" type="text" class="block w-full" />
                                    <button type="button" @click="activities.splice(index, 1)" class="ml-2 text-red-500">×</button>
                                </div>
                            </template>
                            <button type="button" @click="activities.push('')" class="mt-2 text-xs text-blue-500">+ Tambah Kegiatan</button>
                        </div>

                        <div class="mt-6">
                            <x-primary-button>{{ __('Update Layanan') }}</x-primary-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
