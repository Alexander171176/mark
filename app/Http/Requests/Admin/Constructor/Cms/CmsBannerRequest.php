<?php

namespace App\Http\Requests\Admin\Constructor\Cms;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CmsBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        foreach (['link_type','link_target','placement'] as $k) {
            if ($this->filled($k)) {
                $merge[$k] = strtolower(trim((string)$this->input($k)));
            }
        }

        if ($this->has('link_params')) {
            $rp = $this->input('link_params');
            if (is_string($rp)) {
                $decoded = json_decode($rp, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $merge['link_params'] = $decoded;
                }
            }
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
        $linkTypes  = ['url','route','none'];
        $targets    = ['_self','_blank'];

        return [
            'title'        => ['required','string','max:255'],
            'subtitle'     => ['nullable','string','max:255'],

            'placement'    => ['required','string','max:64'],

            'link_type'    => ['required','string', Rule::in($linkTypes)],
            'link_url'     => [
                // Допускаем любые абсолютные URL; если нужны относительные — уберите правило "url"
                Rule::requiredIf(fn () => $this->input('link_type') === 'url'),
                'nullable','string','max:2048','url',
                'prohibited_unless:link_type,url',
            ],
            'link_route'   => [
                Rule::requiredIf(fn () => $this->input('link_type') === 'route'),
                'nullable','string','max:191',
                'prohibited_unless:link_type,route',
            ],
            'link_params'  => ['nullable','array'],
            'link_target'  => ['sometimes','string', Rule::in($targets)],
            'button_label' => ['nullable','string','max:64'],

            'starts_at'    => ['nullable','date'],
            'ends_at'      => ['nullable','date','after_or_equal:starts_at'],

            'activity'    => ['sometimes','boolean'],
            'sort'        => ['sometimes','integer','min:0'],

            'meta'         => ['nullable','array'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title'        => 'заголовок',
            'subtitle'     => 'подзаголовок',
            'placement'    => 'позиция показа',
            'link_type'    => 'тип ссылки',
            'link_url'     => 'URL',
            'link_route'   => 'имя роута',
            'link_params'  => 'параметры роута',
            'link_target'  => 'target',
            'button_label' => 'текст кнопки',
            'starts_at'    => 'начало публикации',
            'ends_at'      => 'окончание публикации',
            'activity'     => 'активность',
            'sort'     => 'позиция',
            'meta'         => 'метаданные',
        ];
    }
}
