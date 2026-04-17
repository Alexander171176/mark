<script setup>
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { computed } from 'vue'
import UniversalImageSlider from '@/Components/Public/Default/Images/UniversalImageSlider.vue'

const { t } = useI18n()

/** пропсы */
const props = defineProps({
    assignments: {
        type: Array,
        default: () => [],
    },
    cols: {
        type: Number,
        default: 3,
    },
})

/** показ третьей карточки в ряд, если свёрнута колонка */
const gridClass = computed(() => {
    return props.cols === 2
        ? 'grid-cols-1 sm:grid-cols-2'
        : 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3'
})

/** Нормализация ссылки задания */
const assignmentLink = (assignment) => {
    return route('public.assignments.show', { slug: assignment.slug })
}

/** показ курса */
const getCourse = (assignment) => assignment?.course || null

/** показ модуля */
const getModule = (assignment) => assignment?.module || null

/** показ урока */
const getLesson = (assignment) => assignment?.lesson || null

/** показ инструктора */
const getInstructor = (assignment) => assignment?.instructor || null

/** Нормализация ссылки курса */
const courseLink = (assignment) => {
    const course = getCourse(assignment)
    return course?.slug ? route('public.courses.show', { slug: course.slug }) : '#'
}

/** Нормализация ссылки модуля */
const moduleLink = (assignment) => {
    const module = getModule(assignment)
    return module?.slug ? route('public.modules.show', { slug: module.slug }) : '#'
}

/** Нормализация ссылки урока */
const lessonLink = (assignment) => {
    const lesson = getLesson(assignment)
    return lesson?.slug ? route('public.lessons.show', { slug: lesson.slug }) : '#'
}

/** Нормализация названий курса, модуля, урока, инструктора */
const getCourseTitle = (assignment) => getCourse(assignment)?.title || t('course')
const getModuleTitle = (assignment) => getModule(assignment)?.title || t('module')
const getLessonTitle = (assignment) => getLesson(assignment)?.title || t('lesson')
const getInstructorName = (assignment) => {
    const instructor = getInstructor(assignment)
    return instructor?.user?.name || instructor?.title || t('instructor')
}

