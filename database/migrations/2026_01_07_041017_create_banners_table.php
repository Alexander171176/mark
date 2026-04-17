<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id()->comment('PK');

            // creator/owner
            $table->unsignedBigInteger('user_id')
                ->index('banners_user_id_idx')
                ->comment('Создатель/владелец баннера (users.id)');

            $table->unsignedInteger('sort')
                ->default(0)
                ->index('banners_sort_idx')
                ->comment('Сортировка (по возрастанию)');

            $table->boolean('activity')
                ->default(false)
                ->index('banners_activity_idx')
                ->comment('Активность баннера (показывать/не показывать)');

            $table->boolean('left')->default(false)->index('banners_left_idx')->comment('Позиция: левый блок');
            $table->boolean('main')->default(false)->index('banners_main_idx')->comment('Позиция: главный блок');
            $table->boolean('right')->default(false)->index('banners_right_idx')->comment('Позиция: правый блок');

            $table->string('locale', 10)
                ->index('banners_locale_idx')
                ->comment('Локаль баннера (например: ru, kk, en)');

            /**
             * Модерация:
             * 0 = pending, 1 = approved, 2 = rejected
             */
            $table->unsignedTinyInteger('moderation_status')
                ->default(0)
                ->index('banners_moderation_status_idx')
                ->comment('Модерация: 0=pending, 1=approved, 2=rejected');

            $table->unsignedBigInteger('moderated_by')
                ->nullable()
                ->index('banners_moderated_by_idx')
                ->comment('Кто промодерировал (users.id)');

            $table->timestamp('moderated_at')
                ->nullable()
                ->index('banners_moderated_at_idx')
                ->comment('Когда промодерировано');

            $table->string('moderation_note', 500)
                ->nullable()
                ->comment('Комментарий модератора/причина отклонения');

            $table->string('title', 255)
                ->comment('Название/заголовок баннера');

            $table->text('link')
                ->nullable()
                ->comment('Ссылка (URL) куда ведёт баннер');

            $table->string('short', 255)
                ->nullable()
                ->comment('Короткий текст (анонс)');

            $table->string('comment', 255)
                ->nullable()
                ->comment('Примечание/служебный комментарий');

            $table->timestamps();

            // Частые выборки для выдачи баннеров по локали и позиции
            $table->index(['locale', 'activity', 'sort'], 'banners_locale_activity_sort_idx');

            // --- FK (явные имена) ---
            $table->foreign('user_id', 'banners_user_id_fk')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('moderated_by', 'banners_moderated_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->comment('Рекламные баннеры по локали с модерацией и позиционированием.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
