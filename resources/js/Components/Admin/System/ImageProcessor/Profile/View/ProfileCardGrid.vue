<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import ActivityToggle from '@/Components/Admin/UI/Buttons/ActivityToggle.vue'
import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'

const { t } = useI18n()

const props = defineProps({
    profiles: {
        type: Array,
        default: () => [],
    },
    selectedProfiles: {
        type: Array,
        default: () => [],
    },
})

const emits = defineEmits([
    'toggle-activity',
    'delete',
    'update-sort-order',
    'toggle-select',
    'toggle-all',
])

/** Локальная копия профилей для Drag and Drop */
const localProfiles = ref([])

watch(
    () => props.profiles,
    (newVal) => {
        localProfiles.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/** Завершение сортировки */
const handleDragEnd = () => {
    const newOrderIds = localProfiles.value.map(profile => profile.id)

    emits('update-sort-order', newOrderIds)
}

/** Выбор всех карточек */
const toggleAll = (event) => {
    const checked = event.target.checked
    const ids = localProfiles.value.map(profile => profile.id)

    emits('toggle-all', { ids, checked })
}

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
                {{ t('selected') }}: {{ selectedProfiles.length }}
            </div>

            <label
                v-if="localProfiles.length"
                class="flex items-center text-xs text-slate-600
                       dark:text-slate-200 cursor-pointer"
            >
                <span>{{ t('selectAll') }}</span>
                <input type="checkbox" class="mx-2" @change="toggleAll" />
            </label>
        </div>

        <div v-if="localProfiles.length" class="p-3">
            <draggable
                tag="div"
                v-model="localProfiles"
                item-key="id"
                handle=".handle"
                @end="handleDragEnd"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
            >
                <template #item="{ element: profile }">
                    <article
                        class="relative flex flex-col h-full rounded-md
                               border border-slate-400 dark:border-slate-500
                               bg-slate-50/80 dark:bg-slate-800/90 shadow-sm
                               hover:shadow-md transition-shadow duration-150"
                    >
                        <header
                            class="flex items-center justify-between px-2 py-1
                                   border-b border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-2">
                                <button
                                    type="button"
                                    class="handle text-slate-400 hover:text-slate-700
                                           dark:hover:text-slate-100 cursor-move"
                                    :title="t('dragDrop')"
                                >
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"
                                        />
                                    </svg>
                                </button>

                                <span
                                    class="text-[11px] font-semibold px-1.5 py-0.5 rounded-sm
                                           border border-gray-400 bg-slate-200 dark:bg-slate-700
                                           text-slate-800 dark:text-blue-100"
                                    :title="`[${profile.sort}] / ${formatDate(profile.updated_at)}`"
                                >
                                    ID: {{ profile.id }}
                                </span>
                            </div>

                            <div class="flex items-center space-x-2">
                                <div
                                    class="text-[10px] px-2 py-0.5 rounded-sm bg-blue-500
                                       text-slate-50 font-semibold"
                                    :title="profile.description"
                                >
                                    {{ profile.variants_count ?? 0 }}
                                </div>
                                <input
                                    type="checkbox"
                                    :checked="selectedProfiles.includes(profile.id)"
                                    @change="$emit('toggle-select', profile.id)"
                                />
                            </div>
                        </header>

                        <div class="flex-1 px-3 py-3 space-y-2 text-center">
                            <div
                                class="text-[13px] font-semibold text-orange-500
                                       dark:text-orange-200 line-clamp-2"
                                :title="profile.key"
                            >
                                {{ profile.key }}
                            </div>

                            <div
                                class="text-[14px] font-semibold text-slate-700
                                       dark:text-slate-100 line-clamp-2"
                                :title="profile.name"
                            >
                                {{ profile.name }}
                            </div>

                            <div
                                v-if="profile.description"
                                class="text-[11px] text-slate-500 dark:text-slate-300 line-clamp-3"
                                :title="profile.description"
                            >
                                {{ profile.description }}
                            </div>
                        </div>

                        <footer
                            class="px-3 py-2 border-t border-dashed
                                   border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center justify-center gap-1">
                                <ActivityToggle
                                    :isActive="profile.activity"
                                    @toggle-activity="$emit('toggle-activity', profile)"
                                    :title="profile.activity ? t('enabled') : t('disabled')"
                                />

                                <IconEdit
                                    :href="route('admin.imageProcessorProfiles.edit', profile.id)"
                                />

                                <DeleteIconButton
                                    @delete="$emit('delete', profile.id)"
                                />
                            </div>
                        </footer>
                    </article>
                </template>
            </draggable>
        </div>

        <div v-else class="p-5 text-center text-slate-700 dark:text-slate-100">
            {{ t('noData') }}
        </div>
    </div>
</template>
