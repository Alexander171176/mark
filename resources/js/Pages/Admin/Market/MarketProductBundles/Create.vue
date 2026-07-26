<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Создание комплекта товаров маркетплейса.
 */
import { computed, ref, watch } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
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
import LabelInput from '@/Components/Admin/UI/Input/LabelInput.vue'
import InputText from '@/Components/Admin/UI/Input/InputText.vue'
import InputError from '@/Components/Admin/UI/Input/InputError.vue'
import MetaDescTextarea from '@/Components/Admin/UI/Textarea/MetaDescTextarea.vue'

import TinyEditor from '@/Components/Admin/UI/TinyEditor/TinyEditor.vue'
import TranslationTabs from '@/Components/Admin/UI/Locale/TranslationTabs.vue'
import MultiImageUpload from '@/Components/Admin/UI/Image/MultiImageUpload.vue'
import MultiImagePresetUpload from '@/Components/Admin/UI/Image/MultiImagePresetUpload.vue'

/** Локализация интерфейса */
const { t } = useI18n()

/** Уведомления */
const toast = useToast()

/** Данные текущей Inertia-страницы */
const page = usePage()

/** Входные данные страницы */
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

/** Активная локаль */
const defaultLocale = props.currentLocale || 'ru'
const activeLocale = ref(defaultLocale)

/** Новые изображения */
const newImages = ref([])

/** Валюты для начального выбора */
const initialCurrencies = resourceList(props.currencies)

/** Форма создания комплекта */
const form = useForm({
    user_id: page.props?.auth?.user?.id || null,

    market_company_id: null,
    market_shop_id: null,
    currency_id:
        initialCurrencies.find((item) => item.is_default)?.id
        || initialCurrencies[0]?.id
        || null,

    url: '',
    sku: '',
    vendor_code: '',
    barcode: '',

    calculate_price: true,

    price: 0,
    old_price: null,
    purchase_price: null,
    wholesale_price: null,
    wholesale_min_quantity: null,

    sort: 0,
    activity: false,

    left: false,
    main: false,
    right: false,

    is_new: false,
    is_hit: false,
    is_sale: false,

    status: 'draft',

    published_at: '',
    show_from_at: '',
    show_to_at: '',

    views: 0,
    likes_count: 0,
    rating_avg: 0,
    rating_count: 0,

    items: [
        makeBundleItem(0),
        makeBundleItem(1),
    ],

    images: [],

    translations: {
        [defaultLocale]: makeTranslation(),
    },
})

/** Выбранные связанные сущности */
const selectedCompany = ref(null)
const selectedShop = ref(null)
const selectedCurrency = ref(null)

/** Текущий перевод */
const currentTranslation = computed(() => {
    if (!form.translations[activeLocale.value]) {
        form.translations[activeLocale.value] = makeTranslation()
    }

    return form.translations[activeLocale.value]
})

/** Ошибка поля текущего перевода */
const getTranslationError = (key) => {
    return form.errors[
        `translations.${activeLocale.value}.${key}`
        ]
}

/** Ошибка поля позиции комплекта */
const getItemError = (index, key) => {
    return form.errors[`items.${index}.${key}`]
}

/** Нормализованные справочники */
const companyList = computed(() => resourceList(props.companies))
const shopList = computed(() => resourceList(props.shops))
const currencyList = computed(() => resourceList(props.currencies))
const productList = computed(() => resourceList(props.products))
const variantList = computed(() => resourceList(props.variants))

/** Получить отображаемое название сущности */
const translationTitle = (item) => {
    return item?.translation?.title
        || item?.title
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
    if (!selectedCompany.value?.id) {
        return shopList.value
    }

    return shopList.value.filter((item) => {
        return Number(item.market_company_id)
            === Number(selectedCompany.value.id)
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
        label: `[${item.code}] ${item.name}${item.symbol ? ` — ${item.symbol}` : ''}`,
    }))
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

    const nestedVariants = resourceList(product?.variants)

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
            variant.sku ? `SKU: ${variant.sku}` : '',
        ]
            .filter(Boolean)
            .join(' — '),
    }))
}

/** Найти выбранный вариант позиции */
const selectedVariant = (item) => {
    return variantsForItem(item).find((variant) => {
        return Number(variant.id)
            === Number(item.market_product_variant_id)
    }) || null
}

/** Выбрать товар позиции */
const selectProduct = (index, product) => {
    const item = form.items[index]

    item.market_product_id = product?.id ?? null
    item.market_product_variant_id = null
}

/** Выбрать вариант позиции */
const selectVariant = (index, variant) => {
    form.items[index].market_product_variant_id =
        variant?.id ?? null
}

/** Добавить позицию комплекта */
const addItem = () => {
    form.items.push(
        makeBundleItem(form.items.length)
    )
}

/** Удалить позицию комплекта */
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

    const current = form.items[index]
    form.items[index] = form.items[targetIndex]
    form.items[targetIndex] = current

    normalizeItemsSort()
}

/** Нормализовать сортировку позиций */
const normalizeItemsSort = () => {
    form.items.forEach((item, index) => {
        item.sort = index
    })
}

