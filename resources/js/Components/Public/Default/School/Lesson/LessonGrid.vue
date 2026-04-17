<script setup>
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { computed } from 'vue'
import UniversalImageSlider from '@/Components/Public/Default/Images/UniversalImageSlider.vue'
import EntityStats from '@/Components/Public/Default/Stats/EntityStats.vue'

const { t } = useI18n()

const props = defineProps({
    lessons: {
        type: Array,
        default: () => [],
    },
    cols: {
        type: Number,
        default: 3,
    },
})

const gridClass = computed(() => {
    return props.cols === 2
        ? 'grid-cols-1 sm:grid-cols-2'
        : 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3'
})

const lessonLink = (lesson) => {
    return route('public.lessons.show', { slug: lesson.slug })
}

const getModule = (lesson) => {
    return lesson?.module || null
}

const getCourse = (lesson) => {
    return lesson?.course || lesson?.module?.course || null
}

const moduleLink = (lesson) => {
    const module = getModule(lesson)
    return module?.slug
        ? route('public.modules.show', { slug: module.slug })
        : '#'
}

const courseLink = (lesson) => {
    const course = getCourse(lesson)
    return course?.slug
        ? route('public.courses.show', { slug: course.slug })
        : '#'
}

const getModuleTitle = (lesson) => {
    return getModule(lesson)?.title || t('module')
}

const getCourseTitle = (lesson) => {
    return getCourse(lesson)?.title || t('course')
}

const getAccessType = (lesson) => {
    return lesson?.access_type || ''
}

const translateAccessType = (value) => {
    const normalized = (value || '').toString().trim().toLowerCase()

    const map = {
        free: 'free',
        paid: 'paid',
        preview: 'preview',
    }

    return map[normalized] ? t(map[normalized]) : value
}

const getDuration = (lesson) => {
    return Number(lesson?.duration ?? 0)
}

const getRating = (lesson) => {
    const rating = Number(lesson?.rating_avg ?? 0)
    return Number.isFinite(rating) ? rating : 0
}

/** Инструктор  **/
const getInstructorProfile = (lesson) => {
    return lesson?.course?.instructorProfile || lesson?.module?.course?.instructorProfile || null
}

const getInstructorImages = (lesson) => {
    const instructorProfile = getInstructorProfile(lesson)

    return Array.isArray(instructorProfile?.images)
        ? instructorProfile.images
        : []
}

const getInstructorPrimaryImage = (lesson) => {
    const images = getInstructorImages(lesson)

    if (!images.length) return null

    return [...images].sort((a, b) => {
        const aOrder = Number(a?.order ?? a?.pivot?.order ?? 999999)
        const bOrder = Number(b?.order ?? b?.pivot?.order ?? 999999)
        return aOrder - bOrder
    })[0]
}

const getInstructorImageUrl = (lesson) => {
    const image = getInstructorPrimaryImage(lesson)

    return image?.webp_url
        || image?.url
        || image?.image_url
        || image?.thumb_url
        || null
}

const getInstructorName = (lesson) => {
    const instructorProfile = getInstructorProfile(lesson)

    return instructorProfile?.public_name
        || instructorProfile?.title
        || t('instructor')
}
</script>

