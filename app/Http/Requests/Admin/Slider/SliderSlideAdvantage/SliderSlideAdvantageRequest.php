<?php

namespace App\Http\Requests\Admin\Slider\SliderSlideAdvantage;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SliderSlideAdvantageRequest extends FormRequest
{
    /** Доступ к CRUD проверяется middleware / Policy. */
    public function authorize(): bool
    {
        return true;
    }

    /** Подготовка глобальных полей. Параметры pivot обрабатывает конструктор. */
    protected function prepareForValidation(): void
    {
        $prepared = [];

        foreach (['sort', 'moderation_status', 'moderated_by', 'moderated_at'] as $field) {
            if ($this->exists($field)) {
                $value = $this->input($field);
                $prepared[$field] = $value === '' ? null : $value;
            }
        }

        foreach (['moderation_note', 'icon_type', 'icon', 'icon_color', 'style'] as $field) {
            if ($this->exists($field)) {
                $prepared[$field] = $this->normalizeNullableString($this->input($field));
            }
        }

        // Глобальные поля сортировки и модерации (не pivot.sort).
        foreach (['sort', 'moderation_status', 'moderated_by'] as $field) {
            if ($this->exists($field) && $this->input($field) === '') {
                $prepared[$field] = null;
            }
        }

        if ($this->exists('moderated_at') && $this->input('moderated_at') === '') {
            $prepared['moderated_at'] = null;
        }

        if ($this->exists('moderation_note')) {
            $prepared['moderation_note'] = $this->normalizeNullableString(
                $this->input('moderation_note')
            );
        }

        if ($this->exists('activity')) {
            $value = $this->input('activity');
            if (in_array($value, [true, 1, '1', 'true', 'on', 'yes'], true)) {
                $prepared['activity'] = true;
            } elseif (in_array($value, [false, 0, '0', 'false', 'off', 'no'], true)) {
                $prepared['activity'] = false;
            }
        }

        if ($this->exists('translations') && is_array($this->input('translations'))) {
            $translations = [];
            foreach ($this->input('translations') as $locale => $translation) {
                if (! is_array($translation)) {
                    $translations[$locale] = $translation;
                    continue;
                }
                $translations[$locale] = array_replace($translation, [
                    'title' => $this->normalizeNullableString(Arr::get($translation, 'title')),
                    'text' => $this->normalizeNullableString(Arr::get($translation, 'text')),
                ]);
            }
            $prepared['translations'] = $translations;
        }

        $this->merge($prepared);
    }

