<?php

namespace App\Http\Requests\Admin\Form\Form;

use App\Models\Admin\Form\Form\Form;
use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class FormRequest extends BaseFormRequest
{
    /**
     * Определяет, авторизован ли пользователь
     * выполнять данный запрос.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Подготовка данных перед валидацией.
     */
    protected function prepareForValidation(): void
    {
        $supportedLocales = config(
            'app.available_locales',
            ['ru']
        );

        $translations = $this->input(
            'translations',
            []
        );

        if (!is_array($translations)) {
            $translations = [];
        }

        $preparedTranslations = [];

        foreach ($translations as $locale => $translation) {
            if (!in_array(
                $locale,
                $supportedLocales,
                true
            )) {
                continue;
            }

            if (!is_array($translation)) {
                $translation = [];
            }

            $preparedTranslations[$locale] = [
                'title' => $this->normalizeNullableString(
                    Arr::get($translation, 'title')
                ),

                'subtitle' => $this->normalizeNullableString(
                    Arr::get($translation, 'subtitle')
                ),

                'description' => $this->normalizeNullableText(
                    Arr::get($translation, 'description')
                ),

                'submit_text' => $this->normalizeNullableString(
                    Arr::get($translation, 'submit_text')
                ),

                'success_message' => $this->normalizeNullableText(
                    Arr::get($translation, 'success_message')
                ),

                'error_message' => $this->normalizeNullableText(
                    Arr::get($translation, 'error_message')
                ),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Владелец
        |--------------------------------------------------------------------------
        |
        | user_id может быть null.
        |
        | null означает системную форму, которая не принадлежит
        | конкретному пользователю.
        |
        */

        $userId = $this->input('user_id');

        if (
            $userId === ''
            || $userId === null
        ) {
            $userId = null;
        } elseif (
            is_int($userId)
            || (
                is_string($userId)
                && ctype_digit($userId)
            )
        ) {
            $userId = (int) $userId;
        }

        /*
        |--------------------------------------------------------------------------
        | Дополнительные настройки
        |--------------------------------------------------------------------------
        */

        $settings = $this->prepareSettings(
            $this->input('settings')
        );

        $this->merge([
            'user_id' => $userId,

            'code' => $this->normalizeNullableCode(
                $this->input('code')
            ),

            'status' => $this->normalizeNullableString(
                $this->input('status')
            ) ?: Form::STATUS_DRAFT,

            'activity' => $this->toBoolean(
                $this->input('activity', true),
                true
            ),

            'sort' => $this->filled('sort')
                ? (int) $this->input('sort')
                : 100,

            /*
            |--------------------------------------------------------------------------
            | Защита от спама
            |--------------------------------------------------------------------------
            */

            'spam_protection' => $this->toBoolean(
                $this->input('spam_protection', true),
                true
            ),

            'honeypot_enabled' => $this->toBoolean(
                $this->input('honeypot_enabled', true),
                true
            ),

            'min_submit_seconds' => $this->filled('min_submit_seconds')
                ? (int) $this->input('min_submit_seconds')
                : (int) config(
                    'forms.spam.min_submit_seconds',
                    3
                ),

            'rate_limit' => $this->filled('rate_limit')
                ? (int) $this->input('rate_limit')
                : (int) config(
                    'forms.spam.rate_limit',
                    5
                ),

            'rate_limit_minutes' => $this->filled('rate_limit_minutes')
                ? (int) $this->input('rate_limit_minutes')
                : (int) config(
                    'forms.spam.rate_limit_minutes',
                    10
                ),

            'captcha_enabled' => $this->toBoolean(
                $this->input('captcha_enabled', false),
                false
            ),

            /*
            |--------------------------------------------------------------------------
            | Поведение формы
            |--------------------------------------------------------------------------
            */

            'auth_required' => $this->toBoolean(
                $this->input('auth_required', false),
                false
            ),

            /*
            |--------------------------------------------------------------------------
            | Дополнительные настройки
            |--------------------------------------------------------------------------
            */

            'settings' => $settings,

            /*
            |--------------------------------------------------------------------------
            | Переводы
            |--------------------------------------------------------------------------
            */

            'translations' => $preparedTranslations,
        ]);
    }

    /**
     * Правила валидации.
     */
    public function rules(): array
    {
        $formId = $this->resolveFormId();

        $availableLocales = config(
            'app.available_locales',
            ['ru']
        );

        return [
                /*
                |--------------------------------------------------------------------------
                | Основные данные
                |--------------------------------------------------------------------------
                */

                'user_id' => [
                    'nullable',
                    'integer',
                    'exists:users,id',
                ],

                'code' => [
                    'required',
                    'string',
                    'max:100',
                    'regex:/^[a-z0-9]+(?:_[a-z0-9]+)*$/',

                    Rule::unique(
                        'forms',
                        'code'
                    )->ignore($formId),
                ],

                'status' => [
                    'required',
                    'string',
                    'max:50',

                    Rule::in(
                        array_keys(
                            config(
                                'forms.statuses',
                                []
                            )
                        )
                    ),
                ],

                'activity' => [
                    'required',
                    'boolean',
                ],

                'sort' => [
                    'required',
                    'integer',
                    'min:0',
                ],

                /*
                |--------------------------------------------------------------------------
                | Защита от спама
                |--------------------------------------------------------------------------
                */

                'spam_protection' => [
                    'required',
                    'boolean',
                ],

                'honeypot_enabled' => [
                    'required',
                    'boolean',
                ],

                'min_submit_seconds' => [
                    'required',
                    'integer',
                    'min:0',
                    'max:65535',
                ],

                'rate_limit' => [
                    'required',
                    'integer',
                    'min:1',
                    'max:65535',
                ],

                'rate_limit_minutes' => [
                    'required',
                    'integer',
                    'min:1',
                    'max:65535',
                ],

                'captcha_enabled' => [
                    'required',
                    'boolean',
                ],

                /*
                |--------------------------------------------------------------------------
                | Поведение формы
                |--------------------------------------------------------------------------
                */

                'auth_required' => [
                    'required',
                    'boolean',
                ],

                /*
                |--------------------------------------------------------------------------
                | Дополнительные настройки
                |--------------------------------------------------------------------------
                */

                'settings' => [
                    'nullable',
                    'array',
                ],

                /*
                |--------------------------------------------------------------------------
                | Переводы
                |--------------------------------------------------------------------------
                */

                'translations' => [
                    'required',
                    'array',
                    'min:1',
                ],

                'translations.*' => [
                    'required',
                    'array',
                ],

                'translations.*.title' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'translations.*.subtitle' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'translations.*.description' => [
                    'nullable',
                    'string',
                ],

                'translations.*.submit_text' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'translations.*.success_message' => [
                    'nullable',
                    'string',
                ],

                'translations.*.error_message' => [
                    'nullable',
                    'string',
                ],
            ] + $this->localeRules(
                $availableLocales
            );
    }

    /**
     * Сообщения валидации.
     */
    public function messages(): array
    {
        return [
            /*
            |--------------------------------------------------------------------------
            | Основные данные
            |--------------------------------------------------------------------------
            */

            'user_id.integer' =>
                'ID владельца формы должен быть числом.',

            'user_id.exists' =>
                'Указанный пользователь не найден.',

            'code.required' =>
                'Системный код формы обязателен для заполнения.',

            'code.string' =>
                'Системный код формы должен быть строкой.',

            'code.max' =>
                'Системный код формы не должен превышать 100 символов.',

            'code.regex' =>
                'Системный код может содержать только строчные латинские буквы, цифры и символ подчёркивания.',

            'code.unique' =>
                'Форма с таким системным кодом уже существует.',

            'status.required' =>
                'Необходимо указать статус формы.',

            'status.in' =>
                'Недопустимое значение статуса формы.',

            'activity.boolean' =>
                'Поле активности должно быть логическим значением.',

            'sort.integer' =>
                'Поле сортировки должно быть числом.',

            'sort.min' =>
                'Поле сортировки не может быть меньше 0.',

            /*
            |--------------------------------------------------------------------------
            | Защита от спама
            |--------------------------------------------------------------------------
            */

            'spam_protection.boolean' =>
                'Настройка защиты от спама должна быть логическим значением.',

            'honeypot_enabled.boolean' =>
                'Настройка honeypot должна быть логическим значением.',

            'min_submit_seconds.integer' =>
                'Минимальное время заполнения должно быть числом.',

            'min_submit_seconds.min' =>
                'Минимальное время заполнения не может быть меньше 0 секунд.',

            'min_submit_seconds.max' =>
                'Минимальное время заполнения не может превышать 65535 секунд.',

            'rate_limit.integer' =>
                'Лимит отправок должен быть числом.',

            'rate_limit.min' =>
                'Лимит отправок должен быть не меньше 1.',

            'rate_limit.max' =>
                'Лимит отправок не может превышать 65535.',

            'rate_limit_minutes.integer' =>
                'Период ограничения должен быть числом.',

            'rate_limit_minutes.min' =>
                'Период ограничения должен быть не меньше 1 минуты.',

            'rate_limit_minutes.max' =>
                'Период ограничения не может превышать 65535 минут.',

            'captcha_enabled.boolean' =>
                'Настройка CAPTCHA должна быть логическим значением.',

            /*
            |--------------------------------------------------------------------------
            | Поведение формы
            |--------------------------------------------------------------------------
            */

            'auth_required.boolean' =>
                'Настройка авторизации должна быть логическим значением.',

            /*
            |--------------------------------------------------------------------------
            | Дополнительные настройки
            |--------------------------------------------------------------------------
            */

            'settings.array' =>
                'Дополнительные настройки формы должны быть массивом или корректным JSON-объектом.',

            /*
            |--------------------------------------------------------------------------
            | Переводы
            |--------------------------------------------------------------------------
            */

            'translations.required' =>
                'Необходимо добавить хотя бы один перевод формы.',

            'translations.array' =>
                'Поле переводов должно быть массивом.',

            'translations.min' =>
                'Необходимо добавить хотя бы одну локаль перевода.',

            'translations.*.title.required' =>
                'Название формы обязательно для каждой добавленной локали.',

            'translations.*.title.string' =>
                'Название формы должно быть строкой.',

            'translations.*.title.max' =>
                'Название формы не должно превышать 255 символов.',

            'translations.*.subtitle.max' =>
                'Подзаголовок формы не должен превышать 255 символов.',

            'translations.*.submit_text.max' =>
                'Текст кнопки отправки не должен превышать 255 символов.',
        ];
    }

    /**
     * Правила для разрешённых локалей приложения.
     */
    protected function localeRules(
        array $availableLocales
    ): array {
        $rules = [];

        foreach ($availableLocales as $locale) {
            $rules["translations.$locale"] = [
                'sometimes',
                'array',
            ];
        }

        return $rules;
    }

    /**
     * Получить ID редактируемой формы
     * из параметров текущего маршрута.
     *
     * Поддерживает:
     * - route model binding: Form $form;
     * - числовой параметр {form};
     * - резервный параметр {id}.
     */
    protected function resolveFormId(): ?int
    {
        $routeForm = $this->route('form');

        if ($routeForm instanceof Form) {
            return (int) $routeForm->id;
        }

        if (
            is_int($routeForm)
            || (
                is_string($routeForm)
                && ctype_digit($routeForm)
            )
        ) {
            return (int) $routeForm;
        }

        $routeId = $this->route('id');

        if (
            is_int($routeId)
            || (
                is_string($routeId)
                && ctype_digit($routeId)
            )
        ) {
            return (int) $routeId;
        }

        return null;
    }

    /**
     * Нормализация nullable строки.
     */
    protected function normalizeNullableString(
        mixed $value
    ): ?string {
        if (is_null($value)) {
            return null;
        }

        $value = trim(
            (string) $value
        );

        return $value === ''
            ? null
            : $value;
    }

    /**
     * Нормализация nullable текста.
     */
    protected function normalizeNullableText(
        mixed $value
    ): ?string {
        if (is_null($value)) {
            return null;
        }

        $value = trim(
            (string) $value
        );

        return $value === ''
            ? null
            : $value;
    }

    /**
     * Нормализация системного кода формы.
     *
     * Например:
     * Home Equipment Selector
     * ->
     * home_equipment_selector
     */
    protected function normalizeNullableCode(
        mixed $value
    ): ?string {
        $value = $this->normalizeNullableString(
            $value
        );

        if ($value === null) {
            return null;
        }

        $value = strtolower($value);

        $value = preg_replace(
            '/[^a-z0-9]+/',
            '_',
            $value
        );

        $value = trim(
            (string) $value,
            '_'
        );

        return $value === ''
            ? null
            : $value;
    }

    /**
     * Приведение значения к boolean.
     */
    protected function toBoolean(
        mixed $value,
        bool $default = false
    ): bool {
        $result = filter_var(
            $value,
            FILTER_VALIDATE_BOOLEAN,
            FILTER_NULL_ON_FAILURE
        );

        return $result ?? $default;
    }

    /**
     * Подготовка JSON-настроек формы.
     *
     * Важно:
     * - null / пустая строка -> null;
     * - массив -> массив;
     * - корректный JSON-объект -> массив;
     * - некорректный JSON сохраняется как исходная строка,
     *   чтобы правило "array" вернуло ошибку валидации.
     */
    protected function prepareSettings(
        mixed $settings
    ): mixed {
        if (
            is_null($settings)
            || $settings === ''
        ) {
            return null;
        }

        if (is_array($settings)) {
            return $settings;
        }

        if (is_string($settings)) {
            $decoded = json_decode(
                $settings,
                true
            );

            if (
                json_last_error() === JSON_ERROR_NONE
                && is_array($decoded)
            ) {
                return $decoded;
            }

            /*
            |--------------------------------------------------------------------------
            | Не превращаем ошибочный JSON в null
            |--------------------------------------------------------------------------
            |
            | Оставляем исходную строку.
            | Тогда правило:
            |
            | settings => nullable|array
            |
            | корректно сообщит пользователю об ошибке.
            |
            */

            return $settings;
        }

        return $settings;
    }
}
