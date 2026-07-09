<script setup>
import { defineEmits, defineProps, ref } from 'vue'
import { useI18n } from 'vue-i18n'

import ActivityToggle from '@/Components/Admin/UI/Buttons/ActivityToggle.vue'
import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'

const { t } = useI18n()

const props = defineProps({
    pages: { type: Array, default: () => [] },
    selectedPages: { type: Array, default: () => [] },
    isAdmin: { type: Boolean, default: false },
})

const emit = defineEmits([
    'toggle-activity',
    'delete',
    'toggle-select',
    'toggle-all',
    'toggle-menu',
    'toggle-footer',
    'toggle-content',
    'toggle-seo',
])

const openedOwnerBlocks = ref([])

const isOwnerBlockOpen = (pageId) => {
    return openedOwnerBlocks.value.includes(pageId)
}

const toggleOwnerBlock = (pageId) => {
    if (isOwnerBlockOpen(pageId)) {
        openedOwnerBlocks.value = openedOwnerBlocks.value.filter(id => id !== pageId)
        return
    }

    openedOwnerBlocks.value.push(pageId)
}

const getTranslation = (page) => {
    return page?.translation || page?.translations?.[0] || {}
}

const getTitle = (page) => {
    return page?.title
        || getTranslation(page)?.title
        || `ID: ${page?.id}`
}

const getShort = (page) => {
    return page?.short
        || getTranslation(page)?.short
        || ''
}

const getDescription = (page) => {
    return page?.description
        || getTranslation(page)?.description
        || ''
}

const truncateText = (text, maxLength = 80) => {
    if (!text) return ''

    return text.length > maxLength
        ? text.slice(0, maxLength).trimEnd() + '…'
        : text
}

const toggleAll = (event) => {
    emit('toggle-all', {
        ids: props.pages.map((page) => page.id),
        checked: event.target.checked,
    })
}

const getSafeIcon = (icon) => {
    if (!icon) return null

    const trimmed = icon.trim()

    if (trimmed.startsWith('<svg') && trimmed.endsWith('</svg>')) {
        return trimmed
    }

    return null
}

const statusLabelKeyMap = {
    draft: 'statusDraft',
    published: 'statusPublished',
    archived: 'statusArchived',
}

const getStatusLabel = (status) => t(statusLabelKeyMap[status] || status || 'no')

const ownerName = (page) => page?.owner?.name || t('noData')
const ownerEmail = (page) => page?.owner?.email || ''

const ownerTitle = (page) => {
    const owner = page?.owner

    if (!owner) return t('noData')

    return `${owner.name || ''}${owner.email ? ' — ' + owner.email : ''}`.trim()
}

const ownerAvatar = (page) => {
    return page?.owner?.profile_photo_url
        || '/storage/profile-photos/default-image.png'
}

const parentTitle = (page) => {
    return page?.parent?.title
        || page?.parent?.translation?.title
        || page?.parent?.translations?.[0]?.title
        || t('noData')
}

