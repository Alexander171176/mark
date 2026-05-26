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
        Schema::create('school_provider_accounts', function (Blueprint $t) {
            $t->id();

            // Провайдер и режим
            $t->string('provider', 64)->comment('stripe|paypal|yookassa и т.п.');
            $t->string('title')->nullable()->comment('Название аккаунта');
            $t->string('mode', 8)->default('test')->comment('test|live');

            // Идентификаторы и ключи
            $t->string('account_id', 191)->nullable()->comment('ID аккаунта у провайдера');
            $t->string('public_key', 255)->nullable()->comment('Публичный ключ');
            $t->text('secret_key')->nullable()->comment('Секретный ключ (шифруется)');
            $t->text('webhook_secret')->nullable()->comment('Webhook секрет (шифруется)');

            // Поддержка
            $t->json('supported_currencies')->nullable()->comment('Поддерживаемые валюты');
            $t->json('supported_countries')->nullable()->comment('Поддерживаемые страны');
            $t->json('config')->nullable()->comment('Дополнительные настройки');

            // Флаги
            $t->boolean('activity')->default(true)->comment('Активность');
            $t->boolean('is_default')->default(false)->comment('По умолчанию');

            // Аудит
            $t->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('Кто создал');

            $t->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('Кто обновил');

            $t->timestamps();

            // Индексы
            $t->index(['provider', 'mode', 'activity'], 'idx_school_provider_mode_active');

            $t->unique(
                ['provider', 'mode', 'account_id'],
                'uniq_school_provider_mode_account'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_provider_accounts');
    }
};
