<script setup>
/**
 * Страница конкретной статьи (Блог)
 * - SEO
 * - хлебные крошки
 * - главное изображение / галерея
 * - теги
 * - лайк
 * - связанные статьи
 * - видео статьи
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
import LikeButtonEntity from '@/Components/Public/Like/LikeButtonEntity.vue'
import ImageGalleryMain from '@/Components/Public/Default/Media/ImageGalleryMain.vue'
import CommentThread from '@/Components/Public/Default/Blog/Comment/CommentThread.vue'
import SectionVideoList from '@/Components/Public/Default/Blog/Video/SectionVideoList.vue'
import SectionBanners from '@/Components/Public/Default/Blog/Banner/SectionBanners.vue'
import RubricArticleGrid from '@/Components/Public/Default/Blog/Rubric/RubricArticleGrid.vue'
import RecommendedVideos from '@/Components/Public/Default/Blog/Video/RecommendedVideos.vue'

const { t } = useI18n()
const page = usePage()

const props = defineProps({
    title: String,
    canLogin: Boolean,
    canRegister: Boolean,

    article: { type: Object, default: () => ({}) },
    breadcrumbRubric: { type: Object, default: () => null },

    recommendedArticles: { type: [Array, Object], default: () => [] },
    articleVideos: { type: [Array, Object], default: () => [] },

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

/**
 * Helpers
 */
const normalizeList = (value) => {
    if (Array.isArray(value)) return value
    if (Array.isArray(value?.data)) return value.data
    return []
}

/**
 * Base data
 */
const articleData = computed(() => props.article ?? {})
const breadcrumbRubricData = computed(() => props.breadcrumbRubric ?? null)
const hasBreadcrumbRubric = computed(() => !!breadcrumbRubricData.value?.id)

const rubricTree = computed(() => Array.isArray(props.rubricTree) ? props.rubricTree : [])

const articleVideosList = computed(() => normalizeList(props.articleVideos))

const mainVideosList = computed(() => normalizeList(props.mainVideos))
const mainBannersList = computed(() => normalizeList(props.mainBanners))

const authUser = computed(() => page.props.auth?.user ?? null)

/**
 * Tags
 */
const activeTags = computed(() => {
    return (articleData.value?.tags ?? []).filter(tag => tag?.activity)
})

/**
 * Images
 */
const firstImage = computed(() => {
    const imgs = articleData.value?.images ?? []
    return imgs.length ? imgs[0] : null
})

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

const articleImages = computed(() => {
    return Array.isArray(props.article?.images) ? props.article.images : []
})

const hasArticleImages = computed(() => {
    return articleImages.value.length > 0
})
</script>

