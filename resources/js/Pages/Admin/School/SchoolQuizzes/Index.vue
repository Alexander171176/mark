<script setup>
/**
 * Список квизов школы
 * - режимы обработки: frontend | server | auto
 * - локальный поиск/сортировка/пагинация
 * - серверный поиск/сортировка/пагинация
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
import CountTable from '@/Components/Admin/UI/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/UI/Select/ItemsPerPageSelect.vue'
import ServerItemsPerPageSelect from '@/Components/Admin/UI/Select/ServerItemsPerPageSelect.vue'
import DangerModal from '@/Components/Admin/UI/Modal/DangerModal.vue'
import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue'
import AdminServerPagination from '@/Components/Admin/UI/Pagination/AdminServerPagination.vue'
import ToggleViewButton from '@/Components/Admin/UI/Buttons/ToggleViewButton.vue'
import ProcessingModeSwitcher from '@/Components/Admin/UI/Processing/ProcessingModeSwitcher.vue'

import BulkActionSelect from '@/Components/Admin/School/SchoolQuiz/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/School/SchoolQuiz/Sort/SortSelect.vue'
import QuizTable from '@/Components/Admin/School/SchoolQuiz/Table/QuizTable.vue'
import QuizCardGrid from '@/Components/Admin/School/SchoolQuiz/View/QuizCardGrid.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },

    adminSchoolQuizzesProcessingMode: { type: String, default: 'frontend' },
    useServerProcessing: { type: Boolean, default: false },

    quizzes: { type: [Array, Object], default: () => [] },
    quizzesCount: { type: Number, default: 0 },

    adminSchoolQuizzesPerPage: { type: Number, default: 6 },
    adminSchoolQuizzesDefaultSort: { type: String, default: 'idDesc' },

    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },

    errors: { type: Object, default: () => ({}) },
})

const viewMode = ref(localStorage.getItem('admin_view_mode_quizzes') || 'table')

watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode_quizzes', val)
})

const quizzesList = computed(() => {
    if (Array.isArray(props.quizzes)) return props.quizzes
    if (Array.isArray(props.quizzes?.data)) return props.quizzes.data
    return []
})

const localQuizzes = ref([])

watch(
    quizzesList,
    (newVal) => {
        localQuizzes.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

const itemsPerPage = ref(props.adminSchoolQuizzesPerPage || 6)

watch(itemsPerPage, (newVal) => {
    router.put(
        route('admin.settings.updateAdminCountSchoolQuizzes'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
            onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
        }
    )
})

const sortParam = ref(props.sortParam || props.adminSchoolQuizzesDefaultSort || 'idDesc')

watch(sortParam, (newVal) => {
    currentPage.value = 1

    router.put(
        route('admin.settings.updateAdminSortSchoolQuizzes'),
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

/* ==========================================================
 * ИЗВЛЕЧЕНИЕ ДАННЫХ ИЗ RESOURCES
 * ========================================================== */

/** Заголовок квиза */
const getQuizTitle = (quiz) => {
    return quiz?.translation?.title
        || `ID: ${quiz?.id}`
}

/** Краткое описание */
const getQuizShort = (quiz) => {
    return quiz?.translation?.short
        || ''
}

/** Полное описание */
const getQuizDescription = (quiz) => {
    return quiz?.translation?.description
        || ''
}

/** Заголовок связанной сущности */
const getNestedTitle = (item) => {
    return item?.translation?.title
        || item?.translation?.name
        || ''
}

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

const byStringAsc = (field) => (a, b) =>
    normalize(a?.[field]).localeCompare(normalize(b?.[field]), props.currentLocale)
    || safeNumber(a?.id) - safeNumber(b?.id)

const byStringDesc = (field) => (a, b) =>
    normalize(b?.[field]).localeCompare(normalize(a?.[field]), props.currentLocale)
    || safeNumber(b?.id) - safeNumber(a?.id)

