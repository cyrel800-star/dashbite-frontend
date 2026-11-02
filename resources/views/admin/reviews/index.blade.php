@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-neutral-100">Review Moderation</h1>
            <p class="text-sm text-gray-500 dark:text-neutral-400 mt-1">
                Manage and moderate customer reviews
            </p>
        </div>
        <a href="{{ route('admin.dashboard') }}"
           class="text-sm text-gray-600 dark:text-neutral-400 hover:text-gray-900 dark:hover:text-white font-medium">
            ← Back to Dashboard
        </a>
    </div>

    {{-- Filter Tabs --}}
    <div class="bg-white dark:bg-dark-800 rounded-lg border border-gray-200 dark:border-dark-700 p-2 mb-4 overflow-x-auto">
        <div class="flex gap-1 min-w-max">
            @foreach (['all' => 'All', 'published' => 'Published', 'hidden' => 'Hidden', 'flagged' => 'Flagged'] as $key => $label)
                <a href="{{ route('admin.reviews.index', ['status' => $key === 'flagged' ? '' : $key, 'flagged' => $key === 'flagged' ? 1 : null]) }}"
                   class="px-3.5 py-2 rounded-md text-sm font-medium transition
                          {{ (request('status') === $key || ($key === 'all' && !request('status') && !request('flagged')))
                              ? 'bg-gray-900 dark:bg-dark-700 text-white'
                              : 'text-gray-600 dark:text-neutral-400 hover:bg-gray-100 dark:hover:bg-dark-850' }}">
                    {{ $label }}
                    <span class="ml-1 text-xs">
                        ({{ $counts[$key === 'flagged' ? 'flagged' : $key] ?? 0 }})
                    </span>
                </a>
            @endforeach
        </div>
    </div>

    {{-- Reviews List --}}
    <div class="space-y-4">
        @forelse ($reviews as $review)
            <div class="bg-white dark:bg-dark-800 rounded-lg border border-gray-200 dark:border-dark-700 p-5">

                @php
                    $statusColors = [
                        'published' => 'bg-green-50 dark:bg-green-950/40 text-green-700 dark:text-green-300 border-green-200 dark:border-green-800',
                        'hidden' => 'bg-gray-100 dark:bg-dark-850 text-gray-700 dark:text-neutral-300 border-gray-200 dark:border-dark-600',
                        'flagged' => 'bg-red-50 dark:bg-red-950/40 text-red-700 dark:text-red-300 border-red-200 dark:border-red-800',
                        'pending' => 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800',
                    ];
                @endphp

                {{-- Header --}}
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full {{ $review->user->avatar_color }} flex items-center justify-center text-white font-bold flex-shrink-0">
                            {{ $review->user->initials }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-neutral-100">
                                {{ $review->user->name }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-neutral-400">
                                {{ $review->restaurant->name }} · {{ $review->created_at->format('M d, Y H:i') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs px-2 py-1 rounded-md border font-medium {{ $statusColors[$review->status] ?? '' }}">
                            {{ ucfirst($review->status) }}
                        </span>
                        @if ($review->report_count > 0)
                            <span class="text-xs px-2 py-1 rounded-md bg-red-100 dark:bg-red-950/40 text-red-700 dark:text-red-300 font-bold">
                                ⚠️ {{ $review->report_count }} reports
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Rating --}}
                <div class="flex items-center gap-1 mb-2">
                    @for ($i = 1; $i <= 5; $i++)
                        <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300 dark:text-neutral-600' }}"
                             viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    @endfor
                    @if ($review->is_verified_purchase)
                        <span class="ml-2 text-[10px] bg-green-100 dark:bg-green-950/40 text-green-700 dark:text-green-300 px-1.5 py-0.5 rounded font-bold uppercase">
                            ✓ Verified
                        </span>
                    @endif
                </div>

                {{-- Title & Body --}}
                @if ($review->title)
                    <p class="font-bold text-gray-900 dark:text-neutral-100 mb-1">
                        {{ $review->title }}
                    </p>
                @endif
                <p class="text-sm text-gray-700 dark:text-neutral-300 line-clamp-3">
                    {{ $review->body }}
                </p>

                {{-- Stats --}}
                <div class="flex items-center gap-4 mt-3 text-xs text-gray-500 dark:text-neutral-400">
                    <span>👍 {{ $review->helpful_count }} helpful</span>
                    <span>👎 {{ $review->not_helpful_count }} not helpful</span>
                    @if ($review->has_photos)
                        <span>📷 {{ count($review->images) }} photos</span>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex gap-2 mt-4 pt-4 border-t border-gray-100 dark:border-dark-700">
                    @if ($review->status === 'published')
                        <form method="POST" action="{{ route('admin.reviews.hide', $review) }}">
                            @csrf
                            @method('PATCH')
                            <button class="text-xs font-medium text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 hover:bg-amber-100 px-3 py-1.5 rounded-md border border-amber-200 dark:border-amber-800 transition">
                                Hide
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.reviews.publish', $review) }}">
                            @csrf
                            @method('PATCH')
                            <button class="text-xs font-medium text-green-700 dark:text-green-400 bg-green-50 dark:bg-green-950/40 hover:bg-green-100 px-3 py-1.5 rounded-md border border-green-200 dark:border-green-800 transition">
                                Publish
                            </button>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}"
                          data-confirm="Permanently delete this review?"
                          data-confirm-ok="Delete">
                        @csrf
                        @method('DELETE')
                        <button class="text-xs font-medium text-red-700 dark:text-red-400 bg-red-50 dark:bg-red-950/40 hover:bg-red-100 px-3 py-1.5 rounded-md border border-red-200 dark:border-red-800 transition">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-dark-800 rounded-lg border border-gray-200 dark:border-dark-700 p-12 text-center">
                <p class="text-4xl mb-2">📝</p>
                <p class="text-sm text-gray-500 dark:text-neutral-400">No reviews found.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if ($reviews->hasPages())
        <div class="mt-4">
            {{ $reviews->links() }}
        </div>
    @endif

</div>
@endsection
