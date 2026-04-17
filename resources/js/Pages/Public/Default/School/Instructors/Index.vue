<script setup>
/**
 * Страница списка инструкторов
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
import InstructorGrid from '@/Components/Public/Default/School/Instructor/InstructorGrid.vue'
import InstructorRows from '@/Components/Public/Default/School/Instructor/InstructorRows.vue'

const { t } = useI18n()

const props = defineProps({
    title: String,
    canLogin: Boolean,
    canRegister: Boolean,

    trackTree: { type: Array, default: () => [] },

    instructorProfiles: { type: Object, default: () => ({}) },
    instructorProfilesCount: { type: Number, default: 0 },
    instructorProfilesFound: { type: Number, default: 0 },
    filters: { type: Object, default: () => ({}) },

    tags: { type: Array, default: () => [] },
    mainVideos: { type: Array, default: () => [] },
    mainBanners: { type: Array, default: () => [] },
})

/** Иерархия треков */
const trackTree = computed(() => Array.isArray(props.trackTree) ? props.trackTree : [])

/** Режим показа */
const VIEW_KEY = 'public_view'
const viewMode = ref(localStorage.getItem(VIEW_KEY) || 'grid')

watch(viewMode, (value) => {
    localStorage.setItem(VIEW_KEY, value)
})

/** Данные */
const instructorsData = computed(() => {
    const data = props.instructorProfiles?.data
    return Array.isArray(data) ? data : []
})

/** Пагинация */
const currentPage = computed(() => {
    return Number(props.instructorProfiles?.meta?.current_page ?? props.instructorProfiles?.current_page ?? 1) || 1
})

const lastPage = computed(() => {
    return Number(props.instructorProfiles?.meta?.last_page ?? props.instructorProfiles?.last_page ?? 1) || 1
})

const perPage = computed(() => {
    const value = Number(props.filters?.per_page ?? 12)
    return Number.isFinite(value) ? value : 12
})

/** Поиск */
const q = ref(String(props.filters?.q ?? ''))

/** Сортировка */
const sort = ref(String(props.filters?.sort ?? 'sort_asc'))

const instructorSortOptions = [
    { value: 'sort_asc', label: t('idAsc') },
    { value: 'sort_desc', label: t('idDesc') },
    { value: 'date_desc', label: t('sortNewestFirst') },
    { value: 'date_asc', label: t('sortOldestFirst') },
    { value: 'views_desc', label: t('sortPopularFirst') },
    { value: 'views_asc', label: t('sortUnpopularFirst') },
    { value: 'rating_desc', label: t('ratingDesc') },
    { value: 'rating_asc', label: t('ratingAsc') },
    { value: 'experience_desc', label: t('experienceDesc') },
    { value: 'experience_asc', label: t('experienceAsc') },
]

