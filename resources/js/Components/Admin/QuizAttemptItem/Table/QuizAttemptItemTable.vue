<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'

import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'

const { t } = useI18n()

const props = defineProps({
    items: { type: Array, default: () => [] },
    selectedItems: { type: Array, default: () => [] }
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

const toggleAll = (event) => {
    const checked = event.target.checked
    const ids = localItems.value.map(i => i.id)
    emits('toggle-all', { ids, checked })
}

const formatBool = (v) => (v ? t('yes') : t('no'))

/** Короткий вывод ответа (как было) */
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
           border border-slate-200 dark:border-slate-600 relative"
    >
        <div class="overflow-x-auto">
            <table
                v-if="items.length > 0"
                class="table-auto w-full text-slate-700 dark:text-slate-100"
            >
                <thead
                    class="text-sm uppercase bg-slate-200 dark:bg-cyan-900
                 border border-solid border-gray-300 dark:border-gray-700"
                >
                <tr>
                    <th class="px-2 py-3 w-px">
                        <div class="font-medium text-center">{{ t('id') }}</div>
                    </th>

                    <!-- Содержание -->
                    <th class="px-2 py-3 first:pl-6 last:pr-6">
                        <div class="font-medium text-left">
                            {{ t('content') }}
                        </div>
                    </th>

                    <!-- Попытка / контекст -->
                    <th class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="font-medium text-center">
                            {{ t('attempt') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="font-medium text-center">
                            {{ t('isCorrect') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="font-medium text-center">
                            {{ t('points') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="font-medium text-end">
                            {{ t('actions') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="text-center">
                            <input type="checkbox" @change="toggleAll" />
                        </div>
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
                    <td class="px-2 py-3 w-px">
                        <div class="text-left text-xs text-slate-800 dark:text-blue-200">
                            {{ item.id }}
                        </div>
                    </td>

                    <!-- Содержание: вопрос + ответ + тип/баллы/комментарий -->
                    <td class="px-2 py-3 first:pl-6 last:pr-6">
                        <div class="flex flex-col gap-1">
                            <!-- Вопрос -->
                            <div class="text-[11px] text-slate-500 dark:text-slate-300">
                              <span class="text-orange-600 dark:text-orange-200 font-semibold">
                                {{ t('quiz') }} ID:{{ item.quiz_question_id }}
                              </span>
                                <span v-if="item.question?.type" class="ml-2">
                                  · {{ questionTypeLabel(item.question.type) }}
                                </span>
                              <span v-if="item.question?.points !== null &&
                              item.question?.points !== undefined"
                                      class="ml-2">
                                  · {{ t('points') }}: {{ item.question.points }}
                              </span>
                            </div>

                            <div
                                class="text-[12px] text-fuchsia-800 dark:text-fuchsia-200 line-clamp-2"
                            >
                                {{ item.question?.text || '—' }}
                            </div>

                            <!-- Ответ -->
                            <div class="pt-1 border-t border-dashed
                                        border-slate-600/70 dark:border-slate-300/70">
                                <div class="text-[11px] text-teal-700 dark:text-teal-300">
                                    {{ t('answer') }}:
                                </div>
                                <div class="line-clamp-3 text-[12px]
                                            text-slate-800 dark:text-slate-200">
                                    {{ formatAnswerShort(item) || '—' }}
                                </div>
                            </div>

                            <!-- Комментарий ревьюера -->
                            <div
                                v-if="item.reviewer_comment"
                                class="mt-1 line-clamp-2 text-[11px]
                                       text-slate-600 dark:text-slate-400"
                            >
                                {{ t('comment') }}: {{ item.reviewer_comment }}
                            </div>
                        </div>
                    </td>

                    <!-- Попытка -->
                    <td class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="flex flex-col items-center">

                            <span v-if="item.attempt?.attempt_number"
                                  class="text-[11px] text-slate-500 dark:text-slate-200">
                              ID:{{ item.quiz_attempt_id }} ·
                                {{ t('attemptNumber') }} {{ item.attempt.attempt_number }}
                            </span>

                            <span v-if="item.attempt?.user"
                                  class="text-[11px] text-slate-500 dark:text-slate-200">
                              {{ item.attempt.user.name || '—' }} ({{ item.attempt.user.email || '—' }})
                            </span>

                            <span
                                v-if="item.attempt?.status"
                                class="text-[11px]"
                                :class="attemptStatusClass(item.attempt.status)"
                            >
                              {{ attemptStatusLabel(item.attempt.status) }}
                            </span>

                            <span v-if="item.attempt?.quiz"
                                  class="text-[11px] text-slate-500 dark:text-slate-200">
          {{ ('Q#' + item.attempt.quiz.id) || item.attempt.quiz.title || item.attempt.quiz.slug }}
                            </span>

                        </div>
                    </td>

                    <td class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div
                            class="text-center text-xs font-semibold px-2 py-0.5 rounded-sm border"
                            :class="item.is_correct
          ? 'border-green-500 bg-green-50 text-green-800 dark:bg-green-900/30 dark:text-green-200'
          : 'border-rose-500 bg-rose-50 text-rose-800 dark:bg-rose-900/30 dark:text-rose-200'"
                        >
                            {{ formatBool(item.is_correct) }}
                        </div>
                    </td>

                    <td class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="text-center text-xs text-amber-800 dark:text-amber-200">
                            {{ (item.score ?? '—') }} / {{ (item.max_score ?? '—') }}
                        </div>
                    </td>

                    <td class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="flex justify-center space-x-2">
                            <IconEdit :href="route('admin.quizAttemptItems.edit', item.id)" />
                            <DeleteIconButton
                                @delete="$emit('delete', item.id, item.question?.text || '')"
                            />
                        </div>
                    </td>

                    <td class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="text-center">
                            <input
                                type="checkbox"
                                :checked="selectedItems.includes(item.id)"
                                @change="$emit('toggle-select', item.id)"
                            />
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>

            <div v-else class="p-5 text-center text-slate-700 dark:text-slate-100">
                {{ t('noData') }}
            </div>
        </div>
    </div>
</template>
