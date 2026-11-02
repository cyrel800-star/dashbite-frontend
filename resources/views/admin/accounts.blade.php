@extends('layouts.app')

{{--
    FOODDASH COLOR SYSTEM (mandatory, used as-is)
    Primary   Orange     #FF6B35  -> actions, active tab, approved, highlights
    Secondary Dark Gray  #2D2D2D  -> table header, titles, secondary buttons
    Background White     #FFFFFF  -> page cards and table body
    Only tints of these three colors are used. No red, no amber.
--}}

@section('content')
<div x-data="{
    showDeleteModal: false,
    deleteUserName: '',
    deleteUrl: ''
}" class="max-w-7xl mx-auto">

    {{-- ============================================ --}}
    {{-- ALERTS --}}
    {{-- ============================================ --}}
    @if (session('success'))
        <div class="mb-4 p-3 bg-[#FF6B35]/10 border-l-4 border-[#FF6B35] rounded-xl text-sm flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-[#FF6B35] flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <span class="text-[#2D2D2D] dark:text-white font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-3 bg-[#2D2D2D] border-l-4 border-[#FF6B35] rounded-xl text-sm flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-[#FF6B35] flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <span class="text-white font-semibold">{{ session('error') }}</span>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- FILTER TABS --}}
    {{-- ============================================ --}}
    <div class="bg-white dark:bg-[#2D2D2D] rounded-2xl border border-[#2D2D2D]/10 dark:border-white/10 p-2 mb-4 overflow-x-auto">
        <div class="flex gap-1 min-w-max">
            @php
                $tabs = [
                    'all' => 'All',
                    'customer' => 'Customers',
                    'restaurant' => 'Restaurants',
                    'rider' => 'Riders',
                    'pending' => 'Pending',
                    'disabled' => 'Disabled',
                ];
            @endphp

            @foreach ($tabs as $key => $label)
                <a href="{{ route('admin.accounts', ['filter' => $key]) }}"
                   class="px-4 py-2 rounded-full text-sm font-bold whitespace-nowrap transition focus:outline-none focus-visible:ring-2 focus-visible:ring-[#FF6B35]
                          {{ $filter === $key
                                ? 'bg-[#FF6B35] text-white shadow-sm'
                                : 'text-[#2D2D2D] dark:text-white hover:bg-[#FF6B35]/10' }}">
                    {{ $label }}
                    <span class="ml-1 text-xs font-semibold {{ $filter === $key ? 'text-white/85' : 'text-[#2D2D2D]/50 dark:text-white/50' }}">
                        ({{ $counts[$key] }})
                    </span>
                </a>
            @endforeach
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- ACCOUNTS TABLE --}}
    {{-- ============================================ --}}
    <div class="bg-white dark:bg-[#2D2D2D] rounded-2xl border border-[#2D2D2D]/10 dark:border-white/10 overflow-hidden">

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                {{-- Dark gray header, same look as the FoodDash sidebar --}}
                <thead class="bg-[#2D2D2D] dark:bg-black/30 border-b-2 border-[#FF6B35]">
                    <tr>
                        <th class="text-left px-5 py-3.5 font-semibold text-white text-sm">Name</th>
                        <th class="text-left px-5 py-3.5 font-semibold text-white text-sm">Email</th>
                        <th class="text-left px-5 py-3.5 font-semibold text-white text-sm">Role</th>
                        <th class="text-left px-5 py-3.5 font-semibold text-white text-sm">Status</th>
                        <th class="text-left px-5 py-3.5 font-semibold text-white text-sm">Registered</th>
                        <th class="text-right px-5 py-3.5 font-semibold text-white text-sm">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2D2D2D]/10 dark:divide-white/10">
                    @forelse ($users as $user)
                        <tr class="hover:bg-[#FF6B35]/5 transition">

                            {{-- NAME + PHONE (orange initial circle) --}}
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-[#FF6B35] text-white font-bold flex items-center justify-center flex-shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-[#2D2D2D] dark:text-white">{{ $user->name }}</div>
                                        @if ($user->phone)
                                            <div class="text-xs text-[#2D2D2D]/60 dark:text-white/60 mt-0.5">{{ $user->phone }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- EMAIL --}}
                            <td class="px-5 py-3.5 text-[#2D2D2D]/70 dark:text-white/70">{{ $user->email }}</td>

                            {{-- ROLE BADGE --}}
                            <td class="px-5 py-3.5">
                                @php
                                    $roleStyles = [
                                        'admin' => 'bg-[#2D2D2D] text-white border-[#2D2D2D] dark:bg-white dark:text-[#2D2D2D] dark:border-white',
                                        'restaurant' => 'bg-[#FF6B35]/10 text-[#2D2D2D] border-[#FF6B35]/40 dark:text-white',
                                        'rider' => 'bg-[#FF6B35]/10 text-[#2D2D2D] border-[#FF6B35]/40 dark:text-white',
                                        'customer' => 'bg-white text-[#2D2D2D] border-[#2D2D2D]/20 dark:bg-transparent dark:text-white dark:border-white/20',
                                    ];
                                    $roleClass = $roleStyles[$user->role] ?? 'bg-white text-[#2D2D2D] border-[#2D2D2D]/20 dark:bg-transparent dark:text-white dark:border-white/20';
                                @endphp
                                <span class="inline-block text-xs px-2.5 py-1 rounded-full border font-bold {{ $roleClass }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>

                            {{-- STATUS BADGE --}}
                            <td class="px-5 py-3.5">
                                @php
                                    $statusStyles = [
                                        'approved' => 'bg-[#FF6B35] text-white border-[#FF6B35]',
                                        'pending' => 'bg-white text-[#2D2D2D] border-[#FF6B35] dark:bg-transparent dark:text-white',
                                        'disabled' => 'bg-[#2D2D2D]/10 text-[#2D2D2D] border-[#2D2D2D]/20 dark:bg-white/10 dark:text-white dark:border-white/20',
                                    ];
                                    $statusClass = $statusStyles[$user->status] ?? 'bg-[#2D2D2D]/10 text-[#2D2D2D] border-[#2D2D2D]/20 dark:bg-white/10 dark:text-white dark:border-white/20';

                                    $statusDot = [
                                        'approved' => 'bg-white',
                                        'pending' => 'bg-[#FF6B35]',
                                        'disabled' => 'bg-[#2D2D2D]/50 dark:bg-white/50',
                                    ];
                                    $dotClass = $statusDot[$user->status] ?? 'bg-[#2D2D2D]/50';
                                @endphp
                                <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-full border font-bold {{ $statusClass }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }}"></span>
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>

                            {{-- REGISTERED DATE --}}
                            <td class="px-5 py-3.5 text-xs text-[#2D2D2D]/60 dark:text-white/60">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>

                            {{-- ACTIONS --}}
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-1.5">

                                    {{-- APPROVE (primary action = solid orange) --}}
                                    @if ($user->status !== 'approved')
                                        <form method="POST" action="{{ route('admin.users.approve', $user) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    title="Approve user"
                                                    class="inline-flex items-center gap-1 text-xs font-bold text-white bg-[#FF6B35] hover:brightness-95 px-3 py-1.5 rounded-full transition shadow-sm active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2D2D2D]">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Approve
                                            </button>
                                        </form>
                                    @endif

                                    {{-- DISABLE (secondary action = orange outline) --}}
                                    @if ($user->status !== 'disabled' && $user->role !== 'admin')
                                        <form method="POST" action="{{ route('admin.users.disable', $user) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    title="Disable user"
                                                    data-confirm="Disable {{ $user->name }}? They won't be able to log in."
                                                    data-confirm-ok="Disable"
                                                    class="inline-flex items-center gap-1 text-xs font-bold text-[#2D2D2D] dark:text-white bg-white dark:bg-transparent hover:bg-[#FF6B35]/10 border border-[#FF6B35] px-3 py-1.5 rounded-full transition focus:outline-none focus-visible:ring-2 focus-visible:ring-[#FF6B35]">
                                                <svg class="w-3.5 h-3.5 text-[#FF6B35]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                </svg>
                                                Disable
                                            </button>
                                        </form>
                                    @endif

                                    {{-- DELETE (final action = dark gray) --}}
                                    @if ($user->role !== 'admin')
                                        <button type="button"
                                                title="Delete user"
                                                @click="showDeleteModal = true; deleteUserName = @js($user->name); deleteUrl = @js(route('admin.users.destroy', $user));"
                                                class="inline-flex items-center gap-1 text-xs font-bold text-white bg-[#2D2D2D] dark:bg-white dark:text-[#2D2D2D] hover:bg-black dark:hover:bg-white/80 px-3 py-1.5 rounded-full transition focus:outline-none focus-visible:ring-2 focus-visible:ring-[#FF6B35]">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Delete
                                        </button>
                                    @endif

                                    {{-- ADMIN: NO ACTIONS --}}
                                    @if ($user->role === 'admin' && $user->status === 'approved')
                                        <span class="text-xs text-[#2D2D2D]/40 dark:text-white/40 italic px-2">—</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-[#FF6B35]/10 mb-3">
                                    <svg class="w-8 h-8 text-[#FF6B35]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <p class="text-sm text-[#2D2D2D]/70 dark:text-white/70 font-semibold">No accounts match this filter</p>
                                <p class="text-xs text-[#2D2D2D]/50 dark:text-white/50 mt-1">Pick another tab above to see more accounts.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- DELETE CONFIRMATION MODAL --}}
    {{-- ============================================ --}}
    <div x-show="showDeleteModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         style="display: none;"
         @click.self="showDeleteModal = false">
        <div class="w-full max-w-md rounded-2xl bg-white dark:bg-[#2D2D2D] border border-[#2D2D2D]/10 dark:border-white/10 shadow-2xl overflow-hidden">
            <div class="p-5">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-11 h-11 rounded-full bg-[#FF6B35]/10 flex items-center justify-center">
                        <svg class="w-5 h-5 text-[#FF6B35]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M5.625 19h12.75A1.875 1.875 0 0020.25 17.125V6.875A1.875 1.875 0 0018.375 5H5.625A1.875 1.875 0 003.75 6.875v10.25A1.875 1.875 0 005.625 19z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-[#2D2D2D] dark:text-white">Delete <span x-text="deleteUserName"></span> permanently?</h3>
                        <p class="mt-2 text-sm text-[#2D2D2D]/70 dark:text-white/70">This cannot be undone.</p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button"
                            @click="showDeleteModal = false"
                            class="px-4 py-2 text-sm font-semibold text-[#2D2D2D] dark:text-white hover:bg-[#2D2D2D]/5 dark:hover:bg-white/5 rounded-lg transition">
                        Cancel
                    </button>

                    <form :action="deleteUrl" method="POST" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-4 py-2 text-sm font-semibold text-white bg-[#2D2D2D] hover:bg-black rounded-lg transition">
                            Delete Permanently
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- PAGINATION --}}
    {{-- ============================================ --}}
    @if ($users->hasPages())
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    @endif

</div>
@endsection
