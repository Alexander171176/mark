<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список заданий школы
 * - режимы обработки: frontend | server | auto
 * - локальный поиск/сортировка/пагинация
 * - серверный поиск/сортировка/пагинация
 */

import { computed, defineProps, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router } from '@inertiajs/vue3'

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

/* ==========================================================
 * БАЗОВЫЕ СЕРВИСЫ И PROPS
 * ========================================================== */

/** Локализация интерфейса */
const { t } = useI18n()

/** Уведомления */
const toast = useToast()

/** Данные страницы из Inertia */
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
 * РЕЖИМ ОТОБРАЖЕНИЯ
 * ========================================================== */

/** Текущий режим отображения (таблица / карточки) */
const viewMode = ref(localStorage.getItem('admin_view_mode_assignments') || 'table')

/** Сохраняем выбранный вид локально */
watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode_assignments', val)
})

/* ==========================================================
 * ИСТОЧНИК ДАННЫХ
 * ========================================================== */

/**
 * Унифицированный список заданий:
 * frontend → обычный массив
 * server → assignments.data
 */
const assignmentsList = computed(() => {
    if (Array.isArray(props.assignments)) {
        return props.assignments
    }

    if (Array.isArray(props.assignments?.data)) {
        return props.assignments.data
    }

    return []
})

/* ==========================================================
 * ЛОКАЛЬНОЕ ХРАНИЛИЩЕ ДАННЫХ
 * ========================================================== */

/**
 * Локальная копия списка.
 * Используется для:
 * - локального поиска
 * - локальной сортировки
 * - моментального обновления UI
 */
const localAssignments = ref([])

