<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Order PGS (Admin)') }}
        </h2>
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
                                        <div class="mt-4 p-4 border rounded bg-blue-50 dark:bg-gray-900 dark:border-blue-800">
                                            <h4 class="text-sm font-bold mb-2">{{ $schedule ? 'Update Jadwal' : 'Tetapkan Jadwal' }}</h4>
                                            <form action="{{ $schedule ? route('admin.schedules.update', $schedule->id) : route('admin.orders.set-schedule', $order->id) }}" method="POST" class="space-y-2">
                                                @csrf
                                                @if($schedule) @method('PATCH') @endif
                                                
                                                <input type="text" name="title" value="{{ $schedule->title ?? '' }}" placeholder="Judul Sesi" class="w-full text-xs p-1 rounded dark:bg-gray-800 border-gray-300" required>
                                                
                                                <div class="flex space-x-2">
                                                    <input type="datetime-local" name="start_time" value="{{ $schedule ? date('Y-m-d\TH:i', strtotime($schedule->start_time)) : '' }}" class="w-1/2 text-xs p-1 rounded dark:bg-gray-800 border-gray-300" required>
                                                    <input type="datetime-local" name="end_time" value="{{ $schedule ? date('Y-m-d\TH:i', strtotime($schedule->end_time)) : '' }}" class="w-1/2 text-xs p-1 rounded dark:bg-gray-800 border-gray-300" required>
                                                </div>

                                                <select name="location_type" class="w-full text-xs p-1 rounded dark:bg-gray-800 border-gray-300">
                                                    <option value="online" {{ ($schedule->location_type ?? '') == 'online' ? 'selected' : '' }}>Online</option>
                                                    <option value="offline" {{ ($schedule->location_type ?? '') == 'offline' ? 'selected' : '' }}>Offline (Peta)</option>
                                                </select>

                                                <div x-data="{ locType: '{{ $schedule->location_type ?? 'online' }}' }" @change="locType = $event.target.value">
                                                    <div x-show="locType == 'online'">
                                                        <input type="url" name="meeting_link" value="{{ $schedule->meeting_link ?? '' }}" placeholder="Link Meeting" class="w-full text-xs p-1 rounded dark:bg-gray-800 border-gray-300">
                                                    </div>
                                                    <div x-show="locType == 'offline'" class="mt-2">
                                                        <textarea name="address" placeholder="Alamat Lengkap" class="w-full text-xs p-1 rounded dark:bg-gray-800 border-gray-300">{{ $schedule->address ?? '' }}</textarea>
                                                        <p class="text-[10px] text-gray-500 italic mt-1">*Integrasi Maps akan muncul di dashboard customer.</p>
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
