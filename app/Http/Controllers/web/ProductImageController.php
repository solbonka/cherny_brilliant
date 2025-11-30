<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class ProductImageController extends Controller
{
    public function destroy(ProductImage $image): JsonResponse
    {
        Storage::disk('public')->delete($image->path);
        $image->delete();

        return response()->json([
            'message' => 'Изображение удалено',
        ]);
    }

    public function updateSort(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'images' => 'required|array',
            'images.*.id' => 'required|exists:product_images,id',
            'images.*.sort_order' => 'required|integer|min:0',
            'images.*.is_main' => 'required|boolean',
        ]);

        foreach ($validated['images'] as $imageData) {
            ProductImage::where('id', $imageData['id'])
                ->update([
                    'sort_order' => $imageData['sort_order'],
                    'is_main' => $imageData['is_main']
                ]);
        }

        return response()->json([
            'message' => 'Порядок изображений обновлён',
        ]);
    }
}
