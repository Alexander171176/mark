<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * список попыток прохождения викторин
 */
import { computed, defineProps, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import SearchInput from '@/Components/Admin/UI/Search/SearchInput.vue'
// import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import CountTable from '@/Components/Admin/UI/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/UI/Select/ItemsPerPageSelect.vue'
import DangerModal from '@/Components/Admin/UI/Modal/DangerModal.vue'
import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue'
import ToggleViewButton from '@/Components/Admin/UI/Buttons/ToggleViewButton.vue'

import SortSelect from '@/Components/Admin/School/SchoolQuizAttempt/Sort/SortSelect.vue'
import QuizAttemptTable from '@/Components/Admin/School/SchoolQuizAttempt/Table/QuizAttemptTable.vue'
import QuizAttemptCardGrid from '@/Components/Admin/School/SchoolQuizAttempt/View/QuizAttemptCardGrid.vue'
import BulkActionSelect from '@/Components/Admin/School/SchoolQuizAttempt/Select/BulkActionSelect.vue'

// Локализация и уведомления
const { t } = useI18n()
const toast = useToast()

// Пропсы страницы списка попыток квизов
const props = defineProps({
    attempts: { type: Array, default: () => [] },
    attemptsCount: { type: Number, default: 0 },
    filters: { type: Object, default: () => ({}) },

    adminSchoolQuizAttemptsPerPage: { type: Number, default: 10 },
    adminSchoolQuizAttemptsDefaultSort: { type: String, default: 'idDesc' },

    users: { type: Array, default: () => [] },
    quizzes: { type: Array, default: () => [] },
    enrollments: { type: Array, default: () => [] },
    courses: { type: Array, default: () => [] },
    modules: { type: Array, default: () => [] },
    lessons: { type: Array, default: () => [] },
})

// Режим отображения таблица/карточки
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')

// Сохранение режима отображения в localStorage
watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode', val)
})

// Количество элементов на странице
const itemsPerPage = ref(props.adminSchoolQuizAttemptsPerPage ?? 10)

// Обновление настройки количества элементов
watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountQuizAttempts'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

// Параметр сортировки
const sortParam = ref(props.adminSchoolQuizAttemptsDefaultSort ?? 'idDesc')

