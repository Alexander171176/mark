<script setup>
import { computed, defineProps, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import SearchInput from '@/Components/Admin/UI/Search/SearchInput.vue'
import ServerSearchInput from '@/Components/Admin/UI/Search/ServerSearchInput.vue'
import CountTable from '@/Components/Admin/UI/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/UI/Select/ItemsPerPageSelect.vue'
import ServerItemsPerPageSelect from '@/Components/Admin/UI/Select/ServerItemsPerPageSelect.vue'
import DangerModal from '@/Components/Admin/UI/Modal/DangerModal.vue'
import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue'
import AdminServerPagination from '@/Components/Admin/UI/Pagination/AdminServerPagination.vue'
import ToggleViewButton from '@/Components/Admin/UI/Buttons/ToggleViewButton.vue'
import ProcessingModeSwitcher from '@/Components/Admin/UI/Processing/ProcessingModeSwitcher.vue'

import BulkActionSelect from '@/Components/Admin/School/SchoolQuizAttemptItem/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/School/SchoolQuizAttemptItem/Sort/SortSelect.vue'
import QuizAttemptItemTable from '@/Components/Admin/School/SchoolQuizAttemptItem/Table/QuizAttemptItemTable.vue'
import QuizAttemptItemCardGrid from '@/Components/Admin/School/SchoolQuizAttemptItem/View/QuizAttemptItemCardGrid.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    currentLocale: { type: String, default: '' },

    useServerProcessing: { type: Boolean, default: false },
    adminSchoolQuizAttemptItemsProcessingMode: { type: String, default: 'frontend' },

    items: { type: [Array, Object], default: () => [] },
    itemsCount: { type: Number, default: 0 },

    filters: { type: Object, default: () => ({}) },

    adminSchoolQuizAttemptItemsPerPage: { type: Number, default: 20 },
    adminSchoolQuizAttemptItemsDefaultSort: { type: String, default: 'idDesc' },

    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },

    attempts: { type: Array, default: () => [] },
    questions: { type: Array, default: () => [] },
    answers: { type: Array, default: () => [] },
})

const viewMode = ref(localStorage.getItem('admin_view_mode_quiz_attempt_items') || 'table')

watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode_quiz_attempt_items', val)
})

const itemsList = computed(() => {
    if (Array.isArray(props.items)) return props.items
    if (Array.isArray(props.items?.data)) return props.items.data
    return []
})

const localItems = ref([])

watch(
    itemsList,
    (newVal) => {
        localItems.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

const itemsPerPage = ref(props.adminSchoolQuizAttemptItemsPerPage ?? 20)

watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountQuizAttemptItems'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors?.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

const sortParam = ref(
    props.sortParam ||
    props.adminSchoolQuizAttemptItemsDefaultSort ||
    'idDesc'
)

watch(sortParam, (newVal) => {
    currentPage.value = 1

    router.put(route('admin.settings.updateAdminSortQuizAttemptItems'), { value: newVal }, {
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
            const firstError = errors ? Object.values(errors)[0] : null
            toast.error(firstError || 'Ошибка обновления параметра сортировки.')
        },
    })
})

const currentPage = ref(1)
const searchQuery = ref(props.search || '')

const showConfirmDeleteModal = ref(false)
const itemToDelete = ref(null)

const stripHtml = (value = '') => {
    if (value === null || typeof value === 'undefined') return ''

    const html = typeof value === 'string' ? value : JSON.stringify(value)

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

const normalize = (value) => stripHtml(value).toString().trim().toLowerCase()

const safeNumber = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

const safeDate = (value) => {
    const time = new Date(value || 0).getTime()
    return Number.isFinite(time) ? time : 0
}

const getQuestionText = (item) => {
    return item?.question?.question_text
        || item?.question?.translation?.question_text
        || item?.question?.translations?.[0]?.question_text
        || ''
}

const getSelectedAnswerText = (item) => {
    return item?.selected_answer?.text
        || item?.selectedAnswer?.text
        || item?.selected_answer?.translation?.text
        || item?.selectedAnswer?.translation?.text
        || ''
}

const itemTitle = (item) => {
    if (!item) return ''

    return getQuestionText(item)
        ? stripHtml(getQuestionText(item))
        : `ID: ${item.id}`
}

const sortItems = (items) => {
    const list = (items || []).slice()

    const sortMap = {
        idAsc: (a, b) => safeNumber(a.id) - safeNumber(b.id),
        idDesc: (a, b) => safeNumber(b.id) - safeNumber(a.id),

        attemptIdAsc: (a, b) => safeNumber(a.school_quiz_attempt_id) - safeNumber(b.school_quiz_attempt_id),
        attemptIdDesc: (a, b) => safeNumber(b.school_quiz_attempt_id) - safeNumber(a.school_quiz_attempt_id),

        questionIdAsc: (a, b) => safeNumber(a.school_quiz_question_id) - safeNumber(b.school_quiz_question_id),
        questionIdDesc: (a, b) => safeNumber(b.school_quiz_question_id) - safeNumber(a.school_quiz_question_id),

        scoreAsc: (a, b) => safeNumber(a.score) - safeNumber(b.score),
        scoreDesc: (a, b) => safeNumber(b.score) - safeNumber(a.score),

        maxScoreAsc: (a, b) => safeNumber(a.max_score) - safeNumber(b.max_score),
        maxScoreDesc: (a, b) => safeNumber(b.max_score) - safeNumber(a.max_score),

        correctFirst: (a, b) => Number(!!b.is_correct) - Number(!!a.is_correct),
        wrongFirst: (a, b) => Number(!!a.is_correct) - Number(!!b.is_correct),

        questionTextAsc: (a, b) =>
            normalize(getQuestionText(a)).localeCompare(normalize(getQuestionText(b)), props.currentLocale),

        questionTextDesc: (a, b) =>
            normalize(getQuestionText(b)).localeCompare(normalize(getQuestionText(a)), props.currentLocale),

        createdAtAsc: (a, b) => safeDate(a.created_at) - safeDate(b.created_at),
        createdAtDesc: (a, b) => safeDate(b.created_at) - safeDate(a.created_at),

        updatedAtAsc: (a, b) => safeDate(a.updated_at) - safeDate(b.updated_at),
        updatedAtDesc: (a, b) => safeDate(b.updated_at) - safeDate(a.updated_at),
    }

    return sortMap[sortParam.value]
        ? list.sort(sortMap[sortParam.value])
        : list
}

const filteredItems = computed(() => {
    let filtered = localItems.value || []
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortItems(filtered)
    }

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

            getQuestionText(item),
            item.question?.explanation,
            item.question?.question_type,

            getSelectedAnswerText(item),
            item.selected_answer?.explanation,
            item.selectedAnswer?.explanation,

            item.attempt?.status,
            item.attempt?.attempt_number,
            item.attempt?.user?.name,
            item.attempt?.user?.email,
            item.attempt?.quiz?.title,
            item.attempt?.quiz?.slug,
            item.attempt?.quiz?.id,
        ]

        return values.some(value => normalize(value).includes(query))
    })

    return sortItems(filtered)
})

