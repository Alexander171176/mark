<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'
import ActivityToggle from '@/Components/Admin/Buttons/ActivityToggle.vue'

const { t } = useI18n()

const props = defineProps({
    answers: {
        type: Array,
        default: () => []
    },
    selectedAnswers: {
        type: Array,
        default: () => []
    }
})

const emits = defineEmits([
    'toggle-activity',
    'delete',
    'update-sort-order',
    'toggle-select',
    'toggle-all'
])

/** Локальная копия для drag&drop */
const localAnswers = ref([])

watch(
    () => props.answers,
    (newVal) => {
        localAnswers.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true }
)

/** Drag end → отправка нового порядка */
const handleDragEnd = () => {
    const newOrderIds = localAnswers.value.map(a => a.id)
    emits('update-sort-order', newOrderIds)
}

/** Массовые чекбоксы */
const toggleAll = (event) => {
    const checked = event.target.checked
    const ids = localAnswers.value.map(a => a.id)
    emits('toggle-all', { ids, checked })
}

// Полное очищение текста от HTML + сущностей
const stripHtml = (html) => {
    if (!html) return ''

    return html
        // Переносы строк
        .replace(/<\/p>/gi, '\n')
        .replace(/<br\s*\/?>/gi, '\n')
        // Удаляем все теги
        .replace(/<[^>]+>/g, '')
        // Заменяем HTML-сущности
        .replace(/&nbsp;/gi, ' ')
        .replace(/&amp;/gi, '&')
        .replace(/&quot;/gi, '"')
        .replace(/&#039;/gi, "'")
        .replace(/&lt;/gi, '<')
        .replace(/&gt;/gi, '>')
        // Убираем двойные пробелы
        .replace(/\s+/g, ' ')
        .trim()
}

// Короткий текст
const shortText = (html, length = 120) => {
    const clean = stripHtml(html)
    return clean.length > length ? clean.slice(0, length) + '…' : clean
}
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-200 dark:border-slate-600 relative"
    >

        <!-- Верхняя панель: кол-во выбранных + чекбокс -->
        <div class="flex items-center justify-between px-3 py-2
                    border-b border-slate-400 dark:border-slate-500">
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('selected') }}: {{ selectedAnswers.length }}
            </div>

            <label
                v-if="localAnswers.length"
                class="flex items-center text-xs text-slate-600
                       dark:text-slate-200 cursor-pointer">
                <span>{{ t('selectAll') }}</span>
                <input type="checkbox" class="mx-2" @change="toggleAll" />
            </label>
        </div>

        <div class="overflow-x-auto">
            <table
                v-if="answers.length > 0"
                class="table-auto w-full text-slate-700 dark:text-slate-100"
            >
                <thead
                    class="text-xs uppercase bg-slate-200 dark:bg-cyan-900
                           border border-solid border-gray-300 dark:border-gray-700"
                >
                <tr>
                    <!-- Drag handle -->
                    <th class="px-2 py-3 w-px text-center">
                        <svg class="w-4 h-4 opacity-60" viewBox="0 0 24 24">
                            <path
                                d="M12.707 2.293a1 1 0 0 0-1.414 0l-5 5A1 1 0 1 0 7.707 8.707L12 4.414l4.293 4.293a1 1 0 0 0 1.414-1.414l-5-5z"
                            />
                            <path
                                d="M16.293 15.293 12 19.586l-4.293-4.293a1 1 0 0 0-1.414 1.414l5 5a1 1 0 0 0 1.414 0l5-5a1 1 0 0 0-1.414-1.414z"
                            />
                        </svg>
                    </th>

                    <th class="font-medium px-2 py-3 w-px text-center">
                        ID
                    </th>

                    <th class="font-medium px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap text-center">
                        {{ t('quiz') }}
                    </th>

                    <th class="font-medium px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap text-center">
                        {{ t('quizQuestion') }}
                    </th>

                    <th class="font-medium px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap text-center">
                        {{ t('answer') }}
                    </th>

                    <th class="font-medium px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap text-center">
                        {{ t('isCorrect') }}
                    </th>

                    <th class="font-medium px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap text-center">
                        {{ t('points') }}
                    </th>

                    <th class="font-medium px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap text-end">
                        {{ t('actions') }}
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap text-center">
                        <input type="checkbox" @change="toggleAll" />
                    </th>
                </tr>
                </thead>

                <draggable
                    tag="tbody"
                    v-model="localAnswers"
                    @end="handleDragEnd"
                    item-key="id"
                    handle=".handle"
                >
                    <template #item="{ element: a }">
                        <tr
                            class="text-sm font-semibold border-b-2
                                   hover:bg-slate-100 dark:hover:bg-cyan-800"
                        >
                            <!-- drag -->
                            <td class="px-2 py-1 text-center cursor-move handle">
                                <svg
                                    class="w-4 h-4 text-gray-500 dark:text-gray-300"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"
                                    />
                                </svg>
                            </td>

                            <!-- ID -->
                            <td class="px-2 py-3 text-center text-xs
                                       text-indigo-800 dark:text-indigo-200"
                                :title="a.sort">
                                {{ a.id }}
                            </td>

                            <!-- Quiz title -->
                            <td class="px-2 py-3 first:pl-7 last:pr-7 text-center text-xs
                                       text-blue-600 dark:text-blue-300">
                                {{ a.quiz?.title || ('#' + a.quiz_id) }}
                            </td>

                            <!-- Question text -->
                            <td
                                class="px-2 py-3 first:pl-7 last:pr-7
                                       text-xs text-center font-semibold"
                                :title="stripHtml(a.question?.question_text)"
                            >
                        {{ shortText(a.question?.question_text) || ('ID: ' + a.quiz_question_id) }}
                            </td>

                            <!-- Answer text -->
                            <td
                                class="px-2 py-3 first:pl-7 last:pr-7 text-xs
                                       text-slate-500 dark:text-slate-200 text-center"
                                :title="stripHtml(a.text)"
                            >
                                {{ shortText(a.text) }}
                            </td>

                            <!-- Correct? -->
                            <td class="px-2 py-3 first:pl-7 last:pr-7 text-center text-xs">
                                <span
                                    :class="[
                                        'px-2 pb-0.5 rounded-sm text-[12px] font-semibold ' +
                                         'border-2 border-gray-300 dark:border-gray-400',
                                        a.is_correct
                                            ? 'bg-emerald-100 text-emerald-700 ' +
                                             'dark:bg-emerald-700/40 dark:text-emerald-100'
                                            : 'bg-rose-100 text-rose-700 ' +
                                             'dark:bg-rose-700/40 dark:text-rose-100'
                                    ]"
                                >
                                    {{ a.is_correct ? t('yes') : t('no') }}
                                </span>
                            </td>

                            <!-- Weight -->
                            <td class="px-2 py-3 first:pl-7 last:pr-7 text-center text-xs
                                       text-amber-600 dark:text-amber-300">
                                {{ a.weight ?? 0 }}
                            </td>

                            <!-- Actions -->
                            <td class="px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap">
                                <div class="flex justify-end space-x-2">
                                    <ActivityToggle
                                        :isActive="a.activity"
                                        @toggle-activity="$emit('toggle-activity', a)"
                                    />

                                    <IconEdit :href="route('admin.quizAnswers.edit', a.id)" />

                                    <DeleteIconButton
                                        @delete="$emit('delete', a.id)"
                                    />
                                </div>
                            </td>

                            <!-- Checkbox -->
                            <td class="px-2 py-3 text-center">
                                <input
                                    type="checkbox"
                                    :checked="selectedAnswers.includes(a.id)"
                                    @change="$emit('toggle-select', a.id)"
                                />
                            </td>
                        </tr>
                    </template>
                </draggable>
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
