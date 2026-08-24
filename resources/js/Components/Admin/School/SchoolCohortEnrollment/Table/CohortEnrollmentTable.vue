<script setup>
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router } from '@inertiajs/vue3'

import EditIconButton from '@/Components/Admin/UI/Buttons/EditIconButton.vue'

const { t } = useI18n()
const toast = useToast()

defineProps({
    enrollments: {
        type: Array,
        default: () => [],
    },
})

/* ==========================================================
 * STATUS
 * ========================================================== */

const statusOptions = [
    'pending',
    'approved',
    'rejected',
    'cancelled',
]

const statusLabelKeyMap = {
    pending: 'statusSelectPending',
    approved: 'statusSelectApproved',
    rejected: 'statusSelectRejected',
    cancelled: 'statusSelectCancelled',
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
        case 'approved':
            return {
                badge:
                    'border-emerald-400 bg-emerald-100 ' +
                    'dark:bg-emerald-900/40 ' +
                    'text-emerald-800 dark:text-emerald-100',
            }

        case 'pending':
            return {
                badge:
                    'border-amber-400 bg-amber-100 ' +
                    'dark:bg-amber-900/40 ' +
                    'text-amber-800 dark:text-amber-100',
            }

        case 'rejected':
            return {
                badge:
                    'border-rose-400 bg-rose-100 ' +
                    'dark:bg-rose-900/40 ' +
                    'text-rose-800 dark:text-rose-100',
            }

        case 'cancelled':
            return {
                badge:
                    'border-slate-400 bg-slate-200 ' +
                    'dark:bg-slate-700 ' +
                    'text-slate-800 dark:text-slate-100',
            }

        default:
            return {
                badge:
                    'border-blue-400 bg-blue-100 ' +
                    'dark:bg-blue-900/40 ' +
                    'text-blue-800 dark:text-blue-100',
            }
    }
}

/* ==========================================================
 * NEW SHARED RESOURCE CONTRACT
 * ========================================================== */

const getScheduleTitle = (enrollment) => {
    return enrollment
            ?.schedule
            ?.translation
            ?.title
        || (
            enrollment?.school_course_schedule_id
                ? `ID: ${enrollment.school_course_schedule_id}`
                : '—'
        )
}

const getScheduleSubtitle = (enrollment) => {
    return enrollment
            ?.schedule
            ?.translation
            ?.subtitle
        || ''
}

const getScheduleShort = (enrollment) => {
    return enrollment
            ?.schedule
            ?.translation
            ?.short
        || ''
}

const getCourseTitle = (enrollment) => {
    return enrollment
            ?.schedule
            ?.course
            ?.translation
            ?.title
        || (
            enrollment?.schedule?.course?.id
                ? `ID: ${enrollment.schedule.course.id}`
                : ''
        )
}

const getUserName = (enrollment) => {
    return enrollment?.user?.name
        || (
            enrollment?.user_id
                ? `ID: ${enrollment.user_id}`
                : '—'
        )
}

const getUserEmail = (enrollment) => {
    return enrollment?.user?.email || ''
}

/* ==========================================================
 * DATE
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

/* ==========================================================
 * ACTIONS
 * ========================================================== */

const updateStatus = (
    enrollment,
    status
) => {
    if (!status) {
        return
    }

    router.put(
        route(
            'admin.actions.cohortEnrollments.updateStatus',
            enrollment.id
        ),
        {
            status,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                toast.success(
                    'Статус обновлён'
                )
            },

            onError: (errors) => {
                toast.error(
                    errors?.status
                    || errors?.general
                    || 'Ошибка при обновлении статуса'
                )
            },
        }
    )
}

const editNotes = (enrollment) => {
    const current =
        enrollment?.notes || ''

    const updated =
        window.prompt(
            t('notes'),
            current
        )

    if (updated === null) {
        return
    }

    router.put(
        route(
            'admin.actions.cohortEnrollments.updateNotes',
            enrollment.id
        ),
        {
            notes: updated,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                toast.success(
                    'Заметки обновлены'
                )
            },

            onError: (errors) => {
                toast.error(
                    errors?.notes
                    || errors?.general
                    || 'Ошибка при обновлении заметок'
                )
            },
        }
    )
}
</script>

