<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import ActivityToggle from '@/Components/Admin/Buttons/ActivityToggle.vue'
import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'

const { t } = useI18n()

const props = defineProps({
    learningCategories: { type: Array, default: () => [] },
    selectedLearningCategories: { type: Array, default: () => [] },
})

const emits = defineEmits([
    'toggle-activity',
    'delete',
    'update-sort-order',
    'toggle-select',
    'toggle-all',
])

const localLearningCategories = ref([])

watch(
    () => props.learningCategories,
    (newVal) => {
        localLearningCategories.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

const handleDragEnd = () => {
    const newOrderIds = localLearningCategories.value.map(item => item.id)
    emits('update-sort-order', newOrderIds)
}

const toggleAll = (event) => {
    const checked = event.target.checked
    const ids = localLearningCategories.value.map(item => item.id)
    emits('toggle-all', { ids, checked })
}

const getPrimaryImage = (learningCategory) => {
    if (learningCategory?.images && learningCategory.images.length) {
        return [...learningCategory.images].sort((a, b) => {
            const aOrder = Number(a?.order ?? a?.pivot?.order ?? 0)
            const bOrder = Number(b?.order ?? b?.pivot?.order ?? 0)
            return aOrder - bOrder
        })[0]
    }

    return null
}

const truncateText = (text, maxLength = 80) => {
    if (!text) return ''
    return text.length > maxLength
        ? text.slice(0, maxLength).trimEnd() + '…'
        : text
}

const parentName = (learningCategory) => {
    return learningCategory?.parent?.name || t('noData')
}

const parentTitle = (learningCategory) => {
    const parent = learningCategory?.parent
    if (!parent) return t('noData')
    return `${parent.name || ''}${parent.slug ? ' — ' + parent.slug : ''}`.trim()
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
                {{ t('selected') }}: {{ selectedLearningCategories.length }}
            </div>

            <label
                v-if="localLearningCategories.length"
                class="flex items-center text-xs
                       text-slate-600 dark:text-slate-200 cursor-pointer"
            >
                <span>{{ t('selectAll') }}</span>
                <input type="checkbox" class="mx-2" @change="toggleAll" />
            </label>
        </div>

        <div v-if="localLearningCategories.length" class="p-3">
            <draggable
                tag="div"
                v-model="localLearningCategories"
                item-key="id"
                @end="handleDragEnd"
                handle=".drag-handle"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4"
            >
                <template #item="{ element: learningCategory }">
                    <div
                        class="relative flex flex-col h-full rounded-md
                               border border-slate-400 dark:border-slate-500
                               bg-slate-50/70 dark:bg-slate-800/80
                               shadow-sm hover:shadow-md transition-shadow duration-150"
                    >
                        <!-- header -->
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
                                            d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"
                                        />
                                    </svg>
                                </button>

                                <div
                                    class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm
                                           border border-gray-400 bg-slate-200 dark:bg-slate-700
                                           text-slate-800 dark:text-blue-100"
                                    :title="`[${learningCategory.locale}] : [${learningCategory.sort}]`"
                                >
                                    ID: {{ learningCategory.id }}
                                </div>

                                <div
                                    class="text-[10px] px-1.5 py-0.5 rounded-sm
                                           border border-gray-400 uppercase"
                                    :class="learningCategory.activity
                                        ? 'bg-blue-500 text-white'
                                        : 'bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100'"
                                    :title="t('localization')"
                                >
                                    {{ learningCategory.locale }}
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                <div
                                    v-if="(learningCategory.views ?? 0) > 0"
                                    class="flex items-center space-x-1"
                                >
                                    <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16">
                                        <path
                                            class="fill-current text-blue-600 dark:text-blue-300"
                                            d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"
                                        />
                                    </svg>
                                    <span
                                        class="text-[10px] text-slate-700 dark:text-slate-200"
                                        :title="t('views')"
                                    >
                                        {{ learningCategory.views ?? 0 }}
                                    </span>
                                </div>

                                <input
                                    type="checkbox"
                                    :checked="selectedLearningCategories.includes(learningCategory.id)"
                                    @change="$emit('toggle-select', learningCategory.id)"
                                />
                            </div>
                        </header>

                        <!-- image -->
                        <div class="relative w-full bg-slate-200 dark:bg-slate-900">
                            <template v-if="learningCategory.images && learningCategory.images.length">
                                <img
                                    :src="getPrimaryImage(learningCategory)?.webp_url || getPrimaryImage(learningCategory)?.url"
                                    :alt="getPrimaryImage(learningCategory)?.alt || t('defaultImageAlt')"
                                    :title="getPrimaryImage(learningCategory)?.caption || t('postImage')"
                                    class="h-32 w-full object-cover"
                                />
                            </template>

                            <template v-else>
                                <img
                                    src="/storage/learning_category_images/default-image.png"
                                    :alt="t('defaultImageTitle')"
                                    class="h-32 w-full object-cover"
                                />
                            </template>
                        </div>

                        <!-- content -->
                        <div class="flex flex-col flex-1 px-3 py-2 space-y-2">
                            <div class="flex items-center justify-center text-center">
                                <div class="flex items-center justify-center max-w-full">
                                    <a
                                        :href="`/school/tracks/${encodeURIComponent(learningCategory.slug)}`"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="text-sm font-semibold
                                               text-sky-700 dark:text-sky-200
                                               hover:text-amber-700 dark:hover:text-amber-200
                                               hover:underline line-clamp-2 text-center"
                                        :title="learningCategory.name"
                                    >
                                        {{ truncateText(learningCategory.name) }}
                                    </a>
                                </div>
                            </div>

                            <div
                                v-if="learningCategory.parent"
                                class="flex items-center justify-center text-center"
                            >
                                <div
                                    class="flex flex-col text-[11px] font-semibold
                                           leading-tight line-clamp-1"
                                    :title="parentTitle(learningCategory)"
                                >
                                    <span class="text-gray-500 dark:text-gray-400">
                                        {{ t('parentCategory') }}:
                                    </span>
                                    <span class="text-slate-800 dark:text-slate-200">
                                        {{ parentName(learningCategory) }}
                                    </span>
                                </div>
                            </div>

                            <div
                                v-if="learningCategory.short"
                                class="font-semibold text-[12px] text-center
                                       text-teal-700 dark:text-teal-200"
                            >
                                {{ truncateText(learningCategory.short) }}
                            </div>

                            <div
                                class="flex items-center justify-center gap-3 text-[11px]
                                       text-slate-600 dark:text-slate-300"
                            >
                                <span
                                    class="px-2 py-1 rounded-sm
                                           border border-slate-500 dark:border-slate-400
                                           flex items-center justify-center gap-1"
                                    :title="t('subheadings')"
                                >
                                    <svg class="h-4 w-4 text-violet-700/85 dark:text-violet-300/85"
                                         fill="currentColor" viewBox="0 0 640 512">
                                        <path d="M622.34 153.2L343.4 67.5c-15.2-4.67-31.6-4.67-46.79 0L17.66 153.2c-23.54 7.23-23.54 38.36 0 45.59l48.63 14.94c-10.67 13.19-17.23 29.28-17.88 46.9C38.78 266.15 32 276.11 32 288c0 10.78 5.68 19.85 13.86 25.65L20.33 428.53C18.11 438.52 25.71 448 35.94 448h56.11c10.24 0 17.84-9.48 15.62-19.47L82.14 313.65C90.32 307.85 96 298.78 96 288c0-11.57-6.47-21.25-15.66-26.87.76-15.02 8.44-28.3 20.69-36.72L296.6 284.5c9.06 2.78 26.44 6.25 46.79 0l278.95-85.7c23.55-7.24 23.55-38.36 0-45.6zM352.79 315.09c-28.53 8.76-52.84 3.92-65.59 0l-145.02-44.55L128 384c0 35.35 85.96 64 192 64s192-28.65 192-64l-14.18-113.47-145.03 44.56z"></path>
                                    </svg>
                                    <span class="font-semibold">
                                        {{ learningCategory.children_count ?? 0 }}
                                    </span>
                                </span>

                                <span
                                    class="px-2 py-1 rounded-sm
                                           border border-slate-500 dark:border-slate-400
                                           flex items-center justify-center gap-1"
                                    :title="t('courses')"
                                >
                                    <svg class="h-4 w-4 text-sky-600/85 dark:text-sky-300/85"
                                         fill="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                                    </svg>
                                    <span class="font-semibold">
                                        {{ learningCategory.courses_count ?? 0 }}
                                    </span>
                                </span>
                            </div>
                        </div>

                        <!-- footer actions -->
                        <footer
                            class="flex items-center justify-center px-3 py-2
                                   border-t border-dashed
                                   border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center justify-center space-x-1">
                                <ActivityToggle
                                    :isActive="learningCategory.activity"
                                    @toggle-activity="$emit('toggle-activity', learningCategory)"
                                    :title="learningCategory.activity ? t('enabled') : t('disabled')"
                                />

                                <IconEdit
                                    :href="route('admin.learningCategories.edit', {
                                        learningCategory: learningCategory.id
                                    })"
                                />

                                <DeleteIconButton
                                    @click.stop="$emit('delete', learningCategory)"
                                />
                            </div>
                        </footer>
                    </div>
                </template>
            </draggable>
        </div>

        <div
            v-else
            class="p-4 text-center text-slate-500 dark:text-slate-400"
        >
            {{ t('noData') }}
        </div>
    </div>
</template>
