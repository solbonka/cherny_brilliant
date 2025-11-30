<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $products = Product::with(['images' => function ($query) {
            $query->orderBy('sort_order');
        }])
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Products/Index', [
            'products' => $products,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): Response
    {
        $categories = Category::whereNull('parent_id')
            ->with('childrenRecursive')
            ->orderBy('id')
            ->get();

        return Inertia::render('Products/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'old_price' => 'nullable|numeric|min:0',
            'size' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'material' => 'nullable|string|max:255',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $product = Product::create($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');

                $product->images()->create([
                    'path' => $path,
                    'is_main' => $index === 0, // Первое изображение - главное
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('products.index')
            ->with('success', 'Товар успешно создан');
    }

    public function show(Product $product): Response
    {
        $product->load(['images' => function ($query) {
            $query->orderBy('sort_order');
        }]);

        return Inertia::render('Products/Show', [
            'product' => $product,
        ]);
    }

    public function edit(Product $product): Response
    {
        $product->load(['images' => function ($query) {
            $query->orderBy('sort_order');
        }]);

        $categories = Category::whereNull('parent_id')
            ->with('childrenRecursive')
            ->orderBy('id')
            ->get();

        return Inertia::render('Products/Edit', [
            'product' => $product,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'old_price' => 'nullable|numeric|min:0',
            'size' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'material' => 'nullable|string|max:255',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'existing_images' => 'nullable|array',
            'existing_images.*.id' => 'required|exists:product_images,id',
            'existing_images.*.sort_order' => 'required|integer|min:0',
            'existing_images.*.is_main' => 'required|boolean',
        ]);

        $product->update($validated);
        // Обновление порядка и главного изображения для существующих фото
        if ($request->filled('existing_images')) {
            foreach ($request->input('existing_images') as $imageData) {
                $product->images()->where('id', $imageData['id'])->update([
                    'sort_order' => $imageData['sort_order'],
                    'is_main'    => $imageData['is_main'] ?? false,
                ]);
            }

            // Гарантируем, что ровно одно изображение будет главным
            // (на случай, если пользователь снял галочку со всех или поставил на несколько)
            $hasMain = $product->images()->where('is_main', true)->exists();
            if (! $hasMain && $product->images()->count() > 0) {
                $product->images()->orderBy('sort_order')->first()->update(['is_main' => true]);
            }
        }

        // Добавление новых изображений
        if ($request->hasFile('images')) {
            $maxSortOrder = $product->images()->max('sort_order') ?? -1;

            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');

                $product->images()->create([
                    'path' => $path,
                    'is_main' => false, // Новые изображения не главные по умолчанию
                    'sort_order' => $maxSortOrder + $index + 1,
                ]);
            }
        }

        return redirect()->route('products.index')
            ->with('success', 'Товар успешно обновлён');
    }

    public function destroy(Product $product): RedirectResponse
    {
        // Удаление всех изображений из хранилища
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Товар успешно удалён');
    }
}
