<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список ответов на конкретные вопросы викторин
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

import BulkActionSelect from '@/Components/Admin/School/SchoolQuizAttemptItem/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/School/SchoolQuizAttemptItem/Sort/SortSelect.vue'
import QuizAttemptItemTable from '@/Components/Admin/School/SchoolQuizAttemptItem/Table/QuizAttemptItemTable.vue'
import QuizAttemptItemCardGrid from '@/Components/Admin/School/SchoolQuizAttemptItem/View/QuizAttemptItemCardGrid.vue'

// Локализация и уведомления
const { t } = useI18n()
const toast = useToast()

// Пропсы страницы списка ответов попыток
const props = defineProps({
    items: { type: Array, default: () => [] },
    itemsCount: { type: Number, default: 0 },
    filters: { type: Object, default: () => ({}) },

    adminSchoolQuizAttemptItemsPerPage: { type: Number, default: 20 },
    adminSchoolQuizAttemptItemsDefaultSort: { type: String, default: 'idDesc' },

    attempts: { type: Array, default: () => [] },
    questions: { type: Array, default: () => [] },
    answers: { type: Array, default: () => [] },
})

// Режим отображения таблица/карточки
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')

// Сохранение режима отображения в localStorage
watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode', val)
})

// Количество элементов на странице
const itemsPerPage = ref(props.adminSchoolQuizAttemptItemsPerPage ?? 20)

// Обновление настройки количества элементов
watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountQuizAttemptItems'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors?.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

// Параметр сортировки
const sortParam = ref(props.adminSchoolQuizAttemptItemsDefaultSort ?? 'idDesc')

// Обновление настройки сортировки
watch(sortParam, (newVal) => {
    router.put(route('admin.settings.updateAdminSortQuizAttemptItems'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info('Сортировка успешно изменена'),
        onError: (errors) => {
            const firstError = errors ? Object.values(errors)[0] : null
            toast.error(firstError || 'Ошибка обновления параметра сортировки.')
        },
    })
})

// Текущая страница пагинации
const currentPage = ref(1)

// Поисковый запрос
const searchQuery = ref('')

// Модальное окно удаления
const showConfirmDeleteModal = ref(false)

// Элемент для удаления
const itemToDelete = ref(null)

