<?php

namespace App\Http\Requests\Admin\School\Coupon;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SchoolCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        foreach (['code', 'name', 'description', 'type', 'currency', 'applies_to'] as $field) {
            if ($this->filled($field) && is_string($this->input($field))) {
                $data[$field] = trim($this->input($field));
            }
        }

        if (!empty($data['code'])) {
            $data['code'] = strtoupper($data['code']);
        }

        if (!empty($data['currency'])) {
            $data['currency'] = strtoupper($data['currency']);
        }

        if ($this->input('type') === 'free') {
            $data['value'] = 0;
        }

        foreach (['activity', 'stackable'] as $field) {
            if ($this->has($field)) {
                $data[$field] = filter_var(
                    $this->input($field),
                    FILTER_VALIDATE_BOOL,
                    FILTER_NULL_ON_FAILURE
                );
            }
        }

        foreach (['max_uses', 'max_uses_per_user', 'used_count'] as $field) {
            if ($this->filled($field)) {
                $data[$field] = (int) $this->input($field);
            }
        }

        foreach (['value', 'min_order_total'] as $field) {
            if ($this->filled($field)) {
                $data[$field] = is_string($this->input($field))
                    ? str_replace(',', '.', trim($this->input($field)))
                    : $this->input($field);
            }
        }

        if ($this->filled('meta') && is_string($this->input('meta'))) {
            $decoded = json_decode($this->input('meta'), true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $data['meta'] = $decoded;
            }
        }

        if (!empty($data)) {
            $this->merge($data);
        }
    }

    public function rules(): array
    {
        $coupon = $this->route('school_coupon')
            ?? $this->route('coupon')
            ?? $this->route('schoolCoupon');

        $id = is_object($coupon)
            ? $coupon->id
            : ($coupon ? (int) $coupon : null);

        return [
            'code' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'string',
                'max:64',
                Rule::unique('school_coupons', 'code')->ignore($id),
            ],

            'name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],

            'type' => ['sometimes', 'string', Rule::in(['percent', 'fixed', 'free'])],

            'value' => [
                Rule::requiredIf(fn () => in_array($this->input('type'), ['percent', 'fixed'], true)),
                'numeric',
                'gte:0',
            ],

            'currency' => [
                Rule::requiredIf(fn () => $this->input('type') === 'fixed'),
                'nullable',
                'string',
                'size:3',
                'alpha',
            ],

            'min_order_total' => ['nullable', 'numeric', 'gte:0'],
            'max_uses' => ['nullable', 'integer', 'gte:1'],
            'max_uses_per_user' => ['nullable', 'integer', 'gte:1'],
            'used_count' => ['sometimes', 'integer', 'gte:0'],

            'applies_to' => ['sometimes', 'string', Rule::in(['any', 'courses', 'bundles'])],

            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date'],

            'activity' => ['sometimes', 'boolean'],
            'stackable' => ['sometimes', 'boolean'],

            'meta' => ['nullable', 'array'],

            'course_ids' => ['sometimes', 'array'],
            'course_ids.*' => ['integer', Rule::exists('school_courses', 'id')],

            'bundle_ids' => ['sometimes', 'array'],
            'bundle_ids.*' => ['integer', Rule::exists('school_bundles', 'id')],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $type = $this->input('type');

            if ($type === 'percent' && $this->filled('value')) {
                $value = (float) $this->input('value');

                if ($value <= 0 || $value > 100) {
                    $validator->errors()->add(
                        'value',
                        'Для процента скидки значение должно быть больше 0 и не больше 100.'
                    );
                }
            }

            if ($type === 'fixed' && $this->filled('value')) {
                $value = (float) $this->input('value');

                if ($value <= 0) {
                    $validator->errors()->add(
                        'value',
                        'Фиксированная скидка должна быть больше нуля.'
                    );
                }

                if (!$this->filled('currency')) {
                    $validator->errors()->add(
                        'currency',
                        'Для фиксированной скидки необходимо указать валюту.'
                    );
                }
            }

            if ($type === 'free' && (float) $this->input('value', 0) !== 0.0) {
                $validator->errors()->add(
                    'value',
                    'Для типа free значение должно быть равно 0.'
                );
            }

            if ($this->filled('starts_at') && $this->filled('ends_at')) {
                try {
                    $start = Carbon::parse($this->input('starts_at'));
                    $end = Carbon::parse($this->input('ends_at'));

                    if ($end->lt($start)) {
                        $validator->errors()->add(
                            'ends_at',
                            'Дата окончания не может быть раньше даты начала.'
                        );
                    }
                } catch (\Throwable) {
                    //
                }
            }

            if ($this->filled('max_uses') && $this->filled('used_count')) {
                if ((int) $this->input('used_count') > (int) $this->input('max_uses')) {
                    $validator->errors()->add(
                        'used_count',
                        'Счётчик использований не может превышать общий лимит.'
                    );
                }
            }

            if ($this->input('applies_to') === 'courses' && empty($this->input('course_ids', []))) {
                $validator->errors()->add(
                    'course_ids',
                    'Для области применения courses желательно указать хотя бы один курс.'
                );
            }

            if ($this->input('applies_to') === 'bundles' && empty($this->input('bundle_ids', []))) {
                $validator->errors()->add(
                    'bundle_ids',
                    'Для области применения bundles желательно указать хотя бы один набор курсов.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Укажите промокод.',
            'code.string' => 'Промокод должен быть строкой.',
            'code.max' => 'Промокод не должен превышать 64 символа.',
            'code.unique' => 'Такой промокод уже существует.',

            'name.string' => 'Название должно быть строкой.',
            'name.max' => 'Название не должно превышать 255 символов.',

            'description.string' => 'Описание должно быть строкой.',

            'type.in' => 'Недопустимый тип скидки. Доступно: percent, fixed, free.',

            'value.required' => 'Укажите величину скидки.',
            'value.numeric' => 'Величина скидки должна быть числом.',
            'value.gte' => 'Величина скидки не может быть отрицательной.',

            'currency.required' => 'Для фиксированной скидки требуется указать валюту.',
            'currency.size' => 'Код валюты должен содержать 3 буквы.',
            'currency.alpha' => 'Код валюты должен состоять только из букв.',

            'min_order_total.numeric' => 'Минимальная сумма заказа должна быть числом.',
            'min_order_total.gte' => 'Минимальная сумма заказа не может быть отрицательной.',

            'max_uses.integer' => 'Общий лимит использований должен быть целым числом.',
            'max_uses.gte' => 'Общий лимит использований должен быть не меньше 1.',

            'max_uses_per_user.integer' => 'Лимит на пользователя должен быть целым числом.',
            'max_uses_per_user.gte' => 'Лимит на пользователя должен быть не меньше 1.',

            'used_count.integer' => 'Счётчик использований должен быть целым числом.',
            'used_count.gte' => 'Счётчик использований не может быть отрицательным.',

            'applies_to.in' => 'Недопустимая область применения. Доступно: any, courses, bundles.',

            'starts_at.date' => 'Неверный формат даты начала.',
            'ends_at.date' => 'Неверный формат даты окончания.',

            'activity.boolean' => 'Поле активности должно быть булевым.',
            'stackable.boolean' => 'Поле комбинирования должно быть булевым.',

            'meta.array' => 'Поле meta должно быть объектом/массивом.',

            'course_ids.array' => 'Список курсов должен быть массивом.',
            'course_ids.*.integer' => 'Идентификатор курса должен быть целым числом.',
            'course_ids.*.exists' => 'Указанный курс не найден.',

            'bundle_ids.array' => 'Список наборов должен быть массивом.',
            'bundle_ids.*.integer' => 'Идентификатор набора должен быть целым числом.',
            'bundle_ids.*.exists' => 'Указанный набор курсов не найден.',
        ];
    }

    public function attributes(): array
    {
        return [
            'code' => 'Промокод',
            'name' => 'Название',
            'description' => 'Описание',
            'type' => 'Тип скидки',
            'value' => 'Величина скидки',
            'currency' => 'Валюта',
            'min_order_total' => 'Минимальная сумма заказа',
            'max_uses' => 'Общий лимит использований',
            'max_uses_per_user' => 'Лимит на пользователя',
            'used_count' => 'Счётчик использований',
            'applies_to' => 'Область применения',
            'starts_at' => 'Дата начала',
            'ends_at' => 'Дата окончания',
            'activity' => 'Активность',
            'stackable' => 'Комбинирование',
            'meta' => 'Метаданные',
            'course_ids' => 'Курсы',
            'bundle_ids' => 'Наборы курсов',
        ];
    }
}
