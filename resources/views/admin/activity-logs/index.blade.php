<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Audit Trail / Log Aktivitas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold mb-6 border-b dark:border-gray-700 pb-2">Jejak Aktivitas Sistem</h3>
                    
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900 font-bold">
                                <th class="py-3 px-4">Waktu</th>
                                <th class="py-3 px-4">User</th>
                                <th class="py-3 px-4">Aksi</th>
                                <th class="py-3 px-4">Keterangan</th>
                                <th class="py-3 px-4">IP Address</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-gray-700">
                            @foreach($logs as $log)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                <td class="py-3 px-4 text-[10px] text-gray-500 italic">
                                    {{ $log->created_at->format('d/m/Y H:i:s') }}
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-bold">{{ $log->user->name ?? 'System' }}</div>
                                    <div class="text-[10px] text-gray-400">{{ $log->user->email ?? '-' }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase
                                        {{ strpos($log->action, 'DELETE') !== false ? 'bg-red-100 text-red-800' : '' }}
                                        {{ strpos($log->action, 'VERIFY') !== false ? 'bg-green-100 text-green-800' : '' }}
                                        {{ strpos($log->action, 'UPDATE') !== false ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ !isset($bg) ? 'bg-gray-100 text-gray-800' : '' }}">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-xs">{{ $log->description }}</td>
                                <td class="py-3 px-4 font-mono text-[10px] text-gray-400">{{ $log->ip_address }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-6">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
