<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Layanan Saya') }}
        </h2>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b dark:border-gray-700">
                                <th class="py-3 px-4">Layanan & Detail</th>
                                <th class="py-3 px-4">Status & Jadwal</th>
                                <th class="py-3 px-4">Dokumen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                            <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="py-3 px-4">
                                    <div class="font-mono text-xs text-gray-400">{{ $order->order_number }}</div>
                                    <div class="font-bold text-lg">{{ $order->service->name }}</div>
                                    
                                    @if($order->service->activities)
                                        <div class="mt-2">
                                            <p class="text-xs font-bold uppercase text-gray-500">Rencana Kegiatan:</p>
                                            <ul class="list-disc list-inside text-xs mt-1 text-gray-600 dark:text-gray-400">
                                                @foreach($order->service->activities as $act)
                                                    <li>{{ $act }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        {{ $order->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $order->status == 'confirmed' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $order->status == 'paid' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $order->status == 'active' ? 'bg-purple-100 text-purple-800' : '' }}">
                                        {{ ucfirst($order->status) }}
                                    </span>

                                    @if($order->status == 'active' && $order->schedules->first())
                                        @php $sch = $order->schedules->first(); @endphp
                                        <div class="mt-2 p-3 bg-purple-50 dark:bg-gray-900 border border-purple-200 dark:border-purple-900 rounded">
                                            <p class="text-xs font-bold text-purple-700 uppercase">Jadwal Pelaksanaan:</p>
                                            <p class="text-sm font-semibold">{{ $sch->title }}</p>
                                            <p class="text-xs">{{ \Carbon\Carbon::parse($sch->start_time)->format('d M Y, H:i') }} WIB</p>
                                            
                                            @if($sch->location_type == 'online')
                                                <a href="{{ $sch->meeting_link }}" target="_blank" class="mt-2 inline-block bg-blue-600 text-white px-3 py-1 rounded text-xs">Join Online Meeting</a>
                                            @else
                                                <div class="mt-2">
                                                    <p class="text-[10px] font-bold">Lokasi Offline:</p>
                                                    <p class="text-[10px] italic mb-2">{{ $sch->address }}</p>
                                                    <div id="map-{{ $order->id }}" class="h-32 w-full rounded border"></div>
                                                    <script>
                                                        document.addEventListener('DOMContentLoaded', function() {
                                                            var map = L.map('map-{{ $order->id }}').setView([-6.200000, 106.816666], 13);
                                                            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
                                                            L.marker([-6.200000, 106.816666]).addTo(map).bindPopup('Lokasi Pelaksanaan').openPopup();
                                                        });
                                                    </script>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-sm">
                                    @foreach($order->documents as $doc)
                                        <div class="flex flex-col space-y-1">
                                            <div class="flex items-center space-x-2">
                                                <span class="font-medium">{{ str_replace('_', ' ', ucfirst($doc->type)) }}</span>
                                                <span class="text-xs px-1.5 py-0.5 bg-gray-100 dark:bg-gray-700 rounded text-gray-500 uppercase">{{ $doc->status }}</span>
                                            </div>
                                            
                                            @if($doc->type == 'confirmation_letter')
                                                <a href="{{ route('orders.download-letter', $order->id) }}" class="text-blue-500 hover:underline flex items-center text-xs">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                                    Download Surat
                                                </a>

                                                @if(!$order->documents->where('type', 'signed_letter')->where('status', 'uploaded')->first() && !$order->documents->where('type', 'signed_letter')->where('status', 'verified')->first())
                                                    <form action="{{ route('orders.upload-signed-letter', $order->id) }}" method="POST" enctype="multipart/form-data" class="mt-2">
                                                        @csrf
                                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Upload Surat Tanda Tangan:</label>
                                                        <div class="flex items-center mt-1">
                                                            <input type="file" name="signed_file" class="block w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
                                                            <button type="submit" class="ml-2 bg-blue-600 text-white px-2 py-1 rounded text-xs hover:bg-blue-700">Upload</button>
                                                        </div>
                                                    </form>
                                                @else
                                                    <span class="text-xs text-green-600 flex items-center mt-1">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                        Surat sudah diupload
                                                    </span>
                                                @endif
                                            @endif

                                            @if($doc->type == 'invoice')
                                                <a href="{{ route('orders.download-invoice', $order->id) }}" class="text-blue-500 hover:underline flex items-center text-xs">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                    Download Invoice
                                                </a>

                                                @php $payment = $order->payments->first(); @endphp
                                                @if(!$payment)
                                                    <form action="{{ route('orders.upload-payment-proof', $order->id) }}" method="POST" enctype="multipart/form-data" class="mt-2">
                                                        @csrf
                                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Upload Bukti Bayar:</label>
                                                        <div class="flex items-center mt-1">
                                                            <input type="file" name="payment_proof" class="block w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100" required>
                                                            <button type="submit" class="ml-2 bg-green-600 text-white px-2 py-1 rounded text-xs hover:bg-green-700">Bayar</button>
                                                        </div>
                                                    </form>
                                                @else
                                                    <span class="text-xs text-{{ $payment->status == 'paid' ? 'green' : 'yellow' }}-600 flex items-center mt-1">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        Pembayaran: {{ ucfirst($payment->status) }}
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    @endforeach
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-gray-500 italic">Belum ada pendaftaran layanan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
