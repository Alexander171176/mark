<?php

namespace App\Http\Requests\Admin\Constructor\HomePage\Reason;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class ReasonSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Нормализуем входные данные и подставляем дефолты
        $target = $this->input('cta_btn_target', '_self');
        $target = in_array($target, ['_self', '_blank'], true) ? $target : '_self';

        $this->merge([
            'locale'         => strtolower((string) $this->input('locale', app()->getLocale())),
            'cta_btn_target' => $target,
            'sort'           => is_numeric($this->input('sort')) ? (int) $this->input('sort') : 0,
            'activity'       => filter_var($this->input('activity', true), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
        ]);
    }

    public function rules(): array
    {
        // ID текущей секции в маршруте (на апдейте игнорируем уникальность по нему)
        $routeId = $this->route('section')
            ?? $this->route('reason_section')
            ?? $this->route('id');

        // Разрешённые локали (если пакет локализаций установлен)
        $supportedLocales = array_keys(LaravelLocalization::getSupportedLocales() ?? []);
        // если пусто, не ограничиваем по списку
        $localeRule = $supportedLocales
            ? ['required', 'string', Rule::in($supportedLocales)]
            : ['required', 'string', 'max:8'];

        return [
            'locale'         => array_merge($localeRule, [
                Rule::unique('reason_sections', 'locale')->ignore($routeId),
            ]),
            'subtitle'       => ['nullable', 'string', 'max:255'],
            'title'          => ['required', 'string', 'max:255'],

            'cta_title'      => ['nullable', 'string', 'max:255'],
            'cta_btn_text'   => ['nullable', 'string', 'max:255'],
            'cta_btn_url'    => ['nullable', 'url', 'max:2048'],
            'cta_btn_target' => ['nullable', Rule::in(['_self', '_blank'])],

            'sort'           => ['nullable', 'integer', 'min:0'],
            'activity'       => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'locale'         => 'локаль',
            'subtitle'       => 'подзаголовок',
            'title'          => 'заголовок',
            'cta_title'      => 'CTA заголовок',
            'cta_btn_text'   => 'текст CTA-кнопки',
            'cta_btn_url'    => 'ссылка CTA-кнопки',
            'cta_btn_target' => 'режим открытия ссылки',
            'sort'           => 'сортировка',
            'activity'       => 'активность',
        ];
    }

    public function messages(): array
    {
        return [
            'locale.required' => 'Укажите :attribute.',
            'locale.in'       => 'Значение поля :attribute недопустимо.',
            'locale.unique'   => 'Для этой :attribute запись уже существует.',

            'title.required'  => 'Укажите :attribute.',
            'title.max'       => ':attribute не может быть длиннее :max символов.',

            'subtitle.max'    => ':attribute не может быть длиннее :max символов.',
            'cta_title.max'   => ':attribute не может быть длиннее :max символов.',
            'cta_btn_text.max'=> ':attribute не может быть длиннее :max символов.',

            'cta_btn_url.url' => 'Поле :attribute должно быть корректной ссылкой.',
            'cta_btn_url.max' => ':attribute не может быть длиннее :max символов.',

            'cta_btn_target.in' => 'Поле :attribute может быть только "_self" или "_blank".',

            'sort.integer'    => ':attribute должна быть числом.',
            'sort.min'        => ':attribute не может быть меньше :min.',
            'activity.boolean'=> ':attribute имеет неверный формат.',
        ];
    }
}
