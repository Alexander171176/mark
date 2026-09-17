<script setup>
/**
 * Страница конкретного модуля курса.
 *
 * Public-контракт:
 * module.translation
 * module.images
 * module.course
 *
 * Уроки временно остаются на старом Public-контракте
 * до отдельного рефакторинга SchoolLesson.
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
import LikeButtonEntity from '@/Components/Public/Like/LikeButtonEntity.vue'

import SectionVideoList from '@/Components/Public/Default/Blog/BlogVideo/SectionVideoList.vue'
import SectionBanners from '@/Components/Public/Default/Blog/BlogBanner/SectionBanners.vue'

import ModuleLessonsSection from '@/Components/Public/Default/School/SchoolLesson/ModuleLessonsSection.vue'

const { t } = useI18n()
const page = usePage()

/* ===================== PROPS ===================== */

const props = defineProps({
    title: String,
    canLogin: Boolean,
    canRegister: Boolean,

    module: { type: Object, default: () => ({}) },
    lessons: { type: [Array, Object], default: () => [] },

    trackTree: { type: Array, default: () => [] },
    locale: { type: String, default: 'ru' },

    mainVideos: { type: Array, default: () => [] },
    mainBanners: { type: Array, default: () => [] },
})

/* ===================== HELPERS ===================== */

const normalizeList = (value) => {
    if (Array.isArray(value)) return value
    if (Array.isArray(value?.data)) return value.data
    return []
}

