<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import ActivityToggle from '@/Components/Admin/Buttons/ActivityToggle.vue'
import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'
import CloneIconButton from '@/Components/Admin/Buttons/CloneIconButton.vue'

const { t } = useI18n()

const props = defineProps({
    lessons: {
        type: Array,
        default: () => []
    },
    selectedLessons: {
        type: Array,
        default: () => []
    }
})

const emits = defineEmits([
    'toggle-activity',
    'delete',
    'clone',
    'update-sort-order',
    'toggle-select',
    'toggle-all'
])

// --- Мапы "значение из БД" → "ключ перевода" (как в LessonTable) ---
const lessonStatusLabelKeyMap = {
    draft: 'statusDraft',
    published: 'statusPublished',
    archived: 'statusArchived'
}

const lessonAvailabilityLabelKeyMap = {
    unlisted: 'availabilityUnlisted',
    public: 'availabilityPublic',
    private: 'availabilityPrivate'
}

// access_type: free | paid | bonus
const lessonAccessTypeLabelKeyMap = {
    free: 'free',
    paid: 'paid',
    bonus: 'bonus'
}

// preview_mode: none | full | percent | duration | chars
const lessonPreviewModeLabelKeyMap = {
    none: 'previewNone',
    full: 'previewFull',
    percent: 'previewPercent',
    duration: 'previewDuration',
    chars: 'previewChars'
}

// --- Хелперы ---
const getLessonStatusLabel = (status) => {
    if (!status) return '—'
    const key = lessonStatusLabelKeyMap[status]
    return key ? t(key) : status
}

const getLessonAvailabilityLabel = (availability) => {
    if (!availability) return '—'
    const key = lessonAvailabilityLabelKeyMap[availability]
    return key ? t(key) : availability
}

const getLessonAccessTypeLabel = (accessType) => {
    if (!accessType) return '—'
    const key = lessonAccessTypeLabelKeyMap[accessType]
    return key ? t(key) : accessType
}

const getLessonPreviewModeLabel = (previewMode) => {
    if (!previewMode) return '—'
    const key = lessonPreviewModeLabelKeyMap[previewMode]
    return key ? t(key) : previewMode
}

// --- Локальная копия для vuedraggable ---
const localLessons = ref([])

