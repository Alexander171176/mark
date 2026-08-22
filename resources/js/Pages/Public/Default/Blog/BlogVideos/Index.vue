<script setup>
/**
 * Страница списка видео блога.
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

import VideoGrid from '@/Components/Public/Default/Blog/BlogVideo/VideoGrid.vue'
import VideoRows from '@/Components/Public/Default/Blog/BlogVideo/VideoRows.vue'

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

    publicBlogVideosProcessingMode: {
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

    videos: {
        type: [Array, Object],
        default: () => [],
    },

    videosCount: {
        type: Number,
        default: 0,
    },

    videosFound: {
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

const videosData = computed(() =>
    normalizeList(props.videos)
)

const mainVideos = computed(() =>
    normalizeList(props.mainVideos)
)

const mainBanners = computed(() =>
    normalizeList(props.mainBanners)
)

/* ======================== Video helpers ======================== */

const getVideoTitle = (video) =>
    video?.translation?.title || ''

const getVideoShort = (video) =>
    video?.translation?.short || ''

const getVideoPseudonym = (video) =>
    video?.translation?.pseudonym || ''

const getVideoAuthor = (video) =>
    getVideoPseudonym(video)
    || video?.owner?.name
    || ''

const getVideoDuration = (video) =>
    safeNumber(
        video?.duration
        ?? video?.duration_seconds
    )

/* ======================== SEO ======================== */

const seoTitle = computed(() =>
    props.seo?.title
    || t('videos')
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
        route('public.blogVideos.index')
    )
)

const dcSubject = computed(() =>
    seoKeywords.value
    || seoTitle.value
)

/**
 * Первое доступное изображение
 * видео используем как social preview.
 */
