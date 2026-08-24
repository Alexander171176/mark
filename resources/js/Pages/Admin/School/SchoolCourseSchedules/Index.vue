<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Admin SchoolCourseSchedule Index.
 *
 * Новый контракт:
 * - SchoolCourseScheduleSharedResource;
 * - одна выбранная locale;
 * - server | frontend | auto;
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

import BulkActionSelect from '@/Components/Admin/School/SchoolCourseSchedule/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/School/SchoolCourseSchedule/Sort/SortSelect.vue'
import CourseScheduleTable from '@/Components/Admin/School/SchoolCourseSchedule/Table/CourseScheduleTable.vue'
import CourseScheduleCardGrid from '@/Components/Admin/School/SchoolCourseSchedule/View/CourseScheduleCardGrid.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    currentLocale: {
        type: String,
        default: '',
    },

    availableLocales: {
        type: Array,
        default: () => [],
    },

    adminSchoolCourseSchedulesProcessingMode: {
        type: String,
        default: 'frontend',
    },

    useServerProcessing: {
        type: Boolean,
        default: false,
    },

    schedules: {
        type: [Array, Object],
        default: () => [],
    },

    schedulesCount: {
        type: Number,
        default: 0,
    },

    adminSchoolCourseSchedulesPerPage: {
        type: Number,
        default: 6,
    },

    adminSchoolCourseSchedulesDefaultSort: {
        type: String,
        default: 'idDesc',
    },

    sortParam: {
        type: String,
        default: '',
    },

    search: {
        type: String,
        default: '',
    },

    errors: {
        type: Object,
        default: () => ({}),
    },
})

/* ==========================================================
 * VIEW MODE
 * ========================================================== */

const viewMode = ref(
    localStorage.getItem(
        'admin_view_mode_course_schedules'
    ) || 'table'
)

watch(viewMode, (value) => {
    localStorage.setItem(
        'admin_view_mode_course_schedules',
        value
    )
})

/* ==========================================================
 * SOURCE DATA
 * ========================================================== */

const schedulesList = computed(() => {
    if (Array.isArray(props.schedules)) {
        return props.schedules
    }

    if (Array.isArray(props.schedules?.data)) {
        return props.schedules.data
    }

    return []
})

const localSchedules = ref([])

