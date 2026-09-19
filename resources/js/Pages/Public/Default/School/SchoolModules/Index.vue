<script setup>
/**
 * Публичная страница списка модулей.
 *
 * Возможности:
 * - server / frontend / auto обработка;
 * - поиск;
 * - сортировка;
 * - пагинация;
 * - grid / rows отображение;
 * - боковые колонки;
 * - дерево треков;
 * - хештеги;
 * - видео;
 * - баннеры;
 * - расширенное SEO;
 * - Open Graph;
 * - Twitter Cards;
 * - Dublin Core;
 * - Schema.org JSON-LD.
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
import LeftSidebarSchool from '@/Components/Public/Default/Partials/LeftSidebarSchool.vue'
import RightSidebarSchool from '@/Components/Public/Default/Partials/RightSidebarSchool.vue'
import EntityPageToolbar from '@/Components/Public/Default/PageToolbar/EntityPageToolbar.vue'
import FrontendEntityPageToolbar from '@/Components/Public/Default/PageToolbar/FrontendEntityPageToolbar.vue'
import Pagination from '@/Components/Public/Default/Pagination/Pagination.vue'
import FrontendPagination from '@/Components/Public/Default/Pagination/FrontendPagination.vue'
import SectionVideoList from '@/Components/Public/Default/Blog/BlogVideo/SectionVideoList.vue'
import SectionBanners from '@/Components/Public/Default/Blog/BlogBanner/SectionBanners.vue'
import ModuleGrid from '@/Components/Public/Default/School/SchoolModule/ModuleGrid.vue'
import ModuleRows from '@/Components/Public/Default/School/SchoolModule/ModuleRows.vue'
import PublicAdminBottomPanel from '@/Components/Admin/UI/PublicAdminPanel/PublicAdminBottomPanel.vue'

const { t } = useI18n()

/* ===================== PROPS ===================== */

