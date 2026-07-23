<script setup>
import { defineEmits, defineProps, ref, watch } from 'vue'
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

const props = defineProps({
    products: {
        type: Array,
        default: () => []
    },

    selectedProducts: {
        type: Array,
        default: () => []
    },

    isAdmin: {
        type: Boolean,
        default: false
    }
})

const emits = defineEmits([
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
    'approve'
])

/* ======================== Local state ======================== */

const localProducts = ref([])
const openedOwnerBlocks = ref([])

watch(
    () => props.products,
    (newValue) => {
        localProducts.value = JSON.parse(
            JSON.stringify(newValue || [])
        )
    },
    {
        immediate: true,
        deep: true
    }
)

/* ======================== Drag and drop ======================== */

const handleDragEnd = () => {
    emits(
        'update-sort-order',
        localProducts.value.map((product) => product.id)
    )
}

/* ======================== Selection ======================== */

const toggleAll = (event) => {
    emits('toggle-all', {
        ids: localProducts.value.map((product) => product.id),
        checked: event.target.checked
    })
}

const allSelected = () => {
    return localProducts.value.length
        && localProducts.value.every((product) => {
            return props.selectedProducts.includes(product.id)
        })
}

/* ======================== Translations ======================== */

const productTranslation = (product) => {
    return product?.translation || {}
}

const productTitle = (product) => {
    return productTranslation(product)?.title
        || `ID: ${product?.id}`
}

const productSubtitle = (product) => {
    return productTranslation(product)?.subtitle || ''
}

const productShort = (product) => {
    return productTranslation(product)?.short || ''
}

const relationTitle = (relation) => {
    return relation?.title
        || relation?.translation?.title
        || relation?.translations?.[0]?.title
        || ''
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
    return category?.title
        || category?.translation?.title
        || category?.translations?.[0]?.title
        || `ID: ${category?.id}`
}

const tagTitle = (tag) => {
    return tag?.title
        || tag?.translation?.title
        || tag?.translations?.[0]?.title
        || `ID: ${tag?.id}`
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

    return `${owner.name || ''}${
        owner.email
            ? ` — ${owner.email}`
            : ''
    }`.trim()
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
    if (
        Array.isArray(product?.images)
        && product.images.length
    ) {
        return [...product.images].sort((left, right) => {
            return Number(left?.order ?? 0)
                - Number(right?.order ?? 0)
        })[0]
    }

    return null
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
    archived: 'statusArchived'
}

const getStatusLabel = (status) => {
    return t(
        statusLabelKeyMap[status]
        || status
        || 'no'
    )
}

const moderationBadge = (status) => {
    const value = Number(status ?? 0)

    if (value === 1) {
        return {
            text: t('statusSelectApproved'),
            class:
                'bg-emerald-100 text-emerald-700 border-emerald-300 ' +
                'dark:bg-emerald-900/40 dark:text-emerald-300'
        }
    }

    if (value === 2) {
        return {
            text: t('statusSelectRejected'),
            class:
                'bg-rose-100 text-rose-700 border-rose-300 ' +
                'dark:bg-rose-900/40 dark:text-rose-300'
        }
    }

    return {
        text: t('underModeration'),
        class:
            'bg-amber-100 text-amber-800 border-amber-300 ' +
            'dark:bg-amber-900/40 dark:text-amber-300'
    }
}

/* ======================== Formatting ======================== */

const safeNumber = (value) => {
    const number = Number(value)

    return Number.isFinite(number)
        ? number
        : 0
}

