@extends('layouts.app')

@section('content')
<div class="mx-auto mt-10 w-full max-w-3xl bg-neutral-800/80 rounded-lg shadow-lg p-8">
    <div class="flex flex-col md:flex-row gap-8 items-start">
        @if(!empty($review['cover_url']))
            <img src="{{ $review['cover_url'] }}" alt="Book cover for {{ $review['title']['rendered'] ?? '' }}" class="w-48 h-auto rounded shadow-lg mb-4 md:mb-0" loading="lazy">
        @endif
        <div class="flex-1">
            <h1 class="text-3xl font-bold text-white mb-2">{!! html_entity_decode($review['title']['rendered'] ?? '') !!}</h1>
            <div class="text-lg text-gray-300 mb-4">
                by
                @if(!empty($review['novel_author_names']))
                    @foreach($review['novel_author_names'] as $i => $author)
                        <a href="{{ url('/reviews/authors/' . Str::slug($author)) }}" class="text-blue-300 hover:underline">{{ $author }}</a>@if($i < count($review['novel_author_names']) - 1), @endif
                    @endforeach
                @else
                    Unknown Author
                @endif
            </div>


            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-2 text-base text-gray-200 mb-4">
                <div><strong>Publisher:</strong>
                    @if(!empty($review['publisher_names']))
                        @foreach($review['publisher_names'] as $i => $publisher)
                            <a href="{{ url('/reviews/publishers/' . Str::slug($publisher)) }}" class="text-blue-300 hover:underline">{{ $publisher }}</a>@if($i < count($review['publisher_names']) - 1), @endif
                        @endforeach
                    @else
                        N/A
                    @endif
                </div>
                <div><strong>Format:</strong> {{ $review['book_format'] ?? $review['scraped_format'] ?? 'N/A' }}</div>
                <div><strong>Genre:</strong>
                    @if(!empty($review['genre_names']))
                        @foreach($review['genre_names'] as $i => $genre)
                            <a href="{{ url('/reviews/genres/' . Str::slug($genre)) }}" class="text-blue-300 hover:underline">{{ $genre }}</a>@if($i < count($review['genre_names']) - 1), @endif
                        @endforeach
                    @else
                        N/A
                    @endif
                </div>
                <div><strong>Series:</strong>
                    @if(!empty($review['series_names']))
                        @foreach($review['series_names'] as $i => $series)
                            <a href="{{ url('/reviews/series/' . Str::slug($series)) }}" class="text-blue-300 hover:underline">{{ $series }}</a>@if($i < count($review['series_names']) - 1), @endif
                        @endforeach
                    @else
                        N/A
                    @endif
                </div>
                <div><strong>Series Number:</strong> {{ $review['series_number'] ?? $review['scraped_series_number'] ?? 'N/A' }}</div>
                <div><strong>Published:</strong> {{ $review['publication_date'] ?? $review['scraped_publication_date'] ?? 'N/A' }}</div>
                <div><strong>ISBN:</strong> {{ $review['isbn'] ?? $review['scraped_isbn'] ?? 'N/A' }}</div>
                <div><strong>Book URL:</strong>
                    @if(!empty($review['book_url']))
                        <a href="{{ $review['book_url'] }}" class="text-blue-300 hover:underline" target="_blank" rel="noopener">Goodreads</a>
                    @elseif(!empty($review['scraped_book_url']))
                        <a href="{{ $review['scraped_book_url'] }}" class="text-blue-300 hover:underline" target="_blank" rel="noopener">Goodreads</a>
                    @else
                        N/A
                    @endif
                </div>
                <div><strong>Goodreads Rating:</strong> {{ $review['goodreads_rating'] ?? $review['scraped_goodreads_rating'] ?? 'N/A' }}</div>
                <div><strong>Date Read:</strong> {{ $review['date_read'] ?? $review['scraped_date_read'] ?? 'N/A' }}</div>
                <div><strong>Page Count:</strong> {{ $review['page_count'] ?? $review['scraped_page_count'] ?? 'N/A' }}</div>
            </div>

            {{-- Display scraped purchase links if available --}}
            @if(!empty($review['purchase_links_scraped']) && is_array($review['purchase_links_scraped']))
                <div class="mt-4">
                    <strong>Purchase on:</strong>
                    @foreach($review['purchase_links_scraped'] as $link)
                        <a href="{{ $link['url'] }}" class="ml-2 text-blue-300 hover:text-blue-400 hover:underline" target="_blank" rel="noopener">{{ $link['label'] }}</a>
                    @endforeach
                </div>
            @endif

            {{-- Display API purchase links if available --}}
            @if(!empty($review['purchase_links']) && is_array($review['purchase_links']))
                <div class="mt-2">
                    <strong>API Purchase Links:</strong>
                    @foreach($review['purchase_links'] as $store => $url)
                        @if(!empty($url))
                            <a href="{{ $url }}" class="ml-2 text-blue-300 hover:text-blue-400 hover:underline" target="_blank" rel="noopener">{{ $store }}</a>
                        @endif
                    @endforeach
                </div>
            @endif


            <!-- Display scraped scorecard if available -->

            @if(!empty($review['overall_score']))
                <div class="mt-8 bg-neutral-900/80 rounded-lg shadow p-6">
                    <div class="flex flex-col items-center mb-4">
                        <div class="flex items-center gap-2">
                            <span class="text-4xl font-extrabold text-gray-100">{{ $review['overall_score'] }}</span>
                            <span class="flex items-center" title="Overall Rating">
                                @php
                                    $score = floatval($review['overall_score']);
                                    $maxStars = 5;
                                @endphp
                                @for($i = 0; $i < $maxStars; $i++)
                                    @php
                                        $starValue = min(max($score - $i, 0), 1); // value for this star (0 to 1)
                                        $percent = round($starValue * 100);
                                    @endphp
                                    @if($percent == 100)
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="#FFD700" viewBox="0 0 24 24" width="28" height="28" style="vertical-align:middle;"><path d="M12 17.75l-6.172 3.245 1.179-6.881-5-4.873 6.9-1.002L12 2.25l3.093 6.989 6.9 1.002-5 4.873 1.179 6.881z"/></svg>
                                    @elseif($percent == 0)
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="#222" viewBox="0 0 24 24" width="28" height="28" style="vertical-align:middle;"><path d="M12 17.75l-6.172 3.245 1.179-6.881-5-4.873 6.9-1.002L12 2.25l3.093 6.989 6.9 1.002-5 4.873 1.179 6.881z"/></svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="28" style="vertical-align:middle;">
                                            <defs>
                                                <linearGradient id="star-grad-{{ $i }}">
                                                    <stop offset="{{ $percent }}%" stop-color="#FFD700"/>
                                                    <stop offset="{{ $percent }}%" stop-color="#222"/>
                                                </linearGradient>
                                            </defs>
                                            <path fill="url(#star-grad-{{ $i }})" d="M12 17.75l-6.172 3.245 1.179-6.881-5-4.873 6.9-1.002L12 2.25l3.093 6.989 6.9 1.002-5 4.873 1.179 6.881z"/>
                                        </svg>
                                    @endif
                                @endfor
                            </span>
                        </div>
                        <div class="text-lg font-semibold text-gray-300">Overall Score</div>
                        <div class="text-xl font-bold text-white mt-2">{!! html_entity_decode($review['title']['rendered'] ?? '') !!}</div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                        <div class="flex flex-col items-center">
                            <div class="text-2xl font-bold text-gray-100">{{ $review['plot_score'] ?? 'N/A' }}</div>
                            <div class="text-base text-gray-300">Plot</div>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="text-2xl font-bold text-gray-100">{{ $review['characters_score'] ?? 'N/A' }}</div>
                            <div class="text-base text-gray-300">Characters</div>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="text-2xl font-bold text-gray-100">{{ $review['pacing_score'] ?? 'N/A' }}</div>
                            <div class="text-base text-gray-300">Pacing</div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="prose prose-invert max-w-none text-gray-100 mt-6">
                {!! $review['content']['rendered'] ?? '' !!}
            </div>
        </div>
    </div>
</div>
@endsection
