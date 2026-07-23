<script setup>
import { defineEmits, defineProps, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import ActivityToggle from '@/Components/Admin/UI/Buttons/ActivityToggle.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'
import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import ModerationButton from '@/Components/Admin/UI/Buttons/ModerationButton.vue'

const { t, locale } = useI18n()

const props = defineProps({
    variants: {
        type: Array,
        default: () => [],
    },

    selectedVariants: {
        type: Array,
        default: () => [],
    },

    isAdmin: {
        type: Boolean,
        default: false,
    },
})

const emits = defineEmits([
    'toggle-activity',
    'delete',
    'update-sort-order',
    'toggle-select',
    'toggle-all',
    'approve',
    'make-default',
])

/* ======================== Local state ======================== */

const localVariants = ref([])
const openedDetails = ref([])

watch(
    () => props.variants,
    (newValue) => {
        localVariants.value = JSON.parse(JSON.stringify(newValue || []))
    },
    {
        immediate: true,
        deep: true,
    }
)

/* ======================== Drag and drop ======================== */

const handleDragEnd = () => {
    emits('update-sort-order', localVariants.value.map((variant) => variant.id))
}

/* ======================== Selection ======================== */

const normalizedSelectedIds = () => {
    return props.selectedVariants.map((id) => Number(id))
}

const isSelected = (variantId) => {
    return normalizedSelectedIds().includes(Number(variantId))
}

const toggleAll = (event) => {
    emits('toggle-all', Boolean(event?.target?.checked))
}

const allSelected = () => {
    return localVariants.value.length > 0 && localVariants.value.every((variant) => isSelected(variant.id))
}

/* ======================== Translations ======================== */

const variantTranslation = (variant) => {
    return variant?.translation || variant?.translations?.[0] || {}
}

const variantTitle = (variant) => {
    return variant?.display_title ||
        variantTranslation(variant)?.title || variant?.code || variant?.sku || `ID: ${variant?.id}`
}

const variantSubtitle = (variant) => {
    return variantTranslation(variant)?.subtitle || ''
}

const variantShort = (variant) => {
    return variantTranslation(variant)?.short || ''
}

const relationTitle = (relation) => {
    return relation?.title ||
        relation?.display_title ||
        relation?.translation?.title || relation?.translations?.[0]?.title || ''
}

const productTitle = (variant) => {
    return relationTitle(variant?.product) || `ID: ${variant?.market_product_id}`
}

/* ======================== Details ======================== */

const isDetailsOpen = (variantId) => {
    return openedDetails.value.includes(Number(variantId))
}

const toggleDetails = (variantId) => {
    const id = Number(variantId)

    if (isDetailsOpen(id)) {
        openedDetails.value = openedDetails.value.filter((itemId) => itemId !== id)
        return
    }

    openedDetails.value.push(id)
}

/* ======================== Images ======================== */

const getPrimaryImage = (variant) => {
    if (!Array.isArray(variant?.images) || !variant.images.length) {
        return null
    }

    return [...variant.images].sort((left, right) => {
        const leftOrder = Number(left?.pivot?.order ?? left?.order ?? 0)
        const rightOrder = Number(right?.pivot?.order ?? right?.order ?? 0)

        return leftOrder - rightOrder
    })[0]
}

const imageUrl = (variant) => {
    const image = getPrimaryImage(variant)

    return image?.webp_url ||
        image?.image_url ||
        image?.thumb_url ||
        image?.url || '/storage/market/market_product_variant_images/default-image.png'
}

const imageAlt = (variant) => {
    return getPrimaryImage(variant)?.alt || variantTitle(variant)
}

const imageTitle = (variant) => {
    return getPrimaryImage(variant)?.caption || variantTitle(variant)
}

/* ======================== Attributes ======================== */

const variantValues = (variant) => {
    return Array.isArray(variant?.values) ? variant.values : []
}

const valueTitle = (item) => {
    return item?.display_value ||
        item?.value_title ||
        item?.attribute_value?.title ||
        item?.attribute_value?.translation?.title || item?.attribute_value?.code || ''
}

const attributeColor = (item) => {
    return item?.attribute_value?.color || item?.attribute?.color || null
}

const valuesTooltip = (variant) => {
    return variantValues(variant).map((item) => valueTitle(item)).filter(Boolean).join('\n')
}

/* ======================== Currency and money ======================== */

const effectiveCurrency = (variant) => {
    return variant?.effective_currency || variant?.currency || variant?.product?.currency || null
}

const safeNumber = (value) => {
    const number = Number(value)

    return Number.isFinite(number) ? number : 0
}

const formatMoney = (value, currency) => {
    if (value === null || value === undefined || value === '') {
        return '—'
    }

    const amount = safeNumber(value)
    const precision = Number.isFinite(Number(currency?.precision)) ? Number(currency.precision) : 2
    const thousandsSeparator = currency?.thousands_sep ?? ' '
    const decimalSeparator = currency?.decimal_sep ?? '.'
    const parts = amount.toFixed(precision).split('.')
    const integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandsSeparator)
    const formattedAmount = precision > 0 ? `${integerPart}${decimalSeparator}${parts[1]}` :
        integerPart
    const symbol = String(currency?.symbol || currency?.code || '').trim()

    if (!symbol) {
        return formattedAmount
    }

    return currency?.symbol_first ? `${symbol}${formattedAmount}` : `${formattedAmount} ${symbol}`
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
            class: 'bg-emerald-100 text-emerald-700 border-emerald-300 ' +
                'dark:bg-emerald-900/40 dark:text-emerald-300 dark:border-emerald-700',
        }
    }

    if (value === 2) {
        return {
            text: t('statusSelectRejected'),
            class: 'bg-rose-100 text-rose-700 border-rose-300 ' +
                'dark:bg-rose-900/40 dark:text-rose-300 dark:border-rose-700',
        }
    }

    return {
        text: t('underModeration'),
        class: 'bg-amber-100 text-amber-800 border-amber-300 ' +
            'dark:bg-amber-900/40 dark:text-amber-300 dark:border-amber-700',
    }
}

