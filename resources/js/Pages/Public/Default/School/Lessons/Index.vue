<script setup>
/**
 * Страница списка уроков
 * - шапка, центральная часть, подвал
 * - светлый, тёмный режим
 * - серверный поиск
 * - серверная пагинация
 * - серверная сортировка
 * - показ карточками, в строку
 * - показ главных видео, баннеров внизу страницы
 * - показ, скрытие колонок
 * - показ дерева треков в левой колонке
 * - показ облако хештегов в правой колонке
 *
 * @version PulsarCMS 1.0
 * @author Александр
 */

import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'

import DefaultLayout from '@/Layouts/DefaultLayout.vue'
import Navbar from '@/Partials/Default/Navbar.vue'
import FooterBlog from '@/Partials/Default/FooterBlog.vue'
import Progress from '@/Components/Public/Default/Progress/Progress.vue'
import LeftSidebarSchool from '@/Components/Public/Default/Partials/LeftSidebarSchool.vue'
import RightSidebarSchool from '@/Components/Public/Default/Partials/RightSidebarSchool.vue'
import EntityPageToolbar from '@/Components/Public/Default/PageToolbar/EntityPageToolbar.vue'
import Pagination from '@/Components/Public/Default/Pagination/Pagination.vue'
import SectionVideoList from '@/Components/Public/Default/Blog/Video/SectionVideoList.vue'
import SectionBanners from '@/Components/Public/Default/Blog/Banner/SectionBanners.vue'
import LessonGrid from '@/Components/Public/Default/School/Lesson/LessonGrid.vue'
import LessonRows from '@/Components/Public/Default/School/Lesson/LessonRows.vue'

const { t } = useI18n()

const props = defineProps({
    title: String,
    canLogin: Boolean,
    canRegister: Boolean,

    trackTree: { type: Array, default: () => [] },

    lessons: { type: Object, default: () => ({}) },
    lessonsCount: { type: Number, default: 0 },
    lessonsFound: { type: Number, default: 0 },
    filters: { type: Object, default: () => ({}) },

    mainVideos: { type: [Array, Object], default: () => [] },
    mainBanners: { type: [Array, Object], default: () => [] },
})

const normalizeList = (value) => {
    if (Array.isArray(value)) return value
    if (Array.isArray(value?.data)) return value.data
    return []
}

const trackTree = computed(() => Array.isArray(props.trackTree) ? props.trackTree : [])

const VIEW_KEY = 'public_lessons_view'
const viewMode = ref(localStorage.getItem(VIEW_KEY) || 'grid')

watch(viewMode, (v) => {
    localStorage.setItem(VIEW_KEY, v)
})

const lessonsData = computed(() => {
    const data = props.lessons?.data
    return Array.isArray(data) ? data : []
})

const mainVideosList = computed(() => normalizeList(props.mainVideos))
const mainBannersList = computed(() => normalizeList(props.mainBanners))

const currentPage = computed(() => {
    return Number(props.lessons?.meta?.current_page ?? props.lessons?.current_page ?? 1) || 1
})

const lastPage = computed(() => {
    return Number(props.lessons?.meta?.last_page ?? props.lessons?.last_page ?? 1) || 1
})

const perPage = computed(() => {
    const val = Number(props.filters?.per_page ?? 12)
    return Number.isFinite(val) ? val : 12
})

const q = ref(String(props.filters?.q ?? ''))

const DEFAULT_SORT = 'sort_asc'
const sort = ref(String(props.filters?.sort ?? DEFAULT_SORT))

const lessonSortOptions = [
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
    { value: 'duration_desc', label: t('sortLongest') },
    { value: 'duration_asc', label: t('sortShortest') },
    { value: 'date_desc', label: t('sortNewestFirst') },
    { value: 'date_asc', label: t('sortOldestFirst') },
]

const submitSearch = () => {
    router.get(
        route('public.lessons.index'),
        {
            q: q.value || undefined,
            sort: sort.value || undefined,
            per_page: perPage.value,
            page: 1,
        },
        { preserveState: true, replace: true, preserveScroll: true }
    )
}

