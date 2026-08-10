<script setup>
/**
 * Страница списка товаров маркетплейса.
 *
 * @version PulsarCMS 1.0
 * @author Александр
 */

import { computed, ref, watch } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useSmoothScrollTo } from '@/composables/useSmoothScrollTo'

import DefaultLayout from '@/Layouts/DefaultLayout.vue'
import Navbar from '@/Partials/Default/Navbar.vue'
import FooterBlog from '@/Partials/Default/FooterBlog.vue'
import Progress from '@/Components/Public/Default/Progress/Progress.vue'

import LeftSidebarMarket from '@/Components/Public/Default/Partials/LeftSidebarMarket.vue'
import RightSidebarMarket from '@/Components/Public/Default/Partials/RightSidebarMarket.vue'

import EntityPageToolbar from '@/Components/Public/Default/PageToolbar/EntityPageToolbar.vue'
import FrontendEntityPageToolbar from '@/Components/Public/Default/PageToolbar/FrontendEntityPageToolbar.vue'

import MarketProductGrid from '@/Components/Public/Default/Market/MarketProduct/MarketProductGrid.vue'
import MarketProductRows from '@/Components/Public/Default/Market/MarketProduct/MarketProductRows.vue'

import Pagination from '@/Components/Public/Default/Pagination/Pagination.vue'
import FrontendPagination from '@/Components/Public/Default/Pagination/FrontendPagination.vue'

import PublicAdminBottomPanel from '@/Components/Admin/UI/PublicAdminPanel/PublicAdminBottomPanel.vue'

const { t } = useI18n()

/* ===================== PROPS ===================== */

/** Props страницы */
const props = defineProps({
    locale: { type: String, default: 'ru' },

    seo: {
        type: Object,
        default: () => ({
            title: '',
            keywords: '',
            description: '',
        }),
    },

    useServerProcessing: { type: Boolean, default: false },
    publicMarketProductsProcessingMode: { type: String, default: 'server' },

    title: { type: String, default: '' },
    canLogin: { type: Boolean, default: false },
    canRegister: { type: Boolean, default: false },

    categoryTree: { type: Array, default: () => [] },

    products: { type: [Array, Object], default: () => [] },
    productsCount: { type: Number, default: 0 },
    productsFound: { type: Number, default: 0 },

    filters: { type: Object, default: () => ({}) },
})

/* ===================== PAGE ===================== */

/** Глобальные данные страницы */
const page = usePage()

/** Глобальные настройки сайта */
const siteSettings = page.props?.siteSettings || {}

/** Роль администратора */
const isAdmin = computed(() => page.props?.isAdmin === true)

/** Дерево категорий */
const categoryTree = computed(() => {
    return Array.isArray(props.categoryTree)
        ? props.categoryTree
        : []
})

/* ===================== PRODUCTS DATA ===================== */

/** Универсальный список товаров */
const productsData = computed(() => {
    if (Array.isArray(props.products)) {
        return props.products
    }

    if (Array.isArray(props.products?.data)) {
        return props.products.data
    }

    return []
})

/* ===================== SIDEBARS ===================== */

/** Показ левой колонки */
const showLeft = computed(() => {
    return !siteSettings?.ViewLeftColumn
        || siteSettings.ViewLeftColumn === 'true'
})

/** Показ правой колонки */
const showRight = computed(() => {
    return !siteSettings?.ViewRightColumn
        || siteSettings.ViewRightColumn === 'true'
})

/** Ключ левого сайдбара */
const LEFT_SIDEBAR_KEY = 'public_left_sidebar_collapsed'

/** Ключ правого сайдбара */
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

/**
 * Количество колонок сетки.
 *
 * Оба открыты  → 2.
 * Один свернут → 3.
 * Оба свернуты → 4.
 *
 * Количество товаров при этом не меняется.
 */
