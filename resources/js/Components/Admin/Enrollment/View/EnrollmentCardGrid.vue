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
        return new Date(value).toLocaleString('ru-RU')
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

const handleDelete = (enrollment) => {
    emit('delete', enrollment.id)
}
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-400 dark:border-slate-500 relative"
    >
        <div
            v-if="enrollments.length"
            class="p-3"
        >
            <div class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
                <article
                    v-for="enrollment in enrollments"
                    :key="enrollment.id"
                    class="relative flex flex-col h-full rounded-md
                           border border-slate-400 dark:border-slate-500
                           bg-slate-50/70 dark:bg-slate-800/80 shadow-sm
                           hover:shadow-md transition-shadow duration-150"
                >
                    <!-- Верхняя панель: статус + ID + started_at -->
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
                                :title="t('status')"
                            >
                                {{ getStatusLabel(enrollment.status) }}
                            </span>
                            <span
                                class="mt-0.5 text-[10px]
                                       text-slate-500 dark:text-slate-300"
                                :title="t('shortStarted')"
                            >
                                {{ formatDateTime(enrollment.started_at) }}
                            </span>
                        </div>

                        <div
                            class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm
                                   border border-gray-400
                                   bg-slate-200 dark:bg-slate-700
                                   text-slate-800 dark:text-blue-100"
                        >
                            ID: {{ enrollment.id }}
                        </div>
                    </header>

                    <!-- Контент -->
                    <div class="flex flex-col flex-1 px-3 py-2 space-y-2">
                        <!-- Пользователь -->
                        <div class="text-center">
                            <div
                                class="text-[12px] font-medium text-slate-900 dark:text-slate-50"
                            >
                                {{ enrollment.user?.name || `#${enrollment.user_id}` }}
                            </div>
                            <div
                                v-if="enrollment.user?.email"
                                class="text-[11px] text-slate-500 dark:text-slate-300"
                            >
                                {{ enrollment.user.email }}
                            </div>
                        </div>

                        <!-- Курс -->
                        <div class="text-center space-y-0.5
                                    border border-dotted border-slate-400 dark:border-slate-500">
                            <div
                                class="text-[12px] font-semibold
                                       text-sky-700 dark:text-sky-200 line-clamp-2"
                                :title="enrollment.course?.slug"
                            >
                                {{ enrollment.course?.title || `#${enrollment.course_id}` }}
                            </div>
                        </div>

                        <!-- Поток / расписание -->
                        <div class="h-12 flex flex-col justify-center
                                    text-center space-y-0.5">
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
                                <span v-if="enrollment.schedule?.ends_at"> — <br></span>
                                <span v-if="enrollment.schedule?.ends_at">
                                    {{ formatDateTime(enrollment.schedule.ends_at) }}
                                </span>
                            </div>
                        </div>

                        <!-- Доступ и прогресс -->
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
                                <span
                                    class="text-[10px] text-slate-500 dark:text-slate-300"
                                >
                                    {{ formatDaysLeft(enrollment.days_left) }}
                                </span>
                            </div>

                            <div class="flex flex-col items-center">
                                <span
                                    class="text-[11px] text-slate-700 dark:text-slate-200 mb-0.5"
                                >
                                    {{ t('progress') }}:
                                    {{ enrollment.progress_percent ?? 0 }}%
                                </span>
                                <div
                                    class="w-24 h-1.5 bg-slate-300 dark:bg-slate-600 rounded-full
                                           overflow-hidden"
                                >
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
                        </div>

                        <!-- Заметки (кратко) -->
                        <div class="mt-1">
                            <div
                                class="text-[11px] text-gray-800 dark:text-gray-200
                                       text-center font-semibold line-clamp-3"
                                :title="enrollment.notes || '—'"
                            >
                                {{ enrollment.notes || '—' }}
                            </div>
                        </div>
                    </div>

                    <!-- Низ карточки: сертификат + действия -->
                    <footer
                        class="px-3 py-2 border-t border-dashed
                               border-slate-400 dark:border-slate-500"
                    >
                        <div class="flex items-center justify-between space-x-2">
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
