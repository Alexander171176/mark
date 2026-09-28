<?php

namespace App\Http\Resources\Admin\Form\Form;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormSharedResource extends JsonResource
{
    /**
     * Компактное представление формы.
     *
     * Основное назначение:
     * - Admin Index;
     * - select/справочные списки;
     * - связанные сущности;
     * - заявки формы.
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

            /** Счётчики */
            'fields_count' => $this->when(
                isset($this->fields_count),
                fn () => (int) $this->fields_count
            ),

            'submissions_count' => $this->when(
                isset($this->submissions_count),
                fn () => (int) $this->submissions_count
            ),

            /** Перевод текущей локали с fallback */
            'translation' => $translation
                ? new FormTranslationResource(
                    $translation
                )
                : null,

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
     * Получение перевода только из уже
     * загруженной relation translations.
     *
     * Для Admin Index Controller загружает:
     * - текущую локаль;
     * - fallback локаль.
     *
     * Дополнительные SQL-запросы
     * здесь не выполняются.
     */
    private function currentTranslation()
    {
        return $this->loadedTranslationOrFallback();
    }
}
