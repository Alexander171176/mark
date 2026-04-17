<script setup>
/**
 * Страница конкретного хештега
 * - SEO
 * - хлебные крошки
 * - показ главных видео, баннеров внизу страницы
 * - показ, скрытие колонок
 * - показ дерева треков в левой колонке
 * - показ облако хештегов в правой колонке
 *
 * @version PulsarCMS 1.0
 * @author Александр
 */
import { computed, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { Head, Link, router, usePage } from '@inertiajs/vue3'

import DefaultLayout from '@/Layouts/DefaultLayout.vue'
import Navbar from '@/Partials/Default/Navbar.vue'
import FooterBlog from '@/Partials/Default/FooterBlog.vue'
import Progress from '@/Components/Public/Default/Progress/Progress.vue'
import LeftSidebarSchool from '@/Components/Public/Default/Partials/LeftSidebarSchool.vue'
import RightSidebarSchool from '@/Components/Public/Default/Partials/RightSidebarSchool.vue'
import EntityPageToolbar from '@/Components/Public/Default/PageToolbar/EntityPageToolbar.vue'
import InstructorCourseGrid from '@/Components/Public/Default/School/Instructor/InstructorCourseGrid.vue'
import InstructorCourseRows from '@/Components/Public/Default/School/Instructor/InstructorCourseRows.vue'
import Pagination from '@/Components/Public/Default/Pagination/Pagination.vue'
import SectionVideoList from '@/Components/Public/Default/Blog/Video/SectionVideoList.vue'
import SectionBanners from '@/Components/Public/Default/Blog/Banner/SectionBanners.vue'

const { t } = useI18n()

const props = defineProps({
    title: String,
    canLogin: Boolean,
    canRegister: Boolean,

    hashtag: { type: Object, default: () => ({}) },

    courses: { type: Object, default: () => ({}) },
    coursesFound: { type: Number, default: 0 },
    filters: { type: Object, default: () => ({}) },

    trackTree: { type: Array, default: () => [] },

    mainVideos: { type: Array, default: () => [] },
    mainBanners: { type: Array, default: () => [] },
})

/** Иерархия треков */
const trackTree = computed(() => Array.isArray(props.trackTree) ? props.trackTree : [])

/** Режим показа */
const VIEW_KEY = 'public_hashtag_courses_view'
const viewMode = ref(localStorage.getItem(VIEW_KEY) || 'grid')

watch(viewMode, (v) => {
    localStorage.setItem(VIEW_KEY, v)
})

/** Данные курсов */
const coursesData = computed(() => {
    const data = props.courses?.data
    return Array.isArray(data) ? data : []
})

/** Пагинация */
const currentPage = computed(() => {
    return Number(props.courses?.meta?.current_page ?? props.courses?.current_page ?? 1) || 1
})

const lastPage = computed(() => {
    return Number(props.courses?.meta?.last_page ?? props.courses?.last_page ?? 1) || 1
})

const perPageCourses = computed(() => {
    const val = Number(props.filters?.per_page_courses ?? 12)
    return Number.isFinite(val) ? val : 12
})

/** Поиск */
const qCourses = ref(String(props.filters?.q_courses ?? ''))

/** Сортировка */
const DEFAULT_SORT = 'date_desc'
const sortCourses = ref(String(props.filters?.sort_courses ?? DEFAULT_SORT))

const courseSortOptions = [
    { value: 'sort_asc', label: t('sortDefault') },
    { value: 'sort_desc', label: t('sortReverse') },
    { value: 'title_asc', label: t('sortNameAsc') },
    { value: 'title_desc', label: t('sortNameDesc') },
    { value: 'views_desc', label: t('sortPopularFirst') },
    { value: 'views_asc', label: t('sortUnpopularFirst') },
    { value: 'likes_desc', label: t('sortLikesDesc') },
    { value: 'likes_asc', label: t('sortLikesAsc') },
    { value: 'rating_desc', label: t('ratingDesc') },
    { value: 'rating_asc', label: t('ratingAsc') },
    { value: 'popularity_desc', label: t('sortPopularFirst') },
    { value: 'popularity_asc', label: t('sortUnpopularFirst') },
    { value: 'students_desc', label: t('sortMostStudents') },
    { value: 'students_asc', label: t('sortLeastStudents') },
    { value: 'duration_desc', label: t('sortLongest') },
    { value: 'duration_asc', label: t('sortShortest') },
    { value: 'date_desc', label: t('sortNewestFirst') },
    { value: 'date_asc', label: t('sortOldestFirst') },
]

/** Поиск */
const submitCourseSearch = () => {
    router.get(
        route('public.hashtags.show', props.hashtag.slug),
        {
            q_courses: qCourses.value || undefined,
            sort_courses: sortCourses.value || undefined,
            per_page_courses: perPageCourses.value,
            page_courses: 1,
        },
        { preserveState: true, replace: true, preserveScroll: true }
    )
}

const resetCourseSearch = () => {
    qCourses.value = ''
    sortCourses.value = DEFAULT_SORT

    router.get(
        route('public.hashtags.show', props.hashtag.slug),
        {
            per_page_courses: perPageCourses.value,
            sort_courses: sortCourses.value,
            page_courses: 1,
        },
        { preserveState: true, replace: true, preserveScroll: true }
    )
}

/** Пагинация */
const goToPage = (page) => {
    const p = Number(page)
    if (!Number.isFinite(p)) return

    const safe = Math.max(1, Math.min(p, lastPage.value))

    router.get(
        route('public.hashtags.show', props.hashtag.slug),
        {
            q_courses: qCourses.value || undefined,
            sort_courses: sortCourses.value || undefined,
            per_page_courses: perPageCourses.value,
            page_courses: safe,
        },
        { preserveState: true, replace: true, preserveScroll: true }
    )
}

const goPrev = () => {
    if (currentPage.value <= 1) return
    goToPage(currentPage.value - 1)
}

const goNext = () => {
    if (currentPage.value >= lastPage.value) return
    goToPage(currentPage.value + 1)
}

/** Колонки */
const { siteSettings } = usePage().props

const showLeft = computed(() =>
    !siteSettings?.ViewLeftColumn || siteSettings.ViewLeftColumn === 'true'
)

const showRight = computed(() =>
    !siteSettings?.ViewRightColumn || siteSettings.ViewRightColumn === 'true'
)

const leftCollapsed = ref(false)
const rightCollapsed = ref(false)

const gridCols = computed(() => {
    const leftExpanded = showLeft.value && !leftCollapsed.value
    const rightExpanded = showRight.value && !rightCollapsed.value

    return leftExpanded && rightExpanded ? 2 : 3
})

/** Количество курсов */
const coursesCount = computed(() => {
    return Number(props.hashtag?.courses_count ?? props.coursesFound ?? 0) || 0
})

const hasCourses = computed(() => coursesCount.value > 0)

const normalizeList = (value) => {
    if (Array.isArray(value)) return value
    if (Array.isArray(value?.data)) return value.data
    return []
}

/** нормализация массивов банеров и видео */
const mainVideosList = computed(() => normalizeList(props.mainVideos))
const mainBannersList = computed(() => normalizeList(props.mainBanners))
</script>

<template>
    <Head>
        <title>{{ hashtag.name || '' }}</title>
        <meta name="title" :content="hashtag.meta_title || hashtag.name || ''" />
        <meta name="keywords" :content="hashtag.meta_keywords || ''" />
        <meta name="description" :content="hashtag.meta_desc || hashtag.short || ''" />

        <meta property="og:title" :content="hashtag.meta_title || hashtag.name || ''" />
        <meta property="og:description" :content="hashtag.meta_desc || hashtag.short || ''" />
        <meta property="og:type" content="website" />
        <meta property="og:url" :content="`/school/hashtags/${hashtag.slug || ''}`" />
        <meta property="og:image" content="" />
        <meta property="og:locale" :content="hashtag.locale || 'ru_RU'" />

        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="hashtag.meta_title || hashtag.name || ''" />
        <meta name="twitter:description" :content="hashtag.meta_desc || hashtag.short || ''" />
        <meta name="twitter:image" content="" />

        <meta name="DC.title" :content="hashtag.meta_title || hashtag.name || ''" />
        <meta name="DC.description" :content="hashtag.meta_desc || hashtag.short || ''" />
        <meta name="DC.identifier" :content="`/school/hashtags/${hashtag.slug || ''}`" />
        <meta name="DC.language" :content="hashtag.locale || 'ru'" />
    </Head>

    <DefaultLayout :title="title" :can-login="canLogin" :can-register="canRegister">
        <Navbar />

        <div class="min-h-screen px-1.5">
            <main class="mx-auto flex flex-col lg:flex-row gap-4 tracking-wider">
                <!-- LEFT -->
                <aside
                    v-if="showLeft"
                    class="shrink-0 mt-12 sm:mt-16 pl-3 transition-all duration-300"
                    :class="leftCollapsed ? 'lg:w-10' : 'lg:w-64'"
                >
                    <LeftSidebarSchool
                        :track-tree="trackTree"
                        @collapsed="leftCollapsed = $event"
                    />
                </aside>

                <!-- CENTER -->
                <div class="w-full lg:mt-16 pb-6 slate-1">
                    <div class="mx-auto max-w-6xl">

                        <!-- Breadcrumbs -->
                        <nav class="text-sm mb-3" aria-label="Breadcrumb">
                            <ol class="flex items-center font-semibold">
                                <li>
                                    <Link :href="route('home')" class="breadcrumb-link hover:underline">
                                        {{ t('home') }}
                                    </Link>
                                </li>
                                <li><span class="mx-2 breadcrumbs">/</span></li>
                                <li>
                                    <Link :href="route('public.tracks.index')" class="breadcrumb-link hover:underline">
                                        {{ t('tracks') }}
                                    </Link>
                                </li>
                                <li><span class="mx-2 breadcrumbs">/</span></li>
                                <li>
                                    <Link :href="route('public.courses.index')" class="breadcrumb-link hover:underline">
                                        {{ t('courses') }}
                                    </Link>
                                </li>
                                <li><span class="mx-2 breadcrumbs">/</span></li>
                                <li class="breadcrumbs">
                                    {{ t('hashtags') }}: #{{ hashtag.name }}
                                </li>
                            </ol>
                        </nav>

                        <!-- Header panel -->
                        <div class="flex items-center justify-between gap-1">
                            <!-- courses count -->
                            <div
                                :title="t('courses')"
                                class="flex items-center justify-center gap-1"
                            >
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
                                <span v-if="hasCourses" class="text-center text-sm text-gray-500">
                                    {{ coursesCount }} ·
                                </span>
                            </div>

                            <!-- title -->
                            <div class="flex flex-wrap items-center justify-center gap-3 title my-3">
                                <h1 class="text-2xl font-bold">
                                    #{{ hashtag.name }}
                                </h1>
                            </div>

                            <!-- views -->
                            <div
                                :title="t('views')"
                                class="flex items-center justify-center gap-1"
                            >
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
                                    {{ hashtag.views || 0 }} ·
                                </span>
                            </div>
                        </div>

                        <!-- description -->
                        <div
                            v-if="hashtag.description"
                            class="mt-1 mb-3 text-sm subtitle text-center"
                            v-html="hashtag.description"
                        />

                        <!-- Toolbar -->
                        <EntityPageToolbar
                            v-if="hasCourses"
                            v-model="qCourses"
                            :found="coursesFound"
                            :view-mode="viewMode"
                            :sort-value="sortCourses"
                            :sort-options="courseSortOptions"
                            :default-sort="DEFAULT_SORT"
                            :found-label="t('courses')"
                            :search-placeholder="t('searchByName')"
                            @submit="submitCourseSearch"
                            @reset="resetCourseSearch"
                            @update:viewMode="viewMode = $event"
                            @update:sortValue="sortCourses = $event"
                        />

                        <!-- Empty -->
                        <div
                            v-if="coursesData.length === 0"
                            class="mt-6 text-center text-slate-700 dark:text-slate-300"
                        >
                            {{ t('noData') }}
                        </div>

                        <!-- Views -->
                        <div v-else>
                            <InstructorCourseGrid
                                v-if="viewMode === 'grid'"
                                :courses="coursesData"
                                :cols="gridCols"
                            />

                            <InstructorCourseRows
                                v-else
                                :courses="coursesData"
                            />
                        </div>

                        <!-- Pagination -->
                        <Pagination
                            v-if="hasCourses"
                            :current-page="currentPage"
                            :last-page="lastPage"
                            :found="coursesFound"
                            @prev="goPrev"
                            @next="goNext"
                            @go="goToPage"
                        />

                        <!-- Bottom blocks -->
                        <SectionVideoList :videos="mainVideosList" />
                        <SectionBanners :banners="mainBannersList" />
                    </div>
                </div>

                <!-- RIGHT -->
                <aside
                    v-if="showRight"
                    class="shrink-0 lg:mt-16 pr-3 transition-all duration-300"
                    :class="rightCollapsed ? 'lg:w-10' : 'lg:w-64'"
                >
                    <RightSidebarSchool @collapsed="rightCollapsed = $event" />
                </aside>
            </main>
        </div>

        <FooterBlog />
        <Progress />
    </DefaultLayout>
</template>
