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
        Schema::create('school_course_prices', function (Blueprint $t) {
            $t->id();

            $t->foreignId('school_course_id')
                ->constrained('school_courses')
                ->cascadeOnDelete()
                ->comment('Курс');

            $t->foreignId('currency_id')
                ->constrained('currencies')
                ->restrictOnDelete()
                ->comment('Валюта');

            $t->decimal('price', 18, 2)->comment('Основная цена');
            $t->decimal('sale_price', 18, 2)->nullable()->comment('Акционная цена');
            $t->decimal('compare_at_price', 18, 2)->nullable()->comment('Старая цена');

            $t->timestamp('starts_at')->nullable()->comment('Дата начала действия цены');
            $t->timestamp('ends_at')->nullable()->comment('Дата окончания действия цены');

            $t->boolean('activity')->default(true)->comment('Активность цены');
            $t->unsignedInteger('sort')->default(0)->comment('Порядок сортировки');

            $t->json('meta')->nullable()->comment('Дополнительные данные');

            $t->timestamps();

            $t->index(
                ['school_course_id', 'currency_id', 'activity'],
                'idx_school_course_price_currency_active'
            );

            $t->index(
                ['activity', 'sort'],
                'idx_school_course_price_active_sort'
            );

            $t->index(
                ['school_course_id', 'currency_id', 'starts_at', 'ends_at'],
                'idx_school_course_price_period'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_course_prices');
    }
};