const absoluteUrl = (value) => {
    if (!value) return ''
    if (/^https?:\/\//i.test(value)) return value
    if (typeof window === 'undefined') return value

    try {
        return new URL(value, window.location.origin).href
    } catch {
        return value
    }
}

const stripHtml = (value) => {
    if (!value) return ''

    return String(value)
        .replace(/<[^>]*>/g, ' ')
        .replace(/\s+/g, ' ')
        .trim()
}

/* ===================== MODULE ===================== */

const moduleData = computed(() => props.module ?? {})

/**
 * Backend уже выполнил:
 * current locale → fallback locale.
 */
const translation = computed(() => moduleData.value?.translation ?? {})

const moduleTitle = computed(() => translation.value?.title ?? '')
const moduleSubtitle = computed(() => translation.value?.subtitle ?? '')
const moduleShort = computed(() => translation.value?.short ?? '')
const moduleDescription = computed(() => translation.value?.description ?? '')

/* ===================== COURSE ===================== */

const parentCourse = computed(() => moduleData.value?.course ?? null)
const courseTranslation = computed(() => parentCourse.value?.translation ?? {})

const courseTitle = computed(() =>
    courseTranslation.value?.title || t('course')
)

const courseRouteUrl = computed(() => {
    if (!parentCourse.value?.slug) return ''

    return route('public.schoolCourses.show', {
        slug: parentCourse.value.slug,
    })
})

const courseUrl = computed(() => absoluteUrl(courseRouteUrl.value))

/* ===================== RELATIONS ===================== */

const lessonsList = computed(() => normalizeList(props.lessons))
const trackTree = computed(() =>
    Array.isArray(props.trackTree) ? props.trackTree : []
)

const mainVideosList = computed(() => normalizeList(props.mainVideos))
const mainBannersList = computed(() => normalizeList(props.mainBanners))

const lessonsCount = computed(() =>
    Number(moduleData.value?.lessons_count ?? lessonsList.value.length ?? 0) || 0
)

/* ===================== IMAGES ===================== */

const moduleImages = computed(() => normalizeList(moduleData.value?.images))
const firstModuleImage = computed(() => moduleImages.value[0] ?? null)
const hasModuleImages = computed(() => moduleImages.value.length > 0)

const firstModuleImageUrl = computed(() =>
    absoluteUrl(
        firstModuleImage.value?.webp_url
        || firstModuleImage.value?.image_url
        || firstModuleImage.value?.url
        || firstModuleImage.value?.thumb_url
        || ''
    )
)

/* ===================== SEO ===================== */

const seoTitle = computed(() =>
    translation.value?.meta_title || moduleTitle.value || t('modules')
)

const seoDescription = computed(() =>
    translation.value?.meta_desc
    || moduleShort.value
    || stripHtml(moduleDescription.value)
    || ''
)

const seoKeywords = computed(() =>
    translation.value?.meta_keywords ?? ''
)

const contentLocale = computed(() =>
    translation.value?.locale || props.locale || 'ru'
)

const ogLocale = computed(() => {
    try {
        const locale = new Intl.Locale(contentLocale.value).maximize()
        return `${locale.language}_${locale.region || locale.language.toUpperCase()}`
    } catch {
        return contentLocale.value
    }
})

const moduleRouteUrl = computed(() => {
    if (!parentCourse.value?.slug || !moduleData.value?.slug) return ''

    return route('public.schoolModules.show', {
        courseSlug: parentCourse.value.slug,
        slug: moduleData.value.slug,
    })
})

const canonicalUrl = computed(() => absoluteUrl(moduleRouteUrl.value))
const coursesIndexUrl = computed(() => absoluteUrl(route('public.schoolCourses.index')))
const homeUrl = computed(() => absoluteUrl(route('home')))

const siteName = computed(() =>
    page.props?.siteSettings?.siteName
    || page.props?.siteSettings?.SiteName
    || ''
)

/* ===================== SCHEMA.ORG ===================== */

const schemaDuration = computed(() => {
    const minutes = Number(moduleData.value?.duration)

    if (!Number.isFinite(minutes) || minutes <= 0) return null

    return `PT${Math.round(minutes)}M`
})

const moduleSchema = computed(() => {
    const schema = {
        '@context': 'https://schema.org',
        '@type': 'LearningResource',
        name: moduleTitle.value || seoTitle.value,
        url: canonicalUrl.value || moduleRouteUrl.value,
        inLanguage: contentLocale.value,
    }

    const description = stripHtml(
        seoDescription.value
        || moduleShort.value
        || moduleDescription.value
    )

    if (description) schema.description = description
    if (firstModuleImageUrl.value) schema.image = firstModuleImageUrl.value
    if (moduleData.value?.published_at) schema.datePublished = moduleData.value.published_at
    if (schemaDuration.value) schema.timeRequired = schemaDuration.value

    if (parentCourse.value && courseTitle.value) {
        schema.isPartOf = {
            '@type': 'Course',
            name: courseTitle.value,
        }

        if (courseUrl.value) {
            schema.isPartOf.url = courseUrl.value
        }
    }

    const ratingAvg = Number(moduleData.value?.rating_avg)
    const ratingCount = Number(moduleData.value?.rating_count)

    if (
        Number.isFinite(ratingAvg)
        && ratingAvg > 0
        && Number.isFinite(ratingCount)
        && ratingCount > 0
    ) {
        schema.aggregateRating = {
            '@type': 'AggregateRating',
            ratingValue: ratingAvg,
            ratingCount,
            bestRating: 5,
            worstRating: 1,
        }
    }

    const interactions = []

    const views = Number(moduleData.value?.views)
    const likes = Number(moduleData.value?.likes_count)

    if (Number.isFinite(views) && views > 0) {
        interactions.push({
            '@type': 'InteractionCounter',
            interactionType: { '@type': 'ViewAction' },
            userInteractionCount: views,
        })
    }

    if (Number.isFinite(likes) && likes > 0) {
        interactions.push({
            '@type': 'InteractionCounter',
            interactionType: { '@type': 'LikeAction' },
            userInteractionCount: likes,
        })
    }

    if (interactions.length) {
        schema.interactionStatistic = interactions
    }

    return schema
})

const breadcrumbSchema = computed(() => {
    const items = [
        {
            '@type': 'ListItem',
            position: 1,
            name: t('home'),
            item: homeUrl.value,
        },
        {
            '@type': 'ListItem',
            position: 2,
            name: t('courses'),
            item: coursesIndexUrl.value,
        },
    ]

    if (parentCourse.value?.slug) {
        items.push({
            '@type': 'ListItem',
            position: 3,
            name: courseTitle.value,
            item: courseUrl.value,
        })
    }

    items.push({
        '@type': 'ListItem',
        position: items.length + 1,
        name: moduleTitle.value || seoTitle.value,
        item: canonicalUrl.value || moduleRouteUrl.value,
    })

    return {
        '@context': 'https://schema.org',
        '@type': 'BreadcrumbList',
        itemListElement: items,
    }
})

const moduleSchemaJson = computed(() => JSON.stringify(moduleSchema.value))
const breadcrumbSchemaJson = computed(() => JSON.stringify(breadcrumbSchema.value))

/* ===================== SIDEBARS ===================== */

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

    if (leftExpanded && rightExpanded) return 2
    if (leftExpanded || rightExpanded) return 3

    return 4
})
</script>

