<?php

namespace App\Http\Requests\Admin\Market\MarketProductVariant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class MarketProductVariantRequest extends FormRequest
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
         * Переводы варианта.
         */
        $translations = $this->input('translations', []);

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

                'meta_title' => $this->normalizeNullableString(
                    Arr::get($translation, 'meta_title')
                ),

                'meta_keywords' => $this->normalizeNullableString(
                    Arr::get($translation, 'meta_keywords')
                ),

                'meta_desc' => $this->normalizeNullableText(
                    Arr::get($translation, 'meta_desc')
                ),
            ];
        }

        /*
         * Значения характеристик варианта.
         *
         * Поддерживается формат:
         *
         * values: [
         *     {
         *         market_attribute_id: 1,
         *         market_attribute_value_id: 10,
         *         sort: 0
         *     }
         * ]
         */
        $values = $this->normalizeRelationItems(
            value: $this->input('values', []),
            callback: function (
                mixed $item,
                int $index
            ): ?array {
                if (! is_array($item)) {
                    return null;
                }

                $attributeId = Arr::get(
                    $item,
                    'market_attribute_id'
                );

                $attributeValueId = Arr::get(
                    $item,
                    'market_attribute_value_id'
                );

                if (
                    ! is_numeric($attributeId)
                    || ! is_numeric($attributeValueId)
                ) {
                    return null;
                }

                return [
                    'id' => is_numeric(Arr::get($item, 'id'))
                        ? (int) Arr::get($item, 'id')
                        : null,

                    'market_attribute_id' =>
                        (int) $attributeId,

                    'market_attribute_value_id' =>
                        (int) $attributeValueId,

                    'sort' => is_numeric(
                        Arr::get($item, 'sort')
                    )
                        ? max(
                            0,
                            (int) Arr::get($item, 'sort')
                        )
                        : $index,
                ];
            }
        );

        $this->merge([
            /*
             * Родительский товар и валюта.
             */
            'market_product_id' =>
                $this->filled('market_product_id')
                    ? (int) $this->input('market_product_id')
                    : null,

            'currency_id' => $this->filled('currency_id')
                ? (int) $this->input('currency_id')
                : null,

            /*
             * Торговые идентификаторы.
             */
            'code' => $this->normalizeCode(
                $this->input('code')
            ),

            'sku' => $this->normalizeNullableString(
                $this->input('sku')
            ),

            'vendor_code' => $this->normalizeNullableString(
                $this->input('vendor_code')
            ),

            'barcode' => $this->normalizeNullableString(
                $this->input('barcode')
            ),

            /*
             * Собственные цены варианта.
             *
             * Null означает наследование от товара.
             */
            'price' => $this->normalizeNullableNumber(
                $this->input('price')
            ),

            'old_price' => $this->normalizeNullableNumber(
                $this->input('old_price')
            ),

            'purchase_price' => $this->normalizeNullableNumber(
                $this->input('purchase_price')
            ),

            'wholesale_price' =>
                $this->normalizeNullableNumber(
                    $this->input('wholesale_price')
                ),

            'wholesale_min_quantity' =>
                $this->filled('wholesale_min_quantity')
                    ? max(
                    0,
                    (int) $this->input(
                        'wholesale_min_quantity'
                    )
                )
                    : null,

            /*
             * Остаток варианта.
             */
            'quantity' => $this->filled('quantity')
                ? max(
                    0,
                    (int) $this->input('quantity')
                )
                : 0,

            'in_stock' => filter_var(
                $this->input('in_stock', false),
                FILTER_VALIDATE_BOOLEAN
            ),

            /*
             * Физические параметры.
             */
            'weight' => $this->normalizeNullableNumber(
                $this->input('weight')
            ),

            'length' => $this->normalizeNullableNumber(
                $this->input('length')
            ),

            'width' => $this->normalizeNullableNumber(
                $this->input('width')
            ),

            'height' => $this->normalizeNullableNumber(
                $this->input('height')
            ),

            /*
             * Настройки варианта.
             */
            'is_default' => filter_var(
                $this->input('is_default', false),
                FILTER_VALIDATE_BOOLEAN
            ),

            'sort' => $this->filled('sort')
                ? max(
                    0,
                    (int) $this->input('sort')
                )
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
             * Вложенные данные.
             */
            'translations' => $preparedTranslations,
            'values' => $values,
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
        $variantId = $this->resolveVariantId();

        $availableLocales = config(
            'app.available_locales',
            ['ru']
        );

        return [
                /*
                 * Родительский товар.
                 */
                'market_product_id' => [
                    'required',
                    'integer',
                    Rule::exists('market_products', 'id'),
                ],

                'currency_id' => [
                    'nullable',
                    'integer',
                    Rule::exists('currencies', 'id'),
                ],

                /*
                 * Торговые идентификаторы.
                 */
                'code' => [
                    'nullable',
                    'string',
                    'max:255',
                    'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',

                    Rule::unique(
                        'market_product_variants',
                        'code'
                    )
                        ->where(
                            fn ($query) => $query->where(
                                'market_product_id',
                                $this->input('market_product_id')
                            )
                        )
                        ->ignore($variantId),
                ],

                'sku' => [
                    'nullable',
                    'string',
                    'max:100',

                    Rule::unique(
                        'market_product_variants',
                        'sku'
                    )
                        ->where(
                            fn ($query) => $query->where(
                                'market_product_id',
                                $this->input('market_product_id')
                            )
                        )
                        ->ignore($variantId),
                ],

                'vendor_code' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'barcode' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                /*
                 * Цены.
                 *
                 * Все цены nullable, поскольку вариант может
                 * использовать цены родительского товара.
                 */
                'price' => [
                    'nullable',
                    'numeric',
                    'min:0',
                    'max:9999999999999.99',
                ],

                'old_price' => [
                    'nullable',
                    'numeric',
                    'min:0',
                    'max:9999999999999.99',
                ],

                'purchase_price' => [
                    'nullable',
                    'numeric',
                    'min:0',
                    'max:9999999999999.99',
                ],

                'wholesale_price' => [
                    'nullable',
                    'numeric',
                    'min:0',
                    'max:9999999999999.99',
                    'required_with:wholesale_min_quantity',
                ],

                'wholesale_min_quantity' => [
                    'nullable',
                    'integer',
                    'min:1',
                    'max:4294967295',
                    'required_with:wholesale_price',
                ],

                /*
                 * Остаток.
                 */
                'quantity' => [
                    'nullable',
                    'integer',
                    'min:0',
                    'max:4294967295',
                ],

                'in_stock' => [
                    'nullable',
                    'boolean',
                ],

                /*
                 * Физические параметры.
                 */
                'weight' => [
                    'nullable',
                    'numeric',
                    'min:0',
                    'max:9999999.999',
                ],

                'length' => [
                    'nullable',
                    'numeric',
                    'min:0',
                    'max:99999999.99',
                ],

                'width' => [
                    'nullable',
                    'numeric',
                    'min:0',
                    'max:99999999.99',
                ],

                'height' => [
                    'nullable',
                    'numeric',
                    'min:0',
                    'max:99999999.99',
                ],

                /*
                 * Настройки варианта.
                 */
                'is_default' => [
                    'nullable',
                    'boolean',
                ],

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
                    Rule::in([0, 1, 2]),
                ],

                'moderated_by' => [
                    'nullable',
                    'integer',
                    Rule::exists('users', 'id'),
                ],

                'moderated_at' => [
                    'nullable',
                    'date',
                ],

                'moderation_note' => [
                    'nullable',
                    'string',
                    'max:500',
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
                 * Изображения варианта.
                 */
                'images' => [
                    'nullable',
                    'array',
                ],

                'images.*' => [
                    'array',
                ],

                'images.*.id' => [
                    'nullable',
                    'integer',

                    Rule::exists(
                        'market_product_variant_images',
                        'id'
                    ),

                    Rule::prohibitedIf(
                        fn () => $this->isMethod('POST')
                    ),
                ],

                'images.*.order' => [
                    'nullable',
                    'integer',
                    'min:0',
                    'max:4294967295',
                ],

                'images.*.alt' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'images.*.caption' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'images.*.file' => [
                    'nullable',
                    'required_without:images.*.id',
                    'file',
                    'image',
                    'mimes:jpeg,jpg,png,gif,svg,webp',
                    'max:10240',
                ],

                /*
                 * Удаляемые изображения при update.
                 */
                'deletedImages' => [
                    'sometimes',
                    'array',
                ],

                'deletedImages.*' => [
                    'integer',
                    'distinct',

                    Rule::exists(
                        'market_product_variant_images',
                        'id'
                    ),
                ],

                /*
                 * Значения характеристик варианта.
                 */
                'values' => [
                    'required',
                    'array',
                    'min:1',
                ],

                'values.*' => [
                    'required',
                    'array',
                ],

                'values.*.id' => [
                    'nullable',
                    'integer',

                    Rule::exists(
                        'market_product_variant_values',
                        'id'
                    ),

                    Rule::prohibitedIf(
                        fn () => $this->isMethod('POST')
                    ),
                ],

                'values.*.market_attribute_id' => [
                    'required',
                    'integer',
                    'distinct',

                    Rule::exists(
                        'market_attributes',
                        'id'
                    ),
                ],

                'values.*.market_attribute_value_id' => [
                    'required',
                    'integer',
                    'distinct',

                    Rule::exists(
                        'market_attribute_values',
                        'id'
                    ),
                ],

                'values.*.sort' => [
                    'nullable',
                    'integer',
                    'min:0',
                    'max:4294967295',
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

                'translations.*.meta_title' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'translations.*.meta_keywords' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'translations.*.meta_desc' => [
                    'nullable',
                    'string',
                ],
            ] + $this->localeRules($availableLocales);
    }

    /**
     * Дополнительные проверки связанных данных.
     */
    public function withValidator(
        Validator $validator
    ): void {
        $validator->after(function (
            Validator $validator
        ): void {
            $this->validateOldPrice($validator);
            $this->validateStock($validator);
            $this->validateVariantValues($validator);
            $this->validateValueCombination($validator);
            $this->validateNestedIdentifiers($validator);
            $this->validateDeletedImages($validator);
        });
    }

    /**
     * Проверить старую цену варианта.
     *
     * Если собственная цена не задана, сравнивать old_price
     * с ценой товара нельзя обычным правилом gt:price,
     * поэтому используем отдельную проверку.
     */
    protected function validateOldPrice(
        Validator $validator
    ): void {
        if (! $this->filled('old_price')) {
            return;
        }

        $oldPrice = (float) $this->input('old_price');

        $effectivePrice = $this->filled('price')
            ? (float) $this->input('price')
            : DB::table('market_products')
                ->where(
                    'id',
                    (int) $this->input('market_product_id')
                )
                ->value('price');

        if (
            $effectivePrice !== null
            && $oldPrice <= (float) $effectivePrice
        ) {
            $validator->errors()->add(
                'old_price',
                'Старая цена должна быть больше текущей цены варианта или родительского товара.'
            );
        }
    }

    /**
     * Проверить согласованность остатка.
     */
    protected function validateStock(
        Validator $validator
    ): void {
        if (
            $this->boolean('in_stock')
            && (int) $this->input('quantity', 0) <= 0
        ) {
            $validator->errors()->add(
                'quantity',
                'Для варианта в наличии количество должно быть больше нуля.'
            );
        }
    }

    /**
     * Проверить характеристики и выбранные значения.
     */
    protected function validateVariantValues(
        Validator $validator
    ): void {
        $values = $this->input('values', []);

        if (! is_array($values)) {
            return;
        }

        foreach ($values as $index => $item) {
            if (! is_array($item)) {
                continue;
            }

            $attributeId = Arr::get(
                $item,
                'market_attribute_id'
            );

            $attributeValueId = Arr::get(
                $item,
                'market_attribute_value_id'
            );

            if (
                ! is_numeric($attributeId)
                || ! is_numeric($attributeValueId)
            ) {
                continue;
            }

            $attribute = DB::table('market_attributes')
                ->select([
                    'id',
                    'use_for_variants',
                    'activity',
                ])
                ->where('id', (int) $attributeId)
                ->first();

            if (! $attribute) {
                continue;
            }

            if (! (bool) $attribute->use_for_variants) {
                $validator->errors()->add(
                    "values.{$index}.market_attribute_id",
                    'Эта характеристика не разрешена для формирования вариантов товара.'
                );
            }

            if (! (bool) $attribute->activity) {
                $validator->errors()->add(
                    "values.{$index}.market_attribute_id",
                    'Нельзя использовать неактивную характеристику.'
                );
            }

            $attributeValue = DB::table(
                'market_attribute_values'
            )
                ->select([
                    'id',
                    'market_attribute_id',
                    'activity',
                ])
                ->where(
                    'id',
                    (int) $attributeValueId
                )
                ->first();

            if (! $attributeValue) {
                continue;
            }

            if (
                (int) $attributeValue->market_attribute_id
                !== (int) $attributeId
            ) {
                $validator->errors()->add(
                    "values.{$index}.market_attribute_value_id",
                    'Выбранное значение не принадлежит указанной характеристике.'
                );
            }

            if (! (bool) $attributeValue->activity) {
                $validator->errors()->add(
                    "values.{$index}.market_attribute_value_id",
                    'Нельзя использовать неактивное значение характеристики.'
                );
            }
        }
    }

    /**
     * Проверить уникальность комбинации характеристик.
     *
     * Например, у одного товара не должно быть двух
     * одинаковых вариантов «Чёрный / XL».
     */
    protected function validateValueCombination(
        Validator $validator
    ): void {
        $values = collect($this->input('values', []))
            ->filter(
                fn ($item) =>
                    is_array($item)
                    && is_numeric(
                        Arr::get(
                            $item,
                            'market_attribute_id'
                        )
                    )
                    && is_numeric(
                        Arr::get(
                            $item,
                            'market_attribute_value_id'
                        )
                    )
            )
            ->map(fn ($item) => [
                'attribute_id' => (int) Arr::get(
                    $item,
                    'market_attribute_id'
                ),

                'value_id' => (int) Arr::get(
                    $item,
                    'market_attribute_value_id'
                ),
            ])
            ->sortBy('attribute_id')
            ->values();

        if ($values->isEmpty()) {
            return;
        }

        $variantId = $this->resolveVariantId();

        $candidateVariantIds = DB::table(
            'market_product_variants'
        )
            ->where(
                'market_product_id',
                (int) $this->input('market_product_id')
            )
            ->when(
                $variantId,
                fn ($query) => $query->where(
                    'id',
                    '!=',
                    $variantId
                )
            )
            ->pluck('id');

        foreach ($candidateVariantIds as $candidateId) {
            $candidateValues = DB::table(
                'market_product_variant_values'
            )
                ->where(
                    'market_product_variant_id',
                    $candidateId
                )
                ->orderBy('market_attribute_id')
                ->get([
                    'market_attribute_id',
                    'market_attribute_value_id',
                ])
                ->map(fn ($item) => [
                    'attribute_id' =>
                        (int) $item->market_attribute_id,

                    'value_id' =>
                        (int) $item
                            ->market_attribute_value_id,
                ])
                ->values();

            if (
                $candidateValues->count()
                === $values->count()
                && $candidateValues->all()
                === $values->all()
            ) {
                $validator->errors()->add(
                    'values',
                    'У выбранного товара уже существует вариант с такой комбинацией характеристик.'
                );

                return;
            }
        }
    }

    /**
     * Проверить, что ID вложенных записей принадлежат
     * редактируемому варианту.
     */
    protected function validateNestedIdentifiers(
        Validator $validator
    ): void {
        $variantId = $this->resolveVariantId();

        if (! $variantId) {
            return;
        }

        foreach (
            $this->input('values', [])
            as $index => $item
        ) {
            $valueId = is_array($item)
                ? Arr::get($item, 'id')
                : null;

            if (! is_numeric($valueId)) {
                continue;
            }

            $belongsToVariant = DB::table(
                'market_product_variant_values'
            )
                ->where('id', (int) $valueId)
                ->where(
                    'market_product_variant_id',
                    $variantId
                )
                ->exists();

            if (! $belongsToVariant) {
                $validator->errors()->add(
                    "values.{$index}.id",
                    'Указанное значение не принадлежит редактируемому варианту.'
                );
            }
        }

        foreach (
            $this->input('images', [])
            as $index => $image
        ) {
            $imageId = is_array($image)
                ? Arr::get($image, 'id')
                : null;

            if (! is_numeric($imageId)) {
                continue;
            }

            $belongsToVariant = DB::table(
                'market_product_variant_has_images'
            )
                ->where(
                    'market_product_variant_id',
                    $variantId
                )
                ->where(
                    'market_product_variant_image_id',
                    (int) $imageId
                )
                ->exists();

            if (! $belongsToVariant) {
                $validator->errors()->add(
                    "images.{$index}.id",
                    'Указанное изображение не принадлежит редактируемому варианту.'
                );
            }
        }
    }

    /**
     * Проверить удаляемые изображения.
     */
    protected function validateDeletedImages(
        Validator $validator
    ): void {
        $variantId = $this->resolveVariantId();

        if (! $variantId) {
            return;
        }

        foreach (
            $this->input('deletedImages', [])
            as $index => $imageId
        ) {
            if (! is_numeric($imageId)) {
                continue;
            }

            $belongsToVariant = DB::table(
                'market_product_variant_has_images'
            )
                ->where(
                    'market_product_variant_id',
                    $variantId
                )
                ->where(
                    'market_product_variant_image_id',
                    (int) $imageId
                )
                ->exists();

            if (! $belongsToVariant) {
                $validator->errors()->add(
                    "deletedImages.{$index}",
                    'Удаляемое изображение не принадлежит редактируемому варианту.'
                );
            }
        }
    }

    /**
     * Правила для поддерживаемых локалей.
     *
     * @param array<int, string> $availableLocales
     * @return array<string, mixed>
     */
    protected function localeRules(
        array $availableLocales
    ): array {
        $rules = [];

        foreach ($availableLocales as $locale) {
            $rules["translations.{$locale}"] = [
                'sometimes',
                'array',
            ];

            $rules["translations.{$locale}.title"] = [
                'required_with:translations.' . $locale,
                'string',
                'max:255',
            ];

            $rules[
            "translations.{$locale}.subtitle"
            ] = [
                'nullable',
                'string',
                'max:255',
            ];

            $rules[
            "translations.{$locale}.short"
            ] = [
                'nullable',
                'string',
                'max:255',
            ];

            $rules[
            "translations.{$locale}.description"
            ] = [
                'nullable',
                'string',
            ];

            $rules[
            "translations.{$locale}.meta_title"
            ] = [
                'nullable',
                'string',
                'max:255',
            ];

            $rules[
            "translations.{$locale}.meta_keywords"
            ] = [
                'nullable',
                'string',
                'max:255',
            ];

            $rules[
            "translations.{$locale}.meta_desc"
            ] = [
                'nullable',
                'string',
            ];
        }

        return $rules;
    }

    /**
     * Сообщения об ошибках.
     */
    public function messages(): array
    {
        return [
            'market_product_id.required' =>
                'Необходимо выбрать родительский товар.',

            'market_product_id.exists' =>
                'Выбранный товар не найден.',

            'currency_id.exists' =>
                'Выбранная валюта не найдена.',

            'code.regex' =>
                'Код варианта может содержать только строчные латинские буквы, цифры и дефисы.',

            'code.unique' =>
                'У выбранного товара уже существует вариант с таким кодом.',

            'sku.unique' =>
                'У выбранного товара уже существует вариант с таким SKU.',

            'old_price.numeric' =>
                'Старая цена должна быть числом.',

            'wholesale_price.required_with' =>
                'Для минимального оптового количества необходимо указать оптовую цену.',

            'wholesale_min_quantity.required_with' =>
                'Для оптовой цены необходимо указать минимальное количество.',

            'show_to_at.after_or_equal' =>
                'Дата окончания показа не может быть раньше даты начала.',

            'images.*.file.required_without' =>
                'Для нового изображения необходимо выбрать файл.',

            'images.*.file.image' =>
                'Загружаемый файл должен быть изображением.',

            'images.*.file.max' =>
                'Размер изображения не должен превышать 10 МБ.',

            'values.required' =>
                'Необходимо выбрать характеристики варианта.',

            'values.min' =>
                'Вариант должен содержать хотя бы одну характеристику.',

            'values.*.market_attribute_id.required' =>
                'Необходимо выбрать характеристику.',

            'values.*.market_attribute_id.distinct' =>
                'Одна характеристика не может повторяться внутри варианта.',

            'values.*.market_attribute_value_id.required' =>
                'Необходимо выбрать значение характеристики.',

            'values.*.market_attribute_value_id.distinct' =>
                'Одно значение характеристики не может повторяться внутри варианта.',

            'translations.required' =>
                'Необходимо добавить хотя бы один перевод.',

            'translations.min' =>
                'Необходимо добавить хотя бы один перевод.',

            'translations.*.title.required' =>
                'Название варианта обязательно для каждого добавленного перевода.',

            'translations.*.title.max' =>
                'Название варианта не должно превышать 255 символов.',
        ];
    }

    /**
     * Получить ID редактируемого варианта из маршрута.
     */
    protected function resolveVariantId(): ?int
    {
        $routeVariant = $this->route(
            'marketProductVariant'
        )
            ?? $this->route('variant');

        if (is_object($routeVariant)) {
            $id = $routeVariant->id
                ?? $routeVariant->getKey();

            return is_numeric($id)
                ? (int) $id
                : null;
        }

        return is_numeric($routeVariant)
            ? (int) $routeVariant
            : null;
    }

    /**
     * Нормализовать массив связей.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function normalizeRelationItems(
        mixed $value,
        callable $callback
    ): array {
        if (! is_array($value)) {
            return [];
        }

        return collect($value)
            ->map(
                fn (
                    mixed $item,
                    int $index
                ) => $callback($item, $index)
            )
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Нормализовать код варианта.
     */
    protected function normalizeCode(
        mixed $value
    ): ?string {
        $value = $this->normalizeNullableString(
            $value
        );

        return $value !== null
            ? Str::slug($value)
            : null;
    }

    /**
     * Нормализовать nullable-строку.
     */
    protected function normalizeNullableString(
        mixed $value
    ): ?string {
        if (
            $value === null
            || is_array($value)
            || is_object($value)
        ) {
            return null;
        }

        $value = trim((string) $value);

        return $value !== ''
            ? $value
            : null;
    }

    /**
     * Нормализовать nullable-текст.
     */
    protected function normalizeNullableText(
        mixed $value
    ): ?string {
        return $this->normalizeNullableString(
            $value
        );
    }

    /**
     * Нормализовать nullable-число.
     */
    protected function normalizeNullableNumber(
        mixed $value
    ): int|float|null {
        if (
            $value === null
            || $value === ''
            || ! is_numeric($value)
        ) {
            return null;
        }

        return $value + 0;
    }
}
