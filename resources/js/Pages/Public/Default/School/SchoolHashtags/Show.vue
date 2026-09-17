<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

import DefaultLayout from '@/Layouts/DefaultLayout.vue'
import Navbar from '@/Partials/Default/Navbar.vue'
import FooterBlog from '@/Partials/Default/FooterBlog.vue'

import PublicAdminBottomPanel
    from '@/Components/Admin/UI/PublicAdminPanel/PublicAdminBottomPanel.vue'
import Progress from '@/Components/Public/Default/Progress/Progress.vue'
import LeftSidebarSchool from '@/Components/Public/Default/Partials/LeftSidebarSchool.vue'
import RightSidebarSchool from '@/Components/Public/Default/Partials/RightSidebarSchool.vue'
import Pagination from '@/Components/Public/Default/Pagination/Pagination.vue'
import FrontendPagination from '@/Components/Public/Default/Pagination/FrontendPagination.vue'
import EntityPageToolbar from '@/Components/Public/Default/PageToolbar/EntityPageToolbar.vue'
import CourseGrid from '@/Components/Public/Default/School/SchoolCourse/CourseGrid.vue'
import CourseRows from '@/Components/Public/Default/School/SchoolCourse/CourseRows.vue'
import SectionVideoList from '@/Components/Public/Default/Blog/BlogVideo/SectionVideoList.vue'
import SectionBanners from '@/Components/Public/Default/Blog/BlogBanner/SectionBanners.vue'

import { unwrap, unwrapList } from '@/composables/useUnwrap'

const { t } = useI18n()
const page = usePage()

const props = defineProps({
    title: { type: String, default: '' },
    canLogin: { type: Boolean, default: false },
    canRegister: { type: Boolean, default: false },
    locale: { type: String, default: 'ru' },

    hashtag: { type: Object, default: () => ({}) },

    useServerProcessing: { type: Boolean, default: false },
    publicSchoolCoursesProcessingMode: { type: String, default: 'server' },

    courses: { type: [Array, Object], default: () => [] },
    coursesCount: { type: Number, default: 0 },
    coursesFound: { type: Number, default: 0 },

    filters: { type: Object, default: () => ({}) },
    trackTree: { type: Array, default: () => [] },

    mainVideos: { type: [Array, Object], default: () => [] },
    mainBanners: { type: [Array, Object], default: () => [] },
})

/* ======================== Helpers ======================== */

const normalizeText = (value) => String(value ?? '').trim().toLocaleLowerCase()

const absoluteUrl = (value) => {
    const url = String(value ?? '').trim()
    if (!url) return ''

    try {
        return new URL(url, typeof window !== 'undefined' ? window.location.origin : undefined).href
    } catch {
        return url
    }
}

/* ======================== Hashtag ======================== */

const hashtag = computed(() => unwrap(props.hashtag) ?? {})
const translation = computed(() => hashtag.value?.translation ?? {})

const hashtagName = computed(() => translation.value?.name || hashtag.value?.slug || t('hashtags'))
const hashtagShort = computed(() => translation.value?.short || '')
const hashtagDescription = computed(() => translation.value?.description || '')

const hashtagCoursesCount = computed(() => Number(hashtag.value?.courses_count ?? props.coursesCount ?? 0))
const hashtagModulesCount = computed(() => Number(hashtag.value?.modules_count ?? 0))
const hashtagLessonsCount = computed(() => Number(hashtag.value?.lessons_count ?? 0))

/* ======================== SEO ======================== */

const seoTitle = computed(() => translation.value?.meta_title || hashtagName.value)
const seoDescription = computed(() => translation.value?.meta_desc || hashtagShort.value || '')
const seoKeywords = computed(() => translation.value?.meta_keywords || '')
const contentLocale = computed(() => translation.value?.locale || props.locale || 'ru')

const ogLocale = computed(() => {
    const locale = contentLocale.value.replace('-', '_')
    if (locale === 'ru') return 'ru_RU'
    if (locale === 'en') return 'en_US'
    if (locale === 'kk') return 'kk_KZ'
    return locale
})

const canonicalUrl = computed(() => {
    if (!hashtag.value?.slug) return ''
    return absoluteUrl(route('public.schoolHashtags.show', { slug: hashtag.value.slug }))
})

const coursesIndexUrl = computed(() => absoluteUrl(route('public.schoolCourses.index')))
const homeUrl = computed(() => absoluteUrl(route('home')))
const dcSubject = computed(() => seoKeywords.value || hashtagName.value)

