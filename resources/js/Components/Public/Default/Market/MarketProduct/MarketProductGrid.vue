<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

import UniversalImageSlider from '@/Components/Public/Default/Images/UniversalImageSlider.vue'
import EntityStats from '@/Components/Public/Default/Stats/EntityStats.vue'

const { t } = useI18n()

/** Props */
const props = defineProps({
    products: { type: Array, default: () => [] },
    cols: { type: Number, default: 2 },
})

/**
 * Количество колонок карточек.
 *
 * 2 — оба сайдбара открыты.
 * 3 — свернут один.
 * 4 — свернуты оба.
 */
const gridClass = computed(() => {
    switch (props.cols) {
        case 4:
            return 'grid-cols-1 sm:grid-cols-2 xl:grid-cols-4'

        case 3:
            return 'grid-cols-1 sm:grid-cols-2 xl:grid-cols-3'

        case 2:
        default:
            return 'grid-cols-1 sm:grid-cols-2'
    }
})

/** Ссылка на товар */
const productLink = (product) => {
    return product?.url
        ? route('public.marketProducts.show', { url: product.url })
        : '#'
}

/** Название товара */
const getProductTitle = (product) => {
    return product?.title
        || product?.translation?.title
        || product?.current_translation?.title
        || product?.translations?.[0]?.title
        || ''
}

/** Краткое описание */
const getProductShort = (product) => {
    return product?.short
        || product?.translation?.short
        || product?.translation?.description
        || product?.current_translation?.short
        || product?.current_translation?.description
        || product?.translations?.[0]?.short
        || product?.translations?.[0]?.description
        || ''
}

/** Название бренда */
const getBrandTitle = (product) => {
    return product?.brand?.title || ''
}

/** Название магазина */
const getShopTitle = (product) => {
    return product?.shop?.title || ''
}

/** Название компании */
const getCompanyTitle = (product) => {
    return product?.company?.title
        || product?.company?.legal_name
        || ''
}

/** Название продавца */
const getSellerTitle = (product) => {
    return getShopTitle(product)
        || getCompanyTitle(product)
        || ''
}

/** Цена */
const getPrice = (product) => {
    const value = Number(product?.price)

    return Number.isFinite(value)
        ? value
        : 0
}

/** Старая цена */
const getOldPrice = (product) => {
    const value = Number(product?.old_price)

    return Number.isFinite(value)
        ? value
        : 0
}

/** Есть старая цена */
const hasOldPrice = (product) => {
    return getOldPrice(product) > getPrice(product)
}

/** Символ / код валюты */
const getCurrency = (product) => {
    return product?.currency?.symbol
        || product?.currency?.sign
        || product?.currency?.code
        || ''
}

/** Формат цены */
const formatPrice = (value) => {
    const number = Number(value)

    if (!Number.isFinite(number)) {
        return '0'
    }

    return new Intl.NumberFormat('ru-RU', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
    }).format(number)
}

/** Товар реально есть в наличии */
const hasStock = (product) => {
    return Boolean(
        product?.has_stock
        ?? (
            product?.in_stock
            && Number(product?.quantity ?? 0) > 0
        )
    )
}

/** Количество товара */
const quantity = (product) => {
    return Number(product?.quantity ?? 0)
}

/** Рейтинг */
const rating = (product) => {
    const value = Number(product?.rating_avg ?? 0)

    return Number.isFinite(value)
        ? value
        : 0
}

/** Количество отзывов */
const reviewsCount = (product) => {
    return Number(product?.reviews_count ?? 0)
}

/** Маркетинговые признаки */
const hasMarketingFlags = (product) => {
    return Boolean(
        product?.is_new
        || product?.is_hit
        || product?.is_sale
    )
}
</script>

