<script setup>
/**
 * Страница списка направлений обучения.
 *
 * Логика:
 * - серверный поиск
 * - серверная сортировка
 * - серверная пагинация
 * - переключение вида grid/rows
 * - сохранение вида в localStorage
 * - управление колонками через настройки сайта
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
import SectionVideoList from '@/Components/Public/Default/Blog/BlogVideo/SectionVideoList.vue'
import SectionBanners from '@/Components/Public/Default/Blog/BlogBanner/SectionBanners.vue'

import TrackGrid from '@/Components/Public/Default/School/SchoolTrack/TrackGrid.vue'
import TrackRows from '@/Components/Public/Default/School/SchoolTrack/TrackRows.vue'

const { t } = useI18n()

const props = defineProps({
    title: { type: String, default: '' },
    canLogin: { type: Boolean, default: false },
    canRegister: { type: Boolean, default: false },

    trackTree: { type: Array, default: () => [] },

    tracks: { type: Object, default: () => ({}) },
    tracksCount: { type: Number, default: 0 },
    tracksFound: { type: Number, default: 0 },

    filters: { type: Object, default: () => ({}) },

    mainVideos: { type: Array, default: () => [] },
    mainBanners: { type: Array, default: () => [] },

    locale: { type: String, default: 'ru' },
})

/** Дерево треков для левого аккордеона */
const trackTree = computed(() => Array.isArray(props.trackTree) ? props.trackTree : [])

/** Данные треков из пагинатора */
const tracksData = computed(() => {
    const data = props.tracks?.data

    return Array.isArray(data) ? data : []
})

/** Текущая страница */
const currentPage = computed(() => {
    return Number(props.tracks?.meta?.current_page ?? props.tracks?.current_page ?? 1) || 1
})

/** Последняя страница */
const lastPage = computed(() => {
    return Number(props.tracks?.meta?.last_page ?? props.tracks?.last_page ?? 1) || 1
})

/** Количество элементов на странице */
const perPage = computed(() => {
    const value = Number(props.filters?.per_page ?? 20)

    return Number.isFinite(value) ? value : 20
})

/** Поисковая строка */
const q = ref(String(props.filters?.q ?? ''))

/** Сортировка */
const DEFAULT_SORT = 'sortAsc'
const sort = ref(String(props.filters?.sort ?? DEFAULT_SORT))

/** Режим отображения: карточки или строки */
const VIEW_KEY = 'public_school_tracks_view'

const viewMode = ref(
    String(props.filters?.view || localStorage.getItem(VIEW_KEY) || 'grid')
)

/** Сохраняем режим отображения локально */
watch(viewMode, (value) => {
    localStorage.setItem(VIEW_KEY, value)
})

/** Опции сортировки направлений обучения */
const trackSortOptions = [
    { value: 'sortAsc', label: `${t('sortNumber')} 0→9` },
    { value: 'sortDesc', label: `${t('sortNumber')} 9→0` },

    { value: 'idDesc', label: t('idDesc') },
    { value: 'idAsc', label: t('idAsc') },

    { value: 'nameAsc', label: `${t('title')} A→Z` },
    { value: 'nameDesc', label: `${t('title')} Z→A` },

    { value: 'slugAsc', label: 'Slug A→Z' },
    { value: 'slugDesc', label: 'Slug Z→A' },

    { value: 'viewsDesc', label: `${t('views')} 9→0` },
    { value: 'viewsAsc', label: `${t('views')} 0→9` },

    { value: 'likesDesc', label: `${t('likes')} 9→0` },
    { value: 'likesAsc', label: `${t('likes')} 0→9` },

    { value: 'childrenDesc', label: `${t('children')} 9→0` },
    { value: 'childrenAsc', label: `${t('children')} 0→9` },

    { value: 'coursesDesc', label: `${t('courses')} 9→0` },
    { value: 'coursesAsc', label: `${t('courses')} 0→9` },

    { value: 'imagesDesc', label: `${t('images')} 9→0` },
    { value: 'imagesAsc', label: `${t('images')} 0→9` },

    { value: 'dateDesc', label: t('sortNewestFirst') },
    { value: 'dateAsc', label: t('sortOldestFirst') },
]

/** Маршрут списка направлений обучения */
const indexRoute = () => route('public.schoolTracks.index')

/** Загрузка треков с текущими фильтрами */
const reloadTracks = (page = 1) => {
    router.get(
        indexRoute(),
        {
            q: q.value || undefined,
            sort: sort.value || undefined,
            view: viewMode.value || undefined,
            per_page: perPage.value,
            page,
        },
        {
            preserveState: true,
            replace: true,
            preserveScroll: true,
        }
    )
}

/** Поиск */
const submitSearch = () => {
    reloadTracks(1)
}

/** Сброс поиска и сортировки */
const resetSearch = () => {
    q.value = ''
    sort.value = DEFAULT_SORT

    reloadTracks(1)
}

/** Изменение сортировки */
const updateSort = (value) => {
    sort.value = value || DEFAULT_SORT

    reloadTracks(1)
}

/** Изменение режима отображения */
const updateViewMode = (value) => {
    viewMode.value = value || 'grid'

    reloadTracks(currentPage.value)
}

/** Переход на страницу */
const goToPage = (page) => {
    const value = Number(page)

    if (!Number.isFinite(value)) return

    const safePage = Math.max(1, Math.min(value, lastPage.value))

    reloadTracks(safePage)
}

