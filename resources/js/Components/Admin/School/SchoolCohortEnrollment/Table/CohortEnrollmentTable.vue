<script setup>
import { defineProps } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router } from '@inertiajs/vue3'
import EditIconButton from '@/Components/Admin/UI/Buttons/EditIconButton.vue'

const { t } = useI18n()
const toast = useToast()

defineProps({
    enrollments: {
        type: Array,
        default: () => []
    }
})

// Базовый список статусов для select (контроллер их не ограничивает)
const statusOptions = [
    'pending',
    'approved',
    'rejected',
    'cancelled'
]

// 🔹 Мапа "значение в БД" → "ключ перевода"
// (учитываем, что у тебя flat-структура i18n, без вложенных объектов)
const statusLabelKeyMap = {
    pending: 'statusSelectPending',
    approved: 'statusSelectApproved',
    rejected: 'statusSelectRejected',
    cancelled: 'statusSelectCancelled',
}

// Хелпер для перевода статуса
const getStatusLabel = (status) => {
    return t(statusLabelKeyMap[status] || status)
}

// Цвета статусов
const getStatusClasses = (status) => {
    switch (status) {
        case 'approved':
            return {
                card: 'border-emerald-400 dark:border-emerald-500 ' +
                    'bg-emerald-50/70 dark:bg-emerald-900/20',
                badge: 'border-emerald-400 bg-emerald-100 dark:bg-emerald-900/40 ' +
                    'text-emerald-800 dark:text-emerald-100',
            }

        case 'pending':
            return {
                card: 'border-amber-400 dark:border-amber-500 ' +
                    'bg-amber-50/70 dark:bg-amber-900/20',
                badge: 'border-amber-400 bg-amber-100 dark:bg-amber-900/40 ' +
                    'text-amber-800 dark:text-amber-100',
            }

        case 'rejected':
            return {
                card: 'border-rose-400 dark:border-rose-500 ' +
                    'bg-rose-50/70 dark:bg-rose-900/20',
                badge: 'border-rose-400 bg-rose-100 dark:bg-rose-900/40 ' +
                    'text-rose-800 dark:text-rose-100',
            }

        case 'cancelled':
            return {
                card: 'border-slate-400 dark:border-slate-500 ' +
                    'bg-slate-100/70 dark:bg-slate-800/80',
                badge: 'border-slate-400 bg-slate-200 dark:bg-slate-700 ' +
                    'text-slate-800 dark:text-slate-100',
            }

        default:
            return {
                card: 'border-blue-400 dark:border-blue-500 ' +
                    'bg-blue-50/70 dark:bg-blue-900/20',
                badge: 'border-blue-400 bg-blue-100 dark:bg-blue-900/40 ' +
                    'text-blue-800 dark:text-blue-100',
            }
    }
}

// Форматирование даты
const formatDateTime = (value) => {
    if (!value) return '—'
    try {
        return new Date(value).toLocaleString()
    } catch {
        return value
    }
}

// Обновление статуса одной записи
const updateStatus = (enrollment, status) => {
    if (!status) return

    router.put(
        route('admin.actions.cohortEnrollments.updateStatus', enrollment.id),
        { status },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.success('Статус обновлён'),
            onError: () => toast.error('Ошибка при обновлении статуса')
        }
    )
}

