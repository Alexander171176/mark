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
    lessons: { type: Array, default: () => [] },
    selectedLessons: { type: Array, default: () => [] },
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
 * LABELS
 * ========================================================== */

const lessonStatusLabelKeyMap = {
    draft: 'statusDraft',
    published: 'statusPublished',
    archived: 'statusArchived',
}

const lessonAvailabilityLabelKeyMap = {
    unlisted: 'availabilityUnlisted',
    public: 'availabilityPublic',
    private: 'availabilityPrivate',
}

const lessonAccessTypeLabelKeyMap = {
    free: 'free',
    paid: 'paid',
    bonus: 'bonus',
}

const lessonPreviewModeLabelKeyMap = {
    none: 'previewNone',
    full: 'previewFull',
    percent: 'previewPercent',
    duration: 'previewDuration',
    chars: 'previewChars',
}

const getLessonStatusLabel = (status) => {
    return t(
        lessonStatusLabelKeyMap[status]
        || status
        || 'no'
    )
}

const getLessonAvailabilityLabel = (availability) => {
    return t(
        lessonAvailabilityLabelKeyMap[availability]
        || availability
        || 'no'
    )
}

const getLessonAccessTypeLabel = (accessType) => {
    return t(
        lessonAccessTypeLabelKeyMap[accessType]
        || accessType
        || 'no'
    )
}

const getLessonPreviewModeLabel = (previewMode) => {
    return t(
        lessonPreviewModeLabelKeyMap[previewMode]
        || previewMode
        || 'no'
    )
}

/* ==========================================================
 * LOCAL LIST / DRAG
 * ========================================================== */

const localLessons = ref([])

