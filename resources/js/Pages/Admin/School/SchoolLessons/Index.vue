<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список уроков школы.
 *
 * Admin Index contract:
 * - SchoolLessonSharedResource;
 * - translation содержит только выбранную locale;
 * - module/course/hashtags используют SharedResource;
 * - frontend | server | auto;
 * - frontend search / sort / pagination;
 * - server search / sort / pagination.
 */

import { computed, ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import SearchInput from '@/Components/Admin/UI/Search/SearchInput.vue'
import ServerSearchInput from '@/Components/Admin/UI/Search/ServerSearchInput.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import ToggleViewButton from '@/Components/Admin/UI/Buttons/ToggleViewButton.vue'
import CountTable from '@/Components/Admin/UI/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/UI/Select/ItemsPerPageSelect.vue'
import ServerItemsPerPageSelect from '@/Components/Admin/UI/Select/ServerItemsPerPageSelect.vue'
import DangerModal from '@/Components/Admin/UI/Modal/DangerModal.vue'
import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue'
import AdminServerPagination from '@/Components/Admin/UI/Pagination/AdminServerPagination.vue'
import ProcessingModeSwitcher from '@/Components/Admin/UI/Processing/ProcessingModeSwitcher.vue'

import BulkActionSelect from '@/Components/Admin/School/SchoolLesson/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/School/SchoolLesson/Sort/SortSelect.vue'
import LessonTable from '@/Components/Admin/School/SchoolLesson/Table/LessonTable.vue'
import LessonCardGrid from '@/Components/Admin/School/SchoolLesson/View/LessonCardGrid.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },

    adminSchoolLessonsProcessingMode: { type: String, default: 'frontend' },
    useServerProcessing: { type: Boolean, default: false },

    lessons: { type: [Array, Object], default: () => [] },
    lessonsCount: { type: Number, default: 0 },

    adminSchoolLessonsPerPage: { type: Number, default: 6 },
    adminSchoolLessonsDefaultSort: { type: String, default: 'idDesc' },

    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },

    errors: { type: Object, default: () => ({}) },
})

/* ==========================================================
 * VIEW MODE
 * ========================================================== */

const viewMode = ref(
    localStorage.getItem('admin_view_mode_lessons') || 'table'
)

watch(viewMode, (value) => {
    localStorage.setItem('admin_view_mode_lessons', value)
})

/* ==========================================================
 * SOURCE DATA
 * ========================================================== */

const lessonsList = computed(() => {
    if (Array.isArray(props.lessons)) {
        return props.lessons
    }

    if (Array.isArray(props.lessons?.data)) {
        return props.lessons.data
    }

    return []
})

const localLessons = ref([])

watch(
    lessonsList,
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

/* ==========================================================
 * PAGINATION / SORT / SEARCH
 * ========================================================== */

const itemsPerPage = ref(
    props.adminSchoolLessonsPerPage || 6
)

const sortParam = ref(
    props.sortParam
    || props.adminSchoolLessonsDefaultSort
    || 'idDesc'
)

const searchQuery = ref(
    props.search || ''
)

const currentPage = ref(1)

const serverCurrentPage = computed(() => {
    return Number(
        props.lessons?.meta?.current_page
        ?? props.lessons?.current_page
        ?? 1
    ) || 1
})

const activeCurrentPage = computed(() => {
    return props.useServerProcessing
        ? serverCurrentPage.value
        : currentPage.value
})

watch(itemsPerPage, (newValue) => {
    if (props.useServerProcessing) {
        return
    }

    currentPage.value = 1

    router.put(
        route('admin.settings.updateAdminCountSchoolLessons'),
        {
            value: newValue,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                toast.info(
                    `Показ ${newValue} элементов на странице.`
                )
            },

            onError: (errors) => {
                toast.error(
                    errors?.value
                    || 'Ошибка обновления количества элементов.'
                )
            },
        }
    )
})

