<script setup>
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

import UniversalImageSlider from '@/Components/Public/Default/Images/UniversalImageSlider.vue'
import EntityStats from '@/Components/Public/Default/Stats/EntityStats.vue'
import LikeButtonEntity from '@/Components/Public/Like/LikeButtonEntity.vue'

const { t } = useI18n()

/** Props */
defineProps({
    products: {
        type: Array,
        default: () => [],
    },
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

/** Валюта */
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

/** Товар есть в наличии */
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

/** Есть маркетинговые признаки */
const hasMarketingFlags = (product) => {
    return Boolean(
        product?.is_new
        || product?.is_hit
        || product?.is_sale
    )
}
</script>

<template>
    <div class="space-y-4">
        <div
            v-for="product in products"
            :key="product.id"
            class="group flex flex-col gap-3 rounded-md border border-gray-200
                   bg-white p-3 shadow-sm transition hover:shadow-md dark:border-gray-700
                   dark:bg-gray-900 sm:flex-row"
        >
            <!-- Изображения -->
            <div class="relative shrink-0">
                <Link :href="productLink(product)">
                    <UniversalImageSlider
                        :entity="product"
                        height-class="h-44"
                        rounded-class="rounded-md"
                        wrapper-class="w-full sm:w-60 border border-gray-400 dark:border-gray-600"
                        img-class="w-full h-full object-cover transition
                                   duration-300 group-hover:scale-105"
                    />
                </Link>

                <!-- Маркетинговые признаки -->
                <div
                    v-if="hasMarketingFlags(product)"
                    class="absolute left-2 top-2 z-10 flex flex-wrap gap-1"
                >
                    <span
                        v-if="product.is_new"
                        class="rounded-sm bg-emerald-600/70 px-1 py-0.5
                               text-[9px] font-bold uppercase tracking-wide
                               text-white shadow-sm"
                    >
                        {{ t('new') }}
                    </span>

                    <span
                        v-if="product.is_hit"
                        class="rounded-sm bg-amber-500/70 px-1 py-0.5
                               text-[9px] font-bold uppercase tracking-wide
                               text-white shadow-sm"
                    >
                        {{ t('hit') }}
                    </span>

                    <span
                        v-if="product.is_sale"
                        class="rounded-sm bg-red-500/70 px-1 py-0.5
                               text-[9px] font-bold uppercase tracking-wide
                               text-white shadow-sm"
                    >
                        {{ t('sale') }}
                    </span>
                </div>
            </div>

            <!-- Основная информация -->
            <div class="min-w-0 flex-1 flex flex-col justify-around ml-7">

                <!-- Верхняя строка -->
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">

                        <!-- Бренд -->
                        <div
                            v-if="getBrandTitle(product)"
                            class="mb-1 text-xs font-semibold uppercase tracking-wide
                                   text-slate-500 dark:text-slate-400"
                        >
                            {{ getBrandTitle(product) }}
                        </div>

                        <!-- Название -->
                        <Link
                            :href="productLink(product)"
                            class="inline-flex min-w-0 items-center gap-2"
                        >
                            <span
                                class="text-lg font-semibold text-slate-900/85
                                       group-hover:opacity-75 dark:text-slate-100/85"
                            >
                                {{ getProductTitle(product) }}
                            </span>
                        </Link>
                    </div>

                    <!-- Цена -->
                    <div class="shrink-0 text-right">
                        <div class="text-lg font-bold text-teal-600 dark:text-teal-400">
                            {{ formatPrice(getPrice(product)) }}
                            {{ getCurrency(product) }}
                        </div>

                        <div
                            v-if="hasOldPrice(product)"
                            class="text-sm font-semibold text-slate-400 line-through
                                   dark:text-slate-500"
                        >
                            {{ formatPrice(getOldPrice(product)) }}
                            {{ getCurrency(product) }}
                        </div>

                        <!-- Наличие -->
                        <div
                            class="flex items-center justify-center gap-1 mt-1
                                   text-[10px] font-semibold text-gray-500 dark:text-gray-400"
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
                        {{ hasStock(product) ? t('remainder') : t('outOfStock') }}
                    </span>
                            <span v-if="hasStock(product) && quantity(product) > 0">
                        [{{ quantity(product) }}]
                    </span>
                        </div>
                    </div>
                </div>

                <!-- Краткое описание -->
                <div
                    v-if="getProductShort(product)"
                    class="mt-2 text-sm text-slate-700 dark:text-slate-300"
                >
                    {{ getProductShort(product) }}
                </div>

                <!-- Артикулы -->
                <div
                    v-if="product.sku || product.vendor_code"
                    class="mt-2 flex flex-wrap items-center gap-2 text-[10px]
                           text-slate-400 dark:text-slate-500"
                >
                    <span v-if="product.sku">
                        SKU: {{ product.sku }}
                    </span>

                    <span v-if="product.vendor_code">
                        {{ product.vendor_code }}
                    </span>
                </div>

                <div class="flex items-center justify-between gap-2">
                    <!-- Данные -->
                    <div
                        class="mt-3 flex flex-wrap items-center justify-center gap-2
                           text-xs font-semibold text-slate-500 dark:text-slate-400"
                    >

                        <!-- Варианты -->
                        <div
                            v-if="product.variants_count"
                            class="flex items-center justify-center gap-1 rounded-sm
                               border border-slate-400 px-2 py-1"
                            :title="t('variants')"
                        >
                            <svg
                                class="h-3 w-3 shrink-0 text-violet-600 dark:text-violet-300"
                                viewBox="0 0 24 24"
                                fill="currentColor"
                            >
                                <path d="M4 4h6v6H4V4Zm10 0h6v6h-6V4ZM4 14h6v6H4v-6Zm10 0h6v6h-6v-6Z"/>
                            </svg>

                            {{ product.variants_count }}
                        </div>

                        <!-- Просмотры -->
                        <div
                            v-if="product.views > 0"
                            class="flex items-center justify-center gap-1 rounded-sm
                               border border-slate-400 px-2 py-1"
                            :title="t('views')"
                        >
                            <svg class="h-3 w-3 text-blue-600/85 dark:text-blue-200/85"
                                 viewBox="0 0 576 512" fill="currentColor">
                                <path
                                    d="M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z"></path>
                            </svg>

                            {{ product.views }}
                        </div>

                        <!-- Рейтинг -->
                        <div
                            v-if="rating(product) > 0"
                            class="flex items-center justify-center gap-1 rounded-sm
                               border border-slate-400 px-2 py-1"
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
                            class="flex items-center justify-center gap-1 rounded-sm
                               border border-slate-400 px-2 py-1"
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

                        <!-- Лайк товара -->
                        <div class="mt-1 flex items-center justify-center">
                            <LikeButtonEntity
                                :likes-count="product.likes_count || 0"
                                :already-liked="product.already_liked || false"
                                route-name="public.marketProducts.like"
                                :route-params="{ id: product.id }"
                                icon-class="w-3 h-3 hover:scale-110 active:scale-95"
                            />
                        </div>

                    </div>

                    <!-- Ссылка -->
                    <Link
                        :href="productLink(product)"
                        class="flex w-fit items-center justify-center gap-2
                               rounded-sm px-3 py-1 btn-default"
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
