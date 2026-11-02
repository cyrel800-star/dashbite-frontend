@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    {{-- ============================================ --}}
    {{-- HERO HEADER --}}
    {{-- ============================================ --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 rounded-2xl shadow-xl">

        {{-- Decorative gradient --}}
        <div class="absolute top-0 right-0 w-64 h-64 bg-orange-500 rounded-full blur-3xl opacity-20 -mr-20 -mt-20"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-blue-500 rounded-full blur-3xl opacity-10 -ml-16 -mb-16"></div>

        <div class="relative p-6">
            {{-- TOP ROW --}}
            <div class="flex justify-between items-start mb-6">
                <div class="flex items-center gap-3">
                    <a href="{{ route('rider.dashboard') }}"
                       class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 backdrop-blur flex items-center justify-center transition">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <p class="text-xs text-white/60 uppercase tracking-wider font-medium">Rider</p>
                        <h1 class="text-xl font-bold text-white">Vehicle & Details</h1>
                    </div>
                </div>

                {{-- STATUS PILL --}}
                <div class="flex items-center gap-2 bg-white/10 backdrop-blur rounded-full px-3 py-1.5">
                    <span class="w-2 h-2 rounded-full {{ $rider->is_online ? 'bg-green-400 animate-pulse' : 'bg-gray-400' }}"></span>
                    <span class="text-xs font-semibold text-white">
                        {{ $rider->is_online ? 'Online' : 'Offline' }}
                    </span>
                </div>
            </div>

            {{-- PROFILE INFO --}}
            <div class="flex items-center gap-4 mb-6">
                <div class="relative">
                    @if (auth()->user()->avatar_url)
                        <img src="{{ auth()->user()->avatar_url }}"
                             class="w-16 h-16 rounded-2xl object-cover border-2 border-white/20">
                    @else
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white text-2xl font-bold border-2 border-white/20">
                            {{ auth()->user()->initials }}
                        </div>
                    @endif

                    @if ($rider->is_online)
                        <span class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-green-400 border-4 border-gray-900"></span>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <p class="text-white font-bold text-lg leading-tight">{{ auth()->user()->name }}</p>
                    <p class="text-white/60 text-sm truncate">{{ auth()->user()->email }}</p>
                    @if ($rider->vehicle_type)
                        <p class="text-white/80 text-xs mt-1 flex items-center gap-1.5">
                            <span>{{ $rider->vehicle_type }}</span>
                            @if ($rider->vehicle_plate)
                                <span class="text-white/40">·</span>
                                <span class="font-mono">{{ $rider->vehicle_plate }}</span>
                            @endif
                        </p>
                    @endif
                </div>
            </div>

            {{-- MINI STATS --}}
            <div class="grid grid-cols-3 gap-3 pt-5 border-t border-white/10">
                <div>
                    <p class="text-[10px] text-white/50 uppercase tracking-wider font-medium">Vehicle</p>
                    <p class="text-white font-bold text-sm mt-0.5">{{ $rider->vehicle_type ?? 'Not set' }}</p>
                </div>
                <div class="border-l border-white/10 pl-3">
                    <p class="text-[10px] text-white/50 uppercase tracking-wider font-medium">Plate</p>
                    <p class="text-white font-bold text-sm mt-0.5 font-mono">{{ $rider->vehicle_plate ?? '—' }}</p>
                </div>
                <div class="border-l border-white/10 pl-3">
                    <p class="text-[10px] text-white/50 uppercase tracking-wider font-medium">Since</p>
                    <p class="text-white font-bold text-sm mt-0.5">{{ auth()->user()->created_at->format('M Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- ALERTS --}}
    {{-- ============================================ --}}
    @if (session('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm flex items-center gap-3 animate-fade-in">
            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm flex items-center gap-3 animate-fade-in">
            <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            {{ $errors->first() }}
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- VEHICLE INFO FORM --}}
    {{-- ============================================ --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

        {{-- SECTION HEADER --}}
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-100 to-orange-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-gray-900">Vehicle Information</h2>
                <p class="text-xs text-gray-500">Update your delivery vehicle details</p>
            </div>
        </div>

        <form method="POST" action="{{ route('rider.profile.update') }}" class="p-6 space-y-6">
            @csrf
            @method('PATCH')

            {{-- VEHICLE TYPE --}}
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-3">
                    Vehicle Type
                </label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @php
                        $vehicles = [
                            'Motorcycle' => ['emoji' => '🏍️', 'label' => 'Motorcycle'],
                            'Bicycle' => ['emoji' => '🚲', 'label' => 'Bicycle'],
                            'Car' => ['emoji' => '🚗', 'label' => 'Car'],
                            'E-bike' => ['emoji' => '⚡', 'label' => 'E-bike'],
                        ];
                    @endphp
                    @foreach ($vehicles as $type => $data)
                        <label class="relative cursor-pointer group">
                            <input type="radio"
                                   name="vehicle_type"
                                   value="{{ $type }}"
                                   @checked(old('vehicle_type', $rider->vehicle_type) === $type)
                                   class="peer sr-only">
                            <div class="border-2 border-gray-200 rounded-xl p-4 text-center transition-all
                                        peer-checked:border-orange-500 peer-checked:bg-orange-50 peer-checked:shadow-md
                                        hover:border-gray-300 hover:bg-gray-50">
                                <div class="text-3xl mb-2 transition-transform group-hover:scale-110">{{ $data['emoji'] }}</div>
                                <p class="text-xs font-semibold text-gray-700 peer-checked:text-orange-700">{{ $data['label'] }}</p>

                                {{-- Check indicator --}}
                                <div class="absolute top-2 right-2 w-5 h-5 rounded-full bg-orange-500 items-center justify-center hidden peer-checked:flex">
                                    <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- PLATE NUMBER --}}
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">
                    Plate Number
                    <span class="text-gray-400 font-normal">(optional)</span>
                </label>
                <div class="relative">
                    <input type="text"
                           name="vehicle_plate"
                           value="{{ old('vehicle_plate', $rider->vehicle_plate) }}"
                           placeholder="ABC-1234"
                           maxlength="20"
                           class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm uppercase tracking-widest font-mono font-semibold focus:ring-2 focus:ring-orange-500 focus:border-transparent transition bg-gray-50 focus:bg-white">
                    <div class="absolute right-3 top-1/2 -translate-y-1/2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Kung walang plaka (hal. bicycle), iwan lang na blangko.
                </p>
            </div>

            {{-- ACTIONS --}}
            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="flex-1 sm:flex-none bg-gradient-to-r from-orange-500 to-orange-600 text-white px-8 py-3 rounded-xl font-semibold text-sm shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg active:scale-95 transition transform flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Save Changes
                </button>
                <a href="{{ route('rider.dashboard') }}"
                   class="flex-1 sm:flex-none border border-gray-300 text-gray-700 px-8 py-3 rounded-xl font-semibold text-sm hover:bg-gray-50 text-center transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    {{-- ============================================ --}}
    {{-- ACCOUNT INFO --}}
    {{-- ============================================ --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-100 to-blue-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-gray-900">Account Information</h2>
                <p class="text-xs text-gray-500">Para baguhin, pumunta sa Settings</p>
            </div>
        </div>

        <div class="p-6">
            <div class="space-y-3">
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span class="text-sm text-gray-500">Name</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</span>
                </div>

                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span class="text-sm text-gray-500">Email</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900 truncate ml-4">{{ auth()->user()->email }}</span>
                </div>

                <div class="flex justify-between items-center py-3">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <span class="text-sm text-gray-500">Phone</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900">{{ auth()->user()->phone ?? 'Not set' }}</span>
                </div>
            </div>
        </div>

        {{-- QUICK ACTIONS --}}
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 grid grid-cols-2 gap-3">
            <a href="{{ route('settings.index') }}"
               class="flex items-center justify-center gap-2 bg-white border border-gray-300 text-gray-700 px-4 py-3 rounded-xl text-sm font-semibold hover:bg-gray-100 hover:border-gray-400 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Info
            </a>
            <a href="{{ route('profile.avatar') }}"
               class="flex items-center justify-center gap-2 bg-white border border-gray-300 text-gray-700 px-4 py-3 rounded-xl text-sm font-semibold hover:bg-gray-100 hover:border-gray-400 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Picture
            </a>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<style>
    .animate-fade-in {
        animation: fadeIn 0.3s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .active\:scale-95:active {
        transform: scale(0.95);
    }
</style>
@endpush