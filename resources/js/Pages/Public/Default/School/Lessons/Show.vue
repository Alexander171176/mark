<script setup>
/**
 * Страница конкретного урока
 * - SEO
 * - хлебные крошки
 * - основное изображение / галерея
 * - модуль и курс
 * - хештеги
 * - лайк
 * - контент урока
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

const { t } = useI18n()
const page = usePage()

const props = defineProps({
    title: String,
    canLogin: Boolean,
    canRegister: Boolean,

    lesson: { type: Object, default: () => ({}) },
    trackTree: { type: Array, default: () => [] },

    mainVideos: { type: [Array, Object], default: () => [] },
    mainBanners: { type: [Array, Object], default: () => [] },
})

const normalizeList = (value) => {
    if (Array.isArray(value)) return value
    if (Array.isArray(value?.data)) return value.data
    return []
}

const lessonData = computed(() => props.lesson ?? {})
const trackTree = computed(() => Array.isArray(props.trackTree) ? props.trackTree : [])

const mainVideosList = computed(() => normalizeList(props.mainVideos))
const mainBannersList = computed(() => normalizeList(props.mainBanners))

const lessonImages = computed(() => {
    return Array.isArray(lessonData.value?.images) ? lessonData.value.images : []
})

const firstLessonImage = computed(() => {
    return lessonImages.value.length ? lessonImages.value[0] : null
})

const hasLessonImages = computed(() => lessonImages.value.length > 0)

const hashtags = computed(() => normalizeList(lessonData.value?.hashtags))
const moduleData = computed(() => lessonData.value?.module || null)
const courseData = computed(() => lessonData.value?.course || lessonData.value?.module?.course || null)
const contentData = computed(() => lessonData.value?.content || null)

const { siteSettings } = page.props

const showLeft = computed(() =>
    !siteSettings?.ViewLeftColumn || siteSettings.ViewLeftColumn === 'true'
)

const showRight = computed(() =>
    !siteSettings?.ViewRightColumn || siteSettings.ViewRightColumn === 'true'
)

const leftCollapsed = ref(false)
const rightCollapsed = ref(false)

const translateAccessType = (value) => {
    const normalized = (value || '').toString().trim().toLowerCase()

    const map = {
        free: 'free',
        paid: 'paid',
        preview: 'preview',
    }

    return map[normalized] ? t(map[normalized]) : value
}
</script>

<template>
    <Head>
        <title>{{ lessonData.meta_title || lessonData.title || '' }}</title>

        <meta name="title" :content="lessonData.meta_title || lessonData.title || ''" />
        <meta name="description" :content="lessonData.meta_desc || lessonData.short || ''" />
        <meta name="keywords" :content="lessonData.meta_keywords || ''" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <meta property="og:title" :content="lessonData.meta_title || lessonData.title || ''" />
        <meta property="og:description" :content="lessonData.meta_desc || lessonData.short || ''" />
        <meta property="og:type" content="website" />
        <meta property="og:url" :content="`/school/lessons/${lessonData.slug || ''}`" />
        <meta
            property="og:image"
            :content="firstLessonImage ? (firstLessonImage.webp_url || firstLessonImage.url || firstLessonImage.image_url) : ''"
        />
        <meta property="og:locale" :content="lessonData.locale || 'ru_RU'" />

        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="lessonData.meta_title || lessonData.title || ''" />
        <meta name="twitter:description" :content="lessonData.meta_desc || lessonData.short || ''" />
        <meta
            name="twitter:image"
            :content="firstLessonImage ? (firstLessonImage.webp_url || firstLessonImage.url || firstLessonImage.image_url) : ''"
        />

        <meta name="DC.Title" :content="lessonData.meta_title || lessonData.title || ''" />
        <meta name="DC.Description" :content="lessonData.meta_desc || lessonData.short || ''" />
        <meta name="DC.Subject" :content="lessonData.meta_keywords || ''" />
        <meta name="DC.Type" content="Text" />
        <meta name="DC.Format" content="text/html" />
        <meta name="DC.Language" :content="lessonData.locale || 'ru'" />
        <meta name="DC.Identifier" :content="`/school/lessons/${lessonData.slug || ''}`" />
    </Head>

    <DefaultLayout :title="title" :can-login="canLogin" :can-register="canRegister">
        <Navbar />

        <div class="min-h-screen px-1.5">
            <main class="mx-auto flex flex-col lg:flex-row gap-4 tracking-wider">
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

                <section class="w-full lg:mt-16 pb-6 slate-1 min-w-0">
                    <div class="mx-auto max-w-6xl">
                        <article class="selection:bg-red-400 selection:text-white">
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

                                    <li><span class="mx-2 breadcrumbs">/</span></li>

                                    <li>
                                        <Link
                                            :href="route('public.lessons.index')"
                                            class="breadcrumb-link hover:underline"
                                        >
                                            {{ t('lessons') }}
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

                                    <li><span class="mx-2 breadcrumbs">/</span></li>
                                    <li class="breadcrumbs">
                                        {{ lessonData.title }}
                                    </li>
                                </ol>
                            </nav>

                            <div class="flex flex-wrap items-center justify-center gap-3 title my-3">
                                <h1 class="text-2xl font-bold">
                                    {{ lessonData.title }}
                                </h1>

                                <div
                                    v-if="lessonData.views > 0"
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
                                        {{ lessonData.views }}
                                    </span>
                                </div>
                            </div>

                            <div
                                v-if="lessonData.subtitle"
                                class="mt-1 mb-3 text-sm subtitle text-center"
                            >
                                {{ lessonData.subtitle }}
                            </div>

                            <div
                                v-if="hasLessonImages"
                                class="flex items-center justify-center"
                            >
                                <div class="w-full">
                                    <ImageGalleryMain
                                        :images="lessonImages"
                                        :alt="lessonData.title"
                                        rounded-class="rounded-lg"
                                        shadow-class="shadow-lg shadow-gray-400 dark:shadow-gray-700"
                                        img-class="w-full h-full object-cover"
                                    />
                                </div>
                            </div>

                            <div
                                class="my-4 flex flex-wrap items-center justify-center gap-3
                                       text-sm text-slate-600 dark:text-slate-300"
                            >
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

                                <span
                                    v-if="moduleData?.title"
                                    class="rounded-sm border border-gray-400
                                           flex items-center justify-center gap-1 px-3 py-1"
                                >
                                    <svg class="shrink-0 h-3 w-3 text-teal-600/85
                                                dark:text-teal-200/85"
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

                                <span
                                    v-if="lessonData.access_type"
                                    class="rounded-sm border border-gray-400
                                           flex items-center justify-center gap-1 px-3 py-1"
                                >
                                    <svg
                                        class="h-3 w-3 text-blue-600 dark:text-blue-400"
                                        fill="currentColor"
                                        viewBox="0 0 576 512">
                                        <path d="M423.5 0C339.5.3 272 69.5 272 153.5V224H48c-26.5 0-48 21.5-48 48v192c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V272c0-26.5-21.5-48-48-48h-48v-71.1c0-39.6 31.7-72.5 71.3-72.9 40-.4 72.7 32.1 72.7 72v80c0 13.3 10.7 24 24 24h32c13.3 0 24-10.7 24-24v-80C576 68 507.5-.3 423.5 0z"/>
                                    </svg>
                                    {{ t('access') }}: {{ translateAccessType(lessonData.access_type) }}
                                </span>

                                <span
                                    v-if="lessonData.rating_avg"
                                    class="rounded-sm border border-gray-400
                                           flex items-center justify-center gap-1 px-3 py-1"
                                >
                                    <svg viewBox="0 0 24 24" class="h-3 w-3 text-red-400 dark:text-red-300">
                                        <path class="fill-current"
                                              d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"></path>
                                    </svg>
                                    {{ t('rating') }}: {{ Number(lessonData.rating_avg).toFixed(1) }}
                                </span>

                                <span
                                    v-if="lessonData.duration"
                                    class="rounded-sm border border-gray-400
                                           flex items-center justify-center gap-1 px-3 py-1"
                                >
                                    <svg class="w-3 h-3 text-blue-700 dark:text-blue-300" viewBox="0 0 24 24">
                                        <path class="fill-current"
                                              d="M22,13a1,1,0,0,1,0-2h1.949A12.006,12.006,0,0,0,13,.051V2a1,1,0,0,1-2,0V.051A12.006,12.006,0,0,0,.051,11H2a1,1,0,0,1,0,2H.051A12.006,12.006,0,0,0,11,23.949V22a1,1,0,0,1,2,0v1.949A12.006,12.006,0,0,0,23.949,13Zm-6,0H12a1,1,0,0,1-.832-.445l-4-6a1,1,0,1,1,1.664-1.11L12.535,11H16a1,1,0,0,1,0,2Z"></path>
                                    </svg>
                                    {{ t('duration') }} {{ t('minutes') }} {{ lessonData.duration }}
                                </span>
                            </div>

                            <div
                                v-if="lessonData.description"
                                class="mt-4 text-sm subtitle"
                                v-html="lessonData.description"
                            />

                            <div class="my-1 flex items-center justify-center">
                                <LikeButtonEntity
                                    :likes-count="lessonData.likes_count || lessonData.likes || 0"
                                    :already-liked="lessonData.already_liked || false"
                                    route-name="lessons.like"
                                    :route-params="{ lesson: lessonData.id }"
                                    :title="t('like')"
                                />
                            </div>

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

                            <div
                                v-if="contentData"
                                class="mt-8 rounded-md border border-gray-200 bg-white p-4 shadow-sm
                                       dark:border-gray-700 dark:bg-gray-900"
                            >
                                <h2 class="mb-3 text-center text-lg font-semibold text-gray-700 dark:text-gray-300">
                                    {{ t('content') }}
                                </h2>

                                <div class="flex flex-wrap items-center justify-center gap-3 text-sm">
                                    <span
                                        v-if="contentData.type"
                                        class="rounded-sm border border-gray-400 px-3 py-1"
                                    >
                                        {{ t('type') }}: {{ contentData.type }}
                                    </span>

                                    <Link
                                        v-if="contentData.slug"
                                        :href="`#`"
                                        class="rounded-sm border border-gray-400 px-3 py-1 hover:underline"
                                    >
                                        {{ contentData.title || t('open') }}
                                    </Link>

                                    <span v-else>
                                        {{ contentData.title }}
                                    </span>
                                </div>
                            </div>
                        </article>

                        <SectionVideoList :videos="mainVideosList" />
                        <SectionBanners :banners="mainBannersList" />
                    </div>
                </section>

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
