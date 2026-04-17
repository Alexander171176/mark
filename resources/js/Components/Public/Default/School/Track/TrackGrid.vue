<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import UniversalImageSlider from '@/Components/Public/Default/Images/UniversalImageSlider.vue'
import EntityStats from '@/Components/Public/Default/Stats/EntityStats.vue'

const { t } = useI18n()

const props = defineProps({
    tracks: { type: Array, default: () => [] },
    cols: { type: Number, default: 2 }
})

const gridClass = computed(() => {
    return props.cols === 3
        ? 'grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3'
        : 'grid grid-cols-1 gap-4 sm:grid-cols-2'
})
</script>

<template>
    <div :class="gridClass">
        <div
            v-for="track in tracks"
            :key="track.id"
            class="group flex h-full flex-col overflow-hidden rounded-md
                   border border-gray-200 bg-white shadow-sm
                   transition hover:-translate-y-0.5 hover:shadow-md
                   dark:border-gray-700 dark:bg-gray-900"
        >
            <Link :href="route('public.tracks.show', track.slug)">
                <UniversalImageSlider
                    :entity="track"
                    height-class="h-48"
                    rounded-class=""
                    wrapper-class=""
                    img-class="w-full h-full object-cover transition
                               duration-300 group-hover:scale-105"
                />
            </Link>

            <div class="flex flex-1 flex-col p-4">
                <div class="flex items-center justify-center">
                    <Link
                        :href="route('public.tracks.show', track.slug)"
                        :title="`${t('courses')}: ${track.courses_count}`"
                        class="inline-flex items-center gap-1"
                    >
                        <span
                            class="text-lg font-semibold text-slate-900/85 text-center
                                   group-hover:opacity-75 dark:text-slate-100/85
                                   dark:group-hover:opacity-75"
                        >
                            {{ track.name }}
                        </span>
                    </Link>
                </div>

                <div
                    v-if="track.short"
                    class="mt-3 line-clamp-3 text-sm text-slate-700 dark:text-slate-300"
                >
                    {{ track.short }}
                </div>

                <!-- Данные -->
                <div
                    class="mt-3 flex flex-wrap items-center justify-center gap-2
                           text-xs font-semibold text-slate-500 dark:text-slate-400">

                    <div
                        v-if="track.children_count"
                        class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm
                               border border-slate-600 dark:border-slate-400">
                        <svg class="shrink-0 h-3 w-3 text-fuchsia-600/85 dark:text-fuchsia-200/85"
                             fill="currentColor"
                             viewBox="0 0 24 24">
                            <path d="M23.58.424A1,1,0,0,0,22.819.13C8.791.862,3.609,13.358,3.559,13.484a1,1,0,0,0,.22,1.08l5.657,5.657a1,1,0,0,0,1.085.218c.125-.051,12.554-5.291,13.348-19.253A1,1,0,0,0,23.58.424Zm-8.166,10.99a2,2,0,1,1,0-2.828A2,2,0,0,1,15.414,11.414Z"></path><path d="M1.113,18.844a2.844,2.844,0,1,1,4.022,4.022C4.024,23.977,0,24,0,24S0,19.954,1.113,18.844Z"></path><path d="M10.357,2.341A8.911,8.911,0,0,0,2.522,4.825a9.084,9.084,0,0,0-1.384,1.8,1,1,0,0,0,.155,1.215l1.989,1.99A26.623,26.623,0,0,1,10.357,2.341Z"></path><path d="M21.659,13.643a8.911,8.911,0,0,1-2.484,7.835,9.084,9.084,0,0,1-1.8,1.384,1,1,0,0,1-1.215-.155l-1.99-1.989A26.623,26.623,0,0,0,21.659,13.643Z"></path>
                        </svg>
                        {{ t('subheadings') }}:
                        {{ track.children_count }}
                    </div>
                    <div
                        v-if="track.courses_count"
                        class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm
                               border border-slate-600 dark:border-slate-400">
                        <svg class="shrink-0 h-3 w-3 text-sky-600/85 dark:text-sky-200/85"
                             fill="currentColor"
                             viewBox="0 0 448 512">
                            <path d="M318.38 208h-39.09c-1.49 27.03-6.54 51.35-14.21 70.41 27.71-13.24 48.02-39.19 53.3-70.41zm0-32c-5.29-31.22-25.59-57.17-53.3-70.41 7.68 19.06 12.72 43.38 14.21 70.41h39.09zM224 97.31c-7.69 7.45-20.77 34.42-23.43 78.69h46.87c-2.67-44.26-15.75-71.24-23.44-78.69zm-41.08 8.28c-27.71 13.24-48.02 39.19-53.3 70.41h39.09c1.49-27.03 6.53-51.35 14.21-70.41zm0 172.82c-7.68-19.06-12.72-43.38-14.21-70.41h-39.09c5.28 31.22 25.59 57.17 53.3 70.41zM247.43 208h-46.87c2.66 44.26 15.74 71.24 23.43 78.69 7.7-7.45 20.78-34.43 23.44-78.69zM448 358.4V25.6c0-16-9.6-25.6-25.6-25.6H96C41.6 0 0 41.6 0 96v320c0 54.4 41.6 96 96 96h326.4c12.8 0 25.6-9.6 25.6-25.6v-16c0-6.4-3.2-12.8-9.6-19.2-3.2-16-3.2-60.8 0-73.6 6.4-3.2 9.6-9.6 9.6-19.2zM224 64c70.69 0 128 57.31 128 128s-57.31 128-128 128S96 262.69 96 192 153.31 64 224 64zm160 384H96c-19.2 0-32-12.8-32-32s16-32 32-32h288v64z"/>
                        </svg>
                        {{ t('courses') }}:
                        {{ track.courses_count }}
                    </div>
                </div>

                <div class="mt-3 flex items-center justify-center">
                    <EntityStats
                        :views="track.views || 0"
                        :likes-count="track.likes_count || 0"
                        :already-liked="track.already_liked || false"
                        route-name="tracks.like"
                        :route-params="{ track: track.id }"
                        :show-likes-button="true"
                        compact
                    />
                </div>

                <div class="mt-auto pt-4">
                    <Link
                        :href="route('public.tracks.show', track.slug)"
                        class="flex w-full items-center justify-center gap-2
                               rounded-sm px-3 py-2 btn-default"
                    >
                        <span class="text-sm font-semibold">{{ t('readMore') }}</span>
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                  d="M7.21 14.77a.75.75 0 0 1 .02-1.06L10.94 10 7.23 6.29a.75.75 0 1 1 1.06-1.06l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06.02Z"
                                  clip-rule="evenodd" />
                        </svg>
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