<template>
    <div class="grid gap-4" :class="gridClass">
        <div
            v-for="product in products"
            :key="product.id"
            class="group flex h-full flex-col overflow-hidden rounded-md border border-gray-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-gray-700 dark:bg-gray-900"
        >
            <!-- Изображения -->
            <div class="relative">
                <Link :href="productLink(product)">
                    <UniversalImageSlider
                        :entity="product"
                        height-class="h-48"
                        rounded-class=""
                        wrapper-class=""
                        img-class="w-full h-full object-cover transition duration-300 group-hover:scale-105"
                    />
                </Link>

                <!-- Маркетинговые признаки -->
                <div
                    v-if="hasMarketingFlags(product)"
                    class="absolute left-2 top-2 z-10 flex flex-wrap gap-1"
                >
                    <span
                        v-if="product.is_new"
                        class="rounded-sm bg-emerald-600/90 px-2 py-1 text-[10px] font-bold uppercase tracking-wide text-white shadow-sm"
                    >
                        {{ t('new') }}
                    </span>

                    <span
                        v-if="product.is_hit"
                        class="rounded-sm bg-amber-500/90 px-2 py-1 text-[10px] font-bold uppercase tracking-wide text-white shadow-sm"
                    >
                        {{ t('hit') }}
                    </span>

                    <span
                        v-if="product.is_sale"
                        class="rounded-sm bg-red-500/90 px-2 py-1 text-[10px] font-bold uppercase tracking-wide text-white shadow-sm"
                    >
                        {{ t('sale') }}
                    </span>
                </div>
            </div>

            <div class="flex flex-1 flex-col p-4">
                <!-- Бренд -->
                <div
                    v-if="getBrandTitle(product)"
                    class="mb-2 flex items-center justify-center"
                >
                    <span
                        class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                    >
                        {{ getBrandTitle(product) }}
                    </span>
                </div>

                <!-- Название -->
                <div class="flex items-center justify-center text-center">
                    <Link
                        :href="productLink(product)"
                        class="inline-flex items-center gap-2"
                    >
                        <span
                            class="text-base font-semibold text-slate-900/85 group-hover:opacity-75 dark:text-slate-100/85 dark:group-hover:opacity-75"
                        >
                            {{ getProductTitle(product) }}
                        </span>
                    </Link>
                </div>

                <!-- Артикул -->
                <div
                    v-if="product.sku || product.vendor_code"
                    class="mt-1 flex items-center justify-center gap-2 text-[11px] text-slate-400 dark:text-slate-500"
                >
                    <span v-if="product.sku">
                        SKU: {{ product.sku }}
                    </span>

                    <span v-if="product.vendor_code">
                        {{ product.vendor_code }}
                    </span>
                </div>

                <!-- Краткое описание -->
                <div
                    v-if="getProductShort(product)"
                    class="mt-3 line-clamp-3 text-sm text-slate-700 dark:text-slate-300"
                >
                    {{ getProductShort(product) }}
                </div>

                <!-- Цена -->
                <div class="mt-4 flex flex-wrap items-end justify-center gap-x-2 gap-y-1">
                    <span class="text-xl font-bold text-slate-900 dark:text-slate-100">
                        {{ formatPrice(getPrice(product)) }}
                        {{ getCurrency(product) }}
                    </span>

                    <span
                        v-if="hasOldPrice(product)"
                        class="text-sm font-semibold text-slate-400 line-through dark:text-slate-500"
                    >
                        {{ formatPrice(getOldPrice(product)) }}
                        {{ getCurrency(product) }}
                    </span>
                </div>

                <!-- Данные -->
                <div
                    class="mt-3 flex flex-wrap items-center justify-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400"
                >
                    <!-- Наличие -->
                    <div
                        class="flex items-center justify-center gap-1 rounded-sm border border-slate-600 px-2 py-1 dark:border-slate-400"
                        :title="t('availability')"
                    >
                        <svg
                            class="h-3 w-3 shrink-0"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                            :class="hasStock(product)
                                ? 'text-emerald-600 dark:text-emerald-300'
                                : 'text-red-500 dark:text-red-300'"
                        >
                            <path
                                v-if="hasStock(product)"
                                d="M9 16.17 4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17Z"
                            />

                            <path
                                v-else
                                d="M18.3 5.71 12 12l6.3 6.29-1.41 1.42L10.59 13.41 4.29 19.71 2.88 18.3 9.17 12 2.88 5.71 4.29 4.29 10.59 10.59 16.89 4.29 18.3 5.71Z"
                            />
                        </svg>

                        <span>
                            {{ hasStock(product) ? t('inStock') : t('outOfStock') }}
                        </span>

                        <span v-if="hasStock(product) && quantity(product) > 0">
                            ({{ quantity(product) }})
                        </span>
                    </div>

                    <!-- Рейтинг -->
                    <div
                        v-if="rating(product) > 0"
                        class="flex items-center justify-center gap-1 rounded-sm border border-slate-600 px-2 py-1 dark:border-slate-400"
                        :title="t('rating')"
                    >
                        <svg
                            viewBox="0 0 24 24"
                            class="h-3 w-3 shrink-0"
                        >
                            <path
                                class="fill-current text-amber-500 dark:text-amber-300"
                                d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"
                            />
                        </svg>

                        {{ rating(product).toFixed(1) }}
                    </div>

                    <!-- Отзывы -->
                    <div
                        v-if="reviewsCount(product) > 0"
                        class="flex items-center justify-center gap-1 rounded-sm border border-slate-600 px-2 py-1 dark:border-slate-400"
                        :title="t('reviews')"
                    >
                        <svg
                            class="h-3 w-3 shrink-0 text-sky-600 dark:text-sky-300"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                        >
                            <path d="M4 3h16a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H9l-5 4v-4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm2 5v2h12V8H6Zm0 4v2h8v-2H6Z"/>
                        </svg>

                        {{ reviewsCount(product) }}
                    </div>
                </div>

                <!-- Продавец -->
                <div
                    v-if="getSellerTitle(product)"
                    class="mt-3 flex items-center justify-center gap-1 text-center text-xs font-semibold text-slate-600/85 dark:text-slate-300/85"
                >
                    <svg
                        class="h-3.5 w-3.5 shrink-0 text-violet-600/85 dark:text-violet-300/85"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                    >
                        <path d="M4 4h16l1 5a4 4 0 0 1-1 2.65V20h-6v-5h-4v5H4v-8.35A4 4 0 0 1 3 9l1-5Zm2 2-.6 3H18.6L18 6H6Z"/>
                    </svg>

                    <span class="line-clamp-1">
                        {{ getSellerTitle(product) }}
                    </span>
                </div>

                <!-- Статистика -->
                <div class="mt-3 flex items-center justify-center">
                    <EntityStats
                        :views="product.views || 0"
                        :likes-count="product.likes_count || 0"
                        :already-liked="product.already_liked || false"
                        route-name="public.marketProducts.like"
                        :route-params="product.id"
                        :show-likes-button="true"
                        compact
                    />
                </div>

                <!-- Кнопка -->
                <div class="mt-auto pt-4">
                    <Link
                        :href="productLink(product)"
                        class="flex w-full items-center justify-center gap-2 rounded-sm px-3 py-2 btn-default"
                    >
                        <span class="text-sm font-semibold">
                            {{ t('readMore') }}
                        </span>

                        <svg
                            class="h-4 w-4"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M7.21 14.77a.75.75 0 0 1 .02-1.06L10.94 10 7.23 6.29a.75.75 0 1 1 1.06-1.06l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06.02Z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
