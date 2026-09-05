<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Редактирование комплекта товаров маркетплейса.
 */
import { computed, ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { transliterate } from '@/utils/transliteration'
import VueMultiselect from 'vue-multiselect'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import PrimaryButton from '@/Components/Admin/UI/Buttons/PrimaryButton.vue'
import MetatagsButton from '@/Components/Admin/UI/Buttons/MetatagsButton.vue'
import ClearMetaButton from '@/Components/Admin/UI/Buttons/ClearMetaButton.vue'

import LabelCheckbox from '@/Components/Admin/UI/Checkbox/LabelCheckbox.vue'
import ActivityCheckbox from '@/Components/Admin/UI/Checkbox/ActivityCheckbox.vue'

import InputNumber from '@/Components/Admin/UI/Input/InputNumber.vue'
import MarketProductPriceInput from '@/Components/Admin/UI/Input/MarketProductPriceInput.vue'
import LabelInput from '@/Components/Admin/UI/Input/LabelInput.vue'
import InputText from '@/Components/Admin/UI/Input/InputText.vue'
import InputError from '@/Components/Admin/UI/Input/InputError.vue'
import MetaDescTextarea from '@/Components/Admin/UI/Textarea/MetaDescTextarea.vue'

import TinyEditor from '@/Components/Admin/UI/TinyEditor/TinyEditor.vue'
import TranslationTabs from '@/Components/Admin/UI/Locale/TranslationTabs.vue'
import MultiImageEdit from '@/Components/Admin/UI/Image/MultiImageEdit.vue'
import MultiImageUpload from '@/Components/Admin/UI/Image/MultiImageUpload.vue'
import MultiImagePresetEdit from '@/Components/Admin/UI/Image/MultiImagePresetEdit.vue'
import MultiImagePresetUpload from '@/Components/Admin/UI/Image/MultiImagePresetUpload.vue'

/** Локализация интерфейса */
const { t } = useI18n()

/** Уведомления */
const toast = useToast()

/** Входные данные страницы */
const props = defineProps({
    bundle: {
        type: Object,
        required: true,
    },

    imageProcessorEnabled: {
        type: Boolean,
        default: true,
    },

    imagePreset: {
        type: Object,
        default: null,
    },

    currentLocale: {
        type: String,
        default: '',
    },

    availableLocales: {
        type: Array,
        default: () => [],
    },

    companies: {
        type: [Array, Object],
        default: () => [],
    },

    shops: {
        type: [Array, Object],
        default: () => [],
    },

    currencies: {
        type: [Array, Object],
        default: () => [],
    },

    products: {
        type: [Array, Object],
        default: () => [],
    },

    variants: {
        type: [Array, Object],
        default: () => [],
    },

    errors: {
        type: Object,
        default: () => ({}),
    },
})

/** Получить массив из обычного массива или Laravel Resource Collection */
const resourceList = (value) => {
    if (Array.isArray(value)) {
        return value
    }

    if (Array.isArray(value?.data)) {
        return value.data
    }

    return []
}

/** Редактируемый комплект */
const bundle = computed(() => {
    return props.bundle?.data
        || props.bundle
        || {}
})

/** Создать пустой перевод комплекта */
const makeTranslation = () => ({
    title: '',
    subtitle: '',
    short: '',
    description: '',
    meta_title: '',
    meta_keywords: '',
    meta_desc: '',
})

/** Преобразовать переводы в объект по локалям */
const normalizeTranslations = (translations) => {
    const result = {}

    resourceList(translations).forEach((translation) => {
        if (!translation?.locale) {
            return
        }

        result[translation.locale] = {
            title: translation.title || '',
            subtitle: translation.subtitle || '',
            short: translation.short || '',
            description: translation.description || '',
            meta_title: translation.meta_title || '',
            meta_keywords: translation.meta_keywords || '',
            meta_desc: translation.meta_desc || '',
        }
    })

    if (!Object.keys(result).length) {
        result[props.currentLocale || 'ru'] =
            makeTranslation()
    }

    return result
}

/** Подготовить nullable-значение для поля ввода */
const normalizeNullableInput = (value) => {
    return value === null
    || typeof value === 'undefined'
        ? ''
        : value
}

/** Преобразовать дату в формат datetime-local */
const toDateTimeLocal = (value) => {
    if (!value) {
        return ''
    }

    const stringValue = String(value)

    /*
     * Resource уже может вернуть формат Y-m-d\TH:i.
     * В таком случае не выполняем повторное преобразование зоны.
     */
    if (
        /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/.test(
            stringValue
        )
    ) {
        return stringValue
    }

    const date = new Date(stringValue)

    if (Number.isNaN(date.getTime())) {
        return stringValue.slice(0, 16)
    }

    const pad = (number) => {
        return String(number).padStart(2, '0')
    }

    return [
        `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`,
        `${pad(date.getHours())}:${pad(date.getMinutes())}`,
    ].join('T')
}

/** Создать пустую позицию комплекта */
const makeBundleItem = (sort = 0) => ({
    id: null,
    market_product_id: null,
    market_product_variant_id: null,
    quantity: 1,
    unit_price: null,
    discount_type: null,
    discount_value: null,
    sort,
    activity: true,
})

/** Нормализовать существующие позиции комплекта */
const normalizeItems = (items) => {
    const normalized = resourceList(items)
        .map((item, index) => ({
            id: item?.id
                ? Number(item.id)
                : null,

            market_product_id:
                item?.market_product_id
                    ? Number(item.market_product_id)
                    : null,

            market_product_variant_id:
                item?.market_product_variant_id
                    ? Number(item.market_product_variant_id)
                    : null,

            quantity: Math.max(
                1,
                Number(item?.quantity || 1)
            ),

            unit_price:
                normalizeNullableInput(
                    item?.unit_price
                ),

            discount_type:
                item?.discount_type || null,

            discount_value:
                normalizeNullableInput(
                    item?.discount_value
                ),

            sort: Number(item?.sort ?? index),
            activity: Boolean(
                item?.activity ?? true
            ),
        }))
        .sort((first, second) => {
            if (first.sort !== second.sort) {
                return first.sort - second.sort
            }

            return Number(first.id || 0)
                - Number(second.id || 0)
        })
        .map((item, index) => ({
            ...item,
            sort: index,
        }))

    while (normalized.length < 2) {
        normalized.push(
            makeBundleItem(normalized.length)
        )
    }

    return normalized
}

/** Начальные переводы и локаль */
const initialTranslations = normalizeTranslations(
    bundle.value.translations
)

const defaultLocale =
    initialTranslations[props.currentLocale]
        ? props.currentLocale
        : Object.keys(initialTranslations)[0] || 'ru'

/** Активная локаль */
const activeLocale = ref(defaultLocale)

/** Новые изображения */
const newImages = ref([])

/** Существующие изображения */
const existingImages = ref(
    resourceList(bundle.value.images)
        .filter((image) => {
            return image.url
                || image.webp_url
                || image.image_url
                || image.thumb_url
        })
        .map((image, index) => ({
            id: Number(image.id),

            url:
                image.webp_url
                || image.image_url
                || image.thumb_url
                || image.url,

            webp_url:
                image.webp_url || null,

            thumb_url:
                image.thumb_url || null,

            order: Number(
                image.order
                ?? image.pivot?.order
                ?? index
            ),

            alt: image.alt || '',
            caption: image.caption || '',
        }))
        .sort((first, second) => {
            return first.order - second.order
        })
)

/** Форма редактирования комплекта */
const form = useForm({
    user_id:
        bundle.value.user_id
            ? Number(bundle.value.user_id)
            : null,

    market_company_id:
        bundle.value.market_company_id
            ? Number(bundle.value.market_company_id)
            : null,

    market_shop_id:
        bundle.value.market_shop_id
            ? Number(bundle.value.market_shop_id)
            : null,

    currency_id:
        bundle.value.currency_id
            ? Number(bundle.value.currency_id)
            : null,

    url: bundle.value.url || '',
    sku: bundle.value.sku || '',
    vendor_code:
        bundle.value.vendor_code || '',
    barcode: bundle.value.barcode || '',

    calculate_price: Boolean(
        bundle.value.calculate_price
    ),

    price: normalizeNullableInput(
        bundle.value.price
    ),

    old_price: normalizeNullableInput(
        bundle.value.old_price
    ),

    purchase_price: normalizeNullableInput(
        bundle.value.purchase_price
    ),

    wholesale_price: normalizeNullableInput(
        bundle.value.wholesale_price
    ),

    wholesale_min_quantity:
        normalizeNullableInput(
            bundle.value.wholesale_min_quantity
        ),

    sort: Number(bundle.value.sort || 0),
    activity: Boolean(bundle.value.activity),

    left: Boolean(bundle.value.left),
    main: Boolean(bundle.value.main),
    right: Boolean(bundle.value.right),

    is_new: Boolean(bundle.value.is_new),
    is_hit: Boolean(bundle.value.is_hit),
    is_sale: Boolean(bundle.value.is_sale),

    status: bundle.value.status || 'draft',

    published_at: toDateTimeLocal(
        bundle.value.published_at
    ),

    show_from_at: toDateTimeLocal(
        bundle.value.show_from_at
    ),

    show_to_at: toDateTimeLocal(
        bundle.value.show_to_at
    ),

    views: Number(bundle.value.views || 0),

    likes_count: Number(
        bundle.value.likes_count || 0
    ),

    rating_avg: Number(
        bundle.value.rating_avg || 0
    ),

    rating_count: Number(
        bundle.value.rating_count || 0
    ),

    items: normalizeItems(
        bundle.value.items
        || bundle.value.active_items
    ),

    images: [],
    deletedImages: [],

    translations: initialTranslations,
})

/** Выбранные связанные сущности */
/** Текущий перевод */
const currentTranslation = computed(() => {
    if (!form.translations[activeLocale.value]) {
        form.translations[activeLocale.value] =
            makeTranslation()
    }

    return form.translations[activeLocale.value]
})

/** Ошибка поля текущего перевода */
const getTranslationError = (key) => {
    return form.errors[
        `translations.${activeLocale.value}.${key}`
        ]
}

/** Ошибка поля позиции */
const getItemError = (index, key) => {
    return form.errors[`items.${index}.${key}`]
}

/** Нормализованные справочники */
const companyList = computed(() => {
    return resourceList(props.companies)
})

const shopList = computed(() => {
    return resourceList(props.shops)
})

const currencyList = computed(() => {
    return resourceList(props.currencies)
})

const productList = computed(() => {
    return resourceList(props.products)
})

const variantList = computed(() => {
    return resourceList(props.variants)
})

/** Получить отображаемое название сущности */
const translationTitle = (item) => {
    return item?.translation?.title
        || item?.code
        || item?.sku
        || item?.legal_name
        || item?.name
        || item?.url
        || `ID: ${item?.id}`
}

/** Опции компаний */
const companyOptions = computed(() => {
    return companyList.value.map((item) => ({
        ...item,
        label: `[ID: ${item.id}] ${translationTitle(item)}`,
    }))
})

/** Магазины выбранной компании */
const filteredShopList = computed(() => {
    if (!form.market_company_id) {
        return shopList.value
    }

    return shopList.value.filter((item) => {
        return Number(item.market_company_id)
            === Number(form.market_company_id)
    })
})

/** Опции магазинов */
const shopOptions = computed(() => {
    return filteredShopList.value.map((item) => ({
        ...item,
        label: `[ID: ${item.id}] ${translationTitle(item)}`,
    }))
})

/** Опции валют */
const currencyOptions = computed(() => {
    return currencyList.value.map((item) => ({
        ...item,

        label:
            `[${item.code}] ${item.name}`
            + (
                item.symbol
                    ? ` — ${item.symbol}`
                    : ''
            ),
    }))
})

/**
 * Выбранная компания.
 *
 * form.market_company_id — единственный источник истины.
 */
const selectedCompany = computed({
    get: () => {
        return companyOptions.value.find((item) => {
            return Number(item.id)
                === Number(form.market_company_id)
        }) || null
    },

    set: (value) => {
        const previousCompanyId =
            form.market_company_id

        form.market_company_id =
            value?.id ?? null

        if (
            Number(previousCompanyId)
            !== Number(form.market_company_id)
            && form.market_shop_id
        ) {
            const selectedShopValue =
                shopList.value.find((item) => {
                    return Number(item.id)
                        === Number(form.market_shop_id)
                })

            if (
                selectedShopValue
                && Number(selectedShopValue.market_company_id)
                !== Number(form.market_company_id)
            ) {
                form.market_shop_id = null
            }
        }
    },
})

/**
 * Выбранный магазин.
 *
 * form.market_shop_id — единственный источник истины.
 */
const selectedShop = computed({
    get: () => {
        return shopOptions.value.find((item) => {
            return Number(item.id)
                === Number(form.market_shop_id)
        }) || null
    },

    set: (value) => {
        form.market_shop_id =
            value?.id ?? null

        if (
            value?.market_company_id
            && Number(form.market_company_id)
            !== Number(value.market_company_id)
        ) {
            form.market_company_id =
                Number(value.market_company_id)
        }
    },
})

/**
 * Выбранная валюта.
 *
 * form.currency_id — единственный источник истины.
 */
const selectedCurrency = computed({
    get: () => {
        return currencyOptions.value.find((item) => {
            return Number(item.id)
                === Number(form.currency_id)
        }) || null
    },

    set: (value) => {
        form.currency_id =
            value?.id ?? null
    },
})

/** Опции товаров */
const productOptions = computed(() => {
    return productList.value.map((item) => ({
        ...item,

        label: [
            `[ID: ${item.id}]`,
            translationTitle(item),
            item.sku ? `SKU: ${item.sku}` : '',
        ]
            .filter(Boolean)
            .join(' — '),
    }))
})

/** Найти выбранный товар позиции */
const selectedProduct = (item) => {
    return productOptions.value.find((product) => {
        return Number(product.id)
            === Number(item.market_product_id)
    }) || null
}

/** Получить варианты выбранного товара */
const variantsForItem = (item) => {
    if (!item.market_product_id) {
        return []
    }

    const product = selectedProduct(item)
    const nestedVariants = resourceList(
        product?.variants
    )

    const source = nestedVariants.length
        ? nestedVariants
        : variantList.value.filter((variant) => {
            return Number(variant.market_product_id)
                === Number(item.market_product_id)
        })

    return source.map((variant) => ({
        ...variant,

        label: [
            variant.is_default ? '★' : '',
            `[ID: ${variant.id}]`,
            translationTitle(variant),
            variant.sku
                ? `SKU: ${variant.sku}`
                : '',
        ]
            .filter(Boolean)
            .join(' — '),
    }))
}

/** Найти выбранный вариант позиции */
const selectedVariant = (item) => {
    return variantsForItem(item).find((variant) => {
        return Number(variant.id)
            === Number(
                item.market_product_variant_id
            )
    }) || null
}

/** Выбрать товар позиции */
const selectProduct = (index, product) => {
    const item = form.items[index]

    item.market_product_id =
        product?.id ?? null

    /*
     * Вариант старого товара не может оставаться
     * после изменения товара позиции.
     */
    item.market_product_variant_id = null
}

/** Выбрать вариант позиции */
const selectVariant = (index, variant) => {
    form.items[index]
        .market_product_variant_id =
        variant?.id ?? null
}

/** Добавить позицию */
const addItem = () => {
    form.items.push(
        makeBundleItem(form.items.length)
    )
}

/** Удалить позицию */
const removeItem = (index) => {
    if (form.items.length <= 2) {
        toast.warning(
            'Комплект должен содержать минимум две позиции.'
        )

        return
    }

    form.items.splice(index, 1)
    normalizeItemsSort()
}

/** Переместить позицию */
const moveItem = (index, direction) => {
    const targetIndex = index + direction

    if (
        targetIndex < 0
        || targetIndex >= form.items.length
    ) {
        return
    }

    const currentItem = form.items[index]

    form.items[index] =
        form.items[targetIndex]

    form.items[targetIndex] =
        currentItem

    normalizeItemsSort()
}

/** Нормализовать порядок позиций */
const normalizeItemsSort = () => {
    form.items.forEach((item, index) => {
        item.sort = index
    })
}

/** Сохранить резервную цену для автоматического режима */
watch(
    () => form.calculate_price,
    (value) => {
        if (value && form.price === '') {
            form.price = 0
        }
    }
)

/** Пресет изображений комплекта */
const galleryPreset = computed(() => {
    return props.imagePreset || {
        key: 'rectangle_large',
        shape: 'rectangle',
        width: 1200,
        height: 800,
        image_rotation_enabled: true,
        crop_rotation_enabled: true,
    }
})

/** Обновить порядок существующих изображений */
const handleExistingImagesUpdate = (images) => {
    existingImages.value =
        (images || []).map((image, index) => ({
            ...image,
            order: index,
        }))
}

/** Пометить существующее изображение на удаление */
const handleDeleteExistingImage = (deletedId) => {
    const imageId = Number(deletedId)

    if (!imageId) {
        return
    }

    if (
        !form.deletedImages.some((id) => {
            return Number(id) === imageId
        })
    ) {
        form.deletedImages.push(imageId)
    }

    existingImages.value =
        existingImages.value
            .filter((image) => {
                return Number(image.id) !== imageId
            })
            .map((image, index) => ({
                ...image,
                order: index,
            }))
}

/** Обновить новые изображения */
const handleNewImagesUpdate = (images) => {
    newImages.value =
        (images || []).map((image, index) => ({
            ...image,
            order: index,
        }))
}

/** Сформировать URL из названия */
const handleUrlInputFocus = () => {
    if (
        !form.url
        && currentTranslation.value.title
    ) {
        form.url = transliterate(
            currentTranslation.value.title.toLowerCase()
        )
    }
}

/** Обрезать текст до указанной длины */
const truncateText = (
    text,
    maxLength,
    addEllipsis = false
) => {
    if (!text) {
        return ''
    }

    const value = String(text)

    if (value.length <= maxLength) {
        return value
    }

    const suffix = addEllipsis ? '…' : ''

    return value
        .slice(0, maxLength - suffix.length)
        .trimEnd() + suffix
}

/** Очистить SEO-поля текущего перевода */
const clearMetaFields = () => {
    const translation = currentTranslation.value

    translation.meta_title = ''
    translation.meta_keywords = ''
    translation.meta_desc = ''
}

/** Сгенерировать SEO-поля текущего перевода */
const generateMetatags = () => {
    const translation = currentTranslation.value

    if (
        translation.title
        && !translation.meta_title
    ) {
        translation.meta_title = truncateText(
            translation.title,
            255
        )
    }

    if (
        translation.title
        && !translation.meta_keywords
    ) {
        const words = translation.title
            .split(/\s+/)
            .filter((word) => {
                return word && word.length >= 3
            })
            .map((word) => {
                return word.toLowerCase()
            })
            .filter((value, index, self) => {
                return self.indexOf(value) === index
            })

        translation.meta_keywords =
            truncateText(
                words.join(', '),
                255
            )
    }

    if (
        translation.short
        && !translation.meta_desc
    ) {
        const description = String(
            translation.short
        ).replace(/(<([^>]+)>)/gi, '')

        translation.meta_desc =
            truncateText(
                description,
                255,
                true
            )
    }
}

/** Преобразовать необязательное значение в число */
const nullableNumber = (value, digits = 2) => {
    if (
        value === ''
        || value === null
        || typeof value === 'undefined'
    ) {
        return null
    }

    const number = Number(value)

    return Number.isFinite(number)
        ? Number(number.toFixed(digits))
        : null
}

/** Нормализовать состав перед отправкой */
const normalizedItems = () => {
    return form.items.map((item, index) => ({
        id: item.id
            ? Number(item.id)
            : null,

        market_product_id:
            item.market_product_id
                ? Number(item.market_product_id)
                : null,

        market_product_variant_id:
            item.market_product_variant_id
                ? Number(
                    item.market_product_variant_id
                )
                : null,

        quantity: Math.max(
            1,
            Number(item.quantity || 1)
        ),

        unit_price: nullableNumber(
            item.unit_price,
            2
        ),

        discount_type:
            item.discount_type || null,

        discount_value:
            item.discount_type
                ? nullableNumber(
                    item.discount_value,
                    2
                )
                : null,

        sort: index,
        activity: item.activity ? 1 : 0,
    }))
}

/** Отправить форму обновления комплекта */
const submitForm = () => {
    normalizeItemsSort()

    form.transform((data) => {
        const transformed = {
            ...data,

            /** Laravel принимает multipart PUT через spoofing */
            _method: 'put',

            /** Внешние ключи */
            market_company_id:
                data.market_company_id
                    ? Number(data.market_company_id)
                    : null,

            market_shop_id:
                data.market_shop_id
                    ? Number(data.market_shop_id)
                    : null,

            currency_id:
                data.currency_id
                    ? Number(data.currency_id)
                    : null,

            /** Режим формирования цены */
            calculate_price:
                data.calculate_price ? 1 : 0,

            /** Цены */
            price:
                nullableNumber(data.price, 2) ?? 0,

            old_price:
                nullableNumber(data.old_price, 2),

            purchase_price:
                nullableNumber(
                    data.purchase_price,
                    2
                ),

            wholesale_price:
                nullableNumber(
                    data.wholesale_price,
                    2
                ),

            wholesale_min_quantity:
                data.wholesale_min_quantity === ''
                || data.wholesale_min_quantity === null
                || typeof data.wholesale_min_quantity ===
                'undefined'
                    ? null
                    : Number(
                        data.wholesale_min_quantity
                    ),

            /** Отображение */
            sort: Number(data.sort || 0),

            activity:
                data.activity ? 1 : 0,

            left:
                data.left ? 1 : 0,

            main:
                data.main ? 1 : 0,

            right:
                data.right ? 1 : 0,

            /** Маркетинговые признаки */
            is_new:
                data.is_new ? 1 : 0,

            is_hit:
                data.is_hit ? 1 : 0,

            is_sale:
                data.is_sale ? 1 : 0,

            /** Статистика */
            views:
                Number(data.views || 0),

            likes_count:
                Number(data.likes_count || 0),

            rating_avg:
                nullableNumber(
                    data.rating_avg,
                    2
                ) ?? 0,

            rating_count:
                Number(data.rating_count || 0),

            /** Состав комплекта */
            items: normalizedItems(),
        }

        /*
         * Эти технические поля формируем ниже вручную
         * в формате multipart/form-data.
         */
        delete transformed.images
        delete transformed.deletedImages

        let imageIndex = 0

        /** Существующие изображения */
        existingImages.value.forEach(
            (image, index) => {
                transformed[
                    `images[${imageIndex}][id]`
                    ] = Number(image.id)

                transformed[
                    `images[${imageIndex}][order]`
                    ] = Number(
                    image.order ?? index
                )

                transformed[
                    `images[${imageIndex}][alt]`
                    ] = image.alt || ''

                transformed[
                    `images[${imageIndex}][caption]`
                    ] = image.caption || ''

                imageIndex += 1
            }
        )

        /** Новые изображения */
        newImages.value.forEach(
            (image, index) => {
                if (!image.file) {
                    return
                }

                transformed[
                    `images[${imageIndex}][file]`
                    ] = image.file

                transformed[
                    `images[${imageIndex}][order]`
                    ] = Number(
                    image.order
                    ?? existingImages.value.length
                    + index
                )

                transformed[
                    `images[${imageIndex}][alt]`
                    ] = image.alt || ''

                transformed[
                    `images[${imageIndex}][caption]`
                    ] = image.caption || ''

                imageIndex += 1
            }
        )

        /** Удаляемые изображения без повторяющихся ID */
        const deletedImageIds = [
            ...new Set(
                form.deletedImages
                    .map((id) => Number(id))
                    .filter((id) => id > 0)
            ),
        ]

        deletedImageIds.forEach((id, index) => {
            transformed[
                `deletedImages[${index}]`
                ] = id
        })

        return transformed
    })

    form.post(
        route(
            'admin.marketProductBundles.update',
            {
                marketProductBundle:
                bundle.value.id,
            }
        ),
        {
            forceFormData: true,
            errorBag:
                'updateMarketProductBundle',
            preserveScroll: true,

            onSuccess: () => {
                toast.success(
                    'Комплект товаров успешно обновлён!'
                )

                newImages.value = []
                form.deletedImages = []
            },

            onError: (errors) => {
                console.error(
                    'Ошибка обновления комплекта:',
                    errors
                )

                const firstKey =
                    Object.keys(errors || {})[0]

                toast.error(
                    errors?.[firstKey]
                    || 'Проверьте правильность заполнения полей.'
                )
            },
        }
    )
}
</script>

<template>
    <AdminLayout :title="t('editMarketProductBundle')">
        <template #header>
            <TitlePage>
                {{ t('editMarketProductBundle') }}
            </TitlePage>
        </template>

        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       shadow-lg shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <DefaultButton
                        :href="route('admin.marketProductBundles.index')"
                    >
                        <template #icon>
                            <svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                                 viewBox="0 0 16 16">
                                <path
                                    d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z" />
                            </svg>
                        </template>
                        {{ t('back') }}
                    </DefaultButton>
                </div>

                <form
                    class="p-3 w-full"
                    enctype="multipart/form-data"
                    @submit.prevent="submitForm"
                >
                    <!-- Основные переключатели -->
                    <div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-3">
                        <div class="flex items-center gap-2">
                            <ActivityCheckbox
                                id="activity"
                                v-model="form.activity"
                            />
                            <LabelCheckbox
                                for="activity"
                                :text="t('activity')"
                            />
                        </div>
                        <div class="flex items-center gap-2">
                            <LabelInput
                                for="sort"
                                :value="t('sort')"
                            />
                            <InputNumber
                                id="sort"
                                v-model.number="form.sort"
                                type="number"
                                min="0"
                                class="w-28"
                            />
                            <InputError
                                :message="form.errors.sort"
                            />
                        </div>
                    </div>

                    <!-- Рекламные позиции -->
                    <div class="mb-3 grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="flex items-center gap-2">
                            <ActivityCheckbox
                                id="left"
                                v-model="form.left"
                            />
                            <LabelCheckbox
                                for="left"
                                :text="t('left')"
                            />
                        </div>
                        <div class="flex items-center gap-2">
                            <ActivityCheckbox
                                id="main"
                                v-model="form.main"
                            />
                            <LabelCheckbox
                                for="main"
                                :text="t('main')"
                            />
                        </div>
                        <div class="flex items-center gap-2">
                            <ActivityCheckbox
                                id="right"
                                v-model="form.right"
                            />
                            <LabelCheckbox
                                for="right"
                                :text="t('right')"
                            />
                        </div>
                    </div>

                    <!-- Маркетинговые флаги -->
                    <div class="mb-3 grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="flex items-center gap-2">
                            <ActivityCheckbox
                                id="is_new"
                                v-model="form.is_new"
                            />
                            <LabelCheckbox
                                for="is_new"
                                :text="t('sortIsNew')"
                            />
                        </div>
                        <div class="flex items-center gap-2">
                            <ActivityCheckbox
                                id="is_hit"
                                v-model="form.is_hit"
                            />
                            <LabelCheckbox
                                for="is_hit"
                                :text="t('sortIsHit')"
                            />
                        </div>
                        <div class="flex items-center gap-2">
                            <ActivityCheckbox
                                id="is_sale"
                                v-model="form.is_sale"
                            />
                            <LabelCheckbox
                                for="is_sale"
                                :text="t('sortIsSale')"
                            />
                        </div>
                    </div>

                    <!-- Компания, магазин -->
                    <section class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-3">
                        <div>
                            <LabelInput
                                for="market_company_id"
                                value="Компания"
                            />
                            <VueMultiselect
                                id="market_company_id"
                                v-model="selectedCompany"
                                :options="companyOptions"
                                label="label"
                                track-by="id"
                                :allow-empty="true"
                                :searchable="true"
                                placeholder="Выберите компанию"
                            />
                            <InputError
                                :message="form.errors.market_company_id"
                            />
                        </div>
                        <div>
                            <LabelInput
                                for="market_shop_id"
                                value="Магазин"
                            />
                            <VueMultiselect
                                id="market_shop_id"
                                v-model="selectedShop"
                                :options="shopOptions"
                                label="label"
                                track-by="id"
                                :allow-empty="true"
                                :searchable="true"
                                placeholder="Выберите магазин"
                            />
                            <InputError
                                :message="form.errors.market_shop_id"
                            />
                        </div>
                    </section>

                    <!-- Переводы -->
                    <section
                        class="my-3 p-3 border border-slate-300
                               dark:border-slate-500 bg-white
                               dark:bg-slate-800 rounded-sm"
                    >
                        <TranslationTabs
                            v-model="activeLocale"
                            :translations="form.translations"
                            :available-locales="availableLocales"
                            :make-translation="makeTranslation"
                            @update:translations="form.translations = $event"
                            @removed="toast.warning(t('translationRemoved'))"
                            @added="toast.success(t('localeAdded'))"
                        />
                        <div class="mb-3">
                            <LabelInput
                                :for="`title-${activeLocale}`"
                            >
                                <span class="text-red-500">*</span>
                                {{ t('title') }}
                                [{{ activeLocale.toUpperCase() }}]
                            </LabelInput>
                            <InputText
                                :id="`title-${activeLocale}`"
                                v-model="currentTranslation.title"
                                type="text"
                                maxlength="255"
                            />
                            <InputError
                                :message="getTranslationError('title')"
                            />
                        </div>
                        <div class="mb-3">
                            <LabelInput
                                :for="`subtitle-${activeLocale}`"
                                :value="`${t('subtitle')} [${activeLocale.toUpperCase()}]`"
                            />
                            <InputText
                                :id="`subtitle-${activeLocale}`"
                                v-model="currentTranslation.subtitle"
                                type="text"
                                maxlength="255"
                            />
                            <InputError
                                :message="getTranslationError('subtitle')"
                            />
                        </div>
                        <div class="mb-3">
                            <div class="flex justify-between gap-3">
                                <LabelInput
                                    :for="`short-${activeLocale}`"
                                    :value="`${t('shortDescription')} [${activeLocale.toUpperCase()}]`"
                                />
                                <span class="text-xs text-slate-500">
                                    {{ (currentTranslation.short || '').length }} / 255
                                </span>
                            </div>
                            <MetaDescTextarea
                                :id="`short-${activeLocale}`"
                                v-model="currentTranslation.short"
                                class="w-full"
                            />
                            <InputError
                                :message="getTranslationError('short')"
                            />
                        </div>
                        <div class="mb-3">
                            <LabelInput
                                :for="`description-${activeLocale}`"
                                :value="`${t('description')} [${activeLocale.toUpperCase()}]`"
                            />
                            <TinyEditor
                                v-model="currentTranslation.description"
                                :height="400"
                            />
                            <InputError
                                :message="getTranslationError('description')"
                            />
                        </div>
                        <div class="mb-3">
                            <LabelInput
                                :for="`meta-title-${activeLocale}`"
                                :value="`${t('metaTitle')} [${activeLocale.toUpperCase()}]`"
                            />
                            <InputText
                                :id="`meta-title-${activeLocale}`"
                                v-model="currentTranslation.meta_title"
                                type="text"
                                maxlength="255"
                            />
                            <InputError
                                :message="getTranslationError('meta_title')"
                            />
                        </div>
                        <div class="mb-3">
                            <LabelInput
                                :for="`meta-keywords-${activeLocale}`"
                                :value="`${t('metaKeywords')} [${activeLocale.toUpperCase()}]`"
                            />
                            <InputText
                                :id="`meta-keywords-${activeLocale}`"
                                v-model="currentTranslation.meta_keywords"
                                type="text"
                                maxlength="255"
                            />
                            <InputError
                                :message="getTranslationError('meta_keywords')"
                            />
                        </div>
                        <div class="mb-3">
                            <LabelInput
                                :for="`meta-desc-${activeLocale}`"
                                :value="`${t('metaDescription')} [${activeLocale.toUpperCase()}]`"
                            />
                            <MetaDescTextarea
                                :id="`meta-desc-${activeLocale}`"
                                v-model="currentTranslation.meta_desc"
                                class="w-full"
                            />
                            <InputError
                                :message="getTranslationError('meta_desc')"
                            />
                        </div>
                        <div class="mb-3 flex justify-end gap-2">
                            <ClearMetaButton
                                type="button"
                                @click="clearMetaFields"
                            >
                                {{ t('clearMetaFields') }}
                            </ClearMetaButton>
                            <MetatagsButton
                                type="button"
                                @click="generateMetatags"
                            >
                                {{ t('generateMetaTags') }}
                            </MetatagsButton>
                        </div>
                    </section>

                    <!-- URL -->
                    <div class=" mb-3">
                        <LabelInput for="url">
                            <span class="text-red-500">*</span>
                            URL
                        </LabelInput>
                        <InputText
                            id="url"
                            v-model="form.url"
                            type="text"
                            maxlength="500"
                            @focus="handleUrlInputFocus"
                        />
                        <InputError
                            :message="form.errors.url"
                        />
                    </div>

                    <!-- Коды -->
                    <section class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-3">
                        <div>
                            <LabelInput
                                for="vendor_code"
                                value="Артикул поставщика"
                            />

                            <InputText
                                id="vendor_code"
                                v-model="form.vendor_code"
                                type="text"
                                maxlength="100"
                            />

                            <InputError
                                :message="form.errors.vendor_code"
                            />
                        </div>
                        <div>
                            <LabelInput
                                for="sku"
                                value="SKU"
                            />

                            <InputText
                                id="sku"
                                v-model="form.sku"
                                type="text"
                                maxlength="100"
                            />

                            <InputError
                                :message="form.errors.sku"
                            />
                        </div>
                        <div>
                            <LabelInput
                                for="barcode"
                                value="Штрихкод"
                            />

                            <InputText
                                id="barcode"
                                v-model="form.barcode"
                                type="text"
                                maxlength="100"
                            />

                            <InputError
                                :message="form.errors.barcode"
                            />
                        </div>
                    </section>

                    <!-- Валюта -->
                    <section class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-3">
                        <div class="flex items-center gap-2">
                            <ActivityCheckbox
                                id="calculate_price"
                                v-model="form.calculate_price"
                            />
                            <LabelCheckbox
                                for="calculate_price"
                                text="Рассчитывать цену автоматически"
                            />
                        </div>
                        <div>
                            <LabelInput
                                for="currency_id"
                                :value="t('currency')"
                            />

                            <VueMultiselect
                                id="currency_id"
                                v-model="selectedCurrency"
                                :options="currencyOptions"
                                label="label"
                                track-by="id"
                                :allow-empty="true"
                                :searchable="true"
                                placeholder="Выберите валюту"
                            />

                            <InputError
                                :message="form.errors.currency_id"
                            />
                        </div>
                    </section>

                    <!-- Цены комплекта -->
                    <section
                        class="mb-3 p-4 border border-slate-300
                               dark:border-slate-500 bg-white
                               dark:bg-slate-800 rounded-sm"
                    >
                        <h3 class="mb-3 text-lg font-semibold text-slate-800 dark:text-slate-100">
                            {{ t('price') }}
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-3">
                            <div>
                                <LabelInput
                                    for="price"
                                    :value="t('price')"
                                />
                                <MarketProductPriceInput
                                    id="price"
                                    v-model="form.price"
                                    :currency="selectedCurrency"
                                    :fraction-digits="2"
                                    :disabled="form.calculate_price"
                                />
                                <p
                                    v-if="form.calculate_price"
                                    class="mt-1 text-xs text-slate-500"
                                >
                                    {{ t('calculatedPositions') }}
                                </p>
                                <InputError
                                    :message="form.errors.price"
                                />
                            </div>
                            <div>
                                <LabelInput
                                    for="old_price"
                                    :value="t('compareAtPrice')"
                                />
                                <MarketProductPriceInput
                                    id="old_price"
                                    v-model="form.old_price"
                                    :currency="selectedCurrency"
                                    :fraction-digits="2"
                                />
                                <InputError
                                    :message="form.errors.old_price"
                                />
                            </div>
                            <div>
                                <LabelInput
                                    for="purchase_price"
                                    :value="t('purchasePrice')"
                                />
                                <MarketProductPriceInput
                                    id="purchase_price"
                                    v-model="form.purchase_price"
                                    :currency="selectedCurrency"
                                    :fraction-digits="2"
                                />
                                <InputError
                                    :message="form.errors.purchase_price"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                            <div>
                                <LabelInput
                                    for="wholesale_price"
                                    :value="t('wholesalePrice')"
                                />
                                <MarketProductPriceInput
                                    id="wholesale_price"
                                    v-model="form.wholesale_price"
                                    :currency="selectedCurrency"
                                    :fraction-digits="2"
                                />
                                <InputError
                                    :message="form.errors.wholesale_price"
                                />
                            </div>
                            <div>
                                <LabelInput
                                    for="wholesale_min_quantity"
                                    :value="t('wholesaleMinQuantity')"
                                />
                                <InputNumber
                                    id="wholesale_min_quantity"
                                    v-model.number="form.wholesale_min_quantity"
                                    type="number"
                                    min="1"
                                />
                                <InputError
                                    :message="form.errors.wholesale_min_quantity"
                                />
                            </div>
                        </div>
                    </section>

                    <!-- Состав комплекта -->
                    <section
                        class="mb-5 p-4 border border-blue-300
                               dark:border-blue-600 bg-blue-50/50
                               dark:bg-slate-800 rounded-sm"
                    >
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between
                                    gap-3 mb-4">
                            <div>
                                <h3 class="text-center text-lg font-semibold
                                           text-slate-800 dark:text-slate-100">
                                    {{ t('kitComposition') }}
                                </h3>

                                <p class="text-sm text-slate-600 dark:text-slate-300">
                                    {{ t('addTwoItems') }}
                                </p>
                            </div>

                            <button
                                type="button"
                                class="flex items-center gap-3
                                       px-2 py-0.5 rounded-sm bg-cyan-600
                                       hover:bg-cyan-700 text-white text-sm"
                                @click="addItem"
                            >
                                <svg class="w-4 h-4 fill-current opacity-50 shrink-0"
                                     viewBox="0 0 16 16">
                                    <path
                                        d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"></path>
                                </svg>
                                <span>{{ t('addPosition') }}</span>
                            </button>
                        </div>

                        <InputError
                            class="mb-3"
                            :message="form.errors.items"
                        />
                        <div
                            v-for="(item, index) in form.items"
                            :key="`bundle-item-${index}`"
                            class="mb-4 p-4 border border-slate-300
                                   dark:border-slate-600 bg-white
                                   dark:bg-slate-700 rounded-sm"
                        >
                            <div class="flex items-center justify-between gap-3 mb-3">
                                <h4 class="font-semibold text-slate-700 dark:text-slate-100">
                                    {{ t('position') }} № {{ index + 1 }}
                                </h4>

                                <div class="flex items-center gap-2">
                                    <button
                                        type="button"
                                        class="px-2 py-0 border border-slate-400 rounded-sm"
                                        :disabled="index === 0"
                                        @click="moveItem(index, -1)"
                                    >
                                        ↑
                                    </button>
                                    <button
                                        type="button"
                                        class="px-2 py-0 border border-slate-400 rounded-sm"
                                        :disabled="index === form.items.length - 1"
                                        @click="moveItem(index, 1)"
                                    >
                                        ↓
                                    </button>
                                    <button
                                        type="button"
                                        class="flex items-center gap-3
                                               px-3 py-1 rounded-sm bg-red-500
                                               hover:bg-red-700 text-white"
                                        @click="removeItem(index)"
                                    >
                                        <svg class="w-3 h-3 fill-current shrink-0"
                                             viewBox="0 0 16 16">
                                            <path
                                                d="M5 7h2v6H5V7zm4 0h2v6H9V7zm3-6v2h4v2h-1v10c0 .6-.4 1-1 1H2c-.6 0-1-.4-1-1V5H0V3h4V1c0-.6.4-1 1-1h6c.6 0 1 .4 1 1zM6 2v1h4V2H6zm7 3H3v9h10V5z"></path>
                                        </svg>
                                        <span class="text-xs">{{ t('delete') }}</span>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3 flex justify-between flex-col
                                        lg:flex-row items-center gap-3">
                                <div class="flex items-center gap-2 pt-6">
                                    <ActivityCheckbox
                                        :id="`item-activity-${index}`"
                                        v-model="item.activity"
                                    />
                                    <LabelCheckbox
                                        :for="`item-activity-${index}`"
                                        :text="t('activity')"
                                    />
                                    <InputError
                                        :message="getItemError(index, 'activity')"
                                    />
                                </div>
                                <div>
                                    <LabelInput :value="t('sort')" />
                                    <InputNumber
                                        v-model.number="item.sort"
                                        type="number"
                                        min="0"
                                        disabled
                                    />
                                    <InputError
                                        :message="getItemError(index, 'sort')"
                                    />
                                </div>
                            </div>

                            <div class="mb-3">
                                <div>
                                    <LabelInput>
                                        <span class="text-red-500">*</span>
                                        {{ t('marketProduct') }}
                                    </LabelInput>
                                    <VueMultiselect
                                        :model-value="selectedProduct(item)"
                                        :options="productOptions"
                                        label="label"
                                        track-by="id"
                                        :allow-empty="true"
                                        :searchable="true"
                                        placeholder="Выберите товар"
                                        @update:model-value="selectProduct(index, $event)"
                                    />
                                    <InputError
                                        :message="getItemError(index, 'market_product_id')"
                                    />
                                </div>
                            </div>

                            <div class="mb-3">
                                <div>
                                    <LabelInput :value="t('marketProductVariant')" />
                                    <VueMultiselect
                                        :model-value="selectedVariant(item)"
                                        :options="variantsForItem(item)"
                                        label="label"
                                        track-by="id"
                                        :allow-empty="true"
                                        :searchable="true"
                                        :disabled="!item.market_product_id"
                                        :placeholder="t('withoutFixingIOption')"
                                        @update:model-value="selectVariant(index, $event)"
                                    />
                                    <InputError
                                        :message="getItemError(index, 'market_product_variant_id')"
                                    />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-3">
                                <div>
                                    <LabelInput :value="t('yourUnitPrice')" />
                                    <MarketProductPriceInput
                                        v-model="item.unit_price"
                                        :currency="selectedCurrency"
                                        :fraction-digits="2"
                                    />
                                    <InputError
                                        :message="getItemError(index, 'unit_price')"
                                    />
                                </div>
                                <div>
                                    <LabelInput :value="t('quantity')" />
                                    <InputNumber
                                        v-model.number="item.quantity"
                                        type="number"
                                        min="1"
                                    />
                                    <InputError
                                        :message="getItemError(index, 'quantity')"
                                    />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-3">
                                <div>
                                    <LabelInput :value="t('discountType')" />
                                    <select
                                        v-model="item.discount_type"
                                        class="w-full px-2 py-0.5 form-select rounded-sm
                                               bg-white dark:bg-cyan-800
                                               dark:text-slate-100 text-gray-600
                                               border border-slate-400
                                               dark:border-slate-600"
                                    >
                                        <option :value="null">
                                            {{ t('discountNone') }}
                                        </option>
                                        <option value="fixed">
                                            {{ t('discountFixed') }}
                                        </option>
                                        <option value="percent">
                                            {{ t('discountPercent') }}
                                        </option>
                                    </select>
                                    <InputError
                                        :message="getItemError(index, 'discount_type')"
                                    />
                                </div>
                                <div>
                                    <LabelInput :value="t('discountAmount')" />
                                    <InputNumber
                                        v-model.number="item.discount_value"
                                        type="number"
                                        min="0"
                                        :max="item.discount_type === 'percent' ? 100 : undefined"
                                        :disabled="!item.discount_type"
                                    />
                                    <InputError
                                        :message="getItemError(index, 'discount_value')"
                                    />
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Публикация -->
                    <section class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                        <div>
                            <LabelInput
                                for="status"
                                :value="t('status')"
                            />

                            <select
                                id="status"
                                v-model="form.status"
                                class="w-full px-2 py-1 form-select rounded-sm
                                       bg-white dark:bg-cyan-800
                                       dark:text-slate-100 text-gray-600
                                       border border-slate-400
                                       dark:border-slate-600"
                            >
                                <option value="draft">
                                    {{ t('statusDraft') }}
                                </option>

                                <option value="published">
                                    {{ t('statusPublished') }}
                                </option>

                                <option value="archived">
                                    {{ t('statusArchived') }}
                                </option>
                            </select>

                            <InputError
                                :message="form.errors.status"
                            />
                        </div>

                        <div>
                            <LabelInput
                                for="published_at"
                                :value="t('publishedAt')"
                            />

                            <InputText
                                id="published_at"
                                v-model="form.published_at"
                                type="datetime-local"
                            />

                            <InputError
                                :message="form.errors.published_at"
                            />
                        </div>

                        <div>
                            <LabelInput
                                for="show_from_at"
                                :value="t('showFromAt')"
                            />

                            <InputText
                                id="show_from_at"
                                v-model="form.show_from_at"
                                type="datetime-local"
                            />

                            <InputError
                                :message="form.errors.show_from_at"
                            />
                        </div>

                        <div>
                            <LabelInput
                                for="show_to_at"
                                :value="t('showToAt')"
                            />

                            <InputText
                                id="show_to_at"
                                v-model="form.show_to_at"
                                type="datetime-local"
                            />

                            <InputError
                                :message="form.errors.show_to_at"
                            />
                        </div>
                    </section>

                    <!-- Изображения -->
                    <section class="mt-5">
                        <template v-if="imageProcessorEnabled">
                            <MultiImagePresetEdit
                                :images="existingImages"
                                :preset="galleryPreset"
                                @update:images="handleExistingImagesUpdate"
                                @delete:image="handleDeleteExistingImage"
                            />

                            <MultiImagePresetUpload
                                :images="newImages"
                                :preset="galleryPreset"
                                @update:images="handleNewImagesUpdate"
                            />
                        </template>

                        <template v-else>
                            <MultiImageEdit
                                :images="existingImages"
                                @update:images="handleExistingImagesUpdate"
                                @delete:image="handleDeleteExistingImage"
                            />

                            <MultiImageUpload
                                :images="newImages"
                                @update:images="handleNewImagesUpdate"
                            />
                        </template>

                        <div
                            v-if="newImages.length"
                            class="mt-2 text-xs text-slate-600 dark:text-slate-300"
                        >
                            {{ t('images') }}:
                            {{ newImages.length }}
                        </div>

                        <InputError
                            :message="form.errors.images"
                        />
                    </section>

                    <!-- Кнопки -->
                    <div class="flex items-center justify-center mt-6">
                        <DefaultButton
                            :href="route('admin.marketProductBundles.index')"
                        >
                            <template #icon>
                                <svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                                     viewBox="0 0 16 16">
                                    <path
                                        d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z" />
                                </svg>
                            </template>
                            {{ t('back') }}
                        </DefaultButton>

                        <PrimaryButton
                            class="ms-4 mb-0"
                            :class="{
                                'opacity-25': form.processing,
                            }"
                            :disabled="form.processing"
                        >
                            {{ t('save') }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<style src="/resources/css/vue-multiselect.min.css"></style>
