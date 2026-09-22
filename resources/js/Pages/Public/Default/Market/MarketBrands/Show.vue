<script setup>
/**
 * Публичная страница бренда маркетплейса.
 *
 * - Public Resource бренда
 * - связанные Public-товары
 * - server / frontend / auto
 * - поиск и сортировка товаров
 * - grid / rows
 * - server / frontend pagination
 * - SEO / Open Graph / Twitter / Dublin Core
 * - Schema.org BreadcrumbList / Brand / ItemList / Product
 *
 * @version PulsarCMS 1.0
 * @author Александр
 */

import {
    computed,
    ref,
    watch
} from 'vue'

import {
    Head,
    Link,
    router,
    usePage
} from '@inertiajs/vue3'

import { useI18n } from 'vue-i18n'

import { useSmoothScrollTo }
    from '@/composables/useSmoothScrollTo'

import DefaultLayout
    from '@/Layouts/DefaultLayout.vue'

import Navbar
    from '@/Partials/Default/Navbar.vue'

import FooterBlog
    from '@/Partials/Default/FooterBlog.vue'

import Progress
    from '@/Components/Public/Default/Progress/Progress.vue'

import LeftSidebarMarket
    from '@/Components/Public/Default/Partials/LeftSidebarMarket.vue'

import RightSidebarMarket
    from '@/Components/Public/Default/Partials/RightSidebarMarket.vue'

import EntityPageToolbar
    from '@/Components/Public/Default/PageToolbar/EntityPageToolbar.vue'

import FrontendEntityPageToolbar
    from '@/Components/Public/Default/PageToolbar/FrontendEntityPageToolbar.vue'

import MarketProductGrid
    from '@/Components/Public/Default/Market/MarketProduct/MarketProductGrid.vue'

import MarketProductRows
    from '@/Components/Public/Default/Market/MarketProduct/MarketProductRows.vue'

import Pagination
    from '@/Components/Public/Default/Pagination/Pagination.vue'

import FrontendPagination
    from '@/Components/Public/Default/Pagination/FrontendPagination.vue'

import PublicAdminBottomPanel
    from '@/Components/Admin/UI/PublicAdminPanel/PublicAdminBottomPanel.vue'
import ImageGalleryMain from '@/Components/Public/Default/Media/ImageGalleryMain.vue'

const { t } = useI18n()

/* ===================== PROPS ===================== */

/** Props страницы */
const props = defineProps({
    /** Активная локаль от backend */
    locale: {
        type: String,
        default: '',
    },

    /** Текущий Public-бренд */
    brand: {
        type: Object,
        default: () => ({}),
    },

    /** Связанные Public-товары */
    products: {
        type: [Array, Object],
        default: () => [],
    },

    /** Общее количество Public-товаров бренда */
    productsCount: {
        type: Number,
        default: 0,
    },

    /** Количество найденных товаров */
    productsFound: {
        type: Number,
        default: 0,
    },

    /** Режим обработки товаров */
    useServerProcessing: {
        type: Boolean,
        default: false,
    },

    /** Настройка server / frontend / auto */
    publicMarketProductsProcessingMode: {
        type: String,
        default: 'server',
    },

    /** Сортировка Public по умолчанию */
    defaultSort: {
        type: String,
        default: 'sortAsc',
    },

    /** Текущие параметры списка */
    filters: {
        type: Object,
        default: () => ({}),
    },

    /** Дерево категорий */
    categoryTree: {
        type: Array,
        default: () => [],
    },
})

/* ===================== PAGE ===================== */

/** Глобальные данные страницы */
const page = usePage()

/** Глобальные настройки сайта */
const siteSettings =
    page.props?.siteSettings || {}

/** Роль администратора */
const isAdmin = computed(() => {
    return page.props?.isAdmin === true
})

/** Дерево категорий */
const categoryTree = computed(() => {
    return Array.isArray(
        props.categoryTree
    )
        ? props.categoryTree
        : []
})

