<script setup>
/**
 * Страница конкретного модуля курса
 * - SEO
 * - хлебные крошки
 * - основное изображение / галерея
 * - модуль
 * - уроки обучения
 * - хештеги
 * - лайк
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
import ImageGalleryMain from '@/Components/Public/Default/Media/ImageGalleryMain.vue'
import SectionVideoList from '@/Components/Public/Default/Blog/Video/SectionVideoList.vue'
import SectionBanners from '@/Components/Public/Default/Blog/Banner/SectionBanners.vue'
import ModuleLessonsSection from '@/Components/Public/Default/School/Lesson/ModuleLessonsSection.vue'
import LikeButtonEntity from '@/Components/Public/Like/LikeButtonEntity.vue'

const { t } = useI18n()
const page = usePage()

const props = defineProps({
    title: String,
    canLogin: Boolean,
    canRegister: Boolean,

    module: { type: Object, default: () => ({}) },
    trackTree: { type: Array, default: () => [] },

    mainVideos: { type: Array, default: () => [] },
    mainBanners: { type: Array, default: () => [] },
})

const normalizeList = (value) => {
    if (Array.isArray(value)) return value
    if (Array.isArray(value?.data)) return value.data
    return []
}

const moduleData = computed(() => props.module ?? {})
const trackTree = computed(() => Array.isArray(props.trackTree) ? props.trackTree : [])

const mainVideosList = computed(() => normalizeList(props.mainVideos))
const mainBannersList = computed(() => normalizeList(props.mainBanners))

const moduleImages = computed(() => {
    return Array.isArray(moduleData.value?.images) ? moduleData.value.images : []
})

const firstModuleImage = computed(() => {
    return moduleImages.value.length ? moduleImages.value[0] : null
})

const hasModuleImages = computed(() => moduleImages.value.length > 0)

const lessonsList = computed(() => normalizeList(moduleData.value?.lessons))
const parentCourse = computed(() => moduleData.value?.course || null)

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

const lessonsCount = computed(() => {
    return Number(moduleData.value?.lessons_count_loaded ?? moduleData.value?.lessons_count ?? lessonsList.value.length ?? 0) || 0
})
</script>

<template>
    <Head>
        <title>{{ moduleData.meta_title || moduleData.title || '' }}</title>

        <meta name="title" :content="moduleData.meta_title || moduleData.title || ''" />
        <meta name="description" :content="moduleData.meta_desc || moduleData.short || ''" />
        <meta name="keywords" :content="moduleData.meta_keywords || ''" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <meta property="og:title" :content="moduleData.meta_title || moduleData.title || ''" />
        <meta property="og:description" :content="moduleData.meta_desc || moduleData.short || ''" />
        <meta property="og:type" content="website" />
        <meta property="og:url" :content="`/school/modules/${moduleData.slug || ''}`" />
        <meta
            property="og:image"
            :content="firstModuleImage ? (firstModuleImage.webp_url || firstModuleImage.url || firstModuleImage.image_url) : ''"
        />
        <meta property="og:locale" :content="moduleData.locale || 'ru_RU'" />

        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="moduleData.meta_title || moduleData.title || ''" />
        <meta name="twitter:description" :content="moduleData.meta_desc || moduleData.short || ''" />
        <meta
            name="twitter:image"
            :content="firstModuleImage ? (firstModuleImage.webp_url || firstModuleImage.url || firstModuleImage.image_url) : ''"
        />

        <meta name="DC.Title" :content="moduleData.meta_title || moduleData.title || ''" />
        <meta name="DC.Description" :content="moduleData.meta_desc || moduleData.short || ''" />
        <meta name="DC.Subject" :content="moduleData.meta_keywords || ''" />
        <meta name="DC.Type" content="Text" />
        <meta name="DC.Format" content="text/html" />
        <meta name="DC.Language" :content="moduleData.locale || 'ru'" />
        <meta name="DC.Identifier" :content="`/school/modules/${moduleData.slug || ''}`" />
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
                                        <Link :href="route('home')" class="breadcrumb-link hover:underline">
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

                                    <li v-if="parentCourse?.slug"><span class="mx-2 breadcrumbs">/</span></li>

                                    <li v-if="parentCourse?.slug">
                                        <Link
                                            :href="route('public.courses.show', { slug: parentCourse.slug })"
                                            class="breadcrumb-link hover:underline"
                                        >
                                            {{ parentCourse.title }}
                                        </Link>
                                    </li>

                                    <li><span class="mx-2 breadcrumbs">/</span></li>

                                    <li class="breadcrumbs">
                                        {{ moduleData.title }}
                                    </li>
                                </ol>
                            </nav>

                            <!-- Title / stats -->
                            <div class="flex flex-wrap items-center justify-center gap-3 title my-3">
                                <h1 class="text-2xl font-bold">
                                    {{ moduleData.title }}
                                </h1>

                                <div
                                    v-if="moduleData.views > 0"
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
                                        {{ moduleData.views }}
                                    </span>
                                </div>
                            </div>

                            <!-- Subtitle -->
                            <div
                                v-if="moduleData.subtitle"
                                class="mt-1 mb-3 text-sm subtitle text-center"
                            >
                                {{ moduleData.subtitle }}
                            </div>

                            <!-- Main image -->
                            <div
                                v-if="hasModuleImages"
                                class="flex items-center justify-center"
                            >
                                <div class="w-full">
                                    <ImageGalleryMain
                                        :images="moduleImages"
                                        :alt="moduleData.title"
                                        rounded-class="rounded-lg"
                                        shadow-class="shadow-lg shadow-gray-400 dark:shadow-gray-700"
                                        img-class="w-full h-full object-cover"
                                    />
                                </div>
                            </div>

                            <!-- Meta info -->
                            <div
                                class="my-4 flex flex-wrap items-center justify-center gap-3
                                       text-sm text-slate-600 dark:text-slate-300"
                            >
                                <span
                                    v-if="lessonsCount"
                                    class="rounded-sm border border-gray-400
                                           flex items-center justify-center gap-1 px-3 py-1"
                                >
                                    <svg class="h-4 w-4 text-sky-600/85 dark:text-sky-300/85"
                                         fill="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                                    </svg>
                                    {{ t('lessons') }}: {{ lessonsCount }}
                                </span>

                                <span
                                    v-if="moduleData.duration"
                                    class="rounded-sm border border-gray-400
                                           flex items-center justify-center gap-1 px-3 py-1"
                                >
                                    <svg class="w-3 h-3 text-blue-700 dark:text-blue-300"
                                         viewBox="0 0 24 24">
                                        <path class="fill-current"
                                              d="M22,13a1,1,0,0,1,0-2h1.949A12.006,12.006,0,0,0,13,.051V2a1,1,0,0,1-2,0V.051A12.006,12.006,0,0,0,.051,11H2a1,1,0,0,1,0,2H.051A12.006,12.006,0,0,0,11,23.949V22a1,1,0,0,1,2,0v1.949A12.006,12.006,0,0,0,23.949,13Zm-6,0H12a1,1,0,0,1-.832-.445l-4-6a1,1,0,1,1,1.664-1.11L12.535,11H16a1,1,0,0,1,0,2Z"></path>
                                    </svg>
                                    {{ t('duration') }} {{ t('minutes') }} {{ moduleData.duration }}
                                </span>

                                <span
                                    v-if="moduleData.rating_avg"
                                    class="rounded-sm border border-gray-400
                                           flex items-center justify-center gap-1 px-3 py-1"
                                >
                                    <svg viewBox="0 0 24 24"
                                         class="h-3 w-3 text-red-400 dark:text-red-300">
                                        <path class="fill-current"
                                              d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"></path>
                                    </svg>
                                    {{ t('rating') }}: {{ Number(moduleData.rating_avg).toFixed(1) }}
                                </span>

                                <span
                                    v-if="moduleData.rating_count"
                                    class="rounded-sm border border-gray-400
                                           flex items-center justify-center gap-1 px-3 py-1"
                                >
                                    <svg class="w-3 h-3 text-teal-600/85 dark:text-teal-300/85"
                                         fill="currentColor"
                                         viewBox="0 0 512 512">
                                        <path d="M448 0H64C28.7 0 0 28.7 0 64v288c0 35.3 28.7 64 64 64h96v84c0 9.8 11.2 15.5 19.1 9.7L304 416h144c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64z"></path>
                                    </svg>
                                    {{ t('reviews') }}: {{ moduleData.rating_count }}
                                </span>
                            </div>

                            <!-- Description -->
                            <div
                                v-if="moduleData.description"
                                class="mt-4 text-sm subtitle"
                                v-html="moduleData.description"
                            />

                            <!-- Like -->
                            <div class="my-1 flex items-center justify-center">
                                <LikeButtonEntity
                                    :likes-count="moduleData.likes_count || 0"
                                    :already-liked="moduleData.already_liked || false"
                                    route-name="modules.like"
                                    :route-params="{ module: moduleData.id }"
                                    :title="t('like')"
                                />
                            </div>

                            <!-- Parent course -->
                            <div
                                v-if="parentCourse"
                                class="mt-6 flex items-center justify-center gap-2 text-sm"
                            >
                                <span class="flex items-center justify-center gap-0.5
                                             text-slate-500 dark:text-slate-400 uppercase">
                                    <svg class="h-5 w-5 text-slate-600/85 dark:text-slate-200/85"
                                         fill="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                                    </svg>
                                    {{ t('course') }}:
                                </span>

                                <Link
                                    :href="route('public.courses.show', { slug: parentCourse.slug })"
                                    class="font-semibold text-indigo-700 hover:underline dark:text-indigo-300"
                                >
                                    {{ parentCourse.title }}
                                </Link>
                            </div>

                            <!-- Lessons -->
                            <ModuleLessonsSection
                                :lessons="lessonsList"
                                :cols="gridCols"
                            />
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
