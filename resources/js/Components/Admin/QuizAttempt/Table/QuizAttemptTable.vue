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

/** Локальная копия (паттерн как Lessons) */
const localAttempts = ref([])

watch(
    () => props.attempts,
    (newVal) => {
        localAttempts.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/** Массовые чекбоксы (если используешь чекбокс в шапке таблицы) */
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
               border border-slate-200 dark:border-slate-600 relative"
    >
        <div class="overflow-x-auto">
            <table
                v-if="attempts.length > 0"
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

                    <th class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="font-medium text-left">{{ t('users') }}</div>
                    </th>

                    <th class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="font-medium text-left">{{ t('quiz') }}</div>
                    </th>

                    <th class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="font-medium text-center">{{ t('status') }}</div>
                    </th>

                    <th class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('attemptNumber')">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-4 h-4"
                                height="24"
                                width="24"
                                viewBox="0 0 24 24">
                                <path class="fill-current text-teal-600 dark:text-teal-300"
                                      d="M12,24a1,1,0,0,1,0-2A10,10,0,0,0,12,2a1,1,0,0,1,0-2,12,12,0,0,1,0,24Z"></path>
                                <path
                                    class="fill-current text-teal-600 dark:text-teal-300"
                                    d="M1.045,13.913a1,1,0,0,1-1-.919C.022,12.665,0,12.336,0,12s.022-.665.049-.994a1,1,0,1,1,1.993.162C2.021,11.442,2,11.719,2,12s.021.558.042.832a1,1,0,0,1-.916,1.078Q1.086,13.913,1.045,13.913Z"></path>
                                <path
                                    class="fill-current text-teal-600 dark:text-teal-300"
                                    d="M6.243,3.641a1,1,0,0,1-.526-1.852,12.022,12.022,0,0,1,1.774-.9,1,1,0,1,1,.754,1.851,10.133,10.133,0,0,0-1.478.757A.993.993,0,0,1,6.243,3.641Z"></path>
                                <path
                                    class="fill-current text-teal-600 dark:text-teal-300"
                                    d="M2.188,8.044a.988.988,0,0,1-.451-.108A1,1,0,0,1,1.3,6.592,12.131,12.131,0,0,1,2.342,4.9,1,1,0,0,1,3.953,6.083,10.1,10.1,0,0,0,3.081,7.5,1,1,0,0,1,2.188,8.044Z"></path>
                                <path
                                    class="fill-current text-teal-600 dark:text-teal-300"
                                    d="M3.128,19.482a1,1,0,0,1-.808-.409,12.049,12.049,0,0,1-1.041-1.7,1,1,0,1,1,1.787-.9,10.047,10.047,0,0,0,.868,1.418,1,1,0,0,1-.217,1.4A.986.986,0,0,1,3.128,19.482Z"></path>
                                <path
                                    d="M7.853,23.185a.983.983,0,0,1-.377-.075A11.879,11.879,0,0,1,5.7,22.2,1,1,0,0,1,6.75,20.5a10.041,10.041,0,0,0,1.48.761,1,1,0,0,1-.377,1.926Z"></path>
                                <path
                                    d="M10,17a1,1,0,0,1-.707-.293l-4-4a1,1,0,0,1,1.414-1.414L10,14.586l7.293-7.293a1,1,0,1,1,1.414,1.414l-8,8A1,1,0,0,1,10,17Z"></path>
                            </svg>
                        </div>
                    </th>

                    <th class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="font-medium text-center">{{ t('points') }}</div>
                    </th>

                    <th class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="font-medium text-center">{{ t('percent') }}</div>
                    </th>

                    <th class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('date')">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-4 h-4 fill-current"
                                height="24"
                                width="24"
                                viewBox="0 0 24 24">
                                <path class="fill-current text-sky-700 dark:text-sky-200"
                                      d="M23,3H18V1a1,1,0,0,0-2,0V3H8V1A1,1,0,0,0,6,1V3H1A1,1,0,0,0,0,4V22a1,1,0,0,0,1,1H23a1,1,0,0,0,1-1V4A1,1,0,0,0,23,3ZM22,21H2V7H22Z"></path>
                            </svg>
                        </div>
                    </th>

                    <th class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="font-medium text-end">{{ t('actions') }}</div>
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
                    v-for="attempt in localAttempts"
                    :key="attempt.id"
                    class="text-sm font-semibold border-b-2
                           hover:bg-slate-100 dark:hover:bg-cyan-800"
                >
                    <td class="px-2 py-3 w-px">
                        <div class="text-left text-xs text-slate-800 dark:text-blue-200">
                            {{ attempt.id }}
                        </div>
                    </td>

                    <td class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="flex flex-col">
                            <span class="text-sm text-orange-600 dark:text-orange-200">
                                {{ attempt.user?.name || `#${attempt.user_id}` }}
                            </span>
                            <span
                                v-if="attempt.user?.email"
                                class="text-xs text-slate-500 dark:text-slate-200"
                            >
                                {{ attempt.user.email }}
                            </span>
                        </div>
                    </td>

                    <td class="px-2 py-3 first:pl-6 last:pr-6">
                        <div class="flex flex-col"
                             :title="attempt.quiz.slug">
                            <span class="text-xs text-teal-700 dark:text-teal-300">
                                {{ attempt.quiz?.title || `#${attempt.quiz_id}` }}
                            </span>
                            <div class="mt-1 text-[11px] text-slate-500 dark:text-slate-300">
                                <span v-if="attempt.course?.title">
                                    {{ attempt.course.title }}
                                </span>
                                <span v-if="attempt.module?.title">
                                    · {{ attempt.module.title }}
                                </span>
                                <span v-if="attempt.lesson?.title">
                                    · {{ attempt.lesson.title }}
                                </span>
                            </div>
                        </div>
                    </td>

                    <td class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="items-center px-2 py-0.5
                                   rounded-sm border border-blue-400
                                   text-[11px] font-semibold
                                   bg-blue-50 dark:bg-blue-900/40
                                   text-blue-800 dark:text-blue-100">
                            {{ statusLabel(attempt.status) }}
                        </div>
                    </td>

                    <td class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="text-center text-xs text-rose-700 dark:text-rose-300">
                            {{ attempt.attempt_number ?? '—' }}
                        </div>
                    </td>

                    <td class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="text-center text-xs text-amber-800 dark:text-amber-200">
                            {{ (attempt.score ?? '—') }} / {{ (attempt.max_score ?? '—') }}
                        </div>
                    </td>

                    <td class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="text-center text-xs text-green-700 dark:text-green-300">
                            {{ formatPercent(attempt.percent) }}
                        </div>
                    </td>

                    <td class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
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

                    <!-- ✅ Actions: edit + delete -->
                    <td class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="flex justify-end space-x-2">
                            <IconEdit :href="route('admin.quizAttempts.edit', attempt.id)" />
                            <DeleteIconButton
                                @delete="$emit('delete', attempt.id, attempt.quiz?.title || '')"
                            />
                        </div>
                    </td>

                    <td class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="text-center">
                            <input
                                type="checkbox"
                                :checked="selectedAttempts.includes(attempt.id)"
                                @change="$emit('toggle-select', attempt.id)"
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
