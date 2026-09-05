<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Таблица товаров маркетплейса.
 */

import { computed, defineEmits, defineProps, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import LeftToggle from '@/Components/Admin/UI/Buttons/LeftToggle.vue'
import MainToggle from '@/Components/Admin/UI/Buttons/MainToggle.vue'
import RightToggle from '@/Components/Admin/UI/Buttons/RightToggle.vue'
import ActivityToggle from '@/Components/Admin/UI/Buttons/ActivityToggle.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'
import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import ModerationButton from '@/Components/Admin/UI/Buttons/ModerationButton.vue'
import NewButtonToggle from '@/Components/Admin/UI/Buttons/NewButtonToggle.vue'
import SaleButtonToggle from '@/Components/Admin/UI/Buttons/SaleButtonToggle.vue'
import HitButtonToggle from '@/Components/Admin/UI/Buttons/HitButtonToggle.vue'

const { t, locale } = useI18n()

/* ======================== Props ======================== */

const props = defineProps({
    products: {
        type: Array,
        default: () => [],
    },

    selectedProducts: {
        type: Array,
        default: () => [],
    },

    allSelected: {
        type: Boolean,
        default: false,
    },

    isAdmin: {
        type: Boolean,
        default: false,
    },
})

/* ======================== Emits ======================== */

const emit = defineEmits([
    'toggle-left',
    'toggle-main',
    'toggle-right',
    'toggle-is-new',
    'toggle-is-hit',
    'toggle-is-sale',
    'toggle-activity',
    'delete',
    'update-sort-order',
    'toggle-select',
    'toggle-all',
    'approve',
])

/* ======================== Local products ======================== */

/** Локальная копия нужна для vuedraggable. */
const localProducts = ref([])

watch(
    () => props.products,
    (newValue) => {
        localProducts.value = JSON.parse(JSON.stringify(newValue || []))
    },
    { immediate: true, deep: true }
)

/** Передача нового порядка товаров родительскому компоненту. */
const handleDragEnd = () => {
    emit(
        'update-sort-order',
        localProducts.value.map((product) => product.id)
    )
}

/* ======================== Selection ======================== */

/** Все товары текущей таблицы выбраны. */
const allProductsSelected = computed(() => {
    if (!localProducts.value.length) {
        return false
    }

    return localProducts.value.every((product) => {
        return props.selectedProducts.includes(product.id)
    })
})

/** Выбор всех отображаемых товаров. */
const toggleAll = (event) => {
    emit('toggle-all', {
        ids: localProducts.value.map((product) => product.id),
        checked: Boolean(event?.target?.checked),
    })
}

/* ======================== Translations ======================== */

/** Текущий перевод товара. */
const productTranslation = (product) => {
    return product?.translation || {}
}

/** Название товара. */
const productTitle = (product) => {
    return productTranslation(product)?.title || `ID: ${product?.id}`
}

/** Подзаголовок товара. */
const productSubtitle = (product) => {
    return productTranslation(product)?.subtitle || ''
}

/** Краткое описание товара. */
const productShort = (product) => {
    return productTranslation(product)?.short || ''
}

/** Название переведённой связанной сущности. */
const relationTitle = (relation) => {
    return relation?.translation?.title || ''
}

/** Название компании. */
const companyTitle = (product) => {
    return relationTitle(product?.company)
        || product?.company?.legal_name
        || ''
}

/** Название магазина. */
const shopTitle = (product) => {
    return relationTitle(product?.shop)
}

/** Название бренда. */
const brandTitle = (product) => {
    return relationTitle(product?.brand)
}

/** Название категории. */
const categoryTitle = (category) => {
    return category?.translation?.title || `ID: ${category?.id}`
}

/* ======================== Images ======================== */

/** Первое изображение товара с учётом order. */
const getPrimaryImage = (product) => {
    if (!Array.isArray(product?.images) || !product.images.length) {
        return null
    }

    return [...product.images].sort((left, right) => {
        return Number(left?.order ?? 0) - Number(right?.order ?? 0)
    })[0]
}

/** URL изображения товара. */
const imageUrl = (product) => {
    const image = getPrimaryImage(product)

    return image?.webp_url
        || image?.thumb_url
        || image?.image_url
        || image?.url
        || '/storage/market/market_product_images/default-image.png'
}

/** Alt изображения. */
const imageAlt = (product) => {
    const image = getPrimaryImage(product)

    return image?.alt || productTitle(product)
}

/** Title изображения. */
const imageTitle = (product) => {
    const image = getPrimaryImage(product)

    return image?.caption || productTitle(product)
}

/* ======================== Owner ======================== */

/** Подсказка владельца товара. */
const ownerTitle = (product) => {
    const owner = product?.owner

    if (!owner) {
        return t('noData')
    }

    return `${owner.name || ''}${owner.email ? ` — ${owner.email}` : ''}`.trim()
}

/** Аватар владельца. */
const ownerAvatar = (product) => {
    return product?.owner?.profile_photo_url
        || '/storage/profile-photos/default-image.png'
}

/* ======================== Status ======================== */

const statusLabelKeyMap = {
    draft: 'statusDraft',
    published: 'statusPublished',
    archived: 'statusArchived',
}

/** Название статуса публикации. */
const getStatusLabel = (status) => {
    return t(statusLabelKeyMap[status] || status || 'no')
}

/** Оформление статуса публикации. */
const statusBadge = (status) => {
    if (status === 'published') {
        return {
            text: getStatusLabel(status),
            class:
                'bg-emerald-100 text-emerald-700 border-emerald-300 ' +
                'dark:bg-emerald-900/40 dark:text-emerald-300 dark:border-emerald-700',
        }
    }

    if (status === 'archived') {
        return {
            text: getStatusLabel(status),
            class:
                'bg-slate-200 text-slate-700 border-slate-400 ' +
                'dark:bg-slate-800 dark:text-slate-300 dark:border-slate-600',
        }
    }

    return {
        text: getStatusLabel(status),
        class:
            'bg-amber-100 text-amber-800 border-amber-300 ' +
            'dark:bg-amber-900/40 dark:text-amber-300 dark:border-amber-700',
    }
}

/** Оформление статуса модерации. */
const moderationBadge = (status) => {
    const value = Number(status ?? 0)

    if (value === 1) {
        return {
            text: t('statusSelectApproved'),
            class:
                'bg-emerald-100 text-emerald-700 border-emerald-300 ' +
                'dark:bg-emerald-900/40 dark:text-emerald-300 dark:border-emerald-700',
        }
    }

    if (value === 2) {
        return {
            text: t('statusSelectRejected'),
            class:
                'bg-rose-100 text-rose-700 border-rose-300 ' +
                'dark:bg-rose-900/40 dark:text-rose-300 dark:border-rose-700',
        }
    }

    return {
        text: t('underModeration'),
        class:
            'bg-amber-100 text-amber-800 border-amber-300 ' +
            'dark:bg-amber-900/40 dark:text-amber-300 dark:border-amber-700',
    }
}

/* ======================== Formatting ======================== */

/** Безопасное преобразование в число. */
const safeNumber = (value) => {
    const number = Number(value)

    return Number.isFinite(number) ? number : 0
}

/** Форматирование даты. */
const formatDate = (dateString) => {
    if (!dateString) {
        return ''
    }

    const date = new Date(dateString)

    if (Number.isNaN(date.getTime())) {
        return ''
    }

    return date.toLocaleDateString(locale.value || 'ru-RU', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    })
}

