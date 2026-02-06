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
                                    <div class="flex flex-col space-y-2">
                                        <span class="px-2 py-1 text-xs rounded-full inline-block text-center
                                            {{ $order->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $order->status == 'confirmed' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $order->status == 'paid' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $order->status == 'active' ? 'bg-purple-100 text-purple-800' : '' }}">
                                            {{ ucfirst($order->status) }}
                                        </span>

                                        @if($order->status == 'pending')
                                            <form action="{{ route('orders.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pendaftaran ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-[10px] text-red-500 hover:underline w-full text-center">Batalkan</button>
                                            </form>
                                        @endif
                                    </div>

                                    @if($order->status == 'active' && $order->schedules->first())
                                        @php $sch = $order->schedules->first(); @endphp
                                        <div class="mt-2 p-3 bg-purple-50 dark:bg-gray-900 border border-purple-200 dark:border-purple-900 rounded">
                                            <p class="text-xs font-bold text-purple-700 uppercase">Jadwal Pelaksanaan:</p>
                                            <p class="text-sm font-semibold">{{ $sch->title }}</p>
                                            <p class="text-xs">
                                                {{ \Carbon\Carbon::parse($sch->start_time)->format('d M Y, H:i') }} - 
                                                {{ \Carbon\Carbon::parse($sch->end_time)->format('H:i') }} WIB
                                            </p>
                                            
                                            @if($sch->location_type == 'online')
                                                <a href="{{ $sch->meeting_link }}" target="_blank" class="mt-2 block w-full text-center bg-blue-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-blue-700 transition">Join Online Meeting</a>
                                            @else
                                                <div class="mt-2 space-y-2">
                                                    <div class="flex justify-between items-center">
                                                        <p class="text-[10px] font-bold uppercase text-gray-500">Lokasi Offline:</p>
                                                        <a href="https://www.google.com/maps/search/?api=1&query={{ $sch->latitude }},{{ $sch->longitude }}" target="_blank" class="text-[10px] text-blue-600 hover:underline flex items-center font-bold">
                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                            Buka di Google Maps
                                                        </a>
                                                    </div>
                                                    <p class="text-[10px] italic leading-tight text-gray-600 dark:text-gray-400">{{ $sch->address }}</p>
                                                    <div id="map-{{ $order->id }}" class="h-32 w-full rounded-lg border dark:border-gray-700 shadow-inner"></div>
                                                    <script>
                                                        document.addEventListener('DOMContentLoaded', function() {
                                                            var lat = {{ $sch->latitude ?? -6.200000 }};
                                                            var lng = {{ $sch->longitude ?? 106.816666 }};
                                                            var map = L.map('map-{{ $order->id }}', {
                                                                zoomControl: false,
                                                                attributionControl: false
                                                            }).setView([lat, lng], 15);
                                                            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
                                                            L.marker([lat, lng]).addTo(map);
                                                        });
                                                    </script>
                                                </div>
                                            @endif

                                            {{-- Add to Calendar Button --}}
                                            @php
                                                $start = \Carbon\Carbon::parse($sch->start_time)->format('Ymd\THis');
                                                $end = \Carbon\Carbon::parse($sch->end_time)->format('Ymd\THis');
                                                $calUrl = "https://www.google.com/calendar/render?action=TEMPLATE" .
                                                         "&text=" . urlencode($sch->title) .
                                                         "&details=" . urlencode("Layanan PGS: " . $order->service->name) .
                                                         "&location=" . urlencode($sch->location_type == 'online' ? $sch->meeting_link : $sch->address) .
                                                         "&dates=" . $start . "/" . $end;
                                            @endphp
                                            <a href="{{ $calUrl }}" target="_blank" class="mt-3 flex items-center justify-center space-x-1 w-full border border-gray-300 dark:border-gray-600 rounded-lg py-1 text-[10px] font-bold text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                                <svg class="w-3 h-3 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path></svg>
                                                <span>Tambah ke Google Calendar</span>
                                            </a>
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
