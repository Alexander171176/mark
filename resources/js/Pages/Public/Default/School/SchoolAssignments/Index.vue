<script setup>
/**
 * Страница списка заданий
 * - шапка, центральная часть, подвал
 * - светлый, тёмный режим
 * - серверный и frontend поиск
 * - серверная и frontend пагинация
 * - серверная и frontend сортировка
 * - показ карточками, в строку
 * - показ главных видео, баннеров внизу страницы
 * - показ, скрытие колонок
 * - показ дерева треков в левой колонке
 * - показ данных школы в правой колонке
 */

import { computed, onMounted, ref, watch } from 'vue'
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
import AssignmentGrid from '@/Components/Public/Default/School/SchoolAssignment/AssignmentGrid.vue'
import AssignmentRows from '@/Components/Public/Default/School/SchoolAssignment/AssignmentRows.vue'
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
    publicSchoolAssignmentsProcessingMode: { type: String, default: 'server' },

    title: { type: String, default: '' },
    canLogin: { type: Boolean, default: false },
    canRegister: { type: Boolean, default: false },

    trackTree: { type: Array, default: () => [] },

    assignments: { type: [Array, Object], default: () => [] },
    assignmentsCount: { type: Number, default: 0 },
    assignmentsFound: { type: Number, default: 0 },

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
    return Array.isArray(props.trackTree) ? props.trackTree : []
})

/** Нормализация списка */
const normalizeList = (value) => {
    if (Array.isArray(value)) return value
    if (Array.isArray(value?.data)) return value.data

    return []
}

/** Универсальный список заданий */
const assignmentsData = computed(() => {
    if (Array.isArray(props.assignments)) {
        return props.assignments
    }

    if (Array.isArray(props.assignments?.data)) {
        return props.assignments.data
    }

    return []
})

/* ===================== FILTERS ===================== */

/** Поисковая строка */
const q = ref(String(props.filters?.q ?? ''))

/** Сортировка по умолчанию */
const DEFAULT_SORT = 'idDesc'

/** Текущая сортировка */
const sort = ref(String(props.filters?.sort ?? DEFAULT_SORT))

/** Ключ режима отображения */
const VIEW_KEY = 'public_school_assignments_view'

/** Режим отображения */
const viewMode = ref(
    String(props.filters?.view || localStorage.getItem(VIEW_KEY) || 'grid')
)

/** Сохраняем режим отображения */
watch(viewMode, (value) => {
    localStorage.setItem(VIEW_KEY, value)
})

/* ===================== SIDEBARS ===================== */

/** Показ левой колонки */
const showLeft = computed(() => {
    return !siteSettings?.ViewLeftColumn || siteSettings.ViewLeftColumn === 'true'
})

