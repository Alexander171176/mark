<script setup>
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
import Pagination from '@/Components/Public/Default/Pagination/Pagination.vue'
import FrontendPagination from '@/Components/Public/Default/Pagination/FrontendPagination.vue'

import SectionVideoList from '@/Components/Public/Default/Blog/BlogVideo/SectionVideoList.vue'
import SectionBanners from '@/Components/Public/Default/Blog/BlogBanner/SectionBanners.vue'
import ImageGalleryMain from '@/Components/Public/Default/Media/ImageGalleryMain.vue'

import InstructorCourseGrid from '@/Components/Public/Default/School/SchoolInstructor/InstructorCourseGrid.vue'
import InstructorCourseRows from '@/Components/Public/Default/School/SchoolInstructor/InstructorCourseRows.vue'

import PublicAdminBottomPanel
    from '@/Components/Admin/UI/PublicAdminPanel/PublicAdminBottomPanel.vue'

const { t } = useI18n()
const page = usePage()

const props = defineProps({
    title: String,
    canLogin: Boolean,
    canRegister: Boolean,

    locale: {
        type: String,
        default: 'ru',
    },

    instructorProfile: {
        type: Object,
        default: () => ({}),
    },

    publicSchoolCoursesProcessingMode: {
        type: String,
        default: 'server',
    },

    useServerProcessing: {
        type: Boolean,
        default: false,
    },

    courses: {
        type: [Array, Object],
        default: () => [],
    },

    coursesCount: {
        type: Number,
        default: 0,
    },

    coursesFound: {
        type: Number,
        default: 0,
    },

    filters: {
        type: Object,
        default: () => ({}),
    },

    trackTree: {
        type: Array,
        default: () => [],
    },

    mainVideos: {
        type: [Array, Object],
        default: () => [],
    },

    mainBanners: {
        type: [Array, Object],
        default: () => [],
    },
})

/* ======================== Helpers ======================== */

const normalizeList = (value) => {
    if (Array.isArray(value)) return value
    if (Array.isArray(value?.data)) return value.data

    return []
}

const normalizeText = (value) =>
    String(value ?? '').trim().toLocaleLowerCase()

const safeNumber = (value) => {
    const number = Number(value)

    return Number.isFinite(number)
        ? number
        : 0
}

const safeDate = (value) => {
    const time = value
        ? Date.parse(value)
        : 0

    return Number.isFinite(time)
        ? time
        : 0
}

/* ======================== Instructor ======================== */

const instructor = computed(() =>
    props.instructorProfile ?? {}
)

const translation = computed(() =>
    instructor.value?.translation ?? {}
)

const instructorName = computed(() =>
    translation.value?.title
    || instructor.value?.user?.name
    || t('instructor')
)

const instructorShort = computed(() =>
    translation.value?.short || ''
)

const instructorBio = computed(() =>
    translation.value?.bio || ''
)

const instructorLocale = computed(() =>
    translation.value?.locale
    || props.locale
    || 'ru'
)

/* ======================== Rating ======================== */

const ratingAvg = computed(() =>
    instructor.value?.rating?.avg ?? null
)

const ratingCount = computed(() =>
    safeNumber(
        instructor.value?.rating?.count
    )
)

const formattedRating = computed(() => {
    if (
        ratingAvg.value === null
        || ratingAvg.value === undefined
    ) {
        return '—'
    }

    return Number(
        ratingAvg.value
    ).toFixed(1)
})

/* ======================== Images ======================== */

const instructorImages = computed(() =>
    Array.isArray(instructor.value?.images)
        ? instructor.value.images
        : []
)

const hasInstructorImages = computed(() =>
    instructorImages.value.length > 0
)

const primaryImage = computed(() =>
    instructorImages.value[0] ?? null
)

const getImageUrl = (image) =>
    image?.webp_url
    || image?.image_url
    || image?.thumb_url
    || image?.url
    || image?.preview
    || ''

const seoImage = computed(() =>
    getImageUrl(primaryImage.value)
)

/* ======================== Social links ======================== */

const socialLinks = computed(() =>
    instructor.value?.social_links ?? {}
)

