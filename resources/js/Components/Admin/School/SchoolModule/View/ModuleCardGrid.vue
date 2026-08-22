<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import ActivityToggle from '@/Components/Admin/UI/Buttons/ActivityToggle.vue'
import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'

const { t } = useI18n()

const props = defineProps({
    modules: {
        type: Array,
        default: () => [],
    },

    selectedModules: {
        type: Array,
        default: () => [],
    },
})

const emit = defineEmits([
    'toggle-activity',
    'delete',
    'update-sort-order',
    'toggle-select',
    'toggle-all',
])

const moduleStatusLabelKeyMap = {
    draft: 'statusDraft',
    published: 'statusPublished',
    archived: 'statusArchived',
}

const moduleAvailabilityLabelKeyMap = {
    unlisted: 'availabilityUnlisted',
    public: 'availabilityPublic',
    private: 'availabilityPrivate',
}

const getModuleStatusLabel = (status) => {
    return t(moduleStatusLabelKeyMap[status] || status || 'no')
}

const getModuleAvailabilityLabel = (availability) => {
    return t(moduleAvailabilityLabelKeyMap[availability] || availability || 'no')
}

const localModules = ref([])

watch(
    () => props.modules,
    (newVal) => {
        localModules.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

const handleDragEnd = () => {
    const newOrderIds = localModules.value.map(module => module.id)
    emit('update-sort-order', newOrderIds)
}

const toggleAll = (event) => {
    const checked = event.target.checked
    const ids = localModules.value.map(module => module.id)

    emit('toggle-all', { ids, checked })
}

const getPrimaryImage = (module) => {
    if (module?.primary_image) {
        return module.primary_image
    }

    if (Array.isArray(module?.images) && module.images.length) {
        return [...module.images]
            .sort((a, b) => Number(a?.order ?? 0) - Number(b?.order ?? 0))[0]
    }

    return null
}

const getModuleTitle = (module) => {
    return module?.translation?.title || `ID: ${module?.id}`
}

const getModuleSubtitle = (module) => {
    return module?.translation?.subtitle || ''
}

const getCourseTitle = (module) => {
    return module?.course?.translation?.title
        || `ID: ${module?.school_course_id || '-'}`
}

const getImageUrl = (image) => {
    return image?.thumb_url
        || image?.webp_url
        || image?.image_url
        || image?.url
        || ''
}

const formatDate = (dateStr) => {
    if (!dateStr) return ''

    const d = new Date(dateStr)

    if (isNaN(d)) return ''

    return d.toLocaleDateString('ru-RU', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    })
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
                {{ t('selected') }}: {{ selectedModules.length }}
            </div>

            <label
                v-if="localModules.length"
                class="flex items-center text-xs text-slate-600
                       dark:text-slate-200 cursor-pointer"
            >
                <span>{{ t('selectAll') }}</span>
                <input type="checkbox" class="mx-2" @change="toggleAll" />
            </label>
        </div>

        <div v-if="localModules.length" class="p-3">
            <draggable
                v-model="localModules"
                tag="div"
                item-key="id"
                handle=".drag-handle"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                @end="handleDragEnd"
            >
                <template #item="{ element: module }">
                    <div
                        class="relative flex flex-col h-full rounded-md
                               border border-slate-400 dark:border-slate-500
                               bg-slate-50/70 dark:bg-slate-800/80 shadow-sm
                               hover:shadow-md transition-shadow duration-150"
                    >
                        <div
                            class="flex items-center justify-between px-2 py-1
                                   border-b border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-2">
                                <button
                                    type="button"
                                    class="drag-handle text-slate-400 hover:text-slate-700
                                           dark:hover:text-slate-100"
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
                                           border border-gray-400
                                           bg-slate-200 dark:bg-slate-700
                                           text-slate-800 dark:text-blue-100"
                                    :title="`[sort: ${module.sort}] ${formatDate(module.published_at)}`"
                                >
                                    ID: {{ module.id }}
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                <span
                                    class="text-[10px] px-1.5 py-0.5 rounded-sm
                                           border border-gray-400
                                           bg-fuchsia-100 dark:bg-fuchsia-900/50
                                           text-fuchsia-700 dark:text-fuchsia-300"
                                    :title="t('availability')"
                                >
                                    {{ getModuleAvailabilityLabel(module.availability) }}
                                </span>

                                <input
                                    type="checkbox"
                                    :checked="selectedModules.includes(module.id)"
                                    @change="emit('toggle-select', module.id)"
                                />
                            </div>
                        </div>

                        <div class="relative w-full h-32 bg-slate-200 dark:bg-slate-900">
                            <template v-if="module.images && module.images.length">
                                <img
                                    :src="getImageUrl(getPrimaryImage(module))"
                                    :alt="getPrimaryImage(module)?.alt || t('defaultImageAlt')"
                                    :title="getPrimaryImage(module)?.caption || t('moduleImage')"
                                    class="w-full h-full object-cover"
                                />
                            </template>

                            <template v-else>
                                <img
                                    src="/storage/school/school_module_images/default-image.png"
                                    :alt="t('defaultImageTitle')"
                                    class="w-full h-full object-cover"
                                />
                            </template>
                        </div>

                        <div class="flex flex-col flex-1 px-3 py-2 space-y-1">
                            <a
                                :href="`/school/modules/${encodeURIComponent(module.slug)}`"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-sm font-semibold text-sky-700 dark:text-sky-200
                                       hover:underline line-clamp-2 text-center"
                                :title="getModuleSubtitle(module) || getModuleTitle(module)"
                            >
                                {{ getModuleTitle(module) }}
                            </a>

                            <div
                                class="text-[11px] font-semibold
                                       text-teal-600 dark:text-teal-200 text-center"
                                :title="`ID: ${module.school_course_id}`"
                            >
                                {{ t('course') }}: {{ getCourseTitle(module) }}
                            </div>

                            <div
                                class="text-[10px] text-center text-slate-500 dark:text-slate-300 truncate"
                                :title="module.slug"
                            >
                                {{ module.slug }}
                            </div>

                            <div
                                class="flex flex-wrap justify-center gap-1 mt-1
                                       text-[10px] font-semibold"
                            >
                                <span
                                    class="px-2 py-0.5 rounded-sm
                                           bg-sky-100 dark:bg-sky-900
                                           border border-gray-400
                                           text-sky-700 dark:text-sky-200"
                                    :title="t('status')"
                                >
                                    {{ getModuleStatusLabel(module.status) }}
                                </span>
                            </div>

                            <div
                                class="flex flex-col justify-center
                                       text-gray-700 dark:text-gray-400
                                       text-center text-[11px] mt-2"
                            >
                                <div>
                                    {{ t('duration') }}: {{ module.duration ?? '—' }}
                                </div>

                                <div>
                                    {{ t('lessons') }}: {{ module.lessons_count ?? 0 }}
                                </div>
                            </div>

                            <div
                                class="flex flex-wrap justify-center gap-3 mt-2
                                       text-[11px] text-slate-900 dark:text-slate-200"
                            >
                                <span>
                                    {{ t('views') }}: {{ module.views ?? 0 }}
                                </span>

                                <span>
                                    {{ t('likes') }}: {{ module.likes ?? 0 }}
                                </span>
                            </div>

                            <div
                                class="flex flex-col justify-center mt-2 text-center
                                       text-[11px] text-slate-900 dark:text-slate-200"
                            >
                                <span>
                                    {{ t('ratingCount') }}: {{ module.rating_count ?? 0 }}
                                </span>

                                <span>
                                    {{ t('ratingAvg') }}: {{ module.rating_avg ?? 0 }}
                                </span>
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-center px-3 py-2
                                   border-t border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-1">
                                <ActivityToggle
                                    :isActive="module.activity"
                                    :title="module.activity ? t('enabled') : t('disabled')"
                                    @toggle-activity="emit('toggle-activity', module)"
                                />

                                <IconEdit
                                    :href="route('admin.schoolModules.edit', { schoolModule: module.id })"
                                />

                                <DeleteIconButton
                                    @delete="emit('delete', module)"
                                />
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
