<script setup>
/**
 * Страница конкретного публичного урока.
 *
 * Public-контракт:
 * - resolved translation: current locale → fallback locale;
 * - публичный модуль и курс;
 * - публичные хештеги;
 * - изображения;
 * - лайки;
 * - статистика;
 * - расширенный SEO;
 * - Schema.org LearningResource;
 * - Schema.org BreadcrumbList;
 * - боковые колонки;
 * - дерево треков;
 * - главные видео и баннеры.
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
import SectionVideoList from '@/Components/Public/Default/Blog/BlogVideo/SectionVideoList.vue'
import SectionBanners from '@/Components/Public/Default/Blog/BlogBanner/SectionBanners.vue'

const { t } = useI18n()
const page = usePage()

const props = defineProps({
    title: String,
    canLogin: Boolean,
    canRegister: Boolean,

    lesson: {
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

const absoluteUrl = (value) => {
    if (!value) return ''

    if (/^https?:\/\//i.test(value)) {
        return value
    }

    if (typeof window === 'undefined') {
        return value
    }

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

/* ======================== Lesson ======================== */

const lessonData = computed(() => props.lesson ?? {})

/**
 * Backend уже выполнил:
 *
 * current locale → fallback locale.
 *
 * Vue не должен знать список поддерживаемых языков
 * и не выполняет собственный fallback.
 */
const translation = computed(() =>
    lessonData.value?.translation ?? {}
)

const lessonTitle = computed(() =>
    translation.value?.title ?? ''
)

const lessonSubtitle = computed(() =>
    translation.value?.subtitle ?? ''
)

const lessonShort = computed(() =>
    translation.value?.short ?? ''
)

const lessonDescription = computed(() =>
    translation.value?.description ?? ''
)

/* ======================== Relations ======================== */

const moduleData = computed(() =>
    lessonData.value?.module ?? null
)

const courseData = computed(() =>
    moduleData.value?.course ?? null
)

const hashtags = computed(() =>
    normalizeList(lessonData.value?.hashtags)
)

/**
 * Полиморфный content пока оставляем совместимым.
 *
 * SchoolLessonResource сейчас не отдаёт произвольный
 * MorphTo-контент без отдельного Public-контракта.
 */
const contentData = computed(() =>
    lessonData.value?.content ?? null
)

/* ======================== Images ======================== */

const lessonImages = computed(() =>
    normalizeList(lessonData.value?.images)
)

const firstLessonImage = computed(() =>
    lessonImages.value[0] ?? null
)

const hasLessonImages = computed(() =>
    lessonImages.value.length > 0
)

const firstLessonImageUrl = computed(() =>
    absoluteUrl(
        firstLessonImage.value?.webp_url
        || firstLessonImage.value?.image_url
        || firstLessonImage.value?.url
        || firstLessonImage.value?.thumb_url
        || ''
    )
)

/* ======================== SEO ======================== */

const seoTitle = computed(() =>
    translation.value?.meta_title
    || lessonTitle.value
    || t('lessons')
)

const seoDescription = computed(() =>
    translation.value?.meta_desc
    || lessonShort.value
    || stripHtml(lessonDescription.value)
    || ''
)

const seoKeywords = computed(() =>
    translation.value?.meta_keywords ?? ''
)

/**
 * Фактическая локаль resolved translation.
 *
 * Никаких жёстких ru/en:
 * новый язык не требует изменения компонента.
 */
const contentLocale = computed(() =>
    translation.value?.locale
    || page.props?.locale
    || ''
)

const lessonRouteUrl = computed(() => {
    if (!lessonData.value?.slug) return ''

    return route('public.schoolLessons.show', {
        slug: lessonData.value.slug,
    })
})

const canonicalUrl = computed(() =>
    absoluteUrl(lessonRouteUrl.value)
)

const lessonsIndexUrl = computed(() =>
    absoluteUrl(
        route('public.schoolLessons.index')
    )
)

