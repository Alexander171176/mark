<?php

namespace App\Http\Resources\Admin\Slider\Slider;

use App\Http\Resources\Admin\Slider\SliderSlide\SliderSlideResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Полное представление слайдера для Edit, Show и конструктора. */
class SliderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Используем только загруженные переводы; SQL-запросов из ресурса нет.
        $translation = $this->currentTranslation();

        return [
            'id' => (int) $this->id,
            'user_id' => $this->user_id !== null ? (int) $this->user_id : null,

            /** Основные параметры */
            'code' => $this->code,
            'type' => $this->type,
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,
            'status' => $this->status,

            /** Модерация */
            'moderation_status' => (int) $this->moderation_status,
            'is_pending' => (int) $this->moderation_status === 0,
            'is_approved' => (int) $this->moderation_status === 1,
            'is_rejected' => (int) $this->moderation_status === 2,
            'moderated_by' => $this->moderated_by !== null ? (int) $this->moderated_by : null,
            'moderated_at' => $this->moderated_at?->toISOString(),
            'moderation_note' => $this->moderation_note,

            /** Публикация */
            'published_at' => $this->published_at?->format('Y-m-d\TH:i'),
            'show_from_at' => $this->show_from_at?->format('Y-m-d\TH:i'),
            'show_to_at' => $this->show_to_at?->format('Y-m-d\TH:i'),

            /** Настройки Swiper */
            'autoplay_delay' => $this->autoplay_delay !== null ? (int) $this->autoplay_delay : null,
            'autoplay_disable_on_interaction' => (bool) $this->autoplay_disable_on_interaction,
            'pause_on_hover' => (bool) $this->pause_on_hover,
            'effect' => $this->effect,
            'speed' => (int) $this->speed,
            'effect_options' => $this->effect_options,
            'loop' => (bool) $this->loop,
            'keyboard' => (bool) $this->keyboard,
            'allow_touch_move' => (bool) $this->allow_touch_move,
            'grab_cursor' => (bool) $this->grab_cursor,
            'slides_per_view' => $this->slides_per_view !== null ? (float) $this->slides_per_view : null,
            'space_between' => (int) $this->space_between,
            'auto_height' => (bool) $this->auto_height,
            'breakpoints' => $this->breakpoints,
            'show_navigation' => (bool) $this->show_navigation,
            'show_pagination' => (bool) $this->show_pagination,
            'settings' => $this->settings,

            /** Счётчики, только если переданы контроллером */
            'slides_count' => $this->whenCounted('slides'),

            /** Переводы: текущий и все языки для редактирования */
            'translation' => $translation ? new SliderTranslationResource($translation) : null,
            'translations' => SliderTranslationResource::collection($this->whenLoaded('translations')),

            /** Владелец */
            'owner' => $this->whenLoaded('owner', function () {
                if (! $this->owner) {
                    return null;
                }

                return [
                    'id' => (int) $this->owner->id,
                    'name' => $this->owner->name,
                    'email' => $this->owner->email,
                    'profile_photo_url' => $this->owner->profile_photo_url,
                ];
            }),

            /** Модератор */
            'moderator' => $this->whenLoaded('moderator', function () {
                if (! $this->moderator) {
                    return null;
                }

                return [
                    'id' => (int) $this->moderator->id,
                    'name' => $this->moderator->name,
                    'email' => $this->moderator->email,
                ];
            }),

            /** Слайды со своими локальными pivot-настройками */
            'slides' => SliderSlideResource::collection($this->whenLoaded('slides')),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /** Текущая локаль → fallback → первый доступный перевод. */
    private function currentTranslation(): ?object
    {
        if (! $this->resource->relationLoaded('translations')) {
            return null;
        }

        $locale = app()->getLocale();
        $fallback = config('app.fallback_locale', 'ru');
        $translations = $this->resource->getRelation('translations');

        return $translations->firstWhere('locale', $locale)
            ?: $translations->firstWhere('locale', $fallback)
                ?: $translations->first();
    }
}
