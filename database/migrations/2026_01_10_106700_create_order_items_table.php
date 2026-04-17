<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    // Универсальная позиция заказа
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $t) {
            $t->id();

            /* ============================
             * СВЯЗЬ С ЗАКАЗОМ
             * ============================ */
            $t->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            /* ============================
             * ПОЛИМОРФНАЯ СВЯЗЬ С ТОВАРОМ
             * ============================ */
            // Может ссылаться на:
            // Course, Bundle, Product, SubscriptionPlan, Service, etc.
            $t->morphs('purchasable');
            // создаст: purchasable_type + purchasable_id

            /* ============================
             * СНАПШОТ НАЗВАНИЯ И ДАННЫХ
             * ============================ */
            $t->string('title');     // название на момент покупки
            $t->string('sku')->nullable(); // SKU или код товара (если есть)
            $t->string('unit_name')->nullable(); // ед. измерения (шт, мес, услуга)

            /* ============================
             * ЦЕНЫ И КОЛИЧЕСТВО
             * ============================ */
            $t->char('currency', 3)->default('USD');
            $t->unsignedInteger('quantity')->default(1);

            $t->decimal('unit_price', 18, 2)->default(0);      // цена за единицу
            $t->decimal('discount', 18, 2)->default(0);        // скидка по позиции
            $t->decimal('total', 18, 2)->default(0);           // всего по позиции

            /* ============================
             * ДОП. ПАРАМЕТРЫ ТОВАРА/КУРСА
             * ============================ */
            $t->json('attributes')->nullable(); // цвет/размер, тариф подписки, параметры
            $t->json('meta')->nullable();       // любые доп. данные

            $t->timestamps();

            /* ============================
             * ИНДЕКСЫ
             * ============================ */
            $t->index(['order_id']);
            $t->index(['purchasable_type', 'purchasable_id'], 'idx_purchasable');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
