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
    quizzes: {
        type: Array,
        default: () => [],
    },
    selectedQuizzes: {
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
    emits('update-sort-order', newOrderIds)
}

/** Массовые чекбоксы */
const toggleAll = (event) => {
    const checked = event.target.checked
    const ids = localQuizzes.value.map((q) => q.id)
    emits('toggle-all', { ids, checked })
}

/** Основное изображение (минимальный order) */
const getPrimaryImage = (quiz) => {
    if (quiz.images && quiz.images.length) {
        return [...quiz.images].sort((a, b) => a.order - b.order)[0]
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

// Подпись "Курс / Модуль / Урок"
const getQuizHierarchyTitle = (quiz) => {
    const parts = []

    if (quiz.course && quiz.course.title) {
        parts.push(quiz.course.title)
    }

    if (quiz.module && quiz.module.title) {
        parts.push(quiz.module.title)
    }

    if (quiz.lesson && quiz.lesson.title) {
        parts.push(quiz.lesson.title)
    }

    return parts.length ? parts.join(' / ') : t('noHierarchyData')
}

// 🔹 Мапа "значение из БД" → "ключ перевода" (flat i18n)
const quizTypeLabelKeyMap = {
    graded: 'quizTypeGraded',     // graded — с оценкой
    practice: 'quizTypePractice', // practice — тренировочный
}

// 🔹 Хелпер без прямого t('—'), чтобы не ловить warning'и
const getQuizTypeLabel = (type) => {
    const key = quizTypeLabelKeyMap[type]
    if (key) return t(key)
    return type || '—'
}
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-400 dark:border-slate-500 relative"
    >
        <!-- Панель сверху: счётчик выбранных и select all -->
        <div
            class="flex items-center justify-between px-3 py-2
                   border-b border-slate-400 dark:border-slate-500"
        >
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('selected') }}: {{ selectedQuizzes.length }}
            </div>
            <div class="flex items-center space-x-2">
                <label
                    class="flex items-center
                           text-xs text-slate-600 dark:text-slate-200 cursor-pointer"
                >
                    <span>{{ t('selectAll') }}</span>
                    <input type="checkbox" class="mx-2" @change="toggleAll" />
                </label>
            </div>
        </div>

        <div v-if="localQuizzes.length" class="p-3">
            <draggable
                tag="div"
                v-model="localQuizzes"
                @end="handleDragEnd"
                item-key="id"
                handle=".handle"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4"
            >
                <template #item="{ element: quiz }">
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
                                    :title="`[${quiz.locale}] : [${quiz.sort}] ${formatDate(quiz.published_at)}`"
                                >
                                    ID: {{ quiz.id }}
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                <span
                                    v-if="quiz.type"
                                    class="text-[10px] px-1.5 py-0.5 rounded-sm
                                           border border-gray-400
                                           bg-emerald-100 dark:bg-emerald-900/40
                                           text-emerald-700 dark:text-emerald-200"
                                    :title="t('type')"
                                >
                                    {{ getQuizTypeLabel(quiz.type) }}
                                </span>

                                <input
                                    type="checkbox"
                                    :checked="selectedQuizzes.includes(quiz.id)"
                                    @change="$emit('toggle-select', quiz.id)"
                                />
                            </div>
                        </div>

                        <!-- Изображение -->
                        <div class="relative w-full h-auto bg-slate-200 dark:bg-slate-900">
                            <template v-if="quiz.images && quiz.images.length">
                                <img
                                    :src="
                                        getPrimaryImage(quiz).webp_url ||
                                        getPrimaryImage(quiz).url
                                    "
                                    :alt="
                                        getPrimaryImage(quiz).alt ||
                                        t('defaultImageAlt')
                                    "
                                    :title="
                                        getPrimaryImage(quiz).caption ||
                                        t('image')
                                    "
                                    class="w-full h-full object-cover"
                                />
                            </template>
                            <template v-else>
                                <img
                                    src="/storage/quiz_images/default-image.png"
                                    :alt="t('defaultImageTitle')"
                                    class="w-full h-full object-cover"
                                />
                            </template>
                        </div>

                        <!-- Контент -->
                        <div class="flex flex-col flex-1 px-3 py-2 space-y-1">
                            <!-- Заголовок -->
                            <a
                                :href="`/quizzes/${encodeURIComponent(quiz.slug)}`"
                                class="text-sm font-semibold text-sky-700 dark:text-sky-200 hover:underline
                                       line-clamp-2"
                                target="_blank"
                                rel="noopener noreferrer"
                                :title="quiz.short || quiz.title"
                            >
                                {{ quiz.title }}
                            </a>

                            <!-- Иерархия (курс / модуль / урок) -->
                            <div
                                v-if="getQuizHierarchyTitle(quiz)"
                                class="text-[11px] text-slate-500 dark:text-slate-300 line-clamp-2"
                                :title="getQuizHierarchyTitle(quiz)"
                            >
                                {{ getQuizHierarchyTitle(quiz) }}
                            </div>

                            <!-- Параметры квиза -->
                            <div class="text-[11px] text-slate-600 dark:text-slate-200 space-y-0.5">
                                <div>
                                    <span class="font-semibold">{{ t('limitCount') }}: </span>
                                    <span class="text-amber-800 dark:text-amber-200">
                                            {{ quiz.attempts_limit ?? '—' }}
                                        </span>
                                </div>
                                <div>
                                    <span class="font-semibold">{{ t('limitMinutes') }}: </span>
                                    <span class="text-amber-800 dark:text-amber-200">
                                            {{ quiz.time_limit_minutes ?? '—' }}
                                        </span>
                                </div>
                                <div>
                                    <span class="font-semibold">{{ t('passScore') }}: </span>
                                    <span class="text-amber-800 dark:text-amber-200">
                                            {{ quiz.pass_score ?? '—' }}%
                                        </span>
                                </div>
                            </div>
                        </div>

                        <!-- Тогглы и действия -->
                        <div
                            class="flex items-center justify-between px-3 py-2
                                   border-t border-dashed border-slate-400 dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-1">
                                <LeftToggle
                                    :isActive="quiz.left"
                                    @toggle-left="$emit('toggle-left', quiz)"
                                    :title="quiz.left ? t('enabled') : t('disabled')"
                                />
                                <MainToggle
                                    :isActive="quiz.main"
                                    @toggle-main="$emit('toggle-main', quiz)"
                                    :title="quiz.main ? t('enabled') : t('disabled')"
                                />
                                <RightToggle
                                    :isActive="quiz.right"
                                    @toggle-right="$emit('toggle-right', quiz)"
                                    :title="quiz.right ? t('enabled') : t('disabled')"
                                />
                            </div>

                            <div class="flex items-center space-x-1">
                                <ActivityToggle
                                    :isActive="quiz.activity"
                                    @toggle-activity="$emit('toggle-activity', quiz)"
                                    :title="quiz.activity ? t('enabled') : t('disabled')"
                                />
                                <CloneIconButton @clone="$emit('clone', quiz)" />
                                <IconEdit :href="route('admin.quizzes.edit', quiz.id)" />
                                <DeleteIconButton
                                    @delete="$emit('delete', quiz.id, quiz.title)"
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
