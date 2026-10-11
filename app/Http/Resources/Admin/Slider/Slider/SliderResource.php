<?php

namespace App\Http\Resources\Admin\Slider\Slider;

use App\Http\Resources\Admin\Slider\SliderSlide\SliderSlideResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderResource extends JsonResource
{
    /**
     * Преобразование полного ресурса слайдера.
     */
    public function toArray(Request $request): array
    {
        $translation = $this->currentTranslation();

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,

            /** Основные данные */
            'code' => $this->code,
            'type' => $this->type,

            /** Отображение и сортировка */
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            /** Статус публикации */
            'status' => $this->status,

            /** Модерация */
            'moderation_status' => (int) $this->moderation_status,

            'is_pending' => (int) $this->moderation_status === 0,
            'is_approved' => (int) $this->moderation_status === 1,
            'is_rejected' => (int) $this->moderation_status === 2,

            /** Публикация и период отображения */
            'published_at' => $this->published_at?->format('Y-m-d\TH:i'),
            'show_from_at' => $this->show_from_at?->format('Y-m-d\TH:i'),
            'show_to_at' => $this->show_to_at?->format('Y-m-d\TH:i'),

            /** Автоматическое переключение */
            'autoplay_delay' => $this->autoplay_delay !== null
                ? (int) $this->autoplay_delay
                : null,

            'autoplay_disable_on_interaction' =>
                (bool) $this->autoplay_disable_on_interaction,

            'pause_on_hover' => (bool) $this->pause_on_hover,

            /** Эффекты переключения */
            'effect' => $this->effect,
            'speed' => (int) $this->speed,
            'effect_options' => $this->effect_options,

            /** Управление слайдером */
            'loop' => (bool) $this->loop,
            'keyboard' => (bool) $this->keyboard,
            'allow_touch_move' => (bool) $this->allow_touch_move,
            'grab_cursor' => (bool) $this->grab_cursor,

            /** Размеры и расположение */
            'slides_per_view' => (float) $this->slides_per_view,
            'space_between' => (int) $this->space_between,
            'auto_height' => (bool) $this->auto_height,
            'breakpoints' => $this->breakpoints,

            /** Элементы навигации */
            'show_navigation' => (bool) $this->show_navigation,
            'show_pagination' => (bool) $this->show_pagination,

            /** Дополнительные настройки */
            'settings' => $this->settings,

            /** Счётчики */
            'slides_count' => $this->whenCounted('slides'),

            /** Текущий перевод */
            'translation' => $translation
                ? new SliderTranslationResource($translation)
                : null,

            /** Все переводы */
            'translations' => SliderTranslationResource::collection(
                $this->whenLoaded('translations')
            ),

            /** Владелец */
            'owner' => $this->whenLoaded('owner', function () {
                return [
                    'id' => $this->owner?->id,
                    'name' => $this->owner?->name,
                    'email' => $this->owner?->email,
                    'profile_photo_url' => $this->owner?->profile_photo_url,
                ];
            }),

            /** Слайды */
            'slides' => SliderSlideResource::collection(
                $this->whenLoaded('slides')
            ),

            /** Системные даты */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Текущий перевод из загруженной коллекции.
     *
     * Приоритет:
     * 1. Текущий язык.
     * 2. Резервный язык.
     * 3. Первый доступный перевод.
     *
     * Дополнительные SQL-запросы не выполняются.
     */
    private function currentTranslation(): ?object
    {
        if (! $this->relationLoaded('translations')) {
            return null;
        }

        $locale = app()->getLocale();

        $fallback = config('app.fallback_locale', 'ru');

        return $this->translations->firstWhere('locale', $locale)
            ?: $this->translations->firstWhere('locale', $fallback)
                ?: $this->translations->first();
    }
}
