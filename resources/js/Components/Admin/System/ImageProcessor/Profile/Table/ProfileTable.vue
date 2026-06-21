<script setup>
import { defineProps, defineEmits, watch, ref } from 'vue'
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

/** Выбор всех строк */
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

        <div class="overflow-x-auto">
            <table v-if="profiles.length" class="table-auto w-full text-sm">
                <thead
                    class="text-sm uppercase bg-slate-200 dark:bg-cyan-900
                           border border-solid border-gray-300 dark:border-gray-700"
                >
                <tr>
                    <th class="px-1 py-3 w-px">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-4 h-4 fill-current text-slate-800 dark:text-slate-200"
                             height="24" width="24" viewBox="0 0 24 24">
                            <path
                                d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"></path>
                            <path
                                d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"></path>
                        </svg>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap w-px">
                        <div class="font-medium text-center text-slate-800 dark:text-slate-200">
                            {{ t('id') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-medium text-left text-slate-800 dark:text-slate-200">
                            {{ t('setupKey') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-medium text-left text-slate-800 dark:text-slate-200">
                            {{ t('title') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-medium text-center text-slate-800 dark:text-slate-200">
                            {{ t('optionsOptions') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-medium text-center text-slate-800 dark:text-slate-200">
                            {{ t('actions') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 w-px">
                        <input type="checkbox" @change="toggleAll" />
                    </th>
                </tr>
                </thead>

                <draggable
                    tag="tbody"
                    v-model="localProfiles"
                    item-key="id"
                    handle=".handle"
                    @end="handleDragEnd"
                >
                    <template #item="{ element: profile }">
                        <tr
                            class="text-sm font-semibold border-b-2
                                   hover:bg-slate-100 dark:hover:bg-cyan-800"
                        >
                            <td class="px-2 py-1 text-center cursor-move handle">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-300"
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" />
                                </svg>
                            </td>
                            <td class="px-2 py-1 whitespace-nowrap">
                            <div class="text-center text-slate-800 dark:text-slate-200"
                                 :title="`[${profile.sort}] / ${formatDate(profile.updated_at)}`">
                                {{ profile.id }}
                            </div>
                            </td>
                            <td class="px-2 py-1 whitespace-nowrap">
                                <div
                                    class="text-left text-sm font-semibold
                                           text-orange-500 dark:text-orange-200"
                                    :title="profile.description"
                                >
                                    {{ profile.key }}
                                </div>
                            </td>
                            <td class="px-2 py-1 whitespace-nowrap">
                                <div class="text-left text-sm text-slate-700 dark:text-slate-100">
                                    {{ profile.name }}
                                </div>

                                <div
                                    v-if="profile.description"
                                    class="text-xs text-slate-500 dark:text-slate-300 max-w-xs truncate"
                                >
                                    {{ profile.description }}
                                </div>
                            </td>
                            <td class="px-2 py-1 whitespace-nowrap">
                                <div
                                    class="text-center py-0.5 px-2 badge bg-blue-500
                                           rounded-sm text-xs text-slate-100"
                                >
                                    {{ profile.variants_count ?? 0 }}
                                </div>
                            </td>
                            <td class="px-2 py-1 whitespace-nowrap">
                                <div class="flex justify-center space-x-2">
                                    <ActivityToggle
                                        :isActive="profile.activity"
                                        @toggle-activity="$emit('toggle-activity', profile)"
                                    />

                                    <IconEdit
                                        :href="route('admin.imageProcessorProfiles.edit', profile.id)"
                                    />

                                    <DeleteIconButton
                                        @delete="$emit('delete', profile.id)"
                                    />
                                </div>
                            </td>
                            <td class="px-2 py-1 text-center">
                                <input
                                    type="checkbox"
                                    :checked="selectedProfiles.includes(profile.id)"
                                    @change="$emit('toggle-select', profile.id)"
                                />
                            </td>
                        </tr>
                    </template>
                </draggable>
            </table>

            <div v-else class="p-5 text-center text-slate-700 dark:text-slate-100">
                {{ t('noData') }}
            </div>
        </div>
    </div>
</template>
