<script setup>
/**
 * @version PulsarCMS 1.0
 * @autor Александр Косолапов <kosolapov1976@gmail.com>
 * Список вариантов ответов квизов (QuizAnswer)
 */
import { defineProps, ref, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router, Link } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import SearchInput from '@/Components/Admin/Search/SearchInput.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import BulkActionSelect from '@/Components/Admin/QuizAnswer/Select/BulkActionSelect.vue'
import CountTable from '@/Components/Admin/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/Select/ItemsPerPageSelect.vue'
import QuizAnswerTable from '@/Components/Admin/QuizAnswer/Table/QuizAnswerTable.vue'
import QuizAnswerCardGrid from '@/Components/Admin/QuizAnswer/View/QuizAnswerCardGrid.vue'
import DangerModal from '@/Components/Admin/Modal/DangerModal.vue'
import Pagination from '@/Components/Admin/Pagination/Pagination.vue'
import SortSelect from '@/Components/Admin/QuizAnswer/Sort/SortSelect.vue'
import ToggleViewButton from '@/Components/Admin/Buttons/ToggleViewButton.vue'

// --- i18n, toast ---
const { t } = useI18n()
const toast = useToast()

/**
 * Пропсы из QuizAnswerController@index
 */
const props = defineProps({
    answers: {
        type: Array,
        default: () => []
    },
    answersCount: {
        type: Number,
        default: 0
    },
    adminCountAnswers: {
        type: Number,
        default: 20
    },
    adminSortAnswers: {
        type: String,
        default: 'idDesc'
    },
    // списка квизов для фильтра
    quizzes: {
        type: Array,
        default: () => []
    },
    // список вопросов для фильтра
    questions: {
        type: Array,
        default: () => []
    },
    // текущая локаль и список локалей
    currentLocale: {
        type: String,
        default: 'ru'
    },
    availableLocales: {
        type: Array,
        default: () => []
    },
    currentQuizId: {
        type: Number,
        default: null
    },
    currentQuestionId: {
        type: Number,
        default: null
    }
})

/** переключатель локалей */
const selectedQuizId = ref(props.currentQuizId ?? null)
const selectedQuestionId = ref(props.currentQuestionId ?? null)

const localeLink = (locale) =>
    route('admin.quizAnswers.index', {
        locale,
        ...(selectedQuizId.value ? { quiz_id: selectedQuizId.value } : {}),
        ...(selectedQuestionId.value ? { quiz_question_id: selectedQuestionId.value } : {})
    })

/** Опции квизов и подпись для селекта в фильтре */
const quizOptions = computed(() => props.quizzes ?? [])

const quizOptionLabel = (quiz) => {
    if (!quiz) return ''

    const idPart = `ID:${quiz.id}`
    const localePart = quiz.locale ? ` [${quiz.locale}]` : ''
    const titlePart = quiz.title ? ` ${quiz.title}` : ''

    return `${idPart}${localePart}${titlePart}`
}

/** Опции вопросов для фильтра (id DESC) */
const questionOptions = computed(() => {
    const list = props.questions ? [...props.questions] : []

    return list.sort((a, b) => (b.id ?? 0) - (a.id ?? 0))
})

// Удаляем HTML и сокращаем
const stripHtml = (html = '') => {
    return html
        .replace(/<\/p>/gi, ' ')
        .replace(/<br\s*\/?>/gi, ' ')
        .replace(/<[^>]+>/g, '')
        .replace(/&nbsp;/gi, ' ')
        .replace(/\s+/g, ' ')
        .trim()
}

const shortText = (html, limit = 100) => {
    const clean = stripHtml(html)
    return clean.length > limit ? clean.slice(0, limit) + '…' : clean
}

const questionOptionLabel = (question) => {
    if (!question) return ''

    const idPart = `ID:${question.id}`
    const textPart = question.question_text ? ` ${shortText(question.question_text)}` : ''
    const quizPart = question.quiz_id ? ` (quiz:${question.quiz_id})` : ''

    return `${idPart}${textPart}${quizPart}`
}

