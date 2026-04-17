<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'
import ActivityToggle from '@/Components/Admin/Buttons/ActivityToggle.vue'
import CloneIconButton from '@/Components/Admin/Buttons/CloneIconButton.vue'
import RightToggle from '@/Components/Admin/Buttons/RightToggle.vue'
import MainToggle from '@/Components/Admin/Buttons/MainToggle.vue'
import LeftToggle from '@/Components/Admin/Buttons/LeftToggle.vue'

const { t } = useI18n()

const props = defineProps({
    assignments: {
        type: Array,
        default: () => [],
    },
    selectedAssignments: {
        type: Array,
        default: () => [],
    },
})

const emits = defineEmits([
    'toggle-left',
    'toggle-main',
    'toggle-right',
    'toggle-activity',
    'edit',
    'delete',
    'update-sort-order',
    'toggle-select',
    'toggle-all',
    'clone',
])

/** Локальная копия для vuedraggable */
const localAssignments = ref([])

watch(
    () => props.assignments,
    (newVal) => {
        localAssignments.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true },
)

const handleDragEnd = () => {
    const newOrderIds = localAssignments.value.map((assignment) => assignment.id)
    emits('update-sort-order', newOrderIds)
}

/** Массовые чекбоксы */
const toggleAll = (event) => {
    const checked = event.target.checked
    const ids = localAssignments.value.map((a) => a.id)
    emits('toggle-all', { ids, checked })
}

/** Основное изображение (минимальный order) */
const getPrimaryImage = (assignment) => {
    if (assignment.images && assignment.images.length) {
        return [...assignment.images].sort((a, b) => a.order - b.order)[0]
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
        day: 'numeric',
    })
}

// Формируем подпись "Курс / Модуль / Урок" безопасно
const getAssignmentHierarchyTitle = (assignment) => {
    const parts = []

    if (assignment.course && assignment.course.title) {
        parts.push(assignment.course.title)
    }

    if (assignment.module && assignment.module.title) {
        parts.push(assignment.module.title)
    }

    if (assignment.lesson && assignment.lesson.title) {
        parts.push(assignment.lesson.title)
    }

    return parts.length ? parts.join(' / ') : t('noHierarchyData')
}

// Подпись "Инструктор: user" — "Title: Name/Email"
const getInstructorLabel = (assignment) => {
    if (!assignment.instructor) return ''

    const title = assignment.instructor.title || ''
    const userName =
        assignment.instructor.user?.name ||
        assignment.instructor.user?.email ||
        ''

    if (title && userName) {
        return `${title}: ${userName}`
    }

    return title || userName || ''
}

// 🔹 Мапы "значение из БД" → "ключ перевода" (flat i18n)
const assignmentStatusLabelKeyMap = {
    draft: 'statusDraft',
    published: 'statusPublished',
    archived: 'statusArchived',
}

const assignmentVisibilityLabelKeyMap = {
    public: 'public',
    enrolled: 'enrolled',
    private: 'private',
}

// 🔹 Хелперы (без обращения к t('—'), чтобы не было warning'ов)
const getAssignmentStatusLabel = (status) => {
    const key = assignmentStatusLabelKeyMap[status]
    if (key) return t(key)
    return status || '—'
}

