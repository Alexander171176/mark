<script setup>
/**
 * Страница списка категорий товаров маркетплейса.
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
import MarketCategoryGrid from '@/Components/Public/Default/Market/MarketCategory/MarketCategoryGrid.vue'
import MarketCategoryRows from '@/Components/Public/Default/Market/MarketCategory/MarketCategoryRows.vue'
import Pagination from '@/Components/Public/Default/Pagination/Pagination.vue'
import FrontendPagination from '@/Components/Public/Default/Pagination/FrontendPagination.vue'
import PublicAdminBottomPanel from '@/Components/Admin/UI/PublicAdminPanel/PublicAdminBottomPanel.vue'

const { t } = useI18n()

/* ===================== PROPS ===================== */

/** Props страницы */
const props = defineProps({
    locale: { type: String, default: '' },

    seo: {
        type: Object,
        default: () => ({
            title: '',
            keywords: '',
            description: ''
        })
    },

    useServerProcessing: { type: Boolean, default: false },
    publicMarketCategoriesProcessingMode: { type: String, default: 'server' },

    title: { type: String, default: '' },
    canLogin: { type: Boolean, default: false },
    canRegister: { type: Boolean, default: false },

    categoryTree: { type: Array, default: () => [] },

    categories: { type: [Array, Object], default: () => [] },
    categoriesCount: { type: Number, default: 0 },
    categoriesFound: { type: Number, default: 0 },

    filters: { type: Object, default: () => ({}) },

    /**
     * Источник значения —
     * PublicSettingsService на backend.
     */
    defaultSort: { type: String, default: '' }
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

/* ===================== CATEGORIES DATA ===================== */

/** Универсальный список категорий */
const categoriesData = computed(() => {
    if (Array.isArray(props.categories)) {
        return props.categories
    }

    if (Array.isArray(props.categories?.data)) {
        return props.categories.data
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
 * Оба открыты  → 2.
 * Один свернут → 3.
 * Оба свернуты → 4.
 */
const categoryGridCols = computed(() => {
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
        props.filters?.q
        ?? ''
    )
)

/**
 * Текущая сортировка.
 *
 * Значение по умолчанию приходит
 * исключительно с backend.
 */
const sort = ref(
    String(
        props.filters?.sort
        || props.defaultSort
        || ''
    )
)

/** Ключ режима отображения */
const VIEW_KEY =
    'public_market_categories_view'

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
 * Количество категорий на странице.
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

/**
 * Public-сортировки.
 *
 * Список соответствует
 * scopePublicSortByParam().
 */
const categorySortOptions = [
    { value: 'idDesc', label: 'ID 9→0' },
    { value: 'idAsc', label: 'ID 0→9' },

    { value: 'sortAsc', label: `${t('sortNumber')} 0→9` },
    { value: 'sortDesc', label: `${t('sortNumber')} 9→0` },

    { value: 'titleAsc', label: `${t('title')} A→Z` },
    { value: 'titleDesc', label: `${t('title')} Z→A` },

    { value: 'levelAsc', label: `${t('level')} 0→9` },
    { value: 'levelDesc', label: `${t('level')} 9→0` },

    { value: 'parentAsc', label: `Parent ID 0→9` },
    { value: 'parentDesc', label: `Parent ID 9→0` },

    { value: 'childrenDesc', label: `${t('subheadings')} 9→0` },
    { value: 'childrenAsc', label: `${t('subheadings')} 0→9` },

    { value: 'productsDesc', label: `${t('products')} 9→0` },
    { value: 'productsAsc', label: `${t('products')} 0→9` },

    { value: 'imagesDesc', label: `${t('images')} 9→0` },
    { value: 'imagesAsc', label: `${t('images')} 0→9` },

    { value: 'viewsDesc', label: `${t('views')} 9→0` },
    { value: 'viewsAsc', label: `${t('views')} 0→9` },

    { value: 'urlAsc', label: 'URL A→Z' },
    { value: 'urlDesc', label: 'URL Z→A' },

    { value: 'publishedAtDesc', label: `${t('publishedAt')} 9→0` },
    { value: 'publishedAtAsc', label: `${t('publishedAt')} 0→9` }
]

/* ===================== FRONTEND MODE ===================== */

/** Текущая frontend-страница */
const frontendCurrentPage = ref(1)

/** Плавный скролл к списку */
const {
    targetRef: scrollTarget,
    scrollToTarget
} = useSmoothScrollTo({
    offset: 80,
    duration: 1200
})

/** Нормализация текста */
const normalizeText = (value) => {
    return String(
        value ?? ''
    ).toLowerCase()
}

/**
 * Перевод категории уже разрешен backend:
 * current locale → fallback → null.
 */
const getCategoryTranslation = (category) => {
    return category?.translation || null
}

/** Название категории */
const getCategoryTitle = (category) => {
    return getCategoryTranslation(
        category
    )?.title || ''
}

/** Подзаголовок категории */
const getCategorySubtitle = (category) => {
    return getCategoryTranslation(
        category
    )?.subtitle || ''
}

/** Краткое описание категории */
const getCategoryShort = (category) => {
    return getCategoryTranslation(
        category
    )?.short || ''
}

/** Название родительской категории */
const getParentTitle = (category) => {
    return category?.parent
        ?.translation
        ?.title || ''
}

/**
 * Слова frontend-поиска.
 *
 * Повторяет Public Search:
 * - разделение по пробелам;
 * - минимальная длина слова 2;
 * - AND между словами;
 * - OR между полями.
 */
const searchWords = computed(() => {
    return normalizeText(q.value)
        .trim()
        .split(/\s+/u)
        .filter(
            (word) => word.length >= 2
        )
})

/**
 * Поля Public-поиска категории.
 *
 * Должны соответствовать
 * scopePublicSearch().
 */
const getCategorySearchValues = (category) => {
    return [
        category?.id,
        category?.parent_id,
        category?.level,
        category?.url,
        category?.icon,
        category?.views,

        getCategoryTitle(category),
        getCategorySubtitle(category),
        getCategoryShort(category),

        getParentTitle(category)
    ].map(normalizeText)
}

/** Локальный поиск */
const filteredCategories = computed(() => {
    const words = searchWords.value

    if (!words.length) {
        return categoriesData.value
    }

    return categoriesData.value.filter(
        (category) => {
            const values =
                getCategorySearchValues(
                    category
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
 * Стабильное сравнение числовых значений.
 *
 * При равенстве повторяет backend:
 * id DESC.
 */
const compareNumber = (
    a,
    b,
    direction = 'asc'
) => {
    const first = Number(a ?? 0)
    const second = Number(b ?? 0)

    return direction === 'desc'
        ? second - first
        : first - second
}

/** Стабильное сравнение строк */
const compareText = (
    a,
    b,
    direction = 'asc'
) => {
    const first = normalizeText(a)
    const second = normalizeText(b)

    const result = first.localeCompare(second)

    return direction === 'desc'
        ? -result
        : result
}

/** Сравнение дат */
const compareDate = (
    a,
    b,
    direction = 'asc'
) => {
    const first = a
        ? new Date(a).getTime()
        : 0

    const second = b
        ? new Date(b).getTime()
        : 0

    return direction === 'desc'
        ? second - first
        : first - second
}

/**
 * Backend использует id DESC
 * как дополнительную сортировку.
 */
const compareIdDesc = (a, b) => {
    return Number(b?.id ?? 0)
        - Number(a?.id ?? 0)
}

/** Локальная сортировка */
const sortedCategories = computed(() => {
    const list = [
        ...filteredCategories.value
    ]

    return list.sort((a, b) => {
        let result = 0

        switch (sort.value) {
            case 'idAsc':
                return compareNumber(
                    a.id,
                    b.id
                )

            case 'idDesc':
                return compareNumber(
                    a.id,
                    b.id,
                    'desc'
                )

            case 'sortAsc':
                result = compareNumber(
                    a.sort,
                    b.sort
                )
                break

            case 'sortDesc':
                result = compareNumber(
                    a.sort,
                    b.sort,
                    'desc'
                )
                break

            case 'titleAsc':
                result = compareText(
                    getCategoryTitle(a),
                    getCategoryTitle(b)
                )
                break

            case 'titleDesc':
                result = compareText(
                    getCategoryTitle(a),
                    getCategoryTitle(b),
                    'desc'
                )
                break

            case 'levelAsc':
                result = compareNumber(
                    a.level,
                    b.level
                )
                break

            case 'levelDesc':
                result = compareNumber(
                    a.level,
                    b.level,
                    'desc'
                )
                break

            case 'parentAsc':
                result = compareNumber(
                    a.parent_id,
                    b.parent_id
                )
                break

            case 'parentDesc':
                result = compareNumber(
                    a.parent_id,
                    b.parent_id,
                    'desc'
                )
                break

            case 'childrenAsc':
                result = compareNumber(
                    a.children_count,
                    b.children_count
                )
                break

            case 'childrenDesc':
                result = compareNumber(
                    a.children_count,
                    b.children_count,
                    'desc'
                )
                break

            case 'productsAsc':
                result = compareNumber(
                    a.products_count,
                    b.products_count
                )
                break

            case 'productsDesc':
                result = compareNumber(
                    a.products_count,
                    b.products_count,
                    'desc'
                )
                break

            case 'imagesAsc':
                result = compareNumber(
                    a.images_count,
                    b.images_count
                )
                break

            case 'imagesDesc':
                result = compareNumber(
                    a.images_count,
                    b.images_count,
                    'desc'
                )
                break

            case 'viewsAsc':
                result = compareNumber(
                    a.views,
                    b.views
                )
                break

            case 'viewsDesc':
                result = compareNumber(
                    a.views,
                    b.views,
                    'desc'
                )
                break

            case 'urlAsc':
                result = compareText(
                    a.url,
                    b.url
                )
                break

            case 'urlDesc':
                result = compareText(
                    a.url,
                    b.url,
                    'desc'
                )
                break

            case 'publishedAtAsc':
                result = compareDate(
                    a.published_at,
                    b.published_at
                )
                break

            case 'publishedAtDesc':
                result = compareDate(
                    a.published_at,
                    b.published_at,
                    'desc'
                )
                break

            default:
                result = compareNumber(
                    a.sort,
                    b.sort
                )
                break
        }

        return result !== 0
            ? result
            : compareIdDesc(a, b)
    })
})

/**
 * Frontend-пагинация.
 *
 * Использует то же per_page,
 * которое определил backend.
 */
const frontendPaginatedCategories = computed(() => {
    const start = (
        frontendCurrentPage.value - 1
    ) * perPage.value

    return sortedCategories.value.slice(
        start,
        start + perPage.value
    )
})

/** Сбрасываем frontend-пагинацию */
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
        props.categories?.meta?.current_page
        ?? props.categories?.current_page
        ?? 1
    ) || 1
})

/** Последняя server-страница */
const lastPage = computed(() => {
    return Number(
        props.categories?.meta?.last_page
        ?? props.categories?.last_page
        ?? 1
    ) || 1
})

/** Маршрут списка категорий */
const indexRoute = () => {
    return route(
        'public.marketCategories.index'
    )
}

/**
 * Server-загрузка категорий.
 *
 * per_page намеренно не отправляем.
 * Его всегда определяет backend.
 */
const reloadCategories = (page = 1) => {
    router.get(
        indexRoute(),
        {
            q: q.value || undefined,

            sort:
                sort.value
                || props.defaultSort
                || undefined,

            view:
                viewMode.value
                || undefined,

            page
        },
        {
            preserveState: true,
            replace: true,
            preserveScroll: true
        }
    )
}

/** Server-поиск */
const submitSearch = () => {
    reloadCategories(1)
}

/** Сброс поиска и сортировки */
const resetSearch = () => {
    q.value = ''

    sort.value =
        props.defaultSort
        || ''

    frontendCurrentPage.value = 1

    if (props.useServerProcessing) {
        reloadCategories(1)
    }
}

/** Изменение сортировки */
const updateSort = (value) => {
    sort.value =
        value
        || props.defaultSort
        || ''

    if (props.useServerProcessing) {
        reloadCategories(1)
    }
}

/** Изменение режима отображения */
const updateViewMode = (value) => {
    viewMode.value =
        value
        || 'grid'

    frontendCurrentPage.value = 1

    if (props.useServerProcessing) {
        reloadCategories(1)
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

    reloadCategories(safePage)
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

/** Итоговый список категорий */
const displayedCategories = computed(() => {
    return props.useServerProcessing
        ? categoriesData.value
        : frontendPaginatedCategories.value
})
</script>

<template>
    <!-- SEO -->
    <Head>
        <title>{{ seo?.title || t('categories') }}</title>

        <meta name="title" :content="seo?.title || t('categories')" />
        <meta name="keywords" :content="seo?.keywords || ''" />
        <meta name="description" :content="seo?.description || ''" />

        <meta property="og:title" :content="seo?.title || t('categories')" />
        <meta property="og:description" :content="seo?.description || ''" />
        <meta property="og:type" content="website" />
        <meta property="og:url" :content="`/${locale}/catalog/categories`" />
        <meta property="og:image" content="" />
        <meta
            property="og:locale"
            :content="locale"
        />

        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="seo?.title || t('categories')" />
        <meta name="twitter:description" :content="seo?.description || ''" />
        <meta name="twitter:image" content="" />

        <meta name="DC.title" :content="seo?.title || t('categories')" />
        <meta name="DC.description" :content="seo?.description || ''" />
        <meta name="DC.identifier" :content="`/${locale}/catalog/categories`" />
        <meta name="DC.language" :content="locale" />
    </Head>

    <DefaultLayout :title="title" :can-login="canLogin" :can-register="canRegister">
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
                    <div class="w-full pb-6 slate-1">
                        <div class="mx-auto max-w-6xl">

                            <!-- Хлебные крошки -->
                            <nav class="text-sm" aria-label="Breadcrumb">
                                <ol class="flex flex-wrap items-center font-semibold">
                                    <li>
                                        <Link
                                            :href="route('home')"
                                            class="breadcrumb-link hover:underline"
                                        >
                                            {{ t('home') }}
                                        </Link>
                                    </li>

                                    <li>
                                        <span class="mx-2 breadcrumbs">/</span>
                                    </li>

                                    <li class="breadcrumbs">
                                        {{ t('categories') }}
                                    </li>
                                </ol>
                            </nav>

                            <!-- Заголовок -->
                            <div class="my-3 flex flex-wrap items-center justify-center gap-3 title">
                                <svg
                                    class="h-5 w-5 text-slate-600/85 dark:text-slate-200/85"
                                    viewBox="0 0 24 24"
                                    fill="currentColor"
                                >
                                    <path d="M4 4h6v6H4V4Zm10 0h6v6h-6V4ZM4 14h6v6H4v-6Zm10 0h6v6h-6v-6Z" />
                                </svg>

                                <h1 class="text-2xl font-bold">
                                    {{ t('categories') }}
                                </h1>
                            </div>

                            <!-- Подзаголовок -->
                            <div class="my-1 text-sm subtitle text-center">
                                Выберите категорию товаров и найдите всё необходимое в каталоге
                            </div>

                            <!-- Server toolbar -->
                            <EntityPageToolbar
                                v-if="useServerProcessing"
                                v-model="q"
                                :found="categoriesFound"
                                :view-mode="viewMode"
                                :sort-value="sort"
                                :sort-options="categorySortOptions"
                                :default-sort="defaultSort"
                                :found-label="t('categories')"
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
                                :found="sortedCategories.length"
                                :view-mode="viewMode"
                                :sort-value="sort"
                                :sort-options="categorySortOptions"
                                :default-sort="defaultSort"
                                :found-label="t('categories')"
                                :search-placeholder="t('searchByName')"
                                @reset="resetSearch"
                                @update:viewMode="updateViewMode"
                                @update:sortValue="updateSort"
                            />

                            <!-- Точка скролла -->
                            <div ref="scrollTarget"></div>

                            <!-- Нет данных -->
                            <div
                                v-if="displayedCategories.length === 0"
                                class="mt-6 text-center text-slate-700 dark:text-slate-300"
                            >
                                {{ t('noData') }}
                            </div>

                            <!-- Список -->
                            <div v-else>
                                <MarketCategoryGrid
                                    v-if="viewMode === 'grid'"
                                    :categories="displayedCategories"
                                    :cols="categoryGridCols"
                                />

                                <MarketCategoryRows
                                    v-else
                                    :categories="displayedCategories"
                                />
                            </div>

                            <!-- Server-пагинация -->
                            <Pagination
                                v-if="useServerProcessing"
                                :current-page="currentPage"
                                :last-page="lastPage"
                                :found="categoriesFound"
                                @prev="goPrev"
                                @next="goNext"
                                @go="goToPage"
                            />

                            <!-- Frontend-пагинация -->
                            <FrontendPagination
                                v-else
                                v-model:currentPage="frontendCurrentPage"
                                :items-per-page="perPage"
                                :total-items="sortedCategories.length"
                            />
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
            setting-key="publicMarketCategoriesProcessingMode"
            :mode="publicMarketCategoriesProcessingMode"
            :use-server-processing="useServerProcessing"
            :total="categoriesCount"
        />
    </DefaultLayout>
</template>
