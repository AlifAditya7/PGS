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
            
            {{-- Welcome Message (Shared) --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 text-gray-900 dark:text-gray-100 flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold">Selamat Datang, {{ Auth::user()->name }}!</h1>
                        <p class="text-gray-500 text-sm">Aplikasi PGS - Trusted Partner in Consulting & Training.</p>
                    </div>
                    @unless(auth()->user()->hasRole('admin'))
                        <a href="{{ route('orders.catalog') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition">Lihat Katalog</a>
                    @endunless
                </div>
            </div>

            @if(auth()->user()->hasRole('admin'))
                {{-- ADMIN VIEW --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-b-4 border-blue-500">
                        <div class="text-xs font-bold text-gray-400 uppercase">Total Customers</div>
                        <div class="text-2xl font-black text-gray-800 dark:text-white">{{ $stats['total_users'] }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-b-4 border-purple-500">
                        <div class="text-xs font-bold text-gray-400 uppercase">Total Orders</div>
                        <div class="text-2xl font-black text-gray-800 dark:text-white">{{ $stats['total_orders'] }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-b-4 border-green-500">
                        <div class="text-xs font-bold text-gray-400 uppercase">Revenue</div>
                        <div class="text-xl font-black text-green-600">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-b-4 border-orange-500">
                        <div class="text-xs font-bold text-gray-400 uppercase">Net Profit</div>
                        <div class="text-xl font-black text-orange-600">Rp {{ number_format($stats['total_profit'], 0, ',', '.') }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                        <h3 class="text-lg font-bold mb-4 dark:text-white">Distribusi Layanan</h3>
                        <canvas id="categoryChart"></canvas>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                        <h3 class="text-lg font-bold mb-4 dark:text-white">Tren Pendapatan</h3>
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                <script>
                    const catLabels = {!! json_encode($chartCategories->pluck('name')) !!};
                    const catData = {!! json_encode($chartCategories->pluck('orders_count')) !!};
                    new Chart(document.getElementById('categoryChart'), {
                        type: 'pie',
                        data: {
                            labels: catLabels,
                            datasets: [{ data: catData, backgroundColor: ['#3b82f6', '#8b5cf6', '#10b981', '#f59e0b', '#ef4444'] }]
                        }
                    });

                    const revLabels = {!! json_encode($monthlyRevenue->pluck('month')->map(fn($m) => date('F', mktime(0,0,0,$m,1)))) !!};
                    const revData = {!! json_encode($monthlyRevenue->pluck('total')) !!};
                    new Chart(document.getElementById('revenueChart'), {
                        type: 'line',
                        data: {
                            labels: revLabels,
                            datasets: [{ label: 'Revenue (Rp)', data: revData, borderColor: '#10b981', tension: 0.1, fill: true, backgroundColor: 'rgba(16, 185, 129, 0.1)' }]
                        }
                    });
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
                                        {{ \Carbon\Carbon::parse($sch->start_time)->format('d M Y, H:i') }} WIB
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
