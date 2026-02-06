<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Laporan Keuangan & Analisa Profit') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ 
        openModal: false, 
        activeFinance: null, 
        items: [],
        availableItems: {{ json_encode($availableItems) }},
        availableFacilitators: {{ json_encode($availableFacilitators) }},
        updatePrice(index) {
            let selected = this.items[index];
            let source = selected.type === 'Facilitator' ? this.availableFacilitators : this.availableItems;
            let found = source.find(i => i.name === selected.name);
            if (found) {
                this.items[index].price = found.price;
            }
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Statistik Utama --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 text-gray-900 dark:text-gray-100">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-blue-500">
                    <div class="text-sm font-medium text-gray-500 uppercase">Total Keseluruhan Revenue</div>
                    <div class="text-2xl font-bold text-blue-600">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-red-500">
                    <div class="text-sm font-medium text-gray-500 uppercase">Total Keseluruhan COGS</div>
                    <div class="text-2xl font-bold text-red-600">Rp {{ number_format($totalCogs, 0, ',', '.') }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-green-500">
                    <div class="text-sm font-medium text-gray-500 uppercase">Total Keseluruhan Profit</div>
                    <div class="text-2xl font-bold text-green-600">Rp {{ number_format($totalProfit, 0, ',', '.') }}</div>
                </div>
            </div>

            {{-- Monthly Groups --}}
            <div class="space-y-12">
                @foreach($groupedFinances as $monthYear => $finances)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex justify-between items-center">
                            <h3 class="text-lg font-black text-gray-800 dark:text-white uppercase tracking-widest">{{ $monthYear }}</h3>
                            
                            {{-- Download Button --}}
                            <form action="{{ route('admin.finance.download-report') }}" method="POST">
                                @csrf
                                <input type="hidden" name="month_year" value="{{ $monthYear }}">
                                <button type="submit" class="flex items-center space-x-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-xs font-bold transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <span>Download PDF {{ $monthYear }}</span>
                                </button>
                            </form>
                        </div>
                        
                        <div class="p-6">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="text-xs font-bold text-gray-400 uppercase tracking-wider">
                                        <th class="pb-4 px-4">Order & Customer</th>
                                        <th class="pb-4 px-4 text-right">Revenue</th>
                                        <th class="pb-4 px-4">Rincian Pengeluaran (COGS)</th>
                                        <th class="pb-4 px-4 text-right">Net Profit</th>
                                        <th class="pb-4 px-4 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-900 dark:text-gray-100 divide-y dark:divide-gray-700">
                                    @foreach($finances as $finance)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                        <td class="py-4 px-4">
                                            <div class="font-mono text-[10px] text-gray-400">{{ $finance->order->order_number }}</div>
                                            <div class="font-bold text-sm">{{ $finance->order->service->name }}</div>
                                            <div class="text-[10px] text-gray-500">{{ $finance->order->user->name }}</div>
                                        </td>
                                        <td class="py-4 px-4 text-right font-bold text-blue-600">Rp {{ number_format($finance->revenue, 0, ',', '.') }}</td>
                                        <td class="py-4 px-4 text-xs">
                                            @if($finance->expense_items)
                                                <ul class="space-y-1">
                                                    @foreach($finance->expense_items as $ei)
                                                        <li class="text-gray-500 dark:text-gray-400">• {{ $ei['name'] }} ({{ $ei['qty'] }}x)</li>
                                                    @endforeach
                                                    <li class="font-bold text-red-600">Total COGS: Rp {{ number_format($finance->cogs, 0, ',', '.') }}</li>
                                                </ul>
                                            @else
                                                <span class="text-gray-400 italic">Belum ada rincian</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-right font-bold text-green-600 text-sm">Rp {{ number_format($finance->net_profit, 0, ',', '.') }}</td>
                                        <td class="py-4 px-4 text-center">
                                            <button 
                                                @click="activeFinance = {{ $finance->id }}; items = {{ json_encode($finance->expense_items ?? []) }}; if(items.length === 0) items = [{type: 'Item', name: '', qty: 1, price: 0}]; openModal = true;"
                                                class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 text-[10px] font-black hover:bg-gray-200 transition">
                                                UPDATE COGS
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Floating Modal for COGS --}}
        <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-cloak>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col">
                <div class="p-6 border-b dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-lg font-bold dark:text-white">Rincian Biaya Operasional (COGS)</h3>
                    <button @click="openModal = false" class="text-gray-500 hover:text-gray-700 text-3xl">&times;</button>
                </div>

                <div class="p-6 overflow-y-auto flex-1">
                    <form :action="'{{ url('admin/finance') }}/' + activeFinance + '/update-cogs'" method="POST" id="cogsForm">
                        @csrf
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left border-b dark:border-gray-700 text-gray-500 uppercase text-[10px] tracking-wider font-bold">
                                    <th class="pb-3 px-2">Tipe</th>
                                    <th class="pb-3 px-2">Item/Facilitator</th>
                                    <th class="pb-3 w-20 text-center">Qty</th>
                                    <th class="pb-3 px-2">Harga Satuan</th>
                                    <th class="pb-3 text-right px-2">Total</th>
                                    <th class="pb-3 w-10 text-center"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y dark:divide-gray-700">
                                <template x-for="(item, index) in items" :key="index">
                                    <tr>
                                        <td class="py-3 px-2">
                                            <select x-model="item.type" class="w-full text-xs rounded-lg dark:bg-gray-900 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100">
                                                <option value="Item">Operasional</option>
                                                <option value="Facilitator">Facilitator</option>
                                            </select>
                                        </td>
                                        <td class="py-3 px-2">
                                            <select :name="'items['+index+'][name]'" x-model="item.name" @change="updatePrice(index)" class="w-full text-xs rounded-lg dark:bg-gray-900 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100">
                                                <option value="">-- Pilih --</option>
                                                <template x-if="item.type === 'Item'">
                                                    <template x-for="avail in availableItems">
                                                        <option :value="avail.name" x-text="avail.name" :selected="item.name == avail.name"></option>
                                                    </template>
                                                </template>
                                                <template x-if="item.type === 'Facilitator'">
                                                    <template x-for="avail in availableFacilitators">
                                                        <option :value="avail.name" x-text="avail.name" :selected="item.name == avail.name"></option>
                                                    </template>
                                                </template>
                                            </select>
                                        </td>
                                        <td class="py-3 px-2 text-center">
                                            <input type="number" :name="'items['+index+'][qty]'" x-model.number="item.qty" class="w-16 text-center text-xs rounded-lg dark:bg-gray-900 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100">
                                        </td>
                                        <td class="py-3 px-2">
                                            <input type="number" :name="'items['+index+'][price]'" x-model.number="item.price" class="w-full text-xs rounded-lg dark:bg-gray-900 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100">
                                        </td>
                                        <td class="py-3 text-right font-bold text-gray-900 dark:text-gray-100 px-2" x-text="'Rp ' + (item.qty * item.price).toLocaleString()"></td>
                                        <td class="py-3 text-center">
                                            <button type="button" @click="items.splice(index, 1)" class="text-red-500 hover:text-red-700 font-bold">×</button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>

                        <button type="button" @click="items.push({type: 'Item', name: '', qty: 1, price: 0})" class="mt-6 text-xs bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 px-4 py-2 rounded-lg font-bold hover:bg-blue-100 transition">
                            + Tambah Baris Baru
                        </button>
                    </form>
                </div>

                <div class="p-6 bg-gray-50 dark:bg-gray-900/50 border-t dark:border-gray-700 flex justify-between items-center">
                    <div class="text-right">
                        <span class="text-xs font-medium uppercase text-gray-500">Estimasi Total COGS (Bulan Ini):</span>
                        <div class="text-2xl font-black text-red-600" x-text="'Rp ' + items.reduce((sum, i) => sum + (i.qty * i.price), 0).toLocaleString()"></div>
                    </div>
                    <div class="flex space-x-3">
                        <button type="button" @click="openModal = false" class="px-6 py-2 text-sm font-bold text-gray-500 hover:text-gray-700 transition">Batal</button>
                        <button type="button" @click="document.getElementById('cogsForm').submit()" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-2 rounded-lg text-sm font-black shadow-lg shadow-blue-500/30 transition">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
