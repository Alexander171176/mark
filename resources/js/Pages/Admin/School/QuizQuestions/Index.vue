<script setup>
/**
 * @version PulsarCMS 1.0
 * @author ...
 * Список вопросов квизов (QuizQuestion)
 */
import { defineProps, ref, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router, Link } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import SearchInput from '@/Components/Admin/Search/SearchInput.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import BulkActionSelect from '@/Components/Admin/QuizQuestion/Select/BulkActionSelect.vue'
import CountTable from '@/Components/Admin/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/Select/ItemsPerPageSelect.vue'
import QuizQuestionTable from '@/Components/Admin/QuizQuestion/Table/QuizQuestionTable.vue'
import QuizQuestionCardGrid from '@/Components/Admin/QuizQuestion/View/QuizQuestionCardGrid.vue'
import DangerModal from '@/Components/Admin/Modal/DangerModal.vue'
import Pagination from '@/Components/Admin/Pagination/Pagination.vue'
import SortSelect from '@/Components/Admin/QuizQuestion/Sort/SortSelect.vue'
import ToggleViewButton from '@/Components/Admin/Buttons/ToggleViewButton.vue'

// --- i18n, toast ---
const { t } = useI18n()
const toast = useToast()

/**
 * Пропсы из QuizQuestionController@index
 */
const props = defineProps({
    questions: {
        type: Array,
        default: () => [],
    },
    questionsCount: {
        type: Number,
        default: 0,
    },
    adminCountQuestions: {
        type: Number,
        default: 10,
    },
    adminSortQuestions: {
        type: String,
        default: 'idDesc',
    },
    quizzes: {
        type: Array,
        default: () => [],
    },
    currentQuizId: {
        type: Number,
        default: null,
    },
    currentLocale: {
        type: String,
        default: 'ru',
    },
    availableLocales: {
        type: Array,
        default: () => [],
    },
})

/** выбранный квиз в фильтре */
const selectedQuizId = ref(props.currentQuizId ?? null)

/** переключатель локалей */
const localeLink = (locale) =>
    route('admin.quizQuestions.index', {
        locale,
        ...(selectedQuizId.value ? { quiz_id: selectedQuizId.value } : {}),
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

/** Вид: таблица или карточки (общий ключ для админки) */
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')

watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode', val)
})

/**
 * Смена квиза в фильтре.
 * Перезагружаем страницу с query ?quiz_id=...
 */
const handleQuizFilterChange = () => {
    const data = {}

    if (props.currentLocale) {
        data.locale = props.currentLocale
    }

    if (selectedQuizId.value) {
        data.quiz_id = selectedQuizId.value
    }

    router.get(
        route('admin.quizQuestions.index'),
        data,
        {
            preserveScroll: true,
            preserveState: true,
        }
    )
}

/** Кол-во элементов на странице */
const itemsPerPage = ref(props.adminCountQuestions || 10)

/** Сохранение кол-ва элементов на страницу в настройках */
watch(itemsPerPage, (newVal) => {
    router.put(
        route('admin.settings.updateAdminCountQuizQuestions'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
            onError: (errors) =>
                toast.error(
                    (errors && (errors.value || Object.values(errors)[0])) ||
                    'Ошибка обновления количества элементов.'
                ),
        }
    )
})

/** Параметр сортировки (из настроек) */
const sortParam = ref(props.adminSortQuestions || 'idDesc')

