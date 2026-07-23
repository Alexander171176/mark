<?php

namespace App\Http\Requests\Admin\Market\MarketAttributeValue;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class MarketAttributeValueRequest extends FormRequest
{
    /**
     * Разрешение выполнения запроса.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Подготовка и нормализация данных перед валидацией.
     */
    protected function prepareForValidation(): void
    {
        $supportedLocales = config(
            'app.available_locales',
            ['ru']
        );

        /*
         * Переводы значения характеристики.
         */
        $translations = $this->input(
            'translations',
            []
        );

        if (! is_array($translations)) {
            $translations = [];
        }

        $preparedTranslations = [];

        foreach ($translations as $locale => $translation) {
            if (
                ! in_array($locale, $supportedLocales, true)
                || ! is_array($translation)
            ) {
                continue;
            }

            $preparedTranslations[$locale] = [
                'title' => $this->normalizeNullableString(
                    Arr::get($translation, 'title')
                ),

                'subtitle' => $this->normalizeNullableString(
                    Arr::get($translation, 'subtitle')
                ),

                'short' => $this->normalizeNullableString(
                    Arr::get($translation, 'short')
                ),

                'description' => $this->normalizeNullableText(
                    Arr::get($translation, 'description')
                ),
            ];
        }

        $this->merge([
            /*
             * Родительская характеристика.
             */
            'market_attribute_id' =>
                $this->filled('market_attribute_id')
                    ? (int) $this->input(
                    'market_attribute_id'
                )
                    : null,

            /*
             * Основные данные.
             */
            'code' => $this->filled('code')
                ? Str::slug(
                    trim((string) $this->input('code'))
                )
                : null,

            'icon' => $this->normalizeNullableText(
                $this->input('icon')
            ),

            'color' => $this->normalizeNullableString(
                $this->input('color')
            ),

            /*
             * Отображение.
             */
            'sort' => $this->filled('sort')
                ? max(0, (int) $this->input('sort'))
                : 0,

            'activity' => filter_var(
                $this->input('activity', false),
                FILTER_VALIDATE_BOOLEAN
            ),

            /*
             * Публикация.
             */
            'status' => $this->normalizeNullableString(
                $this->input('status')
            ) ?: 'draft',

            /*
             * Модерация.
             */
            'moderation_status' =>
                $this->filled('moderation_status')
                    ? (int) $this->input(
                    'moderation_status'
                )
                    : 0,

            'moderated_by' =>
                $this->filled('moderated_by')
                    ? (int) $this->input('moderated_by')
                    : null,

            'moderated_at' =>
                $this->filled('moderated_at')
                    ? $this->input('moderated_at')
                    : null,

            'moderation_note' =>
                $this->normalizeNullableText(
                    $this->input('moderation_note')
                ),

            /*
             * Окно публикации.
             */
            'published_at' =>
                $this->filled('published_at')
                    ? $this->input('published_at')
                    : null,

            'show_from_at' =>
                $this->filled('show_from_at')
                    ? $this->input('show_from_at')
                    : null,

            'show_to_at' =>
                $this->filled('show_to_at')
                    ? $this->input('show_to_at')
                    : null,

            /*
             * Переводы.
             */
            'translations' => $preparedTranslations,
        ]);
    }

    /**
     * Правила валидации.
     *
     * Один Request используется для store и update.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $valueId = $this->resolveAttributeValueId();

        $attributeId = $this->filled(
            'market_attribute_id'
        )
            ? (int) $this->input('market_attribute_id')
            : null;

        $availableLocales = config(
            'app.available_locales',
            ['ru']
        );

        return [
                /*
                 * Родительская характеристика.
                 */
                'market_attribute_id' => [
                    'required',
                    'integer',

                    Rule::exists(
                        'market_attributes',
                        'id'
                    ),
                ],

                /*
                 * Основные данные.
                 */
                'code' => [
                    'required',
                    'string',
                    'max:100',
                    'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',

                    Rule::unique(
                        'market_attribute_values',
                        'code'
                    )
                        ->where(
                            fn ($query) => $query->where(
                                'market_attribute_id',
                                $attributeId
                            )
                        )
                        ->ignore($valueId),
                ],

                'icon' => [
                    'nullable',
                    'string',
                ],

                'color' => [
                    'nullable',
                    'string',
                    'max:50',
                ],

