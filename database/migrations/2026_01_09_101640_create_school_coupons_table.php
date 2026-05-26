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
        Schema::create('school_coupons', function (Blueprint $t) {
            $t->id();

            $t->string('code', 64)->unique()->comment('Промокод');
            $t->string('name')->nullable()->comment('Название купона');
            $t->text('description')->nullable()->comment('Описание для админки');

            // Тип и размер скидки
            $t->string('type', 16)
                ->default('percent')
                ->comment('percent|fixed|free');

            $t->decimal('value', 18, 2)->default(0)->comment('Размер скидки');
            $t->char('currency', 3)->nullable()->comment('Валюта для fixed-скидки');

            // Ограничения
            $t->decimal('min_order_total', 18, 2)->nullable()->comment('Минимальная сумма заказа');
            $t->unsignedInteger('max_uses')->nullable()->comment('Общий лимит применений');
            $t->unsignedInteger('max_uses_per_user')->nullable()->comment('Лимит применений на пользователя');
            $t->unsignedInteger('used_count')->default(0)->comment('Количество использований');

            // Область применения
            $t->string('applies_to', 16)
                ->default('any')
                ->comment('any|courses|bundles');

            // Срок действия
            $t->timestamp('starts_at')->nullable()->comment('Дата начала действия');
            $t->timestamp('ends_at')->nullable()->comment('Дата окончания действия');

            // Статус
            $t->boolean('activity')->default(true)->comment('Активность купона');
            $t->boolean('stackable')->default(false)->comment('Можно комбинировать с другими купонами');

            $t->json('meta')->nullable()->comment('Дополнительные данные');

            $t->timestamps();

            $t->index(['activity', 'starts_at', 'ends_at'], 'idx_school_coupon_active_window');
            $t->index(['applies_to', 'activity'], 'idx_school_coupon_scope_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_coupons');
    }
};
