<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('phone');                    // номер телефона
            $table->json('items');                      // весь массив товаров
            $table->unsignedInteger('total_price');     // итого в копейках или рублях (как у тебя сейчас)
            $table->unsignedSmallInteger('total_items'); // количество товаров
            $table->string('status')->default('new');   // new, processing, confirmed, cancelled и т.д.
            $table->text('comment')->nullable();        // если потом добавишь поле комментария
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