/** Синхронизация выбранной компании */
watch(selectedCompany, (value) => {
    form.market_company_id = value?.id ?? null

    if (
        selectedShop.value
        && Number(selectedShop.value.market_company_id)
        !== Number(value?.id)
    ) {
        selectedShop.value = null
    }
})

/** Синхронизация выбранного магазина */
watch(selectedShop, (value) => {
    form.market_shop_id = value?.id ?? null

    if (
        value?.market_company_id
        && Number(selectedCompany.value?.id)
        !== Number(value.market_company_id)
    ) {
        selectedCompany.value =
            companyOptions.value.find((company) => {
                return Number(company.id)
                    === Number(value.market_company_id)
            }) || null
    }
})

/** Синхронизация выбранной валюты */
watch(selectedCurrency, (value) => {
    form.currency_id = value?.id ?? null
})

/** Автоматический выбор валюты */
watch(
    currencyOptions,
    (values) => {
        if (!selectedCurrency.value && values.length) {
            selectedCurrency.value =
                values.find((item) => item.is_default)
                || values[0]
        }
    },
    {
        immediate: true,
    }
)

/** При ручной цене оставляем поле доступным */
watch(
    () => form.calculate_price,
    (value) => {
        if (value && form.price === null) {
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

/** Обновить новые изображения */
const handleNewImagesUpdate = (images) => {
    const normalizedImages = (images || []).map(
        (image, index) => ({
            ...image,
            order: index,
        })
    )

    newImages.value = normalizedImages
    form.images = normalizedImages
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

/** Автоматически заполнить SEO текущего перевода */
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
            .filter((word) => word && word.length >= 3)
            .map((word) => word.toLowerCase())
            .filter((value, index, self) => {
                return self.indexOf(value) === index
            })

        translation.meta_keywords = truncateText(
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

        translation.meta_desc = truncateText(
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
        id: item.id || null,

        market_product_id:
            item.market_product_id
                ? Number(item.market_product_id)
                : null,

        market_product_variant_id:
            item.market_product_variant_id
                ? Number(item.market_product_variant_id)
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

/** Отправить форму создания комплекта */
const submitForm = () => {
    normalizeItemsSort()

    form.transform((data) => {
        const transformed = {
            ...data,

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

            calculate_price:
                data.calculate_price ? 1 : 0,

            price:
                nullableNumber(data.price, 2) ?? 0,

            old_price:
                nullableNumber(data.old_price, 2),

            purchase_price:
                nullableNumber(data.purchase_price, 2),

            wholesale_price:
                nullableNumber(data.wholesale_price, 2),

            wholesale_min_quantity:
                data.wholesale_min_quantity === ''
                || data.wholesale_min_quantity === null
                    ? null
                    : Number(
                        data.wholesale_min_quantity
                    ),

            sort: Number(data.sort || 0),

            activity: data.activity ? 1 : 0,

            left: data.left ? 1 : 0,
            main: data.main ? 1 : 0,
            right: data.right ? 1 : 0,

            is_new: data.is_new ? 1 : 0,
            is_hit: data.is_hit ? 1 : 0,
            is_sale: data.is_sale ? 1 : 0,

            views: Number(data.views || 0),
            likes_count:
                Number(data.likes_count || 0),

            rating_avg:
                nullableNumber(
                    data.rating_avg,
                    2
                ) ?? 0,

            rating_count:
                Number(data.rating_count || 0),

            items: normalizedItems(),
        }

        delete transformed.images

        newImages.value.forEach(
            (image, index) => {
                if (!image.file) {
                    return
                }

                transformed[
                    `images[${index}][file]`
                    ] = image.file

                transformed[
                    `images[${index}][order]`
                    ] = image.order ?? index

                transformed[
                    `images[${index}][alt]`
                    ] = image.alt ?? ''

                transformed[
                    `images[${index}][caption]`
                    ] = image.caption ?? ''
            }
        )

        return transformed
    })

    form.post(
        route('admin.marketProductBundles.store'),
        {
            forceFormData: true,
            errorBag: 'createMarketProductBundle',
            preserveScroll: true,

            onSuccess: () => {
                toast.success(
                    'Комплект товаров успешно создан!'
                )

                newImages.value = []
                form.images = []
            },

            onError: (errors) => {
                console.error(
                    'Ошибка создания комплекта:',
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
    <AdminLayout :title="t('addMarketProductBundle')">
        <template #header>
            <TitlePage>
                {{ t('addMarketProductBundle') }}
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
                        <div class="mb-3 flex justify-end">
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
                            {{ t('price' )}}
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
                                       px-2 py-0.5 rounded-sm bg-blue-600
                                       hover:bg-blue-700 text-white text-sm"
                                @click="addItem"
                            >
                                <svg class="w-4 h-4 fill-current opacity-50 shrink-0"
                                     viewBox="0 0 16 16">
                                    <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"></path>
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
                                            <path d="M5 7h2v6H5V7zm4 0h2v6H9V7zm3-6v2h4v2h-1v10c0 .6-.4 1-1 1H2c-.6 0-1-.4-1-1V5H0V3h4V1c0-.6.4-1 1-1h6c.6 0 1 .4 1 1zM6 2v1h4V2H6zm7 3H3v9h10V5z"></path>
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
