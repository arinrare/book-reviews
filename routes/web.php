<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryReviewsController;

Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);
Route::get('/reviews', [App\Http\Controllers\ReviewsController::class, 'index']);
Route::get('/categories', [App\Http\Controllers\CategoriesController::class, 'index']);
Route::get('/reviews/{type}/{slug}', [CategoryReviewsController::class, 'show'])
	->where('type', 'authors|genres|series|publishers')
	->name('category.reviews');