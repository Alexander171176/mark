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

const { t, locale } = useI18n()

const props = defineProps({
    bundles: {
        type: Array,
        default: () => [],
    },

    selectedBundles: {
        type: Array,
        default: () => [],
    },

    isAdmin: {
        type: Boolean,
        default: false,
    },
})

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

/** Локальная копия комплектов для drag-and-drop. */
const localBundles = ref([])

/** Идентификаторы карточек с раскрытым служебным блоком. */
const openedInfoBlocks = ref([])

/** Синхронизация входного списка с локальной копией. */
watch(
    () => props.bundles,
    (bundles) => {
        localBundles.value = JSON.parse(
            JSON.stringify(bundles || [])
        )
    },
    {
        immediate: true,
        deep: true,
    }
)

/* ======================== Drag and drop ======================== */

/** Передача нового порядка комплектов родительскому компоненту. */
const handleDragEnd = () => {
    emit(
        'update-sort-order',
        localBundles.value.map((bundle) => bundle.id)
    )
}

/* ======================== Selection ======================== */

/** Выбрать или снять выбор со всех видимых комплектов. */
const toggleAll = (event) => {
    emit('toggle-all', {
        ids: localBundles.value.map((bundle) => bundle.id),
        checked: Boolean(event?.target?.checked),
    })
}

/** Все видимые комплекты выбраны. */
const allSelected = () => {
    return localBundles.value.length > 0
        && localBundles.value.every((bundle) => {
            return props.selectedBundles.includes(bundle.id)
        })
}

/* ======================== Translation helpers ======================== */

