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
        $apiBase = config('bookreviews.landing_url') . '/bookreviews-wordpress/wp-json/wp/v2/rcno/';


        $fetchAll = function($endpoint) use ($apiBase) {
            $all = [];
            $page = 1;
            do {
                $resp = Http::get($apiBase . $endpoint, ['per_page' => 100, 'page' => $page]);
                $data = $resp->ok() ? $resp->json() : [];
                $all = array_merge($all, $data);
                $page++;
            } while (is_array($data) && count($data) === 100);
            return $all;
        };

        $authors = Cache::remember($cacheKey, $cacheTime, function () use ($fetchAll) {
            return $fetchAll('authors');
        });

        $publishers = Cache::remember('categories_publishers', $cacheTime, function () use ($fetchAll) {
            return $fetchAll('publishers');
        });

        $series = Cache::remember('categories_series', $cacheTime, function () use ($fetchAll) {
            return $fetchAll('series');
        });

        $genres = Cache::remember('categories_genres', $cacheTime, function () use ($fetchAll) {
            return $fetchAll('genres');
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
