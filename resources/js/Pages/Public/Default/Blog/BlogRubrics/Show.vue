<script setup>
/**
 * Страница конкретной рубрики блога.
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
import RubricGrid from '@/Components/Public/Default/Blog/Rubric/RubricGrid.vue'
import RubricRows from '@/Components/Public/Default/Blog/Rubric/RubricRows.vue'
import SectionVideoList from '@/Components/Public/Default/Blog/Video/SectionVideoList.vue'
import SectionBanners from '@/Components/Public/Default/Blog/Banner/SectionBanners.vue'
import ImageGalleryMain from '@/Components/Public/Default/Media/ImageGalleryMain.vue'

const { t } = useI18n()

/** Props страницы */
const props = defineProps({
    title: { type: String, default: '' },
    canLogin: { type: Boolean, default: false },
    canRegister: { type: Boolean, default: false },

    rubric: { type: Object, default: () => ({}) },
    locale: { type: String, default: 'ru' },

    articles: { type: Object, default: () => ({}) },
    articlesFound: { type: Number, default: 0 },
    filters: { type: Object, default: () => ({}) },

    rubricTree: { type: Array, default: () => [] },

    mainVideos: { type: Array, default: () => [] },
    mainBanners: { type: Array, default: () => [] },
})

/** Текущая рубрика */
const rubric = computed(() => props.rubric || {})

/** Текущий перевод рубрики */
const rubricTranslation = computed(() => rubric.value.translation || {})

/** Заголовок рубрики */
const rubricTitle = computed(() => {
    return rubricTranslation.value.title || rubric.value.title || ''
})

/** Краткое описание рубрики */
const rubricShort = computed(() => {
    return rubricTranslation.value.short || rubric.value.short || ''
})

/** Полное описание рубрики */
const rubricDescription = computed(() => {
    return rubricTranslation.value.description || rubric.value.description || rubricShort.value || ''
})

/** SEO title */
const rubricMetaTitle = computed(() => {
    return rubricTranslation.value.meta_title || rubric.value.meta_title || rubricTitle.value
})

/** SEO keywords */
const rubricMetaKeywords = computed(() => {
    return rubricTranslation.value.meta_keywords || rubric.value.meta_keywords || ''
})

/** SEO description */
const rubricMetaDesc = computed(() => {
    return rubricTranslation.value.meta_desc || rubric.value.meta_desc || rubricShort.value || ''
})

/** Дерево рубрик для левого аккордеона */
const rubricTree = computed(() => Array.isArray(props.rubricTree) ? props.rubricTree : [])

/** Дочерние рубрики */
const childRubrics = computed(() => {
    return Array.isArray(rubric.value.children) ? rubric.value.children : []
})

/** Изображения рубрики */
const rubricImages = computed(() => {
    return Array.isArray(rubric.value.images) ? rubric.value.images : []
})

/** Есть ли изображения рубрики */
const hasRubricImages = computed(() => rubricImages.value.length > 0)

/** Данные статей из пагинатора */
const articlesData = computed(() => {
    const data = props.articles?.data

    return Array.isArray(data) ? data : []
})

/** Текущая страница статей */
const currentPage = computed(() => {
    return Number(props.articles?.meta?.current_page ?? props.articles?.current_page ?? 1) || 1
})

/** Последняя страница статей */
const lastPage = computed(() => {
    return Number(props.articles?.meta?.last_page ?? props.articles?.last_page ?? 1) || 1
})

/** Количество статей на странице */
const perPageArticles = computed(() => {
    const value = Number(props.filters?.per_page_articles ?? 3)

    return Number.isFinite(value) ? value : 3
})

/** Поиск по статьям внутри рубрики */
const qArticles = ref(String(props.filters?.q_articles ?? ''))

/** Сортировка статей */
const DEFAULT_SORT = 'sortAsc'
const sortArticles = ref(String(props.filters?.sort_articles ?? DEFAULT_SORT))

/** Режим отображения статей */
const VIEW_KEY = 'public_blog_rubric_articles_view'

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

/** Маршрут текущей рубрики */
const showRoute = () => route('public.blogRubrics.show', { url: rubric.value.url })

/** Загрузка статей рубрики с текущими фильтрами */
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

/** Поиск статей */
const submitArticleSearch = () => {
    reloadArticles(1)
}

/** Сброс поиска и сортировки статей */
const resetArticleSearch = () => {
    qArticles.value = ''
    sortArticles.value = DEFAULT_SORT

    reloadArticles(1)
}

/** Изменение сортировки статей */
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

/** Проверка что иконка действительно SVG */
const hasSvgIcon = computed(() => {
    if (!rubric.value?.icon) {
        return false
    }

    return /^\s*<svg[\s\S]*<\/svg>\s*$/i.test(
        String(rubric.value.icon)
    )
})
</script>

