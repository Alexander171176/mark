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
import { useSmoothScrollTo } from '@/composables/useSmoothScrollTo'

import DefaultLayout from '@/Layouts/DefaultLayout.vue'
import Navbar from '@/Partials/Default/Navbar.vue'
import FooterBlog from '@/Partials/Default/FooterBlog.vue'
import Progress from '@/Components/Public/Default/Progress/Progress.vue'
import LeftSidebar from '@/Components/Public/Default/Partials/LeftSidebar.vue'
import RightSidebar from '@/Components/Public/Default/Partials/RightSidebar.vue'
import EntityPageToolbar from '@/Components/Public/Default/PageToolbar/EntityPageToolbar.vue'
import RubricGrid from '@/Components/Public/Default/Blog/BlogRubric/RubricGrid.vue'
import RubricRows from '@/Components/Public/Default/Blog/BlogRubric/RubricRows.vue'
import Pagination from '@/Components/Public/Default/Pagination/Pagination.vue'
import SectionVideoList from '@/Components/Public/Default/Blog/BlogVideo/SectionVideoList.vue'
import SectionBanners from '@/Components/Public/Default/Blog/BlogBanner/SectionBanners.vue'
import FrontendEntityPageToolbar from '@/Components/Public/Default/PageToolbar/FrontendEntityPageToolbar.vue'
import FrontendPagination from '@/Components/Public/Default/Pagination/FrontendPagination.vue'
import PublicAdminBottomPanel from '@/Components/Admin/UI/PublicAdminPanel/PublicAdminBottomPanel.vue'

const { t } = useI18n()

/** Props страницы */
const props = defineProps({
    locale: { type: String, default: 'ru' },

    useServerProcessing: { type: Boolean, default: false },
    publicBlogRubricsProcessingMode: { type: String, default: 'server' },

    title: { type: String, default: '' },
    canLogin: { type: Boolean, default: false },
    canRegister: { type: Boolean, default: false },

    rubricTree: { type: Array, default: () => [] },

    rubrics: { type: [Array, Object], default: () => [] },
    rubricsCount: { type: Number, default: 0 },
    rubricsFound: { type: Number, default: 0 },

    filters: { type: Object, default: () => ({}) },

    tags: { type: Array, default: () => [] },
    mainVideos: { type: [Array, Object], default: () => [] },
    mainBanners: { type: [Array, Object], default: () => [] },
})

/** Глобальные данные страницы */
const page = usePage()

/** Глобальные настройки сайта */
const siteSettings = page.props?.siteSettings || {}

/** Роль администратора для нижней служебной панели */
const isAdmin = computed(() => page.props?.isAdmin === true)

/** Дерево рубрик для левого аккордеона */
const rubricTree = computed(() => {
    return Array.isArray(props.rubricTree) ? props.rubricTree : []
})

/** Универсальный список рубрик: server paginator data или frontend array */
const rubricsData = computed(() => {
    if (Array.isArray(props.rubrics)) {
        return props.rubrics
    }

    if (Array.isArray(props.rubrics?.data)) {
        return props.rubrics.data
    }

    return []
})

/** Количество элементов на странице для обоих режимов */
const perPage = computed(() => {
    const value = Number(props.filters?.per_page ?? 6)

    return Number.isFinite(value) ? value : 6
})

/** Поисковая строка для server/frontend */
const q = ref(String(props.filters?.q ?? ''))

/** Сортировка по умолчанию для server/frontend */
const DEFAULT_SORT = 'sortAsc'

/** Текущая сортировка для server/frontend */
const sort = ref(String(props.filters?.sort ?? DEFAULT_SORT))

/** Ключ локального хранения режима отображения */
const VIEW_KEY = 'public_blog_rubrics_view'

/** Режим отображения карточки/строки для server/frontend */
const viewMode = ref(
    String(props.filters?.view || localStorage.getItem(VIEW_KEY) || 'grid')
)

/** Сохраняем режим отображения локально */
watch(viewMode, (value) => {
    localStorage.setItem(VIEW_KEY, value)
})

/** Опции сортировки для toolbar */
const rubricSortOptions = [
    { value: 'sortAsc', label: `${t('sortNumber')} 0→9` },
    { value: 'sortDesc', label: `${t('sortNumber')} 9→0` },

    { value: 'titleAsc', label: `${t('title')} A→Z` },
    { value: 'titleDesc', label: `${t('title')} Z→A` },

    { value: 'viewsDesc', label: `${t('views')} 9→0` },
    { value: 'viewsAsc', label: `${t('views')} 0→9` },

    { value: 'articlesDesc', label: `${t('articles')} 9→0` },
    { value: 'articlesAsc', label: `${t('articles')} 0→9` },

    { value: 'dateDesc', label: `${t('publishedAt')} ↓` },
    { value: 'dateAsc', label: `${t('publishedAt')} ↑` },
]

/* ===================== FRONTEND MODE ===================== */

/** Текущая страница локальной пагинации frontend */
const frontendCurrentPage = ref(1)

/** Цель плавного скролла при frontend-пагинации */
const {
    targetRef: scrollTarget,
    scrollToTarget,
} = useSmoothScrollTo({
    offset: 80,
    duration: 1200,
})

