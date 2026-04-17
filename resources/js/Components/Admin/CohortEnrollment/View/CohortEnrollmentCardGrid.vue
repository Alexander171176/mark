<script setup>
import { defineProps } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router } from '@inertiajs/vue3'
import EditIconButton from '@/Components/Admin/Buttons/EditIconButton.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    enrollments: {
        type: Array,
        default: () => []
    }
})

// Базовый список статусов
const statusOptions = [
    'pending',
    'approved',
    'rejected',
    'cancelled'
]

// Мапа "значение в БД" → ключ перевода (flat i18n)
const statusLabelKeyMap = {
    pending: 'statusSelectPending',
    approved: 'statusSelectApproved',
    rejected: 'statusSelectRejected',
    cancelled: 'statusSelectCancelled',
}

// Хелпер для перевода статуса (без лишних предупреждений i18n)
const getStatusLabel = (status) => {
    if (!status) return '—'

    const key = statusLabelKeyMap[status]
    return key ? t(key) : status
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

// Обновление статуса
const updateStatus = (enrollment, status) => {
    if (!status) return

    router.put(
        route('admin.actions.cohortEnrollments.updateStatus', enrollment.id),
        { status },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.success(t('statusUpdated') || 'Статус обновлён'),
            onError: () => toast.error(t('statusUpdateError') || 'Ошибка при обновлении статуса')
        }
    )
}

// Обновление заметок (через prompt — как в таблице)
const editNotes = (enrollment) => {
    const current = enrollment.notes || ''
    const updated = window.prompt(t('notes'), current)

    if (updated === null) return // отмена

    router.put(
        route('admin.actions.cohortEnrollments.updateNotes', enrollment.id),
        { notes: updated },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.success(t('notesUpdated') || 'Заметки обновлены'),
            onError: () => toast.error(t('notesUpdateError') || 'Ошибка при обновлении заметок')
        }
    )
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
                    <!-- Верхняя панель: ID + дата + статус-бейдж -->
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
                                {{ enrollment.status ? getStatusLabel(enrollment.status) : '—' }}
                            </span>
                            <span
                                class="mt-0.5 text-[10px]
                                       text-slate-500 dark:text-slate-300"
                                :title="t('enrolledAt')"
                            >
                                {{ formatDateTime(enrollment.enrolled_at) }}
                            </span>
                        </div>

                        <div class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm
                                       border border-gray-400
                                       bg-slate-200 dark:bg-slate-700
                                       text-slate-800 dark:text-blue-100">
                            ID: {{ enrollment.id }}
                        </div>
                    </header>

                    <!-- Контент -->
                    <div class="flex flex-col flex-1 px-3 py-2 space-y-2">
                        <!-- Поток -->
                        <div class="text-center">
                            <div
                                class="text-[12px] font-semibold text-amber-700 dark:text-amber-200
                                       line-clamp-2">
                                {{ enrollment.schedule?.title || `#${enrollment.course_schedule_id}` }}
                            </div>
                        </div>

                        <!-- Пользователь -->
                        <div class="text-center space-y-0.5">
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

                        <!-- Заметки (кратко) -->
                        <div class="mt-1">
                            <div
                                class="text-[11px] text-rose-800 dark:text-rose-200
                                       text-center line-clamp-3"
                                :title="enrollment.notes || '—'"
                            >
                                {{ enrollment.notes || '—' }}
                            </div>
                        </div>
                    </div>

                    <!-- Низ карточки: статус + кнопка заметок -->
                    <footer
                        class="px-3 py-2 border-t border-dashed
                               border-slate-400 dark:border-slate-500"
                    >
                        <div class="flex items-center justify-between space-x-2">
                            <select
                                class="block w-full pl-3 pr-6 py-1 text-xs rounded-sm
                                       border border-slate-500 focus:border-indigo-500
                                       focus:ring-indigo-500 dark:border-slate-500
                                       dark:bg-slate-800 dark:text-slate-100"
                                :value="enrollment.status"
                                @change="updateStatus(enrollment, $event.target.value)"
                            >
                                <option
                                    v-for="status in statusOptions"
                                    :key="status"
                                    :value="status"
                                >
                                    {{ getStatusLabel(status) }}
                                </option>
                            </select>

                            <EditIconButton
                                class="ml-1"
                                :title="t('editNotes')"
                                @click="editNotes(enrollment)"
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
