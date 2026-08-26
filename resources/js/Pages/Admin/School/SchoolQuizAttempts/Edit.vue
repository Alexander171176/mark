<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Редактирование попытки прохождения викторины
 */

import { computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'

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

    attempt: {
        type: Object,
        required: true,
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
})

/* ==========================================================
 * HELPERS
 * ========================================================== */

const normalizeDateTimeLocal = (value) => {
    if (!value) return ''

    const string = String(value)
    const match = string.match(/^(\d{4}-\d{2}-\d{2}T\d{2}:\d{2})/)

    return match
        ? match[1]
        : string
}

const fromDatetimeLocal = (value) => {
    if (!value) return null

    const date = new Date(value)

    return Number.isFinite(date.getTime())
        ? date.toISOString()
        : null
}

const normalizeToEmptyString = (value) => {
    return value === null || typeof value === 'undefined'
        ? ''
        : value
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
 * READONLY CONTEXT
 * ========================================================== */

/**
 * User не переводимый.
 */
const userLabel = computed(() => {
    const user = props.attempt?.user

    if (user?.id) {
        return `[ID: ${user.id}] ${user.name || '—'}${user.email ? ` (${user.email})` : ''}`
    }

    return props.attempt?.user_id
        ? `[ID: ${props.attempt.user_id}]`
        : '—'
})

/**
 * Quiz currentLocale.
 */
const quizLabel = computed(() => {
    const quiz = props.attempt?.quiz

    if (quiz?.id) {
        return `[ID: ${quiz.id}] ${getTitle(quiz) || '—'}`
    }

    return props.attempt?.school_quiz_id
        ? `[ID: ${props.attempt.school_quiz_id}]`
        : '—'
})

/**
 * Enrollment сам не переводимый,
 * Course внутри него — currentLocale.
 */
const enrollmentLabel = computed(() => {
    const enrollment = props.attempt?.enrollment

    if (enrollment?.id) {
        const userPart = enrollment.user?.name
            ? ` — ${enrollment.user.name}`
            : ''

        const courseTitle = getTitle(enrollment.course)

        const coursePart = courseTitle
            ? ` / ${courseTitle}`
            : ''

        return `[ID: ${enrollment.id}]${userPart}${coursePart}`
    }

    return props.attempt?.school_enrollment_id
        ? `[ID: ${props.attempt.school_enrollment_id}]`
        : '—'
})

const courseLabel = computed(() => {
    const course = props.attempt?.course

    if (course?.id) {
        return `[ID: ${course.id}] ${getTitle(course) || '—'}`
    }

    return props.attempt?.school_course_id
        ? `[ID: ${props.attempt.school_course_id}]`
        : '—'
})

const moduleLabel = computed(() => {
    const module = props.attempt?.module

    if (module?.id) {
        return `[ID: ${module.id}] ${getTitle(module) || '—'}`
    }

    return props.attempt?.school_module_id
        ? `[ID: ${props.attempt.school_module_id}]`
        : '—'
})

const lessonLabel = computed(() => {
    const lesson = props.attempt?.lesson

    if (lesson?.id) {
        return `[ID: ${lesson.id}] ${getTitle(lesson) || '—'}`
    }

    return props.attempt?.school_lesson_id
        ? `[ID: ${props.attempt.school_lesson_id}]`
        : '—'
})

const ipLabel = computed(() =>
    props.attempt?.ip_address || '—'
)

const userAgentLabel = computed(() =>
    props.attempt?.user_agent || '—'
)

const percentLabel = computed(() => {
    if (
        props.attempt?.percent === null
        || typeof props.attempt?.percent === 'undefined'
    ) {
        return '—'
    }

    return `${props.attempt.percent}%`
})

/* ==========================================================
 * FORM
 * ========================================================== */

const form = useForm({
    _method: 'PUT',

    status:
        props.attempt?.status
        ?? 'in_progress',

    score:
        normalizeToEmptyString(
            props.attempt?.score
        ),

    max_score:
        normalizeToEmptyString(
            props.attempt?.max_score
        ),

    started_at:
        normalizeDateTimeLocal(
            props.attempt?.started_at
        ),

    finished_at:
        normalizeDateTimeLocal(
            props.attempt?.finished_at
        ),

    duration_seconds:
        normalizeToEmptyString(
            props.attempt?.duration_seconds
        ),
})

/* ==========================================================
 * SUBMIT
 * ========================================================== */

const submitForm = () => {
    form.transform(data => {
        const payload = {
            _method: 'PUT',

            status: data.status,

            score:
                toNumberOrNull(
                    data.score
                ),

            max_score:
                toNumberOrNull(
                    data.max_score
                ),

            started_at:
                fromDatetimeLocal(
                    data.started_at
                ),

            finished_at:
                fromDatetimeLocal(
                    data.finished_at
                ),

            duration_seconds:
                toNumberOrNull(
                    data.duration_seconds
                ),
        }

        Object.keys(payload).forEach(key => {
            if (payload[key] === null) {
                delete payload[key]
            }
        })

        return payload
    })

    form.post(
        route('admin.schoolQuizAttempts.update', {
            schoolQuizAttempt: props.attempt.id,
        }),
        {
            preserveScroll: true,

            onSuccess: () => {
                toast.success(
                    'Попытка квиза успешно обновлена.'
                )
            },

            onError: (errors) => {
                const firstKey = Object.keys(errors || {})[0]

                toast.error(
                    errors?.[firstKey]
                    || 'Проверьте правильность заполнения полей.'
                )
            },
        }
    )
}
</script>

<template>
    <AdminLayout :title="t('editQuizAttempt')">
        <template #header>
            <TitlePage>
                {{ t('editQuizAttempt') }} [ID: {{ attempt.id }}]
            </TitlePage>
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

                <!-- Readonly context -->
                <div
                    class="mb-4 p-3 border border-dashed border-slate-500 dark:border-slate-300
                           bg-white/60 dark:bg-slate-800/40"
                >
                    <div class="text-left text-md font-semibold opacity-80 mb-3">
                        {{ t('context') }}
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 text-sm">
                        <div class="space-y-3">
                            <div>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ t('user') }}:
                                </span>

                                <span class="text-slate-800 dark:text-slate-200 opacity-80">
                                    {{ userLabel }}
                                </span>
                            </div>

                            <div>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ t('quiz') }}:
                                </span>

                                <span class="text-slate-800 dark:text-slate-200 opacity-80">
                                    {{ quizLabel }}
                                </span>
                            </div>

                            <div>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ t('enrollment') }}:
                                </span>

                                <span class="text-slate-800 dark:text-slate-200 opacity-80">
                                    {{ enrollmentLabel }}
                                </span>
                            </div>

                            <div>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ t('attemptNumber') }}:
                                </span>

                                <span class="text-slate-800 dark:text-slate-200 opacity-80">
                                    {{ attempt.attempt_number ?? '—' }}
                                </span>
                            </div>

                            <div>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ t('percent') }}:
                                </span>

                                <span class="text-slate-800 dark:text-slate-200 opacity-80">
                                    {{ percentLabel }}
                                </span>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ t('course') }}:
                                </span>

                                <span class="text-slate-800 dark:text-slate-200 opacity-80">
                                    {{ courseLabel }}
                                </span>
                            </div>

                            <div>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ t('module') }}:
                                </span>

                                <span class="text-slate-800 dark:text-slate-200 opacity-80">
                                    {{ moduleLabel }}
                                </span>
                            </div>

                            <div>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ t('lesson') }}:
                                </span>

                                <span class="text-slate-800 dark:text-slate-200 opacity-80">
                                    {{ lessonLabel }}
                                </span>
                            </div>

                            <div>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ t('ipAddress') }}:
                                </span>

                                <span class="text-slate-800 dark:text-slate-200 opacity-80">
                                    {{ ipLabel }}
                                </span>
                            </div>

                            <div>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ t('userAgent') }}:
                                </span>

                                <div
                                    class="mt-1 text-slate-800 dark:text-slate-200
                                           opacity-80 break-words whitespace-pre-wrap"
                                >
                                    {{ userAgentLabel }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Editable fields -->
                <form @submit.prevent="submitForm" class="p-3 w-full space-y-4">
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

                            <InputError
                                class="mt-2"
                                :message="form.errors.duration_seconds"
                            />
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

                            <InputError
                                class="mt-2"
                                :message="form.errors.started_at"
                            />
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

                            <InputError
                                class="mt-2"
                                :message="form.errors.finished_at"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
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
                                <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
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
