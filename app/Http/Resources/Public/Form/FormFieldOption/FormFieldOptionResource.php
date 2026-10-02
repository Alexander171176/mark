<?php

namespace App\Http\Resources\Public\Form\FormFieldOption;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormFieldOptionResource extends JsonResource
{
    /**
     * Преобразование варианта поля для публичной части приложения.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $translation = $this->loadedTranslationOrFallback();

        return [
            'id' => $this->id,
            'value' => $this->value,
            'is_default' => (bool) $this->is_default,

            // Настройки публичного варианта
            'settings' => $this->settings ?? [],

            // Перевод для текущей локали с fallback
            'translation' => $translation
                ? [
                    'label' => $translation->label,
                    'description' => $translation->description,
                ]
                : null,
        ];
    }
}
