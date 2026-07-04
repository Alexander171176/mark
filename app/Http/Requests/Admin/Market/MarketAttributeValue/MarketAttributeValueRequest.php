<?php

namespace App\Http\Requests\Admin\Market\MarketAttributeValue;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MarketAttributeValueRequest extends FormRequest
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
            'market_attribute_id' => $this->filled('market_attribute_id')
                ? (int) $this->input('market_attribute_id')
                : null,

            'code' => $this->filled('code')
                ? Str::slug(trim((string) $this->input('code')))
                : null,

            'icon' => $this->normalizeNullableText($this->input('icon')),
            'color' => $this->normalizeNullableString($this->input('color')),

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
        $valueId = $this->route('marketAttributeValue')?->id
            ?? $this->route('marketAttributeValue')
            ?? $this->route('id');

        $attributeId = (int) $this->input('market_attribute_id');

        $availableLocales = config('app.available_locales', ['ru']);

        return [
                'market_attribute_id' => [
                    'required',
                    'integer',
                    'exists:market_attributes,id',
                ],

                'code' => [
                    'required',
                    'string',
                    'max:100',
                    'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                    Rule::unique('market_attribute_values', 'code')
                        ->where('market_attribute_id', $attributeId)
                        ->ignore($valueId),
                ],

                'icon' => ['nullable', 'string'],
                'color' => ['nullable', 'string', 'max:50'],

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
            'market_attribute_id.required' => 'Необходимо выбрать характеристику.',
            'market_attribute_id.exists' => 'Указанная характеристика не найдена.',

            'code.required' => 'Поле системного кода обязательно для заполнения.',
            'code.max' => 'Системный код не должен превышать 100 символов.',
            'code.regex' => 'Системный код может содержать только строчные латинские буквы, цифры и дефисы.',
            'code.unique' => 'У этой характеристики уже есть значение с таким системным кодом.',

            'color.max' => 'Цвет значения не должен превышать 50 символов.',

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

            'translations.*.title.required' => 'Название значения обязательно для каждой добавленной локали.',
            'translations.*.title.max' => 'Название значения не должно превышать 255 символов.',
            'translations.*.subtitle.max' => 'Подзаголовок не должен превышать 255 символов.',
            'translations.*.short.max' => 'Краткое описание не должно превышать 255 символов.',
        ];
    }

    public function attributes(): array
    {
        return [
            'market_attribute_id' => 'Характеристика',
            'code' => 'Системный код',
            'icon' => 'Иконка',
            'color' => 'Цвет',
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
