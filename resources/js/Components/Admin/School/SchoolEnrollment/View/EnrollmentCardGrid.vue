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

const emit = defineEmits([
    'delete',
])

/* ==========================================================
 * STATUS
 * ========================================================== */

const statusLabelKeyMap = {
    active: 'statusEnrollmentActive',
    completed: 'statusEnrollmentCompleted',
    cancelled: 'statusEnrollmentCancelled',
    expired: 'statusEnrollmentExpired',
    paused: 'statusEnrollmentPaused',
}

const getStatusLabel = (status) => {
    if (!status) {
        return '—'
    }

    const key =
        statusLabelKeyMap[status]

    return key
        ? t(key)
        : status
}

const getStatusClasses = (status) => {
    switch (status) {
        case 'active':
            return (
                'border-emerald-400 bg-emerald-100 ' +
                'dark:bg-emerald-900/40 ' +
                'text-emerald-800 dark:text-emerald-100'
            )

        case 'completed':
            return (
                'border-blue-400 bg-blue-100 ' +
                'dark:bg-blue-900/40 ' +
                'text-blue-800 dark:text-blue-100'
            )

        case 'paused':
            return (
                'border-amber-400 bg-amber-100 ' +
                'dark:bg-amber-900/40 ' +
                'text-amber-800 dark:text-amber-100'
            )

        case 'expired':
        case 'cancelled':
            return (
                'border-rose-400 bg-rose-100 ' +
                'dark:bg-rose-900/40 ' +
                'text-rose-800 dark:text-rose-100'
            )

        default:
            return (
                'border-slate-400 bg-slate-200 ' +
                'dark:bg-slate-700 ' +
                'text-slate-800 dark:text-slate-100'
            )
    }
}

/* ==========================================================
 * NEW SHARED RESOURCE CONTRACT
 * ========================================================== */

const getUserName = (enrollment) => {
    return enrollment?.user?.name
        || (
            enrollment?.user_id
                ? `#${enrollment.user_id}`
                : '—'
        )
}

const getUserEmail = (enrollment) => {
    return enrollment?.user?.email || ''
}

const getCourseTitle = (enrollment) => {
    return enrollment
            ?.course
            ?.translation
            ?.title
        || (
            enrollment?.school_course_id
                ? `#${enrollment.school_course_id}`
                : '—'
        )
}

const getCourseSlug = (enrollment) => {
    return enrollment
            ?.course
            ?.slug
        || ''
}

const getScheduleTitle = (enrollment) => {
    return enrollment
            ?.schedule
            ?.translation
            ?.title
        || ''
}

/* ==========================================================
 * DATE / ACCESS / PROGRESS
 * ========================================================== */

const formatDateTime = (value) => {
    if (!value) {
        return '—'
    }

    const date =
        new Date(value)

    if (
        Number.isNaN(
            date.getTime()
        )
    ) {
        return value
    }

    return date.toLocaleString(
        'ru-RU'
    )
}

const formatDaysLeft = (daysLeft) => {
    if (
        daysLeft === null
        || daysLeft === undefined
    ) {
        return '∞'
    }

    if (
        typeof daysLeft !== 'number'
    ) {
        return daysLeft
    }

    return daysLeft < 0
        ? t(
            'daysLeftPast',
            {
                days:
                    Math.abs(daysLeft),
            }
        )
        : t(
            'daysLeft',
            {
                days:
                daysLeft,
            }
        )
}

const progressWidth = (value) => {
    return `${
        Math.min(
            Math.max(
                value ?? 0,
                0
            ),
            100
        )
    }%`
}

/* ==========================================================
 * ACTIONS
 * ========================================================== */

const handleDelete = (enrollment) => {
    emit(
        'delete',
        enrollment
    )
}
</script>

