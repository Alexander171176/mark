<script setup>
/**
 * Страница списка категорий обучения
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
import TrackGrid from '@/Components/Public/Default/School/Track/TrackGrid.vue'
import TrackRows from '@/Components/Public/Default/School/Track/TrackRows.vue'

const { t } = useI18n()

/** Props */
const props = defineProps({
    title: String,
    canLogin: Boolean,
    canRegister: Boolean,

    trackTree: { type: Array, default: () => [] },

    tracks: { type: Object, default: () => ({}) },
    tracksCount: { type: Number, default: 0 },
    tracksFound: { type: Number, default: 0 },
    filters: { type: Object, default: () => ({}) },

    mainVideos: { type: Array, default: () => [] },
    mainBanners: { type: Array, default: () => [] },
})

/** Иерархия треков */
const trackTree = computed(() => Array.isArray(props.trackTree) ? props.trackTree : [])

/** Режим показа карточками/в строку */
const VIEW_KEY = 'public_track_view'
const viewMode = ref(localStorage.getItem(VIEW_KEY) || 'grid')

watch(viewMode, (v) => {
    localStorage.setItem(VIEW_KEY, v)
})

/** Данные треков */
const tracksData = computed(() => {
    const data = props.tracks?.data
    return Array.isArray(data) ? data : []
})

/** Текущая страница, Количество страниц */
const currentPage = computed(() => {
    return Number(props.tracks?.meta?.current_page ?? props.tracks?.current_page ?? 1) || 1
})

const lastPage = computed(() => {
    return Number(props.tracks?.meta?.last_page ?? props.tracks?.last_page ?? 1) || 1
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
const trackSortOptions = [
    { value: 'sort_asc', label: t('sortDefault') },
    { value: 'sort_desc', label: t('sortReverse') },
    { value: 'name_asc', label: t('sortNameAsc') },
    { value: 'name_desc', label: t('sortNameDesc') },
    { value: 'views_desc', label: t('sortPopularFirst') },
    { value: 'views_asc', label: t('sortUnpopularFirst') },
    { value: 'likes_desc', label: t('sortLikesDesc') },
    { value: 'likes_asc', label: t('sortLikesAsc') },
    { value: 'date_desc', label: t('sortNewestFirst') },
    { value: 'date_asc', label: t('sortOldestFirst') },
]

/** Поисковый запрос */
const submitSearch = () => {
    router.get(
        route('public.tracks.index'),
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
        route('public.tracks.index'),
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
        route('public.tracks.index'),
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
                            :found-label="t('learningCategories')"
                            :search-placeholder="t('searchByName')"
                            @submit="submitSearch"
                            @reset="resetSearch"
                            @update:viewMode="viewMode = $event"
                            @update:sortValue="sort = $event"
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
