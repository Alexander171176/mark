<script setup>
/**
 * Страница конкретного инструктора
 * - шапка, центральная часть, подвал
 * - светлый, тёмный режим
 * - серверный поиск по курсам
 * - серверная пагинация курсов
 * - серверная сортировка курсов
 * - показ курсов карточками / в строку
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
import Pagination from '@/Components/Public/Default/Pagination/Pagination.vue'
import SectionVideoList from '@/Components/Public/Default/Blog/Video/SectionVideoList.vue'
import SectionBanners from '@/Components/Public/Default/Blog/Banner/SectionBanners.vue'
import InstructorCourseGrid from '@/Components/Public/Default/School/Instructor/InstructorCourseGrid.vue'
import InstructorCourseRows from '@/Components/Public/Default/School/Instructor/InstructorCourseRows.vue'
import ImageGalleryMain from '@/Components/Public/Default/Media/ImageGalleryMain.vue'

const { t } = useI18n()

const props = defineProps({
    title: String,
    canLogin: Boolean,
    canRegister: Boolean,

    instructorProfile: { type: Object, default: () => ({}) },

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
const VIEW_KEY = 'public_view'
const viewMode = ref(localStorage.getItem(VIEW_KEY) || 'grid')

watch(viewMode, (v) => {
    localStorage.setItem(VIEW_KEY, v)
})

/** Данные курсов */
const coursesData = computed(() => {
    const data = props.courses?.data
    return Array.isArray(data) ? data : []
})

/** Пагинация */
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

const courseSortOptions = [
    { value: 'sort_asc', label: t('sortDefault') },
    { value: 'sort_desc', label: t('sortReverse') },
    { value: 'title_asc', label: t('sortNameAsc') },
    { value: 'title_desc', label: t('sortNameDesc') },
    { value: 'views_desc', label: t('sortPopularFirst') },
    { value: 'views_asc', label: t('sortUnpopularFirst') },
    { value: 'likes_desc', label: t('sortLikesDesc') },
    { value: 'likes_asc', label: t('sortLikesAsc') },
    { value: 'rating_desc', label: t('ratingDesc') },
    { value: 'rating_asc', label: t('ratingAsc') },
    { value: 'popularity_desc', label: t('sortPopularFirst') },
    { value: 'popularity_asc', label: t('sortUnpopularFirst') },
    { value: 'students_desc', label: t('sortMostStudents') },
    { value: 'students_asc', label: t('sortLeastStudents') },
    { value: 'duration_desc', label: t('sortLongest') },
    { value: 'duration_asc', label: t('sortShortest') },
    { value: 'date_desc', label: t('sortNewestFirst') },
    { value: 'date_asc', label: t('sortOldestFirst') },
]

/** Поисковый запрос */
const submitCourseSearch = () => {
    router.get(
        route('public.instructors.show', {
            slug: props.instructorProfile.slug,
        }),
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
        route('public.instructors.show', {
            slug: props.instructorProfile.slug,
        }),
        {
            per_page_courses: perPageCourses.value,
            sort_courses: sortCourses.value,
            page_courses: 1,
        },
        { preserveState: true, replace: true, preserveScroll: true }
    )
}

/** Пагинация */
const pageInput = ref(currentPage.value)

watch(currentPage, (v) => {
    pageInput.value = v
})

