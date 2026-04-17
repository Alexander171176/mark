<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов
 * Список зачислений (паттерн)
 */
import { defineProps, ref, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import SearchInput from '@/Components/Admin/Search/SearchInput.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import CountTable from '@/Components/Admin/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/Select/ItemsPerPageSelect.vue'
import Pagination from '@/Components/Admin/Pagination/Pagination.vue'
import DangerModal from '@/Components/Admin/Modal/DangerModal.vue'
import SortSelect from '@/Components/Admin/Enrollment/Sort/SortSelect.vue'
import EnrollmentTable from '@/Components/Admin/Enrollment/Table/EnrollmentTable.vue'
import EnrollmentCardGrid from '@/Components/Admin/Enrollment/View/EnrollmentCardGrid.vue'
import ToggleViewButton from '@/Components/Admin/Buttons/ToggleViewButton.vue'

// --- i18n, toast
const { t } = useI18n()
const toast = useToast()

/**
 * Входные свойства компонента.
 */
const props = defineProps({
    enrollments: {
        type: Array,
        default: () => [],
    },
    enrollmentsCount: {
        type: Number,
        default: 0,
    },
    adminCountEnrollments: {
        type: Number,
        default: 10,
    },
    adminSortEnrollments: {
        type: String,
        default: 'idDesc',
    },
    filters: {
        type: Object,
        default: () => ({
            status: null,
            user_id: null,
            course_id: null,
        }),
    },
})

/**
 * Вид: таблица или карточки (общий ключ, как у Courses / CohortEnrollments)
 */
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')

watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode', val)
})

/** Кол-во элементов на странице (из настроек) */
const itemsPerPage = ref(props.adminCountEnrollments || 10)

/**
 * Сохранение настройки количества элементов на странице.
 */
watch(itemsPerPage, (newVal) => {
    router.put(
        route('admin.settings.updateAdminCountEnrollments'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
            onError: (errors) =>
                toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
        },
    )
})

/** Параметр сортировки (из настроек) */
const sortParam = ref(props.adminSortEnrollments || 'idDesc')

/**
 * Сохранение настройки сортировки.
 */
watch(sortParam, (newVal) => {
    router.put(
        route('admin.settings.updateAdminSortEnrollments'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info('Сортировка успешно изменена'),
            onError: (errors) => toast.error(errors.value || 'Ошибка обновления сортировки.'),
        },
    )
})

/** Модалка удаления */
const showConfirmDeleteModal = ref(false)
const enrollmentToDeleteId = ref(null)

/**
 * Открывает модальное окно подтверждения удаления.
 */
const confirmDelete = (id) => {
    enrollmentToDeleteId.value = id
    showConfirmDeleteModal.value = true
}

/**
 * Закрывает модальное окно подтверждения удаления.
 */
const closeModal = () => {
    showConfirmDeleteModal.value = false
    enrollmentToDeleteId.value = null
}

/**
 * Отправляет запрос на удаление зачисления (soft delete).
 */
const deleteEnrollment = () => {
    if (enrollmentToDeleteId.value === null) return
    const idToDelete = enrollmentToDeleteId.value

    router.delete(
        route('admin.enrollments.destroy', { enrollment: idToDelete }),
        {
            preserveScroll: true,
            preserveState: false,
            onSuccess: () => {
                closeModal()
                toast.success('Зачисление удалено', { id: idToDelete })
            },
            onError: (errors) => {
                closeModal()
                const errorMsg =
                    errors.general ||
                    errors[Object.keys(errors)[0]] ||
                    'Произошла ошибка при удалении.'
                toast.error(`${errorMsg} (ID: ${idToDelete})`)
            },
            onFinish: () => {
                enrollmentToDeleteId.value = null
            },
        },
    )
}

/** Пагинация и поиск */
const currentPage = ref(1)
const searchQuery = ref('')

/**
 * Сортировка массива зачислений.
 * sortParam:
 *  - idAsc / idDesc
 *  - started_at / expires_at / completed_at (новые сверху)
 *  - status
 *  - progress_desc (по прогрессу)
 */
