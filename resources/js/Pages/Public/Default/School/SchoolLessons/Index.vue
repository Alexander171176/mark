<script setup>
/**
 * Страница списка уроков
 * - расширенное SEO;
 * - Open Graph;
 * - Twitter Cards;
 * - Dublin Core;
 * - Schema.org CollectionPage;
 * - Schema.org BreadcrumbList;
 * - Schema.org ItemList.
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
            description: ''
        })
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
    mainBanners: { type: [Array, Object], default: () => [] }
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

/** Опции публичной сортировки */
const lessonSortOptions = [
    { value: 'idDesc', label: t('idDesc') },
    { value: 'idAsc', label: t('idAsc') },

    { value: 'sortAsc', label: `${t('sortNumber')} 0→9` },
    { value: 'sortDesc', label: `${t('sortNumber')} 9→0` },

    { value: 'titleAsc', label: `${t('title')} A→Z` },
    { value: 'titleDesc', label: `${t('title')} Z→A` },

    { value: 'difficultyDesc', label: `${t('sortDifficulty')} 9→0` },
    { value: 'difficultyAsc', label: `${t('sortDifficulty')} 0→9` },

    { value: 'durationDesc', label: `${t('duration')} 9→0` },
    { value: 'durationAsc', label: `${t('duration')} 0→9` },

    { value: 'viewsDesc', label: `${t('views')} 9→0` },
    { value: 'viewsAsc', label: `${t('views')} 0→9` },

    { value: 'likesDesc', label: `${t('likes')} 9→0` },
    { value: 'likesAsc', label: `${t('likes')} 0→9` },

    { value: 'popularityDesc', label: `${t('popularity')} 9→0` },
    { value: 'popularityAsc', label: `${t('popularity')} 0→9` },

    { value: 'ratingCountDesc', label: `${t('ratingCount')} 9→0` },
    { value: 'ratingCountAsc', label: `${t('ratingCount')} 0→9` },

    { value: 'ratingAvgDesc', label: `${t('ratingAvg')} 9→0` },
    { value: 'ratingAvgAsc', label: `${t('ratingAvg')} 0→9` },

    { value: 'publishedAtDesc', label: `${t('publishedAt')} ↓` },
    { value: 'publishedAtAsc', label: `${t('publishedAt')} ↑` }
]

/* ===================== FRONTEND MODE ===================== */

/** Текущая frontend-страница */
const frontendCurrentPage = ref(1)

/** Плавный скролл к списку */
const {
    targetRef: scrollTarget,
    scrollToTarget
} = useSmoothScrollTo({
    offset: 80,
    duration: 1200
})

/** Нормализация текста */
const normalizeText = (value) => {
    return String(value ?? '')
        .toLowerCase()
        .trim()
}

/** Название урока */
const getLessonTitle = (lesson) => {
    return lesson?.translation?.title || ''
}

/** Краткий текст урока */
const getLessonShort = (lesson) => {
    return lesson?.translation?.short || ''
}

/**
 * Frontend-поиск.
 *
 * Повторяет публичный контракт
 * SchoolLesson::publicSearch().
 */
const filteredLessons = computed(() => {
    const term = normalizeText(q.value)

    if (!term) {
        return lessonsData.value
    }

    return lessonsData.value.filter((lesson) => {
        return [
            lesson.id,
            lesson.slug,
            getLessonTitle(lesson),
            getLessonShort(lesson)
        ].some((value) => {
            return normalizeText(value).includes(term)
        })
    })
})