/** Вид: таблица или карточки (общий ключ для админки) */
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')

watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode', val)
})

/**
 * Смена квиза в фильтре.
 * Перезагружаем страницу с query ?quiz_id=...
 * При смене квиза сбрасываем выбранный вопрос.
 */
const handleQuizFilterChange = () => {
    const data = {}

    if (props.currentLocale) {
        data.locale = props.currentLocale
    }

    if (selectedQuizId.value) {
        data.quiz_id = selectedQuizId.value
    }

    // при смене квиза сбрасываем фильтр вопроса
    selectedQuestionId.value = null

    router.get(
        route('admin.quizAnswers.index'),
        data,
        {
            preserveScroll: true,
            preserveState: true
        }
    )
}

/**
 * Смена вопроса в фильтре.
 * Перезагружаем страницу с query ?quiz_id=&quiz_question_id=
 */
const handleQuestionFilterChange = () => {
    const data = {}

    if (props.currentLocale) {
        data.locale = props.currentLocale
    }

    if (selectedQuizId.value) {
        data.quiz_id = selectedQuizId.value
    }

    if (selectedQuestionId.value) {
        data.quiz_question_id = selectedQuestionId.value
    }

    router.get(
        route('admin.quizAnswers.index'),
        data,
        {
            preserveScroll: true,
            preserveState: true
        }
    )
}

/** Кол-во элементов на странице */
const itemsPerPage = ref(props.adminCountAnswers || 20)

/** Сохранение кол-ва элементов на страницу в настройках */
watch(itemsPerPage, (newVal) => {
    router.put(
        route('admin.settings.updateAdminCountQuizAnswers'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
            onError: (errors) =>
                toast.error(
                    (errors && (errors.value || Object.values(errors)[0])) ||
                    'Ошибка обновления количества элементов.'
                )
        }
    )
})

/** Параметр сортировки (из настроек) */
const sortParam = ref(props.adminSortAnswers || 'idDesc')

/** Сохранение параметра сортировки в настройках */
watch(sortParam, (newVal) => {
    router.put(
        route('admin.settings.updateAdminSortQuizAnswers'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info('Сортировка успешно изменена'),
            onError: (errors) => {
                const firstError = errors ? Object.values(errors)[0] : null
                toast.error(firstError || 'Ошибка обновления сортировки.')
            }
        }
    )
})

/** Модалка удаления одного ответа */
const showConfirmDeleteModal = ref(false)
const answerToDeleteId = ref(null)

const confirmDelete = (id) => {
    answerToDeleteId.value = id
    showConfirmDeleteModal.value = true
}

const closeModal = () => {
    showConfirmDeleteModal.value = false
    answerToDeleteId.value = null
}

/** Удаление одного ответа */
const deleteAnswer = () => {
    if (answerToDeleteId.value === null) return
    const idToDelete = answerToDeleteId.value

    router.delete(
        route('admin.quizAnswers.destroy', { quizAnswer: idToDelete }),
        {
            preserveScroll: true,
            preserveState: false,
            onSuccess: () => {
                closeModal()
                toast.success(`Ответ "${'ID: ' + idToDelete}" удалён.`)
            },
            onError: (errors) => {
                closeModal()
                const errorMsg =
                    errors?.general ||
                    (Object.keys(errors || {}).length
                        ? errors[Object.keys(errors)[0]]
                        : null) ||
                    'Произошла ошибка при удалении.'
                toast.error(`${errorMsg} (Ответ: ${'ID: ' + idToDelete})`)
            },
            onFinish: () => {
                answerToDeleteId.value = null
            }
        }
    )
}

/** Пагинация и поиск */
const currentPage = ref(1)
const searchQuery = ref('')

