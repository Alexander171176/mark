<script setup>
/**
 * Страница конкретного товара маркетплейса.
 *
 * - SEO
 * - хлебные крошки
 * - галерея изображений
 * - цена и старая цена
 * - наличие
 * - выбор варианта
 * - компания, магазин, бренд
 * - категории и теги
 * - характеристики
 * - физические параметры
 * - лайк
 * - рекомендуемые товары
 * - левая и правая колонки маркетплейса
 *
 * @version PulsarCMS 1.0
 * @author Александр
 */

import { computed, ref, watch } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

import DefaultLayout from '@/Layouts/DefaultLayout.vue'
import Navbar from '@/Partials/Default/Navbar.vue'
import FooterBlog from '@/Partials/Default/FooterBlog.vue'
import Progress from '@/Components/Public/Default/Progress/Progress.vue'

import LeftSidebarMarket from '@/Components/Public/Default/Partials/LeftSidebarMarket.vue'
import RightSidebarMarket from '@/Components/Public/Default/Partials/RightSidebarMarket.vue'

import MarketProductGallery from '@/Components/Public/Default/Market/MarketProduct/MarketProductGallery.vue'
import LikeButtonEntity from '@/Components/Public/Like/LikeButtonEntity.vue'

import MarketProductGrid from '@/Components/Public/Default/Market/MarketProduct/MarketProductGrid.vue'

import SectionVideoList from '@/Components/Public/Default/Blog/BlogVideo/SectionVideoList.vue'
import SectionBanners from '@/Components/Public/Default/Blog/BlogBanner/SectionBanners.vue'
import MarketRecommendedProducts from '@/Components/Public/Default/Market/MarketProduct/MarketRecommendedProducts.vue'

const { t } = useI18n()
const page = usePage()

/* ===================== PROPS ===================== */

/** Props страницы */
const props = defineProps({
    title: { type: String, default: '' },
    canLogin: { type: Boolean, default: false },
    canRegister: { type: Boolean, default: false },

    locale: { type: String, default: 'ru' },

    product: {
        type: Object,
        default: () => ({})
    },

    breadcrumbCategory: {
        type: Object,
        default: null
    },

    categoryTree: {
        type: Array,
        default: () => []
    },

    mainVideos: {
        type: [Array, Object],
        default: () => []
    },

    mainBanners: {
        type: [Array, Object],
        default: () => []
    }
})

/* ===================== PAGE ===================== */

/** Настройки сайта */
const siteSettings = page.props?.siteSettings || {}

/** Основные данные товара */
const productData = computed(() => {
    return props.product ?? {}
})

/** Дерево категорий */
const categoryTree = computed(() => {
    return Array.isArray(props.categoryTree)
        ? props.categoryTree
        : []
})

/** Универсальная нормализация коллекции */
const normalizeList = (value) => {
    if (Array.isArray(value)) {
        return value
    }

    if (Array.isArray(value?.data)) {
        return value.data
    }

    return []
}

/* ===================== TRANSLATION ===================== */

/** Текущий перевод товара */
const translation = computed(() => {
    return productData.value?.translation ?? {}
})

/** Название товара */
const productTitle = computed(() => {
    return translation.value?.title
        || productData.value?.title
        || ''
})

/** Подзаголовок товара */
const productSubtitle = computed(() => {
    return translation.value?.subtitle
        || productData.value?.subtitle
        || ''
})

/** Краткое описание */
const productShort = computed(() => {
    return translation.value?.short
        || productData.value?.short
        || ''
})

/** Полное описание */
const productDescription = computed(() => {
    return translation.value?.description
        || productData.value?.description
        || ''
})

/* ===================== SEO ===================== */

/** SEO title */
const seoTitle = computed(() => {
    return translation.value?.meta_title
        || productTitle.value
        || t('products')
})

/** SEO keywords */
const seoKeywords = computed(() => {
    return translation.value?.meta_keywords || ''
})

/** SEO description */
const seoDescription = computed(() => {
    return translation.value?.meta_desc
        || productShort.value
        || ''
})

/** Канонический URL товара */
const canonicalUrl = computed(() => {
    if (!productData.value?.url) {
        return ''
    }

    return String(
        route('public.marketProducts.show', {
            url: productData.value.url,
        })
    )
})

/** Название категории для хлебных крошек */
const breadcrumbCategoryTitle = computed(() => {
    return props.breadcrumbCategory?.translation?.title
        || props.breadcrumbCategory?.title
        || ''
})

/** Первое изображение товара */
const primaryImage = computed(() => {
    return productImages.value[0] ?? null
})

/** SEO URL основного изображения */
const primaryImageUrl = computed(() => {
    const image = primaryImage.value

    return image?.url
        || image?.original_url
        || image?.full_url
        || image?.image_url
        || image?.src
        || ''
})

