<?php

namespace App\Http\Requests\Admin\Constructor\HomePage\Reason;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReasonItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // layout (из формы) -> align (в БД)
        $layout = strtolower((string) $this->input('layout', ''));
        $align  = in_array($layout, ['left', 'right'], true)
            ? $layout
            : strtolower((string) $this->input('align', 'left'));

        if (! in_array($align, ['left', 'right'], true)) {
            $align = 'left';
        }

        $sort = is_numeric($this->input('sort')) ? (int) $this->input('sort') : 0;

        // activity может отсутствовать — тогда оставляем null (валидатор пропустит как nullable)
        $activityRaw = $this->input('activity', null);
        $activity    = isset($activityRaw)
            ? filter_var($activityRaw, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
            : null;

        $this->merge([
            'align'    => $align,
            'layout'   => $align, // чтобы фронт при возврате формы видел актуальное значение
            'sort'     => $sort,
            'activity' => $activity,
        ]);
    }

    public function rules(): array
    {
        return [
            'section_id' => ['required', 'integer', Rule::exists('reason_sections', 'id')],

            // Контент
            'layout'     => ['nullable', 'string', Rule::in(['left', 'right'])], // alias из формы
            'align'      => ['required', 'string', Rule::in(['left', 'right'])], // фактическое поле БД
            'title'      => ['required', 'string', 'max:255'],
            'text'       => ['nullable', 'string'],

            // Изображения — любой из вариантов опционален
            'image'      => ['nullable', 'file', 'image', 'max:5120'], // ~5MB
            'image_alt'  => ['nullable', 'string', 'max:255'],

            'light'      => ['nullable', 'file', 'image', 'max:5120'],
            'dark'       => ['nullable', 'file', 'image', 'max:5120'],
            'light_alt'  => ['nullable', 'string', 'max:255'],
            'dark_alt'   => ['nullable', 'string', 'max:255'],

            // Технические
            'sort'       => ['nullable', 'integer', 'min:0'],
            'activity'   => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'section_id' => 'секция',
            'layout'     => 'расположение изображения', // alias
            'align'      => 'расположение изображения',
            'title'      => 'заголовок',
            'text'       => 'текст',

            'image'      => 'изображение',
            'image_alt'  => 'ALT изображения',

            'light'      => 'светлая версия изображения',
            'dark'       => 'тёмная версия изображения',
            'light_alt'  => 'ALT светлой версии',
            'dark_alt'   => 'ALT тёмной версии',

            'sort'       => 'сортировка',
            'activity'   => 'активность',
        ];
    }

    public function messages(): array
    {
        return [
            'section_id.required' => 'Не указана :attribute.',
            'section_id.exists'   => 'Выбранная :attribute не найдена.',

            'layout.in'           => ':attribute должна быть "left" или "right".',
            'align.required'      => 'Укажите :attribute.',
            'align.in'            => ':attribute должна быть "left" или "right".',

            'title.required'      => 'Укажите :attribute.',
            'title.max'           => ':attribute не может быть длиннее :max символов.',

            'image.image'         => 'Поле :attribute должно быть изображением.',
            'image.max'           => ':attribute не может превышать :max КБ.',
            'light.image'         => 'Поле :attribute должно быть изображением.',
            'light.max'           => ':attribute не может превышать :max КБ.',
            'dark.image'          => 'Поле :attribute должно быть изображением.',
            'dark.max'            => ':attribute не может превышать :max КБ.',

            'image_alt.max'       => ':attribute не может быть длиннее :max символов.',
            'light_alt.max'       => ':attribute не может быть длиннее :max символов.',
            'dark_alt.max'        => ':attribute не может быть длиннее :max символов.',

            'sort.integer'        => ':attribute должна быть числом.',
            'sort.min'            => ':attribute не может быть меньше :min.',
            'activity.boolean'    => ':attribute имеет неверный формат.',
        ];
    }
}
