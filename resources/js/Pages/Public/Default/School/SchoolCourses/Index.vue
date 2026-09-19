<script setup>
/**
 * Публичная страница списка курсов.
 *
 * Возможности:
 * - server / frontend / auto обработка;
 * - поиск;
 * - сортировка;
 * - пагинация;
 * - grid / rows отображение;
 * - боковые колонки;
 * - дерево треков;
 * - хештеги;
 * - видео;
 * - баннеры;
 * - расширенное SEO;
 * - Open Graph;
 * - Twitter Cards;
 * - Dublin Core;
 * - Schema.org JSON-LD.
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
import CourseGrid
    from '@/Components/Public/Default/School/SchoolCourse/CourseGrid.vue'
import CourseRows
    from '@/Components/Public/Default/School/SchoolCourse/CourseRows.vue'
import PublicAdminBottomPanel
    from '@/Components/Admin/UI/PublicAdminPanel/PublicAdminBottomPanel.vue'

const { t } = useI18n()

/* ===================== PROPS ===================== */

const props = defineProps({
    locale: {
        type: String,
        default: 'ru'
    },

    seo: {
        type: Object,
        default: () => ({
            title: '',
            keywords: '',
            description: ''
        })
    },

    useServerProcessing: {
        type: Boolean,
        default: false
    },

    publicSchoolCoursesProcessingMode: {
        type: String,
        default: 'server'
    },

    title: {
        type: String,
        default: ''
    },

    canLogin: {
        type: Boolean,
        default: false
    },

    canRegister: {
        type: Boolean,
        default: false
    },

    trackTree: {
        type: Array,
        default: () => []
    },

    courses: {
        type: [Array, Object],
        default: () => []
    },

    coursesCount: {
        type: Number,
        default: 0
    },

    coursesFound: {
        type: Number,
        default: 0
    },

    filters: {
        type: Object,
        default: () => ({})
    },

    hashtags: {
        type: Array,
        default: () => []
    },

    mainVideos: {
        type: [Array, Object],
        default: () => []
    },

    mainBanners: {
        type: [Array, Object],
        default: () => []
    }
})

/* ===================== PAGE ===================== */

const page = usePage()

const siteSettings = page.props?.siteSettings || {}

const isAdmin = computed(() => {
    return page.props?.isAdmin === true
})

const trackTree = computed(() => {
    return Array.isArray(props.trackTree)
        ? props.trackTree
        : []
})

/* ===================== COURSES DATA ===================== */

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

const showLeft = computed(() => {
    return !siteSettings?.ViewLeftColumn
        || siteSettings.ViewLeftColumn === 'true'
})

const showRight = computed(() => {
    return !siteSettings?.ViewRightColumn
        || siteSettings.ViewRightColumn === 'true'
})

const LEFT_SIDEBAR_KEY = 'public_left_sidebar_collapsed'
const RIGHT_SIDEBAR_KEY = 'public_right_sidebar_collapsed'

const getStoredBoolean = (key, defaultValue = true) => {
    const value = localStorage.getItem(key)

    if (value === null) {
        return defaultValue
    }

    return value === 'true'
}

const leftCollapsed = ref(
    getStoredBoolean(LEFT_SIDEBAR_KEY, true)
)

const rightCollapsed = ref(
    getStoredBoolean(RIGHT_SIDEBAR_KEY, true)
)

/**
 * Количество колонок карточек.
 *
 * 2 — оба сайдбара открыты.
 * 3 — открыт один.
 * 4 — оба свернуты.
 */
const gridCols = computed(() => {
    const leftExpanded =
        showLeft.value && !leftCollapsed.value

    const rightExpanded =
        showRight.value && !rightCollapsed.value

    if (leftExpanded && rightExpanded) {
        return 2
    }

    if (leftExpanded || rightExpanded) {
        return 3
    }

    return 4
})

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

const q = ref(
    String(props.filters?.q ?? '')
)

const DEFAULT_SORT = 'idDesc'

const sort = ref(
    String(props.filters?.sort ?? DEFAULT_SORT)
)

const VIEW_KEY = 'public_school_courses_view'

const viewMode = ref(
    String(
        props.filters?.view
        || localStorage.getItem(VIEW_KEY)
        || 'grid'
    )
)

watch(viewMode, (value) => {
    localStorage.setItem(VIEW_KEY, value)
})

