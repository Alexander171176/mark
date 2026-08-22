<script setup>
/**
 * Страница списка рубрик блога.
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

import RubricGrid from '@/Components/Public/Default/Blog/BlogRubric/RubricGrid.vue'
import RubricRows from '@/Components/Public/Default/Blog/BlogRubric/RubricRows.vue'

import SectionVideoList from '@/Components/Public/Default/Blog/BlogVideo/SectionVideoList.vue'
import SectionBanners from '@/Components/Public/Default/Blog/BlogBanner/SectionBanners.vue'

import PublicAdminBottomPanel
    from '@/Components/Admin/UI/PublicAdminPanel/PublicAdminBottomPanel.vue'

const { t } = useI18n()
const page = usePage()

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

    publicBlogRubricsProcessingMode: {
        type: String,
        default: 'server',
    },

    useServerProcessing: {
        type: Boolean,
        default: false,
    },

    title: { type: String, default: '' },
    canLogin: { type: Boolean, default: false },
    canRegister: { type: Boolean, default: false },

    rubricTree: {
        type: Array,
        default: () => [],
    },

    rubrics: {
        type: [Array, Object],
        default: () => [],
    },

    rubricsCount: {
        type: Number,
        default: 0,
    },

    rubricsFound: {
        type: Number,
        default: 0,
    },

    filters: {
        type: Object,
        default: () => ({}),
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

const rubricsData = computed(() =>
    normalizeList(props.rubrics)
)

const mainVideos = computed(() =>
    normalizeList(props.mainVideos)
)

const mainBanners = computed(() =>
    normalizeList(props.mainBanners)
)

/* ======================== Rubric helpers ======================== */

const getRubricTitle = (rubric) =>
    rubric?.translation?.title || ''

const getRubricShort = (rubric) =>
    rubric?.translation?.short || ''

/* ======================== SEO ======================== */

const seoTitle = computed(() =>
    props.seo?.title
    || t('rubrics')
)

const seoDescription = computed(() =>
    props.seo?.description
    || ''
)

const seoKeywords = computed(() =>
    props.seo?.keywords
    || ''
)

const contentLocale = computed(() =>
    props.locale || 'ru'
)

const ogLocale = computed(() =>
    contentLocale.value === 'ru'
        ? 'ru_RU'
        : contentLocale.value
)

const canonicalUrl = computed(() =>
    String(
        route('public.blogRubrics.index')
    )
)

const dcSubject = computed(() =>
    seoKeywords.value
    || seoTitle.value
)

/**
 * Первое доступное изображение рубрики
 * используем для Open Graph.
 */
const seoImage = computed(() => {
    for (const rubric of rubricsData.value) {
        const images = normalizeList(
            rubric?.images
        )

        const image = images[0]

        const url =
            image?.webp_url
            || image?.image_url
            || image?.thumb_url
            || image?.url
            || ''

        if (url) return url
    }

    return ''
})

/* ======================== Filters ======================== */

const q = ref(
    String(
        props.filters?.q
        ?? ''
    )
)

const DEFAULT_SORT = 'sortAsc'

const sort = ref(
    String(
        props.filters?.sort
        ?? DEFAULT_SORT
    )
)

/**
 * Только backend setting.
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

const rubricSortOptions = [
    { value: 'sortAsc', label: `${t('sortNumber')} 0→9` },
    { value: 'sortDesc', label: `${t('sortNumber')} 9→0` },

    { value: 'idDesc', label: t('idDesc') },
    { value: 'idAsc', label: t('idAsc') },

    { value: 'titleAsc', label: `${t('title')} A→Z` },
    { value: 'titleDesc', label: `${t('title')} Z→A` },

    { value: 'viewsDesc', label: `${t('views')} 9→0` },
    { value: 'viewsAsc', label: `${t('views')} 0→9` },

    { value: 'articlesDesc', label: `${t('articles')} 9→0` },
    { value: 'articlesAsc', label: `${t('articles')} 0→9` },

    { value: 'dateDesc', label: `${t('createdAt')} ↓` },
    { value: 'dateAsc', label: `${t('createdAt')} ↑` },
]

/* ======================== View mode ======================== */

const VIEW_KEY =
    'public_blog_rubrics_view'

const viewMode = ref(
    String(
        props.filters?.view
        || 'grid'
    )
)

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