watch(
    assignmentsList,
    (newVal) => {
        localAssignments.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/* ==========================================================
 * НАСТРОЙКИ ПАГИНАЦИИ И СОРТИРОВКИ
 * ========================================================== */

/** Количество элементов на странице */
const itemsPerPage = ref(props.adminSchoolAssignmentsPerPage || 6)

watch(itemsPerPage, (newVal) => {
    router.put(
        route('admin.settings.updateAdminCountSchoolAssignments'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
            onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
        }
    )
})

/** Текущий параметр сортировки */
const sortParam = ref(props.sortParam || props.adminSchoolAssignmentsDefaultSort || 'idDesc')

watch(sortParam, (newVal) => {
    router.put(
        route('admin.settings.updateAdminSortSchoolAssignments'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                if (props.useServerProcessing) {
                    router.get(
                        window.location.pathname,
                        {
                            ...Object.fromEntries(new URLSearchParams(window.location.search)),
                            sort: newVal || undefined,
                            page: undefined,
                        },
                        {
                            preserveScroll: true,
                            preserveState: false,
                            replace: true,
                        }
                    )
                }

                toast.info('Сортировка успешно изменена')
            },

            onError: (errors) => {
                toast.error(errors.value || 'Ошибка обновления сортировки.')
            },
        }
    )
})

/* ==========================================================
 * ПОИСК И ПАГИНАЦИЯ
 * ========================================================== */

/** Поисковый запрос */
const searchQuery = ref(props.search || '')

/** Текущая страница */
const currentPage = ref(1)

/* ==========================================================
 * ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ
 * ========================================================== */

/** Нормализация строки */
const normalize = (value) => (value ?? '').toString().trim().toLowerCase()

/** Безопасное преобразование в число */
const safeNumber = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

/** Безопасное преобразование даты */
const safeDate = (value) => {
    const time = new Date(value || 0).getTime()
    return Number.isFinite(time) ? time : 0
}

/* ==========================================================
 * ИЗВЛЕЧЕНИЕ ДАННЫХ ИЗ РЕСУРСОВ
 * ========================================================== */

/** Получение заголовка задания */
const getAssignmentTitle = (assignment) => {
    return assignment?.title
        || assignment?.translation?.title
        || assignment?.translations?.[0]?.title
        || `ID: ${assignment?.id}`
}

/** Получение краткого описания */
const getAssignmentShort = (assignment) => {
    return assignment?.short
        || assignment?.translation?.short
        || assignment?.translations?.[0]?.short
        || ''
}

/** Получение описания */
const getAssignmentDescription = (assignment) => {
    return assignment?.description
        || assignment?.translation?.description
        || assignment?.translations?.[0]?.description
        || ''
}

/** Получение инструкции */
const getAssignmentInstructions = (assignment) => {
    return assignment?.instructions
        || assignment?.translation?.instructions
        || assignment?.translations?.[0]?.instructions
        || ''
}

/** Получение заголовка связанной сущности */
const getNestedTitle = (item, field) => {
    const entity = item?.[field]

    return entity?.title
        || entity?.name
        || entity?.public_name
        || entity?.translation?.title
        || entity?.translation?.name
        || entity?.translations?.[0]?.title
        || entity?.translations?.[0]?.name
        || entity?.user?.name
        || ''
}

/* ==========================================================
 * СОРТИРОВКА FRONTEND
 * ========================================================== */

/** Сортировка чисел ↑ */
const byNumberAsc = (field) => (a, b) =>
    safeNumber(a?.[field]) - safeNumber(b?.[field])
    || safeNumber(a?.id) - safeNumber(b?.id)

/** Сортировка чисел ↓ */
const byNumberDesc = (field) => (a, b) =>
    safeNumber(b?.[field]) - safeNumber(a?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

/** Сортировка строк ↑ */
const byStringAsc = (field) => (a, b) =>
    normalize(a?.[field]).localeCompare(normalize(b?.[field]), props.currentLocale)
    || safeNumber(a?.id) - safeNumber(b?.id)

/** Сортировка строк ↓ */
const byStringDesc = (field) => (a, b) =>
    normalize(b?.[field]).localeCompare(normalize(a?.[field]), props.currentLocale)
    || safeNumber(b?.id) - safeNumber(a?.id)

/**
 * Главный обработчик сортировки.
 * Должен совпадать со scopeSortByParam() модели.
 */
const sortAssignments = (items) => {
    const list = (items || []).slice()

    if (sortParam.value === 'activity') return list.filter(item => !!item.activity)
    if (sortParam.value === 'inactive') return list.filter(item => !item.activity)

    if (sortParam.value === 'left') return list.filter(item => !!item.left)
    if (sortParam.value === 'noLeft') return list.filter(item => !item.left)

    if (sortParam.value === 'main') return list.filter(item => !!item.main)
    if (sortParam.value === 'noMain') return list.filter(item => !item.main)

    if (sortParam.value === 'right') return list.filter(item => !!item.right)
    if (sortParam.value === 'noRight') return list.filter(item => !item.right)

    const sortMap = {
        idAsc: byNumberAsc('id'),
        idDesc: byNumberDesc('id'),

        sortAsc: byNumberAsc('sort'),
        sortDesc: byNumberDesc('sort'),

        titleAsc: (a, b) =>
            normalize(getAssignmentTitle(a)).localeCompare(normalize(getAssignmentTitle(b)), props.currentLocale)
            || safeNumber(a?.id) - safeNumber(b?.id),

        titleDesc: (a, b) =>
            normalize(getAssignmentTitle(b)).localeCompare(normalize(getAssignmentTitle(a)), props.currentLocale)
            || safeNumber(b?.id) - safeNumber(a?.id),

        slugAsc: byStringAsc('slug'),
        slugDesc: byStringDesc('slug'),

        courseAsc: byNumberAsc('school_course_id'),
        courseDesc: byNumberDesc('school_course_id'),

        moduleAsc: byNumberAsc('school_module_id'),
        moduleDesc: byNumberDesc('school_module_id'),

        lessonAsc: byNumberAsc('school_lesson_id'),
        lessonDesc: byNumberDesc('school_lesson_id'),

        instructorAsc: byNumberAsc('school_instructor_profile_id'),
        instructorDesc: byNumberDesc('school_instructor_profile_id'),

        courseTitleAsc: (a, b) =>
            normalize(getNestedTitle(a, 'course')).localeCompare(normalize(getNestedTitle(b, 'course')), props.currentLocale)
            || safeNumber(a?.id) - safeNumber(b?.id),

        courseTitleDesc: (a, b) =>
            normalize(getNestedTitle(b, 'course')).localeCompare(normalize(getNestedTitle(a, 'course')), props.currentLocale)
            || safeNumber(b?.id) - safeNumber(a?.id),

        moduleTitleAsc: (a, b) =>
            normalize(getNestedTitle(a, 'module')).localeCompare(normalize(getNestedTitle(b, 'module')), props.currentLocale)
            || safeNumber(a?.id) - safeNumber(b?.id),

        moduleTitleDesc: (a, b) =>
            normalize(getNestedTitle(b, 'module')).localeCompare(normalize(getNestedTitle(a, 'module')), props.currentLocale)
            || safeNumber(b?.id) - safeNumber(a?.id),

        lessonTitleAsc: (a, b) =>
            normalize(getNestedTitle(a, 'lesson')).localeCompare(normalize(getNestedTitle(b, 'lesson')), props.currentLocale)
            || safeNumber(a?.id) - safeNumber(b?.id),

        lessonTitleDesc: (a, b) =>
            normalize(getNestedTitle(b, 'lesson')).localeCompare(normalize(getNestedTitle(a, 'lesson')), props.currentLocale)
            || safeNumber(b?.id) - safeNumber(a?.id),

        instructorTitleAsc: (a, b) =>
            normalize(getNestedTitle(a, 'instructor')).localeCompare(normalize(getNestedTitle(b, 'instructor')), props.currentLocale)
            || safeNumber(a?.id) - safeNumber(b?.id),

        instructorTitleDesc: (a, b) =>
            normalize(getNestedTitle(b, 'instructor')).localeCompare(normalize(getNestedTitle(a, 'instructor')), props.currentLocale)
            || safeNumber(b?.id) - safeNumber(a?.id),

        statusAsc: byStringAsc('status'),
        statusDesc: byStringDesc('status'),

        visibilityAsc: byStringAsc('visibility'),
        visibilityDesc: byStringDesc('visibility'),

        gradingTypeAsc: byStringAsc('grading_type'),
        gradingTypeDesc: byStringDesc('grading_type'),

        attemptsLimitAsc: byNumberAsc('attempts_limit'),
        attemptsLimitDesc: byNumberDesc('attempts_limit'),

        maxScoreAsc: byNumberAsc('max_score'),
        maxScoreDesc: byNumberDesc('max_score'),

        submissionsAsc: byNumberAsc('submissions_count'),
        submissionsDesc: byNumberDesc('submissions_count'),

        imagesAsc: byNumberAsc('images_count'),
        imagesDesc: byNumberDesc('images_count'),

        activityAsc: byNumberAsc('activity'),
        activityDesc: byNumberDesc('activity'),

        publishedAtAsc: (a, b) =>
            safeDate(a?.published_at) - safeDate(b?.published_at)
            || safeNumber(a?.id) - safeNumber(b?.id),

        publishedAtDesc: (a, b) =>
            safeDate(b?.published_at) - safeDate(a?.published_at)
            || safeNumber(b?.id) - safeNumber(a?.id),

        dueAtAsc: (a, b) =>
            safeDate(a?.due_at) - safeDate(b?.due_at)
            || safeNumber(a?.id) - safeNumber(b?.id),

        dueAtDesc: (a, b) =>
            safeDate(b?.due_at) - safeDate(a?.due_at)
            || safeNumber(b?.id) - safeNumber(a?.id),

        createdAtAsc: (a, b) =>
            safeDate(a?.created_at) - safeDate(b?.created_at)
            || safeNumber(a?.id) - safeNumber(b?.id),

        createdAtDesc: (a, b) =>
            safeDate(b?.created_at) - safeDate(a?.created_at)
            || safeNumber(b?.id) - safeNumber(a?.id),

        updatedAtAsc: (a, b) =>
            safeDate(a?.updated_at) - safeDate(b?.updated_at)
            || safeNumber(a?.id) - safeNumber(b?.id),

        updatedAtDesc: (a, b) =>
            safeDate(b?.updated_at) - safeDate(a?.updated_at)
            || safeNumber(b?.id) - safeNumber(a?.id),
    }

    return sortMap[sortParam.value]
        ? list.sort(sortMap[sortParam.value])
        : list
}

/* ==========================================================
 * ПОИСК FRONTEND
 * ========================================================== */

/**
 * Фильтрация списка.
 *
 * frontend:
 * поиск выполняется здесь
 *
 * server:
 * поиск выполняется контроллером
 */
const filteredAssignments = computed(() => {
    let filtered = localAssignments.value || []
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortAssignments(filtered)
    }

    filtered = filtered.filter((assignment) => {
        const title = normalize(getAssignmentTitle(assignment))
        const subtitle = normalize(assignment?.subtitle || assignment?.translation?.subtitle)
        const slug = normalize(assignment?.slug || assignment?.translation?.slug)
        const short = normalize(getAssignmentShort(assignment))
        const description = normalize(getAssignmentDescription(assignment))
        const instructions = normalize(getAssignmentInstructions(assignment))

        const courseTitle = normalize(getNestedTitle(assignment, 'course'))
        const moduleTitle = normalize(getNestedTitle(assignment, 'module'))
        const lessonTitle = normalize(getNestedTitle(assignment, 'lesson'))
        const instructorTitle = normalize(getNestedTitle(assignment, 'instructor'))
        const instructorName = normalize(assignment?.instructor?.user?.name)
        const instructorEmail = normalize(assignment?.instructor?.user?.email)

        return title.includes(query)
            || subtitle.includes(query)
            || slug.includes(query)
            || short.includes(query)
            || description.includes(query)
            || instructions.includes(query)
            || courseTitle.includes(query)
            || moduleTitle.includes(query)
            || lessonTitle.includes(query)
            || instructorTitle.includes(query)
            || instructorName.includes(query)
            || instructorEmail.includes(query)
    })

    return sortAssignments(filtered)
})

/* ==========================================================
 * ЛОКАЛЬНАЯ ПАГИНАЦИЯ
 * ========================================================== */

/** Разбиение списка по страницам */
const paginatedAssignments = computed(() => {
    const per = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * per

    return filteredAssignments.value.slice(start, start + per)
})

/**
 * Итоговый список:
 * frontend → локальная пагинация
 * server → данные сервера
 */
const displayedAssignments = computed(() => {
    return props.useServerProcessing
        ? assignmentsList.value
        : paginatedAssignments.value
})

watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

/* ==========================================================
 * УДАЛЕНИЕ
 * ========================================================== */

/** Состояние модального окна удаления */
const showConfirmDeleteModal = ref(false)
const assignmentToDeleteId = ref(null)
const assignmentToDeleteTitle = ref('')

/** Открыть подтверждение удаления */
const confirmDelete = (assignmentOrId, title = null) => {
    if (typeof assignmentOrId === 'object') {
        assignmentToDeleteId.value = assignmentOrId.id
        assignmentToDeleteTitle.value = title || getAssignmentTitle(assignmentOrId)
    } else {
        assignmentToDeleteId.value = assignmentOrId
        assignmentToDeleteTitle.value = title || `ID: ${assignmentOrId}`
    }

    showConfirmDeleteModal.value = true
}

/** Закрыть окно */
const closeModal = () => {
    showConfirmDeleteModal.value = false
    assignmentToDeleteId.value = null
    assignmentToDeleteTitle.value = ''
}

/** Выполнить удаление */
const deleteAssignment = () => {
    if (assignmentToDeleteId.value === null) return

    const idToDelete = assignmentToDeleteId.value
    const titleToDelete = assignmentToDeleteTitle.value

    router.delete(route('admin.schoolAssignments.destroy', { schoolAssignment: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            toast.success(`Задание "${titleToDelete || 'ID: ' + idToDelete}" удалено.`)
        },
        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            const errorMsg = errors.general || errors[errorKey] || 'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Задание: ${titleToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => closeModal(),
    })
}

/* ==========================================================
 * ЛОКАЛЬНОЕ ОБНОВЛЕНИЕ UI
 * ========================================================== */

/**
 * Обновление записи локально
 * без полной перезагрузки страницы
 */
const patchAssignment = (assignmentId, payload) => {
    const index = localAssignments.value.findIndex(assignment => assignment.id === assignmentId)

    if (index !== -1) {
        localAssignments.value[index] = {
            ...localAssignments.value[index],
            ...payload,
        }
    }
}

/* ==========================================================
 * МАССОВЫЕ ОПЕРАЦИИ
 * ========================================================== */

/** Выбранные элементы */
const selectedAssignments = ref([])

/** Выбрать все */
const toggleAll = (payload) => {
    const checked = payload?.checked ?? payload?.target?.checked ?? false
    const ids = payload?.ids ?? displayedAssignments.value.map((assignment) => assignment.id)

    if (checked) {
        selectedAssignments.value = [...new Set([...selectedAssignments.value, ...ids])]
    } else {
        selectedAssignments.value = selectedAssignments.value.filter((id) => !ids.includes(id))
    }
}

/** Выбрать элемент */
const toggleSelectAssignment = (id) => {
    const index = selectedAssignments.value.indexOf(id)

    if (index > -1) {
        selectedAssignments.value.splice(index, 1)
    } else {
        selectedAssignments.value.push(id)
    }
}

/** Изменить порядок */
const handleSortOrderUpdate = (orderedIds) => {
    const startSort = (currentPage.value - 1) * itemsPerPage.value

    const items = orderedIds.map((id, index) => ({
        id,
        sort: startSort + index + 1,
    }))

    if (!items.length) return

    router.put(route('admin.actions.schoolAssignments.updateSortBulk'), { items }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.success('Порядок заданий успешно обновлён.'),
        onError: (errors) => {
            console.error('Ошибка обновления сортировки заданий:', errors)
            toast.error(errors?.message || errors?.general || 'Не удалось обновить порядок заданий.')

            router.reload({
                only: ['assignments'],
                preserveScroll: true,
            })
        },
    })
}

