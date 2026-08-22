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
import InstructorCourseGrid
    from '@/Components/Public/Default/School/SchoolInstructor/InstructorCourseGrid.vue'
import InstructorCourseRows
    from '@/Components/Public/Default/School/SchoolInstructor/InstructorCourseRows.vue'

import SectionVideoList from '@/Components/Public/Default/Blog/BlogVideo/SectionVideoList.vue'
import SectionBanners from '@/Components/Public/Default/Blog/BlogBanner/SectionBanners.vue'

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

const normalizeList = (value) => {
    if (Array.isArray(value)) return value
    if (Array.isArray(value?.data)) return value.data
    return []
}

const normalizeText = (value) => String(value ?? '').trim().toLocaleLowerCase()

/* ======================== Hashtag ======================== */

const hashtag = computed(() => props.hashtag ?? {})
const translation = computed(() => hashtag.value?.translation ?? {})

const hashtagName = computed(() =>
    translation.value?.name || hashtag.value?.slug || t('hashtags')
)

const hashtagShort = computed(() => translation.value?.short || '')
const hashtagDescription = computed(() => translation.value?.description || '')

/* ======================== SEO ======================== */

const seoTitle = computed(() =>
    translation.value?.meta_title || hashtagName.value
)

const seoDescription = computed(() =>
    translation.value?.meta_desc || hashtagShort.value || ''
)

const seoKeywords = computed(() =>
    translation.value?.meta_keywords || ''
)

const contentLocale = computed(() =>
    translation.value?.locale || props.locale || 'ru'
)

const ogLocale = computed(() =>
    contentLocale.value === 'ru' ? 'ru_RU' : contentLocale.value
)

/** Канонический URL */
const canonicalUrl = computed(() => {
    if (!hashtag.value?.slug) return ''

    return String(route('public.schoolHashtags.show', {
        slug: hashtag.value.slug,
    }))
})

/** Дата создания сущности */
const seoCreatedAt = computed(() =>
    hashtag.value?.created_at || ''
)

/**
 * Dublin Core subject.
 *
 * Если meta_keywords заполнены —
 * используем их.
 * Иначе название хештега.
 */
const dcSubject = computed(() =>
    seoKeywords.value || hashtagName.value
)

/* ======================== Courses ======================== */

const coursesData = computed(() => normalizeList(props.courses))

const qCourses = ref(String(props.filters?.q_courses ?? ''))

/**
 * Количество элементов задаётся
 * только backend-настройкой
 * publicSchoolCoursesPerPage.
 */
const perPageCourses = computed(() =>
    Number(props.filters?.per_page_courses ?? 12)
)

const DEFAULT_SORT = 'idDesc'
const sortCourses = ref(String(props.filters?.sort_courses ?? DEFAULT_SORT))

const courseSortOptions = computed(() => [
    { value: 'idDesc', label: t('idDesc') },
    { value: 'idAsc', label: t('idAsc') },
    { value: 'sortAsc', label: t('sortDefault') },
    { value: 'sortDesc', label: t('sortReverse') },
    { value: 'titleAsc', label: t('sortNameAsc') },
    { value: 'titleDesc', label: t('sortNameDesc') },
    { value: 'viewsDesc', label: t('views') },
    { value: 'ratingAvgDesc', label: t('rating') },
    { value: 'studentsDesc', label: t('students') },
])

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

/* ======================== Frontend search ======================== */

const getCourseTitle = (course) => course?.translation?.title || ''
const getCourseShort = (course) => course?.translation?.short || ''

const getInstructorName = (course) =>
    course?.instructorProfile?.translation?.title ||
    course?.instructorProfile?.user?.name ||
    ''

const frontendFilteredCourses = computed(() => {
    if (props.useServerProcessing) return coursesData.value

    const query = normalizeText(qCourses.value)
    if (!query) return coursesData.value

    return coursesData.value.filter((course) => {
        return [
            course?.id,
            course?.slug,
            getCourseTitle(course),
            getCourseShort(course),
            getInstructorName(course),
            course?.level,
            course?.availability,
        ].some((value) => normalizeText(value).includes(query))
    })
})

/* ======================== Frontend sort ======================== */

const compareText = (a, b) =>
    String(a ?? '').localeCompare(String(b ?? ''),
        props.locale,
        { sensitivity: 'base' })

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
                return compareText(getCourseTitle(a), getCourseTitle(b))

            case 'titleDesc':
                return compareText(getCourseTitle(b), getCourseTitle(a))

            case 'viewsAsc':
                return compareNumber(a?.views, b?.views)

            case 'viewsDesc':
                return compareNumber(b?.views, a?.views)

            case 'ratingAvgAsc':
                return compareNumber(a?.rating_avg, b?.rating_avg)

            case 'ratingAvgDesc':
                return compareNumber(b?.rating_avg, a?.rating_avg)

            case 'studentsAsc':
                return compareNumber(a?.students_count, b?.students_count)

            case 'studentsDesc':
                return compareNumber(b?.students_count, a?.students_count)

            case 'durationAsc':
                return compareNumber(a?.duration, b?.duration)

            case 'durationDesc':
                return compareNumber(b?.duration, a?.duration)

            case 'createdAtAsc':
            case 'dateAsc':
                return compareText(a?.created_at, b?.created_at)

            case 'createdAtDesc':
            case 'dateDesc':
                return compareText(b?.created_at, a?.created_at)

            default:
                return 0
        }
    })

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
    if (!props.useServerProcessing) return

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
    if (props.useServerProcessing) {
        loadServerCourses(1)
    } else {
        frontendCurrentPage.value = 1
    }
}

