<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Курсы валют (источник -> котировка)
    public function up(): void
    {
        Schema::create('exchange_rates', function (Blueprint $t) {
            $t->id();

            // Пара валют ISO-4217, UPPERCASE (например, USD -> EUR)
            $t->char('base_currency', 3);   // из какой валюты конвертируем
            $t->char('quote_currency', 3);  // в какую валюту конвертируем

            // Курс: 1 base_currency = rate quote_currency
            $t->decimal('rate', 20, 8);     // высокая точность для финансов

            // Метаданные источника/валидации
            $t->string('provider', 64)->nullable();     // название источника (ECB, Fixer, ЦБ РФ, и т.д.)
            $t->timestamp('fetched_at')->nullable();    // когда получили с источника
            $t->timestamp('valid_from')->nullable();    // с какого момента считать актуальным
            $t->timestamp('valid_to')->nullable();      // до какого момента актуален (nullable = бессрочно)
            $t->boolean('activity')->default(true);    // флаг использования в расчётах (можно выключать старые)

            $t->json('meta')->nullable();               // “сырой” ответ провайдера/атрибуты

            $t->timestamps();
            $t->softDeletes();

            // Частые запросы
            $t->index(['base_currency', 'quote_currency', 'activity'], 'idx_pair_active');
            $t->index(['valid_from', 'valid_to'], 'idx_valid_window');

            // Уникальность пары на момент времени (опционально, чтобы не плодить дублей “актуальных” записей)
            // Если нужен строгий контроль одной активной записи на пару — раскомментируй:
            // $t->unique(['base_currency', 'quote_currency', 'valid_from'], 'uniq_pair_from');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
