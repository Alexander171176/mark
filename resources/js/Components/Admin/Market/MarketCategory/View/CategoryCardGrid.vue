<script setup>
import { computed, defineEmits, defineProps, ref } from 'vue'
import { useI18n } from 'vue-i18n'

import ModerationButton from '@/Components/Admin/UI/Buttons/ModerationButton.vue'
import ActivityToggle from '@/Components/Admin/UI/Buttons/ActivityToggle.vue'
import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'
import MenuToggle from '@/Components/Admin/UI/Buttons/MenuToggle.vue'

/** Сервисы компонента */
const { t, locale } = useI18n()

/** Входные параметры */
const props = defineProps({
    categories: { type: Array, default: () => [] },
    selectedCategories: { type: Array, default: () => [] },
    isAdmin: { type: Boolean, default: false },
})

/** События компонента */
const emit = defineEmits([
    'toggle-activity',
    'delete',
    'toggle-select',
    'toggle-all',
    'approve',
    'toggle-menu',
])

/** Открытые блоки владельцев */
const openedOwnerBlocks = ref([])

/** Проверка открытого блока владельца */
const isOwnerBlockOpen = (categoryId) => {
    return openedOwnerBlocks.value.includes(categoryId)
}

/** Переключение блока владельца */
const toggleOwnerBlock = (categoryId) => {
    if (isOwnerBlockOpen(categoryId)) {
        openedOwnerBlocks.value = openedOwnerBlocks.value.filter(
            (id) => id !== categoryId
        )

        return
    }

    openedOwnerBlocks.value.push(categoryId)
}

/** Текущий перевод категории */
const getTranslation = (category) => {
    return category?.translation || {}
}

/** Название категории */
const getTitle = (category) => {
    return getTranslation(category).title || `ID: ${category?.id}`
}

/** Краткое описание */
const getShort = (category) => {
    return getTranslation(category).short || ''
}

/** Полное описание */
const getDescription = (category) => {
    return getTranslation(category).description || ''
}

/** Название родительской категории */
const getParentTitle = (category) => {
    if (!category?.parent_id) {
        return t('rootCategory')
    }

    return category?.parent?.translation?.title || t('noData')
}

/** Обрезка текста */
const truncateText = (text, maxLength = 80) => {
    if (!text) {
        return ''
    }

    const value = String(text)

    return value.length > maxLength
        ? `${value.slice(0, maxLength).trimEnd()}…`
        : value
}

/** Выбор всех карточек */
const toggleAll = (event) => {
    emit('toggle-all', {
        ids: props.categories.map((category) => category.id),
        checked: event.target.checked,
    })
}

/** Основное изображение категории */
const getPrimaryImage = (category) => {
    const images = Array.isArray(category?.images)
        ? category.images
        : []

    if (!images.length) {
        return null
    }

    return [...images].sort((a, b) => {
        return Number(a?.order ?? 0) - Number(b?.order ?? 0)
    })[0]
}

/** URL основного изображения */
const getPrimaryImageUrl = (category) => {
    const image = getPrimaryImage(category)

    return image?.webp_url
        || image?.thumb_url
        || image?.url
        || ''
}

/** Безопасная SVG-иконка */
const getSafeIcon = (icon) => {
    if (typeof icon !== 'string') {
        return ''
    }

    const value = icon.trim()

    if (
        value.startsWith('<svg')
        && value.endsWith('</svg>')
    ) {
        return value
    }

    return ''
}

/** Названия статусов */
const statusLabelKeyMap = {
    draft: 'statusDraft',
    published: 'statusPublished',
    archived: 'statusArchived',
}

/** Локализованное название статуса */
const getStatusLabel = (status) => {
    return t(
        statusLabelKeyMap[status]
        || status
        || 'no'
    )
}

/** Locale для даты */
const dateLocale = computed(() => {
    const locales = {
        ru: 'ru-RU',
        en: 'en-US',
        kk: 'kk-KZ',
        kz: 'kk-KZ',
    }

    return locales[locale.value]
        || locale.value
        || 'ru-RU'
})

/** Форматирование даты */
const formatDate = (value) => {
    if (!value) {
        return ''
    }

    const date = new Date(value)

    if (Number.isNaN(date.getTime())) {
        return ''
    }

    return new Intl.DateTimeFormat(
        dateLocale.value,
        {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        }
    ).format(date)
}

