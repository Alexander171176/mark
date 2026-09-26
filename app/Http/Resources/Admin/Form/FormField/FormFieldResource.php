<?php

namespace App\Http\Resources\Admin\Form\FormField;

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
            'form_id' => (int) $this->form_id,

            'name' => $this->name,
            'type' => $this->type,

            /** Состояние */
            'activity' => (bool) $this->activity,
            'required' => (bool) $this->required,
            'readonly' => (bool) $this->readonly,
            'disabled' => (bool) $this->disabled,

            /** Порядок и отображение */
            'sort' => (int) $this->sort,
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

            /** Текущий перевод с fallback */
            'translation' => $translation
                ? new FormFieldTranslationResource($translation)
                : null,

            /** Все переводы */
            'translations' => FormFieldTranslationResource::collection(
                $this->whenLoaded('translations')
            ),

            /** Варианты выбора */
            'options' => FormFieldOptionResource::collection(
                $this->whenLoaded('options')
            ),

            /** Счётчики */
            'options_count' => $this->whenCounted('options'),
            'submission_values_count' => $this->whenCounted(
                'submissionValues'
            ),
            'submission_files_count' => $this->whenCounted(
                'submissionFiles'
            ),

            /** Даты */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
