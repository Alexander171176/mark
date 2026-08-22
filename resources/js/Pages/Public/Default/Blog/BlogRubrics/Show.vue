<script setup>
/**
 * Страница конкретной рубрики блога.
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
import LeftSidebar from '@/Components/Public/Default/Partials/LeftSidebar.vue'
import RightSidebar from '@/Components/Public/Default/Partials/RightSidebar.vue'

import EntityPageToolbar from '@/Components/Public/Default/PageToolbar/EntityPageToolbar.vue'
import Pagination from '@/Components/Public/Default/Pagination/Pagination.vue'
import FrontendPagination from '@/Components/Public/Default/Pagination/FrontendPagination.vue'

import RubricArticleGrid from '@/Components/Public/Default/Blog/BlogRubric/RubricArticleGrid.vue'
import RubricArticleRows from '@/Components/Public/Default/Blog/BlogRubric/RubricArticleRows.vue'
import RubricGrid from '@/Components/Public/Default/Blog/BlogRubric/RubricGrid.vue'
import RubricRows from '@/Components/Public/Default/Blog/BlogRubric/RubricRows.vue'

import SectionVideoList from '@/Components/Public/Default/Blog/BlogVideo/SectionVideoList.vue'
import SectionBanners from '@/Components/Public/Default/Blog/BlogBanner/SectionBanners.vue'
import ImageGalleryMain from '@/Components/Public/Default/Media/ImageGalleryMain.vue'

import PublicAdminBottomPanel
    from '@/Components/Admin/UI/PublicAdminPanel/PublicAdminBottomPanel.vue'

const { t } = useI18n()
const page = usePage()

const props = defineProps({
    title: { type: String, default: '' },
    canLogin: { type: Boolean, default: false },
    canRegister: { type: Boolean, default: false },

    locale: { type: String, default: 'ru' },

    rubric: {
        type: Object,
        default: () => ({}),
    },

    publicBlogArticlesProcessingMode: {
        type: String,
        default: 'server',
    },

    useServerProcessing: {
        type: Boolean,
        default: false,
    },

    articles: {
        type: [Array, Object],
        default: () => [],
    },

    articlesCount: {
        type: Number,
        default: 0,
    },

    articlesFound: {
        type: Number,
        default: 0,
    },

    filters: {
        type: Object,
        default: () => ({}),
    },

    rubricTree: {
        type: Array,
        default: () => [],
    },

    mainVideos: {
        type: [Array, Object],
        default: () => [],
    },

    mainBanners: {
        type: [Array, Object],
        default: () => [],
    },
})

/* ======================== Helpers ======================== */

const normalizeList = (value) => {
    if (Array.isArray(value)) return value
    if (Array.isArray(value?.data)) return value.data

    return []
}

const normalizeText = (value) =>
    String(value ?? '').trim().toLocaleLowerCase()

const safeNumber = (value) => {
    const number = Number(value)

    return Number.isFinite(number)
        ? number
        : 0
}

const safeDate = (value) => {
    const time = value
        ? Date.parse(value)
        : 0

    return Number.isFinite(time)
        ? time
        : 0
}

/* ======================== Rubric ======================== */

const rubric = computed(() =>
    props.rubric ?? {}
)

const rubricTranslation = computed(() =>
    rubric.value?.translation ?? {}
)

const rubricTitle = computed(() =>
    rubricTranslation.value?.title
    || ''
)

const rubricShort = computed(() =>
    rubricTranslation.value?.short
    || ''
)

const rubricDescription = computed(() =>
    rubricTranslation.value?.description
    || rubricShort.value
    || ''
)

const rubricLocale = computed(() =>
    rubricTranslation.value?.locale
    || props.locale
    || 'ru'
)

const childRubrics = computed(() =>
    normalizeList(
        rubric.value?.children
    )
)

const rubricImages = computed(() =>
    normalizeList(
        rubric.value?.images
    )
)

const hasRubricImages = computed(() =>
    rubricImages.value.length > 0
)

