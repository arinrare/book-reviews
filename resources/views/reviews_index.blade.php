@extends('layouts.app')

@section('content')
    <style>
        /* Custom thin scrollbar for review title */
        .review-title-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .review-title-scrollbar::-webkit-scrollbar-thumb {
            background: #444;
            border-radius: 3px;
        }
        .review-title-scrollbar::-webkit-scrollbar-track {
            background: #222;
            border-radius: 3px;
        }
        .review-title-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #444 #222;
        }
    </style>
    <!-- Alpine.js for flip effect -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <div class="mt-5 font-sans font-bold text-3xl sm:text-4xl md:text-6xl lg:text-7xl text-center w-4/5 md:w-auto mx-auto">{{ $title }}</div>
    <div class="mx-auto mt-10 w-full max-w-6xl">
        <div class="pl-20 pr-20 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 w-full max-w-6xl mx-auto">
            @foreach($reviews as $review)
            <div class="perspective group w-full aspect-[1/1]" x-data="{ flipped: false }">
                <div class="relative w-full h-full transition-transform duration-500 preserve-3d group-hover:scale-105 group-focus:scale-105" :class="{ 'rotate-y-180': flipped }">
                    <div class="absolute inset-0 backface-hidden flex flex-col items-center justify-center bg-neutral-800/80 rounded-lg shadow-lg overflow-hidden">
                        <div 
                            class="review-title-scrollbar text-base sm:text-lg font-semibold mb-2 text-center w-full pl-3 pr-3 max-h-20 overflow-y-hidden"
                            style="word-break:break-word; pointer-events: auto; line-height: 1.3;"
                            tabindex="0"
                            x-init="
                                const checkOverflow = () => {
                                    setTimeout(() => {
                                        if ($el.scrollHeight > $el.clientHeight) {
                                            $el.style.overflowY = 'auto';
                                        } else {
                                            $el.style.overflowY = 'hidden';
                                        }
                                    }, 50);
                                };
                                $nextTick(checkOverflow);
                                // Check again after fonts load
                                document.fonts.ready.then(checkOverflow);
                            "
                            @wheel.stop="
                                const el = $event.currentTarget;
                                const delta = $event.deltaY;
                                if ((delta < 0 && el.scrollTop > 0) || (delta > 0 && el.scrollTop + el.clientHeight < el.scrollHeight)) {
                                    el.scrollTop += delta;
                                    $event.preventDefault();
                                }
                            "
                        >
                            {!! html_entity_decode($review['title']['rendered']) !!}
                        </div>
                        <div class="flex-1 w-full flex items-center justify-center rounded mb-2 relative">
                            @if(!empty($review['cover_url']))
                                <img src="{{ $review['cover_url'] }}"
                                    alt="Book cover for {{ $review['title']['rendered'] }}"
                                    style="background: transparent;"
                                    class="object-contain w-full h-full max-h-60 rounded"
                                    loading="lazy">
                                <!-- Click/Touch to flip: only on image area, only when not flipped -->
                                <button x-show="!flipped" @click="flipped = true" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" aria-label="Flip card"></button>
                            @else
                                <div class="w-full h-full flex items-center justify-center text-white">No Cover</div>
                            @endif
                        </div>
                    </div>
                    <!-- Back: Book Details -->
                    <div class="absolute inset-0 backface-hidden rotate-y-180 flex flex-col items-center justify-center bg-neutral-800/95 text-gray-100 rounded-lg shadow-lg p-4 h-full w-full" style="position: relative; min-height: 100%; min-width: 100%;">
                        <!-- Back button -->
                        <button @click="flipped = false" class="absolute top-2 left-2 z-20 bg-neutral-700 hover:bg-neutral-600 text-white rounded-full p-1 focus:outline-none focus:ring-2 focus:ring-blue-500" aria-label="Flip back">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <div class="text-sm font-semibold mb-1 text-center">{!! html_entity_decode($review['title']['rendered']) !!}</div>
                        <div class="text-xs mb-1"><strong>Review Date:</strong> {{ \Carbon\Carbon::parse($review['date'])->format('M d, Y') }}</div>
                        <div class="text-xs mb-1"><strong>Author:</strong> @if(!empty($review['novel_author_names'])) {{ implode(', ', $review['novel_author_names']) }} @else N/A @endif</div>
                        <div class="text-xs mb-1"><strong>Publisher:</strong> @if(!empty($review['publisher_names'])) {{ implode(', ', $review['publisher_names']) }} @else N/A @endif</div>
                        <div class="text-xs mb-1"><strong>Series:</strong> @if(!empty($review['series_names'])) {{ implode(', ', $review['series_names']) }} @else N/A @endif</div>
                        <div class="text-xs text-gray-100 mb-2 line-clamp-4">{!! \Illuminate\Support\Str::limit(strip_tags($review['content']['rendered']), 200) !!}</div>
                        <a href="{{ url('/review/' . ($review['slug'] ?? '')) }}" class="text-gray-200 hover:text-white hover:underline focus:underline active:underline transition-colors mt-2">Read full review</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    <div class="w-full flex justify-center mt-8 mb-4">
        @if(isset($currentPage) && isset($totalPages) && $totalPages > 1)
            <nav class="flex items-center space-x-2" x-data="{ page: {{ $currentPage }}, total: {{ $totalPages }} }">
                <!-- Skip to start -->
                <a href="?page=1" class="px-3 py-2 bg-neutral-700 text-white rounded hover:bg-neutral-600" :class="{ 'opacity-50 pointer-events-none': page === 1 }">&laquo;&laquo;</a>
                <!-- Previous -->
                <a href="?page={{ $currentPage - 1 }}" class="px-3 py-2 bg-neutral-700 text-white rounded hover:bg-neutral-600" :class="{ 'opacity-50 pointer-events-none': page === 1 }">&laquo;</a>

                <!-- Page input -->
                <span class="px-2 text-gray-300">Page</span>
                <form @submit.prevent="if(page >= 1 && page <= total) { window.location.search = '?page=' + page }" class="inline-block">
                    <input type="number" min="1" :max="total" x-model.number="page" class="w-16 px-2 py-1 rounded bg-neutral-900 text-white border border-neutral-700 text-center focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </form>
                <span class="px-2 text-gray-300">of {{ $totalPages }}</span>

                <!-- Next -->
                <a href="?page={{ $currentPage + 1 }}" class="px-3 py-2 bg-neutral-700 text-white rounded hover:bg-neutral-600" :class="{ 'opacity-50 pointer-events-none': page === total }">&raquo;</a>
                <!-- Skip to end -->
                <a href="?page={{ $totalPages }}" class="px-3 py-2 bg-neutral-700 text-white rounded hover:bg-neutral-600" :class="{ 'opacity-50 pointer-events-none': page === total }">&raquo;&raquo;</a>
            </nav>
        @endif
    </div>
@endsection