<template>
    <Head>
        <title>{{ articleData.title || '' }}</title>

        <meta name="title" :content="articleData.title || ''" />
        <meta name="description" :content="articleData.meta_desc || ''" />
        <meta name="keywords" :content="articleData.meta_keywords || ''" />
        <meta name="author" :content="articleData.author || articleData.owner?.name || ''" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <!-- Open Graph -->
        <meta property="og:title" :content="articleData.title || ''" />
        <meta property="og:description" :content="articleData.meta_desc || ''" />
        <meta property="og:type" content="article" />
        <meta property="og:url" :content="`/articles/${articleData.url || ''}`" />
        <meta
            property="og:image"
            :content="firstImage ? (firstImage.webp_url || firstImage.url || firstImage.image_url) : ''"
        />
        <meta property="og:locale" :content="articleData.locale || 'ru_RU'" />

        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="articleData.title || ''" />
        <meta name="twitter:description" :content="articleData.meta_desc || ''" />
        <meta
            name="twitter:image"
            :content="firstImage ? (firstImage.webp_url || firstImage.url || firstImage.image_url) : ''"
        />

        <!-- Schema.org -->
        <meta itemprop="name" :content="articleData.title || ''" />
        <meta itemprop="description" :content="articleData.meta_desc || ''" />
        <meta
            itemprop="image"
            :content="firstImage ? (firstImage.webp_url || firstImage.url || firstImage.image_url) : ''"
        />

        <!-- Dublin Core -->
        <meta name="DC.Title" :content="articleData.title || ''" />
        <meta name="DC.Description" :content="articleData.meta_desc || ''" />
        <meta name="DC.Subject" :content="articleData.meta_keywords || ''" />
        <meta name="DC.Creator" :content="articleData.author || articleData.owner?.name || ''" />
        <meta name="DC.Type" content="Text" />
        <meta name="DC.Format" content="text/html" />
        <meta name="DC.Language" :content="articleData.locale || 'ru'" />
        <meta name="DC.Identifier" :content="`/articles/${articleData.url || ''}`" />

        <meta name="DCTERMS.Issued" :content="articleData.published_at || articleData.created_at || ''" />
        <meta name="DCTERMS.Modified" :content="articleData.updated_at || ''" />
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
                            itemtype="https://schema.org/BlogPosting"
                            class="selection:bg-red-400 selection:text-white"
                        >
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
                                            :href="route('public.rubrics.index')"
                                            class="breadcrumb-link hover:underline"
                                        >
                                            {{ t('rubrics') }}
                                        </Link>
                                    </li>
                                    <template v-if="hasBreadcrumbRubric">
                                        <li><span class="mx-2 breadcrumbs">/</span></li>
                                        <li>
                                            <Link
                                                :href="route('public.rubrics.show', breadcrumbRubricData.url)"
                                                class="breadcrumb-link hover:underline"
                                            >
                                                {{ breadcrumbRubricData.title }}
                                            </Link>
                                        </li>
                                    </template>
                                    <li><span class="mx-2 breadcrumbs">/</span></li>

                                    <li class="breadcrumbs">
                                        {{ articleData.title }}
                                    </li>
                                </ol>
                            </nav>

                            <!-- Image -->
                            <div
                                v-if="hasArticleImages"
                                class="flex items-center justify-center"
                            >
                                <div class="w-full">
                                    <ImageGalleryMain
                                        :images="articleData.images"
                                        :alt="articleData.alt"
                                        rounded-class="rounded-lg"
                                        shadow-class="shadow-lg shadow-gray-400 dark:shadow-gray-700"
                                        img-class="w-full h-full object-cover"
                                    />
                                </div>
                            </div>

                            <!-- Title / stats -->
                            <div class="flex flex-wrap items-center justify-center gap-3
                                        title my-3">
                                <h1
                                    itemprop="headline"
                                    class="text-2xl font-bold"
                                >
                                    {{ articleData.title }}
                                </h1>
                                <div :title="t('views')"
                                     class="flex items-center justify-center gap-1"
                                     itemprop="interactionStatistic"
                                     itemscope
                                     itemtype="http://schema.org/InteractionCounter"
                                >
                                    <svg class="h-4 w-4 text-slate-600/85 dark:text-slate-200/85"
                                         xmlns="http://www.w3.org/2000/svg"
                                         viewBox="0 0 576 512"
                                         fill="currentColor">
                                        <path
                                            d="M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z"
                                        />
                                    </svg>
                                    <meta itemprop="interactionType"
                                          content="http://schema.org/ViewAction" />
                                    <meta itemprop="userInteractionCount"
                                          :content="articleData.views || 0" />
                                    <span class="text-center text-sm text-gray-500">
                                            {{ articleData.views || 0 }} ·
                                        </span>
                                </div>
                            </div>

                            <!-- Description -->
                            <div
                                v-if="articleData.description"
                                class="my-3 text-sm subtitle text-center"
                                v-html="articleData.description"
                                itemprop="articleBody"
                            />

                            <div class="flex items-center justify-center gap-3">

                                <!-- Tags -->
                                <div
                                    v-if="activeTags.length"
                                    class="flex flex-wrap justify-center items-center gap-1
                                           font-semibold italic"
                                >
                                    <template v-for="(tag, index) in activeTags" :key="tag.id">
                                        <Link
                                            :href="`/tags/${tag.url || tag.slug}`"
                                            itemprop="keywords"
                                            class="text-sm text-blue-500 dark:text-violet-300
                                               hover:text-rose-400 hover:dark:text-rose-300"
                                        >
                                            {{ tag.title || tag.name }}
                                        </Link>
                                        <span
                                            v-if="index < activeTags.length - 1"
                                            class="text-slate-500 dark:text-slate-400"
                                        >
                                        ,
                                    </span>
                                    </template>
                                </div>

                                <!-- Like -->
                                <div class="flex justify-center items-center">
                                    <LikeButtonEntity
                                        :likes-count="articleData.likes_count || 0"
                                        :already-liked="articleData.already_liked || false"
                                        route-name="articles.like"
                                        :route-params="{ article: articleData.id }"
                                        :title="t('like')"
                                        icon-class="w-4 h-4"
                                    />
                                </div>
                            </div>

                            <!-- Owner -->
                            <div v-if="article?.owner"
                                 class="mt-4 flex items-center justify-center gap-2">
                                <img
                                    v-if="article.owner?.profile_photo_url"
                                    :src="article.owner.profile_photo_url"
                                    :alt="article.owner.name"
                                    loading="lazy"
                                    class="h-8 w-8 rounded-full object-cover
                                           ring-1 ring-gray-200 dark:ring-gray-700"
                                />
                                <div class="min-w-0 text-sm font-semibold
                                text-slate-700/85 dark:text-slate-300/85">
                                    {{ article.owner?.name }}
                                </div>
                            </div>

                            <!-- Related articles -->
                            <div v-if="recommendedArticles?.length" class="mt-8">
                                <h2 class="mb-4 text-center text-lg font-semibold
                                           text-gray-700 dark:text-gray-300">
                                    {{ t('relatedArticles') }}
                                </h2>

                                <RubricArticleGrid
                                    :articles="recommendedArticles"
                                    :cols="2"
                                />
                            </div>

                            <!-- Article videos -->
                            <div v-if="articleVideosList.length">
                                <RecommendedVideos :videos="articleVideosList" />
                            </div>

                            <!-- Comments -->
                            <CommentThread
                                commentable-type="App\Models\Admin\Blog\Article\Article"
                                :commentable-id="articleData.id"
                                :auth-user="authUser"
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
                    <RightSidebar @collapsed="rightCollapsed = $event" />
                </aside>
            </main>
        </div>

        <FooterBlog />
        <Progress />
    </DefaultLayout>
</template>