const normalizedSocialLinks = computed(() => {
    if (Array.isArray(socialLinks.value)) {
        return socialLinks.value
            .filter((item) => item?.url)
            .map((item) => ({
                label:
                    item.label
                    || item.name
                    || item.platform
                    || t('link'),
                url: item.url,
            }))
    }

    return Object
        .entries(socialLinks.value || {})
        .filter(([, value]) => Boolean(value))
        .map(([key, value]) => ({
            label: key,
            url: value,
        }))
})

const detectSocialType = (item) => {
    const label = String(
        item?.label || ''
    ).toLowerCase()

    const url = String(
        item?.url || ''
    ).toLowerCase()

    if (
        label.includes('instagram')
        || url.includes('instagram.com')
    ) return 'instagram'

    if (
        label.includes('facebook')
        || url.includes('facebook.com')
    ) return 'facebook'

    if (
        label.includes('telegram')
        || url.includes('t.me')
        || url.includes('telegram.me')
    ) return 'telegram'

    if (
        label.includes('youtube')
        || url.includes('youtube.com')
        || url.includes('youtu.be')
    ) return 'youtube'

    if (
        label.includes('tiktok')
        || url.includes('tiktok.com')
    ) return 'tiktok'

    if (
        label.includes('linkedin')
        || url.includes('linkedin.com')
    ) return 'linkedin'

    if (
        label === 'x'
        || label.includes('twitter')
        || url.includes('twitter.com')
        || url.includes('x.com')
    ) return 'x'

    if (
        label.includes('whatsapp')
        || url.includes('wa.me')
        || url.includes('whatsapp.com')
    ) return 'whatsapp'

    if (
        label === 'vk'
        || label.includes('vkontakte')
        || url.includes('vk.com')
    ) return 'vk'

    if (
        label.includes('site')
        || label.includes('website')
    ) return 'website'

    return 'link'
}

const getSocialLabel = (item) => {
    switch (detectSocialType(item)) {
        case 'instagram':
            return 'Instagram'

        case 'facebook':
            return 'Facebook'

        case 'telegram':
            return 'Telegram'

        case 'youtube':
            return 'YouTube'

        case 'tiktok':
            return 'TikTok'

        case 'linkedin':
            return 'LinkedIn'

        case 'x':
            return 'X'

        case 'whatsapp':
            return 'WhatsApp'

        case 'vk':
            return 'VK'

        case 'website':
            return t('website')

        default:
            return item?.label || t('link')
    }
}

/* ======================== SEO ======================== */

const seoTitle = computed(() =>
    translation.value?.meta_title
    || instructorName.value
)

const seoDescription = computed(() =>
    translation.value?.meta_desc
    || instructorShort.value
    || ''
)

const seoKeywords = computed(() =>
    translation.value?.meta_keywords
    || ''
)

const ogLocale = computed(() =>
    instructorLocale.value === 'ru'
        ? 'ru_RU'
        : instructorLocale.value
)

const canonicalUrl = computed(() => {
    if (!instructor.value?.slug) {
        return ''
    }

    return String(
        route('public.schoolInstructors.show', {
            slug: instructor.value.slug,
        })
    )
})

const dcSubject = computed(() =>
    seoKeywords.value
    || instructorName.value
)

const seoCreatedAt = computed(() =>
    instructor.value?.created_at || ''
)

/* ======================== Courses ======================== */

const coursesData = computed(() =>
    normalizeList(props.courses)
)

const coursesCount = computed(() =>
    safeNumber(props.coursesCount)
)

const hasCourses = computed(() =>
    coursesCount.value > 0
)

const qCourses = ref(
    String(
        props.filters?.q_courses ?? ''
    )
)

const DEFAULT_SORT = 'sortAsc'

const sortCourses = ref(
    String(
        props.filters?.sort_courses
        ?? DEFAULT_SORT
    )
)

/**
 * Значение приходит только от backend-настройки
 * publicSchoolCoursesPerPage.
 */
const perPageCourses = computed(() => {
    const value = Number(
        props.filters?.per_page_courses
    )

    return Number.isFinite(value)
    && value > 0
        ? value
        : 12
})

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
]

const getCourseTitle = (course) =>
    course?.translation?.title || ''

const getCourseShort = (course) =>
    course?.translation?.short || ''

const getCourseInstructorName = (course) =>
    course?.instructorProfile?.translation?.title
    || course?.instructorProfile?.user?.name
    || ''

