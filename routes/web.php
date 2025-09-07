<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryReviewsController;

Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);
Route::get('/reviews', [App\Http\Controllers\ReviewsController::class, 'index']);
Route::get('/categories', [App\Http\Controllers\CategoriesController::class, 'index']);
Route::get('/review/{slug}', [App\Http\Controllers\ReviewController::class, 'index'])->name('review.show');
Route::get('/reviews/{type}/{slug}', [CategoryReviewsController::class, 'index'])
	->where('type', 'authors|genres|series|publishers')
	->name('category.reviews');