<?php

namespace App\Http\Resources\Admin\Slider\SliderSlideAction;

use App\Models\Admin\Slider\SliderSlideAction\SliderSlideActionTranslation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderSlideActionResource extends JsonResource
{
    /** Полное представление кнопки для Show, Edit и конструктора. */
    public function toArray(Request $request): array
    {
        $translation = $this->currentTranslation();
        $pivot = $this->resource->relationLoaded('pivot') ? $this->resource->pivot : null;

        return [
            'id' => (int) $this->id,
            'activity' => (bool) $this->activity,
            'sort' => (int) $this->sort,

            // Глобальная модерация кнопки.
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

            // Тип и назначение действия.
            'action_type' => $this->action_type,
            'action_value' => $this->action_value,
            'route_params' => $this->route_params,
            'target' => $this->target,

            // Внешний вид кнопки.
            'style' => $this->style,
            'icon' => $this->icon,
            'icon_position' => $this->icon_position,
            'css_class' => $this->css_class,
            'settings' => $this->settings,

            // Локальный порядок и настройки размещения в слайде.
            'pivot' => $pivot ? [
                'id' => $pivot->id !== null ? (int) $pivot->id : null,
                'slider_slide_id' => (int) $pivot->slider_slide_id,
                'slider_slide_action_id' => (int) $pivot->slider_slide_action_id,
                'sort' => (int) $pivot->sort,
                'activity' => (bool) $pivot->activity,
                'is_primary' => (bool) $pivot->is_primary,
                'created_at' => $pivot->created_at?->toISOString(),
                'updated_at' => $pivot->updated_at?->toISOString(),
            ] : null,

            'slides_count' => $this->whenCounted('slides'),

            // Собственные переводы для Edit: контроллер загружает все локали.
            'translation' => $translation ? new SliderSlideActionTranslationResource($translation) : null,
            'translations' => SliderSlideActionTranslationResource::collection($this->whenLoaded('translations')),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /** Только заранее загруженные переводы; SQL-запросов нет. */
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