const hasSvgIcon = computed(() => {
    if (!rubric.value?.icon) {
        return false
    }

    return /^\s*<svg[\s\S]*<\/svg>\s*$/i.test(
        String(rubric.value.icon)
    )
})

/* ======================== SEO ======================== */

const seoTitle = computed(() =>
    rubricTranslation.value?.meta_title
    || rubricTitle.value
    || t('rubrics')
)

const seoKeywords = computed(() =>
    rubricTranslation.value?.meta_keywords
    || ''
)

const seoDescription = computed(() =>
    rubricTranslation.value?.meta_desc
    || rubricShort.value
    || rubricDescription.value
    || ''
)

const canonicalUrl = computed(() => {
    if (!rubric.value?.url) {
        return ''
    }

    return String(
        route('public.blogRubrics.show', {
            url: rubric.value.url,
        })
    )
})

const ogLocale = computed(() =>
    rubricLocale.value === 'ru'
        ? 'ru_RU'
        : rubricLocale.value
)

const dcSubject = computed(() =>
    seoKeywords.value
    || rubricTitle.value
)

const getImageUrl = (image) =>
    image?.webp_url
    || image?.image_url
    || image?.thumb_url
    || image?.url
    || ''

const seoImage = computed(() => {
    for (const image of rubricImages.value) {
        const url = getImageUrl(image)

        if (url) return url
    }

    return ''
})

/* ======================== Shared data ======================== */

const siteSettings = computed(() =>
    page.props?.siteSettings ?? {}
)

const isAdmin = computed(() =>
    page.props?.isAdmin === true
)

const rubricTree = computed(() =>
    Array.isArray(props.rubricTree)
        ? props.rubricTree
        : []
)

const mainVideos = computed(() =>
    normalizeList(props.mainVideos)
)

const mainBanners = computed(() =>
    normalizeList(props.mainBanners)
)

/* ======================== Articles ======================== */

const articlesData = computed(() =>
    normalizeList(props.articles)
)

const articlesCount = computed(() =>
    safeNumber(props.articlesCount)
)

const hasArticles = computed(() =>
    articlesCount.value > 0
)

const getArticleTitle = (article) =>
    article?.translation?.title || ''

const getArticleSubtitle = (article) =>
    article?.translation?.subtitle || ''

const getArticleShort = (article) =>
    article?.translation?.short || ''

const getArticleDescription = (article) =>
    article?.translation?.description || ''

const getArticleUrl = (article) =>
    article?.url || ''

/* ======================== Filters ======================== */

const qArticles = ref(
    String(
        props.filters?.q_articles
        ?? ''
    )
)

const DEFAULT_SORT = 'sortAsc'

const sortArticles = ref(
    String(
        props.filters?.sort_articles
        ?? DEFAULT_SORT
    )
)

const perPageArticles = computed(() => {
    const value = Number(
        props.filters?.per_page_articles
    )

    return Number.isFinite(value)
    && value > 0
        ? value
        : 12
})

const articleSortOptions = [
    { value: 'sortAsc', label: `${t('sortNumber')} 0→9` },
    { value: 'sortDesc', label: `${t('sortNumber')} 9→0` },

    { value: 'idDesc', label: t('idDesc') },
    { value: 'idAsc', label: t('idAsc') },

    { value: 'titleAsc', label: `${t('title')} A→Z` },
    { value: 'titleDesc', label: `${t('title')} Z→A` },

    { value: 'viewsDesc', label: `${t('views')} 9→0` },
    { value: 'viewsAsc', label: `${t('views')} 0→9` },

    { value: 'likesDesc', label: `${t('likes')} 9→0` },
    { value: 'likesAsc', label: `${t('likes')} 0→9` },

    { value: 'dateDesc', label: `${t('publishedAt')} ↓` },
    { value: 'dateAsc', label: `${t('publishedAt')} ↑` },
]

/* ======================== View mode ======================== */

const VIEW_KEY = 'public_blog_rubric_articles_view'

const viewMode = ref('grid')

onMounted(() => {
    try {
        const storedView =
            localStorage.getItem(VIEW_KEY)

        if (
            storedView === 'grid'
            || storedView === 'rows'
        ) {
            viewMode.value = storedView
        }
    } catch {
        //
    }
})

