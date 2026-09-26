<?php

namespace App\Http\Resources\Admin\Form\FormFieldOption;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormFieldOptionSharedResource extends JsonResource
{
    /**
     * Компактное представление варианта поля формы.
     *
     * Основное назначение:
     * - Admin Index;
     * - связанные сущности;
     * - справочные списки;
     * - компактное отображение вариантов.
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
            'form_field_id' => (int) $this->form_field_id,

            'value' => $this->value,

            /** Состояние */
            'activity' => (bool) $this->activity,
            'is_default' => (bool) $this->is_default,

            /** Порядок */
            'sort' => (int) $this->sort,

            /** Перевод текущей локали с fallback */
            'translation' => $translation
                ? new FormFieldOptionTranslationResource($translation)
                : null,

            /** Даты */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
