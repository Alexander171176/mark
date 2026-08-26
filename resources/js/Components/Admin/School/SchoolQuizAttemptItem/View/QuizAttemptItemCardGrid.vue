<script setup>
import { defineEmits, defineProps, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'

import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'

const { t } = useI18n()

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
    selectedItems: {
        type: Array,
        default: () => [],
    },
})

const emit = defineEmits([
    'delete',
    'toggle-select',
    'toggle-all',
])

/* ==========================================================
 * LOCAL DATA
 * ========================================================== */

const localItems = ref([])

watch(
    () => props.items,
    (newValue) => {
        localItems.value = JSON.parse(JSON.stringify(newValue || []))
    },
    { immediate: true, deep: true }
)

/* ==========================================================
 * SELECTION
 * ========================================================== */

const toggleAll = (event) => {
    emit('toggle-all', {
        ids: localItems.value.map(item => item.id),
        checked: event.target.checked,
    })
}

/* ==========================================================
 * TEXT
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

/* ==========================================================
 * RESOURCE HELPERS
 * ========================================================== */

const getQuestionText = (item) => {
    return item?.question?.translation?.question_text || ''
}

const getQuizTitle = (item) => {
    return item?.attempt?.quiz?.translation?.title
        || item?.attempt?.quiz?.slug
        || '—'
}

const formatAnswerShort = (item) => {
    if (item.free_text_answer) {
        return shortText(item.free_text_answer, 160)
    }

    if (item.selected_answer?.translation?.text) {
        return shortText(
            item.selected_answer.translation.text,
            160
        )
    }

    if (
        Array.isArray(item.selected_answers)
        && item.selected_answers.length
    ) {
        return item.selected_answers
            .map(answer =>
                shortText(
                    answer?.translation?.text || `#${answer.id}`,
                    60
                )
            )
            .join(', ')
    }

    if (item.selected_answer_id) {
        return `#${item.selected_answer_id}`
    }

    if (
        Array.isArray(item.selected_answer_ids)
        && item.selected_answer_ids.length
    ) {
        return item.selected_answer_ids.join(', ')
    }

    return '—'
}

/* ==========================================================
 * LABELS
 * ========================================================== */

