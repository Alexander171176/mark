<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов
 *
 * Редактирование варианта товара маркетплейса.
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
import MarketProductMeasureInput from '@/Components/Admin/UI/Input/MarketProductMeasureInput.vue'
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

const { t } = useI18n()
const toast = useToast()

/* ======================== Props ======================== */

const props = defineProps({
    variant: {
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

    products: {
        type: [Array, Object],
        default: () => [],
    },

    currencies: {
        type: [Array, Object],
        default: () => [],
    },

    attributes: {
        type: [Array, Object],
        default: () => [],
    },

    selectedProductId: {
        type: [Number, String, null],
        default: null,
    },

    errors: {
        type: Object,
        default: () => ({}),
    },
})

/* ======================== Helpers ======================== */

/** Преобразовать Resource или ResourceCollection в обычный массив. */
function resourceList(value) {
    if (Array.isArray(value)) {
        return value
    }

    if (Array.isArray(value?.data)) {
        return value.data
    }

    return []
}

/** Получить отображаемое название сущности. */
function translationTitle(item) {
    return item?.translation?.title
        || item?.code
        || `ID: ${item?.id}`
}

/** Создать пустой объект перевода. */
function makeTranslation() {
    return {
        title: '',
        subtitle: '',
        short: '',
        description: '',
        meta_title: '',
        meta_keywords: '',
        meta_desc: '',
    }
}

/** Преобразовать переводы ресурса в объект по локалям. */
function normalizeTranslations(translations) {
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
        result[props.currentLocale || 'ru'] = makeTranslation()
    }

    return result
}

/** Преобразовать nullable-значение ресурса в значение input. */
function normalizeNullableInput(value) {
    return value === null || value === undefined
        ? ''
        : value
}

/** Преобразовать значение в число с заданной точностью или null. */
function nullableNumber(value, digits = 2) {
    if (value === '' || value === null || typeof value === 'undefined') {
        return null
    }

    const number = Number(value)

    return Number.isFinite(number)
        ? Number(number.toFixed(digits))
        : null
}

