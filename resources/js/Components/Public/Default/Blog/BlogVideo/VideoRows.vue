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
})

/* ======================== Data ======================== */

const normalizedVideos = computed(() =>
    Array.isArray(props.videos)
        ? props.videos
        : []
)

/* ======================== Video helpers ======================== */

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

const videoThumbnail = (video) =>
    imageUrl(
        videoImages(video)[0]
    )

const videoPublishedAt = (video) =>
    video?.published_at
    || video?.created_at
    || ''

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
        class="space-y-4"
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
            class="group overflow-hidden rounded-md
                   border border-gray-200 bg-white shadow-sm
                   transition hover:shadow-md
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

                <!-- ======================== Row ======================== -->

                <div
                    class="flex flex-col xl:flex-row
                           gap-3 p-3"
                >
                    <!-- Player -->
                    <div
                        class="w-full xl:w-96 shrink-0"
                    >
                        <VideoPlayer
                            :video="video"
                        />
                    </div>

                    <!-- Content -->
                    <div
                        class="min-w-0 flex-1 flex
                               flex-col justify-around"
                    >
                        <div
                            class="flex items-start
                                   justify-between gap-3"
                        >
                            <Link
                                :href="videoShowRoute(video)"
                                class="min-w-0 inline-flex
                                       items-center gap-2"
                            >
                                <span
                                    class="truncate text-lg font-semibold
                                           text-slate-900/85
                                           dark:text-slate-100/85
                                           group-hover:opacity-75"
                                >
                                    {{ videoTitle(video) }}
                                </span>
                            </Link>
                        </div>

                        <div
                            v-if="videoShort(video)"
                            class="mt-2 line-clamp-2 text-sm
                                   text-slate-700
                                   dark:text-slate-300"
                        >
                            {{ videoShort(video) }}
                        </div>

                        <!-- Author -->
                        <div
                            v-if="video?.owner"
                            class="mt-3 flex items-center gap-2"
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
                                class="text-xs font-semibold
                                       text-slate-700/85
                                       dark:text-slate-300/85
                                       truncate"
                            >
                                {{ video.owner?.name }}
                            </div>
                        </div>

                        <!-- Stats / link -->
                        <div
                            class="mt-3 flex items-center
                                   justify-between gap-3"
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

                            <Link
                                :href="videoShowRoute(video)"
                                class="flex items-center
                                       justify-center gap-2
                                       rounded-sm px-3 py-1
                                       btn-default"
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
                </div>
            </article>
        </div>
    </div>
</template>