const jsonLd = computed(() => {
    if (!canonicalUrl.value) return null

    const graph = [
        {
            '@type': 'CollectionPage',
            '@id': canonicalUrl.value,
            url: canonicalUrl.value,
            name: seoTitle.value,
            inLanguage: contentLocale.value,
            isPartOf: {
                '@type': 'WebSite',
                url: homeUrl.value,
            },
            mainEntity: {
                '@id': `${canonicalUrl.value}#courses`,
            },
        },
        {
            '@type': 'BreadcrumbList',
            '@id': `${canonicalUrl.value}#breadcrumb`,
            itemListElement: [
                {
                    '@type': 'ListItem',
                    position: 1,
                    name: t('home'),
                    item: homeUrl.value,
                },
                {
                    '@type': 'ListItem',
                    position: 2,
                    name: t('courses'),
                    item: coursesIndexUrl.value,
                },
                {
                    '@type': 'ListItem',
                    position: 3,
                    name: `#${hashtagName.value}`,
                    item: canonicalUrl.value,
                },
            ],
        },
        {
            '@type': 'ItemList',
            '@id': `${canonicalUrl.value}#courses`,
            name: `${t('courses')}: #${hashtagName.value}`,
            numberOfItems: Number(props.coursesCount ?? hashtagCoursesCount.value),
            itemListElement: coursesData.value.map((course, index) => ({
                '@type': 'ListItem',
                position: index + 1,
                name: getCourseTitle(course),
                url: course?.slug
                    ? absoluteUrl(route('public.schoolCourses.show', { slug: course.slug }))
                    : undefined,
            })).filter((item) => item.url),
        },
    ]

    if (seoDescription.value) graph[0].description = seoDescription.value
    if (seoKeywords.value) graph[0].keywords = seoKeywords.value

    return {
        '@context': 'https://schema.org',
        '@graph': graph,
    }
})

/* ======================== Courses ======================== */

const coursesData = computed(() => unwrapList(props.courses))

const qCourses = ref(String(props.filters?.q_courses ?? ''))
const perPageCourses = computed(() => Number(props.filters?.per_page_courses ?? 12))

const DEFAULT_SORT = 'idDesc'
const sortCourses = ref(String(props.filters?.sort_courses ?? DEFAULT_SORT))

const courseSortOptions = computed(() => [
    { value: 'idDesc', label: t('idDesc') },
    { value: 'idAsc', label: t('idAsc') },
    { value: 'sortAsc', label: t('sortDefault') },
    { value: 'sortDesc', label: t('sortReverse') },
    { value: 'titleAsc', label: t('sortNameAsc') },
    { value: 'titleDesc', label: t('sortNameDesc') },
    { value: 'studentsCountAsc', label: `${t('students')} ↑` },
    { value: 'studentsCountDesc', label: `${t('students')} ↓` },
    { value: 'viewsAsc', label: `${t('views')} ↑` },
    { value: 'viewsDesc', label: `${t('views')} ↓` },
    { value: 'likesAsc', label: `${t('likes')} ↑` },
    { value: 'likesDesc', label: `${t('likes')} ↓` },
    { value: 'popularityAsc', label: `${t('popularity')} ↑` },
    { value: 'popularityDesc', label: `${t('popularity')} ↓` },
    { value: 'ratingAvgAsc', label: `${t('rating')} ↑` },
    { value: 'ratingAvgDesc', label: `${t('rating')} ↓` },
    { value: 'ratingCountAsc', label: `${t('rating')} ↑` },
    { value: 'ratingCountDesc', label: `${t('rating')} ↓` },
    { value: 'difficultyAsc', label: `${t('difficulty')} ↑` },
    { value: 'difficultyDesc', label: `${t('difficulty')} ↓` },
    { value: 'durationAsc', label: `${t('duration')} ↑` },
    { value: 'durationDesc', label: `${t('duration')} ↓` },
    { value: 'levelAsc', label: `${t('level')} ↑` },
    { value: 'levelDesc', label: `${t('level')} ↓` },
    { value: 'modulesAsc', label: `${t('modules')} ↑` },
    { value: 'modulesDesc', label: `${t('modules')} ↓` },
    { value: 'lessonsAsc', label: `${t('lessons')} ↑` },
    { value: 'lessonsDesc', label: `${t('lessons')} ↓` },
    { value: 'tracksAsc', label: `${t('tracks')} ↑` },
    { value: 'tracksDesc', label: `${t('tracks')} ↓` },
    { value: 'hashtagsAsc', label: `${t('hashtags')} ↑` },
    { value: 'hashtagsDesc', label: `${t('hashtags')} ↓` },
    { value: 'reviewsAsc', label: `${t('reviews')} ↑` },
    { value: 'reviewsDesc', label: `${t('reviews')} ↓` },
    { value: 'publishedAtAsc', label: `${t('date')} ↑` },
    { value: 'publishedAtDesc', label: `${t('date')} ↓` },
])

