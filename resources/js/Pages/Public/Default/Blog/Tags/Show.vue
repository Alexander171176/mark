<script setup>
/**
 * Страница конкретного Тега (Блог)
 * - шапка, центральная часть подвал
 * - светлый, тёмный режим
 * - серверный поиск
 * - серверная пагинация
 * - серверная сортировка
 * - показ карточками, в строку
 * - показ главных видео, баннеров внизу страницы
 * - показ, скрытие колонок со статьями, баннерами, видео
 * - показ в колонках: аккордеона рубрик, списка тегов
 *
 *   @version PulsarCMS 1.0
 *   @author Александр
 */
import { computed, defineProps, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { Head, Link, router, usePage } from '@inertiajs/vue3'

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

const props = defineProps({
    title: String,
    canLogin: Boolean,
    canRegister: Boolean,

    tag: { type: Object, default: () => ({}) },
    locale: String,

    articles: { type: Object, default: () => ({}) },
    articlesFound: { type: Number, default: 0 },
    filters: { type: Object, default: () => ({}) },

    rubricTree: { type: Array, default: () => [] },

    mainVideos: { type: Array, default: () => [] },
    mainBanners: { type: Array, default: () => [] },
})

const rubricTree = computed(() => Array.isArray(props.rubricTree) ? props.rubricTree : [])

const VIEW_KEY = 'public_view'
const viewMode = ref(localStorage.getItem(VIEW_KEY) || 'grid')

watch(viewMode, (v) => {
    localStorage.setItem(VIEW_KEY, v)
})

const articlesData = computed(() => {
    const data = props.articles?.data
    return Array.isArray(data) ? data : []
})

const currentPage = computed(() => {
    return Number(props.articles?.meta?.current_page ?? props.articles?.current_page ?? 1) || 1
})

const lastPage = computed(() => {
    return Number(props.articles?.meta?.last_page ?? props.articles?.last_page ?? 1) || 1
})

const perPageArticles = computed(() => {
    const val = Number(props.filters?.per_page_articles ?? 12)
    return Number.isFinite(val) ? val : 12
})

const qArticles = ref(String(props.filters?.q_articles ?? ''))

const DEFAULT_SORT = 'date_desc'
const sortArticles = ref(String(props.filters?.sort_articles ?? DEFAULT_SORT))

const articleSortOptions = [
    { value: 'sort_asc', label: t('sortDefault') },
    { value: 'sort_desc', label: t('sortReverse') },
    { value: 'title_asc', label: t('sortNameAsc') },
    { value: 'title_desc', label: t('sortNameDesc') },
    { value: 'views_desc', label: t('sortPopularFirst') },
    { value: 'views_asc', label: t('sortUnpopularFirst') },
    { value: 'date_desc', label: t('sortNewestFirst') },
    { value: 'date_asc', label: t('sortOldestFirst') },
    { value: 'likes_desc', label: t('sortLikesDesc') },
    { value: 'likes_asc', label: t('sortLikesAsc') },
]

const submitArticleSearch = () => {
    router.get(
        route('public.tags.show', props.tag.slug),
        {
            q_articles: qArticles.value || undefined,
            sort_articles: sortArticles.value || undefined,
            per_page_articles: perPageArticles.value,
            page_articles: 1,
        },
        { preserveState: true, replace: true, preserveScroll: true }
    )
}

const resetArticleSearch = () => {
    qArticles.value = ''
    sortArticles.value = DEFAULT_SORT

    router.get(
        route('public.tags.show', props.tag.slug),
        {
            per_page_articles: perPageArticles.value,
            sort_articles: sortArticles.value,
            page_articles: 1,
        },
        { preserveState: true, replace: true, preserveScroll: true }
    )
}

const pageInput = ref(currentPage.value)

watch(currentPage, (v) => {
    pageInput.value = v
})

const goToPage = (page) => {
    const p = Number(page)
    if (!Number.isFinite(p)) return

    const safe = Math.max(1, Math.min(p, lastPage.value))

    router.get(
        route('public.tags.show', props.tag.slug),
        {
            q_articles: qArticles.value || undefined,
            sort_articles: sortArticles.value || undefined,
            per_page_articles: perPageArticles.value,
            page_articles: safe,
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

const { siteSettings } = usePage().props

const showLeft = computed(() =>
    !siteSettings?.ViewLeftColumn || siteSettings.ViewLeftColumn === 'true'
)

const showRight = computed(() =>
    !siteSettings?.ViewRightColumn || siteSettings.ViewRightColumn === 'true'
)

const leftCollapsed = ref(false)
const rightCollapsed = ref(false)

const articleGridCols = computed(() => {
    const leftExpanded = showLeft.value && !leftCollapsed.value
    const rightExpanded = showRight.value && !rightCollapsed.value

    return leftExpanded && rightExpanded ? 2 : 3
})
</script>

<template>
    <Head>
        <title>{{ tag.name || '' }}</title>
        <meta name="title" :content="tag.meta_title || tag.name || ''" />
        <meta name="keywords" :content="tag.meta_keywords || ''" />
        <meta name="description" :content="tag.meta_desc || tag.short || ''" />

        <meta property="og:title" :content="tag.meta_title || tag.name || ''" />
        <meta property="og:description" :content="tag.meta_desc || tag.short || ''" />
        <meta property="og:type" content="website" />
        <meta property="og:url" :content="`/tags/${tag.slug || ''}`" />
        <meta property="og:image" :content="''" />
        <meta property="og:locale" :content="tag.locale || 'ru_RU'" />

        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="tag.meta_title || tag.name || ''" />
        <meta name="twitter:description" :content="tag.meta_desc || tag.short || ''" />
        <meta name="twitter:image" :content="''" />

        <meta name="DC.title" :content="tag.meta_title || tag.name || ''" />
        <meta name="DC.description" :content="tag.meta_desc || tag.short || ''" />
        <meta name="DC.identifier" :content="`/tags/${tag.slug || ''}`" />
        <meta name="DC.language" :content="tag.locale || 'ru'" />
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

                        <!-- Breadcrumbs -->
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
                                <li>
                                    <Link
                                        :href="route('public.articles.index')"
                                        class="breadcrumb-link hover:underline"
                                    >
                                        {{ t('articles') }}
                                    </Link>
                                </li>
                                <li><span class="mx-2 breadcrumbs">/</span></li>
                                <li>
                                    <Link
                                        :href="route('public.videos.index')"
                                        class="breadcrumb-link hover:underline"
                                    >
                                        {{ t('videos') }}
                                    </Link>
                                </li>
                                <li><span class="mx-2 breadcrumbs">/</span></li>
                                <li class="breadcrumbs">
                                    {{ t('tag') }} #{{ tag.name }}
                                </li>
                            </ol>
                        </nav>

                        <!-- Stats / title / views -->
                        <div class="flex items-center justify-between gap-1">
                            <div
                                :title="t('articles')"
                                class="flex items-center justify-center gap-1"
                            >
                                <svg
                                    class="h-4 w-4 text-slate-600/85 dark:text-slate-200/85"
                                    viewBox="0 0 384 512"
                                    fill="currentColor"
                                >
                                    <path d="M288 248v28c0 6.6-5.4 12-12 12H108c-6.6 0-12-5.4-12-12v-28c0-6.6 5.4-12 12-12h168c6.6 0 12 5.4 12 12zm-12 72H108c-6.6 0-12 5.4-12 12v28c0 6.6 5.4 12 12 12h168c6.6 0 12-5.4 12-12v-28c0-6.6-5.4-12-12-12zm108-188.1V464c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V48C0 21.5 21.5 0 48 0h204.1C264.8 0 277 5.1 286 14.1L369.9 98c9 8.9 14.1 21.2 14.1 33.9zm-128-80V128h76.1L256 51.9zM336 464V176H232c-13.3 0-24-10.7-24-24V48H48v416h288z"/>
                                </svg>
                                <span class="text-center text-sm text-gray-500">
                                    {{ tag.articles_count || articlesFound }} ·
                                </span>
                            </div>

                            <div class="flex flex-wrap items-center justify-center gap-3 title my-3">
                                <span
                                    v-if="tag.icon"
                                    class="flex"
                                    v-html="tag.icon"
                                />
                                <h1 class="text-2xl font-bold">
                                    #{{ tag.name }}
                                </h1>
                            </div>

                            <div
                                :title="t('views')"
                                class="flex items-center justify-center gap-1"
                            >
                                <svg
                                    class="h-4 w-4 text-slate-600/85 dark:text-slate-200/85"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 576 512"
                                    fill="currentColor"
                                >
                                    <path
                                        d="M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z"
                                    />
                                </svg>
                                <span class="text-center text-sm text-gray-500">
                                    {{ tag.views || 0 }} ·
                                </span>
                            </div>
                        </div>

                        <!-- subtitle -->
                        <div
                            v-if="tag.subtitle"
                            class="mt-1 text-sm subtitle text-center"
                        >
                            {{ tag.subtitle }}
                        </div>

                        <!-- description -->
                        <div
                            v-if="tag.description"
                            class="mt-1 mb-3 text-sm subtitle text-center"
                        >
                            {{ tag.description }}
                        </div>

                        <!-- Toolbar -->
                        <EntityPageToolbar
                            v-model="qArticles"
                            :found="articlesFound"
                            :view-mode="viewMode"
                            :sort-value="sortArticles"
                            :sort-options="articleSortOptions"
                            :default-sort="DEFAULT_SORT"
                            :found-label="t('articles')"
                            :search-placeholder="t('searchByName')"
                            @submit="submitArticleSearch"
                            @reset="resetArticleSearch"
                            @update:viewMode="viewMode = $event"
                            @update:sortValue="sortArticles = $event"
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

                        <!-- Pagination -->
                        <Pagination
                            :current-page="currentPage"
                            :last-page="lastPage"
                            :found="articlesFound"
                            @prev="goPrev"
                            @next="goNext"
                            @go="goToPage"
                        />

                        <!-- Bottom blocks -->
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
