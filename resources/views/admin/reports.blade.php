@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    {{-- ============================================ --}}
    {{-- DATE RANGE FILTER --}}
    {{-- ============================================ --}}
    <form method="GET" class="bg-white dark:bg-[#141414] rounded-xl border border-neutral-200 dark:border-[#262626] p-4 mb-4">

        <div class="flex items-center gap-2 mb-3">
            <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center shadow-sm">
                <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <h2 class="text-sm font-bold text-neutral-900 dark:text-white">Date Range</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-3">
            <div>
                <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">From</label>
                <input type="date" name="from" value="{{ $from }}"
                       class="w-full border border-neutral-300 dark:border-[#262626] rounded-lg px-3 py-2 text-sm bg-white dark:bg-[#0a0a0a] text-neutral-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
            </div>
            <div>
                <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">To</label>
                <input type="date" name="to" value="{{ $to }}"
                       class="w-full border border-neutral-300 dark:border-[#262626] rounded-lg px-3 py-2 text-sm bg-white dark:bg-[#0a0a0a] text-neutral-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
            </div>
        </div>

        {{-- QUICK RANGES --}}
        <div class="flex flex-wrap gap-2 mb-3">
            @php
                $today = now();
                $quickRanges = [
                    ['label' => 'Today', 'from' => $today->copy()->format('Y-m-d'), 'to' => $today->copy()->format('Y-m-d')],
                    ['label' => 'Last 7 days', 'from' => $today->copy()->subDays(7)->format('Y-m-d'), 'to' => $today->copy()->format('Y-m-d')],
                    ['label' => 'Last 30 days', 'from' => $today->copy()->subDays(30)->format('Y-m-d'), 'to' => $today->copy()->format('Y-m-d')],
                    ['label' => 'This month', 'from' => $today->copy()->startOfMonth()->format('Y-m-d'), 'to' => $today->copy()->format('Y-m-d')],
                ];
            @endphp

            @foreach ($quickRanges as $range)
                <a href="{{ route('admin.reports', ['from' => $range['from'], 'to' => $range['to']]) }}"
                   class="text-xs font-semibold px-3 py-1.5 rounded-lg border transition
                          {{ $from === $range['from'] && $to === $range['to']
                                ? 'bg-gradient-to-r from-orange-500 to-orange-600 text-white border-transparent shadow-md'
                                : 'bg-white dark:bg-[#141414] text-neutral-600 dark:text-neutral-400 border-neutral-300 dark:border-[#262626] hover:bg-neutral-50 dark:hover:bg-[#0a0a0a] hover:text-orange-600 dark:hover:text-orange-400' }}">
                    {{ $range['label'] }}
                </a>
            @endforeach
        </div>

        {{-- ACTIONS --}}
        <div class="flex flex-wrap gap-2 pt-3 border-t border-neutral-100 dark:border-[#262626]">
            <button type="submit"
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white hover:from-orange-600 hover:to-orange-700 px-4 py-2 rounded-lg text-sm font-semibold shadow-md hover:shadow-lg active:scale-98 transition transform">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                Apply
            </button>

            <a href="{{ route('admin.reports.export', ['from' => $from, 'to' => $to]) }}"
               class="inline-flex items-center gap-2 bg-white dark:bg-[#141414] border border-neutral-300 dark:border-[#262626] text-neutral-700 dark:text-neutral-300 hover:bg-neutral-50 dark:hover:bg-[#0a0a0a] px-4 py-2 rounded-lg text-sm font-semibold transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export CSV
            </a>
        </div>
    </form>

    {{-- ============================================ --}}
    {{-- SUMMARY STATS --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-4">

        {{-- TOTAL ORDERS --}}
        <div class="bg-white dark:bg-[#141414] rounded-xl border border-neutral-200 dark:border-[#262626] p-4 transition-colors">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wide">Total Orders</p>
                <div class="w-7 h-7 rounded-lg bg-orange-100 dark:bg-orange-950/40 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-neutral-900 dark:text-white">{{ number_format($salesStats['total_orders']) }}</p>
        </div>

        {{-- FOOD COST --}}
        <div class="bg-white dark:bg-[#141414] rounded-xl border border-neutral-200 dark:border-[#262626] p-4 transition-colors">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wide">Food Cost</p>
                <div class="w-7 h-7 rounded-lg bg-orange-100 dark:bg-orange-950/40 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-neutral-900 dark:text-white">₱{{ number_format($salesStats['total_food_cost'], 0) }}</p>
        </div>

        {{-- DELIVERY FEES --}}
        <div class="bg-white dark:bg-[#141414] rounded-xl border border-neutral-200 dark:border-[#262626] p-4 transition-colors">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wide">Delivery Fees</p>
                <div class="w-7 h-7 rounded-lg bg-orange-100 dark:bg-orange-950/40 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-neutral-900 dark:text-white">₱{{ number_format($salesStats['total_delivery_fees'], 0) }}</p>
        </div>

        {{-- ⭐ PLATFORM COMMISSION --}}
        <div class="bg-white dark:bg-[#141414] rounded-xl border-2 border-orange-300 dark:border-orange-800 p-4 transition-colors">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-bold text-orange-600 dark:text-orange-400 uppercase tracking-wide">Commission</p>
                <div class="w-7 h-7 rounded-lg bg-orange-100 dark:bg-orange-950/40 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">₱{{ number_format($salesStats['total_commission'] ?? 0, 0) }}</p>
        </div>

        {{-- TOTAL REVENUE --}}
        <div class="bg-white dark:bg-[#141414] rounded-xl border border-neutral-200 dark:border-[#262626] p-4 transition-colors">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wide">Total Revenue</p>
                <div class="w-7 h-7 rounded-lg bg-orange-100 dark:bg-orange-950/40 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">₱{{ number_format($salesStats['total_revenue'], 0) }}</p>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- TOP PERFORMERS --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">

        {{-- TOP RESTAURANTS --}}
        <div class="bg-white dark:bg-[#141414] rounded-xl border border-neutral-200 dark:border-[#262626] overflow-hidden">
            <div class="px-5 py-3 border-b border-neutral-100 dark:border-[#262626] flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-orange-100 dark:bg-orange-950/40 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h2 class="text-sm font-bold text-neutral-900 dark:text-white">Top Restaurants</h2>
                @if ($topRestaurants->count() > 0)
                    <span class="ml-auto text-xs text-neutral-400 dark:text-neutral-500">{{ $topRestaurants->count() }} total</span>
                @endif
            </div>

            @if ($topRestaurants->isEmpty())
                <div class="px-5 py-10 text-center">
                    <svg class="w-8 h-8 text-neutral-300 dark:text-neutral-600 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <p class="text-sm text-neutral-400 dark:text-neutral-500">No data available</p>
                </div>
            @else
                <div class="divide-y divide-neutral-100 dark:divide-[#262626]">
                    @foreach ($topRestaurants as $index => $r)
                        <div class="px-5 py-3 flex items-center gap-3 hover:bg-orange-50/50 dark:hover:bg-orange-950/10 transition">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 text-xs font-bold
                                        {{ $index === 0 ? 'bg-gradient-to-br from-orange-500 to-orange-600 text-white shadow-md' :
                                           ($index === 1 ? 'bg-gradient-to-br from-neutral-700 to-neutral-800 text-white' :
                                           ($index === 2 ? 'bg-gradient-to-br from-amber-600 to-amber-700 text-white' : 'bg-neutral-100 dark:bg-[#262626] text-neutral-500 dark:text-neutral-400')) }}">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-neutral-900 dark:text-white truncate">{{ $r->name }}</p>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ $r->total_orders }} {{ Str::plural('order', $r->total_orders) }}</p>
                            </div>
                            <p class="text-sm font-bold text-orange-600 dark:text-orange-400">₱{{ number_format($r->total_sales, 0) }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- TOP RIDERS --}}
        <div class="bg-white dark:bg-[#141414] rounded-xl border border-neutral-200 dark:border-[#262626] overflow-hidden">
            <div class="px-5 py-3 border-b border-neutral-100 dark:border-[#262626] flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-orange-100 dark:bg-orange-950/40 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1" />
                    </svg>
                </div>
                <h2 class="text-sm font-bold text-neutral-900 dark:text-white">Top Riders</h2>
                @if ($topRiders->count() > 0)
                    <span class="ml-auto text-xs text-neutral-400 dark:text-neutral-500">{{ $topRiders->count() }} total</span>
                @endif
            </div>

            @if ($topRiders->isEmpty())
                <div class="px-5 py-10 text-center">
                    <svg class="w-8 h-8 text-neutral-300 dark:text-neutral-600 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <p class="text-sm text-neutral-400 dark:text-neutral-500">No data available</p>
                </div>
            @else
                <div class="divide-y divide-neutral-100 dark:divide-[#262626]">
                    @foreach ($topRiders as $index => $r)
                        <div class="px-5 py-3 flex items-center gap-3 hover:bg-orange-50/50 dark:hover:bg-orange-950/10 transition">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 text-xs font-bold
                                        {{ $index === 0 ? 'bg-gradient-to-br from-orange-500 to-orange-600 text-white shadow-md' :
                                           ($index === 1 ? 'bg-gradient-to-br from-neutral-700 to-neutral-800 text-white' :
                                           ($index === 2 ? 'bg-gradient-to-br from-amber-600 to-amber-700 text-white' : 'bg-neutral-100 dark:bg-[#262626] text-neutral-500 dark:text-neutral-400')) }}">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-neutral-900 dark:text-white truncate">{{ $r->name }}</p>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ $r->total_deliveries }} {{ Str::plural('delivery', $r->total_deliveries) }}</p>
                            </div>
                            <p class="text-sm font-bold text-orange-600 dark:text-orange-400">₱{{ number_format($r->total_earnings, 0) }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- DAILY SALES --}}
    {{-- ============================================ --}}
    <div class="bg-white dark:bg-[#141414] rounded-xl border border-neutral-200 dark:border-[#262626] overflow-hidden">

        <div class="px-5 py-3 border-b border-neutral-100 dark:border-[#262626] flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-orange-100 dark:bg-orange-950/40 flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <h2 class="text-sm font-bold text-neutral-900 dark:text-white">Daily Sales</h2>
            @if ($dailySales->count() > 0)
                <span class="ml-auto text-xs text-neutral-400 dark:text-neutral-500">{{ $dailySales->count() }} {{ Str::plural('day', $dailySales->count()) }}</span>
            @endif
        </div>

        @if ($dailySales->isEmpty())
            <div class="px-5 py-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-neutral-100 dark:bg-[#262626] mb-3">
                    <svg class="w-8 h-8 text-neutral-400 dark:text-neutral-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <p class="text-sm text-neutral-500 dark:text-neutral-400 font-medium">No sales data for this period</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-neutral-50 dark:bg-[#0a0a0a] border-b border-neutral-100 dark:border-[#262626]">
                        <tr>
                            <th class="text-left px-5 py-3 font-semibold text-neutral-600 dark:text-neutral-400 text-xs uppercase tracking-wide">Date</th>
                            <th class="text-left px-5 py-3 font-semibold text-neutral-600 dark:text-neutral-400 text-xs uppercase tracking-wide">Day</th>
                            <th class="text-right px-5 py-3 font-semibold text-neutral-600 dark:text-neutral-400 text-xs uppercase tracking-wide">Orders</th>
                            <th class="text-right px-5 py-3 font-semibold text-neutral-600 dark:text-neutral-400 text-xs uppercase tracking-wide">Sales</th>
                            <th class="text-right px-5 py-3 font-semibold text-neutral-600 dark:text-neutral-400 text-xs uppercase tracking-wide">Avg Order</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 dark:divide-[#262626]">
                        @foreach ($dailySales as $d)
                            @php
                                $date = \Carbon\Carbon::parse($d->date);
                                $avg = $d->count > 0 ? $d->total / $d->count : 0;
                            @endphp
                            <tr class="hover:bg-orange-50/50 dark:hover:bg-orange-950/10 transition">
                                <td class="px-5 py-3">
                                    <span class="font-semibold text-neutral-900 dark:text-white">{{ $date->format('M d, Y') }}</span>
                                </td>
                                <td class="px-5 py-3 text-neutral-500 dark:text-neutral-400">{{ $date->format('l') }}</td>
                                <td class="px-5 py-3 text-right text-neutral-700 dark:text-neutral-300">{{ number_format($d->count) }}</td>
                                <td class="px-5 py-3 text-right font-bold text-orange-600 dark:text-orange-400">
                                    ₱{{ number_format($d->total, 2) }}
                                </td>
                                <td class="px-5 py-3 text-right text-neutral-500 dark:text-neutral-400">
                                    ₱{{ number_format($avg, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-neutral-50 dark:bg-[#0a0a0a] border-t border-neutral-200 dark:border-[#262626]">
                        <tr>
                            <td colspan="2" class="px-5 py-3 text-xs font-bold text-neutral-600 dark:text-neutral-400 uppercase tracking-wide">
                                Total
                            </td>
                            <td class="px-5 py-3 text-right font-bold text-neutral-900 dark:text-white">
                                {{ number_format($dailySales->sum('count')) }}
                            </td>
                            <td class="px-5 py-3 text-right font-bold text-orange-600 dark:text-orange-400">
                                ₱{{ number_format($dailySales->sum('total'), 2) }}
                            </td>
                            <td class="px-5 py-3 text-right text-neutral-500 dark:text-neutral-400">
                                ₱{{ number_format($dailySales->sum('count') > 0 ? $dailySales->sum('total') / $dailySales->sum('count') : 0, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<style>
    .active\:scale-98:active { transform: scale(0.98); }
</style>
@endpush