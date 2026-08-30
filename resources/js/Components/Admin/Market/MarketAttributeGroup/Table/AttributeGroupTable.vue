<script setup>
import { defineEmits, defineProps, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import ActivityToggle from '@/Components/Admin/UI/Buttons/ActivityToggle.vue'
import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'
import ModerationButton from '@/Components/Admin/UI/Buttons/ModerationButton.vue'

const { t, locale } = useI18n()

const props = defineProps({
    groups: { type: Array, default: () => [] },
    selectedGroups: { type: Array, default: () => [] },
    isAdmin: { type: Boolean, default: false },
})

const emits = defineEmits([
    'toggle-activity',
    'delete',
    'update-sort-order',
    'toggle-select',
    'toggle-all',
    'approve',
])

/** Локальная копия списка для drag&drop */
const localGroups = ref([])

watch(
    () => props.groups,
    (newVal) => {
        localGroups.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/** Завершение drag&drop сортировки */
const handleDragEnd = () => {
    emits(
        'update-sort-order',
        localGroups.value.map(group => group.id)
    )
}

/** Выделить / снять выделение текущего списка */
const toggleAll = (event) => {
    emits('toggle-all', {
        ids: localGroups.value.map(group => group.id),
        checked: Boolean(event?.target?.checked),
    })
}

/** Все отображаемые группы выделены */
const allSelected = () => {
    return localGroups.value.length > 0
        && localGroups.value.every(group => props.selectedGroups.includes(group.id))
}

/** Текущий перевод */
const groupTranslation = (group) => group?.translation || {}

/** Название группы */
const groupTitle = (group) =>
    groupTranslation(group)?.title
    || `ID: ${group?.id}`

/** Краткое описание */
const groupShort = (group) =>
    groupTranslation(group)?.short || ''

/** Подсказка владельца */
const ownerTitle = (group) => {
    const owner = group?.owner

    if (!owner) return t('noData')

    return `${owner.name || ''}${owner.email ? ' — ' + owner.email : ''}`.trim()
}

/** Аватар владельца */
const ownerAvatar = (group) => {
    return group?.owner?.profile_photo_url
        || '/storage/profile-photos/default-image.png'
}

/**
 * Проверка SVG-иконки.
 *
 * Компонент принимает только строку,
 * внешне похожую на SVG.
 */
const getSafeIcon = (icon) => {
    if (typeof icon !== 'string') return null

    const trimmed = icon.trim()

    if (!trimmed) return null

    return trimmed.startsWith('<svg')
    && trimmed.endsWith('</svg>')
        ? trimmed
        : null
}

/** Название статуса публикации */
const getStatusLabel = (status) => {
    const map = {
        draft: 'statusDraft',
        published: 'statusPublished',
        archived: 'statusArchived',
    }

    return t(map[status] || status || 'no')
}

/** Статус модерации */
const moderationBadge = (status) => {
    const value = Number(status ?? 0)

    if (value === 1) {
        return {
            text: t('statusSelectApproved'),
            class: 'bg-emerald-100 text-emerald-700 border-emerald-300 ' +
                'dark:bg-emerald-900/40 dark:text-emerald-300',
        }
    }

    if (value === 2) {
        return {
            text: t('statusSelectRejected'),
            class: 'bg-rose-100 text-rose-700 border-rose-300 ' +
                'dark:bg-rose-900/40 dark:text-rose-300',
        }
    }

    return {
        text: t('underModeration'),
        class: 'bg-amber-100 text-amber-800 border-amber-300 ' +
            'dark:bg-amber-900/40 dark:text-amber-300',
    }
}

/** Форматирование даты с учётом текущей локали */
const formatDate = (dateStr) => {
    if (!dateStr) return ''

    const date = new Date(dateStr)

    if (Number.isNaN(date.getTime())) return ''

    return date.toLocaleDateString(locale.value || undefined, {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    })
}

/** Ограничение длинного текста */
const truncateText = (text, maxLength = 70) => {
    if (!text) return ''

    const value = String(text)

    return value.length > maxLength
        ? value.slice(0, maxLength).trimEnd() + '…'
        : value
}
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-200 dark:border-slate-600 relative"
    >
        <div
            class="flex items-center justify-between px-3 py-2
                   border-b border-slate-400 dark:border-slate-500"
        >
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('selected') }}: {{ selectedGroups.length }}
            </div>

            <label
                v-if="localGroups.length"
                class="flex items-center text-xs text-slate-600
                       dark:text-slate-200 cursor-pointer"
            >
                <span>{{ t('selectAll') }}</span>

                <input
                    type="checkbox"
                    class="mx-2"
                    :checked="allSelected()"
                    @change="toggleAll"
                />
            </label>
        </div>

        <div class="overflow-x-auto">
            <table
                v-if="localGroups.length"
                class="table-auto w-full text-slate-700 dark:text-slate-100"
            >
                <thead
                    class="text-sm uppercase bg-slate-200 dark:bg-cyan-900
                           border border-solid border-gray-300 dark:border-gray-700"
                >
                <tr>
                    <th class="px-1 py-3 w-px">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-4 h-4 fill-current text-slate-800 dark:text-slate-200"
                            height="24"
                            width="24"
                            viewBox="0 0 24 24"
                        >
                            <path
                                d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"
                            />
                            <path
                                d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"
                            />
                        </svg>
                    </th>

                    <th class="px-1 py-3 whitespace-nowrap w-px">
                        <div class="font-semibold text-center">
                            {{ t('id') }}
                        </div>
                    </th>

                    <th class="px-1 py-3 whitespace-nowrap w-px">
                        <div class="font-semibold text-center">
                            {{ t('owner') }}
                        </div>
                    </th>

                    <th class="px-1 py-3 whitespace-nowrap w-px">
                        <div class="font-semibold text-center">
                            {{ t('icon') }}
                        </div>
                    </th>

                    <th class="px-1 py-3 whitespace-nowrap">
                        <div class="font-semibold text-center">
                            {{ t('title') }}
                        </div>
                    </th>

                    <th class="px-1 py-3 whitespace-nowrap">
                        <div class="font-semibold text-center">
                            {{ t('description') }}
                        </div>
                    </th>

                    <th class="px-1 py-3 whitespace-nowrap">
                        <div class="font-semibold text-center">
                            {{ t('attributes') }}
                        </div>
                    </th>

                    <th class="px-1 py-3 whitespace-nowrap">
                        <div class="font-semibold text-center">
                            {{ t('status') }}
                        </div>
                    </th>

                    <th class="px-1 py-3 whitespace-nowrap">
                        <div class="font-semibold text-end">
                            {{ t('actions') }}
                        </div>
                    </th>

                    <th class="px-1 py-1 whitespace-nowrap text-center">
                        <input
                            type="checkbox"
                            :checked="allSelected()"
                            @change="toggleAll"
                        />
                    </th>
                </tr>
                </thead>

                <draggable
                    tag="tbody"
                    v-model="localGroups"
                    item-key="id"
                    handle=".handle"
                    @end="handleDragEnd"
                >
                    <template #item="{ element: group }">
                        <tr
                            class="text-sm font-semibold border-b-2
                                   hover:bg-slate-100 dark:hover:bg-cyan-800"
                        >
                            <td class="px-1 py-1 text-center cursor-move handle w-px">
                                <svg
                                    class="w-4 h-4 text-gray-500 dark:text-gray-300"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"
                                    />
                                </svg>
                            </td>

                            <td class="px-1 py-1 whitespace-nowrap w-px">
                                <div
                                    class="text-center text-blue-600 dark:text-blue-200"
                                    :title="`[${group.sort}] / ${formatDate(group.published_at)}`"
                                >
                                    {{ group.id }}
                                </div>
                            </td>

                            <td class="px-1 py-1">
                                <div class="flex justify-center">
                                    <img
                                        :src="ownerAvatar(group)"
                                        :title="ownerTitle(group)"
                                        class="h-6 w-6 rounded-full object-cover
                                               border border-slate-300 dark:border-slate-600"
                                        :alt="t('owner')"
                                    />
                                </div>

                                <div
                                    class="text-[10px] font-semibold text-center
                                           text-slate-700 dark:text-slate-300"
                                >
                                    {{ ownerTitle(group) }}
                                </div>
                            </td>

                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="flex justify-center">
                                    <div
                                        v-if="getSafeIcon(group.icon)"
                                        v-html="getSafeIcon(group.icon)"
                                        class="w-6 h-6 text-slate-700 dark:text-slate-100
                                               flex items-center justify-center"
                                    />

                                    <span
                                        v-else
                                        class="text-slate-400 dark:text-slate-300"
                                    >
                                        —
                                    </span>
                                </div>
                            </td>

                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="flex flex-col items-center space-y-1">
                                    <div
                                        class="text-xs font-semibold text-center
                                               bg-white dark:bg-slate-600
                                               w-fit border-2 px-2 py-0.5 rounded-md"
                                        :style="{ borderColor: group.color || 'transparent' }"
                                        :title="group.show_from_at
                                            ? `${t('show')}: ${group.show_from_at} / ${group.show_to_at || '—'}`
                                            : formatDate(group.published_at)"
                                    >
                                        <span :style="{ color: group.color || '#666666' }">
                                            {{ truncateText(groupTitle(group)) }}
                                        </span>
                                    </div>

                                    <div
                                        class="italic text-center text-xs
                                               text-slate-500 dark:text-slate-400"
                                    >
                                        {{ group.code }}
                                    </div>

                                    <div
                                        class="flex justify-center items-center gap-1"
                                        :title="t('typeColor')"
                                    >
                                        <span
                                            class="inline-block w-5 h-5 rounded-sm
                                                   border border-slate-400"
                                            :style="{ backgroundColor: group.color || 'transparent' }"
                                        ></span>

                                        <span class="text-xs text-slate-700 dark:text-slate-300">
                                            {{ group.color || '—' }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <td class="px-1 py-1">
                                <div class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ truncateText(groupShort(group), 80) }}
                                </div>
                            </td>

                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="text-center text-blue-600 dark:text-blue-300">
                                    {{ group.attributes_count ?? 0 }}
                                </div>
                            </td>

                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="flex flex-col items-center justify-center space-y-1">
                                    <div class="flex items-center justify-center gap-1">
                                        <span
                                            class="text-[10px] px-2 py-1 rounded-sm
                                                   border font-semibold"
                                            :class="moderationBadge(group.moderation_status).class"
                                            :title="group.moderation_note && group.moderated_at
                                                ? `${group.moderation_note} [${formatDate(group.moderated_at)}]`
                                                : null"
                                        >
                                            {{ moderationBadge(group.moderation_status).text }}
                                        </span>

                                        <ModerationButton
                                            :isAdmin="isAdmin"
                                            :status="group?.moderation_status ?? 0"
                                            :initialNote="group?.moderation_note || ''"
                                            mode="toggle"
                                            @submit="({ status, note }) => emits('approve', group, status, note)"
                                        />
                                    </div>

                                    <div class="text-[10px] text-fuchsia-700 dark:text-fuchsia-300">
                                        {{ getStatusLabel(group.status) }}
                                    </div>
                                </div>
                            </td>

                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="flex justify-end space-x-1">
                                    <ActivityToggle
                                        :isActive="group.activity"
                                        :title="group.activity ? t('enabled') : t('disabled')"
                                        @toggle-activity="emits('toggle-activity', group)"
                                    />

                                    <IconEdit
                                        :href="route('admin.marketAttributeGroups.edit', {
                                            marketAttributeGroup: group.id
                                        })"
                                    />

                                    <DeleteIconButton
                                        @delete="emits('delete', group)"
                                    />
                                </div>
                            </td>

                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="text-center">
                                    <input
                                        type="checkbox"
                                        :checked="selectedGroups.includes(group.id)"
                                        @change="emits('toggle-select', group.id)"
                                    />
                                </div>
                            </td>
                        </tr>
                    </template>
                </draggable>
            </table>

            <div
                v-else
                class="p-5 text-center text-slate-700 dark:text-slate-100"
            >
                {{ t('noData') }}
            </div>
        </div>
    </div>
</template>
