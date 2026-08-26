<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Создать ответ на конкретный вопрос викторины
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
import InputNumber from '@/Components/Admin/UI/Input/InputNumber.vue'
import InputError from '@/Components/Admin/UI/Input/InputError.vue'
import TinyEditor from '@/Components/Admin/UI/TinyEditor/TinyEditor.vue'
import ActivityCheckbox from '@/Components/Admin/UI/Checkbox/ActivityCheckbox.vue'

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

    attempts: {
        type: Array,
        default: () => [],
    },

    questions: {
        type: Array,
        default: () => [],
    },

    answers: {
        type: Array,
        default: () => [],
    },

    defaultAttemptId: {
        type: Number,
        default: null,
    },

    defaultQuestionId: {
        type: Number,
        default: null,
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

const shortText = (value, limit = 120) => {
    const clean = stripHtml(value)

    return clean.length > limit
        ? clean.slice(0, limit) + '…'
        : clean
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

const dynamicOptionsLimit = (items) => {
    const list = Array.isArray(items)
        ? items
        : []

    return list.length + 10
}

const findById = (items, id) => {
    if (!id) return null

    return items.find(
        item => Number(item.id) === Number(id)
    ) || null
}

/* ==========================================================
 * QUESTION TYPE
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

/* ==========================================================
 * OPTIONS
 * ========================================================== */

const attemptOptions = computed(() => {
    return (props.attempts || []).map(attempt => {
        const user = attempt.user
            ? `${attempt.user.name || '—'}${attempt.user.email ? ` (${attempt.user.email})` : ''}`
            : `User ID: ${attempt.user_id || '—'}`

        const quizTitle =
            attempt.quiz?.translation?.title
            || attempt.quiz?.slug
            || `Quiz ID: ${attempt.school_quiz_id || '—'}`

        return {
            ...attempt,

            label:
                `[ID: ${attempt.id}] `
                + `${user} — `
                + `${quizTitle} — `
                + `${t('attemptNumber')} ${attempt.attempt_number ?? '—'}`,
        }
    })
})

const questionOptions = computed(() => {
    return (props.questions || []).map(question => ({
        ...question,

        label:
            `[ID: ${question.id}] `
            + `${shortText(
                question.translation?.question_text || `#${question.id}`,
                140
            )} — `
            + `${questionTypeLabel(question.question_type)}`,
    }))
})

const answerOptionsAll = computed(() => {
    return (props.answers || []).map(answer => ({
        ...answer,

        label:
            `[ID: ${answer.id}] `
            + `${shortText(
                answer.translation?.text || `#${answer.id}`,
                120
            )}`
            + `${answer.is_correct ? ' ✅' : ''}`,
    }))
})

/* ==========================================================
 * INITIAL CONTEXT
 * ========================================================== */

const initialAttempt = findById(
    attemptOptions.value,
    props.defaultAttemptId
)

const initialQuestion = findById(
    questionOptions.value,
    props.defaultQuestionId
)

/* ==========================================================
 * FORM
 * ========================================================== */

/**
 * Единственный источник состояния:
 *
 * form.school_quiz_attempt_id
 * form.school_quiz_question_id
 * form.selected_answer_id
 * form.selected_answer_ids
 */
const form = useForm({
    school_quiz_attempt_id:
        initialAttempt?.id
        ?? props.defaultAttemptId
        ?? null,

    school_quiz_question_id:
        initialQuestion?.id
        ?? props.defaultQuestionId
        ?? null,

    selected_answer_id: null,
    selected_answer_ids: [],
    free_text_answer: '',

    is_correct: false,
    score: 0,

    max_score:
        initialQuestion?.points !== null
        && typeof initialQuestion?.points !== 'undefined'
            ? Number(initialQuestion.points)
            : 0,

    reviewer_comment: '',
})

/* ==========================================================
 * SELECTED ATTEMPT
 * ========================================================== */

const selectedAttempt = computed({
    get: () => findById(
        attemptOptions.value,
        form.school_quiz_attempt_id
    ),

    set: (attempt) => {
        form.school_quiz_attempt_id =
            attempt?.id ?? null
    },
})

/* ==========================================================
 * SELECTED QUESTION
 * ========================================================== */

const selectedQuestion = computed({
    get: () => findById(
        questionOptions.value,
        form.school_quiz_question_id
    ),

    set: (question) => {
        form.school_quiz_question_id =
            question?.id ?? null

        /**
         * При смене Question
         * полностью очищаем ответ.
         */
        form.selected_answer_id = null
        form.selected_answer_ids = []
        form.free_text_answer = ''
        form.is_correct = false
        form.score = 0

        const points = Number(
            question?.points
        )

        form.max_score =
            Number.isFinite(points)
                ? points
                : 0
    },
})

/* ==========================================================
 * QUESTION STATE
 * ========================================================== */

const questionType = computed(() =>
    selectedQuestion.value?.question_type
    || null
)

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

const maxScoreReadonly = computed(() => {
    const points = Number(
        selectedQuestion.value?.points
    )

    return Number.isFinite(points)
        ? points
        : null
})

/* ==========================================================
 * ANSWERS
 * ========================================================== */

const answerOptions = computed(() => {
    const questionId =
        form.school_quiz_question_id

    if (!questionId) {
        return answerOptionsAll.value
    }

    return answerOptionsAll.value.filter(
        answer =>
            Number(answer.school_quiz_question_id)
            === Number(questionId)
    )
})

/* ==========================================================
 * SELECTED SINGLE ANSWER
 * ========================================================== */

const selectedAnswer = computed({
    get: () => findById(
        answerOptions.value,
        form.selected_answer_id
    ),

    set: (answer) => {
        form.selected_answer_id =
            answer?.id ?? null

        if (!answer) {
            form.is_correct = false
            form.score = 0
            return
        }

        form.selected_answer_ids = []
        form.free_text_answer = ''

        form.is_correct =
            Boolean(answer.is_correct)

        form.score =
            answer.is_correct
                ? (
                    maxScoreReadonly.value
                    ?? form.max_score
                    ?? 0
                )
                : 0
    },
})

/* ==========================================================
 * SELECTED MULTIPLE ANSWERS
 * ========================================================== */

const selectedAnswers = computed({
    get: () => {
        const ids = Array.isArray(
            form.selected_answer_ids
        )
            ? form.selected_answer_ids
            : []

        return answerOptions.value.filter(
            answer =>
                ids.some(
                    id =>
                        Number(id)
                        === Number(answer.id)
                )
        )
    },

    set: (answers) => {
        const list = Array.isArray(answers)
            ? answers
            : []

        form.selected_answer_ids =
            list.map(answer => answer.id)

        form.selected_answer_id = null
        form.free_text_answer = ''

        if (!list.length) {
            form.is_correct = false
            form.score = 0
            return
        }

        const hasWrong =
            list.some(
                answer => !answer.is_correct
            )

        form.is_correct =
            !hasWrong

        form.score =
            form.is_correct
                ? (
                    maxScoreReadonly.value
                    ?? form.max_score
                    ?? 0
                )
                : 0
    },
})

/* ==========================================================
 * SCORE HELPERS
 * ========================================================== */

const setScoreMax = () => {
    if (maxScoreReadonly.value === null) {
        return
    }

    form.score =
        maxScoreReadonly.value

    form.max_score =
        maxScoreReadonly.value

    toast.info(
        `${t('score')}: ${t('setMax')}`
    )
}

const setScoreZero = () => {
    form.score = 0

    toast.info(
        `${t('score')}: 0`
    )
}

/* ==========================================================
 * SUBMIT
 * ========================================================== */

const submitForm = () => {
    form.transform(data => {
        return {
            ...data,

            school_quiz_attempt_id:
                data.school_quiz_attempt_id
                ?? null,

            school_quiz_question_id:
                data.school_quiz_question_id
                ?? null,

            selected_answer_id:
                isSingleChoice.value
                    ? data.selected_answer_id ?? null
                    : null,

            selected_answer_ids:
                isMultipleChoice.value
                    ? (
                        Array.isArray(
                            data.selected_answer_ids
                        )
                            ? data.selected_answer_ids
                            : []
                    )
                    : null,

            free_text_answer:
                isOpenText.value
                    ? (
                        data.free_text_answer
                        || null
                    )
                    : null,

            is_correct:
                Boolean(
                    data.is_correct
                ),

            score:
                toNumberOrNull(
                    data.score
                ) ?? 0,

            max_score:
                toNumberOrNull(
                    data.max_score
                )
                ?? maxScoreReadonly.value
                ?? 0,

            reviewer_comment:
                (data.reviewer_comment || '')
                    .toString()
                    .trim()
                || null,
        }
    })

    form.post(
        route(
            'admin.schoolQuizAttemptItems.store'
        ),
        {
            preserveScroll: true,

            onSuccess: () => {
                toast.success(
                    'Ответ попытки квиза успешно создан.'
                )
            },

            onError: (errors) => {
                const firstKey =
                    Object.keys(
                        errors || {}
                    )[0]

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
    <AdminLayout :title="t('createQuizAttemptItem')">
        <template #header>
            <TitlePage>
                {{ t('createQuizAttemptItem') }}
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
                            class="text-center text-md font-semibold
                                   opacity-80 text-gray-900 dark:text-gray-100"
                        >
                            {{ t('context') }}
                        </div>

                        <div class="flex flex-col items-start text-xs mb-2">
                            <LabelInput for="question">
                                <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                {{ t('quizQuestion') }}
                            </LabelInput>

                            <VueMultiselect
                                id="question"
                                v-model="selectedQuestion"
                                :options="questionOptions"
                                :options-limit="dynamicOptionsLimit(questionOptions)"
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
                                :message="form.errors.school_quiz_question_id"
                            />
                        </div>

                        <div class="flex flex-col items-start text-xs">
                            <LabelInput for="attempt">
                                <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                {{ t('attempt') }}
                            </LabelInput>

                            <VueMultiselect
                                id="attempt"
                                v-model="selectedAttempt"
                                :options="attemptOptions"
                                :options-limit="dynamicOptionsLimit(attemptOptions)"
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
                                :message="form.errors.school_quiz_attempt_id"
                            />
                        </div>

                        <div
                            v-if="selectedQuestion"
                            class="mt-4 p-2 border border-slate-300/70
                                   dark:border-slate-200/30
                                   bg-white/70 dark:bg-slate-900/20"
                        >
                            <div class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                                ID: {{ selectedQuestion.id }}
                            </div>

                            <div
                                class="mt-2 whitespace-pre-wrap leading-relaxed
                                       text-sm font-semibold
                                       text-amber-800 dark:text-amber-200"
                            >
                                {{
                                    stripHtml(
                                        selectedQuestion.translation?.question_text
                                    ) || '—'
                                }}
                            </div>

                            <div class="mt-2 text-sm flex flex-row items-center justify-start">
                                <div>
                                    <span class="font-semibold text-slate-900 dark:text-slate-100">
                                        {{ t('type') }}:
                                    </span>

                                    <span class="font-semibold text-teal-800 dark:text-teal-200">
                                        {{ questionTypeLabel(selectedQuestion.question_type) }}
                                    </span>
                                </div>

                                <div class="mx-2">|</div>

                                <div>
                                    <span class="font-semibold text-slate-900 dark:text-slate-100">
                                        {{ t('maxScore') }}:
                                    </span>

                                    <span class="font-semibold text-teal-800 dark:text-teal-200">
                                        {{ maxScoreReadonly ?? form.max_score ?? '—' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Student Answer -->
                    <div
                        class="mb-4 p-3 border border-dashed
                               border-slate-500 dark:border-slate-300
                               bg-white/60 dark:bg-slate-800/40"
                    >
                        <div
                            class="text-center text-md font-semibold
                                   opacity-80 text-gray-900 dark:text-gray-100"
                        >
                            {{ t('studentAnswer') }}
                        </div>

                        <div
                            v-if="isSingleChoice"
                            class="mt-4 flex flex-col items-start"
                        >
                            <LabelInput for="selected_answer_id">
                                <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                {{ t('selectedOneAnswer') }}
                            </LabelInput>

                            <VueMultiselect
                                id="selected_answer_id"
                                v-model="selectedAnswer"
                                :options="answerOptions"
                                :options-limit="dynamicOptionsLimit(answerOptions)"
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
                                :message="form.errors.selected_answer_id"
                            />
                        </div>

                        <div
                            v-else-if="isMultipleChoice"
                            class="mt-4 flex flex-col items-start"
                        >
                            <LabelInput for="selected_answer_ids">
                                <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                {{ t('selectedSeveralAnswers') }}
                            </LabelInput>

                            <VueMultiselect
                                id="selected_answer_ids"
                                v-model="selectedAnswers"
                                :options="answerOptions"
                                :options-limit="dynamicOptionsLimit(answerOptions)"
                                :multiple="true"
                                :close-on-select="false"
                                :clear-on-select="false"
                                :preserve-search="true"
                                :placeholder="t('select')"
                                label="label"
                                track-by="id"
                                class="w-full"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.selected_answer_ids"
                            />
                        </div>

                        <div
                            v-else-if="isOpenText"
                            class="mt-4 flex flex-col items-start"
                        >
                            <LabelInput for="free_text_answer">
                                <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                {{ t('selectedTextAnswer') }}
                            </LabelInput>

                            <TinyEditor
                                id="free_text_answer"
                                v-model="form.free_text_answer"
                                :height="260"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.free_text_answer"
                            />
                        </div>

                        <div
                            v-else
                            class="mt-3 p-3 text-center
                                   text-slate-700 dark:text-slate-100"
                        >
                            {{ t('select') }} {{ t('quizQuestion') }}
                        </div>
                    </div>

                    <!-- Check -->
                    <div
                        class="mb-4 p-3 border border-dashed
                               border-slate-500 dark:border-slate-300
                               bg-white/60 dark:bg-slate-800/40"
                    >
                        <div
                            class="text-center text-md font-semibold
                                   opacity-80 text-gray-900 dark:text-gray-100"
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
                                </LabelInput>

                                <label class="flex items-center gap-2 text-sm">
                                    <ActivityCheckbox
                                        id="is_correct"
                                        v-model="form.is_correct"
                                    />

                                    <span class="text-gray-900 dark:text-gray-100">
                                        {{ form.is_correct ? t('yes') : t('no') }}
                                    </span>
                                </label>

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
                                <LabelInput
                                    for="max_score"
                                    class="mb-1"
                                >
                                    {{ t('maxScore') }}
                                </LabelInput>

                                <InputNumber
                                    id="max_score"
                                    type="number"
                                    min="0"
                                    v-model="form.max_score"
                                    class="w-full"
                                />

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
                            {{ t('save') }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<style src="/resources/css/vue-multiselect.min.css"></style>
