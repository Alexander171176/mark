<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import UniversalImageSlider from '@/Components/Public/Default/Images/UniversalImageSlider.vue'

const { t } = useI18n()

const props = defineProps({
    courses: {
        type: [Array, Object],
        default: () => [],
    },
    cols: {
        type: Number,
        default: 2,
    },
})

/**
 * normalize
 */
const normalizeList = (value) => {
    if (Array.isArray(value)) return value
    if (Array.isArray(value?.data)) return value.data
    return []
}

const coursesList = computed(() => normalizeList(props.courses))

const gridClass = computed(() => {
    return props.cols === 2
        ? 'grid-cols-1 sm:grid-cols-2'
        : 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3'
})

const translateLevel = (level) => {
    const normalized = (level || '').toString().trim().toLowerCase()

    const map = {
        beginner: 'levelBeginner',
        intermediate: 'levelIntermediate',
        advanced: 'levelAdvanced',
    }

    return map[normalized] ? t(map[normalized]) : level
}

/**
 * Instructor helpers
 */
const getInstructorProfile = (course) => {
    return course?.instructorProfile || null
}

const getInstructorImages = (course) => {
    const instructorProfile = getInstructorProfile(course)

    return Array.isArray(instructorProfile?.images)
        ? instructorProfile.images
        : []
}

const getInstructorPrimaryImage = (course) => {
    const images = getInstructorImages(course)

    if (!images.length) return null

    return [...images].sort((a, b) => {
        const aOrder = Number(a?.order ?? a?.pivot?.order ?? 999999)
        const bOrder = Number(b?.order ?? b?.pivot?.order ?? 999999)
        return aOrder - bOrder
    })[0]
}

const getInstructorImageUrl = (course) => {
    const image = getInstructorPrimaryImage(course)

    return image?.webp_url
        || image?.url
        || image?.image_url
        || image?.thumb_url
        || null
}

const getInstructorName = (course) => {
    const instructorProfile = getInstructorProfile(course)

    return instructorProfile?.public_name
        || instructorProfile?.title
        || t('instructor')
}
</script>

