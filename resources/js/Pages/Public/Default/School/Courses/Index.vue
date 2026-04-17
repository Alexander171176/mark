<script setup>
/**
 * Страница списка курсов
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
import InstructorCourseGrid from '@/Components/Public/Default/School/Instructor/InstructorCourseGrid.vue'
import InstructorCourseRows from '@/Components/Public/Default/School/Instructor/InstructorCourseRows.vue'

const { t } = useI18n()

/** Props */
const props = defineProps({
    title: String,
    canLogin: Boolean,
    canRegister: Boolean,

    trackTree: { type: Array, default: () => [] },

    courses: { type: Object, default: () => ({}) },
    coursesCount: { type: Number, default: 0 },
    coursesFound: { type: Number, default: 0 },
    filters: { type: Object, default: () => ({}) },

    hashtags: { type: Array, default: () => [] },
    mainVideos: { type: Array, default: () => [] },
    mainBanners: { type: Array, default: () => [] },
})

/** Иерархия треков */
const trackTree = computed(() => Array.isArray(props.trackTree) ? props.trackTree : [])

/** Режим показа карточками/в строку */
const VIEW_KEY = 'public_courses_view'
const viewMode = ref(localStorage.getItem(VIEW_KEY) || 'grid')

watch(viewMode, (v) => {
    localStorage.setItem(VIEW_KEY, v)
})

/** Данные курсов */
const coursesData = computed(() => {
    const data = props.courses?.data
    return Array.isArray(data) ? data : []
})

/** Текущая страница, Количество страниц */
const currentPage = computed(() => {
    return Number(props.courses?.meta?.current_page ?? props.courses?.current_page ?? 1) || 1
})

const lastPage = computed(() => {
    return Number(props.courses?.meta?.last_page ?? props.courses?.last_page ?? 1) || 1
})

const perPage = computed(() => {
    const val = Number(props.filters?.per_page ?? 12)
    return Number.isFinite(val) ? val : 12
})

/** Поиск */
const q = ref(String(props.filters?.q ?? ''))

/** Сортировка */
const DEFAULT_SORT = 'sort_asc'
const sort = ref(String(props.filters?.sort ?? DEFAULT_SORT))

/** Опции сортировки */
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

/** Поисковый запрос */
const submitSearch = () => {
    router.get(
        route('public.courses.index'),
        {
            q: q.value || undefined,
            sort: sort.value || undefined,
            per_page: perPage.value,
            page: 1,
        },
        { preserveState: true, replace: true, preserveScroll: true }
    )
}

/** Сброс поиска */
const resetSearch = () => {
    q.value = ''
    sort.value = DEFAULT_SORT

    router.get(
        route('public.courses.index'),
        {
            per_page: perPage.value,
            sort: sort.value,
            page: 1,
        },
        { preserveState: true, replace: true, preserveScroll: true }
    )
}

