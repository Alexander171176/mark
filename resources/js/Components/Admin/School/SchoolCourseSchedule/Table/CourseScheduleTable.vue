<script setup>
import { ref, watch } from 'vue'
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

/* ==========================================================
 * LOCAL LIST / DRAG
 * ========================================================== */

const localSchedules = ref([])

watch(
    () => props.schedules,
    (schedules) => {
        localSchedules.value = JSON.parse(
            JSON.stringify(
                schedules || []
            )
        )
    },
    {
        immediate: true,
        deep: true,
    }
)

const handleDragEnd = () => {
    emit(
        'update-sort-order',
        localSchedules.value.map(
            schedule => schedule.id
        )
    )
}

const toggleAll = (event) => {
    emit('toggle-all', {
        ids: localSchedules.value.map(
            schedule => schedule.id
        ),

        checked:
        event.target.checked,
    })
}

/* ==========================================================
 * NEW SHARED RESOURCE CONTRACT
 * ========================================================== */

const getScheduleTitle = (schedule) => {
    return schedule?.translation?.title
        || `ID: ${schedule?.id}`
}

const getScheduleSubtitle = (schedule) => {
    return schedule?.translation?.subtitle || ''
}

const getScheduleShort = (schedule) => {
    return schedule?.translation?.short || ''
}

const getCourseTitle = (schedule) => {
    return schedule?.course?.translation?.title
        || (
            schedule?.course?.id
                ? `ID: ${schedule.course.id}`
                : ''
        )
}

const getInstructorTitle = (schedule) => {
    return schedule?.instructor?.translation?.title || ''
}

const getInstructorUserLabel = (schedule) => {
    return schedule?.instructor?.user?.name
        || schedule?.instructor?.user?.email
        || ''
}

/* ==========================================================
 * LABELS
 * ========================================================== */

const scheduleStatusLabelKeyMap = {
    draft: 'statusDraft',
    published: 'statusPublished',
    archived: 'statusArchived',
    cancelled: 'statusCancelled',
}

const getScheduleStatusLabel = (status) => {
    if (!status) {
        return '—'
    }

    const key =
        scheduleStatusLabelKeyMap[status]

    return key
        ? t(key)
        : status
}

const getScheduleOnlineLabel = (isOnline) => {
    return t(
        isOnline
            ? 'online'
            : 'offline'
    )
}

/* ==========================================================
 * DATE
 * ========================================================== */

const formatDate = (dateStr) => {
    if (!dateStr) {
        return ''
    }

    const date =
        new Date(dateStr)

    if (
        Number.isNaN(
            date.getTime()
        )
    ) {
        return ''
    }

    return date.toLocaleDateString(
        'ru-RU',
        {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        }
    )
}
</script>

