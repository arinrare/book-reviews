
@extends('layouts.app')

@section('content')
<div class="mt-5 font-sans font-bold text-3xl sm:text-4xl md:text-6xl lg:text-7xl text-center w-4/5 md:w-auto mx-auto">Categories</div>

<div class="pl-20 pr-20 mx-auto mt-10 w-full max-w-4xl flex flex-col gap-10">
    <!-- Authors -->
    <div class="bg-neutral-800/80 rounded-lg shadow-lg p-6">
        <h2 class="text-xl sm:text-2xl font-bold mb-2 text-left text-white border-b border-neutral-700 pb-1">Authors</h2>
    <div class="flex flex-wrap justify-center gap-x-6 gap-y-2 mt-2">
            @forelse($authors as $author)
                <a href="{{ url('/reviews/authors/' . ($author['slug'] ?? '')) }}" class="text-base sm:text-lg text-gray-100 hover:text-white font-medium transition-all no-underline hover:underline hover:decoration-white hover:underline-offset-2 focus:underline focus:decoration-white focus:underline-offset-2 active:underline active:decoration-white active:underline-offset-2">
                    {{ $author['name'] ?? '' }}
                </a>
            @empty
                <span class="text-gray-400">No authors found.</span>
            @endforelse
        </div>
    </div>

    <!-- Genres -->
    <div class="bg-neutral-800/80 rounded-lg shadow-lg p-6">
        <h2 class="text-xl sm:text-2xl font-bold mb-2 text-left text-white border-b border-neutral-700 pb-1">Genres</h2>
    <div class="flex flex-wrap justify-center gap-x-6 gap-y-2 mt-2">
            @forelse($genres as $genre)
                <a href="{{ url('/reviews/genres/' . ($genre['slug'] ?? '')) }}" class="text-base sm:text-lg text-gray-100 hover:text-white font-medium transition-all no-underline hover:underline hover:decoration-white hover:underline-offset-2 focus:underline focus:decoration-white focus:underline-offset-2 active:underline active:decoration-white active:underline-offset-2">
                    {{ $genre['name'] ?? '' }}
                </a>
            @empty
                <span class="text-gray-400">No genres found.</span>
            @endforelse
        </div>
    </div>

    <!-- Publishers -->
    <div class="bg-neutral-800/80 rounded-lg shadow-lg p-6">
        <h2 class="text-xl sm:text-2xl font-bold mb-2 text-left text-white border-b border-neutral-700 pb-1">Publishers</h2>
    <div class="flex flex-wrap justify-center gap-x-6 gap-y-2 mt-2">
            @forelse($publishers as $publisher)
                <a href="{{ url('/reviews/publishers/' . ($publisher['slug'] ?? '')) }}" class="text-base sm:text-lg text-gray-100 hover:text-white font-medium transition-all no-underline hover:underline hover:decoration-white hover:underline-offset-2 focus:underline focus:decoration-white focus:underline-offset-2 active:underline active:decoration-white active:underline-offset-2">
                    {{ $publisher['name'] ?? '' }}
                </a>
            @empty
                <span class="text-gray-400">No publishers found.</span>
            @endforelse
        </div>
    </div>

    <!-- Series -->
    <div class="bg-neutral-800/80 rounded-lg shadow-lg p-6">
        <h2 class="text-xl sm:text-2xl font-bold mb-2 text-left text-white border-b border-neutral-700 pb-1">Series</h2>
    <div class="flex flex-wrap justify-center gap-x-6 gap-y-2 mt-2">
            @forelse($series as $s)
                <a href="{{ url('/reviews/series/' . ($s['slug'] ?? '')) }}" class="text-base sm:text-lg text-gray-100 hover:text-white font-medium transition-all no-underline hover:underline hover:decoration-white hover:underline-offset-2 focus:underline focus:decoration-white focus:underline-offset-2 active:underline active:decoration-white active:underline-offset-2">
                    {{ $s['name'] ?? '' }}
                </a>
            @empty
                <span class="text-gray-400">No series found.</span>
            @endforelse
        </div>
    </div>
</div>
@endsection