/* ======================== View ======================== */

const VIEW_KEY = 'public_school_courses_view'

const getStoredView = () => {
    if (typeof window === 'undefined') return 'grid'

    try {
        const value = localStorage.getItem(VIEW_KEY)
        return ['grid', 'rows'].includes(value) ? value : 'grid'
    } catch {
        return 'grid'
    }
}

const viewMode = ref(getStoredView())

watch(viewMode, (value) => {
    if (typeof window === 'undefined') return

    try {
        localStorage.setItem(VIEW_KEY, value)
    } catch {
        //
    }
})

/* ======================== Frontend search ======================== */

const getCourseTitle = (course) => course?.translation?.title || ''
const getCourseShort = (course) => course?.translation?.short || ''

const getInstructorName = (course) =>
    course?.instructorProfile?.translation?.title
    || course?.instructorProfile?.user?.name
    || ''

const frontendFilteredCourses = computed(() => {
    if (props.useServerProcessing) return coursesData.value

    const query = normalizeText(qCourses.value)
    if (!query) return coursesData.value

    return coursesData.value.filter((course) => [
        course?.slug,
        getCourseTitle(course),
        getCourseShort(course),
        getInstructorName(course),
    ].some((value) => normalizeText(value).includes(query)))
})

/* ======================== Frontend sort ======================== */

const compareText = (a, b) =>
    String(a ?? '').localeCompare(String(b ?? ''), props.locale, { sensitivity: 'base' })

const compareNumber = (a, b) => Number(a ?? 0) - Number(b ?? 0)

