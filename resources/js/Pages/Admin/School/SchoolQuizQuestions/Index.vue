<script setup>
/**
 * Список вопросов квизов школы
 * - режимы обработки: frontend | server | auto
 * - фильтр по квизу
 * - локальный и серверный поиск
 * - локальная и серверная пагинация
 */

import { computed, defineProps, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import SearchInput from '@/Components/Admin/UI/Search/SearchInput.vue'
import ServerSearchInput from '@/Components/Admin/UI/Search/ServerSearchInput.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import ToggleViewButton from '@/Components/Admin/UI/Buttons/ToggleViewButton.vue'
import CountTable from '@/Components/Admin/UI/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/UI/Select/ItemsPerPageSelect.vue'
import ServerItemsPerPageSelect from '@/Components/Admin/UI/Select/ServerItemsPerPageSelect.vue'
import DangerModal from '@/Components/Admin/UI/Modal/DangerModal.vue'
import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue'
import AdminServerPagination from '@/Components/Admin/UI/Pagination/AdminServerPagination.vue'
import ProcessingModeSwitcher from '@/Components/Admin/UI/Processing/ProcessingModeSwitcher.vue'

import BulkActionSelect from '@/Components/Admin/School/SchoolQuizQuestion/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/School/SchoolQuizQuestion/Sort/SortSelect.vue'
import QuizQuestionTable from '@/Components/Admin/School/SchoolQuizQuestion/Table/QuizQuestionTable.vue'
import QuizQuestionCardGrid from '@/Components/Admin/School/SchoolQuizQuestion/View/QuizQuestionCardGrid.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },

    adminSchoolQuizQuestionsProcessingMode: { type: String, default: 'frontend' },
    useServerProcessing: { type: Boolean, default: false },

    questions: { type: [Array, Object], default: () => [] },
    questionsCount: { type: Number, default: 0 },

    adminSchoolQuizQuestionsPerPage: { type: Number, default: 10 },
    adminSchoolQuizQuestionsDefaultSort: { type: String, default: 'idDesc' },

    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },

    filters: { type: Object, default: () => ({}) },

    quizzes: { type: Array, default: () => [] },
    currentQuizId: { type: Number, default: null },

    errors: { type: Object, default: () => ({}) },
})

const viewMode = ref(localStorage.getItem('admin_view_mode_quiz_questions') || 'table')

watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode_quiz_questions', val)
})

const questionsList = computed(() => {
    if (Array.isArray(props.questions)) return props.questions
    if (Array.isArray(props.questions?.data)) return props.questions.data

    return []
})

const localQuestions = ref([])

