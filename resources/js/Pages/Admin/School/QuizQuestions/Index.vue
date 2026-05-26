<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список вопросов квизов школы
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

import BulkActionSelect from '@/Components/Admin/School/QuizQuestion/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/School/QuizQuestion/Sort/SortSelect.vue'
import QuizQuestionTable from '@/Components/Admin/School/QuizQuestion/Table/QuizQuestionTable.vue'
import QuizQuestionCardGrid from '@/Components/Admin/School/QuizQuestion/View/QuizQuestionCardGrid.vue'

// Локализация и уведомления
const { t } = useI18n()
const toast = useToast()

// Props из контроллера
const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },

    questions: { type: Array, default: () => [] },
    questionsCount: { type: Number, default: 0 },

    adminSchoolQuizQuestionsPerPage: { type: Number, default: 10 },
    adminSchoolQuizQuestionsDefaultSort: { type: String, default: 'idDesc' },

    filters: { type: Object, default: () => ({}) },

    quizzes: { type: Array, default: () => [] },
    currentQuizId: { type: Number, default: null },
})

// Режим отображения
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')

// Сохраняем режим отображения
watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode', val)
})

// Локальная копия вопросов
const localQuestions = ref([])

// Синхронизация локального списка вопросов
watch(
    () => props.questions,
    (newVal) => {
        localQuestions.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

// Выбранный квиз в фильтре
const selectedQuizId = ref(props.currentQuizId ?? props.filters?.school_quiz_id ?? null)

// Опции квизов для фильтра
const quizOptions = computed(() => props.quizzes || [])

// Подпись квиза в select
const quizOptionLabel = (quiz) => {
    if (!quiz) return ''

    const idPart = `[ID: ${quiz.id}]`
    const titlePart = quiz.title || quiz.slug || `#${quiz.id}`

    const context = [
        quiz.lesson?.title ? `Урок: ${quiz.lesson.title}` : null,
        quiz.module?.title ? `Модуль: ${quiz.module.title}` : null,
        quiz.course?.title ? `Курс: ${quiz.course.title}` : null,
    ].filter(Boolean).join(' / ')

    return context
        ? `${idPart} ${titlePart} — ${context}`
        : `${idPart} ${titlePart}`
}

// Смена квиза в фильтре
const handleQuizFilterChange = () => {
    const data = {}

    if (selectedQuizId.value) {
        data.school_quiz_id = selectedQuizId.value
    }

    router.get(route('admin.schoolQuizQuestions.index'), data, {
        preserveScroll: true,
        preserveState: true,
    })
}

// Количество элементов на странице
const itemsPerPage = ref(props.adminSchoolQuizQuestionsPerPage || 10)

// Сохранение количества элементов
watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountQuizQuestions'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

// Параметр сортировки
const sortParam = ref(props.adminSchoolQuizQuestionsDefaultSort || 'idDesc')

// Сохранение параметра сортировки
watch(sortParam, (newVal) => {
    router.put(route('admin.settings.updateAdminSortQuizQuestions'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info('Сортировка успешно изменена'),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления сортировки.'),
    })
})

// Модальное окно удаления
const showConfirmDeleteModal = ref(false)
const questionToDeleteId = ref(null)
const questionToDeleteTitle = ref('')

// Открытие модального окна удаления
const confirmDelete = (questionOrId, title = null) => {
    if (typeof questionOrId === 'object') {
        questionToDeleteId.value = questionOrId.id
        questionToDeleteTitle.value =
            title ||
            questionOrId.question_text ||
            `ID: ${questionOrId.id}`
    } else {
        questionToDeleteId.value = questionOrId
        questionToDeleteTitle.value = title || `ID: ${questionOrId}`
    }

    showConfirmDeleteModal.value = true
}

// Закрытие модального окна
const closeModal = () => {
    showConfirmDeleteModal.value = false
    questionToDeleteId.value = null
    questionToDeleteTitle.value = ''
}

// Удаление вопроса
const deleteQuestion = () => {
    if (questionToDeleteId.value === null) return

    const idToDelete = questionToDeleteId.value
    const titleToDelete = questionToDeleteTitle.value

    router.delete(route('admin.schoolQuizQuestions.destroy', {
        schoolQuizQuestion: idToDelete,
    }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            toast.success(`Вопрос "${titleToDelete || 'ID: ' + idToDelete}" удалён.`)
        },
        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            const errorMsg = errors.general || errors[errorKey] || 'Произошла ошибка при удалении.'

            toast.error(`${errorMsg} (Вопрос: ${titleToDelete || 'ID: ' + idToDelete})`)
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

// Сортировка вопросов
const sortQuestions = (items) => {
    const list = (items || []).slice()

    if (sortParam.value === 'idAsc') {
        return list.sort((a, b) => (a.id ?? 0) - (b.id ?? 0))
    }

    if (sortParam.value === 'idDesc') {
        return list.sort((a, b) => (b.id ?? 0) - (a.id ?? 0))
    }

    if (sortParam.value === 'sortAsc') {
        return list.sort((a, b) => (a.sort ?? 0) - (b.sort ?? 0))
    }

    if (sortParam.value === 'sortDesc') {
        return list.sort((a, b) => (b.sort ?? 0) - (a.sort ?? 0))
    }

    if (sortParam.value === 'questionTextAsc') {
        return list.sort((a, b) =>
            normalize(a.question_text).localeCompare(normalize(b.question_text))
        )
    }

    if (sortParam.value === 'questionTextDesc') {
        return list.sort((a, b) =>
            normalize(b.question_text).localeCompare(normalize(a.question_text))
        )
    }

    if (sortParam.value === 'pointsAsc') {
        return list.sort((a, b) => (a.points ?? 0) - (b.points ?? 0))
    }

    if (sortParam.value === 'pointsDesc') {
        return list.sort((a, b) => (b.points ?? 0) - (a.points ?? 0))
    }

    if (sortParam.value === 'answersCountAsc') {
        return list.sort((a, b) => (a.answers_count ?? 0) - (b.answers_count ?? 0))
    }

    if (sortParam.value === 'answersCountDesc') {
        return list.sort((a, b) => (b.answers_count ?? 0) - (a.answers_count ?? 0))
    }

    if (sortParam.value === 'activity') return list.filter(item => !!item.activity)
    if (sortParam.value === 'inactive') return list.filter(item => !item.activity)

    if (sortParam.value === 'singleChoice') {
        return list.filter(item => item.question_type === 'single_choice')
    }

    if (sortParam.value === 'multipleChoice') {
        return list.filter(item => item.question_type === 'multiple_choice')
    }

    if (sortParam.value === 'trueFalse') {
        return list.filter(item => item.question_type === 'true_false')
    }

    if (sortParam.value === 'openText') {
        return list.filter(item => item.question_type === 'open_text')
    }

    if (sortParam.value === 'quizTitleAsc') {
        return list.sort((a, b) =>
            normalize(a.quiz?.title).localeCompare(normalize(b.quiz?.title))
        )
    }

    if (sortParam.value === 'quizTitleDesc') {
        return list.sort((a, b) =>
            normalize(b.quiz?.title).localeCompare(normalize(a.quiz?.title))
        )
    }

    return list
}

// Отфильтрованные вопросы
const filteredQuestions = computed(() => {
    let filtered = localQuestions.value || []
    const q = normalize(searchQuery.value)

    if (selectedQuizId.value) {
        filtered = filtered.filter(question =>
            Number(question.school_quiz_id) === Number(selectedQuizId.value)
        )
    }

    if (!q) {
        return sortQuestions(filtered)
    }

    filtered = filtered.filter((question) => {
        const values = [
            question.question_text,
            question.explanation,
            question.question_type,
            question.quiz?.title,
            question.quiz?.slug,
        ]

        return values.some(value => normalize(value).includes(q))
    })

    return sortQuestions(filtered)
})

// Вопросы текущей страницы
const paginatedQuestions = computed(() => {
    const per = Number(itemsPerPage.value || 20)
    const start = (currentPage.value - 1) * per

    return filteredQuestions.value.slice(start, start + per)
})

// Сброс страницы при поиске, фильтре и изменении лимита
watch([itemsPerPage, searchQuery, selectedQuizId], () => {
    currentPage.value = 1
})

// Локальное обновление вопроса
const patchQuestion = (questionId, payload) => {
    const index = localQuestions.value.findIndex(question => question.id === questionId)

    if (index !== -1) {
        localQuestions.value[index] = {
            ...localQuestions.value[index],
            ...payload,
        }
    }
}

// Выбранные вопросы
const selectedQuestions = ref([])

// Выбрать / снять выбор со всех вопросов
const toggleAll = ({ ids, checked }) => {
    selectedQuestions.value = checked ? [...ids] : []
}

// Выбор одного вопроса
const toggleSelectQuestion = (id) => {
    const index = selectedQuestions.value.indexOf(id)

    if (index > -1) {
        selectedQuestions.value.splice(index, 1)
    } else {
        selectedQuestions.value.push(id)
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

    router.put(route('admin.actions.schoolQuizQuestions.updateSortBulk'), { items }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.success('Порядок вопросов успешно обновлён.'),
        onError: (errors) => {
            console.error('Ошибка обновления сортировки вопросов:', errors)
            toast.error(errors?.message || errors?.general || 'Не удалось обновить порядок вопросов.')

            router.reload({
                only: ['questions'],
                preserveScroll: true,
            })
        },
    })
}

// Массовое обновление активности
const bulkToggleActivity = (newActivity) => {
    if (!selectedQuestions.value.length) {
        toast.warning('Выберите вопросы для активации/деактивации.')
        return
    }

    const idsToUpdate = [...selectedQuestions.value]

    router.put(route('admin.actions.schoolQuizQuestions.bulkUpdateActivity'), {
        ids: idsToUpdate,
        activity: newActivity,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            idsToUpdate.forEach(id => patchQuestion(id, { activity: newActivity }))
            selectedQuestions.value = []
            toast.success('Активность выбранных вопросов обновлена.')
        },
        onError: (errors) => {
            toast.error(errors?.ids || errors?.activity || errors?.general || 'Ошибка массового обновления активности.')
        },
    })
}

// Массовое удаление вопросов
const bulkDelete = () => {
    if (!selectedQuestions.value.length) {
        toast.warning('Выберите хотя бы один вопрос для удаления.')
        return
    }

    if (!confirm('Вы уверены, что хотите удалить выбранные вопросы?')) {
        return
    }

    router.delete(route('admin.actions.schoolQuizQuestions.bulkDestroy'), {
        data: {
            ids: selectedQuestions.value,
            ...(selectedQuizId.value ? { school_quiz_id: selectedQuizId.value } : {}),
        },
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            selectedQuestions.value = []
            toast.success('Массовое удаление вопросов успешно завершено.')
        },
        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            const errorMessage = errors[errorKey] || 'Произошла ошибка при удалении вопросов.'

            toast.error(errorMessage)
        },
    })
}

// Обработка массовых действий
const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        selectedQuestions.value = paginatedQuestions.value.map(question => question.id)
    } else if (action === 'deselectAll') {
        selectedQuestions.value = []
    } else if (action === 'activate') {
        bulkToggleActivity(true)
    } else if (action === 'deactivate') {
        bulkToggleActivity(false)
    } else if (action === 'delete') {
        bulkDelete()
    }

    event.target.value = ''
}

