<script setup>
/**
 * Страница списка заданий
 * - шапка, центральная часть, подвал
 * - светлый, тёмный режим
 * - серверный поиск
 * - серверная пагинация
 * - серверная сортировка
 * - показ карточками, в строку
 * - показ главных видео, баннеров внизу страницы
 * - показ, скрытие колонок
 * - показ дерева треков в левой колонке
 * - показ облака хештегов в правой колонке
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
import AssignmentGrid from '@/Components/Public/Default/School/Assignment/AssignmentGrid.vue'
import AssignmentRows from '@/Components/Public/Default/School/Assignment/AssignmentRows.vue'

const { t } = useI18n()

/** пропсы */
const props = defineProps({
    title: String,
    canLogin: Boolean,
    canRegister: Boolean,

    trackTree: { type: Array, default: () => [] },

    assignments: { type: Object, default: () => ({}) },
    assignmentsCount: { type: Number, default: 0 },
    assignmentsFound: { type: Number, default: 0 },
    filters: { type: Object, default: () => ({}) },

    mainVideos: { type: [Array, Object], default: () => [] },
    mainBanners: { type: [Array, Object], default: () => [] },
})

/** настройки из БД */
const { siteSettings } = usePage().props

/** показ левой колонки */
const showLeft = computed(() =>
    !siteSettings?.ViewLeftColumn || siteSettings.ViewLeftColumn === 'true'
)

/** показ правой колонки */
const showRight = computed(() =>
    !siteSettings?.ViewRightColumn || siteSettings.ViewRightColumn === 'true'
)

/** состояние левой и правой колонки */
const leftCollapsed = ref(false)
const rightCollapsed = ref(false)

/** показ третьей карточки в ряд, если свёрнута колонка */
const gridCols = computed(() => {
    const leftExpanded = showLeft.value && !leftCollapsed.value
    const rightExpanded = showRight.value && !rightCollapsed.value

    return leftExpanded && rightExpanded ? 2 : 3
})

/** Вид плиткой/строкой */
const VIEW_KEY = 'public_assignments_view'
const viewMode = ref(localStorage.getItem(VIEW_KEY) || 'grid')

watch(viewMode, (v) => {
    localStorage.setItem(VIEW_KEY, v)
})

/** нормализация данных */
const normalizeList = (value) => {
    if (Array.isArray(value)) return value
    if (Array.isArray(value?.data)) return value.data
    return []
}

/** данные задания */
const assignmentsData = computed(() => {
    const data = props.assignments?.data
    return Array.isArray(data) ? data : []
})

/** дерево треков */
const trackTree = computed(() => Array.isArray(props.trackTree) ? props.trackTree : [])

/** внизу страницы банеры */
const mainVideosList = computed(() => normalizeList(props.mainVideos))

/** внизу страницы видео */
const mainBannersList = computed(() => normalizeList(props.mainBanners))

/** Пагинация */
const currentPage = computed(() => {
    return Number(props.assignments?.meta?.current_page ?? props.assignments?.current_page ?? 1) || 1
})

const lastPage = computed(() => {
    return Number(props.assignments?.meta?.last_page ?? props.assignments?.last_page ?? 1) || 1
})

const perPage = computed(() => {
    const val = Number(props.filters?.per_page ?? 12)
    return Number.isFinite(val) ? val : 12
})

/** Сортировка */
const DEFAULT_SORT = 'sort_asc'

const q = ref(String(props.filters?.q ?? ''))
const sort = ref(String(props.filters?.sort ?? DEFAULT_SORT))

watch(
    () => props.filters?.q,
    (value) => {
        q.value = String(value ?? '')
    }
)

watch(
    () => props.filters?.sort,
    (value) => {
        sort.value = String(value ?? DEFAULT_SORT)
    }
)

/** Опции сортировки */
const assignmentSortOptions = [
    { value: 'sort_asc', label: t('sortDefault') },
    { value: 'sort_desc', label: t('sortReverse') },
    { value: 'title_asc', label: t('sortNameAsc') },
    { value: 'title_desc', label: t('sortNameDesc') },
    { value: 'score_desc', label: t('sortPopularFirst') },
    { value: 'score_asc', label: t('sortUnpopularFirst') },
    { value: 'due_desc', label: t('sortNewestFirst') },
    { value: 'due_asc', label: t('sortOldestFirst') },
    { value: 'date_desc', label: t('sortNewestFirst') },
    { value: 'date_asc', label: t('sortOldestFirst') },
]

