<script setup>
/**
 * Страница списка направлений обучения.
 *
 * Логика:
 * - серверный и frontend поиск
 * - серверная и frontend сортировка
 * - серверная и frontend пагинация
 * - переключение вида grid/rows
 * - сохранение вида в localStorage
 * - управление колонками через настройки сайта
 */

import { computed, ref, watch } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useSmoothScrollTo } from '@/composables/useSmoothScrollTo'

import DefaultLayout from '@/Layouts/DefaultLayout.vue'
import Navbar from '@/Partials/Default/Navbar.vue'
import FooterBlog from '@/Partials/Default/FooterBlog.vue'
import Progress from '@/Components/Public/Default/Progress/Progress.vue'
import LeftSidebarSchool from '@/Components/Public/Default/Partials/LeftSidebarSchool.vue'
import RightSidebarSchool from '@/Components/Public/Default/Partials/RightSidebarSchool.vue'
import EntityPageToolbar from '@/Components/Public/Default/PageToolbar/EntityPageToolbar.vue'
import FrontendEntityPageToolbar from '@/Components/Public/Default/PageToolbar/FrontendEntityPageToolbar.vue'
import Pagination from '@/Components/Public/Default/Pagination/Pagination.vue'
import FrontendPagination from '@/Components/Public/Default/Pagination/FrontendPagination.vue'
import SectionVideoList from '@/Components/Public/Default/Blog/BlogVideo/SectionVideoList.vue'
import SectionBanners from '@/Components/Public/Default/Blog/BlogBanner/SectionBanners.vue'
import TrackGrid from '@/Components/Public/Default/School/SchoolTrack/TrackGrid.vue'
import TrackRows from '@/Components/Public/Default/School/SchoolTrack/TrackRows.vue'
import PublicAdminBottomPanel from '@/Components/Admin/UI/PublicAdminPanel/PublicAdminBottomPanel.vue'

const { t } = useI18n()

/* ===================== PROPS ===================== */

/** Props страницы */
const props = defineProps({
    locale: { type: String, default: 'ru' },

    seo: {
        type: Object,
        default: () => ({
            title: '',
            keywords: '',
            description: '',
        }),
    },

    useServerProcessing: { type: Boolean, default: false },
    publicSchoolTracksProcessingMode: { type: String, default: 'server' },

    title: { type: String, default: '' },
    canLogin: { type: Boolean, default: false },
    canRegister: { type: Boolean, default: false },

    trackTree: { type: Array, default: () => [] },

    tracks: { type: [Array, Object], default: () => [] },
    tracksCount: { type: Number, default: 0 },
    tracksFound: { type: Number, default: 0 },

    filters: { type: Object, default: () => ({}) },

    mainVideos: { type: [Array, Object], default: () => [] },
    mainBanners: { type: [Array, Object], default: () => [] },
})

/* ===================== PAGE ===================== */

/** Глобальные данные страницы */
const page = usePage()

/** Глобальные настройки сайта */
const siteSettings = page.props?.siteSettings || {}

/** Роль администратора */
const isAdmin = computed(() => page.props?.isAdmin === true)

/** Дерево треков */
const trackTree = computed(() => {
    return Array.isArray(props.trackTree)
        ? props.trackTree
        : []
})

/** Нормализация списков */
const normalizeList = (value) => {
    if (Array.isArray(value)) {
        return value
    }

    if (Array.isArray(value?.data)) {
        return value.data
    }

    return []
}

/* ===================== TRACKS DATA ===================== */

/** Универсальный список треков */
const tracksData = computed(() => {
    if (Array.isArray(props.tracks)) {
        return props.tracks
    }

    if (Array.isArray(props.tracks?.data)) {
        return props.tracks.data
    }

    return []
})

/* ===================== SIDEBARS ===================== */

/** Показ левой колонки */
const showLeft = computed(() => {
    return !siteSettings?.ViewLeftColumn
        || siteSettings.ViewLeftColumn === 'true'
})

