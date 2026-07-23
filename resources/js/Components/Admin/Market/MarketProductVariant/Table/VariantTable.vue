<script setup>
import { defineEmits, defineProps, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import ActivityToggle from '@/Components/Admin/UI/Buttons/ActivityToggle.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'
import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import ModerationButton from '@/Components/Admin/UI/Buttons/ModerationButton.vue'

const { t, locale } = useI18n()

/* ======================== Props ======================== */

const props = defineProps({
    variants: {
        type: Array,
        default: () => []
    },

    selectedVariants: {
        type: Array,
        default: () => []
    },

    allSelected: {
        type: Boolean,
        default: false
    },

    isAdmin: {
        type: Boolean,
        default: false
    }
})

/* ======================== Emits ======================== */

const emit = defineEmits([
    'toggle-activity',
    'delete',
    'update-sort-order',
    'toggle-select',
    'toggle-all',
    'approve',
    'make-default',
])

/* ======================== Local variants ======================== */

const localVariants = ref([])

watch(
    () => props.variants,
    (newValue) => {
        localVariants.value = JSON.parse(JSON.stringify(newValue || []))
    },
    {
        immediate: true,
        deep: true
    }
)

/* ======================== Drag and drop ======================== */

const handleDragEnd = () => {
    emit(
        'update-sort-order',
        localVariants.value.map((variant) => variant.id)
    )
}

/* ======================== Selection ======================== */

const normalizedSelectedIds = () => {
    return props.selectedVariants.map((id) => Number(id))
}

const isSelected = (variantId) => {
    return normalizedSelectedIds().includes(Number(variantId))
}

const toggleAll = (event) => {
    emit('toggle-all', Boolean(event?.target?.checked))
}

const allVariantsSelected = () => {
    if (!localVariants.value.length) {
        return false
    }

    return localVariants.value.every((variant) => isSelected(variant.id))
}

/* ======================== Translations ======================== */

const variantTranslation = (variant) => {
    return variant?.translation || variant?.translations?.[0] || {}
}

const variantTitle = (variant) => {
    return variant?.display_title || variantTranslation(variant)?.title || variant?.code || variant?.sku || `ID: ${variant?.id}`
}

const variantSubtitle = (variant) => {
    return variantTranslation(variant)?.subtitle || ''
}

const variantShort = (variant) => {
    return variantTranslation(variant)?.short || ''
}

const relationTitle = (relation) => {
    return relation?.title || relation?.display_title || relation?.translation?.title || relation?.translations?.[0]?.title || ''
}

const productTitle = (variant) => {
    return relationTitle(variant?.product) || `ID: ${variant?.market_product_id}`
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

    return image?.webp_url || image?.thumb_url || image?.image_url || image?.url || '/storage/market/market_product_variant_images/default-image.png'
}

const imageAlt = (variant) => {
    return getPrimaryImage(variant)?.alt || variantTitle(variant)
}

const imageTitle = (variant) => {
    return getPrimaryImage(variant)?.caption || variantTitle(variant)
}

/* ======================== Attribute values ======================== */

const variantValues = (variant) => {
    return Array.isArray(variant?.values) ? variant.values : []
}

const valueTitle = (item) => {
    return item?.display_value || item?.value_title || item?.attribute_value?.title || item?.attribute_value?.translation?.title || item?.attribute_value?.code || ''
}

const attributeColor = (item) => {
    return item?.attribute_value?.color || item?.attribute?.color || null
}

const valuesTooltip = (variant) => {
    return variantValues(variant)
        .map((item) => valueTitle(item))
        .filter(Boolean)
        .join('\n')
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
    const formattedAmount = precision > 0 ? `${integerPart}${decimalSeparator}${parts[1]}` : integerPart
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
    archived: 'statusArchived'
}

const getStatusLabel = (status) => {
    return t(statusLabelKeyMap[status] || status || 'no')
}

const statusBadge = (status) => {
    if (status === 'published') {
        return {
            text: getStatusLabel(status),
            class: 'bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300 dark:border-emerald-700'
        }
    }

    if (status === 'archived') {
        return {
            text: getStatusLabel(status),
            class: 'bg-slate-200 text-slate-700 border-slate-400 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-600'
        }
    }

    return {
        text: getStatusLabel(status),
        class: 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-900/40 dark:text-amber-300 dark:border-amber-700'
    }
}

const moderationBadge = (status) => {
    const value = Number(status ?? 0)

    if (value === 1) {
        return {
            text: t('statusSelectApproved'),
            class: 'bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300 dark:border-emerald-700'
        }
    }

    if (value === 2) {
        return {
            text: t('statusSelectRejected'),
            class: 'bg-rose-100 text-rose-700 border-rose-300 dark:bg-rose-900/40 dark:text-rose-300 dark:border-rose-700'
        }
    }

    return {
        text: t('underModeration'),
        class: 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-900/40 dark:text-amber-300 dark:border-amber-700'
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
        day: 'numeric'
    })
}