/** Сортировка массива ответов */
const sortAnswers = (answers) => {
    const value = sortParam.value
    const list = answers.slice()

    // --- ID ---
    if (value === 'idAsc') {
        return list.sort((a, b) => a.id - b.id)
    }
    if (value === 'idDesc') {
        return list.sort((a, b) => b.id - a.id)
    }

    // --- По sort (числовое поле sort) ---
    if (value === 'sort' || value === 'sortAsc') {
        return list.sort((a, b) => (a.sort ?? 0) - (b.sort ?? 0))
    }
    if (value === 'sortDesc') {
        return list.sort((a, b) => (b.sort ?? 0) - (a.sort ?? 0))
    }

    // --- Вес ---
    if (value === 'weightAsc') {
        return list.sort((a, b) => (a.weight ?? 0) - (b.weight ?? 0))
    }
    if (value === 'weightDesc') {
        return list.sort((a, b) => (b.weight ?? 0) - (a.weight ?? 0))
    }

    // --- Фильтры по активности ---
    if (value === 'activity') return answers.filter(a => a.activity)
    if (value === 'inactive') return answers.filter(a => !a.activity)

    // --- Квиз (quiz.title) ---
    if (value === 'quizTitle') {
        return list.sort((a, b) => {
            const av = (a.quiz?.title || '').toLowerCase()
            const bv = (b.quiz?.title || '').toLowerCase()
            if (av < bv) return -1
            if (av > bv) return 1
            return 0
        })
    }

    // --- Вопрос (по id вопроса) ---
    if (value === 'questionId') {
        return list.sort((a, b) => (a.quiz_question_id ?? 0) - (b.quiz_question_id ?? 0))
    }

    // --- Правильный / неправильный ---
    if (value === 'isCorrectFirst') {
        return list.sort((a, b) => {
            // true -> 0, false -> 1 (чтобы правильные были вверху)
            const av = a.is_correct ? 0 : 1
            const bv = b.is_correct ? 0 : 1
            return av - bv
        })
    }

    // --- Общий случай для строковых полей (например, title) ---
    // сюда попадёт value === 'title' из SortSelect
    return list.sort((a, b) => {
        const av = (a[value] ?? '').toString().toLowerCase()
        const bv = (b[value] ?? '').toString().toLowerCase()
        if (av < bv) return -1
        if (av > bv) return 1
        return 0
    })
}

/** Фильтрация + сортировка */
const filteredAnswers = computed(() => {
    let filtered = props.answers || []

    // Фильтр по квизу
    if (selectedQuizId.value) {
        filtered = filtered.filter(a => a.quiz_id === selectedQuizId.value)
    }

    // Фильтр по вопросу
    if (selectedQuestionId.value) {
        filtered = filtered.filter(a => a.quiz_question_id === selectedQuestionId.value)
    }

    // Поиск по тексту ответа / пояснению
    // Поиск по ответу, пояснению, вопросу и названию квиза
    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase();

        filtered = filtered.filter(answer => {
            const answerText = (answer.text || '').toLowerCase();
            const explanation = (answer.explanation || '').toLowerCase();

            // quiz.title
            const quizTitle = (answer.quiz?.title || '').toLowerCase();

            // quiz_questions.question_text
            const questionText = stripHtml(answer.question?.question_text || '').toLowerCase();

            return (
                answerText.includes(q) ||
                explanation.includes(q) ||
                quizTitle.includes(q) ||
                questionText.includes(q)
            );
        });
    }

    return sortAnswers(filtered)
})

/** Пагинация */
const paginatedAnswers = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value
    return filteredAnswers.value.slice(start, start + itemsPerPage.value)
})

/** Общее количество страниц (нужно для Pagination) */
const totalPages = computed(() =>
    Math.ceil((filteredAnswers.value.length || 0) / itemsPerPage.value)
)

/** Массив выбранных ответов (для массовых операций) */
const selectedAnswers = ref([])

/** toggle-all из таблицы/карточек */
const toggleAll = ({ ids, checked }) => {
    if (checked) {
        selectedAnswers.value = [...ids]
    } else {
        selectedAnswers.value = []
    }
}

/** Выбор/снятие выбора одного ответа */
const toggleSelectAnswer = (id) => {
    const idx = selectedAnswers.value.indexOf(id)
    if (idx > -1) selectedAnswers.value.splice(idx, 1)
    else selectedAnswers.value.push(id)
}

