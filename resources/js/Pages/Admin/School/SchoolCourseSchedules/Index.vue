<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список расписаний курсов школы
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

import BulkActionSelect from '@/Components/Admin/School/SchoolCourseSchedule/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/School/SchoolCourseSchedule/Sort/SortSelect.vue'
import CourseScheduleTable from '@/Components/Admin/School/SchoolCourseSchedule/Table/CourseScheduleTable.vue'
import CourseScheduleCardGrid from '@/Components/Admin/School/SchoolCourseSchedule/View/CourseScheduleCardGrid.vue'

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

    adminSchoolCourseSchedulesProcessingMode: { type: String, default: 'frontend' },
    useServerProcessing: { type: Boolean, default: false },

    schedules: { type: [Array, Object], default: () => [] },
    schedulesCount: { type: Number, default: 0 },

    adminSchoolCourseSchedulesPerPage: { type: Number, default: 6 },
    adminSchoolCourseSchedulesDefaultSort: { type: String, default: 'idDesc' },

    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },

    errors: { type: Object, default: () => ({}) },
})

/* ==========================================================
 * РЕЖИМ ОТОБРАЖЕНИЯ
 * ========================================================== */

/** Текущий режим отображения: таблица / карточки */
const viewMode = ref(localStorage.getItem('admin_view_mode_course_schedules') || 'table')

/** Сохраняем выбранный вид локально */
watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode_course_schedules', val)
})

/* ==========================================================
 * ИСТОЧНИК ДАННЫХ
 * ========================================================== */

/**
 * Унифицированный список расписаний:
 * frontend → обычный массив
 * server → schedules.data
 */
