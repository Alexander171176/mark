<script setup>
/**
 * Edit.vue — REVIEW-ONLY для QuizAttemptItem
 * Редактируем только:
 * - score
 * - is_correct (только для open_text)
 * - reviewer_comment
 *
 * Всё остальное — readonly контекст (attempt/user/quiz/question/student answer).
 */

import { computed } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import PrimaryButton from '@/Components/Admin/Buttons/PrimaryButton.vue'

import LabelInput from '@/Components/Admin/Input/LabelInput.vue'
import InputNumber from '@/Components/Admin/Input/InputNumber.vue'
import InputError from '@/Components/Admin/Input/InputError.vue'
import TinyEditor from '@/Components/Admin/TinyEditor/TinyEditor.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    item: { type: Object, required: true } // QuizAttemptItemResource->resolve()
})

/* ============================================================
   Auth (инструктор/админ кто проверяет)
   ============================================================ */
const page = usePage()

/* ============================================================
   Помощники
   ============================================================ */
const normalizeToEmptyString = (v) => (v === null || typeof v === 'undefined') ? '' : v

const formatDateTime = (value) => {
    if (!value) return '—'
    const d = new Date(value)
    if (Number.isNaN(d.getTime())) return String(value)
    return d.toLocaleString('ru-RU')
}

/* ============================================================
   Maps: status / questionType
   ============================================================ */

const statusLabel = computed(() => attemptStatusLabel(attempt.value?.status))
const statusClass = computed(() => attemptStatusClass(attempt.value?.status))

const questionTypeLabelValue = computed(() => questionTypeLabel(questionType.value))

/** Тип вопроса (map) */
const questionTypeLabel = (type) => {
    if (!type) return '—'
    const key = String(type)

    const map = {
        single_choice: t('questionTypeSingleChoice'),
        multiple_choice: t('questionTypeMultipleChoice'),
        true_false: t('questionTypeTrueFalse'),
        open_text: t('questionTypeOpenText'),
    }

    return map[key] || key
}

/** Статус попытки (map) */
const attemptStatusMap = () => ({
    in_progress: t('setStatusInProgress'),
    completed: t('setStatusCompleted'),
    graded: t('setStatusGraded'),
})

const attemptStatusLabel = (status) => {
    if (!status) return '—'
    const key = String(status)
    return attemptStatusMap()[key] || key
}

/** Подсветка статуса */
const attemptStatusClass = (status) => {
    const s = String(status || '')
    if (s === 'graded') return 'text-emerald-700 dark:text-emerald-200'
    if (s === 'completed') return 'text-sky-800 dark:text-sky-200'
    if (s === 'in_progress') return 'text-amber-800 dark:text-amber-200'
    return 'text-slate-500 dark:text-slate-300'
}

/* ============================================================
   Полученные данные из ресурса
   ============================================================ */
const attempt = computed(() => props.item?.attempt || null)
const student = computed(() => props.item?.attempt?.user || null)
const quiz = computed(() => props.item?.attempt?.quiz || null)
const question = computed(() => props.item?.question || null)

const questionType = computed(() => question.value?.type || null)
const questionText = computed(() => question.value?.text || '')
const questionPoints = computed(() => question.value?.points ?? null)

const maxScoreReadonly = computed(() => {
    const n = Number(questionPoints.value)
    return Number.isFinite(n) ? n : null
})

const isOpenText = computed(() => {
    const s = String(questionType.value || '').toLowerCase()
    return s.includes('open') || s.includes('text')
})

const isMultipleChoice = computed(() => {
    const s = String(questionType.value || '').toLowerCase()
    return s.includes('multiple')
})

const isSingleChoice = computed(() => {
    // single_choice / true_false / etc.
    if (!questionType.value) return false
    return !isMultipleChoice.value && !isOpenText.value
})

/* Ответ студента */
const selectedAnswerSingle = computed(() => props.item?.selected_answer || null)
const selectedAnswersMultiple = computed(() => {
    const arr = props.item?.selected_answers
    return Array.isArray(arr) ? arr : []
})
const selectedAnswerIds = computed(() => {
    const arr = props.item?.selected_answer_ids
    return Array.isArray(arr) ? arr : []
})
const freeTextAnswer = computed(() => props.item?.free_text_answer || '')

/* ============================================================
   Форма (поля только для проверки)
   ============================================================ */
