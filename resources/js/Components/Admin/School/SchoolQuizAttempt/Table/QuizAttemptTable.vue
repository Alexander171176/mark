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

    return 'border-amber-500 bg-amber-50 dark:bg-amber-900/40 ' +
        'text-amber-700 dark:text-amber-100'
}
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-200 dark:border-slate-600 relative"
    >
        <div class="overflow-x-auto">
            <table
                v-if="localAttempts.length"
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
                        <div class="font-medium text-sm text-left">
                            {{ t('users') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-medium text-sm text-left">
                            {{ t('quiz') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-medium text-sm text-center">
                            {{ t('date') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-medium text-sm text-center">
                            {{ t('status') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-medium text-sm text-center">
                            {{ t('attemptNumber') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-medium text-sm text-center">
                            {{ t('points') }}
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
                            :checked="localAttempts.length &&
                                    localAttempts.every(attempt =>
                                    selectedAttempts.includes(attempt.id))"
                            @change="toggleAll"
                        />
                    </th>
                </tr>
                </thead>

                <tbody>
                <tr
                    v-for="attempt in localAttempts"
                    :key="attempt.id"
                    class="text-sm font-semibold border-b-2
                               hover:bg-slate-100 dark:hover:bg-cyan-800"
                >
                    <td class="px-2 py-3 w-px text-center">
                        <div class="text-xs text-slate-800 dark:text-blue-200">
                            {{ attempt.id }}
                        </div>
                    </td>

                    <td class="px-2 py-3 whitespace-nowrap">
                        <div class="flex flex-col">
                                <span class="text-sm text-orange-600 dark:text-orange-200">
                                    {{ attempt.user?.name || `User ID: ${attempt.user_id || '—'}` }}
                                </span>

                            <span
                                v-if="attempt.user?.email"
                                class="text-xs text-slate-500 dark:text-slate-200"
                            >
                                    {{ attempt.user.email }}
                                </span>
                        </div>
                    </td>

                    <td class="px-2 py-3">
                        <div class="flex flex-col" :title="attempt.quiz?.slug || ''">
                                <span class="text-xs text-blue-700 dark:text-blue-300">
                                    {{ getQuizTitle(attempt) }}
                                </span>

                            <div class="mt-1 text-[10px] text-teal-700 dark:text-teal-300">
                                    <span v-if="getLessonTitle(attempt)">
                                        {{ getLessonTitle(attempt) }}
                                    </span>

                                <span v-if="getModuleTitle(attempt)">
                                        · {{ getModuleTitle(attempt) }}
                                    </span>

                                <span v-if="getCourseTitle(attempt)">
                                        · {{ getCourseTitle(attempt) }}
                                    </span>
                            </div>
                        </div>
                    </td>

                    <td class="px-2 py-3 whitespace-nowrap">
                        <div class="flex flex-col text-[11px] gap-2">
                                <span class="text-sky-700 dark:text-sky-200">
                                    <span class="text-slate-500 dark:text-slate-300">
                                        {{ t('shortStarted') }}:
                                    </span>
                                    <br>
                                    {{ formatDateTime(attempt.started_at) }}
                                </span>

                            <span class="text-sky-700 dark:text-sky-200">
                                    <span class="text-slate-500 dark:text-slate-300">
                                        {{ t('shortExpires') }}:
                                    </span>
                                    <br>
                                    {{ formatDateTime(attempt.finished_at) }}
                                </span>
                        </div>
                    </td>

                    <td class="px-2 py-3 whitespace-nowrap text-center space-y-1">
                            <span
                                :class="[
                                    'inline-flex items-center px-2 py-0.5 rounded-sm ' +
                                     'border text-[11px] font-semibold',
                                    statusClass(attempt.status),
                                ]"
                            >
                                {{ statusLabel(attempt.status) }}
                            </span>

                        <div class="text-xs text-slate-700 dark:text-slate-200">
                            {{ formatPercent(attempt.percent) }}
                        </div>
                    </td>

                    <td class="px-2 py-3 text-center">
                        <div class="text-xs text-rose-700 dark:text-rose-300">
                            {{ attempt.attempt_number ?? '—' }}
                        </div>

                        <div class="text-xs">
                                <span class="text-slate-500 dark:text-slate-300">
                                    {{ t('duration') }}:
                                </span>
                            <span class="text-slate-700 dark:text-slate-200">
                                    {{ formatDuration(attempt.duration_seconds) }}
                                </span>
                        </div>
                    </td>

                    <td class="px-2 py-3 whitespace-nowrap text-center">
                        <div class="text-xs text-amber-800 dark:text-amber-200">
                            {{ attempt.score ?? '—' }} / {{ attempt.max_score ?? '—' }}
                        </div>
                    </td>

                    <td class="px-2 py-3 whitespace-nowrap">
                        <div class="flex justify-end space-x-2">
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
                    </td>

                    <td class="px-2 py-3 whitespace-nowrap text-center">
                        <input
                            type="checkbox"
                            :checked="selectedAttempts.includes(attempt.id)"
                            @change="emit('toggle-select', attempt.id)"
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
