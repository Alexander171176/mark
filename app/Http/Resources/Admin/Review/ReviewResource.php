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

            'replied_by' => $this->replied_by !== null
                ? (int) $this->replied_by
                : null,

            'replied_at' => $this->replied_at?->toISOString(),
            'has_reply' => filled($this->reply),

            /** Модерация */
            'moderation_status' => (int) $this->moderation_status,

            'is_pending' => (int) $this->moderation_status === 0,
            'is_approved' => (int) $this->moderation_status === 1,
            'is_rejected' => (int) $this->moderation_status === 2,

            'moderated_by' => $this->moderated_by !== null
                ? (int) $this->moderated_by
                : null,

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
            'author' => $this->whenLoaded(
                'author',
                fn () => $this->userPayload($this->author)
            ),

            /** Пользователь, оставивший ответ */
            'replier' => $this->whenLoaded(
                'replier',
                fn () => $this->userPayload($this->replier)
            ),

            /** Модератор */
            'moderator' => $this->whenLoaded(
                'moderator',
                fn () => $this->userPayload($this->moderator)
            ),

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
     * Ресурс не выполняет запросы самостоятельно.
     * Для переводимых сущностей Controller должен заранее
     * загрузить translations только для currentLocale.
     *
     * @return array<string, mixed>|null
     */
    protected function reviewablePayload(): ?array
    {
        $reviewable = $this->reviewable;

        if (! $reviewable) {
            return null;
        }

        $translation = $this->reviewableTranslation(
            $reviewable
        );

        return [
            /** Основные идентификаторы */
            'id' => (int) $reviewable->getKey(),

            /** Алиас из morphMap */
            'type' => $this->reviewable_type,

            /** PHP-класс нужен административному интерфейсу */
            'class' => $reviewable::class,

            /** Универсальные поля сущности */
            'url' => $reviewable->url ?? null,
            'slug' => $reviewable->slug ?? null,

            'sku' => $reviewable->sku ?? null,
            'vendor_code' => $reviewable->vendor_code ?? null,
            'barcode' => $reviewable->barcode ?? null,
            'code' => $reviewable->code ?? null,

            /** Данные текущего перевода */
            'title' => $translation?->title
                ?? $translation?->name
                    ?? $this->reviewableFallbackTitle($reviewable),

            'subtitle' => $translation?->subtitle ?? null,
            'short' => $translation?->short ?? null,
            'description' => $translation?->description ?? null,

            /** Состояние сущности */
            'activity' => isset($reviewable->activity)
                ? (bool) $reviewable->activity
                : null,

            'status' => $reviewable->status ?? null,
        ];
    }

    /**
     * Получить уже загруженный перевод reviewable-сущности.
     *
     * Controller загружает translations только для currentLocale,
     * поэтому первый элемент коллекции является текущим переводом.
     *
     * Метод не инициирует SQL-запросы.
     */
    protected function reviewableTranslation(
        object $reviewable
    ): ?object {
        if (! $reviewable->relationLoaded('translations')) {
            return null;
        }

        return $reviewable->translations->first();
    }

    /**
     * Fallback-название для сущностей без системы переводов
     * или при отсутствии перевода текущей локали.
     */
    protected function reviewableFallbackTitle(
        object $reviewable
    ): ?string {
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
