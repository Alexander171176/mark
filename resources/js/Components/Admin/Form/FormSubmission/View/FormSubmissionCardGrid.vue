<script setup>
import { defineProps, defineEmits } from 'vue'
import { useI18n } from 'vue-i18n'

import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'
import IconShow from '@/Components/Admin/UI/Buttons/IconShow.vue'
import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'

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
               border border-slate-400 dark:border-slate-500 relative"
    >
        <!-- Сетка карточек -->
        <div
            v-if="submissions.length"
            class="p-3"
        >
            <div class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="submission in submissions"
                    :key="submission.id"
                    class="relative flex flex-col h-full rounded-md
                           border border-slate-400 dark:border-slate-500
                           bg-slate-50/70 dark:bg-slate-800/80 shadow-sm
                           hover:shadow-md transition-shadow duration-150"
                >
                    <!-- Верхняя панель -->
                    <header
                        class="flex items-center justify-between px-2 py-1
                               border-b border-dashed
                               border-slate-400 dark:border-slate-500"
                    >
                        <!-- ID -->
                        <div
                            class="text-[10px] font-semibold px-1.5 py-0.5
                                   rounded-sm border border-gray-400
                                   bg-slate-200 dark:bg-slate-700
                                   text-slate-800 dark:text-blue-100"
                        >
                            ID: {{ submission.id }}
                        </div>

                        <!-- Статус -->
                        <span
                            class="text-[10px] px-2 py-0.5 rounded-sm
                                   border font-semibold"
                            :class="statusBadge(submission.status)"
                        >
                            {{ statusLabel(submission.status) }}
                        </span>
                    </header>

                    <!-- Контент карточки -->
                    <div class="flex flex-col flex-1 px-3 py-2 space-y-3">
                        <!-- Форма -->
                        <div
                            class="text-center pb-2
                                   border-b border-dashed
                                   border-slate-300 dark:border-slate-600"
                        >
                            <div
                                class="text-sm font-semibold break-words
                                       text-blue-700 dark:text-blue-300"
                            >
                                {{ formTitle(submission) }}
                            </div>

                            <div
                                v-if="submission.form?.code"
                                class="font-semibold text-[12px] text-fuchsia-700
                                       dark:text-fuchsia-300 break-all"
                            >
                                {{ submission.form.code }}
                            </div>

                            <div
                                class="mt-0.5 text-[11px] font-semibold
                                       text-slate-500 dark:text-slate-400"
                            >
                                {{ t('form') }} ID:
                                {{ submission.form_id }}
                            </div>
                        </div>

                        <!-- Отправитель -->
                        <div class="flex flex-col items-center justify-center">
                            <div
                                class="text-[11px] uppercase tracking-wide
                                       font-semibold text-slate-500 dark:text-slate-400"
                            >
                                {{ t('sender') }}
                            </div>

                            <div
                                class="font-semibold text-amber-700 dark:text-amber-300
                                       text-sm text-center break-words"
                            >
                                {{ senderName(submission) }}
                            </div>

                            <div
                                v-if="senderEmail(submission)"
                                class="text-[10px] text-cyan-700 dark:text-cyan-500 break-all
                                       text-center font-semibold"
                            >
                                {{ senderEmail(submission) }}
                            </div>

                            <div
                                v-if="submission.user_id"
                                class="text-xs text-slate-700
                                       dark:text-slate-300 font-semibold"
                            >
                                ID: {{ submission.user_id }}
                            </div>

                            <div
                                v-else
                                class="text-[10px] text-slate-400
                                       dark:text-slate-400"
                            >
                                {{ t('guest') }}
                            </div>
                        </div>

                        <!-- Ответственный -->
                        <div
                            class="flex flex-col items-center justify-center
                                   pt-2 border-t border-dashed
                                   border-slate-300 dark:border-slate-600"
                        >
                            <div
                                class="text-[11px] uppercase tracking-wide
                                       font-semibold text-slate-500 dark:text-slate-400"
                            >
                                {{ t('assignedUser') }}
                            </div>

                            <template v-if="assignedUser(submission)">
                                <div
                                    class="font-semibold text-violet-700
                                           dark:text-violet-300
                                           text-sm text-center break-words"
                                >
                                    {{ assignedUser(submission).name || '—' }}
                                </div>

                                <div
                                    v-if="assignedUser(submission).email"
                                    class="text-[10px] text-gray-500
                                           dark:text-gray-300
                                           break-all text-center"
                                >
                                    {{ assignedUser(submission).email }}
                                </div>

                                <div
                                    v-if="submission.assigned_user_id"
                                    class="text-xs text-slate-500
                                           dark:text-slate-300"
                                >
                                    ID:
                                    {{ submission.assigned_user_id }}
                                </div>
                            </template>

                            <div
                                v-else
                                class="text-xs font-semibold
                                       text-slate-400 dark:text-slate-400"
                            >
                                {{ t('unassigned') }}
                            </div>
                        </div>

                        <!-- Источник / локаль -->
                        <div
                            class="flex flex-wrap items-center
                                   justify-center gap-2"
                        >
                            <span
                                class="text-[10px] font-semibold px-2 py-1 rounded-sm
                                       border border-cyan-400 dark:border-cyan-500
                                       bg-cyan-50 dark:bg-cyan-900/40
                                       text-cyan-700 dark:text-cyan-300"
                                :title="t('source')"
                            >
                                {{ submission.source || '—' }}
                            </span>

                            <span
                                v-if="submission.locale"
                                class="text-[10px] px-2 py-1 rounded-sm
                                       border border-slate-300 dark:border-slate-600
                                       bg-slate-100 dark:bg-slate-700 font-semibold
                                       text-slate-500 dark:text-slate-300 uppercase"
                                :title="t('locale')"
                            >
                                {{ submission.locale }}
                            </span>
                        </div>

                        <!-- Значения / файлы -->
                        <div
                            class="flex items-center justify-center
                                   gap-2 text-xs"
                        >
                            <span
                                class="inline-flex items-center gap-1 rounded-sm px-2 py-0.5
                                       bg-gray-100 dark:bg-gray-700
                                       border border-slate-300 dark:border-slate-500
                                       text-slate-600 dark:text-slate-200"
                                :title="t('values')"
                            >
                                {{ t('values') }}:
                                {{ submission.values_count ?? 0 }}
                            </span>

                            <span
                                class="inline-flex items-center gap-1 rounded-sm px-2 py-0.5
                                       bg-gray-100 dark:bg-gray-700
                                       border border-slate-300 dark:border-slate-500
                                       text-slate-600 dark:text-slate-200"
                                :title="t('files')"
                            >
                                {{ t('files') }}:
                                {{ submission.files_count ?? 0 }}
                            </span>
                        </div>

                        <!-- Дата отправки -->
                        <div
                            class="flex flex-col items-center justify-center
                                   pt-2 border-t border-dashed
                                   border-slate-300 dark:border-slate-600"
                        >
                            <div
                                class="text-[10px] uppercase tracking-wide
                                       font-semibold text-slate-500 dark:text-slate-400"
                            >
                                {{ t('submittedAt') }}
                            </div>

                            <div
                                class="text-xs font-semibold text-center
                                       text-indigo-600 dark:text-indigo-200"
                                :title="formatDate(submission.created_at)"
                            >
                                {{ formatDate(submission.submitted_at) }}
                            </div>
                        </div>

                        <!-- Обработка -->
                        <div
                            v-if="
                                submission.processed_at
                                || submission.completed_at
                            "
                            class="flex flex-col items-center gap-1"
                        >
                            <div
                                v-if="submission.processed_at"
                                class="text-[10px] text-center
                                       text-amber-700 dark:text-amber-300"
                            >
                                {{ t('processedAt') }}:
                                {{ formatDate(submission.processed_at) }}
                            </div>

                            <div
                                v-if="submission.completed_at"
                                class="text-[10px] text-center
                                       text-emerald-700 dark:text-emerald-300"
                            >
                                {{ t('completedAt') }}:
                                {{ formatDate(submission.completed_at) }}
                            </div>
                        </div>
                    </div>

                    <!-- Нижняя панель: действия -->
                    <div
                        class="px-3 py-2 border-t border-dashed
                               border-slate-400 dark:border-slate-500"
                    >
                        <div class="flex items-center justify-center space-x-2">
                            <!-- Просмотр -->
                            <IconShow
                                :href="route('admin.formSubmissions.show', {
                                    formSubmission: submission.id
                                })"
                            />

                            <!-- Редактирование -->
                            <IconEdit
                                :href="route('admin.formSubmissions.edit', {
                                    formSubmission: submission.id
                                })"
                            />

                            <!-- Удаление -->
                            <DeleteIconButton
                                @click="emits('delete', submission)"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Нет данных -->
        <div
            v-else
            class="p-5 text-center
                   text-slate-700 dark:text-slate-100"
        >
            {{ t('noData') }}
        </div>
    </div>
</template>