/** ISO-код валюты для SEO */
const currencyCode = computed(() => {
    return activeCurrency.value?.code || ''
})

/** Schema.org наличие */
const schemaAvailability = computed(() => {
    return hasStock.value
        ? 'https://schema.org/InStock'
        : 'https://schema.org/OutOfStock'
})

/** Open Graph наличие товара */
const productAvailability = computed(() => {
    return hasStock.value
        ? 'in stock'
        : 'out of stock'
})

/* ===================== IMAGES ===================== */

/** Изображения товара */
const productImages = computed(() => {
    return normalizeList(productData.value?.images)
})

/* ===================== RELATIONS ===================== */

/** Категории */
const categories = computed(() => {
    return normalizeList(productData.value?.categories)
})

/** Теги */
const tags = computed(() => {
    return normalizeList(productData.value?.tags)
})

/** Характеристики */
const attributeValues = computed(() => {
    return normalizeList(productData.value?.attribute_values)
})

/** Публичные варианты */
const publicVariants = computed(() => {
    return normalizeList(productData.value?.public_variants)
})

/** Связанные товары */
const relatedProducts = computed(() => {
    return normalizeList(productData.value?.related_products)
})

/** Видео внизу страницы */
const mainVideosList = computed(() => {
    return normalizeList(props.mainVideos)
})

/** Баннеры внизу страницы */
const mainBannersList = computed(() => {
    return normalizeList(props.mainBanners)
})

/* ===================== SIDEBARS ===================== */

/** Показывать левую колонку */
const showLeft = computed(() => {
    return !siteSettings?.ViewLeftColumn
        || siteSettings.ViewLeftColumn === 'true'
})

/** Показывать правую колонку */
const showRight = computed(() => {
    return !siteSettings?.ViewRightColumn
        || siteSettings.ViewRightColumn === 'true'
})

/** Ключи localStorage */
const LEFT_SIDEBAR_KEY = 'public_left_sidebar_collapsed'
const RIGHT_SIDEBAR_KEY = 'public_right_sidebar_collapsed'

/** Получение boolean из localStorage */
const getStoredBoolean = (key, defaultValue = true) => {
    const value = localStorage.getItem(key)

    if (value === null) {
        return defaultValue
    }

    return value === 'true'
}

/** Левый сайдбар по умолчанию свернут */
const leftCollapsed = ref(
    getStoredBoolean(LEFT_SIDEBAR_KEY, true)
)

/** Правый сайдбар по умолчанию свернут */
const rightCollapsed = ref(
    getStoredBoolean(RIGHT_SIDEBAR_KEY, true)
)

/** Сохраняем состояние сайдбаров */
watch([leftCollapsed, rightCollapsed], () => {
    localStorage.setItem(
        LEFT_SIDEBAR_KEY,
        String(leftCollapsed.value)
    )

    localStorage.setItem(
        RIGHT_SIDEBAR_KEY,
        String(rightCollapsed.value)
    )
})

/** Раскладка основного блока товара */
const productMainGridClass = computed(() => {
    const leftExpanded = showLeft.value && !leftCollapsed.value
    const rightExpanded = showRight.value && !rightCollapsed.value

    if (leftExpanded && rightExpanded) {
        return 'grid-cols-1'
    }

    return 'grid-cols-1 lg:grid-cols-2'
})

/** Количество колонок рекомендуемых товаров */
const relatedGridCols = computed(() => {
    const leftExpanded = showLeft.value && !leftCollapsed.value
    const rightExpanded = showRight.value && !rightCollapsed.value

    if (leftExpanded && rightExpanded) {
        return 2
    }

    if (leftExpanded || rightExpanded) {
        return 3
    }

    return 4
})

/* ===================== VARIANTS ===================== */

/** Основной публичный вариант */
const defaultPublicVariant = computed(() => {
    if (!publicVariants.value.length) {
        return null
    }

    return publicVariants.value.find(
        (variant) => variant?.is_default
    ) || publicVariants.value[0]
})

/** Выбранный вариант */
const selectedVariantId = ref(
    defaultPublicVariant.value?.id ?? null
)

/** Выбранный вариант целиком */
const selectedVariant = computed(() => {
    if (!selectedVariantId.value) {
        return null
    }

    return publicVariants.value.find(
        (variant) => Number(variant.id) === Number(selectedVariantId.value)
    ) || null
})

/** Если список вариантов изменился */
watch(publicVariants, () => {
    if (
        selectedVariantId.value
        && publicVariants.value.some(
            (variant) => Number(variant.id) === Number(selectedVariantId.value)
        )
    ) {
        return
    }

    selectedVariantId.value = defaultPublicVariant.value?.id ?? null
})

/** Название варианта */
const getVariantTitle = (variant) => {
    return variant?.display_title
        || variant?.translation?.title
        || variant?.code
        || variant?.sku
        || `#${variant?.id ?? ''}`
}

