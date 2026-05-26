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
        Schema::create('school_order_items', function (Blueprint $t) {
            $t->id();

            // Заказ онлайн-школы
            $t->foreignId('school_order_id')
                ->constrained('school_orders')
                ->cascadeOnDelete()
                ->comment('Заказ онлайн-школы');

            // Покупаемая сущность: курс, набор, тариф и т.д.
            $t->morphs('purchasable');

            // Снапшот данных на момент покупки
            $t->string('title')->comment('Название позиции на момент покупки');
            $t->string('sku')->nullable()->comment('SKU или код');
            $t->string('unit_name')->nullable()->comment('Единица измерения');

            // Цена и количество
            $t->char('currency', 3)->default('USD')->comment('Валюта');
            $t->unsignedInteger('quantity')->default(1)->comment('Количество');

            $t->decimal('unit_price', 18, 2)->default(0)->comment('Цена за единицу');
            $t->decimal('discount', 18, 2)->default(0)->comment('Скидка');
            $t->decimal('total', 18, 2)->default(0)->comment('Итого по позиции');

            // Дополнительные данные
            $t->json('attributes')->nullable()->comment('Атрибуты позиции');
            $t->json('meta')->nullable()->comment('Дополнительные данные');

            $t->timestamps();

            $t->index('school_order_id', 'idx_school_order_item_order');
            $t->index(['purchasable_type', 'purchasable_id'], 'idx_school_order_item_purchasable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_order_items');
    }
};
