<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'


import ModerationButton from '@/Components/Admin/Buttons/ModerationButton.vue'
import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'
import ActivityToggle from '@/Components/Admin/Buttons/ActivityToggle.vue'
import LeftToggle from '@/Components/Admin/Buttons/LeftToggle.vue'
import MainToggle from '@/Components/Admin/Buttons/MainToggle.vue'
import RightToggle from '@/Components/Admin/Buttons/RightToggle.vue'
import CloneIconButton from '@/Components/Admin/Buttons/CloneIconButton.vue'

const { t } = useI18n()

/** Входящие пропсы */
const props = defineProps({
    videos: { type: Array, default: () => [] },
    selectedVideos: { type: Array, default: () => [] },
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
    'approve'
])

/** Локальная копия для vuedraggable */
const localVideos = ref([])

watch(
    () => props.videos,
    (newVal) => {
        localVideos.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/** draggable end */
const handleDragEnd = () => {
    const newOrderIds = localVideos.value.map(video => video.id)
    emits('update-sort-order', newOrderIds)
}

/** Массовые чекбоксы */
const toggleAll = (event) => {
    const checked = event.target.checked
    const ids = localVideos.value.map(r => r.id)
    emits('toggle-all', { ids, checked })
}

/** Основное изображение (минимальный order) */
const getPrimaryImage = (video) => {
    if (video.images && video.images.length) {
        return [...video.images].sort((a, b) => a.order - b.order)[0]
    }
    return null
}

/** формат даты публикации */
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
const ownerName = (video) => video?.owner?.name || t('noData')
const ownerEmail = (video) => video?.owner?.email || ''

/** title автора */
const ownerTitle = (video) => {
    const o = video?.owner
    if (!o) return t('noData')
    return `${o.name || ''}${o.email ? ' — ' + o.email : ''}`.trim()
}

/** путь изображения автора */
const ownerAvatar = (video) => {
    return video?.owner?.profile_photo_url || '/storage/profile-photos/default-image.png'
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

/** бейдж приватности */
const privacyBadge = (isPrivate) => {
    if (isPrivate) {
        return {
            text: t('private'),
            class: 'bg-fuchsia-100 text-fuchsia-800 border-fuchsia-300 dark:bg-fuchsia-900/40 dark:text-fuchsia-200'
        }
    }

    return {
        text: t('public'),
        class: 'bg-sky-100 text-sky-800 border-sky-300 dark:bg-sky-900/40 dark:text-sky-200'
    }
}

/** получить источник для превью */
const preview = (video) => {
    const type = video?.source_type

    if (type === 'local') {
        return { type: 'video', src: video?.video_url || null }
    }

    if (type === 'youtube' || type === 'vimeo') {
        return { type: 'iframe', src: video?.embed_url || null }
    }

    if (type === 'code') {
        return { type: 'html', html: video?.embed_code || '' }
    }

    return { type: 'none' }
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
                {{ t('selected') }}: {{ selectedVideos.length }}
            </div>

            <div class="flex items-center space-x-2">
                <label
                    v-if="localVideos.length"
                    class="flex items-center
                           text-xs text-slate-600 dark:text-slate-200 cursor-pointer"
                >
                    <span>{{ t('selectAll') }}</span>
                    <input type="checkbox" class="mx-2" @change="toggleAll" />
                </label>
            </div>
        </div>

        <!-- Сетка карточек + DnD -->
        <div v-if="localVideos.length" class="p-3">
            <draggable
                v-model="localVideos"
                tag="div"
                item-key="id"
                @end="handleDragEnd"
                handle=".handle"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2"
            >
                <template #item="{ element: video }">
                    <div
                        class="relative flex flex-col h-full rounded-md
                               border border-slate-400 dark:border-slate-500
                               bg-slate-50/70 dark:bg-slate-800/80 shadow-sm
                               hover:shadow-md transition-shadow duration-150"
                    >
                        <!-- Верхняя панель: drag handle + ID/locale/source + чекбокс -->
                        <header
                            class="flex items-center justify-between px-2 py-1
                                   border-b border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-2">

                                <!-- drag handle -->
                                <button
                                    type="button"
                                    class="handle cursor-move text-slate-500
                                           dark:text-slate-300 hover:text-slate-800
                                           dark:hover:text-slate-100"
                                    :title="t('dragDrop')"
                                >
                                    <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
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
                                    :title="`[${video.locale}] : [${video.sort}]`"
                                >
                                    ID: {{ video.id }}
                                </div>
                                <!-- Locale -->
                                <div
                                    class="text-[10px] px-1.5 py-0.5 rounded-sm
                                           border border-gray-400 uppercase"
                                    :class="video.activity
                          ? 'bg-blue-500 text-white'
                          : 'bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100'"
                                    :title="t('localization')"
                                >
                                    {{ video.locale }}
                                </div>
                            </div>

                            <div class="flex flex-row items-start">
                                <span
                                    class="text-[10px] px-2 py-0.5 rounded-sm border font-semibold"
                                    :class="privacyBadge(video.is_private).class"
                                >
                                    {{ privacyBadge(video.is_private).text }}
                                </span>
                            </div>

                            <div class="flex items-center space-x-2">
                                <input
                                    type="checkbox"
                                    :checked="selectedVideos.includes(video.id)"
                                    @change="$emit('toggle-select', video.id)"
                                />
                            </div>
                        </header>

                        <!-- Видео -->
                        <div class="relative w-full bg-slate-200 dark:bg-slate-900">
                            <template v-if="preview(video).type === 'video' && preview(video).src">
                                <video
                                    class="h-32 w-full object-cover"
                                    :src="preview(video).src"
                                    controls
                                    preload="metadata"
                                />
                            </template>

                            <template v-else-if="preview(video).type === 'iframe' &&
                                        preview(video).src">
                                <iframe
                                    class="h-64 w-full"
                                    :src="preview(video).src"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write;
                                           encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen
                                    loading="lazy"
                                />
                            </template>

                            <template v-else-if="preview(video).type === 'html' &&
                                        preview(video).html">
                                <div
                                    class="h-64 w-full overflow-hidden bg-white dark:bg-slate-800"
                                    v-html="preview(video).html"
                                />
                            </template>

                            <template v-else>
                                <div class="h-64 w-full flex items-center justify-center
                                            text-xs font-semibold
                                            text-slate-700 dark:text-slate-200">
                                    {{ t('noData') }}
                                </div>
                            </template>
                        </div>

                        <!-- Контент карточки -->
                        <div class="flex flex-col flex-1 px-3 py-2 gap-3">
                            <!-- Заголовок -->
                            <div class="text-center">
                                <a
                                    :href="`/blog/videos/${encodeURIComponent(video.url)}`"
                                    :title="video.source_type"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-lg font-semibold text-center
                                           text-sky-700 dark:text-sky-200 hover:underline
                                           hover:text-amber-700 dark:hover:text-amber-200
                                           line-clamp-2"
                                >
                                    {{ video.title }}
                                </a>
                            </div>

                            <!-- Views / Likes -->
                            <div
                                class="flex items-center justify-between text-[11px]
                                       font-semibold text-slate-600 dark:text-slate-200"
                            >
                                <span
                                    v-if="(video.views ?? 0) > 0"
                                    class="flex flex-row items-center"
                                    :title="t('views')"
                                >
                                    <svg class="w-3 h-3 fill-current shrink-0" viewBox="0 0 16 16">
                                        <path class="fill-current text-blue-600 dark:text-blue-300"
                                              d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z">
                                        </path>
                                    </svg>

                                    <span class="ml-1">{{ video.views }}</span>
                                </span>
                                <span
                                    v-if="(video.likes_count ?? 0) > 0"
                                    :title="t('likes')"
                                >
                                    ❤ {{ video.likes_count }}
                                </span>
                            </div>

                            <!-- Краткое описание -->
                            <div class="font-semibold text-[14px]
                                        text-slate-700 dark:text-slate-200">
                                {{ truncateText(video.short) }}
                            </div>

                            <!-- Автор -->
                            <div class="flex flex-row items-center justify-center
                                        text-center gap-1">
                                <img
                                    :src="ownerAvatar(video)"
                                    :title="ownerTitle(video)"
                                    class="h-6 w-6 rounded-full object-cover
                                           border border-slate-300 dark:border-slate-600"
                                    :alt="t('author')"
                                />
                                <div class="flex flex-col items-center justify-center">
                                    <div class="mt-1 text-[11px] font-semibold
                                            text-slate-700 dark:text-slate-100
                                            leading-tight line-clamp-1"
                                         :title="ownerName(video)">
                                        {{ ownerName(video) }}
                                    </div>
                                    <div v-if="ownerEmail(video)"
                                         class="text-[10px] text-slate-500 dark:text-slate-300
                                            leading-tight line-clamp-1"
                                         :title="ownerEmail(video)">
                                        {{ ownerEmail(video) }}
                                    </div>
                                </div>
                            </div>

                            <!-- дата публикации -->
                            <div v-if="video.show_from_at"
                                 class="flex flex-col items-center justify-center font-semibold
                                        text-center text-[12px] text-slate-500 dark:text-slate-300
                                        border border-dashed border-gray-400 py-1">
                                {{ t('show') }}: {{ video.show_from_at }} / {{ video.show_to_at }}
                            </div>
                            <div v-else
                                 class="flex flex-col items-center justify-center font-semibold
                                        text-center text-[12px] text-slate-500 dark:text-slate-300
                                        border border-dashed border-gray-400 py-1">
                                {{ formatDate(video.published_at) }}
                            </div>

                            <!-- Модерация -->
                            <div class="flex justify-center space-x-1">
                                <span
                                    class="text-[10px] px-2 py-1 rounded-sm border font-semibold"
                                    :class="moderationBadge(video.moderation_status).class"
                                    :title="video.moderation_note && video.moderated_at
                                    ? `${video.moderation_note} [${formatDate(video.moderated_at)}]`
                                    : null"
                                >
                                 {{ moderationBadge(video.moderation_status).text }}
                                </span>
                                <!-- Модерация (только админ) -->
                                <ModerationButton
                                    :isAdmin="isAdmin"
                                    :status="video?.moderation_status ?? 0"
                                    :initialNote="video?.moderation_note || ''"
                                    mode="toggle"
                                    @submit="({ status, note }) =>
                                    $emit('approve', video, status, note)"
                                />
                            </div>
                        </div>

                        <!-- Нижняя панель: тумблеры + кнопки -->
                        <div
                            class="flex items-center justify-between px-3 py-2
                                   border-t border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-1">
                                <LeftToggle
                                    :isActive="video.left"
                                    @toggle-left="$emit('toggle-left', video)"
                                    :title="video.left ? t('enabled') : t('disabled')"
                                />
                                <MainToggle
                                    :isActive="video.main"
                                    @toggle-main="$emit('toggle-main', video)"
                                    :title="video.main ? t('enabled') : t('disabled')"
                                />
                                <RightToggle
                                    :isActive="video.right"
                                    @toggle-right="$emit('toggle-right', video)"
                                    :title="video.right ? t('enabled') : t('disabled')"
                                />
                            </div>

                            <div class="flex items-center space-x-1">
                                <ActivityToggle
                                    :isActive="video.activity"
                                    @toggle-activity="$emit('toggle-activity', video)"
                                    :title="video.activity ? t('enabled') : t('disabled')"
                                />
                                <CloneIconButton @clone="$emit('clone', video)" />
                                <IconEdit :href="route('admin.videos.edit', video.id)" />
                                <DeleteIconButton @delete="$emit('delete', video.id)" />
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
