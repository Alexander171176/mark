<script setup>
import { defineEmits, defineProps, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'

import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'

const { t } = useI18n()

const props = defineProps({
    attempts: {
        type: Array,
        default: () => [],
    },
    selectedAttempts: {
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

const localAttempts = ref([])

watch(
    () => props.attempts,
    (newValue) => {
        localAttempts.value = JSON.parse(JSON.stringify(newValue || []))
    },
    { immediate: true, deep: true }
)

/* ==========================================================
 * SELECTION
 * ========================================================== */

const toggleAll = (event) => {
    emit('toggle-all', {
        ids: localAttempts.value.map(attempt => attempt.id),
        checked: event.target.checked,
    })
}

/* ==========================================================
 * RESOURCE HELPERS
 * ========================================================== */

const getNestedTitle = (item) => {
    return item?.translation?.title
        || item?.translation?.name
        || ''
}

const getQuizTitle = (attempt) => {
    return getNestedTitle(attempt?.quiz)
        || `Quiz ID: ${attempt?.school_quiz_id || '—'}`
}

const getCourseTitle = (attempt) => getNestedTitle(attempt?.course)
const getModuleTitle = (attempt) => getNestedTitle(attempt?.module)
const getLessonTitle = (attempt) => getNestedTitle(attempt?.lesson)

/* ==========================================================
 * FORMATTING
 * ========================================================== */

const formatDateTime = (value) => {
    if (!value) return '—'

    const date = new Date(value)

    if (Number.isNaN(date.getTime())) return '—'

    return date.toLocaleString('ru-RU')
}

const formatDuration = (seconds) => {
    const total = Number(seconds || 0)

    if (!total) return '—'

    const hours = Math.floor(total / 3600)
    const minutes = Math.floor((total % 3600) / 60)
    const secs = total % 60

    if (hours) return `${hours}ч ${minutes}м ${secs}с`
    if (minutes) return `${minutes}м ${secs}с`

    return `${secs}с`
}

const formatPercent = (value) => {
    if (value === null || typeof value === 'undefined') return '—'

    const number = Number(value)

    return Number.isFinite(number)
        ? `${number}%`
        : '—'
}

/* ==========================================================
 * STATUS
 * ========================================================== */

const statusLabel = (status) => {
    if (status === 'in_progress') return t('setStatusInProgress')
    if (status === 'completed') return t('setStatusCompleted')
    if (status === 'graded') return t('setStatusGraded')

    return status || '—'
}

const statusClass = (status) => {
    if (status === 'completed') {
        return 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/40 ' +
            'text-emerald-700 dark:text-emerald-100'
    }

    if (status === 'graded') {
        return 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/40 ' +
            'text-indigo-700 dark:text-indigo-100'
    }

    return 'border-amber-500 bg-amber-50 dark:bg-amber-900/40 text-amber-700 dark:text-amber-100'
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
                {{ t('selected') }}: {{ selectedAttempts.length }}
            </div>

            <label
                v-if="localAttempts.length"
                class="flex items-center text-xs text-slate-600
                       dark:text-slate-200 cursor-pointer"
            >
                <span>{{ t('selectAll') }}</span>

                <input
                    type="checkbox"
                    class="rounded-sm border-slate-400 mx-2"
                    :checked="localAttempts.length &&
                        localAttempts.every(attempt => selectedAttempts.includes(attempt.id))"
                    @change="toggleAll"
                />
            </label>
        </div>

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
                    <header
                        class="flex items-center justify-between px-2 py-1
                               border-b border-dashed border-slate-400
                               dark:border-slate-500"
                    >
                        <div
                            class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm
                                   border border-gray-400 bg-slate-200 dark:bg-slate-700
                                   text-slate-800 dark:text-blue-100"
                        >
                            ID: {{ attempt.id }}
                        </div>

                        <div class="flex items-center gap-2">
                            <span
                                :class="[
                                    'inline-flex items-center px-2 py-0.5 rounded-sm ' +
                                     'border text-[11px] font-semibold',
                                    statusClass(attempt.status),
                                ]"
                            >
                                {{ statusLabel(attempt.status) }}
                            </span>

                            <input
                                type="checkbox"
                                :checked="selectedAttempts.includes(attempt.id)"
                                @change="emit('toggle-select', attempt.id)"
                            />
                        </div>
                    </header>

                    <div class="flex flex-col flex-1 px-3 py-2 space-y-2">
                        <div class="text-center space-y-0.5">
                            <div
                                class="text-[12px] font-medium
                                       text-orange-600 dark:text-orange-200"
                            >
                                {{ attempt.user?.name || `User ID: ${attempt.user_id || '—'}` }}
                            </div>

                            <div
                                v-if="attempt.user?.email"
                                class="text-[11px] font-semibold
                                       text-slate-500 dark:text-slate-200"
                            >
                                {{ attempt.user.email }}
                            </div>
                        </div>

                        <div class="text-center" :title="attempt.quiz?.slug || ''">
                            <div
                                class="text-[12px] font-semibold
                                       text-blue-700 dark:text-blue-300"
                            >
                                {{ getQuizTitle(attempt) }}
                            </div>

                            <div
                                class="mt-1 text-[10px] font-semibold
                                       text-teal-700 dark:text-teal-300"
                            >
                                <span v-if="getCourseTitle(attempt)">
                                    {{ getCourseTitle(attempt) }}
                                </span>

                                <span v-if="getModuleTitle(attempt)">
                                    · {{ getModuleTitle(attempt) }}
                                </span>

                                <span v-if="getLessonTitle(attempt)">
                                    · {{ getLessonTitle(attempt) }}
                                </span>
                            </div>
                        </div>

                        <div
                            class="grid grid-cols-2 gap-2
                                   text-[11px] font-semibold text-center"
                        >
                            <div
                                class="rounded-sm border border-dashed
                                       border-slate-300 dark:border-slate-600 p-1"
                            >
                                <div class="text-slate-500 dark:text-slate-300">
                                    {{ t('attemptNumber') }}
                                </div>

                                <div class="text-rose-700 dark:text-rose-300">
                                    {{ attempt.attempt_number ?? '—' }}
                                </div>
                            </div>

                            <div
                                class="rounded-sm border border-dashed
                                       border-slate-300 dark:border-slate-600 p-1"
                            >
                                <div class="text-slate-500 dark:text-slate-300">
                                    {{ t('percent') }}
                                </div>

                                <div class="text-green-700 dark:text-green-300">
                                    {{ formatPercent(attempt.percent) }}
                                </div>
                            </div>
                        </div>

                        <div class="font-semibold text-center text-[11px]">
                            <span class="text-slate-700 dark:text-slate-200">
                                {{ t('score') }}:
                            </span>

                            <span class="text-amber-800 dark:text-amber-200">
                                {{ attempt.score ?? '—' }} / {{ attempt.max_score ?? '—' }}
                            </span>
                        </div>

                        <div class="font-semibold text-left text-[10px]">
                            <span class="text-slate-500 dark:text-slate-300">
                                {{ t('shortStarted') }}:
                            </span>

                            <span class="text-sky-700 dark:text-sky-200">
                                {{ formatDateTime(attempt.started_at) }}
                            </span>
                        </div>

                        <div class="font-semibold text-left text-[10px]">
                            <span class="text-slate-500 dark:text-slate-300">
                                {{ t('shortExpires') }}:
                            </span>

                            <span class="text-sky-700 dark:text-sky-200">
                                {{ formatDateTime(attempt.finished_at) }}
                            </span>
                        </div>

                        <div class="font-semibold text-center text-[10px]">
                            <span class="text-slate-500 dark:text-slate-300">
                                {{ t('duration') }}:
                            </span>

                            <span class="text-slate-900 dark:text-slate-100">
                                {{ formatDuration(attempt.duration_seconds) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-1 text-[10px] text-center">
                            <span
                                class="border border-dashed border-slate-300
                                       dark:border-slate-600 rounded-sm px-1 py-0.5"
                            >
                                User ID: {{ attempt.user_id ?? '—' }}
                            </span>

                            <span
                                class="border border-dashed border-slate-300
                                       dark:border-slate-600 rounded-sm px-1 py-0.5"
                            >
                                Quiz ID: {{ attempt.school_quiz_id ?? '—' }}
                            </span>
                        </div>

                        <div class="text-center text-[10px]">
                            <span
                                class="inline-flex border border-dashed
                                       border-slate-300 dark:border-slate-600
                                       rounded-sm px-2 py-0.5"
                            >
                                {{ t('items') }}: {{ attempt.items_count ?? 0 }}
                            </span>
                        </div>
                    </div>

                    <footer
                        class="flex items-center justify-center px-3 py-2
                               border-t border-dashed border-slate-400
                               dark:border-slate-500"
                    >
                        <div class="flex items-center space-x-1">
                            <IconEdit
                                :href="route('admin.schoolQuizAttempts.edit', {
                                    schoolQuizAttempt: attempt.id,
                                })"
                            />

                            <DeleteIconButton
                                :title="t('delete')"
                                @delete="emit('delete', attempt)"
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
