<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Карточки товаров маркетплейса.
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
import SaleButtonToggle from '@/Components/Admin/UI/Buttons/SaleButtonToggle.vue'
import HitButtonToggle from '@/Components/Admin/UI/Buttons/HitButtonToggle.vue'
import NewButtonToggle from '@/Components/Admin/UI/Buttons/NewButtonToggle.vue'
import AddButton from '@/Components/Admin/UI/Buttons/AddButton.vue'

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

/* ======================== Local state ======================== */

const localProducts = ref([])
const openedOwnerBlocks = ref([])

watch(
    () => props.products,
    (newValue) => {
        localProducts.value = JSON.parse(JSON.stringify(newValue || []))
    },
    { immediate: true, deep: true }
)

/* ======================== Drag and drop ======================== */

const handleDragEnd = () => {
    emit(
        'update-sort-order',
        localProducts.value.map((product) => product.id)
    )
}

/* ======================== Selection ======================== */

const allSelected = computed(() => {
    if (!localProducts.value.length) {
        return false
    }

    return localProducts.value.every((product) => {
        return props.selectedProducts.includes(product.id)
    })
})

const toggleAll = (event) => {
    emit('toggle-all', {
        ids: localProducts.value.map((product) => product.id),
        checked: Boolean(event?.target?.checked),
    })
}

/* ======================== Translations ======================== */

const productTranslation = (product) => {
    return product?.translation || {}
}

const productTitle = (product) => {
    return productTranslation(product)?.title || `ID: ${product?.id}`
}

const productSubtitle = (product) => {
    return productTranslation(product)?.subtitle || ''
}

const productShort = (product) => {
    return productTranslation(product)?.short || ''
}

const relationTitle = (relation) => {
    return relation?.translation?.title || ''
}

const companyTitle = (product) => {
    return relationTitle(product?.company)
        || product?.company?.legal_name
        || ''
}

const shopTitle = (product) => {
    return relationTitle(product?.shop)
}

const brandTitle = (product) => {
    return relationTitle(product?.brand)
}

const categoryTitle = (category) => {
    return category?.translation?.title || `ID: ${category?.id}`
}

/* ======================== Owner ======================== */

const ownerName = (product) => {
    return product?.owner?.name || t('noData')
}

const ownerEmail = (product) => {
    return product?.owner?.email || ''
}

const ownerTitle = (product) => {
    const owner = product?.owner

    if (!owner) {
        return t('noData')
    }

    return `${owner.name || ''}${owner.email ? ` — ${owner.email}` : ''}`.trim()
}

const ownerAvatar = (product) => {
    return product?.owner?.profile_photo_url
        || '/storage/profile-photos/default-image.png'
}

const isOwnerBlockOpen = (productId) => {
    return openedOwnerBlocks.value.includes(productId)
}

const toggleOwnerBlock = (productId) => {
    if (isOwnerBlockOpen(productId)) {
        openedOwnerBlocks.value = openedOwnerBlocks.value.filter(
            (id) => id !== productId
        )

        return
    }

    openedOwnerBlocks.value.push(productId)
}

/* ======================== Images ======================== */

const getPrimaryImage = (product) => {
    if (!Array.isArray(product?.images) || !product.images.length) {
        return null
    }

    return [...product.images].sort((left, right) => {
        return Number(left?.order ?? 0) - Number(right?.order ?? 0)
    })[0]
}

const imageUrl = (product) => {
    const image = getPrimaryImage(product)

    return image?.webp_url
        || image?.thumb_url
        || image?.image_url
        || image?.url
        || '/storage/market/market_product_images/default-image.png'
}

const imageAlt = (product) => {
    const image = getPrimaryImage(product)

    return image?.alt || productTitle(product)
}

const imageTitle = (product) => {
    const image = getPrimaryImage(product)

    return image?.caption || productTitle(product)
}

/* ======================== Status ======================== */