/* ===================== PRICE ===================== */

/** Числовая цена */
const toNumber = (value) => {
    const number = Number(value)

    return Number.isFinite(number)
        ? number
        : 0
}

/** Текущая цена */
const currentPrice = computed(() => {
    if (
        selectedVariant.value
        && selectedVariant.value.effective_price !== null
        && selectedVariant.value.effective_price !== undefined
    ) {
        return toNumber(
            selectedVariant.value.effective_price
        )
    }

    return toNumber(productData.value?.price)
})

/** Текущая старая цена */
const currentOldPrice = computed(() => {
    if (
        selectedVariant.value
        && selectedVariant.value.effective_old_price !== null
        && selectedVariant.value.effective_old_price !== undefined
    ) {
        return toNumber(
            selectedVariant.value.effective_old_price
        )
    }

    return toNumber(productData.value?.old_price)
})

/** Есть старая цена */
const hasOldPrice = computed(() => {
    return currentOldPrice.value > currentPrice.value
})

/** Форматирование цены */
const formatPrice = (value) => {
    return new Intl.NumberFormat(props.locale, {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
    }).format(toNumber(value))
}

/** Валюта товара */
const productCurrency = computed(() => {
    return productData.value?.currency ?? null
})

/** Валюта выбранного варианта */
const selectedVariantCurrency = computed(() => {
    return selectedVariant.value?.currency ?? null
})

/** Активная валюта */
const activeCurrency = computed(() => {
    return selectedVariantCurrency.value
        || productCurrency.value
        || null
})

/** Символ / код валюты */
const currencyLabel = computed(() => {
    return activeCurrency.value?.symbol
        || activeCurrency.value?.sign
        || activeCurrency.value?.code
        || ''
})

/* ===================== STOCK ===================== */

/** Наличие товара или варианта */
const hasStock = computed(() => {
    if (selectedVariant.value) {
        return Boolean(selectedVariant.value?.has_stock)
    }

    return Boolean(
        productData.value?.has_stock
        ?? (
            productData.value?.in_stock
            && Number(productData.value?.quantity ?? 0) > 0
        )
    )
})

/** Остаток товара или варианта */
const quantity = computed(() => {
    if (selectedVariant.value) {
        return Number(selectedVariant.value?.quantity ?? 0)
    }

    return Number(productData.value?.quantity ?? 0)
})

/* ===================== WHOLESALE ===================== */

/** Оптовая цена */
const wholesalePrice = computed(() => {
    if (
        selectedVariant.value
        && selectedVariant.value.effective_wholesale_price !== null
        && selectedVariant.value.effective_wholesale_price !== undefined
    ) {
        return toNumber(
            selectedVariant.value.effective_wholesale_price
        )
    }

    return toNumber(productData.value?.wholesale_price)
})

/** Минимальное количество для опта */
const wholesaleMinQuantity = computed(() => {
    if (
        selectedVariant.value
        && selectedVariant.value.effective_wholesale_min_quantity !== null
        && selectedVariant.value.effective_wholesale_min_quantity !== undefined
    ) {
        return Number(
            selectedVariant.value.effective_wholesale_min_quantity
        )
    }

    return Number(
        productData.value?.wholesale_min_quantity ?? 0
    )
})

/** Есть оптовая цена */
const hasWholesalePrice = computed(() => {
    return wholesalePrice.value > 0
        && wholesaleMinQuantity.value > 0
})

/* ===================== RATING ===================== */

/** Рейтинг товара */
const rating = computed(() => {
    return toNumber(productData.value?.rating_avg)
})

/** Количество оценок */
const ratingCount = computed(() => {
    return Number(productData.value?.rating_count ?? 0)
})

/* ===================== ATTRIBUTES ===================== */

/** Получить значение характеристики */
const getAttributeValue = (item) => {
    if (item?.attribute_value?.title) {
        return item.attribute_value.title
    }

    if (
        item?.value_string !== null
        && item?.value_string !== undefined
        && item?.value_string !== ''
    ) {
        return item.value_string
    }

    if (
        item?.value_number !== null
        && item?.value_number !== undefined
    ) {
        return `${item.value_number}${item.unit ? ` ${item.unit}` : ''}`
    }

    if (
        item?.value_boolean !== null
        && item?.value_boolean !== undefined
    ) {
        return item.value_boolean
            ? t('yes')
            : t('no')
    }

    if (item?.value_date) {
        return item.value_date
    }

    if (item?.value_json) {
        return typeof item.value_json === 'string'
            ? item.value_json
            : JSON.stringify(item.value_json)
    }

    return '—'
}

/* ===================== PHYSICAL PARAMETERS ===================== */

/** Есть физические параметры */
const hasPhysicalParameters = computed(() => {
    return Boolean(
        productData.value?.weight
        || productData.value?.length
        || productData.value?.width
        || productData.value?.height
    )
})