watch(
    viewMode,
    (value) => {
        try {
            localStorage.setItem(
                VIEW_KEY,
                value
            )
        } catch {
            //
        }
    }
)

/* ======================== Frontend search ======================== */

const filteredArticles = computed(() => {
    if (props.useServerProcessing) {
        return articlesData.value
    }

    const query =
        normalizeText(qArticles.value)

    if (!query) {
        return articlesData.value
    }

    return articlesData.value.filter(
        (article) => {
            const ownerName =
                article?.owner?.name
                || ''

            const rubricTitles =
                Array.isArray(article?.rubrics)
                    ? article.rubrics
                        .map(
                            (item) =>
                                item?.translation?.title
                                || ''
                        )
                        .join(' ')
                    : ''

            return [
                article?.id,
                getArticleTitle(article),
                getArticleSubtitle(article),
                getArticleShort(article),
                getArticleDescription(article),
                getArticleUrl(article),
                ownerName,
                rubricTitles,
            ].some((value) =>
                normalizeText(value).includes(query)
            )
        }
    )
})

/* ======================== Frontend sort ======================== */

const compareText = (a, b) =>
    String(a ?? '').localeCompare(
        String(b ?? ''),
        props.locale,
        { sensitivity: 'base' }
    )

const compareNumber = (a, b) =>
    safeNumber(a) - safeNumber(b)

const sortedArticles = computed(() => {
    if (props.useServerProcessing) {
        return filteredArticles.value
    }

    const list = [
        ...filteredArticles.value,
    ]

    switch (sortArticles.value) {
        case 'sortAsc':
            list.sort((a, b) =>
                compareNumber(a?.sort, b?.sort)
                || compareNumber(a?.id, b?.id)
            )
            break

        case 'sortDesc':
            list.sort((a, b) =>
                compareNumber(b?.sort, a?.sort)
                || compareNumber(b?.id, a?.id)
            )
            break

        case 'idAsc':
            list.sort((a, b) =>
                compareNumber(a?.id, b?.id)
            )
            break

        case 'idDesc':
            list.sort((a, b) =>
                compareNumber(b?.id, a?.id)
            )
            break

        case 'titleAsc':
            list.sort((a, b) =>
                compareText(
                    getArticleTitle(a),
                    getArticleTitle(b)
                )
            )
            break

        case 'titleDesc':
            list.sort((a, b) =>
                compareText(
                    getArticleTitle(b),
                    getArticleTitle(a)
                )
            )
            break

        case 'viewsAsc':
            list.sort((a, b) =>
                compareNumber(
                    a?.views,
                    b?.views
                )
            )
            break

        case 'viewsDesc':
            list.sort((a, b) =>
                compareNumber(
                    b?.views,
                    a?.views
                )
            )
            break

        case 'likesAsc':
            list.sort((a, b) =>
                compareNumber(
                    a?.likes_count,
                    b?.likes_count
                )
            )
            break

        case 'likesDesc':
            list.sort((a, b) =>
                compareNumber(
                    b?.likes_count,
                    a?.likes_count
                )
            )
            break

        case 'dateAsc':
            list.sort((a, b) =>
                safeDate(a?.published_at)
                - safeDate(b?.published_at)
            )
            break

        case 'dateDesc':
            list.sort((a, b) =>
                safeDate(b?.published_at)
                - safeDate(a?.published_at)
            )
            break
    }

    return list
})

/* ======================== Frontend pagination ======================== */

const frontendCurrentPage = ref(1)

const {
    targetRef: articlesScrollTarget,
    scrollToTarget: scrollToArticles,
} = useSmoothScrollTo({
    offset: 80,
    duration: 1200,
})

watch(
    [
        qArticles,
        sortArticles,
    ],
    () => {
        if (!props.useServerProcessing) {
            frontendCurrentPage.value = 1
        }
    }
)

watch(
    frontendCurrentPage,
    () => {
        if (!props.useServerProcessing) {
            scrollToArticles()
        }
    }
)

