<script setup>
import { computed, ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import ModuleLessonsAccordion from '@/Components/Public/Default/School/Module/ModuleLessonsAccordion.vue'
import UniversalImageSlider from '@/Components/Public/Default/Images/UniversalImageSlider.vue'
import EntityStats from '@/Components/Public/Default/Stats/EntityStats.vue'

const { t } = useI18n()

const props = defineProps({
    modules: { type: Array, default: () => [] },
    cols: { type: Number, default: 2 },
})

const openedIds = ref([])

const toggleModule = (id) => {
    if (openedIds.value.includes(id)) {
        openedIds.value = openedIds.value.filter(item => item !== id)
        return
    }

    openedIds.value = [...openedIds.value, id]
}

const isOpen = (id) => openedIds.value.includes(id)

const gridClass = computed(() => {
    return props.cols === 2
        ? 'grid-cols-1 sm:grid-cols-2'
        : 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3'
})

const lessonsCount = (module) => {
    return Number(module?.lessons_count_loaded ?? module?.lessons_count ?? 0)
}

const shortText = (module) => {
    return module?.short || module?.subtitle || ''
}

const moduleLessons = (module) => {
    return Array.isArray(module?.lessons) ? module.lessons : []
}

const moduleLink = (module) => {
    return module?.slug
        ? route('public.modules.show', { slug: module.slug })
        : '#'
}

const courseLink = (module) => {
    return module?.course?.slug
        ? route('public.courses.show', { slug: module.course.slug })
        : '#'
}
</script>

<template>
    <div class="grid gap-4" :class="gridClass">
        <div
            v-for="module in modules"
            :key="module.id"
            class="group flex h-full flex-col overflow-hidden
                   rounded-md border border-gray-200
                   bg-white shadow-sm transition
                   hover:-translate-y-0.5 hover:shadow-md
                   dark:border-gray-700 dark:bg-gray-900"
        >
            <Link :href="moduleLink(module)">
                <UniversalImageSlider
                    :entity="module"
                    height-class="h-48"
                    rounded-class=""
                    wrapper-class=""
                    img-class="w-full h-full object-cover transition
                               duration-300 group-hover:scale-105"
                />
            </Link>

            <div class="flex flex-1 flex-col p-4">
                <div class="flex items-center justify-center gap-2">
                    <Link
                        :href="moduleLink(module)"
                        class="inline-flex items-center gap-2"
                    >
                        <span
                            class="text-base font-semibold
                                   text-slate-900/85 group-hover:opacity-75
                                   dark:text-slate-100/85 dark:group-hover:opacity-75"
                        >
                            {{ module.title }}
                        </span>
                    </Link>
                </div>

                <div
                    v-if="shortText(module)"
                    class="mt-3 line-clamp-3 text-sm text-slate-700 dark:text-slate-300"
                >
                    {{ shortText(module) }}
                </div>

                <!-- Данные -->
                <div
                    class="mt-3 flex flex-wrap items-center justify-center gap-2
                           text-xs font-semibold text-slate-500 dark:text-slate-400">
                    <div
                        v-if="lessonsCount(module)"
                        class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm
                               border border-slate-600 dark:border-slate-400"
                        :title="t('lessons')"
                    >
                        <svg class="shrink-0 h-4 w-4 text-indigo-600/85 dark:text-indigo-200/85"
                             fill="currentColor"
                             viewBox="0 0 24 24">
                            <path d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                        {{ lessonsCount(module) }}
                    </div>

                    <div
                        v-if="module.duration"
                        class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm
                               border border-slate-600 dark:border-slate-400"
                        :title="t('duration')"
                    >
                        <svg
                            class="w-3 h-3"
                            viewBox="0 0 24 24"
                        >
                            <path
                                class="fill-current text-blue-700 dark:text-blue-300"
                                d="M22,13a1,1,0,0,1,0-2h1.949A12.006,12.006,0,0,0,13,.051V2a1,1,0,0,1-2,0V.051A12.006,12.006,0,0,0,.051,11H2a1,1,0,0,1,0,2H.051A12.006,12.006,0,0,0,11,23.949V22a1,1,0,0,1,2,0v1.949A12.006,12.006,0,0,0,23.949,13Zm-6,0H12a1,1,0,0,1-.832-.445l-4-6a1,1,0,1,1,1.664-1.11L12.535,11H16a1,1,0,0,1,0,2Z"
                            />
                        </svg>
                        {{ module.duration }} {{ t('minutes') }}
                    </div>

                    <div
                        v-if="module.rating_avg"
                        class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm
                               border border-slate-600 dark:border-slate-400"
                        :title="t('rating')"
                    >
                        <svg
                            viewBox="0 0 24 24"
                            class="shrink-0 h-3 w-3"
                        >
                            <path
                                class="fill-current text-red-400 dark:text-red-300"
                                d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"
                            />
                        </svg>
                        {{ Number(module.rating_avg).toFixed(1) }}
                    </div>
                </div>

                <div
                    v-if="module?.course"
                    class="mt-4 flex items-center justify-center gap-2"
                >
                    <div
                        class="flex items-center justify-center gap-1
                               min-w-0 text-xs font-semibold
                               text-slate-700/85 dark:text-slate-300/85"
                        :title="module.course.title"
                    >
                        <div class="text-slate-500 dark:text-slate-400">
                            {{ t('course') }}:
                        </div>
                        <Link
                            :href="courseLink(module)"
                            class="ml-1 hover:text-blue-600 dark:hover:text-blue-400"
                        >
                            {{ module.course.title }}
                        </Link>
                        <svg class="shrink-0 h-3 w-3 text-sky-600/85 dark:text-sky-200/85"
                             fill="currentColor"
                             viewBox="0 0 448 512">
                            <path d="M318.38 208h-39.09c-1.49 27.03-6.54 51.35-14.21 70.41 27.71-13.24 48.02-39.19 53.3-70.41zm0-32c-5.29-31.22-25.59-57.17-53.3-70.41 7.68 19.06 12.72 43.38 14.21 70.41h39.09zM224 97.31c-7.69 7.45-20.77 34.42-23.43 78.69h46.87c-2.67-44.26-15.75-71.24-23.44-78.69zm-41.08 8.28c-27.71 13.24-48.02 39.19-53.3 70.41h39.09c1.49-27.03 6.53-51.35 14.21-70.41zm0 172.82c-7.68-19.06-12.72-43.38-14.21-70.41h-39.09c5.28 31.22 25.59 57.17 53.3 70.41zM247.43 208h-46.87c2.66 44.26 15.74 71.24 23.43 78.69 7.7-7.45 20.78-34.43 23.44-78.69zM448 358.4V25.6c0-16-9.6-25.6-25.6-25.6H96C41.6 0 0 41.6 0 96v320c0 54.4 41.6 96 96 96h326.4c12.8 0 25.6-9.6 25.6-25.6v-16c0-6.4-3.2-12.8-9.6-19.2-3.2-16-3.2-60.8 0-73.6 6.4-3.2 9.6-9.6 9.6-19.2zM224 64c70.69 0 128 57.31 128 128s-57.31 128-128 128S96 262.69 96 192 153.31 64 224 64zm160 384H96c-19.2 0-32-12.8-32-32s16-32 32-32h288v64z"/>
                        </svg>
                    </div>
                </div>

                <div class="mt-3 flex items-center justify-center">
                    <EntityStats
                        :views="module.views || 0"
                        :likes-count="module.likes_count || 0"
                        :already-liked="module.already_liked || false"
                        route-name="modules.like"
                        :route-params="{ module: module.id }"
                        :show-likes-button="true"
                        compact
                    />
                </div>

                <div class="mt-auto pt-4">
                    <div v-if="moduleLessons(module).length" class="mb-4">
                        <button
                            type="button"
                            class="flex w-full items-center justify-between rounded-lg
                                   border px-3 py-2 text-sm font-medium text-slate-700
                                   transition hover:bg-slate-50 border-slate-400
                                   dark:text-slate-300 dark:hover:bg-slate-800"
                            @click="toggleModule(module.id)"
                        >
                            <span>
                                {{ t('lessons') }}
                            </span>

                            <svg
                                class="h-4 w-4 transition-transform"
                                :class="isOpen(module.id) ? 'rotate-180' : ''"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </button>

                        <transition
                            enter-active-class="transition duration-200 ease-out"
                            enter-from-class="opacity-0 -translate-y-1"
                            enter-to-class="opacity-100 translate-y-0"
                            leave-active-class="transition duration-150 ease-in"
                            leave-from-class="opacity-100 translate-y-0"
                            leave-to-class="opacity-0 -translate-y-1"
                        >
                            <ModuleLessonsAccordion
                                v-if="isOpen(module.id)"
                                :lessons="moduleLessons(module)"
                            />
                        </transition>
                    </div>

                    <Link
                        :href="moduleLink(module)"
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
