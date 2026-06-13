<script setup>
/**
 * Список зачислений студентов
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
import DangerModal from '@/Components/Admin/UI/Modal/DangerModal.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'

import EnrollmentTable from '@/Components/Admin/School/SchoolEnrollment/Table/EnrollmentTable.vue'
import EnrollmentCardGrid from '@/Components/Admin/School/SchoolEnrollment/View/EnrollmentCardGrid.vue'
import SortSelect from '@/Components/Admin/School/SchoolEnrollment/Sort/SortSelect.vue'

/* ==========================================================
 * БАЗОВЫЕ СЕРВИСЫ И PROPS
 * ========================================================== */

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    adminSchoolEnrollmentsProcessingMode: { type: String, default: 'frontend' },
    useServerProcessing: { type: Boolean, default: false },

    enrollments: { type: [Array, Object], default: () => [] },
    enrollmentsCount: { type: Number, default: 0 },

    adminSchoolEnrollmentsPerPage: { type: Number, default: 10 },
    adminSchoolEnrollmentsDefaultSort: { type: String, default: 'idDesc' },

    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },

    filters: { type: Object, default: () => ({}) },

    users: { type: [Array, Object], default: () => [] },
    courses: { type: [Array, Object], default: () => [] },
    schedules: { type: [Array, Object], default: () => [] },
    orders: { type: [Array, Object], default: () => [] },

    errors: { type: Object, default: () => ({}) },
})

/* ==========================================================
 * РЕЖИМ ОТОБРАЖЕНИЯ
 * ========================================================== */

const viewMode = ref(localStorage.getItem('admin_view_mode_enrollments') || 'table')

watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode_enrollments', val)
})

/* ==========================================================
 * ИСТОЧНИК ДАННЫХ
 * ========================================================== */

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

const itemsPerPage = ref(props.adminSchoolEnrollmentsPerPage || 10)

watch(itemsPerPage, (newVal) => {
    router.put(
        route('admin.settings.updateAdminCountEnrollments'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
            onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
        }
    )
})

const sortParam = ref(props.sortParam || props.adminSchoolEnrollmentsDefaultSort || 'idDesc')

