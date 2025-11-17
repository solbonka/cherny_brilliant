<?php


namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        // Заглушки категорий
        $categories = collect([
            ['id' => 1, 'name' => 'Норковые шубы'],
            ['id' => 2, 'name' => 'Дубленки'],
            ['id' => 3, 'name' => 'Пуховики'],
            ['id' => 4, 'name' => 'Тренчи и пальто'],
        ]);

        // Заглушки товаров
        $products = collect([
            ['id' => 1, 'name' => 'Норковая шуба премиум', 'description' => 'Тепло и стиль', 'icon' => '🦊', 'category_id' => 1],
            ['id' => 2, 'name' => 'Дубленка классическая', 'description' => 'Элегантность и комфорт', 'icon' => '👔', 'category_id' => 2],
            ['id' => 3, 'name' => 'Пуховик зимний', 'description' => 'Легкий и теплый', 'icon' => '❄️', 'category_id' => 3],
            ['id' => 4, 'name' => 'Тренч стильный', 'description' => 'Идеально для весны', 'icon' => '🧥', 'category_id' => 4],
        ]);

        $categoryId = $request->query('category');
        if ($categoryId) {
            $products = $products->where('category_id', (int)$categoryId)->values();
        }

        return Inertia::render('Catalog/Index', [
            'categories' => $categories,
            'products' => $products,
            'selectedCategory' => $categoryId ? (int)$categoryId : null,
        ]);
    }
}
