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
        localProducts.value = JSON.parse(
            JSON.stringify(newValue || [])
        )
    },
    { immediate: true, deep: true, }
)

/** Передача нового порядка товаров родительскому компоненту. */
const handleDragEnd = () => {
    emit(
        'update-sort-order',
        localProducts.value.map((product) => product.id)
    )
}

/* ======================== Selection ======================== */

/** Выбор всех товаров, отображаемых в таблице. */
const toggleAll = (event) => {
    emit('toggle-all', {
        ids: localProducts.value.map((product) => product.id),
        checked: event.target.checked,
    })
}

/** Проверка выбора всех товаров. */
const allProductsSelected = () => {
    if (!localProducts.value.length) {
        return false
    }

    return localProducts.value.every((product) => {
        return props.selectedProducts.includes(product.id)
    })
}

/* ======================== Translations ======================== */

/** Текущий перевод товара. */
const productTranslation = (product) => {
    return product?.translation
        || product?.translations?.[0]
        || {}
}

/** Название товара. */
const productTitle = (product) => {
    return productTranslation(product)?.title
        || `ID: ${product?.id}`
}

/** Подзаголовок товара. */
const productSubtitle = (product) => {
    return productTranslation(product)?.subtitle || ''
}

/** Краткое описание товара. */
const productShort = (product) => {
    return productTranslation(product)?.short || ''
}

/** Получение заголовка переведённой связанной сущности. */
const relationTitle = (relation) => {
    return relation?.translation?.title
        || relation?.title
        || relation?.translations?.[0]?.title
        || ''
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
    return category?.translation?.title
        || category?.title
        || category?.translations?.[0]?.title
        || `ID: ${category?.id}`
}

/* ======================== Images ======================== */

/** Первое изображение товара с учётом order. */
const getPrimaryImage = (product) => {
    if (!Array.isArray(product?.images) || !product.images.length ) {
        return null
    }

    return [...product.images].sort((left, right) => {
        return Number(left?.order ?? 0)
            - Number(right?.order ?? 0)
    })[0]
}

/** URL изображения товара.  */
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

/** Подсказка владельца товара.  */
const ownerTitle = (product) => {
    const owner = product?.owner

    if (!owner) {
        return t('noData')
    }

    return `${owner.name || ''}${
        owner.email ? ` — ${owner.email}` : ''
    }`.trim()
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
const truncateText = (text, maxLength = 50) => {
    if (!text) {
        return ''
    }

    const normalizedText = String(text)

    return normalizedText.length > maxLength
        ? `${normalizedText.slice(0, maxLength).trimEnd()}…`
        : normalizedText
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

    const thousandsSep = currency?.thousands_sep ?? ' '
    const decimalSep = currency?.decimal_sep ?? '.'

    const parts = amount
        .toFixed(precision)
        .split('.')

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

/** Название основной категории. */
const mainCategoryTitle = (product) => {
    if (!Array.isArray(product?.categories)) {
        return ''
    }

    const mainCategory = product.categories.find((category) => {
        return Boolean(category?.pivot?.main)
            || Boolean(category?.main)
    })

    return mainCategory
        ? categoryTitle(mainCategory)
        : categoryTitle(product.categories[0])
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
            const attribute = item?.attribute?.title
                || `ID: ${item?.market_attribute_id}`

            const value = item?.attribute_value?.title
                || item?.value_string
                || item?.value_number
                || (
                    item?.value_boolean === true
                        ? t('yes')
                        : item?.value_boolean === false
                            ? t('no')
                            : null
                )
                || item?.value_date
                || ''

            const unit = item?.unit
                ? ` ${item.unit}`
                : ''

            return value
                ? `${attribute}: ${value}${unit}`
                : attribute
        })
        .filter(Boolean)
        .join('\n')
}

