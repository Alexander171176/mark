<script setup>
import { defineProps, defineEmits } from 'vue'
import { useI18n } from 'vue-i18n'

import ActivityToggle from "@/Components/Admin/Buttons/ActivityToggle.vue"
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'
import ModerationButton from '@/Components/Admin/Buttons/ModerationButton.vue'

const { t } = useI18n()

/** Входящие пропсы */
const props = defineProps({
    comments: { type: Array, default: () => [] },
    selectedComments: { type: Array, default: () => [] },
    isAdmin: { type: Boolean, default: false },
})

/** обработчики */
const emits = defineEmits([
    'toggle-activity',
    'edit',
    'delete',
    'toggle-select',
    'toggle-all',
    'view-details',
    'approve-comment',
])

/** массовые чекбоксы */
const toggleAll = (event) => {
    const isChecked = event.target.checked
    props.comments.forEach(comment => {
        if (isChecked && !props.selectedComments.includes(comment.id)) {
            emits('toggle-select', comment.id)
        } else if (!isChecked && props.selectedComments.includes(comment.id)) {
            emits('toggle-select', comment.id)
        }
    })
}

/** форматирование даты */
const formatDate = (dateString) => {
    if (!dateString) return ''
    const date = new Date(dateString)
    if (isNaN(date)) return ''
    return new Intl.DateTimeFormat('ru-RU', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(date)
}

/** бейджи модерации */
const moderationBadge = (status) => {
    const s = Number(status ?? 0)

    if (s === 1) {
        return {
            text: t('statusSelectApproved'),
            class:
                'bg-emerald-100 text-emerald-700 ' +
                'border-emerald-300 dark:bg-emerald-900/40 ' +
                'dark:text-emerald-300',
        }
    }

    if (s === 2) {
        return {
            text: t('statusSelectRejected'),
            class:
                'bg-rose-100 text-rose-700 ' +
                'border-rose-300 dark:bg-rose-900/40 ' +
                'dark:text-rose-300',
        }
    }

    return {
        text: t('underModeration'),
        class:
            'bg-amber-100 text-amber-800 ' +
            'border-amber-300 dark:bg-amber-900/40 ' +
            'dark:text-amber-300',
    }
}
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
           border border-slate-200 dark:border-slate-600 relative"
    >
        <!-- Верхняя панель -->
        <div
            class="flex items-center justify-between px-3 py-2
             border-b border-slate-400 dark:border-slate-500"
        >
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('selected') }}: {{ selectedComments.length }}
            </div>

            <label
                v-if="comments.length"
                class="flex items-center text-xs text-slate-600
               dark:text-slate-200 cursor-pointer"
            >
                <span>{{ t('selectAll') }}</span>
                <input type="checkbox" class="mx-2" @change="toggleAll" />
            </label>
        </div>

        <div class="overflow-x-auto">
            <table
                v-if="comments.length > 0"
                class="table-auto w-full text-slate-700 dark:text-slate-100"
            >
                <thead
                    class="text-sm uppercase bg-slate-200 dark:bg-cyan-900
                 border border-solid border-gray-300 dark:border-gray-700"
                >
                <tr>
                    <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap w-px">
                        <div class="font-medium text-center">{{ t('id') }}</div>
                    </th>

                    <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                        <div class="font-medium text-left">{{ t('name') }}</div>
                    </th>

                    <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                        <div class="font-medium text-left">{{ t('comment') }}</div>
                    </th>

                    <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                        <div class="font-medium text-center">{{ t('object') }}</div>
                    </th>

                    <!-- колонка модерации -->
                    <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                        <div class="font-medium text-center">{{ t('status') }}</div>
                    </th>

                    <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                        <div class="font-medium text-end">{{ t('actions') }}</div>
                    </th>

                    <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap w-px">
                        <div class="font-semibold text-center">
                            <input type="checkbox" @change="toggleAll" />
                        </div>
                    </th>
                </tr>
                </thead>

                <tbody>
                <tr
                    v-for="comment in comments"
                    :key="comment.id"
                    class="text-sm font-semibold border-b-2
                           hover:bg-slate-100 dark:hover:bg-cyan-800"
                >
                    <td class="px-2 first:pl-5 last:pr-5 py-1 whitespace-nowrap">
                        <div class="text-center">{{ comment.id }}</div>
                    </td>

                    <td class="px-2 first:pl-5 last:pr-5 py-1 whitespace-nowrap">
                        <div class="font-semibold text-blue-700 dark:text-blue-300">
                            {{ comment.user?.name }}
                        </div>
                        <div class="text-xs text-slate-500 dark:text-slate-300">
                            ID: {{ comment.user_id }}
                        </div>
                    </td>

                    <td class="px-2 first:pl-5 last:pr-5 py-1 whitespace-nowrap">
                        <div
                            class="text-xs text-left text-slate-600 dark:text-slate-200
                                   cursor-pointer text-wrap"
                            :title="formatDate(comment.updated_at)"
                        >
                            {{ (comment?.content || '').length > 160
                            ? (comment.content || '').slice(0, 160) + '…'
                            : (comment?.content || '') }}
                        </div>
                    </td>

                    <td class="px-2 first:pl-5 last:pr-5 py-1">
                        <div class="flex flex-row items-center justify-center gap-1
                                    text-xs font-semibold text-violet-700 dark:text-violet-300">
                            <div>
                                {{ (comment?.commentable_type || '').split('\\').pop() }}
                            </div>
                            <div>
                                {{ t('id') }}: {{ comment.commentable_id }}
                            </div>
                        </div>
                        <div v-if="comment.commentable_title"
                             class="text-xs text-fuchsia-700 dark:text-fuchsia-300 line-clamp-2">
                            {{ comment.commentable_title }}
                        </div>
                    </td>

                    <!-- модерация -->
                    <td class="px-2 first:pl-5 last:pr-5 py-1 whitespace-nowrap">
                        <div class="flex items-center justify-center gap-1">
                            <span
                                class="text-[10px] px-2 py-1 rounded-sm border font-semibold"
                                :class="moderationBadge(comment?.moderation_status).class"
                                :title="comment?.moderation_note && comment?.moderated_at
                                ? `${comment.moderation_note} [${formatDate(comment.moderated_at)}]`
                                : null"
                            >
                              {{ moderationBadge(comment?.moderation_status).text }}
                            </span>

                            <ModerationButton
                                :isAdmin="isAdmin"
                                :status="comment?.moderation_status ?? 0"
                                :initialNote="comment?.moderation_note || ''"
                                mode="toggle"
                                @submit="({ status, note }) =>
                                $emit('approve-comment', comment, status, note)"
                            />
                        </div>
                    </td>

                    <td class="px-2 first:pl-5 last:pr-5 py-1 whitespace-nowrap">
                        <div class="flex justify-end space-x-2">
                            <!-- просмотр -->
                            <button
                                @click="$emit('view-details', comment)"
                                :title="t('view')"
                                class="flex items-center py-1 px-0 rounded
                                       border border-slate-300 hover:border-blue-500
                                       dark:border-blue-300 dark:hover:border-blue-100"
                            >
                                <svg
                                    class="w-4 h-4 shrink-0 fill-current text-blue-500 mx-1"
                                    viewBox="0 0 16 16"
                                >
                                    <path
                                        d="M5 9h11v2H5V9zM0 9h3v2H0V9zm5 4h6v2H5v-2zm-5 0h3v2H0v-2zm5-8h7v2H5V5zM0 5h3v2H0V5zm5-4h11v2H5V1zM0 1h3v2H0V1z"
                                    />
                                </svg>
                            </button>

                            <ActivityToggle
                                :isActive="comment.activity"
                                @toggle-activity="$emit('toggle-activity', comment)"
                                :title="comment.activity ? t('enabled') : t('disabled')"
                            />

                            <DeleteIconButton @click="$emit('delete', comment.id)" />
                        </div>
                    </td>

                    <td class="px-2 first:pl-5 last:pr-5 py-1 whitespace-nowrap">
                        <div class="text-center">
                            <input
                                type="checkbox"
                                :checked="selectedComments.includes(comment.id)"
                                @change="$emit('toggle-select', comment.id)"
                            />
                        </div>
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