watch(
    schedulesList,
    (schedules) => {
        localSchedules.value = JSON.parse(
            JSON.stringify(
                schedules || []
            )
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
    props.adminSchoolCourseSchedulesPerPage || 6
)

const sortParam = ref(
    props.sortParam
    || props.adminSchoolCourseSchedulesDefaultSort
    || 'idDesc'
)

const searchQuery = ref(
    props.search || ''
)

const currentPage = ref(1)

/**
 * Реальная серверная страница paginator.
 */
const serverCurrentPage = computed(() => {
    return Number(
        props.schedules?.meta?.current_page
        ?? props.schedules?.current_page
        ?? 1
    ) || 1
})

const activeCurrentPage = computed(() => {
    return props.useServerProcessing
        ? serverCurrentPage.value
        : currentPage.value
})

/**
 * Количество элементов.
 *
 * В server mode настройкой занимается
 * ServerItemsPerPageSelect.
 */
watch(itemsPerPage, (newValue) => {
    if (props.useServerProcessing) {
        return
    }

    currentPage.value = 1

    router.put(
        route(
            'admin.settings.updateAdminCountSchoolCourseSchedules'
        ),
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

/**
 * Сортировка.
 */
watch(sortParam, (newValue) => {
    currentPage.value = 1

    router.put(
        route(
            'admin.settings.updateAdminSortSchoolCourseSchedules'
        ),
        {
            value: newValue,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                if (props.useServerProcessing) {
                    const query =
                        Object.fromEntries(
                            new URLSearchParams(
                                window.location.search
                            )
                        )

                    router.get(
                        window.location.pathname,
                        {
                            ...query,
                            sort:
                                newValue
                                || undefined,
                            page:
                            undefined,
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
 * NEW SHARED RESOURCE CONTRACT
 * ========================================================== */

/**
 * Schedule.
 */
const getScheduleTitle = (schedule) => {
    return schedule?.translation?.title
        || `ID: ${schedule?.id}`
}

const getScheduleSubtitle = (schedule) => {
    return schedule?.translation?.subtitle || ''
}

const getScheduleShort = (schedule) => {
    return schedule?.translation?.short || ''
}

const getScheduleDescription = (schedule) => {
    return schedule?.translation?.description || ''
}

/**
 * Course.
 */
const getCourseTitle = (schedule) => {
    return schedule?.course?.translation?.title
        || (
            schedule?.course?.id
                ? `ID: ${schedule.course.id}`
                : ''
        )
}

const getCourseSlug = (schedule) => {
    return schedule?.course?.slug || ''
}

/**
 * Instructor.
 */
const getInstructorTitle = (schedule) => {
    const instructor =
        schedule?.instructor

    if (!instructor) {
        return ''
    }

    return instructor?.translation?.title
        || instructor?.user?.name
        || (
            instructor?.id
                ? `ID: ${instructor.id}`
                : ''
        )
}

const getInstructorName = (schedule) => {
    return schedule?.instructor?.user?.name || ''
}

const getInstructorEmail = (schedule) => {
    return schedule?.instructor?.user?.email || ''
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
    const time =
        new Date(
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

const byDateAsc = (field) => {
    return (a, b) =>
        safeDate(a?.[field])
        - safeDate(b?.[field])
        || safeNumber(a?.id)
        - safeNumber(b?.id)
}

const byDateDesc = (field) => {
    return (a, b) =>
        safeDate(b?.[field])
        - safeDate(a?.[field])
        || safeNumber(b?.id)
        - safeNumber(a?.id)
}

/* ==========================================================
 * FRONTEND SORT
 * ========================================================== */

const sortSchedules = (items) => {
    const list = [
        ...(items || []),
    ]

    /**
     * Filter-параметры.
     */
    if (sortParam.value === 'activity') {
        return list.filter(
            schedule =>
                !!schedule.activity
        )
    }

    if (sortParam.value === 'inactive') {
        return list.filter(
            schedule =>
                !schedule.activity
        )
    }

    if (sortParam.value === 'online') {
        return list.filter(
            schedule =>
                !!schedule.is_online
        )
    }

    if (sortParam.value === 'offline') {
        return list.filter(
            schedule =>
                !schedule.is_online
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

        slugAsc:
            byStringAsc('slug'),

        slugDesc:
            byStringDesc('slug'),

        titleAsc: (a, b) =>
            compareText(
                getScheduleTitle(a),
                getScheduleTitle(b)
            )
            || safeNumber(a?.id)
            - safeNumber(b?.id),

        titleDesc: (a, b) =>
            compareText(
                getScheduleTitle(b),
                getScheduleTitle(a)
            )
            || safeNumber(b?.id)
            - safeNumber(a?.id),

        statusAsc:
            byStringAsc('status'),

        statusDesc:
            byStringDesc('status'),

        timezoneAsc:
            byStringAsc('timezone'),

        timezoneDesc:
            byStringDesc('timezone'),

        locationAsc:
            byStringAsc('location'),

        locationDesc:
            byStringDesc('location'),

        meetingUrlAsc:
            byStringAsc('meeting_url'),

        meetingUrlDesc:
            byStringDesc('meeting_url'),

        capacityAsc:
            byNumberAsc('capacity'),

        capacityDesc:
            byNumberDesc('capacity'),

        viewsAsc:
            byNumberAsc('views'),

        viewsDesc:
            byNumberDesc('views'),

        imagesAsc:
            byNumberAsc('images_count'),

        imagesDesc:
            byNumberDesc('images_count'),

        cohortEnrollmentsAsc:
            byNumberAsc(
                'cohort_enrollments_count'
            ),

        cohortEnrollmentsDesc:
            byNumberDesc(
                'cohort_enrollments_count'
            ),

        activityAsc:
            byNumberAsc('activity'),

        activityDesc:
            byNumberDesc('activity'),

        onlineAsc:
            byNumberAsc('is_online'),

        onlineDesc:
            byNumberDesc('is_online'),

        startsAtAsc:
            byDateAsc('starts_at'),

        startsAtDesc:
            byDateDesc('starts_at'),

        endsAtAsc:
            byDateAsc('ends_at'),

        endsAtDesc:
            byDateDesc('ends_at'),

        enrollStartsAtAsc:
            byDateAsc(
                'enroll_starts_at'
            ),

        enrollStartsAtDesc:
            byDateDesc(
                'enroll_starts_at'
            ),

        enrollEndsAtAsc:
            byDateAsc(
                'enroll_ends_at'
            ),

        enrollEndsAtDesc:
            byDateDesc(
                'enroll_ends_at'
            ),

        createdAtAsc:
            byDateAsc('created_at'),

        createdAtDesc:
            byDateDesc('created_at'),

        updatedAtAsc:
            byDateAsc('updated_at'),

        updatedAtDesc:
            byDateDesc('updated_at'),
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

const filteredSchedules = computed(() => {
    let schedules =
        localSchedules.value || []

    /**
     * Server уже выполнил:
     * - search;
     * - sort;
     * - pagination.
     *
     * Повторно обрабатывать только
     * текущую страницу нельзя.
     */
    if (props.useServerProcessing) {
        return schedules
    }

    const query =
        normalize(
            searchQuery.value
        )

    if (query) {
        schedules = schedules.filter(
            (schedule) => {
                const title =
                    normalize(
                        getScheduleTitle(
                            schedule
                        )
                    )

                const subtitle =
                    normalize(
                        getScheduleSubtitle(
                            schedule
                        )
                    )

                const short =
                    normalize(
                        getScheduleShort(
                            schedule
                        )
                    )

                const description =
                    normalize(
                        getScheduleDescription(
                            schedule
                        )
                    )

                const slug =
                    normalize(
                        schedule?.slug
                    )

                const location =
                    normalize(
                        schedule?.location
                    )

                const meetingUrl =
                    normalize(
                        schedule?.meeting_url
                    )

                const timezone =
                    normalize(
                        schedule?.timezone
                    )

                const status =
                    normalize(
                        schedule?.status
                    )

                const courseTitle =
                    normalize(
                        getCourseTitle(
                            schedule
                        )
                    )

                const courseSlug =
                    normalize(
                        getCourseSlug(
                            schedule
                        )
                    )

                const instructorTitle =
                    normalize(
                        getInstructorTitle(
                            schedule
                        )
                    )

                const instructorName =
                    normalize(
                        getInstructorName(
                            schedule
                        )
                    )

                const instructorEmail =
                    normalize(
                        getInstructorEmail(
                            schedule
                        )
                    )

                const ids = [
                    schedule?.id,
                    schedule?.sort,
                    schedule?.school_course_id,
                    schedule?.school_instructor_profile_id,
                    schedule?.capacity,
                    schedule?.views,
                    schedule?.images_count,
                    schedule?.cohort_enrollments_count,
                ]
                    .map(normalize)
                    .join(' ')

                return title.includes(query)
                    || subtitle.includes(query)
                    || short.includes(query)
                    || description.includes(query)
                    || slug.includes(query)
                    || location.includes(query)
                    || meetingUrl.includes(query)
                    || timezone.includes(query)
                    || status.includes(query)
                    || courseTitle.includes(query)
                    || courseSlug.includes(query)
                    || instructorTitle.includes(query)
                    || instructorName.includes(query)
                    || instructorEmail.includes(query)
                    || ids.includes(query)
            }
        )
    }

    return sortSchedules(
        schedules
    )
})

/* ==========================================================
 * FRONTEND PAGINATION
 * ========================================================== */

const paginatedSchedules = computed(() => {
    const perPage =
        Number(
            itemsPerPage.value
        ) || 6

    const start =
        (
            currentPage.value - 1
        ) * perPage

    return filteredSchedules.value.slice(
        start,
        start + perPage
    )
})

const displayedSchedules = computed(() => {
    return props.useServerProcessing
        ? schedulesList.value
        : paginatedSchedules.value
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

const scheduleToDeleteId =
    ref(null)

const scheduleToDeleteTitle =
    ref('')

const confirmDelete = (
    scheduleOrId,
    title = null
) => {
    if (
        typeof scheduleOrId === 'object'
        && scheduleOrId !== null
    ) {
        scheduleToDeleteId.value =
            scheduleOrId.id

        scheduleToDeleteTitle.value =
            title
            || getScheduleTitle(
                scheduleOrId
            )
    } else {
        scheduleToDeleteId.value =
            scheduleOrId

        scheduleToDeleteTitle.value =
            title
            || `ID: ${scheduleOrId}`
    }

    showConfirmDeleteModal.value =
        true
}

const closeModal = () => {
    showConfirmDeleteModal.value =
        false

    scheduleToDeleteId.value =
        null

    scheduleToDeleteTitle.value =
        ''
}

const deleteSchedule = () => {
    if (
        scheduleToDeleteId.value === null
    ) {
        return
    }

    const id =
        scheduleToDeleteId.value

    const title =
        scheduleToDeleteTitle.value

    router.delete(
        route(
            'admin.schoolCourseSchedules.destroy',
            {
                schoolCourseSchedule:
                id,
            }
        ),
        {
            preserveScroll: true,
            preserveState: false,

            onSuccess: () => {
                toast.success(
                    `Расписание "${title || `ID: ${id}`}" удалено.`
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
                    `${message} (Расписание: ${title || `ID: ${id}`})`
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

const patchSchedule = (
    scheduleId,
    payload
) => {
    const index =
        localSchedules.value.findIndex(
            schedule =>
                schedule.id === scheduleId
        )

    if (index === -1) {
        return
    }

    localSchedules.value[index] = {
        ...localSchedules.value[index],
        ...payload,
    }
}

/* ==========================================================
 * SELECTION
 * ========================================================== */

const selectedSchedules =
    ref([])

const toggleAll = (payload) => {
    const checked =
        payload?.checked
        ?? payload?.target?.checked
        ?? false

    const ids =
        payload?.ids
        ?? displayedSchedules.value.map(
            schedule =>
                schedule.id
        )

    if (checked) {
        selectedSchedules.value = [
            ...new Set([
                ...selectedSchedules.value,
                ...ids,
            ]),
        ]

        return
    }

    selectedSchedules.value =
        selectedSchedules.value.filter(
            id => !ids.includes(id)
        )
}

const toggleSelectSchedule = (id) => {
    const index =
        selectedSchedules.value.indexOf(
            id
        )

    if (index > -1) {
        selectedSchedules.value.splice(
            index,
            1
        )

        return
    }

    selectedSchedules.value.push(
        id
    )
}

/* ==========================================================
 * DRAG & DROP
 * ========================================================== */

const handleSortOrderUpdate = (
    orderedIds
) => {
    /**
     * Для server используем настоящую
     * страницу paginator.
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
            'admin.actions.schoolCourseSchedules.updateSortBulk'
        ),
        {
            items,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                toast.success(
                    'Порядок расписаний успешно обновлён.'
                )
            },

            onError: (errors) => {
                console.error(
                    'Ошибка обновления сортировки расписаний:',
                    errors
                )

                toast.error(
                    errors?.message
                    || errors?.general
                    || 'Не удалось обновить порядок расписаний.'
                )

                router.reload({
                    only: [
                        'schedules',
                    ],
                    preserveScroll: true,
                })
            },
        }
    )
}

/* ==========================================================
 * BULK ACTIONS
 * ========================================================== */

const bulkToggleActivity = (
    newActivity
) => {
    if (
        !selectedSchedules.value.length
    ) {
        toast.warning(
            'Выберите расписания для активации/деактивации.'
        )

        return
    }

    const ids = [
        ...selectedSchedules.value,
    ]

    router.put(
        route(
            'admin.actions.schoolCourseSchedules.bulkUpdateActivity'
        ),
        {
            ids,
            activity:
            newActivity,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                ids.forEach((id) => {
                    patchSchedule(
                        id,
                        {
                            activity:
                            newActivity,
                        }
                    )
                })

                selectedSchedules.value =
                    []

                toast.success(
                    'Активность выбранных расписаний обновлена.'
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

const bulkDelete = () => {
    if (
        !selectedSchedules.value.length
    ) {
        toast.warning(
            'Выберите расписания для удаления.'
        )

        return
    }

    if (
        !confirm(
            'Вы уверены, что хотите удалить выбранные расписания?'
        )
    ) {
        return
    }

    router.delete(
        route(
            'admin.actions.schoolCourseSchedules.bulkDestroy'
        ),
        {
            data: {
                ids:
                selectedSchedules.value,
            },

            preserveScroll: true,
            preserveState: false,

            onSuccess: () => {
                selectedSchedules.value =
                    []

                toast.success(
                    'Выбранные расписания успешно удалены.'
                )
            },

            onError: (errors) => {
                const errorKey =
                    Object.keys(
                        errors || {}
                    )[0]

                toast.error(
                    errors?.[errorKey]
                    || 'Ошибка массового удаления расписаний.'
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
        bulkToggleActivity(
            true
        )
    }

    if (action === 'deactivate') {
        bulkToggleActivity(
            false
        )
    }

    if (action === 'delete') {
        bulkDelete()
    }

    event.target.value = ''
}

/* ==========================================================
 * SINGLE ACTIONS
 * ========================================================== */

const toggleActivity = (schedule) => {
    const newActivity =
        !schedule.activity

    const scheduleTitle =
        getScheduleTitle(
            schedule
        )

    const actionText =
        newActivity
            ? t('activated')
            : t('deactivated')

    router.put(
        route(
            'admin.actions.schoolCourseSchedules.updateActivity',
            {
                schoolCourseSchedule:
                schedule.id,
            }
        ),
        {
            activity:
            newActivity,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchSchedule(
                    schedule.id,
                    {
                        activity:
                        newActivity,
                    }
                )

                schedule.activity =
                    newActivity

                toast.success(
                    `Расписание "${scheduleTitle}" ${actionText}.`
                )
            },

            onError: (errors) => {
                toast.error(
                    errors?.activity
                    || errors?.general
                    || `Ошибка изменения активности для расписания "${scheduleTitle}".`
                )
            },
        }
    )
}

const cloneSchedule = (schedule) => {
    router.post(
        route(
            'admin.actions.schoolCourseSchedules.clone',
            {
                schoolCourseSchedule:
                schedule.id,
            }
        ),
        {},
        {
            preserveScroll: true,

            onSuccess: () => {
                toast.success(
                    'Расписание успешно клонировано.'
                )
            },

            onError: () => {
                toast.error(
                    'Ошибка при клонировании расписания.'
                )
            },
        }
    )
}
</script>

<template>
    <AdminLayout :title="t('schedules')">
        <template #header>
            <TitlePage>
                {{ t('schedules') }}
            </TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <!-- Create + processing -->
                <div
                    class="sm:flex sm:justify-between
                           sm:items-center mb-3 gap-3"
                >
                    <DefaultButton
                        :href="route('admin.schoolCourseSchedules.create')"
                    >
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

                        {{ t('addSchedule') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminSchoolCourseSchedulesProcessingMode"
                        :mode="adminSchoolCourseSchedulesProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="schedulesCount"
                    />
                </div>

                <!-- Search -->
                <SearchInput
                    v-if="schedulesCount && !useServerProcessing"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <ServerSearchInput
                    v-if="schedulesCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <!-- Per page + sort -->
                <div
                    v-if="schedulesCount"
                    class="flex justify-between items-center
                           flex-col md:flex-row my-3 gap-3"
                >
                    <ItemsPerPageSelect
                        v-if="!useServerProcessing"
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <ServerItemsPerPageSelect
                        v-else
                        :items-per-page="itemsPerPage"
                        update-route="admin.settings.updateAdminCountSchoolCourseSchedules"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="sortParam = $event"
                    />
                </div>

                <!-- Count + bulk + view -->
                <div
                    v-if="schedulesCount"
                    class="flex flex-col lg:flex-row
                           items-center justify-between gap-3"
                >
                    <CountTable>
                        {{ schedulesCount }}
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
                    v-if="schedulesCount"
                    class="flex justify-center items-center
                           flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredSchedules.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="schedules"
                    />
                </div>

                <!-- Table -->
                <CourseScheduleTable
                    v-if="viewMode === 'table'"
                    :schedules="displayedSchedules"
                    :selected-schedules="selectedSchedules"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @clone="cloneSchedule"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectSchedule"
                    @toggle-all="toggleAll"
                />

                <!-- Cards -->
                <CourseScheduleCardGrid
                    v-else
                    :schedules="displayedSchedules"
                    :selected-schedules="selectedSchedules"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @clone="cloneSchedule"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectSchedule"
                    @toggle-all="toggleAll"
                />

                <!-- Bottom pagination -->
                <div
                    v-if="schedulesCount"
                    class="flex justify-center items-center
                           flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredSchedules.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="schedules"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            :onCancel="closeModal"
            :onConfirm="deleteSchedule"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
            @close="closeModal"
        />
    </AdminLayout>
</template>