/** Подсказка публикации. */
const publicationTitle = (product) => {
    const values = [
        `Sort: ${product?.sort ?? 0}`,
    ]

    if (product?.published_at) {
        values.push(
            `Публикация: ${formatDate(product.published_at)}`
        )
    }

    if (product?.show_from_at) {
        values.push(
            `Показ с: ${formatDate(product.show_from_at)}`
        )
    }

    if (product?.show_to_at) {
        values.push(
            `Показ до: ${formatDate(product.show_to_at)}`
        )
    }

    return values.join('\n')
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
                    :checked="allProductsSelected() || allSelected"
                    @change="toggleAll"
                />
            </label>
        </div>

        <div class="overflow-x-auto">
            <table
                v-if="localProducts.length"
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
                    <th class="w-px px-1 py-3">
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
                    <th class="w-px whitespace-nowrap px-1 py-3">
                        <div class="text-center font-semibold">
                            {{ t('id') }}
                        </div>
                    </th>

                    <!-- Владелец -->
                    <th class="w-px whitespace-nowrap px-1 py-3">
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
                    <th class="w-px px-1 py-3">
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

                    <!-- Товар -->
                    <th class="min-w-64 px-2 py-3">
                        <div class="text-left font-semibold">
                            {{ t('marketProduct') }}
                        </div>
                    </th>

                    <!-- Цена -->
                    <th class="whitespace-nowrap px-2 py-3">
                        <div class="text-right font-semibold">
                            {{ t('price') }}
                        </div>
                    </th>

                    <!-- Остаток -->
                    <th class="whitespace-nowrap px-2 py-3">
                        <div class="text-center font-semibold">
                            {{ t('quantity') }}
                        </div>
                    </th>

                    <!-- Средняя оценка рейтинга -->
                    <th class="whitespace-nowrap px-2 py-3">
                        <div class="flex justify-center" :title="t('ratingAvg')">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24" width="24"
                                 viewBox="0 0 24 24" class="shrink-0 h-4 w-4">
                                <path class="fill-current text-red-400 dark:text-red-300"
                                      d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"></path>
                            </svg>
                        </div>
                    </th>

                    <!-- Отзывы -->
                    <th class="whitespace-nowrap px-2 py-3">
                        <div class="flex justify-center" :title="t('reviews')">
                            <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 512 512">
                                <path class="fill-current text-sky-700 dark:text-sky-300"
                                      d="M256 32C114.62 32 0 125.12 0 240c0 49.56 21.41 95.01 57.02 130.74C44.46 421.05 2.7 465.97 2.2 466.5A7.995 7.995 0 0 0 8 480c66.26 0 115.99-31.75 140.6-51.38C181.29 440.93 217.59 448 256 448c141.38 0 256-93.12 256-208S397.38 32 256 32zm24 302.44V352c0 8.84-7.16 16-16 16h-16c-8.84 0-16-7.16-16-16v-17.73c-11.42-1.35-22.28-5.19-31.78-11.46-6.22-4.11-6.82-13.11-1.55-18.38l17.52-17.52c3.74-3.74 9.31-4.24 14.11-2.03 3.18 1.46 6.66 2.22 10.26 2.22h32.78c4.66 0 8.44-3.78 8.44-8.42 0-3.75-2.52-7.08-6.12-8.11l-50.07-14.3c-22.25-6.35-40.01-24.71-42.91-47.67-4.05-32.07 19.03-59.43 49.32-63.05V128c0-8.84 7.16-16 16-16h16c8.84 0 16 7.16 16 16v17.73c11.42 1.35 22.28 5.19 31.78 11.46 6.22 4.11 6.82 13.11 1.55 18.38l-17.52 17.52c-3.74 3.74-9.31 4.24-14.11 2.03a24.516 24.516 0 0 0-10.26-2.22h-32.78c-4.66 0-8.44 3.78-8.44 8.42 0 3.75 2.52 7.08 6.12 8.11l50.07 14.3c22.25 6.36 40.01 24.71 42.91 47.67 4.05 32.06-19.03 59.42-49.32 63.04z" />
                            </svg>
                        </div>
                    </th>

                    <!-- Просмотры -->
                    <th class="whitespace-nowrap px-2 py-3">
                        <div class="flex justify-center" :title="t('views')">
                            <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16">
                                <path class="fill-current text-blue-600 dark:text-blue-300"
                                      d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"
                                />
                            </svg>
                        </div>
                    </th>

                    <!-- Лайки -->
                    <th class="whitespace-nowrap px-2 py-3">
                        <div class="flex justify-center" :title="t('likes')">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24" width="24"
                                 viewBox="0 0 24 24" class="shrink-0 h-4 w-4">
                                <path class="fill-current text-red-400 dark:text-red-300"
                                      d="M3,9H1a1,1,0,0,0-1,1V22a1,1,0,0,0,1,1H4V10A1,1,0,0,0,3,9Z"></path>
                                <path class="fill-current text-red-400 dark:text-red-300"
                                      d="M21.882,8.133A2.986,2.986,0,0,0,21,8H15V5c0-3.824-2.589-4.942-3.958-5a1.017,1.017,0,0,0-.734.277A1,1,0,0,0,10,1V5.638l-4,4.8V23H18.23A2.985,2.985,0,0,0,21.1,20.882l2.769-9A3,3,0,0,0,21.882,8.133Z"></path>
                            </svg>
                        </div>
                    </th>

                    <!-- Рекламные позиции, Статус -->
                    <th class="whitespace-nowrap px-2 py-3">
                        <div class="text-center font-medium">
                            {{ t('show') }}
                        </div>
                    </th>

                    <!-- Действия -->
                    <th class="whitespace-nowrap px-2 py-3">
                        <div class="text-right font-semibold">
                            {{ t('actions') }}
                        </div>
                    </th>

                    <!-- Checkbox -->
                    <th class="w-px whitespace-nowrap px-1 py-1 text-center">
                        <input
                            type="checkbox"
                            :checked="allProductsSelected() || allSelected"
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
                                    class="text-center
                                           text-blue-600 dark:text-blue-200"
                                    :title="publicationTitle(product)"
                                >
                                    {{ product.id }}
                                </div>
                            </td>

                            <!-- Владелец -->
                            <td class="w-px px-1 py-1">
                                <div class="flex justify-center">
                                    <img
                                        :src="ownerAvatar(product)"
                                        :title="ownerTitle(product)"
                                        :alt="t('owner')"
                                        class="h-7 w-7 rounded-full
                                               border border-slate-300
                                               object-cover
                                               dark:border-slate-600"
                                    />
                                </div>
                            </td>

                            <!-- Изображение -->
                            <td class="w-16 px-1 py-1">
                                <div class="flex justify-center">
                                    <img
                                        :src="imageUrl(product)"
                                        :alt="imageAlt(product)"
                                        :title="imageTitle(product)"
                                        class="h-10 w-14 rounded-sm
                                               border border-slate-300
                                               object-cover
                                               dark:border-slate-600"
                                    />
                                </div>

                                <div
                                    v-if="product.images_count"
                                    class="mt-0.5 text-center text-[9px]
                                           text-slate-500 dark:text-slate-300"
                                >
                                    {{ product.images_count }}
                                </div>
                            </td>

                            <!-- Товар -->
                            <td class="px-2 py-1">
                                <div class="text-left">

                                    <a
                                        :href="productPublicUrl(product)"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="text-xs
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
                                        class="text-[10px]
                                               text-slate-700 dark:text-slate-300"
                                    >
                                        {{ truncateText(productSubtitle(product), 75) }}
                                    </div>

                                    <!-- URL -->
                                    <div
                                        class="mt-0.5 text-[10px] italic
                                               text-slate-500 dark:text-slate-400"
                                    >
                                        {{ truncateText(product.url, 70) }}
                                    </div>

                                    <!-- Категория -->
                                    <div
                                        v-if="mainCategoryTitle(product)"
                                        class="mt-1 flex items-center gap-1
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

                                    <!-- Характеристика -->
                                    <div
                                        v-if="product.attribute_values_count"
                                        class="mt-0.5 flex items-center gap-1
                                               text-[9px] text-fuchsia-700 dark:text-fuchsia-300"
                                        :title="attributesTitle(product)"
                                    >
                                        <svg class="h-3 w-3 shrink-0 fill-current"
                                             viewBox="0 0 512 512">
                                            <path d="M395.198 256c3.461-10.526 18.796-21.28 36.265-32.425 16.625-10.605 35.467-22.626 50.341-38.862 17.458-19.054 25.944-40.175 25.944-64.567 0-60.562-50.702-88.146-97.81-88.146-42.491 0-76.378 22.016-94.432 50.447-4.654 7.329-2.592 17.036 4.623 21.865l30.328 20.296c7.032 4.706 16.46 3.084 21.63-3.614 8.022-10.394 18.818-18.225 31.667-18.225 19.387 0 26.266 12.901 26.266 23.948 0 36.159-119.437 57.023-119.437 160.024 0 6.654.561 13.014 1.415 19.331 1.076 7.964 7.834 13.928 15.87 13.928H496c8.837 0 16-7.163 16-16v-32c0-8.837-7.163-16-16-16H395.198zM272 416c8.837 0 16 7.163 16 16v32c0 8.837-7.163 16-16 16h-62.399a16 16 0 0 1-13.541-7.478l-45.701-72.615c-2.297-3.352-4.422-6.969-6.195-10.209-1.65 3.244-3.647 6.937-5.874 10.582l-44.712 72.147a15.999 15.999 0 0 1-13.6 7.572H16c-8.837 0-16-7.163-16-16v-32c0-8.837 7.163-16 16-16h26.325l56.552-82.709L46.111 256H16c-8.837 0-16-7.163-16-16v-32c0-8.837 7.163-16 16-16h68.806a16 16 0 0 1 13.645 7.644l39.882 65.126c2.072 3.523 4.053 7.171 5.727 10.37 1.777-3.244 3.92-6.954 6.237-10.537l40.332-65.035a16 16 0 0 1 13.598-7.567H272c8.837 0 16 7.163 16 16v32c0 8.837-7.163 16-16 16h-27.979l-52.69 75.671L249.974 416H272z"></path>
                                        </svg>
                                        {{ t('attributes') }}:
                                        {{ product.attribute_values_count }}
                                    </div>

                                    <!-- Магазин -->
                                    <div
                                        v-if="shopTitle(product)"
                                        class="mt-1 flex items-center gap-1
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
                                        class="mt-1 text-[10px] text-slate-400"
                                        :title="supplierTitle(product)"
                                    >
                                        {{ t('noData') }}
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

                                    <div class="mt-0.5 flex flex-wrap gap-1 text-[9px]">
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

                                </div>
                            </td>

                            <!-- Цена -->
                            <td class="whitespace-nowrap px-2 py-1">
                                <div class="text-right">
                                    <div
                                        class="text-sm font-bold
                                               text-teal-700 dark:text-teal-300"
                                    >
                                        {{ formatMoney(product.price, product.currency) }}
                                    </div>

                                    <div
                                        v-if="product.old_price"
                                        class="text-sm
                                               text-slate-400 line-through"
                                    >
                                        {{ formatMoney(product.old_price, product.currency) }}
                                    </div>

                                    <div
                                        v-if="product.wholesale_price"
                                        class="mt-0.5 text-[10px]
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
                                </div>
                            </td>

                            <!-- Остаток -->
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
                                        {{ product.in_stock
                                        ? t('inStock')
                                        : t('outOfStock') }}
                                    </span>
                                </div>
                            </td>

                            <!-- Средняя оценка рейтинга -->
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div
                                    class="text-center text-xs
                                           text-rose-500 dark:text-rose-300"
                                    :title="t('rating')">
                                    {{ formatRating(product) }}
                                </div>
                            </td>

                            <!-- Отзывы -->
                            <td class="whitespace-nowrap px-2 py-1">
                                <div
                                    class="text-center text-xs
                                           text-sky-700 dark:text-sky-300"
                                    :title="t('reviews')"
                                >
                                    {{ product.reviews_count ?? product.rating_count ?? 0 }}
                                </div>
                            </td>

                            <!-- Просмотры -->
                            <td class="whitespace-nowrap px-2 py-1">
                                <div class="text-center text-xs
                                            text-blue-700 dark:text-blue-300"
                                     :title="t('views')">
                                    {{ product.views ?? 0 }}
                                </div>
                            </td>

                            <!-- Лайки -->
                            <td class="whitespace-nowrap px-2 py-1">
                                <div class="text-center text-xs"
                                     :title="t('likes')">
                                    {{ product.likes_count ?? 0 }}
                                </div>
                            </td>

                            <!-- Рекламные позиции -->
                            <td class="whitespace-nowrap px-2 py-1">
                                <div class="flex flex-col justify-center items-center gap-1">
                                    <div class="flex flex-row items-center justify-center gap-1">
                                        <LeftToggle
                                            :isActive="product.left"
                                            :title="product.left
                                                ? t('enabled')
                                                : t('disabled')"
                                            @toggle-left="emit('toggle-left', product)"
                                        />
                                        <MainToggle
                                            :isActive="product.main"
                                            :title="product.main
                                                ? t('enabled')
                                                : t('disabled')"
                                            @toggle-main="emit('toggle-main', product)"
                                        />
                                        <RightToggle
                                            :isActive="product.right"
                                            :title="product.right
                                                ? t('enabled')
                                                : t('disabled')"
                                            @toggle-right="emit('toggle-right', product)"
                                        />
                                    </div>
                                    <div class="flex flex-row items-center justify-center gap-1">
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
                                    <!-- Статус и модерация -->
                                    <div class="flex flex-row items-center justify-center gap-1">
                                        <div
                                            class="flex flex-col items-center
                                           justify-center gap-1"
                                        >
                                            <span
                                                class="px-2 py-1 text-[10px] font-semibold
                                                       text-fuchsia-700 dark:text-fuchsia-300">
                                                {{ statusBadge(product.status).text }}
                                            </span>
                                            <div
                                                class="flex items-center
                                               justify-center gap-1"
                                            >
                                        <span
                                            class="rounded-sm border
                                                   px-2 py-1 text-[9px]
                                                   font-semibold"
                                            :class="moderationBadge(product.moderation_status).class"
                                            :title="product.moderation_note
                                                ? `${product.moderation_note}${
                                                    product.moderated_at
                                                        ? ` [${formatDate(
                                                            product.moderated_at
                                                        )}]`
                                                        : ''
                                                }`
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
                                                    emit('approve',
                                                        product,
                                                        status,
                                                        note
                                                    )"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Действия -->
                            <td class="whitespace-nowrap px-2 py-1">
                                <div class="flex flex-row items-center justify-center gap-1">
                                    <ActivityToggle
                                        :isActive="product.activity"
                                        :title="product.activity
                                                ? t('enabled')
                                                : t('disabled')"
                                        @toggle-activity="
                                                emit(
                                                    'toggle-activity',
                                                    product
                                                )
                                            "
                                    />
                                    <IconEdit
                                        :href="route('admin.marketProducts.edit',
                                                { marketProduct: product.id,})"
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
                                        :checked="
                                            selectedProducts.includes(
                                                product.id
                                            )
                                        "
                                        @change="
                                            emit(
                                                'toggle-select',
                                                product.id
                                            )
                                        "
                                    />
                                </div>
                            </td>
                        </tr>
                    </template>
                </draggable>
            </table>

            <div
                v-else
                class="p-5 text-center
                       text-slate-700 dark:text-slate-100"
            >
                {{ t('noData') }}
            </div>
        </div>
    </div>
</template>