const statusLabelKeyMap = {
    draft: 'statusDraft',
    published: 'statusPublished',
    archived: 'statusArchived',
}

const getStatusLabel = (status) => {
    return t(statusLabelKeyMap[status] || status || 'no')
}

const moderationBadge = (status) => {
    const value = Number(status ?? 0)

    if (value === 1) {
        return {
            text: t('statusSelectApproved'),
            class:
                'bg-emerald-100 text-emerald-700 border-emerald-300 ' +
                'dark:bg-emerald-900/40 dark:text-emerald-300',
        }
    }

    if (value === 2) {
        return {
            text: t('statusSelectRejected'),
            class:
                'bg-rose-100 text-rose-700 border-rose-300 ' +
                'dark:bg-rose-900/40 dark:text-rose-300',
        }
    }

    return {
        text: t('underModeration'),
        class:
            'bg-amber-100 text-amber-800 border-amber-300 ' +
            'dark:bg-amber-900/40 dark:text-amber-300',
    }
}

/* ======================== Formatting ======================== */

const safeNumber = (value) => {
    const number = Number(value)

    return Number.isFinite(number) ? number : 0
}

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

const truncateText = (text, maxLength = 80) => {
    if (!text) {
        return ''
    }

    const value = String(text)

    return value.length > maxLength
        ? `${value.slice(0, maxLength).trimEnd()}…`
        : value
}

