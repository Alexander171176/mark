<script setup>
import { defineProps } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

import VideoPlayer from '@/Components/Public/Default/Blog/Video/VideoPlayer.vue'
import ArticleStats from '@/Components/Public/Default/Blog/Article/ArticleStats.vue'

const { t } = useI18n()

const props = defineProps({
    videos: {
        type: Array,
        default: () => []
    },
    cols: {
        type: Number,
        default: 3
    }
})

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
</script>

<template>
    <div :class="['grid gap-4', getGridClass()]">
        <div
            v-for="video in videos"
            :key="video.id"
            class="group flex h-full flex-col overflow-hidden rounded-md
                   border border-gray-200 bg-white shadow-sm
                   transition hover:-translate-y-0.5 hover:shadow-md
                   dark:border-gray-700 dark:bg-gray-900"
        >
            <!-- player -->
            <div class="p-4 pb-0">
                <VideoPlayer :video="video" />
            </div>

            <div class="flex flex-1 flex-col p-4">
                <!-- title -->
                <div class="flex items-center justify-center text-center">
                    <Link
                        :href="route('public.videos.show', video.url)"
                        class="inline-flex items-center gap-1"
                    >
                        <span
                            class="text-lg font-semibold text-slate-900/85 text-center
                                   group-hover:opacity-75 dark:text-slate-100/85
                                   dark:group-hover:opacity-75"
                        >
                            {{ video.title }}
                        </span>
                    </Link>
                </div>

                <!-- short -->
                <div
                    v-if="video.short"
                    class="mt-3 line-clamp-3 text-sm text-slate-700 dark:text-slate-300"
                >
                    {{ video.short }}
                </div>

                <!-- owner -->
                <div
                    v-if="video?.owner"
                    class="mt-4 flex items-center justify-center gap-2"
                >
                    <img
                        v-if="video.owner?.profile_photo_url"
                        :src="video.owner.profile_photo_url"
                        :alt="video.owner.name"
                        loading="lazy"
                        class="h-6 w-6 rounded-full object-cover
                               ring-1 ring-gray-200 dark:ring-gray-700"
                    />
                    <div
                        class="min-w-0 text-xs font-semibold
                               text-slate-700/85 dark:text-slate-300/85"
                    >
                        {{ video.owner?.name }}
                    </div>
                </div>

                <!-- stats + like -->
                <div class="mt-3 flex items-center justify-center">
                    <ArticleStats
                        :views="video.views || 0"
                        :likes-count="video.likes_count || 0"
                        :already-liked="video.already_liked || false"
                        route-name="videos.like"
                        :route-params="{ video: video.id }"
                        :show-likes-button="true"
                        compact
                    />
                </div>

                <!-- action -->
                <div class="mt-auto pt-4">
                    <Link
                        :href="route('public.videos.show', video.url)"
                        class="flex w-full items-center justify-center gap-2
                               rounded-sm px-3 py-2 btn-default"
                    >
                        <span class="text-sm font-semibold">
                            {{ t('readMore') }}
                        </span>
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
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
    </div>
</template>
