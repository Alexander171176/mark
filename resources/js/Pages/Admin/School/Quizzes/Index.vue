<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 * Список квизов (паттерн, аналог Assignment Index)
 */

import { defineProps, ref, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router, Link } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import SearchInput from '@/Components/Admin/Search/SearchInput.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import BulkActionSelect from '@/Components/Admin/Quiz/Select/BulkActionSelect.vue'
import CountTable from '@/Components/Admin/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/Select/ItemsPerPageSelect.vue'
import QuizTable from '@/Components/Admin/Quiz/Table/QuizTable.vue'
import QuizCardGrid from '@/Components/Admin/Quiz/View/QuizCardGrid.vue'
import DangerModal from '@/Components/Admin/Modal/DangerModal.vue'
import Pagination from '@/Components/Admin/Pagination/Pagination.vue'
import SortSelect from '@/Components/Admin/Quiz/Sort/SortSelect.vue'
import ToggleViewButton from '@/Components/Admin/Buttons/ToggleViewButton.vue'

// --- i18n, toast ---
const { t } = useI18n()
const toast = useToast()

/**
 * Входные свойства компонента.
 */
const props = defineProps({
    quizzes: Array,
    quizzesCount: Number,
    adminCountQuizzes: Number,
    adminSortQuizzes: String,
    currentLocale: String,
    availableLocales: Array,
})

/** Вид: таблица или карточки (общий ключ для админки) */
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')

watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode', val)
})

/** Кол-во элементов на странице */
const itemsPerPage = ref(props.adminCountQuizzes || 10)

/**
 * Наблюдатель за изменением количества элементов на странице.
 */
watch(itemsPerPage, (newVal) => {
    router.put(
        route('admin.settings.updateAdminCountQuizzes'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
            onError: (errors) =>
                toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
        }
    )
})

/** Параметр сортировки */
const sortParam = ref(props.adminSortQuizzes || 'idDesc')

/**
 * Наблюдатель за изменением параметра сортировки.
 */
watch(sortParam, (newVal) => {
    router.put(
        route('admin.settings.updateAdminSortQuizzes'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info('Сортировка успешно изменена'),
            onError: (errors) => {
                const firstError = errors ? Object.values(errors)[0] : null
                toast.error(firstError || 'Ошибка обновления сортировки.')
            },
        }
    )
})

/** Модалка удаления */
const showConfirmDeleteModal = ref(false)
const quizToDeleteId = ref(null)
const quizToDeleteTitle = ref('')

/**
 * Открывает модальное окно подтверждения удаления с входными переменными.
 */
const confirmDelete = (id, title) => {
    quizToDeleteId.value = id
    quizToDeleteTitle.value = title
    showConfirmDeleteModal.value = true
}

/**
 * Закрывает модальное окно подтверждения и сбрасывает связанные переменные.
 */
const closeModal = () => {
    showConfirmDeleteModal.value = false
    quizToDeleteId.value = null
    quizToDeleteTitle.value = ''
}

/**
 * Отправляет запрос на удаление.
 */
const deleteQuiz = () => {
    if (quizToDeleteId.value === null) return
    const idToDelete = quizToDeleteId.value
    const titleToDelete = quizToDeleteTitle.value

    router.delete(route('admin.quizzes.destroy', { quiz: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            closeModal()
            toast.success(`Квиз "${titleToDelete || 'ID: ' + idToDelete}" удалён.`)
        },
        onError: (errors) => {
            closeModal()
            const errorMsg =
                errors.general ||
                errors[Object.keys(errors)[0]] ||
                'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Квиз: ${titleToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => {
            quizToDeleteId.value = null
            quizToDeleteTitle.value = ''
        },
    })
}

/**
 * Отправляет запрос для изменения статуса активности в левой колонке.
 */
