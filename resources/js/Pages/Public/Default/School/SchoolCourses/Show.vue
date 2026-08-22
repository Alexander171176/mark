<script setup>
/**
 * Страница конкретного курса.
 *
 * Public-контракт:
 *
 * course.translation
 * course.images
 * course.instructorProfile
 * course.tracks
 * course.hashtags
 * course.prices
 * course.reviews
 * course.related_courses
 *
 * Модули пока остаются
 * на текущем старом контракте.
 *
 * @version PulsarCMS 1.0
 * @author Александр
 */

import {
    computed,
    ref,
    watch,
} from 'vue'

import {
    Head,
    Link,
    usePage,
} from '@inertiajs/vue3'

import { useI18n } from 'vue-i18n'

import DefaultLayout from '@/Layouts/DefaultLayout.vue'
import Navbar from '@/Partials/Default/Navbar.vue'
import FooterBlog from '@/Partials/Default/FooterBlog.vue'
import Progress from '@/Components/Public/Default/Progress/Progress.vue'

import LeftSidebarSchool from '@/Components/Public/Default/Partials/LeftSidebarSchool.vue'
import RightSidebarSchool from '@/Components/Public/Default/Partials/RightSidebarSchool.vue'

import LikeButtonEntity from '@/Components/Public/Like/LikeButtonEntity.vue'
import ImageGalleryMain from '@/Components/Public/Default/Media/ImageGalleryMain.vue'

import SectionVideoList from '@/Components/Public/Default/Blog/BlogVideo/SectionVideoList.vue'
import SectionBanners from '@/Components/Public/Default/Blog/BlogBanner/SectionBanners.vue'

import CourseModulesSection from '@/Components/Public/Default/School/SchoolModule/CourseModulesSection.vue'
import InstructorCourseGrid from '@/Components/Public/Default/School/SchoolInstructor/InstructorCourseGrid.vue'

const { t } = useI18n()

const page = usePage()

/* ===================== PROPS ===================== */

const props = defineProps({
    title: String,

    canLogin: Boolean,
    canRegister: Boolean,

    /**
     * Полный Public SchoolCourseResource.
     */
    course: {
        type: Object,
        default: () => ({}),
    },

    /**
     * Модули пока находятся
     * на старом контракте.
     */
    modules: {
        type: [Array, Object],
        default: () => [],
    },

    trackTree: {
        type: Array,
        default: () => [],
    },

    locale: {
        type: String,
        default: 'ru',
    },

    mainVideos: {
        type: Array,
        default: () => [],
    },

    mainBanners: {
        type: Array,
        default: () => [],
    },
})

/* ===================== HELPERS ===================== */

/**
 * Нормализация обычной коллекции
 * и Laravel ResourceCollection.
 */
const normalizeList = (value) => {
    if (Array.isArray(value)) {
        return value
    }

    if (Array.isArray(value?.data)) {
        return value.data
    }

    return []
}

/**
 * Перевод уровня курса.
 */
const translateLevel = (level) => {
    const normalized = String(
        level ?? ''
    )
        .trim()
        .toLowerCase()

    const map = {
        beginner: 'levelBeginner',
        intermediate: 'levelIntermediate',
        advanced: 'levelAdvanced',
    }

    return map[normalized]
        ? t(map[normalized])
        : level
}

/* ===================== COURSE ===================== */

/**
 * Основной объект курса.
 */
const courseData = computed(() =>
    props.course ?? {}
)

/**
 * Resolved Public-перевод.
 *
 * Backend уже выполнил:
 *
 * current locale
 * → fallback locale
 * → первый доступный.
 */
const translation = computed(() =>
    courseData.value?.translation ?? {}
)

/**
 * Публичные текстовые поля курса.
 */
const courseTitle = computed(() =>
    translation.value?.title ?? ''
)

const courseSubtitle = computed(() =>
    translation.value?.subtitle ?? ''
)

const courseShort = computed(() =>
    translation.value?.short ?? ''
)

const courseDescription = computed(() =>
    translation.value?.description ?? ''
)

/* ===================== SEO ===================== */