/** Преобразовать дату ресурса в формат datetime-local. */
function toDateTimeLocal(value) {
    if (!value) {
        return ''
    }

    const date = new Date(value)

    if (Number.isNaN(date.getTime())) {
        return String(value).slice(0, 16)
    }

    const pad = (number) =>
        String(number).padStart(2, '0')

    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`
}

/** Обрезать текст до указанной длины. */
function truncateText(text, maxLength, addEllipsis = false) {
    if (!text) {
        return ''
    }

    const value = String(text)

    if (value.length <= maxLength) {
        return value
    }

    const lastSpaceIndex = value.lastIndexOf(
        ' ',
        maxLength
    )

    const truncated = lastSpaceIndex === -1
        ? value.substring(0, maxLength)
        : value.substring(0, lastSpaceIndex)

    return addEllipsis ? `${truncated}...` : truncated
}

/* ======================== Resources and options ======================== */

/** Текущий вариант товара из Resource. */
const variant = computed(() => {
    return props.variant?.data || props.variant || {}
})

/** Список товаров. */
const productList = computed(() => resourceList(props.products))

/** Список валют. */
const currencyList = computed(() => resourceList(props.currencies))

/** Список характеристик вариантов. */
const attributeList = computed(() => resourceList(props.attributes))

/** Товары для VueMultiselect. */
const productOptions = computed(() => {
    return productList.value.map((item) => ({
        ...item,
        label: `[ID: ${item.id}] ${translationTitle(item)}`,
    }))
})

/** Валюты для VueMultiselect. */
const currencyOptions = computed(() => {
    return currencyList.value.map((item) => ({
        ...item,
        label: `[${item.code}] ${item.name || item.title || item.code}${item.symbol ? ` — ${item.symbol}` : ''}`,
    }))
})

/** Характеристики для VueMultiselect. */
const attributeOptions = computed(() => {
    return attributeList.value.map((item) => ({
        ...item,
        label: `[ID: ${item.id}] ${translationTitle(item)}`,
    }))
})

/* ======================== Initial data ======================== */

/** Переводы варианта, подготовленные для TranslationTabs. */
const initialTranslations = normalizeTranslations(
    variant.value.translations
)

/** Локаль формы по умолчанию. */
const defaultLocale = initialTranslations[props.currentLocale]
    ? props.currentLocale
    : Object.keys(initialTranslations)[0] || 'ru'

/** Исходные строки значений характеристик варианта. */
const initialValues = resourceList(
    variant.value.values
).map((item, index) => ({
    id: item?.id ? Number(item.id) : null,

    market_attribute_id: Number(
        item.market_attribute_id || 0
    ),

    market_attribute_value_id: Number(
        item.market_attribute_value_id || 0
    ),

    sort: Number(item.sort ?? index),
}))

/** Исходные изображения варианта. */
const initialImages = resourceList(
    variant.value.images
)
    .filter((image) => image?.id)
    .map((image, index) => ({
        id: Number(image.id),

        url:
            image.webp_url ||
            image.image_url ||
            image.thumb_url ||
            image.url ||
            '',

        order: Number(
            image.pivot?.order ??
            image.order ??
            index
        ),

        alt: image.alt || '',
        caption: image.caption || '',
    }))
    .sort((left, right) => left.order - right.order)

/* ======================== Form and state ======================== */

/** Активная вкладка переводов. */
const activeLocale = ref(defaultLocale)

/** Выбранные значения характеристик по ID характеристик. */
const selectedAttributeValues = ref({})

/** Существующие изображения варианта. */
const existingImages = ref(initialImages)

/** Новые изображения варианта. */
const newImages = ref([])

/** Форма редактирования варианта товара. */
const form = useForm({
    market_product_id: Number(
        variant.value.market_product_id
        || props.selectedProductId
        || 0
    ) || null,

    currency_id: variant.value.currency_id
        ? Number(variant.value.currency_id)
        : null,

    code: variant.value.code || '',
    sku: variant.value.sku || '',
    vendor_code: variant.value.vendor_code || '',
    barcode: variant.value.barcode || '',

    price: normalizeNullableInput(
        variant.value.price
    ),

    old_price: normalizeNullableInput(
        variant.value.old_price
    ),

    purchase_price: normalizeNullableInput(
        variant.value.purchase_price
    ),

    wholesale_price: normalizeNullableInput(
        variant.value.wholesale_price
    ),

    wholesale_min_quantity: normalizeNullableInput(
        variant.value.wholesale_min_quantity
    ),

    quantity: Number(variant.value.quantity || 0),
    in_stock: Boolean(variant.value.in_stock),

    weight: normalizeNullableInput(
        variant.value.weight
    ),

    length: normalizeNullableInput(
        variant.value.length
    ),

    width: normalizeNullableInput(
        variant.value.width
    ),

    height: normalizeNullableInput(
        variant.value.height
    ),

    sort: Number(variant.value.sort || 0),
    activity: Boolean(variant.value.activity),
    status: variant.value.status || 'draft',

    published_at: toDateTimeLocal(
        variant.value.published_at
    ),

    show_from_at: toDateTimeLocal(
        variant.value.show_from_at
    ),

    show_to_at: toDateTimeLocal(
        variant.value.show_to_at
    ),

    values: initialValues,
    images: [],
    deletedImages: [],

    translations: initialTranslations,
})

/**
 * Выбранный родительский товар.
 *
 * form.market_product_id — единственный источник истины.
 */
const selectedProduct = computed({
    get() {
        if (!form.market_product_id) {
            return null
        }

        return productOptions.value.find(
            (item) => Number(item.id) === Number(form.market_product_id)
        ) || null
    },

    set(value) {
        form.market_product_id = value?.id
            ? Number(value.id)
            : null
    },
})

/**
 * Выбранная собственная валюта варианта.
 *
 * form.currency_id — единственный источник истины.
 */
const selectedCurrency = computed({
    get() {
        if (!form.currency_id) {
            return null
        }

        return currencyOptions.value.find(
            (item) => Number(item.id) === Number(form.currency_id)
        ) || null
    },

    set(value) {
        form.currency_id = value?.id
            ? Number(value.id)
            : null
    },
})

/* ======================== Variant values ======================== */

/** Получить значения конкретной характеристики. */
function attributeValues(attribute) {
    if (Array.isArray(attribute?.values)) {
        return attribute.values
    }

    if (Array.isArray(attribute?.values?.data)) {
        return attribute.values.data
    }

    return []
}

/** Подготовить значения характеристики для VueMultiselect. */
function valueOptions(attribute) {
    return attributeValues(attribute).map((value) => ({
        ...value,
        label: `[ID: ${value.id}] ${translationTitle(value)}`,
    }))
}

/** Синхронизировать выбранные характеристики с данными формы. */
function syncVariantValues() {
    form.values = attributeOptions.value
        .map((attribute, index) => {
            const selectedValue =
                selectedAttributeValues.value[attribute.id]

            if (!selectedValue?.id) {
                return null
            }

            const existingRow = initialValues.find(
                (row) =>
                    Number(row.market_attribute_id) ===
                    Number(attribute.id)
            )

            return {
                id: existingRow?.id || null,
                market_attribute_id: Number(attribute.id),
                market_attribute_value_id: Number(
                    selectedValue.id
                ),
                sort: index,
            }
        })
        .filter(Boolean)
}

/** Обновить выбранное значение характеристики. */
function updateAttributeValue(attribute, value) {
    selectedAttributeValues.value = {
        ...selectedAttributeValues.value,
        [attribute.id]: value || null,
    }

    syncVariantValues()
}

/** Очистить выбранные характеристики варианта. */
function clearVariantValues() {
    selectedAttributeValues.value = {}
    form.values = []
}

/* ======================== Initial selections ======================== */

/** Признак завершённой инициализации выбранных характеристик. */
let attributeSelectionsInitialized = false

/** Инициализировать выбранные значения характеристик из variant.values. */
function initializeAttributeSelections() {
    if (attributeSelectionsInitialized || !attributeOptions.value.length) {
        return
    }

    const selectedValues = {}

    initialValues.forEach((row) => {
        const attribute = attributeOptions.value.find(
            (item) =>
                Number(item.id) ===
                Number(row.market_attribute_id)
        )

        if (!attribute) {
            return
        }

        const selectedValue = valueOptions(attribute).find(
            (item) =>
                Number(item.id) ===
                Number(row.market_attribute_value_id)
        )

        if (selectedValue) {
            selectedValues[attribute.id] = selectedValue
        }
    })

    selectedAttributeValues.value = selectedValues
    syncVariantValues()
    attributeSelectionsInitialized = true
}

/* ======================== Selection watchers ======================== */

/** Инициализировать выбранные характеристики после загрузки справочника. */
watch(
    attributeOptions,
    () => {
        initializeAttributeSelections()
    },
    {
        immediate: true,
    }
)

/** Очистить характеристики после смены родительского товара. */
watch(
    () => form.market_product_id,
    (newValue, oldValue) => {
        if (
            !oldValue
            || Number(newValue) === Number(oldValue)
        ) {
            return
        }

        clearVariantValues()
    }
)

/* ======================== Translations ======================== */

/** Текущий перевод активной локали. */
const currentTranslation = computed(() => {
    if (!form.translations[activeLocale.value]) {
        form.translations[activeLocale.value] =
            makeTranslation()
    }

    return form.translations[activeLocale.value]
})

/** Получить ошибку поля текущего перевода. */
function getTranslationError(key) {
    return form.errors[
        `translations.${activeLocale.value}.${key}`
        ]
}

/** Автоматически сформировать системный code из названия. */
function handleCodeInputFocus() {
    if (!form.code && currentTranslation.value.title) {
        form.code = transliterate(
            currentTranslation.value.title.toLowerCase()
        )
    }
}

/** Очистить SEO-поля текущего перевода. */
function clearMetaFields() {
    const translation = currentTranslation.value

    translation.meta_title = ''
    translation.meta_keywords = ''
    translation.meta_desc = ''
}

/** Автоматически заполнить незаполненные SEO-поля. */
function generateMetaFields() {
    const translation = currentTranslation.value

    if (translation.title && !translation.meta_title) {
        translation.meta_title = truncateText(
            translation.title,
            255
        )
    }

    if (!translation.meta_keywords && translation.short) {
        let text = String(translation.short)
            .replace(/(<([^>]+)>)/gi, '')

        text = text.replace(/[.,!?;:()[\]{}"'«»]/g, '')

        const words = text
            .split(/\s+/)
            .filter((word) => word && word.length >= 3)
            .map((word) => word.toLowerCase())
            .filter(
                (value, index, values) =>
                    values.indexOf(value) === index
            )

        translation.meta_keywords = truncateText(
            words.join(', '),
            255
        )
    }

    if (translation.short && !translation.meta_desc) {
        const description = String(translation.short)
            .replace(/(<([^>]+)>)/gi, '')

        translation.meta_desc = truncateText(
            description,
            255,
            true
        )
    }
}

/* ======================== Product inheritance ======================== */

/** Валюта родительского товара. */
const selectedProductCurrency = computed(() => {
    if (!selectedProduct.value?.currency_id) {
        return selectedProduct.value?.currency || null
    }

    return currencyOptions.value.find(
        (item) =>
            Number(item.id) ===
            Number(selectedProduct.value.currency_id)
    ) || selectedProduct.value?.currency || null
})

/** Валюта, применяемая к значениям цен в интерфейсе. */
const effectiveCurrencyPreview = computed(() => {
    return selectedCurrency.value || selectedProductCurrency.value
})

/** Ценовые значения, наследуемые от родительского товара. */
const productPrice = computed(
    () => selectedProduct.value?.price ?? null
)

const productOldPrice = computed(
    () => selectedProduct.value?.old_price ?? null
)

const productPurchasePrice = computed(
    () => selectedProduct.value?.purchase_price ?? null
)

const productWholesalePrice = computed(
    () => selectedProduct.value?.wholesale_price ?? null
)

const productWholesaleMinQuantity = computed(
    () => selectedProduct.value?.wholesale_min_quantity ?? null
)

/** Физические параметры, наследуемые от родительского товара. */
const productWeight = computed(
    () => selectedProduct.value?.weight ?? null
)

const productLength = computed(
    () => selectedProduct.value?.length ?? null
)

const productWidth = computed(
    () => selectedProduct.value?.width ?? null
)

const productHeight = computed(
    () => selectedProduct.value?.height ?? null
)

/* ======================== Images ======================== */

/** Пресет загрузчиков изображений. */
const galleryPreset = computed(() => props.imagePreset)

/** Обновить порядок и данные существующих изображений. */
function handleExistingImagesUpdate(images) {
    existingImages.value = (images || []).map(
        (image, index) => ({
            ...image,
            order: index,
        })
    )
}

/** Пометить существующее изображение для удаления. */
function handleDeleteExistingImage(deletedId) {
    const id = Number(deletedId)

    if (!id) {
        return
    }

    if (!form.deletedImages.includes(id)) {
        form.deletedImages.push(id)
    }

    existingImages.value = existingImages.value
        .filter((image) => Number(image.id) !== id)
        .map((image, index) => ({
            ...image,
            order: index,
        }))
}

/** Нормализировать и сохранить новые изображения. */
function handleNewImagesUpdate(images) {
    newImages.value = (images || []).map(
        (image, index) => ({
            ...image,
            order: index,
        })
    )
}

/* ======================== Submit ======================== */

/** Подготовить данные и обновить вариант товара. */
function submitForm() {
    syncVariantValues()

    form.transform((data) => {
        const transformed = {
            ...data,
            _method: 'put',

            market_product_id: data.market_product_id
                ? Number(data.market_product_id)
                : null,

            currency_id: data.currency_id
                ? Number(data.currency_id)
                : null,

            price: nullableNumber(data.price, 2),
            old_price: nullableNumber(data.old_price, 2),
            purchase_price: nullableNumber(
                data.purchase_price,
                2
            ),

            wholesale_price: nullableNumber(
                data.wholesale_price,
                2
            ),

            wholesale_min_quantity:
                data.wholesale_min_quantity === '' ||
                data.wholesale_min_quantity === null
                    ? null
                    : Number(data.wholesale_min_quantity),

            quantity: Number(data.quantity || 0),
            in_stock: data.in_stock ? 1 : 0,

            weight: nullableNumber(data.weight, 3),
            length: nullableNumber(data.length, 2),
            width: nullableNumber(data.width, 2),
            height: nullableNumber(data.height, 2),

            sort: Number(data.sort || 0),
            activity: data.activity ? 1 : 0,

            published_at: data.published_at || null,
            show_from_at: data.show_from_at || null,
            show_to_at: data.show_to_at || null,

            values: data.values.map((item, index) => ({
                id: item.id || null,

                market_attribute_id: Number(
                    item.market_attribute_id
                ),

                market_attribute_value_id: Number(
                    item.market_attribute_value_id
                ),

                sort: index,
            })),
        }

        delete transformed.images
        delete transformed.deletedImages

        let imageIndex = 0

        /** Добавить существующие изображения в FormData. */
        existingImages.value.forEach((image, index) => {
            transformed[`images[${imageIndex}][id]`] =
                Number(image.id)

            transformed[`images[${imageIndex}][order]`] =
                Number(image.order ?? index)

            transformed[`images[${imageIndex}][alt]`] =
                image.alt || ''

            transformed[`images[${imageIndex}][caption]`] =
                image.caption || ''

            imageIndex++
        })

        /** Добавить новые файлы изображений в FormData. */
        newImages.value.forEach((image, index) => {
            if (!image.file) {
                return
            }

            transformed[`images[${imageIndex}][file]`] =
                image.file

            transformed[`images[${imageIndex}][order]`] =
                Number(
                    image.order ??
                    existingImages.value.length + index
                )

            transformed[`images[${imageIndex}][alt]`] =
                image.alt || ''

            transformed[`images[${imageIndex}][caption]`] =
                image.caption || ''

            imageIndex++
        })

        /** Добавить ID удаляемых изображений в FormData. */
        form.deletedImages.forEach((id, index) => {
            transformed[`deletedImages[${index}]`] =
                Number(id)
        })

        return transformed
    })

    form.post(
        route('admin.marketProductVariants.update', {
            marketProductVariant: variant.value.id,
        }),
        {
            forceFormData: true,
            errorBag: 'updateMarketProductVariant',
            preserveScroll: true,

            onSuccess: () => {
                toast.success(
                    'Вариант товара успешно обновлён.'
                )

                newImages.value = []
                form.deletedImages = []
            },

            onError: (errors) => {
                console.error(
                    'Ошибка обновления варианта товара:',
                    errors
                )

                const firstKey = Object.keys(errors || {})[0]

                toast.error(
                    errors?.[firstKey] ||
                    'Проверьте правильность заполнения полей.'
                )
            },
        }
    )
}
</script>

<template>
    <AdminLayout :title="t('editMarketProductVariant')">
        <template #header>
            <TitlePage>{{ t('editMarketProductVariant') }}</TitlePage>
        </template>

        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 shadow-lg shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95">
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <DefaultButton :href="route('admin.marketProductVariants.index')">
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

                <form @submit.prevent="submitForm">

                    <!-- Активность, основной вариант и сортировка -->
                    <div class="mb-4 flex justify-between flex-col lg:flex-row items-center gap-4">
                        <div class="flex flex-col">
                            <div class="flex flex-row items-center gap-2">
                                <ActivityCheckbox
                                    id="activity"
                                    v-model="form.activity"
                                />

                                <LabelCheckbox
                                    for="activity"
                                    :text="t('activity')"
                                    class="text-sm h-8 flex items-center"
                                />
                            </div>

                            <InputError
                                class="mt-2"
                                :message="form.errors.activity"
                            />
                        </div>

                        <div class="flex flex-row items-center gap-2">
                            <LabelInput
                                for="sort"
                                :value="t('sort')"
                                class="text-sm"
                            />

                            <InputNumber
                                id="sort"
                                v-model.number="form.sort"
                                type="number"
                                min="0"
                                class="w-full lg:w-28"
                            />

                            <InputError
                                class="mt-2 lg:mt-0"
                                :message="form.errors.sort"
                            />
                        </div>
                    </div>

                    <!-- Коды -->
                    <div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <LabelInput for="code" :value="t('code')" />
                            <InputText
                                id="code"
                                v-model="form.code"
                                type="text"
                                maxlength="255"
                                @focus="handleCodeInputFocus"
                            />
                            <InputError :message="form.errors.code" />
                        </div>

                        <div>
                            <LabelInput for="sku" :value="t('sku')" />
                            <InputText id="sku" v-model="form.sku" type="text" maxlength="100" />
                            <InputError :message="form.errors.sku" />
                        </div>

                        <div>
                            <LabelInput for="vendor_code" :value="t('vendorCode')" />
                            <InputText
                                id="vendor_code"
                                v-model="form.vendor_code"
                                type="text"
                                maxlength="100" />
                            <InputError :message="form.errors.vendor_code" />
                        </div>

                        <div>
                            <LabelInput for="barcode" :value="t('barcode')" />
                            <InputText id="barcode" v-model="form.barcode" type="text" maxlength="100" />
                            <InputError :message="form.errors.barcode" />
                        </div>
                    </div>

                    <!-- Родительский товар -->
                    <div class="mb-4 flex flex-col items-start">
                        <LabelInput for="market_product_id">
                            <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                            {{ t('marketProduct') }}
                        </LabelInput>

                        <VueMultiselect
                            v-model="selectedProduct"
                            :options="productOptions"
                            label="label"
                            track-by="id"
                            :searchable="true"
                            :allow-empty="false"
                            :show-labels="false"
                            placeholder="Выберите родительский товар"
                        />

                        <InputError class="mt-2" :message="form.errors.market_product_id" />
                    </div>

                    <!-- Информация о выбранном товаре -->
                    <div v-if="selectedProduct"
                         class="mb-5 rounded-sm border border-cyan-300 dark:border-cyan-700 bg-cyan-50 dark:bg-cyan-900/20 p-3">
                        <div class="text-sm font-semibold text-cyan-800 dark:text-cyan-200">
                            {{ translationTitle(selectedProduct) }}
                        </div>

                        <div
                            class="mt-2 grid grid-cols-1 gap-2 text-xs text-slate-600 dark:text-slate-300 md:grid-cols-2 lg:grid-cols-4">
                            <div>{{ t('price') }}: {{ productPrice ?? '—' }}</div>
                            <div>{{ t('currency') }}: {{ selectedProductCurrency?.code || '—' }}</div>
                            <div>{{ t('quantity') }}: {{ selectedProduct.quantity ?? 0 }}</div>
                            <div>{{ t('variants') }}: {{ selectedProduct.variants_count ?? 0 }}</div>
                        </div>
                    </div>

                    <!-- Переводы -->
                    <div
                        class="my-5 p-3 border border-slate-300 dark:border-slate-500 bg-white dark:bg-slate-800 rounded-sm">
                        <TranslationTabs
                            v-model="activeLocale"
                            :translations="form.translations"
                            :available-locales="availableLocales"
                            :make-translation="makeTranslation"
                            @update:translations="form.translations = $event"
                            @removed="toast.warning(t('translationRemoved'))"
                            @added="toast.success(t('localeAdded'))"
                        />

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput :for="`title-${activeLocale}`">
                                <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                {{ t('title') }} [{{ activeLocale.toUpperCase() }}]
                            </LabelInput>

                            <InputText
                                :id="`title-${activeLocale}`"
                                v-model="currentTranslation.title"
                                type="text"
                                required
                                maxlength="255"
                            />

                            <InputError class="mt-2" :message="getTranslationError('title')" />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput :for="`subtitle-${activeLocale}`"
                                        :value="`${t('subtitle')} [${activeLocale.toUpperCase()}]`" />
                            <InputText
                                :id="`subtitle-${activeLocale}`"
                                v-model="currentTranslation.subtitle"
                                type="text"
                                maxlength="255"
                            />
                            <InputError class="mt-2" :message="getTranslationError('subtitle')" />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput :for="`short-${activeLocale}`"
                                        :value="`${t('shortDescription')} [${activeLocale.toUpperCase()}]`" />
                            <InputText
                                :id="`short-${activeLocale}`"
                                v-model="currentTranslation.short"
                                type="text"
                                maxlength="255"
                            />
                            <InputError class="mt-2" :message="getTranslationError('short')" />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput :for="`description-${activeLocale}`"
                                        :value="`${t('description')} [${activeLocale.toUpperCase()}]`" />
                            <TinyEditor
                                :id="`description-${activeLocale}`"
                                v-model="currentTranslation.description"
                            />
                            <InputError class="mt-2" :message="getTranslationError('description')" />
                        </div>

                        <div class="mt-5 border-t border-slate-300 dark:border-slate-600 pt-4">
                            <div class="mb-3 flex flex-col items-start">
                                <LabelInput :for="`meta-title-${activeLocale}`"
                                            :value="`${t('metaTitle')} [${activeLocale.toUpperCase()}]`" />
                                <InputText
                                    :id="`meta-title-${activeLocale}`"
                                    v-model="currentTranslation.meta_title"
                                    type="text"
                                    maxlength="255"
                                />
                                <InputError class="mt-2" :message="getTranslationError('meta_title')" />
                            </div>

                            <div class="mb-3 flex flex-col items-start">
                                <LabelInput :for="`meta-keywords-${activeLocale}`"
                                            :value="`${t('metaKeywords')} [${activeLocale.toUpperCase()}]`" />
                                <InputText
                                    :id="`meta-keywords-${activeLocale}`"
                                    v-model="currentTranslation.meta_keywords"
                                    type="text"
                                    maxlength="255"
                                />
                                <InputError class="mt-2" :message="getTranslationError('meta_keywords')" />
                            </div>

                            <div class="mb-3 flex flex-col items-start">
                                <LabelInput :for="`meta-desc-${activeLocale}`"
                                            :value="`${t('metaDescription')} [${activeLocale.toUpperCase()}]`" />
                                <MetaDescTextarea
                                    :id="`meta-desc-${activeLocale}`"
                                    v-model="currentTranslation.meta_desc"
                                    class="w-full"
                                />
                                <InputError class="mt-2" :message="getTranslationError('meta_desc')" />
                            </div>

                            <div class="flex justify-end gap-2 mt-4">
                                <ClearMetaButton @click.prevent="clearMetaFields">
                                    {{ t('clearMetaFields') }}
                                </ClearMetaButton>

                                <MetatagsButton @click.prevent="generateMetaFields">
                                    {{ t('generateMetaTags') }}
                                </MetatagsButton>
                            </div>
                        </div>
                    </div>

                    <!-- Характеристики варианта -->
                    <div
                        class="mb-5 rounded-sm border border-fuchsia-300 dark:border-fuchsia-700 bg-white dark:bg-slate-800 p-3">
                        <div class="mb-3 text-sm font-semibold text-fuchsia-700 dark:text-fuchsia-300">
                            {{ t('attributes') }}
                            <span class="text-red-500 dark:text-red-300">*</span>
                        </div>

                        <div v-if="attributeOptions.length" class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div
                                v-for="attribute in attributeOptions"
                                :key="attribute.id"
                                class="rounded-sm border border-slate-300 dark:border-slate-600 p-3"
                            >
                                <LabelInput :for="`attribute-${attribute.id}`" :value="attribute.label" />

                                <VueMultiselect
                                    :model-value="selectedAttributeValues[attribute.id] || null"
                                    :options="valueOptions(attribute)"
                                    label="label"
                                    track-by="id"
                                    :searchable="true"
                                    :allow-empty="true"
                                    :show-labels="false"
                                    placeholder="Выберите значение"
                                    @update:model-value="updateAttributeValue(attribute, $event)"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="form.errors[`values.${attributeOptions.findIndex((item) => item.id === attribute.id)}.market_attribute_value_id`]"
                                />
                            </div>
                        </div>

                        <div v-else class="text-sm text-slate-500 dark:text-slate-300">
                            Нет характеристик, разрешённых для формирования вариантов.
                        </div>

                        <InputError class="mt-2" :message="form.errors.values" />
                    </div>

                    <!-- Валюта -->
                    <div class="mb-4 flex flex-col items-start">
                        <LabelInput for="currency_id" :value="t('currency')" />

                        <VueMultiselect
                            v-model="selectedCurrency"
                            :options="currencyOptions"
                            label="label"
                            track-by="id"
                            :searchable="true"
                            :allow-empty="true"
                            :show-labels="false"
                            placeholder="Оставьте пустым для наследования валюты товара"
                        />

                        <div class="mt-1 text-xs text-slate-500 dark:text-slate-300">
                            {{ t('currencyDesc') }}:
                            {{ selectedProductCurrency?.code || 'не выбрана' }}.
                        </div>

                        <InputError class="mt-2" :message="form.errors.currency_id" />
                    </div>

                    <!-- Цены -->
                    <div
                        class="mb-4 rounded-sm border border-emerald-300 dark:border-emerald-700
                               bg-white dark:bg-slate-800 p-3">

                        <div class="mb-3 text-sm font-semibold
                                    text-emerald-700 dark:text-emerald-300">
                            {{ t('optionPrices') }}
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                            <div>
                                <LabelInput for="old_price" :value="t('compareAtPrice')" />
                                <MarketProductPriceInput
                                    id="old_price"
                                    v-model="form.old_price"
                                    :currency="effectiveCurrencyPreview"
                                    :fraction-digits="2"
                                    class="w-full"
                                />
                                <div class="mt-1 text-[10px] text-slate-500 dark:text-slate-300">
                                    {{ t('inherited') }}: {{ productOldPrice ?? '—' }}
                                </div>
                                <InputError :message="form.errors.old_price" />
                            </div>
                            <div>
                                <LabelInput for="price" :value="t('price')" />
                                <MarketProductPriceInput
                                    id="price"
                                    v-model="form.price"
                                    :currency="effectiveCurrencyPreview"
                                    :fraction-digits="2"
                                    class="w-full"
                                />
                                <div class="mt-1 text-[10px] text-slate-500 dark:text-slate-300">
                                    {{ t('inherited') }}: {{ productPrice ?? '—' }}
                                </div>
                                <InputError :message="form.errors.price" />
                            </div>
                            <div>
                                <LabelInput for="purchase_price" :value="t('purchasePrice')" />
                                <MarketProductPriceInput
                                    id="purchase_price"
                                    v-model="form.purchase_price"
                                    :currency="effectiveCurrencyPreview"
                                    :fraction-digits="2"
                                    class="w-full"
                                />
                                <div class="mt-1 text-[10px] text-slate-500 dark:text-slate-300">
                                    {{ t('inherited') }}: {{ productPurchasePrice ?? '—' }}
                                </div>
                                <InputError :message="form.errors.purchase_price" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <LabelInput for="wholesale_price" :value="t('wholesalePrice')" />
                                <MarketProductPriceInput
                                    id="wholesale_price"
                                    v-model="form.wholesale_price"
                                    :currency="effectiveCurrencyPreview"
                                    :fraction-digits="2"
                                    class="w-full"
                                />
                                <div class="mt-1 text-[10px] text-slate-500 dark:text-slate-300">
                                    {{ t('inherited') }}: {{ productWholesalePrice ?? '—' }}
                                </div>
                                <InputError :message="form.errors.wholesale_price" />
                            </div>
                            <div>
                                <LabelInput
                                    for="wholesale_min_quantity"
                                    :value="t('wholesaleMinQuantity')" />
                                <InputNumber
                                    id="wholesale_min_quantity"
                                    v-model.number="form.wholesale_min_quantity"
                                    type="number"
                                    min="1"
                                    class="w-full"
                                />
                                <div class="mt-1 text-[10px] text-slate-500 dark:text-slate-300">
                                    {{ t('inherited') }}: {{ productWholesaleMinQuantity ?? '—' }}
                                </div>
                                <InputError :message="form.errors.wholesale_min_quantity" />
                            </div>
                        </div>
                    </div>

                    <!-- Остаток -->
                    <div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <LabelInput for="quantity" :value="t('quantity')" />

                            <InputNumber
                                id="quantity"
                                v-model.number="form.quantity"
                                type="number"
                                min="0"
                                class="w-full"
                            />

                            <InputError :message="form.errors.quantity" />
                        </div>

                        <div class="flex flex-col justify-end">
                            <div class="flex flex-row items-center gap-2">
                                <ActivityCheckbox
                                    id="in_stock"
                                    v-model="form.in_stock"
                                />

                                <LabelCheckbox
                                    for="in_stock"
                                    :text="t('inStock')"
                                    class="text-sm h-8 flex items-center"
                                />
                            </div>

                            <InputError
                                class="mt-2"
                                :message="form.errors.in_stock"
                            />
                        </div>
                    </div>

                    <!-- Габариты -->
                    <div
                        class="mb-4 rounded-sm border border-violet-300 dark:border-violet-700
                               bg-white dark:bg-slate-800 p-3">
                        <div class="mb-3 text-sm font-semibold
                                    text-violet-700 dark:text-violet-300">
                            {{ t('variantDimensions') }}
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                            <div>
                                <LabelInput for="weight" :value="t('weight')" />
                                <MarketProductMeasureInput
                                    id="weight"
                                    v-model="form.weight"
                                    :fraction-digits="3"
                                    unit="кг"
                                />
                                <div class="mt-1 text-[10px] text-slate-500 dark:text-slate-300">
                                    {{ t('inherited') }}: {{ productWeight ?? '—' }}
                                </div>
                                <InputError :message="form.errors.weight" />
                            </div>

                            <div>
                                <LabelInput for="length" :value="t('length')" />
                                <MarketProductMeasureInput
                                    id="length"
                                    v-model="form.length"
                                    :fraction-digits="2"
                                    unit="см"
                                />
                                <div class="mt-1 text-[10px] text-slate-500 dark:text-slate-300">
                                    {{ t('inherited') }}: {{ productLength ?? '—' }}
                                </div>
                                <InputError :message="form.errors.length" />
                            </div>

                            <div>
                                <LabelInput for="width" :value="t('width')" />
                                <MarketProductMeasureInput
                                    id="width"
                                    v-model="form.width"
                                    :fraction-digits="2"
                                    unit="см"
                                />
                                <div class="mt-1 text-[10px] text-slate-500 dark:text-slate-300">
                                    {{ t('inherited') }}: {{ productWidth ?? '—' }}
                                </div>
                                <InputError :message="form.errors.width" />
                            </div>

                            <div>
                                <LabelInput for="height" :value="t('height')" />
                                <MarketProductMeasureInput
                                    id="height"
                                    v-model="form.height"
                                    :fraction-digits="2"
                                    unit="см"
                                />
                                <div class="mt-1 text-[10px] text-slate-500 dark:text-slate-300">
                                    {{ t('inherited') }}: {{ productHeight ?? '—' }}
                                </div>
                                <InputError :message="form.errors.height" />
                            </div>
                        </div>
                    </div>

                    <!-- Публикация -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-4">
                        <div class="flex flex-col items-start">
                            <LabelInput for="status" :value="t('status')" />

                            <select
                                id="status"
                                v-model="form.status"
                                class="w-full px-2 py-0.5 form-select rounded-sm shadow-sm bg-white dark:bg-cyan-800 dark:text-slate-100 text-gray-600 border border-slate-400 dark:border-slate-600"
                            >
                                <option value="draft">{{ t('statusDraft') }}</option>
                                <option value="published">{{ t('statusPublished') }}</option>
                                <option value="archived">{{ t('statusArchived') }}</option>
                            </select>

                            <InputError class="mt-2" :message="form.errors.status" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="published_at" :value="t('publishedAt')" />
                            <InputText
                                id="published_at"
                                v-model="form.published_at"
                                type="datetime-local"
                            />
                            <InputError class="mt-2" :message="form.errors.published_at" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                        <div class="flex flex-col items-start">
                            <LabelInput for="show_from_at" :value="t('showFromAt')" />
                            <InputText
                                id="show_from_at"
                                v-model="form.show_from_at"
                                type="datetime-local"
                            />
                            <InputError class="mt-2" :message="form.errors.show_from_at" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="show_to_at" :value="t('showToAt')" />
                            <InputText
                                id="show_to_at"
                                v-model="form.show_to_at"
                                type="datetime-local"
                            />
                            <InputError class="mt-2" :message="form.errors.show_to_at" />
                        </div>
                    </div>

                    <!-- Существующие изображения -->
                    <div v-if="existingImages.length" class="mt-4">
                        <MultiImagePresetEdit
                            v-if="imageProcessorEnabled && galleryPreset"
                            :images="existingImages"
                            :preset="galleryPreset"
                            @update:images="handleExistingImagesUpdate"
                            @delete="handleDeleteExistingImage"
                        />

                        <MultiImageEdit
                            v-else
                            :images="existingImages"
                            @update:images="handleExistingImagesUpdate"
                            @delete="handleDeleteExistingImage"
                        />

                        <div class="mt-2 text-xs text-slate-600 dark:text-slate-300">
                            {{ t('images') }}: {{ existingImages.length }}
                        </div>
                    </div>

                    <!-- Новые изображения -->
                    <div class="mt-4">
                        <MultiImagePresetUpload
                            v-if="imageProcessorEnabled && galleryPreset"
                            :images="newImages"
                            :preset="galleryPreset"
                            @update:images="handleNewImagesUpdate"
                        />

                        <MultiImageUpload
                            v-else
                            :images="newImages"
                            @update:images="handleNewImagesUpdate"
                        />

                        <div v-if="newImages.length" class="mt-2 text-xs text-slate-600 dark:text-slate-300">
                            {{ t('newImages') }}: {{ newImages.length }}
                        </div>

                        <InputError class="mt-2" :message="form.errors.images" />
                        <InputError class="mt-2" :message="form.errors.deletedImages" />
                    </div>

                    <!-- Кнопки -->
                    <div class="flex items-center justify-center mt-6">
                        <DefaultButton :href="route('admin.marketProductVariants.index')">
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
                            :class="{ 'opacity-25': form.processing }"
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