/** Ограничение длины текста. */
const truncateText = (text, maxLength = 50) => {
    if (!text) {
        return ''
    }

    const value = String(text)

    return value.length > maxLength
        ? `${value.slice(0, maxLength).trimEnd()}…`
        : value
}

/** Форматирование денежного значения. */
const formatMoney = (value, currency) => {
    if (value === null || value === undefined || value === '') {
        return '—'
    }

    const amount = safeNumber(value)

    const precision = Number.isFinite(Number(currency?.precision))
        ? Number(currency.precision)
        : 2

    const thousandsSep = currency?.thousands_sep ?? ' '
    const decimalSep = currency?.decimal_sep ?? '.'

    const parts = amount.toFixed(precision).split('.')

    const integerPart = parts[0].replace(
        /\B(?=(\d{3})+(?!\d))/g,
        thousandsSep
    )

    const formattedAmount = precision > 0
        ? `${integerPart}${decimalSep}${parts[1]}`
        : integerPart

    const symbol = String(
        currency?.symbol || currency?.code || ''
    ).trim()

    if (!symbol) {
        return formattedAmount
    }

    return currency?.symbol_first
        ? `${symbol}${formattedAmount}`
        : `${formattedAmount} ${symbol}`
}

/** Форматирование рейтинга. */
const formatRating = (product) => {
    return safeNumber(product?.rating_avg).toFixed(1)
}