const seoImage = computed(() => {
    for (const video of videosData.value) {
        const images =
            normalizeList(video?.images)

        const image =
            images[0]

        const url =
            image?.webp_url
            || image?.image_url
            || image?.thumb_url
            || image?.url
            || ''

        if (url) {
            return url
        }
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

const DEFAULT_SORT =
    'sortAsc'

const sort = ref(
    String(
        props.filters?.sort
        ?? DEFAULT_SORT
    )
)

/**
 * Единственный источник истины —
 * PublicSettingsService backend.
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

const videoSortOptions = [
    { value: 'sortAsc', label: `${t('sortNumber')} 0→9` },
    { value: 'sortDesc', label: `${t('sortNumber')} 9→0` },

    { value: 'titleAsc', label: `${t('title')} A→Z` },
    { value: 'titleDesc', label: `${t('title')} Z→A` },

    { value: 'viewsDesc', label: `${t('views')} 9→0` },
    { value: 'viewsAsc', label: `${t('views')} 0→9` },

    { value: 'likesDesc', label: `${t('likes')} 9→0` },
    { value: 'likesAsc', label: `${t('likes')} 0→9` },

    { value: 'commentsDesc', label: `${t('comments')} 9→0` },
    { value: 'commentsAsc', label: `${t('comments')} 0→9` },

    { value: 'durationDesc', label: `${t('duration')} 9→0` },
    { value: 'durationAsc', label: `${t('duration')} 0→9` },

    { value: 'publishedAtDesc', label: `${t('publishedAt')} ↓` },
    { value: 'publishedAtAsc', label: `${t('publishedAt')} ↑` },
]

/* ======================== View mode ======================== */

const VIEW_KEY =
    'public_blog_videos_view'

const viewMode = ref(
    String(
        props.filters?.view
        || 'grid'
    )
)

/**
 * localStorage читаем только после mount.
 */
onMounted(() => {
    try {
        const storedView =
            localStorage.getItem(VIEW_KEY)

        if (
            storedView === 'grid'
            || storedView === 'rows'
        ) {
            viewMode.value =
                storedView
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

const filteredVideos = computed(() => {
    if (props.useServerProcessing) {
        return videosData.value
    }

    const query =
        normalizeText(q.value)

    if (!query) {
        return videosData.value
    }

    return videosData.value.filter(
        (video) => [
            video?.id,
            getVideoTitle(video),
            getVideoShort(video),
            getVideoPseudonym(video),
            video?.url,
            video?.source_type,
            video?.external_video_id,
            getVideoAuthor(video),
            video?.owner?.name,
            video?.owner?.email,
        ].some((value) =>
            normalizeText(value)
                .includes(query)
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

const sortedVideos = computed(() => {
    if (props.useServerProcessing) {
        return filteredVideos.value
    }

    const list = [
        ...filteredVideos.value,
    ]

    switch (sort.value) {
        case 'sortAsc':
            list.sort((a, b) =>
                compareNumber(
                    a?.sort,
                    b?.sort
                )
                ||
                compareNumber(
                    b?.id,
                    a?.id
                )
            )
            break

        case 'sortDesc':
            list.sort((a, b) =>
                compareNumber(
                    b?.sort,
                    a?.sort
                )
                ||
                compareNumber(
                    b?.id,
                    a?.id
                )
            )
            break

        case 'titleAsc':
            list.sort((a, b) =>
                compareText(
                    getVideoTitle(a),
                    getVideoTitle(b)
                )
            )
            break

        case 'titleDesc':
            list.sort((a, b) =>
                compareText(
                    getVideoTitle(b),
                    getVideoTitle(a)
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

        case 'commentsAsc':
            list.sort((a, b) =>
                compareNumber(
                    a?.comments_count,
                    b?.comments_count
                )
            )
            break

        case 'commentsDesc':
            list.sort((a, b) =>
                compareNumber(
                    b?.comments_count,
                    a?.comments_count
                )
            )
            break

        case 'durationAsc':
            list.sort((a, b) =>
                getVideoDuration(a)
                - getVideoDuration(b)
            )
            break

        case 'durationDesc':
            list.sort((a, b) =>
                getVideoDuration(b)
                - getVideoDuration(a)
            )
            break

        case 'publishedAtAsc':
            list.sort((a, b) =>
                safeDate(
                    a?.published_at
                    || a?.created_at
                )
                -
                safeDate(
                    b?.published_at
                    || b?.created_at
                )
            )
            break

        case 'publishedAtDesc':
            list.sort((a, b) =>
                safeDate(
                    b?.published_at
                    || b?.created_at
                )
                -
                safeDate(
                    a?.published_at
                    || a?.created_at
                )
            )
            break
    }

    return list
})

/* ======================== Frontend pagination ======================== */

const frontendCurrentPage =
    ref(1)

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

const effectiveVideosFound = computed(() =>
    props.useServerProcessing
        ? safeNumber(
            props.videosFound
        )
        : sortedVideos.value.length
)

const frontendPaginatedVideos =
    computed(() => {
        if (props.useServerProcessing) {
            return videosData.value
        }

        const start =
            (
                frontendCurrentPage.value - 1
            ) * perPage.value

        return sortedVideos.value.slice(
            start,
            start + perPage.value
        )
    })

const displayedVideos = computed(() =>
    props.useServerProcessing
        ? videosData.value
        : frontendPaginatedVideos.value
)

/* ======================== Server pagination ======================== */

const currentPage = computed(() =>
    Number(
        props.videos?.meta?.current_page
        ?? props.videos?.current_page
        ?? 1
    ) || 1
)

const lastPage = computed(() =>
    Number(
        props.videos?.meta?.last_page
        ?? props.videos?.last_page
        ?? 1
    ) || 1
)

const reloadVideos = (
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

    reloadVideos(target)
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
        reloadVideos(1)
    } else {
        frontendCurrentPage.value = 1
    }
}

const resetFilters = () => {
    if (props.useServerProcessing) {
        reloadVideos(1)
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
 * Первый render — sidebar свёрнуты.
 *
 * После mounted восстанавливаем
 * сохранённое состояние.
 */
const leftCollapsed =
    ref(true)

const rightCollapsed =
    ref(true)

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
const videoGridCols = computed(() => {
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
                                    aria-current="page"
                                >
                                    <span class="mx-2 breadcrumbs">
                                        /
                                    </span>

                                    <span
                                        itemprop="name"
                                        class="breadcrumbs"
                                    >
                                        {{ t('videos') }}
                                    </span>

                                    <meta
                                        itemprop="item"
                                        :content="canonicalUrl"
                                    >

                                    <meta
                                        itemprop="position"
                                        content="2"
                                    >
                                </li>
                            </ol>
                        </nav>

                        <!-- Header -->
                        <div
                            class="my-3 flex flex-wrap items-center
                                   justify-center gap-2 title"
                        >
                            <svg
                                class="h-5 w-5"
                                fill="currentColor"
                                viewBox="0 0 576 512"
                            >
                                <path
                                    d="M336.2 64H47.8C21.4 64 0 85.4 0 111.8v288.4C0 426.6 21.4 448 47.8 448h288.4c26.4 0 47.8-21.4 47.8-47.8V111.8c0-26.4-21.4-47.8-47.8-47.8zm189.4 37.7L416 177.3v157.4l109.6 75.5c21.2 14.6 50.4-.3 50.4-25.8V127.5c0-25.4-29.1-40.4-50.4-25.8z"
                                />
                            </svg>

                            <h1
                                itemprop="headline"
                                class="text-2xl font-bold"
                            >
                                {{ t('videos') }}
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
                            :found="effectiveVideosFound"
                            :sort-options="videoSortOptions"
                            :default-sort="DEFAULT_SORT"
                            :found-label="t('videos')"
                            :search-placeholder="t('searchByName')"
                            @submit="applyFilters"
                            @reset="resetFilters"
                        />

                        <div ref="scrollTarget"></div>

                        <!-- Empty -->
                        <div
                            v-if="displayedVideos.length === 0"
                            class="mt-6 text-center text-slate-700 dark:text-slate-300"
                        >
                            {{ t('noData') }}
                        </div>

                        <!-- Videos -->
                        <div v-else>
                            <VideoGrid
                                v-if="viewMode === 'grid'"
                                :videos="displayedVideos"
                                :cols="videoGridCols"
                            />

                            <VideoRows
                                v-else
                                :videos="displayedVideos"
                            />
                        </div>

                        <!-- Server pagination -->
                        <Pagination
                            v-if="useServerProcessing && lastPage > 1"
                            :current-page="currentPage"
                            :last-page="lastPage"
                            :found="videosFound"
                            @prev="goPrev"
                            @next="goNext"
                            @go="goToPage"
                        />

                        <!-- Frontend pagination -->
                        <FrontendPagination
                            v-if="!useServerProcessing && effectiveVideosFound > perPage"
                            v-model:currentPage="frontendCurrentPage"
                            :items-per-page="perPage"
                            :total-items="effectiveVideosFound"
                        />

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
            setting-key="publicBlogVideosProcessingMode"
            :mode="publicBlogVideosProcessingMode"
            :use-server-processing="useServerProcessing"
            :total="videosCount"
        />
    </DefaultLayout>
</template>
