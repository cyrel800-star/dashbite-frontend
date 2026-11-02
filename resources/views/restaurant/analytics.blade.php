@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-5">

    {{-- ============================================ --}}
    {{-- HERO HEADER --}}
    {{-- ============================================ --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-orange-500 via-orange-500 to-orange-600 rounded-2xl shadow-xl text-white">

        {{-- Decorative --}}
        <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full blur-3xl opacity-10 -mr-20 -mt-20"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-yellow-300 rounded-full blur-3xl opacity-20 -ml-16 -mb-16"></div>

        <div class="relative p-6">
            <div class="flex justify-between items-start mb-5">
                <div class="flex items-center gap-3">
                    <a href="{{ route('restaurant.dashboard') }}"
                       class="w-10 h-10 rounded-xl bg-white/15 hover:bg-white/25 backdrop-blur flex items-center justify-center transition border border-white/20">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <p class="text-xs text-white/70 uppercase tracking-wider font-medium">Restaurant</p>
                        <h1 class="text-xl font-bold leading-tight">Sales Analytics</h1>
                    </div>
                </div>

                @if (request('from') || request('to'))
                    <div class="bg-white/15 backdrop-blur rounded-full px-3.5 py-1.5 border border-white/20">
                        <span class="text-xs font-bold">
                            {{ request('from') ?? 'Start' }} → {{ request('to') ?? 'Now' }}
                        </span>
                    </div>
                @endif
            </div>

            {{-- SUMMARY --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 pt-5 border-t border-white/20">
                <div>
                    <p class="text-[10px] text-white/70 uppercase tracking-wider font-medium">Total Sales</p>
                    <p class="text-xl font-bold mt-1">₱{{ number_format($stats['total_sales'], 0) }}</p>
                </div>
                <div class="md:border-l md:border-white/20 md:pl-3">
                    <p class="text-[10px] text-white/70 uppercase tracking-wider font-medium">Orders</p>
                    <p class="text-xl font-bold mt-1">{{ $stats['total_orders'] }}</p>
                </div>
                <div class="md:border-l md:border-white/20 md:pl-3">
                    <p class="text-[10px] text-white/70 uppercase tracking-wider font-medium">Avg Order</p>
                    <p class="text-xl font-bold mt-1">₱{{ number_format($stats['avg_order_value'], 0) }}</p>
                </div>
                <div class="md:border-l md:border-white/20 md:pl-3">
                    <p class="text-[10px] text-white/70 uppercase tracking-wider font-medium">Rating</p>
                    <div class="flex items-center gap-1 mt-1">
                        @if ($stats['avg_rating'] !== 'N/A')
                            <span class="text-xl font-bold">{{ $stats['avg_rating'] }}</span>
                            <svg class="w-4 h-4 fill-yellow-300 text-yellow-300" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        @else
                            <span class="text-sm font-medium text-white/70">N/A</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- FILTERS --}}
    {{-- ============================================ --}}
    <form method="GET" class="bg-white dark:bg-[#141414] rounded-2xl border border-neutral-200 dark:border-[#262626] p-5">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center shadow-sm">
                <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
            </div>
            <p class="text-sm font-bold text-neutral-900 dark:text-white">Date Range</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">From</label>
                <input type="date" name="from" value="{{ request('from') }}"
                       class="w-full border border-neutral-300 dark:border-[#262626] dark:bg-[#0a0a0a] dark:text-white rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
            </div>
            <div>
                <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">To</label>
                <input type="date" name="to" value="{{ request('to') }}"
                       class="w-full border border-neutral-300 dark:border-[#262626] dark:bg-[#0a0a0a] dark:text-white rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
            </div>
            <div class="flex items-end gap-2 md:col-span-2">
                <button type="submit"
                        class="flex-1 bg-gradient-to-r from-orange-500 to-orange-600 text-white px-4 py-2.5 rounded-xl font-semibold text-sm shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg active:scale-98 transition transform">
                    Apply Filter
                </button>
                <a href="{{ route('restaurant.analytics') }}"
                   class="px-4 py-2.5 text-sm font-semibold text-neutral-700 dark:text-neutral-300 border border-neutral-300 dark:border-[#262626] rounded-xl hover:bg-neutral-50 dark:hover:bg-[#0a0a0a] transition">
                    Reset
                </a>
            </div>
        </div>
    </form>

    {{-- ============================================ --}}
    {{-- SALES CHART --}}
    {{-- ============================================ --}}
    <div class="bg-white dark:bg-[#141414] rounded-2xl border border-neutral-200 dark:border-[#262626] p-6">
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center shadow-md">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-neutral-900 dark:text-white">Sales Trend</h2>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Last 7 days</p>
                </div>
            </div>
        </div>

        @if ($dailySales->count() > 0)
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="salesChart"></canvas>
            </div>
        @else
            <div class="py-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-orange-100 to-orange-50 dark:from-orange-950/40 dark:to-orange-900/20 mb-3">
                    <svg class="w-8 h-8 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <p class="text-sm text-neutral-500 dark:text-neutral-400 font-medium">No sales data yet</p>
                <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-1">Complete orders to see your sales trend</p>
            </div>
        @endif
    </div>

    {{-- ============================================ --}}
    {{-- TOP SELLING ITEMS --}}
    {{-- ============================================ --}}
    <div class="bg-white dark:bg-[#141414] rounded-2xl border border-neutral-200 dark:border-[#262626] overflow-hidden">

        <div class="px-6 py-4 border-b border-neutral-100 dark:border-[#262626] flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center shadow-md">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-neutral-900 dark:text-white">Top Selling Items</h2>
                <p class="text-xs text-neutral-500 dark:text-neutral-400">Most popular menu items</p>
            </div>
        </div>

        @if ($topItems->count() > 0)
            <div class="divide-y divide-neutral-100 dark:divide-[#262626]">
                @foreach ($topItems as $index => $item)
                    <div class="px-6 py-4 flex items-center gap-4 hover:bg-neutral-50 dark:hover:bg-[#0a0a0a] transition">
                        {{-- RANK --}}
                        <div class="flex-shrink-0 w-8 h-8 rounded-lg
                            @if($index === 0) bg-gradient-to-br from-orange-500 to-orange-600 text-white shadow-md
                            @elseif($index === 1) bg-gradient-to-br from-neutral-700 to-neutral-800 text-white
                            @elseif($index === 2) bg-gradient-to-br from-neutral-800 to-neutral-900 dark:from-neutral-600 dark:to-neutral-700 text-white
                            @else bg-neutral-100 dark:bg-[#262626] text-neutral-600 dark:text-neutral-400 @endif
                            flex items-center justify-center font-bold text-sm">
                            {{ $index + 1 }}
                        </div>

                        {{-- INFO --}}
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-neutral-900 dark:text-white truncate">{{ $item->name }}</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">
                                {{ $item->total_quantity }} {{ Str::plural('order', $item->total_quantity) }}
                            </p>
                        </div>

                        {{-- REVENUE --}}
                        <div class="text-right flex-shrink-0">
                            <p class="font-bold bg-gradient-to-r from-orange-500 to-orange-600 bg-clip-text text-transparent">₱{{ number_format($item->total_revenue, 0) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-orange-100 to-orange-50 dark:from-orange-950/40 dark:to-orange-900/20 mb-3">
                    <svg class="w-8 h-8 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <p class="text-sm text-neutral-500 dark:text-neutral-400 font-medium">No items sold yet</p>
            </div>
        @endif
    </div>

    {{-- ============================================ --}}
    {{-- DAILY SALES BREAKDOWN --}}
    {{-- ============================================ --}}
    <div class="bg-white dark:bg-[#141414] rounded-2xl border border-neutral-200 dark:border-[#262626] overflow-hidden">

        <div class="px-6 py-4 border-b border-neutral-100 dark:border-[#262626] flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-neutral-800 to-neutral-900 dark:from-neutral-700 dark:to-neutral-800 flex items-center justify-center shadow-md">
                <svg class="w-5 h-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-neutral-900 dark:text-white">Daily Breakdown</h2>
                <p class="text-xs text-neutral-500 dark:text-neutral-400">Sales per day</p>
            </div>
        </div>

        @if ($dailySales->count() > 0)
            <div class="divide-y divide-neutral-100 dark:divide-[#262626]">
                @foreach ($dailySales as $day)
                    <div class="px-6 py-4 flex items-center justify-between hover:bg-neutral-50 dark:hover:bg-[#0a0a0a] transition">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-neutral-100 dark:bg-[#262626] flex items-center justify-center flex-shrink-0 border border-neutral-200 dark:border-[#262626]">
                                <span class="text-xs font-bold text-neutral-700 dark:text-neutral-300">
                                    {{ \Carbon\Carbon::parse($day->date)->format('d') }}
                                </span>
                            </div>
                            <div>
                                <p class="font-semibold text-neutral-900 dark:text-white text-sm">
                                    {{ \Carbon\Carbon::parse($day->date)->format('l') }}
                                </p>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">
                                    {{ \Carbon\Carbon::parse($day->date)->format('M d, Y') }}
                                </p>
                            </div>
                        </div>

                        <div class="text-right">
                            <p class="font-bold bg-gradient-to-r from-orange-500 to-orange-600 bg-clip-text text-transparent">₱{{ number_format($day->total, 0) }}</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">
                                {{ $day->count }} {{ Str::plural('order', $day->count) }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-orange-100 to-orange-50 dark:from-orange-950/40 dark:to-orange-900/20 mb-3">
                    <svg class="w-8 h-8 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <p class="text-sm text-neutral-500 dark:text-neutral-400 font-medium">No daily data yet</p>
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
    function initSalesChart() {
        const canvas = document.getElementById('salesChart');
        if (!canvas) return;

        // Destroy existing chart kung meron
        const existingChart = Chart.getChart(canvas);
        if (existingChart) existingChart.destroy();

        const labels = @json($dailySales->sortBy('date')->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('M d')));
        const data = @json($dailySales->sortBy('date')->pluck('total'));

        // Detect dark mode
        const isDark = document.documentElement.classList.contains('dark');
        const gridColor = isDark ? '#262626' : '#f5f5f5';
        const tickColor = isDark ? '#a3a3a3' : '#737373';
        const pointBorderColor = isDark ? '#0a0a0a' : '#ffffff';

        new Chart(canvas, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Sales (₱)',
                    data: data,
                    borderColor: '#f97316',
                    backgroundColor: 'rgba(249, 115, 22, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#f97316',
                    pointBorderColor: pointBorderColor,
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 500 },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0a0a0a',
                        padding: 12,
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#f97316',
                        borderWidth: 1,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return '₱' + parseFloat(context.parsed.y).toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            },
                            color: tickColor,
                            font: { size: 11, weight: '600' }
                        },
                        grid: { color: gridColor }
                    },
                    x: {
                        ticks: {
                            color: tickColor,
                            font: { size: 11, weight: '600' }
                        },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSalesChart);
    } else {
        initSalesChart();
    }
})();
</script>
@endpush