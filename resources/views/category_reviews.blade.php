@extends('layouts.app')

@section('content')
    <div class="mt-5 font-sans font-bold text-3xl sm:text-4xl md:text-5xl text-center w-4/5 md:w-auto mx-auto">
        {{ $term['name'] ?? '' }}
    </div>
    @if(!empty($term['description']))
        <div class="mt-4 mb-8 max-w-2xl mx-auto text-lg text-gray-200 text-center">
            {!! nl2br(e($term['description'])) !!}
        </div>
    @endif
    <div class="mx-auto mt-6 w-full max-w-6xl">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 w-full max-w-6xl mx-auto">
            @forelse($reviews as $review)
                <div class="bg-neutral-800/80 rounded-lg shadow-lg p-4 flex flex-col">
                    <div class="text-base font-bold mb-2 text-center">{!! html_entity_decode($review['title']['rendered']) !!}</div>
                    <div class="text-xs text-gray-100 mb-2 line-clamp-4">{!! \Illuminate\Support\Str::limit(strip_tags($review['content']['rendered']), 200) !!}</div>
                    <a href="{{ $review['link'] }}" class="text-gray-100 hover:text-white hover:underline text-center transition-colors" target="_blank" rel="noopener">Read full review</a>
                </div>
            @empty
                <div class="col-span-3 text-gray-400 text-center">No reviews found for this category.</div>
            @endforelse
        </div>
    </div>
    <div class="w-full flex justify-center mt-8 mb-4">
        @if(isset($currentPage) && isset($totalPages) && $totalPages > 1)
            <nav class="flex items-center space-x-2" x-data="{ page: {{ $currentPage }}, total: {{ $totalPages }} }">
                <a href="?page=1" class="px-3 py-2 bg-neutral-700 text-white rounded hover:bg-neutral-600" :class="{ 'opacity-50 pointer-events-none': page === 1 }">&laquo;&laquo;</a>
                <a href="?page={{ $currentPage - 1 }}" class="px-3 py-2 bg-neutral-700 text-white rounded hover:bg-neutral-600" :class="{ 'opacity-50 pointer-events-none': page === 1 }">&laquo;</a>
                <span class="px-2 text-gray-300">Page</span>
                <form @submit.prevent="if(page >= 1 && page <= total) { window.location.search = '?page=' + page }" class="inline-block">
                    <input type="number" min="1" :max="total" x-model.number="page" class="w-16 px-2 py-1 rounded bg-neutral-900 text-white border border-neutral-700 text-center focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </form>
                <span class="px-2 text-gray-300">of {{ $totalPages }}</span>
                <a href="?page={{ $currentPage + 1 }}" class="px-3 py-2 bg-neutral-700 text-white rounded hover:bg-neutral-600" :class="{ 'opacity-50 pointer-events-none': page === total }">&raquo;</a>
                <a href="?page={{ $totalPages }}" class="px-3 py-2 bg-neutral-700 text-white rounded hover:bg-neutral-600" :class="{ 'opacity-50 pointer-events-none': page === total }">&raquo;&raquo;</a>
            </nav>
        @endif
    </div>
@endsection