watch(sortParam, (newValue) => {
    currentPage.value = 1

    router.put(
        route('admin.settings.updateAdminSortSchoolLessons'),
        {
            value: newValue,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                if (props.useServerProcessing) {
                    const query = Object.fromEntries(
                        new URLSearchParams(
                            window.location.search
                        )
                    )

                    router.get(
                        window.location.pathname,
                        {
                            ...query,
                            sort: newValue || undefined,
                            page: undefined,
                        },
                        {
                            preserveScroll: true,
                            preserveState: false,
                            replace: true,
                        }
                    )
                }

                toast.info(
                    'Сортировка успешно изменена'
                )
            },

            onError: (errors) => {
                toast.error(
                    errors?.value
                    || 'Ошибка обновления сортировки.'
                )
            },
        }
    )
})

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

const getLessonDescription = (lesson) => {
    return lesson?.translation?.description || ''
}

const getLessonSlug = (lesson) => {
    return lesson?.slug || ''
}

const getModuleTitle = (lesson) => {
    return lesson?.module?.translation?.title
        || `ID: ${lesson?.school_module_id || '-'}`
}

const getModuleSlug = (lesson) => {
    return lesson?.module?.slug || ''
}

const getCourse = (lesson) => {
    return lesson?.module?.course || null
}

const getCourseTitle = (lesson) => {
    const course = getCourse(lesson)

    return course?.translation?.title
        || (course?.id ? `ID: ${course.id}` : '')
}

const getCourseSlug = (lesson) => {
    return getCourse(lesson)?.slug || ''
}

const getHashtagTitle = (hashtag) => {
    return hashtag?.translation?.name
        || hashtag?.translation?.title
        || hashtag?.slug
        || ''
}

const getHashtagsText = (lesson) => {
    const hashtags = Array.isArray(lesson?.hashtags)
        ? lesson.hashtags
        : []

    return hashtags
        .map(getHashtagTitle)
        .filter(Boolean)
        .join(' ')
}

/* ==========================================================
 * HELPERS
 * ========================================================== */

const normalize = (value) => {
    return String(value ?? '')
        .trim()
        .toLocaleLowerCase()
}

const safeNumber = (value) => {
    const number = Number(value)

    return Number.isFinite(number)
        ? number
        : 0
}

const safeDate = (value) => {
    const time = new Date(
        value || 0
    ).getTime()

    return Number.isFinite(time)
        ? time
        : 0
}

const compareText = (a, b) => {
    return normalize(a).localeCompare(
        normalize(b),
        props.currentLocale || undefined,
        {
            sensitivity: 'base',
        }
    )
}

/* ==========================================================
 * FRONTEND SORT
 * ========================================================== */

const byNumberAsc = (field) => {
    return (a, b) =>
        safeNumber(a?.[field])
        - safeNumber(b?.[field])
        || safeNumber(a?.id)
        - safeNumber(b?.id)
}

const byNumberDesc = (field) => {
    return (a, b) =>
        safeNumber(b?.[field])
        - safeNumber(a?.[field])
        || safeNumber(b?.id)
        - safeNumber(a?.id)
}

const byStringAsc = (field) => {
    return (a, b) =>
        compareText(
            a?.[field],
            b?.[field]
        )
        || safeNumber(a?.id)
        - safeNumber(b?.id)
}

const byStringDesc = (field) => {
    return (a, b) =>
        compareText(
            b?.[field],
            a?.[field]
        )
        || safeNumber(b?.id)
        - safeNumber(a?.id)
}

/**
 * Должен совпадать:
 *
 * SchoolLesson::scopeSortByParam()
 * SortSelect.vue
 * frontend sort.
 */
