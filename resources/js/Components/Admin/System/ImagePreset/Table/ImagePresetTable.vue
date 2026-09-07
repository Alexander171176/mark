<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'

const { t } = useI18n()

const props = defineProps({
    presets: { type: Array, default: () => [] },
})

const emits = defineEmits(['update-sort-order'])

const localPresets = ref([])

watch(
    () => props.presets,
    newVal => localPresets.value = JSON.parse(JSON.stringify(newVal || [])),
    { immediate: true, deep: true }
)

const handleDragEnd = () => {
    emits('update-sort-order', localPresets.value.map(preset => preset.id))
}

const shapeLabel = (shape) => {
    const map = {
        rectangle: 'shapeRectangle',
        square: 'shapeSquare',
        circle: 'shapeCircle',
    }

    return map[shape] ? t(map[shape]) : shape
}

const shapeBadgeClass = (shape) => {
    const classes = {
        rectangle: 'bg-blue-600 dark:bg-blue-700 text-white',
        square: 'bg-purple-600 dark:bg-purple-700 text-white',
        circle: 'bg-pink-600 dark:bg-pink-700 text-white',
    }

    return classes[shape] || 'bg-gray-500 text-white'
}

const rotationBadgeClass = (type) => {
    const classes = {
        image: 'bg-sky-600 dark:bg-sky-700 text-white',
        crop: 'bg-cyan-600 dark:bg-cyan-700 text-white',
    }

    return classes[type] || 'bg-gray-500 text-white'
}

const originalBadgeClass = () => 'bg-slate-500 dark:bg-slate-900 text-white'
const booleanLabel = value => value ? t('yes') : t('no')

const aspectRatio = preset => {
    const width = Number(preset.width)
    const height = Number(preset.height)

    return width && height ? (width / height).toFixed(2) : null
}

const maxFileSizeMb = preset => {
    return (Number(preset.max_file_size_kb || 0) / 1024).toFixed(2)
}
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-200 dark:border-slate-600 relative"
    >
        <div class="overflow-x-auto">
            <table
                v-if="localPresets.length"
                class="table-auto w-full text-slate-700 dark:text-slate-100"
            >
                <thead
                    class="text-sm font-semibold uppercase bg-slate-200 dark:bg-cyan-900
                           border border-solid border-gray-300 dark:border-gray-700"
                >
                <tr>
                    <th class="px-2 py-3 w-px">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-4 h-4 fill-current text-slate-800 dark:text-slate-200"
                            viewBox="0 0 24 24"
                        >
                            <path d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z" />
                            <path d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z" />
                        </svg>
                    </th>

                    <th class="px-2 py-3 w-px text-center">{{ t('id') }}</th>
                    <th class="px-2 py-3 whitespace-nowrap text-left">{{ t('key') }}</th>
                    <th class="px-2 py-3 whitespace-nowrap text-center">{{ t('shape') }}</th>
                    <th class="px-2 py-3 whitespace-nowrap text-center">{{ t('typeSize') }}</th>
                    <th class="px-2 py-3 whitespace-nowrap text-center">{{ t('file') }}</th>
                    <th class="px-2 py-3 whitespace-nowrap text-center">{{ t('turn') }}</th>
                    <th class="px-2 py-3 whitespace-nowrap text-center">{{ t('originalShort') }}</th>
                    <th class="px-2 py-3 whitespace-nowrap text-end">{{ t('actions') }}</th>
                </tr>
                </thead>

                <draggable
                    tag="tbody"
                    v-model="localPresets"
                    item-key="id"
                    handle=".handle"
                    @end="handleDragEnd"
                >
                    <template #item="{ element: preset }">
                        <tr
                            class="text-sm font-semibold border-b-2
                                   hover:bg-slate-100 dark:hover:bg-cyan-800"
                        >
                            <td class="px-2 py-1 text-center cursor-move handle">
                                <svg
                                    class="w-4 h-4 text-gray-500 dark:text-gray-300"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" />
                                </svg>
                            </td>

                            <td class="px-2 py-3 whitespace-nowrap text-center">
                                <span
                                    class="text-xs text-slate-900 dark:text-slate-100"
                                    :title="`sort: ${preset.sort ?? '—'}`"
                                >
                                    {{ preset.id }}
                                </span>
                            </td>

                            <td class="px-2 py-3 whitespace-nowrap">
                                <span
                                    class="text-amber-600 dark:text-amber-200"
                                    :title="preset.description || '—'"
                                >
                                    {{ preset.key }}
                                </span>
                            </td>

                            <td class="px-2 py-3 whitespace-nowrap text-center">
                                <span
                                    class="block w-full py-0.5 px-2 rounded-sm text-xs font-semibold"
                                    :class="shapeBadgeClass(preset.shape)"
                                >
                                    {{ shapeLabel(preset.shape) }}
                                </span>
                            </td>

                            <td class="px-2 py-3 whitespace-nowrap text-center">
                                <div class="text-sky-700 dark:text-sky-300">
                                    {{ preset.resolution }}
                                </div>
                                <div
                                    v-if="aspectRatio(preset)"
                                    class="text-xs text-slate-500 dark:text-slate-300"
                                >
                                    {{ aspectRatio(preset) }}
                                </div>
                            </td>

                            <td class="px-2 py-3 whitespace-nowrap text-center">
                                <div class="text-indigo-700 dark:text-indigo-300">
                                    {{ maxFileSizeMb(preset) }} MB
                                </div>
                                <div class="text-xs text-slate-500 dark:text-slate-300">
                                    {{ preset.max_file_size_kb }} KB
                                </div>
                            </td>

                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="flex flex-col items-stretch gap-1">
                                    <span
                                        class="block w-full py-0.5 px-2 rounded-sm text-[10px]
                                               font-semibold text-center"
                                        :class="rotationBadgeClass('image')"
                                    >
                                        {{ t('photo') }}:
                                        {{ booleanLabel(preset.image_rotation_enabled) }}
                                    </span>

                                    <span
                                        class="block w-full py-0.5 px-2 rounded-sm text-[10px]
                                               font-semibold text-center"
                                        :class="rotationBadgeClass('crop')"
                                    >
                                        {{ t('photoFrames') }}:
                                        {{ booleanLabel(preset.crop_rotation_enabled) }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-2 py-3 whitespace-nowrap text-center">
                                <span
                                    class="block w-full py-0.5 px-2 rounded-sm text-[10px]
                                           font-semibold text-center"
                                    :class="originalBadgeClass()"
                                >
                                    {{ t('originalShort') }}:
                                    {{ booleanLabel(preset.keep_original) }}
                                </span>
                            </td>

                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="flex justify-end">
                                    <IconEdit
                                        :href="route('admin.imagePresets.edit', preset.id)"
                                    />
                                </div>
                            </td>
                        </tr>
                    </template>
                </draggable>
            </table>

            <div
                v-else
                class="p-5 text-center text-slate-700 dark:text-slate-100"
            >
                {{ t('noData') }}
            </div>
        </div>
    </div>
</template>
