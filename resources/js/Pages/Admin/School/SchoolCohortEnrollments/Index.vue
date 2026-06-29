<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Записи пользователей на потоки
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
import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue'
import AdminServerPagination from '@/Components/Admin/UI/Pagination/AdminServerPagination.vue'
import SearchInput from '@/Components/Admin/UI/Search/SearchInput.vue'
import ServerSearchInput from '@/Components/Admin/UI/Search/ServerSearchInput.vue'
import CountTable from '@/Components/Admin/UI/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/UI/Select/ItemsPerPageSelect.vue'
import ServerItemsPerPageSelect from '@/Components/Admin/UI/Select/ServerItemsPerPageSelect.vue'
import ToggleViewButton from '@/Components/Admin/UI/Buttons/ToggleViewButton.vue'
import ProcessingModeSwitcher from '@/Components/Admin/UI/Processing/ProcessingModeSwitcher.vue'

import CohortEnrollmentTable from '@/Components/Admin/School/SchoolCohortEnrollment/Table/CohortEnrollmentTable.vue'
import CohortEnrollmentCardGrid
    from '@/Components/Admin/School/SchoolCohortEnrollment/View/CohortEnrollmentCardGrid.vue'
import SortSelect from '@/Components/Admin/School/SchoolCohortEnrollment/Sort/SortSelect.vue'

/* ==========================================================
 * БАЗОВЫЕ СЕРВИСЫ И PROPS
 * ========================================================== */

/** Локализация интерфейса */
const { t } = useI18n()

/** Уведомления */
const toast = useToast()

/** Данные страницы из Inertia */
const props = defineProps({
    adminSchoolCohortEnrollmentsProcessingMode: { type: String, default: 'frontend' },
    useServerProcessing: { type: Boolean, default: false },

    enrollments: { type: [Array, Object], default: () => [] },
    enrollmentsCount: { type: Number, default: 0 },

    adminSchoolCohortEnrollmentsPerPage: { type: Number, default: 10 },
    adminSchoolCohortEnrollmentsDefaultSort: { type: String, default: 'idDesc' },

    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },

    filters: { type: Object, default: () => ({}) },
    schedules: { type: [Array, Object], default: () => [] },
    users: { type: [Array, Object], default: () => [] },

    errors: { type: Object, default: () => ({}) },
})

/* ==========================================================
 * РЕЖИМ ОТОБРАЖЕНИЯ
 * ========================================================== */

/** Текущий режим отображения */
const viewMode = ref(localStorage.getItem('admin_view_mode_cohort_enrollments') || 'table')

/** Сохраняем выбранный вид локально */
watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode_cohort_enrollments', val)
})

/* ==========================================================
 * ИСТОЧНИК ДАННЫХ
 * ========================================================== */

/** Унифицированный список записей */
const enrollmentsList = computed(() => {
    if (Array.isArray(props.enrollments)) {
        return props.enrollments
    }

    if (Array.isArray(props.enrollments?.data)) {
        return props.enrollments.data
    }

    return []
})

/* ==========================================================
 * ЛОКАЛЬНОЕ ХРАНИЛИЩЕ ДАННЫХ
 * ========================================================== */

/** Локальная копия списка */
const localEnrollments = ref([])

