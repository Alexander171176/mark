<script setup>
/**
 * Страница списка модулей
 * - шапка, центральная часть, подвал
 * - светлый, тёмный режим
 * - серверный и frontend поиск
 * - серверная и frontend пагинация
 * - серверная и frontend сортировка
 * - показ карточками, в строку
 * - показ главных видео, баннеров внизу страницы
 * - показ, скрытие колонок
 * - показ дерева треков в левой колонке
 * - показ облака хештегов в правой колонке
 *
 * @version PulsarCMS 1.0
 * @author Александр
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
import CourseModuleGrid from '@/Components/Public/Default/School/SchoolModule/CourseModuleGrid.vue'
import CourseModuleRows from '@/Components/Public/Default/School/SchoolModule/CourseModuleRows.vue'
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
    publicSchoolModulesProcessingMode: { type: String, default: 'server' },

    title: { type: String, default: '' },
    canLogin: { type: Boolean, default: false },
    canRegister: { type: Boolean, default: false },

    trackTree: { type: Array, default: () => [] },

    modules: { type: [Array, Object], default: () => [] },
    modulesCount: { type: Number, default: 0 },
    modulesFound: { type: Number, default: 0 },

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

/** Дерево треков */
const trackTree = computed(() => {
    return Array.isArray(props.trackTree)
        ? props.trackTree
        : []
})

/* ===================== MODULES DATA ===================== */

