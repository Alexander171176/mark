<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 * Список курсов (паттерн)
 */
import { defineProps, ref, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router, Link } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import SearchInput from '@/Components/Admin/Search/SearchInput.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import BulkActionSelect from '@/Components/Admin/Course/Select/BulkActionSelect.vue'
import CountTable from '@/Components/Admin/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/Select/ItemsPerPageSelect.vue'
import CourseTable from '@/Components/Admin/Course/Table/CourseTable.vue'
import CourseCardGrid from '@/Components/Admin/Course/View/CourseCardGrid.vue'
import DangerModal from '@/Components/Admin/Modal/DangerModal.vue'
import Pagination from '@/Components/Admin/Pagination/Pagination.vue'
import SortSelect from '@/Components/Admin/Course/Sort/SortSelect.vue'
import ToggleViewButton from '@/Components/Admin/Buttons/ToggleViewButton.vue'

// --- Инициализация экземпляр i18n, toast ---
const { t } = useI18n()
const toast = useToast()

/**
 * Входные свойства компонента.
 */
const props = defineProps({
    courses: Array,
    coursesCount: Number,
    adminCountLearningCourses: Number,
    adminSortLearningCourses: String,
    currentLocale: String,
    availableLocales: Array,
})

/** Вид: таблица или карточки (общий ключ) */
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')

watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode', val)
})


/** Кол-во элементов на странице */
const itemsPerPage = ref(props.adminCountLearningCourses || 10)

/**
 * Наблюдатель за изменением количества элементов на странице.
 */
watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountLearningCourses'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) =>
            toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

/** Параметр сортировки */
const sortParam = ref(props.adminSortLearningCourses || 'idDesc')

/**
 * Наблюдатель за изменением параметра сортировки.
 */
watch(sortParam, (newVal) => {
    router.put(route('admin.settings.updateAdminSortLearningCourses'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info('Сортировка успешно изменена'),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления сортировки.'),
    })
})

/** Модалка удаления */
const showConfirmDeleteModal = ref(false)
const courseToDeleteId = ref(null)
const courseToDeleteTitle = ref('')

/**
 * Открывает модальное окно подтверждения удаления с входными переменными.
 */
const confirmDelete = (id, title) => {
    courseToDeleteId.value = id
    courseToDeleteTitle.value = title
    showConfirmDeleteModal.value = true
}

/**
 * Закрывает модальное окно подтверждения и сбрасывает связанные переменные.
 */
const closeModal = () => {
    showConfirmDeleteModal.value = false
    courseToDeleteId.value = null
    courseToDeleteTitle.value = ''
}

/**
 * Отправляет запрос на удаление.
 */
const deleteCourse = () => {
    if (courseToDeleteId.value === null) return
    const idToDelete = courseToDeleteId.value
    const titleToDelete = courseToDeleteTitle.value

    router.delete(route('admin.courses.destroy', { course: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            closeModal()
            toast.success(`Курс "${titleToDelete || 'ID: ' + idToDelete}" удалён.`)
        },
        onError: (errors) => {
            closeModal()
            const errorMsg =
                errors.general ||
                errors[Object.keys(errors)[0]] ||
                'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Курс: ${titleToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => {
            courseToDeleteId.value = null
            courseToDeleteTitle.value = ''
        },
    })
}

/** Пагинация и поиск */
const currentPage = ref(1)
const searchQuery = ref('')

/** Сортировка массива курсов */
const sortCourses = (courses) => {
    const value = sortParam.value
    const list = courses.slice()

    if (value === 'idAsc') {
        return list.sort((a, b) => a.id - b.id)
    }

    if (value === 'idDesc') {
        return list.sort((a, b) => b.id - a.id)
    }

    // Дата публикации — от новых к старым, null в конец
    if (value === 'published_at') {
        return list.sort((a, b) => {
            const aTime = a.published_at ? new Date(a.published_at).getTime() : 0
            const bTime = b.published_at ? new Date(b.published_at).getTime() : 0
            return bTime - aTime
        })
    }

    // Фильтры по активности и колонкам
    if (value === 'activity') return courses.filter(c => c.activity)
    if (value === 'inactive') return courses.filter(c => !c.activity)
    if (value === 'left') return courses.filter(c => c.left)
    if (value === 'noLeft') return courses.filter(c => !c.left)
    if (value === 'main') return courses.filter(c => c.main)
    if (value === 'noMain') return courses.filter(c => !c.main)
    if (value === 'right') return courses.filter(c => c.right)
    if (value === 'noRight') return courses.filter(c => !c.right)

    // Флаги витрины — только курсы с признаком
    if (value === 'is_new') return courses.filter(c => c.is_new)
    if (value === 'is_hit') return courses.filter(c => c.is_hit)
    if (value === 'is_sale') return courses.filter(c => c.is_sale)

    // Числовые поля — сортировка по убыванию
    if ([
        'views',
        'likes',
        'students_count',
        'popularity',
        'rating_count',
        'rating_avg',
        'difficulty',
        'duration',
    ].includes(value)) {
        return list.sort((a, b) => {
            const av = a[value] ?? 0
            const bv = b[value] ?? 0
            return bv - av
        })
    }

    // level, status, availability, title, sort и прочее — обычная сортировка по возрастанию
    return list.sort((a, b) => {
        const av = a[value] ?? ''
        const bv = b[value] ?? ''
        if (av < bv) return -1
        if (av > bv) return 1
        return 0
    })
}

/** Фильтрация + сортировка (поиск по title) */
const filteredCourses = computed(() => {
    let filtered = props.courses || []

    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase()
        filtered = filtered.filter(c =>
            (c.title || '').toLowerCase().includes(q) ||
            (c.slug || '').toLowerCase().includes(q)
        )
    }

    return sortCourses(filtered)
})