const form = useForm({
    is_correct: !!props.item?.is_correct,
    score: normalizeToEmptyString(props.item?.score),
    reviewer_comment: props.item?.reviewer_comment || ''
})

/* ============================================================
   Быстрые действия
   ============================================================ */
const setScoreMax = () => {
    if (maxScoreReadonly.value === null) return
    form.score = String(maxScoreReadonly.value)
    toast.info(`${t('score')}: ${t('setMax')}`)
}

const setScoreZero = () => {
    form.score = '0'
    toast.info(`${t('score')}: 0`)
}

/* ============================================================
   Форма отправки
   ============================================================ */
const submitForm = () => {
    form.clearErrors()

    form.transform((data) => {
        const toNum = (v) => {
            if (v === '' || v === null || typeof v === 'undefined') return null
            const n = Number(v)
            return Number.isFinite(n) ? n : null
        }

        const payload = {
            score: toNum(data.score),
            reviewer_comment: (data.reviewer_comment || '').toString().trim() || null
        }

        // is_correct отправляем только для open_text (строго по твоей логике)
        if (isOpenText.value) payload.is_correct = !!data.is_correct

        return payload
    })

    form.put(route('admin.quizAttemptItems.update', props.item.id), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.success('Изменения сохранены!'),
        onError: (errors) => {
            console.error('❌ Ошибка при обновлении QuizAttemptItem:', errors)
            const firstKey = Object.keys(errors || {})[0]
            toast.error(firstKey ? errors[firstKey] : ('Проверь поля формы.'))
        }
    })
}
</script>

