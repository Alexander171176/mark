<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Купоны/промокоды
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $t) {
            $t->id();

            $t->string('code', 64)->unique();        // Промокод, вводимый пользователем
            $t->string('name')->nullable();          // Человекочитаемое название
            $t->text('description')->nullable();     // Описание для админки

            // Тип и величина скидки
            $t->string('type', 16)
                ->default('percent')
                ->comment('percent|fixed|free');       // free — 100% скидка
            $t->decimal('value', 18, 2)->default(0); // % (0..100) для percent, сумма для fixed
            $t->char('currency', 3)->nullable();     // Валюта суммы (нужно только для fixed)

            // Ограничения применения
            $t->decimal('min_order_total', 18, 2)->nullable(); // Мин. сумма заказа
            $t->unsignedInteger('max_uses')->nullable();       // Общий лимит применений
            $t->unsignedInteger('max_uses_per_user')->nullable(); // Лимит на пользователя
            $t->unsignedInteger('used_count')->default(0);     // Счётчик применений (для отчётности)

            // Область применения (если не через пивоты)
            $t->string('applies_to', 16)
                ->default('any')
                ->comment('any|courses|bundles');      // Общая подсказка для UI/валидации

            // Срок действия и статус
            $t->timestamp('starts_at')->nullable();
            $t->timestamp('ends_at')->nullable();
            $t->boolean('activity')->default(true);
            $t->boolean('stackable')->default(false); // Можно ли комбинировать с другими купонами

            // Служебные поля
            $t->json('meta')->nullable();
            $t->timestamps();
            $t->softDeletes();

            // Частые индексы
            $t->index(['activity', 'starts_at', 'ends_at'], 'idx_coupons_active_window');
            $t->index(['applies_to', 'activity'], 'idx_coupons_scope_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
