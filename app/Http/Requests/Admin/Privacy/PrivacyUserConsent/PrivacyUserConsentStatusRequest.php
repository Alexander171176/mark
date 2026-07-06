<?php

namespace App\Http\Requests\Admin\Privacy\PrivacyUserConsent;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PrivacyUserConsentStatusRequest extends FormRequest
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
     * В настоящее время GET-запрос не принимает входных данных,
     * однако Request оставлен для единой архитектуры проекта
     * и возможного расширения в будущем.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [];
    }
}
