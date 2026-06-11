<script setup>
/**
 * Страница списка статей блога.
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
import FrontendEntityPageToolbar from '@/Components/Public/Default/PageToolbar/FrontendEntityPageToolbar.vue'
import RubricArticleGrid from '@/Components/Public/Default/Blog/BlogRubric/RubricArticleGrid.vue'
import RubricArticleRows from '@/Components/Public/Default/Blog/BlogRubric/RubricArticleRows.vue'
import Pagination from '@/Components/Public/Default/Pagination/Pagination.vue'
import FrontendPagination from '@/Components/Public/Default/Pagination/FrontendPagination.vue'
import SectionVideoList from '@/Components/Public/Default/Blog/BlogVideo/SectionVideoList.vue'
import SectionBanners from '@/Components/Public/Default/Blog/BlogBanner/SectionBanners.vue'
import ProcessingModeSwitcher from '@/Components/Admin/UI/Processing/ProcessingModeSwitcher.vue'

const { t } = useI18n()

/** Props страницы */
const props = defineProps({
    locale: { type: String, default: 'ru' },

    useServerProcessing: { type: Boolean, default: false },
    publicBlogArticlesProcessingMode: { type: String, default: 'server' },

    title: { type: String, default: '' },
    canLogin: { type: Boolean, default: false },
    canRegister: { type: Boolean, default: false },

    rubricTree: { type: Array, default: () => [] },

    articles: { type: [Array, Object], default: () => [] },
    articlesCount: { type: Number, default: 0 },
    articlesFound: { type: Number, default: 0 },

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

/** Дерево рубрик для левого сайдбара */
const rubricTree = computed(() => {
    return Array.isArray(props.rubricTree) ? props.rubricTree : []
})

/** Универсальный список статей: server paginator data или frontend array */
const articlesData = computed(() => {
    if (Array.isArray(props.articles)) {
        return props.articles
    }

    if (Array.isArray(props.articles?.data)) {
        return props.articles.data
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
const VIEW_KEY = 'public_blog_articles_view'

/** Режим отображения карточки/строки для server/frontend */
const viewMode = ref(
    String(props.filters?.view || localStorage.getItem(VIEW_KEY) || 'grid')
)

/** Сохраняем режим отображения локально */
watch(viewMode, (value) => {
    localStorage.setItem(VIEW_KEY, value)
})

/** Опции сортировки для toolbar */
const articleSortOptions = [
    { value: 'sortAsc', label: `${t('sortNumber')} 0→9` },
    { value: 'sortDesc', label: `${t('sortNumber')} 9→0` },

    { value: 'titleAsc', label: `${t('title')} A→Z` },
    { value: 'titleDesc', label: `${t('title')} Z→A` },

    { value: 'viewsDesc', label: `${t('views')} 9→0` },
    { value: 'viewsAsc', label: `${t('views')} 0→9` },

    { value: 'likesDesc', label: `${t('likes')} 9→0` },
    { value: 'likesAsc', label: `${t('likes')} 0→9` },

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

/** Получение названия статьи из разных возможных структур ресурса */
const getArticleTitle = (article) => {
    return article.title
        || article.name
        || article.translation?.title
        || article.current_translation?.title
        || article.translations?.[0]?.title
        || ''
}

/** Получение краткого текста статьи */
const getArticleShort = (article) => {
    return article.short
        || article.description
        || article.translation?.short
        || article.translation?.description
        || article.current_translation?.short
        || article.current_translation?.description
        || article.translations?.[0]?.short
        || article.translations?.[0]?.description
        || ''
}

/** Локальный поиск frontend */
const filteredArticles = computed(() => {
    const query = normalizeText(q.value).trim()

    if (!query) {
        return articlesData.value
    }

    return articlesData.value.filter((article) => {
        return [
            getArticleTitle(article),
            getArticleShort(article),
            article.url,
            article.slug,
            article.owner?.name,
            article.owner?.email,
        ].some((value) => normalizeText(value).includes(query))
    })
})

/** Локальная сортировка frontend */
const sortedArticles = computed(() => {
    const list = [...filteredArticles.value]

    return list.sort((a, b) => {
        switch (sort.value) {
            case 'sortAsc':
                return (a.sort ?? 0) - (b.sort ?? 0)

            case 'sortDesc':
                return (b.sort ?? 0) - (a.sort ?? 0)

            case 'titleAsc':
                return normalizeText(getArticleTitle(a))
                    .localeCompare(normalizeText(getArticleTitle(b)))

            case 'titleDesc':
                return normalizeText(getArticleTitle(b))
                    .localeCompare(normalizeText(getArticleTitle(a)))

            case 'viewsAsc':
                return (a.views ?? 0) - (b.views ?? 0)

            case 'viewsDesc':
                return (b.views ?? 0) - (a.views ?? 0)

            case 'likesAsc':
                return (a.likes_count ?? 0) - (b.likes_count ?? 0)

            case 'likesDesc':
                return (b.likes_count ?? 0) - (a.likes_count ?? 0)

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
const frontendPaginatedArticles = computed(() => {
    const start = (frontendCurrentPage.value - 1) * perPage.value

    return sortedArticles.value.slice(start, start + perPage.value)
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
    return Number(props.articles?.meta?.current_page ?? props.articles?.current_page ?? 1) || 1
})

/** Последняя страница server-пагинации */
const lastPage = computed(() => {
    return Number(props.articles?.meta?.last_page ?? props.articles?.last_page ?? 1) || 1
})

/** Маршрут списка статей для server-режима */
const indexRoute = () => route('public.blogArticles.index')

/** Server-загрузка статей с query-параметрами */
const reloadArticles = (page = 1) => {
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
    reloadArticles(1)
}

/** Сброс поиска и сортировки для обоих режимов */
const resetSearch = () => {
    q.value = ''
    sort.value = DEFAULT_SORT
    frontendCurrentPage.value = 1

    if (props.useServerProcessing) {
        reloadArticles(1)
    }
}

/** Server-изменение сортировки */
const updateSort = (value) => {
    sort.value = value || DEFAULT_SORT

    if (props.useServerProcessing) {
        reloadArticles(1)
    }
}

/** Изменение режима отображения для обоих режимов */
const updateViewMode = (value) => {
    viewMode.value = value || 'grid'

    if (props.useServerProcessing) {
        reloadArticles(currentPage.value)
    }
}

/** Server-переход на страницу */
const goToPage = (page) => {
    const value = Number(page)

    if (!Number.isFinite(value)) {
        return
    }

    const safePage = Math.max(1, Math.min(value, lastPage.value))

    reloadArticles(safePage)
}

/** Server-предыдущая страница */
const goPrev = () => {
    if (currentPage.value <= 1) {
        return
    }

    goToPage(currentPage.value - 1)
}

/** Server-следующая страница */
const goNext = () => {
    if (currentPage.value >= lastPage.value) {
        return
    }

    goToPage(currentPage.value + 1)
}

/* ===================== COMMON VIEW ===================== */

/** Итоговый список для отображения: server data или frontend page */
const displayedArticles = computed(() => {
    return props.useServerProcessing
        ? articlesData.value
        : frontendPaginatedArticles.value
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
const articleGridCols = computed(() => {
    const leftExpanded = showLeft.value && !leftCollapsed.value
    const rightExpanded = showRight.value && !rightCollapsed.value

    return leftExpanded && rightExpanded ? 2 : 3
})

/** Ключ localStorage для нижней админ-панели */
const ADMIN_PANEL_KEY = 'public_admin_panel_collapsed'

/** Состояние нижней админ-панели */
const adminPanelCollapsed = ref(
    getStoredBoolean(ADMIN_PANEL_KEY, false)
)

/** Сохраняем состояние нижней админ-панели */
watch(adminPanelCollapsed, (value) => {
    localStorage.setItem(ADMIN_PANEL_KEY, String(value))
})

/** Переключение нижней админ-панели */
const toggleAdminPanel = () => {
    adminPanelCollapsed.value = !adminPanelCollapsed.value
}

</script>

<template>
    <Head>
        <title>{{ t('articles') }}</title>
        <meta name="title" :content="''" />
        <meta name="keywords" :content="''" />
        <meta name="description" :content="''" />

        <meta property="og:title" :content="''" />
        <meta property="og:description" :content="''" />
        <meta property="og:type" content="website" />
        <meta property="og:url" :content="`/`" />
        <meta property="og:image" :content="''" />
        <meta property="og:locale" :content="'ru_RU'" />

        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="''" />
        <meta name="twitter:description" :content="''" />
        <meta name="twitter:image" :content="''" />

        <meta name="DC.title" :content="''" />
        <meta name="DC.description" :content="''" />
        <meta name="DC.identifier" :content="`/`" />
        <meta name="DC.language" :content="'ru'" />
    </Head>

    <DefaultLayout :title="title" :can-login="canLogin" :can-register="canRegister">
        <Navbar />

        <div class="min-h-screen px-3 max-w-full">
            <main class="mx-auto flex flex-col lg:flex-row gap-4 tracking-wider">
                <!-- LEFT -->
                <aside
                    v-if="showLeft"
                    class="shrink-0 mt-12 sm:mt-16 transition-all duration-300"
                    :class="leftCollapsed ? 'lg:w-10' : 'lg:w-64'"
                >
                    <LeftSidebar
                        :rubric-tree="rubricTree"
                        :collapsed="leftCollapsed"
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
                                <li>
                                    <Link
                                        :href="route('public.blogRubrics.index')"
                                        class="breadcrumb-link hover:underline"
                                    >
                                        {{ t('rubrics') }}
                                    </Link>
                                </li>
                                <li><span class="mx-2 breadcrumbs">/</span></li>
                                <li class="breadcrumbs">
                                    {{ t('articles') }}
                                </li>
                            </ol>
                        </nav>

                        <!-- Заголовок -->
                        <div class="my-3 flex flex-wrap items-center justify-center gap-2 title">
                            <svg
                                class="h-5 w-5"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 384 512"
                                fill="currentColor"
                            >
                                <path
                                    d="M288 248v28c0 6.6-5.4 12-12 12H108c-6.6 0-12-5.4-12-12v-28c0-6.6 5.4-12 12-12h168c6.6 0 12 5.4 12 12zm-12 72H108c-6.6 0-12 5.4-12 12v28c0 6.6 5.4 12 12 12h168c6.6 0 12-5.4 12-12v-28c0-6.6-5.4-12-12-12zm108-188.1V464c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V48C0 21.5 21.5 0 48 0h204.1C264.8 0 277 5.1 286 14.1L369.9 98c9 8.9 14.1 21.2 14.1 33.9zM256 51.9V128h76.1L256 51.9zM336 464V176H232c-13.3 0-24-10.7-24-24V48H48v416h288z" />
                            </svg>

                            <h1 class="text-2xl font-bold">
                                {{ t('articles') }}
                            </h1>
                        </div>

                        <!-- Подзаголовок -->
                        <div class="my-1 text-sm subtitle text-center">
                            Изучайте статьи и руководства от экспертов сообщества
                        </div>

                        <!-- Поиск, переключатель вида, сортировка -->
                        <EntityPageToolbar
                            v-if="useServerProcessing"
                            v-model="q"
                            :found="articlesFound"
                            :view-mode="viewMode"
                            :sort-value="sort"
                            :sort-options="articleSortOptions"
                            :default-sort="DEFAULT_SORT"
                            :found-label="t('articles')"
                            :search-placeholder="t('searchByName')"
                            @submit="submitSearch"
                            @reset="resetSearch"
                            @update:viewMode="updateViewMode"
                            @update:sortValue="updateSort"
                        />

                        <FrontendEntityPageToolbar
                            v-else
                            v-model="q"
                            :found="sortedArticles.length"
                            :view-mode="viewMode"
                            :sort-value="sort"
                            :sort-options="articleSortOptions"
                            :default-sort="DEFAULT_SORT"
                            :found-label="t('articles')"
                            :search-placeholder="t('searchByName')"
                            @reset="resetSearch"
                            @update:viewMode="updateViewMode"
                            @update:sortValue="updateSort"
                        />

                        <div ref="scrollTarget"></div>

                        <!-- Empty -->
                        <div
                            v-if="displayedArticles.length === 0"
                            class="mt-6 text-center text-slate-700 dark:text-slate-300"
                        >
                            {{ t('noData') }}
                        </div>

                        <!-- Views -->
                        <div v-else>
                            <RubricArticleGrid
                                v-if="viewMode === 'grid'"
                                :articles="displayedArticles"
                                :cols="articleGridCols"
                            />
                            <RubricArticleRows
                                v-else
                                :articles="displayedArticles"
                            />
                        </div>

                        <!-- Пагинация -->
                        <Pagination
                            v-if="useServerProcessing"
                            :current-page="currentPage"
                            :last-page="lastPage"
                            :found="articlesFound"
                            @prev="goPrev"
                            @next="goNext"
                            @go="goToPage"
                        />

                        <FrontendPagination
                            v-else
                            v-model:currentPage="frontendCurrentPage"
                            :items-per-page="perPage"
                            :total-items="sortedArticles.length"
                        />

                        <!-- Нижние блоки -->
                        <SectionVideoList :videos="mainVideos" />
                        <SectionBanners :banners="mainBanners" />
                    </div>
                </div>

                <!-- RIGHT -->
                <aside
                    v-if="showRight"
                    class="shrink-0 lg:mt-16 transition-all duration-300"
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
        <div
            v-if="isAdmin"
            class="fixed bottom-0 left-0 right-0 z-[9999]"
        >
            <!-- Ярлычок сворачивания/разворачивания -->
            <button
                type="button"
                class="absolute left-1/2 -translate-x-1/2
                       flex h-3 w-6 items-center justify-center
                       rounded-t-full border border-b-0
                       border-slate-400/60
                       bg-slate-300/95 dark:bg-slate-700/95
                       text-slate-700 dark:text-slate-300
                       shadow-md backdrop-blur-md
                       hover:text-indigo-600 dark:hover:text-indigo-300"
                :class="adminPanelCollapsed ? 'bottom-0' : 'bottom-8'"
                :title="adminPanelCollapsed ? t('show') : t('hide')"
                @click="toggleAdminPanel"
            >
                <svg
                    class="h-3 w-3 transition-transform duration-300"
                    :class="adminPanelCollapsed ? 'rotate-180' : ''"
                    fill="currentColor"
                    viewBox="0 0 320 512"
                >
                    <path
                        d="M182.6 137.4c-12.5-12.5-32.8-12.5-45.3 0l-128 128c-9.2 9.2-11.9 22.9-6.9 34.9S19.1 320 32 320h256c12.9 0 24.6-7.8 29.6-19.8s2.2-25.7-6.9-34.9l-128-128z"
                    />
                </svg>
            </button>

            <!-- Содержимое панели -->
            <div
                class="h-8 flex items-center justify-between px-3
               border-t border-slate-400/40
               bg-slate-300/90 dark:bg-slate-700/90
               backdrop-blur-md text-[11px] text-slate-100
               transition-transform duration-300"
                :class="adminPanelCollapsed ? 'translate-y-full' : 'translate-y-0'"
            >
                <div class="flex items-center gap-2">
                    <Link
                        :href="route('admin.index')"
                        class="bg-gray-200 dark:bg-gray-800 rounded-sm px-2 py-0.5
                       border-2 border-slate-500 hover:border-indigo-500"
                    >
                <span class="text-slate-700 dark:text-slate-300
                             hover:text-indigo-700 hover:dark:text-indigo-300">
                    {{ t('adminPanel') }}
                </span>
                    </Link>
                </div>

                <div class="flex items-center gap-2">
                    <ProcessingModeSwitcher
                        setting-key="publicBlogArticlesProcessingMode"
                        :mode="publicBlogArticlesProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="articlesCount"
                    />
                </div>
            </div>
        </div>
    </DefaultLayout>
</template>
