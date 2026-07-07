<script setup>
import { defineOptions, defineProps, defineEmits, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import ActivityToggle from '@/Components/Admin/UI/Buttons/ActivityToggle.vue'
import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'
import MenuToggle from '@/Components/Admin/UI/Buttons/MenuToggle.vue'

const { t } = useI18n()

defineOptions({ name: 'PageTreeDraggable' })

const props = defineProps({
    page: { type: Object, required: true },
    level: { type: Number, default: 0 },
    selectedPages: { type: Array, default: () => [] },
    isAdmin: { type: Boolean, default: false },
})

const emit = defineEmits([
    'toggle-activity',
    'delete',
    'toggle-select',
    'request-drag-end',
    'toggle-menu',
    'toggle-footer',
    'toggle-content',
    'toggle-seo',
])

const isExpanded = ref(true)
const localChildren = ref([])

watch(
    () => props.page.children,
    (children) => {
        localChildren.value = Array.isArray(children) ? [...children] : []
    },
    { immediate: true, deep: true }
)

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

const toggleExpand = () => {
    isExpanded.value = !isExpanded.value
}

const handleInnerDragEnd = (event) => {
    emit('request-drag-end', event)
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

const ownerTitle = (page) => {
    const owner = page?.owner

    if (!owner) return t('noData')

    return `${owner.name || ''}${owner.email ? ' — ' + owner.email : ''}`.trim()
}

const badgeClass = (enabled) => {
    return enabled
        ? 'bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300'
        : 'bg-slate-100 text-slate-600 border-slate-300 dark:bg-slate-700 dark:text-slate-300'
}
</script>

<template>
    <div>
        <div
            class="cms-page-item mb-1"
            :style="{ marginLeft: level * 20 + 'px' }"
        >
            <div
                class="flex items-center justify-between py-1 px-2
                       border border-gray-400 rounded-sm
                       bg-white dark:bg-slate-600
                       hover:bg-slate-50 dark:hover:bg-slate-700
                       transition duration-150 ease-in-out"
            >
                <div class="flex items-center space-x-2 flex-grow min-w-0">
                    <span
                        class="drag-handle handle cursor-move mr-1 flex-shrink-0"
                        :title="t('dragDrop')"
                    >
                        <svg viewBox="0 0 512 512" class="w-4 h-4">
                            <path
                                class="fill-current text-sky-500 dark:text-sky-200"
                                d="M278.6 9.4c-12.5-12.5-32.8-12.5-45.3 0l-64 64c-9.2 9.2-11.9 22.9-6.9 34.9s16.6 19.8 29.6 19.8h32v96h-96v-32c0-12.9-7.8-24.6-19.8-29.6s-25.7-2.2-34.9 6.9l-64 64c-12.5 12.5-12.5 32.8 0 45.3l64 64c9.2 9.2 22.9 11.9 34.9 6.9s19.8-16.6 19.8-29.6v-32h96v96h-32c-12.9 0-24.6 7.8-29.6 19.8s-2.2 25.7 6.9 34.9l64 64c12.5 12.5 32.8 12.5 45.3 0l64-64c9.2-9.2 11.9-22.9 6.9-34.9s-16.6-19.8-29.6-19.8h-32v-96h96v32c0 12.9 7.8 24.6 19.8 29.6s25.7 2.2 34.9-6.9l64-64c12.5-12.5 12.5-32.8 0-45.3l-64-64c-9.2-9.2-22.9-11.9-34.9-6.9s-19.8 16.6-19.8 29.6v32h-96v-96h32c12.9 0 24.6-7.8 29.6-19.8s2.2-25.7-6.9-34.9l-64-64z"
                            />
                        </svg>
                    </span>

                    <button
                        v-if="page.children && page.children.length"
                        type="button"
                        :title="isExpanded ? t('collapse') : t('expand')"
                        @click="toggleExpand"
                        class="flex-shrink-0 text-slate-900 hover:text-red-500
                               dark:text-slate-100 dark:hover:text-red-200"
                    >
                        <svg
                            class="w-5 h-5 transform transition-transform duration-150"
                            :class="{ 'rotate-90': isExpanded }"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5l7 7-7 7"
                            />
                        </svg>
                    </button>

                    <span v-else class="w-5 h-5 inline-block flex-shrink-0" />

                    <div
                        class="w-8 font-semibold text-sm
                               text-amber-600 dark:text-amber-200 mr-1 flex-shrink-0"
:title="`[${t('sort')}: ${page.sort}] ${t('level')}: ${page.level} / ${getStatusLabel(page.status)}`"
                    >
                        {{ page.id }}
                    </div>

                    <img
                        v-if="page?.owner?.profile_photo_url"
                        :src="page.owner.profile_photo_url"
                        :title="ownerTitle(page)"
                        class="h-6 w-6 rounded-full object-cover
                               border border-slate-300 dark:border-slate-600"
                        :alt="t('author')"
                    />

                    <div
                        v-if="getSafeIcon(page.icon)"
                        v-html="getSafeIcon(page.icon)"
                        class="pl-3 w-6 h-6 text-slate-700 dark:text-slate-100
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

                    <a
                        :href="page.url || '#'"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-xs font-semibold
                               text-blue-700 dark:text-blue-200
                               hover:text-amber-700 dark:hover:text-amber-200
                               hover:underline line-clamp-2 text-center"
                        :title="getShort(page)"
                    >
                        {{ getTitle(page) }}
                    </a>

                    <span
                        class="shrink-0 text-[10px] px-1.5 py-0.5 rounded-sm
                               border border-slate-300 dark:border-slate-600
                               bg-slate-100 dark:bg-slate-700
                               text-slate-700 dark:text-slate-200"
                        :title="t('level')"
                    >
                        L{{ page.level ?? 1 }}
                    </span>

                    <span
                        class="shrink-0 text-[10px] px-1.5 py-0.5 rounded-sm
                               border border-slate-300 dark:border-slate-600
                               bg-slate-100 dark:bg-slate-700
                               text-slate-700 dark:text-slate-200"
                        :title="t('children')"
                    >
                        {{ page.children_count ?? page.children?.length ?? 0 }}
                    </span>

                    <span
                        class="shrink-0 text-[10px] px-1.5 py-0.5 rounded-sm border font-semibold"
                        :class="badgeClass(page.in_menu)"
                        :title="t('showInMenu')"
                    >
                        Menu
                    </span>

                    <span
                        class="shrink-0 text-[10px] px-1.5 py-0.5 rounded-sm border font-semibold"
                        :class="badgeClass(page.in_footer)"
                        :title="t('showInFooter')"
                    >
                        Footer
                    </span>

                    <span
                        class="shrink-0 text-[10px] px-1.5 py-0.5 rounded-sm border font-semibold"
                        :class="badgeClass(page.show_content)"
                        :title="t('showHtml')"
                    >
                        HTML
                    </span>

                    <span
                        class="shrink-0 text-[10px] px-1.5 py-0.5 rounded-sm border font-semibold"
                        :class="badgeClass(page.show_seo)"
                        :title="t('showSeo')"
                    >
                        SEO
                    </span>
                </div>

                <div class="flex items-center space-x-1 flex-shrink-0 ml-4">
                    <div class="flex items-center gap-1">
                        <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16">
                            <path
                                class="fill-current text-blue-600 dark:text-blue-300"
                                d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975C.253 8.715 3.248 14 8 14s7.747-5.285 7.872-5.51a1 1 0 0 0 0-.98C15.747 7.285 12.752 2 8 2zm0 10a4 4 0 110-8 4 4 0 010 8zm0-6a2 2 0 100 4 2 2 0 000-4z"
                            />
                        </svg>

                        <div
                            class="w-7 font-semibold text-[10px]
                                   text-slate-700 dark:text-slate-200"
                            :title="t('views')"
                        >
                            {{ page.views ?? 0 }}
                        </div>
                    </div>

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
                        class="text-[10px] px-2.5 py-1 rounded-sm border font-semibold"
                        :class="badgeClass(page.in_footer)"
                        :title="page.in_footer ? t('showInFooter') : t('notShowInFooter')"
                        @click.stop="emit('toggle-footer', page)"
                    >
                        F
                    </button>

                    <button
                        type="button"
                        class="text-[10px] px-2 py-1 rounded-sm border font-semibold"
                        :class="badgeClass(page.show_content)"
                        :title="page.show_content ? t('showHtml') : t('notShowHtml')"
                        @click.stop="emit('toggle-content', page)"
                    >
                        H
                    </button>

                    <button
                        type="button"
                        class="text-[10px] px-2 py-1 rounded-sm border font-semibold"
                        :class="badgeClass(page.show_seo)"
                        :title="page.show_seo ? t('showSeo') : t('notShowSeo')"
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

                    <DeleteIconButton @click.stop="emit('delete', page)" />

                    <div class="pl-1.5">
                        <input
                            type="checkbox"
                            :checked="selectedPages.includes(page.id)"
                            @change="emit('toggle-select', page.id)"
                            class="form-checkbox rounded-sm text-indigo-500 flex-shrink-0"
                        />
                    </div>
                </div>
            </div>
        </div>

        <div
            v-show="isExpanded && localChildren.length"
            class="children-container mt-1"
        >
            <draggable
                v-model="localChildren"
                tag="div"
                item-key="id"
                handle=".drag-handle"
                group="cms-pages"
                @end="handleInnerDragEnd"
                class="cms-page-tree-children"
                :data-parent-id="page.id"
            >
                <template #item="{ element: childPage }">
                    <PageTreeDraggable
                        :page="childPage"
                        :level="level + 1"
                        :selected-pages="selectedPages"
                        :is-admin="isAdmin"
                        @toggle-activity="(payload) => emit('toggle-activity', payload)"
                        @delete="(payload) => emit('delete', payload)"
                        @toggle-select="(id) => emit('toggle-select', id)"
                        @request-drag-end="handleInnerDragEnd"
                        @toggle-menu="(payload) => emit('toggle-menu', payload)"
                        @toggle-footer="(payload) => emit('toggle-footer', payload)"
                        @toggle-content="(payload) => emit('toggle-content', payload)"
                        @toggle-seo="(payload) => emit('toggle-seo', payload)"
                    />
                </template>
            </draggable>
        </div>
    </div>
</template>
