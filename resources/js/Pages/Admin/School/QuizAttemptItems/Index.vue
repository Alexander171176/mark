<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр
 * Список ответов в попытках (QuizAttemptItem)
 */
import { defineProps, ref, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import SearchInput from '@/Components/Admin/Search/SearchInput.vue'
import CountTable from '@/Components/Admin/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/Select/ItemsPerPageSelect.vue'
import DangerModal from '@/Components/Admin/Modal/DangerModal.vue'
import Pagination from '@/Components/Admin/Pagination/Pagination.vue'
import ToggleViewButton from '@/Components/Admin/Buttons/ToggleViewButton.vue'

import BulkActionSelect from '@/Components/Admin/QuizAttemptItem/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/QuizAttemptItem/Sort/SortSelect.vue'
import QuizAttemptItemTable from '@/Components/Admin/QuizAttemptItem/Table/QuizAttemptItemTable.vue'
import QuizAttemptItemCardGrid from '@/Components/Admin/QuizAttemptItem/View/QuizAttemptItemCardGrid.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    items: Array,
    itemsCount: Number,
    adminCountItems: Number,
    adminSortItems: String,
})

/** Вид: таблица или карточки */
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')
watch(viewMode, (val) => localStorage.setItem('admin_view_mode', val))

/** Кол-во элементов на странице */
const itemsPerPage = ref(props.adminCountItems || 10)

watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountQuizAttemptItems'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors?.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

/** Параметр сортировки */
const sortParam = ref(props.adminSortItems || 'idDesc')

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

/** Модалка удаления */
const showConfirmDeleteModal = ref(false)
const itemToDeleteId = ref(null)
const itemToDeleteTitle = ref('')

const confirmDelete = (id, title) => {
    itemToDeleteId.value = id
    itemToDeleteTitle.value = title
    showConfirmDeleteModal.value = true
}

const closeModal = () => {
    showConfirmDeleteModal.value = false
    itemToDeleteId.value = null
    itemToDeleteTitle.value = ''
}

const deleteItem = () => {
    if (itemToDeleteId.value === null) return

    const idToDelete = itemToDeleteId.value
    const titleToDelete = itemToDeleteTitle.value

    router.delete(route('admin.quizAttemptItems.destroy', { quizAttemptItem: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            closeModal()
            toast.success(`Ответ "${titleToDelete || 'ID: ' + idToDelete}" удалён.`)
        },
        onError: (errors) => {
            closeModal()
            const errorMsg =
                errors?.general ||
                errors?.[Object.keys(errors || {})[0]] ||
                'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Ответ: ${titleToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => {
            itemToDeleteId.value = null
            itemToDeleteTitle.value = ''
        },
    })
}

/** Пагинация и поиск */
const currentPage = ref(1)
const searchQuery = ref('')

/** Сортировка */
const toTs = (v) => {
    if (!v) return null
    const ts = new Date(v).getTime()
    return Number.isFinite(ts) ? ts : null
}

const normalizeNum = (v) => {
    const n = Number(v)
    return Number.isFinite(n) ? n : 0
}

const sortItems = (items) => {
    const value = sortParam.value
    const list = items.slice()

    if (value === 'idAsc') return list.sort((a, b) => a.id - b.id)
    if (value === 'idDesc') return list.sort((a, b) => b.id - a.id)

    if (value === 'attemptIdAsc') return list.sort((a, b) => (a.quiz_attempt_id ?? 0) - (b.quiz_attempt_id ?? 0))
    if (value === 'attemptIdDesc') return list.sort((a, b) => (b.quiz_attempt_id ?? 0) - (a.quiz_attempt_id ?? 0))

    if (value === 'questionIdAsc') return list.sort((a, b) => (a.quiz_question_id ?? 0) - (b.quiz_question_id ?? 0))
    if (value === 'questionIdDesc') return list.sort((a, b) => (b.quiz_question_id ?? 0) - (a.quiz_question_id ?? 0))

    if (value === 'scoreAsc') return list.sort((a, b) => normalizeNum(a.score) - normalizeNum(b.score))
    if (value === 'scoreDesc') return list.sort((a, b) => normalizeNum(b.score) - normalizeNum(a.score))

    if (value === 'createdAtAsc' || value === 'createdAtDesc') {
        return list.sort((a, b) => {
            const ad = toTs(a.created_at)
            const bd = toTs(b.created_at)
            if (ad == null && bd == null) return 0
            if (ad == null) return 1
            if (bd == null) return -1
            return value === 'createdAtAsc' ? (ad - bd) : (bd - ad)
        })
    }

    if (value === 'correctFirst' || value === 'wrongFirst') {
        return list.sort((a, b) => {
            const av = a.is_correct ? 1 : 0
            const bv = b.is_correct ? 1 : 0
            return value === 'correctFirst' ? (bv - av) : (av - bv)
        })
    }

    return list
}

