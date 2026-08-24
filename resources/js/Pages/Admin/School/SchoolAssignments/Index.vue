<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список заданий школы.
 *
 * Admin Index contract:
 * - SchoolAssignmentSharedResource;
 * - translation содержит только выбранную locale;
 * - course/module/lesson/instructor используют SharedResource;
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

import BulkActionSelect from '@/Components/Admin/School/SchoolAssignment/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/School/SchoolAssignment/Sort/SortSelect.vue'
import AssignmentTable from '@/Components/Admin/School/SchoolAssignment/Table/AssignmentTable.vue'
import AssignmentCardGrid from '@/Components/Admin/School/SchoolAssignment/View/AssignmentCardGrid.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },

    adminSchoolAssignmentsProcessingMode: { type: String, default: 'frontend' },
    useServerProcessing: { type: Boolean, default: false },

    assignments: { type: [Array, Object], default: () => [] },
    assignmentsCount: { type: Number, default: 0 },

    adminSchoolAssignmentsPerPage: { type: Number, default: 6 },
    adminSchoolAssignmentsDefaultSort: { type: String, default: 'idDesc' },

    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },

    errors: { type: Object, default: () => ({}) },
})

/* ==========================================================
 * VIEW MODE
 * ========================================================== */

const viewMode = ref(
    localStorage.getItem('admin_view_mode_assignments') || 'table'
)

watch(viewMode, (value) => {
    localStorage.setItem(
        'admin_view_mode_assignments',
        value
    )
})

/* ==========================================================
 * SOURCE DATA
 * ========================================================== */

const assignmentsList = computed(() => {
    if (Array.isArray(props.assignments)) {
        return props.assignments
    }

    if (Array.isArray(props.assignments?.data)) {
        return props.assignments.data
    }

    return []
})

const localAssignments = ref([])

