<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id()->comment('PK');

            // user_id (nullable) + index + FK с явным именем
            $table->unsignedBigInteger('user_id')
                ->nullable()
                ->index('comments_user_id_idx')
                ->comment('Автор комментария (users.id), NULL если пользователь удалён/гость');

            // Полиморфная связь + явное имя индекса
            $table->unsignedBigInteger('commentable_id')
                ->comment('ID связанной сущности (polymorphic)');
            $table->string('commentable_type')
                ->comment('Тип связанной сущности (polymorphic)');
            $table->index(['commentable_type', 'commentable_id'], 'comments_commentable_idx');

            // parent_id (nullable) + index + FK с явным именем
            $table->unsignedBigInteger('parent_id')
                ->nullable()
                ->index('comments_parent_id_idx')
                ->comment('Родительский комментарий (для дерева)');

            $table->text('content')->comment('Текст комментария');

            $table->boolean('activity')
                ->default(true)
                ->index('comments_activity_idx')
                ->comment('Активность комментария (видимость/скрыт)');

            // moderation_status
            $table->unsignedTinyInteger('moderation_status')
                ->default(0)
                ->index('comments_moderation_status_idx')
                ->comment('Модерация: 0=pending, 1=approved, 2=rejected');

            // moderated_by (nullable) + index + FK с явным именем
            $table->unsignedBigInteger('moderated_by')
                ->nullable()
                ->index('comments_moderated_by_idx')
                ->comment('Кто промодерировал (users.id)');

            $table->timestamp('moderated_at')
                ->nullable()
                ->index('comments_moderated_at_idx')
                ->comment('Когда промодерировано');

            $table->string('moderation_note', 500)
                ->nullable()
                ->comment('Комментарий модератора/причина отклонения');

            $table->timestamps();

            // --- FK constraints (явные имена) ---

            $table->foreign('user_id', 'comments_user_id_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->foreign('parent_id', 'comments_parent_id_fk')
                ->references('id')
                ->on('comments')
                ->cascadeOnDelete();

            $table->foreign('moderated_by', 'comments_moderated_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->comment('Полиморфные комментарии с древовидной структурой и модерацией.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