    /** Полное состояние формы для store и update. */
    public function rules(): array
    {
        return [
            'sort' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'sort' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'moderation_status' => ['sometimes', 'integer', Rule::in([0, 1, 2])],
            'moderated_by' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'moderated_at' => ['nullable', 'date'],
            'moderation_note' => ['nullable', 'string'],

            'activity' => ['required', 'boolean'],
            'moderation_status' => ['sometimes', 'integer', Rule::in([0, 1, 2])],
            'moderated_by' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'moderated_at' => ['nullable', 'date'],
            'moderation_note' => ['nullable', 'string'],
            'icon_type' => ['required', 'string', 'max:30', Rule::in(['lucide', 'heroicon', 'class', 'none'])],
            'icon' => [Rule::requiredIf(fn () => in_array($this->input('icon_type'), ['lucide', 'heroicon', 'class'], true)), 'nullable', 'string', 'max:100'],
            'icon_color' => ['nullable', 'string', 'max:30'],
            'style' => ['nullable', 'string', 'max:50', Rule::in(['default', 'card', 'compact', 'inline', 'minimal', 'outlined'])],
            'settings' => ['nullable', 'array'],
            'translations' => ['required', 'array', 'min:1'],
            'translations.*' => ['required', 'array:title,text'],
            'translations.*.title' => ['nullable', 'string', 'max:255'],
            'translations.*.text' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $translations = $this->input('translations');
            if (is_array($translations)) {
                $availableLocales = $this->availableLocales();
                $hasContent = false;
                foreach ($translations as $locale => $translation) {
                    if (! in_array((string) $locale, $availableLocales, true)) {
                        $validator->errors()->add('translations.' . $locale, 'Указанный язык не поддерживается приложением.');
                        continue;
                    }
                    if (is_array($translation) && (filled($translation['title'] ?? null) || filled($translation['text'] ?? null))) {
                        $hasContent = true;
                    }
                }
                if (! $hasContent) {
                    $validator->errors()->add('translations', 'Заполните заголовок или описание преимущества хотя бы на одном языке.');
                }
            }

            $iconType = $this->input('icon_type');
            $icon = $this->input('icon');
            if ($iconType === 'none' && filled($icon)) {
                $validator->errors()->add('icon', 'Для типа none необходимо очистить название иконки.');
            }
            if (in_array($iconType, ['lucide', 'heroicon'], true) && is_string($icon) && $icon !== '' && ! preg_match('/^[a-zA-Z][a-zA-Z0-9_-]*$/', $icon)) {
                $validator->errors()->add('icon', 'Название иконки может содержать латинские буквы, цифры, дефисы и подчёркивания.');
            }
            if ($iconType === 'class' && is_string($icon) && $icon !== '' && ! preg_match('/^[a-zA-Z0-9_\\-: ]+$/', $icon)) {
                $validator->errors()->add('icon', 'CSS-класс иконки содержит недопустимые символы.');
            }
            $iconColor = $this->input('icon_color');
            if (is_string($iconColor) && $iconColor !== '' && ! preg_match('/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{4}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/', $iconColor)) {
                $validator->errors()->add('icon_color', 'Укажите цвет в HEX-формате, например #FFFFFF или #2563EB.');
            }
        });
    }

    /** @return array<int, string> */
    protected function availableLocales(): array
    {
        $locales = config('app.available_locales', []);
        return array_values(array_unique(array_filter(
            is_array($locales) ? $locales : [],
            fn (mixed $locale): bool => is_string($locale) && $locale !== ''
        )));
    }

    public function messages(): array
    {
        return [
            'sort.required' => 'Необходимо указать порядок сортировки.',
            'sort.integer' => 'Порядок сортировки должен быть целым числом.',
            'sort.min' => 'Порядок сортировки не может быть отрицательным.',
            'sort.max' => 'Порядок сортировки превышает допустимое значение.',
            'moderation_status.integer' => 'Статус модерации должен быть целым числом.',
            'moderation_status.in' => 'Недопустимый статус модерации.',
            'moderated_by.integer' => 'Идентификатор модератора должен быть целым числом.',
            'moderated_by.exists' => 'Указанный модератор не найден.',
            'moderated_at.date' => 'Дата модерации имеет неверный формат.',
            'moderation_note.string' => 'Комментарий модератора должен быть текстом.',

            'sort.required' => 'Укажите глобальный порядок сортировки.',
            'sort.integer' => 'Порядок сортировки должен быть целым числом.',
            'sort.min' => 'Порядок сортировки не может быть отрицательным.',
            'sort.max' => 'Превышено максимальное значение сортировки.',
            'moderation_status.in' => 'Недопустимый статус модерации.',
            'moderated_by.integer' => 'Идентификатор модератора должен быть целым числом.',
            'moderated_by.exists' => 'Указанный модератор не найден.',
            'moderated_at.date' => 'Дата модерации имеет неверный формат.',
            'moderation_note.string' => 'Комментарий модератора должен быть текстом.',
            'activity.required' => 'Необходимо указать активность преимущества.',
            'activity.boolean' => 'Некорректное значение активности.',
            'icon_type.required' => 'Необходимо выбрать тип иконки.',
            'icon_type.in' => 'Недопустимый тип иконки.',
            'icon.required' => 'Укажите название иконки или CSS-класс.',
            'icon.max' => 'Название иконки не должно превышать 100 символов.',
            'icon_color.max' => 'Значение цвета не должно превышать 30 символов.',
            'style.in' => 'Недопустимый вариант оформления преимущества.',
            'style.max' => 'Название стиля не должно превышать 50 символов.',
            'settings.array' => 'Дополнительные настройки должны быть массивом.',
            'translations.required' => 'Необходимо добавить переводы преимущества.',
            'translations.array' => 'Переводы должны быть массивом.',
            'translations.min' => 'Необходимо указать хотя бы один язык.',
            'translations.*.array' => 'Каждый перевод должен содержать только поля title и text.',
            'translations.*.title.max' => 'Заголовок преимущества не должен превышать 255 символов.',
            'translations.*.title.string' => 'Заголовок преимущества должен быть строкой.',
            'translations.*.text.string' => 'Описание преимущества должно быть строкой.',
        ];
    }

    /** Пустые строки преобразуем в null, неверные типы не скрываем. */
    protected function normalizeNullableString(mixed $value): mixed
    {
        if ($value === null || ! is_string($value)) {
            return $value;
        }
        $value = trim($value);
        return $value === '' ? null : $value;
    }
}
