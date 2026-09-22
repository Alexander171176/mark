<script setup>
/**
 * Публичная страница категории товаров маркетплейса.
 *
 * - Public Resource категории
 * - связанные Public-товары
 * - server / frontend / auto
 * - поиск и сортировка товаров
 * - grid / rows
 * - server / frontend pagination
 * - SEO / Open Graph / Twitter / Dublin Core
 * - Schema.org BreadcrumbList / CollectionPage / ItemList / Product
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
import FrontendEntityPageToolbar
    from '@/Components/Public/Default/PageToolbar/FrontendEntityPageToolbar.vue'
import MarketProductGrid
    from '@/Components/Public/Default/Market/MarketProduct/MarketProductGrid.vue'
import MarketProductRows
    from '@/Components/Public/Default/Market/MarketProduct/MarketProductRows.vue'
import MarketCategoryGrid
    from '@/Components/Public/Default/Market/MarketCategory/MarketCategoryGrid.vue'
import MarketCategoryRows
    from '@/Components/Public/Default/Market/MarketCategory/MarketCategoryRows.vue'
import Pagination from '@/Components/Public/Default/Pagination/Pagination.vue'
import FrontendPagination from '@/Components/Public/Default/Pagination/FrontendPagination.vue'
import PublicAdminBottomPanel
    from '@/Components/Admin/UI/PublicAdminPanel/PublicAdminBottomPanel.vue'
import ImageGalleryMain from '@/Components/Public/Default/Media/ImageGalleryMain.vue'
import ViewModeToggle from '@/Components/Public/Default/Buttons/ViewModeToggle.vue'

const { t } = useI18n()

/* ===================== PROPS ===================== */

/** Props страницы */
const props = defineProps({
    /** Активная локаль от backend */
    locale: { type: String, default: '' },

    /** Текущая Public-категория */
    category: { type: Object, default: () => ({}) },

    /** Связанные Public-товары */
    products: {
        type: [Array, Object],
        default: () => [],
    },

    /** Общее количество Public-товаров категории */
    productsCount: { type: Number, default: 0 },

    /** Количество найденных товаров */
    productsFound: { type: Number, default: 0 },

    /** Режим обработки товаров */
    useServerProcessing: { type: Boolean, default: false },

    /** Настройка server / frontend / auto */
    publicMarketProductsProcessingMode: { type: String, default: 'server' },

    /** Сортировка Public по умолчанию */
    defaultSort: { type: String, default: 'sortAsc' },

    /** Текущие параметры списка */
    filters: { type: Object, default: () => ({}) },

    /** Дерево категорий */
    categoryTree: { type: Array, default: () => [] },
})

/* ===================== PAGE ===================== */

/** Глобальные данные страницы */
const page = usePage()

/** Глобальные настройки сайта */
const siteSettings = page.props?.siteSettings || {}

/** Роль администратора */
const isAdmin = computed(() => page.props?.isAdmin === true)

/** Дерево категорий */
const categoryTree = computed(() => Array.isArray(props.categoryTree) ? props.categoryTree : [])

/* ===================== CATEGORY ===================== */

/** Текущая категория */
const category = computed(() => props.category || {})

/**
 * Активный перевод категории.
 *
 * Перевод уже разрешён Laravel:
 * current → configured fallback → null.
 */
const categoryTranslation = computed(() => category.value?.translation || {})

/** Название категории */
const categoryTitle = computed(() => categoryTranslation.value?.title || '')

/** Подзаголовок категории */
const categorySubtitle = computed(() => categoryTranslation.value?.subtitle || '')

/** Краткое описание категории */
const categoryShort = computed(() => categoryTranslation.value?.short || '')

/** Полное описание категории */
const categoryDescription = computed(() => {
    return categoryTranslation.value?.description
        || categoryShort.value
        || ''
})

/** Количество Public-товаров категории */
const categoryProductsCount = computed(() => {
    return Number(
        props.productsCount
        ?? category.value?.products_count
        ?? 0
    )
})

/** Количество просмотров категории */
const categoryViews = computed(() => Number(category.value?.views ?? 0))