const formatBool = (value) => {
    return value
        ? t('yes')
        : t('no')
}

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
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-200 dark:border-slate-600 relative"
    >
        <div
            class="flex items-center justify-between px-3 py-2
                   border-b border-slate-400 dark:border-slate-500"
        >
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('selected') }}: {{ selectedItems.length }}
            </div>

            <label
                v-if="localItems.length"
                class="flex items-center text-xs text-slate-600
                       dark:text-slate-200 cursor-pointer"
            >
                <span>{{ t('selectAll') }}</span>

                <input
                    type="checkbox"
                    class="rounded-sm border-slate-400 mx-2"
                    :checked="localItems.length &&
                        localItems.every(item => selectedItems.includes(item.id))"
                    @change="toggleAll"
                />
            </label>
        </div>

        <div v-if="localItems.length" class="p-3">
            <div class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
                <article
                    v-for="item in localItems"
                    :key="item.id"
                    class="relative flex flex-col h-full rounded-md
                           border border-slate-400 dark:border-slate-500
                           bg-slate-50/70 dark:bg-slate-800/80 shadow-sm
                           hover:shadow-md transition-shadow duration-150"
                >
                    <header
                        class="flex items-center justify-between px-2 py-1
                               border-b border-dashed border-slate-400
                               dark:border-slate-500"
                    >
                        <div class="flex items-center gap-2">
                            <div
                                class="text-[10px] font-semibold px-1.5 py-0.5
                                       rounded-sm border border-gray-400
                                       bg-slate-200 dark:bg-slate-700
                                       text-slate-800 dark:text-blue-100"
                            >
                                ID: {{ item.id }}
                            </div>

                            <span
                                v-if="item.attempt?.status"
                                class="text-[11px] font-semibold"
                                :class="attemptStatusClass(item.attempt.status)"
                            >
                                {{ attemptStatusLabel(item.attempt.status) }}
                            </span>
                        </div>

                        <div class="flex items-center gap-2">
                            <span
                                class="inline-flex items-center px-2 py-0.5
                                       rounded-sm border text-[11px] font-semibold"
                                :class="item.is_correct
                                    ? 'border-green-500 bg-green-50 text-green-800 dark:bg-green-900/30 dark:text-green-200'
                                    : 'border-rose-500 bg-rose-50 text-rose-800 dark:bg-rose-900/30 dark:text-rose-200'"
                                :title="t('isCorrect')"
                            >
                                {{ formatBool(item.is_correct) }}
                            </span>

                            <input
                                type="checkbox"
                                :checked="selectedItems.includes(item.id)"
                                @change="emit('toggle-select', item.id)"
                            />
                        </div>
                    </header>

                    <div class="flex flex-col flex-1 px-3 py-2">
                        <div class="flex flex-col items-center text-[11px] font-semibold">
                            <div
                                v-if="item.attempt?.quiz"
                                class="w-full pb-1 text-[11px] text-center
                                       text-slate-500 dark:text-slate-200
                                       border-b border-dotted
                                       border-slate-700/70 dark:border-slate-300/70"
                                :title="getQuizTitle(item)"
                            >
                                [ID: {{ item.attempt.quiz.id }}]
                                {{ t('quiz') }} —

                                <div class="text-rose-700 dark:text-rose-300">
                                    {{ getQuizTitle(item) }}
                                </div>

                                <div
                                    class="text-center text-amber-800 dark:text-amber-200"
                                    :title="`${t('passScore')} / ${t('maxScore')}`"
                                >
                                    {{ item.score ?? '—' }} / {{ item.max_score ?? '—' }}
                                </div>
                            </div>

                            <div class="pt-1 text-slate-500 dark:text-slate-300">
                                [ID: {{ item.school_quiz_question_id }}]
                                {{ t('quizQuestion') }}:
                            </div>

                            <div
                                class="pb-1 font-semibold text-[12px]
                                       text-fuchsia-800 dark:text-fuchsia-200
                                       border-b border-dotted
                                       border-slate-700/70 dark:border-slate-300/70"
                                :title="stripHtml(getQuestionText(item))"
                            >
                                {{ shortText(getQuestionText(item)) || '—' }}
                            </div>

                            <div
                                v-if="item.question?.question_type"
                                class="py-1 text-orange-600 dark:text-orange-200"
                            >
                                {{ questionTypeLabel(item.question.question_type) }}
                            </div>

                            <div
                                class="w-full font-semibold py-1
                                       border-t border-b border-dotted
                                       border-slate-700/70 dark:border-slate-300/70"
                            >
                                <div class="text-[12px] text-slate-500 dark:text-slate-300">
                                    {{ t('answer') }}:
                                </div>

                                <div
                                    class="text-[11px] text-teal-700 dark:text-teal-300"
                                    :title="stripHtml(formatAnswerShort(item))"
                                >
                                    {{ formatAnswerShort(item) }}
                                </div>

                                <div
                                    v-if="item.question?.points !== null &&
                                        item.question?.points !== undefined"
                                    class="text-amber-700 dark:text-amber-300"
                                >
                                    {{ t('points') }}: {{ item.question.points }}
                                </div>
                            </div>

                            <div class="py-1 flex flex-col items-center gap-0.5">
                                <div class="text-[11px] text-slate-500 dark:text-slate-200">
                                    ID: {{ item.school_quiz_attempt_id }}

                                    <span
                                        v-if="item.attempt?.attempt_number"
                                        class="text-indigo-700 dark:text-indigo-300"
                                    >
                                        · {{ t('attemptNumber') }}
                                        {{ item.attempt.attempt_number }}
                                    </span>
                                </div>

                                <div
                                    v-if="item.attempt?.user"
                                    class="flex flex-col items-center justify-center
                                           text-[11px] text-blue-700 dark:text-blue-300"
                                >
                                    {{ item.attempt.user.name || '—' }}

                                    <span
                                        v-if="item.attempt.user.email"
                                        class="text-slate-500 dark:text-slate-200"
                                    >
                                        ({{ item.attempt.user.email }})
                                    </span>
                                </div>
                            </div>

                            <div
                                v-if="item.reviewer_comment"
                                class="pt-1 text-[11px] font-semibold
                                       text-slate-600 dark:text-slate-400
                                       border-t border-dotted
                                       border-slate-700/70 dark:border-slate-300/70"
                                :title="stripHtml(item.reviewer_comment)"
                            >
                                {{ t('comment') }}:
                                {{ shortText(item.reviewer_comment, 100) }}
                            </div>
                        </div>
                    </div>

                    <footer
                        class="flex items-center justify-center px-3 py-2
                               border-t border-dashed border-slate-400
                               dark:border-slate-500"
                    >
                        <div class="flex items-center space-x-1">
                            <IconEdit
                                :href="route('admin.schoolQuizAttemptItems.edit', {
                                    schoolQuizAttemptItem: item.id,
                                })"
                            />

                            <DeleteIconButton
                                :title="t('delete')"
                                @delete="emit('delete', item)"
                            />
                        </div>
                    </footer>
                </article>
            </div>
        </div>

        <div
            v-else
            class="p-5 text-center text-slate-700 dark:text-slate-100"
        >
            {{ t('noData') }}
        </div>
    </div>
</template>
