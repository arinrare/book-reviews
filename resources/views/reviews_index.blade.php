@vite('resources/css/app.css')

<body class="text-white min-h-screen w-full bg-cover bg-center bg-no-repeat flex flex-col" style="background-image: url('/images/fantastic_library.jpg')">
    <x-navbar />
    <main class="flex-1 flex flex-col items-center w-full">
        <div class="mt-5 font-sans font-bold text-3xl sm:text-4xl md:text-6xl lg:text-7xl text-center w-4/5 md:w-auto mx-auto">{{ $title }}</div>
        <div class="w-4/5 md:w-auto mx-auto mt-10">
           @foreach($reviews as $review)
                <div class="mb-8 p-6 bg-neutral-800/80 rounded-lg shadow-lg">
                    @if(!empty($review['cover_url']))
                        <img src="{{ $review['cover_url'] }}" alt="Book cover for {{ $review['title']['rendered'] }}" class="w-32 h-auto mb-4 rounded shadow-md mx-auto" loading="lazy">
                    @endif
                    <h2 class="text-2xl font-bold mb-2">{!! html_entity_decode($review['title']['rendered']) !!}</h2>
                    <div class="flex flex-wrap text-sm text-gray-300 mb-2 gap-x-4 gap-y-1">
                        <span>Published: {{ \Carbon\Carbon::parse($review['date'])->format('F j, Y') }}</span>
                    </div>
                    <div class="flex flex-wrap text-xs text-gray-400 mb-2 gap-x-4 gap-y-1">
                        <span>Authors: @if(!empty($review['novel_author_names'])) {{ implode(', ', $review['novel_author_names']) }} @else N/A @endif</span>
                        <span>Genres: @if(!empty($review['genre_names'])) {{ implode(', ', $review['genre_names']) }} @else N/A @endif</span>
                        <span>Series: @if(!empty($review['series_names'])) {{ implode(', ', $review['series_names']) }} @else N/A @endif</span>
                        <span>Publishers: @if(!empty($review['publisher_names'])) {{ implode(', ', $review['publisher_names']) }} @else N/A @endif</span>
                    </div>
                    <div class="prose prose-invert max-w-none mb-4">{!! $review['content']['rendered'] !!}</div>
                    <a href="{{ $review['link'] }}" class="text-blue-300 underline" target="_blank" rel="noopener">View on Site</a>
                </div>
            @endforeach
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
