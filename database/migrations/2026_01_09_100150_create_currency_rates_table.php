<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currency_rates', function (Blueprint $table) {
            $table->id();

            $table->foreignId('base_currency_id')
                ->constrained('currencies')
                ->cascadeOnDelete();

            $table->foreignId('quote_currency_id')
                ->constrained('currencies')
                ->cascadeOnDelete();

            // курс: 20,8 как у тебя
            $table->decimal('rate', 20, 8);

            $table->string('provider', 64)->nullable();
            $table->boolean('is_manual')->default(false);

            // ✅ микросекунды + useCurrent => MySQL: CURRENT_TIMESTAMP(6), PG: now()
            // не nullable — чтобы unique работал предсказуемо
            $table->timestamp('fetched_at', 6)->useCurrent();

            $table->timestamps();

            // ✅ unique уже создаёт индекс по этим полям — отдельный idx_pair_fetched_at не нужен
            $table->unique(
                ['base_currency_id', 'quote_currency_id', 'fetched_at'],
                'currency_rates_pair_at_unique'
            );

            // ✅ быстрые выборки по паре (история/последняя запись)
            $table->index(
                ['base_currency_id', 'quote_currency_id'],
                'currency_rates_pair_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currency_rates');
    }
};
