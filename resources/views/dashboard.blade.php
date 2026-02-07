<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
        @if(auth()->user()->hasRole('admin'))
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        @endif
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Email Verification Warning --}}
            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div class="mb-8 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 shadow-sm rounded-r-lg flex justify-between items-center">
                    <div>
                        <p class="font-bold">Alamat email belum terverifikasi!</p>
                        <p class="text-sm">Silakan cek email Anda untuk tautan verifikasi. Anda belum bisa melakukan pendaftaran layanan sebelum email terverifikasi.</p>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="text-xs bg-red-200 hover:bg-red-300 px-3 py-1.5 rounded font-black uppercase transition">Verifikasi Sekarang</a>
                </div>
            @endif

            {{-- Welcome Message (Shared) --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 text-gray-900 dark:text-gray-100 flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold">Selamat Datang, {{ Auth::user()->name }}!</h1>
                        <p class="text-gray-500 text-sm">We are the solution for your business need.</p>
                    </div>
                    @unless(auth()->user()->hasRole('admin'))
                        <a href="{{ route('orders.catalog') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition">Lihat Katalog</a>
                    @endunless
                </div>
            </div>

            @if(auth()->user()->hasRole('admin'))
                {{-- ADMIN VIEW --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-b-4 border-blue-500 transform transition duration-500 hover:-translate-y-1 hover:shadow-xl">
                        <div class="text-xs font-bold text-gray-400 uppercase">Total Customers</div>
                        <div class="text-2xl font-black text-gray-800 dark:text-white">{{ $stats['total_users'] }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-b-4 border-purple-500 transform transition duration-500 hover:-translate-y-1 hover:shadow-xl">
                        <div class="text-xs font-bold text-gray-400 uppercase">Total Orders</div>
                        <div class="text-2xl font-black text-gray-800 dark:text-white">{{ $stats['total_orders'] }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-b-4 border-green-500 transform transition duration-500 hover:-translate-y-1 hover:shadow-xl">
                        <div class="text-xs font-bold text-gray-400 uppercase">Revenue</div>
                        <div class="text-xl font-black text-green-600">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-b-4 border-orange-500 transform transition duration-500 hover:-translate-y-1 hover:shadow-xl">
                        <div class="text-xs font-bold text-gray-400 uppercase">Net Profit</div>
                        <div class="text-xl font-black text-orange-600">Rp {{ number_format($stats['total_profit'], 0, ',', '.') }}</div>
                    </div>
                </div>

                {{-- Next Events Highlight (Top 3) --}}
                <div class="mb-8">
                    <h3 class="text-lg font-bold mb-4 dark:text-white uppercase tracking-wider text-gray-500 text-xs text-center">Jadwal Pelaksanaan Terdekat</h3>
                    <div class="flex flex-col md:flex-row gap-4 justify-center">
                        @forelse($upcomingEvents as $event)
                            <div class="w-full @if($upcomingEvents->count() > 1) md:flex-1 @else md:max-w-4xl @endif bg-blue-600 text-white p-6 rounded-xl shadow-lg relative overflow-hidden flex flex-col justify-between transition hover:shadow-2xl">
                                <div class="relative z-10">
                                    <span class="bg-white/20 px-2 py-0.5 rounded text-[10px] font-bold uppercase">{{ $event->location_type }}</span>
                                    <h4 class="text-xl font-black mt-2 leading-tight">{{ $event->title }}</h4>
                                    <p class="text-sm opacity-80 mt-1">{{ $event->order->service->name }}</p>
                                    <p class="text-[11px] opacity-70 mt-3 font-bold">Customer: {{ $event->order->user->name }}</p>
                                </div>
                                <div class="mt-6 flex justify-between items-end relative z-10">
                                    <div>
                                        <div class="text-2xl font-black">{{ \Carbon\Carbon::parse($event->start_time)->format('d M Y') }}</div>
                                        <div class="text-sm opacity-80 font-bold">{{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($event->end_time)->format('H:i') }} WIB</div>
                                    </div>
                                    <a href="{{ route('admin.orders.index') }}" class="text-xs font-black uppercase bg-white text-blue-600 px-4 py-2 rounded-lg shadow-sm hover:bg-blue-50 transition">Lihat Detail</a>
                                </div>
                                {{-- Decorative Pattern --}}
                                <svg class="absolute -right-10 -bottom-10 w-48 h-48 text-white/10" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path></svg>
                            </div>
                        @empty
                            <div class="w-full bg-gray-100 dark:bg-gray-800 p-6 rounded-xl text-center text-gray-500 italic border-2 border-dashed dark:border-gray-700">
                                Tidak ada jadwal aktif mendatang.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    {{-- Pie Chart --}}
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                        <h3 class="text-lg font-bold mb-4 dark:text-white">Distribusi Layanan</h3>
                        <canvas id="categoryChart"></canvas>
                    </div>
                    
                    {{-- Interactive Line Chart --}}
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow md:col-span-2">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold dark:text-white text-blue-600">Tren Finansial</h3>
                            <div class="flex bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                                <button onclick="updateMainChart('daily')" id="btn-daily" class="px-3 py-1 text-xs font-bold rounded-md transition bg-white dark:bg-gray-600 shadow-sm">Harian</button>
                                <button onclick="updateMainChart('monthly')" id="btn-monthly" class="px-3 py-1 text-xs font-bold rounded-md transition text-gray-500 hover:text-gray-700 dark:text-gray-400">Bulanan</button>
                            </div>
                        </div>
                        <canvas id="revenueChart" style="max-height: 300px;"></canvas>
                    </div>
                </div>

                <script>
                    // Category Pie Chart
                    const catLabels = {!! json_encode($chartCategories->pluck('name')) !!};
                    const catData = {!! json_encode($chartCategories->pluck('orders_count')) !!};
                    new Chart(document.getElementById('categoryChart'), {
                        type: 'pie',
                        data: {
                            labels: catLabels,
                            datasets: [{ data: catData, backgroundColor: ['#3b82f6', '#8b5cf6', '#10b981', '#f59e0b', '#ef4444'] }]
                        },
                        options: { plugins: { legend: { position: 'bottom' } } }
                    });

                    // Interactive Financial Chart
                    const dailyData = {!! json_encode($dailyData) !!};
                    const monthlyData = {!! json_encode($monthlyData) !!};
                    let mainChart;

                    function initMainChart(data) {
                        const ctx = document.getElementById('revenueChart').getContext('2d');
                        if (mainChart) mainChart.destroy();

                        mainChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: data.map(i => i.label),
                                datasets: [
                                    {
                                        label: 'Revenue (Rp)',
                                        data: data.map(i => i.revenue),
                                        borderColor: '#3b82f6',
                                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                        fill: true,
                                        tension: 0.3
                                    },
                                    {
                                        label: 'Net Profit (Rp)',
                                        data: data.map(i => i.profit),
                                        borderColor: '#10b981',
                                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                        fill: true,
                                        tension: 0.3
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                interaction: { intersect: false, mode: 'index' },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: { callback: (val) => 'Rp ' + val.toLocaleString() }
                                    }
                                }
                            }
                        });
                    }

                    function updateMainChart(type) {
                        const data = type === 'daily' ? dailyData : monthlyData;
                        initMainChart(data);
                        
                        // Update UI Buttons
                        document.getElementById('btn-daily').classList.toggle('bg-white', type === 'daily');
                        document.getElementById('btn-daily').classList.toggle('dark:bg-gray-600', type === 'daily');
                        document.getElementById('btn-daily').classList.toggle('shadow-sm', type === 'daily');
                        document.getElementById('btn-daily').classList.toggle('text-gray-500', type !== 'daily');

                        document.getElementById('btn-monthly').classList.toggle('bg-white', type === 'monthly');
                        document.getElementById('btn-monthly').classList.toggle('dark:bg-gray-600', type === 'monthly');
                        document.getElementById('btn-monthly').classList.toggle('shadow-sm', type === 'monthly');
                        document.getElementById('btn-monthly').classList.toggle('text-gray-500', type !== 'monthly');
                    }

                    // Initial Load
                    initMainChart(dailyData);
                </script>

            @else
                {{-- CUSTOMER VIEW --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    {{-- Left Col: Upcoming Subscriptions --}}
                    <div class="lg:col-span-2 space-y-6">
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white">Jadwal Mendatang</h3>
                        
                        @forelse($upcomingSchedules as $sch)
                            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border-l-8 border-blue-600 flex justify-between items-center transition hover:shadow-md">
                                <div>
                                    <span class="text-[10px] font-bold uppercase px-2 py-0.5 bg-blue-100 text-blue-700 rounded">{{ $sch->location_type }}</span>
                                    <h4 class="text-lg font-bold mt-1 dark:text-white">{{ $sch->title }}</h4>
                                    <p class="text-sm text-gray-500 italic">{{ $sch->order->service->name }}</p>
                                    <div class="mt-3 flex items-center text-xs text-gray-600 dark:text-gray-400">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        {{ \Carbon\Carbon::parse($sch->start_time)->format('d M Y, H:i') }} - {{ \Carbon\Carbon::parse($sch->end_time)->format('H:i') }} WIB
                                    </div>
                                </div>
                                <a href="{{ route('orders.my-orders') }}" class="text-blue-600 font-bold text-sm hover:underline">Detail</a>
                            </div>
                        @empty
                            <div class="bg-gray-100 dark:bg-gray-800/50 border-2 border-dashed dark:border-gray-700 rounded-xl p-12 text-center">
                                <p class="text-gray-500 italic">Belum ada jadwal pelaksanaan terdekat.</p>
                                <a href="{{ route('orders.catalog') }}" class="mt-4 inline-block text-blue-600 font-bold underline">Daftar Layanan Sekarang</a>
                            </div>
                        @endforelse
                    </div>

                    {{-- Right Col: Offerings / Ads --}}
                    <div class="space-y-6">
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white">Top Services untuk Anda</h3>
                        @foreach($topServices as $service)
                            <a href="{{ route('orders.catalog') }}" class="block group">
                                <div class="bg-gradient-to-br from-blue-600 to-indigo-700 p-6 rounded-xl shadow-lg relative overflow-hidden transition transform hover:scale-[1.02]">
                                    <div class="relative z-10">
                                        <h4 class="text-white font-bold text-lg leading-tight">{{ $service->name }}</h4>
                                        <p class="text-blue-100 text-xs mt-2 line-clamp-2">{{ $service->description }}</p>
                                        <div class="mt-4 text-white text-sm font-black">Mulai Rp {{ number_format($service->price, 0, ',', '.') }}</div>
                                    </div>
                                    {{-- Abstract Background Element --}}
                                    <div class="absolute -right-4 -bottom-4 bg-white/10 w-24 h-24 rounded-full group-hover:scale-150 transition duration-500"></div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                </div>
            @endif
        </div>
    </div>
</x-app-layout>