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

/** Название формы */
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

/** Да / Нет */
const booleanLabel = (value) => {
    return value ? t('yes') : t('no')
}

/** Цветовая метка оригинала */
const originalBadgeClass = () => {
    return 'bg-slate-500 dark:bg-slate-900 text-white'
}
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-400 dark:border-slate-500 relative"
    >
        <div v-if="localPresets.length" class="p-3">
            <draggable
                tag="div"
                v-model="localPresets"
                item-key="id"
                @end="handleDragEnd"
                handle=".drag-handle"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
            >
                <template #item="{ element: preset }">
                    <article
                        class="relative flex flex-col h-full rounded-md
                               border border-slate-400 dark:border-slate-500
                               bg-slate-50/70 dark:bg-slate-800/80 shadow-sm
                               hover:shadow-md transition-shadow duration-150"
                    >
                        <!-- Шапка -->
                        <header
                            class="flex items-center justify-between px-2 py-1
                                   border-b border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-2">
                                <button
                                    type="button"
                                    class="drag-handle text-slate-400 hover:text-slate-700
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
                                    class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm
                                           border border-gray-400 bg-slate-200 dark:bg-slate-700
                                           text-slate-800 dark:text-slate-200"
                                    :title="`sort: ${preset.sort ?? '—'}`"
                                >
                                    ID: {{ preset.id }}
                                </span>
                            </div>

                            <span
                                class="block w-fit py-0.5 px-2 rounded-sm text-xs font-semibold"
                                :class="shapeBadgeClass(preset.shape)"
                            >
                                {{ shapeLabel(preset.shape) }}
                            </span>
                        </header>

                        <!-- Контент -->
                        <div class="flex flex-col flex-1 px-3 py-3 space-y-3">
                            <div
                                class="text-center font-semibold
                                       text-amber-600 dark:text-amber-200"
                                :title="preset.description || '—'"
                            >
                                {{ preset.key }}
                            </div>

                            <div
                                class="text-xs text-center font-semibold
                                       text-slate-600 dark:text-slate-300
                                       line-clamp-2 min-h-[32px]"
                                :title="preset.description"
                            >
                                {{ preset.description || '—' }}
                            </div>

                            <div
                                class="grid grid-cols-2 gap-2 text-xs
                                       text-slate-700 dark:text-slate-100"
                            >
                                <div
                                    class="rounded-sm p-2 text-center
                                           border border-slate-300 dark:border-slate-600r"
                                >
                                    <div class="text-slate-700 dark:text-slate-300">
                                        {{ t('ratio') }}
                                    </div>
                                    <div class="font-semibold text-sm
                                                text-sky-700 dark:text-sky-300">
                                        {{ preset.resolution }}
                                    </div>
                                    <div
                                        v-if="preset.aspect_ratio"
                                        class="text-center text-xs
                                               text-slate-700 dark:text-slate-300"
                                    >
                                        {{ preset.aspect_ratio }}
                                    </div>
                                </div>

                                <div
                                    class="rounded-sm border border-slate-300 dark:border-slate-600
                                           p-2 text-center"
                                >
                                    <div class="text-slate-700 dark:text-slate-300">
                                        {{ t('file') }}
                                    </div>
                                    <div class="font-semibold text-sm
                                                text-indigo-700 dark:text-indigo-300">
                                        {{ preset.max_file_size_mb }} MB
                                    </div>
                                </div>

                                <div
                                    class="rounded-sm border border-slate-300 dark:border-slate-600
                                           p-2 text-center"
                                >
                                    <div class="text-slate-700 dark:text-slate-300">
                                        {{ t('photo') }}
                                    </div>
                                    <div
                                        class="font-semibold text-sm"
                                        :class="preset.image_rotation_enabled
                                            ? 'text-teal-700 dark:text-teal-300'
                                            : 'text-red-700 dark:text-red-300'"
                                    >
                                        {{ booleanLabel(preset.image_rotation_enabled) }}
                                    </div>
                                </div>

                                <div
                                    class="rounded-sm border border-slate-300 dark:border-slate-600
                                           p-2 text-center"
                                >
                                    <div class="text-slate-700 dark:text-slate-300">
                                        {{ t('photoFrames') }}
                                    </div>
                                    <div
                                        class="font-semibold text-sm"
                                        :class="preset.crop_rotation_enabled
                                            ? 'text-teal-700 dark:text-teal-300'
                                            : 'text-red-700 dark:text-red-300'"
                                    >
                                        {{ booleanLabel(preset.crop_rotation_enabled) }}
                                    </div>
                                </div>
                            </div>

                            <div class="text-center text-xs">
                                <span
                                    class="block w-full py-0.5 px-2 rounded-sm
                                               text-[10px] font-semibold text-center"
                                    :class="originalBadgeClass()"
                                    :title="t('keepOriginal')"
                                >
                                {{ t('originalShort') }}: {{ booleanLabel(preset.keep_original) }}
                                </span>
                            </div>
                        </div>

                        <!-- Действия -->
                        <footer
                            class="flex items-center justify-center px-3 py-2
                                   border-t border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <IconEdit
                                :href="route(
                                    'admin.imagePresets.edit',
                                    preset.id
                                )"
                            />
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