/** Пагинация */
const paginatedCourses = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value
    return filteredCourses.value.slice(start, start + itemsPerPage.value)
})

/**
 * Вычисляемое свойство, возвращающее общее количество страниц пагинации.
 */
const totalPages = computed(() =>
    Math.ceil((filteredCourses.value.length || 0) / itemsPerPage.value)
)

/** Обновление сортировки (drag&drop) */
const handleSortOrderUpdate = (orderedIds) => {
    const startSort = (currentPage.value - 1) * itemsPerPage.value

    const sortData = orderedIds.map((id, index) => ({
        id,
        sort: startSort + index + 1,
    }))

    router.put(
        route('admin.actions.courses.updateSortBulk'),
        { courses: sortData },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.success('Порядок курсов успешно обновлён.'),
            onError: (errors) => {
                console.error('Ошибка обновления сортировки курсов:', errors)
                toast.error(
                    errors?.general ||
                    errors?.courses ||
                    'Не удалось обновить порядок курсов.'
                )
                router.reload({ only: ['courses'], preserveScroll: true })
            },
        }
    )
}

/** Массовые действия */
const selectedCourses = ref([])

/**
 * Логика выбора всех для массовых действий.
 */
const toggleAll = ({ ids, checked }) => {
    if (checked) {
        selectedCourses.value = [...ids]
    } else {
        selectedCourses.value = []
    }
}

/**
 * Обрабатывает событие выбора/снятия выбора одной строки.
 */
const toggleSelectCourse = (id) => {
    const idx = selectedCourses.value.indexOf(id)
    if (idx > -1) selectedCourses.value.splice(idx, 1)
    else selectedCourses.value.push(id)
}

/**
 * Выполняет массовое включение/выключение активности выбранных.
 */
const bulkToggleActivity = (newActivity) => {
    if (!selectedCourses.value.length) {
        toast.warning('Выберите курсы для активации/деактивации')
        return
    }

    router.put(
        route('admin.actions.courses.bulkUpdateActivity'),
        { ids: selectedCourses.value, activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                toast.success('Активность курсов массово обновлена')
                const updatedIds = [...selectedCourses.value]
                selectedCourses.value = []

                paginatedCourses.value.forEach(c => {
                    if (updatedIds.includes(c.id)) c.activity = newActivity
                })
            },
            onError: (errors) => {
                const msg =
                    errors?.ids || errors?.activity || errors?.general ||
                    'Не удалось массово обновить активность курсов'
                toast.error(msg)
            },
        }
    )
}

/**
 * Обрабатывает выбор действия в селекте массовых действий.
 */
const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        selectedCourses.value = paginatedCourses.value.map(c => c.id)
    } else if (action === 'deselectAll') {
        selectedCourses.value = []
    } else if (action === 'activate') {
        bulkToggleActivity(true)
    } else if (action === 'deactivate') {
        bulkToggleActivity(false)
    }

    event.target.value = ''
}

/** Переключение активности одного курса */
const toggleActivity = (course) => {
    const newActivity = !course.activity
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(
        route('admin.actions.courses.updateActivity', { course: course.id }),
        { activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                course.activity = newActivity
                toast.success(`Курс "${course.title}" ${actionText}.`)
            },
            onError: (errors) => {
                toast.error(
                    errors?.activity ||
                    errors?.general ||
                    `Ошибка изменения активности для курса "${course.title}".`
                )
            },
        }
    )
}

/** Ссылка для табов локалей */
const localeLink = (locale) => route('admin.courses.index', { locale })
</script>

<template>
    <AdminLayout :title="t('courses')">
        <template #header>
            <TitlePage>{{ t('courses') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <!-- Добавить курс -->
                    <DefaultButton :href="route('admin.courses.create')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"
                                />
                            </svg>
                        </template>
                        {{ t('addCourse') }}
                    </DefaultButton>

                    <BulkActionSelect
                        v-if="coursesCount"
                        @change="handleBulkAction"
                    />
                </div>

                <!-- Локали + счётчик -->
                <div class="flex items-center justify-between mt-5">
                    <div
                        class="flex items-center justify-end space-x-2 px-3 py-1
                               border-x border-t border-gray-400 rounded-t-lg
                               bg-gray-100 dark:bg-gray-900"
                    >
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
                                        : 'bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-200 hover:bg-slate-300 dark:hover:bg-slate-600'
                                ]"
                                preserve-scroll
                                preserve-state
                            >
                                {{ locale.toUpperCase() }}
                            </Link>
                        </template>
                    </div>

                    <div class="flex items-center space-x-3">
                        <CountTable v-if="coursesCount">
                            {{ coursesCount }}
                        </CountTable>

                        <ToggleViewButton v-model:viewMode="viewMode" />
                    </div>

                </div>

                <SearchInput
                    v-if="coursesCount"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <!-- Таблица -->
                <CourseTable
                    v-if="viewMode === 'table'"
                    :courses="paginatedCourses"
                    :selected-courses="selectedCourses"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectCourse"
                    @toggle-all="toggleAll"
                />

                <!-- Карточки -->
                <CourseCardGrid
                    v-else
                    :courses="paginatedCourses"
                    :selected-courses="selectedCourses"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectCourse"
                    @toggle-all="toggleAll"
                />

                <div
                    class="flex justify-between items-center flex-col md:flex-row my-1"
                    v-if="coursesCount"
                >
                    <ItemsPerPageSelect
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredCourses.length"
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
            :onConfirm="deleteCourse"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