// Очистка HTML из текста
const stripHtml = (value = '') => {
    if (value === null || typeof value === 'undefined') {
        return ''
    }

    const html = typeof value === 'string'
        ? value
        : JSON.stringify(value)

    return html
        .replace(/<\/p>/gi, ' ')
        .replace(/<br\s*\/?>/gi, ' ')
        .replace(/<[^>]+>/g, '')
        .replace(/&nbsp;/gi, ' ')
        .replace(/&amp;/gi, '&')
        .replace(/&quot;/gi, '"')
        .replace(/&#039;/gi, "'")
        .replace(/&lt;/gi, '<')
        .replace(/&gt;/gi, '>')
        .replace(/\s+/g, ' ')
        .trim()
}

// Нормализация строк для поиска и сортировки
const normalize = (value) => {
    return stripHtml(value)
        .toString()
        .trim()
        .toLowerCase()
}

// Преобразование даты во временную метку
const toTime = (value) => {
    if (!value) return 0

    const time = new Date(value).getTime()

    return Number.isNaN(time) ? 0 : time
}

// Нормализация чисел
const normalizeNum = (value) => {
    const number = Number(value)

    return Number.isFinite(number) ? number : 0
}

// Заголовок элемента для удаления
const itemTitle = (item) => {
    if (!item) return ''

    return item.question?.question_text
        ? stripHtml(item.question.question_text)
        : `ID: ${item.id}`
}

// Открытие модального окна удаления
const confirmDelete = (item) => {
    itemToDelete.value = item
    showConfirmDeleteModal.value = true
}

// Закрытие модального окна удаления
const closeModal = () => {
    showConfirmDeleteModal.value = false
    itemToDelete.value = null
}

// Удаление одного ответа попытки
const deleteItem = () => {
    if (!itemToDelete.value?.id) return

    const idToDelete = itemToDelete.value.id
    const titleToDelete = itemTitle(itemToDelete.value)

    router.delete(route('admin.schoolQuizAttemptItems.destroy', {
        schoolQuizAttemptItem: idToDelete,
    }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            toast.success(`Ответ "${titleToDelete || 'ID: ' + idToDelete}" удалён.`)
        },
        onError: (errors) => {
            const firstKey = Object.keys(errors || {})[0]
            const errorMsg = errors?.general || errors?.[firstKey] || 'Произошла ошибка при удалении.'

            toast.error(`${errorMsg} (Ответ: ${titleToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => closeModal(),
    })
}

// Сортировка ответов попыток
const sortItems = (items) => {
    const list = items.slice()

    switch (sortParam.value) {
        case 'idAsc':
            return list.sort((a, b) => a.id - b.id)

        case 'idDesc':
            return list.sort((a, b) => b.id - a.id)

        case 'attemptIdAsc':
            return list.sort((a, b) =>
                normalizeNum(a.school_quiz_attempt_id) - normalizeNum(b.school_quiz_attempt_id)
            )

        case 'attemptIdDesc':
            return list.sort((a, b) =>
                normalizeNum(b.school_quiz_attempt_id) - normalizeNum(a.school_quiz_attempt_id)
            )

        case 'questionIdAsc':
            return list.sort((a, b) =>
                normalizeNum(a.school_quiz_question_id) - normalizeNum(b.school_quiz_question_id)
            )

        case 'questionIdDesc':
            return list.sort((a, b) =>
                normalizeNum(b.school_quiz_question_id) - normalizeNum(a.school_quiz_question_id)
            )

        case 'scoreAsc':
            return list.sort((a, b) => normalizeNum(a.score) - normalizeNum(b.score))

        case 'scoreDesc':
            return list.sort((a, b) => normalizeNum(b.score) - normalizeNum(a.score))

        case 'maxScoreAsc':
            return list.sort((a, b) => normalizeNum(a.max_score) - normalizeNum(b.max_score))

        case 'maxScoreDesc':
            return list.sort((a, b) => normalizeNum(b.max_score) - normalizeNum(a.max_score))

        case 'createdAtAsc':
            return list.sort((a, b) => toTime(a.created_at) - toTime(b.created_at))

        case 'createdAtDesc':
            return list.sort((a, b) => toTime(b.created_at) - toTime(a.created_at))

        case 'correctFirst':
            return list.sort((a, b) => Number(!!b.is_correct) - Number(!!a.is_correct))

        case 'wrongFirst':
            return list.sort((a, b) => Number(!!a.is_correct) - Number(!!b.is_correct))

        case 'questionTextAsc':
            return list.sort((a, b) =>
                normalize(a.question?.question_text).localeCompare(normalize(b.question?.question_text))
            )

        case 'questionTextDesc':
            return list.sort((a, b) =>
                normalize(b.question?.question_text).localeCompare(normalize(a.question?.question_text))
            )

        default:
            return list
    }
}

// Фильтрация и поиск ответов попыток
const filteredItems = computed(() => {
    let filtered = Array.isArray(props.items) ? props.items : []

    if (searchQuery.value) {
        const q = normalize(searchQuery.value)

        filtered = filtered.filter((item) => {
            const values = [
                item.id,
                item.school_quiz_attempt_id,
                item.school_quiz_question_id,
                item.selected_answer_id,
                item.selected_answer_ids ? JSON.stringify(item.selected_answer_ids) : '',
                item.free_text_answer,
                item.reviewer_comment,
                item.score,
                item.max_score,

                item.question?.question_text,
                item.question?.explanation,
                item.question?.question_type,

                item.selected_answer?.text,
                item.selected_answer?.explanation,

                item.selected_answers ? JSON.stringify(item.selected_answers) : '',

                item.attempt?.status,
                item.attempt?.attempt_number,
                item.attempt?.user?.name,
                item.attempt?.user?.email,
                item.attempt?.quiz?.title,
                item.attempt?.quiz?.slug,
                item.attempt?.quiz?.id,
            ]

            return values.some(value => normalize(value).includes(q))
        })
    }

    return sortItems(filtered)
})

// Пагинация списка ответов
const paginatedItems = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value

    return filteredItems.value.slice(start, start + itemsPerPage.value)
})

// Общее количество страниц
const totalPages = computed(() => {
    if (!itemsPerPage.value) return 1

    return Math.ceil(filteredItems.value.length / itemsPerPage.value) || 1
})

// Корректировка страницы после фильтрации
watch([filteredItems, itemsPerPage], () => {
    if (currentPage.value > totalPages.value) {
        currentPage.value = totalPages.value
    }
})

// Выбранные элементы
const selectedItems = ref([])

// Выбор/снятие всех элементов
const toggleAll = ({ ids, checked }) => {
    selectedItems.value = checked ? [...ids] : []
}

// Выбор одного элемента
const toggleSelectItem = (id) => {
    const index = selectedItems.value.indexOf(id)

    if (index > -1) {
        selectedItems.value.splice(index, 1)
    } else {
        selectedItems.value.push(id)
    }
}

// Обновление локальной записи элемента
const patchItem = (itemId, payload) => {
    const item = props.items.find(item => item.id === itemId)

    if (item) {
        Object.assign(item, payload)
    }
}

// Массовое обновление правильности
const bulkUpdateCorrect = (isCorrect) => {
    if (!selectedItems.value.length) {
        toast.warning('Выберите ответы.')
        return
    }

    const idsToUpdate = [...selectedItems.value]

    router.put(route('admin.actions.schoolQuizAttemptItems.bulkUpdateCorrect'), {
        ids: idsToUpdate,
        is_correct: isCorrect,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            idsToUpdate.forEach(id => patchItem(id, { is_correct: isCorrect }))

            selectedItems.value = []

            toast.success('Правильность выбранных текстовых ответов обновлена.')
        },
        onError: (errors) => {
            toast.error(
                errors?.ids ||
                errors?.is_correct ||
                errors?.general ||
                'Не удалось массово обновить правильность.'
            )
        },
    })
}

// Массовое удаление ответов попыток
const bulkDestroy = () => {
    if (!selectedItems.value.length) {
        toast.warning('Выберите ответы для удаления.')
        return
    }

    if (!confirm('Вы уверены, что хотите удалить выбранные ответы?')) return

    router.delete(route('admin.actions.schoolQuizAttemptItems.bulkDestroy'), {
        data: {
            ids: selectedItems.value,
            ...(props.filters?.school_quiz_attempt_id
                ? { school_quiz_attempt_id: props.filters.school_quiz_attempt_id }
                : {}),
        },
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            selectedItems.value = []
            toast.success('Выбранные ответы успешно удалены.')
        },
        onError: (errors) => {
            const firstKey = Object.keys(errors || {})[0]
            toast.error(errors?.[firstKey] || 'Ошибка массового удаления ответов.')
        },
    })
}

// Обработка массовых действий
const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        selectedItems.value = paginatedItems.value.map(item => item.id)
    } else if (action === 'deselectAll') {
        selectedItems.value = []
    } else if (action === 'correct:1') {
        bulkUpdateCorrect(true)
    } else if (action === 'correct:0') {
        bulkUpdateCorrect(false)
    } else if (action === 'delete') {
        bulkDestroy()
    }

    event.target.value = ''
}
</script>

<template>
    <AdminLayout :title="t('quizAttemptItems')">
        <template #header>
            <TitlePage>{{ t('quizAttemptItems') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500
                       dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-3">
<!--                    <DefaultButton :href="route('admin.schoolQuizAttemptItems.create')">-->
<!--                        <template #icon>-->
<!--                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0"-->
<!--                                 viewBox="0 0 16 16">-->
<!--                                <path-->
<!--                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"-->
<!--                                />-->
<!--                            </svg>-->
<!--                        </template>-->
<!--                        {{ t('addQuizAttemptItem') }}-->
<!--                    </DefaultButton>-->
                </div>

                <SearchInput
                    v-if="itemsCount"
                    v-model="searchQuery"
                    :placeholder="t('search')"
                />

                <div
                    v-if="itemsCount"
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
                    v-if="itemsCount"
                    class="flex justify-between items-center flex-col md:flex-row my-3"
                >
                    <CountTable>{{ itemsCount }}</CountTable>

                    <BulkActionSelect @change="handleBulkAction" />

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div
                    v-if="itemsCount"
                    class="flex justify-center items-center flex-col md:flex-row mb-3"
                >
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredItems.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                </div>

                <QuizAttemptItemTable
                    v-if="viewMode === 'table'"
                    :items="paginatedItems"
                    :selected-items="selectedItems"
                    @toggle-select="toggleSelectItem"
                    @toggle-all="toggleAll"
                    @delete="confirmDelete"
                />

                <QuizAttemptItemCardGrid
                    v-else
                    :items="paginatedItems"
                    :selected-items="selectedItems"
                    @toggle-select="toggleSelectItem"
                    @toggle-all="toggleAll"
                    @delete="confirmDelete"
                />

                <div
                    v-if="itemsCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredItems.length"
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
            :onConfirm="deleteItem"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