const perPage = computed(() => {
    const value = Number(props.filters?.per_page)

    return Number.isFinite(value) && value > 0
        ? value
        : 12
})

/**
 * Public-сортировки.
 *
 * Полностью соответствуют:
 * - SchoolCourse::publicSortByParam();
 * - SchoolCourseSharedResource;
 * - frontend sortedCourses.
 */
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

    { value: 'modulesDesc', label: `${t('modules')} 9→0` },
    { value: 'modulesAsc', label: `${t('modules')} 0→9` },

    { value: 'lessonsDesc', label: `${t('lessons')} 9→0` },
    { value: 'lessonsAsc', label: `${t('lessons')} 0→9` },

    { value: 'tracksDesc', label: `${t('tracks')} 9→0` },
    { value: 'tracksAsc', label: `${t('tracks')} 0→9` },

    { value: 'hashtagsDesc', label: `${t('hashtags')} 9→0` },
    { value: 'hashtagsAsc', label: `${t('hashtags')} 0→9` },

    { value: 'reviewsDesc', label: `${t('reviews')} 9→0` },
    { value: 'reviewsAsc', label: `${t('reviews')} 0→9` },

    { value: 'publishedAtDesc', label: `${t('publishedAt')} ↓` },
    { value: 'publishedAtAsc', label: `${t('publishedAt')} ↑` }
]

/* ===================== FRONTEND MODE ===================== */

const frontendCurrentPage = ref(1)

const {
    targetRef: scrollTarget,
    scrollToTarget
} = useSmoothScrollTo({
    offset: 80,
    duration: 1200
})

const normalizeText = (value) => {
    return String(value ?? '').toLowerCase()
}

const getCourseTitle = (course) => {
    return course?.translation?.title || ''
}

const getCourseShort = (course) => {
    return course?.translation?.short || ''
}

const getCourseSlug = (course) => {
    return course?.slug || ''
}

/**
 * Локальный Public-поиск.
 *
 * Полностью соответствует SchoolCourse::publicSearch():
 * - translation.title;
 * - translation.short;
 * - slug;
 * - instructorProfile.translation.title;
 * - instructorProfile.user.name.
 */
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
                ?.name
        ].some((value) =>
            normalizeText(value).includes(query)
        )
    })
})

/**
 * Локальная Public-сортировка.
 */
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
                    .localeCompare(
                        normalizeText(getCourseTitle(b))
                    )

            case 'titleDesc':
                return normalizeText(getCourseTitle(b))
                    .localeCompare(
                        normalizeText(getCourseTitle(a))
                    )

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
                    .localeCompare(
                        normalizeText(b.level)
                    )

            case 'levelDesc':
                return normalizeText(b.level)
                    .localeCompare(
                        normalizeText(a.level)
                    )

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

            default:
                return 0
        }
    })
})

const frontendPaginatedCourses = computed(() => {
    const start = (
        frontendCurrentPage.value - 1
    ) * perPage.value

    return sortedCourses.value.slice(
        start,
        start + perPage.value
    )
})

watch([q, sort, viewMode], () => {
    frontendCurrentPage.value = 1
})

watch(frontendCurrentPage, () => {
    if (!props.useServerProcessing) {
        scrollToTarget()
    }
})

/* ===================== SERVER MODE ===================== */

const currentPage = computed(() => {
    return Number(
        props.courses?.meta?.current_page
        ?? props.courses?.current_page
        ?? 1
    ) || 1
})

const lastPage = computed(() => {
    return Number(
        props.courses?.meta?.last_page
        ?? props.courses?.last_page
        ?? 1
    ) || 1
})

const indexRoute = () => {
    return route('public.schoolCourses.index')
}