watch(
    () => props.lessons,
    (lessons) => {
        localLessons.value = JSON.parse(
            JSON.stringify(lessons || [])
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
        localLessons.value.map(
            lesson => lesson.id
        )
    )
}

const toggleAll = (event) => {
    emit('toggle-all', {
        ids: localLessons.value.map(
            lesson => lesson.id
        ),
        checked: event.target.checked,
    })
}

/* ==========================================================
 * NEW RESOURCE CONTRACT
 * ========================================================== */

const getLessonTitle = (lesson) => {
    return lesson?.translation?.title
        || `ID: ${lesson?.id}`
}

const getLessonSubtitle = (lesson) => {
    return lesson?.translation?.subtitle || ''
}

const getLessonShort = (lesson) => {
    return lesson?.translation?.short || ''
}

const getModuleTitle = (lesson) => {
    return lesson?.module?.translation?.title
        || `ID: ${lesson?.school_module_id || '-'}`
}

const getCourse = (lesson) => {
    return lesson?.module?.course || null
}

const getCourseTitle = (lesson) => {
    const course = getCourse(lesson)

    return course?.translation?.title
        || (course?.id ? `ID: ${course.id}` : '—')
}

/* ==========================================================
 * IMAGES
 * ========================================================== */

const getPrimaryImage = (lesson) => {
    if (lesson?.primary_image) {
        return lesson.primary_image
    }

    if (
        Array.isArray(lesson?.images)
        && lesson.images.length
    ) {
        return [...lesson.images]
            .sort(
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
        month: 'short',
        day: 'numeric',
    })
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
                {{ t('selected') }}:
                {{ selectedLessons.length }}
            </div>

            <label
                v-if="localLessons.length"
                class="flex items-center text-xs text-slate-600
                       dark:text-slate-200 cursor-pointer"
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
            v-if="localLessons.length"
            class="p-3"
        >
            <draggable
                v-model="localLessons"
                tag="div"
                item-key="id"
                handle=".drag-handle"
                class="grid gap-3 grid-cols-1
                       sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                @end="handleDragEnd"
            >
                <template #item="{ element: lesson }">
                    <div
                        class="relative flex flex-col h-full rounded-md
                               border border-slate-400 dark:border-slate-500
                               bg-slate-50/70 dark:bg-slate-800/80 shadow-sm
                               hover:shadow-md transition-shadow duration-150"
                    >
                        <!-- Header -->
                        <div
                            class="flex items-center justify-between px-2 py-1
                                   border-b border-dashed border-slate-400
                                   dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-2">
                                <button
                                    type="button"
                                    class="drag-handle text-slate-400
                                           hover:text-slate-700
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
                                    class="text-[10px] font-semibold
                                           px-1.5 py-0.5 rounded-sm
                                           border border-gray-400
                                           bg-slate-200 dark:bg-slate-700
                                           text-slate-800 dark:text-blue-100"
                                    :title="`[sort: ${lesson.sort}] ${formatDate(lesson.published_at)}`"
                                >
                                    ID: {{ lesson.id }}
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                <span
                                    class="text-[10px] px-1.5 py-0.5 rounded-sm
                                           border border-gray-400
                                           bg-fuchsia-100 dark:bg-fuchsia-900/50
                                           text-fuchsia-700 dark:text-fuchsia-300"
                                >
                                    {{ getLessonAvailabilityLabel(lesson.availability) }}
                                </span>

                                <input
                                    type="checkbox"
                                    :checked="selectedLessons.includes(lesson.id)"
                                    @change="emit('toggle-select', lesson.id)"
                                >
                            </div>
                        </div>

                        <!-- Image -->
                        <div
                            class="relative w-full h-32
                                   bg-slate-200 dark:bg-slate-900"
                        >
                            <img
                                v-if="getPrimaryImage(lesson)"
                                :src="getImageUrl(getPrimaryImage(lesson))"
                                :alt="getPrimaryImage(lesson)?.alt || t('defaultImageAlt')"
                                :title="getPrimaryImage(lesson)?.caption || getLessonTitle(lesson)"
                                class="w-full h-full object-cover"
                            >

                            <img
                                v-else
                                src="/storage/school/school_lesson_images/default-image.png"
                                :alt="t('defaultImageTitle')"
                                class="w-full h-full object-cover"
                            >
                        </div>

                        <!-- Content -->
                        <div class="flex flex-col flex-1 px-3 py-2 space-y-1">
                            <a
                                :href="`/school/lessons/${encodeURIComponent(lesson.slug)}`"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-sm font-semibold
                                       text-sky-700 dark:text-sky-200
                                       hover:underline line-clamp-2 text-center"
                                :title="getLessonSubtitle(lesson) || getLessonShort(lesson)"
                            >
                                {{ getLessonTitle(lesson) }}
                            </a>

                            <div class="text-[11px] text-center">
                                <div
                                    class="text-slate-600
                                           dark:text-slate-400 font-semibold"
                                >
                                    {{ t('module') }}:
                                    {{ getModuleTitle(lesson) }}
                                </div>

                                <div
                                    class="text-teal-700
                                           dark:text-teal-200 font-semibold"
                                >
                                    {{ t('course') }}:
                                    {{ getCourseTitle(lesson) }}
                                </div>
                            </div>

                            <div
                                class="text-[10px] text-center
                                       text-slate-500 dark:text-slate-300 truncate"
                                :title="lesson.slug"
                            >
                                {{ lesson.slug }}
                            </div>

                            <div
                                class="flex flex-wrap justify-center gap-1 mt-1
                                       text-[10px] font-semibold"
                            >
                                <span
                                    class="px-2 py-0.5 rounded-sm
                                           bg-sky-100 dark:bg-sky-900
                                           border border-gray-400
                                           text-sky-700 dark:text-sky-200"
                                >
                                    {{ getLessonStatusLabel(lesson.status) }}
                                </span>

                                <span
                                    class="px-2 py-0.5 rounded-sm
                                           bg-emerald-100 dark:bg-emerald-900
                                           border border-gray-400
                                           text-emerald-700 dark:text-emerald-200"
                                >
                                    {{ getLessonAccessTypeLabel(lesson.access_type) }}
                                </span>
                            </div>

                            <div
                                class="flex flex-col justify-center
                                       text-gray-700 dark:text-gray-400
                                       text-center text-[11px] mt-2"
                            >
                                <div>
                                    {{ t('duration') }}:
                                    {{ lesson.duration ?? '—' }}
                                    {{ getLessonPreviewModeLabel(lesson.preview_mode) }}
                                </div>

                                <div>
                                    {{ t('difficulty') }}:
                                    {{ lesson.difficulty ?? '—' }}
                                </div>
                            </div>

                            <div
                                class="flex flex-wrap justify-center gap-3 mt-2
                                       text-[11px] text-slate-900 dark:text-slate-200"
                            >
                                <span>
                                    {{ t('views') }}:
                                    {{ lesson.views ?? 0 }}
                                </span>

                                <span>
                                    {{ t('likes') }}:
                                    {{ lesson.likes ?? 0 }}
                                </span>
                            </div>

                            <div
                                class="flex flex-col justify-center mt-2
                                       text-center text-[11px]
                                       text-slate-900 dark:text-slate-200"
                            >
                                <span>
                                    {{ t('ratingCount') }}:
                                    {{ lesson.rating_count ?? 0 }}
                                </span>

                                <span>
                                    {{ t('ratingAvg') }}:
                                    {{ lesson.rating_avg ?? 0 }}
                                </span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div
                            class="flex items-center justify-center px-3 py-2
                                   border-t border-dashed border-slate-400
                                   dark:border-slate-500"
                        >
                            <div class="flex items-center space-x-1">
                                <CloneIconButton
                                    :title="t('clone')"
                                    @clone="emit('clone', lesson)"
                                />

                                <ActivityToggle
                                    :isActive="lesson.activity"
                                    :title="lesson.activity ? t('enabled') : t('disabled')"
                                    @toggle-activity="emit('toggle-activity', lesson)"
                                />

                                <IconEdit
                                    :href="route('admin.schoolLessons.edit', {
                                        schoolLesson: lesson.id
                                    })"
                                />

                                <DeleteIconButton
                                    @delete="emit('delete', lesson)"
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
