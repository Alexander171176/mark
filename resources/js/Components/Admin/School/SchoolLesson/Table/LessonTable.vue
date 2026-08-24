<script setup>
import { ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'
import ActivityToggle from '@/Components/Admin/UI/Buttons/ActivityToggle.vue'
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
        month: 'long',
        day: 'numeric',
    })
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
                {{ t('selected') }}: {{ selectedLessons.length }}
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

        <div class="overflow-x-auto">
            <table
                v-if="localLessons.length > 0"
                class="table-auto w-full text-slate-700 dark:text-slate-100"
            >
                <thead
                    class="text-sm uppercase bg-slate-200 dark:bg-cyan-900
                           border border-solid border-gray-300 dark:border-gray-700"
                >
                <tr>
                    <th class="px-2 py-3 w-px">
                        <svg
                            class="w-4 h-4 fill-current
                                       text-slate-800 dark:text-slate-200"
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
                        <div
                            class="flex justify-center"
                            :title="t('image')"
                        >
                            <svg
                                class="w-6 h-6 fill-current shrink-0"
                                viewBox="0 0 512 512"
                            >
                                <path
                                    d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l96 0 32 0 208 0c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"
                                />
                            </svg>
                        </div>
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-left">
                            {{ t('lesson') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-center">
                            {{ t('status') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap">
                        <div
                            class="flex justify-center"
                            :title="t('duration')"
                        >
                            <svg
                                class="w-4 h-4"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    class="fill-current text-violet-700 dark:text-violet-300"
                                    d="M22,13a1,1,0,0,1,0-2h1.949A12.006,12.006,0,0,0,13,.051V2a1,1,0,0,1-2,0V.051A12.006,12.006,0,0,0,.051,11H2a1,1,0,0,1,0,2H.051A12.006,12.006,0,0,0,11,23.949V22a1,1,0,0,1,2,0v1.949A12.006,12.006,0,0,0,23.949,13Zm-6,0H12a1,1,0,0,1-.832-.445l-4-6a1,1,0,1,1,1.664-1.11L12.535,11H16a1,1,0,0,1,0,2Z"
                                />
                            </svg>
                        </div>
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap">
                        <div
                            class="flex justify-center"
                            :title="t('views')"
                        >
                            {{ t('views') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap">
                        <div
                            class="flex justify-center"
                            :title="t('rating')"
                        >
                            {{ t('rating') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap">
                        <div
                            class="flex justify-center"
                            :title="t('likes')"
                        >
                            {{ t('likes') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-end">
                            {{ t('actions') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="text-center">
                            <input
                                type="checkbox"
                                @change="toggleAll"
                            >
                        </div>
                    </th>
                </tr>
                </thead>

                <draggable
                    v-model="localLessons"
                    tag="tbody"
                    item-key="id"
                    handle=".handle"
                    @end="handleDragEnd"
                >
                    <template #item="{ element: lesson }">
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
                                    :title="`[sort: ${lesson.sort}] ${formatDate(lesson.published_at)}`"
                                >
                                    {{ lesson.id }}
                                </div>
                            </td>

                            <td class="px-2 py-3 whitespace-nowrap">
                                <div
                                    class="flex justify-center"
                                    :title="getLessonTitle(lesson)"
                                >
                                    <img
                                        v-if="getPrimaryImage(lesson)"
                                        :src="getImageUrl(getPrimaryImage(lesson))"
                                        :alt="getPrimaryImage(lesson)?.alt || t('defaultImageAlt')"
                                        :title="getPrimaryImage(lesson)?.caption || t('lessonImage')"
                                        class="h-8 w-12 object-cover rounded-sm"
                                    >

                                    <img
                                        v-else
                                        src="/storage/school/school_lesson_images/default-image.png"
                                        :alt="t('defaultImageTitle')"
                                        class="h-8 w-12 object-cover rounded-sm"
                                    >
                                </div>
                            </td>

                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="min-w-0">
                                    <a
                                        :href="`/school/lessons/${encodeURIComponent(lesson.slug)}`"
                                        class="text-xs text-sky-600 dark:text-sky-200
                                               hover:underline"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        :title="getLessonSubtitle(lesson) || getLessonShort(lesson)"
                                    >
                                        {{ getLessonTitle(lesson) }}
                                    </a>

                                    <div
                                        class="flex flex-row items-center gap-1
                                               text-xs text-teal-700 dark:text-teal-200"
                                        :title="`ID: ${lesson.school_module_id}`"
                                    >
                                        <span>
                                            {{ getModuleTitle(lesson) }}
                                        </span>
                                    </div>

                                    <div
                                        class="flex flex-row items-center gap-1
                                               text-xs text-slate-700 dark:text-slate-200"
                                    >
                                        <span>
                                            {{ getCourseTitle(lesson) }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <td class="px-2 py-3 whitespace-nowrap">
                                <div
                                    class="text-center text-xs
                                           text-fuchsia-800 dark:text-fuchsia-400"
                                >
                                    {{ getLessonStatusLabel(lesson.status) }}
                                </div>

                                <div
                                    class="text-center text-xs
                                           text-orange-600 dark:text-orange-300"
                                >
                                    {{ getLessonAvailabilityLabel(lesson.availability) }}
                                </div>

                                <div
                                    class="text-center text-[10px]
                                           text-slate-500 dark:text-slate-300"
                                >
                                    {{ getLessonAccessTypeLabel(lesson.access_type) }}
                                </div>
                            </td>

                            <td class="px-2 py-3 whitespace-nowrap">
                                <div
                                    class="text-center text-xs
                                           text-violet-700 dark:text-violet-300"
                                >
                                    {{ lesson.duration ?? 0 }}
                                    {{ getLessonPreviewModeLabel(lesson.preview_mode) }}
                                </div>

                                <div
                                    class="text-center text-[10px]
                                           text-slate-500 dark:text-slate-300"
                                >
                                    {{ t('difficulty') }}:
                                    {{ lesson.difficulty ?? 0 }}
                                </div>
                            </td>

                            <td class="px-2 py-3 whitespace-nowrap">
                                <div
                                    class="text-center text-xs
                                           text-blue-700 dark:text-blue-300"
                                >
                                    {{ lesson.views ?? 0 }}
                                </div>
                            </td>

                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
                                    <div
                                        class="text-center text-xs
                                               text-rose-500 dark:text-rose-300"
                                    >
                                        {{ lesson.rating_avg ?? 0 }}
                                    </div>

                                    <div
                                        class="text-center text-[10px]
                                               text-slate-500 dark:text-slate-300"
                                    >
                                        {{ lesson.rating_count ?? 0 }}
                                    </div>
                                </div>
                            </td>

                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="text-center text-xs">
                                    {{ lesson.likes ?? 0 }}
                                </div>
                            </td>

                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="flex flex-row items-center justify-end gap-1">
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
                            </td>

                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="text-center">
                                    <input
                                        type="checkbox"
                                        :checked="selectedLessons.includes(lesson.id)"
                                        @change="emit('toggle-select', lesson.id)"
                                    >
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