/** Дочерние Public-категории */
const childCategories = computed(() => {
    const value = category.value?.children

    if (Array.isArray(value)) {
        return value
    }

    if (Array.isArray(value?.data)) {
        return value.data
    }

    return []
})

/** Количество дочерних Public-категорий */
const childrenCount = computed(() => {
    return Number(
        category.value?.children_count
        ?? childCategories.value.length
        ?? 0
    )
})

/** Родительская Public-категория */
const parentCategory = computed(() => category.value?.parent || null)

/** Название родительской категории */
const parentCategoryTitle = computed(() => parentCategory.value?.translation?.title || '')

/** Универсальный список изображений категории */
const categoryImages = computed(() => {
    const value = category.value?.images

    if (Array.isArray(value)) {
        return value
    }

    if (Array.isArray(value?.data)) {
        return value.data
    }

    return []
})

/** Наличие изображений категории */
const hasCategoryImages = computed(() => categoryImages.value.length > 0)

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

/** Основное изображение категории */
const categoryPrimaryImage = computed(() => {
    const image = categoryImages.value[0]

    return normalizeStorageUrl(
        image?.webp_url
        || image?.image_url
        || image?.thumb_url
        || image?.url
        || ''
    )
})

/** Alt основного изображения */
const categoryPrimaryImageAlt = computed(() => {
    const image = categoryImages.value[0]

    return image?.alt
        || categoryTitle.value
        || ''
})

/** Проверка SVG-иконки */
const hasSvgIcon = computed(() => {
    if (!category.value?.icon) {
        return false
    }

    return /^\s*<svg[\s\S]*<\/svg>\s*$/i.test(
        String(category.value.icon)
    )
})

/** Ключ режима отображения дочерних категорий */
const CATEGORY_VIEW_KEY = 'public_market_category_children_view'

/** Режим отображения дочерних категорий */
const categoryViewMode = ref(localStorage.getItem(CATEGORY_VIEW_KEY) || 'grid')

/** Сохраняем режим отображения дочерних категорий */
watch(categoryViewMode, (value) => localStorage.setItem(CATEGORY_VIEW_KEY, value))

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
const showLeft = computed(() => !siteSettings?.ViewLeftColumn
    || siteSettings.ViewLeftColumn === 'true')

/** Показ правой колонки */
const showRight = computed(() => !siteSettings?.ViewRightColumn
    || siteSettings.ViewRightColumn === 'true')

/** Ключ левого сайдбара */
const LEFT_SIDEBAR_KEY = 'public_left_sidebar_collapsed'

/** Ключ правого сайдбара */
const RIGHT_SIDEBAR_KEY = 'public_right_sidebar_collapsed'

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

/** Количество колонок дочерних категорий */
const categoryGridCols = computed(() => {
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
watch(viewMode, (value) => localStorage.setItem(VIEW_KEY, value))

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
        : 12
})

/** Опции сортировки товаров */
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

    { value: 'categoryAsc', label: `${t('category')} A→Z` },
    { value: 'categoryDesc', label: `${t('category')} Z→A` },

    { value: 'publishedAtDesc', label: `${t('publishedAt')} ↓` },
    { value: 'publishedAtAsc', label: `${t('publishedAt')} ↑` },

    { value: 'createdAtDesc', label: `${t('createdAt')} ↓` },
    { value: 'createdAtAsc', label: `${t('createdAt')} ↑` },
]

/* ===================== FRONTEND MODE ===================== */

/** Текущая frontend-страница */
const frontendCurrentPage = ref(1)

/** Плавный скролл к списку */
const { targetRef: scrollTarget, scrollToTarget } = useSmoothScrollTo(
    { offset: 80, duration: 1200 }
)

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
const getProductTitle = (product) => product?.translation?.title || ''

