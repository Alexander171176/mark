<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Запуск миграции.
     */
    public function up(): void
    {
        Schema::create('market_product_related', function (Blueprint $table) {
            $table->id()
                ->comment('PK');

            /**
             * Основной товар
             */
            $table->unsignedBigInteger('market_product_id')
                ->comment('Основной товар (market_products.id)');

            $table->foreign(
                'market_product_id',
                'market_product_related_product_id_fk'
            )
                ->references('id')
                ->on('market_products')
                ->cascadeOnDelete();

            /**
             * Рекомендуемый / похожий товар
             */
            $table->unsignedBigInteger('related_product_id')
                ->comment('Рекомендуемый товар (market_products.id)');

            $table->foreign(
                'related_product_id',
                'market_product_related_related_product_id_fk'
            )
                ->references('id')
                ->on('market_products')
                ->cascadeOnDelete();

            /**
             * Тип связи
             */
            $table->string('type', 50)
                ->default('related')
                ->index('market_product_related_type_idx')
                ->comment('Тип связи: related, similar, accessory, analog');

            /**
             * Отображение
             */
            $table->unsignedInteger('order')
                ->default(0)
                ->index('market_product_related_order_idx')
                ->comment('Порядок отображения рекомендованного товара');

            $table->boolean('activity')
                ->default(true)
                ->index('market_product_related_activity_idx')
                ->comment('Активность связи');

            $table->timestamps();

            /**
             * Один товар не должен иметь один и тот же рекомендуемый товар дважды
             */
            $table->unique(
                ['market_product_id', 'related_product_id', 'type'],
                'market_product_related_unique'
            );

            /**
             * Быстрые выборки
             */
            $table->index(
                ['market_product_id', 'type', 'activity', 'order'],
                'market_product_related_product_type_idx'
            );

            $table->index(
                ['related_product_id', 'activity'],
                'market_product_related_related_activity_idx'
            );

            $table->comment(
                'Маркетплейс: рекомендуемые, похожие, аналогичные товары и аксессуары для товара.'
            );
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_product_related');
    }
};
