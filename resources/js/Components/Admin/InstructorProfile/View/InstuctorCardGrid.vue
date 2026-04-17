<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import ActivityToggle from '@/Components/Admin/Buttons/ActivityToggle.vue'
import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'

const { t } = useI18n()

const props = defineProps({
    instructorProfiles: {
        type: Array,
        default: () => []
    },
    selectedInstructorProfiles: {
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

// --- Локальная копия для vuedraggable ---
const localInstructorProfiles = ref([])

watch(
    () => props.instructorProfiles,
    (newVal) => {
        localInstructorProfiles.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

// --- Drag end: отдаем массив ID вверх (как в InstructorProfileTable)
const handleDragEnd = () => {
    const newOrderIds = localInstructorProfiles.value.map(
        (instructorProfile) => instructorProfile.id
    )
    emits('update-sort-order', newOrderIds)
}

// --- Массовый выбор (как в InstructorProfileTable)
const toggleAll = (event) => {
    const checked = event.target.checked
    const ids = localInstructorProfiles.value.map(r => r.id)
    emits('toggle-all', { ids, checked })
}

// --- Главное изображение
const getPrimaryImage = (instructorProfile) => {
    if (instructorProfile.images && instructorProfile.images.length) {
        return [...instructorProfile.images].sort((a, b) => a.order - b.order)[0]
    }
    return null
}
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-400 dark:border-slate-500 relative">

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

        <div v-if="localInstructorProfiles.length" class="p-3">
            <draggable
                tag="div"
                v-model="localInstructorProfiles"
                item-key="id"
                @end="handleDragEnd"
                handle=".drag-handle"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4"
            >
                <template #item="{ element: instructorProfile }">
                    <div
                        class="relative flex flex-col h-full rounded-md
                               border border-slate-400 dark:border-slate-500
                               bg-slate-50/70 dark:bg-slate-800/80 shadow-sm
                               hover:shadow-md transition-shadow duration-150">

                        <!-- Верхняя панель карточки -->
                        <div class="flex items-center justify-between px-2 py-1
                                    border-b border-dashed border-slate-400 dark:border-slate-500">
                            <div class="flex items-center space-x-2">
                                <!-- drag handle (оставляем твой SVG) -->
                                <button
                                    type="button"
                                    class="drag-handle text-slate-400 hover:text-slate-700
                                           dark:hover:text-slate-100"
                                    :title="t('dragDrop')"
                                >
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 128 128">
                                        <path d="M10.4,119.2l44.1-19.4c2.2-1,3.6-3.1,3.6-5.5v-8.9c-2.8,2.1-6.2,3.3-9.8,3.3c-4.4,0-8.5-1.7-11.5-4.8L20.3,67.4l-0.2-0.2
                                            L19.9,67c-0.4-0.5-1.2-1.3-1.9-2.4c-0.8-1.3-1.5-2.8-1.9-4.4c-1.5-5.4,0-11.5,4.1-15.7l0,0c0.6-0.6,3.2-3.2,16.3-16.3
                                            c3.1-3.1,7.2-4.8,11.5-4.8c3.7,0,7.3,1.3,10.1,3.5V12.1c0-4.3-4.5-7.2-8.4-5.5L5.6,26.1C3.4,27,2,29.2,2,31.6v82.2
                                            C2,118.1,6.5,121,10.4,119.2z" />
                                        <path d="M91.6,28.2c14,14,15.9,15.9,16.3,16.3l0,0c4,4.2,5.6,10.2,4.1,15.7c-0.5,1.6-1.1,3.1-1.9,4.4c-0.7,1.1-1.5,2-1.9,2.4
                                            l-0.2,0.2l-0.2,0.2L91.2,83.9c-3.1,3.1-7.2,4.8-11.5,4.8c-3.6,0-7-1.2-9.8-3.3v8.9c0,2.4,1.4,4.5,3.6,5.5l44.1,19.4
                                            c4,1.7,8.4-1.2,8.4-5.5V31.6c0-2.4-1.4-4.5-3.6-5.5L78.3,6.6c-4-1.7-8.4,1.2-8.4,5.5V27c2.9-2.3,6.4-3.5,10.1-3.5
                                            C84.4,23.4,88.5,25.1,91.6,28.2z" />
                                        <path d="M100.6,51.4c0,0-16.1-16.1-16.1-16.1c-1.2-1.2-2.9-1.9-4.5-1.9s-3.2,0.6-4.5,1.9c-2.5,2.5-2.5,6.5,0,9l5.3,5.3H47.1l5.3-5.3
                                            c2.5-2.5,2.5-6.5,0-9c-1.2-1.2-2.9-1.9-4.5-1.9c-1.6,0-3.2,0.6-4.5,1.9c0,0-16.1,16.1-16.1,16.1c-2.5,2.5-2.2,6.4,0,9l16.5,16.5
                                            c1.2,1.2,2.9,1.9,4.5,1.9c1.6,0,3.2-0.6,4.5-1.9c2.5-2.5,2.5-6.5,0-9l-5.7-5.7h33.7l-5.7,5.7c-2.5,2.5-2.5,6.5,0,9
                                            c1.2,1.2,2.9,1.9,4.5,1.9c1.6,0,3.2-0.6,4.5-1.9c0,0,16.5-16.5,16.5-16.5C103.1,57.9,103,53.9,100.6,51.4z" />
                                    </svg>
                                </button>

                                <!-- ID как бейдж -->
                                <div
                                    class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm
                                           border border-gray-400
                                           bg-slate-200 dark:bg-slate-700
                                           text-slate-800 dark:text-blue-100"
                                    :title="`ID: ${instructorProfile.id}`">
                                    ID: {{ instructorProfile.id }}
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                <input
                                    type="checkbox"
                                    :checked="selectedInstructorProfiles.includes(instructorProfile.id)"
                                    @change="$emit('toggle-select', instructorProfile.id)"
                                />
                            </div>
                        </div>

                        <!-- Аватар -->
                        <div class="relative w-full h-32 flex items-center justify-center
                                    bg-slate-200 dark:bg-slate-900">
                            <template v-if="instructorProfile.images?.length">
                                <img
                                    :src="getPrimaryImage(instructorProfile).webp_url || getPrimaryImage(instructorProfile).url"
                                    :alt="getPrimaryImage(instructorProfile).alt || t('defaultImageAlt')"
                                    class="h-20 w-20 object-cover rounded-full border border-slate-300 dark:border-slate-500"
                                />
                            </template>
                            <template v-else>
                                <img
                                    src="/storage/instructor_profile_images/default-image.png"
                                    :alt="t('defaultImageTitle')"
                                    class="h-20 w-20 object-cover rounded-full border border-slate-300 dark:border-slate-500"
                                />
                            </template>
                        </div>

                        <!-- Контент -->
                        <div class="flex flex-col flex-1 px-3 py-2 space-y-1">

                            <!-- Заголовок / имя профиля -->
                            <a
                                :href="`/instructors/${encodeURIComponent(instructorProfile.slug)}`"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-sm font-semibold text-orange-600 dark:text-orange-200
                                       hover:underline line-clamp-2 text-center"
                                :title="instructorProfile.title"
                            >
                                {{ instructorProfile.title }}
                            </a>

                            <!-- Пользователь -->
                            <div class="font-semibold text-[13px]
                                        text-teal-700 dark:text-teal-200 text-center">
                                {{ instructorProfile.user?.name || '—' }}
                            </div>

                            <!-- Опыт -->
                            <div
                                class="flex justify-center items-center text-[11px]
                                       font-semibold text-blue-700 dark:text-blue-200 mt-1">
                                {{ t('experienceYears') }}: {{ instructorProfile.experience_years ?? '—' }}
                            </div>

                            <!-- Ставка -->
                            <div class="flex justify-center items-center mt-1">
                                <div
                                    v-if="Number(instructorProfile.hourly_rate) > 0"
                                    class="relative inline-flex text-center px-3 py-1
                                           rounded bg-emerald-500"
                                >
                                    <div
                                        class="absolute w-3 h-3 rounded-full bg-white
                                               left-0 -translate-x-1/2 top-1/2 -translate-y-1/2"
                                        aria-hidden="true"
                                    />
                                    <div
                                        class="absolute w-3 h-3 rounded-full bg-white
                                               right-0 translate-x-1/2 top-1/2 -translate-y-1/2"
                                        aria-hidden="true"
                                    />
                                    <span class="text-sm text-emerald-50 font-medium uppercase">
                                        {{ instructorProfile.hourly_rate }}
                                    </span>
                                </div>
                            </div>

                            <!-- Статистика -->
                            <div class="pt-2 flex flex-wrap justify-center gap-4
                                        font-semibold text-[11px] text-slate-900 dark:text-slate-200">
                                <div
                                    v-if="instructorProfile.views > 0"
                                    :title="t('views')"
                                    class="flex flex-row items-center"
                                >
                                    <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16">
                                        <path class="fill-current text-blue-600 dark:text-blue-300"
                                              d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"></path>
                                    </svg>
                                    <span class="ml-1">{{ instructorProfile.views ?? 0 }}</span>
                                </div>

                                <div
                                    v-if="instructorProfile.rating?.count > 0"
                                    :title="t('ratingCount')"
                                    class="flex flex-row items-center"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24" width="24"
                                         viewBox="0 0 24 24" class="shrink-0 h-4 w-4">
                                        <path class="fill-current text-red-400 dark:text-red-300"
                                              d="M14.586,9.439S15.7,2.858,11.138,0A8.055,8.055,0,0,1,8.1,5.831C6.149,7.546,2.481,11.4,2.52,15.51A9.435,9.435,0,0,0,7.7,24a5.975,5.975,0,0,1,2.091-4.132,4.877,4.877,0,0,0,1.869-3.278,8.786,8.786,0,0,1,4.652,7.322v.02a8.827,8.827,0,0,0,5.137-7.659c.324-3.863-1.792-9.112-3.668-10.828A10.192,10.192,0,0,1,14.586,9.439Z"></path>
                                    </svg>
                                    <span class="ml-1">{{ instructorProfile.rating.count }}</span>
                                </div>

                                <div
                                    v-if="instructorProfile.rating?.avg"
                                    :title="t('ratingAvg')"
                                    class="flex flex-row items-center"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24" width="24"
                                         viewBox="0 0 24 24" class="shrink-0 h-4 w-4">
                                        <path class="fill-current text-red-400 dark:text-red-300"
                                              d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"></path>
                                    </svg>
                                    <span class="ml-1">{{ instructorProfile.rating.avg }}</span>
                                </div>
                            </div>

                            <!-- Курсы -->
                            <div
                                v-if="instructorProfile.courses &&
                                    instructorProfile.courses.length"
                                class="flex justify-center flex-wrap gap-1 p-1
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

                        </div>

                        <!-- Действия -->
                        <div
                            class="flex items-center justify-center px-3 py-2
                                   border-t border-dashed border-slate-400 dark:border-slate-500">
                            <div class="flex items-center space-x-1">
                                <ActivityToggle
                                    :isActive="instructorProfile.activity"
                                    @toggle-activity="$emit('toggle-activity', instructorProfile)"
                                    :title="instructorProfile.activity ? t('enabled') : t('disabled')"
                                />
                                <IconEdit :href="route('admin.instructorProfiles.edit', instructorProfile.id)" />
                                <DeleteIconButton @delete="$emit('delete', instructorProfile.id)" />
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