const props = defineProps({
    locale: {
        type: String,
        default: 'ru'
    },

    seo: {
        type: Object,
        default: () => ({
            title: '',
            keywords: '',
            description: ''
        })
    },

    useServerProcessing: {
        type: Boolean,
        default: false
    },

    publicSchoolModulesProcessingMode: {
        type: String,
        default: 'server'
    },

    title: {
        type: String,
        default: ''
    },

    canLogin: {
        type: Boolean,
        default: false
    },

    canRegister: {
        type: Boolean,
        default: false
    },

    trackTree: {
        type: Array,
        default: () => []
    },

    modules: {
        type: [Array, Object],
        default: () => []
    },

    modulesCount: {
        type: Number,
        default: 0
    },

    modulesFound: {
        type: Number,
        default: 0
    },

    filters: {
        type: Object,
        default: () => ({})
    },

    hashtags: {
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

const page = usePage()
const siteSettings = page.props?.siteSettings || {}

const isAdmin = computed(() => {
    return page.props?.isAdmin === true
})

const trackTree = computed(() => {
    return Array.isArray(props.trackTree)
        ? props.trackTree
        : []
})

/* ===================== MODULES DATA ===================== */

const modulesData = computed(() => {
    if (Array.isArray(props.modules)) {
        return props.modules
    }

    if (Array.isArray(props.modules?.data)) {
        return props.modules.data
    }

    return []
})

/* ===================== SIDEBARS ===================== */

const showLeft = computed(() => {
    return !siteSettings?.ViewLeftColumn
        || siteSettings.ViewLeftColumn === 'true'
})

const showRight = computed(() => {
    return !siteSettings?.ViewRightColumn
        || siteSettings.ViewRightColumn === 'true'
})

const LEFT_SIDEBAR_KEY = 'public_left_sidebar_collapsed'
const RIGHT_SIDEBAR_KEY = 'public_right_sidebar_collapsed'

const getStoredBoolean = (key, defaultValue = true) => {
    const value = localStorage.getItem(key)

    if (value === null) {
        return defaultValue
    }

    return value === 'true'
}

const leftCollapsed = ref(
    getStoredBoolean(LEFT_SIDEBAR_KEY, true)
)

const rightCollapsed = ref(
    getStoredBoolean(RIGHT_SIDEBAR_KEY, true)
)

/**
 * Количество колонок карточек.
 *
 * 2 — оба сайдбара открыты.
 * 3 — открыт один.
 * 4 — оба свернуты.
 */
const gridCols = computed(() => {
    const leftExpanded =
        showLeft.value && !leftCollapsed.value

    const rightExpanded =
        showRight.value && !rightCollapsed.value

    if (leftExpanded && rightExpanded) {
        return 2
    }

    if (leftExpanded || rightExpanded) {
        return 3
    }

    return 4
})

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

const q = ref(
    String(props.filters?.q ?? '')
)

const DEFAULT_SORT = 'idDesc'

const sort = ref(
    String(props.filters?.sort ?? DEFAULT_SORT)
)

const VIEW_KEY = 'public_school_modules_view'

const viewMode = ref(
    String(
        props.filters?.view
        || localStorage.getItem(VIEW_KEY)
        || 'grid'
    )
)

watch(viewMode, (value) => {
    localStorage.setItem(VIEW_KEY, value)
})

const perPage = computed(() => {
    const value = Number(props.filters?.per_page)

    return Number.isFinite(value) && value > 0
        ? value
        : 12
})

/**
 * Public-сортировки.
 *
 * Полностью соответствуют:
 * - SchoolModule::publicSortByParam();
 * - SchoolModuleSharedResource;
 * - frontend sortedModules.
 */
const moduleSortOptions = [
    { value: 'idDesc', label: t('idDesc') },
    { value: 'idAsc', label: t('idAsc') },

    { value: 'sortAsc', label: `${t('sortNumber')} 0→9` },
    { value: 'sortDesc', label: `${t('sortNumber')} 9→0` },

    { value: 'titleAsc', label: `${t('title')} A→Z` },
    { value: 'titleDesc', label: `${t('title')} Z→A` },

    { value: 'difficultyDesc', label: `${t('sortDifficulty')} 9→0` },
    { value: 'difficultyAsc', label: `${t('sortDifficulty')} 0→9` },

    { value: 'durationDesc', label: `${t('duration')} 9→0` },
    { value: 'durationAsc', label: `${t('duration')} 0→9` },

    { value: 'lessonsDesc', label: `${t('lessons')} 9→0` },
    { value: 'lessonsAsc', label: `${t('lessons')} 0→9` },

    { value: 'viewsDesc', label: `${t('views')} 9→0` },
    { value: 'viewsAsc', label: `${t('views')} 0→9` },

    { value: 'likesDesc', label: `${t('likes')} 9→0` },
    { value: 'likesAsc', label: `${t('likes')} 0→9` },

    { value: 'popularityDesc', label: `${t('popularity')} 9→0` },
    { value: 'popularityAsc', label: `${t('popularity')} 0→9` },

    { value: 'ratingAvgDesc', label: `${t('ratingAvg')} 9→0` },
    { value: 'ratingAvgAsc', label: `${t('ratingAvg')} 0→9` },

    { value: 'ratingCountDesc', label: `${t('ratingCount')} 9→0` },
    { value: 'ratingCountAsc', label: `${t('ratingCount')} 0→9` },

    { value: 'publishedAtDesc', label: `${t('publishedAt')} ↓` },
    { value: 'publishedAtAsc', label: `${t('publishedAt')} ↑` }
]

/* ===================== FRONTEND MODE ===================== */

const frontendCurrentPage = ref(1)

const {
    targetRef: scrollTarget,
    scrollToTarget
} = useSmoothScrollTo({
    offset: 80,
    duration: 1200
})

const normalizeText = (value) => {
    return String(value ?? '').toLowerCase()
}

const getModuleTitle = (module) => {
    return module?.translation?.title || ''
}

const getModuleShort = (module) => {
    return module?.translation?.short || ''
}

const getModuleSlug = (module) => {
    return module?.slug || ''
}

/**
 * Локальный Public-поиск.
 *
 * Полностью соответствует SchoolModule::publicSearch():
 * - translation.title;
 * - translation.short;
 * - slug.
 */
const filteredModules = computed(() => {
    const query = normalizeText(q.value).trim()

    if (!query) {
        return modulesData.value
    }

    return modulesData.value.filter((module) => {
        return [
            getModuleTitle(module),
            getModuleShort(module),
            getModuleSlug(module)
        ].some((value) =>
            normalizeText(value).includes(query)
        )
    })
})

/**
 * Open Graph locale без жёсткого списка языков.
 *
 * Intl.Locale динамически определяет регион для любой
 * добавленной локали: ru -> ru_RU, en -> en_US,
 * de -> de_DE и т.д. Если окружение не поддерживает
 * maximize(), используем переданную приложением локаль.
 */
const ogLocale = computed(() => {
    const locale = String(props.locale || '').trim().replace('_', '-')

    if (!locale) {
        return undefined
    }

    try {
        const normalized = new Intl.Locale(locale).maximize()

        return normalized.region
            ? `${normalized.language}_${normalized.region}`
            : normalized.language
    } catch {
        return locale.replace('-', '_')
    }
})

/**
 * Локальная Public-сортировка.
 */
const sortedModules = computed(() => {
    const list = [...filteredModules.value]

    return list.sort((a, b) => {
        switch (sort.value) {
            case 'idAsc':
                return (a.id ?? 0) - (b.id ?? 0)

            case 'idDesc':
                return (b.id ?? 0) - (a.id ?? 0)

            case 'sortAsc':
                return (a.sort ?? 0) - (b.sort ?? 0)

            case 'sortDesc':
                return (b.sort ?? 0) - (a.sort ?? 0)

            case 'titleAsc':
                return normalizeText(getModuleTitle(a))
                    .localeCompare(
                        normalizeText(getModuleTitle(b))
                    )

            case 'titleDesc':
                return normalizeText(getModuleTitle(b))
                    .localeCompare(
                        normalizeText(getModuleTitle(a))
                    )

            case 'difficultyAsc':
                return (a.difficulty ?? 0)
                    - (b.difficulty ?? 0)

            case 'difficultyDesc':
                return (b.difficulty ?? 0)
                    - (a.difficulty ?? 0)

            case 'durationAsc':
                return (a.duration ?? 0)
                    - (b.duration ?? 0)

            case 'durationDesc':
                return (b.duration ?? 0)
                    - (a.duration ?? 0)

            case 'lessonsAsc':
                return (a.lessons_count ?? 0)
                    - (b.lessons_count ?? 0)

            case 'lessonsDesc':
                return (b.lessons_count ?? 0)
                    - (a.lessons_count ?? 0)

            case 'viewsAsc':
                return (a.views ?? 0)
                    - (b.views ?? 0)

            case 'viewsDesc':
                return (b.views ?? 0)
                    - (a.views ?? 0)

            case 'likesAsc':
                return (a.likes_count ?? 0)
                    - (b.likes_count ?? 0)

            case 'likesDesc':
                return (b.likes_count ?? 0)
                    - (a.likes_count ?? 0)

            case 'popularityAsc':
                return (a.popularity ?? 0)
                    - (b.popularity ?? 0)

            case 'popularityDesc':
                return (b.popularity ?? 0)
                    - (a.popularity ?? 0)

            case 'ratingAvgAsc':
                return (a.rating_avg ?? 0)
                    - (b.rating_avg ?? 0)

            case 'ratingAvgDesc':
                return (b.rating_avg ?? 0)
                    - (a.rating_avg ?? 0)

            case 'ratingCountAsc':
                return (a.rating_count ?? 0)
                    - (b.rating_count ?? 0)

            case 'ratingCountDesc':
                return (b.rating_count ?? 0)
                    - (a.rating_count ?? 0)

            case 'publishedAtAsc':
                return new Date(a.published_at ?? 0)
                    - new Date(b.published_at ?? 0)

            case 'publishedAtDesc':
                return new Date(b.published_at ?? 0)
                    - new Date(a.published_at ?? 0)

            default:
                return 0
        }
    })
})

const frontendPaginatedModules = computed(() => {
    const start = (
        frontendCurrentPage.value - 1
    ) * perPage.value

    return sortedModules.value.slice(
        start,
        start + perPage.value
    )
})

watch([q, sort, viewMode], () => {
    frontendCurrentPage.value = 1
})

watch(frontendCurrentPage, () => {
    if (!props.useServerProcessing) {
        scrollToTarget()
    }
})

/* ===================== SERVER MODE ===================== */

const currentPage = computed(() => {
    return Number(
        props.modules?.meta?.current_page
        ?? props.modules?.current_page
        ?? 1
    ) || 1
})

const lastPage = computed(() => {
    return Number(
        props.modules?.meta?.last_page
        ?? props.modules?.last_page
        ?? 1
    ) || 1
})

const indexRoute = () => {
    return route('public.schoolModules.index')
}

const reloadModules = (page = 1) => {
    router.get(
        indexRoute(),
        {
            q: q.value || undefined,
            sort: sort.value || undefined,
            view: viewMode.value || undefined,
            page
        },
        {
            preserveState: true,
            replace: true,
            preserveScroll: true
        }
    )
}

const submitSearch = () => {
    reloadModules(1)
}

const resetSearch = () => {
    q.value = ''
    sort.value = DEFAULT_SORT
    frontendCurrentPage.value = 1

    if (props.useServerProcessing) {
        reloadModules(1)
    }
}

const updateSort = (value) => {
    sort.value = value || DEFAULT_SORT

    if (props.useServerProcessing) {
        reloadModules(1)
    }
}

const updateViewMode = (value) => {
    viewMode.value = value || 'grid'
    frontendCurrentPage.value = 1

    if (props.useServerProcessing) {
        reloadModules(1)
    }
}

const goToPage = (pageNumber) => {
    const value = Number(pageNumber)

    if (!Number.isFinite(value)) {
        return
    }

    const safePage = Math.max(
        1,
        Math.min(value, lastPage.value)
    )

    reloadModules(safePage)
}

const goPrev = () => {
    if (currentPage.value <= 1) {
        return
    }

    goToPage(currentPage.value - 1)
}

const goNext = () => {
    if (currentPage.value >= lastPage.value) {
        return
    }

    goToPage(currentPage.value + 1)
}

/* ===================== COMMON VIEW ===================== */

const displayedModules = computed(() => {
    return props.useServerProcessing
        ? modulesData.value
        : frontendPaginatedModules.value
})

/* ===================== SEO ===================== */

const seoTitle = computed(() => {
    return String(
        props.seo?.title
        || t('modules')
    ).trim()
})

const seoDescription = computed(() => {
    return String(
        props.seo?.description
        || t('modules')
    ).trim()
})

const seoKeywords = computed(() => {
    return String(
        props.seo?.keywords
        || ''
    ).trim()
})

const seoCurrentPage = computed(() => {
    return props.useServerProcessing
        ? currentPage.value
        : frontendCurrentPage.value
})

/**
 * Фильтры q/sort/view не являются частью canonical.
 */
const canonicalPath = computed(() => {
    const base = `/${props.locale}/school/modules`

    return seoCurrentPage.value > 1
        ? `${base}?page=${seoCurrentPage.value}`
        : base
})

const canonicalUrl = computed(() => {
    if (typeof window === 'undefined') {
        return canonicalPath.value
    }

    return new URL(
        canonicalPath.value,
        window.location.origin
    ).toString()
})

const homeUrl = computed(() => {
    if (typeof window === 'undefined') {
        return '/'
    }

    return new URL(
        route('home'),
        window.location.origin
    ).toString()
})

const getAbsoluteModuleUrl = (module) => {
    if (!module?.course?.slug || !module?.slug) return ''

    const url = route('public.schoolModules.show', {
        courseSlug: module.course.slug,
        slug: module.slug
    })

    if (typeof window === 'undefined') return url

    return new URL(url, window.location.origin).toString()
}

const siteName = computed(() => {
    return String(
        siteSettings?.siteName
        || siteSettings?.SiteName
        || ''
    ).trim()
})

const robotsContent = computed(() => {
    return q.value.trim()
        ? 'noindex, follow'
        : 'index, follow'
})

const collectionPageSchema = computed(() => ({
    '@context': 'https://schema.org',
    '@type': 'CollectionPage',
    '@id': `${canonicalUrl.value}#webpage`,
    url: canonicalUrl.value,
    name: seoTitle.value,
    description: seoDescription.value,
    inLanguage: props.locale,
    isPartOf: {
        '@type': 'WebSite',
        url: homeUrl.value,
        ...(siteName.value
            ? { name: siteName.value }
            : {})
    }
}))

const breadcrumbSchema = computed(() => ({
    '@context': 'https://schema.org',
    '@type': 'BreadcrumbList',
    itemListElement: [
        {
            '@type': 'ListItem',
            position: 1,
            name: t('home'),
            item: homeUrl.value
        },
        {
            '@type': 'ListItem',
            position: 2,
            name: seoTitle.value,
            item: canonicalUrl.value
        }
    ]
}))

const moduleItemListSchema = computed(() => ({
    '@context': 'https://schema.org',
    '@type': 'ItemList',
    name: seoTitle.value,
    numberOfItems: displayedModules.value.length,
    itemListElement: displayedModules.value.map(
        (module, index) => {
            const moduleUrl =
                getAbsoluteModuleUrl(module)

            const item = {
                '@type': 'LearningResource',
                '@id': `${moduleUrl}#learning-resource`,
                url: moduleUrl,
                name: getModuleTitle(module),
                inLanguage: props.locale
            }

            if (getModuleShort(module)) {
                item.description =
                    getModuleShort(module)
            }

            return {
                '@type': 'ListItem',
                position:
                    (
                        (
                            seoCurrentPage.value - 1
                        ) * perPage.value
                    )
                    + index
                    + 1,
                url: moduleUrl,
                item
            }
        }
    )
}))

const collectionPageJsonLd = computed(() => {
    return JSON.stringify(
        collectionPageSchema.value
    )
})

const breadcrumbJsonLd = computed(() => {
    return JSON.stringify(
        breadcrumbSchema.value
    )
})

const moduleItemListJsonLd = computed(() => {
    return JSON.stringify(
        moduleItemListSchema.value
    )
})
</script>

<template>
    <!-- ===================== SEO ===================== -->

    <Head>
        <!-- Основные -->
        <title>{{ seoTitle }}</title>

        <meta
            name="title"
            :content="seoTitle"
        />

        <meta
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

        <meta
            name="googlebot"
            :content="robotsContent"
        />

        <link
            rel="canonical"
            :href="canonicalUrl"
        />

        <!-- Open Graph -->
        <meta
            property="og:title"
            :content="seoTitle"
        />

        <meta
            property="og:description"
            :content="seoDescription"
        />

        <meta
            property="og:type"
            content="website"
        />

        <meta
            property="og:url"
            :content="canonicalUrl"
        />

        <meta
            property="og:locale"
            :content="ogLocale"
        />

        <meta
            v-if="siteName"
            property="og:site_name"
            :content="siteName"
        />

        <!-- Twitter -->
        <meta
            name="twitter:card"
            content="summary"
        />

        <meta
            name="twitter:title"
            :content="seoTitle"
        />

        <meta
            name="twitter:description"
            :content="seoDescription"
        />

        <!-- Dublin Core -->
        <meta
            name="DC.title"
            :content="seoTitle"
        />

        <meta
            name="DC.description"
            :content="seoDescription"
        />

        <meta
            v-if="seoKeywords"
            name="DC.subject"
            :content="seoKeywords"
        />

        <meta
            name="DC.identifier"
            :content="canonicalUrl"
        />

        <meta
            name="DC.language"
            :content="locale"
        />

        <meta
            name="DC.type"
            content="Collection"
        />

        <meta
            name="DC.format"
            content="text/html"
        />

        <meta
            v-if="siteName"
            name="DC.publisher"
            :content="siteName"
        />

        <!-- JSON-LD -->
        <component
            :is="'script'"
            type="application/ld+json"
            v-html="collectionPageJsonLd"
        />

        <component
            :is="'script'"
            type="application/ld+json"
            v-html="breadcrumbJsonLd"
        />

        <component
            :is="'script'"
            type="application/ld+json"
            v-html="moduleItemListJsonLd"
        />
    </Head>

    <DefaultLayout
        :title="title"
        :can-login="canLogin"
        :can-register="canRegister"
    >
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
                        <LeftSidebarSchool
                            :track-tree="trackTree"
                            :collapsed="leftCollapsed"
                            @collapsed="leftCollapsed = $event"
                        />
                    </aside>

                    <!-- Центральный SEO-контент страницы. Сайдбары намеренно находятся вне CollectionPage. -->
                    <article
                        class="w-full pb-6 slate-1"
                        itemscope
                        itemtype="https://schema.org/CollectionPage"
                    >
                        <div class="mx-auto max-w-6xl">

                            <!-- Хлебные крошки -->
                            <nav
                                class="text-sm"
                                aria-label="Breadcrumb"
                                itemscope
                                itemtype="https://schema.org/BreadcrumbList"
                            >
                                <ol class="flex flex-wrap items-center font-semibold">
                                    <li
                                        itemprop="itemListElement"
                                        itemscope
                                        itemtype="https://schema.org/ListItem"
                                    >
                                        <Link
                                            :href="route('home')"
                                            class="breadcrumb-link hover:underline"
                                            itemprop="item"
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

                                    <li aria-hidden="true">
                                    <span class="mx-2 breadcrumbs">
                                        /
                                    </span>
                                    </li>

                                    <li
                                        class="breadcrumbs"
                                        itemprop="itemListElement"
                                        itemscope
                                        itemtype="https://schema.org/ListItem"
                                        aria-current="page"
                                    >
                                    <span itemprop="name">
                                        {{ t('modules') }}
                                    </span>

                                        <meta
                                            itemprop="item"
                                            :content="canonicalUrl"
                                        />

                                        <meta
                                            itemprop="position"
                                            content="2"
                                        />
                                    </li>
                                </ol>
                            </nav>

                            <!-- Заголовок -->
                            <div
                                class="my-3 flex flex-wrap items-center
                                   justify-center gap-3 title"
                            >
                                <svg
                                    class="shrink-0 h-5 w-5
                                       text-slate-600/85
                                       dark:text-slate-200/85"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                >
                                    <rect x="1" y="1" width="10" height="10" rx="2"></rect>
                                    <path
                                        class="fill-current text-slate-400"
                                        d="M23.428,4.618,19.381.572h0a1.957,1.957,0,0,0-2.762,0L12.572,4.618a1.959,1.959,0,0,0,0,2.764l4.047,4.047a1.957,1.957,0,0,0,2.762,0l4.047-4.046A1.959,1.959,0,0,0,23.428,4.618Z"
                                    ></path>
                                    <rect x="13" y="13" width="10" height="10" rx="2"></rect>
                                    <rect x="1" y="13" width="10" height="10" rx="2"></rect>
                                </svg>

                                <h1
                                    class="text-2xl font-bold"
                                    itemprop="name"
                                >
                                    {{ t('modules') }}
                                </h1>
                            </div>

                            <!-- SEO-описание страницы -->
                            <div
                                v-if="seoDescription"
                                class="my-1 text-sm subtitle text-center"
                                itemprop="description"
                            >
                                {{ seoDescription }}
                            </div>

                            <!-- Поиск, количество, сортировка, вид -->
                            <EntityPageToolbar
                                v-if="useServerProcessing"
                                v-model="q"
                                :found="modulesFound"
                                :view-mode="viewMode"
                                :sort-value="sort"
                                :sort-options="moduleSortOptions"
                                :default-sort="DEFAULT_SORT"
                                :found-label="t('modules')"
                                :search-placeholder="t('searchByName')"
                                @submit="submitSearch"
                                @reset="resetSearch"
                                @update:viewMode="updateViewMode"
                                @update:sortValue="updateSort"
                            />

                            <FrontendEntityPageToolbar
                                v-else
                                v-model="q"
                                :found="sortedModules.length"
                                :view-mode="viewMode"
                                :sort-value="sort"
                                :sort-options="moduleSortOptions"
                                :default-sort="DEFAULT_SORT"
                                :found-label="t('modules')"
                                :search-placeholder="t('searchByName')"
                                @reset="resetSearch"
                                @update:viewMode="updateViewMode"
                                @update:sortValue="updateSort"
                            />

                            <div ref="scrollTarget"></div>

                            <!-- Нет данных -->
                            <div
                                v-if="displayedModules.length === 0"
                                class="mt-6 text-center
                                   text-slate-700 dark:text-slate-300"
                            >
                                {{ t('noData') }}
                            </div>

                            <!-- Модули -->
                            <section
                                v-else
                                :aria-label="t('modules')"
                                itemprop="mainEntity"
                            >
                                <ModuleGrid
                                    v-if="viewMode === 'grid'"
                                    :modules="displayedModules"
                                    :cols="gridCols"
                                />

                                <ModuleRows
                                    v-else
                                    :modules="displayedModules"
                                />
                            </section>

                            <!-- Пагинация -->
                            <Pagination
                                v-if="useServerProcessing"
                                :current-page="currentPage"
                                :last-page="lastPage"
                                :found="modulesFound"
                                @prev="goPrev"
                                @next="goNext"
                                @go="goToPage"
                            />

                            <FrontendPagination
                                v-else
                                v-model:currentPage="frontendCurrentPage"
                                :items-per-page="perPage"
                                :total-items="sortedModules.length"
                            />

                            <!-- Главные видео и баннеры -->
                            <SectionVideoList
                                :videos="mainVideos"
                            />

                            <SectionBanners
                                :banners="mainBanners"
                            />
                        </div>
                    </article>

                    <!-- Правая колонка -->
                    <aside
                        v-if="showRight"
                        class="shrink-0
                           transition-all duration-300"
                        :class="rightCollapsed ? 'lg:w-6' : 'lg:w-72'"
                    >
                        <RightSidebarSchool
                            :collapsed="rightCollapsed"
                            @collapsed="rightCollapsed = $event"
                        />
                    </aside>
                </div>
            </div>
        </main>

        <!-- Подвал -->
        <FooterBlog />

        <Progress />

        <!-- Нижняя панель администратора -->
        <PublicAdminBottomPanel
            v-if="isAdmin"
            setting-key="publicSchoolModulesProcessingMode"
            :mode="publicSchoolModulesProcessingMode"
            :use-server-processing="useServerProcessing"
            :total="modulesCount"
        />
    </DefaultLayout>
</template>