const sortLessons = (items) => {
    const list = [
        ...(items || []),
    ]

    if (sortParam.value === 'activity') {
        return list.filter(
            lesson => !!lesson.activity
        )
    }

    if (sortParam.value === 'inactive') {
        return list.filter(
            lesson => !lesson.activity
        )
    }

    const sortMap = {
        idAsc:
            byNumberAsc('id'),

        idDesc:
            byNumberDesc('id'),

        sortAsc:
            byNumberAsc('sort'),

        sortDesc:
            byNumberDesc('sort'),

        moduleAsc:
            byNumberAsc('school_module_id'),

        moduleDesc:
            byNumberDesc('school_module_id'),

        titleAsc: (a, b) =>
            compareText(
                getLessonTitle(a),
                getLessonTitle(b)
            )
            || safeNumber(a?.id)
            - safeNumber(b?.id),

        titleDesc: (a, b) =>
            compareText(
                getLessonTitle(b),
                getLessonTitle(a)
            )
            || safeNumber(b?.id)
            - safeNumber(a?.id),

        slugAsc: (a, b) =>
            compareText(
                getLessonSlug(a),
                getLessonSlug(b)
            )
            || safeNumber(a?.id)
            - safeNumber(b?.id),

        slugDesc: (a, b) =>
            compareText(
                getLessonSlug(b),
                getLessonSlug(a)
            )
            || safeNumber(b?.id)
            - safeNumber(a?.id),

        statusAsc:
            byStringAsc('status'),

        statusDesc:
            byStringDesc('status'),

        availabilityAsc:
            byStringAsc('availability'),

        availabilityDesc:
            byStringDesc('availability'),

        accessTypeAsc:
            byStringAsc('access_type'),

        accessTypeDesc:
            byStringDesc('access_type'),

        contentTypeAsc:
            byStringAsc('content_type'),

        contentTypeDesc:
            byStringDesc('content_type'),

        contentIdAsc:
            byNumberAsc('content_id'),

        contentIdDesc:
            byNumberDesc('content_id'),

        difficultyAsc:
            byNumberAsc('difficulty'),

        difficultyDesc:
            byNumberDesc('difficulty'),

        durationAsc:
            byNumberAsc('duration'),

        durationDesc:
            byNumberDesc('duration'),

        previewValueAsc:
            byNumberAsc('preview_value'),

        previewValueDesc:
            byNumberDesc('preview_value'),

        popularityAsc:
            byNumberAsc('popularity'),

        popularityDesc:
            byNumberDesc('popularity'),

        ratingCountAsc:
            byNumberAsc('rating_count'),

        ratingCountDesc:
            byNumberDesc('rating_count'),

        ratingAvgAsc:
            byNumberAsc('rating_avg'),

        ratingAvgDesc:
            byNumberDesc('rating_avg'),

        viewsAsc:
            byNumberAsc('views'),

        viewsDesc:
            byNumberDesc('views'),

        likesAsc:
            byNumberAsc('likes'),

        likesDesc:
            byNumberDesc('likes'),

        likesCountAsc:
            byNumberAsc('likes_count'),

        likesCountDesc:
            byNumberDesc('likes_count'),

        imagesAsc:
            byNumberAsc('images_count'),

        imagesDesc:
            byNumberDesc('images_count'),

        hashtagsAsc:
            byNumberAsc('hashtags_count'),

        hashtagsDesc:
            byNumberDesc('hashtags_count'),

        activityAsc:
            byNumberAsc('activity'),

        activityDesc:
            byNumberDesc('activity'),

        publishedAtAsc: (a, b) =>
            safeDate(a?.published_at)
            - safeDate(b?.published_at)
            || safeNumber(a?.id)
            - safeNumber(b?.id),

        publishedAtDesc: (a, b) =>
            safeDate(b?.published_at)
            - safeDate(a?.published_at)
            || safeNumber(b?.id)
            - safeNumber(a?.id),

        dateAsc: (a, b) =>
            safeDate(a?.published_at)
            - safeDate(b?.published_at)
            || safeNumber(a?.id)
            - safeNumber(b?.id),

        dateDesc: (a, b) =>
            safeDate(b?.published_at)
            - safeDate(a?.published_at)
            || safeNumber(b?.id)
            - safeNumber(a?.id),

        createdAtAsc: (a, b) =>
            safeDate(a?.created_at)
            - safeDate(b?.created_at)
            || safeNumber(a?.id)
            - safeNumber(b?.id),

        createdAtDesc: (a, b) =>
            safeDate(b?.created_at)
            - safeDate(a?.created_at)
            || safeNumber(b?.id)
            - safeNumber(a?.id),

        updatedAtAsc: (a, b) =>
            safeDate(a?.updated_at)
            - safeDate(b?.updated_at)
            || safeNumber(a?.id)
            - safeNumber(b?.id),

        updatedAtDesc: (a, b) =>
            safeDate(b?.updated_at)
            - safeDate(a?.updated_at)
            || safeNumber(b?.id)
            - safeNumber(a?.id),
    }

    const sorter =
        sortMap[sortParam.value]

    return sorter
        ? list.sort(sorter)
        : list
}

