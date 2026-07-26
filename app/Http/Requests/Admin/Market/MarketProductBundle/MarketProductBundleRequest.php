<?php

namespace App\Http\Requests\Admin\Market\MarketProductBundle;

use App\Models\Admin\Market\MarketProduct\MarketProduct;
use App\Models\Admin\Market\MarketProductBundle\MarketProductBundle;
use App\Models\Admin\Market\MarketProductVariant\MarketProductVariant;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class MarketProductBundleRequest extends FormRequest
{
    /**
     * Проверка доступа к запросу.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Подготовка данных перед валидацией.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'calculate_price' => $this->boolean('calculate_price'),
            'activity' => $this->boolean('activity'),

            'left' => $this->boolean('left'),
            'main' => $this->boolean('main'),
            'right' => $this->boolean('right'),

            'is_new' => $this->boolean('is_new'),
            'is_hit' => $this->boolean('is_hit'),
            'is_sale' => $this->boolean('is_sale'),
        ]);

        $nullableFields = [
            'market_company_id',
            'market_shop_id',
            'currency_id',

            'sku',
            'vendor_code',
            'barcode',

            'old_price',
            'purchase_price',
            'wholesale_price',
            'wholesale_min_quantity',

            'published_at',
            'show_from_at',
            'show_to_at',
        ];

        $normalizedFields = [];

        foreach ($nullableFields as $field) {
            if ($this->input($field) === '') {
                $normalizedFields[$field] = null;
            }
        }

        if ($normalizedFields !== []) {
            $this->merge($normalizedFields);
        }

        $items = collect($this->input('items', []))
            ->map(function ($item, int $index): array {
                $item = is_array($item) ? $item : [];

                return array_merge($item, [
                    'id' => filled($item['id'] ?? null)
                        ? (int) $item['id']
                        : null,

                    'market_product_id' => filled($item['market_product_id'] ?? null)
                        ? (int) $item['market_product_id']
                        : null,

                    'market_product_variant_id' => filled($item['market_product_variant_id'] ?? null)
                        ? (int) $item['market_product_variant_id']
                        : null,

                    'quantity' => filled($item['quantity'] ?? null)
                        ? (int) $item['quantity']
                        : 1,

                    'unit_price' => filled($item['unit_price'] ?? null)
                        ? $item['unit_price']
                        : null,

                    'discount_type' => filled($item['discount_type'] ?? null)
                        ? $item['discount_type']
                        : null,

                    'discount_value' => filled($item['discount_value'] ?? null)
                        ? $item['discount_value']
                        : null,

                    'sort' => filled($item['sort'] ?? null)
                        ? (int) $item['sort']
                        : $index,

                    'activity' => filter_var(
                            $item['activity'] ?? true,
                            FILTER_VALIDATE_BOOLEAN,
                            FILTER_NULL_ON_FAILURE
                        ) ?? true,
                ]);
            })
            ->values()
            ->all();

        $this->merge([
            'items' => $items,
        ]);
    }

    /**
     * Правила валидации для store и update.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $bundleId = $this->bundleId();
        $ownerId = $this->ownerId();
        $fallbackLocale = config('app.fallback_locale', 'ru');

        $rules = [
            /**
             * Основные связи.
             */
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

            'currency_id' => [
                'nullable',
                'integer',
                Rule::exists('currencies', 'id'),
            ],

            /**
             * Основные данные.
             */
            /** Уникальный URL комплекта в пределах владельца */
            'url' => [
                'required',
                'string',
                'max:500',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',

                Rule::unique(
                    'market_product_bundles',
                    'url'
                )
                    ->where(
                        function (Builder $query) use (
                            $ownerId
                        ): void {
                            $query->where(
                                'user_id',
                                $ownerId
                            );
                        }
                    )
                    ->ignore(
                        $bundleId,
                        'id'
                    ),
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

            /**
             * Цена комплекта.
             */
            'calculate_price' => [
                'required',
                'boolean',
            ],

            'price' => [
                Rule::requiredIf(fn () => ! $this->boolean('calculate_price')),
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

            /**
             * Отображение.
             */
            'sort' => [
                'nullable',
                'integer',
                'min:0',
                'max:4294967295',
            ],

            'activity' => [
                'required',
                'boolean',
            ],

            'left' => [
                'required',
                'boolean',
            ],

            'main' => [
                'required',
                'boolean',
            ],

            'right' => [
                'required',
                'boolean',
            ],

            'is_new' => [
                'required',
                'boolean',
            ],

            'is_hit' => [
                'required',
                'boolean',
            ],

            'is_sale' => [
                'required',
                'boolean',
            ],

            /**
             * Публикация.
             */
            'status' => [
                'required',
                'string',
                Rule::in([
                    'draft',
                    'published',
                    'archived',
                ]),
            ],

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

            /**
             * Переводы.
             */
            'translations' => [
                'required',
                'array',
            ],

            "translations.{$fallbackLocale}" => [
                'required',
                'array',
            ],

            /**
             * Состав комплекта.
             */
            'items' => [
                'required',
                'array',
                'min:2',
                'max:100',
            ],

            'items.*' => [
                'required',
                'array',
            ],

            'items.*.id' => [
                'nullable',
                'integer',
                Rule::exists('market_product_bundle_items', 'id'),
            ],

            'items.*.market_product_id' => [
                'required',
                'integer',
                Rule::exists('market_products', 'id'),
            ],

            'items.*.market_product_variant_id' => [
                'nullable',
                'integer',
                Rule::exists('market_product_variants', 'id'),
            ],

            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
                'max:4294967295',
            ],

            'items.*.unit_price' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999999999.99',
            ],

            'items.*.discount_type' => [
                'nullable',
                'string',
                Rule::in([
                    'fixed',
                    'percent',
                ]),
            ],

            'items.*.discount_value' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999999999.99',
            ],

            'items.*.sort' => [
                'required',
                'integer',
                'min:0',
                'max:4294967295',
            ],

            'items.*.activity' => [
                'required',
                'boolean',
            ],

            /**
             * Изображения.
             */
            'images' => [
                'nullable',
                'array',
                'max:20',
            ],

            'images.*' => [
                'array',
            ],

            'images.*.id' => [
                'nullable',
                'integer',
                Rule::exists('market_product_bundle_images', 'id'),
            ],

            'images.*.file' => [
                'nullable',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp,gif',
                'max:10240',
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

            'deletedImages' => [
                'nullable',
                'array',
            ],

            'deletedImages.*' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('market_product_bundle_images', 'id'),
            ],
        ];

        foreach ($this->availableLocales() as $locale) {
            $rules["translations.{$locale}"] = [
                'nullable',
                'array',
            ];

            $rules["translations.{$locale}.title"] = $locale === $fallbackLocale
                ? [
                    'required',
                    'string',
                    'max:255',
                ]
                : [
                    'nullable',
                    'string',
                    'max:255',
                    "required_with:translations.{$locale}.subtitle,translations.{$locale}.short,translations.{$locale}.description,translations.{$locale}.meta_title,translations.{$locale}.meta_keywords,translations.{$locale}.meta_desc",
                ];

            $rules["translations.{$locale}.subtitle"] = [
                'nullable',
                'string',
                'max:255',
            ];

            $rules["translations.{$locale}.short"] = [
                'nullable',
                'string',
                'max:255',
            ];

            $rules["translations.{$locale}.description"] = [
                'nullable',
                'string',
            ];

            $rules["translations.{$locale}.meta_title"] = [
                'nullable',
                'string',
                'max:255',
            ];

            $rules["translations.{$locale}.meta_keywords"] = [
                'nullable',
                'string',
                'max:255',
            ];

            $rules["translations.{$locale}.meta_desc"] = [
                'nullable',
                'string',
            ];
        }

        return $rules;
    }

    /**
     * Дополнительные проверки после основной валидации.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $this->validateManualPrice($validator);
                $this->validateWholesalePrice($validator);
                $this->validateDiscounts($validator);
                $this->validateBundleItems($validator);
                $this->validateItemOwnership($validator);
                $this->validateCompanyAndShop($validator);
                $this->validateItemIds($validator);
                $this->validateImageIds($validator);
            },
        ];
    }

    /**
     * Проверка ручной цены комплекта.
     */
    protected function validateManualPrice(Validator $validator): void
    {
        if ($this->boolean('calculate_price')) {
            return;
        }

        $price = (float) $this->input('price', 0);
        $oldPrice = $this->input('old_price');

        if ($price <= 0) {
            $validator->errors()->add(
                'price',
                'При ручном режиме цена комплекта должна быть больше нуля.'
            );
        }

        if ($oldPrice !== null && (float) $oldPrice <= $price) {
            $validator->errors()->add(
                'old_price',
                'Старая цена должна быть больше текущей цены комплекта.'
            );
        }
    }

    /**
     * Проверка согласованности оптовой цены.
     */
    protected function validateWholesalePrice(Validator $validator): void
    {
        $wholesalePrice = $this->input('wholesale_price');
        $wholesaleMinQuantity = $this->input('wholesale_min_quantity');

        if ($wholesalePrice !== null && $wholesaleMinQuantity === null) {
            $validator->errors()->add(
                'wholesale_min_quantity',
                'Укажите минимальное количество для оптовой цены.'
            );
        }

        if ($wholesalePrice === null && $wholesaleMinQuantity !== null) {
            $validator->errors()->add(
                'wholesale_price',
                'Укажите оптовую цену комплекта.'
            );
        }
    }

    /**
     * Проверка скидок позиций.
     */
    protected function validateDiscounts(Validator $validator): void
    {
        foreach ($this->input('items', []) as $index => $item) {
            $discountType = $item['discount_type'] ?? null;
            $discountValue = $item['discount_value'] ?? null;

            if ($discountType === null && $discountValue === null) {
                continue;
            }

            if ($discountType === null && $discountValue !== null) {
                $validator->errors()->add(
                    "items.{$index}.discount_type",
                    'Выберите тип скидки позиции.'
                );

                continue;
            }

            if ($discountType !== null && $discountValue === null) {
                $validator->errors()->add(
                    "items.{$index}.discount_value",
                    'Укажите размер скидки позиции.'
                );

                continue;
            }

            if ($discountType === 'percent' && (float) $discountValue > 100) {
                $validator->errors()->add(
                    "items.{$index}.discount_value",
                    'Процентная скидка не может превышать 100%.'
                );
            }
        }
    }

    /**
     * Проверка состава комплекта.
     */
    protected function validateBundleItems(Validator $validator): void
    {
        $items = collect($this->input('items', []));

        $variantIds = $items
            ->pluck('market_product_variant_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $variants = MarketProductVariant::query()
            ->whereIn('id', $variantIds)
            ->get([
                'id',
                'market_product_id',
            ])
            ->keyBy('id');

        $usedPairs = [];
        $productsWithBaseItem = [];

        foreach ($items as $index => $item) {
            $productId = (int) ($item['market_product_id'] ?? 0);

            $variantId = filled($item['market_product_variant_id'] ?? null)
                ? (int) $item['market_product_variant_id']
                : null;

            if ($productId <= 0) {
                continue;
            }

            if ($variantId !== null) {
                $variant = $variants->get($variantId);

                if (! $variant || (int) $variant->market_product_id !== $productId) {
                    $validator->errors()->add(
                        "items.{$index}.market_product_variant_id",
                        'Выбранный вариант не принадлежит указанному товару.'
                    );
                }
            }

            $pairKey = $productId . ':' . ($variantId ?? 'base');

            if (isset($usedPairs[$pairKey])) {
                $validator->errors()->add(
                    "items.{$index}.market_product_id",
                    'Такая позиция уже добавлена в комплект.'
                );
            }

            $usedPairs[$pairKey] = true;

            if ($variantId === null) {
                if (isset($productsWithBaseItem[$productId])) {
                    $validator->errors()->add(
                        "items.{$index}.market_product_id",
                        'Базовый товар уже добавлен в комплект.'
                    );
                }

                $productsWithBaseItem[$productId] = true;
            }
        }
    }

    /**
     * Проверка доступности выбранных товаров.
     */
    protected function validateItemOwnership(Validator $validator): void
    {
        $user = auth()->user();

        if (! $user || (method_exists($user, 'hasRole') && $user->hasRole('admin'))) {
            return;
        }

        $productIds = collect($this->input('items', []))
            ->pluck('market_product_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $allowedProductIds = MarketProduct::query()
            ->where('user_id', $user->id)
            ->whereIn('id', $productIds)
            ->pluck('id')
            ->map(fn ($id) => (int) $id);

        if ($productIds->diff($allowedProductIds)->isNotEmpty()) {
            $validator->errors()->add(
                'items',
                'В состав комплекта добавлены недоступные вам товары.'
            );
        }
    }

    /**
     * Проверка принадлежности магазина компании.
     */
    protected function validateCompanyAndShop(Validator $validator): void
    {
        $companyId = $this->integer('market_company_id');
        $shopId = $this->integer('market_shop_id');

        if (! $shopId) {
            return;
        }

        $shop = DB::table('market_shops')
            ->where('id', $shopId)
            ->first([
                'id',
                'market_company_id',
                'user_id',
            ]);

        if (! $shop) {
            return;
        }

        if ($companyId && (int) $shop->market_company_id !== $companyId) {
            $validator->errors()->add(
                'market_shop_id',
                'Выбранный магазин не принадлежит указанной компании.'
            );
        }

        $user = auth()->user();

        if (
            $user
            && method_exists($user, 'hasRole')
            && ! $user->hasRole('admin')
            && (int) $shop->user_id !== (int) $user->id
        ) {
            $validator->errors()->add(
                'market_shop_id',
                'Выбранный магазин вам недоступен.'
            );
        }
    }

    /**
     * Проверка ID позиций при обновлении.
     */
    protected function validateItemIds(Validator $validator): void
    {
        $bundleId = $this->bundleId();

        if (! $bundleId) {
            return;
        }

        $itemIds = collect($this->input('items', []))
            ->pluck('id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($itemIds->isEmpty()) {
            return;
        }

        $allowedIds = DB::table('market_product_bundle_items')
            ->where('market_product_bundle_id', $bundleId)
            ->whereIn('id', $itemIds)
            ->pluck('id')
            ->map(fn ($id) => (int) $id);

        if ($itemIds->diff($allowedIds)->isNotEmpty()) {
            $validator->errors()->add(
                'items',
                'Одна или несколько позиций не принадлежат редактируемому комплекту.'
            );
        }
    }

    /**
     * Проверка изображений при обновлении.
     */
    protected function validateImageIds(Validator $validator): void
    {
        $bundleId = $this->bundleId();

        if (! $bundleId) {
            return;
        }

        $imageIds = collect($this->input('images', []))
            ->pluck('id')
            ->merge($this->input('deletedImages', []))
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($imageIds->isEmpty()) {
            return;
        }

        $allowedIds = DB::table('market_product_bundle_has_images')
            ->where('market_product_bundle_id', $bundleId)
            ->whereIn('market_product_bundle_image_id', $imageIds)
            ->pluck('market_product_bundle_image_id')
            ->map(fn ($id) => (int) $id);

        if ($imageIds->diff($allowedIds)->isNotEmpty()) {
            $validator->errors()->add(
                'images',
                'Одно или несколько изображений не принадлежат редактируемому комплекту.'
            );
        }
    }

    /**
     * Доступные локали.
     *
     * @return array<int, string>
     */
    protected function availableLocales(): array
    {
        $locales = config('app.available_locales', [
            'ru',
        ]);

        return array_values(
            array_filter(
                $locales,
                fn ($locale) => is_string($locale) && $locale !== ''
            )
        );
    }

    /**
     * Получить ID редактируемого комплекта.
     */
    protected function bundleId(): ?int
    {
        $routeBundle = $this->route('marketProductBundle')
            ?? $this->route('market_product_bundle');

        if ($routeBundle instanceof MarketProductBundle) {
            return (int) $routeBundle->id;
        }

        if (is_numeric($routeBundle)) {
            return (int) $routeBundle;
        }

        $routeId = $this->route('id');

        return is_numeric($routeId)
            ? (int) $routeId
            : null;
    }

    /**
     * Получить владельца комплекта.
     */
    protected function ownerId(): int
    {
        $bundleId = $this->bundleId();

        if ($bundleId) {
            $ownerId = MarketProductBundle::query()
                ->whereKey($bundleId)
                ->value('user_id');

            if ($ownerId !== null) {
                return (int) $ownerId;
            }
        }

        return (int) auth()->id();
    }

    /**
     * Сообщения об ошибках.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'url.required' => 'Введите URL комплекта.',
            'url.max' => 'URL комплекта не должен превышать 500 символов.',
            'url.regex' => 'URL может содержать только латинские буквы, цифры и дефисы.',
            'url.unique' => 'Комплект с таким URL уже существует.',

            'market_company_id.exists' => 'Выбранная компания не найдена.',
            'market_shop_id.exists' => 'Выбранный магазин не найден.',
            'currency_id.exists' => 'Выбранная валюта не найдена.',

            'price.required' => 'Укажите цену комплекта.',
            'price.numeric' => 'Цена комплекта должна быть числом.',
            'price.min' => 'Цена комплекта не может быть отрицательной.',

            'old_price.numeric' => 'Старая цена должна быть числом.',
            'old_price.min' => 'Старая цена не может быть отрицательной.',

            'purchase_price.numeric' => 'Закупочная цена должна быть числом.',
            'purchase_price.min' => 'Закупочная цена не может быть отрицательной.',

            'wholesale_price.numeric' => 'Оптовая цена должна быть числом.',
            'wholesale_price.min' => 'Оптовая цена не может быть отрицательной.',

            'wholesale_min_quantity.integer' => 'Минимальное оптовое количество должно быть целым числом.',
            'wholesale_min_quantity.min' => 'Минимальное оптовое количество должно быть не меньше одного.',

            'status.required' => 'Выберите статус комплекта.',
            'status.in' => 'Выбран недопустимый статус комплекта.',

            'show_to_at.after_or_equal' => 'Дата окончания показа не может быть раньше даты начала.',

            'translations.required' => 'Добавьте переводы комплекта.',
            'translations.array' => 'Переводы должны быть переданы массивом.',
            'translations.*.title.required' => 'Введите название комплекта.',
            'translations.*.title.max' => 'Название комплекта не должно превышать 255 символов.',
            'translations.*.subtitle.max' => 'Подзаголовок не должен превышать 255 символов.',
            'translations.*.short.max' => 'Краткое описание не должно превышать 255 символов.',
            'translations.*.meta_title.max' => 'SEO-заголовок не должен превышать 255 символов.',
            'translations.*.meta_keywords.max' => 'SEO-ключевые слова не должны превышать 255 символов.',

            'items.required' => 'Добавьте товары в комплект.',
            'items.array' => 'Состав комплекта должен быть массивом.',
            'items.min' => 'Комплект должен содержать минимум две позиции.',
            'items.max' => 'Комплект не может содержать больше 100 позиций.',

            'items.*.market_product_id.required' => 'Выберите товар для позиции комплекта.',
            'items.*.market_product_id.exists' => 'Выбранный товар не найден.',
            'items.*.market_product_variant_id.exists' => 'Выбранный вариант товара не найден.',

            'items.*.quantity.required' => 'Укажите количество товара в комплекте.',
            'items.*.quantity.integer' => 'Количество товара должно быть целым числом.',
            'items.*.quantity.min' => 'Количество товара должно быть не меньше одного.',

            'items.*.unit_price.numeric' => 'Цена позиции должна быть числом.',
            'items.*.unit_price.min' => 'Цена позиции не может быть отрицательной.',

            'items.*.discount_type.in' => 'Допустимые типы скидки: fixed или percent.',
            'items.*.discount_value.numeric' => 'Размер скидки должен быть числом.',
            'items.*.discount_value.min' => 'Размер скидки не может быть отрицательным.',

            'images.max' => 'К комплекту можно добавить не более 20 изображений.',
            'images.*.file.image' => 'Загруженный файл должен быть изображением.',
            'images.*.file.mimes' => 'Разрешены изображения JPG, JPEG, PNG, WEBP и GIF.',
            'images.*.file.max' => 'Размер изображения не должен превышать 10 МБ.',
        ];
    }
}
