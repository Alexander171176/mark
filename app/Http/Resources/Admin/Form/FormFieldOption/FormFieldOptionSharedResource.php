<?php

namespace App\Http\Resources\Admin\Form\FormFieldOption;

use App\Http\Resources\Admin\Form\FormField\FormFieldSharedResource;
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
     * Resource работает только с заранее
     * загруженными relations/counts и не должен
     * выполнять дополнительные SQL-запросы.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $translation = $this->currentTranslation();

        return [
            'id' => $this->id,
            'form_field_id' => $this->form_field_id,

            /** Основные данные */
            'value' => $this->value,

            /** Отображение / сортировка / активность */
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            /** Состояние */
            'is_default' => (bool) $this->is_default,

            /** Перевод текущей локали с fallback */
            'translation' => $translation
                ? new FormFieldOptionTranslationResource(
                    $translation
                )
                : null,

            /** Родительское поле */
            'field' => $this->whenLoaded(
                'field',
                function () {
                    return $this->field
                        ? new FormFieldSharedResource(
                            $this->field
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
