<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_companies', function (Blueprint $table) {
            $table->id()->comment('ID компании');

            // Владелец/создатель компании (users.id)
            $table->foreignId('owner_user_id')
                ->comment('Владелец компании (users.id)')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->unsignedInteger('sort')->default(0)->comment('Позиция сортировки');
            $table->boolean('activity')->default(false)->comment('Активность компании');

            // Идентификаторы/публичность
            $table->string('name')->comment('Публичное название компании');
            $table->string('brand_name')->nullable()->comment('Бренд компании (если отличается от name)');
            $table->string('legal_name')->nullable()->comment('Юридическое название компании');

            $table->string('slug', 191)->nullable()->comment('Slug компании для URL (уникальный)');
            $table->string('external_id', 191)->nullable()->comment('ID компании во внешних системах (CRM/ERP и т.п.)');

            // Тип/налоги
            $table->string('company_type', 64)->nullable()->comment('Тип компании (ИП/ТОО/ООО/LLC/...)');
            $table->string('tax_regime', 64)->nullable()->comment('Налоговый режим (ОУР/УСН/патент/НДС/...)');

            $table->string('bin_iin', 32)->nullable()->comment('БИН/ИИН компании');

            // Контакты
            $table->string('email', 191)->nullable()->comment('Email компании');
            $table->string('phone', 32)->nullable()->comment('Телефон компании');

            // Мессенджеры
            $table->string('messenger_type', 32)->nullable()->comment('Тип мессенджера (whatsapp/telegram/wechat/viber)');
            $table->string('messenger_contact', 191)->nullable()->comment('Контакт в мессенджере (номер или username)');

            // Локация/адреса
            $table->string('country', 2)->nullable()->comment('Страна (ISO2, например: KZ)');
            $table->string('city', 128)->nullable()->comment('Город компании');

            $table->text('legal_address')->nullable()->comment('Юридический адрес компании');
            $table->text('actual_address')->nullable()->comment('Фактический адрес компании');

            $table->timestamps();

            /* ============================
             * ИНДЕКСЫ / УНИКАЛЬНОСТИ
             * ============================ */
            $table->index(['owner_user_id', 'activity', 'sort'], 'ix_market_companies_owner_activity_sort');

            $table->index(['activity'], 'ix_market_companies_activity');
            $table->index(['email'], 'ix_market_companies_email');
            $table->index(['bin_iin'], 'ix_market_companies_bin_iin');

            $table->unique(['slug'], 'uq_market_companies_slug');
            $table->unique(['bin_iin'], 'uq_market_companies_bin_iin');

            $table->comment('Маркет: торговые компании (тенанты)');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_companies');
    }
};