watch(
    questionsList,
    (newVal) => {
        localQuestions.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

const selectedQuizId = ref(props.currentQuizId ?? props.filters?.school_quiz_id ?? null)

const quizOptions = computed(() => props.quizzes || [])

const quizOptionLabel = (quiz) => {
    if (!quiz) return ''

    const idPart = `[ID: ${quiz.id}]`
    const titlePart = quiz.title || quiz.slug || `#${quiz.id}`

    const context = [
        quiz.lesson?.title ? `Урок: ${quiz.lesson.title}` : null,
        quiz.module?.title ? `Модуль: ${quiz.module.title}` : null,
        quiz.course?.title ? `Курс: ${quiz.course.title}` : null,
    ].filter(Boolean).join(' / ')

    return context ? `${idPart} ${titlePart} — ${context}` : `${idPart} ${titlePart}`
}

const handleQuizFilterChange = () => {
    const params = {
        ...Object.fromEntries(new URLSearchParams(window.location.search)),
        page: undefined,
    }

    if (selectedQuizId.value) {
        params.school_quiz_id = selectedQuizId.value
    } else {
        delete params.school_quiz_id
    }

    router.get(route('admin.schoolQuizQuestions.index'), params, {
        preserveScroll: true,
        preserveState: false,
        replace: true,
    })
}

const itemsPerPage = ref(props.adminSchoolQuizQuestionsPerPage || 10)

watch(itemsPerPage, (newVal) => {
    router.put(
        route('admin.settings.updateAdminCountSchoolQuizQuestions'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
            onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
        }
    )
})

const sortParam = ref(
    props.sortParam ||
    props.adminSchoolQuizQuestionsDefaultSort ||
    'idDesc'
)

watch(sortParam, (newVal) => {
    currentPage.value = 1

    router.put(
        route('admin.settings.updateAdminSortSchoolQuizQuestions'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                if (props.useServerProcessing) {
                    router.get(
                        window.location.pathname,
                        {
                            ...Object.fromEntries(new URLSearchParams(window.location.search)),
                            sort: newVal || undefined,
                            page: undefined,
                        },
                        {
                            preserveScroll: true,
                            preserveState: false,
                            replace: true,
                        }
                    )
                }

                toast.info('Сортировка успешно изменена')
            },
            onError: (errors) => toast.error(errors.value || 'Ошибка обновления сортировки.'),
        }
    )
})

const searchQuery = ref(props.search || '')
const currentPage = ref(1)

const normalize = (value) => (value ?? '').toString().trim().toLowerCase()

const safeNumber = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

const safeDate = (value) => {
    const time = new Date(value || 0).getTime()
    return Number.isFinite(time) ? time : 0
}

const getQuestionText = (question) => {
    return question?.question_text
        || question?.translation?.question_text
        || question?.translations?.[0]?.question_text
        || `ID: ${question?.id}`
}

const getQuestionExplanation = (question) => {
    return question?.explanation
        || question?.translation?.explanation
        || question?.translations?.[0]?.explanation
        || ''
}

const getNestedTitle = (item) => {
    return item?.title
        || item?.name
        || item?.translation?.title
        || item?.translation?.name
        || item?.translations?.[0]?.title
        || item?.translations?.[0]?.name
        || ''
}

const getQuizTitle = (question) => getNestedTitle(question?.quiz)

const byNumberAsc = (field) => (a, b) =>
    safeNumber(a?.[field]) - safeNumber(b?.[field])
    || safeNumber(a?.id) - safeNumber(b?.id)

const byNumberDesc = (field) => (a, b) =>
    safeNumber(b?.[field]) - safeNumber(a?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

const byStringAsc = (field) => (a, b) =>
    normalize(a?.[field]).localeCompare(normalize(b?.[field]), props.currentLocale)
    || safeNumber(a?.id) - safeNumber(b?.id)

const byStringDesc = (field) => (a, b) =>
    normalize(b?.[field]).localeCompare(normalize(a?.[field]), props.currentLocale)
    || safeNumber(b?.id) - safeNumber(a?.id)

const byDateAsc = (field) => (a, b) =>
    safeDate(a?.[field]) - safeDate(b?.[field])
    || safeNumber(a?.id) - safeNumber(b?.id)

const byDateDesc = (field) => (a, b) =>
    safeDate(b?.[field]) - safeDate(a?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

const sortQuestions = (items) => {
    const list = (items || []).slice()

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

    const sortMap = {
        idAsc: byNumberAsc('id'),
        idDesc: byNumberDesc('id'),

        sortAsc: byNumberAsc('sort'),
        sortDesc: byNumberDesc('sort'),

        questionTextAsc: (a, b) =>
            normalize(getQuestionText(a)).localeCompare(normalize(getQuestionText(b)), props.currentLocale)
            || safeNumber(a?.id) - safeNumber(b?.id),

        questionTextDesc: (a, b) =>
            normalize(getQuestionText(b)).localeCompare(normalize(getQuestionText(a)), props.currentLocale)
            || safeNumber(b?.id) - safeNumber(a?.id),

        quizTitleAsc: (a, b) =>
            normalize(getQuizTitle(a)).localeCompare(normalize(getQuizTitle(b)), props.currentLocale)
            || safeNumber(a?.id) - safeNumber(b?.id),

        quizTitleDesc: (a, b) =>
            normalize(getQuizTitle(b)).localeCompare(normalize(getQuizTitle(a)), props.currentLocale)
            || safeNumber(b?.id) - safeNumber(a?.id),

        questionTypeAsc: byStringAsc('question_type'),
        questionTypeDesc: byStringDesc('question_type'),

        pointsAsc: byNumberAsc('points'),
        pointsDesc: byNumberDesc('points'),

        answersCountAsc: byNumberAsc('answers_count'),
        answersCountDesc: byNumberDesc('answers_count'),

        attemptItemsCountAsc: byNumberAsc('attempt_items_count'),
        attemptItemsCountDesc: byNumberDesc('attempt_items_count'),

        activityAsc: byNumberAsc('activity'),
        activityDesc: byNumberDesc('activity'),

        createdAtAsc: byDateAsc('created_at'),
        createdAtDesc: byDateDesc('created_at'),

        updatedAtAsc: byDateAsc('updated_at'),
        updatedAtDesc: byDateDesc('updated_at'),
    }

    return sortMap[sortParam.value]
        ? list.sort(sortMap[sortParam.value])
        : list
}

const filteredQuestions = computed(() => {
    let filtered = localQuestions.value || []
    const query = normalize(searchQuery.value)

    if (selectedQuizId.value && !props.useServerProcessing) {
        filtered = filtered.filter(question =>
            Number(question.school_quiz_id) === Number(selectedQuizId.value)
        )
    }

    if (!query) {
        return sortQuestions(filtered)
    }

    filtered = filtered.filter((question) => {
        const values = [
            getQuestionText(question),
            getQuestionExplanation(question),
            question?.question_type,
            getQuizTitle(question),
            question?.quiz?.slug,
        ]

        return values.some(value => normalize(value).includes(query))
    })

    return sortQuestions(filtered)
})

const paginatedQuestions = computed(() => {
    const per = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * per

    return filteredQuestions.value.slice(start, start + per)
})

const displayedQuestions = computed(() => {
    return props.useServerProcessing
        ? questionsList.value
        : paginatedQuestions.value
})

watch([itemsPerPage, searchQuery, selectedQuizId], () => {
    currentPage.value = 1
})

const showConfirmDeleteModal = ref(false)
const questionToDeleteId = ref(null)
const questionToDeleteTitle = ref('')

const confirmDelete = (questionOrId, title = null) => {
    if (typeof questionOrId === 'object') {
        questionToDeleteId.value = questionOrId.id
        questionToDeleteTitle.value = title || getQuestionText(questionOrId)
    } else {
        questionToDeleteId.value = questionOrId
        questionToDeleteTitle.value = title || `ID: ${questionOrId}`
    }

    showConfirmDeleteModal.value = true
}

const closeModal = () => {
    showConfirmDeleteModal.value = false
    questionToDeleteId.value = null
    questionToDeleteTitle.value = ''
}

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

const patchQuestion = (questionId, payload) => {
    const index = localQuestions.value.findIndex(question => question.id === questionId)

    if (index !== -1) {
        localQuestions.value[index] = {
            ...localQuestions.value[index],
            ...payload,
        }
    }
}

const selectedQuestions = ref([])

const toggleAll = (payload) => {
    const checked = payload?.checked ?? payload?.target?.checked ?? false
    const ids = payload?.ids ?? displayedQuestions.value.map(question => question.id)

    if (checked) {
        selectedQuestions.value = [...new Set([...selectedQuestions.value, ...ids])]
    } else {
        selectedQuestions.value = selectedQuestions.value.filter(id => !ids.includes(id))
    }
}

const toggleSelectQuestion = (id) => {
    const index = selectedQuestions.value.indexOf(id)

    if (index > -1) {
        selectedQuestions.value.splice(index, 1)
    } else {
        selectedQuestions.value.push(id)
    }
}

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

const bulkDelete = () => {
    if (!selectedQuestions.value.length) {
        toast.warning('Выберите хотя бы один вопрос для удаления.')
        return
    }

    if (!confirm('Вы уверены, что хотите удалить выбранные вопросы?')) return

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
            toast.error(errors[errorKey] || 'Произошла ошибка при удалении вопросов.')
        },
    })
}