const onSortChange = (value) => {
    sort.value = value || DEFAULT_SORT

    router.get(
        route('public.assignments.index'),
        {
            q: String(q.value ?? '').trim() || undefined,
            sort: sort.value || undefined,
            per_page: perPage.value,
            page: 1,
        },
        { preserveState: true, replace: true, preserveScroll: true }
    )
}

/** Поиск */
const submitSearch = () => {
    const searchValue = String(q.value ?? '').trim()

    router.get(
        route('public.assignments.index'),
        {
            q: searchValue || undefined,
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
        route('public.assignments.index'),
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
        route('public.assignments.index'),
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

</script>

<template>
    <!-- SEO -->
    <Head>
        <title>{{ t('assignments') }}</title>
        <meta name="title" :content="t('assignments')" />
        <meta name="keywords" content="" />
        <meta name="description" :content="t('assignments')" />

        <meta property="og:title" :content="t('assignments')" />
        <meta property="og:description" :content="t('assignments')" />
        <meta property="og:type" content="website" />
        <meta property="og:url" :content="`/school/assignments`" />
        <meta property="og:image" content="" />
        <meta property="og:locale" :content="'ru_RU'" />

        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="t('assignments')" />
        <meta name="twitter:description" :content="t('assignments')" />
        <meta name="twitter:image" content="" />

        <meta name="DC.title" :content="t('assignments')" />
        <meta name="DC.description" :content="t('assignments')" />
        <meta name="DC.identifier" :content="`/school/assignments`" />
        <meta name="DC.language" :content="'ru'" />
    </Head>

    <DefaultLayout :title="title" :can-login="canLogin" :can-register="canRegister">
        <!-- Шапка -->
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
                                    {{ t('assignments') }}
                                </li>
                            </ol>
                        </nav>

                        <div class="my-3 flex flex-wrap items-center justify-center gap-3 title">
                            <svg class="shrink-0 h-5 w-5 text-slate-600/85 dark:text-slate-200/85"
                                 fill="currentColor"
                                 viewBox="0 0 24 24">
                                <path d="M15,18v2H9v-2H1v5c0,0.552,0.448,1,1,1h20c0.552,0,1-0.448,1-1v-5H15z"></path>
                                <path d="M23,4h-6V1c0-0.552-0.448-1-1-1H8C7.448,0,7,0.448,7,1v3H1C0.448,4,0,4.448,0,5v10c0,0.552,0.448,1,1,1h8v-3 h6v3h8c0.552,0,1-0.448,1-1V5C24,4.448,23.552,4,23,4z M15,4H9V2h6V4z"></path>
                            </svg>
                            <h1 class="text-2xl font-bold">
                                {{ t('assignments') }}
                            </h1>
                        </div>

                        <div class="my-1 text-sm subtitle text-center">
                            Просматривайте задания, сроки сдачи и требования в удобном формате.
                        </div>

                        <EntityPageToolbar
                            v-model="q"
                            :found="assignmentsFound"
                            :view-mode="viewMode"
                            :sort-value="sort"
                            :sort-options="assignmentSortOptions"
                            :default-sort="DEFAULT_SORT"
                            :found-label="t('assignments')"
                            :search-placeholder="t('searchByName')"
                            @submit="submitSearch"
                            @reset="resetSearch"
                            @update:viewMode="viewMode = $event"
                            @update:sortValue="onSortChange"
                        />

                        <div
                            v-if="assignmentsData.length === 0"
                            class="mt-6 text-center text-slate-700 dark:text-slate-300"
                        >
                            {{ t('noData') }}
                        </div>

                        <div v-else>
                            <AssignmentGrid
                                v-if="viewMode === 'grid'"
                                :assignments="assignmentsData"
                                :cols="gridCols"
                            />

                            <AssignmentRows
                                v-else
                                :assignments="assignmentsData"
                            />
                        </div>

                        <Pagination
                            :current-page="currentPage"
                            :last-page="lastPage"
                            :found="assignmentsFound"
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
        <!-- Подвал и прогресс -->
        <FooterBlog />
        <Progress />
    </DefaultLayout>
</template>