<template>
    <!-- SEO -->
    <Head>
        <title>{{ seoTitle }}</title>

        <meta name="title" :content="seoTitle" />
        <meta name="description" :content="seoDescription" />
        <meta v-if="seoKeywords" name="keywords" :content="seoKeywords" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="robots" content="index, follow, max-image-preview:large" />
        <meta name="googlebot" content="index, follow, max-image-preview:large" />

        <link v-if="canonicalUrl" rel="canonical" :href="canonicalUrl" />

        <!-- Open Graph -->
        <meta property="og:title" :content="seoTitle" />
        <meta property="og:description" :content="seoDescription" />
        <meta property="og:type" content="website" />
        <meta v-if="canonicalUrl" property="og:url" :content="canonicalUrl" />
        <meta v-if="firstModuleImageUrl" property="og:image" :content="firstModuleImageUrl" />
        <meta v-if="firstModuleImageUrl" property="og:image:alt" :content="moduleTitle" />
        <meta property="og:locale" :content="ogLocale" />
        <meta v-if="siteName" property="og:site_name" :content="siteName" />

        <!-- Twitter -->
        <meta
            name="twitter:card"
            :content="firstModuleImageUrl ? 'summary_large_image' : 'summary'"
        />
        <meta name="twitter:title" :content="seoTitle" />
        <meta name="twitter:description" :content="seoDescription" />
        <meta v-if="firstModuleImageUrl" name="twitter:image" :content="firstModuleImageUrl" />
        <meta v-if="firstModuleImageUrl" name="twitter:image:alt" :content="moduleTitle" />

        <!-- Dublin Core -->
        <meta name="DC.Title" :content="seoTitle" />
        <meta name="DC.Description" :content="seoDescription" />
        <meta v-if="seoKeywords" name="DC.Subject" :content="seoKeywords" />
        <meta name="DC.Type" content="Text" />
        <meta name="DC.Format" content="text/html" />
        <meta name="DC.Language" :content="contentLocale" />
        <meta v-if="canonicalUrl" name="DC.Identifier" :content="canonicalUrl" />
        <meta v-if="siteName" name="DC.Publisher" :content="siteName" />

        <!-- Schema.org -->
        <component :is="'script'" type="application/ld+json" v-text="moduleSchemaJson" />
        <component :is="'script'" type="application/ld+json" v-text="breadcrumbSchemaJson" />
    </Head>

    <DefaultLayout :title="title" :can-login="canLogin" :can-register="canRegister">
        <Navbar />

        <div class="min-h-screen px-1.5">
            <main class="mx-auto flex flex-col lg:flex-row gap-4 tracking-wider">
                <!-- LEFT -->
                <aside
                    v-if="showLeft"
                    class="shrink-0 mt-12 lg:mt-28 pl-3 transition-all duration-300"
                    :class="leftCollapsed ? 'lg:w-10' : 'lg:w-64'"
                >
                    <LeftSidebarSchool
                        :track-tree="trackTree"
                        :collapsed="leftCollapsed"
                        @collapsed="leftCollapsed = $event"
                    />
                </aside>

                <!-- CENTER -->
                <section class="w-full lg:mt-28 pb-6 slate-1 min-w-0">
                    <div class="mx-auto max-w-6xl">
                        <article
                            class="selection:bg-red-400 selection:text-white"
                            itemscope
                            itemtype="https://schema.org/LearningResource"
                            :itemid="canonicalUrl"
                        >
                            <meta itemprop="url" :content="canonicalUrl" />
                            <meta itemprop="inLanguage" :content="contentLocale" />
                            <meta
                                v-if="moduleData.published_at"
                                itemprop="datePublished"
                                :content="moduleData.published_at"
                            />
                            <meta
                                v-if="schemaDuration"
                                itemprop="timeRequired"
                                :content="schemaDuration"
                            />

                            <!-- Breadcrumbs -->
                            <nav class="text-sm mb-3" aria-label="Breadcrumb">
                                <ol class="flex flex-wrap items-center font-semibold">
                                    <li>
                                        <Link :href="route('home')" class="breadcrumb-link hover:underline">
                                            {{ t('home') }}
                                        </Link>
                                    </li>

                                    <li aria-hidden="true">
                                        <span class="mx-2 breadcrumbs">/</span>
                                    </li>

                                    <li>
                                        <Link
                                            :href="route('public.schoolCourses.index')"
                                            class="breadcrumb-link hover:underline"
                                        >
                                            {{ t('courses') }}
                                        </Link>
                                    </li>

                                    <template v-if="parentCourse?.slug">
                                        <li aria-hidden="true">
                                            <span class="mx-2 breadcrumbs">/</span>
                                        </li>

                                        <li>
                                            <Link
                                                :href="route('public.schoolCourses.show', { slug: parentCourse.slug })"
                                                class="breadcrumb-link hover:underline"
                                            >
                                                {{ courseTitle }}
                                            </Link>
                                        </li>
                                    </template>

                                    <li aria-hidden="true">
                                        <span class="mx-2 breadcrumbs">/</span>
                                    </li>

                                    <li class="breadcrumbs" aria-current="page">
                                        {{ moduleTitle }}
                                    </li>
                                </ol>
                            </nav>

                            <!-- Title / views -->
                            <div class="flex flex-wrap items-center justify-center gap-3 title my-3">
                                <h1 class="text-2xl font-bold" itemprop="name">
                                    {{ moduleTitle }}
                                </h1>

                                <div
                                    v-if="moduleData.views > 0"
                                    :title="t('views')"
                                    class="flex items-center justify-center gap-1"
                                    itemprop="interactionStatistic"
                                    itemscope
                                    itemtype="https://schema.org/InteractionCounter"
                                >
                                    <link itemprop="interactionType" href="https://schema.org/ViewAction" />
                                    <meta itemprop="userInteractionCount" :content="moduleData.views" />

                                    <svg
                                        class="h-4 w-4 text-slate-600/85 dark:text-slate-200/85"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 576 512"
                                        fill="currentColor"
                                        aria-hidden="true"
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
                                v-if="moduleSubtitle"
                                class="mt-1 mb-3 text-sm subtitle text-center"
                            >
                                {{ moduleSubtitle }}
                            </div>

                            <!-- Main image -->
                            <div
                                v-if="hasModuleImages"
                                class="flex items-center justify-center"
                            >
                                <div class="w-full">
                                    <ImageGalleryMain
                                        :images="moduleImages"
                                        :alt="moduleTitle"
                                        itemprop="image"
                                        loading="eager"
                                        fetchpriority="high"
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
                                <!-- Lessons -->
                                <span
                                    v-if="lessonsCount"
                                    class="rounded-sm border border-gray-400
                                           flex items-center justify-center gap-1 px-3 py-1"
                                >
                                    <svg
                                        class="h-4 w-4 text-sky-600/85 dark:text-sky-300/85"
                                        fill="currentColor"
                                        viewBox="0 0 24 24"
                                        aria-hidden="true"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"
                                        />
                                    </svg>

                                    {{ t('lessons') }}: {{ lessonsCount }}
                                </span>

                                <!-- Duration -->
                                <span
                                    v-if="moduleData.duration"
                                    class="rounded-sm border border-gray-400
                                           flex items-center justify-center gap-1 px-3 py-1"
                                >
                                    <svg
                                        class="w-3 h-3 text-blue-700 dark:text-blue-300"
                                        viewBox="0 0 24 24"
                                        aria-hidden="true"
                                    >
                                        <path
                                            class="fill-current"
                                            d="M22,13a1,1,0,0,1,0-2h1.949A12.006,12.006,0,0,0,13,.051V2a1,1,0,0,1-2,0V.051A12.006,12.006,0,0,0,.051,11H2a1,1,0,0,1,0,2H.051A12.006,12.006,0,0,0,11,23.949V22a1,1,0,0,1,2,0v1.949A12.006,12.006,0,0,0,23.949,13Zm-6,0H12a1,1,0,0,1-.832-.445l-4-6a1,1,0,1,1,1.664-1.11L12.535,11H16a1,1,0,0,1,0,2Z"
                                        />
                                    </svg>

                                    {{ t('duration') }}:
                                    {{ moduleData.duration }}
                                    {{ t('minutes') }}
                                </span>

                                <!-- Rating -->
                                <span
                                    v-if="moduleData.rating_avg"
                                    class="rounded-sm border border-gray-400
                                           flex items-center justify-center gap-1 px-3 py-1"
                                    itemprop="aggregateRating"
                                    itemscope
                                    itemtype="https://schema.org/AggregateRating"
                                >
                                    <meta itemprop="ratingValue" :content="moduleData.rating_avg" />
                                    <meta itemprop="ratingCount" :content="moduleData.rating_count || 0" />
                                    <meta itemprop="bestRating" content="5" />
                                    <meta itemprop="worstRating" content="1" />

                                    <svg
                                        viewBox="0 0 24 24"
                                        class="h-3 w-3 text-red-400 dark:text-red-300"
                                        aria-hidden="true"
                                    >
                                        <path
                                            class="fill-current"
                                            d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"
                                        />
                                    </svg>

                                    {{ t('rating') }}:
                                    {{ Number(moduleData.rating_avg).toFixed(1) }}
                                </span>

                                <!-- Rating count -->
                                <span
                                    v-if="moduleData.rating_count"
                                    class="rounded-sm border border-gray-400
                                           flex items-center justify-center gap-1 px-3 py-1"
                                >
                                    <svg
                                        class="w-3 h-3 text-teal-600/85 dark:text-teal-300/85"
                                        fill="currentColor"
                                        viewBox="0 0 512 512"
                                        aria-hidden="true"
                                    >
                                        <path
                                            d="M448 0H64C28.7 0 0 28.7 0 64v288c0 35.3 28.7 64 64 64h96v84c0 9.8 11.2 15.5 19.1 9.7L304 416h144c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64z"
                                        />
                                    </svg>

                                    {{ t('reviews') }}:
                                    {{ moduleData.rating_count }}
                                </span>
                            </div>

                            <!-- Description -->
                            <div
                                v-if="moduleDescription"
                                class="mt-4 text-sm subtitle"
                                itemprop="description"
                                v-html="moduleDescription"
                            />

                            <!-- Like -->
                            <div class="my-1 flex items-center justify-center">
                                <div
                                    itemprop="interactionStatistic"
                                    itemscope
                                    itemtype="https://schema.org/InteractionCounter"
                                >
                                    <link itemprop="interactionType" href="https://schema.org/LikeAction" />
                                    <meta
                                        itemprop="userInteractionCount"
                                        :content="moduleData.likes_count || 0"
                                    />

                                    <LikeButtonEntity
                                        :likes-count="moduleData.likes_count || 0"
                                        :already-liked="moduleData.already_liked || false"
                                        route-name="public.schoolModules.like"
                                        :route-params="moduleData.id"
                                        :title="t('like')"
                                    />
                                </div>
                            </div>

                            <!-- Parent course -->
                            <div
                                v-if="parentCourse?.slug"
                                class="mt-6 flex items-center justify-center gap-2 text-sm"
                                itemprop="isPartOf"
                                itemscope
                                itemtype="https://schema.org/Course"
                            >
                                <link itemprop="url" :href="courseUrl" />

                                <span
                                    class="flex items-center justify-center gap-0.5
                                           text-slate-500 dark:text-slate-400 uppercase"
                                >
                                    <svg
                                        class="h-5 w-5 text-slate-600/85 dark:text-slate-200/85"
                                        fill="currentColor"
                                        viewBox="0 0 24 24"
                                        aria-hidden="true"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"
                                        />
                                    </svg>

                                    {{ t('course') }}:
                                </span>

                                <Link
                                    :href="route('public.schoolCourses.show', { slug: parentCourse.slug })"
                                    class="font-semibold text-indigo-700 hover:underline dark:text-indigo-300"
                                    itemprop="name"
                                >
                                    {{ courseTitle }}
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
                    class="shrink-0 lg:mt-28 pr-3 transition-all duration-300"
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
