<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * market_feedback_spam_blocks
 * Блокировки спама: IP / fingerprint / email / phone / user_id.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_feedback_spam_blocks', function (Blueprint $table) {
            $table->id()->comment('ID правила блокировки спама');

            // tenant-safe
            $table->foreignId('company_id')
                ->comment('Компания-владелец (market_companies.id)')
                ->constrained('market_companies')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('storefront_id')
                ->comment('Витрина (market_storefronts.id)');

            $table->foreign(['company_id', 'storefront_id'], 'fk_mfsb_company_storefront')
                ->references(['company_id', 'id'])
                ->on('market_storefronts')
                ->cascadeOnDelete();

            $table->boolean('activity')->default(true)->comment('Активность блокировки');

            $table->string('type', 32)->comment('Тип: ip/fingerprint/email/phone/user');
            $table->string('value', 255)->comment('Значение (ip/хеш/email/phone/user_id строкой)');

            $table->string('reason', 255)->nullable()->comment('Причина блокировки');
            $table->timestamp('blocked_until')->nullable()->comment('Блокировать до (если временно)');

            $table->foreignId('created_by_user_id')
                ->nullable()
                ->comment('Кто добавил блокировку (users.id), опционально')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            // Нельзя плодить дубликаты блокировок
            $table->unique(['storefront_id', 'type', 'value'], 'uq_mfsb_storefront_type_value');

            // ✅ критично для tenant-safe ссылок в будущем
            $table->unique(['company_id', 'storefront_id', 'id'], 'uq_mfsb_tenant_id');

            $table->index(['storefront_id', 'activity'], 'ix_mfsb_storefront_active');
            $table->index(['type', 'value'], 'ix_mfsb_type_value');
            $table->index(['blocked_until'], 'ix_mfsb_blocked_until');

            // ✅ удобно для быстрых проверок блокировки
            $table->index(['storefront_id','type','value','activity'], 'ix_mfsb_sf_type_value_active');

            $table->comment('Маркет: блокировки спама для форм обратной связи (IP/fingerprint/email/phone/user), tenant-safe.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_feedback_spam_blocks');
    }
};
