<script setup>
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import UniversalImageSlider from '@/Components/Public/Default/Images/UniversalImageSlider.vue'
import EntityStats from '@/Components/Public/Default/Stats/EntityStats.vue'

const { t } = useI18n()

const props = defineProps({
    courses: {
        type: Array,
        default: () => [],
    },
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
    <div class="space-y-4">
        <div
            v-for="course in courses"
            :key="course.id"
            class="group flex flex-col sm:flex-row gap-3
                   rounded-md border border-gray-200 bg-white shadow-sm
                   p-3 transition hover:shadow-md
                   dark:border-gray-700 dark:bg-gray-900"
        >
            <Link
                :href="route('public.courses.show', { slug: course.slug })"
                class="shrink-0">
                <UniversalImageSlider
                    :entity="course"
                    height-class="h-44"
                    rounded-class="rounded-md"
                    wrapper-class="w-full sm:w-60 border border-gray-400 dark:border-gray-600"
                    img-class="w-full h-full object-cover transition
                               duration-300 group-hover:scale-105"
                />
            </Link>

                <div class="min-w-0 flex-1 flex flex-col justify-around">
                    <div class="flex items-start justify-center gap-3">

                        <Link :href="route('public.courses.show', { slug: course.slug })"
                              class="min-w-0 inline-flex items-center gap-2">
                            <span class="truncate text-lg font-semibold
                                          text-slate-900/85 dark:text-slate-100/85
                                          group-hover:opacity-75">
                                {{ course.title }}
                            </span>
                        </Link>

                    </div>

                    <div
                        v-if="course.short"
                        class="mt-2 line-clamp-2 text-sm text-slate-700 dark:text-slate-300"
                    >
                        {{ course.short }}
                    </div>

                    <!-- Данные -->
                    <div
                        class="mt-3 flex flex-wrap items-center justify-center gap-2
                           text-xs font-semibold text-slate-500 dark:text-slate-400">

                        <div
                            v-if="course.level"
                            class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm
                                   border border-slate-600 dark:border-slate-400"
                             :title="t('level')">
                            <svg
                                class="w-3 h-3 fill-current text-slate-600 dark:text-slate-200"
                                viewBox="0 0 24 24">
                                <path class="fill-current text-teal-600 dark:text-teal-300"
                                      d="M12,24a1,1,0,0,1,0-2A10,10,0,0,0,12,2a1,1,0,0,1,0-2,12,12,0,0,1,0,24Z"></path><path class="fill-current text-teal-600 dark:text-teal-300" d="M1.045,13.913a1,1,0,0,1-1-.919C.022,12.665,0,12.336,0,12s.022-.665.049-.994a1,1,0,1,1,1.993.162C2.021,11.442,2,11.719,2,12s.021.558.042.832a1,1,0,0,1-.916,1.078Q1.086,13.913,1.045,13.913Z"></path><path class="fill-current text-teal-600 dark:text-teal-300" d="M6.243,3.641a1,1,0,0,1-.526-1.852,12.022,12.022,0,0,1,1.774-.9,1,1,0,1,1,.754,1.851,10.133,10.133,0,0,0-1.478.757A.993.993,0,0,1,6.243,3.641Z"></path><path class="fill-current text-teal-600 dark:text-teal-300" d="M2.188,8.044a.988.988,0,0,1-.451-.108A1,1,0,0,1,1.3,6.592,12.131,12.131,0,0,1,2.342,4.9,1,1,0,0,1,3.953,6.083,10.1,10.1,0,0,0,3.081,7.5,1,1,0,0,1,2.188,8.044Z"></path><path class="fill-current text-teal-600 dark:text-teal-300" d="M3.128,19.482a1,1,0,0,1-.808-.409,12.049,12.049,0,0,1-1.041-1.7,1,1,0,1,1,1.787-.9,10.047,10.047,0,0,0,.868,1.418,1,1,0,0,1-.217,1.4A.986.986,0,0,1,3.128,19.482Z"></path><path d="M7.853,23.185a.983.983,0,0,1-.377-.075A11.879,11.879,0,0,1,5.7,22.2,1,1,0,0,1,6.75,20.5a10.041,10.041,0,0,0,1.48.761,1,1,0,0,1-.377,1.926Z"></path><path d="M10,17a1,1,0,0,1-.707-.293l-4-4a1,1,0,0,1,1.414-1.414L10,14.586l7.293-7.293a1,1,0,1,1,1.414,1.414l-8,8A1,1,0,0,1,10,17Z"></path>
                            </svg>
                            {{ translateLevel(course.level) }}
                        </div>
                        <div
                            v-if="course.duration"
                            class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm
                                   border border-slate-600 dark:border-slate-400"
                             :title="t('duration')">
                            <svg
                                class="w-3 h-3"
                                viewBox="0 0 24 24">
                                <path class="fill-current text-blue-700 dark:text-blue-300"
                                      d="M22,13a1,1,0,0,1,0-2h1.949A12.006,12.006,0,0,0,13,.051V2a1,1,0,0,1-2,0V.051A12.006,12.006,0,0,0,.051,11H2a1,1,0,0,1,0,2H.051A12.006,12.006,0,0,0,11,23.949V22a1,1,0,0,1,2,0v1.949A12.006,12.006,0,0,0,23.949,13Zm-6,0H12a1,1,0,0,1-.832-.445l-4-6a1,1,0,1,1,1.664-1.11L12.535,11H16a1,1,0,0,1,0,2Z"></path>
                            </svg>
                            {{ course.duration }} мин
                        </div>
                        <div
                            v-if="course.rating_avg"
                            class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm
                                   border border-slate-600 dark:border-slate-400"
                             :title="t('rating')">
                            <svg
                                viewBox="0 0 24 24"
                                class="shrink-0 h-3 w-3">
                                <path class="fill-current text-red-400 dark:text-red-300"
                                      d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"></path>
                            </svg>
                            {{ Number(course.rating_avg).toFixed(1) }}
                        </div>
                    </div>

                    <!-- Инструктор -->
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

                        <div class="flex items-center justify-center gap-1
                                    min-w-0 text-xs font-semibold
                                    text-slate-700/85 dark:text-slate-300/85"
                             :title="`ID: ${course.instructorProfile.id}`">
                            {{ getInstructorName(course) }}
                            <svg class="shrink-0 h-4 w-4 text-violet-600/85 dark:text-violet-200/85"
                                 fill="currentColor"
                                 viewBox="0 0 640 512">
                                <path d="M622.34 153.2L343.4 67.5c-15.2-4.67-31.6-4.67-46.79 0L17.66 153.2c-23.54 7.23-23.54 38.36 0 45.59l48.63 14.94c-10.67 13.19-17.23 29.28-17.88 46.9C38.78 266.15 32 276.11 32 288c0 10.78 5.68 19.85 13.86 25.65L20.33 428.53C18.11 438.52 25.71 448 35.94 448h56.11c10.24 0 17.84-9.48 15.62-19.47L82.14 313.65C90.32 307.85 96 298.78 96 288c0-11.57-6.47-21.25-15.66-26.87.76-15.02 8.44-28.3 20.69-36.72L296.6 284.5c9.06 2.78 26.44 6.25 46.79 0l278.95-85.7c23.55-7.24 23.55-38.36 0-45.6zM352.79 315.09c-28.53 8.76-52.84 3.92-65.59 0l-145.02-44.55L128 384c0 35.35 85.96 64 192 64s192-28.65 192-64l-14.18-113.47-145.03 44.56z"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="mt-2 flex items-center justify-between gap-3">
                        <EntityStats
                            :views="course.views || 0"
                            :likes-count="course.likes_count || 0"
                            :already-liked="course.already_liked || false"
                            route-name="courses.like"
                            :route-params="{ course: course.id }"
                            :show-likes-button="true"
                            compact
                        />
                        <Link
                            :href="route('public.courses.show', { slug: course.slug })"
                            class="flex w-full items-center justify-center gap-2
                                   rounded-sm px-3 py-1 btn-default"
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
