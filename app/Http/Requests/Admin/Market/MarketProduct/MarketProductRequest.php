<?php

namespace App\Http\Requests\Admin\Market\MarketProduct;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class MarketProductRequest extends FormRequest
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
        $supportedLocales = config('app.available_locales', ['ru']);

        /* Переводы */
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
         * Категории товара.
         *
         * Поддерживаются оба варианта:
         *
         * categories: [1, 2]
         *
         * categories: [
         *     ['id' => 1, 'main' => true, 'order' => 0],
         *     ['id' => 2, 'main' => false, 'order' => 1],
         * ]
         */
        $categories = $this->normalizeRelationItems(
            value: $this->input('categories', []),
            callback: function (mixed $item, int $index): ?array {
                if (is_numeric($item)) {
                    return [
                        'id' => (int) $item,
                        'main' => $index === 0,
                        'order' => $index,
                    ];
                }

                if (! is_array($item)) {
                    return null;
                }

                $id = Arr::get($item, 'id');

                if (! is_numeric($id)) {
                    return null;
                }

                return [
                    'id' => (int) $id,

                    'main' => filter_var(
                        Arr::get($item, 'main', false),
                        FILTER_VALIDATE_BOOLEAN
                    ),

                    'order' => is_numeric(Arr::get($item, 'order'))
                        ? max(0, (int) Arr::get($item, 'order'))
                        : $index,
                ];
            }
        );

        /* Теги товара */
        $tags = $this->normalizeRelationItems(
            value: $this->input('tags', []),
            callback: function (mixed $item, int $index): ?array {
                if (is_numeric($item)) {
                    return [
                        'id' => (int) $item,
                        'order' => $index,
                    ];
                }

                if (! is_array($item)) {
                    return null;
                }

                $id = Arr::get($item, 'id');

                if (! is_numeric($id)) {
                    return null;
                }

                return [
                    'id' => (int) $id,

                    'order' => is_numeric(Arr::get($item, 'order'))
                        ? max(0, (int) Arr::get($item, 'order'))
                        : $index,
                ];
            }
        );

        /* Рекомендуемые товары */
        $relatedProducts = $this->normalizeRelationItems(
            value: $this->input('related_products', []),
            callback: function (mixed $item, int $index): ?array {
                if (is_numeric($item)) {
                    return [
                        'id' => (int) $item,
                        'type' => 'related',
                        'order' => $index,
                        'activity' => true,
                    ];
                }

                if (! is_array($item)) {
                    return null;
                }

                $id = Arr::get($item, 'id');

                if (! is_numeric($id)) {
                    return null;
                }

                return [
                    'id' => (int) $id,

                    'type' => $this->normalizeNullableString(
                        Arr::get($item, 'type')
                    ) ?: 'related',

                    'order' => is_numeric(Arr::get($item, 'order'))
                        ? max(0, (int) Arr::get($item, 'order'))
                        : $index,

                    'activity' => filter_var(
                        Arr::get($item, 'activity', true),
                        FILTER_VALIDATE_BOOLEAN
                    ),
                ];
            }
        );

        /* Характеристики товара */
        $attributeValues = $this->normalizeRelationItems(
            value: $this->input('attribute_values', []),
            callback: function (mixed $item, int $index): ?array {
                if (! is_array($item)) {
                    return null;
                }

                $attributeId = Arr::get($item, 'market_attribute_id');

                if (! is_numeric($attributeId)) {
                    return null;
                }

                $attributeValueId = Arr::get(
                    $item,
                    'market_attribute_value_id'
                );

                return [
                    'id' => is_numeric(Arr::get($item, 'id'))
                        ? (int) Arr::get($item, 'id')
                        : null,

                    'market_attribute_id' => (int) $attributeId,

                    'market_attribute_value_id' =>
                        is_numeric($attributeValueId)
                            ? (int) $attributeValueId
                            : null,

                    'value_string' => $this->normalizeNullableString(
                        Arr::get($item, 'value_string')
                    ),

                    'value_number' => $this->normalizeNullableNumber(
                        Arr::get($item, 'value_number')
                    ),

                    /*
                     * Здесь важно сохранить false как настоящее значение,
                     * а не превращать его в null.
                     */
                    'value_boolean' => $this->normalizeNullableBoolean(
                        Arr::get($item, 'value_boolean')
                    ),

                    'value_date' => filled(Arr::get($item, 'value_date'))
                        ? Arr::get($item, 'value_date')
                        : null,

                    'value_json' => is_array(Arr::get($item, 'value_json'))
                        ? Arr::get($item, 'value_json')
                        : null,

                    'unit' => $this->normalizeNullableString(
                        Arr::get($item, 'unit')
                    ),

                    'order' => is_numeric(Arr::get($item, 'order'))
                        ? max(0, (int) Arr::get($item, 'order'))
                        : $index,

                    'activity' => filter_var(
                        Arr::get($item, 'activity', true),
                        FILTER_VALIDATE_BOOLEAN
                    ),
                ];
            }
        );

        $this->merge([
            /* ладелец и основные связи */
            'user_id' => $this->filled('user_id')
                ? (int) $this->input('user_id')
                : $this->user()?->id,

            'market_company_id' => $this->filled('market_company_id')
                ? (int) $this->input('market_company_id')
                : null,

            'market_shop_id' => $this->filled('market_shop_id')
                ? (int) $this->input('market_shop_id')
                : null,

            'market_brand_id' => $this->filled('market_brand_id')
                ? (int) $this->input('market_brand_id')
                : null,

            'currency_id' => $this->filled('currency_id')
                ? (int) $this->input('currency_id')
                : null,

            /*
             * Основные данные
             */
            'url' => $this->filled('url')
                ? Str::slug(trim((string) $this->input('url')))
                : null,

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
             * Цены
             */
            'price' => $this->normalizeNumber(
                $this->input('price'),
                0
            ),

            'old_price' => $this->normalizeNullableNumber(
                $this->input('old_price')
            ),

            'purchase_price' => $this->normalizeNullableNumber(
                $this->input('purchase_price')
            ),

            'wholesale_price' => $this->normalizeNullableNumber(
                $this->input('wholesale_price')
            ),

            'wholesale_min_quantity' =>
                $this->filled('wholesale_min_quantity')
                    ? max(
                    0,
                    (int) $this->input('wholesale_min_quantity')
                )
                    : null,

            /*
             * Остаток
             */
            'quantity' => $this->filled('quantity')
                ? max(0, (int) $this->input('quantity'))
                : 0,

            'in_stock' => filter_var(
                $this->input('in_stock', false),
                FILTER_VALIDATE_BOOLEAN
            ),

            /*
             * Размеры
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
             * Отображение
             */
            'sort' => $this->filled('sort')
                ? max(0, (int) $this->input('sort'))
                : 0,

            'activity' => filter_var(
                $this->input('activity', false),
                FILTER_VALIDATE_BOOLEAN
            ),

            'left' => filter_var(
                $this->input('left', false),
                FILTER_VALIDATE_BOOLEAN
            ),

            'main' => filter_var(
                $this->input('main', false),
                FILTER_VALIDATE_BOOLEAN
            ),

            'right' => filter_var(
                $this->input('right', false),
                FILTER_VALIDATE_BOOLEAN
            ),

            'is_new' => filter_var(
                $this->input('is_new', false),
                FILTER_VALIDATE_BOOLEAN
            ),

            'is_hit' => filter_var(
                $this->input('is_hit', false),
                FILTER_VALIDATE_BOOLEAN
            ),

            'is_sale' => filter_var(
                $this->input('is_sale', false),
                FILTER_VALIDATE_BOOLEAN
            ),

            /*
             * Публикация
             */
            'status' => $this->normalizeNullableString(
                $this->input('status')
            ) ?: 'draft',

            /*
             * Модерация
             */
            'moderation_status' => $this->filled('moderation_status')
                ? (int) $this->input('moderation_status')
                : 0,

            'moderated_by' => $this->filled('moderated_by')
                ? (int) $this->input('moderated_by')
                : null,

            'moderated_at' => $this->filled('moderated_at')
                ? $this->input('moderated_at')
                : null,

            'moderation_note' => $this->normalizeNullableText(
                $this->input('moderation_note')
            ),

            /*
             * Период публикации
             */
            'published_at' => $this->filled('published_at')
                ? $this->input('published_at')
                : null,

            'show_from_at' => $this->filled('show_from_at')
                ? $this->input('show_from_at')
                : null,

            'show_to_at' => $this->filled('show_to_at')
                ? $this->input('show_to_at')
                : null,

            /*
             * Счётчики
             */
            'views' => $this->filled('views')
                ? max(0, (int) $this->input('views'))
                : 0,

            'likes_count' => $this->filled('likes_count')
                ? max(0, (int) $this->input('likes_count'))
                : 0,

            'rating_avg' => $this->filled('rating_avg')
                ? (float) $this->input('rating_avg')
                : 0,

            'rating_count' => $this->filled('rating_count')
                ? max(0, (int) $this->input('rating_count'))
                : 0,

            /*
             * Вложенные данные
             */
            'translations' => $preparedTranslations,
            'categories' => $categories,
            'tags' => $tags,
            'related_products' => $relatedProducts,
            'attribute_values' => $attributeValues,
        ]);
    }

    /**
     * Правила валидации.
     *
     * Один Request используется одновременно для store и update.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $productId = $this->resolveProductId();

        $availableLocales = config(
            'app.available_locales',
            ['ru']
        );

        return [
                /* Владелец и основные связи */
                'user_id' => [
                    'required',
                    'integer',
                    Rule::exists('users', 'id'),
                ],

                'market_company_id' => [
                    'nullable',
                    'integer',
                    Rule::exists('market_companies', 'id'),
                ],

                'market_shop_id' => [
                    'nullable',
                    'integer',
                    Rule::exists('market_shops', 'id'),
                ],

                'market_brand_id' => [
                    'nullable',
                    'integer',
                    Rule::exists('market_brands', 'id'),
                ],

                'currency_id' => [
                    'nullable',
                    'integer',
                    Rule::exists('currencies', 'id'),
                ],

                /* Основные данные товара */
                'url' => [
                    'required',
                    'string',
                    'max:500',
                    'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',

                    Rule::unique('market_products', 'url')
                        ->where(
                            fn ($query) => $query->where(
                                'user_id',
                                $this->input('user_id')
                            )
                        )
                        ->ignore($productId),
                ],

                'sku' => [
                    'nullable',
                    'string',
                    'max:100',
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

                /* Цены */
                'price' => [
                    'required',
                    'numeric',
                    'min:0',
                    'max:9999999999999.99',
                ],

                'old_price' => [
                    'nullable',
                    'numeric',
                    'min:0',
                    'max:9999999999999.99',
                    'gt:price',
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
                    'required_with:wholesale_price',
                ],

                /* Остаток */
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

                /* Физические параметры */
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

                /* Отображение */
                'sort' => [
                    'nullable',
                    'integer',
                    'min:0',
                ],

                'activity' => [
                    'nullable',
                    'boolean',
                ],

                'left' => [
                    'nullable',
                    'boolean',
                ],

                'main' => [
                    'nullable',
                    'boolean',
                ],

                'right' => [
                    'nullable',
                    'boolean',
                ],

                'is_new' => [
                    'nullable',
                    'boolean',
                ],

                'is_hit' => [
                    'nullable',
                    'boolean',
                ],

                'is_sale' => [
                    'nullable',
                    'boolean',
                ],

                /* Статус публикации */
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

                /* Модерация */
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
                ],

                /* Период публикации */
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

                /* Счётчики */
                'views' => [
                    'nullable',
                    'integer',
                    'min:0',
                ],

                'likes_count' => [
                    'nullable',
                    'integer',
                    'min:0',
                ],

                'rating_avg' => [
                    'nullable',
                    'numeric',
                    'between:0,5',
                ],

                'rating_count' => [
                    'nullable',
                    'integer',
                    'min:0',
                ],

                /* Изображения */
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
                        'market_product_images',
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

                'deletedImages' => [
                    'sometimes',
                    'array',
                ],

                'deletedImages.*' => [
                    'integer',
                    'distinct',
                    Rule::exists(
                        'market_product_images',
                        'id'
                    ),
                ],

                /* Категории */
                'categories' => [
                    'nullable',
                    'array',
                ],

                'categories.*' => [
                    'required',
                    'array',
                ],

                'categories.*.id' => [
                    'required',
                    'integer',
                    'distinct',
                    Rule::exists(
                        'market_categories',
                        'id'
                    ),
                ],

                'categories.*.main' => [
                    'nullable',
                    'boolean',
                ],

                'categories.*.order' => [
                    'nullable',
                    'integer',
                    'min:0',
                ],

                /* Теги */
                'tags' => [
                    'nullable',
                    'array',
                ],

                'tags.*' => [
                    'required',
                    'array',
                ],

                'tags.*.id' => [
                    'required',
                    'integer',
                    'distinct',
                    Rule::exists(
                        'market_tags',
                        'id'
                    ),
                ],

                'tags.*.order' => [
                    'nullable',
                    'integer',
                    'min:0',
                ],

                /* Рекомендуемые товары */
                'related_products' => [
                    'nullable',
                    'array',
                ],

                'related_products.*' => [
                    'required',
                    'array',
                ],

                'related_products.*.id' => [
                    'required',
                    'integer',
                    'distinct',

                    Rule::exists(
                        'market_products',
                        'id'
                    ),

                    Rule::notIn(
                        $productId ? [(int) $productId] : []
                    ),
                ],

                'related_products.*.type' => [
                    'nullable',
                    'string',
                    'max:50',

                    Rule::in([
                        'related',
                        'similar',
                        'accessory',
                        'analog',
                    ]),
                ],

                'related_products.*.order' => [
                    'nullable',
                    'integer',
                    'min:0',
                ],

                'related_products.*.activity' => [
                    'nullable',
                    'boolean',
                ],

                /* Характеристики товара */
                'attribute_values' => [
                    'nullable',
                    'array',
                ],

                'attribute_values.*' => [
                    'required',
                    'array',
                ],

                'attribute_values.*.id' => [
                    'nullable',
                    'integer',

                    Rule::exists(
                        'market_product_attribute_values',
                        'id'
                    ),

                    Rule::prohibitedIf(
                        fn () => $this->isMethod('POST')
                    ),
                ],

                'attribute_values.*.market_attribute_id' => [
                    'required',
                    'integer',
                    'distinct',

                    Rule::exists(
                        'market_attributes',
                        'id'
                    ),
                ],

                'attribute_values.*.market_attribute_value_id' => [
                    'nullable',
                    'integer',

                    Rule::exists(
                        'market_attribute_values',
                        'id'
                    ),
                ],

                'attribute_values.*.value_string' => [
                    'nullable',
                    'string',
                    'max:500',
                ],

                'attribute_values.*.value_number' => [
                    'nullable',
                    'numeric',
                    'min:-99999999999.9999',
                    'max:99999999999.9999',
                ],

                'attribute_values.*.value_boolean' => [
                    'nullable',
                    'boolean',
                ],

                'attribute_values.*.value_date' => [
                    'nullable',
                    'date',
                ],

                'attribute_values.*.value_json' => [
                    'nullable',
                    'array',
                ],

                'attribute_values.*.unit' => [
                    'nullable',
                    'string',
                    'max:50',
                ],

                'attribute_values.*.order' => [
                    'nullable',
                    'integer',
                    'min:0',
                ],

                'attribute_values.*.activity' => [
                    'nullable',
                    'boolean',
                ],

                /* Переводы */
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

    /** Дополнительная проверка связанных сущностей. */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $this->validateMainCategory($validator);
            $this->validateShopCompany($validator);
            $this->validateAttributeValues($validator);
        });
    }

    /** Сообщения об ошибках. */
    public function messages(): array
    {
        return [
            /*
             * Владелец и связи
             */
            'user_id.required' =>
                'Необходимо указать владельца товара.',

            'user_id.exists' =>
                'Указанный пользователь не найден.',

            'market_company_id.exists' =>
                'Указанная компания не найдена.',

            'market_shop_id.exists' =>
                'Указанный магазин не найден.',

            'market_brand_id.exists' =>
                'Указанный бренд не найден.',

            'currency_id.exists' =>
                'Указанная валюта не найдена.',

            /* Основные данные */
            'url.required' =>
                'Поле URL обязательно для заполнения.',

            'url.max' =>
                'URL товара не должен превышать 500 символов.',

            'url.regex' =>
                'URL может содержать только строчные латинские буквы, цифры и дефисы.',

            'url.unique' =>
                'Товар с таким URL уже существует у данного владельца.',

            'sku.max' =>
                'Внутренний артикул не должен превышать 100 символов.',

            'vendor_code.max' =>
                'Артикул производителя не должен превышать 100 символов.',

            'barcode.max' =>
                'Штрихкод не должен превышать 100 символов.',

            /* Цены */
            'price.required' =>
                'Необходимо указать цену товара.',

            'price.numeric' =>
                'Цена товара должна быть числом.',

            'price.min' =>
                'Цена товара не может быть отрицательной.',

            'old_price.numeric' =>
                'Старая цена должна быть числом.',

            'old_price.gt' =>
                'Старая цена должна быть больше текущей цены.',

            'purchase_price.numeric' =>
                'Закупочная цена должна быть числом.',

            'wholesale_price.numeric' =>
                'Оптовая цена должна быть числом.',

            'wholesale_price.required_with' =>
                'Укажите оптовую цену при заполненном минимальном количестве.',

            'wholesale_min_quantity.required_with' =>
                'Укажите минимальное количество для оптовой цены.',

            'wholesale_min_quantity.min' =>
                'Минимальное оптовое количество должно быть не меньше 1.',

            /* Остаток */
            'quantity.integer' =>
                'Количество товара должно быть целым числом.',

            'quantity.min' =>
                'Количество товара не может быть отрицательным.',

            'in_stock.boolean' =>
                'Поле наличия должно быть логическим значением.',

            /* Физические параметры */
            'weight.numeric' =>
                'Вес товара должен быть числом.',

            'length.numeric' =>
                'Длина товара должна быть числом.',

            'width.numeric' =>
                'Ширина товара должна быть числом.',

            'height.numeric' =>
                'Высота товара должна быть числом.',

            /* Отображение */
            'sort.integer' =>
                'Поле сортировки должно быть целым числом.',

            'sort.min' =>
                'Сортировка не может быть меньше 0.',

            'activity.boolean' =>
                'Поле активности должно быть логическим значением.',

            'left.boolean' =>
                'Поле left должно быть логическим значением.',

            'main.boolean' =>
                'Поле main должно быть логическим значением.',

            'right.boolean' =>
                'Поле right должно быть логическим значением.',

            'is_new.boolean' =>
                'Поле новинки должно быть логическим значением.',

            'is_hit.boolean' =>
                'Поле хита продаж должно быть логическим значением.',

            'is_sale.boolean' =>
                'Поле распродажи должно быть логическим значением.',

            /* Публикация и модерация */
            'status.in' =>
                'Недопустимый статус публикации товара.',

            'moderation_status.in' =>
                'Недопустимый статус модерации товара.',

            'moderated_by.exists' =>
                'Указанный модератор не найден.',

            'moderated_at.date' =>
                'Дата модерации имеет неверный формат.',

            'published_at.date' =>
                'Дата публикации имеет неверный формат.',

            'show_from_at.date' =>
                'Дата начала показа имеет неверный формат.',

            'show_to_at.date' =>
                'Дата окончания показа имеет неверный формат.',

            'show_to_at.after_or_equal' =>
                'Дата окончания показа не может быть раньше даты начала показа.',

            /* Изображения */
            'images.array' =>
                'Изображения должны быть массивом.',

            'images.*.id.exists' =>
                'Одно из изображений товара не найдено.',

            'images.*.id.prohibited_if' =>
                'При создании товара нельзя передавать существующий ID изображения.',

            'images.*.order.integer' =>
                'Порядок изображения должен быть целым числом.',

            'images.*.order.min' =>
                'Порядок изображения не может быть меньше 0.',

            'images.*.alt.max' =>
                'Alt-текст изображения не должен превышать 255 символов.',

            'images.*.caption.max' =>
                'Подпись изображения не должна превышать 255 символов.',

            'images.*.file.required_without' =>
                'Необходимо загрузить файл изображения.',

            'images.*.file.image' =>
                'Загруженный файл должен быть изображением.',

            'images.*.file.mimes' =>
                'Допустимые форматы изображения: JPEG, JPG, PNG, GIF, SVG и WEBP.',

            'images.*.file.max' =>
                'Размер изображения не должен превышать 10 МБ.',

            'deletedImages.*.exists' =>
                'Одно из удаляемых изображений не найдено.',

            /* Категории */
            'categories.array' =>
                'Категории должны быть массивом.',

            'categories.*.id.required' =>
                'Необходимо указать категорию товара.',

            'categories.*.id.exists' =>
                'Одна из выбранных категорий не найдена.',

            'categories.*.id.distinct' =>
                'Категории товара не должны повторяться.',

            'categories.*.main.boolean' =>
                'Признак основной категории должен быть логическим значением.',

            'categories.*.order.integer' =>
                'Порядок категории должен быть целым числом.',

            /* Теги */
            'tags.array' =>
                'Теги должны быть массивом.',

            'tags.*.id.required' =>
                'Необходимо указать тег товара.',

            'tags.*.id.exists' =>
                'Один из выбранных тегов не найден.',

            'tags.*.id.distinct' =>
                'Теги товара не должны повторяться.',

            /* Рекомендуемые товары */
            'related_products.array' =>
                'Рекомендуемые товары должны быть массивом.',

            'related_products.*.id.required' =>
                'Необходимо указать рекомендуемый товар.',

            'related_products.*.id.exists' =>
                'Один из рекомендуемых товаров не найден.',

            'related_products.*.id.distinct' =>
                'Рекомендуемые товары не должны повторяться.',

            'related_products.*.id.not_in' =>
                'Товар нельзя рекомендовать самому себе.',

            'related_products.*.type.in' =>
                'Недопустимый тип связи рекомендуемого товара.',

            /* Характеристики */
            'attribute_values.array' =>
                'Характеристики товара должны быть массивом.',

            'attribute_values.*.id.exists' =>
                'Одно из значений характеристики товара не найдено.',

            'attribute_values.*.market_attribute_id.required' =>
                'Необходимо указать характеристику товара.',

            'attribute_values.*.market_attribute_id.exists' =>
                'Одна из характеристик товара не найдена.',

            'attribute_values.*.market_attribute_id.distinct' =>
                'Одна характеристика не может быть добавлена товару несколько раз.',

            'attribute_values.*.market_attribute_value_id.exists' =>
                'Выбранное значение характеристики не найдено.',

            'attribute_values.*.value_string.max' =>
                'Строковое значение характеристики не должно превышать 500 символов.',

            'attribute_values.*.value_number.numeric' =>
                'Числовое значение характеристики должно быть числом.',

            'attribute_values.*.value_boolean.boolean' =>
                'Логическое значение характеристики имеет неверный формат.',

            'attribute_values.*.value_date.date' =>
                'Дата характеристики имеет неверный формат.',

            'attribute_values.*.value_json.array' =>
                'JSON-значение характеристики должно быть массивом.',

            'attribute_values.*.unit.max' =>
                'Единица измерения не должна превышать 50 символов.',

            /* Переводы */
            'translations.required' =>
                'Необходимо добавить хотя бы один перевод товара.',

            'translations.array' =>
                'Поле переводов должно быть массивом.',

            'translations.min' =>
                'Необходимо добавить хотя бы одну локаль перевода.',

            'translations.*.title.required' =>
                'Название товара обязательно для каждой добавленной локали.',

            'translations.*.title.max' =>
                'Название товара не должно превышать 255 символов.',

            'translations.*.subtitle.max' =>
                'Подзаголовок товара не должен превышать 255 символов.',

            'translations.*.short.max' =>
                'Краткое описание товара не должно превышать 255 символов.',

            'translations.*.meta_title.max' =>
                'Meta title не должен превышать 255 символов.',

            'translations.*.meta_keywords.max' =>
                'Meta keywords не должны превышать 255 символов.',
        ];
    }

    /**
     * Проверка основной категории.
     *
     * Если у товара выбраны категории, ровно одна должна быть основной.
     */
    protected function validateMainCategory(
        Validator $validator
    ): void {
        $categories = $this->input('categories', []);

        if (! is_array($categories) || $categories === []) {
            return;
        }

        $mainCategoriesCount = collect($categories)
            ->filter(
                fn (mixed $category) =>
                    is_array($category)
                    && filter_var(
                        Arr::get($category, 'main', false),
                        FILTER_VALIDATE_BOOLEAN
                    )
            )
            ->count();

        if ($mainCategoriesCount !== 1) {
            $validator->errors()->add(
                'categories',
                'Для товара должна быть выбрана ровно одна основная категория.'
            );
        }
    }

    /** Магазин должен относиться к выбранной компании. */
    protected function validateShopCompany(
        Validator $validator
    ): void {
        $companyId = $this->input('market_company_id');
        $shopId = $this->input('market_shop_id');

        if (! $companyId || ! $shopId) {
            return;
        }

        $belongsToCompany = DB::table('market_shops')
            ->where('id', $shopId)
            ->where('market_company_id', $companyId)
            ->exists();

        if (! $belongsToCompany) {
            $validator->errors()->add(
                'market_shop_id',
                'Выбранный магазин не относится к указанной компании.'
            );
        }
    }

    /**
     * Проверка значений характеристик.
     *
     * Для одной характеристики должно быть заполнено ровно одно
     * фактическое значение:
     *
     * - справочное значение;
     * - строка;
     * - число;
     * - boolean;
     * - дата;
     * - JSON.
     */
    protected function validateAttributeValues(
        Validator $validator
    ): void {
        $items = $this->input('attribute_values', []);

        if (! is_array($items)) {
            return;
        }

        foreach ($items as $index => $item) {
            if (! is_array($item)) {
                continue;
            }

            $values = [
                Arr::get($item, 'market_attribute_value_id'),
                Arr::get($item, 'value_string'),
                Arr::get($item, 'value_number'),
                Arr::get($item, 'value_boolean'),
                Arr::get($item, 'value_date'),
                Arr::get($item, 'value_json'),
            ];

            $filledValuesCount = collect($values)
                ->filter(
                    fn (mixed $value) =>
                        $value !== null
                        && $value !== ''
                        && $value !== []
                )
                ->count();

            if ($filledValuesCount === 0) {
                $validator->errors()->add(
                    "attribute_values.$index",
                    'Необходимо указать значение характеристики товара.'
                );

                continue;
            }

            if ($filledValuesCount > 1) {
                $validator->errors()->add(
                    "attribute_values.$index",
                    'Для характеристики можно указать только одно значение.'
                );
            }

            $attributeId = Arr::get(
                $item,
                'market_attribute_id'
            );

            $attributeValueId = Arr::get(
                $item,
                'market_attribute_value_id'
            );

            if (! $attributeId || ! $attributeValueId) {
                continue;
            }

            /*
             * Готовое справочное значение должно принадлежать
             * выбранной характеристике.
             */
            $belongsToAttribute = DB::table(
                'market_attribute_values'
            )
                ->where('id', $attributeValueId)
                ->where('market_attribute_id', $attributeId)
                ->exists();

            if (! $belongsToAttribute) {
                $validator->errors()->add(
                    "attribute_values.$index.market_attribute_value_id",
                    'Выбранное значение не относится к указанной характеристике.'
                );
            }
        }
    }

    /** Получить ID редактируемого товара из маршрута. */
    protected function resolveProductId(): ?int
    {
        $routeProduct = $this->route('marketProduct')
            ?? $this->route('product')
            ?? $this->route('id');

        if (is_object($routeProduct) && isset($routeProduct->id)) {
            return (int) $routeProduct->id;
        }

        return is_numeric($routeProduct)
            ? (int) $routeProduct
            : null;
    }

    /** Дополнительные правила для поддерживаемых локалей. */
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

    /** Нормализация массива связей. */
    protected function normalizeRelationItems(
        mixed $value,
        callable $callback
    ): array {
        if (! is_array($value)) {
            return [];
        }

        $prepared = [];

        foreach (array_values($value) as $index => $item) {
            $normalized = $callback($item, $index);

            if (is_array($normalized)) {
                $prepared[] = $normalized;
            }
        }

        return $prepared;
    }

    /** Нормализация nullable-строки. */
    protected function normalizeNullableString(
        mixed $value
    ): ?string {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === ''
            ? null
            : $value;
    }

    /** Нормализация nullable-текста. */
    protected function normalizeNullableText(
        mixed $value
    ): ?string {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === ''
            ? null
            : $value;
    }

    /** Нормализация обязательного числа. */
    protected function normalizeNumber(
        mixed $value,
        float|int $default = 0
    ): float|int {
        if ($value === null || $value === '') {
            return $default;
        }

        if (! is_numeric($value)) {
            return $value;
        }

        return (float) $value;
    }

    /** Нормализация nullable-числа. */
    protected function normalizeNullableNumber(
        mixed $value
    ): mixed {
        if ($value === null || $value === '') {
            return null;
        }

        return is_numeric($value)
            ? (float) $value
            : $value;
    }

    /**
     * Нормализация nullable boolean.
     *
     * Сохраняет false как реальное значение.
     */
    protected function normalizeNullableBoolean(
        mixed $value
    ): ?bool {
        if ($value === null || $value === '') {
            return null;
        }

        return filter_var(
            $value,
            FILTER_VALIDATE_BOOLEAN,
            FILTER_NULL_ON_FAILURE
        );
    }
}