// Обновление настройки сортировки
watch(sortParam, (newVal) => {
    router.put(route('admin.settings.updateAdminSortQuizAttempts'), { value: newVal }, {
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

// Модальное окно удаления
const showConfirmDeleteModal = ref(false)

// Попытка для удаления
const attemptToDelete = ref(null)

// Открытие модального окна удаления
const confirmDelete = (attempt) => {
    attemptToDelete.value = attempt
    showConfirmDeleteModal.value = true
}

// Закрытие модального окна удаления
const closeModal = () => {
    showConfirmDeleteModal.value = false
    attemptToDelete.value = null
}

// Удаление попытки
const deleteAttempt = () => {
    if (!attemptToDelete.value?.id) return

    const idToDelete = attemptToDelete.value.id

    router.delete(route('admin.schoolQuizAttempts.destroy', {
        schoolQuizAttempt: idToDelete,
    }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            toast.success(`Попытка ID: ${idToDelete} удалена.`)
        },
        onError: (errors) => {
            const firstKey = Object.keys(errors || {})[0]
            const errorMsg = errors?.general || errors?.[firstKey] || 'Ошибка при удалении попытки.'

            toast.error(`${errorMsg} ID: ${idToDelete}`)
        },
        onFinish: () => closeModal(),
    })
}

// Нормализация строк для поиска и сортировки
const normalize = (value) => (value ?? '').toString().trim().toLowerCase()

// Преобразование даты во временную метку
const toTime = (value) => {
    if (!value) return 0

    const time = new Date(value).getTime()

    return Number.isNaN(time) ? 0 : time
}

// Сортировка попыток
const sortAttempts = (items) => {
    const list = items.slice()

    switch (sortParam.value) {
        case 'idAsc':
            return list.sort((a, b) => a.id - b.id)

        case 'idDesc':
            return list.sort((a, b) => b.id - a.id)

        case 'attemptAsc':
            return list.sort((a, b) => (a.attempt_number ?? 0) - (b.attempt_number ?? 0))

        case 'attemptDesc':
            return list.sort((a, b) => (b.attempt_number ?? 0) - (a.attempt_number ?? 0))

        case 'scoreAsc':
            return list.sort((a, b) => (a.score ?? 0) - (b.score ?? 0))

        case 'scoreDesc':
            return list.sort((a, b) => (b.score ?? 0) - (a.score ?? 0))

        case 'percentAsc':
            return list.sort((a, b) => (a.percent ?? 0) - (b.percent ?? 0))

        case 'percentDesc':
            return list.sort((a, b) => (b.percent ?? 0) - (a.percent ?? 0))

        case 'startedAtAsc':
            return list.sort((a, b) => toTime(a.started_at) - toTime(b.started_at))

        case 'startedAtDesc':
            return list.sort((a, b) => toTime(b.started_at) - toTime(a.started_at))

        case 'finishedAtAsc':
            return list.sort((a, b) => toTime(a.finished_at) - toTime(b.finished_at))

        case 'finishedAtDesc':
            return list.sort((a, b) => toTime(b.finished_at) - toTime(a.finished_at))

        case 'statusAsc':
            return list.sort((a, b) => normalize(a.status).localeCompare(normalize(b.status)))

        case 'statusDesc':
            return list.sort((a, b) => normalize(b.status).localeCompare(normalize(a.status)))

        case 'userNameAsc':
            return list.sort((a, b) => normalize(a.user?.name).localeCompare(normalize(b.user?.name)))

        case 'userNameDesc':
            return list.sort((a, b) => normalize(b.user?.name).localeCompare(normalize(a.user?.name)))

        case 'quizTitleAsc':
            return list.sort((a, b) => normalize(a.quiz?.title).localeCompare(normalize(b.quiz?.title)))

        case 'quizTitleDesc':
            return list.sort((a, b) => normalize(b.quiz?.title).localeCompare(normalize(a.quiz?.title)))

        default:
            return list
    }
}

// Фильтрация и поиск попыток
const filteredAttempts = computed(() => {
    let filtered = Array.isArray(props.attempts) ? props.attempts : []

    if (searchQuery.value) {
        const q = normalize(searchQuery.value)

        filtered = filtered.filter((attempt) => {
            const values = [
                attempt.id,
                attempt.status,
                attempt.attempt_number,
                attempt.score,
                attempt.max_score,
                attempt.percent,

                attempt.user?.name,
                attempt.user?.email,

                attempt.quiz?.title,
                attempt.quiz?.slug,

                attempt.enrollment?.id,

                attempt.course?.title,
                attempt.course?.slug,

                attempt.module?.title,
                attempt.module?.slug,

                attempt.lesson?.title,
                attempt.lesson?.slug,

                attempt.ip_address,
                attempt.user_agent,
            ]

            return values.some(value => normalize(value).includes(q))
        })
    }

    return sortAttempts(filtered)
})

// Пагинация списка попыток
const paginatedAttempts = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value

    return filteredAttempts.value.slice(start, start + itemsPerPage.value)
})

// Общее количество страниц
const totalPages = computed(() => {
    if (!itemsPerPage.value) return 1

    return Math.ceil(filteredAttempts.value.length / itemsPerPage.value) || 1
})

// Корректировка страницы после фильтрации
watch([filteredAttempts, itemsPerPage], () => {
    if (currentPage.value > totalPages.value) {
        currentPage.value = totalPages.value
    }
})

// Выбранные попытки
const selectedAttempts = ref([])

// Выбор/снятие всех попыток
const toggleAll = ({ ids, checked }) => {
    selectedAttempts.value = checked ? [...ids] : []
}

// Выбор одной попытки
const toggleSelectAttempt = (id) => {
    const index = selectedAttempts.value.indexOf(id)

    if (index > -1) {
        selectedAttempts.value.splice(index, 1)
    } else {
        selectedAttempts.value.push(id)
    }
}

// Обновление локальной записи попытки
const patchAttempt = (attemptId, payload) => {
    const attempt = props.attempts.find(item => item.id === attemptId)

    if (attempt) {
        Object.assign(attempt, payload)
    }
}

// Массовое обновление статуса
const bulkUpdateStatus = (status) => {
    if (!selectedAttempts.value.length) {
        toast.warning('Выберите попытки.')
        return
    }

    const idsToUpdate = [...selectedAttempts.value]

    router.put(route('admin.actions.schoolQuizAttempts.bulkUpdateStatus'), {
        ids: idsToUpdate,
        status,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            idsToUpdate.forEach(id => patchAttempt(id, { status }))
            selectedAttempts.value = []
            toast.success('Статус выбранных попыток обновлён.')
        },
        onError: (errors) => {
            toast.error(errors?.ids || errors?.status || errors?.general || 'Ошибка массового обновления статуса.')
        },
    })
}