/* ==========================================================
 * FRONTEND SEARCH
 * ========================================================== */

const filteredLessons = computed(() => {
    let lessons =
        localLessons.value || []

    /**
     * Server уже выполнил
     * поиск и сортировку.
     *
     * Не фильтруем повторно
     * текущую страницу paginator.
     */
    if (props.useServerProcessing) {
        return lessons
    }

    const query =
        normalize(searchQuery.value)

    if (query) {
        lessons = lessons.filter((lesson) => {
            const title =
                normalize(
                    getLessonTitle(lesson)
                )

            const subtitle =
                normalize(
                    getLessonSubtitle(lesson)
                )

            const slug =
                normalize(
                    getLessonSlug(lesson)
                )

            const short =
                normalize(
                    getLessonShort(lesson)
                )

            const description =
                normalize(
                    getLessonDescription(lesson)
                )

            const moduleTitle =
                normalize(
                    getModuleTitle(lesson)
                )

            const moduleSlug =
                normalize(
                    getModuleSlug(lesson)
                )

            const courseTitle =
                normalize(
                    getCourseTitle(lesson)
                )

            const courseSlug =
                normalize(
                    getCourseSlug(lesson)
                )

            const hashtags =
                normalize(
                    getHashtagsText(lesson)
                )

            const status =
                normalize(
                    lesson?.status
                )

            const availability =
                normalize(
                    lesson?.availability
                )

            const accessType =
                normalize(
                    lesson?.access_type
                )

            const contentType =
                normalize(
                    lesson?.content_type
                )

            const contentId =
                normalize(
                    lesson?.content_id
                )

            return title.includes(query)
                || subtitle.includes(query)
                || slug.includes(query)
                || short.includes(query)
                || description.includes(query)
                || moduleTitle.includes(query)
                || moduleSlug.includes(query)
                || courseTitle.includes(query)
                || courseSlug.includes(query)
                || hashtags.includes(query)
                || status.includes(query)
                || availability.includes(query)
                || accessType.includes(query)
                || contentType.includes(query)
                || contentId.includes(query)
        })
    }

    return sortLessons(
        lessons
    )
})

/* ==========================================================
 * FRONTEND PAGINATION
 * ========================================================== */

const paginatedLessons = computed(() => {
    const perPage =
        Number(itemsPerPage.value) || 6

    const start =
        (currentPage.value - 1)
        * perPage

    return filteredLessons.value.slice(
        start,
        start + perPage
    )
})

const displayedLessons = computed(() => {
    return props.useServerProcessing
        ? lessonsList.value
        : paginatedLessons.value
})

watch(
    [
        itemsPerPage,
        searchQuery,
    ],
    () => {
        if (!props.useServerProcessing) {
            currentPage.value = 1
        }
    }
)

/* ==========================================================
 * DELETE
 * ========================================================== */

const showConfirmDeleteModal =
    ref(false)

const lessonToDeleteId =
    ref(null)

const lessonToDeleteTitle =
    ref('')

const confirmDelete = (
    lessonOrId,
    title = null
) => {
    if (
        typeof lessonOrId === 'object'
        && lessonOrId !== null
    ) {
        lessonToDeleteId.value =
            lessonOrId.id

        lessonToDeleteTitle.value =
            title
            || getLessonTitle(
                lessonOrId
            )
    } else {
        lessonToDeleteId.value =
            lessonOrId

        lessonToDeleteTitle.value =
            title
            || `ID: ${lessonOrId}`
    }

    showConfirmDeleteModal.value =
        true
}

const closeModal = () => {
    showConfirmDeleteModal.value =
        false

    lessonToDeleteId.value =
        null

    lessonToDeleteTitle.value =
        ''
}

