<script setup>
import { defineProps, computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t, locale } = useI18n()

const props = defineProps({
    history: {
        type: Array,
        default: () => [],
    },
})

/**
 * Получение перевода товара.
 *
 * Сначала используем translation,
 * если он подготовлен Resource/model.
 *
 * Иначе ищем перевод текущей локали
 * среди translations.
 *
 * Последний fallback — первый доступный перевод.
 */
const productTranslation = (product) => {
    if (!product) {
        return null
    }

    if (product.translation) {
        return product.translation
    }

    const translations = Array.isArray(product.translations)
        ? product.translations
        : []

    return translations.find(
        (translation) => translation.locale === locale.value
    ) || translations[0] || null
}

/**
 * Название товара.
 */
const productTitle = (product) => {
    return productTranslation(product)?.title
        || product?.title
        || '—'
}

/**
 * Название бренда.
 */
const brandTitle = (product) => {
    if (!product?.brand) {
        return '—'
    }

    if (product.brand.translation?.title) {
        return product.brand.translation.title
    }

    const translations = Array.isArray(product.brand.translations)
        ? product.brand.translations
        : []

    return translations.find(
            (translation) => translation.locale === locale.value
        )?.title
        || translations[0]?.title
        || product.brand.title
        || '—'
}

/**
 * Форматирование даты просмотра.
 */
const formatDate = (dateString) => {
    if (!dateString) {
        return '—'
    }

    const date = new Date(dateString)

    if (Number.isNaN(date.getTime())) {
        return '—'
    }

    return new Intl.DateTimeFormat(
        locale.value || 'ru-RU',
        {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        }
    ).format(date)
}

/**
 * Цена товара.
 */
const productPrice = (product) => {
    if (!product) {
        return '—'
    }

    const value =
        product.price
        ?? product.current_price
        ?? product.base_price

    if (
        value === null
        || value === undefined
        || value === ''
    ) {
        return '—'
    }

    const number = Number(value)

    if (!Number.isFinite(number)) {
        return value
    }

    return new Intl.NumberFormat(
        locale.value || 'ru-RU',
        {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2,
        }
    ).format(number)
}

/**
 * Символ/код валюты.
 */
const currency = (product) => {
    return product?.currency?.symbol
        || product?.currency?.code
        || ''
}

/**
 * Есть ли данные для таблицы.
 */
const hasHistory = computed(() => {
    return props.history.length > 0
})
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-200 dark:border-slate-600 relative"
    >
        <div class="overflow-x-auto">
            <table
                v-if="hasHistory"
                class="table-auto w-full
                       text-slate-700 dark:text-slate-100"
            >
                <thead
                    class="text-sm uppercase
                           bg-slate-200 dark:bg-cyan-900
                           border border-solid
                           border-gray-300 dark:border-gray-700"
                >
                <tr>
                    <!-- ID истории -->
                    <th
                        class="px-2 first:pl-5 last:pr-5 py-3
                                   whitespace-nowrap w-px"
                    >
                        <div class="font-medium text-center">
                            {{ t('id') }}
                        </div>
                    </th>

                    <!-- Товар -->
                    <th
                        class="px-2 first:pl-5 last:pr-5 py-3
                                   whitespace-nowrap"
                    >
                        <div class="font-medium text-left">
                            {{ t('marketProduct') }}
                        </div>
                    </th>

                    <!-- Артикул -->
                    <th
                        class="px-2 first:pl-5 last:pr-5 py-3
                                   whitespace-nowrap"
                    >
                        <div class="font-medium text-center">
                            SKU
                        </div>
                    </th>

                    <!-- Бренд -->
                    <th
                        class="px-2 first:pl-5 last:pr-5 py-3
                                   whitespace-nowrap"
                    >
                        <div class="font-medium text-center">
                            {{ t('brand') }}
                        </div>
                    </th>

                    <!-- Цена -->
                    <th
                        class="px-2 first:pl-5 last:pr-5 py-3
                                   whitespace-nowrap"
                    >
                        <div class="font-medium text-end">
                            {{ t('price') }}
                        </div>
                    </th>

                    <!-- Дата просмотра -->
                    <th
                        class="px-2 first:pl-5 last:pr-5 py-3
                                   whitespace-nowrap"
                    >
                        <div class="font-medium text-center">
                            {{ t('viewedAt') }}
                        </div>
                    </th>
                </tr>
                </thead>

                <tbody>
                <tr
                    v-for="item in history"
                    :key="item.id"
                    class="text-sm font-semibold border-b-2
                               hover:bg-slate-100
                               dark:hover:bg-cyan-800"
                >
                    <!-- ID истории -->
                    <td
                        class="px-2 first:pl-5 last:pr-5 py-2
                                   whitespace-nowrap"
                    >
                        <div class="text-center">
                            {{ item.id }}
                        </div>
                    </td>

                    <!-- Товар -->
                    <td
                        class="px-2 first:pl-5 last:pr-5 py-2
                                   min-w-64"
                    >
                        <div class="flex flex-col">
                            <div
                                class="mt-0.5 text-xs
                                           text-slate-500 dark:text-slate-300"
                            >
                                ID:
                                {{ item.market_product_id }}
                            </div>
                            <div
                                class="font-semibold
                                       text-amber-700 dark:text-amber-300"
                            >
                                {{ productTitle(item.product) }}
                            </div>
                        </div>
                    </td>

                    <!-- SKU -->
                    <td
                        class="px-2 first:pl-5 last:pr-5 py-2
                               whitespace-nowrap"
                    >
                        <div class="text-center text-xs">
                            {{ item.product?.sku || '—' }}
                        </div>
                    </td>

                    <!-- Бренд -->
                    <td
                        class="px-2 first:pl-5 last:pr-5 py-2"
                    >
                        <div
                            class="text-center text-blue-700 dark:text-blue-300"
                        >
                            {{ brandTitle(item.product) }}
                        </div>
                    </td>

                    <!-- Цена -->
                    <td
                        class="px-2 first:pl-5 last:pr-5 py-2
                               whitespace-nowrap"
                    >
                        <div class="text-end text-teal-700 dark:text-teal-300">
                            {{ productPrice(item.product) }}

                            <span
                                v-if="currency(item.product)"
                                class="ml-1 text-xs"
                            >
                                    {{ currency(item.product) }}
                                </span>
                        </div>
                    </td>

                    <!-- Просмотрен -->
                    <td
                        class="px-2 first:pl-5 last:pr-5 py-2
                                whitespace-nowrap"
                    >
                        <div class="text-center">
                                <span
                                    class="inline-flex px-2 py-1
                                           rounded-sm border
                                           border-indigo-300
                                           bg-indigo-50
                                           text-xs font-semibold
                                           text-indigo-800
                                           dark:border-indigo-700
                                           dark:bg-indigo-900/40
                                           dark:text-indigo-200"
                                >
                                    {{ formatDate(item.viewed_at) }}
                                </span>
                        </div>
                    </td>
                </tr>
                </tbody>
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
