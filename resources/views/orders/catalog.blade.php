<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Katalog Layanan PGS') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($services as $service)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border dark:border-gray-700">
                    <div class="p-6">
                        <span class="px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full uppercase">{{ $service->category }}</span>
                        <h3 class="text-xl font-bold mt-2 text-gray-900 dark:text-gray-100">{{ $service->name }}</h3>
                        <p class="text-2xl font-bold text-green-600 mt-2">Rp {{ number_format($service->price, 0, ',', '.') }}</p>
                        
                        <p class="text-gray-600 dark:text-gray-400 mt-4 text-sm">{{ $service->description }}</p>
                        
                        <div class="mt-4">
                            <h4 class="font-semibold text-gray-700 dark:text-gray-300 text-sm">Benefits:</h4>
                            <ul class="list-disc list-inside text-xs text-gray-500 mt-1">
                                @foreach($service->benefits as $benefit)
                                    <li>{{ $benefit }}</li>
                                @endforeach
                            </ul>
                        </div>

                        @if($service->proposal_path)
                            <div class="mt-4">
                                <a href="{{ asset('storage/' . $service->proposal_path) }}" target="_blank" class="flex items-center justify-center w-full px-4 py-2 text-xs font-bold text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    Download Proposal (PDF)
                                </a>
                            </div>
                        @endif

                        <form action="{{ route('orders.store') }}" method="POST" class="mt-6" onsubmit="return confirm('Apakah Anda yakin ingin mendaftar layanan ini?')">
                            @csrf
                            <input type="hidden" name="service_id" value="{{ $service->id }}">
                            <x-primary-button class="w-full justify-center">
                                {{ __('Daftar Sekarang') }}
                            </x-primary-button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