const frontendSortedCourses = computed(() => {
    if (props.useServerProcessing) return frontendFilteredCourses.value

    const list = [...frontendFilteredCourses.value]

    list.sort((a, b) => {
        switch (sortCourses.value) {
            case 'idAsc':
                return compareNumber(a?.id, b?.id)
            case 'idDesc':
                return compareNumber(b?.id, a?.id)
            case 'sortAsc':
                return compareNumber(a?.sort, b?.sort) || compareNumber(b?.id, a?.id)
            case 'sortDesc':
                return compareNumber(b?.sort, a?.sort) || compareNumber(b?.id, a?.id)
            case 'titleAsc':
                return compareText(getCourseTitle(a), getCourseTitle(b)) || compareNumber(b?.id, a?.id)
            case 'titleDesc':
                return compareText(getCourseTitle(b), getCourseTitle(a)) || compareNumber(b?.id, a?.id)
            case 'studentsCountAsc':
                return compareNumber(a?.students_count, b?.students_count) || compareNumber(b?.id, a?.id)
            case 'studentsCountDesc':
                return compareNumber(b?.students_count, a?.students_count) || compareNumber(b?.id, a?.id)
            case 'viewsAsc':
                return compareNumber(a?.views, b?.views) || compareNumber(b?.id, a?.id)
            case 'viewsDesc':
                return compareNumber(b?.views, a?.views) || compareNumber(b?.id, a?.id)
            case 'likesAsc':
                return compareNumber(a?.likes_count, b?.likes_count) || compareNumber(b?.id, a?.id)
            case 'likesDesc':
                return compareNumber(b?.likes_count, a?.likes_count) || compareNumber(b?.id, a?.id)
            case 'popularityAsc':
                return compareNumber(a?.popularity, b?.popularity) || compareNumber(b?.id, a?.id)
            case 'popularityDesc':
                return compareNumber(b?.popularity, a?.popularity) || compareNumber(b?.id, a?.id)
            case 'ratingAvgAsc':
                return compareNumber(a?.rating_avg, b?.rating_avg) || compareNumber(b?.id, a?.id)
            case 'ratingAvgDesc':
                return compareNumber(b?.rating_avg, a?.rating_avg) || compareNumber(b?.id, a?.id)
            case 'ratingCountAsc':
                return compareNumber(a?.rating_count, b?.rating_count) || compareNumber(b?.id, a?.id)
            case 'ratingCountDesc':
                return compareNumber(b?.rating_count, a?.rating_count) || compareNumber(b?.id, a?.id)
            case 'difficultyAsc':
                return compareNumber(a?.difficulty, b?.difficulty) || compareNumber(b?.id, a?.id)
            case 'difficultyDesc':
                return compareNumber(b?.difficulty, a?.difficulty) || compareNumber(b?.id, a?.id)
            case 'durationAsc':
                return compareNumber(a?.duration, b?.duration) || compareNumber(b?.id, a?.id)
            case 'durationDesc':
                return compareNumber(b?.duration, a?.duration) || compareNumber(b?.id, a?.id)
            case 'levelAsc':
                return compareText(a?.level, b?.level) || compareNumber(b?.id, a?.id)
            case 'levelDesc':
                return compareText(b?.level, a?.level) || compareNumber(b?.id, a?.id)
            case 'modulesAsc':
                return compareNumber(a?.modules_count, b?.modules_count) || compareNumber(b?.id, a?.id)
            case 'modulesDesc':
                return compareNumber(b?.modules_count, a?.modules_count) || compareNumber(b?.id, a?.id)
            case 'lessonsAsc':
                return compareNumber(a?.lessons_count, b?.lessons_count) || compareNumber(b?.id, a?.id)
            case 'lessonsDesc':
                return compareNumber(b?.lessons_count, a?.lessons_count) || compareNumber(b?.id, a?.id)
            case 'tracksAsc':
                return compareNumber(a?.tracks_count, b?.tracks_count) || compareNumber(b?.id, a?.id)
            case 'tracksDesc':
                return compareNumber(b?.tracks_count, a?.tracks_count) || compareNumber(b?.id, a?.id)
            case 'hashtagsAsc':
                return compareNumber(a?.hashtags_count, b?.hashtags_count) || compareNumber(b?.id, a?.id)
            case 'hashtagsDesc':
                return compareNumber(b?.hashtags_count, a?.hashtags_count) || compareNumber(b?.id, a?.id)
            case 'reviewsAsc':
                return compareNumber(a?.reviews_count, b?.reviews_count) || compareNumber(b?.id, a?.id)
            case 'reviewsDesc':
                return compareNumber(b?.reviews_count, a?.reviews_count) || compareNumber(b?.id, a?.id)
            case 'publishedAtAsc':
                return compareText(a?.published_at, b?.published_at) || compareNumber(b?.id, a?.id)
            case 'publishedAtDesc':
                return compareText(b?.published_at, a?.published_at) || compareNumber(b?.id, a?.id)
            default:
                return compareNumber(a?.sort, b?.sort) || compareNumber(b?.id, a?.id)
        }
    })

    return list
})

/* ======================== Frontend pagination ======================== */

const frontendCurrentPage = ref(1)

watch([qCourses, sortCourses], () => {
    if (!props.useServerProcessing) frontendCurrentPage.value = 1
})

const effectiveCoursesFound = computed(() =>
    props.useServerProcessing ? Number(props.coursesFound ?? 0) : frontendSortedCourses.value.length
)

const frontendPaginatedCourses = computed(() => {
    if (props.useServerProcessing) return coursesData.value

    const perPage = Math.max(1, perPageCourses.value)
    const start = (frontendCurrentPage.value - 1) * perPage

    return frontendSortedCourses.value.slice(start, start + perPage)
})

const displayedCourses = computed(() =>
    props.useServerProcessing ? coursesData.value : frontendPaginatedCourses.value
)

/* ======================== Server pagination ======================== */

const currentPage = computed(() =>
    Number(props.courses?.meta?.current_page ?? props.courses?.current_page ?? 1) || 1
)

const lastPage = computed(() =>
    Number(props.courses?.meta?.last_page ?? props.courses?.last_page ?? 1) || 1
)

/* ======================== Server requests ======================== */

const loadServerCourses = (pageNumber = 1) => {
    if (!props.useServerProcessing || !canonicalUrl.value) return

    router.get(
        canonicalUrl.value,
        {
            q_courses: qCourses.value || undefined,
            sort_courses: sortCourses.value,
            page_courses: pageNumber,
        },
        {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        }
    )
}

const applyFilters = () => {
    if (props.useServerProcessing) loadServerCourses(1)
    else frontendCurrentPage.value = 1
}

const resetFilters = () => {
    qCourses.value = ''
    sortCourses.value = DEFAULT_SORT

    if (props.useServerProcessing) loadServerCourses(1)
    else frontendCurrentPage.value = 1
}

const goToPage = (pageNumber) => {
    const target = Math.min(Math.max(1, Number(pageNumber) || 1), lastPage.value)
    loadServerCourses(target)
}

