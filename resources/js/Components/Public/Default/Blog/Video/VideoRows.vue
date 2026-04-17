<script setup>
import { defineProps } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

import VideoPlayer from '@/Components/Public/Default/Blog/Video/VideoPlayer.vue'
import ArticleStats from '@/Components/Public/Default/Blog/Article/ArticleStats.vue'

const { t } = useI18n()

defineProps({
    videos: {
        type: Array,
        default: () => []
    }
})
</script>

<template>
    <div class="space-y-4">
        <div
            v-for="video in videos"
            :key="video.id"
            class="group overflow-hidden rounded-md border border-gray-200
                   bg-white shadow-sm transition hover:shadow-md
                   dark:border-gray-700 dark:bg-gray-900"
        >
            <div class="flex flex-col xl:flex-row gap-3 p-3">
                <!-- player -->
                <div class="w-full xl:w-96 shrink-0">
                    <VideoPlayer :video="video" />
                </div>

                <!-- content -->
                <div class="min-w-0 flex-1 flex flex-col justify-around">
                    <!-- title -->
                    <div class="flex items-start justify-between gap-3">
                        <Link
                            :href="route('public.videos.show', video.url)"
                            class="min-w-0 inline-flex items-center gap-2"
                        >
                            <span
                                class="truncate text-lg font-semibold
                                       text-slate-900/85 dark:text-slate-100/85
                                       group-hover:opacity-75"
                            >
                                {{ video.title }}
                            </span>
                        </Link>
                    </div>

                    <!-- short -->
                    <div
                        v-if="video.short"
                        class="mt-2 line-clamp-2 text-sm text-slate-700 dark:text-slate-300"
                    >
                        {{ video.short }}
                    </div>

                    <!-- owner -->
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
                                   ring-1 ring-gray-200 dark:ring-gray-700"
                        />
                        <div
                            class="text-xs font-semibold
                                   text-slate-700/85 dark:text-slate-300/85 truncate"
                        >
                            {{ video.owner?.name }}
                        </div>
                    </div>

                    <!-- stats + action -->
                    <div class="mt-3 flex items-center justify-between gap-3">
                        <ArticleStats
                            :views="video.views || 0"
                            :likes-count="video.likes_count || 0"
                            :already-liked="video.already_liked || false"
                            route-name="videos.like"
                            :route-params="{ video: video.id }"
                            :show-likes-button="true"
                            compact
                        />

                        <Link
                            :href="route('public.videos.show', video.url)"
                            class="flex items-center justify-center gap-2
                                   rounded-sm px-3 py-1 btn-default"
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
    </div>
</template>
