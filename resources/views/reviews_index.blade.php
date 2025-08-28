@vite('resources/css/app.css')

<body class="text-white min-h-screen w-full bg-cover bg-center bg-no-repeat flex flex-col" style="background-image: url('/images/fantastic_library.jpg')">
    <x-navbar />
    <main class="flex-1 flex flex-col items-center w-full pl-5 pr-5">
        <div class="mt-5 font-sans font-bold text-3xl sm:text-4xl md:text-6xl lg:text-7xl text-center w-4/5 md:w-auto mx-auto">{{ $title }}</div>
        <!-- Alpine.js for flip effect -->
        <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
        <div class="mx-auto mt-10 w-full max-w-6xl">
           <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 w-full max-w-6xl mx-auto">
                @foreach($reviews as $review)
                <div class="perspective group w-full aspect-[1/1]" x-data="{ flipped: false }">
                    <div class="relative w-full h-full transition-transform duration-500 preserve-3d group-hover:scale-105 group-focus:scale-105" :class="{ 'rotate-y-180': flipped }">
                        <div class="absolute inset-0 backface-hidden flex flex-col items-center justify-center bg-neutral-800/80 rounded-lg shadow-lg overflow-hidden">
                            <div class="text-lg font-bold mb-2 text-center w-full pl-5 pr-5">{!! html_entity_decode($review['title']['rendered']) !!}</div>
                            <div class="flex-1 w-full flex items-center justify-center rounded mb-2">
                                @if(!empty($review['cover_url']))
                                    <img src="{{ $review['cover_url'] }}"
                                        alt="Book cover for {{ $review['title']['rendered'] }}"
                                        style="background: transparent;"
                                        class="object-contain w-full h-full max-h-60 rounded"
                                        loading="lazy">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-white">No Cover</div>
                                @endif
                            </div>
                        </div>
                        <!-- Back: Book Details -->
                        <div class="absolute inset-0 backface-hidden rotate-y-180 flex flex-col items-center justify-center bg-neutral-800/95 text-gray-100 rounded-lg shadow-lg p-4">
                            <div class="text-sm font-semibold mb-1 text-center">{!! html_entity_decode($review['title']['rendered']) !!}</div>
                            <div class="text-xs mb-1"><strong>Review Date:</strong> {{ \Carbon\Carbon::parse($review['date'])->format('M d, Y') }}</div>
                            <div class="text-xs mb-1"><strong>Author:</strong> @if(!empty($review['novel_author_names'])) {{ implode(', ', $review['novel_author_names']) }} @else N/A @endif</div>
                            <div class="text-xs mb-1"><strong>Publisher:</strong> @if(!empty($review['publisher_names'])) {{ implode(', ', $review['publisher_names']) }} @else N/A @endif</div>
                            <div class="text-xs mb-1"><strong>Series:</strong> @if(!empty($review['series_names'])) {{ implode(', ', $review['series_names']) }} @else N/A @endif</div>
                            <div class="text-xs text-gray-100 mb-2 line-clamp-4">{!! \Illuminate\Support\Str::limit(strip_tags($review['content']['rendered']), 200) !!}</div>
                            <a href="{{ $review['link'] }}" class="text-blue-600 hover:underline" target="_blank" rel="noopener">Read full review</a>
                        </div>
                    </div>
                    <!-- Click/Touch to flip -->
                    <button @click="flipped = !flipped" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" aria-label="Flip card"></button>
                </div>
                @endforeach
            </div>
        </div>
    </main>
    <div class="w-full flex justify-center mt-8 mb-4">
        @if(isset($currentPage) && isset($totalPages) && $totalPages > 1)
            <nav class="flex space-x-4">
                @if($currentPage > 1)
                    <a href="?page={{ $currentPage - 1 }}" class="px-4 py-2 bg-neutral-700 text-white rounded hover:bg-neutral-600">&laquo; Previous</a>
                @endif
                <span class="px-4 py-2 text-gray-300">Page {{ $currentPage }} of {{ $totalPages }}</span>
                @if($currentPage < $totalPages)
                    <a href="?page={{ $currentPage + 1 }}" class="px-4 py-2 bg-neutral-700 text-white rounded hover:bg-neutral-600">Next &raquo;</a>
                @endif
            </nav>
        @endif
    </div>
    <footer class="w-full text-center text-gray-300 py-4 text-sm">
        &copy; 2025
    </footer>
</body>