const goPrev = () => {
    if (currentPage.value > 1) goToPage(currentPage.value - 1)
}

const goNext = () => {
    if (currentPage.value < lastPage.value) goToPage(currentPage.value + 1)
}

/* ======================== Sidebars ======================== */

const siteSettings = page.props?.siteSettings || {}
const isAdmin = computed(() => page.props?.isAdmin === true)

const showLeft = computed(() =>
    !siteSettings?.ViewLeftColumn || siteSettings.ViewLeftColumn === 'true'
)

const showRight = computed(() =>
    !siteSettings?.ViewRightColumn || siteSettings.ViewRightColumn === 'true'
)

const LEFT_SIDEBAR_KEY = 'public_left_sidebar_collapsed'
const RIGHT_SIDEBAR_KEY = 'public_right_sidebar_collapsed'

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
    leftCollapsed.value = readStoredBoolean(LEFT_SIDEBAR_KEY, true)
    rightCollapsed.value = readStoredBoolean(RIGHT_SIDEBAR_KEY, true)
})

const setLeftCollapsed = (value) => {
    leftCollapsed.value = Boolean(value)
    writeStoredBoolean(LEFT_SIDEBAR_KEY, leftCollapsed.value)
}

const setRightCollapsed = (value) => {
    rightCollapsed.value = Boolean(value)
    writeStoredBoolean(RIGHT_SIDEBAR_KEY, rightCollapsed.value)
}

const courseGridCols = computed(() => {
    const leftExpanded = showLeft.value && !leftCollapsed.value
    const rightExpanded = showRight.value && !rightCollapsed.value

    if (leftExpanded && rightExpanded) return 2
    if (leftExpanded || rightExpanded) return 3
    return 4
})

/* ======================== Sidebar data ======================== */

const trackTree = computed(() => Array.isArray(props.trackTree) ? props.trackTree : [])
const mainVideos = computed(() => unwrapList(props.mainVideos))
const mainBanners = computed(() => unwrapList(props.mainBanners))
</script>