/** Поисковый запрос */
const submitSearch = () => {
    router.get(
        route('instructors.index', { locale: props.currentLocale }),
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
    sort.value = 'sort_asc'

    router.get(
        route('instructors.index', { locale: props.currentLocale }),
        {
            per_page: perPage.value,
            sort: sort.value,
            page: 1,
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
        route('instructors.index', { locale: props.currentLocale }),
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

/** Показ/скрытие колонок */
const { siteSettings } = usePage().props

const showLeft = computed(() =>
    !siteSettings?.ViewLeftColumn || siteSettings.ViewLeftColumn === 'true'
)

const showRight = computed(() =>
    !siteSettings?.ViewRightColumn || siteSettings.ViewRightColumn === 'true'
)

/** Состояние сайдбаров */
const leftCollapsed = ref(false)
const rightCollapsed = ref(false)

/** Колонки сетки */
const instructorGridCols = computed(() => {
    const leftExpanded = showLeft.value && !leftCollapsed.value
    const rightExpanded = showRight.value && !rightCollapsed.value

    return leftExpanded && rightExpanded ? 2 : 3
})

/** нормализация массивов баннеров и видео внизу */
const normalizeList = (value) => {
    if (Array.isArray(value)) return value
    if (Array.isArray(value?.data)) return value.data
    return []
}

const mainVideosList = computed(() => normalizeList(props.mainVideos))
const mainBannersList = computed(() => normalizeList(props.mainBanners))
</script>

<template>
    <Head>
        <title>{{t('instructors')}}</title>
        <meta name="title" :content="t('instructors')" />
        <meta name="keywords" content="" />
        <meta name="description" :content="t('instructors')" />

        <meta property="og:title" :content="t('instructors')" />
        <meta property="og:description" :content="t('instructors')" />
        <meta property="og:type" content="website" />
        <meta property="og:url" :content="`/instructors`" />
        <meta property="og:image" content="" />
        <meta property="og:locale" :content="'ru_RU'" />

        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="t('instructors')" />
        <meta name="twitter:description" :content="t('instructors')" />
        <meta name="twitter:image" content="" />

        <meta name="DC.title" :content="t('instructors')" />
        <meta name="DC.description" :content="t('instructors')" />
        <meta name="DC.identifier" :content="`/instructors`" />
        <meta name="DC.language" :content="'ru'" />
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
                                    {{ t('instructors') }}
                                </li>
                            </ol>
                        </nav>

                        <!-- Заголовок -->
                        <div class="my-3 flex flex-wrap items-center justify-center gap-2 title">
                            <svg class="shrink-0 h-7 w-7 text-slate-600/85 dark:text-slate-200/85"
                                 fill="currentColor"
                                 viewBox="0 0 640 512">
                                <path d="M622.34 153.2L343.4 67.5c-15.2-4.67-31.6-4.67-46.79 0L17.66 153.2c-23.54 7.23-23.54 38.36 0 45.59l48.63 14.94c-10.67 13.19-17.23 29.28-17.88 46.9C38.78 266.15 32 276.11 32 288c0 10.78 5.68 19.85 13.86 25.65L20.33 428.53C18.11 438.52 25.71 448 35.94 448h56.11c10.24 0 17.84-9.48 15.62-19.47L82.14 313.65C90.32 307.85 96 298.78 96 288c0-11.57-6.47-21.25-15.66-26.87.76-15.02 8.44-28.3 20.69-36.72L296.6 284.5c9.06 2.78 26.44 6.25 46.79 0l278.95-85.7c23.55-7.24 23.55-38.36 0-45.6zM352.79 315.09c-28.53 8.76-52.84 3.92-65.59 0l-145.02-44.55L128 384c0 35.35 85.96 64 192 64s192-28.65 192-64l-14.18-113.47-145.03 44.56z"></path>
                            </svg>
                            <h1 class="text-2xl font-bold">
                                {{ t('instructors') }}
                            </h1>
                        </div>

                        <!-- Подзаголовок -->
                        <div class="my-1 text-sm subtitle text-center">
                            Найдите подходящего инструктора для обучения
                        </div>

                        <!-- Toolbar -->
                        <EntityPageToolbar
                            v-model="q"
                            :found="instructorProfilesFound"
                            :view-mode="viewMode"
                            :sort-value="sort"
                            :sort-options="instructorSortOptions"
                            @submit="submitSearch"
                            @reset="resetSearch"
                            @update:viewMode="viewMode = $event"
                            @update:sortValue="sort = $event"
                        />

                        <!-- Empty -->
                        <div
                            v-if="instructorsData.length === 0"
                            class="mt-6 text-center text-slate-700 dark:text-slate-300"
                        >
                            {{ t('noData') }}
                        </div>

                        <!-- Views -->
                        <div v-else>
                            <InstructorGrid
                                v-if="viewMode === 'grid'"
                                :instructors="instructorsData"
                                :cols="instructorGridCols"
                            />
                            <InstructorRows
                                v-else
                                :instructors="instructorsData"
                            />
                        </div>

                        <!-- Пагинация -->
                        <Pagination
                            :current-page="currentPage"
                            :last-page="lastPage"
                            :found="instructorProfilesFound"
                            @prev="goPrev"
                            @next="goNext"
                            @go="goToPage"
                        />

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
