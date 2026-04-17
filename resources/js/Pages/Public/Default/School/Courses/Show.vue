<script setup>
/**
 * Страница конкретного курса
 * - SEO
 * - хлебные крошки
 * - основное изображение / галерея
 * - инструктор
 * - категории обучения
 * - хештеги
 * - лайк
 * - отзывы
 * - рекомендуемые курсы
 * - показ главных видео, баннеров внизу страницы
 * - показ, скрытие колонок
 * - показ дерева треков в левой колонке
 * - показ облако хештегов в правой колонке
 *
 * @version PulsarCMS 1.0
 * @author Александр
 */

import { computed, ref } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

import DefaultLayout from '@/Layouts/DefaultLayout.vue'
import Navbar from '@/Partials/Default/Navbar.vue'
import FooterBlog from '@/Partials/Default/FooterBlog.vue'
import Progress from '@/Components/Public/Default/Progress/Progress.vue'
import LeftSidebarSchool from '@/Components/Public/Default/Partials/LeftSidebarSchool.vue'
import RightSidebarSchool from '@/Components/Public/Default/Partials/RightSidebarSchool.vue'
import LikeButtonEntity from '@/Components/Public/Like/LikeButtonEntity.vue'
import ImageGalleryMain from '@/Components/Public/Default/Media/ImageGalleryMain.vue'
import SectionVideoList from '@/Components/Public/Default/Blog/Video/SectionVideoList.vue'
import SectionBanners from '@/Components/Public/Default/Blog/Banner/SectionBanners.vue'
import CourseModulesSection from '@/Components/Public/Default/School/Module/CourseModulesSection.vue'
import InstructorCourseGrid from '@/Components/Public/Default/School/Instructor/InstructorCourseGrid.vue'

const { t } = useI18n()
const page = usePage()

const props = defineProps({
    title: String,
    canLogin: Boolean,
    canRegister: Boolean,

    course: { type: Object, default: () => ({}) },
    modules: { type: [Array, Object], default: () => [] },
    trackTree: { type: Array, default: () => [] },

    mainVideos: { type: Array, default: () => [] },
    mainBanners: { type: Array, default: () => [] }
})

/**
 * Helpers
 */
const normalizeList = (value) => {
    if (Array.isArray(value)) return value
    if (Array.isArray(value?.data)) return value.data
    return []
}

const translateLevel = (level) => {
    const normalized = (level || '').toString().trim().toLowerCase()

    const map = {
        beginner: 'levelBeginner',
        intermediate: 'levelIntermediate',
        advanced: 'levelAdvanced'
    }

    return map[normalized] ? t(map[normalized]) : level
}

/**
 * Base data
 */
const courseData = computed(() => props.course ?? {})
const trackTree = computed(() => Array.isArray(props.trackTree) ? props.trackTree : [])

const mainVideosList = computed(() => normalizeList(props.mainVideos))
const mainBannersList = computed(() => normalizeList(props.mainBanners))

const authUser = computed(() => page.props.auth?.user ?? null)

/**
 * Images
 */
const courseImages = computed(() => {
    return Array.isArray(courseData.value?.images) ? courseData.value.images : []
})

const firstCourseImage = computed(() => {
    return courseImages.value.length ? courseImages.value[0] : null
})

const hasCourseImages = computed(() => courseImages.value.length > 0)

/**
 * Instructor
 */
const instructorProfile = computed(() => {
    return courseData.value?.instructorProfile || null
})

const instructorImages = computed(() => {
    return Array.isArray(instructorProfile.value?.images)
        ? instructorProfile.value.images
        : []
})

const instructorPrimaryImage = computed(() => {
    if (!instructorImages.value.length) return null

    return [...instructorImages.value].sort((a, b) => {
        const aOrder = Number(a?.order ?? a?.pivot?.order ?? 999999)
        const bOrder = Number(b?.order ?? b?.pivot?.order ?? 999999)
        return aOrder - bOrder
    })[0]
})

const instructorImageUrl = computed(() => {
    return instructorPrimaryImage.value?.webp_url
        || instructorPrimaryImage.value?.url
        || instructorPrimaryImage.value?.image_url
        || instructorPrimaryImage.value?.thumb_url
        || null
})

const instructorName = computed(() => {
    return instructorProfile.value?.public_name
        || instructorProfile.value?.title
        || t('instructor')
})

/**
 * Categories / hashtags / reviews
 */
