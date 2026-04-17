<script setup>
import { defineProps, defineEmits } from 'vue'
import { useI18n } from 'vue-i18n'
import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'

const { t } = useI18n()

const props = defineProps({
    enrollments: {
        type: Array,
        default: () => [],
    },
})

const emit = defineEmits(['delete'])

// Мапа статус → ключ перевода (flat i18n)
const statusLabelKeyMap = {
    active: 'statusEnrollmentActive',
    completed: 'statusEnrollmentCompleted',
    cancelled: 'statusEnrollmentCancelled',
    expired: 'statusEnrollmentExpired',
    paused: 'statusEnrollmentPaused',
}

const getStatusLabel = (status) => {
    if (!status) return '—'
    const key = statusLabelKeyMap[status] || status
    return t(key)
}

// Форматирование даты/времени
const formatDateTime = (value) => {
    if (!value) return '—'
    try {
        return new Date(value).toLocaleString()
    } catch {
        return value
    }
}

// Форматирование дней до истечения
const formatDaysLeft = (daysLeft) => {
    if (daysLeft === null || daysLeft === undefined) return '∞'
    if (typeof daysLeft !== 'number') return daysLeft
    if (daysLeft < 0) {
        return t('daysLeftPast', { days: Math.abs(daysLeft) })
    }
    return t('daysLeft', { days: daysLeft })
}

// Клик по "удалить" — просто эмит наверх, подтверждение и запрос делает Index.vue
const handleDelete = (enrollment) => {
    emit('delete', enrollment.id)
}

// Формируем строку для title по датам доступа
const formatDatesTitle = (enrollment) => {
    const parts = []

    parts.push(
        `${t('shortStarted')}: ${formatDateTime(enrollment.started_at)}`
    )
    parts.push(
        `${t('shortExpires')}: ${formatDateTime(enrollment.expires_at)}`
    )
    parts.push(
        `${t('shortCompleted')}: ${formatDateTime(enrollment.completed_at)}`
    )

    return parts.join('\n')
}