/** Нормализация текста для локального поиска frontend */
const normalizeText = (value) => {
    return String(value ?? '').toLowerCase()
}

/** Получение названия рубрики из разных возможных структур ресурса */
const getRubricTitle = (rubric) => {
    return rubric.title
        || rubric.name
        || rubric.translation?.title
        || rubric.translation?.name
        || rubric.current_translation?.title
        || rubric.current_translation?.name
        || rubric.translations?.[0]?.title
        || rubric.translations?.[0]?.name
        || ''
}

/** Получение краткого текста рубрики */
const getRubricShort = (rubric) => {
    return rubric.short
        || rubric.description
        || rubric.translation?.short
        || rubric.translation?.description
        || rubric.current_translation?.short
        || rubric.current_translation?.description
        || rubric.translations?.[0]?.short
        || rubric.translations?.[0]?.description
        || ''
}

/** Локальный поиск frontend */
const filteredRubrics = computed(() => {
    const query = normalizeText(q.value).trim()

    if (!query) {
        return rubricsData.value
    }

    return rubricsData.value.filter((rubric) => {
        return [
            getRubricTitle(rubric),
            getRubricShort(rubric),
            rubric.url,
            rubric.slug,
            rubric.owner?.name,
            rubric.owner?.email,
        ].some((value) => normalizeText(value).includes(query))
    })
})

/** Локальная сортировка frontend */
const sortedRubrics = computed(() => {
    const list = [...filteredRubrics.value]

    return list.sort((a, b) => {
        switch (sort.value) {
            case 'sortAsc':
                return (a.sort ?? 0) - (b.sort ?? 0)

            case 'sortDesc':
                return (b.sort ?? 0) - (a.sort ?? 0)

            case 'titleAsc':
                return normalizeText(getRubricTitle(a))
                    .localeCompare(normalizeText(getRubricTitle(b)))

            case 'titleDesc':
                return normalizeText(getRubricTitle(b))
                    .localeCompare(normalizeText(getRubricTitle(a)))

            case 'viewsAsc':
                return (a.views ?? 0) - (b.views ?? 0)

            case 'viewsDesc':
                return (b.views ?? 0) - (a.views ?? 0)

            case 'articlesAsc':
                return (a.articles_count ?? 0) - (b.articles_count ?? 0)

            case 'articlesDesc':
                return (b.articles_count ?? 0) - (a.articles_count ?? 0)

            case 'dateAsc':
                return new Date(a.published_at ?? a.created_at ?? 0) -
                    new Date(b.published_at ?? b.created_at ?? 0)

            case 'dateDesc':
                return new Date(b.published_at ?? b.created_at ?? 0) -
                    new Date(a.published_at ?? a.created_at ?? 0)

            default:
                return 0
        }
    })
})

/** Локальная пагинация frontend */
const frontendPaginatedRubrics = computed(() => {
    const start = (frontendCurrentPage.value - 1) * perPage.value

    return sortedRubrics.value.slice(start, start + perPage.value)
})

/** Сбрасываем frontend-пагинацию при поиске/сортировке/виде */
watch([q, sort, viewMode], () => {
    frontendCurrentPage.value = 1
})

/** Плавно возвращаемся к началу списка при frontend-пагинации */
watch(frontendCurrentPage, () => {
    if (!props.useServerProcessing) {
        scrollToTarget()
    }
})

/* ===================== SERVER MODE ===================== */

/** Текущая страница server-пагинации */
const currentPage = computed(() => {
    return Number(props.rubrics?.meta?.current_page ?? props.rubrics?.current_page ?? 1) || 1
})

/** Последняя страница server-пагинации */
const lastPage = computed(() => {
    return Number(props.rubrics?.meta?.last_page ?? props.rubrics?.last_page ?? 1) || 1
})

/** Маршрут списка рубрик для server-режима */
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

/** Server-поиск */
const submitSearch = () => {
    reloadRubrics(1)
}

/** Сброс поиска и сортировки для обоих режимов */
const resetSearch = () => {
    q.value = ''
    sort.value = DEFAULT_SORT
    frontendCurrentPage.value = 1

    if (props.useServerProcessing) {
        reloadRubrics(1)
    }
}

/** Server-изменение сортировки */
const updateSort = (value) => {
    sort.value = value || DEFAULT_SORT

    if (props.useServerProcessing) {
        reloadRubrics(1)
    }
}

/** Изменение режима отображения для обоих режимов */
const updateViewMode = (value) => {
    viewMode.value = value || 'grid'

    if (props.useServerProcessing) {
        reloadRubrics(currentPage.value)
    }
}

/** Server-переход на страницу */
const goToPage = (page) => {
    const value = Number(page)

    if (!Number.isFinite(value)) return

    const safePage = Math.max(1, Math.min(value, lastPage.value))

    reloadRubrics(safePage)
}

/** Server-предыдущая страница */
const goPrev = () => {
    if (currentPage.value <= 1) return

    goToPage(currentPage.value - 1)
}

