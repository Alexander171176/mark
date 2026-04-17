<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'
import ActivityToggle from '@/Components/Admin/Buttons/ActivityToggle.vue'
import CloneIconButton from '@/Components/Admin/Buttons/CloneIconButton.vue'

const { t } = useI18n()

const props = defineProps({
    questions: {
        type: Array,
        default: () => []
    },
    selectedQuestions: {
        type: Array,
        default: () => []
    }
})

const emits = defineEmits([
    'toggle-activity',
    'edit',
    'delete',
    'update-sort-order',
    'toggle-select',
    'toggle-all',
    'clone'
])

/** Локальная копия для drag&drop */
const localQuestions = ref([])

watch(
    () => props.questions,
    (newVal) => {
        localQuestions.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true }
)

/** Drag end → отправка нового порядка */
const handleDragEnd = () => {
    const newOrderIds = localQuestions.value.map(q => q.id)
    emits('update-sort-order', newOrderIds)
}

/** Массовые чекбоксы */
const toggleAll = (event) => {
    const checked = event.target.checked
    const ids = localQuestions.value.map(q => q.id)
    emits('toggle-all', { ids, checked })
}

// Полное очищение текста от HTML + сущностей
const stripHtml = (html) => {
    if (!html) return '';

    return html
        // Переносы строк
        .replace(/<\/p>/gi, '\n')
        .replace(/<br\s*\/?>/gi, '\n')
        // Удаляем все теги
        .replace(/<[^>]+>/g, '')
        // Заменяем HTML-сущности
        .replace(/&nbsp;/gi, ' ')   // ← Убираем неразрывный пробел
        .replace(/&amp;/gi, '&')
        .replace(/&quot;/gi, '"')
        .replace(/&#039;/gi, "'")
        .replace(/&lt;/gi, '<')
        .replace(/&gt;/gi, '>')
        // Убираем двойные пробелы
        .replace(/\s+/g, ' ')
        .trim();
};

// Короткий текст
const shortText = (html, length = 120) => {
    const clean = stripHtml(html);
    return clean.length > length ? clean.slice(0, length) + '…' : clean;
};

/** Краткое отображение правильных ответов */
const getCorrectAnswersSummary = (question) => {
    if (!question.answers || !question.answers.length) return '—'
    const correct = question.answers.filter(a => a.is_correct)
    if (!correct.length) return '—'

    return correct
        .map(a => a.text || a.value || '')
        .filter(Boolean)
        .slice(0, 3)
        .join(', ') + (correct.length > 3 ? '…' : '')
}

// 🔹 Мапа: значение из БД → ключ перевода (flat i18n)
const questionTypeLabelKeyMap = {
    single_choice: 'questionTypeSingleChoice',     // один правильный ответ
    multiple_choice: 'questionTypeMultipleChoice', // несколько правильных ответов
    true_false: 'questionTypeTrueFalse',           // верно/неверно
    open_text: 'questionTypeOpenText',             // развернутый ответ
}

// 🔹 Хелпер: вернуть человекочитаемую подпись типа вопроса
const getQuestionTypeLabel = (type) => {
    if (!type) return '—'

    const key = questionTypeLabelKeyMap[type]

    // если есть ключ — берём перевод
    if (key) return t(key)

    // на всякий случай fallback — сырое значение
    return type
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
                {{ t('selected') }}: {{ selectedQuestions.length }}
            </div>

            <label
                v-if="localQuestions.length"
                class="flex items-center text-xs text-slate-600
                       dark:text-slate-200 cursor-pointer">
                <span>{{ t('selectAll') }}</span>
                <input type="checkbox" class="mx-2" @change="toggleAll" />
            </label>
        </div>

        <div class="overflow-x-auto">
            <table
                v-if="questions.length > 0"
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

                    <th class="font-medium px-2 py-3 whitespace-nowrap text-center">
                        {{ t('quiz') }}
                    </th>

                    <th class="font-medium px-2 py-3 whitespace-nowrap text-center">
                        {{ t('question') }}
                    </th>

                    <th class="font-medium px-2 py-3 whitespace-nowrap text-center">
                        {{ t('questionType') }}
                    </th>

                    <th class="font-medium px-2 py-3 whitespace-nowrap text-center">
                        {{ t('answers') }}
                    </th>

                    <th class="font-medium px-2 py-3 whitespace-nowrap text-center">
                        {{ t('points') }}
                    </th>

                    <th class="font-medium px-2 py-3 whitespace-nowrap text-end">
                        {{ t('actions') }}
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap text-center">
                        <input type="checkbox" @change="toggleAll" />
                    </th>
                </tr>
                </thead>

                <draggable
                    tag="tbody"
                    v-model="localQuestions"
                    @end="handleDragEnd"
                    item-key="id"
                    handle=".handle"
                >
                    <template #item="{ element: q }">
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
                            <td class="px-2 py-3 text-center text-xs" :title="q.sort">
                                {{ q.id }}
                            </td>

                            <!-- Quiz title -->
                            <td class="px-2 py-3 text-center text-xs
                                       text-blue-600 dark:text-blue-300">
                                {{ q.quiz?.title || ('#' + q.quiz_id) }}
                            </td>

                            <!-- Question text -->
                            <td class="px-2 py-3 text-xs">
                                {{ shortText(q.question_text) }}
                            </td>

                            <!-- Type -->
                            <td class="px-2 py-3 text-center text-xs
                                       text-fuchsia-700 dark:text-fuchsia-200">
                                {{ getQuestionTypeLabel(q.question_type) }}
                            </td>

                            <!-- Correct Answers -->
                            <td class="px-2 py-3 text-center text-xs
                                       text-emerald-600 dark:text-emerald-300">
                                {{ getCorrectAnswersSummary(q) }}
                            </td>

                            <!-- Points -->
                            <td class="px-2 py-3 text-center text-xs
                                       text-amber-600 dark:text-amber-300">
                                {{ q.points ?? 0 }}
                            </td>

                            <!-- Actions -->
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="flex justify-end space-x-2">
                                    <ActivityToggle
                                        :isActive="q.activity"
                                        @toggle-activity="$emit('toggle-activity', q)"
                                    />

                                    <CloneIconButton @clone="$emit('clone', q)" />

                                    <IconEdit :href="route('admin.quizQuestions.edit', q.id)" />

                                    <DeleteIconButton
                                        @delete="$emit('delete', q.id, q.question_text)"
                                    />
                                </div>
                            </td>

                            <!-- Checkbox -->
                            <td class="px-2 py-3 text-center">
                                <input
                                    type="checkbox"
                                    :checked="selectedQuestions.includes(q.id)"
                                    @change="$emit('toggle-select', q.id)"
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
