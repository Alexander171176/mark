<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'
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
        month: 'short',
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
               border border-slate-400 dark:border-slate-500 relative"
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

        <div v-if="localSchedules.length" class="p-3">
            <draggable
                v-model="localSchedules"
                tag="div"
                item-key="id"
                handle=".drag-handle"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                @end="handleDragEnd"
            >
                <template #item="{ element: schedule }">
                    <div
                        class="relative flex flex-col h-full rounded-md
                               border border-slate-400 dark:border-slate-500
                               bg-slate-50/70 dark:bg-slate-800/80 shadow-sm
                               hover:shadow-md transition-shadow duration-150"
                    >
                        <div
                            class="flex items-center justify-between px-2 py-1
                                   border-b border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-2">
                                <button
                                    type="button"
                                    class="drag-handle text-slate-400 hover:text-slate-700
                                           dark:hover:text-slate-100"
                                    :title="t('dragDrop')"
                                >
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"
                                        />
                                    </svg>
                                </button>
                                <div
                                    class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm
                                           border border-gray-400
                                           bg-slate-200 dark:bg-slate-700
                                           text-slate-800 dark:text-blue-100"
                                    :title="`[sort: ${schedule.sort}]`"
                                >
                                    ID: {{ schedule.id }}
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span
                                    class="text-[10px] px-1.5 py-0.5 rounded-sm
                                           border border-gray-400
                                           bg-emerald-100 dark:bg-emerald-900/50
                                           text-emerald-700 dark:text-emerald-200"
                                    :title="t('online')"
                                >
                                    {{ getScheduleOnlineLabel(schedule.is_online) }}
                                </span>
                                <input
                                    type="checkbox"
                                    :checked="selectedSchedules.includes(schedule.id)"
                                    @change="emit('toggle-select', schedule.id)"
                                >
                            </div>
                        </div>

                        <div class="relative w-full h-32 bg-slate-200 dark:bg-slate-900">
                            <img
                                v-if="schedule.images && schedule.images.length"
                                :src="getPrimaryImage(schedule)?.webp_url || getPrimaryImage(schedule)?.url"
                                :alt="getPrimaryImage(schedule)?.alt || t('defaultImageAlt')"
                                :title="getPrimaryImage(schedule)?.caption || t('scheduleImage')"
                                class="w-full h-full object-cover"
                            >
                        <img
                            v-else
                            src="/storage/school/school_course_schedule_images/default-image.png"
                            :alt="t('defaultImageTitle')"
                            class="w-full h-full object-cover"
                        >
                        </div>
                        <div class="flex flex-col flex-1 px-3 py-2 space-y-1 text-[11px]">
                            <a
                                :href="`/school/schedules/${encodeURIComponent(schedule.slug)}`"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-xs font-semibold text-amber-600
                                       dark:text-amber-200 hover:text-red-600
                                       dark:hover:text-red-300
                                       hover:underline text-center"
                                :title="schedule.subtitle || schedule.title"
                            >
                                {{ schedule.title || `ID: ${schedule.id}` }}
                            </a>
                            <div class="text-[10px] text-slate-500 dark:text-slate-300 text-center"
                                 :title="schedule.slug">
                                {{ schedule.slug }}
                            </div>
                            <div
                                class="text-[10px] font-semibold
                                       text-blue-700 dark:text-blue-300 text-center"
                                :title="schedule.course?.title || ''"
                            >
                                {{ schedule.course?.title || '—' }}
                            </div>
                            <div class="text-center mt-1">
                                <span class="font-semibold text-slate-700 dark:text-slate-300">
                                    {{ getInstructorUserLabel(schedule) || '—' }}
                                    <span v-if="getInstructorTitle(schedule)">:</span>
                                </span>
                                <br v-if="getInstructorTitle(schedule)">
                                <span
                                    v-if="getInstructorTitle(schedule)"
                                    class="font-semibold text-teal-600 dark:text-teal-300"
                                >
                                    {{ getInstructorTitle(schedule) }}
                                </span>
                            </div>
                            <div
                                v-if="schedule.location"
                                class="text-center text-[10px]
                                       text-amber-700 dark:text-amber-300"
                                :title="schedule.location"
                            >
                                {{ schedule.location }}
                            </div>
                            <div
                                class="px-1 text-left text-[9px] font-semibold
                                       border-dashed border border-gray-400"
                            >
                                <div>
                                    <span class="text-gray-600 dark:text-gray-300">
                                        {{ t('scheduleStartsAt') }}:
                                    </span>
                                    <span class="text-sky-700 dark:text-sky-300">
                                    {{ schedule.starts_at ? formatDate(schedule.starts_at) : '—' }}
                                    </span>
                                </div>
                                <div v-if="schedule.ends_at">
                                    <span class="text-gray-600 dark:text-gray-300">
                                        {{ t('scheduleEndsAt') }}:
                                    </span>
                                    <span class="text-rose-600 dark:text-rose-300">
                                        {{ formatDate(schedule.ends_at) }}
                                    </span>
                                </div>
                            </div>
                            <div class="mt-1 text-center text-[9px] font-semibold">
                                <div class="flex flex-col justify-start">
                                    <span class="text-slate-800 dark:text-slate-100">
                                        {{ t('scheduleEnrollStartsAt') }}:
                                    </span>
                                    <span class="text-blue-700 dark:text-blue-300">
                    {{ schedule.enroll_starts_at ? formatDate(schedule.enroll_starts_at) : '—' }}
                                    </span>
                                </div>

                                <div
                                    v-if="schedule.enroll_ends_at"
                                    class="flex flex-col justify-start"
                                >
                                    <span class="text-slate-800 dark:text-slate-100">
                                        {{ t('scheduleEnrollEndsAt') }}:
                                    </span>

                                    <span class="text-rose-600 dark:text-rose-300">
                                        {{ formatDate(schedule.enroll_ends_at) }}
                                    </span>
                                </div>
                            </div>
                            <div
                                class="flex flex-wrap justify-center gap-1 mt-2
                                       text-[10px] font-semibold"
                            >
                                <span
                                    class="px-2 py-0.5 rounded-sm
                                           bg-amber-100 dark:bg-amber-900
                                           border border-gray-400
                                           text-amber-700 dark:text-amber-200"
                                    :title="t('capacity')"
                                >
                                    {{ t('capacity') }}: {{ schedule.capacity ?? '—' }}
                                </span>
                                <span
                                    v-if="schedule.cohort_enrollments_count !== undefined"
                                    class="px-2 py-0.5 rounded-sm
                                           bg-violet-100 dark:bg-violet-900
                                           border border-gray-400
                                           text-violet-700 dark:text-violet-200"
                                >
                                    {{ t('enrollments') }}: {{ schedule.cohort_enrollments_count }}
                                </span>
                                <span
                                    class="px-2 py-0.5 rounded-sm
                                           bg-sky-100 dark:bg-sky-900
                                           border border-gray-400
                                           text-sky-700 dark:text-sky-200"
                                    :title="t('status')"
                                >
                                    {{ getScheduleStatusLabel(schedule.status) }}
                                </span>
                                <span
                                    class="px-2 py-0.5 rounded-sm
                                           bg-blue-100 dark:bg-blue-900
                                           border border-gray-400
                                           text-blue-700 dark:text-blue-200"
                                    :title="t('views')"
                                >
                                    {{ t('views') }}: {{ schedule.views ?? 0 }}
                                </span>
                            </div>
                        </div>
                        <div
                            class="flex items-center justify-center px-3 py-2
                                   border-t border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-1">
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
