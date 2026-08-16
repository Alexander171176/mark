<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'

import VideoPlayer from '@/Components/Public/Default/Blog/BlogVideo/VideoPlayer.vue'

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
 * Название из нового Public-контракта.
 */
const videoTitle = (video) => {
    return video?.translation?.title || ''
}

/**
 * URL публичной страницы видео.
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
    <!-- Блок видео -->
    <div v-if="normalizedVideos.length">
        <ul>
            <li
                v-for="video in normalizedVideos"
                :key="video.id"
                class="mb-4 overflow-hidden rounded-sm
                       border border-gray-200 dark:border-gray-700
                       bg-white dark:bg-gray-900
                       shadow-sm hover:shadow-md transition-shadow"
            >
                <VideoPlayer
                    :video="video"
                />

                <div class="px-3 pb-3">
                    <div
                        class="text-center font-semibold
                               text-sm leading-snug"
                    >
                        <Link
                            :href="videoShowRoute(video)"
                            class="hover:underline transition
                                   text-slate-900/85 dark:text-slate-100/85
                                   hover:text-indigo-700
                                   dark:hover:text-indigo-300"
                        >
                            {{ videoTitle(video) }}
                        </Link>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</template>
