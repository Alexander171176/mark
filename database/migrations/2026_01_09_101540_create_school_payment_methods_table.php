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
        Schema::create('school_payment_methods', function (Blueprint $t) {
            $t->id();

            $t->string('code', 64)->unique()->comment('Системный код');
            $t->string('name')->comment('Название способа оплаты');

            $t->string('provider', 64)->nullable()->comment('Провайдер');
            $t->string('type', 32)->nullable()->comment('Тип оплаты');

            $t->boolean('supports_refund')->default(true)->comment('Поддерживает возвраты');
            $t->boolean('supports_recurring')->default(true)->comment('Поддерживает рекуррентные платежи');

            $t->boolean('activity')->default(true)->comment('Активность');
            $t->unsignedInteger('sort')->default(0)->comment('Порядок сортировки');

            $t->json('meta')->nullable()->comment('Настройки способа оплаты');

            $t->timestamps();

            $t->index(['activity', 'sort'], 'idx_school_pm_active_sort');
            $t->index(['provider', 'type'], 'idx_school_pm_provider_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_payment_methods');
    }
};