const badgeClass = (enabled) => {
    return enabled
        ? 'bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300'
        : 'bg-slate-100 text-slate-600 border-slate-300 dark:bg-slate-700 dark:text-slate-300'
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
                {{ t('selected') }}: {{ selectedPages.length }}
            </div>

            <label
                v-if="pages.length"
                class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"
            >
                <span>{{ t('selectAll') }}</span>

                <input
                    type="checkbox"
                    class="mx-2"
                    @change="toggleAll"
                />
            </label>
        </div>

        <div v-if="pages.length" class="p-3">
            <div class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <div
                    v-for="page in pages"
                    :key="page.id"
                    class="relative flex flex-col h-full rounded-md
                           border border-slate-400 dark:border-slate-500
                           bg-slate-50/70 dark:bg-slate-800/80
                           shadow-sm hover:shadow-md transition-shadow duration-150"
                >
                    <header
                        class="flex items-center justify-between px-2 py-1
                               border-b border-dashed border-slate-400 dark:border-slate-500"
                    >
                        <div class="flex items-center space-x-2">
                            <div
                                class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm
                                       border border-gray-400 bg-slate-200 dark:bg-slate-700
                                       text-slate-800 dark:text-blue-100"
                                :title="`sort: ${page.sort}`"
                            >
                                ID: {{ page.id }}
                            </div>

                            <button
                                type="button"
                                class="text-slate-400 hover:text-blue-600 dark:hover:text-blue-300"
                                :title="isOwnerBlockOpen(page.id) ? t('hideOwner') : t('showOwner')"
                                @click.prevent="toggleOwnerBlock(page.id)"
                            >
                                <svg
                                    class="w-4 h-4 transition-transform duration-200"
                                    :class="{ 'rotate-180': isOwnerBlockOpen(page.id) }"
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

                        <input
                            type="checkbox"
                            :checked="selectedPages.includes(page.id)"
                            @change="emit('toggle-select', page.id)"
                        />
                    </header>

                    <div
                        v-show="isOwnerBlockOpen(page.id)"
                        class="flex flex-col items-center justify-center text-center py-3"
                    >
                        <img
                            :src="ownerAvatar(page)"
                            :title="ownerTitle(page)"
                            class="h-12 w-12 rounded-full object-cover
                                   border border-slate-300 dark:border-slate-600"
                            :alt="t('author')"
                        />

                        <div
                            class="mt-1 text-[11px] font-semibold
                                   text-slate-700 dark:text-slate-100
                                   leading-tight line-clamp-1"
                            :title="ownerName(page)"
                        >
                            {{ ownerName(page) }}
                        </div>

                        <div
                            v-if="ownerEmail(page)"
                            class="text-[10px] text-slate-500 dark:text-slate-300
                                   leading-tight line-clamp-1"
                            :title="ownerEmail(page)"
                        >
                            {{ ownerEmail(page) }}
                        </div>
                    </div>

                    <div class="flex flex-col flex-1 px-3 py-3 space-y-2">

                        <div class="flex flex-wrap justify-center gap-1 font-semibold">
                            <span
                                class="text-[10px] px-2 py-0.5 rounded-sm border
                                       border-slate-300 dark:border-slate-600
                                       bg-slate-100 dark:bg-slate-700
                                       text-slate-700 dark:text-slate-200"
                                :title="t('level')"
                            >
                                L{{ page.level ?? 1 }}
                            </span>

                            <span
                                class="text-[10px] px-2 py-0.5 rounded-sm border
                                       border-slate-300 dark:border-slate-600
                                       bg-slate-100 dark:bg-slate-700
                                       text-slate-700 dark:text-slate-200"
                                :title="t('parentPage')"
                            >
                                {{ page.parent_id ? parentTitle(page) : t('rootPage') }}
                            </span>

                            <span
                                class="text-[10px] px-2 py-0.5 rounded-sm border
                                       border-slate-300 dark:border-slate-600
                                       bg-slate-100 dark:bg-slate-700
                                       text-slate-700 dark:text-slate-200"
                                :title="t('children')"
                            >
                                {{ page.children_count ?? 0 }}
                            </span>
                        </div>

                        <div class="flex items-center justify-center text-center">
                            <div class="flex items-center justify-center space-x-2 max-w-full">
                                <div class="flex items-center justify-center shrink-0">
                                    <div
                                        v-if="getSafeIcon(page.icon)"
                                        v-html="getSafeIcon(page.icon)"
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
                                            d="M2 2a1 1 0 011-1h3.5L8 2.5 9.5 1H13a1 1 0 011 1v3.5L12.5 7 14 8.5V12a1 1 0 01-1 1H9.5L8 11.5 6.5 13H3a1 1 0 01-1-1V8.5L3.5 7 2 5.5V2z"
                                        />
                                    </svg>
                                </div>

                                <a
                                    :href="page.url || '#'"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-sm font-semibold
                                           text-blue-700 dark:text-blue-200
                                           hover:text-amber-700 dark:hover:text-amber-200
                                           hover:underline line-clamp-2 text-center"
                                    :title="getTitle(page)"
                                >
                                    {{ truncateText(getTitle(page)) }}
                                </a>
                            </div>
                        </div>

                        <div
                            class="flex justify-center font-semibold text-[10px]
                                   text-slate-600 dark:text-slate-200"
                            :title="t('url')"
                        >
                            {{ page.url || '#' }}
                        </div>

                        <div
                            class="font-semibold text-[12px] text-center
                                   text-cyan-700 dark:text-cyan-300"
                            :title="getShort(page)"
                        >
                            {{ truncateText(getShort(page)) }}
                        </div>

                        <div
                            v-if="getDescription(page)"
                            class="text-[11px] text-center text-slate-600 dark:text-slate-300"
                            :title="getDescription(page)"
                        >
                            {{ truncateText(getDescription(page), 100) }}
                        </div>

                        <div
                            v-if="(page.views ?? 0) > 0"
                            class="flex items-center justify-center space-x-1"
                        >
                            <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16">
                                <path
                                    class="fill-current text-blue-600 dark:text-blue-300"
                                    d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975C.253 8.715 3.248 14 8 14s7.747-5.285 7.872-5.51a1 1 0 0 0 0-.98C15.747 7.285 12.752 2 8 2zm0 10a4 4 0 110-8 4 4 0 010 8zm0-6a2 2 0 100 4 2 2 0 000-4z"
                                />
                            </svg>

                            <span
                                class="text-[10px] text-slate-700 dark:text-slate-200"
                                :title="t('views')"
                            >
                                {{ page.views ?? 0 }}
                            </span>
                        </div>

                        <div
                            class="grid grid-cols-1 gap-0.5 text-[11px]
                                   text-slate-600 dark:text-slate-300"
                        >
                            <div class="font-semibold text-center text-fuchsia-700 dark:text-fuchsia-300">
                                <span>{{ t('status') }}: </span>
                                {{ getStatusLabel(page.status) }}
                            </div>
                        </div>

                        <div class="flex flex-wrap justify-center gap-1">
                            <span class="text-[10px] px-2 py-1 rounded-sm border font-semibold"
                                  :class="badgeClass(page.in_menu)">
                                Menu
                            </span>

                            <span class="text-[10px] px-2 py-1 rounded-sm border font-semibold"
                                  :class="badgeClass(page.in_footer)">
                                Footer
                            </span>

                            <span class="text-[10px] px-2 py-1 rounded-sm border font-semibold"
                                  :class="badgeClass(page.show_content)">
                                HTML
                            </span>

                            <span class="text-[10px] px-2 py-1 rounded-sm border font-semibold"
                                  :class="badgeClass(page.show_seo)">
                                SEO
                            </span>
                        </div>
                    </div>

                    <footer
                        class="flex items-center justify-center px-3 py-2
                               border-t border-dashed border-slate-400 dark:border-slate-500"
                    >
                        <div class="flex items-center space-x-1">
                            <button
                                type="button"
                                class="text-[10px] px-2 py-1 rounded-sm border font-semibold"
                                :class="badgeClass(page.in_menu)"
                                :title="page.in_menu ? t('showInMenu') : t('notShowInMenu')"
                                @click.stop="emit('toggle-menu', page)"
                            >
                                M
                            </button>

                            <button
                                type="button"
                                class="text-[10px] px-2 py-1 rounded-sm border font-semibold"
                                :class="badgeClass(page.in_footer)"
                                title="Footer"
                                @click.stop="emit('toggle-footer', page)"
                            >
                                F
                            </button>

                            <button
                                type="button"
                                class="text-[10px] px-2 py-1 rounded-sm border font-semibold"
                                :class="badgeClass(page.show_content)"
                                title="HTML"
                                @click.stop="emit('toggle-content', page)"
                            >
                                H
                            </button>

                            <button
                                type="button"
                                class="text-[10px] px-2 py-1 rounded-sm border font-semibold"
                                :class="badgeClass(page.show_seo)"
                                title="SEO"
                                @click.stop="emit('toggle-seo', page)"
                            >
                                S
                            </button>

                            <ActivityToggle
                                :isActive="page.activity"
                                :title="page.activity ? t('enabled') : t('disabled')"
                                @toggle-activity="emit('toggle-activity', page)"
                            />

                            <IconEdit
                                :href="route('admin.cmsPages.edit', {
                                    cmsPage: page.id,
                                })"
                            />

                            <DeleteIconButton
                                @click.stop="emit('delete', page)"
                            />
                        </div>
                    </footer>
                </div>
            </div>
        </div>

        <div v-else class="p-5 text-center text-slate-700 dark:text-slate-100">
            {{ t('noData') }}
        </div>
    </div>
</template>