/** Показ правой колонки */
const showRight = computed(() => {
    return !siteSettings?.ViewRightColumn
        || siteSettings.ViewRightColumn === 'true'
})

/** Ключ левого сайдбара */
const LEFT_SIDEBAR_KEY = 'public_left_sidebar_collapsed'

/** Ключ правого сайдбара */
const RIGHT_SIDEBAR_KEY = 'public_right_sidebar_collapsed'

/** Получение boolean из localStorage */
const getStoredBoolean = (key, defaultValue = true) => {
    const value = localStorage.getItem(key)

    if (value === null) {
        return defaultValue
    }

    return value === 'true'
}

/** Левый сайдбар по умолчанию свернут */
const leftCollapsed = ref(
    getStoredBoolean(LEFT_SIDEBAR_KEY, true)
)

/** Правый сайдбар по умолчанию свернут */
const rightCollapsed = ref(
    getStoredBoolean(RIGHT_SIDEBAR_KEY, true)
)

/**
 * Количество колонок сетки.
 *
 * Оба открыты  → 2.
 * Один свернут → 3.
 * Оба свернуты → 4.
 *
 * Количество треков при этом не меняется.
 */
const trackGridCols = computed(() => {
    const leftExpanded = showLeft.value && !leftCollapsed.value
    const rightExpanded = showRight.value && !rightCollapsed.value

    if (leftExpanded && rightExpanded) {
        return 2
    }

    if (leftExpanded || rightExpanded) {
        return 3
    }

    return 4
})

/** Сохраняем состояние сайдбаров */
watch([leftCollapsed, rightCollapsed], () => {
    localStorage.setItem(
        LEFT_SIDEBAR_KEY,
        String(leftCollapsed.value)
    )

    localStorage.setItem(
        RIGHT_SIDEBAR_KEY,
        String(rightCollapsed.value)
    )
})

/* ===================== FILTERS ===================== */

/** Поисковая строка */
const q = ref(
    String(props.filters?.q ?? '')
)

/**
 * Сортировка по умолчанию.
 *
 * Совпадает с fallback контроллера:
 * publicSchoolTracksDefaultSort → sortAsc.
 */
const DEFAULT_SORT = 'sortAsc'

/** Текущая сортировка */
const sort = ref(
    String(props.filters?.sort ?? DEFAULT_SORT)
)

/** Ключ режима отображения */
const VIEW_KEY = 'public_school_tracks_view'

/** Режим отображения */
const viewMode = ref(
    String(
        props.filters?.view
        || localStorage.getItem(VIEW_KEY)
        || 'grid'
    )
)

/** Сохраняем режим отображения */
watch(viewMode, (value) => {
    localStorage.setItem(VIEW_KEY, value)
})

/**
 * Количество треков на странице.
 *
 * Источник значения — backend:
 * PublicSettingsService → resolvePerPage() → filters.per_page.
 *
 * 12 используется только как аварийный fallback.
 */
const perPage = computed(() => {
    const value = Number(props.filters?.per_page)

    return Number.isFinite(value) && value > 0
        ? value
        : 12
})

/** Опции сортировки */
const trackSortOptions = [
    { value: 'sortAsc', label: `${t('sortNumber')} 0→9` },
    { value: 'sortDesc', label: `${t('sortNumber')} 9→0` },

    { value: 'idDesc', label: t('idDesc') },
    { value: 'idAsc', label: t('idAsc') },

    { value: 'nameAsc', label: `${t('title')} A→Z` },
    { value: 'nameDesc', label: `${t('title')} Z→A` },

    { value: 'slugAsc', label: 'URL A→Z' },
    { value: 'slugDesc', label: 'URL Z→A' },

    { value: 'viewsDesc', label: `${t('views')} 9→0` },
    { value: 'viewsAsc', label: `${t('views')} 0→9` },

    { value: 'likesDesc', label: `${t('likes')} 9→0` },
    { value: 'likesAsc', label: `${t('likes')} 0→9` },

    { value: 'childrenDesc', label: `${t('subheadings')} 9→0` },
    { value: 'childrenAsc', label: `${t('subheadings')} 0→9` },

    { value: 'coursesDesc', label: `${t('courses')} 9→0` },
    { value: 'coursesAsc', label: `${t('courses')} 0→9` },

    { value: 'imagesDesc', label: `${t('images')} 9→0` },
    { value: 'imagesAsc', label: `${t('images')} 0→9` },

    { value: 'dateDesc', label: t('sortNewestFirst') },
    { value: 'dateAsc', label: t('sortOldestFirst') },
]

