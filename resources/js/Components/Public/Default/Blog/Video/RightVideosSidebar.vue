<script setup>
import { Link } from '@inertiajs/vue3'
import VideoPlayer from '@/Components/Public/Default/Blog/Video/VideoPlayer.vue'

defineProps({
    videos: {
        type: Array,
        default: () => [],
    },
})

const formatDate = (dateString) => {
    if (!dateString) return ''
    const d = new Date(dateString)
    if (Number.isNaN(d.getTime())) return ''
    return d.toLocaleDateString()
}
</script>

<template>
    <!-- Блок видео -->
    <div v-if="videos.length">
        <ul>
            <li
                v-for="video in videos"
                :key="video.id"
                class="mb-4 overflow-hidden rounded-sm
                       border border-gray-200 dark:border-gray-700
                       bg-white dark:bg-gray-900
                       shadow-sm hover:shadow-md transition-shadow"
            >

                <VideoPlayer :video="video" />

                <div class="px-3 pb-3">
                    <div class="text-center font-semibold text-sm leading-snug">
                        <Link
                            target="_blank"
                            :href="`/videos/${video.url}`"
                            class="hover:underline transition
                                   text-slate-900/85 dark:text-slate-100/85
                                   hover:text-indigo-700 dark:hover:text-indigo-300"
                        >
                            {{ video.title }}
                        </Link>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</template>