const learningCategories = computed(() => normalizeList(courseData.value?.learning_categories))
const hashtags = computed(() => normalizeList(courseData.value?.hashtags))
const reviews = computed(() => normalizeList(courseData.value?.reviews))
const modulesList = computed(() => normalizeList(props.modules))

/**
 * Layout / sidebars
 */
const { siteSettings } = page.props

const showLeft = computed(() =>
    !siteSettings?.ViewLeftColumn || siteSettings.ViewLeftColumn === 'true'
)

const showRight = computed(() =>
    !siteSettings?.ViewRightColumn || siteSettings.ViewRightColumn === 'true'
)

const leftCollapsed = ref(false)
const rightCollapsed = ref(false)

const gridCols = computed(() => {
    const leftExpanded = showLeft.value && !leftCollapsed.value
    const rightExpanded = showRight.value && !rightCollapsed.value

    return leftExpanded && rightExpanded ? 2 : 3
})
</script>

<template>
    <Head>
        <title>{{ courseData.meta_title || courseData.title || '' }}</title>

        <meta name="title" :content="courseData.meta_title || courseData.title || ''" />
        <meta name="description" :content="courseData.meta_desc || courseData.short || ''" />
        <meta name="keywords" :content="courseData.meta_keywords || ''" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <meta property="og:title" :content="courseData.meta_title || courseData.title || ''" />
        <meta property="og:description" :content="courseData.meta_desc || courseData.short || ''" />
        <meta property="og:type" content="website" />
        <meta property="og:url" :content="`/school/courses/${courseData.slug || ''}`" />
        <meta
            property="og:image"
            :content="firstCourseImage ? (firstCourseImage.webp_url || firstCourseImage.url || firstCourseImage.image_url) : ''"
        />
        <meta property="og:locale" :content="courseData.locale || 'ru_RU'" />

        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="courseData.meta_title || courseData.title || ''" />
        <meta name="twitter:description" :content="courseData.meta_desc || courseData.short || ''" />
        <meta
            name="twitter:image"
            :content="firstCourseImage ? (firstCourseImage.webp_url || firstCourseImage.url || firstCourseImage.image_url) : ''"
        />

        <meta name="DC.Title" :content="courseData.meta_title || courseData.title || ''" />
        <meta name="DC.Description" :content="courseData.meta_desc || courseData.short || ''" />
        <meta name="DC.Subject" :content="courseData.meta_keywords || ''" />
        <meta name="DC.Type" content="Text" />
        <meta name="DC.Format" content="text/html" />
        <meta name="DC.Language" :content="courseData.locale || 'ru'" />
        <meta name="DC.Identifier" :content="`/school/courses/${courseData.slug || ''}`" />
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
                <section class="w-full lg:mt-16 pb-6 slate-1 min-w-0">
                    <div class="mx-auto max-w-6xl">
                        <article class="selection:bg-red-400 selection:text-white">

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
                                            :href="route('public.courses.index')"
                                            class="breadcrumb-link hover:underline"
                                        >
                                            {{ t('courses') }}
                                        </Link>
                                    </li>
                                    <li><span class="mx-2 breadcrumbs">/</span></li>
                                    <li class="breadcrumbs">
                                        {{ courseData.title }}
                                    </li>
                                </ol>
                            </nav>

                            <!-- Title / stats -->
                            <div class="flex flex-wrap items-center justify-center gap-3 title my-3">
                                <h1 class="text-2xl font-bold">
                                    {{ courseData.title }}
                                </h1>

                                <div
                                    v-if="courseData.views > 0"
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
                                        {{ courseData.views }}
                                    </span>
                                </div>
                            </div>

                            <!-- Subtitle -->
                            <div
                                v-if="courseData.subtitle"
                                class="mt-1 mb-3 text-sm subtitle text-center"
                            >
                                {{ courseData.subtitle }}
                            </div>

                            <!-- Main image -->
                            <div
                                v-if="hasCourseImages"
                                class="flex items-center justify-center"
                            >
                                <div class="w-full">
                                    <ImageGalleryMain
                                        :images="courseImages"
                                        :alt="courseData.title"
                                        rounded-class="rounded-lg"
                                        shadow-class="shadow-lg shadow-gray-400 dark:shadow-gray-700"
                                        img-class="w-full h-full object-cover"
                                    />
                                </div>
                            </div>

                            <!-- Данные -->
                            <div
                                class="mt-3 flex flex-wrap items-center justify-center gap-2
                                       text-xs font-semibold text-slate-500 dark:text-slate-400">
                                <div
                                    v-if="courseData.level"
                                    class="flex items-center justify-center gap-1 px-2 py-1
                                           rounded-sm border border-slate-600 dark:border-slate-400"
                                >
                                    <svg
                                        class="w-3 h-3 fill-current text-teal-700 dark:text-teal-300"
                                        viewBox="0 0 24 24">
                                        <path d="M12,24a1,1,0,0,1,0-2A10,10,0,0,0,12,2a1,1,0,0,1,0-2,12,12,0,0,1,0,24Z"></path>
                                        <path d="M1.045,13.913a1,1,0,0,1-1-.919C.022,12.665,0,12.336,0,12s.022-.665.049-.994a1,1,0,1,1,1.993.162C2.021,11.442,2,11.719,2,12s.021.558.042.832a1,1,0,0,1-.916,1.078Q1.086,13.913,1.045,13.913Z"></path>
                                        <path d="M6.243,3.641a1,1,0,0,1-.526-1.852,12.022,12.022,0,0,1,1.774-.9,1,1,0,1,1,.754,1.851,10.133,10.133,0,0,0-1.478.757A.993.993,0,0,1,6.243,3.641Z"></path>
                                        <path d="M2.188,8.044a.988.988,0,0,1-.451-.108A1,1,0,0,1,1.3,6.592,12.131,12.131,0,0,1,2.342,4.9,1,1,0,0,1,3.953,6.083,10.1,10.1,0,0,0,3.081,7.5,1,1,0,0,1,2.188,8.044Z"></path>
                                        <path d="M3.128,19.482a1,1,0,0,1-.808-.409,12.049,12.049,0,0,1-1.041-1.7,1,1,0,1,1,1.787-.9,10.047,10.047,0,0,0,.868,1.418,1,1,0,0,1-.217,1.4A.986.986,0,0,1,3.128,19.482Z"></path>
                                        <path d="M7.853,23.185a.983.983,0,0,1-.377-.075A11.879,11.879,0,0,1,5.7,22.2,1,1,0,0,1,6.75,20.5a10.041,10.041,0,0,0,1.48.761,1,1,0,0,1-.377,1.926Z"></path>
                                        <path d="M10,17a1,1,0,0,1-.707-.293l-4-4a1,1,0,0,1,1.414-1.414L10,14.586l7.293-7.293a1,1,0,1,1,1.414,1.414l-8,8A1,1,0,0,1,10,17Z"></path>
                                    </svg>
                                    {{ t('level') }}: {{ translateLevel(courseData.level) }}
                                </div>

                                <div
                                    v-if="courseData.duration"
                                    class="flex items-center justify-center gap-1 px-2 py-1
                                           rounded-sm border border-slate-600 dark:border-slate-400"
                                >
                                    <svg class="w-3 h-3 text-blue-700 dark:text-blue-300"
                                         viewBox="0 0 24 24">
                                        <path class="fill-current"
                                              d="M22,13a1,1,0,0,1,0-2h1.949A12.006,12.006,0,0,0,13,.051V2a1,1,0,0,1-2,0V.051A12.006,12.006,0,0,0,.051,11H2a1,1,0,0,1,0,2H.051A12.006,12.006,0,0,0,11,23.949V22a1,1,0,0,1,2,0v1.949A12.006,12.006,0,0,0,23.949,13Zm-6,0H12a1,1,0,0,1-.832-.445l-4-6a1,1,0,1,1,1.664-1.11L12.535,11H16a1,1,0,0,1,0,2Z"></path>
                                    </svg>
                                    {{ t('duration') }} {{ t('minutes') }} {{ courseData.duration }}
                                </div>

                                <div
                                    v-if="courseData.rating_avg"
                                    class="flex items-center justify-center gap-1 px-2 py-1
                                           rounded-sm border border-slate-600 dark:border-slate-400"
                                >
                                    <svg viewBox="0 0 24 24"
                                         class="h-3 w-3 text-red-400 dark:text-red-300">
                                        <path class="fill-current"
                                              d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"></path>
                                    </svg>
                                    {{ t('rating') }}: {{ Number(courseData.rating_avg).toFixed(1) }}
                                </div>

                                <div
                                    v-if="courseData.students_count"
                                    class="flex items-center justify-center gap-1 px-2 py-1
                                           rounded-sm border border-slate-600 dark:border-slate-400"
                                >
                                    <svg class="h-4 w-4" viewBox="0 0 24 24">
                                        <path class="fill-current text-cyan-600" d="M18.974 8H22a2 2 0 012 2v6h-2v5a1 1 0 01-1 1h-2a1 1 0 01-1-1v-5h-2v-6a2 2 0 012-2h.974zM20 7a2 2 0 11-.001-3.999A2 2 0 0120 7zM2.974 8H6a2 2 0 012 2v6H6v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5H0v-6a2 2 0 012-2h.974zM4 7a2 2 0 11-.001-3.999A2 2 0 014 7z"></path>
                                        <path class="fill-current text-cyan-400" d="M12 6a3 3 0 110-6 3 3 0 010 6zm2 18h-4a1 1 0 01-1-1v-6H6v-6a3 3 0 013-3h6a3 3 0 013 3v6h-3v6a1 1 0 01-1 1z"></path>
                                    </svg>
                                    {{ t('students') }}: {{ courseData.students_count }}
                                </div>
                            </div>

                            <!-- Description -->
                            <div
                                v-if="courseData.description"
                                class="mt-4 text-sm subtitle"
                                v-html="courseData.description"
                            />

                            <!-- Like -->
                            <div class="my-1 flex items-center justify-center">
                                <LikeButtonEntity
                                    :likes-count="courseData.likes_count || 0"
                                    :already-liked="courseData.already_liked || false"
                                    route-name="courses.like"
                                    :route-params="{ course: courseData.id }"
                                    :title="t('like')"
                                />
                            </div>

                            <!-- Hashtags -->
                            <div
                                v-if="hashtags.length"
                                class="mt-4 flex flex-wrap items-center justify-center gap-2"
                            >
                                <span
                                    v-for="hashtag in hashtags"
                                    :key="hashtag.id"
                                    class="rounded-sm px-2 py-1 text-xs font-semibold
                                           text-indigo-700 bg-indigo-50 dark:text-indigo-300
                                           dark:bg-indigo-950/50 border border-indigo-400"
                                >
                                    #{{ hashtag.name }}
                                </span>
                            </div>

                            <!-- Instructor -->
                            <div
                                v-if="instructorProfile"
                                class="mt-4 flex items-center justify-center gap-3"
                            >
                                <img
                                    v-if="instructorImageUrl"
                                    :src="instructorImageUrl"
                                    :alt="instructorPrimaryImage?.alt || instructorName"
                                    loading="lazy"
                                    class="h-12 w-12 rounded-full object-cover
                                           ring-1 ring-gray-200 dark:ring-gray-700"
                                />

                                <div class="flex flex-col items-start">
                                    <span class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ t('instructor') }}
                                    </span>
                                    <span class="text-sm font-semibold
                                                 text-slate-700 dark:text-slate-300">
                                        {{ instructorName }}
                                    </span>
                                </div>
                            </div>

                            <!-- Reviews -->
                            <div v-if="reviews.length" class="mt-8">
                                <h2 class="mb-4 text-center text-lg font-semibold text-gray-700 dark:text-gray-300">
                                    {{ t('reviews') }}
                                </h2>

                                <div class="space-y-3">
                                    <div
                                        v-for="review in reviews"
                                        :key="review.id"
                                        class="rounded-md border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900"
                                    >
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                                                {{ review.user?.name || t('user') }}
                                            </div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400">
                                                {{ t('rating') }}: {{ review.rating }}
                                            </div>
                                        </div>

                                        <div
                                            v-if="review.title"
                                            class="mt-2 text-sm font-semibold text-slate-700 dark:text-slate-300"
                                        >
                                            {{ review.title }}
                                        </div>

                                        <div
                                            v-if="review.body"
                                            class="mt-2 text-sm text-slate-700 dark:text-slate-300"
                                        >
                                            {{ review.body }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <CourseModulesSection
                                :modules="modulesList"
                                :cols="gridCols"
                            />

                            <!-- Related courses -->
                            <div v-if="courseData.related_courses.length" class="mt-8">
                                <h2 class="mb-4 tracking-wide text-center
                                           font-semibold text-lg text-gray-700 dark:text-gray-300">
                                    {{ t('relatedCourses') }}
                                </h2>
                                <InstructorCourseGrid
                                    :courses="courseData.related_courses"
                                    :cols="gridCols"
                                />
                            </div>
                        </article>

                        <!-- Bottom main blocks -->
                        <SectionVideoList :videos="mainVideosList" />
                        <SectionBanners :banners="mainBannersList" />
                    </div>
                </section>

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