<template>
    <Head>
        <title>{{ rubricMetaTitle }}</title>

        <meta name="title" :content="rubricMetaTitle" />
        <meta name="keywords" :content="rubricMetaKeywords" />
        <meta name="description" :content="rubricMetaDesc" />

        <meta property="og:title" :content="rubricMetaTitle" />
        <meta property="og:description" :content="rubricMetaDesc" />
        <meta property="og:type" content="website" />
        <meta property="og:url" :content="`/blog/rubrics/${rubric.url}`" />
        <meta property="og:image" :content="rubric.icon || ''" />
        <meta property="og:locale" :content="locale" />

        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="rubricMetaTitle" />
        <meta name="twitter:description" :content="rubricMetaDesc" />
        <meta name="twitter:image" :content="rubric.icon || ''" />

        <meta name="DC.title" :content="rubricMetaTitle" />
        <meta name="DC.description" :content="rubricMetaDesc" />
        <meta name="DC.identifier" :content="`/blog/rubrics/${rubric.url}`" />
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
                        <nav class="text-sm mb-3" aria-label="Breadcrumb">
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

                                <li>
                                    <Link
                                        :href="route('public.blogRubrics.index')"
                                        class="breadcrumb-link hover:underline"
                                    >
                                        {{ t('rubrics') }}
                                    </Link>
                                </li>

                                <li>
                                    <span class="mx-2 breadcrumbs">/</span>
                                </li>

                                <li class="breadcrumbs">
                                    {{ rubricTitle }}
                                </li>
                            </ol>
                        </nav>

                        <div
                            v-if="hasRubricImages"
                            class="flex items-center justify-center"
                        >
                            <div class="w-full">
                                <ImageGalleryMain
                                    :images="rubricImages"
                                    :alt="rubricTitle"
                                    rounded-class="rounded-lg"
                                    shadow-class="shadow-lg shadow-gray-400 dark:shadow-gray-700"
                                    img-class="w-full h-full object-cover"
                                />
                            </div>
                        </div>

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
                                    <path d="M288 248v28c0 6.6-5.4 12-12 12H108c-6.6 0-12-5.4-12-12v-28c0-6.6 5.4-12 12-12h168c6.6 0 12 5.4 12 12zm-12 72H108c-6.6 0-12 5.4-12 12v28c0 6.6 5.4 12 12 12h168c6.6 0 12-5.4 12-12v-28c0-6.6-5.4-12-12-12zm108-188.1V464c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V48C0 21.5 21.5 0 48 0h204.1C264.8 0 277 5.1 286 14.1L369.9 98c9 8.9 14.1 21.2 14.1 33.9zm-128-80V128h76.1L256 51.9zM336 464V176H232c-13.3 0-24-10.7-24-24V48H48v416h288z" />
                                </svg>

                                <span class="text-center text-sm text-gray-500">
                                    {{ rubric.articles_count || 0 }} ·
                                </span>
                            </div>

                            <div class="flex flex-wrap items-center justify-center gap-3 title my-3">
                                <span
                                    v-if="hasSvgIcon"
                                    class="flex"
                                    v-html="rubric.icon"
                                />

                                <h1 class="text-2xl font-bold">
                                    {{ rubricTitle }}
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
                                    <path d="M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z" />
                                </svg>

                                <span class="text-center text-sm text-gray-500">
                                    {{ rubric.views || 0 }} ·
                                </span>
                            </div>
                        </div>

                        <div
                            v-if="rubricDescription"
                            class="mt-1 mb-3 text-sm subtitle text-center"
                        >
                            {{ rubricDescription }}
                        </div>

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

                        <div
                            v-if="articlesData.length === 0"
                            class="mt-6 text-center text-slate-700 dark:text-slate-300"
                        >
                            {{ t('noData') }}
                        </div>

                        <div v-else>
                            <RubricArticleGrid
                                v-if="viewMode === 'grid'"
                                :articles="articlesData"
                                :cols="rubricGridCols"
                            />

                            <RubricArticleRows
                                v-else
                                :articles="articlesData"
                            />
                        </div>

                        <Pagination
                            :current-page="currentPage"
                            :last-page="lastPage"
                            :found="articlesFound"
                            @prev="goPrev"
                            @next="goNext"
                            @go="goToPage"
                        />

                        <div v-if="childRubrics.length">
                            <div
                                class="mt-4 flex flex-wrap items-center justify-center gap-3
                                       text-slate-700/85 dark:text-slate-300/85"
                            >
                                <svg
                                    class="h-8 w-8 opacity-70"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="currentColor"
                                    viewBox="0 0 640 512"
                                >
                                    <path d="M622.34 153.2L343.4 67.5c-15.2-4.67-31.6-4.67-46.79 0L17.66 153.2c-23.54 7.23-23.54 38.36 0 45.59l278.95 85.7c20.35 6.25 37.73 2.78 46.79 0l278.95-85.7c23.55-7.24 23.55-38.36 0-45.6zM352.79 315.09c-28.53 8.76-52.84 3.92-65.59 0l-145.02-44.55L128 384c0 35.35 85.96 64 192 64s192-28.65 192-64l-14.18-113.47-145.03 44.56z" />
                                </svg>

                                <h2 class="text-xl font-semibold">
                                    {{ t('subheadings') }}
                                </h2>
                            </div>

                            <div class="mt-6">
                                <RubricGrid
                                    v-if="viewMode === 'grid'"
                                    :rubrics="childRubrics"
                                    :cols="rubricGridCols"
                                />

                                <RubricRows
                                    v-else
                                    :rubrics="childRubrics"
                                />
                            </div>
                        </div>

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
