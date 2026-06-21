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
    const newOrderIds = localQuizzes.value.map((quiz) => quiz.id)

    emit('update-sort-order', newOrderIds)
}

const toggleAll = (event) => {
    const checked = event.target.checked
    const ids = localQuizzes.value.map((quiz) => quiz.id)

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
               border border-slate-400 dark:border-slate-500 relative"
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
        <div
            v-if="localQuizzes.length"
            class="p-3"
        >
            <draggable
                v-model="localQuizzes"
                tag="div"
                item-key="id"
                handle=".handle"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                @end="handleDragEnd"
            >
                <template #item="{ element: quiz }">
                    <article
                        class="relative flex flex-col h-full rounded-md
                               border border-slate-400 dark:border-slate-500
                               bg-slate-50/70 dark:bg-slate-800/80 shadow-sm
                               hover:shadow-md transition-shadow duration-150"
                    >
                        <header
                            class="flex items-center justify-between px-2 py-1
                                   border-b border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-2">
                                <button
                                    type="button"
                                    class="handle text-slate-400 hover:text-slate-700
                                           dark:hover:text-slate-100"
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
                                    :title="`[${quiz.sort}] ${formatDate(quiz.published_at)}`"
                                >
                                    ID: {{ quiz.id }}
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span
                                    v-if="quiz.type"
                                    class="text-[10px] px-1.5 py-0.5 rounded-sm
                                           font-semibold border border-gray-400
                                           bg-fuchsia-100 dark:bg-fuchsia-900/40
                                           text-fuchsia-800 dark:text-fuchsia-200"
                                    :title="t('type')"
                                >
                                    {{ getQuizTypeLabel(quiz.type) }}
                                </span>

                                <input
                                    type="checkbox"
                                    :checked="selectedQuizzes.includes(quiz.id)"
                                    @change="emit('toggle-select', quiz.id)"
                                />
                            </div>
                        </header>
                        <div class="relative w-full h-40 bg-slate-200 dark:bg-slate-900">
                            <img
                                :src="getImageUrl(quiz)"
                                :alt="getImageAlt(quiz)"
                                :title="getPrimaryImage(quiz)?.caption || quiz.title || t('image')"
                                class="w-full h-full object-cover"
                            />
                        </div>
                        <div class="flex flex-col flex-1 px-3 py-2 space-y-1">
                            <a
                                :href="`/quizzes/${encodeURIComponent(quiz.slug || '')}`"
                                class="text-sm font-semibold text-fuchsia-800 dark:text-fuchsia-200
                                       hover:underline text-center"
                                target="_blank"
                                rel="noopener noreferrer"
                                :title="quiz.short || quiz.title"
                            >
                                {{ quiz.title || `ID: ${quiz.id}` }}
                            </a>
                            <div
                                class="text-[11px] text-slate-500 dark:text-slate-300"
                                :title="getQuizHierarchyTitle(quiz)"
                            >
                                {{ getQuizHierarchyTitle(quiz) }}
                            </div>
                            <div class="text-[11px] text-slate-600 dark:text-slate-200 space-y-0.5">
                                <div class="font-semibold">
                                    <span>{{ t('limitCount') }}: </span>
                                    <span class="text-red-800 dark:text-red-200">
                                        {{ quiz.attempts_limit ?? '—' }}
                                    </span>
                                </div>
                                <div class="font-semibold">
                                    <span>{{ t('limitMinutes') }}: </span>
                                    <span class="text-red-800 dark:text-red-200">
                                        {{ quiz.time_limit_minutes ?? '—' }}
                                    </span>
                                </div>
                                <div class="font-semibold">
                                    <span>{{ t('passScore') }}: </span>
                                    <span class="text-amber-800 dark:text-amber-200">
                                        {{ quiz.pass_score ?? '—' }}%
                                    </span>
                                </div>
                                <div class="grid grid-cols-2 gap-1 pt-1 text-[9px] text-center">
                                    <span
                                        class="border border-dashed border-slate-300
                                               dark:border-slate-600 rounded-sm px-1 py-0.5">
                                        {{ t('allQuestions') }}: {{ quiz.questions_count ?? 0 }}
                                    </span>
                                    <span
                                        class="border border-dashed border-slate-300
                                               dark:border-slate-600 rounded-sm px-1 py-0.5">
                                        {{ t('quizAttemptItems') }}: {{ quiz.attempts_count ?? 0 }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <footer
                            class="flex items-center justify-between px-3 py-2
                                   border-t border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-1">
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
                            <div class="flex items-center space-x-1">
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
                        </footer>
                    </article>
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
