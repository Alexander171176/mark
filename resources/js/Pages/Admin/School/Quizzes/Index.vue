<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список квизов школы
 */
import { computed, defineProps, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import SearchInput from '@/Components/Admin/UI/Search/SearchInput.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import CountTable from '@/Components/Admin/UI/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/UI/Select/ItemsPerPageSelect.vue'
import DangerModal from '@/Components/Admin/UI/Modal/DangerModal.vue'
import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue'
import ToggleViewButton from '@/Components/Admin/UI/Buttons/ToggleViewButton.vue'

import BulkActionSelect from '@/Components/Admin/School/Quiz/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/School/Quiz/Sort/SortSelect.vue'
import QuizTable from '@/Components/Admin/School/Quiz/Table/QuizTable.vue'
import QuizCardGrid from '@/Components/Admin/School/Quiz/View/QuizCardGrid.vue'

// Локализация интерфейса
const { t } = useI18n()

// Уведомления
const toast = useToast()

// Входящие данные страницы
const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },
    quizzes: { type: Array, default: () => [] },
    quizzesCount: { type: Number, default: 0 },
    adminSchoolQuizzesPerPage: { type: Number, default: 10 },
    adminSchoolQuizzesDefaultSort: { type: String, default: 'idDesc' },
})

// Режим отображения (таблица/карточки)
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')

// Сохранение режима отображения
watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode', val)
})

// Локальный список квизов
const localQuizzes = ref([])

