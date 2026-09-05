<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов
 *
 * Создание варианта товара маркетплейса.
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
import MultiImageUpload from '@/Components/Admin/UI/Image/MultiImageUpload.vue'
import MultiImagePresetUpload from '@/Components/Admin/UI/Image/MultiImagePresetUpload.vue'

const { t } = useI18n()
const toast = useToast()

/* ======================== Props ======================== */

const props = defineProps({
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

/** Обрезать текст до указанной длины. */
function truncateText(text, maxLength, addEllipsis = false) {
    if (!text) {
        return ''
    }

    const value = String(text)

    if (value.length <= maxLength) {
        return value
    }

    const lastSpaceIndex = value.lastIndexOf(' ', maxLength)
    const truncated = lastSpaceIndex === -1
        ? value.substring(0, maxLength)
        : value.substring(0, lastSpaceIndex)

    return addEllipsis ? `${truncated}...` : truncated
}

/* ======================== Resources and options ======================== */

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

/* ======================== Form and state ======================== */

/** Локаль формы по умолчанию. */
const defaultLocale = props.currentLocale || 'ru'

/** Активная вкладка переводов. */
const activeLocale = ref(defaultLocale)

/** Выбранные значения характеристик по ID характеристик. */
const selectedAttributeValues = ref({})

/** Новые изображения варианта. */
const newImages = ref([])

/** Форма создания варианта товара. */
const form = useForm({
    market_product_id: props.selectedProductId
        ? Number(props.selectedProductId)
        : null,

    currency_id: null,

    code: '',
    sku: '',
    vendor_code: '',
    barcode: '',

    price: null,
    old_price: null,
    purchase_price: null,
    wholesale_price: null,
    wholesale_min_quantity: null,

    quantity: 0,
    in_stock: false,

    weight: null,
    length: null,
    width: null,
    height: null,

    sort: 0,
    activity: false,
    status: 'draft',

    moderation_status: 0,
    moderated_by: null,
    moderated_at: null,
    moderation_note: null,

    published_at: '',
    show_from_at: '',
    show_to_at: '',

    values: [],
    images: [],

    translations: {
        [defaultLocale]: makeTranslation(),
    },
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
            const selectedValue = selectedAttributeValues.value[attribute.id]

            if (!selectedValue?.id) {
                return null
            }

            return {
                market_attribute_id: Number(attribute.id),
                market_attribute_value_id: Number(selectedValue.id),
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

/* ======================== Selection watchers ======================== */

/** Очистить характеристики после смены родительского товара. */
watch(
    () => form.market_product_id,
    (newValue, oldValue) => {
        if (oldValue && Number(newValue) !== Number(oldValue)) {
            clearVariantValues()
        }
    }
)

/* ======================== Translations ======================== */

/** Текущий перевод активной локали. */
const currentTranslation = computed(() => {
    if (!form.translations[activeLocale.value]) {
        form.translations[activeLocale.value] = makeTranslation()
    }

    return form.translations[activeLocale.value]
})

/** Получить ошибку поля текущего перевода. */
function getTranslationError(key) {
    return form.errors[`translations.${activeLocale.value}.${key}`]
}

/** Автоматически сформировать системный code из названия. */
function handleCodeInputFocus() {
    if (!form.code && currentTranslation.value.title) {
        form.code = transliterate(
            currentTranslation.value.title.toLowerCase()
        )
    }
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

/** Пресет загрузчика изображений. */
const galleryPreset = computed(() => props.imagePreset)

/** Нормализировать и сохранить новые изображения. */
function handleNewImagesUpdate(images) {
    const normalizedImages = (images || []).map(
        (image, index) => ({
            ...image,
            order: index,
        })
    )

    newImages.value = normalizedImages
    form.images = normalizedImages
}

/* ======================== Submit ======================== */

/** Подготовить данные и создать вариант товара. */
function submitForm() {
    syncVariantValues()

    form.transform((data) => {
        const transformed = {
            ...data,

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

            moderation_status: Number(
                data.moderation_status || 0
            ),

            published_at: data.published_at || null,
            show_from_at: data.show_from_at || null,
            show_to_at: data.show_to_at || null,

            values: data.values.map((item, index) => ({
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

        /** Добавить файлы новых изображений в FormData. */
        newImages.value.forEach((image, index) => {
            if (!image.file) {
                return
            }

            transformed[`images[${index}][file]`] =
                image.file

            transformed[`images[${index}][order]`] =
                image.order ?? index

            transformed[`images[${index}][alt]`] =
                image.alt ?? ''

            transformed[`images[${index}][caption]`] =
                image.caption ?? ''
        })

        return transformed
    })

    form.post(
        route('admin.marketProductVariants.store'),
        {
            forceFormData: true,
            errorBag: 'createMarketProductVariant',
            preserveScroll: true,

            onSuccess: () => {
                toast.success(
                    'Вариант товара успешно создан.'
                )

                newImages.value = []
                form.images = []
            },

            onError: (errors) => {
                console.error(
                    'Ошибка создания варианта товара:',
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
    <AdminLayout :title="t('addMarketProductVariant')">
        <template #header>
            <TitlePage>{{ t('addMarketProductVariant') }}</TitlePage>
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

                            <div class="flex justify-end mt-4">
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

                    <!-- Изображения -->
                    <div class="mt-4">
                        <MultiImagePresetUpload
                            v-if="imageProcessorEnabled"
                            :images="newImages"
                            :preset="galleryPreset"
                            @update:images="handleNewImagesUpdate"
                        />

                        <MultiImageUpload
                            v-else
                            :images="newImages"
                            @update:images="handleNewImagesUpdate"
                        />

                        <div v-if="newImages.length" class="text-xs text-slate-600 dark:text-slate-300 mt-2">
                            {{ t('images') }}: {{ newImages.length }}
                        </div>

                        <InputError class="mt-2" :message="form.errors.images" />
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
