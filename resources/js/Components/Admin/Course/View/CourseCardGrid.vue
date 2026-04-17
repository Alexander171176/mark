<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import ActivityToggle from '@/Components/Admin/Buttons/ActivityToggle.vue'
import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'

const { t } = useI18n()

const props = defineProps({
    courses: {
        type: Array,
        default: () => []
    },
    selectedCourses: {
        type: Array,
        default: () => []
    }
})

const emits = defineEmits([
    'toggle-activity',
    'delete',
    'update-sort-order',
    'toggle-select',
    'toggle-all'
])

// --- Мапы "значение из БД" → "ключ перевода" (flat i18n) ---
const courseStatusLabelKeyMap = {
    draft: 'statusDraft',
    published: 'statusPublished',
    archived: 'statusArchived',
}

const courseAvailabilityLabelKeyMap = {
    unlisted: 'availabilityUnlisted',
    public: 'availabilityPublic',
    private: 'availabilityPrivate',
}

const courseLevelLabelKeyMap = {
    beginner: 'levelBeginner',
    intermediate: 'levelIntermediate',
    advanced: 'levelAdvanced',
}

// --- Хелперы ---
const getCourseStatusLabel = (status) => {
    if (!status) return '—'
    const key = courseStatusLabelKeyMap[status]
    return key ? t(key) : status
}

const getCourseAvailabilityLabel = (availability) => {
    if (!availability) return '—'
    const key = courseAvailabilityLabelKeyMap[availability]
    return key ? t(key) : availability
}

const getCourseLevelLabel = (level) => {
    if (!level) return '—'
    const key = courseLevelLabelKeyMap[level]
    return key ? t(key) : level
}

// --- Локальная копия для vuedraggable ---
const localCourses = ref([])