/** формат даты дедлайна */
const formatDueDate = (value) => {
    if (!value) return null

    const date = new Date(value)
    if (Number.isNaN(date.getTime())) return value

    return new Intl.DateTimeFormat('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(date)
}

/** формат даты публикации задания */
const formatPublishedAt = (value) => {
    if (!value) return null

    const date = new Date(value)
    if (Number.isNaN(date.getTime())) return value

    return new Intl.DateTimeFormat('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(date)
}

/** маппинг перевода типа оценки */
const translateGradingType = (value) => {
    const map = {
        manual: t('gradingManual'),
        auto: t('gradingAuto'),
    }

    return map[value] || value || '—'
}

/** маппинг перевода видимости */
const translateVisibility = (value) => {
    const map = {
        public: t('public'),
        enrolled: t('enrolled'),
        private: t('private'),
    }

    return map[value] || value || '—'
}

/** маппинг перевода статуса */
const translateStatus = (value) => {
    const map = {
        draft: t('statusDraft'),
        published: t('statusPublished'),
        archived: t('statusArchived'),
    }

    return map[value] || value || '—'
}
</script>

<template>
    <div class="grid gap-4" :class="gridClass">
        <div
            v-for="assignment in assignments"
            :key="assignment.id"
            class="group flex h-full flex-col overflow-hidden
                   rounded-md border border-gray-200
                   bg-white shadow-sm transition
                   hover:-translate-y-0.5 hover:shadow-md
                   dark:border-gray-700 dark:bg-gray-900"
        >
            <!-- Images || Slider -->
            <Link :href="assignmentLink(assignment)">
                <UniversalImageSlider
                    :entity="assignment"
                    height-class="h-48"
                    rounded-class=""
                    wrapper-class=""
                    img-class="w-full h-full object-cover
                               transition duration-300 group-hover:scale-105"
                />
            </Link>

            <div class="flex flex-1 flex-col p-4">

                <!-- Title -->
                <div class="flex items-center justify-center gap-3">
                    <Link
                        :href="assignmentLink(assignment)"
                        class="inline-flex items-center gap-2"
                    >
                        <span
                            class="text-base font-semibold
                                   text-slate-900/85 group-hover:opacity-95
                                   dark:text-slate-100/85 dark:group-hover:opacity-95"
                        >
                            {{ assignment.title }}
                        </span>
                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                            #{{ assignment.id }}
                        </span>
                    </Link>
                </div>

                <!-- Краткое описание -->
                <div
                    v-if="assignment.short"
                    class="mt-3 line-clamp-3 text-sm text-slate-800 dark:text-slate-200"
                >
                    {{ assignment.short }}
                </div>

                <!-- Данные -->
                <div
                    class="mt-3 flex flex-wrap items-center justify-center gap-2
                           text-xs font-semibold text-slate-500 dark:text-slate-400">

                    <!-- Инструктор -->
                    <div
                        v-if="getInstructor(assignment)"
                        class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm
                               border border-slate-600 dark:border-slate-400"
                    >
                        <div
                            class="flex items-center justify-center gap-1 min-w-0
                               text-xs font-semibold text-slate-700/85 dark:text-slate-300/85"
                            :title="getInstructorName(assignment)"
                        >
                            <svg class="shrink-0 h-4 w-4 text-violet-600/85 dark:text-violet-200/85"
                                 fill="currentColor" viewBox="0 0 640 512">
                                <path
                                    d="M622.34 153.2L343.4 67.5c-15.2-4.67-31.6-4.67-46.79 0L17.66 153.2c-23.54 7.23-23.54 38.36 0 45.59l48.63 14.94c-10.67 13.19-17.23 29.28-17.88 46.9C38.78 266.15 32 276.11 32 288c0 10.78 5.68 19.85 13.86 25.65L20.33 428.53C18.11 438.52 25.71 448 35.94 448h56.11c10.24 0 17.84-9.48 15.62-19.47L82.14 313.65C90.32 307.85 96 298.78 96 288c0-11.57-6.47-21.25-15.66-26.87.76-15.02 8.44-28.3 20.69-36.72L296.6 284.5c9.06 2.78 26.44 6.25 46.79 0l278.95-85.7c23.55-7.24 23.55-38.36 0-45.6zM352.79 315.09c-28.53 8.76-52.84 3.92-65.59 0l-145.02-44.55L128 384c0 35.35 85.96 64 192 64s192-28.65 192-64l-14.18-113.47-145.03 44.56z"></path>
                            </svg>
                            <div class="text-slate-500 dark:text-slate-400">
                                {{ t('instructor') }}:
                            </div>
                            <div class="ml-1">
                                {{ getInstructorName(assignment) }}
                            </div>
                        </div>
                    </div>

                    <!-- Курс -->
                    <div
                        v-if="getCourse(assignment)"
                        class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm
                               border border-slate-600 dark:border-slate-400"
                    >
                        <div
                            class="flex items-center justify-center gap-1 min-w-0
                                   text-xs font-semibold text-slate-700/85 dark:text-slate-300/85"
                            :title="getCourseTitle(assignment)"
                        >
                            <svg class="shrink-0 h-3 w-3 text-sky-600/85 dark:text-sky-200/85"
                                 fill="currentColor" viewBox="0 0 448 512">
                                <path d="M318.38 208h-39.09c-1.49 27.03-6.54 51.35-14.21 70.41 27.71-13.24 48.02-39.19 53.3-70.41zm0-32c-5.29-31.22-25.59-57.17-53.3-70.41 7.68 19.06 12.72 43.38 14.21 70.41h39.09zM224 97.31c-7.69 7.45-20.77 34.42-23.43 78.69h46.87c-2.67-44.26-15.75-71.24-23.44-78.69zm-41.08 8.28c-27.71 13.24-48.02 39.19-53.3 70.41h39.09c1.49-27.03 6.53-51.35 14.21-70.41zm0 172.82c-7.68-19.06-12.72-43.38-14.21-70.41h-39.09c5.28 31.22 25.59 57.17 53.3 70.41zM247.43 208h-46.87c2.66 44.26 15.74 71.24 23.43 78.69 7.7-7.45 20.78-34.43 23.44-78.69zM448 358.4V25.6c0-16-9.6-25.6-25.6-25.6H96C41.6 0 0 41.6 0 96v320c0 54.4 41.6 96 96 96h326.4c12.8 0 25.6-9.6 25.6-25.6v-16c0-6.4-3.2-12.8-9.6-19.2-3.2-16-3.2-60.8 0-73.6 6.4-3.2 9.6-9.6 9.6-19.2zM224 64c70.69 0 128 57.31 128 128s-57.31 128-128 128S96 262.69 96 192 153.31 64 224 64zm160 384H96c-19.2 0-32-12.8-32-32s16-32 32-32h288v64z"></path>
                            </svg>
                            <div class="text-slate-500 dark:text-slate-400">{{ t('course') }}:</div>
                            <Link
                                :href="courseLink(assignment)"
                                class="ml-1 hover:text-blue-600 dark:hover:text-blue-400"
                            >
                                {{ getCourseTitle(assignment) }}
                            </Link>
                        </div>
                    </div>

                    <!-- Модуль -->
                    <div
                        v-if="getModule(assignment)"
                        class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm
                               border border-slate-600 dark:border-slate-400"
                    >
                        <div
                            class="flex items-center justify-center gap-1 min-w-0
                                   text-xs font-semibold text-slate-700/85 dark:text-slate-300/85"
                            :title="getModuleTitle(assignment)"
                        >
                            <svg class="shrink-0 h-3 w-3 text-teal-600/85 dark:text-teal-200/85"
                                 fill="currentColor" viewBox="0 0 24 24">
                                <rect x="1" y="1" width="10" height="10" rx="2"></rect>
                                <path class="fill-current text-teal-400" d="M23.428,4.618,19.381.572h0a1.957,1.957,0,0,0-2.762,0L12.572,4.618a1.959,1.959,0,0,0,0,2.764l4.047,4.047a1.957,1.957,0,0,0,2.762,0l4.047-4.046A1.959,1.959,0,0,0,23.428,4.618Z"></path>
                                <rect x="13" y="13" width="10" height="10" rx="2"></rect>
                                <rect x="1" y="13" width="10" height="10" rx="2"></rect>
                            </svg>
                            <div class="text-slate-500 dark:text-slate-400">{{ t('module') }}:</div>
                            <Link
                                :href="moduleLink(assignment)"
                                class="ml-1 hover:text-blue-600 dark:hover:text-blue-400"
                            >
                                {{ getModuleTitle(assignment) }}
                            </Link>
                        </div>
                    </div>

                    <!-- Урок -->
                    <div
                        v-if="getLesson(assignment)"
                        class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm
                               border border-slate-600 dark:border-slate-400"
                    >
                        <div
                            class="flex items-center justify-center gap-1 min-w-0
                                   text-xs font-semibold text-slate-700/85 dark:text-slate-300/85"
                            :title="getLessonTitle(assignment)"
                        >
                            <svg class="h-4 w-4 text-sky-600/85 dark:text-sky-300/85"
                                 fill="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                            </svg>
                            <div class="text-slate-500 dark:text-slate-400">{{ t('lesson') }}:</div>
                            <Link
                                :href="lessonLink(assignment)"
                                class="ml-1 hover:text-blue-600 dark:hover:text-blue-400"
                            >
                                {{ getLessonTitle(assignment) }}
                            </Link>
                        </div>
                    </div>

                    <!-- Статус -->
                    <div
                        v-if="assignment.status"
                        class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm
                                   border border-slate-600 dark:border-slate-400"
                        :title="t('status')">
                        <svg class="w-3 h-3 text-violet-600 dark:text-violet-300"
                             fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2a10 10 0 1 0 10 10A10.01 10.01 0 0 0 12 2Zm1 15h-2v-2h2Zm0-4h-2V7h2Z" />
                        </svg>
                        {{ translateStatus(assignment.status) }}
                    </div>

                    <!-- Дата публикации -->
                    <span
                        v-if="assignment.published_at"
                        class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm
                                   border border-slate-600 dark:border-slate-400"
                    >
                        <svg class="h-3 w-3 text-slate-600 dark:text-slate-300"
                             fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M19 4h-1V2h-2v2H8V2H6v2H5a2 2 0 0 0-2 2v13a3 3 0 0 0 3 3h12a3 3 0 0 0 3-3V6a2 2 0 0 0-2-2Zm0 15a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V10h14Z" />
                        </svg>
                        {{ t('publishedAt') }}:
                        {{ formatPublishedAt(assignment.published_at) }}
                    </span>

                    <!-- Тип оценки -->
                    <div
                        v-if="assignment.grading_type"
                        class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm
                                   border border-slate-600 dark:border-slate-400"
                        :title="t('gradingType')">
                        <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-300"
                             fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 16.17 4.83 12 3.41 13.41 9 19l12-12-1.41-1.41z" />
                        </svg>
                        {{ translateGradingType(assignment.grading_type) }}
                    </div>

                    <!-- Средняя оценка -->
                    <div
                        v-if="assignment.max_score"
                        class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm
                                   border border-slate-600 dark:border-slate-400"
                        :title="t('maxScore')"
                    >
                        <svg class="w-3 h-3 text-amber-600 dark:text-amber-300"
                             fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2 14.85 8.15 21.5 9.27l-4.75 4.63 1.12 6.6L12 17.27 6.13 20.5l1.12-6.6L2.5 9.27l6.65-1.12L12 2Z" />
                        </svg>
                        {{ assignment.max_score }}
                    </div>

                    <!-- Количество попыток -->
                    <div
                        v-if="assignment.attempts_limit !== null &&
                        assignment.attempts_limit !== undefined"
                        class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm
                                   border border-slate-600 dark:border-slate-400"
                        :title="t('attemptsLimit')"
                    >
                        <svg class="w-3 h-3 text-blue-600 dark:text-blue-300"
                             fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2a10 10 0 1 0 10 10A10.01 10.01 0 0 0 12 2Zm1 5h-2v6l5 3 1-1.73-4-2.27Z" />
                        </svg>
                        {{ assignment.attempts_limit === 0 ? t('no') : assignment.attempts_limit }}
                    </div>

                    <!-- Видимость -->
                    <div
                        v-if="assignment.visibility"
                        class="flex items-center justify-center gap-1 px-2 py-1 rounded-sm
                                   border border-slate-600 dark:border-slate-400"
                        :title="t('visibility')">
                        <svg class="w-3 h-3 text-sky-600 dark:text-sky-300"
                             fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7Zm0 11a4 4 0 1 1 4-4 4 4 0 0 1-4 4Z" />
                        </svg>
                        {{ translateVisibility(assignment.visibility) }}
                    </div>

                </div>

                <!-- Дедлайн -->
                <div
                    v-if="assignment.due_at"
                    class="mt-2 flex items-center justify-center gap-2
                           text-xs font-semibold text-rose-600 dark:text-rose-300"
                >
                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 4h-1V2h-2v2H8V2H6v2H5a2 2 0 0 0-2 2v13a3 3 0 0 0 3 3h12a3 3 0 0 0 3-3V6a2 2 0 0 0-2-2Zm0 15a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V10h14Z"/>
                    </svg>
                    {{ t('dueAt') }}: {{ formatDueDate(assignment.due_at) }}
                </div>

                <div class="mt-auto pt-4">
                    <Link
                        :href="assignmentLink(assignment)"
                        class="flex w-full items-center justify-center gap-2 rounded-sm px-3 py-2 btn-default"
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
