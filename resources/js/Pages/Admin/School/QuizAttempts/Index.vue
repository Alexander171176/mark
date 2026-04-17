<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов
 * Список попыток (QuizAttempt)
 */
import { defineProps, ref, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import SearchInput from '@/Components/Admin/Search/SearchInput.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import BulkActionSelect from '@/Components/Admin/QuizAttempt/Select/BulkActionSelect.vue'
import CountTable from '@/Components/Admin/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/Select/ItemsPerPageSelect.vue'
import QuizAttemptTable from '@/Components/Admin/QuizAttempt/Table/QuizAttemptTable.vue'
import QuizAttemptCardGrid from '@/Components/Admin/QuizAttempt/View/QuizAttemptCardGrid.vue'
import DangerModal from '@/Components/Admin/Modal/DangerModal.vue'
import Pagination from '@/Components/Admin/Pagination/Pagination.vue'
import SortSelect from '@/Components/Admin/QuizAttempt/Sort/SortSelect.vue'
import ToggleViewButton from '@/Components/Admin/Buttons/ToggleViewButton.vue'

// --- Инициализация экземпляр i18n, toast ---
const { t } = useI18n()
const toast = useToast()

/**
 * Входные свойства компонента.
 */
const props = defineProps({
    attempts: Array,
    attemptsCount: Number,
    adminCountAttempts: Number,
    adminSortAttempts: String,
})

/** Вид: таблица или карточки (общий ключ для админки) */
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')
watch(viewMode, (val) => localStorage.setItem('admin_view_mode', val))

/** Кол-во элементов на странице */
const itemsPerPage = ref(props.adminCountAttempts || 10)

/**
 * Наблюдатель за изменением количества элементов на странице.
 */
watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountQuizAttempts'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

/** Параметр сортировки */
const sortParam = ref(props.adminSortAttempts || 'idDesc')

/**
 * Наблюдатель за изменением параметра сортировки.
 */
watch(sortParam, (newVal) => {
    router.put(route('admin.settings.updateAdminSortQuizAttempts'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info('Сортировка успешно изменена'),
        onError: (errors) => {
            const firstError = errors ? Object.values(errors)[0] : null
            toast.error(firstError || 'Ошибка обновления параметра сортировки.')
        },
    })
})

/** Модалка удаления (как у уроков) */
const showConfirmDeleteModal = ref(false)
const attemptToDeleteId = ref(null)
const attemptToDeleteTitle = ref('')

/**
 * Открывает модальное окно подтверждения удаления с входными переменными.
 * (как Lessons: confirmDelete(id, title))
 */
const confirmDelete = (id, title) => {
    attemptToDeleteId.value = id
    attemptToDeleteTitle.value = title
    showConfirmDeleteModal.value = true
}

/**
 * Закрывает модальное окно подтверждения и сбрасывает связанные переменные.
 */
const closeModal = () => {
    showConfirmDeleteModal.value = false
    attemptToDeleteId.value = null
    attemptToDeleteTitle.value = ''
}

/**
 * Отправляет запрос на удаление (как Lessons: deleteLesson, только deleteAttempt).
 */
const deleteAttempt = () => {
    if (attemptToDeleteId.value === null) return

    const idToDelete = attemptToDeleteId.value
    const titleToDelete = attemptToDeleteTitle.value

    router.delete(route('admin.quizAttempts.destroy', { quizAttempt: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            closeModal()
            toast.success(`Попытка "${titleToDelete || 'ID: ' + idToDelete}" удалена.`)
        },
        onError: (errors) => {
            closeModal()
            const errorMsg =
                errors.general ||
                errors[Object.keys(errors)[0]] ||
                'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Попытка: ${titleToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => {
            attemptToDeleteId.value = null
            attemptToDeleteTitle.value = ''
        },
    })
}

/** Пагинация и поиск */
const currentPage = ref(1)
const searchQuery = ref('')

/** Нормализация даты */
const toTs = (v) => {
    if (!v) return null
    const ts = new Date(v).getTime()
    return Number.isFinite(ts) ? ts : null
}

/** Сортировка массива попыток */
const sortAttempts = (attempts) => {
    const value = sortParam.value
    const list = attempts.slice()

    if (value === 'idAsc') return list.sort((a, b) => a.id - b.id)
    if (value === 'idDesc') return list.sort((a, b) => b.id - a.id)

    if (value === 'startedAtAsc' || value === 'startedAtDesc') {
        return list.sort((a, b) => {
            const ad = toTs(a.started_at)
            const bd = toTs(b.started_at)
            if (ad == null && bd == null) return 0
            if (ad == null) return 1
            if (bd == null) return -1
            return value === 'startedAtAsc' ? (ad - bd) : (bd - ad)
        })
    }

    if (value === 'finishedAtAsc' || value === 'finishedAtDesc') {
        return list.sort((a, b) => {
            const ad = toTs(a.finished_at)
            const bd = toTs(b.finished_at)
            if (ad == null && bd == null) return 0
            if (ad == null) return 1
            if (bd == null) return -1
            return value === 'finishedAtAsc' ? (ad - bd) : (bd - ad)
        })
    }

    if (value === 'percentAsc' || value === 'percentDesc') {
        return list.sort((a, b) => {
            const av = Number.isFinite(+a.percent) ? +a.percent : 0
            const bv = Number.isFinite(+b.percent) ? +b.percent : 0
            return value === 'percentAsc' ? (av - bv) : (bv - av)
        })
    }

    return list
}

/** Фильтрация + сортировка */
const filteredAttempts = computed(() => {
    let filtered = props.attempts || []

    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase()

        filtered = filtered.filter(a =>
            (a.status || '').toString().toLowerCase().includes(q) ||
            (a.quiz?.title || '').toString().toLowerCase().includes(q) ||
            (a.quiz?.slug || '').toString().toLowerCase().includes(q) ||
            (a.user?.name || '').toString().toLowerCase().includes(q) ||
            (a.user?.email || '').toString().toLowerCase().includes(q) ||
            (a.course?.title || '').toString().toLowerCase().includes(q) ||
            (a.module?.title || '').toString().toLowerCase().includes(q) ||
            (a.lesson?.title || '').toString().toLowerCase().includes(q) ||
            (a.attempt_number ?? '').toString().toLowerCase().includes(q) ||
            (a.score ?? '').toString().toLowerCase().includes(q) ||
            (a.max_score ?? '').toString().toLowerCase().includes(q) ||
            (a.percent ?? '').toString().toLowerCase().includes(q)
        )
    }

    return sortAttempts(filtered)
})

