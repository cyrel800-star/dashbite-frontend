@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    {{-- ============================================ --}}
    {{-- PAGE HEADER --}}
    {{-- ============================================ --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">All Orders</h1>
            <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">
                {{ number_format($orders->total()) }} {{ Str::plural('order', $orders->total()) }} found
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.orders.export', request()->query()) }}"
               class="inline-flex items-center gap-2 bg-white dark:bg-[#141414] border border-neutral-300 dark:border-[#262626] text-neutral-700 dark:text-neutral-300 hover:bg-neutral-50 dark:hover:bg-[#0a0a0a] px-4 py-2 rounded-lg text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export CSV
            </a>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- FILTERS --}}
    {{-- ============================================ --}}
    <form method="GET" class="bg-white dark:bg-[#141414] rounded-xl border border-neutral-200 dark:border-[#262626] p-4 mb-4">

        <div class="flex items-center gap-2 mb-3">
            <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center shadow-sm">
                <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
            </div>
            <h2 class="text-sm font-bold text-neutral-900 dark:text-white">Filters</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 mb-3">

{{-- STATUS --}}
<div x-data="{
    open: false,
    value: '{{ request('status') }}',
    label: '{{ request('status') ? ucfirst(str_replace('_', ' ', request('status'))) : 'All Status' }}',
    select(val, text) { this.value = val; this.label = text; this.open = false; }
}" class="relative">
    <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">Status</label>
    <input type="hidden" name="status" :value="value">

    {{-- Dropdown Trigger Button --}}
    <button type="button" @click="open = !open" @click.outside="open = false"
            class="w-full flex items-center justify-between border border-neutral-300 dark:border-[#262626] rounded-lg px-3 py-2 text-sm bg-white dark:bg-[#0a0a0a] text-neutral-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
        <span x-text="label" class="truncate"></span>
        <svg class="w-4 h-4 text-neutral-400 transition-transform flex-shrink-0" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    {{-- Orange Dropdown Menu --}}
    <div x-show="open" x-cloak
         class="absolute left-0 right-0 mt-1 max-h-60 overflow-y-auto bg-white dark:bg-[#141414] border border-neutral-200 dark:border-[#262626] rounded-lg shadow-xl z-50 py-1">
        <div @click="select('', 'All Status')"
             class="px-3 py-2 text-sm cursor-pointer transition hover:bg-orange-500 hover:text-white"
             :class="value === '' ? 'bg-orange-50 dark:bg-orange-950/30 text-orange-600 font-semibold' : 'text-neutral-800 dark:text-neutral-200'">
            All Status
        </div>
        @foreach(['received','confirmed','preparing','finding_rider','rider_assigned','picked_up','out_for_delivery','delivered','rejected','cancelled','no_rider'] as $s)
            <div @click="select('{{ $s }}', '{{ ucfirst(str_replace('_', ' ', $s)) }}')"
                 class="px-3 py-2 text-sm cursor-pointer transition hover:bg-orange-500 hover:text-white"
                 :class="value === '{{ $s }}' ? 'bg-orange-50 dark:bg-orange-950/30 text-orange-600 font-semibold' : 'text-neutral-800 dark:text-neutral-200'">
                {{ ucfirst(str_replace('_', ' ', $s)) }}
            </div>
        @endforeach
    </div>
</div>

{{-- RESTAURANT --}}
@php
    $selectedRestaurantName = 'All Restaurants';
    if (request('restaurant_id')) {
        $found = $restaurants->firstWhere('id', request('restaurant_id'));
        if ($found) { $selectedRestaurantName = $found->name; }
    }
@endphp
<div x-data="{
    open: false,
    value: '{{ request('restaurant_id') }}',
    label: '{{ addslashes($selectedRestaurantName) }}',
    select(val, text) { this.value = val; this.label = text; this.open = false; }
}" class="relative">
    <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">Restaurant</label>
    <input type="hidden" name="restaurant_id" :value="value">

    {{-- Dropdown Trigger Button --}}
    <button type="button" @click="open = !open" @click.outside="open = false"
            class="w-full flex items-center justify-between border border-neutral-300 dark:border-[#262626] rounded-lg px-3 py-2 text-sm bg-white dark:bg-[#0a0a0a] text-neutral-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
        <span x-text="label" class="truncate"></span>
        <svg class="w-4 h-4 text-neutral-400 transition-transform flex-shrink-0" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    {{-- Orange Dropdown Menu --}}
    <div x-show="open" x-cloak
         class="absolute left-0 right-0 mt-1 max-h-60 overflow-y-auto bg-white dark:bg-[#141414] border border-neutral-200 dark:border-[#262626] rounded-lg shadow-xl z-50 py-1">
        <div @click="select('', 'All Restaurants')"
             class="px-3 py-2 text-sm cursor-pointer transition hover:bg-orange-500 hover:text-white"
             :class="value === '' ? 'bg-orange-50 dark:bg-orange-950/30 text-orange-600 font-semibold' : 'text-neutral-800 dark:text-neutral-200'">
            All Restaurants
        </div>
        @foreach ($restaurants as $r)
            <div @click="select('{{ $r->id }}', '{{ addslashes($r->name) }}')"
                 class="px-3 py-2 text-sm cursor-pointer transition hover:bg-orange-500 hover:text-white"
                 :class="value == '{{ $r->id }}' ? 'bg-orange-50 dark:bg-orange-950/30 text-orange-600 font-semibold' : 'text-neutral-800 dark:text-neutral-200'">
                {{ $r->name }}
            </div>
        @endforeach
    </div>
