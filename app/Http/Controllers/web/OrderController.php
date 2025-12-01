<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'phone'       => 'required|string|size:11',
            'items'       => 'required|array|min:1',
            'items.*.id'    => 'required|integer',
            'items.*.title' => 'required|string',
            'items.*.price' => 'required|numeric',
            'items.*.old_price' => 'nullable|numeric',
            'items.*.quantity'  => 'required|integer|min:1',
            'items.*.image'     => 'required|url',
            'total_price'   => 'required|numeric|min:1',
            'total_items'   => 'required|integer|min:1',
        ]);

        $items = collect($data['items'])->map(function ($item) {
            return [
                'id'         => (int) $item['id'],
                'title'      => $item['title'],
                'price'      => (int) round((float) $item['price']),
                'old_price'  => $item['old_price'] ? (int) round((float) $item['old_price']) : null,
                'quantity'   => (int) $item['quantity'],
                'image'      => $item['image'],
            ];
        })->toArray();

        $totalPrice = (int) round((float) $data['total_price']);

        // Защита от подмены цены
        $calculatedTotal = collect($items)->sum(fn($item) => $item['price'] * $item['quantity']);

        if ($calculatedTotal !== $totalPrice) {
            return response()->json([
                'errors' => ['total_price' => ['Неверная сумма заказа']]
            ], 422);
        }

        $order = Order::create([
            'phone'       => $data['phone'],
            'items'       => $items,
            'total_price' => $totalPrice,
            'total_items' => (int) $data['total_items'],
            'status'      => 'new',
        ]);

        // ✅ ИСПРАВЛЕНИЕ: Используем env() правильно
        $botToken = config('services.telegram.bot_token');
        $chatId   = config('services.telegram.admin_chat_id');

        if ($botToken && $chatId) {
            $itemsText = collect($items)
                ->map(fn($item) => "• {$item['title']} — {$item['quantity']} шт. × " . number_format($item['price'], 0, '', ' ') . " ₽")
                ->join("\n");

            $message = "🛍 Новый заказ #{$order->id}\n\n" .
                "📞 Телефон: +{$data['phone']}\n" .
                "💰 Сумма: " . number_format($totalPrice, 0, '', ' ') . " ₽\n\n" .
                "📦 Товары:\n{$itemsText}\n\n" .
                "🕐 " . now()->format('d.m.Y H:i');

            try {
                $response = Http::timeout(5)->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                    'chat_id'    => $chatId,
                    'text'       => $message,
                    'parse_mode' => 'HTML', // ✅ Добавить для форматирования
                ]);

                if (!$response->successful()) {
                    Log::error('Telegram notification failed', [
                        'order_id' => $order->id,
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Telegram exception', [
                    'order_id' => $order->id,
                    'message' => $e->getMessage()
                ]);
            }
        }

        // ✅ ПРАВИЛЬНО: Возвращаем 200 OK для Inertia
        return back();
    }
}