watch(
    enrollmentsList,
    (newVal) => {
        localEnrollments.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/* ==========================================================
 * НАСТРОЙКИ ПАГИНАЦИИ И СОРТИРОВКИ
 * ========================================================== */

/** Количество элементов на странице */
const itemsPerPage = ref(props.adminSchoolCohortEnrollmentsPerPage || 10)

/** Сохраняем количество элементов */
watch(itemsPerPage, (newVal) => {
    router.put(
        route('admin.settings.updateAdminCountSchoolCohortEnrollments'),
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
const sortParam = ref(props.sortParam || props.adminSchoolCohortEnrollmentsDefaultSort || 'idDesc')

/** Сохраняем сортировку и при server-режиме перезагружаем список */
watch(sortParam, (newVal) => {
    currentPage.value = 1

    router.put(
        route('admin.settings.updateAdminSortSchoolCohortEnrollments'),
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

const normalize = (value) => (value ?? '').toString().trim().toLowerCase()

const safeNumber = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

const safeDate = (value) => {
    const time = new Date(value || 0).getTime()
    return Number.isFinite(time) ? time : 0
}

/* ==========================================================
 * ИЗВЛЕЧЕНИЕ ДАННЫХ ИЗ РЕСУРСОВ
 * ========================================================== */

const getUserName = (enrollment) => enrollment?.user?.name || ''

const getUserEmail = (enrollment) => enrollment?.user?.email || ''

const getScheduleTitle = (enrollment) => {
    return enrollment?.schedule?.title
        || enrollment?.schedule?.translation?.title
        || enrollment?.schedule?.translations?.[0]?.title
        || ''
}

const getScheduleDescription = (enrollment) => {
    return enrollment?.schedule?.description
        || enrollment?.schedule?.translation?.description
        || enrollment?.schedule?.translations?.[0]?.description
        || ''
}

const getCourseTitle = (enrollment) => {
    const course = enrollment?.schedule?.course

    return course?.title
        || course?.translation?.title
        || course?.translations?.[0]?.title
        || ''
}

const getCourseShort = (enrollment) => {
    const course = enrollment?.schedule?.course

    return course?.short
        || course?.translation?.short
        || course?.translations?.[0]?.short
        || ''
}

const getCourseDescription = (enrollment) => {
    const course = enrollment?.schedule?.course

    return course?.description
        || course?.translation?.description
        || course?.translations?.[0]?.description
        || ''
}

/* ==========================================================
 * СОРТИРОВКА FRONTEND
 * ========================================================== */

const byNumberAsc = (field) => (a, b) =>
    safeNumber(a?.[field]) - safeNumber(b?.[field])
    || safeNumber(a?.id) - safeNumber(b?.id)

const byNumberDesc = (field) => (a, b) =>
    safeNumber(b?.[field]) - safeNumber(a?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

const byStringGetterAsc = (getter) => (a, b) =>
    normalize(getter(a)).localeCompare(normalize(getter(b)))
    || safeNumber(a?.id) - safeNumber(b?.id)

const byStringGetterDesc = (getter) => (a, b) =>
    normalize(getter(b)).localeCompare(normalize(getter(a)))
    || safeNumber(b?.id) - safeNumber(a?.id)

const byDateAsc = (field) => (a, b) =>
    safeDate(a?.[field]) - safeDate(b?.[field])
    || safeNumber(a?.id) - safeNumber(b?.id)

const byDateDesc = (field) => (a, b) =>
    safeDate(b?.[field]) - safeDate(a?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

/** Сортировка записей */
const sortEnrollments = (items) => {
    const list = (items || []).slice()

    const sortMap = {
        idAsc: byNumberAsc('id'),
        idDesc: byNumberDesc('id'),

        enrolledAtAsc: byDateAsc('enrolled_at'),
        enrolledAtDesc: byDateDesc('enrolled_at'),

        statusAsc: (a, b) =>
            normalize(a?.status).localeCompare(normalize(b?.status))
            || safeNumber(a?.id) - safeNumber(b?.id),

        statusDesc: (a, b) =>
            normalize(b?.status).localeCompare(normalize(a?.status))
            || safeNumber(b?.id) - safeNumber(a?.id),

        userNameAsc: byStringGetterAsc(getUserName),
        userNameDesc: byStringGetterDesc(getUserName),

        userEmailAsc: byStringGetterAsc(getUserEmail),
        userEmailDesc: byStringGetterDesc(getUserEmail),

        scheduleTitleAsc: byStringGetterAsc(getScheduleTitle),
        scheduleTitleDesc: byStringGetterDesc(getScheduleTitle),

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

/** Фильтрация записей */
const filteredEnrollments = computed(() => {
    let filtered = localEnrollments.value || []
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortEnrollments(filtered)
    }

    filtered = filtered.filter((enrollment) => {
        const status = normalize(enrollment?.status)
        const notes = normalize(enrollment?.notes)
        const userName = normalize(getUserName(enrollment))
        const userEmail = normalize(getUserEmail(enrollment))
        const scheduleTitle = normalize(getScheduleTitle(enrollment))
        const scheduleDescription = normalize(getScheduleDescription(enrollment))
        const courseTitle = normalize(getCourseTitle(enrollment))
        const courseShort = normalize(getCourseShort(enrollment))
        const courseDescription = normalize(getCourseDescription(enrollment))

        return status.includes(query)
            || notes.includes(query)
            || userName.includes(query)
            || userEmail.includes(query)
            || scheduleTitle.includes(query)
            || scheduleDescription.includes(query)
            || courseTitle.includes(query)
            || courseShort.includes(query)
            || courseDescription.includes(query)
    })

    return sortEnrollments(filtered)
})

/* ==========================================================
 * ЛОКАЛЬНАЯ ПАГИНАЦИЯ
 * ========================================================== */

const paginatedEnrollments = computed(() => {
    const per = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * per

    return filteredEnrollments.value.slice(start, start + per)
})

const displayedEnrollments = computed(() => {
    return props.useServerProcessing
        ? enrollmentsList.value
        : paginatedEnrollments.value
})

watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})
</script>

<template>
    <AdminLayout :title="t('cohortEnrollments')">
        <template #header>
            <TitlePage>
                {{ t('cohortEnrollments') }}
            </TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-end sm:items-center mb-3 gap-3">
                    <ProcessingModeSwitcher
                        setting-key="adminSchoolCohortEnrollmentsProcessingMode"
                        :mode="adminSchoolCohortEnrollmentsProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="enrollmentsCount"
                    />
                </div>

                <SearchInput
                    v-if="enrollmentsCount && !useServerProcessing"
                    v-model="searchQuery"
                    :placeholder="t('search')"
                />

                <ServerSearchInput
                    v-if="enrollmentsCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <div
                    v-if="enrollmentsCount"
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
                        update-route="admin.settings.updateAdminCountSchoolCohortEnrollments"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="val => sortParam = val"
                    />
                </div>

                <div
                    v-if="enrollmentsCount"
                    class="flex justify-between items-center flex-col md:flex-row my-3"
                >
                    <CountTable>
                        {{ enrollmentsCount }}
                    </CountTable>

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div
                    v-if="enrollmentsCount"
                    class="flex justify-center items-center flex-col md:flex-row mb-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredEnrollments.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="enrollments"
                    />
                </div>

                <CohortEnrollmentTable
                    v-if="viewMode === 'table'"
                    :enrollments="displayedEnrollments"
                />

                <CohortEnrollmentCardGrid
                    v-else
                    :enrollments="displayedEnrollments"
                />

                <div
                    v-if="enrollmentsCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredEnrollments.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="enrollments"
                    />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
