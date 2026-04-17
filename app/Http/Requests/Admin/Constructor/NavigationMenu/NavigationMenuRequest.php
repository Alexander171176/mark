<?php

namespace App\Http\Requests\Admin\Constructor\NavigationMenu;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NavigationMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        if ($this->filled('slug')) {
            $merge['slug'] = strtolower(trim((string) $this->input('slug')));
        }

        if ($this->filled('name')) {
            $merge['name'] = trim((string) $this->input('name'));
        }

        if ($this->filled('location')) {
            $merge['location'] = strtolower(trim((string) $this->input('location')));
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
        $locations = ['header','footer','sidebar','custom'];

        // для обновления игнорируем текущую запись по route-model binding
        $currentId = $this->route('navigation_menu');
        if ($currentId instanceof \App\Models\Admin\Constructor\NavigationMenu\NavigationMenu) {
            $currentId = $currentId->getKey();
        }

        return [
            'name'      => ['required','string','max:255'],
            'slug'      => [
                'required','string','max:255','alpha_dash',
                Rule::unique('navigation_menus', 'slug')->ignore($currentId),
            ],
            'location'  => ['sometimes','string', Rule::in($locations)],
            'activity'  => ['sometimes','boolean'],
            'sort'      => ['sometimes','integer','min:0'],
            'meta'      => ['nullable','array'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'      => 'название меню',
            'slug'      => 'slug',
            'location'  => 'расположение',
            'activity' => 'активность',
            'sort'  => 'позиция',
            'meta'      => 'метаданные',
        ];
    }
}