const goToPage = (page) => {
    const p = Number(page)
    if (!Number.isFinite(p)) return

    const safe = Math.max(1, Math.min(p, lastPage.value))

    router.get(
        route('public.instructors.show', {
            slug: props.instructorProfile.slug,
        }),
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

const submitPageInput = () => goToPage(pageInput.value)

/** Изображения */
const images = computed(() =>
    Array.isArray(props.instructorProfile?.images) ? props.instructorProfile.images : []
)

const primaryImage = computed(() => images.value?.[0] ?? null)

const getImageUrl = (image) => {
    return image?.url || image?.preview || image?.image_url || null
}

/** Соцсети */
const socialLinks = computed(() => props.instructorProfile?.social_links ?? {})

const normalizedSocialLinks = computed(() => {
    if (Array.isArray(socialLinks.value)) {
        return socialLinks.value
            .filter(item => item?.url)
            .map(item => ({
                label: item.label || item.name || item.platform || 'Ссылка',
                url: item.url,
            }))
    }

    return Object.entries(socialLinks.value || {})
        .filter(([, value]) => !!value)
        .map(([key, value]) => ({
            label: key,
            url: value,
        }))
})

const detectSocialType = (item) => {
    const label = String(item?.label || '').toLowerCase()
    const url = String(item?.url || '').toLowerCase()

    if (label.includes('instagram') || url.includes('instagram.com')) return 'instagram'
    if (label.includes('facebook') || url.includes('facebook.com')) return 'facebook'
    if (label.includes('telegram') || url.includes('t.me') || url.includes('telegram.me')) return 'telegram'
    if (label.includes('youtube') || url.includes('youtube.com') || url.includes('youtu.be')) return 'youtube'
    if (label.includes('tiktok') || url.includes('tiktok.com')) return 'tiktok'
    if (label.includes('linkedin') || url.includes('linkedin.com')) return 'linkedin'
    if (label.includes('x') || label.includes('twitter') || url.includes('twitter.com') || url.includes('x.com')) return 'x'
    if (label.includes('whatsapp') || url.includes('wa.me') || url.includes('whatsapp.com')) return 'whatsapp'
    if (label.includes('vk') || label.includes('vkontakte') || url.includes('vk.com')) return 'vk'
    if (label.includes('site') || label.includes('website')) return 'website'

    return 'link'
}

const getSocialLabel = (item) => {
    const type = detectSocialType(item)

    switch (type) {
        case 'instagram': return 'Instagram'
        case 'facebook': return 'Facebook'
        case 'telegram': return 'Telegram'
        case 'youtube': return 'YouTube'
        case 'tiktok': return 'TikTok'
        case 'linkedin': return 'LinkedIn'
        case 'x': return 'X'
        case 'whatsapp': return 'WhatsApp'
        case 'vk': return 'VK'
        case 'website': return 'Сайт'
        default: return item?.label || 'Ссылка'
    }
}

/** Форматирование */
const formattedRating = computed(() => {
    const avg = props.instructorProfile?.rating?.avg
    if (avg === null || avg === undefined) return '—'
    return Number(avg).toFixed(1)
})

/** Количество курсов */
const coursesCount = computed(() => {
    const count = props.instructorProfile?.courses_count ?? props.coursesFound ?? 0
    return Number(count) || 0
})

const hasCourses = computed(() => coursesCount.value > 0)

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

/** добавление третей карточки в ряд при свернутой колонке */
const gridCols = computed(() => {
    const leftExpanded = showLeft.value && !leftCollapsed.value
    const rightExpanded = showRight.value && !rightCollapsed.value

    return leftExpanded && rightExpanded ? 2 : 3
})

const instructorImages = computed(() => {
    return Array.isArray(props.instructorProfile?.images) ? props.instructorProfile.images : []
})

const hasInstructorImages = computed(() => {
    return instructorImages.value.length > 0
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
        <title>{{ instructorProfile.public_name || instructorProfile.title || 'Инструктор' }}</title>
        <meta name="title" :content="instructorProfile.public_name || instructorProfile.title || ''" />
        <meta name="keywords" :content="instructorProfile.meta_keywords || instructorProfile.meta_title ||  ''" />
        <meta name="description" :content="instructorProfile.meta_desc || instructorProfile.short || ''" />

        <meta property="og:title" :content="instructorProfile.meta_title || instructorProfile.public_name || instructorProfile.title || ''" />
        <meta property="og:description" :content="instructorProfile.meta_desc || instructorProfile.short || ''" />
        <meta property="og:type" content="website" />
        <meta property="og:url" :content="`/instructors/${instructorProfile.slug || ''}`" />
        <meta property="og:image" :content="getImageUrl(primaryImage) || ''" />
        <meta property="og:locale" :content="instructorProfile.locale || 'ru_RU'" />

        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="instructorProfile.meta_title || instructorProfile.public_name || instructorProfile.title || ''" />
        <meta name="twitter:description" :content="instructorProfile.meta_desc || instructorProfile.short || ''" />
        <meta name="twitter:image" :content="getImageUrl(primaryImage) || ''" />

        <meta name="DC.title" :content="instructorProfile.meta_title || instructorProfile.public_name || instructorProfile.title || ''" />
        <meta name="DC.description" :content="instructorProfile.meta_desc || instructorProfile.short || ''" />
        <meta name="DC.identifier" :content="`/instructors/${instructorProfile.slug || ''}`" />
        <meta name="DC.language" :content="instructorProfile.locale || 'ru'" />
    </Head>

    <DefaultLayout :title="title" :can-login="canLogin" :can-register="canRegister">
        <Navbar />

        <div class="min-h-screen px-1.5">
            <main class="mx-auto flex flex-col lg:flex-row gap-4 tracking-wider">
                <!-- LEFT -->
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

                <!-- CENTER -->
                <div class="w-full lg:mt-16 pb-6 slate-1">
                    <div class="mx-auto max-w-6xl">

                        <!-- Breadcrumbs -->
                        <nav class="text-sm mb-3" aria-label="Breadcrumb">
                            <ol class="flex items-center font-semibold">
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
                                        :href="route('public.instructors.index')"
                                        class="breadcrumb-link hover:underline"
                                    >
                                        Инструкторы
                                    </Link>
                                </li>
                                <li><span class="mx-2 breadcrumbs">/</span></li>
                                <li class="breadcrumbs">
                                    {{ instructorProfile.public_name || instructorProfile.title }}
                                </li>
                            </ol>
                        </nav>

                        <!-- Image -->
                        <div
                            v-if="hasInstructorImages"
                            class="flex items-center justify-center"
                        >
                            <div class="w-full">
                                <ImageGalleryMain
                                    :images="images"
                                    :alt="instructorProfile.public_name || instructorProfile.title"
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
                                <svg class="h-5 w-5 text-slate-600/85 dark:text-slate-200/85"
                                     fill="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                                </svg>
                                <span
                                    v-if="hasCourses"
                                    class="text-center text-sm text-gray-500"
                                >
                                    {{ coursesCount }} ·
                                </span>
                            </div>

                            <div class="my-3 flex flex-wrap items-center justify-center gap-3 title">
                                <h1 class="text-2xl font-bold">
                                    {{ instructorProfile.public_name || instructorProfile.title }}
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
                                    <path d="M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z" />
                                </svg>
                                <span class="text-center text-sm text-gray-500">
                                    {{ instructorProfile.views ?? 0 }} ·
                                </span>
                            </div>
                        </div>

                        <!-- Details -->
                        <div class="mb-4 flex flex-wrap items-center justify-center gap-3
                                    text-sm text-slate-600 dark:text-slate-300">

                            <div class="rounded-sm border border-slate-400 px-3 py-1
                                        flex items-center justify-center gap-0.5">
                                {{t('rating')}}:
                                <svg
                                    viewBox="0 0 24 24"
                                    class="shrink-0 h-4 w-4">
                                    <path class="fill-current text-red-400 dark:text-red-300"
                                          d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"></path>
                                </svg>
                                {{ formattedRating }}
                                ({{ instructorProfile.rating?.count ?? 0 }})
                            </div>

                            <div class="flex justify-center items-center">
                                <div class="relative inline-flex text-center px-3 py-1
                                            rounded bg-emerald-500">
                                    <div class="absolute w-3 h-3 rounded-full
                                                bg-white left-0
                                                -translate-x-1/2 top-1/2 -translate-y-1/2"
                                         aria-hidden="true">
                                    </div>
                                    <div class="absolute w-3 h-3 rounded-full
                                                bg-white right-0
                                                translate-x-1/2 top-1/2 -translate-y-1/2"
                                         aria-hidden="true">
                                    </div>
                                    <span class="text-sm text-emerald-50 font-medium">
                                        {{t('experienceYears')}}:
                                        {{ instructorProfile.experience_years ?? 0 }}
                                    </span>
                                </div>
                            </div>

                        </div>

                        <!-- Bio -->
                        <div
                            v-if="instructorProfile.bio"
                            class="mb-6 rounded-md border border-gray-200
                                   bg-white p-4 text-sm leading-7
                                   text-slate-700 shadow-sm
                                   dark:border-gray-700 dark:bg-gray-900 dark:text-slate-300"
                        >
                            {{ instructorProfile.bio }}
                        </div>

                        <!-- Social -->
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
                                       text-sm font-semibold inline-flex items-center gap-2"
                            >
                                <!-- Instagram -->
                                <svg
                                    v-if="detectSocialType(item) === 'instagram'"
                                    class="h-4 w-4"
                                    viewBox="0 0 24 24"
                                    fill="currentColor"
                                    aria-hidden="true"
                                >
                                    <path d="M7.75 2h8.5A5.75 5.75 0 0 1 22 7.75v8.5A5.75 5.75 0 0 1 16.25 22h-8.5A5.75 5.75 0 0 1 2 16.25v-8.5A5.75 5.75 0 0 1 7.75 2Zm0 1.5A4.25 4.25 0 0 0 3.5 7.75v8.5A4.25 4.25 0 0 0 7.75 20.5h8.5a4.25 4.25 0 0 0 4.25-4.25v-8.5A4.25 4.25 0 0 0 16.25 3.5h-8.5Zm8.75 1.75a1 1 0 1 1 0 2a1 1 0 0 1 0-2ZM12 7a5 5 0 1 1 0 10a5 5 0 0 1 0-10Zm0 1.5A3.5 3.5 0 1 0 12 15.5A3.5 3.5 0 0 0 12 8.5Z"/>
                                </svg>

                                <!-- Facebook -->
                                <svg
                                    v-else-if="detectSocialType(item) === 'facebook'"
                                    class="h-4 w-4"
                                    viewBox="0 0 24 24"
                                    fill="currentColor"
                                    aria-hidden="true"
                                >
                                    <path d="M13.5 22v-8h2.7l.4-3h-3.1V9.1c0-.9.3-1.6 1.6-1.6H16.7V4.8c-.3 0-1.2-.1-2.3-.1c-2.3 0-3.9 1.4-3.9 4V11H8v3h2.5v8h3Z"/>
                                </svg>

                                <!-- Telegram -->
                                <svg
                                    v-else-if="detectSocialType(item) === 'telegram'"
                                    class="h-4 w-4"
                                    viewBox="0 0 24 24"
                                    fill="currentColor"
                                    aria-hidden="true"
                                >
                                    <path d="M21.5 4.5L18.3 19c-.2 1-.8 1.2-1.6.8l-4.7-3.4l-2.3 2.2c-.3.3-.5.5-1 .5l.3-4.8l8.8-7.9c.4-.4-.1-.6-.6-.3L6.3 13L1.7 11.6c-1-.3-1-1 .2-1.5L20 3.2c.8-.3 1.6.2 1.5 1.3Z"/>
                                </svg>

                                <!-- YouTube -->
                                <svg
                                    v-else-if="detectSocialType(item) === 'youtube'"
                                    class="h-4 w-4"
                                    viewBox="0 0 24 24"
                                    fill="currentColor"
                                    aria-hidden="true"
                                >
                                    <path d="M21.8 8s-.2-1.4-.8-2c-.8-.8-1.7-.8-2.1-.9C15.9 4.8 12 4.8 12 4.8h0s-3.9 0-6.9.3c-.4.1-1.3.1-2.1.9c-.6.6-.8 2-.8 2S2 9.6 2 11.2v1.5C2 14.4 2.2 16 2.2 16s.2 1.4.8 2c.8.8 1.9.8 2.4.9c1.7.2 6.6.3 6.6.3s3.9 0 6.9-.3c.4-.1 1.3-.1 2.1-.9c.6-.6.8-2 .8-2s.2-1.6.2-3.3v-1.5C22 9.6 21.8 8 21.8 8ZM9.8 15.2V8.8l6.2 3.2l-6.2 3.2Z"/>
                                </svg>

                                <!-- TikTok -->
                                <svg
                                    v-else-if="detectSocialType(item) === 'tiktok'"
                                    class="h-4 w-4"
                                    viewBox="0 0 24 24"
                                    fill="currentColor"
                                    aria-hidden="true"
                                >
                                    <path d="M16.5 3c.3 2.1 1.5 3.5 3.5 3.8v2.7c-1.5.1-2.9-.3-4.2-1.1v5.1c0 3.8-4.1 6.2-7.4 4.5c-3.2-1.6-3.5-6.4-.5-8.4c.9-.6 1.9-.9 3-.9v2.8c-.5 0-1 .1-1.4.4c-1.3.9-.9 3 .6 3.4c1.6.5 3.1-.7 3.1-2.3V3h3.3Z"/>
                                </svg>

                                <!-- LinkedIn -->
                                <svg
                                    v-else-if="detectSocialType(item) === 'linkedin'"
                                    class="h-4 w-4"
                                    viewBox="0 0 24 24"
                                    fill="currentColor"
                                    aria-hidden="true"
                                >
                                    <path d="M6.94 8.5H3.56V19h3.38V8.5ZM5.25 3A1.97 1.97 0 1 0 5.3 6.94A1.97 1.97 0 0 0 5.25 3ZM20.44 12.36c0-3.13-1.67-4.58-3.9-4.58c-1.8 0-2.6 1-3.05 1.7V8.5h-3.38V19h3.38v-5.2c0-1.37.26-2.7 1.96-2.7c1.67 0 1.7 1.56 1.7 2.8V19h3.39l-.01-6.64Z"/>
                                </svg>

                                <!-- X / Twitter -->
                                <svg
                                    v-else-if="detectSocialType(item) === 'x'"
                                    class="h-4 w-4"
                                    viewBox="0 0 24 24"
                                    fill="currentColor"
                                    aria-hidden="true"
                                >
                                    <path d="M18.9 2H22l-6.8 7.8L23 22h-6.1l-4.8-6.3L6.6 22H3.5l7.2-8.2L1 2h6.3l4.4 5.8L18.9 2Zm-1.1 18h1.7L6.4 3.9H4.6L17.8 20Z"/>
                                </svg>

                                <!-- WhatsApp -->
                                <svg
                                    v-else-if="detectSocialType(item) === 'whatsapp'"
                                    class="h-4 w-4"
                                    viewBox="0 0 24 24"
                                    fill="currentColor"
                                    aria-hidden="true"
                                >
                                    <path d="M20.5 3.5A11 11 0 0 0 3.3 16.8L2 22l5.3-1.4A11 11 0 1 0 20.5 3.5ZM12 20a8.9 8.9 0 0 1-4.5-1.2l-.3-.2l-3.1.8l.8-3l-.2-.3A9 9 0 1 1 12 20Zm4.9-6.8c-.3-.2-1.7-.8-2-.9c-.3-.1-.5-.2-.7.2s-.8.9-.9 1.1c-.2.2-.3.2-.6.1c-.3-.2-1.2-.4-2.2-1.4c-.8-.7-1.4-1.7-1.6-2c-.2-.3 0-.4.1-.6l.5-.5c.2-.2.2-.3.3-.5c.1-.2 0-.4 0-.5c-.1-.2-.7-1.8-.9-2.4c-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.7.3c-.2.2-.9.9-.9 2.1s.9 2.4 1 2.6c.1.2 1.8 2.9 4.4 4c2.6 1.1 2.6.7 3.1.7c.5-.1 1.7-.7 1.9-1.4c.2-.7.2-1.3.1-1.4c-.1-.1-.3-.2-.6-.4Z"/>
                                </svg>

                                <!-- VK -->
                                <svg
                                    v-else-if="detectSocialType(item) === 'vk'"
                                    class="h-4 w-4"
                                    viewBox="0 0 24 24"
                                    fill="currentColor"
                                    aria-hidden="true"
                                >
                                    <path d="M12.8 17c-6.1 0-9.6-4.2-9.7-11h3c.1 5 2.3 7.1 4 7.5V6h2.8v4.3c1.6-.2 3.4-2.2 3.9-4.3h2.8c-.4 2.6-2.4 4.6-3.8 5.4c1.4.7 3.7 2.5 4.6 5.6h-3.1c-.6-2-2.2-3.6-4.4-3.8V17h-.1Z"/>
                                </svg>

                                <!-- Website / default -->
                                <svg
                                    v-else
                                    class="h-4 w-4"
                                    fill="currentColor"
                                    aria-hidden="true"
                                    viewBox="0 0 496 512">
                                    <path d="M165.9 397.4c0 2-2.3 3.6-5.2 3.6-3.3.3-5.6-1.3-5.6-3.6 0-2 2.3-3.6 5.2-3.6 3-.3 5.6 1.3 5.6 3.6zm-31.1-4.5c-.7 2 1.3 4.3 4.3 4.9 2.6 1 5.6 0 6.2-2s-1.3-4.3-4.3-5.2c-2.6-.7-5.5.3-6.2 2.3zm44.2-1.7c-2.9.7-4.9 2.6-4.6 4.9.3 2 2.9 3.3 5.9 2.6 2.9-.7 4.9-2.6 4.6-4.6-.3-1.9-3-3.2-5.9-2.9zM244.8 8C106.1 8 0 113.3 0 252c0 110.9 69.8 205.8 169.5 239.2 12.8 2.3 17.3-5.6 17.3-12.1 0-6.2-.3-40.4-.3-61.4 0 0-70 15-84.7-29.8 0 0-11.4-29.1-27.8-36.6 0 0-22.9-15.7 1.6-15.4 0 0 24.9 2 38.6 25.8 21.9 38.6 58.6 27.5 72.9 20.9 2.3-16 8.8-27.1 16-33.7-55.9-6.2-112.3-14.3-112.3-110.5 0-27.5 7.6-41.3 23.6-58.9-2.6-6.5-11.1-33.3 2.6-67.9 20.9-6.5 69 27 69 27 20-5.6 41.5-8.5 62.8-8.5s42.8 2.9 62.8 8.5c0 0 48.1-33.6 69-27 13.7 34.7 5.2 61.4 2.6 67.9 16 17.7 25.8 31.5 25.8 58.9 0 96.5-58.9 104.2-114.8 110.5 9.2 7.9 17 22.9 17 46.4 0 33.7-.3 75.4-.3 83.6 0 6.5 4.6 14.4 17.3 12.1C428.2 457.8 496 362.9 496 252 496 113.3 383.5 8 244.8 8zM97.2 352.9c-1.3 1-1 3.3.7 5.2 1.6 1.6 3.9 2.3 5.2 1 1.3-1 1-3.3-.7-5.2-1.6-1.6-3.9-2.3-5.2-1zm-10.8-8.1c-.7 1.3.3 2.9 2.3 3.9 1.6 1 3.6.7 4.3-.7.7-1.3-.3-2.9-2.3-3.9-2-.6-3.6-.3-4.3.7zm32.4 35.6c-1.6 1.3-1 4.3 1.3 6.2 2.3 2.3 5.2 2.6 6.5 1 1.3-1.3.7-4.3-1.3-6.2-2.2-2.3-5.2-2.6-6.5-1zm-11.4-14.7c-1.6 1-1.6 3.6 0 5.9 1.6 2.3 4.3 3.3 5.6 2.3 1.6-1.3 1.6-3.9 0-6.2-1.4-2.3-4-3.3-5.6-2z"/>
                                </svg>
                                <span>{{ getSocialLabel(item) }}</span>
                            </a>
                        </div>

                        <!-- Toolbar -->
                        <EntityPageToolbar
                            v-if="hasCourses"
                            v-model="qCourses"
                            :found="coursesFound"
                            :view-mode="viewMode"
                            :sort-value="sortCourses"
                            :sort-options="courseSortOptions"
                            :default-sort="DEFAULT_SORT"
                            @submit="submitCourseSearch"
                            @reset="resetCourseSearch"
                            @update:viewMode="viewMode = $event"
                            @update:sortValue="sortCourses = $event"
                        />

                        <!-- Empty -->
                        <div
                            v-if="coursesData.length === 0"
                            class="mt-6 text-center text-slate-700 dark:text-slate-300"
                        >
                            {{ t('noData') }}
                        </div>

                        <!-- Views -->
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

                        <!-- Pagination -->
                        <Pagination
                            v-if="hasCourses"
                            :current-page="currentPage"
                            :last-page="lastPage"
                            :found="coursesFound"
                            @prev="goPrev"
                            @next="goNext"
                            @go="goToPage"
                        />

                        <SectionVideoList :videos="mainVideosList" />
                        <SectionBanners :banners="mainBannersList" />
                    </div>
                </div>

                <!-- RIGHT -->
                <aside
                    v-if="showRight"
                    class="shrink-0 lg:mt-16 pr-3 transition-all duration-300"
                    :class="rightCollapsed ? 'lg:w-10' : 'lg:w-64'"
                >
                    <RightSidebarSchool @collapsed="rightCollapsed = $event" />
                </aside>
            </main>
        </div>

        <FooterBlog />
        <Progress />
    </DefaultLayout>
</template>