const reloadCourses = (page = 1) => {
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

const submitSearch = () => {
    reloadCourses(1)
}

const resetSearch = () => {
    q.value = ''
    sort.value = DEFAULT_SORT
    frontendCurrentPage.value = 1

    if (props.useServerProcessing) {
        reloadCourses(1)
    }
}

const updateSort = (value) => {
    sort.value = value || DEFAULT_SORT

    if (props.useServerProcessing) {
        reloadCourses(1)
    }
}

const updateViewMode = (value) => {
    viewMode.value = value || 'grid'
    frontendCurrentPage.value = 1

    if (props.useServerProcessing) {
        reloadCourses(1)
    }
}

const goToPage = (pageNumber) => {
    const value = Number(pageNumber)

    if (!Number.isFinite(value)) {
        return
    }

    const safePage = Math.max(
        1,
        Math.min(value, lastPage.value)
    )

    reloadCourses(safePage)
}

const goPrev = () => {
    if (currentPage.value <= 1) {
        return
    }

    goToPage(currentPage.value - 1)
}

const goNext = () => {
    if (currentPage.value >= lastPage.value) {
        return
    }

    goToPage(currentPage.value + 1)
}

/* ===================== COMMON VIEW ===================== */

const displayedCourses = computed(() => {
    return props.useServerProcessing
        ? coursesData.value
        : frontendPaginatedCourses.value
})

/* ===================== SEO ===================== */

/**
 * SEO-заголовок.
 */
const seoTitle = computed(() => {
    return String(
        props.seo?.title
        || t('courses')
    ).trim()
})

/**
 * SEO-описание.
 */
const seoDescription = computed(() => {
    return String(
        props.seo?.description
        || t('courses')
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
 *
 * Intl.Locale динамически определяет регион для любой
 * добавленной локали: ru -> ru_RU, en -> en_US,
 * de -> de_DE и т.д. Если окружение не поддерживает
 * maximize(), используем переданную приложением локаль.
 */
const ogLocale = computed(() => {
    const locale = String(props.locale || '').trim().replace('_', '-')

    if (!locale) {
        return undefined
    }

    try {
        const normalized = new Intl.Locale(locale).maximize()

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
 * Фильтры q/sort/view не являются частью canonical.
 */
const canonicalPath = computed(() => {
    const base = `/${props.locale}/school/courses`

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
        return '/'
    }

    return new URL(
        route('home'),
        window.location.origin
    ).toString()
})

/**
 * Абсолютный URL конкретного курса.
 */
const getAbsoluteCourseUrl = (course) => {
    const url = route(
        'public.schoolCourses.show',
        {
            slug: course.slug
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
 *
 * Используем существующую глобальную настройку,
 * если она определена. Иначе SEO остаётся
 * корректным без og:site_name.
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
 * Поиск является пользовательским состоянием,
 * поэтому результаты поиска не индексируем.
 * Обычный каталог и пагинация индексируются.
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
 * JSON-LD: ItemList курсов,
 * реально отображаемых на текущей странице.
 */
const courseItemListSchema = computed(() => ({
    '@context': 'https://schema.org',
    '@type': 'ItemList',
    name: seoTitle.value,
    numberOfItems: displayedCourses.value.length,
    itemListElement: displayedCourses.value.map(
        (course, index) => {
            const courseUrl =
                getAbsoluteCourseUrl(course)

            const item = {
                '@type': 'Course',
                '@id': `${courseUrl}#course`,
                url: courseUrl,
                name: getCourseTitle(course),
                inLanguage: props.locale
            }

            if (getCourseShort(course)) {
                item.description =
                    getCourseShort(course)
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
                url: courseUrl,
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

const courseItemListJsonLd = computed(() => {
    return JSON.stringify(
        courseItemListSchema.value
    )
})
</script>

<template>
    <!-- ===================== SEO ===================== -->

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
            name="DC.title"
            :content="seoTitle"
        />

        <meta
            name="DC.description"
            :content="seoDescription"
        />

        <meta
            v-if="seoKeywords"
            name="DC.subject"
            :content="seoKeywords"
        />

        <meta
            name="DC.identifier"
            :content="canonicalUrl"
        />

        <meta
            name="DC.language"
            :content="locale"
        />

        <meta
            name="DC.type"
            content="Collection"
        />

        <meta
            name="DC.format"
            content="text/html"
        />

        <meta
            v-if="siteName"
            name="DC.publisher"
            :content="siteName"
        />

        <!-- JSON-LD -->
        <component
            :is="'script'"
            type="application/ld+json"
            v-html="collectionPageJsonLd"
        />

        <component
            :is="'script'"
            type="application/ld+json"
            v-html="breadcrumbJsonLd"
        />

        <component
            :is="'script'"
            type="application/ld+json"
            v-html="courseItemListJsonLd"
        />
    </Head>

    <DefaultLayout
        :title="title"
        :can-login="canLogin"
        :can-register="canRegister"
    >
        <!-- Шапка -->
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

                    <!-- Левая колонка -->
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

                    <!-- Центральный SEO-контент страницы. Сайдбары намеренно находятся вне CollectionPage. -->
                    <article
                        class="w-full pb-6 slate-1"
                        itemscope
                        itemtype="https://schema.org/CollectionPage"
                    >
                        <div class="mx-auto max-w-6xl">

                            <!-- Хлебные крошки -->
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

                                    <li aria-hidden="true">
                                    <span class="mx-2 breadcrumbs">
                                        /
                                    </span>
                                    </li>

                                    <li
                                        class="breadcrumbs"
                                        itemprop="itemListElement"
                                        itemscope
                                        itemtype="https://schema.org/ListItem"
                                        aria-current="page"
                                    >
                                    <span itemprop="name">
                                        {{ t('courses') }}
                                    </span>

                                        <meta
                                            itemprop="item"
                                            :content="canonicalUrl"
                                        />

                                        <meta
                                            itemprop="position"
                                            content="2"
                                        />
                                    </li>
                                </ol>
                            </nav>

                            <!-- Заголовок -->
                            <div
                                class="my-3 flex flex-wrap items-center
                                   justify-center gap-3 title"
                            >
                                <svg
                                    class="shrink-0 h-5 w-5
                                       text-slate-600/85
                                       dark:text-slate-200/85"
                                    fill="currentColor"
                                    viewBox="0 0 448 512"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M318.38 208h-39.09c-1.49 27.03-6.54 51.35-14.21 70.41 27.71-13.24 48.02-39.19 53.3-70.41zm0-32c-5.29-31.22-25.59-57.17-53.3-70.41 7.68 19.06 12.72 43.38 14.21 70.41h39.09zM224 97.31c-7.69 7.45-20.77 34.42-23.43 78.69h46.87c-2.67-44.26-15.75-71.24-23.44-78.69zm-41.08 8.28c-27.71 13.24-48.02 39.19-53.3 70.41h39.09c1.49-27.03 6.53-51.35 14.21-70.41zm0 172.82c-7.68-19.06-12.72-43.38-14.21-70.41h-39.09c5.28 31.22 25.59 57.17 53.3 70.41zM247.43 208h-46.87c2.66 44.26 15.74 71.24 23.43 78.69 7.7-7.45 20.78-34.43 23.44-78.69zM448 358.4V25.6c0-16-9.6-25.6-25.6-25.6H96C41.6 0 0 41.6 0 96v320c0 54.4 41.6 96 96 96h326.4c12.8 0 25.6-9.6 25.6-25.6v-16c0-6.4-3.2-12.8-9.6-19.2-3.2-16-3.2-60.8 0-73.6 6.4-3.2 9.6-9.6 9.6-19.2zM224 64c70.69 0 128 57.31 128 128s-57.31 128-128 128S96 262.69 96 192 153.31 64 224 64zm160 384H96c-19.2 0-32-12.8-32-32s16-32 32-32h288v64z"
                                    />
                                </svg>

                                <h1
                                    class="text-2xl font-bold"
                                    itemprop="name"
                                >
                                    {{ t('courses') }}
                                </h1>
                            </div>

                            <!-- SEO-описание страницы -->
                            <div
                                v-if="seoDescription"
                                class="my-1 text-sm subtitle text-center"
                                itemprop="description"
                            >
                                {{ seoDescription }}
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
                                class="mt-6 text-center
                                   text-slate-700 dark:text-slate-300"
                            >
                                {{ t('noData') }}
                            </div>

                            <!-- Курсы -->
                            <section
                                v-else
                                :aria-label="t('courses')"
                                itemprop="mainEntity"
                            >
                                <CourseGrid
                                    v-if="viewMode === 'grid'"
                                    :courses="displayedCourses"
                                    :cols="gridCols"
                                />

                                <CourseRows
                                    v-else
                                    :courses="displayedCourses"
                                />
                            </section>

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

                            <!-- Главные видео и баннеры -->
                            <SectionVideoList
                                :videos="mainVideos"
                            />

                            <SectionBanners
                                :banners="mainBanners"
                            />
                        </div>
                    </article>

                    <!-- Правая колонка -->
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

        <!-- Подвал -->
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
