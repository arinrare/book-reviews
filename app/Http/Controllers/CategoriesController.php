<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class CategoriesController extends Controller
{
    public function index(Request $request)
    {
        $title = "Categories";
        $page = max(1, (int) $request->query('page', 1));
        $cacheTime = config('bookreviews.cache_time', 300); // default 5 min if not set
        $cacheKey = 'categories_page_' . $page;
        $apiBase = config('bookreviews.landing_url') . '/bookreviews/wp-json/wp/v2/rcno/';


        $authors = Cache::remember($cacheKey, $cacheTime, function () use ($apiBase) {
            $resp = Http::get($apiBase . 'authors');
            return $resp->ok() ? $resp->json() : [];
        });

        $publishers = Cache::remember('categories_publishers', $cacheTime, function () use ($apiBase) {
            $resp = Http::get($apiBase . 'publishers');
            return $resp->ok() ? $resp->json() : [];
        });

        $series = Cache::remember('categories_series', $cacheTime, function () use ($apiBase) {
            $resp = Http::get($apiBase . 'series');
            return $resp->ok() ? $resp->json() : [];
        });

        $genres = Cache::remember('categories_genres', $cacheTime, function () use ($apiBase) {
            $resp = Http::get($apiBase . 'genres');
            return $resp->ok() ? $resp->json() : [];
        });

        return view('categories_index', [
            'title' => $title,
            'authors' => $authors,
            'publishers' => $publishers,
            'series' => $series,
            'genres' => $genres,
        ]);
    }
}
