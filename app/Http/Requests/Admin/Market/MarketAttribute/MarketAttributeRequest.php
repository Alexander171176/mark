<?php

namespace App\Http\Requests\Admin\Market\MarketAttribute;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class MarketAttributeRequest extends FormRequest
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
         * Переводы характеристики.
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
             * Основные связи.
             */
            'market_attribute_group_id' =>
                $this->filled('market_attribute_group_id')
                    ? (int) $this->input(
                    'market_attribute_group_id'
                )
                    : null,

            'user_id' => $this->filled('user_id')
                ? (int) $this->input('user_id')
                : $this->user()?->id,

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

            'type' => $this->normalizeNullableString(
                $this->input('type')
            ) ?: 'string',

            'unit' => $this->normalizeNullableString(
                $this->input('unit')
            ),

            /*
             * Настройки характеристики.
             */
            'required' => filter_var(
                $this->input('required', false),
                FILTER_VALIDATE_BOOLEAN
            ),

            'filterable' => filter_var(
                $this->input('filterable', false),
                FILTER_VALIDATE_BOOLEAN
            ),

            'use_for_variants' => filter_var(
                $this->input('use_for_variants', false),
                FILTER_VALIDATE_BOOLEAN
            ),

            'visible' => filter_var(
                $this->input('visible', true),
                FILTER_VALIDATE_BOOLEAN
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
             * Период публикации.
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
        $attributeId = $this->resolveAttributeId();

        $availableLocales = config(
            'app.available_locales',
            ['ru']
        );

        return [
                /*
                 * Основные связи.
                 */
                'market_attribute_group_id' => [
                    'required',
                    'integer',

                    Rule::exists(
                        'market_attribute_groups',
                        'id'
                    ),
                ],

                'user_id' => [
                    'required',
                    'integer',

                    Rule::exists(
                        'users',
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
                        'market_attributes',
                        'code'
                    )->ignore($attributeId),
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

                'unit' => [
                    'nullable',
                    'string',
                    'max:50',
                ],

                /*
                 * Настройки характеристики.
                 */
                'required' => [
                    'nullable',
                    'boolean',
                ],

                'filterable' => [
                    'nullable',
                    'boolean',
                ],

                'use_for_variants' => [
                    'nullable',
                    'boolean',
                ],

                'visible' => [
                    'nullable',
                    'boolean',
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
                 * Период публикации.
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
     * Дополнительная проверка характеристики.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (
            Validator $validator
        ): void {
            $this->validateVariantConfiguration(
                $validator
            );

            $this->validateExistingVariantUsage(
                $validator
            );
        });
    }

    /**
     * Проверить настройки характеристики,
     * используемой для формирования вариантов.
     */
    protected function validateVariantConfiguration(
        Validator $validator
    ): void {
        if (! $this->boolean('use_for_variants')) {
            return;
        }

        $type = $this->input('type');

        /*
         * Варианты формируются на основе справочных значений.
         *
         * Например:
         * Цвет → Чёрный;
         * Размер → XL.
         *
         * Поэтому свободный текст, число или дата
         * для формирования варианта не подходят.
         */
        if (! in_array($type, [
            'select',
            'multiselect',
        ], true)) {
            $validator->errors()->add(
                'type',
                'Для формирования вариантов можно использовать только характеристику типа select или multiselect.'
            );
        }
    }

    /**
     * Защита характеристики, которая уже используется
     * в вариантах товаров.
     */
    protected function validateExistingVariantUsage(
        Validator $validator
    ): void {
        $attributeId = $this->resolveAttributeId();

        if ($attributeId === null) {
            return;
        }

        $existingAttribute = DB::table(
            'market_attributes'
        )
            ->where('id', $attributeId)
            ->first([
                'use_for_variants',
                'type',
            ]);

        if (! $existingAttribute) {
            return;
        }

        $isUsedInVariants = DB::table(
            'market_product_variant_values'
        )
            ->where(
                'market_attribute_id',
                $attributeId
            )
            ->exists();

        if (! $isUsedInVariants) {
            return;
        }

        /*
         * Нельзя отключить использование для вариантов,
         * пока характеристика участвует хотя бы
         * в одном существующем варианте.
         */
        if (! $this->boolean('use_for_variants')) {
            $validator->errors()->add(
                'use_for_variants',
                'Нельзя отключить использование характеристики для вариантов, пока она применяется в существующих вариантах товаров.'
            );
        }

        /*
         * Нельзя изменить тип используемой характеристики
         * на тип без справочных значений.
         */
        $newType = $this->input('type');

        if (! in_array($newType, [
            'select',
            'multiselect',
        ], true)) {
            $validator->errors()->add(
                'type',
                'Нельзя изменить тип характеристики, используемой в вариантах, на тип без справочных значений.'
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
             * Основные связи.
             */
            'market_attribute_group_id.required' =>
                'Необходимо выбрать группу характеристики.',

            'market_attribute_group_id.integer' =>
                'ID группы характеристики должен быть целым числом.',

            'market_attribute_group_id.exists' =>
                'Указанная группа характеристик не найдена.',

            'user_id.required' =>
                'Необходимо указать создателя характеристики.',

            'user_id.integer' =>
                'ID создателя должен быть целым числом.',

            'user_id.exists' =>
                'Указанный пользователь не найден.',

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
                'Характеристика с таким системным кодом уже существует.',

            'color.max' =>
                'Цвет характеристики не должен превышать 50 символов.',

            'type.required' =>
                'Необходимо выбрать тип характеристики.',

            'type.in' =>
                'Недопустимый тип характеристики.',

            'unit.max' =>
                'Единица измерения не должна превышать 50 символов.',

            /*
             * Настройки характеристики.
             */
            'required.boolean' =>
                'Поле обязательности должно быть логическим значением.',

            'filterable.boolean' =>
                'Поле фильтрации должно быть логическим значением.',

            'use_for_variants.boolean' =>
                'Поле использования для вариантов должно быть логическим значением.',

            'visible.boolean' =>
                'Поле видимости должно быть логическим значением.',

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
             * Период показа.
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
                'Название характеристики обязательно для каждой добавленной локали.',

            'translations.*.title.max' =>
                'Название характеристики не должно превышать 255 символов.',

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
            'market_attribute_group_id' =>
                'группа характеристики',

            'user_id' => 'создатель',

            'code' => 'системный код',
            'icon' => 'иконка',
            'color' => 'цвет',
            'type' => 'тип',
            'unit' => 'единица измерения',

            'required' => 'обязательное поле',
            'filterable' => 'фильтрация',
            'use_for_variants' =>
                'использование для вариантов',

            'visible' => 'видимость',

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
     * Получить ID редактируемой характеристики
     * из параметров маршрута.
     */
    protected function resolveAttributeId(): ?int
    {
        $routeAttribute = $this->route(
            'marketAttribute'
        )
            ?? $this->route('attribute')
            ?? $this->route('id');

        if (
            is_object($routeAttribute)
            && isset($routeAttribute->id)
        ) {
            return (int) $routeAttribute->id;
        }

        return is_numeric($routeAttribute)
            ? (int) $routeAttribute
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