/** Пагинация */
const paginatedAttempts = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value
    return filteredAttempts.value.slice(start, start + itemsPerPage.value)
})

/** Общее количество страниц */
const totalPages = computed(() =>
    Math.ceil((filteredAttempts.value.length || 0) / itemsPerPage.value)
)

/** Если после фильтрации текущая страница > totalPages — откатываем */
watch(filteredAttempts, () => {
    if (currentPage.value > totalPages.value) currentPage.value = totalPages.value
})

/** Массовые действия */
const selectedAttempts = ref([])

/**
 * Логика выбора всех для массовых действий. (как Lessons)
 */
const toggleAll = ({ ids, checked }) => {
    if (checked) selectedAttempts.value = [...ids]
    else selectedAttempts.value = []
}

/**
 * Обрабатывает событие выбора/снятия выбора одной строки. (как Lessons)
 */
const toggleSelectAttempt = (id) => {
    const idx = selectedAttempts.value.indexOf(id)
    if (idx > -1) selectedAttempts.value.splice(idx, 1)
    else selectedAttempts.value.push(id)
}

/**
 * Массовое обновление статуса выбранных.
 * (аналог bulkToggleActivity, только статус)
 */
const bulkUpdateStatus = (status) => {
    if (!selectedAttempts.value.length) {
        toast.warning('Выберите попытки')
        return
    }

    router.put(
        route('admin.actions.quizAttempts.bulkUpdateStatus'),
        { ids: selectedAttempts.value, status },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                toast.success('Статус попыток массово обновлён')
                const updatedIds = [...selectedAttempts.value]
                selectedAttempts.value = []

                paginatedAttempts.value.forEach(a => {
                    if (updatedIds.includes(a.id)) a.status = status
                })
            },
            onError: (errors) => {
                const msg =
                    errors?.ids || errors?.status || errors?.general ||
                    'Не удалось массово обновить статус'
                toast.error(msg)
            },
        }
    )
}

