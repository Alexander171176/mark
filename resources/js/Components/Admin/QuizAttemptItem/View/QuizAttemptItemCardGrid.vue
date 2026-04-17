<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'

import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'

const { t } = useI18n()

const props = defineProps({
    items: { type: Array, default: () => [] },
    selectedItems: { type: Array, default: () => [] },
})

const emits = defineEmits(['delete', 'toggle-select', 'toggle-all'])

const localItems = ref([])

watch(
    () => props.items,
    (newVal) => {
        localItems.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

const formatBool = (v) => (v ? t('yes') : t('no'))

const formatAnswerShort = (item) => {
    if (item.free_text_answer) return item.free_text_answer
    if (item.selected_answer?.text) return item.selected_answer.text
    if (item.selected_answer_id) return `#${item.selected_answer_id}`
    if (item.selected_answer_ids?.length) return item.selected_answer_ids.join(', ')
    return '—'
}

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

/** “подсветка” по статусу */
const attemptStatusClass = (status) => {
    const s = String(status || '')
    if (s === 'graded') return 'text-emerald-700 dark:text-emerald-200'
    if (s === 'completed') return 'text-sky-800 dark:text-sky-200'
    if (s === 'in_progress') return 'text-amber-800 dark:text-amber-200'
    return 'text-slate-500 dark:text-slate-300'
}

</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-200 dark:border-slate-600 relative">

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
                    <!-- Header -->
                    <header
                        class="flex items-center justify-between px-2 py-1
                               border-b border-dashed border-slate-400 dark:border-slate-500"
                    >
                        <div class="flex items-center gap-2">
                            <div
                                class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm
                                       border border-gray-400 bg-slate-200 dark:bg-slate-700
                                       text-slate-800 dark:text-blue-100"
                            >
                                ID: {{ item.id }}
                            </div>

                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-sm border
                                       text-[11px] font-semibold"
                                :class="item.is_correct
                                  ? 'border-green-500 bg-green-50 text-green-800 dark:bg-green-900/30 dark:text-green-200'
                                  : 'border-rose-500 bg-rose-50 text-rose-800 dark:bg-rose-900/30 dark:text-rose-200'"
                                :title="t('isCorrect')"
                            >
                                {{ formatBool(item.is_correct) }}
                            </span>
                        </div>

                        <input
                            type="checkbox"
                            :checked="selectedItems.includes(item.id)"
                            @change="$emit('toggle-select', item.id)"
                        />
                    </header>

                    <!-- Body -->
                    <div class="flex flex-col flex-1 px-3 py-2 space-y-2">

                        <!-- Content: question meta -->
                        <div class="flex flex-col items-center text-[11px] font-semibold">

                            <span class="text-orange-600 dark:text-orange-200">
                                {{ t('quiz') }} ID:{{ item.quiz_question_id }}
                            </span>

                            <span v-if="item.question?.type"
                                  class="text-slate-500 dark:text-slate-400 ml-2">
                              · {{ questionTypeLabel(item.question.type) }}
                            </span>

                            <span
                                v-if="item.question?.points !== null && item.question?.points !== undefined"
                                class="text-slate-700 dark:text-slate-300 ml-2"
                            >
                                · {{ t('points') }}: {{ item.question.points }}
                            </span>
                        </div>

                        <!-- Question text -->
                        <div class="font-semibold text-[12px]
                                    text-fuchsia-800 dark:text-fuchsia-200 line-clamp-3">
                            {{ item.question?.text || '—' }}
                        </div>

                        <!-- Answer block -->
                        <div class="font-semibold pt-1 border-t border-dotted
                                    border-slate-700/70 dark:border-slate-300/70">
                            <div class="text-[12px] text-teal-700 dark:text-teal-300">
                                {{ t('answer') }}:
                            </div>

                            <div class="line-clamp-4 text-[11px] text-slate-800 dark:text-slate-200">
                                {{ formatAnswerShort(item) || '—' }}
                            </div>
                        </div>

                        <!-- Reviewer comment -->
                        <div
                            v-if="item.reviewer_comment"
                            class="mt-1 line-clamp-2 text-[11px] font-semibold
                                   text-slate-600 dark:text-slate-400"
                        >
                            {{ t('comment') }}: {{ item.reviewer_comment }}
                        </div>

                        <!-- Attempt block (как “Попытка” в таблице) -->
                        <div class="font-semibold pt-1 border-t border-dotted
                                    border-slate-700/70 dark:border-slate-300/70">
                            <div class="flex flex-col items-center gap-0.5">
                                <span
                                    v-if="item.attempt?.attempt_number"
                                    class="text-[11px] text-slate-500 dark:text-slate-200"
                                >
                                    ID:{{ item.quiz_attempt_id }} · {{ t('attemptNumber')
                                    }} {{ item.attempt.attempt_number }}
                                </span>

                                <span
                                    v-if="item.attempt?.user"
                                    class="text-[11px] text-slate-500 dark:text-slate-200 text-center"
                                >
                                    {{ item.attempt.user.name || '—' }} ({{ item.attempt.user.email || '—' }})
                                </span>

                                <span
                                    v-if="item.attempt?.status"
                                    class="text-[11px] text-sky-800 dark:text-sky-200"
                                    :class="attemptStatusClass(item.attempt.status)"
                                >
                                  {{ attemptStatusLabel(item.attempt.status) }}
                                </span>

                                <span
                                    v-if="item.attempt?.quiz"
                                    class="text-[11px] text-slate-500
                                           dark:text-slate-200 text-center line-clamp-2"
                                >
                                    {{ ('Q#' + item.attempt.quiz.id) || item.attempt.quiz.title || item.attempt.quiz.slug
                                    }}
                                </span>
                            </div>
                        </div>

                        <!-- Score -->
                        <div class="text-center text-[11px] font-semibold">
                            <span class="text-amber-800 dark:text-amber-200">
                                {{ (item.score ?? '—') }} / {{ (item.max_score ?? '—') }}
                            </span>
                        </div>
                    </div>

                    <!-- Footer -->
                    <footer
                        class="flex items-center justify-center px-3 py-2
                               border-t border-dashed border-slate-400 dark:border-slate-500"
                    >
                        <div class="flex items-center space-x-1">
                            <IconEdit :href="route('admin.quizAttemptItems.edit', item.id)" />
                            <DeleteIconButton
                                @delete="$emit('delete', item.id, item.question?.text || '')"
                            />
                        </div>
                    </footer>
                </article>
            </div>
        </div>

        <div v-else class="p-5 text-center text-slate-700 dark:text-slate-100">
            {{ t('noData') }}
        </div>
    </div>
</template>
