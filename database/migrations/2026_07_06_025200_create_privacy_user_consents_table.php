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
        Schema::create('privacy_user_consents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('ID авторизованного пользователя, если пользователь вошёл в систему');

            $table->string('session_id', 255)
                ->nullable()
                ->index('privacy_user_consents_session_id_idx')
                ->comment('ID сессии пользователя');

            $table->string('ip_address', 45)
                ->nullable()
                ->index('privacy_user_consents_ip_address_idx')
                ->comment('IP-адрес пользователя IPv4 или IPv6');

            $table->text('user_agent')
                ->nullable()
                ->comment('Информация о браузере, устройстве и операционной системе пользователя');

            $table->string('locale', 10)
                ->nullable()
                ->index('privacy_user_consents_locale_idx')
                ->comment('Язык, на котором пользователь принял политику: ru, en, kk');

            $table->unsignedInteger('policy_version')
                ->default(1)
                ->index('privacy_user_consents_policy_version_idx')
                ->comment('Версия политики конфиденциальности и обработки данных');

            $table->string('policy_url', 500)
                ->nullable()
                ->comment('URL страницы политики конфиденциальности, которую принял пользователь');

            $table->string('policy_hash', 64)
                ->nullable()
                ->index('privacy_user_consents_policy_hash_idx')
                ->comment('SHA-256 хэш текста политики для фиксации принятой версии документа');

            $table->boolean('accepted')
                ->default(false)
                ->index('privacy_user_consents_accepted_idx')
                ->comment('Пользователь дал согласие на обработку данных и использование cookie');

            $table->timestamp('accepted_at')
                ->nullable()
                ->index('privacy_user_consents_accepted_at_idx')
                ->comment('Дата и время принятия политики');

            $table->timestamp('revoked_at')
                ->nullable()
                ->index('privacy_user_consents_revoked_at_idx')
                ->comment('Дата и время отзыва согласия пользователем');

            $table->timestamps();

            $table->index(
                ['user_id', 'accepted', 'policy_version'],
                'privacy_user_consents_user_accepted_policy_idx'
            );

            $table->index(
                ['session_id', 'accepted', 'policy_version'],
                'privacy_user_consents_session_accepted_policy_idx'
            );

            $table->comment(
                'Согласия пользователей на обработку персональных данных, технических данных и использование cookie'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('privacy_user_consents');
    }
};
