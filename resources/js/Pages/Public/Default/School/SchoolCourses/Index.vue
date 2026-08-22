<script setup>
/**
 * Страница списка курсов
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
import FrontendEntityPageToolbar
    from '@/Components/Public/Default/PageToolbar/FrontendEntityPageToolbar.vue'
import Pagination from '@/Components/Public/Default/Pagination/Pagination.vue'
import FrontendPagination from '@/Components/Public/Default/Pagination/FrontendPagination.vue'
import SectionVideoList from '@/Components/Public/Default/Blog/BlogVideo/SectionVideoList.vue'
import SectionBanners from '@/Components/Public/Default/Blog/BlogBanner/SectionBanners.vue'
import InstructorCourseGrid
    from '@/Components/Public/Default/School/SchoolInstructor/InstructorCourseGrid.vue'
import InstructorCourseRows
    from '@/Components/Public/Default/School/SchoolInstructor/InstructorCourseRows.vue'
import PublicAdminBottomPanel
    from '@/Components/Admin/UI/PublicAdminPanel/PublicAdminBottomPanel.vue'

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
    publicSchoolCoursesProcessingMode: { type: String, default: 'server' },

    title: { type: String, default: '' },
    canLogin: { type: Boolean, default: false },
    canRegister: { type: Boolean, default: false },

    trackTree: { type: Array, default: () => [] },

    courses: { type: [Array, Object], default: () => [] },
    coursesCount: { type: Number, default: 0 },
    coursesFound: { type: Number, default: 0 },

    filters: { type: Object, default: () => ({}) },

    hashtags: { type: Array, default: () => [] },
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

/* ===================== COURSES DATA ===================== */