/** Универсальный список модулей */
const modulesData = computed(() => {
    if (Array.isArray(props.modules)) {
        return props.modules
    }

    if (Array.isArray(props.modules?.data)) {
        return props.modules.data
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
 * Количество модулей при этом не меняется.
 */
const gridCols = computed(() => {
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
 * publicSchoolModulesDefaultSort → idDesc.
 */
const DEFAULT_SORT = 'idDesc'

/** Текущая сортировка */
const sort = ref(
    String(props.filters?.sort ?? DEFAULT_SORT)
)

/** Ключ режима отображения */
const VIEW_KEY = 'public_school_modules_view'

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
 * Количество модулей на странице.
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
const moduleSortOptions = [
    { value: 'idDesc', label: t('idDesc') },
    { value: 'idAsc', label: t('idAsc') },

    { value: 'sortAsc', label: `${t('sortNumber')} 0→9` },
    { value: 'sortDesc', label: `${t('sortNumber')} 9→0` },

    { value: 'titleAsc', label: `${t('title')} A→Z` },
    { value: 'titleDesc', label: `${t('title')} Z→A` },

    { value: 'courseDesc', label: `${t('course')} ID 9→0` },
    { value: 'courseAsc', label: `${t('course')} ID 0→9` },

    { value: 'statusAsc', label: `${t('status')} A→Z` },
    { value: 'statusDesc', label: `${t('status')} Z→A` },

    { value: 'availabilityAsc', label: `${t('availability')} A→Z` },
    { value: 'availabilityDesc', label: `${t('availability')} Z→A` },

    { value: 'viewsDesc', label: `${t('views')} 9→0` },
    { value: 'viewsAsc', label: `${t('views')} 0→9` },

    { value: 'likesDesc', label: `${t('likes')} 9→0` },
    { value: 'likesAsc', label: `${t('likes')} 0→9` },

    { value: 'likesCountDesc', label: `${t('likes')} 9→0` },
    { value: 'likesCountAsc', label: `${t('likes')} 0→9` },

    { value: 'popularityDesc', label: `${t('popularity')} 9→0` },
    { value: 'popularityAsc', label: `${t('popularity')} 0→9` },

    { value: 'ratingAvgDesc', label: `${t('ratingAvg')} 9→0` },
    { value: 'ratingAvgAsc', label: `${t('ratingAvg')} 0→9` },

    { value: 'ratingCountDesc', label: `${t('ratingCount')} 9→0` },
    { value: 'ratingCountAsc', label: `${t('ratingCount')} 0→9` },

    { value: 'difficultyDesc', label: `${t('sortDifficulty')} 9→0` },
    { value: 'difficultyAsc', label: `${t('sortDifficulty')} 0→9` },

    { value: 'durationDesc', label: `${t('duration')} 9→0` },
    { value: 'durationAsc', label: `${t('duration')} 0→9` },

    { value: 'lessonsDesc', label: `${t('lessonsCount')} 9→0` },
    { value: 'lessonsAsc', label: `${t('lessonsCount')} 0→9` },

    { value: 'imagesDesc', label: `${t('images')} 9→0` },
    { value: 'imagesAsc', label: `${t('images')} 0→9` },

    { value: 'publishedAtDesc', label: `${t('publishedAt')} ↓` },
    { value: 'publishedAtAsc', label: `${t('publishedAt')} ↑` },

    { value: 'createdAtDesc', label: `${t('createdAt')} ↓` },
    { value: 'createdAtAsc', label: `${t('createdAt')} ↑` },

    { value: 'updatedAtDesc', label: `${t('updatedAt')} ↓` },
    { value: 'updatedAtAsc', label: `${t('updatedAt')} ↑` },
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

/** Название модуля */
const getModuleTitle = (module) => {
    return module?.title
        || module?.name
        || module?.translation?.title
        || module?.translation?.name
        || module?.current_translation?.title
        || module?.current_translation?.name
        || module?.translations?.[0]?.title
        || module?.translations?.[0]?.name
        || ''
}

/** Краткий текст модуля */
const getModuleShort = (module) => {
    return module?.short
        || module?.description
        || module?.subtitle
        || module?.translation?.short
        || module?.translation?.description
        || module?.translation?.subtitle
        || module?.current_translation?.short
        || module?.current_translation?.description
        || module?.current_translation?.subtitle
        || module?.translations?.[0]?.short
        || module?.translations?.[0]?.description
        || module?.translations?.[0]?.subtitle
        || ''
}

/** Slug модуля */
const getModuleSlug = (module) => {
    return module?.slug
        || module?.url
        || module?.translation?.slug
        || module?.current_translation?.slug
        || module?.translations?.[0]?.slug
        || ''
}

/** Название курса */
const getCourseTitle = (module) => {
    return module?.course?.title
        || module?.course?.name
        || module?.course?.translation?.title
        || module?.course?.translation?.name
        || module?.course?.translations?.[0]?.title
        || module?.course?.translations?.[0]?.name
        || ''
}

/** Локальный поиск */
const filteredModules = computed(() => {
    const query = normalizeText(q.value).trim()

    if (!query) {
        return modulesData.value
    }

    return modulesData.value.filter((module) => {
        return [
            getModuleTitle(module),
            getModuleShort(module),
            getModuleSlug(module),
            getCourseTitle(module),
            module.course?.instructorProfile?.title,
            module.course?.instructor_profile?.title,
            module.course?.instructorProfile?.user?.name,
            module.course?.instructor_profile?.user?.name,
        ].some((value) => {
            return normalizeText(value).includes(query)
        })
    })
})

/** Локальная сортировка */
const sortedModules = computed(() => {
    const list = [...filteredModules.value]

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

            case 'titleAsc':
                return normalizeText(getModuleTitle(a))
                    .localeCompare(
                        normalizeText(getModuleTitle(b))
                    )

            case 'titleDesc':
                return normalizeText(getModuleTitle(b))
                    .localeCompare(
                        normalizeText(getModuleTitle(a))
                    )

            case 'courseAsc':
                return (
                    a.course_id
                    ?? a.course?.id
                    ?? 0
                ) - (
                    b.course_id
                    ?? b.course?.id
                    ?? 0
                )

            case 'courseDesc':
                return (
                    b.course_id
                    ?? b.course?.id
                    ?? 0
                ) - (
                    a.course_id
                    ?? a.course?.id
                    ?? 0
                )

            case 'statusAsc':
                return normalizeText(a.status)
                    .localeCompare(
                        normalizeText(b.status)
                    )

            case 'statusDesc':
                return normalizeText(b.status)
                    .localeCompare(
                        normalizeText(a.status)
                    )

            case 'availabilityAsc':
                return normalizeText(a.availability)
                    .localeCompare(
                        normalizeText(b.availability)
                    )

            case 'availabilityDesc':
                return normalizeText(b.availability)
                    .localeCompare(
                        normalizeText(a.availability)
                    )

            case 'viewsAsc':
                return (a.views ?? 0)
                    - (b.views ?? 0)

            case 'viewsDesc':
                return (b.views ?? 0)
                    - (a.views ?? 0)

            case 'likesAsc':
            case 'likesCountAsc':
                return (a.likes_count ?? 0)
                    - (b.likes_count ?? 0)

            case 'likesDesc':
            case 'likesCountDesc':
                return (b.likes_count ?? 0)
                    - (a.likes_count ?? 0)

            case 'popularityAsc':
                return (a.popularity ?? 0)
                    - (b.popularity ?? 0)

            case 'popularityDesc':
                return (b.popularity ?? 0)
                    - (a.popularity ?? 0)

            case 'ratingAvgAsc':
                return (a.rating_avg ?? 0)
                    - (b.rating_avg ?? 0)

            case 'ratingAvgDesc':
                return (b.rating_avg ?? 0)
                    - (a.rating_avg ?? 0)

            case 'ratingCountAsc':
                return (a.rating_count ?? 0)
                    - (b.rating_count ?? 0)

            case 'ratingCountDesc':
                return (b.rating_count ?? 0)
                    - (a.rating_count ?? 0)

            case 'difficultyAsc':
                return (a.difficulty ?? 0)
                    - (b.difficulty ?? 0)

            case 'difficultyDesc':
                return (b.difficulty ?? 0)
                    - (a.difficulty ?? 0)

            case 'durationAsc':
                return (a.duration ?? 0)
                    - (b.duration ?? 0)

            case 'durationDesc':
                return (b.duration ?? 0)
                    - (a.duration ?? 0)

            case 'lessonsAsc':
                return (a.lessons_count ?? 0)
                    - (b.lessons_count ?? 0)

            case 'lessonsDesc':
                return (b.lessons_count ?? 0)
                    - (a.lessons_count ?? 0)

            case 'imagesAsc':
                return (a.images_count ?? 0)
                    - (b.images_count ?? 0)

            case 'imagesDesc':
                return (b.images_count ?? 0)
                    - (a.images_count ?? 0)

            case 'publishedAtAsc':
                return new Date(a.published_at ?? 0)
                    - new Date(b.published_at ?? 0)

            case 'publishedAtDesc':
                return new Date(b.published_at ?? 0)
                    - new Date(a.published_at ?? 0)

            case 'createdAtAsc':
                return new Date(a.created_at ?? 0)
                    - new Date(b.created_at ?? 0)

            case 'createdAtDesc':
                return new Date(b.created_at ?? 0)
                    - new Date(a.created_at ?? 0)

            case 'updatedAtAsc':
                return new Date(a.updated_at ?? 0)
                    - new Date(b.updated_at ?? 0)

            case 'updatedAtDesc':
                return new Date(b.updated_at ?? 0)
                    - new Date(a.updated_at ?? 0)

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
const frontendPaginatedModules = computed(() => {
    const start = (
        frontendCurrentPage.value - 1
    ) * perPage.value

    return sortedModules.value.slice(
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
        props.modules?.meta?.current_page
        ?? props.modules?.current_page
        ?? 1
    ) || 1
})

/** Последняя server-страница */
const lastPage = computed(() => {
    return Number(
        props.modules?.meta?.last_page
        ?? props.modules?.last_page
        ?? 1
    ) || 1
})

/** Маршрут списка модулей */
const indexRoute = () => {
    return route('public.schoolModules.index')
}

/**
 * Server-загрузка модулей.
 *
 * per_page намеренно не отправляем.
 * Его всегда определяет backend через PublicSettingsService.
 */
const reloadModules = (page = 1) => {
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
    reloadModules(1)
}

/** Сброс поиска и сортировки */
const resetSearch = () => {
    q.value = ''
    sort.value = DEFAULT_SORT
    frontendCurrentPage.value = 1

    if (props.useServerProcessing) {
        reloadModules(1)
    }
}

/** Server-изменение сортировки */
const updateSort = (value) => {
    sort.value = value || DEFAULT_SORT

    if (props.useServerProcessing) {
        reloadModules(1)
    }
}

/** Изменение режима отображения */
const updateViewMode = (value) => {
    viewMode.value = value || 'grid'
    frontendCurrentPage.value = 1

    if (props.useServerProcessing) {
        reloadModules(1)
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

    reloadModules(safePage)
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

/** Итоговый список модулей */
const displayedModules = computed(() => {
    return props.useServerProcessing
        ? modulesData.value
        : frontendPaginatedModules.value
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
    <Head>
        <title>{{ seo?.title || t('modules') }}</title>

        <meta name="title" :content="seo?.title || t('modules')" />
        <meta name="keywords" :content="seo?.keywords || ''" />
        <meta name="description" :content="seo?.description || t('modules')" />

        <meta property="og:title" :content="seo?.title || t('modules')" />
        <meta property="og:description" :content="seo?.description || t('modules')" />
        <meta property="og:type" content="website" />
        <meta property="og:url" :content="`/${locale}/school/modules`" />
        <meta property="og:image" content="" />
        <meta property="og:locale" :content="locale === 'ru' ? 'ru_RU' : locale" />

        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="seo?.title || t('modules')" />
        <meta name="twitter:description" :content="seo?.description || t('modules')" />
        <meta name="twitter:image" content="" />

        <meta name="DC.title" :content="seo?.title || t('modules')" />
        <meta name="DC.description" :content="seo?.description || t('modules')" />
        <meta name="DC.identifier" :content="`/${locale}/school/modules`" />
        <meta name="DC.language" :content="locale" />
    </Head>

    <DefaultLayout :title="title" :can-login="canLogin" :can-register="canRegister">
        <Navbar />

        <div class="min-h-screen px-3 max-w-full">
            <main class="mx-auto flex flex-col lg:flex-row gap-4 tracking-wider">
                <!-- Левая колонка -->
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

                <!-- Центральный контент -->
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
                                    {{ t('modules') }}
                                </li>
                            </ol>
                        </nav>

                        <!-- Заголовок -->
                        <div class="my-3 flex flex-wrap items-center justify-center gap-3 title">
                            <svg class="shrink-0 h-5 w-5 text-slate-600/85 dark:text-slate-200/85"
                                 fill="currentColor"
                                 viewBox="0 0 24 24">
                                <rect x="1" y="1" width="10" height="10" rx="2"></rect>
                                <path class="fill-current text-slate-400"
                                      d="M23.428,4.618,19.381.572h0a1.957,1.957,0,0,0-2.762,0L12.572,4.618a1.959,1.959,0,0,0,0,2.764l4.047,4.047a1.957,1.957,0,0,0,2.762,0l4.047-4.046A1.959,1.959,0,0,0,23.428,4.618Z"></path>
                                <rect x="13" y="13" width="10" height="10" rx="2"></rect>
                                <rect x="1" y="13" width="10" height="10" rx="2"></rect>
                            </svg>
                            <h1 class="text-2xl font-bold">
                                {{ t('modules') }}
                            </h1>
                        </div>

                        <!-- Подзаголовок -->
                        <div class="my-1 text-sm subtitle text-center">
                            Изучайте модули и выбирайте подходящий путь обучения.
                        </div>

                        <!-- Поиск, количество, сортировка, вид -->
                        <EntityPageToolbar
                            v-if="useServerProcessing"
                            v-model="q"
                            :found="modulesFound"
                            :view-mode="viewMode"
                            :sort-value="sort"
                            :sort-options="moduleSortOptions"
                            :default-sort="DEFAULT_SORT"
                            :found-label="t('modules')"
                            :search-placeholder="t('searchByName')"
                            @submit="submitSearch"
                            @reset="resetSearch"
                            @update:viewMode="updateViewMode"
                            @update:sortValue="updateSort"
                        />

                        <FrontendEntityPageToolbar
                            v-else
                            v-model="q"
                            :found="sortedModules.length"
                            :view-mode="viewMode"
                            :sort-value="sort"
                            :sort-options="moduleSortOptions"
                            :default-sort="DEFAULT_SORT"
                            :found-label="t('modules')"
                            :search-placeholder="t('searchByName')"
                            @reset="resetSearch"
                            @update:viewMode="updateViewMode"
                            @update:sortValue="updateSort"
                        />

                        <div ref="scrollTarget"></div>

                        <!-- Нет данных -->
                        <div
                            v-if="displayedModules.length === 0"
                            class="mt-6 text-center text-slate-700 dark:text-slate-300"
                        >
                            {{ t('noData') }}
                        </div>

                        <!-- Показ grid/rows -->
                        <div v-else>
                            <CourseModuleGrid
                                v-if="viewMode === 'grid'"
                                :modules="displayedModules"
                                :cols="gridCols"
                            />

                            <CourseModuleRows
                                v-else
                                :modules="displayedModules"
                            />
                        </div>

                        <!-- Пагинация -->
                        <Pagination
                            v-if="useServerProcessing"
                            :current-page="currentPage"
                            :last-page="lastPage"
                            :found="modulesFound"
                            @prev="goPrev"
                            @next="goNext"
                            @go="goToPage"
                        />

                        <FrontendPagination
                            v-else
                            v-model:currentPage="frontendCurrentPage"
                            :items-per-page="perPage"
                            :total-items="sortedModules.length"
                        />

                        <!-- Главные видео, баннеры -->
                        <SectionVideoList :videos="mainVideosList" />
                        <SectionBanners :banners="mainBannersList" />
                    </div>
                </div>

                <!-- Правая колонка -->
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

        <FooterBlog />
        <Progress />

        <!-- Нижняя панель администратора -->
        <PublicAdminBottomPanel
            v-if="isAdmin"
            setting-key="publicSchoolModulesProcessingMode"
            :mode="publicSchoolModulesProcessingMode"
            :use-server-processing="useServerProcessing"
            :total="modulesCount"
        />
    </DefaultLayout>
</template>