// Массовое удаление попыток
const bulkDestroy = () => {
    if (!selectedAttempts.value.length) {
        toast.warning('Выберите попытки для удаления.')
        return
    }

    if (!confirm('Вы уверены, что хотите удалить выбранные попытки?')) return

    router.delete(route('admin.actions.schoolQuizAttempts.bulkDestroy'), {
        data: {
            ids: selectedAttempts.value,
            ...(props.filters?.school_quiz_id ? { school_quiz_id: props.filters.school_quiz_id } : {}),
            ...(props.filters?.user_id ? { user_id: props.filters.user_id } : {}),
            ...(props.filters?.status ? { status: props.filters.status } : {}),
        },
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            selectedAttempts.value = []
            toast.success('Выбранные попытки успешно удалены.')
        },
        onError: (errors) => {
            const firstKey = Object.keys(errors || {})[0]
            toast.error(errors[firstKey] || 'Ошибка массового удаления попыток.')
        },
    })
}

// Обработка массовых действий
const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        selectedAttempts.value = paginatedAttempts.value.map(attempt => attempt.id)
    } else if (action === 'deselectAll') {
        selectedAttempts.value = []
    } else if (action.startsWith('status:')) {
        bulkUpdateStatus(action.split(':')[1])
    } else if (action === 'delete') {
        bulkDestroy()
    }

    event.target.value = ''
}
</script>

<template>
    <AdminLayout :title="t('quizAttempts')">
        <template #header>
            <TitlePage>{{ t('quizAttempts') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500
                       dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-3">
<!--                    <DefaultButton :href="route('admin.schoolQuizAttempts.create')">-->
<!--                        <template #icon>-->
<!--                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0"-->
<!--                                 viewBox="0 0 16 16">-->
<!--                                <path-->
<!--                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"-->
<!--                                />-->
<!--                            </svg>-->
<!--                        </template>-->
<!--                        {{ t('addQuizAttempt') }}-->
<!--                    </DefaultButton>-->
                </div>

                <SearchInput
                    v-if="attemptsCount"
                    v-model="searchQuery"
                    :placeholder="t('search')"
                />

                <div
                    v-if="attemptsCount"
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
                    v-if="attemptsCount"
                    class="flex justify-between items-center flex-col md:flex-row my-3"
                >
                    <CountTable>{{ attemptsCount }}</CountTable>

                    <BulkActionSelect @change="handleBulkAction" />

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div
                    v-if="attemptsCount"
                    class="flex justify-center items-center flex-col md:flex-row mb-3"
                >
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredAttempts.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                </div>

                <QuizAttemptTable
                    v-if="viewMode === 'table'"
                    :attempts="paginatedAttempts"
                    :selected-attempts="selectedAttempts"
                    @toggle-select="toggleSelectAttempt"
                    @toggle-all="toggleAll"
                    @delete="confirmDelete"
                />

                <QuizAttemptCardGrid
                    v-else
                    :attempts="paginatedAttempts"
                    :selected-attempts="selectedAttempts"
                    @toggle-select="toggleSelectAttempt"
                    @toggle-all="toggleAll"
                    @delete="confirmDelete"
                />

                <div
                    v-if="attemptsCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredAttempts.length"
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
            :onConfirm="deleteAttempt"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
