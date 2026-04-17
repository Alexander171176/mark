<script setup>
import { defineProps, defineEmits, ref, watch, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import draggable from 'vuedraggable'

import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'
import ActivityToggle from '@/Components/Admin/Buttons/ActivityToggle.vue'

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

// 🔹 Мапы "значение из БД" → "ключ перевода" (flat i18n)
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

// 🔹 Хелперы
const getCourseStatusLabel = (status) => {
    return t(courseStatusLabelKeyMap[status] || status || 'no')
}

const getCourseAvailabilityLabel = (availability) => {
    return t(courseAvailabilityLabelKeyMap[availability] || availability || 'no')
}

const getCourseLevelLabel = (level) => {
    return t(courseLevelLabelKeyMap[level] || level || 'no')
}

const emits = defineEmits([
    'toggle-activity',
    'edit',
    'delete',
    'update-sort-order',
    'toggle-select',
    'toggle-all'
])

/** Локальная копия для vuedraggable */
const localCourses = ref([])

watch(
    () => props.courses,
    (newVal) => {
        localCourses.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

const handleDragEnd = () => {
    const newOrderIds = localCourses.value.map(course => course.id)
    emits('update-sort-order', newOrderIds)
}

/** Массовые чекбоксы */
const toggleAll = (event) => {
    const checked = event.target.checked
    const ids = localCourses.value.map(c => c.id)
    emits('toggle-all', { ids, checked })
}

/** Основное изображение курса (минимальный order) */
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

const getInstructorName = (course) => {
    const instructorProfile = getInstructorProfile(course)

    return instructorProfile?.public_name
        || instructorProfile?.title
        || 'no'
}

/** Функция форматирования даты */
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

const selectedCount = computed(() => props.selectedCourses.length)
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-200 dark:border-slate-600 relative">

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

        <div class="overflow-x-auto">
            <table
                v-if="courses.length > 0"
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
                    <th class="px-2 py-3 first:pl-14 last:pr-14 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('image')">
                            <svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 512 512">
                                <path
                                    d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l96 0 32 0 208 0c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"
                                />
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-14 last:pr-14 whitespace-nowrap">
                        <div class="font-semibold text-left">
                            {{ t('course') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-14 last:pr-14 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('availability')">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-4 h-4"
                                height="24"
                                width="24"
                                viewBox="0 0 24 24">
                                <path class="fill-current text-fuchsia-800 dark:text-fuchsia-400"
                                    d="M22.707,6.707,17.293,1.293A1,1,0,0,0,16.586,1H4A3,3,0,0,0,1,4V20a3,3,0,0,0,3,3H20a3,3,0,0,0,3-3V7.414A1,1,0,0,0,22.707,6.707ZM14.5,4h1a.5.5,0,0,1,.5.5v4a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-4A.5.5,0,0,1,14.5,4ZM19,12.5v6a.5.5,0,0,1-.5.5H5.5a.5.5,0,0,1-.5-.5v-6a.5.5,0,0,1,.5-.5h13A.5.5,0,0,1,19,12.5Z"></path>
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-14 last:pr-14 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('level')">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-4 h-4 fill-current text-slate-600 dark:text-slate-200"
                                height="24"
                                width="24"
                                viewBox="0 0 24 24">
                                <path class="fill-current text-teal-600 dark:text-teal-300"
                                      d="M12,24a1,1,0,0,1,0-2A10,10,0,0,0,12,2a1,1,0,0,1,0-2,12,12,0,0,1,0,24Z"></path>
                                <path
                                    class="fill-current text-teal-600 dark:text-teal-300"
                                    d="M1.045,13.913a1,1,0,0,1-1-.919C.022,12.665,0,12.336,0,12s.022-.665.049-.994a1,1,0,1,1,1.993.162C2.021,11.442,2,11.719,2,12s.021.558.042.832a1,1,0,0,1-.916,1.078Q1.086,13.913,1.045,13.913Z"></path>
                                <path
                                    class="fill-current text-teal-600 dark:text-teal-300"
                                    d="M6.243,3.641a1,1,0,0,1-.526-1.852,12.022,12.022,0,0,1,1.774-.9,1,1,0,1,1,.754,1.851,10.133,10.133,0,0,0-1.478.757A.993.993,0,0,1,6.243,3.641Z"></path>
                                <path
                                    class="fill-current text-teal-600 dark:text-teal-300"
                                    d="M2.188,8.044a.988.988,0,0,1-.451-.108A1,1,0,0,1,1.3,6.592,12.131,12.131,0,0,1,2.342,4.9,1,1,0,0,1,3.953,6.083,10.1,10.1,0,0,0,3.081,7.5,1,1,0,0,1,2.188,8.044Z"></path>
                                <path
                                    class="fill-current text-teal-600 dark:text-teal-300"
                                    d="M3.128,19.482a1,1,0,0,1-.808-.409,12.049,12.049,0,0,1-1.041-1.7,1,1,0,1,1,1.787-.9,10.047,10.047,0,0,0,.868,1.418,1,1,0,0,1-.217,1.4A.986.986,0,0,1,3.128,19.482Z"></path>
                                <path
                                    d="M7.853,23.185a.983.983,0,0,1-.377-.075A11.879,11.879,0,0,1,5.7,22.2,1,1,0,0,1,6.75,20.5a10.041,10.041,0,0,0,1.48.761,1,1,0,0,1-.377,1.926Z"></path>
                                <path
                                    d="M10,17a1,1,0,0,1-.707-.293l-4-4a1,1,0,0,1,1.414-1.414L10,14.586l7.293-7.293a1,1,0,1,1,1.414,1.414l-8,8A1,1,0,0,1,10,17Z"></path>
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-14 last:pr-14 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('duration')">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-4 h-4"
                                height="24"
                                width="24"
                                viewBox="0 0 24 24">
                                <path
                                    class="fill-current text-violet-700 dark:text-violet-300"
                                    d="M22,13a1,1,0,0,1,0-2h1.949A12.006,12.006,0,0,0,13,.051V2a1,1,0,0,1-2,0V.051A12.006,12.006,0,0,0,.051,11H2a1,1,0,0,1,0,2H.051A12.006,12.006,0,0,0,11,23.949V22a1,1,0,0,1,2,0v1.949A12.006,12.006,0,0,0,23.949,13Zm-6,0H12a1,1,0,0,1-.832-.445l-4-6a1,1,0,1,1,1.664-1.11L12.535,11H16a1,1,0,0,1,0,2Z"></path>
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-14 last:pr-14 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('views')">
                            <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16">
                                <path class="fill-current text-blue-600 dark:text-blue-300"
                                    d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"
                                />
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-14 last:pr-14 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('popularity')">
                            <div class="flex justify-center">
                                <svg class="h-8 w-8 fill-current" viewBox="0 0 32 32">
                                    <path class="fill-current text-red-400 dark:text-red-300"
                                        d="M22.682 11.318A4.485 4.485 0 0019.5 10a4.377 4.377 0 00-3.5 1.707A4.383 4.383 0 0012.5 10a4.5 4.5 0 00-3.182 7.682L16 24l6.682-6.318a4.5 4.5 0 000-6.364zm-1.4 4.933L16 21.247l-5.285-5A2.5 2.5 0 0112.5 12c1.437 0 2.312.681 3.5 2.625C17.187 12.681 18.062 12 19.5 12a2.5 2.5 0 011.785 4.251h-.003z"></path>
                                </svg>
                            </div>
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-14 last:pr-14 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('ratingCount')">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24" width="24"
                                 viewBox="0 0 24 24" class="shrink-0 h-4 w-4">
                                <path class="fill-current text-red-400 dark:text-red-300"
                                      d="M14.586,9.439S15.7,2.858,11.138,0A8.055,8.055,0,0,1,8.1,5.831C6.149,7.546,2.481,11.4,2.52,15.51A9.435,9.435,0,0,0,7.7,24a5.975,5.975,0,0,1,2.091-4.132,4.877,4.877,0,0,0,1.869-3.278,8.786,8.786,0,0,1,4.652,7.322v.02a8.827,8.827,0,0,0,5.137-7.659c.324-3.863-1.792-9.112-3.668-10.828A10.192,10.192,0,0,1,14.586,9.439Z"></path>
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-14 last:pr-14 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('ratingAvg')">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24" width="24"
                                 viewBox="0 0 24 24" class="shrink-0 h-4 w-4">
                                <path class="fill-current text-red-400 dark:text-red-300"
                                      d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"></path>
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-14 last:pr-14 whitespace-nowrap">
                        <div class="flex justify-center" :title="t('likes')">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24" width="24"
                                 viewBox="0 0 24 24" class="shrink-0 h-4 w-4">
                                <path class="fill-current text-red-400 dark:text-red-300"
                                      d="M3,9H1a1,1,0,0,0-1,1V22a1,1,0,0,0,1,1H4V10A1,1,0,0,0,3,9Z"></path>
                                <path class="fill-current text-red-400 dark:text-red-300"
                                      d="M21.882,8.133A2.986,2.986,0,0,0,21,8H15V5c0-3.824-2.589-4.942-3.958-5a1.017,1.017,0,0,0-.734.277A1,1,0,0,0,10,1V5.638l-4,4.8V23H18.23A2.985,2.985,0,0,0,21.1,20.882l2.769-9A3,3,0,0,0,21.882,8.133Z"></path>
                            </svg>
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-14 last:pr-14 whitespace-nowrap">
                        <div class="font-semibold text-end">
                            {{ t('actions') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-14 last:pr-14 whitespace-nowrap">
                        <div class="text-center">
                            <input type="checkbox" @change="toggleAll" />
                        </div>
                    </th>
                </tr>
                </thead>

                <draggable
                    tag="tbody"
                    v-model="localCourses"
                    @end="handleDragEnd"
                    item-key="id"
                    handle=".handle"
                >
                    <template #item="{ element: course }">
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
                            <td class="px-2 py-3 first:pl-14 last:pr-14 whitespace-nowrap">
                                <div
                                    class="text-center text-xs
                                           text-slate-800 dark:text-blue-200"
                                    :title="`[${course.locale}] : [${course.sort}] ${formatDate(course.published_at)}`">
                                    {{ course.id }}
                                </div>
                            </td>
                            <td class="first:pl-14 last:pr-14">
                                <div class="flex justify-center"
                                     :title="course.instructorProfile.title">
                                    <template
                                        v-if="course.images && course.images.length">
                                        <img
                                            :src="getPrimaryImage(course).webp_url ||
                                            getPrimaryImage(course).url"
                                            :alt="getPrimaryImage(course).alt ||
                                                t('defaultImageAlt')"
                                            :title="
                                                getPrimaryImage(course).caption ||
                                                t('currentImage')"
                                            class="h-8 w-12 object-cover rounded-sm"
                                        />
                                    </template>
                                    <template v-else>
                                        <img
                                            src="/storage/course_images/default-image.png"
                                            :alt="t('defaultImageTitle')"
                                            class="h-8 w-12 object-cover rounded-sm"
                                        />
                                    </template>
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-14 last:pr-14 whitespace-nowrap">
                                <div class="min-w-0">
                                    <a
                                        :href="`/school/courses/${encodeURIComponent(course.slug)}`"
                                        class="text-xs text-sky-600 dark:text-sky-200 hover:underline
                                                   hover:text-sky-600 dark:hover:text-sky-200"
                                        target="_blank"
                                        rel="noopener noreferrer" :title="course.subtitle">
                                        {{ course.title }}
                                    </a>

                                    <div
                                        class="text-xs text-teal-700 dark:text-teal-200 truncate"
                                        :title="`ID: ${course?.instructorProfile?.id ?? '-'} | ${getInstructorName(course)}`"
                                    >
                                        {{ getInstructorName(course) }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-14 last:pr-14 whitespace-nowrap">
                                <div class="text-center text-xs
                                            text-fuchsia-800 dark:text-fuchsia-400"
                                :title="`${t('status')} : ${getCourseStatusLabel(course.status)}`">
                                    {{ getCourseAvailabilityLabel(course.availability) }}
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-14 last:pr-14 whitespace-nowrap">
                                <div class="text-center text-xs
                                            text-teal-600 dark:text-teal-300"
    :title="`${t('level')} : ${getCourseLevelLabel(course.level)} (${course.difficulty || '-'})`">
                                    {{ getCourseLevelLabel(course.level) }}
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-14 last:pr-14 whitespace-nowrap">
                                <div class="text-center text-xs
                                            text-violet-700 dark:text-violet-300"
                                     :title="`${t('duration')} [${course.duration}]`">
                                    {{ course.duration }}
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-14 last:pr-14 whitespace-nowrap">
                                <div class="text-center text-xs
                                            text-blue-700 dark:text-blue-300"
                                     :title="course.students_count">
                                    {{ course.views }}
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-14 last:pr-14 whitespace-nowrap">
                                <div
                                    class="text-center text-xs
                                           text-rose-500 dark:text-rose-300">
                                    {{ course.popularity }}
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-14 last:pr-14 whitespace-nowrap">
                                <div class="text-center text-xs">
                                    {{ course.rating_count }}
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-14 last:pr-14 whitespace-nowrap">
                                <div
                                    class="text-center text-xs
                                           text-rose-500 dark:text-rose-300">
                                    {{ course.rating_avg ?? 'no' }}
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-14 last:pr-14 whitespace-nowrap">
                                <div class="text-center text-xs">
                                    {{ course.likes }}
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-14 last:pr-14 whitespace-nowrap">
                                <div class="flex justify-end space-x-2">
                                    <ActivityToggle
                                        :isActive="course.activity"
                                        @toggle-activity="$emit('toggle-activity', course)"
                                        :title="course.activity ? t('enabled') : t('disabled')"
                                    />
                                    <IconEdit
                                        :href="route('admin.courses.edit', course.id)"
                                    />
                                    <DeleteIconButton
                                        @delete="$emit('delete', course.id)"
                                    />
                                </div>
                            </td>
                            <td class="px-2 py-3 first:pl-14 last:pr-14 whitespace-nowrap">
                                <div class="text-center">
                                    <input
                                        type="checkbox"
                                        :checked="selectedCourses.includes(course.id)"
                                        @change="$emit('toggle-select', course.id)"
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
