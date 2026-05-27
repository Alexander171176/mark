<script setup>
import { defineEmits, defineProps, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import ActivityToggle from '@/Components/Admin/UI/Buttons/ActivityToggle.vue'
import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'

const { t } = useI18n()

const props = defineProps({
    answers: { type: Array, default: () => [] },
    selectedAnswers: { type: Array, default: () => [] },
})

const emit = defineEmits([
    'toggle-activity',
    'delete',
    'update-sort-order',
    'toggle-select',
    'toggle-all',
])

const localAnswers = ref([])

watch(
    () => props.answers,
    (newVal) => {
        localAnswers.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

const handleDragEnd = () => {
    emit('update-sort-order', localAnswers.value.map(answer => answer.id))
}

const toggleAll = (event) => {
    emit('toggle-all', {
        ids: localAnswers.value.map(answer => answer.id),
        checked: event.target.checked,
    })
}

const stripHtml = (html) => {
    if (!html) return ''

    return html
        .replace(/<\/p>/gi, '\n')
        .replace(/<br\s*\/?>/gi, '\n')
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

const shortText = (html, length = 120) => {
    const clean = stripHtml(html)

    return clean.length > length ? clean.slice(0, length) + '…' : clean
}
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-400 dark:border-slate-500 relative"
    >
        <div
            class="flex items-center justify-between px-3 py-2
                   border-b border-slate-400 dark:border-slate-500"
        >
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('selected') }}: {{ selectedAnswers.length }}
            </div>

            <label
                v-if="localAnswers.length"
                class="flex items-center text-xs text-slate-600
                       dark:text-slate-200 cursor-pointer"
            >
                <span>{{ t('selectAll') }}</span>
                <input type="checkbox" class="mx-2" @change="toggleAll" />
            </label>
        </div>

        <div v-if="localAnswers.length" class="p-3">
            <draggable
                v-model="localAnswers"
                tag="div"
                item-key="id"
                handle=".drag-handle"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                @end="handleDragEnd"
            >
                <template #item="{ element: answer }">
                    <article
                        class="relative flex flex-col h-full rounded-md
                               border border-slate-400 dark:border-slate-500
                               bg-slate-50/70 dark:bg-slate-800/80 shadow-sm
                               hover:shadow-md transition-shadow duration-150"
                    >
                        <header
                            class="flex items-center justify-between px-2 py-1
                                   border-b border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-2">
                                <button
                                    type="button"
                                    class="drag-handle text-slate-400 hover:text-slate-700
                                           dark:hover:text-slate-100"
                                    :title="t('dragDrop')"
                                >
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"
                                        />
                                    </svg>
                                </button>

                                <div
                                    class="text-[10px] font-semibold px-2 py-0.5 rounded-sm
                                           border border-gray-400
                                           bg-slate-200 dark:bg-slate-700
                                           text-slate-800 dark:text-blue-100"
                                    :title="`sort: ${answer.sort ?? '—'}`"
                                >
                                    ID: {{ answer.id }}
                                </div>
                            </div>

                            <input
                                type="checkbox"
                                :checked="selectedAnswers.includes(answer.id)"
                                @change="emit('toggle-select', answer.id)"
                            />
                        </header>

                        <div class="flex flex-col flex-1 px-3 py-2 space-y-2 text-[11px]">
                            <div
                                class="text-[11px] text-center font-semibold
                                       text-blue-600 dark:text-blue-300"
                                :title="answer.quiz?.title || ('#' + answer.school_quiz_id)"
                            >
                                {{ answer.quiz?.title || ('Quiz ID: ' + answer.school_quiz_id) }}
                            </div>

                            <div
                                class="text-xs text-slate-800 dark:text-slate-100
                                       font-semibold border border-dashed border-slate-400
                                       bg-slate-100/70 dark:bg-slate-900/40
                                       rounded-sm px-2 py-1 min-h-[3rem]"
                                :title="stripHtml(answer.question?.question_text)"
                            >
        {{ shortText(answer.question?.question_text) || ('ID: ' + answer.school_quiz_question_id) }}
                            </div>

                            <div
                                class="text-[11px] text-center font-semibold
                                       text-indigo-700 dark:text-indigo-200"
                                :title="stripHtml(answer.text)"
                            >
                                {{ shortText(answer.text) }}
                            </div>

                            <div
                                v-if="answer.explanation"
                                class="text-[10px] text-slate-600 dark:text-slate-300"
                                :title="stripHtml(answer.explanation)"
                            >
                                {{ shortText(answer.explanation, 90) }}
                            </div>

                            <div class="flex flex-wrap justify-between gap-2 mt-1 text-[10px]">
                                <span
                                    :class="[
                                        'px-2 py-0.5 rounded-sm font-semibold border',
                                        answer.is_correct
                                            ? 'border-emerald-500 ' +
                                             'bg-emerald-50 dark:bg-emerald-900/40 ' +
                                              'text-emerald-700 dark:text-emerald-200'
                                            : 'border-rose-500 bg-rose-50 dark:bg-rose-900/40 ' +
                                             'text-rose-700 dark:text-rose-200'
                                    ]"
                                >
                                    {{ t('isCorrect') }}:
                                    {{ answer.is_correct ? t('yes') : t('no') }}
                                </span>

                                <span
                                    class="px-2 py-0.5 rounded-sm
                                           bg-amber-100 dark:bg-amber-900
                                           border border-gray-400
                                           text-amber-700 dark:text-amber-300"
                                >
                                    {{ t('points') }}: {{ answer.weight ?? 0 }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-1 text-[10px] text-center">
                                <span
                                    class="border border-dashed border-slate-300
                                           dark:border-slate-600 rounded-sm px-1 py-0.5"
                                >
                                    {{ t('quiz') }} ID: {{ answer.school_quiz_id ?? '—' }}
                                </span>

                                <span
                                    class="border border-dashed border-slate-300
                                           dark:border-slate-600 rounded-sm px-1 py-0.5"
                                >
                                {{ t('question') }} ID: {{ answer.school_quiz_question_id ?? '—' }}
                                </span>
                            </div>
                        </div>

                        <footer
                            class="flex items-center justify-center px-3 py-2
                                   border-t border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-1">
                                <ActivityToggle
                                    :isActive="answer.activity"
                                    :title="answer.activity ? t('enabled') : t('disabled')"
                                    @toggle-activity="emit('toggle-activity', answer)"
                                />

                                <IconEdit
                                    :href="route('admin.schoolQuizAnswers.edit', {
                                        schoolQuizAnswer: answer.id,
                                    })"
                                />

                                <DeleteIconButton
                                    :title="t('delete')"
                                    @delete="emit('delete', answer)"
                                />
                            </div>
                        </footer>
                    </article>
                </template>
            </draggable>
        </div>

        <div v-else class="p-5 text-center text-slate-700 dark:text-slate-100">
            {{ t('noData') }}
        </div>
    </div>
</template>