const sortQuizzes = (items) => {
    const list = (items || []).slice()

    if (sortParam.value === 'activity') return list.filter(item => !!item.activity)
    if (sortParam.value === 'inactive') return list.filter(item => !item.activity)

    if (sortParam.value === 'left') return list.filter(item => !!item.left)
    if (sortParam.value === 'noLeft') return list.filter(item => !item.left)

    if (sortParam.value === 'main') return list.filter(item => !!item.main)
    if (sortParam.value === 'noMain') return list.filter(item => !item.main)

    if (sortParam.value === 'right') return list.filter(item => !!item.right)
    if (sortParam.value === 'noRight') return list.filter(item => !item.right)

    if (sortParam.value === 'graded') return list.filter(item => item.type === 'graded')
    if (sortParam.value === 'practice') return list.filter(item => item.type === 'practice')

    const sortMap = {
        idAsc: byNumberAsc('id'),
        idDesc: byNumberDesc('id'),

        sortAsc: byNumberAsc('sort'),
        sortDesc: byNumberDesc('sort'),

        titleAsc: (a, b) =>
            normalize(getQuizTitle(a)).localeCompare(normalize(getQuizTitle(b)), props.currentLocale)
            || safeNumber(a?.id) - safeNumber(b?.id),

        titleDesc: (a, b) =>
            normalize(getQuizTitle(b)).localeCompare(normalize(getQuizTitle(a)), props.currentLocale)
            || safeNumber(b?.id) - safeNumber(a?.id),

        slugAsc: byStringAsc('slug'),
        slugDesc: byStringDesc('slug'),

        typeAsc: byStringAsc('type'),
        typeDesc: byStringDesc('type'),

        passScoreAsc: byNumberAsc('pass_score'),
        passScoreDesc: byNumberDesc('pass_score'),

        attemptsLimitAsc: byNumberAsc('attempts_limit'),
        attemptsLimitDesc: byNumberDesc('attempts_limit'),

        timeLimitAsc: byNumberAsc('time_limit_minutes'),
        timeLimitDesc: byNumberDesc('time_limit_minutes'),

        questionsAsc: byNumberAsc('questions_count'),
        questionsDesc: byNumberDesc('questions_count'),

        attemptsAsc: byNumberAsc('attempts_count'),
        attemptsDesc: byNumberDesc('attempts_count'),

        imagesAsc: byNumberAsc('images_count'),
        imagesDesc: byNumberDesc('images_count'),

        activityAsc: byNumberAsc('activity'),
        activityDesc: byNumberDesc('activity'),

        publishedAtAsc: byDateAsc('published_at'),
        publishedAtDesc: byDateDesc('published_at'),

        createdAtAsc: byDateAsc('created_at'),
        createdAtDesc: byDateDesc('created_at'),

        updatedAtAsc: byDateAsc('updated_at'),
        updatedAtDesc: byDateDesc('updated_at'),

        courseTitleAsc: (a, b) =>
            normalize(getNestedTitle(a?.course)).localeCompare(normalize(getNestedTitle(b?.course)), props.currentLocale)
            || safeNumber(a?.id) - safeNumber(b?.id),

        courseTitleDesc: (a, b) =>
            normalize(getNestedTitle(b?.course)).localeCompare(normalize(getNestedTitle(a?.course)), props.currentLocale)
            || safeNumber(b?.id) - safeNumber(a?.id),

        moduleTitleAsc: (a, b) =>
            normalize(getNestedTitle(a?.module)).localeCompare(normalize(getNestedTitle(b?.module)), props.currentLocale)
            || safeNumber(a?.id) - safeNumber(b?.id),

        moduleTitleDesc: (a, b) =>
            normalize(getNestedTitle(b?.module)).localeCompare(normalize(getNestedTitle(a?.module)), props.currentLocale)
            || safeNumber(b?.id) - safeNumber(a?.id),

        lessonTitleAsc: (a, b) =>
            normalize(getNestedTitle(a?.lesson)).localeCompare(normalize(getNestedTitle(b?.lesson)), props.currentLocale)
            || safeNumber(a?.id) - safeNumber(b?.id),

        lessonTitleDesc: (a, b) =>
            normalize(getNestedTitle(b?.lesson)).localeCompare(normalize(getNestedTitle(a?.lesson)), props.currentLocale)
            || safeNumber(b?.id) - safeNumber(a?.id),
    }

    return sortMap[sortParam.value]
        ? list.sort(sortMap[sortParam.value])
        : list
}

const filteredQuizzes = computed(() => {
    let filtered = localQuizzes.value || []
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortQuizzes(filtered)
    }

    filtered = filtered.filter((quiz) => {
        const values = [
            getQuizTitle(quiz),
            getQuizShort(quiz),
            getQuizDescription(quiz),
            quiz?.slug,
            quiz?.type,

            getNestedTitle(quiz?.course),
            quiz?.course?.slug,

            getNestedTitle(quiz?.module),
            quiz?.module?.slug,

            getNestedTitle(quiz?.lesson),
            quiz?.lesson?.slug,
        ]

        return values.some(value => normalize(value).includes(query))
    })

    return sortQuizzes(filtered)
})

const paginatedQuizzes = computed(() => {
    const per = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * per

    return filteredQuizzes.value.slice(start, start + per)
})

const displayedQuizzes = computed(() => {
    return props.useServerProcessing
        ? quizzesList.value
        : paginatedQuizzes.value
})

watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