const getAssignmentVisibilityLabel = (visibility) => {
    const key = assignmentVisibilityLabelKeyMap[visibility]
    if (key) return t(key)
    return visibility || '—'
}
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-400 dark:border-slate-500 relative"
    >

        <!-- верх: выбранные + selectAll -->
        <div class="flex items-center justify-between px-3 py-2
                    border-b border-slate-400 dark:border-slate-500">
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('selected') }}: {{ selectedAssignments.length }}
            </div>
            <div class="flex items-center space-x-2">
                <label class="flex items-center text-xs
                              text-slate-600 dark:text-slate-200 cursor-pointer">
                    <span>{{ t('selectAll') }}</span>
                    <input type="checkbox" class="mx-2" @change="toggleAll" />
                </label>
            </div>
        </div>

        <div v-if="localAssignments.length" class="p-3">
            <draggable
                tag="div"
                v-model="localAssignments"
                @end="handleDragEnd"
                item-key="id"
                handle=".handle"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4"
            >
                <template #item="{ element: assignment }">
                    <div
                        class="relative flex flex-col h-full rounded-md
                               border border-slate-400 dark:border-slate-500
                               bg-slate-50/70 dark:bg-slate-800/80 shadow-sm
                               hover:shadow-md transition-shadow duration-150"
                    >
                        <!-- Верхняя панель: drag, ID, дата, чекбокс -->
                        <div
                            class="flex items-center justify-between px-2 py-1
                                   border-b border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-2">
                                <!-- drag handle -->
                                <button
                                    type="button"
                                    class="handle text-slate-400 hover:text-slate-700 dark:hover:text-slate-100"
                                    :title="t('dragDrop')"
                                >
                                    <svg
                                        class="w-4 h-4"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
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
                                    :title="`[${assignment.locale}] : [${assignment.sort}] ${formatDate(assignment.published_at)}`"
                                >
                                    ID: {{ assignment.id }}
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                <span
                                    v-if="assignment.visibility"
                                    class="text-[10px] px-1.5 py-0.5 rounded-sm
                                           border border-gray-400
                                           bg-amber-100 dark:bg-amber-900/50
                                           text-amber-700 dark:text-amber-300"
                                    :title="`${t('status')} : ${getAssignmentStatusLabel(assignment.status)}`"
                                >
                                    {{ getAssignmentVisibilityLabel(assignment.visibility) }}
                                </span>

                                <input
                                    type="checkbox"
                                    :checked="selectedAssignments.includes(assignment.id)"
                                    @change="$emit('toggle-select', assignment.id)"
                                />
                            </div>
                        </div>

                        <!-- Изображение -->
                        <div class="relative w-full h-auto bg-slate-200 dark:bg-slate-900">
                            <template v-if="assignment.images && assignment.images.length">
                                <img
                                    :src="getPrimaryImage(assignment).webp_url || getPrimaryImage(assignment).url"
                                    :alt="getPrimaryImage(assignment).alt || t('defaultImageAlt')"
                                    :title="getPrimaryImage(assignment).caption || t('assignmentImage')"
                                    class="w-full h-full object-cover"
                                />
                            </template>
                            <template v-else>
                                <img
                                    src="/storage/assignment_images/default-image.png"
                                    :alt="t('defaultImageTitle')"
                                    class="w-full h-full object-cover"
                                />
                            </template>
                        </div>

                        <!-- Контент -->
                        <div class="flex flex-col flex-1 px-3 py-2 space-y-1">
                            <!-- Заголовок -->
                            <a
                                :href="`/school/assignments/${encodeURIComponent(assignment.slug)}`"
                                class="text-sm font-semibold text-sky-700 dark:text-sky-200 hover:underline
                                       line-clamp-2"
                                target="_blank"
                                rel="noopener noreferrer"
                                :title="assignment.subtitle || assignment.title"
                            >
                                {{ assignment.title }}
                            </a>

                            <!-- Иерархия (курс / модуль / урок) -->
                            <div
                                v-if="getAssignmentHierarchyTitle(assignment)"
                                class="text-[11px] text-slate-500 dark:text-slate-300 line-clamp-2"
                                :title="getAssignmentHierarchyTitle(assignment)"
                            >
                                {{ getAssignmentHierarchyTitle(assignment) }}
                            </div>

                            <!-- Инструктор -->
                            <div
                                v-if="getInstructorLabel(assignment)"
                                class="text-[11px] text-teal-700 dark:text-teal-300"
                                :title="getInstructorLabel(assignment)"
                            >
                                {{ getInstructorLabel(assignment) }}
                            </div>
                        </div>

                        <!-- Тогглы и действия -->
                        <div
                            class="flex items-center justify-between px-3 py-2
                                   border-t border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-1">
                                <LeftToggle
                                    :isActive="assignment.left"
                                    @toggle-left="$emit('toggle-left', assignment)"
                                    :title="assignment.left ? t('enabled') : t('disabled')"
                                />
                                <MainToggle
                                    :isActive="assignment.main"
                                    @toggle-main="$emit('toggle-main', assignment)"
                                    :title="assignment.main ? t('enabled') : t('disabled')"
                                />
                                <RightToggle
                                    :isActive="assignment.right"
                                    @toggle-right="$emit('toggle-right', assignment)"
                                    :title="assignment.right ? t('enabled') : t('disabled')"
                                />
                            </div>

                            <div class="flex items-center space-x-1">
                                <ActivityToggle
                                    :isActive="assignment.activity"
                                    @toggle-activity="$emit('toggle-activity', assignment)"
                                    :title="assignment.activity ? t('enabled') : t('disabled')"
                                />
                                <CloneIconButton @clone="$emit('clone', assignment)" />
                                <IconEdit :href="route('admin.assignments.edit', assignment.id)" />
                                <DeleteIconButton @delete="$emit('delete', assignment.id)" />
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