const deleteLesson = () => {
    if (
        lessonToDeleteId.value === null
    ) {
        return
    }

    const id =
        lessonToDeleteId.value

    const title =
        lessonToDeleteTitle.value

    router.delete(
        route(
            'admin.schoolLessons.destroy',
            {
                schoolLesson: id,
            }
        ),
        {
            preserveScroll: true,
            preserveState: false,

            onSuccess: () => {
                toast.success(
                    `Урок "${title || `ID: ${id}`}" удалён.`
                )
            },

            onError: (errors) => {
                const errorKey =
                    Object.keys(
                        errors || {}
                    )[0]

                const message =
                    errors?.general
                    || errors?.[errorKey]
                    || 'Произошла ошибка при удалении.'

                toast.error(
                    `${message} (Урок: ${title || `ID: ${id}`})`
                )
            },

            onFinish: () => {
                closeModal()
            },
        }
    )
}

/* ==========================================================
 * LOCAL UI PATCH
 * ========================================================== */

const patchLesson = (
    lessonId,
    payload
) => {
    const index =
        localLessons.value.findIndex(
            lesson => lesson.id === lessonId
        )

    if (index === -1) {
        return
    }

    localLessons.value[index] = {
        ...localLessons.value[index],
        ...payload,
    }
}

/* ==========================================================
 * SELECTION / BULK ACTIONS
 * ========================================================== */

const selectedLessons =
    ref([])

const toggleAll = (payload) => {
    const checked =
        payload?.checked
        ?? payload?.target?.checked
        ?? false

    const ids =
        payload?.ids
        ?? displayedLessons.value.map(
            lesson => lesson.id
        )

    if (checked) {
        selectedLessons.value = [
            ...new Set([
                ...selectedLessons.value,
                ...ids,
            ]),
        ]

        return
    }

    selectedLessons.value =
        selectedLessons.value.filter(
            id => !ids.includes(id)
        )
}

const toggleSelectLesson = (id) => {
    const index =
        selectedLessons.value.indexOf(id)

    if (index > -1) {
        selectedLessons.value.splice(
            index,
            1
        )

        return
    }

    selectedLessons.value.push(id)
}

/* ==========================================================
 * DRAG & DROP SORT
 * ========================================================== */

const handleSortOrderUpdate = (
    orderedIds
) => {
    /**
     * В server mode учитываем
     * реальную страницу paginator.
     */
    const startSort =
        (
            activeCurrentPage.value - 1
        )
        * Number(
            itemsPerPage.value || 6
        )

    const items =
        orderedIds.map(
            (id, index) => ({
                id,
                sort:
                    startSort
                    + index
                    + 1,
            })
        )

    if (!items.length) {
        return
    }

    router.put(
        route(
            'admin.actions.schoolLessons.updateSortBulk'
        ),
        {
            items,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                toast.success(
                    'Порядок уроков успешно обновлён.'
                )
            },

            onError: (errors) => {
                console.error(
                    'Ошибка обновления сортировки уроков:',
                    errors
                )

                toast.error(
                    errors?.message
                    || errors?.general
                    || 'Не удалось обновить порядок уроков.'
                )

                router.reload({
                    only: ['lessons'],
                    preserveScroll: true,
                })
            },
        }
    )
}

/* ==========================================================
 * BULK ACTIVITY
 * ========================================================== */

const bulkToggleActivity = (
    newActivity
) => {
    if (
        !selectedLessons.value.length
    ) {
        toast.warning(
            'Выберите уроки для активации/деактивации.'
        )

        return
    }

    const ids = [
        ...selectedLessons.value,
    ]

    router.put(
        route(
            'admin.actions.schoolLessons.bulkUpdateActivity'
        ),
        {
            ids,
            activity: newActivity,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                ids.forEach((id) => {
                    patchLesson(
                        id,
                        {
                            activity: newActivity,
                        }
                    )
                })

                selectedLessons.value = []

                toast.success(
                    'Активность выбранных уроков обновлена.'
                )
            },

            onError: (errors) => {
                toast.error(
                    errors?.ids
                    || errors?.activity
                    || errors?.general
                    || 'Ошибка массового обновления активности.'
                )
            },
        }
    )
}

const handleBulkAction = (event) => {
    const action =
        event.target.value

    if (action === 'selectAll') {
        toggleAll({
            target: {
                checked: true,
            },
        })
    }

    if (action === 'deselectAll') {
        toggleAll({
            target: {
                checked: false,
            },
        })
    }

    if (action === 'activate') {
        bulkToggleActivity(true)
    }

    if (action === 'deactivate') {
        bulkToggleActivity(false)
    }

    event.target.value = ''
}

