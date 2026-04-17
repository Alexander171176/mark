<?php

namespace App\Http\Requests\Admin\School\Coupon;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Привяжите к Policy при необходимости
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        // Нормализация строк
        foreach (['code','name','description','type','currency','applies_to'] as $key) {
            if ($this->filled($key) && is_string($this->input($key))) {
                $merge[$key] = trim($this->input($key));
            }
        }

        if (!empty($merge['code'])) {
            $merge['code'] = strtoupper($merge['code']); // промокоды обычно верхним регистром
        }
        if (!empty($merge['currency'])) {
            $merge['currency'] = strtoupper($merge['currency']);
        }

        // Если type=free — значение скидки принудительно 0
        if ($this->input('type') === 'free') {
            $merge['value'] = 0;
        }

        // meta: принять строковый JSON
        if ($this->filled('meta') && is_string($this->input('meta'))) {
            $decoded = json_decode($this->input('meta'), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $merge['meta'] = $decoded;
            }
        }

        if ($merge) {
            $this->merge($merge);
        }
    }

    public function rules(): array
    {
        $routeModel = $this->route('coupon');
        $id = is_object($routeModel) ? $routeModel->id : ($routeModel ? (int)$routeModel : null);

        $types = ['percent','fixed','free'];
        $scopes = ['any','courses','bundles'];

        return [
            'code' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'string','max:64',
                Rule::unique('coupons','code')->ignore($id),
            ],
            'name'        => ['nullable','string','max:255'],
            'description' => ['nullable','string'],

            'type'        => ['sometimes','string', Rule::in($types)],
            'value'       => [
                // required для percent/fixed, игнорируется для free (готовим в prepareForValidation)
                Rule::requiredIf(fn() => in_array($this->input('type'), ['percent','fixed'], true)),
                'numeric','gte:0',
            ],
            'currency'    => [
                // обязательна для fixed
                Rule::requiredIf(fn() => $this->input('type') === 'fixed'),
                'nullable','string','size:3','alpha',
            ],

            'min_order_total'   => ['nullable','numeric','gte:0'],
            'max_uses'          => ['nullable','integer','gte:1'],
            'max_uses_per_user' => ['nullable','integer','gte:1'],
            'used_count'        => ['sometimes','integer','gte:0'],

            'applies_to'  => ['sometimes','string', Rule::in($scopes)],

            'starts_at'   => ['nullable','date'],
            'ends_at'     => ['nullable','date'],
            'activity'   => ['sometimes','boolean'],
            'stackable'   => ['sometimes','boolean'],

            'meta'        => ['nullable','array'],

            // Привязки (если создаёте/обновляете через этот же запрос)
            'course_ids'  => ['sometimes','array'],
            'course_ids.*'=> ['integer','exists:courses,id'],
            'bundle_ids'  => ['sometimes','array'],
            'bundle_ids.*'=> ['integer','exists:bundles,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            // Проверки по типу скидки
            $type = $this->input('type');

            if ($type === 'percent' && $this->filled('value')) {
                $val = (float) $this->input('value');
                if ($val <= 0 || $val > 100) {
                    $v->errors()->add('value', 'Для процента скидки значение должно быть > 0 и ≤ 100.');
                }
            }

            if ($type === 'fixed' && $this->filled('value')) {
                $val = (float) $this->input('value');
                if ($val <= 0) {
                    $v->errors()->add('value', 'Фиксированная скидка должна быть больше нуля.');
                }
                if (!$this->filled('currency')) {
                    $v->errors()->add('currency', 'Для фиксированной скидки необходимо указать валюту.');
                }
            }

            if ($type === 'free') {
                // На всякий случай запретим положительные value для free
                if ((float) $this->input('value', 0) !== 0.0) {
                    $v->errors()->add('value', 'Для типа free значение должно быть равно 0.');
                }
            }

            // Период действия
            if ($this->filled('starts_at') && $this->filled('ends_at')) {
                try {
                    $start = Carbon::parse($this->input('starts_at'));
                    $end   = Carbon::parse($this->input('ends_at'));
                    if ($end->lt($start)) {
                        $v->errors()->add('ends_at', 'Дата окончания не может быть раньше даты начала.');
                    }
                } catch (\Throwable $e) {
                    // игнор — базовые правила date отработают
                }
            }

            // Лимиты использования: used_count не должен превышать max_uses
            if ($this->filled('max_uses') && $this->filled('used_count')) {
                if ((int)$this->input('used_count') > (int)$this->input('max_uses')) {
                    $v->errors()->add('used_count', 'Счётчик использований не может превышать общий лимит.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Укажите промокод.',
            'code.string'   => 'Промокод должен быть строкой.',
            'code.max'      => 'Промокод не должен превышать 64 символа.',
            'code.unique'   => 'Такой промокод уже существует.',

            'name.string'   => 'Название должно быть строкой.',
            'name.max'      => 'Название слишком длинное.',

            'description.string' => 'Описание должно быть строкой.',

            'type.in'       => 'Недопустимый тип скидки (доступно: percent, fixed, free).',

            'value.required' => 'Укажите величину скидки.',
            'value.numeric'  => 'Величина скидки должна быть числом.',
            'value.gte'      => 'Величина скидки не может быть отрицательной.',

            'currency.required' => 'Для фиксированной скидки требуется указать валюту.',
            'currency.size'     => 'Код валюты должен содержать 3 буквы (ISO 4217).',
            'currency.alpha'    => 'Код валюты должен состоять только из букв.',

            'min_order_total.numeric' => 'Минимальная сумма заказа должна быть числом.',
            'min_order_total.gte'     => 'Минимальная сумма заказа не может быть отрицательной.',

            'max_uses.integer'        => 'Общий лимит использований должен быть целым числом.',
            'max_uses.gte'            => 'Общий лимит использований должен быть ≥ 1.',
            'max_uses_per_user.integer' => 'Лимит на пользователя должен быть целым числом.',
            'max_uses_per_user.gte'     => 'Лимит на пользователя должен быть ≥ 1.',
            'used_count.integer'      => 'Счётчик использований должен быть целым числом.',
            'used_count.gte'          => 'Счётчик использований не может быть отрицательным.',

            'applies_to.in' => 'Недопустимая область применения (any|courses|bundles).',

            'starts_at.date' => 'Неверный формат даты начала.',
            'ends_at.date'   => 'Неверный формат даты окончания.',

            'activity.boolean'  => 'Поле активности должно быть булевым.',
            'stackable.boolean' => 'Поле комбинирования должно быть булевым.',

            'meta.array' => 'Поле meta должно быть объектом/массивом.',

            'course_ids.array'    => 'course_ids должен быть массивом.',
            'course_ids.*.integer'=> 'Идентификатор курса должен быть целым числом.',
            'course_ids.*.exists' => 'Указанный курс не найден.',

            'bundle_ids.array'    => 'bundle_ids должен быть массивом.',
            'bundle_ids.*.integer'=> 'Идентификатор бандла должен быть целым числом.',
            'bundle_ids.*.exists' => 'Указанный бандл не найден.',
        ];
    }

    public function attributes(): array
    {
        return [
            'code'               => 'промокод',
            'name'               => 'название',
            'description'        => 'описание',
            'type'               => 'тип скидки',
            'value'              => 'величина скидки',
            'currency'           => 'валюта',
            'min_order_total'    => 'минимальная сумма заказа',
            'max_uses'           => 'общий лимит использований',
            'max_uses_per_user'  => 'лимит на пользователя',
            'used_count'         => 'счётчик использований',
            'applies_to'         => 'область применения',
            'starts_at'          => 'дата начала',
            'ends_at'            => 'дата окончания',
            'activity'           => 'активность',
            'stackable'          => 'комбинирование',
            'meta'               => 'метаданные',
            'course_ids'         => 'курсы',
            'bundle_ids'         => 'бандлы',
        ];
    }
}
