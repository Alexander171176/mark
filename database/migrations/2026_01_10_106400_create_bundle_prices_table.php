<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Таблица цен для бандлов.
     * Поддерживает:
     *  - мультивалютность (несколько валют на один бандл),
     *  - акции (sale_price / compare_at_price),
     *  - периоды действия цены (starts_at / ends_at),
     *  - сортировку и управление активностью.
     */
    public function up(): void
    {
        Schema::create('bundle_prices', function (Blueprint $t) {
            /**
             * Первичный ключ записи цены.
             */
            $t->id();

            /**
             * Ссылка на бандл, к которому относится цена.
             * Один бандл может иметь несколько цен (разные валюты, разные периоды, разные акции).
             */
            $t->foreignId('bundle_id')
                ->constrained('bundles')
                ->cascadeOnDelete();

            /**
             * Валюта цены (например USD, EUR, KZT).
             * Ссылается на справочник currencies.
             * restrictOnDelete — запрещает удалять валюту, если она используется в ценах.
             */
            $t->foreignId('currency_id')
                ->constrained('currencies')
                ->restrictOnDelete();

            /**
             * Основная (базовая) цена бандла в указанной валюте.
             * Всегда должна быть задана.
             */
            $t->decimal('price', 18, 2);

            /**
             * Акционная цена.
             * Если задана и меньше price — используется как текущая цена.
             */
            $t->decimal('sale_price', 18, 2)->nullable();

            /**
             * "Старая" цена для визуального отображения скидки ("было / стало").
             * Обычно больше текущей цены.
             */
            $t->decimal('compare_at_price', 18, 2)->nullable();

            /**
             * Дата начала действия данной цены.
             * Если NULL — цена действует сразу после создания записи.
             */
            $t->timestamp('starts_at')->nullable();

            /**
             * Дата окончания действия данной цены.
             * Если NULL — цена действует бессрочно.
             */
            $t->timestamp('ends_at')->nullable();

            /**
             * Признак активности цены.
             * false — цена отключена и не участвует в расчётах и отображении.
             */
            $t->boolean('activity')->default(true);

            /**
             * Позиция сортировки внутри списка цен.
             * Используется для ручного управления порядком отображения.
             */
            $t->unsignedInteger('sort')->default(0);

            /**
             * Дополнительные данные в формате JSON:
             * например: тип акции, источник цены, комментарии, маркетинговые параметры и т.д.
             */
            $t->json('meta')->nullable();

            /**
             * Временные метки создания и обновления записи.
             */
            $t->timestamps();

            /**
             * Мягкое удаление (логическое удаление).
             * Запись не удаляется физически из БД.
             */
            $t->softDeletes();

            /**
             * Индекс для быстрого поиска активных цен конкретного бандла в определённой валюте.
             * Часто используется при расчёте цены на витрине.
             */
            $t->index(
                ['bundle_id', 'currency_id', 'activity'],
                'idx_bundle_prices_bundle_currency_active'
            );

            /**
             * Индекс для выборок всех активных цен в нужном порядке сортировки.
             * Используется в админке и витрине.
             */
            $t->index(
                ['activity', 'sort'],
                'idx_bundle_prices_active_pos'
            );

            /**
             * Индекс для поиска актуальной цены бандла по валюте и периоду действия.
             * Используется для выбора цены на определённую дату.
             */
            $t->index(
                ['bundle_id', 'currency_id', 'starts_at', 'ends_at'],
                'idx_bundle_prices_bundle_currency_period'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bundle_prices');
    }
};
