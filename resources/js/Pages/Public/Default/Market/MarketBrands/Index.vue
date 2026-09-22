<script setup>
/**
 * Публичная страница списка брендов маркетплейса.
 *
 * Поддерживает:
 * - server processing;
 * - frontend processing;
 * - auto processing;
 * - поиск;
 * - сортировку;
 * - grid / rows;
 * - server / frontend pagination;
 * - SEO;
 * - Schema.org.
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

import EntityPageToolbar
    from '@/Components/Public/Default/PageToolbar/EntityPageToolbar.vue'
import FrontendEntityPageToolbar
    from '@/Components/Public/Default/PageToolbar/FrontendEntityPageToolbar.vue'

import MarketBrandGrid
    from '@/Components/Public/Default/Market/MarketBrand/MarketBrandGrid.vue'
import MarketBrandRows
    from '@/Components/Public/Default/Market/MarketBrand/MarketBrandRows.vue'

import Pagination
    from '@/Components/Public/Default/Pagination/Pagination.vue'
import FrontendPagination
    from '@/Components/Public/Default/Pagination/FrontendPagination.vue'

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

    useServerProcessing: {
        type: Boolean,
        default: false,
    },

    publicMarketBrandsProcessingMode: {
        type: String,
        default: 'server',
    },

    /** Сортировка Public по умолчанию из backend */
    defaultSort: {
        type: String,
        default: 'sortAsc',
    },

    categoryTree: {
        type: Array,
        default: () => [],
    },

    brands: {
        type: [Array, Object],
        default: () => [],
    },

    brandsCount: {
        type: Number,
        default: 0,
    },

    brandsFound: {
        type: Number,
        default: 0,
    },

    filters: {
        type: Object,
        default: () => ({}),
    },
})

/* ===================== PAGE ===================== */

/** Глобальные данные страницы */
const page = usePage()

/** Глобальные настройки сайта */
const siteSettings = page.props?.siteSettings || {}

/** Роль администратора */
const isAdmin = computed(() => {
    return page.props?.isAdmin === true
})

/** Дерево категорий */
const categoryTree = computed(() => {
    return Array.isArray(props.categoryTree)
        ? props.categoryTree
        : []
})

/* ===================== BRANDS DATA ===================== */