const seoTitle = computed(() =>
    translation.value?.meta_title
    || courseTitle.value
    || t('courses')
)

const seoDescription = computed(() =>
    translation.value?.meta_desc
    || courseShort.value
    || ''
)

const seoKeywords = computed(() =>
    translation.value?.meta_keywords ?? ''
)

/**
 * Локаль фактически выбранного перевода.
 *
 * Например:
 * страница en,
 * а перевод найден через fallback ru.
 */
const contentLocale = computed(() =>
    translation.value?.locale
    || props.locale
    || 'ru'
)

const ogLocale = computed(() =>
    contentLocale.value === 'ru'
        ? 'ru_RU'
        : contentLocale.value
)

/* ===================== IMAGES ===================== */

/**
 * Изображения курса.
 */
const courseImages = computed(() =>
    normalizeList(
        courseData.value?.images
    )
)

/**
 * Первое изображение уже соответствует
 * pivot order backend-связи.
 */
const firstCourseImage = computed(() =>
    courseImages.value[0] ?? null
)

const hasCourseImages = computed(() =>
    courseImages.value.length > 0
)

const firstCourseImageUrl = computed(() =>
    firstCourseImage.value?.webp_url
    || firstCourseImage.value?.image_url
    || firstCourseImage.value?.url
    || firstCourseImage.value?.thumb_url
    || ''
)

/* ===================== INSTRUCTOR ===================== */

/**
 * Public Shared Resource инструктора.
 */
const instructorProfile = computed(() =>
    courseData.value?.instructorProfile
    ?? null
)

/**
 * Изображения инструктора.
 */
const instructorImages = computed(() =>
    normalizeList(
        instructorProfile.value?.images
    )
)

/**
 * images() уже сортируется backend
 * по pivot order.
 */
const instructorPrimaryImage = computed(() =>
    instructorImages.value[0] ?? null
)

const instructorImageUrl = computed(() =>
    instructorPrimaryImage.value?.webp_url
    || instructorPrimaryImage.value?.image_url
    || instructorPrimaryImage.value?.url
    || instructorPrimaryImage.value?.thumb_url
    || null
)

/**
 * Имя инструктора по новому контракту.
 */
const instructorName = computed(() =>
    instructorProfile.value?.translation?.title
    || instructorProfile.value?.user?.name
    || t('instructor')
)

/* ===================== RELATIONS ===================== */

/**
 * Хештеги курса.
 */
const hashtags = computed(() =>
    normalizeList(
        courseData.value?.hashtags
    )
)

/**
 * Отзывы курса.
 */
const reviews = computed(() =>
    normalizeList(
        courseData.value?.reviews
    )
)

/**
 * Рекомендованные курсы.
 */
const relatedCourses = computed(() =>
    normalizeList(
        courseData.value?.related_courses
    )
)

/**
 * Модули пока оставляем
 * на текущем контракте.
 */
const modulesList = computed(() =>
    normalizeList(
        props.modules
    )
)

/**
 * Дерево треков для левого сайдбара.
 */
const trackTree = computed(() =>
    Array.isArray(props.trackTree)
        ? props.trackTree
        : []
)

/**
 * Главные видео и баннеры.
 */
const mainVideosList = computed(() =>
    normalizeList(
        props.mainVideos
    )
)

const mainBannersList = computed(() =>
    normalizeList(
        props.mainBanners
    )
)

/* ===================== SIDEBARS ===================== */

const { siteSettings } = page.props

/**
 * Разрешена ли левая колонка
 * глобальными настройками сайта.
 */
const showLeft = computed(() => {
    return !siteSettings?.ViewLeftColumn
        || siteSettings.ViewLeftColumn === 'true'
})

/**
 * Разрешена ли правая колонка
 * глобальными настройками сайта.
 */
const showRight = computed(() => {
    return !siteSettings?.ViewRightColumn
        || siteSettings.ViewRightColumn === 'true'
})

/**
 * Общие ключи для всех Public-страниц.
 */
const LEFT_SIDEBAR_KEY =
    'public_left_sidebar_collapsed'

