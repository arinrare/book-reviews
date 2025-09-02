<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CategoryReviewsController extends Controller
{
    public function show($type, $slug, Request $request)
    {
        $perPage = 10;
        $page = max(1, (int) $request->query('page', 1));
        $cacheTime = config('bookreviews.cache_time', 300);
        $apiBase = rtrim(config('bookreviews.landing_url'), '/') . '/bookreviews/wp-json/wp/v2/';


        // Map type to endpoints and query params
        $endpointMap = [
            'authors' => [
                'term_endpoint' => 'rcno/authors',
                'review_param' => 'rcno/authors',
            ],
            'genres' => [
                'term_endpoint' => 'rcno/genres',
                'review_param' => 'rcno/genres',
            ],
            'series' => [
                'term_endpoint' => 'rcno/series',
                'review_param' => 'rcno/series',
            ],
            'publishers' => [
                'term_endpoint' => 'rcno/publishers',
                'review_param' => 'rcno/publishers',
            ],
        ];
        if (!isset($endpointMap[$type])) {
            abort(404);
        }
        $termEndpoint = $endpointMap[$type]['term_endpoint'];
        $reviewParam = $endpointMap[$type]['review_param'];

        // Get category term info
        $termCacheKey = "category_term_{$termEndpoint}_{$slug}";
        $term = Cache::remember($termCacheKey, $cacheTime, function () use ($apiBase, $termEndpoint, $slug) {
            $resp = Http::get($apiBase . $termEndpoint, ['slug' => $slug]);
            if ($resp->ok() && is_array($resp->json()) && count($resp->json()) > 0) {
                return $resp->json()[0];
            }
            return null;
        });
        if (!$term) {
            abort(404);
        }

        // Get reviews for this category
        $reviewsCacheKey = "category_reviews_{$termEndpoint}_{$slug}_page_{$page}";
        $reviews = Cache::remember($reviewsCacheKey, $cacheTime, function () use ($apiBase, $reviewParam, $term, $perPage, $page) {
            $resp = Http::get($apiBase . 'rcno/reviews', [
                $reviewParam => $term['id'],
                'per_page' => $perPage,
                'page' => $page,
                '_embed' => 1,
            ]);
            return $resp->ok() ? $resp->json() : [];
        });

        // Get total count for pagination
        $countCacheKey = "category_reviews_count_{$termEndpoint}_{$slug}";
        $totalReviews = Cache::remember($countCacheKey, 60, function () use ($apiBase, $reviewParam, $term) {
            $resp = Http::get($apiBase . 'rcno/reviews', [
                $reviewParam => $term['id'],
                'per_page' => 1,
                'page' => 1,
            ]);
            return $resp->header('X-WP-Total') ?? 0;
        });
        $totalPages = $totalReviews > 0 ? ceil($totalReviews / $perPage) : 1;

        // Pass to view
        return view('category_reviews', [
            'type' => $type,
            'term' => $term,
            'reviews' => $reviews,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ]);
    }
}
