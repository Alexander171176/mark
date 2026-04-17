<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import ActivityToggle from '@/Components/Admin/Buttons/ActivityToggle.vue'
import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'

const { t } = useI18n()

const props = defineProps({
    hashtags: {
        type: Array,
        default: () => [],
    },
    selectedHashtags: {
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

// --- Локальная копия для vuedraggable ---
const localHashtags = ref([])

watch(
    () => props.hashtags,
    (newVal) => {
        localHashtags.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true },
)

// --- Drag end: отдаем массив ID вверх (как в HashtagTable) ---
const handleDragEnd = () => {
    const newOrderIds = localHashtags.value.map((hashtag) => hashtag.id)
    emits('update-sort-order', newOrderIds)
}

// --- Массовый выбор (как в HashtagTable) ---
const toggleAll = (event) => {
    const checked = event.target.checked
    const ids = localHashtags.value.map((r) => r.id)
    emits('toggle-all', { ids, checked })
}
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-400 dark:border-slate-500 relative"
    >
        <!-- Верхняя панель: кол-во выбранных + чекбокс -->
        <div
            class="flex items-center justify-between px-3 py-2
                   border-b border-slate-400 dark:border-slate-500"
        >
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('selected') }}: {{ selectedHashtags.length }}
            </div>

            <label
                v-if="localHashtags.length"
                class="flex items-center text-xs text-slate-600
                       dark:text-slate-200 cursor-pointer"
            >
                <span>{{ t('selectAll') }}</span>
                <input type="checkbox" class="mx-2" @change="toggleAll" />
            </label>
        </div>

        <div v-if="localHashtags.length" class="p-3">
            <draggable
                tag="div"
                v-model="localHashtags"
                item-key="id"
                @end="handleDragEnd"
                handle=".drag-handle"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4"
            >
                <template #item="{ element: hashtag }">
                    <div
                        class="relative flex flex-col h-full rounded-md
                               border border-slate-400 dark:border-slate-500
                               bg-slate-50/70 dark:bg-slate-800/80 shadow-sm
                               hover:shadow-md transition-shadow duration-150"
                    >
                        <!-- Верхняя панель карточки -->
                        <div
                            class="flex items-center justify-between px-2 py-1
                                   border-b border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-2">
                                <!-- drag handle -->
                                <button
                                    type="button"
                                    class="drag-handle text-slate-400 hover:text-slate-700
                                           dark:hover:text-slate-100"
                                    :title="t('dragDrop')"
                                >
                                    <svg
                                        class="w-4 h-4"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"
                                        />
                                    </svg>
                                </button>

                                <!-- ID бейдж -->
                                <div
                                    class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm
                                           border border-gray-400
                                           bg-slate-200 dark:bg-slate-700
                                           text-slate-800 dark:text-blue-100"
                                    :title="`[sort: ${hashtag.sort ?? '-'}]`"
                                >
                                    ID: {{ hashtag.id }}
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                <input
                                    type="checkbox"
                                    :checked="selectedHashtags.includes(hashtag.id)"
                                    @change="$emit('toggle-select', hashtag.id)"
                                />
                            </div>
                        </div>

                        <!-- Контент -->
                        <div class="flex flex-col flex-1 px-3 py-2 space-y-2">
                            <!-- Имя / хештег как цветной бейдж -->
                            <div class="flex justify-center">
                                <a
                                    :href="`/hashtags/${encodeURIComponent(hashtag.slug)}`"
                                    class="inline-flex items-center text-xs font-semibold
                                           text-sky-700 dark:text-sky-200 hover:underline
                                           px-2 py-0.5 rounded-full border-2 bg-white/70 dark:bg-slate-900/60"
                                    :style="{ borderColor: hashtag.color || '#666666' }"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    :title="hashtag.slug"
                                >
                                    {{ hashtag.name }}
                                </a>
                            </div>

                            <!-- Короткое описание -->
                            <div
                                v-if="hashtag.short"
                                class="text-[11px] text-slate-700 dark:text-slate-100
                                       text-center line-clamp-3"
                            >
                                {{ hashtag.short }}
                            </div>

                            <!-- Статистика -->
                            <div
                                class="pt-1 flex flex-wrap justify-center gap-3
                                       font-semibold text-[11px]
                                       text-slate-900 dark:text-slate-200"
                            >
                                <div
                                    v-if="hashtag.views > 0"
                                    :title="t('views')"
                                    class="flex flex-row items-center"
                                >
                                    <svg
                                        class="w-4 h-4 fill-current shrink-0"
                                        viewBox="0 0 16 16"
                                    >
                                        <path
                                            class="fill-current text-blue-600 dark:text-blue-300"
                                            d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"
                                        />
                                    </svg>
                                    <span class="ml-1">
                                        {{ hashtag.views ?? 0 }}
                                    </span>
                                </div>

                                <div
                                    v-if="hashtag.likes > 0"
                                    :title="t('likes')"
                                    class="flex flex-row items-center"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        height="24"
                                        width="24"
                                        viewBox="0 0 24 24"
                                        class="shrink-0 h-4 w-4"
                                    >
                                        <path
                                            class="fill-current text-red-400 dark:text-red-300"
                                            d="M3,9H1a1,1,0,0,0-1,1V22a1,1,0,0,0,1,1H4V10A1,1,0,0,0,3,9Z"
                                        />
                                        <path
                                            class="fill-current text-red-400 dark:text-red-300"
                                            d="M21.882,8.133A2.986,2.986,0,0,0,21,8H15V5c0-3.824-2.589-4.942-3.958-5a1.017,1.017,0,0,0-.734.277A1,1,0,0,0,10,1V5.638l-4,4.8V23H18.23A2.985,2.985,0,0,0,21.1,20.882l2.769-9A3,3,0,0,0,21.882,8.133Z"
                                        />
                                    </svg>
                                    <span class="ml-1">{{ hashtag.likes }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Действия -->
                        <div
                            class="flex items-center justify-center px-3 py-2
                                   border-t border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-1">
                                <ActivityToggle
                                    :isActive="hashtag.activity"
                                    @toggle-activity="$emit('toggle-activity', hashtag)"
                                    :title="hashtag.activity ? t('enabled') : t('disabled')"
                                />
                                <IconEdit :href="route('admin.hashtags.edit', hashtag.id)" />
                                <DeleteIconButton @delete="$emit('delete', hashtag.id)" />
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
