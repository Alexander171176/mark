<script setup>
import { ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'
import ActivityToggle from '@/Components/Admin/UI/Buttons/ActivityToggle.vue'
import CloneIconButton from '@/Components/Admin/UI/Buttons/CloneIconButton.vue'
import RightToggle from '@/Components/Admin/UI/Buttons/RightToggle.vue'
import MainToggle from '@/Components/Admin/UI/Buttons/MainToggle.vue'
import LeftToggle from '@/Components/Admin/UI/Buttons/LeftToggle.vue'

const { t } = useI18n()

const props = defineProps({
    assignments: { type: Array, default: () => [] },
    selectedAssignments: { type: Array, default: () => [] },
})

const emit = defineEmits([
    'toggle-left',
    'toggle-main',
    'toggle-right',
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

const localAssignments = ref([])

watch(
    () => props.assignments,
    (assignments) => {
        localAssignments.value = JSON.parse(
            JSON.stringify(assignments || [])
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
        localAssignments.value.map(
            assignment => assignment.id
        )
    )
}

const toggleAll = (event) => {
    emit('toggle-all', {
        ids: localAssignments.value.map(
            assignment => assignment.id
        ),
        checked: event.target.checked,
    })
}

/* ==========================================================
 * NEW RESOURCE CONTRACT
 * ========================================================== */

const getAssignmentTitle = (assignment) => {
    return assignment?.translation?.title
        || `ID: ${assignment?.id}`
}

const getAssignmentSubtitle = (assignment) => {
    return assignment?.translation?.subtitle || ''
}

const getAssignmentShort = (assignment) => {
    return assignment?.translation?.short || ''
}

const getCourseLabel = (assignment) => {
    return assignment?.course?.translation?.title
        || (
            assignment?.course?.id
                ? `ID: ${assignment.course.id}`
                : ''
        )
}

const getModuleLabel = (assignment) => {
    return assignment?.module?.translation?.title
        || (
            assignment?.module?.id
                ? `ID: ${assignment.module.id}`
                : ''
        )
}

const getLessonLabel = (assignment) => {
    return assignment?.lesson?.translation?.title
        || (
            assignment?.lesson?.id
                ? `ID: ${assignment.lesson.id}`
                : ''
        )
}

const getInstructorLabel = (assignment) => {
    const instructor = assignment?.instructor

    if (!instructor) {
        return ''
    }

    const title =
        instructor?.translation?.title || ''

    const userName =
        instructor?.user?.name
        || instructor?.user?.email
        || ''

    if (title && userName && title !== userName) {
        return `${title}: ${userName}`
    }

    return title
        || userName
        || (
            instructor?.id
                ? `ID: ${instructor.id}`
                : ''
        )
}

/* ==========================================================
 * IMAGES
 * ========================================================== */

const getPrimaryImage = (assignment) => {
    if (assignment?.primary_image) {
        return assignment.primary_image
    }

    if (
        Array.isArray(assignment?.images)
        && assignment.images.length
    ) {
        return [...assignment.images].sort(
            (a, b) =>
                Number(a?.order ?? 0)
                - Number(b?.order ?? 0)
        )[0]
    }

    return null
}

const getImageUrl = (image) => {
    return image?.thumb_url
        || image?.webp_url
        || image?.image_url
        || image?.url
        || ''
}

/* ==========================================================
 * LABELS
 * ========================================================== */

const assignmentStatusLabelKeyMap = {
    draft: 'statusDraft',
    published: 'statusPublished',
    archived: 'statusArchived',
}

const assignmentVisibilityLabelKeyMap = {
    public: 'assignmentGeneral',
    enrolled: 'assignmentTest',
    private: 'assignmentThematic',
}

const assignmentGradingTypeLabelKeyMap = {
    manual: 'gradingManual',
    auto: 'gradingAuto',
}

const getAssignmentStatusLabel = (status) => {
    return t(
        assignmentStatusLabelKeyMap[status]
        || status
        || 'no'
    )
}

const getAssignmentVisibilityLabel = (visibility) => {
    return t(
        assignmentVisibilityLabelKeyMap[visibility]
        || visibility
        || 'no'
    )
}

const getAssignmentGradingTypeLabel = (gradingType) => {
    return t(
        assignmentGradingTypeLabelKeyMap[gradingType]
        || gradingType
        || 'no'
    )
}

/* ==========================================================
 * DATE
 * ========================================================== */

const formatDate = (dateStr) => {
    if (!dateStr) {
        return ''
    }

    const date = new Date(dateStr)

    if (Number.isNaN(date.getTime())) {
        return ''
    }

    return date.toLocaleDateString('ru-RU', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    })
}
</script>

<template>
    <div
        class="relative rounded-sm border border-slate-400
               bg-white shadow-lg dark:border-slate-500 dark:bg-slate-700"
    >
        <div
            class="flex items-center justify-between border-b
                   border-slate-400 px-3 py-2 dark:border-slate-500"
        >
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('selected') }}: {{ selectedAssignments.length }}
            </div>

            <label
                v-if="localAssignments.length"
                class="flex cursor-pointer items-center
                       text-xs text-slate-600 dark:text-slate-200"
            >
                <span>{{ t('selectAll') }}</span>

                <input
                    type="checkbox"
                    class="mx-2"
                    @change="toggleAll"
                >
            </label>
        </div>

        <div
            v-if="localAssignments.length"
            class="p-3"
        >
            <draggable
                v-model="localAssignments"
                tag="div"
                item-key="id"
                handle=".handle"
                class="grid grid-cols-1 gap-3
                       sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                @end="handleDragEnd"
            >
                <template #item="{ element: assignment }">
                    <div
                        class="relative flex h-full flex-col rounded-md
                               border border-slate-400 bg-slate-50/70 shadow-sm
                               transition-shadow duration-150 hover:shadow-md
                               dark:border-slate-500 dark:bg-slate-800/80"
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
                                    class="handle text-slate-400
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
                                           text-slate-800 dark:bg-slate-700
                                           dark:text-blue-100"
                                    :title="`[sort: ${assignment.sort}] ${formatDate(assignment.published_at)}`"
                                >
                                    ID: {{ assignment.id }}
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <span
                                    class="rounded-sm border border-gray-400
                                           bg-fuchsia-100 px-1.5 py-0.5
                                           text-[10px] text-fuchsia-800
                                           dark:bg-fuchsia-900/50 dark:text-fuchsia-400"
                                    :title="`${t('status')}: ${getAssignmentStatusLabel(assignment.status)} / ${getAssignmentVisibilityLabel(assignment.visibility)}`"
                                >
                                    {{ getAssignmentStatusLabel(assignment.status) }}
                                </span>

                                <input
                                    type="checkbox"
                                    :checked="selectedAssignments.includes(assignment.id)"
                                    @change="emit('toggle-select', assignment.id)"
                                >
                            </div>
                        </div>

                        <!-- Image -->
                        <div class="relative h-32 w-full bg-slate-200 dark:bg-slate-900">
                            <img
                                v-if="getPrimaryImage(assignment)"
                                :src="getImageUrl(getPrimaryImage(assignment))"
                                :alt="getPrimaryImage(assignment)?.alt || t('defaultImageAlt')"
                                :title="getPrimaryImage(assignment)?.caption || t('assignmentImage')"
                                class="h-full w-full object-cover"
                            >

                            <img
                                v-else
                                src="/storage/school/school_assignment_images/default-image.png"
                                :alt="t('defaultImageTitle')"
                                class="h-full w-full object-cover"
                            >
                        </div>

                        <!-- Content -->
                        <div class="flex flex-1 flex-col space-y-1 px-3 py-2">
                            <a
                                :href="`/school/assignments/${encodeURIComponent(assignment.slug)}`"
                                class="line-clamp-2 text-center text-sm font-semibold
                                       text-sky-700 hover:underline dark:text-sky-200"
                                target="_blank"
                                rel="noopener noreferrer"
                                :title="getAssignmentSubtitle(assignment) || getAssignmentShort(assignment)"
                            >
                                {{ getAssignmentTitle(assignment) }}
                            </a>

                            <div
                                class="truncate text-center text-[10px]
                                       text-slate-600 dark:text-slate-300"
                                :title="assignment.slug"
                            >
                                {{ assignment.slug }}
                            </div>

                            <div
                                class="text-xs font-semibold
                                       text-slate-600 dark:text-slate-300"
                                :title="getLessonLabel(assignment)"
                            >
                                {{ t('lesson') }}:
                                {{ getLessonLabel(assignment) || '—' }}
                            </div>

                            <div
                                class="text-xs font-semibold
                                       text-slate-600 dark:text-slate-300"
                                :title="getModuleLabel(assignment)"
                            >
                                {{ t('module') }}:
                                {{ getModuleLabel(assignment) || '—' }}
                            </div>

                            <div
                                class="text-xs font-semibold
                                       text-slate-600 dark:text-slate-300"
                                :title="getCourseLabel(assignment)"
                            >
                                {{ t('course') }}:
                                {{ getCourseLabel(assignment) || '—' }}
                            </div>

                            <div
                                class="text-center text-xs font-semibold
                                       text-teal-600 dark:text-teal-300"
                                :title="getInstructorLabel(assignment)"
                            >
                                {{ t('instructor') }}:
                                {{ getInstructorLabel(assignment) || '—' }}
                            </div>

                            <div
                                class="mt-1 flex flex-wrap justify-center
                                       gap-1 text-[10px] font-semibold"
                            >
                                <span
                                    class="rounded-sm border border-gray-400
                                           bg-amber-50 px-2 py-0.5
                                           text-amber-600
                                           dark:bg-yellow-950/50 dark:text-yellow-200"
                                >
                                    {{ getAssignmentVisibilityLabel(assignment.visibility) }}
                                </span>

                                <span
                                    class="rounded-sm border border-gray-400
                                           bg-emerald-100 px-2 py-0.5
                                           text-emerald-700
                                           dark:bg-emerald-900 dark:text-emerald-200"
                                >
                                    {{ getAssignmentGradingTypeLabel(assignment.grading_type) }}
                                </span>
                            </div>

                            <div
                                class="mt-2 flex flex-col justify-center
                                       text-center text-[11px]
                                       text-gray-700 dark:text-gray-400"
                            >
                                <div>
                                    {{ t('maxScore') }}:
                                    {{ assignment.max_score ?? 0 }}
                                </div>

                                <div>
                                    {{ t('quizAttemptItems') }}:
                                    {{ assignment.attempts_limit ?? 0 }}
                                </div>

                                <div
                                    v-if="assignment.due_at"
                                    :class="assignment.is_overdue ? 'text-red-500' : ''"
                                >
                                    {{ t('dueAt') }}:
                                    {{ formatDate(assignment.due_at) }}
                                </div>

                                <div>
                                    {{ t('submissions') }}:
                                    {{ assignment.submissions_count ?? 0 }}
                                </div>

                                <div>
                                    {{ t('images') }}:
                                    {{ assignment.images_count ?? 0 }}
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div
                            class="flex items-center justify-between
                                   border-t border-dashed border-slate-400
                                   px-3 py-2 dark:border-slate-500"
                        >
                            <div class="flex items-center gap-1">
                                <LeftToggle
                                    :isActive="assignment.left"
                                    :title="assignment.left ? t('enabled') : t('disabled')"
                                    @toggle-left="emit('toggle-left', assignment)"
                                />

                                <MainToggle
                                    :isActive="assignment.main"
                                    :title="assignment.main ? t('enabled') : t('disabled')"
                                    @toggle-main="emit('toggle-main', assignment)"
                                />

                                <RightToggle
                                    :isActive="assignment.right"
                                    :title="assignment.right ? t('enabled') : t('disabled')"
                                    @toggle-right="emit('toggle-right', assignment)"
                                />
                            </div>

                            <div class="flex items-center gap-1">
                                <ActivityToggle
                                    :isActive="assignment.activity"
                                    :title="assignment.activity ? t('enabled') : t('disabled')"
                                    @toggle-activity="emit('toggle-activity', assignment)"
                                />

                                <CloneIconButton
                                    :title="t('clone')"
                                    @clone="emit('clone', assignment)"
                                />

                                <IconEdit
                                    :href="route('admin.schoolAssignments.edit', {
                                        schoolAssignment: assignment.id
                                    })"
                                />

                                <DeleteIconButton
                                    @delete="emit('delete', assignment)"
                                />
                            </div>
                        </div>
                    </div>
                </template>
            </draggable>
        </div>

        <div
            v-else
            class="p-5 text-center text-slate-700 dark:text-slate-100"
        >
            {{ t('noData') }}
        </div>
    </div>
</template>
