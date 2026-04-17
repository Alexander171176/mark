<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import UniversalImageSlider from '@/Components/Public/Default/Images/UniversalImageSlider.vue'
import EntityStats from '@/Components/Public/Default/Stats/EntityStats.vue'

const { t } = useI18n()

const props = defineProps({
    lessons: { type: Array, default: () => [] },
    cols: { type: Number, default: 2 },
})

const gridClass = computed(() => {
    return props.cols === 2
        ? 'grid-cols-1 sm:grid-cols-2'
        : 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3'
})

const lessonLink = (lesson) => {
    return lesson?.slug
        ? route('public.lessons.show', { slug: lesson.slug })
        : '#'
}

const shortText = (lesson) => {
    return lesson?.short || lesson?.subtitle || ''
}

// --- Мапы "значение из БД" → "ключ перевода" ---
const lessonStatusLabelKeyMap = {
    draft: 'statusDraft',
    published: 'statusPublished',
    archived: 'statusArchived'
}

const lessonAvailabilityLabelKeyMap = {
    unlisted: 'availabilityUnlisted',
    public: 'availabilityPublic',
    private: 'availabilityPrivate'
}

const lessonAccessTypeLabelKeyMap = {
    free: 'free',
    paid: 'paid',
    bonus: 'bonus'
}

const getLessonStatusLabel = (status) => {
    if (!status) return ''
    const key = lessonStatusLabelKeyMap[status]
    return key ? t(key) : status
}

const getLessonAvailabilityLabel = (availability) => {
    if (!availability) return ''
    const key = lessonAvailabilityLabelKeyMap[availability]
    return key ? t(key) : availability
}

