<script setup>
/**
 * Страница конкретного задания
 * - SEO
 * - хлебные крошки
 * - основное изображение / галерея
 * - курс, модуль, урок
 * - инструктор
 * - дедлайн, баллы, попытки, статус, видимость
 * - описание и инструкции
 * - показ главных видео, баннеров внизу страницы
 * - показ, скрытие колонок
 * - показ дерева треков в левой колонке
 * - показ облака хештегов в правой колонке
 *  *
 *  * @version PulsarCMS 1.0
 *  * @author Александр
 *  */

import { computed, ref } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

import DefaultLayout from '@/Layouts/DefaultLayout.vue'
import Navbar from '@/Partials/Default/Navbar.vue'
import FooterBlog from '@/Partials/Default/FooterBlog.vue'
import Progress from '@/Components/Public/Default/Progress/Progress.vue'
import LeftSidebarSchool from '@/Components/Public/Default/Partials/LeftSidebarSchool.vue'
import RightSidebarSchool from '@/Components/Public/Default/Partials/RightSidebarSchool.vue'
import ImageGalleryMain from '@/Components/Public/Default/Media/ImageGalleryMain.vue'
import SectionVideoList from '@/Components/Public/Default/Blog/Video/SectionVideoList.vue'
import SectionBanners from '@/Components/Public/Default/Blog/Banner/SectionBanners.vue'

const { t } = useI18n()
const page = usePage()

/** пропсы */
const props = defineProps({
    title: String,
    canLogin: Boolean,
    canRegister: Boolean,

    assignment: { type: Object, default: () => ({}) },
    trackTree: { type: Array, default: () => [] },

    mainVideos: { type: [Array, Object], default: () => [] },
    mainBanners: { type: [Array, Object], default: () => [] },
})

/** настройки из БД */
const { siteSettings } = page.props

/** показ левой колонки */
const showLeft = computed(() =>
    !siteSettings?.ViewLeftColumn || siteSettings.ViewLeftColumn === 'true'
)

/** показ правой колонки */
const showRight = computed(() =>
    !siteSettings?.ViewRightColumn || siteSettings.ViewRightColumn === 'true'
)

/** состояние левой и правой колонки */
const leftCollapsed = ref(false)
const rightCollapsed = ref(false)

/** нормализация данных */
const normalizeList = (value) => {
    if (Array.isArray(value)) return value
    if (Array.isArray(value?.data)) return value.data
    return []
}

/** данные задания */
const assignmentData = computed(() => props.assignment ?? {})

/** дерево треков */
const trackTree = computed(() => Array.isArray(props.trackTree) ? props.trackTree : [])

/** внизу страницы банеры */
const mainVideosList = computed(() => normalizeList(props.mainVideos))

/** внизу страницы видео */
const mainBannersList = computed(() => normalizeList(props.mainBanners))

/** изображения задания */
const assignmentImages = computed(() => {
    return Array.isArray(assignmentData.value?.images) ? assignmentData.value.images : []
})
/** первое изображение задания */
const firstAssignmentImage = computed(() => {
    return assignmentImages.value.length ? assignmentImages.value[0] : null
})
/** если изображения есть */
const hasAssignmentImages = computed(() => assignmentImages.value.length > 0)

/** данные урока */
const lessonData = computed(() => {
    return assignmentData.value?.lesson || null
})

/** данные модуля */
const moduleData = computed(() => {
    return assignmentData.value?.module
        || assignmentData.value?.lesson?.module
        || null
})

/** данные курса */
const courseData = computed(() => {
    return assignmentData.value?.course
        || assignmentData.value?.module?.course
        || assignmentData.value?.lesson?.module?.course
        || null
})

/** данные инструктора */
const instructorData = computed(() => {
    return assignmentData.value?.instructor || null
})

/** перевод типа оценки */
const translateGradingType = (value) => {
    const map = {
        manual: t('gradingManual'),
        auto: t('gradingAuto'),
    }

    return map[value] || value || '—'
}

/** перевод типа задания */
const translateVisibility = (value) => {
    const map = {
        public: t('public'),
        enrolled: t('enrolled'),
        private: t('private'),
    }

    return map[value] || value || '—'
}

/** перевод статуса задания */
const translateStatus = (value) => {
    const map = {
        draft: t('statusDraft'),
        published: t('statusPublished'),
        archived: t('statusArchived'),
    }

    return map[value] || value || '—'
}