/**
 * Обрабатывает выбор действия в селекте массовых действий. (как Lessons)
 */
const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        selectedAttempts.value = paginatedAttempts.value.map(a => a.id)
    } else if (action === 'deselectAll') {
        selectedAttempts.value = []
    } else if (action.startsWith('status:')) {
        const status = action.split(':')[1]
        bulkUpdateStatus(status)
    } else if (action === 'delete') {
        bulkDestroy()
    }

    event.target.value = ''
}

/** Массовое удаление */
const bulkDestroy = () => {
    if (!selectedAttempts.value.length) {
        toast.warning('Выберите попытки')
        return
    }

    router.delete(
        route('admin.actions.quizAttempts.bulkDestroy'),
        {
            data: { ids: selectedAttempts.value },
            preserveScroll: true,
            preserveState: false,
            onSuccess: () => {
                toast.success('Попытки успешно удалены')
                selectedAttempts.value = []
            },
            onError: (errors) => {
                const msg =
                    errors?.ids || errors?.general ||
                    'Ошибка удаления'
                toast.error(msg)
            },
        }
    )
}

</script>

<template>
    <AdminLayout :title="t('quizAttempts')">
        <template #header>
            <TitlePage>{{ t('quizAttempts') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <DefaultButton :href="route('admin.quizAttempts.create')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"
                                />
                            </svg>
                        </template>
                        {{ t('addQuizAttempt') }}
                    </DefaultButton>

                    <BulkActionSelect
                        v-if="attemptsCount"
                        @change="handleBulkAction"
                    />
                </div>

                <SearchInput
                    v-if="attemptsCount"
                    v-model="searchQuery"
                    :placeholder="t('search')"
                />

                <div v-if="attemptsCount" class="flex items-center justify-end my-2">
                    <CountTable>{{ attemptsCount }}</CountTable>
                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <!-- Один общий чекбокс выбрать всё -->
                <div v-if="attemptsCount"
                     class="flex items-center justify-between px-3 py-2
                            border-b border-slate-400 dark:border-slate-500">

                    <div class="text-xs text-slate-600 dark:text-slate-200">
                        {{ t('selected') }}: {{ selectedAttempts.length }}
                    </div>
                    <label
                        class="flex items-center text-xs text-slate-600
                               dark:text-slate-200 cursor-pointer">
                        <span>{{ t('selectAll') }}</span>
                        <input
                            type="checkbox"
                            class="rounded-sm border-slate-400 mx-2"
                            :checked="paginatedAttempts.length &&
                            paginatedAttempts.every(a => selectedAttempts.includes(a.id))"
                            @change="toggleAll({ ids: paginatedAttempts.map(a => a.id),
                            checked: $event.target.checked })"
                        />
                    </label>
                </div>

                <!-- Табличный вид -->
                <QuizAttemptTable
                    v-if="viewMode === 'table'"
                    :attempts="paginatedAttempts"
                    :selected-attempts="selectedAttempts"
                    @toggle-select="toggleSelectAttempt"
                    @toggle-all="toggleAll"
                    @delete="confirmDelete"
                />

                <!-- Карточный вид -->
                <QuizAttemptCardGrid
                    v-else
                    :attempts="paginatedAttempts"
                    :selected-attempts="selectedAttempts"
                    @toggle-select="toggleSelectAttempt"
                    @toggle-all="toggleAll"
                    @delete="confirmDelete"
                />

                <div
                    class="flex justify-between items-center flex-col md:flex-row my-1"
                    v-if="attemptsCount"
                >
                    <ItemsPerPageSelect
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredAttempts.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="val => (sortParam = val)"
                    />
                </div>
            </div>
        </div>

        <!-- Обычное удаление 1 попытки -->
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
