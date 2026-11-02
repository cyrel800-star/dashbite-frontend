<div class="space-y-5">

    {{-- ============================================ --}}
    {{-- RATING SUMMARY --}}
    {{-- ============================================ --}}
    <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- Big Rating --}}
            <div class="text-center md:border-r border-gray-100 dark:border-dark-700 md:pr-6">
                <p class="text-6xl font-bold text-gray-900 dark:text-neutral-100">
                    {{ number_format((float)($restaurant->rating_avg ?? 0), 1) }}
                </p>

                <div class="flex justify-center gap-1 my-3">
                    @for ($i = 1; $i <= 5; $i++)
                        <svg class="w-6 h-6 {{ $i <= round($restaurant->rating_avg ?? 0) ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300 dark:text-neutral-600' }}"
                             viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    @endfor
                </div>

                <p class="text-sm text-gray-500 dark:text-neutral-400">
                    <strong class="text-gray-900 dark:text-neutral-100">{{ (int)($restaurant->rating_count ?? 0) }}</strong>
                    {{ Str::plural('review', (int)($restaurant->rating_count ?? 0)) }}
                </p>
            </div>

            {{-- Breakdown --}}
            <div class="md:col-span-2 space-y-2">
                @php $breakdown = $restaurant->rating_breakdown; @endphp
                @foreach ([5, 4, 3, 2, 1] as $stars)
                    @php $data = $breakdown[$stars] ?? ['count' => 0, 'percentage' => 0]; @endphp
                    <div class="flex items-center gap-3 text-sm">
                        <span class="w-10 text-gray-700 dark:text-neutral-300 font-medium">{{ $stars }}★</span>
                        <div class="flex-1 bg-gray-200 dark:bg-dark-700 rounded-full h-2 overflow-hidden">
                            <div class="h-full bg-yellow-400"
                                 style="width: {{ $data['percentage'] }}%"></div>
                        </div>
                        <span class="w-20 text-right text-gray-500 dark:text-neutral-400 text-xs">
                            {{ $data['count'] }} ({{ $data['percentage'] }}%)
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- REVIEWS LIST --}}
    {{-- ============================================ --}}
    @forelse ($reviews as $review)
        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 p-6"
             x-data='reviewActions(
                 {{ $review->id }},
                 {{ (int) $review->helpful_count }},
                 {{ (int) $review->not_helpful_count }},
                 @json($review->userVoteType()),
                 @json($review->hasUserReported())
             )'>

            {{-- Header --}}
            <div class="flex items-start gap-4 mb-4">
                <div class="flex-shrink-0">
                    @if ($review->user->avatar_url)
                        <img src="{{ $review->user->avatar_url }}"
                             class="w-12 h-12 rounded-full object-cover">
                    @else
                        <div class="w-12 h-12 rounded-full {{ $review->user->avatar_color }} flex items-center justify-center text-white text-sm font-bold">
                            {{ $review->user->initials }}
                        </div>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <p class="font-bold text-gray-900 dark:text-neutral-100">{{ $review->user->name }}</p>

                        @if ($review->is_verified_purchase)
                            <span class="inline-flex items-center gap-1 text-[10px] bg-green-100 dark:bg-green-950/40 text-green-700 dark:text-green-300 px-2 py-0.5 rounded-full font-bold uppercase tracking-wide">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                                Verified Purchase
                            </span>
                        @endif

                        @if ($review->is_edited)
                            <span class="text-[10px] text-gray-400 italic">edited</span>
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
            </div>

            {{-- Title --}}
            @if ($review->title)
                <p class="font-bold text-lg text-gray-900 dark:text-neutral-100 mb-2">{{ $review->title }}</p>
            @endif

            {{-- Body --}}
            <p class="text-gray-700 dark:text-neutral-300 whitespace-pre-wrap leading-relaxed">
                {{ $review->body }}
            </p>

            {{-- Photos --}}
            @if ($review->has_photos)
                <div class="flex gap-2 mt-4 overflow-x-auto pb-1" x-data="{ lightbox: null }">
                    @foreach ($review->image_urls as $url)
                        <img src="{{ $url }}"
                             class="w-20 h-20 rounded-xl object-cover cursor-pointer hover:opacity-90 transition flex-shrink-0"
                             @click="lightbox = '{{ $url }}'">
                    @endforeach

                    <div x-show="lightbox" x-cloak @click="lightbox = null"
                         class="fixed inset-0 bg-black/90 z-[200] flex items-center justify-center p-4">
                        <img :src="lightbox" class="max-w-full max-h-full rounded-lg" @click.stop>
                    </div>
                </div>
            @endif

            {{-- Owner Reply --}}
            @if ($review->replies->first())
                @php $reply = $review->replies->first(); @endphp
                <div class="mt-4 ml-6 p-4 bg-orange-50 dark:bg-orange-950/20 rounded-xl border-l-4 border-orange-500">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-6 h-6 rounded-full bg-orange-500 flex items-center justify-center text-white">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <p class="text-xs font-bold text-orange-700 dark:text-orange-300 uppercase tracking-wide">
                            Reply from {{ $restaurant->name }}
                        </p>
                    </div>
                    <p class="text-sm text-gray-700 dark:text-neutral-300">{{ $reply->body }}</p>
                </div>
            @endif

            {{-- Actions --}}
            <div class="flex items-center gap-4 mt-4 pt-4 border-t border-gray-100 dark:border-dark-700 text-sm">

                <button type="button" @click="vote('helpful')"
                        :disabled="loading"
                        :class="userVote === 'helpful' ? 'text-orange-600 dark:text-orange-400 font-bold' : 'text-gray-500 dark:text-neutral-400 hover:text-orange-600'"
                        class="flex items-center gap-1.5 transition disabled:opacity-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                    </svg>
                    <span x-text="'Helpful (' + helpfulCount + ')'"></span>
                </button>

                <button type="button" @click="vote('not_helpful')"
                        :disabled="loading"
                        :class="userVote === 'not_helpful' ? 'text-gray-700 dark:text-neutral-100 font-bold' : 'text-gray-500 dark:text-neutral-400 hover:text-gray-700'"
                        class="flex items-center gap-1.5 transition disabled:opacity-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.096c.5 0 .905-.405.905-.904 0-.715.211-1.413.608-2.008L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5" />
                    </svg>
                    <span x-text="'Not Helpful (' + notHelpfulCount + ')'"></span>
                </button>

                @auth
                    @if ($review->user_id !== auth()->id())
                        <button type="button" @click="report()"
                                :disabled="hasReported || loading"
                                :class="hasReported ? 'text-gray-400 cursor-not-allowed' : 'text-gray-400 hover:text-red-500'"
                                class="ml-auto flex items-center gap-1.5 transition text-xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span x-text="hasReported ? 'Reported' : 'Report'"></span>
                        </button>
                    @endif
                @endauth
            </div>
        </div>
    @empty
        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 p-12 text-center">
            <div class="flex justify-center mb-3">
                <svg class="w-16 h-16 text-gray-300 dark:text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-neutral-100 mb-1">Wala pang reviews</h3>
            <p class="text-sm text-gray-500 dark:text-neutral-400">Be the first to review {{ $restaurant->name }}!</p>
        </div>
    @endforelse

    {{-- Pagination --}}
    @if ($reviews->hasPages())
        <div class="mt-4">
            {{ $reviews->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>
function reviewActions(reviewId, initialHelpful, initialNotHelpful, initialVote, initialReported) {
    return {
        reviewId: reviewId,
        helpfulCount: Number(initialHelpful) || 0,
        notHelpfulCount: Number(initialNotHelpful) || 0,
        userVote: initialVote || null,
        hasReported: initialReported === true,
        loading: false,

        async vote(type) {
            if (this.loading) return;
            this.loading = true;

            try {
                const res = await fetch(`/reviews/${this.reviewId}/vote`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ vote_type: type }),
                });

                const data = await res.json();

                if (!res.ok) {
                    console.error('Vote failed:', data);
                    alert(data.message || 'Hindi ma-save ang vote. Subukan muli.');
                    this.loading = false;
                    return;
                }

                if (data.ok) {
                    this.helpfulCount = Number(data.helpful) || 0;
                    this.notHelpfulCount = Number(data.not_helpful) || 0;
                    this.userVote = data.user_vote;
                }
            } catch (err) {
                console.error('Network error:', err);
                alert('Network error. Subukan muli.');
            }

            this.loading = false;
        },

        async report() {
            if (this.hasReported || this.loading) return;
            const reason = prompt('Bakit mo ito nire-report?\n\nOptions: spam, offensive, fake, irrelevant, other');
            if (!reason) return;

            const cleanReason = reason.toLowerCase().trim();
            const validReasons = ['spam', 'offensive', 'fake', 'irrelevant', 'other'];
            if (!validReasons.includes(cleanReason)) {
                alert('Invalid reason. Pumili sa: ' + validReasons.join(', '));
                return;
            }

            this.loading = true;
            try {
                const res = await fetch(`/reviews/${this.reviewId}/report`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ reason: cleanReason }),
                });
                const data = await res.json();
                if (data.ok) {
                    this.hasReported = true;
                    alert(data.message || 'Report submitted.');
                } else {
                    alert(data.message || 'Hindi ma-submit.');
                }
            } catch (err) {
                alert('Network error.');
            }
            this.loading = false;
        }
    }
}
</script>
@endpush