/** Локальная публичная сортировка */
const sortedLessons = computed(() => {
    const list = [...filteredLessons.value]

    return list.sort((a, b) => {
        switch (sort.value) {
            case 'idAsc':
                return (a.id ?? 0) - (b.id ?? 0)

            case 'idDesc':
                return (b.id ?? 0) - (a.id ?? 0)

            case 'sortAsc':
                return (a.sort ?? 0) - (b.sort ?? 0)
                    || (b.id ?? 0) - (a.id ?? 0)

            case 'sortDesc':
                return (b.sort ?? 0) - (a.sort ?? 0)
                    || (b.id ?? 0) - (a.id ?? 0)

            case 'titleAsc':
                return normalizeText(getLessonTitle(a))
                        .localeCompare(
                            normalizeText(getLessonTitle(b))
                        )
                    || (b.id ?? 0) - (a.id ?? 0)

            case 'titleDesc':
                return normalizeText(getLessonTitle(b))
                        .localeCompare(
                            normalizeText(getLessonTitle(a))
                        )
                    || (b.id ?? 0) - (a.id ?? 0)

            case 'difficultyAsc':
                return (a.difficulty ?? 0) - (b.difficulty ?? 0)
                    || (b.id ?? 0) - (a.id ?? 0)

            case 'difficultyDesc':
                return (b.difficulty ?? 0) - (a.difficulty ?? 0)
                    || (b.id ?? 0) - (a.id ?? 0)

            case 'durationAsc':
                return (a.duration ?? 0) - (b.duration ?? 0)
                    || (b.id ?? 0) - (a.id ?? 0)

            case 'durationDesc':
                return (b.duration ?? 0) - (a.duration ?? 0)
                    || (b.id ?? 0) - (a.id ?? 0)

            case 'viewsAsc':
                return (a.views ?? 0) - (b.views ?? 0)
                    || (b.id ?? 0) - (a.id ?? 0)

            case 'viewsDesc':
                return (b.views ?? 0) - (a.views ?? 0)
                    || (b.id ?? 0) - (a.id ?? 0)

            case 'likesAsc':
                return (a.likes_count ?? 0) - (b.likes_count ?? 0)
                    || (b.id ?? 0) - (a.id ?? 0)

            case 'likesDesc':
                return (b.likes_count ?? 0) - (a.likes_count ?? 0)
                    || (b.id ?? 0) - (a.id ?? 0)

            case 'popularityAsc':
                return (a.popularity ?? 0) - (b.popularity ?? 0)
                    || (b.id ?? 0) - (a.id ?? 0)

            case 'popularityDesc':
                return (b.popularity ?? 0) - (a.popularity ?? 0)
                    || (b.id ?? 0) - (a.id ?? 0)

            case 'ratingCountAsc':
                return (a.rating_count ?? 0) - (b.rating_count ?? 0)
                    || (b.id ?? 0) - (a.id ?? 0)

            case 'ratingCountDesc':
                return (b.rating_count ?? 0) - (a.rating_count ?? 0)
                    || (b.id ?? 0) - (a.id ?? 0)

            case 'ratingAvgAsc':
                return (a.rating_avg ?? 0) - (b.rating_avg ?? 0)
                    || (b.id ?? 0) - (a.id ?? 0)

            case 'ratingAvgDesc':
                return (b.rating_avg ?? 0) - (a.rating_avg ?? 0)
                    || (b.id ?? 0) - (a.id ?? 0)

            case 'publishedAtAsc':
            case 'dateAsc':
                return new Date(a.published_at ?? 0)
                    - new Date(b.published_at ?? 0)
                    || (b.id ?? 0) - (a.id ?? 0)

            case 'publishedAtDesc':
            case 'dateDesc':
                return new Date(b.published_at ?? 0)
                    - new Date(a.published_at ?? 0)
                    || (b.id ?? 0) - (a.id ?? 0)

            default:
                return (a.sort ?? 0) - (b.sort ?? 0)
                    || (b.id ?? 0) - (a.id ?? 0)
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
            page
        },
        {
            preserveState: true,
            replace: true,
            preserveScroll: true
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

/* ===================== SEO ===================== */

/**
 * SEO-заголовок.
 */
const seoTitle = computed(() => {
    return String(
        props.seo?.title
        || t('lessons')
    ).trim()
})

/**
 * SEO-описание.
 */
const seoDescription = computed(() => {
    return String(
        props.seo?.description
        || t('lessons')
    ).trim()
})

/**
 * SEO keywords.
 */
const seoKeywords = computed(() => {
    return String(
        props.seo?.keywords
        || ''
    ).trim()
})

/**
 * Open Graph locale без жёсткого списка языков.
 */
const ogLocale = computed(() => {
    const locale = String(
        props.locale || ''
    ).trim().replace('_', '-')

    if (!locale) {
        return undefined
    }

    try {
        const normalized =
            new Intl.Locale(locale).maximize()

        return normalized.region
            ? `${normalized.language}_${normalized.region}`
            : normalized.language
    } catch {
        return locale.replace('-', '_')
    }
})

/**
 * Текущая страница списка независимо
 * от server/frontend режима.
 */
const seoCurrentPage = computed(() => {
    return props.useServerProcessing
        ? currentPage.value
        : frontendCurrentPage.value
})

/**
 * Базовый путь каталога.
 *
 * q/sort/view не являются частью canonical.
 */
const canonicalPath = computed(() => {
    const base =
        `/${props.locale}/school/lessons`

    return seoCurrentPage.value > 1
        ? `${base}?page=${seoCurrentPage.value}`
        : base
})

/**
 * Абсолютный canonical URL.
 */
const canonicalUrl = computed(() => {
    if (typeof window === 'undefined') {
        return canonicalPath.value
    }

    return new URL(
        canonicalPath.value,
        window.location.origin
    ).toString()
})

/**
 * Абсолютный URL главной страницы.
 */
const homeUrl = computed(() => {
    if (typeof window === 'undefined') {
        return route('home')
    }

    return new URL(
        route('home'),
        window.location.origin
    ).toString()
})

/**
 * Абсолютный URL конкретного урока.
 */
const getAbsoluteLessonUrl = (lesson) => {
    const url = route(
        'public.schoolLessons.show',
        {
            slug: lesson.slug
        }
    )

    if (typeof window === 'undefined') {
        return url
    }

    return new URL(
        url,
        window.location.origin
    ).toString()
}

/**
 * Имя сайта.
 */
const siteName = computed(() => {
    return String(
        siteSettings?.siteName
        || siteSettings?.SiteName
        || ''
    ).trim()
})

/**
 * robots.
 *
 * Поисковую выдачу не индексируем.
 * Обычный каталог и пагинацию индексируем.
 */
const robotsContent = computed(() => {
    return q.value.trim()
        ? 'noindex, follow'
        : 'index, follow'
})

/**
 * JSON-LD: CollectionPage.
 */
const collectionPageSchema = computed(() => ({
    '@context': 'https://schema.org',
    '@type': 'CollectionPage',
    '@id': `${canonicalUrl.value}#webpage`,
    url: canonicalUrl.value,
    name: seoTitle.value,
    description: seoDescription.value,
    inLanguage: props.locale,
    isPartOf: {
        '@type': 'WebSite',
        url: homeUrl.value,
        ...(siteName.value
            ? { name: siteName.value }
            : {})
    }
}))

/**
 * JSON-LD: BreadcrumbList.
 */
const breadcrumbSchema = computed(() => ({
    '@context': 'https://schema.org',
    '@type': 'BreadcrumbList',
    itemListElement: [
        {
            '@type': 'ListItem',
            position: 1,
            name: t('home'),
            item: homeUrl.value
        },
        {
            '@type': 'ListItem',
            position: 2,
            name: seoTitle.value,
            item: canonicalUrl.value
        }
    ]
}))

/**
 * JSON-LD: ItemList уроков,
 * реально отображаемых на текущей странице.
 */
const lessonItemListSchema = computed(() => ({
    '@context': 'https://schema.org',
    '@type': 'ItemList',
    name: seoTitle.value,
    numberOfItems: displayedLessons.value.length,

    itemListElement:
        displayedLessons.value.map(
            (lesson, index) => {
                const lessonUrl =
                    getAbsoluteLessonUrl(lesson)

                const item = {
                    '@type': 'LearningResource',
                    '@id':
                        `${lessonUrl}#learning-resource`,
                    url: lessonUrl,
                    name:
                        getLessonTitle(lesson)
                        || t('lesson'),
                    learningResourceType: 'Lesson',
                    inLanguage:
                        lesson?.translation?.locale
                        || props.locale
                }

                if (getLessonShort(lesson)) {
                    item.description =
                        getLessonShort(lesson)
                }

                const duration =
                    Number(lesson?.duration)

                if (
                    Number.isFinite(duration)
                    && duration > 0
                ) {
                    item.timeRequired =
                        `PT${Math.round(duration)}M`
                }

                if (
                    lesson?.difficulty !== null
                    && lesson?.difficulty !== undefined
                ) {
                    item.educationalLevel =
                        String(lesson.difficulty)
                }

                if (lesson?.published_at) {
                    item.datePublished =
                        lesson.published_at
                }

                return {
                    '@type': 'ListItem',

                    position:
                        (
                            (
                                seoCurrentPage.value - 1
                            ) * perPage.value
                        )
                        + index
                        + 1,

                    url: lessonUrl,
                    item
                }
            }
        )
}))

const collectionPageJsonLd = computed(() => {
    return JSON.stringify(
        collectionPageSchema.value
    )
})

const breadcrumbJsonLd = computed(() => {
    return JSON.stringify(
        breadcrumbSchema.value
    )
})

const lessonItemListJsonLd = computed(() => {
    return JSON.stringify(
        lessonItemListSchema.value
    )
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
        <!-- Основные -->
        <title>{{ seoTitle }}</title>

        <meta
            name="title"
            :content="seoTitle"
        />

        <meta
            name="description"
            :content="seoDescription"
        />

        <meta
            v-if="seoKeywords"
            name="keywords"
            :content="seoKeywords"
        />

        <meta
            name="robots"
            :content="robotsContent"
        />

        <meta
            name="googlebot"
            :content="robotsContent"
        />

        <link
            rel="canonical"
            :href="canonicalUrl"
        />

        <!-- Open Graph -->
        <meta
            property="og:title"
            :content="seoTitle"
        />

        <meta
            property="og:description"
            :content="seoDescription"
        />

        <meta
            property="og:type"
            content="website"
        />

        <meta
            property="og:url"
            :content="canonicalUrl"
        />

        <meta
            v-if="ogLocale"
            property="og:locale"
            :content="ogLocale"
        />

        <meta
            v-if="siteName"
            property="og:site_name"
            :content="siteName"
        />

        <!-- Twitter -->
        <meta
            name="twitter:card"
            content="summary"
        />

        <meta
            name="twitter:title"
            :content="seoTitle"
        />

        <meta
            name="twitter:description"
            :content="seoDescription"
        />

        <!-- Dublin Core -->
        <meta
            name="DC.Title"
            :content="seoTitle"
        />

        <meta
            name="DC.Description"
            :content="seoDescription"
        />

        <meta
            v-if="seoKeywords"
            name="DC.Subject"
            :content="seoKeywords"
        />

        <meta
            name="DC.Type"
            content="Collection"
        />

        <meta
            name="DC.Format"
            content="text/html"
        />

        <meta
            name="DC.Language"
            :content="locale"
        />

        <meta
            name="DC.Identifier"
            :content="canonicalUrl"
        />

        <!-- Schema.org -->
        <component
            :is="'script'"
            type="application/ld+json"
            v-text="collectionPageJsonLd"
        />

        <component
            :is="'script'"
            type="application/ld+json"
            v-text="breadcrumbJsonLd"
        />

        <component
            :is="'script'"
            type="application/ld+json"
            v-text="lessonItemListJsonLd"
        />
    </Head>

    <DefaultLayout :title="title" :can-login="canLogin" :can-register="canRegister">
        <Navbar />

        <main class="min-h-screen px-1 lg:px-6 max-w-full">
            <div
                class="mx-auto tracking-wider pt-20 lg:pt-44"
            >
                <div
                    class="ext-color w-full min-w-0 py-3 px-1
                           flex flex-col lg:flex-row gap-4 rounded-3xl
                           border-2 border-slate-300 dark:border-slate-500"
                >
                    <aside
                        v-if="showLeft"
                        class="shrink-0 transition-all duration-300"
                        :class="leftCollapsed ? 'lg:w-6' : 'lg:w-72'"
                    >
                        <LeftSidebarSchool
                            :track-tree="trackTree"
                            :collapsed="leftCollapsed"
                            @collapsed="leftCollapsed = $event"
                        />
                    </aside>

                    <div class="w-full pb-6 slate-1">
                        <div class="mx-auto max-w-6xl">

                            <nav
                                class="text-sm"
                                aria-label="Breadcrumb"
                                itemscope
                                itemtype="https://schema.org/BreadcrumbList"
                            >
                                <ol class="flex flex-wrap items-center font-semibold">
                                    <li
                                        itemprop="itemListElement"
                                        itemscope
                                        itemtype="https://schema.org/ListItem"
                                    >
                                        <Link
                                            :href="route('home')"
                                            class="breadcrumb-link hover:underline"
                                            itemprop="item"
                                        >
                                        <span itemprop="name">
                                            {{ t('home') }}
                                        </span>
                                        </Link>

                                        <meta
                                            itemprop="position"
                                            content="1"
                                        />
                                    </li>

                                    <li>
                                    <span class="mx-2 breadcrumbs">
                                        /
                                    </span>
                                    </li>

                                    <li
                                        class="breadcrumbs"
                                        itemprop="itemListElement"
                                        itemscope
                                        itemtype="https://schema.org/ListItem"
                                    >
                                    <span itemprop="name">
                                        {{ t('lessons') }}
                                    </span>

                                        <meta
                                            itemprop="position"
                                            content="2"
                                        />

                                        <meta
                                            itemprop="item"
                                            :content="canonicalUrl"
                                        />
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
                                    {{ seoTitle }}
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
                        class="shrink-0 transition-all duration-300"
                        :class="rightCollapsed ? 'lg:w-6' : 'lg:w-72'"
                    >
                        <RightSidebarSchool
                            :collapsed="rightCollapsed"
                            @collapsed="rightCollapsed = $event"
                        />
                    </aside>
                </div>
            </div>
        </main>

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
