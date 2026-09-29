<?php

namespace App\Http\Resources\Admin\Form\FormField;

use App\Http\Resources\Admin\Form\Form\FormSharedResource;
use App\Http\Resources\Admin\Form\FormFieldOption\FormFieldOptionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormFieldResource extends JsonResource
{
    /**
     * Полное административное представление поля формы.
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

            /** Значение по умолчанию */
            'default_value' => $this->default_value,

            /** Валидация */
            'validation' => $this->validation,

            /** Настройки типа поля */
            'settings' => $this->settings,

            /** Возможности типа поля */
            'supports_options' => $this->supportsOptions(),
            'supports_multiple' => $this->supportsMultiple(),

            /** Счётчики */
            'options_count' => $this->when(
                isset($this->options_count),
                fn () => (int) $this->options_count
            ),

            'submission_values_count' => $this->when(
                isset($this->submission_values_count),
                fn () => (int) $this->submission_values_count
            ),

            'submission_files_count' => $this->when(
                isset($this->submission_files_count),
                fn () => (int) $this->submission_files_count
            ),

            /** Текущий перевод */
            'translation' => $translation
                ? new FormFieldTranslationResource(
                    $translation
                )
                : null,

            /** Все переводы */
            'translations' => FormFieldTranslationResource::collection(
                $this->whenLoaded(
                    'translations'
                )
            ),

            /** Варианты выбора */
            'options' => FormFieldOptionResource::collection(
                $this->whenLoaded(
                    'options'
                )
            ),

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
     * Получение текущего перевода
     * только из уже загруженной relation translations.
     *
     * Для полного Resource Controller загружает
     * все переводы поля.
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
