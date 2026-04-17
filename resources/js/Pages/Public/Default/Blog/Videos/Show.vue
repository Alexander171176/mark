<script setup>
/**
 * Страница конкретного видео (Блог)
 * - SEO
 * - хлебные крошки
 * - видео-плеер / embed / local
 * - лайк
 * - связанные видео
 * - комментарии
 * - показ главных видео и баннеров внизу страницы
 * - показ/скрытие левой и правой колонок
 * - показ rubricTree в LeftSidebar
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
import LeftSidebar from '@/Components/Public/Default/Partials/LeftSidebar.vue'
import RightSidebar from '@/Components/Public/Default/Partials/RightSidebar.vue'
import VideoGrid from '@/Components/Public/Default/Blog/Video/VideoGrid.vue'
import CommentThread from '@/Components/Public/Default/Blog/Comment/CommentThread.vue'
import SectionVideoList from '@/Components/Public/Default/Blog/Video/SectionVideoList.vue'
import SectionBanners from '@/Components/Public/Default/Blog/Banner/SectionBanners.vue'
import LikeButtonEntity from '@/Components/Public/Like/LikeButtonEntity.vue'

const { t } = useI18n()
const page = usePage()

const props = defineProps({
    title: String,
    canLogin: Boolean,
    canRegister: Boolean,

    video: { type: Object, default: () => ({}) },
    recommendedVideos: { type: [Array, Object], default: () => [] },

    rubricTree: { type: Array, default: () => [] },

    tags: { type: Array, default: () => [] },
    leftArticles: { type: [Array, Object], default: () => [] },
    rightArticles: { type: [Array, Object], default: () => [] },

    leftBanners: { type: [Array, Object], default: () => [] },
    mainBanners: { type: [Array, Object], default: () => [] },
    rightBanners: { type: [Array, Object], default: () => [] },

    leftVideos: { type: [Array, Object], default: () => [] },
    mainVideos: { type: [Array, Object], default: () => [] },
    rightVideos: { type: [Array, Object], default: () => [] },

    locale: String
})

const normalizeList = (value) => {
    if (Array.isArray(value)) return value
    if (Array.isArray(value?.data)) return value.data
    return []
}

/**
 * Base data
 */
const videoData = computed(() => props.video ?? {})
const rubricTree = computed(() => Array.isArray(props.rubricTree) ? props.rubricTree : [])

const recommendedVideosList = computed(() => normalizeList(props.recommendedVideos))
const mainVideosList = computed(() => normalizeList(props.mainVideos))
const mainBannersList = computed(() => normalizeList(props.mainBanners))

const authUser = computed(() => page.props.auth?.user ?? null)

/**
 * Cover image
 */
const firstImage = computed(() => {
    const images = videoData.value?.images ?? []

    if (images.length) {
        return images[0]
    }

    if (
        videoData.value?.cover_webp_url ||
        videoData.value?.cover_image_url ||
        videoData.value?.cover_thumb_url
    ) {
        return {
            webp_url: videoData.value.cover_webp_url,
            image_url: videoData.value.cover_image_url,
            thumb_url: videoData.value.cover_thumb_url,
        }
    }

    return null
})

/**
 * Video source helpers
 */
const hasEmbedUrl = computed(() => !!videoData.value?.embed_url)
const hasVideoUrl = computed(() => !!videoData.value?.video_url)
const hasEmbedCode = computed(() => !!videoData.value?.embed_code)

const displaySourceType = computed(() => {
    switch (videoData.value?.source_type) {
        case 'youtube':
            return 'YouTube'
        case 'vimeo':
            return 'Vimeo'
        case 'local':
            return t('video')
        case 'code':
            return t('embedCode')
        default:
            return t('video')
    }
})

/**
 * Date
 */
const formatDate = (dateString) => {
    if (!dateString) return ''

    const date = new Date(dateString)

    if (Number.isNaN(date.getTime())) {
        return dateString
    }

    const day = String(date.getDate()).padStart(2, '0')
    const month = String(date.getMonth() + 1).padStart(2, '0')
    const year = date.getFullYear()

    return `${day}.${month}.${year}`
}

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
</script>

