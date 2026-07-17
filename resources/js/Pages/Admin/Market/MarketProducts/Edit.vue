<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Редактирование товара маркетплейса.
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
import MarketProductAttributesField
    from '@/Components/Admin/Market/MarketProduct/Attribute/MarketProductAttributesField.vue'
import MarketProductRelatedProductsField
    from '@/Components/Admin/Market/MarketProduct/Related/MarketProductRelatedProductsField.vue'
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
import ClearMetaButton from '@/Components/Admin/UI/Buttons/ClearMetaButton.vue'

/** Локализация интерфейса */
const { t } = useI18n()

/** Уведомления */
const toast = useToast()

/** Входные данные страницы */
const props = defineProps({
    product: { type: Object, required: true },
    imageProcessorEnabled: { type: Boolean, default: true },
    imagePreset: { type: Object, default: null },
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },
    companies: { type: Array, default: () => [] },
    shops: { type: Array, default: () => [] },
    brands: { type: Array, default: () => [] },
    currencies: { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] },
    tags: { type: Array, default: () => [] },
    attributes: { type: Array, default: () => [] },
    attributeValues: { type: Array, default: () => [] },
    relatedProducts: { type: Array, default: () => [] },
    related_products: { type: Array, default: () => [] },
    errors: { type: Object, default: () => ({}) },
})

/** Получить массив из обычного массива или Laravel Resource Collection */
const resourceList = (value) => {
    if (Array.isArray(value)) return value
    if (Array.isArray(value?.data)) return value.data
    return []
}

/** Редактируемый товар */
const product = computed(() => props.product?.data || props.product || {})

/** Создать пустой перевод товара */
const makeTranslation = () => ({
    title: '',
    subtitle: '',
    short: '',
    description: '',
    meta_title: '',
    meta_keywords: '',
    meta_desc: '',
})

