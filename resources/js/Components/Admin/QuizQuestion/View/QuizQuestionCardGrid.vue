<script setup>
import { defineEmits, defineProps, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import ActivityToggle from '@/Components/Admin/Buttons/ActivityToggle.vue'
import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'
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
    'delete',
    'update-sort-order',
    'toggle-select',
    'toggle-all',
    'clone'
])

/** Локальная копия для vuedraggable */
const localQuestions = ref([])

watch(
    () => props.questions,
    (newVal) => {
        localQuestions.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/** Drag end: отдаём массив ID вверх */
const handleDragEnd = () => {
    const newOrderIds = localQuestions.value.map(q => q.id)
    emits('update-sort-order', newOrderIds)
}

/** Массовые чекбоксы */
const toggleAll = (event) => {
    const checked = event.target.checked
    const ids = localQuestions.value.map(l => l.id)
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

    const list = correct
        .map(a => a.text || a.value || '')
        .filter(Boolean)

    if (!list.length) return '—'

    const sliced = list.slice(0, 3).join(', ')
    return sliced + (list.length > 3 ? '…' : '')
}

// 🔹 Мапа: значение из БД → ключ перевода (flat i18n)
const questionTypeLabelKeyMap = {
    single_choice: 'questionTypeSingleChoice',   // один правильный ответ
    multiple_choice: 'questionTypeMultipleChoice', // несколько правильных ответов
    true_false: 'questionTypeTrueFalse',        // верно/неверно
    open_text: 'questionTypeOpenText',          // развернутый ответ
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
               border border-slate-400 dark:border-slate-500 relative">

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

        <div v-if="localQuestions.length" class="p-3">
            <draggable
                tag="div"
                v-model="localQuestions"
                item-key="id"
                @end="handleDragEnd"
                handle=".drag-handle"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4"
            >
                <template #item="{ element: q }">
                    <div
                        class="relative flex flex-col h-full rounded-md
                               border border-slate-400 dark:border-slate-500
                               bg-slate-50/70 dark:bg-slate-800/80 shadow-sm
                               hover:shadow-md transition-shadow duration-150">

                        <!-- Верхняя панель карточки -->
                        <div class="flex items-center justify-between px-2 py-1
                                    border-b border-dashed border-slate-400 dark:border-slate-500">
                            <div class="flex items-center space-x-2">
                                <!-- drag handle -->
                                <button
                                    type="button"
                                    class="drag-handle text-slate-400 hover:text-slate-700
                                           dark:hover:text-slate-100"
                                    :title="t('dragDrop')">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"
                                        />
                                    </svg>
                                </button>

                                <!-- ID -->
                                <div
                                    class="text-[10px] font-semibold px-2 py-0.5 rounded-sm
                                           border border-gray-400
                                           bg-slate-200 dark:bg-slate-700
                                           text-slate-800 dark:text-blue-100"
                                    :title="`sort: ${q.sort ?? '—'}`">
                                    ID: {{ q.id }}
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                <input
                                    type="checkbox"
                                    :checked="selectedQuestions.includes(q.id)"
                                    @change="$emit('toggle-select', q.id)"
                                />
                            </div>
                        </div>

                        <!-- Контент карточки -->
                        <div class="flex flex-col flex-1 px-3 py-2 space-y-2 text-[11px]">

                            <!-- Квиз -->
                            <div
                                class="text-[11px] text-center font-semibold
                                       text-sky-700 dark:text-sky-200 line-clamp-2"
                                :title="q.quiz?.title || ('#' + q.quiz_id)"
                            >
                                {{ q.quiz?.title || ('Quiz ID: ' + q.quiz_id) }}
                            </div>

                            <!-- Вопрос -->
                            <div
                                class="text-xs text-slate-800 dark:text-slate-100
                                       font-semibold border border-dashed border-slate-400
                                       bg-slate-100/70 dark:bg-slate-900/40
                                       rounded-sm px-2 py-1 min-h-[3rem]"
                                :title="q.question_text"
                            >
                                {{ shortText(q.question_text) }}
                            </div>

                            <!-- Ответы -->
                            <div
                                class="text-center text-[10px] px-2 py-0.5 rounded-sm
                                       font-semibold border border-slate-400
                                       bg-fuchsia-50 dark:bg-fuchsia-900/40
                                       text-fuchsia-700 dark:text-fuchsia-200">
                                <span>
                                    {{ getQuestionTypeLabel(q.question_type) }}
                                </span>
                            </div>

                            <!-- Ответы и Баллы -->
                            <div class="flex flex-wrap justify-between gap-2 mt-1 text-[10px]">

                                <!-- Ответы -->
                                <span
                                    class="text-[10px] px-2 py-0.5 rounded-sm
                                           border border-slate-400
                                           bg-emerald-50 dark:bg-emerald-900/40
                                           text-emerald-700 dark:text-emerald-200">
                                    <span class="font-semibold">{{ t('answers') }}: </span>
                                    <span>
                                        {{ getCorrectAnswersSummary(q) }}
                                    </span>
                                </span>

                                <span
                                    class="px-2 py-0.5 rounded-sm
                                           bg-amber-100 dark:bg-amber-700
                                           border border-gray-400
                                           text-amber-700 dark:text-amber-200"
                                    :title="t('points')"
                                >
                                    {{ t('points') }}: {{ q.points ?? 0 }}
                                </span>

                            </div>
                        </div>

                        <!-- Действия -->
                        <div
                            class="flex items-center justify-center px-3 py-2
                                   border-t border-dashed border-slate-400 dark:border-slate-500">
                            <div class="flex items-center space-x-1">
                                <ActivityToggle
                                    :isActive="q.activity"
                                    @toggle-activity="$emit('toggle-activity', q)"
                                    :title="q.activity ? t('enabled') : t('disabled')"
                                />
                                <CloneIconButton @clone="$emit('clone', q)" />
                                <IconEdit :href="route('admin.quizQuestions.edit', q.id)" />
                                <DeleteIconButton
                                    @delete="$emit('delete', q.id, q.question_text)"
                                />
                            </div>
                        </div>
                    </div>
                </template>
            </draggable>
        </div>

        <div v-else class="p-5 text-center text-slate-700 dark:text-slate-100">
            {{ t('noData') }}
        </div>
    </div>
</template>