watch(
    () => props.lessons,
    (newVal) => {
        localLessons.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

// --- Drag end: отдаём массив ID вверх ---
const handleDragEnd = () => {
    const newOrderIds = localLessons.value.map(lesson => lesson.id)
    emits('update-sort-order', newOrderIds)
}

// --- Массовый выбор ---
const toggleAll = (event) => {
    const checked = event.target.checked
    const ids = localLessons.value.map(l => l.id)
    emits('toggle-all', { ids, checked })
}

// --- Главное изображение урока ---
const getPrimaryImage = (lesson) => {
    if (lesson.images && lesson.images.length) {
        return [...lesson.images].sort((a, b) => a.order - b.order)[0]
    }
    return null
}

// --- Форматирование даты ---
const formatDate = (dateStr) => {
    if (!dateStr) return ''
    const d = new Date(dateStr)
    if (isNaN(d)) return ''
    return d.toLocaleDateString('ru-RU', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-400 dark:border-slate-500 relative">

        <!-- Верхняя панель: кол-во выбранных + чекбокс -->
        <div class="flex items-center justify-between px-3 py-2
                    border-b border-slate-400 dark:border-slate-500">
            <div class="text-xs text-slate-600 dark:text-slate-200">
                {{ t('selected') }}: {{ selectedLessons.length }}
            </div>

            <label class="flex items-center text-xs text-slate-600
                          dark:text-slate-200 cursor-pointer">
                <span>{{ t('selectAll') }}</span>
                <input type="checkbox" class="mx-2" @change="toggleAll" />
            </label>
        </div>

        <div v-if="localLessons.length" class="p-3">
            <draggable
                tag="div"
                v-model="localLessons"
                item-key="id"
                @end="handleDragEnd"
                handle=".drag-handle"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4"
            >
                <template #item="{ element: lesson }">
                    <div
                        class="relative flex flex-col h-full rounded-md
                               border border-slate-400 dark:border-slate-500
                               bg-slate-50/70 dark:bg-slate-800/80 shadow-sm
                               hover:shadow-md transition-shadow duration-150">

                        <!-- Верхняя панель карточки -->
                        <div class="flex items-center justify-between px-2 py-1
                                    border-b border-dashed border-slate-400 dark:border-slate-500">
                            <div class="flex items-center space-x-2">

                                <!-- drag handle -->
                                <button
                                    type="button"
                                    class="drag-handle text-slate-400 hover:text-slate-700
                                           dark:hover:text-slate-100"
                                    :title="t('dragDrop')">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"
                                        />
                                    </svg>
                                </button>

                                <!-- ID -->
                                <div
                                    class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm
                                           border border-gray-400
                                           bg-slate-200 dark:bg-slate-700
                                           text-slate-800 dark:text-blue-100"
                                    :title="`[${lesson.locale}] : [${lesson.sort}] ${formatDate(lesson.published_at)}`">
                                    ID: {{ lesson.id }}
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">

                                <!-- Availability badge -->
                                <span
                                    class="text-[10px] px-1.5 py-0.5 rounded-sm
                                           border border-gray-400
                                           bg-fuchsia-100 dark:bg-fuchsia-900/50
                                           text-fuchsia-700 dark:text-fuchsia-300">
                                    {{ getLessonAvailabilityLabel(lesson.availability) }}
                                </span>

                                <input
                                    type="checkbox"
                                    :checked="selectedLessons.includes(lesson.id)"
                                    @change="$emit('toggle-select', lesson.id)"
                                />
                            </div>
                        </div>

                        <!-- Image -->
                        <div class="relative w-full h-32 bg-slate-200 dark:bg-slate-900">
                            <template v-if="lesson.images?.length">
                                <img
                                    :src="getPrimaryImage(lesson).webp_url || getPrimaryImage(lesson).url"
                                    :alt="getPrimaryImage(lesson).alt || t('defaultImageAlt')"
                                    class="w-full h-full object-cover"
                                />
                            </template>
                            <template v-else>
                                <img
                                    src="/storage/lesson_images/default-image.png"
                                    :alt="t('defaultImageTitle')"
                                    class="w-full h-full object-cover"
                                />
                            </template>
                        </div>

                        <!-- Content block -->
                        <div class="flex flex-col flex-1 px-3 py-2 space-y-1">

                            <!-- Title -->
                            <a
                                :href="`/school/lessons/${encodeURIComponent(lesson.slug)}`"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-sm font-semibold text-sky-700 dark:text-sky-200
                                       hover:underline line-clamp-2 text-center"
                                :title="lesson.subtitle || lesson.title">
                                {{ lesson.title }}
                            </a>

                            <div class="text-[11px] text-slate-500 dark:text-slate-300 text-center">
                                <!-- МОДУЛЬ -->
                                <div class="text-slate-600 dark:text-slate-400 font-semibold">
                                    {{ lesson.module?.title || '—' }}
                                    <span class="ml-1 text-[10px] opacity-70 uppercase">
                        [ID: {{ lesson.module?.id || '—' }}] [{{ lesson.module?.locale || '—' }}]
                                    </span>
                                </div>

                                <!-- КУРС -->
                                <div class="text-teal-700 dark:text-teal-200 font-semibold">
                                    {{ lesson.course?.title || '—' }}
                                    <span class="ml-1 text-[10px] text-slate-600
                                                 dark:text-slate-400 opacity-70 uppercase">
                        [ID: {{ lesson.course?.id || '—' }}] [{{ lesson.course?.locale || '—' }}]
                                    </span>
                                </div>
                            </div>

                            <!-- Badges -->
                            <div class="flex flex-wrap justify-center gap-1 mt-1
                                        text-[10px] font-semibold">

                                <span class="px-2 py-0.5 rounded-sm bg-sky-100 dark:bg-sky-900
                                             border border-gray-400
                                             text-sky-700 dark:text-sky-200">
                                    {{ getLessonStatusLabel(lesson.status) }}
                                </span>

                                <span class="px-2 py-0.5 rounded-sm
                                             bg-emerald-100 dark:bg-emerald-900
                                             border border-gray-400
                                             text-emerald-700 dark:text-emerald-200">
                                    {{ getLessonAccessTypeLabel(lesson.access_type) }}
                                </span>

                                <span class="px-2 py-0.5 rounded-sm bg-amber-100 dark:bg-amber-900
                                             border border-gray-400
                                             text-amber-700 dark:text-amber-200">
                                    {{ getLessonPreviewModeLabel(lesson.preview_mode) }}
                                </span>

                            </div>

                            <!-- Duration + Difficulty -->
                            <div class="flex flex-col justify-center
                                        text-gray-700 dark:text-gray-400
                                        text-center text-[11px] mt-2">
                                <div>{{ t('duration') }}: {{ lesson.duration || '—' }}</div>
                                <div>{{ t('difficulty') }}: {{ lesson.difficulty || '—' }}</div>
                            </div>

                            <!-- Stats -->
                            <div class="flex flex-wrap justify-center gap-3 mt-2
                                        text-[11px] text-slate-900 dark:text-slate-200">
                                <span v-if="lesson.views">
                                    {{ t('views') }}: {{ lesson.views }}
                                </span>
                                <span v-if="lesson.likes">
                                    {{ t('likes') }}: {{ lesson.likes }}
                                </span>
                            </div>
                            <div class="flex flex-col justify-center mt-2 text-center
                                        text-[11px] text-slate-900 dark:text-slate-200">
                                <span v-if="lesson.rating_count">
                                    {{ t('ratingCount') }}: {{ lesson.rating_count }}
                                </span>
                                <span v-if="lesson.rating_avg">
                                    {{ t('ratingAvg') }}: {{ lesson.rating_avg }}
                                </span>
                            </div>

                        </div>

                        <!-- Actions -->
                        <div
                            class="flex items-center justify-center px-3 py-2
                                   border-t border-dashed border-slate-400 dark:border-slate-500">

                            <div class="flex items-center space-x-1">
                                <ActivityToggle
                                    :isActive="lesson.activity"
                                    @toggle-activity="$emit('toggle-activity', lesson)"
                                />
                                <CloneIconButton @clone="$emit('clone', lesson)" />
                                <IconEdit :href="route('admin.lessons.edit', lesson.id)" />
                                <DeleteIconButton @delete="$emit('delete', lesson.id)" />
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
