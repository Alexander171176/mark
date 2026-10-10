<?php

namespace App\Http\Resources\Public\Form\Form;

use App\Http\Resources\Public\Form\FormField\FormFieldResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormResource extends JsonResource
{
    /**
     * Преобразование формы для публичной части приложения.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $translation = $this->loadedTranslationOrFallback();

        return [
            'id' => $this->id,
            'code' => $this->code,

            /*
            |--------------------------------------------------------------------------
            | Поведение формы
            |--------------------------------------------------------------------------
            */

            'auth_required' => (bool) $this->auth_required,

            /*
            |--------------------------------------------------------------------------
            | Защита публичной формы
            |--------------------------------------------------------------------------
            */

            'captcha_enabled' => (bool) $this->captcha_enabled,

            'spam_protection' => (bool) $this->spam_protection,

            'honeypot_enabled' => (bool) $this->honeypot_enabled,

            /*
            |--------------------------------------------------------------------------
            | Перевод для текущей локали с fallback
            |--------------------------------------------------------------------------
            */

            'translation' => $translation
                ? [
                    'title' => $translation->title,
                    'subtitle' => $translation->subtitle,
                    'description' => $translation->description,
                    'submit_text' => $translation->submit_text,
                    'success_message' => $translation->success_message,
                    'error_message' => $translation->error_message,
                ]
                : null,

            /*
            |--------------------------------------------------------------------------
            | Только публичные поля формы
            |--------------------------------------------------------------------------
            */

            'fields' => FormFieldResource::collection(
                $this->whenLoaded('activeFields')
            ),
        ];
    }
}