const toggleLeft = (quiz) => {
    const newLeft = !quiz.left
    const actionText = newLeft ? 'активирован в левой колонке' : 'деактивирован в левой колонке'

    router.put(
        route('admin.actions.quizzes.updateLeft', { quiz: quiz.id }),
        { left: newLeft },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                toast.success(`Квиз "${quiz.title}" ${actionText}.`)
            },
            onError: (errors) => {
                toast.error(
                    errors.left || errors.general || `Ошибка изменения активности для "${quiz.title}".`
                )
            },
        }
    )
}

/**
 * Отправляет запрос для изменения статуса активности в главном.
 */
const toggleMain = (quiz) => {
    const newMain = !quiz.main
    const actionText = newMain ? 'активирован в главном' : 'деактивирован в главном'

    router.put(
        route('admin.actions.quizzes.updateMain', { quiz: quiz.id }),
        { main: newMain },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                toast.success(`Квиз "${quiz.title}" ${actionText}.`)
            },
            onError: (errors) => {
                toast.error(
                    errors.main || errors.general || `Ошибка изменения активности для "${quiz.title}".`
                )
            },
        }
    )
}

/**
 * Отправляет запрос для изменения статуса активности в правой колонке.
 */
const toggleRight = (quiz) => {
    const newRight = !quiz.right
    const actionText = newRight ? 'активирован в правой колонке' : 'деактивирован в правой колонке'

    router.put(
        route('admin.actions.quizzes.updateRight', { quiz: quiz.id }),
        { right: newRight },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                toast.success(`Квиз "${quiz.title}" ${actionText}.`)
            },
            onError: (errors) => {
                toast.error(
                    errors.right || errors.general || `Ошибка изменения активности для "${quiz.title}".`
                )
            },
        }
    )
}

/**
 * Отправляет запрос для клонирования.
 */
const cloneQuiz = (quizObject) => {
    const quizId = quizObject?.id
    const quizTitle = quizObject?.title || `ID: ${quizId}`

    if (typeof quizId === 'undefined' || quizId === null) {
        console.error('Не удалось получить ID квиза для клонирования', quizObject)
        toast.error('Не удалось определить квиз для клонирования.')
        return
    }

    if (!confirm(`Вы уверены, что хотите клонировать квиз "${quizTitle}"?`)) {
        return
    }

    router.post(
        route('admin.actions.quizzes.clone', { quiz: quizId }),
        {},
        {
            preserveScroll: true,
            preserveState: false,
            onSuccess: () => {
                toast.success(`Квиз "${quizTitle}" успешно клонирован.`)
            },
            onError: (errors) => {
                const errorKey = Object.keys(errors)[0]
                const errorMessage =
                    errors[errorKey] || `Ошибка клонирования квиза "${quizTitle}".`
                toast.error(errorMessage)
            },
        }
    )
}

/** Пагинация и поиск */
const currentPage = ref(1)
const searchQuery = ref('')

/** Сортировка массива квизов */
const sortQuizzes = (quizzes) => {
    const value = sortParam.value
    const list = quizzes.slice()

    if (value === 'idAsc') {
        return list.sort((a, b) => a.id - b.id)
    }

    if (value === 'idDesc') {
        return list.sort((a, b) => b.id - a.id)
    }

    if (sortParam.value === 'left') {
        return list.sort((quiz) => quiz.left)
    }
    if (sortParam.value === 'noLeft') {
        return list.sort((quiz) => !quiz.left)
    }
    if (sortParam.value === 'main') {
        return list.sort((quiz) => quiz.main)
    }

    // Дата публикации — от новых к старым, null в конец
    if (value === 'published_at') {
        return list.sort((a, b) => {
            const aTime = a.published_at ? new Date(a.published_at).getTime() : 0
            const bTime = b.published_at ? new Date(b.published_at).getTime() : 0
            return bTime - aTime
        })
    }

    // Фильтры по активности
    if (value === 'activity') return quizzes.filter((q) => q.activity)
    if (value === 'inactive') return quizzes.filter((q) => !q.activity)

    // sort, title и прочее — обычная сортировка по возрастанию
    return list.sort((a, b) => {
        const av = a[value] ?? ''
        const bv = b[value] ?? ''
        if (av < bv) return -1
        if (av > bv) return 1
        return 0
    })
}

