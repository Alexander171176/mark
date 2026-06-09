<script setup>
/**
 * Страница конкретной статьи блога.
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
import SectionVideoList from '@/Components/Public/Default/Blog/BlogVideo/SectionVideoList.vue'
import SectionBanners from '@/Components/Public/Default/Blog/BlogBanner/SectionBanners.vue'
import RubricArticleGrid from '@/Components/Public/Default/Blog/BlogRubric/RubricArticleGrid.vue'
import RecommendedVideos from '@/Components/Public/Default/Blog/BlogVideo/RecommendedVideos.vue'

const { t } = useI18n()
const page = usePage()

/** Props страницы */
const props = defineProps({
    title: { type: String, default: '' },
    canLogin: { type: Boolean, default: false },
    canRegister: { type: Boolean, default: false },

    article: { type: Object, default: () => ({}) },
    breadcrumbRubric: { type: Object, default: () => null },

    recommendedArticles: { type: [Array, Object], default: () => [] },
    articleVideos: { type: [Array, Object], default: () => [] },

    rubricTree: { type: Array, default: () => [] },

    tags: { type: Array, default: () => [] },

    mainVideos: { type: [Array, Object], default: () => [] },
    mainBanners: { type: [Array, Object], default: () => [] },

    locale: { type: String, default: 'ru' },
})

/** Нормализация массивов и ResourceCollection */
const normalizeList = (value) => {
    if (Array.isArray(value)) return value
    if (Array.isArray(value?.data)) return value.data

    return []
}

/** Текущая статья */
const articleData = computed(() => props.article || {})

/** Текущий перевод статьи */
const articleTranslation = computed(() => articleData.value.translation || {})

/** Заголовок статьи */
const articleTitle = computed(() => {
    return articleTranslation.value.title || articleData.value.title || ''
})

/** Краткое описание статьи */
const articleShort = computed(() => {
    return articleTranslation.value.short || articleData.value.short || ''
})

/** Полное описание статьи */
const articleDescription = computed(() => {
    return articleTranslation.value.description || articleData.value.description || ''
})

/** SEO title */
const articleMetaTitle = computed(() => {
    return articleTranslation.value.meta_title || articleData.value.meta_title || articleTitle.value
})

/** SEO keywords */
const articleMetaKeywords = computed(() => {
    return articleTranslation.value.meta_keywords || articleData.value.meta_keywords || ''
})

/** SEO description */
const articleMetaDesc = computed(() => {
    return articleTranslation.value.meta_desc || articleData.value.meta_desc || articleShort.value || ''
})

/** Псевдоним автора из перевода */
const articlePseudonym = computed(() => {
    return articleTranslation.value.pseudonym || ''
})

/** Рубрика для хлебных крошек */
const breadcrumbRubricData = computed(() => props.breadcrumbRubric || null)

/** Перевод рубрики хлебных крошек */
const breadcrumbRubricTranslation = computed(() => {
    return breadcrumbRubricData.value?.translation || {}
})

/** Заголовок рубрики хлебных крошек */
const breadcrumbRubricTitle = computed(() => {
    return breadcrumbRubricTranslation.value.title ||
        breadcrumbRubricData.value?.title ||
        ''
})

/** Есть ли рубрика для хлебных крошек */
const hasBreadcrumbRubric = computed(() => !!breadcrumbRubricData.value?.id)

/** Дерево рубрик для левого аккордеона */
const rubricTree = computed(() => Array.isArray(props.rubricTree) ? props.rubricTree : [])

/** Видео статьи */
const articleVideosList = computed(() => normalizeList(props.articleVideos))

/** Рекомендованные статьи */
const recommendedArticlesList = computed(() => normalizeList(props.recommendedArticles))

/** Нижний блок видео */
const mainVideosList = computed(() => normalizeList(props.mainVideos))

/** Нижний блок баннеров */
const mainBannersList = computed(() => normalizeList(props.mainBanners))

/** Текущий пользователь */
const authUser = computed(() => page.props.auth?.user ?? null)

/** Активные теги статьи */
const activeTags = computed(() => {
    return normalizeList(articleData.value.tags).filter(tag => tag?.activity)
})

/** Изображения статьи */
const articleImages = computed(() => {
    return normalizeList(articleData.value.images)
})