/* ===================== FRONTEND MODE ===================== */

/** Текущая frontend-страница */
const frontendCurrentPage = ref(1)

/** Плавный скролл к списку */
const {
    targetRef: scrollTarget,
    scrollToTarget,
} = useSmoothScrollTo({
    offset: 80,
    duration: 1200,
})

/** Нормализация текста */
const normalizeText = (value) => {
    return String(value ?? '').toLowerCase()
}

/** Название трека */
const getTrackTitle = (track) => {
    return track?.title
        || track?.name
        || track?.translation?.title
        || track?.translation?.name
        || track?.current_translation?.title
        || track?.current_translation?.name
        || track?.translations?.[0]?.title
        || track?.translations?.[0]?.name
        || ''
}

/** Краткий текст трека */
const getTrackShort = (track) => {
    return track?.short
        || track?.description
        || track?.translation?.short
        || track?.translation?.description
        || track?.current_translation?.short
        || track?.current_translation?.description
        || track?.translations?.[0]?.short
        || track?.translations?.[0]?.description
        || ''
}

/** Slug трека */
const getTrackSlug = (track) => {
    return track?.slug
        || track?.url
        || track?.translation?.slug
        || track?.current_translation?.slug
        || track?.translations?.[0]?.slug
        || ''
}

/** Локальный поиск */
const filteredTracks = computed(() => {
    const query = normalizeText(q.value).trim()

    if (!query) {
        return tracksData.value
    }

    return tracksData.value.filter((track) => {
        return [
            getTrackTitle(track),
            getTrackShort(track),
            getTrackSlug(track),
            track.owner?.name,
            track.owner?.email,
        ].some((value) => {
            return normalizeText(value).includes(query)
        })
    })
})

/** Локальная сортировка */
const sortedTracks = computed(() => {
    const list = [...filteredTracks.value]

    return list.sort((a, b) => {
        switch (sort.value) {
            case 'sortAsc':
                return (a.sort ?? 0)
                    - (b.sort ?? 0)

            case 'sortDesc':
                return (b.sort ?? 0)
                    - (a.sort ?? 0)

            case 'idAsc':
                return (a.id ?? 0)
                    - (b.id ?? 0)

            case 'idDesc':
                return (b.id ?? 0)
                    - (a.id ?? 0)

            case 'nameAsc':
            case 'titleAsc':
                return normalizeText(getTrackTitle(a))
                    .localeCompare(
                        normalizeText(getTrackTitle(b))
                    )

            case 'nameDesc':
            case 'titleDesc':
                return normalizeText(getTrackTitle(b))
                    .localeCompare(
                        normalizeText(getTrackTitle(a))
                    )

            case 'slugAsc':
                return normalizeText(getTrackSlug(a))
                    .localeCompare(
                        normalizeText(getTrackSlug(b))
                    )

            case 'slugDesc':
                return normalizeText(getTrackSlug(b))
                    .localeCompare(
                        normalizeText(getTrackSlug(a))
                    )

            case 'viewsAsc':
                return (a.views ?? 0)
                    - (b.views ?? 0)

            case 'viewsDesc':
                return (b.views ?? 0)
                    - (a.views ?? 0)

            case 'likesAsc':
                return (a.likes_count ?? 0)
                    - (b.likes_count ?? 0)

            case 'likesDesc':
                return (b.likes_count ?? 0)
                    - (a.likes_count ?? 0)

            case 'childrenAsc':
                return (a.children_count ?? 0)
                    - (b.children_count ?? 0)

            case 'childrenDesc':
                return (b.children_count ?? 0)
                    - (a.children_count ?? 0)

            case 'coursesAsc':
                return (a.courses_count ?? 0)
                    - (b.courses_count ?? 0)

            case 'coursesDesc':
                return (b.courses_count ?? 0)
                    - (a.courses_count ?? 0)

            case 'imagesAsc':
                return (a.images_count ?? 0)
                    - (b.images_count ?? 0)

            case 'imagesDesc':
                return (b.images_count ?? 0)
                    - (a.images_count ?? 0)

            case 'dateAsc':
                return new Date(
                    a.published_at
                    ?? a.created_at
                    ?? 0
                ) - new Date(
                    b.published_at
                    ?? b.created_at
                    ?? 0
                )

            case 'dateDesc':
                return new Date(
                    b.published_at
                    ?? b.created_at
                    ?? 0
                ) - new Date(
                    a.published_at
                    ?? a.created_at
                    ?? 0
                )

            default:
                return 0
        }
    })
})