// Синхронизация локального списка
watch(
    () => props.quizzes,
    (newVal) => {
        localQuizzes.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

// Количество элементов на странице
const itemsPerPage = ref(props.adminSchoolQuizzesPerPage || 10)

// Обновление количества элементов
watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountQuizzes'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

// Параметр сортировки
const sortParam = ref(props.adminSchoolQuizzesDefaultSort || 'idDesc')

// Обновление сортировки
watch(sortParam, (newVal) => {
    router.put(route('admin.settings.updateAdminSortQuizzes'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info('Сортировка успешно изменена'),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления сортировки.'),
    })
})

// Модальное окно удаления
const showConfirmDeleteModal = ref(false)

// ID удаляемого квиза
const quizToDeleteId = ref(null)

// Название удаляемого квиза
const quizToDeleteTitle = ref('')

// Подтверждение удаления квиза
const confirmDelete = (quizOrId, title = null) => {
    if (typeof quizOrId === 'object') {
        quizToDeleteId.value = quizOrId.id
        quizToDeleteTitle.value = title || quizOrId.title || `ID: ${quizOrId.id}`
    } else {
        quizToDeleteId.value = quizOrId
        quizToDeleteTitle.value = title || `ID: ${quizOrId}`
    }

    showConfirmDeleteModal.value = true
}

// Закрытие модального окна
const closeModal = () => {
    showConfirmDeleteModal.value = false
    quizToDeleteId.value = null
    quizToDeleteTitle.value = ''
}

// Удаление квиза
const deleteQuiz = () => {
    if (quizToDeleteId.value === null) return

    const idToDelete = quizToDeleteId.value
    const titleToDelete = quizToDeleteTitle.value

    router.delete(route('admin.schoolQuizzes.destroy', {
        schoolQuiz: idToDelete,
    }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            toast.success(`Квиз "${titleToDelete || 'ID: ' + idToDelete}" удалён.`)
        },
        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            const errorMsg = errors.general || errors[errorKey] || 'Произошла ошибка при удалении.'

            toast.error(`${errorMsg} (Квиз: ${titleToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => closeModal(),
    })
}

// Текущая страница пагинации
const currentPage = ref(1)

// Поисковый запрос
const searchQuery = ref('')

// Нормализация строк поиска
const normalize = (value) => (value ?? '').toString().trim().toLowerCase()

// Сортировка и фильтрация квизов
const sortQuizzes = (items) => {
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

    if (sortParam.value === 'titleAsc') {
        return list.sort((a, b) => normalize(a.title).localeCompare(normalize(b.title)))
    }

    if (sortParam.value === 'titleDesc') {
        return list.sort((a, b) => normalize(b.title).localeCompare(normalize(a.title)))
    }

    if (sortParam.value === 'passScoreAsc') {
        return list.sort((a, b) => (a.pass_score ?? 0) - (b.pass_score ?? 0))
    }

    if (sortParam.value === 'passScoreDesc') {
        return list.sort((a, b) => (b.pass_score ?? 0) - (a.pass_score ?? 0))
    }

    if (sortParam.value === 'attemptsLimitAsc') {
        return list.sort((a, b) => (a.attempts_limit ?? 0) - (b.attempts_limit ?? 0))
    }

    if (sortParam.value === 'attemptsLimitDesc') {
        return list.sort((a, b) => (b.attempts_limit ?? 0) - (a.attempts_limit ?? 0))
    }

    if (sortParam.value === 'timeLimitAsc') {
        return list.sort((a, b) => (a.time_limit_minutes ?? 0) - (b.time_limit_minutes ?? 0))
    }

    if (sortParam.value === 'timeLimitDesc') {
        return list.sort((a, b) => (b.time_limit_minutes ?? 0) - (a.time_limit_minutes ?? 0))
    }

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

    if ([
        'questions_count',
        'attempts_count',
        'images_count',
    ].includes(sortParam.value)) {
        return list.sort((a, b) => (b[sortParam.value] ?? 0) - (a[sortParam.value] ?? 0))
    }

    return list
}

// Отфильтрованные квизы
const filteredQuizzes = computed(() => {
    let filtered = localQuizzes.value || []
    const q = normalize(searchQuery.value)

    if (!q) {
        return sortQuizzes(filtered)
    }

    filtered = filtered.filter((quiz) => {
        const values = [
            quiz.title,
            quiz.short,
            quiz.description,
            quiz.slug,
            quiz.type,

            quiz.course?.title,
            quiz.course?.slug,

            quiz.module?.title,
            quiz.module?.slug,

            quiz.lesson?.title,
            quiz.lesson?.slug,
        ]

        return values.some(value => normalize(value).includes(q))
    })

    return sortQuizzes(filtered)
})

// Квизы текущей страницы
const paginatedQuizzes = computed(() => {
    const per = Number(itemsPerPage.value || 20)
    const start = (currentPage.value - 1) * per

    return filteredQuizzes.value.slice(start, start + per)
})

// Сброс страницы при изменении поиска или лимита
watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

// Локальное обновление данных квиза
const patchQuiz = (quizId, payload) => {
    const index = localQuizzes.value.findIndex(quiz => quiz.id === quizId)

    if (index !== -1) {
        localQuizzes.value[index] = {
            ...localQuizzes.value[index],
            ...payload,
        }
    }
}

// Выбранные квизы
const selectedQuizzes = ref([])

// Выбор или снятие выбора всех квизов
const toggleAll = ({ ids, checked }) => {
    selectedQuizzes.value = checked ? [...ids] : []
}

// Переключение выбора квиза
const toggleSelectQuiz = (id) => {
    const index = selectedQuizzes.value.indexOf(id)

    if (index > -1) {
        selectedQuizzes.value.splice(index, 1)
    } else {
        selectedQuizzes.value.push(id)
    }
}

// Обновление сортировки drag-and-drop
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
            console.error('Ошибка обновления сортировки квизов:', errors)
            toast.error(errors?.message || errors?.general || 'Не удалось обновить порядок квизов.')

            router.reload({
                only: ['quizzes'],
                preserveScroll: true,
            })
        },
    })
}

// Массовое обновление активности
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

// Массовое обновление флагов left/main/right
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

// Массовое удаление квизов
const bulkDelete = () => {
    if (!selectedQuizzes.value.length) {
        toast.warning('Выберите хотя бы один квиз для удаления.')
        return
    }

    if (!confirm('Вы уверены, что хотите удалить выбранные квизы?')) {
        return
    }

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
            const errorMessage = errors[errorKey] || 'Произошла ошибка при удалении квизов.'

            toast.error(errorMessage)
        },
    })
}

// Обработка массовых действий
const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        selectedQuizzes.value = paginatedQuizzes.value.map(quiz => quiz.id)
    } else if (action === 'deselectAll') {
        selectedQuizzes.value = []
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

// Переключение активности квиза
const toggleActivity = (quiz) => {
    const newActivity = !quiz.activity
    const quizTitle = quiz.title || `ID: ${quiz.id}`
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

// Универсальное переключение флагов
const togglePlacement = (quiz, field, routeName) => {
    const newValue = !quiz[field]
    const quizTitle = quiz.title || `ID: ${quiz.id}`

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

// Переключение левой колонки
const toggleLeft = (quiz) =>
    togglePlacement(quiz, 'left', 'admin.actions.schoolQuizzes.updateLeft')

// Переключение центральной колонки
const toggleMain = (quiz) =>
    togglePlacement(quiz, 'main', 'admin.actions.schoolQuizzes.updateMain')

// Переключение правой колонки
const toggleRight = (quiz) =>
    togglePlacement(quiz, 'right', 'admin.actions.schoolQuizzes.updateRight')

// Клонирование квиза
const cloneQuiz = (quiz) => {
    const quizId = quiz?.id
    const quizTitle = quiz?.title || `ID: ${quizId}`

    if (!quizId) {
        toast.error('Не удалось определить квиз для клонирования.')
        return
    }

    if (!confirm(`Вы уверены, что хотите клонировать квиз "${quizTitle}"?`)) {
        return
    }

    router.post(route('admin.actions.schoolQuizzes.clone', {
        schoolQuiz: quizId,
    }), {}, {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => toast.success(`Квиз "${quizTitle}" успешно клонирован.`),
        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            const errorMessage = errors[errorKey] || `Ошибка клонирования квиза "${quizTitle}".`

            toast.error(errorMessage)
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
                <div class="sm:flex sm:justify-between sm:items-center mb-3">
                    <DefaultButton :href="route('admin.schoolQuizzes.create')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"
                                />
                            </svg>
                        </template>
                        {{ t('addQuiz') }}
                    </DefaultButton>
                </div>
                <SearchInput
                    v-if="quizzesCount"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />
                <div v-if="quizzesCount"
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
                <div v-if="quizzesCount"
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
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredQuizzes.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                </div>
                <QuizTable
                    v-if="viewMode === 'table'"
                    :quizzes="paginatedQuizzes"
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
                    :quizzes="paginatedQuizzes"
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
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredQuizzes.length"
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
            :onConfirm="deleteQuiz"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
