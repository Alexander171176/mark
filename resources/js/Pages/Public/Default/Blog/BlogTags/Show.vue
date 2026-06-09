<script setup>
/**
 * Страница конкретного тега блога.
 *
 * @version PulsarCMS 1.0
 * @author Александр
 */
import { computed, ref, watch } from 'vue'
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

/** Props страницы */
const props = defineProps({
    title: { type: String, default: '' },
    canLogin: { type: Boolean, default: false },
    canRegister: { type: Boolean, default: false },

    tag: { type: Object, default: () => ({}) },
    locale: { type: String, default: 'ru' },

    articles: { type: Object, default: () => ({}) },
    articlesFound: { type: Number, default: 0 },
    filters: { type: Object, default: () => ({}) },

    rubricTree: { type: Array, default: () => [] },

    mainVideos: { type: [Array, Object], default: () => [] },
    mainBanners: { type: [Array, Object], default: () => [] },
})

/** Текущий тег */
const tag = computed(() => props.tag || {})

/** Активный перевод тега */
const tagTranslation = computed(() => tag.value.translation || {})

/** Название тега */
const tagName = computed(() => tagTranslation.value.name || tag.value.name || '')

/** Подзаголовок */
const tagSubtitle = computed(() => tagTranslation.value.subtitle || tag.value.subtitle || '')

/** Краткое описание */
const tagShort = computed(() => tagTranslation.value.short || tag.value.short || '')

/** Полное описание */
const tagDescription = computed(() => {
    return tagTranslation.value.description || tag.value.description || tagShort.value || ''
})

/** SEO заголовок */
const tagMetaTitle = computed(() => {
    return tagTranslation.value.meta_title || tag.value.meta_title || tagName.value
})

/** SEO ключевые слова */
const tagMetaKeywords = computed(() => {
    return tagTranslation.value.meta_keywords || tag.value.meta_keywords || ''
})

/** SEO описание */
const tagMetaDesc = computed(() => {
    return tagTranslation.value.meta_desc || tag.value.meta_desc || tagShort.value || ''
})

/** Дерево рубрик */
const rubricTree = computed(() => Array.isArray(props.rubricTree) ? props.rubricTree : [])

/** Данные статей из пагинатора */
const articlesData = computed(() => {
    const data = props.articles?.data

    return Array.isArray(data) ? data : []
})

/** Текущая страница статей */
const currentPage = computed(() => {
    return Number(props.articles?.meta?.current_page ?? props.articles?.current_page ?? 1) || 1
})

/** Последняя страница */
const lastPage = computed(() => {
    return Number(props.articles?.meta?.last_page ?? props.articles?.last_page ?? 1) || 1
})

/** Количество статей на странице */
const perPageArticles = computed(() => {
    const value = Number(props.filters?.per_page_articles ?? 3)

    return Number.isFinite(value) ? value : 3
})

/** Поиск по статьям */
const qArticles = ref(String(props.filters?.q_articles ?? ''))

/** Сортировка статей */
const DEFAULT_SORT = 'sortAsc'
const sortArticles = ref(String(props.filters?.sort_articles ?? DEFAULT_SORT))

/** Ключ хранения режима отображения */
const VIEW_KEY = 'public_blog_tag_articles_view'

/** Режим отображения */
const viewMode = ref(
    String(props.filters?.view || localStorage.getItem(VIEW_KEY) || 'grid')
)

/** Сохраняем режим отображения */
watch(viewMode, (value) => {
    localStorage.setItem(VIEW_KEY, value)
})

/** Опции сортировки статей */
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

/** Маршрут страницы тега */
const showRoute = () => route('public.blogTags.show', { url: tag.value.slug })

/** Загрузка статей */
const reloadArticles = (page = 1) => {
    router.get(
        showRoute(),
        {
            q_articles: qArticles.value || undefined,
            sort_articles: sortArticles.value || undefined,
            per_page_articles: perPageArticles.value,
            page_articles: page,
            view: viewMode.value || undefined,
        },
        {
            preserveState: true,
            replace: true,
            preserveScroll: true,
        }
    )
}

/** Поиск */
const submitArticleSearch = () => {
    reloadArticles(1)
}

