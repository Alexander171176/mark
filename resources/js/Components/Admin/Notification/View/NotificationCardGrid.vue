<script setup>
import { defineEmits, defineProps } from 'vue'
import { useI18n } from 'vue-i18n'

import IconShow from '@/Components/Admin/UI/Buttons/IconShow.vue'
import MarkAsReadButton from '@/Components/Admin/UI/Buttons/MarkAsReadButton.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'

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
               border border-slate-400 dark:border-slate-500 relative"
    >
        <!-- Сетка карточек -->
        <div
            v-if="notifications.length"
            class="p-3"
        >
            <div class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="notification in notifications"
                    :key="notification.id"
                    class="relative flex flex-col h-full rounded-md
                           border border-slate-400 dark:border-slate-500
                           bg-slate-50/70 dark:bg-slate-800/80 shadow-sm
                           hover:shadow-md transition-shadow duration-150"
                    :class="{
                        'ring-1 ring-blue-300 dark:ring-blue-700':
                            !notification.is_read,
                    }"
                >
                    <!-- Верхняя панель -->
                    <header
                        class="flex items-center justify-between gap-2 px-2 py-1
                               border-b border-dashed
                               border-slate-400 dark:border-slate-500"
                    >
                        <!-- Принадлежность -->
                        <div
                            class="text-[10px] font-semibold px-1.5 py-0.5
                                   rounded-sm border"
                            :class="
                                notification.is_own
                                    ? 'border-emerald-400 bg-emerald-50 text-emerald-700 ' +
                                     'dark:bg-emerald-900/40 dark:text-emerald-300'
                                    : 'border-gray-400 bg-slate-200 text-slate-600 ' +
                                     'dark:bg-slate-700 dark:text-slate-300'
                            "
                        >
                            {{ notification.is_own ? t('my') : t('SomeoneElse') }}
                        </div>

                        <!-- Состояние -->
                        <span
                            class="text-[10px] px-2 py-0.5 rounded-sm
                                   border font-semibold"
                            :class="readStatusBadge(notification)"
                        >
                            {{ readStatusLabel(notification) }}
                        </span>
                    </header>

                    <!-- Контент карточки -->
                    <div class="flex flex-col flex-1 px-3 py-2 space-y-2">

                        <!-- Связанная сущность -->
                        <div
                            class="flex flex-col items-center justify-center"
                        >
                            <template
                                v-if="notification.entity_type || notification.entity_id"
                            >
                                <div class="flex flex-row items-center justify-center gap-3">
                                    <div
                                        class="font-semibold text-cyan-700
                                           dark:text-cyan-300
                                           text-sm text-center break-words"
                                    >
                                        {{ entityTypeLabel(notification.entity_type) }}
                                    </div>

                                    <div
                                        v-if="notification.entity_id"
                                        class="font-semibold text-xs
                                               text-slate-500 dark:text-slate-300"
                                    >
                                        ID:
                                        {{ notification.entity_id }}
                                    </div>
                                </div>
                            </template>

                            <div
                                v-else
                                class="text-xs font-semibold
                                       text-slate-400 dark:text-slate-400"
                            >
                                —
                            </div>
                        </div>

                        <!-- Дата создания -->
                        <div
                            class="text-xs font-semibold text-center
                                       text-indigo-600 dark:text-indigo-200"
                        >
                            {{ formatDate(notification.created_at) }}
                        </div>

                        <!-- Категория / уровень -->
                        <div
                            class="flex flex-wrap items-center justify-center gap-2"
                        >
                            <span
                                class="text-[10px] font-semibold
                                       px-2 py-1 rounded-sm border"
                                :class="categoryBadge(notification.category)"
                            >
                                {{ categoryLabel(notification.category) }}
                            </span>

                            <span
                                class="text-[10px] font-semibold
                                       px-2 py-1 rounded-sm border"
                                :class="levelBadge(notification.level)"
                            >
                                {{ levelLabel(notification.level) }}
                            </span>
                        </div>

                        <!-- Уведомление -->
                        <div
                            class="text-center pb-2
                                   border-b border-dashed
                                   border-slate-300 dark:border-slate-600"
                        >
                            <button
                                type="button"
                                class="text-sm font-semibold break-words
                                       text-blue-700 dark:text-blue-300
                                       hover:text-blue-900 dark:hover:text-blue-200
                                       hover:underline"
                                @click="openNotification(notification)"
                            >
                                {{ notification.title || '—' }}
                            </button>

                            <div
                                v-if="notification.message"
                                class="mt-1 text-xs font-normal
                                       text-slate-600 dark:text-slate-300
                                       break-words"
                            >
                                {{ notification.message }}
                            </div>

                            <div
                                v-if="notification.type"
                                class="mt-2 text-[10px] font-semibold
                                       text-fuchsia-700 dark:text-fuchsia-300
                                       break-all"
                                :title="notification.type"
                            >
                                {{ notification.type }}
                            </div>
                        </div>

                        <!-- Получатель -->
                        <div class="flex flex-col items-center justify-center">
                            <div
                                class="text-[11px] uppercase tracking-wide
                                       font-semibold text-slate-500 dark:text-slate-400"
                            >
                                {{ t('recipient') }}
                            </div>

                            <div
                                class="font-semibold text-violet-700
                                       dark:text-violet-300
                                       text-sm text-center break-words"
                            >
                                {{ notifiableName(notification) }}
                            </div>

                            <div
                                v-if="notifiableEmail(notification)"
                                class="text-[10px] text-cyan-700
                                       dark:text-cyan-500 break-all
                                       text-center font-semibold"
                            >
                                {{ notifiableEmail(notification) }}
                            </div>

                            <div
                                v-if="notification.notifiable?.id"
                                class="text-xs text-slate-700
                                       dark:text-slate-300 font-semibold"
                            >
                                ID:
                                {{ notification.notifiable.id }}
                            </div>

                            <div
                                v-if="notification.notifiable?.type"
                                class="max-w-full text-[10px]
                                       text-slate-400 dark:text-slate-400
                                       break-all text-center"
                            >
                                {{ notification.notifiable.type }}
                            </div>
                        </div>

                        <!-- Дата прочтения -->
                        <div
                            v-if="notification.read_at"
                            class="flex flex-col items-center justify-center"
                        >
                            <div
                                class="text-[10px] uppercase tracking-wide
                                       font-semibold text-slate-500 dark:text-slate-400"
                            >
                                {{ t('read') }}
                            </div>

                            <div
                                class="text-[10px] text-center
                                       text-emerald-700 dark:text-emerald-300"
                            >
                                {{ formatDate(notification.read_at) }}
                            </div>
                        </div>

                        <!-- UUID -->
                        <div
                            class="mt-auto text-[9px] text-center
                                   text-slate-400 dark:text-slate-500
                                   break-all"
                            :title="notification.id"
                        >
                            ID: {{ notification.id }}
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