/** Сохранение параметра сортировки в настройках */
watch(sortParam, (newVal) => {
    router.put(
        route('admin.settings.updateAdminSortQuizQuestions'),
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

/** Модалка удаления одного вопроса */
const showConfirmDeleteModal = ref(false)
const questionToDeleteId = ref(null)
const questionToDeleteTitle = ref('')

const confirmDelete = (id, title) => {
    questionToDeleteId.value = id
    questionToDeleteTitle.value = title
    showConfirmDeleteModal.value = true
}

const closeModal = () => {
    showConfirmDeleteModal.value = false
    questionToDeleteId.value = null
    questionToDeleteTitle.value = ''
}

/** Удаление одного вопроса */
const deleteQuestion = () => {
    if (questionToDeleteId.value === null) return
    const idToDelete = questionToDeleteId.value

    router.delete(
        route('admin.quizQuestions.destroy', { quizQuestion: idToDelete }),
        {
            preserveScroll: true,
            preserveState: false,
            onSuccess: () => {
                closeModal()
                toast.success(
                    `Вопрос "${'ID: ' + idToDelete}" удалён.`
                )
            },
            onError: (errors) => {
                closeModal()
                const errorMsg =
                    errors?.general ||
                    (Object.keys(errors || {}).length
                        ? errors[Object.keys(errors)[0]]
                        : null) ||
                    'Произошла ошибка при удалении.'
                toast.error(
                    `${errorMsg} (Вопрос: ${'ID: ' + idToDelete})`
                )
            },
            onFinish: () => {
                questionToDeleteId.value = null
                questionToDeleteTitle.value = ''
            },
        }
    )
}

/** Пагинация и поиск */
const currentPage = ref(1)
const searchQuery = ref('')

/** Сортировка массива вопросов */
const sortQuestions = (questions) => {
    const value = sortParam.value
    const list = questions.slice()

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

    // --- Баллы (points) ---
    if (value === 'pointsAsc') {
        return list.sort((a, b) => (a.points ?? 0) - (b.points ?? 0))
    }
    if (value === 'pointsDesc') {
        return list.sort((a, b) => (b.points ?? 0) - (a.points ?? 0))
    }

    // --- Фильтры по активности ---
    if (value === 'activity') return questions.filter(q => q.activity)
    if (value === 'inactive') return questions.filter(q => !q.activity)

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

    // --- Тип вопроса (question_type) ---
    if (value === 'questionType') {
        return list.sort((a, b) => {
            const av = (a.question_type || '').toLowerCase()
            const bv = (b.question_type || '').toLowerCase()
            if (av < bv) return -1
            if (av > bv) return 1
            return 0
        })
    }

    // --- Общий случай для строковых полей (если SortSelect вернёт другое значение) ---
    return list.sort((a, b) => {
        const av = (a[value] ?? '').toString().toLowerCase()
        const bv = (b[value] ?? '').toString().toLowerCase()
        if (av < bv) return -1
        if (av > bv) return 1
        return 0
    })
}

/** Фильтрация + сортировка */
const filteredQuestions = computed(() => {
    let filtered = props.questions || []

    // Фильтр по квизу, если выбран
    if (selectedQuizId.value) {
        filtered = filtered.filter(q => q.quiz_id === selectedQuizId.value)
    }

    // Поиск по тексту вопроса / пояснению
    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase()
        filtered = filtered.filter(question =>
            (question.question_text || '').toLowerCase().includes(q) ||
            (question.explanation || '').toLowerCase().includes(q)
        )
    }

    return sortQuestions(filtered)
})

/** Пагинация */
const paginatedQuestions = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value
    return filteredQuestions.value.slice(start, start + itemsPerPage.value)
})

/** Общее количество страниц */
const totalPages = computed(() =>
    Math.ceil((filteredQuestions.value.length || 0) / itemsPerPage.value)
)

/** Массив выбранных вопросов (для массовых операций) */
const selectedQuestions = ref([])

/** toggle-all из таблицы/карточек */
const toggleAll = ({ ids, checked }) => {
    if (checked) {
        selectedQuestions.value = [...ids]
    } else {
        selectedQuestions.value = []
    }
}

/** Выбор/снятие выбора одного вопроса */
const toggleSelectQuestion = (id) => {
    const idx = selectedQuestions.value.indexOf(id)
    if (idx > -1) selectedQuestions.value.splice(idx, 1)
    else selectedQuestions.value.push(id)
}

/** Массовое обновление активности (как у QuizAnswer) */
const bulkToggleActivity = (newActivity) => {
    if (!selectedQuestions.value.length) {
        toast.warning('Выберите вопросы для активации/деактивации')
        return
    }

    router.put(
        route('admin.actions.quizQuestions.bulkUpdateActivity'),
        {
            ids: selectedQuestions.value,
            activity: newActivity,
            ...(selectedQuizId.value ? { quiz_id: selectedQuizId.value } : {}),
        },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                toast.success('Активность вопросов массово обновлена')
                const updatedIds = [...selectedQuestions.value]
                selectedQuestions.value = []

                paginatedQuestions.value.forEach(q => {
                    if (updatedIds.includes(q.id)) q.activity = newActivity
                })
            },
            onError: (errors) => {
                const msg =
                    errors?.ids ||
                    errors?.activity ||
                    errors?.general ||
                    'Не удалось массово обновить активность вопросов'
                toast.error(msg)
            },
        }
    )
}