const sortEnrollments = (enrollments) => {
    const value = sortParam.value
    const list = enrollments.slice()

    if (value === 'idAsc') {
        return list.sort((a, b) => a.id - b.id)
    }

    if (value === 'idDesc') {
        return list.sort((a, b) => b.id - a.id)
    }

    if (['started_at', 'expires_at', 'completed_at'].includes(value)) {
        // Новые сверху, null в конец
        return list.sort((a, b) => {
            const aTime = a[value] ? new Date(a[value]).getTime() : 0
            const bTime = b[value] ? new Date(b[value]).getTime() : 0
            return bTime - aTime
        })
    }

    if (value === 'progress_desc') {
        return list.sort((a, b) => {
            const av = a.progress_percent ?? 0
            const bv = b.progress_percent ?? 0
            return bv - av
        })
    }

    if (value === 'status') {
        return list.sort((a, b) => {
            const av = a.status || ''
            const bv = b.status || ''
            if (av < bv) return -1
            if (av > bv) return 1
            return 0
        })
    }

    // По умолчанию — id по убыванию
    return list.sort((a, b) => b.id - a.id)
}

/**
 * Фильтрация + сортировка.
 * Поиск: по user.name, user.email, course.title, course.slug, статусу.
 */
const filteredEnrollments = computed(() => {
    let filtered = props.enrollments || []

    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase()

        filtered = filtered.filter((e) => {
            const userName = e.user?.name?.toLowerCase() || ''
            const userEmail = e.user?.email?.toLowerCase() || ''
            const courseTitle = e.course?.title?.toLowerCase() || ''
            const courseSlug = e.course?.slug?.toLowerCase() || ''
            const status = e.status?.toLowerCase() || ''

            return (
                userName.includes(q) ||
                userEmail.includes(q) ||
                courseTitle.includes(q) ||
                courseSlug.includes(q) ||
                status.includes(q)
            )
        })
    }

    return sortEnrollments(filtered)
})

/** Пагинация */
const paginatedEnrollments = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value
    return filteredEnrollments.value.slice(
        start,
        start + itemsPerPage.value,
    )
})

/**
 * Общее количество страниц.
 */
const totalPages = computed(() =>
    Math.ceil((filteredEnrollments.value.length || 0) / itemsPerPage.value),
)

// Если после фильтрации текущая страница > totalPages — откатываем
watch(filteredEnrollments, () => {
    if (currentPage.value > totalPages.value) {
        currentPage.value = totalPages.value || 1
    }
})
</script>

<template>
    <AdminLayout :title="t('enrollments')">
        <template #header>
            <TitlePage>{{ t('enrollments') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <!-- Верхняя панель: кнопка + счётчик + переключение вида -->
                <div class="flex justify-between items-center mb-4">
                    <!-- Добавить зачисление -->
                    <DefaultButton :href="route('admin.enrollments.create')">
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
                        {{ t('addEnrollment') }}
                    </DefaultButton>

                    <div class="flex items-center space-x-3">
                        <CountTable v-if="enrollmentsCount">
                            {{ enrollmentsCount }}
                        </CountTable>

                        <ToggleViewButton v-if="enrollmentsCount" v-model:viewMode="viewMode" />
                    </div>
                </div>

                <!-- Поиск -->
                <SearchInput
                    v-if="enrollmentsCount"
                    v-model="searchQuery"
                    :placeholder="t('search')"
                />

                <!-- Таблица / карточки зачислений -->
                <EnrollmentTable
                    v-if="enrollmentsCount && viewMode === 'table'"
                    :enrollments="paginatedEnrollments"
                    @delete="confirmDelete"
                />

                <EnrollmentCardGrid
                    v-else-if="enrollmentsCount && viewMode === 'cards'"
                    :enrollments="paginatedEnrollments"
                    @delete="confirmDelete"
                />

                <div
                    v-if="enrollmentsCount"
                    class="flex justify-between items-center flex-col md:flex-row
                           my-2 space-y-2 md:space-y-0"
                >
                    <ItemsPerPageSelect
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredEnrollments.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="(val) => (sortParam = val)"
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
