<script setup>
/**
 * Страница списка уроков
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
import LessonGrid from '@/Components/Public/Default/School/SchoolLesson/LessonGrid.vue'
import LessonRows from '@/Components/Public/Default/School/SchoolLesson/LessonRows.vue'
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
    publicSchoolLessonsProcessingMode: { type: String, default: 'server' },

    title: { type: String, default: '' },
    canLogin: { type: Boolean, default: false },
    canRegister: { type: Boolean, default: false },

    trackTree: { type: Array, default: () => [] },

    lessons: { type: [Array, Object], default: () => [] },
    lessonsCount: { type: Number, default: 0 },
    lessonsFound: { type: Number, default: 0 },

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

/** Нормализация массивов */
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

/* ===================== LESSONS DATA ===================== */

/** Универсальный список уроков */
const lessonsData = computed(() => {
    if (Array.isArray(props.lessons)) {
        return props.lessons
    }

    if (Array.isArray(props.lessons?.data)) {
        return props.lessons.data
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
 * Количество уроков при этом не меняется.
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

/** Сортировка по умолчанию */
const DEFAULT_SORT = 'idDesc'

/** Текущая сортировка */
const sort = ref(
    String(props.filters?.sort ?? DEFAULT_SORT)
)

/** Ключ режима отображения */
const VIEW_KEY = 'public_school_lessons_view'

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
 * Количество уроков на странице.
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
const lessonSortOptions = [
    { value: 'idDesc', label: t('idDesc') },
    { value: 'idAsc', label: t('idAsc') },

    { value: 'sortAsc', label: `${t('sortNumber')} 0→9` },
    { value: 'sortDesc', label: `${t('sortNumber')} 9→0` },

    { value: 'titleAsc', label: `${t('title')} A→Z` },
    { value: 'titleDesc', label: `${t('title')} Z→A` },

    { value: 'viewsDesc', label: `${t('views')} 9→0` },
    { value: 'viewsAsc', label: `${t('views')} 0→9` },

    { value: 'likesCountDesc', label: `${t('likes')} 9→0` },
    { value: 'likesCountAsc', label: `${t('likes')} 0→9` },

    { value: 'ratingAvgDesc', label: `${t('ratingAvg')} 9→0` },
    { value: 'ratingAvgAsc', label: `${t('ratingAvg')} 0→9` },

    { value: 'ratingCountDesc', label: `${t('ratingCount')} 9→0` },
    { value: 'ratingCountAsc', label: `${t('ratingCount')} 0→9` },

    { value: 'popularityDesc', label: `${t('popularity')} 9→0` },
    { value: 'popularityAsc', label: `${t('popularity')} 0→9` },

    { value: 'durationDesc', label: `${t('duration')} 9→0` },
    { value: 'durationAsc', label: `${t('duration')} 0→9` },

    { value: 'difficultyDesc', label: `${t('sortDifficulty')} 9→0` },
    { value: 'difficultyAsc', label: `${t('sortDifficulty')} 0→9` },

    { value: 'publishedAtDesc', label: `${t('publishedAt')} ↓` },
    { value: 'publishedAtAsc', label: `${t('publishedAt')} ↑` },

    { value: 'dateDesc', label: t('sortNewestFirst') },
    { value: 'dateAsc', label: t('sortOldestFirst') },

    { value: 'moduleDesc', label: `${t('module')} 9→0` },
    { value: 'moduleAsc', label: `${t('module')} 0→9` },

    { value: 'statusAsc', label: `${t('status')} A→Z` },
    { value: 'statusDesc', label: `${t('status')} Z→A` },

    { value: 'availabilityAsc', label: `${t('availability')} A→Z` },
    { value: 'availabilityDesc', label: `${t('availability')} Z→A` },

    { value: 'accessTypeAsc', label: `${t('accessType')} A→Z` },
    { value: 'accessTypeDesc', label: `${t('accessType')} Z→A` },

    { value: 'imagesDesc', label: `${t('images')} 9→0` },
    { value: 'imagesAsc', label: `${t('images')} 0→9` },

    { value: 'hashtagsDesc', label: `${t('hashtags')} 9→0` },
    { value: 'hashtagsAsc', label: `${t('hashtags')} 0→9` },

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

/** Название урока */
const getLessonTitle = (lesson) => {
    return lesson?.title
        || lesson?.name
        || lesson?.translation?.title
        || lesson?.translation?.name
        || lesson?.current_translation?.title
        || lesson?.current_translation?.name
        || lesson?.translations?.[0]?.title
        || lesson?.translations?.[0]?.name
        || ''
}

/** Краткий текст урока */
const getLessonShort = (lesson) => {
    return lesson?.short
        || lesson?.description
        || lesson?.subtitle
        || lesson?.translation?.short
        || lesson?.translation?.description
        || lesson?.translation?.subtitle
        || lesson?.current_translation?.short
        || lesson?.current_translation?.description
        || lesson?.current_translation?.subtitle
        || lesson?.translations?.[0]?.short
        || lesson?.translations?.[0]?.description
        || lesson?.translations?.[0]?.subtitle
        || ''
}

/** Slug урока */
const getLessonSlug = (lesson) => {
    return lesson?.slug
        || lesson?.url
        || lesson?.translation?.slug
        || lesson?.current_translation?.slug
        || lesson?.translations?.[0]?.slug
        || ''
}

/** Название модуля */
const getModuleTitle = (lesson) => {
    return lesson?.module?.title
        || lesson?.module?.name
        || lesson?.module?.translation?.title
        || lesson?.module?.translation?.name
        || lesson?.module?.translations?.[0]?.title
        || lesson?.module?.translations?.[0]?.name
        || ''
}

/** Название курса */
const getCourseTitle = (lesson) => {
    return lesson?.module?.course?.title
        || lesson?.module?.course?.name
        || lesson?.module?.course?.translation?.title
        || lesson?.module?.course?.translation?.name
        || lesson?.module?.course?.translations?.[0]?.title
        || lesson?.module?.course?.translations?.[0]?.name
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

/** Текст хештегов */
const getHashtagText = (lesson) => {
    const hashtags = Array.isArray(lesson?.hashtags)
        ? lesson.hashtags
        : []

    return hashtags.map((hashtag) => {
        return [
            hashtag.name,
            hashtag.title,
            hashtag.slug,
            hashtag.translation?.name,
            hashtag.translation?.slug,
            hashtag.translation?.short,
            hashtag.translation?.description,
            hashtag.translations?.[0]?.name,
            hashtag.translations?.[0]?.slug,
            hashtag.translations?.[0]?.short,
            hashtag.translations?.[0]?.description,
        ].filter(Boolean).join(' ')
    }).join(' ')
}

/** Локальный поиск */
const filteredLessons = computed(() => {
    const words = splitSearchWords(q.value)

    if (words.length === 0) {
        return lessonsData.value
    }

    return lessonsData.value.filter((lesson) => {
        const haystack = normalizeText([
            lesson.id,
            lesson.school_module_id,
            lesson.status,
            lesson.availability,
            lesson.access_type,
            lesson.content_type,

            getLessonTitle(lesson),
            getLessonShort(lesson),
            getLessonSlug(lesson),
            getModuleTitle(lesson),
            getCourseTitle(lesson),

            lesson.module?.course?.instructorProfile?.title,
            lesson.module?.course?.instructor_profile?.title,
            lesson.module?.course?.instructorProfile?.user?.name,
            lesson.module?.course?.instructor_profile?.user?.name,
            lesson.module?.course?.instructorProfile?.user?.email,
            lesson.module?.course?.instructor_profile?.user?.email,

            getHashtagText(lesson),
        ].filter(Boolean).join(' '))

        return words.every((word) => {
            return haystack.includes(word)
        })
    })
})

/** Локальная сортировка */
const sortedLessons = computed(() => {
    const list = [...filteredLessons.value]

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
                return normalizeText(getLessonTitle(a))
                    .localeCompare(
                        normalizeText(getLessonTitle(b))
                    )

            case 'titleDesc':
                return normalizeText(getLessonTitle(b))
                    .localeCompare(
                        normalizeText(getLessonTitle(a))
                    )

            case 'viewsAsc':
                return (a.views ?? 0)
                    - (b.views ?? 0)

            case 'viewsDesc':
                return (b.views ?? 0)
                    - (a.views ?? 0)

            case 'likesCountAsc':
            case 'likesAsc':
                return (a.likes_count ?? a.likes ?? 0)
                    - (b.likes_count ?? b.likes ?? 0)

            case 'likesCountDesc':
            case 'likesDesc':
                return (b.likes_count ?? b.likes ?? 0)
                    - (a.likes_count ?? a.likes ?? 0)

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

            case 'popularityAsc':
                return (a.popularity ?? 0)
                    - (b.popularity ?? 0)

            case 'popularityDesc':
                return (b.popularity ?? 0)
                    - (a.popularity ?? 0)

            case 'durationAsc':
                return (a.duration ?? 0)
                    - (b.duration ?? 0)

            case 'durationDesc':
                return (b.duration ?? 0)
                    - (a.duration ?? 0)

            case 'difficultyAsc':
                return (a.difficulty ?? 0)
                    - (b.difficulty ?? 0)

            case 'difficultyDesc':
                return (b.difficulty ?? 0)
                    - (a.difficulty ?? 0)

            case 'moduleAsc':
                return (
                    a.school_module_id
                    ?? a.module_id
                    ?? a.module?.id
                    ?? 0
                ) - (
                    b.school_module_id
                    ?? b.module_id
                    ?? b.module?.id
                    ?? 0
                )

            case 'moduleDesc':
                return (
                    b.school_module_id
                    ?? b.module_id
                    ?? b.module?.id
                    ?? 0
                ) - (
                    a.school_module_id
                    ?? a.module_id
                    ?? a.module?.id
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

            case 'accessTypeAsc':
                return normalizeText(a.access_type)
                    .localeCompare(
                        normalizeText(b.access_type)
                    )

            case 'accessTypeDesc':
                return normalizeText(b.access_type)
                    .localeCompare(
                        normalizeText(a.access_type)
                    )

            case 'imagesAsc':
                return (a.images_count ?? 0)
                    - (b.images_count ?? 0)

            case 'imagesDesc':
                return (b.images_count ?? 0)
                    - (a.images_count ?? 0)

            case 'hashtagsAsc':
                return (a.hashtags_count ?? 0)
                    - (b.hashtags_count ?? 0)

            case 'hashtagsDesc':
                return (b.hashtags_count ?? 0)
                    - (a.hashtags_count ?? 0)

            case 'publishedAtAsc':
                return new Date(
                    a.published_at
                    ?? a.created_at
                    ?? 0
                ) - new Date(
                    b.published_at
                    ?? b.created_at
                    ?? 0
                )

            case 'publishedAtDesc':
                return new Date(
                    b.published_at
                    ?? b.created_at
                    ?? 0
                ) - new Date(
                    a.published_at
                    ?? a.created_at
                    ?? 0
                )

            case 'dateAsc':
            case 'createdAtAsc':
                return new Date(
                    a.created_at
                    ?? a.published_at
                    ?? 0
                ) - new Date(
                    b.created_at
                    ?? b.published_at
                    ?? 0
                )

            case 'dateDesc':
            case 'createdAtDesc':
                return new Date(
                    b.created_at
                    ?? b.published_at
                    ?? 0
                ) - new Date(
                    a.created_at
                    ?? a.published_at
                    ?? 0
                )

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
const frontendPaginatedLessons = computed(() => {
    const start = (
        frontendCurrentPage.value - 1
    ) * perPage.value

    return sortedLessons.value.slice(
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
        props.lessons?.meta?.current_page
        ?? props.lessons?.current_page
        ?? 1
    ) || 1
})

/** Последняя server-страница */
const lastPage = computed(() => {
    return Number(
        props.lessons?.meta?.last_page
        ?? props.lessons?.last_page
        ?? 1
    ) || 1
})

/** Маршрут списка уроков */
const indexRoute = () => {
    return route('public.schoolLessons.index')
}

/**
 * Server-загрузка уроков.
 *
 * per_page намеренно не отправляем.
 * Его всегда определяет backend через PublicSettingsService.
 */
const reloadLessons = (page = 1) => {
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
    reloadLessons(1)
}

/** Сброс поиска и сортировки */
const resetSearch = () => {
    q.value = ''
    sort.value = DEFAULT_SORT
    frontendCurrentPage.value = 1

    if (props.useServerProcessing) {
        reloadLessons(1)
    }
}

/** Server-изменение сортировки */
const updateSort = (value) => {
    sort.value = value || DEFAULT_SORT

    if (props.useServerProcessing) {
        reloadLessons(1)
    }
}

/** Изменение режима отображения */
const updateViewMode = (value) => {
    viewMode.value = value || 'grid'
    frontendCurrentPage.value = 1

    if (props.useServerProcessing) {
        reloadLessons(1)
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

    reloadLessons(safePage)
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

/** Итоговый список уроков */
const displayedLessons = computed(() => {
    return props.useServerProcessing
        ? lessonsData.value
        : frontendPaginatedLessons.value
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
        <title>{{ seo?.title || t('lessons') }}</title>

        <meta name="title" :content="seo?.title || t('lessons')" />
        <meta name="keywords" :content="seo?.keywords || ''" />
        <meta name="description" :content="seo?.description || t('lessons')" />

        <meta property="og:title" :content="seo?.title || t('lessons')" />
        <meta property="og:description" :content="seo?.description || t('lessons')" />
        <meta property="og:type" content="website" />
        <meta property="og:url" :content="`/${locale}/school/lessons`" />
        <meta property="og:image" content="" />
        <meta property="og:locale" :content="locale === 'ru' ? 'ru_RU' : locale" />

        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="seo?.title || t('lessons')" />
        <meta name="twitter:description" :content="seo?.description || t('lessons')" />
        <meta name="twitter:image" content="" />

        <meta name="DC.title" :content="seo?.title || t('lessons')" />
        <meta name="DC.description" :content="seo?.description || t('lessons')" />
        <meta name="DC.identifier" :content="`/${locale}/school/lessons`" />
        <meta name="DC.language" :content="locale" />
    </Head>

    <DefaultLayout :title="title" :can-login="canLogin" :can-register="canRegister">
        <Navbar />

        <div class="min-h-screen px-3 max-w-full">
            <main class="mx-auto flex flex-col lg:flex-row gap-4 tracking-wider">
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

                <div class="w-full lg:mt-28 pb-6 slate-1">
                    <div class="mx-auto max-w-6xl">

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
                                    {{ t('lessons') }}
                                </li>
                            </ol>
                        </nav>

                        <div class="my-3 flex flex-wrap items-center justify-center gap-3 title">
                            <svg class="shrink-0 h-6 w-6 text-slate-600/85 dark:text-slate-200/85"
                                 fill="currentColor"
                                 viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                            </svg>
                            <h1 class="text-2xl font-bold">
                                {{ t('lessons') }}
                            </h1>
                        </div>

                        <div class="my-1 text-sm subtitle text-center">
                            Выберите урок и продолжайте обучение в удобном формате.
                        </div>

                        <EntityPageToolbar
                            v-if="useServerProcessing"
                            v-model="q"
                            :found="lessonsFound"
                            :view-mode="viewMode"
                            :sort-value="sort"
                            :sort-options="lessonSortOptions"
                            :default-sort="DEFAULT_SORT"
                            :found-label="t('lessons')"
                            :search-placeholder="t('searchByName')"
                            @submit="submitSearch"
                            @reset="resetSearch"
                            @update:viewMode="updateViewMode"
                            @update:sortValue="updateSort"
                        />

                        <FrontendEntityPageToolbar
                            v-else
                            v-model="q"
                            :found="sortedLessons.length"
                            :view-mode="viewMode"
                            :sort-value="sort"
                            :sort-options="lessonSortOptions"
                            :default-sort="DEFAULT_SORT"
                            :found-label="t('lessons')"
                            :search-placeholder="t('searchByName')"
                            @reset="resetSearch"
                            @update:viewMode="updateViewMode"
                            @update:sortValue="updateSort"
                        />

                        <div ref="scrollTarget"></div>

                        <div
                            v-if="displayedLessons.length === 0"
                            class="mt-6 text-center text-slate-700 dark:text-slate-300"
                        >
                            {{ t('noData') }}
                        </div>

                        <div v-else>
                            <LessonGrid
                                v-if="viewMode === 'grid'"
                                :lessons="displayedLessons"
                                :cols="gridCols"
                            />

                            <LessonRows
                                v-else
                                :lessons="displayedLessons"
                            />
                        </div>

                        <Pagination
                            v-if="useServerProcessing"
                            :current-page="currentPage"
                            :last-page="lastPage"
                            :found="lessonsFound"
                            @prev="goPrev"
                            @next="goNext"
                            @go="goToPage"
                        />

                        <FrontendPagination
                            v-else
                            v-model:currentPage="frontendCurrentPage"
                            :items-per-page="perPage"
                            :total-items="sortedLessons.length"
                        />

                        <SectionVideoList :videos="mainVideosList" />
                        <SectionBanners :banners="mainBannersList" />
                    </div>
                </div>

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
            setting-key="publicSchoolLessonsProcessingMode"
            :mode="publicSchoolLessonsProcessingMode"
            :use-server-processing="useServerProcessing"
            :total="lessonsCount"
        />
    </DefaultLayout>
</template>
