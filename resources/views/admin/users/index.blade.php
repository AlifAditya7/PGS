<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Form Tambah User --}}
                <div class="md:col-span-1">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                        <h3 class="text-lg font-bold mb-4">Tambah User Baru</h3>
                        <form action="{{ route('admin.users.store') }}" method="POST">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <x-input-label for="name" :value="__('Nama')" />
                                    <x-text-input name="name" type="text" class="block mt-1 w-full" required />
                                </div>
                                <div>
                                    <x-input-label for="email" :value="__('Email')" />
                                    <x-text-input name="email" type="email" class="block mt-1 w-full" required />
                                </div>
                                <div>
                                    <x-input-label for="password" :value="__('Password')" />
                                    <x-text-input name="password" type="password" class="block mt-1 w-full" required />
                                </div>
                                <div>
                                    <x-input-label for="role" :value="__('Role')" />
                                    <select name="role" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <x-primary-button class="w-full justify-center">Simpan User</x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Daftar User --}}
                <div class="md:col-span-2">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr class="border-b dark:border-gray-700">
                                        <th class="py-2">Nama</th>
                                        <th class="py-2">Email</th>
                                        <th class="py-2">Role</th>
                                        <th class="py-2">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                    <tr class="border-b dark:border-gray-700">
                                        <td class="py-3">{{ $user->name }}</td>
                                        <td class="py-3">{{ $user->email }}</td>
                                        <td class="py-3">
                                            <span class="px-2 py-1 rounded-full text-[10px] {{ $user->hasRole('admin') ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $user->getRoleNames()->first() }}
                                            </span>
                                        </td>
                                        <td class="py-3">
                                            @if($user->id !== auth()->id())
                                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Yakin hapus user ini?')">
                                                    @csrf @method('DELETE')
                                                    <button class="text-red-500 hover:underline">Hapus</button>
                                                </form>
                                            @endif
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