const schedulesList = computed(() => {
    if (Array.isArray(props.schedules)) {
        return props.schedules
    }

    if (Array.isArray(props.schedules?.data)) {
        return props.schedules.data
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
const localSchedules = ref([])

watch(
    schedulesList,
    (newVal) => {
        localSchedules.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/* ==========================================================
 * НАСТРОЙКИ ПАГИНАЦИИ И СОРТИРОВКИ
 * ========================================================== */

/** Количество элементов на странице */
const itemsPerPage = ref(props.adminSchoolCourseSchedulesPerPage || 6)

/** Сохраняем настройку количества элементов */
watch(itemsPerPage, (newVal) => {
    router.put(
        route('admin.settings.updateAdminCountCourseSchedules'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: () => {
                toast.info(`Показ ${newVal} элементов на странице.`)
            },

            onError: (errors) => {
                toast.error(errors.value || 'Ошибка обновления кол-ва элементов.')
            },
        }
    )
})

/** Текущий параметр сортировки */
const sortParam = ref(
    props.sortParam ||
    props.adminSchoolCourseSchedulesDefaultSort ||
    'idDesc'
)

/** Сохраняем сортировку и при server-режиме перезагружаем список */
watch(sortParam, (newVal) => {
    currentPage.value = 1

    router.put(
        route('admin.settings.updateAdminSortCourseSchedules'),
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

/** Текущая страница frontend-пагинации */
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

/** Получение заголовка расписания */
const getScheduleTitle = (schedule) => {
    return schedule?.title
        || schedule?.translation?.title
        || schedule?.translations?.[0]?.title
        || `ID: ${schedule?.id}`
}

/** Получение подзаголовка расписания */
const getScheduleSubtitle = (schedule) => {
    return schedule?.subtitle
        || schedule?.translation?.subtitle
        || schedule?.translations?.[0]?.subtitle
        || ''
}

/** Получение краткого описания расписания */
const getScheduleShort = (schedule) => {
    return schedule?.short
        || schedule?.translation?.short
        || schedule?.translations?.[0]?.short
        || ''
}

/** Получение описания расписания */
const getScheduleDescription = (schedule) => {
    return schedule?.description
        || schedule?.translation?.description
        || schedule?.translations?.[0]?.description
        || ''
}

/** Получение slug расписания */
const getScheduleSlug = (schedule) => {
    return schedule?.slug
        || schedule?.translation?.slug
        || schedule?.translations?.[0]?.slug
        || ''
}

/** Получение заголовка связанной сущности */
const getNestedTitle = (item) => {
    return item?.title
        || item?.name
        || item?.translation?.title
        || item?.translation?.name
        || item?.translations?.[0]?.title
        || item?.translations?.[0]?.name
        || item?.user?.name
        || ''
}

/** Получение названия курса */
const getCourseTitle = (schedule) => {
    return getNestedTitle(schedule?.course)
}

/** Получение имени инструктора */
const getInstructorTitle = (schedule) => {
    return getNestedTitle(schedule?.instructor)
}

/** Получение имени пользователя инструктора */
const getInstructorUserText = (schedule) => {
    const user = schedule?.instructor?.user

    return [
        user?.name,
        user?.email,
    ].filter(Boolean).join(' ')
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

/** Сортировка дат ↑ */
const byDateAsc = (field) => (a, b) =>
    safeDate(a?.[field]) - safeDate(b?.[field])
    || safeNumber(a?.id) - safeNumber(b?.id)

/** Сортировка дат ↓ */
const byDateDesc = (field) => (a, b) =>
    safeDate(b?.[field]) - safeDate(a?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

/**
 * Главный обработчик сортировки.
 * Должен совпадать со scopeSortByParam() модели и SortSelect.vue.
 */
const sortSchedules = (items) => {
    const list = (items || []).slice()

    if (sortParam.value === 'activity') return list.filter(item => !!item.activity)
    if (sortParam.value === 'inactive') return list.filter(item => !item.activity)

    if (sortParam.value === 'online') return list.filter(item => !!item.is_online)
    if (sortParam.value === 'offline') return list.filter(item => !item.is_online)

    const sortMap = {
        idAsc: byNumberAsc('id'),
        idDesc: byNumberDesc('id'),

        sortAsc: byNumberAsc('sort'),
        sortDesc: byNumberDesc('sort'),

        titleAsc: (a, b) =>
            normalize(getScheduleTitle(a)).localeCompare(normalize(getScheduleTitle(b)), props.currentLocale)
            || safeNumber(a?.id) - safeNumber(b?.id),

        titleDesc: (a, b) =>
            normalize(getScheduleTitle(b)).localeCompare(normalize(getScheduleTitle(a)), props.currentLocale)
            || safeNumber(b?.id) - safeNumber(a?.id),

        slugAsc: byStringAsc('slug'),
        slugDesc: byStringDesc('slug'),

        statusAsc: byStringAsc('status'),
        statusDesc: byStringDesc('status'),

        timezoneAsc: byStringAsc('timezone'),
        timezoneDesc: byStringDesc('timezone'),

        locationAsc: byStringAsc('location'),
        locationDesc: byStringDesc('location'),

        meetingUrlAsc: byStringAsc('meeting_url'),
        meetingUrlDesc: byStringDesc('meeting_url'),

        capacityAsc: byNumberAsc('capacity'),
        capacityDesc: byNumberDesc('capacity'),

        viewsAsc: byNumberAsc('views'),
        viewsDesc: byNumberDesc('views'),

        imagesAsc: byNumberAsc('images_count'),
        imagesDesc: byNumberDesc('images_count'),

        cohortEnrollmentsAsc: byNumberAsc('cohort_enrollments_count'),
        cohortEnrollmentsDesc: byNumberDesc('cohort_enrollments_count'),

        activityAsc: byNumberAsc('activity'),
        activityDesc: byNumberDesc('activity'),

        onlineAsc: byNumberAsc('is_online'),
        onlineDesc: byNumberDesc('is_online'),

        startsAtAsc: byDateAsc('starts_at'),
        startsAtDesc: byDateDesc('starts_at'),

        endsAtAsc: byDateAsc('ends_at'),
        endsAtDesc: byDateDesc('ends_at'),

        enrollStartsAtAsc: byDateAsc('enroll_starts_at'),
        enrollStartsAtDesc: byDateDesc('enroll_starts_at'),

        enrollEndsAtAsc: byDateAsc('enroll_ends_at'),
        enrollEndsAtDesc: byDateDesc('enroll_ends_at'),

        createdAtAsc: byDateAsc('created_at'),
        createdAtDesc: byDateDesc('created_at'),

        updatedAtAsc: byDateAsc('updated_at'),
        updatedAtDesc: byDateDesc('updated_at'),
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
const filteredSchedules = computed(() => {
    let filtered = localSchedules.value || []
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortSchedules(filtered)
    }

    filtered = filtered.filter((schedule) => {
        const title = normalize(getScheduleTitle(schedule))
        const subtitle = normalize(getScheduleSubtitle(schedule))
        const slug = normalize(getScheduleSlug(schedule))
        const short = normalize(getScheduleShort(schedule))
        const description = normalize(getScheduleDescription(schedule))
        const notes = normalize(schedule?.notes)
        const location = normalize(schedule?.location)
        const meetingUrl = normalize(schedule?.meeting_url)
        const timezone = normalize(schedule?.timezone)
        const status = normalize(schedule?.status)
        const courseTitle = normalize(getCourseTitle(schedule))
        const instructorTitle = normalize(getInstructorTitle(schedule))
        const instructorUser = normalize(getInstructorUserText(schedule))

        return title.includes(query)
            || subtitle.includes(query)
            || slug.includes(query)
            || short.includes(query)
            || description.includes(query)
            || notes.includes(query)
            || location.includes(query)
            || meetingUrl.includes(query)
            || timezone.includes(query)
            || status.includes(query)
            || courseTitle.includes(query)
            || instructorTitle.includes(query)
            || instructorUser.includes(query)
    })

    return sortSchedules(filtered)
})

/* ==========================================================
 * ЛОКАЛЬНАЯ ПАГИНАЦИЯ
 * ========================================================== */

/** Разбиение списка по страницам */
const paginatedSchedules = computed(() => {
    const per = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * per

    return filteredSchedules.value.slice(start, start + per)
})

/**
 * Итоговый список:
 * frontend → локальная пагинация
 * server → данные сервера
 */
const displayedSchedules = computed(() => {
    return props.useServerProcessing
        ? schedulesList.value
        : paginatedSchedules.value
})

watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

/* ==========================================================
 * УДАЛЕНИЕ
 * ========================================================== */

/** Состояние модального окна удаления */
const showConfirmDeleteModal = ref(false)
const scheduleToDeleteId = ref(null)
const scheduleToDeleteTitle = ref('')

/** Открыть подтверждение удаления */
const confirmDelete = (scheduleOrId, title = null) => {
    if (typeof scheduleOrId === 'object') {
        scheduleToDeleteId.value = scheduleOrId.id
        scheduleToDeleteTitle.value = title || getScheduleTitle(scheduleOrId)
    } else {
        scheduleToDeleteId.value = scheduleOrId
        scheduleToDeleteTitle.value = title || `ID: ${scheduleOrId}`
    }

    showConfirmDeleteModal.value = true
}

/** Закрыть окно */
const closeModal = () => {
    showConfirmDeleteModal.value = false
    scheduleToDeleteId.value = null
    scheduleToDeleteTitle.value = ''
}

/** Выполнить удаление */
const deleteSchedule = () => {
    if (scheduleToDeleteId.value === null) return

    const idToDelete = scheduleToDeleteId.value
    const titleToDelete = scheduleToDeleteTitle.value

    router.delete(route('admin.schoolCourseSchedules.destroy', {
        schoolCourseSchedule: idToDelete,
    }), {
        preserveScroll: true,
        preserveState: false,

        onSuccess: () => {
            toast.success(`Расписание "${titleToDelete || 'ID: ' + idToDelete}" удалено.`)
        },

        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            const errorMsg = errors.general || errors[errorKey] || 'Произошла ошибка при удалении.'

            toast.error(`${errorMsg} (Расписание: ${titleToDelete || 'ID: ' + idToDelete})`)
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
const patchSchedule = (scheduleId, payload) => {
    const index = localSchedules.value.findIndex(schedule => schedule.id === scheduleId)

    if (index !== -1) {
        localSchedules.value[index] = {
            ...localSchedules.value[index],
            ...payload,
        }
    }
}

/* ==========================================================
 * МАССОВЫЕ ОПЕРАЦИИ
 * ========================================================== */

/** Выбранные элементы */
const selectedSchedules = ref([])

/** Выбрать все */
const toggleAll = (payload) => {
    const checked = payload?.checked ?? payload?.target?.checked ?? false
    const ids = payload?.ids ?? displayedSchedules.value.map((schedule) => schedule.id)

    if (checked) {
        selectedSchedules.value = [...new Set([...selectedSchedules.value, ...ids])]
    } else {
        selectedSchedules.value = selectedSchedules.value.filter((id) => !ids.includes(id))
    }
}

/** Выбрать элемент */
const toggleSelectSchedule = (id) => {
    const index = selectedSchedules.value.indexOf(id)

    if (index > -1) {
        selectedSchedules.value.splice(index, 1)
    } else {
        selectedSchedules.value.push(id)
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

    router.put(route('admin.actions.schoolCourseSchedules.updateSortBulk'), { items }, {
        preserveScroll: true,
        preserveState: true,

        onSuccess: () => {
            toast.success('Порядок расписаний успешно обновлён.')
        },

        onError: (errors) => {
            console.error('Ошибка обновления сортировки расписаний:', errors)

            toast.error(
                errors?.message ||
                errors?.general ||
                'Не удалось обновить порядок расписаний.'
            )

            router.reload({
                only: ['schedules'],
                preserveScroll: true,
            })
        },
    })
}

/** Массовая активность */
const bulkToggleActivity = (newActivity) => {
    if (!selectedSchedules.value.length) {
        toast.warning('Выберите расписания для активации/деактивации.')
        return
    }

    const idsToUpdate = [...selectedSchedules.value]

    router.put(route('admin.actions.schoolCourseSchedules.bulkUpdateActivity'), {
        ids: idsToUpdate,
        activity: newActivity,
    }, {
        preserveScroll: true,
        preserveState: true,

        onSuccess: () => {
            idsToUpdate.forEach(id => patchSchedule(id, { activity: newActivity }))
            selectedSchedules.value = []
            toast.success('Активность выбранных расписаний обновлена.')
        },

        onError: (errors) => {
            toast.error(
                errors?.ids ||
                errors?.activity ||
                errors?.general ||
                'Ошибка массового обновления активности.'
            )
        },
    })
}

/** Обработчик массовых действий */
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
    }

    event.target.value = ''
}

/* ==========================================================
 * ОПЕРАЦИИ НАД ОДНОЙ ЗАПИСЬЮ
 * ========================================================== */

/** Переключение активности */
const toggleActivity = (schedule) => {
    const newActivity = !schedule.activity
    const scheduleTitle = getScheduleTitle(schedule)
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(route('admin.actions.schoolCourseSchedules.updateActivity', {
        schoolCourseSchedule: schedule.id,
    }), {
        activity: newActivity,
    }, {
        preserveScroll: true,
        preserveState: true,

        onSuccess: () => {
            patchSchedule(schedule.id, { activity: newActivity })
            schedule.activity = newActivity

            toast.success(`Расписание "${scheduleTitle}" ${actionText}.`)
        },

        onError: (errors) => {
            toast.error(
                errors?.activity ||
                errors?.general ||
                `Ошибка изменения активности для расписания "${scheduleTitle}".`
            )
        },
    })
}

/** Клонирование расписания */
const cloneSchedule = (schedule) => {
    router.post(route('admin.actions.schoolCourseSchedules.clone', {
        schoolCourseSchedule: schedule.id,
    }), {}, {
        preserveScroll: true,

        onSuccess: () => {
            toast.success('Расписание успешно клонировано.')
        },

        onError: () => {
            toast.error('Ошибка при клонировании расписания.')
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('schedules')">
        <template #header>
            <TitlePage>{{ t('schedules') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3">
                    <DefaultButton :href="route('admin.schoolCourseSchedules.create')">
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

                <SearchInput
                    v-if="schedulesCount && !useServerProcessing"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <ServerSearchInput
                    v-if="schedulesCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <div
                    v-if="schedulesCount"
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
                        update-route="admin.settings.updateAdminCountCourseSchedules"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="val => sortParam = val"
                    />
                </div>

                <div
                    v-if="schedulesCount"
                    class="flex flex-col lg:flex-row items-center justify-between gap-3"
                >
                    <CountTable>{{ schedulesCount }}</CountTable>

                    <BulkActionSelect @change="handleBulkAction" />

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div
                    v-if="schedulesCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
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

                <div
                    v-if="schedulesCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
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