/* ==========================================================
 * SINGLE ACTIVITY
 * ========================================================== */

const toggleActivity = (lesson) => {
    const newActivity =
        !lesson.activity

    const lessonTitle =
        getLessonTitle(lesson)

    const actionText =
        newActivity
            ? t('activated')
            : t('deactivated')

    router.put(
        route(
            'admin.actions.schoolLessons.updateActivity',
            {
                schoolLesson:
                lesson.id,
            }
        ),
        {
            activity: newActivity,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchLesson(
                    lesson.id,
                    {
                        activity: newActivity,
                    }
                )

                lesson.activity =
                    newActivity

                toast.success(
                    `Урок "${lessonTitle}" ${actionText}.`
                )
            },

            onError: (errors) => {
                toast.error(
                    errors?.activity
                    || errors?.general
                    || `Ошибка изменения активности для урока "${lessonTitle}".`
                )
            },
        }
    )
}

/* ==========================================================
 * CLONE
 * ========================================================== */

const cloneLesson = (lesson) => {
    router.post(
        route(
            'admin.actions.schoolLessons.clone',
            {
                schoolLesson:
                lesson.id,
            }
        ),
        {},
        {
            preserveScroll: true,

            onSuccess: () => {
                toast.success(
                    'Урок успешно клонирован.'
                )
            },

            onError: () => {
                toast.error(
                    'Ошибка при клонировании урока.'
                )
            },
        }
    )
}
</script>

<template>
    <AdminLayout :title="t('lessons')">
        <template #header>
            <TitlePage>
                {{ t('lessons') }}
            </TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3">
                    <DefaultButton :href="route('admin.schoolLessons.create')">
                        <template #icon>
                            <svg
                                class="w-4 h-4 fill-current opacity-50 shrink-0"
                                viewBox="0 0 16 16"
                            >
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"
                                />
                            </svg>
                        </template>

                        {{ t('addLesson') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminSchoolLessonsProcessingMode"
                        :mode="adminSchoolLessonsProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="lessonsCount"
                    />
                </div>

                <!-- Search -->
                <SearchInput
                    v-if="lessonsCount && !useServerProcessing"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <ServerSearchInput
                    v-if="lessonsCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <!-- Per page + sort -->
                <div
                    v-if="lessonsCount"
                    class="flex justify-between items-center flex-col md:flex-row my-3 gap-3"
                >
                    <ItemsPerPageSelect
                        v-if="!useServerProcessing"
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <ServerItemsPerPageSelect
                        v-else
                        :items-per-page="itemsPerPage"
                        update-route="admin.settings.updateAdminCountSchoolLessons"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="sortParam = $event"
                    />
                </div>

                <!-- Count + bulk + view -->
                <div
                    v-if="lessonsCount"
                    class="flex flex-col lg:flex-row items-center justify-between gap-3"
                >
                    <CountTable>
                        {{ lessonsCount }}
                    </CountTable>

                    <BulkActionSelect
                        @change="handleBulkAction"
                    />

                    <ToggleViewButton
                        v-model:viewMode="viewMode"
                    />
                </div>

                <!-- Top pagination -->
                <div
                    v-if="lessonsCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredLessons.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="lessons"
                    />
                </div>

                <!-- Table -->
                <LessonTable
                    v-if="viewMode === 'table'"
                    :lessons="displayedLessons"
                    :selected-lessons="selectedLessons"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectLesson"
                    @toggle-all="toggleAll"
                    @clone="cloneLesson"
                />

                <!-- Cards -->
                <LessonCardGrid
                    v-else
                    :lessons="displayedLessons"
                    :selected-lessons="selectedLessons"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectLesson"
                    @toggle-all="toggleAll"
                    @clone="cloneLesson"
                />

                <!-- Bottom pagination -->
                <div
                    v-if="lessonsCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredLessons.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="lessons"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            :onCancel="closeModal"
            :onConfirm="deleteLesson"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
            @close="closeModal"
        />
    </AdminLayout>
</template>
