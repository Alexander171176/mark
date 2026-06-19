<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_companies', function (Blueprint $table) {
            $table->id()->comment('PK');

            $table->unsignedBigInteger('user_id')
                ->index('market_companies_user_id_idx')
                ->comment('Владелец компании (users.id)');

            $table->foreign('user_id', 'market_companies_user_id_fk')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->string('url', 500)
                ->index('market_companies_url_idx')
                ->comment('Общий URL/slug компании (без домена), один для всех локалей');

            $table->string('company_type', 50)
                ->default('company')
                ->index('market_companies_company_type_idx')
                ->comment('Тип компании: company, entrepreneur, individual');

            $table->string('bin_iin', 20)
                ->nullable()
                ->unique('market_companies_bin_iin_unique')
                ->comment('БИН или ИИН компании');

            $table->string('legal_name', 255)
                ->nullable()
                ->comment('Юридическое название компании');

            $table->string('director_name', 255)
                ->nullable()
                ->comment('ФИО руководителя по юридическим документам');

            $table->string('email', 255)
                ->nullable()
                ->comment('Основной email компании');

            $table->string('phone', 50)
                ->nullable()
                ->comment('Основной телефон компании');

            $table->string('website', 255)
                ->nullable()
                ->comment('Сайт компании');

            $table->string('logo', 500)
                ->nullable()
                ->comment('Логотип компании');

            $table->string('signature', 500)
                ->nullable()
                ->comment('Путь к файлу подписи PNG');

            $table->string('stamp', 500)
                ->nullable()
                ->comment('Путь к файлу печати PNG');

            $table->string('country', 100)
                ->nullable()
                ->comment('Страна');

            $table->string('region', 100)
                ->nullable()
                ->comment('Регион или область');

            $table->string('city', 100)
                ->nullable()
                ->index('market_companies_city_idx')
                ->comment('Город');

            $table->string('legal_address', 500)
                ->nullable()
                ->comment('Юридический адрес компании');

            $table->string('actual_address', 500)
                ->nullable()
                ->comment('Фактический адрес компании');

            $table->decimal('latitude', 10, 7)
                ->nullable()
                ->comment('Широта');

            $table->decimal('longitude', 10, 7)
                ->nullable()
                ->comment('Долгота');

            $table->string('bank_name', 255)
                ->nullable()
                ->comment('Название банка');

            $table->string('bank_account', 100)
                ->nullable()
                ->comment('Банковский счёт №1 компании');

            $table->string('bank_account_secondary', 100)
                ->nullable()
                ->comment('Банковский счёт №2 компании');

            $table->string('bank_bik', 50)
                ->nullable()
                ->comment('БИК банка');

            $table->string('bank_iban', 100)
                ->nullable()
                ->comment('IBAN счёт');

            $table->boolean('vat_enabled')
                ->default(false)
                ->index('market_companies_vat_enabled_idx')
                ->comment('Использование НДС');

            $table->decimal('vat_rate', 5, 2)
                ->nullable()
                ->comment('Ставка НДС в процентах');

            $table->json('social_links')
                ->nullable()
                ->comment('Социальные сети компании в JSON');

            $table->unsignedInteger('sort')
                ->default(0)
                ->index('market_companies_sort_idx')
                ->comment('Сортировка (по возрастанию)');

            $table->boolean('activity')
                ->default(false)
                ->index('market_companies_activity_idx')
                ->comment('Активность/публикация компании (видимость)');

            $table->boolean('left')
                ->default(false)
                ->index('market_companies_left_idx')
                ->comment('Флаг: показывать в левом блоке');

            $table->boolean('main')
                ->default(false)
                ->index('market_companies_main_idx')
                ->comment('Флаг: показывать в главном блоке');

            $table->boolean('right')
                ->default(false)
                ->index('market_companies_right_idx')
                ->comment('Флаг: показывать в правом блоке');

            $table->string('status', 50)
                ->default('draft')
                ->index('market_companies_status_idx')
                ->comment('Статус публикации: draft, published, archived');

            $table->unsignedTinyInteger('moderation_status')
                ->default(0)
                ->index('market_companies_moderation_status_idx')
                ->comment('Модерация: 0=pending, 1=approved, 2=rejected');

            $table->unsignedBigInteger('moderated_by')
                ->nullable()
                ->index('market_companies_moderated_by_idx')
                ->comment('Кто промодерировал (users.id)');

            $table->foreign('moderated_by', 'market_companies_moderated_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->timestamp('moderated_at')
                ->nullable()
                ->index('market_companies_moderated_at_idx')
                ->comment('Когда промодерировано');

            $table->string('moderation_note', 500)
                ->nullable()
                ->comment('Комментарий модератора/причина отклонения');

            $table->date('published_at')
                ->nullable()
                ->index('market_companies_published_at_idx')
                ->comment('Дата публикации');

            $table->timestamp('show_from_at')
                ->nullable()
                ->index('market_companies_show_from_at_idx')
                ->comment('Показывать компанию начиная с');

            $table->timestamp('show_to_at')
                ->nullable()
                ->index('market_companies_show_to_at_idx')
                ->comment('Показывать компанию до');

            $table->unsignedBigInteger('views')
                ->default(0)
                ->index('market_companies_views_idx')
                ->comment('Счётчик просмотров');

            $table->timestamps();

            $table->unique(['user_id', 'url'], 'market_companies_user_url_unique');

            $table->index(
                ['user_id', 'activity', 'sort'],
                'market_companies_user_activity_sort_idx'
            );

            $table->index(
                ['activity', 'show_from_at', 'show_to_at'],
                'market_companies_activity_show_window_idx'
            );

            $table->index(
                ['moderation_status', 'status'],
                'market_companies_moderation_status_idx_status_idx'
            );

            $table->comment('Маркетплейс: компании. Основная таблица без переводов. Владелец = пользователь. Есть модерация, реквизиты и планирование показа.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_companies');
    }
};