/** Сброс фильтров */
const resetArticleSearch = () => {
    qArticles.value = ''
    sortArticles.value = DEFAULT_SORT

    reloadArticles(1)
}

/** Изменение сортировки */
const updateArticleSort = (value) => {
    sortArticles.value = value || DEFAULT_SORT

    reloadArticles(1)
}

/** Изменение режима отображения */
const updateViewMode = (value) => {
    viewMode.value = value || 'grid'

    reloadArticles(currentPage.value)
}

/** Переход на страницу */
const goToPage = (page) => {
    const value = Number(page)

    if (!Number.isFinite(value)) return

    const safePage = Math.max(1, Math.min(value, lastPage.value))

    reloadArticles(safePage)
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

/** Настройки сайта */
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

/** Количество колонок сетки */
const articleGridCols = computed(() => {
    const leftExpanded = showLeft.value && !leftCollapsed.value
    const rightExpanded = showRight.value && !rightCollapsed.value

    return leftExpanded && rightExpanded ? 2 : 3
})

/** Проверка что иконка является SVG */
const hasSvgIcon = computed(() => {
    if (!tag.value?.icon) return false

    return /^\s*<svg[\s\S]*<\/svg>\s*$/i.test(String(tag.value.icon))
})
</script>

<template>
    <Head>
        <title>{{ tagMetaTitle }}</title>

        <meta name="title" :content="tagMetaTitle" />
        <meta name="keywords" :content="tagMetaKeywords" />
        <meta name="description" :content="tagMetaDesc" />

        <meta property="og:title" :content="tagMetaTitle" />
        <meta property="og:description" :content="tagMetaDesc" />
        <meta property="og:type" content="website" />
        <meta property="og:url" :content="`/blog/tags/${tag.slug || ''}`" />
        <meta property="og:image" content="" />
        <meta property="og:locale" :content="locale" />

        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="tagMetaTitle" />
        <meta name="twitter:description" :content="tagMetaDesc" />
        <meta name="twitter:image" content="" />

        <meta name="DC.title" :content="tagMetaTitle" />
        <meta name="DC.description" :content="tagMetaDesc" />
        <meta name="DC.identifier" :content="`/blog/tags/${tag.slug || ''}`" />
        <meta name="DC.language" :content="locale" />
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
                                    <Link :href="route('public.blogRubrics.index')"
                                          class="breadcrumb-link hover:underline">
                                        {{ t('rubrics') }}
                                    </Link>
                                </li>
                                <li><span class="mx-2 breadcrumbs">/</span></li>
                                <li>
                                    <Link :href="route('public.blogArticles.index')"
                                          class="breadcrumb-link hover:underline">
                                        {{ t('articles') }}
                                    </Link>
                                </li>
                                <li><span class="mx-2 breadcrumbs">/</span></li>
                                <li>
                                    <Link :href="route('public.blogVideos.index')"
                                          class="breadcrumb-link hover:underline">
                                        {{ t('videos') }}
                                    </Link>
                                </li>
                                <li><span class="mx-2 breadcrumbs">/</span></li>
                                <li class="breadcrumbs">
                                    {{ t('tag') }} #{{ tagName }}
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
                                    v-if="hasSvgIcon"
                                    class="flex"
                                    v-html="tag.icon"
                                />
                                <h1 class="text-2xl font-bold">
                                    #{{ tagName }}
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
                        <div v-if="tagSubtitle" class="mt-1 text-sm subtitle text-center">
                            {{ tagSubtitle }}
                        </div>

                        <!-- description -->
                        <div v-if="tagDescription" class="mt-1 mb-3 text-sm subtitle text-center">
                            {{ tagDescription }}
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
                            @update:viewMode="updateViewMode"
                            @update:sortValue="updateArticleSort"
                        />

                        <!-- Empty -->
                        <div
                            v-if="articlesData.length === 0"
                            class="mt-6 text-center text-slate-700 dark:text-slate-300"
                        >
                            {{ t('noData') }}
                        </div>

                        <!-- Views -->
                        <RubricArticleGrid
                            v-if="viewMode === 'grid'"
                            :articles="articlesData"
                            :cols="articleGridCols"
                        />

                        <RubricArticleRows
                            v-else
                            :articles="articlesData"
                        />

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
