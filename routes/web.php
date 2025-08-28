<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);
Route::get('/reviews', [App\Http\Controllers\ReviewsController::class, 'index']);
