<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->query('q', '');

        if (strlen(trim($query)) < 2) {
            return response()->json(['products' => []]);
        }

        $products = Product::with(['images' => fn($q) => $q->orderBy('sort_order')])
            ->where('title', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->select('id', 'title', 'price', 'old_price')
            ->limit(15)
            ->get();

        $mapped = $products->map(function ($product) {
            $firstImage = $product->images->first();

            return [
                'id'         => $product->id,
                'title'      => $product->title,
                'price'      => (int) $product->price,
                'old_price'  => $product->old_price ? (int) $product->old_price : null,
                'image'      => $firstImage
                    ? Storage::url($firstImage->path)           // ← если у тебя поле path
                    // ? asset('storage/' . $firstImage->image) // ← если поле называется image
                    : '/images/placeholder.jpg',
                // Если потом добавишь slug — просто добавь сюда
            ];
        });

        return response()->json(['products' => $mapped]);
    }
}
