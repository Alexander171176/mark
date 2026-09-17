<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import UniversalImageSlider from '@/Components/Public/Default/Images/UniversalImageSlider.vue'
import EntityStats from '@/Components/Public/Default/Stats/EntityStats.vue'

const { t } = useI18n()

const props = defineProps({
    modules: { type: Array, default: () => [] },
    cols: { type: Number, default: 2 },
})

const gridClass = computed(() => {
    switch (props.cols) {
        case 4:
            return 'grid-cols-1 sm:grid-cols-2 xl:grid-cols-4'
        case 3:
            return 'grid-cols-1 sm:grid-cols-2 xl:grid-cols-3'
        case 2:
        default:
            return 'grid-cols-1 sm:grid-cols-2'
    }
})

/**
 * Public Resource уже разрешил перевод:
 * current locale → fallback locale.
 */
const moduleTitle = (module) => module?.translation?.title || ''
const moduleSubtitle = (module) => module?.translation?.subtitle || ''
const moduleShort = (module) => module?.translation?.short || ''
const moduleDescription = (module) => moduleShort(module) || moduleSubtitle(module)
const courseTitle = (module) => module?.course?.translation?.title || ''

const moduleLink = (module) => module?.course?.slug && module?.slug
    ? route('public.schoolModules.show', {
        courseSlug: module.course.slug,
        slug: module.slug,
    })
    : '#'

const courseLink = (module) => module?.course?.slug
    ? route('public.schoolCourses.show', { slug: module.course.slug })
    : '#'

const moduleDuration = (module) => {
    const duration = Number(module?.duration || 0)
    return duration > 0 ? `PT${duration}M` : null
}
</script>

