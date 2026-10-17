<?php

namespace App\Http\Resources\Admin\Slider\SliderSlideAdvantage;

use App\Models\Admin\Slider\SliderSlideAdvantage\SliderSlideAdvantageTranslation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderSlideAdvantageResource extends JsonResource
{
    /**
     * Полный ресурс самостоятельного преимущества для Create/Edit/Show
     * и административного конструктора слайдов.
     * Все связи загружаются контроллером; ресурс не выполняет SQL-запросов.
     */
    public function toArray(Request $request): array
    {
        $translation = $this->currentTranslation();
        $pivot = $this->resource->relationLoaded('pivot') ? $this->resource->pivot : null;

        return [
            'id' => (int) $this->id,

            // Глобальные свойства преимущества.
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            // Модерация самостоятельного преимущества.
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

            // Настройки иконки.
            'icon_type' => $this->icon_type,
            'icon' => $this->icon,
            'icon_color' => $this->icon_color,

            // Оформление и дополнительные настройки.
            'style' => $this->style,
            'settings' => $this->settings,

            // Локальное размещение в конкретном слайде.
            // pivot.sort не зависит от глобального sort преимущества.
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

            // Текущий перевод и все переводы для редактирования.
            'translation' => $translation
                ? new SliderSlideAdvantageTranslationResource($translation)
                : null,
            'translations' => SliderSlideAdvantageTranslationResource::collection(
                $this->whenLoaded('translations')
            ),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Текущая локаль → резервная → первый доступный перевод.
     * Используется только заранее загруженная коллекция translations.
     */
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
