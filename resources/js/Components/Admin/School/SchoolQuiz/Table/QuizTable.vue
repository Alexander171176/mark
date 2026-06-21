<script setup>
import { defineEmits, defineProps, ref, watch } from 'vue'
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
    quizzes: {
        type: Array,
        default: () => [],
    },

    selectedQuizzes: {
        type: Array,
        default: () => [],
    },
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

const localQuizzes = ref([])

watch(
    () => props.quizzes,
    (newVal) => {
        localQuizzes.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true },
)

const handleDragEnd = () => {
    const newOrderIds = localQuizzes.value.map(quiz => quiz.id)

    emit('update-sort-order', newOrderIds)
}

const toggleAll = (event) => {
    const checked = event.target.checked
    const ids = localQuizzes.value.map(quiz => quiz.id)

    emit('toggle-all', { ids, checked })
}

const getPrimaryImage = (quiz) => {
    if (!quiz.images?.length) return null

    return [...quiz.images].sort((a, b) => (a.order ?? 0) - (b.order ?? 0))[0]
}

const getImageUrl = (quiz) => {
    const image = getPrimaryImage(quiz)

    return image?.webp_url
        || image?.thumb_url
        || image?.image_url
        || image?.url
        || '/storage/school/school_quiz_images/default-image.png'
}

const getImageAlt = (quiz) => {
    const image = getPrimaryImage(quiz)

    return image?.alt || quiz.title || t('defaultImageAlt')
}

const formatDate = (dateStr) => {
    if (!dateStr) return ''

    const date = new Date(dateStr)

    if (Number.isNaN(date.getTime())) return ''

    return date.toLocaleDateString('ru-RU', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    })
}

const getQuizHierarchyTitle = (quiz) => {
    const parts = [
        quiz.course?.title,
        quiz.module?.title,
        quiz.lesson?.title,
    ].filter(Boolean)

    return parts.length ? parts.join(' / ') : t('noHierarchyData')
}

const quizTypeLabelKeyMap = {
    graded: 'quizTypeGraded',
    practice: 'quizTypePractice',
}