/** Пагинация: prev / input / next */
const goToPage = (page) => {
    const p = Number(page)
    if (!Number.isFinite(p)) return

    const safe = Math.max(1, Math.min(p, lastPage.value))

    router.get(
        route('public.courses.index'),
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

/** показ/скрытие колонок */
const { siteSettings } = usePage().props

const showLeft = computed(() =>
    !siteSettings?.ViewLeftColumn || siteSettings.ViewLeftColumn === 'true'
)

const showRight = computed(() =>
    !siteSettings?.ViewRightColumn || siteSettings.ViewRightColumn === 'true'
)

/** состояние сайдбаров */
const leftCollapsed = ref(false)
const rightCollapsed = ref(false)

/** добавление третьей карточки в ряд при свернутой колонке */
const gridCols = computed(() => {
    const leftExpanded = showLeft.value && !leftCollapsed.value
    const rightExpanded = showRight.value && !rightCollapsed.value

    return leftExpanded && rightExpanded ? 2 : 3
})
</script>

<template>
    <!-- SEO -->
    <Head>
        <title>{{ t('courses') }}</title>
        <meta name="title" :content="t('courses')" />
        <meta name="keywords" content="" />
        <meta name="description" :content="t('courses')" />

        <meta property="og:title" :content="t('courses')" />
        <meta property="og:description" :content="t('courses')" />
        <meta property="og:type" content="website" />
        <meta property="og:url" :content="`/school/courses`" />
        <meta property="og:image" content="" />
        <meta property="og:locale" :content="'ru_RU'" />

        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="t('courses')" />
        <meta name="twitter:description" :content="t('courses')" />
        <meta name="twitter:image" content="" />

        <meta name="DC.title" :content="t('courses')" />
        <meta name="DC.description" :content="t('courses')" />
        <meta name="DC.identifier" :content="`/school/courses`" />
        <meta name="DC.language" :content="'ru'" />
    </Head>

    <DefaultLayout :title="title" :can-login="canLogin" :can-register="canRegister">
        <!-- Шапка -->
        <Navbar />

        <div class="min-h-screen px-1.5">
            <main class="mx-auto flex flex-col lg:flex-row gap-4 tracking-wider">
                <!-- Левая колонка -->
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

                <!-- Центральный контент -->
                <div class="w-full lg:mt-16 pb-6 slate-1">
                    <div class="mx-auto max-w-6xl">

                        <!-- Хлебные крошки -->
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
                                    {{ t('courses') }}
                                </li>
                            </ol>
                        </nav>

                        <!-- Заголовок -->
                        <div class="my-3 flex flex-wrap items-center justify-center gap-3 title">
                            <svg class="shrink-0 h-5 w-5 text-slate-600/85 dark:text-slate-200/85"
                                 fill="currentColor"
                                 viewBox="0 0 448 512">
                                <path d="M318.38 208h-39.09c-1.49 27.03-6.54 51.35-14.21 70.41 27.71-13.24 48.02-39.19 53.3-70.41zm0-32c-5.29-31.22-25.59-57.17-53.3-70.41 7.68 19.06 12.72 43.38 14.21 70.41h39.09zM224 97.31c-7.69 7.45-20.77 34.42-23.43 78.69h46.87c-2.67-44.26-15.75-71.24-23.44-78.69zm-41.08 8.28c-27.71 13.24-48.02 39.19-53.3 70.41h39.09c1.49-27.03 6.53-51.35 14.21-70.41zm0 172.82c-7.68-19.06-12.72-43.38-14.21-70.41h-39.09c5.28 31.22 25.59 57.17 53.3 70.41zM247.43 208h-46.87c2.66 44.26 15.74 71.24 23.43 78.69 7.7-7.45 20.78-34.43 23.44-78.69zM448 358.4V25.6c0-16-9.6-25.6-25.6-25.6H96C41.6 0 0 41.6 0 96v320c0 54.4 41.6 96 96 96h326.4c12.8 0 25.6-9.6 25.6-25.6v-16c0-6.4-3.2-12.8-9.6-19.2-3.2-16-3.2-60.8 0-73.6 6.4-3.2 9.6-9.6 9.6-19.2zM224 64c70.69 0 128 57.31 128 128s-57.31 128-128 128S96 262.69 96 192 153.31 64 224 64zm160 384H96c-19.2 0-32-12.8-32-32s16-32 32-32h288v64z"/>
                            </svg>
                            <h1 class="text-2xl font-bold">
                                {{ t('courses') }}
                            </h1>
                        </div>

                        <!-- Подзаголовок -->
                        <div class="my-1 text-sm subtitle text-center">
                            Найдите идеальный курс для вашего развития.
                        </div>

                        <!-- Поиск, количество, сортировка, вид -->
                        <EntityPageToolbar
                            v-model="q"
                            :found="coursesFound"
                            :view-mode="viewMode"
                            :sort-value="sort"
                            :sort-options="courseSortOptions"
                            :default-sort="DEFAULT_SORT"
                            :found-label="t('courses')"
                            :search-placeholder="t('searchByName')"
                            @submit="submitSearch"
                            @reset="resetSearch"
                            @update:viewMode="viewMode = $event"
                            @update:sortValue="sort = $event"
                        />

                        <!-- Нет данных -->
                        <div
                            v-if="coursesData.length === 0"
                            class="mt-6 text-center text-slate-700 dark:text-slate-300"
                        >
                            {{ t('noData') }}
                        </div>

                        <!-- Показ grid/rows -->
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

                        <!-- Пагинация -->
                        <Pagination
                            :current-page="currentPage"
                            :last-page="lastPage"
                            :found="coursesFound"
                            @prev="goPrev"
                            @next="goNext"
                            @go="goToPage"
                        />

                        <!-- Главные видео, баннеры -->
                        <SectionVideoList :videos="mainVideos" />
                        <SectionBanners :banners="mainBanners" />
                    </div>
                </div>

                <!-- Правая колонка -->
                <aside
                    v-if="showRight"
                    class="shrink-0 lg:mt-16 pr-3 transition-all duration-300"
                    :class="rightCollapsed ? 'lg:w-10' : 'lg:w-64'"
                >
                    <RightSidebarSchool @collapsed="rightCollapsed = $event" />
                </aside>
            </main>
        </div>

        <!-- Подвал и кнопка с прогрессом -->
        <FooterBlog />
        <Progress />
    </DefaultLayout>
</template>