/** Server-следующая страница */
const goNext = () => {
    if (currentPage.value >= lastPage.value) return

    goToPage(currentPage.value + 1)
}

/* ===================== COMMON VIEW ===================== */

/** Итоговый список для отображения: server data или frontend page */
const displayedRubrics = computed(() => {
    return props.useServerProcessing
        ? rubricsData.value
        : frontendPaginatedRubrics.value
})

/** Показ левой колонки */
const showLeft = computed(() => {
    return !siteSettings?.ViewLeftColumn || siteSettings.ViewLeftColumn === 'true'
})

/** Показ правой колонки */
const showRight = computed(() => {
    return !siteSettings?.ViewRightColumn || siteSettings.ViewRightColumn === 'true'
})

/** Ключ localStorage для левого сайдбара */
const LEFT_SIDEBAR_KEY = 'public_left_sidebar_collapsed'

/** Ключ localStorage для правого сайдбара */
const RIGHT_SIDEBAR_KEY = 'public_right_sidebar_collapsed'

/** Получение boolean из localStorage */
const getStoredBoolean = (key, defaultValue = false) => {
    const value = localStorage.getItem(key)

    if (value === null) {
        return defaultValue
    }

    return value === 'true'
}

/** Состояние левого сайдбара */
const leftCollapsed = ref(
    getStoredBoolean(LEFT_SIDEBAR_KEY, false)
)

/** Состояние правого сайдбара */
const rightCollapsed = ref(
    getStoredBoolean(RIGHT_SIDEBAR_KEY, false)
)

/** Сохраняем состояние левого сайдбара */
watch(leftCollapsed, (value) => {
    localStorage.setItem(LEFT_SIDEBAR_KEY, String(value))
})

/** Сохраняем состояние правого сайдбара */
watch(rightCollapsed, (value) => {
    localStorage.setItem(RIGHT_SIDEBAR_KEY, String(value))
})

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

        <div class="min-h-screen px-3 max-w-full">
            <main class="mx-auto flex flex-col lg:flex-row gap-4 tracking-wider">
                <!-- LEFT -->
                <aside
                    v-if="showLeft"
                    class="shrink-0 mt-12 lg:mt-28 transition-all duration-300"
                    :class="leftCollapsed ? 'lg:w-10' : 'lg:w-64'"
                >
                    <LeftSidebar
                        :rubric-tree="rubricTree"
                        :collapsed="leftCollapsed"
                        @collapsed="leftCollapsed = $event"
                    />
                </aside>

                <div class="w-full lg:mt-28 pb-6 slate-1">
                    <div class="mx-auto max-w-6xl">
                        <nav class="text-sm" aria-label="Breadcrumb">
                            <ol class="flex flex-wrap items-center font-semibold">
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
                            v-if="useServerProcessing"
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

                        <FrontendEntityPageToolbar
                            v-else
                            v-model="q"
                            :found="sortedRubrics.length"
                            :view-mode="viewMode"
                            :sort-value="sort"
                            :sort-options="rubricSortOptions"
                            :default-sort="DEFAULT_SORT"
                            :found-label="t('rubrics')"
                            :search-placeholder="t('searchByName')"
                            @reset="resetSearch"
                            @update:viewMode="updateViewMode"
                            @update:sortValue="updateSort"
                        />

                        <div ref="scrollTarget"></div>

                        <div
                            v-if="displayedRubrics.length === 0"
                            class="mt-6 text-center text-slate-700 dark:text-slate-300"
                        >
                            {{ t('noData') }}
                        </div>

                        <div v-else>
                            <RubricGrid
                                v-if="viewMode === 'grid'"
                                :rubrics="displayedRubrics"
                                :cols="rubricGridCols"
                            />

                            <RubricRows
                                v-else
                                :rubrics="displayedRubrics"
                            />
                        </div>

                        <Pagination
                            v-if="useServerProcessing"
                            :current-page="currentPage"
                            :last-page="lastPage"
                            :found="rubricsFound"
                            @prev="goPrev"
                            @next="goNext"
                            @go="goToPage"
                        />

                        <FrontendPagination
                            v-else
                            v-model:currentPage="frontendCurrentPage"
                            :items-per-page="perPage"
                            :total-items="sortedRubrics.length"
                        />

                        <SectionVideoList :videos="mainVideos" />
                        <SectionBanners :banners="mainBanners" />
                    </div>
                </div>

                <!-- RIGHT -->
                <aside
                    v-if="showRight"
                    class="shrink-0 lg:mt-28 transition-all duration-300"
                    :class="rightCollapsed ? 'lg:w-10' : 'lg:w-64'"
                >
                    <RightSidebar
                        :collapsed="rightCollapsed"
                        @collapsed="rightCollapsed = $event"
                    />
                </aside>
            </main>
        </div>

        <FooterBlog />
        <Progress />
        <!-- Нижняя панель администратора -->
        <PublicAdminBottomPanel
            v-if="isAdmin"
            setting-key="publicBlogRubricsProcessingMode"
            :mode="publicBlogRubricsProcessingMode"
            :use-server-processing="useServerProcessing"
            :total="rubricsCount"
        />
    </DefaultLayout>
</template>