const homeUrl = computed(() =>
    absoluteUrl(
        route('home')
    )
)

const courseUrl = computed(() => {
    if (!courseData.value?.slug) return ''

    return absoluteUrl(
        route('public.schoolCourses.show', {
            slug: courseData.value.slug,
        })
    )
})

const moduleUrl = computed(() => {
    if (
        !courseData.value?.slug
        || !moduleData.value?.slug
    ) {
        return ''
    }

    return absoluteUrl(
        route('public.schoolModules.show', {
            courseSlug: courseData.value.slug,
            slug: moduleData.value.slug,
        })
    )
})

/* ======================== Schema.org ======================== */

const schemaDuration = computed(() => {
    const minutes = Number(lessonData.value?.duration)

    if (!Number.isFinite(minutes) || minutes <= 0) {
        return null
    }

    return `PT${Math.round(minutes)}M`
})

const lessonSchema = computed(() => {
    const schema = {
        '@context': 'https://schema.org',
        '@type': 'LearningResource',
        name: lessonTitle.value || seoTitle.value,
        url: canonicalUrl.value || lessonRouteUrl.value,
        learningResourceType: 'Lesson',
    }

    if (contentLocale.value) {
        schema.inLanguage = contentLocale.value
    }

    const description = stripHtml(
        seoDescription.value
        || lessonShort.value
        || lessonDescription.value
    )

    if (description) {
        schema.description = description
    }

    if (firstLessonImageUrl.value) {
        schema.image = firstLessonImageUrl.value
    }

    if (lessonData.value?.published_at) {
        schema.datePublished =
            lessonData.value.published_at
    }

    if (
        lessonData.value?.difficulty !== null
        && lessonData.value?.difficulty !== undefined
    ) {
        schema.educationalLevel =
            String(lessonData.value.difficulty)
    }

    if (schemaDuration.value) {
        schema.timeRequired =
            schemaDuration.value
    }

    if (seoKeywords.value) {
        schema.keywords =
            seoKeywords.value
    }

    if (courseData.value?.translation?.title) {
        schema.isPartOf = {
            '@type': 'Course',
            name: courseData.value.translation.title,
        }

        if (courseUrl.value) {
            schema.isPartOf.url =
                courseUrl.value
        }
    }

    const interactions = []

    const views =
        Number(lessonData.value?.views)

    const likes =
        Number(lessonData.value?.likes_count)

    if (Number.isFinite(views) && views > 0) {
        interactions.push({
            '@type': 'InteractionCounter',
            interactionType: {
                '@type': 'ViewAction',
            },
            userInteractionCount: views,
        })
    }

    if (Number.isFinite(likes) && likes > 0) {
        interactions.push({
            '@type': 'InteractionCounter',
            interactionType: {
                '@type': 'LikeAction',
            },
            userInteractionCount: likes,
        })
    }

    if (interactions.length) {
        schema.interactionStatistic =
            interactions
    }

    return schema
})

const breadcrumbSchema = computed(() => {
    const items = []

    let position = 1

    items.push({
        '@type': 'ListItem',
        position: position++,
        name: t('home'),
        item: homeUrl.value,
    })

    items.push({
        '@type': 'ListItem',
        position: position++,
        name: t('lessons'),
        item: lessonsIndexUrl.value,
    })

    if (
        courseUrl.value
        && courseData.value?.translation?.title
    ) {
        items.push({
            '@type': 'ListItem',
            position: position++,
            name: courseData.value.translation.title,
            item: courseUrl.value,
        })
    }

    if (
        moduleUrl.value
        && moduleData.value?.translation?.title
    ) {
        items.push({
            '@type': 'ListItem',
            position: position++,
            name: moduleData.value.translation.title,
            item: moduleUrl.value,
        })
    }

    items.push({
        '@type': 'ListItem',
        position,
        name: lessonTitle.value || seoTitle.value,
        item: canonicalUrl.value || lessonRouteUrl.value,
    })

    return {
        '@context': 'https://schema.org',
        '@type': 'BreadcrumbList',
        itemListElement: items,
    }
})

