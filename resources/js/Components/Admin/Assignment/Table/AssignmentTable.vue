<script setup>
import { defineProps, defineEmits, ref, watch, computed } from 'vue'
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
        default: () => []
    },
    selectedAssignments: {
        type: Array,
        default: () => []
    }
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
    'clone'
])

/** Локальная копия для vuedraggable */
const localAssignments = ref([])

watch(
    () => props.assignments,
    (newVal) => {
        localAssignments.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

const handleDragEnd = () => {
    const newOrderIds = localAssignments.value.map(assignment => assignment.id)
    emits('update-sort-order', newOrderIds)
}

/** Массовые чекбоксы */
const toggleAll = (event) => {
    const checked = event.target.checked
    const ids = localAssignments.value.map(l => l.id)
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
        day: 'numeric'
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

    // Если ничего нет — либо пустая строка, либо текст-заглушка
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

// 🔹 Хелперы
const getAssignmentStatusLabel = (status) => {
    return t(assignmentStatusLabelKeyMap[status] || status || '—')
}

const getAssignmentVisibilityLabel = (visibility) => {
    return t(assignmentVisibilityLabelKeyMap[visibility] || visibility || '—')
}

</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-200 dark:border-slate-600 relative">

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

        <div class="overflow-x-auto">
            <table
                v-if="assignments.length > 0"
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
                    <th class="px-2 py-3 first:pl-9 last:pr-9 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('image')">
                            <svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 512 512">
                                <path
                                    d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l96 0 32 0 208 0c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"
                                />
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-9 last:pr-9 whitespace-nowrap">
                        <div class="font-semibold text-left">
                            {{ t('title') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-9 last:pr-9 whitespace-nowrap">
                        <div class="font-semibold text-left">
                            {{ t('instructor') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-9 last:pr-9 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('visibility')">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-4 h-4"
                                height="24"
                                width="24"
                                viewBox="0 0 24 24">
                                <path class="fill-current text-amber-600 dark:text-amber-400"
                                      d="M20,1H4A3,3,0,0,0,1,4V20a3,3,0,0,0,3,3H20a3,3,0,0,0,3-3V4A3,3,0,0,0,20,1ZM4,3H20a1,1,0,0,1,1,1V15H16a1,1,0,0,0-1,1v2H9V16a1,1,0,0,0-1-1H3V4A1,1,0,0,1,4,3Z"></path>
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-9 last:pr-9 whitespace-nowrap">
                        <div class="font-medium text-center">{{ t('show') }}</div>
                    </th>
                    <th class="px-2 py-3 first:pl-9 last:pr-9 whitespace-nowrap">
                        <div class="font-semibold text-end">
                            {{ t('actions') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-9 last:pr-9 whitespace-nowrap">
                        <div class="text-center">
                            <input type="checkbox" @change="toggleAll" />
                        </div>
                    </th>
                </tr>
                </thead>

                <draggable
                    tag="tbody"
                    v-model="localAssignments"
                    @end="handleDragEnd"
                    item-key="id"
                    handle=".handle"
                >
                    <template #item="{ element: assignment }">
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
                            <td class="px-2 py-3 first:pl-9 last:pr-9 whitespace-nowrap">
                                <div
                                    class="text-center text-xs
                                           text-slate-800 dark:text-blue-200"
                                    :title="`[${assignment.locale}] : [${assignment.sort}] ${formatDate(assignment.published_at)}`">
                                    {{ assignment.id }}
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-9 last:pr-9 whitespace-nowrap">
                                <div class="flex justify-center">
                                    <template
                                        v-if="assignment.images && assignment.images.length">
                                        <img
                                            :src="getPrimaryImage(assignment).webp_url ||
                                            getPrimaryImage(assignment).url"
                                            :alt="getPrimaryImage(assignment).alt ||
                                                t('defaultImageAlt')"
                                            :title="
                                                getPrimaryImage(assignment).caption ||
                                                t('assignmentImage')"
                                            class="h-6 w-8 object-cover rounded-xs"
                                        />
                                    </template>
                                    <template v-else>
                                        <img
                                            src="/storage/assignment_images/default-image.png"
                                            :alt="t('defaultImageTitle')"
                                            class="h-6 w-8 object-cover rounded-xs"
                                        />
                                    </template>
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-9 last:pr-9">
                                <a
                                    :href="`/school/assignments/${encodeURIComponent(assignment.slug)}`"
                                    class="text-xs text-sky-600 dark:text-sky-200 hover:underline
                                           hover:text-sky-600 dark:hover:text-sky-200"
                                    target="_blank"
                                    rel="noopener noreferrer" :title="assignment.subtitle">
                                    {{ assignment.title }}
                                </a>
                            </td>
                            <td class="px-2 py-3 first:pl-9 last:pr-9 whitespace-nowrap">
                                <div
                                    class="text-center text-xs text-teal-600 dark:text-teal-400"
                                    :title="getAssignmentHierarchyTitle(assignment)">
                                    <template v-if="assignment.instructor">

                                        <!-- Первая строка: title -->
                                        <span>
                                            {{ assignment.instructor.title }}
                                        </span>

                                        <!-- Вторая строка: user.name или user.email -->
                                        <template v-if="assignment.instructor.user">
                                            :<br>
                                            <span class="text-gray-500 dark:text-gray-300">
                    {{ assignment.instructor.user.name || assignment.instructor.user.email }}
                                            </span>
                                        </template>
                                    </template>
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-9 last:pr-9 whitespace-nowrap">
                                <div
                                    class="text-center text-xs
                                           text-amber-600 dark:text-amber-400"
                        :title="`${t('status')} : ${getAssignmentStatusLabel(assignment.status)}`"
                                >
                                    {{ getAssignmentVisibilityLabel(assignment.visibility) }}
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-9 last:pr-9 whitespace-nowrap">
                                <div class="flex justify-center space-x-2">
                                    <LeftToggle :isActive="assignment.left"
                                        @toggle-left="$emit('toggle-left', assignment)"
                                        :title="assignment.left ? t('enabled') : t('disabled')"/>
                                    <MainToggle :isActive="assignment.main"
                                        @toggle-main="$emit('toggle-main', assignment)"
                                        :title="assignment.main ? t('enabled') : t('disabled')"/>
                                    <RightToggle :isActive="assignment.right"
                                         @toggle-right="$emit('toggle-right', assignment)"
                                         :title="assignment.right ? t('enabled') : t('disabled')"/>
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-9 last:pr-9 whitespace-nowrap">
                                <div class="flex justify-end space-x-2">
                                    <ActivityToggle
                                        :isActive="assignment.activity"
                                        @toggle-activity="$emit('toggle-activity', assignment)"
                                        :title="assignment.activity ? t('enabled') : t('disabled')"
                                    />
                                    <CloneIconButton @clone="$emit('clone', assignment)" />
                                    <IconEdit
                                        :href="route('admin.assignments.edit', assignment.id)"
                                    />
                                    <DeleteIconButton
                                        @delete="$emit('delete', assignment.id)"
                                    />
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-9 last:pr-9 whitespace-nowrap">
                                <div class="text-center">
                                    <input
                                        type="checkbox"
                                        :checked="selectedAssignments.includes(assignment.id)"
                                        @change="$emit('toggle-select', assignment.id)"
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
