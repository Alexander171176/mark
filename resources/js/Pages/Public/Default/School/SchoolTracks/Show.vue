<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

import DefaultLayout from '@/Layouts/DefaultLayout.vue'
import Navbar from '@/Partials/Default/Navbar.vue'
import FooterBlog from '@/Partials/Default/FooterBlog.vue'

import Progress from '@/Components/Public/Default/Progress/Progress.vue'
import LeftSidebarSchool from '@/Components/Public/Default/Partials/LeftSidebarSchool.vue'
import RightSidebarSchool from '@/Components/Public/Default/Partials/RightSidebarSchool.vue'

import EntityPageToolbar from '@/Components/Public/Default/PageToolbar/EntityPageToolbar.vue'
import ImageGalleryMain from '@/Components/Public/Default/Media/ImageGalleryMain.vue'
import Pagination from '@/Components/Public/Default/Pagination/Pagination.vue'
import FrontendPagination from '@/Components/Public/Default/Pagination/FrontendPagination.vue'

import SectionVideoList from '@/Components/Public/Default/Blog/BlogVideo/SectionVideoList.vue'
import SectionBanners from '@/Components/Public/Default/Blog/BlogBanner/SectionBanners.vue'

import LikeButtonEntity from '@/Components/Public/Like/LikeButtonEntity.vue'

import InstructorCourseGrid from '@/Components/Public/Default/School/SchoolInstructor/InstructorCourseGrid.vue'
import InstructorCourseRows from '@/Components/Public/Default/School/SchoolInstructor/InstructorCourseRows.vue'

import TrackGrid from '@/Components/Public/Default/School/SchoolTrack/TrackGrid.vue'
import TrackRows from '@/Components/Public/Default/School/SchoolTrack/TrackRows.vue'

import PublicAdminBottomPanel
    from '@/Components/Admin/UI/PublicAdminPanel/PublicAdminBottomPanel.vue'

const { t } = useI18n()
const page = usePage()