const effectiveArticlesFound = computed(() =>
    props.useServerProcessing
        ? safeNumber(
            props.articlesFound
        )
        : sortedArticles.value.length
)

const frontendPaginatedArticles = computed(() => {
    if (props.useServerProcessing) {
        return articlesData.value
    }

    const start =
        (
            frontendCurrentPage.value - 1
        ) * perPageArticles.value

    return sortedArticles.value.slice(
        start,
        start + perPageArticles.value
    )
})

const displayedArticles = computed(() =>
    props.useServerProcessing
        ? articlesData.value
        : frontendPaginatedArticles.value
)

/* ======================== Server pagination ======================== */

const currentPage = computed(() =>
    Number(
        props.articles?.meta?.current_page
        ?? props.articles?.current_page
        ?? 1
    ) || 1
)

const lastPage = computed(() =>
    Number(
        props.articles?.meta?.last_page
        ?? props.articles?.last_page
        ?? 1
    ) || 1
)

const reloadArticles = (pageNumber = 1) => {
    if (
        !props.useServerProcessing
        || !canonicalUrl.value
    ) {
        return
    }

    router.get(
        canonicalUrl.value,
        {
            q_articles:
                qArticles.value
                || undefined,

            sort_articles:
                sortArticles.value
                || undefined,

            page_articles:
            pageNumber,
        },
        {
            preserveState: true,
            replace: true,
            preserveScroll: true,
        }
    )
}

const goToPage = (pageNumber) => {
    const target = Math.min(
        Math.max(
            1,
            Number(pageNumber) || 1
        ),
        lastPage.value
    )

    reloadArticles(target)
}

const goPrev = () => {
    if (currentPage.value > 1) {
        goToPage(
            currentPage.value - 1
        )
    }
}

const goNext = () => {
    if (
        currentPage.value
        < lastPage.value
    ) {
        goToPage(
            currentPage.value + 1
        )
    }
}

/* ======================== Toolbar ======================== */

const applyArticleFilters = () => {
    if (props.useServerProcessing) {
        reloadArticles(1)
    } else {
        frontendCurrentPage.value = 1
    }
}

const resetArticleFilters = () => {
    if (props.useServerProcessing) {
        reloadArticles(1)
    } else {
        frontendCurrentPage.value = 1
    }
}

/* ======================== Sidebars ======================== */

const settingEnabled = (
    value,
    defaultValue = true
) => {
    if (
        value === undefined
        || value === null
        || value === ''
    ) {
        return defaultValue
    }

    if (typeof value === 'boolean') {
        return value
    }

    return String(value) === 'true'
}

const showLeft = computed(() =>
    settingEnabled(
        siteSettings.value?.ViewLeftColumn,
        true
    )
)

const showRight = computed(() =>
    settingEnabled(
        siteSettings.value?.ViewRightColumn,
        true
    )
)

const LEFT_SIDEBAR_KEY =
    'public_left_sidebar_collapsed'

const RIGHT_SIDEBAR_KEY =
    'public_right_sidebar_collapsed'

/**
 * Первый render:
 * оба sidebar свёрнуты.
 *
 * После mounted восстанавливаем
 * сохранённое состояние.
 */
const leftCollapsed = ref(true)
const rightCollapsed = ref(true)

const readStoredBoolean = (
    key,
    fallback = true
) => {
    try {
        const value =
            localStorage.getItem(key)

        return value === null
            ? fallback
            : value === 'true'
    } catch {
        return fallback
    }
}

const writeStoredBoolean = (
    key,
    value
) => {
    try {
        localStorage.setItem(
            key,
            String(Boolean(value))
        )
    } catch {
        //
    }
}

onMounted(() => {
    leftCollapsed.value =
        readStoredBoolean(
            LEFT_SIDEBAR_KEY,
            true
        )

    rightCollapsed.value =
        readStoredBoolean(
            RIGHT_SIDEBAR_KEY,
            true
        )
})

