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
               border border-slate-200 dark:border-slate-600 relative">
        <div class="overflow-x-auto">
            <table
                v-if="enrollments.length > 0"
                class="table-auto w-full text-slate-700 dark:text-slate-100">

                <thead
                    class="text-sm uppercase bg-slate-200 dark:bg-cyan-900
                           border border-solid border-gray-300 dark:border-gray-700">

                <tr>
                    <th class="px-2 py-3 w-px">
                        <div class="font-medium text-sm">
                            {{ t('id') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="font-medium text-sm text-left">
                            Поток
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="font-medium text-sm text-left">
                            {{ t('users') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="font-medium text-sm text-left">
                            {{ t('status') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="font-medium text-sm text-left">
                            {{ t('enrolledAt') }}
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
                    <td class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
                        <div class="text-left text-slate-800 dark:text-slate-200">
                            {{ enrollment.id }}
                        </div>
                    </td>

                    <!-- Поток -->
                    <td class="px-2 py-3 first:pl-6 last:pr-6 whitespace-nowrap">
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
                                class="inline-flex items-center px-2 py-0 text-xs
                                       font-semibold text-blue-800 dark:text-blue-200
                                       border border-blue-400 rounded-sm">
                                {{ enrollment.status ? getStatusLabel(enrollment.status) : '—' }}
                            </span>
                        </div>
                        <select
                            class="block w-full pl-3 pr-6 py-1 text-xs rounded-sm
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
                        <div class="text-xs text-teal-700 dark:text-teal-300">
                            {{ formatDateTime(enrollment.enrolled_at) }}
                        </div>
                    </td>

                    <!-- Заметки -->
                    <td class="px-2 py-3 first:pl-6 last:pr-6 max-w-xs
                               flex flex-row justify-start items-center">
                        <div class="text-xs text-rose-800 dark:text-rose-200 line-clamp-2">
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