/** Преобразовать переводы товара в объект по локалям */
const normalizeTranslations = (translations) => {
    const result = {}

    resourceList(translations).forEach((translation) => {
        if (!translation?.locale) return

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

/** Подготовить nullable-значение для поля ввода */
const normalizeNullableInput = (value) => {
    return value === null || value === undefined ? '' : value
}

/** Преобразовать дату в формат datetime-local */
const toDateTimeLocal = (value) => {
    if (!value) return ''

    const date = new Date(value)

    if (Number.isNaN(date.getTime())) {
        return String(value).slice(0, 16)
    }

    const pad = (number) => String(number).padStart(2, '0')

    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`
}

/** Начальные переводы и активная локаль */
const initialTranslations = normalizeTranslations(product.value.translations)
const defaultLocale = initialTranslations[props.currentLocale]
    ? props.currentLocale
    : Object.keys(initialTranslations)[0] || 'ru'

/** Активная локаль и новые изображения */
const activeLocale = ref(defaultLocale)
const newImages = ref([])

/** Существующие изображения с актуальным порядком */
const existingImages = ref(
    resourceList(product.value.images)
        .filter(image => image.url || image.webp_url || image.image_url || image.thumb_url)
        .map((image, index) => ({
            id: Number(image.id),
            url: image.webp_url || image.image_url || image.thumb_url || image.url,
            order: Number(image.order ?? image.pivot?.order ?? index),
            alt: image.alt || '',
            caption: image.caption || '',
        }))
        .sort((a, b) => a.order - b.order)
)

/** Форма редактирования товара */
const form = useForm({
    user_id: product.value.user_id || product.value.owner?.id || null,

    market_company_id: product.value.market_company_id || product.value.company?.id || null,
    market_shop_id: product.value.market_shop_id || product.value.shop?.id || null,
    market_brand_id: product.value.market_brand_id || product.value.brand?.id || null,
    currency_id: product.value.currency_id || product.value.currency?.id || null,

    url: product.value.url || '',
    sku: product.value.sku || '',
    vendor_code: product.value.vendor_code || '',
    barcode: product.value.barcode || '',

    price: normalizeNullableInput(product.value.price),
    old_price: normalizeNullableInput(product.value.old_price),
    purchase_price: normalizeNullableInput(product.value.purchase_price),
    wholesale_price: normalizeNullableInput(product.value.wholesale_price),
    wholesale_min_quantity: normalizeNullableInput(product.value.wholesale_min_quantity),

    quantity: Number(product.value.quantity || 0),
    in_stock: Boolean(product.value.in_stock),

    weight: normalizeNullableInput(product.value.weight),
    length: normalizeNullableInput(product.value.length),
    width: normalizeNullableInput(product.value.width),
    height: normalizeNullableInput(product.value.height),

    sort: Number(product.value.sort || 0),
    activity: Boolean(product.value.activity),

    left: Boolean(product.value.left),
    main: Boolean(product.value.main),
    right: Boolean(product.value.right),

    is_new: Boolean(product.value.is_new),
    is_hit: Boolean(product.value.is_hit),
    is_sale: Boolean(product.value.is_sale),

    status: product.value.status || 'draft',

    published_at: toDateTimeLocal(product.value.published_at),
    show_from_at: toDateTimeLocal(product.value.show_from_at),
    show_to_at: toDateTimeLocal(product.value.show_to_at),

    views: Number(product.value.views || 0),
    likes_count: Number(product.value.likes_count || 0),
    rating_avg: Number(product.value.rating_avg || 0),
    rating_count: Number(product.value.rating_count || 0),

    categories: [],
    tags: [],
    related_products: resourceList(product.value.related_products || product.value.relatedProducts),
    attribute_values: resourceList(product.value.attribute_values || product.value.attributeValues),
    images: [],
    deletedImages: [],

    translations: initialTranslations,
})

/** Выбранные связанные сущности */
const selectedCompany = ref(null)
const selectedShop = ref(null)
const selectedBrand = ref(null)
const selectedCurrency = ref(null)
const selectedCategories = ref([])
const selectedMainCategory = ref(null)
const selectedTags = ref([])

/** Текущий редактируемый перевод */
const currentTranslation = computed(() => {
    if (!form.translations[activeLocale.value]) {
        form.translations[activeLocale.value] = makeTranslation()
    }

    return form.translations[activeLocale.value]
})

/** Ошибка поля текущего перевода */
const getError = (key) => form.errors[`translations.${activeLocale.value}.${key}`]

/** Нормализованные справочники */
const companyList = computed(() => resourceList(props.companies))
const shopList = computed(() => resourceList(props.shops))
const brandList = computed(() => resourceList(props.brands))
const currencyList = computed(() => resourceList(props.currencies))
const categoryList = computed(() => resourceList(props.categories))
const tagList = computed(() => resourceList(props.tags))

/** Получить отображаемое название сущности */
const translationTitle = (item) => {
    return item?.translation?.title || item?.title || item?.legal_name || item?.name || item?.url || `ID: ${item?.id}`
}

/** Опции компаний */
const companyOptions = computed(() => companyList.value.map(item => ({
    ...item,
    label: `[ID: ${item.id}] ${translationTitle(item)}`,
})))

/** Магазины выбранной компании */
const filteredShopList = computed(() => {
    if (!selectedCompany.value?.id) return shopList.value

    return shopList.value.filter(item => Number(item.market_company_id) === Number(selectedCompany.value.id))
})

/** Опции магазинов */
const shopOptions = computed(() => filteredShopList.value.map(item => ({
    ...item,
    label: `[ID: ${item.id}] ${translationTitle(item)}`,
})))

/** Опции брендов */
const brandOptions = computed(() => brandList.value.map(item => ({
    ...item,
    label: `[ID: ${item.id}] ${translationTitle(item)}`,
})))

/** Опции валют */
const currencyOptions = computed(() => currencyList.value.map(item => ({
    ...item,
    label: `[${item.code}] ${item.name}${item.symbol ? ` — ${item.symbol}` : ''}`,
})))

/** Опции категорий с учётом уровня вложенности */
const categoryOptions = computed(() => categoryList.value.map(item => {
    const prefix = item.level > 1 ? `${'— '.repeat(item.level - 1)}` : ''

    return {
        ...item,
        label: `[ID: ${item.id}] ${prefix}${translationTitle(item)}`,
    }
}))

/** Опции тегов */
const tagOptions = computed(() => tagList.value.map(item => ({
    ...item,
    label: `[ID: ${item.id}] ${translationTitle(item)}`,
})))

/** Динамический лимит элементов мультиселекта */
const dynamicOptionsLimit = (items) => {
    return Array.isArray(items) ? items.length + 10 : 10
}

/** Пресет обработки изображений товара */
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

/** Синхронизировать категории и основную категорию с формой */
const syncCategories = () => {
    form.categories = selectedCategories.value.map((item, index) => ({
        id: item.id,
        main: Number(item.id) === Number(selectedMainCategory.value?.id),
        order: index,
    }))
}

/** Инициализировать выбранные связи товара */
const initializeRelations = () => {
    selectedCompany.value = companyOptions.value.find(item =>
        Number(item.id) === Number(form.market_company_id)) || null
    selectedBrand.value = brandOptions.value.find(item =>
        Number(item.id) === Number(form.market_brand_id)) || null
    selectedCurrency.value = currencyOptions.value.find(item =>
        Number(item.id) === Number(form.currency_id)) || null

    selectedShop.value = shopList.value
        .map(item => ({ ...item, label: `[ID: ${item.id}] ${translationTitle(item)}` }))
        .find(item => Number(item.id) === Number(form.market_shop_id)) || null

    const productCategories = resourceList(product.value.categories)

    selectedCategories.value = productCategories
        .map(category => categoryOptions.value.find(option =>
            Number(option.id) === Number(category.id)))
        .filter(Boolean)

    const mainCategory = productCategories.find(category =>
            Boolean(category.pivot?.main ?? category.main))
        || resourceList(product.value.main_categories || product.value.mainCategories)[0]
        || productCategories[0]

    selectedMainCategory.value = mainCategory
        ? selectedCategories.value.find(category =>
        Number(category.id) === Number(mainCategory.id)) || null
        : null

    selectedTags.value = resourceList(product.value.tags)
        .map(tag => tagOptions.value.find(option =>
            Number(option.id) === Number(tag.id)))
        .filter(Boolean)

    syncCategories()
}

/** Выполнить первичную инициализацию связей */
initializeRelations()

/** Синхронизация выбранной компании */
watch(selectedCompany, (value) => {
    form.market_company_id = value?.id ?? null

    if (selectedShop.value && Number(selectedShop.value.market_company_id) !== Number(value?.id)) {
        selectedShop.value = null
    }
})

/** Синхронизация выбранного магазина */
watch(selectedShop, (value) => {
    form.market_shop_id = value?.id ?? null

    if (value?.market_company_id &&
        Number(selectedCompany.value?.id) !== Number(value.market_company_id)) {
        selectedCompany.value = companyOptions.value.find(item =>
            Number(item.id) === Number(value.market_company_id)) || null
    }
})

/** Синхронизация выбранного бренда */
watch(selectedBrand, (value) => {
    form.market_brand_id = value?.id ?? null
})

/** Синхронизация выбранной валюты */
watch(selectedCurrency, (value) => {
    form.currency_id = value?.id ?? null
})

/** Синхронизация категорий */
watch(selectedCategories, (values) => {
    if (!values.some(item => Number(item.id) === Number(selectedMainCategory.value?.id))) {
        selectedMainCategory.value = values[0] || null
    }

    syncCategories()
}, { deep: true })

/** Синхронизация основной категории */
watch(selectedMainCategory, syncCategories)

/** Синхронизация тегов */
watch(selectedTags, (values) => {
    form.tags = values.map((item, index) => ({
        id: item.id,
        order: Number(item.pivot?.order ?? index),
    }))
}, { deep: true, immediate: true })

/** Обновить порядок существующих изображений после перетаскивания */
const handleExistingImagesUpdate = (images) => {
    existingImages.value = (images || []).map((image, index) => ({
        ...image,
        order: index,
    }))
}

/** Пометить существующее изображение на удаление */
const handleDeleteExistingImage = (deletedId) => {
    if (!deletedId) return

    if (!form.deletedImages.includes(deletedId)) {
        form.deletedImages.push(deletedId)
    }

    existingImages.value = existingImages.value
        .filter(image => Number(image.id) !== Number(deletedId))
        .map((image, index) => ({
            ...image,
            order: index,
        }))
}

/** Обновить новые изображения и их порядок */
const handleNewImagesUpdate = (images) => {
    newImages.value = (images || []).map((image, index) => ({
        ...image,
        order: index,
    }))
}

/** Сформировать URL из названия товара */
const handleUrlInputFocus = () => {
    if (!form.url && currentTranslation.value.title) {
        form.url = transliterate(currentTranslation.value.title.toLowerCase())
    }
}

/** Обрезать текст до указанной длины */
const truncateText = (text, maxLength, addEllipsis = false) => {
    if (!text) return ''

    const value = String(text)

    if (value.length <= maxLength) return value

    const lastSpaceIndex = value.lastIndexOf(' ', maxLength)
    const truncated = lastSpaceIndex === -1 ? value.substring(0, maxLength) : value.substring(0, lastSpaceIndex)

    return addEllipsis ? `${truncated}...` : truncated
}

/** Очистить SEO-поля текущего перевода */
const clearMetaFields = () => {
    const translation = currentTranslation.value

    translation.meta_title = ''
    translation.meta_keywords = ''
    translation.meta_desc = ''
}

/** Сгенерировать SEO-поля текущего перевода */
const generateMetaFields = () => {
    const translation = currentTranslation.value

    if (translation.title && !translation.meta_title) {
        translation.meta_title = truncateText(translation.title, 255)
    }

    if (!translation.meta_keywords && translation.short) {
        let text = String(translation.short).replace(/(<([^>]+)>)/gi, '')
        text = text.replace(/[.,!?;:()[\]{}"'«»]/g, '')

        const words = text
            .split(/\s+/)
            .filter(word => word && word.length >= 3)
            .map(word => word.toLowerCase())
            .filter((value, index, self) => self.indexOf(value) === index)

        translation.meta_keywords = truncateText(words.join(', '), 255)
    }

    if (translation.short && !translation.meta_desc) {
        const description = String(translation.short).replace(/(<([^>]+)>)/gi, '')
        translation.meta_desc = truncateText(description, 255, true)
    }
}

/** Преобразовать необязательное значение в число */
const nullableNumber = (value, digits = 2) => {
    if (value === '' || value === null || value === undefined) return null

    const number = Number(value)

    return Number.isFinite(number) ? Number(number.toFixed(digits)) : null
}

/** Отправить форму обновления товара */
const submitForm = () => {
    syncCategories()

    form.transform((data) => {
        const transformed = {
            ...data,
            _method: 'put',

            price: nullableNumber(data.price, 2) ?? 0,
            old_price: nullableNumber(data.old_price, 2),
            purchase_price: nullableNumber(data.purchase_price, 2),
            wholesale_price: nullableNumber(data.wholesale_price, 2),
            wholesale_min_quantity: data.wholesale_min_quantity === '' ||
            data.wholesale_min_quantity === null ? null : Number(data.wholesale_min_quantity),

            quantity: Number(data.quantity || 0),
            weight: nullableNumber(data.weight, 3),
            length: nullableNumber(data.length, 2),
            width: nullableNumber(data.width, 2),
            height: nullableNumber(data.height, 2),

            sort: Number(data.sort || 0),
            views: Number(data.views || 0),
            likes_count: Number(data.likes_count || 0),
            rating_avg: nullableNumber(data.rating_avg, 2) ?? 0,
            rating_count: Number(data.rating_count || 0),

            activity: data.activity ? 1 : 0,
            in_stock: data.in_stock ? 1 : 0,
            left: data.left ? 1 : 0,
            main: data.main ? 1 : 0,
            right: data.right ? 1 : 0,
            is_new: data.is_new ? 1 : 0,
            is_hit: data.is_hit ? 1 : 0,
            is_sale: data.is_sale ? 1 : 0,

            deletedImages: [...new Set(form.deletedImages)],

            categories: data.categories,
            tags: data.tags,
            related_products: data.related_products,
            attribute_values: data.attribute_values.map((item, index) => ({
                ...item,
                id: item.id || null,
                value_string: item.value_string || null,
                value_number: nullableNumber(item.value_number, 4),
                value_date: item.value_date || null,
                unit: item.unit || null,
                order: index,
                activity: item.activity ? 1 : 0,
            })),
        }

        delete transformed.images
        delete transformed.deletedImages

        let imageIndex = 0

        existingImages.value.forEach((image, index) => {
            transformed[`images[${imageIndex}][id]`] = image.id
            transformed[`images[${imageIndex}][order]`] = Number(image.order ?? index)
            transformed[`images[${imageIndex}][alt]`] = image.alt || ''
            transformed[`images[${imageIndex}][caption]`] = image.caption || ''
            imageIndex++
        })

        newImages.value.forEach((image, index) => {
            if (!image.file) return

            transformed[`images[${imageIndex}][file]`] = image.file
            transformed[`images[${imageIndex}][order]`] = Number(image.order ?? existingImages.value.length + index)
            transformed[`images[${imageIndex}][alt]`] = image.alt || ''
            transformed[`images[${imageIndex}][caption]`] = image.caption || ''
            imageIndex++
        })

        form.deletedImages.forEach((id, index) => {
            transformed[`deletedImages[${index}]`] = id
        })

        return transformed
    })

    form.post(route('admin.marketProducts.update', { marketProduct: product.value.id }), {
        forceFormData: true,
        errorBag: 'updateMarketProduct',
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Товар успешно обновлён!')
            newImages.value = []
            form.deletedImages = []
        },
        onError: (errors) => {
            console.error('Ошибка обновления товара:', errors)

            const firstKey = Object.keys(errors || {})[0]
            toast.error(errors?.[firstKey] || 'Проверьте правильность заполнения полей.')
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('editMarketProduct')">
        <template #header>
            <TitlePage>{{ t('editMarketProduct') }}</TitlePage>
        </template>

        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       shadow-lg shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95">
                <div class="sm:flex sm:justify-between sm:items-center mb-2">

                    <DefaultButton :href="route('admin.marketProducts.index')">
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

                <form class="p-3 w-full" enctype="multipart/form-data"
                      @submit.prevent="submitForm">

                    <!-- Активность, наличие и сортировка -->
                    <div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4">
                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox id="activity" v-model="form.activity" />
                            <LabelCheckbox
                                for="activity"
                                :text="t('activity')"
                                class="text-sm h-8 flex items-center" />
                        </div>
                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox id="in_stock" v-model="form.in_stock" />
                            <LabelCheckbox
                                for="in_stock"
                                :text="t('inStock')"
                                class="text-sm h-8 flex items-center" />
                        </div>
                        <div class="flex flex-row items-center gap-2">
                            <LabelInput for="sort" :value="t('sort')" class="text-sm" />
                            <InputNumber
                                id="sort"
                                v-model.number="form.sort"
                                type="number" min="0"
                                class="w-full lg:w-28" />
                            <InputError class="mt-2 lg:mt-0" :message="form.errors.sort" />
                        </div>
                    </div>

                    <!-- left, main, right -->
                    <div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4">
                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox id="left" v-model="form.left" />
                            <LabelCheckbox
                                for="left"
                                :text="t('left')"
                                class="text-sm h-8 flex items-center" />
                        </div>
                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox id="main" v-model="form.main" />
                            <LabelCheckbox
                                for="main"
                                :text="t('main')"
                                class="text-sm h-8 flex items-center" />
                        </div>
                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox id="right" v-model="form.right" />
                            <LabelCheckbox
                                for="right"
                                :text="t('right')"
                                class="text-sm h-8 flex items-center" />
                        </div>
                    </div>

                    <!-- is_new, is_hit, is_sale -->
                    <div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4">
                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox id="is_new" v-model="form.is_new" />
                            <LabelCheckbox for="is_new" :text="t('sortIsNew')"
                                           class="text-sm h-8 flex items-center" />
                        </div>
                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox id="is_hit" v-model="form.is_hit" />
                            <LabelCheckbox for="is_hit" :text="t('sortIsHit')"
                                           class="text-sm h-8 flex items-center" />
                        </div>
                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox id="is_sale" v-model="form.is_sale" />
                            <LabelCheckbox for="is_sale" :text="t('sortIsSale')"
                                           class="text-sm h-8 flex items-center" />
                        </div>
                    </div>

                    <!-- Переводы товара -->
                    <div class="my-5 p-3 border border-slate-300 dark:border-slate-500
                                bg-white dark:bg-slate-800 rounded-sm">
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

                            <InputError class="mt-2" :message="getError('title')" />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput
                                :for="`subtitle-${activeLocale}`"
                                :value="`${t('subtitle')} [${activeLocale.toUpperCase()}]`" />
                            <InputText
                                :id="`subtitle-${activeLocale}`"
                                v-model="currentTranslation.subtitle"
                                type="text"
                                maxlength="255" />
                            <InputError class="mt-2" :message="getError('subtitle')" />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <div class="flex justify-between w-full">
                                <LabelInput
                                    :for="`short-${activeLocale}`"
                            :value="`${t('shortDescription')} [${activeLocale.toUpperCase()}]`" />
                                <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                        {{ (currentTranslation.short || '').length }} / 255 {{ t('characters') }}
                                </div>
                            </div>

                            <MetaDescTextarea
                                :id="`short-${activeLocale}`"
                                v-model="currentTranslation.short"
                                class="w-full" />
                            <InputError class="mt-2" :message="getError('short')" />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput
                                :for="`description-${activeLocale}`"
                                :value="`${t('description')} [${activeLocale.toUpperCase()}]`" />
                            <TinyEditor v-model="currentTranslation.description" :height="400" />
                            <InputError class="mt-2" :message="getError('description')" />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput
                                :for="`meta-title-${activeLocale}`"
                                :value="`${t('metaTitle')} [${activeLocale.toUpperCase()}]`" />
                            <InputText
                                :id="`meta-title-${activeLocale}`"
                                v-model="currentTranslation.meta_title"
                                type="text"
                                maxlength="255" />
                            <InputError class="mt-2" :message="getError('meta_title')" />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput
                                :for="`meta-keywords-${activeLocale}`"
                                :value="`${t('metaKeywords')} [${activeLocale.toUpperCase()}]`" />
                            <InputText
                                :id="`meta-keywords-${activeLocale}`"
                                v-model="currentTranslation.meta_keywords"
                                type="text"
                                maxlength="255" />
                            <InputError
                                class="mt-2"
                                :message="getError('meta_keywords')" />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput
                                :for="`meta-desc-${activeLocale}`"
                            :value="`${t('metaDescription')} [${activeLocale.toUpperCase()}]`" />
                            <MetaDescTextarea
                                :id="`meta-desc-${activeLocale}`"
                                v-model="currentTranslation.meta_desc"
                                class="w-full" />
                            <InputError class="mt-2" :message="getError('meta_desc')" />
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

                    <!-- URL товара -->
                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput for="url">
                            <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                            URL
                        </LabelInput>

                        <InputText
                            id="url"
                            v-model="form.url"
                            type="text"
                            required
                            maxlength="500"
                            autocomplete="url"
                            @focus="handleUrlInputFocus"
                        />

                        <InputError class="mt-2" :message="form.errors.url" />
                    </div>

                    <!-- Артикул, Код поставщика, Штрихкод -->
                    <div class="mb-3 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <LabelInput for="vendor_code" :value="t('vendorCode')" />
                            <InputText id="vendor_code" v-model="form.vendor_code" type="text" />
                            <InputError :message="form.errors.vendor_code" />
                        </div>
                        <div>
                            <LabelInput for="sku" :value="t('sku')" />
                            <InputText id="sku" v-model="form.sku" type="text" />
                            <InputError :message="form.errors.sku" />
                        </div>
                        <div>
                            <LabelInput for="barcode" :value="t('barcode')" />
                            <InputText id="barcode" v-model="form.barcode" type="text" />
                            <InputError :message="form.errors.barcode" />
                        </div>
                    </div>

                    <!-- Фирма -->
                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput for="market_company_id" :value="t('marketCompany')" />
                        <VueMultiselect
                            v-model="selectedCompany"
                            :options="companyOptions"
                            label="label"
                            track-by="id"
                            :searchable="true"
                            :allow-empty="true"
                            :show-labels="false"
                            placeholder="Выберите компанию" />
                        <InputError :message="form.errors.market_company_id" />
                    </div>

                    <!-- Магазин -->
                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput for="market_shop_id" :value="t('marketShop')" />
                        <VueMultiselect
                            v-model="selectedShop"
                            :options="shopOptions"
                            label="label"
                            track-by="id"
                            :searchable="true"
                            :allow-empty="true"
                            :show-labels="false"
                            placeholder="Выберите магазин" />
                        <InputError :message="form.errors.market_shop_id" />
                    </div>

                    <!-- Бренд -->
                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput for="market_brand_id" :value="t('brand')" />
                        <VueMultiselect
                            v-model="selectedBrand"
                            :options="brandOptions"
                            label="label"
                            track-by="id"
                            :searchable="true"
                            :allow-empty="true"
                            :show-labels="false"
                            placeholder="Выберите бренд" />
                        <InputError :message="form.errors.market_brand_id" />
                    </div>

                    <!-- Категории -->
                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput for="categories" :value="t('categories')" />
                        <VueMultiselect
                            v-model="selectedCategories"
                            :options="categoryOptions"
                            label="label"
                            track-by="id"
                            :multiple="true"
                            :close-on-select="false"
                            :searchable="true"
                            :show-labels="false"
                            :options-limit="dynamicOptionsLimit(categoryOptions)"
                            placeholder="Выберите категории" />
                        <InputError :message="form.errors.categories" />
                    </div>

                    <!-- Основная категория -->
                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput for="category" :value="t('mainCategory')" />
                        <VueMultiselect
                            v-model="selectedMainCategory"
                            :options="selectedCategories"
                            label="label"
                            track-by="id"
                            :searchable="true"
                            :allow-empty="true"
                            :show-labels="false"
                            placeholder="Выберите основную категорию" />
                    </div>

                    <!-- Теги -->
                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput for="tags" :value="t('tags')" />
                        <VueMultiselect
                            v-model="selectedTags"
                            :options="tagOptions"
                            label="label"
                            track-by="id"
                            :multiple="true"
                            :close-on-select="false"
                            :searchable="true"
                            :show-labels="false"
                            :options-limit="dynamicOptionsLimit(tagOptions)"
                            placeholder="Выберите теги" />
                        <InputError :message="form.errors.tags" />
                    </div>

                    <!-- Валюта -->
                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput for="currency_id" :value="t('currency')" />
                        <VueMultiselect
                            v-model="selectedCurrency"
                            :options="currencyOptions"
                            label="label"
                            track-by="id"
                            :searchable="true"
                            :allow-empty="true"
                            :show-labels="false"
                            placeholder="Выберите валюту" />
                        <InputError :message="form.errors.currency_id" />
                    </div>

                    <!-- Старая цена, Стоимость, Цена покупки -->
                    <div class="mb-3 flex flex-col gap-4 lg:flex-row lg:justify-between">
                        <div>
                            <LabelInput for="old_price" :value="t('compareAtPrice')" />
                            <MarketProductPriceInput
                                id="old_price"
                                v-model="form.old_price"
                                :currency="selectedCurrency"
                                :fraction-digits="2"
                                class="w-full lg:w-36" />
                            <InputError :message="form.errors.old_price" />
                        </div>
                        <div>
                            <LabelInput for="price" :value="t('price')" />
                            <MarketProductPriceInput
                                id="price"
                                v-model="form.price"
                                :currency="selectedCurrency"
                                :fraction-digits="2"
                                class="w-full lg:w-36" />
                            <InputError :message="form.errors.price" />
                        </div>
                        <div>
                            <LabelInput for="purchase_price" :value="t('purchasePrice')" />
                            <MarketProductPriceInput
                                id="purchase_price"
                                v-model="form.purchase_price"
                                :currency="selectedCurrency"
                                :fraction-digits="2"
                                class="w-full lg:w-36" />
                            <InputError :message="form.errors.purchase_price" />
                        </div>
                    </div>

                    <!-- Оптовая цена, минимальное количество для оптовой цены -->
                    <div class="mb-3 flex flex-col gap-4 lg:flex-row lg:justify-center">
                        <div>
                            <LabelInput for="wholesale_price" :value="t('wholesalePrice')" />
                            <MarketProductPriceInput
                                id="wholesale_price"
                                v-model="form.wholesale_price"
                                :currency="selectedCurrency"
                                :fraction-digits="2"
                                class="w-full lg:w-36" />
                            <InputError :message="form.errors.wholesale_price" />
                        </div>
                        <div>
                            <LabelInput for="wholesale_min_quantity"
                                        :value="t('wholesaleMinQuantity')" />

                            <InputNumber
                                id="wholesale_min_quantity"
                                v-model.number="form.wholesale_min_quantity"
                                type="number"
                                min="0"
                                class="w-full lg:w-36"
                            />

                            <InputError :message="form.errors.wholesale_min_quantity" />
                        </div>
                    </div>

                    <!-- Количество, вес -->
                    <div class="mb-3 flex flex-col gap-4 lg:flex-row lg:justify-center">
                        <div>
                            <LabelInput for="quantity" :value="t('quantity')" />

                            <InputNumber
                                id="quantity"
                                v-model.number="form.quantity"
                                type="number"
                                min="0"
                                class="w-full lg:w-36"
                            />

                            <InputError :message="form.errors.quantity" />
                        </div>
                        <div>
                            <LabelInput for="weight" :value="t('weight')" />
                            <MarketProductMeasureInput
                                id="weight"
                                v-model="form.weight"
                                :fraction-digits="3"
                                unit="кг"
                                class="w-full lg:w-36" />
                            <InputError :message="form.errors.weight" />
                        </div>
                    </div>

                    <!-- Габариты товара -->
                    <div class="mb-3 flex flex-col gap-4 lg:flex-row lg:justify-between">
                        <div>
                            <LabelInput for="length" :value="t('length')" />
                            <MarketProductMeasureInput
                                id="length"
                                v-model="form.length"
                                :fraction-digits="2"
                                unit="см" />
                            <InputError :message="form.errors.length" />
                        </div>
                        <div>
                            <LabelInput for="width" :value="t('width')" />
                            <MarketProductMeasureInput
                                id="width"
                                v-model="form.width"
                                :fraction-digits="2"
                                unit="см" />
                            <InputError :message="form.errors.width" />
                        </div>
                        <div>
                            <LabelInput for="height" :value="t('height')" />
                            <MarketProductMeasureInput
                                id="height"
                                v-model="form.height"
                                :fraction-digits="2"
                                unit="см" />
                            <InputError :message="form.errors.height" />
                        </div>
                    </div>

                    <!-- Характеристики -->
                    <MarketProductAttributesField
                        v-model="form.attribute_values"
                        :attributes="attributes"
                        :attribute-values="attributeValues"
                        :errors="form.errors"
                    />

                    <!-- Рекомендуемые товары -->
                    <MarketProductRelatedProductsField
                        v-model="form.related_products"
                        :products="relatedProducts.length ? relatedProducts : related_products"
                        :errors="form.errors"
                        :exclude-product-id="product.id"
                    />

                    <!-- Публикация -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-4">
                        <div class="flex flex-col items-start">
                            <LabelInput for="status" :value="t('status')" />

                            <select
                                id="status"
                                v-model="form.status"
                                class="w-full px-2 py-0.5 form-select rounded-sm shadow-sm
                                       bg-white dark:bg-cyan-800 dark:text-slate-100 text-gray-600
                                       border border-slate-400 dark:border-slate-600"
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
                                type="datetime-local" />
                            <InputError class="mt-2" :message="form.errors.published_at" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                        <div class="flex flex-col items-start">
                            <LabelInput for="show_from_at" :value="t('showFromAt')" />
                            <InputText
                                id="show_from_at"
                                v-model="form.show_from_at"
                                type="datetime-local" />
                            <InputError class="mt-2" :message="form.errors.show_from_at" />
                        </div>
                        <div class="flex flex-col items-start">
                            <LabelInput for="show_to_at" :value="t('showToAt')" />
                            <InputText
                                id="show_to_at"
                                v-model="form.show_to_at"
                                type="datetime-local" />
                            <InputError class="mt-2" :message="form.errors.show_to_at" />
                        </div>
                    </div>

                    <!-- Изображения товара -->
                    <div class="mt-4">
                        <template v-if="imageProcessorEnabled">
                            <MultiImagePresetEdit
                                :images="existingImages"
                                :preset="galleryPreset"
                                @update:images="handleExistingImagesUpdate"
                                @delete:image="handleDeleteExistingImage"
                            />

                            <MultiImagePresetUpload
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
                                @update:images="handleNewImagesUpdate"
                            />
                        </template>

                        <div v-if="newImages.length"
                             class="mt-2 text-xs text-slate-600 dark:text-slate-300">
                            {{ t('images') }}: {{ newImages.length }}
                        </div>

                        <InputError class="mt-2" :message="form.errors.images" />
                    </div>

                    <!-- Кнопки формы -->
                    <div class="flex items-center justify-center mt-4">
                        <DefaultButton :href="route('admin.marketProducts.index')">
                            <template #icon>
                                <svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                                     viewBox="0 0 16 16">
                                    <path d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z" />
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
