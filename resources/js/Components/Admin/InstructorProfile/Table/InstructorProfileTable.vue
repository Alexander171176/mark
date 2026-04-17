<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable';
import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'
import ActivityToggle from '@/Components/Admin/Buttons/ActivityToggle.vue'

const { t } = useI18n()

const props = defineProps({
    instructorProfiles: {
        type: Array,
        default: () => [],
    },
    selectedInstructorProfiles: {
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
const localInstructorProfiles = ref([]);

watch(
    () => props.instructorProfiles,
    (newVal) => {
        localInstructorProfiles.value = JSON.parse(JSON.stringify(newVal || []));
    },
    { immediate: true, deep: true }
);

// --- Функция, вызываемая vuedraggable после завершения перетаскивания ---
const handleDragEnd = () => {
    const newOrderIds = localInstructorProfiles.value.map(
        (instructorProfile) => instructorProfile.id
    );
    emits('update-sort-order', newOrderIds);
};

// --- Логика массовых действий ---
const toggleAll = (event) => {
    const checked = event.target.checked;
    const ids = localInstructorProfiles.value.map(r => r.id);
    emits('toggle-all', { ids, checked });
};

// Функция для выбора изображения с наименьшим значением order
const getPrimaryImage = (instructorProfile) => {
    if (instructorProfile.images && instructorProfile.images.length) {
        // Создаем копию массива и сортируем по возрастанию order
        return [...instructorProfile.images].sort((a, b) => a.order - b.order)[0];
    }
    return null;
};

</script>

<template>
    <div class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
                border border-slate-200 dark:border-slate-600 relative">

        <!-- Верхняя панель: кол-во выбранных + чекбокс -->
        <div class="flex items-center justify-between px-3 py-2
                    border-b border-slate-400 dark:border-slate-500">
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('selected') }}: {{ selectedInstructorProfiles.length }}
            </div>

            <label
                v-if="localInstructorProfiles.length"
                class="flex items-center text-xs text-slate-600
                       dark:text-slate-200 cursor-pointer">
                <span>{{ t('selectAll') }}</span>
                <input type="checkbox" class="mx-2" @change="toggleAll" />
            </label>
        </div>

        <div class="overflow-x-auto">
            <table v-if="instructorProfiles.length > 0"
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
                    <th class="px-2 first:pl-11 last:pr-11 py-3 whitespace-nowrap w-px">
                        <div class="font-medium text-center">{{ t('id') }}</div>
                    </th>
                    <th class="px-2 first:pl-11 last:pr-11 py-3 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('image')">
                            <svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 512 512">
                                <path d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l96 0 32 0 208 0c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"/>
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 first:pl-11 last:pr-11 py-3 whitespace-nowrap">
                        <div class="font-semibold text-left">{{ t('instructor') }}</div>
                    </th>
                    <th class="px-2 first:pl-11 last:pr-11 py-3 whitespace-nowrap">
                        <div class="flex justify-center items-center font-semibold text-left">
                            {{ t('experienceDesc') }}
                        </div>
                    </th>
                    <th class="px-2 first:pl-11 last:pr-11 py-3 whitespace-nowrap">
                        <div class="font-medium text-center">
                            {{ t('courses') }}
                        </div>
                    </th>
                    <th class="px-2 first:pl-11 last:pr-11 py-3 whitespace-nowrap">
                        <div class="flex justify-center items-center" :title="t('hourlyRate')">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24" width="24"
                                 viewBox="0 0 24 24" class="shrink-0 h-6 w-6">
                                <path class="fill-current text-green-600 dark:text-green-200"
                                      d="M0,5V19H24V5ZM7,9.749a.492.492,0,0,1-.19.392,7.537,7.537,0,0,0-2.795,5.366A.517.517,0,0,1,3.507,16c-.63,0-.992.005-.992.005a.506.506,0,0,1-.5-.52A7.27,7.27,0,0,1,4.431,10H1.5A.5.5,0,0,1,1,9.5v-1A.5.5,0,0,1,1.5,8h5a.5.5,0,0,1,.5.5Zm8,0a.492.492,0,0,1-.19.392,7.537,7.537,0,0,0-2.795,5.366.517.517,0,0,1-.508.488c-.63,0-.992.005-.992.005a.506.506,0,0,1-.5-.52A7.27,7.27,0,0,1,12.431,10H9.5A.5.5,0,0,1,9,9.5v-1A.5.5,0,0,1,9.5,8h5a.5.5,0,0,1,.5.5Zm8,0a.492.492,0,0,1-.19.392,7.537,7.537,0,0,0-2.795,5.366.517.517,0,0,1-.508.488c-.63,0-.992.005-.992.005a.506.506,0,0,1-.5-.52A7.27,7.27,0,0,1,20.431,10H17.5a.5.5,0,0,1-.5-.5v-1a.5.5,0,0,1,.5-.5h5a.5.5,0,0,1,.5.5Z"></path>
                                <path class="fill-current text-green-600 dark:text-green-200"
                                      d="M1,1H23a1,1,0,0,1,1,1V3a0,0,0,0,1,0,0H0A0,0,0,0,1,0,3V2A1,1,0,0,1,1,1Z"></path>
                                <path class="fill-current text-green-600 dark:text-green-200"
                                      d="M0,21H24a0,0,0,0,1,0,0v1a1,1,0,0,1-1,1H1a1,1,0,0,1-1-1V21A0,0,0,0,1,0,21Z"></path>
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 first:pl-11 last:pr-11 py-3 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('views')">
                            <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16">
                                <path class="fill-current text-blue-600 dark:text-blue-300"
                                    d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"></path>
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 first:pl-11 last:pr-11 py-3 whitespace-nowrap">
                        <div :title="t('ratingCount')">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24" width="24"
                                 viewBox="0 0 24 24" class="shrink-0 h-4 w-4">
                                <path class="fill-current text-red-400 dark:text-red-300"
                                      d="M14.586,9.439S15.7,2.858,11.138,0A8.055,8.055,0,0,1,8.1,5.831C6.149,7.546,2.481,11.4,2.52,15.51A9.435,9.435,0,0,0,7.7,24a5.975,5.975,0,0,1,2.091-4.132,4.877,4.877,0,0,0,1.869-3.278,8.786,8.786,0,0,1,4.652,7.322v.02a8.827,8.827,0,0,0,5.137-7.659c.324-3.863-1.792-9.112-3.668-10.828A10.192,10.192,0,0,1,14.586,9.439Z"></path>
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 first:pl-11 last:pr-11 py-3 whitespace-nowrap">
                        <div :title="t('ratingAvg')">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24" width="24"
                                 viewBox="0 0 24 24" class="shrink-0 h-4 w-4">
                                <path class="fill-current text-red-400 dark:text-red-300"
                                      d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"></path>
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 first:pl-11 last:pr-11 py-3 whitespace-nowrap">
                        <div class="font-semibold text-end">{{ t('actions') }}</div>
                    </th>
                    <th class="px-2 first:pl-11 last:pr-11 py-3 whitespace-nowrap">
                        <div class="text-center">
                            <input type="checkbox" @change="toggleAll"/>
                        </div>
                    </th>
                </tr>
                </thead>
                <draggable
                    tag="tbody"
                    v-model="localInstructorProfiles"
                    @end="handleDragEnd"
                    item-key="id"
                    handle=".handle"
                >
                <template #item="{ element: instructorProfile }">
                        <tr class="text-sm font-semibold border-b-2
                                   hover:bg-slate-100 dark:hover:bg-cyan-800">
                            <td class="px-2 py-1 text-center cursor-move handle">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-300"
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" />
                                </svg>
                            </td>
                            <td class="px-2 first:pl-11 last:pr-11 py-1 whitespace-nowrap">
                                <div class="text-center text-slate-800 dark:text-blue-200">
                                    {{ instructorProfile.id }}
                                </div>
                            </td>
                            <td class="first:pl-11 last:pr-11 py-1">
                                <div class="flex justify-center">
                                    <template v-if="instructorProfile.images && instructorProfile.images.length">
                                        <img
                                            :src="getPrimaryImage(instructorProfile).webp_url || getPrimaryImage(instructorProfile).url"
                                            :alt="getPrimaryImage(instructorProfile).alt || t('defaultImageAlt')"
                                            :title="getPrimaryImage(instructorProfile).caption || t('postImage')"
                                            class="h-16 w-auto object-cover rounded-sm"
                                        >
                                    </template>
                                    <template v-else>
                                        <img
                                            src="/storage/instructor_profile_images/default-image.png"
                                            :alt="t('defaultImageTitle')"
                                            class="h-16 w-auto object-cover rounded-sm"
                                        >
                                    </template>
                                </div>
                            </td>
                            <td class="px-2 first:pl-11 last:pr-11 py-1 whitespace-nowrap
                                       align-middle">
                                <div class="flex flex-col items-center justify-center gap-1">
                                    <a :href="`/school/instructors/${encodeURIComponent(instructorProfile.slug)}`"
                                       class="text-orange-600 dark:text-orange-200 hover:underline
                                              hover:text-orange-600 dark:hover:text-orange-200"
                                       target="_blank" rel="noopener noreferrer">
                                        {{ instructorProfile.title }}
                                    </a>
                                    <div class="text-center text-teal-600 dark:text-teal-200"
                                         :title="instructorProfile.user?.id">
                                        {{ instructorProfile.user?.name || '—' }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-2 first:pl-11 last:pr-11 py-1 whitespace-nowrap">
                                <div class="flex justify-center items-center
                                    text-left uppercase text-violet-600 dark:text-violet-200">
                                    {{ instructorProfile.experience_years }}
                                </div>
                            </td>
                            <td class="px-2 first:pl-11 last:pr-11 py-1">
                                <div class="text-center text-sky-700 dark:text-sky-200"
                                     :title="t('quantity')">
                                    [{{ instructorProfile.courses_count ?? 0 }}]
                                </div>
                                <div
                                    v-if="instructorProfile.courses &&
                                    instructorProfile.courses.length"
                                    class="flex justify-start flex-wrap gap-1 p-1
                                      border border-dashed border-slate-400 dark:border-slate-500"
                                >
                                    <a
                                        v-for="course in instructorProfile.courses"
                                        :key="course.id"
                                        :href="`/school/courses/${encodeURIComponent(course.slug)}`"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-flex items-center rounded-sm bg-sky-100
                                               px-2 py-0.5 text-[9px] font-semibold
                                               border border-slate-400
                                               text-blue-700 hover:bg-sky-200
                                               dark:bg-sky-900 dark:text-blue-300
                                               dark:hover:bg-sky-800"
                                        :title="course.title"
                                    >
                                        <span :title="`ID: ${course.id}`">{{ course.title }}</span>
                                    </a>
                                </div>
                                <div
                                    v-else
                                    class="text-slate-400 dark:text-slate-500"
                                >
                                    —
                                </div>
                            </td>
                            <td class="px-2 first:pl-11 last:pr-11 py-1 whitespace-nowrap">
                                <div class="flex justify-center items-center">
                                    <div v-if="Number(instructorProfile.hourly_rate) > 0"
                                         class="relative inline-flex text-center px-3 py-1 rounded bg-emerald-500">
                                        <div class="absolute w-3 h-3 rounded-full bg-white
                            left-0 -translate-x-1/2 top-1/2 -translate-y-1/2" aria-hidden="true">
                                        </div>
                                        <div class="absolute w-3 h-3 rounded-full bg-white
                            right-0 translate-x-1/2 top-1/2 -translate-y-1/2" aria-hidden="true">
                                        </div>
                                        <span class="text-sm text-emerald-50 font-medium uppercase">
                                {{ instructorProfile.hourly_rate }}
                            </span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-2 first:pl-11 last:pr-11 py-1 whitespace-nowrap">
                                <div class="text-center">{{ instructorProfile.views }}</div>
                            </td>
                            <td class="px-2 first:pl-11 last:pr-11 py-1 whitespace-nowrap">
                                <div v-if="Number(instructorProfile.rating.count) > 0"
                                     class="text-left uppercase text-slate-700 dark:text-slate-200">
                                    {{ instructorProfile.rating.count }}
                                </div>
                            </td>
                            <td class="px-2 first:pl-11 last:pr-11 py-1 whitespace-nowrap">
                                <div class="text-left uppercase text-rose-500 dark:text-rose-300">
                                    {{ instructorProfile.rating.avg }}
                                </div>
                            </td>
                            <td class="px-2 first:pl-11 last:pr-11 py-1 whitespace-nowrap">
                                <div class="flex justify-end space-x-2">
                                    <ActivityToggle :isActive="instructorProfile.activity"
                                                    @toggle-activity="$emit('toggle-activity', instructorProfile)"
                                                    :title="instructorProfile.activity ? t('enabled') : t('disabled')"/>
                                    <IconEdit :href="route('admin.instructorProfiles.edit', instructorProfile.id)" />
                                    <DeleteIconButton @delete="$emit('delete', instructorProfile.id)" />
                                </div>
                            </td>
                            <td class="px-2 first:pl-11 last:pr-11 py-1 whitespace-nowrap">
                                <div class="text-center">
                                    <div class="text-center">
                                        <input type="checkbox"
                                               :checked="selectedInstructorProfiles.includes(instructorProfile.id)"
                                               @change="$emit('toggle-select', instructorProfile.id)"/>
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
