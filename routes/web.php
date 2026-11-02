<?php

use App\Http\Controllers\Auth\WebLoginController;
use App\Http\Controllers\Customer\FavoriteController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Customer\RestaurantController;
use App\Http\Controllers\Customer\RestaurantReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('customer.restaurants', ['login' => 1]);
});

Route::get('/login', function () {
    return redirect()->route('customer.restaurants', ['login' => 1]);
})->name('login');

Route::post('/login', [WebLoginController::class, 'store'])->middleware('throttle:login')->name('login.store');
Route::post('/logout', [WebLoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::get('/restaurants', [RestaurantController::class, 'index'])->name('customer.restaurants');
Route::get('/restaurants/{restaurant}', [RestaurantController::class, 'show'])->name('customer.restaurants.show');
Route::post('/restaurants/{restaurant}/orders', [OrderController::class, 'store'])
    ->middleware(['auth', 'role:customer'])
    ->name('customer.orders.store');
Route::post('/orders/{order}/review', [RestaurantReviewController::class, 'store'])
    ->middleware(['auth', 'role:customer'])
    ->name('customer.reviews.store');
Route::get('/menu/recommendations', [RestaurantController::class, 'randomMenu'])->name('customer.menu.random');
Route::post('/favorites/{restaurant}/toggle', [FavoriteController::class, 'toggle'])
    ->middleware('auth')
    ->name('favorites.toggle');
