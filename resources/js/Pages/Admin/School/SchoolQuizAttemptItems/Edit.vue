<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Редактировать ответ на конкретный вопрос викторины
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
import InputNumber from '@/Components/Admin/UI/Input/InputNumber.vue'
import InputError from '@/Components/Admin/UI/Input/InputError.vue'
import TinyEditor from '@/Components/Admin/UI/TinyEditor/TinyEditor.vue'

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

    item: {
        type: Object,
        required: true,
    },
})

/* ==========================================================
 * HELPERS
 * ========================================================== */

const stripHtml = (html = '') => {
    return (html || '')
        .replace(/<\/p>/gi, ' ')
        .replace(/<br\s*\/?>/gi, ' ')
        .replace(/<[^>]+>/g, '')
        .replace(/&nbsp;/gi, ' ')
        .replace(/&amp;/gi, '&')
        .replace(/&quot;/gi, '"')
        .replace(/&#039;/gi, "'")
        .replace(/&lt;/gi, '<')
        .replace(/&gt;/gi, '>')
        .replace(/\s+/g, ' ')
        .trim()
}

const normalizeToEmptyString = (value) => {
    return value === null
    || typeof value === 'undefined'
        ? ''
        : value
}

const toNumberOrNull = (value) => {
    if (
        value === ''
        || value === null
        || typeof value === 'undefined'
    ) {
        return null
    }

    const number = Number(value)

    return Number.isFinite(number)
        ? number
        : null
}

const formatDateTime = (value) => {
    if (!value) return '—'

    const date = new Date(value)

    if (Number.isNaN(date.getTime())) {
        return String(value)
    }

    return date.toLocaleString('ru-RU')
}

/* ==========================================================
 * LABELS
 * ========================================================== */

const questionTypeLabel = (type) => {
    const map = {
        single_choice: t('questionTypeSingleChoice'),
        multiple_choice: t('questionTypeMultipleChoice'),
        true_false: t('questionTypeTrueFalse'),
        open_text: t('questionTypeOpenText'),
    }

    return map[type] || type || '—'
}

const attemptStatusLabel = (status) => {
    const map = {
        in_progress: t('setStatusInProgress'),
        completed: t('setStatusCompleted'),
        graded: t('setStatusGraded'),
    }

    return map[status] || status || '—'
}

const attemptStatusClass = (status) => {
    if (status === 'graded') {
        return 'text-emerald-700 dark:text-emerald-200'
    }

    if (status === 'completed') {
        return 'text-sky-800 dark:text-sky-200'
    }

    if (status === 'in_progress') {
        return 'text-amber-800 dark:text-amber-200'
    }

    return 'text-slate-500 dark:text-slate-300'
}

/* ==========================================================
 * CONTEXT
 * ========================================================== */

const attempt = computed(() =>
    props.item?.attempt
    || null
)

const student = computed(() =>
    props.item?.attempt?.user
    || null
)

const quiz = computed(() =>
    props.item?.attempt?.quiz
    || null
)

const quizTitle = computed(() =>
    quiz.value?.translation?.title
    || quiz.value?.slug
    || '—'
)

const question = computed(() =>
    props.item?.question
    || null
)

const questionType = computed(() =>
    question.value?.question_type
    || null
)

const questionText = computed(() =>
    question.value?.translation?.question_text
    || ''
)

const questionExplanation = computed(() =>
    question.value?.translation?.explanation
    || ''
)

const questionPoints = computed(() =>
    question.value?.points
    ?? null
)

const statusLabel = computed(() =>
    attemptStatusLabel(
        attempt.value?.status
    )
)

const statusClass = computed(() =>
    attemptStatusClass(
        attempt.value?.status
    )
)

const questionTypeLabelValue = computed(() =>
    questionTypeLabel(
        questionType.value
    )
)

/* ==========================================================
 * QUESTION STATE
 * ========================================================== */

const maxScoreReadonly = computed(() => {
    const points = Number(
        questionPoints.value
    )

    if (Number.isFinite(points)) {
        return points
    }

    const maxScore = Number(
        props.item?.max_score
    )

    return Number.isFinite(maxScore)
        ? maxScore
        : null
})

const isOpenText = computed(() =>
    questionType.value === 'open_text'
)

const isMultipleChoice = computed(() =>
    questionType.value === 'multiple_choice'
)

const isSingleChoice = computed(() =>
    [
        'single_choice',
        'true_false',
    ].includes(
        questionType.value
    )
)

/* ==========================================================
 * STUDENT ANSWER
 * ========================================================== */

const selectedAnswerSingle = computed(() =>
    props.item?.selected_answer
    || null
)

const selectedAnswersMultiple = computed(() => {
    return Array.isArray(
        props.item?.selected_answers
    )
        ? props.item.selected_answers
        : []
})

const selectedAnswerIds = computed(() => {
    return Array.isArray(
        props.item?.selected_answer_ids
    )
        ? props.item.selected_answer_ids
        : []
})

const freeTextAnswer = computed(() =>
    props.item?.free_text_answer
    || ''
)

/* ==========================================================
 * FORM
 * ========================================================== */

const form = useForm({
    _method: 'PUT',

    is_correct:
        Boolean(
            props.item?.is_correct
        ),

    score:
        normalizeToEmptyString(
            props.item?.score
        ),

    max_score:
        normalizeToEmptyString(
            props.item?.max_score
        ),

    reviewer_comment:
        props.item?.reviewer_comment
        || '',
})

/* ==========================================================
 * SCORE HELPERS
 * ========================================================== */

const setScoreMax = () => {
    if (maxScoreReadonly.value === null) {
        return
    }

    form.score =
        String(
            maxScoreReadonly.value
        )

    toast.info(
        `${t('score')}: ${t('setMax')}`
    )
}

const setScoreZero = () => {
    form.score = '0'

    toast.info(
        `${t('score')}: 0`
    )
}

/* ==========================================================
 * SUBMIT
 * ========================================================== */

const submitForm = () => {
    form.transform(data => {
        const payload = {
            _method: 'PUT',

            score:
                toNumberOrNull(
                    data.score
                ),

            max_score:
                toNumberOrNull(
                    data.max_score
                )
                ?? maxScoreReadonly.value,

            reviewer_comment:
                (data.reviewer_comment || '')
                    .toString()
                    .trim()
                || null,
        }

        /**
         * Для open_text преподаватель
         * определяет правильность вручную.
         *
         * Для остальных типов правильность
         * уже определяется системой.
         */
        if (isOpenText.value) {
            payload.is_correct =
                Boolean(
                    data.is_correct
                )
        }

        Object.keys(payload).forEach(key => {
            if (payload[key] === null) {
                delete payload[key]
            }
        })

        return payload
    })

    form.post(
        route(
            'admin.schoolQuizAttemptItems.update',
            {
                schoolQuizAttemptItem:
                props.item.id,
            }
        ),
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                toast.success(
                    'Проверка ответа успешно обновлена.'
                )
            },

            onError: (errors) => {
                console.error(
                    'Ошибка обновления ответа попытки:',
                    errors
                )

                const firstKey =
                    Object.keys(
                        errors || {}
                    )[0]

                toast.error(
                    errors?.[firstKey]
                    || 'Проверьте поля формы.'
                )
            },
        }
    )
}
</script>

