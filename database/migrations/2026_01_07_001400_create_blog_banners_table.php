<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_banners', function (Blueprint $table) {
            $table->id()->comment('PK');

            /**
             * Создатель / владелец
             */
            $table->unsignedBigInteger('user_id')
                ->index('blog_banners_user_id_idx')
                ->comment('Создатель/владелец баннера (users.id)');

            /**
             * Отображение / сортировка / позиции
             */
            $table->unsignedInteger('sort')
                ->default(0)
                ->index('blog_banners_sort_idx')
                ->comment('Сортировка (по возрастанию)');

            $table->boolean('activity')
                ->default(false)
                ->index('blog_banners_activity_idx')
                ->comment('Активность баннера (показывать/не показывать)');

            $table->boolean('left')
                ->default(false)
                ->index('blog_banners_left_idx')
                ->comment('Позиция: левый блок');

            $table->boolean('main')
                ->default(false)
                ->index('blog_banners_main_idx')
                ->comment('Позиция: главный блок');

            $table->boolean('right')
                ->default(false)
                ->index('blog_banners_right_idx')
                ->comment('Позиция: правый блок');

            /**
             * Модерация
             * 0 = pending, 1 = approved, 2 = rejected
             */
            $table->unsignedTinyInteger('moderation_status')
                ->default(0)
                ->index('blog_banners_moderation_status_idx')
                ->comment('Модерация: 0=pending, 1=approved, 2=rejected');

            $table->unsignedBigInteger('moderated_by')
                ->nullable()
                ->index('blog_banners_moderated_by_idx')
                ->comment('Кто промодерировал (users.id)');

            $table->timestamp('moderated_at')
                ->nullable()
                ->index('blog_banners_moderated_at_idx')
                ->comment('Когда промодерировано');

            $table->string('moderation_note', 500)
                ->nullable()
                ->comment('Комментарий модератора/причина отклонения');

            /**
             * Служебное поле
             */
            $table->string('comment', 255)
                ->nullable()
                ->comment('Примечание/служебный комментарий');

            $table->timestamps();

            /**
             * Частые выборки по активности и позициям
             */
            $table->index(
                ['activity', 'sort'],
                'blog_banners_activity_sort_idx'
            );

            /**
             * FK
             */
            $table->foreign('user_id', 'blog_banners_user_id_fk')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('moderated_by', 'blog_banners_moderated_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->comment('Блог: баннеры. Основная таблица без переводов, с модерацией и позиционированием.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_banners');
    }
};
