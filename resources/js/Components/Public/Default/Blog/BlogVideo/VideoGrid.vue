<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

import VideoPlayer from '@/Components/Public/Default/Blog/BlogVideo/VideoPlayer.vue'
import EntityStats from '@/Components/Public/Default/Blog/BlogArticle/EntityStats.vue'

const { t } = useI18n()

const props = defineProps({
    videos: {
        type: Array,
        default: () => [],
    },

    cols: {
        type: Number,
        default: 3,
    },
})

/* ======================== Grid ======================== */

const getGridClass = () => {
    switch (props.cols) {
        case 1:
            return 'grid-cols-1'

        case 2:
            return 'grid-cols-1 sm:grid-cols-2'

        case 3:
            return 'grid-cols-1 sm:grid-cols-2 xl:grid-cols-3'

        case 4:
            return 'grid-cols-1 sm:grid-cols-2 xl:grid-cols-4'

        default:
            return 'grid-cols-1 sm:grid-cols-2 xl:grid-cols-3'
    }
}

/* ======================== Data ======================== */

const normalizedVideos = computed(() =>
    Array.isArray(props.videos)
        ? props.videos
        : []
)

/* ======================== Video helpers ======================== */

/**
 * Resource уже выполнил
 * current locale -> fallback.
 */
const videoTitle = (video) =>
    video?.translation?.title
    || ''

const videoShort = (video) =>
    video?.translation?.short
    || ''

const videoAuthorName = (video) =>
    video?.translation?.pseudonym
    || video?.owner?.name
    || ''

const videoShowRoute = (video) =>
    route(
        'public.blogVideos.show',
        {
            url:
                video?.url
                || '',
        }
    )

const videoImages = (video) =>
    Array.isArray(video?.images)
        ? video.images
        : []

const imageUrl = (image) =>
    image?.webp_url
    || image?.image_url
    || image?.thumb_url
    || image?.url
    || ''

const videoThumbnail = (video) => {
    const image =
        videoImages(video)[0]

    return imageUrl(
        image
    )
}

const videoPublishedAt = (video) =>
    video?.published_at
    || video?.created_at
    || ''

/**
 * Schema.org duration
 * использует ISO 8601.
 *
 * Пример:
 * 90 -> PT1M30S
 */
const schemaDuration = (seconds) => {
    const value =
        Number(seconds)

    if (
        !Number.isFinite(value)
        || value <= 0
    ) {
        return ''
    }

    const total =
        Math.floor(value)

    const hours =
        Math.floor(
            total / 3600
        )

    const minutes =
        Math.floor(
            (total % 3600) / 60
        )

    const secs =
        total % 60

    return [
        'PT',

        hours
            ? `${hours}H`
            : '',

        minutes
            ? `${minutes}M`
            : '',

        secs
        || (
            !hours
            && !minutes
        )
            ? `${secs}S`
            : '',
    ].join('')
}
</script>

