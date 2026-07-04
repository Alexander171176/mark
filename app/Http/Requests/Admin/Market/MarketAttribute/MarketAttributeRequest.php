<?php

namespace App\Http\Requests\Admin\Market\MarketAttribute;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MarketAttributeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        $supportedLocales = config('app.available_locales', ['ru']);

        $translations = $this->input('translations', []);

        if (!is_array($translations)) {
            $translations = [];
        }

        $preparedTranslations = [];

        foreach ($translations as $locale => $translation) {
            if (!in_array($locale, $supportedLocales, true)) {
                continue;
            }

            $preparedTranslations[$locale] = [
                'title' => $this->normalizeNullableString(Arr::get($translation, 'title')),
                'subtitle' => $this->normalizeNullableString(Arr::get($translation, 'subtitle')),
                'short' => $this->normalizeNullableString(Arr::get($translation, 'short')),
                'description' => $this->normalizeNullableText(Arr::get($translation, 'description')),
            ];
        }

        $this->merge([
            'market_attribute_group_id' => $this->filled('market_attribute_group_id')
                ? (int) $this->input('market_attribute_group_id')
                : null,

            'user_id' => $this->filled('user_id')
                ? (int) $this->input('user_id')
                : $this->user()?->id,

            'code' => $this->filled('code')
                ? Str::slug(trim((string) $this->input('code')))
                : null,

            'icon' => $this->normalizeNullableText($this->input('icon')),
            'color' => $this->normalizeNullableString($this->input('color')),
            'type' => $this->normalizeNullableString($this->input('type')) ?: 'string',
            'unit' => $this->normalizeNullableString($this->input('unit')),

            'required' => filter_var($this->input('required', false), FILTER_VALIDATE_BOOLEAN),
            'filterable' => filter_var($this->input('filterable', false), FILTER_VALIDATE_BOOLEAN),
            'visible' => filter_var($this->input('visible', true), FILTER_VALIDATE_BOOLEAN),

            'sort' => $this->filled('sort') ? (int) $this->input('sort') : 0,
            'activity' => filter_var($this->input('activity', false), FILTER_VALIDATE_BOOLEAN),

            'status' => $this->normalizeNullableString($this->input('status')) ?: 'draft',

            'moderation_status' => $this->filled('moderation_status')
                ? (int) $this->input('moderation_status')
                : 0,

            'moderated_by' => $this->filled('moderated_by') ? (int) $this->input('moderated_by') : null,
            'moderated_at' => $this->filled('moderated_at') ? $this->input('moderated_at') : null,
            'moderation_note' => $this->normalizeNullableText($this->input('moderation_note')),

            'published_at' => $this->filled('published_at') ? $this->input('published_at') : null,
            'show_from_at' => $this->filled('show_from_at') ? $this->input('show_from_at') : null,
            'show_to_at' => $this->filled('show_to_at') ? $this->input('show_to_at') : null,

            'translations' => $preparedTranslations,
        ]);
    }

    public function rules(): array
    {
        $attributeId = $this->route('marketAttribute')?->id
            ?? $this->route('marketAttribute')
            ?? $this->route('id');

        $availableLocales = config('app.available_locales', ['ru']);

        return [
                'market_attribute_group_id' => [
                    'required',
                    'integer',
                    'exists:market_attribute_groups,id',
                ],

                'user_id' => ['required', 'integer', 'exists:users,id'],

                'code' => [
                    'required',
                    'string',
                    'max:100',
                    'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                    Rule::unique('market_attributes', 'code')->ignore($attributeId),
                ],

                'icon' => ['nullable', 'string'],
                'color' => ['nullable', 'string', 'max:50'],

                'type' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::in([
                        'string',
                        'text',
                        'integer',
                        'decimal',
                        'boolean',
                        'date',
                        'datetime',
                        'select',
                        'multiselect',
                    ]),
                ],

                'unit' => ['nullable', 'string', 'max:50'],

                'required' => ['nullable', 'boolean'],
                'filterable' => ['nullable', 'boolean'],
                'visible' => ['nullable', 'boolean'],

                'sort' => ['nullable', 'integer', 'min:0'],
                'activity' => ['nullable', 'boolean'],

                'status' => [
                    'nullable',
                    'string',
                    'max:50',
                    Rule::in(['draft', 'published', 'archived']),
                ],

                'moderation_status' => [
                    'nullable',
                    'integer',
                    Rule::in([0, 1, 2]),
                ],

                'moderated_by' => ['nullable', 'integer', 'exists:users,id'],
                'moderated_at' => ['nullable', 'date'],
                'moderation_note' => ['nullable', 'string'],

                'published_at' => ['nullable', 'date'],
                'show_from_at' => ['nullable', 'date'],
                'show_to_at' => ['nullable', 'date', 'after_or_equal:show_from_at'],

                'translations' => ['required', 'array', 'min:1'],
                'translations.*' => ['required', 'array'],

                'translations.*.title' => ['required', 'string', 'max:255'],
                'translations.*.subtitle' => ['nullable', 'string', 'max:255'],
                'translations.*.short' => ['nullable', 'string', 'max:255'],
                'translations.*.description' => ['nullable', 'string'],
            ] + $this->localeRules($availableLocales);
    }

    public function messages(): array
    {
        return [
            'market_attribute_group_id.required' => 'Необходимо выбрать группу характеристики.',
            'market_attribute_group_id.exists' => 'Указанная группа характеристик не найдена.',

            'user_id.required' => 'Необходимо указать создателя характеристики.',
            'user_id.exists' => 'Указанный пользователь не найден.',

            'code.required' => 'Поле системного кода обязательно для заполнения.',
            'code.max' => 'Системный код не должен превышать 100 символов.',
            'code.regex' => 'Системный код может содержать только строчные латинские буквы, цифры и дефисы.',
            'code.unique' => 'Характеристика с таким системным кодом уже существует.',

            'color.max' => 'Цвет характеристики не должен превышать 50 символов.',

            'type.required' => 'Необходимо выбрать тип характеристики.',
            'type.in' => 'Недопустимый тип характеристики.',
            'unit.max' => 'Единица измерения не должна превышать 50 символов.',

            'required.boolean' => 'Поле обязательности должно быть логическим значением.',
            'filterable.boolean' => 'Поле фильтрации должно быть логическим значением.',
            'visible.boolean' => 'Поле видимости должно быть логическим значением.',

            'sort.integer' => 'Поле сортировки должно быть числом.',
            'sort.min' => 'Поле сортировки не может быть меньше 0.',

            'activity.boolean' => 'Поле активности должно быть логическим значением.',

            'status.in' => 'Недопустимое значение статуса публикации.',

            'moderation_status.in' => 'Недопустимое значение статуса модерации.',
            'moderated_by.exists' => 'Указанный модератор не найден.',
            'moderated_at.date' => 'Дата модерации имеет неверный формат.',

            'published_at.date' => 'Дата публикации имеет неверный формат.',
            'show_from_at.date' => 'Дата начала показа имеет неверный формат.',
            'show_to_at.date' => 'Дата окончания показа имеет неверный формат.',
            'show_to_at.after_or_equal' => 'Дата окончания показа не может быть раньше даты начала показа.',

            'translations.required' => 'Необходимо добавить хотя бы один перевод.',
            'translations.array' => 'Поле переводов должно быть массивом.',
            'translations.min' => 'Необходимо добавить хотя бы одну локаль перевода.',

            'translations.*.title.required' => 'Название характеристики обязательно для каждой добавленной локали.',
            'translations.*.title.max' => 'Название характеристики не должно превышать 255 символов.',
            'translations.*.subtitle.max' => 'Подзаголовок не должен превышать 255 символов.',
            'translations.*.short.max' => 'Краткое описание не должно превышать 255 символов.',
        ];
    }

    public function attributes(): array
    {
        return [
            'market_attribute_group_id' => 'Группа характеристики',
            'user_id' => 'Создатель',
            'code' => 'Системный код',
            'icon' => 'Иконка',
            'color' => 'Цвет',
            'type' => 'Тип',
            'unit' => 'Единица измерения',
            'required' => 'Обязательное поле',
            'filterable' => 'Фильтрация',
            'visible' => 'Видимость',
            'sort' => 'Сортировка',
            'activity' => 'Активность',
            'status' => 'Статус публикации',
            'moderation_status' => 'Статус модерации',
            'moderated_by' => 'Модератор',
            'moderated_at' => 'Дата модерации',
            'moderation_note' => 'Комментарий модератора',
            'published_at' => 'Дата публикации',
            'show_from_at' => 'Начало показа',
            'show_to_at' => 'Окончание показа',
            'translations' => 'Переводы',
        ];
    }

    protected function localeRules(array $availableLocales): array
    {
        $rules = [];

        foreach ($availableLocales as $locale) {
            $rules["translations.$locale"] = ['sometimes', 'array'];
        }

        return $rules;
    }

    protected function normalizeNullableString(mixed $value): ?string
    {
        if (is_null($value)) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    protected function normalizeNullableText(mixed $value): ?string
    {
        if (is_null($value)) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
