<?php


namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ReviewsController extends Controller
{
    public function index(Request $request)
    {
        $title = "Book Reviews";
        $page = max(1, (int) $request->query('page', 1));
        $perPage = 10;
        $cacheKey = 'reviews_page_' . $page;
        $reviews = Cache::remember($cacheKey, 300, function () use ($page, $perPage) {
            \Log::info('Fetching reviews from API...');
            $response = Http::get('https://michaelbaggott.site/bookreviews/wp-json/wp/v2/rcno/reviews', [
                'per_page' => $perPage,
                'page' => $page,
                '_embed' => 1,
            ]);
            $reviews = $response->json();
            if (!is_array($reviews)) {
                $reviews = [];
            }

            // Extract embedded novel author, genres, series, publisher names, and cover image
            foreach ($reviews as &$review) {
                // Novel author(s) from _embedded
                if (isset($review['_embedded']['wp:term'])) {
                    foreach ($review['_embedded']['wp:term'] as $termGroup) {
                        foreach ($termGroup as $term) {
                            if (isset($term['taxonomy']) && $term['taxonomy'] === 'rcno_author') {
                                $novelAuthors[] = $term['name'];
                            }
                        }
                    }
                }
                $review['novel_author_names'] = $novelAuthors;

                // Genres from _embedded
                $genreNames = [];
                if (isset($review['_embedded']['wp:term'])) {
                    foreach ($review['_embedded']['wp:term'] as $termGroup) {
                        foreach ($termGroup as $term) {
                            if (isset($term['taxonomy']) && $term['taxonomy'] === 'rcno_genre') {
                                $genreNames[] = $term['name'];
                            }
                        }
                    }
                }
                $review['genre_names'] = $genreNames;

                // Series from _embedded
                $seriesNames = [];
                $publisherNames = [];
                if (isset($review['_embedded']['wp:term'])) {
                    foreach ($review['_embedded']['wp:term'] as $termGroup) {
                        foreach ($termGroup as $term) {
                            if (isset($term['taxonomy'])) {
                                if ($term['taxonomy'] === 'rcno_series') {
                                    $seriesNames[] = $term['name'];
                                }
                                if ($term['taxonomy'] === 'rcno_publisher') {
                                    $publisherNames[] = $term['name'];
                                }
                            }
                        }
                    }
                }
                $review['series_names'] = $seriesNames;
                $review['publisher_names'] = $publisherNames;

                // Book cover (featured image) from _embedded, or fallback to first attachment
                $coverUrl = null;
                if (isset($review['_embedded']['wp:featuredmedia'][0]['source_url'])) {
                    $coverUrl = $review['_embedded']['wp:featuredmedia'][0]['source_url'];
                }
                // Fallback: fetch first attachment if no featured image
                if (!$coverUrl && isset($review['id'])) {
                    $mediaCacheKey = 'review_' . $review['id'] . '_first_attachment';
                    $coverUrl = Cache::remember($mediaCacheKey, 300, function () use ($review) {
                        $mediaResp = Http::get('https://michaelbaggott.site/bookreviews/wp-json/wp/v2/media', [
                            'parent' => $review['id'],
                            'per_page' => 1
                        ]);
                        if ($mediaResp->ok() && is_array($mediaResp->json()) && count($mediaResp->json()) > 0) {
                            $media = $mediaResp->json()[0];
                            if (isset($media['source_url'])) {
                                return $media['source_url'];
                            }
                        }
                        return null;
                    });
                }

                // Fallback: if still no cover, try to get from the first book in the same series
                if (!$coverUrl && !empty($review['series_names']) && isset($review['rcno/series'][0])) {
                    $seriesId = $review['rcno/series'][0];
                    $seriesCacheKey = 'series_' . $seriesId . '_first_book_cover';
                    $coverUrl = Cache::remember($seriesCacheKey, 300, function () use ($seriesId, $review) {
                        $seriesReviewsResp = Http::get('https://michaelbaggott.site/bookreviews/wp-json/wp/v2/rcno/reviews', [
                            'rcno/series' => $seriesId,
                            'per_page' => 1,
                            'orderby' => 'date',
                            'order' => 'asc',
                            '_embed' => 1
                        ]);
                        if ($seriesReviewsResp->ok() && is_array($seriesReviewsResp->json()) && count($seriesReviewsResp->json()) > 0) {
                            $firstSeriesReview = $seriesReviewsResp->json()[0];
                            // Try featured image
                            if (isset($firstSeriesReview['_embedded']['wp:featuredmedia'][0]['source_url'])) {
                                return $firstSeriesReview['_embedded']['wp:featuredmedia'][0]['source_url'];
                            } else {
                                // Try first attachment
                                $mediaResp = Http::get('https://michaelbaggott.site/bookreviews/wp-json/wp/v2/media', [
                                    'parent' => $firstSeriesReview['id'],
                                    'per_page' => 1
                                ]);
                                if ($mediaResp->ok() && is_array($mediaResp->json()) && count($mediaResp->json()) > 0) {
                                    $media = $mediaResp->json()[0];
                                    if (isset($media['source_url'])) {
                                        return $media['source_url'];
                                    }
                                }
                            }
                        }
                        return null;
                    });
                }
                $review['cover_url'] = $coverUrl;
            }
            unset($review);
            return $reviews;
        });

        $countCacheKey = 'reviews_total_count';
        $totalReviews = Cache::remember($countCacheKey, 60, function () {
            $resp = Http::get('https://michaelbaggott.site/bookreviews/wp-json/wp/v2/rcno/reviews', [
                'per_page' => 1,
                'page' => 1,
            ]);
            return $resp->header('X-WP-Total') ?? 0;
        });

        $totalPages = $totalReviews > 0 ? ceil($totalReviews / $perPage) : 1;

        return view('reviews_index', [
            'title' => $title,
            'reviews' => $reviews,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ]);
    }
}