/** Универсальный список курсов */
const coursesData = computed(() => {
    if (Array.isArray(props.courses)) {
        return props.courses
    }

    if (Array.isArray(props.courses?.data)) {
        return props.courses.data
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
 * Количество курсов при этом не меняется.
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
 * publicSchoolCoursesDefaultSort → idDesc.
 */
const DEFAULT_SORT = 'idDesc'

/** Текущая сортировка */
const sort = ref(
    String(props.filters?.sort ?? DEFAULT_SORT)
)

/** Ключ режима отображения */
const VIEW_KEY = 'public_school_courses_view'

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
 * Количество курсов на странице.
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
const courseSortOptions = [
    { value: 'idDesc', label: t('idDesc') },
    { value: 'idAsc', label: t('idAsc') },

    { value: 'sortAsc', label: `${t('sortNumber')} 0→9` },
    { value: 'sortDesc', label: `${t('sortNumber')} 9→0` },

    { value: 'titleAsc', label: `${t('title')} A→Z` },
    { value: 'titleDesc', label: `${t('title')} Z→A` },

    { value: 'studentsCountDesc', label: `${t('students')} 9→0` },
    { value: 'studentsCountAsc', label: `${t('students')} 0→9` },

    { value: 'viewsDesc', label: `${t('views')} 9→0` },
    { value: 'viewsAsc', label: `${t('views')} 0→9` },

    { value: 'likesDesc', label: `${t('likes')} 9→0` },
    { value: 'likesAsc', label: `${t('likes')} 0→9` },

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

    { value: 'levelAsc', label: `${t('level')} A→Z` },
    { value: 'levelDesc', label: `${t('level')} Z→A` },

    { value: 'statusAsc', label: `${t('status')} A→Z` },
    { value: 'statusDesc', label: `${t('status')} Z→A` },

    { value: 'availabilityAsc', label: `${t('availability')} A→Z` },
    { value: 'availabilityDesc', label: `${t('availability')} Z→A` },

    { value: 'modulesDesc', label: `${t('modules')} 9→0` },
    { value: 'modulesAsc', label: `${t('modules')} 0→9` },

    { value: 'lessonsDesc', label: `${t('lessons')} 9→0` },
    { value: 'lessonsAsc', label: `${t('lessons')} 0→9` },

    { value: 'tracksDesc', label: `${t('tracks')} 9→0` },
    { value: 'tracksAsc', label: `${t('tracks')} 0→9` },

    { value: 'hashtagsDesc', label: `${t('hashtags')} 9→0` },
    { value: 'hashtagsAsc', label: `${t('hashtags')} 0→9` },

    { value: 'imagesDesc', label: `${t('images')} 9→0` },
    { value: 'imagesAsc', label: `${t('images')} 0→9` },

    { value: 'pricesDesc', label: `${t('prices')} 9→0` },
    { value: 'pricesAsc', label: `${t('prices')} 0→9` },

    { value: 'reviewsDesc', label: `${t('reviews')} 9→0` },
    { value: 'reviewsAsc', label: `${t('reviews')} 0→9` },

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

/** Название курса */
const getCourseTitle = (course) => {
    return course?.translation?.title
        || ''
}

/** Краткий текст курса */
const getCourseShort = (course) => {
    return course?.translation?.short
        || ''
}

/** Slug курса */
const getCourseSlug = (course) => {
    return course?.slug
        || ''
}

/** Локальный поиск */
const filteredCourses = computed(() => {
    const query = normalizeText(q.value).trim()

    if (!query) {
        return coursesData.value
    }

    return coursesData.value.filter((course) => {
        return [
            getCourseTitle(course),
            getCourseShort(course),
            getCourseSlug(course),

            course?.instructorProfile
                ?.translation
                ?.title,

            course?.instructorProfile
                ?.user
                ?.name,
        ].some((value) =>
            normalizeText(value)
                .includes(query)
        )
    })
})

/** Локальная сортировка */
const sortedCourses = computed(() => {
    const list = [...filteredCourses.value]

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
                return normalizeText(getCourseTitle(a))
                    .localeCompare(normalizeText(getCourseTitle(b)))

            case 'titleDesc':
                return normalizeText(getCourseTitle(b))
                    .localeCompare(normalizeText(getCourseTitle(a)))

            case 'studentsCountAsc':
                return (a.students_count ?? 0)
                    - (b.students_count ?? 0)

            case 'studentsCountDesc':
                return (b.students_count ?? 0)
                    - (a.students_count ?? 0)

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

            case 'levelAsc':
                return normalizeText(a.level)
                    .localeCompare(normalizeText(b.level))

            case 'levelDesc':
                return normalizeText(b.level)
                    .localeCompare(normalizeText(a.level))

            case 'statusAsc':
                return normalizeText(a.status)
                    .localeCompare(normalizeText(b.status))

            case 'statusDesc':
                return normalizeText(b.status)
                    .localeCompare(normalizeText(a.status))

            case 'availabilityAsc':
                return normalizeText(a.availability)
                    .localeCompare(normalizeText(b.availability))

            case 'availabilityDesc':
                return normalizeText(b.availability)
                    .localeCompare(normalizeText(a.availability))

            case 'modulesAsc':
                return (a.modules_count ?? 0)
                    - (b.modules_count ?? 0)

            case 'modulesDesc':
                return (b.modules_count ?? 0)
                    - (a.modules_count ?? 0)

            case 'lessonsAsc':
                return (a.lessons_count ?? 0)
                    - (b.lessons_count ?? 0)

            case 'lessonsDesc':
                return (b.lessons_count ?? 0)
                    - (a.lessons_count ?? 0)

            case 'tracksAsc':
                return (a.tracks_count ?? 0)
                    - (b.tracks_count ?? 0)

            case 'tracksDesc':
                return (b.tracks_count ?? 0)
                    - (a.tracks_count ?? 0)

            case 'hashtagsAsc':
                return (a.hashtags_count ?? 0)
                    - (b.hashtags_count ?? 0)

            case 'hashtagsDesc':
                return (b.hashtags_count ?? 0)
                    - (a.hashtags_count ?? 0)

            case 'imagesAsc':
                return (a.images_count ?? 0)
                    - (b.images_count ?? 0)

            case 'imagesDesc':
                return (b.images_count ?? 0)
                    - (a.images_count ?? 0)

            case 'pricesAsc':
                return (a.prices_count ?? 0)
                    - (b.prices_count ?? 0)

            case 'pricesDesc':
                return (b.prices_count ?? 0)
                    - (a.prices_count ?? 0)

            case 'reviewsAsc':
                return (a.reviews_count ?? 0)
                    - (b.reviews_count ?? 0)

            case 'reviewsDesc':
                return (b.reviews_count ?? 0)
                    - (a.reviews_count ?? 0)

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
const frontendPaginatedCourses = computed(() => {
    const start = (
        frontendCurrentPage.value - 1
    ) * perPage.value

    return sortedCourses.value.slice(
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
        props.courses?.meta?.current_page
        ?? props.courses?.current_page
        ?? 1
    ) || 1
})

/** Последняя server-страница */
const lastPage = computed(() => {
    return Number(
        props.courses?.meta?.last_page
        ?? props.courses?.last_page
        ?? 1
    ) || 1
})

/** Маршрут списка курсов */
const indexRoute = () => {
    return route('public.schoolCourses.index')
}

/**
 * Server-загрузка курсов.
 *
 * per_page намеренно не отправляем.
 * Его всегда определяет backend через PublicSettingsService.
 */
const reloadCourses = (page = 1) => {
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
    reloadCourses(1)
}

/** Сброс поиска и сортировки */
const resetSearch = () => {
    q.value = ''
    sort.value = DEFAULT_SORT
    frontendCurrentPage.value = 1

    if (props.useServerProcessing) {
        reloadCourses(1)
    }
}

/** Server-изменение сортировки */
const updateSort = (value) => {
    sort.value = value || DEFAULT_SORT

    if (props.useServerProcessing) {
        reloadCourses(1)
    }
}

/** Изменение режима отображения */
const updateViewMode = (value) => {
    viewMode.value = value || 'grid'
    frontendCurrentPage.value = 1

    if (props.useServerProcessing) {
        reloadCourses(1)
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

    reloadCourses(safePage)
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

/** Итоговый список курсов */
const displayedCourses = computed(() => {
    return props.useServerProcessing
        ? coursesData.value
        : frontendPaginatedCourses.value
})
</script>

<template>
    <!-- SEO -->
    <Head>
        <title>{{ seo?.title || t('courses') }}</title>

        <meta name="title" :content="seo?.title || t('courses')" />
        <meta name="keywords" :content="seo?.keywords || ''" />
        <meta name="description" :content="seo?.description || t('courses')" />

        <meta property="og:title" :content="seo?.title || t('courses')" />
        <meta property="og:description" :content="seo?.description || t('courses')" />
        <meta property="og:type" content="website" />
        <meta property="og:url" :content="`/${locale}/school/courses`" />
        <meta property="og:image" content="" />
        <meta property="og:locale" :content="locale === 'ru' ? 'ru_RU' : locale" />

        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="seo?.title || t('courses')" />
        <meta name="twitter:description" :content="seo?.description || t('courses')" />
        <meta name="twitter:image" content="" />

        <meta name="DC.title" :content="seo?.title || t('courses')" />
        <meta name="DC.description" :content="seo?.description || t('courses')" />
        <meta name="DC.identifier" :content="`/${locale}/school/courses`" />
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
                                    {{ t('courses') }}
                                </li>
                            </ol>
                        </nav>

                        <!-- Заголовок -->
                        <div class="my-3 flex flex-wrap items-center justify-center gap-3 title">
                            <svg class="shrink-0 h-5 w-5 text-slate-600/85 dark:text-slate-200/85"
                                 fill="currentColor"
                                 viewBox="0 0 448 512">
                                <path d="M318.38 208h-39.09c-1.49 27.03-6.54 51.35-14.21 70.41 27.71-13.24 48.02-39.19 53.3-70.41zm0-32c-5.29-31.22-25.59-57.17-53.3-70.41 7.68 19.06 12.72 43.38 14.21 70.41h39.09zM224 97.31c-7.69 7.45-20.77 34.42-23.43 78.69h46.87c-2.67-44.26-15.75-71.24-23.44-78.69zm-41.08 8.28c-27.71 13.24-48.02 39.19-53.3 70.41h39.09c1.49-27.03 6.53-51.35 14.21-70.41zm0 172.82c-7.68-19.06-12.72-43.38-14.21-70.41h-39.09c5.28 31.22 25.59 57.17 53.3 70.41zM247.43 208h-46.87c2.66 44.26 15.74 71.24 23.43 78.69 7.7-7.45 20.78-34.43 23.44-78.69zM448 358.4V25.6c0-16-9.6-25.6-25.6-25.6H96C41.6 0 0 41.6 0 96v320c0 54.4 41.6 96 96 96h326.4c12.8 0 25.6-9.6 25.6-25.6v-16c0-6.4-3.2-12.8-9.6-19.2-3.2-16-3.2-60.8 0-73.6 6.4-3.2 9.6-9.6 9.6-19.2zM224 64c70.69 0 128 57.31 128 128s-57.31 128-128 128S96 262.69 96 192 153.31 64 224 64zm160 384H96c-19.2 0-32-12.8-32-32s16-32 32-32h288v64z"/>
                            </svg>
                            <h1 class="text-2xl font-bold">
                                {{ t('courses') }}
                            </h1>
                        </div>

                        <!-- Подзаголовок -->
                        <div class="my-1 text-sm subtitle text-center">
                            Найдите идеальный курс для вашего развития.
                        </div>

                        <!-- Поиск, количество, сортировка, вид -->
                        <EntityPageToolbar
                            v-if="useServerProcessing"
                            v-model="q"
                            :found="coursesFound"
                            :view-mode="viewMode"
                            :sort-value="sort"
                            :sort-options="courseSortOptions"
                            :default-sort="DEFAULT_SORT"
                            :found-label="t('courses')"
                            :search-placeholder="t('searchByName')"
                            @submit="submitSearch"
                            @reset="resetSearch"
                            @update:viewMode="updateViewMode"
                            @update:sortValue="updateSort"
                        />

                        <FrontendEntityPageToolbar
                            v-else
                            v-model="q"
                            :found="sortedCourses.length"
                            :view-mode="viewMode"
                            :sort-value="sort"
                            :sort-options="courseSortOptions"
                            :default-sort="DEFAULT_SORT"
                            :found-label="t('courses')"
                            :search-placeholder="t('searchByName')"
                            @reset="resetSearch"
                            @update:viewMode="updateViewMode"
                            @update:sortValue="updateSort"
                        />

                        <div ref="scrollTarget"></div>

                        <!-- Нет данных -->
                        <div
                            v-if="displayedCourses.length === 0"
                            class="mt-6 text-center text-slate-700 dark:text-slate-300"
                        >
                            {{ t('noData') }}
                        </div>

                        <!-- Показ grid/rows -->
                        <div v-else>
                            <InstructorCourseGrid
                                v-if="viewMode === 'grid'"
                                :courses="displayedCourses"
                                :cols="gridCols"
                            />

                            <InstructorCourseRows
                                v-else
                                :courses="displayedCourses"
                            />
                        </div>

                        <!-- Пагинация -->
                        <Pagination
                            v-if="useServerProcessing"
                            :current-page="currentPage"
                            :last-page="lastPage"
                            :found="coursesFound"
                            @prev="goPrev"
                            @next="goNext"
                            @go="goToPage"
                        />

                        <FrontendPagination
                            v-else
                            v-model:currentPage="frontendCurrentPage"
                            :items-per-page="perPage"
                            :total-items="sortedCourses.length"
                        />

                        <!-- Главные видео, баннеры -->
                        <SectionVideoList :videos="mainVideos" />
                        <SectionBanners :banners="mainBanners" />
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

        <!-- Подвал и кнопка с прогрессом -->
        <FooterBlog />
        <Progress />

        <!-- Нижняя панель администратора -->
        <PublicAdminBottomPanel
            v-if="isAdmin"
            setting-key="publicSchoolCoursesProcessingMode"
            :mode="publicSchoolCoursesProcessingMode"
            :use-server-processing="useServerProcessing"
            :total="coursesCount"
        />
    </DefaultLayout>
</template>