/** Фильтрация + сортировка (поиск по title/slug) */
const filteredQuizzes = computed(() => {
    let filtered = props.quizzes || []

    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase()
        filtered = filtered.filter(
            (quiz) =>
                (quiz.title || '').toLowerCase().includes(q) ||
                (quiz.slug || '').toLowerCase().includes(q)
        )
    }

    return sortQuizzes(filtered)
})

/** Пагинация */
const paginatedQuizzes = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value
    return filteredQuizzes.value.slice(start, start + itemsPerPage.value)
})

/** Общее число страниц */
const totalPages = computed(() =>
    Math.ceil((filteredQuizzes.value.length || 0) / itemsPerPage.value)
)

/** Обновление сортировки (drag&drop) */
const handleSortOrderUpdate = (orderedIds) => {
    const startSort = (currentPage.value - 1) * itemsPerPage.value

    const sortData = orderedIds.map((id, index) => ({
        id,
        sort: startSort + index + 1,
    }))

    router.put(
        route('admin.actions.quizzes.updateSortBulk'),
        { quizzes: sortData },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.success('Порядок квизов успешно обновлён.'),
            onError: (errors) => {
                console.error('Ошибка обновления сортировки квизов:', errors)
                toast.error(
                    errors?.general ||
                    errors?.quizzes ||
                    'Не удалось обновить порядок квизов.'
                )
                router.reload({ only: ['quizzes'], preserveScroll: true })
            },
        }
    )
}

/** Массовые действия */
const selectedQuizzes = ref([])

/**
 * Логика выбора всех для массовых действий.
 */
const toggleAll = ({ ids, checked }) => {
    if (checked) {
        selectedQuizzes.value = [...ids]
    } else {
        selectedQuizzes.value = []
    }
}

/**
 * Обрабатывает событие выбора/снятия выбора одной строки.
 */
const toggleSelectQuiz = (id) => {
    const idx = selectedQuizzes.value.indexOf(id)
    if (idx > -1) selectedQuizzes.value.splice(idx, 1)
    else selectedQuizzes.value.push(id)
}

/**
 * Выполняет массовое включение/выключение активности выбранных.
 */
const bulkToggleActivity = (newActivity) => {
    if (!selectedQuizzes.value.length) {
        toast.warning('Выберите квизы для активации/деактивации')
        return
    }

    router.put(
        route('admin.actions.quizzes.bulkUpdateActivity'),
        { ids: selectedQuizzes.value, activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                toast.success('Активность квизов массово обновлена')
                const updatedIds = [...selectedQuizzes.value]
                selectedQuizzes.value = []

                paginatedQuizzes.value.forEach((q) => {
                    if (updatedIds.includes(q.id)) q.activity = newActivity
                })
            },
            onError: (errors) => {
                const msg =
                    errors?.ids ||
                    errors?.activity ||
                    errors?.general ||
                    'Не удалось массово обновить активность квизов'
                toast.error(msg)
            },
        }
    )
}

/**
 * Выполняет массовое удаление выбранных.
 */
const bulkDelete = () => {
    if (selectedQuizzes.value.length === 0) {
        toast.warning('Выберите хотя бы один квиз для удаления.')
        return
    }
    if (!confirm('Вы уверены, что хотите их удалить ?')) {
        return
    }

    router.delete(route('admin.actions.quizzes.bulkDestroy'), {
        data: { ids: selectedQuizzes.value },
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            selectedQuizzes.value = []
            toast.success('Массовое удаление квизов успешно завершено.')
        },
        onError: (errors) => {
            console.error('Ошибка массового удаления:', errors)
            const errorKey = Object.keys(errors)[0]
            const errorMessage =
                errors[errorKey] || 'Произошла ошибка при удалении квизов.'
            toast.error(errorMessage)
        },
    })
}

/**
 * Обрабатывает выбор действия в селекте массовых действий.
 */