<template>
    <div
        class="relative rounded-sm border border-slate-200
               bg-white shadow-lg
               dark:border-slate-600 dark:bg-slate-700"
    >
        <div class="overflow-x-auto">
            <table
                v-if="enrollments.length"
                class="table-auto w-full
                       text-slate-700 dark:text-slate-100"
            >
                <thead
                    class="border border-solid border-gray-300
                           bg-slate-200 text-sm uppercase
                           dark:border-gray-700 dark:bg-cyan-900"
                >
                <tr>
                    <!-- ID -->
                    <th class="w-px px-2 py-3">
                        <div class="text-center font-medium">
                            {{ t('id') }}
                        </div>
                    </th>

                    <!-- Schedule -->
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div
                            class="flex items-center
                                       justify-center gap-2"
                            :title="t('learningFlow')"
                        >
                            <svg
                                class="h-4 w-4 fill-current
                                           text-cyan-600
                                           dark:text-cyan-400"
                                viewBox="0 0 512 512"
                            >
                                <path
                                    d="M352 96c0-53.02-42.98-96-96-96s-96 42.98-96 96 42.98 96 96 96 96-42.98 96-96zM233.59 241.1c-59.33-36.32-155.43-46.3-203.79-49.05C13.55 191.13 0 203.51 0 219.14v222.8c0 14.33 11.59 26.28 26.49 27.05 43.66 2.29 131.99 10.68 193.04 41.43 9.37 4.72 20.48-1.71 20.48-11.87V252.56c-.01-4.67-2.32-8.95-6.42-11.46zm248.61-49.05c-48.35 2.74-144.46 12.73-203.78 49.05-4.1 2.51-6.41 6.96-6.41 11.63v245.79c0 10.19 11.14 16.63 20.54 11.9 61.04-30.72 149.32-39.11 192.97-41.4 14.9-.78 26.49-12.73 26.49-27.06V219.14c-.01-15.63-13.56-28.01-29.81-27.09z"
                                />
                            </svg>

                            <span class="font-medium">
                                    {{ t('learningFlow') }}
                                </span>
                        </div>
                    </th>

                    <!-- Course -->
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="text-center font-medium">
                            {{ t('course') }}
                        </div>
                    </th>

                    <!-- User -->
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div
                            class="flex items-center
                                       justify-center gap-2"
                        >
                            <svg
                                class="h-4 w-4 shrink-0"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    class="fill-current text-cyan-400"
                                    d="M18.974 8H22a2 2 0 012 2v6h-2v5a1 1 0 01-1 1h-2a1 1 0 01-1-1v-5h-2v-6a2 2 0 012-2h.974zM20 7a2 2 0 11-.001-3.999A2 2 0 0120 7zM2.974 8H6a2 2 0 012 2v6H6v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5H0v-6a2 2 0 012-2h.974zM4 7a2 2 0 11-.001-3.999A2 2 0 014 7z"
                                />

                                <path
                                    class="fill-current text-cyan-600"
                                    d="M12 6a3 3 0 110-6 3 3 0 010 6zm2 18h-4a1 1 0 01-1-1v-6H6v-6a3 3 0 013-3h6a3 3 0 013 3v6h-3v6a1 1 0 01-1 1z"
                                />
                            </svg>

                            <span class="font-medium">
                                    {{ t('users') }}
                                </span>
                        </div>
                    </th>

                    <!-- Status -->
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="text-center font-medium">
                            {{ t('status') }}
                        </div>
                    </th>

                    <!-- Enrolled -->
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div
                            class="flex justify-center"
                            :title="t('enrolledAt')"
                        >
                            <svg
                                class="h-4 w-4 shrink-0 fill-current"
                                viewBox="0 0 448 512"
                            >
                                <path
                                    d="M400 64h-48V12c0-6.627-5.373-12-12-12h-40c-6.627 0-12 5.373-12 12v52H160V12c0-6.627-5.373-12-12-12h-40c-6.627 0-12 5.373-12 12v52H48C21.49 64 0 85.49 0 112v352c0 26.51 21.49 48 48 48h352c26.51 0 48-21.49 48-48V112c0-26.51-21.49-48-48-48zm-6 400H54a6 6 0 0 1-6-6V160h352v298a6 6 0 0 1-6 6z"
                                />
                            </svg>
                        </div>
                    </th>

                    <!-- Notes -->
                    <th class="px-2 py-3">
                        <div class="text-left font-medium">
                            {{ t('notes') }}
                        </div>
                    </th>
                </tr>
                </thead>

                <tbody>
                <tr
                    v-for="enrollment in enrollments"
                    :key="enrollment.id"
                    class="border-b-2 text-xs font-semibold
                               hover:bg-slate-100
                               dark:hover:bg-cyan-800"
                >
                    <!-- ID -->
                    <td class="w-px px-2 py-3">
                        <div
                            class="text-center
                                       text-slate-800
                                       dark:text-slate-200"
                        >
                            {{ enrollment.id }}
                        </div>
                    </td>

                    <!-- Schedule -->
                    <td class="px-2 py-3">
                        <div
                            class="text-left text-xs
                                       text-amber-600
                                       dark:text-amber-200"
                            :title="
                                    getScheduleSubtitle(enrollment)
                                    || getScheduleShort(enrollment)
                                "
                        >
                            {{ getScheduleTitle(enrollment) }}
                        </div>

                        <div
                            v-if="enrollment.schedule?.slug"
                            class="mt-0.5 text-[10px]
                                       text-slate-500
                                       dark:text-slate-400"
                        >
                            {{ enrollment.schedule.slug }}
                        </div>
                    </td>

                    <!-- Course -->
                    <td class="px-2 py-3">
                        <div
                            class="text-center text-xs
                                       text-blue-700
                                       dark:text-blue-300"
                        >
                            {{ getCourseTitle(enrollment) || '—' }}
                        </div>
                    </td>

                    <!-- User -->
                    <td class="px-2 py-3 whitespace-nowrap">
                        <div class="flex flex-col">
                                <span
                                    class="text-sm
                                           text-slate-700
                                           dark:text-slate-200"
                                >
                                    {{ getUserName(enrollment) }}
                                </span>

                            <span
                                v-if="getUserEmail(enrollment)"
                                class="text-xs
                                           text-slate-500
                                           dark:text-slate-400"
                            >
                                    {{ getUserEmail(enrollment) }}
                                </span>
                        </div>
                    </td>

                    <!-- Status -->
                    <td class="px-2 py-3 whitespace-nowrap">
                        <div
                            class="flex flex-col
                                       items-center justify-center"
                        >
                                <span
                                    :class="[
                                        'mb-1 inline-flex items-center rounded-sm border px-2 py-0.5 text-[11px] font-semibold',
                                        getStatusClasses(enrollment.status).badge,
                                    ]"
                                >
                                    {{ getStatusLabel(enrollment.status) }}
                                </span>

                            <select
                                class="block w-full
                                           rounded-sm border
                                           border-slate-500
                                           py-0.5 pl-3 pr-7
                                           text-xs
                                           focus:border-indigo-500
                                           focus:ring-indigo-500
                                           dark:border-slate-500
                                           dark:bg-slate-800
                                           dark:text-slate-100"
                                :value="enrollment.status"
                                @change="
                                        updateStatus(
                                            enrollment,
                                            $event.target.value
                                        )
                                    "
                            >
                                <option
                                    v-for="status in statusOptions"
                                    :key="status"
                                    :value="status"
                                >
                                    {{ getStatusLabel(status) }}
                                </option>
                            </select>
                        </div>
                    </td>

                    <!-- Enrolled -->
                    <td class="px-2 py-3 whitespace-nowrap">
                        <div
                            class="text-center text-[10px]
                                       text-teal-700
                                       dark:text-teal-300"
                        >
                            {{ formatDateTime(enrollment.enrolled_at) }}
                        </div>
                    </td>

                    <!-- Notes -->
                    <td class="px-2 py-3 max-w-xs">
                        <div
                            class="flex items-center
                                       justify-between gap-2"
                        >
                            <div
                                class="line-clamp-3 text-xs
                                           text-rose-800
                                           dark:text-rose-200"
                                :title="enrollment.notes || '—'"
                            >
                                {{ enrollment.notes || '—' }}
                            </div>

                            <EditIconButton
                                class="shrink-0"
                                :title="t('editNotes')"
                                @click="editNotes(enrollment)"
                            />
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>

            <div
                v-else
                class="p-5 text-center
                       text-slate-700
                       dark:text-slate-100"
            >
                {{ t('noData') }}
            </div>
        </div>
    </div>
</template>