const lessonSchemaJson = computed(() =>
    JSON.stringify(lessonSchema.value)
)

const breadcrumbSchemaJson = computed(() =>
    JSON.stringify(breadcrumbSchema.value)
)

/* ======================== Sidebars ======================== */

const trackTree = computed(() =>
    Array.isArray(props.trackTree)
        ? props.trackTree
        : []
)

const { siteSettings } = page.props

const showLeft = computed(() =>
    !siteSettings?.ViewLeftColumn
    || siteSettings.ViewLeftColumn === 'true'
)

const showRight = computed(() =>
    !siteSettings?.ViewRightColumn
    || siteSettings.ViewRightColumn === 'true'
)

const leftCollapsed = ref(false)
const rightCollapsed = ref(false)

/* ======================== Main media ======================== */

const mainVideosList = computed(() =>
    normalizeList(props.mainVideos)
)

const mainBannersList = computed(() =>
    normalizeList(props.mainBanners)
)

/* ======================== Translations ======================== */

const translateAccessType = (value) => {
    const normalized = String(value ?? '')
        .trim()
        .toLowerCase()

    const map = {
        free: 'free',
        paid: 'paid',
        preview: 'preview',
    }

    return map[normalized]
        ? t(map[normalized])
        : value
}
</script>

<template>
    <Head>
        <title>{{ seoTitle }}</title>

        <meta name="title" :content="seoTitle" />
        <meta name="description" :content="seoDescription" />
        <meta v-if="seoKeywords" name="keywords" :content="seoKeywords" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta
            name="robots"
            content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1"
        />

        <link
            v-if="canonicalUrl"
            rel="canonical"
            :href="canonicalUrl"
        />

        <!-- Open Graph -->
        <meta property="og:title" :content="seoTitle" />
        <meta property="og:description" :content="seoDescription" />
        <meta property="og:type" content="article" />

        <meta
            v-if="canonicalUrl"
            property="og:url"
            :content="canonicalUrl"
        />

        <meta
            v-if="firstLessonImageUrl"
            property="og:image"
            :content="firstLessonImageUrl"
        />

        <meta
            v-if="firstLessonImageUrl"
            property="og:image:alt"
            :content="lessonTitle"
        />

        <meta
            v-if="contentLocale"
            property="og:locale"
            :content="contentLocale"
        />

        <meta
            v-if="title"
            property="og:site_name"
            :content="title"
        />

        <!-- Article -->
        <meta
            v-if="lessonData.published_at"
            property="article:published_time"
            :content="lessonData.published_at"
        />

        <!-- Twitter -->
        <meta
            name="twitter:card"
            :content="firstLessonImageUrl
            ? 'summary_large_image'
            : 'summary'"
        />

        <meta name="twitter:title" :content="seoTitle" />
        <meta name="twitter:description" :content="seoDescription" />

        <meta
            v-if="firstLessonImageUrl"
            name="twitter:image"
            :content="firstLessonImageUrl"
        />

        <meta
            v-if="firstLessonImageUrl"
            name="twitter:image:alt"
            :content="lessonTitle"
        />

        <!-- Dublin Core -->
        <meta name="DC.Title" :content="seoTitle" />
        <meta name="DC.Description" :content="seoDescription" />
        <meta v-if="seoKeywords" name="DC.Subject" :content="seoKeywords" />
        <meta name="DC.Type" content="Text" />
        <meta name="DC.Format" content="text/html" />

        <meta
            v-if="contentLocale"
            name="DC.Language"
            :content="contentLocale"
        />

        <meta
            v-if="canonicalUrl"
            name="DC.Identifier"
            :content="canonicalUrl"
        />

        <meta
            v-if="lessonData.published_at"
            name="DC.Date"
            :content="lessonData.published_at"
        />

        <!-- Schema.org -->
        <component
            :is="'script'"
            type="application/ld+json"
            v-text="lessonSchemaJson"
        />

        <component
            :is="'script'"
            type="application/ld+json"
            v-text="breadcrumbSchemaJson"
        />
    </Head>

    <DefaultLayout
        :title="title"
        :can-login="canLogin"
        :can-register="canRegister"
    >
        <Navbar />

        <div class="min-h-screen px-1.5">
            <main class="mx-auto flex flex-col lg:flex-row gap-4 tracking-wider">

                <!-- ======================== Left sidebar ======================== -->

                <aside
                    v-if="showLeft"
                    class="shrink-0 mt-12 lg:mt-28 pl-3 transition-all duration-300"
                    :class="leftCollapsed ? 'lg:w-10' : 'lg:w-64'"
                >
                    <LeftSidebarSchool
                        :track-tree="trackTree"
                        @collapsed="leftCollapsed = $event"
                    />
                </aside>

                <!-- ======================== Content ======================== -->

                <section class="w-full lg:mt-28 pb-6 slate-1 min-w-0">
                    <div class="mx-auto max-w-6xl">
                        <article
                            class="selection:bg-red-400 selection:text-white"
                            itemscope
                            itemtype="https://schema.org/LearningResource"
                        >
                            <meta
                                v-if="canonicalUrl"
                                itemprop="url"
                                :content="canonicalUrl"
                            />

                            <meta
                                v-if="contentLocale"
                                itemprop="inLanguage"
                                :content="contentLocale"
                            />

                            <meta
                                itemprop="learningResourceType"
                                content="Lesson"
                            />

                            <meta
                                v-if="lessonData.published_at"
                                itemprop="datePublished"
                                :content="lessonData.published_at"
                            />

                            <meta
                                v-if="schemaDuration"
                                itemprop="timeRequired"
                                :content="schemaDuration"
                            />

                            <meta
                                v-if="lessonData.difficulty !== null
                                && lessonData.difficulty !== undefined"
                                itemprop="educationalLevel"
                                :content="String(lessonData.difficulty)"
                            />

                            <!-- ======================== Breadcrumbs ======================== -->

                            <nav
                                class="text-sm mb-3"
                                aria-label="Breadcrumb"
                            >
                                <ol class="flex flex-wrap items-center font-semibold">
                                    <li>
                                        <Link
                                            :href="route('home')"
                                            class="breadcrumb-link hover:underline"
                                        >
                                            {{ t('home') }}
                                        </Link>
                                    </li>

                                    <li>
                                        <span class="mx-2 breadcrumbs">/</span>
                                    </li>

                                    <li>
                                        <Link
                                            :href="route('public.schoolLessons.index')"
                                            class="breadcrumb-link hover:underline"
                                        >
                                            {{ t('lessons') }}
                                        </Link>
                                    </li>

                                    <!-- Course -->

                                    <template v-if="courseData?.slug">
                                        <li>
                                            <span class="mx-2 breadcrumbs">/</span>
                                        </li>

                                        <li>
                                            <Link
                                                :href="route('public.schoolCourses.show', {
                                                    slug: courseData.slug,
                                                })"
                                                class="breadcrumb-link hover:underline"
                                            >
                                                {{ courseData.translation?.title }}
                                            </Link>
                                        </li>
                                    </template>

                                    <!-- Module -->

                                    <template
                                        v-if="courseData?.slug && moduleData?.slug"
                                    >
                                        <li>
                                            <span class="mx-2 breadcrumbs">/</span>
                                        </li>

                                        <li>
                                            <Link
                                                :href="route('public.schoolModules.show', {
                                                    courseSlug: courseData.slug,
                                                    slug: moduleData.slug,
                                                })"
                                                class="breadcrumb-link hover:underline"
                                            >
                                                {{ moduleData.translation?.title }}
                                            </Link>
                                        </li>
                                    </template>

                                    <!-- Lesson -->

                                    <li>
                                        <span class="mx-2 breadcrumbs">/</span>
                                    </li>

                                    <li
                                        class="breadcrumbs"
                                        aria-current="page"
                                    >
                                        {{ translation.title }}
                                    </li>
                                </ol>
                            </nav>

                            <!-- ======================== Title ======================== -->

                            <div
                                class="flex flex-wrap items-center justify-center
                                       gap-3 title my-3"
                            >
                                <h1
                                    class="text-2xl font-bold"
                                    itemprop="name"
                                >
                                    {{ lessonTitle }}
                                </h1>

                                <div
                                    v-if="lessonData.views > 0"
                                    :title="t('views')"
                                    class="flex items-center justify-center gap-1"
                                    itemprop="interactionStatistic"
                                    itemscope
                                    itemtype="https://schema.org/InteractionCounter"
                                >
                                    <meta
                                        itemprop="interactionType"
                                        content="https://schema.org/ViewAction"
                                    />

                                    <meta
                                        itemprop="userInteractionCount"
                                        :content="lessonData.views"
                                    />

                                    <!-- существующая SVG и число просмотров -->
                                </div>
                            </div>

                            <!-- ======================== Subtitle ======================== -->

                            <div
                                v-if="translation.subtitle"
                                class="mt-1 mb-3 text-sm subtitle text-center"
                            >
                                {{ translation.subtitle }}
                            </div>

                            <!-- ======================== Images ======================== -->

                            <div
                                v-if="hasLessonImages"
                                class="flex items-center justify-center"
                            >
                                <div class="w-full">
                                    <ImageGalleryMain
                                        :images="lessonImages"
                                        :alt="lessonTitle"
                                        itemprop="image"
                                        loading="eager"
                                        fetchpriority="high"
                                        rounded-class="rounded-lg"
                                        shadow-class="shadow-lg shadow-gray-400 dark:shadow-gray-700"
                                        img-class="w-full h-full object-cover"
                                    />
                                </div>
                            </div>

                            <!-- ======================== Lesson info ======================== -->

                            <div
                                class="my-4 flex flex-wrap items-center justify-center
                                       gap-3 text-sm text-slate-600
                                       dark:text-slate-300"
                            >
                                <!-- Course -->

                                <Link
                                    v-if="courseData?.slug"
                                    :href="route('public.schoolCourses.show', {
                                        slug: courseData.slug,
                                    })"
                                    class="rounded-sm border border-gray-400
                                           flex items-center justify-center gap-1
                                           px-3 py-1 hover:text-blue-600
                                           dark:hover:text-blue-400"
                                >
                                    <svg
                                        class="shrink-0 h-3 w-3
                                               text-sky-600/85 dark:text-sky-200/85"
                                        fill="currentColor"
                                        viewBox="0 0 448 512"
                                    >
                                        <path
                                            d="M318.38 208h-39.09c-1.49 27.03-6.54 51.35-14.21 70.41 27.71-13.24 48.02-39.19 53.3-70.41zm0-32c-5.29-31.22-25.59-57.17-53.3-70.41 7.68 19.06 12.72 43.38 14.21 70.41h39.09zM224 97.31c-7.69 7.45-20.77 34.42-23.43 78.69h46.87c-2.67-44.26-15.75-71.24-23.44-78.69zm-41.08 8.28c-27.71 13.24-48.02 39.19-53.3 70.41h39.09c1.49-27.03 6.53-51.35 14.21-70.41zm0 172.82c-7.68-19.06-12.72-43.38-14.21-70.41h-39.09c5.28 31.22 25.59 57.17 53.3 70.41zM247.43 208h-46.87c2.66 44.26 15.74 71.24 23.43 78.69 7.7-7.45 20.78-34.43 23.44-78.69zM448 358.4V25.6c0-16-9.6-25.6-25.6-25.6H96C41.6 0 0 41.6 0 96v320c0 54.4 41.6 96 96 96h326.4c12.8 0 25.6-9.6 25.6-25.6v-16c0-6.4-3.2-12.8-9.6-19.2-3.2-16-3.2-60.8 0-73.6 6.4-3.2 9.6-9.6 9.6-19.2zM224 64c70.69 0 128 57.31 128 128s-57.31 128-128 128S96 262.69 96 192 153.31 64 224 64zm160 384H96c-19.2 0-32-12.8-32-32s16-32 32-32h288v64z"
                                        />
                                    </svg>

                                    {{ t('course') }}:
                                    {{ courseData.translation?.title }}
                                </Link>

                                <!-- Module -->

                                <Link
                                    v-if="courseData?.slug && moduleData?.slug"
                                    :href="route('public.schoolModules.show', {
                                        courseSlug: courseData.slug,
                                        slug: moduleData.slug,
                                    })"
                                    class="rounded-sm border border-gray-400
                                           flex items-center justify-center gap-1
                                           px-3 py-1 hover:text-blue-600
                                           dark:hover:text-blue-400"
                                >
                                    <svg
                                        class="shrink-0 h-3 w-3
                                               text-teal-600/85 dark:text-teal-200/85"
                                        fill="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <rect
                                            x="1"
                                            y="1"
                                            width="10"
                                            height="10"
                                            rx="2"
                                        />

                                        <path
                                            class="fill-current text-teal-400"
                                            d="M23.428,4.618,19.381,.572a1.957,1.957,0,0,0-2.762,0L12.572,4.618a1.959,1.959,0,0,0,0,2.764l4.047,4.047a1.957,1.957,0,0,0,2.762,0l4.047-4.046A1.959,1.959,0,0,0,23.428,4.618Z"
                                        />

                                        <rect
                                            x="13"
                                            y="13"
                                            width="10"
                                            height="10"
                                            rx="2"
                                        />

                                        <rect
                                            x="1"
                                            y="13"
                                            width="10"
                                            height="10"
                                            rx="2"
                                        />
                                    </svg>

                                    {{ t('module') }}:
                                    {{ moduleData.translation?.title }}
                                </Link>

                                <!-- Access -->

                                <span
                                    v-if="lessonData.access_type"
                                    class="rounded-sm border border-gray-400
                                           flex items-center justify-center gap-1
                                           px-3 py-1"
                                >
                                    <svg
                                        class="h-3 w-3 text-blue-600
                                               dark:text-blue-400"
                                        fill="currentColor"
                                        viewBox="0 0 576 512"
                                    >
                                        <path
                                            d="M423.5 0C339.5.3 272 69.5 272 153.5V224H48c-26.5 0-48 21.5-48 48v192c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V272c0-26.5-21.5-48-48-48h-48v-71.1c0-39.6 31.7-72.5 71.3-72.9 40-.4 72.7 32.1 72.7 72v80c0 13.3 10.7 24 24 24h32c13.3 0 24-10.7 24-24v-80C576 68 507.5-.3 423.5 0z"
                                        />
                                    </svg>

                                    {{ t('access') }}:
                                    {{ translateAccessType(lessonData.access_type) }}
                                </span>

                                <!-- Rating -->

                                <span
                                    v-if="lessonData.rating_avg"
                                    class="rounded-sm border border-gray-400
                                           flex items-center justify-center gap-1
                                           px-3 py-1"
                                >
                                    <svg
                                        viewBox="0 0 24 24"
                                        class="h-3 w-3 text-red-400
                                               dark:text-red-300"
                                    >
                                        <path
                                            class="fill-current"
                                            d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"
                                        />
                                    </svg>

                                    {{ t('rating') }}:
                                    {{ Number(lessonData.rating_avg).toFixed(1) }}
                                </span>

                                <!-- Duration -->

                                <span
                                    v-if="lessonData.duration"
                                    class="rounded-sm border border-gray-400
                                           flex items-center justify-center gap-1
                                           px-3 py-1"
                                >
                                    <svg
                                        class="w-3 h-3 text-blue-700
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
                                    {{ lessonData.duration }}
                                </span>
                            </div>

                            <!-- ======================== Description ======================== -->

                            <div
                                v-if="lessonDescription"
                                class="mt-4 text-sm subtitle"
                                itemprop="description"
                                v-html="lessonDescription"
                            />

                            <!-- ======================== Like ======================== -->

                            <div class="my-1 flex items-center justify-center">
                                <div
                                    itemprop="interactionStatistic"
                                    itemscope
                                    itemtype="https://schema.org/InteractionCounter"
                                >
                                    <meta
                                        itemprop="interactionType"
                                        content="https://schema.org/LikeAction"
                                    />

                                    <meta
                                        itemprop="userInteractionCount"
                                        :content="lessonData.likes_count || 0"
                                    />

                                    <LikeButtonEntity
                                        :likes-count="lessonData.likes_count || 0"
                                        :already-liked="lessonData.already_liked || false"
                                        route-name="public.schoolLessons.like"
                                        :route-params="lessonData.id"
                                        :title="t('like')"
                                    />
                                </div>
                            </div>

                            <!-- ======================== Hashtags ======================== -->

                            <div
                                v-if="hashtags.length"
                                class="mt-4 flex flex-wrap items-center
                                       justify-center gap-2"
                            >
                                <Link
                                    v-for="hashtag in hashtags"
                                    :key="hashtag.id"
                                    :href="route('public.schoolHashtags.show', {
                                        slug: hashtag.slug,
                                    })"
                                    class="rounded-sm px-2 py-1 text-xs font-semibold
                                           text-indigo-700 bg-indigo-50
                                           dark:text-indigo-300 dark:bg-indigo-950/50
                                           border border-indigo-400 hover:underline"
                                >
                                    #{{ hashtag.translation?.name }}
                                </Link>
                            </div>

                            <!-- ======================== Content ======================== -->

                            <div
                                v-if="contentData"
                                class="mt-8 rounded-md border border-gray-200
                                       bg-white p-4 shadow-sm
                                       dark:border-gray-700 dark:bg-gray-900"
                            >
                                <h2
                                    class="mb-3 text-center text-lg font-semibold
                                           text-gray-700 dark:text-gray-300"
                                >
                                    {{ t('content') }}
                                </h2>

                                <div
                                    class="flex flex-wrap items-center
                                           justify-center gap-3 text-sm"
                                >
                                    <span
                                        v-if="contentData.type"
                                        class="rounded-sm border border-gray-400
                                               px-3 py-1"
                                    >
                                        {{ t('type') }}:
                                        {{ contentData.type }}
                                    </span>

                                    <Link
                                        v-if="contentData.slug"
                                        :href="`#`"
                                        class="rounded-sm border border-gray-400
                                               px-3 py-1 hover:underline"
                                    >
                                        {{ contentData.title || t('open') }}
                                    </Link>

                                    <span v-else>
                                        {{ contentData.title }}
                                    </span>
                                </div>
                            </div>
                        </article>

                        <!-- ======================== Main videos ======================== -->

                        <SectionVideoList
                            :videos="mainVideosList"
                        />

                        <!-- ======================== Main banners ======================== -->

                        <SectionBanners
                            :banners="mainBannersList"
                        />
                    </div>
                </section>

                <!-- ======================== Right sidebar ======================== -->

                <aside
                    v-if="showRight"
                    class="shrink-0 lg:mt-28 pr-3 transition-all duration-300"
                    :class="rightCollapsed ? 'lg:w-10' : 'lg:w-64'"
                >
                    <RightSidebarSchool
                        @collapsed="rightCollapsed = $event"
                    />
                </aside>
            </main>
        </div>

        <FooterBlog />
        <Progress />
    </DefaultLayout>
</template>
