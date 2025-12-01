<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    // Список категорий
    public function index()
    {
        $categories = Category::with('childrenRecursive')->whereNull('parent_id')->orderBy('id')->get();

        return Inertia::render('Categories', [
            'categories' => $categories
        ]);
    }

    // Форма создания
    public function create()
    {
        $parents = Category::whereNull('parent_id')->get(); // для выбора родителя
        return Inertia::render('Categories/Create', [
            'parents' => $parents
        ]);
    }

    // Сохранение новой категории
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        Category::create($request->only('name', 'parent_id'));

        return redirect()->route('categories.index')->with('success', 'Категория создана');
    }

    // Форма редактирования
    public function edit(Category $category)
    {
        $parents = Category::whereNull('parent_id')->where('id', '!=', $category->id)->get();

        return Inertia::render('Categories/Edit', [
            'category' => $category,
            'parents' => $parents,
        ]);
    }

    // Обновление категории
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $category->update($request->only('name', 'parent_id'));

        return redirect()->route('categories.index')->with('success', 'Категория обновлена');
    }

    // Удаление категории
    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Категория удалена');
    }
}
