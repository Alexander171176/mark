<script setup>
/**
 * Страница списка товаров маркетплейса.
 *
 * @version PulsarCMS 1.0
 * @author Александр
 */

import { computed, onMounted, ref, watch } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useSmoothScrollTo } from '@/composables/useSmoothScrollTo'

import DefaultLayout from '@/Layouts/DefaultLayout.vue'
import Navbar from '@/Partials/Default/Navbar.vue'
import FooterBlog from '@/Partials/Default/FooterBlog.vue'
import Progress from '@/Components/Public/Default/Progress/Progress.vue'

import LeftSidebarMarket from '@/Components/Public/Default/Partials/LeftSidebarMarket.vue'
import RightSidebarMarket from '@/Components/Public/Default/Partials/RightSidebarMarket.vue'

import EntityListToolbar from '@/Components/Public/Default/PageToolbar/EntityListToolbar.vue'

import MarketProductGrid
    from '@/Components/Public/Default/Market/MarketProduct/MarketProductGrid.vue'
import MarketProductRows
    from '@/Components/Public/Default/Market/MarketProduct/MarketProductRows.vue'

import MarketRecentlyViewedProducts
    from '@/Components/Public/Default/Market/MarketProduct/MarketRecentlyViewedProducts.vue'

import { useRecentlyViewedProducts }
    from '@/composables/market/useRecentlyViewedProducts'

import Pagination from '@/Components/Public/Default/Pagination/Pagination.vue'
import FrontendPagination from '@/Components/Public/Default/Pagination/FrontendPagination.vue'

import PublicAdminBottomPanel
    from '@/Components/Admin/UI/PublicAdminPanel/PublicAdminBottomPanel.vue'

const { t } = useI18n()

/* ===================== PROPS ===================== */

