<script setup>
/**
 * Страница списка статей (Блог)
 * - шапка, центральная часть, подвал
 * - светлый, тёмный режим
 * - серверный поиск
 * - серверная пагинация
 * - серверная сортировка
 * - показ карточками, в строку
 * - показ главных видео, баннеров внизу страницы
 * - показ/скрытие колонок
 * - показ rubricTree в левой колонке
 *
 * @version PulsarCMS 1.0
 * @author Александр
 */
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { computed, defineProps, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'

import DefaultLayout from '@/Layouts/DefaultLayout.vue'
import Navbar from '@/Partials/Default/Navbar.vue'
import FooterBlog from '@/Partials/Default/FooterBlog.vue'
import Progress from '@/Components/Public/Default/Progress/Progress.vue'
import LeftSidebar from '@/Components/Public/Default/Partials/LeftSidebar.vue'
import RightSidebar from '@/Components/Public/Default/Partials/RightSidebar.vue'
import EntityPageToolbar from '@/Components/Public/Default/PageToolbar/EntityPageToolbar.vue'
import RubricArticleGrid from '@/Components/Public/Default/Blog/Rubric/RubricArticleGrid.vue'
import RubricArticleRows from '@/Components/Public/Default/Blog/Rubric/RubricArticleRows.vue'
import Pagination from '@/Components/Public/Default/Pagination/Pagination.vue'
import SectionVideoList from '@/Components/Public/Default/Blog/Video/SectionVideoList.vue'
import SectionBanners from '@/Components/Public/Default/Blog/Banner/SectionBanners.vue'

const { t } = useI18n()

/** Props */
const props = defineProps({
    title: String,
    canLogin: Boolean,
    canRegister: Boolean,

    rubricTree: { type: Array, default: () => [] },

    articles: { type: Object, default: () => ({}) },
    articlesCount: { type: Number, default: 0 },
    articlesFound: { type: Number, default: 0 },
    filters: { type: Object, default: () => ({}) },

    tags: { type: Array, default: () => [] },
    mainVideos: { type: Array, default: () => [] },
    mainBanners: { type: Array, default: () => [] },

    locale: String
})

/** Иерархия рубрик */
const rubricTree = computed(() => Array.isArray(props.rubricTree) ? props.rubricTree : [])

/** Режим показа карточками/в строку */
const VIEW_KEY = 'public_articles_view'
const viewMode = ref(localStorage.getItem(VIEW_KEY) || 'grid') // 'grid' | 'row'

watch(viewMode, (v) => {
    localStorage.setItem(VIEW_KEY, v)
})

/** Данные статей */
const articlesData = computed(() => {
    const data = props.articles?.data
    return Array.isArray(data) ? data : []
})

/** Текущая страница, Количество страниц */
const currentPage = computed(() => {
    return Number(props.articles?.meta?.current_page ?? props.articles?.current_page ?? 1) || 1
})

const lastPage = computed(() => {
    return Number(props.articles?.meta?.last_page ?? props.articles?.last_page ?? 1) || 1
})

const perPage = computed(() => {
    const val = Number(props.filters?.per_page ?? 12)
    return Number.isFinite(val) ? val : 12
})

/** Поиск */
const q = ref(String(props.filters?.q ?? ''))

/** Сортировка */
const DEFAULT_SORT = 'date_desc'
const sort = ref(String(props.filters?.sort ?? DEFAULT_SORT))

/** Опции сортировки */
const articleSortOptions = [
    { value: 'sort_asc', label: t('sortDefault') },
    { value: 'sort_desc', label: t('sortReverse') },
    { value: 'title_asc', label: t('sortNameAsc') },
    { value: 'title_desc', label: t('sortNameDesc') },
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
        route('public.articles.index'),
        {
            q: q.value || undefined,
            sort: sort.value || undefined,
            per_page: perPage.value,
            page: 1
        },
        { preserveState: true, replace: true, preserveScroll: true }
    )
}

/** Сброс поиска */
const resetSearch = () => {
    q.value = ''
    sort.value = DEFAULT_SORT

    router.get(
        route('public.articles.index'),
        {
            per_page: perPage.value,
            sort: sort.value,
            page: 1
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
        route('public.articles.index'),
        {
            q: q.value || undefined,
            sort: sort.value || undefined,
            per_page: perPage.value,
            page: safe
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
const articleGridCols = computed(() => {
    const leftExpanded = showLeft.value && !leftCollapsed.value
    const rightExpanded = showRight.value && !rightCollapsed.value

    return leftExpanded && rightExpanded ? 2 : 3
})
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

        <div class="min-h-screen px-1.5">
            <main class="mx-auto flex flex-col lg:flex-row gap-4 tracking-wider">
                <!-- LEFT -->
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
                                        :href="route('public.rubrics.index')"
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
                            @update:viewMode="viewMode = $event"
                            @update:sortValue="sort = $event"
                        />

                        <!-- Empty -->
                        <div
                            v-if="articlesData.length === 0"
                            class="mt-6 text-center text-slate-700 dark:text-slate-300"
                        >
                            {{ t('noData') }}
                        </div>

                        <!-- Views -->
                        <div v-else>
                            <RubricArticleGrid
                                v-if="viewMode === 'grid'"
                                :articles="articlesData"
                                :cols="articleGridCols"
                            />
                            <RubricArticleRows
                                v-else
                                :articles="articlesData"
                            />
                        </div>

                        <!-- Пагинация -->
                        <Pagination
                            :current-page="currentPage"
                            :last-page="lastPage"
                            :found="articlesFound"
                            @prev="goPrev"
                            @next="goNext"
                            @go="goToPage"
                        />

                        <!-- Нижние блоки -->
                        <SectionVideoList :videos="mainVideos" />
                        <SectionBanners :banners="mainBanners" />
                    </div>
                </div>

                <!-- RIGHT -->
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