/** Массовая активность */
const bulkToggleActivity = (newActivity) => {
    if (!selectedAssignments.value.length) {
        toast.warning('Выберите задания для активации/деактивации.')
        return
    }

    const idsToUpdate = [...selectedAssignments.value]

    router.put(route('admin.actions.schoolAssignments.bulkUpdateActivity'), {
        ids: idsToUpdate,
        activity: newActivity,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            idsToUpdate.forEach(id => patchAssignment(id, { activity: newActivity }))
            selectedAssignments.value = []
            toast.success('Активность выбранных заданий обновлена.')
        },
        onError: (errors) => {
            toast.error(errors?.ids || errors?.activity || errors?.general || 'Ошибка массового обновления активности.')
        },
    })
}

/** Массовые флаги */
const bulkToggleFlag = (field, newValue, routeName, successMessage) => {
    if (!selectedAssignments.value.length) {
        toast.warning('Выберите задания для массового действия.')
        return
    }

    const idsToUpdate = [...selectedAssignments.value]

    router.put(route(routeName), {
        ids: idsToUpdate,
        [field]: newValue,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            idsToUpdate.forEach(id => patchAssignment(id, { [field]: newValue }))
            selectedAssignments.value = []
            toast.success(successMessage)
        },
        onError: (errors) => {
            toast.error(errors?.ids || errors?.[field] || errors?.general || 'Ошибка массового обновления.')
        },
    })
}

