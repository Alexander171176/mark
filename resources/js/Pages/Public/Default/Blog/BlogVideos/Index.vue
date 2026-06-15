<script setup>
/**
 * Страница списка видео.
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
import VideoGrid from '@/Components/Public/Default/Blog/BlogVideo/VideoGrid.vue'
import VideoRows from '@/Components/Public/Default/Blog/BlogVideo/VideoRows.vue'
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
    publicBlogVideosProcessingMode: { type: String, default: 'server' },

    title: { type: String, default: '' },
    canLogin: { type: Boolean, default: false },
    canRegister: { type: Boolean, default: false },

    rubricTree: { type: Array, default: () => [] },

    videos: { type: [Array, Object], default: () => [] },
    videosCount: { type: Number, default: 0 },
    videosFound: { type: Number, default: 0 },

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

/** Универсальный список видео: server paginator data или frontend array */
const videosData = computed(() => {
    if (Array.isArray(props.videos)) {
        return props.videos
    }

    if (Array.isArray(props.videos?.data)) {
        return props.videos.data
    }

    return []
})

/** Количество элементов на странице для обоих режимов */
const perPage = computed(() => {
    const value = Number(props.filters?.per_page ?? 20)

    return Number.isFinite(value) ? value : 20
})

/** Поисковая строка для server/frontend */
const q = ref(String(props.filters?.q ?? ''))

/** Сортировка по умолчанию для server/frontend */
const DEFAULT_SORT = 'sortAsc'

/** Текущая сортировка для server/frontend */
const sort = ref(String(props.filters?.sort ?? DEFAULT_SORT))

/** Ключ локального хранения режима отображения */
const VIEW_KEY = 'public_blog_videos_view'

/** Режим отображения карточки/строки для server/frontend */
const viewMode = ref(
    String(props.filters?.view || localStorage.getItem(VIEW_KEY) || 'grid')
)

/** Сохраняем режим отображения локально */
watch(viewMode, (value) => {
    localStorage.setItem(VIEW_KEY, value)
})

