<script setup>
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import UniversalImageSlider from '@/Components/Public/Default/Images/UniversalImageSlider.vue'

const { t } = useI18n()

const props = defineProps({
    instructors: {
        type: Array,
        default: () => [],
    },
    cols: {
        type: Number,
        default: 3,
    },
})

const gridClass = computed(() => {
    return props.cols === 3
        ? 'grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3'
        : 'grid grid-cols-1 gap-4 sm:grid-cols-2'
})

const getInstructorName = (instructor) => {
    return instructor?.public_name || instructor?.title || 'Инструктор'
}

const getInstructorShort = (instructor) => {
    return instructor?.short || ''
}

const getRatingText = (instructor) => {
    const avg = instructor?.rating?.avg
    const count = instructor?.rating?.count ?? 0

    if (avg === null || avg === undefined) {
        return '—'
    }

    return `${Number(avg).toFixed(1)} (${count})`
}
</script>

<template>
    <div :class="gridClass">
        <div
            v-for="instructor in instructors"
            :key="instructor.id"
            class="group flex h-full flex-col overflow-hidden rounded-md
                   border border-gray-200 bg-white shadow-sm
                   transition hover:-translate-y-0.5 hover:shadow-md
                   dark:border-gray-700 dark:bg-gray-900"
        >
            <Link
                :href="route('public.instructors.show', { slug: instructor.slug })"
            >
                <UniversalImageSlider
                    :entity="instructor"
                    height-class="h-48"
                    rounded-class=""
                    wrapper-class=""
                    img-class="w-full h-full object-cover transition
                               duration-300 group-hover:scale-105"
                />
            </Link>

            <div class="flex flex-1 flex-col p-4">
                <div class="flex items-center justify-between gap-2">
                    <Link
                        :href="route('public.instructors.show', { slug: instructor.slug })"
                        class="inline-flex items-center gap-1"
                    >
                        <span
                            class="text-lg font-semibold text-slate-900/85
                                   group-hover:opacity-75 dark:text-slate-100/85
                                   dark:group-hover:opacity-75"
                        >
                            {{ getInstructorName(instructor) }}
                        </span>
                    </Link>

                    <div v-if="instructor?.views > 0" class="inline-flex items-center gap-2">
                        <svg
                            class="h-4 w-4 text-slate-600/85 dark:text-slate-200/85"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 576 512"
                            fill="currentColor"
                        >
                            <path
                                d="M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z"
                            />
                        </svg>
                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                            {{ instructor.views }}
                        </span>
                    </div>
                </div>

                <div
                    v-if="getInstructorShort(instructor)"
                    class="mt-2 line-clamp-2 text-sm text-slate-700 dark:text-slate-300"
                >
                    {{ getInstructorShort(instructor) }}
                </div>

                <!-- Данные -->
                <div
                    class="mt-3 flex flex-wrap items-center justify-center gap-2
                           text-xs font-semibold text-slate-500 dark:text-slate-400">

                    <div
                        v-if="instructor.experience_years"
                        class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm
                               border border-slate-600 dark:border-slate-400">
                        <svg viewBox="0 0 24 24"
                             class="shrink-0 h-3 w-3">
                            <path class="fill-current text-red-400 dark:text-red-300"
                                  d="M14.586,9.439S15.7,2.858,11.138,0A8.055,8.055,0,0,1,8.1,5.831C6.149,7.546,2.481,11.4,2.52,15.51A9.435,9.435,0,0,0,7.7,24a5.975,5.975,0,0,1,2.091-4.132,4.877,4.877,0,0,0,1.869-3.278,8.786,8.786,0,0,1,4.652,7.322v.02a8.827,8.827,0,0,0,5.137-7.659c.324-3.863-1.792-9.112-3.668-10.828A10.192,10.192,0,0,1,14.586,9.439Z"></path>
                        </svg>
                        {{t('experienceYears')}}:
                        {{ instructor.experience_years }}
                    </div>
                    <div
                        class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm
                               border border-slate-600 dark:border-slate-400">
                        <svg viewBox="0 0 24 24"
                             class="shrink-0 h-3 w-3">
                            <path class="fill-current text-red-400 dark:text-red-300"
                                  d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"></path>
                        </svg>
                        {{t('rating')}}:
                        {{ getRatingText(instructor) }}
                    </div>
                    <div
                        v-if="instructor.courses_count > 0"
                        class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm
                               border border-slate-600 dark:border-slate-400">
                        <svg class="shrink-0 h-3 w-3 text-sky-600/85 dark:text-sky-200/85"
                             fill="currentColor"
                             viewBox="0 0 448 512">
                            <path d="M318.38 208h-39.09c-1.49 27.03-6.54 51.35-14.21 70.41 27.71-13.24 48.02-39.19 53.3-70.41zm0-32c-5.29-31.22-25.59-57.17-53.3-70.41 7.68 19.06 12.72 43.38 14.21 70.41h39.09zM224 97.31c-7.69 7.45-20.77 34.42-23.43 78.69h46.87c-2.67-44.26-15.75-71.24-23.44-78.69zm-41.08 8.28c-27.71 13.24-48.02 39.19-53.3 70.41h39.09c1.49-27.03 6.53-51.35 14.21-70.41zm0 172.82c-7.68-19.06-12.72-43.38-14.21-70.41h-39.09c5.28 31.22 25.59 57.17 53.3 70.41zM247.43 208h-46.87c2.66 44.26 15.74 71.24 23.43 78.69 7.7-7.45 20.78-34.43 23.44-78.69zM448 358.4V25.6c0-16-9.6-25.6-25.6-25.6H96C41.6 0 0 41.6 0 96v320c0 54.4 41.6 96 96 96h326.4c12.8 0 25.6-9.6 25.6-25.6v-16c0-6.4-3.2-12.8-9.6-19.2-3.2-16-3.2-60.8 0-73.6 6.4-3.2 9.6-9.6 9.6-19.2zM224 64c70.69 0 128 57.31 128 128s-57.31 128-128 128S96 262.69 96 192 153.31 64 224 64zm160 384H96c-19.2 0-32-12.8-32-32s16-32 32-32h288v64z"/>
                        </svg>
                        {{t('courses')}}:
                        {{ instructor.courses_count }}
                    </div>
                </div>

                <div class="mt-auto pt-4">
                    <Link
                        :href="route('public.instructors.show', { slug: instructor.slug })"
                        class="flex w-full items-center justify-center gap-2
                               rounded-sm px-3 py-2 btn-default"
                    >
                        <span class="text-sm font-semibold">{{ t('readMore') }}</span>
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