const formatMoney = (value, currency) => {
    if (value === null || value === undefined || value === '') {
        return '—'
    }

    const amount = safeNumber(value)

    const precision = Number.isFinite(Number(currency?.precision))
        ? Number(currency.precision)
        : 2

    const thousandsSeparator = currency?.thousands_sep ?? ' '
    const decimalSeparator = currency?.decimal_sep ?? '.'

    const parts = amount.toFixed(precision).split('.')

    const integerPart = parts[0].replace(
        /\B(?=(\d{3})+(?!\d))/g,
        thousandsSeparator
    )

    const formattedAmount = precision > 0
        ? `${integerPart}${decimalSeparator}${parts[1]}`
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

const formatRating = (product) => {
    return safeNumber(product?.rating_avg).toFixed(1)
}

/* ======================== Relations ======================== */

const mainCategory = (product) => {
    const categories = Array.isArray(product?.categories)
        ? product.categories
        : []

    return categories.find((category) => {
        return Boolean(category?.pivot?.main)
    }) || categories[0] || null
}

const mainCategoryTitle = (product) => {
    const category = mainCategory(product)

    return category ? categoryTitle(category) : ''
}

const categoriesTitle = (product) => {
    const categories = Array.isArray(product?.categories)
        ? product.categories
        : []

    return categories
        .map((category) => categoryTitle(category))
        .filter(Boolean)
        .join(', ')
}

/* ======================== Attributes ======================== */

const attributeTitle = (item) => {
    return item?.attribute?.translation?.title
        || `ID: ${item?.market_attribute_id}`
}

const attributeReferenceValueTitle = (item) => {
    return item?.attribute_value?.translation?.title || ''
}

const attributeValueText = (item) => {
    const referenceTitle = attributeReferenceValueTitle(item)

    if (referenceTitle) {
        return referenceTitle
    }

    if (
        item?.value_string !== null
        && item?.value_string !== undefined
        && item.value_string !== ''
    ) {
        return String(item.value_string)
    }

    if (
        item?.value_number !== null
        && item?.value_number !== undefined
        && item.value_number !== ''
    ) {
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

    if (
        Array.isArray(item?.value_json)
        || (
            item?.value_json
            && typeof item.value_json === 'object'
        )
    ) {
        return JSON.stringify(item.value_json)
    }

    return ''
}

const attributesTooltip = (product) => {
    const attributes = Array.isArray(product?.attribute_values)
        ? product.attribute_values
        : []

    return attributes
        .map((item) => {
            const title = attributeTitle(item)
            const value = attributeValueText(item)
            const unit = value && item?.unit ? ` ${item.unit}` : ''

            return value
                ? `${title}: ${value}${unit}`
                : title
        })
        .filter(Boolean)
        .join('\n')
}

/* ======================== Tooltips ======================== */

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

/* ======================== URLs ======================== */

const productPublicUrl = (product) => {
    return `/market/products/${encodeURIComponent(product?.url || '')}`
}
</script>

<template>
    <div class="relative rounded-sm border border-slate-400 bg-white shadow-lg dark:border-slate-500 dark:bg-slate-700">

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
                    :checked="Boolean(allSelected)"
                    @change="toggleAll"
                />
            </label>
        </div>

        <div v-if="localProducts.length" class="p-3">
            <draggable
                v-model="localProducts"
                tag="div"
                item-key="id"
                handle=".handle"
                class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                @end="handleDragEnd"
            >
                <template #item="{ element: product }">
                    <div class="relative flex h-full flex-col rounded-md border border-slate-400 bg-slate-50/70 shadow-sm transition-shadow duration-150 hover:shadow-md dark:border-slate-500 dark:bg-slate-800/80">

                        <!-- Header -->
                        <header class="flex items-center justify-between border-b border-dashed border-slate-400 px-2 py-1 dark:border-slate-500">
                            <div class="flex items-center space-x-2">
                                <button
                                    type="button"
                                    class="handle cursor-move text-slate-400 hover:text-slate-700 dark:hover:text-slate-100"
                                    :title="t('dragDrop')"
                                >
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" />
                                    </svg>
                                </button>

                                <div
                                    class="rounded-sm border border-gray-400 bg-slate-200 px-1.5 py-0.5 text-[10px] font-semibold text-slate-800 dark:bg-slate-700 dark:text-blue-100"
                                    :title="`[${product.sort}] / ${formatDate(product.published_at)}`"
                                >
                                    ID: {{ product.id }}
                                </div>

                                <button
                                    type="button"
                                    class="text-slate-400 hover:text-blue-600 dark:hover:text-blue-300"
                                    :title="isOwnerBlockOpen(product.id) ? t('hideOwner') : t('showOwner')"
                                    @click.prevent="toggleOwnerBlock(product.id)"
                                >
                                    <svg
                                        class="h-4 w-4 transition-transform duration-200"
                                        :class="{ 'rotate-180': isOwnerBlockOpen(product.id) }"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </button>
                            </div>

                            <div class="flex items-center space-x-2">
                                <span
                                    class="rounded-sm border px-2 py-0.5 text-[10px] font-semibold"
                                    :class="moderationBadge(product.moderation_status).class"
                                    :title="product.moderation_note && product.moderated_at
                                        ? `${product.moderation_note} [${formatDate(product.moderated_at)}]`
                                        : null"
                                >
                                    {{ moderationBadge(product.moderation_status).text }}
                                </span>

                                <input
                                    type="checkbox"
                                    :checked="selectedProducts.includes(product.id)"
                                    @change="emit('toggle-select', product.id)"
                                />
                            </div>
                        </header>

                        <!-- Content -->
                        <div class="flex flex-1 flex-col">

                            <!-- Owner -->
                            <div
                                v-show="isOwnerBlockOpen(product.id)"
                                class="flex flex-col items-center justify-center text-center"
                            >
                                <img
                                    :src="ownerAvatar(product)"
                                    :title="ownerTitle(product)"
                                    :alt="t('owner')"
                                    class="h-12 w-12 rounded-full border border-slate-300 object-cover dark:border-slate-600"
                                />

                                <div
                                    class="mt-1 line-clamp-1 text-[11px] font-semibold leading-tight text-slate-700 dark:text-slate-100"
                                    :title="ownerName(product)"
                                >
                                    {{ ownerName(product) }}
                                </div>

                                <div
                                    v-if="ownerEmail(product)"
                                    class="line-clamp-1 text-[10px] leading-tight text-slate-500 dark:text-slate-300"
                                    :title="ownerEmail(product)"
                                >
                                    {{ ownerEmail(product) }}
                                </div>

                                <div
                                    v-if="product.show_from_at"
                                    class="mt-1 flex flex-col items-center justify-center text-center text-[10px] text-slate-500 dark:text-slate-300"
                                >
                                    {{ t('show') }}:
                                    {{ formatDate(product.show_from_at) }}
                                    /
                                    {{ formatDate(product.show_to_at) }}
                                </div>

                                <div
                                    v-else
                                    class="mt-1 text-center text-[10px] text-slate-500 dark:text-slate-300"
                                >
                                    {{ formatDate(product.published_at) }}
                                </div>
                            </div>

                            <!-- Image -->
                            <div class="relative w-full overflow-hidden bg-slate-200 dark:bg-slate-900">
                                <img
                                    :src="imageUrl(product)"
                                    :alt="imageAlt(product)"
                                    :title="imageTitle(product)"
                                    class="h-auto w-full object-cover"
                                />

                                <div class="absolute left-2 top-2 flex flex-wrap gap-1">
                                    <span
                                        v-if="product.is_new"
                                        :title="t('sortIsNew')"
                                        class="rounded-sm border border-teal-700 bg-teal-200/95 px-1.5 py-0.5 text-[9px] font-semibold text-teal-700 dark:border-teal-300 dark:bg-teal-800/90 dark:text-teal-300"
                                    >
                                        NEW
                                    </span>

                                    <span
                                        v-if="product.is_hit"
                                        :title="t('sortIsHit')"
                                        class="rounded-sm border border-yellow-700 bg-yellow-200/95 px-1.5 py-0.5 text-[9px] font-semibold text-yellow-700 dark:border-yellow-300 dark:bg-yellow-800/90 dark:text-yellow-300"
                                    >
                                        HIT
                                    </span>

                                    <span
                                        v-if="product.is_sale"
                                        :title="t('sortIsSale')"
                                        class="rounded-sm border border-pink-700 bg-pink-200/95 px-1.5 py-0.5 text-[9px] font-semibold text-pink-700 dark:border-pink-300 dark:bg-pink-800/90 dark:text-pink-300"
                                    >
                                        SALE
                                    </span>
                                </div>

                                <div
                                    v-if="product.images_count"
                                    class="absolute bottom-2 right-2 rounded-sm bg-slate-900/70 px-1.5 py-0.5 text-[9px] font-semibold text-white"
                                    :title="t('images')"
                                >
                                    {{ product.images_count }}
                                </div>
                            </div>

                            <div class="space-y-1">

                                <!-- Barcode -->
                                <div
                                    v-if="product.barcode"
                                    class="mt-0.5 flex items-center justify-center gap-1 text-[9px]"
                                >
                                    <span
                                        class="rounded-sm border border-slate-400 bg-slate-200 px-1 py-0.5 text-slate-700 dark:bg-slate-800 dark:text-slate-300"
                                        :title="t('barcode')"
                                    >
                                        {{ truncateText(product.barcode, 24) }}
                                    </span>
                                </div>

                                <!-- Codes -->
                                <div class="mt-0.5 flex flex-wrap items-center justify-center gap-1 text-[9px]">
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

                                <!-- Title -->
                                <a
                                    :href="productPublicUrl(product)"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="flex items-center justify-center px-3 text-xs font-semibold text-sky-700 hover:text-amber-700 hover:underline dark:text-sky-300 dark:hover:text-amber-300"
                                    :title="productShort(product) || productSubtitle(product) || productTitle(product)"
                                >
                                    {{ truncateText(productTitle(product), 80) }}
                                </a>

                                <div
                                    v-if="productSubtitle(product)"
                                    class="flex items-center justify-center text-[10px] font-semibold text-slate-700 dark:text-slate-300"
                                >
                                    {{ truncateText(productSubtitle(product), 75) }}
                                </div>

                                <div
                                    class="line-clamp-1 text-center text-[10px] italic text-slate-500 dark:text-slate-400"
                                    :title="product.url"
                                >
                                    {{ truncateText(product.url, 90) }}
                                </div>

                                <!-- Price -->
                                <div class="flex items-center justify-center space-x-2 px-2 font-semibold">
                                    <div
                                        v-if="product.old_price"
                                        class="text-sm text-slate-400 line-through"
                                    >
                                        {{ formatMoney(product.old_price, product.currency) }}
                                    </div>

                                    <div class="text-sm font-bold text-teal-700 dark:text-teal-300">
                                        {{ formatMoney(product.price, product.currency) }}
                                    </div>
                                </div>

                                <div
                                    v-if="product.wholesale_price"
                                    class="mt-0.5 text-center text-[10px] font-semibold text-blue-700 dark:text-blue-300"
                                    :title="t('wholesalePrice')"
                                >
                                    {{ formatMoney(product.wholesale_price, product.currency) }}

                                    <span v-if="product.wholesale_min_quantity">
                                        × {{ product.wholesale_min_quantity }}
                                    </span>
                                </div>

                                <!-- Stock -->
                                <div class="flex items-center justify-center space-x-1 text-[11px] font-semibold">
                                    <span
                                        class="text-[10px]"
                                        :class="product.in_stock
                                            ? 'text-amber-600 dark:text-amber-300'
                                            : 'text-rose-600 dark:text-rose-300'"
                                    >
                                        {{ product.in_stock ? t('inStock') : t('outOfStock') }}
                                    </span>

                                    <div
                                        class="text-sm font-bold"
                                        :class="product.in_stock
                                            ? 'text-amber-600 dark:text-amber-300'
                                            : 'text-rose-600 dark:text-rose-300'"
                                    >
                                        {{ product.quantity }}
                                    </div>
                                </div>

                                <!-- Shop -->
                                <div
                                    v-if="shopTitle(product)"
                                    class="mt-1 flex items-center justify-center gap-1 text-[10px] text-amber-700 dark:text-amber-300"
                                    :title="supplierTitle(product)"
                                >
                                    <span class="truncate">
                                        {{ truncateText(shopTitle(product), 42) }}
                                    </span>
                                </div>

                                <div
                                    v-else
                                    class="mt-1 flex items-center justify-center text-[10px] text-slate-400"
                                    :title="supplierTitle(product)"
                                >
                                    {{ t('noData') }}
                                </div>

                                <!-- Short -->
                                <div
                                    v-if="productShort(product)"
                                    class="line-clamp-3 text-center text-[12px] font-semibold text-teal-700 dark:text-teal-300"
                                >
                                    {{ truncateText(productShort(product), 160) }}
                                </div>

                                <!-- Tags / Attributes -->
                                <div class="grid grid-cols-2 gap-1 px-1 text-center text-[10px] font-semibold">
                                    <div
                                        class="rounded-sm border border-gray-400 bg-indigo-100 px-1 py-1 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300"
                                        :title="t('tags')"
                                    >
                                        <div>{{ t('tags') }}</div>
                                        <div>{{ product.tags_count ?? 0 }}</div>
                                    </div>

                                    <div
                                        class="rounded-sm border border-gray-400 bg-cyan-100 px-1 py-1 text-cyan-700 dark:bg-cyan-900/40 dark:text-cyan-300"
                                        :title="attributesTooltip(product)"
                                    >
                                        <div>{{ t('attributes') }}</div>
                                        <div>{{ product.attribute_values_count ?? 0 }}</div>
                                    </div>
                                </div>

                                <!-- Category -->
                                <div
                                    v-if="mainCategoryTitle(product)"
                                    class="mt-1 flex items-center justify-center gap-1 text-[10px] text-indigo-700 dark:text-indigo-300"
                                    :title="categoriesTitle(product)"
                                >
                                    {{ truncateText(mainCategoryTitle(product), 55) }}

                                    <span
                                        v-if="product.categories_count"
                                        class="text-slate-400"
                                    >
                                        ({{ product.categories_count }})
                                    </span>
                                </div>

                                <!-- Stats -->
                                <div class="grid grid-cols-5 gap-1 text-center text-[10px] font-semibold">
                                    <div class="px-1 py-1" :title="t('rating')">
                                        <div class="text-red-500 dark:text-red-400">★</div>
                                        <span class="text-slate-700 dark:text-slate-300">
                                            {{ formatRating(product) }}
                                        </span>
                                    </div>

                                    <div class="px-1 py-1" :title="t('reviews')">
                                        <div class="text-sky-500 dark:text-sky-300">●</div>
                                        <span class="text-slate-700 dark:text-slate-300">
                                            {{ product.reviews_count ?? 0 }}
                                        </span>
                                    </div>

                                    <div class="px-1 py-1" :title="t('likes')">
                                        <div class="text-rose-400 dark:text-rose-300">♥</div>
                                        <span class="text-slate-700 dark:text-slate-300">
                                            {{ product.likes_count ?? 0 }}
                                        </span>
                                    </div>

                                    <div class="px-1 py-1" :title="t('views')">
                                        <div class="text-blue-600 dark:text-blue-300">◉</div>
                                        <span class="text-slate-700 dark:text-slate-300">
                                            {{ product.views ?? 0 }}
                                        </span>
                                    </div>

                                    <a
                                        :href="route('admin.marketProductVariants.index', {
                                            market_product_id: product.id,
                                        })"
                                        class="flex flex-col items-center justify-center px-1 py-1 text-fuchsia-700 transition dark:text-fuchsia-300"
                                        :title="t('marketProductVariants')"
                                    >
                                        <span>▦</span>

                                        <span class="text-slate-700 dark:text-slate-300">
                                            {{ product.variants_count ?? 0 }}

                                            <span
                                                v-if="product.available_variants_count"
                                                class="text-emerald-600 dark:text-emerald-300"
                                            >
                                                / {{ product.available_variants_count }}
                                            </span>
                                        </span>
                                    </a>
                                </div>

                                <!-- Status -->
                                <div class="text-center text-[11px] font-semibold text-fuchsia-700 dark:text-fuchsia-300">
                                    <span>{{ t('status') }}: </span>
                                    {{ getStatusLabel(product.status) }}
                                </div>

                                <!-- Moderation -->
                                <div class="flex justify-center space-x-1">
                                    <span
                                        class="rounded-sm border px-2 py-1 text-[10px] font-semibold"
                                        :class="moderationBadge(product.moderation_status).class"
                                        :title="product.moderation_note && product.moderated_at
                                            ? `${product.moderation_note} [${formatDate(product.moderated_at)}]`
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
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-center border-t border-dashed border-slate-400 px-3 py-2 dark:border-slate-500">
                            <div class="flex flex-wrap items-center justify-center gap-1">
                                <div class="flex flex-row items-center space-x-7">
                                    <div class="flex items-center space-x-1">
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

                                    <div class="flex flex-row items-center space-x-1">
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
                                </div>

                                <div class="flex items-center space-x-1">
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

                                    <AddButton
                                        :href="route('admin.marketProductVariants.create', {
                                            market_product_id: product.id,
                                        })"
                                        :title="t('addMarketProductVariant')"
                                        class="py-1"
                                    />

                                    <DeleteIconButton
                                        @delete="emit('delete', product)"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </draggable>
        </div>

        <div
            v-else
            class="p-5 text-center text-slate-700 dark:text-slate-100"
        >
            {{ t('noData') }}
        </div>
    </div>
</template>
