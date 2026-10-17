<?php

namespace App\Http\Resources\Admin\Slider\SliderSlideAdvantage;

use App\Models\Admin\Slider\SliderSlideAdvantage\SliderSlideAdvantageTranslation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderSlideAdvantageSharedResource extends JsonResource
{
    /**
     * Компактный ресурс для Index.vue, справочников выбора
     * и списков подключённых к слайду преимуществ.
     * Не выполняет SQL-запросов и не возвращает translations[].
     */
    public function toArray(Request $request): array
    {
        $translation = $this->currentTranslation();
        $pivot = $this->resource->relationLoaded('pivot') ? $this->resource->pivot : null;

        return [
            'id' => (int) $this->id,

            // Глобальная сортировка и активность.
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            // Модерация для административного списка.
            'moderation_status' => (int) $this->moderation_status,
            'is_pending' => (int) $this->moderation_status === 0,
            'is_approved' => (int) $this->moderation_status === 1,
            'is_rejected' => (int) $this->moderation_status === 2,
            'moderated_by' => $this->moderated_by !== null ? (int) $this->moderated_by : null,
            'moderated_at' => $this->moderated_at?->toISOString(),
            'moderation_note' => $this->moderation_note,
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

            // Данные для таблицы и предпросмотра.
            'icon_type' => $this->icon_type,
            'icon' => $this->icon,
            'icon_color' => $this->icon_color,
            'style' => $this->style,

            // Параметры конкретного размещения в слайде.
            'pivot' => $pivot ? [
                'id' => $pivot->id !== null ? (int) $pivot->id : null,
                'slider_slide_id' => (int) $pivot->slider_slide_id,
                'slider_slide_advantage_id' => (int) $pivot->slider_slide_advantage_id,
                'sort' => (int) $pivot->sort,
                'activity' => (bool) $pivot->activity,
                'created_at' => $pivot->created_at?->toISOString(),
                'updated_at' => $pivot->updated_at?->toISOString(),
            ] : null,

            'slides_count' => $this->whenCounted('slides'),

            // Один перевод из предварительно загруженной коллекции.
            'translation' => $translation
                ? new SliderSlideAdvantageTranslationResource($translation)
                : null,

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /** Выбор перевода без дополнительных SQL-запросов. */
    private function currentTranslation(): ?SliderSlideAdvantageTranslation
    {
        if (! $this->resource->relationLoaded('translations')) {
            return null;
        }

        $locale = app()->getLocale();
        $fallback = config('app.fallback_locale', 'ru');
        $translations = $this->resource->translations;

        return $translations->firstWhere('locale', $locale)
            ?: $translations->firstWhere('locale', $fallback)
                ?: $translations->first();
    }
}
