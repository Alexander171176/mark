<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable';
import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'
import ActivityToggle from '@/Components/Admin/Buttons/ActivityToggle.vue'

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
    'edit',
    'delete',
    'update-sort-order',
    'toggle-select',
    'toggle-all'
])

// --- Локальная копия для vuedraggable ---
const localHashtags = ref([]);

watch(
    () => props.hashtags,
    (newVal) => {
        localHashtags.value = JSON.parse(JSON.stringify(newVal || []));
    },
    { immediate: true, deep: true }
);

// --- Функция, вызываемая vuedraggable после завершения перетаскивания ---
const handleDragEnd = () => {
    const newOrderIds = localHashtags.value.map(
        (hashtag) => hashtag.id
    );
    emits('update-sort-order', newOrderIds);
};

// --- Логика массовых действий ---
const toggleAll = (event) => {
    const checked = event.target.checked;
    const ids = localHashtags.value.map(r => r.id);
    emits('toggle-all', { ids, checked });
};

//  Усечение текста с троеточием
const truncateText = (text, maxLength = 30) => {
    if (!text) return '';
    return text.length > maxLength
        ? text.slice(0, maxLength).trimEnd() + '…'
        : text;
};
</script>

<template>
    <div class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
                border border-slate-200 dark:border-slate-600 relative">

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

        <div class="overflow-x-auto">
            <table v-if="hashtags.length > 0"
                   class="table-auto w-full text-slate-700 dark:text-slate-100">
                <thead
                    class="text-sm uppercase bg-slate-200 dark:bg-cyan-900
                           border border-solid border-gray-300 dark:border-gray-700">
                <tr>
                    <th class="px-2 py-3 w-px">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-4 h-4 fill-current text-slate-800 dark:text-slate-200"
                             height="24" width="24" viewBox="0 0 24 24">
                            <path
                                d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"></path>
                            <path
                                d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"></path>
                        </svg>
                    </th>
                    <th class="px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap w-px">
                        <div class="font-medium text-center">{{ t('id') }}</div>
                    </th>
                    <th class="px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap">
                        <div class="font-medium text-left">{{ t('name') }}</div>
                    </th>
                    <th class="px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap">
                        <div class="font-semibold text-left">
                            {{ t('description') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('views')">
                            <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16">
                                <path d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"></path>
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('likes')">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24" width="24"
                                 viewBox="0 0 24 24" class="shrink-0 h-4 w-4">
                                <path class="fill-current text-red-400 dark:text-red-300"
                                      d="M3,9H1a1,1,0,0,0-1,1V22a1,1,0,0,0,1,1H4V10A1,1,0,0,0,3,9Z"></path>
                                <path class="fill-current text-red-400 dark:text-red-300"
                                      d="M21.882,8.133A2.986,2.986,0,0,0,21,8H15V5c0-3.824-2.589-4.942-3.958-5a1.017,1.017,0,0,0-.734.277A1,1,0,0,0,10,1V5.638l-4,4.8V23H18.23A2.985,2.985,0,0,0,21.1,20.882l2.769-9A3,3,0,0,0,21.882,8.133Z"></path>
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap">
                        <div class="font-semibold text-end">{{ t('actions') }}</div>
                    </th>
                    <th class="px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap">
                        <div class="text-center">
                            <input type="checkbox" @change="toggleAll"/>
                        </div>
                    </th>
                </tr>
                </thead>
                <draggable
                    tag="tbody"
                    v-model="localHashtags"
                    @end="handleDragEnd"
                    item-key="id"
                    handle=".handle"
                >
                <template #item="{ element: hashtag }">
                        <tr class="text-sm font-semibold border-b-2
                                   hover:bg-slate-100 dark:hover:bg-cyan-800">

                            <td class="px-2 py-1 text-center cursor-move handle">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-300"
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" />
                                </svg>
                            </td>
                            <td class="px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap">
                                <div class="text-center text-slate-800 dark:text-blue-200"
                                     :title="hashtag.sort">
                                    {{ hashtag.id }}
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap">
                                <a
                                    :href="`/hashtags/${encodeURIComponent(hashtag.slug)}`"
                                    class="text-sky-600 dark:text-sky-200 hover:underline
                                           hover:text-sky-600 dark:hover:text-sky-200
                                           px-2 py-0.5 rounded border-2"
                                    :style="{ borderColor: hashtag.color || '#666666' }"
                                    target="_blank"
                                    rel="noopener noreferrer">
                                    {{ hashtag.name }}
                                </a>
                            </td>
                            <td class="px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap">
                                <div class="text-left text-xs
                                            text-teal-600 dark:text-teal-300">
                                    {{ truncateText(hashtag.short, 30) }}
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap">
                                <div class="text-center">{{ hashtag.views }}</div>
                            </td>
                            <td class="px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap">
                                <div class="text-center text-red-400 dark:text-red-300">
                                    {{ hashtag.likes }}
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap">
                                <div class="flex justify-end space-x-2">
                                    <ActivityToggle :isActive="hashtag.activity"
                                                    @toggle-activity="$emit('toggle-activity', hashtag)"
                                                    :title="hashtag.activity ? t('enabled') : t('disabled')"/>
                                    <IconEdit :href="route('admin.hashtags.edit', hashtag.id)" />
                                    <DeleteIconButton @delete="$emit('delete', hashtag.id)" />
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap">
                                <div class="text-center">
                                    <div class="text-center">
                                        <input type="checkbox"
                                               :checked="selectedHashtags.includes(hashtag.id)"
                                               @change="$emit('toggle-select', hashtag.id)"/>
                                    </div>
                                </div>
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