const getQuizTypeLabel = (type) => {
    const key = quizTypeLabelKeyMap[type]

    return key ? t(key) : type || '—'
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
                {{ t('selected') }}: {{ selectedQuizzes.length }}
            </div>
            <label
                class="flex items-center text-xs text-slate-600
                       dark:text-slate-200 cursor-pointer"
            >
                <span>{{ t('selectAll') }}</span>

                <input
                    type="checkbox"
                    class="mx-2"
                    @change="toggleAll"
                />
            </label>
        </div>
        <div class="overflow-x-auto">
            <table
                v-if="quizzes.length > 0"
                class="table-auto w-full text-slate-700 dark:text-slate-100"
            >
                <thead
                    class="text-sm uppercase bg-slate-200 dark:bg-cyan-900
                           border border-solid border-gray-300 dark:border-gray-700"
                >
                <tr>
                    <th class="px-2 py-3 w-px">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-4 h-4 fill-current text-slate-800 dark:text-slate-200"
                            height="24"
                            width="24"
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
                    <th class="px-2 py-3 w-px">
                        <div class="font-medium text-center">
                            {{ t('id') }}
                        </div>
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
                        <div class="font-semibold text-left">
                            {{ t('title') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-left">
                            {{ t('context') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-left">
                            {{ t('parametersHeader') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-medium text-center">
                            {{ t('show') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-end">
                            {{ t('actions') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="text-center">
                            <input type="checkbox" @change="toggleAll" />
                        </div>
                    </th>
                </tr>
                </thead>
                <draggable
                    v-model="localQuizzes"
                    tag="tbody"
                    item-key="id"
                    handle=".handle"
                    @end="handleDragEnd"
                >
                    <template #item="{ element: quiz }">
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
                                    class="text-center text-xs
                                           text-slate-800 dark:text-blue-200"
                                    :title="`[${quiz.sort}] ${formatDate(quiz.published_at)}`"
                                >
                                    {{ quiz.id }}
                                </div>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="flex justify-center">
                                    <img
                                        :src="getImageUrl(quiz)"
                                        :alt="getImageAlt(quiz)"
                                        :title="getPrimaryImage(quiz)?.caption || quiz.title || t('image')"
                                        class="h-8 w-10 object-cover rounded-xs"
                                    />
                                </div>
                            </td>
                            <td class="px-2 py-3">
                                <a
                                    :href="`/quizzes/${encodeURIComponent(quiz.slug || '')}`"
                                    class="text-xs text-fuchsia-800 dark:text-fuchsia-200
                                           hover:underline hover:text-sky-600
                                           dark:hover:text-sky-200"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    :title="quiz.short || quiz.title"
                                >
                                    {{ quiz.title || `ID: ${quiz.id}` }}
                                </a>
                                <div
                                    v-if="quiz.slug"
                                    class="text-[10px] text-slate-500 dark:text-slate-300"
                                >
                                    {{ quiz.slug }}
                                </div>
                            </td>
                            <td class="px-2 py-3">
                                <div
                                    class="text-xs text-teal-700 dark:text-teal-300"
                                    :title="getQuizHierarchyTitle(quiz)"
                                >
                                    {{ getQuizHierarchyTitle(quiz) }}
                                </div>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="text-[11px] leading-snug
                                            text-slate-700 dark:text-slate-100">
                                    <div>
                                        <span class="font-semibold">{{ t('type') }}: </span>
                                        <span class="text-fuchsia-800 dark:text-fuchsia-200">
                                            {{ getQuizTypeLabel(quiz.type) }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="font-semibold">{{ t('limitCount') }}: </span>
                                        <span class="text-red-800 dark:text-red-200">
                                            {{ quiz.attempts_limit ?? '—' }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="font-semibold">{{ t('limitMinutes') }}: </span>
                                        <span class="text-red-800 dark:text-red-200">
                                            {{ quiz.time_limit_minutes ?? '—' }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="font-semibold">{{ t('passScore') }}: </span>
                                        <span class="text-amber-800 dark:text-amber-200">
                                            {{ quiz.pass_score ?? '—' }}%
                                        </span>
                                    </div>
                                    <div class="pt-1 fle flex-col
                                                text-[10px] text-slate-500 dark:text-slate-300">
                                        <div>
                                            {{ t('allQuestions') }}:
                                            {{ quiz.questions_count ?? 0 }},
                                        </div>
                                        <div>
                                            {{ t('quizAttemptItems') }}:
                                            {{ quiz.attempts_count ?? 0 }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="flex justify-center space-x-2">
                                    <LeftToggle
                                        :isActive="quiz.left"
                                        :title="quiz.left ? t('enabled') : t('disabled')"
                                        @toggle-left="emit('toggle-left', quiz)"
                                    />
                                    <MainToggle
                                        :isActive="quiz.main"
                                        :title="quiz.main ? t('enabled') : t('disabled')"
                                        @toggle-main="emit('toggle-main', quiz)"
                                    />
                                    <RightToggle
                                        :isActive="quiz.right"
                                        :title="quiz.right ? t('enabled') : t('disabled')"
                                        @toggle-right="emit('toggle-right', quiz)"
                                    />
                                </div>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="flex justify-end space-x-2">
                                    <ActivityToggle
                                        :isActive="quiz.activity"
                                        :title="quiz.activity ? t('enabled') : t('disabled')"
                                        @toggle-activity="emit('toggle-activity', quiz)"
                                    />
                                    <CloneIconButton
                                        :title="t('clone')"
                                        @clone="emit('clone', quiz)"
                                    />
                                    <IconEdit
                                        :href="route('admin.schoolQuizzes.edit', {
                                            schoolQuiz: quiz.id,
                                        })"
                                    />
                                    <DeleteIconButton
                                        :title="t('delete')"
                                        @delete="emit('delete', quiz)"
                                    />
                                </div>
                            </td>
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="text-center">
                                    <input
                                        type="checkbox"
                                        :checked="selectedQuizzes.includes(quiz.id)"
                                        @change="emit('toggle-select', quiz.id)"
                                    />
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