/** Props страницы */
const props = defineProps({
    locale: {
        type: String,
        default: '',
    },

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

    /** Сортировка Public по умолчанию из backend */
    defaultSort: { type: String, default: 'sortAsc' },

    categoryTree: { type: Array, default: () => [] },

    products: { type: [Array, Object], default: () => [] },
    productsCount: { type: Number, default: 0 },
    productsFound: { type: Number, default: 0 },

    /** Недавно просмотренные товары */
    recentlyViewedProducts: {
        type: [Array, Object],
        default: () => [],
    },

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

/* ===================== RECENTLY VIEWED ===================== */

/**
 * История недавно просмотренных товаров.
 *
 * Авторизованный пользователь получает
 * начальную историю от Laravel через Inertia.
 *
 * Для гостя ID хранятся в localStorage,
 * а актуальные карточки загружаются через API.
 */
const {
    products: recentlyViewed,
    load: loadRecentlyViewedProducts,
    mergeGuestHistory,
    setProducts: setRecentlyViewedProducts,
} = useRecentlyViewedProducts()

/** История, пришедшая от backend */
const initialRecentlyViewedProducts = computed(() => {
    return normalizeList(
        props.recentlyViewedProducts
    )
})

/**
 * Инициализация истории.
 */
onMounted(async () => {
    /**
     * Авторизованный пользователь.
     *
     * Laravel уже передал историю,
     * поэтому сначала используем готовые данные.
     */
    if (page.props?.auth?.user) {
        setRecentlyViewedProducts(
            initialRecentlyViewedProducts.value
        )

        /**
         * Если пользователь до авторизации
         * смотрел товары как гость,
         * объединяем localStorage с БД.
         */
        await mergeGuestHistory()

        return
    }

    /**
     * Гость.
     *
     * Получаем ID из localStorage и через backend
     * загружаем актуальные данные товаров.
     */
    await loadRecentlyViewedProducts()
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

/** Текущая сортировка */
const sort = ref(
    String(
        props.filters?.sort
        ?? props.defaultSort
    )
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
    return String(value ?? '')
        .toLocaleLowerCase()
        .trim()
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

/**
 * Название товара.
 *
 * Перевод уже разрешён Laravel:
 * current → fallback → null.
 */
const getProductTitle = (product) => {
    return product?.translation?.title || ''
}

/** Подзаголовок товара */
const getProductSubtitle = (product) => {
    return product?.translation?.subtitle || ''
}

/** Краткое описание товара */
const getProductShort = (product) => {
    return product?.translation?.short || ''
}

/** Название бренда */
const getBrandTitle = (product) => {
    return product?.brand?.translation?.title || ''
}

/**
 * Слова Public-поиска.
 *
 * Полностью повторяет backend:
 * - разделение по пробелам;
 * - слова от 2 символов;
 * - AND между словами;
 * - OR между полями.
 */
const searchWords = computed(() => {
    return normalizeText(q.value)
        .split(/\s+/u)
        .filter((word) => word.length >= 2)
})

/**
 * Локальный Public-поиск.
 *
 * Поля совпадают с MarketProduct::publicSearch():
 * title, subtitle, short,
 * url, sku, vendor_code, barcode,
 * brand.translation.title.
 */
const filteredProducts = computed(() => {
    const words = searchWords.value

    if (!words.length) {
        return productsData.value
    }

    return productsData.value.filter((product) => {
        const fields = [
            getProductTitle(product),
            getProductSubtitle(product),
            getProductShort(product),

            product?.url,
            product?.sku,
            product?.vendor_code,
            product?.barcode,

            getBrandTitle(product),
        ].map(normalizeText)

        return words.every((word) => {
            return fields.some((field) => {
                return field.includes(word)
            })
        })
    })
})

/**
 * Дополнительная сортировка по ID DESC.
 *
 * Повторяет второй orderBy backend
 * для одинаковых значений.
 */
const compareIdDesc = (a, b) => {
    return normalizeNumber(b?.id)
        - normalizeNumber(a?.id)
}

/**
 * Сравнение числового поля.
 */
const compareNumber = (
    a,
    b,
    field,
    direction = 'asc'
) => {
    const first = normalizeNumber(a?.[field])
    const second = normalizeNumber(b?.[field])

    const result = direction === 'desc'
        ? second - first
        : first - second

    return result || compareIdDesc(a, b)
}

/**
 * Сравнение текстового значения.
 */
const compareText = (
    first,
    second,
    direction = 'asc'
) => {
    const a = normalizeText(first)
    const b = normalizeText(second)

    return direction === 'desc'
        ? b.localeCompare(a)
        : a.localeCompare(b)
}

/**
 * Сравнение даты.
 */
const compareDate = (
    a,
    b,
    field,
    direction = 'asc'
) => {
    const first = normalizeDate(a?.[field])
    const second = normalizeDate(b?.[field])

    const result = direction === 'desc'
        ? second - first
        : first - second

    return result || compareIdDesc(a, b)
}

/**
 * Локальная Public-сортировка.
 *
 * Набор sort-параметров совпадает
 * с MarketProduct::publicSortByParam().
 */
const sortedProducts = computed(() => {
    const list = [...filteredProducts.value]

    return list.sort((a, b) => {
        switch (sort.value) {
            case 'idAsc':
                return normalizeNumber(a.id)
                    - normalizeNumber(b.id)

            case 'idDesc':
                return normalizeNumber(b.id)
                    - normalizeNumber(a.id)

            case 'sortAsc':
                return compareNumber(
                    a,
                    b,
                    'sort',
                    'asc'
                )

            case 'sortDesc':
                return compareNumber(
                    a,
                    b,
                    'sort',
                    'desc'
                )

            case 'titleAsc': {
                const result = compareText(
                    getProductTitle(a),
                    getProductTitle(b),
                    'asc'
                )

                return result || compareIdDesc(a, b)
            }

            case 'titleDesc': {
                const result = compareText(
                    getProductTitle(a),
                    getProductTitle(b),
                    'desc'
                )

                return result || compareIdDesc(a, b)
            }

            case 'priceAsc':
                return compareNumber(
                    a,
                    b,
                    'price',
                    'asc'
                )

            case 'priceDesc':
                return compareNumber(
                    a,
                    b,
                    'price',
                    'desc'
                )

            case 'quantityAsc':
                return compareNumber(
                    a,
                    b,
                    'quantity',
                    'asc'
                )

            case 'quantityDesc':
                return compareNumber(
                    a,
                    b,
                    'quantity',
                    'desc'
                )

            case 'viewsAsc':
                return compareNumber(
                    a,
                    b,
                    'views',
                    'asc'
                )

            case 'viewsDesc':
                return compareNumber(
                    a,
                    b,
                    'views',
                    'desc'
                )

            case 'likesAsc':
                return compareNumber(
                    a,
                    b,
                    'likes_count',
                    'asc'
                )

            case 'likesDesc':
                return compareNumber(
                    a,
                    b,
                    'likes_count',
                    'desc'
                )

            case 'ratingAsc':
                return compareNumber(
                    a,
                    b,
                    'rating_avg',
                    'asc'
                )

            case 'ratingDesc':
                return compareNumber(
                    a,
                    b,
                    'rating_avg',
                    'desc'
                )

            case 'ratingCountAsc':
                return compareNumber(
                    a,
                    b,
                    'rating_count',
                    'asc'
                )

            case 'ratingCountDesc':
                return compareNumber(
                    a,
                    b,
                    'rating_count',
                    'desc'
                )

            case 'reviewsAsc':
                return compareNumber(
                    a,
                    b,
                    'reviews_count',
                    'asc'
                )

            case 'reviewsDesc':
                return compareNumber(
                    a,
                    b,
                    'reviews_count',
                    'desc'
                )

            case 'brandAsc': {
                const result = compareText(
                    getBrandTitle(a),
                    getBrandTitle(b),
                    'asc'
                )

                return result || compareIdDesc(a, b)
            }

            case 'brandDesc': {
                const result = compareText(
                    getBrandTitle(a),
                    getBrandTitle(b),
                    'desc'
                )

                return result || compareIdDesc(a, b)
            }

            case 'publishedAtAsc':
                return compareDate(
                    a,
                    b,
                    'published_at',
                    'asc'
                )

            case 'publishedAtDesc':
                return compareDate(
                    a,
                    b,
                    'published_at',
                    'desc'
                )

            case 'createdAtAsc':
                return compareDate(
                    a,
                    b,
                    'created_at',
                    'asc'
                )

            case 'createdAtDesc':
                return compareDate(
                    a,
                    b,
                    'created_at',
                    'desc'
                )

            default:
                return compareNumber(
                    a,
                    b,
                    'sort',
                    'asc'
                )
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

/* ===================== SEO ===================== */

/** SEO title страницы каталога */
const seoTitle = computed(() => {
    return props.seo?.title
        || t('products')
})

/** SEO keywords */
const seoKeywords = computed(() => {
    return props.seo?.keywords || ''
})

/** SEO description */
const seoDescription = computed(() => {
    return props.seo?.description
        || t('catalogDesc')
        || ''
})

/**
 * Активная локаль приходит
 * исключительно от backend.
 *
 * Vue не определяет fallback-язык.
 */
const contentLocale = computed(() => {
    return String(props.locale || '')
})

/**
 * Open Graph locale.
 *
 * Значение локали определяет backend.
 */
const ogLocale = computed(() => {
    return contentLocale.value
})

/** Dublin Core subject */
const dcSubject = computed(() => {
    return seoKeywords.value
        || seoTitle.value
})

/**
 * Первое доступное изображение товара
 * используется как social preview каталога.
 */
const seoPreview = computed(() => {
    for (const product of productsData.value) {
        const images = normalizeList(
            product?.images
        )

        const image = images[0]

        const url =
            image?.webp_url
            || image?.image_url
            || image?.thumb_url
            || image?.url
            || ''

        if (url) {
            return {
                url,
                alt:
                    getProductTitle(product)
                    || seoTitle.value,
            }
        }
    }

    return {
        url: '',
        alt: '',
    }
})

/** Изображение social preview */
const seoImage = computed(() => {
    return seoPreview.value.url
})

/** Alt изображения social preview */
const seoImageAlt = computed(() => {
    return seoPreview.value.alt
})

/**
 * Канонический URL каталога.
 *
 * Каждая страница серверной пагинации имеет
 * собственный canonical:
 *
 * /catalog/products
 * /catalog/products?page=2
 * /catalog/products?page=3
 *
 * Поиск, сортировка и режим grid/rows
 * в canonical намеренно не включаются.
 */
const canonicalUrl = computed(() => {
    const baseUrl = String(
        route('public.marketProducts.index')
    )

    if (
        props.useServerProcessing
        && currentPage.value > 1
    ) {
        return `${baseUrl}?page=${currentPage.value}`
    }

    return baseUrl
})

/**
 * URL поисковой или альтернативно
 * отсортированной выдачи не индексируем.
 *
 * Обычный каталог и страницы пагинации
 * остаются index, follow.
 */
const robotsContent = computed(() => {
    const hasSearch = String(q.value || '').trim() !== ''

    const hasAlternativeSort =
        String(sort.value || props.defaultSort) !== String(props.defaultSort)

    if (
        props.useServerProcessing
        && (hasSearch || hasAlternativeSort)
    ) {
        return 'noindex, follow, max-image-preview:large'
    }

    return 'index, follow, max-image-preview:large'
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

/** Изменение сортировки */
const updateSort = (value) => {
    sort.value = value || props.defaultSort

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

/** Начальная позиция товара для Schema.org ItemList */
const productListStartPosition = computed(() => {
    const pageNumber = props.useServerProcessing
        ? currentPage.value
        : frontendCurrentPage.value

    return Math.max(
        0,
        (pageNumber - 1) * perPage.value
    )
})
</script>

<template>
    <!-- SEO -->
    <Head>
        <!-- Basic SEO -->
        <title>{{ seoTitle }}</title>

        <meta
            name="title"
            :content="seoTitle"
        />

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
            :content="robotsContent"
        />

        <!-- Canonical -->
        <link
            rel="canonical"
            :href="canonicalUrl"
        />

        <!-- Open Graph -->
        <meta
            property="og:type"
            content="website"
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
            property="og:url"
            :content="canonicalUrl"
        />

        <meta
            v-if="ogLocale"
            property="og:locale"
            :content="ogLocale"
        />

        <meta
            v-if="seoImage"
            property="og:image"
            :content="seoImage"
        />

        <meta
            v-if="seoImage && seoImageAlt"
            property="og:image:alt"
            :content="seoImageAlt"
        />

        <!-- Twitter / X -->
        <meta
            name="twitter:card"
            :content="
            seoImage
                ? 'summary_large_image'
                : 'summary'
        "
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
            v-if="seoImage"
            name="twitter:image"
            :content="seoImage"
        />

        <meta
            v-if="seoImage && seoImageAlt"
            name="twitter:image:alt"
            :content="seoImageAlt"
        />

        <!-- Dublin Core -->
        <meta
            name="DC.title"
            :content="seoTitle"
        />

        <meta
            v-if="seoDescription"
            name="DC.description"
            :content="seoDescription"
        />

        <meta
            v-if="dcSubject"
            name="DC.subject"
            :content="dcSubject"
        />

        <meta
            v-if="contentLocale"
            name="DC.language"
            :content="contentLocale"
        />

        <meta
            name="DC.identifier"
            :content="canonicalUrl"
        />

        <meta
            name="DC.type"
            content="Collection"
        />

        <meta
            name="DC.format"
            content="text/html"
        />
    </Head>

    <DefaultLayout>
        <!-- Шапка -->
        <Navbar />

        <main class="min-h-screen px-1 lg:px-6 max-w-full">
            <div
                class="mx-auto tracking-wider pt-20 lg:pt-44"
            >
                <div
                    class="ext-color w-full min-w-0 py-3 px-1
                           flex flex-col lg:flex-row gap-4 rounded-3xl
                           border-2 border-slate-300 dark:border-slate-500"
                >
                    <!-- Левая колонка -->
                    <aside
                        v-if="showLeft"
                        class="shrink-0 transition-all duration-300"
                        :class="leftCollapsed ? 'lg:w-6' : 'lg:w-72'"
                    >
                        <LeftSidebarMarket
                            :category-tree="categoryTree"
                            :collapsed="leftCollapsed"
                            @collapsed="leftCollapsed = $event"
                        />
                    </aside>

                    <!-- Центральная колонка -->
                    <div class="min-w-0 flex-1 pb-6 slate-1">
                        <div class="w-full">

                            <article
                                itemscope
                                itemtype="https://schema.org/CollectionPage"
                                :itemid="canonicalUrl"
                            >
                                <!-- Мета-данные страницы каталога -->
                                <meta
                                    itemprop="url"
                                    :content="canonicalUrl"
                                />

                                <meta
                                    itemprop="name"
                                    :content="seoTitle"
                                />

                                <meta
                                    v-if="seoDescription"
                                    itemprop="description"
                                    :content="seoDescription"
                                />

                                <meta
                                    v-if="seoKeywords"
                                    itemprop="keywords"
                                    :content="seoKeywords"
                                />

                                <meta
                                    v-if="contentLocale"
                                    itemprop="inLanguage"
                                    :content="contentLocale"
                                />

                                <!-- Хлебные крошки -->
                                <nav
                                    class="text-sm"
                                    aria-label="Breadcrumb"
                                    itemscope
                                    itemtype="https://schema.org/BreadcrumbList"
                                >
                                    <ol class="flex flex-wrap items-center font-semibold">
                                        <!-- Главная -->
                                        <li
                                            itemprop="itemListElement"
                                            itemscope
                                            itemtype="https://schema.org/ListItem"
                                            class="flex items-center"
                                        >
                                            <Link
                                                itemprop="item"
                                                :href="route('home')"
                                                class="breadcrumb-link hover:underline"
                                            >
                                                <span itemprop="name">
                                                    {{ t('home') }}
                                                </span>
                                            </Link>

                                            <meta
                                                itemprop="position"
                                                content="1"
                                            />
                                        </li>

                                        <!-- Категории -->
                                        <li
                                            itemprop="itemListElement"
                                            itemscope
                                            itemtype="https://schema.org/ListItem"
                                            class="flex items-center"
                                        >
                                            <span class="mx-2 breadcrumbs">
                                                /
                                            </span>

                                            <Link
                                                itemprop="item"
                                                :href="route('public.marketCategories.index')"
                                                class="breadcrumb-link hover:underline"
                                            >
                                                <span itemprop="name">
                                                    {{ t('categories') }}
                                                </span>
                                            </Link>

                                            <meta
                                                itemprop="position"
                                                content="2"
                                            />
                                        </li>

                                        <!-- Товары -->
                                        <li
                                            itemprop="itemListElement"
                                            itemscope
                                            itemtype="https://schema.org/ListItem"
                                            class="flex items-center"
                                            aria-current="page"
                                        >
                                            <span class="mx-2 breadcrumbs">
                                                /
                                            </span>

                                            <h1
                                                itemprop="name"
                                                class="breadcrumbs text-sm font-semibold"
                                            >
                                                {{ t('products') }}
                                            </h1>

                                            <meta
                                                itemprop="item"
                                                :content="canonicalUrl"
                                            />

                                            <meta
                                                itemprop="position"
                                                content="3"
                                            />
                                        </li>
                                    </ol>
                                </nav>

                                <!-- Управление списком товаров -->
                                <EntityListToolbar
                                    :found="useServerProcessing ? productsFound : sortedProducts.length"
                                    :view-mode="viewMode"
                                    :sort-value="sort"
                                    :sort-options="productSortOptions"
                                    :found-label="t('products')"
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
                                        :start-position="productListStartPosition"
                                    />

                                    <MarketProductRows
                                        v-else
                                        :products="displayedProducts"
                                        :start-position="productListStartPosition"
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
                            </article>

                        </div>
                    </div>

                    <!-- Правая колонка -->
                    <aside
                        v-if="showRight"
                        class="shrink-0 transition-all duration-300"
                        :class="rightCollapsed ? 'lg:w-6' : 'lg:w-72'"
                    >
                        <RightSidebarMarket
                            :collapsed="rightCollapsed"
                            @collapsed="rightCollapsed = $event"
                        />
                    </aside>
                </div>

                <!-- Недавно просмотренные товары -->
                <MarketRecentlyViewedProducts
                    :products="recentlyViewed"
                />
            </div>
        </main>

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
