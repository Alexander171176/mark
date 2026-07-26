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
import NewButtonToggle from '@/Components/Admin/UI/Buttons/NewButtonToggle.vue'
import SaleButtonToggle from '@/Components/Admin/UI/Buttons/SaleButtonToggle.vue'
import HitButtonToggle from '@/Components/Admin/UI/Buttons/HitButtonToggle.vue'

const { t, locale } = useI18n()

/* ======================== Props ======================== */

const props = defineProps({
    bundles: {
        type: Array,
        default: () => [],
    },

    selectedBundles: {
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

/* ======================== Local bundles ======================== */

/** Локальная копия списка для drag-and-drop. */
const localBundles = ref([])

watch(
    () => props.bundles,
    (newValue) => {
        localBundles.value = JSON.parse(
            JSON.stringify(newValue || [])
        )
    },
    {
        immediate: true,
        deep: true,
    }
)

/** Передача нового порядка комплектов родительскому компоненту. */
const handleDragEnd = () => {
    emit(
        'update-sort-order',
        localBundles.value.map((bundle) => bundle.id)
    )
}

/* ======================== Selection ======================== */

/** Выбор всех отображаемых комплектов. */
const toggleAll = (event) => {
    emit('toggle-all', {
        ids: localBundles.value.map((bundle) => bundle.id),
        checked: event.target.checked,
    })
}

/** Выбраны ли все строки текущей таблицы. */
const allBundlesSelected = () => {
    if (!localBundles.value.length) {
        return false
    }

    return localBundles.value.every((bundle) => {
        return props.selectedBundles.includes(bundle.id)
    })
}

/* ======================== Translations ======================== */

/** Текущий перевод комплекта. */
const bundleTranslation = (bundle) => {
    return bundle?.translation
        || bundle?.translations?.[0]
        || {}
}

/** Название комплекта. */
const bundleTitle = (bundle) => {
    return bundleTranslation(bundle)?.title
        || `ID: ${bundle?.id}`
}

/** Подзаголовок комплекта. */
const bundleSubtitle = (bundle) => {
    return bundleTranslation(bundle)?.subtitle || ''
}

/** Краткое описание комплекта. */
const bundleShort = (bundle) => {
    return bundleTranslation(bundle)?.short || ''
}

/** Название связанной переводимой сущности. */
const relationTitle = (relation) => {
    return relation?.title
        || relation?.translation?.title
        || relation?.translations?.[0]?.title
        || ''
}

/** Название компании. */
const companyTitle = (bundle) => {
    return relationTitle(bundle?.company)
        || bundle?.company?.legal_name
        || ''
}

/** Название магазина. */
const shopTitle = (bundle) => {
    return relationTitle(bundle?.shop)
}

/* ======================== Images ======================== */

/** Первое изображение комплекта с учётом порядка. */
const getPrimaryImage = (bundle) => {
    if (
        !Array.isArray(bundle?.images)
        || !bundle.images.length
    ) {
        return null
    }

    return [...bundle.images].sort((left, right) => {
        return Number(left?.order ?? 0)
            - Number(right?.order ?? 0)
    })[0]
}

/** URL изображения комплекта. */
const imageUrl = (bundle) => {
    const image = getPrimaryImage(bundle)

    return image?.webp_url
        || image?.thumb_url
        || image?.image_url
        || image?.url
        || '/storage/market/market_product_bundle_images/default-image.png'
}

/** Alt изображения комплекта. */
const imageAlt = (bundle) => {
    return getPrimaryImage(bundle)?.alt
        || bundleTitle(bundle)
}

/** Title изображения комплекта. */
const imageTitle = (bundle) => {
    return getPrimaryImage(bundle)?.caption
        || bundleTitle(bundle)
}

/* ======================== Owner ======================== */

/** Подсказка владельца комплекта. */
const ownerTitle = (bundle) => {
    const owner = bundle?.owner

    if (!owner) {
        return t('noData')
    }

    return `${owner.name || ''}${
        owner.email
            ? ` — ${owner.email}`
            : ''
    }`.trim()
}

/** Аватар владельца. */
const ownerAvatar = (bundle) => {
    return bundle?.owner?.profile_photo_url
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
    return t(
        statusLabelKeyMap[status]
        || status
        || 'no'
    )
}

/** Оформление статуса публикации. */
const statusBadge = (status) => {
    if (status === 'published') {
        return {
            text: getStatusLabel(status),
            class:
                'bg-emerald-100 text-emerald-700 border-emerald-300 ' +
                'dark:bg-emerald-900/40 dark:text-emerald-300 ' +
                'dark:border-emerald-700',
        }
    }

    if (status === 'archived') {
        return {
            text: getStatusLabel(status),
            class:
                'bg-slate-200 text-slate-700 border-slate-400 ' +
                'dark:bg-slate-800 dark:text-slate-300 ' +
                'dark:border-slate-600',
        }
    }

    return {
        text: getStatusLabel(status),
        class:
            'bg-amber-100 text-amber-800 border-amber-300 ' +
            'dark:bg-amber-900/40 dark:text-amber-300 ' +
            'dark:border-amber-700',
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
                'dark:bg-emerald-900/40 dark:text-emerald-300 ' +
                'dark:border-emerald-700',
        }
    }

    if (value === 2) {
        return {
            text: t('statusSelectRejected'),
            class:
                'bg-rose-100 text-rose-700 border-rose-300 ' +
                'dark:bg-rose-900/40 dark:text-rose-300 ' +
                'dark:border-rose-700',
        }
    }

    return {
        text: t('underModeration'),
        class:
            'bg-amber-100 text-amber-800 border-amber-300 ' +
            'dark:bg-amber-900/40 dark:text-amber-300 ' +
            'dark:border-amber-700',
    }
}

/* ======================== Formatting ======================== */

/** Безопасное преобразование в число. */
const safeNumber = (value) => {
    const number = Number(value)

    return Number.isFinite(number)
        ? number
        : 0
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

    return date.toLocaleDateString(
        locale.value || 'ru-RU',
        {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        }
    )
}

/** Ограничение длины текста. */
const truncateText = (text, maxLength = 55) => {
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

/** Форматирование рейтинга. */
const formatRating = (bundle) => {
    return safeNumber(bundle?.rating_avg).toFixed(1)
}

/* ======================== Bundle data ======================== */

/** Фактическая цена комплекта. */
const effectivePrice = (bundle) => {
    return bundle?.effective_price
        ?? bundle?.price
        ?? 0
}

/** Режим формирования цены. */
const priceModeTitle = (bundle) => {
    return bundle?.calculate_price
        ? 'Цена рассчитывается автоматически по составу'
        : 'Цена комплекта задаётся вручную'
}

/** Количество позиций комплекта. */
const itemsCount = (bundle) => {
    if (bundle?.items_count !== undefined) {
        return safeNumber(bundle.items_count)
    }

    return Array.isArray(bundle?.items)
        ? bundle.items.length
        : 0
}

/** Количество активных позиций. */
const activeItemsCount = (bundle) => {
    if (bundle?.active_items_count !== undefined) {
        return safeNumber(bundle.active_items_count)
    }

    if (Array.isArray(bundle?.active_items)) {
        return bundle.active_items.length
    }

    if (Array.isArray(bundle?.items)) {
        return bundle.items.filter(
            (item) => Boolean(item?.activity)
        ).length
    }

    return 0
}

/** Название позиции состава. */
const itemTitle = (item) => {
    return item?.display_title
        || item?.variant?.translation?.title
        || item?.variant?.title
        || item?.product?.translation?.title
        || item?.product?.title
        || `ID: ${item?.id}`
}

/** Подсказка с составом комплекта. */
const itemsTitle = (bundle) => {
    const items = Array.isArray(bundle?.items)
        ? bundle.items
        : (
            Array.isArray(bundle?.active_items)
                ? bundle.active_items
                : []
        )

    if (!items.length) {
        return t('noData')
    }

    return items.map((item, index) => {
        const values = [
            `${index + 1}. ${itemTitle(item)}`,
            `× ${safeNumber(item?.quantity) || 1}`,
        ]

        if (item?.unit_price !== null && item?.unit_price !== undefined) {
            values.push(
                `Цена: ${formatMoney(
                    item.unit_price,
                    bundle?.currency
                )}`
            )
        }

        if (item?.discount_type && safeNumber(item?.discount_value) > 0) {
            values.push(
                item.discount_type === 'percent'
                    ? `Скидка: ${item.discount_value}%`
                    : `Скидка: ${formatMoney(
                        item.discount_value,
                        bundle?.currency
                    )}`
            )
        }

        return values.join(' ')
    }).join('\n')
}

/** Подсказка с кодами комплекта. */
const codesTitle = (bundle) => {
    return [
        bundle?.url ? `URL: ${bundle.url}` : null,
        bundle?.sku ? `SKU: ${bundle.sku}` : null,
        bundle?.vendor_code
            ? `Артикул: ${bundle.vendor_code}`
            : null,
        bundle?.barcode
            ? `Штрихкод: ${bundle.barcode}`
            : null,
    ]
        .filter(Boolean)
        .join('\n')
}

/** Подсказка поставщика. */
const supplierTitle = (bundle) => {
    return [
        companyTitle(bundle)
            ? `Компания: ${companyTitle(bundle)}`
            : null,
        shopTitle(bundle)
            ? `Магазин: ${shopTitle(bundle)}`
            : null,
    ]
        .filter(Boolean)
        .join('\n')
}

/** Подсказка публикации и сортировки. */
const publicationTitle = (bundle) => {
    return [
        `Sort: ${bundle?.sort ?? 0}`,
        bundle?.published_at
            ? `Публикация: ${formatDate(bundle.published_at)}`
            : null,
        bundle?.show_from_at
            ? `Показ с: ${formatDate(bundle.show_from_at)}`
            : null,
        bundle?.show_to_at
            ? `Показ до: ${formatDate(bundle.show_to_at)}`
            : null,
    ]
        .filter(Boolean)
        .join('\n')
}

/** Публичный URL комплекта. */
const bundlePublicUrl = (bundle) => {
    return `/market/bundles/${encodeURIComponent(bundle?.url || '')}`
}
</script>

<template>
    <div
        class="relative rounded-sm border
               border-slate-200 dark:border-slate-600
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
                {{ selectedBundles.length }}
            </div>

            <label
                v-if="localBundles.length"
                class="flex cursor-pointer items-center
                       text-xs text-slate-600 dark:text-slate-200"
            >
                <span>{{ t('selectAll') }}</span>

                <input
                    type="checkbox"
                    class="mx-2"
                    :checked="allBundlesSelected() || allSelected"
                    @change="toggleAll"
                />
            </label>
        </div>

        <div class="overflow-x-auto">
            <table
                v-if="localBundles.length"
                class="table-auto w-full
                       text-slate-700 dark:text-slate-100"
            >
                <thead
                    class="border border-solid
                           border-gray-300 dark:border-gray-700
                           bg-slate-200 dark:bg-cyan-900
                           text-xs uppercase"
                >
                <tr>
                    <!-- Drag handle -->
                    <th class="w-px px-1 py-1">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 fill-current
                                       text-slate-800 dark:text-slate-200"
                            viewBox="0 0 24 24"
                        >
                            <path
                                d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"
                            />
                            <path
                                d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"
                            />
                        </svg>
                    </th>

                    <!-- ID -->
                    <th class="w-px whitespace-nowrap px-1 py-1">
                        <div class="text-center font-semibold">
                            {{ t('id') }}
                        </div>
                    </th>

                    <!-- Владелец -->
                    <th class="w-px whitespace-nowrap px-1 py-1">
                        <div
                            class="flex justify-center"
                            :title="t('owner')"
                        >
                            <svg
                                class="h-6 w-6 shrink-0 fill-current"
                                viewBox="0 0 24 24"
                            >
                                <path d="M3,7H1V2A1,1,0,0,1,2,1H7V3H3Z" />
                                <path d="M23,7H21V3H17V1h5a1,1,0,0,1,1,1Z" />
                                <path d="M7,23H2a1,1,0,0,1-1-1V17H3v4H7Z" />
                                <path d="M22,23H17V21h4V17h2v5A1,1,0,0,1,22,23Z" />
                                <path
                                    d="M18.242,18.03l-2.727-.681a1,1,0,0,1-.744-.806l-.249-1.491A6.792,6.792,0,0,0,17,10V9A5,5,0,0,0,7,9v1a6.792,6.792,0,0,0,2.478,5.052l-.249,1.491a1,1,0,0,1-.743.806l-2.728.681A1,1,0,0,0,6,20H18a1,1,0,0,0,.242-1.97Z"
                                />
                            </svg>
                        </div>
                    </th>

                    <!-- Изображение -->
                    <th class="w-px px-1 py-1">
                        <div
                            class="flex justify-center"
                            :title="t('image')"
                        >
                            <svg
                                class="h-6 w-6 shrink-0 fill-current"
                                viewBox="0 0 512 512"
                            >
                                <path
                                    d="M0 96C0 60.7 28.7 32 64 32h384c35.3 0 64 28.7 64 64v320c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V96zm323.8 106.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4S78.8 416 88 416h336c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"
                                />
                            </svg>
                        </div>
                    </th>

                    <!-- Комплект -->
                    <th class="min-w-64 px-2 py-1">
                        <div class="text-left font-semibold">
                            {{ t('marketProductBundle') }}
                        </div>
                    </th>

                    <!-- Поставщик, Состав -->
                    <th class="min-w-64 px-2 py-1">
                        <div class="text-left font-semibold">
                            {{ t('supplier') }} / {{ t('compound') }}
                        </div>
                    </th>

                    <!-- Цена -->
                    <th class="whitespace-nowrap px-2 py-1">
                        <div class="text-right font-semibold">
                            {{ t('price') }}
                        </div>
                    </th>

                    <!-- Остаток -->
                    <th class="whitespace-nowrap px-2 py-1">
                        <div class="text-center font-semibold">
                            {{ t('quantity') }}
                        </div>
                    </th>

                    <!-- Действия, Рекламные позиции, Статус -->
                    <th class="whitespace-nowrap px-2 py-1">
                        <div class="text-right font-semibold">
                            {{ t('actions') }}
                        </div>
                    </th>

                    <!-- Checkbox -->
                    <th class="w-px whitespace-nowrap px-1 py-1 text-center">
                        <input
                            type="checkbox"
                            :checked="allBundlesSelected() || allSelected"
                            @change="toggleAll"
                        />
                    </th>
                </tr>
                </thead>

                <draggable
                    v-model="localBundles"
                    tag="tbody"
                    item-key="id"
                    handle=".handle"
                    @end="handleDragEnd"
                >
                    <template #item="{ element: bundle }">
                        <tr
                            class="border-b-2 text-xs font-semibold
                                   hover:bg-slate-100
                                   dark:hover:bg-cyan-800"
                        >
                            <!-- Drag handle -->
                            <td
                                class="handle w-px cursor-move
                                       px-1 py-1 text-center"
                            >
                                <svg
                                    class="h-4 w-4
                                           text-gray-500 dark:text-gray-300"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"
                                    />
                                </svg>
                            </td>

                            <!-- ID -->
                            <td class="w-px whitespace-nowrap px-1 py-1">
                                <div
                                    class="text-center text-blue-600
                                           dark:text-blue-300"
                                    :title="publicationTitle(bundle)"
                                >
                                    {{ bundle.id }}
                                </div>
                            </td>

                            <!-- Владелец -->
                            <td class="w-px px-1 py-1">
                                <div
                                    class="flex justify-center"
                                    :title="ownerTitle(bundle)"
                                >
                                    <img
                                        :src="ownerAvatar(bundle)"
                                        :alt="ownerTitle(bundle)"
                                        class="h-6 w-6 rounded-full
                                               border border-slate-300
                                               object-cover dark:border-slate-500"
                                    />
                                </div>
                            </td>

                            <!-- Изображение -->
                            <td class="w-px px-1 py-1">
                                <a
                                    :href="bundlePublicUrl(bundle)"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="block"
                                >
                                    <img
                                        :src="imageUrl(bundle)"
                                        :alt="imageAlt(bundle)"
                                        :title="imageTitle(bundle)"
                                        class="h-8 w-12 rounded
                                               border border-slate-300
                                               object-cover
                                               dark:border-slate-500"
                                    />
                                </a>
                            </td>

                            <!-- Комплект -->
                            <td class="px-2 py-1 align-top">
                                <div class="space-y-1">


                                    <a
                                        :href="bundlePublicUrl(bundle)"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="text-center text-xs font-semibold
                                               text-sky-700 hover:text-amber-700
                                               hover:underline
                                               dark:text-sky-300 dark:hover:text-amber-300"
                                        :title="codesTitle(bundle)"
                                    >
                                        {{ bundleTitle(bundle) }}
                                    </a>

                                    <!-- URL -->
                                    <div
                                        class="mt-0.5 text-[10px] italic
                                               text-slate-500 dark:text-slate-400"
                                    >
                                        {{ truncateText(bundle.url, 70) }}
                                    </div>

                                    <!-- Заголовок -->
                                    <div
                                        v-if="bundleSubtitle(bundle)"
                                        class="text-xs text-slate-600 dark:text-slate-300"
                                    >
                                        {{ truncateText(bundleSubtitle(bundle), 65) }}
                                    </div>

                                    <div
                                        v-if="bundleShort(bundle)"
                                        class="text-[10px] font-normal
                                               text-slate-500 dark:text-slate-300"
                                    >
                                        {{ truncateText(bundleShort(bundle), 90) }}
                                    </div>

                                    <div class="mt-0.5 flex flex-wrap gap-1 text-[9px]">
                                        <span
                                            v-if="bundle.vendor_code"
                                            class="rounded-sm
                                                   border border-slate-400
                                                   bg-violet-100 px-1 py-0.5
                                                   text-violet-700
                                                   dark:bg-violet-900/40
                                                   dark:text-violet-300"
                                            :title="t('vendorCode')"
                                        >
                                            {{ truncateText(bundle.vendor_code, 24) }}
                                        </span>
                                        <span
                                            v-if="bundle.sku"
                                            class="rounded-sm
                                                   border border-slate-400
                                                   bg-blue-100 px-1 py-0.5
                                                   text-blue-700
                                                   dark:bg-blue-900/40
                                                   dark:text-blue-300"
                                            :title="t('sku')"
                                        >
                                            SKU: {{ truncateText(bundle.sku, 24) }}
                                        </span>
                                    </div>

                                    <!-- Баркод -->
                                    <div
                                        class="mt-0.5 flex items-center gap-1 text-[9px]"
                                    >
                                        <svg class="h-5 w-5 shrink-0 fill-current"
                                             viewBox="0 0 512 512">
                                            <path d="M0 448V64h18v384H0zm26.857-.273V64H36v383.727h-9.143zm27.143 0V64h8.857v383.727H54zm44.857 0V64h8.857v383.727h-8.857zm36 0V64h17.714v383.727h-17.714zm44.857 0V64h8.857v383.727h-8.857zm18 0V64h8.857v383.727h-8.857zm18 0V64h8.857v383.727h-8.857zm35.715 0V64h18v383.727h-18zm44.857 0V64h18v383.727h-18zm35.999 0V64h18.001v383.727h-18.001zm36.001 0V64h18.001v383.727h-18.001zm26.857 0V64h18v383.727h-18zm45.143 0V64h26.857v383.727h-26.857zm35.714 0V64h9.143v383.727H476zm18 .273V64h18v384h-18z" />
                                        </svg>
                                        <span
                                            v-if="bundle.barcode"
                                            class="rounded-sm
                                                   border border-slate-400
                                                   bg-slate-200 px-1 py-0.5
                                                   text-slate-700
                                                   dark:bg-slate-800
                                                   dark:text-slate-300"
                                            :title="t('barcode')"
                                        >
                                            {{ truncateText(bundle.barcode, 24) }}
                                        </span>
                                    </div>

                                </div>
                            </td>

                            <!-- Поставщик, Состав, Статистика  -->
                            <td class="px-2 py-1 align-top">
                                <div :title="supplierTitle(bundle)">
                                    <div
                                        v-if="companyTitle(bundle)"
                                        class="font-semibold"
                                    >
                                        {{ truncateText(companyTitle(bundle), 38) }}
                                    </div>

                                    <div
                                        v-if="shopTitle(bundle)"
                                        class="mt-1 font-normal
                                           text-slate-500
                                           dark:text-slate-300"
                                    >
                                        {{ truncateText(shopTitle(bundle), 38) }}
                                    </div>

                                    <span
                                        v-if="!companyTitle(bundle) && !shopTitle(bundle)"
                                        class="font-normal text-slate-400"
                                    >
                                    {{ t('noData') }}
                                </span>
                                </div>
                                <div :title="itemsTitle(bundle)">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="rounded-full border
                                                   bg-cyan-100 px-2 py-0.5
                                                   text-cyan-700
                                                   dark:bg-cyan-900/40
                                                   dark:text-cyan-300"
                                        >
                                            {{ itemsCount(bundle) }}
                                        </div>

                                        <div
                                            class="mt-1 font-normal
                                           text-emerald-700
                                           dark:text-emerald-300"
                                        >
                                            [{{ activeItemsCount(bundle) }}]
                                        </div>
                                    </div>

                                    <div
                                        v-if="Array.isArray(bundle.items)"
                                        class="mt-1 font-normal text-[9px]
                                           text-slate-500 dark:text-slate-300"
                                    >
                                        {{ truncateText(bundle.items.slice(0, 2).map(itemTitle).join(', '), 70) }}
                                    </div>
                                </div>
                                <div :title="t('statistics')"
                                    class="flex flex-col gap-1 font-semibold"
                                >
                                    <div class="flex flex-row items-center gap-1"
                                         :title="t('rating')">
                                        <svg
                                            class="w-3 h-3 fill-current shrink-0
                                                   text-red-500 dark:text-red-400"
                                            viewBox="0 0 24 24">
                                            <path d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z" ></path>
                                        </svg>
                                        <span>
                                {{ formatRating(bundle) }} ({{ safeNumber(bundle.rating_count) }})
                                        </span>
                                    </div>

                                    <div class="flex flex-row items-center gap-1"
                                         :title="t('views')">
                                        <svg
                                            class="w-3 h-3 fill-current shrink-0
                                                   text-blue-600 dark:text-blue-300"
                                            viewBox="0 0 16 16">
                                            <path d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z" ></path>
                                        </svg>
                                        <span>
                                            {{ safeNumber(bundle.views) }}
                                        </span>
                                    </div>

                                    <div class="flex flex-row items-center gap-1"
                                         :title="t('likes')">
                                        <svg
                                            class="w-3 h-3 fill-current shrink-0
                                                   text-rose-400 dark:text-rose-300"
                                            viewBox="0 0 24 24">
                                            <path d="M3,9H1a1,1,0,0,0-1,1V22a1,1,0,0,0,1,1H4V10A1,1,0,0,0,3,9Z"></path>
                                            <path d="M21.882,8.133A2.986,2.986,0,0,0,21,8H15V5c0-3.824-2.589-4.942-3.958-5a1.017,1.017,0,0,0-.734.277A1,1,0,0,0,10,1V5.638l-4,4.8V23H18.23A2.985,2.985,0,0,0,21.1,20.882l2.769-9A3,3,0,0,0,21.882,8.133Z"></path>
                                        </svg>
                                        <span>
                                         {{ safeNumber(bundle.likes_count) }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Цена -->
                            <td class="whitespace-nowrap px-2 py-1
                                       text-right align-top"
                                :title="priceModeTitle(bundle)"
                            >
                                <div
                                    class="text-sm font-bold
                                           text-teal-700 dark:text-teal-300"
                                >
                                    {{ formatMoney(effectivePrice(bundle), bundle.currency) }}
                                </div>

                                <div
                                    v-if="bundle.has_old_price"
                                    class="mt-1 font-normal
                                           text-slate-400 line-through"
                                >
                                    {{ formatMoney(bundle.old_price, bundle.currency) }}
                                </div>

                                <div
                                    v-if="safeNumber(bundle.saving_percent) > 0"
                                    class="mt-1 text-rose-600
                                           dark:text-rose-300"
                                >
                                    −{{ safeNumber(bundle.saving_percent).toFixed(0) }}%
                                </div>

                                <div
                                    class="mt-1 rounded px-2 py-0.5 border border-gray-400
                                           text-[10px] text-center font-normal"
                                    :class="
                                        bundle.calculate_price
                                            ? 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/40 ' +
                                             'dark:text-cyan-300'
                                            : 'bg-violet-100 text-violet-700 dark:bg-violet-900/40 ' +
                                             'dark:text-violet-300'
                                    "
                                >
                            {{ bundle.calculate_price ? t('automaticPrice') : t('manualPrice') }}
                                </div>
                            </td>

                            <!-- Наличие -->
                            <td class="whitespace-nowrap px-2 py-1
                                       text-center align-top"
                            >
                                <div
                                    class="text-sm font-bold"
                                    :class="
                                        bundle.has_stock
                                            ? 'text-amber-600 dark:text-amber-300'
                                            : 'text-rose-600 dark:text-rose-300'"
                                >
                                    {{ safeNumber(bundle.available_quantity) }}
                                </div>

                                <div
                                    class="mt-1 font-normal"
                                    :class="
                                        bundle.has_stock
                                            ? 'text-amber-600 dark:text-amber-300'
                                            : 'text-rose-500 dark:text-rose-300'"
                                >
                                    {{ bundle.has_stock ? t('inStock') : t('notAvailable') }}
                                </div>
                            </td>

                            <!-- Флаги, Статус и модерация -->
                            <td class="whitespace-nowrap px-2 py-1">
                                <div class="flex flex-col justify-center items-center gap-1">
                                    <div class="flex flex-row items-center justify-center gap-1">
                                        <ActivityToggle
                                        :isActive="bundle.activity"
                                        :title="
                                            bundle.activity ? t('enabled') : t('disabled')"
                                        @toggle-activity="
                                            emit(
                                                'toggle-activity',
                                                bundle
                                            )"
                                    />
                                        <IconEdit
                                            :href="
                                            route(
                                                'admin.marketProductBundles.edit',
                                                {
                                                    marketProductBundle:
                                                        bundle.id,
                                                }
                                            )"
                                        />
                                        <DeleteIconButton
                                            @delete="
                                            emit('delete', bundle)"
                                        />
                                    </div>
                                    <div class="flex flex-row items-center justify-center gap-1">
                                        <LeftToggle
                                            :isActive="bundle.left"
                                            :title="
                                                bundle.left
                                                    ? t('enabled')
                                                    : t('disabled')"
                                            @toggle-left="emit('toggle-left', bundle)"
                                        />
                                        <MainToggle
                                            :isActive="bundle.main"
                                            :title="
                                                bundle.main
                                                    ? t('enabled')
                                                    : t('disabled')"
                                            @toggle-main="emit('toggle-main', bundle)"
                                        />
                                        <RightToggle
                                            :isActive="bundle.right"
                                            :title="
                                                bundle.right
                                                    ? t('enabled')
                                                    : t('disabled')"
                                            @toggle-right="emit('toggle-right', bundle)"
                                        />
                                    </div>
                                    <div class="flex flex-row items-center justify-center gap-1">
                                        <NewButtonToggle
                                            :isActive="bundle.is_new"
                                            :title="bundle.is_new ? t('enabled') : t('disabled')"
                                            @toggle-is-new="emit('toggle-is-new', bundle)"
                                        />
                                        <HitButtonToggle
                                            :isActive="bundle.is_hit"
                                            :title="bundle.is_hit ? t('enabled') : t('disabled')"
                                            @toggle-is-hit="emit('toggle-is-hit', bundle)"
                                        />
                                        <SaleButtonToggle
                                            :isActive="bundle.is_sale"
                                            :title="bundle.is_sale ? t('enabled') : t('disabled')"
                                            @toggle-is-sale="emit('toggle-is-sale', bundle)"
                                        />
                                    </div>
                                    <!-- Статус и модерация -->
                                    <div class="flex flex-row items-center justify-center gap-1">
                                        <div
                                            class="flex flex-col items-center
                                           justify-center gap-1"
                                        >
                                            <span
                                                class="px-2 py-1 text-[10px] font-semibold
                                                       text-fuchsia-700 dark:text-fuchsia-300">
                                                {{ statusBadge(bundle.status).text }}
                                            </span>
                                            <div
                                                class="flex items-center
                                               justify-center gap-1"
                                            >
                                        <span
                                            class="rounded-sm border
                                                   px-2 py-1 text-[9px]
                                                   font-semibold"
                                            :class="moderationBadge(bundle.moderation_status).class"
                                            :title="bundle.moderation_note
                                                ? `${bundle.moderation_note}${
                                                    bundle.moderated_at
                                                        ? ` [${formatDate(
                                                            bundle.moderated_at
                                                        )}]`
                                                        : ''
                                                }`
                                                : null"
                                        >
                                            {{ moderationBadge(bundle.moderation_status).text }}
                                        </span>

                                                <ModerationButton
                                                    :isAdmin="isAdmin"
                                                    :status="bundle?.moderation_status ?? 0"
                                                    :initialNote="bundle?.moderation_note || ''"
                                                    mode="toggle"
                                                    @submit="({ status, note }) =>
                                                    emit('approve',
                                                        bundle,
                                                        status,
                                                        note
                                                    )"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Checkbox -->
                            <td class="w-px whitespace-nowrap px-2 py-2">
                                <div class="text-center">
                                    <input
                                        type="checkbox"
                                        :checked="
                                            selectedBundles.includes(
                                                bundle.id
                                            )"
                                        @change="
                                            emit(
                                                'toggle-select',
                                                bundle.id
                                            )"
                                    />
                                </div>
                            </td>
                        </tr>
                    </template>
                </draggable>
            </table>

            <div
                v-else
                class="p-8 text-center
                       text-slate-500 dark:text-slate-300"
            >
                {{ t('noData') }}
            </div>
        </div>
    </div>
</template>
