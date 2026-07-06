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
        Schema::create('analytics_visitor_logs', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Пользователь
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('user_id')
                ->nullable()
                ->index('analytics_visitor_logs_user_id_idx')
                ->comment('ID авторизованного пользователя из основной базы');

            $table->string('session_id', 255)
                ->nullable()
                ->index('analytics_visitor_logs_session_id_idx')
                ->comment('ID сессии пользователя');

            $table->uuid('visitor_uuid')
                ->nullable()
                ->index('analytics_visitor_logs_visitor_uuid_idx')
                ->comment('Уникальный идентификатор посетителя');

            $table->string('user_gender', 30)
                ->nullable()
                ->index('analytics_visitor_logs_user_gender_idx')
                ->comment('Пол пользователя из профиля на момент импорта: male, female, unknown');

            $table->unsignedTinyInteger('user_age')
                ->nullable()
                ->index('analytics_visitor_logs_user_age_idx')
                ->comment('Возраст пользователя на момент посещения');

            $table->string('user_age_group', 30)
                ->nullable()
                ->index('analytics_visitor_logs_user_age_group_idx')
                ->comment('Возрастная группа пользователя: under_18, 18_24, 25_34, 35_44, 45_54, 55_plus');

            /*
            |--------------------------------------------------------------------------
            | Backend данные запроса
            |--------------------------------------------------------------------------
            */

            $table->string('ip_address', 45)
                ->nullable()
                ->index('analytics_visitor_logs_ip_address_idx')
                ->comment('IP-адрес пользователя IPv4 или IPv6');

            $table->text('user_agent')
                ->nullable()
                ->comment('Полная строка User-Agent');

            $table->string('method', 10)
                ->nullable()
                ->comment('HTTP метод');

            $table->string('url', 1000)
                ->nullable()
                ->comment('URL страницы');

            $table->string('page_title', 500)
                ->nullable()
                ->comment('Заголовок страницы');

            $table->string('route_name', 255)
                ->nullable()
                ->index('analytics_visitor_logs_route_name_idx')
                ->comment('Название маршрута');

            $table->string('module', 50)
                ->nullable()
                ->index('analytics_visitor_logs_module_idx')
                ->comment('Модуль платформы: blog, school, market, crm, chat, ai, admin, public');

            $table->string('entity_type', 100)
                ->nullable()
                ->index('analytics_visitor_logs_entity_type_idx')
                ->comment('Тип сущности: article, product, company, course, lesson, chat, message, agent');

            $table->unsignedBigInteger('entity_id')
                ->nullable()
                ->index('analytics_visitor_logs_entity_id_idx')
                ->comment('ID сущности из основной базы');

            $table->string('event_type', 50)
                ->default('page_view')
                ->index('analytics_visitor_logs_event_type_idx')
                ->comment('Тип события: page_view, click, search, chat_open, message_send, agent_action');

            $table->string('request_type', 30)
                ->nullable()
                ->index('analytics_visitor_logs_request_type_idx')
                ->comment('Тип запроса: web, api, ajax, agent');

            $table->unsignedSmallInteger('status_code')
                ->nullable()
                ->index('analytics_visitor_logs_status_code_idx')
                ->comment('HTTP статус ответа');

            $table->unsignedInteger('response_time')
                ->nullable()
                ->comment('Время выполнения запроса в миллисекундах');

            /*
            |--------------------------------------------------------------------------
            | География
            |--------------------------------------------------------------------------
            */

            $table->string('country', 100)
                ->nullable()
                ->index('analytics_visitor_logs_country_idx')
                ->comment('Страна пользователя');

            $table->string('region', 100)
                ->nullable()
                ->comment('Регион пользователя');

            $table->string('city', 100)
                ->nullable()
                ->index('analytics_visitor_logs_city_idx')
                ->comment('Город пользователя');

            /*
            |--------------------------------------------------------------------------
            | Устройство, браузер, система
            |--------------------------------------------------------------------------
            */

            $table->string('device_type', 30)
                ->nullable()
                ->index('analytics_visitor_logs_device_type_idx')
                ->comment('Тип устройства: desktop, mobile, tablet, bot');

            $table->string('device_name', 255)
                ->nullable()
                ->comment('Название устройства');

            $table->string('browser', 100)
                ->nullable()
                ->index('analytics_visitor_logs_browser_idx')
                ->comment('Название браузера');

            $table->string('browser_version', 50)
                ->nullable()
                ->comment('Версия браузера');

            $table->string('os', 100)
                ->nullable()
                ->index('analytics_visitor_logs_os_idx')
                ->comment('Операционная система');

            $table->string('os_version', 50)
                ->nullable()
                ->comment('Версия операционной системы');

            /*
            |--------------------------------------------------------------------------
            | Frontend данные устройства
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('screen_width')
                ->nullable()
                ->comment('Ширина экрана пользователя');

            $table->unsignedInteger('screen_height')
                ->nullable()
                ->comment('Высота экрана пользователя');

            $table->string('browser_language', 20)
                ->nullable()
                ->comment('Язык браузера пользователя');

            $table->string('timezone', 100)
                ->nullable()
                ->comment('Часовой пояс пользователя');

            /*
            |--------------------------------------------------------------------------
            | Источник посещения
            |--------------------------------------------------------------------------
            */

            $table->string('referer', 1000)
                ->nullable()
                ->comment('Источник перехода');

            $table->string('source_type', 50)
                ->nullable()
                ->index('analytics_visitor_logs_source_type_idx')
                ->comment('Тип источника: direct, search, social, internal, external');

            $table->string('search_engine', 100)
                ->nullable()
                ->comment('Поисковая система');

            /*
            |--------------------------------------------------------------------------
            | Поведение на странице
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('time_on_page')
                ->nullable()
                ->comment('Время на странице в секундах');

            $table->unsignedTinyInteger('scroll_depth')
                ->nullable()
                ->comment('Глубина прокрутки страницы в процентах');

            $table->unsignedInteger('clicks_count')
                ->default(0)
                ->comment('Количество кликов на странице');

            /*
            |--------------------------------------------------------------------------
            | Системные данные
            |--------------------------------------------------------------------------
            */

            $table->string('locale', 10)
                ->nullable()
                ->index('analytics_visitor_logs_locale_idx')
                ->comment('Язык сайта');

            $table->timestamp('visited_at')
                ->nullable()
                ->index('analytics_visitor_logs_visited_at_idx')
                ->comment('Дата и время посещения');

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Составные индексы
            |--------------------------------------------------------------------------
            */

            $table->index(
                ['user_id', 'visited_at'],
                'analytics_visitor_logs_user_visited_idx'
            );

            $table->index(
                ['session_id', 'visited_at'],
                'analytics_visitor_logs_session_visited_idx'
            );

            $table->index(
                ['visitor_uuid', 'visited_at'],
                'analytics_visitor_logs_visitor_visited_idx'
            );

            $table->index(
                ['route_name', 'visited_at'],
                'analytics_visitor_logs_route_visited_idx'
            );

            $table->index(
                ['country', 'city'],
                'analytics_visitor_logs_country_city_idx'
            );

            $table->index(
                ['module', 'visited_at'],
                'analytics_visitor_logs_module_visited_idx'
            );

            $table->index(
                ['event_type', 'visited_at'],
                'analytics_visitor_logs_event_visited_idx'
            );

            $table->index(
                ['source_type', 'visited_at'],
                'analytics_visitor_logs_source_visited_idx'
            );

            $table->comment(
                'Журнал аналитики посещений, источников трафика, устройств и поведения пользователей'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_visitor_logs');
    }
};