<template>
    <div
        class="relative rounded-sm border border-slate-200
               bg-white shadow-lg
               dark:border-slate-600 dark:bg-slate-700"
    >
        <!-- Selection header -->
        <div
            class="flex items-center justify-between
                   border-b border-slate-400 px-3 py-2
                   dark:border-slate-500"
        >
            <div
                class="text-xs text-slate-600
                       dark:text-slate-200"
            >
                {{ t('selected') }}:
                {{ selectedSchedules.length }}
            </div>

            <label
                v-if="localSchedules.length"
                class="flex cursor-pointer items-center
                       text-xs text-slate-600
                       dark:text-slate-200"
            >
                <span>
                    {{ t('selectAll') }}
                </span>

                <input
                    type="checkbox"
                    class="mx-2"
                    @change="toggleAll"
                >
            </label>
        </div>

        <div class="overflow-x-auto">
            <table
                v-if="localSchedules.length"
                class="table-auto w-full
                       text-slate-700 dark:text-slate-100"
            >
                <thead
                    class="border border-solid border-gray-300
                           bg-slate-200 text-sm uppercase
                           dark:border-gray-700 dark:bg-cyan-900"
                >
                <tr>
                    <!-- Drag -->
                    <th class="w-px px-2 py-3">
                        <svg
                            class="h-4 w-4 fill-current
                                       text-slate-800 dark:text-slate-200"
                            viewBox="0 0 24 24"
                        >
                            <path
                                d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"
                            />

                            <path
                                d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"
                            />
                        </svg>
                    </th>

                    <!-- ID -->
                    <th class="w-px px-2 py-3">
                        <div class="text-center font-medium">
                            {{ t('id') }}
                        </div>
                    </th>

                    <!-- Image -->
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div
                            class="flex justify-center"
                            :title="t('image')"
                        >
                            <svg
                                class="h-6 w-6 shrink-0 fill-current"
                                viewBox="0 0 512 512"
                            >
                                <path
                                    d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l96 0 32 0 208 0c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"
                                />
                            </svg>
                        </div>
                    </th>

                    <!-- Title -->
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="text-left font-semibold">
                            {{ t('title') }}
                        </div>
                    </th>

                    <!-- Relations -->
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="text-center font-semibold">
                            {{ t('course') }}
                            /
                            {{ t('instructor') }}
                        </div>
                    </th>

                    <!-- Schedule -->
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div
                            class="flex items-center justify-center"
                            :title="t('scheduleStartsAt')"
                        >
                            <svg
                                class="h-4 w-4 shrink-0 fill-current"
                                viewBox="0 0 448 512"
                            >
                                <path
                                    d="M148 288h-40c-6.6 0-12-5.4-12-12v-40c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12zm108-12v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm96 0v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm-96 96v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm-96 0v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm192 0v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm96-260v352c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V112c0-26.5 21.5-48 48-48h48V12c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v52h128V12c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v52h48c26.5 0 48 21.5 48 48zm-48 346V160H48v298c0 3.3 2.7 6 6 6h340c3.3 0 6-2.7 6-6z"
                                />
                            </svg>
                        </div>
                    </th>

                    <!-- Enroll -->
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div
                            class="flex items-center justify-center"
                            :title="t('scheduleEnrollStartsAt')"
                        >
                            {{ t('enrollments') }}
                        </div>
                    </th>

                    <!-- Status -->
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="text-center font-semibold">
                            {{ t('status') }}
                        </div>
                    </th>

                    <!-- Views -->
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div
                            class="flex justify-center"
                            :title="t('views')"
                        >
                            <svg
                                class="h-4 w-4 shrink-0 fill-current"
                                viewBox="0 0 16 16"
                            >
                                <path
                                    class="fill-current text-blue-600 dark:text-blue-300"
                                    d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"
                                />
                            </svg>
                        </div>
                    </th>

                    <!-- Actions -->
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="text-end font-semibold">
                            {{ t('actions') }}
                        </div>
                    </th>

                    <!-- Select -->
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="text-center">
                            <input
                                type="checkbox"
                                @change="toggleAll"
                            >
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
                            class="border-b-2 text-sm font-semibold
                                   hover:bg-slate-100
                                   dark:hover:bg-cyan-800"
                        >
                            <!-- Drag -->
                            <td
                                class="handle cursor-move
                                       px-2 py-1 text-center"
                            >
                                <svg
                                    class="h-4 w-4
                                           text-gray-500 dark:text-gray-300"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"
                                    />
                                </svg>
                            </td>

                            <!-- ID -->
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div
                                    class="text-center text-xs
                                           text-slate-800 dark:text-blue-200"
                                    :title="`[sort: ${schedule.sort}]`"
                                >
                                    {{ schedule.id }}
                                </div>
                            </td>

                            <!-- Thumbnail -->
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="flex justify-center">
                                    <img
                                        v-if="schedule.thumbnail_url"
                                        :src="schedule.thumbnail_url"
                                        :alt="getScheduleTitle(schedule)"
                                        :title="getScheduleTitle(schedule)"
                                        class="h-8 w-12 rounded-sm object-cover"
                                    >

                                    <img
                                        v-else
                                        src="/storage/school/school_course_schedule_images/default-image.png"
                                        :alt="t('defaultImageTitle')"
                                        class="h-8 w-12 rounded-sm object-cover"
                                    >
                                </div>
                            </td>

                            <!-- Schedule -->
                            <td class="px-2 py-3">
                                <a
                                    :href="`/school/schedules/${encodeURIComponent(schedule.slug)}`"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-xs text-amber-600
                                           hover:text-red-600 hover:underline
                                           dark:text-amber-200
                                           dark:hover:text-red-300"
                                    :title="
                                        getScheduleSubtitle(schedule)
                                        || getScheduleShort(schedule)
                                    "
                                >
                                    {{ getScheduleTitle(schedule) }}
                                </a>

                                <div
                                    class="truncate text-xs
                                           text-slate-500 dark:text-slate-300"
                                    :title="schedule.slug"
                                >
                                    {{ schedule.slug }}
                                </div>

                                <div
                                    v-if="schedule.location"
                                    class="truncate text-[10px]
                                           text-teal-700 dark:text-teal-300"
                                    :title="schedule.location"
                                >
                                    {{ schedule.location }}
                                </div>

                                <div
                                    v-if="schedule.timezone"
                                    class="text-[9px]
                                           text-slate-500 dark:text-slate-400"
                                >
                                    {{ schedule.timezone }}
                                </div>
                            </td>

                            <!-- Relations -->
                            <td class="px-2 py-3">
                                <div
                                    class="text-center text-xs
                                           text-blue-700 dark:text-blue-300"
                                    :title="getCourseTitle(schedule)"
                                >
                                    {{ getCourseTitle(schedule) || '—' }}
                                </div>

                                <div class="mt-1 text-center text-xs">
                                    <span
                                        class="text-slate-700
                                               dark:text-slate-300"
                                    >
                                        {{ getInstructorUserLabel(schedule) || '—' }}
                                    </span>

                                    <template v-if="getInstructorTitle(schedule)">
                                        <br>

                                        <span
                                            class="text-teal-600
                                                   dark:text-teal-300"
                                        >
                                            {{ getInstructorTitle(schedule) }}
                                        </span>
                                    </template>
                                </div>
                            </td>

                            <!-- Course dates -->
                            <td class="px-2 py-3">
                                <div class="text-center text-[10px]">
                                    <span
                                        v-if="schedule.starts_at"
                                        class="text-sky-600 dark:text-sky-400"
                                    >
                                        {{ formatDate(schedule.starts_at) }}
                                    </span>

                                    <span
                                        v-if="schedule.starts_at && schedule.ends_at"
                                    >
                                        /
                                    </span>

                                    <br v-if="schedule.ends_at">

                                    <span
                                        v-if="schedule.ends_at"
                                        class="text-red-400 dark:text-red-200"
                                    >
                                        {{ formatDate(schedule.ends_at) }}
                                    </span>

                                    <span
                                        v-if="!schedule.starts_at && !schedule.ends_at"
                                    >
                                        —
                                    </span>
                                </div>

                                <div
                                    class="text-center text-[10px]
                                           text-emerald-500 dark:text-emerald-300"
                                >
                                    {{ getScheduleOnlineLabel(schedule.is_online) }}
                                </div>

                                <a
                                    v-if="schedule.meeting_url"
                                    :href="schedule.meeting_url"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="block max-w-40 truncate
                                           text-center text-[8px]
                                           text-sky-600 hover:underline
                                           dark:text-sky-300"
                                    :title="schedule.meeting_url"
                                >
                                    {{ schedule.meeting_url }}
                                </a>
                            </td>

                            <!-- Enrollment dates -->
                            <td class="px-2 py-3">
                                <div class="text-center text-[10px]">
                                    <span
                                        v-if="schedule.enroll_starts_at"
                                        class="text-blue-700 dark:text-blue-300"
                                    >
                                        {{ formatDate(schedule.enroll_starts_at) }}
                                    </span>

                                    <span
                                        v-if="
                                            schedule.enroll_starts_at
                                            && schedule.enroll_ends_at
                                        "
                                    >
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
                                        v-if="
                                            !schedule.enroll_starts_at
                                            && !schedule.enroll_ends_at
                                        "
                                    >
                                        —
                                    </span>
                                </div>

                                <div
                                    class="mt-1 text-center text-[10px]"
                                    :class="
                                        schedule.is_enrollment_open
                                            ? 'text-emerald-600 dark:text-emerald-300'
                                            : 'text-slate-500 dark:text-slate-400'
                                    "
                                >
                                    {{
                                        schedule.is_enrollment_open
                                            ? t('enabled')
                                            : t('disabled')
                                    }}
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div
                                    class="text-center text-[10px]
                                           text-fuchsia-800
                                           dark:text-fuchsia-400"
                                    :title="
                                        `${t('status')}: ${getScheduleStatusLabel(schedule.status)}`
                                    "
                                >
                                    {{ getScheduleStatusLabel(schedule.status) }}
                                </div>

                                <div
                                    class="text-center text-xs
                                           text-amber-600 dark:text-amber-400"
                                    :title="t('capacity')"
                                >
                                    {{ schedule.capacity ?? 0 }}
                                </div>

                                <div
                                    class="text-center text-[10px]
                                           text-slate-500 dark:text-slate-300"
                                >
                                    {{ t('enrollments') }}:
                                    {{ schedule.cohort_enrollments_count ?? 0 }}
                                </div>

                                <div
                                    class="text-center text-[10px]
                                           text-slate-500 dark:text-slate-300"
                                >
                                    {{ t('images') }}:
                                    {{ schedule.images_count ?? 0 }}
                                </div>
                            </td>

                            <!-- Views -->
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div
                                    class="text-center text-xs
                                           text-blue-700 dark:text-blue-300"
                                >
                                    {{ schedule.views ?? 0 }}
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="flex justify-end gap-2">
                                    <ActivityToggle
                                        :isActive="schedule.activity"
                                        :title="
                                            schedule.activity
                                                ? t('enabled')
                                                : t('disabled')
                                        "
                                        @toggle-activity="
                                            emit('toggle-activity', schedule)
                                        "
                                    />

                                    <CloneIconButton
                                        :title="t('clone')"
                                        @clone="
                                            emit('clone', schedule)
                                        "
                                    />

                                    <IconEdit
                                        :href="
                                            route(
                                                'admin.schoolCourseSchedules.edit',
                                                {
                                                    schoolCourseSchedule:
                                                        schedule.id,
                                                }
                                            )
                                        "
                                    />

                                    <DeleteIconButton
                                        @delete="
                                            emit('delete', schedule)
                                        "
                                    />
                                </div>
                            </td>

                            <!-- Select -->
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="text-center">
                                    <input
                                        type="checkbox"
                                        :checked="
                                            selectedSchedules.includes(
                                                schedule.id
                                            )
                                        "
                                        @change="
                                            emit(
                                                'toggle-select',
                                                schedule.id
                                            )
                                        "
                                    >
                                </div>
                            </td>
                        </tr>
                    </template>
                </draggable>
            </table>

            <div
                v-else
                class="p-5 text-center
                       text-slate-700 dark:text-slate-100"
            >
                {{ t('noData') }}
            </div>
        </div>
    </div>
</template>