<template>
    <AdminLayout :title="t('editQuizAttemptItem')">
        <template #header>
            <TitlePage>
                {{ t('editQuizAttemptItem') }} [ID: {{ item.id }}]
            </TitlePage>
        </template>

        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto">
            <div
                class="px-4 pt-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       shadow-lg shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <DefaultButton :href="route('admin.schoolQuizAttemptItems.index')">
                        <template #icon>
                            <svg
                                class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                                viewBox="0 0 16 16"
                            >
                                <path
                                    d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2 .8-6.4z"
                                />
                            </svg>
                        </template>

                        {{ t('back') }}
                    </DefaultButton>
                </div>

                <form
                    @submit.prevent="submitForm"
                    class="pt-3 w-full"
                >
                    <!-- Context -->
                    <div
                        class="mb-4 p-3 border border-dashed
                               border-slate-500 dark:border-slate-300
                               bg-white/60 dark:bg-slate-800/40"
                    >
                        <div
                            class="text-center text-md font-semibold opacity-80
                                   text-gray-900 dark:text-gray-100"
                        >
                            {{ t('context') }}
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-3 text-sm">
                            <!-- Student -->
                            <div
                                class="p-2 border border-slate-300/70
                                       dark:border-slate-200/30
                                       bg-white/70 dark:bg-slate-900/20"
                            >
                                <div class="font-semibold opacity-80 text-slate-800 dark:text-slate-200">
                                    {{ t('student') }}
                                </div>

                                <div class="mt-1">
                                    <div class="font-semibold text-slate-700 dark:text-slate-300">
                                        ID:
                                        {{ attempt?.user_id ?? student?.id ?? '—' }}
                                    </div>

                                    <template v-if="student">
                                        <div>
                                            <span class="font-semibold text-slate-700 dark:text-slate-300">
                                                {{ t('name') }}:
                                            </span>

                                            <span class="font-semibold text-indigo-700 dark:text-indigo-300">
                                                {{ student.name || '—' }}
                                            </span>
                                        </div>

                                        <div>
                                            <span class="font-semibold text-slate-700 dark:text-slate-300">
                                                Email:
                                            </span>

                                            <span class="font-semibold text-indigo-700 dark:text-indigo-300">
                                                {{ student.email || '—' }}
                                            </span>
                                        </div>
                                    </template>

                                    <div
                                        v-else
                                        class="p-5 text-center text-slate-700 dark:text-slate-100"
                                    >
                                        {{ t('noData') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Quiz -->
                            <div
                                class="p-2 border border-slate-300/70
                                       dark:border-slate-200/30
                                       bg-white/70 dark:bg-slate-900/20"
                            >
                                <div class="font-semibold opacity-80 text-slate-800 dark:text-slate-200">
                                    {{ t('quiz') }}
                                </div>

                                <div class="mt-1">
                                    <div class="font-semibold text-slate-700 dark:text-slate-300">
                                        ID:
                                        {{ quiz?.id ?? attempt?.school_quiz_id ?? '—' }}
                                    </div>

                                    <div>
                                        <span class="font-semibold text-slate-700 dark:text-slate-300">
                                            {{ t('title') }}:
                                        </span>

                                        <span class="font-semibold text-indigo-700 dark:text-indigo-300">
                                            {{ quizTitle }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Attempt -->
                            <div
                                class="p-2 border border-slate-300/70
                                       dark:border-slate-200/30
                                       bg-white/70 dark:bg-slate-900/20"
                            >
                                <div class="font-semibold opacity-80 text-slate-800 dark:text-slate-200">
                                    {{ t('attempt') }}
                                </div>

                                <div class="mt-1">
                                    <div class="flex flex-wrap justify-between items-center">
                                        <div class="font-semibold text-slate-700 dark:text-slate-300">
                                            Item ID: {{ item.id }}
                                        </div>

                                        <div class="font-semibold text-slate-700 dark:text-slate-300">
                                            {{ t('attempt') }} ID:
                                            {{ attempt?.id ?? item.school_quiz_attempt_id ?? '—' }}
                                        </div>
                                    </div>

                                    <div class="font-semibold text-indigo-700 dark:text-indigo-300">
                                        {{ t('attemptNumber') }}
                                        {{ attempt?.attempt_number ?? '—' }}
                                    </div>

                                    <div>
                                        <span class="font-semibold text-slate-700 dark:text-slate-300">
                                            {{ t('status') }}:
                                        </span>

                                        <span
                                            class="font-semibold"
                                            :class="statusClass"
                                        >
                                            {{ statusLabel }}
                                        </span>
                                    </div>

                                    <div class="flex flex-col items-start mt-1 text-xs opacity-75">
                                        <div>
                                            <span class="font-semibold text-slate-900 dark:text-slate-100">
                                                {{ t('createdAt') }}:
                                            </span>

                                            <span class="font-semibold text-blue-700 dark:text-blue-300">
                                                {{ formatDateTime(item.created_at) }}
                                            </span>
                                        </div>

                                        <div>
                                            <span class="font-semibold text-slate-900 dark:text-slate-100">
                                                {{ t('updatedAt') }}:
                                            </span>

                                            <span class="font-semibold text-blue-700 dark:text-blue-300">
                                                {{ formatDateTime(item.updated_at) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Question -->
                        <div
                            class="mt-4 p-2 border border-slate-300/70
                                   dark:border-slate-200/30
                                   bg-white/70 dark:bg-slate-900/20"
                        >
                            <div
                                class="text-center font-semibold opacity-80
                                       text-gray-900 dark:text-gray-100 text-md"
                            >
                                {{ t('quizQuestion') }}
                            </div>

                            <div class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                                ID:
                                {{ question?.id ?? item.school_quiz_question_id ?? '—' }}
                            </div>

                            <div
                                class="mt-2 whitespace-pre-wrap leading-relaxed
                                       text-sm font-semibold
                                       text-amber-800 dark:text-amber-200"
                            >
                                {{ stripHtml(questionText) || '—' }}
                            </div>

                            <div
                                v-if="questionExplanation"
                                class="mt-2 text-xs text-slate-600 dark:text-slate-300"
                            >
                                {{ stripHtml(questionExplanation) }}
                            </div>

                            <div class="mt-2 text-sm flex flex-row items-center justify-start">
                                <div>
                                    <span class="font-semibold text-slate-900 dark:text-slate-100">
                                        {{ t('type') }}:
                                    </span>

                                    <span class="font-semibold text-teal-800 dark:text-teal-200">
                                        {{ questionTypeLabelValue }}
                                    </span>
                                </div>

                                <div class="mx-2">|</div>

                                <div>
                                    <span class="font-semibold text-slate-900 dark:text-slate-100">
                                        {{ t('maxScore') }}:
                                    </span>

                                    <span class="font-semibold text-teal-800 dark:text-teal-200">
                                        {{ maxScoreReadonly ?? item.max_score ?? '—' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Student Answer -->
                    <div
                        class="mb-4 p-3 border border-dashed
                               border-slate-500 dark:border-slate-300
                               bg-white/60 dark:bg-slate-800/40 text-md"
                    >
                        <div
                            class="text-center text-md font-semibold opacity-80
                                   text-gray-900 dark:text-gray-100"
                        >
                            {{ t('studentAnswer') }}
                        </div>

                        <!-- Single -->
                        <div
                            v-if="isSingleChoice"
                            class="mt-3 p-2 border border-slate-300/70
                                   dark:border-slate-200/30
                                   bg-white/70 dark:bg-slate-900/20 text-sm"
                        >
                            <div class="font-semibold opacity-80 text-amber-800 dark:text-amber-200">
                                {{ t('selectedOneAnswer') }}
                            </div>

                            <div class="mt-1 flex flex-row items-center justify-start">
                                <template v-if="selectedAnswerSingle">
                                    <div class="font-semibold text-slate-700 dark:text-slate-300">
                                        ID:
                                        {{ selectedAnswerSingle.id }}
                                    </div>

                                    <div class="mx-2">|</div>

                                    <div>
                                        <span class="font-semibold text-slate-900 dark:text-slate-100">
                                            {{ t('text') }}:
                                        </span>

                                        <span class="font-semibold text-teal-800 dark:text-teal-200">
                                            {{
                                                stripHtml(
                                                    selectedAnswerSingle.translation?.text
                                                ) || '—'
                                            }}
                                        </span>
                                    </div>

                                    <span
                                        v-if="selectedAnswerSingle.is_correct"
                                        class="ml-2"
                                    >
                                        ✅
                                    </span>

                                    <span
                                        v-else
                                        class="ml-2"
                                    >
                                        ❌
                                    </span>
                                </template>

                                <template v-else>
                                    —
                                </template>
                            </div>
                        </div>

                        <!-- Multiple -->
                        <div
                            v-else-if="isMultipleChoice"
                            class="mt-3 p-2 border border-slate-300/70
                                   dark:border-slate-200/30
                                   bg-white/70 dark:bg-slate-900/20 text-sm"
                        >
                            <div class="font-semibold opacity-80 text-amber-800 dark:text-amber-200">
                                {{ t('selectedSeveralAnswers') }}
                            </div>

                            <template v-if="selectedAnswersMultiple.length">
                                <ul class="mt-2 list-disc pl-5">
                                    <li
                                        v-for="answer in selectedAnswersMultiple"
                                        :key="answer.id"
                                        class="flex flex-row items-center justify-start"
                                    >
                                        <span class="font-semibold text-slate-700 dark:text-slate-300">
                                            ID: {{ answer.id }} —
                                            {{
                                                stripHtml(
                                                    answer.translation?.text
                                                ) || '—'
                                            }}
                                        </span>

                                        <span
                                            v-if="answer.is_correct"
                                            class="ml-2"
                                        >
                                            ✅
                                        </span>

                                        <span
                                            v-else
                                            class="ml-2"
                                        >
                                            ❌
                                        </span>
                                    </li>
                                </ul>
                            </template>

                            <template v-else>
                                <div
                                    class="mt-2 text-sm opacity-80
                                           font-semibold text-slate-700
                                           dark:text-slate-300"
                                >
                                    {{
                                        selectedAnswerIds.length
                                            ? selectedAnswerIds.join(', ')
                                            : t('noData')
                                    }}
                                </div>
                            </template>
                        </div>

                        <!-- Open text -->
                        <div
                            v-else
                            class="mt-3 p-2 border border-slate-300/70
                                   dark:border-slate-200/30
                                   bg-white/70 dark:bg-slate-900/20 text-sm"
                        >
                            <div class="font-semibold opacity-80 text-slate-800 dark:text-slate-200">
                                {{ t('selectedTextAnswer') }}
                            </div>

                            <div
                                class="mt-2 p-3 border border-slate-300
                                       dark:border-slate-600 bg-white
                                       dark:bg-slate-900/40 rounded-sm"
                            >
                                <div
                                    class="whitespace-pre-wrap font-semibold
                                           text-violet-800 dark:text-violet-200"
                                >
                                    {{ stripHtml(freeTextAnswer) || '—' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Instructor check -->
                    <div
                        class="mb-4 p-3 border border-dashed
                               border-slate-500 dark:border-slate-300
                               bg-white/60 dark:bg-slate-800/40"
                    >
                        <div
                            class="text-center text-md font-semibold opacity-80
                                   text-gray-900 dark:text-gray-100"
                        >
                            {{ t('checkInstructor') }}
                        </div>

                        <div class="mt-4 grid grid-cols-1 lg:grid-cols-3 gap-4">
                            <div class="flex flex-col items-start">
                                <LabelInput
                                    for="is_correct"
                                    class="mb-1"
                                >
                                    {{ t('isCorrect') }}

                                    <span
                                        v-if="!isOpenText"
                                        class="ml-2 text-xs opacity-70"
                                    >
                                        ({{ t('auto') }})
                                    </span>
                                </LabelInput>

                                <label class="flex items-center gap-2 text-sm">
                                    <input
                                        id="is_correct"
                                        type="checkbox"
                                        v-model="form.is_correct"
                                        class="rounded border-slate-400"
                                        :disabled="!isOpenText"
                                    />

                                    <span class="text-gray-900 dark:text-gray-100">
                                        {{ form.is_correct ? t('yes') : t('no') }}
                                    </span>
                                </label>

                                <div
                                    v-if="!isOpenText"
                                    class="mt-1 text-xs opacity-70
                                           text-gray-900 dark:text-gray-100"
                                >
                                    {{ t('auto') }}
                                </div>

                                <InputError
                                    class="mt-2"
                                    :message="form.errors.is_correct"
                                />
                            </div>

                            <div class="flex flex-col items-start">
                                <LabelInput
                                    for="score"
                                    class="mb-1"
                                >
                                    {{ t('score') }}
                                </LabelInput>

                                <InputNumber
                                    id="score"
                                    type="number"
                                    min="0"
                                    :max="maxScoreReadonly ?? undefined"
                                    v-model="form.score"
                                    class="w-full"
                                />

                                <div class="flex gap-2 mt-2">
                                    <button
                                        type="button"
                                        class="text-xs px-2 py-1 border
                                               border-slate-400 rounded-sm
                                               hover:bg-slate-100
                                               dark:hover:bg-slate-700
                                               text-gray-900 dark:text-gray-100
                                               font-semibold"
                                        :disabled="maxScoreReadonly === null"
                                        @click="setScoreMax"
                                    >
                                        {{ t('setMax') }}
                                    </button>

                                    <button
                                        type="button"
                                        class="text-xs px-2 py-1 border
                                               border-slate-400 rounded-sm
                                               hover:bg-slate-100
                                               dark:hover:bg-slate-700
                                               text-gray-900 dark:text-gray-100
                                               font-semibold"
                                        @click="setScoreZero"
                                    >
                                        {{ t('setZero') }}
                                    </button>
                                </div>

                                <InputError
                                    class="mt-2"
                                    :message="form.errors.score"
                                />
                            </div>

                            <div class="flex flex-col items-start">
                                <LabelInput class="mb-1">
                                    {{ t('maxScore') }}
                                </LabelInput>

                                <div
                                    class="w-full py-0.5 px-2 text-sm
                                           border border-slate-400 rounded-sm
                                           bg-slate-100 dark:bg-slate-800
                                           font-semibold text-gray-900
                                           dark:text-gray-100"
                                >
                                    {{ maxScoreReadonly ?? item.max_score ?? '—' }}
                                </div>

                                <InputError
                                    class="mt-2"
                                    :message="form.errors.max_score"
                                />
                            </div>
                        </div>

                        <div class="mt-4 flex flex-col items-start">
                            <LabelInput
                                for="reviewer_comment"
                                class="mb-1"
                            >
                                {{ t('reviewerComment') }}
                            </LabelInput>

                            <TinyEditor
                                id="reviewer_comment"
                                v-model="form.reviewer_comment"
                                :height="260"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.reviewer_comment"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.server"
                            />
                        </div>
                    </div>

                    <div class="flex items-center justify-center gap-3 pb-4">
                        <DefaultButton :href="route('admin.schoolQuizAttemptItems.index')">
                            <template #icon>
                                <svg
                                    class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                                    viewBox="0 0 16 16"
                                >
                                    <path
                                        d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2 .8-6.4z"
                                    />
                                </svg>
                            </template>

                            {{ t('back') }}
                        </DefaultButton>

                        <PrimaryButton
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            {{ t('save') || t('update') }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
