<script setup>
/**
 * Страница конкретной категории обучения
 * - шапка, центральная часть, подвал
 * - светлый, тёмный режим
 * - серверный поиск
 * - серверная пагинация
 * - серверная сортировка
 * - показ курсов карточками, в строку
 * - показ главных видео, баннеров внизу страницы
 * - показ, скрытие колонок
 * - показ дерева треков в левой колонке
 * - показ облако хештегов в правой колонке
 *
 * @version PulsarCMS 1.0
 * @author Александр
 */
import { computed, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { Head, Link, router, usePage } from '@inertiajs/vue3'

import DefaultLayout from '@/Layouts/DefaultLayout.vue'
import Navbar from '@/Partials/Default/Navbar.vue'
import FooterBlog from '@/Partials/Default/FooterBlog.vue'
import Progress from '@/Components/Public/Default/Progress/Progress.vue'
import LeftSidebarSchool from '@/Components/Public/Default/Partials/LeftSidebarSchool.vue'
import RightSidebarSchool from '@/Components/Public/Default/Partials/RightSidebarSchool.vue'
import EntityPageToolbar from '@/Components/Public/Default/PageToolbar/EntityPageToolbar.vue'
import ImageGalleryMain from '@/Components/Public/Default/Media/ImageGalleryMain.vue'
import Pagination from '@/Components/Public/Default/Pagination/Pagination.vue'
import SectionVideoList from '@/Components/Public/Default/Blog/BlogVideo/SectionVideoList.vue'
import SectionBanners from '@/Components/Public/Default/Blog/BlogBanner/SectionBanners.vue'
import LikeButtonEntity from '@/Components/Public/Like/LikeButtonEntity.vue'

import InstructorCourseGrid from '@/Components/Public/Default/School/SchoolInstructor/InstructorCourseGrid.vue'
import InstructorCourseRows from '@/Components/Public/Default/School/SchoolInstructor/InstructorCourseRows.vue'
import TrackGrid from '@/Components/Public/Default/School/SchoolTrack/TrackGrid.vue'
import TrackRows from '@/Components/Public/Default/School/SchoolTrack/TrackRows.vue'

const { t } = useI18n()

/** Props */
const props = defineProps({
    title: String,
    canLogin: Boolean,
    canRegister: Boolean,

    track: { type: Object, default: () => ({}) },

    courses: { type: Object, default: () => ({}) },
    coursesFound: { type: Number, default: 0 },
    filters: { type: Object, default: () => ({}) },

    trackTree: { type: Array, default: () => [] },

    mainVideos: { type: Array, default: () => [] },
    mainBanners: { type: Array, default: () => [] },
})

/** Иерархия треков */
const trackTree = computed(() => Array.isArray(props.trackTree) ? props.trackTree : [])

/** Режим показа карточками/в строку */
const VIEW_KEY = 'public_track_courses_view'
const viewMode = ref(localStorage.getItem(VIEW_KEY) || 'grid')

watch(viewMode, (v) => {
    localStorage.setItem(VIEW_KEY, v)
})

/** Данные курсов */
const coursesData = computed(() => {
    const data = props.courses?.data
    return Array.isArray(data) ? data : []
})

/** Текущая страница, Количество страниц */
const currentPage = computed(() => {
    return Number(props.courses?.meta?.current_page ?? props.courses?.current_page ?? 1) || 1
})

const lastPage = computed(() => {
    return Number(props.courses?.meta?.last_page ?? props.courses?.last_page ?? 1) || 1
})

const perPageCourses = computed(() => {
    const val = Number(props.filters?.per_page_courses ?? 12)
    return Number.isFinite(val) ? val : 12
})

/** Поиск */
const qCourses = ref(String(props.filters?.q_courses ?? ''))

/** Сортировка */
const DEFAULT_SORT = 'date_desc'
const sortCourses = ref(String(props.filters?.sort_courses ?? DEFAULT_SORT))

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

/** Поисковый запрос */
const submitCourseSearch = () => {
    router.get(
        route('public.schoolTracks.show', props.track.slug),
        {
            q_courses: qCourses.value || undefined,
            sort_courses: sortCourses.value || undefined,
            per_page_courses: perPageCourses.value,
            page_courses: 1,
        },
        { preserveState: true, replace: true, preserveScroll: true }
    )
}

/** Сброс поиска */
const resetCourseSearch = () => {
    qCourses.value = ''
    sortCourses.value = DEFAULT_SORT

    router.get(
        route('public.schoolTracks.show', props.track.slug),
        {
            per_page_courses: perPageCourses.value,
            sort_courses: sortCourses.value,
            page_courses: 1,
        },
        { preserveState: true, replace: true, preserveScroll: true }
    )
}

/** Пагинация: prev / input / next */
const goToPage = (page) => {
    const p = Number(page)
    if (!Number.isFinite(p)) return

    const safe = Math.max(1, Math.min(p, lastPage.value))

    router.get(
        route('public.schoolTracks.show', props.track.slug),
        {
            q_courses: qCourses.value || undefined,
            sort_courses: sortCourses.value || undefined,
            per_page_courses: perPageCourses.value,
            page_courses: safe,
        },
        { preserveState: true, replace: true, preserveScroll: true }
    )
}

const goPrev = () => {
    if (currentPage.value <= 1) return
    goToPage(currentPage.value - 1)
}

const goNext = () => {
    if (currentPage.value >= lastPage.value) return
    goToPage(currentPage.value + 1)
}

/** показ/скрытие колонок */
const { siteSettings } = usePage().props

const showLeft = computed(() =>
    !siteSettings?.ViewLeftColumn || siteSettings.ViewLeftColumn === 'true'
)

const showRight = computed(() =>
    !siteSettings?.ViewRightColumn || siteSettings.ViewRightColumn === 'true'
)

/** состояние сайдбаров */
const leftCollapsed = ref(false)
const rightCollapsed = ref(false)

/** добавление третьей карточки в ряд при свернутой колонке */
const gridCols = computed(() => {
    const leftExpanded = showLeft.value && !leftCollapsed.value
    const rightExpanded = showRight.value && !rightCollapsed.value

    return leftExpanded && rightExpanded ? 2 : 3
})

/** Количество курсов */
const coursesCount = computed(() => {
    return Number(props.track?.courses_count ?? props.coursesFound ?? 0) || 0
})

const hasCourses = computed(() => coursesCount.value > 0)

/** Изображение треков */
const trackImages = computed(() => {
    return Array.isArray(props.track?.images) ? props.track.images : []
})

const hasTrackImages = computed(() => {
    return trackImages.value.length > 0
})
</script>

<template>
    <!-- SEO -->
    <Head>
        <title>{{ track.name || '' }}</title>
        <meta name="title" :content="track.meta_title || track.name || ''" />
        <meta name="keywords" :content="track.meta_keywords || ''" />
        <meta name="description" :content="track.meta_desc || track.short || ''" />

        <meta property="og:title" :content="track.meta_title || track.name || ''" />
        <meta property="og:description" :content="track.meta_desc || track.short || ''" />
        <meta property="og:type" content="website" />
        <meta property="og:url" :content="`/school/tracks/${track.slug || ''}`" />
        <meta property="og:image" :content="track?.images?.[0]?.url || track?.images?.[0]?.image_url || ''" />
        <meta property="og:locale" :content="track.locale || 'ru_RU'" />

        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="track.meta_title || track.name || ''" />
        <meta name="twitter:description" :content="track.meta_desc || track.short || ''" />
        <meta name="twitter:image" :content="track?.images?.[0]?.url || track?.images?.[0]?.image_url || ''" />

        <meta name="DC.title" :content="track.meta_title || track.name || ''" />
        <meta name="DC.description" :content="track.meta_desc || track.short || ''" />
        <meta name="DC.identifier" :content="`/school/tracks/${track.slug || ''}`" />
        <meta name="DC.language" :content="track.locale || 'ru'" />
    </Head>

    <DefaultLayout :title="title" :can-login="canLogin" :can-register="canRegister">
        <!-- Шапка -->
        <Navbar />

        <div class="min-h-screen px-1.5">
            <main class="mx-auto flex flex-col lg:flex-row gap-4 tracking-wider">
                <!-- Левая колонка -->
                <aside
                    v-if="showLeft"
                    class="shrink-0 mt-12 sm:mt-16 pl-3 transition-all duration-300"
                    :class="leftCollapsed ? 'lg:w-10' : 'lg:w-64'"
                >
                    <LeftSidebarSchool
                        :track-tree="trackTree"
                        @collapsed="leftCollapsed = $event"
                    />
                </aside>

                <!-- Центральный контент -->
                <div class="w-full lg:mt-16 pb-6 slate-1">
                    <div class="mx-auto max-w-6xl">

                        <!-- Хлебные крошки -->
                        <nav class="text-sm mb-3" aria-label="Breadcrumb">
                            <ol class="flex items-center font-semibold">
                                <li>
                                    <Link :href="route('home')" class="breadcrumb-link hover:underline">
                                        {{ t('home') }}
                                    </Link>
                                </li>
                                <li><span class="mx-2 breadcrumbs">/</span></li>
                                <li>
                                    <Link :href="route('public.schoolTracks.index')"
                                          class="breadcrumb-link hover:underline">
                                        {{ t('tracks') }}
                                    </Link>
                                </li>
                                <li><span class="mx-2 breadcrumbs">/</span></li>
                                <li class="breadcrumbs">
                                    {{ track.name }}
                                </li>
                            </ol>
                        </nav>

                        <!-- Главное изображение/Слайдер -->
                        <div
                            v-if="hasTrackImages"
                            class="flex items-center justify-center"
                        >
                            <div class="w-full">
                                <ImageGalleryMain
                                    :images="trackImages"
                                    :alt="track.name"
                                    rounded-class="rounded-lg"
                                    shadow-class="shadow-lg shadow-gray-400 dark:shadow-gray-700"
                                    img-class="w-full h-full object-cover"
                                />
                            </div>
                        </div>

                        <!-- Панель заголовка -->
                        <div class="flex items-center justify-between gap-1">
                            <!-- Количество курсов -->
                            <div
                                :title="t('courses')"
                                class="flex items-center justify-center gap-1"
                            >
                                <svg class="h-5 w-5 text-slate-600/85 dark:text-slate-200/85"
                                     fill="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                                </svg>
                                <span v-if="hasCourses" class="text-center text-sm text-gray-500">
                                    {{ coursesCount }} ·
                                </span>
                            </div>

                            <!-- Иконка и заголовок -->
                            <div class="flex flex-wrap items-center justify-center gap-3 title my-3">
                                <h1 class="text-2xl font-bold">
                                    {{ track.name }}
                                </h1>
                            </div>

                            <!-- Количество просмотров -->
                            <div :title="t('views')"
                                 class="flex items-center justify-center gap-1">
                                <svg class="h-4 w-4 text-slate-600/85 dark:text-slate-200/85"
                                     xmlns="http://www.w3.org/2000/svg"
                                     viewBox="0 0 576 512"
                                     fill="currentColor">
                                    <path d="M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z" />
                                </svg>
                                <span class="text-center text-sm text-gray-500">
                                    {{ track.views }} ·
                                </span>
                            </div>
                        </div>

                        <!-- Описание -->
                        <div
                            v-if="track.description"
                            class="my-3 text-sm subtitle text-center"
                            v-html="track.description"
                            itemprop="articleBody"
                        />

                        <!-- Like -->
                        <div class="flex justify-center items-center">
                            <LikeButtonEntity
                                :likes-count="track.likes_count || track.likes || 0"
                                :already-liked="track.already_liked || false"
                                route-name="public.schoolTracks.like"
                                :route-params="track.id"
                                :title="t('like')"
                                icon-class="w-4 h-4"
                            />
                        </div>

                        <!-- Поиск, количество, сортировка, вид -->
                        <EntityPageToolbar
                            v-if="hasCourses"
                            v-model="qCourses"
                            :found="coursesFound"
                            :view-mode="viewMode"
                            :sort-value="sortCourses"
                            :sort-options="courseSortOptions"
                            :default-sort="DEFAULT_SORT"
                            :found-label="t('courses')"
                            :search-placeholder="t('searchByName')"
                            @submit="submitCourseSearch"
                            @reset="resetCourseSearch"
                            @update:viewMode="viewMode = $event"
                            @update:sortValue="sortCourses = $event"
                        />

                        <!-- Нет данных -->
                        <div
                            v-if="coursesData.length === 0"
                            class="mt-6 text-center text-slate-700 dark:text-slate-300"
                        >
                            {{ t('noData') }}
                        </div>

                        <!-- Показ grid/rows -->
                        <div v-else>
                            <InstructorCourseGrid
                                v-if="viewMode === 'grid'"
                                :courses="coursesData"
                                :cols="gridCols"
                            />

                            <InstructorCourseRows
                                v-else
                                :courses="coursesData"
                            />
                        </div>

                        <!-- Пагинация -->
                        <Pagination
                            v-if="hasCourses"
                            :current-page="currentPage"
                            :last-page="lastPage"
                            :found="coursesFound"
                            @prev="goPrev"
                            @next="goNext"
                            @go="goToPage"
                        />

                        <div v-if="track.children?.length">
                            <div
                                class="mt-4 flex flex-wrap items-center justify-center gap-3
                                       text-slate-700/85 dark:text-slate-300/85"
                            >
                                <svg class="h-8 w-8 opacity-70"
                                     fill="currentColor"
                                     viewBox="0 0 640 512">
                                    <path d="M622.34 153.2L343.4 67.5c-15.2-4.67-31.6-4.67-46.79 0L17.66 153.2c-23.54 7.23-23.54 38.36 0 45.59l48.63 14.94c-10.67 13.19-17.23 29.28-17.88 46.9C38.78 266.15 32 276.11 32 288c0 10.78 5.68 19.85 13.86 25.65L20.33 428.53C18.11 438.52 25.71 448 35.94 448h56.11c10.24 0 17.84-9.48 15.62-19.47L82.14 313.65C90.32 307.85 96 298.78 96 288c0-11.57-6.47-21.25-15.66-26.87.76-15.02 8.44-28.3 20.69-36.72L296.6 284.5c9.06 2.78 26.44 6.25 46.79 0l278.95-85.7c23.55-7.24 23.55-38.36 0-45.6zM352.79 315.09c-28.53 8.76-52.84 3.92-65.59 0l-145.02-44.55L128 384c0 35.35 85.96 64 192 64s192-28.65 192-64l-14.18-113.47-145.03 44.56z"></path>
                                </svg>

                                <h2 class="text-xl font-semibold">
                                    {{ t('subheadings') }}
                                </h2>
                            </div>

                            <div class="mt-6">
                                <TrackGrid
                                    v-if="viewMode === 'grid'"
                                    :tracks="track.children"
                                    :cols="gridCols"
                                />

                                <TrackRows
                                    v-else
                                    :tracks="track.children"
                                />
                            </div>
                        </div>

                        <!-- Главные видео, баннеры -->
                        <SectionVideoList :videos="mainVideos" />
                        <SectionBanners :banners="mainBanners" />
                    </div>
                </div>

                <!-- Правая колонка -->
                <aside
                    v-if="showRight"
                    class="shrink-0 lg:mt-16 pr-3 transition-all duration-300"
                    :class="rightCollapsed ? 'lg:w-10' : 'lg:w-64'"
                >
                    <RightSidebarSchool @collapsed="rightCollapsed = $event" />
                </aside>
            </main>
        </div>

        <!-- Подвал и кнопка с прогрессом -->
        <FooterBlog />
        <Progress />
    </DefaultLayout>
</template>
