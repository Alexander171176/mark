<?php

namespace App\Http\Resources\Admin\Form\Form;

use App\Http\Resources\Admin\Form\FormField\FormFieldResource;
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
     * Resource работает только с заранее
     * загруженными relations/counts и не должен
     * выполнять дополнительные SQL-запросы.
     */
    public function toArray(Request $request): array
    {
        $translation = $this->currentTranslation();

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,

            /** Основные данные */
            'code' => $this->code,

            /** Отображение / сортировка / активность */
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            /** Статус */
            'status' => $this->status,

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

            /** Счётчики */
            'fields_count' => $this->whenCounted(
                'fields'
            ),

            'submissions_count' => $this->whenCounted(
                'submissions'
            ),

            /** Текущий перевод */
            'translation' => $translation
                ? new FormTranslationResource(
                    $translation
                )
                : null,

            /** Все переводы */
            'translations' => FormTranslationResource::collection(
                $this->whenLoaded('translations')
            ),

            /** Поля формы */
            'fields' => FormFieldResource::collection(
                $this->whenLoaded('fields')
            ),

            /** Владелец */
            'owner' => $this->whenLoaded(
                'user',
                function () {
                    return [
                        'id' => $this->user?->id,
                        'name' => $this->user?->name,
                        'email' => $this->user?->email,
                        'profile_photo_url' => $this->user?->profile_photo_url,
                    ];
                }
            ),

            /** Timestamps */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Получение текущего перевода
     * только из уже загруженной relation translations.
     *
     * Для полного Resource Controller загружает
     * все переводы формы.
     *
     * Порядок:
     * current → fallback → первый доступный.
     *
     * Дополнительные SQL-запросы
     * здесь не выполняются.
     */
    private function currentTranslation()
    {
        return $this->loadedTranslationOrFallback();
    }
}
