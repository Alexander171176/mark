<script setup>
/**
 * Страница списка инструкторов
 * - шапка, центральная часть, подвал
 * - светлый, тёмный режим
 * - серверный поиск
 * - серверная пагинация
 * - серверная сортировка
 * - показ карточками, в строку
 * - показ главных видео, баннеров внизу страницы
 * - показ, скрытие колонок
 * - показ дерева треков в левой колонке
 * - показ облако хештегов в правой колонке
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
import LeftSidebarSchool from '@/Components/Public/Default/Partials/LeftSidebarSchool.vue'
import RightSidebarSchool from '@/Components/Public/Default/Partials/RightSidebarSchool.vue'
import EntityPageToolbar from '@/Components/Public/Default/PageToolbar/EntityPageToolbar.vue'
import Pagination from '@/Components/Public/Default/Pagination/Pagination.vue'
import SectionVideoList from '@/Components/Public/Default/Blog/BlogVideo/SectionVideoList.vue'
import SectionBanners from '@/Components/Public/Default/Blog/BlogBanner/SectionBanners.vue'

import InstructorGrid from '@/Components/Public/Default/School/SchoolInstructor/InstructorGrid.vue'
import InstructorRows from '@/Components/Public/Default/School/SchoolInstructor/InstructorRows.vue'
import FrontendPagination from '@/Components/Public/Default/Pagination/FrontendPagination.vue'
import FrontendEntityPageToolbar from '@/Components/Public/Default/PageToolbar/FrontendEntityPageToolbar.vue'
import PublicAdminBottomPanel from '@/Components/Admin/UI/PublicAdminPanel/PublicAdminBottomPanel.vue'

const { t } = useI18n()

/** Props страницы */
const props = defineProps({
    locale: { type: String, default: 'ru' },

    useServerProcessing: { type: Boolean, default: false },
    publicSchoolInstructorsProcessingMode: { type: String, default: 'server' },

    title: { type: String, default: '' },
    canLogin: { type: Boolean, default: false },
    canRegister: { type: Boolean, default: false },

    trackTree: { type: Array, default: () => [] },

    instructorProfiles: { type: [Array, Object], default: () => [] },
    instructorProfilesCount: { type: Number, default: 0 },
    instructorProfilesFound: { type: Number, default: 0 },

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

/** Дерево треков для левого аккордеона */
const trackTree = computed(() => Array.isArray(props.trackTree) ? props.trackTree : [])

/** Универсальный список инструкторов: server paginator data или frontend array */
const instructorsData = computed(() => {
    if (Array.isArray(props.instructorProfiles)) {
        return props.instructorProfiles
    }

    if (Array.isArray(props.instructorProfiles?.data)) {
        return props.instructorProfiles.data
    }

    return []
})

/** Количество элементов на странице для обоих режимов */
const perPage = computed(() => {
    const value = Number(props.filters?.per_page ?? 6)

    return Number.isFinite(value) ? value : 6
})

/** Поисковая строка для server/frontend */
const q = ref(String(props.filters?.q ?? ''))

/** Сортировка по умолчанию для server/frontend */
const DEFAULT_SORT = 'idDesc'

/** Текущая сортировка для server/frontend */
const sort = ref(String(props.filters?.sort ?? DEFAULT_SORT))

/** Ключ локального хранения режима отображения */
const VIEW_KEY = 'public_school_instructors_view'

/** Режим отображения карточки/строки для server/frontend */
const viewMode = ref(
    String(props.filters?.view || localStorage.getItem(VIEW_KEY) || 'grid')
)

/** Сохраняем режим отображения локально */
watch(viewMode, (value) => {
    localStorage.setItem(VIEW_KEY, value)
})

/** Опции сортировки для toolbar */
const instructorSortOptions = [
    { value: 'sortAsc', label: `${t('sortNumber')} 0→9` },
    { value: 'sortDesc', label: `${t('sortNumber')} 9→0` },

    { value: 'idDesc', label: t('idDesc') },
    { value: 'idAsc', label: t('idAsc') },

    { value: 'titleAsc', label: `${t('title')} A→Z` },
    { value: 'titleDesc', label: `${t('title')} Z→A` },

    { value: 'viewsDesc', label: `${t('views')} 9→0` },
    { value: 'viewsAsc', label: `${t('views')} 0→9` },

    { value: 'ratingCountDesc', label: `${t('rating')} 5→0` },
    { value: 'ratingCountAsc', label: `${t('rating')} 0→5` },

    { value: 'ratingAvgDesc', label: `${t('rating')} ${t('instructor')} 9→0` },
    { value: 'ratingAvgAsc', label: `${t('rating')} ${t('instructor')} 0→9` },

    { value: 'experienceDesc', label: `${t('experienceYears')} 9→0` },
    { value: 'experienceAsc', label: `${t('experienceYears')} 0→9` },

    { value: 'coursesDesc', label: `${t('courses')} 9→0` },
    { value: 'coursesAsc', label: `${t('courses')} 0→9` },

    { value: 'dateDesc', label: `${t('createdAt')} ↓` },
    { value: 'dateAsc', label: `${t('createdAt')} ↑` },
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

/** Получение названия инструктора из разных возможных структур ресурса */
const getInstructorTitle = (instructor) => {
    return instructor.title
        || instructor.name
        || instructor.full_name
        || instructor.user?.name
        || instructor.translation?.title
        || instructor.translation?.name
        || instructor.current_translation?.title
        || instructor.current_translation?.name
        || instructor.translations?.[0]?.title
        || instructor.translations?.[0]?.name
        || ''
}

/** Получение краткого текста инструктора */
const getInstructorShort = (instructor) => {
    return instructor.short
        || instructor.description
        || instructor.subtitle
        || instructor.translation?.short
        || instructor.translation?.description
        || instructor.translation?.subtitle
        || instructor.current_translation?.short
        || instructor.current_translation?.description
        || instructor.current_translation?.subtitle
        || instructor.translations?.[0]?.short
        || instructor.translations?.[0]?.description
        || instructor.translations?.[0]?.subtitle
        || ''
}

/** Получение slug инструктора */
const getInstructorSlug = (instructor) => {
    return instructor.slug
        || instructor.url
        || instructor.translation?.slug
        || instructor.current_translation?.slug
        || instructor.translations?.[0]?.slug
        || ''
}

/** Локальный поиск frontend */
const filteredInstructors = computed(() => {
    const query = normalizeText(q.value).trim()

    if (!query) {
        return instructorsData.value
    }

    return instructorsData.value.filter((instructor) => {
        return [
            getInstructorTitle(instructor),
            getInstructorShort(instructor),
            getInstructorSlug(instructor),
            instructor.user?.name,
            instructor.user?.email,
        ].some((value) => normalizeText(value).includes(query))
    })
})

/** Локальная сортировка frontend */
const sortedInstructors = computed(() => {
    const list = [...filteredInstructors.value]

    return list.sort((a, b) => {
        switch (sort.value) {
            case 'sortAsc':
                return (a.sort ?? 0) - (b.sort ?? 0)

            case 'sortDesc':
                return (b.sort ?? 0) - (a.sort ?? 0)

            case 'idAsc':
                return (a.id ?? 0) - (b.id ?? 0)

            case 'idDesc':
                return (b.id ?? 0) - (a.id ?? 0)

            case 'titleAsc':
            case 'nameAsc':
                return normalizeText(getInstructorTitle(a))
                    .localeCompare(normalizeText(getInstructorTitle(b)))

            case 'titleDesc':
            case 'nameDesc':
                return normalizeText(getInstructorTitle(b))
                    .localeCompare(normalizeText(getInstructorTitle(a)))

            case 'viewsAsc':
                return (a.views ?? 0) - (b.views ?? 0)

            case 'viewsDesc':
                return (b.views ?? 0) - (a.views ?? 0)

            case 'ratingAvgAsc':
                return (a.rating_avg ?? 0) - (b.rating_avg ?? 0)

            case 'ratingAvgDesc':
                return (b.rating_avg ?? 0) - (a.rating_avg ?? 0)

            case 'ratingCountAsc':
                return (a.rating_count ?? 0) - (b.rating_count ?? 0)

            case 'ratingCountDesc':
                return (b.rating_count ?? 0) - (a.rating_count ?? 0)

            case 'experienceAsc':
                return (a.experience_years ?? 0) - (b.experience_years ?? 0)

            case 'experienceDesc':
                return (b.experience_years ?? 0) - (a.experience_years ?? 0)

            case 'coursesAsc':
                return (a.courses_count ?? 0) - (b.courses_count ?? 0)

            case 'coursesDesc':
                return (b.courses_count ?? 0) - (a.courses_count ?? 0)

            case 'dateAsc':
                return new Date(a.published_at ?? a.created_at ?? 0) -
                    new Date(b.published_at ?? b.created_at ?? 0)

            case 'dateDesc':
                return new Date(b.published_at ?? b.created_at ?? 0) -
                    new Date(a.published_at ?? a.created_at ?? 0)

            default:
                return 0
        }
    })
})

/** Локальная пагинация frontend */
const frontendPaginatedInstructors = computed(() => {
    const start = (frontendCurrentPage.value - 1) * perPage.value

    return sortedInstructors.value.slice(start, start + perPage.value)
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
    return Number(
        props.instructorProfiles?.meta?.current_page ??
        props.instructorProfiles?.current_page ?? 1) || 1
})

/** Последняя страница server-пагинации */
const lastPage = computed(() => {
    return Number(
        props.instructorProfiles?.meta?.last_page ??
        props.instructorProfiles?.last_page ?? 1) || 1
})

/** Маршрут списка рубрик для server-режима */
const indexRoute = () => route('public.schoolInstructors.index')

/** Server-загрузка рубрик с query-параметрами */
const reloadInstructors = (page = 1) => {
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
    reloadInstructors(1)
}

/** Сброс поиска и сортировки для обоих режимов */
const resetSearch = () => {
    q.value = ''
    sort.value = DEFAULT_SORT
    frontendCurrentPage.value = 1

    if (props.useServerProcessing) {
        reloadInstructors(1)
    }
}

/** Server-изменение сортировки */
const updateSort = (value) => {
    sort.value = value || DEFAULT_SORT

    if (props.useServerProcessing) {
        reloadInstructors(1)
    }
}

/** Изменение режима отображения для обоих режимов */
const updateViewMode = (value) => {
    viewMode.value = value || 'grid'

    if (props.useServerProcessing) {
        reloadInstructors(currentPage.value)
    }
}

/** Server-переход на страницу */
const goToPage = (page) => {
    const value = Number(page)

    if (!Number.isFinite(value)) return

    const safePage = Math.max(1, Math.min(value, lastPage.value))

    reloadInstructors(safePage)
}

/** Server-предыдущая страница */
const goPrev = () => {
    if (currentPage.value <= 1) return
    goToPage(currentPage.value - 1)
}

/** Server-следующая страница */
const goNext = () => {
    if (currentPage.value >= lastPage.value) return
    goToPage(currentPage.value + 1)
}

/* ===================== COMMON VIEW ===================== */

/** Итоговый список для отображения: server data или frontend page */
const displayedInstructors = computed(() => {
    return props.useServerProcessing
        ? instructorsData.value
        : frontendPaginatedInstructors.value
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
const instructorGridCols = computed(() => {
    const leftExpanded = showLeft.value && !leftCollapsed.value
    const rightExpanded = showRight.value && !rightCollapsed.value

    return leftExpanded && rightExpanded ? 2 : 3
})

/** нормализация массивов баннеров и видео внизу */
const normalizeList = (value) => {
    if (Array.isArray(value)) return value
    if (Array.isArray(value?.data)) return value.data
    return []
}

const mainVideosList = computed(() => normalizeList(props.mainVideos))
const mainBannersList = computed(() => normalizeList(props.mainBanners))

</script>

<template>
    <Head>
        <title>{{t('instructors')}}</title>
        <meta name="title" :content="t('instructors')" />
        <meta name="keywords" content="" />
        <meta name="description" :content="t('instructors')" />

        <meta property="og:title" :content="t('instructors')" />
        <meta property="og:description" :content="t('instructors')" />
        <meta property="og:type" content="website" />
        <meta property="og:url" :content="`/instructors`" />
        <meta property="og:image" content="" />
        <meta property="og:locale" :content="'ru_RU'" />

        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="t('instructors')" />
        <meta name="twitter:description" :content="t('instructors')" />
        <meta name="twitter:image" content="" />

        <meta name="DC.title" :content="t('instructors')" />
        <meta name="DC.description" :content="t('instructors')" />
        <meta name="DC.identifier" :content="`/instructors`" />
        <meta name="DC.language" :content="'ru'" />
    </Head>

    <DefaultLayout :title="title" :can-login="canLogin" :can-register="canRegister">
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
                                    {{ t('instructors') }}
                                </li>
                            </ol>
                        </nav>

                        <!-- Заголовок -->
                        <div class="my-3 flex flex-wrap items-center justify-center gap-2 title">
                            <svg class="shrink-0 h-7 w-7 text-slate-600/85 dark:text-slate-200/85"
                                 fill="currentColor"
                                 viewBox="0 0 640 512">
                                <path d="M622.34 153.2L343.4 67.5c-15.2-4.67-31.6-4.67-46.79 0L17.66 153.2c-23.54 7.23-23.54 38.36 0 45.59l48.63 14.94c-10.67 13.19-17.23 29.28-17.88 46.9C38.78 266.15 32 276.11 32 288c0 10.78 5.68 19.85 13.86 25.65L20.33 428.53C18.11 438.52 25.71 448 35.94 448h56.11c10.24 0 17.84-9.48 15.62-19.47L82.14 313.65C90.32 307.85 96 298.78 96 288c0-11.57-6.47-21.25-15.66-26.87.76-15.02 8.44-28.3 20.69-36.72L296.6 284.5c9.06 2.78 26.44 6.25 46.79 0l278.95-85.7c23.55-7.24 23.55-38.36 0-45.6zM352.79 315.09c-28.53 8.76-52.84 3.92-65.59 0l-145.02-44.55L128 384c0 35.35 85.96 64 192 64s192-28.65 192-64l-14.18-113.47-145.03 44.56z"></path>
                            </svg>
                            <h1 class="text-2xl font-bold">
                                {{ t('instructors') }}
                            </h1>
                        </div>

                        <!-- Подзаголовок -->
                        <div class="my-1 text-sm subtitle text-center">
                            Найдите подходящего инструктора для обучения
                        </div>

                        <!-- Toolbar -->
                        <EntityPageToolbar
                            v-if="useServerProcessing"
                            v-model="q"
                            :found="instructorProfilesFound"
                            :view-mode="viewMode"
                            :sort-value="sort"
                            :sort-options="instructorSortOptions"
                            :default-sort="DEFAULT_SORT"
                            :found-label="t('instructors')"
                            :search-placeholder="t('searchByName')"
                            @submit="submitSearch"
                            @reset="resetSearch"
                            @update:viewMode="updateViewMode"
                            @update:sortValue="updateSort"
                        />

                        <FrontendEntityPageToolbar
                            v-else
                            v-model="q"
                            :found="sortedInstructors.length"
                            :view-mode="viewMode"
                            :sort-value="sort"
                            :sort-options="instructorSortOptions"
                            :default-sort="DEFAULT_SORT"
                            :found-label="t('instructors')"
                            :search-placeholder="t('searchByName')"
                            @reset="resetSearch"
                            @update:viewMode="updateViewMode"
                            @update:sortValue="updateSort"
                        />

                        <div ref="scrollTarget"></div>

                        <!-- Empty -->
                        <div
                            v-if="displayedInstructors.length === 0"
                            class="mt-6 text-center text-slate-700 dark:text-slate-300"
                        >
                            {{ t('noData') }}
                        </div>

                        <!-- Views -->
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

                        <!-- Пагинация -->
                        <Pagination
                            v-if="useServerProcessing"
                            :current-page="currentPage"
                            :last-page="lastPage"
                            :found="instructorProfilesFound"
                            @prev="goPrev"
                            @next="goNext"
                            @go="goToPage"
                        />

                        <FrontendPagination
                            v-else
                            v-model:currentPage="frontendCurrentPage"
                            :items-per-page="perPage"
                            :total-items="sortedInstructors.length"
                        />

                        <SectionVideoList :videos="mainVideosList" />
                        <SectionBanners :banners="mainBannersList" />
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
            setting-key="publicSchoolInstructorsProcessingMode"
            :mode="publicSchoolInstructorsProcessingMode"
            :use-server-processing="useServerProcessing"
            :total="instructorProfilesCount"
        />
    </DefaultLayout>
</template>
