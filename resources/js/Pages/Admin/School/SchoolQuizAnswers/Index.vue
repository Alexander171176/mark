<script setup>
/**
 * Список ответов вопросов викторин
 * - режимы обработки: frontend | server | auto
 * - фильтр по квизу и вопросу
 * - локальный/серверный поиск
 * - локальная/серверная пагинация
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

import BulkActionSelect from '@/Components/Admin/School/SchoolQuizAnswer/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/School/SchoolQuizAnswer/Sort/SortSelect.vue'
import QuizAnswerTable from '@/Components/Admin/School/SchoolQuizAnswer/Table/QuizAnswerTable.vue'
import QuizAnswerCardGrid from '@/Components/Admin/School/SchoolQuizAnswer/View/QuizAnswerCardGrid.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },

    adminSchoolQuizAnswersProcessingMode: { type: String, default: 'frontend' },
    useServerProcessing: { type: Boolean, default: false },

    answers: { type: [Array, Object], default: () => [] },
    answersCount: { type: Number, default: 0 },

    adminSchoolQuizAnswersPerPage: { type: Number, default: 10 },
    adminSchoolQuizAnswersDefaultSort: { type: String, default: 'idDesc' },

    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },

    filters: { type: Object, default: () => ({}) },

    quizzes: { type: Array, default: () => [] },
    questions: { type: Array, default: () => [] },

    currentQuizId: { type: Number, default: null },
    currentQuestionId: { type: Number, default: null },

    errors: { type: Object, default: () => ({}) },
})

const viewMode = ref(localStorage.getItem('admin_view_mode_quiz_answers') || 'table')

watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode_quiz_answers', val)
})

const answersList = computed(() => {
    if (Array.isArray(props.answers)) return props.answers
    if (Array.isArray(props.answers?.data)) return props.answers.data

    return []
})

const localAnswers = ref([])

watch(
    answersList,
    (newVal) => {
        localAnswers.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

const selectedQuizId = ref(props.currentQuizId ?? props.filters?.school_quiz_id ?? null)
const selectedQuestionId = ref(props.currentQuestionId ?? props.filters?.school_quiz_question_id ?? null)

const quizOptions = computed(() => props.quizzes || [])
const questionOptions = computed(() => props.questions || [])

const stripHtml = (html = '') => {
    return (html || '')
        .replace(/<\/p>/gi, ' ')
        .replace(/<br\s*\/?>/gi, ' ')
        .replace(/<[^>]+>/g, '')
        .replace(/&nbsp;/gi, ' ')
        .replace(/\s+/g, ' ')
        .trim()
}

const shortText = (html, limit = 100) => {
    const clean = stripHtml(html)

    return clean.length > limit ? `${clean.slice(0, limit)}…` : clean
}

const quizOptionLabel = (quiz) => {
    if (!quiz) return ''

    const idPart = `[ID: ${quiz.id}]`
    const titlePart = quiz.title || quiz.slug || `#${quiz.id}`

    const context = [
        quiz.course?.title ? `Курс: ${quiz.course.title}` : null,
        quiz.module?.title ? `Модуль: ${quiz.module.title}` : null,
        quiz.lesson?.title ? `Урок: ${quiz.lesson.title}` : null,
    ].filter(Boolean).join(' / ')

    return context ? `${idPart} ${titlePart} — ${context}` : `${idPart} ${titlePart}`
}

const questionOptionLabel = (question) => {
    if (!question) return ''

    const idPart = `[ID: ${question.id}]`
    const textPart = question.question_text
        ? shortText(question.question_text, 120)
        : `#${question.id}`

    return question.school_quiz_id
        ? `${idPart} ${textPart} — Quiz ID: ${question.school_quiz_id}`
        : `${idPart} ${textPart}`
}

const reloadWithFilters = () => {
    const params = {
        ...Object.fromEntries(new URLSearchParams(window.location.search)),
        page: undefined,
    }

    if (selectedQuizId.value) {
        params.school_quiz_id = selectedQuizId.value
    } else {
        delete params.school_quiz_id
    }

    if (selectedQuestionId.value) {
        params.school_quiz_question_id = selectedQuestionId.value
    } else {
        delete params.school_quiz_question_id
    }

    router.get(route('admin.schoolQuizAnswers.index'), params, {
        preserveScroll: true,
        preserveState: false,
        replace: true,
    })
}

const handleQuizFilterChange = () => {
    selectedQuestionId.value = null
    reloadWithFilters()
}

const handleQuestionFilterChange = () => {
    reloadWithFilters()
}

const itemsPerPage = ref(props.adminSchoolQuizAnswersPerPage || 10)

watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountQuizAnswers'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

const sortParam = ref(
    props.sortParam ||
    props.adminSchoolQuizAnswersDefaultSort ||
    'idDesc'
)

watch(sortParam, (newVal) => {
    currentPage.value = 1

    router.put(route('admin.settings.updateAdminSortQuizAnswers'), { value: newVal }, {
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

        onError: (errors) => {
            toast.error(errors.value || 'Ошибка обновления сортировки.')
        },
    })
})

const currentPage = ref(1)
const searchQuery = ref(props.search || '')

const normalize = (value) => stripHtml(value ?? '').toString().trim().toLowerCase()

const safeNumber = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

const safeDate = (value) => {
    const time = new Date(value || 0).getTime()
    return Number.isFinite(time) ? time : 0
}

const getAnswerText = (answer) => {
    return answer?.text
        || answer?.translation?.text
        || answer?.translations?.[0]?.text
        || `ID: ${answer?.id}`
}

const getAnswerExplanation = (answer) => {
    return answer?.explanation
        || answer?.translation?.explanation
        || answer?.translations?.[0]?.explanation
        || ''
}

const getNestedTitle = (item) => {
    return item?.title
        || item?.name
        || item?.question_text
        || item?.translation?.title
        || item?.translation?.name
        || item?.translation?.question_text
        || item?.translations?.[0]?.title
        || item?.translations?.[0]?.name
        || item?.translations?.[0]?.question_text
        || ''
}

const getQuizTitle = (answer) => getNestedTitle(answer?.quiz)
const getQuestionText = (answer) => getNestedTitle(answer?.question)

const byNumberAsc = (field) => (a, b) =>
    safeNumber(a?.[field]) - safeNumber(b?.[field])
    || safeNumber(a?.id) - safeNumber(b?.id)

const byNumberDesc = (field) => (a, b) =>
    safeNumber(b?.[field]) - safeNumber(a?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

const byDateAsc = (field) => (a, b) =>
    safeDate(a?.[field]) - safeDate(b?.[field])
    || safeNumber(a?.id) - safeNumber(b?.id)

const byDateDesc = (field) => (a, b) =>
    safeDate(b?.[field]) - safeDate(a?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

const sortAnswers = (items) => {
    const list = (items || []).slice()

    if (sortParam.value === 'activity') return list.filter(item => !!item.activity)
    if (sortParam.value === 'inactive') return list.filter(item => !item.activity)

    if (sortParam.value === 'correct') return list.filter(item => !!item.is_correct)
    if (sortParam.value === 'incorrect') return list.filter(item => !item.is_correct)

    const sortMap = {
        idAsc: byNumberAsc('id'),
        idDesc: byNumberDesc('id'),

        sortAsc: byNumberAsc('sort'),
        sortDesc: byNumberDesc('sort'),

        textAsc: (a, b) =>
            normalize(getAnswerText(a)).localeCompare(normalize(getAnswerText(b)), props.currentLocale)
            || safeNumber(a?.id) - safeNumber(b?.id),

        textDesc: (a, b) =>
            normalize(getAnswerText(b)).localeCompare(normalize(getAnswerText(a)), props.currentLocale)
            || safeNumber(b?.id) - safeNumber(a?.id),

        quizTitleAsc: (a, b) =>
            normalize(getQuizTitle(a)).localeCompare(normalize(getQuizTitle(b)), props.currentLocale)
            || safeNumber(a?.id) - safeNumber(b?.id),

        quizTitleDesc: (a, b) =>
            normalize(getQuizTitle(b)).localeCompare(normalize(getQuizTitle(a)), props.currentLocale)
            || safeNumber(b?.id) - safeNumber(a?.id),

        questionTextAsc: (a, b) =>
            normalize(getQuestionText(a)).localeCompare(normalize(getQuestionText(b)), props.currentLocale)
            || safeNumber(a?.id) - safeNumber(b?.id),

        questionTextDesc: (a, b) =>
            normalize(getQuestionText(b)).localeCompare(normalize(getQuestionText(a)), props.currentLocale)
            || safeNumber(b?.id) - safeNumber(a?.id),

        weightAsc: byNumberAsc('weight'),
        weightDesc: byNumberDesc('weight'),

        attemptItemsAsc: byNumberAsc('attempt_items_count'),
        attemptItemsDesc: byNumberDesc('attempt_items_count'),

        correctAsc: byNumberAsc('is_correct'),
        correctDesc: byNumberDesc('is_correct'),

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

const filteredAnswers = computed(() => {
    let filtered = localAnswers.value || []
    const query = normalize(searchQuery.value)

    if (selectedQuizId.value && !props.useServerProcessing) {
        filtered = filtered.filter(answer =>
            Number(answer.school_quiz_id) === Number(selectedQuizId.value)
        )
    }

    if (selectedQuestionId.value && !props.useServerProcessing) {
        filtered = filtered.filter(answer =>
            Number(answer.school_quiz_question_id) === Number(selectedQuestionId.value)
        )
    }

    if (!query) {
        return sortAnswers(filtered)
    }

    filtered = filtered.filter((answer) => {
        const values = [
            getAnswerText(answer),
            getAnswerExplanation(answer),
            getQuizTitle(answer),
            answer?.quiz?.slug,
            getQuestionText(answer),
            answer?.question?.explanation,
        ]

        return values.some(value => normalize(value).includes(query))
    })

    return sortAnswers(filtered)
})

const paginatedAnswers = computed(() => {
    const per = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * per

    return filteredAnswers.value.slice(start, start + per)
})

const displayedAnswers = computed(() => {
    return props.useServerProcessing
        ? answersList.value
        : paginatedAnswers.value
})

watch([itemsPerPage, searchQuery, selectedQuizId, selectedQuestionId], () => {
    currentPage.value = 1
})

const showConfirmDeleteModal = ref(false)
const answerToDeleteId = ref(null)
const answerToDeleteTitle = ref('')

const confirmDelete = (answerOrId, title = null) => {
    if (typeof answerOrId === 'object') {
        answerToDeleteId.value = answerOrId.id
        answerToDeleteTitle.value = title || stripHtml(getAnswerText(answerOrId)) || `ID: ${answerOrId.id}`
    } else {
        answerToDeleteId.value = answerOrId
        answerToDeleteTitle.value = title || `ID: ${answerOrId}`
    }

    showConfirmDeleteModal.value = true
}

const closeModal = () => {
    showConfirmDeleteModal.value = false
    answerToDeleteId.value = null
    answerToDeleteTitle.value = ''
}

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

const patchAnswer = (answerId, payload) => {
    const index = localAnswers.value.findIndex(answer => answer.id === answerId)

    if (index !== -1) {
        localAnswers.value[index] = {
            ...localAnswers.value[index],
            ...payload,
        }
    }
}

const selectedAnswers = ref([])

const toggleAll = (payload) => {
    const checked = payload?.checked ?? payload?.target?.checked ?? false
    const ids = payload?.ids ?? displayedAnswers.value.map(answer => answer.id)

    if (checked) {
        selectedAnswers.value = [...new Set([...selectedAnswers.value, ...ids])]
    } else {
        selectedAnswers.value = selectedAnswers.value.filter(id => !ids.includes(id))
    }
}

const toggleSelectAnswer = (id) => {
    const index = selectedAnswers.value.indexOf(id)

    if (index > -1) {
        selectedAnswers.value.splice(index, 1)
    } else {
        selectedAnswers.value.push(id)
    }
}

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
            toast.error(errors?.message || errors?.general || 'Не удалось обновить порядок ответов.')

            router.reload({
                only: ['answers'],
                preserveScroll: true,
            })
        },
    })
}

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

const toggleActivity = (answer) => {
    const newActivity = !answer.activity
    const answerTitle = stripHtml(getAnswerText(answer)) || `ID: ${answer.id}`
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
                <div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3">
                    <DefaultButton :href="route('admin.schoolQuizAnswers.create')">
                        <template #icon>
                        <svg class="w-4 h-4 fill-current opacity-50 shrink-0"
                             viewBox="0 0 16 16">
                            <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"></path>
                        </svg>
                    </template>
                        {{ t('addQuizAnswer') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminSchoolQuizAnswersProcessingMode"
                        :mode="adminSchoolQuizAnswersProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="answersCount"
                    />
                </div>

                <SearchInput
                    v-if="answersCount && !useServerProcessing"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <ServerSearchInput
                    v-if="answersCount && useServerProcessing"
                    v-model="searchQuery"
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
                        v-if="!useServerProcessing"
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <ServerItemsPerPageSelect
                        v-else
                        :items-per-page="itemsPerPage"
                        update-route="admin.settings.updateAdminCountQuizAnswers"
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
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredAnswers.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="answers"
                    />
                </div>

                <QuizAnswerTable
                    v-if="viewMode === 'table'"
                    :answers="displayedAnswers"
                    :selected-answers="selectedAnswers"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectAnswer"
                    @toggle-all="toggleAll"
                />

                <QuizAnswerCardGrid
                    v-else
                    :answers="displayedAnswers"
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
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredAnswers.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="answers"
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