/** Массовое обновление активности */
const bulkToggleActivity = (newActivity) => {
    if (!selectedAnswers.value.length) {
        toast.warning('Выберите ответы для активации/деактивации')
        return
    }

    router.put(
        route('admin.actions.quizAnswers.bulkUpdateActivity'),
        {
            ids: selectedAnswers.value,
            activity: newActivity,
            // опционально – если есть выбранные фильтры, можем их всё-таки передавать:
            ...(selectedQuizId.value ? { quiz_id: selectedQuizId.value } : {}),
            ...(selectedQuestionId.value ? { quiz_question_id: selectedQuestionId.value } : {}),
        },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                toast.success('Активность ответов массово обновлено')
                const updatedIds = [...selectedAnswers.value]
                selectedAnswers.value = []

                paginatedAnswers.value.forEach(a => {
                    if (updatedIds.includes(a.id)) a.activity = newActivity
                })
            },
            onError: (errors) => {
                const msg =
                    errors?.ids ||
                    errors?.activity ||
                    errors?.general ||
                    'Не удалось массово обновить активность ответов'
                toast.error(msg)
            }
        }
    )
}

/** Массовое удаление */
const bulkDelete = () => {
    if (!selectedAnswers.value.length) {
        toast.warning('Выберите ответы для удаления')
        return
    }

    if (!confirm(`Удалить ${selectedAnswers.value.length} выбранных ответ(ов)?`)) {
        return
    }

    router.delete(
        route('admin.actions.quizAnswers.bulkDestroy'),
        {
            data: {
                ids: selectedAnswers.value,
                ...(selectedQuizId.value ? { quiz_id: selectedQuizId.value } : {}),
                ...(selectedQuestionId.value ? { quiz_question_id: selectedQuestionId.value } : {}),
            },
            preserveScroll: true,
            preserveState: false,
            onSuccess: () => {
                toast.success('Выбранные ответы удалены.')
                selectedAnswers.value = []
            },
            onError: (errors) => {
                const msg =
                    errors?.ids ||
                    errors?.quiz_id ||
                    errors?.quiz_question_id ||
                    errors?.general ||
                    'Ошибка массового удаления ответов.'
                toast.error(msg)
            }
        }
    )
}

/** Обработка выбора действия из селекта массовых действий */
const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        selectedAnswers.value = paginatedAnswers.value.map(a => a.id)
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

/** Переключение активности одного ответа */
const toggleActivity = (answer) => {
    const newActivity = !answer.activity
    const actionText = newActivity ? 'активирован' : 'деактивирован'

    router.put(
        route('admin.actions.quizAnswers.updateActivity', {
            quizAnswer: answer.id
        }),
        { activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                answer.activity = newActivity
                toast.success(`Ответ ID ${answer.id} ${actionText}.`)
            },
            onError: (errors) => {
                toast.error(
                    errors?.activity ||
                    errors?.general ||
                    `Ошибка изменения активности для ответа ID ${answer.id}.`
                )
            }
        }
    )
}

/** Drag&drop сортировка (массовое обновление sort по текущей странице) */
const handleSortOrderUpdate = (orderedIds) => {
    const startSort = (currentPage.value - 1) * itemsPerPage.value

    const sortData = orderedIds.map((id, index) => ({
        id,
        sort: startSort + index + 1
    }))

    router.put(
        route('admin.actions.quizAnswers.updateSortBulk'),
        {
            answers: sortData,
            ...(selectedQuizId.value ? { quiz_id: selectedQuizId.value } : {}),
            ...(selectedQuestionId.value ? { quiz_question_id: selectedQuestionId.value } : {}),
        },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                toast.success('Порядок ответов успешно обновлён.')
            },
            onError: (errors) => {
                console.error('Ошибка обновления сортировки ответов:', errors)
                toast.error(
                    errors?.general ||
                    errors?.answers ||
                    'Не удалось обновить порядок ответов.'
                )
                router.reload({ only: ['answers'], preserveScroll: true })
            }
        }
    )
}

