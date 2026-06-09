<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список зачислений студентов
 */
import { computed, defineProps, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue'
import SearchInput from '@/Components/Admin/UI/Search/SearchInput.vue'
import CountTable from '@/Components/Admin/UI/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/UI/Select/ItemsPerPageSelect.vue'
import ToggleViewButton from '@/Components/Admin/UI/Buttons/ToggleViewButton.vue'
import DangerModal from '@/Components/Admin/UI/Modal/DangerModal.vue'

import EnrollmentTable from '@/Components/Admin/School/SchoolEnrollment/Table/EnrollmentTable.vue'
import EnrollmentCardGrid from '@/Components/Admin/School/SchoolEnrollment/View/EnrollmentCardGrid.vue'
import SortSelect from '@/Components/Admin/School/SchoolEnrollment/Sort/SortSelect.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'

// Локализация и уведомления
const { t } = useI18n()
const toast = useToast()

// Пропсы страницы списка зачислений
const props = defineProps({
    enrollments: { type: Array, default: () => [] },
    enrollmentsCount: { type: Number, default: 0 },
    filters: { type: Object, default: () => ({}) },

    adminSchoolEnrollmentsPerPage: { type: Number, default: 10 },
    adminSchoolEnrollmentsDefaultSort: { type: String, default: 'idDesc' },

    users: { type: Array, default: () => [] },
    courses: { type: Array, default: () => [] },
    schedules: { type: Array, default: () => [] },
    orders: { type: Array, default: () => [] },
})

// Режим отображения списка
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')

// Сохранение режима отображения
watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode', val)
})

// Количество элементов на странице
const itemsPerPage = ref(props.adminSchoolEnrollmentsPerPage ?? 10)

// Сохранение количества элементов
watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountEnrollments'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

// Параметр сортировки
const sortParam = ref(props.adminSchoolEnrollmentsDefaultSort ?? 'idDesc')

// Сохранение выбранной сортировки
watch(sortParam, (newVal) => {
    router.put(route('admin.settings.updateAdminSortEnrollments'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info('Сортировка успешно изменена'),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления сортировки.'),
    })
})

// Текущая страница пагинации
const currentPage = ref(1)

// Поисковый запрос
const searchQuery = ref('')

// Состояние модального окна удаления
const showConfirmDeleteModal = ref(false)

// Выбранное зачисление для удаления
const enrollmentToDelete = ref(null)

// Открытие модального окна удаления
const confirmDelete = (enrollment) => {
    enrollmentToDelete.value = enrollment
    showConfirmDeleteModal.value = true
}

// Закрытие модального окна
const closeModal = () => {
    showConfirmDeleteModal.value = false
    enrollmentToDelete.value = null
}

// Удаление зачисления
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

// Нормализация значения для поиска и сортировки
const normalize = (value) => (value ?? '').toString().trim().toLowerCase()

// Преобразование даты в timestamp
const toTime = (value) => {
    if (!value) return 0

    const time = new Date(value).getTime()

    return Number.isNaN(time) ? 0 : time
}

// Сортировка зачислений
const sortEnrollments = (items) => {
    const list = items.slice()

    switch (sortParam.value) {
        case 'idAsc':
            return list.sort((a, b) => a.id - b.id)

        case 'idDesc':
            return list.sort((a, b) => b.id - a.id)

        case 'startedAtAsc':
            return list.sort((a, b) => toTime(a.started_at) - toTime(b.started_at))

        case 'startedAtDesc':
            return list.sort((a, b) => toTime(b.started_at) - toTime(a.started_at))

        case 'completedAtAsc':
            return list.sort((a, b) => toTime(a.completed_at) - toTime(b.completed_at))

        case 'completedAtDesc':
            return list.sort((a, b) => toTime(b.completed_at) - toTime(a.completed_at))

        case 'expiresAtAsc':
            return list.sort((a, b) => toTime(a.expires_at) - toTime(b.expires_at))

        case 'expiresAtDesc':
            return list.sort((a, b) => toTime(b.expires_at) - toTime(a.expires_at))

        case 'progressAsc':
            return list.sort((a, b) => (a.progress_percent ?? 0) - (b.progress_percent ?? 0))

        case 'progressDesc':
            return list.sort((a, b) => (b.progress_percent ?? 0) - (a.progress_percent ?? 0))

        case 'statusAsc':
            return list.sort((a, b) => normalize(a.status).localeCompare(normalize(b.status)))

        case 'statusDesc':
            return list.sort((a, b) => normalize(b.status).localeCompare(normalize(a.status)))

        case 'userNameAsc':
            return list.sort((a, b) =>
                normalize(a.user?.name).localeCompare(normalize(b.user?.name))
            )

        case 'userNameDesc':
            return list.sort((a, b) =>
                normalize(b.user?.name).localeCompare(normalize(a.user?.name))
            )

        case 'courseTitleAsc':
            return list.sort((a, b) =>
                normalize(a.course?.title).localeCompare(normalize(b.course?.title))
            )

        case 'courseTitleDesc':
            return list.sort((a, b) =>
                normalize(b.course?.title).localeCompare(normalize(a.course?.title))
            )

        default:
            return list
    }
}

// Фильтрация и поиск зачислений
const filteredEnrollments = computed(() => {
    let filtered = Array.isArray(props.enrollments) ? props.enrollments : []

    if (searchQuery.value) {
        const q = normalize(searchQuery.value)

        filtered = filtered.filter((enrollment) => {
            const values = [
                enrollment.id,
                enrollment.status,

                enrollment.user?.name,
                enrollment.user?.email,

                enrollment.course?.title,
                enrollment.course?.slug,

                enrollment.schedule?.title,
                enrollment.schedule?.slug,

                enrollment.order?.number,

                enrollment.notes,
            ]

            return values.some(value => normalize(value).includes(q))
        })
    }

    return sortEnrollments(filtered)
})

// Пагинация списка зачислений
const paginatedEnrollments = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value

    return filteredEnrollments.value.slice(start, start + itemsPerPage.value)
})

// Общее количество страниц
const totalPages = computed(() => {
    if (!itemsPerPage.value) return 1

    return Math.ceil(filteredEnrollments.value.length / itemsPerPage.value) || 1
})

// Корректировка страницы после фильтрации
watch([filteredEnrollments, itemsPerPage], () => {
    if (currentPage.value > totalPages.value) {
        currentPage.value = totalPages.value
    }
})
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
                <div class="sm:flex sm:justify-between sm:items-center mb-3">
                    <DefaultButton :href="route('admin.schoolEnrollments.create')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"
                                />
                            </svg>
                        </template>
                        {{ t('addEnrollment') }}
                    </DefaultButton>
                </div>

                <SearchInput
                    v-if="enrollmentsCount"
                    v-model="searchQuery"
                    :placeholder="t('search')"
                />

                <div
                    v-if="enrollmentsCount"
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
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredEnrollments.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                </div>

                <EnrollmentTable
                    v-if="viewMode === 'table'"
                    :enrollments="paginatedEnrollments"
                    @delete="confirmDelete"
                />

                <EnrollmentCardGrid
                    v-else
                    :enrollments="paginatedEnrollments"
                    @delete="confirmDelete"
                />

                <div
                    v-if="enrollmentsCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredEnrollments.length"
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
            :onConfirm="deleteEnrollment"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