<template>
    <div v-if="coursesList.length" class="mt-8">
        <h2 class="mb-4 tracking-wide text-center font-semibold text-lg text-gray-700 dark:text-gray-300">
            {{ t('relatedCourses') }}
        </h2>

        <div class="grid gap-4" :class="gridClass">
            <div
                v-for="course in coursesList"
                :key="course.id"
                class="group flex h-full flex-col overflow-hidden
                       rounded-md border border-gray-200
                       bg-white shadow-sm transition
                       hover:-translate-y-0.5 hover:shadow-md dark:border-gray-700 dark:bg-gray-900"
            >
                <Link :href="route('public.courses.show', { slug: course.slug })">
                    <UniversalImageSlider
                        :entity="course"
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
                            :href="route('public.courses.show', { slug: course.slug })"
                            class="inline-flex items-center gap-2"
                        >
                            <span
                                class="text-base font-semibold
                                       text-slate-900/85 group-hover:opacity-75
                                       dark:text-slate-100/85 dark:group-hover:opacity-75"
                            >
                                {{ course.title }}
                            </span>
                        </Link>

                        <div
                            v-if="course?.views > 0"
                            class="inline-flex items-center gap-2"
                        >
                            <svg
                                class="h-4 w-4 text-slate-600/85 dark:text-slate-200/85"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 576 512"
                                fill="currentColor"
                            >
                                <path d="M569.354 231.631C512.97 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-102.556 0-192.091-54.701-240-136 44.157-74.933 123.677-127.27 216.162-135.007C273.958 131.078 280 144.83 280 160c0 30.928-25.072 56-56 56s-56-25.072-56-56l.001-.042C157.794 179.043 152 200.844 152 224c0 75.111 60.889 136 136 136s136-60.889 136-136c0-31.031-10.4-59.629-27.895-82.515C451.704 164.638 498.009 205.106 528 256c-47.908 81.299-137.444 136-240 136z" />
                            </svg>
                            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                                {{ course.views }}
                            </span>
                        </div>
                    </div>

                    <div
                        v-if="course.short"
                        class="mt-3 line-clamp-3 text-sm text-slate-700 dark:text-slate-300"
                    >
                        {{ course.short }}
                    </div>

                    <div class="mt-3 flex flex-wrap items-center justify-center gap-3
                                text-xs font-semibold text-slate-500 dark:text-slate-400">
                        <div
                            v-if="course.level"
                            class="flex items-center justify-center gap-1"
                            :title="t('level')"
                        >
                            <svg
                                class="w-3 h-3 fill-current text-slate-600 dark:text-slate-200"
                                viewBox="0 0 24 24"
                            >
                                <path class="fill-current text-teal-600 dark:text-teal-300"
                                      d="M12,24a1,1,0,0,1,0-2A10,10,0,0,0,12,2a1,1,0,0,1,0-2,12,12,0,0,1,0,24Z"></path>
                                <path class="fill-current text-teal-600 dark:text-teal-300"
                                      d="M1.045,13.913a1,1,0,0,1-1-.919C.022,12.665,0,12.336,0,12s.022-.665.049-.994a1,1,0,1,1,1.993.162C2.021,11.442,2,11.719,2,12s.021.558.042.832a1,1,0,0,1-.916,1.078Q1.086,13.913,1.045,13.913Z"></path>
                                <path class="fill-current text-teal-600 dark:text-teal-300"
                                      d="M6.243,3.641a1,1,0,0,1-.526-1.852,12.022,12.022,0,0,1,1.774-.9,1,1,0,1,1,.754,1.851,10.133,10.133,0,0,0-1.478.757A.993.993,0,0,1,6.243,3.641Z"></path>
                                <path class="fill-current text-teal-600 dark:text-teal-300"
                                      d="M2.188,8.044a.988.988,0,0,1-.451-.108A1,1,0,0,1,1.3,6.592,12.131,12.131,0,0,1,2.342,4.9,1,1,0,0,1,3.953,6.083,10.1,10.1,0,0,0,3.081,7.5,1,1,0,0,1,2.188,8.044Z"></path>
                                <path class="fill-current text-teal-600 dark:text-teal-300"
                                      d="M3.128,19.482a1,1,0,0,1-.808-.409,12.049,12.049,0,0,1-1.041-1.7,1,1,0,1,1,1.787-.9,10.047,10.047,0,0,0,.868,1.418,1,1,0,0,1-.217,1.4A.986.986,0,0,1,3.128,19.482Z"></path>
                                <path d="M7.853,23.185a.983.983,0,0,1-.377-.075A11.879,11.879,0,0,1,5.7,22.2,1,1,0,0,1,6.75,20.5a10.041,10.041,0,0,0,1.48.761,1,1,0,0,1-.377,1.926Z"></path>
                                <path d="M10,17a1,1,0,0,1-.707-.293l-4-4a1,1,0,0,1,1.414-1.414L10,14.586l7.293-7.293a1,1,0,1,1,1.414,1.414l-8,8A1,1,0,0,1,10,17Z"></path>
                            </svg>
                            {{ translateLevel(course.level) }}
                        </div>

                        <div
                            v-if="course.duration"
                            class="flex items-center justify-center gap-1"
                            :title="t('duration')"
                        >
                            <svg
                                class="w-3 h-3"
                                viewBox="0 0 24 24"
                            >
                                <path class="fill-current text-blue-700 dark:text-blue-300"
                                      d="M22,13a1,1,0,0,1,0-2h1.949A12.006,12.006,0,0,0,13,.051V2a1,1,0,0,1-2,0V.051A12.006,12.006,0,0,0,.051,11H2a1,1,0,0,1,0,2H.051A12.006,12.006,0,0,0,11,23.949V22a1,1,0,0,1,2,0v1.949A12.006,12.006,0,0,0,23.949,13Zm-6,0H12a1,1,0,0,1-.832-.445l-4-6a1,1,0,1,1,1.664-1.11L12.535,11H16a1,1,0,0,1,0,2Z"></path>
                            </svg>
                            {{ course.duration }} мин
                        </div>

                        <div
                            v-if="course.rating_avg"
                            class="flex items-center justify-center gap-1"
                            :title="t('rating')"
                        >
                            <svg
                                viewBox="0 0 24 24"
                                class="shrink-0 h-3 w-3"
                            >
                                <path class="fill-current text-red-400 dark:text-red-300"
                                      d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"></path>
                            </svg>
                            {{ Number(course.rating_avg).toFixed(1) }}
                        </div>
                    </div>

                    <div
                        v-if="course?.instructorProfile"
                        class="mt-4 flex items-center justify-center gap-2"
                    >
                        <img
                            v-if="getInstructorImageUrl(course)"
                            :src="getInstructorImageUrl(course)"
                            :alt="getInstructorName(course)"
                            loading="lazy"
                            class="h-6 w-6 rounded-full object-cover
                                   ring-1 ring-gray-200 dark:ring-gray-700"
                        />

                        <div
                            class="min-w-0 text-xs font-semibold
                                   text-slate-700/85 dark:text-slate-300/85"
                            :title="`ID: ${course.instructorProfile.id}`"
                        >
                            {{ getInstructorName(course) }}
                        </div>
                    </div>

                    <div class="mt-auto pt-4">
                        <Link
                            :href="route('public.courses.show', { slug: course.slug })"
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
    </div>
</template>