/* ======================== Formatting ======================== */

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

const truncateText = (text, maxLength = 110) => {
    if (!text) {
        return ''
    }

    const value = String(text)

    return value.length > maxLength ? `${value.slice(0, maxLength).trimEnd()}…` : value
}

const formatDimension = (value, unit = '') => {
    if (value === null || value === undefined || value === '') {
        return '—'
    }

    return `${value}${unit ? ` ${unit}` : ''}`
}

const publicationTitle = (variant) => {
    const values = []

    if (variant?.published_at) {
        values.push(`${t('publishedAt')}: ${formatDate(variant.published_at)}`)
    }

    if (variant?.show_from_at) {
        values.push(`${t('showFromAt')}: ${formatDate(variant.show_from_at)}`)
    }

    if (variant?.show_to_at) {
        values.push(`${t('showToAt')}: ${formatDate(variant.show_to_at)}`)
    }

    return values.join('\n')
}
</script>

<template>
    <div class="relative rounded-sm border border-slate-400 dark:border-slate-500
                bg-white dark:bg-slate-700 shadow-lg">
        <!-- Панель выбора -->
        <div class="flex items-center justify-between
                    border-b border-slate-400 dark:border-slate-500 px-3 py-2">
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('selected') }}: {{ selectedVariants.length }}
            </div>

            <label v-if="localVariants.length"
                   class="flex cursor-pointer items-center text-xs
                          text-slate-600 dark:text-slate-200">
                <span>{{ t('selectAll') }}</span>
                <input type="checkbox" class="mx-2" :checked="allSelected()" @change="toggleAll" />
            </label>
        </div>

        <div v-if="localVariants.length" class="p-3">
            <draggable
                v-model="localVariants"
                tag="div"
                item-key="id"
                handle=".handle"
                class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                @end="handleDragEnd"
            >
                <template #item="{ element: variant }">
                    <article class="relative flex h-full flex-col rounded-md
                                    border border-slate-400 dark:border-slate-500
                                    bg-slate-50/70 dark:bg-slate-800/80 shadow-sm
                                    transition-shadow duration-150 hover:shadow-md">

                        <!-- Шапка карточки -->
                        <header class="flex items-center justify-between
                                       border-b border-dashed border-slate-400
                                       dark:border-slate-500 px-2 py-1">
                            <div class="flex items-center space-x-2">
                                <button type="button"
                                        class="handle cursor-move text-slate-400
                                               hover:text-slate-700 dark:hover:text-slate-100"
                                        :title="t('dragDrop')">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" />
                                    </svg>
                                </button>

                                <div class="rounded-sm border border-gray-400
                                            bg-slate-200 dark:bg-slate-700 px-1.5 py-0.5
                                            text-[10px] font-semibold text-slate-800
                                            dark:text-blue-100"
                                     :title="`sort: ${variant.sort}`">
                                    ID: {{ variant.id }}
                                </div>

                                <button type="button"
                                        class="text-slate-400 hover:text-blue-600
                                               dark:hover:text-blue-300"
                                        :title="isDetailsOpen(variant.id) ? t('hideDetails') : t('showDetails')"
                                        @click.prevent="toggleDetails(variant.id)">
                                    <svg class="h-4 w-4 transition-transform duration-200"
                                         :class="{ 'rotate-180': isDetailsOpen(variant.id) }"
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>

                            <div class="flex items-center space-x-2">
                                <div class="rounded-sm border px-2 py-0.5
                                             text-[10px] font-semibold"
                                     :class="moderationBadge(variant.moderation_status).class">
                                    {{ moderationBadge(variant.moderation_status).text }}
                                </div>

                                <input
                                    type="checkbox"
                                    :checked="isSelected(variant.id)"
                                    @change="emits('toggle-select', variant.id)" />
                            </div>
                        </header>

                        <!-- Раскрываемый блок кодов -->
                        <div
                            v-show="isDetailsOpen(variant.id)"
                            class="border-b border-dashed border-slate-300 bg-slate-100/80 px-2 py-2 text-[10px] text-slate-600 dark:border-slate-600 dark:bg-slate-900/40 dark:text-slate-300"
                        >
                            <div class="grid grid-cols-1 gap-1 sm:grid-cols-2">
                                <div
                                    v-if="variant.code"
                                    class="rounded-sm border border-slate-400 bg-white px-1.5 py-1 dark:bg-slate-800"
                                    :title="t('code')"
                                >
                                    <span class="font-semibold">{{ t('code') }}:</span>
                                    {{ truncateText(variant.code, 50) }}
                                </div>

                                <div
                                    v-if="variant.sku"
                                    class="rounded-sm border border-blue-400 bg-blue-100 px-1.5 py-1 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300"
                                    :title="t('sku')"
                                >
                                    <span class="font-semibold">SKU:</span>
                                    {{ truncateText(variant.sku, 50) }}
                                </div>

                                <div
                                    v-if="variant.vendor_code"
                                    class="rounded-sm border border-violet-400 bg-violet-100 px-1.5 py-1 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300"
                                    :title="t('vendorCode')"
                                >
                                    <span class="font-semibold">{{ t('vendorCode') }}:</span>
                                    {{ truncateText(variant.vendor_code, 50) }}
                                </div>

                                <div
                                    v-if="variant.barcode"
                                    class="flex items-center gap-1 rounded-sm border border-slate-400 bg-slate-200 px-1.5 py-1 text-slate-700 dark:bg-slate-800 dark:text-slate-300"
                                    :title="t('barcode')"
                                >
                                    <svg class="h-5 w-5 shrink-0 fill-current" viewBox="0 0 512 512">
                                        <path d="M0 448V64h18v384H0zm26.857-.273V64H36v383.727h-9.143zm27.143 0V64h8.857v383.727H54zm44.857 0V64h8.857v383.727h-8.857zm36 0V64h17.714v383.727h-17.714zm44.857 0V64h8.857v383.727h-8.857zm18 0V64h8.857v383.727h-8.857zm18 0V64h8.857v383.727h-8.857zm35.715 0V64h18v383.727h-18zm44.857 0V64h18v383.727h-18zm35.999 0V64h18.001v383.727h-18.001zm36.001 0V64h18.001v383.727h-18.001zm26.857 0V64h18v383.727h-18zm45.143 0V64h26.857v383.727h-26.857zm35.714 0V64h9.143v383.727H476zm18 .273V64h18v384h-18z" />
                                    </svg>

                                    <span class="min-w-0">
                                        <span class="font-semibold">{{ t('barcode') }}:</span>
                                        {{ truncateText(variant.barcode, 50) }}
                                    </span>
                                </div>
                            </div>

                            <div
                                v-if="!variant.code && !variant.sku && !variant.vendor_code && !variant.barcode"
                                class="text-center text-slate-400"
                            >
                                {{ t('noData') }}
                            </div>
                        </div>

                        <!-- Изображение -->
                        <div class="relative w-full overflow-hidden bg-slate-200 dark:bg-slate-900">
                            <img
                                :src="imageUrl(variant)"
                                :alt="imageAlt(variant)"
                                :title="imageTitle(variant)"
                                class="aspect-[4/3] w-full object-cover" />

                            <div class="absolute left-2 top-2 flex flex-wrap gap-1">
                                <span v-if="variant.has_own_price"
                                      class="rounded-sm border border-violet-300
                                             bg-violet-100/95 px-1.5 py-0.5 text-[9px]
                                             font-semibold text-violet-700
                                             dark:border-violet-700 dark:bg-violet-900/80
                                             dark:text-violet-300">
                                    {{ t('ownPrice') }}
                                </span>

                                <span v-if="variant.has_own_currency"
                                      class="rounded-sm border border-cyan-300
                                             bg-cyan-100/95 px-1.5 py-0.5 text-[9px]
                                             font-semibold text-cyan-700 dark:border-cyan-700
                                             dark:bg-cyan-900/80 dark:text-cyan-300">
                                    {{ t('ownCurrency') }}
                                </span>

                                <span v-if="variant.has_own_dimensions"
                                      class="rounded-sm border border-orange-300 bg-orange-100/95
                                             px-1.5 py-0.5 text-[9px] font-semibold
                                             text-orange-700 dark:border-orange-700
                                             dark:bg-orange-900/80 dark:text-orange-300">
                                    {{ t('ownDimensions') }}
                                </span>
                            </div>

                            <div v-if="variant.images_count"
                                 class="absolute bottom-2 right-2 rounded-sm bg-black/65
                                        px-1.5 py-0.5 text-[10px] font-semibold text-white">
                                {{ variant.images_count }}
                            </div>
                        </div>

                        <!-- Основная информация -->
                        <div class="flex flex-1 flex-col px-3 py-2">
                            <div class="mb-1 text-center text-[10px]
                                        font-semibold uppercase tracking-wide
                                        text-blue-600 dark:text-blue-300"
                                 :title="productTitle(variant)">
                                {{ truncateText(productTitle(variant), 70) }}
                            </div>

                            <h3 class="text-center text-xs font-semibold
                                       text-sky-700 dark:text-sky-300"
                                :title="variantTitle(variant)">
                                {{ variantTitle(variant) }}
                            </h3>

                            <div v-if="variantSubtitle(variant)"
                                 class="mt-0.5 line-clamp-1 text-center text-[10px]
                                        text-slate-600 dark:text-slate-300"
                                 :title="variantSubtitle(variant)">
                                {{ variantSubtitle(variant) }}
                            </div>

                            <p v-if="variantShort(variant)"
                               class="my-2 line-clamp-3 text-center text-[11px]
                                      leading-4 text-slate-600 dark:text-slate-300"
                               :title="variantShort(variant)">
                                {{ truncateText(variantShort(variant)) }}
                            </p>

                            <!-- Характеристики варианта -->
                            <div v-if="variantValues(variant).length"
                                 class="mt-2 flex flex-wrap justify-center gap-1"
                                 :title="valuesTooltip(variant)">

                                <span v-for="item in variantValues(variant)"
                                      :key="item.id"
                                      class="inline-flex max-w-full items-center rounded-sm
                                             border border-slate-300 bg-white px-1.5 py-0.5
                                             text-[10px] text-slate-700 dark:border-slate-600
                                             dark:bg-slate-700 dark:text-slate-200">

                                    <span v-if="attributeColor(item)"
                                          class="mr-1 h-2.5 w-2.5 shrink-0 rounded-full
                                                 border border-slate-400"
                                          :style="{ backgroundColor: attributeColor(item) }"></span>
                                    <span class="truncate">{{ valueTitle(item) }}</span>

                                </span>
                            </div>

                            <!-- Цена -->
                            <div class="mt-1 text-center">

                                <div class="text-base font-bold
                                            text-teal-700 dark:text-teal-300">
                                    {{ formatMoney(variant.effective_price, effectiveCurrency(variant)) }}
                                </div>

                                <div v-if="variant.effective_old_price"
                                     class="text-sm text-slate-400 line-through">
                                    {{ formatMoney(variant.effective_old_price, effectiveCurrency(variant)) }}
                                </div>

                                <div v-if="variant.effective_wholesale_price"
                                     class="mt-0.5 text-[10px] font-semibold">
                                    {{ t('wholesalePrice') }}:
                                    <span class="text-indigo-600 dark:text-indigo-300">
                    {{ formatMoney(variant.effective_wholesale_price, effectiveCurrency(variant)) }}
                                    </span>
                                </div>

                            </div>

                            <!-- Остаток -->
                            <div class="mt-2 flex items-center justify-center gap-2
                                        text-[11px] font-semibold">
                                <span
                                    :class="variant.in_stock && variant.has_stock ?
                                    'text-emerald-700 dark:text-emerald-300' :
                                    'text-rose-700 dark:text-rose-300'">
                        {{ variant.in_stock && variant.has_stock ? t('inStock') : t('outOfStock') }}
                                </span>
                                <span class="text-slate-600 dark:text-slate-300">
                                    {{ t('quantity') }}: {{ variant.quantity ?? 0 }}
                                </span>
                            </div>

                            <!-- Раскрываемые детали -->
                            <div v-show="isDetailsOpen(variant.id)"
                                 class="mt-3 space-y-2 border-t border-dashed border-slate-300
                                        pt-2 text-[10px] text-slate-600 dark:border-slate-600
                                        dark:text-slate-300">
                                <div class="grid grid-cols-2 gap-1">
                                    <div>
                                        {{ t('weight') }}:
                                        {{ formatDimension(variant.effective_weight, 'kg') }}
                                    </div>
                                    <div>
                                        {{ t('length') }}:
                                        {{ formatDimension(variant.effective_length, 'mm') }}
                                    </div>
                                    <div>
                                        {{ t('width') }}:
                                        {{ formatDimension(variant.effective_width, 'mm') }}
                                    </div>
                                    <div>
                                        {{ t('height') }}:
                                        {{ formatDimension(variant.effective_height, 'mm') }}
                                    </div>
                                </div>

                                <div
                                    v-if="variant.show_from_at ||
                                    variant.show_to_at ||
                                    variant.published_at"
                                    class="text-center"
                                    :title="publicationTitle(variant)">
                                    <div v-if="variant.published_at">
                                        {{ t('publishedAt') }}:
                                        {{ formatDate(variant.published_at) }}
                                    </div>
                                    <div v-if="variant.show_from_at">
                                        {{ t('showFromAt') }}:
                                        {{ formatDate(variant.show_from_at) }}
                                    </div>
                                    <div v-if="variant.show_to_at">
                                        {{ t('showToAt') }}:
                                        {{ formatDate(variant.show_to_at) }}
                                    </div>
                                </div>

                                <div v-if="variant.moderator" class="text-center">
                                    {{ t('moderator') }}:
                                    {{ variant.moderator.name || variant.moderator.email || `ID: ${variant.moderated_by}` }}
                                </div>

                                <div class="flex justify-center gap-3">
                                    <span>{{ t('images') }}: {{ variant.images_count ?? 0 }}</span>
                                    <span>
                                        {{ t('attributes') }}:
                                        {{ variant.values_count ?? variantValues(variant).length }}
                                    </span>
                                </div>
                            </div>

                            <!-- Default -->
                            <div class="flex justify-center items-center my-1">
                                <span v-if="variant.is_default"
                                      class="rounded-sm text-wrap
                                             font-semibold text-[10px]
                                             text-yellow-700 dark:text-yellow-200">
                                    {{ t('basicProductVariant') }}
                                </span>
                                <span v-else class="text-[9px] text-slate-400">
                                    —
                                </span>
                            </div>

                            <!-- Статусы -->
                            <div class="mt-auto">
                                <div class="text-center text-[11px] font-semibold
                                            text-fuchsia-700 dark:text-fuchsia-300">
                                    {{ t('status') }}: {{ getStatusLabel(variant.status) }}
                                </div>

                                <div class="mt-2 flex items-center justify-center space-x-1">
                                    <span class="rounded-sm border px-2 py-1
                                                 text-[10px] font-semibold"
                                          :class="moderationBadge(variant.moderation_status).class"
                                          :title="variant.moderation_note && variant.moderated_at ? `${variant.moderation_note} [${formatDate(variant.moderated_at)}]` : null">
                                        {{ moderationBadge(variant.moderation_status).text }}
                                    </span>

                                    <ModerationButton
                                        :isAdmin="isAdmin"
                                        :status="variant?.moderation_status ?? 0"
                                        :initialNote="variant?.moderation_note || ''"
                                        mode="toggle"
                                        @submit="({ status, note }) =>
                                        emits('approve', variant, status, note)"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Действия -->
                        <footer class="flex items-center justify-center
                                       border-t border-dashed border-slate-400
                                       dark:border-slate-500 px-3 py-2">
                            <div class="flex items-center space-x-1">

                                <button
                                    type="button"
                                    class="inline-flex h-7 w-7 items-center justify-center
                                           rounded-sm border transition"
                                    :class="variant.is_default
                                    ? 'cursor-default border-amber-400 bg-violet-100 ' +
                                     'text-amber-600 dark:bg-violet-900/40 dark:text-amber-300'
                                    : 'border-slate-400 bg-white text-slate-400 ' +
                                     'hover:border-amber-400 hover:text-amber-500 dark:bg-slate-800'"
                                    :disabled="variant.is_default"
                                    :title="variant.is_default
                                        ? t('defaultVariant')
                                        : t('makePrimary')"
                                    @click="emits('make-default', variant)"
                                >
                                    <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24">
                                        <path d="m12 2.5 2.93 5.94 6.56.95-4.75 4.63 1.12 6.54L12 17.48l-5.86 3.08 1.12-6.54-4.75-4.63 6.56-.95L12 2.5Z" />
                                    </svg>
                                </button>

                                <ActivityToggle
                                    :isActive="variant.activity"
                                    :title="variant.activity ? t('enabled') : t('disabled')"
                                    @toggle-activity="emits('toggle-activity', variant)"
                                />

                                <IconEdit
                                    :href="route('admin.marketProductVariants.edit',
                                    { marketProductVariant: variant.id })"
                                />

                                <DeleteIconButton @delete="emits('delete', variant)" />
                            </div>
                        </footer>
                    </article>
                </template>
            </draggable>
        </div>

        <div v-else class="px-3 py-10 text-center text-sm text-slate-500 dark:text-slate-300">
            {{ t('noData') }}
        </div>
    </div>
</template>
