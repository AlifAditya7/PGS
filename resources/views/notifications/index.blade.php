<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Pusat Notifikasi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold mb-6 border-b dark:border-gray-700 pb-2">Semua Notifikasi</h3>
                    
                    <div class="space-y-4">
                        @forelse($notifications as $notification)
                            <div class="p-4 border dark:border-gray-700 rounded-lg flex items-start space-x-4 {{ $notification->read_at ? 'opacity-60 bg-gray-50 dark:bg-gray-900/50' : 'bg-white dark:bg-gray-800 shadow-sm border-blue-200 dark:border-blue-900' }}">
                                <div class="flex-shrink-0 mt-1">
                                    @if(!$notification->read_at)
                                        <span class="flex h-2 w-2 rounded-full bg-blue-600"></span>
                                    @else
                                        <span class="flex h-2 w-2 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-center">
                                        <h4 class="text-sm font-bold {{ $notification->read_at ? 'text-gray-600 dark:text-gray-400' : 'text-gray-900 dark:text-white' }}">
                                            {{ $notification->data['title'] }}
                                        </h4>
                                        <span class="text-[10px] text-gray-400 italic">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $notification->data['message'] }}
                                    </p>
                                    
                                    <div class="mt-3">
                                        <a href="{{ route('notifications.read', $notification->id) }}" class="text-[10px] font-bold text-blue-600 hover:underline uppercase tracking-wider">
                                            Buka Detail &rarr;
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-12 text-center text-gray-500 italic">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                Belum ada notifikasi untuk Anda.
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-8">
                        {{ $notifications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