/** Массовое удаление */
const bulkDelete = () => {
    if (!selectedAssignments.value.length) {
        toast.warning('Выберите задания для удаления.')
        return
    }

    if (!confirm('Вы уверены, что хотите удалить выбранные задания?')) return

    router.delete(route('admin.actions.schoolAssignments.bulkDestroy'), {
        data: { ids: selectedAssignments.value },
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            selectedAssignments.value = []
            toast.success('Выбранные задания успешно удалены.')
        },
        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            toast.error(errors[errorKey] || 'Ошибка массового удаления заданий.')
        },
    })
}

/** Обработчик действий */
const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        toggleAll({ target: { checked: true } })
    } else if (action === 'deselectAll') {
        toggleAll({ target: { checked: false } })
    } else if (action === 'activate') {
        bulkToggleActivity(true)
    } else if (action === 'deactivate') {
        bulkToggleActivity(false)
    } else if (action === 'left') {
        bulkToggleFlag('left', true, 'admin.actions.schoolAssignments.bulkUpdateLeft', 'Задания добавлены в левую колонку.')
    } else if (action === 'noLeft') {
        bulkToggleFlag('left', false, 'admin.actions.schoolAssignments.bulkUpdateLeft', 'Задания убраны из левой колонки.')
    } else if (action === 'main') {
        bulkToggleFlag('main', true, 'admin.actions.schoolAssignments.bulkUpdateMain', 'Задания добавлены в главный блок.')
    } else if (action === 'noMain') {
        bulkToggleFlag('main', false, 'admin.actions.schoolAssignments.bulkUpdateMain', 'Задания убраны из главного блока.')
    } else if (action === 'right') {
        bulkToggleFlag('right', true, 'admin.actions.schoolAssignments.bulkUpdateRight', 'Задания добавлены в правую колонку.')
    } else if (action === 'noRight') {
        bulkToggleFlag('right', false, 'admin.actions.schoolAssignments.bulkUpdateRight', 'Задания убраны из правой колонки.')
    } else if (action === 'delete') {
        bulkDelete()
    }

    event.target.value = ''
}