// Обновление заметок (простая версия через prompt)
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
            onSuccess: () => toast.success('Заметки обновлены'),
            onError: () => toast.error('Ошибка при обновлении заметок')
        }
    )
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
                        <div class="font-medium text-center">{{ t('id') }}</div>
                    </th>
                    <th class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="flex items-center justify-center gap-2"
                             :title="t('learningFlow')">
                            <svg class="w-4 h-4 fill-current text-cyan-600 dark:text-cyan-400"
                                 viewBox="0 0 512 512">
                                <path d="M352 96c0-53.02-42.98-96-96-96s-96 42.98-96 96 42.98 96 96 96 96-42.98 96-96zM233.59 241.1c-59.33-36.32-155.43-46.3-203.79-49.05C13.55 191.13 0 203.51 0 219.14v222.8c0 14.33 11.59 26.28 26.49 27.05 43.66 2.29 131.99 10.68 193.04 41.43 9.37 4.72 20.48-1.71 20.48-11.87V252.56c-.01-4.67-2.32-8.95-6.42-11.46zm248.61-49.05c-48.35 2.74-144.46 12.73-203.78 49.05-4.1 2.51-6.41 6.96-6.41 11.63v245.79c0 10.19 11.14 16.63 20.54 11.9 61.04-30.72 149.32-39.11 192.97-41.4 14.9-.78 26.49-12.73 26.49-27.06V219.14c-.01-15.63-13.56-28.01-29.81-27.09z" />
                            </svg>
                            <span class="font-medium">{{ t('learningFlow') }}</span>
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="shrink-0 h-4 w-4"
                                 viewBox="0 0 24 24">
                                <path
                                    class="fill-current text-cyan-400"
                                    d="M18.974 8H22a2 2 0 012 2v6h-2v5a1 1 0 01-1 1h-2a1 1 0 01-1-1v-5h-2v-6a2 2 0 012-2h.974zM20 7a2 2 0 11-.001-3.999A2 2 0 0120 7zM2.974 8H6a2 2 0 012 2v6H6v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5H0v-6a2 2 0 012-2h.974zM4 7a2 2 0 11-.001-3.999A2 2 0 014 7z"></path>
                                <path
                                    class="fill-current text-cyan-600"
                                    d="M12 6a3 3 0 110-6 3 3 0 010 6zm2 18h-4a1 1 0 01-1-1v-6H6v-6a3 3 0 013-3h6a3 3 0 013 3v6h-3v6a1 1 0 01-1 1z"></path>
                            </svg>
                            <span class="font-medium">{{ t('users') }}</span>
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="font-medium text-sm text-center">
                            {{ t('status') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="flex items-center justify-center"
                             :title="t('enrolledAt')">
                            <svg class="w-4 h-4 fill-current shrink-0"
                                 viewBox="0 0 448 512">
                                <path d="M400 64h-48V12c0-6.627-5.373-12-12-12h-40c-6.627 0-12 5.373-12 12v52H160V12c0-6.627-5.373-12-12-12h-40c-6.627 0-12 5.373-12 12v52H48C21.49 64 0 85.49 0 112v352c0 26.51 21.49 48 48 48h352c26.51 0 48-21.49 48-48V112c0-26.51-21.49-48-48-48zm-6 400H54a6 6 0 0 1-6-6V160h352v298a6 6 0 0 1-6 6zm-52.849-200.65L198.842 404.519c-4.705 4.667-12.303 4.637-16.971-.068l-75.091-75.699c-4.667-4.705-4.637-12.303.068-16.971l22.719-22.536c4.705-4.667 12.303-4.637 16.97.069l44.104 44.461 111.072-110.181c4.705-4.667 12.303-4.637 16.971.068l22.536 22.718c4.667 4.705 4.636 12.303-.069 16.97z"></path>
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 py-3">
                        <div class="font-medium text-sm text-left">
                            {{ t('notes') }}
                        </div>
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr
                    v-for="enrollment in enrollments"
                    :key="enrollment.id"
                    class="text-xs font-semibold border-b-2
                           hover:bg-slate-100 dark:hover:bg-cyan-800">
                    <!-- ID -->
                    <td class="px-2 py-3 w-px">
                        <div class="text-left text-slate-800 dark:text-slate-200">
                            {{ enrollment.id }}
                        </div>
                    </td>

                    <!-- Поток -->
                    <td class="px-2 py-3 first:pl-6 last:pr-6">
                        <div class="text-xs text-left text-amber-600 dark:text-amber-200">
                            {{ enrollment.schedule?.title || `#${enrollment.course_schedule_id}` }}
                        </div>
                    </td>

                    <!-- Пользователь -->
                    <td class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="flex flex-col">
                            <span class="text-sm text-slate-700 dark:text-slate-200">
                                {{ enrollment.user?.name || `#${enrollment.user_id}` }}
                            </span>
                            <span
                                v-if="enrollment.user?.email"
                                class="text-xs text-slate-500 dark:text-slate-400">
                                {{ enrollment.user.email }}
                            </span>
                        </div>
                    </td>

                    <!-- Статус -->
                    <td class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap
                               flex flex-col justify-center items-center">
                        <div class="mb-1">
                            <span
                                :class="[
    'inline-flex items-center px-2 py-0.5 rounded-sm border text-[11px] font-semibold',
    getStatusClasses(enrollment.status).badge
]">
                                {{ enrollment.status ? getStatusLabel(enrollment.status) : '—' }}
                            </span>
                        </div>
                        <select
                            class="block w-full pl-3 pr-7 py-0.5 text-xs rounded-sm
                                   border border-slate-500
                                   focus:border-indigo-500 focus:ring-indigo-500
                                   dark:border-slate-500 dark:bg-slate-800 dark:text-slate-100"
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
                    </td>

                    <!-- Дата зачисления -->
                    <td class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="text-[10px] text-teal-700 dark:text-teal-300">
                            {{ formatDateTime(enrollment.enrolled_at) }}
                        </div>
                    </td>

                    <!-- Заметки -->
                    <td class="px-2 py-3 first:pl-6 last:pr-6 max-w-xs
                               flex flex-row justify-center items-center">
                        <div class="text-xs text-rose-800 dark:text-rose-200">
                            {{ enrollment.notes || '—' }}
                        </div>
                        <EditIconButton @click="editNotes(enrollment)"
                                        class="ml-1" />
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