/* ======================== URLs ======================== */

/** Публичный URL товара. */
const productPublicUrl = (product) => {
    return `/market/products/${encodeURIComponent(product?.url || '')}`
}

/* ======================== Categories ======================== */

/** Подсказка с категориями. */
const categoriesTitle = (product) => {
    if (!Array.isArray(product?.categories) || !product.categories.length) {
        return ''
    }

    return product.categories
        .map((category) => categoryTitle(category))
        .filter(Boolean)
        .join(', ')
}

/** Основная категория. */
const mainCategory = (product) => {
    if (!Array.isArray(product?.categories)) {
        return null
    }

    return product.categories.find((category) => {
        return Boolean(category?.pivot?.main)
    }) || product.categories[0] || null
}

/** Название основной категории. */
const mainCategoryTitle = (product) => {
    const category = mainCategory(product)

    return category ? categoryTitle(category) : ''
}

/* ======================== Attributes ======================== */

/** Заголовок характеристики. */
const attributeTitle = (item) => {
    return item?.attribute?.translation?.title
        || `ID: ${item?.market_attribute_id}`
}

/** Текст справочного значения характеристики. */
const attributeReferenceValueTitle = (item) => {
    return item?.attribute_value?.translation?.title || ''
}

/**
 * Фактическое значение характеристики.
 *
 * Проверяем значения явно, чтобы 0 и false
 * не терялись из-за логического OR.
 */
const attributeValueText = (item) => {
    const referenceTitle = attributeReferenceValueTitle(item)

    if (referenceTitle) {
        return referenceTitle
    }

    if (item?.value_string !== null && item?.value_string !== undefined && item.value_string !== '') {
        return String(item.value_string)
    }

    if (item?.value_number !== null && item?.value_number !== undefined && item.value_number !== '') {
        return String(item.value_number)
    }

    if (item?.value_boolean === true) {
        return t('yes')
    }

    if (item?.value_boolean === false) {
        return t('no')
    }

    if (item?.value_date) {
        return String(item.value_date)
    }

    return ''
}

/** Подсказка с характеристиками товара. */
const attributesTitle = (product) => {
    if (
        !Array.isArray(product?.attribute_values)
        || !product.attribute_values.length
    ) {
        return ''
    }

    return product.attribute_values
        .map((item) => {
            const attribute = attributeTitle(item)
            const value = attributeValueText(item)
            const unit = value && item?.unit ? ` ${item.unit}` : ''

            return value
                ? `${attribute}: ${value}${unit}`
                : attribute
        })
        .filter(Boolean)
        .join('\n')
}

/* ======================== Tooltips ======================== */

/** Подсказка магазина: компания и бренд товара. */
const supplierTitle = (product) => {
    const values = []

    if (companyTitle(product)) {
        values.push(`Компания: ${companyTitle(product)}`)
    }

    if (brandTitle(product)) {
        values.push(`Бренд: ${brandTitle(product)}`)
    }

    return values.join('\n')
}

