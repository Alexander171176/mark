<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Создание попытки прохождения викторины
 */

import { computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import VueMultiselect from 'vue-multiselect'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import PrimaryButton from '@/Components/Admin/UI/Buttons/PrimaryButton.vue'
import LabelInput from '@/Components/Admin/UI/Input/LabelInput.vue'
import InputText from '@/Components/Admin/UI/Input/InputText.vue'
import InputNumber from '@/Components/Admin/UI/Input/InputNumber.vue'
import InputError from '@/Components/Admin/UI/Input/InputError.vue'

/* ==========================================================
 * I18N / TOAST
 * ========================================================== */

const { t } = useI18n()
const toast = useToast()

/* ==========================================================
 * PROPS
 * ========================================================== */

const props = defineProps({
    currentLocale: {
        type: String,
        default: '',
    },
    availableLocales: {
        type: Array,
        default: () => [],
    },

    users: {
        type: Array,
        default: () => [],
    },
    quizzes: {
        type: Array,
        default: () => [],
    },
    enrollments: {
        type: Array,
        default: () => [],
    },
    courses: {
        type: Array,
        default: () => [],
    },
    modules: {
        type: Array,
        default: () => [],
    },
    lessons: {
        type: Array,
        default: () => [],
    },

    defaultUserId: {
        type: Number,
        default: null,
    },
    defaultQuizId: {
        type: Number,
        default: null,
    },
    defaultEnrollmentId: {
        type: Number,
        default: null,
    },
})

/* ==========================================================
 * HELPERS
 * ========================================================== */

const dynamicOptionsLimit = (items) => {
    const list = Array.isArray(items) ? items : []
    return list.length + 10
}

const fromDatetimeLocal = (value) => {
    if (!value) return null

    const date = new Date(value)

    return Number.isFinite(date.getTime())
        ? date.toISOString()
        : null
}

const toNumberOrNull = (value) => {
    if (value === '' || value === null || typeof value === 'undefined') {
        return null
    }

    const number = Number(value)

    return Number.isFinite(number)
        ? number
        : null
}

const getTitle = (item) => {
    return item?.translation?.title
        || item?.translation?.name
        || item?.slug
        || null
}

const findById = (items, id) => {
    if (!id) return null

    return items.find(item => Number(item.id) === Number(id)) || null
}

/* ==========================================================
 * STATUS OPTIONS
 * ========================================================== */

const statusOptions = computed(() => ([
    {
        value: 'in_progress',
        label: t('setStatusInProgress'),
    },
    {
        value: 'completed',
        label: t('setStatusCompleted'),
    },
    {
        value: 'graded',
        label: t('setStatusGraded'),
    },
]))

/* ==========================================================
 * SELECT OPTIONS
 * ========================================================== */

/**
 * User не переводимый.
 */
const userOptions = computed(() =>
    (props.users || []).map(user => ({
        id: user.id,
        name: user.name,
        email: user.email,
        label: `[ID: ${user.id}] ${user.name || '—'}${user.email ? ` (${user.email})` : ''}`,
    }))
)

/**
 * Quiz.
 *
 * Controller загружает только currentLocale.
 */
const quizOptions = computed(() =>
    (props.quizzes || []).map(quiz => {
        const context = [
            getTitle(quiz.course)
                ? `Курс: ${getTitle(quiz.course)}`
                : null,

            getTitle(quiz.module)
                ? `Модуль: ${getTitle(quiz.module)}`
                : null,

            getTitle(quiz.lesson)
                ? `Урок: ${getTitle(quiz.lesson)}`
                : null,
        ].filter(Boolean).join(' / ')

        const title = getTitle(quiz) || `#${quiz.id}`

        return {
            id: quiz.id,
            school_course_id: quiz.school_course_id ?? quiz.course?.id ?? null,
            school_module_id: quiz.school_module_id ?? quiz.module?.id ?? null,
            school_lesson_id: quiz.school_lesson_id ?? quiz.lesson?.id ?? null,

            label: context
                ? `[ID: ${quiz.id}] ${title} — ${context}`
                : `[ID: ${quiz.id}] ${title}`,
        }
    })
)

/**
 * Course.
 */
const courseOptions = computed(() =>
    (props.courses || []).map(course => ({
        id: course.id,
        label: `[ID: ${course.id}] ${getTitle(course) || `#${course.id}`}`,
    }))
)

/**
 * Module.
 */
const moduleOptionsAll = computed(() =>
    (props.modules || []).map(module => ({
        id: module.id,
        school_course_id: module.school_course_id ?? module.course?.id ?? null,

        label: `[ID: ${module.id}] ${getTitle(module) || `#${module.id}`}`,
    }))
)

/**
 * Lesson.
 */
const lessonOptionsAll = computed(() =>
    (props.lessons || []).map(lesson => ({
        id: lesson.id,
        school_module_id: lesson.school_module_id ?? lesson.module?.id ?? null,

        label: `[ID: ${lesson.id}] ${getTitle(lesson) || `#${lesson.id}`}`,
    }))
)

/**
 * Enrollment сам не переводимый.
 * Его Course — currentLocale.
 */
const enrollmentOptionsAll = computed(() =>
    (props.enrollments || []).map(enrollment => {
        const userTitle = enrollment.user?.name
            ? `${enrollment.user.name}${enrollment.user.email ? ` (${enrollment.user.email})` : ''}`
            : `User ID: ${enrollment.user_id || '—'}`

        const courseTitle = getTitle(enrollment.course)
            || `Course ID: ${enrollment.school_course_id || '—'}`

        return {
            id: enrollment.id,
            user_id: enrollment.user_id ?? null,
            school_course_id: enrollment.school_course_id ?? enrollment.course?.id ?? null,

            label: `[ID: ${enrollment.id}] ${userTitle} — ${courseTitle}`,
        }
    })
)

/* ==========================================================
 * INITIAL CONTEXT
 * ========================================================== */

const initialQuiz = findById(
    quizOptions.value,
    props.defaultQuizId
)

const initialEnrollment = findById(
    enrollmentOptionsAll.value,
    props.defaultEnrollmentId
)

const initialCourseId =
    initialQuiz?.school_course_id
    ?? initialEnrollment?.school_course_id
    ?? null

const initialEnrollmentIsCompatible =
    !initialEnrollment
    || !initialCourseId
    || Number(initialEnrollment.school_course_id) === Number(initialCourseId)

/* ==========================================================
 * FORM
 * ========================================================== */

/**
 * Единственный источник состояния связей:
 *
 * form.user_id
 * form.school_quiz_id
 * form.school_enrollment_id
 * form.school_course_id
 * form.school_module_id
 * form.school_lesson_id
 */
const form = useForm({
    user_id:
        props.defaultUserId
        ?? initialEnrollment?.user_id
        ?? null,

    school_quiz_id:
        initialQuiz?.id
        ?? props.defaultQuizId
        ?? null,

    school_enrollment_id:
        initialEnrollmentIsCompatible
            ? initialEnrollment?.id ?? null
            : null,

    school_course_id:
    initialCourseId,

    school_module_id:
        initialQuiz?.school_module_id
        ?? null,

    school_lesson_id:
        initialQuiz?.school_lesson_id
        ?? null,

    attempt_number: '',
    status: 'in_progress',

    score: '',
    max_score: '',

    started_at: '',
    finished_at: '',
    duration_seconds: '',
})

/* ==========================================================
 * FILTERED OPTIONS
 * ========================================================== */

const moduleOptions = computed(() => {
    if (!form.school_course_id) {
        return moduleOptionsAll.value
    }

    return moduleOptionsAll.value.filter(module =>
        Number(module.school_course_id) === Number(form.school_course_id)
    )
})

const lessonOptions = computed(() => {
    if (!form.school_module_id) {
        return lessonOptionsAll.value
    }

    return lessonOptionsAll.value.filter(lesson =>
        Number(lesson.school_module_id) === Number(form.school_module_id)
    )
})

const enrollmentOptions = computed(() => {
    let list = enrollmentOptionsAll.value

    if (form.user_id) {
        list = list.filter(enrollment =>
            Number(enrollment.user_id) === Number(form.user_id)
        )
    }

    if (form.school_course_id) {
        list = list.filter(enrollment =>
            Number(enrollment.school_course_id) === Number(form.school_course_id)
        )
    }

    return list
})

/* ==========================================================
 * SELECTED USER
 * ========================================================== */

const selectedUser = computed({
    get: () => findById(userOptions.value, form.user_id),

    set: (user) => {
        form.user_id = user?.id ?? null

        if (!form.school_enrollment_id) return

        const enrollment = findById(
            enrollmentOptionsAll.value,
            form.school_enrollment_id
        )

        if (
            !user
            || (
                enrollment
                && Number(enrollment.user_id) !== Number(user.id)
            )
        ) {
            form.school_enrollment_id = null
        }
    },
})

/* ==========================================================
 * SELECTED QUIZ
 * ========================================================== */

const selectedQuiz = computed({
    get: () => findById(quizOptions.value, form.school_quiz_id),

    set: (quiz) => {
        form.school_quiz_id = quiz?.id ?? null

        if (!quiz) {
            form.school_course_id = null
            form.school_module_id = null
            form.school_lesson_id = null
            form.school_enrollment_id = null
            return
        }

        form.school_course_id = quiz.school_course_id ?? null
        form.school_module_id = quiz.school_module_id ?? null
        form.school_lesson_id = quiz.school_lesson_id ?? null

        if (!form.school_enrollment_id || !form.school_course_id) return

        const enrollment = findById(
            enrollmentOptionsAll.value,
            form.school_enrollment_id
        )

        if (
            enrollment
            && Number(enrollment.school_course_id) !== Number(form.school_course_id)
        ) {
            form.school_enrollment_id = null
        }
    },
})

/* ==========================================================
 * SELECTED COURSE
 * ========================================================== */

const selectedCourse = computed({
    get: () => findById(courseOptions.value, form.school_course_id),

    set: (course) => {
        form.school_course_id = course?.id ?? null

        if (!course) {
            form.school_module_id = null
            form.school_lesson_id = null
            form.school_enrollment_id = null
            return
        }

        if (form.school_module_id) {
            const module = findById(
                moduleOptionsAll.value,
                form.school_module_id
            )

            if (
                module
                && Number(module.school_course_id) !== Number(course.id)
            ) {
                form.school_module_id = null
                form.school_lesson_id = null
            }
        }

        if (form.school_enrollment_id) {
            const enrollment = findById(
                enrollmentOptionsAll.value,
                form.school_enrollment_id
            )

            if (
                enrollment
                && Number(enrollment.school_course_id) !== Number(course.id)
            ) {
                form.school_enrollment_id = null
            }
        }
    },
})

/* ==========================================================
 * SELECTED MODULE
 * ========================================================== */

const selectedModule = computed({
    get: () => findById(moduleOptionsAll.value, form.school_module_id),

    set: (module) => {
        form.school_module_id = module?.id ?? null

        if (!module) {
            form.school_lesson_id = null
            return
        }

        if (module.school_course_id) {
            form.school_course_id = module.school_course_id

            if (form.school_enrollment_id) {
                const enrollment = findById(
                    enrollmentOptionsAll.value,
                    form.school_enrollment_id
                )

                if (
                    enrollment
                    && Number(enrollment.school_course_id) !== Number(module.school_course_id)
                ) {
                    form.school_enrollment_id = null
                }
            }
        }

        if (form.school_lesson_id) {
            const lesson = findById(
                lessonOptionsAll.value,
                form.school_lesson_id
            )

            if (
                lesson
                && Number(lesson.school_module_id) !== Number(module.id)
            ) {
                form.school_lesson_id = null
            }
        }
    },
})

/* ==========================================================
 * SELECTED LESSON
 * ========================================================== */

const selectedLesson = computed({
    get: () => findById(lessonOptionsAll.value, form.school_lesson_id),

    set: (lesson) => {
        form.school_lesson_id = lesson?.id ?? null

        if (!lesson?.school_module_id) return

        const module = findById(
            moduleOptionsAll.value,
            lesson.school_module_id
        )

        if (!module) return

        form.school_module_id = module.id

        if (module.school_course_id) {
            form.school_course_id = module.school_course_id

            if (form.school_enrollment_id) {
                const enrollment = findById(
                    enrollmentOptionsAll.value,
                    form.school_enrollment_id
                )

                if (
                    enrollment
                    && Number(enrollment.school_course_id) !== Number(module.school_course_id)
                ) {
                    form.school_enrollment_id = null
                }
            }
        }
    },
})

/* ==========================================================
 * SELECTED ENROLLMENT
 * ========================================================== */

const selectedEnrollment = computed({
    get: () => findById(enrollmentOptionsAll.value, form.school_enrollment_id),

    set: (enrollment) => {
        form.school_enrollment_id = enrollment?.id ?? null

        if (!enrollment) return

        if (enrollment.user_id) {
            form.user_id = enrollment.user_id
        }

        if (!enrollment.school_course_id) return

        const courseChanged =
            form.school_course_id
            && Number(form.school_course_id) !== Number(enrollment.school_course_id)

        form.school_course_id = enrollment.school_course_id

        /**
         * Если Enrollment сменил Course,
         * старые Module/Lesson уже могут
         * не принадлежать новому Course.
         */
        if (courseChanged && form.school_module_id) {
            const module = findById(
                moduleOptionsAll.value,
                form.school_module_id
            )

            if (
                module
                && Number(module.school_course_id) !== Number(enrollment.school_course_id)
            ) {
                form.school_module_id = null
                form.school_lesson_id = null
            }
        }
    },
})

/* ==========================================================
 * SUBMIT
 * ========================================================== */

const submitForm = () => {
    form.transform(data => ({
        ...data,

        user_id: data.user_id ?? null,
        school_quiz_id: data.school_quiz_id ?? null,

        school_enrollment_id: data.school_enrollment_id ?? null,
        school_course_id: data.school_course_id ?? null,
        school_module_id: data.school_module_id ?? null,
        school_lesson_id: data.school_lesson_id ?? null,

        attempt_number: toNumberOrNull(data.attempt_number),
        score: toNumberOrNull(data.score),
        max_score: toNumberOrNull(data.max_score),

        started_at: fromDatetimeLocal(data.started_at),
        finished_at: fromDatetimeLocal(data.finished_at),
        duration_seconds: toNumberOrNull(data.duration_seconds),
    }))

    form.post(route('admin.schoolQuizAttempts.store'), {
        preserveScroll: true,

        onSuccess: () => {
            toast.success('Попытка квиза успешно создана.')
        },

        onError: (errors) => {
            const firstKey = Object.keys(errors || {})[0]

            toast.error(
                errors?.[firstKey]
                || 'Проверьте правильность заполнения полей.'
            )
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('createQuizAttempt')">
        <template #header>
            <TitlePage>{{ t('createQuizAttempt') }}</TitlePage>
        </template>

        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       shadow-lg shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <DefaultButton :href="route('admin.schoolQuizAttempts.index')">
                        <template #icon>
                            <svg
                                class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                                viewBox="0 0 16 16"
                            >
                                <path
                                    d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z"
                                />
                            </svg>
                        </template>

                        {{ t('back') }}
                    </DefaultButton>
                </div>

                <form @submit.prevent="submitForm" class="p-3 w-full space-y-4">

                    <!-- User / Quiz -->
                    <div class="flex flex-col items-start">
                        <LabelInput for="user">
                            <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                            {{ t('user') }}
                        </LabelInput>

                        <VueMultiselect
                            id="user"
                            v-model="selectedUser"
                            :options="userOptions"
                            :options-limit="dynamicOptionsLimit(userOptions)"
                            :multiple="false"
                            :close-on-select="true"
                            :clear-on-select="false"
                            :preserve-search="true"
                            :placeholder="t('select')"
                            label="label"
                            track-by="id"
                            class="w-full"
                        />

                        <InputError class="mt-2" :message="form.errors.user_id" />
                    </div>

                    <div class="flex flex-col items-start">
                        <LabelInput for="quiz">
                            <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                            {{ t('quiz') }}
                        </LabelInput>

                        <VueMultiselect
                            id="quiz"
                            v-model="selectedQuiz"
                            :options="quizOptions"
                            :options-limit="dynamicOptionsLimit(quizOptions)"
                            :multiple="false"
                            :close-on-select="true"
                            :clear-on-select="false"
                            :preserve-search="true"
                            :placeholder="t('select')"
                            label="label"
                            track-by="id"
                            class="w-full"
                        />

                        <InputError class="mt-2" :message="form.errors.school_quiz_id" />
                    </div>

                    <!-- Context -->
                    <div
                        class="p-3 border border-dashed border-slate-500 dark:border-slate-300
                               bg-white/60 dark:bg-slate-800/40"
                    >
                        <div class="text-center text-md font-semibold opacity-80 mb-3">
                            {{ t('context') }}
                        </div>

                        <div class="grid grid-cols-1 gap-4">
                            <div class="flex flex-col items-start">
                                <LabelInput for="lesson">
                                    {{ t('lesson') }}
                                </LabelInput>

                                <VueMultiselect
                                    id="lesson"
                                    v-model="selectedLesson"
                                    :options="lessonOptions"
                                    :options-limit="dynamicOptionsLimit(lessonOptions)"
                                    :multiple="false"
                                    :close-on-select="true"
                                    :clear-on-select="false"
                                    :preserve-search="true"
                                    :placeholder="t('select')"
                                    label="label"
                                    track-by="id"
                                    class="w-full"
                                />

                                <InputError class="mt-2" :message="form.errors.school_lesson_id" />
                            </div>

                            <div class="flex flex-col items-start">
                                <LabelInput for="module">
                                    {{ t('module') }} ({{ t('autoCorrect') }})
                                </LabelInput>

                                <VueMultiselect
                                    id="module"
                                    v-model="selectedModule"
                                    :options="moduleOptions"
                                    :options-limit="dynamicOptionsLimit(moduleOptions)"
                                    :multiple="false"
                                    :close-on-select="true"
                                    :clear-on-select="false"
                                    :preserve-search="true"
                                    :placeholder="t('select')"
                                    label="label"
                                    track-by="id"
                                    class="w-full"
                                />

                                <InputError class="mt-2" :message="form.errors.school_module_id" />
                            </div>

                            <div class="flex flex-col items-start">
                                <LabelInput for="course">
                                    {{ t('course') }} ({{ t('autoCorrect') }})
                                </LabelInput>

                                <VueMultiselect
                                    id="course"
                                    v-model="selectedCourse"
                                    :options="courseOptions"
                                    :options-limit="dynamicOptionsLimit(courseOptions)"
                                    :multiple="false"
                                    :close-on-select="true"
                                    :clear-on-select="false"
                                    :preserve-search="true"
                                    :placeholder="t('select')"
                                    label="label"
                                    track-by="id"
                                    class="w-full"
                                />

                                <InputError class="mt-2" :message="form.errors.school_course_id" />
                            </div>

                            <div class="flex flex-col items-start">
                                <LabelInput for="enrollment">
                                    {{ t('enrollment') }}
                                </LabelInput>

                                <VueMultiselect
                                    id="enrollment"
                                    v-model="selectedEnrollment"
                                    :options="enrollmentOptions"
                                    :options-limit="dynamicOptionsLimit(enrollmentOptions)"
                                    :multiple="false"
                                    :close-on-select="true"
                                    :clear-on-select="false"
                                    :preserve-search="true"
                                    :placeholder="t('select')"
                                    label="label"
                                    track-by="id"
                                    class="w-full"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="form.errors.school_enrollment_id"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Time -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                        <div class="flex flex-col items-start">
                            <LabelInput for="duration_seconds">
                                {{ t('duration') }}
                            </LabelInput>

                            <InputNumber
                                id="duration_seconds"
                                type="number"
                                min="0"
                                v-model="form.duration_seconds"
                                class="w-full"
                            />

                            <InputError class="mt-2" :message="form.errors.duration_seconds" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="started_at">
                                {{ t('shortStarted') }}
                            </LabelInput>

                            <InputText
                                id="started_at"
                                type="datetime-local"
                                v-model="form.started_at"
                                class="w-full"
                            />

                            <InputError class="mt-2" :message="form.errors.started_at" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="finished_at">
                                {{ t('shortExpires') }}
                            </LabelInput>

                            <InputText
                                id="finished_at"
                                type="datetime-local"
                                v-model="form.finished_at"
                                class="w-full"
                            />

                            <InputError class="mt-2" :message="form.errors.finished_at" />
                        </div>
                    </div>

                    <!-- Score / Status -->
                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                        <div class="flex flex-col items-start">
                            <LabelInput for="attempt_number">
                                {{ t('attemptNumber') }}
                            </LabelInput>

                            <InputNumber
                                id="attempt_number"
                                type="number"
                                min="1"
                                v-model="form.attempt_number"
                                class="w-full"
                            />

                            <InputError class="mt-2" :message="form.errors.attempt_number" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="score">
                                {{ t('score') }}
                            </LabelInput>

                            <InputNumber
                                id="score"
                                type="number"
                                min="0"
                                v-model="form.score"
                                class="w-full"
                            />

                            <InputError class="mt-2" :message="form.errors.score" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="max_score">
                                {{ t('maxScore') }}
                            </LabelInput>

                            <InputNumber
                                id="max_score"
                                type="number"
                                min="0"
                                v-model="form.max_score"
                                class="w-full"
                            />

                            <InputError class="mt-2" :message="form.errors.max_score" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="status">
                                {{ t('status') }}
                            </LabelInput>

                            <select
                                id="status"
                                v-model="form.status"
                                class="w-full py-0.5 font-semibold text-sm
                                       border border-slate-500 rounded-sm shadow-sm
                                       focus:border-indigo-500 focus:ring-indigo-300
                                       dark:bg-cyan-800 dark:text-slate-100"
                            >
                                <option
                                    v-for="option in statusOptions"
                                    :key="option.value"
                                    :value="option.value"
                                >
                                    {{ option.label }}
                                </option>
                            </select>

                            <InputError class="mt-2" :message="form.errors.status" />
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center justify-center gap-3">
                        <DefaultButton :href="route('admin.schoolQuizAttempts.index')">
                            <template #icon>
                                <svg
                                    class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                                    viewBox="0 0 16 16"
                                >
                                    <path
                                        d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z"
                                    />
                                </svg>
                            </template>

                            {{ t('back') }}
                        </DefaultButton>

                        <PrimaryButton
                            class="mb-0"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            {{ t('save') }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<style src="/resources/css/vue-multiselect.min.css"></style>