/**
 * Frontend-пагинация.
 *
 * Использует то же per_page,
 * которое определил backend.
 */
const frontendPaginatedTracks = computed(() => {
    const start = (
        frontendCurrentPage.value - 1
    ) * perPage.value

    return sortedTracks.value.slice(
        start,
        start + perPage.value
    )
})

/** Сбрасываем frontend-пагинацию */
watch([q, sort, viewMode], () => {
    frontendCurrentPage.value = 1
})

/** Скролл при frontend-пагинации */
watch(frontendCurrentPage, () => {
    if (!props.useServerProcessing) {
        scrollToTarget()
    }
})

/* ===================== SERVER MODE ===================== */

/** Текущая server-страница */
const currentPage = computed(() => {
    return Number(
        props.tracks?.meta?.current_page
        ?? props.tracks?.current_page
        ?? 1
    ) || 1
})

/** Последняя server-страница */
const lastPage = computed(() => {
    return Number(
        props.tracks?.meta?.last_page
        ?? props.tracks?.last_page
        ?? 1
    ) || 1
})

/** Маршрут списка треков */
const indexRoute = () => {
    return route('public.schoolTracks.index')
}

/**
 * Server-загрузка треков.
 *
 * per_page намеренно не отправляем.
 * Его всегда определяет backend через PublicSettingsService.
 */
