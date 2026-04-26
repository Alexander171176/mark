<script setup>
import { defineProps, defineEmits, watch, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import ModerationButton from '@/Components/Admin/Buttons/ModerationButton.vue'
import ActivityToggle from '@/Components/Admin/Buttons/ActivityToggle.vue'
import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'

const { t } = useI18n()

/** Входящие пропсы */
const props = defineProps({
    tags: { type: Array, default: () => [] },
    selectedTags: { type: Array, default: () => [] },
    isAdmin: { type: Boolean, default: false },
})

/** События компонента */
const emits = defineEmits([
    'toggle-activity',
    'delete',
    'update-sort-order',
    'toggle-select',
    'toggle-all',
    'approve',
])

/** Локальная копия для vuedraggable */
const localTags = ref([])

watch(
    () => props.tags,
    (newVal) => {
        localTags.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/** Завершение drag-and-drop */
const handleDragEnd = () => {
    const newOrderIds = localTags.value.map(tag => tag.id)
    emits('update-sort-order', newOrderIds)
}

/** Массовый выбор */
const toggleAll = (event) => {
    const checked = event.target.checked
    const ids = localTags.value.map(tag => tag.id)

    emits('toggle-all', { ids, checked })
}

/** Текущий перевод тега */
const tagTranslation = (tag) => tag?.translation || {}

/** Название тега */
const tagName = (tag) => tagTranslation(tag)?.name || `ID: ${tag?.id}`

/** Краткое описание */
const tagShort = (tag) => tagTranslation(tag)?.short || ''

/** Локаль текущего перевода */
const tagLocale = (tag) => tagTranslation(tag)?.locale || ''

/** иконка svg если есть */
const getSafeIcon = (icon) => {
    if (!icon) return null

    const trimmed = icon.trim()

    // Проверяем что это SVG
    if (trimmed.startsWith('<svg') && trimmed.endsWith('</svg>')) {
        return trimmed
    }

    return null
}

/** Форматирование даты */
const formatDate = (dateStr) => {
    if (!dateStr) return ''

    const date = new Date(dateStr)

    if (isNaN(date)) return ''

    return date.toLocaleDateString('ru-RU', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    })
}

/** Усечение текста */
const truncateText = (text, maxLength = 80) => {
    if (!text) return ''

    return text.length > maxLength
        ? text.slice(0, maxLength).trimEnd() + '…'
        : text
}

/** Имя и email автора */
const ownerName = (tag) => tag?.owner?.name || t('noData')
const ownerEmail = (tag) => tag?.owner?.email || ''

/** Title автора */
const ownerTitle = (tag) => {
    const owner = tag?.owner

    if (!owner) return t('noData')

    return `${owner.name || ''}${owner.email ? ' — ' + owner.email : ''}`.trim()
}

/** Аватар автора */
const ownerAvatar = (tag) => {
    return tag?.owner?.profile_photo_url || '/storage/profile-photos/default-image.png'
}

/** Бейдж модерации */
const moderationBadge = (status) => {
    const s = Number(status ?? 0)

    if (s === 1) {
        return {
            text: t('statusSelectApproved'),
            class: 'bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300',
        }
    }

    if (s === 2) {
        return {
            text: t('statusSelectRejected'),
            class: 'bg-rose-100 text-rose-700 border-rose-300 dark:bg-rose-900/40 dark:text-rose-300',
        }
    }

    return {
        text: t('underModeration'),
        class: 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-900/40 dark:text-amber-300',
    }
}
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-400 dark:border-slate-500 relative"
    >
        <!-- Верхняя панель -->
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
                    :checked="localTags.length && localTags.every(tag => selectedTags.includes(tag.id))"
                    @change="toggleAll"
                />
            </label>
        </div>

        <!-- Сетка карточек -->
        <div v-if="localTags.length" class="p-3">
            <draggable
                tag="div"
                v-model="localTags"
                item-key="id"
                @end="handleDragEnd"
                handle=".drag-handle"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4"
            >
                <template #item="{ element: tag }">
                    <div
                        class="relative flex flex-col h-full rounded-md
                               border border-slate-400 dark:border-slate-500
                               bg-slate-50/70 dark:bg-slate-800/80 shadow-sm
                               hover:shadow-md transition-shadow duration-150"
                    >
                        <!-- Верх карточки -->
                        <header
                            class="flex items-center justify-between px-2 py-1
                                   border-b border-dashed
                                   border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-2">
                                <button
                                    type="button"
                                    class="drag-handle cursor-move text-slate-400
                                           hover:text-slate-700 dark:hover:text-slate-100"
                                    :title="t('dragDrop')"
                                >
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" />
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

                                <div
                                    class="text-[10px] px-1.5 py-0.5 rounded-sm
                                           border border-gray-400 uppercase"
                                    :class="tag.activity
                                        ? 'bg-blue-500 text-white'
                                        : 'bg-gray-100 dark:bg-gray-900 ' +
                                         'text-gray-900 dark:text-gray-100'"
                                    :title="t('localization')"
                                >
                                    {{ tagLocale(tag).toUpperCase() }}
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                <div
                                    v-if="(tag.views ?? 0) > 0"
                                    class="flex items-center space-x-1"
                                >
                                    <svg class="w-4 h-4 fill-current shrink-0"
                                         viewBox="0 0 16 16">
                                        <path
                                            class="fill-current text-blue-600 dark:text-blue-300"
                                            d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"
                                        />
                                    </svg>

                                    <span
                                        class="text-[10px] text-slate-700 dark:text-slate-200"
                                        :title="t('views')"
                                    >
                                        {{ tag.views ?? 0 }}
                                    </span>
                                </div>

                                <input
                                    type="checkbox"
                                    :checked="selectedTags.includes(tag.id)"
                                    @change="$emit('toggle-select', tag.id)"
                                />
                            </div>
                        </header>

                        <!-- Контент -->
                        <div class="flex flex-col flex-1 px-3 py-2 space-y-2">
                            <!-- Автор -->
                            <div class="flex flex-col items-center justify-center text-center">
                                <img
                                    :src="ownerAvatar(tag)"
                                    :title="ownerTitle(tag)"
                                    class="h-12 w-12 rounded-full object-cover
                                           border border-slate-300 dark:border-slate-600"
                                    :alt="t('author')"
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
                            </div>

                            <!-- Название -->
                            <div class="flex items-center justify-center text-center">
                                <div class="flex items-center justify-center space-x-2 max-w-full">
                                    <div class="flex items-center justify-center shrink-0">
                                        <div
                                            v-if="getSafeIcon(tag.icon)"
                                            v-html="getSafeIcon(tag.icon)"
                                            class="w-6 h-6 text-slate-700 dark:text-slate-100
                                                   flex items-center justify-center"
                                        />

                                        <svg
                                            v-else
                                            class="w-4 h-4 text-slate-500 dark:text-slate-300"
                                            fill="currentColor"
                                            viewBox="0 0 16 16"
                                        >
                                            <path
                                                d="M8 8a3 3 0 100-6 3 3 0 000 6zm2-3a2 2 0 11-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"
                                            />
                                        </svg>
                                    </div>

                                    <a
                                        :href="`/blog/tags/${encodeURIComponent(tag.slug)}`"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="text-sm font-semibold
                                               text-sky-700 dark:text-sky-200
                                               hover:text-amber-700 dark:hover:text-amber-200
                                               hover:underline line-clamp-2 text-center"
                                        :title="tagName(tag)"
                                    >
                                        {{ tagName(tag) }}
                                    </a>
                                </div>
                            </div>

                            <!-- Краткое описание -->
                            <div
                                class="font-semibold text-[12px] text-center
                                       text-teal-700 dark:text-teal-200"
                            >
                                {{ truncateText(tagShort(tag)) }}
                            </div>

                            <!-- Модерация -->
                            <div class="flex justify-center gap-1">
                                <span
                                    class="text-[10px] px-2 py-1 rounded-sm border font-semibold"
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
                                    @submit="({ status, note }) => $emit('approve', tag, status, note)"
                                />
                            </div>
                        </div>

                        <!-- Действия -->
                        <div
                            class="flex items-center justify-center px-3 py-2
                                   border-t border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-1">
                                <ActivityToggle
                                    :isActive="tag.activity"
                                    @toggle-activity="$emit('toggle-activity', tag)"
                                    :title="tag.activity ? t('enabled') : t('disabled')"
                                />
                                <IconEdit :href="route('admin.blogTags.edit', { blogTag: tag.id })" />
                                <DeleteIconButton @click="$emit('delete', tag)" />
                            </div>
                        </div>
                    </div>
                </template>
            </draggable>
        </div>

        <div v-else class="p-5 text-center text-slate-700 dark:text-slate-100">
            {{ t('noData') }}
        </div>
    </div>
</template>
