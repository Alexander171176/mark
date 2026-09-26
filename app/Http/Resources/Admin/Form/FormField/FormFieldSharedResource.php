<?php

namespace App\Http\Resources\Admin\Form\FormField;

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

            /** Отображение */
            'sort' => (int) $this->sort,
            'width' => $this->width,

            /** Перевод текущей локали с fallback */
            'translation' => $translation
                ? new FormFieldTranslationResource($translation)
                : null,

            /** Счётчики */
            'options_count' => $this->whenCounted('options'),

            /** Даты */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
