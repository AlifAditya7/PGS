<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Laporan Keuangan & Analisa Profit') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ openModal: false, activeFinance: null, items: [] }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Statistik Utama --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-blue-500">
                    <div class="text-sm font-medium text-gray-500 uppercase">Total Revenue</div>
                    <div class="text-2xl font-bold text-blue-600">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-red-500">
                    <div class="text-sm font-medium text-gray-500 uppercase">Total COGS (Operasional)</div>
                    <div class="text-2xl font-bold text-red-600">Rp {{ number_format($totalCogs, 0, ',', '.') }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-green-500">
                    <div class="text-sm font-medium text-gray-500 uppercase">Net Profit</div>
                    <div class="text-2xl font-bold text-green-600">Rp {{ number_format($totalProfit, 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm">
                                <th class="py-3 px-4">No. Order / Layanan</th>
                                <th class="py-3 px-4 text-right">Revenue</th>
                                <th class="py-3 px-4 text-right">Total COGS</th>
                                <th class="py-3 px-4 text-right">Net Profit</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($finances as $finance)
                            <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm">
                                <td class="py-3 px-4">
                                    <div class="font-mono text-xs">{{ $finance->order->order_number }}</div>
                                    <div class="font-bold">{{ $finance->order->service->name }}</div>
                                </td>
                                <td class="py-3 px-4 text-right font-bold text-blue-600">Rp {{ number_format($finance->revenue, 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-right font-bold text-red-600">Rp {{ number_format($finance->cogs, 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-right font-bold text-green-600">Rp {{ number_format($finance->net_profit, 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-center">
                                    <button 
                                        @click="activeFinance = {{ $finance->id }}; items = {{ json_encode($finance->expense_items ?? []) }}; if(items.length === 0) items = [{name: 'Gedung', qty: 1, price: 0}]; openModal = true;"
                                        class="bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded border hover:bg-gray-200 text-xs">
                                        Edit COGS Terperinci
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Floating Modal for COGS --}}
        <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50" x-cloak>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold">Rincian Biaya Operasional (COGS)</h3>
                        <button @click="openModal = false" class="text-gray-500">&times;</button>
                    </div>

                    <form :action="'{{ url('admin/finance') }}/' + activeFinance + '/update-cogs'" method="POST">
                        @csrf
                        <table class="w-full text-sm mb-4">
                            <thead>
                                <tr class="text-left border-b">
                                    <th class="pb-2">Item</th>
                                    <th class="pb-2 w-20">Qty</th>
                                    <th class="pb-2">Harga Satuan</th>
                                    <th class="pb-2 text-right">Total</th>
                                    <th class="pb-2"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, index) in items" :key="index">
                                    <tr class="border-b dark:border-gray-700">
                                        <td class="py-2">
                                            <select :name="'items['+index+'][name]'" x-model="item.name" class="w-full text-xs rounded dark:bg-gray-700 border-gray-300">
                                                <option value="Gedung">Sewa Gedung</option>
                                                <option value="Facilitator">Facilitator / Trainer</option>
                                                <option value="Catering">Konsumsi / Catering</option>
                                                <option value="Transport">Transportasi</option>
                                                <option value="Atk">ATK & Materi</option>
                                                <option value="Lainnya">Lainnya</option>
                                            </select>
                                        </td>
                                        <td class="py-2">
                                            <input type="number" :name="'items['+index+'][qty]'" x-model.number="item.qty" class="w-full text-xs rounded dark:bg-gray-700 border-gray-300">
                                        </td>
                                        <td class="py-2">
                                            <input type="number" :name="'items['+index+'][price]'" x-model.number="item.price" class="w-full text-xs rounded dark:bg-gray-700 border-gray-300">
                                        </td>
                                        <td class="py-2 text-right font-bold" x-text="'Rp ' + (item.qty * item.price).toLocaleString()"></td>
                                        <td class="py-2 text-center">
                                            <button type="button" @click="items.splice(index, 1)" class="text-red-500">&times;</button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>

                        <div class="flex justify-between items-center mt-4">
                            <button type="button" @click="items.push({name: 'Lainnya', qty: 1, price: 0})" class="text-xs text-blue-500">+ Tambah Baris</button>
                            <div class="text-right">
                                <span class="text-sm font-medium uppercase text-gray-500">Estimasi Total COGS:</span>
                                <div class="text-xl font-bold text-red-600" x-text="'Rp ' + items.reduce((sum, i) => sum + (i.qty * i.price), 0).toLocaleString()"></div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" @click="openModal = false" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700">Batal</button>
                            <x-primary-button>Simpan Rincian</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>