<template>
    <div
        class="relative rounded-sm border
               border-slate-400 bg-white shadow-lg
               dark:border-slate-500 dark:bg-slate-700"
    >
        <div
            v-if="enrollments.length"
            class="p-3"
        >
            <div
                class="grid grid-cols-1 gap-3
                       sm:grid-cols-2
                       lg:grid-cols-3
                       xl:grid-cols-4"
            >
                <article
                    v-for="enrollment in enrollments"
                    :key="enrollment.id"
                    class="relative flex h-full flex-col rounded-md
                           border border-slate-400
                           bg-slate-50/70 shadow-sm
                           transition-shadow duration-150
                           hover:shadow-md
                           dark:border-slate-500
                           dark:bg-slate-800/80"
                >
                    <!-- HEADER -->
                    <header
                        class="flex items-center justify-between
                               border-b border-dashed
                               border-slate-400
                               px-2 py-1
                               dark:border-slate-500"
                    >
                        <div
                            class="rounded-sm border
                                   border-gray-400
                                   bg-slate-200
                                   px-1.5 py-0.5
                                   text-[10px] font-semibold
                                   text-slate-800
                                   dark:bg-slate-700
                                   dark:text-blue-100"
                        >
                            ID: {{ enrollment.id }}
                        </div>

                        <div>
                            <span
                                :class="[
                                    'inline-flex items-center rounded-sm border px-2 py-0.5 text-[11px] font-semibold',
                                    getStatusClasses(enrollment.status),
                                ]"
                                :title="t('status')"
                            >
                                {{ getStatusLabel(enrollment.status) }}
                            </span>
                        </div>
                    </header>

                    <!-- CONTENT -->
                    <div
                        class="flex flex-1 flex-col
                               space-y-2 px-3 py-2"
                    >
                        <!-- USER -->
                        <div class="text-center">
                            <div
                                class="text-[12px] font-medium
                                       text-slate-900
                                       dark:text-slate-50"
                            >
                                {{ getUserName(enrollment) }}
                            </div>

                            <div
                                v-if="getUserEmail(enrollment)"
                                class="text-[11px]
                                       text-slate-500
                                       dark:text-slate-300"
                            >
                                {{ getUserEmail(enrollment) }}
                            </div>
                        </div>

                        <!-- COURSE -->
                        <div
                            class="space-y-0.5 text-center
                                   border border-dotted
                                   border-slate-400
                                   dark:border-slate-500"
                        >
                            <div
                                class="line-clamp-2
                                       text-[12px] font-semibold
                                       text-sky-700
                                       dark:text-sky-200"
                                :title="getCourseSlug(enrollment)"
                            >
                                {{ getCourseTitle(enrollment) }}
                            </div>

                            <div
                                v-if="getCourseSlug(enrollment)"
                                class="text-[9px]
                                       text-slate-500
                                       dark:text-slate-400"
                            >
                                {{ getCourseSlug(enrollment) }}
                            </div>
                        </div>

                        <!-- SCHEDULE -->
                        <div
                            class="flex h-12 flex-col
                                   justify-center space-y-0.5
                                   text-center"
                        >
                            <div
                                class="line-clamp-2
                                       text-[11px] font-semibold
                                       text-amber-700
                                       dark:text-amber-200"
                            >
                                {{
                                    getScheduleTitle(enrollment)
                                    || t('scheduleNotSet')
                                }}
                            </div>

                            <div
                                v-if="enrollment.schedule?.starts_at"
                                class="text-[10px]
                                       text-slate-500
                                       dark:text-slate-300"
                            >
                                {{
                                    formatDateTime(
                                        enrollment.schedule.starts_at
                                    )
                                }}

                                <span
                                    v-if="enrollment.schedule?.ends_at"
                                >
                                    —
                                    <br>

                                    {{
                                        formatDateTime(
                                            enrollment.schedule.ends_at
                                        )
                                    }}
                                </span>
                            </div>
                        </div>

                        <!-- ACCESS / PROGRESS -->
                        <div class="mt-1 space-y-1">
                            <div
                                class="flex items-center
                                       justify-center space-x-2"
                            >
                                <span
                                    class="text-[11px] font-semibold"
                                    :class="
                                        enrollment.is_accessible
                                            ? 'text-emerald-700 dark:text-emerald-300'
                                            : 'text-rose-700 dark:text-rose-300'
                                    "
                                >
                                    {{
                                        enrollment.is_accessible
                                            ? t('accessGranted')
                                            : t('accessDenied')
                                    }}
                                </span>

                                <span
                                    class="text-[10px]
                                           text-slate-500
                                           dark:text-slate-300"
                                >
                                    {{ formatDaysLeft(enrollment.days_left) }}
                                </span>
                            </div>

                            <div
                                class="flex flex-col items-center"
                            >
                                <span
                                    class="mb-0.5 text-[11px]
                                           text-slate-700
                                           dark:text-slate-200"
                                >
                                    {{ t('progress') }}:
                                    {{ enrollment.progress_percent ?? 0 }}%
                                </span>

                                <div
                                    class="h-1.5 w-24 overflow-hidden
                                           rounded-full bg-slate-300
                                           dark:bg-slate-600"
                                >
                                    <div
                                        class="h-1.5
                                               bg-emerald-500
                                               dark:bg-emerald-400"
                                        :style="{
                                            width: progressWidth(
                                                enrollment.progress_percent
                                            ),
                                        }"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- NOTES / STARTED -->
                        <div
                            class="mt-1 flex flex-col
                                   justify-center space-y-0.5
                                   text-center"
                        >
                            <div
                                class="line-clamp-3 text-center
                                       text-[11px] font-semibold
                                       text-gray-800
                                       dark:text-gray-200"
                                :title="enrollment.notes || '—'"
                            >
                                {{ enrollment.notes || '—' }}
                            </div>

                            <span
                                class="mt-0.5 text-[10px]
                                       text-slate-500
                                       dark:text-slate-300"
                                :title="t('shortStarted')"
                            >
                                {{ formatDateTime(enrollment.started_at) }}
                            </span>
                        </div>
                    </div>

                    <!-- FOOTER -->
                    <footer
                        class="border-t border-dashed
                               border-slate-400
                               px-3 py-2
                               dark:border-slate-500"
                    >
                        <div
                            class="flex items-center
                                   justify-center space-x-2"
                        >
                            <div class="flex items-center space-x-1">
                                <span
                                    v-if="enrollment.certificate"
                                    class="inline-flex items-center
                                           text-[12px]
                                           text-yellow-700
                                           dark:text-yellow-300"
                                >
                                    <svg
                                        class="mr-1 h-4 w-4"
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
                                    :href="
                                        route(
                                            'admin.schoolEnrollments.edit',
                                            {
                                                schoolEnrollment:
                                                    enrollment.id,
                                            }
                                        )
                                    "
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
            class="p-5 text-center
                   text-slate-700
                   dark:text-slate-100"
        >
            {{ t('noData') }}
        </div>
    </div>
</template>