/** формат даты дедлайна */
const formatDueDate = (value) => {
    if (!value) return null

    const date = new Date(value)
    if (Number.isNaN(date.getTime())) return value

    return new Intl.DateTimeFormat('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(date)
}

/** формат даты публикации задания */
const formatPublishedAt = (value) => {
    if (!value) return null

    const date = new Date(value)
    if (Number.isNaN(date.getTime())) return value

    return new Intl.DateTimeFormat('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(date)
}

/** массив изображений инструктора */
const getInstructorImages = () => {
    return Array.isArray(instructorData.value?.images)
        ? instructorData.value.images
        : []
}

/** первое изображение инструктора */
const getInstructorPrimaryImage = () => {
    const images = getInstructorImages()

    if (!images.length) return null

    return [...images].sort((a, b) => {
        const aOrder = Number(a?.order ?? a?.pivot?.order ?? 999999)
        const bOrder = Number(b?.order ?? b?.pivot?.order ?? 999999)
        return aOrder - bOrder
    })[0]
}

/** превью изображения инструктора */
const getInstructorImageUrl = () => {
    const image = getInstructorPrimaryImage()

    return image?.webp_url
        || image?.url
        || image?.image_url
        || image?.thumb_url
        || null
}

/** данные инструктора */
const getInstructorName = () => {
    return instructorData.value?.public_name
        || instructorData.value?.title
        || instructorData.value?.user?.name
        || t('instructor')
}
</script>

<template>
    <!-- SEO -->
    <Head>
        <title>{{ assignmentData.title || '' }}</title>

        <meta name="title" :content="assignmentData.title || ''" />
        <meta name="description" :content="assignmentData.short || assignmentData.subtitle || ''" />
        <meta name="keywords" content="" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <meta property="og:title" :content="assignmentData.title || ''" />
        <meta property="og:description" :content="assignmentData.short || assignmentData.subtitle || ''" />
        <meta property="og:type" content="website" />
        <meta property="og:url" :content="`/school/assignments/${assignmentData.slug || ''}`" />
        <meta
            property="og:image"
            :content="firstAssignmentImage ? (firstAssignmentImage.webp_url || firstAssignmentImage.url || firstAssignmentImage.image_url) : ''"
        />
        <meta property="og:locale" :content="assignmentData.locale || 'ru_RU'" />

        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="assignmentData.title || ''" />
        <meta name="twitter:description" :content="assignmentData.short || assignmentData.subtitle || ''" />
        <meta
            name="twitter:image"
            :content="firstAssignmentImage ? (firstAssignmentImage.webp_url || firstAssignmentImage.url || firstAssignmentImage.image_url) : ''"
        />
    </Head>

    <DefaultLayout :title="title" :can-login="canLogin" :can-register="canRegister">
        <!-- Шапка -->
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
                                <ol class="flex items-center font-semibold flex-wrap">
                                    <li>
                                        <Link
                                            :href="route('home')"
                                            class="breadcrumb-link hover:underline"
                                        >
                                            {{ t('home') }}
                                        </Link>
                                    </li>

                                    <template v-if="courseData?.slug">
                                        <li><span class="mx-2 breadcrumbs">/</span></li>
                                        <li>
                                            <Link
                                                :href="route('public.courses.show', { slug: courseData.slug })"
                                                class="breadcrumb-link hover:underline"
                                            >
                                                {{ courseData.title }}
                                            </Link>
                                        </li>
                                    </template>

                                    <template v-if="moduleData?.slug">
                                        <li><span class="mx-2 breadcrumbs">/</span></li>
                                        <li>
                                            <Link
                                                :href="route('public.modules.show', { slug: moduleData.slug })"
                                                class="breadcrumb-link hover:underline"
                                            >
                                                {{ moduleData.title }}
                                            </Link>
                                        </li>
                                    </template>

                                    <template v-if="lessonData?.slug">
                                        <li><span class="mx-2 breadcrumbs">/</span></li>
                                        <li>
                                            <Link
                                                :href="route('public.lessons.show', { slug: lessonData.slug })"
                                                class="breadcrumb-link hover:underline"
                                            >
                                                {{ lessonData.title }}
                                            </Link>
                                        </li>
                                    </template>

                                    <li><span class="mx-2 breadcrumbs">/</span></li>

                                    <li>
                                        <Link
                                            :href="route('public.assignments.index')"
                                            class="breadcrumb-link hover:underline"
                                        >
                                            {{ t('assignments') }}
                                        </Link>
                                    </li>

                                    <li><span class="mx-2 breadcrumbs">/</span></li>

                                    <li class="breadcrumbs">
                                        {{ assignmentData.title }}
                                    </li>
                                </ol>
                            </nav>

                            <!-- Title -->
                            <div class="flex items-center justify-center gap-3 title my-3">
                                <svg class="shrink-0 h-5 w-5
                                            text-slate-600/85 dark:text-slate-200/85"
                                     fill="currentColor"
                                     viewBox="0 0 24 24">
                                    <path d="M15,18v2H9v-2H1v5c0,0.552,0.448,1,1,1h20c0.552,0,1-0.448,1-1v-5H15z"></path>
                                    <path d="M23,4h-6V1c0-0.552-0.448-1-1-1H8C7.448,0,7,0.448,7,1v3H1C0.448,4,0,4.448,0,5v10c0,0.552,0.448,1,1,1h8v-3 h6v3h8c0.552,0,1-0.448,1-1V5C24,4.448,23.552,4,23,4z M15,4H9V2h6V4z"></path>
                                </svg>

                                <h1 class="text-2xl font-bold">
                                    {{ assignmentData.title }}
                                </h1>
                            </div>

                            <!-- Subtitle -->
                            <div
                                v-if="assignmentData.subtitle"
                                class="mt-1 mb-3 text-sm subtitle text-center"
                            >
                                {{ assignmentData.subtitle }}
                            </div>

                            <!-- Images || Slider -->
                            <div
                                v-if="hasAssignmentImages"
                                class="flex items-center justify-center"
                            >
                                <div class="w-full">
                                    <ImageGalleryMain
                                        :images="assignmentImages"
                                        :alt="assignmentData.title"
                                        rounded-class="rounded-lg"
                                        shadow-class="shadow-lg shadow-gray-400
                                                      dark:shadow-gray-700"
                                        img-class="w-full h-full object-cover"
                                    />
                                </div>
                            </div>

                            <!-- Данные -->
                            <div
                                class="my-4 flex flex-wrap items-center justify-center gap-3
                                       text-sm text-slate-600 dark:text-slate-300"
                            >

                                <!-- Инструктор -->
                                <span
                                    v-if="instructorData"
                                    class="rounded-sm border border-gray-400 flex
                                           items-center justify-center gap-2 px-3 py-1"
                                >
                                    <img
                                        v-if="getInstructorImageUrl()"
                                        :src="getInstructorImageUrl()"
                                        :alt="getInstructorName()"
                                        loading="lazy"
                                        class="h-6 w-6 rounded-full object-cover
                                               ring-1 ring-gray-200 dark:ring-gray-700"
                                    />

                                    <svg
                                        v-else
                                        class="h-4 w-4 text-violet-600/85 dark:text-violet-200/85"
                                        fill="currentColor"
                                        viewBox="0 0 640 512"
                                    >
                                        <path d="M622.34 153.2L343.4 67.5c-15.2-4.67-31.6-4.67-46.79 0L17.66 153.2c-23.54 7.23-23.54 38.36 0 45.59l48.63 14.94c-10.67 13.19-17.23 29.28-17.88 46.9C38.78 266.15 32 276.11 32 288c0 10.78 5.68 19.85 13.86 25.65L20.33 428.53C18.11 438.52 25.71 448 35.94 448h56.11c10.24 0 17.84-9.48 15.62-19.47L82.14 313.65C90.32 307.85 96 298.78 96 288c0-11.57-6.47-21.25-15.66-26.87.76-15.02 8.44-28.3 20.69-36.72L296.6 284.5c9.06 2.78 26.44 6.25 46.79 0l278.95-85.7c23.55-7.24 23.55-38.36 0-45.6zM352.79 315.09c-28.53 8.76-52.84 3.92-65.59 0l-145.02-44.55L128 384c0 35.35 85.96 64 192 64s192-28.65 192-64l-14.18-113.47-145.03 44.56z"></path>
                                    </svg>

                                    <span>
                                        {{ t('instructor') }}: {{ getInstructorName() }}
                                    </span>
                                </span>

                                <!-- Курс -->
                                <span
                                    v-if="courseData?.title"
                                    class="rounded-sm border border-gray-400
                                           flex items-center justify-center gap-1 px-3 py-1"
                                >
                                    <svg class="shrink-0 h-3 w-3
                                                text-sky-600/85 dark:text-sky-200/85"
                                         fill="currentColor"
                                         viewBox="0 0 448 512">
                                        <path d="M318.38 208h-39.09c-1.49 27.03-6.54 51.35-14.21 70.41 27.71-13.24 48.02-39.19 53.3-70.41zm0-32c-5.29-31.22-25.59-57.17-53.3-70.41 7.68 19.06 12.72 43.38 14.21 70.41h39.09zM224 97.31c-7.69 7.45-20.77 34.42-23.43 78.69h46.87c-2.67-44.26-15.75-71.24-23.44-78.69zm-41.08 8.28c-27.71 13.24-48.02 39.19-53.3 70.41h39.09c1.49-27.03 6.53-51.35 14.21-70.41zm0 172.82c-7.68-19.06-12.72-43.38-14.21-70.41h-39.09c5.28 31.22 25.59 57.17 53.3 70.41zM247.43 208h-46.87c2.66 44.26 15.74 71.24 23.43 78.69 7.7-7.45 20.78-34.43 23.44-78.69zM448 358.4V25.6c0-16-9.6-25.6-25.6-25.6H96C41.6 0 0 41.6 0 96v320c0 54.4 41.6 96 96 96h326.4c12.8 0 25.6-9.6 25.6-25.6v-16c0-6.4-3.2-12.8-9.6-19.2-3.2-16-3.2-60.8 0-73.6 6.4-3.2 9.6-9.6 9.6-19.2zM224 64c70.69 0 128 57.31 128 128s-57.31 128-128 128S96 262.69 96 192 153.31 64 224 64zm160 384H96c-19.2 0-32-12.8-32-32s16-32 32-32h288v64z"></path>
                                    </svg>
                                    {{ t('course') }}: {{ courseData.title }}
                                </span>

                                <!-- Модуль -->
                                <span
                                    v-if="moduleData?.title"
                                    class="rounded-sm border border-gray-400
                                           flex items-center justify-center gap-1 px-3 py-1"
                                >
                                    <svg class="shrink-0 h-3 w-3
                                                text-teal-600/85 dark:text-teal-200/85"
                                         fill="currentColor"
                                         viewBox="0 0 24 24">
                                        <rect x="1" y="1" width="10" height="10" rx="2"></rect>
                                        <path class="fill-current text-teal-400"
                                              d="M23.428,4.618,19.381.572h0a1.957,1.957,0,0,0-2.762,0L12.572,4.618a1.959,1.959,0,0,0,0,2.764l4.047,4.047a1.957,1.957,0,0,0,2.762,0l4.047-4.046A1.959,1.959,0,0,0,23.428,4.618Z"></path>
                                        <rect x="13" y="13" width="10" height="10" rx="2"></rect>
                                        <rect x="1" y="13" width="10" height="10" rx="2"></rect>
                                    </svg>
                                    {{ t('module') }}: {{ moduleData.title }}
                                </span>

                                <!-- Урок -->
                                <span
                                    v-if="lessonData?.title"
                                    class="rounded-sm border border-gray-400 flex
                                           items-center justify-center gap-1 px-3 py-1"
                                >
                                    <svg class="shrink-0 h-3 w-3
                                                text-indigo-600/85 dark:text-indigo-300/85"
                                         fill="currentColor"
                                         viewBox="0 0 24 24">
                                        <path d="M7 4.75h8.5A2.75 2.75 0 0 1 18.25 7.5v9A2.75 2.75 0 0 1 15.5 19.25H7A2.25 2.25 0 0 1 4.75 17V7A2.25 2.25 0 0 1 7 4.75Z"/>
                                        <path d="M8.5 8.5h6M8.5 12h6M8.5 15.5h4" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                    </svg>
                                    {{ t('lesson') }}: {{ lessonData.title }}
                                </span>

                                <!-- Статус -->
                                <span
                                    class="rounded-sm border border-gray-400 flex
                                           items-center justify-center gap-1 px-3 py-1"
                                >
                                    <svg class="h-3 w-3 text-violet-600 dark:text-violet-300"
                                         fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2a10 10 0 1 0 10 10A10.01 10.01 0 0 0 12 2Zm1 15h-2v-2h2Zm0-4h-2V7h2Z"/>
                                    </svg>
                                    {{ t('status') }}: {{ translateStatus(assignmentData.status) }}
                                </span>

                                <!-- Дата публикации -->
                                <span
                                    v-if="assignmentData.published_at"
                                    class="rounded-sm border border-gray-400 flex
                                           items-center justify-center gap-1 px-3 py-1"
                                >
                                    <svg class="h-3 w-3 text-slate-600 dark:text-slate-300"
                                         fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 4h-1V2h-2v2H8V2H6v2H5a2 2 0 0 0-2 2v13a3 3 0 0 0 3 3h12a3 3 0 0 0 3-3V6a2 2 0 0 0-2-2Zm0 15a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V10h14Z"/>
                                    </svg>
                                    {{ t('publishedAt') }}:
                                    {{ formatPublishedAt(assignmentData.published_at) }}
                                </span>

                                <!-- Тип оценки -->
                                <span
                                    class="rounded-sm border border-gray-400 flex
                                           items-center justify-center gap-1 px-3 py-1"
                                >
                                    <svg class="h-3 w-3 text-emerald-600 dark:text-emerald-300"
                                         fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 16.17 4.83 12 3.41 13.41 9 19l12-12-1.41-1.41z"/>
                                    </svg>
                                    {{ t('gradingType') }}:
                                    {{ translateGradingType(assignmentData.grading_type) }}
                                </span>

                                <!-- Средняя оценка -->
                                <span
                                    v-if="assignmentData.max_score"
                                    class="rounded-sm border border-gray-400 flex
                                           items-center justify-center gap-1 px-3 py-1"
                                >
                                    <svg class="h-3 w-3 text-amber-500"
                                         fill="currentColor"
                                         viewBox="0 0 24 24">
                                        <path d="M12 2 14.85 8.15 21.5 9.27l-4.75 4.63 1.12 6.6L12 17.27 6.13 20.5l1.12-6.6L2.5 9.27l6.65-1.12L12 2Z"/>
                                    </svg>
                                    {{ t('maxScore') }}: {{ assignmentData.max_score }}
                                </span>

                                <!-- Количество попыток -->
                                <span
                                    v-if="assignmentData.attempts_limit !== null &&
                                    assignmentData.attempts_limit !== undefined"
                                    class="rounded-sm border border-gray-400 flex
                                           items-center justify-center gap-1 px-3 py-1"
                                >
                                    <svg class="h-3 w-3 text-blue-700 dark:text-blue-300"
                                         fill="currentColor"
                                         viewBox="0 0 24 24">
                                        <path d="M12 2a10 10 0 1 0 10 10A10.01 10.01 0 0 0 12 2Zm1 5h-2v6l5 3 1-1.73-4-2.27Z"/>
                                    </svg>
                                    {{ t('attemptsLimit') }}:
                                    {{ assignmentData.attempts_limit === 0 ? t('no') : assignmentData.attempts_limit }}
                                </span>

                                <!-- Видимость -->
                                <span
                                    class="rounded-sm border border-gray-400 flex
                                           items-center justify-center gap-1 px-3 py-1"
                                >
                                    <svg class="h-3 w-3 text-sky-600 dark:text-sky-300"
                                         fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7Zm0 11a4 4 0 1 1 4-4 4 4 0 0 1-4 4Z"/>
                                    </svg>
                                    {{ t('visibility') }}:
                                    {{ translateVisibility(assignmentData.visibility) }}
                                </span>

                                <!-- Дедлайн -->
                                <span
                                    v-if="assignmentData.due_at"
                                    class="rounded-sm border border-gray-400 flex
                                           items-center justify-center gap-1 px-3 py-1
                                           text-rose-600 dark:text-rose-300"
                                >
                                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 4h-1V2h-2v2H8V2H6v2H5a2 2 0 0 0-2 2v13a3 3 0 0 0 3 3h12a3 3 0 0 0 3-3V6a2 2 0 0 0-2-2Zm0 15a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V10h14Z"/>
                                    </svg>
                                    {{ t('dueAt') }}: {{ formatDueDate(assignmentData.due_at) }}
                                </span>
                            </div>

                            <!-- Описание -->
                            <div
                                v-if="assignmentData.description"
                                class="mt-4 text-sm subtitle"
                                v-html="assignmentData.description"
                            />
                        </article>

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
        <!-- Подвал и прогресс -->
        <FooterBlog />
        <Progress />
    </DefaultLayout>
</template>