/* ===================== BRAND ===================== */

/** Текущий бренд */
const brand = computed(() => {
    return props.brand || {}
})

/**
 * Активный перевод бренда.
 *
 * Перевод уже разрешён Laravel:
 * current → configured fallback → null.
 */
const brandTranslation = computed(() => {
    return brand.value?.translation || {}
})

/** Название бренда */
const brandTitle = computed(() => {
    return brandTranslation.value?.title || ''
})

/** Подзаголовок бренда */
const brandSubtitle = computed(() => {
    return brandTranslation.value?.subtitle || ''
})

/** Краткое описание бренда */
const brandShort = computed(() => {
    return brandTranslation.value?.short || ''
})

/** Полное описание бренда */
const brandDescription = computed(() => {
    return brandTranslation.value?.description
        || brandShort.value
        || ''
})

/** Количество Public-товаров бренда */
const brandProductsCount = computed(() => {
    return Number(
        props.productsCount
        ?? brand.value?.products_count
        ?? 0
    )
})

/** Количество просмотров бренда */
const brandViews = computed(() => {
    return Number(
        brand.value?.views ?? 0
    )
})

/** Универсальный список изображений бренда */
const brandImages = computed(() => {
    const value = brand.value?.images

    if (Array.isArray(value)) {
        return value
    }

    if (Array.isArray(value?.data)) {
        return value.data
    }

    return []
})

/** Наличие изображений бренда */
const hasBrandImages = computed(() => {
    return brandImages.value.length > 0
})

/** Нормализация storage URL */
const normalizeStorageUrl = (value) => {
    if (!value) {
        return ''
    }

    const url = String(value).trim()

    if (!url) {
        return ''
    }

    if (
        url.startsWith('http://')
        || url.startsWith('https://')
        || url.startsWith('/storage/')
    ) {
        return url
    }

    return `/storage/${url}`
}

/** Логотип бренда */
const brandLogo = computed(() => {
    return normalizeStorageUrl(
        brand.value?.logo
    )
})

/** Основное изображение бренда */
const brandPrimaryImage = computed(() => {
    const image = brandImages.value[0]

    return normalizeStorageUrl(
        image?.webp_url
        || image?.image_url
        || image?.thumb_url
        || image?.url
        || ''
    )
})

/** Alt основного изображения */
const brandPrimaryImageAlt = computed(() => {
    const image = brandImages.value[0]

    return image?.alt
        || brandTitle.value
        || ''
})

/** Website бренда */
const brandWebsite = computed(() => {
    return String(
        brand.value?.website || ''
    ).trim()
})

/** Социальные ссылки бренда */
const brandSocialLinks = computed(() => {
    const value = brand.value?.social_links

    if (!value) {
        return []
    }

    if (Array.isArray(value)) {
        return value
            .map((item) => {
                if (typeof item === 'string') {
                    return {
                        name: '',
                        url: item,
                    }
                }

                return {
                    name:
                        item?.name
                        || item?.title
                        || item?.label
                        || '',
                    url:
                        item?.url
                        || item?.link
                        || '',
                }
            })
            .filter((item) => item.url)
    }

    if (typeof value === 'object') {
        return Object.entries(value)
            .map(([name, url]) => ({
                name,
                url:
                    typeof url === 'string'
                        ? url
                        : url?.url || url?.link || '',
            }))
            .filter((item) => item.url)
    }

    return []
})

/* ===================== PRODUCTS DATA ===================== */