watch(
    () => props.courses,
    (newVal) => {
        localCourses.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

// --- Drag end: отдаем массив ID вверх ---
const handleDragEnd = () => {
    const newOrderIds = localCourses.value.map(course => course.id)
    emits('update-sort-order', newOrderIds)
}

// --- Массовый выбор (как в таблице) ---
const toggleAll = (event) => {
    const checked = event.target.checked
    const ids = localCourses.value.map(c => c.id)
    emits('toggle-all', { ids, checked })
}

// --- Главное изображение курса ---
const getPrimaryImage = (course) => {
    if (course.images && course.images.length) {
        return [...course.images].sort((a, b) => a.order - b.order)[0]
    }
    return null
}

/** Инструктор */
const getInstructorProfile = (course) => {
    return course?.instructorProfile || null
}

const getInstructorImages = (course) => {
    const instructorProfile = getInstructorProfile(course)

    return Array.isArray(instructorProfile?.images)
        ? instructorProfile.images
        : []
}

const getInstructorPrimaryImage = (course) => {
    const images = getInstructorImages(course)

    if (!images.length) return null

    return [...images].sort((a, b) => {
        const aOrder = Number(a?.order ?? a?.pivot?.order ?? 999999)
        const bOrder = Number(b?.order ?? b?.pivot?.order ?? 999999)
        return aOrder - bOrder
    })[0]
}

const getInstructorImageUrl = (course) => {
    const image = getInstructorPrimaryImage(course)

    return image?.webp_url
        || image?.url
        || image?.image_url
        || image?.thumb_url
        || null
}

const getInstructorName = (course) => {
    const instructorProfile = getInstructorProfile(course)

    return instructorProfile?.public_name
        || instructorProfile?.title
        || '—'
}

/** Функция форматирования даты */
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
                {{ t('selected') }}: {{ selectedCourses.length }}
            </div>

            <label
                v-if="localCourses.length"
                class="flex items-center text-xs text-slate-600
                       dark:text-slate-200 cursor-pointer">
                <span>{{ t('selectAll') }}</span>
                <input type="checkbox" class="mx-2" @change="toggleAll" />
            </label>
        </div>

        <div v-if="localCourses.length" class="p-3">
            <draggable
                tag="div"
                v-model="localCourses"
                item-key="id"
                @end="handleDragEnd"
                handle=".drag-handle"
                class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4"
            >
                <template #item="{ element: course }">
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
                                    :title="`[${course.locale}] : [${course.sort}] ${formatDate(course.published_at)}`">
                                    ID: {{ course.id }}
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                <!-- Availability badge -->
                                <span
                                    class="text-[10px] px-1.5 py-0.5 rounded-sm
                                           border border-gray-400
                                           bg-fuchsia-100 dark:bg-fuchsia-900/50
                                           text-fuchsia-700 dark:text-fuchsia-300"
                                    :title="t('availability')">
                                    {{ getCourseAvailabilityLabel(course.availability) }}
                                </span>

                                <input
                                    type="checkbox"
                                    :checked="selectedCourses.includes(course.id)"
                                    @change="$emit('toggle-select', course.id)"
                                />
                            </div>
                        </div>

                        <!-- Изображение -->
                        <div class="relative w-full h-32 bg-slate-200 dark:bg-slate-900">
                            <template v-if="course.images?.length">
                                <img
                                    :src="getPrimaryImage(course).webp_url || getPrimaryImage(course).url"
                                    :alt="getPrimaryImage(course).alt || t('defaultImageAlt')"
                                    :title="getPrimaryImage(course).caption || t('currentImage')"
                                    class="w-full h-full object-cover"
                                />
                            </template>
                            <template v-else>
                                <img
                                    src="/storage/course_images/default-image.png"
                                    :alt="t('defaultImageTitle')"
                                    class="w-full h-full object-cover"
                                />
                            </template>
                        </div>

                        <!-- Контент -->
                        <div class="flex flex-col flex-1 px-3 py-2 space-y-1">
                            <!-- Заголовок курса -->
                            <a
                                :href="`/school/courses/${encodeURIComponent(course.slug)}`"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-sm font-semibold text-sky-700 dark:text-sky-200
                                       hover:underline line-clamp-2 text-center"
                                :title="course.subtitle || course.title"
                            >
                                {{ course.title }}
                            </a>

                            <!-- Бейджи: статус / уровень -->
                            <div class="flex flex-wrap justify-center gap-1 mt-1
                                        text-[10px] font-semibold">
                                <!-- Статус -->
                                <span
                                    class="px-2 py-0.5 rounded-sm
                                           bg-sky-100 dark:bg-sky-900
                                           border border-gray-400
                                           text-sky-700 dark:text-sky-200"
                                    :title="t('status')">
                                    {{ getCourseStatusLabel(course.status) }}
                                </span>

                                <!-- Уровень -->
                                <span
                                    class="px-2 py-0.5 rounded-sm
                                           bg-emerald-100 dark:bg-emerald-900
                                           border border-gray-400
                                           text-emerald-700 dark:text-emerald-200"
                                    :title="`${t('level')} : ${getCourseLevelLabel(course.level)}`">
                                    {{ t('level') }}: {{ getCourseLevelLabel(course.level) }}
                                </span>
                            </div>

                            <!-- Продолжительность -->
                            <div
                                class="flex flex-col justify-center
                                       text-gray-700 dark:text-gray-400
                                       text-center text-[11px] mt-2">
                                <div>
                                    {{ t('duration') }}: {{ course.duration || '—' }}
                                </div>
                            </div>

                            <!-- Статистика -->
                            <div class="flex flex-wrap justify-center gap-3 mt-2
                                        text-[11px] text-slate-900 dark:text-slate-200">
                                <span v-if="course.views">
                                    {{ t('views') }}: {{ course.views }}
                                </span>
                                <span v-if="course.likes">
                                    {{ t('likes') }}: {{ course.likes }}
                                </span>
                            </div>

                            <div class="flex flex-col justify-center mt-2 text-center
                                        text-[11px] text-slate-900 dark:text-slate-200">
                                <span v-if="course.rating_count">
                                    {{ t('ratingCount') }}: {{ course.rating_count }}
                                </span>
                                <span v-if="course.rating_avg">
                                    {{ t('ratingAvg') }}: {{ course.rating_avg }}
                                </span>
                            </div>

                            <!-- Инструктор -->
                            <div class="flex items-center justify-center gap-2 mt-1">
                                <img
                                    v-if="getInstructorImageUrl(course)"
                                    :src="getInstructorImageUrl(course)"
                                    :alt="getInstructorName(course)"
                                    class="h-7 w-7 rounded-full object-cover
                                           ring-1 ring-slate-300 dark:ring-slate-600 shrink-0"
                                    loading="lazy"
                                />

                                <div
                                    class="text-[11px] font-semibold
                                           text-teal-700 dark:text-teal-200 text-center"
                                    :title="`ID: ${course?.instructorProfile?.id ?? '-'} | ${getInstructorName(course)}`"
                                >
                                    {{ getInstructorName(course) }}
                                </div>
                            </div>
                        </div>

                        <!-- Действия -->
                        <div
                            class="flex items-center justify-center px-3 py-2
                                   border-t border-dashed border-slate-400 dark:border-slate-500">
                            <div class="flex items-center space-x-1">
                                <ActivityToggle
                                    :isActive="course.activity"
                                    @toggle-activity="$emit('toggle-activity', course)"
                                    :title="course.activity ? t('enabled') : t('disabled')"
                                />
                                <IconEdit :href="route('admin.courses.edit', course.id)" />
                                <DeleteIconButton @delete="$emit('delete', course.id)" />
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
