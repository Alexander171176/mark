<?php

namespace App\Http\Resources\Admin\Review;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    /**
     * Преобразование универсального отзыва в массив.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /** Основные идентификаторы */
            'id' => (int) $this->id,

            'reviewable_type' => $this->reviewable_type,
            'reviewable_id' => (int) $this->reviewable_id,

            'user_id' => (int) $this->user_id,

            /** Оценка и содержимое отзыва */
            'rating' => (int) $this->rating,

            'advantages' => $this->advantages,
            'disadvantages' => $this->disadvantages,
            'comment' => $this->comment,

            /** Подтверждённый опыт */
            'verified' => (bool) $this->verified,
            'is_verified' => (bool) $this->verified,

            /** Ответ владельца сущности или администратора */
            'reply' => $this->reply,
            'replied_by' => $this->replied_by,
            'replied_at' => $this->replied_at?->toISOString(),
            'has_reply' => filled($this->reply),

            /** Модерация */
            'moderation_status' => (int) $this->moderation_status,

            'is_pending' => (int) $this->moderation_status === 0,
            'is_approved' => (int) $this->moderation_status === 1,
            'is_rejected' => (int) $this->moderation_status === 2,

            'moderated_by' => $this->moderated_by,
            'moderated_at' => $this->moderated_at?->toISOString(),
            'moderation_note' => $this->moderation_note,

            /** Полезность и активность */
            'likes' => (int) $this->likes,

            'activity' => (bool) $this->activity,
            'is_active' => (bool) $this->activity,

            /** Количество изображений */
            'images_count' => $this->whenCounted('images'),

            /** Полиморфная сущность отзыва */
            'reviewable' => $this->whenLoaded(
                'reviewable',
                fn () => $this->reviewablePayload()
            ),

            /** Автор отзыва */
            'author' => $this->whenLoaded('author', function () {
                return $this->userPayload($this->author);
            }),

            /** Пользователь, оставивший ответ */
            'replier' => $this->whenLoaded('replier', function () {
                return $this->userPayload($this->replier);
            }),

            /** Модератор */
            'moderator' => $this->whenLoaded('moderator', function () {
                return $this->userPayload($this->moderator);
            }),

            /** Изображения отзыва */
            'images' => ReviewImageResource::collection(
                $this->whenLoaded('images')
            ),

            /** Даты */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Компактные данные полиморфной сущности.
     *
     * Ресурс не зависит от конкретного типа:
     * товара, комплекта, курса или другой сущности.
     *
     * @return array<string, mixed>|null
     */
    protected function reviewablePayload(): ?array
    {
        $reviewable = $this->reviewable;

        if (! $reviewable) {
            return null;
        }

        return [
            'id' => (int) $reviewable->getKey(),

            /** Алиас из morphMap */
            'type' => $this->reviewable_type,

            /** PHP-класс нужен только административному интерфейсу */
            'class' => $reviewable::class,

            /** Универсальные поля, если они существуют у модели */
            'url' => $reviewable->url ?? null,
            'slug' => $reviewable->slug ?? null,
            'sku' => $reviewable->sku ?? null,
            'code' => $reviewable->code ?? null,

            /** Отображаемое название сущности */
            'title' => $this->reviewableTitle($reviewable),

            /** Состояние сущности, если поля существуют */
            'activity' => isset($reviewable->activity)
                ? (bool) $reviewable->activity
                : null,

            'status' => $reviewable->status ?? null,
        ];
    }

    /**
     * Получить отображаемое название сущности.
     */
    protected function reviewableTitle(object $reviewable): ?string
    {
        /*
         * Если у сущности есть специальный helper,
         * используем его первым.
         */
        if (method_exists($reviewable, 'getTranslatedTitle')) {
            return $reviewable->getTranslatedTitle();
        }

        /*
         * Текущий перевод был загружен отдельной связью.
         */
        if (
            $reviewable->relationLoaded('translation')
            && $reviewable->translation
        ) {
            return $reviewable->translation->title;
        }

        /*
         * Все переводы были загружены коллекцией.
         */
        if ($reviewable->relationLoaded('translations')) {
            $translations = $reviewable->translations;

            $translation = $translations->firstWhere(
                'locale',
                app()->getLocale()
            )
                ?: $translations->firstWhere(
                    'locale',
                    config('app.fallback_locale', 'ru')
                )
                    ?: $translations->first();

            if ($translation) {
                return $translation->title;
            }
        }

        /*
         * Для сущностей без системы переводов.
         */
        return $reviewable->title
            ?? $reviewable->name
            ?? $reviewable->legal_name
            ?? $reviewable->url
            ?? null;
    }

    /**
     * Компактные данные пользователя.
     *
     * @return array<string, mixed>|null
     */
    protected function userPayload(?object $user): ?array
    {
        if (! $user) {
            return null;
        }

        return [
            'id' => (int) $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'profile_photo_url' => $user->profile_photo_url,
        ];
    }
}
