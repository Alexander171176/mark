<script setup>
/**
 * @version PulsarCMS 1.0
 * @autor Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список ответов вопросов викторин (SchoolQuizAnswer)
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

import BulkActionSelect from '@/Components/Admin/School/QuizAnswer/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/School/QuizAnswer/Sort/SortSelect.vue'
import QuizAnswerTable from '@/Components/Admin/School/QuizAnswer/Table/QuizAnswerTable.vue'
import QuizAnswerCardGrid from '@/Components/Admin/School/QuizAnswer/View/QuizAnswerCardGrid.vue'

// Локализация
const { t } = useI18n()

// Toast уведомления
const toast = useToast()

// Props страницы списка ответов
const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },

    answers: { type: Array, default: () => [] },
    answersCount: { type: Number, default: 0 },

    adminSchoolQuizAnswersPerPage: { type: Number, default: 10 },
    adminSchoolQuizAnswersDefaultSort: { type: String, default: 'idDesc' },

    filters: { type: Object, default: () => ({}) },

    quizzes: { type: Array, default: () => [] },
    questions: { type: Array, default: () => [] },

    currentQuizId: { type: Number, default: null },
    currentQuestionId: { type: Number, default: null },
})

// Режим отображения (таблица / карточки)
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')

// Сохранение режима отображения
watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode', val)
})

// Локальная копия ответов
const localAnswers = ref([])

// Синхронизация локальных данных
watch(
    () => props.answers,
    (newVal) => {
        localAnswers.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

// Выбранный квиз для фильтра
const selectedQuizId = ref(props.currentQuizId ?? props.filters?.school_quiz_id ?? null)

// Выбранный вопрос для фильтра
const selectedQuestionId = ref(
    props.currentQuestionId ?? props.filters?.school_quiz_question_id ?? null)

// Список квизов
const quizOptions = computed(() => props.quizzes || [])

// Список вопросов
const questionOptions = computed(() => props.questions || [])

// Очистка HTML тегов
const stripHtml = (html = '') => {
    return (html || '')
        .replace(/<\/p>/gi, ' ')
        .replace(/<br\s*\/?>/gi, ' ')
        .replace(/<[^>]+>/g, '')
        .replace(/&nbsp;/gi, ' ')
        .replace(/\s+/g, ' ')
        .trim()
}

// Сокращение текста
const shortText = (html, limit = 100) => {
    const clean = stripHtml(html)

    return clean.length > limit ? clean.slice(0, limit) + '…' : clean
}

// Подпись квиза в select
const quizOptionLabel = (quiz) => {
    if (!quiz) return ''

    const idPart = `[ID: ${quiz.id}]`
    const titlePart = quiz.title || quiz.slug || `#${quiz.id}`

    const context = [
        quiz.course?.title ? `Курс: ${quiz.course.title}` : null,
        quiz.module?.title ? `Модуль: ${quiz.module.title}` : null,
        quiz.lesson?.title ? `Урок: ${quiz.lesson.title}` : null,
    ].filter(Boolean).join(' / ')

    return context
        ? `${idPart} ${titlePart} — ${context}`
        : `${idPart} ${titlePart}`
}

// Подпись вопроса в select
const questionOptionLabel = (question) => {
    if (!question) return ''

    const idPart = `[ID: ${question.id}]`
    const textPart = question.question_text
        ? shortText(question.question_text, 120)
        : `#${question.id}`
    const quizPart = question.school_quiz_id
        ? `Quiz ID: ${question.school_quiz_id}`
        : null

    return quizPart
        ? `${idPart} ${textPart} — ${quizPart}`
        : `${idPart} ${textPart}`
}

// Фильтр по квизу
const handleQuizFilterChange = () => {
    const data = {}

    if (selectedQuizId.value) {
        data.school_quiz_id = selectedQuizId.value
    }

    selectedQuestionId.value = null

    router.get(route('admin.schoolQuizAnswers.index'), data, {
        preserveScroll: true,
        preserveState: true,
    })
}

// Фильтр по вопросу
const handleQuestionFilterChange = () => {
    const data = {}

    if (selectedQuizId.value) {
        data.school_quiz_id = selectedQuizId.value
    }

    if (selectedQuestionId.value) {
        data.school_quiz_question_id = selectedQuestionId.value
    }

    router.get(route('admin.schoolQuizAnswers.index'), data, {
        preserveScroll: true,
        preserveState: true,
    })
}

// Количество элементов на странице
const itemsPerPage = ref(props.adminSchoolQuizAnswersPerPage || 10)

// Обновление количества элементов
watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountQuizAnswers'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

// Текущая сортировка
const sortParam = ref(props.adminSchoolQuizAnswersDefaultSort || 'idDesc')

// Обновление сортировки
watch(sortParam, (newVal) => {
    router.put(route('admin.settings.updateAdminSortQuizAnswers'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info('Сортировка успешно изменена'),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления сортировки.'),
    })
})

// Модальное окно удаления
const showConfirmDeleteModal = ref(false)

// ID удаляемого ответа
const answerToDeleteId = ref(null)

// Заголовок удаляемого ответа
const answerToDeleteTitle = ref('')

// Подтверждение удаления
const confirmDelete = (answerOrId, title = null) => {
    if (typeof answerOrId === 'object') {
        answerToDeleteId.value = answerOrId.id
        answerToDeleteTitle.value = title || stripHtml(answerOrId.text) || `ID: ${answerOrId.id}`
    } else {
        answerToDeleteId.value = answerOrId
        answerToDeleteTitle.value = title || `ID: ${answerOrId}`
    }

    showConfirmDeleteModal.value = true
}

// Закрытие модального окна
const closeModal = () => {
    showConfirmDeleteModal.value = false
    answerToDeleteId.value = null
    answerToDeleteTitle.value = ''
}

// Удаление ответа
const deleteAnswer = () => {
    if (answerToDeleteId.value === null) return

    const idToDelete = answerToDeleteId.value
    const titleToDelete = answerToDeleteTitle.value

    router.delete(route('admin.schoolQuizAnswers.destroy', {
        schoolQuizAnswer: idToDelete,
    }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            toast.success(`Ответ "${titleToDelete || 'ID: ' + idToDelete}" удалён.`)
        },
        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            const errorMsg = errors.general || errors[errorKey] || 'Произошла ошибка при удалении.'

            toast.error(`${errorMsg} (Ответ: ${titleToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => closeModal(),
    })
}

// Текущая страница пагинации
const currentPage = ref(1)

// Поисковый запрос
const searchQuery = ref('')

// Нормализация текста
const normalize = (value) => stripHtml(value ?? '').toString().trim().toLowerCase()

// Сортировка ответов
const sortAnswers = (items) => {
    const list = (items || []).slice()

    if (sortParam.value === 'idAsc') return list.sort((a, b) => (a.id ?? 0) - (b.id ?? 0))
    if (sortParam.value === 'idDesc') return list.sort((a, b) => (b.id ?? 0) - (a.id ?? 0))

    if (sortParam.value === 'sortAsc') return list.sort((a, b) => (a.sort ?? 0) - (b.sort ?? 0))
    if (sortParam.value === 'sortDesc') return list.sort((a, b) => (b.sort ?? 0) - (a.sort ?? 0))

    if (sortParam.value === 'textAsc') {
        return list.sort((a, b) => normalize(a.text).localeCompare(normalize(b.text)))
    }

    if (sortParam.value === 'textDesc') {
        return list.sort((a, b) => normalize(b.text).localeCompare(normalize(a.text)))
    }

    if (sortParam.value === 'weightAsc') return list.sort((a, b) => (a.weight ?? 0) - (b.weight ?? 0))
    if (sortParam.value === 'weightDesc') return list.sort((a, b) => (b.weight ?? 0) - (a.weight ?? 0))

    if (sortParam.value === 'activity') return list.filter(item => !!item.activity)
    if (sortParam.value === 'inactive') return list.filter(item => !item.activity)

    if (sortParam.value === 'correct') return list.filter(item => !!item.is_correct)
    if (sortParam.value === 'incorrect') return list.filter(item => !item.is_correct)

    if (sortParam.value === 'quizTitleAsc') {
        return list.sort((a, b) => normalize(a.quiz?.title).localeCompare(normalize(b.quiz?.title)))
    }

    if (sortParam.value === 'quizTitleDesc') {
        return list.sort((a, b) => normalize(b.quiz?.title).localeCompare(normalize(a.quiz?.title)))
    }

    if (sortParam.value === 'questionTextAsc') {
        return list.sort((a, b) => normalize(a.question?.question_text).localeCompare(normalize(b.question?.question_text)))
    }

    if (sortParam.value === 'questionTextDesc') {
        return list.sort((a, b) => normalize(b.question?.question_text).localeCompare(normalize(a.question?.question_text)))
    }

    return list
}

// Отфильтрованные ответы
const filteredAnswers = computed(() => {
    let filtered = localAnswers.value || []
    const q = normalize(searchQuery.value)

    if (selectedQuizId.value) {
        filtered = filtered.filter(answer =>
            Number(answer.school_quiz_id) === Number(selectedQuizId.value)
        )
    }

    if (selectedQuestionId.value) {
        filtered = filtered.filter(answer =>
            Number(answer.school_quiz_question_id) === Number(selectedQuestionId.value)
        )
    }

    if (!q) {
        return sortAnswers(filtered)
    }

    filtered = filtered.filter((answer) => {
        const values = [
            answer.text,
            answer.explanation,
            answer.quiz?.title,
            answer.quiz?.slug,
            answer.question?.question_text,
            answer.question?.explanation,
        ]

        return values.some(value => normalize(value).includes(q))
    })

    return sortAnswers(filtered)
})

// Ответы текущей страницы
const paginatedAnswers = computed(() => {
    const per = Number(itemsPerPage.value || 20)
    const start = (currentPage.value - 1) * per

    return filteredAnswers.value.slice(start, start + per)
})

// Сброс страницы при поиске и изменении лимита и условий
watch([itemsPerPage, searchQuery, selectedQuizId, selectedQuestionId], () => {
    currentPage.value = 1
})

// Локальное обновление ответа
const patchAnswer = (answerId, payload) => {
    const index = localAnswers.value.findIndex(answer => answer.id === answerId)

    if (index !== -1) {
        localAnswers.value[index] = {
            ...localAnswers.value[index],
            ...payload,
        }
    }
}

// Выбранные ответы
const selectedAnswers = ref([])

// Выбрать / снять выбор со всех ответов
const toggleAll = ({ ids, checked }) => {
    selectedAnswers.value = checked ? [...ids] : []
}

// Выбор одного ответа
const toggleSelectAnswer = (id) => {
    const index = selectedAnswers.value.indexOf(id)

    if (index > -1) {
        selectedAnswers.value.splice(index, 1)
    } else {
        selectedAnswers.value.push(id)
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

    router.put(route('admin.actions.schoolQuizAnswers.updateSortBulk'), { items }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.success('Порядок ответов успешно обновлён.'),
        onError: (errors) => {
            console.error('Ошибка обновления сортировки ответов:', errors)
            toast.error(errors?.message || errors?.general || 'Не удалось обновить порядок ответов.')

            router.reload({
                only: ['answers'],
                preserveScroll: true,
            })
        },
    })
}

// Массовое обновление активности
const bulkToggleActivity = (newActivity) => {
    if (!selectedAnswers.value.length) {
        toast.warning('Выберите ответы для активации/деактивации.')
        return
    }

    const idsToUpdate = [...selectedAnswers.value]

    router.put(route('admin.actions.schoolQuizAnswers.bulkUpdateActivity'), {
        ids: idsToUpdate,
        activity: newActivity,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            idsToUpdate.forEach(id => patchAnswer(id, { activity: newActivity }))
            selectedAnswers.value = []
            toast.success('Активность выбранных ответов обновлена.')
        },
        onError: (errors) => {
            toast.error(errors?.ids || errors?.activity || errors?.general || 'Ошибка массового обновления активности.')
        },
    })
}

// Массовое удаление
const bulkDelete = () => {
    if (!selectedAnswers.value.length) {
        toast.warning('Выберите ответы для удаления.')
        return
    }

    if (!confirm('Вы уверены, что хотите удалить выбранные ответы?')) return

    router.delete(route('admin.actions.schoolQuizAnswers.bulkDestroy'), {
        data: {
            ids: selectedAnswers.value,
            ...(selectedQuizId.value ? { school_quiz_id: selectedQuizId.value } : {}),
            ...(selectedQuestionId.value ? { school_quiz_question_id: selectedQuestionId.value } : {}),
        },
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            selectedAnswers.value = []
            toast.success('Выбранные ответы успешно удалены.')
        },
        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            toast.error(errors[errorKey] || 'Ошибка массового удаления ответов.')
        },
    })
}

// Обработка массовых действий
const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        selectedAnswers.value = paginatedAnswers.value.map(answer => answer.id)
    } else if (action === 'deselectAll') {
        selectedAnswers.value = []
    } else if (action === 'activate') {
        bulkToggleActivity(true)
    } else if (action === 'deactivate') {
        bulkToggleActivity(false)
    } else if (action === 'delete') {
        bulkDelete()
    }

    event.target.value = ''
}

// Переключение активности ответов
const toggleActivity = (answer) => {
    const newActivity = !answer.activity
    const answerTitle = stripHtml(answer.text) || `ID: ${answer.id}`
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(route('admin.actions.schoolQuizAnswers.updateActivity', {
        schoolQuizAnswer: answer.id,
    }), {
        activity: newActivity,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            patchAnswer(answer.id, { activity: newActivity })
            answer.activity = newActivity
            toast.success(`Ответ "${answerTitle}" ${actionText}.`)
        },
        onError: (errors) => {
            toast.error(errors?.activity || errors?.general || `Ошибка изменения активности для ответа "${answerTitle}".`)
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('quizAnswers')">
        <template #header>
            <TitlePage>{{ t('quizAnswers') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500
                       dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-3">
                    <DefaultButton :href="route('admin.schoolQuizAnswers.create')">
                        <template #icon>
                        <svg class="w-4 h-4 fill-current opacity-50 shrink-0"
                             viewBox="0 0 16 16">
                            <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"></path>
                        </svg>
                    </template>
                        {{ t('addQuizAnswer') }}
                    </DefaultButton>
                </div>

                <SearchInput
                    v-if="answersCount"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <div class="mb-3 flex flex-col gap-3">
                    <select
                        v-model.number="selectedQuizId"
                        @change="handleQuizFilterChange"
                        class="px-2 py-1 text-xs bg-slate-200 dark:bg-cyan-900">
                        <option :value="null">{{ t('allQuizzes') }}</option>
                        <option
                            v-for="quiz in quizOptions"
                            :key="quiz.id"
                            :value="quiz.id">
                            {{ quizOptionLabel(quiz) }}
                        </option>
                    </select>

                    <select
                        v-model.number="selectedQuestionId"
                        @change="handleQuestionFilterChange"
                        class="px-2 py-1 text-xs bg-slate-200 dark:bg-cyan-900">
                        <option :value="null">{{ t('allQuestions') }}</option>
                        <option
                            v-for="question in questionOptions"
                            :key="question.id"
                            :value="question.id">
                            {{ questionOptionLabel(question) }}
                        </option>
                    </select>
                </div>

                <div v-if="answersCount"
                     class="flex justify-between items-center flex-col md:flex-row my-3">
                    <ItemsPerPageSelect
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="val => sortParam = val"
                    />
                </div>

                <div v-if="answersCount"
                     class="flex flex-col lg:flex-row items-center justify-between gap-3">
                    <CountTable>{{ answersCount }}</CountTable>
                    <BulkActionSelect @change="handleBulkAction" />
                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div v-if="answersCount"
                     class="flex justify-center items-center flex-col md:flex-row mt-3">
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredAnswers.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                </div>

                <QuizAnswerTable
                    v-if="viewMode === 'table'"
                    :answers="paginatedAnswers"
                    :selected-answers="selectedAnswers"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectAnswer"
                    @toggle-all="toggleAll"
                />

                <QuizAnswerCardGrid
                    v-else
                    :answers="paginatedAnswers"
                    :selected-answers="selectedAnswers"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectAnswer"
                    @toggle-all="toggleAll"
                />

                <div v-if="answersCount"
                     class="flex justify-center items-center flex-col md:flex-row mt-3">
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredAnswers.length"
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
            :onConfirm="deleteAnswer"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
