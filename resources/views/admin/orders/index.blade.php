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

                                    {{-- Section Penjadwalan (Multi-Session CRUD) --}}
                                    @if($order->status == 'paid' || $order->status == 'active')
                                        <div class="mt-6 p-4 border rounded-xl bg-slate-50 dark:bg-gray-900/50 dark:border-gray-700" x-data="{ showForm: false, editingSes: null }">
                                            <div class="flex justify-between items-center mb-4">
                                                <h4 class="text-xs font-black uppercase text-slate-500">Manajemen Sesi & Jadwal</h4>
                                                <button @click="showForm = true; editingSes = null" class="bg-blue-600 text-white px-2 py-1 rounded text-[10px] font-bold hover:bg-blue-700">+ Tambah Sesi</button>
                                            </div>

                                            {{-- List Sesi --}}
                                            <div class="space-y-2 mb-4">
                                                @forelse($order->schedules as $sch)
                                                    <div class="p-2 bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-lg shadow-sm flex justify-between items-start">
                                                        <div>
                                                            <div class="text-[10px] font-bold text-blue-600 uppercase">{{ $sch->location_type }}</div>
                                                            <div class="text-xs font-bold">{{ $sch->title }}</div>
                                                            <div class="text-[9px] text-gray-500">{{ \Carbon\Carbon::parse($sch->start_time)->format('d M Y, H:i') }}</div>
                                                        </div>
                                                        <div class="flex space-x-2">
                                                            <button @click="editingSes = {{ json_encode($sch) }}; showForm = true;" class="text-blue-500 hover:underline text-[10px]">Edit</button>
                                                            <form action="{{ route('admin.schedules.destroy', $sch->id) }}" method="POST" onsubmit="return confirm('Hapus sesi ini?')">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="text-red-500 hover:underline text-[10px]">Hapus</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <p class="text-[10px] text-gray-400 italic text-center py-2">Belum ada sesi yang dijadwalkan.</p>
                                                @endforelse
                                            </div>

                                            {{-- Floating Form (Inline Modal feel) --}}
                                            <template x-if="showForm">
                                                <div class="mt-4 p-4 border-t dark:border-gray-700 space-y-3 bg-gray-50 dark:bg-gray-900 rounded-b-xl">
                                                    <h5 class="text-[10px] font-black uppercase" x-text="editingSes ? 'Edit Sesi' : 'Tambah Sesi Baru'"></h5>
                                                    <form :action="editingSes ? '{{ url('admin/schedules') }}/' + editingSes.id : '{{ route('admin.orders.set-schedule', $order->id) }}'" method="POST" class="space-y-2">
                                                        @csrf
                                                        <template x-if="editingSes"><input type="hidden" name="_method" value="PATCH"></template>
                                                        
                                                        <input type="text" name="title" :value="editingSes ? editingSes.title : ''" placeholder="Judul Sesi" class="w-full text-xs p-1.5 rounded dark:bg-gray-800 border-gray-300" required>
                                                        
                                                        <div class="flex space-x-2">
                                                            <div class="flex-1">
                                                                <label class="text-[9px] uppercase text-gray-400">Mulai</label>
                                                                <input type="datetime-local" name="start_time" :value="editingSes ? editingSes.start_time.replace(' ', 'T').substring(0,16) : ''" class="w-full text-xs p-1 rounded dark:bg-gray-800 border-gray-300" required>
                                                            </div>
                                                            <div class="flex-1">
                                                                <label class="text-[9px] uppercase text-gray-400">Selesai</label>
                                                                <input type="datetime-local" name="end_time" :value="editingSes ? editingSes.end_time.replace(' ', 'T').substring(0,16) : ''" class="w-full text-xs p-1 rounded dark:bg-gray-800 border-gray-300" required>
                                                            </div>
                                                        </div>

                                                        <div class="space-y-3" x-data="{ 
                                                            loc: editingSes ? editingSes.location_type : 'online',
                                                            lat: editingSes ? editingSes.latitude : -6.200000,
                                                            lng: editingSes ? editingSes.longitude : 106.816666,
                                                            map: null,
                                                            marker: null,
                                                            searchQ: '',
                                                            initMap() {
                                                                if (this.loc === 'offline' && !this.map) {
                                                                    setTimeout(() => {
                                                                        this.map = L.map('map-picker-' + (editingSes ? editingSes.id : 'new-{{ $order->id }}')).setView([this.lat, this.lng], 13);
                                                                        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(this.map);
                                                                        this.marker = L.marker([this.lat, this.lng], {draggable: true}).addTo(this.map);
                                                                        this.marker.on('dragend', (e) => {
                                                                            let p = e.target.getLatLng(); this.lat = p.lat; this.lng = p.lng;
                                                                        });
                                                                        this.map.invalidateSize();
                                                                    }, 200);
                                                                }
                                                            },
                                                            search() {
                                                                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${this.searchQ}`)
                                                                    .then(r => r.json()).then(d => {
                                                                        if(d.length>0) {
                                                                            this.lat = d[0].lat; this.lng = d[0].lon;
                                                                            this.map.setView([this.lat, this.lng], 15);
                                                                            this.marker.setLatLng([this.lat, this.lng]);
                                                                        }
                                                                    });
                                                            }
                                                        }" x-init="$watch('loc', v => { if(v==='offline') initMap() })">
                                                            
                                                            <div class="flex space-x-4 py-1">
                                                                <label class="inline-flex items-center text-[10px]">
                                                                    <input type="radio" name="location_type" value="online" x-model="loc" class="text-blue-600">
                                                                    <span class="ml-1">Online</span>
                                                                </label>
                                                                <label class="inline-flex items-center text-[10px]">
                                                                    <input type="radio" name="location_type" value="offline" x-model="loc" class="text-blue-600">
                                                                    <span class="ml-1">Offline</span>
                                                                </label>
                                                            </div>

                                                            <div x-show="loc == 'online'">
                                                                <input type="url" name="meeting_link" :value="editingSes ? editingSes.meeting_link : ''" placeholder="Link Meeting" class="w-full text-xs p-1.5 rounded dark:bg-gray-800 border-gray-300">
                                                            </div>

                                                            <div x-show="loc == 'offline'" class="space-y-2">
                                                                <textarea name="address" placeholder="Alamat Lengkap" class="w-full text-xs p-1.5 rounded dark:bg-gray-800 border-gray-300" rows="2" x-text="editingSes ? editingSes.address : ''"></textarea>
                                                                
                                                                <div class="flex space-x-1">
                                                                    <input type="text" x-model="searchQ" placeholder="Cari di peta..." class="flex-1 text-[10px] p-1 rounded border-gray-300 dark:bg-gray-800">
                                                                    <button type="button" @click="search()" class="bg-gray-200 dark:bg-gray-700 px-2 py-1 rounded text-[10px]">Cari</button>
                                                                </div>

                                                                <div :id="'map-picker-' + (editingSes ? editingSes.id : 'new-{{ $order->id }}')" class="h-32 w-full rounded border"></div>
                                                                
                                                                <div class="flex space-x-2">
                                                                    <input type="hidden" name="latitude" x-model="lat">
                                                                    <input type="hidden" name="longitude" x-model="lng">
                                                                    <div class="text-[8px] text-gray-400">Coord: <span x-text="lat"></span>, <span x-text="lng"></span></div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="flex space-x-2 pt-2">
                                                            <button type="submit" class="flex-1 bg-blue-600 text-white text-[10px] font-bold py-1.5 rounded hover:bg-blue-700">Simpan Sesi</button>
                                                            <button type="button" @click="showForm = false; editingSes = null" class="px-3 bg-gray-200 dark:bg-gray-700 text-[10px] rounded">Batal</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </template>
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
