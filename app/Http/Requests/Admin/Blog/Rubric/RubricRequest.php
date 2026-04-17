<?php

namespace App\Http\Requests\Admin\Blog\Rubric;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\Rule;

class RubricRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // IMPORTANT:
        // Пока маршруты/контроллеры не приводим — оставляем как у тебя:
        // $this->route('rubric') может быть моделью или id.
        $routeRubric = $this->route('rubric');
        $rubricId = is_object($routeRubric) ? ($routeRubric->id ?? null) : $routeRubric;

        // Владелец обязателен по миграции.
        // В админке обычно берём auth()->id(), но если в запросе нет auth (например сиды/тесты) —
        // можно прокинуть user_id из контроллера. Здесь делаем мягко:
        $userId = (int) ($this->input('user_id') ?: ($this->user()?->id ?? 0));

        return [
            // дерево
            'parent_id' => [
                'nullable',
                'integer',
                'exists:rubrics,id',
                // сам в себя не вкладываем
                Rule::when($rubricId, ['not_in:' . $rubricId]),
            ],
            // level НЕ валидируем: это кэш/серверная логика
            // 'level' => ...

            'in_menu'  => ['required', 'boolean'],

            'sort'     => ['nullable', 'integer', 'min:0'],
            'activity' => ['required', 'boolean'],

            // moderation
            'moderation_status' => ['nullable', 'integer', Rule::in([0, 1, 2])],
            'moderation_note'   => ['nullable', 'string', 'max:500'],
            // moderated_by / moderated_at — НЕ принимаем с фронта

            'icon'   => ['nullable', 'string'],
            'locale' => ['required', 'string', 'max:10'],

            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('rubrics')
                    ->where(fn ($q) => $q
                        ->where('user_id', $userId)
                        ->where('locale', $this->input('locale'))
                    )
                    ->ignore($rubricId),
            ],

            'url' => [
                'required',
                'string',
                'max:255', // по миграции
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('rubrics')
                    ->where(fn ($q) => $q
                        ->where('user_id', $userId)
                        ->where('locale', $this->input('locale'))
                    )
                    ->ignore($rubricId),
            ],

            'subtitle'    => ['nullable', 'string', 'max:255'],
            'short'       => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],

            // views по миграции есть, но обычно это системное поле
            // если ты реально редактируешь views из админки — скажи, вернём
            // 'views' => ['nullable', 'integer', 'min:0'],

            'meta_title'    => ['nullable', 'string', 'max:255'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'meta_desc'     => ['nullable', 'string'],

            'images'             => ['nullable','array'],
            'images.*.id'        => [
                'nullable','integer',
                Rule::exists('rubric_images','id'),
                Rule::prohibitedIf(fn() => $this->isMethod('POST')),
            ],
            'images.*.order'     => ['nullable','integer','min:0'],
            'images.*.alt'       => ['nullable','string','max:255'],
            'images.*.caption'   => ['nullable','string','max:255'],
            'images.*.file'      => [
                'nullable',
                'required_without:images.*.id',
                'file',
                'image',
                'mimes:jpeg,jpg,png,gif,svg,webp',
                'max:10240',
            ],

            'deletedImages'      => ['sometimes','array'],
            'deletedImages.*'    => ['integer','exists:rubric_images,id'],
        ];
    }

    public function messages(): array
    {
        return Lang::get('admin/requests');
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'activity' => filter_var($this->input('activity'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            'in_menu'  => filter_var($this->input('in_menu'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true,

            'locale' => is_string($this->input('locale')) ? trim($this->input('locale')) : $this->input('locale'),
            'title'  => is_string($this->input('title')) ? trim($this->input('title')) : $this->input('title'),
            'url'    => is_string($this->input('url')) ? trim($this->input('url')) : $this->input('url'),

            'subtitle' => is_string($this->input('subtitle')) ? trim($this->input('subtitle')) : $this->input('subtitle'),
            'short'    => is_string($this->input('short')) ? trim($this->input('short')) : $this->input('short'),
        ]);
    }
}
