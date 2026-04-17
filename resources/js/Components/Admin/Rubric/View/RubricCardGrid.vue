<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import ModerationButton from '@/Components/Admin/Buttons/ModerationButton.vue'
import ActivityToggle from '@/Components/Admin/Buttons/ActivityToggle.vue'
import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'
import CloneIconButton from '@/Components/Admin/Buttons/CloneIconButton.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'

const { t } = useI18n()

/** Входящие пропсы */
const props = defineProps({
    rubrics: { type: Array, default: () => [] },
    selectedRubrics: { type: Array, default: () => [] },
    isAdmin: { type: Boolean, default: false }
})

/** обработчики */
const emits = defineEmits([
    'toggle-activity',
    'delete',
    'update-sort-order',
    'clone',
    'toggle-select',
    'toggle-all',
    'approve'
])

/** Локальная копия для vuedraggable */
const localRubrics = ref([])

watch(
    () => props.rubrics,
    (newVal) => {
        localRubrics.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/** draggable end */
const handleDragEnd = () => {
    const newOrderIds = localRubrics.value.map(rubric => rubric.id)
    emits('update-sort-order', newOrderIds)
}

/** Массовые чекбоксы */
const toggleAll = (event) => {
    const checked = event.target.checked
    const ids = localRubrics.value.map(r => r.id)
    emits('toggle-all', { ids, checked })
}

/** Основное изображение (минимальный order) */
const getPrimaryImage = (rubric) => {
    if (rubric.images && rubric.images.length) {
        return [...rubric.images].sort((a, b) => (a.order ?? 0) - (b.order ?? 0))[0]
    }
    return null
}

/** Форматирование даты */
const formatDate = (dateStr) => {
    if (!dateStr) return ''
    const d = new Date(dateStr)
    if (isNaN(d)) return ''
    return d.toLocaleDateString('ru-RU', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

/** Усечение текста */
const truncateText = (text, maxLength = 80) => {
    if (!text) return ''
    return text.length > maxLength
        ? text.slice(0, maxLength).trimEnd() + '…'
        : text
}

/** имя и email автора */
const ownerName = (rubric) => rubric?.owner?.name || t('noData')
const ownerEmail = (rubric) => rubric?.owner?.email || ''

/** title автора */
const ownerTitle = (rubric) => {
    const o = rubric?.owner
    if (!o) return t('noData')
    return `${o.name || ''}${o.email ? ' — ' + o.email : ''}`.trim()
}

/** путь изображения автора */
const ownerAvatar = (rubric) => {
    return rubric?.owner?.profile_photo_url || '/storage/profile-photos/default-image.png'
}

/** бейджи модерации */
const moderationBadge = (status) => {
    const s = Number(status ?? 0)

    if (s === 1) {
        return {
            text: t('statusSelectApproved'),
            class: 'bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300'
        }
    }

    if (s === 2) {
        return {
            text: t('statusSelectRejected'),
            class: 'bg-rose-100 text-rose-700 border-rose-300 dark:bg-rose-900/40 dark:text-rose-300'
        }
    }

    return {
        text: t('underModeration'),
        class: 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-900/40 dark:text-amber-300'
    }
}

</script>

<template>
    <div class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
                border border-slate-400 dark:border-slate-500 relative">

        <!-- Верхняя панель -->
        <div class="flex items-center justify-between px-3 py-2
                    border-b border-slate-400 dark:border-slate-500">
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('selected') }}: {{ selectedRubrics.length }}
            </div>

            <label
                v-if="localRubrics.length"
                class="flex items-center text-xs
                       text-slate-600 dark:text-slate-200 cursor-pointer"
            >
                <span>{{ t('selectAll') }}</span>
                <input type="checkbox" class="mx-2" @change="toggleAll" />
            </label>
        </div>

        <div v-if="localRubrics.length" class="p-3">
            <draggable
                tag="div"
                v-model="localRubrics"
                item-key="id"
                @end="handleDragEnd"
                handle=".drag-handle"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4"
            >
                <template #item="{ element: rubric }">
                    <div class="relative flex flex-col h-full rounded-md
                                border border-slate-400 dark:border-slate-500
                                bg-slate-50/70 dark:bg-slate-800/80
                                shadow-sm hover:shadow-md transition-shadow duration-150">

                        <!-- Верх -->
                        <header class="flex items-center justify-between px-2 py-1
                                       border-b border-dashed
                                       border-slate-400 dark:border-slate-500">
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
                                    :title="`[${rubric.locale}] : [${rubric.sort}]`"
                                >
                                    ID: {{ rubric.id }}
                                </div>

                                <div
                                    class="text-[10px] px-1.5 py-0.5 rounded-sm
                                           border border-gray-400 uppercase"
                                    :class="rubric.activity
                          ? 'bg-blue-500 text-white'
                          : 'bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100'"
                                    :title="t('localization')"
                                >
                                    {{ rubric.locale }}
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                <div v-if="(rubric.views ?? 0) > 0"
                                     class="flex items-center space-x-1">
                                    <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16">
                                        <path class="fill-current text-blue-600 dark:text-blue-300"
                                              d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"></path>
                                    </svg>
                                    <span class="text-[10px] text-slate-700 dark:text-slate-200"
                                          :title="t('views')">
                                        {{ rubric.views ?? 0 }}
                                    </span>
                                </div>
                                <input
                                    type="checkbox"
                                    :checked="selectedRubrics.includes(rubric.id)"
                                    @change="$emit('toggle-select', rubric.id)"
                                />
                            </div>
                        </header>

                        <!-- Изображение -->
                        <div class="relative w-full bg-slate-200 dark:bg-slate-900">
                            <template v-if="rubric.images && rubric.images.length">
                                <img
                                    :src="getPrimaryImage(rubric)?.webp_url || getPrimaryImage(rubric)?.url"
                                    :alt="getPrimaryImage(rubric)?.alt || t('defaultImageAlt')"
                                    :title="getPrimaryImage(rubric)?.caption || t('postImage')"
                                    class="h-32 w-full object-cover"
                                />
                            </template>
                            <template v-else>
                                <img
                                    src="/storage/rubric_images/default-image.png"
                                    :alt="t('defaultImageTitle')"
                                    class="h-32 w-full object-cover"
                                />
                            </template>
                        </div>

                        <!-- Контент -->
                        <div class="flex flex-col flex-1 px-3 py-2 space-y-2">
                            <!-- Автор -->
                            <div class="flex flex-col items-center justify-center text-center">
                                <img
                                    :src="ownerAvatar(rubric)"
                                    :title="ownerTitle(rubric)"
                                    class="h-12 w-12 rounded-full object-cover
                                           border border-slate-300 dark:border-slate-600"
                                    :alt="t('author')"
                                />
                                <div class="mt-1 text-[11px] font-semibold
                                            text-slate-700 dark:text-slate-100
                                            leading-tight line-clamp-1"
                                     :title="ownerName(rubric)">
                                    {{ ownerName(rubric) }}
                                </div>
                                <div v-if="ownerEmail(rubric)"
                                     class="text-[10px] text-slate-500 dark:text-slate-300
                                            leading-tight line-clamp-1"
                                     :title="ownerEmail(rubric)">
                                    {{ ownerEmail(rubric) }}
                                </div>
                            </div>

                            <div class="flex items-center justify-center text-center">
                                <div class="flex items-center justify-center space-x-2 max-w-full">
                                    <div class="flex items-center justify-center shrink-0">
                                        <div
                                            v-if="rubric.icon"
                                            class="w-6 h-6 text-slate-700 dark:text-slate-100
                                                   flex items-center justify-center"
                                            v-html="rubric.icon"
                                        />
                                        <svg v-else class="w-4 h-4
                                                           text-slate-500 dark:text-slate-300"
                                             fill="currentColor"
                                             viewBox="0 0 16 16">
                                            <path
                                                d="M8 8a3 3 0 100-6 3 3 0 000 6zm2-3a2 2 0 11-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"
                                            />
                                        </svg>
                                    </div>

                                    <a
                                        :href="`/blog/rubrics/${encodeURIComponent(rubric.url)}`"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="text-sm font-semibold
                                               text-sky-700 dark:text-sky-200
                                               hover:text-amber-700 dark:hover:text-amber-200
                                               hover:underline line-clamp-2 text-center"
                                        :title="rubric.title"
                                    >
                                        {{ truncateText(rubric.title) }}
                                    </a>

                                    <span
                                        class="shrink-0 text-[10px] px-1.5 py-0.5
                                               rounded-sm border
                                               border-slate-300 dark:border-slate-600
                                               bg-slate-100 dark:bg-slate-700
                                               text-slate-700 dark:text-slate-200"
                                        :title="`${t('articles')}: ${rubric.articles_count ?? 0}`"
                                    >
                                        {{ rubric.articles_count ?? 0 }}
                                    </span>
                                </div>
                            </div>

                            <!-- Краткое описание -->
                            <div class="font-semibold text-[12px] text-center
                                        text-teal-700 dark:text-teal-200">
                                {{ truncateText(rubric.short) }}
                            </div>

                            <div class="flex justify-center gap-1">
                              <span
                                  class="text-[10px] px-2 py-1 rounded-sm border font-semibold"
                                  :class="moderationBadge(rubric.moderation_status).class"
                                  :title="rubric.moderation_note && rubric.moderated_at
                                ? `${rubric.moderation_note} [${formatDate(rubric.moderated_at)}]`
                                : null"
                              >
                                {{ moderationBadge(rubric.moderation_status).text }}
                              </span>
                                <!-- Модерация (только админ) -->
                                <ModerationButton
                                    :isAdmin="isAdmin"
                                    :status="rubric?.moderation_status ?? 0"
                                    :initialNote="rubric?.moderation_note || ''"
                                    mode="toggle"
                                    @submit="({ status, note }) => $emit('approve', rubric, status, note)"
                                />
                            </div>
                        </div>

                        <!-- Действия -->
                        <footer class="flex items-center justify-center px-3 py-2
                                       border-t border-dashed
                                       border-slate-400 dark:border-slate-500">
                            <div class="flex items-center space-x-1">
                                <ActivityToggle
                                    :isActive="rubric.activity"
                                    @toggle-activity="$emit('toggle-activity', rubric)"
                                    :title="rubric.activity ? t('enabled') : t('disabled')"
                                />
                                <CloneIconButton @clone="$emit('clone', rubric)" />
                                <IconEdit :href="route('admin.rubrics.edit', rubric.id)" />
                                <DeleteIconButton @delete="$emit('delete', rubric.id)" />
                            </div>
                        </footer>
                    </div>
                </template>
            </draggable>
        </div>

        <div v-else class="p-5 text-center text-slate-700 dark:text-slate-100">
            {{ t('noData') }}
        </div>
    </div>
</template>
