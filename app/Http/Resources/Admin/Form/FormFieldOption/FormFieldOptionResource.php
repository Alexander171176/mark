<?php

namespace App\Http\Resources\Admin\Form\FormFieldOption;

use App\Http\Resources\Admin\Form\FormField\FormFieldSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormFieldOptionResource extends JsonResource
{
    /**
     * Полное административное представление
     * варианта поля формы.
     *
     * Основное назначение:
     * - Edit;
     * - Show;
     * - административный конструктор формы.
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

            /** Дополнительные настройки */
            'settings' => $this->settings,

            /** Текущий перевод с fallback */
            'translation' => $translation
                ? new FormFieldOptionTranslationResource(
                    $translation
                )
                : null,

            /** Все переводы */
            'translations' =>
                FormFieldOptionTranslationResource::collection(
                    $this->whenLoaded(
                        'translations'
                    )
                ),

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
     * Получение текущего перевода
     * только из уже загруженной relation translations.
     *
     * Для полного Resource Controller загружает
     * все переводы варианта.
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