const setLeftCollapsed = (value) => {
    leftCollapsed.value =
        Boolean(value)

    writeStoredBoolean(
        LEFT_SIDEBAR_KEY,
        leftCollapsed.value
    )
}

const setRightCollapsed = (value) => {
    rightCollapsed.value =
        Boolean(value)

    writeStoredBoolean(
        RIGHT_SIDEBAR_KEY,
        rightCollapsed.value
    )
}

/**
 * Оба открыты → 2.
 * Один открыт → 3.
 * Оба закрыты → 4.
 */
const rubricGridCols = computed(() => {
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
</script>

<template>
    <Head>
        <!-- Basic SEO -->
        <title>{{ seoTitle }}</title>

        <meta name="title" :content="seoTitle">

        <meta
            v-if="seoDescription"
            name="description"
            :content="seoDescription"
        >

        <meta
            v-if="seoKeywords"
            name="keywords"
            :content="seoKeywords"
        >

        <meta
            name="robots"
            content="index, follow, max-image-preview:large"
        >

        <!-- Canonical -->
        <link
            v-if="canonicalUrl"
            rel="canonical"
            :href="canonicalUrl"
        >

        <!-- Open Graph -->
        <meta
            property="og:type"
            content="website"
        >

        <meta
            property="og:title"
            :content="seoTitle"
        >

        <meta
            v-if="seoDescription"
            property="og:description"
            :content="seoDescription"
        >

        <meta
            v-if="canonicalUrl"
            property="og:url"
            :content="canonicalUrl"
        >

        <meta
            property="og:locale"
            :content="ogLocale"
        >

        <meta
            v-if="seoImage"
            property="og:image"
            :content="seoImage"
        >

        <!-- Twitter / X -->
        <meta
            name="twitter:card"
            :content="seoImage ? 'summary_large_image' : 'summary'"
        >

        <meta
            name="twitter:title"
            :content="seoTitle"
        >

        <meta
            v-if="seoDescription"
            name="twitter:description"
            :content="seoDescription"
        >

        <meta
            v-if="seoImage"
            name="twitter:image"
            :content="seoImage"
        >

        <!-- Dublin Core -->
        <meta
            name="DC.title"
            :content="seoTitle"
        >

        <meta
            v-if="seoDescription"
            name="DC.description"
            :content="seoDescription"
        >

        <meta
            v-if="dcSubject"
            name="DC.subject"
            :content="dcSubject"
        >

        <meta
            name="DC.language"
            :content="rubricLocale"
        >

        <meta
            v-if="canonicalUrl"
            name="DC.identifier"
            :content="canonicalUrl"
        >

        <meta
            name="DC.type"
            content="Collection"
        >

        <meta
            name="DC.format"
            content="text/html"
        >
    </Head>

    <DefaultLayout
        :title="title"
        :can-login="canLogin"
        :can-register="canRegister"
    >
        <Navbar />

        <div class="min-h-screen px-3 max-w-full">
            <main class="mx-auto flex flex-col lg:flex-row gap-4 tracking-wider">

                <!-- Left sidebar -->
                <aside
                    v-if="showLeft"
                    class="shrink-0 mt-12 lg:mt-28 transition-all duration-300 overflow-hidden"
                    :class="leftCollapsed ? 'lg:w-10' : 'lg:w-64'"
                >
                    <LeftSidebar
                        :rubric-tree="rubricTree"
                        :collapsed="leftCollapsed"
                        @collapsed="setLeftCollapsed"
                    />
                </aside>

                <!-- Content -->
                <article
                    itemscope
                    itemtype="https://schema.org/CollectionPage"
                    :itemid="canonicalUrl"
                    class="w-full lg:mt-28 pb-6 slate-1 min-w-0"
                >
                    <meta
                        itemprop="name"
                        :content="seoTitle"
                    >

                    <meta
                        v-if="seoDescription"
                        itemprop="description"
                        :content="seoDescription"
                    >

                    <meta
                        v-if="seoKeywords"
                        itemprop="keywords"
                        :content="seoKeywords"
                    >

                    <meta
                        v-if="canonicalUrl"
                        itemprop="url"
                        :content="canonicalUrl"
                    >

                    <meta
                        itemprop="inLanguage"
                        :content="rubricLocale"
                    >

                    <div class="mx-auto max-w-6xl">

                        <!-- Breadcrumbs -->
                        <nav
                            class="text-sm mb-3"
                            aria-label="Breadcrumb"
                            itemscope
                            itemtype="https://schema.org/BreadcrumbList"
                        >
                            <ol class="flex flex-wrap items-center font-semibold">

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

                                    <meta itemprop="position" content="1">
                                </li>

                                <li
                                    itemprop="itemListElement"
                                    itemscope
                                    itemtype="https://schema.org/ListItem"
                                    class="flex items-center"
                                >
                                    <span class="mx-2 breadcrumbs">/</span>

                                    <Link
                                        itemprop="item"
                                        :href="route('public.blogRubrics.index')"
                                        class="breadcrumb-link hover:underline"
                                    >
                                        <span itemprop="name">
                                            {{ t('rubrics') }}
                                        </span>
                                    </Link>

                                    <meta itemprop="position" content="2">
                                </li>

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
                                        {{ rubricTitle }}
                                    </span>

                                    <meta
                                        v-if="canonicalUrl"
                                        itemprop="item"
                                        :content="canonicalUrl"
                                    >

                                    <meta itemprop="position" content="3">
                                </li>

                            </ol>
                        </nav>

                        <!-- Images -->
                        <div
                            v-if="hasRubricImages"
                            class="flex items-center justify-center"
                        >
                            <div class="w-full">
                                <ImageGalleryMain
                                    :images="rubricImages"
                                    :alt="rubricTitle"
                                    rounded-class="rounded-lg"
                                    shadow-class="shadow-lg shadow-gray-400 dark:shadow-gray-700"
                                    img-class="w-full h-full object-cover"
                                />
                            </div>
                        </div>

                        <!-- Header -->
                        <div class="flex items-center justify-between gap-1">

                            <div
                                :title="t('articles')"
                                class="flex items-center justify-center gap-1"
                            >
                                <svg
                                    class="h-4 w-4 text-slate-600/85 dark:text-slate-200/85"
                                    viewBox="0 0 384 512"
                                    fill="currentColor"
                                >
                                    <path
                                        d="M288 248v28c0 6.6-5.4 12-12 12H108c-6.6 0-12-5.4-12-12v-28c0-6.6 5.4-12 12-12h168c6.6 0 12 5.4 12 12zm-12 72H108c-6.6 0-12 5.4-12 12v28c0 6.6 5.4 12 12 12h168c6.6 0 12-5.4 12-12v-28c0-6.6-5.4-12-12-12h168c6.6 0 12 5.4 12 12z"
                                    />
                                </svg>

                                <span class="text-sm text-gray-500">
                                    {{ articlesCount }}
                                </span>
                            </div>

                            <div class="my-3 flex flex-wrap items-center justify-center gap-3 title">
                                <span
                                    v-if="hasSvgIcon"
                                    class="flex"
                                    v-html="rubric.icon"
                                />

                                <h1
                                    itemprop="headline"
                                    class="text-2xl font-bold"
                                >
                                    {{ rubricTitle }}
                                </h1>
                            </div>

                            <div
                                :title="t('views')"
                                class="flex items-center justify-center gap-1"
                            >
                                <svg
                                    class="h-4 w-4 text-slate-600/85 dark:text-slate-200/85"
                                    viewBox="0 0 576 512"
                                    fill="currentColor"
                                >
                                    <path
                                        d="M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738z"
                                    />
                                </svg>

                                <span class="text-sm text-gray-500">
                                    {{ rubric.views || 0 }}
                                </span>
                            </div>
                        </div>

                        <!-- Description -->
                        <div
                            v-if="rubricDescription"
                            itemprop="abstract"
                            class="mt-1 mb-3 text-sm subtitle text-center"
                        >
                            {{ rubricDescription }}
                        </div>

                        <!-- Toolbar -->
                        <EntityPageToolbar
                            v-if="hasArticles"
                            v-model="qArticles"
                            v-model:view-mode="viewMode"
                            v-model:sort-value="sortArticles"
                            :found="effectiveArticlesFound"
                            :sort-options="articleSortOptions"
                            :default-sort="DEFAULT_SORT"
                            :found-label="t('articles')"
                            :search-placeholder="t('searchByName')"
                            @submit="applyArticleFilters"
                            @reset="resetArticleFilters"
                        />

                        <div ref="articlesScrollTarget"></div>

                        <!-- Empty search -->
                        <div
                            v-if="hasArticles && displayedArticles.length === 0"
                            class="mt-6 text-center text-slate-700 dark:text-slate-300"
                        >
                            {{ t('noData') }}
                        </div>

                        <!-- Articles -->
                        <div v-if="displayedArticles.length">
                            <RubricArticleGrid
                                v-if="viewMode === 'grid'"
                                :articles="displayedArticles"
                                :cols="rubricGridCols"
                            />

                            <RubricArticleRows
                                v-else
                                :articles="displayedArticles"
                            />
                        </div>

                        <!-- Server pagination -->
                        <Pagination
                            v-if="useServerProcessing && hasArticles && lastPage > 1"
                            :current-page="currentPage"
                            :last-page="lastPage"
                            :found="articlesFound"
                            @prev="goPrev"
                            @next="goNext"
                            @go="goToPage"
                        />

                        <!-- Frontend pagination -->
                        <FrontendPagination
                            v-if="!useServerProcessing && effectiveArticlesFound > perPageArticles"
                            v-model:currentPage="frontendCurrentPage"
                            :items-per-page="perPageArticles"
                            :total-items="effectiveArticlesFound"
                        />

                        <!-- Child rubrics -->
                        <div v-if="childRubrics.length">
                            <div
                                class="mt-4 flex flex-wrap items-center justify-center gap-3
                                       text-slate-700/85 dark:text-slate-300/85"
                            >
                                <svg
                                    class="h-8 w-8 opacity-70"
                                    fill="currentColor"
                                    viewBox="0 0 640 512"
                                >
                                    <path
                                        d="M622.34 153.2L343.4 67.5c-15.2-4.67-31.6-4.67-46.79 0L17.66 153.2c-23.54 7.23-23.54 38.36 0 45.59l278.95 85.7c20.35 6.25 37.73 2.78 46.79 0l278.95-85.7c23.55-7.24 23.55-38.36 0-45.6zM352.79 315.09c-28.53 8.76-52.84 3.92-65.59 0l-145.02-44.55L128 384c0 35.35 85.96 64 192 64s192-28.65 192-64l-14.18-113.47-145.03 44.56z"
                                    />
                                </svg>

                                <h2 class="text-xl font-semibold">
                                    {{ t('subheadings') }}
                                </h2>
                            </div>

                            <div class="mt-6">
                                <RubricGrid
                                    v-if="viewMode === 'grid'"
                                    :rubrics="childRubrics"
                                    :cols="rubricGridCols"
                                />

                                <RubricRows
                                    v-else
                                    :rubrics="childRubrics"
                                />
                            </div>
                        </div>

                        <SectionVideoList :videos="mainVideos" />
                        <SectionBanners :banners="mainBanners" />
                    </div>
                </article>

                <!-- Right sidebar -->
                <aside
                    v-if="showRight"
                    class="shrink-0 lg:mt-28 transition-all duration-300 overflow-hidden"
                    :class="rightCollapsed ? 'lg:w-10' : 'lg:w-64'"
                >
                    <RightSidebar
                        :collapsed="rightCollapsed"
                        @collapsed="setRightCollapsed"
                    />
                </aside>

            </main>
        </div>

        <FooterBlog />
        <Progress />

        <!-- Admin bottom panel -->
        <PublicAdminBottomPanel
            v-if="isAdmin"
            setting-key="publicBlogArticlesProcessingMode"
            :mode="publicBlogArticlesProcessingMode"
            :use-server-processing="useServerProcessing"
            :total="articlesCount"
        />
    </DefaultLayout>
</template>