/* ======================== Frontend search ======================== */

const frontendFilteredCourses = computed(() => {
    if (props.useServerProcessing) {
        return coursesData.value
    }

    const query = normalizeText(
        qCourses.value
    )

    if (!query) {
        return coursesData.value
    }

    return coursesData.value.filter((course) => [
        course?.id,
        course?.slug,
        getCourseTitle(course),
        getCourseShort(course),
        getCourseInstructorName(course),
        course?.level,
        course?.availability,
    ].some((value) =>
        normalizeText(value).includes(query)
    ))
})

/* ======================== Frontend sort ======================== */

const compareText = (a, b) =>
    String(a ?? '').localeCompare(
        String(b ?? ''),
        props.locale,
        { sensitivity: 'base' }
    )

const compareNumber = (a, b) =>
    safeNumber(a) - safeNumber(b)

const frontendSortedCourses = computed(() => {
    if (props.useServerProcessing) {
        return frontendFilteredCourses.value
    }

    const list = [
        ...frontendFilteredCourses.value,
    ]

    const numberSort = {
        idAsc: ['id', false],
        idDesc: ['id', true],

        sortAsc: ['sort', false],
        sortDesc: ['sort', true],

        studentsCountAsc: ['students_count', false],
        studentsCountDesc: ['students_count', true],

        viewsAsc: ['views', false],
        viewsDesc: ['views', true],

        likesAsc: ['likes_count', false],
        likesDesc: ['likes_count', true],

        popularityAsc: ['popularity', false],
        popularityDesc: ['popularity', true],

        ratingCountAsc: ['rating_count', false],
        ratingCountDesc: ['rating_count', true],

        ratingAvgAsc: ['rating_avg', false],
        ratingAvgDesc: ['rating_avg', true],

        difficultyAsc: ['difficulty', false],
        difficultyDesc: ['difficulty', true],

        durationAsc: ['duration', false],
        durationDesc: ['duration', true],

        modulesAsc: ['modules_count', false],
        modulesDesc: ['modules_count', true],

        lessonsAsc: ['lessons_count', false],
        lessonsDesc: ['lessons_count', true],

        tracksAsc: ['tracks_count', false],
        tracksDesc: ['tracks_count', true],

        hashtagsAsc: ['hashtags_count', false],
        hashtagsDesc: ['hashtags_count', true],

        imagesAsc: ['images_count', false],
        imagesDesc: ['images_count', true],

        pricesAsc: ['prices_count', false],
        pricesDesc: ['prices_count', true],

        reviewsAsc: ['reviews_count', false],
        reviewsDesc: ['reviews_count', true],
    }

    const numberRule =
        numberSort[sortCourses.value]

    if (numberRule) {
        const [field, desc] =
            numberRule

        list.sort((a, b) =>
            desc
                ? compareNumber(
                    b?.[field],
                    a?.[field]
                )
                : compareNumber(
                    a?.[field],
                    b?.[field]
                )
        )

        return list
    }

    switch (sortCourses.value) {
        case 'titleAsc':
            list.sort((a, b) =>
                compareText(
                    getCourseTitle(a),
                    getCourseTitle(b)
                )
            )
            break

        case 'titleDesc':
            list.sort((a, b) =>
                compareText(
                    getCourseTitle(b),
                    getCourseTitle(a)
                )
            )
            break

        case 'levelAsc':
            list.sort((a, b) =>
                compareText(
                    a?.level,
                    b?.level
                )
            )
            break

        case 'levelDesc':
            list.sort((a, b) =>
                compareText(
                    b?.level,
                    a?.level
                )
            )
            break

        case 'availabilityAsc':
            list.sort((a, b) =>
                compareText(
                    a?.availability,
                    b?.availability
                )
            )
            break

        case 'availabilityDesc':
            list.sort((a, b) =>
                compareText(
                    b?.availability,
                    a?.availability
                )
            )
            break

        case 'publishedAtAsc':
            list.sort((a, b) =>
                safeDate(a?.published_at)
                - safeDate(b?.published_at)
            )
            break

        case 'publishedAtDesc':
            list.sort((a, b) =>
                safeDate(b?.published_at)
                - safeDate(a?.published_at)
            )
            break

        case 'createdAtAsc':
            list.sort((a, b) =>
                safeDate(a?.created_at)
                - safeDate(b?.created_at)
            )
            break

        case 'createdAtDesc':
            list.sort((a, b) =>
                safeDate(b?.created_at)
                - safeDate(a?.created_at)
            )
            break
    }

    return list
})