<template>
    <div class="grid gap-4" :class="gridClass">
        <article
            v-for="module in modules"
            :key="module.id"
            class="group flex h-full flex-col overflow-hidden rounded-md border border-gray-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-gray-700 dark:bg-gray-900"
            itemscope
            itemtype="https://schema.org/LearningResource"
        >
            <link itemprop="url" :href="moduleLink(module)">

            <Link :href="moduleLink(module)" :aria-label="moduleTitle(module)">
                <UniversalImageSlider
                    :entity="module"
                    :alt="moduleTitle(module)"
                    itemprop="image"
                    height-class="h-48"
                    rounded-class=""
                    wrapper-class=""
                    img-class="w-full h-full object-cover transition duration-300 group-hover:scale-105"
                />
            </Link>

            <div class="flex flex-1 flex-col p-4">
                <div class="flex items-center justify-center gap-2">
                    <Link :href="moduleLink(module)" class="inline-flex items-center gap-2">
                        <span
                            class="text-base font-semibold text-slate-900/85 group-hover:opacity-75 dark:text-slate-100/85 dark:group-hover:opacity-75"
                            itemprop="name"
                        >
                            {{ moduleTitle(module) }}
                        </span>
                    </Link>
                </div>

                <div
                    v-if="moduleSubtitle(module)"
                    class="mt-1 text-center text-xs font-semibold text-slate-500 dark:text-slate-400"
                >
                    {{ moduleSubtitle(module) }}
                </div>

                <div
                    v-if="moduleShort(module)"
                    class="mt-3 line-clamp-3 text-sm text-slate-700 dark:text-slate-300"
                    itemprop="description"
                >
                    {{ moduleShort(module) }}
                </div>

                <meta
                    v-else-if="moduleDescription(module)"
                    itemprop="description"
                    :content="moduleDescription(module)"
                >

                <meta
                    v-if="module.translation?.locale"
                    itemprop="inLanguage"
                    :content="module.translation.locale"
                >

                <meta
                    v-if="moduleDuration(module)"
                    itemprop="timeRequired"
                    :content="moduleDuration(module)"
                >

                <!-- Данные модуля -->
                <div class="mt-3 flex flex-wrap items-center justify-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                    <div
                        v-if="module.lessons_count"
                        class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"
                        :title="t('lessons')"
                    >
                        <svg class="shrink-0 h-4 w-4 text-indigo-600/85 dark:text-indigo-200/85" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        {{ module.lessons_count }}

                        <span
                            itemscope
                            itemprop="interactionStatistic"
                            itemtype="https://schema.org/InteractionCounter"
                            class="hidden"
                        >
                            <link itemprop="interactionType" href="https://schema.org/ConsumeAction">
                            <meta itemprop="userInteractionCount" :content="module.lessons_count">
                        </span>
                    </div>

                    <div
                        v-if="module.duration"
                        class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"
                        :title="t('duration')"
                    >
                        <svg class="w-3 h-3" viewBox="0 0 24 24" aria-hidden="true">
                            <path
                                class="fill-current text-blue-700 dark:text-blue-300"
                                d="M22,13a1,1,0,0,1,0-2h1.949A12.006,12.006,0,0,0,13,.051V2a1,1,0,0,1-2,0V.051A12.006,12.006,0,0,0,.051,11H2a1,1,0,0,1,0,2H.051A12.006,12.006,0,0,0,11,23.949V22a1,1,0,0,1,2,0v1.949A12.006,12.006,0,0,0,23.949,13Zm-6,0H12a1,1,0,0,1-.832-.445l-4-6a1,1,0,1,1,1.664-1.11L12.535,11H16a1,1,0,0,1,0,2Z"
                            />
                        </svg>
                        {{ module.duration }} {{ t('minutes') }}
                    </div>

                    <div
                        v-if="module.rating_avg && module.rating_count"
                        class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm border border-slate-600 dark:border-slate-400"
                        :title="t('rating')"
                        itemprop="aggregateRating"
                        itemscope
                        itemtype="https://schema.org/AggregateRating"
                    >
                        <svg viewBox="0 0 24 24" class="shrink-0 h-3 w-3" aria-hidden="true">
                            <path
                                class="fill-current text-red-400 dark:text-red-300"
                                d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"
                            />
                        </svg>

                        <span itemprop="ratingValue">{{ Number(module.rating_avg).toFixed(1) }}</span>
                        <meta itemprop="ratingCount" :content="module.rating_count">
                        <meta itemprop="bestRating" content="5">
                        <meta itemprop="worstRating" content="1">
                    </div>
                </div>

                <!-- Родительский курс -->
                <div
                    v-if="module.course && courseTitle(module)"
                    class="mt-4 flex items-center justify-center gap-2"
                    itemprop="isPartOf"
                    itemscope
                    itemtype="https://schema.org/Course"
                >
                    <link itemprop="url" :href="courseLink(module)">

                    <div
                        class="flex items-center justify-center gap-1 min-w-0 text-xs font-semibold text-slate-700/85 dark:text-slate-300/85"
                        :title="courseTitle(module)"
                    >
                        <div class="text-slate-500 dark:text-slate-400">
                            {{ t('course') }}:
                        </div>

                        <Link
                            :href="courseLink(module)"
                            class="ml-1 truncate hover:text-blue-600 dark:hover:text-blue-400"
                            itemprop="name"
                        >
                            {{ courseTitle(module) }}
                        </Link>

                        <svg class="shrink-0 h-3 w-3 text-sky-600/85 dark:text-sky-200/85" fill="currentColor" viewBox="0 0 448 512" aria-hidden="true">
                            <path d="M318.38 208h-39.09c-1.49 27.03-6.54 51.35-14.21 70.41 27.71-13.24 48.02-39.19 53.3-70.41zm0-32c-5.29-31.22-25.59-57.17-53.3-70.41 7.68 19.06 12.72 43.38 14.21 70.41h39.09zM224 97.31c-7.69 7.45-20.77 34.42-23.43 78.69h46.87c-2.67-44.26-15.75-71.24-23.44-78.69zm-41.08 8.28c-27.71 13.24-48.02 39.19-53.3 70.41h39.09c1.49-27.03 6.53-51.35 14.21-70.41zm0 172.82c-7.68-19.06-12.72-43.38-14.21-70.41h-39.09c5.28 31.22 25.59 57.17 53.3 70.41zM247.43 208h-46.87c2.66 44.26 15.74 71.24 23.43 78.69 7.7-7.45 20.78-34.43 23.44-78.69zM448 358.4V25.6c0-16-9.6-25.6-25.6-25.6H96C41.6 0 0 41.6 0 96v320c0 54.4 41.6 96 96 96h326.4c12.8 0 25.6-9.6 25.6-25.6v-16c0-6.4-3.2-12.8-9.6-19.2-3.2-16-3.2-60.8 0-73.6 6.4-3.2 9.6-9.6 9.6-19.2zM224 64c70.69 0 128 57.31 128 128s-57.31 128-128 128S96 262.69 96 192 153.31 64 224 64zm160 384H96c-19.2 0-32-12.8-32-32s16-32 32-32h288v64z" />
                        </svg>
                    </div>
                </div>

                <div class="mt-3 flex items-center justify-center">
                    <EntityStats
                        :views="module.views ?? 0"
                        :likes-count="module.likes_count ?? 0"
                        :already-liked="module.already_liked ?? false"
                        route-name="public.schoolModules.like"
                        :route-params="module.id"
                        :show-likes-button="true"
                        compact
                    />
                </div>

                <meta v-if="module.published_at" itemprop="datePublished" :content="module.published_at">

                <div class="mt-auto pt-4">
                    <Link
                        :href="moduleLink(module)"
                        class="flex w-full items-center justify-center gap-2 rounded-sm px-3 py-2 btn-default"
                        :aria-label="`${t('readMore')}: ${moduleTitle(module)}`"
                    >
                        <span class="text-sm font-semibold">{{ t('readMore') }}</span>
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path
                                fill-rule="evenodd"
                                d="M7.21 14.77a.75.75 0 0 1 .02-1.06L10.94 10 7.23 6.29a.75.75 0 1 1 1.06-1.06l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06.02Z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </Link>
                </div>
            </div>
        </article>
    </div>
</template>
