<?php

namespace App\Http\Resources\Admin\Slider\SliderSlideAction;

use App\Models\Admin\Slider\SliderSlideAction\SliderSlideActionTranslation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Компактная кнопка для Index, выбора и связанных списков. */
class SliderSlideActionSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translation = $this->currentTranslation();
        $pivot = $this->resource->relationLoaded('pivot') ? $this->resource->pivot : null;

        return [
            'id' => (int) $this->id,
            'activity' => (bool) $this->activity,
            'sort' => (int) $this->sort,

            // Глобальная модерация.
            'moderation_status' => (int) $this->moderation_status,
            'is_pending' => $this->resource->isPendingModeration(),
            'is_approved' => $this->resource->isApproved(),
            'is_rejected' => $this->resource->isRejected(),
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

            'action_type' => $this->action_type,
            'action_value' => $this->action_value,
            'target' => $this->target,
            'style' => $this->style,
            'icon' => $this->icon,
            'icon_position' => $this->icon_position,
            'css_class' => $this->css_class,

            // Локальные свойства размещения, не глобальные поля кнопки.
            'pivot' => $pivot ? [
                'id' => $pivot->id !== null ? (int) $pivot->id : null,
                'slider_slide_id' => (int) $pivot->slider_slide_id,
                'slider_slide_action_id' => (int) $pivot->slider_slide_action_id,
                'sort' => (int) $pivot->sort,
                'activity' => (bool) $pivot->activity,
                'is_primary' => (bool) $pivot->is_primary,
            ] : null,

            'slides_count' => $this->whenCounted('slides'),
            'translation' => $translation ? new SliderSlideActionTranslationResource($translation) : null,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /** Текущая локаль → fallback → первый загруженный перевод, без SQL. */
    private function currentTranslation(): ?SliderSlideActionTranslation
    {
        if (! $this->resource->relationLoaded('translations')) {
            return null;
        }

        $locale = app()->getLocale();
        $fallback = config('app.fallback_locale', 'ru');

        return $this->resource->translations->firstWhere('locale', $locale)
            ?: $this->resource->translations->firstWhere('locale', $fallback)
                ?: $this->resource->translations->first();
    }
}