/* ======================== Frontend pagination ======================== */

const frontendCurrentPage = ref(1)

const {
    targetRef: coursesScrollTarget,
    scrollToTarget: scrollToCourses,
} = useSmoothScrollTo({
    offset: 80,
    duration: 1200,
})

watch(
    [
        qCourses,
        sortCourses,
    ],
    () => {
        if (!props.useServerProcessing) {
            frontendCurrentPage.value = 1
        }
    }
)

watch(
    frontendCurrentPage,
    () => {
        if (!props.useServerProcessing) {
            scrollToCourses()
        }
    }
)

const effectiveCoursesFound = computed(() =>
    props.useServerProcessing
        ? safeNumber(props.coursesFound)
        : frontendSortedCourses.value.length
)

const frontendPaginatedCourses = computed(() => {
    if (props.useServerProcessing) {
        return coursesData.value
    }

    const start =
        (frontendCurrentPage.value - 1)
        * perPageCourses.value

    return frontendSortedCourses.value.slice(
        start,
        start + perPageCourses.value
    )
})

const displayedCourses = computed(() =>
    props.useServerProcessing
        ? coursesData.value
        : frontendPaginatedCourses.value
)

/* ======================== Server pagination ======================== */

const currentPage = computed(() =>
    Number(
        props.courses?.meta?.current_page
        ?? props.courses?.current_page
        ?? 1
    ) || 1
)

const lastPage = computed(() =>
    Number(
        props.courses?.meta?.last_page
        ?? props.courses?.last_page
        ?? 1
    ) || 1
)

const loadServerCourses = (
    pageNumber = 1
) => {
    if (
        !props.useServerProcessing
        || !canonicalUrl.value
    ) {
        return
    }

    router.get(
        canonicalUrl.value,
        {
            q_courses:
                qCourses.value
                || undefined,

            sort_courses:
                sortCourses.value
                || undefined,

            page_courses:
            pageNumber,
        },
        {
            preserveState: true,
            replace: true,
            preserveScroll: true,
        }
    )
}

const goToPage = (pageNumber) => {
    const target = Math.min(
        Math.max(
            1,
            Number(pageNumber) || 1
        ),
        lastPage.value
    )

    loadServerCourses(target)
}

const goPrev = () => {
    if (currentPage.value > 1) {
        goToPage(
            currentPage.value - 1
        )
    }
}

const goNext = () => {
    if (
        currentPage.value
        < lastPage.value
    ) {
        goToPage(
            currentPage.value + 1
        )
    }
}

/* ======================== Toolbar ======================== */

const applyCourseFilters = () => {
    if (props.useServerProcessing) {
        loadServerCourses(1)
    } else {
        frontendCurrentPage.value = 1
    }
}

const resetCourseFilters = () => {
    if (props.useServerProcessing) {
        loadServerCourses(1)
    } else {
        frontendCurrentPage.value = 1
    }
}

/* ======================== View ======================== */

const VIEW_KEY =
    'public_school_courses_view'

const getStoredView = () => {
    if (typeof window === 'undefined') {
        return 'grid'
    }

    const value =
        localStorage.getItem(VIEW_KEY)

    return ['grid', 'rows'].includes(value)
        ? value
        : 'grid'
}

const viewMode = ref(
    getStoredView()
)

watch(
    viewMode,
    (value) => {
        if (
            typeof window
            !== 'undefined'
        ) {
            localStorage.setItem(
                VIEW_KEY,
                value
            )
        }
    }
)

/* ======================== Sidebars ======================== */

const siteSettings = computed(() =>
    page.props?.siteSettings ?? {}
)

const isAdmin = computed(() =>
    page.props?.isAdmin === true
)

const settingEnabled = (
    value,
    defaultValue = true
) => {
    if (
        value === undefined
        || value === null
        || value === ''
    ) {
        return defaultValue
    }

    if (typeof value === 'boolean') {
        return value
    }

    return String(value) === 'true'
}