const productGridCols = computed(() => {
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

/* ===================== FILTERS ===================== */

/** Поисковая строка */
const q = ref(String(props.filters?.q ?? ''))

/** Сортировка по умолчанию */
const DEFAULT_SORT = 'sortAsc'

/** Текущая сортировка */
const sort = ref(
    String(props.filters?.sort ?? DEFAULT_SORT)
)

/** Ключ режима отображения */
const VIEW_KEY = 'public_market_products_view'

/** Режим отображения */
const viewMode = ref(
    String(
        props.filters?.view
        || localStorage.getItem(VIEW_KEY)
        || 'grid'
    )
)

/** Сохраняем режим отображения */
watch(viewMode, (value) => {
    localStorage.setItem(VIEW_KEY, value)
})

/**
 * Количество товаров на странице.
 *
 * Источник значения — backend:
 * PublicSettingsService → resolvePerPage() → filters.per_page.
 *
 * 12 используется только как аварийный fallback.
 */
const perPage = computed(() => {
    const value = Number(props.filters?.per_page)

    return Number.isFinite(value) && value > 0
        ? value
        : 12
})

/** Опции сортировки */
const productSortOptions = [
    { value: 'idDesc', label: t('idDesc') },
    { value: 'idAsc', label: t('idAsc') },

    { value: 'sortAsc', label: `${t('sortNumber')} 0→9` },
    { value: 'sortDesc', label: `${t('sortNumber')} 9→0` },

    { value: 'titleAsc', label: `${t('title')} A→Z` },
    { value: 'titleDesc', label: `${t('title')} Z→A` },

    { value: 'priceAsc', label: `${t('price')} 0→9` },
    { value: 'priceDesc', label: `${t('price')} 9→0` },

    { value: 'quantityAsc', label: `${t('quantity')} 0→9` },
    { value: 'quantityDesc', label: `${t('quantity')} 9→0` },

    { value: 'viewsDesc', label: `${t('views')} 9→0` },
    { value: 'viewsAsc', label: `${t('views')} 0→9` },

    { value: 'likesDesc', label: `${t('likes')} 9→0` },
    { value: 'likesAsc', label: `${t('likes')} 0→9` },

    { value: 'ratingDesc', label: `${t('rating')} 9→0` },
    { value: 'ratingAsc', label: `${t('rating')} 0→9` },

    { value: 'ratingCountDesc', label: `${t('ratingCount')} 9→0` },
    { value: 'ratingCountAsc', label: `${t('ratingCount')} 0→9` },

    { value: 'reviewsDesc', label: `${t('reviews')} 9→0` },
    { value: 'reviewsAsc', label: `${t('reviews')} 0→9` },

    { value: 'brandAsc', label: `${t('brand')} A→Z` },
    { value: 'brandDesc', label: `${t('brand')} Z→A` },

    { value: 'publishedAtDesc', label: `${t('publishedAt')} ↓` },
    { value: 'publishedAtAsc', label: `${t('publishedAt')} ↑` },

    { value: 'createdAtDesc', label: `${t('createdAt')} ↓` },
    { value: 'createdAtAsc', label: `${t('createdAt')} ↑` },
]

/* ===================== FRONTEND MODE ===================== */

/** Текущая frontend-страница */
const frontendCurrentPage = ref(1)

/** Плавный скролл к списку */
const {
    targetRef: scrollTarget,
    scrollToTarget,
} = useSmoothScrollTo({
    offset: 80,
    duration: 1200,
})

/** Нормализация текста */
const normalizeText = (value) => {
    return String(value ?? '').toLowerCase()
}

/** Нормализация числа */
const normalizeNumber = (value) => {
    const number = Number(value)

    return Number.isFinite(number)
        ? number
        : 0
}

/** Нормализация даты */
const normalizeDate = (value) => {
    if (!value) {
        return 0
    }

    const timestamp = new Date(value).getTime()

    return Number.isFinite(timestamp)
        ? timestamp
        : 0
}

/** Название товара */
const getProductTitle = (product) => {
    return product?.title
        || product?.translation?.title
        || product?.current_translation?.title
        || product?.translations?.[0]?.title
        || ''
}

/** Подзаголовок товара */
const getProductSubtitle = (product) => {
    return product?.subtitle
        || product?.translation?.subtitle
        || product?.current_translation?.subtitle
        || product?.translations?.[0]?.subtitle
        || ''
}

/** Краткое описание товара */
const getProductShort = (product) => {
    return product?.short
        || product?.description
        || product?.translation?.short
        || product?.translation?.description
        || product?.current_translation?.short
        || product?.current_translation?.description
        || product?.translations?.[0]?.short
        || product?.translations?.[0]?.description
        || ''
}

/** Название компании */
const getCompanyTitle = (product) => {
    return product?.company?.title
        || product?.company?.legal_name
        || ''
}

/** Название магазина */
const getShopTitle = (product) => {
    return product?.shop?.title || ''
}

/** Название бренда */
const getBrandTitle = (product) => {
    return product?.brand?.title || ''
}

/** Локальный поиск */
const filteredProducts = computed(() => {
    const query = normalizeText(q.value).trim()

    if (!query) {
        return productsData.value
    }

    return productsData.value.filter((product) => {
        return [
            getProductTitle(product),
            getProductSubtitle(product),
            getProductShort(product),
            product.url,
            product.sku,
            product.vendor_code,
            product.barcode,
            getCompanyTitle(product),
            getShopTitle(product),
            getBrandTitle(product),
        ].some((value) => {
            return normalizeText(value).includes(query)
        })
    })
})

/** Локальная сортировка */
const sortedProducts = computed(() => {
    const list = [...filteredProducts.value]

    return list.sort((a, b) => {
        switch (sort.value) {
            case 'idAsc':
                return normalizeNumber(a.id) - normalizeNumber(b.id)

            case 'idDesc':
                return normalizeNumber(b.id) - normalizeNumber(a.id)

            case 'sortAsc':
                return normalizeNumber(a.sort) - normalizeNumber(b.sort)

            case 'sortDesc':
                return normalizeNumber(b.sort) - normalizeNumber(a.sort)

            case 'titleAsc':
                return normalizeText(getProductTitle(a))
                    .localeCompare(normalizeText(getProductTitle(b)))

            case 'titleDesc':
                return normalizeText(getProductTitle(b))
                    .localeCompare(normalizeText(getProductTitle(a)))

            case 'priceAsc':
                return normalizeNumber(a.price) - normalizeNumber(b.price)

            case 'priceDesc':
                return normalizeNumber(b.price) - normalizeNumber(a.price)

            case 'quantityAsc':
                return normalizeNumber(a.quantity) - normalizeNumber(b.quantity)

            case 'quantityDesc':
                return normalizeNumber(b.quantity) - normalizeNumber(a.quantity)

            case 'viewsAsc':
                return normalizeNumber(a.views) - normalizeNumber(b.views)

            case 'viewsDesc':
                return normalizeNumber(b.views) - normalizeNumber(a.views)

            case 'likesAsc':
                return normalizeNumber(a.likes_count) - normalizeNumber(b.likes_count)

            case 'likesDesc':
                return normalizeNumber(b.likes_count) - normalizeNumber(a.likes_count)

            case 'ratingAsc':
                return normalizeNumber(a.rating_avg) - normalizeNumber(b.rating_avg)

            case 'ratingDesc':
                return normalizeNumber(b.rating_avg) - normalizeNumber(a.rating_avg)

            case 'ratingCountAsc':
                return normalizeNumber(a.rating_count) - normalizeNumber(b.rating_count)

            case 'ratingCountDesc':
                return normalizeNumber(b.rating_count) - normalizeNumber(a.rating_count)

            case 'reviewsAsc':
                return normalizeNumber(a.reviews_count) - normalizeNumber(b.reviews_count)

            case 'reviewsDesc':
                return normalizeNumber(b.reviews_count) - normalizeNumber(a.reviews_count)

            case 'brandAsc':
                return normalizeText(getBrandTitle(a))
                    .localeCompare(normalizeText(getBrandTitle(b)))

            case 'brandDesc':
                return normalizeText(getBrandTitle(b))
                    .localeCompare(normalizeText(getBrandTitle(a)))

            case 'publishedAtAsc':
                return normalizeDate(a.published_at) - normalizeDate(b.published_at)

            case 'publishedAtDesc':
                return normalizeDate(b.published_at) - normalizeDate(a.published_at)

            case 'createdAtAsc':
                return normalizeDate(a.created_at) - normalizeDate(b.created_at)

            case 'createdAtDesc':
                return normalizeDate(b.created_at) - normalizeDate(a.created_at)

            default:
                return 0
        }
    })
})

/**
 * Frontend-пагинация.
 *
 * Использует то же per_page,
 * которое определил backend.
 */
const frontendPaginatedProducts = computed(() => {
    const start = (
        frontendCurrentPage.value - 1
    ) * perPage.value

    return sortedProducts.value.slice(
        start,
        start + perPage.value
    )
})

/** Сбрасываем frontend-пагинацию */
watch([q, sort, viewMode], () => {
    frontendCurrentPage.value = 1
})

/** Скролл при frontend-пагинации */
watch(frontendCurrentPage, () => {
    if (!props.useServerProcessing) {
        scrollToTarget()
    }
})

/* ===================== SERVER MODE ===================== */

/** Текущая server-страница */
const currentPage = computed(() => {
    return Number(
        props.products?.meta?.current_page
        ?? props.products?.current_page
        ?? 1
    ) || 1
})

/** Последняя server-страница */
const lastPage = computed(() => {
    return Number(
        props.products?.meta?.last_page
        ?? props.products?.last_page
        ?? 1
    ) || 1
})

/** Маршрут списка товаров */
const indexRoute = () => {
    return route('public.marketProducts.index')
}

/**
 * Server-загрузка товаров.
 *
 * per_page намеренно не отправляем.
 * Его всегда определяет backend через PublicSettingsService.
 */
const reloadProducts = (page = 1) => {
    router.get(
        indexRoute(),
        {
            q: q.value || undefined,
            sort: sort.value || undefined,
            view: viewMode.value || undefined,
            page,
        },
        {
            preserveState: true,
            replace: true,
            preserveScroll: true,
        }
    )
}

/** Server-поиск */
const submitSearch = () => {
    reloadProducts(1)
}

/** Сброс поиска и сортировки */
const resetSearch = () => {
    q.value = ''
    sort.value = DEFAULT_SORT
    frontendCurrentPage.value = 1

    if (props.useServerProcessing) {
        reloadProducts(1)
    }
}

/** Изменение сортировки */
const updateSort = (value) => {
    sort.value = value || DEFAULT_SORT

    if (props.useServerProcessing) {
        reloadProducts(1)
    }
}

/** Изменение режима отображения */
const updateViewMode = (value) => {
    viewMode.value = value || 'grid'
    frontendCurrentPage.value = 1

    if (props.useServerProcessing) {
        reloadProducts(1)
    }
}

/** Server-переход на страницу */
const goToPage = (page) => {
    const value = Number(page)

    if (!Number.isFinite(value)) {
        return
    }

    const safePage = Math.max(
        1,
        Math.min(value, lastPage.value)
    )

    reloadProducts(safePage)
}

/** Предыдущая server-страница */
const goPrev = () => {
    if (currentPage.value <= 1) {
        return
    }

    goToPage(currentPage.value - 1)
}

/** Следующая server-страница */
const goNext = () => {
    if (currentPage.value >= lastPage.value) {
        return
    }

    goToPage(currentPage.value + 1)
}

/* ===================== COMMON VIEW ===================== */

/** Итоговый список товаров */
const displayedProducts = computed(() => {
    return props.useServerProcessing
        ? productsData.value
        : frontendPaginatedProducts.value
})
</script>

<template>
    <!-- SEO -->
    <Head>
        <title>{{ seo?.title || t('products') }}</title>

        <meta name="title" :content="seo?.title || t('products')" />
        <meta name="keywords" :content="seo?.keywords || ''" />
        <meta name="description" :content="seo?.description || ''" />
        <meta property="og:title" :content="seo?.title || t('products')" />
        <meta property="og:description" :content="seo?.description || ''" />
        <meta property="og:type" content="website" />
        <meta property="og:url" :content="`/${locale}/catalog/products`" />
        <meta property="og:image" content="" />
        <meta property="og:locale" :content="locale === 'ru' ? 'ru_RU' : locale" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="seo?.title || t('products')" />
        <meta name="twitter:description" :content="seo?.description || ''" />
        <meta name="twitter:image" content="" />
        <meta name="DC.title" :content="seo?.title || t('products')" />
        <meta name="DC.description" :content="seo?.description || ''" />
        <meta name="DC.identifier" :content="`/${locale}/catalog/products`" />
        <meta name="DC.language" :content="locale" />
    </Head>

    <DefaultLayout
        :title="title"
        :can-login="canLogin"
        :can-register="canRegister"
    >
        <!-- Шапка -->
        <Navbar />

        <div class="min-h-screen px-3 max-w-full">
            <main
                class="mx-auto flex flex-col lg:flex-row gap-4 tracking-wider"
            >
                <!-- Левая колонка -->
                <aside
                    v-if="showLeft"
                    class="shrink-0 mt-12 lg:mt-28 transition-all duration-300"
                    :class="leftCollapsed ? 'lg:w-10' : 'lg:w-64'"
                >
                    <LeftSidebarMarket
                        :category-tree="categoryTree"
                        :collapsed="leftCollapsed"
                        @collapsed="leftCollapsed = $event"
                    />
                </aside>

                <!-- Центральная колонка -->
                <div class="w-full lg:mt-28 pb-6 slate-1">
                    <div class="mx-auto max-w-6xl">

                        <!-- Хлебные крошки -->
                        <nav
                            class="text-sm"
                            aria-label="Breadcrumb"
                        >
                            <ol
                                class="flex flex-wrap items-center font-semibold"
                            >
                                <li>
                                    <Link
                                        :href="route('home')"
                                        class="breadcrumb-link hover:underline"
                                    >
                                        {{ t('home') }}
                                    </Link>
                                </li>

                                <li>
                                    <span class="mx-2 breadcrumbs">
                                        /
                                    </span>
                                </li>

                                <li>
                                    <Link
                                        :href="route('public.marketCategories.index')"
                                        class="breadcrumb-link hover:underline"
                                    >
                                        {{ t('categories') }}
                                    </Link>
                                </li>

                                <li>
                                    <span class="mx-2 breadcrumbs">
                                        /
                                    </span>
                                </li>

                                <li class="breadcrumbs">
                                    {{ t('products') }}
                                </li>
                            </ol>
                        </nav>

                        <!-- Заголовок -->
                        <div
                            class="my-3 flex flex-wrap items-center justify-center gap-3 title"
                        >
                            <svg
                                class="h-6 w-6 text-slate-600/85 dark:text-slate-200/85"
                                fill="currentColor"
                                viewBox="0 0 512 512">
                                <path d="M239.1 6.3l-208 78c-18.7 7-31.1 25-31.1 45v225.1c0 18.2 10.3 34.8 26.5 42.9l208 104c13.5 6.8 29.4 6.8 42.9 0l208-104c16.3-8.1 26.5-24.8 26.5-42.9V129.3c0-20-12.4-37.9-31.1-44.9l-208-78C262 2.2 250 2.2 239.1 6.3zM256 68.4l192 72v1.1l-192 78-192-78v-1.1l192-72zm32 356V275.5l160-65v133.9l-160 80z" />
                            </svg>

                            <h1 class="text-2xl font-bold">
                                {{ t('products') }}
                            </h1>
                        </div>

                        <!-- Подзаголовок -->
                        <div
                            class="my-1 text-sm subtitle text-center"
                        >
                            {{ t('catalogDesc') }}
                        </div>

                        <!-- Server toolbar -->
                        <EntityPageToolbar
                            v-if="useServerProcessing"
                            v-model="q"
                            :found="productsFound"
                            :view-mode="viewMode"
                            :sort-value="sort"
                            :sort-options="productSortOptions"
                            :default-sort="DEFAULT_SORT"
                            :found-label="t('products')"
                            :search-placeholder="t('searchByName')"
                            @submit="submitSearch"
                            @reset="resetSearch"
                            @update:viewMode="updateViewMode"
                            @update:sortValue="updateSort"
                        />

                        <!-- Frontend toolbar -->
                        <FrontendEntityPageToolbar
                            v-else
                            v-model="q"
                            :found="sortedProducts.length"
                            :view-mode="viewMode"
                            :sort-value="sort"
                            :sort-options="productSortOptions"
                            :default-sort="DEFAULT_SORT"
                            :found-label="t('products')"
                            :search-placeholder="t('searchByName')"
                            @reset="resetSearch"
                            @update:viewMode="updateViewMode"
                            @update:sortValue="updateSort"
                        />

                        <!-- Точка скролла -->
                        <div ref="scrollTarget"></div>

                        <!-- Нет данных -->
                        <div
                            v-if="displayedProducts.length === 0"
                            class="mt-6 text-center text-slate-700 dark:text-slate-300"
                        >
                            {{ t('noData') }}
                        </div>

                        <!-- Товары -->
                        <div v-else>
                            <MarketProductGrid
                                v-if="viewMode === 'grid'"
                                :products="displayedProducts"
                                :cols="productGridCols"
                            />

                            <MarketProductRows
                                v-else
                                :products="displayedProducts"
                            />
                        </div>

                        <!-- Server-пагинация -->
                        <Pagination
                            v-if="useServerProcessing"
                            :current-page="currentPage"
                            :last-page="lastPage"
                            :found="productsFound"
                            @prev="goPrev"
                            @next="goNext"
                            @go="goToPage"
                        />

                        <!-- Frontend-пагинация -->
                        <FrontendPagination
                            v-else
                            v-model:currentPage="frontendCurrentPage"
                            :items-per-page="perPage"
                            :total-items="sortedProducts.length"
                        />
                    </div>
                </div>

                <!-- Правая колонка -->
                <aside
                    v-if="showRight"
                    class="shrink-0 lg:mt-28 transition-all duration-300"
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

        <!-- Панель администратора -->
        <PublicAdminBottomPanel
            v-if="isAdmin"
            setting-key="publicMarketProductsProcessingMode"
            :mode="publicMarketProductsProcessingMode"
            :use-server-processing="useServerProcessing"
            :total="productsCount"
        />
    </DefaultLayout>
</template>
