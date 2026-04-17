<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue';
import draggable from 'vuedraggable';
import { useI18n } from 'vue-i18n';
import ActivityToggle from '@/Components/Admin/Buttons/ActivityToggle.vue';
import IconEdit from "@/Components/Admin/Buttons/IconEdit.vue";
import CloneIconButton from '@/Components/Admin/Buttons/CloneIconButton.vue';
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue';

const { t } = useI18n();

/** Входящие пропсы компонента */
const props = defineProps({
    rubrics: Array,
    selectedRubrics: Array
});

/** Обработчики действий */
const emits = defineEmits([
    'toggle-activity',
    'edit',
    'delete',
    'update-sort-order',
    'clone',
    'toggle-select',
    'toggle-all'
]);

/** Локальная копия для vuedraggable */
const localRubrics = ref([]);

/** Следим за изменением props.rubrics и обновляем локальную копию */
watch(() => props.rubrics, (newVal) => {
    // Создаем глубокую копию, чтобы избежать мутации props
    localRubrics.value = JSON.parse(JSON.stringify(newVal || []));
}, { immediate: true, deep: true }); // immediate: true для инициализации

/** Функция, вызываемая vuedraggable после завершения перетаскивания */
const handleDragEnd = () => {
    // Отправляем НОВЫЙ ПОРЯДОК ID из локального массива
    const newOrderIds = localRubrics.value.map(rubric => rubric.id);
    emits('update-sort-order', newOrderIds); // Отправляем массив ID
};

/** Логика массовых действий */
const toggleAll = (event) => {
    const checked = event.target.checked;
    const ids = localRubrics.value.map(r => r.id);
    emits('toggle-all', { ids, checked });
};

/** Основное изображение (минимальный order) */
const getPrimaryImage = (rubric) => {
    if (rubric.images && rubric.images.length) {
        // Создаем копию массива и сортируем по возрастанию order
        return [...rubric.images].sort((a, b) => a.order - b.order)[0];
    }
    return null;
};

/** Хелперы автора (имя, email) */
const ownerName = (rubric) => rubric?.owner?.name || t('noData')
const ownerEmail = (rubric) => rubric?.owner?.email || ''


/** title изображения автора (имя, email) */
const ownerTitle = (rubric) => {
    const o = rubric?.owner
    if (!o) return t('noData')
    return `${o.name || ''}${o.email ? ' — ' + o.email : ''}`.trim()
}

/** изображение автора */
const ownerAvatar = (rubric) => {
    return rubric?.owner?.profile_photo_url || '/storage/profile-photos/default-image.png'
}