const RIGHT_SIDEBAR_KEY =
    'public_right_sidebar_collapsed'

/**
 * Получение boolean из localStorage.
 */
const getStoredBoolean = (
    key,
    defaultValue = false
) => {
    const value =
        localStorage.getItem(key)

    if (value === null) {
        return defaultValue
    }

    return value === 'true'
}

/**
 * Состояние сайдбаров.
 *
 * По умолчанию оба открыты.
 */
const leftCollapsed = ref(
    getStoredBoolean(
        LEFT_SIDEBAR_KEY,
        false
    )
)

const rightCollapsed = ref(
    getStoredBoolean(
        RIGHT_SIDEBAR_KEY,
        false
    )
)

/**
 * Сохраняем состояние
 * левого сайдбара.
 */
watch(
    leftCollapsed,
    (value) => {
        localStorage.setItem(
            LEFT_SIDEBAR_KEY,
            String(value)
        )
    }
)

/**
 * Сохраняем состояние
 * правого сайдбара.
 */
watch(
    rightCollapsed,
    (value) => {
        localStorage.setItem(
            RIGHT_SIDEBAR_KEY,
            String(value)
        )
    }
)

/**
 * Количество колонок карточек
 * с учётом состояния сайдбаров.
 *
 * Оба сайдбара открыты  → 2.
 * Один сайдбар свернут   → 3.
 * Оба сайдбара свернуты  → 4.
 *
 * Если колонка глобально отключена
 * через настройки сайта,
 * она считается отсутствующей.
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
</script>

<template>
    <!-- SEO -->
    <Head>
        <title>
            {{ seoTitle }}
        </title>

        <meta
            name="title"
            :content="seoTitle"
        />

        <meta
            name="description"
            :content="seoDescription"
        />

        <meta
            name="keywords"
            :content="seoKeywords"
        />

        <meta
            name="viewport"
            content="width=device-width, initial-scale=1"
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
            :content="`/${props.locale}/school/courses/${courseData.slug || ''}`"
        />

        <meta
            property="og:image"
            :content="firstCourseImageUrl"
        />

        <meta
            property="og:locale"
            :content="ogLocale"
        />

        <!-- Twitter -->
        <meta
            name="twitter:card"
            content="summary_large_image"
        />

        <meta
            name="twitter:title"
            :content="seoTitle"
        />

        <meta
            name="twitter:description"
            :content="seoDescription"
        />

        <meta
            name="twitter:image"
            :content="firstCourseImageUrl"
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
            name="DC.Subject"
            :content="seoKeywords"
        />

        <meta
            name="DC.Type"
            content="Text"
        />

        <meta
            name="DC.Format"
            content="text/html"
        />

        <meta
            name="DC.Language"
            :content="contentLocale"
        />

        <meta
            name="DC.Identifier"
            :content="`/${props.locale}/school/courses/${courseData.slug || ''}`"
        />
    </Head>

    <DefaultLayout
        :title="title"
        :can-login="canLogin"
        :can-register="canRegister"
    >
        <Navbar />

        <div class="min-h-screen px-3 max-w-full">
            <main class="mx-auto flex flex-col lg:flex-row gap-4 tracking-wider">

                <!-- LEFT -->
                <aside
                    v-if="showLeft"
                    class="shrink-0 mt-12 lg:mt-28 transition-all duration-300 overflow-hidden"
                    :class="leftCollapsed ? 'lg:w-10' : 'lg:w-64'"
                >
                    <LeftSidebarSchool
                        :track-tree="trackTree"
                        :collapsed="leftCollapsed"
                        @collapsed="leftCollapsed = $event"
                    />
                </aside>

                <!-- CENTER -->
                <section
                    class="w-full lg:mt-28 pb-6 slate-1"
                >
                    <div class="mx-auto max-w-6xl">
                        <article
                            class="selection:bg-red-400
                                   selection:text-white"
                        >
                            <!-- Breadcrumbs -->
                            <nav
                                class="text-sm mb-3"
                                aria-label="Breadcrumb"
                            >
                                <ol
                                    class="flex flex-wrap
                                           items-center
                                           font-semibold"
                                >
                                    <li>
                                        <Link
                                            :href="route('home')"
                                            class="breadcrumb-link
                                                   hover:underline"
                                        >
                                            {{ t('home') }}
                                        </Link>
                                    </li>

                                    <li>
                                        <span class="mx-2 breadcrumbs">
                                            /
                                        </span>
                                    </li>

                                    <li>
                                        <Link
                                            :href="
                                                route(
                                                    'public.schoolCourses.index'
                                                )
                                            "
                                            class="breadcrumb-link
                                                   hover:underline"
                                        >
                                            {{ t('courses') }}
                                        </Link>
                                    </li>

                                    <li>
                                        <span class="mx-2 breadcrumbs">
                                            /
                                        </span>
                                    </li>

                                    <li class="breadcrumbs">
                                        {{ courseTitle }}
                                    </li>
                                </ol>
                            </nav>

                            <!-- Title / stats -->
                            <div
                                class="flex flex-wrap
                                       items-center justify-center
                                       gap-3 title my-3"
                            >
                                <h1 class="text-2xl font-bold">
                                    {{ courseTitle }}
                                </h1>

                                <!-- Views -->
                                <div
                                    v-if="courseData.views > 0"
                                    :title="t('views')"
                                    class="flex items-center
                                           justify-center gap-1"
                                >
                                    <svg
                                        class="h-4 w-4
                                               text-slate-600/85
                                               dark:text-slate-200/85"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 576 512"
                                        fill="currentColor"
                                    >
                                        <path
                                            d="M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z"
                                        />
                                    </svg>

                                    <span
                                        class="text-center text-sm
                                               text-gray-500"
                                    >
                                        {{ courseData.views }}
                                    </span>
                                </div>
                            </div>

                            <!-- Subtitle -->
                            <div
                                v-if="courseSubtitle"
                                class="mt-1 mb-3
                                       text-sm subtitle text-center"
                            >
                                {{ courseSubtitle }}
                            </div>

                            <!-- Main image -->
                            <div
                                v-if="hasCourseImages"
                                class="flex items-center justify-center"
                            >
                                <div class="w-full">
                                    <ImageGalleryMain
                                        :images="courseImages"
                                        :alt="courseTitle"
                                        rounded-class="rounded-lg"
                                        shadow-class="
                                            shadow-lg
                                            shadow-gray-400
                                            dark:shadow-gray-700
                                        "
                                        img-class="
                                            w-full h-full object-cover
                                        "
                                    />
                                </div>
                            </div>

                            <!-- Data -->
                            <div
                                class="mt-3 flex flex-wrap
                                       items-center justify-center
                                       gap-2 text-xs font-semibold
                                       text-slate-500
                                       dark:text-slate-400"
                            >
                                <!-- Level -->
                                <div
                                    v-if="courseData.level"
                                    class="flex items-center
                                           justify-center gap-1
                                           px-2 py-1 rounded-sm
                                           border border-slate-600
                                           dark:border-slate-400"
                                >
                                    <svg
                                        class="w-3 h-3 fill-current
                                               text-teal-700
                                               dark:text-teal-300"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            d="M12,24a1,1,0,0,1,0-2A10,10,0,0,0,12,2a1,1,0,0,1,0-2,12,12,0,0,1,0,24Z"
                                        />

                                        <path
                                            d="M10,17a1,1,0,0,1-.707-.293l-4-4a1,1,0,0,1,1.414-1.414L10,14.586l7.293-7.293a1,1,0,1,1,1.414,1.414l-8,8A1,1,0,0,1,10,17Z"
                                        />
                                    </svg>

                                    {{ t('level') }}:
                                    {{ translateLevel(courseData.level) }}
                                </div>

                                <!-- Duration -->
                                <div
                                    v-if="courseData.duration"
                                    class="flex items-center
                                           justify-center gap-1
                                           px-2 py-1 rounded-sm
                                           border border-slate-600
                                           dark:border-slate-400"
                                >
                                    <svg
                                        class="w-3 h-3
                                               text-blue-700
                                               dark:text-blue-300"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            class="fill-current"
                                            d="M22,13a1,1,0,0,1,0-2h1.949A12.006,12.006,0,0,0,13,.051V2a1,1,0,0,1-2,0V.051A12.006,12.006,0,0,0,.051,11H2a1,1,0,0,1,0,2H.051A12.006,12.006,0,0,0,11,23.949V22a1,1,0,0,1,2,0v1.949A12.006,12.006,0,0,0,23.949,13Zm-6,0H12a1,1,0,0,1-.832-.445l-4-6a1,1,0,1,1,1.664-1.11L12.535,11H16a1,1,0,0,1,0,2Z"
                                        />
                                    </svg>

                                    {{ t('duration') }}
                                    {{ t('minutes') }}
                                    {{ courseData.duration }}
                                </div>

                                <!-- Rating -->
                                <div
                                    v-if="courseData.rating_avg"
                                    class="flex items-center
                                           justify-center gap-1
                                           px-2 py-1 rounded-sm
                                           border border-slate-600
                                           dark:border-slate-400"
                                >
                                    <svg
                                        viewBox="0 0 24 24"
                                        class="h-3 w-3
                                               text-red-400
                                               dark:text-red-300"
                                    >
                                        <path
                                            class="fill-current"
                                            d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"
                                        />
                                    </svg>

                                    {{ t('rating') }}:
                                    {{
                                        Number(
                                            courseData.rating_avg
                                        ).toFixed(1)
                                    }}
                                </div>

                                <!-- Students -->
                                <div
                                    v-if="courseData.students_count"
                                    class="flex items-center
                                           justify-center gap-1
                                           px-2 py-1 rounded-sm
                                           border border-slate-600
                                           dark:border-slate-400"
                                >
                                    <svg
                                        class="h-4 w-4"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            class="fill-current
                                                   text-cyan-600"
                                            d="M18.974 8H22a2 2 0 012 2v6h-2v5a1 1 0 01-1 1h-2a1 1 0 01-1-1v-5h-2v-6a2 2 0 012-2h.974zM20 7a2 2 0 11-.001-3.999A2 2 0 0120 7zM2.974 8H6a2 2 0 012 2v6H6v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5H0v-6a2 2 0 012-2h.974zM4 7a2 2 0 11-.001-3.999A2 2 0 014 7z"
                                        />

                                        <path
                                            class="fill-current
                                                   text-cyan-400"
                                            d="M12 6a3 3 0 110-6 3 3 0 010 6zm2 18h-4a1 1 0 01-1-1v-6H6v-6a3 3 0 013-3h6a3 3 0 013 3v6h-3v6a1 1 0 01-1 1z"
                                        />
                                    </svg>

                                    {{ t('students') }}:
                                    {{ courseData.students_count }}
                                </div>
                            </div>

                            <!-- Description -->
                            <div
                                v-if="courseDescription"
                                class="mt-4 text-sm subtitle"
                                v-html="courseDescription"
                            />

                            <!-- Like -->
                            <div
                                class="my-1 flex
                                       items-center justify-center"
                            >
                                <LikeButtonEntity
                                    :likes-count="
                                        courseData.likes_count || 0
                                    "
                                    :already-liked="
                                        courseData.already_liked || false
                                    "
                                    route-name="public.schoolCourses.like"
                                    :route-params="courseData.id"
                                    :title="t('like')"
                                />
                            </div>

                            <!-- Hashtags -->
                            <div
                                v-if="hashtags.length"
                                class="mt-4 flex flex-wrap
                                       items-center justify-center
                                       gap-2"
                            >
                                <Link
                                    v-for="hashtag in hashtags"
                                    :key="hashtag.id"
                                    :href="
                                        route(
                                            'public.schoolHashtags.show',
                                            {
                                                slug: hashtag.slug,
                                            }
                                        )
                                    "
                                    class="rounded-sm px-2 py-1
                                           text-xs font-semibold
                                           text-indigo-700
                                           bg-indigo-50
                                           dark:text-indigo-300
                                           dark:bg-indigo-950/50
                                           border border-indigo-400"
                                >
                                    #{{
                                        hashtag.translation?.name
                                        || hashtag.slug
                                    }}
                                </Link>
                            </div>

                            <!-- Instructor -->
                            <div
                                v-if="instructorProfile"
                                class="mt-4 flex
                                       items-center justify-center
                                       gap-3"
                            >
                                <img
                                    v-if="instructorImageUrl"
                                    :src="instructorImageUrl"
                                    :alt="
                                        instructorPrimaryImage?.alt
                                        || instructorName
                                    "
                                    loading="lazy"
                                    class="h-12 w-12
                                           rounded-full object-cover
                                           ring-1 ring-gray-200
                                           dark:ring-gray-700"
                                />

                                <div
                                    class="flex flex-col
                                           items-start"
                                >
                                    <span
                                        class="text-xs
                                               text-slate-500
                                               dark:text-slate-400"
                                    >
                                        {{ t('instructor') }}
                                    </span>

                                    <span
                                        class="text-sm font-semibold
                                               text-slate-700
                                               dark:text-slate-300"
                                    >
                                        {{ instructorName }}
                                    </span>
                                </div>
                            </div>

                            <!-- Reviews -->
                            <div
                                v-if="reviews.length"
                                class="mt-8"
                            >
                                <h2
                                    class="mb-4 text-center
                                           text-lg font-semibold
                                           text-gray-700
                                           dark:text-gray-300"
                                >
                                    {{ t('reviews') }}
                                </h2>

                                <div class="space-y-3">
                                    <div
                                        v-for="review in reviews"
                                        :key="review.id"
                                        class="rounded-md
                                               border border-gray-200
                                               bg-white p-4 shadow-sm
                                               dark:border-gray-700
                                               dark:bg-gray-900"
                                    >
                                        <div
                                            class="flex items-center
                                                   justify-between
                                                   gap-3"
                                        >
                                            <div
                                                class="text-sm
                                                       font-semibold
                                                       text-slate-700
                                                       dark:text-slate-300"
                                            >
                                                {{
                                                    review.user?.name
                                                    || t('user')
                                                }}
                                            </div>

                                            <div
                                                v-if="
                                                    review.rating
                                                    !== null
                                                    && review.rating
                                                    !== undefined
                                                "
                                                class="text-xs
                                                       text-slate-500
                                                       dark:text-slate-400"
                                            >
                                                {{ t('rating') }}:
                                                {{ review.rating }}
                                            </div>
                                        </div>

                                        <div
                                            v-if="review.title"
                                            class="mt-2 text-sm
                                                   font-semibold
                                                   text-slate-700
                                                   dark:text-slate-300"
                                        >
                                            {{ review.title }}
                                        </div>

                                        <div
                                            v-if="review.body"
                                            class="mt-2 text-sm
                                                   text-slate-700
                                                   dark:text-slate-300"
                                        >
                                            {{ review.body }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!--
                                Modules.

                                ВАЖНО:
                                этот блок пока не переводим
                                на новый контракт.

                                SchoolModule / SchoolLesson
                                будем рефакторить отдельно.
                            -->
                            <CourseModulesSection
                                :modules="modulesList"
                                :cols="gridCols"
                            />

                            <!-- Related courses -->
                            <div
                                v-if="relatedCourses.length"
                                class="mt-8"
                            >
                                <h2
                                    class="mb-4 tracking-wide
                                           text-center font-semibold
                                           text-lg text-gray-700
                                           dark:text-gray-300"
                                >
                                    {{ t('relatedCourses') }}
                                </h2>

                                <InstructorCourseGrid
                                    :courses="relatedCourses"
                                    :cols="gridCols"
                                />
                            </div>
                        </article>

                        <!-- Bottom main blocks -->
                        <SectionVideoList
                            :videos="mainVideosList"
                        />

                        <SectionBanners
                            :banners="mainBannersList"
                        />
                    </div>
                </section>

                <!-- RIGHT -->
                <aside
                    v-if="showRight"
                    class="shrink-0 lg:mt-28 transition-all duration-300 overflow-hidden"
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
    </DefaultLayout>
</template>
