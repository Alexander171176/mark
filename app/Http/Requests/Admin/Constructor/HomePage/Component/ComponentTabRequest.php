<?php

namespace App\Http\Requests\Admin\Constructor\HomePage\Component;

use App\Models\Admin\Constructor\HomePage\Component\ComponentTab;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ComponentTabRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $routeTab = $this->route('tab'); // модель или id
        $this->merge([
            'slug'     => strtolower((string) $this->input('slug', '')),
            'sort'     => is_numeric($this->input('sort')) ? (int) $this->input('sort') : 0,
            'activity' => filter_var($this->input('activity', true), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            'section_id' => $this->input('section_id')
                ?? ($routeTab instanceof ComponentTab ? $routeTab->section_id : null),
        ]);
    }

    public function rules(): array
    {
        $routeTab = $this->route('tab') ?? $this->route('component_tab');
        $routeId  = $routeTab instanceof ComponentTab ? $routeTab->getKey() : $routeTab;

        $sectionId = $this->input('section_id')
            ?? ($routeTab instanceof ComponentTab ? $routeTab->section_id : null)
            ?? $this->route('section')
            ?? $this->route('component_section');

        return [
            'section_id' => ['required','integer', Rule::exists('component_sections','id')],
            'slug'       => [
                'required','string','max:64','regex:/^[a-z0-9\-]+$/',
                Rule::unique('component_tabs','slug')
                    ->ignore($routeId)
                    ->where(fn($q) => $q->where('section_id', $sectionId)),
            ],
            'label'      => ['required','string','max:255'],
            'sort'       => ['nullable','integer','min:0'],
            'activity'   => ['nullable','boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'section_id' => 'секция',
            'slug'       => 'ключ вкладки',
            'label'      => 'заголовок вкладки',
            'sort'       => 'сортировка',
            'activity'   => 'активность',
        ];
    }

    public function messages(): array
    {
        return [
            'section_id.required' => 'Не указана :attribute.',
            'section_id.exists'   => 'Выбранная :attribute не найдена.',
            'slug.required'       => 'Укажите :attribute.',
            'slug.regex'          => ':attribute может содержать только латинские буквы, цифры и дефисы.',
            'slug.unique'         => 'Такая :attribute уже существует в этой секции.',
            'label.required'      => 'Укажите :attribute.',
            'label.max'           => ':attribute не может быть длиннее :max символов.',
            'sort.integer'        => ':attribute должна быть числом.',
            'sort.min'            => ':attribute не может быть меньше :min.',
            'activity.boolean'    => ':attribute имеет неверный формат.',
        ];
    }
}
