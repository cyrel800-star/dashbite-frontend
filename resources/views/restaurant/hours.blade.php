@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-5" x-data="hoursForm()">

    {{-- ============================================ --}}
    {{-- HEADER --}}
    {{-- ============================================ --}}
    <div class="flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">Operating Hours</h1>
                <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-0.5">I-set ang schedule ng iyong restaurant</p>
            </div>
        </div>

        <a href="{{ route('restaurant.dashboard') }}"
           class="inline-flex items-center gap-2 text-sm font-semibold text-neutral-700 dark:text-neutral-300 hover:text-orange-600 dark:hover:text-orange-400 transition group">
            <svg class="w-5 h-5 group-hover:-translate-x-0.5 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Back
        </a>
    </div>

    {{-- ============================================ --}}
    {{-- ALERTS --}}
    {{-- ============================================ --}}
    @if (session('success'))
        <div class="p-4 bg-gradient-to-r from-orange-50 to-orange-100/50 dark:from-orange-950/30 dark:to-orange-900/10 border-l-4 border-orange-500 rounded-xl text-sm flex items-center gap-3 shadow-sm">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center flex-shrink-0 shadow-md">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <span class="text-neutral-800 dark:text-neutral-200 font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="p-4 bg-black dark:bg-neutral-900 border-l-4 border-orange-500 rounded-xl text-sm flex items-center gap-3 shadow-sm">
            <div class="w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <span class="text-white font-medium">{{ $errors->first() }}</span>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- INFO BOX --}}
    {{-- ============================================ --}}
    <div class="bg-gradient-to-r from-orange-50 to-orange-100/50 dark:from-orange-950/30 dark:to-orange-900/10 border border-orange-200 dark:border-orange-800/50 rounded-2xl p-4">
        <div class="flex gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center flex-shrink-0 shadow-md">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="text-sm">
                <p class="font-bold text-orange-900 dark:text-orange-300 mb-1">Auto Open/Close</p>
                <p class="text-xs text-orange-800 dark:text-orange-300/80 leading-relaxed">
                    Ang system ay mag-a-auto open/close ng iyong restaurant base sa schedule.
                    Ang <strong>manual toggle</strong> (Open/Close button sa dashboard) ay mag-o-override sa auto-toggle.
                </p>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- FORM --}}
    {{-- ============================================ --}}
    <form method="POST" action="{{ route('restaurant.hours.update') }}" class="space-y-3">
        @csrf
        @method('PATCH')

        @foreach ($days as $day)
            <div class="bg-white dark:bg-[#141414] rounded-2xl border border-neutral-200 dark:border-[#262626] p-4 transition-all hover:shadow-lg hover:border-orange-300 dark:hover:border-orange-800/50"
                 x-data="{ isOpen: {{ $day['is_open'] ? 'true' : 'false' }} }">

                <div class="flex flex-col sm:flex-row sm:items-center gap-4">

                    {{-- DAY NAME + TOGGLE --}}
                    <div class="flex items-center gap-3 sm:w-48 flex-shrink-0">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" :name="'days[{{ $day['day_of_week'] }}][is_open]'" :value="isOpen ? 1 : 0">
                            <input type="checkbox"
                                   x-model="isOpen"
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-neutral-200 dark:bg-[#262626] peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-500/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-neutral-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-orange-500 peer-checked:to-orange-600 shadow-sm"></div>
                        </label>

                        <div>
                            <p class="font-bold text-neutral-900 dark:text-white">{{ $day['day_name'] }}</p>
                            <p class="text-xs font-semibold uppercase tracking-wide"
                               :class="isOpen ? 'text-orange-600 dark:text-orange-400' : 'text-neutral-400 dark:text-neutral-500'"
                               x-text="isOpen ? 'Open' : 'Closed'"></p>
                        </div>
                    </div>

                    {{-- HOURS INPUTS --}}
                    <div class="flex-1 flex items-center gap-3 transition-opacity duration-200"
                         :class="!isOpen && 'opacity-40 pointer-events-none'">

                        <input type="hidden" :name="'days[{{ $day['day_of_week'] }}][day_of_week]'" value="{{ $day['day_of_week'] }}">

                        <div class="flex-1">
                            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wide mb-1.5">Open</label>
                            <input type="time"
                                   name="days[{{ $day['day_of_week'] }}][open_time]"
                                   value="{{ $day['open_time'] }}"
                                   class="w-full border border-neutral-300 dark:border-[#262626] dark:bg-[#0a0a0a] dark:text-white rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
                        </div>

                        <div class="text-neutral-400 dark:text-neutral-500 pt-5 flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </div>

                        <div class="flex-1">
                            <label class="block text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wide mb-1.5">Close</label>
                            <input type="time"
                                   name="days[{{ $day['day_of_week'] }}][close_time]"
                                   value="{{ $day['close_time'] }}"
                                   class="w-full border border-neutral-300 dark:border-[#262626] dark:bg-[#0a0a0a] dark:text-white rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- ============================================ --}}
        {{-- QUICK ACTIONS --}}
        {{-- ============================================ --}}
        <div class="bg-white dark:bg-[#141414] rounded-2xl border border-neutral-200 dark:border-[#262626] p-4">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center shadow-sm">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <p class="text-sm font-bold text-neutral-900 dark:text-white">Quick Actions</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button"
                        @click="setAllDays(true, '09:00', '21:00')"
                        class="text-xs bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 hover:bg-orange-500 dark:hover:bg-orange-500 hover:text-white px-4 py-2 rounded-lg font-semibold transition shadow-sm">
                    Open All (9AM–9PM)
                </button>
                <button type="button"
                        @click="setAllDays(true, '10:00', '22:00')"
                        class="text-xs bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 hover:bg-orange-500 dark:hover:bg-orange-500 hover:text-white px-4 py-2 rounded-lg font-semibold transition shadow-sm">
                    Open All (10AM–10PM)
                </button>
                <button type="button"
                        @click="setAllDays(false)"
                        class="text-xs bg-neutral-100 dark:bg-[#262626] text-neutral-700 dark:text-neutral-300 hover:bg-orange-100 dark:hover:bg-orange-950/40 hover:text-orange-700 dark:hover:text-orange-400 px-4 py-2 rounded-lg font-semibold transition">
                    Close All
                </button>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- SUBMIT --}}
        {{-- ============================================ --}}
        <div class="pt-2 flex gap-2">
            <button type="submit"
                    class="flex-1 sm:flex-none bg-gradient-to-r from-orange-500 to-orange-600 text-white px-8 py-3 rounded-xl font-semibold text-sm shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg active:scale-98 transition transform flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                Save Schedule
            </button>
            <a href="{{ route('restaurant.dashboard') }}"
               class="flex-1 sm:flex-none border border-neutral-300 dark:border-[#262626] text-neutral-700 dark:text-neutral-300 px-8 py-3 rounded-xl font-semibold text-sm hover:bg-neutral-50 dark:hover:bg-[#0a0a0a] text-center transition">
                Cancel
            </a>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<style>
    .active\:scale-98:active { transform: scale(0.98); }
</style>
<script>
function hoursForm() {
    return {
        setAllDays(isOpen, openTime = '09:00', closeTime = '21:00') {
            // Set all time inputs
            document.querySelectorAll('input[type="time"]').forEach((el, idx) => {
                el.value = idx % 2 === 0 ? openTime : closeTime;
            });

            // Trigger Alpine models
            document.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                cb.checked = isOpen;
                cb.dispatchEvent(new Event('change'));
            });
        }
    }
}
</script>
@endpush