</script>

<template>
    <AdminLayout :title="t('quizAnswers')">
        <template #header>
            <TitlePage>{{ t('quizAnswers') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <!-- Добавить ответ -->
                    <DefaultButton :href="route('admin.quizAnswers.create')">
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
                        {{ t('addQuizAnswer') }}
                    </DefaultButton>

                    <!-- Массовые операции -->
                    <BulkActionSelect
                        v-if="answersCount"
                        @change="handleBulkAction"
                    />
                </div>

                <!-- Локали -->
                <div class="flex items-center justify-between mt-5">
                    <div
                        class="flex items-center justify-end space-x-2 px-3 py-1
                               border-x border-t border-gray-400 rounded-t-lg
                               bg-gray-100 dark:bg-gray-900">

                        <span class="text-sm font-medium text-slate-700 dark:text-slate-200">
                            {{ t('localization') }}:
                        </span>

                        <template v-for="locale in availableLocales" :key="locale">
                            <Link
                                :href="localeLink(locale)"
                                :class="[
                                    'px-3 py-1 text-sm font-medium rounded-sm',
                                    currentLocale === locale
                                        ? 'bg-blue-500 text-white'
                                        : 'bg-slate-100 dark:bg-slate-900 ' +
                                          'text-slate-700 dark:text-slate-200 ' +
                                          'hover:bg-slate-300 dark:hover:bg-slate-600'
                                ]"
                                preserve-scroll
                                preserve-state
                            >
                                {{ locale.toUpperCase() }}
                            </Link>
                        </template>
                    </div>
                </div>

                <!-- Поиск -->
                <SearchInput
                    v-if="answersCount"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <div
                    class="flex flex-col gap-3
                           md:flex-row md:items-center md:justify-between my-3"
                >
                    <!-- Фильтр по квизу и вопросу -->
                    <div class="flex flex-col gap-3">
                        <!-- Квиз -->
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-200">
                                {{ t('quiz') }}:
                            </span>
                            <select
                                v-model.number="selectedQuizId"
                                @change="handleQuizFilterChange"
                                class="border rounded px-2 py-1 text-sm bg-white dark:bg-slate-800
                                       dark:text-slate-100 w-full"
                            >
                                <option :value="null">
                                    {{ t('allQuizzes') }}
                                </option>
                                <option
                                    v-for="q in quizOptions"
                                    :key="q.id"
                                    :value="q.id"
                                >
                                    {{ quizOptionLabel(q) }}
                                </option>
                            </select>
                        </div>

                        <!-- Вопрос -->
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-200">
                                {{ t('quizQuestion') }}:
                            </span>
                            <select
                                v-model.number="selectedQuestionId"
                                @change="handleQuestionFilterChange"
                                class="border rounded px-2 py-1 text-sm bg-white dark:bg-slate-800
                                       dark:text-slate-100 w-full"
                            >
                                <option :value="null">
                                    {{ t('allQuestions') }}
                                </option>
                                <option
                                    v-for="q in questionOptions"
                                    :key="q.id"
                                    :value="q.id"
                                >
                                    {{ questionOptionLabel(q) }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Счётчик ответов + Переключатель вида -->
                <div class="flex items-center justify-end space-x-2 my-3">
                    <CountTable v-if="answersCount">
                        {{ answersCount }}
                    </CountTable>

                    <!-- Переключатель вида (таблица / карточки), общий -->
                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <!-- Таблица -->
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

                <!-- Карточки -->
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

                <div
                    v-if="answersCount"
                    class="flex justify-between items-center flex-col md:flex-row my-1"
                >
                    <ItemsPerPageSelect
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredAnswers.length"
                        :total-pages="totalPages"
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
            :onConfirm="deleteAnswer"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        >
            <template #title>
                {{ t('delete') }} {{ t('quizAnswer') }}
            </template>
            <template #content>
                {{ t('areYouSureDelete') }}
            </template>
        </DangerModal>
    </AdminLayout>
</template>