const truncateText = (text, maxLength = 60) => {
    if (!text) {
        return ''
    }

    const value = String(text)

    return value.length > maxLength ? `${value.slice(0, maxLength).trimEnd()}…` : value
}

const publicationTitle = (variant) => {
    const values = [
        `Sort: ${variant?.sort ?? 0}`
    ]

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

const dimensionsTitle = (variant) => {
    return [
        `${t('weight')}: ${variant?.effective_weight ?? '—'} kg`,
        `${t('length')}: ${variant?.effective_length ?? '—'} mm`,
        `${t('width')}: ${variant?.effective_width ?? '—'} mm`,
        `${t('height')}: ${variant?.effective_height ?? '—'} mm`
    ].join('\n')
}
</script>

<template>
    <div class="relative rounded-sm border border-slate-200 dark:border-slate-600
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
                <input type="checkbox" class="mx-2" :checked="allVariantsSelected() || allSelected"
                       @change="toggleAll" />
            </label>
        </div>

        <div class="overflow-x-auto">
            <table v-if="localVariants.length"
                   class="table-auto w-full text-slate-700 dark:text-slate-100">
                <thead
                    class="border border-solid border-gray-300 dark:border-gray-700
                           bg-slate-200 dark:bg-cyan-900 text-xs uppercase">
                <tr>
                    <th class="w-px px-1 py-3">
                        <svg class="h-4 w-4 fill-current text-slate-800 dark:text-slate-200"
                             viewBox="0 0 24 24">
                            <path
                                d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z" />
                            <path
                                d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z" />
                        </svg>
                    </th>
                    <th class="w-px whitespace-nowrap px-1 py-3">
                        {{ t('id') }}
                    </th>
                    <th class="w-px px-1 py-3">
                        <div class="flex justify-center" title="Изображение">
                            <svg class="h-6 w-6 shrink-0 fill-current"
                                 viewBox="0 0 512 512">
                                <path d="M0 96C0 60.7 28.7 32 64 32h384c35.3 0 64 28.7 64 64v320c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V96zm323.8 106.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4S78.8 416 88 416h336c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z" ></path>
                            </svg>
                        </div>
                    </th>
                    <th class="min-w-72 px-2 py-3 text-left">
                        {{ t('marketProductVariant') }}
                    </th>
                    <th class="min-w-52 px-2 py-3 text-left">
                        {{ t('attributes') }}
                    </th>
                    <th class="whitespace-nowrap px-2 py-3 text-right">
                        {{ t('price') }}
                    </th>
                    <th class="whitespace-nowrap px-2 py-3 text-center">
                        {{ t('quantity') }}
                    </th>
                    <th class="whitespace-nowrap px-2 py-3 text-center">
                        {{ t('actions') }}
                    </th>
                    <th class="w-px whitespace-nowrap px-1 py-1 text-center">
                        <input
                            type="checkbox"
                            :checked="allVariantsSelected() || allSelected" @change="toggleAll" />
                    </th>
                </tr>
                </thead>

                <draggable
                    v-model="localVariants"
                    tag="tbody"
                    item-key="id"
                    handle=".handle"
                    @end="handleDragEnd"
                >
                    <template #item="{ element: variant }">
                        <tr class="border-b-2 text-xs font-semibold
                                   hover:bg-slate-100 dark:hover:bg-cyan-800">

                            <!-- Drag handle -->
                            <td class="handle w-px cursor-move px-1 py-1 text-center">
                                <svg class="h-4 w-4 text-gray-500 dark:text-gray-300"
                                     fill="currentColor"
                                     viewBox="0 0 20 20">
                                    <path
                                        d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" />
                                </svg>
                            </td>

                            <!-- ID -->
                            <td class="w-px whitespace-nowrap px-1 py-1
                                       text-center text-blue-600 dark:text-blue-200"
                                :title="publicationTitle(variant)">
                                {{ variant.id }}
                            </td>

                            <!-- Image -->
                            <td class="w-16 px-1 py-1">
                                <div class="flex justify-center">
                                    <img
                                        :src="imageUrl(variant)"
                                        :alt="imageAlt(variant)"
                                        :title="imageTitle(variant)"
                                        class="h-10 w-14 rounded-sm border border-slate-300
                                               object-cover dark:border-slate-600"
                                    />
                                </div>

                                <div v-if="variant.images_count"
                                     class="mt-0.5 text-center text-[9px] text-slate-500 dark:text-slate-300">
                                    {{ variant.images_count }}
                                </div>
                            </td>

                            <!-- Variant -->
                            <td class="px-2 py-1">
                                <div class="text-left">
                                    <div
                                        class="text-[10px] font-semibold uppercase
                                               text-blue-600 dark:text-blue-300"
                                        :title="productTitle(variant)">
                                        {{ truncateText(productTitle(variant), 75) }}
                                    </div>

                                    <div class="mt-0.5 text-xs font-semibold
                                                text-sky-700 dark:text-sky-300"
                 :title="variantShort(variant) || variantSubtitle(variant) || variantTitle(variant)">
                                        {{ truncateText(variantTitle(variant), 85) }}
                                    </div>

                                    <div v-if="variantSubtitle(variant)"
                                         class="mt-0.5 text-[10px]
                                                text-slate-600 dark:text-slate-300">
                                        {{ truncateText(variantSubtitle(variant), 75) }}
                                    </div>

                                    <div class="mt-1 flex flex-wrap gap-1 text-[9px]">

                                        <!-- Код -->
                                        <div v-if="variant.code"
                                              class="rounded-sm border border-slate-400
                                                     bg-slate-100 px-1 py-0.5 dark:bg-slate-800"
                                              :title="t('code')">
                                            {{ truncateText(variant.code, 25) }}
                                        </div>

                                        <!-- Баркод -->
                                        <div
                                            v-if="variant.barcode"
                                            class="mt-0.5 flex items-center gap-1 text-[9px]"
                                        >
                                            <svg class="h-5 w-5 shrink-0 fill-current"
                                                 viewBox="0 0 512 512">
                                                <path d="M0 448V64h18v384H0zm26.857-.273V64H36v383.727h-9.143zm27.143 0V64h8.857v383.727H54zm44.857 0V64h8.857v383.727h-8.857zm36 0V64h17.714v383.727h-17.714zm44.857 0V64h8.857v383.727h-8.857zm18 0V64h8.857v383.727h-8.857zm18 0V64h8.857v383.727h-8.857zm35.715 0V64h18v383.727h-18zm44.857 0V64h18v383.727h-18zm35.999 0V64h18.001v383.727h-18.001zm36.001 0V64h18.001v383.727h-18.001zm26.857 0V64h18v383.727h-18zm45.143 0V64h26.857v383.727h-26.857zm35.714 0V64h9.143v383.727H476zm18 .273V64h18v384h-18z" />
                                            </svg>
                                            <span
                                                v-if="variant.barcode"
                                                class="rounded-sm
                                                   border border-slate-400
                                                   bg-slate-200 px-1 py-0.5
                                                   text-slate-700
                                                   dark:bg-slate-800
                                                   dark:text-slate-300"
                                                :title="t('barcode')"
                                            >
                                            {{ truncateText(variant.barcode, 24) }}
                                        </span>
                                        </div>

                                        <div v-if="variant.sku"
                                              class="rounded-sm border border-blue-400 bg-blue-100
                                                     px-1 py-0.5 text-blue-700 dark:bg-blue-900/40
                                                     dark:text-blue-300"
                                              :title="t('sku')">
                                            SKU: {{ truncateText(variant.sku, 50) }}
                                        </div>

                                        <div v-if="variant.vendor_code"
                                              class="rounded-sm border border-violet-400
                                                     bg-violet-100 px-1 py-0.5 text-violet-700
                                                     dark:bg-violet-900/40 dark:text-violet-300"
                                              :title="t('vendorCode')">
                                            {{ truncateText(variant.vendor_code, 25) }}
                                        </div>
                                    </div>

                                    <div class="mt-1 flex flex-wrap gap-1 text-[10px]">
                                        <span v-if="variant.has_own_price"
                                              class="rounded-sm px-1 py-0.5
                                                     text-fuchsia-700 dark:text-fuchsia-300">
                                            {{ t('ownPrice') }}
                                        </span>

                                        <span v-if="variant.has_own_currency"
                                              class="rounded-sm border border-cyan-300 bg-cyan-100
                                                     px-1 py-0.5 text-cyan-700 dark:bg-cyan-900/40
                                                     dark:text-cyan-300">
                                            {{ t('ownCurrency') }}
                                        </span>

                                        <span v-if="variant.has_own_dimensions"
                                              class="rounded-sm border
                                                     border-orange-300 bg-orange-100 px-1 py-0.5
                                                     text-orange-700 dark:bg-orange-900/40
                                                     dark:text-orange-300"
                                              :title="dimensionsTitle(variant)">
                                            {{ t('ownDimensions') }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Attribute values -->
                            <td class="px-2 py-1">
                                <div v-if="variantValues(variant).length"
                                     class="flex max-w-64 flex-wrap gap-1"
                                     :title="valuesTooltip(variant)">
                                    <span
                                        v-for="item in variantValues(variant)"
                                        :key="item.id"
                                        class="inline-flex max-w-full items-center rounded-sm
                                               border border-slate-300 bg-white px-1.5 py-0.5
                                               text-[9px] text-slate-700 dark:border-slate-600
                                               dark:bg-slate-700 dark:text-slate-200"
                                    >
                                        <span v-if="attributeColor(item)"
                                              class="mr-1 h-2.5 w-2.5 shrink-0 rounded-full
                                                     border border-slate-400"
                                              :style="{ backgroundColor: attributeColor(item) }">
                                        </span>
                                        <span class="truncate">{{ valueTitle(item) }}</span>
                                    </span>
                                </div>

                                <div v-else class="text-center text-slate-400">
                                    {{ t('noData') }}
                                </div>

                                <div class="mt-1 text-[9px] text-slate-500 dark:text-slate-300">
                                    {{ t('attributes') }}:
                                    {{ variant.values_count ?? variantValues(variant).length }}
                                </div>
                            </td>

                            <!-- Price -->
                            <td class="whitespace-nowrap px-2 py-1">
                                <div class="text-right">
                                    <div class="text-sm font-bold
                                                text-teal-700 dark:text-teal-300">
                            {{ formatMoney(variant.effective_price, effectiveCurrency(variant)) }}
                                    </div>

                                    <div v-if="variant.effective_old_price"
                                         class="text-sm text-slate-400 line-through">
                        {{ formatMoney(variant.effective_old_price, effectiveCurrency(variant)) }}
                                    </div>

                                    <div v-if="variant.effective_wholesale_price"
                                         class="mt-0.5 text-[10px]
                                                text-blue-700 dark:text-blue-300"
                                         :title="t('wholesalePrice')">
                    {{ formatMoney(variant.effective_wholesale_price, effectiveCurrency(variant)) }}
                                        <span v-if="variant.effective_wholesale_min_quantity">
                                            × {{ variant.effective_wholesale_min_quantity }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Stock -->
                            <td class="whitespace-nowrap px-2 py-1 text-center">
                                <div class="text-sm font-bold"
                                     :class="variant.in_stock && variant.has_stock ?
                                     'text-amber-600 dark:text-amber-300' :
                                     'text-rose-600 dark:text-rose-300'">
                                    {{ variant.quantity ?? 0 }}
                                </div>

                                <div class="text-[9px]"
                                     :class="variant.in_stock && variant.has_stock ?
                                     'text-amber-600 dark:text-amber-300' :
                                     'text-rose-600 dark:text-rose-300'">
                        {{ variant.in_stock && variant.has_stock ? t('inStock') : t('outOfStock') }}
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="whitespace-nowrap px-2 py-1">
                                <!-- Status and moderation -->
                                <div class="whitespace-nowrap px-2 py-1">

                                    <!-- Default -->
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-1 rounded-sm
                                               border px-2 py-1 text-[9px] font-semibold transition"
                                        :class="variant.is_default
                                        ? 'cursor-default border-amber-400 bg-violet-100 ' +
                                         'text-amber-700 dark:bg-violet-900/40 dark:text-amber-300'
                                        : 'border-slate-400 bg-white text-slate-500 ' +
                                         'hover:border-amber-400 hover:text-amber-600 ' +
                                          'dark:bg-slate-800 dark:text-slate-300'"
                                        :disabled="variant.is_default"
                                        @click="emit('make-default', variant)"
                                    >
                                        <svg class="h-3.5 w-3.5 fill-current" viewBox="0 0 24 24">
                                            <path d="m12 2.5 2.93 5.94 6.56.95-4.75 4.63 1.12 6.54L12 17.48l-5.86 3.08 1.12-6.54-4.75-4.63 6.56-.95L12 2.5Z" />
                                        </svg>

                                {{ variant.is_default ? t('defaultVariant') : t('makePrimary') }}
                                    </button>

                                    <div class="flex flex-col items-center justify-center gap-1">
                                        <span class="rounded-sm px-2 py-1 text-[9px]
                                                     font-semibold">
                                            {{ statusBadge(variant.status).text }}
                                        </span>

                                        <div class="flex items-center justify-center gap-1">
                                        <span
                                            class="rounded-sm border px-2 py-1 text-[9px]
                                                   font-semibold"
                                            :class="moderationBadge(variant.moderation_status).class"
:title="variant.moderation_note && variant.moderated_at ? `${variant.moderation_note} [${formatDate(variant.moderated_at)}]` : variant.moderation_note || null"
                                        >
                                            {{ moderationBadge(variant.moderation_status).text }}
                                        </span>

                                            <ModerationButton
                                                :isAdmin="isAdmin"
                                                :status="variant?.moderation_status ?? 0"
                                                :initialNote="variant?.moderation_note || ''"
                                                mode="toggle"
                                                @submit="({ status, note }) =>
                                            emit('approve', variant, status, note)"
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center justify-center gap-1">
                                    <ActivityToggle
                                        :isActive="variant.activity"
                                        :title="variant.activity ? t('enabled') : t('disabled')"
                                        @toggle-activity="emit('toggle-activity', variant)"
                                    />

                                    <IconEdit
                                        :href="route('admin.marketProductVariants.edit', { marketProductVariant: variant.id })"
                                    />

                                    <DeleteIconButton @delete="emit('delete', variant)" />
                                </div>
                            </td>

                            <!-- Checkbox -->
                            <td class="w-px whitespace-nowrap px-1 py-1 text-center">
                                <input type="checkbox" :checked="isSelected(variant.id)"
                                       @change="emit('toggle-select', variant.id)" />
                            </td>
                        </tr>
                    </template>
                </draggable>
            </table>

            <div v-else class="p-5 text-center text-slate-700 dark:text-slate-100">
                {{ t('noData') }}
            </div>
        </div>
    </div>
</template>
