<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Таблица цен для курсов.
     * Поддерживает:
     *  - мультивалютность (несколько валют на один курс),
     *  - акции (sale_price / compare_at_price),
     *  - периоды действия цены (starts_at / ends_at),
     *  - сортировку и управление активностью.
     */
    public function up(): void
    {
        Schema::create('course_prices', function (Blueprint $t) {
            // Первичный ключ
            $t->id();

            /**
             * Ссылка на курс, к которому относится цена.
             * Один курс может иметь несколько цен (например, в разных валютах или в разные периоды).
             */
            $t->foreignId('course_id')
                ->constrained('courses')
                ->cascadeOnDelete();

            /**
             * Валюта цены (например USD, EUR, KZT).
             * Ссылается на справочник currencies.
             * restrictOnDelete — нельзя удалить валюту, если есть связанные цены.
             */
            $t->foreignId('currency_id')
                ->constrained('currencies')
                ->restrictOnDelete();

            /**
             * Основная (базовая) цена курса в указанной валюте.
             * Всегда должна быть задана.
             */
            $t->decimal('price', 18, 2);

            /**
             * Акционная цена.
             * Если задана и меньше price — используется как текущая цена.
             */
            $t->decimal('sale_price', 18, 2)->nullable();

            /**
             * "Старая" цена для отображения скидки.
             * Обычно больше price и используется только для визуального эффекта ("было / стало").
             */
            $t->decimal('compare_at_price', 18, 2)->nullable();

            /**
             * Дата начала действия данной цены.
             * Если NULL — цена действует с момента создания записи.
             */
            $t->timestamp('starts_at')->nullable();

            /**
             * Дата окончания действия данной цены.
             * Если NULL — цена действует бессрочно.
             */
            $t->timestamp('ends_at')->nullable();

            /**
             * Признак активности цены.
             * false — цена отключена и не участвует в расчётах/показе.
             */
            $t->boolean('activity')->default(true);

            /**
             * Позиция сортировки внутри набора цен.
             * Используется для ручного управления порядком отображения.
             */
            $t->unsignedInteger('sort')->default(0);

            /**
             * Дополнительные данные в JSON:
             * например: источник цены, комментарий, тип акции, условия и т.п.
             */
            $t->json('meta')->nullable();

            // Временные метки created_at и updated_at
            $t->timestamps();

            // Мягкое удаление (логическое удаление)
            $t->softDeletes();

            /**
             * Индекс для быстрого поиска активных цен по курсу и валюте.
             * Часто используется при расчёте цены на витрине.
             */
            $t->index(
                ['course_id', 'currency_id', 'activity'],
                'idx_course_prices_course_currency_active'
            );

            /**
             * Индекс для выборок всех активных цен в нужном порядке.
             * Используется в админке и витрине.
             */
            $t->index(
                ['activity', 'sort'],
                'idx_course_prices_active_pos'
            );

            /**
             * Индекс для поиска цены по курсу, валюте и периоду действия.
             * Нужен для выбора "актуальной" цены на определённую дату.
             */
            $t->index(
                ['course_id', 'currency_id', 'starts_at', 'ends_at'],
                'idx_course_prices_course_currency_period'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_prices');
    }
};
