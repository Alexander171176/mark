<?php

namespace App\Http\Requests\Admin\System\Redirect;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RedirectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        // from_path — относительный путь, приведём к началу со слэша и без пробелов
        if ($this->filled('from_path')) {
            $path = trim((string)$this->input('from_path'));
            // убрать домен/протокол, если случайно прислали
            if (preg_match('~^https?://[^/]+(/.*)$~i', $path, $m)) {
                $path = $m[1];
            }
            if ($path === '' || $path[0] !== '/') {
                $path = '/'.$path;
            }
            $merge['from_path'] = $path;
        }

        if ($this->filled('locale')) {
            $merge['locale'] = strtolower(trim((string)$this->input('locale')));
        }

        if ($this->filled('to_url')) {
            $merge['to_url'] = trim((string)$this->input('to_url'));
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
        // поддержим стандартные коды редиректов
        $codes = [301, 302, 307, 308];

        // для правила уникальности учтём локаль (nullable = отдельная группа)
        $ignoreId = $this->route('redirect')?->id ?? null;

        return [
            'from_path' => [
                'required',
                'string',
                'max:512',
                // относительный путь, без пробелов
                'regex:/^\/\S*$/',
                Rule::unique('redirects', 'from_path')
                    ->where(fn ($q) => $q->where('locale', $this->input('locale')))
                    ->ignore($ignoreId),
            ],
            // Может быть абсолютный или относительный URL — поэтому без правила url()
            'to_url' => [
                'required',
                'string',
                'max:2048',
                // запретим пробелы
                'regex:/^\S+$/',
            ],
            'code' => ['sometimes','integer', Rule::in($codes)],
            'preserve_query' => ['sometimes','boolean'],
            'locale' => ['nullable','string','max:16'],
            'activity' => ['sometimes','boolean'],
            'hits' => ['sometimes','integer','min:0'],
            'last_used_at' => ['nullable','date'],
            'notes' => ['nullable','string'],
            'meta' => ['nullable','array'],
        ];
    }

    public function attributes(): array
    {
        return [
            'from_path'      => 'исходный путь',
            'to_url'         => 'целевой URL',
            'code'           => 'HTTP‑код',
            'preserve_query' => 'сохранять query‑параметры',
            'locale'         => 'локаль',
            'activity'       => 'активность',
            'hits'           => 'счётчик переходов',
            'last_used_at'   => 'последнее использование',
            'notes'          => 'заметки',
            'meta'           => 'метаданные',
        ];
    }
}
