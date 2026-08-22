<script setup>
import { computed, onMounted, ref, watch } from 'vue'
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
import Pagination from '@/Components/Public/Default/Pagination/Pagination.vue'
import FrontendPagination from '@/Components/Public/Default/Pagination/FrontendPagination.vue'

import SectionVideoList from '@/Components/Public/Default/Blog/BlogVideo/SectionVideoList.vue'
import SectionBanners from '@/Components/Public/Default/Blog/BlogBanner/SectionBanners.vue'

import TrackGrid from '@/Components/Public/Default/School/SchoolTrack/TrackGrid.vue'
import TrackRows from '@/Components/Public/Default/School/SchoolTrack/TrackRows.vue'

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

    publicSchoolTracksProcessingMode: {
        type: String,
        default: 'server',
    },

    useServerProcessing: {
        type: Boolean,
        default: false,
    },

    title: {
        type: String,
        default: '',
    },

    canLogin: {
        type: Boolean,
        default: false,
    },

    canRegister: {
        type: Boolean,
        default: false,
    },

    tracks: {
        type: [Array, Object],
        default: () => [],
    },

    tracksCount: {
        type: Number,
        default: 0,
    },

    tracksFound: {
        type: Number,
        default: 0,
    },

    filters: {
        type: Object,
        default: () => ({}),
    },

    trackTree: {
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

/* ======================== Page ======================== */

const siteSettings = computed(() =>
    page.props?.siteSettings ?? {}
)

const isAdmin = computed(() =>
    page.props?.isAdmin === true
)

const trackTree = computed(() =>
    Array.isArray(props.trackTree)
        ? props.trackTree
        : []
)

const mainVideos = computed(() =>
    normalizeList(props.mainVideos)
)

const mainBanners = computed(() =>
    normalizeList(props.mainBanners)
)

/* ======================== Tracks ======================== */

const tracksData = computed(() =>
    normalizeList(props.tracks)
)

const getTrackTitle = (track) =>
    track?.translation?.name || ''

const getTrackShort = (track) =>
    track?.translation?.short || ''

const getTrackDescription = (track) =>
    track?.translation?.description || ''

const getTrackSlug = (track) =>
    track?.slug || ''

/* ======================== SEO ======================== */

const seoTitle = computed(() =>
    props.seo?.title || t('learningCategories')
)

const seoDescription = computed(() =>
    props.seo?.description || t('learningCategories')
)

const seoKeywords = computed(() =>
    props.seo?.keywords || ''
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
    String(route('public.schoolTracks.index'))
)

const seoImage = computed(() => {
    for (const track of tracksData.value) {
        const images = Array.isArray(track?.images)
            ? track.images
            : []

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

const dcSubject = computed(() =>
    seoKeywords.value || seoTitle.value
)

/* ======================== Filters ======================== */

const q = ref(
    String(props.filters?.q ?? '')
)

const DEFAULT_SORT = 'sortAsc'

const sort = ref(
    String(props.filters?.sort ?? DEFAULT_SORT)
)

/**
 * Количество элементов приходит только
 * от backend-настройки publicSchoolTracksPerPage.
 */
const perPage = computed(() => {
    const value = Number(
        props.filters?.per_page
    )

    return Number.isFinite(value) && value > 0
        ? value
        : 12
})

const trackSortOptions = [
    { value: 'sortAsc', label: `${t('sortNumber')} 0→9` },
    { value: 'sortDesc', label: `${t('sortNumber')} 9→0` },

    { value: 'idDesc', label: t('idDesc') },
    { value: 'idAsc', label: t('idAsc') },

    { value: 'nameAsc', label: `${t('title')} A→Z` },
    { value: 'nameDesc', label: `${t('title')} Z→A` },

    { value: 'slugAsc', label: 'URL A→Z' },
    { value: 'slugDesc', label: 'URL Z→A' },

    { value: 'viewsDesc', label: `${t('views')} 9→0` },
    { value: 'viewsAsc', label: `${t('views')} 0→9` },

    { value: 'likesDesc', label: `${t('likes')} 9→0` },
    { value: 'likesAsc', label: `${t('likes')} 0→9` },

    { value: 'childrenDesc', label: `${t('subheadings')} 9→0` },
    { value: 'childrenAsc', label: `${t('subheadings')} 0→9` },

    { value: 'coursesDesc', label: `${t('courses')} 9→0` },
    { value: 'coursesAsc', label: `${t('courses')} 0→9` },

    { value: 'imagesDesc', label: `${t('images')} 9→0` },
    { value: 'imagesAsc', label: `${t('images')} 0→9` },

    { value: 'dateDesc', label: t('sortNewestFirst') },
    { value: 'dateAsc', label: t('sortOldestFirst') },
]

/* ======================== View ======================== */

const VIEW_KEY = 'public_school_tracks_view'

const viewMode = ref(
    String(props.filters?.view || 'grid')
)

onMounted(() => {
    try {
        const storedView = localStorage.getItem(VIEW_KEY)

        if (['grid', 'rows'].includes(storedView)) {
            viewMode.value = storedView
        }
    } catch {
        //
    }
})

watch(viewMode, (value) => {
    try {
        localStorage.setItem(VIEW_KEY, value)
    } catch {
        //
    }
})

/* ======================== Frontend search ======================== */

const filteredTracks = computed(() => {
    if (props.useServerProcessing) {
        return tracksData.value
    }

    const query = normalizeText(q.value)

    if (!query) {
        return tracksData.value
    }

    return tracksData.value.filter((track) => {
        const parentTitle =
            track?.parent?.translation?.name
            || track?.parent?.name
            || ''

        return [
            track?.id,
            getTrackTitle(track),
            getTrackShort(track),
            getTrackDescription(track),
            getTrackSlug(track),
            parentTitle,
        ].some((value) =>
            normalizeText(value).includes(query)
        )
    })
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

const sortedTracks = computed(() => {
    if (props.useServerProcessing) {
        return filteredTracks.value
    }

    const list = [...filteredTracks.value]

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

        case 'nameAsc':
        case 'titleAsc':
            list.sort((a, b) =>
                compareText(
                    getTrackTitle(a),
                    getTrackTitle(b)
                )
            )
            break

        case 'nameDesc':
        case 'titleDesc':
            list.sort((a, b) =>
                compareText(
                    getTrackTitle(b),
                    getTrackTitle(a)
                )
            )
            break

        case 'slugAsc':
            list.sort((a, b) =>
                compareText(
                    getTrackSlug(a),
                    getTrackSlug(b)
                )
            )
            break

        case 'slugDesc':
            list.sort((a, b) =>
                compareText(
                    getTrackSlug(b),
                    getTrackSlug(a)
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

        case 'childrenAsc':
            list.sort((a, b) =>
                compareNumber(
                    a?.children_count,
                    b?.children_count
                )
            )
            break

        case 'childrenDesc':
            list.sort((a, b) =>
                compareNumber(
                    b?.children_count,
                    a?.children_count
                )
            )
            break

        case 'coursesAsc':
            list.sort((a, b) =>
                compareNumber(
                    a?.courses_count,
                    b?.courses_count
                )
            )
            break

        case 'coursesDesc':
            list.sort((a, b) =>
                compareNumber(
                    b?.courses_count,
                    a?.courses_count
                )
            )
            break

        case 'imagesAsc':
            list.sort((a, b) =>
                compareNumber(
                    a?.images_count,
                    b?.images_count
                )
            )
            break

        case 'imagesDesc':
            list.sort((a, b) =>
                compareNumber(
                    b?.images_count,
                    a?.images_count
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

watch([q, sort], () => {
    if (!props.useServerProcessing) {
        frontendCurrentPage.value = 1
    }
})

watch(frontendCurrentPage, () => {
    if (!props.useServerProcessing) {
        scrollToTarget()
    }
})

const effectiveTracksFound = computed(() =>
    props.useServerProcessing
        ? Number(props.tracksFound ?? 0)
        : sortedTracks.value.length
)

const frontendPaginatedTracks = computed(() => {
    if (props.useServerProcessing) {
        return tracksData.value
    }

    const start =
        (frontendCurrentPage.value - 1)
        * perPage.value

    return sortedTracks.value.slice(
        start,
        start + perPage.value
    )
})

const displayedTracks = computed(() =>
    props.useServerProcessing
        ? tracksData.value
        : frontendPaginatedTracks.value
)

/* ======================== Server pagination ======================== */

const currentPage = computed(() =>
    Number(
        props.tracks?.meta?.current_page
        ?? props.tracks?.current_page
        ?? 1
    ) || 1
)

const lastPage = computed(() =>
    Number(
        props.tracks?.meta?.last_page
        ?? props.tracks?.last_page
        ?? 1
    ) || 1
)

const reloadTracks = (pageNumber = 1) => {
    if (!props.useServerProcessing) {
        return
    }

    router.get(
        route('public.schoolTracks.index'),
        {
            q: q.value || undefined,
            sort: sort.value || undefined,
            page: pageNumber,
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

    reloadTracks(target)
}

const goPrev = () => {
    if (currentPage.value > 1) {
        goToPage(
            currentPage.value - 1
        )
    }
}

const goNext = () => {
    if (currentPage.value < lastPage.value) {
        goToPage(
            currentPage.value + 1
        )
    }
}

/* ======================== Toolbar ======================== */

const applyFilters = () => {
    if (props.useServerProcessing) {
        reloadTracks(1)
    } else {
        frontendCurrentPage.value = 1
    }
}

const resetFilters = () => {
    if (props.useServerProcessing) {
        reloadTracks(1)
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

const leftCollapsed = ref(true)
const rightCollapsed = ref(true)

const readStoredBoolean = (
    key,
    fallback = true
) => {
    try {
        const value = localStorage.getItem(key)

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
 * Оба открыты  → 2.
 * Один открыт  → 3.
 * Оба закрыты  → 4.
 */
const trackGridCols = computed(() => {
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
</script>

<template>
    <Head>
        <!-- Основные SEO -->
        <title>{{ seoTitle }}</title>

        <meta
            name="title"
            :content="seoTitle"
        >

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
            :content="
                seoImage
                    ? 'summary_large_image'
                    : 'summary'
            "
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
            :content="contentLocale"
        >

        <meta
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
                    class="shrink-0 mt-12 lg:mt-28 transition-all duration-300"
                    :class="
                        leftCollapsed
                            ? 'lg:w-10'
                            : 'lg:w-64'
                    "
                >
                    <LeftSidebarSchool
                        :track-tree="trackTree"
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
                    <!-- Schema.org -->
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
                        itemprop="url"
                        :content="canonicalUrl"
                    >

                    <meta
                        itemprop="inLanguage"
                        :content="contentLocale"
                    >

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

                                    <meta
                                        itemprop="position"
                                        content="1"
                                    >
                                </li>

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
                                        :href="route('public.schoolInstructors.index')"
                                        class="breadcrumb-link hover:underline"
                                    >
                                        <span itemprop="name">
                                            {{ t('instructors') }}
                                        </span>
                                    </Link>

                                    <meta
                                        itemprop="position"
                                        content="2"
                                    >
                                </li>

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

                                    <span
                                        itemprop="name"
                                        class="breadcrumbs"
                                    >
                                        {{ t('tracks') }}
                                    </span>

                                    <meta
                                        itemprop="item"
                                        :content="canonicalUrl"
                                    >

                                    <meta
                                        itemprop="position"
                                        content="3"
                                    >
                                </li>
                            </ol>
                        </nav>

                        <!-- Header -->
                        <div class="my-3 flex flex-wrap items-center justify-center gap-3 title">
                            <svg
                                class="shrink-0 h-5 w-5 text-slate-600/85 dark:text-slate-200/85"
                                fill="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M23.58.424A1,1,0,0,0,22.819.13C8.791.862,3.609,13.358,3.559,13.484a1,1,0,0,0,.22,1.08l5.657,5.657a1,1,0,0,0,1.085.218c.125-.051,12.554-5.291,13.348-19.253A1,1,0,0,0,23.58.424Zm-8.166,10.99a2,2,0,1,1,0-2.828A2,2,0,0,1,15.414,11.414Z"
                                />
                                <path
                                    d="M1.113,18.844a2.844,2.844,0,1,1,4.022,4.022C4.024,23.977,0,24,0,24S0,19.954,1.113,18.844Z"
                                />
                                <path
                                    d="M10.357,2.341A8.911,8.911,0,0,0,2.522,4.825a9.084,9.084,0,0,0-1.384,1.8,1,1,0,0,0,.155,1.215l1.989,1.99A26.623,26.623,0,0,1,10.357,2.341Z"
                                />
                                <path
                                    d="M21.659,13.643a8.911,8.911,0,0,1-2.484,7.835,9.084,9.084,0,0,1-1.8,1.384,1,1,0,0,1-1.215-.155l-1.99-1.989A26.623,26.623,0,0,0,21.659,13.643Z"
                                />
                            </svg>

                            <h1
                                itemprop="headline"
                                class="text-2xl font-bold"
                            >
                                {{ t('tracks') }}
                            </h1>
                        </div>

                        <!-- Description -->
                        <div
                            v-if="seoDescription"
                            itemprop="abstract"
                            class="my-1 text-sm subtitle text-center"
                        >
                            {{ seoDescription }}
                        </div>

                        <!-- Toolbar -->
                        <EntityPageToolbar
                            v-model="q"
                            v-model:view-mode="viewMode"
                            v-model:sort-value="sort"
                            :found="effectiveTracksFound"
                            :sort-options="trackSortOptions"
                            :default-sort="DEFAULT_SORT"
                            :found-label="t('tracks')"
                            :search-placeholder="t('searchByName')"
                            @submit="applyFilters"
                            @reset="resetFilters"
                        />

                        <div ref="scrollTarget"></div>

                        <!-- Empty -->
                        <div
                            v-if="displayedTracks.length === 0"
                            class="mt-6 text-center text-slate-700 dark:text-slate-300"
                        >
                            {{ t('noData') }}
                        </div>

                        <!-- Tracks -->
                        <div v-else>
                            <TrackGrid
                                v-if="viewMode === 'grid'"
                                :tracks="displayedTracks"
                                :cols="trackGridCols"
                            />

                            <TrackRows
                                v-else
                                :tracks="displayedTracks"
                            />
                        </div>

                        <!-- Server pagination -->
                        <Pagination
                            v-if="useServerProcessing && lastPage > 1"
                            :current-page="currentPage"
                            :last-page="lastPage"
                            :found="tracksFound"
                            @prev="goPrev"
                            @next="goNext"
                            @go="goToPage"
                        />

                        <!-- Frontend pagination -->
                        <FrontendPagination
                            v-if="!useServerProcessing && effectiveTracksFound > perPage"
                            v-model:currentPage="frontendCurrentPage"
                            :items-per-page="perPage"
                            :total-items="effectiveTracksFound"
                        />

                        <!-- Main content -->
                        <SectionVideoList
                            :videos="mainVideos"
                        />

                        <SectionBanners
                            :banners="mainBanners"
                        />
                    </div>
                </article>

                <!-- Right sidebar -->
                <aside
                    v-if="showRight"
                    class="shrink-0 lg:mt-28 transition-all duration-300"
                    :class="
                        rightCollapsed
                            ? 'lg:w-10'
                            : 'lg:w-64'
                    "
                >
                    <RightSidebarSchool
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
            setting-key="publicSchoolTracksProcessingMode"
            :mode="publicSchoolTracksProcessingMode"
            :use-server-processing="useServerProcessing"
            :total="tracksCount"
        />
    </DefaultLayout>
</template>
