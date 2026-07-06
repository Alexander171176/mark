<?php

namespace App\Http\Requests\Admin\Privacy\PrivacyUserConsent;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PrivacyUserConsentRequest extends FormRequest
{
    /**
     * Определяет, имеет ли пользователь право выполнить запрос.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Правила валидации запроса.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Согласие пользователя
            |--------------------------------------------------------------------------
            */

            'accepted' => ['required', 'boolean'],

            /*
            |--------------------------------------------------------------------------
            | Дополнительные данные
            |--------------------------------------------------------------------------
            */

            'locale' => ['nullable', 'string', 'max:10'],

            'policy_version' => ['nullable', 'integer', 'min:1'],

            'policy_url' => ['nullable', 'string', 'max:500'],

            'policy_hash' => ['nullable', 'string', 'size:64'],
        ];
    }
}