watch(
    assignmentsList,
    (assignments) => {
        localAssignments.value = JSON.parse(
            JSON.stringify(assignments || [])
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
    props.adminSchoolAssignmentsPerPage || 6
)

const sortParam = ref(
    props.sortParam
    || props.adminSchoolAssignmentsDefaultSort
    || 'idDesc'
)

const searchQuery = ref(
    props.search || ''
)

const currentPage = ref(1)

const serverCurrentPage = computed(() => {
    return Number(
        props.assignments?.meta?.current_page
        ?? props.assignments?.current_page
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
        route(
            'admin.settings.updateAdminCountSchoolAssignments'
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

watch(sortParam, (newValue) => {
    currentPage.value = 1

    router.put(
        route(
            'admin.settings.updateAdminSortSchoolAssignments'
        ),
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

const getAssignmentTitle = (assignment) => {
    return assignment?.translation?.title
        || `ID: ${assignment?.id}`
}

const getAssignmentSubtitle = (assignment) => {
    return assignment?.translation?.subtitle || ''
}

const getAssignmentShort = (assignment) => {
    return assignment?.translation?.short || ''
}

const getAssignmentDescription = (assignment) => {
    return assignment?.translation?.description || ''
}

const getAssignmentInstructions = (assignment) => {
    return assignment?.translation?.instructions || ''
}

const getCourseTitle = (assignment) => {
    return assignment?.course?.translation?.title
        || (
            assignment?.course?.id
                ? `ID: ${assignment.course.id}`
                : ''
        )
}

const getCourseSlug = (assignment) => {
    return assignment?.course?.slug || ''
}

const getModuleTitle = (assignment) => {
    return assignment?.module?.translation?.title
        || (
            assignment?.module?.id
                ? `ID: ${assignment.module.id}`
                : ''
        )
}

const getModuleSlug = (assignment) => {
    return assignment?.module?.slug || ''
}

const getLessonTitle = (assignment) => {
    return assignment?.lesson?.translation?.title
        || (
            assignment?.lesson?.id
                ? `ID: ${assignment.lesson.id}`
                : ''
        )
}

const getLessonSlug = (assignment) => {
    return assignment?.lesson?.slug || ''
}

const getInstructorTitle = (assignment) => {
    return assignment?.instructor?.translation?.title
        || assignment?.instructor?.user?.name
        || (
            assignment?.instructor?.id
                ? `ID: ${assignment.instructor.id}`
                : ''
        )
}

const getInstructorName = (assignment) => {
    return assignment?.instructor?.user?.name || ''
}

const getInstructorEmail = (assignment) => {
    return assignment?.instructor?.user?.email || ''
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

/* ==========================================================
 * FRONTEND SORT
 * ========================================================== */

const sortAssignments = (items) => {
    const list = [
        ...(items || []),
    ]

    if (sortParam.value === 'activity') {
        return list.filter(
            assignment => !!assignment.activity
        )
    }

    if (sortParam.value === 'inactive') {
        return list.filter(
            assignment => !assignment.activity
        )
    }

    if (sortParam.value === 'left') {
        return list.filter(
            assignment => !!assignment.left
        )
    }

    if (sortParam.value === 'noLeft') {
        return list.filter(
            assignment => !assignment.left
        )
    }

    if (sortParam.value === 'main') {
        return list.filter(
            assignment => !!assignment.main
        )
    }

    if (sortParam.value === 'noMain') {
        return list.filter(
            assignment => !assignment.main
        )
    }

    if (sortParam.value === 'right') {
        return list.filter(
            assignment => !!assignment.right
        )
    }

    if (sortParam.value === 'noRight') {
        return list.filter(
            assignment => !assignment.right
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
                getAssignmentTitle(a),
                getAssignmentTitle(b)
            )
            || safeNumber(a?.id)
            - safeNumber(b?.id),

        titleDesc: (a, b) =>
            compareText(
                getAssignmentTitle(b),
                getAssignmentTitle(a)
            )
            || safeNumber(b?.id)
            - safeNumber(a?.id),

        courseAsc:
            byNumberAsc('school_course_id'),

        courseDesc:
            byNumberDesc('school_course_id'),

        courseTitleAsc: (a, b) =>
            compareText(
                getCourseTitle(a),
                getCourseTitle(b)
            )
            || safeNumber(a?.id)
            - safeNumber(b?.id),

        courseTitleDesc: (a, b) =>
            compareText(
                getCourseTitle(b),
                getCourseTitle(a)
            )
            || safeNumber(b?.id)
            - safeNumber(a?.id),

        moduleAsc:
            byNumberAsc('school_module_id'),

        moduleDesc:
            byNumberDesc('school_module_id'),

        moduleTitleAsc: (a, b) =>
            compareText(
                getModuleTitle(a),
                getModuleTitle(b)
            )
            || safeNumber(a?.id)
            - safeNumber(b?.id),

        moduleTitleDesc: (a, b) =>
            compareText(
                getModuleTitle(b),
                getModuleTitle(a)
            )
            || safeNumber(b?.id)
            - safeNumber(a?.id),

        lessonAsc:
            byNumberAsc('school_lesson_id'),

        lessonDesc:
            byNumberDesc('school_lesson_id'),

        lessonTitleAsc: (a, b) =>
            compareText(
                getLessonTitle(a),
                getLessonTitle(b)
            )
            || safeNumber(a?.id)
            - safeNumber(b?.id),

        lessonTitleDesc: (a, b) =>
            compareText(
                getLessonTitle(b),
                getLessonTitle(a)
            )
            || safeNumber(b?.id)
            - safeNumber(a?.id),

        instructorAsc:
            byNumberAsc(
                'school_instructor_profile_id'
            ),

        instructorDesc:
            byNumberDesc(
                'school_instructor_profile_id'
            ),

        instructorTitleAsc: (a, b) =>
            compareText(
                getInstructorTitle(a),
                getInstructorTitle(b)
            )
            || safeNumber(a?.id)
            - safeNumber(b?.id),

        instructorTitleDesc: (a, b) =>
            compareText(
                getInstructorTitle(b),
                getInstructorTitle(a)
            )
            || safeNumber(b?.id)
            - safeNumber(a?.id),

        statusAsc:
            byStringAsc('status'),

        statusDesc:
            byStringDesc('status'),

        visibilityAsc:
            byStringAsc('visibility'),

        visibilityDesc:
            byStringDesc('visibility'),

        gradingTypeAsc:
            byStringAsc('grading_type'),

        gradingTypeDesc:
            byStringDesc('grading_type'),

        attemptsLimitAsc:
            byNumberAsc('attempts_limit'),

        attemptsLimitDesc:
            byNumberDesc('attempts_limit'),

        maxScoreAsc:
            byNumberAsc('max_score'),

        maxScoreDesc:
            byNumberDesc('max_score'),

        submissionsAsc:
            byNumberAsc('submissions_count'),

        submissionsDesc:
            byNumberDesc('submissions_count'),

        imagesAsc:
            byNumberAsc('images_count'),

        imagesDesc:
            byNumberDesc('images_count'),

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

        dueAtAsc: (a, b) =>
            safeDate(a?.due_at)
            - safeDate(b?.due_at)
            || safeNumber(a?.id)
            - safeNumber(b?.id),

        dueAtDesc: (a, b) =>
            safeDate(b?.due_at)
            - safeDate(a?.due_at)
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

const filteredAssignments = computed(() => {
    let assignments =
        localAssignments.value || []

    /**
     * В server mode backend уже выполнил
     * поиск и сортировку.
     *
     * Нельзя повторно фильтровать
     * только текущую страницу paginator.
     */
    if (props.useServerProcessing) {
        return assignments
    }

    const query =
        normalize(searchQuery.value)

    if (query) {
        assignments = assignments.filter(
            (assignment) => {
                const title =
                    normalize(
                        getAssignmentTitle(
                            assignment
                        )
                    )

                const subtitle =
                    normalize(
                        getAssignmentSubtitle(
                            assignment
                        )
                    )

                const slug =
                    normalize(
                        assignment?.slug
                    )

                const short =
                    normalize(
                        getAssignmentShort(
                            assignment
                        )
                    )

                const description =
                    normalize(
                        getAssignmentDescription(
                            assignment
                        )
                    )

                const instructions =
                    normalize(
                        getAssignmentInstructions(
                            assignment
                        )
                    )

                const courseTitle =
                    normalize(
                        getCourseTitle(
                            assignment
                        )
                    )

                const courseSlug =
                    normalize(
                        getCourseSlug(
                            assignment
                        )
                    )

                const moduleTitle =
                    normalize(
                        getModuleTitle(
                            assignment
                        )
                    )

                const moduleSlug =
                    normalize(
                        getModuleSlug(
                            assignment
                        )
                    )

                const lessonTitle =
                    normalize(
                        getLessonTitle(
                            assignment
                        )
                    )

                const lessonSlug =
                    normalize(
                        getLessonSlug(
                            assignment
                        )
                    )

                const instructorTitle =
                    normalize(
                        getInstructorTitle(
                            assignment
                        )
                    )

                const instructorName =
                    normalize(
                        getInstructorName(
                            assignment
                        )
                    )

                const instructorEmail =
                    normalize(
                        getInstructorEmail(
                            assignment
                        )
                    )

                const status =
                    normalize(
                        assignment?.status
                    )

                const visibility =
                    normalize(
                        assignment?.visibility
                    )

                const gradingType =
                    normalize(
                        assignment?.grading_type
                    )

                const ids = [
                    assignment?.id,
                    assignment?.sort,
                    assignment?.school_course_id,
                    assignment?.school_module_id,
                    assignment?.school_lesson_id,
                    assignment?.school_instructor_profile_id,
                    assignment?.max_score,
                    assignment?.attempts_limit,
                ]
                    .map(normalize)
                    .join(' ')

                return title.includes(query)
                    || subtitle.includes(query)
                    || slug.includes(query)
                    || short.includes(query)
                    || description.includes(query)
                    || instructions.includes(query)
                    || courseTitle.includes(query)
                    || courseSlug.includes(query)
                    || moduleTitle.includes(query)
                    || moduleSlug.includes(query)
                    || lessonTitle.includes(query)
                    || lessonSlug.includes(query)
                    || instructorTitle.includes(query)
                    || instructorName.includes(query)
                    || instructorEmail.includes(query)
                    || status.includes(query)
                    || visibility.includes(query)
                    || gradingType.includes(query)
                    || ids.includes(query)
            }
        )
    }

    return sortAssignments(
        assignments
    )
})

/* ==========================================================
 * FRONTEND PAGINATION
 * ========================================================== */

const paginatedAssignments = computed(() => {
    const perPage =
        Number(itemsPerPage.value) || 6

    const start =
        (currentPage.value - 1)
        * perPage

    return filteredAssignments.value.slice(
        start,
        start + perPage
    )
})

const displayedAssignments = computed(() => {
    return props.useServerProcessing
        ? assignmentsList.value
        : paginatedAssignments.value
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

const assignmentToDeleteId =
    ref(null)

const assignmentToDeleteTitle =
    ref('')

const confirmDelete = (
    assignmentOrId,
    title = null
) => {
    if (
        typeof assignmentOrId === 'object'
        && assignmentOrId !== null
    ) {
        assignmentToDeleteId.value =
            assignmentOrId.id

        assignmentToDeleteTitle.value =
            title
            || getAssignmentTitle(
                assignmentOrId
            )
    } else {
        assignmentToDeleteId.value =
            assignmentOrId

        assignmentToDeleteTitle.value =
            title
            || `ID: ${assignmentOrId}`
    }

    showConfirmDeleteModal.value =
        true
}

const closeModal = () => {
    showConfirmDeleteModal.value =
        false

    assignmentToDeleteId.value =
        null

    assignmentToDeleteTitle.value =
        ''
}

const deleteAssignment = () => {
    if (
        assignmentToDeleteId.value === null
    ) {
        return
    }

    const id =
        assignmentToDeleteId.value

    const title =
        assignmentToDeleteTitle.value

    router.delete(
        route(
            'admin.schoolAssignments.destroy',
            {
                schoolAssignment: id,
            }
        ),
        {
            preserveScroll: true,
            preserveState: false,

            onSuccess: () => {
                toast.success(
                    `Задание "${title || `ID: ${id}`}" удалено.`
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
                    `${message} (Задание: ${title || `ID: ${id}`})`
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

const patchAssignment = (
    assignmentId,
    payload
) => {
    const index =
        localAssignments.value.findIndex(
            assignment =>
                assignment.id === assignmentId
        )

    if (index === -1) {
        return
    }

    localAssignments.value[index] = {
        ...localAssignments.value[index],
        ...payload,
    }
}

/* ==========================================================
 * SELECTION / BULK ACTIONS
 * ========================================================== */

const selectedAssignments =
    ref([])

const toggleAll = (payload) => {
    const checked =
        payload?.checked
        ?? payload?.target?.checked
        ?? false

    const ids =
        payload?.ids
        ?? displayedAssignments.value.map(
            assignment => assignment.id
        )

    if (checked) {
        selectedAssignments.value = [
            ...new Set([
                ...selectedAssignments.value,
                ...ids,
            ]),
        ]

        return
    }

    selectedAssignments.value =
        selectedAssignments.value.filter(
            id => !ids.includes(id)
        )
}

const toggleSelectAssignment = (id) => {
    const index =
        selectedAssignments.value.indexOf(id)

    if (index > -1) {
        selectedAssignments.value.splice(
            index,
            1
        )

        return
    }

    selectedAssignments.value.push(id)
}

/* ==========================================================
 * DRAG & DROP SORT
 * ========================================================== */

const handleSortOrderUpdate = (
    orderedIds
) => {
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
            'admin.actions.schoolAssignments.updateSortBulk'
        ),
        {
            items,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                toast.success(
                    'Порядок заданий успешно обновлён.'
                )
            },

            onError: (errors) => {
                console.error(
                    'Ошибка обновления сортировки заданий:',
                    errors
                )

                toast.error(
                    errors?.message
                    || errors?.general
                    || 'Не удалось обновить порядок заданий.'
                )

                router.reload({
                    only: [
                        'assignments',
                    ],
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
        !selectedAssignments.value.length
    ) {
        toast.warning(
            'Выберите задания для активации/деактивации.'
        )

        return
    }

    const ids = [
        ...selectedAssignments.value,
    ]

    router.put(
        route(
            'admin.actions.schoolAssignments.bulkUpdateActivity'
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
                    patchAssignment(
                        id,
                        {
                            activity: newActivity,
                        }
                    )
                })

                selectedAssignments.value =
                    []

                toast.success(
                    'Активность выбранных заданий обновлена.'
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

/* ==========================================================
 * BULK FLAGS
 * ========================================================== */

const bulkToggleFlag = (
    field,
    newValue,
    routeName,
    successMessage
) => {
    if (
        !selectedAssignments.value.length
    ) {
        toast.warning(
            'Выберите задания для массового действия.'
        )

        return
    }

    const ids = [
        ...selectedAssignments.value,
    ]

    router.put(
        route(
            routeName
        ),
        {
            ids,
            [field]: newValue,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                ids.forEach((id) => {
                    patchAssignment(
                        id,
                        {
                            [field]: newValue,
                        }
                    )
                })

                selectedAssignments.value =
                    []

                toast.success(
                    successMessage
                )
            },

            onError: (errors) => {
                toast.error(
                    errors?.ids
                    || errors?.[field]
                    || errors?.general
                    || 'Ошибка массового обновления.'
                )
            },
        }
    )
}

const bulkDelete = () => {
    if (
        !selectedAssignments.value.length
    ) {
        toast.warning(
            'Выберите задания для удаления.'
        )

        return
    }

    if (
        !confirm(
            'Вы уверены, что хотите удалить выбранные задания?'
        )
    ) {
        return
    }

    router.delete(
        route(
            'admin.actions.schoolAssignments.bulkDestroy'
        ),
        {
            data: {
                ids:
                selectedAssignments.value,
            },

            preserveScroll: true,
            preserveState: false,

            onSuccess: () => {
                selectedAssignments.value =
                    []

                toast.success(
                    'Выбранные задания успешно удалены.'
                )
            },

            onError: (errors) => {
                const errorKey =
                    Object.keys(
                        errors || {}
                    )[0]

                toast.error(
                    errors?.[errorKey]
                    || 'Ошибка массового удаления заданий.'
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

    if (action === 'left') {
        bulkToggleFlag(
            'left',
            true,
            'admin.actions.schoolAssignments.bulkUpdateLeft',
            'Задания добавлены в левую колонку.'
        )
    }

    if (action === 'noLeft') {
        bulkToggleFlag(
            'left',
            false,
            'admin.actions.schoolAssignments.bulkUpdateLeft',
            'Задания убраны из левой колонки.'
        )
    }

    if (action === 'main') {
        bulkToggleFlag(
            'main',
            true,
            'admin.actions.schoolAssignments.bulkUpdateMain',
            'Задания добавлены в главный блок.'
        )
    }

    if (action === 'noMain') {
        bulkToggleFlag(
            'main',
            false,
            'admin.actions.schoolAssignments.bulkUpdateMain',
            'Задания убраны из главного блока.'
        )
    }

    if (action === 'right') {
        bulkToggleFlag(
            'right',
            true,
            'admin.actions.schoolAssignments.bulkUpdateRight',
            'Задания добавлены в правую колонку.'
        )
    }

    if (action === 'noRight') {
        bulkToggleFlag(
            'right',
            false,
            'admin.actions.schoolAssignments.bulkUpdateRight',
            'Задания убраны из правой колонки.'
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

const toggleActivity = (assignment) => {
    const newActivity =
        !assignment.activity

    const assignmentTitle =
        getAssignmentTitle(
            assignment
        )

    const actionText =
        newActivity
            ? t('activated')
            : t('deactivated')

    router.put(
        route(
            'admin.actions.schoolAssignments.updateActivity',
            {
                schoolAssignment:
                assignment.id,
            }
        ),
        {
            activity: newActivity,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchAssignment(
                    assignment.id,
                    {
                        activity: newActivity,
                    }
                )

                assignment.activity =
                    newActivity

                toast.success(
                    `Задание "${assignmentTitle}" ${actionText}.`
                )
            },

            onError: (errors) => {
                toast.error(
                    errors?.activity
                    || errors?.general
                    || `Ошибка изменения активности для задания "${assignmentTitle}".`
                )
            },
        }
    )
}

const toggleFlag = (
    assignment,
    field,
    routeName,
    successMessage,
    errorMessage
) => {
    const newValue =
        !assignment[field]

    router.put(
        route(
            routeName,
            {
                schoolAssignment:
                assignment.id,
            }
        ),
        {
            [field]:
            newValue,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                patchAssignment(
                    assignment.id,
                    {
                        [field]:
                        newValue,
                    }
                )

                assignment[field] =
                    newValue

                toast.success(
                    successMessage
                )
            },

            onError: (errors) => {
                toast.error(
                    errors?.[field]
                    || errors?.general
                    || errorMessage
                )
            },
        }
    )
}

const toggleLeft = (assignment) => {
    toggleFlag(
        assignment,
        'left',
        'admin.actions.schoolAssignments.updateLeft',
        'Левая колонка обновлена.',
        'Ошибка обновления левой колонки.'
    )
}

const toggleMain = (assignment) => {
    toggleFlag(
        assignment,
        'main',
        'admin.actions.schoolAssignments.updateMain',
        'Главный блок обновлён.',
        'Ошибка обновления главного блока.'
    )
}

const toggleRight = (assignment) => {
    toggleFlag(
        assignment,
        'right',
        'admin.actions.schoolAssignments.updateRight',
        'Правая колонка обновлена.',
        'Ошибка обновления правой колонки.'
    )
}

const cloneAssignment = (assignment) => {
    router.post(
        route(
            'admin.actions.schoolAssignments.clone',
            {
                schoolAssignment:
                assignment.id,
            }
        ),
        {},
        {
            preserveScroll: true,

            onSuccess: () => {
                toast.success(
                    'Задание успешно клонировано.'
                )
            },

            onError: () => {
                toast.error(
                    'Ошибка при клонировании задания.'
                )
            },
        }
    )
}
</script>

<template>
    <AdminLayout :title="t('assignments')">
        <template #header>
            <TitlePage>
                {{ t('assignments') }}
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
                    <DefaultButton :href="route('admin.schoolAssignments.create')">
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

                        {{ t('addAssignment') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminSchoolAssignmentsProcessingMode"
                        :mode="adminSchoolAssignmentsProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="assignmentsCount"
                    />
                </div>

                <!-- Search -->
                <SearchInput
                    v-if="assignmentsCount && !useServerProcessing"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <ServerSearchInput
                    v-if="assignmentsCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <!-- Per page + sort -->
                <div
                    v-if="assignmentsCount"
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
                        update-route="admin.settings.updateAdminCountSchoolAssignments"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="sortParam = $event"
                    />
                </div>

                <!-- Count + bulk + view -->
                <div
                    v-if="assignmentsCount"
                    class="flex flex-col lg:flex-row
                           items-center justify-between gap-3"
                >
                    <CountTable>
                        {{ assignmentsCount }}
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
                    v-if="assignmentsCount"
                    class="flex justify-center items-center
                           flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredAssignments.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="assignments"
                    />
                </div>

                <!-- Table -->
                <AssignmentTable
                    v-if="viewMode === 'table'"
                    :assignments="displayedAssignments"
                    :selected-assignments="selectedAssignments"
                    @toggle-activity="toggleActivity"
                    @toggle-left="toggleLeft"
                    @toggle-main="toggleMain"
                    @toggle-right="toggleRight"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectAssignment"
                    @toggle-all="toggleAll"
                    @clone="cloneAssignment"
                />

                <!-- Cards -->
                <AssignmentCardGrid
                    v-else
                    :assignments="displayedAssignments"
                    :selected-assignments="selectedAssignments"
                    @toggle-activity="toggleActivity"
                    @toggle-left="toggleLeft"
                    @toggle-main="toggleMain"
                    @toggle-right="toggleRight"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectAssignment"
                    @toggle-all="toggleAll"
                    @clone="cloneAssignment"
                />

                <!-- Bottom pagination -->
                <div
                    v-if="assignmentsCount"
                    class="flex justify-center items-center
                           flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredAssignments.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="assignments"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            :onCancel="closeModal"
            :onConfirm="deleteAssignment"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
            @close="closeModal"
        />
    </AdminLayout>
</template>
