<script setup>
import { defineEmits, defineProps } from 'vue'
import { useI18n } from 'vue-i18n'

import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'

const { t } = useI18n()

defineProps({
    enrollments: {
        type: Array,
        default: () => [],
    },
})

const emit = defineEmits(['delete'])

const statusLabelKeyMap = {
    active: 'statusEnrollmentActive',
    completed: 'statusEnrollmentCompleted',
    cancelled: 'statusEnrollmentCancelled',
    expired: 'statusEnrollmentExpired',
    paused: 'statusEnrollmentPaused',
}

const getStatusLabel = (status) => {
    if (!status) return '—'

    const key = statusLabelKeyMap[status]

    return key ? t(key) : status
}

const getStatusClasses = (status) => {
    switch (status) {
        case 'active':
            return 'border-emerald-400 bg-emerald-100 dark:bg-emerald-900/40 ' +
                'text-emerald-800 dark:text-emerald-100'

        case 'completed':
            return 'border-blue-400 bg-blue-100 dark:bg-blue-900/40 ' +
                'text-blue-800 dark:text-blue-100'

        case 'paused':
            return 'border-amber-400 bg-amber-100 dark:bg-amber-900/40 ' +
                'text-amber-800 dark:text-amber-100'

        case 'expired':
        case 'cancelled':
            return 'border-rose-400 bg-rose-100 dark:bg-rose-900/40 ' +
                'text-rose-800 dark:text-rose-100'

        default:
            return 'border-slate-400 bg-slate-200 dark:bg-slate-700 ' +
                'text-slate-800 dark:text-slate-100'
    }
}

const formatDateTime = (value) => {
    if (!value) return '—'

    try {
        return new Date(value).toLocaleString('ru-RU')
    } catch {
        return value
    }
}

const formatDaysLeft = (daysLeft) => {
    if (daysLeft === null || daysLeft === undefined) return '∞'
    if (typeof daysLeft !== 'number') return daysLeft

    return daysLeft < 0
        ? t('daysLeftPast', { days: Math.abs(daysLeft) })
        : t('daysLeft', { days: daysLeft })
}

const progressWidth = (value) => {
    return `${Math.min(Math.max(value ?? 0, 0), 100)}%`
}

const handleDelete = (enrollment) => {
    emit('delete', enrollment)
}

const formatDatesTitle = (enrollment) => {
    return [
        `${t('shortStarted')}: ${formatDateTime(enrollment.started_at)}`,
        `${t('shortExpires')}: ${formatDateTime(enrollment.expires_at)}`,
        `${t('shortCompleted')}: ${formatDateTime(enrollment.completed_at)}`,
    ].join('\n')
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
                            {{ t('course') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-medium text-sm text-left">
                            {{ t('schedule') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-medium text-sm text-center">
                            {{ t('status') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-medium text-sm text-left">
                            {{ t('access') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-medium text-sm text-left">
                            {{ t('progress') }}
                        </div>
                    </th>
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
                    <td class="px-2 py-3 whitespace-nowrap">
                        <div class="text-left text-slate-800 dark:text-slate-200 font-semibold">
                            {{ enrollment.id }}
                        </div>
                    </td>
                    <td class="px-2 py-3 whitespace-nowrap">
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
                    <td class="px-2 py-3">
                        <div class="flex flex-col">
                            <span class="text-sm text-sky-700 dark:text-sky-200">
                                {{ enrollment.course?.title || `#${enrollment.school_course_id}` }}
                            </span>
                            <span
                                v-if="enrollment.course?.slug"
                                class="text-xs text-slate-500 dark:text-slate-300"
                            >
                                {{ enrollment.course.slug }}
                            </span>
                        </div>
                    </td>
                    <td class="px-2 py-3">
                        <div class="flex flex-col">
                            <span class="text-xs text-amber-700 dark:text-amber-200">
                                {{ enrollment.schedule?.title || '—' }}
                            </span>
                            <span
                                v-if="enrollment.schedule?.starts_at"
                                class="text-[11px] text-slate-500 dark:text-slate-300"
                            >
                                {{ formatDateTime(enrollment.schedule.starts_at) }}

                                <span v-if="enrollment.schedule?.ends_at">
                                    —
                                    <br>
                                    {{ formatDateTime(enrollment.schedule.ends_at) }}
                                </span>
                            </span>
                        </div>
                    </td>
                    <td class="px-2 py-3 whitespace-nowrap">
                        <div class="flex justify-center">
                            <span
                                :class="[
                                    'inline-flex items-center px-2 py-0.5 rounded-sm ' +
                                     'border text-[11px] font-semibold',
                                    getStatusClasses(enrollment.status)
                                ]"
                            >
                                {{ getStatusLabel(enrollment.status) }}
                            </span>
                        </div>
                    </td>
                    <td
                        class="px-2 py-3"
                        :title="formatDatesTitle(enrollment)"
                    >
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
                                       text-yellow-600 dark:text-yellow-300"
                            >
                                ★ {{ t('certificateIssued') }}
                            </span>
                        </div>
                    </td>
                    <td class="px-2 py-3 whitespace-nowrap">
                        <div class="flex flex-col">
                            <span class="text-xs text-slate-800 dark:text-slate-100">
                                {{ enrollment.progress_percent ?? 0 }}%
                            </span>
                            <div class="mt-1 w-24 h-1.5 bg-slate-300 dark:bg-slate-600">
                                <div
                                    class="h-1.5 bg-emerald-500 dark:bg-emerald-400"
                                    :style="{ width: progressWidth(enrollment.progress_percent) }"
                                ></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-2 py-3 whitespace-nowrap">
                        <div class="flex items-center justify-end space-x-1">
                            <IconEdit
                                :href="route('admin.schoolEnrollments.edit', {
                                    schoolEnrollment: enrollment.id,
                                })"
                            />
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
