<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'

import VideoPlayer from '@/Components/Public/Default/Blog/BlogVideo/VideoPlayer.vue'

import {
    unwrap,
    unwrapList,
} from '@/composables/useUnwrap.js'

const props = defineProps({
    videos: {
        type: [Array, Object],
        default: () => [],
    },
})

/**
 * Нормализованный список видео.
 */
const list = computed(() =>
    unwrapList(props.videos)
)

/**
 * Название видео
 * из Public BlogVideoSharedResource.
 */
const getVideoTitle = (video) => {
    const item = unwrap(video)

    return item?.translation?.title || ''
}

/**
 * URL публичной страницы видео.
 */
const getVideoUrl = (video) => {
    const item = unwrap(video)

    return route(
        'public.blogVideos.show',
        {
            url: item?.url,
        }
    )
}
</script>

<template>
    <!-- Блок видео -->
    <div v-if="list.length">
        <ul>
            <li
                v-for="video in list"
                :key="unwrap(video).id"
                class="mb-4 overflow-hidden rounded-sm
                       border border-gray-200 dark:border-gray-700
                       bg-white dark:bg-gray-900
                       shadow-sm hover:shadow-md transition-shadow"
            >
                <VideoPlayer
                    :video="unwrap(video)"
                />

                <div class="px-3 pb-3">
                    <div
                        class="text-center font-semibold
                               text-sm leading-snug"
                    >
                        <Link
                            :href="getVideoUrl(video)"
                            class="hover:underline transition
                                   text-slate-900/85 dark:text-slate-100/85
                                   hover:text-indigo-700
                                   dark:hover:text-indigo-300"
                        >
                            {{ getVideoTitle(video) }}
                        </Link>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</template>