/** Массовое удаление (как у QuizAnswer) */
const bulkDelete = () => {
    if (!selectedQuestions.value.length) {
        toast.warning('Выберите вопросы для удаления')
        return
    }

    if (
        !confirm(
            `Удалить ${selectedQuestions.value.length} выбранных вопрос(ов)?`
        )
    ) {
        return
    }

    router.delete(
        route('admin.actions.quizQuestions.bulkDestroy'),
        {
            data: {
                ids: selectedQuestions.value,
                ...(selectedQuizId.value ? { quiz_id: selectedQuizId.value } : {}),
            },
            preserveScroll: true,
            preserveState: false,
            onSuccess: () => {
                toast.success('Выбранные вопросы удалены.')
                selectedQuestions.value = []
            },
            onError: (errors) => {
                const msg =
                    errors?.ids ||
                    errors?.quiz_id ||
                    errors?.general ||
                    'Ошибка массового удаления вопросов.'
                toast.error(msg)
            },
        }
    )
}

/** Обработка выбора действия из селекта массовых действий */
const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        selectedQuestions.value = paginatedQuestions.value.map(q => q.id)
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

/** Переключение активности одного вопроса */
const toggleActivity = (question) => {
    const newActivity = !question.activity
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(
        route('admin.actions.quizQuestions.updateActivity', {
            quizQuestion: question.id,
        }),
        { activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                question.activity = newActivity
                toast.success(`Вопрос ID ${question.id} ${actionText}.`)
            },
            onError: (errors) => {
                toast.error(
                    errors?.activity ||
                    errors?.general ||
                    `Ошибка изменения активности для вопроса ID ${question.id}.`
                )
            },
        }
    )
}

/** Drag&drop сортировка (массовое обновление sort) — как у QuizAnswer */
const handleSortOrderUpdate = (orderedIds) => {
    const startSort = (currentPage.value - 1) * itemsPerPage.value

    const sortData = orderedIds.map((id, index) => ({
        id,
        sort: startSort + index + 1,
    }))

    router.put(
        route('admin.actions.quizQuestions.updateSortBulk'),
        {
            questions: sortData,
            ...(selectedQuizId.value ? { quiz_id: selectedQuizId.value } : {}),
        },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                toast.success('Порядок вопросов успешно обновлён.')
            },
            onError: (errors) => {
                console.error('Ошибка обновления сортировки вопросов:', errors)
                toast.error(
                    errors?.general ||
                    errors?.questions ||
                    'Не удалось обновить порядок вопросов.'
                )
                router.reload({ only: ['questions'], preserveScroll: true })
            },
        }
    )
}

/** Клонирование одного вопроса */
const cloneQuestion = (question) => {
    router.post(
        route('admin.actions.quizQuestions.clone', {
            quizQuestion: question.id,
        }),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                // при необходимости можно добавить toast
            },
        }
    )
}
</script>

<template>
    <AdminLayout :title="t('quizQuestions')">
        <template #header>
            <TitlePage>{{ t('quizQuestions') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <!-- Добавить вопрос -->
                    <DefaultButton :href="route('admin.quizQuestions.create')">
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
                        {{ t('addQuizQuestion') }}
                    </DefaultButton>

                    <!-- Массовые операции -->
                    <BulkActionSelect
                        v-if="questionsCount"
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
                    v-if="questionsCount"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <div class="flex flex-col gap-3
                            md:flex-row md:items-center md:justify-between my-2">

                    <!-- Фильтр по квизу -->
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-200">
                            {{ t('quiz') }}:
                        </span>
                        <select
                            v-model.number="selectedQuizId"
                            @change="handleQuizFilterChange"
                            class="border rounded px-2 py-1 text-sm bg-white dark:bg-slate-800
                                   dark:text-slate-100">
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

                    <!-- Счётчик вопросов + Переключатель вида -->
                    <div class="flex items-center space-x-2">
                        <CountTable v-if="questionsCount">
                            {{ questionsCount }}
                        </CountTable>

                        <!-- Переключатель вида (таблица / карточки), общий -->
                        <ToggleViewButton v-model:viewMode="viewMode" />
                    </div>

                </div>

                <!-- Таблица -->
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

                <!-- Карточки -->
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
                    class="flex justify-between items-center flex-col md:flex-row my-1"
                >
                    <ItemsPerPageSelect
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredQuestions.length"
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
            :onConfirm="deleteQuestion"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
