<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'
import ActivityToggle from '@/Components/Admin/Buttons/ActivityToggle.vue'
import CloneIconButton from '@/Components/Admin/Buttons/CloneIconButton.vue'

const { t } = useI18n()

const props = defineProps({
    schedules: {
        type: Array,
        default: () => []
    },
    selectedSchedules: {
        type: Array,
        default: () => []
    }
})

// 🔹 Мапы "значение из БД" → "ключ перевода" (flat i18n) для расписаний
const scheduleStatusLabelKeyMap = {
    draft: 'statusDraft',
    published: 'statusPublished',
    archived: 'statusArchived',
}

// 🔹 Хелпер
const getScheduleStatusLabel = (status) => {
    if (!status) return '—'

    const key = scheduleStatusLabelKeyMap[status]

    // Если есть ключ — переводим
    if (key) return t(key)

    // Нет ключа — не вызываем t(), просто возвращаем текст
    return status
}

// 🔹 Хелпер для онлайна (true/false → да/нет)
const getScheduleOnlineLabel = (isOnline) => {
    const normalized =
        isOnline === true ||
        isOnline === 1 ||
        isOnline === '1' ||
        isOnline === 'true'

    return t(normalized ? 'online' : 'offline')
}

const emits = defineEmits([
    'toggle-activity',
    'edit',
    'delete',
    'update-sort-order',
    'toggle-select',
    'toggle-all',
    'clone'
])

/** Локальная копия для vuedraggable */
const localSchedules = ref([])

