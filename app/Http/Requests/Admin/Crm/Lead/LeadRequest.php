<?php

namespace App\Http\Requests\Admin\Crm\Lead;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        foreach (['name','email','phone','message','source','page_url','referrer',
                     'utm_source','utm_medium','utm_campaign','utm_term','utm_content',
                     'ip','user_agent','status','notes'] as $f) {
            if ($this->filled($f)) {
                $merge[$f] = trim((string) $this->input($f));
            }
        }

        if ($this->has('consent')) {
            $merge['consent'] = filter_var($this->input('consent'), FILTER_VALIDATE_BOOL);
        }

        if (!empty($merge)) {
            $this->merge($merge);
        }
    }

    /**
     * @return array<string,ValidationRule|array|string>
     */
    public function rules(): array
    {
        $statuses = ['new','contacted','qualified','won','lost','spam'];

        return [
            // Контакты (в форме хотя бы один из email/phone/ message обычно обязателен — оставим гибко)
            'name'         => ['nullable','string','max:255'],
            'email'        => ['nullable','email','max:255'],
            'phone'        => ['nullable','string','max:32'],

            // Сообщение/источник
            'message'      => ['nullable','string'],
            'source'       => ['nullable','string','max:64'],
            'page_url'     => ['nullable','url','max:2048'],
            'referrer'     => ['nullable','url','max:2048'],

            // UTM
            'utm_source'   => ['nullable','string','max:64'],
            'utm_medium'   => ['nullable','string','max:64'],
            'utm_campaign' => ['nullable','string','max:128'],
            'utm_term'     => ['nullable','string','max:128'],
            'utm_content'  => ['nullable','string','max:128'],

            // Техническое
            'ip'           => ['nullable','ip'],
            'user_agent'   => ['nullable','string'],
            'consent'      => ['sometimes','boolean'],

            // Статус/обработка
            'status'       => ['sometimes','string', Rule::in($statuses)],
            'processed_at' => ['nullable','date'],

            // Менеджер
            'manager_id'   => ['nullable','integer','exists:users,id'],

            // Прочее
            'notes'        => ['nullable','string'],
            'meta'         => ['nullable','array'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'         => 'имя',
            'email'        => 'email',
            'phone'        => 'телефон',
            'message'      => 'сообщение',
            'source'       => 'источник',
            'page_url'     => 'URL страницы',
            'referrer'     => 'реферер',
            'utm_source'   => 'UTM source',
            'utm_medium'   => 'UTM medium',
            'utm_campaign' => 'UTM campaign',
            'utm_term'     => 'UTM term',
            'utm_content'  => 'UTM content',
            'ip'           => 'IP адрес',
            'user_agent'   => 'User‑Agent',
            'consent'      => 'согласие',
            'status'       => 'статус',
            'processed_at' => 'время обработки',
            'manager_id'   => 'менеджер',
            'notes'        => 'примечания',
            'meta'         => 'метаданные',
        ];
    }
}
