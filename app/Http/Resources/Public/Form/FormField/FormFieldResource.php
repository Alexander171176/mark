<?php

namespace App\Http\Resources\Public\Form\FormField;

use App\Http\Resources\Public\Form\FormFieldOption\FormFieldOptionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormFieldResource extends JsonResource
{
    /**
     * Преобразование поля формы для публичной части приложения.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $translation = $this->loadedTranslationOrFallback();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,

            // Поведение поля
            'required' => (bool) $this->required,
            'readonly' => (bool) $this->readonly,
            'disabled' => (bool) $this->disabled,

            // Отображение
            'width' => $this->width,
            'default_value' => $this->default_value,

            // Настройки публичного поля
            'settings' => $this->settings ?? [],

            // Перевод для текущей локали с fallback
            'translation' => $translation
                ? [
                    'label' => $translation->label,
                    'placeholder' => $translation->placeholder,
                    'description' => $translation->description,
                ]
                : null,

            // Только активные варианты выбора
            'options' => FormFieldOptionResource::collection(
                $this->whenLoaded('activeOptions')
            ),
        ];
    }
}
