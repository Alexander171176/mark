<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список заданий школы
 */
import { computed, defineProps, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import SearchInput from '@/Components/Admin/UI/Search/SearchInput.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import ToggleViewButton from '@/Components/Admin/UI/Buttons/ToggleViewButton.vue'
import CountTable from '@/Components/Admin/UI/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/UI/Select/ItemsPerPageSelect.vue'
import DangerModal from '@/Components/Admin/UI/Modal/DangerModal.vue'
import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue'

import BulkActionSelect from '@/Components/Admin/School/SchoolAssignment/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/School/SchoolAssignment/Sort/SortSelect.vue'
import AssignmentTable from '@/Components/Admin/School/SchoolAssignment/Table/AssignmentTable.vue'
import AssignmentCardGrid from '@/Components/Admin/School/SchoolAssignment/View/AssignmentCardGrid.vue'

// Локализация и Toast уведомления
const { t } = useI18n()
const toast = useToast()

// Props из контроллера
const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },

    assignments: { type: Array, default: () => [] },
    assignmentsCount: { type: Number, default: 0 },

    adminSchoolAssignmentsPerPage: { type: Number, default: 10 },
    adminSchoolAssignmentsDefaultSort: { type: String, default: 'idDesc' },
})

// Режим отображения
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')

// Сохраняем режим отображения
watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode', val)
})

// Локальная копия заданий
const localAssignments = ref([])

// Обновление локального списка заданий
watch(
    () => props.assignments,
    (newVal) => {
        localAssignments.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

// Количество элементов на странице
const itemsPerPage = ref(props.adminSchoolAssignmentsPerPage || 10)

// Сохранение количества элементов
watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountAssignments'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

// Параметр сортировки
const sortParam = ref(props.adminSchoolAssignmentsDefaultSort || 'idDesc')

// Сохранение параметра сортировки
watch(sortParam, (newVal) => {
    currentPage.value = 1

    router.put(route('admin.settings.updateAdminSortAssignments'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info('Сортировка успешно изменена'),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления сортировки.'),
    })
})

// Модальное окно удаления
const showConfirmDeleteModal = ref(false)
const assignmentToDeleteId = ref(null)
const assignmentToDeleteTitle = ref('')

// Открытие модального окна удаления
const confirmDelete = (assignmentOrId, title = null) => {
    if (typeof assignmentOrId === 'object') {
        assignmentToDeleteId.value = assignmentOrId.id
        assignmentToDeleteTitle.value = title || assignmentOrId.title || `ID: ${assignmentOrId.id}`
    } else {
        assignmentToDeleteId.value = assignmentOrId
        assignmentToDeleteTitle.value = title || `ID: ${assignmentOrId}`
    }

    showConfirmDeleteModal.value = true
}

// Закрытие модального окна
const closeModal = () => {
    showConfirmDeleteModal.value = false
    assignmentToDeleteId.value = null
    assignmentToDeleteTitle.value = ''
}

// Удаление задания
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

// Текущая страница
const currentPage = ref(1)

// Поисковый запрос
const searchQuery = ref('')

// Нормализация строки
const normalize = (value) => (value ?? '').toString().trim().toLowerCase()

// Сортировка заданий
const safeNumber = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

const safeDate = (value) => {
    const time = new Date(value || 0).getTime()
    return Number.isFinite(time) ? time : 0
}

const byNumberAsc = (field) => (a, b) =>
    safeNumber(a?.[field]) - safeNumber(b?.[field])
    || safeNumber(a?.id) - safeNumber(b?.id)

const byNumberDesc = (field) => (a, b) =>
    safeNumber(b?.[field]) - safeNumber(a?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

const byStringAsc = (field) => (a, b) =>
    normalize(a?.[field]).localeCompare(normalize(b?.[field]), props.currentLocale)
    || safeNumber(a?.id) - safeNumber(b?.id)

const byStringDesc = (field) => (a, b) =>
    normalize(b?.[field]).localeCompare(normalize(a?.[field]), props.currentLocale)
    || safeNumber(b?.id) - safeNumber(a?.id)

const getNestedTitle = (item, field) => {
    const entity = item?.[field]

    return entity?.title
        || entity?.name
        || entity?.public_name
        || entity?.user?.name
        || ''
}

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

        titleAsc: byStringAsc('title'),
        titleDesc: byStringDesc('title'),

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

// Отфильтрованные задания
const filteredAssignments = computed(() => {
    let filtered = localAssignments.value || []
    const q = normalize(searchQuery.value)

    if (!q) {
        return sortAssignments(filtered)
    }

    filtered = filtered.filter((assignment) => {
        const title = normalize(assignment?.title)
        const subtitle = normalize(assignment?.subtitle)
        const slug = normalize(assignment?.slug)
        const short = normalize(assignment?.short)
        const description = normalize(assignment?.description)
        const instructions = normalize(assignment?.instructions)

        const courseTitle = normalize(assignment?.course?.title)
        const moduleTitle = normalize(assignment?.module?.title)
        const lessonTitle = normalize(assignment?.lesson?.title)
        const instructorTitle = normalize(assignment?.instructor?.title)
        const instructorName = normalize(assignment?.instructor?.user?.name)

        return (
            title.includes(q) ||
            subtitle.includes(q) ||
            slug.includes(q) ||
            short.includes(q) ||
            description.includes(q) ||
            instructions.includes(q) ||
            courseTitle.includes(q) ||
            moduleTitle.includes(q) ||
            lessonTitle.includes(q) ||
            instructorTitle.includes(q) ||
            instructorName.includes(q)
        )
    })

    return sortAssignments(filtered)
})

// Задания текущей страницы
const paginatedAssignments = computed(() => {
    const per = Number(itemsPerPage.value || 20)
    const start = (currentPage.value - 1) * per

    return filteredAssignments.value.slice(start, start + per)
})

// Сброс страницы при поиске и изменении лимита
watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

// Локальное обновление задания
const patchAssignment = (assignmentId, payload) => {
    const index = localAssignments.value.findIndex(assignment => assignment.id === assignmentId)

    if (index !== -1) {
        localAssignments.value[index] = {
            ...localAssignments.value[index],
            ...payload,
        }
    }
}

// Выбранные задания
const selectedAssignments = ref([])

// Выбрать / снять выбор со всех заданий
const toggleAll = ({ ids, checked }) => {
    selectedAssignments.value = checked ? [...ids] : []
}

// Выбор одного задания
const toggleSelectAssignment = (id) => {
    const index = selectedAssignments.value.indexOf(id)

    if (index > -1) {
        selectedAssignments.value.splice(index, 1)
    } else {
        selectedAssignments.value.push(id)
    }
}

// Обновление порядка сортировки
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

// Массовое обновление активности
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

// Массовое удаление
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

// Обработка массовых действий
const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        selectedAssignments.value = paginatedAssignments.value.map(assignment => assignment.id)
    } else if (action === 'deselectAll') {
        selectedAssignments.value = []
    } else if (action === 'activate') {
        bulkToggleActivity(true)
    } else if (action === 'deactivate') {
        bulkToggleActivity(false)
    } else if (action === 'delete') {
        bulkDelete()
    }

    event.target.value = ''
}

// Переключение активности задания
const toggleActivity = (assignment) => {
    const newActivity = !assignment.activity
    const assignmentTitle = assignment.title || `ID: ${assignment.id}`
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

// Переключение левой колонки
const toggleLeft = (assignment) => {
    const newLeft = !assignment.left

    router.put(route('admin.actions.schoolAssignments.updateLeft', {
        schoolAssignment: assignment.id,
    }), {
        left: newLeft,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            patchAssignment(assignment.id, { left: newLeft })
            assignment.left = newLeft
            toast.success('Левая колонка обновлена.')
        },
        onError: (errors) => {
            toast.error(errors?.left || errors?.general || 'Ошибка обновления левой колонки.')
        },
    })
}

// Переключение главного блока
const toggleMain = (assignment) => {
    const newMain = !assignment.main

    router.put(route('admin.actions.schoolAssignments.updateMain', {
        schoolAssignment: assignment.id,
    }), {
        main: newMain,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            patchAssignment(assignment.id, { main: newMain })
            assignment.main = newMain
            toast.success('Главный блок обновлён.')
        },
        onError: (errors) => {
            toast.error(errors?.main || errors?.general || 'Ошибка обновления главного блока.')
        },
    })
}