const showConfirmDeleteModal = ref(false)
const quizToDeleteId = ref(null)
const quizToDeleteTitle = ref('')

const confirmDelete = (quizOrId, title = null) => {
    if (typeof quizOrId === 'object') {
        quizToDeleteId.value = quizOrId.id
        quizToDeleteTitle.value = title || getQuizTitle(quizOrId)
    } else {
        quizToDeleteId.value = quizOrId
        quizToDeleteTitle.value = title || `ID: ${quizOrId}`
    }

    showConfirmDeleteModal.value = true
}

const closeModal = () => {
    showConfirmDeleteModal.value = false
    quizToDeleteId.value = null
    quizToDeleteTitle.value = ''
}

const deleteQuiz = () => {
    if (quizToDeleteId.value === null) return

    const idToDelete = quizToDeleteId.value
    const titleToDelete = quizToDeleteTitle.value

    router.delete(route('admin.schoolQuizzes.destroy', {
        schoolQuiz: idToDelete,
    }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => toast.success(`Квиз "${titleToDelete || 'ID: ' + idToDelete}" удалён.`),
        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            const errorMsg = errors.general || errors[errorKey] || 'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Квиз: ${titleToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => closeModal(),
    })
}

const patchQuiz = (quizId, payload) => {
    const index = localQuizzes.value.findIndex(quiz => quiz.id === quizId)

    if (index !== -1) {
        localQuizzes.value[index] = {
            ...localQuizzes.value[index],
            ...payload,
        }
    }
}

const selectedQuizzes = ref([])

const toggleAll = (payload) => {
    const checked = payload?.checked ?? payload?.target?.checked ?? false
    const ids = payload?.ids ?? displayedQuizzes.value.map((quiz) => quiz.id)

    if (checked) {
        selectedQuizzes.value = [...new Set([...selectedQuizzes.value, ...ids])]
    } else {
        selectedQuizzes.value = selectedQuizzes.value.filter((id) => !ids.includes(id))
    }
}

const toggleSelectQuiz = (id) => {
    const index = selectedQuizzes.value.indexOf(id)

    if (index > -1) {
        selectedQuizzes.value.splice(index, 1)
    } else {
        selectedQuizzes.value.push(id)
    }
}

const handleSortOrderUpdate = (orderedIds) => {
    const startSort = (currentPage.value - 1) * itemsPerPage.value

    const items = orderedIds.map((id, index) => ({
        id,
        sort: startSort + index + 1,
    }))

    if (!items.length) return

    router.put(route('admin.actions.schoolQuizzes.updateSortBulk'), { items }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.success('Порядок квизов успешно обновлён.'),
        onError: (errors) => {
            toast.error(errors?.message || errors?.general || 'Не удалось обновить порядок квизов.')

            router.reload({
                only: ['quizzes'],
                preserveScroll: true,
            })
        },
    })
}

const bulkToggleActivity = (newActivity) => {
    if (!selectedQuizzes.value.length) {
        toast.warning('Выберите квизы для активации/деактивации.')
        return
    }

    const idsToUpdate = [...selectedQuizzes.value]

    router.put(route('admin.actions.schoolQuizzes.bulkUpdateActivity'), {
        ids: idsToUpdate,
        activity: newActivity,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            idsToUpdate.forEach(id => patchQuiz(id, { activity: newActivity }))
            selectedQuizzes.value = []
            toast.success('Активность выбранных квизов обновлена.')
        },
        onError: (errors) => {
            toast.error(errors?.ids || errors?.activity || errors?.general || 'Ошибка массового обновления активности.')
        },
    })
}

const bulkToggleFlag = (field, routeName, value) => {
    if (!selectedQuizzes.value.length) {
        toast.warning('Выберите квизы.')
        return
    }

    const idsToUpdate = [...selectedQuizzes.value]

    router.put(route(routeName), {
        ids: idsToUpdate,
        [field]: value,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            idsToUpdate.forEach(id => patchQuiz(id, { [field]: value }))
            selectedQuizzes.value = []
            toast.success('Выбранные квизы обновлены.')
        },
        onError: (errors) => {
            toast.error(errors?.ids || errors?.[field] || errors?.general || 'Ошибка массового обновления.')
        },
    })
}