/** Предыдущая страница */
const goPrev = () => {
    if (currentPage.value <= 1) return

    goToPage(currentPage.value - 1)
}

/** Следующая страница */
const goNext = () => {
    if (currentPage.value >= lastPage.value) return

    goToPage(currentPage.value + 1)
}

/** Глобальные настройки сайта */
const { siteSettings } = usePage().props

/** Показ левой колонки */
const showLeft = computed(() =>
    !siteSettings?.ViewLeftColumn || siteSettings.ViewLeftColumn === 'true'
)

/** Показ правой колонки */
const showRight = computed(() =>
    !siteSettings?.ViewRightColumn || siteSettings.ViewRightColumn === 'true'
)

/** Состояние сайдбаров */
const leftCollapsed = ref(false)
const rightCollapsed = ref(false)

/** Количество колонок сетки с учётом сайдбаров */
const trackGridCols = computed(() => {
    const leftExpanded = showLeft.value && !leftCollapsed.value
    const rightExpanded = showRight.value && !rightCollapsed.value

    return leftExpanded && rightExpanded ? 2 : 3
})
</script>

<template>
    <!-- SEO -->
    <Head>
        <title>{{ t('learningCategories') }}</title>
        <meta name="title" :content="t('learningCategories')" />
        <meta name="keywords" content="" />
        <meta name="description" :content="t('learningCategories')" />

        <meta property="og:title" :content="t('learningCategories')" />
        <meta property="og:description" :content="t('learningCategories')" />
        <meta property="og:type" content="website" />
        <meta property="og:url" :content="`/school/tracks`" />
        <meta property="og:image" content="" />
        <meta property="og:locale" :content="'ru_RU'" />

        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="t('learningCategories')" />
        <meta name="twitter:description" :content="t('learningCategories')" />
        <meta name="twitter:image" content="" />

        <meta name="DC.title" :content="t('learningCategories')" />
        <meta name="DC.description" :content="t('learningCategories')" />
        <meta name="DC.identifier" :content="`/school/tracks`" />
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
                                    {{ t('tracks') }}
                                </li>
                            </ol>
                        </nav>

                        <!-- Заголовок -->
                        <div class="my-3 flex flex-wrap
                                    items-center justify-center gap-3 title">
                            <svg class="shrink-0 h-5 w-5 text-slate-600/85 dark:text-slate-200/85"
                                 fill="currentColor"
                                 viewBox="0 0 24 24">
                                <path d="M23.58.424A1,1,0,0,0,22.819.13C8.791.862,3.609,13.358,3.559,13.484a1,1,0,0,0,.22,1.08l5.657,5.657a1,1,0,0,0,1.085.218c.125-.051,12.554-5.291,13.348-19.253A1,1,0,0,0,23.58.424Zm-8.166,10.99a2,2,0,1,1,0-2.828A2,2,0,0,1,15.414,11.414Z"></path>
                                <path d="M1.113,18.844a2.844,2.844,0,1,1,4.022,4.022C4.024,23.977,0,24,0,24S0,19.954,1.113,18.844Z"></path>
                                <path d="M10.357,2.341A8.911,8.911,0,0,0,2.522,4.825a9.084,9.084,0,0,0-1.384,1.8,1,1,0,0,0,.155,1.215l1.989,1.99A26.623,26.623,0,0,1,10.357,2.341Z"></path>
                                <path d="M21.659,13.643a8.911,8.911,0,0,1-2.484,7.835,9.084,9.084,0,0,1-1.8,1.384,1,1,0,0,1-1.215-.155l-1.99-1.989A26.623,26.623,0,0,0,21.659,13.643Z"></path>
                            </svg>
                            <h1 class="text-2xl font-bold">
                                {{ t('tracks') }}
                            </h1>
                        </div>

                        <!-- Подзаголовок -->
                        <div class="my-1 text-sm subtitle text-center">
                            Изучите краткое содержание каждого трека и выберите путь,
                            который соответствует вашим целям и интересам в IT-сфере
                        </div>

                        <!-- Поиск, количество, сортировка, вид -->
                        <EntityPageToolbar
                            v-model="q"
                            :found="tracksFound"
                            :view-mode="viewMode"
                            :sort-value="sort"
                            :sort-options="trackSortOptions"
                            :default-sort="DEFAULT_SORT"
                            :found-label="t('tracks')"
                            :search-placeholder="t('searchByName')"
                            @submit="submitSearch"
                            @reset="resetSearch"
                            @update:viewMode="updateViewMode"
                            @update:sortValue="updateSort"
                        />

                        <!-- Нет данных -->
                        <div
                            v-if="tracksData.length === 0"
                            class="mt-6 text-center text-slate-700 dark:text-slate-300"
                        >
                            {{ t('noData') }}
                        </div>

                        <!-- Показ grid/rows -->
                        <div v-else>
                            <TrackGrid
                                v-if="viewMode === 'grid'"
                                :tracks="tracksData"
                                :cols="trackGridCols"
                            />
                            <TrackRows
                                v-else
                                :tracks="tracksData"
                            />
                        </div>

                        <!-- Пагинация -->
                        <Pagination
                            :current-page="currentPage"
                            :last-page="lastPage"
                            :found="tracksFound"
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