const resetSearch = () => {
    q.value = ''
    sort.value = DEFAULT_SORT

    router.get(
        route('public.lessons.index'),
        {
            per_page: perPage.value,
            sort: sort.value,
            page: 1,
        },
        { preserveState: true, replace: true, preserveScroll: true }
    )
}

const goToPage = (page) => {
    const p = Number(page)
    if (!Number.isFinite(p)) return

    const safe = Math.max(1, Math.min(p, lastPage.value))

    router.get(
        route('public.lessons.index'),
        {
            q: q.value || undefined,
            sort: sort.value || undefined,
            per_page: perPage.value,
            page: safe,
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
</script>

<template>
    <Head>
        <title>{{ t('lessons') }}</title>
        <meta name="title" :content="t('lessons')" />
        <meta name="keywords" content="" />
        <meta name="description" :content="t('lessons')" />

        <meta property="og:title" :content="t('lessons')" />
        <meta property="og:description" :content="t('lessons')" />
        <meta property="og:type" content="website" />
        <meta property="og:url" :content="`/school/lessons`" />
        <meta property="og:image" content="" />
        <meta property="og:locale" :content="'ru_RU'" />

        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="t('lessons')" />
        <meta name="twitter:description" :content="t('lessons')" />
        <meta name="twitter:image" content="" />

        <meta name="DC.title" :content="t('lessons')" />
        <meta name="DC.description" :content="t('lessons')" />
        <meta name="DC.identifier" :content="`/school/lessons`" />
        <meta name="DC.language" :content="'ru'" />
    </Head>

    <DefaultLayout :title="title" :can-login="canLogin" :can-register="canRegister">
        <Navbar />

        <div class="min-h-screen px-1.5">
            <main class="mx-auto flex flex-col lg:flex-row gap-4 tracking-wider">
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

                <div class="w-full lg:mt-16 pb-6 slate-1">
                    <div class="mx-auto max-w-6xl">
                        <nav class="text-sm" aria-label="Breadcrumb">
                            <ol class="flex items-center font-semibold">
                                <li>
                                    <Link
                                        :href="route('home')"
                                        class="breadcrumb-link hover:underline"
                                    >
                                        {{ t('home') }}
                                    </Link>
                                </li>
                                <li><span class="mx-2 breadcrumbs">/</span></li>
                                <li class="breadcrumbs">
                                    {{ t('lessons') }}
                                </li>
                            </ol>
                        </nav>

                        <div class="my-3 flex flex-wrap items-center justify-center gap-3 title">
                            <svg class="shrink-0 h-6 w-6 text-slate-600/85 dark:text-slate-200/85"
                                 fill="currentColor"
                                 viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                            </svg>
                            <h1 class="text-2xl font-bold">
                                {{ t('lessons') }}
                            </h1>
                        </div>

                        <div class="my-1 text-sm subtitle text-center">
                            Выберите урок и продолжайте обучение в удобном формате.
                        </div>

                        <EntityPageToolbar
                            v-model="q"
                            :found="lessonsFound"
                            :view-mode="viewMode"
                            :sort-value="sort"
                            :sort-options="lessonSortOptions"
                            :default-sort="DEFAULT_SORT"
                            :found-label="t('lessons')"
                            :search-placeholder="t('searchByName')"
                            @submit="submitSearch"
                            @reset="resetSearch"
                            @update:viewMode="viewMode = $event"
                            @update:sortValue="sort = $event"
                        />

                        <div
                            v-if="lessonsData.length === 0"
                            class="mt-6 text-center text-slate-700 dark:text-slate-300"
                        >
                            {{ t('noData') }}
                        </div>

                        <div v-else>
                            <LessonGrid
                                v-if="viewMode === 'grid'"
                                :lessons="lessonsData"
                                :cols="gridCols"
                            />

                            <LessonRows
                                v-else
                                :lessons="lessonsData"
                            />
                        </div>

                        <Pagination
                            :current-page="currentPage"
                            :last-page="lastPage"
                            :found="lessonsFound"
                            @prev="goPrev"
                            @next="goNext"
                            @go="goToPage"
                        />

                        <SectionVideoList :videos="mainVideosList" />
                        <SectionBanners :banners="mainBannersList" />
                    </div>
                </div>

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
