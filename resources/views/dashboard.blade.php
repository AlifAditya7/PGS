<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold mb-4">Verifikasi Scaffolding Awal PGS</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="border p-4 rounded dark:border-gray-700">
                            <p class="font-semibold">Status Role:</p>
                            <p class="text-green-500">Aktif (Role Anda: {{ auth()->user()->getRoleNames()->first() }})</p>
                        </div>
                        
                        <div class="border p-4 rounded dark:border-gray-700">
                            <p class="font-semibold">PDF Generator:</p>
                            <a href="{{ route('test.pdf') }}" target="_blank" class="text-blue-500 hover:underline">Test Generate PDF</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
