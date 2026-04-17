<script setup>
/**
 * Страница списка модулей
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
import CourseModuleGrid from '@/Components/Public/Default/School/Module/CourseModuleGrid.vue'
import CourseModuleRows from '@/Components/Public/Default/School/Module/CourseModuleRows.vue'

const { t } = useI18n()

/** Props */
const props = defineProps({
    title: String,
    canLogin: Boolean,
    canRegister: Boolean,

    trackTree: { type: Array, default: () => [] },

    modules: { type: Object, default: () => ({}) },
    modulesCount: { type: Number, default: 0 },
    modulesFound: { type: Number, default: 0 },
    filters: { type: Object, default: () => ({}) },

    mainVideos: { type: [Array, Object], default: () => [] },
    mainBanners: { type: [Array, Object], default: () => [] },
})

/** Нормализация списков */
const normalizeList = (value) => {
    if (Array.isArray(value)) return value
    if (Array.isArray(value?.data)) return value.data
    return []
}

/** Иерархия треков */
const trackTree = computed(() => Array.isArray(props.trackTree) ? props.trackTree : [])

/** Режим показа карточками/в строку */
const VIEW_KEY = 'public_modules_view'
const viewMode = ref(localStorage.getItem(VIEW_KEY) || 'grid')

watch(viewMode, (v) => {
    localStorage.setItem(VIEW_KEY, v)
})

/** Данные модулей */
const modulesData = computed(() => {
    const data = props.modules?.data
    return Array.isArray(data) ? data : []
})

/** Видео и баннеры */
const mainVideosList = computed(() => normalizeList(props.mainVideos))
const mainBannersList = computed(() => normalizeList(props.mainBanners))

/** Текущая страница, Количество страниц */
const currentPage = computed(() => {
    return Number(props.modules?.meta?.current_page ?? props.modules?.current_page ?? 1) || 1
})

const lastPage = computed(() => {
    return Number(props.modules?.meta?.last_page ?? props.modules?.last_page ?? 1) || 1
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
const moduleSortOptions = [
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
    { value: 'lessons_desc', label: t('sortMostLessons') },
    { value: 'lessons_asc', label: t('sortLeastLessons') },
    { value: 'duration_desc', label: t('sortLongest') },
    { value: 'duration_asc', label: t('sortShortest') },
    { value: 'date_desc', label: t('sortNewestFirst') },
    { value: 'date_asc', label: t('sortOldestFirst') },
]

/** Поисковый запрос */
const submitSearch = () => {
    router.get(
        route('public.modules.index'),
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
        route('public.modules.index'),
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
        route('public.modules.index'),
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
    <Head>
        <title>{{ t('modules') }}</title>
        <meta name="title" :content="t('modules')" />
        <meta name="keywords" content="" />
        <meta name="description" :content="t('modules')" />

        <meta property="og:title" :content="t('modules')" />
        <meta property="og:description" :content="t('modules')" />
        <meta property="og:type" content="website" />
        <meta property="og:url" :content="`/school/modules`" />
        <meta property="og:image" content="" />
        <meta property="og:locale" :content="'ru_RU'" />

        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="t('modules')" />
        <meta name="twitter:description" :content="t('modules')" />
        <meta name="twitter:image" content="" />

        <meta name="DC.title" :content="t('modules')" />
        <meta name="DC.description" :content="t('modules')" />
        <meta name="DC.identifier" :content="`/school/modules`" />
        <meta name="DC.language" :content="'ru'" />
    </Head>

    <DefaultLayout :title="title" :can-login="canLogin" :can-register="canRegister">
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
                                    {{ t('modules') }}
                                </li>
                            </ol>
                        </nav>

                        <!-- Заголовок -->
                        <div class="my-3 flex flex-wrap items-center justify-center gap-3 title">
                            <svg class="shrink-0 h-5 w-5 text-slate-600/85 dark:text-slate-200/85"
                                 fill="currentColor"
                                 viewBox="0 0 24 24">
                                <rect x="1" y="1" width="10" height="10" rx="2"></rect>
                                <path class="fill-current text-slate-400"
                                      d="M23.428,4.618,19.381.572h0a1.957,1.957,0,0,0-2.762,0L12.572,4.618a1.959,1.959,0,0,0,0,2.764l4.047,4.047a1.957,1.957,0,0,0,2.762,0l4.047-4.046A1.959,1.959,0,0,0,23.428,4.618Z"></path>
                                <rect x="13" y="13" width="10" height="10" rx="2"></rect>
                                <rect x="1" y="13" width="10" height="10" rx="2"></rect>
                            </svg>
                            <h1 class="text-2xl font-bold">
                                {{ t('modules') }}
                            </h1>
                        </div>

                        <!-- Подзаголовок -->
                        <div class="my-1 text-sm subtitle text-center">
                            Изучайте модули и выбирайте подходящий путь обучения.
                        </div>

                        <!-- Поиск, количество, сортировка, вид -->
                        <EntityPageToolbar
                            v-model="q"
                            :found="modulesFound"
                            :view-mode="viewMode"
                            :sort-value="sort"
                            :sort-options="moduleSortOptions"
                            :default-sort="DEFAULT_SORT"
                            :found-label="t('modules')"
                            :search-placeholder="t('searchByName')"
                            @submit="submitSearch"
                            @reset="resetSearch"
                            @update:viewMode="viewMode = $event"
                            @update:sortValue="sort = $event"
                        />

                        <!-- Нет данных -->
                        <div
                            v-if="modulesData.length === 0"
                            class="mt-6 text-center text-slate-700 dark:text-slate-300"
                        >
                            {{ t('noData') }}
                        </div>

                        <!-- Показ grid/rows -->
                        <div v-else>
                            <CourseModuleGrid
                                v-if="viewMode === 'grid'"
                                :modules="modulesData"
                                :cols="gridCols"
                            />

                            <CourseModuleRows
                                v-else
                                :modules="modulesData"
                            />
                        </div>

                        <!-- Пагинация -->
                        <Pagination
                            :current-page="currentPage"
                            :last-page="lastPage"
                            :found="modulesFound"
                            @prev="goPrev"
                            @next="goNext"
                            @go="goToPage"
                        />

                        <!-- Главные видео, баннеры -->
                        <SectionVideoList :videos="mainVideosList" />
                        <SectionBanners :banners="mainBannersList" />
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

        <FooterBlog />
        <Progress />
    </DefaultLayout>
</template>
