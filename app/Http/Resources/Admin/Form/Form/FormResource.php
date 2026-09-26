<?php

namespace App\Http\Resources\Admin\Form\Form;

use App\Http\Resources\Admin\Form\FormField\FormFieldResource;
use App\Http\Resources\Admin\System\User\UserSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormResource extends JsonResource
{
    /**
     * Полное административное представление формы.
     *
     * Основное назначение:
     * - Edit;
     * - Show;
     * - административный конструктор формы.
     *
     * Resource читает только заранее загруженные
     * relations/counts и не должен создавать
     * дополнительные SQL-запросы.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $translation = $this->loadedTranslationOrFallback();

        return [
            /** Основные данные */
            'id' => (int) $this->id,

            'user_id' => $this->user_id !== null
                ? (int) $this->user_id
                : null,

            'code' => $this->code,
            'status' => $this->status,

            'activity' => (bool) $this->activity,
            'sort' => (int) $this->sort,

            /** Владелец */
            'owner' => new UserSharedResource(
                $this->whenLoaded('user')
            ),

            /** Текущий перевод с fallback */
            'translation' => $translation
                ? new FormTranslationResource($translation)
                : null,

            /** Все переводы */
            'translations' => FormTranslationResource::collection(
                $this->whenLoaded('translations')
            ),

            /** Защита от спама */
            'spam_protection' => (bool) $this->spam_protection,
            'honeypot_enabled' => (bool) $this->honeypot_enabled,

            'min_submit_seconds' => (int) $this->min_submit_seconds,

            'rate_limit' => (int) $this->rate_limit,
            'rate_limit_minutes' => (int) $this->rate_limit_minutes,

            'captcha_enabled' => (bool) $this->captcha_enabled,

            /** Поведение формы */
            'auth_required' => (bool) $this->auth_required,

            /** Дополнительные настройки */
            'settings' => $this->settings,

            /** Поля формы */
            'fields' => FormFieldResource::collection(
                $this->whenLoaded('fields')
            ),

            /** Счётчики */
            'fields_count' => $this->whenCounted('fields'),
            'submissions_count' => $this->whenCounted('submissions'),

            /** Даты */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
