<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import ModerationButton from '@/Components/Admin/Buttons/ModerationButton.vue'
import LeftToggle from '@/Components/Admin/Buttons/LeftToggle.vue'
import MainToggle from '@/Components/Admin/Buttons/MainToggle.vue'
import RightToggle from '@/Components/Admin/Buttons/RightToggle.vue'
import ActivityToggle from '@/Components/Admin/Buttons/ActivityToggle.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'
import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'

const { t } = useI18n()

/** Входящие пропсы */
const props = defineProps({
    banners: { type: Array, default: () => [], },
    selectedBanners: { type: Array, default: () => [], },
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
const localBanners = ref([])

watch(
    () => props.banners,
    (newVal) => {
        localBanners.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true },
)

/** draggable end */
const handleDragEnd = () => {
    const newOrderIds = localBanners.value.map(banner => banner.id)
    emits('update-sort-order', newOrderIds)
}

/** Массовые чекбоксы */
const toggleAll = (event) => {
    const checked = event.target.checked
    const ids = localBanners.value.map(r => r.id)
    emits('toggle-all', { ids, checked })
}

/** Основное изображение (минимальный order) */
const getPrimaryImage = (banner) => {
    if (banner.images && banner.images.length) {
        return [...banner.images].sort((a, b) => a.order - b.order)[0]
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

/** имя и email автора */
const ownerName = (banner) => banner?.owner?.name || t('noData')
const ownerEmail = (banner) => banner?.owner?.email || ''

/** title автора */
const ownerTitle = (banner) => {
    const o = banner?.owner
    if (!o) return t('noData')
    return `${o.name || ''}${o.email ? ' — ' + o.email : ''}`.trim()
}

/** путь изображения автора */
const ownerAvatar = (banner) => {
    return banner?.owner?.profile_photo_url || '/storage/profile-photos/default-image.png'
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
                {{ t('selected') }}: {{ selectedBanners.length }}
            </div>
            <div class="flex items-center space-x-2">
                <label
                    class="flex items-center
                           text-xs text-slate-600 dark:text-slate-200 cursor-pointer"
                >
                    <span>{{ t('selectAll') }}</span>
                    <input type="checkbox" class="mx-2" @change="toggleAll" />
                </label>
            </div>
        </div>

        <!-- Сетка карточек + DnD -->
        <div v-if="localBanners.length" class="p-3">
            <draggable
                v-model="localBanners"
                tag="div"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4"
                item-key="id"
                @end="handleDragEnd"
                handle=".handle"
            >
                <template #item="{ element: banner }">
                    <div
                        class="relative flex flex-col h-full rounded-md
                               border border-slate-400 dark:border-slate-500
                               bg-slate-50/70 dark:bg-slate-800/80 shadow-sm
                               hover:shadow-md transition-shadow duration-150"
                    >
                        <!-- Верхняя панель карточки: drag handle + ID + чекбокс -->
                        <header
                            class="flex items-center justify-between px-2 py-1
                                   border-b border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-2">
                                <!-- drag handle -->
                                <button
                                    type="button"
                                    class="handle cursor-move text-slate-500 dark:text-slate-300
                                           hover:text-slate-800 dark:hover:text-slate-100"
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
                                    :title="`[${banner.locale}] : [${banner.sort}]`"
                                >
                                    ID: {{ banner.id }}
                                </div>

                                <!-- Locale -->
                                <div
                                    class="text-[10px] px-1.5 py-0.5 rounded-sm
                                           border border-gray-400 uppercase"
                                    :class="banner.activity
                          ? 'bg-blue-500 text-white'
                          : 'bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100'"
                                    :title="t('localization')"
                                >
                                    {{ banner.locale }}
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                <input
                                    type="checkbox"
                                    :checked="selectedBanners.includes(banner.id)"
                                    @change="$emit('toggle-select', banner.id)"
                                />
                            </div>
                        </header>

                        <!-- Изображение -->
                        <div class="relative w-full bg-slate-200 dark:bg-slate-900">
                            <template v-if="banner.images && banner.images.length">
                                <img
                                    :src="getPrimaryImage(banner).webp_url || getPrimaryImage(banner).url"
                                    :alt="getPrimaryImage(banner).alt || t('defaultImageAlt')"
                                    :title="getPrimaryImage(banner).caption || t('postImage')"
                                    class="h-32 w-full object-cover"
                                >
                            </template>
                            <template v-else>
                                <img
                                    src="/storage/banner_images/default-image.png"
                                    :alt="t('defaultImageTitle')"
                                    class="h-32 w-full object-cover"
                                >
                            </template>
                        </div>

                        <!-- Контент карточки -->
                        <div class="flex flex-col flex-1 px-3 py-2 space-y-2">
                            <!-- Заголовок -->
                            <div class="text-center">
                                <div
                                    class="text-[14px] font-semibold
                                           text-sky-700 dark:text-sky-200
                                           line-clamp-2"
                                    :title="banner.title"
                                >
                                    {{ banner.title }}
                                </div>
                            </div>

                            <!-- Ссылка -->
                            <div class="text-center">
                                <a
                                    :href="banner.link"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="font-semibold text-xs
                                           text-violet-600 dark:text-violet-200
                                           hover:text-violet-800 dark:hover:text-violet-50
                                           hover:underline break-all"
                                >
                                    {{ banner.link }}
                                </a>
                            </div>

                            <!-- Автор -->
                            <div class="flex flex-col items-center justify-center text-center">
                                <img
                                    :src="ownerAvatar(banner)"
                                    :title="ownerTitle(banner)"
                                    class="h-12 w-12 rounded-full object-cover
                                           border border-slate-300 dark:border-slate-600"
                                    alt="author"
                                />
                                <div class="mt-1 text-[11px] font-semibold
                                            text-slate-700 dark:text-slate-100
                                            leading-tight line-clamp-1"
                                     :title="ownerName(banner)">
                                    {{ ownerName(banner) }}
                                </div>
                                <div v-if="ownerEmail(banner)"
                                     class="text-[10px] text-slate-500 dark:text-slate-300
                                            leading-tight line-clamp-1"
                                     :title="ownerEmail(banner)">
                                    {{ ownerEmail(banner) }}
                                </div>
                            </div>

                            <!-- Модерация -->
                            <div class="flex justify-center space-x-1">
                                <span
                                    class="text-[10px] px-2 py-1 rounded-sm border font-semibold"
                                    :class="moderationBadge(banner.moderation_status).class"
                                    :title="banner.moderation_note && banner.moderated_at
                                ? `${banner.moderation_note} [${formatDate(banner.moderated_at)}]`
                                : null"
                                >
                                 {{ moderationBadge(banner.moderation_status).text }}
                                </span>
                                <!-- Модерация (только админ) -->
                                <ModerationButton
                                    :isAdmin="isAdmin"
                                    :status="banner?.moderation_status ?? 0"
                                    :initialNote="banner?.moderation_note || ''"
                                    mode="toggle"
                                    @submit="({ status, note }) =>
                                    $emit('approve', banner, status, note)"
                                />
                            </div>
                        </div>

                        <!-- Нижняя панель: тумблеры + кнопки -->
                        <div
                            class="px-3 py-2 border-t border-dashed
                                   border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center justify-between space-x-2">
                                <!-- Левый/правый -->
                                <div class="flex items-center space-x-1">
                                    <LeftToggle
                                        :isActive="banner.left"
                                        @toggle-left="$emit('toggle-left', banner)"
                                        :title="banner.left ? t('enabled') : t('disabled')"
                                    />
                                    <MainToggle
                                        :isActive="banner.main"
                                        @toggle-main="$emit('toggle-main', banner)"
                                        :title="banner.main ? t('enabled') : t('disabled')"
                                    />
                                    <RightToggle
                                        :isActive="banner.right"
                                        @toggle-right="$emit('toggle-right', banner)"
                                        :title="banner.right ? t('enabled') : t('disabled')"
                                    />
                                </div>

                                <!-- Активность + редактирование + удаление -->
                                <div class="flex items-center space-x-1">
                                    <ActivityToggle
                                        :isActive="banner.activity"
                                        @toggle-activity="$emit('toggle-activity', banner)"
                                        :title="banner.activity ? t('enabled') : t('disabled')"
                                    />
                                    <IconEdit :href="route('admin.banners.edit', banner.id)" />
                                    <DeleteIconButton @delete="$emit('delete', banner.id)" />
                                </div>
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