const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        selectedQuizzes.value = paginatedQuizzes.value.map((q) => q.id)
    } else if (action === 'deselectAll') {
        selectedQuizzes.value = []
    } else if (action === 'activate') {
        bulkToggleActivity(true)
    } else if (action === 'deactivate') {
        bulkToggleActivity(false)
    } else if (action === 'delete') {
        bulkDelete()
    }

    event.target.value = ''
}

/** Переключение активности одного квиза */
const toggleActivity = (quiz) => {
    const newActivity = !quiz.activity
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(
        route('admin.actions.quizzes.updateActivity', { quiz: quiz.id }),
        { activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                toast.success(`Квиз "${quiz.title}" ${actionText}.`)
            },
            onError: (errors) => {
                toast.error(
                    errors.activity || errors.general || `Ошибка изменения активности для "${quiz.title}".`
                )
            },
        }
    )
}

/** Ссылка для табов локалей */
const localeLink = (locale) => route('admin.quizzes.index', { locale })
</script>

<template>
    <AdminLayout :title="t('quizzes')">
        <template #header>
            <TitlePage>{{ t('quizzes') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <!-- Добавить квиз -->
                    <DefaultButton :href="route('admin.quizzes.create')">
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
                        {{ t('addQuiz') }}
                    </DefaultButton>

                    <BulkActionSelect
                        v-if="quizzesCount"
                        @change="handleBulkAction"
                    />
                </div>

                <!-- Локали + счётчик -->
                <div class="flex items-center justify-between mt-5">
                    <div
                        class="flex items-center justify-end space-x-2 px-3 py-1
                               border-x border-t border-gray-400 rounded-t-lg
                               bg-gray-100 dark:bg-gray-900"
                    >
                        <span
                            class="text-sm font-medium text-slate-700 dark:text-slate-200"
                        >
                            {{ t('localization') }}:
                        </span>
                        <template
                            v-for="locale in availableLocales"
                            :key="locale"
                        >
                            <Link
                                :href="localeLink(locale)"
                                :class="[
                                    'px-3 py-1 text-sm font-medium rounded-sm',
                                    currentLocale === locale
                                        ? 'bg-blue-500 text-white'
                                        : 'bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-200 hover:bg-slate-300 dark:hover:bg-slate-600'
                                ]"
                                preserve-scroll
                                preserve-state
                            >
                                {{ locale.toUpperCase() }}
                            </Link>
                        </template>
                    </div>

                    <div class="flex items-center space-x-3">
                        <CountTable v-if="quizzesCount">
                            {{ quizzesCount }}
                        </CountTable>

                        <ToggleViewButton v-model:viewMode="viewMode" />
                    </div>
                </div>

                <SearchInput
                    v-if="quizzesCount"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <!-- Таблица / Карточки -->
                <QuizTable
                    v-if="viewMode === 'table'"
                    :quizzes="paginatedQuizzes"
                    :selected-quizzes="selectedQuizzes"
                    @toggle-activity="toggleActivity"
                    @toggle-left="toggleLeft"
                    @toggle-main="toggleMain"
                    @toggle-right="toggleRight"
                    @delete="confirmDelete"
                    @clone="cloneQuiz"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectQuiz"
                    @toggle-all="toggleAll"
                />

                <QuizCardGrid
                    v-else
                    :quizzes="paginatedQuizzes"
                    :selected-quizzes="selectedQuizzes"
                    @toggle-activity="toggleActivity"
                    @toggle-left="toggleLeft"
                    @toggle-main="toggleMain"
                    @toggle-right="toggleRight"
                    @delete="confirmDelete"
                    @clone="cloneQuiz"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectQuiz"
                    @toggle-all="toggleAll"
                />

                <div
                    class="flex justify-between items-center flex-col md:flex-row my-1"
                    v-if="quizzesCount"
                >
                    <ItemsPerPageSelect
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredQuizzes.length"
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

        <DangerModal
            :show="showConfirmDeleteModal"
            @close="closeModal"
            :onCancel="closeModal"
            :onConfirm="deleteQuiz"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