/** Универсальный список товаров */
const productsData = computed(() => {
    if (Array.isArray(props.products)) {
        return props.products
    }

    if (
        Array.isArray(
            props.products?.data
        )
    ) {
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
const LEFT_SIDEBAR_KEY =
    'public_left_sidebar_collapsed'

/** Ключ правого сайдбара */
const RIGHT_SIDEBAR_KEY =
    'public_right_sidebar_collapsed'

/** Получение boolean из localStorage */
const getStoredBoolean = (
    key,
    defaultValue = true
) => {
    const value =
        localStorage.getItem(key)

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
const productGridCols = computed(() => {
    const leftExpanded =
        showLeft.value
        && !leftCollapsed.value

    const rightExpanded =
        showRight.value
        && !rightCollapsed.value

    if (
        leftExpanded
        && rightExpanded
    ) {
        return 2
    }

    if (
        leftExpanded
        || rightExpanded
    ) {
        return 3
    }

    return 4
})

/** Сохраняем состояние сайдбаров */
watch(
    [
        leftCollapsed,
        rightCollapsed
    ],
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
        props.filters?.q
        ?? ''
    )
)

/** Текущая сортировка */
const sort = ref(
    String(
        props.filters?.sort
        ?? props.defaultSort
    )
)

/** Ключ режима отображения */
const VIEW_KEY =
    'public_market_products_view'

/** Режим отображения */
const viewMode = ref(
    String(
        props.filters?.view
        || localStorage.getItem(VIEW_KEY)
        || 'grid'
    )
)

/** Сохраняем режим отображения */
watch(
    viewMode,
    (value) => {
        localStorage.setItem(
            VIEW_KEY,
            value
        )
    }
)

/**
 * Количество товаров на странице.
 *
 * Источник:
 * PublicSettingsService
 * → resolvePerPage()
 * → filters.per_page.
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

/** Опции сортировки товаров */
const productSortOptions = [
    {
        value: 'idDesc',
        label: t('idDesc')
    },
    {
        value: 'idAsc',
        label: t('idAsc')
    },

    {
        value: 'sortAsc',
        label: `${t('sortNumber')} 0→9`
    },
    {
        value: 'sortDesc',
        label: `${t('sortNumber')} 9→0`
    },

    {
        value: 'titleAsc',
        label: `${t('title')} A→Z`
    },
    {
        value: 'titleDesc',
        label: `${t('title')} Z→A`
    },

    {
        value: 'priceAsc',
        label: `${t('price')} 0→9`
    },
    {
        value: 'priceDesc',
        label: `${t('price')} 9→0`
    },

    {
        value: 'quantityAsc',
        label: `${t('quantity')} 0→9`
    },
    {
        value: 'quantityDesc',
        label: `${t('quantity')} 9→0`
    },

    {
        value: 'viewsDesc',
        label: `${t('views')} 9→0`
    },
    {
        value: 'viewsAsc',
        label: `${t('views')} 0→9`
    },

    {
        value: 'likesDesc',
        label: `${t('likes')} 9→0`
    },
    {
        value: 'likesAsc',
        label: `${t('likes')} 0→9`
    },

    {
        value: 'ratingDesc',
        label: `${t('rating')} 9→0`
    },
    {
        value: 'ratingAsc',
        label: `${t('rating')} 0→9`
    },

    {
        value: 'ratingCountDesc',
        label: `${t('ratingCount')} 9→0`
    },
    {
        value: 'ratingCountAsc',
        label: `${t('ratingCount')} 0→9`
    },

    {
        value: 'reviewsDesc',
        label: `${t('reviews')} 9→0`
    },
    {
        value: 'reviewsAsc',
        label: `${t('reviews')} 0→9`
    },

    {
        value: 'brandAsc',
        label: `${t('brand')} A→Z`
    },
    {
        value: 'brandDesc',
        label: `${t('brand')} Z→A`
    },

    {
        value: 'publishedAtDesc',
        label: `${t('publishedAt')} ↓`
    },
    {
        value: 'publishedAtAsc',
        label: `${t('publishedAt')} ↑`
    },

    {
        value: 'createdAtDesc',
        label: `${t('createdAt')} ↓`
    },
    {
        value: 'createdAtAsc',
        label: `${t('createdAt')} ↑`
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

/** Нормализация даты */
const normalizeDate = (value) => {
    if (!value) {
        return 0
    }

    const timestamp =
        new Date(value).getTime()

    return Number.isFinite(timestamp)
        ? timestamp
        : 0
}

/** Название товара */
const getProductTitle = (product) => {
    return product?.translation?.title || ''
}

/** Название бренда товара */
const getProductBrandTitle = (product) => {
    return product?.brand
        ?.translation
        ?.title || ''
}

/**
 * Слова frontend-поиска.
 *
 * - минимальная длина слова 2;
 * - AND между словами;
 * - OR между Public-полями товара.
 */
const searchWords = computed(() => {
    return normalizeText(q.value)
        .split(/\s+/u)
        .filter(
            (word) => word.length >= 2
        )
})

/** Поля frontend-поиска товара */
const getProductSearchValues = (product) => {
    return [
        product?.url,
        product?.sku,
        product?.vendor_code,
        product?.barcode,
        product?.translation?.title,
        product?.translation?.subtitle,
        product?.translation?.short,
        product?.brand
            ?.translation
            ?.title,
    ].map(normalizeText)
}

/** Локальный поиск товаров */
const filteredProducts = computed(() => {
    const words = searchWords.value

    if (!words.length) {
        return productsData.value
    }

    return productsData.value.filter(
        (product) => {
            const values =
                getProductSearchValues(
                    product
                )

            return words.every(
                (word) =>
                    values.some(
                        (value) =>
                            value.includes(word)
                    )
            )
        }
    )
})

/**
 * Дополнительная сортировка
 * по ID DESC.
 *
 * Повторяет tie-break backend.
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
    const first =
        normalizeNumber(a?.[field])

    const second =
        normalizeNumber(b?.[field])

    const result =
        direction === 'desc'
            ? second - first
            : first - second

    return result
        || compareIdDesc(a, b)
}

/** Сравнение текста */
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

/** Сравнение даты */
const compareDate = (
    a,
    b,
    field,
    direction = 'asc'
) => {
    const first =
        normalizeDate(a?.[field])

    const second =
        normalizeDate(b?.[field])

    const result =
        direction === 'desc'
            ? second - first
            : first - second

    return result
        || compareIdDesc(a, b)
}

/**
 * Локальная Public-сортировка.
 *
 * Набор параметров совпадает
 * с MarketProduct::publicSortByParam().
 */
const sortedProducts = computed(() => {
    const list = [
        ...filteredProducts.value
    ]

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
                const result =
                    compareText(
                        getProductTitle(a),
                        getProductTitle(b),
                        'asc'
                    )

                return result
                    || compareIdDesc(a, b)
            }

            case 'titleDesc': {
                const result =
                    compareText(
                        getProductTitle(a),
                        getProductTitle(b),
                        'desc'
                    )

                return result
                    || compareIdDesc(a, b)
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
                const result =
                    compareText(
                        getProductBrandTitle(a),
                        getProductBrandTitle(b),
                        'asc'
                    )

                return result
                    || compareIdDesc(a, b)
            }

            case 'brandDesc': {
                const result =
                    compareText(
                        getProductBrandTitle(a),
                        getProductBrandTitle(b),
                        'desc'
                    )

                return result
                    || compareIdDesc(a, b)
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
 */
const frontendPaginatedProducts =
    computed(() => {
        const start = (
            frontendCurrentPage.value - 1
        ) * perPage.value

        return sortedProducts.value.slice(
            start,
            start + perPage.value
        )
    })

/** Сбрасываем frontend-пагинацию */
watch(
    [
        q,
        sort,
        viewMode
    ],
    () => {
        frontendCurrentPage.value = 1
    }
)

/** Скролл при frontend-пагинации */
watch(
    frontendCurrentPage,
    () => {
        if (!props.useServerProcessing) {
            scrollToTarget()
        }
    }
)

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

/**
 * Активная локаль приходит
 * исключительно от backend.
 */
const contentLocale = computed(() => {
    return String(
        props.locale || ''
    )
})

/** SEO title */
const seoTitle = computed(() => {
    return brandTranslation.value?.meta_title
        || brandTitle.value
})

/** SEO keywords */
const seoKeywords = computed(() => {
    return brandTranslation.value?.meta_keywords
        || ''
})

/** SEO description */
const seoDescription = computed(() => {
    return brandTranslation.value?.meta_desc
        || brandShort.value
        || brandDescription.value
        || ''
})

/** Open Graph locale */
const ogLocale = computed(() => {
    return contentLocale.value
})

/** Dublin Core subject */
const dcSubject = computed(() => {
    return seoKeywords.value
        || brandTitle.value
})

/**
 * Social preview бренда.
 *
 * Приоритет:
 * - основное изображение;
 * - логотип.
 */
const seoImage = computed(() => {
    return brandPrimaryImage.value
        || brandLogo.value
        || ''
})

/** Alt social preview */
const seoImageAlt = computed(() => {
    return brandPrimaryImageAlt.value
        || brandTitle.value
        || ''
})

/**
 * Канонический URL страницы бренда.
 *
 * Server pagination:
 *
 * /catalog/brands/{url}
 * /catalog/brands/{url}?page=2
 *
 * Сортировка и view
 * в canonical не включаются.
 */
const canonicalUrl = computed(() => {
    const baseUrl = String(
        route(
            'public.marketBrands.show',
            {
                url: brand.value?.url
            }
        )
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
 * Альтернативную server-сортировку
 * не индексируем.
 *
 * Основная страница бренда и обычные
 * страницы пагинации индексируются.
 */
const robotsContent = computed(() => {
    const hasSearch =
        normalizeText(q.value).length > 0

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

/** Маршрут текущего бренда */
const showRoute = () => {
    return route(
        'public.marketBrands.show',
        {
            url: brand.value?.url
        }
    )
}

/**
 * Server-загрузка товаров бренда.
 *
 * per_page намеренно не отправляем:
 * значение определяет backend.
 */
const reloadProducts = (
    pageNumber = 1
) => {
    router.get(
        showRoute(),
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

            page:
            pageNumber,
        },
        {
            preserveState: true,
            replace: true,
            preserveScroll: true,
        }
    )
}

/** Поиск товаров */
const submitSearch = () => {
    frontendCurrentPage.value = 1

    if (props.useServerProcessing) {
        reloadProducts(1)
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
        reloadProducts(1)
    }
}

/** Изменение сортировки */
const updateSort = (value) => {
    sort.value =
        value
        || props.defaultSort

    frontendCurrentPage.value = 1

    if (props.useServerProcessing) {
        reloadProducts(1)
    }
}

/** Изменение режима отображения */
const updateViewMode = (value) => {
    viewMode.value =
        value
        || 'grid'

    frontendCurrentPage.value = 1

    if (props.useServerProcessing) {
        reloadProducts(1)
    }
}

/** Server-переход на страницу */
const goToPage = (pageNumber) => {
    const value = Number(
        pageNumber
    )

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

    reloadProducts(
        safePage
    )
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

/** Итоговый список товаров */
const displayedProducts = computed(() => {
    return props.useServerProcessing
        ? productsData.value
        : frontendPaginatedProducts.value
})

/**
 * Начальная позиция товара
 * для Schema.org ItemList.
 */
const productListStartPosition =
    computed(() => {
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
            content="Text"
        />

        <meta
            name="DC.format"
            content="text/html"
        />
    </Head>

    <DefaultLayout>
        <!-- Шапка -->
        <Navbar />

        <main
            class="min-h-screen px-1 lg:px-6 max-w-full"
        >
            <div
                class="mx-auto tracking-wider pt-20 lg:pt-44"
            >
                <div
                    class="ext-color w-full min-w-0 py-3 px-1
                           flex flex-col lg:flex-row gap-4
                           rounded-3xl border-2 border-slate-300 dark:border-slate-500"
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
                    <div
                        class="min-w-0 flex-1 pb-6 slate-1"
                    >
                        <div class="w-full">
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

                                    <!-- Бренды -->
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
                                            :href="route('public.marketBrands.index')"
                                            class="breadcrumb-link hover:underline"
                                        >
                                            <span itemprop="name">
                                                {{ t('brands') }}
                                            </span>
                                        </Link>

                                        <meta
                                            itemprop="position"
                                            content="2"
                                        />
                                    </li>

                                    <!-- Текущий бренд -->
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
                                            {{ brandTitle }}
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

                            <!-- Бренд -->
                            <article
                                class="mt-4"
                                itemscope
                                itemtype="https://schema.org/Brand"
                                :itemid="canonicalUrl"
                            >
                                <meta
                                    itemprop="url"
                                    :content="canonicalUrl"
                                />

                                <meta
                                    itemprop="name"
                                    :content="brandTitle"
                                />

                                <!-- Основная информация -->
                                <section
                                    class="overflow-hidden rounded-2xl
                                           border border-slate-200
                                           bg-white dark:border-slate-700 dark:bg-slate-900"
                                >
                                    <!-- Большое изображение -->
                                    <div
                                        v-if="brandPrimaryImage"
                                        class="relative"
                                    >
                                        <!-- Brand images -->
                                        <div
                                            v-if="hasBrandImages"
                                            class="flex items-center justify-center"
                                        >
                                            <div class="w-full">
                                                <ImageGalleryMain
                                                    :images="brandImages"
                                                    :alt="brandTitle"
                                                    rounded-class="rounded-lg"
                                                    shadow-class="shadow-lg shadow-gray-400
                                                                  dark:shadow-gray-700"
                                                    img-class="w-full h-full object-cover"
                                                    itemprop="image"
                                                    loading="eager"
                                                    fetchpriority="high"
                                                />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="px-4 pb-5 sm:px-6"
                                    >
                                        <!-- Логотип -->
                                        <div
                                            v-if="brandLogo"
                                            class="relative z-10 flex justify-center"
                                            :class="brandPrimaryImage ? 'mt-1' : 'pt-5'"
                                        >
                                            <div
                                                class="flex h-24 w-auto
                                                       items-center justify-center
                                                       rounded-xl border border-slate-200
                                                       bg-white p-3 shadow-sm
                                                       dark:border-slate-700 dark:bg-slate-800"
                                            >
                                                <img
                                                    itemprop="logo"
                                                    :src="brandLogo"
                                                    :alt="brandTitle"
                                                    class="max-h-full max-w-full object-contain"
                                                />
                                            </div>
                                        </div>

                                        <!-- Название -->
                                        <div class="mt-4 text-center">
                                            <h2
                                                class="text-xl font-semibold
                                                       text-slate-900 dark:text-slate-100"
                                            >
                                                {{ brandTitle }}
                                            </h2>

                                            <div
                                                v-if="brandSubtitle"
                                                class="mt-1 text-sm subtitle"
                                            >
                                                {{ brandSubtitle }}
                                            </div>
                                        </div>

                                        <!-- Статистика -->
                                        <div
                                            class="mt-3 flex flex-wrap items-center
                                                   justify-center gap-x-5 gap-y-2"
                                        >
                                            <!-- Товары -->
                                            <div
                                                :title="t('products')"
                                                class="flex items-center gap-1"
                                            >
                                                <svg
                                                    class="h-4 w-4 text-sky-600/85
                                                           dark:text-sky-200/85"
                                                    viewBox="0 0 24 24"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        d="M21 8.5 12 3 3 8.5V19l9 5 9-5V8.5ZM12 5.3l5.8 3.5-2.2 1.3L10 6.8 12 5.3Zm-3.8 2.6 5.8 3.5-2 1.2-5.8-3.5 2-1.2ZM5 10.6l6 3.6v7L5 17.8v-7.2Zm8 10.6v-7l6-3.6v7.2l-6 3.4Z"
                                                    />
                                                </svg>

                                                <span class="text-sm text-gray-500">
                                                    {{ brandProductsCount }}
                                                </span>
                                            </div>

                                            <!-- Просмотры -->
                                            <div
                                                :title="t('views')"
                                                class="flex items-center gap-1"
                                            >
                                                <svg
                                                    class="h-4 w-4
                                                           text-slate-600/85
                                                           dark:text-slate-200/85"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 576 512"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        d="M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z"
                                                    />
                                                </svg>

                                                <span class="text-sm text-gray-500">
                                                    {{ brandViews }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Краткое описание -->
                                        <div
                                            v-if="brandShort"
                                            class="mx-auto mt-4 max-w-4xl
                                                   text-center text-sm subtitle"
                                        >
                                            {{ brandShort }}
                                        </div>

                                        <!-- Website -->
                                        <div
                                            v-if="brandWebsite"
                                            class="mt-4 text-center"
                                        >
                                            <a
                                                :href="brandWebsite"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="text-sm text-sky-600
                                                       hover:underline dark:text-sky-300"
                                            >
                                                {{ brandWebsite }}
                                            </a>
                                        </div>

                                        <!-- Социальные ссылки -->
                                        <div
                                            v-if="brandSocialLinks.length"
                                            class="mt-3 flex flex-wrap
                                                   justify-center gap-3"
                                        >
                                            <a
                                                v-for="(social, index) in brandSocialLinks"
                                                :key="`${social.url}-${index}`"
                                                :href="social.url"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="text-sm text-sky-600
                                                       hover:underline dark:text-sky-300"
                                            >
                                                {{ social.name || social.url }}
                                            </a>
                                        </div>
                                    </div>
                                </section>

                                <!-- Полное описание -->
                                <section
                                    v-if="brandDescription"
                                    class="mt-5 rounded-2xl
                                           border border-slate-200
                                           bg-white p-4 dark:border-slate-700 dark:bg-slate-900"
                                >
                                    <div
                                        itemprop="description"
                                        class="text-sm leading-7
                                               text-slate-700 dark:text-slate-300
                                               whitespace-pre-line"
                                    >
                                        {{ brandDescription }}
                                    </div>
                                </section>
                            </article>

                            <!-- Каталог товаров бренда -->
                            <section
                                class="mt-6"
                                aria-labelledby="brand-products-title"
                            >
                                <h2
                                    id="brand-products-title"
                                    class="mb-3 text-lg font-semibold
                                           text-slate-900 dark:text-slate-100"
                                >
                                    {{ t('products') }}
                                    —
                                    {{ brandTitle }}
                                </h2>

                                <!-- Server toolbar -->
                                <EntityPageToolbar
                                    v-if="useServerProcessing"
                                    v-model="q"
                                    :found="productsFound"
                                    :view-mode="viewMode"
                                    :sort-value="sort"
                                    :sort-options="productSortOptions"
                                    :default-sort="defaultSort"
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
                                    :default-sort="defaultSort"
                                    :found-label="t('products')"
                                    :search-placeholder="t('searchByName')"
                                    @reset="resetSearch"
                                    @update:viewMode="updateViewMode"
                                    @update:sortValue="updateSort"
                                />

                                <!-- Точка скролла -->
                                <div ref="scrollTarget"></div>

                                <!-- Нет товаров -->
                                <div
                                    v-if="displayedProducts.length === 0"
                                    class="mt-6 text-center text-slate-700 dark:text-slate-300"
                                >
                                    {{ t('noData') }}
                                </div>

                                <!--
                                    MarketProductGrid / Rows
                                    формируют:

                                    ItemList
                                    → ListItem
                                    → Product
                                    → Brand / Offer /
                                      AggregateRating.
                                -->
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
                            </section>
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
            setting-key="publicMarketProductsProcessingMode"
            :mode="publicMarketProductsProcessingMode"
            :use-server-processing="useServerProcessing"
            :total="productsCount"
        />
    </DefaultLayout>
</template>
