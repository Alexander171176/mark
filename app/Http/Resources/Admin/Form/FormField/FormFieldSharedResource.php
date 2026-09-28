<?php

namespace App\Http\Resources\Admin\Form\FormField;

use App\Http\Resources\Admin\Form\Form\FormSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormFieldSharedResource extends JsonResource
{
    /**
     * Компактное представление поля формы.
     *
     * Основное назначение:
     * - Admin Index;
     * - связанные сущности;
     * - значения заявок;
     * - справочные списки.
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
            'form_id' => $this->form_id,

            /** Основные данные */
            'name' => $this->name,
            'type' => $this->type,

            /** Отображение / сортировка / активность */
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            /** Состояние */
            'required' => (bool) $this->required,
            'readonly' => (bool) $this->readonly,
            'disabled' => (bool) $this->disabled,

            /** Отображение */
            'width' => $this->width,

            /** Счётчики */
            'options_count' => $this->when(
                isset($this->options_count),
                fn () => (int) $this->options_count
            ),

            /** Перевод текущей локали с fallback */
            'translation' => $translation
                ? new FormFieldTranslationResource(
                    $translation
                )
                : null,

            /** Родительская форма */
            'form' => $this->whenLoaded(
                'form',
                function () {
                    return $this->form
                        ? new FormSharedResource(
                            $this->form
                        )
                        : null;
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
