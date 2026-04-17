<?php

namespace App\Http\Requests\Admin\Finance\ExchangeRate;

use Illuminate\Foundation\Http\FormRequest;

class ExchangeRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // контролируйте доступ через Policy/гейты
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        // Нормализуем валюты (UPPER)
        foreach (['base_currency', 'quote_currency'] as $k) {
            if ($this->filled($k) && is_string($this->input($k))) {
                $merge[$k] = strtoupper(trim($this->input($k)));
            }
        }

        // Трим строки
        foreach (['provider'] as $k) {
            if ($this->filled($k) && is_string($this->input($k))) {
                $merge[$k] = trim($this->input($k));
            }
        }

        // Разбор JSON-строки meta (если пришла строкой)
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
        return [
            'base_currency'  => ['required','string','size:3'],
            'quote_currency' => ['required','string','size:3','different:base_currency'],

            // Курс > 0, высокая точность допустима — backend хранит decimal(20,8)
            'rate'           => ['required','numeric','gt:0'],

            'provider'   => ['nullable','string','max:64'],
            'fetched_at' => ['nullable','date'],
            'valid_from' => ['nullable','date'],
            'valid_to'   => ['nullable','date','after_or_equal:valid_from'],
            'activity'   => ['sometimes','boolean'],
            'meta'       => ['nullable','array'],
        ];
    }

    public function messages(): array
    {
        return [
            'base_currency.required'  => 'Укажите базовую валюту.',
            'base_currency.size'      => 'Код базовой валюты должен состоять из 3 символов (ISO 4217).',

            'quote_currency.required' => 'Укажите котируемую валюту.',
            'quote_currency.size'     => 'Код котируемой валюты должен состоять из 3 символов (ISO 4217).',
            'quote_currency.different'=> 'Базовая и котируемая валюты не должны совпадать.',

            'rate.required' => 'Укажите значение курса.',
            'rate.numeric'  => 'Курс должен быть числом.',
            'rate.gt'       => 'Курс должен быть больше 0.',

            'provider.max'           => 'Слишком длинное название провайдера (макс. 64).',
            'fetched_at.date'        => 'Поле fetched_at должно быть датой.',
            'valid_from.date'        => 'Поле valid_from должно быть датой.',
            'valid_to.date'          => 'Поле valid_to должно быть датой.',
            'valid_to.after_or_equal'=> 'Дата окончания действия должна быть не раньше даты начала.',
            'activity.boolean'       => 'Поле activity должно быть булевым.',
            'meta.array'             => 'Поле meta должно быть объектом/массивом.',
        ];
    }

    public function attributes(): array
    {
        return [
            'base_currency'  => 'базовая валюта',
            'quote_currency' => 'котируемая валюта',
            'rate'           => 'курс',
            'provider'       => 'провайдер',
            'fetched_at'     => 'дата получения',
            'valid_from'     => 'дата начала действия',
            'valid_to'       => 'дата окончания действия',
            'activity'       => 'активность',
            'meta'           => 'метаданные',
        ];
    }
}