const filteredRubrics = computed(() => {
    if (props.useServerProcessing) {
        return rubricsData.value
    }

    const query =
        normalizeText(q.value)

    if (!query) {
        return rubricsData.value
    }

    return rubricsData.value.filter(
        (rubric) => [
            rubric?.id,
            getRubricTitle(rubric),
            getRubricShort(rubric),
            rubric?.url,
            rubric?.owner?.name,
        ].some((value) =>
            normalizeText(value).includes(query)
        )
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

const sortedRubrics = computed(() => {
    if (props.useServerProcessing) {
        return filteredRubrics.value
    }

    const list = [
        ...filteredRubrics.value,
    ]

    switch (sort.value) {
        case 'sortAsc':
            list.sort((a, b) =>
                compareNumber(a?.sort, b?.sort)
                || compareNumber(b?.id, a?.id)
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
                    getRubricTitle(a),
                    getRubricTitle(b)
                )
            )
            break

        case 'titleDesc':
            list.sort((a, b) =>
                compareText(
                    getRubricTitle(b),
                    getRubricTitle(a)
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

        case 'articlesAsc':
            list.sort((a, b) =>
                compareNumber(
                    a?.articles_count,
                    b?.articles_count
                )
            )
            break

        case 'articlesDesc':
            list.sort((a, b) =>
                compareNumber(
                    b?.articles_count,
                    a?.articles_count
                )
            )
            break

        case 'dateAsc':
            list.sort((a, b) =>
                safeDate(a?.created_at)
                - safeDate(b?.created_at)
            )
            break

        case 'dateDesc':
            list.sort((a, b) =>
                safeDate(b?.created_at)
                - safeDate(a?.created_at)
            )
            break
    }

    return list
})

/* ======================== Frontend pagination ======================== */

const frontendCurrentPage = ref(1)

const {
    targetRef: scrollTarget,
    scrollToTarget,
} = useSmoothScrollTo({
    offset: 80,
    duration: 1200,
})

watch(
    [q, sort],
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
            scrollToTarget()
        }
    }
)

const effectiveRubricsFound = computed(() =>
    props.useServerProcessing
        ? safeNumber(
            props.rubricsFound
        )
        : sortedRubrics.value.length
)

const frontendPaginatedRubrics = computed(() => {
    if (props.useServerProcessing) {
        return rubricsData.value
    }

    const start =
        (
            frontendCurrentPage.value - 1
        ) * perPage.value

    return sortedRubrics.value.slice(
        start,
        start + perPage.value
    )
})

const displayedRubrics = computed(() =>
    props.useServerProcessing
        ? rubricsData.value
        : frontendPaginatedRubrics.value
)

/* ======================== Server pagination ======================== */

const currentPage = computed(() =>
    Number(
        props.rubrics?.meta?.current_page
        ?? props.rubrics?.current_page
        ?? 1
    ) || 1
)

const lastPage = computed(() =>
    Number(
        props.rubrics?.meta?.last_page
        ?? props.rubrics?.last_page
        ?? 1
    ) || 1
)

const reloadRubrics = (
    pageNumber = 1
) => {
    if (!props.useServerProcessing) {
        return
    }

    router.get(
        canonicalUrl.value,
        {
            q:
                q.value
                || undefined,

            sort:
                sort.value
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

const goToPage = (pageNumber) => {
    const target = Math.min(
        Math.max(
            1,
            Number(pageNumber) || 1
        ),
        lastPage.value
    )

    reloadRubrics(target)
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

const applyFilters = () => {
    if (props.useServerProcessing) {
        reloadRubrics(1)
    } else {
        frontendCurrentPage.value = 1
    }
}

const resetFilters = () => {
    if (props.useServerProcessing) {
        reloadRubrics(1)
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
 * Начальное состояние специально true.
 *
 * После mounted восстанавливаем localStorage.
 * Вместе с transition-all получаем
 * естественную анимацию сайдбаров.
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

        <link
            rel="canonical"
            :href="canonicalUrl"
        >

        <!-- Open Graph -->
        <meta property="og:type" content="website">
        <meta property="og:title" :content="seoTitle">

        <meta
            v-if="seoDescription"
            property="og:description"
            :content="seoDescription"
        >

        <meta property="og:url" :content="canonicalUrl">
        <meta property="og:locale" :content="ogLocale">

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

        <meta name="twitter:title" :content="seoTitle">

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
        <meta name="DC.title" :content="seoTitle">

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

        <meta name="DC.language" :content="contentLocale">
        <meta name="DC.identifier" :content="canonicalUrl">
        <meta name="DC.type" content="Collection">
        <meta name="DC.format" content="text/html">
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
                    <meta itemprop="name" :content="seoTitle">

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

                    <meta itemprop="url" :content="canonicalUrl">
                    <meta itemprop="inLanguage" :content="contentLocale">

                    <div class="mx-auto max-w-6xl">

                        <!-- Breadcrumbs -->
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
                                    aria-current="page"
                                >
                                    <span class="mx-2 breadcrumbs">/</span>

                                    <span
                                        itemprop="name"
                                        class="breadcrumbs"
                                    >
                                        {{ t('rubrics') }}
                                    </span>

                                    <meta
                                        itemprop="item"
                                        :content="canonicalUrl"
                                    >

                                    <meta itemprop="position" content="2">
                                </li>
                            </ol>
                        </nav>

                        <!-- Header -->
                        <div class="my-3 flex flex-wrap items-center justify-center gap-3 title">
                            <svg
                                class="h-5 w-5"
                                viewBox="0 0 512 512"
                                fill="currentColor"
                            >
                                <path
                                    d="M489.2 287.9h-27.4c-2.6 0-4.6 2-4.6 4.6v32h-36.6V146.2c0-2.6-2-4.6-4.6-4.6h-27.4c-2.6 0-4.6 2-4.6 4.6v32h-36.6v-32c0-2.6-2-4.6-4.6-4.6h-27.4c-2.6 0-4.6 2-4.6 4.6v32h-36.6v-32c0-6-8-4.6-11.7-4.6v-38c8.3-2 17.1-3.4 25.7-3.4 10.9 0 20.9 4.3 31.4 4.3 4.6 0 27.7-1.1 27.7-8v-60c0-2.6-2-4.6-4.6-4.6-5.1 0-15.1 4.3-24 4.3-9.7 0-20.9-4.3-32.6-4.3-8 0-16 1.1-23.7 2.9v-4.9c5.4-2.6 9.1-8.3 9.1-14.3 0-20.7-31.4-20.8-31.4 0 0 6 3.7 11.7 9.1 14.3v111.7c-3.7 0-11.7-1.4-11.7 4.6v32h-36.6v-32c0-2.6-2-4.6-4.6-4.6h-27.4c-2.6 0-4.6 2-4.6 4.6v32H128v-32c0-2.6-2-4.6-4.6-4.6H96c-2.6 0-4.6 2-4.6 4.6v178.3H54.8v-32c0-2.6-2-4.6-4.6-4.6H22.8c-2.6 0-4.6 2-4.6 4.6V512h182.9v-96c0-72.6 109.7-72.6 109.7 0v96h182.9V292.5c.1-2.6-1.9-4.6-4.5-4.6z"
                                />
                            </svg>

                            <h1
                                itemprop="headline"
                                class="text-2xl font-bold"
                            >
                                {{ t('rubrics') }}
                            </h1>
                        </div>

                        <div
                            v-if="seoDescription"
                            itemprop="abstract"
                            class="my-1 text-sm subtitle text-center"
                        >
                            {{ seoDescription }}
                        </div>

                        <!-- One toolbar -->
                        <EntityPageToolbar
                            v-model="q"
                            v-model:view-mode="viewMode"
                            v-model:sort-value="sort"
                            :found="effectiveRubricsFound"
                            :sort-options="rubricSortOptions"
                            :default-sort="DEFAULT_SORT"
                            :found-label="t('rubrics')"
                            :search-placeholder="t('searchByName')"
                            @submit="applyFilters"
                            @reset="resetFilters"
                        />

                        <div ref="scrollTarget"></div>

                        <div
                            v-if="displayedRubrics.length === 0"
                            class="mt-6 text-center text-slate-700 dark:text-slate-300"
                        >
                            {{ t('noData') }}
                        </div>

                        <div v-else>
                            <RubricGrid
                                v-if="viewMode === 'grid'"
                                :rubrics="displayedRubrics"
                                :cols="rubricGridCols"
                            />

                            <RubricRows
                                v-else
                                :rubrics="displayedRubrics"
                            />
                        </div>

                        <Pagination
                            v-if="useServerProcessing && lastPage > 1"
                            :current-page="currentPage"
                            :last-page="lastPage"
                            :found="rubricsFound"
                            @prev="goPrev"
                            @next="goNext"
                            @go="goToPage"
                        />

                        <FrontendPagination
                            v-if="!useServerProcessing && effectiveRubricsFound > perPage"
                            v-model:currentPage="frontendCurrentPage"
                            :items-per-page="perPage"
                            :total-items="effectiveRubricsFound"
                        />

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

        <PublicAdminBottomPanel
            v-if="isAdmin"
            setting-key="publicBlogRubricsProcessingMode"
            :mode="publicBlogRubricsProcessingMode"
            :use-server-processing="useServerProcessing"
            :total="rubricsCount"
        />
    </DefaultLayout>
</template>
