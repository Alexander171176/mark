<script setup>
import { ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import ActivityToggle from '@/Components/Admin/UI/Buttons/ActivityToggle.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'
import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
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

/** Локальный список для drag&drop */
const localTags = ref([])

/** Открытые блоки владельцев */
const openedOwnerBlocks = ref([])

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

/** Название */
const tagTitle = (tag) => {
    return tagTranslation(tag)?.title
        || `ID: ${tag?.id}`
}

/** Краткое описание */
const tagShort = (tag) => {
    return tagTranslation(tag)?.short || ''
}

/** Локаль перевода */
const tagLocale = (tag) => {
    return tagTranslation(tag)?.locale || ''
}

/* ===================== URL ===================== */

/** Публичный URL */
const tagPublicUrl = (tag) => {
    if (!tag?.url) {
        return '#'
    }

    return `/market/tags/${encodeURIComponent(tag.url)}`
}

/* ===================== Owner ===================== */

/** Имя владельца */
const ownerName = (tag) => {
    return tag?.owner?.name
        || t('noData')
}

/** Email владельца */
const ownerEmail = (tag) => {
    return tag?.owner?.email || ''
}

/** Подсказка владельца */
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
    maxLength = 80
) => {
    if (!text) {
        return ''
    }

    const value = String(text)

    return value.length > maxLength
        ? `${value.slice(0, maxLength).trimEnd()}…`
        : value
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

/* ===================== Owner block ===================== */

/** Проверка блока владельца */
const isOwnerBlockOpen = (tagId) => {
    return openedOwnerBlocks.value.includes(
        tagId
    )
}

/** Переключение блока владельца */
const toggleOwnerBlock = (tagId) => {
    if (
        isOwnerBlockOpen(
            tagId
        )
    ) {
        openedOwnerBlocks.value = openedOwnerBlocks.value.filter(
            (id) => id !== tagId
        )

        return
    }

    openedOwnerBlocks.value.push(
        tagId
    )
}
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-400 dark:border-slate-500 relative"
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

        <div
            v-if="localTags.length"
            class="p-3"
        >
            <draggable
                tag="div"
                v-model="localTags"
                item-key="id"
                handle=".handle"
                @end="handleDragEnd"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
            >
                <template #item="{ element: tag }">
                    <div
                        class="relative flex flex-col h-full rounded-md
                               border border-slate-400 dark:border-slate-500
                               bg-slate-50/70 dark:bg-slate-800/80 shadow-sm
                               hover:shadow-md transition-shadow duration-150"
                    >
                        <header
                            class="flex items-center justify-between px-2 py-1
                                   border-b border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-2">
                                <button
                                    type="button"
                                    class="handle cursor-move text-slate-400
                                           hover:text-slate-700 dark:hover:text-slate-100"
                                    :title="t('dragDrop')"
                                >
                                    <svg
                                        class="w-4 h-4"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"
                                        />
                                    </svg>
                                </button>

                                <div
                                    class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm
                                           border border-gray-400 bg-slate-200 dark:bg-slate-700
                                           text-slate-800 dark:text-blue-100"
                                    :title="`[${tagLocale(tag)}] : [${tag.sort}]`"
                                >
                                    ID: {{ tag.id }}
                                </div>

                                <button
                                    type="button"
                                    class="text-slate-400 hover:text-blue-600
                                           dark:hover:text-blue-300"
                                    :title="isOwnerBlockOpen(tag.id)
                                        ? t('hideOwner')
                                        : t('showOwner')"
                                    @click.prevent="toggleOwnerBlock(tag.id)"
                                >
                                    <svg
                                        class="w-4 h-4 transition-transform duration-200"
                                        :class="{ 'rotate-180': isOwnerBlockOpen(tag.id) }"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </button>
                            </div>

                            <div class="flex items-center space-x-2">
                                <span
                                    class="text-[10px] px-2 py-0.5 rounded-sm border font-semibold"
                                    :class="moderationBadge(tag.moderation_status).class"
                                    :title="tag.moderation_note && tag.moderated_at
                                        ? `${tag.moderation_note} [${formatDate(tag.moderated_at)}]`
                                        : null"
                                >
                                    {{ moderationBadge(tag.moderation_status).text }}
                                </span>

                                <input
                                    type="checkbox"
                                    :checked="selectedTags.includes(tag.id)"
                                    @change="emits('toggle-select', tag.id)"
                                />
                            </div>
                        </header>

                        <div class="flex flex-col flex-1 px-3 py-2 space-y-2">
                            <div
                                v-show="isOwnerBlockOpen(tag.id)"
                                class="flex flex-col items-center justify-center text-center"
                            >
                                <img
                                    :src="ownerAvatar(tag)"
                                    :title="ownerTitle(tag)"
                                    class="h-12 w-12 rounded-full object-cover
                                           border border-slate-300 dark:border-slate-600"
                                    :alt="t('owner')"
                                />

                                <div
                                    class="mt-1 text-[11px] font-semibold
                                           text-slate-700 dark:text-slate-100
                                           leading-tight line-clamp-1"
                                    :title="ownerName(tag)"
                                >
                                    {{ ownerName(tag) }}
                                </div>

                                <div
                                    v-if="ownerEmail(tag)"
                                    class="text-[10px] text-slate-500 dark:text-slate-300
                                           leading-tight line-clamp-1"
                                    :title="ownerEmail(tag)"
                                >
                                    {{ ownerEmail(tag) }}
                                </div>

                                <div
                                    v-if="tag.show_from_at"
                                    class="text-center text-[10px]
                                           text-slate-500 dark:text-slate-300"
                                >
                                    {{ t('show') }}:
                                    {{ tag.show_from_at }}
                                    /
                                    {{ tag.show_to_at || '—' }}
                                </div>

                                <div
                                    v-else
                                    class="text-center text-[10px]
                                           text-slate-500 dark:text-slate-300"
                                >
                                    {{ formatDate(tag.published_at) }}
                                </div>
                            </div>

                            <div class="flex justify-center items-center">
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
                            </div>

                            <div class="text-center text-xs text-slate-500 dark:text-slate-400">
                                {{ truncateText(tag.url, 90) }}
                            </div>

                            <div
                                v-if="tag.color"
                                class="flex items-center justify-center gap-1
                                       font-semibold text-xs
                                       text-slate-700 dark:text-slate-300"
                            >
                                <span
                                    class="inline-block w-4 h-4 rounded-sm
                                           border border-slate-400"
                                    :style="{ backgroundColor: tag.color }"
                                />

                                <span>
                                    {{ tag.color }}
                                </span>
                            </div>

                            <div
                                v-if="tagShort(tag)"
                                class="font-semibold text-[12px] text-center
                                       text-cyan-700 dark:text-cyan-300"
                            >
                                {{ truncateText(tagShort(tag), 120) }}
                            </div>

                            <div
                                class="flex items-center justify-center gap-3 text-[11px]
                                       font-semibold text-slate-600 dark:text-slate-200"
                            >
                                <div
                                    class="flex items-center justify-center space-x-1"
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

                                    <span class="text-[12px] text-slate-700 dark:text-slate-200">
                                        {{ tag.views ?? 0 }}
                                    </span>
                                </div>
                            </div>

                            <div
                                class="font-semibold text-center text-[11px]
                                       text-fuchsia-700 dark:text-fuchsia-300"
                            >
                                <span>{{ t('status') }}: </span>
                                {{ getStatusLabel(tag.status) }}
                            </div>

                            <div class="flex justify-center space-x-1">
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
                        </div>

                        <div
                            class="flex items-center justify-center px-3 py-2
                                   border-t border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-1">
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
                        </div>
                    </div>
                </template>
            </draggable>
        </div>

        <div
            v-else
            class="p-5 text-center text-slate-700 dark:text-slate-100"
        >
            {{ t('noData') }}
        </div>
    </div>
</template>