/** Универсальный список брендов */
const brandsData = computed(() => {
    if (Array.isArray(props.brands)) {
        return props.brands
    }

    if (Array.isArray(props.brands?.data)) {
        return props.brands.data
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
const getStoredBoolean = (
    key,
    defaultValue = true
) => {
    const value = localStorage.getItem(key)

    if (value === null) {
        return defaultValue
    }

    return value === 'true'
}

/** Левый сайдбар по умолчанию свернут */
const leftCollapsed = ref(
    getStoredBoolean(
        LEFT_SIDEBAR_KEY,
        true
    )
)

/** Правый сайдбар по умолчанию свернут */
const rightCollapsed = ref(
    getStoredBoolean(
        RIGHT_SIDEBAR_KEY,
        true
    )
)

/**
 * Количество колонок сетки.
 *
 * Оба сайдбара открыты → 2.
 * Один открыт          → 3.
 * Оба свернуты         → 4.
 */
const brandGridCols = computed(() => {
    const leftExpanded =
        showLeft.value
        && !leftCollapsed.value

    const rightExpanded =
        showRight.value
        && !rightCollapsed.value

    if (leftExpanded && rightExpanded) {
        return 2
    }

    if (leftExpanded || rightExpanded) {
        return 3
    }

    return 4
})

/** Сохраняем состояние сайдбаров */
watch(
    [leftCollapsed, rightCollapsed],
    () => {
        localStorage.setItem(
            LEFT_SIDEBAR_KEY,
            String(leftCollapsed.value)
        )

        localStorage.setItem(
            RIGHT_SIDEBAR_KEY,
            String(rightCollapsed.value)
        )
    }
)

/* ===================== FILTERS ===================== */

/** Поисковая строка */
const q = ref(
    String(
        props.filters?.q ?? ''
    )
)

/** Текущая сортировка */
const sort = ref(
    String(
        props.filters?.sort
        ?? props.defaultSort
        ?? ''
    )
)

/** Ключ режима отображения */
const VIEW_KEY = 'public_market_brands_view'

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
    localStorage.setItem(
        VIEW_KEY,
        value
    )
})

/**
 * Количество брендов на странице.
 *
 * Источник значения — backend:
 * PublicSettingsService →
 * resolvePerPage() →
 * filters.per_page.
 */
const perPage = computed(() => {
    const value = Number(
        props.filters?.per_page
    )

    return Number.isFinite(value)
    && value > 0
        ? value
        : 1
})

/** Опции Public-сортировки брендов */
const brandSortOptions = [
    {
        value: 'idDesc',
        label: t('idDesc'),
    },
    {
        value: 'idAsc',
        label: t('idAsc'),
    },

    {
        value: 'sortAsc',
        label: `${t('sortNumber')} 0→9`,
    },
    {
        value: 'sortDesc',
        label: `${t('sortNumber')} 9→0`,
    },

    {
        value: 'titleAsc',
        label: `${t('title')} A→Z`,
    },
    {
        value: 'titleDesc',
        label: `${t('title')} Z→A`,
    },

    {
        value: 'productsDesc',
        label: `${t('products')} 9→0`,
    },
    {
        value: 'productsAsc',
        label: `${t('products')} 0→9`,
    },

    {
        value: 'viewsDesc',
        label: `${t('views')} 9→0`,
    },
    {
        value: 'viewsAsc',
        label: `${t('views')} 0→9`,
    },
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

/**
 * Название бренда.
 *
 * Перевод уже разрешён Laravel:
 * current → fallback → null.
 */
const getBrandTitle = (brand) => {
    return brand?.translation?.title || ''
}

/** Подзаголовок бренда */
const getBrandSubtitle = (brand) => {
    return brand?.translation?.subtitle || ''
}

/** Краткое описание бренда */
const getBrandShort = (brand) => {
    return brand?.translation?.short || ''
}

/**
 * Слова Public-поиска.
 */
const searchWords = computed(() => {
    return normalizeText(q.value)
        .split(/\s+/u)
        .filter((word) => {
            return word.length >= 2
        })
})

/**
 * Локальный Public-поиск.
 *
 * Повторяет публичный поиск модели:
 * - title;
 * - subtitle;
 * - short;
 * - description;
 * - url;
 * - website.
 */
const filteredBrands = computed(() => {
    const words = searchWords.value

    if (!words.length) {
        return brandsData.value
    }

    return brandsData.value.filter(
        (brand) => {
            const fields = [
                getBrandTitle(brand),
                getBrandSubtitle(brand),
                getBrandShort(brand),
                brand?.translation?.description,
                brand?.url,
                brand?.website,
            ].map(normalizeText)

            return words.every((word) => {
                return fields.some((field) => {
                    return field.includes(word)
                })
            })
        }
    )
})

/**
 * Дополнительная сортировка по ID DESC.
 */
const compareIdDesc = (a, b) => {
    return normalizeNumber(b?.id)
        - normalizeNumber(a?.id)
}

/** Сравнение числового поля */
const compareNumber = (
    a,
    b,
    field,
    direction = 'asc'
) => {
    const first = normalizeNumber(
        a?.[field]
    )

    const second = normalizeNumber(
        b?.[field]
    )

    const result = direction === 'desc'
        ? second - first
        : first - second

    return result
        || compareIdDesc(a, b)
}

/** Сравнение текстового значения */
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
 * Локальная Public-сортировка.
 *
 * Набор параметров совпадает
 * с MarketBrand::publicSortByParam().
 */
const sortedBrands = computed(() => {
    const list = [
        ...filteredBrands.value,
    ]

    return list.sort((a, b) => {
        switch (sort.value) {
            case 'idAsc':
                return normalizeNumber(a?.id)
                    - normalizeNumber(b?.id)

            case 'idDesc':
                return normalizeNumber(b?.id)
                    - normalizeNumber(a?.id)

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
                    getBrandTitle(a),
                    getBrandTitle(b),
                    'asc'
                )

                return result
                    || compareIdDesc(a, b)
            }

            case 'titleDesc': {
                const result = compareText(
                    getBrandTitle(a),
                    getBrandTitle(b),
                    'desc'
                )

                return result
                    || compareIdDesc(a, b)
            }

            case 'productsAsc':
                return compareNumber(
                    a,
                    b,
                    'products_count',
                    'asc'
                )

            case 'productsDesc':
                return compareNumber(
                    a,
                    b,
                    'products_count',
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
 */
const frontendPaginatedBrands = computed(() => {
    const start = (
        frontendCurrentPage.value - 1
    ) * perPage.value

    return sortedBrands.value.slice(
        start,
        start + perPage.value
    )
})

/**
 * При изменении поиска, сортировки
 * или режима отображения возвращаемся
 * на первую frontend-страницу.
 */
watch(
    [q, sort, viewMode],
    () => {
        frontendCurrentPage.value = 1
    }
)

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
        props.brands?.meta?.current_page
        ?? props.brands?.current_page
        ?? 1
    ) || 1
})

/** Последняя server-страница */
const lastPage = computed(() => {
    return Number(
        props.brands?.meta?.last_page
        ?? props.brands?.last_page
        ?? 1
    ) || 1
})

/** URL логотипа бренда */
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

/* ===================== SEO ===================== */

/** SEO title страницы брендов */
const seoTitle = computed(() => {
    return props.seo?.title
        || t('brands')
})

/** SEO keywords */
const seoKeywords = computed(() => {
    return props.seo?.keywords || ''
})

/** SEO description */
const seoDescription = computed(() => {
    return props.seo?.description
        || t('brands')
        || ''
})

/**
 * Активная локаль приходит
 * исключительно от backend.
 */
const contentLocale = computed(() => {
    return String(
        props.locale || ''
    )
})

/** Open Graph locale */
const ogLocale = computed(() => {
    return contentLocale.value
})

/** Dublin Core subject */
const dcSubject = computed(() => {
    return seoKeywords.value
        || seoTitle.value
})

/**
 * Первое доступное изображение бренда
 * используется как social preview.
 *
 * При отсутствии изображения может
 * использоваться реальный логотип бренда.
 */
const seoPreview = computed(() => {
    for (const brand of brandsData.value) {
        const images = normalizeList(
            brand?.images
        )

        const image = images[0]

        const url =
            image?.webp_url
            || image?.image_url
            || image?.thumb_url
            || image?.url
            || getBrandLogo(brand)
            || ''

        if (url) {
            return {
                url,
                alt:
                    image?.alt
                    || getBrandTitle(brand)
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
 * Канонический URL списка брендов.
 *
 * Search, sort и view в canonical
 * намеренно не включаются.
 */
const canonicalUrl = computed(() => {
    const baseUrl = String(
        route('public.marketBrands.index')
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
 * Поисковую и альтернативно
 * отсортированную server-выдачу
 * не индексируем.
 */
const robotsContent = computed(() => {
    const hasSearch =
        String(q.value || '').trim() !== ''

    const hasAlternativeSort =
        String(
            sort.value
            || props.defaultSort
        ) !== String(
            props.defaultSort
        )

    if (
        props.useServerProcessing
        && (
            hasSearch
            || hasAlternativeSort
        )
    ) {
        return 'noindex, follow, max-image-preview:large'
    }

    return 'index, follow, max-image-preview:large'
})

/* ===================== SERVER ACTIONS ===================== */

/** Маршрут списка брендов */
const indexRoute = () => {
    return route(
        'public.marketBrands.index'
    )
}

/**
 * Server-загрузка брендов.
 *
 * per_page намеренно не отправляем:
 * значение всегда определяет backend.
 */
const reloadBrands = (page = 1) => {
    router.get(
        indexRoute(),
        {
            q:
                q.value
                || undefined,

            sort:
                sort.value
                || props.defaultSort
                || undefined,

            view:
                viewMode.value
                || undefined,

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
    frontendCurrentPage.value = 1

    if (props.useServerProcessing) {
        reloadBrands(1)
    }
}

/** Сброс поиска и сортировки */
const resetSearch = () => {
    q.value = ''

    sort.value =
        props.defaultSort
        || ''

    frontendCurrentPage.value = 1

    if (props.useServerProcessing) {
        reloadBrands(1)
    }
}

/** Изменение сортировки */
const updateSort = (value) => {
    sort.value =
        value
        || props.defaultSort
        || ''

    frontendCurrentPage.value = 1

    if (props.useServerProcessing) {
        reloadBrands(1)
    }
}

/** Изменение режима отображения */
const updateViewMode = (value) => {
    viewMode.value =
        value
        || 'grid'

    frontendCurrentPage.value = 1

    if (props.useServerProcessing) {
        reloadBrands(1)
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
        Math.min(
            value,
            lastPage.value
        )
    )

    reloadBrands(safePage)
}

/** Предыдущая server-страница */
const goPrev = () => {
    if (currentPage.value <= 1) {
        return
    }

    goToPage(
        currentPage.value - 1
    )
}

/** Следующая server-страница */
const goNext = () => {
    if (
        currentPage.value
        >= lastPage.value
    ) {
        return
    }

    goToPage(
        currentPage.value + 1
    )
}

/* ===================== COMMON VIEW ===================== */

/** Итоговый список брендов */
const displayedBrands = computed(() => {
    return props.useServerProcessing
        ? brandsData.value
        : frontendPaginatedBrands.value
})

/**
 * Начальная позиция бренда
 * для Schema.org ItemList.
 */
const brandListStartPosition = computed(() => {
    const pageNumber =
        props.useServerProcessing
            ? currentPage.value
            : frontendCurrentPage.value

    return Math.max(
        0,
        (
            pageNumber - 1
        ) * perPage.value
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
            v-if="contentLocale"
            http-equiv="content-language"
            :content="contentLocale"
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
            <div class="mx-auto tracking-wider pt-20 lg:pt-44">
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

                                <meta
                                    v-if="seoImage"
                                    itemprop="primaryImageOfPage"
                                    :content="seoImage"
                                />

                                <!-- Хлебные крошки -->
                                <nav
                                    class="text-sm"
                                    aria-label="Breadcrumb"
                                    itemscope
                                    itemtype="https://schema.org/BreadcrumbList"
                                >
                                    <ol
                                        class="flex flex-wrap items-center font-semibold"
                                    >
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

                                        <!-- Бренды -->
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
                                                {{ t('brands') }}
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

                                <!-- Server toolbar -->
                                <EntityPageToolbar
                                    v-if="useServerProcessing"
                                    v-model="q"
                                    :found="brandsFound"
                                    :view-mode="viewMode"
                                    :sort-value="sort"
                                    :sort-options="brandSortOptions"
                                    :default-sort="defaultSort"
                                    :found-label="t('brands')"
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
                                    :found="sortedBrands.length"
                                    :view-mode="viewMode"
                                    :sort-value="sort"
                                    :sort-options="brandSortOptions"
                                    :default-sort="defaultSort"
                                    :found-label="t('brands')"
                                    :search-placeholder="t('searchByName')"
                                    @reset="resetSearch"
                                    @update:viewMode="updateViewMode"
                                    @update:sortValue="updateSort"
                                />

                                <!-- Точка скролла -->
                                <div ref="scrollTarget"></div>

                                <!-- Нет данных -->
                                <div
                                    v-if="displayedBrands.length === 0"
                                    class="mt-6 text-center text-slate-700 dark:text-slate-300"
                                >
                                    {{ t('noData') }}
                                </div>

                                <!-- Бренды -->
                                <div
                                    v-else
                                    itemprop="mainEntity"
                                >
                                    <MarketBrandGrid
                                        v-if="viewMode === 'grid'"
                                        :brands="displayedBrands"
                                        :cols="brandGridCols"
                                        :start-position="brandListStartPosition"
                                    />

                                    <MarketBrandRows
                                        v-else
                                        :brands="displayedBrands"
                                        :start-position="brandListStartPosition"
                                    />
                                </div>

                                <!-- Server-пагинация -->
                                <Pagination
                                    v-if="useServerProcessing"
                                    :current-page="currentPage"
                                    :last-page="lastPage"
                                    :found="brandsFound"
                                    @prev="goPrev"
                                    @next="goNext"
                                    @go="goToPage"
                                />

                                <!-- Frontend-пагинация -->
                                <FrontendPagination
                                    v-else
                                    v-model:currentPage="frontendCurrentPage"
                                    :items-per-page="perPage"
                                    :total-items="sortedBrands.length"
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
            </div>
        </main>

        <!-- Подвал -->
        <FooterBlog />

        <!-- Прогресс -->
        <Progress />

        <!-- Панель администратора -->
        <PublicAdminBottomPanel
            v-if="isAdmin"
            setting-key="publicMarketBrandsProcessingMode"
            :mode="publicMarketBrandsProcessingMode"
            :use-server-processing="useServerProcessing"
            :total="brandsCount"
        />
    </DefaultLayout>
</template>
