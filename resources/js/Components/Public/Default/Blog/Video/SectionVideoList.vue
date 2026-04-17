<!-- SectionVideoList.vue -->
<script setup>
import { Link } from '@inertiajs/vue3'
import VideoPlayer from './VideoPlayer.vue'

defineProps({
    videos: {
        type: Array,
        default: () => []
    }
})
</script>

<template>
    <section v-if="videos.length" class="mt-8 space-y-6">
        <div class="flex flex-col gap-4">
            <div v-for="video in videos" :key="video.id"
                 class="mb-3 pb-3 border-b border-slate-500 dark:border-slate-100">
                <VideoPlayer :video="video" />
                <Link :href="`/videos/${video.url}`"
                      class="flex justify-center font-semibold text-md mb-1
                             hover:underline transition
                             text-indigo-700 dark:text-indigo-300
                             hover:text-indigo-500 dark:hover:text-indigo-500">
                    {{ video.title }}
                </Link>
                <!-- Краткое описание, если есть -->
                <p v-if="video.short"
                   class="flex items-center font-semibold
                          tracking-wide text-xs text-slate-700/85 dark:text-slate-300/85">
                    {{ video.short }}
                </p>
            </div>
        </div>
    </section>
</template>