const props = defineProps({
    title: String,
    canLogin: Boolean,
    canRegister: Boolean,

    locale: { type: String, default: 'ru' },

    track: {
        type: Object,
        default: () => ({}),
    },

    useServerProcessing: {
        type: Boolean,
        default: false,
    },

    publicSchoolCoursesProcessingMode: {
        type: String,
        default: 'server',
    },

    courses: {
        type: [Array, Object],
        default: () => [],
    },

    coursesCount: {
        type: Number,
        default: 0,
    },

    coursesFound: {
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
    return Number.isFinite(number) ? number : 0
}

const safeDate = (value) => {
    const time = value ? Date.parse(value) : 0
    return Number.isFinite(time) ? time : 0
}

/* ======================== Track ======================== */

const track = computed(() => props.track ?? {})
const translation = computed(() => track.value?.translation ?? {})

const trackName = computed(() => translation.value?.name || '')
const trackShort = computed(() => translation.value?.short || '')
const trackDescription = computed(() => translation.value?.description || '')

const childTracks = computed(() =>
    Array.isArray(track.value?.children) ? track.value.children : []
)

const hasChildren = computed(() => childTracks.value.length > 0)

/* ======================== SEO ======================== */

const trackMetaTitle = computed(() =>
    translation.value?.meta_title || trackName.value
)

const trackMetaKeywords = computed(() =>
    translation.value?.meta_keywords || ''
)

const trackMetaDescription = computed(() =>
    translation.value?.meta_desc || trackShort.value || ''
)

const trackLocale = computed(() =>
    translation.value?.locale || props.locale || 'ru'
)

const ogLocale = computed(() =>
    trackLocale.value === 'ru' ? 'ru_RU' : trackLocale.value
)

const canonicalUrl = computed(() => {
    if (!track.value?.slug) return ''

    return String(route('public.schoolTracks.show', {
        slug: track.value.slug,
    }))
})

/* ======================== Images ======================== */

const trackImages = computed(() =>
    Array.isArray(track.value?.images) ? track.value.images : []
)

const hasTrackImages = computed(() => trackImages.value.length > 0)

const trackSeoImage = computed(() => {
    const image = trackImages.value[0]

    return image?.webp_url
        || image?.image_url
        || image?.thumb_url
        || image?.url
        || ''
})

/* ======================== Courses ======================== */

const coursesData = computed(() => normalizeList(props.courses))

const qCourses = ref(String(props.filters?.q_courses ?? ''))

/**
 * Только backend-настройка
 * publicSchoolCoursesPerPage.
 */
const perPageCourses = computed(() =>
    Number(props.filters?.per_page_courses ?? 12)
)

const DEFAULT_SORT = 'idDesc'

const sortCourses = ref(
    String(props.filters?.sort_courses ?? DEFAULT_SORT)
)

const courseSortOptions = [
    { value: 'idDesc', label: t('idDesc') },
    { value: 'idAsc', label: t('idAsc') },

    { value: 'sortAsc', label: `${t('sortNumber')} 0→9` },
    { value: 'sortDesc', label: `${t('sortNumber')} 9→0` },

    { value: 'titleAsc', label: `${t('title')} A→Z` },
    { value: 'titleDesc', label: `${t('title')} Z→A` },

    { value: 'studentsCountDesc', label: `${t('students')} 9→0` },
    { value: 'studentsCountAsc', label: `${t('students')} 0→9` },

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

    { value: 'difficultyDesc', label: `${t('sortDifficulty')} 9→0` },
    { value: 'difficultyAsc', label: `${t('sortDifficulty')} 0→9` },

    { value: 'durationDesc', label: `${t('duration')} 9→0` },
    { value: 'durationAsc', label: `${t('duration')} 0→9` },

    { value: 'levelAsc', label: `${t('level')} A→Z` },
    { value: 'levelDesc', label: `${t('level')} Z→A` },

    { value: 'availabilityAsc', label: `${t('availability')} A→Z` },
    { value: 'availabilityDesc', label: `${t('availability')} Z→A` },

    { value: 'modulesDesc', label: `${t('modules')} 9→0` },
    { value: 'modulesAsc', label: `${t('modules')} 0→9` },

    { value: 'lessonsDesc', label: `${t('lessons')} 9→0` },
    { value: 'lessonsAsc', label: `${t('lessons')} 0→9` },

    { value: 'tracksDesc', label: `${t('tracks')} 9→0` },
    { value: 'tracksAsc', label: `${t('tracks')} 0→9` },

    { value: 'hashtagsDesc', label: `${t('hashtags')} 9→0` },
    { value: 'hashtagsAsc', label: `${t('hashtags')} 0→9` },

    { value: 'imagesDesc', label: `${t('images')} 9→0` },
    { value: 'imagesAsc', label: `${t('images')} 0→9` },

    { value: 'pricesDesc', label: `${t('prices')} 9→0` },
    { value: 'pricesAsc', label: `${t('prices')} 0→9` },

    { value: 'reviewsDesc', label: `${t('reviews')} 9→0` },
    { value: 'reviewsAsc', label: `${t('reviews')} 0→9` },

    { value: 'publishedAtDesc', label: `${t('publishedAt')} ↓` },
    { value: 'publishedAtAsc', label: `${t('publishedAt')} ↑` },

    { value: 'createdAtDesc', label: `${t('createdAt')} ↓` },
    { value: 'createdAtAsc', label: `${t('createdAt')} ↑` },
]

const getCourseTitle = (course) =>
    course?.translation?.title || ''

const getCourseShort = (course) =>
    course?.translation?.short || ''

const getInstructorName = (course) =>
    course?.instructorProfile?.translation?.title
    || course?.instructorProfile?.user?.name
    || ''

/* ======================== Frontend search ======================== */

const frontendFilteredCourses = computed(() => {
    if (props.useServerProcessing) return coursesData.value

    const query = normalizeText(qCourses.value)
    if (!query) return coursesData.value

    return coursesData.value.filter((course) => [
        course?.id,
        course?.slug,
        getCourseTitle(course),
        getCourseShort(course),
        getInstructorName(course),
        course?.level,
        course?.availability,
    ].some((value) => normalizeText(value).includes(query)))
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

const frontendSortedCourses = computed(() => {
    if (props.useServerProcessing) return frontendFilteredCourses.value

    const list = [...frontendFilteredCourses.value]

    const numberSort = {
        idAsc: ['id', false],
        idDesc: ['id', true],
        sortAsc: ['sort', false],
        sortDesc: ['sort', true],

        studentsCountAsc: ['students_count', false],
        studentsCountDesc: ['students_count', true],

        viewsAsc: ['views', false],
        viewsDesc: ['views', true],

        likesAsc: ['likes_count', false],
        likesDesc: ['likes_count', true],

        popularityAsc: ['popularity', false],
        popularityDesc: ['popularity', true],

        ratingCountAsc: ['rating_count', false],
        ratingCountDesc: ['rating_count', true],

        ratingAvgAsc: ['rating_avg', false],
        ratingAvgDesc: ['rating_avg', true],

        difficultyAsc: ['difficulty', false],
        difficultyDesc: ['difficulty', true],

        durationAsc: ['duration', false],
        durationDesc: ['duration', true],

        modulesAsc: ['modules_count', false],
        modulesDesc: ['modules_count', true],

        lessonsAsc: ['lessons_count', false],
        lessonsDesc: ['lessons_count', true],

        tracksAsc: ['tracks_count', false],
        tracksDesc: ['tracks_count', true],

        hashtagsAsc: ['hashtags_count', false],
        hashtagsDesc: ['hashtags_count', true],

        imagesAsc: ['images_count', false],
        imagesDesc: ['images_count', true],

        pricesAsc: ['prices_count', false],
        pricesDesc: ['prices_count', true],

        reviewsAsc: ['reviews_count', false],
        reviewsDesc: ['reviews_count', true],
    }

    const numberRule = numberSort[sortCourses.value]

    if (numberRule) {
        const [field, desc] = numberRule

        list.sort((a, b) =>
            desc
                ? compareNumber(b?.[field], a?.[field])
                : compareNumber(a?.[field], b?.[field])
        )

        return list
    }

    switch (sortCourses.value) {
        case 'titleAsc':
            list.sort((a, b) =>
                compareText(getCourseTitle(a), getCourseTitle(b))
            )
            break

        case 'titleDesc':
            list.sort((a, b) =>
                compareText(getCourseTitle(b), getCourseTitle(a))
            )
            break

        case 'levelAsc':
            list.sort((a, b) => compareText(a?.level, b?.level))
            break

        case 'levelDesc':
            list.sort((a, b) => compareText(b?.level, a?.level))
            break

        case 'availabilityAsc':
            list.sort((a, b) => compareText(a?.availability, b?.availability))
            break

        case 'availabilityDesc':
            list.sort((a, b) => compareText(b?.availability, a?.availability))
            break

        case 'publishedAtAsc':
            list.sort((a, b) =>
                safeDate(a?.published_at) - safeDate(b?.published_at)
            )
            break

        case 'publishedAtDesc':
            list.sort((a, b) =>
                safeDate(b?.published_at) - safeDate(a?.published_at)
            )
            break

        case 'createdAtAsc':
            list.sort((a, b) =>
                safeDate(a?.created_at) - safeDate(b?.created_at)
            )
            break

        case 'createdAtDesc':
            list.sort((a, b) =>
                safeDate(b?.created_at) - safeDate(a?.created_at)
            )
            break
    }

    return list
})

/* ======================== Frontend pagination ======================== */

const frontendCurrentPage = ref(1)

watch([qCourses, sortCourses], () => {
    if (!props.useServerProcessing) {
        frontendCurrentPage.value = 1
    }
})

const effectiveCoursesFound = computed(() =>
    props.useServerProcessing
        ? Number(props.coursesFound ?? 0)
        : frontendSortedCourses.value.length
)

const frontendPaginatedCourses = computed(() => {
    if (props.useServerProcessing) return coursesData.value

    const perPage = Math.max(1, perPageCourses.value)
    const start = (frontendCurrentPage.value - 1) * perPage

    return frontendSortedCourses.value.slice(start, start + perPage)
})

const displayedCourses = computed(() =>
    props.useServerProcessing
        ? coursesData.value
        : frontendPaginatedCourses.value
)

const hasCourses = computed(() =>
    Number(props.coursesCount ?? 0) > 0
)

/* ======================== Server pagination ======================== */

const currentPage = computed(() =>
    Number(
        props.courses?.meta?.current_page
        ?? props.courses?.current_page
        ?? 1
    ) || 1
)

const lastPage = computed(() =>
    Number(
        props.courses?.meta?.last_page
        ?? props.courses?.last_page
        ?? 1
    ) || 1
)

/* ======================== Toolbar ======================== */

const applyCourseFilters = () => {
    if (props.useServerProcessing) {
        loadServerCourses(1)
    } else {
        frontendCurrentPage.value = 1
    }
}

const resetCourseFilters = () => {
    if (props.useServerProcessing) {
        loadServerCourses(1)
    } else {
        frontendCurrentPage.value = 1
    }
}

/* ======================== Server requests ======================== */

const loadServerCourses = (pageNumber = 1) => {
    if (!props.useServerProcessing || !canonicalUrl.value) return

    router.get(
        canonicalUrl.value,
        {
            q_courses: qCourses.value || undefined,
            sort_courses: sortCourses.value || undefined,
            page_courses: pageNumber,
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
        Math.max(1, Number(pageNumber) || 1),
        lastPage.value
    )

    loadServerCourses(target)
}

const goPrev = () => {
    if (currentPage.value > 1) {
        goToPage(currentPage.value - 1)
    }
}

const goNext = () => {
    if (currentPage.value < lastPage.value) {
        goToPage(currentPage.value + 1)
    }
}

/* ======================== View ======================== */

const VIEW_KEY = 'public_school_courses_view'

const getStoredView = () => {
    if (typeof window === 'undefined') return 'grid'

    const value = localStorage.getItem(VIEW_KEY)
    return ['grid', 'rows'].includes(value) ? value : 'grid'
}

const viewMode = ref(getStoredView())

watch(viewMode, (value) => {
    if (typeof window !== 'undefined') {
        localStorage.setItem(VIEW_KEY, value)
    }
})

/* ======================== Sidebars ======================== */

const siteSettings = computed(() =>
    page.props.siteSettings ?? {}
)

const settingEnabled = (value, defaultValue = true) => {
    if (value === undefined || value === null || value === '') {
        return defaultValue
    }

    if (typeof value === 'boolean') return value

    return String(value) === 'true'
}

const showLeft = computed(() =>
    settingEnabled(siteSettings.value?.ViewLeftColumn, true)
)

const showRight = computed(() =>
    settingEnabled(siteSettings.value?.ViewRightColumn, true)
)

const LEFT_SIDEBAR_KEY = 'public_left_sidebar_collapsed'
const RIGHT_SIDEBAR_KEY = 'public_right_sidebar_collapsed'

/**
 * На первом render считаем сайдбары свёрнутыми.
 *
 * После mounted восстанавливаем реальное
 * состояние из localStorage.
 *
 * В сочетании с transition-all это создаёт
 * естественный эффект раскрытия страницы.
 */
const leftCollapsed = ref(true)
const rightCollapsed = ref(true)

const readStoredBoolean = (key, fallback = true) => {
    try {
        const value = localStorage.getItem(key)
        return value === null ? fallback : value === 'true'
    } catch {
        return fallback
    }
}

const writeStoredBoolean = (key, value) => {
    try {
        localStorage.setItem(key, String(Boolean(value)))
    } catch {
        //
    }
}

onMounted(() => {
    leftCollapsed.value = readStoredBoolean(
        LEFT_SIDEBAR_KEY,
        true
    )

    rightCollapsed.value = readStoredBoolean(
        RIGHT_SIDEBAR_KEY,
        true
    )
})

const setLeftCollapsed = (value) => {
    leftCollapsed.value = Boolean(value)
    writeStoredBoolean(LEFT_SIDEBAR_KEY, leftCollapsed.value)
}

const setRightCollapsed = (value) => {
    rightCollapsed.value = Boolean(value)
    writeStoredBoolean(RIGHT_SIDEBAR_KEY, rightCollapsed.value)
}

/**
 * 2 — оба сайдбара открыты.
 * 3 — открыт один.
 * 4 — оба закрыты.
 */
const gridCols = computed(() => {
    const leftExpanded = showLeft.value && !leftCollapsed.value
    const rightExpanded = showRight.value && !rightCollapsed.value

    if (leftExpanded && rightExpanded) return 2
    if (leftExpanded || rightExpanded) return 3

    return 4
})

/* ======================== Shared data ======================== */

const trackTree = computed(() =>
    Array.isArray(props.trackTree) ? props.trackTree : []
)

const mainVideos = computed(() =>
    normalizeList(props.mainVideos)
)

const mainBanners = computed(() =>
    normalizeList(props.mainBanners)
)

/* ======================== Admin ======================== */

const isAdmin = computed(() =>
    page.props?.isAdmin === true
)
</script>

<template>
    <Head>
        <title>{{ trackMetaTitle }}</title>

        <meta v-if="trackMetaDescription" name="description" :content="trackMetaDescription">
        <meta v-if="trackMetaKeywords" name="keywords" :content="trackMetaKeywords">
        <meta name="robots" content="index, follow, max-image-preview:large">

        <link v-if="canonicalUrl" rel="canonical" :href="canonicalUrl">

        <!-- Open Graph -->
        <meta property="og:type" content="website">
        <meta property="og:title" :content="trackMetaTitle">
        <meta v-if="trackMetaDescription" property="og:description" :content="trackMetaDescription">
        <meta v-if="canonicalUrl" property="og:url" :content="canonicalUrl">
        <meta property="og:locale" :content="ogLocale">
        <meta v-if="trackSeoImage" property="og:image" :content="trackSeoImage">

        <!-- Twitter / X -->
        <meta
            name="twitter:card"
            :content="trackSeoImage ? 'summary_large_image' : 'summary'"
        >
        <meta name="twitter:title" :content="trackMetaTitle">
        <meta v-if="trackMetaDescription" name="twitter:description" :content="trackMetaDescription">
        <meta v-if="trackSeoImage" name="twitter:image" :content="trackSeoImage">

        <!-- Dublin Core -->
        <meta name="DC.title" :content="trackMetaTitle">
        <meta v-if="trackMetaDescription" name="DC.description" :content="trackMetaDescription">
        <meta v-if="trackMetaKeywords" name="DC.subject" :content="trackMetaKeywords">
        <meta name="DC.language" :content="trackLocale">
        <meta v-if="canonicalUrl" name="DC.identifier" :content="canonicalUrl">
        <meta name="DC.type" content="Collection">
        <meta name="DC.format" content="text/html">
    </Head>

    <DefaultLayout
        :title="title"
        :can-login="canLogin"
        :can-register="canRegister"
    >
        <Navbar />

        <div class="min-h-screen px-1.5">
            <main class="mx-auto flex flex-col lg:flex-row gap-4 tracking-wider">

                <!-- Left sidebar -->
                <aside
                    v-if="showLeft"
                    class="shrink-0 mt-12 lg:mt-28 pl-3 transition-all duration-300"
                    :class="leftCollapsed ? 'lg:w-10' : 'lg:w-64'"
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
                    <meta itemprop="name" :content="trackName">
                    <meta v-if="trackMetaDescription" itemprop="description" :content="trackMetaDescription">
                    <meta v-if="trackMetaKeywords" itemprop="keywords" :content="trackMetaKeywords">
                    <meta v-if="canonicalUrl" itemprop="url" :content="canonicalUrl">
                    <meta itemprop="inLanguage" :content="trackLocale">

                    <div class="mx-auto max-w-6xl">

                        <!-- Breadcrumbs -->
                        <nav
                            class="mb-3 text-sm"
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
                                    <Link itemprop="item" :href="route('home')" class="breadcrumb-link hover:underline">
                                        <span itemprop="name">{{ t('home') }}</span>
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
                                        :href="route('public.schoolInstructors.index')"
                                        class="breadcrumb-link hover:underline"
                                    >
                                        <span itemprop="name">{{ t('instructors') }}</span>
                                    </Link>

                                    <meta itemprop="position" content="2">
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
                                        :href="route('public.schoolTracks.index')"
                                        class="breadcrumb-link hover:underline"
                                    >
                                        <span itemprop="name">{{ t('tracks') }}</span>
                                    </Link>

                                    <meta itemprop="position" content="3">
                                </li>

                                <template v-if="track?.parent">
                                    <li
                                        itemprop="itemListElement"
                                        itemscope
                                        itemtype="https://schema.org/ListItem"
                                        class="flex items-center"
                                    >
                                        <span class="mx-2 breadcrumbs">/</span>

                                        <Link
                                            itemprop="item"
                                            :href="route('public.schoolTracks.show', { slug: track.parent.slug })"
                                            class="breadcrumb-link hover:underline"
                                        >
                                            <span itemprop="name">
                                                {{ track.parent?.translation?.name || '' }}
                                            </span>
                                        </Link>

                                        <meta itemprop="position" content="4">
                                    </li>
                                </template>

                                <li
                                    itemprop="itemListElement"
                                    itemscope
                                    itemtype="https://schema.org/ListItem"
                                    class="flex items-center"
                                    aria-current="page"
                                >
                                    <span class="mx-2 breadcrumbs">/</span>
                                    <span itemprop="name" class="breadcrumbs">{{ trackName }}</span>

                                    <meta v-if="canonicalUrl" itemprop="item" :content="canonicalUrl">
                                    <meta itemprop="position" :content="track?.parent ? 5 : 4">
                                </li>
                            </ol>
                        </nav>

                        <!-- Gallery -->
                        <div v-if="hasTrackImages" class="flex items-center justify-center">
                            <div class="w-full">
                                <ImageGalleryMain
                                    :images="trackImages"
                                    :alt="trackName"
                                    rounded-class="rounded-lg"
                                    shadow-class="shadow-lg shadow-gray-400 dark:shadow-gray-700"
                                    img-class="w-full h-full object-cover"
                                />
                            </div>
                        </div>

                        <!-- Header -->
                        <div class="flex items-center justify-between gap-1">
                            <div :title="t('courses')" class="flex items-center justify-center gap-1">
                                <svg
                                    class="h-5 w-5 text-slate-600/85 dark:text-slate-200/85"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"
                                    />
                                </svg>

                                <span
                                    v-if="hasCourses"
                                    class="text-center text-sm text-gray-500"
                                >
                                    {{ coursesCount }}
                                </span>
                            </div>

                            <div class="flex flex-wrap items-center justify-center gap-3 title my-3">
                                <h1 itemprop="headline" class="text-2xl font-bold">
                                    {{ trackName }}
                                </h1>
                            </div>

                            <div :title="t('views')" class="flex items-center justify-center gap-1">
                                <svg
                                    class="h-4 w-4 text-slate-600/85 dark:text-slate-200/85"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 576 512"
                                    fill="currentColor"
                                >
                                    <path
                                        d="M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z"
                                    />
                                </svg>

                                <span class="text-center text-sm text-gray-500">
                                    {{ track.views || 0 }}
                                </span>
                            </div>
                        </div>

                        <div
                            v-if="trackShort"
                            itemprop="abstract"
                            class="mb-3 text-sm subtitle text-center"
                        >
                            {{ trackShort }}
                        </div>

                        <div
                            v-if="trackDescription"
                            itemprop="text"
                            class="my-3 text-sm subtitle text-center"
                            v-html="trackDescription"
                        />

                        <!-- Like -->
                        <div class="flex justify-center items-center">
                            <LikeButtonEntity
                                :likes-count="track.likes_count || 0"
                                :already-liked="track.already_liked || false"
                                route-name="public.schoolTracks.like"
                                :route-params="track.id"
                                :title="t('like')"
                                icon-class="w-4 h-4"
                            />
                        </div>

                        <!-- Course toolbar -->
                        <EntityPageToolbar
                            v-if="hasCourses"
                            v-model="qCourses"
                            v-model:view-mode="viewMode"
                            v-model:sort-value="sortCourses"
                            :found="effectiveCoursesFound"
                            :sort-options="courseSortOptions"
                            :default-sort="DEFAULT_SORT"
                            :found-label="t('courses')"
                            :search-placeholder="t('searchByName')"
                            @submit="applyCourseFilters"
                            @reset="resetCourseFilters"
                        />

                        <!-- Empty -->
                        <div
                            v-if="hasCourses && displayedCourses.length === 0"
                            class="mt-6 text-center text-slate-700 dark:text-slate-300"
                        >
                            {{ t('noData') }}
                        </div>

                        <!-- Courses -->
                        <div v-if="displayedCourses.length">
                            <InstructorCourseGrid
                                v-if="viewMode === 'grid'"
                                :courses="displayedCourses"
                                :cols="gridCols"
                            />

                            <InstructorCourseRows
                                v-else
                                :courses="displayedCourses"
                            />
                        </div>

                        <!-- Server pagination -->
                        <Pagination
                            v-if="useServerProcessing && hasCourses && lastPage > 1"
                            :current-page="currentPage"
                            :last-page="lastPage"
                            :found="coursesFound"
                            @prev="goPrev"
                            @next="goNext"
                            @go="goToPage"
                        />

                        <!-- Frontend pagination -->
                        <FrontendPagination
                            v-if="!useServerProcessing && effectiveCoursesFound > perPageCourses"
                            v-model:currentPage="frontendCurrentPage"
                            :items-per-page="perPageCourses"
                            :total-items="effectiveCoursesFound"
                        />

                        <!-- Child tracks -->
                        <div v-if="hasChildren">
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
                                        d="M622.34 153.2L343.4 67.5c-15.2-4.67-31.6-4.67-46.79 0L17.66 153.2c-23.54 7.23-23.54 38.36 0 45.59l48.63 14.94c-10.67 13.19-17.23 29.28-17.88 46.9C38.78 266.15 32 276.11 32 288c0 10.78 5.68 19.85 13.86 25.65L20.33 428.53C18.11 438.52 25.71 448 35.94 448h56.11c10.24 0 17.84-9.48 15.62-19.47L82.14 313.65C90.32 307.85 96 298.78 96 288c0-11.57-6.47-21.25-15.66-26.87.76-15.02 8.44-28.3 20.69-36.72L296.6 284.5c9.06 2.78 26.44 6.25 46.79 0l278.95-85.7c23.55-7.24 23.55-38.36 0-45.6zM352.79 315.09c-28.53 8.76-52.84 3.92-65.59 0l-145.02-44.55L128 384c0 35.35 85.96 64 192 64s192-28.65 192-64l-14.18-113.47-145.03 44.56z"
                                    />
                                </svg>

                                <h2 class="text-xl font-semibold">
                                    {{ t('subheadings') }}
                                </h2>
                            </div>

                            <div class="mt-6">
                                <TrackGrid
                                    v-if="viewMode === 'grid'"
                                    :tracks="childTracks"
                                    :cols="gridCols"
                                />

                                <TrackRows
                                    v-else
                                    :tracks="childTracks"
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
                    class="shrink-0 lg:mt-28 pr-3 transition-all duration-300"
                    :class="rightCollapsed ? 'lg:w-10' : 'lg:w-64'"
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

        <!-- Admin panel -->
        <PublicAdminBottomPanel
            v-if="isAdmin"
            setting-key="publicSchoolCoursesProcessingMode"
            :mode="publicSchoolCoursesProcessingMode"
            :use-server-processing="useServerProcessing"
            :total="coursesCount"
        />
    </DefaultLayout>
</template>