<template>
    <Head>
        <!-- Основные SEO -->
        <title>{{ seoTitle }}</title>
        <meta v-if="seoDescription" name="description" :content="seoDescription">
        <meta v-if="seoKeywords" name="keywords" :content="seoKeywords">
        <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
        <meta name="googlebot" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">

        <!-- Canonical -->
        <link v-if="canonicalUrl" rel="canonical" :href="canonicalUrl">

        <!-- Open Graph -->
        <meta property="og:type" content="website">
        <meta property="og:title" :content="seoTitle">
        <meta v-if="seoDescription" property="og:description" :content="seoDescription">
        <meta v-if="canonicalUrl" property="og:url" :content="canonicalUrl">
        <meta property="og:locale" :content="ogLocale">

        <!-- Twitter / X -->
        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" :content="seoTitle">
        <meta v-if="seoDescription" name="twitter:description" :content="seoDescription">

        <!-- Dublin Core -->
        <meta name="DC.title" :content="seoTitle">
        <meta v-if="seoDescription" name="DC.description" :content="seoDescription">
        <meta v-if="dcSubject" name="DC.subject" :content="dcSubject">
        <meta name="DC.language" :content="contentLocale">
        <meta v-if="canonicalUrl" name="DC.identifier" :content="canonicalUrl">
        <meta name="DC.type" content="Collection">
        <meta name="DC.format" content="text/html">

        <!-- JSON-LD -->
        <component
            :is="'script'"
            v-if="jsonLd"
            type="application/ld+json"
            v-text="JSON.stringify(jsonLd)"
        />
    </Head>

    <DefaultLayout :title="title" :can-login="canLogin" :can-register="canRegister">
        <Navbar />

        <div class="min-h-screen px-1.5">
            <main class="mx-auto flex flex-col gap-4 tracking-wider lg:flex-row">

                <!-- Left sidebar -->
                <aside
                    v-if="showLeft"
                    class="mt-12 shrink-0 pl-3 transition-all duration-300 lg:mt-28"
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
                    :itemid="canonicalUrl || undefined"
                    class="slate-1 w-full min-w-0 pb-6 lg:mt-28"
                >
                    <meta itemprop="name" :content="seoTitle">
                    <meta v-if="seoDescription" itemprop="description" :content="seoDescription">
                    <meta v-if="seoKeywords" itemprop="keywords" :content="seoKeywords">
                    <link v-if="canonicalUrl" itemprop="url" :href="canonicalUrl">
                    <meta itemprop="inLanguage" :content="contentLocale">

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
                                    <Link itemprop="item" :href="route('home')"
                                          class="breadcrumb-link hover:underline">
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
                                        :href="route('public.schoolCourses.index')"
                                        class="breadcrumb-link hover:underline"
                                    >
                                        <span itemprop="name">{{ t('courses') }}</span>
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
                                    <span itemprop="name" class="breadcrumbs">
                                        #{{ hashtagName }}
                                    </span>
                                    <link v-if="canonicalUrl" itemprop="item" :href="canonicalUrl">
                                    <meta itemprop="position" content="3">
                                </li>
                            </ol>
                        </nav>

                        <!-- Hashtag -->
                        <header
                            class="mb-5 rounded-md border border-gray-200 bg-white p-4
                                   shadow-sm dark:border-gray-700 dark:bg-gray-900">
                            <div class="flex flex-wrap items-center justify-center gap-3">
                                <h1 itemprop="headline"
                                    class="text-2xl font-bold text-slate-800 dark:text-slate-100">
                                    #{{ hashtagName }}
                                </h1>

                                <span
                                    v-if="hashtag.color"
                                    class="h-4 w-4 rounded-full border border-slate-400"
                                    :style="{ backgroundColor: hashtag.color }"
                                    aria-hidden="true"
                                />
                            </div>

                            <div
                                v-if="hashtagShort"
                                itemprop="abstract"
                                class="mt-2 text-center text-sm text-slate-600 dark:text-slate-300"
                            >
                                {{ hashtagShort }}
                            </div>

                            <div
                                v-if="hashtagDescription"
                                itemprop="text"
                                class="mt-4 text-sm text-slate-700 dark:text-slate-300"
                                v-html="hashtagDescription"
                            />

                            <div
                                class="mt-4 flex flex-wrap justify-center gap-x-5 gap-y-2
                                       text-xs text-slate-500 dark:text-slate-400">
                                <span v-if="hashtagCoursesCount">
                                    {{ t('courses') }}: {{ hashtagCoursesCount }}
                                </span>
                                <span v-if="hashtagModulesCount">
                                    {{ t('modules') }}: {{ hashtagModulesCount }}
                                </span>
                                <span v-if="hashtagLessonsCount">
                                    {{ t('lessons') }}: {{ hashtagLessonsCount }}
                                </span>
                                <span v-if="hashtag.views">
                                    {{ t('views') }}: {{ hashtag.views }}
                                </span>
                                <span v-if="hashtag.likes">
                                    {{ t('likes') }}: {{ hashtag.likes }}
                                </span>
                            </div>
                        </header>

                        <!-- Controls -->
                        <EntityPageToolbar
                            v-model="qCourses"
                            v-model:view-mode="viewMode"
                            v-model:sort-value="sortCourses"
                            :found="effectiveCoursesFound"
                            :sort-options="courseSortOptions"
                            :default-sort="DEFAULT_SORT"
                            :found-label="t('courses')"
                            :search-placeholder="t('searchByName')"
                            @submit="applyFilters"
                            @reset="resetFilters"
                        />

                        <!-- Empty -->
                        <div
                            v-if="!displayedCourses.length"
                            class="rounded-md border border-gray-200 bg-white p-8
                                   text-center text-sm text-slate-500 dark:border-gray-700
                                   dark:bg-gray-900 dark:text-slate-400"
                        >
                            {{ t('nothingFound') }}
                        </div>

                        <!-- Courses -->
                        <template v-else>
                            <CourseGrid
                                v-if="viewMode === 'grid'"
                                :courses="displayedCourses"
                                :cols="courseGridCols"
                            />

                            <CourseRows v-else :courses="displayedCourses" />
                        </template>

                        <!-- Server pagination -->
                        <Pagination
                            v-if="useServerProcessing && lastPage > 1"
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

                        <SectionVideoList :videos="mainVideos" />
                        <SectionBanners :banners="mainBanners" />
                    </div>
                </article>

                <!-- Right sidebar -->
                <aside
                    v-if="showRight"
                    class="shrink-0 pr-3 transition-all duration-300 lg:mt-28"
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

        <!-- Нижняя панель администратора -->
        <PublicAdminBottomPanel
            v-if="isAdmin"
            setting-key="publicSchoolCoursesProcessingMode"
            :mode="publicSchoolCoursesProcessingMode"
            :use-server-processing="useServerProcessing"
            :total="coursesCount"
        />
    </DefaultLayout>
</template>