watch(
    () => props.schedules,
    (newVal) => {
        localSchedules.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

const handleDragEnd = () => {
    const newOrderIds = localSchedules.value.map(schedule => schedule.id)
    emits('update-sort-order', newOrderIds)
}

/** Массовые чекбоксы */
const toggleAll = (event) => {
    const checked = event.target.checked
    const ids = localSchedules.value.map(l => l.id)
    emits('toggle-all', { ids, checked })
}

/** Основное изображение курса (минимальный order) */
const getPrimaryImage = (schedule) => {
    if (schedule.images && schedule.images.length) {
        return [...schedule.images].sort((a, b) => a.order - b.order)[0]
    }
    return null
}

// Функция форматирования даты
const formatDate = (dateStr) => {
    if (!dateStr) return ''
    const d = new Date(dateStr)
    if (isNaN(d)) return ''
    return d.toLocaleDateString('ru-RU', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-200 dark:border-slate-600 relative">

        <!-- Верхняя панель: кол-во выбранных + чекбокс -->
        <div class="flex items-center justify-between px-3 py-2
                    border-b border-slate-400 dark:border-slate-500">
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('selected') }}: {{ selectedSchedules.length }}
            </div>

            <label
                v-if="localSchedules.length"
                class="flex items-center text-xs text-slate-600
                       dark:text-slate-200 cursor-pointer">
                <span>{{ t('selectAll') }}</span>
                <input type="checkbox" class="mx-2" @change="toggleAll" />
            </label>
        </div>

        <div class="overflow-x-auto">
            <table
                v-if="schedules.length > 0"
                class="table-auto w-full text-slate-700 dark:text-slate-100">
                <thead
                    class="text-sm uppercase bg-slate-200 dark:bg-cyan-900
                           border border-solid border-gray-300 dark:border-gray-700">
                <tr>
                    <th class="px-2 py-3 w-px">
                        <!-- drag handle header -->
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-4 h-4 fill-current text-slate-800 dark:text-slate-200"
                            height="24"
                            width="24"
                            viewBox="0 0 24 24">
                            <path
                                d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"
                            />
                            <path
                                d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"
                            />
                        </svg>
                    </th>
                    <th class="px-2 py-3 w-px">
                        <div class="font-medium text-center">{{ t('id') }}</div>
                    </th>
                    <th class="px-2 py-3 first:pl-12 last:pr-12 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('image')">
                            <svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 512 512">
                                <path
                                    d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l96 0 32 0 208 0c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"
                                />
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-12 last:pr-12 whitespace-nowrap">
                        <div class="font-semibold text-center">
                            {{ t('title') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-12 last:pr-12 whitespace-nowrap">
                        <div class="font-semibold text-center">
                            {{ t('course') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-12 last:pr-12 whitespace-nowrap">
                        <div class="font-semibold text-center">
                            {{ t('instructor') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-12 last:pr-12 whitespace-nowrap">
                        <div class="flex justify-center"
                             :title="t('online')">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-4 h-4"
                                height="24"
                                width="24"
                                viewBox="0 0 24 24">
                                <circle class="fill-current text-blue-600 dark:text-blue-400"
                                        cx="11" cy="9" r="7"></circle>
                                <path class="fill-current text-blue-600 dark:text-blue-400"
                                      d="M17.602,0.21c-0.442-0.331-1.068-0.243-1.4,0.199c-0.332,0.442-0.242,1.068,0.199,1.4 C18.688,3.527,20,6.148,20,9c0,4.962-4.038,9-9,9c-2.852,0-5.473-1.312-7.19-3.6c-0.332-0.441-0.958-0.53-1.4-0.199 c-0.441,0.332-0.531,0.958-0.199,1.4c1.897,2.525,4.697,4.065,7.79,4.341V22H6c-0.552,0-1,0.448-1,1s0.448,1,1,1h10 c0.552,0,1-0.448,1-1s-0.448-1-1-1h-4v-2.051C17.598,19.442,22,14.728,22,9C22,5.513,20.397,2.309,17.602,0.21z"></path>
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-12 last:pr-12 whitespace-nowrap">
                        <div class="flex justify-center"
                             :title="`${t('scheduleStartsAt')} / ${t('scheduleEndsAt')}`">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-4 h-4"
                                height="24"
                                width="24"
                                viewBox="0 0 24 24">
                                <path class="fill-current text-sky-600 dark:text-sky-400"
                                      d="M23,3H18V1a1,1,0,0,0-2,0V3H8V1A1,1,0,0,0,6,1V3H1A1,1,0,0,0,0,4V22a1,1,0,0,0,1,1H23a1,1,0,0,0,1-1V4A1,1,0,0,0,23,3ZM22,21H2V7H22Z"></path>
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-12 last:pr-12 whitespace-nowrap">
                        <div class="flex justify-center"
                         :title="`${t('scheduleEnrollStartsAt')} / ${t('scheduleEnrollEndsAt')}`">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-4 h-4"
                                height="24"
                                width="24"
                                viewBox="0 0 24 24">
                                <path class="fill-current text-blue-700 dark:text-blue-300"
                                      d="M22,13a1,1,0,0,1,0-2h1.949A12.006,12.006,0,0,0,13,.051V2a1,1,0,0,1-2,0V.051A12.006,12.006,0,0,0,.051,11H2a1,1,0,0,1,0,2H.051A12.006,12.006,0,0,0,11,23.949V22a1,1,0,0,1,2,0v1.949A12.006,12.006,0,0,0,23.949,13Zm-6,0H12a1,1,0,0,1-.832-.445l-4-6a1,1,0,1,1,1.664-1.11L12.535,11H16a1,1,0,0,1,0,2Z"></path>
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-12 last:pr-12 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('status')">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-4 h-4"
                                height="24"
                                width="24"
                                viewBox="0 0 24 24">
                                <path class="fill-current text-fuchsia-800 dark:text-fuchsia-400"
                                      d="M22.707,6.707,17.293,1.293A1,1,0,0,0,16.586,1H4A3,3,0,0,0,1,4V20a3,3,0,0,0,3,3H20a3,3,0,0,0,3-3V7.414A1,1,0,0,0,22.707,6.707ZM14.5,4h1a.5.5,0,0,1,.5.5v4a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-4A.5.5,0,0,1,14.5,4ZM19,12.5v6a.5.5,0,0,1-.5.5H5.5a.5.5,0,0,1-.5-.5v-6a.5.5,0,0,1,.5-.5h13A.5.5,0,0,1,19,12.5Z"></path>
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-12 last:pr-12 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('capacity')">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-4 h-4"
                                height="24"
                                width="24"
                                viewBox="0 0 24 24">
                                <circle class="fill-current text-amber-600 dark:text-amber-400"
                                        cx="12" cy="3" r="3"></circle>
                                <path class="fill-current text-amber-600 dark:text-amber-400"
                                      d="M22,7H2c-.552,0-1,.448-1,1h0c0,.552,.448,1,1,1h7v14.263c0,.407,.33,.737,.737,.737h.525c.407,0,.737-.33,.737-.737v-7.263h2v7.263c0,.407,.33,.737,.737,.737h.525c.407,0,.737-.33,.737-.737V9h7c.552,0,1-.448,1-1h0c0-.552-.448-1-1-1Z"></path>
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-12 last:pr-12 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('views')">
                            <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16">
                                <path class="fill-current text-blue-600 dark:text-blue-300"
                                      d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"
                                />
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-12 last:pr-12 whitespace-nowrap">
                        <div class="font-semibold text-end">
                            {{ t('actions') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-12 last:pr-12 whitespace-nowrap">
                        <div class="text-center">
                            <input type="checkbox" @change="toggleAll" />
                        </div>
                    </th>
                </tr>
                </thead>

                <draggable
                    tag="tbody"
                    v-model="localSchedules"
                    @end="handleDragEnd"
                    item-key="id"
                    handle=".handle"
                >
                    <template #item="{ element: schedule }">
                        <tr
                            class="text-sm font-semibold border-b-2
                                   hover:bg-slate-100 dark:hover:bg-cyan-800">

                            <td class="px-2 py-1 text-center cursor-move handle">
                                <svg
                                    class="w-4 h-4 text-gray-500 dark:text-gray-300"
                                    fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path
                                        d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"
                                    />
                                </svg>
                            </td>
                            <td class="px-2 py-3 first:pl-12 last:pr-12 whitespace-nowrap">
                                <div
                                    class="text-center text-xs
                                           text-slate-800 dark:text-blue-200"
                                    :title="`[${schedule.locale}] : [${schedule.sort}]`">
                                    {{ schedule.id }}
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-12 last:pr-12 whitespace-nowrap">
                                <div class="flex justify-center">
                                    <template
                                        v-if="schedule.images && schedule.images.length">
                                        <img
                                            :src="getPrimaryImage(schedule).webp_url ||
                                            getPrimaryImage(schedule).url"
                                            :alt="getPrimaryImage(schedule).alt ||
                                                t('defaultImageAlt')"
                                            :title="
                                                getPrimaryImage(schedule).caption ||
                                                t('scheduleImage')"
                                            class="h-6 w-8 object-cover rounded-xs"
                                        />
                                    </template>
                                    <template v-else>
                                        <img
                                            src="/storage/course_schedule_images/default-image.png"
                                            :alt="t('defaultImageTitle')"
                                            class="h-6 w-8 object-cover rounded-xs"
                                        />
                                    </template>
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-12 last:pr-12">
                                <a
                                    :href="`/schedules/${encodeURIComponent(schedule.slug)}`"
                                    class="text-xs text-amber-600 dark:text-amber-200 hover:underline
                                           hover:text-red-600 dark:hover:text-red-300"
                                    target="_blank"
                                    rel="noopener noreferrer" :title="schedule.subtitle">
                                    {{ schedule.title }}
                                </a>
                            </td>
                            <td class="px-2 py-3 first:pl-12 last:pr-12">
                                <div class="text-center text-xs
                                            text-blue-700 dark:text-blue-300">
                                    {{ schedule.course.title }}
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-12 last:pr-12">
                                <div class="text-center text-xs">
                                    <template v-if="schedule.instructor.user">
                            <span class="text-slate-700 dark:text-slate-300">
                                {{ schedule.instructor.user.name || schedule.instructor.user.email }}
                                <span v-if="schedule.instructor.title">:</span>
                            </span>
                                        <br v-if="schedule.instructor.title">
                                <span v-if="schedule.instructor.title"
                                      class="text-teal-600 dark:text-teal-300">
                                    {{ schedule.instructor.title }}
                                </span>
                                    </template>
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-12 last:pr-12 whitespace-nowrap">
                                <div
                                    class="text-center text-xs
                                           text-orange-700 dark:text-orange-300"
                                    :title="t('online')"
                                >
                                    {{ getScheduleOnlineLabel(schedule.is_online) }}
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-12 last:pr-12">
                                <div class="text-center text-xs">
                                    <span v-if="schedule.starts_at"
                                          class="text-sky-600 dark:text-sky-400">
                                        {{ formatDate(schedule.starts_at) }}
                                    </span>
                                    / <br v-if="schedule.ends_at">
                                    <span v-if="schedule.ends_at"
                                          class="text-red-400 dark:text-red-200">
                                        {{ formatDate(schedule.ends_at) }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-12 last:pr-12">
                                <div class="text-center text-xs">
                                    <span v-if="schedule.enroll_starts_at"
                                          class="text-blue-700 dark:text-blue-300">
                                        {{ formatDate(schedule.enroll_starts_at) }}
                                    </span>
                                    / <br v-if="schedule.enroll_ends_at">
                                    <span v-if="schedule.enroll_ends_at"
                                          class="text-rose-600 dark:text-rose-400">
                                        {{ formatDate(schedule.enroll_ends_at) }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-12 last:pr-12 whitespace-nowrap">
                                <div
                                    class="text-center text-xs
                                           text-fuchsia-800 dark:text-fuchsia-400"
                            :title="`${t('status')} : ${getScheduleStatusLabel(schedule.status)}`"
                                >
                                    {{ getScheduleStatusLabel(schedule.status) }}
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-12 last:pr-12 whitespace-nowrap">
                                <div class="text-center text-xs
                                            text-amber-600 dark:text-amber-400">
                                    {{ schedule.capacity }}
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-12 last:pr-12 whitespace-nowrap">
                                <div class="text-center text-xs
                                            text-blue-700 dark:text-blue-300">
                                    {{ schedule.views }}
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-12 last:pr-12 whitespace-nowrap">
                                <div class="flex justify-end space-x-2">
                                    <ActivityToggle
                                        :isActive="schedule.activity"
                                        @toggle-activity="$emit('toggle-activity', schedule)"
                                        :title="schedule.activity ? t('enabled') : t('disabled')"
                                    />
                                    <CloneIconButton @clone="$emit('clone', schedule)" />
                                    <IconEdit
                                        :href="route('admin.courseSchedules.edit', schedule.id)"
                                    />
                                    <DeleteIconButton
                                        @delete="$emit('delete', schedule.id, schedule.title)"
                                    />
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-12 last:pr-12 whitespace-nowrap">
                                <div class="text-center">
                                    <input
                                        type="checkbox"
                                        :checked="selectedSchedules.includes(schedule.id)"
                                        @change="$emit('toggle-select', schedule.id)"
                                    />
                                </div>
                            </td>
                        </tr>
                    </template>
                </draggable>
            </table>

            <div
                v-else
                class="p-5 text-center text-slate-700 dark:text-slate-100">
                {{ t('noData') }}
            </div>
        </div>
    </div>
</template>
