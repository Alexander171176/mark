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

import InstructorGrid from '@/Components/Public/Default/School/SchoolInstructor/InstructorGrid.vue'
import InstructorRows from '@/Components/Public/Default/School/SchoolInstructor/InstructorRows.vue'

import PublicAdminBottomPanel
    from '@/Components/Admin/UI/PublicAdminPanel/PublicAdminBottomPanel.vue'

const { t } = useI18n()
const page = usePage()

const props = defineProps({
    locale: {
        type: String,
        default: 'ru',
    },

    seo: {
        type: Object,
        default: () => ({
            title: '',
            keywords: '',
            description: '',
        }),
    },

    publicSchoolInstructorsProcessingMode: {
        type: String,
        default: 'server',
    },

    useServerProcessing: {
        type: Boolean,
        default: false,
    },

    title: {
        type: String,
        default: '',
    },

    canLogin: {
        type: Boolean,
        default: false,
    },

    canRegister: {
        type: Boolean,
        default: false,
    },

    instructorProfiles: {
        type: [Array, Object],
        default: () => [],
    },

    instructorProfilesCount: {
        type: Number,
        default: 0,
    },

    instructorProfilesFound: {
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

/* ======================== Shared page data ======================== */

const siteSettings = computed(() =>
    page.props?.siteSettings ?? {}
)

const isAdmin = computed(() =>
    page.props?.isAdmin === true
)

const trackTree = computed(() =>
    Array.isArray(props.trackTree)
        ? props.trackTree
        : []
)

const mainVideos = computed(() =>
    normalizeList(props.mainVideos)
)

const mainBanners = computed(() =>
    normalizeList(props.mainBanners)
)

/* ======================== Instructors ======================== */

const instructorsData = computed(() =>
    normalizeList(props.instructorProfiles)
)

const getInstructorTitle = (instructor) =>
    instructor?.translation?.title
    || instructor?.user?.name
    || t('instructor')

const getInstructorShort = (instructor) =>
    instructor?.translation?.short || ''

const getInstructorSlug = (instructor) =>
    instructor?.slug || ''

/* ======================== SEO ======================== */

const seoTitle = computed(() =>
    props.seo?.title || t('instructors')
)

const seoDescription = computed(() =>
    props.seo?.description || t('instructors')
)

const seoKeywords = computed(() =>
    props.seo?.keywords || ''
)

const contentLocale = computed(() =>
    props.locale || 'ru'
)

const ogLocale = computed(() =>
    contentLocale.value === 'ru'
        ? 'ru_RU'
        : contentLocale.value
)

const canonicalUrl = computed(() =>
    String(
        route('public.schoolInstructors.index')
    )
)

const getImageUrl = (image) =>
    image?.webp_url
    || image?.image_url
    || image?.thumb_url
    || image?.url
    || image?.preview
    || ''

/**
 * Для CollectionPage берём первое
 * доступное изображение инструктора.
 */
const seoImage = computed(() => {
    for (const instructor of instructorsData.value) {
        const images = Array.isArray(instructor?.images)
            ? instructor.images
            : []

        const url = getImageUrl(images[0])

        if (url) return url
    }

    return ''
})

const dcSubject = computed(() =>
    seoKeywords.value || seoTitle.value
)

/* ======================== Filters ======================== */

const q = ref(
    String(
        props.filters?.q ?? ''
    )
)

const DEFAULT_SORT = 'idDesc'

const sort = ref(
    String(
        props.filters?.sort
        ?? DEFAULT_SORT
    )
)

/**
 * Единственный источник истины:
 * PublicSettingsService → controller.
 *
 * Vue только получает значение.
 */
const perPage = computed(() => {
    const value = Number(
        props.filters?.per_page
    )

    return Number.isFinite(value)
    && value > 0
        ? value
        : 12
})

const instructorSortOptions = [
    { value: 'sortAsc', label: `${t('sortNumber')} 0→9` },
    { value: 'sortDesc', label: `${t('sortNumber')} 9→0` },

    { value: 'idDesc', label: t('idDesc') },
    { value: 'idAsc', label: t('idAsc') },

    { value: 'titleAsc', label: `${t('title')} A→Z` },
    { value: 'titleDesc', label: `${t('title')} Z→A` },

    { value: 'viewsDesc', label: `${t('views')} 9→0` },
    { value: 'viewsAsc', label: `${t('views')} 0→9` },

    { value: 'ratingCountDesc', label: `${t('ratingCount')} 9→0` },
    { value: 'ratingCountAsc', label: `${t('ratingCount')} 0→9` },

    { value: 'ratingAvgDesc', label: `${t('ratingAvg')} 9→0` },
    { value: 'ratingAvgAsc', label: `${t('ratingAvg')} 0→9` },

    { value: 'experienceDesc', label: `${t('experienceYears')} 9→0` },
    { value: 'experienceAsc', label: `${t('experienceYears')} 0→9` },

    { value: 'coursesDesc', label: `${t('courses')} 9→0` },
    { value: 'coursesAsc', label: `${t('courses')} 0→9` },

    { value: 'dateDesc', label: `${t('createdAt')} ↓` },
    { value: 'dateAsc', label: `${t('createdAt')} ↑` },
]

/* ======================== View mode ======================== */

const VIEW_KEY =
    'public_school_instructors_view'

const viewMode = ref(
    String(
        props.filters?.view || 'grid'
    )
)

onMounted(() => {
    try {
        const storedView =
            localStorage.getItem(VIEW_KEY)

        if (
            storedView === 'grid'
            || storedView === 'rows'
        ) {
            viewMode.value = storedView
        }
    } catch {
        //
    }
})

watch(
    viewMode,
    (value) => {
        try {
            localStorage.setItem(
                VIEW_KEY,
                value
            )
        } catch {
            //
        }
    }
)

/* ======================== Frontend search ======================== */

const filteredInstructors = computed(() => {
    if (props.useServerProcessing) {
        return instructorsData.value
    }

    const query =
        normalizeText(q.value)

    if (!query) {
        return instructorsData.value
    }

    return instructorsData.value.filter(
        (instructor) => [
            instructor?.id,
            getInstructorTitle(instructor),
            getInstructorShort(instructor),
            getInstructorSlug(instructor),
            instructor?.user?.name,
        ].some((value) =>
            normalizeText(value).includes(query)
        )
    )
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

const sortedInstructors = computed(() => {
    if (props.useServerProcessing) {
        return filteredInstructors.value
    }

    const list = [
        ...filteredInstructors.value,
    ]

    switch (sort.value) {
        case 'sortAsc':
            list.sort((a, b) =>
                compareNumber(a?.sort, b?.sort)
                || compareNumber(b?.id, a?.id)
            )
            break

        case 'sortDesc':
            list.sort((a, b) =>
                compareNumber(b?.sort, a?.sort)
                || compareNumber(b?.id, a?.id)
            )
            break

        case 'idAsc':
            list.sort((a, b) =>
                compareNumber(a?.id, b?.id)
            )
            break

        case 'idDesc':
            list.sort((a, b) =>
                compareNumber(b?.id, a?.id)
            )
            break

        case 'titleAsc':
            list.sort((a, b) =>
                compareText(
                    getInstructorTitle(a),
                    getInstructorTitle(b)
                )
            )
            break

        case 'titleDesc':
            list.sort((a, b) =>
                compareText(
                    getInstructorTitle(b),
                    getInstructorTitle(a)
                )
            )
            break

        case 'viewsAsc':
            list.sort((a, b) =>
                compareNumber(
                    a?.views,
                    b?.views
                )
            )
            break

        case 'viewsDesc':
            list.sort((a, b) =>
                compareNumber(
                    b?.views,
                    a?.views
                )
            )
            break

        case 'ratingAvgAsc':
            list.sort((a, b) =>
                compareNumber(
                    a?.rating_avg,
                    b?.rating_avg
                )
            )
            break

        case 'ratingAvgDesc':
            list.sort((a, b) =>
                compareNumber(
                    b?.rating_avg,
                    a?.rating_avg
                )
            )
            break

        case 'ratingCountAsc':
            list.sort((a, b) =>
                compareNumber(
                    a?.rating_count,
                    b?.rating_count
                )
            )
            break

        case 'ratingCountDesc':
            list.sort((a, b) =>
                compareNumber(
                    b?.rating_count,
                    a?.rating_count
                )
            )
            break

        case 'experienceAsc':
            list.sort((a, b) =>
                compareNumber(
                    a?.experience_years,
                    b?.experience_years
                )
            )
            break

        case 'experienceDesc':
            list.sort((a, b) =>
                compareNumber(
                    b?.experience_years,
                    a?.experience_years
                )
            )
            break

        case 'coursesAsc':
            list.sort((a, b) =>
                compareNumber(
                    a?.courses_count,
                    b?.courses_count
                )
            )
            break

        case 'coursesDesc':
            list.sort((a, b) =>
                compareNumber(
                    b?.courses_count,
                    a?.courses_count
                )
            )
            break

        case 'dateAsc':
            list.sort((a, b) =>
                safeDate(a?.created_at)
                - safeDate(b?.created_at)
            )
            break

        case 'dateDesc':
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
    targetRef: scrollTarget,
    scrollToTarget,
} = useSmoothScrollTo({
    offset: 80,
    duration: 1200,
})

watch(
    [
        q,
        sort,
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
            scrollToTarget()
        }
    }
)

const effectiveInstructorsFound = computed(() =>
    props.useServerProcessing
        ? safeNumber(
            props.instructorProfilesFound
        )
        : sortedInstructors.value.length
)

const frontendPaginatedInstructors =
    computed(() => {
        if (props.useServerProcessing) {
            return instructorsData.value
        }

        const start =
            (
                frontendCurrentPage.value - 1
            ) * perPage.value

        return sortedInstructors.value.slice(
            start,
            start + perPage.value
        )
    })

const displayedInstructors = computed(() =>
    props.useServerProcessing
        ? instructorsData.value
        : frontendPaginatedInstructors.value
)

/* ======================== Server pagination ======================== */

const currentPage = computed(() =>
    Number(
        props.instructorProfiles
            ?.meta
            ?.current_page

        ?? props.instructorProfiles
            ?.current_page

        ?? 1
    ) || 1
)

const lastPage = computed(() =>
    Number(
        props.instructorProfiles
            ?.meta
            ?.last_page

        ?? props.instructorProfiles
            ?.last_page

        ?? 1
    ) || 1
)

const reloadInstructors = (
    pageNumber = 1
) => {
    if (!props.useServerProcessing) {
        return
    }

    router.get(
        route(
            'public.schoolInstructors.index'
        ),
        {
            q:
                q.value
                || undefined,

            sort:
                sort.value
                || undefined,

            page:
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

    reloadInstructors(target)
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

const applyFilters = () => {
    if (props.useServerProcessing) {
        reloadInstructors(1)
    } else {
        frontendCurrentPage.value = 1
    }
}

const resetFilters = () => {
    if (props.useServerProcessing) {
        reloadInstructors(1)
    } else {
        frontendCurrentPage.value = 1
    }
}

/* ======================== Sidebars ======================== */

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
 * Первый render:
 * оба sidebar свёрнуты.
 *
 * После mounted восстанавливаем
 * состояние из localStorage.
 *
 * transition-all duration-300
 * создаёт естественное раскрытие.
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
 * Оба открыты → 2.
 * Один открыт → 3.
 * Оба закрыты → 4.
 */
const instructorGridCols = computed(() => {
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
            rel="canonical"
            :href="canonicalUrl"
        >

        <!-- Open Graph -->
        <meta
            property="og:type"
            content="website"
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
            :content="contentLocale"
        >

        <meta
            name="DC.identifier"
            :content="canonicalUrl"
        >

        <meta
            name="DC.type"
            content="Collection"
        >

        <meta
            name="DC.format"
            content="text/html"
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

                <!-- Content -->
                <article
                    itemscope
                    itemtype="https://schema.org/CollectionPage"
                    :itemid="canonicalUrl"
                    class="w-full lg:mt-28 pb-6 slate-1 min-w-0"
                >
                    <!-- Schema.org -->
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
                        v-if="seoKeywords"
                        itemprop="keywords"
                        :content="seoKeywords"
                    >

                    <meta
                        itemprop="url"
                        :content="canonicalUrl"
                    >

                    <meta
                        itemprop="inLanguage"
                        :content="contentLocale"
                    >

                    <div class="mx-auto max-w-6xl">

                        <!-- Breadcrumbs -->
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
                                    aria-current="page"
                                >
                                    <span class="mx-2 breadcrumbs">
                                        /
                                    </span>

                                    <span
                                        itemprop="name"
                                        class="breadcrumbs"
                                    >
                                        {{ t('instructors') }}
                                    </span>

                                    <meta
                                        itemprop="item"
                                        :content="canonicalUrl"
                                    >

                                    <meta
                                        itemprop="position"
                                        content="2"
                                    >
                                </li>

                            </ol>
                        </nav>

                        <!-- Header -->
                        <div
                            class="my-3 flex flex-wrap items-center
                                   justify-center gap-2 title"
                        >
                            <svg
                                class="shrink-0 h-7 w-7 text-slate-600/85
                                       dark:text-slate-200/85"
                                fill="currentColor"
                                viewBox="0 0 640 512"
                            >
                                <path
                                    d="M622.34 153.2L343.4 67.5c-15.2-4.67-31.6-4.67-46.79 0L17.66 153.2c-23.54 7.23-23.54 38.36 0 45.59l48.63 14.94c-10.67 13.19-17.23 29.28-17.88 46.9C38.78 266.15 32 276.11 32 288c0 10.78 5.68 19.85 13.86 25.65L20.33 428.53C18.11 438.52 25.71 448 35.94 448h56.11c10.24 0 17.84-9.48 15.62-19.47L82.14 313.65C90.32 307.85 96 298.78 96 288c0-11.57-6.47-21.25-15.66-26.87.76-15.02 8.44-28.3 20.69-36.72L296.6 284.5c9.06 2.78 26.44 6.25 46.79 0l278.95-85.7c23.55-7.24 23.55-38.36 0-45.6zM352.79 315.09c-28.53 8.76-52.84 3.92-65.59 0l-145.02-44.55L128 384c0 35.35 85.96 64 192 64s192-28.65 192-64l-14.18-113.47-145.03 44.56z"
                                />
                            </svg>

                            <h1
                                itemprop="headline"
                                class="text-2xl font-bold"
                            >
                                {{ t('instructors') }}
                            </h1>
                        </div>

                        <!-- SEO description -->
                        <div
                            v-if="seoDescription"
                            itemprop="abstract"
                            class="my-1 text-sm subtitle text-center"
                        >
                            {{ seoDescription }}
                        </div>

                        <!-- Toolbar -->
                        <EntityPageToolbar
                            v-model="q"
                            v-model:view-mode="viewMode"
                            v-model:sort-value="sort"
                            :found="effectiveInstructorsFound"
                            :sort-options="instructorSortOptions"
                            :default-sort="DEFAULT_SORT"
                            :found-label="t('instructors')"
                            :search-placeholder="t('searchByName')"
                            @submit="applyFilters"
                            @reset="resetFilters"
                        />

                        <div ref="scrollTarget"></div>

                        <!-- Empty -->
                        <div
                            v-if="displayedInstructors.length === 0"
                            class="mt-6 text-center
                                   text-slate-700 dark:text-slate-300"
                        >
                            {{ t('noData') }}
                        </div>

                        <!-- Instructors -->
                        <div v-else>
                            <InstructorGrid
                                v-if="viewMode === 'grid'"
                                :instructors="displayedInstructors"
                                :cols="instructorGridCols"
                            />

                            <InstructorRows
                                v-else
                                :instructors="displayedInstructors"
                            />
                        </div>

                        <!-- Server pagination -->
                        <Pagination
                            v-if="useServerProcessing && lastPage > 1"
                            :current-page="currentPage"
                            :last-page="lastPage"
                            :found="instructorProfilesFound"
                            @prev="goPrev"
                            @next="goNext"
                            @go="goToPage"
                        />

                        <!-- Frontend pagination -->
                        <FrontendPagination
                            v-if="
                                !useServerProcessing
                                && effectiveInstructorsFound > perPage
                            "
                            v-model:currentPage="frontendCurrentPage"
                            :items-per-page="perPage"
                            :total-items="effectiveInstructorsFound"
                        />

                        <!-- Main content -->
                        <SectionVideoList
                            :videos="mainVideos"
                        />

                        <SectionBanners
                            :banners="mainBanners"
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
            setting-key="publicSchoolInstructorsProcessingMode"
            :mode="publicSchoolInstructorsProcessingMode"
            :use-server-processing="useServerProcessing"
            :total="instructorProfilesCount"
        />
    </DefaultLayout>
</template>
