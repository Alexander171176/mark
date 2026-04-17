<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import LeftToggle from '@/Components/Admin/Buttons/LeftToggle.vue'
import MainToggle from '@/Components/Admin/Buttons/MainToggle.vue'
import RightToggle from '@/Components/Admin/Buttons/RightToggle.vue'
import ActivityToggle from '@/Components/Admin/Buttons/ActivityToggle.vue'
import CloneIconButton from '@/Components/Admin/Buttons/CloneIconButton.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'
import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'
import ModerationButton from '@/Components/Admin/Buttons/ModerationButton.vue'

const { t } = useI18n()

/** Входящие пропсы */
const props = defineProps({
    articles: { type: Array, default: () => [] },
    selectedArticles: { type: Array, default: () => [] },
    isAdmin: { type: Boolean, default: false }
})

/** обработчики */
const emits = defineEmits([
    'toggle-left',
    'toggle-main',
    'toggle-right',
    'toggle-activity',
    'edit',
    'delete',
    'update-sort-order',
    'toggle-select',
    'toggle-all',
    'clone',
    'approve'
])

/** Локальная копия для vuedraggable */
const localArticles = ref([])

watch(
    () => props.articles,
    (newVal) => {
        localArticles.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/** draggable end */
const handleDragEnd = () => {
    const newOrderIds = localArticles.value.map((article) => article.id)
    emits('update-sort-order', newOrderIds)
}

/** Массовые чекбоксы */
const toggleAll = (event) => {
    const checked = event.target.checked
    const ids = localArticles.value.map((a) => a.id)
    emits('toggle-all', { ids, checked })
}

/** Основное изображение (минимальный order) */
const getPrimaryImage = (article) => {
    if (article.images && article.images.length) {
        return [...article.images].sort((a, b) => a.order - b.order)[0]
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
const ownerName = (article) => article?.owner?.name || t('noData')
const ownerEmail = (article) => article?.owner?.email || ''

/** title автора */
const ownerTitle = (article) => {
    const o = article?.owner
    if (!o) return t('noData')
    return `${o.name || ''}${o.email ? ' — ' + o.email : ''}`.trim()
}

/** путь изображения автора */
const ownerAvatar = (article) => {
    return article?.owner?.profile_photo_url || '/storage/profile-photos/default-image.png'
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
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-400 dark:border-slate-500 relative"
    >
        <!-- Верхняя панель: выбранные + чекбокс "выбрать все" -->
        <div
            class="flex items-center justify-between px-3 py-2
                   border-b border-slate-400 dark:border-slate-500"
        >
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('selected') }}: {{ selectedArticles.length }}
            </div>
            <div class="flex items-center space-x-2">
                <label
                    v-if="localArticles.length"
                    class="flex items-center
                           text-xs text-slate-600 dark:text-slate-200 cursor-pointer"
                >
                    <span>{{ t('selectAll') }}</span>
                    <input type="checkbox" class="mx-2" @change="toggleAll" />
                </label>
            </div>
        </div>

        <div v-if="localArticles.length" class="p-3">
            <draggable
                tag="div"
                v-model="localArticles"
                @end="handleDragEnd"
                item-key="id"
                handle=".handle"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4"
            >
                <template #item="{ element: article }">
                    <div
                        class="relative flex flex-col h-full rounded-md
                               border border-slate-400 dark:border-slate-500
                               bg-slate-50/70 dark:bg-slate-800/80 shadow-sm
                               hover:shadow-md transition-shadow duration-150"
                    >
                        <!-- Верхняя панель: drag, ID/locale, чекбокс -->
                        <header
                            class="flex items-center justify-between px-2 py-1
                                   border-b border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-2">
                                <!-- drag handle -->
                                <button
                                    type="button"
                                    class="handle cursor-move text-slate-400 hover:text-slate-700
                                           dark:hover:text-slate-100"
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

                                <!-- ID -->
                                <div
                                    class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm
                                           border border-gray-400
                                           bg-slate-200 dark:bg-slate-700
                                           text-slate-800 dark:text-blue-100"
                                    :title="`[${article.locale}] : [${article.sort}] ${formatDate(article.published_at)}`"
                                >
                                    ID: {{ article.id }}
                                </div>

                                <!-- Locale -->
                                <div
                                    class="text-[10px] px-1.5 py-0.5 rounded-sm
                                           border border-gray-400 uppercase"
                                    :class="article.activity
                          ? 'bg-blue-500 text-white'
                          : 'bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100'"
                                    :title="t('localization')"
                                >
                                    {{ article.locale }}
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                <!-- чекбокс выбора -->
                                <input
                                    type="checkbox"
                                    :checked="selectedArticles.includes(article.id)"
                                    @change="$emit('toggle-select', article.id)"
                                />
                            </div>
                        </header>

                        <!-- Изображение -->
                        <div class="relative w-full bg-slate-200 dark:bg-slate-900">
                            <template v-if="article.images && article.images.length">
                                <img
                                    :src="getPrimaryImage(article).webp_url || getPrimaryImage(article).url"
                                    :alt="getPrimaryImage(article).alt || t('defaultImageAlt')"
                                    :title="getPrimaryImage(article).caption || t('postImage')"
                                    class="h-32 w-full object-cover"
                                />
                            </template>
                            <template v-else>
                                <img
                                    src="/storage/article_images/default-image.png"
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
                                    :src="ownerAvatar(article)"
                                    :title="ownerTitle(article)"
                                    class="h-12 w-12 rounded-full object-cover
                                           border border-slate-300 dark:border-slate-600"
                                    alt="author"
                                />
                                <div class="mt-1 text-[11px] font-semibold
                                            text-slate-700 dark:text-slate-100
                                            leading-tight line-clamp-1"
                                     :title="ownerName(article)">
                                    {{ ownerName(article) }}
                                </div>
                                <div v-if="ownerEmail(article)"
                                     class="text-[10px] text-slate-500 dark:text-slate-300
                                            leading-tight line-clamp-1"
                                     :title="ownerEmail(article)">
                                    {{ ownerEmail(article) }}
                                </div>
                            </div>

                            <!-- дата публикации -->
                            <div v-if="article.show_from_at"
                                 class="flex flex-col items-center justify-center
                                        text-center text-[10px] text-slate-500 dark:text-slate-300">
                            {{ t('show') }}: {{ article.show_from_at}} / {{ article.show_to_at }}
                            </div>
                            <div v-else
                                 class="flex flex-col items-center justify-center
                                        text-center text-[10px] text-slate-500 dark:text-slate-300">
                                {{ formatDate(article.published_at) }}
                            </div>

                            <!-- Заголовок -->
                            <a
                                :href="`/blog/articles/${encodeURIComponent(article.url)}`"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-xs font-semibold
                                       text-sky-700 dark:text-sky-200 hover:underline
                                       hover:text-amber-700 dark:hover:text-amber-200
                                       line-clamp-2 text-center"
                            >
                                {{ truncateText(article.title) }}
                            </a>

                            <!-- Views / Likes -->
                            <div
                                class="flex items-center justify-between text-[11px]
                                       font-semibold text-slate-600 dark:text-slate-200"
                            >
                                <span
                                    v-if="(article.views ?? 0) > 0"
                                    class="flex flex-row items-center"
                                    :title="t('views')"
                                >
                                    <svg class="w-3 h-3 fill-current shrink-0" viewBox="0 0 16 16">
                                        <path class="fill-current text-blue-600 dark:text-blue-300"
                                              d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z">
                                        </path>
                                    </svg>

                                    <span class="ml-1">{{ article.views }}</span>
                                </span>
                                <span
                                    v-if="(article.likes_count ?? 0) > 0"
                                    :title="t('likes')"
                                >
                                    ❤ {{ article.likes_count }}
                                </span>
                            </div>

                            <!-- Краткое описание -->
                            <div class="font-semibold text-[12px] text-center
                                        text-teal-700 dark:text-teal-200">
                                {{ truncateText(article.short) }}
                            </div>

                            <!-- Рубрики -->
                            <div class="flex flex-col justify-center">
                                <div class="text-center text-[11px] font-medium
                                            text-slate-600 dark:text-slate-300 mb-0.5">
                                    {{ t('rubrics') }}:
                                </div>
                                <div
                                    v-if="article.rubrics && article.rubrics.length"
                                    class="flex justify-center flex-wrap gap-1"
                                >
                                <span
                                    v-for="rubric in article.rubrics"
                                    :key="rubric.id"
                                    :title="rubric.title"
                                    class="py-0.5 px-1.5 badge
                                           bg-indigo-500 dark:bg-indigo-200
                                           rounded-sm text-[10px]
                                           text-slate-100 dark:text-slate-900"
                                >
                                    {{ rubric.id }}
                                </span>
                                </div>
                            </div>

                            <!-- Модерация -->
                            <div class="flex justify-center space-x-1">
                                <span
                                  class="text-[10px] px-2 py-1 rounded-sm border font-semibold"
                                  :class="moderationBadge(article.moderation_status).class"
                                  :title="article.moderation_note && article.moderated_at
                                ? `${article.moderation_note} [${formatDate(article.moderated_at)}]`
                                : null"
                                >
                                 {{ moderationBadge(article.moderation_status).text }}
                                </span>
                                <!-- Модерация (только админ) -->
                                <ModerationButton
                                    :isAdmin="isAdmin"
                                    :status="article?.moderation_status ?? 0"
                                    :initialNote="article?.moderation_note || ''"
                                    mode="toggle"
                                    @submit="({ status, note }) =>
                                    $emit('approve', article, status, note)"
                                />
                            </div>

                        </div>

                        <!-- Тогглы и действия -->
                        <div
                            class="flex items-center justify-between px-3 py-2
                                   border-t border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-1">
                                <LeftToggle
                                    :isActive="article.left"
                                    @toggle-left="$emit('toggle-left', article)"
                                    :title="article.left ? t('enabled') : t('disabled')"
                                />
                                <MainToggle
                                    :isActive="article.main"
                                    @toggle-main="$emit('toggle-main', article)"
                                    :title="article.main ? t('enabled') : t('disabled')"
                                />
                                <RightToggle
                                    :isActive="article.right"
                                    @toggle-right="$emit('toggle-right', article)"
                                    :title="article.right ? t('enabled') : t('disabled')"
                                />
                            </div>

                            <div class="flex items-center space-x-1">
                                <ActivityToggle
                                    :isActive="article.activity"
                                    @toggle-activity="$emit('toggle-activity', article)"
                                    :title="article.activity ? t('enabled') : t('disabled')"
                                />
                                <CloneIconButton @clone="$emit('clone', article)" />
                                <IconEdit :href="route('admin.articles.edit', article.id)" />
                                <DeleteIconButton @delete="$emit('delete', article.id)" />
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
