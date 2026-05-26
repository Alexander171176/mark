<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'
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

const localAssignments = ref([])

watch(
    () => props.assignments,
    (newVal) => {
        localAssignments.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true },
)

const handleDragEnd = () => {
    emit('update-sort-order', localAssignments.value.map(assignment => assignment.id))
}

const toggleAll = (event) => {
    emit('toggle-all', {
        ids: localAssignments.value.map(assignment => assignment.id),
        checked: event.target.checked,
    })
}

const getPrimaryImage = (assignment) => {
    if (assignment.images && assignment.images.length) {
        return [...assignment.images].sort((a, b) => (a.order ?? 0) - (b.order ?? 0))[0]
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

const getInstructorLabel = (assignment) => {
    if (!assignment.instructor) return ''

    const title = assignment.instructor.title || ''
    const userName = assignment.instructor.user?.name || assignment.instructor.user?.email || ''

    if (title && userName) return `${title}: ${userName}`

    return title || userName || ''
}

const getCourseLabel = (assignment) => {
    return assignment.course?.title || ''
}

const getModuleLabel = (assignment) => {
    return assignment.module?.title || ''
}

const getLessonLabel = (assignment) => {
    return assignment.lesson?.title || ''
}

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

const getAssignmentStatusLabel = (status) => t(assignmentStatusLabelKeyMap[status] || status || 'no')
const getAssignmentVisibilityLabel = (visibility) => t(assignmentVisibilityLabelKeyMap[visibility] || visibility || 'no')
const getAssignmentGradingTypeLabel = (gradingType) => t(assignmentGradingTypeLabelKeyMap[gradingType] || gradingType || 'no')
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
                {{ t('selected') }}: {{ selectedAssignments.length }}
            </div>

            <label
                v-if="localAssignments.length"
                class="flex items-center text-xs text-slate-600
                       dark:text-slate-200 cursor-pointer"
            >
                <span>{{ t('selectAll') }}</span>
                <input type="checkbox" class="mx-2" @change="toggleAll">
            </label>
        </div>

        <div v-if="localAssignments.length" class="p-3">
            <draggable
                v-model="localAssignments"
                tag="div"
                item-key="id"
                handle=".handle"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                @end="handleDragEnd"
            >
                <template #item="{ element: assignment }">
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
                                    class="handle text-slate-400
                                           hover:text-slate-700 dark:hover:text-slate-100"
                                    :title="t('dragDrop')"
                                >
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" />
                                    </svg>
                                </button>

                                <div
                                    class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm
                                           border border-gray-400
                                           bg-slate-200 dark:bg-slate-700
                                           text-slate-800 dark:text-blue-100"
                                    :title="`[sort: ${assignment.sort}] ${formatDate(assignment.published_at)}`"
                                >
                                    ID: {{ assignment.id }}
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span
                                    class="text-[10px] px-1.5 py-0.5 rounded-sm
                                           border border-gray-400
                                           bg-fuchsia-100 dark:bg-fuchsia-900/50
                                           text-fuchsia-800 dark:text-fuchsia-400"
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
                        <div class="relative w-full h-32 bg-slate-200 dark:bg-slate-900">
                <img
                    v-if="assignment.images && assignment.images.length"
                    :src="getPrimaryImage(assignment)?.webp_url || getPrimaryImage(assignment)?.url"
                    :alt="getPrimaryImage(assignment)?.alt || t('defaultImageAlt')"
                    :title="getPrimaryImage(assignment)?.caption || t('assignmentImage')"
                    class="w-full h-full object-cover"
                >
                            <img
                                v-else
                                src="/storage/school_assignment_images/default-image.png"
                                :alt="t('defaultImageTitle')"
                                class="w-full h-full object-cover"
                            >
                        </div>
                        <div class="flex flex-col flex-1 px-3 py-2 space-y-1">
                            <a
                                :href="`/school/assignments/${encodeURIComponent(assignment.slug)}`"
                                class="text-sm font-semibold text-sky-700 dark:text-sky-200
                                       hover:underline line-clamp-2 text-center"
                                target="_blank"
                                rel="noopener noreferrer"
                                :title="assignment.subtitle || assignment.title"
                            >
                                {{ assignment.title || `ID: ${assignment.id}` }}
                            </a>
                            <div class="text-[10px] text-slate-600 dark:text-slate-300
                                        truncate text-center"
                                 :title="assignment.slug">
                                {{ assignment.slug }}
                            </div>
                            <div
                                class="text-xs text-slate-600 dark:text-slate-300 font-semibold"
                                :title="getLessonLabel(assignment)"
                            >
                                {{ t('lesson') }}: {{ getLessonLabel(assignment) || '—' }}
                            </div>
                            <div
                                class="text-xs text-slate-600 dark:text-slate-300 font-semibold"
                                :title="getModuleLabel(assignment)"
                            >
                                {{ t('module') }}: {{ getModuleLabel(assignment) || '—' }}
                            </div>
                            <div
                                class="text-xs text-slate-600 dark:text-slate-300 font-semibold"
                                :title="getCourseLabel(assignment)"
                            >
                                {{ t('course') }}: {{ getCourseLabel(assignment) || '—' }}
                            </div>
                            <div
                                class="text-xs text-teal-600 dark:text-teal-300
                                       font-semibold text-center"
                                :title="getInstructorLabel(assignment)"
                            >
                                {{ t('instructor') }}: {{ getInstructorLabel(assignment) || '—' }}
                            </div>
                            <div class="flex flex-wrap justify-center gap-1 mt-1
                                        text-[10px] font-semibold">
                                <span
                                    class="px-2 py-0.5 rounded-sm
                                           bg-amber-50 dark:bg-yellow-950/50
                                           text-amber-600 dark:text-yellow-200
                                           border border-gray-400">
                                    {{ getAssignmentVisibilityLabel(assignment.visibility) }}
                                </span>

                                <span
                                    class="px-2 py-0.5 rounded-sm
                                           bg-emerald-100 dark:bg-emerald-900
                                           border border-gray-400 text-emerald-700
                                           dark:text-emerald-200">
                                    {{ getAssignmentGradingTypeLabel(assignment.grading_type) }}
                                </span>
                            </div>
                            <div
                                class="flex flex-col justify-center mt-2
                                       text-gray-700 dark:text-gray-400 text-center text-[11px]">
                                <div>
                                    {{ t('maxScore') }}: {{ assignment.max_score ?? 0 }}
                                </div>
                                <div>
                                    {{ t('quizAttemptItems') }}: {{ assignment.attempts_limit ?? 0 }}
                                </div>
                                <div v-if="assignment.due_at"
                                     :class="assignment.is_overdue ? 'text-red-500' : ''">
                                    {{ t('dueAt') }}: {{ formatDate(assignment.due_at) }}
                                </div>
                            </div>
                        </div>
                        <div
                            class="flex items-center justify-between px-3 py-2
                                   border-t border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-1">
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
                            <div class="flex items-center space-x-1">
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
                                    :href="route('admin.schoolAssignments.edit',
                                    { schoolAssignment: assignment.id })"
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

        <div v-else class="p-5 text-center text-slate-700 dark:text-slate-100">
            {{ t('noData') }}
        </div>
    </div>
</template>