</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-200 dark:border-slate-600 relative">

        <!-- Верхняя панель: кол-во выбранных + чекбокс -->
        <div
            class="flex items-center justify-between px-3 py-2
                   border-b border-slate-400 dark:border-slate-500">
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('selected') }}: {{ selectedRubrics.length }}
            </div>

            <label
                v-if="localRubrics.length"
                class="flex items-center text-xs text-slate-600
                       dark:text-slate-200 cursor-pointer">
                <span>{{ t('selectAll') }}</span>
                <input type="checkbox" class="mx-2" @change="toggleAll" />
            </label>
        </div>

        <div class="overflow-x-auto">
            <table v-if="rubrics.length > 0"
                   class="table-auto w-full text-slate-700 dark:text-slate-100">
                <thead class="text-sm uppercase
                              bg-slate-200 dark:bg-cyan-900
                              border border-solid border-gray-300 dark:border-gray-700">
                <tr>
                    <th class="px-2 py-3 w-px">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-4 h-4 fill-current text-slate-800 dark:text-slate-200"
                             height="24" width="24" viewBox="0 0 24 24">
                            <path d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"></path>
                            <path d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"></path>
                        </svg>
                    </th>
                    <th class="px-2 first:pl-8 last:pr-8 py-3 whitespace-nowrap w-px">
                        <div class="text-center font-medium">{{ t('id') }}</div>
                    </th>
                    <th class="px-2 first:pl-8 last:pr-8 py-3 whitespace-nowrap w-px">
                        <div class="flex justify-center" :title="t('localization')">
                            <svg class="w-8 h-8 fill-current shrink-0" viewBox="0 0 640 512">
                                <path d="M0 128C0 92.7 28.7 64 64 64l192 0 48 0 16 0 256 0c35.3 0 64 28.7 64 64l0 256c0 35.3-28.7 64-64 64l-256 0-16 0-48 0L64 448c-35.3 0-64-28.7-64-64L0 128zm320 0l0 256 256 0 0-256-256 0zM178.3 175.9c-3.2-7.2-10.4-11.9-18.3-11.9s-15.1 4.7-18.3 11.9l-64 144c-4.5 10.1 .1 21.9 10.2 26.4s21.9-.1 26.4-10.2l8.9-20.1 73.6 0 8.9 20.1c4.5 10.1 16.3 14.6 26.4 10.2s14.6-16.3 10.2-26.4l-64-144zM160 233.2L179 276l-38 0 19-42.8zM448 164c11 0 20 9 20 20l0 4 44 0 16 0c11 0 20 9 20 20s-9 20-20 20l-2 0-1.6 4.5c-8.9 24.4-22.4 46.6-39.6 65.4c.9 .6 1.8 1.1 2.7 1.6l18.9 11.3c9.5 5.7 12.5 18 6.9 27.4s-18 12.5-27.4 6.9l-18.9-11.3c-4.5-2.7-8.8-5.5-13.1-8.5c-10.6 7.5-21.9 14-34 19.4l-3.6 1.6c-10.1 4.5-21.9-.1-26.4-10.2s.1-21.9 10.2-26.4l3.6-1.6c6.4-2.9 12.6-6.1 18.5-9.8l-12.2-12.2c-7.8-7.8-7.8-20.5 0-28.3s20.5-7.8 28.3 0l14.6 14.6 .5 .5c12.4-13.1 22.5-28.3 29.8-45L448 228l-72 0c-11 0-20-9-20-20s9-20 20-20l52 0 0-4c0-11 9-20 20-20z"/>
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 first:pl-8 last:pr-8 py-3 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('owner')">
                            <svg  class="w-6 h-6 fill-current shrink-0" viewBox="0 0 24 24">
                                <path d="M3,7H1V2A1,1,0,0,1,2,1H7V3H3Z"></path>
                                <path d="M23,7H21V3H17V1h5a1,1,0,0,1,1,1Z"></path>
                                <path d="M7,23H2a1,1,0,0,1-1-1V17H3v4H7Z"></path>
                                <path d="M22,23H17V21h4V17h2v5A1,1,0,0,1,22,23Z"></path>
                                <path d="M18.242,18.03l-2.727-.681a1,1,0,0,1-.744-.806l-.249-1.491A6.792,6.792,0,0,0,17,10V9A5,5,0,0,0,7,9v1a6.792,6.792,0,0,0,2.478,5.052l-.249,1.491a1,1,0,0,1-.743.806l-2.728.681A1,1,0,0,0,6,20H18a1,1,0,0,0,.242-1.97Z"></path>
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 first:pl-8 last:pr-8 py-3 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('image')">
                            <svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 512 512">
                                <path d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l96 0 32 0 208 0c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"/>
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 first:pl-8 last:pr-8 py-3 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('icon')">
                            <svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 24 24">
                                <rect fill-rule="nonzero" x="0" y="0"></rect>
                                <path d="M4,5 C4,3.89543 4.89543,3 6,3 L15.1716,3 C15.702,3 16.2107,3.21071 16.5858,3.58579 L19.4142,6.41421 C19.7893,6.78929 20,7.29799 20,7.82843 L20,19 C20,20.1046 19.1046,21 18,21 L6,21 C4.89543,21 4,20.1046 4,19 L4,5 Z" stroke-width="2" stroke-linecap="round"></path>
                                <path class="text-slate-100 dark:text-slate-900"
                                      d="M15,4 L15,6 C15,7.10457 15.8954,8 17,8 L19,8" stroke-width="2" stroke-linecap="round"></path>
                                <path class="text-slate-100 dark:text-slate-900"
                                      d="M8.61462,13.856 C8.52662,13.744 8.41062,13.656 8.26662,13.592 C8.12662,13.524 7.99062,13.49 7.85862,13.49 C7.79062,13.49 7.72062,13.496 7.64862,13.508 C7.58062,13.52 7.51862,13.544 7.46262,13.58 C7.40662,13.612 7.35862,13.656 7.31862,13.712 C7.28262,13.764 7.26462,13.832 7.26462,13.916 C7.26462,13.988 7.27862,14.048 7.30662,14.096 C7.33862,14.144 7.38262,14.186 7.43862,14.222 C7.49862,14.258 7.56862,14.292 7.64862,14.324 C7.72862,14.352 7.81862,14.382 7.91862,14.414 C8.06262,14.462 8.21262,14.516 8.36862,14.576 C8.52462,14.632 8.66662,14.708 8.79462,14.804 C8.92262,14.9 9.02862,15.02 9.11262,15.164 C9.19662,15.304 9.23862,15.48 9.23862,15.692 C9.23862,15.936 9.19262,16.148 9.10062,16.328 C9.01262,16.504 8.89262,16.65 8.74062,16.766 C8.58862,16.882 8.41462,16.968 8.21862,17.024 C8.02262,17.08 7.82062,17.108 7.61262,17.108 C7.30862,17.108 7.01462,17.056 6.73062,16.952 C6.44662,16.844 6.21062,16.692 6.02262,16.496 L6.69462,15.812 C6.79862,15.94 6.93462,16.048 7.10262,16.136 C7.27462,16.22 7.44462,16.262 7.61262,16.262 C7.68862,16.262 7.76262,16.254 7.83462,16.238 C7.90662,16.222 7.96862,16.196 8.02062,16.16 C8.07662,16.124 8.12062,16.076 8.15262,16.016 C8.18462,15.956 8.20062,15.884 8.20062,15.8 C8.20062,15.72 8.18062,15.652 8.14062,15.596 C8.10062,15.54 8.04262,15.49 7.96662,15.446 C7.89462,15.398 7.80262,15.356 7.69062,15.32 C7.58262,15.28 7.45862,15.238 7.31862,15.194 C7.18262,15.15 7.04862,15.098 6.91662,15.038 C6.78862,14.978 6.67262,14.902 6.56862,14.81 C6.46862,14.714 6.38662,14.6 6.32262,14.468 C6.26262,14.332 6.23262,14.168 6.23262,13.976 C6.23262,13.74 6.28062,13.538 6.37662,13.37 C6.47262,13.202 6.59862,13.064 6.75462,12.956 C6.91062,12.848 7.08662,12.77 7.28262,12.722 C7.47862,12.67 7.67662,12.644 7.87662,12.644 C8.11662,12.644 8.36062,12.688 8.60862,12.776 C8.86062,12.864 9.08062,12.994 9.26862,13.166 L8.61462,13.856 Z M11.9805,17 L10.9545,17 L9.34652,12.752 L10.4985,12.752 L11.4825,15.764 L11.5065,15.764 L12.4845,12.752 L13.6185,12.752 L11.9805,17 Z M17.6681,16.742 C17.4601,16.85 17.2161,16.938 16.9361,17.006 C16.6601,17.074 16.3581,17.108 16.0301,17.108 C15.6901,17.108 15.3761,17.054 15.0881,16.946 C14.8041,16.838 14.5581,16.686 14.3501,16.49 C14.1461,16.294 13.9861,16.06 13.8701,15.788 C13.7541,15.512 13.6961,15.206 13.6961,14.87 C13.6961,14.53 13.7541,14.222 13.8701,13.946 C13.9901,13.67 14.1541,13.436 14.3621,13.244 C14.5701,13.048 14.8141,12.898 15.0941,12.794 C15.3741,12.69 15.6761,12.638 16.0001,12.638 C16.3361,12.638 16.6481,12.69 16.9361,12.794 C17.2241,12.894 17.4581,13.03 17.6381,13.202 L16.9901,13.94 C16.8901,13.824 16.7581,13.73 16.5941,13.658 C16.4301,13.582 16.2441,13.544 16.0361,13.544 C15.8561,13.544 15.6901,13.578 15.5381,13.646 C15.3861,13.71 15.2541,13.802 15.1421,13.922 C15.0301,14.038 14.9421,14.178 14.8781,14.342 C14.8181,14.502 14.7881,14.678 14.7881,14.87 C14.7881,15.066 14.8161,15.246 14.8721,15.41 C14.9281,15.574 15.0101,15.716 15.1181,15.836 C15.2301,15.952 15.3661,16.044 15.5261,16.112 C15.6901,16.176 15.8761,16.208 16.0841,16.208 C16.2041,16.208 16.3181,16.2 16.4261,16.184 C16.5341,16.164 16.6341,16.134 16.7261,16.094 L16.7261,15.32 L15.9161,15.32 L15.9161,14.492 L17.6681,14.492 L17.6681,16.742 Z" fill-rule="nonzero"></path>
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 first:pl-8 last:pr-8 py-3 whitespace-nowrap">
                        <div class="text-left font-medium">{{ t('title') }}</div>
                    </th>
                    <th class="px-2 first:pl-8 last:pr-8 py-3 whitespace-nowrap w-px">
                        <div class="flex justify-center" :title="t('views')">
                            <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16">
                                <path d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"></path>
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 first:pl-8 last:pr-8 py-3 whitespace-nowrap">
                        <div class="text-center font-medium">{{ t('actions') }}</div>
                    </th>
                    <th class="px-2 first:pl-8 last:pr-8 py-3 whitespace-nowrap">
                        <div class="text-center font-medium">
                            <input type="checkbox" @change="toggleAll"/>
                        </div>
                    </th>
                </tr>
                </thead>
                <draggable
                    tag="tbody"
                    v-model="localRubrics"
                    @end="handleDragEnd"
                    itemKey="id"
                    handle=".handle"
                >
                    <template #item="{ element: rubric }">
                        <tr class="text-sm font-semibold border-b-2
                                   hover:bg-slate-100 dark:hover:bg-cyan-800">
                            <td class="px-2 py-1 text-center cursor-move handle">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-300"
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" />
                                </svg>
                            </td>
                            <td class="px-2 first:pl-8 last:pr-8 py-1 whitespace-nowrap">
                                <div class="text-center text-blue-600 dark:text-blue-200">
                                    {{ rubric.id }}
                                </div>
                            </td>
                            <td class="px-2 first:pl-8 last:pr-8 py-1 whitespace-nowrap">
                                <div class="text-center uppercase
                                            text-orange-500 dark:text-orange-200">
                                    {{ rubric.locale }}
                                </div>
                            </td>
                            <td class="first:pl-8 last:pr-8 py-1">
                                <div class="flex justify-center">
                                    <img
                                        :src="ownerAvatar(rubric)"
                                        :title="ownerTitle(rubric)"
                                        class="h-6 w-6 rounded-full object-cover
                                           border border-slate-300 dark:border-slate-600"
                                        alt="author"
                                    />
                                </div>
                            </td>
                            <td class="first:pl-8 last:pr-8 py-1">
                                <div class="flex justify-center">
                                    <template v-if="rubric.images && rubric.images.length">
                                        <img
                                            :src="getPrimaryImage(rubric).webp_url || getPrimaryImage(rubric).url"
                                            :alt="getPrimaryImage(rubric).alt || t('defaultImageAlt')"
                                            :title="getPrimaryImage(rubric).caption || t('postImage')"
                                            class="h-6 w-8 object-cover rounded-xs"
                                        >
                                    </template>
                                    <template v-else>
                                        <img
                                            src="/storage/rubric_images/default-image.png"
                                            :alt="t('defaultImageTitle')"
                                            class="h-6 w-8 object-cover rounded-xs"
                                        >
                                    </template>
                                </div>
                            </td>
                            <td class="px-2 first:pl-8 last:pr-8 py-1 whitespace-nowrap">
                                <div v-if="rubric.icon"
                                     class="flex justify-center items-center h-full"
                                     v-html="rubric.icon"></div>
                                <div v-else class="flex justify-center items-center
                                                   h-full text-slate-400">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M8 8a3 3 0 100-6 3 3 0 000 6zm2-3a2 2 0 11-4 0 2 2 0 014 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
                                    </svg>
                                </div>
                            </td>
                            <td class="px-2 first:pl-8 last:pr-8 py-1 whitespace-nowrap">
                                <div class="text-left flex justify-start items-center gap-1">
                                    <a :href="`/blog/rubrics/${encodeURIComponent(rubric.url)}`"
                                       class="text-sky-600 dark:text-sky-200 hover:underline
                                              hover:text-amber-700 dark:hover:text-amber-200"
                                       target="_blank" rel="noopener noreferrer">
                                        {{ rubric.title }}
                                    </a>
                                    <!-- Кол-во статей -->
                                    <span
                                        class="shrink-0 text-[10px] px-1.5 py-0.5 rounded-sm
                                               border border-slate-300 dark:border-slate-600
                                               bg-slate-100 dark:bg-slate-700
                                               text-slate-700 dark:text-slate-200"
                                        :title="`${t('articles')}: ${rubric.articles_count ?? 0}`"
                                    >
                                        {{ rubric.articles_count ?? 0 }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-2 first:pl-8 last:pr-8 py-1 whitespace-nowrap">
                                <div class="text-center">{{ rubric.views }}</div>
                            </td>
                            <td class="px-2 first:pl-8 last:pr-8 py-1 whitespace-nowrap">
                                <div class="flex justify-center space-x-2">
                                    <ActivityToggle :isActive="rubric.activity"
                                                    @toggle-activity="$emit('toggle-activity', rubric)"
                                                    :title="rubric.activity ? t('enabled') : t('disabled')" />
                                    <CloneIconButton @clone="$emit('clone', rubric)" />
                                    <IconEdit :href="route('admin.rubrics.edit', rubric.id)" />
                                    <DeleteIconButton @delete="$emit('delete', rubric.id)" />
                                </div>
                            </td>
                            <td class="px-2 first:pl-8 last:pr-8 py-1 whitespace-nowrap">
                                <div class="text-center">
                                    <input type="checkbox" :checked="selectedRubrics.includes(rubric.id)"
                                           @change="$emit('toggle-select', rubric.id)"/>
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
