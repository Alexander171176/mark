<script setup>
/**
 * Страница списка рубрик блога.
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
import LeftSidebar from '@/Components/Public/Default/Partials/LeftSidebar.vue'
import RightSidebar from '@/Components/Public/Default/Partials/RightSidebar.vue'
import EntityPageToolbar from '@/Components/Public/Default/PageToolbar/EntityPageToolbar.vue'
import RubricGrid from '@/Components/Public/Default/Blog/Rubric/RubricGrid.vue'
import RubricRows from '@/Components/Public/Default/Blog/Rubric/RubricRows.vue'
import Pagination from '@/Components/Public/Default/Pagination/Pagination.vue'
import SectionVideoList from '@/Components/Public/Default/Blog/Video/SectionVideoList.vue'
import SectionBanners from '@/Components/Public/Default/Blog/Banner/SectionBanners.vue'

const { t } = useI18n()

const props = defineProps({
    title: { type: String, default: '' },
    canLogin: { type: Boolean, default: false },
    canRegister: { type: Boolean, default: false },

    rubricTree: { type: Array, default: () => [] },

    rubrics: { type: Object, default: () => ({}) },
    rubricsCount: { type: Number, default: 0 },
    rubricsFound: { type: Number, default: 0 },

    filters: { type: Object, default: () => ({}) },

    tags: { type: Array, default: () => [] },
    mainVideos: { type: Array, default: () => [] },
    mainBanners: { type: Array, default: () => [] },

    locale: { type: String, default: 'ru' },
})

/** Дерево рубрик для левого аккордеона */
const rubricTree = computed(() => Array.isArray(props.rubricTree) ? props.rubricTree : [])

/** Данные рубрик из пагинатора */
const rubricsData = computed(() => {
    const data = props.rubrics?.data

    return Array.isArray(data) ? data : []
})

/** Текущая страница */
const currentPage = computed(() => {
    return Number(props.rubrics?.meta?.current_page ?? props.rubrics?.current_page ?? 1) || 1
})

/** Последняя страница */
const lastPage = computed(() => {
    return Number(props.rubrics?.meta?.last_page ?? props.rubrics?.last_page ?? 1) || 1
})

/** Количество элементов на странице */
const perPage = computed(() => {
    const value = Number(props.filters?.per_page ?? 20)

    return Number.isFinite(value) ? value : 20
})

/** Поисковая строка */
const q = ref(String(props.filters?.q ?? ''))

/** Сортировка */
const DEFAULT_SORT = 'sort'
const sort = ref(String(props.filters?.sort ?? DEFAULT_SORT))

/** Режим отображения: карточки или строки */
const VIEW_KEY = 'public_blog_rubrics_view'

const viewMode = ref(
    String(props.filters?.view || localStorage.getItem(VIEW_KEY) || 'grid')
)

/** Сохраняем режим отображения локально */
watch(viewMode, (value) => {
    localStorage.setItem(VIEW_KEY, value)
})

/** Опции сортировки рубрик */
const rubricSortOptions = [
    { value: 'sort', label: t('sortDefault') },
    { value: 'sort_asc', label: t('sortAsc') },
    { value: 'sort_desc', label: t('sortDesc') },
    { value: 'title_asc', label: t('sortNameAsc') },
    { value: 'title_desc', label: t('sortNameDesc') },
    { value: 'views_desc', label: t('sortPopularFirst') },
    { value: 'views_asc', label: t('sortUnpopularFirst') },
    { value: 'date_desc', label: t('sortNewestFirst') },
    { value: 'date_asc', label: t('sortOldestFirst') },
]

/** Маршрут списка рубрик */
const indexRoute = () => route('public.blogRubrics.index')

/** Загрузка рубрик с текущими фильтрами */
const reloadRubrics = (page = 1) => {
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
    reloadRubrics(1)
}

/** Сброс поиска и сортировки */
const resetSearch = () => {
    q.value = ''
    sort.value = DEFAULT_SORT

    reloadRubrics(1)
}

/** Изменение сортировки */
const updateSort = (value) => {
    sort.value = value || DEFAULT_SORT

    reloadRubrics(1)
}

/** Изменение режима отображения */
const updateViewMode = (value) => {
    viewMode.value = value || 'grid'

    reloadRubrics(currentPage.value)
}

