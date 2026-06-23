<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'

const { t } = useI18n()

const props = defineProps({
    presets: {
        type: Array,
        default: () => [],
    },
})

const emits = defineEmits([
    'update-sort-order',
])

/** Локальная копия для drag&drop */
const localPresets = ref([])

watch(
    () => props.presets,
    (newVal) => {
        localPresets.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/** После перетаскивания отдаём новый порядок ID наверх */
const handleDragEnd = () => {
    const newOrderIds = localPresets.value.map(preset => preset.id)

    emits('update-sort-order', newOrderIds)
}

/** Перевод формы */
const shapeLabel = (shape) => {
    const map = {
        rectangle: 'shapeRectangle',
        square: 'shapeSquare',
        circle: 'shapeCircle',
    }

    return map[shape] ? t(map[shape]) : shape
}

/** Цветовая метка формы */
const shapeBadgeClass = (shape) => {
    const classes = {
        rectangle: 'bg-blue-600 dark:bg-blue-700 text-white',
        square: 'bg-purple-600 dark:bg-purple-700 text-white',
        circle: 'bg-pink-600 dark:bg-pink-700 text-white',
    }

    return classes[shape] || 'bg-gray-500 text-white'
}

/** Цветовая метка типа поворота */
const rotationBadgeClass = (type) => {
    const classes = {
        image: 'bg-sky-600 dark:bg-sky-700 text-white',
        crop: 'bg-cyan-600 dark:bg-cyan-700 text-white',
    }

    return classes[type] || 'bg-gray-500 text-white'
}

/** Цветовая метка оригинала */
const originalBadgeClass = () => {
    return 'bg-slate-500 dark:bg-slate-900 text-white'
}

/** Да / Нет */
const booleanLabel = (value) => {
    return value ? t('yes') : t('no')
}
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-200 dark:border-slate-600 relative"
    >
        <div class="overflow-x-auto">
            <table
                v-if="localPresets.length > 0"
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
                            height="24"
                            width="24"
                            viewBox="0 0 24 24"
                        >
                            <path
                                d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z" />
                            <path
                                d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z" />
                        </svg>
                    </th>
                    <th class="px-2 py-3 w-px">
                        <div class="font-medium text-center">{{ t('id') }}</div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-left">{{ t('key') }}</div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-center">{{ t('shape') }}</div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-center">{{ t('typeSize') }}</div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-center">{{ t('file') }}</div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-center">{{ t('turn') }}</div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-center">{{ t('originalShort') }}</div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-end">{{ t('actions') }}</div>
                    </th>
                </tr>
                </thead>

                <draggable
                    tag="tbody"
                    v-model="localPresets"
                    @end="handleDragEnd"
                    item-key="id"
                    handle=".handle"
                >
                    <template #item="{ element: preset }">
                        <tr
                            class="text-sm font-semibold border-b-2
                                   hover:bg-slate-100 dark:hover:bg-cyan-800"
                        >
                            <!-- drag handle -->
                            <td class="px-2 py-1 text-center cursor-move handle">
                                <svg
                                    class="w-4 h-4 text-gray-500 dark:text-gray-300"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" />
                                </svg>
                            </td>
                            <!-- id -->
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div
                                    class="font-semibold text-center text-xs
                                           text-slate-900 dark:text-slate-100"
                                    :title="`sort: ${preset.sort ?? '—'}`"
                                >
                                    {{ preset.id }}
                                </div>
                            </td>
                            <!-- key -->
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div
                                    class="font-semibold text-amber-600 dark:text-amber-200"
                                    :title="preset.description || '—'"
                                >
                                    {{ preset.key }}
                                </div>
                            </td>
                            <!-- shape -->
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="text-center">
                                    <span
                                        class="block w-full py-0.5 px-2 rounded-sm text-xs font-semibold"
                                        :class="shapeBadgeClass(preset.shape)"
                                    >
                                        {{ shapeLabel(preset.shape) }}
                                    </span>
                                </div>
                            </td>
                            <!-- resolution -->
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="text-center text-sky-700 dark:text-sky-300">
                                    {{ preset.resolution }}
                                </div>

                                <div
                                    v-if="preset.aspect_ratio"
                                    class="text-center text-xs text-slate-500 dark:text-slate-300"
                                >
                                    {{ preset.aspect_ratio }}
                                </div>
                            </td>
                            <!-- max file size -->
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="text-center text-indigo-700 dark:text-indigo-300">
                                    {{ preset.max_file_size_mb }} MB
                                </div>

                                <div class="text-center text-xs text-slate-500 dark:text-slate-300">
                                    {{ preset.max_file_size_kb }} KB
                                </div>
                            </td>
                            <!-- rotations -->
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="flex flex-col justify-center items-stretch gap-1">
        <span
            class="block w-full py-0.5 px-2 rounded-sm text-[10px] font-semibold text-center"
            :class="rotationBadgeClass('image')"
            :title="t('photo')"
        >
            {{ t('photo') }}: {{ booleanLabel(preset.image_rotation_enabled) }}
        </span>

                                    <span
                                        class="block w-full py-0.5 px-2 rounded-sm text-[10px] font-semibold text-center"
                                        :class="rotationBadgeClass('crop')"
                                        :title="t('photoFrames')"
                                    >
            {{ t('photoFrames') }}: {{ booleanLabel(preset.crop_rotation_enabled) }}
        </span>
                                </div>
                            </td>
                            <!-- keep original -->
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="text-center">
                                    <span
                                        class="block w-full py-0.5 px-2 rounded-sm
                                               text-[10px] font-semibold text-center"
                                        :class="originalBadgeClass()"
                                        :title="t('keepOriginal')"
                                    >
                                {{ t('originalShort') }}: {{ booleanLabel(preset.keep_original) }}
                                    </span>
                                </div>
                            </td>
                            <!-- actions -->
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="flex justify-end">
                                    <IconEdit
                                        :href="route(
                                            'admin.imagePresets.edit',
                                            preset.id
                                        )"
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
