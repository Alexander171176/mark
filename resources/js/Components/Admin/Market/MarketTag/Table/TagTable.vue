<script setup>
import { ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import ActivityToggle from '@/Components/Admin/UI/Buttons/ActivityToggle.vue'
import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'
import ModerationButton from '@/Components/Admin/UI/Buttons/ModerationButton.vue'

const { t, locale } = useI18n()

const props = defineProps({
    tags: { type: Array, default: () => [] },
    selectedTags: { type: Array, default: () => [] },
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

/** Локальный список тегов для drag&drop */
const localTags = ref([])

/** Синхронизация списка */
watch(
    () => props.tags,
    (newVal) => {
        localTags.value = JSON.parse(
            JSON.stringify(
                Array.isArray(newVal)
                    ? newVal
                    : []
            )
        )
    },
    {
        immediate: true,
        deep: true,
    }
)

/** Завершение drag&drop */
const handleDragEnd = () => {
    emits(
        'update-sort-order',
        localTags.value.map(
            (tag) => tag.id
        )
    )
}

/** Массовое выделение */
const toggleAll = (event) => {
    emits(
        'toggle-all',
        {
            ids: localTags.value.map(
                (tag) => tag.id
            ),
            checked: Boolean(
                event?.target?.checked
            ),
        }
    )
}

/** Проверка полного выделения */
const allSelected = () => {
    if (!localTags.value.length) {
        return false
    }

    return localTags.value.every(
        (tag) => props.selectedTags.includes(
            tag.id
        )
    )
}

/* ===================== Translation ===================== */

/** Текущий перевод */
const tagTranslation = (tag) => {
    return tag?.translation || {}
}

/** Название тега */
const tagTitle = (tag) => {
    return tagTranslation(tag)?.title
        || `ID: ${tag?.id}`
}

/** Краткое описание */
const tagShort = (tag) => {
    return tagTranslation(tag)?.short || ''
}

/* ===================== URL ===================== */

/** Публичный URL тега */
const tagPublicUrl = (tag) => {
    if (!tag?.url) {
        return '#'
    }

    return `/market/tags/${encodeURIComponent(tag.url)}`
}

/* ===================== Owner ===================== */

/** Информация о владельце */
const ownerTitle = (tag) => {
    const owner = tag?.owner

    if (!owner) {
        return t('noData')
    }

    const values = [
        owner.name,
        owner.email,
    ].filter(Boolean)

    return values.length
        ? values.join(' — ')
        : t('noData')
}

/** Аватар владельца */
const ownerAvatar = (tag) => {
    return tag?.owner?.profile_photo_url
        || '/storage/profile-photos/default-image.png'
}

/* ===================== Helpers ===================== */

/** Безопасный SVG */
const getSafeIcon = (icon) => {
    if (
        !icon
        || typeof icon !== 'string'
    ) {
        return null
    }

    const trimmed = icon.trim()

    return trimmed.startsWith('<svg')
    && trimmed.endsWith('</svg>')
        ? trimmed
        : null
}

/** Название статуса */
const getStatusLabel = (status) => {
    const map = {
        draft: 'statusDraft',
        published: 'statusPublished',
        archived: 'statusArchived',
    }

    return t(
        map[status]
        || status
        || 'no'
    )
}

/** Badge модерации */
const moderationBadge = (status) => {
    const value = Number(
        status ?? 0
    )

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

/** Форматирование даты */
const formatDate = (dateStr) => {
    if (!dateStr) {
        return ''
    }

    const date = new Date(dateStr)

    if (
        Number.isNaN(
            date.getTime()
        )
    ) {
        return ''
    }

    return date.toLocaleDateString(
        locale.value || undefined,
        {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        }
    )
}

/** Обрезка текста */
const truncateText = (
    text,
    maxLength = 70
) => {
    if (!text) {
        return ''
    }

    const value = String(text)

    return value.length > maxLength
        ? `${value.slice(0, maxLength).trimEnd()}…`
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
                {{ t('selected') }}: {{ selectedTags.length }}
            </div>

            <label
                v-if="localTags.length"
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
                v-if="localTags.length"
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

                    <th class="px-1 py-3 first:pl-8 last:pr-8 whitespace-nowrap w-px">
                        <div
                            class="flex justify-center"
                            :title="t('icon')"
                        >
                            <svg
                                class="w-6 h-6 fill-current shrink-0"
                                viewBox="0 0 512 512"
                            >
                                <path
                                    d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l96 0 32 0 208 0c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"
                                />
                            </svg>
                        </div>
                    </th>

                    <th class="px-1 py-3 whitespace-nowrap w-px">
                        <div class="font-semibold text-center">
                            {{ t('typeColor') }}
                        </div>
                    </th>

                    <th class="px-1 py-3 whitespace-nowrap">
                        <div class="font-semibold text-left">
                            {{ t('title') }}
                        </div>
                    </th>

                    <th class="px-1 py-3 whitespace-nowrap">
                        <div
                            class="flex justify-center"
                            :title="t('views')"
                        >
                            <svg
                                class="w-4 h-4 fill-current shrink-0"
                                viewBox="0 0 16 16"
                            >
                                <path
                                    class="fill-current text-blue-600 dark:text-blue-300"
                                    d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"
                                />
                            </svg>
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
                    v-model="localTags"
                    item-key="id"
                    handle=".handle"
                    @end="handleDragEnd"
                >
                    <template #item="{ element: tag }">
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
                                    :title="`[${tag.sort}] / ${formatDate(tag.published_at)}`"
                                >
                                    {{ tag.id }}
                                </div>
                            </td>

                            <td class="px-1 py-1">
                                <div class="flex justify-center">
                                    <img
                                        :src="ownerAvatar(tag)"
                                        :title="ownerTitle(tag)"
                                        class="h-6 w-6 rounded-full object-cover
                                               border border-slate-300 dark:border-slate-600"
                                        :alt="t('owner')"
                                    />
                                </div>

                                <div
                                    class="text-[10px] font-semibold text-center
                                           text-slate-700 dark:text-slate-300"
                                >
                                    {{ ownerTitle(tag) }}
                                </div>
                            </td>

                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="flex justify-center">
                                    <div
                                        v-if="getSafeIcon(tag.icon)"
                                        v-html="getSafeIcon(tag.icon)"
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
                                <div class="flex justify-center items-center gap-1">
                                    <span
                                        class="inline-block w-5 h-5 rounded-sm
                                               border border-slate-400"
                                        :style="{ backgroundColor: tag.color || 'transparent' }"
                                    />

                                    <span class="text-xs text-slate-700 dark:text-slate-300">
                                        {{ tag.color || '—' }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-1 py-1">
                                <div class="flex flex-col items-start space-y-1">
                                    <a
                                        :href="tagPublicUrl(tag)"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="text-xs font-semibold text-center
                                               bg-white dark:bg-slate-600
                                               w-fit border-2 px-2 py-0.5 rounded-md"
                                        :style="{ borderColor: tag.color || 'transparent' }"
                                        :title="tag.show_from_at
                                            ? `${t('show')}: ${tag.show_from_at} / ${tag.show_to_at || '—'}`
                                            : formatDate(tag.published_at)"
                                    >
                                        <span :style="{ color: tag.color || '#666666' }">
                                            {{ truncateText(tagTitle(tag)) }}
                                        </span>
                                    </a>

                                    <div class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ truncateText(tag.url, 70) }}
                                    </div>

                                    <div
                                        v-if="tagShort(tag)"
                                        class="text-[10px] text-cyan-700 dark:text-cyan-300"
                                    >
                                        {{ truncateText(tagShort(tag), 80) }}
                                    </div>
                                </div>
                            </td>

                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="text-center text-blue-600 dark:text-blue-300">
                                    {{ tag.views ?? 0 }}
                                </div>
                            </td>

                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="flex flex-col items-center justify-center space-y-1">
                                    <div class="flex items-center justify-center gap-1">
                                        <span
                                            class="text-[10px] px-2 py-1 rounded-sm
                                                   border font-semibold"
                                            :class="moderationBadge(tag.moderation_status).class"
                                            :title="tag.moderation_note && tag.moderated_at
                                                ? `${tag.moderation_note} [${formatDate(tag.moderated_at)}]`
                                                : null"
                                        >
                                            {{ moderationBadge(tag.moderation_status).text }}
                                        </span>

                                        <ModerationButton
                                            :isAdmin="isAdmin"
                                            :status="tag?.moderation_status ?? 0"
                                            :initialNote="tag?.moderation_note || ''"
                                            mode="toggle"
                                            @submit="({ status, note }) => emits('approve', tag, status, note)"
                                        />
                                    </div>

                                    <div class="text-[10px] text-fuchsia-700 dark:text-fuchsia-300">
                                        {{ getStatusLabel(tag.status) }}
                                    </div>
                                </div>
                            </td>

                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="flex justify-end space-x-1">
                                    <ActivityToggle
                                        :isActive="tag.activity"
                                        :title="tag.activity ? t('enabled') : t('disabled')"
                                        @toggle-activity="emits('toggle-activity', tag)"
                                    />

                                    <IconEdit
                                        :href="route('admin.marketTags.edit', { marketTag: tag.id })"
                                    />

                                    <DeleteIconButton
                                        @delete="emits('delete', tag)"
                                    />
                                </div>
                            </td>

                            <td class="px-1 py-1 whitespace-nowrap">
                                <div class="text-center">
                                    <input
                                        type="checkbox"
                                        :checked="selectedTags.includes(tag.id)"
                                        @change="emits('toggle-select', tag.id)"
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
