<script setup>
import { ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import ActivityToggle from '@/Components/Admin/UI/Buttons/ActivityToggle.vue'
import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'
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
            month: 'short',
            day: 'numeric',
        }
    )
}
</script>

<template>
    <div
        class="relative rounded-sm border border-slate-400
               bg-white shadow-lg
               dark:border-slate-500 dark:bg-slate-700"
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

        <div
            v-if="localSchedules.length"
            class="p-3"
        >
            <draggable
                v-model="localSchedules"
                tag="div"
                item-key="id"
                handle=".drag-handle"
                class="grid grid-cols-1 gap-3
                       sm:grid-cols-2
                       lg:grid-cols-3
                       xl:grid-cols-4"
                @end="handleDragEnd"
            >
                <template #item="{ element: schedule }">
                    <div
                        class="relative flex h-full flex-col
                               rounded-md border border-slate-400
                               bg-slate-50/70 shadow-sm
                               transition-shadow duration-150
                               hover:shadow-md
                               dark:border-slate-500
                               dark:bg-slate-800/80"
                    >
                        <!-- Header -->
                        <div
                            class="flex items-center justify-between
                                   border-b border-dashed border-slate-400
                                   px-2 py-1 dark:border-slate-500"
                        >
                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    class="drag-handle
                                           text-slate-400
                                           hover:text-slate-700
                                           dark:hover:text-slate-100"
                                    :title="t('dragDrop')"
                                >
                                    <svg
                                        class="h-4 w-4"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"
                                        />
                                    </svg>
                                </button>

                                <div
                                    class="rounded-sm border border-gray-400
                                           bg-slate-200 px-1.5 py-0.5
                                           text-[10px] font-semibold
                                           text-slate-800
                                           dark:bg-slate-700
                                           dark:text-blue-100"
                                    :title="`[sort: ${schedule.sort}]`"
                                >
                                    ID: {{ schedule.id }}
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <span
                                    class="rounded-sm border border-gray-400
                                           bg-emerald-100 px-1.5 py-0.5
                                           text-[10px] text-emerald-700
                                           dark:bg-emerald-900/50
                                           dark:text-emerald-200"
                                    :title="t('online')"
                                >
                                    {{ getScheduleOnlineLabel(schedule.is_online) }}
                                </span>

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
                        </div>

                        <!-- Thumbnail -->
                        <div
                            class="relative h-32 w-full
                                   bg-slate-200 dark:bg-slate-900"
                        >
                            <img
                                v-if="schedule.thumbnail_url"
                                :src="schedule.thumbnail_url"
                                :alt="getScheduleTitle(schedule)"
                                :title="getScheduleTitle(schedule)"
                                class="h-full w-full object-cover"
                            >

                            <img
                                v-else
                                src="/storage/school/school_course_schedule_images/default-image.png"
                                :alt="t('defaultImageTitle')"
                                class="h-full w-full object-cover"
                            >
                        </div>

                        <!-- Content -->
                        <div
                            class="flex flex-1 flex-col
                                   space-y-1 px-3 py-2
                                   text-[11px]"
                        >
                            <!-- Title -->
                            <a
                                :href="`/school/schedules/${encodeURIComponent(schedule.slug)}`"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-center text-xs font-semibold
                                       text-amber-600
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

                            <!-- Slug -->
                            <div
                                class="text-center text-[10px]
                                       text-slate-500 dark:text-slate-300"
                                :title="schedule.slug"
                            >
                                {{ schedule.slug }}
                            </div>

                            <!-- Course -->
                            <div
                                class="text-center text-[10px] font-semibold
                                       text-blue-700 dark:text-blue-300"
                                :title="getCourseTitle(schedule)"
                            >
                                {{ getCourseTitle(schedule) || '—' }}
                            </div>

                            <!-- Instructor -->
                            <div class="mt-1 text-center">
                                <span
                                    class="font-semibold
                                           text-slate-700 dark:text-slate-300"
                                >
                                    {{ getInstructorUserLabel(schedule) || '—' }}
                                </span>

                                <template v-if="getInstructorTitle(schedule)">
                                    <br>

                                    <span
                                        class="font-semibold
                                               text-teal-600 dark:text-teal-300"
                                    >
                                        {{ getInstructorTitle(schedule) }}
                                    </span>
                                </template>
                            </div>

                            <!-- Location -->
                            <div
                                v-if="schedule.location"
                                class="text-center text-[10px]
                                       text-amber-700 dark:text-amber-300"
                                :title="schedule.location"
                            >
                                {{ schedule.location }}
                            </div>

                            <!-- Timezone -->
                            <div
                                v-if="schedule.timezone"
                                class="text-center text-[9px]
                                       text-slate-500 dark:text-slate-400"
                            >
                                {{ schedule.timezone }}
                            </div>

                            <!-- Course dates -->
                            <div
                                class="mt-1 border border-dashed
                                       border-gray-400 px-1
                                       text-left text-[9px] font-semibold"
                            >
                                <div>
                                    <span
                                        class="text-gray-600
                                               dark:text-gray-300"
                                    >
                                        {{ t('scheduleStartsAt') }}:
                                    </span>

                                    <span
                                        class="text-sky-700
                                               dark:text-sky-300"
                                    >
                                        {{
                                            schedule.starts_at
                                                ? formatDate(schedule.starts_at)
                                                : '—'
                                        }}
                                    </span>
                                </div>

                                <div>
                                    <span
                                        class="text-gray-600
                                               dark:text-gray-300"
                                    >
                                        {{ t('scheduleEndsAt') }}:
                                    </span>

                                    <span
                                        class="text-rose-600
                                               dark:text-rose-300"
                                    >
                                        {{
                                            schedule.ends_at
                                                ? formatDate(schedule.ends_at)
                                                : '—'
                                        }}
                                    </span>
                                </div>
                            </div>

                            <!-- Enrollment dates -->
                            <div
                                class="mt-1 text-center
                                       text-[9px] font-semibold"
                            >
                                <div class="flex flex-col">
                                    <span
                                        class="text-slate-800
                                               dark:text-slate-100"
                                    >
                                        {{ t('scheduleEnrollStartsAt') }}:
                                    </span>

                                    <span
                                        class="text-blue-700
                                               dark:text-blue-300"
                                    >
                                        {{
                                            schedule.enroll_starts_at
                                                ? formatDate(schedule.enroll_starts_at)
                                                : '—'
                                        }}
                                    </span>
                                </div>

                                <div class="flex flex-col">
                                    <span
                                        class="text-slate-800
                                               dark:text-slate-100"
                                    >
                                        {{ t('scheduleEnrollEndsAt') }}:
                                    </span>

                                    <span
                                        class="text-rose-600
                                               dark:text-rose-300"
                                    >
                                        {{
                                            schedule.enroll_ends_at
                                                ? formatDate(schedule.enroll_ends_at)
                                                : '—'
                                        }}
                                    </span>
                                </div>
                            </div>

                            <!-- Meeting URL -->
                            <a
                                v-if="schedule.meeting_url"
                                :href="schedule.meeting_url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="truncate text-center text-[9px]
                                       text-sky-600 hover:underline
                                       dark:text-sky-300"
                                :title="schedule.meeting_url"
                            >
                                {{ schedule.meeting_url }}
                            </a>

                            <!-- Badges -->
                            <div
                                class="mt-2 flex flex-wrap
                                       justify-center gap-1
                                       text-[10px] font-semibold"
                            >
                                <span
                                    class="rounded-sm border border-gray-400
                                           bg-amber-100 px-2 py-0.5
                                           text-amber-700
                                           dark:bg-amber-900
                                           dark:text-amber-200"
                                    :title="t('capacity')"
                                >
                                    {{ t('capacity') }}:
                                    {{ schedule.capacity ?? 0 }}
                                </span>

                                <span
                                    class="rounded-sm border border-gray-400
                                           bg-violet-100 px-2 py-0.5
                                           text-violet-700
                                           dark:bg-violet-900
                                           dark:text-violet-200"
                                >
                                    {{ t('enrollments') }}:
                                    {{ schedule.cohort_enrollments_count ?? 0 }}
                                </span>

                                <span
                                    class="rounded-sm border border-gray-400
                                           bg-slate-100 px-2 py-0.5
                                           text-slate-700
                                           dark:bg-slate-700
                                           dark:text-slate-200"
                                >
                                    {{ t('images') }}:
                                    {{ schedule.images_count ?? 0 }}
                                </span>

                                <span
                                    class="rounded-sm border border-gray-400
                                           bg-sky-100 px-2 py-0.5
                                           text-sky-700
                                           dark:bg-sky-900
                                           dark:text-sky-200"
                                    :title="t('status')"
                                >
                                    {{ getScheduleStatusLabel(schedule.status) }}
                                </span>

                                <span
                                    class="rounded-sm border border-gray-400
                                           bg-blue-100 px-2 py-0.5
                                           text-blue-700
                                           dark:bg-blue-900
                                           dark:text-blue-200"
                                    :title="t('views')"
                                >
                                    {{ t('views') }}:
                                    {{ schedule.views ?? 0 }}
                                </span>
                            </div>

                            <!-- Enrollment state -->
                            <div
                                class="mt-1 text-center text-[10px] font-semibold"
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
                        </div>

                        <!-- Actions -->
                        <div
                            class="flex items-center justify-center
                                   border-t border-dashed
                                   border-slate-400 px-3 py-2
                                   dark:border-slate-500"
                        >
                            <div class="flex items-center gap-1">
                                <ActivityToggle
                                    :isActive="schedule.activity"
                                    :title="
                                        schedule.activity
                                            ? t('enabled')
                                            : t('disabled')
                                    "
                                    @toggle-activity="
                                        emit(
                                            'toggle-activity',
                                            schedule
                                        )
                                    "
                                />

                                <CloneIconButton
                                    :title="t('clone')"
                                    @clone="
                                        emit(
                                            'clone',
                                            schedule
                                        )
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
                                        emit(
                                            'delete',
                                            schedule
                                        )
                                    "
                                />
                            </div>
                        </div>
                    </div>
                </template>
            </draggable>
        </div>

        <div
            v-else
            class="p-5 text-center
                   text-slate-700 dark:text-slate-100"
        >
            {{ t('noData') }}
        </div>
    </div>
</template>