                /*
                 * Отображение.
                 */
                'sort' => [
                    'nullable',
                    'integer',
                    'min:0',
                    'max:4294967295',
                ],

                'activity' => [
                    'nullable',
                    'boolean',
                ],

                /*
                 * Статус публикации.
                 */
                'status' => [
                    'nullable',
                    'string',
                    'max:50',

                    Rule::in([
                        'draft',
                        'published',
                        'archived',
                    ]),
                ],

                /*
                 * Модерация.
                 */
                'moderation_status' => [
                    'nullable',
                    'integer',

                    Rule::in([
                        0,
                        1,
                        2,
                    ]),
                ],

                'moderated_by' => [
                    'nullable',
                    'integer',

                    Rule::exists(
                        'users',
                        'id'
                    ),
                ],

                'moderated_at' => [
                    'nullable',
                    'date',
                ],

                'moderation_note' => [
                    'nullable',
                    'string',
                ],

                /*
                 * Окно публикации.
                 */
                'published_at' => [
                    'nullable',
                    'date',
                ],

                'show_from_at' => [
                    'nullable',
                    'date',
                ],

                'show_to_at' => [
                    'nullable',
                    'date',
                    'after_or_equal:show_from_at',
                ],

                /*
                 * Переводы.
                 */
                'translations' => [
                    'required',
                    'array',
                    'min:1',
                ],

                'translations.*' => [
                    'required',
                    'array',
                ],

