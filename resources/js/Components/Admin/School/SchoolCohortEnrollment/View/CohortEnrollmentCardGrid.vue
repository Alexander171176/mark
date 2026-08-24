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
                card:
                    'border-emerald-400 dark:border-emerald-500 ' +
                    'bg-emerald-50/70 dark:bg-emerald-900/20',

                badge:
                    'border-emerald-400 bg-emerald-100 ' +
                    'dark:bg-emerald-900/40 ' +
                    'text-emerald-800 dark:text-emerald-100',
            }

        case 'pending':
            return {
                card:
                    'border-amber-400 dark:border-amber-500 ' +
                    'bg-amber-50/70 dark:bg-amber-900/20',

                badge:
                    'border-amber-400 bg-amber-100 ' +
                    'dark:bg-amber-900/40 ' +
                    'text-amber-800 dark:text-amber-100',
            }

        case 'rejected':
            return {
                card:
                    'border-rose-400 dark:border-rose-500 ' +
                    'bg-rose-50/70 dark:bg-rose-900/20',

                badge:
                    'border-rose-400 bg-rose-100 ' +
                    'dark:bg-rose-900/40 ' +
                    'text-rose-800 dark:text-rose-100',
            }

        case 'cancelled':
            return {
                card:
                    'border-slate-400 dark:border-slate-500 ' +
                    'bg-slate-100/70 dark:bg-slate-800/80',

                badge:
                    'border-slate-400 bg-slate-200 ' +
                    'dark:bg-slate-700 ' +
                    'text-slate-800 dark:text-slate-100',
            }

        default:
            return {
                card:
                    'border-blue-400 dark:border-blue-500 ' +
                    'bg-blue-50/70 dark:bg-blue-900/20',

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
        class="relative rounded-sm border
               border-slate-400 bg-white
               shadow-lg
               dark:border-slate-500
               dark:bg-slate-700"
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
                    :class="[
                        'relative flex h-full flex-col rounded-md border shadow-sm transition-shadow duration-150 hover:shadow-md',
                        getStatusClasses(enrollment.status).card,
                    ]"
                >
                    <!-- Header -->
                    <header
                        class="flex items-center justify-between
                               border-b border-dashed
                               border-slate-400
                               px-2 py-1
                               dark:border-slate-500"
                    >
                        <div
                            class="flex w-full
                                   items-center justify-between"
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

                            <div
                                :class="[
                                    'inline-flex items-center rounded-sm border px-2 py-0.5 text-[11px] font-semibold',
                                    getStatusClasses(enrollment.status).badge,
                                ]"
                                :title="t('status')"
                            >
                                {{ getStatusLabel(enrollment.status) }}
                            </div>
                        </div>
                    </header>

                    <!-- Content -->
                    <div
                        class="flex flex-1 flex-col
                               space-y-2 px-3 py-2"
                    >
                        <!-- Schedule -->
                        <div class="text-center">
                            <div
                                class="text-[12px] font-semibold
                                       text-amber-700
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
                                class="mt-0.5 text-[9px]
                                       text-slate-500
                                       dark:text-slate-400"
                            >
                                {{ enrollment.schedule.slug }}
                            </div>
                        </div>

                        <!-- Course -->
                        <div
                            v-if="getCourseTitle(enrollment)"
                            class="text-center"
                        >
                            <div
                                class="text-[11px] font-semibold
                                       text-blue-700
                                       dark:text-blue-300"
                            >
                                {{ getCourseTitle(enrollment) }}
                            </div>
                        </div>

                        <!-- User -->
                        <div
                            class="space-y-0.5 text-center"
                        >
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

                            <span
                                class="mt-0.5 text-[10px]
                                       text-teal-700
                                       dark:text-teal-300"
                                :title="t('enrolledAt')"
                            >
                                {{ formatDateTime(enrollment.enrolled_at) }}
                            </span>
                        </div>

                        <!-- Notes -->
                        <div class="mt-1">
                            <div
                                class="line-clamp-3 text-center
                                       text-[11px]
                                       text-rose-800
                                       dark:text-rose-200"
                                :title="enrollment.notes || '—'"
                            >
                                {{ enrollment.notes || '—' }}
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <footer
                        class="border-t border-dashed
                               border-slate-400
                               px-3 py-2
                               dark:border-slate-500"
                    >
                        <div
                            class="flex items-center
                                   justify-between gap-2"
                        >
                            <EditIconButton
                                class="shrink-0"
                                :title="t('editNotes')"
                                @click="editNotes(enrollment)"
                            />

                            <select
                                class="block w-full
                                       rounded-sm border
                                       border-slate-500
                                       py-1 pl-3 pr-6
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