/* ===================== MARKETING ===================== */

/** Есть маркетинговые признаки */
const hasMarketingFlags = computed(() => {
    return Boolean(
        productData.value?.is_new
        || productData.value?.is_hit
        || productData.value?.is_sale
    )
})
</script>

<template>
    <Head>
        <!-- Основные SEO -->
        <title>{{ seoTitle }}</title>

        <meta
            v-if="seoDescription"
            name="description"
            :content="seoDescription"
        />

        <meta
            v-if="seoKeywords"
            name="keywords"
            :content="seoKeywords"
        />

        <meta
            name="robots"
            content="index, follow, max-image-preview:large"
        />

        <!-- Canonical -->
        <link
            v-if="canonicalUrl"
            rel="canonical"
            :href="canonicalUrl"
        />

        <!-- Open Graph -->
        <meta
            property="og:type"
            content="product"
        />

        <meta
            property="og:title"
            :content="seoTitle"
        />

        <meta
            v-if="seoDescription"
            property="og:description"
            :content="seoDescription"
        />

        <meta
            v-if="canonicalUrl"
            property="og:url"
            :content="canonicalUrl"
        />

        <meta
            v-if="primaryImageUrl"
            property="og:image"
            :content="primaryImageUrl"
        />

        <meta
            v-if="productTitle"
            property="og:image:alt"
            :content="productTitle"
        />

        <!-- Данные товара -->
        <meta
            v-if="currentPrice > 0"
            property="product:price:amount"
            :content="String(currentPrice)"
        />

        <meta
            v-if="currencyCode"
            property="product:price:currency"
            :content="currencyCode"
        />

        <meta
            property="product:availability"
            :content="productAvailability"
        />

        <meta
            property="product:condition"
            content="new"
        />

        <!-- Twitter / X -->
        <meta
            name="twitter:card"
            content="summary_large_image"
        />

        <meta
            name="twitter:title"
            :content="seoTitle"
        />

        <meta
            v-if="seoDescription"
            name="twitter:description"
            :content="seoDescription"
        />

        <meta
            v-if="primaryImageUrl"
            name="twitter:image"
            :content="primaryImageUrl"
        />

        <meta
            v-if="productTitle"
            name="twitter:image:alt"
            :content="productTitle"
        />
    </Head>

    <DefaultLayout :title="title" :can-login="canLogin" :can-register="canRegister">
        <!-- Шапка -->
        <Navbar />

        <div class="min-h-screen px-1.5">
            <main class="mx-auto flex w-full flex-col lg:flex-row gap-4 tracking-wider">

                <!-- Левая колонка -->
                <aside
                    v-if="showLeft"
                    class="shrink-0 mt-12 lg:mt-28 pl-3 transition-all duration-300"
                    :class="leftCollapsed ? 'lg:w-10' : 'lg:w-64'"
                >
                    <LeftSidebarMarket
                        :category-tree="categoryTree"
                        :collapsed="leftCollapsed"
                        @collapsed="leftCollapsed = $event"
                    />
                </aside>

                <!-- Центральная колонка -->
                <div class="min-w-0 flex-1 lg:mt-28 pb-6 slate-1">
                    <div class="w-full">
                        <article
                            itemscope
                            itemtype="https://schema.org/Product"
                            :itemid="canonicalUrl"
                            class="selection:bg-red-400 selection:text-white transition-all"
                        >
                            <!-- Мета-данные товара -->
                            <meta
                                itemprop="url"
                                :content="canonicalUrl"
                            />

                            <meta
                                v-if="productData.sku"
                                itemprop="sku"
                                :content="productData.sku"
                            />

                            <meta
                                v-if="primaryImageUrl"
                                itemprop="image"
                                :content="primaryImageUrl"
                            />

                            <meta
                                v-if="breadcrumbCategoryTitle"
                                itemprop="category"
                                :content="breadcrumbCategoryTitle"
                            />

                            <!-- Хлебные крошки -->
                            <nav
                                class="text-sm mb-3"
                                aria-label="Breadcrumb"
                                itemscope
                                itemtype="https://schema.org/BreadcrumbList"
                            >
                                <ol class="flex flex-wrap items-center font-semibold">
                                    <!-- Товары -->
                                    <li
                                        itemprop="itemListElement"
                                        itemscope
                                        itemtype="https://schema.org/ListItem"
                                        class="flex items-center"
                                    >
                                        <Link
                                            itemprop="item"
                                            :href="route('public.marketProducts.index')"
                                            class="transition hover:text-indigo-500"
                                        >
                <span itemprop="name">
                    {{ t('products') }}
                </span>
                                        </Link>

                                        <meta
                                            itemprop="position"
                                            content="1"
                                        />
                                    </li>

                                    <!-- Категория -->
                                    <li
                                        v-if="breadcrumbCategory"
                                        itemprop="itemListElement"
                                        itemscope
                                        itemtype="https://schema.org/ListItem"
                                        class="flex items-center"
                                    >
                                        <span class="mx-2 breadcrumbs">/</span>

                                        <Link
                                            itemprop="item"
                                            :href="route('public.marketCategories.show', {
                    url: breadcrumbCategory.url,
                })"
                                            class="transition hover:text-indigo-500"
                                        >
                <span itemprop="name">
                    {{ breadcrumbCategoryTitle }}
                </span>
                                        </Link>

                                        <meta
                                            itemprop="position"
                                            content="2"
                                        />
                                    </li>

                                    <!-- Текущий товар -->
                                    <li
                                        itemprop="itemListElement"
                                        itemscope
                                        itemtype="https://schema.org/ListItem"
                                        class="flex items-center"
                                        aria-current="page"
                                    >
                                        <span class="mx-2 breadcrumbs">/</span>

                                        <span
                                            itemprop="name"
                                            class="breadcrumbs"
                                        >
                {{ productTitle }}
            </span>

                                        <meta
                                            itemprop="item"
                                            :content="canonicalUrl"
                                        />

                                        <meta
                                            itemprop="position"
                                            :content="breadcrumbCategory ? '3' : '2'"
                                        />
                                    </li>
                                </ol>
                            </nav>

                            <!-- Основной блок товара -->
                            <section
                                class="overflow-hidden rounded-sm
                                       border border-gray-300 dark:border-gray-600
                                       bg-slate-50 dark:bg-slate-900 shadow-sm"
                            >
                                <div
                                    class="grid gap-6 p-4"
                                    :class="productMainGridClass"
                                >

                                    <!-- Галерея товара -->
                                    <div class="min-w-0">
                                        <MarketProductGallery
                                            :images="productImages"
                                            :title="productTitle"
                                        />
                                    </div>

                                    <!-- Информация -->
                                    <div class="flex min-w-0 flex-col">

                                        <div class="flex items-center justify-between mb-3">
                                            <!-- Маркетинговые признаки -->
                                            <div
                                                v-if="hasMarketingFlags"
                                                class="flex flex-wrap gap-2"
                                            >
                                                <!-- Новинка -->
                                                <span
                                                    v-if="productData.is_new"
                                                    class="inline-flex items-center rounded-sm
                                                       bg-teal-500 dark:bg-teal-600
                                                       px-3 py-1 text-[10px] font-bold
                                                       uppercase tracking-wide text-white"
                                                >
                                                NEW
                                            </span>

                                                <!-- Хит -->
                                                <span
                                                    v-if="productData.is_hit"
                                                    class="inline-flex items-center rounded-sm
                                                       bg-amber-500 dark:bg-amber-400
                                                       px-3 py-1 text-[10px] font-bold
                                                       uppercase tracking-wide text-white
                                                       dark:text-slate-900"
                                                >
                                                HIT
                                            </span>

                                                <!-- Распродажа -->
                                                <span
                                                    v-if="productData.is_sale"
                                                    class="inline-flex items-center rounded-sm
                                                       bg-red-500 dark:bg-red-600
                                                       px-3 py-1 text-[10px] font-bold
                                                       uppercase tracking-wide text-white"
                                                >
                                                SALE
                                            </span>
                                            </div>

                                            <!-- Рейтинг / отзывы -->
                                            <div class="flex flex-wrap items-center gap-3">

                                                <div
                                                    v-if="productData.reviews_count > 0"
                                                    class="text-xs font-semibold
                                                   text-slate-500 dark:text-slate-400"
                                                >
                                                    {{ t('reviews') }}:
                                                    {{ productData.reviews_count }}
                                                </div>

                                                <div
                                                    v-if="productData.views > 0"
                                                    class="text-xs font-semibold
                                                   text-slate-500 dark:text-slate-400"
                                                >
                                                    {{ t('views') }}:
                                                    {{ productData.views }}
                                                </div>

                                                <div
                                                    v-if="rating > 0 && ratingCount > 0"
                                                    :title="t('rating')"
                                                    itemprop="aggregateRating"
                                                    itemscope
                                                    itemtype="https://schema.org/AggregateRating"
                                                    class="inline-flex items-center gap-1 text-sm font-semibold"
                                                >
                                                    <meta
                                                        itemprop="ratingValue"
                                                        :content="String(rating)"
                                                    />

                                                    <meta
                                                        itemprop="ratingCount"
                                                        :content="String(ratingCount)"
                                                    />

                                                    <svg
                                                        class="h-4 w-4 text-amber-500"
                                                        viewBox="0 0 24 24"
                                                        fill="currentColor"
                                                    >
                                                        <path d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"/>
                                                    </svg>

                                                    <span>
                                                    {{ rating.toFixed(1) }}
                                                </span>

                                                    <span class="font-normal text-slate-500">
                                                    ({{ ratingCount }})
                                                </span>
                                                </div>

                                            </div>

                                        </div>

                                        <!-- Бренд -->
                                        <div
                                            v-if="productData.brand?.title"
                                            itemprop="brand"
                                            itemscope
                                            itemtype="https://schema.org/Brand"
                                            class="mb-1 text-sm font-semibold uppercase
                                                   tracking-wide text-slate-500 dark:text-slate-400"
                                        >
                                            <span itemprop="name">
                                                {{ productData.brand.title }}
                                            </span>
                                        </div>

                                        <!-- Название -->
                                        <h1
                                            itemprop="name"
                                            class="text-xl font-bold
                                                   text-slate-700 dark:text-slate-300"
                                        >
                                            {{ productTitle }}
                                        </h1>

                                        <!-- Подзаголовок -->
                                        <div
                                            v-if="productSubtitle"
                                            class="mt-2 text-sm font-medium text-slate-500
                                               dark:text-slate-400"
                                        >
                                            {{ productSubtitle }}
                                        </div>

                                        <!-- Краткое описание -->
                                        <div
                                            v-if="productShort"
                                            itemprop="description"
                                            class="mt-3 text-sm leading-6 text-slate-700
                                                   dark:text-slate-300"
                                        >
                                            {{ productShort }}
                                        </div>

                                        <div class="mt-2 py-2 border-y border-dashed
                                                    border-gray-600 dark:border-gray-400">
                                            <!-- Наличие -->
                                            <div
                                                class="flex items-center gap-2
                                               text-sm font-semibold"
                                                :class="hasStock
                                            ? 'text-emerald-600 dark:text-emerald-300'
                                            : 'text-red-500 dark:text-red-400'"
                                            >
                                                <svg
                                                    class="h-4 w-4"
                                                    viewBox="0 0 24 24"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        v-if="hasStock"
                                                        d="M9 16.17 4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17Z"
                                                    />

                                                    <path
                                                        v-else
                                                        d="M18.3 5.71 12 12l6.3 6.29-1.41 1.42L10.59 13.41 4.29 19.71 2.88 18.3 9.17 12 2.88 5.71 4.29 4.29 10.59 10.59 16.89 4.29 18.3 5.71Z"
                                                    />
                                                </svg>

                                                <span>
                                                  {{ hasStock ? t('remainder') : t('outOfStock') }}
                                                </span>

                                                <span v-if="hasStock && quantity > 0">
                                                    [{{ quantity }}]
                                                </span>
                                            </div>

                                            <!-- Цена / предложение -->
                                            <div
                                                itemprop="offers"
                                                itemscope
                                                itemtype="https://schema.org/Offer"
                                            >
                                                <meta
                                                    v-if="canonicalUrl"
                                                    itemprop="url"
                                                    :content="canonicalUrl"
                                                />

                                                <meta
                                                    v-if="currencyCode"
                                                    itemprop="priceCurrency"
                                                    :content="currencyCode"
                                                />

                                                <meta
                                                    v-if="currentPrice > 0"
                                                    itemprop="price"
                                            :content="Number(currentPrice).toFixed(2)"
                                                />

                                                <link
                                                    itemprop="availability"
                                                    :href="schemaAvailability"
                                                />

                                                <div class="flex flex-wrap items-end gap-3">
                                                    <div
                                                        class="text-xl font-bold
                                                           text-teal-600 dark:text-teal-400"
                                                    >
                                                        {{ formatPrice(currentPrice) }}
                                                        {{ currencyLabel }}
                                                    </div>

                                                    <div
                                                        v-if="hasOldPrice"
                                                        class="pb-1 text-base font-semibold
                                                           text-slate-400 line-through
                                                           dark:text-slate-500"
                                                    >
                                                        {{ formatPrice(currentOldPrice) }}
                                                        {{ currencyLabel }}
                                                    </div>
                                                </div>

                                                <!-- Оптовая цена -->
                                                <div
                                                    v-if="hasWholesalePrice"
                                                    class="mt-1 text-xs font-semibold
                                                       text-gray-500 dark:text-gray-400"
                                                >
                                                    {{ t('wholesalePrice') }}:
                                                    {{ formatPrice(wholesalePrice) }}
                                                    {{ currencyLabel }}

                                                    <span>
                                                    /
                                                    {{ t('wholesaleMinQuantity') }}:
                                                    {{ wholesaleMinQuantity }}
                                                </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Варианты -->
                                        <div
                                            v-if="publicVariants.length"
                                            class="mt-1"
                                        >
                                            <div
                                                class="mb-2 text-sm font-semibold
                                                   text-slate-700 dark:text-slate-300"
                                            >
                                                {{ t('variants') }}
                                            </div>

                                            <select
                                                v-model="selectedVariantId"
                                                class="w-full rounded-sm border
                                                       bg-white dark:bg-gray-800 px-3 py-1
                                                       border-gray-400 dark:border-gray-600
                                                       text-sm text-slate-700 dark:text-slate-300
                                                       focus:border-indigo-500
                                                       focus:ring-indigo-500"
                                            >
                                                <option
                                                    v-for="variant in publicVariants"
                                                    :key="variant.id"
                                                    :value="variant.id"
                                                    :disabled="!variant.has_stock"
                                                >
                                                    {{ getVariantTitle(variant) }}
                                                    —
                                                    {{ formatPrice(variant.effective_price) }}
                                                    {{ variant.currency?.symbol
                                                || variant.currency?.sign
                                                || variant.currency?.code
                                                || currencyLabel }}
                                                    {{ !variant.has_stock
                                                    ? `(${t('outOfStock')})`
                                                    : '' }}
                                                </option>
                                            </select>

                                            <!-- Характеристики варианта -->
                                            <div
                                                v-if="selectedVariant?.values?.length"
                                                class="mt-3 flex flex-wrap gap-2"
                                            >
                                            <span
                                                v-for="value in selectedVariant.values"
                                                :key="value.id"
                                                class="bg-white dark:bg-slate-950
                                                       rounded-sm border
                                                       border-slate-400 dark:border-slate-600
                                                       px-2 py-1 text-xs font-semibold
                                                       text-slate-600 dark:text-slate-300"
                                            >
                                                {{ value.display_value }}
                                            </span>
                                            </div>
                                        </div>

                                        <!-- Артикулы -->
                                        <div
                                            v-if="productData.sku
                                            || productData.vendor_code
                                            || productData.barcode"
                                            class="mt-3 flex lg:flex-row lg:justify-between
                                                   items-center gap-2
                                                   text-xs text-slate-600 dark:text-slate-400"
                                        >
                                            <div v-if="productData.vendor_code">
                                                {{ t('vendorCode') }}:
                                                <span class="font-semibold">
                                                    {{ productData.vendor_code }}
                                                </span>
                                            </div>
                                            <div v-if="productData.sku">
                                                {{ t('sku') }}:
                                                <span class="font-semibold">
                                                    {{ productData.sku }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Лайк -->
                                        <div class="mt-3">
                                            <LikeButtonEntity
                                                :likes-count="productData.likes_count || 0"
                                                :already-liked="productData.already_liked || false"
                                                route-name="public.marketProducts.like"
                                                :route-params="{ id: productData.id }"
                                                icon-class="w-5 h-5 hover:scale-110 active:scale-95"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <!-- Описание -->
                            <section
                                v-if="productDescription"
                                class="mt-4 p-4 rounded-sm shadow-sm
                                       border border-gray-300 dark:border-gray-600
                                       bg-white dark:bg-gray-900"
                            >
                                <h2
                                    class="mb-4 text-lg font-semibold
                                       text-slate-800 dark:text-slate-200"
                                >
                                    {{ t('description') }}
                                </h2>

                                <div
                                    class="prose max-w-none dark:prose-invert"
                                    v-html="productDescription"
                                />
                            </section>

                            <!-- Физические параметры -->
                            <section
                                v-if="hasPhysicalParameters"
                                class="mt-4 p-4 rounded-sm shadow-sm
                                       border border-gray-300 dark:border-gray-600
                                       bg-white dark:bg-gray-900"
                            >
                                <h2
                                    class="mb-4 text-lg font-semibold
                                       text-slate-800 dark:text-slate-200"
                                >
                                    {{ t('dimensions') }}
                                </h2>

                                <div
                                    class="grid gap-3 text-sm
                                       sm:grid-cols-2 lg:grid-cols-4"
                                >
                                    <div v-if="productData.weight">
                                    <span
                                        class="text-slate-500 dark:text-slate-400"
                                    >
                                        {{ t('weight') }}:
                                    </span>

                                        <span class="ml-1 font-semibold">
                                        {{ productData.weight }}
                                    </span>
                                    </div>

                                    <div v-if="productData.length">
                                    <span
                                        class="text-slate-500 dark:text-slate-400"
                                    >
                                        {{ t('length') }}:
                                    </span>

                                        <span class="ml-1 font-semibold">
                                        {{ productData.length }}
                                    </span>
                                    </div>

                                    <div v-if="productData.width">
                                    <span
                                        class="text-slate-500 dark:text-slate-400"
                                    >
                                        {{ t('width') }}:
                                    </span>

                                        <span class="ml-1 font-semibold">
                                        {{ productData.width }}
                                    </span>
                                    </div>

                                    <div v-if="productData.height">
                                    <span
                                        class="text-slate-500 dark:text-slate-400"
                                    >
                                        {{ t('height') }}:
                                    </span>

                                        <span class="ml-1 font-semibold">
                                        {{ productData.height }}
                                    </span>
                                    </div>
                                </div>
                            </section>

                            <!-- Характеристики -->
                            <section
                                v-if="attributeValues.length"
                                class="mt-4 p-4 rounded-sm shadow-sm
                                       border border-gray-300 dark:border-gray-600
                                       bg-white dark:bg-gray-900"
                            >
                                <h2
                                    class="mb-4 text-lg font-semibold
                                       text-slate-800 dark:text-slate-200"
                                >
                                    {{ t('characteristics') }}
                                </h2>

                                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <div
                                        v-for="item in attributeValues"
                                        :key="item.id"
                                        class="grid gap-2 py-2
                                           text-sm sm:grid-cols-2"
                                    >
                                        <div
                                            class="font-semibold text-slate-600
                                               dark:text-slate-400"
                                        >
                                            {{ item.attribute?.title || '—' }}
                                        </div>

                                        <div
                                            class="text-slate-800 dark:text-slate-200"
                                        >
                                            {{ getAttributeValue(item) }}
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <!-- Категории -->
                            <section
                                v-if="categories.length"
                                class="mt-4 p-4 rounded-sm shadow-sm
                                       border border-gray-300 dark:border-gray-600
                                       bg-white dark:bg-gray-900"
                            >
                                <h2
                                    class="mb-3 text-lg font-semibold
                                       text-slate-800 dark:text-slate-200"
                                >
                                    {{ t('categories') }}
                                </h2>

                                <div class="flex flex-wrap gap-2">
                                    <Link
                                        v-for="category in categories"
                                        :key="category.id"
                                        :href="route('public.marketCategories.show', {
                                        url: category.url,
                                    })"
                                        class="rounded-sm border
                                               border-slate-300 dark:border-slate-600
                                               px-3 py-1 text-xs font-semibold
                                               text-slate-600 dark:text-slate-300
                                               hover:text-blue-600 dark:hover:text-blue-400
                                               transition bg-slate-50 dark:bg-slate-950
                                               hover:bg-slate-100 dark:hover:bg-slate-800"
                                    >
                                        {{ category.translation?.title
                                    || category.title }}
                                    </Link>
                                </div>
                            </section>

                            <!-- Теги -->
                            <section
                                v-if="tags.length"
                                class="mt-4 p-4 rounded-sm shadow-sm
                                       border border-gray-300 dark:border-gray-600
                                       bg-white dark:bg-gray-900"
                            >
                                <h2
                                    class="mb-3 text-lg font-semibold
                                       text-slate-800 dark:text-slate-200"
                                >
                                    {{ t('tags') }}
                                </h2>

                                <div class="flex flex-wrap gap-2">
                                    <Link
                                        v-for="tag in tags"
                                        :key="tag.id"
                                        :href="route('public.marketTags.show', {
                                        url: tag.url,
                                    })"
                                        class="rounded-sm border
                                               border-slate-300 dark:border-slate-600
                                               px-3 py-1 text-xs font-semibold
                                               text-slate-600 dark:text-slate-300
                                               hover:text-blue-600 dark:hover:text-blue-400
                                               transition bg-slate-50 dark:bg-slate-950
                                               hover:bg-slate-100 dark:hover:bg-slate-800"
                                    >
                                        {{ tag.translation?.title
                                    || tag.title }}
                                    </Link>
                                </div>
                            </section>

                            <!-- Рекомендуемые товары -->
                            <section
                                v-if="relatedProducts.length"
                                class="mt-6"
                            >
                                <h2
                                    class="mb-4 text-xl font-semibold text-center
                                           text-slate-800 dark:text-slate-200"
                                >
                                    {{ t('relatedProducts') }}
                                </h2>

                                <MarketRecommendedProducts
                                    :products="relatedProducts"
                                    :cols="relatedGridCols"
                                    :locale="locale"
                                />
                            </section>

                        </article>

                        <!-- Центральные видео -->
                        <SectionVideoList
                            v-if="mainVideosList.length"
                            class="mt-6"
                            :videos="mainVideosList"
                        />

                        <!-- Центральные баннеры -->
                        <SectionBanners
                            v-if="mainBannersList.length"
                            class="mt-6"
                            :banners="mainBannersList"
                        />
                    </div>
                </div>

                <!-- Правая колонка -->
                <aside
                    v-if="showRight"
                    class="shrink-0 lg:mt-28 pr-3 transition-all duration-300"
                    :class="rightCollapsed ? 'lg:w-10' : 'lg:w-64'"
                >
                    <RightSidebarMarket
                        :collapsed="rightCollapsed"
                        @collapsed="rightCollapsed = $event"
                    />
                </aside>
            </main>
        </div>

        <!-- Подвал -->
        <FooterBlog />

        <!-- Прогресс -->
        <Progress />
    </DefaultLayout>
</template>