/* ==========================================================
 * ОПЕРАЦИИ НАД ОДНОЙ ЗАПИСЬЮ
 * ========================================================== */

/** Переключение активности */
const toggleActivity = (assignment) => {
    const newActivity = !assignment.activity
    const assignmentTitle = getAssignmentTitle(assignment)
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(route('admin.actions.schoolAssignments.updateActivity', {
        schoolAssignment: assignment.id,
    }), {
        activity: newActivity,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            patchAssignment(assignment.id, { activity: newActivity })
            assignment.activity = newActivity
            toast.success(`Задание "${assignmentTitle}" ${actionText}.`)
        },
        onError: (errors) => {
            toast.error(errors?.activity || errors?.general || `Ошибка изменения активности для задания "${assignmentTitle}".`)
        },
    })
}

/** Универсальный переключатель флагов */
const toggleFlag = (assignment, field, routeName, successMessage, errorMessage) => {
    const newValue = !assignment[field]

    router.put(route(routeName, {
        schoolAssignment: assignment.id,
    }), {
        [field]: newValue,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            patchAssignment(assignment.id, { [field]: newValue })
            assignment[field] = newValue
            toast.success(successMessage)
        },
        onError: (errors) => {
            toast.error(errors?.[field] || errors?.general || errorMessage)
        },
    })
}

