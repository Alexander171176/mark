<?php

namespace App\Http\Requests\Admin\Finance\SubscriptionPlan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubscriptionPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Приводим входные данные к ожидаемым типам/форматам ДО валидации.
     */
    protected function prepareForValidation(): void
    {
        $merge = [];

        /* ===================== Простые поля ===================== */

        if ($this->has('activity')) {
            // В Inertia часто прилетает true/false, "1"/"0", "on" и т.д.
            $merge['activity'] = filter_var($this->input('activity'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        // auto_renew → bool
        if ($this->has('auto_renew')) {
            $merge['auto_renew'] = filter_var(
                $this->input('auto_renew'),
                FILTER_VALIDATE_BOOLEAN,
                FILTER_NULL_ON_FAILURE
            );
        }

        foreach (['sort', 'interval', 'trial_days', 'currency_id'] as $intField) {
            if ($this->filled($intField)) {
                $merge[$intField] = (int) $this->input($intField);
            }
        }

        // Цена может прийти строкой "12,50" — приводим к "12.50"
        if ($this->filled('price')) {
            $merge['price'] = is_string($this->input('price'))
                ? str_replace(',', '.', trim($this->input('price')))
                : $this->input('price');
        }

        /* ===================== JSON поля ===================== */

        // provider_payload / config могут прийти строкой JSON
        foreach (['provider_payload', 'config'] as $jsonField) {
            if ($this->filled($jsonField) && is_string($this->input($jsonField))) {
                $decoded = json_decode($this->input($jsonField), true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $merge[$jsonField] = $decoded;
                }
            }
        }

        if (!empty($merge)) {
            $this->merge($merge);
        }
    }

    public function rules(): array
    {
        /**
         * Важно:
         * - slug уникален в пределах locale
         * - при update игнорируем текущую запись и мягко удалённые
         */
        $planId = $this->route('subscription_plan')?->id
            ?? $this->route('subscriptionPlan')?->id
            ?? $this->route('id');

        return [
            /* ===================== Управление ===================== */

            'sort'     => ['nullable', 'integer', 'min:0'],
            'activity' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'boolean',
            ],

            /* ===================== Витрина / локаль ===================== */

            'locale' => ['required', 'string', 'size:2'],

            'title' => ['required', 'string', 'max:255'],

            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('subscription_plans', 'slug')
                    ->ignore($planId)
                    ->where(fn ($q) => $q
                        ->where('locale', $this->input('locale'))
                        ->whereNull('deleted_at')
                    ),
            ],

            'subtitle'    => ['nullable', 'string', 'max:255'],
            'short'       => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],

            /* ===================== SEO ===================== */

            'meta_title'    => ['nullable', 'string', 'max:160'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'meta_desc'     => ['nullable', 'string', 'max:255'],

            /* ===================== Публикация / доступность ===================== */

            'published_at'    => ['nullable', 'date'],
            'available_from'  => ['nullable', 'date'],
            'available_until' => ['nullable', 'date', 'after_or_equal:available_from'],

            /* ===================== Биллинг ===================== */

            'billing_period' => ['required', Rule::in(['day', 'week', 'month', 'year'])],
            'interval'       => ['required', 'integer', 'min:1', 'max:365'],

            'currency_id' => ['required', 'integer', Rule::exists('currencies', 'id')],

            'price' => ['required', 'numeric', 'min:0'],

            'trial_days' => ['nullable', 'integer', 'min:0', 'max:365'],

            'auto_renew' => ['sometimes', 'boolean'],

            /* ===================== Провайдер оплаты ===================== */

            'provider' => ['nullable', 'string', 'max:70'],
            'provider_ref' => ['nullable', 'string', 'max:255'],

            'provider_payload' => ['nullable', 'array'],
            'config'           => ['nullable', 'array'],

            /* ===================== Изображения ===================== */

            'images'           => ['nullable', 'array'],
            'images.*.id'      => [
                'nullable',
                'integer',
                Rule::exists('subscription_plan_images', 'id'),
                Rule::prohibitedIf(fn () => $this->isMethod('post')),
            ],
            // order лучше хранить/менять через pivot, но валидировать можно как число
            'images.*.order'   => ['nullable', 'integer', 'min:0'],
            'images.*.alt'     => ['nullable', 'string', 'max:255'],
            'images.*.caption' => ['nullable', 'string', 'max:255'],
            'images.*.file'    => [
                'nullable',
                'required_without:images.*.id',
                'file',
                'image',
                'mimes:jpeg,jpg,png,gif,svg,webp',
                'max:10240',
            ],

            'deletedImages'   => ['sometimes', 'array'],
            'deletedImages.*' => ['integer', Rule::exists('subscription_plan_images', 'id')],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Укажите название тарифа.',
            'title.string'   => 'Название тарифа должно быть строкой.',
            'title.max'      => 'Название тарифа слишком длинное.',

            'locale.required' => 'Укажите локаль тарифа.',
            'locale.size'     => 'Локаль должна содержать 2 символа.',

            'slug.required' => 'Укажите слаг тарифа.',
            'slug.string'   => 'Слаг должен быть строкой.',
            'slug.max'      => 'Слаг слишком длинный.',
            'slug.unique'   => 'Такой слаг уже используется для выбранной локали.',

            'billing_period.required' => 'Укажите период биллинга.',
            'billing_period.in'       => 'Период биллинга должен быть одним из: day, week, month, year.',

            'interval.required' => 'Укажите интервал тарифа.',
            'interval.integer'  => 'Интервал должен быть целым числом.',
            'interval.min'      => 'Интервал не может быть меньше 1.',
            'interval.max'      => 'Интервал слишком большой.',

            'currency_id.required' => 'Укажите валюту.',
            'currency_id.exists'   => 'Выбранная валюта не найдена.',

            'price.required' => 'Укажите цену.',
            'price.numeric'  => 'Цена должна быть числом.',
            'price.min'      => 'Цена не может быть отрицательной.',

            'trial_days.integer' => 'Число дней пробного периода должно быть целым числом.',
            'trial_days.min'     => 'Пробный период не может быть отрицательным.',
            'trial_days.max'     => 'Пробный период слишком большой.',

            'available_until.after_or_equal' => 'Дата окончания доступности должна быть не раньше даты начала.',

            'provider.max' => 'Поле «Провайдер» не должно превышать :max символов.',

            'provider_payload.array' => 'Поле provider_payload должно быть объектом/массивом.',
            'config.array'           => 'Поле config должно быть объектом/массивом.',

            // images
            'images.array' => 'Неверный формат поля «Изображения».',

            'images.*.id.integer'    => 'ID изображения должен быть целым числом.',
            'images.*.id.exists'     => 'Указанное изображение не найдено.',
            'images.*.id.prohibited' => 'ID изображения нельзя передавать при создании — только при редактировании.',

            'images.*.order.integer' => 'Порядок изображения должен быть целым числом.',
            'images.*.order.min'     => 'Порядок изображения не может быть отрицательным.',

            'images.*.alt.string'     => 'Alt-текст изображения должен быть строкой.',
            'images.*.alt.max'        => 'Alt-текст изображения не должен превышать :max символов.',
            'images.*.caption.string' => 'Подпись изображения должна быть строкой.',
            'images.*.caption.max'    => 'Подпись изображения не должна превышать :max символов.',

            'images.*.file.required_without' => 'Загрузите файл изображения или укажите существующий ID.',
            'images.*.file.file'             => 'Неверный файл изображения.',
            'images.*.file.image'            => 'Файл должен быть изображением.',
            'images.*.file.mimes'            => 'Разрешённые форматы: jpeg, jpg, png, gif, svg, webp.',
            'images.*.file.max'              => 'Максимальный размер изображения — 10 МБ.',

            'deletedImages.array'     => 'Неверный формат списка удаляемых изображений.',
            'deletedImages.*.integer' => 'ID удаляемого изображения должен быть целым числом.',
            'deletedImages.*.exists'  => 'Некоторых изображений для удаления не существует.',
        ];
    }

    public function attributes(): array
    {
        return [
            'locale'         => 'Локаль',
            'title'          => 'Название',
            'slug'           => 'Слаг',
            'subtitle'       => 'Подзаголовок',
            'short'          => 'Краткое описание',
            'description'    => 'Описание',

            'meta_title'     => 'Meta Title',
            'meta_keywords'  => 'Meta Keywords',
            'meta_desc'      => 'Meta Description',

            'published_at'   => 'Дата публикации',
            'available_from' => 'Доступен с',
            'available_until'=> 'Доступен до',

            'billing_period' => 'Период биллинга',
            'interval'       => 'Интервал',
            'currency_id'    => 'Валюта',
            'price'          => 'Цена',
            'trial_days'     => 'Пробный период (дней)',
            'auto_renew'     => 'Автопродление',

            'provider'       => 'Провайдер',
            'provider_ref'   => 'Референс провайдера',
            'provider_payload' => 'Данные провайдера',
            'config'           => 'Конфиг',

            'activity'       => 'Активность',
            'sort'           => 'Сортировка',

            'images'         => 'Изображения тарифа',
        ];
    }
}
