<script setup>
import { defineEmits, defineProps } from 'vue'
import { useI18n } from 'vue-i18n'

import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'
import IconShow from '@/Components/Admin/UI/Buttons/IconShow.vue'
import MarkAsReadButton from '@/Components/Admin/UI/Buttons/MarkAsReadButton.vue'

const { t } = useI18n()

defineProps({
    notifications: {
        type: Array,
        default: () => [],
    },
})

const emits = defineEmits([
    'open',
    'read',
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
| Состояние прочтения
|--------------------------------------------------------------------------
*/

const readStatusLabel = (notification) => {
    return notification?.is_read
        ? t('read')
        : t('new')
}

const readStatusBadge = (notification) => {
    if (notification?.is_read) {
        return (
            'bg-gray-100 text-gray-700 border-gray-300 ' +
            'dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500'
        )
    }

    return (
        'bg-fuchsia-100 text-fuchsia-700 border-fuchsia-300 ' +
        'dark:bg-fuchsia-900/40 dark:text-fuchsia-300 dark:border-fuchsia-700'
    )
}

/*
|--------------------------------------------------------------------------
| Категория
|--------------------------------------------------------------------------
*/

const categoryLabel = (category) => {
    const labels = {
        system: t('system'),
        form: t('forms'),
        blog: t('blog'),
        comment: t('comments'),
        review: t('reviews'),
        market: t('marketplace'),
        school: t('school'),
        crm: 'CRM',
    }

    return labels[category]
        || category
        || '—'
}

const categoryBadge = (category) => {
    const classes = {
        system:
            'bg-slate-100 text-slate-700 border-slate-300 ' +
            'dark:bg-slate-800 dark:text-slate-300 dark:border-slate-600',

        form:
            'bg-blue-100 text-blue-700 border-blue-300 ' +
            'dark:bg-blue-900/40 dark:text-blue-300 dark:border-blue-700',

        blog:
            'bg-violet-100 text-violet-700 border-violet-300 ' +
            'dark:bg-violet-900/40 dark:text-violet-300 dark:border-violet-700',

        comment:
            'bg-cyan-100 text-cyan-700 border-cyan-300 ' +
            'dark:bg-cyan-900/40 dark:text-cyan-300 dark:border-cyan-700',

        review:
            'bg-amber-100 text-amber-700 border-amber-300 ' +
            'dark:bg-amber-900/40 dark:text-amber-300 dark:border-amber-700',

        market:
            'bg-emerald-100 text-emerald-700 border-emerald-300 ' +
            'dark:bg-emerald-900/40 dark:text-emerald-300 dark:border-emerald-700',

        school:
            'bg-indigo-100 text-indigo-700 border-indigo-300 ' +
            'dark:bg-indigo-900/40 dark:text-indigo-300 dark:border-indigo-700',

        crm:
            'bg-fuchsia-100 text-fuchsia-700 border-fuchsia-300 ' +
            'dark:bg-fuchsia-900/40 dark:text-fuchsia-300 dark:border-fuchsia-700',
    }

    return classes[category]
        || (
            'bg-gray-100 text-gray-700 border-gray-300 ' +
            'dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600'
        )
}

/*
|--------------------------------------------------------------------------
| Уровень
|--------------------------------------------------------------------------
*/

const levelLabel = (level) => {
    const labels = {
        info: t('information'),
        success: t('successfully'),
        warning: t('warning'),
        error: t('error'),
    }

    return labels[level]
        || level
        || '—'
}

const levelBadge = (level) => {
    const classes = {
        info:
            'bg-blue-50 text-blue-700 border-blue-300 ' +
            'dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-700',

        success:
            'bg-emerald-50 text-emerald-700 border-emerald-300 ' +
            'dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-700',

        warning:
            'bg-amber-50 text-amber-800 border-amber-300 ' +
            'dark:bg-amber-900/30 dark:text-amber-300 dark:border-amber-700',

        error:
            'bg-rose-50 text-rose-700 border-rose-300 ' +
            'dark:bg-rose-900/30 dark:text-rose-300 dark:border-rose-700',
    }

    return classes[level]
        || (
            'bg-gray-50 text-gray-700 border-gray-300 ' +
            'dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600'
        )
}

/*
|--------------------------------------------------------------------------
| Получатель
|--------------------------------------------------------------------------
*/

const notifiableName = (notification) => {
    return notification?.notifiable?.name
        || notification?.notifiable?.email
        || `ID: ${notification?.notifiable?.id ?? '—'}`
}

const notifiableEmail = (notification) => {
    return notification?.notifiable?.email
        || ''
}

/*
|--------------------------------------------------------------------------
| Связанная сущность
|--------------------------------------------------------------------------
*/

const entityTypeLabel = (entityType) => {
    const labels = {
        form_submission: t('submissionForm'),
    }

    return labels[entityType]
        || entityType
        || '—'
}

/*
|--------------------------------------------------------------------------
| Открытие уведомления
|--------------------------------------------------------------------------
*/

const openNotification = (notification) => {
    emits('open', notification)
}

/*
|--------------------------------------------------------------------------
| Отметка как прочитанного
|--------------------------------------------------------------------------
*/

const markAsRead = (notification) => {
    if (
        !notification?.is_own
        || notification?.is_read
    ) {
        return
    }

    emits('read', notification)
}

/*
|--------------------------------------------------------------------------
| Удаление
|--------------------------------------------------------------------------
*/

const deleteNotification = (notification) => {
    if (!notification?.is_own) {
        return
    }

    emits('delete', notification)
}
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-200 dark:border-slate-600 relative"
    >
        <div class="overflow-x-auto">
            <table
                v-if="notifications.length > 0"
                class="table-auto w-full text-slate-700 dark:text-slate-100"
            >
                <thead
                    class="text-sm uppercase bg-slate-200 dark:bg-cyan-900
                           border border-solid border-gray-300 dark:border-gray-700"
                >
                <tr>
                    <!-- Состояние -->
                    <th
                        class="px-2 first:pl-5 last:pr-5 py-3
                                   whitespace-nowrap w-px"
                    >
                        <div class="font-medium text-center">
                            {{ t('submissions') }}
                        </div>
                    </th>

                    <!-- Категория / уровень -->
                    <th
                        class="px-2 first:pl-5 last:pr-5 py-3
                                   whitespace-nowrap"
                    >
                        <div class="font-medium text-center">
                            {{ t('category') }}
                        </div>
                    </th>

                    <!-- Уведомление -->
                    <th
                        class="px-2 first:pl-5 last:pr-5 py-3"
                    >
                        <div class="font-medium text-left">
                            {{ t('notification') }}
                        </div>
                    </th>

                    <!-- Получатель -->
                    <th
                        class="px-2 first:pl-5 last:pr-5 py-3
                                   whitespace-nowrap"
                    >
                        <div class="font-medium text-left">
                            {{ t('recipient') }}
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
                    v-for="notification in notifications"
                    :key="notification.id"
                    class="text-sm font-semibold border-b-2
                               hover:bg-slate-100 dark:hover:bg-cyan-800"
                    :class="{
                            'bg-blue-50/50 dark:bg-blue-950/20':
                                !notification.is_read,
                        }"
                >
                    <!-- Состояние, Связанная сущность, Дата -->
                    <td
                        class="flex flex-col justify-center gap-1
                               px-2 first:pl-5 last:pr-5 py-1
                               whitespace-nowrap"
                    >
                        <div
                            v-if="notification.entity_type || notification.entity_id"
                            class="flex flex-row items-center justify-center gap-2"
                        >
                            <div
                                class="text-xs font-semibold text-cyan-700 dark:text-cyan-300"
                            >
                                {{ entityTypeLabel(notification.entity_type) }}
                            </div>

                            <div
                                v-if="notification.entity_id"
                                class="text-[10px]
                                           text-slate-500 dark:text-slate-300"
                            >
                                ID:
                                {{ notification.entity_id }}
                            </div>
                        </div>

                        <div
                            v-else
                            class="text-xs text-center
                                       text-slate-400 dark:text-slate-400"
                        >
                            —
                        </div>

                        <div
                            class="text-[10px] text-indigo-600 dark:text-indigo-200"
                        >
                            {{ formatDate(notification.created_at) }}
                        </div>

                        <div
                            v-if="notification.read_at"
                            class="mt-1 text-[10px] text-center
                                       text-emerald-700 dark:text-emerald-300"
                        >
                            {{ t('read') }}:
                            {{ formatDate(notification.read_at) }}
                        </div>

                        <div class="flex flex-col items-center justify-center gap-1">
                            <span
                                class="w-full px-2 py-0.5 rounded-sm
                                       border font-semibold text-[10px] text-center"
                                :class="readStatusBadge(notification)"
                            >
                                {{ readStatusLabel(notification) }}
                            </span>

                            <span
                                v-if="notification.is_own"
                                class="text-[10px] text-emerald-600
                                           dark:text-emerald-300"
                            >
                                    {{ t('my') }}
                                </span>

                            <span
                                v-else
                                class="text-[10px] text-slate-400
                                           dark:text-slate-400"
                            >
                                    {{ t('SomeoneElse') }}
                                </span>
                        </div>
                    </td>

                    <!-- Категория / уровень -->
                    <td
                        class="px-2 first:pl-5 last:pr-5 py-1
                                   whitespace-nowrap"
                    >
                        <div class="flex flex-col items-center justify-center gap-1">
                            <span
                                v-if="notification.type"
                                class="max-w-[160px] truncate text-[10px]
                                           text-slate-500 dark:text-slate-300"
                                :title="notification.type"
                            >
                                {{ notification.type }}
                            </span>

                            <span
                                class="w-full px-2 py-0.5 rounded-sm
                                       border font-semibold text-[10px] text-center"
                                :class="categoryBadge(notification.category)"
                            >
                                {{ categoryLabel(notification.category) }}
                            </span>

                            <span
                                class="w-full px-2 py-0.5 rounded-sm
                                           border font-semibold text-[10px] text-center"
                                :class="levelBadge(notification.level)"
                            >
                                {{ levelLabel(notification.level) }}
                            </span>
                        </div>
                    </td>

                    <!-- Уведомление -->
                    <td
                        class="px-2 first:pl-5 last:pr-5 py-2"
                    >
                        <div class="flex flex-col min-w-[260px] max-w-[520px]">
                            <button
                                type="button"
                                class="text-left text-xs font-semibold
                                           text-blue-700 hover:text-blue-900
                                           hover:underline
                                           dark:text-blue-300
                                           dark:hover:text-blue-200"
                                @click="openNotification(notification)"
                            >
                                {{ notification.title || '—' }}
                            </button>

                            <div
                                v-if="notification.message"
                                class="mt-1 text-[10px] text-slate-700 dark:text-slate-300"
                            >
                                {{ notification.message }}
                            </div>

                            <div
                                class="mt-1 text-[10px]
                                           text-slate-400 dark:text-slate-400"
                                :title="notification.id"
                            >
                                ID:
                                {{ notification.id }}
                            </div>
                        </div>
                    </td>

                    <!-- Получатель -->
                    <td
                        class="px-2 first:pl-5 last:pr-5 py-1"
                    >
                        <div class="flex flex-col min-w-[140px]">
                            <div
                                class="text-xs font-semibold
                                           text-violet-700 dark:text-violet-300"
                            >
                                {{ notifiableName(notification) }}
                            </div>

                            <div
                                v-if="notifiableEmail(notification)"
                                class="text-[10px] text-gray-500
                                           dark:text-gray-300 break-all"
                            >
                                {{ notifiableEmail(notification) }}
                            </div>

                            <div
                                v-if="notification.notifiable?.id"
                                class="text-[10px] text-slate-500 dark:text-slate-300"
                            >
                                ID:
                                {{ notification.notifiable.id }}
                            </div>

                            <div
                                v-if="notification.notifiable?.type"
                                class="max-w-[180px] truncate text-[10px]
                                           text-slate-400 dark:text-slate-400"
                                :title="notification.notifiable.type"
                            >
                                {{ notification.notifiable.type }}
                            </div>
                        </div>
                    </td>

                    <!-- Действия -->
                    <td
                        class="px-2 first:pl-5 last:pr-5 py-1
                                   whitespace-nowrap"
                    >
                        <div class="flex justify-end items-center space-x-1">
                            <!-- Просмотр -->
                            <IconShow
                                :title="t('view')"
                                @show="openNotification(notification)"
                            />

                            <!-- Отметить как прочитанное -->
                            <MarkAsReadButton
                                v-if="notification.is_own && !notification.is_read"
                                :title="t('markAsRead')"
                                @read="markAsRead(notification)"
                            />

                            <!-- Удалить -->
                            <DeleteIconButton
                                v-if="notification.is_own"
                                @delete="deleteNotification(notification)"
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