</div>

{{-- RIDER --}}
@php
    $selectedRiderName = 'All Riders';
    if (request('rider_id')) {
        $found = $riders->firstWhere('id', request('rider_id'));
        if ($found && $found->user) { $selectedRiderName = $found->user->name; }
    }
@endphp
<div x-data="{
    open: false,
    value: '{{ request('rider_id') }}',
    label: '{{ addslashes($selectedRiderName) }}',
    select(val, text) { this.value = val; this.label = text; this.open = false; }
}" class="relative">
    <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">Rider</label>
    <input type="hidden" name="rider_id" :value="value">

    {{-- Dropdown Trigger Button --}}
    <button type="button" @click="open = !open" @click.outside="open = false"
            class="w-full flex items-center justify-between border border-neutral-300 dark:border-[#262626] rounded-lg px-3 py-2 text-sm bg-white dark:bg-[#0a0a0a] text-neutral-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
        <span x-text="label" class="truncate"></span>
        <svg class="w-4 h-4 text-neutral-400 transition-transform flex-shrink-0" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    {{-- Orange Dropdown Menu --}}
    <div x-show="open" x-cloak
         class="absolute left-0 right-0 mt-1 max-h-60 overflow-y-auto bg-white dark:bg-[#141414] border border-neutral-200 dark:border-[#262626] rounded-lg shadow-xl z-50 py-1">
        <div @click="select('', 'All Riders')"
             class="px-3 py-2 text-sm cursor-pointer transition hover:bg-orange-500 hover:text-white"
             :class="value === '' ? 'bg-orange-50 dark:bg-orange-950/30 text-orange-600 font-semibold' : 'text-neutral-800 dark:text-neutral-200'">
            All Riders
        </div>
        @foreach ($riders as $r)
            @if ($r->user)
                <div @click="select('{{ $r->id }}', '{{ addslashes($r->user->name) }}')"
                     class="px-3 py-2 text-sm cursor-pointer transition hover:bg-orange-500 hover:text-white"
                     :class="value == '{{ $r->id }}' ? 'bg-orange-50 dark:bg-orange-950/30 text-orange-600 font-semibold' : 'text-neutral-800 dark:text-neutral-200'">
                    {{ $r->user->name }}
                </div>
            @endif
        @endforeach
    </div>
</div>

{{-- FROM --}}
<div class="relative">
    <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">From</label>
    <input type="text" name="from" value="{{ request('from') }}" placeholder="YYYY-MM-DD" autocomplete="off"
           class="datepicker w-full border border-neutral-300 dark:border-[#262626] rounded-lg px-3 py-2 text-sm bg-white dark:bg-[#0a0a0a] text-neutral-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
</div>

{{-- TO --}}
<div class="relative">
    <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">To</label>
    <input type="text" name="to" value="{{ request('to') }}" placeholder="YYYY-MM-DD" autocomplete="off"
           class="datepicker w-full border border-neutral-300 dark:border-[#262626] rounded-lg px-3 py-2 text-sm bg-white dark:bg-[#0a0a0a] text-neutral-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