<template>
    <div class="grid gap-4" :class="gridClass">
        <div
            v-for="lesson in lessons"
            :key="lesson.id"
            class="group flex h-full flex-col overflow-hidden
                   rounded-md border border-gray-200
                   bg-white shadow-sm transition
                   hover:-translate-y-0.5 hover:shadow-md dark:border-gray-700 dark:bg-gray-900"
        >
            <Link :href="lessonLink(lesson)">
                <UniversalImageSlider
                    :entity="lesson"
                    height-class="h-48"
                    rounded-class=""
                    wrapper-class=""
                    img-class="w-full h-full object-cover transition
                               duration-300 group-hover:scale-105"
                />
            </Link>

            <div class="flex flex-1 flex-col p-4">
                <div class="flex items-center justify-center text-center">
                    <Link
                        :href="lessonLink(lesson)"
                        class="inline-flex items-center gap-2"
                    >
                        <span
                            class="text-base font-semibold
                                   text-slate-900/85 group-hover:opacity-75
                                   dark:text-slate-100/85 dark:group-hover:opacity-75"
                        >
                            {{ lesson.title }} #{{ lesson.id }}
                        </span>
                    </Link>
                </div>

                <div
                    v-if="lesson.short"
                    class="mt-3 line-clamp-3 text-sm text-slate-700 dark:text-slate-300"
                >
                    {{ lesson.short }}
                </div>

                <!-- Данные -->
                <div
                    class="mt-3 flex flex-wrap items-center justify-center gap-2
                           text-xs font-semibold text-slate-500 dark:text-slate-400">
                    <div
                        v-if="getAccessType(lesson)"
                        class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm
                               border border-slate-600 dark:border-slate-400"
                        :title="t('access')"
                    >
                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                class="fill-current text-teal-600 dark:text-teal-300"
                                d="M12 1a5 5 0 00-5 5v3H6a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2v-8a2 2 0 00-2-2h-1V6a5 5 0 00-5-5zm-3 8V6a3 3 0 116 0v3H9z"
                            />
                        </svg>
                        {{ translateAccessType(getAccessType(lesson)) }}
                    </div>

                    <div
                        v-if="getDuration(lesson) > 0"
                        class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm
                               border border-slate-600 dark:border-slate-400"
                        :title="t('duration')"
                    >
                        <svg class="w-3 h-3" viewBox="0 0 24 24">
                            <path
                                class="fill-current text-blue-700 dark:text-blue-300"
                                d="M22,13a1,1,0,0,1,0-2h1.949A12.006,12.006,0,0,0,13,.051V2a1,1,0,0,1-2,0V.051A12.006,12.006,0,0,0,.051,11H2a1,1,0,0,1,0,2H.051A12.006,12.006,0,0,0,11,23.949V22a1,1,0,0,1,2,0v1.949A12.006,12.006,0,0,0,23.949,13Zm-6,0H12a1,1,0,0,1-.832-.445l-4-6a1,1,0,1,1,1.664-1.11L12.535,11H16a1,1,0,0,1,0,2Z"
                            />
                        </svg>
                        {{ getDuration(lesson) }} {{ t('minutes') }}
                    </div>

                    <div
                        v-if="getRating(lesson) > 0"
                        class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm
                               border border-slate-600 dark:border-slate-400"
                        :title="t('rating')"
                    >
                        <svg viewBox="0 0 24 24" class="shrink-0 h-3 w-3">
                            <path
                                class="fill-current text-red-400 dark:text-red-300"
                                d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"
                            />
                        </svg>
                        {{ getRating(lesson).toFixed(1) }}
                    </div>
                </div>

                <div
                    v-if="lesson?.module"
                    class="mt-4 flex items-center justify-center gap-2"
                >
                    <div
                        class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm
                               border border-slate-600 dark:border-slate-400
                               min-w-0 text-xs font-semibold
                               text-slate-700/85 dark:text-slate-300/85"
                        :title="getModuleTitle(lesson)"
                    >
                        <div class="text-slate-500 dark:text-slate-400">
                            {{ t('module') }}:
                        </div>
                        <Link
                            :href="moduleLink(lesson)"
                            class="ml-1 hover:text-blue-600 dark:hover:text-blue-400"
                        >
                            {{ getModuleTitle(lesson) }}
                        </Link>
                        <svg class="shrink-0 h-3 w-3 text-teal-600/85 dark:text-teal-200/85"
                             fill="currentColor"
                             viewBox="0 0 24 24">
                            <rect x="1" y="1" width="10" height="10" rx="2"></rect>
                            <path class="fill-current text-teal-400"
                                  d="M23.428,4.618,19.381.572h0a1.957,1.957,0,0,0-2.762,0L12.572,4.618a1.959,1.959,0,0,0,0,2.764l4.047,4.047a1.957,1.957,0,0,0,2.762,0l4.047-4.046A1.959,1.959,0,0,0,23.428,4.618Z"></path>
                            <rect x="13" y="13" width="10" height="10" rx="2"></rect>
                            <rect x="1" y="13" width="10" height="10" rx="2"></rect>
                        </svg>
                    </div>
                </div>

                <div
                    v-if="getCourse(lesson)"
                    class="mt-2 flex items-center justify-center gap-2"
                >
                    <div
                        class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm
                               border border-slate-600 dark:border-slate-400
                               min-w-0 text-xs font-semibold
                               text-slate-700/85 dark:text-slate-300/85"
                        :title="getCourseTitle(lesson)"
                    >
                        <div class="text-slate-500 dark:text-slate-400">
                            {{ t('course') }}:
                        </div>
                        <Link
                            :href="courseLink(lesson)"
                            class="ml-1 hover:text-blue-600 dark:hover:text-blue-400"
                        >
                            {{ getCourseTitle(lesson) }}
                        </Link>
                        <svg class="shrink-0 h-3 w-3 text-sky-600/85 dark:text-sky-200/85"
                             fill="currentColor"
                             viewBox="0 0 448 512">
                            <path d="M318.38 208h-39.09c-1.49 27.03-6.54 51.35-14.21 70.41 27.71-13.24 48.02-39.19 53.3-70.41zm0-32c-5.29-31.22-25.59-57.17-53.3-70.41 7.68 19.06 12.72 43.38 14.21 70.41h39.09zM224 97.31c-7.69 7.45-20.77 34.42-23.43 78.69h46.87c-2.67-44.26-15.75-71.24-23.44-78.69zm-41.08 8.28c-27.71 13.24-48.02 39.19-53.3 70.41h39.09c1.49-27.03 6.53-51.35 14.21-70.41zm0 172.82c-7.68-19.06-12.72-43.38-14.21-70.41h-39.09c5.28 31.22 25.59 57.17 53.3 70.41zM247.43 208h-46.87c2.66 44.26 15.74 71.24 23.43 78.69 7.7-7.45 20.78-34.43 23.44-78.69zM448 358.4V25.6c0-16-9.6-25.6-25.6-25.6H96C41.6 0 0 41.6 0 96v320c0 54.4 41.6 96 96 96h326.4c12.8 0 25.6-9.6 25.6-25.6v-16c0-6.4-3.2-12.8-9.6-19.2-3.2-16-3.2-60.8 0-73.6 6.4-3.2 9.6-9.6 9.6-19.2zM224 64c70.69 0 128 57.31 128 128s-57.31 128-128 128S96 262.69 96 192 153.31 64 224 64zm160 384H96c-19.2 0-32-12.8-32-32s16-32 32-32h288v64z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Инструктор -->
                <div
                    v-if="getInstructorProfile(lesson)"
                    class="mt-2 flex items-center justify-center gap-2"
                >
                    <img
                        v-if="getInstructorImageUrl(lesson)"
                        :src="getInstructorImageUrl(lesson)"
                        :alt="getInstructorName(lesson)"
                        loading="lazy"
                        class="h-6 w-6 rounded-full object-cover
                               ring-1 ring-gray-200 dark:ring-gray-700"
                    />

                    <div
                        class="flex items-center justify-center gap-1
                               min-w-0 text-xs font-semibold
                               text-slate-700/85 dark:text-slate-300/85"
                        :title="getInstructorName(lesson)"
                    >
                        <div class="text-slate-500 dark:text-slate-400">
                            {{ t('instructor') }}:
                        </div>
                        <div class="ml-1">
                            {{ getInstructorName(lesson) }}
                        </div>
                        <svg class="shrink-0 h-4 w-4 text-violet-600/85 dark:text-violet-200/85"
                             fill="currentColor"
                             viewBox="0 0 640 512">
                            <path d="M622.34 153.2L343.4 67.5c-15.2-4.67-31.6-4.67-46.79 0L17.66 153.2c-23.54 7.23-23.54 38.36 0 45.59l48.63 14.94c-10.67 13.19-17.23 29.28-17.88 46.9C38.78 266.15 32 276.11 32 288c0 10.78 5.68 19.85 13.86 25.65L20.33 428.53C18.11 438.52 25.71 448 35.94 448h56.11c10.24 0 17.84-9.48 15.62-19.47L82.14 313.65C90.32 307.85 96 298.78 96 288c0-11.57-6.47-21.25-15.66-26.87.76-15.02 8.44-28.3 20.69-36.72L296.6 284.5c9.06 2.78 26.44 6.25 46.79 0l278.95-85.7c23.55-7.24 23.55-38.36 0-45.6zM352.79 315.09c-28.53 8.76-52.84 3.92-65.59 0l-145.02-44.55L128 384c0 35.35 85.96 64 192 64s192-28.65 192-64l-14.18-113.47-145.03 44.56z"></path>
                        </svg>
                    </div>
                </div>

                <div class="mt-3 flex items-center justify-center">
                    <EntityStats
                        :views="lesson.views || 0"
                        :likes-count="lesson.likes_count || lesson.likes || 0"
                        :already-liked="lesson.already_liked || false"
                        route-name="lessons.like"
                        :route-params="{ lesson: lesson.id }"
                        :show-likes-button="true"
                        compact
                    />
                </div>

                <div class="mt-auto pt-4">
                    <Link
                        :href="lessonLink(lesson)"
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