<template>
    <div
        :class="[
            'grid gap-4',
            getGridClass(),
        ]"
        itemscope
        itemtype="https://schema.org/ItemList"
    >
        <meta
            itemprop="numberOfItems"
            :content="normalizedVideos.length"
        >

        <div
            v-for="(video, index) in normalizedVideos"
            :key="video.id"
            itemprop="itemListElement"
            itemscope
            itemtype="https://schema.org/ListItem"
            class="group flex h-full flex-col overflow-hidden rounded-md
                   border border-gray-200 bg-white shadow-sm
                   transition hover:-translate-y-0.5 hover:shadow-md
                   dark:border-gray-700 dark:bg-gray-900"
        >
            <meta
                itemprop="position"
                :content="index + 1"
            >

            <article
                itemprop="item"
                itemscope
                itemtype="https://schema.org/VideoObject"
                class="flex h-full flex-col"
            >
                <!-- ======================== VideoObject ======================== -->

                <meta
                    itemprop="url"
                    :content="videoShowRoute(video)"
                >

                <meta
                    itemprop="name"
                    :content="videoTitle(video)"
                >

                <meta
                    v-if="videoShort(video)"
                    itemprop="description"
                    :content="videoShort(video)"
                >

                <meta
                    v-if="videoThumbnail(video)"
                    itemprop="thumbnailUrl"
                    :content="videoThumbnail(video)"
                >

                <meta
                    v-if="videoPublishedAt(video)"
                    itemprop="uploadDate"
                    :content="videoPublishedAt(video)"
                >

                <meta
                    v-if="schemaDuration(video?.duration)"
                    itemprop="duration"
                    :content="schemaDuration(video?.duration)"
                >

                <div
                    v-if="videoAuthorName(video)"
                    itemprop="author"
                    itemscope
                    itemtype="https://schema.org/Person"
                >
                    <meta
                        itemprop="name"
                        :content="videoAuthorName(video)"
                    >
                </div>

                <!-- Views -->
                <div
                    itemprop="interactionStatistic"
                    itemscope
                    itemtype="https://schema.org/InteractionCounter"
                >
                    <meta
                        itemprop="interactionType"
                        content="https://schema.org/WatchAction"
                    >

                    <meta
                        itemprop="userInteractionCount"
                        :content="Number(video?.views || 0)"
                    >
                </div>

                <!-- Likes -->
                <div
                    v-if="video?.likes_count !== undefined"
                    itemprop="interactionStatistic"
                    itemscope
                    itemtype="https://schema.org/InteractionCounter"
                >
                    <meta
                        itemprop="interactionType"
                        content="https://schema.org/LikeAction"
                    >

                    <meta
                        itemprop="userInteractionCount"
                        :content="Number(video?.likes_count || 0)"
                    >
                </div>

                <!-- Comments -->
                <div
                    v-if="video?.comments_count !== undefined"
                    itemprop="interactionStatistic"
                    itemscope
                    itemtype="https://schema.org/InteractionCounter"
                >
                    <meta
                        itemprop="interactionType"
                        content="https://schema.org/CommentAction"
                    >

                    <meta
                        itemprop="userInteractionCount"
                        :content="Number(video?.comments_count || 0)"
                    >
                </div>

                <!-- ======================== Player ======================== -->

                <div class="p-4 pb-0">
                    <VideoPlayer
                        :video="video"
                    />
                </div>

                <!-- ======================== Content ======================== -->

                <div class="flex flex-1 flex-col p-4">
                    <div
                        class="flex items-center
                               justify-center text-center"
                    >
                        <Link
                            :href="videoShowRoute(video)"
                            class="inline-flex items-center gap-1"
                        >
                            <span
                                class="text-lg font-semibold
                                       text-slate-900/85 text-center
                                       group-hover:opacity-75
                                       dark:text-slate-100/85
                                       dark:group-hover:opacity-75"
                            >
                                {{ videoTitle(video) }}
                            </span>
                        </Link>
                    </div>

                    <div
                        v-if="videoShort(video)"
                        class="mt-3 line-clamp-3 text-sm
                               text-slate-700 dark:text-slate-300"
                    >
                        {{ videoShort(video) }}
                    </div>

                    <!-- Author -->
                    <div
                        v-if="video?.owner"
                        class="mt-4 flex items-center
                               justify-center gap-2"
                    >
                        <img
                            v-if="video.owner?.profile_photo_url"
                            :src="video.owner.profile_photo_url"
                            :alt="video.owner.name"
                            loading="lazy"
                            class="h-6 w-6 rounded-full object-cover
                                   ring-1 ring-gray-200
                                   dark:ring-gray-700"
                        >

                        <div
                            class="min-w-0 text-xs font-semibold
                                   text-slate-700/85
                                   dark:text-slate-300/85"
                        >
                            {{ video.owner?.name }}
                        </div>
                    </div>

                    <!-- Stats -->
                    <div
                        class="mt-3 flex items-center justify-center"
                    >
                        <EntityStats
                            :views="video.views || 0"
                            :likes-count="video.likes_count || 0"
                            :already-liked="video.already_liked || false"
                            route-name="public.blogVideos.like"
                            :route-params="{ id: video.id }"
                            :show-likes-button="true"
                            compact
                        />
                    </div>

                    <!-- Read more -->
                    <div class="mt-auto pt-4">
                        <Link
                            :href="videoShowRoute(video)"
                            class="flex w-full items-center
                                   justify-center gap-2 rounded-sm
                                   px-3 py-2 btn-default"
                        >
                            <span class="text-sm font-semibold">
                                {{ t('readMore') }}
                            </span>

                            <svg
                                class="h-4 w-4"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M7.21 14.77a.75.75 0 0 1 .02-1.06L10.94 10 7.23 6.29a.75.75 0 1 1 1.06-1.06l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06.02Z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </Link>
                    </div>
                </div>
            </article>
        </div>
    </div>
</template>