</div>
    </div>

        {{-- ACTIONS --}}
        <div class="flex flex-wrap gap-2 pt-3 border-t border-neutral-100 dark:border-[#262626]">
            <button type="submit"
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white hover:from-orange-600 hover:to-orange-700 px-4 py-2 rounded-lg text-sm font-semibold shadow-md hover:shadow-lg active:scale-98 transition transform">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Apply Filters
            </button>

            @if (request()->hasAny(['status', 'restaurant_id', 'rider_id', 'from', 'to']))
                <a href="{{ route('admin.orders') }}"
                   class="inline-flex items-center gap-2 border border-neutral-300 dark:border-[#262626] text-neutral-700 dark:text-neutral-300 hover:bg-neutral-50 dark:hover:bg-[#0a0a0a] px-4 py-2 rounded-lg text-sm font-semibold transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Clear Filters
                </a>
            @endif
        </div>
    </form>

    {{-- ============================================ --}}
    {{-- ORDERS TABLE --}}
    {{-- ============================================ --}}
    <div class="bg-white dark:bg-[#141414] rounded-xl border border-neutral-200 dark:border-[#262626] overflow-hidden">

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-neutral-50 dark:bg-[#0a0a0a] border-b border-neutral-100 dark:border-[#262626]">
                    <tr>
                        <th class="text-left px-5 py-3 font-semibold text-neutral-600 dark:text-neutral-400 text-xs uppercase tracking-wide">Order</th>
                        <th class="text-left px-5 py-3 font-semibold text-neutral-600 dark:text-neutral-400 text-xs uppercase tracking-wide">Date</th>
                        <th class="text-left px-5 py-3 font-semibold text-neutral-600 dark:text-neutral-400 text-xs uppercase tracking-wide">Customer</th>
                        <th class="text-left px-5 py-3 font-semibold text-neutral-600 dark:text-neutral-400 text-xs uppercase tracking-wide">Restaurant</th>
                        <th class="text-left px-5 py-3 font-semibold text-neutral-600 dark:text-neutral-400 text-xs uppercase tracking-wide">Rider</th>
                        <th class="text-right px-5 py-3 font-semibold text-neutral-600 dark:text-neutral-400 text-xs uppercase tracking-wide">Total</th>
                        <th class="text-left px-5 py-3 font-semibold text-neutral-600 dark:text-neutral-400 text-xs uppercase tracking-wide">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-[#262626]">
                    @forelse ($orders as $o)
                        @php
                            $statusStyles = [
                                'delivered' => 'bg-orange-50 dark:bg-orange-950/30 text-orange-700 dark:text-orange-400 border-orange-200 dark:border-orange-800',
                                'cancelled' => 'bg-neutral-100 dark:bg-[#262626] text-neutral-700 dark:text-neutral-400 border-neutral-200 dark:border-[#262626]',
                                'rejected' => 'bg-red-50 dark:bg-red-950/30 text-red-700 dark:text-red-400 border-red-200 dark:border-red-800',
                                'no_rider' => 'bg-red-50 dark:bg-red-950/30 text-red-700 dark:text-red-400 border-red-200 dark:border-red-800',
                                'received' => 'bg-orange-50 dark:bg-orange-950/30 text-orange-700 dark:text-orange-400 border-orange-200 dark:border-orange-800',
                                'confirmed' => 'bg-orange-50 dark:bg-orange-950/30 text-orange-700 dark:text-orange-400 border-orange-200 dark:border-orange-800',
                            ];
                            $statusClass = $statusStyles[$o->status] ?? 'bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800';
                        @endphp
                        <tr onclick="window.location='{{ route('admin.orders.show', $o) }}'"
                            class="hover:bg-orange-50/50 dark:hover:bg-orange-950/10 transition cursor-pointer">

                            {{-- ORDER ID --}}
                            <td class="px-5 py-3">
                                <span class="font-semibold text-neutral-900 dark:text-white">#{{ $o->id }}</span>
                                @if ($o->is_external_order)
                                    <span class="ml-1.5 inline-block text-[10px] bg-orange-100 dark:bg-orange-950/40 text-orange-700 dark:text-orange-400 border border-orange-200 dark:border-orange-800 px-1.5 py-0.5 rounded-full font-bold uppercase tracking-wide">
                                        External
                                    </span>
                                @endif
                            </td>

                            {{-- DATE --}}
                            <td class="px-5 py-3 text-xs text-neutral-500 dark:text-neutral-400 whitespace-nowrap">
                                <div>{{ $o->created_at->format('M d, Y') }}</div>
                                <div class="text-neutral-400 dark:text-neutral-500">{{ $o->created_at->format('H:i') }}</div>
                            </td>
                            {{-- CUSTOMER --}}
                            <td class="px-5 py-3 text-neutral-700 dark:text-neutral-300">
                                {{ $o->customer->name ?? '—' }}
                            </td>

                            {{-- RESTAURANT --}}
                            <td class="px-5 py-3 text-neutral-700 dark:text-neutral-300">
                                {{ $o->restaurant->name ?? '—' }}
                            </td>

                            {{-- RIDER --}}
                            <td class="px-5 py-3">
                                @if ($o->rider && $o->rider->user)
                                    <span class="text-neutral-700 dark:text-neutral-300">{{ $o->rider->user->name }}</span>
                                @else
                                    <span class="text-xs text-neutral-400 dark:text-neutral-500 italic">Unassigned</span>
                                @endif
                            </td>

                            {{-- TOTAL --}}
                            <td class="px-5 py-3 text-right font-bold text-neutral-900 dark:text-white whitespace-nowrap">
                                ₱{{ number_format($o->total_amount, 2) }}
                            </td>

                            {{-- STATUS --}}
                            <td class="px-5 py-3">
                                <span class="inline-block text-xs px-2 py-0.5 rounded-full border font-bold uppercase tracking-wide {{ $statusClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $o->status)) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-neutral-100 dark:bg-[#262626] mb-3">
                                    <svg class="w-8 h-8 text-neutral-400 dark:text-neutral-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <p class="text-sm text-neutral-500 dark:text-neutral-400 font-medium">No orders found</p>
                                @if (request()->hasAny(['status', 'restaurant_id', 'rider_id', 'from', 'to']))
                                    <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-1">Try adjusting your filters</p>
                                    <a href="{{ route('admin.orders') }}"
                                       class="inline-block mt-3 text-xs font-semibold text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 underline">
                                        Clear all filters
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- PAGINATION --}}
    {{-- ============================================ --}}
    @if ($orders->hasPages())
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    @endif

</div>
@endsection

@push('scripts')
{{-- Flatpickr CSS & JS --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        flatpickr('.datepicker', {
            dateFormat: 'Y-m-d',
            allowInput: true,
            static: true
        });
    });
</script>
@endpush