<template>
    <AdminLayout :title="t('editQuizAttemptItem')">
        <template #header>
            <TitlePage>
                {{ t('editQuizAttemptItem') }}
            </TitlePage>
        </template>

        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto">
            <div
                class="px-4 pt-4 bg-slate-50 dark:bg-slate-700
               border border-blue-400 dark:border-blue-200
               shadow-lg shadow-gray-500 dark:shadow-slate-400
               bg-opacity-95 dark:bg-opacity-95"
            >
                <!-- Кнопка назад -->
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <DefaultButton :href="route('admin.quizAttemptItems.index')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                                 viewBox="0 0 16 16">
                                <path
                                    d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.2-1.4-2.7l-2 .3c-.2 1.5 .9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2 .8-6.4z"
                                />
                            </svg>
                        </template>
                        {{ t('back') }}
                    </DefaultButton>
                </div>

                <form @submit.prevent="submitForm" class="pt-3 w-full">

                    <!-- =======================
                         Контекст
                         ======================= -->
                    <div
                        class="mb-4 p-3 border border-dashed border-slate-500 dark:border-slate-300
                               bg-white/60 dark:bg-slate-800/40">
                        <div class="text-center text-md font-semibold opacity-80
                                    text-gray-900 dark:text-gray-100">
                            {{ t('context') }}
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-3 text-sm">

                            <!-- Студент -->
                            <div class="p-2 border border-slate-300/70 dark:border-slate-200/30
                                        bg-white/70 dark:bg-slate-900/20">
                                <div class="font-semibold opacity-80
                                            text-slate-800 dark:text-slate-200">
                                    {{ t('student') }}
                                </div>

                                <div class="mt-1">
                                    <div class="font-semibold text slate-700 dark:text-slate-300">
                                        ID: {{ attempt?.user_id ?? student?.id ?? '—' }}
                                    </div>

                                    <div v-if="student">
                                        <div>
                                            <span class="font-semibold
                                                         text-slate-700 dark:text-slate-300">
                                                {{ t('name') }}:
                                            </span>
                                            <span class="font-semibold
                                                         text-indigo-700 dark:text-indigo-300">
                                                {{ student.name || '—' }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="font-semibold text
                                                         slate-700 dark:text-slate-300">
                                                Email:
                                            </span>
                                            <span class="font-semibold
                                                         text-indigo-700 dark:text-indigo-300">
                                                {{ student.email || '—' }}
                                            </span>
                                        </div>
                                    </div>

                                    <div v-else class="p-5 text-center text-slate-700
                                                       dark:text-slate-100">
                                        {{ t('noData') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Викторина -->
                            <div
                                class="p-2 border border-slate-300/70 dark:border-slate-200/30
                                       bg-white/70 dark:bg-slate-900/20">
                                <div class="font-semibold opacity-80
                                            text-slate-800 dark:text-slate-200">
                                    {{ t('quiz') }}
                                </div>
                                <div class="mt-1">
                                    <div class="font-semibold text slate-700 dark:text-slate-300">
                                        <span class="font-semibold text
                                                     slate-700 dark:text-slate-300">
                                            ID:
                                        </span>
                                        <span>{{ quiz?.id ?? attempt?.quiz_id ?? '—' }}</span>
                                    </div>
                                    <div>
                                        <span class="font-semibold text
                                                     slate-700 dark:text-slate-300">
                                            {{ t('title') }}:
                                        </span>
                                        <span class="font-semibold
                                                     text-indigo-700 dark:text-indigo-300">
                                            {{ quiz?.title ?? quiz?.slug ?? '—' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Попытка -->
                            <div
                                class="p-2 border border-slate-300/70 dark:border-slate-200/30
                                       bg-white/70 dark:bg-slate-900/20">
                                <div class="font-semibold opacity-80
                                            text-slate-800 dark:text-slate-200">
                                    {{ t('attempt') }}
                                </div>
                                <div class="mt-1">
                                    <div class="flex flex-wrap justify-between items-center">
                                        <div class="font-semibold
                                                    text slate-700 dark:text-slate-300">
                                            ID: {{ item.id }}
                                        </div>
                                        <div class="font-semibold
                                                    text slate-700 dark:text-slate-300">
                                            {{ t('attempt') }}
                                            ID: {{ attempt?.id ?? item.quiz_attempt_id ?? '—' }}
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap justify-between items-center">
                                        <div class="font-semibold
                                                    text-indigo-700 dark:text-indigo-300">
                                            {{ t('attemptNumber') }}
                                            {{ attempt?.attempt_number ?? '—' }}
                                        </div>
                                    </div>
                                    <div>
                                        <span class="font-semibold
                                                    text-slate-700 dark:text-slate-300">
                                            {{ t('status') }}:
                                        </span>
                                        <span class="font-semibold" :class="statusClass">
                                          {{ statusLabel }}
                                        </span>
                                    </div>

                                    <div class="flex flex-col items-start mt-1 text-xs opacity-75">
                                        <div>
                                            <span class="font-semibold
                                                         text-slate-900 dark:text-slate-100">
                                                {{ t('createdAt') }}:
                                            </span>
                                            <span class="font-semibold
                                                         text-blue-700 dark:text-blue-300">
                                                {{ formatDateTime(item.created_at) }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="font-semibold
                                                         text-slate-900 dark:text-slate-100">
                                                {{ t('updatedAt') }}:
                                            </span>
                                            <span class="font-semibold
                                                         text-blue-700 dark:text-blue-300">
                                                {{ formatDateTime(item.updated_at) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Вопрос -->
                        <div
                            class="mt-4 p-2 border border-slate-300/70 dark:border-slate-200/30
                                   bg-white/70 dark:bg-slate-900/20">
                            <div class="text-center text-md font-semibold opacity-80
                                        text-gray-900 dark:text-gray-100 text-md">
                                {{ t('quizQuestion') }}
                            </div>

                            <div class=" text-sm font-semibold text-slate-700 dark:text-slate-300">
                                ID: {{ question?.id ?? item.quiz_question_id ?? '—' }}
                            </div>

                            <div class="mt-2 whitespace-pre-wrap leading-relaxed text-sm
                                        font-semibold text-amber-800 dark:text-amber-200">
                                {{ questionText || '—' }}
                            </div>

                            <div class="mt-2 text-sm flex flex-row items-center justify-start">
                                <div>
                                    <span class="font-semibold
                                                 text-slate-900 dark:text-slate-100">
                                        {{ t('type') }}:
                                    </span>
                                    <span class="font-semibold
                                                 text-teal-800 dark:text-teal-200">
                                        <span class="font-semibold text-teal-800 dark:text-teal-200">
                                          {{ questionTypeLabelValue }}
                                        </span>
                                    </span>
                                </div>
                                <div class="mx-2">|</div>
                                <div>
                                    <span class="font-semibold
                                                 text-slate-900 dark:text-slate-100">
                                        {{ t('maxScore') }}:
                                    </span>
                                    <span class="font-semibold
                                                 text-teal-800 dark:text-teal-200">
                                        {{ maxScoreReadonly ?? item.max_score ?? '—' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- =======================
                         Студент
                         ======================= -->
                    <div
                        class="mb-4 p-3 border border-dashed border-slate-500 dark:border-slate-300
                               bg-white/60 dark:bg-slate-800/40 text-md">
                        <div class="text-center text-md font-semibold opacity-80
                                    text-gray-900 dark:text-gray-100">
                            {{ t('studentAnswer') }}
                        </div>

                        <!-- Один правильный ответ -->
                        <div v-if="isSingleChoice"
                             class="mt-3 p-2 border border-slate-300/70 dark:border-slate-200/30
                                    bg-white/70 dark:bg-slate-900/20 text-sm">
                            <div class="font-semibold opacity-80
                                        text-amber-800 dark:text-amber-200">
                                {{ t('selectedOneAnswer') }}
                            </div>
                            <div class="mt-1 flex flex-row items-center justify-start">
                                <template v-if="selectedAnswerSingle">
                                    <div class="font-semibold text slate-700 dark:text-slate-300">
                                        ID: {{ selectedAnswerSingle.id }}
                                    </div>
                                    <div class="mx-2">|</div>
                                    <div>
                                        <span class="font-semibold
                                                     text-slate-900 dark:text-slate-100">
                                            {{ t('text') }}:
                                        </span>
                                        <span class="font-semibold
                                                     text-teal-800 dark:text-teal-200">
                                            {{ selectedAnswerSingle.text || '—' }}
                                        </span>
                                    </div>
                                    <span class="ml-2" v-if="selectedAnswerSingle.is_correct">
                                        ✅
                                    </span>
                                    <span class="ml-2" v-else>❌</span>
                                </template>
                                <template v-else>
                                    —
                                </template>
                            </div>
                        </div>

                        <!-- Множество правильных ответов -->
                        <div v-else-if="isMultipleChoice"
                             class="mt-3 p-2 border border-slate-300/70 dark:border-slate-200/30
                                    bg-white/70 dark:bg-slate-900/20 text-sm">
                            <div class="font-semibold opacity-80
                                        text-amber-800 dark:text-amber-200">
                                {{ t('selectedSeveralAnswers') }}
                            </div>

                            <template v-if="selectedAnswersMultiple.length">
                                <ul class="mt-2 list-disc pl-5">
                                    <li class="flex flex-row items-center justify-start"
                                        v-for="a in selectedAnswersMultiple" :key="a.id">
                                        <span class="font-semibold
                                                     text slate-700 dark:text-slate-300">
                                            ID: {{ a.id }} — {{ a.text || '—' }}
                                        </span>
                                        <span v-if="a.is_correct" class="ml-2">
                                            ✅
                                        </span>
                                        <span v-else class="ml-2">
                                            ❌
                                        </span>
                                    </li>
                                </ul>
                            </template>

                            <template v-else>
                                <div class="mt-2 text-sm opacity-80 font-semibold
                                            text slate-700 dark:text-slate-300">
                        {{ selectedAnswerIds.length ? selectedAnswerIds.join(', ') : t('noData') }}
                                </div>
                                <div
                                    class="p-5 text-center text-slate-700
                                           dark:text-slate-100">
                                    {{ t('noData') }}
                                </div>
                            </template>
                        </div>

                        <!-- Развёрнутый ответ -->
                        <div v-else
                             class="mt-3 p-2 border border-slate-300/70 dark:border-slate-200/30
                                    bg-white/70 dark:bg-slate-900/20 text-sm">
                            <div class="font-semibold opacity-80
                                        text-slate-800 dark:text-slate-200">
                                {{ t('selectedTextAnswer') }}
                            </div>
                            <div
                                class="mt-2 p-3 border border-slate-300 dark:border-slate-600
                                       bg-white dark:bg-slate-900/40 rounded-sm">
                                <div class="prose prose-sm max-w-none dark:prose-invert
                                            font-semibold text-violet-800 dark:text-violet-200"
                                     v-html="freeTextAnswer || '—'"></div>
                            </div>
                        </div>
                    </div>

                    <!-- =======================
                         Инструктор
                         ======================= -->
                    <div
                        class="mb-4 p-3 border border-dashed border-slate-500 dark:border-slate-300
                               bg-white/60 dark:bg-slate-800/40">
                        <div class="text-center text-md font-semibold opacity-80
                                    text-gray-900 dark:text-gray-100">
                            {{ t('checkInstructor') }}
                        </div>

                        <div class="mt-4 grid grid-cols-1 lg:grid-cols-3 gap-4">

                            <!-- правильность ответа -->
                            <div class="flex flex-col items-start">
                                <LabelInput for="is_correct" class="mb-1">
                                    {{ t('isCorrect') }}
                                    <span v-if="!isOpenText" class="ml-2 text-xs opacity-70">
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

                                <div v-if="!isOpenText"
                                     class="mt-1 text-xs opacity-70
                                            text-gray-900 dark:text-gray-100">
                                    {{ t('auto') }}
                                </div>

                                <InputError class="mt-2" :message="form.errors.is_correct" />
                            </div>

                            <!-- баллы -->
                            <div class="flex flex-col items-start">
                                <LabelInput for="score" class="mb-1">{{ t('score') }}</LabelInput>

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
                                        class="text-xs px-2 py-1 border border-slate-400 rounded-sm
                                               hover:bg-slate-100 dark:hover:bg-slate-700
                                               text-gray-900 dark:text-gray-100 font-semibold"
                                        @click="setScoreMax"
                                        :disabled="maxScoreReadonly === null"
                                    >
                                        {{ t('setMax') }}
                                    </button>
                                    <button
                                        type="button"
                                        class="text-xs px-2 py-1 border border-slate-400 rounded-sm
                                               hover:bg-slate-100 dark:hover:bg-slate-700
                                               text-gray-900 dark:text-gray-100 font-semibold"
                                        @click="setScoreZero"
                                    >
                                        {{ t('setZero') }}
                                    </button>
                                </div>

                                <div v-if="maxScoreReadonly !== null"
                                     class="mt-1 text-xs opacity-70
                                            text-gray-900 dark:text-gray-100 font-semibold">
                                    {{ t('maxScore') }}: {{ maxScoreReadonly }}
                                </div>

                                <InputError class="mt-2" :message="form.errors.score" />
                            </div>

                            <!-- максимальное кол-во баллов -->
                            <div class="flex flex-col items-start">
                                <LabelInput class="mb-1">
                                    {{ t('maxScore') }}
                                </LabelInput>
                                <div
                                    class="w-full py-0.5 px-2 text-sm border border-slate-400
                                           rounded-sm bg-slate-100 dark:bg-slate-800
                                           font-semibold text-gray-900 dark:text-gray-100">
                                    {{ maxScoreReadonly ?? item.max_score ?? '—' }}
                                </div>
                            </div>
                        </div>

                        <!-- комментарий проверяющего -->
                        <div class="mt-4 flex flex-col items-start">
                            <LabelInput for="reviewer_comment" class="mb-1">
                                {{ t('reviewerComment') }}
                            </LabelInput>

                            <TinyEditor v-model="form.reviewer_comment" :height="260" />
                            <InputError class="mt-2" :message="form.errors.reviewer_comment" />
                            <InputError class="mt-2" :message="form.errors.server" />
                        </div>
                    </div>

                    <!-- Кнопка сохранить-->
                    <div class="flex items-center justify-center gap-3 pb-4">
                        <DefaultButton :href="route('admin.quizAttemptItems.index')"
                                       class="mb-3">
                            <template #icon>
                                <svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                                     viewBox="0 0 16 16">
                                    <path
                                        d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4 .7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.2-1.4-2.7l-2 .3c-.2 1.5 .9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2 .8-6.4z"
                                    />
                                </svg>
                            </template>
                            {{ t('back') || 'Назад' }}
                        </DefaultButton>

                        <PrimaryButton
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing">
                            <template #icon>
                                <svg class="w-4 h-4 fill-current text-slate-100"
                                     viewBox="0 0 16 16">
                                    <path
                                        d="M14.3 2.3L5 11.6 1.7 8.3c-.4-.4-1-.4-1.4 0-.4.4-.4 1 0 1.4l4 4c.2.2.4.3.7.3.3 0 .5-.1 .7-.3l10-10c.4-.4.4-1 0-1.4-.4-.4-1-.4-1.4 0z"
                                    />
                                </svg>
                            </template>
                            {{ t('save') || t('update') || 'Сохранить' }}
                        </PrimaryButton>
                    </div>

                </form>
            </div>
        </div>
    </AdminLayout>
</template>