const getLessonAccessTypeLabel = (accessType) => {
    if (!accessType) return ''
    const key = lessonAccessTypeLabelKeyMap[accessType]
    return key ? t(key) : accessType
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
                    img-class="w-full h-full object-cover transition duration-300 group-hover:scale-105"
                    :autoplay="true"
                    :interval="4000"
                />
            </Link>

            <div class="flex flex-1 flex-col p-4">

                <!-- Заголовок и просмотры -->
                <Link
                    :href="lessonLink(lesson)"
                    class="inline-flex items-center justify-center gap-2"
                >
                        <span
                            class="text-base font-semibold
                                   text-slate-900/85 group-hover:opacity-75
                                   dark:text-slate-100/85 dark:group-hover:opacity-75"
                        >
                            {{ lesson.title }}
                        </span>
                </Link>

                <!-- Краткое описание -->
                <div
                    v-if="shortText(lesson)"
                    class="mt-3 line-clamp-3 text-sm text-slate-700 dark:text-slate-300"
                >
                    {{ shortText(lesson) }}
                </div>

                <!-- Данные -->
                <div
                    class="mt-3 flex flex-wrap items-center justify-center gap-2
                           text-xs font-semibold text-slate-500 dark:text-slate-400">
                    <div
                        v-if="lesson.status"
                        class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm
                               border border-slate-600 dark:border-slate-400"
                    >
                        <svg class="h-3 w-3 fill-current text-cyan-700 dark:text-blue-300"
                             viewBox="0 0 24 24">
                            <path d="M22.641,4.232c-.427-.354-1.056-.296-1.409,.128L11.933,15.519l-.433-.433-1.286,1.543,1.078,1.078c.188,.188,.442,.293,.707,.293,.015,0,.03,0,.045,0,.281-.013,.543-.143,.724-.359L22.769,5.64c.354-.424,.296-1.055-.128-1.408Z"></path>
                            <path d="M6,18c-.265,0-.52-.105-.707-.293L1.293,13.707c-.391-.391-.391-1.023,0-1.414s1.023-.391,1.414,0l3.226,3.226L15.231,4.36c.354-.424,.983-.481,1.409-.128,.424,.354,.481,.984,.128,1.408L6.769,17.64c-.181,.216-.442,.346-.724,.359-.015,0-.03,0-.045,0Z"></path>
                        </svg>
                        {{ getLessonStatusLabel(lesson.status) }}
                    </div>

                    <div
                        v-if="lesson.availability"
                        class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm
                               border border-slate-600 dark:border-slate-400"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-3 h-3 fill-current text-blue-700 dark:text-blue-300"
                             viewBox="0 0 24 24">
                            <path d="M22.707,6.707,17.293,1.293A1,1,0,0,0,16.586,1H4A3,3,0,0,0,1,4V20a3,3,0,0,0,3,3H20a3,3,0,0,0,3-3V7.414A1,1,0,0,0,22.707,6.707ZM14.5,4h1a.5.5,0,0,1,.5.5v4a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-4A.5.5,0,0,1,14.5,4ZM19,12.5v6a.5.5,0,0,1-.5.5H5.5a.5.5,0,0,1-.5-.5v-6a.5.5,0,0,1,.5-.5h13A.5.5,0,0,1,19,12.5Z"></path>
                        </svg>
                        {{ getLessonAvailabilityLabel(lesson.availability) }}
                    </div>

                    <div
                        v-if="lesson.access_type"
                        class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm
                               border border-slate-600 dark:border-slate-400"
                    >
                        <svg
                            class="h-3 w-3 text-blue-600 dark:text-blue-400"
                            fill="currentColor"
                            viewBox="0 0 576 512">
                            <path d="M423.5 0C339.5.3 272 69.5 272 153.5V224H48c-26.5 0-48 21.5-48 48v192c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V272c0-26.5-21.5-48-48-48h-48v-71.1c0-39.6 31.7-72.5 71.3-72.9 40-.4 72.7 32.1 72.7 72v80c0 13.3 10.7 24 24 24h32c13.3 0 24-10.7 24-24v-80C576 68 507.5-.3 423.5 0z"/>
                        </svg>
                        {{ getLessonAccessTypeLabel(lesson.access_type) }}
                    </div>

                    <div
                        v-if="lesson.difficulty !== null && lesson.difficulty !== undefined"
                        class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm
                               border border-slate-600 dark:border-slate-400"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-3 h-3 fill-current text-amber-600 dark:text-amber-300"
                             viewBox="0 0 24 24">
                            <path d="M12,24a1,1,0,0,1,0-2A10,10,0,0,0,12,2a1,1,0,0,1,0-2,12,12,0,0,1,0,24Z"></path><path class="fill-current text-teal-600 dark:text-teal-300" d="M1.045,13.913a1,1,0,0,1-1-.919C.022,12.665,0,12.336,0,12s.022-.665.049-.994a1,1,0,1,1,1.993.162C2.021,11.442,2,11.719,2,12s.021.558.042.832a1,1,0,0,1-.916,1.078Q1.086,13.913,1.045,13.913Z"></path><path class="fill-current text-teal-600 dark:text-teal-300" d="M6.243,3.641a1,1,0,0,1-.526-1.852,12.022,12.022,0,0,1,1.774-.9,1,1,0,1,1,.754,1.851,10.133,10.133,0,0,0-1.478.757A.993.993,0,0,1,6.243,3.641Z"></path><path class="fill-current text-teal-600 dark:text-teal-300" d="M2.188,8.044a.988.988,0,0,1-.451-.108A1,1,0,0,1,1.3,6.592,12.131,12.131,0,0,1,2.342,4.9,1,1,0,0,1,3.953,6.083,10.1,10.1,0,0,0,3.081,7.5,1,1,0,0,1,2.188,8.044Z"></path><path class="fill-current text-teal-600 dark:text-teal-300" d="M3.128,19.482a1,1,0,0,1-.808-.409,12.049,12.049,0,0,1-1.041-1.7,1,1,0,1,1,1.787-.9,10.047,10.047,0,0,0,.868,1.418,1,1,0,0,1-.217,1.4A.986.986,0,0,1,3.128,19.482Z"></path><path d="M7.853,23.185a.983.983,0,0,1-.377-.075A11.879,11.879,0,0,1,5.7,22.2,1,1,0,0,1,6.75,20.5a10.041,10.041,0,0,0,1.48.761,1,1,0,0,1-.377,1.926Z"></path><path d="M10,17a1,1,0,0,1-.707-.293l-4-4a1,1,0,0,1,1.414-1.414L10,14.586l7.293-7.293a1,1,0,1,1,1.414,1.414l-8,8A1,1,0,0,1,10,17Z"></path>
                        </svg>
                        {{ t('difficulty') }}: {{ lesson.difficulty }}
                    </div>

                    <div
                        v-if="lesson.duration"
                        class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm
                               border border-slate-600 dark:border-slate-400"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-3 h-3 fill-current text-blue-700 dark:text-blue-300"
                             viewBox="0 0 24 24">
                            <path d="M22,13a1,1,0,0,1,0-2h1.949A12.006,12.006,0,0,0,13,.051V2a1,1,0,0,1-2,0V.051A12.006,12.006,0,0,0,.051,11H2a1,1,0,0,1,0,2H.051A12.006,12.006,0,0,0,11,23.949V22a1,1,0,0,1,2,0v1.949A12.006,12.006,0,0,0,23.949,13Zm-6,0H12a1,1,0,0,1-.832-.445l-4-6a1,1,0,1,1,1.664-1.11L12.535,11H16a1,1,0,0,1,0,2Z"></path>
                        </svg>
                        {{ t('duration') }}: {{ lesson.duration }} {{ t('minutes') }}
                    </div>

                    <div
                        v-if="lesson.rating_avg"
                        class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm
                               border border-slate-600 dark:border-slate-400"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg"
                             viewBox="0 0 24 24"
                             class="h-3 w-3 fill-current text-red-400 dark:text-red-300">
                            <path d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"></path>
                        </svg>
                        {{ t('rating') }}: {{ Number(lesson.rating_avg).toFixed(1) }}
                    </div>

                    <div
                        v-if="lesson.rating_count"
                        class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm
                               border border-slate-600 dark:border-slate-400"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg"
                             viewBox="0 0 512 512"
                             class="h-3 w-3 fill-current text-green-600 dark:text-green-400">
                            <path
                                d="M448 0H64C28.7 0 0 28.7 0 64v288c0 35.3 28.7 64 64 64h96v84c0 9.8 11.2 15.5 19.1 9.7L304 416h144c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64z"/>
                        </svg>
                        {{ t('reviews') }}: {{ lesson.rating_count }}
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
