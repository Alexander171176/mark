<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Таблица редиректов для CMS
    public function up(): void
    {
        Schema::create('redirects', function (Blueprint $t) {
            $t->id();

            // Относительный путь, с которого редиректим (например: "/old/page")
            // Рекомендуется хранить без домена и без query string.
            $t->string('from_path', 512);

            // Куда редиректим — может быть абсолютная или относительная ссылка.
            // Т.к. адрес может быть длинным, используем TEXT.
            $t->text('to_url');

            // HTTP код редиректа: 301 (постоянный) или 302 (временный)
            $t->unsignedSmallInteger('code')->default(301);

            // Сохранять ли query string исходного запроса и добавлять к to_url
            $t->boolean('preserve_query')->default(false);

            // Ограничение по локали (если используете mcamara/laravel-localization).
            // Если null — редирект работает для любой локали.
            $t->string('locale', 10)->nullable();

            // Вкл/выкл
            $t->boolean('activity')->default(true);

            // Счётчики/телеметрия
            $t->unsignedBigInteger('hits')->default(0);
            $t->timestamp('last_used_at')->nullable();

            // Служебная информация
            $t->text('notes')->nullable();
            $t->json('meta')->nullable();

            $t->timestamps();
            $t->softDeletes();

            // Один from_path на локаль — уникален (для любой локали допускаем дубликаты через NULL)
            $t->unique(['from_path', 'locale'], 'uniq_redirect_from_locale');

            // Частые фильтры
            $t->index(['activity', 'code'], 'idx_redirect_active_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('redirects');
    }
};