/** Текущий перевод комплекта. */
const bundleTranslation = (bundle) => {
    return bundle?.translation || {}
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

/** Название связанной сущности. */
const relationTitle = (relation) => {
    return relation?.translation?.title || ''
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

/* ======================== Owner and info ======================== */

/** Имя владельца. */
const ownerName = (bundle) => {
    return bundle?.owner?.name || t('noData')
}

/** Email владельца. */
const ownerEmail = (bundle) => {
    return bundle?.owner?.email || ''
}

/** Полная подсказка владельца. */
const ownerTitle = (bundle) => {
    const owner = bundle?.owner

    if (!owner) {
        return t('noData')
    }

    return [
        owner.name,
        owner.email,
    ].filter(Boolean).join(' — ')
}

/** Аватар владельца. */
const ownerAvatar = (bundle) => {
    return bundle?.owner?.profile_photo_url
        || '/storage/profile-photos/default-image.png'
}

/** Открыт ли служебный блок карточки. */
const isInfoBlockOpen = (bundleId) => {
    return openedInfoBlocks.value.includes(bundleId)
}

/** Открытие или закрытие служебного блока. */
const toggleInfoBlock = (bundleId) => {
    if (isInfoBlockOpen(bundleId)) {
        openedInfoBlocks.value = openedInfoBlocks.value.filter(
            (id) => id !== bundleId
        )

        return
    }

    openedInfoBlocks.value.push(bundleId)
}

/* ======================== Images ======================== */

/** Главное изображение комплекта. */
const primaryImage = (bundle) => {
    if (!Array.isArray(bundle?.images) || !bundle.images.length) {
        return null
    }

    return [...bundle.images].sort((left, right) => {
        return Number(left?.order ?? 0)
            - Number(right?.order ?? 0)
    })[0]
}

/** URL главного изображения. */
const imageUrl = (bundle) => {
    const image = primaryImage(bundle)

    return image?.webp_url
        || image?.thumb_url
        || image?.url
        || '/storage/market/market_product_bundle_images/default-image.png'
}

/** Alt главного изображения. */
const imageAlt = (bundle) => {
    return primaryImage(bundle)?.alt || bundleTitle(bundle)
}

/** Подсказка главного изображения. */
const imageTitle = (bundle) => {
    return primaryImage(bundle)?.caption || bundleTitle(bundle)
}

/* ======================== Bundle items ======================== */

/** Позиции комплекта. */
const bundleItems = (bundle) => {
    if (Array.isArray(bundle?.items)) {
        return bundle.items
    }

    if (Array.isArray(bundle?.active_items)) {
        return bundle.active_items
    }

    return []
}

/** Название позиции состава. */
const itemTitle = (item) => {
    return item?.variant?.translation?.title
        || item?.product?.translation?.title
        || `ID: ${item?.id}`
}

/** Подсказка с полным составом комплекта. */
const itemsTooltip = (bundle) => {
    return bundleItems(bundle)
        .map((item) => {
            return `${itemTitle(item)} × ${Number(item?.quantity || 1)}`
        })
        .join('\n')
}

/* ======================== Status ======================== */

const statusLabelKeyMap = {
    draft: 'statusDraft',
    published: 'statusPublished',
    archived: 'statusArchived',
}

/** Текст статуса публикации. */
const statusLabel = (status) => {
    return t(
        statusLabelKeyMap[status]
        || status
        || 'no'
    )
}

/** Оформление статуса модерации. */
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

/** Безопасное число. */
const safeNumber = (value) => {
    const number = Number(value)

    return Number.isFinite(number) ? number : 0
}

/** Форматирование даты. */
const formatDate = (value) => {
    if (!value) {
        return ''
    }

    const date = new Date(value)

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

/** Ограничение длины строки. */
const truncateText = (value, maxLength = 80) => {
    if (!value) {
        return ''
    }

    const text = String(value)

    return text.length > maxLength
        ? `${text.slice(0, maxLength).trimEnd()}…`
        : text
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

    const thousandsSeparator = currency?.thousands_sep ?? ' '
    const decimalSeparator = currency?.decimal_sep ?? '.'

    const parts = amount.toFixed(precision).split('.')

    const integerPart = parts[0].replace(
        /\B(?=(\d{3})+(?!\d))/g,
        thousandsSeparator
    )

    const formatted = precision > 0
        ? `${integerPart}${decimalSeparator}${parts[1]}`
        : integerPart

    const symbol = String(
        currency?.symbol
        || currency?.code
        || ''
    ).trim()

    if (!symbol) {
        return formatted
    }

    return currency?.symbol_first
        ? `${symbol}${formatted}`
        : `${formatted} ${symbol}`
}

/** Фактическая цена комплекта. */
const effectivePrice = (bundle) => {
    return bundle?.effective_price
        ?? bundle?.calculated_price
        ?? bundle?.price
        ?? 0
}

/** Рейтинг с одной цифрой после запятой. */
const formatRating = (bundle) => {
    return safeNumber(bundle?.rating_avg).toFixed(1)
}

/** Публичная ссылка комплекта. */
const bundlePublicUrl = (bundle) => {
    return `/market/bundles/${encodeURIComponent(
        bundle?.url || ''
    )}`
}
</script>

<template>
    <div
        class="relative rounded-sm border
               border-slate-400 bg-white shadow-lg
               dark:border-slate-500 dark:bg-slate-700"
    >
        <!-- Панель выбора -->
        <div
            class="flex items-center justify-between
                   border-b border-slate-400 px-3 py-2
                   dark:border-slate-500"
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
                    :checked="allSelected()"
                    @change="toggleAll"
                />
            </label>
        </div>

        <div v-if="localBundles.length" class="p-3">
            <draggable
                v-model="localBundles"
                tag="div"
                item-key="id"
                handle=".handle"
                class="grid grid-cols-1 gap-3
                       sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                @end="handleDragEnd"
            >
                <template #item="{ element: bundle }">
                    <article
                        class="relative flex h-full flex-col
                               overflow-hidden rounded-md border
                               border-slate-400 bg-slate-50/70
                               shadow-sm transition-shadow
                               hover:shadow-md
                               dark:border-slate-500 dark:bg-slate-800/80"
                    >
                        <!-- Шапка карточки -->
                        <header
                            class="flex items-center justify-between
                                   border-b border-dashed
                                   border-slate-400 px-2 py-1
                                   dark:border-slate-500"
                        >
                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    class="handle cursor-move text-slate-400
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
                                    class="rounded-sm border border-gray-400
                                           bg-slate-200 px-1.5 py-0.5
                                           text-[10px] font-semibold
                                           text-slate-800
                                           dark:bg-slate-700 dark:text-blue-100"
                                    :title="`[${bundle.sort}] / ${formatDate(bundle.published_at)}`"
                                >
                                    ID: {{ bundle.id }}
                                </div>

                                <button
                                    type="button"
                                    class="text-slate-400 hover:text-blue-600
                                           dark:hover:text-blue-300"
                                    :title="isInfoBlockOpen(bundle.id)
                                        ? t('hideOwner')
                                        : t('showOwner')"
                                    @click.prevent="toggleInfoBlock(bundle.id)"
                                >
                                    <svg
                                        class="h-4 w-4 transition-transform"
                                        :class="{
                                            'rotate-180': isInfoBlockOpen(bundle.id),
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

                            <div class="flex items-center gap-2">
                                <span
                                    class="rounded-sm border px-2 py-0.5
                                           text-[10px] font-semibold"
                                    :class="moderationBadge(bundle.moderation_status).class"
                                    :title="bundle.moderation_note || null"
                                >
                                    {{ moderationBadge(bundle.moderation_status).text }}
                                </span>

                                <input
                                    type="checkbox"
                                    :checked="selectedBundles.includes(bundle.id)"
                                    @change="emit('toggle-select', bundle.id)"
                                />
                            </div>
                        </header>

                        <!-- Владелец и служебные сведения -->
                        <div
                            v-show="isInfoBlockOpen(bundle.id)"
                            class="border-b border-dashed border-slate-300
                                   px-3 py-2 text-center
                                   dark:border-slate-600"
                        >
                            <img
                                :src="ownerAvatar(bundle)"
                                :title="ownerTitle(bundle)"
                                :alt="t('owner')"
                                class="mx-auto h-12 w-12 rounded-full
                                       border border-slate-300 object-cover
                                       dark:border-slate-600"
                            />

                            <div
                                class="mt-1 truncate text-[11px] font-semibold
                                       text-slate-700 dark:text-slate-100"
                            >
                                {{ ownerName(bundle) }}
                            </div>

                            <div
                                v-if="ownerEmail(bundle)"
                                class="truncate text-[10px]
                                       text-slate-500 dark:text-slate-300"
                            >
                                {{ ownerEmail(bundle) }}
                            </div>

                            <div
                                class="mt-1 text-[10px]
                                       text-slate-500 dark:text-slate-300"
                            >
                                {{ formatDate(bundle.show_from_at || bundle.published_at) }}
                                <span v-if="bundle.show_to_at">
                                    — {{ formatDate(bundle.show_to_at) }}
                                </span>
                            </div>

                            <div class="mt-0.5 flex flex-col justify-center gap-1 text-[9px]">
                                <div
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
                                </div>
                                <div
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
                                </div>

                                <!-- Баркод -->
                                <div
                                    class="mt-0.5 flex items-center justify-center
                                           gap-1 text-[9px]"
                                >
                                    <svg class="h-5 w-5 shrink-0 fill-current"
                                         viewBox="0 0 512 512">
                                        <path
                                            d="M0 448V64h18v384H0zm26.857-.273V64H36v383.727h-9.143zm27.143 0V64h8.857v383.727H54zm44.857 0V64h8.857v383.727h-8.857zm36 0V64h17.714v383.727h-17.714zm44.857 0V64h8.857v383.727h-8.857zm18 0V64h8.857v383.727h-8.857zm18 0V64h8.857v383.727h-8.857zm35.715 0V64h18v383.727h-18zm44.857 0V64h18v383.727h-18zm35.999 0V64h18.001v383.727h-18.001zm36.001 0V64h18.001v383.727h-18.001zm26.857 0V64h18v383.727h-18zm45.143 0V64h26.857v383.727h-26.857zm35.714 0V64h9.143v383.727H476zm18 .273V64h18v384h-18z" />
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
                        </div>

                        <!-- Изображение -->
                        <div
                            class="relative w-full overflow-hidden
                                   bg-slate-200 dark:bg-slate-900"
                        >
                            <img
                                :src="imageUrl(bundle)"
                                :alt="imageAlt(bundle)"
                                :title="imageTitle(bundle)"
                                class="aspect-[4/3] w-full object-cover"
                            />

                            <div class="absolute left-2 top-2 flex flex-wrap gap-1">
                                <span
                                    v-if="bundle.is_new"
                                    class="rounded-sm border border-teal-700
                                           bg-teal-200/95 px-1.5 py-0.5
                                           text-[9px] font-semibold text-teal-700
                                           dark:border-teal-300 dark:bg-teal-800/90
                                           dark:text-teal-300"
                                >
                                    NEW
                                </span>

                                <span
                                    v-if="bundle.is_hit"
                                    class="rounded-sm border border-yellow-700
                                           bg-yellow-200/95 px-1.5 py-0.5
                                           text-[9px] font-semibold text-yellow-700
                                           dark:border-yellow-300 dark:bg-yellow-800/90
                                           dark:text-yellow-300"
                                >
                                    HIT
                                </span>

                                <span
                                    v-if="bundle.is_sale"
                                    class="rounded-sm border border-pink-700
                                           bg-pink-200/95 px-1.5 py-0.5
                                           text-[9px] font-semibold text-pink-700
                                           dark:border-pink-300 dark:bg-pink-800/90
                                           dark:text-pink-300"
                                >
                                    SALE
                                </span>
                            </div>

                            <div
                                v-if="bundle.images_count"
                                class="absolute bottom-2 right-2 rounded-sm
                                       bg-slate-900/70 px-1.5 py-0.5
                                       text-[9px] font-semibold text-white"
                            >
                                {{ bundle.images_count }}
                            </div>
                        </div>

                        <!-- Основные данные -->
                        <div class="flex flex-1 flex-col space-y-1 p-2">
                            <a
                                :href="bundlePublicUrl(bundle)"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-center text-xs font-semibold
                                       text-sky-700 hover:text-amber-700
                                       hover:underline
                                       dark:text-sky-300 dark:hover:text-amber-300"
                                :title="bundleShort(bundle)
                                    || bundleSubtitle(bundle)
                                    || bundleTitle(bundle)"
                            >
                                {{ truncateText(bundleTitle(bundle), 80) }}
                            </a>

                            <div
                                v-if="bundleSubtitle(bundle)"
                                class="text-center text-[10px] font-semibold
                                       text-slate-700 dark:text-slate-300"
                            >
                                {{ truncateText(bundleSubtitle(bundle), 80) }}
                            </div>

                            <div
                                class="truncate text-center text-[10px] italic
                                       text-slate-500 dark:text-slate-400"
                                :title="bundle.url"
                            >
                                {{ bundle.url }}
                            </div>

                            <!-- Цена -->
                            <div class="text-center">
                                <div
                                    class="text-[9px] font-semibold uppercase
                                           tracking-wide"
                                    :class="bundle.calculate_price
                                        ? 'text-violet-600 dark:text-violet-300'
                                        : 'text-blue-600 dark:text-blue-300'"
                                >
                                    {{ bundle.calculate_price ? t('automaticPrice') : t('manualPrice') }}
                                </div>

                                <div class="flex items-center justify-center gap-2">
                                    <span
                                        v-if="bundle.has_old_price"
                                        class="text-xs text-slate-400 line-through"
                                    >
                                        {{ formatMoney(bundle.old_price, bundle.currency) }}
                                    </span>

                                    <span
                                        class="text-sm font-bold
                                               text-teal-700 dark:text-teal-300"
                                    >
                                        {{ formatMoney(effectivePrice(bundle), bundle.currency) }}
                                    </span>
                                </div>

                                <div
                                    v-if="safeNumber(bundle.saving_percent) > 0"
                                    class="text-[10px] font-semibold
                                           text-pink-600 dark:text-pink-300"
                                >
                                    −{{ bundle.saving_percent }}%
                                </div>
                            </div>

                            <!-- Наличие и состав -->
                            <div class="grid grid-cols-2 gap-1 text-center text-[10px]">
                                <div
                                    class="rounded-sm border border-fuchsia-300
                                           bg-fuchsia-50 px-1 py-1
                                           text-fuchsia-700
                                           dark:bg-fuchsia-900/30
                                           dark:text-fuchsia-300"
                                    :title="itemsTooltip(bundle)"
                                >
                                    <div class="font-semibold">{{ t('compound') }}</div>
                                    <div>
                                        {{ bundle.items_count ?? bundleItems(bundle).length }}
                                        <span
                                            v-if="bundle.active_items_count !== undefined"
                                            class="text-emerald-600 dark:text-emerald-300"
                                        >
                                            / {{ bundle.active_items_count }}
                                        </span>
                                    </div>
                                </div>

                                <div
                                    class="rounded-sm border px-1 py-1"
                                    :class="bundle.has_stock
                                        ? 'border-emerald-300 bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'
                                        : 'border-rose-300 bg-rose-50 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300'"
                                >
                                    <div class="font-semibold">
                                        {{ bundle.has_stock
                                        ? t('inStock')
                                        : t('outOfStock') }}
                                    </div>
                                    <div>
                                        {{ bundle.available_quantity ?? 0 }}
                                    </div>
                                </div>
                            </div>

                            <!-- Первые позиции состава -->
                            <div
                                v-if="bundleItems(bundle).length"
                                class="space-y-0.5 rounded-sm border
                                       border-slate-300 bg-white/60 p-1
                                       dark:border-slate-600 dark:bg-slate-900/30"
                            >
                                <div
                                    v-for="item in bundleItems(bundle).slice(0, 3)"
                                    :key="item.id"
                                    class="flex items-center justify-between gap-2
                                           text-[9px] text-slate-600
                                           dark:text-slate-300"
                                >
                                    <span class="truncate" :title="itemTitle(item)">
                                        {{ itemTitle(item) }}
                                    </span>

                                    <span class="shrink-0 font-semibold">
                                        × {{ item.quantity ?? 1 }}
                                    </span>
                                </div>

                                <div
                                    v-if="bundleItems(bundle).length > 3"
                                    class="text-center text-[9px]
                                           text-slate-400"
                                >
                                    + {{ bundleItems(bundle).length - 3 }}
                                </div>
                            </div>

                            <div
                                v-if="shopTitle(bundle) || companyTitle(bundle)"
                                class="truncate text-center text-[10px]
                                       text-amber-700 dark:text-amber-300"
                                :title="[companyTitle(bundle), shopTitle(bundle)]
                                    .filter(Boolean).join(' / ')"
                            >
                                {{ shopTitle(bundle) || companyTitle(bundle) }}
                            </div>

                            <div
                                v-if="bundleShort(bundle)"
                                class="line-clamp-3 text-center text-[11px]
                                       text-slate-600 dark:text-slate-300"
                            >
                                {{ truncateText(bundleShort(bundle), 150) }}
                            </div>

                            <!-- Статистика -->
                            <div
                                class="grid grid-cols-3 gap-1 text-center
                                       text-[10px] font-semibold"
                            >
                                <div class="flex flex-col items-center gap-1"
                                     :title="t('rating')">
                                    <svg
                                        class="w-3 h-3 fill-current shrink-0
                                                   text-red-500 dark:text-red-400"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"></path>
                                    </svg>
                                    <span>
                                {{ formatRating(bundle) }} ({{ safeNumber(bundle.rating_count) }})
                                    </span>
                                </div>

                                <div class="flex flex-col items-center gap-1"
                                     :title="t('views')">
                                    <svg
                                        class="w-3 h-3 fill-current shrink-0
                                                   text-blue-600 dark:text-blue-300"
                                        viewBox="0 0 16 16">
                                        <path
                                            d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"></path>
                                    </svg>
                                    <span>
                                            {{ safeNumber(bundle.views) }}
                                        </span>
                                </div>

                                <div class="flex flex-col items-center gap-1"
                                     :title="t('likes')">
                                    <svg
                                        class="w-3 h-3 fill-current shrink-0
                                                   text-rose-400 dark:text-rose-300"
                                        viewBox="0 0 24 24">
                                        <path d="M3,9H1a1,1,0,0,0-1,1V22a1,1,0,0,0,1,1H4V10A1,1,0,0,0,3,9Z"></path>
                                        <path
                                            d="M21.882,8.133A2.986,2.986,0,0,0,21,8H15V5c0-3.824-2.589-4.942-3.958-5a1.017,1.017,0,0,0-.734.277A1,1,0,0,0,10,1V5.638l-4,4.8V23H18.23A2.985,2.985,0,0,0,21.1,20.882l2.769-9A3,3,0,0,0,21.882,8.133Z"></path>
                                    </svg>
                                    <span>
                                         {{ safeNumber(bundle.likes_count) }}
                                        </span>
                                </div>
                            </div>

                            <div
                                class="text-center text-[11px] font-semibold
                                       text-fuchsia-700 dark:text-fuchsia-300"
                            >
                                {{ t('status') }}:
                                {{ statusLabel(bundle.status) }}
                            </div>

                            <div class="flex justify-center gap-1">
                                <span
                                    class="rounded-sm border px-2 py-1
                                           text-[10px] font-semibold"
                                    :class="moderationBadge(bundle.moderation_status).class"
                                >
                                    {{ moderationBadge(bundle.moderation_status).text }}
                                </span>

                                <ModerationButton
                                    :isAdmin="isAdmin"
                                    :status="bundle?.moderation_status ?? 0"
                                    :initialNote="bundle?.moderation_note || ''"
                                    mode="toggle"
                                    @submit="({ status, note }) =>
                                        emit('approve', bundle, status, note)"
                                />
                            </div>
                        </div>

                        <!-- Действия -->
                        <footer
                            class="border-t border-dashed border-slate-400
                                   px-3 py-2 dark:border-slate-500"
                        >
                            <div class="flex flex-wrap items-center justify-center gap-2">
                                <div class="flex items-center gap-1">
                                    <LeftToggle
                                        :isActive="bundle.left"
                                        :title="bundle.left ? t('enabled') : t('disabled')"
                                        @toggle-left="emit('toggle-left', bundle)"
                                    />
                                    <MainToggle
                                        :isActive="bundle.main"
                                        :title="bundle.main ? t('enabled') : t('disabled')"
                                        @toggle-main="emit('toggle-main', bundle)"
                                    />
                                    <RightToggle
                                        :isActive="bundle.right"
                                        :title="bundle.right ? t('enabled') : t('disabled')"
                                        @toggle-right="emit('toggle-right', bundle)"
                                    />
                                </div>
                                <div class="flex items-center gap-1">
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

                                <div class="flex items-center gap-1">
                                    <ActivityToggle
                                        :isActive="bundle.activity"
                                        :title="bundle.activity
                                            ? t('enabled')
                                            : t('disabled')"
                                        @toggle-activity="emit('toggle-activity', bundle)"
                                    />

                                    <IconEdit
                                        :href="route(
                                            'admin.marketProductBundles.edit',
                                            {
                                                marketProductBundle: bundle.id,
                                            }
                                        )"
                                    />

                                    <DeleteIconButton
                                        @delete="emit('delete', bundle)"
                                    />
                                </div>
                            </div>
                        </footer>
                    </article>
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
