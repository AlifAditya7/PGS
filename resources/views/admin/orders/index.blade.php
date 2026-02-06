<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Order PGS (Admin)') }}
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
                            <tr class="border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                                <th class="py-3 px-4">Customer</th>
                                <th class="py-3 px-4">Layanan</th>
                                <th class="py-3 px-4">No. Order</th>
                                <th class="py-3 px-4">Status Order</th>
                                <th class="py-3 px-4">Dokumen & Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="py-3 px-4">
                                    <div class="font-bold">{{ $order->user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $order->user->email }}</div>
                                </td>
                                <td class="py-3 px-4 text-sm">{{ $order->service->name }}</td>
                                <td class="py-3 px-4 font-mono text-sm">{{ $order->order_number }}</td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 uppercase font-semibold">
                                        {{ $order->status }}
                                    </span>
                                    <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" class="mt-2" onsubmit="return confirm('Yakin ingin menghapus seluruh data pendaftaran ini? Tindakan ini tidak bisa dibatalkan.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-[10px] text-red-500 hover:underline italic">Hapus Permanen</button>
                                    </form>
                                </td>
                                <td class="py-3 px-4">
                                    @foreach($order->documents as $doc)
                                        <div class="mb-2 p-2 border dark:border-gray-600 rounded bg-gray-50 dark:bg-gray-900">
                                            <div class="flex justify-between items-center mb-1">
                                                <span class="text-xs font-bold uppercase">{{ str_replace('_', ' ', $doc->type) }}</span>
                                                <span class="text-[10px] px-1 bg-gray-200 dark:bg-gray-700 rounded">{{ $doc->status }}</span>
                                            </div>
                                            
                                            <div class="flex space-x-2">
                                                @if($doc->file_path)
                                                    <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="text-xs text-blue-500 hover:underline">Lihat File</a>
                                                @endif

                                                @if($doc->status == 'uploaded')
                                                    <form action="{{ route('admin.documents.verify', $doc->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="text-xs text-green-500 hover:underline font-bold">Verifikasi & Terbitkan Invoice</button>
                                                    </form>
                                                @elseif($doc->status == 'verified')
                                                    <form action="{{ route('admin.documents.unverify', $doc->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="text-[10px] text-orange-500 hover:underline">Batalkan Verifikasi</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach

                                    {{-- Section Penjadwalan --}}
                                    @if($order->status == 'paid' || $order->status == 'active')
                                        @php $schedule = $order->schedules->first(); @endphp
                                        <div class="mt-4 p-4 border rounded bg-blue-50 dark:bg-gray-900 dark:border-blue-800" 
                                             x-data="{ 
                                                locType: '{{ $schedule->location_type ?? 'online' }}',
                                                lat: '{{ $schedule->latitude ?? -6.200000 }}',
                                                lng: '{{ $schedule->longitude ?? 106.816666 }}',
                                                address: '{{ $schedule->address ?? '' }}',
                                                map: null,
                                                marker: null,
                                                initMap() {
                                                    if (this.locType === 'offline' && !this.map) {
                                                        setTimeout(() => {
                                                            const container = document.getElementById('map-picker-{{ $order->id }}');
                                                            if (!container) return;
                                                            
                                                            this.map = L.map('map-picker-{{ $order->id }}').setView([this.lat, this.lng], 13);
                                                            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(this.map);
                                                            this.marker = L.marker([this.lat, this.lng], {draggable: true}).addTo(this.map);
                                                            
                                                            this.marker.on('dragend', (e) => {
                                                                let pos = e.target.getLatLng();
                                                                this.lat = pos.lat;
                                                                this.lng = pos.lng;
                                                            });

                                                            this.map.on('click', (e) => {
                                                                this.marker.setLatLng(e.latlng);
                                                                this.lat = e.latlng.lat;
                                                                this.lng = e.latlng.lng;
                                                            });
                                                            this.map.invalidateSize();
                                                        }, 300);
                                                    } else if (this.locType === 'offline' && this.map) {
                                                        setTimeout(() => { this.map.invalidateSize(); }, 300);
                                                    }
                                                },
                                                async searchAddress() {
                                                    if(!this.address) return;
                                                    const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${this.address}`);
                                                    const data = await response.json();
                                                    if (data.length > 0) {
                                                        this.lat = data[0].lat;
                                                        this.lng = data[0].lon;
                                                        this.map.setView([this.lat, this.lng], 15);
                                                        this.marker.setLatLng([this.lat, this.lng]);
                                                    } else {
                                                        alert('Alamat tidak ditemukan');
                                                    }
                                                }
                                             }" 
                                             x-init="if(locType === 'offline') initMap(); $watch('locType', value => { if(value === 'offline') initMap() })">
                                            
                                            <h4 class="text-sm font-bold mb-2">{{ $schedule ? 'Update Jadwal' : 'Tetapkan Jadwal' }}</h4>
                                            <form action="{{ $schedule ? route('admin.schedules.update', $schedule->id) : route('admin.orders.set-schedule', $order->id) }}" method="POST" class="space-y-2">
                                                @csrf
                                                @if($schedule) @method('PATCH') @endif
                                                
                                                <input type="text" name="title" value="{{ $schedule->title ?? '' }}" placeholder="Judul Sesi" class="w-full text-xs p-1 rounded dark:bg-gray-800 border-gray-300" required>
                                                
                                                <div class="flex space-x-2">
                                                    <input type="datetime-local" name="start_time" value="{{ $schedule ? date('Y-m-d\TH:i', strtotime($schedule->start_time)) : '' }}" class="w-1/2 text-xs p-1 rounded dark:bg-gray-800 border-gray-300" required>
                                                    <input type="datetime-local" name="end_time" value="{{ $schedule ? date('Y-m-d\TH:i', strtotime($schedule->end_time)) : '' }}" class="w-1/2 text-xs p-1 rounded dark:bg-gray-800 border-gray-300" required>
                                                </div>

                                                <div class="flex space-x-4 mb-2">
                                                    <label class="inline-flex items-center text-xs cursor-pointer">
                                                        <input type="radio" name="location_type" value="online" x-model="locType" class="text-blue-600 focus:ring-blue-500">
                                                        <span class="ml-1">Online</span>
                                                    </label>
                                                    <label class="inline-flex items-center text-xs cursor-pointer">
                                                        <input type="radio" name="location_type" value="offline" x-model="locType" class="text-blue-600 focus:ring-blue-500">
                                                        <span class="ml-1">Offline</span>
                                                    </label>
                                                </div>

                                                <div x-show="locType == 'online'">
                                                    <input type="url" name="meeting_link" value="{{ $schedule->meeting_link ?? '' }}" placeholder="Link Meeting" class="w-full text-xs p-1 rounded dark:bg-gray-800 border-gray-300">
                                                </div>

                                                <div x-show="locType == 'offline'" class="mt-2 space-y-3" x-data="{ searchQuery: '' }">
                                                    {{-- Input Alamat Riil --}}
                                                    <div>
                                                        <label class="text-[10px] font-bold uppercase text-gray-500">Alamat Lengkap (Detail):</label>
                                                        <textarea name="address" x-model="address" placeholder="Contoh: Gedung Graha PGS, Lt. 3, Ruang Consulting" class="w-full text-xs p-1 rounded dark:bg-gray-800 border-gray-300" rows="2">{{ $schedule->address ?? '' }}</textarea>
                                                    </div>

                                                    {{-- Search & Map Picker --}}
                                                    <div>
                                                        <label class="text-[10px] font-bold uppercase text-gray-500">Titik Koordinat Peta:</label>
                                                        <div class="flex space-x-1 mb-2">
                                                            <input type="text" x-model="searchQuery" placeholder="Cari lokasi di peta..." class="flex-1 text-xs p-1 rounded dark:bg-gray-800 border-gray-300">
                                                            <button type="button" @click="
                                                                if(!searchQuery) return;
                                                                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${searchQuery}`)
                                                                    .then(res => res.json())
                                                                    .then(data => {
                                                                        if(data.length > 0) {
                                                                            lat = data[0].lat; lng = data[0].lon;
                                                                            map.setView([lat, lng], 15);
                                                                            marker.setLatLng([lat, lng]);
                                                                        } else { alert('Lokasi tidak ditemukan'); }
                                                                    })
                                                            " class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs hover:bg-blue-200">Cari</button>
                                                        </div>
                                                        
                                                        <div id="map-picker-{{ $order->id }}" class="h-48 w-full rounded border mb-2"></div>
                                                        
                                                        <div class="flex space-x-2">
                                                            <div class="flex-1">
                                                                <span class="text-[9px] text-gray-400 uppercase">Latitude</span>
                                                                <input type="text" name="latitude" x-model="lat" readonly class="w-full text-[10px] p-0 bg-transparent border-none focus:ring-0">
                                                            </div>
                                                            <div class="flex-1">
                                                                <span class="text-[9px] text-gray-400 uppercase">Longitude</span>
                                                                <input type="text" name="longitude" x-model="lng" readonly class="w-full text-[10px] p-0 bg-transparent border-none focus:ring-0">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <button type="submit" class="w-full bg-blue-600 text-white text-xs py-1 rounded hover:bg-blue-700">
                                                    {{ $schedule ? 'Simpan Perubahan' : 'Aktifkan & Set Jadwal' }}
                                                </button>
                                            </form>
                                        </div>
                                    @endif

                                    {{-- Section Pembayaran --}}
                                    @foreach($order->payments as $payment)
                                        <div class="mt-4 p-2 border-2 border-green-500 rounded bg-green-50 dark:bg-gray-900">
                                            <div class="flex justify-between items-center mb-1">
                                                <span class="text-xs font-bold uppercase text-green-700">Bukti Pembayaran</span>
                                                <span class="text-[10px] px-1 bg-green-200 dark:bg-green-800 rounded">{{ $payment->status }}</span>
                                            </div>
                                            
                                            <div class="flex space-x-2">
                                                <a href="{{ asset('storage/' . $payment->proof_path) }}" target="_blank" class="text-xs text-blue-500 hover:underline">Lihat Bukti</a>

                                                @if($payment->status == 'pending')
                                                    <form action="{{ route('admin.payments.verify', $payment->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="text-xs text-green-600 hover:underline font-bold underline">Konfirmasi Bayar</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