/** Имя владельца */
const getOwnerName = (category) => {
    return category?.owner?.name || t('noData')
}

/** Email владельца */
const getOwnerEmail = (category) => {
    return category?.owner?.email || ''
}

/** Подсказка владельца */
const getOwnerTitle = (category) => {
    const owner = category?.owner

    if (!owner) {
        return t('noData')
    }

    const name = owner.name || ''
    const email = owner.email || ''

    return email
        ? `${name} — ${email}`.trim()
        : name || t('noData')
}

/** Аватар владельца */
const getOwnerAvatar = (category) => {
    return category?.owner?.profile_photo_url
        || '/storage/profile-photos/default-image.png'
}

/** Статус модерации */
const moderationBadge = (status) => {
    const value = Number(status ?? 0)

    if (value === 1) {
        return {
            text: t('statusSelectApproved'),
            class: 'bg-emerald-100 text-emerald-700 border-emerald-300 '
                + 'dark:bg-emerald-900/40 dark:text-emerald-300',
        }
    }

    if (value === 2) {
        return {
            text: t('statusSelectRejected'),
            class: 'bg-rose-100 text-rose-700 border-rose-300 '
                + 'dark:bg-rose-900/40 dark:text-rose-300',
        }
    }

    return {
        text: t('underModeration'),
        class: 'bg-amber-100 text-amber-800 border-amber-300 '
            + 'dark:bg-amber-900/40 dark:text-amber-300',
    }
}
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-400 dark:border-slate-500 relative"
    >
        <!-- Панель выбора -->
        <div
            class="flex items-center justify-between px-3 py-2
                   border-b border-slate-400 dark:border-slate-500"
        >
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('selected') }}: {{ selectedCategories.length }}
            </div>

            <label
                v-if="categories.length"
                class="flex items-center text-xs
                       text-slate-600 dark:text-slate-200
                       cursor-pointer"
            >
                <span>
                    {{ t('selectAll') }}
                </span>

                <input
                    type="checkbox"
                    class="mx-2"
                    @change="toggleAll"
                >
            </label>
        </div>

        <!-- Карточки -->
        <div
            v-if="categories.length"
            class="p-3"
        >
            <div
                class="grid gap-3
                       grid-cols-1 sm:grid-cols-2
                       lg:grid-cols-3 xl:grid-cols-4"
            >
                <div
                    v-for="category in categories"
                    :key="category.id"
                    class="relative flex flex-col h-full rounded-md
                           border border-slate-400 dark:border-slate-500
                           bg-slate-50/70 dark:bg-slate-800/80
                           shadow-sm hover:shadow-md
                           transition-shadow duration-150"
                >
                    <!-- Header -->
                    <header
                        class="flex items-center justify-between px-2 py-1
                               border-b border-dashed
                               border-slate-400 dark:border-slate-500"
                    >
                        <div class="flex items-center space-x-2">
                            <div
                                class="text-[10px] font-semibold
                                       px-1.5 py-0.5 rounded-sm
                                       border border-gray-400
                                       bg-slate-200 dark:bg-slate-700
                                       text-slate-800 dark:text-blue-100"
                                :title="`${t('sort')}: ${category.sort}`"
                            >
                                ID: {{ category.id }}
                            </div>

                            <button
                                type="button"
                                class="text-slate-400 hover:text-blue-600
                                       dark:hover:text-blue-300"
                                :title="isOwnerBlockOpen(category.id)
                                    ? t('hideOwner')
                                    : t('showOwner')"
                                @click.prevent="toggleOwnerBlock(category.id)"
                            >
                                <svg
                                    class="w-4 h-4 transition-transform duration-200"
                                    :class="{ 'rotate-180': isOwnerBlockOpen(category.id) }"
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
                                class="text-[10px] px-2 py-0.5
                                       rounded-sm border font-semibold"
                                :class="moderationBadge(category.moderation_status).class"
                                :title="category.moderation_note && category.moderated_at
                                    ? `${category.moderation_note} [${formatDate(category.moderated_at)}]`
                                    : null"
                            >
                                {{ moderationBadge(category.moderation_status).text }}
                            </span>

                            <input
                                type="checkbox"
                                :checked="selectedCategories.includes(category.id)"
                                @change="emit('toggle-select', category.id)"
                            >
                        </div>
                    </header>

                    <!-- Владелец -->
                    <div
                        v-show="isOwnerBlockOpen(category.id)"
                        class="flex flex-col items-center
                               justify-center text-center py-3"
                    >
                        <img
                            :src="getOwnerAvatar(category)"
                            :title="getOwnerTitle(category)"
                            :alt="t('author')"
                            class="h-12 w-12 rounded-full object-cover
                                   border border-slate-300
                                   dark:border-slate-600"
                        >

                        <div
                            class="mt-1 text-[11px] font-semibold
                                   text-slate-700 dark:text-slate-100
                                   leading-tight line-clamp-1"
                            :title="getOwnerName(category)"
                        >
                            {{ getOwnerName(category) }}
                        </div>

                        <div
                            v-if="getOwnerEmail(category)"
                            class="text-[10px]
                                   text-slate-500 dark:text-slate-300
                                   leading-tight line-clamp-1"
                            :title="getOwnerEmail(category)"
                        >
                            {{ getOwnerEmail(category) }}
                        </div>
                    </div>

                    <!-- Изображение -->
                    <div class="relative w-full bg-slate-200 dark:bg-slate-900">
                        <img
                            v-if="getPrimaryImageUrl(category)"
                            :src="getPrimaryImageUrl(category)"
                            :alt="getPrimaryImage(category)?.alt || t('defaultImageAlt')"
                            :title="getPrimaryImage(category)?.caption || t('image')"
                            class="h-32 w-full object-cover"
                        >

                        <img
                            v-else
                            src="/storage/market/market_category_images/default-image.png"
                            :alt="t('defaultImageTitle')"
                            :title="t('defaultImageTitle')"
                            class="h-32 w-full object-cover"
                        >
                    </div>

                    <!-- Основное содержимое -->
                    <div class="flex flex-col flex-1 px-3 py-2 space-y-2">
                        <!-- Название -->
                        <div class="flex items-center justify-center text-center">
                            <div
                                class="flex items-center justify-center
                                       space-x-2 max-w-full"
                            >
                                <div
                                    class="flex items-center
                                           justify-center shrink-0"
                                >
                                    <div
                                        v-if="getSafeIcon(category.icon)"
                                        v-html="getSafeIcon(category.icon)"
                                        class="w-6 h-6
                                               text-slate-700 dark:text-slate-100
                                               flex items-center justify-center"
                                    />

                                    <svg
                                        v-else
                                        class="w-4 h-4
                                               text-slate-500 dark:text-slate-300"
                                        fill="currentColor"
                                        viewBox="0 0 16 16"
                                    >
                                        <path
                                            d="M2 2a1 1 0 011-1h3.5L8 2.5 9.5 1H13a1 1 0 011 1v3.5L12.5 7 14 8.5V12a1 1 0 01-1 1H9.5L8 11.5 6.5 13H3a1 1 0 01-1-1V8.5L3.5 7 2 5.5V2z"
                                        />
                                    </svg>
                                </div>

                                <a
                                    :href="`/market/categories/${encodeURIComponent(category.url)}`"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-sm font-semibold
                                           text-sky-700 dark:text-sky-200
                                           hover:text-amber-700
                                           dark:hover:text-amber-200
                                           hover:underline line-clamp-2
                                           text-center"
                                    :title="getTitle(category)"
                                >
                                    {{ truncateText(getTitle(category)) }}
                                </a>
                            </div>
                        </div>

                        <!-- Краткое описание -->
                        <div
                            v-if="getShort(category)"
                            class="font-semibold text-[12px] text-center
                                   text-cyan-700 dark:text-cyan-300"
                            :title="getShort(category)"
                        >
                            {{ truncateText(getShort(category)) }}
                        </div>

                        <!-- Описание -->
                        <div
                            v-if="getDescription(category)"
                            class="text-[11px] text-center
                                   text-slate-600 dark:text-slate-300"
                            :title="getDescription(category)"
                        >
                            {{ truncateText(getDescription(category), 100) }}
                        </div>

                        <!-- Иерархия -->
                        <div class="flex flex-wrap justify-center gap-1">
                            <span
                                class="text-[10px] px-2 py-0.5 rounded-sm border
                                       border-slate-300 dark:border-slate-600
                                       bg-slate-100 dark:bg-slate-700
                                       text-slate-700 dark:text-slate-200"
                                :title="t('level')"
                            >
                                L{{ category.level ?? 1 }}
                            </span>

                            <span
                                class="text-[10px] px-2 py-0.5 rounded-sm border
                                       border-slate-300 dark:border-slate-600
                                       bg-slate-100 dark:bg-slate-700
                                       text-slate-700 dark:text-slate-200"
                                :title="t('parentCategory')"
                            >
                                {{ getParentTitle(category) }}
                            </span>

                            <span
                                class="text-[10px] px-2 py-0.5 rounded-sm border
                                       border-slate-300 dark:border-slate-600
                                       bg-slate-100 dark:bg-slate-700
                                       text-slate-700 dark:text-slate-200"
                                :title="t('children')"
                            >
                                {{ category.children_count ?? 0 }}
                            </span>
                        </div>

                        <!-- Просмотры -->
                        <div
                            v-if="(category.views ?? 0) > 0"
                            class="flex items-center
                                   justify-center space-x-1"
                        >
                            <svg
                                class="w-4 h-4 fill-current shrink-0"
                                viewBox="0 0 16 16"
                            >
                                <path
                                    class="fill-current
                                           text-blue-600 dark:text-blue-300"
                                    d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975C.253 8.715 3.248 14 8 14s7.747-5.285 7.872-5.51a1 1 0 0 0 0-.98C15.747 7.285 12.752 2 8 2zm0 10a4 4 0 110-8 4 4 0 010 8zm0-6a2 2 0 100 4 2 2 0 000-4z"
                                />
                            </svg>

                            <span
                                class="text-[10px]
                                       text-slate-700 dark:text-slate-200"
                                :title="t('views')"
                            >
                                {{ category.views ?? 0 }}
                            </span>
                        </div>

                        <!-- Статус -->
                        <div
                            class="grid grid-cols-1 gap-0.5 text-[11px]
                                   text-slate-600 dark:text-slate-300"
                        >
                            <div
                                class="font-semibold text-center
                                       text-fuchsia-700 dark:text-fuchsia-300"
                            >
                                <span>
                                    {{ t('status') }}:
                                </span>

                                {{ getStatusLabel(category.status) }}
                            </div>
                        </div>

                        <!-- Модерация -->
                        <div class="flex justify-center gap-1">
                            <span
                                class="text-[10px] px-2 py-1
                                       rounded-sm border font-semibold"
                                :class="moderationBadge(category.moderation_status).class"
                                :title="category.moderation_note && category.moderated_at
                                    ? `${category.moderation_note} [${formatDate(category.moderated_at)}]`
                                    : null"
                            >
                                {{ moderationBadge(category.moderation_status).text }}
                            </span>

                            <ModerationButton
                                :isAdmin="isAdmin"
                                :status="category.moderation_status ?? 0"
                                :initialNote="category.moderation_note || ''"
                                mode="toggle"
                                @submit="({ status, note }) =>
                                    emit('approve', category, status, note)"
                            />
                        </div>
                    </div>

                    <!-- Footer -->
                    <footer
                        class="flex items-center justify-center px-3 py-2
                               border-t border-dashed
                               border-slate-400 dark:border-slate-500"
                    >
                        <div class="flex items-center space-x-1">
                            <MenuToggle
                                :is-enabled="category.in_menu"
                                :title="category.in_menu
                                    ? t('showInMenu')
                                    : t('notShowInMenu')"
                                @toggle-menu="emit('toggle-menu', category)"
                            />

                            <ActivityToggle
                                :isActive="category.activity"
                                :title="category.activity
                                    ? t('enabled')
                                    : t('disabled')"
                                @toggle-activity="emit('toggle-activity', category)"
                            />

                            <IconEdit
                                :href="route('admin.marketCategories.edit', {
                                    marketCategory: category.id,
                                })"
                            />

                            <DeleteIconButton
                                @click.stop="emit('delete', category)"
                            />
                        </div>
                    </footer>
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
