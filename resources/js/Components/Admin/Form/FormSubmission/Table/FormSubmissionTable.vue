<script setup>
import { defineProps, defineEmits } from 'vue'
import { useI18n } from 'vue-i18n'

import IconShow from '@/Components/Admin/UI/Buttons/IconShow.vue'
import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'

const { t } = useI18n()

const props = defineProps({
    submissions: {
        type: Array,
        default: () => [],
    },

    statuses: {
        type: [Array, Object],
        default: () => [],
    },
})

const emits = defineEmits([
    'delete',
])

/*
|--------------------------------------------------------------------------
| Форматирование даты
|--------------------------------------------------------------------------
*/

const formatDate = (dateString) => {
    if (!dateString) {
        return '—'
    }

    const date = new Date(dateString)

    if (Number.isNaN(date.getTime())) {
        return '—'
    }

    return new Intl.DateTimeFormat('ru-RU', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(date)
}

/*
|--------------------------------------------------------------------------
| Форма
|--------------------------------------------------------------------------
*/

const formTitle = (submission) => {
    return submission?.form?.translation?.title
        || submission?.form?.title
        || submission?.form?.code
        || '—'
}

/*
|--------------------------------------------------------------------------
| Отправитель
|--------------------------------------------------------------------------
*/

const senderName = (submission) => {
    return submission?.user?.name
        || t('guest')
}

const senderEmail = (submission) => {
    return submission?.user?.email
        || ''
}

/*
|--------------------------------------------------------------------------
| Ответственный
|--------------------------------------------------------------------------
*/

const assignedUser = (submission) => {
    return submission?.assigned_user
        || submission?.assignedUser
        || null
}

/*
|--------------------------------------------------------------------------
| Название статуса
|--------------------------------------------------------------------------
*/

const statusLabel = (status) => {
    if (
        props.statuses
        && typeof props.statuses === 'object'
        && props.statuses[status]
    ) {
        return props.statuses[status]
    }

    const labels = {
        new: t('statusNew'),
        processing: t('statusProcessing'),
        completed: t('statusCompleted'),
        cancelled: t('statusCancelled'),
        spam: t('statusSpam'),
    }

    return labels[status]
        || status
        || '—'
}

/*
|--------------------------------------------------------------------------
| Оформление статуса
|--------------------------------------------------------------------------
*/

const statusBadge = (status) => {
    const classes = {
        new:
            'bg-blue-100 text-blue-700 border-blue-300 ' +
            'dark:bg-blue-900/40 dark:text-blue-300 dark:border-blue-700',

        processing:
            'bg-amber-100 text-amber-800 border-amber-300 ' +
            'dark:bg-amber-900/40 dark:text-amber-300 dark:border-amber-700',

        completed:
            'bg-emerald-100 text-emerald-700 border-emerald-300 ' +
            'dark:bg-emerald-900/40 dark:text-emerald-300 dark:border-emerald-700',

        cancelled:
            'bg-slate-100 text-slate-700 border-slate-300 ' +
            'dark:bg-slate-800 dark:text-slate-300 dark:border-slate-600',

        spam:
            'bg-rose-100 text-rose-700 border-rose-300 ' +
            'dark:bg-rose-900/40 dark:text-rose-300 dark:border-rose-700',
    }

    return classes[status]
        || (
            'bg-gray-100 text-gray-700 border-gray-300 ' +
            'dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600'
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
                v-if="submissions.length > 0"
                class="table-auto w-full text-slate-700 dark:text-slate-100"
            >
                <thead
                    class="text-sm uppercase bg-slate-200 dark:bg-cyan-900
                           border border-solid border-gray-300 dark:border-gray-700"
                >
                <tr>
                    <!-- ID -->
                    <th
                        class="px-2 first:pl-5 last:pr-5 py-3
                                   whitespace-nowrap w-px"
                    >
                        <div class="font-medium text-center">
                            {{ t('id') }}
                        </div>
                    </th>

                    <!-- Форма -->
                    <th
                        class="px-2 first:pl-5 last:pr-5 py-3
                                   whitespace-nowrap"
                    >
                        <div class="font-medium text-left">
                            {{ t('form') }}
                        </div>
                    </th>

                    <!-- Отправитель -->
                    <th
                        class="px-2 first:pl-5 last:pr-5 py-3
                                   whitespace-nowrap"
                    >
                        <div class="font-medium text-left">
                            {{ t('sender') }}
                        </div>
                    </th>

                    <!-- Статус -->
                    <th
                        class="px-2 first:pl-5 last:pr-5 py-3
                                   whitespace-nowrap"
                    >
                        <div class="font-medium text-center">
                            {{ t('status') }}
                        </div>
                    </th>

                    <!-- Ответственный -->
                    <th
                        class="px-2 first:pl-5 last:pr-5 py-3
                                   whitespace-nowrap"
                    >
                        <div class="font-medium text-left">
                            {{ t('assignedUser') }}
                        </div>
                    </th>

                    <!-- Данные -->
                    <th
                        class="px-2 first:pl-5 last:pr-5 py-3
                                   whitespace-nowrap"
                    >
                        <div class="font-medium text-center">
                            {{ t('data') }}
                        </div>
                    </th>

                    <!-- Дата отправки -->
                    <th
                        class="px-2 first:pl-5 last:pr-5 py-3
                                   whitespace-nowrap"
                    >
                        <div class="font-medium text-center">
                            {{ t('submittedAt') }}
                        </div>
                    </th>

                    <!-- Действия -->
                    <th
                        class="px-2 first:pl-5 last:pr-5 py-3
                                   whitespace-nowrap"
                    >
                        <div class="font-medium text-end">
                            {{ t('actions') }}
                        </div>
                    </th>
                </tr>
                </thead>

                <tbody>
                <tr
                    v-for="submission in submissions"
                    :key="submission.id"
                    class="text-sm font-semibold border-b-2
                               hover:bg-slate-100 dark:hover:bg-cyan-800"
                >
                    <!-- ID -->
                    <td
                        class="px-2 first:pl-5 last:pr-5 py-1
                                   whitespace-nowrap"
                    >
                        <div class="text-center">
                            {{ submission.id }}
                        </div>
                    </td>

                    <!-- Форма -->
                    <td
                        class="px-2 first:pl-5 last:pr-5 py-1"
                    >
                        <div class="flex flex-col min-w-[150px]">
                            <div
                                class="text-xs font-semibold text-blue-700 dark:text-blue-300"
                            >
                                {{ formTitle(submission) }}
                            </div>

                            <div
                                v-if="submission.form?.code"
                                class="text-[10px] text-gray-500 dark:text-gray-300"
                            >
                                {{ submission.form.code }}
                            </div>

                            <div
                                class="text-xs text-slate-500 dark:text-slate-300"
                            >
                                {{ t('id') }}:
                                {{ submission.form_id }}
                            </div>
                        </div>
                    </td>

                    <!-- Отправитель -->
                    <td
                        class="px-2 first:pl-5 last:pr-5 py-1"
                    >
                        <div class="flex flex-col min-w-[140px]">
                            <div
                                class="text-xs font-semibold text-amber-700 dark:text-amber-300"
                            >
                                {{ senderName(submission) }}
                            </div>

                            <div
                                v-if="senderEmail(submission)"
                                class="text-[10px] text-gray-500 dark:text-gray-300 break-all"
                            >
                                {{ senderEmail(submission) }}
                            </div>

                            <div
                                v-if="submission.user_id"
                                class="text-xs text-slate-500 dark:text-slate-300"
                            >
                                {{ t('id') }}:
                                {{ submission.user_id }}
                            </div>

                            <div
                                v-else
                                class="text-[10px] text-slate-400
                                           dark:text-slate-400"
                            >
                                {{ t('guest') }}
                            </div>
                        </div>
                    </td>

                    <!-- Статус / Источник -->
                    <td
                        class="px-2 first:pl-5 last:pr-5 py-1
                                   whitespace-nowrap"
                    >
                        <div class="flex flex-col items-center justify-center gap-1">
                            <span
                                class="w-full px-2 py-0.5 rounded-sm
                                       border font-semibold text-[10px] text-center"
                                :class="statusBadge(submission.status)"
                            >
                                {{ statusLabel(submission.status) }}
                            </span>

                            <span
                                class="w-full px-2 py-0.5 rounded-sm text-[10px] text-center
                                       border border-cyan-400 dark:border-cyan-500
                                       bg-cyan-50 dark:bg-cyan-900/40
                                       text-cyan-700 dark:text-cyan-300"
                            >
                                {{ submission.source || '—' }}
                            </span>

                            <span
                                v-if="submission.locale"
                                class="text-[12px] uppercase
                                       text-slate-500 dark:text-slate-300"
                            >
                                    {{ submission.locale }}
                                </span>
                        </div>
                    </td>

                    <!-- Ответственный -->
                    <td
                        class="px-2 first:pl-5 last:pr-5 py-1"
                    >
                        <div
                            v-if="assignedUser(submission)"
                            class="flex flex-col min-w-[140px]"
                        >
                            <div
                                class="text-xs font-semibold text-violet-700
                                       dark:text-violet-300"
                            >
                                {{ assignedUser(submission).name || '—' }}
                            </div>

                            <div
                                v-if="assignedUser(submission).email"
                                class="text-[10px] text-gray-500
                                           dark:text-gray-300 break-all"
                            >
                                {{ assignedUser(submission).email }}
                            </div>

                            <div
                                v-if="submission.assigned_user_id"
                                class="text-xs text-slate-500
                                           dark:text-slate-300"
                            >
                                {{ t('id') }}:
                                {{ submission.assigned_user_id }}
                            </div>
                        </div>

                        <div
                            v-else
                            class="text-xs text-center text-slate-400
                                       dark:text-slate-400"
                        >
                            {{ t('unassigned') }}
                        </div>
                    </td>

                    <!-- Значения / файлы -->
                    <td
                        class="px-2 first:pl-5 last:pr-5 py-1
                                   whitespace-nowrap"
                    >
                        <div
                            class="flex flex-row items-center justify-center gap-2 text-xs"
                        >
                            <span
                                class="inline-flex items-center gap-1 px-2 py-0.5
                                       bg-gray-100 dark:bg-gray-700
                                       rounded-sm border border-slate-300 dark:border-slate-500
                                       text-center text-slate-600 dark:text-slate-200"
                                :title="t('values')"
                            >
                                {{ submission.values_count ?? 0 }}
                            </span>

                            <span
                                class="inline-flex items-center gap-1 px-2 py-0.5
                                       bg-gray-100 dark:bg-gray-700
                                       rounded-sm border border-slate-300 dark:border-slate-500
                                       text-center text-slate-600 dark:text-slate-200"
                                :title="t('files')"
                            >
                                    {{ submission.files_count ?? 0 }}
                                </span>
                        </div>
                    </td>

                    <!-- Дата отправки -->
                    <td
                        class="px-2 first:pl-5 last:pr-5 py-1
                                   whitespace-nowrap"
                    >
                        <div
                            class="text-xs text-center text-indigo-600 dark:text-indigo-200"
                            :title="formatDate(submission.created_at)"
                        >
                            {{ formatDate(submission.submitted_at) }}
                        </div>

                        <div
                            v-if="submission.processed_at"
                            class="mt-1 text-[10px] text-center
                                       text-amber-700 dark:text-amber-300"
                        >
                            {{ t('processedAt') }}:
                            {{ formatDate(submission.processed_at) }}
                        </div>

                        <div
                            v-if="submission.completed_at"
                            class="mt-1 text-[10px] text-center
                                       text-emerald-700 dark:text-emerald-300"
                        >
                            {{ t('completedAt') }}:
                            {{ formatDate(submission.completed_at) }}
                        </div>
                    </td>

                    <!-- Действия -->
                    <td class="px-2 first:pl-5 last:pr-5 py-1 whitespace-nowrap">
                        <div class="flex justify-end items-center space-x-1">
                            <IconShow
                                :href="route('admin.formSubmissions.show', {
                                    formSubmission: submission.id
                                })"
                            />

                            <IconEdit
                                :href="route('admin.formSubmissions.edit', {
                                    formSubmission: submission.id
                                })"
                            />

                            <DeleteIconButton
                                @delete="emits('delete', submission)"
                            />
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>

            <div
                v-else
                class="p-5 text-center
                       text-slate-700 dark:text-slate-100"
            >
                {{ t('noData') }}
            </div>
        </div>
    </div>
</template>