const showLeft = computed(() =>
    settingEnabled(
        siteSettings.value?.ViewLeftColumn,
        true
    )
)

const showRight = computed(() =>
    settingEnabled(
        siteSettings.value?.ViewRightColumn,
        true
    )
)

const LEFT_SIDEBAR_KEY =
    'public_left_sidebar_collapsed'

const RIGHT_SIDEBAR_KEY =
    'public_right_sidebar_collapsed'

/**
 * Первый render всегда со свёрнутыми
 * сайдбарами.
 *
 * После mounted восстанавливаем реальное
 * состояние из localStorage.
 *
 * В сочетании с transition-all duration-300
 * получается естественная анимация раскрытия.
 */
const leftCollapsed = ref(true)
const rightCollapsed = ref(true)

const readStoredBoolean = (
    key,
    fallback = true
) => {
    try {
        const value =
            localStorage.getItem(key)

        return value === null
            ? fallback
            : value === 'true'
    } catch {
        return fallback
    }
}

const writeStoredBoolean = (
    key,
    value
) => {
    try {
        localStorage.setItem(
            key,
            String(Boolean(value))
        )
    } catch {
        //
    }
}

onMounted(() => {
    leftCollapsed.value =
        readStoredBoolean(
            LEFT_SIDEBAR_KEY,
            true
        )

    rightCollapsed.value =
        readStoredBoolean(
            RIGHT_SIDEBAR_KEY,
            true
        )
})

const setLeftCollapsed = (value) => {
    leftCollapsed.value =
        Boolean(value)

    writeStoredBoolean(
        LEFT_SIDEBAR_KEY,
        leftCollapsed.value
    )
}

const setRightCollapsed = (value) => {
    rightCollapsed.value =
        Boolean(value)

    writeStoredBoolean(
        RIGHT_SIDEBAR_KEY,
        rightCollapsed.value
    )
}

/**
 * Оба сайдбара открыты → 2.
 * Один открыт → 3.
 * Оба закрыты → 4.
 */
const gridCols = computed(() => {
    const leftExpanded =
        showLeft.value
        && !leftCollapsed.value

    const rightExpanded =
        showRight.value
        && !rightCollapsed.value

    if (
        leftExpanded
        && rightExpanded
    ) {
        return 2
    }

    if (
        leftExpanded
        || rightExpanded
    ) {
        return 3
    }

    return 4
})

/* ======================== Shared data ======================== */

const trackTree = computed(() =>
    Array.isArray(props.trackTree)
        ? props.trackTree
        : []
)

const mainVideosList = computed(() =>
    normalizeList(props.mainVideos)
)

const mainBannersList = computed(() =>
    normalizeList(props.mainBanners)
)
</script>