<template>
    <Head>
        <title>{{ videoData.title || '' }}</title>

        <meta name="title" :content="videoData.meta_title || videoData.title || ''" />
        <meta name="description" :content="videoData.meta_desc || videoData.short || ''" />
        <meta name="keywords" :content="videoData.meta_keywords || ''" />
        <meta name="author" :content="videoData.pseudonym || videoData.owner?.name || ''" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <!-- Open Graph -->
        <meta property="og:title" :content="videoData.meta_title || videoData.title || ''" />
        <meta property="og:description" :content="videoData.meta_desc || videoData.short || ''" />
        <meta property="og:type" content="video.other" />
        <meta property="og:url" :content="`/videos/${videoData.url || ''}`" />
        <meta
            property="og:image"
            :content="firstImage ? (firstImage.webp_url || firstImage.url || firstImage.image_url || firstImage.thumb_url) : ''"
        />
        <meta property="og:locale" :content="videoData.locale || 'ru_RU'" />

        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="videoData.meta_title || videoData.title || ''" />
        <meta name="twitter:description" :content="videoData.meta_desc || videoData.short || ''" />
        <meta
            name="twitter:image"
            :content="firstImage ? (firstImage.webp_url || firstImage.url || firstImage.image_url || firstImage.thumb_url) : ''"
        />

        <!-- Dublin Core -->
        <meta name="DC.Title" :content="videoData.meta_title || videoData.title || ''" />
        <meta name="DC.Description" :content="videoData.meta_desc || videoData.short || ''" />
        <meta name="DC.Subject" :content="videoData.meta_keywords || ''" />
        <meta name="DC.Creator" :content="videoData.pseudonym || videoData.owner?.name || ''" />
        <meta name="DC.Type" content="MovingImage" />
        <meta name="DC.Format" content="text/html" />
        <meta name="DC.Language" :content="videoData.locale || 'ru'" />
        <meta name="DC.Identifier" :content="`/videos/${videoData.url || ''}`" />

        <meta name="DCTERMS.Issued" :content="videoData.published_at || videoData.created_at || ''" />
        <meta name="DCTERMS.Modified" :content="videoData.updated_at || ''" />
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
                    <LeftSidebar
                        :rubric-tree="rubricTree"
                        @collapsed="leftCollapsed = $event"
                    />
                </aside>

                <!-- CENTER -->
                <section class="w-full lg:mt-16 pb-6 slate-1 min-w-0">
                    <div class="mx-auto max-w-6xl">
                        <article
                            itemscope
                            itemtype="https://schema.org/VideoObject"
                            class="selection:bg-red-400 selection:text-white"
                        >
                            <!-- Breadcrumbs -->
                            <nav class="text-sm" aria-label="Breadcrumb">
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
                                            :href="route('public.videos.index')"
                                            class="breadcrumb-link hover:underline"
                                        >
                                            {{ t('videos') }}
                                        </Link>
                                    </li>

                                    <li><span class="mx-2 breadcrumbs">/</span></li>

                                    <li class="breadcrumbs">
                                        {{ videoData.title }}
                                    </li>
                                </ol>
                            </nav>

                            <!-- Video player -->
                            <div class="overflow-hidden rounded-sm mt-4
                                        shadow-md shadow-gray-400 dark:shadow-gray-800">
                                <!-- embed url -->
                                <div
                                    v-if="hasEmbedUrl"
                                    class="relative w-full overflow-hidden bg-black"
                                    style="padding-top: 56.25%;"
                                >
                                    <iframe
                                        :src="videoData.embed_url"
                                        :title="videoData.title"
                                        class="absolute left-0 top-0 h-full w-full"
                                        frameborder="0"
                                        allow="autoplay; fullscreen; picture-in-picture"
                                        allowfullscreen
                                    />
                                </div>

                                <!-- local video -->
                                <div
                                    v-else-if="hasVideoUrl"
                                    class="bg-black"
                                >
                                    <video
                                        class="h-auto w-full"
                                        controls
                                        preload="metadata"
                                        :poster="firstImage ? (firstImage.webp_url || firstImage.url || firstImage.image_url || firstImage.thumb_url) : ''"
                                    >
                                        <source :src="videoData.video_url" />
                                        {{ t('video') }}
                                    </video>
                                </div>

                                <!-- raw embed code -->
                                <div
                                    v-else-if="hasEmbedCode"
                                    class="w-full [&_iframe]:w-full [&_iframe]:min-h-[400px]"
                                    v-html="videoData.embed_code"
                                />

                                <!-- no source -->
                                <div
                                    v-else
                                    class="flex min-h-[320px] items-center justify-center bg-gray-100 text-sm font-semibold text-gray-500 dark:bg-gray-800 dark:text-gray-400"
                                >
                                    {{ t('noData') }}
                                </div>
                            </div>

                            <!-- Title / stats -->
                            <div class="flex flex-wrap items-center justify-between
                                        gap-3 title my-3">

                                <div
                                    v-if="videoData.likes_count > 0"
                                    :title="t('likes')"
                                    class="flex items-center justify-center gap-2"
                                >
                                    <svg
                                        class="h-4 w-4 text-slate-600/85 dark:text-slate-200/85"
                                        fill="currentColor"
                                        viewBox="0 0 24 24">
                                        <path d="M3,9H1a1,1,0,0,0-1,1V22a1,1,0,0,0,1,1H4V10A1,1,0,0,0,3,9Z"></path><path d="M21.882,8.133A2.986,2.986,0,0,0,21,8H15V5c0-3.824-2.589-4.942-3.958-5a1.017,1.017,0,0,0-.734.277A1,1,0,0,0,10,1V5.638l-4,4.8V23H18.23A2.985,2.985,0,0,0,21.1,20.882l2.769-9A3,3,0,0,0,21.882,8.133Z"></path>
                                    </svg>
                                    <span class="text-center text-sm text-gray-500">
                                        {{ videoData.likes_count }} ·
                                    </span>
                                </div>

                                <h1
                                    itemprop="name"
                                    class="text-2xl font-bold"
                                >
                                    {{ videoData.title }}
                                </h1>

                                <div
                                    :title="t('views')"
                                    class="flex items-center justify-center gap-2"
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
                                        {{ videoData.views || 0 }} ·
                                    </span>
                                </div>
                            </div>

                            <!-- Description -->
                            <div
                                v-if="videoData.description"
                                class="my-4 text-sm subtitle text-center"
                                v-html="videoData.description"
                                itemprop="description"
                            />

                            <!-- Like -->
                            <div class="flex items-center justify-center gap-3">
                                <LikeButtonEntity
                                    :likes-count="videoData.likes_count || 0"
                                    :already-liked="videoData.already_liked || false"
                                    route-name="videos.like"
                                    :route-params="{ video: videoData.id }"
                                    :title="t('like')"
                                    icon-class="w-4 h-4"
                                />
                            </div>

                            <!-- Owner -->
                            <div
                                v-if="videoData?.owner"
                                class="mt-4 flex items-center justify-center gap-2"
                            >
                                <img
                                    v-if="videoData.owner?.profile_photo_url"
                                    :src="videoData.owner.profile_photo_url"
                                    :alt="videoData.owner.name"
                                    loading="lazy"
                                    class="h-8 w-8 rounded-full object-cover ring-1 ring-gray-200 dark:ring-gray-700"
                                />
                                <div class="min-w-0 text-sm font-semibold text-slate-700/85 dark:text-slate-300/85">
                                    {{ videoData.owner?.name }}
                                </div>
                            </div>

                            <!-- Comments -->
                            <CommentThread
                                commentable-type="App\Models\Admin\Blog\Video\Video"
                                :commentable-id="videoData.id"
                                :auth-user="authUser"
                            />

                            <!-- Related videos -->
                            <div v-if="recommendedVideosList.length" class="mt-6">
                                <h2 class="mb-4 tracking-wide text-center font-semibold text-lg text-gray-700 dark:text-gray-300">
                                    {{ t('relatedVideos') }}
                                </h2>

                                <VideoGrid
                                    :videos="recommendedVideosList"
                                    :cols="2"
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
                    <RightSidebar @collapsed="rightCollapsed = $event" />
                </aside>
            </main>
        </div>

        <FooterBlog />
        <Progress />
    </DefaultLayout>
</template>