const bulkDelete = () => {
    if (!selectedQuizzes.value.length) {
        toast.warning('Выберите хотя бы один квиз для удаления.')
        return
    }

    if (!confirm('Вы уверены, что хотите удалить выбранные квизы?')) return

    router.delete(route('admin.actions.schoolQuizzes.bulkDestroy'), {
        data: { ids: selectedQuizzes.value },
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            selectedQuizzes.value = []
            toast.success('Массовое удаление квизов успешно завершено.')
        },
        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            toast.error(errors[errorKey] || 'Произошла ошибка при удалении квизов.')
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
    } else if (action === 'leftOn') {
        bulkToggleFlag('left', 'admin.actions.schoolQuizzes.bulkUpdateLeft', true)
    } else if (action === 'leftOff') {
        bulkToggleFlag('left', 'admin.actions.schoolQuizzes.bulkUpdateLeft', false)
    } else if (action === 'mainOn') {
        bulkToggleFlag('main', 'admin.actions.schoolQuizzes.bulkUpdateMain', true)
    } else if (action === 'mainOff') {
        bulkToggleFlag('main', 'admin.actions.schoolQuizzes.bulkUpdateMain', false)
    } else if (action === 'rightOn') {
        bulkToggleFlag('right', 'admin.actions.schoolQuizzes.bulkUpdateRight', true)
    } else if (action === 'rightOff') {
        bulkToggleFlag('right', 'admin.actions.schoolQuizzes.bulkUpdateRight', false)
    } else if (action === 'delete') {
        bulkDelete()
    }

    event.target.value = ''
}

const toggleActivity = (quiz) => {
    const newActivity = !quiz.activity
    const quizTitle = getQuizTitle(quiz)
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(route('admin.actions.schoolQuizzes.updateActivity', {
        schoolQuiz: quiz.id,
    }), {
        activity: newActivity,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            patchQuiz(quiz.id, { activity: newActivity })
            quiz.activity = newActivity
            toast.success(`Квиз "${quizTitle}" ${actionText}.`)
        },
        onError: (errors) => {
            toast.error(errors?.activity || errors?.general || `Ошибка изменения активности для квиза "${quizTitle}".`)
        },
    })
}

const togglePlacement = (quiz, field, routeName) => {
    const newValue = !quiz[field]
    const quizTitle = getQuizTitle(quiz)

    router.put(route(routeName, {
        schoolQuiz: quiz.id,
    }), {
        [field]: newValue,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            patchQuiz(quiz.id, { [field]: newValue })
            quiz[field] = newValue
            toast.success(`Поле "${field}" для квиза "${quizTitle}" обновлено.`)
        },
        onError: (errors) => {
            toast.error(errors?.[field] || errors?.general || `Ошибка обновления поля "${field}".`)
        },
    })
}

const toggleLeft = (quiz) =>
    togglePlacement(quiz, 'left', 'admin.actions.schoolQuizzes.updateLeft')

const toggleMain = (quiz) =>
    togglePlacement(quiz, 'main', 'admin.actions.schoolQuizzes.updateMain')

const toggleRight = (quiz) =>
    togglePlacement(quiz, 'right', 'admin.actions.schoolQuizzes.updateRight')

const cloneQuiz = (quiz) => {
    const quizId = quiz?.id
    const quizTitle = getQuizTitle(quiz)

    if (!quizId) {
        toast.error('Не удалось определить квиз для клонирования.')
        return
    }

    if (!confirm(`Вы уверены, что хотите клонировать квиз "${quizTitle}"?`)) return

    router.post(route('admin.actions.schoolQuizzes.clone', {
        schoolQuiz: quizId,
    }), {}, {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => toast.success(`Квиз "${quizTitle}" успешно клонирован.`),
        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            toast.error(errors[errorKey] || `Ошибка клонирования квиза "${quizTitle}".`)
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('quizzes')">
        <template #header>
            <TitlePage>{{ t('quizzes') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3">
                    <DefaultButton :href="route('admin.schoolQuizzes.create')">
                        {{ t('addQuiz') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminSchoolQuizzesProcessingMode"
                        :mode="adminSchoolQuizzesProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="quizzesCount"
                    />
                </div>

                <SearchInput
                    v-if="quizzesCount && !useServerProcessing"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <ServerSearchInput
                    v-if="quizzesCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <div
                    v-if="quizzesCount"
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
                        update-route="admin.settings.updateAdminCountSchoolQuizzes"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="val => sortParam = val"
                    />
                </div>

                <div
                    v-if="quizzesCount"
                    class="flex flex-col lg:flex-row items-center justify-between gap-3"
                >
                    <CountTable>{{ quizzesCount }}</CountTable>
                    <BulkActionSelect @change="handleBulkAction" />
                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div
                    v-if="quizzesCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredQuizzes.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="quizzes"
                    />
                </div>

                <QuizTable
                    v-if="viewMode === 'table'"
                    :quizzes="displayedQuizzes"
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
                    :quizzes="displayedQuizzes"
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
                    v-if="quizzesCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredQuizzes.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="quizzes"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            :onCancel="closeModal"
            :onConfirm="deleteQuiz"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
            @close="closeModal"
        />
    </AdminLayout>
</template>