/** Опции сортировки для toolbar */
const videoSortOptions = [
    { value: 'sortAsc', label: `${t('sortNumber')} 0→9` },
    { value: 'sortDesc', label: `${t('sortNumber')} 9→0` },

    { value: 'titleAsc', label: `${t('title')} A→Z` },
    { value: 'titleDesc', label: `${t('title')} Z→A` },

    { value: 'viewsDesc', label: `${t('views')} 9→0` },
    { value: 'viewsAsc', label: `${t('views')} 0→9` },

    { value: 'likesDesc', label: `${t('likes')} 9→0` },
    { value: 'likesAsc', label: `${t('likes')} 0→9` },

    { value: 'commentsDesc', label: `${t('comments')} 9→0` },
    { value: 'commentsAsc', label: `${t('comments')} 0→9` },

    { value: 'durationDesc', label: `${t('duration')} 9→0` },
    { value: 'durationAsc', label: `${t('duration')} 0→9` },

    { value: 'publishedAtDesc', label: `${t('publishedAt')} ↓` },
    { value: 'publishedAtAsc', label: `${t('publishedAt')} ↑` },
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

/** Получение названия видео из разных возможных структур ресурса */
const getVideoTitle = (video) => {
    return video.title
        || video.name
        || video.translation?.title
        || video.current_translation?.title
        || video.translations?.[0]?.title
        || ''
}

/** Получение краткого текста видео */
const getVideoShort = (video) => {
    return video.short
        || video.description
        || video.translation?.short
        || video.translation?.description
        || video.current_translation?.short
        || video.current_translation?.description
        || video.translations?.[0]?.short
        || video.translations?.[0]?.description
        || ''
}

/** Получение продолжительности видео */
const getVideoDuration = (video) => {
    return Number(video.duration ?? video.duration_seconds ?? 0) || 0
}

/** Локальный поиск frontend */
const filteredVideos = computed(() => {
    const query = normalizeText(q.value).trim()

    if (!query) {
        return videosData.value
    }

    return videosData.value.filter((video) => {
        return [
            getVideoTitle(video),
            getVideoShort(video),
            video.url,
            video.slug,
            video.owner?.name,
            video.owner?.email,
        ].some((value) => normalizeText(value).includes(query))
    })
})

/** Локальная сортировка frontend */
const sortedVideos = computed(() => {
    const list = [...filteredVideos.value]

    return list.sort((a, b) => {
        switch (sort.value) {
            case 'sortAsc':
                return (a.sort ?? 0) - (b.sort ?? 0)

            case 'sortDesc':
                return (b.sort ?? 0) - (a.sort ?? 0)

            case 'titleAsc':
                return normalizeText(getVideoTitle(a))
                    .localeCompare(normalizeText(getVideoTitle(b)))

            case 'titleDesc':
                return normalizeText(getVideoTitle(b))
                    .localeCompare(normalizeText(getVideoTitle(a)))

            case 'viewsAsc':
                return (a.views ?? 0) - (b.views ?? 0)

            case 'viewsDesc':
                return (b.views ?? 0) - (a.views ?? 0)

            case 'likesAsc':
                return (a.likes_count ?? 0) - (b.likes_count ?? 0)

            case 'likesDesc':
                return (b.likes_count ?? 0) - (a.likes_count ?? 0)

            case 'commentsAsc':
                return (a.comments_count ?? 0) - (b.comments_count ?? 0)

            case 'commentsDesc':
                return (b.comments_count ?? 0) - (a.comments_count ?? 0)

            case 'durationAsc':
                return getVideoDuration(a) - getVideoDuration(b)

            case 'durationDesc':
                return getVideoDuration(b) - getVideoDuration(a)

            case 'publishedAtAsc':
                return new Date(a.published_at ?? a.created_at ?? 0) -
                    new Date(b.published_at ?? b.created_at ?? 0)

            case 'publishedAtDesc':
                return new Date(b.published_at ?? b.created_at ?? 0) -
                    new Date(a.published_at ?? a.created_at ?? 0)

            default:
                return 0
        }
    })
})

/** Локальная пагинация frontend */
const frontendPaginatedVideos = computed(() => {
    const start = (frontendCurrentPage.value - 1) * perPage.value

    return sortedVideos.value.slice(start, start + perPage.value)
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
    return Number(props.videos?.meta?.current_page ?? props.videos?.current_page ?? 1) || 1
})

/** Последняя страница server-пагинации */
const lastPage = computed(() => {
    return Number(props.videos?.meta?.last_page ?? props.videos?.last_page ?? 1) || 1
})

/** Маршрут списка видео для server-режима */
const indexRoute = () => route('public.blogVideos.index')

/** Server-загрузка видео с query-параметрами */
const reloadVideos = (page = 1) => {
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
    reloadVideos(1)
}

/** Сброс поиска и сортировки для обоих режимов */
const resetSearch = () => {
    q.value = ''
    sort.value = DEFAULT_SORT
    frontendCurrentPage.value = 1

    if (props.useServerProcessing) {
        reloadVideos(1)
    }
}

/** Server-изменение сортировки */
const updateSort = (value) => {
    sort.value = value || DEFAULT_SORT

    if (props.useServerProcessing) {
        reloadVideos(1)
    }
}

/** Изменение режима отображения для обоих режимов */
const updateViewMode = (value) => {
    viewMode.value = value || 'grid'

    if (props.useServerProcessing) {
        reloadVideos(currentPage.value)
    }
}

/** Server-переход на страницу */
const goToPage = (page) => {
    const value = Number(page)

    if (!Number.isFinite(value)) {
        return
    }

    const safePage = Math.max(1, Math.min(value, lastPage.value))

    reloadVideos(safePage)
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
const displayedVideos = computed(() => {
    return props.useServerProcessing
        ? videosData.value
        : frontendPaginatedVideos.value
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
const videoGridCols = computed(() => {
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
        <title>{{ t('videos') }}</title>
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
                                <li class="breadcrumbs">
                                    {{ t('videos') }}
                                </li>
                            </ol>
                        </nav>

                        <!-- Заголовок -->
                        <div class="my-3 flex flex-wrap items-center justify-center gap-2 title">
                            <svg
                                class="h-5 w-5"
                                fill="currentColor"
                                viewBox="0 0 576 512">
                                <path d="M336.2 64H47.8C21.4 64 0 85.4 0 111.8v288.4C0 426.6 21.4 448 47.8 448h288.4c26.4 0 47.8-21.4 47.8-47.8V111.8c0-26.4-21.4-47.8-47.8-47.8zm189.4 37.7L416 177.3v157.4l109.6 75.5c21.2 14.6 50.4-.3 50.4-25.8V127.5c0-25.4-29.1-40.4-50.4-25.8z"/>
                            </svg>

                            <h1 class="text-2xl font-bold">
                                {{ t('videos') }}
                            </h1>
                        </div>

                        <!-- Подзаголовок -->
                        <div class="my-1 text-sm subtitle text-center">
                            Смотрите видео и обзоры от экспертов сообщества
                        </div>

                        <!-- Поиск, переключатель вида, сортировка -->
                        <EntityPageToolbar
                            v-if="useServerProcessing"
                            v-model="q"
                            :found="videosFound"
                            :view-mode="viewMode"
                            :sort-value="sort"
                            :sort-options="videoSortOptions"
                            :default-sort="DEFAULT_SORT"
                            :found-label="t('videos')"
                            :search-placeholder="t('searchByName')"
                            @submit="submitSearch"
                            @reset="resetSearch"
                            @update:viewMode="updateViewMode"
                            @update:sortValue="updateSort"
                        />

                        <FrontendEntityPageToolbar
                            v-else
                            v-model="q"
                            :found="sortedVideos.length"
                            :view-mode="viewMode"
                            :sort-value="sort"
                            :sort-options="videoSortOptions"
                            :default-sort="DEFAULT_SORT"
                            :found-label="t('videos')"
                            :search-placeholder="t('searchByName')"
                            @reset="resetSearch"
                            @update:viewMode="updateViewMode"
                            @update:sortValue="updateSort"
                        />

                        <div ref="scrollTarget"></div>

                        <!-- Empty -->
                        <div
                            v-if="displayedVideos.length === 0"
                            class="mt-6 text-center text-slate-700 dark:text-slate-300"
                        >
                            {{ t('noData') }}
                        </div>

                        <!-- Views -->
                        <div v-else>
                            <VideoGrid
                                v-if="viewMode === 'grid'"
                                :videos="displayedVideos"
                                :cols="videoGridCols"
                            />

                            <VideoRows
                                v-else
                                :videos="displayedVideos"
                            />
                        </div>

                        <!-- Пагинация -->
                        <Pagination
                            v-if="useServerProcessing"
                            :current-page="currentPage"
                            :last-page="lastPage"
                            :found="videosFound"
                            @prev="goPrev"
                            @next="goNext"
                            @go="goToPage"
                        />

                        <FrontendPagination
                            v-else
                            v-model:currentPage="frontendCurrentPage"
                            :items-per-page="perPage"
                            :total-items="sortedVideos.length"
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
                        setting-key="publicBlogVideosProcessingMode"
                        :mode="publicBlogVideosProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="videosCount"
                    />
                </div>
            </div>
        </div>
    </DefaultLayout>
</template>
