<?php


namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()
            ->with(['images' => fn($q) => $q->orderBy('sort_order')])
            ->with('category') // если нужно имя категории на фронте

            // Поиск по названию и описанию
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })

            // Фильтр по категории (и всем её дочерним рекурсивно)
            ->when($request->filled('category_id'), function ($q) use ($request) {
                $categoryId = (int) $request->category_id;

                // Загружаем категорию со всем её рекурсивным поддеревом (один запрос)
                $category = Category::with('childrenRecursive')->findOrFail($categoryId);

                // Рекурсивно собираем ВСЕ ID потомков + сама категория
                $descendantIds = collect([$categoryId]);

                $queue = collect([$category]);

                while ($queue->isNotEmpty()) {
                    $current = $queue->pop();

                    if ($current->childrenRecursive?->isNotEmpty()) {
                        $childIds = $current->childrenRecursive->pluck('id');

                        $descendantIds = $descendantIds->merge($childIds);
                        $queue = $queue->merge($current->childrenRecursive);
                    }
                }

                $q->whereIn('category_id', $descendantIds);
            })

            ->latest()
            ->paginate(12)
            ->withQueryString(); // важно! сохраняет search и category_id в URL

        return Inertia::render('Catalog/Index', [
            'products'   => $products,
            'categories' => Category::with('childrenRecursive')
                ->whereNull('parent_id')
                ->orderBy('id') // или id, как тебе удобно
                ->get(),

            'filters' => $request->only(['search', 'category_id']),
        ]);
    }

    public function show(Product $product)
    {
        $product->load(['images' => fn($q) => $q->orderBy('sort_order')]);

        return Inertia::render('Catalog/ProductShow', [
            'product' => $product
        ]);
    }
}
