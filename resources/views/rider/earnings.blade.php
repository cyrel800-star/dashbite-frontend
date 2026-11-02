@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">

    {{-- HEADER --}}
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-neutral-100">Earnings</h1>
            <p class="text-sm text-gray-500 dark:text-neutral-400 mt-1">Track your delivery income</p>
        </div>

        <a href="{{ route('rider.dashboard') }}"
           class="text-sm text-gray-500 dark:text-neutral-400 hover:text-gray-700 dark:hover:text-gray-300">
            ← Back
        </a>
    </div>

    {{-- PERIOD SELECTOR --}}
    <div class="flex gap-2 mb-6">
        <a href="{{ route('rider.earnings', ['period' => '7days']) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $period === '7days' ? 'bg-orange-500 text-white' : 'bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800' }}">
            Last 7 Days
        </a>
        <a href="{{ route('rider.earnings', ['period' => '30days']) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $period === '30days' ? 'bg-orange-500 text-white' : 'bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800' }}">
            Last 30 Days
        </a>
        <a href="{{ route('rider.earnings', ['period' => 'month']) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $period === 'month' ? 'bg-orange-500 text-white' : 'bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800' }}">
            This Month
        </a>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-800 p-5 transition-colors">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-full bg-orange-500 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Total Earnings</p>
            </div>
            <p class="text-2xl font-bold text-orange-500">₱{{ number_format($totalEarnings, 2) }}</p>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-800 p-5 transition-colors">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-full bg-zinc-800 dark:bg-zinc-200 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white dark:text-zinc-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                </div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Total Deliveries</p>
            </div>
            <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $totalDeliveries }}</p>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-800 p-5 transition-colors">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-full bg-orange-500 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Avg per Delivery</p>
            </div>
            <p class="text-2xl font-bold text-zinc-900 dark:text-white">₱{{ number_format($avgPerDelivery, 2) }}</p>
        </div>
    </div>

    {{-- BEST DAY HIGHLIGHT --}}
    @if ($bestDay)
        <div class="bg-orange-500 rounded-lg p-5 mb-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs opacity-90 uppercase tracking-wide mb-1">🏆 Best Day</p>
                    <p class="text-xl font-bold">{{ \Carbon\Carbon::parse($bestDay->date)->format('F d, Y') }}</p>
                    <p class="text-sm opacity-90 mt-1">{{ $bestDay->count }} deliveries</p>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-bold">₱{{ number_format($bestDay->total, 2) }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- CHART --}}
    <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-800 p-5 mb-6 transition-colors">
        <div class="flex justify-between items-center mb-4">
            <h2 class="font-semibold text-zinc-900 dark:text-white">Earnings Trend</h2>
            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $from->format('M d') }} → {{ $to->format('M d, Y') }}</p>
        </div>

        @if ($chartData->sum() > 0)
            {{-- FIXED HEIGHT WRAPPER --}}
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="earningsChart"></canvas>
            </div>
        @else
            <div class="py-12 text-center">
                <p class="text-4xl mb-2">📊</p>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">No earnings data yet</p>
                <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">Complete deliveries to see your earnings here</p>
            </div>
        @endif
    </div>

    {{-- DAILY BREAKDOWN --}}
    <div class="bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-800 overflow-hidden transition-colors">
        <div class="px-5 py-4 border-b border-zinc-200 dark:border-zinc-800">
            <h2 class="font-semibold text-zinc-900 dark:text-white">Daily Breakdown</h2>
        </div>

        @if ($allDays && count($allDays) > 0)
            <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @foreach (array_reverse($allDays) as $day)
                    @if ($day['count'] > 0)
                        <div class="px-5 py-3 flex justify-between items-center hover:bg-zinc-50 dark:hover:bg-zinc-800 transition">
                            <div>
                                <p class="text-sm font-medium text-zinc-900 dark:text-white">{{ $day['label'] }}</p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $day['count'] }} {{ Str::plural('delivery', $day['count']) }}</p>
                            </div>
                            <p class="text-sm font-semibold text-orange-500">₱{{ number_format($day['total'], 2) }}</p>
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- Total footer --}}
            <div class="px-5 py-4 bg-orange-500 border-t border-zinc-200 dark:border-zinc-800 flex justify-between items-center">
                <p class="text-sm font-medium text-white">Total</p>
                <p class="text-lg font-bold text-white">₱{{ number_format($totalEarnings, 2) }}</p>
            </div>
        @else
            <div class="p-8 text-center text-zinc-500 dark:text-zinc-400 text-sm">
                No deliveries yet in this period.
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
    function initEarningsChart() {
        const canvas = document.getElementById('earningsChart');
        if (!canvas) return;

        // Check kung may existing chart
        const existingChart = Chart.getChart(canvas);
        if (existingChart) {
            existingChart.destroy();
        }

        // Detect dark mode
        const isDark = document.documentElement.classList.contains('dark');
        const gridColor = isDark ? '#27272a' : '#f4f4f5';
        const tickColor = isDark ? '#a1a1aa' : '#71717a';
        const pointBorderColor = isDark ? '#18181b' : '#ffffff';

        new Chart(canvas, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Earnings (₱)',
                    data: {!! json_encode($chartData) !!},
                    borderColor: '#f97316',
                    backgroundColor: 'rgba(249, 115, 22, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#f97316',
                    pointBorderColor: pointBorderColor,
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 500,
                },
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        backgroundColor: '#18181b',
                        padding: 12,
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#f97316',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                return '₱' + parseFloat(context.parsed.y).toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value;
                            },
                            color: tickColor,
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            color: gridColor,
                        }
                    },
                    x: {
                        ticks: {
                            color: tickColor,
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            display: false,
                        }
                    }
                }
            }
        });
    }

    // Run kapag ready na ang DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initEarningsChart);
    } else {
        initEarningsChart();
    }
})();
</script>
@endpush