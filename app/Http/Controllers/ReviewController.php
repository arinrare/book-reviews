<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ReviewController extends Controller
{
	public function index($slug)
	{
		$cacheTime = config('bookreviews.cache_time', 300);
		$apiBase = config('bookreviews.landing_url') . '/bookreviews-wordpress/wp-json/wp/v2/rcno/reviews';
		$cacheKey = 'review_' . $slug;

		$review = Cache::remember($cacheKey, $cacheTime, function () use ($apiBase, $slug) {
			$resp = Http::get($apiBase, [
				'slug' => $slug,
				'per_page' => 1
			]);
			$data = $resp->ok() ? $resp->json() : [];
			return !empty($data) ? $data[0] : null;
		});

		if (!$review) {
			abort(404, 'Review not found');
		}

		// Helper to fetch taxonomy names by ID
		$apiBase = config('bookreviews.landing_url') . '/bookreviews-wordpress/wp-json/wp/v2/rcno/';
		$getNames = function($type, $ids) use ($apiBase) {
			if (empty($ids)) return [];
			$names = [];
			foreach ($ids as $id) {
				$resp = Http::get($apiBase . $type . '/' . $id);
				if ($resp->ok()) {
					$data = $resp->json();
					if (!empty($data['name'])) $names[] = $data['name'];
				}
			}
			return $names;
		};

		// Authors
		$review['novel_author_names'] = $getNames('authors', $review['rcno/authors'] ?? []);
		// Genres
		$review['genre_names'] = $getNames('genres', $review['rcno/genres'] ?? []);
		// Series
		$review['series_names'] = $getNames('series', $review['rcno/series'] ?? []);
		// Publishers
		$review['publisher_names'] = $getNames('publishers', $review['rcno/publishers'] ?? []);

		// Cover image (try featured_media, fallback to attachment, then fallback to first book in series)
		$review['cover_url'] = null;
		if (!empty($review['featured_media'])) {
			$mediaResp = Http::get(config('bookreviews.landing_url') . '/bookreviews-wordpress/wp-json/wp/v2/media/' . $review['featured_media']);
			if ($mediaResp->ok()) {
				$media = $mediaResp->json();
				$review['cover_url'] = $media['source_url'] ?? null;
			}
		}
		// Fallback: try attachments
		if (empty($review['cover_url'])) {
			$attachResp = Http::get(config('bookreviews.landing_url') . '/bookreviews-wordpress/wp-json/wp/v2/media', ['parent' => $review['id']]);
			if ($attachResp->ok()) {
				$attachments = $attachResp->json();
				if (!empty($attachments[0]['source_url'])) {
					$review['cover_url'] = $attachments[0]['source_url'];
				}
			}
		}
		// Fallback: if still no cover, try to get from the first book in the same series
		if (empty($review['cover_url']) && !empty($review['series_names']) && isset($review['rcno/series'][0])) {
			$seriesId = $review['rcno/series'][0];
			$seriesReviewsResp = Http::get(config('bookreviews.landing_url') . '/bookreviews-wordpress/wp-json/wp/v2/rcno/reviews', [
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
					$review['cover_url'] = $firstSeriesReview['_embedded']['wp:featuredmedia'][0]['source_url'];
				} else {
					// Try first attachment
					$mediaResp = Http::get(config('bookreviews.landing_url') . '/bookreviews-wordpress/wp-json/wp/v2/media', [
						'parent' => $firstSeriesReview['id'],
						'per_page' => 1
					]);
					if ($mediaResp->ok() && is_array($mediaResp->json()) && count($mediaResp->json()) > 0) {
						$media = $mediaResp->json()[0];
						if (isset($media['source_url'])) {
							$review['cover_url'] = $media['source_url'];
						}
					}
				}
			}
		}

		// Other fields from meta if present
		$fields = [
			'book_format', 'series_number', 'publication_date', 'isbn', 'book_url', 'goodreads_rating',
			'date_read', 'page_count', 'content'
		];
		foreach ($fields as $field) {
			if (!isset($review[$field])) {
				$review[$field] = $review['meta'][$field] ?? null;
			}
		}

		// Scrape score results and missing book details from review page if not present in API
		$reviewUrl = config('bookreviews.landing_url') . '/bookreviews-wordpress/review/' . $slug . '/';
		$htmlResp = Http::get($reviewUrl);
		if ($htmlResp->ok()) {
			$html = $htmlResp->body();
			// Extract overall score
			if (!isset($review['overall_score']) && preg_match('/<span class="overall">([\d\.]+)<\/span>/i', $html, $m)) {
				$review['overall_score'] = floatval($m[1]);
			}
			// Extract category scores
			$categories = ['Plot', 'Characters', 'Pacing'];
			foreach ($categories as $cat) {
				$pattern = '/<span class="score-bar">' . preg_quote($cat, '/') . '<\/span><\/div><span class="right">([\d\.]+)<\/span>/i';
				if (!isset($review[strtolower($cat) . '_score']) && preg_match($pattern, $html, $m)) {
					$review[strtolower($cat) . '_score'] = floatval($m[1]);
				}
			}
			// Extract purchase links
			if (empty($review['purchase_links_scraped'])) {
				$review['purchase_links_scraped'] = [];
				if (preg_match('/<div class="rcno-purchase-links-container">(.*?)<\/div>/is', $html, $m)) {
					$linksHtml = $m[1];
					preg_match_all('/<a[^>]+href="([^"]+)"[^>]*class="rcno-purchase-links[^"]*"[^>]*>([^<]+)<\/a>/i', $linksHtml, $matches, PREG_SET_ORDER);
					foreach ($matches as $match) {
						$review['purchase_links_scraped'][] = [
							'url' => $match[1],
							'label' => trim($match[2])
						];
					}
				}
			}

			// Extract book details from .rcno-full-book-details section
			$details = [
				'scraped_author' => '/<span class="rcno-tax-name">Author:\s*<\/span><span class="rcno-tax-term">(?:<a[^>]+>)?([^<]+)(?:<\/a>)?<\/span>/i',
				'scraped_title' => '/<div class="rcno_book_title">.*?<span class="rcno-meta-value">([^<]+)<\/span>/is',
				'scraped_publisher' => '/<span class="rcno-tax-name">Publisher:\s*<\/span><span class="rcno-tax-term">(?:<a[^>]+>)?([^<]+)(?:<\/a>)?<\/span>/i',
				'scraped_format' => '/<div class="rcno_book_pub_format">.*?<span class="rcno-meta-value">([^<]+)<\/span>/is',
				'scraped_genre' => '/<span class="rcno-tax-name">Genre:\s*<\/span><span class="rcno-tax-term">(?:<a[^>]+>)?([^<]+)(?:<\/a>)?<\/span>/i',
				'scraped_series' => '/<span class="rcno-tax-name">Series:\s*<\/span><span class="rcno-tax-term">(?:<a[^>]+>)?([^<]+)(?:<\/a>)?<\/span>/i',
				'scraped_series_number' => '/<div class="rcno_book_series_number">.*?<span class="rcno-meta-value">([^<]+)<\/span>/is',
				'scraped_publication_date' => '/<div class="rcno_book_pub_date">.*?<span class="rcno-meta-value">([^<]+)<\/span>/is',
				'scraped_isbn' => '/<div class="rcno_book_isbn">.*?<span class="rcno-meta-value">([^<]+)<\/span>/is',
				'scraped_book_url' => '/<div class="rcno_book_gr_url">.*?<a[^>]+href="([^"]+)"[^>]*>GoodReads\.com<\/a>/is',
				'scraped_goodreads_rating' => '/<div class="rcno_book_gr_review">.*?<span class="rcno-meta-value">([^<]+)<\/span>/is',
				'scraped_date_read' => '/<div class="rcno_dateread_meta">.*?<span class="rcno-meta-value">([^<]+)<\/span>/is',
				'scraped_page_count' => '/<div class="rcno_book_page_count">.*?<span class="rcno-meta-value">([^<]+)<\/span>/is',
			];
			foreach ($details as $key => $pattern) {
				if (empty($review[$key]) && preg_match($pattern, $html, $m)) {
					$review[$key] = trim($m[1]);
				}
			}
		}

		return view('review_index', [
			'review' => $review
		]);
	}
}