const resetFilters = () => {
    qCourses.value = ''
    sortCourses.value = DEFAULT_SORT

    if (props.useServerProcessing) {
        loadServerCourses(1)
    } else {
        frontendCurrentPage.value = 1
    }
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

/** Администратор */
const isAdmin = computed(() => page.props?.isAdmin === true)

const showLeft = computed(() =>
    !siteSettings?.ViewLeftColumn || siteSettings.ViewLeftColumn === 'true'
)

const showRight = computed(() =>
    !siteSettings?.ViewRightColumn || siteSettings.ViewRightColumn === 'true'
)

const LEFT_SIDEBAR_KEY = 'public_left_sidebar_collapsed'
const RIGHT_SIDEBAR_KEY = 'public_right_sidebar_collapsed'

/**
 * На первом render сайдбары свёрнуты.
 * После mounted восстанавливаем состояние
 * из localStorage.
 *
 * transition-all создаёт естественный
 * эффект раскрытия страницы.
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
const courseGridCols = computed(() => {
    const leftExpanded = showLeft.value && !leftCollapsed.value
    const rightExpanded = showRight.value && !rightCollapsed.value

    if (leftExpanded && rightExpanded) return 2
    if (leftExpanded || rightExpanded) return 3

    return 4
})

/* ======================== Sidebar data ======================== */

const trackTree = computed(() =>
    Array.isArray(props.trackTree) ? props.trackTree : []
)

const mainVideos = computed(() => normalizeList(props.mainVideos))
const mainBanners = computed(() => normalizeList(props.mainBanners))
</script>

<template>
    <Head>
        <!-- Основные SEO -->
        <title>{{ seoTitle }}</title>

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
        <meta property="og:type" content="website">
        <meta property="og:title" :content="seoTitle">

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

        <!-- Twitter / X -->
        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" :content="seoTitle">

        <meta
            v-if="seoDescription"
            name="twitter:description"
            :content="seoDescription"
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

        <meta
            name="DC.language"
            :content="contentLocale"
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

        <meta
            v-if="seoCreatedAt"
            name="DC.date"
            :content="seoCreatedAt"
        >
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
                    <!-- Schema.org metadata -->
                    <meta itemprop="name" :content="hashtagName">

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
                        :content="contentLocale"
                    >

                    <meta
                        v-if="seoCreatedAt"
                        itemprop="dateCreated"
                        :content="seoCreatedAt"
                    >

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

                                    <meta itemprop="position" content="4">
                                </li>

                                <li
                                    itemprop="itemListElement"
                                    itemscope
                                    itemtype="https://schema.org/ListItem"
                                    class="flex items-center"
                                    aria-current="page"
                                >
                                    <span class="mx-2 breadcrumbs">/</span>
                                    <span itemprop="name" class="breadcrumbs">{{ hashtagName }}</span>

                                    <meta
                                        v-if="canonicalUrl"
                                        itemprop="item"
                                        :content="canonicalUrl"
                                    >

                                    <meta itemprop="position" content="5">
                                </li>
                            </ol>
                        </nav>

                        <!-- Hashtag -->
                        <div
                            class="mb-5 rounded-md border border-gray-200 bg-white p-4 shadow-sm
                                   dark:border-gray-700 dark:bg-gray-900"
                        >
                            <div class="flex flex-wrap items-center justify-center gap-3">
                                <h1
                                    itemprop="headline"
                                    class="text-2xl font-bold text-slate-800 dark:text-slate-100"
                                >
                                    #{{ hashtagName }}
                                </h1>

                                <span
                                    v-if="hashtag.color"
                                    class="h-4 w-4 rounded-full border border-slate-400"
                                    :style="{ backgroundColor: hashtag.color }"
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
                        </div>

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
                            class="rounded-md border border-gray-200 bg-white p-8 text-center text-sm
                                   text-slate-500 dark:border-gray-700 dark:bg-gray-900 dark:text-slate-400"
                        >
                            {{ t('nothingFound') }}
                        </div>

                        <!-- Courses -->
                        <template v-else>
                            <InstructorCourseGrid
                                v-if="viewMode === 'grid'"
                                :courses="displayedCourses"
                                :cols="courseGridCols"
                            />

                            <InstructorCourseRows
                                v-else
                                :courses="displayedCourses"
                            />
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
