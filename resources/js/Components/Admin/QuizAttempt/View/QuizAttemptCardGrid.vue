<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'

import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'

const { t } = useI18n()

const props = defineProps({
    attempts: {
        type: Array,
        default: () => []
    },
    selectedAttempts: {
        type: Array,
        default: () => []
    }
})

const emits = defineEmits([
    'delete',
    'toggle-select',
    'toggle-all',
])

/** Локальная копия — паттерн как Lessons */
const localAttempts = ref([])

watch(
    () => props.attempts,
    (newVal) => {
        localAttempts.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/** (если используешь чекбокс "выбрать всё" внутри гридки — можешь вызывать, но UI мы убрали) */
const toggleAll = (event) => {
    const checked = event.target.checked
    const ids = localAttempts.value.map(a => a.id)
    emits('toggle-all', { ids, checked })
}

/** Форматирование даты/времени */
const formatDateTime = (value) => {
    if (!value) return '—'
    const d = new Date(value)
    if (isNaN(d)) return '—'
    return d.toLocaleString('ru-RU')
}

/** Проценты */
const formatPercent = (value) => {
    if (value === null || typeof value === 'undefined') return '—'
    const n = Number(value)
    if (!Number.isFinite(n)) return '—'
    return `${n}%`
}

const statusLabel = (s) => {
    if (s === 'in_progress') return t('setStatusInProgress')
    if (s === 'completed') return t('setStatusCompleted')
    if (s === 'graded') return t('setStatusGraded')
    return s || '—'
}

</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-400 dark:border-slate-500 relative"
    >
        <div v-if="localAttempts.length" class="p-3">
            <div class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
                <article
                    v-for="attempt in localAttempts"
                    :key="attempt.id"
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
                        <div class="flex flex-col">
                            <span
                                class="inline-flex items-center px-2 py-0.5
                                       rounded-sm border border-blue-400
                                       text-[11px] font-semibold
                                       bg-blue-50 dark:bg-blue-900/40
                                       text-blue-800 dark:text-blue-100"
                            >
                                {{ statusLabel(attempt.status) }}
                            </span>
                        </div>

                        <div class="flex items-center gap-2">
                            <div
                                class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm
                                       border border-gray-400
                                       bg-slate-200 dark:bg-slate-700
                                       text-slate-800 dark:text-blue-100"
                            >
                                ID: {{ attempt.id }}
                            </div>

                            <input
                                type="checkbox"
                                :checked="selectedAttempts.includes(attempt.id)"
                                @change="$emit('toggle-select', attempt.id)"
                            />
                        </div>
                    </header>

                    <!-- Content -->
                    <div class="flex flex-col flex-1 px-3 py-2 space-y-2">
                        <div class="text-center" :title="attempt.quiz.slug">
                            <div class="text-[12px] font-semibold
                                        text-teal-700 dark:text-teal-300 line-clamp-2">
                                {{ attempt.quiz?.title || `#${attempt.quiz_id}` }}
                            </div>
                            <div class="mt-1 text-[11px] text-slate-500 dark:text-slate-300">
                                <span v-if="attempt.course?.title">{{ attempt.course.title }}</span>
                                <span v-if="attempt.module?.title"> · {{ attempt.module.title }}</span>
                                <span v-if="attempt.lesson?.title"> · {{ attempt.lesson.title }}</span>
                            </div>
                        </div>

                        <div class="text-center space-y-0.5">
                            <div class="text-[12px] font-medium
                                        text-orange-600 dark:text-orange-200">
                                {{ attempt.user?.name || `#${attempt.user_id}` }}
                            </div>
                            <div v-if="attempt.user?.email"
                                 class="text-[11px] text-slate-500 dark:text-slate-200">
                                {{ attempt.user.email }}
                            </div>
                        </div>

                        <div class="font-semibold text-center text-[11px]">
                            <div class="text-slate-700 dark:text-slate-300">
                                {{t('attemptNumber')}}{{ attempt.attempt_number ?? '—' }}
                            </div>
                        </div>

                        <div class="font-semibold text-center text-[11px]">
                            <div class="mb-1">
                                <span class="text-amber-800 dark:text-amber-200">
                                    {{ t('score') }}:
                                </span>
                                <span class="text-slate-700 dark:text-slate-200">
                                    {{ (attempt.score ?? '—') }} / {{ (attempt.max_score ?? '—') }}
                                </span>
                            </div>
                            <div class="text-green-700 dark:text-green-300">
                                {{ formatPercent(attempt.percent) }}
                            </div>
                        </div>

                        <div class="font-semibold text-left text-[10px]">
                            <span class="text-sky-700 dark:text-sky-200">
                                {{ t('shortStarted') }}:
                            </span>
                            <span class="text-slate-800 dark:text-slate-200">
                                {{ formatDateTime(attempt.started_at) }}
                            </span>
                        </div>

                        <div class="font-semibold text-left text-[10px]">
                            <span class="text-sky-700 dark:text-sky-200">
                                {{ t('shortExpires') }}:
                            </span>
                            <span class="text-slate-800 dark:text-slate-200">
                                {{ formatDateTime(attempt.finished_at) }}
                            </span>
                        </div>
                    </div>

                    <!-- Footer actions: edit + delete -->
                    <footer
                        class="flex items-center justify-center px-3 py-2
                               border-t border-dashed border-slate-400 dark:border-slate-500"
                    >
                        <div class="flex items-center space-x-1">
                            <IconEdit :href="route('admin.quizAttempts.edit', attempt.id)" />
                            <DeleteIconButton
                                @delete="$emit('delete', attempt.id, attempt.quiz?.title || '')"
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