const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        toggleAll({ target: { checked: true } })
    } else if (action === 'deselectAll') {
        toggleAll({ target: { checked: false } })
    } else if (action === 'activate') {
        bulkToggleActivity(true)
    } else if (action === 'deactivate') {
        bulkToggleActivity(false)
    } else if (action === 'delete') {
        bulkDelete()
    }

    event.target.value = ''
}

const toggleActivity = (question) => {
    const newActivity = !question.activity
    const questionTitle = getQuestionText(question)
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
                class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95">
                <div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3">
                    <DefaultButton
                        :href="route('admin.schoolQuizQuestions.create', {
                            ...(selectedQuizId ? { school_quiz_id: selectedQuizId } : {}),
                        })"
                    >
                        {{ t('addQuizQuestion') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminSchoolQuizQuestionsProcessingMode"
                        :mode="adminSchoolQuizQuestionsProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="questionsCount"
                    />
                </div>

                <SearchInput
                    v-if="questionsCount && !useServerProcessing"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <ServerSearchInput
                    v-if="questionsCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <div
                    v-if="quizzes.length"
                    class="flex items-center justify-center gap-2 my-3"
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
                        class="w-full rounded-sm border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 text-xs font-semibold px-3 py-1"
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
                        v-if="!useServerProcessing"
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <ServerItemsPerPageSelect
                        v-else
                        :items-per-page="itemsPerPage"
                        update-route="admin.settings.updateAdminCountSchoolQuizQuestions"
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
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredQuestions.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="questions"
                    />
                </div>

                <QuizQuestionTable
                    v-if="viewMode === 'table'"
                    :questions="displayedQuestions"
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
                    :questions="displayedQuestions"
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
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredQuestions.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="questions"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            :onCancel="closeModal"
            :onConfirm="deleteQuestion"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
            @close="closeModal"
        />
    </AdminLayout>
</template>
