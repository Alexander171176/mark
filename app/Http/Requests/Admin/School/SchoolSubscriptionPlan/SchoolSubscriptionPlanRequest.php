<?php

namespace App\Http\Requests\Admin\School\SchoolSubscriptionPlan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SchoolSubscriptionPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        if ($this->has('slug') && is_string($this->input('slug'))) {
            $data['slug'] = Str::slug($this->input('slug'));
        }

        foreach (['activity', 'auto_renew'] as $field) {
            if ($this->has($field)) {
                $data[$field] = filter_var(
                    $this->input($field),
                    FILTER_VALIDATE_BOOL,
                    FILTER_NULL_ON_FAILURE
                );
            }
        }

        foreach (['sort', 'interval', 'trial_days', 'currency_id'] as $field) {
            if ($this->has($field) && is_numeric($this->input($field))) {
                $data[$field] = (int) $this->input($field);
            }
        }

        if ($this->has('price')) {
            $value = $this->input('price');

            $data['price'] = $value === null || $value === ''
                ? null
                : (is_string($value) ? str_replace(',', '.', trim($value)) : $value);
        }

        foreach (['provider_payload', 'config'] as $jsonField) {
            if ($this->filled($jsonField) && is_string($this->input($jsonField))) {
                $decoded = json_decode($this->input($jsonField), true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    $data[$jsonField] = $decoded;
                }
            }
        }

        if (!empty($data)) {
            $this->merge($data);
        }
    }

    public function rules(): array
    {
        $plan = $this->route('schoolSubscriptionPlan')
            ?? $this->route('subscriptionPlan')
            ?? $this->route('school_subscription_plan')
            ?? $this->route('id');

        $planId = is_object($plan)
            ? $plan->id
            : ($plan ? (int) $plan : null);

        return [
            'sort' => ['nullable', 'integer', 'min:0'],
            'activity' => ['required', 'boolean'],

            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('school_subscription_plans', 'slug')->ignore($planId),
            ],

            'published_at' => ['nullable', 'date'],
            'available_from' => ['nullable', 'date'],
            'available_until' => ['nullable', 'date', 'after_or_equal:available_from'],

            'billing_period' => ['required', Rule::in(['day', 'week', 'month', 'year'])],
            'interval' => ['required', 'integer', 'min:1', 'max:365'],

            'currency_id' => ['required', 'integer', Rule::exists('currencies', 'id')],
            'price' => ['required', 'numeric', 'min:0'],

            'trial_days' => ['nullable', 'integer', 'min:0', 'max:365'],
            'auto_renew' => ['sometimes', 'boolean'],

            'provider' => ['nullable', 'string', 'max:70'],
            'provider_ref' => ['nullable', 'string', 'max:255'],
            'provider_payload' => ['nullable', 'array'],
            'config' => ['nullable', 'array'],

            'translations' => ['required', 'array', 'min:1'],
            'translations.*.title' => ['required', 'string', 'max:255'],
            'translations.*.subtitle' => ['nullable', 'string', 'max:255'],
            'translations.*.short' => ['nullable', 'string', 'max:255'],
            'translations.*.description' => ['nullable', 'string'],
            'translations.*.meta_title' => ['nullable', 'string', 'max:160'],
            'translations.*.meta_keywords' => ['nullable', 'string', 'max:255'],
            'translations.*.meta_desc' => ['nullable', 'string', 'max:255'],

            'images' => ['nullable', 'array'],
            'images.*.id' => [
                'nullable',
                'integer',
                Rule::exists('school_subscription_plan_images', 'id'),
                Rule::prohibitedIf(fn () => $this->isMethod('post')),
            ],
            'images.*.order' => ['nullable', 'integer', 'min:0'],
            'images.*.alt' => ['nullable', 'string', 'max:255'],
            'images.*.caption' => ['nullable', 'string', 'max:255'],
            'images.*.file' => [
                'nullable',
                'required_without:images.*.id',
                'file',
                'image',
                'mimes:jpeg,jpg,png,gif,svg,webp',
                'max:10240',
            ],

            'deletedImages' => ['sometimes', 'array'],
            'deletedImages.*' => ['integer', Rule::exists('school_subscription_plan_images', 'id')],
        ];
    }

    public function messages(): array
    {
        return [
            'slug.required' => 'Укажите slug тарифа.',
            'slug.regex' => 'Slug может содержать только латинские буквы, цифры и дефисы.',
            'slug.unique' => 'Тариф с таким slug уже существует.',

            'activity.required' => 'Укажите активность тарифа.',
            'activity.boolean' => 'Поле активности должно быть булевым значением.',

            'billing_period.required' => 'Укажите период биллинга.',
            'billing_period.in' => 'Период биллинга должен быть одним из: day, week, month, year.',

            'interval.required' => 'Укажите интервал тарифа.',
            'interval.integer' => 'Интервал должен быть целым числом.',
            'interval.min' => 'Интервал не может быть меньше 1.',

            'currency_id.required' => 'Укажите валюту.',
            'currency_id.exists' => 'Выбранная валюта не найдена.',

            'price.required' => 'Укажите цену.',
            'price.numeric' => 'Цена должна быть числом.',
            'price.min' => 'Цена не может быть отрицательной.',

            'trial_days.integer' => 'Пробный период должен быть целым числом.',
            'trial_days.min' => 'Пробный период не может быть отрицательным.',

            'available_until.after_or_equal' => 'Дата окончания доступности должна быть не раньше даты начала.',

            'provider_payload.array' => 'Поле provider_payload должно быть объектом/массивом.',
            'config.array' => 'Поле config должно быть объектом/массивом.',

            'translations.required' => 'Необходимо передать переводы тарифа.',
            'translations.*.title.required' => 'Укажите название тарифа.',

            'images.array' => 'Неверный формат поля изображений.',
            'images.*.id.exists' => 'Указанное изображение не найдено.',
            'images.*.id.prohibited' => 'ID изображения нельзя передавать при создании.',
            'images.*.file.required_without' => 'Загрузите файл изображения или укажите существующий ID.',
            'images.*.file.image' => 'Файл должен быть изображением.',
            'images.*.file.mimes' => 'Разрешённые форматы: jpeg, jpg, png, gif, svg, webp.',
            'images.*.file.max' => 'Максимальный размер изображения — 10 МБ.',

            'deletedImages.*.exists' => 'Некоторых изображений для удаления не существует.',
        ];
    }

    public function attributes(): array
    {
        return [
            'slug' => 'Slug',
            'activity' => 'Активность',
            'sort' => 'Сортировка',
            'published_at' => 'Дата публикации',
            'available_from' => 'Доступен с',
            'available_until' => 'Доступен до',
            'billing_period' => 'Период биллинга',
            'interval' => 'Интервал',
            'currency_id' => 'Валюта',
            'price' => 'Цена',
            'trial_days' => 'Пробный период',
            'auto_renew' => 'Автопродление',
            'provider' => 'Провайдер',
            'provider_ref' => 'ID тарифа у провайдера',
            'provider_payload' => 'Данные провайдера',
            'config' => 'Конфиг тарифа',
            'translations' => 'Переводы',
            'images' => 'Изображения тарифа',
        ];
    }
}
