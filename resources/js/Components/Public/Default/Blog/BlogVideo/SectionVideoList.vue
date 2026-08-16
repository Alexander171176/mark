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

/**
 * Нормализованный список видео.
 */
const normalizedVideos = computed(() => {
    return Array.isArray(props.videos)
        ? props.videos
        : []
})

/**
 * Название видео из нового Public-контракта.
 */
const videoTitle = (video) => {
    return video?.translation?.title || ''
}

/**
 * Краткое описание видео.
 */
const videoShort = (video) => {
    return video?.translation?.short || ''
}

/**
 * Публичная страница конкретного видео.
 */
const videoShowRoute = (video) => {
    return route(
        'public.blogVideos.show',
        {
            url: video?.url,
        }
    )
}
</script>

<template>
    <section
        v-if="normalizedVideos.length"
        class="mt-8 space-y-6"
    >
        <div class="flex flex-col gap-4">
            <div
                v-for="video in normalizedVideos"
                :key="video.id"
                class="mb-3 pb-3 border-b
                       border-slate-500 dark:border-slate-100"
            >
                <VideoPlayer
                    :video="video"
                />

                <Link
                    :href="videoShowRoute(video)"
                    class="flex justify-center font-semibold text-md mb-1
                           hover:underline transition
                           text-indigo-700 dark:text-indigo-300
                           hover:text-indigo-500 dark:hover:text-indigo-500"
                >
                    {{ videoTitle(video) }}
                </Link>

                <!-- Краткое описание -->
                <p
                    v-if="videoShort(video)"
                    class="flex items-center font-semibold
                           tracking-wide text-xs
                           text-slate-700/85 dark:text-slate-300/85"
                >
                    {{ videoShort(video) }}
                </p>
            </div>
        </div>
    </section>
</template>
