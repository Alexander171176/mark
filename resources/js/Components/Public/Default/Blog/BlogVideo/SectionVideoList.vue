<!-- SectionVideoList.vue -->

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'

import VideoPlayer from './VideoPlayer.vue'

const props = defineProps({
    videos: {
        type: Array,
        default: () => [],
    },
})

/* ======================== Data ======================== */

/**
 * Нормализованный список видео.
 */
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

/**
 * Краткое описание видео.
 */
const videoShort = (video) =>
    video?.translation?.short
    || ''

/**
 * Автор / псевдоним.
 */
const videoAuthorName = (video) =>
    video?.translation?.pseudonym
    || video?.owner?.name
    || ''

/**
 * Публичная страница
 * конкретного видео.
 */
const videoShowRoute = (video) =>
    route(
        'public.blogVideos.show',
        {
            url:
                video?.url
                || '',
        }
    )

/**
 * Изображения видео.
 */
const videoImages = (video) =>
    Array.isArray(video?.images)
        ? video.images
        : []

/**
 * URL изображения.
 */
const imageUrl = (image) =>
    image?.webp_url
    || image?.image_url
    || image?.thumb_url
    || image?.url
    || ''

/**
 * Первое изображение видео.
 */
const videoThumbnail = (video) =>
    imageUrl(
        videoImages(video)[0]
    )

/**
 * Эффективная дата публикации.
 */
const videoPublishedAt = (video) =>
    video?.published_at
    || video?.created_at
    || ''

/**
 * Duration Schema.org
 * в ISO 8601.
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
    <section
        v-if="normalizedVideos.length"
        itemprop="hasPart"
        itemscope
        itemtype="https://schema.org/ItemList"
        class="mt-8 space-y-6"
    >
        <meta
            itemprop="numberOfItems"
            :content="normalizedVideos.length"
        >

        <div class="flex flex-col gap-4">
            <div
                v-for="(video, index) in normalizedVideos"
                :key="video.id"
                itemprop="itemListElement"
                itemscope
                itemtype="https://schema.org/ListItem"
                class="mb-3 pb-3 border-b
                       border-slate-500
                       dark:border-slate-100"
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

                    <!-- ======================== Player ======================== -->

                    <VideoPlayer
                        :video="video"
                    />

                    <!-- ======================== Title ======================== -->

                    <Link
                        :href="videoShowRoute(video)"
                        class="flex justify-center
                               font-semibold text-md mb-1
                               hover:underline transition
                               text-indigo-700
                               dark:text-indigo-300
                               hover:text-indigo-500
                               dark:hover:text-indigo-500"
                    >
                        {{ videoTitle(video) }}
                    </Link>

                    <!-- ======================== Short ======================== -->

                    <p
                        v-if="videoShort(video)"
                        class="flex items-center font-semibold
                               tracking-wide text-xs
                               text-slate-700/85
                               dark:text-slate-300/85"
                    >
                        {{ videoShort(video) }}
                    </p>
                </article>
            </div>
        </div>
    </section>
</template>