</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-200 dark:border-slate-600 relative"
    >
        <div class="overflow-x-auto">
            <table
                v-if="enrollments.length > 0"
                class="table-auto w-full text-slate-700 dark:text-slate-100"
            >
                <thead
                    class="text-sm uppercase bg-slate-200 dark:bg-cyan-900
                           border border-solid border-gray-300 dark:border-gray-700"
                >
                <tr>
                    <!-- ID -->
                    <th class="px-2 py-3 w-px">
                        <div class="font-medium text-sm text-left">
                            {{ t('id') }}
                        </div>
                    </th>

                    <!-- Пользователь -->
                    <th class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="font-medium text-sm text-left">
                            {{ t('users') }}
                        </div>
                    </th>

                    <!-- Курс -->
                    <th class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="font-medium text-sm text-left">
                            {{ t('course') }}
                        </div>
                    </th>

                    <!-- Поток / расписание -->
                    <th class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="font-medium text-sm text-left">
                            {{ t('schedule') }}
                        </div>
                    </th>

                    <!-- Доступ / дни до окончания -->
                    <th class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="font-medium text-sm text-left">
                            {{ t('access') }}
                        </div>
                    </th>

                    <!-- Прогресс -->
                    <th class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="font-medium text-sm text-left">
                            {{ t('progress') }}
                        </div>
                    </th>

                    <!-- Действия -->
                    <th class="px-2 py-3 w-px">
                        <div class="font-medium text-sm text-right">
                            {{ t('actions') }}
                        </div>
                    </th>
                </tr>
                </thead>

                <tbody>
                <tr
                    v-for="enrollment in enrollments"
                    :key="enrollment.id"
                    class="text-xs font-semibold border-b-2
                               hover:bg-slate-100 dark:hover:bg-cyan-800"
                >
                    <!-- ID -->
                    <td class="px-2 py-3 whitespace-nowrap">
                        <div
                            class="text-left text-slate-800 dark:text-slate-200 font-semibold"
                        >
                            {{ enrollment.id }}
                        </div>
                    </td>

                    <!-- Пользователь -->
                    <td class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="flex flex-col" :title="enrollment.notes || '—'">
                                <span class="text-sm text-slate-800 dark:text-slate-100">
                                    {{ enrollment.user?.name || `#${enrollment.user_id}` }}
                                </span>
                            <span
                                v-if="enrollment.user?.email"
                                class="text-xs text-slate-500 dark:text-slate-300"
                            >
                                    {{ enrollment.user.email }}
                            </span>
                        </div>
                    </td>

                    <!-- Курс -->
                    <td class="px-2 py-3 first:pl-6 last:pr-6">
                        <div class="flex flex-col">
                                <span class="text-sm text-sky-700 dark:text-sky-200">
                                    {{ enrollment.course?.title || `#${enrollment.course_id}` }}
                                </span>
                            <span
                                v-if="enrollment.course?.slug"
                                class="text-xs text-slate-500 dark:text-slate-300"
                            >
                                    {{ enrollment.course.slug }}
                                </span>
                        </div>
                    </td>

                    <!-- Поток / расписание -->
                    <td class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="flex flex-col" :title="getStatusLabel(enrollment.status)">
                                <span class="text-xs text-amber-700 dark:text-amber-200">
                                    {{ enrollment.schedule?.title || '—' }}
                                </span>
                            <span
                                v-if="enrollment.schedule?.starts_at"
                                class="text-[11px] text-slate-500 dark:text-slate-300"
                            >
                                    {{ formatDateTime(enrollment.schedule.starts_at) }}
                                    <span v-if="enrollment.schedule?.ends_at"> — <br></span>
                                    <span v-if="enrollment.schedule?.ends_at">
                                        {{ formatDateTime(enrollment.schedule.ends_at) }}
                                    </span>
                                </span>
                        </div>
                    </td>

                    <!-- Доступ: is_accessible + days_left -->
                    <td class="px-2 py-3 first:pl-6 last:pr-6"
                        :title="formatDatesTitle(enrollment)">
                        <div class="flex flex-col">
                        <span
                            class="text-xs font-semibold"
                            :class="enrollment.is_accessible
                                ? 'text-emerald-700 dark:text-emerald-300'
                                : 'text-rose-700 dark:text-rose-300'"
                        >
                            {{ enrollment.is_accessible ? t('accessGranted') : t('accessDenied') }}
                        </span>
                            <span class="text-[11px] text-slate-500 dark:text-slate-300">
                                {{ formatDaysLeft(enrollment.days_left) }}
                            </span>

                            <span
                                v-if="enrollment.certificate"
                                class="mt-1 inline-flex items-center text-[11px]
                                       text-yellow-600 dark:text-yellow-300">
                                <svg
                                    class="w-3 h-3 mr-1"
                                    viewBox="0 0 16 16"
                                    fill="currentColor"
                                >
                                    <path
                                        d="M8 0L6.59 4.26 2 4.62l3.5 2.88L4.33 12 8 9.8 11.67 12 10.5 7.5 14 4.62l-4.59-.36L8 0z"
                                    />
                                </svg>
                                {{ t('certificateIssued') }}
                            </span>
                        </div>
                    </td>

                    <!-- Прогресс -->
                    <td class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="flex flex-col">
                                <span class="text-xs text-slate-800 dark:text-slate-100">
                                    {{ enrollment.progress_percent ?? 0 }}%
                                </span>
                            <div class="mt-1 w-24 h-1.5 bg-slate-300 dark:bg-slate-600">
                                <div
                                    class="h-1.5 bg-emerald-500 dark:bg-emerald-400"
                                    :style="{
                                            width: `${Math.min(
                                                Math.max(enrollment.progress_percent ?? 0, 0),
                                                100,
                                            )}%`,
                                        }"
                                ></div>
                            </div>
                        </div>
                    </td>

                    <!-- Действия -->
                    <td class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div
                            class="flex items-center justify-end space-x-1"
                        >
                            <!-- Редактировать -->
                            <IconEdit
                                :href="route('admin.enrollments.edit', enrollment.id)"
                            />

                            <!-- Удалить -->
                            <DeleteIconButton
                                :title="t('delete')"
                                @delete="handleDelete(enrollment)"
                            />
                        </div>
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