// Переключение активности вопроса
const toggleActivity = (question) => {
    const newActivity = !question.activity
    const questionTitle = question.question_text || `ID: ${question.id}`
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(route('admin.actions.schoolQuizQuestions.updateActivity', {
        schoolQuizQuestion: question.id,
    }), {
        activity: newActivity,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            patchQuestion(question.id, { activity: newActivity })
            question.activity = newActivity
            toast.success(`Вопрос "${questionTitle}" ${actionText}.`)
        },
        onError: (errors) => {
            toast.error(errors?.activity || errors?.general || `Ошибка изменения активности для вопроса "${questionTitle}".`)
        },
    })
}

// Клонирование вопроса
const cloneQuestion = (question) => {
    router.post(route('admin.actions.schoolQuizQuestions.clone', {
        schoolQuizQuestion: question.id,
    }), {}, {
        preserveScroll: true,
        onSuccess: () => toast.success('Вопрос успешно клонирован.'),
        onError: () => toast.error('Ошибка при клонировании вопроса.'),
    })
}
</script>

<template>
    <AdminLayout :title="t('quizQuestions')">
        <template #header>
            <TitlePage>{{ t('quizQuestions') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-3">
                    <DefaultButton
                        :href="route('admin.schoolQuizQuestions.create', {
                            ...(selectedQuizId ? { school_quiz_id: selectedQuizId } : {}),
                        })"
                    >
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0"
                                 viewBox="0 0 16 16">
                                <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"></path>
                            </svg>
                        </template>
                        {{ t('addQuizQuestion') }}
                    </DefaultButton>
                </div>

                <SearchInput
                    v-if="questionsCount"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <div
                    v-if="quizzes.length"
                    class="flex items-center justify-center gap-2"
                >
                    <label
                        for="school_quiz_id"
                        class="text-sm font-semibold text-slate-700 dark:text-slate-200"
                    >
                        {{ t('quiz') }}
                    </label>

                    <select
                        id="school_quiz_id"
                        v-model.number="selectedQuizId"
                        @change="handleQuizFilterChange"
                        class="w-full rounded-sm border-slate-300
                               dark:border-slate-600 dark:bg-slate-800
                               dark:text-slate-100 text-xs font-semibold px-3 py-1"
                    >
                        <option :value="null">
                            {{ t('allQuizzes') }}
                        </option>

                        <option
                            v-for="quiz in quizOptions"
                            :key="quiz.id"
                            :value="quiz.id"
                        >
                            {{ quizOptionLabel(quiz) }}
                        </option>
                    </select>
                </div>

                <div
                    v-if="questionsCount"
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
                    v-if="questionsCount"
                    class="flex flex-col lg:flex-row items-center justify-between gap-3"
                >
                    <CountTable>{{ questionsCount }}</CountTable>

                    <BulkActionSelect @change="handleBulkAction" />

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div
                    v-if="questionsCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredQuestions.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                </div>

                <QuizQuestionTable
                    v-if="viewMode === 'table'"
                    :questions="paginatedQuestions"
                    :selected-questions="selectedQuestions"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectQuestion"
                    @toggle-all="toggleAll"
                    @clone="cloneQuestion"
                />

                <QuizQuestionCardGrid
                    v-else
                    :questions="paginatedQuestions"
                    :selected-questions="selectedQuestions"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectQuestion"
                    @toggle-all="toggleAll"
                    @clone="cloneQuestion"
                />

                <div
                    v-if="questionsCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredQuestions.length"
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
            :onConfirm="deleteQuestion"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