// Переключение правой колонки
const toggleRight = (assignment) => {
    const newRight = !assignment.right

    router.put(route('admin.actions.schoolAssignments.updateRight', {
        schoolAssignment: assignment.id,
    }), {
        right: newRight,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            patchAssignment(assignment.id, { right: newRight })
            assignment.right = newRight
            toast.success('Правая колонка обновлена.')
        },
        onError: (errors) => {
            toast.error(errors?.right || errors?.general || 'Ошибка обновления правой колонки.')
        },
    })
}

// Клонирование задания
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
                <div class="sm:flex sm:justify-between sm:items-center mb-3">
                    <DefaultButton :href="route('admin.schoolAssignments.create')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0"
                                 viewBox="0 0 16 16">
                                <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"></path>
                            </svg>
                        </template>
                        {{ t('addAssignment') }}
                    </DefaultButton>
                </div>

                <SearchInput
                    v-if="assignmentsCount"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <div
                    v-if="assignmentsCount"
                    class="flex justify-between items-center flex-col md:flex-row my-3"
                >
                    <ItemsPerPageSelect
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
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
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredAssignments.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                </div>

                <AssignmentTable
                    v-if="viewMode === 'table'"
                    :assignments="paginatedAssignments"
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
                    :assignments="paginatedAssignments"
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
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredAssignments.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            @close="closeModal"
            :onCancel="closeModal"
            :onConfirm="deleteAssignment"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