const reloadTracks = (page = 1) => {
    router.get(
        indexRoute(),
        {
            q: q.value || undefined,
            sort: sort.value || undefined,
            view: viewMode.value || undefined,
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
    reloadTracks(1)
}

/** Сброс поиска и сортировки */
const resetSearch = () => {
    q.value = ''
    sort.value = DEFAULT_SORT
    frontendCurrentPage.value = 1

    if (props.useServerProcessing) {
        reloadTracks(1)
    }
}

/** Server-изменение сортировки */
const updateSort = (value) => {
    sort.value = value || DEFAULT_SORT

    if (props.useServerProcessing) {
        reloadTracks(1)
    }
}

/** Изменение режима отображения */
const updateViewMode = (value) => {
    viewMode.value = value || 'grid'
    frontendCurrentPage.value = 1

    if (props.useServerProcessing) {
        reloadTracks(1)
    }
}

/** Server-переход на страницу */
const goToPage = (page) => {
    const value = Number(page)

    if (!Number.isFinite(value)) {
        return
    }

    const safePage = Math.max(
        1,
        Math.min(value, lastPage.value)
    )

    reloadTracks(safePage)
}

/** Предыдущая server-страница */
const goPrev = () => {
    if (currentPage.value <= 1) {
        return
    }

    goToPage(currentPage.value - 1)
}

/** Следующая server-страница */
const goNext = () => {
    if (currentPage.value >= lastPage.value) {
        return
    }

    goToPage(currentPage.value + 1)
}

/* ===================== COMMON VIEW ===================== */

/** Итоговый список треков */
const displayedTracks = computed(() => {
    return props.useServerProcessing
        ? tracksData.value
        : frontendPaginatedTracks.value
})

/** Видео внизу страницы */
const mainVideosList = computed(() => {
    return normalizeList(props.mainVideos)
})

/** Баннеры внизу страницы */
const mainBannersList = computed(() => {
    return normalizeList(props.mainBanners)
})
</script>

<template>
    <!-- SEO -->
    <Head>
        <title>{{ seo?.title || t('learningCategories') }}</title>

        <meta name="title" :content="seo?.title || t('learningCategories')" />
        <meta name="keywords" :content="seo?.keywords || ''" />
        <meta name="description" :content="seo?.description || t('learningCategories')" />

        <meta property="og:title" :content="seo?.title || t('learningCategories')" />
        <meta property="og:description" :content="seo?.description || t('learningCategories')" />
        <meta property="og:type" content="website" />
        <meta property="og:url" :content="`/${locale}/school/tracks`" />
        <meta property="og:image" content="" />
        <meta property="og:locale" :content="locale === 'ru' ? 'ru_RU' : locale" />

        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="seo?.title || t('learningCategories')" />
        <meta name="twitter:description" :content="seo?.description || t('learningCategories')" />
        <meta name="twitter:image" content="" />

        <meta name="DC.title" :content="seo?.title || t('learningCategories')" />
        <meta name="DC.description" :content="seo?.description || t('learningCategories')" />
        <meta name="DC.identifier" :content="`/${locale}/school/tracks`" />
        <meta name="DC.language" :content="locale" />
    </Head>

    <DefaultLayout :title="title" :can-login="canLogin" :can-register="canRegister">
        <!-- Шапка -->
        <Navbar />

        <div class="min-h-screen px-3 max-w-full">
            <main class="mx-auto flex flex-col lg:flex-row gap-4 tracking-wider">
                <!-- LEFT -->
                <aside
                    v-if="showLeft"
                    class="shrink-0 mt-12 lg:mt-28 transition-all duration-300"
                    :class="leftCollapsed ? 'lg:w-10' : 'lg:w-64'"
                >
                    <LeftSidebarSchool
                        :track-tree="trackTree"
                        :collapsed="leftCollapsed"
                        @collapsed="leftCollapsed = $event"
                    />
                </aside>

                <!-- CENTER -->
                <div class="w-full lg:mt-28 pb-6 slate-1">
                    <div class="mx-auto max-w-6xl">

                        <!-- Хлебные крошки -->
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
                                <li><span class="mx-2 breadcrumbs">/</span></li>
                                <li class="breadcrumbs">
                                    {{ t('tracks') }}
                                </li>
                            </ol>
                        </nav>

                        <!-- Заголовок -->
                        <div class="my-3 flex flex-wrap
                                    items-center justify-center gap-3 title">
                            <svg class="shrink-0 h-5 w-5 text-slate-600/85 dark:text-slate-200/85"
                                 fill="currentColor"
                                 viewBox="0 0 24 24">
                                <path d="M23.58.424A1,1,0,0,0,22.819.13C8.791.862,3.609,13.358,3.559,13.484a1,1,0,0,0,.22,1.08l5.657,5.657a1,1,0,0,0,1.085.218c.125-.051,12.554-5.291,13.348-19.253A1,1,0,0,0,23.58.424Zm-8.166,10.99a2,2,0,1,1,0-2.828A2,2,0,0,1,15.414,11.414Z"></path>
                                <path d="M1.113,18.844a2.844,2.844,0,1,1,4.022,4.022C4.024,23.977,0,24,0,24S0,19.954,1.113,18.844Z"></path>
                                <path d="M10.357,2.341A8.911,8.911,0,0,0,2.522,4.825a9.084,9.084,0,0,0-1.384,1.8,1,1,0,0,0,.155,1.215l1.989,1.99A26.623,26.623,0,0,1,10.357,2.341Z"></path>
                                <path d="M21.659,13.643a8.911,8.911,0,0,1-2.484,7.835,9.084,9.084,0,0,1-1.8,1.384,1,1,0,0,1-1.215-.155l-1.99-1.989A26.623,26.623,0,0,0,21.659,13.643Z"></path>
                            </svg>
                            <h1 class="text-2xl font-bold">
                                {{ t('tracks') }}
                            </h1>
                        </div>

                        <!-- Подзаголовок -->
                        <div class="my-1 text-sm subtitle text-center">
                            Изучите краткое содержание каждого трека и выберите путь,
                            который соответствует вашим целям и интересам в IT-сфере
                        </div>

                        <!-- Поиск, количество, сортировка, вид -->
                        <EntityPageToolbar
                            v-if="useServerProcessing"
                            v-model="q"
                            :found="tracksFound"
                            :view-mode="viewMode"
                            :sort-value="sort"
                            :sort-options="trackSortOptions"
                            :default-sort="DEFAULT_SORT"
                            :found-label="t('tracks')"
                            :search-placeholder="t('searchByName')"
                            @submit="submitSearch"
                            @reset="resetSearch"
                            @update:viewMode="updateViewMode"
                            @update:sortValue="updateSort"
                        />

                        <FrontendEntityPageToolbar
                            v-else
                            v-model="q"
                            :found="sortedTracks.length"
                            :view-mode="viewMode"
                            :sort-value="sort"
                            :sort-options="trackSortOptions"
                            :default-sort="DEFAULT_SORT"
                            :found-label="t('tracks')"
                            :search-placeholder="t('searchByName')"
                            @reset="resetSearch"
                            @update:viewMode="updateViewMode"
                            @update:sortValue="updateSort"
                        />

                        <div ref="scrollTarget"></div>

                        <!-- Нет данных -->
                        <div
                            v-if="displayedTracks.length === 0"
                            class="mt-6 text-center text-slate-700 dark:text-slate-300"
                        >
                            {{ t('noData') }}
                        </div>

                        <!-- Показ grid/rows -->
                        <div v-else>
                            <TrackGrid
                                v-if="viewMode === 'grid'"
                                :tracks="displayedTracks"
                                :cols="trackGridCols"
                            />

                            <TrackRows
                                v-else
                                :tracks="displayedTracks"
                            />
                        </div>

                        <!-- Пагинация -->
                        <Pagination
                            v-if="useServerProcessing"
                            :current-page="currentPage"
                            :last-page="lastPage"
                            :found="tracksFound"
                            @prev="goPrev"
                            @next="goNext"
                            @go="goToPage"
                        />

                        <FrontendPagination
                            v-else
                            v-model:currentPage="frontendCurrentPage"
                            :items-per-page="perPage"
                            :total-items="sortedTracks.length"
                        />

                        <!-- Главные видео, баннеры -->
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
                    <RightSidebarSchool
                        :collapsed="rightCollapsed"
                        @collapsed="rightCollapsed = $event"
                    />
                </aside>
            </main>
        </div>

        <!-- Подвал и кнопка с прогрессом -->
        <FooterBlog />
        <Progress />

        <!-- Нижняя панель администратора -->
        <PublicAdminBottomPanel
            v-if="isAdmin"
            setting-key="publicSchoolTracksProcessingMode"
            :mode="publicSchoolTracksProcessingMode"
            :use-server-processing="useServerProcessing"
            :total="tracksCount"
        />
    </DefaultLayout>
</template>