const paginatedItems = computed(() => {
    const per = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * per

    return filteredItems.value.slice(start, start + per)
})

const displayedItems = computed(() => {
    return props.useServerProcessing
        ? itemsList.value
        : paginatedItems.value
})

watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

const confirmDelete = (item) => {
    itemToDelete.value = item
    showConfirmDeleteModal.value = true
}

const closeModal = () => {
    showConfirmDeleteModal.value = false
    itemToDelete.value = null
}

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

const selectedItems = ref([])

const toggleAll = (payload) => {
    const checked = payload?.checked ?? payload?.target?.checked ?? false
    const ids = payload?.ids ?? displayedItems.value.map(item => item.id)

    if (checked) {
        selectedItems.value = [...new Set([...selectedItems.value, ...ids])]
    } else {
        selectedItems.value = selectedItems.value.filter(id => !ids.includes(id))
    }
}

const toggleSelectItem = (id) => {
    const index = selectedItems.value.indexOf(id)

    if (index > -1) {
        selectedItems.value.splice(index, 1)
    } else {
        selectedItems.value.push(id)
    }
}

const patchItem = (itemId, payload) => {
    const index = localItems.value.findIndex(item => item.id === itemId)

    if (index !== -1) {
        localItems.value[index] = {
            ...localItems.value[index],
            ...payload,
        }
    }
}

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

const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        toggleAll({ checked: true })
    } else if (action === 'deselectAll') {
        toggleAll({ checked: false })
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
                class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95">
                <div class="sm:flex sm:justify-end sm:items-center mb-3">
                    <ProcessingModeSwitcher
                        setting-key="adminSchoolQuizAttemptItemsProcessingMode"
                        :mode="adminSchoolQuizAttemptItemsProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="itemsCount"
                    />
                </div>

                <SearchInput
                    v-if="itemsCount && !useServerProcessing"
                    v-model="searchQuery"
                    :placeholder="t('search')"
                />

                <ServerSearchInput
                    v-if="itemsCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <div v-if="itemsCount" class="flex justify-between items-center flex-col md:flex-row my-3">
                    <ItemsPerPageSelect
                        v-if="!useServerProcessing"
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <ServerItemsPerPageSelect
                        v-else
                        :items-per-page="itemsPerPage"
                        update-route="admin.settings.updateAdminCountQuizAttemptItems"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="val => sortParam = val"
                    />
                </div>

                <div v-if="itemsCount" class="flex justify-between items-center flex-col md:flex-row my-3">
                    <CountTable>{{ itemsCount }}</CountTable>
                    <BulkActionSelect @change="handleBulkAction" />
                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div v-if="itemsCount" class="flex justify-center items-center flex-col md:flex-row mb-3">
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredItems.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="items"
                    />
                </div>

                <QuizAttemptItemTable
                    v-if="viewMode === 'table'"
                    :items="displayedItems"
                    :selected-items="selectedItems"
                    @toggle-select="toggleSelectItem"
                    @toggle-all="toggleAll"
                    @delete="confirmDelete"
                />

                <QuizAttemptItemCardGrid
                    v-else
                    :items="displayedItems"
                    :selected-items="selectedItems"
                    @toggle-select="toggleSelectItem"
                    @toggle-all="toggleAll"
                    @delete="confirmDelete"
                />

                <div v-if="itemsCount" class="flex justify-center items-center flex-col md:flex-row mt-3">
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredItems.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="items"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            :onCancel="closeModal"
            :onConfirm="deleteItem"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
            @close="closeModal"
        />
    </AdminLayout>
</template>