/** Подсказка публикации. */
const publicationTitle = (product) => {
    const values = [`Sort: ${product?.sort ?? 0}`]

    if (product?.published_at) {
        values.push(`Публикация: ${formatDate(product.published_at)}`)
    }

    if (product?.show_from_at) {
        values.push(`Показ с: ${formatDate(product.show_from_at)}`)
    }

    if (product?.show_to_at) {
        values.push(`Показ до: ${formatDate(product.show_to_at)}`)
    }

    return values.join('\n')
}
</script>

<template>
    <div class="relative rounded-sm border border-slate-200 bg-white shadow-lg dark:border-slate-600 dark:bg-slate-700">

        <!-- Панель выбора -->
        <div class="flex items-center justify-between border-b border-slate-400 px-3 py-2 dark:border-slate-500">
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('selected') }}: {{ selectedProducts.length }}
            </div>

            <label
                v-if="localProducts.length"
                class="flex cursor-pointer items-center text-xs text-slate-600 dark:text-slate-200"
            >
                <span>{{ t('selectAll') }}</span>

                <input
                    type="checkbox"
                    class="mx-2"
                    :checked="Boolean(allProductsSelected || allSelected)"
                    @change="toggleAll"
                />
            </label>
        </div>

        <div class="overflow-x-auto">
            <table
                v-if="localProducts.length"
                class="table-auto w-full text-slate-700 dark:text-slate-100"
            >
                <thead class="border border-solid border-gray-300 bg-slate-200 text-xs uppercase dark:border-gray-700 dark:bg-cyan-900">
                <tr>
                    <!-- Drag handle -->
                    <th class="w-px px-1 py-3">
                        <svg
                            class="h-4 w-4 fill-current text-slate-800 dark:text-slate-200"
                            viewBox="0 0 24 24"
                        >
                            <path d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z" />
                            <path d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z" />
                        </svg>
                    </th>

                    <th class="w-px whitespace-nowrap px-1 py-3">
                        <div class="text-center font-semibold">{{ t('id') }}</div>
                    </th>

                    <th class="w-px whitespace-nowrap px-1 py-3">
                        <div class="flex justify-center" :title="t('owner')">
                            <svg class="h-6 w-6 shrink-0 fill-current" viewBox="0 0 24 24">
                                <path d="M3,7H1V2A1,1,0,0,1,2,1H7V3H3Z" />
                                <path d="M23,7H21V3H17V1h5a1,1,0,0,1,1,1Z" />
                                <path d="M7,23H2a1,1,0,0,1-1-1V17H3v4H7Z" />
                                <path d="M22,23H17V21h4V17h2v5A1,1,0,0,1,22,23Z" />
                                <path d="M18.242,18.03l-2.727-.681a1,1,0,0,1-.744-.806l-.249-1.491A6.792,6.792,0,0,0,17,10V9A5,5,0,0,0,7,9v1a6.792,6.792,0,0,0,2.478,5.052l-.249,1.491a1,1,0,0,1-.743.806l-2.728.681A1,1,0,0,0,6,20H18a1,1,0,0,0,.242-1.97Z" />
                            </svg>
                        </div>
                    </th>

                    <th class="w-px px-1 py-3">
                        <div class="flex justify-center" :title="t('image')">
                            <svg class="h-6 w-6 shrink-0 fill-current" viewBox="0 0 512 512">
                                <path d="M0 96C0 60.7 28.7 32 64 32h384c35.3 0 64 28.7 64 64v320c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V96zm323.8 106.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4S78.8 416 88 416h336c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z" />
                            </svg>
                        </div>
                    </th>

                    <th class="min-w-64 px-2 py-3">
                        <div class="text-left font-semibold">{{ t('marketProduct') }}</div>
                    </th>

                    <th class="whitespace-nowrap px-2 py-3">
                        <div class="text-right font-semibold">{{ t('price') }}</div>
                    </th>

                    <th class="whitespace-nowrap px-2 py-3">
                        <div class="text-center font-semibold">{{ t('quantity') }}</div>
                    </th>

                    <th class="whitespace-nowrap px-2 py-3">
                        <div class="flex justify-center" :title="t('ratingAvg')">
                            <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24">
                                <path
                                    class="fill-current text-red-400 dark:text-red-300"
                                    d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"
                                />
                            </svg>
                        </div>
                    </th>

                    <th class="whitespace-nowrap px-2 py-3">
                        <div class="flex justify-center" :title="t('reviews')">
                            <svg class="h-4 w-4 shrink-0 fill-current" viewBox="0 0 512 512">
                                <path
                                    class="fill-current text-sky-700 dark:text-sky-300"
                                    d="M256 32C114.62 32 0 125.12 0 240c0 49.56 21.41 95.01 57.02 130.74C44.46 421.05 2.7 465.97 2.2 466.5A7.995 7.995 0 0 0 8 480c66.26 0 115.99-31.75 140.6-51.38C181.29 440.93 217.59 448 256 448c141.38 0 256-93.12 256-208S397.38 32 256 32z"
                                />
                            </svg>
                        </div>
                    </th>

                    <th class="whitespace-nowrap px-2 py-3">
                        <div class="flex justify-center" :title="t('views')">
                            <svg class="h-4 w-4 shrink-0 fill-current" viewBox="0 0 16 16">
                                <path
                                    class="fill-current text-blue-600 dark:text-blue-300"
                                    d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"
                                />
                            </svg>
                        </div>
                    </th>

                    <th class="whitespace-nowrap px-2 py-3">
                        <div class="flex justify-center" :title="t('likes')">
                            <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24">
                                <path class="fill-current text-red-400 dark:text-red-300" d="M3,9H1a1,1,0,0,0-1,1V22a1,1,0,0,0,1,1H4V10A1,1,0,0,0,3,9Z" />
                                <path class="fill-current text-red-400 dark:text-red-300" d="M21.882,8.133A2.986,2.986,0,0,0,21,8H15V5c0-3.824-2.589-4.942-3.958-5a1.017,1.017,0,0,0-.734.277A1,1,0,0,0,10,1V5.638l-4,4.8V23H18.23A2.985,2.985,0,0,0,21.1,20.882l2.769-9A3,3,0,0,0,21.882,8.133Z" />
                            </svg>
                        </div>
                    </th>

                    <th class="whitespace-nowrap px-2 py-3">
                        <div class="text-center font-medium">{{ t('show') }}</div>
                    </th>

                    <th class="whitespace-nowrap px-2 py-3">
                        <div class="text-right font-semibold">{{ t('actions') }}</div>
                    </th>

                    <th class="w-px whitespace-nowrap px-1 py-1 text-center">
                        <input
                            type="checkbox"
                            :checked="Boolean(allProductsSelected || allSelected)"
                            @change="toggleAll"
                        />
                    </th>
                </tr>
                </thead>

                <draggable
                    v-model="localProducts"
                    tag="tbody"
                    item-key="id"
                    handle=".handle"
                    @end="handleDragEnd"
                >
                    <template #item="{ element: product }">
                        <tr class="border-b-2 text-xs font-semibold hover:bg-slate-100 dark:hover:bg-cyan-800">

                            <!-- Drag -->
                            <td class="handle w-px cursor-move px-1 py-1 text-center">
                                <svg class="h-4 w-4 text-gray-500 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" />
                                </svg>
                            </td>

                            <!-- ID -->
                            <td class="w-px whitespace-nowrap px-1 py-1">
                                <div
                                    class="text-center text-blue-600 dark:text-blue-200"
                                    :title="publicationTitle(product)"
                                >
                                    {{ product.id }}
                                </div>
                            </td>

                            <!-- Owner -->
                            <td class="w-px px-1 py-1">
                                <div class="flex justify-center">
                                    <img
                                        :src="ownerAvatar(product)"
                                        :title="ownerTitle(product)"
                                        :alt="t('owner')"
                                        class="h-7 w-7 rounded-full border border-slate-300 object-cover dark:border-slate-600"
                                    />
                                </div>
                            </td>

                            <!-- Image -->
                            <td class="w-16 px-1 py-1">
                                <div class="flex justify-center">
                                    <img
                                        :src="imageUrl(product)"
                                        :alt="imageAlt(product)"
                                        :title="imageTitle(product)"
                                        class="h-10 w-14 rounded-sm border border-slate-300 object-cover dark:border-slate-600"
                                    />
                                </div>

                                <div
                                    v-if="product.images_count"
                                    class="mt-0.5 text-center text-[9px] text-slate-500 dark:text-slate-300"
                                >
                                    {{ product.images_count }}
                                </div>
                            </td>

                            <!-- Product -->
                            <td class="px-2 py-1">
                                <div class="text-left">
                                    <a
                                        :href="productPublicUrl(product)"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="text-xs text-sky-700 hover:text-amber-700 hover:underline dark:text-sky-300 dark:hover:text-amber-300"
                                        :title="productShort(product) || productSubtitle(product) || productTitle(product)"
                                    >
                                        {{ truncateText(productTitle(product), 80) }}
                                    </a>

                                    <div
                                        v-if="productSubtitle(product)"
                                        class="text-[10px] text-slate-700 dark:text-slate-300"
                                    >
                                        {{ truncateText(productSubtitle(product), 75) }}
                                    </div>

                                    <div class="mt-0.5 text-[10px] italic text-slate-500 dark:text-slate-400">
                                        {{ truncateText(product.url, 70) }}
                                    </div>

                                    <!-- Category -->
                                    <div
                                        v-if="mainCategoryTitle(product)"
                                        class="mt-1 flex items-center gap-1 text-[10px] text-indigo-700 dark:text-indigo-300"
                                        :title="categoriesTitle(product)"
                                    >
                                        <svg class="h-3 w-3 shrink-0 fill-current" viewBox="0 0 512 512">
                                            <path d="M144 132h352c8.837 0 16-7.163 16-16V76c0-8.837-7.163-16-16-16H144c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16zm0 160h352c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H144c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16zm0 160h352c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H144c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16z" />
                                        </svg>

                                        {{ truncateText(mainCategoryTitle(product), 55) }}

                                        <span
                                            v-if="product.categories_count"
                                            class="text-slate-400"
                                        >
                                            ({{ product.categories_count }})
                                        </span>
                                    </div>

                                    <!-- Attributes -->
                                    <div
                                        v-if="product.attribute_values_count"
                                        class="mt-0.5 flex items-center gap-1 text-[9px] text-fuchsia-700 dark:text-fuchsia-300"
                                        :title="attributesTitle(product)"
                                    >
                                        {{ t('attributes') }}:
                                        {{ product.attribute_values_count }}
                                    </div>

                                    <!-- Variants -->
                                    <a
                                        :href="route('admin.marketProductVariants.index', {
                                            market_product_id: product.id,
                                        })"
                                        class="mt-0.5 flex items-center gap-1 text-[10px] text-fuchsia-700 hover:text-fuchsia-900 hover:underline dark:text-fuchsia-300 dark:hover:text-fuchsia-100"
                                        :title="t('marketProductVariants')"
                                    >
                                        {{ t('marketProductVariants') }}:
                                        {{ product.variants_count ?? 0 }}

                                        <span
                                            v-if="product.available_variants_count"
                                            class="text-emerald-600 dark:text-emerald-300"
                                        >
                                            ({{ product.available_variants_count }})
                                        </span>
                                    </a>

                                    <!-- Shop -->
                                    <div
                                        v-if="shopTitle(product)"
                                        class="mt-1 flex items-center gap-1 text-[10px] text-amber-700 dark:text-amber-300"
                                        :title="supplierTitle(product)"
                                    >
                                        <span class="truncate">
                                            {{ truncateText(shopTitle(product), 42) }}
                                        </span>
                                    </div>

                                    <div
                                        v-else
                                        class="mt-1 text-[10px] text-slate-400"
                                        :title="supplierTitle(product)"
                                    >
                                        {{ t('noData') }}
                                    </div>

                                    <!-- Barcode -->
                                    <div
                                        v-if="product.barcode"
                                        class="mt-0.5 flex items-center gap-1 text-[9px]"
                                    >
                                        <span
                                            class="rounded-sm border border-slate-400 bg-slate-200 px-1 py-0.5 text-slate-700 dark:bg-slate-800 dark:text-slate-300"
                                            :title="t('barcode')"
                                        >
                                            {{ truncateText(product.barcode, 24) }}
                                        </span>
                                    </div>

                                    <div class="mt-0.5 flex flex-wrap gap-1 text-[9px]">
                                        <span
                                            v-if="product.vendor_code"
                                            class="rounded-sm border border-slate-400 bg-violet-100 px-1 py-0.5 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300"
                                            :title="t('vendorCode')"
                                        >
                                            {{ truncateText(product.vendor_code, 24) }}
                                        </span>

                                        <span
                                            v-if="product.sku"
                                            class="rounded-sm border border-slate-400 bg-blue-100 px-1 py-0.5 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300"
                                            :title="t('sku')"
                                        >
                                            SKU: {{ truncateText(product.sku, 24) }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Price -->
                            <td class="whitespace-nowrap px-2 py-1">
                                <div class="text-right">
                                    <div class="text-sm font-bold text-teal-700 dark:text-teal-300">
                                        {{ formatMoney(product.price, product.currency) }}
                                    </div>

                                    <div
                                        v-if="product.old_price"
                                        class="text-sm text-slate-400 line-through"
                                    >
                                        {{ formatMoney(product.old_price, product.currency) }}
                                    </div>

                                    <div
                                        v-if="product.wholesale_price"
                                        class="mt-0.5 text-[10px] text-blue-700 dark:text-blue-300"
                                        :title="t('wholesalePrice')"
                                    >
                                        {{ formatMoney(product.wholesale_price, product.currency) }}

                                        <span v-if="product.wholesale_min_quantity">
                                            × {{ product.wholesale_min_quantity }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Quantity -->
                            <td class="whitespace-nowrap px-2 py-1">
                                <div class="text-center">
                                    <div
                                        class="text-sm font-bold"
                                        :class="product.in_stock
                                            ? 'text-amber-600 dark:text-amber-300'
                                            : 'text-rose-600 dark:text-rose-300'"
                                    >
                                        {{ product.quantity }}
                                    </div>

                                    <span
                                        class="text-[9px]"
                                        :class="product.in_stock
                                            ? 'text-amber-600 dark:text-amber-300'
                                            : 'text-rose-600 dark:text-rose-300'"
                                    >
                                        {{ product.in_stock ? t('inStock') : t('outOfStock') }}
                                    </span>
                                </div>
                            </td>

                            <!-- Rating -->
                            <td class="whitespace-nowrap px-2 py-3">
                                <div
                                    class="text-center text-xs text-rose-500 dark:text-rose-300"
                                    :title="t('rating')"
                                >
                                    {{ formatRating(product) }}
                                </div>
                            </td>

                            <!-- Reviews -->
                            <td class="whitespace-nowrap px-2 py-1">
                                <div
                                    class="text-center text-xs text-sky-700 dark:text-sky-300"
                                    :title="t('reviews')"
                                >
                                    {{ product.reviews_count ?? product.rating_count ?? 0 }}
                                </div>
                            </td>

                            <!-- Views -->
                            <td class="whitespace-nowrap px-2 py-1">
                                <div
                                    class="text-center text-xs text-blue-700 dark:text-blue-300"
                                    :title="t('views')"
                                >
                                    {{ product.views ?? 0 }}
                                </div>
                            </td>

                            <!-- Likes -->
                            <td class="whitespace-nowrap px-2 py-1">
                                <div class="text-center text-xs" :title="t('likes')">
                                    {{ product.likes_count ?? 0 }}
                                </div>
                            </td>

                            <!-- Flags / moderation -->
                            <td class="whitespace-nowrap px-2 py-1">
                                <div class="flex flex-col items-center justify-center gap-1">
                                    <div class="flex items-center justify-center gap-1">
                                        <LeftToggle
                                            :isActive="product.left"
                                            :title="product.left ? t('enabled') : t('disabled')"
                                            @toggle-left="emit('toggle-left', product)"
                                        />

                                        <MainToggle
                                            :isActive="product.main"
                                            :title="product.main ? t('enabled') : t('disabled')"
                                            @toggle-main="emit('toggle-main', product)"
                                        />

                                        <RightToggle
                                            :isActive="product.right"
                                            :title="product.right ? t('enabled') : t('disabled')"
                                            @toggle-right="emit('toggle-right', product)"
                                        />
                                    </div>

                                    <div class="flex items-center justify-center gap-1">
                                        <NewButtonToggle
                                            :isActive="product.is_new"
                                            :title="product.is_new ? t('enabled') : t('disabled')"
                                            @toggle-is-new="emit('toggle-is-new', product)"
                                        />

                                        <HitButtonToggle
                                            :isActive="product.is_hit"
                                            :title="product.is_hit ? t('enabled') : t('disabled')"
                                            @toggle-is-hit="emit('toggle-is-hit', product)"
                                        />

                                        <SaleButtonToggle
                                            :isActive="product.is_sale"
                                            :title="product.is_sale ? t('enabled') : t('disabled')"
                                            @toggle-is-sale="emit('toggle-is-sale', product)"
                                        />
                                    </div>

                                    <span class="px-2 py-1 text-[10px] font-semibold text-fuchsia-700 dark:text-fuchsia-300">
                                        {{ statusBadge(product.status).text }}
                                    </span>

                                    <div class="flex items-center justify-center gap-1">
                                        <span
                                            class="rounded-sm border px-2 py-1 text-[9px] font-semibold"
                                            :class="moderationBadge(product.moderation_status).class"
                                            :title="product.moderation_note
                                                ? `${product.moderation_note}${product.moderated_at
                                                    ? ` [${formatDate(product.moderated_at)}]`
                                                    : ''}`
                                                : null"
                                        >
                                            {{ moderationBadge(product.moderation_status).text }}
                                        </span>

                                        <ModerationButton
                                            :isAdmin="isAdmin"
                                            :status="product.moderation_status ?? 0"
                                            :initialNote="product.moderation_note || ''"
                                            mode="toggle"
                                            @submit="({ status, note }) => emit(
                                                'approve',
                                                product,
                                                status,
                                                note
                                            )"
                                        />
                                    </div>
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="whitespace-nowrap px-2 py-1">
                                <div class="flex items-center justify-center gap-1">
                                    <ActivityToggle
                                        :isActive="product.activity"
                                        :title="product.activity ? t('enabled') : t('disabled')"
                                        @toggle-activity="emit('toggle-activity', product)"
                                    />

                                    <IconEdit
                                        :href="route('admin.marketProducts.edit', {
                                            marketProduct: product.id,
                                        })"
                                    />

                                    <DeleteIconButton
                                        @delete="emit('delete', product)"
                                    />
                                </div>
                            </td>

                            <!-- Checkbox -->
                            <td class="w-px whitespace-nowrap px-1 py-1">
                                <div class="text-center">
                                    <input
                                        type="checkbox"
                                        :checked="selectedProducts.includes(product.id)"
                                        @change="emit('toggle-select', product.id)"
                                    />
                                </div>
                            </td>
                        </tr>
                    </template>
                </draggable>
            </table>

            <div
                v-else
                class="p-5 text-center text-slate-700 dark:text-slate-100"
            >
                {{ t('noData') }}
            </div>
        </div>
    </div>
</template>