<template>
    <Head>
        <!-- Основные SEO -->
        <title>{{ seoTitle }}</title>

        <meta
            name="title"
            :content="seoTitle"
        >

        <meta
            v-if="seoDescription"
            name="description"
            :content="seoDescription"
        >

        <meta
            v-if="seoKeywords"
            name="keywords"
            :content="seoKeywords"
        >

        <meta
            name="robots"
            content="index, follow, max-image-preview:large"
        >

        <!-- Canonical -->
        <link
            v-if="canonicalUrl"
            rel="canonical"
            :href="canonicalUrl"
        >

        <!-- Open Graph -->
        <meta
            property="og:type"
            content="profile"
        >

        <meta
            property="og:title"
            :content="seoTitle"
        >

        <meta
            v-if="seoDescription"
            property="og:description"
            :content="seoDescription"
        >

        <meta
            v-if="canonicalUrl"
            property="og:url"
            :content="canonicalUrl"
        >

        <meta
            property="og:locale"
            :content="ogLocale"
        >

        <meta
            v-if="seoImage"
            property="og:image"
            :content="seoImage"
        >

        <!-- Twitter / X -->
        <meta
            name="twitter:card"
            :content="
                seoImage
                    ? 'summary_large_image'
                    : 'summary'
            "
        >

        <meta
            name="twitter:title"
            :content="seoTitle"
        >

        <meta
            v-if="seoDescription"
            name="twitter:description"
            :content="seoDescription"
        >

        <meta
            v-if="seoImage"
            name="twitter:image"
            :content="seoImage"
        >

        <!-- Dublin Core -->
        <meta
            name="DC.title"
            :content="seoTitle"
        >

        <meta
            v-if="seoDescription"
            name="DC.description"
            :content="seoDescription"
        >

        <meta
            v-if="dcSubject"
            name="DC.subject"
            :content="dcSubject"
        >

        <meta
            name="DC.language"
            :content="instructorLocale"
        >

        <meta
            v-if="canonicalUrl"
            name="DC.identifier"
            :content="canonicalUrl"
        >

        <meta
            name="DC.type"
            content="Person"
        >

        <meta
            name="DC.format"
            content="text/html"
        >

        <meta
            v-if="seoCreatedAt"
            name="DC.date"
            :content="seoCreatedAt"
        >
    </Head>

    <DefaultLayout
        :title="title"
        :can-login="canLogin"
        :can-register="canRegister"
    >
        <Navbar />

        <div class="min-h-screen px-3 max-w-full">
            <main class="mx-auto flex flex-col lg:flex-row gap-4 tracking-wider">

                <!-- Left sidebar -->
                <aside
                    v-if="showLeft"
                    class="shrink-0 mt-12 lg:mt-28 transition-all duration-300 overflow-hidden"
                    :class="
                        leftCollapsed
                            ? 'lg:w-10'
                            : 'lg:w-64'
                    "
                >
                    <LeftSidebarSchool
                        :track-tree="trackTree"
                        :collapsed="leftCollapsed"
                        @collapsed="setLeftCollapsed"
                    />
                </aside>

                <!-- Profile page -->
                <article
                    itemscope
                    itemtype="https://schema.org/ProfilePage"
                    :itemid="canonicalUrl"
                    class="w-full lg:mt-28 pb-6 slate-1 min-w-0"
                >
                    <!-- ProfilePage metadata -->
                    <meta
                        itemprop="name"
                        :content="seoTitle"
                    >

                    <meta
                        v-if="seoDescription"
                        itemprop="description"
                        :content="seoDescription"
                    >

                    <meta
                        itemprop="inLanguage"
                        :content="instructorLocale"
                    >

                    <meta
                        v-if="canonicalUrl"
                        itemprop="url"
                        :content="canonicalUrl"
                    >

                    <meta
                        v-if="seoCreatedAt"
                        itemprop="dateCreated"
                        :content="seoCreatedAt"
                    >

                    <div class="mx-auto max-w-6xl">

                        <!-- Breadcrumbs -->
                        <nav
                            class="mb-3 text-sm"
                            aria-label="Breadcrumb"
                            itemscope
                            itemtype="https://schema.org/BreadcrumbList"
                        >
                            <ol class="flex flex-wrap items-center font-semibold">
                                <li
                                    itemprop="itemListElement"
                                    itemscope
                                    itemtype="https://schema.org/ListItem"
                                    class="flex items-center"
                                >
                                    <Link
                                        itemprop="item"
                                        :href="route('home')"
                                        class="breadcrumb-link hover:underline"
                                    >
                                        <span itemprop="name">
                                            {{ t('home') }}
                                        </span>
                                    </Link>

                                    <meta
                                        itemprop="position"
                                        content="1"
                                    >
                                </li>

                                <li
                                    itemprop="itemListElement"
                                    itemscope
                                    itemtype="https://schema.org/ListItem"
                                    class="flex items-center"
                                >
                                    <span class="mx-2 breadcrumbs">
                                        /
                                    </span>

                                    <Link
                                        itemprop="item"
                                        :href="route('public.schoolInstructors.index')"
                                        class="breadcrumb-link hover:underline"
                                    >
                                        <span itemprop="name">
                                            {{ t('instructors') }}
                                        </span>
                                    </Link>

                                    <meta
                                        itemprop="position"
                                        content="2"
                                    >
                                </li>

                                <li
                                    itemprop="itemListElement"
                                    itemscope
                                    itemtype="https://schema.org/ListItem"
                                    class="flex items-center"
                                    aria-current="page"
                                >
                                    <span class="mx-2 breadcrumbs">
                                        /
                                    </span>

                                    <span
                                        itemprop="name"
                                        class="breadcrumbs"
                                    >
                                        {{ instructorName }}
                                    </span>

                                    <meta
                                        v-if="canonicalUrl"
                                        itemprop="item"
                                        :content="canonicalUrl"
                                    >

                                    <meta
                                        itemprop="position"
                                        content="3"
                                    >
                                </li>
                            </ol>
                        </nav>

                        <!-- Person -->
                        <div
                            itemprop="mainEntity"
                            itemscope
                            itemtype="https://schema.org/Person"
                        >
                            <meta
                                itemprop="name"
                                :content="instructorName"
                            >

                            <meta
                                v-if="instructorShort"
                                itemprop="description"
                                :content="instructorShort"
                            >

                            <meta
                                v-if="canonicalUrl"
                                itemprop="url"
                                :content="canonicalUrl"
                            >

                            <meta
                                v-if="seoImage"
                                itemprop="image"
                                :content="seoImage"
                            >

                            <link
                                v-for="(item, index) in normalizedSocialLinks"
                                :key="`same-as-${index}`"
                                itemprop="sameAs"
                                :href="item.url"
                            >

                            <!-- Gallery -->
                            <div
                                v-if="hasInstructorImages"
                                class="flex items-center justify-center"
                            >
                                <div class="w-full">
                                    <ImageGalleryMain
                                        :images="instructorImages"
                                        :alt="instructorName"
                                        rounded-class="rounded-lg"
                                        shadow-class="shadow-lg shadow-gray-400 dark:shadow-gray-700"
                                        img-class="w-full h-full object-cover"
                                    />
                                </div>
                            </div>

                            <!-- Stats / title / views -->
                            <div class="flex items-center justify-between gap-1">
                                <div
                                    :title="t('courses')"
                                    class="flex items-center justify-center gap-1"
                                >
                                    <svg
                                        class="h-5 w-5 text-slate-600/85 dark:text-slate-200/85"
                                        fill="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"
                                        />
                                    </svg>

                                    <span
                                        v-if="hasCourses"
                                        class="text-center text-sm text-gray-500"
                                    >
                                        {{ coursesCount }}
                                    </span>
                                </div>

                                <div class="my-3 flex flex-wrap items-center justify-center gap-3 title">
                                    <h1
                                        itemprop="headline"
                                        class="text-2xl font-bold"
                                    >
                                        {{ instructorName }}
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
                                        {{ instructor.views ?? 0 }}
                                    </span>
                                </div>
                            </div>

                            <!-- Short -->
                            <div
                                v-if="instructorShort"
                                itemprop="abstract"
                                class="mb-4 text-center text-sm text-slate-600 dark:text-slate-300"
                            >
                                {{ instructorShort }}
                            </div>

                            <!-- Details -->
                            <div
                                class="mb-4 flex flex-wrap items-center justify-center gap-3
                                       text-sm text-slate-600 dark:text-slate-300"
                            >
                                <!-- Rating -->
                                <div
                                    class="rounded-sm border border-slate-400 px-3 py-1
                                           flex items-center justify-center gap-1"
                                >
                                    {{ t('rating') }}:

                                    <svg
                                        viewBox="0 0 24 24"
                                        class="shrink-0 h-4 w-4"
                                    >
                                        <path
                                            class="fill-current text-red-400 dark:text-red-300"
                                            d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"
                                        />
                                    </svg>

                                    {{ formattedRating }}
                                    ({{ ratingCount }})
                                </div>

                                <!-- Experience -->
                                <div
                                    v-if="safeNumber(instructor.experience_years) > 0"
                                    class="relative inline-flex text-center px-3 py-1
                                           rounded bg-emerald-500"
                                >
                                    <div
                                        class="absolute w-3 h-3 rounded-full
                                               bg-white left-0 -translate-x-1/2
                                               top-1/2 -translate-y-1/2"
                                        aria-hidden="true"
                                    />

                                    <div
                                        class="absolute w-3 h-3 rounded-full
                                               bg-white right-0 translate-x-1/2
                                               top-1/2 -translate-y-1/2"
                                        aria-hidden="true"
                                    />

                                    <span class="text-sm text-emerald-50 font-medium">
                                        {{ t('experienceYears') }}:
                                        {{ instructor.experience_years }}
                                    </span>
                                </div>

                                <!-- Hourly rate -->
                                <div
                                    v-if="instructor.hourly_rate"
                                    class="rounded-sm border border-slate-400 px-3 py-1"
                                >
                                    {{ t('hourlyRate') }}:
                                    {{ instructor.hourly_rate }}
                                </div>
                            </div>

                            <!-- Bio -->
                            <div
                                v-if="instructorBio"
                                itemprop="description"
                                class="mb-6 rounded-md border border-gray-200
                                       bg-white p-4 text-sm leading-7
                                       text-slate-700 shadow-sm
                                       dark:border-gray-700 dark:bg-gray-900
                                       dark:text-slate-300"
                                v-html="instructorBio"
                            />

                            <!-- Social links -->
                            <div
                                v-if="normalizedSocialLinks.length"
                                class="mb-6 flex flex-wrap items-center justify-center gap-3"
                            >
                                <a
                                    v-for="(item, index) in normalizedSocialLinks"
                                    :key="index"
                                    :href="item.url"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="rounded-md px-3 py-1 btn-default
                                           text-sm font-semibold
                                           inline-flex items-center gap-2"
                                >
                                    <svg
                                        class="h-4 w-4"
                                        viewBox="0 0 24 24"
                                        fill="currentColor"
                                        aria-hidden="true"
                                    >
                                        <path
                                            d="M14 3h7v7h-2V6.41l-8.29 8.3-1.42-1.42L17.59 5H14V3ZM5 5h6v2H5v12h12v-6h2v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z"
                                        />
                                    </svg>

                                    <span>
                                        {{ getSocialLabel(item) }}
                                    </span>
                                </a>
                            </div>
                        </div>

                        <!-- Courses toolbar -->
                        <EntityPageToolbar
                            v-if="hasCourses"
                            v-model="qCourses"
                            v-model:view-mode="viewMode"
                            v-model:sort-value="sortCourses"
                            :found="effectiveCoursesFound"
                            :sort-options="courseSortOptions"
                            :default-sort="DEFAULT_SORT"
                            :found-label="t('courses')"
                            :search-placeholder="t('searchByName')"
                            @submit="applyCourseFilters"
                            @reset="resetCourseFilters"
                        />

                        <div ref="coursesScrollTarget" />

                        <!-- Empty -->
                        <div
                            v-if="hasCourses && displayedCourses.length === 0"
                            class="mt-6 text-center text-slate-700 dark:text-slate-300"
                        >
                            {{ t('noData') }}
                        </div>

                        <!-- Courses -->
                        <div v-if="displayedCourses.length">
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

                        <!-- Server pagination -->
                        <Pagination
                            v-if="useServerProcessing && hasCourses && lastPage > 1"
                            :current-page="currentPage"
                            :last-page="lastPage"
                            :found="coursesFound"
                            @prev="goPrev"
                            @next="goNext"
                            @go="goToPage"
                        />

                        <!-- Frontend pagination -->
                        <FrontendPagination
                            v-if="!useServerProcessing && effectiveCoursesFound > perPageCourses"
                            v-model:currentPage="frontendCurrentPage"
                            :items-per-page="perPageCourses"
                            :total-items="effectiveCoursesFound"
                        />

                        <!-- Main content -->
                        <SectionVideoList
                            :videos="mainVideosList"
                        />

                        <SectionBanners
                            :banners="mainBannersList"
                        />
                    </div>
                </article>

                <!-- Right sidebar -->
                <aside
                    v-if="showRight"
                    class="shrink-0 lg:mt-28 transition-all duration-300 overflow-hidden"
                    :class="
                        rightCollapsed
                            ? 'lg:w-10'
                            : 'lg:w-64'
                    "
                >
                    <RightSidebarSchool
                        :collapsed="rightCollapsed"
                        @collapsed="setRightCollapsed"
                    />
                </aside>
            </main>
        </div>

        <FooterBlog />
        <Progress />

        <!-- Admin bottom panel -->
        <PublicAdminBottomPanel
            v-if="isAdmin"
            setting-key="publicSchoolCoursesProcessingMode"
            :mode="publicSchoolCoursesProcessingMode"
            :use-server-processing="useServerProcessing"
            :total="coursesCount"
        />
    </DefaultLayout>
</template>
