<?php

use App\Http\Controllers\web\CartController;
use App\Http\Controllers\web\CatalogController;
use App\Http\Controllers\web\CategoryController;
use App\Http\Controllers\web\FavoritesController;
use App\Http\Controllers\web\ProductController;
use App\Http\Controllers\web\ProductImageController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    $products = [
        ['id' => 1, 'icon' => '🦊', 'name' => 'Норковые шубы', 'description' => 'Роскошные шубы из натуральной норки премиум качества'],
        ['id' => 2, 'icon' => '🧥', 'name' => 'Парки', 'description' => 'Модные и комфортные парки для холодной погоды'],
        ['id' => 3, 'icon' => '👔', 'name' => 'Дубленки', 'description' => 'Стильные дубленки из натуральной кожи'],
        ['id' => 4, 'icon' => '🎩', 'name' => 'Пальто', 'description' => 'Элегантные пальто для создания идеального образа'],
        ['id' => 5, 'icon' => '❄️', 'name' => 'Пуховики', 'description' => 'Качественные пуховики с современным дизайном'],
        ['id' => 6, 'icon' => '✨', 'name' => 'Жилеты', 'description' => 'Стильные жилеты для любого случая']
    ];

    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
        'products' => $products,
    ]);
})->name('home');

Route::get('/favorites', [FavoritesController::class, 'index'])->name('favorites');
Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog');
Route::get('/catalog/{product}', [CatalogController::class, 'show'])
    ->name('catalog.product.show');
Route::resource('categories', CategoryController::class)->middleware(['auth', 'verified']);
Route::resource('products', ProductController::class)->middleware(['auth', 'verified']);
Route::post('products/{product}', [ProductController::class, 'update'])->middleware(['auth', 'verified']);
Route::delete('product-images/{image}', [ProductImageController::class, 'destroy'])
    ->name('product-images.destroy')->middleware(['auth', 'verified']);
Route::post('product-images/sort', [ProductImageController::class, 'updateSort'])
    ->name('product-images.sort')->middleware(['auth', 'verified']);
Route::get('dashboard', function () {
    return Inertia::render('Dashboard', []);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/test', function () {
    return Inertia::render('main/AppHeadLogo', []);
});
require __DIR__.'/settings.php';