/** Левая колонка */
const toggleLeft = (assignment) => {
    toggleFlag(
        assignment,
        'left',
        'admin.actions.schoolAssignments.updateLeft',
        'Левая колонка обновлена.',
        'Ошибка обновления левой колонки.'
    )
}

/** Главный блок */
const toggleMain = (assignment) => {
    toggleFlag(
        assignment,
        'main',
        'admin.actions.schoolAssignments.updateMain',
        'Главный блок обновлён.',
        'Ошибка обновления главного блока.'
    )
}

/** Правая колонка */
const toggleRight = (assignment) => {
    toggleFlag(
        assignment,
        'right',
        'admin.actions.schoolAssignments.updateRight',
        'Правая колонка обновлена.',
        'Ошибка обновления правой колонки.'
    )
}

/** Клонирование */
const cloneAssignment = (assignment) => {
    router.post(route('admin.actions.schoolAssignments.clone', {
        schoolAssignment: assignment.id,
    }), {}, {
        preserveScroll: true,
        onSuccess: () => toast.success('Задание успешно клонировано.'),
        onError: () => toast.error('Ошибка при клонировании задания.'),
    })
}
</script>

<template>
    <AdminLayout :title="t('assignments')">
        <template #header>
            <TitlePage>{{ t('assignments') }}</TitlePage>
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
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0"
                                 viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"></path>
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

                <SearchInput
                    v-if="assignmentsCount && !useServerProcessing"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <ServerSearchInput
                    v-if="assignmentsCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <div
                    v-if="assignmentsCount"
                    class="flex justify-between items-center flex-col md:flex-row my-3"
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
                        @update:sortParam="val => sortParam = val"
                    />
                </div>

                <div
                    v-if="assignmentsCount"
                    class="flex flex-col lg:flex-row items-center justify-between gap-3"
                >
                    <CountTable>{{ assignmentsCount }}</CountTable>

                    <BulkActionSelect @change="handleBulkAction" />

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div
                    v-if="assignmentsCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
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

                <div
                    v-if="assignmentsCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
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
