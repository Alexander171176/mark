<script setup>
/**
 * Страница конкретной статьи блога.
 *
 * @version PulsarCMS 1.0
 * @author Александр
 */

import { computed, onMounted, ref } from 'vue'
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

const props = defineProps({
    title: { type: String, default: '' },
    canLogin: { type: Boolean, default: false },
    canRegister: { type: Boolean, default: false },

    article: {
        type: Object,
        default: () => ({}),
    },

    breadcrumbRubric: {
        type: Object,
        default: () => null,
    },

    recommendedArticles: {
        type: [Array, Object],
        default: () => [],
    },

    articleVideos: {
        type: [Array, Object],
        default: () => [],
    },

    rubricTree: {
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

    locale: {
        type: String,
        default: 'ru',
    },
})

/* ======================== Helpers ======================== */

const normalizeList = (value) => {
    if (Array.isArray(value)) return value
    if (Array.isArray(value?.data)) return value.data

    return []
}

const settingEnabled = (
    value,
    defaultValue = true
) => {
    if (
        value === undefined
        || value === null
        || value === ''
    ) {
        return defaultValue
    }

    if (typeof value === 'boolean') {
        return value
    }

    return String(value) === 'true'
}

/* ======================== Article ======================== */

const articleData = computed(() =>
    props.article ?? {}
)

const articleTranslation = computed(() =>
    articleData.value?.translation ?? {}
)

const articleTitle = computed(() =>
    articleTranslation.value?.title || ''
)

const articleShort = computed(() =>
    articleTranslation.value?.short || ''
)

const articleDescription = computed(() =>
    articleTranslation.value?.description
    || articleShort.value
    || ''
)

const articlePseudonym = computed(() =>
    articleTranslation.value?.pseudonym || ''
)

const articleLocale = computed(() =>
    articleTranslation.value?.locale
    || props.locale
    || 'ru'
)

const articleAuthor = computed(() =>
    articlePseudonym.value
    || articleData.value?.owner?.name
    || ''
)

/* ======================== Relations ======================== */

const rubricTree = computed(() =>
    Array.isArray(props.rubricTree)
        ? props.rubricTree
        : []
)

const breadcrumbRubricData = computed(() =>
    props.breadcrumbRubric ?? null
)

const breadcrumbRubricTranslation = computed(() =>
    breadcrumbRubricData.value?.translation ?? {}
)

const breadcrumbRubricTitle = computed(() =>
    breadcrumbRubricTranslation.value?.title || ''
)

const hasBreadcrumbRubric = computed(() =>
    Boolean(
        breadcrumbRubricData.value?.id
        && breadcrumbRubricData.value?.url
    )
)

const articleImages = computed(() =>
    normalizeList(
        articleData.value?.images
    )
)

const hasArticleImages = computed(() =>
    articleImages.value.length > 0
)

const activeTags = computed(() =>
    normalizeList(
        articleData.value?.tags
    )
)

const recommendedArticlesList = computed(() =>
    normalizeList(
        props.recommendedArticles
    )
)

const articleVideosList = computed(() =>
    normalizeList(
        props.articleVideos
    )
)

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

/* ======================== Auth ======================== */

const authUser = computed(() =>
    page.props?.auth?.user ?? null
)

/* ======================== SEO ======================== */

const seoTitle = computed(() =>
    articleTranslation.value?.meta_title
    || articleTitle.value
)

const seoKeywords = computed(() =>
    articleTranslation.value?.meta_keywords
    || ''
)

const seoDescription = computed(() =>
    articleTranslation.value?.meta_desc
    || articleShort.value
    || ''
)

const canonicalUrl = computed(() => {
    if (!articleData.value?.url) {
        return ''
    }

    return String(
        route('public.blogArticles.show', {
            url: articleData.value.url,
        })
    )
})

const ogLocale = computed(() =>
    articleLocale.value === 'ru'
        ? 'ru_RU'
        : articleLocale.value
)

const firstImage = computed(() =>
    articleImages.value[0] ?? null
)

const firstImageUrl = computed(() =>
    firstImage.value?.webp_url
    || firstImage.value?.image_url
    || firstImage.value?.thumb_url
    || firstImage.value?.url
    || ''
)

const publishedAt = computed(() =>
    articleData.value?.published_at
    || articleData.value?.created_at
    || ''
)

const modifiedAt = computed(() =>
    articleData.value?.updated_at
    || ''
)

const dcSubject = computed(() =>
    seoKeywords.value
    || articleTitle.value
)

/* ======================== Sidebars ======================== */

const siteSettings = computed(() =>
    page.props?.siteSettings ?? {}
)

const showLeft = computed(() =>
    settingEnabled(
        siteSettings.value?.ViewLeftColumn,
        true
    )
)

const showRight = computed(() =>
    settingEnabled(
        siteSettings.value?.ViewRightColumn,
        true
    )
)

const LEFT_SIDEBAR_KEY =
    'public_left_sidebar_collapsed'

const RIGHT_SIDEBAR_KEY =
    'public_right_sidebar_collapsed'

/**
 * Первый render всегда происходит
 * со свёрнутыми sidebar.
 *
 * После mounted восстанавливаем
 * сохранённое состояние.
 */
const leftCollapsed = ref(true)
const rightCollapsed = ref(true)

const readStoredBoolean = (
    key,
    fallback = true
) => {
    try {
        const value =
            localStorage.getItem(key)

        return value === null
            ? fallback
            : value === 'true'
    } catch {
        return fallback
    }
}

const writeStoredBoolean = (
    key,
    value
) => {
    try {
        localStorage.setItem(
            key,
            String(Boolean(value))
        )
    } catch {
        //
    }
}

onMounted(() => {
    leftCollapsed.value =
        readStoredBoolean(
            LEFT_SIDEBAR_KEY,
            true
        )

    rightCollapsed.value =
        readStoredBoolean(
            RIGHT_SIDEBAR_KEY,
            true
        )
})

const setLeftCollapsed = (value) => {
    leftCollapsed.value =
        Boolean(value)

    writeStoredBoolean(
        LEFT_SIDEBAR_KEY,
        leftCollapsed.value
    )
}

const setRightCollapsed = (value) => {
    rightCollapsed.value =
        Boolean(value)

    writeStoredBoolean(
        RIGHT_SIDEBAR_KEY,
        rightCollapsed.value
    )
}

/**
 * Рекомендованные статьи:
 *
 * оба sidebar открыты → 2;
 * один открыт → 3;
 * оба закрыты → 4.
 */
const articleGridCols = computed(() => {
    const leftExpanded =
        showLeft.value
        && !leftCollapsed.value

    const rightExpanded =
        showRight.value
        && !rightCollapsed.value

    if (leftExpanded && rightExpanded) {
        return 2
    }

    if (leftExpanded || rightExpanded) {
        return 3
    }

    return 4
})
</script>

<template>
    <Head>
        <!-- Basic SEO -->
        <title>{{ seoTitle }}</title>

        <meta
            name="title"
            :content="seoTitle"
        >

        <meta
            v-if="seoDescription"
            name="description"
            :content="seoDescription"
        >

        <meta
            v-if="seoKeywords"
            name="keywords"
            :content="seoKeywords"
        >

        <meta
            v-if="articleAuthor"
            name="author"
            :content="articleAuthor"
        >

        <meta
            name="robots"
            content="index, follow, max-image-preview:large"
        >

        <!-- Canonical -->
        <link
            v-if="canonicalUrl"
            rel="canonical"
            :href="canonicalUrl"
        >

        <!-- Open Graph -->
        <meta
            property="og:type"
            content="article"
        >

        <meta
            property="og:title"
            :content="seoTitle"
        >

        <meta
            v-if="seoDescription"
            property="og:description"
            :content="seoDescription"
        >

        <meta
            v-if="canonicalUrl"
            property="og:url"
            :content="canonicalUrl"
        >

        <meta
            property="og:locale"
            :content="ogLocale"
        >

        <meta
            v-if="firstImageUrl"
            property="og:image"
            :content="firstImageUrl"
        >

        <meta
            v-if="publishedAt"
            property="article:published_time"
            :content="publishedAt"
        >

        <meta
            v-if="modifiedAt"
            property="article:modified_time"
            :content="modifiedAt"
        >

        <meta
            v-if="articleAuthor"
            property="article:author"
            :content="articleAuthor"
        >

        <!-- Twitter / X -->
        <meta
            name="twitter:card"
            :content="
                firstImageUrl
                    ? 'summary_large_image'
                    : 'summary'
            "
        >

        <meta
            name="twitter:title"
            :content="seoTitle"
        >

        <meta
            v-if="seoDescription"
            name="twitter:description"
            :content="seoDescription"
        >

        <meta
            v-if="firstImageUrl"
            name="twitter:image"
            :content="firstImageUrl"
        >

        <!-- Dublin Core -->
        <meta
            name="DC.title"
            :content="seoTitle"
        >

        <meta
            v-if="seoDescription"
            name="DC.description"
            :content="seoDescription"
        >

        <meta
            v-if="dcSubject"
            name="DC.subject"
            :content="dcSubject"
        >

        <meta
            v-if="articleAuthor"
            name="DC.creator"
            :content="articleAuthor"
        >

        <meta
            name="DC.type"
            content="Text"
        >

        <meta
            name="DC.format"
            content="text/html"
        >

        <meta
            name="DC.language"
            :content="articleLocale"
        >

        <meta
            v-if="canonicalUrl"
            name="DC.identifier"
            :content="canonicalUrl"
        >

        <meta
            v-if="publishedAt"
            name="DCTERMS.issued"
            :content="publishedAt"
        >

        <meta
            v-if="modifiedAt"
            name="DCTERMS.modified"
            :content="modifiedAt"
        >
    </Head>

    <DefaultLayout
        :title="title"
        :can-login="canLogin"
        :can-register="canRegister"
    >
        <Navbar />

        <div class="min-h-screen px-3 max-w-full">
            <main class="mx-auto flex flex-col lg:flex-row gap-4 tracking-wider">

                <!-- Left sidebar -->
                <aside
                    v-if="showLeft"
                    class="shrink-0 mt-12 lg:mt-28 transition-all duration-300 overflow-hidden"
                    :class="leftCollapsed ? 'lg:w-10' : 'lg:w-64'"
                >
                    <LeftSidebar
                        :rubric-tree="rubricTree"
                        :collapsed="leftCollapsed"
                        @collapsed="setLeftCollapsed"
                    />
                </aside>

                <!-- Main content -->
                <section class="w-full lg:mt-28 pb-6 slate-1 min-w-0">
                    <div class="mx-auto max-w-6xl">

                        <article
                            itemscope
                            itemtype="https://schema.org/BlogPosting"
                            :itemid="canonicalUrl"
                            class="selection:bg-red-400 selection:text-white"
                        >
                            <!-- BlogPosting metadata -->
                            <meta
                                itemprop="mainEntityOfPage"
                                :content="canonicalUrl"
                            >

                            <meta
                                itemprop="inLanguage"
                                :content="articleLocale"
                            >

                            <meta
                                v-if="publishedAt"
                                itemprop="datePublished"
                                :content="publishedAt"
                            >

                            <meta
                                v-if="modifiedAt"
                                itemprop="dateModified"
                                :content="modifiedAt"
                            >

                            <meta
                                v-if="firstImageUrl"
                                itemprop="image"
                                :content="firstImageUrl"
                            >

                            <meta
                                v-if="seoDescription"
                                itemprop="description"
                                :content="seoDescription"
                            >

                            <meta
                                v-if="seoKeywords"
                                itemprop="keywords"
                                :content="seoKeywords"
                            >

                            <!-- Breadcrumbs -->
                            <nav
                                class="text-sm mb-3"
                                aria-label="Breadcrumb"
                                itemscope
                                itemtype="https://schema.org/BreadcrumbList"
                            >
                                <ol class="flex flex-wrap items-center font-semibold">

                                    <!-- Home -->
                                    <li
                                        itemprop="itemListElement"
                                        itemscope
                                        itemtype="https://schema.org/ListItem"
                                        class="flex items-center"
                                    >
                                        <Link
                                            itemprop="item"
                                            :href="route('home')"
                                            class="breadcrumb-link hover:underline"
                                        >
                                            <span itemprop="name">
                                                {{ t('home') }}
                                            </span>
                                        </Link>

                                        <meta
                                            itemprop="position"
                                            content="1"
                                        >
                                    </li>

                                    <!-- Rubrics -->
                                    <li
                                        itemprop="itemListElement"
                                        itemscope
                                        itemtype="https://schema.org/ListItem"
                                        class="flex items-center"
                                    >
                                        <span class="mx-2 breadcrumbs">
                                            /
                                        </span>

                                        <Link
                                            itemprop="item"
                                            :href="route('public.blogRubrics.index')"
                                            class="breadcrumb-link hover:underline"
                                        >
                                            <span itemprop="name">
                                                {{ t('rubrics') }}
                                            </span>
                                        </Link>

                                        <meta
                                            itemprop="position"
                                            content="2"
                                        >
                                    </li>

                                    <!-- Current rubric -->
                                    <li
                                        v-if="hasBreadcrumbRubric"
                                        itemprop="itemListElement"
                                        itemscope
                                        itemtype="https://schema.org/ListItem"
                                        class="flex items-center"
                                    >
                                        <span class="mx-2 breadcrumbs">
                                            /
                                        </span>

                                        <Link
                                            itemprop="item"
                                            :href="route('public.blogRubrics.show', {
                                                url: breadcrumbRubricData.url
                                            })"
                                            class="breadcrumb-link hover:underline"
                                        >
                                            <span itemprop="name">
                                                {{ breadcrumbRubricTitle }}
                                            </span>
                                        </Link>

                                        <meta
                                            itemprop="position"
                                            content="3"
                                        >
                                    </li>

                                    <!-- Article -->
                                    <li
                                        itemprop="itemListElement"
                                        itemscope
                                        itemtype="https://schema.org/ListItem"
                                        class="flex items-center"
                                        aria-current="page"
                                    >
                                        <span class="mx-2 breadcrumbs">
                                            /
                                        </span>

                                        <span
                                            itemprop="name"
                                            class="breadcrumbs"
                                        >
                                            {{ articleTitle }}
                                        </span>

                                        <meta
                                            v-if="canonicalUrl"
                                            itemprop="item"
                                            :content="canonicalUrl"
                                        >

                                        <meta
                                            itemprop="position"
                                            :content="
                                                hasBreadcrumbRubric
                                                    ? '4'
                                                    : '3'
                                            "
                                        >
                                    </li>
                                </ol>
                            </nav>

                            <!-- Images -->
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

                            <!-- Header -->
                            <div
                                class="my-3 flex flex-wrap items-center
                                       justify-center gap-3 title"
                            >
                                <h1
                                    itemprop="headline"
                                    class="text-2xl font-bold"
                                >
                                    {{ articleTitle }}
                                </h1>

                                <!-- Views -->
                                <div
                                    :title="t('views')"
                                    class="flex items-center justify-center gap-1"
                                    itemprop="interactionStatistic"
                                    itemscope
                                    itemtype="https://schema.org/InteractionCounter"
                                >
                                    <svg
                                        class="h-4 w-4 text-slate-600/85 dark:text-slate-200/85"
                                        viewBox="0 0 576 512"
                                        fill="currentColor"
                                    >
                                        <path
                                            d="M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z"
                                        />
                                    </svg>

                                    <meta
                                        itemprop="interactionType"
                                        content="https://schema.org/ViewAction"
                                    >

                                    <meta
                                        itemprop="userInteractionCount"
                                        :content="articleData.views || 0"
                                    >

                                    <span class="text-sm text-gray-500">
                                        {{ articleData.views || 0 }}
                                    </span>
                                </div>
                            </div>

                            <!-- Article body -->
                            <div
                                v-if="articleDescription"
                                itemprop="articleBody"
                                class="my-3 text-sm subtitle"
                                v-html="articleDescription"
                            />

                            <!-- Tags + like -->
                            <div
                                class="flex flex-wrap items-center
                                       justify-center gap-3"
                            >
                                <div
                                    v-if="activeTags.length"
                                    class="flex flex-wrap items-center
                                           justify-center gap-1
                                           font-semibold italic"
                                >
                                    <template
                                        v-for="(tag, index) in activeTags"
                                        :key="tag.id"
                                    >
                                        <Link
                                            :href="route('public.blogTags.show', {
                                                url: tag.slug
                                            })"
                                            class="text-sm text-blue-500
                                                   dark:text-violet-300
                                                   hover:text-rose-400
                                                   dark:hover:text-rose-300"
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

                                <LikeButtonEntity
                                    :likes-count="articleData.likes_count || 0"
                                    :already-liked="articleData.already_liked || false"
                                    route-name="public.blogArticles.like"
                                    :route-params="{ id: articleData.id }"
                                    :title="t('like')"
                                    icon-class="w-4 h-4"
                                />
                            </div>

                            <!-- Author -->
                            <div
                                v-if="articleData.owner"
                                itemprop="author"
                                itemscope
                                itemtype="https://schema.org/Person"
                                class="mt-4 flex items-center justify-center gap-2"
                            >
                                <meta
                                    itemprop="name"
                                    :content="articleAuthor"
                                >

                                <img
                                    v-if="articleData.owner?.profile_photo_url"
                                    :src="articleData.owner.profile_photo_url"
                                    :alt="articleAuthor"
                                    loading="lazy"
                                    class="h-8 w-8 rounded-full object-cover
                                           ring-1 ring-gray-200 dark:ring-gray-700"
                                >

                                <div
                                    class="min-w-0 text-sm font-semibold
                                           text-slate-700/85 dark:text-slate-300/85"
                                >
                                    {{ articleAuthor }}
                                </div>
                            </div>

                            <!-- Recommended articles -->
                            <div
                                v-if="recommendedArticlesList.length"
                                class="mt-8"
                            >
                                <h2
                                    class="mb-4 text-center text-lg font-semibold
                                           text-gray-700 dark:text-gray-300"
                                >
                                    {{ t('relatedArticles') }}
                                </h2>

                                <RubricArticleGrid
                                    :articles="recommendedArticlesList"
                                    :cols="articleGridCols"
                                />
                            </div>

                            <!-- Article videos -->
                            <div
                                v-if="articleVideosList.length"
                                class="mt-8"
                            >
                                <RecommendedVideos
                                    :videos="articleVideosList"
                                />
                            </div>

                            <!-- Comments -->
                            <CommentThread
                                commentable-type="App\Models\Admin\Blog\BlogArticle\BlogArticle"
                                :commentable-id="articleData.id"
                                :auth-user="authUser"
                            />
                        </article>

                        <!-- Bottom content -->
                        <SectionVideoList
                            :videos="mainVideosList"
                        />

                        <SectionBanners
                            :banners="mainBannersList"
                        />
                    </div>
                </section>

                <!-- Right sidebar -->
                <aside
                    v-if="showRight"
                    class="shrink-0 lg:mt-28 transition-all duration-300 overflow-hidden"
                    :class="rightCollapsed ? 'lg:w-10' : 'lg:w-64'"
                >
                    <RightSidebar
                        :collapsed="rightCollapsed"
                        @collapsed="setRightCollapsed"
                    />
                </aside>
            </main>
        </div>

        <FooterBlog />
        <Progress />
    </DefaultLayout>
</template>
