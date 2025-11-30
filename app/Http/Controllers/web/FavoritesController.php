<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class FavoritesController extends Controller
{
    /**
     * Показать страницу избранного
     */
    public function index(): Response
    {
        $products = Product::with(['images' => function ($query) {
            $query->orderBy('sort_order');
        }])->get();

        return Inertia::render('Favorites/Index', [
            'products' => $products,
        ]);
    }
}
