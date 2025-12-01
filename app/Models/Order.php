<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // items всегда будет json → массив
    protected $casts = [
        'items' => 'array',
        'total_price' => 'integer',
        'total_items' => 'integer',
    ];

    // Красивое форматирование цены для админки
    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total_price, 0, '', ' ') . ' ₽';
    }
}