/** Переход на страницу */
const goToPage = (page) => {
    const value = Number(page)

    if (!Number.isFinite(value)) return

    const safePage = Math.max(1, Math.min(value, lastPage.value))

    reloadRubrics(safePage)
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
const rubricGridCols = computed(() => {
    const leftExpanded = showLeft.value && !leftCollapsed.value
    const rightExpanded = showRight.value && !rightCollapsed.value

    return leftExpanded && rightExpanded ? 2 : 3
})
</script>

<template>
    <Head>
        <title>{{ t('rubrics') }}</title>
        <meta name="title" :content="t('rubrics')" />
        <meta name="keywords" content="" />
        <meta name="description" content="" />

        <meta property="og:title" :content="t('rubrics')" />
        <meta property="og:description" content="" />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="/" />
        <meta property="og:image" content="" />
        <meta property="og:locale" :content="locale" />

        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="t('rubrics')" />
        <meta name="twitter:description" content="" />
        <meta name="twitter:image" content="" />

        <meta name="DC.title" :content="t('rubrics')" />
        <meta name="DC.description" content="" />
        <meta name="DC.identifier" content="/" />
        <meta name="DC.language" :content="locale" />
    </Head>

    <DefaultLayout
        :title="title"
        :can-login="canLogin"
        :can-register="canRegister"
    >
        <Navbar />

        <div class="min-h-screen px-1.5">
            <main class="mx-auto flex flex-col lg:flex-row gap-4 tracking-wider">
                <aside
                    v-if="showLeft"
                    class="shrink-0 mt-12 sm:mt-16 pl-3 transition-all duration-300"
                    :class="leftCollapsed ? 'lg:w-10' : 'lg:w-64'"
                >
                    <LeftSidebar
                        :rubric-tree="rubricTree"
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

                                <li>
                                    <span class="mx-2 breadcrumbs">/</span>
                                </li>

                                <li class="breadcrumbs">
                                    {{ t('rubrics') }}
                                </li>
                            </ol>
                        </nav>

                        <div class="my-3 flex flex-wrap items-center justify-center gap-3 title">
                            <svg
                                class="h-5 w-5"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 512 512"
                                fill="currentColor"
                            >
                                <path
                                    d="M489.2 287.9h-27.4c-2.6 0-4.6 2-4.6 4.6v32h-36.6V146.2c0-2.6-2-4.6-4.6-4.6h-27.4c-2.6 0-4.6 2-4.6 4.6v32h-36.6v-32c0-2.6-2-4.6-4.6-4.6h-27.4c-2.6 0-4.6 2-4.6 4.6v32h-36.6v-32c0-6-8-4.6-11.7-4.6v-38c8.3-2 17.1-3.4 25.7-3.4 10.9 0 20.9 4.3 31.4 4.3 4.6 0 27.7-1.1 27.7-8v-60c0-2.6-2-4.6-4.6-4.6-5.1 0-15.1 4.3-24 4.3-9.7 0-20.9-4.3-32.6-4.3-8 0-16 1.1-23.7 2.9v-4.9c5.4-2.6 9.1-8.3 9.1-14.3 0-20.7-31.4-20.8-31.4 0 0 6 3.7 11.7 9.1 14.3v111.7c-3.7 0-11.7-1.4-11.7 4.6v32h-36.6v-32c0-2.6-2-4.6-4.6-4.6h-27.4c-2.6 0-4.6 2-4.6 4.6v32H128v-32c0-2.6-2-4.6-4.6-4.6H96c-2.6 0-4.6 2-4.6 4.6v178.3H54.8v-32c0-2.6-2-4.6-4.6-4.6H22.8c-2.6 0-4.6 2-4.6 4.6V512h182.9v-96c0-72.6 109.7-72.6 109.7 0v96h182.9V292.5c.1-2.6-1.9-4.6-4.5-4.6zm-288.1-4.5c0 2.6-2 4.6-4.6 4.6h-27.4c-2.6 0-4.6-2-4.6-4.6v-64c0-2.6 2-4.6 4.6-4.6h27.4c2.6 0 4.6 2 4.6 4.6v64zm146.4 0c0 2.6-2 4.6-4.6 4.6h-27.4c-2.6 0-4.6-2-4.6-4.6v-64c0-2.6 2-4.6 4.6-4.6h27.4c2.6 0 4.6 2 4.6 4.6v64z"
                                />
                            </svg>

                            <h1 class="text-2xl font-bold">
                                {{ t('rubrics') }}
                            </h1>
                        </div>

                        <div class="my-1 text-sm subtitle text-center">
                            Изучайте статьи и руководства от экспертов сообщества
                        </div>

                        <EntityPageToolbar
                            v-model="q"
                            :found="rubricsFound"
                            :view-mode="viewMode"
                            :sort-value="sort"
                            :sort-options="rubricSortOptions"
                            :default-sort="DEFAULT_SORT"
                            :found-label="t('rubrics')"
                            :search-placeholder="t('searchByName')"
                            @submit="submitSearch"
                            @reset="resetSearch"
                            @update:viewMode="updateViewMode"
                            @update:sortValue="updateSort"
                        />

                        <div
                            v-if="rubricsData.length === 0"
                            class="mt-6 text-center text-slate-700 dark:text-slate-300"
                        >
                            {{ t('noData') }}
                        </div>

                        <div v-else>
                            <RubricGrid
                                v-if="viewMode === 'grid'"
                                :rubrics="rubricsData"
                                :cols="rubricGridCols"
                            />

                            <RubricRows
                                v-else
                                :rubrics="rubricsData"
                            />
                        </div>

                        <Pagination
                            :current-page="currentPage"
                            :last-page="lastPage"
                            :found="rubricsFound"
                            @prev="goPrev"
                            @next="goNext"
                            @go="goToPage"
                        />

                        <SectionVideoList :videos="mainVideos" />
                        <SectionBanners :banners="mainBanners" />
                    </div>
                </div>

                <aside
                    v-if="showRight"
                    class="shrink-0 lg:mt-16 pr-3 transition-all duration-300"
                    :class="rightCollapsed ? 'lg:w-10' : 'lg:w-64'"
                >
                    <RightSidebar @collapsed="rightCollapsed = $event" />
                </aside>
            </main>
        </div>

        <FooterBlog />
        <Progress />
    </DefaultLayout>
</template>
