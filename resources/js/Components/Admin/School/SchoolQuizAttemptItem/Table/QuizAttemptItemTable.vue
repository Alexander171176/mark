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

const shortText = (value, limit = 140) => {
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
        return shortText(item.free_text_answer, 180)
    }

    if (item.selected_answer?.translation?.text) {
        return shortText(
            item.selected_answer.translation.text,
            180
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
        <div class="overflow-x-auto">
            <table
                v-if="localItems.length"
                class="table-auto w-full text-slate-700 dark:text-slate-100"
            >
                <thead
                    class="text-sm uppercase bg-slate-200 dark:bg-cyan-900
                           border border-solid border-gray-300 dark:border-gray-700"
                >
                <tr>
                    <th class="px-2 py-3 w-px">
                        <div class="font-medium text-sm text-left">
                            {{ t('id') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-medium text-sm text-center">
                            {{ t('attempt') }}
                        </div>
                    </th>

                    <th class="px-2 py-3">
                        <div class="font-medium text-sm text-left">
                            {{ t('content') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-medium text-sm text-center">
                            {{ t('isCorrect') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-medium text-sm text-end">
                            {{ t('actions') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap text-center">
                        <input
                            type="checkbox"
                            :checked="localItems.length &&
                                    localItems.every(item => selectedItems.includes(item.id))"
                            @change="toggleAll"
                        />
                    </th>
                </tr>
                </thead>

                <tbody>
                <tr
                    v-for="item in localItems"
                    :key="item.id"
                    class="text-sm font-semibold border-b-2
                               hover:bg-slate-100 dark:hover:bg-cyan-800"
                >
                    <td class="px-2 py-3 w-px text-center">
                        <div class="text-xs text-slate-800 dark:text-blue-200">
                            {{ item.id }}
                        </div>
                    </td>

                    <td class="px-2 py-3 whitespace-nowrap">
                        <div class="flex flex-col items-center">
                                <span class="text-[11px] text-slate-500 dark:text-slate-200">
                                    ID: {{ item.school_quiz_attempt_id }}

                                    <span
                                        v-if="item.attempt?.attempt_number"
                                        class="text-indigo-700 dark:text-indigo-300"
                                    >
                                        · {{ t('attemptNumber') }}
                                        {{ item.attempt.attempt_number }}
                                    </span>
                                </span>

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
                    </td>

                    <td class="px-2 py-3">
                        <div class="flex flex-col gap-1">
                            <div
                                v-if="item.attempt?.quiz"
                                class="text-[11px] text-slate-500
                                           dark:text-slate-200 text-left"
                                :title="getQuizTitle(item)"
                            >
                                [ID: {{ item.attempt.quiz.id }}]
                                {{ t('quiz') }}:

                                <span class="text-rose-700 dark:text-rose-300">
                                        {{ getQuizTitle(item) }}
                                    </span>

                                <span
                                    class="text-xs text-amber-800 dark:text-amber-200"
                                    :title="`${t('passScore')} / ${t('maxScore')}`"
                                >
                                        — {{ item.score ?? '—' }} / {{ item.max_score ?? '—' }}
                                    </span>
                            </div>

                            <div class="text-[11px]">
                                    <span class="font-semibold text-slate-500 dark:text-slate-300">
                                        [ID: {{ item.school_quiz_question_id }}]
                                        {{ t('quizQuestion') }}:
                                    </span>

                                <span
                                    v-if="item.question?.question_type"
                                    class="text-orange-600 dark:text-orange-200"
                                >
                                        · {{ questionTypeLabel(item.question.question_type) }}
                                    </span>

                                <span
                                    v-if="item.question?.points !== null &&
                                            item.question?.points !== undefined"
                                    class="text-amber-700 dark:text-amber-300"
                                >
                                        · {{ t('points') }}: {{ item.question.points }}
                                    </span>
                            </div>

                            <div
                                class="text-[12px] text-fuchsia-800 dark:text-fuchsia-200"
                                :title="stripHtml(getQuestionText(item))"
                            >
                                {{ shortText(getQuestionText(item)) || '—' }}
                            </div>

                            <div
                                class="pt-1 border-t border-dashed
                                           border-slate-600/70 dark:border-slate-300/70"
                            >
                                <div class="text-[11px] text-slate-500 dark:text-slate-300">
                                    {{ t('answer') }}:
                                </div>

                                <div
                                    class="text-[12px] text-teal-700 dark:text-teal-300"
                                    :title="stripHtml(formatAnswerShort(item))"
                                >
                                    {{ formatAnswerShort(item) }}
                                </div>
                            </div>

                            <div
                                v-if="item.reviewer_comment"
                                class="mt-1 line-clamp-2 text-[11px]
                                           text-slate-600 dark:text-slate-400"
                                :title="stripHtml(item.reviewer_comment)"
                            >
                                {{ t('comment') }}:
                                {{ shortText(item.reviewer_comment, 120) }}
                            </div>
                        </div>
                    </td>

                    <td
                        class="px-2 py-3 flex flex-col
                                   items-center justify-center gap-1"
                    >
                            <span
                                v-if="item.attempt?.status"
                                class="text-[11px]"
                                :class="attemptStatusClass(item.attempt.status)"
                            >
                                {{ attemptStatusLabel(item.attempt.status) }}
                            </span>

                        <div
                            class="text-center text-xs font-semibold
                                       px-2 py-0.5 rounded-sm border"
                            :class="item.is_correct
                                    ? 'border-green-500 bg-green-50 text-green-800 dark:bg-green-900/30 dark:text-green-200'
                                    : 'border-rose-500 bg-rose-50 text-rose-800 dark:bg-rose-900/30 dark:text-rose-200'"
                        >
                            {{ formatBool(item.is_correct) }}
                        </div>
                    </td>

                    <td class="px-2 py-3 whitespace-nowrap">
                        <div class="flex justify-end space-x-2">
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
                    </td>

                    <td class="px-2 py-3 whitespace-nowrap text-center">
                        <input
                            type="checkbox"
                            :checked="selectedItems.includes(item.id)"
                            @change="emit('toggle-select', item.id)"
                        />
                    </td>
                </tr>
                </tbody>
            </table>

            <div
                v-else
                class="p-5 text-center text-slate-700 dark:text-slate-100"
            >
                {{ t('noData') }}
            </div>
        </div>
    </div>
</template>
