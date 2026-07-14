<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Public routes
Route::get('/about', [App\Http\Controllers\AboutController::class, 'index'])->name('about');
Route::get('/tutorial', [App\Http\Controllers\TutorialController::class, 'index'])->name('tutorial');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    // Food spots (with filter support via GET params)
    Route::get('/food-spots', [App\Http\Controllers\FoodSpotController::class, 'index'])->name('food-spots.index');
    Route::post('/food-spots', [App\Http\Controllers\FoodSpotController::class, 'store'])->name('food-spots.store');

    // Point & Redeem
    Route::get('/poin', [App\Http\Controllers\PointController::class, 'index'])->name('poin.index');
    Route::post('/poin/redeem', [App\Http\Controllers\PointController::class, 'redeem'])->name('poin.redeem');
});