/** Фильтрация + сортировка */
const filteredItems = computed(() => {
    let filtered = props.items || []

    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase()

        filtered = filtered.filter(i =>
            (i.id ?? '').toString().toLowerCase().includes(q) ||
            (i.quiz_attempt_id ?? '').toString().toLowerCase().includes(q) ||
            (i.quiz_question_id ?? '').toString().toLowerCase().includes(q) ||
            (i.selected_answer_id ?? '').toString().toLowerCase().includes(q) ||
            (i.selected_answer_ids ? JSON.stringify(i.selected_answer_ids) : '').toLowerCase().includes(q) ||
            (i.free_text_answer ?? '').toString().toLowerCase().includes(q) ||
            (i.reviewer_comment ?? '').toString().toLowerCase().includes(q) ||
            (i.score ?? '').toString().toLowerCase().includes(q) ||
            (i.max_score ?? '').toString().toLowerCase().includes(q) ||
            (i.question?.text ?? '').toString().toLowerCase().includes(q) ||
            (i.selected_answer?.text ?? '').toString().toLowerCase().includes(q) ||
            (i.attempt?.status ?? '').toString().toLowerCase().includes(q) ||

            (i.attempt?.user?.name ?? '').toString().toLowerCase().includes(q) ||
            (i.attempt?.user?.email ?? '').toString().toLowerCase().includes(q) ||
            (i.attempt?.quiz?.title ?? '').toString().toLowerCase().includes(q) ||
            (i.attempt?.quiz?.slug ?? '').toString().toLowerCase().includes(q) ||
            (i.attempt?.quiz?.id ?? '').toString().toLowerCase().includes(q)
        )
    }

    return sortItems(filtered)
})

/** Пагинация */
const paginatedItems = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value
    return filteredItems.value.slice(start, start + itemsPerPage.value)
})

/** Общее количество страниц */
const totalPages = computed(() =>
    Math.ceil((filteredItems.value.length || 0) / itemsPerPage.value)
)

watch(filteredItems, () => {
    if (currentPage.value > totalPages.value) currentPage.value = totalPages.value || 1
})

/** Массовые действия */
const selectedItems = ref([])

const toggleAll = ({ ids, checked }) => {
    if (checked) selectedItems.value = [...ids]
    else selectedItems.value = []
}

const toggleSelectItem = (id) => {
    const idx = selectedItems.value.indexOf(id)
    if (idx > -1) selectedItems.value.splice(idx, 1)
    else selectedItems.value.push(id)
}

/** Массовое проставление correct */
const bulkUpdateCorrect = (isCorrect) => {
    if (!selectedItems.value.length) {
        toast.warning('Выберите ответы')
        return
    }

    router.put(
        route('admin.actions.quizAttemptItems.bulkUpdateCorrect'),
        { ids: selectedItems.value, is_correct: isCorrect },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                toast.success('Правильность ответов массово обновлена')
                const updatedIds = [...selectedItems.value]
                selectedItems.value = []

                paginatedItems.value.forEach(i => {
                    if (updatedIds.includes(i.id)) i.is_correct = !!isCorrect
                })
            },
            onError: (errors) => {
                const msg =
                    errors?.ids || errors?.is_correct || errors?.general ||
                    'Не удалось массово обновить правильность'
                toast.error(msg)
            },
        }
    )
}

/** Массовое удаление */
const bulkDestroy = () => {
    if (!selectedItems.value.length) {
        toast.warning('Выберите ответы')
        return
    }

    router.delete(
        route('admin.actions.quizAttemptItems.bulkDestroy'),
        {
            data: { ids: selectedItems.value },
            preserveScroll: true,
            preserveState: false,
            onSuccess: () => {
                toast.success('Ответы успешно удалены')
                selectedItems.value = []
            },
            onError: (errors) => {
                const msg = errors?.ids || errors?.general || 'Ошибка удаления'
                toast.error(msg)
            },
        }
    )
}

/** Обрабатывает выбор действия в селекте массовых действий */
const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        selectedItems.value = paginatedItems.value.map(i => i.id)
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
                class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="flex justify-end items-center mb-2">
                    <!-- В большинстве случаев attempt items вручную не создают -->


                    <BulkActionSelect
                        v-if="itemsCount"
                        @change="handleBulkAction"
                    />
                </div>

                <SearchInput
                    v-if="itemsCount"
                    v-model="searchQuery"
                    :placeholder="t('search')"
                />

                <div v-if="itemsCount" class="flex items-center justify-end my-2">
                    <CountTable>{{ itemsCount }}</CountTable>
                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <!-- Один общий чекбокс выбрать всё -->
                <div v-if="itemsCount"
                     class="flex items-center justify-between px-3 py-2
                            border-b border-slate-400 dark:border-slate-500">

                    <div class="text-xs text-slate-600 dark:text-slate-200">
                        {{ t('selected') }}: {{ selectedItems.length }}
                    </div>

                    <label
                        class="flex items-center text-xs text-slate-600
                               dark:text-slate-200 cursor-pointer">
                        <span>{{ t('selectAll') }}</span>
                        <input
                            type="checkbox"
                            class="rounded-sm border-slate-400 mx-2"
                            :checked="paginatedItems.length &&
                            paginatedItems.every(i => selectedItems.includes(i.id))"
                            @change="toggleAll({
                                ids: paginatedItems.map(i => i.id),
                                checked: $event.target.checked
                            })"
                        />
                    </label>
                </div>

                <!-- Табличный вид -->
                <QuizAttemptItemTable
                    v-if="viewMode === 'table'"
                    :items="paginatedItems"
                    :selected-items="selectedItems"
                    @toggle-select="toggleSelectItem"
                    @toggle-all="toggleAll"
                    @delete="confirmDelete"
                />

                <!-- Карточный вид -->
                <QuizAttemptItemCardGrid
                    v-else
                    :items="paginatedItems"
                    :selected-items="selectedItems"
                    @toggle-select="toggleSelectItem"
                    @toggle-all="toggleAll"
                    @delete="confirmDelete"
                />

                <div
                    class="flex justify-between items-center flex-col md:flex-row my-1"
                    v-if="itemsCount"
                >
                    <ItemsPerPageSelect
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredItems.length"
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
            :onConfirm="deleteItem"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
