<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

import UniversalImageSlider
    from '@/Components/Public/Default/Images/UniversalImageSlider.vue'

const { t } = useI18n()

/** Props */
const props = defineProps({
    brands: {
        type: Array,
        default: () => [],
    },

    cols: {
        type: Number,
        default: 2,
    },

    /** Начальная позиция списка Schema.org */
    startPosition: {
        type: Number,
        default: 0,
    },
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

/* ===================== BRAND ===================== */

/** Ссылка на бренд */
const brandLink = (brand) => {
    return brand?.url
        ? route(
            'public.marketBrands.show',
            {
                url: brand.url,
            }
        )
        : '#'
}

/** Абсолютная ссылка бренда для Schema.org */
const brandAbsoluteUrl = (brand) => {
    return brand?.url
        ? route(
            'public.marketBrands.show',
            {
                url: brand.url,
            }
        )
        : ''
}

/**
 * Название бренда.
 *
 * Перевод уже разрешён Laravel:
 * current → fallback → null.
 */
const getBrandTitle = (brand) => {
    return brand?.translation?.title || ''
}

/** Краткое описание бренда */
const getBrandShort = (brand) => {
    return brand?.translation?.short || ''
}

/* ===================== IMAGES ===================== */

/** Нормализованный список изображений бренда */
const getBrandImages = (brand) => {
    if (Array.isArray(brand?.images)) {
        return brand.images
    }

    if (Array.isArray(brand?.images?.data)) {
        return brand.images.data
    }

    return []
}

/** Есть изображения бренда */
const hasBrandImages = (brand) => {
    return getBrandImages(brand).length > 0
}

/**
 * Первое реальное изображение бренда.
 *
 * Используется только для Schema.org.
 * Визуальный вывод выполняет UniversalImageSlider.
 */
const getBrandImage = (brand) => {
    const image = getBrandImages(brand)[0]

    return image?.webp_url
        || image?.image_url
        || image?.thumb_url
        || image?.url
        || ''
}

/**
 * URL логотипа.
 *
 * Если логотип отсутствует —
 * ничего не подставляем.
 */
const getBrandLogo = (brand) => {
    const logo = brand?.logo

    if (!logo) {
        return ''
    }

    const value = String(logo).trim()

    if (!value) {
        return ''
    }

    if (
        value.startsWith('http://')
        || value.startsWith('https://')
        || value.startsWith('/storage/')
    ) {
        return value
    }

    return `/storage/${value}`
}

/* ===================== COUNTS ===================== */

/** Количество публичных товаров */
const getProductsCount = (brand) => {
    const value = Number(
        brand?.products_count ?? 0
    )

    return Number.isFinite(value)
        ? value
        : 0
}

/** Количество просмотров */
const getViews = (brand) => {
    const value = Number(
        brand?.views ?? 0
    )

    return Number.isFinite(value)
        ? value
        : 0
}
</script>

<template>
    <div
        class="grid gap-4"
        :class="gridClass"
        itemscope
        itemtype="https://schema.org/ItemList"
    >
        <!--
            Количество элементов именно
            в текущем отображаемом списке.
        -->
        <meta
            itemprop="numberOfItems"
            :content="String(brands.length)"
        />

        <article
            v-for="(brand, index) in brands"
            :key="brand.id"
            class="group flex h-full flex-col overflow-hidden rounded-2xl
                   border border-gray-200 bg-white shadow-sm transition
                   hover:-translate-y-0.5 hover:shadow-md
                   dark:border-gray-700 dark:bg-gray-900"
            itemprop="itemListElement"
            itemscope
            itemtype="https://schema.org/ListItem"
        >
            <!-- Глобальная позиция в ItemList -->
            <meta
                itemprop="position"
                :content="String(
                    startPosition
                    + index
                    + 1
                )"
            />

            <!-- Brand -->
            <div
                class="flex h-full flex-col"
                itemprop="item"
                itemscope
                itemtype="https://schema.org/Brand"
            >
                <!-- URL бренда -->
                <meta
                    v-if="brandAbsoluteUrl(brand)"
                    itemprop="url"
                    :content="brandAbsoluteUrl(brand)"
                />

                <!-- Название для Schema.org -->
                <meta
                    v-if="getBrandTitle(brand)"
                    itemprop="name"
                    :content="getBrandTitle(brand)"
                />

                <!-- Описание для Schema.org -->
                <meta
                    v-if="getBrandShort(brand)"
                    itemprop="description"
                    :content="getBrandShort(brand)"
                />

                <!-- Главное изображение для Schema.org -->
                <meta
                    v-if="getBrandImage(brand)"
                    itemprop="image"
                    :content="getBrandImage(brand)"
                />

                <!-- Логотип для Schema.org -->
                <meta
                    v-if="getBrandLogo(brand)"
                    itemprop="logo"
                    :content="getBrandLogo(brand)"
                />

                <!-- Изображения -->
                <div
                    v-if="hasBrandImages(brand)"
                    class="relative"
                >
                    <Link
                        :href="brandLink(brand)"
                        :title="getBrandTitle(brand)"
                    >
                        <UniversalImageSlider
                            :entity="brand"
                            height-class="h-48"
                            rounded-class=""
                            wrapper-class=""
                            img-class="w-full h-full object-cover transition
                                       duration-300 group-hover:scale-105"
                        />
                    </Link>
                </div>

                <!-- Информация -->
                <div
                    class="flex flex-1 flex-col items-center px-4 pb-4 text-center"
                    :class="hasBrandImages(brand) ? 'pt-3' : 'pt-4'"
                >
                    <!-- Логотип -->
                    <Link
                        v-if="getBrandLogo(brand)"
                        :href="brandLink(brand)"
                        :title="getBrandTitle(brand)"
                        class="mb-2 flex h-16 w-auto items-center
                               justify-center overflow-hidden rounded-md
                               border border-gray-200 bg-white p-1.5 shadow-sm
                               dark:border-gray-600 dark:bg-gray-800"
                        :class="hasBrandImages(brand) ? 'mt-1' : ''"
                    >
                        <img
                            :src="getBrandLogo(brand)"
                            :alt="getBrandTitle(brand)"
                            :title="getBrandTitle(brand)"
                            class="h-full w-full object-contain"
                            loading="lazy"
                        />
                    </Link>

                    <!-- Название -->
                    <Link
                        v-if="getBrandTitle(brand)"
                        :href="brandLink(brand)"
                        :title="getBrandShort(brand)"
                        class="inline-flex items-center justify-center"
                    >
                        <span
                            class="text-base font-semibold text-slate-900/85
                                   group-hover:opacity-75 dark:text-slate-100/85"
                        >
                            {{ getBrandTitle(brand) }}
                        </span>
                    </Link>

                    <!-- Краткое описание -->
                    <div
                        v-if="getBrandShort(brand)"
                        class="mt-2 line-clamp-3 text-sm text-slate-600 dark:text-slate-300"
                    >
                        {{ getBrandShort(brand) }}
                    </div>

                    <!-- Нижняя информация -->
                    <div
                        class="mt-auto flex flex-wrap items-center justify-center gap-2 pt-4
                               text-xs font-semibold text-slate-500 dark:text-slate-400"
                    >
                        <!-- Количество товаров -->
                        <div
                            class="flex items-center justify-center gap-1
                                   rounded-sm border border-slate-400 px-2 py-1"
                            :title="t('products')"
                        >
                            <svg
                                class="h-3 w-3 shrink-0 text-violet-600 dark:text-violet-300"
                                viewBox="0 0 24 24"
                                fill="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    d="M4 4h6v6H4V4Zm10 0h6v6h-6V4ZM4 14h6v6H4v-6Zm10 0h6v6h-6v-6Z"
                                />
                            </svg>

                            {{ getProductsCount(brand) }}
                        </div>

                        <!-- Просмотры -->
                        <div
                            v-if="getViews(brand) > 0"
                            class="flex items-center justify-center gap-1
                                   rounded-sm border border-slate-400 px-2 py-1"
                            :title="t('views')"
                        >
                            <svg
                                class="h-3 w-3 text-blue-600/85 dark:text-blue-200/85"
                                viewBox="0 0 576 512"
                                fill="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    d="M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z"
                                />
                            </svg>

                            {{ getViews(brand) }}
                        </div>
                    </div>

                    <!-- Подробнее -->
                    <Link
                        :href="brandLink(brand)"
                        :title="getBrandTitle(brand)"
                        class="mt-3 flex w-fit items-center justify-center gap-2 rounded-sm
                               px-3 py-1 btn-default"
                    >
                        <span class="text-sm font-semibold">
                            {{ t('readMore') }}
                        </span>

                        <svg
                            class="h-4 w-4"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            aria-hidden="true"
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
        </article>
    </div>
</template>