/** Показ правой колонки */
const showRight = computed(() => {
    return !siteSettings?.ViewRightColumn || siteSettings.ViewRightColumn === 'true'
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

/**
 * Количество дополнительных карточек.
 *
 * Каждый свернутый или отключенный сайдбар
 * добавляет одну карточку.
 */
const additionalCards = computed(() => {
    if (viewMode.value !== 'grid') {
        return 0
    }

    let count = 0

    if (!showLeft.value || leftCollapsed.value) {
        count++
    }

    if (!showRight.value || rightCollapsed.value) {
        count++
    }

    return count
})

/**
 * Определяем базовое количество заданий.
 *
 * Если per_page уже находится в URL,
 * убираем добавочные карточки сайдбаров.
 */
const resolveBasePerPage = () => {
    const filterPerPage = Number(props.filters?.per_page ?? 6)
    const safePerPage = Number.isFinite(filterPerPage) ? filterPerPage : 6
    const params = new URLSearchParams(window.location.search)

    if (!params.has('per_page')) {
        return safePerPage
    }

    if (viewMode.value !== 'grid') {
        return safePerPage
    }

    let collapsedCount = 0

    if (!showLeft.value || leftCollapsed.value) {
        collapsedCount++
    }

    if (!showRight.value || rightCollapsed.value) {
        collapsedCount++
    }

    return Math.max(1, safePerPage - collapsedCount)
}

/** Базовое количество заданий */
const basePerPage = ref(resolveBasePerPage())

/**
 * Итоговое количество заданий.
 *
 * 6 — оба сайдбара открыты.
 * 7 — свернут один.
 * 8 — свернуты оба.
 */
const perPage = computed(() => {
    return basePerPage.value + additionalCards.value
})

/** Опции сортировки */
const assignmentSortOptions = [
    { value: 'idDesc', label: t('idDesc') },
    { value: 'idAsc', label: t('idAsc') },

    { value: 'sortAsc', label: `${t('sortNumber')} 0→9` },
    { value: 'sortDesc', label: `${t('sortNumber')} 9→0` },

    { value: 'titleAsc', label: `${t('title')} A→Z` },
    { value: 'titleDesc', label: `${t('title')} Z→A` },

    { value: 'statusAsc', label: `${t('status')} A→Z` },
    { value: 'statusDesc', label: `${t('status')} Z→A` },

    { value: 'gradingTypeAsc', label: `${t('gradingType')} A→Z` },
    { value: 'gradingTypeDesc', label: `${t('gradingType')} Z→A` },

    { value: 'attemptsLimitAsc', label: `${t('attemptsLimit')} 0→9` },
    { value: 'attemptsLimitDesc', label: `${t('attemptsLimit')} 9→0` },

    { value: 'maxScoreAsc', label: `${t('maxScore')} 0→9` },
    { value: 'maxScoreDesc', label: `${t('maxScore')} 9→0` },

    { value: 'submissionsAsc', label: `${t('submissions')} 0→9` },
    { value: 'submissionsDesc', label: `${t('submissions')} 9→0` },

    { value: 'imagesAsc', label: `${t('images')} 0→9` },
    { value: 'imagesDesc', label: `${t('images')} 9→0` },

    { value: 'dueAtAsc', label: `${t('dueAt')} ↑` },
    { value: 'dueAtDesc', label: `${t('dueAt')} ↓` },

    { value: 'publishedAtAsc', label: `${t('publishedAt')} ↑` },
    { value: 'publishedAtDesc', label: `${t('publishedAt')} ↓` },

    { value: 'dateAsc', label: t('sortOldestFirst') },
    { value: 'dateDesc', label: t('sortNewestFirst') },
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
const normalizeText = (value) => String(value ?? '').toLowerCase()

/** Название задания */
const getAssignmentTitle = (assignment) => {
    return assignment.title
        || assignment.name
        || assignment.translation?.title
        || assignment.translation?.name
        || assignment.current_translation?.title
        || assignment.current_translation?.name
        || assignment.translations?.[0]?.title
        || assignment.translations?.[0]?.name
        || ''
}

/** Краткий текст задания */
const getAssignmentShort = (assignment) => {
    return assignment.short
        || assignment.description
        || assignment.translation?.short
        || assignment.translation?.description
        || assignment.current_translation?.short
        || assignment.current_translation?.description
        || assignment.translations?.[0]?.short
        || assignment.translations?.[0]?.description
        || ''
}

/** Slug задания */
const getAssignmentSlug = (assignment) => {
    return assignment.slug
        || assignment.url
        || assignment.translation?.slug
        || assignment.current_translation?.slug
        || assignment.translations?.[0]?.slug
        || ''
}

/** Название связанной сущности */
const getRelationTitle = (item) => {
    return item?.title
        || item?.name
        || item?.translation?.title
        || item?.translation?.name
        || item?.translations?.[0]?.title
        || item?.translations?.[0]?.name
        || ''
}

/** Разбивка поисковой строки */
const splitSearchWords = (value) => {
    return String(value ?? '')
        .toLowerCase()
        .split(/[\s:#№,"'«»(){}\[\].!?/\\|]+/u)
        .map((word) => word.trim())
        .filter((word) => word.length >= 2)
}

/** Локальный поиск */
const filteredAssignments = computed(() => {
    const words = splitSearchWords(q.value)

    if (words.length === 0) {
        return assignmentsData.value
    }

    return assignmentsData.value.filter((assignment) => {
        const haystack = normalizeText([
            assignment.id,
            assignment.sort,
            assignment.school_course_id,
            assignment.school_module_id,
            assignment.school_lesson_id,
            assignment.school_instructor_profile_id,
            assignment.status,
            assignment.visibility,
            assignment.grading_type,
            assignment.attempts_limit,
            assignment.max_score,

            getAssignmentTitle(assignment),
            getAssignmentShort(assignment),
            getAssignmentSlug(assignment),

            getRelationTitle(assignment.course),
            getRelationTitle(assignment.module),
            getRelationTitle(assignment.lesson),
            getRelationTitle(assignment.instructor),

            assignment.instructor?.user?.name,
            assignment.instructor?.user?.email,
        ].filter(Boolean).join(' '))

        return words.every((word) => haystack.includes(word))
    })
})

/** Локальная сортировка */
const sortedAssignments = computed(() => {
    const list = [...filteredAssignments.value]

    return list.sort((a, b) => {
        switch (sort.value) {
            case 'idAsc':
                return (a.id ?? 0) - (b.id ?? 0)

            case 'idDesc':
                return (b.id ?? 0) - (a.id ?? 0)

            case 'sortAsc':
                return (a.sort ?? 0) - (b.sort ?? 0)

            case 'sortDesc':
                return (b.sort ?? 0) - (a.sort ?? 0)

            case 'titleAsc':
                return normalizeText(getAssignmentTitle(a))
                    .localeCompare(normalizeText(getAssignmentTitle(b)))

            case 'titleDesc':
                return normalizeText(getAssignmentTitle(b))
                    .localeCompare(normalizeText(getAssignmentTitle(a)))

            case 'statusAsc':
                return normalizeText(a.status).localeCompare(normalizeText(b.status))

            case 'statusDesc':
                return normalizeText(b.status).localeCompare(normalizeText(a.status))

            case 'gradingTypeAsc':
                return normalizeText(a.grading_type)
                    .localeCompare(normalizeText(b.grading_type))

            case 'gradingTypeDesc':
                return normalizeText(b.grading_type)
                    .localeCompare(normalizeText(a.grading_type))

            case 'attemptsLimitAsc':
                return (a.attempts_limit ?? 0) - (b.attempts_limit ?? 0)

            case 'attemptsLimitDesc':
                return (b.attempts_limit ?? 0) - (a.attempts_limit ?? 0)

            case 'maxScoreAsc':
                return (a.max_score ?? 0) - (b.max_score ?? 0)

            case 'maxScoreDesc':
                return (b.max_score ?? 0) - (a.max_score ?? 0)

            case 'submissionsAsc':
                return (a.submissions_count ?? 0) - (b.submissions_count ?? 0)

            case 'submissionsDesc':
                return (b.submissions_count ?? 0) - (a.submissions_count ?? 0)

            case 'imagesAsc':
                return (a.images_count ?? 0) - (b.images_count ?? 0)

            case 'imagesDesc':
                return (b.images_count ?? 0) - (a.images_count ?? 0)

            case 'dueAtAsc':
                return new Date(a.due_at ?? 0) - new Date(b.due_at ?? 0)

            case 'dueAtDesc':
                return new Date(b.due_at ?? 0) - new Date(a.due_at ?? 0)

            case 'publishedAtAsc':
                return new Date(a.published_at ?? a.created_at ?? 0)
                    - new Date(b.published_at ?? b.created_at ?? 0)

            case 'publishedAtDesc':
                return new Date(b.published_at ?? b.created_at ?? 0)
                    - new Date(a.published_at ?? a.created_at ?? 0)

            case 'dateAsc':
                return new Date(a.created_at ?? a.published_at ?? 0)
                    - new Date(b.created_at ?? b.published_at ?? 0)

            case 'dateDesc':
                return new Date(b.created_at ?? b.published_at ?? 0)
                    - new Date(a.created_at ?? a.published_at ?? 0)

            default:
                return 0
        }
    })
})

/** Frontend-пагинация */
const frontendPaginatedAssignments = computed(() => {
    const start = (frontendCurrentPage.value - 1) * perPage.value

    return sortedAssignments.value.slice(start, start + perPage.value)
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
        props.assignments?.meta?.current_page
        ?? props.assignments?.current_page
        ?? 1
    ) || 1
})

/** Последняя server-страница */
const lastPage = computed(() => {
    return Number(
        props.assignments?.meta?.last_page
        ?? props.assignments?.last_page
        ?? 1
    ) || 1
})

/** Маршрут списка заданий */
const indexRoute = () => route('public.schoolAssignments.index')

/** Server-загрузка заданий */
const reloadAssignments = (page = 1) => {
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
    reloadAssignments(1)
}

/** Сброс поиска и сортировки */
const resetSearch = () => {
    q.value = ''
    sort.value = DEFAULT_SORT
    frontendCurrentPage.value = 1

    if (props.useServerProcessing) {
        reloadAssignments(1)
    }
}

/** Server-изменение сортировки */
const updateSort = (value) => {
    sort.value = value || DEFAULT_SORT

    if (props.useServerProcessing) {
        reloadAssignments(1)
    }
}

/** Изменение режима отображения */
const updateViewMode = (value) => {
    viewMode.value = value || 'grid'
    frontendCurrentPage.value = 1

    if (props.useServerProcessing) {
        reloadAssignments(1)
    }
}

/** Server-переход на страницу */
const goToPage = (page) => {
    const value = Number(page)

    if (!Number.isFinite(value)) {
        return
    }

    const safePage = Math.max(1, Math.min(value, lastPage.value))

    reloadAssignments(safePage)
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

/* ===================== SIDEBAR WATCH ===================== */

/**
 * Сохраняем состояние сайдбаров.
 *
 * В server-режиме при сетке
 * запрашиваем новое количество заданий.
 */
watch([leftCollapsed, rightCollapsed], () => {
    localStorage.setItem(LEFT_SIDEBAR_KEY, String(leftCollapsed.value))
    localStorage.setItem(RIGHT_SIDEBAR_KEY, String(rightCollapsed.value))

    frontendCurrentPage.value = 1

    if (props.useServerProcessing && viewMode.value === 'grid') {
        reloadAssignments(1)
    }
})

/**
 * Синхронизация первого server-запроса
 * с состоянием сайдбаров из localStorage.
 */
onMounted(() => {
    if (!props.useServerProcessing || viewMode.value !== 'grid') {
        return
    }

    const serverPerPage = Number(props.filters?.per_page ?? basePerPage.value)

    if (serverPerPage !== perPage.value) {
        reloadAssignments(1)
    }
})

/* ===================== COMMON VIEW ===================== */

/** Итоговый список заданий */
const displayedAssignments = computed(() => {
    return props.useServerProcessing
        ? assignmentsData.value
        : frontendPaginatedAssignments.value
})

/** Видео внизу страницы */
const mainVideosList = computed(() => normalizeList(props.mainVideos))

/** Баннеры внизу страницы */
const mainBannersList = computed(() => normalizeList(props.mainBanners))
</script>

<template>
    <!-- SEO -->
    <Head>
        <title>{{ seo?.title || t('assignments') }}</title>

        <meta name="title" :content="seo?.title || t('assignments')" />
        <meta name="keywords" :content="seo?.keywords || ''" />
        <meta name="description" :content="seo?.description || t('assignments')" />

        <meta property="og:title" :content="seo?.title || t('assignments')" />
        <meta property="og:description" :content="seo?.description || t('assignments')" />
        <meta property="og:type" content="website" />
        <meta property="og:url" :content="`/${locale}/school/assignments`" />
        <meta property="og:image" content="" />
        <meta property="og:locale" :content="locale === 'ru' ? 'ru_RU' : locale" />

        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="seo?.title || t('assignments')" />
        <meta name="twitter:description" :content="seo?.description || t('assignments')" />
        <meta name="twitter:image" content="" />

        <meta name="DC.title" :content="seo?.title || t('assignments')" />
        <meta name="DC.description" :content="seo?.description || t('assignments')" />
        <meta name="DC.identifier" :content="`/${locale}/school/assignments`" />
        <meta name="DC.language" :content="locale" />
    </Head>

    <DefaultLayout :title="title" :can-login="canLogin" :can-register="canRegister">
        <!-- Шапка -->
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

                <!-- Центральная колонка -->
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

                                <li>
                                    <Link
                                        :href="route('public.schoolTracks.index')"
                                        class="breadcrumb-link hover:underline"
                                    >
                                        {{ t('tracks') }}
                                    </Link>
                                </li>

                                <li><span class="mx-2 breadcrumbs">/</span></li>

                                <li>
                                    <Link
                                        :href="route('public.schoolCourses.index')"
                                        class="breadcrumb-link hover:underline"
                                    >
                                        {{ t('courses') }}
                                    </Link>
                                </li>

                                <li><span class="mx-2 breadcrumbs">/</span></li>

                                <li>
                                    <Link
                                        :href="route('public.schoolModules.index')"
                                        class="breadcrumb-link hover:underline"
                                    >
                                        {{ t('modules') }}
                                    </Link>
                                </li>

                                <li><span class="mx-2 breadcrumbs">/</span></li>

                                <li>
                                    <Link
                                        :href="route('public.schoolLessons.index')"
                                        class="breadcrumb-link hover:underline"
                                    >
                                        {{ t('lessons') }}
                                    </Link>
                                </li>

                                <li><span class="mx-2 breadcrumbs">/</span></li>

                                <li class="breadcrumbs">
                                    {{ t('assignments') }}
                                </li>
                            </ol>
                        </nav>

                        <!-- Заголовок -->
                        <div class="my-3 flex flex-wrap items-center justify-center gap-3 title">
                            <svg
                                class="shrink-0 h-5 w-5 text-slate-600/85 dark:text-slate-200/85"
                                fill="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path d="M15,18v2H9v-2H1v5c0,0.552,0.448,1,1,1h20c0.552,0,1-0.448,1-1v-5H15z"></path>
                                <path d="M23,4h-6V1c0-0.552-0.448-1-1-1H8C7.448,0,7,0.448,7,1v3H1C0.448,4,0,4.448,0,5v10c0,0.552,0.448,1,1,1h8v-3 h6v3h8c0.552,0,1-0.448,1-1V5C24,4.448,23.552,4,23,4z M15,4H9V2h6V4z"></path>
                            </svg>

                            <h1 class="text-2xl font-bold">
                                {{ t('assignments') }}
                            </h1>
                        </div>

                        <!-- Подзаголовок -->
                        <div class="my-1 text-sm subtitle text-center">
                            Просматривайте задания, сроки сдачи и требования в удобном формате.
                        </div>

                        <!-- Server toolbar -->
                        <EntityPageToolbar
                            v-if="useServerProcessing"
                            v-model="q"
                            :found="assignmentsFound"
                            :view-mode="viewMode"
                            :sort-value="sort"
                            :sort-options="assignmentSortOptions"
                            :default-sort="DEFAULT_SORT"
                            :found-label="t('assignments')"
                            :search-placeholder="t('searchByName')"
                            @submit="submitSearch"
                            @reset="resetSearch"
                            @update:viewMode="updateViewMode"
                            @update:sortValue="updateSort"
                        />

                        <!-- Frontend toolbar -->
                        <FrontendEntityPageToolbar
                            v-else
                            v-model="q"
                            :found="sortedAssignments.length"
                            :view-mode="viewMode"
                            :sort-value="sort"
                            :sort-options="assignmentSortOptions"
                            :default-sort="DEFAULT_SORT"
                            :found-label="t('assignments')"
                            :search-placeholder="t('searchByName')"
                            @reset="resetSearch"
                            @update:viewMode="updateViewMode"
                            @update:sortValue="updateSort"
                        />

                        <!-- Точка скролла -->
                        <div ref="scrollTarget"></div>

                        <!-- Нет данных -->
                        <div
                            v-if="displayedAssignments.length === 0"
                            class="mt-6 text-center text-slate-700 dark:text-slate-300"
                        >
                            {{ t('noData') }}
                        </div>

                        <!-- Список -->
                        <div v-else>
                            <AssignmentGrid
                                v-if="viewMode === 'grid'"
                                :assignments="displayedAssignments"
                                :cols="gridCols"
                            />

                            <AssignmentRows
                                v-else
                                :assignments="displayedAssignments"
                            />
                        </div>

                        <!-- Server-пагинация -->
                        <Pagination
                            v-if="useServerProcessing"
                            :current-page="currentPage"
                            :last-page="lastPage"
                            :found="assignmentsFound"
                            @prev="goPrev"
                            @next="goNext"
                            @go="goToPage"
                        />

                        <!-- Frontend-пагинация -->
                        <FrontendPagination
                            v-else
                            v-model:currentPage="frontendCurrentPage"
                            :items-per-page="perPage"
                            :total-items="sortedAssignments.length"
                        />

                        <!-- Видео -->
                        <SectionVideoList :videos="mainVideosList" />

                        <!-- Баннеры -->
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

        <!-- Подвал и прогресс -->
        <FooterBlog />
        <Progress />

        <!-- Нижняя панель администратора -->
        <PublicAdminBottomPanel
            v-if="isAdmin"
            setting-key="publicSchoolAssignmentsProcessingMode"
            :mode="publicSchoolAssignmentsProcessingMode"
            :use-server-processing="useServerProcessing"
            :total="assignmentsCount"
        />
    </DefaultLayout>
</template>