                'translations.*.title' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'translations.*.subtitle' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'translations.*.short' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'translations.*.description' => [
                    'nullable',
                    'string',
                ],
            ] + $this->localeRules($availableLocales);
    }

    /**
     * Дополнительные проверки значения характеристики.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (
            Validator $validator
        ): void {
            $this->validateParentAttribute($validator);
            $this->validateExistingUsage($validator);
        });
    }

    /**
     * Проверить родительскую характеристику.
     *
     * Справочные значения предназначены только
     * для характеристик типа select и multiselect.
     */
    protected function validateParentAttribute(
        Validator $validator
    ): void {
        $attributeId = $this->input(
            'market_attribute_id'
        );

        if (! is_numeric($attributeId)) {
            return;
        }

        $attribute = DB::table(
            'market_attributes'
        )
            ->where('id', (int) $attributeId)
            ->first([
                'id',
                'type',
            ]);

        if (! $attribute) {
            return;
        }

        if (! in_array($attribute->type, [
            'select',
            'multiselect',
        ], true)) {
            $validator->errors()->add(
                'market_attribute_id',
                'Справочные значения можно создавать только для характеристик типа select или multiselect.'
            );
        }
    }

    /**
     * Защитить значение, уже используемое
     * в товарах или вариантах товаров.
     */
    protected function validateExistingUsage(
        Validator $validator
    ): void {
        $valueId = $this->resolveAttributeValueId();

        if ($valueId === null) {
            return;
        }

        $existingValue = DB::table(
            'market_attribute_values'
        )
            ->where('id', $valueId)
            ->first([
                'market_attribute_id',
            ]);

        if (! $existingValue) {
            return;
        }

        $newAttributeId = $this->input(
            'market_attribute_id'
        );

        if (! is_numeric($newAttributeId)) {
            return;
        }

        /*
         * Родительская характеристика не меняется —
         * дополнительная защита не требуется.
         */
        if (
            (int) $existingValue->market_attribute_id
            === (int) $newAttributeId
        ) {
            return;
        }

        /*
         * Использование значения в обычных
         * характеристиках товаров.
         */
        $isUsedInProducts = DB::table(
            'market_product_attribute_values'
        )
            ->where(
                'market_attribute_value_id',
                $valueId
            )
            ->exists();

        /*
         * Использование значения
         * в вариантах товаров.
         */
        $isUsedInVariants = DB::table(
            'market_product_variant_values'
        )
            ->where(
                'market_attribute_value_id',
                $valueId
            )
            ->exists();

        if ($isUsedInProducts || $isUsedInVariants) {
            $validator->errors()->add(
                'market_attribute_id',
                'Нельзя перенести значение к другой характеристике, пока оно используется в товарах или вариантах товаров.'
            );
        }
    }

    /**
     * Сообщения об ошибках.
     */
    public function messages(): array
    {
        return [
            /*
             * Родительская характеристика.
             */
            'market_attribute_id.required' =>
                'Необходимо выбрать характеристику.',

            'market_attribute_id.integer' =>
                'ID характеристики должен быть целым числом.',

            'market_attribute_id.exists' =>
                'Указанная характеристика не найдена.',

            /*
             * Основные данные.
             */
            'code.required' =>
                'Поле системного кода обязательно для заполнения.',

            'code.max' =>
                'Системный код не должен превышать 100 символов.',

            'code.regex' =>
                'Системный код может содержать только строчные латинские буквы, цифры и дефисы.',

            'code.unique' =>
                'У этой характеристики уже есть значение с таким системным кодом.',

            'color.max' =>
                'Цвет значения не должен превышать 50 символов.',

            /*
             * Отображение.
             */
            'sort.integer' =>
                'Поле сортировки должно быть целым числом.',

            'sort.min' =>
                'Поле сортировки не может быть меньше 0.',

            'activity.boolean' =>
                'Поле активности должно быть логическим значением.',

            /*
             * Публикация и модерация.
             */
            'status.in' =>
                'Недопустимое значение статуса публикации.',

            'moderation_status.in' =>
                'Недопустимое значение статуса модерации.',

            'moderated_by.exists' =>
                'Указанный модератор не найден.',

            'moderated_at.date' =>
                'Дата модерации имеет неверный формат.',

            /*
             * Окно публикации.
             */
            'published_at.date' =>
                'Дата публикации имеет неверный формат.',

            'show_from_at.date' =>
                'Дата начала показа имеет неверный формат.',

            'show_to_at.date' =>
                'Дата окончания показа имеет неверный формат.',

            'show_to_at.after_or_equal' =>
                'Дата окончания показа не может быть раньше даты начала показа.',

            /*
             * Переводы.
             */
            'translations.required' =>
                'Необходимо добавить хотя бы один перевод.',

            'translations.array' =>
                'Поле переводов должно быть массивом.',

            'translations.min' =>
                'Необходимо добавить хотя бы одну локаль перевода.',

            'translations.*.title.required' =>
                'Название значения обязательно для каждой добавленной локали.',

            'translations.*.title.max' =>
                'Название значения не должно превышать 255 символов.',

            'translations.*.subtitle.max' =>
                'Подзаголовок не должен превышать 255 символов.',

            'translations.*.short.max' =>
                'Краткое описание не должно превышать 255 символов.',
        ];
    }

    /**
     * Человекочитаемые названия полей.
     */
    public function attributes(): array
    {
        return [
            'market_attribute_id' => 'характеристика',

            'code' => 'системный код',
            'icon' => 'иконка',
            'color' => 'цвет',

            'sort' => 'сортировка',
            'activity' => 'активность',

            'status' => 'статус публикации',

            'moderation_status' =>
                'статус модерации',

            'moderated_by' => 'модератор',
            'moderated_at' => 'дата модерации',
            'moderation_note' =>
                'комментарий модератора',

            'published_at' => 'дата публикации',
            'show_from_at' => 'начало показа',
            'show_to_at' => 'окончание показа',

            'translations' => 'переводы',
        ];
    }

    /**
     * Получить ID редактируемого значения
     * характеристики из параметров маршрута.
     */
    protected function resolveAttributeValueId(): ?int
    {
        $routeValue = $this->route(
            'marketAttributeValue'
        )
            ?? $this->route('attributeValue')
            ?? $this->route('value')
            ?? $this->route('id');

        if (
            is_object($routeValue)
            && isset($routeValue->id)
        ) {
            return (int) $routeValue->id;
        }

        return is_numeric($routeValue)
            ? (int) $routeValue
            : null;
    }

    /**
     * Дополнительные правила поддерживаемых локалей.
     *
     * @param array<int, string> $availableLocales
     *
     * @return array<string, mixed>
     */
    protected function localeRules(
        array $availableLocales
    ): array {
        $rules = [];

        foreach ($availableLocales as $locale) {
            $rules["translations.$locale"] = [
                'sometimes',
                'array',
            ];
        }

        return $rules;
    }

    /**
     * Нормализация nullable-строки.
     */
    protected function normalizeNullableString(
        mixed $value
    ): ?string {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value !== ''
            ? $value
            : null;
    }

    /**
     * Нормализация nullable-текста.
     */
    protected function normalizeNullableText(
        mixed $value
    ): ?string {
        return $this->normalizeNullableString($value);
    }
}
