<?php

namespace App\Http\Requests\Admin\Constructor\NavigationItem;

use App\Models\Admin\Constructor\NavigationItem\NavigationItem;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NavigationItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        foreach (['type','target'] as $k) {
            if ($this->filled($k)) {
                $merge[$k] = strtolower(trim((string)$this->input($k)));
            }
        }

        if ($this->filled('title')) {
            $merge['title'] = trim((string)$this->input('title'));
        }

        // route_params может прийти строкой (JSON) — приведём к массиву
        if ($this->has('route_params')) {
            $rp = $this->input('route_params');
            if (is_string($rp)) {
                $decoded = json_decode($rp, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $merge['route_params'] = $decoded;
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
        $types   = ['custom','internal','route'];
        $targets = ['_self','_blank'];

        // Для обновления parent_id может ссылаться на себя — запретим в withValidator()
        return [
            'menu_id'     => ['required','integer','exists:navigation_menus,id'],
            'parent_id'   => ['nullable','integer','exists:navigation_items,id'],
            'title'       => ['required','string','max:255'],
            'type'        => ['required','string', Rule::in($types)],

            // Для custom|internal требуем url; для route — route_name
            'url'         => [
                Rule::requiredIf(fn () => in_array($this->input('type'), ['custom','internal'], true)),
                'nullable','string','max:2048'
                // internal может быть относительным — не ставим правило "url"
            ],
            'route_name'  => [
                Rule::requiredIf(fn () => $this->input('type') === 'route'),
                'nullable','string','max:191'
            ],
            'route_params'=> ['nullable','array'],

            'target'      => ['sometimes','string', Rule::in($targets)],
            'icon'        => ['nullable','string','max:64'],
            'activity'    => ['sometimes','boolean'],
            'sort'        => ['sometimes','integer','min:0'],
            'meta'        => ['nullable','array'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            $id        = $this->route('navigation_item'); // при update может быть модель/ID
            $currentId = is_object($id) ? $id->getKey() : $id;

            // Нельзя ссылаться на себя в качестве родителя
            if ($currentId && (int)$this->input('parent_id') === (int)$currentId) {
                $v->errors()->add('parent_id', 'Пункт меню не может быть родителем самого себя.');
            }

            // (опционально) Если задан parent_id — сверим, что он в том же меню
            if ($this->filled('parent_id')) {
                $parent = NavigationItem::find($this->input('parent_id'));
                if ($parent && (int)$parent->menu_id !== (int)$this->input('menu_id')) {
                    $v->errors()->add('parent_id', 'Родительский пункт должен принадлежать тому же меню.');
                }
            }
        });
    }

    public function attributes(): array
    {
        return [
            'menu_id'     => 'меню',
            'parent_id'   => 'родительский пункт',
            'title'       => 'заголовок',
            'type'        => 'тип ссылки',
            'url'         => 'URL',
            'route_name'  => 'имя роута',
            'route_params'=> 'параметры роута',
            'target'      => 'target',
            'icon'        => 'иконка',
            'activity'    => 'активность',
            'sort'    => 'позиция',
            'meta'        => 'метаданные',
        ];
    }
}