/** Есть ли изображения статьи */
const hasArticleImages = computed(() => articleImages.value.length > 0)

/** Первое изображение для SEO */
const firstImage = computed(() => {
    return articleImages.value.length ? articleImages.value[0] : null
})

/** URL первого изображения */
const firstImageUrl = computed(() => {
    return firstImage.value?.webp_url ||
        firstImage.value?.url ||
        firstImage.value?.image_url ||
        firstImage.value?.thumb_url ||
        ''
})

/** Глобальные настройки сайта */
const { siteSettings } = page.props

/** Показ левой колонки */
const showLeft = computed(() =>
    !siteSettings?.ViewLeftColumn || siteSettings.ViewLeftColumn === 'true'
)

/** Показ правой колонки */
const showRight = computed(() =>
    !siteSettings?.ViewRightColumn || siteSettings.ViewRightColumn === 'true'
)

/** Состояние сайдбаров */
const leftCollapsed = ref(false)
const rightCollapsed = ref(false)
</script>

<template>
    <Head>
        <title>{{ articleMetaTitle }}</title>

        <meta name="title" :content="articleMetaTitle" />
        <meta name="description" :content="articleMetaDesc" />
        <meta name="keywords" :content="articleMetaKeywords" />
        <meta name="author" :content="articlePseudonym || articleData.owner?.name || ''" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <meta property="og:title" :content="articleMetaTitle" />
        <meta property="og:description" :content="articleMetaDesc" />
        <meta property="og:type" content="article" />
        <meta property="og:url" :content="`/blog/articles/${articleData.url || ''}`" />
        <meta property="og:image" :content="firstImageUrl" />
        <meta property="og:locale" :content="locale" />

        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="articleMetaTitle" />
        <meta name="twitter:description" :content="articleMetaDesc" />
        <meta name="twitter:image" :content="firstImageUrl" />

        <meta itemprop="name" :content="articleMetaTitle" />
        <meta itemprop="description" :content="articleMetaDesc" />
        <meta itemprop="image" :content="firstImageUrl" />

        <meta name="DC.Title" :content="articleMetaTitle" />
        <meta name="DC.Description" :content="articleMetaDesc" />
        <meta name="DC.Subject" :content="articleMetaKeywords" />
        <meta name="DC.Creator" :content="articlePseudonym || articleData.owner?.name || ''" />
        <meta name="DC.Type" content="Text" />
        <meta name="DC.Format" content="text/html" />
        <meta name="DC.Language" :content="locale" />
        <meta name="DC.Identifier" :content="`/blog/articles/${articleData.url || ''}`" />

        <meta name="DCTERMS.Issued" :content="articleData.published_at || articleData.created_at || ''" />
        <meta name="DCTERMS.Modified" :content="articleData.updated_at || ''" />
    </Head>

    <DefaultLayout
        :title="title"
        :can-login="canLogin"
        :can-register="canRegister"
    >
        <Navbar />

        <div class="min-h-screen px-1.5">
            <main class="mx-auto flex flex-col lg:flex-row gap-4 tracking-wider">
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

                <section class="w-full lg:mt-16 pb-6 slate-1 min-w-0">
                    <div class="mx-auto max-w-6xl">
                        <article
                            itemscope
                            itemtype="https://schema.org/BlogPosting"
                            class="selection:bg-red-400 selection:text-white"
                        >
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
                                            :href="route('public.blogRubrics.index')"
                                            class="breadcrumb-link hover:underline"
                                        >
                                            {{ t('rubrics') }}
                                        </Link>
                                    </li>

                                    <template v-if="hasBreadcrumbRubric">
                                        <li><span class="mx-2 breadcrumbs">/</span></li>

                                        <li>
                                            <Link
                                                :href="route('public.blogRubrics.show', { url: breadcrumbRubricData.url })"
                                                class="breadcrumb-link hover:underline"
                                            >
                                                {{ breadcrumbRubricTitle }}
                                            </Link>
                                        </li>
                                    </template>

                                    <li><span class="mx-2 breadcrumbs">/</span></li>

                                    <li class="breadcrumbs">
                                        {{ articleTitle }}
                                    </li>
                                </ol>
                            </nav>

                            <div
                                v-if="hasArticleImages"
                                class="flex items-center justify-center"
                            >
                                <div class="w-full">
                                    <ImageGalleryMain
                                        :images="articleImages"
                                        :alt="articleTitle"
                                        rounded-class="rounded-lg"
                                        shadow-class="shadow-lg shadow-gray-400 dark:shadow-gray-700"
                                        img-class="w-full h-full object-cover"
                                    />
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center justify-center gap-3 title my-3">
                                <h1
                                    itemprop="headline"
                                    class="text-2xl font-bold"
                                >
                                    {{ articleTitle }}
                                </h1>

                                <div
                                    :title="t('views')"
                                    class="flex items-center justify-center gap-1"
                                    itemprop="interactionStatistic"
                                    itemscope
                                    itemtype="http://schema.org/InteractionCounter"
                                >
                                    <svg
                                        class="h-4 w-4 text-slate-600/85 dark:text-slate-200/85"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 576 512"
                                        fill="currentColor"
                                    >
                                        <path d="M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z" />
                                    </svg>

                                    <meta itemprop="interactionType" content="http://schema.org/ViewAction" />
                                    <meta itemprop="userInteractionCount" :content="articleData.views || 0" />

                                    <span class="text-center text-sm text-gray-500">
                                        {{ articleData.views || 0 }} ·
                                    </span>
                                </div>
                            </div>

                            <div
                                v-if="articleDescription"
                                class="my-3 text-sm subtitle text-center"
                                v-html="articleDescription"
                                itemprop="articleBody"
                            />

                            <div class="flex flex-wrap items-center justify-center gap-3">
                                <div
                                    v-if="activeTags.length"
                                    class="flex flex-wrap justify-center items-center gap-1 font-semibold italic"
                                >
                                    <template
                                        v-for="(tag, index) in activeTags"
                                        :key="tag.id"
                                    >
                                        <Link
                                            :href="route('public.blogTags.show', {
                                                url: tag.slug
                                            })"
                                            itemprop="keywords"
                                            class="text-sm text-blue-500 dark:text-violet-300
                                                    hover:text-rose-400 hover:dark:text-rose-300"
                                        >
                                            {{ tag.translation?.name || tag.name }}
                                        </Link>

                                        <span
                                            v-if="index < activeTags.length - 1"
                                            class="text-slate-500 dark:text-slate-400"
                                        >
                                            ,
                                        </span>
                                    </template>
                                </div>

                                <div class="flex justify-center items-center">
                                    <LikeButtonEntity
                                        :likes-count="articleData.likes_count || 0"
                                        :already-liked="articleData.already_liked || false"
                                        route-name="public.blogArticles.like"
                                        :route-params="{ id: articleData.id }"
                                        :title="t('like')"
                                        icon-class="w-4 h-4"
                                    />
                                </div>
                            </div>

                            <div
                                v-if="articleData.owner"
                                class="mt-4 flex items-center justify-center gap-2"
                            >
                                <img
                                    v-if="articleData.owner?.profile_photo_url"
                                    :src="articleData.owner.profile_photo_url"
                                    :alt="articleData.owner.name"
                                    loading="lazy"
                                    class="h-8 w-8 rounded-full object-cover
                                           ring-1 ring-gray-200 dark:ring-gray-700"
                                />

                                <div class="min-w-0 text-sm font-semibold text-slate-700/85 dark:text-slate-300/85">
                                    {{ articlePseudonym || articleData.owner?.name }}
                                </div>
                            </div>

                            <div
                                v-if="recommendedArticlesList.length"
                                class="mt-8"
                            >
                                <h2 class="mb-4 text-center text-lg font-semibold text-gray-700 dark:text-gray-300">
                                    {{ t('relatedArticles') }}
                                </h2>

                                <RubricArticleGrid
                                    :articles="recommendedArticlesList"
                                    :cols="2"
                                />
                            </div>

                            <div v-if="articleVideosList.length">
                                <RecommendedVideos :videos="articleVideosList" />
                            </div>

                            <CommentThread
                                commentable-type="App\Models\Admin\Blog\BlogArticle\BlogArticle"
                                :commentable-id="articleData.id"
                                :auth-user="authUser"
                            />
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
                    <RightSidebar @collapsed="rightCollapsed = $event" />
                </aside>
            </main>
        </div>

        <FooterBlog />
        <Progress />
    </DefaultLayout>
</template>
