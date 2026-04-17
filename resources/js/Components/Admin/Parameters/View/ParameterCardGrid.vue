<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import ActivityToggle from '@/Components/Admin/Buttons/ActivityToggle.vue'
import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'

const { t } = useI18n()

const props = defineProps({
    settings: {
        type: Array,
        default: () => []
    },
    selectedSettings: {
        type: Array,
        default: () => []
    }
})

const emits = defineEmits([
    'toggle-activity',
    'delete',
    'update-sort-order',
    'toggle-select',
    'toggle-all'
])

// Локальная копия для drag’n’drop
const localSettings = ref([])

watch(
    () => props.settings,
    (newVal) => {
        localSettings.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

// Drag end → отправляем порядок ID
const handleDragEnd = () => {
    const newOrderIds = localSettings.value.map(s => s.id)
    emits('update-sort-order', newOrderIds)
}

// Массовый выбор
const toggleAll = (event) => {
    const checked = event.target.checked
    const ids = localSettings.value.map(p => p.id)
    emits('toggle-all', { ids, checked })
}
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-400 dark:border-slate-500 relative"
    >
        <!-- Верхняя панель: инфо + чекбокс "выбрать всё" -->
        <div
            class="flex items-center justify-between px-3 py-2
                   border-b border-slate-400 dark:border-slate-500"
        >
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('selected') }}: {{ selectedSettings.length }}
            </div>

            <label
                v-if="localSettings.length"
                class="flex items-center text-xs text-slate-600
                       dark:text-slate-200 cursor-pointer"
            >
                <span>{{ t('selectAll') }}</span>
                <input type="checkbox" class="mx-2" @change="toggleAll" />
            </label>
        </div>

        <!-- Сетка карточек с drag’n’drop -->
        <div v-if="localSettings.length" class="p-3">
            <draggable
                tag="div"
                v-model="localSettings"
                item-key="id"
                @end="handleDragEnd"
                handle=".handle"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
            >
                <template #item="{ element: setting }">
                    <article
                        class="relative flex flex-col h-full rounded-md
                               border border-slate-400 dark:border-slate-500
                               bg-slate-50/80 dark:bg-slate-800/90 shadow-sm
                               hover:shadow-md transition-shadow duration-150"
                    >
                        <!-- Шапка карточки: drag, ID, категория -->
                        <header
                            class="flex items-center justify-between px-2 py-1
                                   border-b border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-2">
                                <!-- drag handle -->
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
                                >
                                    ID: {{ setting.id }}
                                </span>
                            </div>

                            <div
                                class="text-[10px] px-2 py-0.5 rounded-sm bg-blue-500
                                       text-slate-50 font-semibold"
                                :title="setting.description"
                            >
                                {{ setting.category }}
                            </div>
                        </header>

                        <!-- Основное содержимое: параметр + значение -->
                        <div class="flex-1 px-3 py-3 space-y-2 text-center">
                            <!-- Название параметра -->
                            <div
                                class="text-[13px] font-semibold text-orange-500
                                       dark:text-orange-200 line-clamp-2"
                                :title="setting.option"
                            >
                                {{ setting.option }}
                            </div>

                            <!-- Значение -->
                            <div
                                class="text-[13px] font-semibold text-teal-600
                                       dark:text-teal-200 break-all"
                                :title="setting.value"
                            >
                                {{ setting.value }}
                            </div>

                            <!-- Тип или доп. инфо при желании -->
                            <div
                                v-if="setting.type"
                                class="text-[11px] text-slate-500 dark:text-slate-300"
                            >
                                {{ t('type') }}: {{ setting.type }}
                            </div>
                        </div>

                        <!-- Подвал: активность + действия + чекбокс -->
                        <footer
                            class="px-3 py-2 border-t border-dashed
                                   border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center justify-between space-x-2">
                                <div class="flex items-center space-x-2">
                                    <ActivityToggle
                                        :isActive="setting.activity"
                                        @toggle-activity="$emit('toggle-activity', setting)"
                                        :title="setting.activity ? t('enabled') : t('disabled')"
                                    />
                                    <IconEdit :href="route('admin.parameters.edit', setting.id)" />
                                    <DeleteIconButton @delete="$emit('delete', setting.id)" />
                                </div>

                                <div>
                                    <input
                                        type="checkbox"
                                        :checked="selectedSettings.includes(setting.id)"
                                        @change="$emit('toggle-select', setting.id)"
                                    />
                                </div>
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
