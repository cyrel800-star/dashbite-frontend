@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-5">

    {{-- ============================================ --}}
    {{-- HERO HEADER --}}
    {{-- ============================================ --}}
    <div class="relative overflow-hidden bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 shadow-xl">

        {{-- COVER PHOTO --}}
        <div class="relative h-32 overflow-hidden">
            @if ($restaurant->cover_image_url)
                <img src="{{ $restaurant->cover_image_url }}"
                     alt="{{ $restaurant->name }}"
                     class="absolute inset-0 w-full h-full object-cover">
            @else
                <div class="absolute inset-0 bg-gradient-to-br from-yellow-500 via-amber-500 to-orange-500"></div>
                <div class="absolute inset-0 opacity-20"
                     style="background-image: radial-gradient(circle at 20% 50%, white 1px, transparent 1px), radial-gradient(circle at 80% 80%, white 1px, transparent 1px); background-size: 40px 40px;"></div>
            @endif

            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>

            <a href="{{ route('restaurant.dashboard') }}"
               class="absolute top-4 left-4 w-10 h-10 rounded-xl bg-white/90 dark:bg-dark-800/90 backdrop-blur flex items-center justify-center shadow-lg hover:bg-white transition z-10">
                <svg class="w-5 h-5 text-gray-700 dark:text-neutral-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>

            <div class="absolute top-4 right-4 bg-white/90 dark:bg-dark-800/90 backdrop-blur rounded-full px-3 py-1.5 shadow-lg">
                <span class="text-xs font-bold uppercase tracking-wide text-gray-700 dark:text-neutral-300">
                    {{ $stats['total'] }} Reviews
                </span>
            </div>
        </div>

        <div class="relative px-6 -mt-10">
            <div class="flex items-end gap-4 mb-5">
                <div class="w-20 h-20 rounded-2xl bg-white dark:bg-dark-800 border-4 border-white dark:border-dark-800 shadow-lg flex items-center justify-center flex-shrink-0 overflow-hidden">
                    @if ($restaurant->profile_image_url)
                        <img src="{{ $restaurant->profile_image_url }}"
                             alt="{{ $restaurant->name }}"
                             class="w-full h-full object-cover">
                    @else
                        <svg class="w-10 h-10 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    @endif
                </div>

                <div class="flex-1 pb-1 min-w-0">
                    <p class="text-xs text-gray-500 dark:text-neutral-400 uppercase tracking-wider font-medium">Restaurant</p>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-neutral-100 leading-tight truncate">Customer Reviews</h1>
                </div>
            </div>

            {{-- SUMMARY STATS --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 pb-6 border-t border-gray-100 dark:border-dark-700 pt-5">
                <div>
                    <p class="text-[10px] text-gray-500 dark:text-neutral-400 uppercase tracking-wider font-medium">Average Rating</p>
                    <div class="flex items-center gap-1 mt-1">
                        <p class="text-xl font-bold text-gray-900 dark:text-neutral-100">{{ $stats['avg'] }}</p>
                        <svg class="w-4 h-4 fill-yellow-400 text-yellow-400" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    </div>
                </div>
                <div class="md:border-l border-gray-100 dark:border-dark-700 md:pl-3">
                    <p class="text-[10px] text-gray-500 dark:text-neutral-400 uppercase tracking-wider font-medium">Total</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-neutral-100 mt-1">{{ $stats['total'] }}</p>
                </div>
                <div class="md:border-l border-gray-100 dark:border-dark-700 md:pl-3">
                    <p class="text-[10px] text-gray-500 dark:text-neutral-400 uppercase tracking-wider font-medium">Replied</p>
                    <p class="text-xl font-bold text-green-600 dark:text-green-400 mt-1">{{ $stats['replied'] }}</p>
                </div>
                <div class="md:border-l border-gray-100 dark:border-dark-700 md:pl-3">
                    <p class="text-[10px] text-gray-500 dark:text-neutral-400 uppercase tracking-wider font-medium">Unreplied</p>
                    <p class="text-xl font-bold {{ $stats['unreplied'] > 0 ? 'text-orange-600 dark:text-orange-400' : 'text-gray-900 dark:text-neutral-100' }} mt-1">{{ $stats['unreplied'] }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- ALERTS --}}
    {{-- ============================================ --}}
    @if (session('success'))
        <div class="p-4 bg-green-50 dark:bg-green-950/30 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 rounded-xl text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 rounded-xl text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- FILTERS --}}
    {{-- ============================================ --}}
    <form method="GET" class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 p-5">
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-4 h-4 text-gray-500 dark:text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            <p class="text-sm font-semibold text-gray-900 dark:text-neutral-100">Filters</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            {{-- RATING --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-neutral-300 mb-1.5">Rating</label>
                <select name="rating"
                        class="w-full border border-gray-300 dark:border-dark-600 dark:bg-dark-850 dark:text-neutral-100 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    <option value="">All Ratings</option>
                    @foreach ([5, 4, 3, 2, 1] as $r)
                        <option value="{{ $r }}" @selected(request('rating') == $r)>
                            {{ str_repeat('★', $r) }} ({{ $r }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- STATUS --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-neutral-300 mb-1.5">Reply Status</label>
                <select name="status"
                        class="w-full border border-gray-300 dark:border-dark-600 dark:bg-dark-850 dark:text-neutral-100 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    <option value="">All</option>
                    <option value="replied" @selected(request('status') === 'replied')>Replied</option>
                    <option value="unreplied" @selected(request('status') === 'unreplied')>Unreplied</option>
                </select>
            </div>

            {{-- ACTIONS --}}
            <div class="flex items-end gap-2">
                <button type="submit"
                        class="flex-1 bg-gradient-to-r from-orange-500 to-orange-600 text-white px-4 py-2.5 rounded-xl font-semibold text-sm shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg active:scale-98 transition transform">
                    Apply
                </button>
                <a href="{{ route('restaurant.reviews.index') }}"
                   class="px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-neutral-300 border border-gray-300 dark:border-dark-600 rounded-xl hover:bg-gray-50 dark:hover:bg-dark-850 transition">
                    Reset
                </a>
            </div>
        </div>
    </form>

    {{-- ============================================ --}}
    {{-- REVIEWS LIST --}}
    {{-- ============================================ --}}
    @if ($reviews->isEmpty())
        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 p-12 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-dark-850 mb-4">
                <svg class="w-8 h-8 text-gray-400 dark:text-neutral-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
            </div>
            <h3 class="font-semibold text-gray-900 dark:text-neutral-100 mb-1">No reviews yet</h3>
            <p class="text-sm text-gray-500 dark:text-neutral-400">Customer reviews will appear here</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($reviews as $review)
                <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 overflow-hidden">

                    {{-- REVIEW HEADER --}}
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-dark-700 flex items-start gap-3">
                        {{-- Customer Avatar --}}
                        @if ($review->user->avatar_url)
                            <img src="{{ $review->user->avatar_url }}"
                                 alt="{{ $review->user->name }}"
                                 class="w-11 h-11 rounded-full object-cover flex-shrink-0">
                        @else
                            <div class="w-11 h-11 rounded-full {{ $review->user->avatar_color }} flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                                {{ $review->user->initials }}
                            </div>
                        @endif

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <p class="font-bold text-gray-900 dark:text-neutral-100">
                                    {{ $review->user->name }}
                                </p>
                                @if ($review->is_verified_purchase)
                                    <span class="inline-flex items-center gap-1 text-[10px] bg-green-100 dark:bg-green-950/40 text-green-700 dark:text-green-300 px-2 py-0.5 rounded-full font-bold uppercase tracking-wide">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Verified
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="flex gap-0.5">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300 dark:text-neutral-600' }}"
                                             viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @endfor
                                </div>
                                <span class="text-xs text-gray-500 dark:text-neutral-400">{{ $review->time_ago }}</span>
                            </div>
                        </div>

                        {{-- Reply Status Badge --}}
                        @if ($review->replies->first())
                            <span class="text-[10px] bg-green-100 dark:bg-green-950/40 text-green-700 dark:text-green-300 px-2 py-1 rounded-full font-bold uppercase tracking-wide flex-shrink-0">
                                Replied
                            </span>
                        @else
                            <span class="text-[10px] bg-orange-100 dark:bg-orange-950/40 text-orange-700 dark:text-orange-300 px-2 py-1 rounded-full font-bold uppercase tracking-wide flex-shrink-0">
                                Awaiting Reply
                            </span>
                        @endif
                    </div>

                    {{-- REVIEW BODY --}}
                    <div class="p-5">
                        @if ($review->title)
                            <p class="font-bold text-gray-900 dark:text-neutral-100 mb-2">{{ $review->title }}</p>
                        @endif

                        <p class="text-gray-700 dark:text-neutral-300 whitespace-pre-wrap leading-relaxed text-sm">
                            {{ $review->body }}
                        </p>

                        {{-- Photos --}}
                        @if ($review->has_photos)
                            <div class="flex gap-2 mt-3 overflow-x-auto pb-1">
                                @foreach ($review->image_urls as $url)
                                    <img src="{{ $url }}"
                                         class="w-16 h-16 rounded-lg object-cover cursor-pointer hover:opacity-90 transition flex-shrink-0">
                                @endforeach
                            </div>
                        @endif

                        {{-- Order info --}}
                        <div class="flex items-center gap-2 mt-3 text-xs text-gray-500 dark:text-neutral-400">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Order #{{ $review->order_id }}
                        </div>
                    </div>

                    {{-- EXISTING REPLY --}}
                    @if ($reply = $review->replies->first())
                        <div class="px-5 pb-5">
                            <div class="ml-6 p-4 bg-orange-50 dark:bg-orange-950/20 rounded-xl border-l-4 border-orange-500">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-6 h-6 rounded-full bg-orange-500 flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    <p class="text-xs font-bold text-orange-700 dark:text-orange-300 uppercase tracking-wide">
                                        Your Reply
                                    </p>
                                    <span class="text-[10px] text-orange-600 dark:text-orange-400 ml-auto">
                                        {{ $reply->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-700 dark:text-neutral-300">{{ $reply->body }}</p>
                            </div>
                        </div>
                    @else
                        {{-- REPLY FORM --}}
                        <div class="px-5 pb-5" x-data="{ showReplyForm: false }">
                            <button type="button"
                                    @click="showReplyForm = !showReplyForm"
                                    class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white py-2.5 rounded-xl font-semibold text-sm shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg active:scale-98 transition transform">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                </svg>
                                <span x-show="!showReplyForm">Reply to Review</span>
                                <span x-show="showReplyForm" x-cloak>Cancel</span>
                            </button>

                            <div x-show="showReplyForm" x-cloak x-transition class="mt-3">
                                <form method="POST"
                                      action="{{ route('restaurant.reviews.reply', $review) }}"
                                      class="space-y-3">
                                    @csrf
                                    <textarea name="body"
                                              rows="3"
                                              required
                                              minlength="5"
                                              maxlength="1000"
                                              placeholder="Salamat sa feedback! We appreciate your review..."
                                              class="w-full border border-gray-300 dark:border-dark-600 dark:bg-dark-850 dark:text-neutral-100 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-none"></textarea>

                                    <div class="flex gap-2">
                                        <button type="submit"
                                                class="flex-1 bg-gradient-to-r from-orange-500 to-orange-600 text-white py-2.5 rounded-xl font-semibold text-sm shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg active:scale-98 transition transform">
                                            Post Reply
                                        </button>
                                        <button type="button"
                                                @click="showReplyForm = false"
                                                class="px-4 py-2.5 border border-gray-300 dark:border-dark-600 text-gray-700 dark:text-neutral-300 rounded-xl text-sm font-semibold hover:bg-gray-50 dark:hover:bg-dark-850 transition">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- PAGINATION --}}
    {{-- ============================================ --}}
    @if ($reviews->hasPages())
        <div class="mt-4">
            {{ $reviews->links() }}
        </div>
    @endif

</div>
@endsection