<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'
import ActivityToggle from '@/Components/Admin/UI/Buttons/ActivityToggle.vue'
import CloneIconButton from '@/Components/Admin/UI/Buttons/CloneIconButton.vue'

const { t } = useI18n()

const props = defineProps({
    schedules: {
        type: Array,
        default: () => [],
    },
    selectedSchedules: {
        type: Array,
        default: () => [],
    },
})

const emit = defineEmits([
    'toggle-activity',
    'delete',
    'update-sort-order',
    'toggle-select',
    'toggle-all',
    'clone',
])

const scheduleStatusLabelKeyMap = {
    draft: 'statusDraft',
    published: 'statusPublished',
    archived: 'statusArchived',
    cancelled: 'statusCancelled',
}

const getScheduleStatusLabel = (status) => {
    if (!status) return '—'

    const key = scheduleStatusLabelKeyMap[status]

    return key ? t(key) : status
}

const getScheduleOnlineLabel = (isOnline) => {
    const normalized =
        isOnline === true ||
        isOnline === 1 ||
        isOnline === '1' ||
        isOnline === 'true'

    return t(normalized ? 'online' : 'offline')
}

const localSchedules = ref([])

watch(
    () => props.schedules,
    (newVal) => {
        localSchedules.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

const handleDragEnd = () => {
    emit('update-sort-order', localSchedules.value.map(schedule => schedule.id))
}

const toggleAll = (event) => {
    emit('toggle-all', {
        ids: localSchedules.value.map(schedule => schedule.id),
        checked: event.target.checked,
    })
}

const getPrimaryImage = (schedule) => {
    if (schedule.images && schedule.images.length) {
        return [...schedule.images].sort((a, b) => (a.order ?? 0) - (b.order ?? 0))[0]
    }

    return null
}

const formatDate = (dateStr) => {
    if (!dateStr) return ''

    const d = new Date(dateStr)

    if (isNaN(d)) return ''

    return d.toLocaleDateString('ru-RU', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    })
}

const getInstructorUserLabel = (schedule) => {
    return schedule.instructor?.user?.name ||
        schedule.instructor?.user?.email ||
        ''
}

const getInstructorTitle = (schedule) => {
    return schedule.instructor?.title || ''
}
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-200 dark:border-slate-600 relative"
    >
        <div
            class="flex items-center justify-between px-3 py-2
                   border-b border-slate-400 dark:border-slate-500"
        >
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('selected') }}: {{ selectedSchedules.length }}
            </div>

            <label
                v-if="localSchedules.length"
                class="flex items-center text-xs text-slate-600
                       dark:text-slate-200 cursor-pointer"
            >
                <span>{{ t('selectAll') }}</span>
                <input type="checkbox" class="mx-2" @change="toggleAll">
            </label>
        </div>

        <div class="overflow-x-auto">
            <table
                v-if="localSchedules.length > 0"
                class="table-auto w-full text-slate-700 dark:text-slate-100"
            >
                <thead
                    class="text-sm uppercase bg-slate-200 dark:bg-cyan-900
                           border border-solid border-gray-300 dark:border-gray-700"
                >
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
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('image')">
                            <svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 512 512">
                                <path
                                    d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l96 0 32 0 208 0c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"
                                />
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-left">{{ t('title') }}</div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-center">
                            {{ t('course') }} / {{ t('instructor') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="flex items-center justify-center"
                             :title="t('scheduleStartsAt')">
                            <svg class="w-4 h-4 fill-current shrink-0"
                                 viewBox="0 0 448 512">
                                <path
                                    d="M148 288h-40c-6.6 0-12-5.4-12-12v-40c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12zm108-12v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm96 0v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm-96 96v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm-96 0v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm192 0v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm96-260v352c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V112c0-26.5 21.5-48 48-48h48V12c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v52h128V12c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v52h48c26.5 0 48 21.5 48 48zm-48 346V160H48v298c0 3.3 2.7 6 6 6h340c3.3 0 6-2.7 6-6z" />
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="flex items-center justify-center"
                             :title="t('scheduleEnrollStartsAt')">
                            <svg class="w-4 h-4 fill-current shrink-0"
                                 viewBox="0 0 448 512">
                                <path
                                    d="M400 64h-48V12c0-6.627-5.373-12-12-12h-40c-6.627 0-12 5.373-12 12v52H160V12c0-6.627-5.373-12-12-12h-40c-6.627 0-12 5.373-12 12v52H48C21.49 64 0 85.49 0 112v352c0 26.51 21.49 48 48 48h352c26.51 0 48-21.49 48-48V112c0-26.51-21.49-48-48-48zm-6 400H54a6 6 0 0 1-6-6V160h352v298a6 6 0 0 1-6 6zm-52.849-200.65L198.842 404.519c-4.705 4.667-12.303 4.637-16.971-.068l-75.091-75.699c-4.667-4.705-4.637-12.303.068-16.971l22.719-22.536c4.705-4.667 12.303-4.637 16.97.069l44.104 44.461 111.072-110.181c4.705-4.667 12.303-4.637 16.971.068l22.536 22.718c4.667 4.705 4.636 12.303-.069 16.97z" />
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-center">{{ t('status') }}</div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('views')">
                            <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16">
                                <path class="fill-current text-blue-600 dark:text-blue-300"
                                      d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"
                                />
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-end">{{ t('actions') }}</div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="text-center">
                            <input type="checkbox" @change="toggleAll">
                        </div>
                    </th>
                </tr>
                </thead>

                <draggable
                    v-model="localSchedules"
                    tag="tbody"
                    item-key="id"
                    handle=".handle"
                    @end="handleDragEnd"
                >
                    <template #item="{ element: schedule }">
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
                                    <path
                                        d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"
                                    />
                                </svg>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div
                                    class="text-center text-xs text-slate-800 dark:text-blue-200"
                                    :title="`[sort: ${schedule.sort}]`"
                                >
                                    {{ schedule.id }}
                                </div>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="flex justify-center">
                                <img
                                    v-if="schedule.images && schedule.images.length"
                                    :src="getPrimaryImage(schedule)?.webp_url || getPrimaryImage(schedule)?.url"
                                    :alt="getPrimaryImage(schedule)?.alt || t('defaultImageAlt')"
                                    :title="getPrimaryImage(schedule)?.caption || t('scheduleImage')"
                                    class="h-8 w-12 object-cover rounded-sm"
                                >
                        <img
                            v-else
                            src="/storage/school/school_course_schedule_images/default-image.png"
                            :alt="t('defaultImageTitle')"
                            class="h-8 w-12 object-cover rounded-sm"
                        >
                                </div>
                            </td>
                            <td class="px-2 py-3">
                                <a
                                    :href="`/school/schedules/${encodeURIComponent(schedule.slug)}`"
                                    class="text-xs text-amber-600
                                           dark:text-amber-200 hover:underline
                                           hover:text-red-600 dark:hover:text-red-300"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    :title="schedule.subtitle || schedule.short"
                                >
                                    {{ schedule.title || `ID: ${schedule.id}` }}
                                </a>
                                <div class="text-xs text-slate-500 dark:text-slate-300 truncate"
                                     :title="schedule.slug">
                                    {{ schedule.slug }}
                                </div>
                                <div
                                    v-if="schedule.location"
                                    class="text-[10px] text-teal-700 dark:text-teal-300 truncate"
                                    :title="schedule.location"
                                >
                                    {{ schedule.location }}
                                </div>
                            </td>
                            <td class="px-2 py-3">
                                <div
                                    class="text-center text-xs text-blue-700 dark:text-blue-300"
                                    :title="schedule.course?.title || ''"
                                >
                                    {{ schedule.course?.title || '—' }}
                                </div>
                                <div class="text-center text-xs">
                                    <span class="text-slate-700 dark:text-slate-300">
                                        {{ getInstructorUserLabel(schedule) || '—' }}
                                        <span v-if="getInstructorTitle(schedule)">:</span>
                                    </span>

                                    <br v-if="getInstructorTitle(schedule)">

                                    <span
                                        v-if="getInstructorTitle(schedule)"
                                        class="text-teal-600 dark:text-teal-300"
                                    >
                                        {{ getInstructorTitle(schedule) }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-2 py-3">
                                <div class="text-center text-[10px]">
                                    <span
                                        v-if="schedule.starts_at"
                                        class="text-sky-600 dark:text-sky-400"
                                    >
                                        {{ formatDate(schedule.starts_at) }}
                                    </span>
                                    <span v-if="schedule.starts_at || schedule.ends_at"> / </span>
                                    <br v-if="schedule.ends_at">
                                    <span
                                        v-if="schedule.ends_at"
                                        class="text-red-400 dark:text-red-200"
                                    >
                                        {{ formatDate(schedule.ends_at) }}
                                    </span>
                                    <span v-if="!schedule.starts_at && !schedule.ends_at">—</span>
                                </div>
                                <div class="text-center text-[10px]
                                            text-emerald-500 dark:text-emerald-300">
                                    {{ getScheduleOnlineLabel(schedule.is_online) }}
                                </div>
                                <div
                                    v-if="schedule.meeting_url"
                                    class="text-center text-[8px]
                                           text-slate-500 dark:text-slate-300"
                                    :title="t('meetingUrl')"
                                >
                                    {{ schedule.meeting_url }}
                                </div>
                            </td>
                            <td class="px-2 py-3">
                                <div class="text-center text-[10px]">
                                    <span
                                        v-if="schedule.enroll_starts_at"
                                        class="text-blue-700 dark:text-blue-300"
                                    >
                                        {{ formatDate(schedule.enroll_starts_at) }}
                                    </span>
                                    <span
                                        v-if="schedule.enroll_starts_at || schedule.enroll_ends_at">
                                        /
                                    </span>
                                    <br v-if="schedule.enroll_ends_at">
                                    <span
                                        v-if="schedule.enroll_ends_at"
                                        class="text-rose-600 dark:text-rose-400"
                                    >
                                        {{ formatDate(schedule.enroll_ends_at) }}
                                    </span>
                                    <span
                                        v-if="!schedule.enroll_starts_at && !schedule.enroll_ends_at">
                                        —
                                    </span>
                                </div>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div
                                    class="text-center text-[10px]
                                           text-fuchsia-800 dark:text-fuchsia-400"
                            :title="`${t('status')}: ${getScheduleStatusLabel(schedule.status)}`"
                                >
                                    {{ getScheduleStatusLabel(schedule.status) }}
                                </div>
                                <div class="text-center text-xs text-amber-600 dark:text-amber-400">
                                    {{ schedule.capacity ?? 0 }}
                                </div>
                                <div
                                    v-if="schedule.cohort_enrollments_count !== undefined"
                                    class="text-center text-[10px]
                                           text-slate-500 dark:text-slate-300"
                                >
                                    {{ t('enrollments') }}: {{ schedule.cohort_enrollments_count }}
                                </div>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="text-center text-xs text-blue-700 dark:text-blue-300">
                                    {{ schedule.views ?? 0 }}
                                </div>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="flex justify-end space-x-2">
                                    <ActivityToggle
                                        :isActive="schedule.activity"
                                        :title="schedule.activity ? t('enabled') : t('disabled')"
                                        @toggle-activity="emit('toggle-activity', schedule)"
                                    />
                                    <CloneIconButton
                                        :title="t('clone')"
                                        @clone="emit('clone', schedule)"
                                    />
                                    <IconEdit
                                        :href="route('admin.schoolCourseSchedules.edit', {
                                            schoolCourseSchedule: schedule.id,
                                        })"
                                    />
                                    <DeleteIconButton
                                        @delete="emit('delete', schedule)"
                                    />
                                </div>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="text-center">
                                    <input
                                        type="checkbox"
                                        :checked="selectedSchedules.includes(schedule.id)"
                                        @change="emit('toggle-select', schedule.id)"
                                    >
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