watch(sortParam, (newVal) => {
    currentPage.value = 1

    router.put(
        route('admin.settings.updateAdminSortEnrollments'),
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

const searchQuery = ref(props.search || '')
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

const getCourseTitle = (enrollment) => {
    const course = enrollment?.course

    return course?.title
        || course?.translation?.title
        || course?.translations?.[0]?.title
        || ''
}

const getCourseSlug = (enrollment) => {
    const course = enrollment?.course

    return course?.slug
        || course?.translation?.slug
        || course?.translations?.[0]?.slug
        || ''
}

const getCourseShort = (enrollment) => {
    const course = enrollment?.course

    return course?.short
        || course?.translation?.short
        || course?.translations?.[0]?.short
        || ''
}

const getCourseDescription = (enrollment) => {
    const course = enrollment?.course

    return course?.description
        || course?.translation?.description
        || course?.translations?.[0]?.description
        || ''
}

const getScheduleTitle = (enrollment) => {
    const schedule = enrollment?.schedule

    return schedule?.title
        || schedule?.translation?.title
        || schedule?.translations?.[0]?.title
        || ''
}

const getScheduleSlug = (enrollment) => {
    const schedule = enrollment?.schedule

    return schedule?.slug
        || schedule?.translation?.slug
        || schedule?.translations?.[0]?.slug
        || ''
}

const getOrderNumber = (enrollment) => {
    return enrollment?.order?.number
        || enrollment?.order?.id
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

const byDateAsc = (field) => (a, b) =>
    safeDate(a?.[field]) - safeDate(b?.[field])
    || safeNumber(a?.id) - safeNumber(b?.id)

const byDateDesc = (field) => (a, b) =>
    safeDate(b?.[field]) - safeDate(a?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

const byStringGetterAsc = (getter) => (a, b) =>
    normalize(getter(a)).localeCompare(normalize(getter(b)))
    || safeNumber(a?.id) - safeNumber(b?.id)

const byStringGetterDesc = (getter) => (a, b) =>
    normalize(getter(b)).localeCompare(normalize(getter(a)))
    || safeNumber(b?.id) - safeNumber(a?.id)

const sortEnrollments = (items) => {
    const list = (items || []).slice()

    const sortMap = {
        idAsc: byNumberAsc('id'),
        idDesc: byNumberDesc('id'),

        startedAtAsc: byDateAsc('started_at'),
        startedAtDesc: byDateDesc('started_at'),

        expiresAtAsc: byDateAsc('expires_at'),
        expiresAtDesc: byDateDesc('expires_at'),

        completedAtAsc: byDateAsc('completed_at'),
        completedAtDesc: byDateDesc('completed_at'),

        progressAsc: byNumberAsc('progress_percent'),
        progressDesc: byNumberDesc('progress_percent'),

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

        courseTitleAsc: byStringGetterAsc(getCourseTitle),
        courseTitleDesc: byStringGetterDesc(getCourseTitle),

        scheduleTitleAsc: byStringGetterAsc(getScheduleTitle),
        scheduleTitleDesc: byStringGetterDesc(getScheduleTitle),

        progressRecordsAsc: byNumberAsc('progress_records_count'),
        progressRecordsDesc: byNumberDesc('progress_records_count'),

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

const filteredEnrollments = computed(() => {
    let filtered = localEnrollments.value || []
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortEnrollments(filtered)
    }

    filtered = filtered.filter((enrollment) => {
        const values = [
            enrollment?.id,
            enrollment?.status,
            enrollment?.notes,

            getUserName(enrollment),
            getUserEmail(enrollment),

            getCourseTitle(enrollment),
            getCourseSlug(enrollment),
            getCourseShort(enrollment),
            getCourseDescription(enrollment),

            getScheduleTitle(enrollment),
            getScheduleSlug(enrollment),

            getOrderNumber(enrollment),
        ]

        return values.some(value => normalize(value).includes(query))
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

/* ==========================================================
 * УДАЛЕНИЕ
 * ========================================================== */

const showConfirmDeleteModal = ref(false)
const enrollmentToDelete = ref(null)

const confirmDelete = (enrollment) => {
    enrollmentToDelete.value = enrollment
    showConfirmDeleteModal.value = true
}

const closeModal = () => {
    showConfirmDeleteModal.value = false
    enrollmentToDelete.value = null
}

const deleteEnrollment = () => {
    if (!enrollmentToDelete.value?.id) return

    const idToDelete = enrollmentToDelete.value.id

    router.delete(route('admin.schoolEnrollments.destroy', {
        schoolEnrollment: idToDelete,
    }), {
        preserveScroll: true,
        preserveState: false,

        onSuccess: () => {
            toast.success(`Зачисление ID: ${idToDelete} удалено.`)
        },

        onError: (errors) => {
            const firstKey = Object.keys(errors || {})[0]
            const errorMsg = errors?.general || errors?.[firstKey] || 'Ошибка при удалении зачисления.'

            toast.error(`${errorMsg} ID: ${idToDelete}`)
        },

        onFinish: () => closeModal(),
    })
}
</script>

<template>
    <AdminLayout :title="t('enrollments')">
        <template #header>
            <TitlePage>
                {{ t('enrollments') }}
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
                    <DefaultButton :href="route('admin.schoolEnrollments.create')">
                        {{ t('addEnrollment') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminSchoolEnrollmentsProcessingMode"
                        :mode="adminSchoolEnrollmentsProcessingMode"
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
                        update-route="admin.settings.updateAdminCountEnrollments"
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

                <EnrollmentTable
                    v-if="viewMode === 'table'"
                    :enrollments="displayedEnrollments"
                    @delete="confirmDelete"
                />

                <EnrollmentCardGrid
                    v-else
                    :enrollments="displayedEnrollments"
                    @delete="confirmDelete"
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

        <DangerModal
            :show="showConfirmDeleteModal"
            @close="closeModal"
            :onCancel="closeModal"
            :onConfirm="deleteEnrollment"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
