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
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-400 dark:border-slate-500 relative"
    >
        <div v-if="enrollments.length" class="p-3">
            <div class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <article
                    v-for="enrollment in enrollments"
                    :key="enrollment.id"
                    class="relative flex flex-col h-full rounded-md
                           border border-slate-400 dark:border-slate-500
                           bg-slate-50/70 dark:bg-slate-800/80 shadow-sm
                           hover:shadow-md transition-shadow duration-150"
                >
                    <header
                        class="flex items-center justify-between px-2 py-1
                               border-b border-dashed border-slate-400 dark:border-slate-500"
                    >
                        <div
                            class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm
                                   border border-gray-400
                                   bg-slate-200 dark:bg-slate-700
                                   text-slate-800 dark:text-blue-100"
                        >
                            ID: {{ enrollment.id }}
                        </div>
                        <div>
                            <span
                                :class="[
                                    'inline-flex items-center px-2 py-0.5 rounded-sm ' +
                                     'border text-[11px] font-semibold',
                                    getStatusClasses(enrollment.status)
                                ]"
                                :title="t('status')"
                            >
                                {{ getStatusLabel(enrollment.status) }}
                            </span>
                        </div>
                    </header>
                    <div class="flex flex-col flex-1 px-3 py-2 space-y-2">
                        <div class="text-center">
                            <div class="text-[12px] font-medium text-slate-900 dark:text-slate-50">
                                {{ enrollment.user?.name || `#${enrollment.user_id}` }}
                            </div>
                            <div
                                v-if="enrollment.user?.email"
                                class="text-[11px] text-slate-500 dark:text-slate-300"
                            >
                                {{ enrollment.user.email }}
                            </div>
                        </div>
                        <div
                            class="text-center space-y-0.5
                                   border border-dotted border-slate-400 dark:border-slate-500"
                        >
                            <div
                                class="text-[12px] font-semibold
                                       text-sky-700 dark:text-sky-200 line-clamp-2"
                                :title="enrollment.course?.slug"
                            >
                                {{ enrollment.course?.title || `#${enrollment.school_course_id}` }}
                            </div>
                        </div>
                        <div class="h-12 flex flex-col justify-center text-center space-y-0.5">
                            <div
                                class="text-[11px] font-semibold
                                       text-amber-700 dark:text-amber-200 line-clamp-2"
                            >
                                {{ enrollment.schedule?.title || t('scheduleNotSet') }}
                            </div>
                            <div
                                v-if="enrollment.schedule?.starts_at"
                                class="text-[10px] text-slate-500 dark:text-slate-300"
                            >
                                {{ formatDateTime(enrollment.schedule.starts_at) }}

                                <span v-if="enrollment.schedule?.ends_at">
                                    —
                                    <br>
                                    {{ formatDateTime(enrollment.schedule.ends_at) }}
                                </span>
                            </div>
                        </div>
                        <div class="mt-1 space-y-1">
                            <div class="flex items-center justify-center space-x-2">
                                <span
                                    class="text-[11px] font-semibold"
                                    :class="enrollment.is_accessible
                                        ? 'text-emerald-700 dark:text-emerald-300'
                                        : 'text-rose-700 dark:text-rose-300'"
                                >
                                    {{ enrollment.is_accessible ? t('accessGranted') : t('accessDenied') }}
                                </span>
                                <span class="text-[10px] text-slate-500 dark:text-slate-300">
                                    {{ formatDaysLeft(enrollment.days_left) }}
                                </span>
                            </div>
                            <div class="flex flex-col items-center">
                                <span class="text-[11px] text-slate-700 dark:text-slate-200 mb-0.5">
                                    {{ t('progress') }}: {{ enrollment.progress_percent ?? 0 }}%
                                </span>
                                <div
                                    class="w-24 h-1.5 bg-slate-300 dark:bg-slate-600
                                           rounded-full overflow-hidden"
                                >
                                    <div
                                        class="h-1.5 bg-emerald-500 dark:bg-emerald-400"
                                        :style="{ width: progressWidth(enrollment.progress_percent) }"
                                    ></div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-1 flex flex-col justify-center text-center space-y-0.5">
                            <div
                                class="text-[11px] text-gray-800 dark:text-gray-200
                                       text-center font-semibold line-clamp-3"
                                :title="enrollment.notes || '—'"
                            >
                                {{ enrollment.notes || '—' }}
                            </div>
                            <span
                                class="mt-0.5 text-[10px] text-slate-500 dark:text-slate-300"
                                :title="t('shortStarted')"
                            >
                                {{ formatDateTime(enrollment.started_at) }}
                            </span>
                        </div>
                    </div>
                    <footer
                        class="px-3 py-2 border-t border-dashed
                               border-slate-400 dark:border-slate-500"
                    >
                        <div class="flex items-center justify-center space-x-2">
                            <div class="flex items-center space-x-1">
                                <span
                                    v-if="enrollment.certificate"
                                    class="inline-flex items-center text-[12px]
                                           text-yellow-700 dark:text-yellow-300"
                                >
                                    <svg
                                        class="w-4 h-4 mr-1"
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
                            <div class="flex items-center space-x-1">
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