/** Название категории товара */
const getProductCategoryTitle = (product) => product?.category?.translation?.title || ''

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
        product?.category
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

            case 'categoryAsc': {
                const result =
                    compareText(
                        getProductCategoryTitle(a),
                        getProductCategoryTitle(b),
                        'asc'
                    )

                return result
                    || compareIdDesc(a, b)
            }

            case 'categoryDesc': {
                const result =
                    compareText(
                        getProductCategoryTitle(a),
                        getProductCategoryTitle(b),
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
const contentLocale = computed(() => String(props.locale || ''))

/** SEO title */
const seoTitle = computed(() => {
    return categoryTranslation.value?.meta_title
        || categoryTitle.value
})

/** SEO keywords */
const seoKeywords = computed(() => {
    return categoryTranslation.value?.meta_keywords
        || ''
})

/** SEO description */
const seoDescription = computed(() => {
    return categoryTranslation.value?.meta_description
        || categoryShort.value
        || categoryDescription.value
        || ''
})

/** Open Graph locale */
const ogLocale = computed(() => contentLocale.value)

/** Dublin Core subject */
const dcSubject = computed(() => {
    return seoKeywords.value
        || categoryTitle.value
})

/**
 * Social preview категории.
 */
const seoImage = computed(() => {
    return categoryPrimaryImage.value
        || ''
})

/** Alt social preview */
const seoImageAlt = computed(() => {
    return categoryPrimaryImageAlt.value
        || categoryTitle.value
        || ''
})

/**
 * Канонический URL страницы категории.
 *
 * Server pagination:
 *
 * /catalog/categorys/{url}
 * /catalog/categorys/{url}?page=2
 *
 * Сортировка и view
 * в canonical не включаются.
 */
const canonicalUrl = computed(() => {
    const baseUrl = String(
        route(
            'public.marketCategories.show',
            {
                url: category.value?.url
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
 * Основная страница категории и обычные
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

/** Маршрут текущего категории */
const showRoute = () => {
    return route(
        'public.marketCategories.show',
        {
            url: category.value?.url
        }
    )
}

/**
 * Server-загрузка товаров категории.
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

/** Полное количество товаров текущего ItemList */
const productListTotalItems = computed(() => {
    return props.useServerProcessing
        ? Number(props.productsFound ?? 0)
        : sortedProducts.value.length
})

/**
 * Начальная позиция товара
 * для Schema.org ItemList.
 */
const productListStartPosition = computed(() => {
    const pageNumber = props.useServerProcessing
        ? currentPage.value
        : frontendCurrentPage.value

    return Math.max(0, (pageNumber - 1) * perPage.value)
})
</script>

<template>
    <!-- SEO -->
    <Head>
        <!-- Basic SEO -->
        <title>{{ seoTitle }}</title>

        <meta name="title" :content="seoTitle" />
        <meta v-if="seoDescription" name="description" :content="seoDescription" />
        <meta v-if="seoKeywords" name="keywords" :content="seoKeywords" />
        <meta v-if="contentLocale" http-equiv="content-language" :content="contentLocale" />
        <meta name="robots" :content="robotsContent" />

        <!-- Canonical -->
        <link rel="canonical" :href="canonicalUrl" />

        <!-- Open Graph -->
        <meta property="og:type" content="website" />
        <meta property="og:title" :content="seoTitle" />
        <meta v-if="seoDescription" property="og:description" :content="seoDescription" />
        <meta property="og:url" :content="canonicalUrl" />
        <meta v-if="ogLocale" property="og:locale" :content="ogLocale" />
        <meta v-if="seoImage" property="og:image" :content="seoImage" />
        <meta v-if="seoImage && seoImageAlt" property="og:image:alt" :content="seoImageAlt" />

        <!-- Twitter / X -->
        <meta name="twitter:card" :content="seoImage ? 'summary_large_image' : 'summary'" />
        <meta name="twitter:title" :content="seoTitle" />
        <meta v-if="seoDescription" name="twitter:description" :content="seoDescription" />
        <meta v-if="seoImage" name="twitter:image" :content="seoImage" />
        <meta v-if="seoImage && seoImageAlt" name="twitter:image:alt" :content="seoImageAlt" />

        <!-- Dublin Core -->
        <meta name="DC.title" :content="seoTitle" />
        <meta v-if="seoDescription" name="DC.description" :content="seoDescription" />
        <meta v-if="dcSubject" name="DC.subject" :content="dcSubject" />
        <meta v-if="contentLocale" name="DC.language" :content="contentLocale" />
        <meta name="DC.identifier" :content="canonicalUrl" />
        <meta name="DC.type" content="Collection" />
        <meta name="DC.format" content="text/html" />
    </Head>

    <DefaultLayout>
        <!-- Шапка -->
        <Navbar />

        <main class="min-h-screen px-1 lg:px-6 max-w-full">
            <div class="mx-auto tracking-wider pt-20 lg:pt-44">
                <div class="ext-color w-full min-w-0 py-3 px-1 flex flex-col lg:flex-row gap-4
                            rounded-3xl border-2 border-slate-300 dark:border-slate-500">

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
                                            <span itemprop="name">{{ t('home') }}</span>
                                        </Link>

                                        <meta itemprop="position" content="1" />
                                    </li>

                                    <!-- Категории -->
                                    <li
                                        itemprop="itemListElement"
                                        itemscope
                                        itemtype="https://schema.org/ListItem"
                                        class="flex items-center"
                                    >
                                        <span class="mx-2 breadcrumbs">/</span>

                                        <Link
                                            itemprop="item"
                                            :href="route('public.marketCategories.index')"
                                            class="breadcrumb-link hover:underline"
                                        >
                                            <span itemprop="name">{{ t('categories') }}</span>
                                        </Link>

                                        <meta itemprop="position" content="2" />
                                    </li>

                                    <!-- Родительская категория -->
                                    <li
                                        v-if="parentCategory"
                                        itemprop="itemListElement"
                                        itemscope
                                        itemtype="https://schema.org/ListItem"
                                        class="flex items-center"
                                    >
                                        <span class="mx-2 breadcrumbs">/</span>

                                        <Link
                                            itemprop="item"
                                            :href="route('public.marketCategories.show',
                                            { url: parentCategory.url })"
                                            class="breadcrumb-link hover:underline"
                                        >
                                            <span itemprop="name">{{ parentCategoryTitle }}</span>
                                        </Link>

                                        <meta itemprop="position" content="3" />
                                    </li>

                                    <!-- Текущая категория -->
                                    <li
                                        itemprop="itemListElement"
                                        itemscope
                                        itemtype="https://schema.org/ListItem"
                                        class="flex items-center"
                                        aria-current="page"
                                    >
                                        <span class="mx-2 breadcrumbs">/</span>

                                        <h1 itemprop="name"
                                            class="breadcrumbs text-sm font-semibold">
                                            {{ categoryTitle }}
                                        </h1>

                                        <meta itemprop="item" :content="canonicalUrl" />
                                        <meta
                                            itemprop="position"
                                            :content="parentCategory ? 4 : 3" />
                                    </li>
                                </ol>
                            </nav>

                            <!--
                                CollectionPage текущей категории.

                                Внутри находятся:
                                - основная информация;
                                - описание;
                                - дочерние категории;
                                - mainEntity → ItemList товаров.
                            -->
                            <article
                                class="mt-4"
                                itemscope
                                itemtype="https://schema.org/CollectionPage"
                                :itemid="canonicalUrl"
                            >
                                <meta itemprop="url" :content="canonicalUrl" />
                                <meta itemprop="name" :content="categoryTitle" />
                                <meta
                                    v-if="contentLocale"
                                    itemprop="inLanguage"
                                    :content="contentLocale" />

                                <!-- Основная информация -->
                                <section
                                    class="overflow-hidden rounded-2xl border border-slate-200
                                           bg-white dark:border-slate-700 dark:bg-slate-900">
                                    <!-- Галерея категории -->
                                    <div v-if="hasCategoryImages"
                                         class="flex items-center justify-center">
                                        <div class="w-full">
                                            <ImageGalleryMain
                                                :images="categoryImages"
                                                :alt="categoryTitle"
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

                                    <div class="px-4 py-5 sm:px-6">
                                        <!-- Название -->
                                        <div class="flex flex-wrap
                                                    items-center justify-center gap-3 text-center">
                                            <span
                                                v-if="hasSvgIcon"
                                                class="flex shrink-0"
                                                v-html="category.icon"
                                            />

                                            <h2 class="text-xl font-semibold
                                                       text-slate-900 dark:text-slate-100">
                                                {{ categoryTitle }}
                                            </h2>
                                        </div>

                                        <!-- Подзаголовок -->
                                        <div
                                            v-if="categorySubtitle"
                                            class="mt-1 text-center text-sm subtitle"
                                        >
                                            {{ categorySubtitle }}
                                        </div>

                                        <!-- Статистика -->
                                        <div class="mt-3 flex flex-wrap
                                                    items-center justify-center gap-x-5 gap-y-2
                                                    text-sm text-slate-500 dark:text-slate-400">
                                            <span>
                                                {{ t('products') }}:
                                                {{ categoryProductsCount }}
                                            </span>

                                            <span>
                                                {{ t('subheadings') }}:
                                                {{ childrenCount }}
                                            </span>

                                            <span>
                                                {{ t('views') }}:
                                                {{ categoryViews }}
                                            </span>
                                        </div>

                                        <!-- Краткое описание -->
                                        <div
                                            v-if="categoryShort"
                                            class="mt-4 text-center text-sm leading-6
                                                   text-slate-600 dark:text-slate-300"
                                        >
                                            {{ categoryShort }}
                                        </div>
                                    </div>
                                </section>

                                <!-- Полное описание -->
                                <section
                                    v-if="categoryDescription"
                                    class="mt-5 rounded-sm border border-slate-200 bg-white p-4
                                           dark:border-slate-700 dark:bg-slate-900"
                                >
                                    <div
                                        itemprop="description"
                                        class="text-sm leading-7
                                               text-slate-700 dark:text-slate-300
                                               whitespace-pre-line"
                                    >
                                        {{ categoryDescription }}
                                    </div>
                                </section>

                                <!-- Дочерние категории -->
                                <section
                                    v-if="childCategories.length"
                                    class="mt-6"
                                    aria-labelledby="child-categories-title"
                                >
                                    <div class="flex flex-wrap items-center justify-between gap-3">
                                        <h2
                                            id="child-categories-title"
                                            class="text-lg font-semibold
                                                   text-slate-900 dark:text-slate-100"
                                        >
                                            {{ t('subheadings') }}
                                        </h2>

                                        <ViewModeToggle
                                            v-model="categoryViewMode"
                                            grid-value="grid"
                                            row-value="rows"
                                        />
                                    </div>

                                    <div class="mt-4">
                                        <MarketCategoryGrid
                                            v-if="categoryViewMode === 'grid'"
                                            :categories="childCategories"
                                            :cols="categoryGridCols"
                                        />

                                        <MarketCategoryRows
                                            v-else
                                            :categories="childCategories"
                                        />
                                    </div>
                                </section>

                                <!-- Каталог товаров категории -->
                                <section
                                    class="mt-6"
                                    aria-labelledby="category-products-title"
                                >
                                    <h2
                                        id="category-products-title"
                                        class="mb-3 text-lg font-semibold
                                               text-slate-900 dark:text-slate-100"
                                    >
                                        {{ t('products') }} — {{ categoryTitle }}
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
                                        MarketProductGrid / Rows формируют:

                                        CollectionPage
                                        → mainEntity
                                        → ItemList
                                        → ListItem
                                        → Product
                                        → Brand / Category / Offer / AggregateRating.
                                    -->
                                    <div v-else>
                                        <MarketProductGrid
                                            v-if="viewMode === 'grid'"
                                            :products="displayedProducts"
                                            :cols="productGridCols"
                                            :start-position="productListStartPosition"
                                            :total-items="productListTotalItems"
                                            schema-property="mainEntity"
                                        />

                                        <MarketProductRows
                                            v-else
                                            :products="displayedProducts"
                                            :start-position="productListStartPosition"
                                            :total-items="productListTotalItems"
                                            schema-property="mainEntity"
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
            setting-key="publicMarketProductsProcessingMode"
            :mode="publicMarketProductsProcessingMode"
            :use-server-processing="useServerProcessing"
            :total="productsCount"
        />
    </DefaultLayout>
</template>