const formatDate = (dateString) => {
    if (!dateString) {
        return ''
    }

    const date = new Date(dateString)

    if (Number.isNaN(date.getTime())) {
        return ''
    }

    return date.toLocaleDateString(
        locale.value || 'ru-RU',
        {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        }
    )
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
    if (
        value === null
        || value === undefined
        || value === ''
    ) {
        return '—'
    }

    const amount = safeNumber(value)

    const precision = Number.isFinite(
        Number(currency?.precision)
    )
        ? Number(currency.precision)
        : 2

    const thousandsSeparator =
        currency?.thousands_sep ?? ' '

    const decimalSeparator =
        currency?.decimal_sep ?? '.'

    const parts = amount
        .toFixed(precision)
        .split('.')

    const integerPart = parts[0].replace(
        /\B(?=(\d{3})+(?!\d))/g,
        thousandsSeparator
    )

    const formattedAmount = precision > 0
        ? `${integerPart}${decimalSeparator}${parts[1]}`
        : integerPart

    const symbol = String(
        currency?.symbol
        || currency?.code
        || ''
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

/** Подсказка с категориями. */
const categoriesTitle = (product) => {
    if (
        !Array.isArray(product?.categories)
        || !product.categories.length
    ) {
        return ''
    }

    return product.categories
        .map((category) => categoryTitle(category))
        .filter(Boolean)
        .join(', ')
}

/* ======================== Relations ======================== */

const mainCategory = (product) => {
    const categories = Array.isArray(product?.categories)
        ? product.categories
        : []

    return categories.find((category) => {
        return Boolean(category?.pivot?.main)
            || Boolean(category?.main)
    }) || categories[0] || null
}

const mainCategoryTitle = (product) => {
    const category = mainCategory(product)

    return category
        ? categoryTitle(category)
        : ''
}

const tagsTooltip = (product) => {
    const tags = Array.isArray(product?.tags)
        ? product.tags
        : []

    return tags
        .map((tag) => tagTitle(tag))
        .filter(Boolean)
        .join(', ')
}

const attributesTooltip = (product) => {
    const attributes = Array.isArray(product?.attribute_values)
        ? product.attribute_values
        : []

    return attributes
        .map((item) => {
            const attributeTitle =
                item?.attribute?.title
                || `ID: ${item?.market_attribute_id}`

            let value =
                item?.attribute_value?.title
                || item?.value_string
                || item?.value_number
                || item?.value_date
                || ''

            if (item?.value_boolean === true) {
                value = t('yes')
            }

            if (item?.value_boolean === false) {
                value = t('no')
            }

            if (
                Array.isArray(item?.value_json)
                || (
                    item?.value_json
                    && typeof item.value_json === 'object'
                )
            ) {
                value = JSON.stringify(item.value_json)
            }

            const unit = item?.unit
                ? ` ${item.unit}`
                : ''

            return value !== ''
                ? `${attributeTitle}: ${value}${unit}`
                : attributeTitle
        })
        .filter(Boolean)
        .join('\n')
}

/* ======================== URLs ======================== */

const productPublicUrl = (product) => {
    return `/market/products/${encodeURIComponent(
        product?.url || ''
    )}`
}
</script>

<template>
    <div
        class="relative rounded-sm border
               border-slate-400 dark:border-slate-500
               bg-white dark:bg-slate-700
               shadow-lg"
    >
        <!-- Панель выбора -->
        <div
            class="flex items-center justify-between
                   border-b border-slate-400 dark:border-slate-500
                   px-3 py-2"
        >
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('selected') }}:
                {{ selectedProducts.length }}
            </div>

            <label
                v-if="localProducts.length"
                class="flex cursor-pointer items-center
                       text-xs text-slate-600 dark:text-slate-200"
            >
                <span>{{ t('selectAll') }}</span>

                <input
                    type="checkbox"
                    class="mx-2"
                    :checked="allSelected()"
                    @change="toggleAll"
                />
            </label>
        </div>

        <div
            v-if="localProducts.length"
            class="p-3"
        >
            <draggable
                v-model="localProducts"
                tag="div"
                item-key="id"
                handle=".handle"
                class="grid grid-cols-1 gap-3
                       sm:grid-cols-2
                       lg:grid-cols-3
                       xl:grid-cols-4"
                @end="handleDragEnd"
            >
                <template #item="{ element: product }">
                    <div
                        class="relative flex h-full flex-col
                               rounded-md border
                               border-slate-400 dark:border-slate-500
                               bg-slate-50/70 dark:bg-slate-800/80
                               shadow-sm transition-shadow duration-150
                               hover:shadow-md"
                    >
                        <!-- Шапка карточки -->
                        <header
                            class="flex items-center justify-between
                                   border-b border-dashed
                                   border-slate-400 dark:border-slate-500
                                   px-2 py-1"
                        >
                            <div class="flex items-center space-x-2">
                                <button
                                    type="button"
                                    class="handle cursor-move
                                           text-slate-400
                                           hover:text-slate-700
                                           dark:hover:text-slate-100"
                                    :title="t('dragDrop')"
                                >
                                    <svg
                                        class="h-4 w-4"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"
                                        />
                                    </svg>
                                </button>

                                <div
                                    class="rounded-sm border
                                           border-gray-400
                                           bg-slate-200 dark:bg-slate-700
                                           px-1.5 py-0.5
                                           text-[10px] font-semibold
                                           text-slate-800 dark:text-blue-100"
                                    :title="`[${product.sort}] / ${formatDate(product.published_at)}`"
                                >
                                    ID: {{ product.id }}
                                </div>

                                <button
                                    type="button"
                                    class="text-slate-400
                                           hover:text-blue-600
                                           dark:hover:text-blue-300"
                                    :title="isOwnerBlockOpen(product.id)
                                            ? t('hideOwner')
                                            : t('showOwner')"
                                    @click.prevent="toggleOwnerBlock(product.id)"
                                >
                                    <svg
                                        class="h-4 w-4
                                               transition-transform duration-200"
                                        :class="{
                                            'rotate-180':
                                                isOwnerBlockOpen(product.id),
                                        }"
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
                                    class="rounded-sm border
                                           px-2 py-0.5
                                           text-[10px] font-semibold"
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
                                    @change="$emit('toggle-select', product.id)"
                                />
                            </div>
                        </header>

                        <!-- Основной блок -->
                        <div
                            class="flex flex-1 flex-col"
                        >
                            <!-- Владелец и период показа -->
                            <div
                                v-show="isOwnerBlockOpen(product.id)"
                                class="flex flex-col items-center
                                       justify-center text-center"
                            >
                                <img
                                    :src="ownerAvatar(product)"
                                    :title="ownerTitle(product)"
                                    :alt="t('owner')"
                                    class="h-12 w-12 rounded-full
                                           border border-slate-300
                                           object-cover
                                           dark:border-slate-600"
                                />

                                <div
                                    class="mt-1 line-clamp-1
                                           text-[11px] font-semibold
                                           leading-tight
                                           text-slate-700 dark:text-slate-100"
                                    :title="ownerName(product)"
                                >
                                    {{ ownerName(product) }}
                                </div>

                                <div
                                    v-if="ownerEmail(product)"
                                    class="line-clamp-1
                                           text-[10px] leading-tight
                                           text-slate-500 dark:text-slate-300"
                                    :title="ownerEmail(product)"
                                >
                                    {{ ownerEmail(product) }}
                                </div>

                                <div
                                    v-if="product.show_from_at"
                                    class="mt-1 flex flex-col
                                           items-center justify-center
                                           text-center text-[10px]
                                           text-slate-500 dark:text-slate-300"
                                >
                                    {{ t('show') }}:
                                    {{ product.show_from_at }}
                                    /
                                    {{ product.show_to_at }}
                                </div>

                                <div
                                    v-else
                                    class="mt-1 text-center text-[10px]
                                           text-slate-500 dark:text-slate-300"
                                >
                                    {{ formatDate(product.published_at) }}
                                </div>
                            </div>

                            <!-- Изображение -->
                            <div
                                class="relative w-full overflow-hidden
                                       bg-slate-200 dark:bg-slate-900"
                            >
                                <img
                                    :src="imageUrl(product)"
                                    :alt="imageAlt(product)"
                                    :title="imageTitle(product)"
                                    class="h-auto w-full object-cover"
                                />

                                <!-- Маркетинговые метки -->
                                <div
                                    class="absolute left-2 top-2
                                           flex flex-wrap gap-1"
                                >
                                    <span
                                        v-if="product.is_new"
                                        :title="t('sortIsNew')"
                                        class="px-1.5 py-0.5 rounded-sm
                                               border border-teal-700 dark:border-teal-300
                                               bg-teal-200/95 dark:bg-teal-800/90
                                               text-[9px] font-semibold
                                               text-teal-700 dark:text-teal-300"
                                    >
                                        NEW
                                    </span>

                                    <span
                                        v-if="product.is_hit"
                                        :title="t('sortIsHit')"
                                        class="px-1.5 py-0.5 rounded-sm
                                               border border-yellow-700 dark:border-yellow-300
                                               bg-yellow-200/95 dark:bg-yellow-800/90
                                               text-[9px] font-semibold
                                               text-yellow-700 dark:text-yellow-300"
                                    >
                                        HIT
                                    </span>

                                    <span
                                        v-if="product.is_sale"
                                        :title="t('sortIsSale')"
                                        class="px-1.5 py-0.5 rounded-sm
                                               border border-pink-700 dark:border-pink-300
                                               bg-pink-200/95 dark:bg-pink-800/90
                                               text-[9px] font-semibold
                                               text-pink-700 dark:text-pink-300"
                                    >
                                        SALE
                                    </span>
                                </div>

                                <div
                                    v-if="product.images_count"
                                    class="absolute bottom-2 right-2
                                           rounded-sm bg-slate-900/70
                                           px-1.5 py-0.5
                                           text-[9px] font-semibold
                                           text-white"
                                    :title="t('images')"
                                >
                                    {{ product.images_count }}
                                </div>
                            </div>

                            <div class="space-y-1">

                                <!-- Баркод -->
                                <div
                                    class="mt-0.5 flex items-center justify-center gap-1 text-[9px]"
                                >
                                    <svg class="h-5 w-5 shrink-0 fill-current"
                                         viewBox="0 0 512 512">
                                        <path d="M0 448V64h18v384H0zm26.857-.273V64H36v383.727h-9.143zm27.143 0V64h8.857v383.727H54zm44.857 0V64h8.857v383.727h-8.857zm36 0V64h17.714v383.727h-17.714zm44.857 0V64h8.857v383.727h-8.857zm18 0V64h8.857v383.727h-8.857zm18 0V64h8.857v383.727h-8.857zm35.715 0V64h18v383.727h-18zm44.857 0V64h18v383.727h-18zm35.999 0V64h18.001v383.727h-18.001zm36.001 0V64h18.001v383.727h-18.001zm26.857 0V64h18v383.727h-18zm45.143 0V64h26.857v383.727h-26.857zm35.714 0V64h9.143v383.727H476zm18 .273V64h18v384h-18z" />
                                    </svg>
                                    <span
                                        v-if="product.barcode"
                                        class="rounded-sm
                                                   border border-slate-400
                                                   bg-slate-200 px-1 py-0.5
                                                   text-slate-700
                                                   dark:bg-slate-800
                                                   dark:text-slate-300"
                                        :title="t('barcode')"
                                    >
                                            {{ truncateText(product.barcode, 24) }}
                                        </span>
                                </div>

                                <!-- Артикулы -->
                                <div class="mt-0.5 flex items-center justify-center
                                            flex-wrap gap-1 text-[9px]">
                                    <span
                                        v-if="product.vendor_code"
                                        class="rounded-sm
                                               border border-slate-400
                                               bg-violet-100 px-1 py-0.5
                                               text-violet-700
                                               dark:bg-violet-900/40
                                               dark:text-violet-300"
                                        :title="t('vendorCode')"
                                    >
                                        {{ truncateText(product.vendor_code, 24) }}
                                    </span>
                                    <span
                                        v-if="product.sku"
                                        class="rounded-sm
                                                   border border-slate-400
                                                   bg-blue-100 px-1 py-0.5
                                                   text-blue-700
                                                   dark:bg-blue-900/40
                                                   dark:text-blue-300"
                                        :title="t('sku')"
                                    >
                                            SKU: {{ truncateText(product.sku, 24) }}
                                        </span>
                                </div>

                                <!-- Название -->
                                <a
                                    :href="productPublicUrl(product)"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="flex items-center justify-center
                                           px-3 font-semibold text-xs
                                           text-sky-700 dark:text-sky-300
                                           hover:text-amber-700
                                           hover:underline
                                           dark:hover:text-amber-300"
                                    :title="productShort(product)
                                            || productSubtitle(product)
                                            || productTitle(product)"
                                >
                                    {{ truncateText(productTitle(product), 80) }}
                                </a>

                                <!-- Заголовок -->
                                <div
                                    v-if="productSubtitle(product)"
                                    class="flex items-center justify-center font-semibold
                                           text-[10px] text-slate-700 dark:text-slate-300"
                                >
                                    {{ truncateText(productSubtitle(product), 75) }}
                                </div>

                                <!-- URL -->
                                <div
                                    class="line-clamp-1 text-center
                                           text-[10px] italic text-slate-500 dark:text-slate-400"
                                    :title="product.url"
                                >
                                    {{ truncateText(product.url, 90) }}
                                </div>

                                <!-- Цена -->
                                <div class="flex items-center justify-center space-x-2
                                            font-semibold px-2">
                                    <div
                                        v-if="product.old_price"
                                        class="text-sm
                                               text-slate-400 line-through"
                                    >
                                        {{ formatMoney(product.old_price, product.currency) }}
                                    </div>
                                    <div
                                        class="text-sm font-bold
                                               text-teal-700 dark:text-teal-300"
                                    >
                                        {{ formatMoney(product.price, product.currency) }}
                                    </div>
                                </div>

                                <!-- Оптовая Цена -->
                                <div
                                    v-if="product.wholesale_price"
                                    class="mt-0.5 text-[10px] font-semibold text-center
                                           text-blue-700 dark:text-blue-300"
                                    :title="t('wholesalePrice')"
                                >
                                    {{ formatMoney(product.wholesale_price, product.currency) }}

                                    <span
                                        v-if="product.wholesale_min_quantity"
                                    >
                                            × {{ product.wholesale_min_quantity }}
                                        </span>
                                </div>

                                <!-- Наличие -->
                                <div class="flex items-center justify-center space-x-1
                                            text-[11px] font-semibold">
                                    <span
                                        class="text-[10px]"
                                        :class="product.in_stock
                                            ? 'text-amber-600 dark:text-amber-300'
                                            : 'text-rose-600 dark:text-rose-300'"
                                    >
                                        {{ product.in_stock
                                        ? t('inStock')
                                        : t('outOfStock') }}
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

                                <!-- Магазин -->
                                <div
                                    v-if="shopTitle(product)"
                                    class="mt-1 flex items-center justify-center gap-1
                                           text-[10px] text-amber-700 dark:text-amber-300"
                                    :title="supplierTitle(product)"
                                >
                                    <svg class="h-3.5 w-3.5 shrink-0 fill-current"
                                         viewBox="0 0 640 512">
                                        <path d="M624 416H381.54c-.74 19.81-14.71 32-32.74 32H288c-18.69 0-33.02-17.47-32.77-32H16c-8.8 0-16 7.2-16 16v16c0 35.2 28.8 64 64 64h512c35.2 0 64-28.8 64-64v-16c0-8.8-7.2-16-16-16zM576 48c0-26.4-21.6-48-48-48H112C85.6 0 64 21.6 64 48v336h512V48zm-64 272H128V64h384v256z"></path>
                                    </svg>

                                    <span class="truncate">
                                        {{ truncateText(shopTitle(product), 42) }}
                                    </span>
                                </div>
                                <div
                                    v-else
                                    class="mt-1 flex items-center justify-center
                                           text-[10px] text-slate-400"
                                    :title="supplierTitle(product)"
                                >
                                    {{ t('noData') }}
                                </div>

                                <!-- Краткое описание -->
                                <div
                                    v-if="productShort(product)"
                                    class="line-clamp-3 text-center
                                           text-[12px] font-semibold
                                           text-teal-700 dark:text-teal-300"
                                >
                                    {{ truncateText(productShort(product), 160) }}
                                </div>

                                <!-- Теги, характеристики -->
                                <div
                                    class="grid grid-cols-2 gap-1 px-1
                                           font-semibold text-center text-[10px]"
                                >
                                    <div
                                        class="rounded-sm border border-gray-400
                                               bg-indigo-100 px-1 py-1
                                               text-indigo-700
                                               dark:bg-indigo-900/40
                                               dark:text-indigo-300"
                                        :title="tagsTooltip(product)"
                                    >
                                        <div class="font-semibold">
                                            {{ t('tags') }}
                                        </div>

                                        <div>
                                            {{ product.tags_count ?? 0 }}
                                        </div>
                                    </div>
                                    <div
                                        class="rounded-sm border border-gray-400
                                               bg-cyan-100 px-1 py-1
                                               text-cyan-700
                                               dark:bg-cyan-900/40
                                               dark:text-cyan-300"
                                        :title="attributesTooltip(product)"
                                    >
                                        <div class="font-semibold">
                                            {{ t('attributes') }}
                                        </div>

                                        <div>
                                            {{ product.attribute_values_count ?? 0 }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Категория -->
                                <div
                                    v-if="mainCategoryTitle(product)"
                                    class="mt-1 flex items-center justify-center gap-1
                                           text-[10px] text-indigo-700 dark:text-indigo-300"
                                    :title="categoriesTitle(product)"
                                >
                                    <svg class="h-3 w-3 shrink-0 fill-current"
                                         viewBox="0 0 512 512">
                                        <path d="M3.263 139.527c0-7.477 3.917-11.572 11.573-11.572h15.131V88.078c0-5.163.534-10.503.534-10.503h-.356s-1.779 2.67-2.848 3.738c-4.451 4.273-10.504 4.451-15.666-1.068l-5.518-6.231c-5.342-5.341-4.984-11.216.534-16.379l21.72-19.938C32.815 33.602 36.732 32 42.785 32H54.89c7.656 0 11.749 3.916 11.749 11.572v84.384h15.488c7.655 0 11.572 4.094 11.572 11.572v8.901c0 7.477-3.917 11.572-11.572 11.572H14.836c-7.656 0-11.573-4.095-11.573-11.572v-8.902zM2.211 304.591c0-47.278 50.955-56.383 50.955-69.165 0-7.18-5.954-8.755-9.28-8.755-3.153 0-6.479 1.051-9.455 3.852-5.079 4.903-10.507 7.004-16.111 2.451l-8.579-6.829c-5.779-4.553-7.18-9.805-2.803-15.409C13.592 201.981 26.025 192 47.387 192c19.437 0 44.476 10.506 44.476 39.573 0 38.347-46.753 46.402-48.679 56.909h39.049c7.529 0 11.557 4.027 11.557 11.382v8.755c0 7.354-4.028 11.382-11.557 11.382h-67.94c-7.005 0-12.083-4.028-12.083-11.382v-4.028zM5.654 454.61l5.603-9.28c3.853-6.654 9.105-7.004 15.584-3.152 4.903 2.101 9.63 3.152 14.359 3.152 10.155 0 14.358-3.502 14.358-8.23 0-6.654-5.604-9.106-15.934-9.106h-4.728c-5.954 0-9.28-2.101-12.258-7.88l-1.05-1.926c-2.451-4.728-1.226-9.806 2.801-14.884l5.604-7.004c6.829-8.405 12.257-13.483 12.257-13.483v-.35s-4.203 1.051-12.608 1.051H16.685c-7.53 0-11.383-4.028-11.383-11.382v-8.755c0-7.53 3.853-11.382 11.383-11.382h58.484c7.529 0 11.382 4.027 11.382 11.382v3.327c0 5.778-1.401 9.806-5.079 14.183l-17.509 20.137c19.611 5.078 28.716 20.487 28.716 34.845 0 21.363-14.358 44.126-48.503 44.126-16.636 0-28.192-4.728-35.896-9.455-5.779-4.202-6.304-9.805-2.626-15.934zM144 132h352c8.837 0 16-7.163 16-16V76c0-8.837-7.163-16-16-16H144c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16zm0 160h352c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H144c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16zm0 160h352c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H144c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16z"></path>
                                    </svg>

                                    {{ truncateText(mainCategoryTitle(product), 55) }}

                                    <span
                                        v-if="product.categories_count"
                                        class="text-slate-400"
                                    >
                                            ({{ product.categories_count }})
                                        </span>
                                </div>

                                <!-- Статистика -->
                                <div
                                    class="grid grid-cols-5 gap-1
                                       text-center text-[10px]
                                       font-semibold"
                                >
                                    <div
                                        class="px-1 py-1"
                                        :title="t('rating')"
                                    >
                                        <div class="flex justify-center"
                                             :title="t('ratingAvg')">
                                            <svg class="w-4 h-4 fill-current shrink-0
                                                        text-red-500 dark:text-red-400"
                                                 viewBox="0 0 24 24">
                                                <path
                                                    d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"></path>
                                            </svg>
                                        </div>
                                        <span class="text-slate-700 dark:text-slate-300">
                                            {{ formatRating(product) }}
                                        </span>
                                    </div>

                                    <div
                                        class="px-1 py-1"
                                        :title="t('reviews')"
                                    >
                                        <div class="flex justify-center" :title="t('reviews')">
                                            <svg class="w-4 h-4 fill-current shrink-0
                                                        text-sky-500 dark:text-sky-300"
                                                 viewBox="0 0 512 512">
                                                <path
                                                    d="M256 32C114.62 32 0 125.12 0 240c0 49.56 21.41 95.01 57.02 130.74C44.46 421.05 2.7 465.97 2.2 466.5A7.995 7.995 0 0 0 8 480c66.26 0 115.99-31.75 140.6-51.38C181.29 440.93 217.59 448 256 448c141.38 0 256-93.12 256-208S397.38 32 256 32zm24 302.44V352c0 8.84-7.16 16-16 16h-16c-8.84 0-16-7.16-16-16v-17.73c-11.42-1.35-22.28-5.19-31.78-11.46-6.22-4.11-6.82-13.11-1.55-18.38l17.52-17.52c3.74-3.74 9.31-4.24 14.11-2.03 3.18 1.46 6.66 2.22 10.26 2.22h32.78c4.66 0 8.44-3.78 8.44-8.42 0-3.75-2.52-7.08-6.12-8.11l-50.07-14.3c-22.25-6.35-40.01-24.71-42.91-47.67-4.05-32.07 19.03-59.43 49.32-63.05V128c0-8.84 7.16-16 16-16h16c8.84 0 16 7.16 16 16v17.73c11.42 1.35 22.28 5.19 31.78 11.46 6.22 4.11 6.82 13.11 1.55 18.38l-17.52 17.52c-3.74 3.74-9.31 4.24-14.11 2.03a24.516 24.516 0 0 0-10.26-2.22h-32.78c-4.66 0-8.44 3.78-8.44 8.42 0 3.75 2.52 7.08 6.12 8.11l50.07 14.3c22.25 6.36 40.01 24.71 42.91 47.67 4.05 32.06-19.03 59.42-49.32 63.04z" />
                                            </svg>
                                        </div>
                                        <span class="text-slate-700 dark:text-slate-300">
                                            {{ product.reviews_count ?? 0 }}
                                        </span>
                                    </div>

                                    <div
                                        class="px-1 py-1"
                                        :title="t('likes')"
                                    >
                                        <div class="flex justify-center" :title="t('likes')">
                                            <svg class="w-4 h-4 fill-current shrink-0
                                                        text-rose-400 dark:text-rose-300"
                                                 viewBox="0 0 24 24">
                                                <path
                                                    d="M3,9H1a1,1,0,0,0-1,1V22a1,1,0,0,0,1,1H4V10A1,1,0,0,0,3,9Z"></path>
                                                <path
                                                    d="M21.882,8.133A2.986,2.986,0,0,0,21,8H15V5c0-3.824-2.589-4.942-3.958-5a1.017,1.017,0,0,0-.734.277A1,1,0,0,0,10,1V5.638l-4,4.8V23H18.23A2.985,2.985,0,0,0,21.1,20.882l2.769-9A3,3,0,0,0,21.882,8.133Z"></path>
                                            </svg>
                                        </div>
                                        <span class="text-slate-700 dark:text-slate-300">
                                            {{ product.likes_count ?? 0 }}
                                        </span>
                                    </div>

                                    <div
                                        class="px-1 py-1"
                                        :title="t('views')"
                                    >
                                        <div class="flex justify-center" :title="t('views')">
                                            <svg class="w-4 h-4 fill-current shrink-0
                                                        text-blue-600 dark:text-blue-300"
                                                 viewBox="0 0 16 16">
                                                <path
                                                    d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"
                                                />
                                            </svg>
                                        </div>
                                        <span class="text-slate-700 dark:text-slate-300">
                                            {{ product.views ?? 0 }}
                                        </span>
                                    </div>

                                    <a :href="route('admin.marketProductVariants.index', {
                                            market_product_id: product.id,
                                        })"
                                        class="flex flex-col items-center justify-center px-1 py-1
                                               text-fuchsia-700 transition
                                               dark:text-fuchsia-300"
                                        :title="t('marketProductVariants')"
                                    >
                                        <svg
                                            class="h-4 w-4 shrink-0 fill-current"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                d="M4 4h7v7H4V4Zm9 0h7v7h-7V4ZM4 13h7v7H4v-7Zm9 0h7v7h-7v-7Z"
                                            />
                                        </svg>

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

                                <!-- Статус -->
                                <div
                                    class="text-center text-[11px]
                                       font-semibold
                                       text-fuchsia-700
                                       dark:text-fuchsia-300"
                                >
                                    <span>{{ t('status') }}: </span>
                                    {{ getStatusLabel(product.status) }}
                                </div>

                                <!-- Модерация -->
                                <div class="flex justify-center space-x-1">
                                <span
                                    class="rounded-sm border
                                           px-2 py-1
                                           text-[10px] font-semibold"
                                    :class="moderationBadge(product.moderation_status).class"
                                    :title="product.moderation_note && product.moderated_at
                                ? `${product.moderation_note} [${formatDate(product.moderated_at)}]`
                                : null"
                                >
                                    {{ moderationBadge(product.moderation_status).text }}
                                </span>

                                    <ModerationButton
                                        :isAdmin="isAdmin"
                                        :status="product?.moderation_status ?? 0"
                                        :initialNote="product?.moderation_note || ''"
                                        mode="toggle"
                                        @submit="({ status, note }) =>
                                            $emit('approve', product, status, note)"
                                    />
                                </div>

                            </div>
                        </div>

                        <!-- Действия -->
                        <div
                            class="flex items-center justify-center px-3 py-2
                                   border-t border-dashed border-slate-400 dark:border-slate-500">
                            <div class="flex flex-wrap items-center justify-center gap-1">
                                <div class="flex flex-row items-center space-x-7">
                                    <div class="flex items-center space-x-1">
                                        <LeftToggle
                                            :isActive="product.left"
                                            :title="product.left
                                            ? t('enabled')
                                            : t('disabled')"
                                            @toggle-left="$emit('toggle-left', product)"
                                        />
                                        <MainToggle
                                            :isActive="product.main"
                                            :title="product.main
                                            ? t('enabled')
                                            : t('disabled')"
                                            @toggle-main="$emit('toggle-main', product)"
                                        />
                                        <RightToggle
                                            :isActive="product.right"
                                            :title="product.right
                                            ? t('enabled')
                                            : t('disabled')"
                                            @toggle-right="$emit('toggle-right', product)"
                                        />
                                    </div>
                                    <div class="flex flex-row items-center space-x-1">
                                        <NewButtonToggle
                                            :isActive="product.is_new"
                                            @toggle-is-new="$emit('toggle-is-new', product)"
                                            :title="product.is_new ? t('enabled') : t('disabled')"
                                        />
                                        <HitButtonToggle
                                            :isActive="product.is_hit"
                                            @toggle-is-hit="$emit('toggle-is-hit', product)"
                                            :title="product.is_hit ? t('enabled') : t('disabled')"
                                        />
                                        <SaleButtonToggle
                                            :isActive="product.is_sale"
                                            @toggle-is-sale="$emit('toggle-is-sale', product)"
                                            :title="product.is_sale ? t('enabled') : t('disabled')"
                                        />
                                    </div>
                                </div>
                                <div class="flex items-center space-x-1">
                                    <ActivityToggle
                                        :isActive="product.activity"
                                        :title="
                                        product.activity ? t('enabled') : t('disabled')"
                                        @toggle-activity="
                                        $emit('toggle-activity', product)"
                                    />

                                    <IconEdit
                                        :href="route('admin.marketProducts.edit',
                                        {marketProduct: product.id,})"
                                    />

                                    <AddButton
                                        :href="route('admin.marketProductVariants.create', {
                                            market_product_id: product.id,
                                        })"
                                        :title="t('addMarketProductVariant')"
                                        class="py-1"
                                    >
                                    </AddButton>

                                    <DeleteIconButton
                                        @delete="
                                        $emit(
                                            'delete',
                                            product
                                        )
                                    "
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
            class="p-5 text-center
                   text-slate-700 dark:text-slate-100"
        >
            {{ t('noData') }}
        </div>
    </div>
</template>
