<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 * Список потоков (Lesson)
 */
import { defineProps, ref, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router, Link } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import SearchInput from '@/Components/Admin/Search/SearchInput.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import BulkActionSelect from '@/Components/Admin/CourseSchedule/Select/BulkActionSelect.vue'
import CountTable from '@/Components/Admin/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/Select/ItemsPerPageSelect.vue'
import CourseScheduleTable from '@/Components/Admin/CourseSchedule/Table/CourseScheduleTable.vue'
import CourseScheduleCardGrid from '@/Components/Admin/CourseSchedule/View/CourseScheduleCardGrid.vue'
import DangerModal from '@/Components/Admin/Modal/DangerModal.vue'
import Pagination from '@/Components/Admin/Pagination/Pagination.vue'
import SortSelect from '@/Components/Admin/CourseSchedule/Sort/SortSelect.vue'
import ToggleViewButton from '@/Components/Admin/Buttons/ToggleViewButton.vue'

// --- Инициализация экземпляр i18n, toast ---
const { t } = useI18n()
const toast = useToast()

/**
 * Входные свойства компонента.
 */
const props = defineProps({
    schedules: Array,
    schedulesCount: Number,
    adminCountCourseSchedules: Number,
    adminSortCourseSchedules: String,
    currentLocale: String,
    availableLocales: Array,
})

/** Вид: таблица или карточки (общий ключ, как у курсов/модулей/потоков) */
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')

watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode', val)
})

/** Кол-во элементов на странице */
const itemsPerPage = ref(props.adminCountCourseSchedules || 10)

/**
 * Наблюдатель за изменением количества элементов на странице.
 */
watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountCourseSchedules'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) =>
            toast.error(errors.value || 'Ошибка обновления сортировки.'),
    })
})

/** Параметр сортировки */
const sortParam = ref(props.adminSortCourseSchedules || 'idDesc')

/**
 * Наблюдатель за изменением параметра сортировки.
 */
watch(sortParam, (newVal) => {
    router.put(route('admin.settings.updateAdminSortCourseSchedules'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info('Сортировка успешно изменена'),
        onError: (errors) => {
            const firstError = errors ? Object.values(errors)[0] : null
            toast.error(firstError || 'Ошибка обновления кол-ва элементов.')
        },
    })
})

/** Модалка удаления */
const showConfirmDeleteModal = ref(false)
const scheduleToDeleteId = ref(null)
const scheduleToDeleteTitle = ref('')

/**
 * Открывает модальное окно подтверждения удаления с входными переменными.
 */
const confirmDelete = (id, title) => {
    scheduleToDeleteId.value = id
    scheduleToDeleteTitle.value = title
    showConfirmDeleteModal.value = true
}

/**
 * Закрывает модальное окно подтверждения и сбрасывает связанные переменные.
 */
const closeModal = () => {
    showConfirmDeleteModal.value = false
    scheduleToDeleteId.value = null
    scheduleToDeleteTitle.value = ''
}

/**
 * Отправляет запрос на удаление.
 */
const deleteSchedule = () => {
    if (scheduleToDeleteId.value === null) return
    const idToDelete = scheduleToDeleteId.value
    const titleToDelete = scheduleToDeleteTitle.value

    router.delete(route('admin.courseSchedules.destroy', { courseSchedule: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            closeModal()
            toast.success(`Поток "${titleToDelete || 'ID: ' + idToDelete}" удалён.`)
        },
        onError: (errors) => {
            closeModal()
            const errorMsg =
                errors.general ||
                errors[Object.keys(errors)[0]] ||
                'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Поток: ${titleToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => {
            scheduleToDeleteId.value = null
            scheduleToDeleteTitle.value = ''
        },
    })
}

/**
 * Отправляет запрос для клонирования.
 */
const cloneSchedule = (scheduleObject) => { // Переименовываем параметр для ясности
    // Извлекаем ID из объекта
    const scheduleId = scheduleObject?.id; // Используем опциональную цепочку на случай undefined/null
    const scheduleTitle = scheduleObject?.title || `ID: ${scheduleId}`; // Пытаемся получить title или используем ID

    // Проверяем, что ID получен
    if (typeof scheduleId === 'undefined' || scheduleId === null) {
        console.error("Не удалось получить ID потока для клонирования", scheduleObject);
        toast.error("Не удалось определить поток для клонирования.");
        return;
    }

    // Используем confirm с извлеченным ID (или title)
    if (!confirm(`Вы уверены, что хотите клонировать поток "${scheduleTitle}"?`)) {
        return;
    }

    // В route() передаем именно scheduleId
    router.post(
        route('admin.actions.courseSchedules.clone', { courseSchedule: scheduleId }),
        {}, {
            preserveScroll: true,
            preserveState: false,
            onSuccess: () => {
                toast.success(`Поток "${scheduleTitle}" успешно клонирован.`);
            },
            onError: (errors) => {
                const errorKey = Object.keys(errors)[0];
                const errorMessage = errors[errorKey] || `Ошибка клонирования потока "${scheduleTitle}".`;
                toast.error(errorMessage);
            }
    });
};

/** Пагинация и поиск */
const currentPage = ref(1)
const searchQuery = ref('')

/** Сортировка массива потоков */
const sortSchedules = (schedules) => {
    const value = sortParam.value
    const list = schedules.slice()

    // --- ID ---
    if (value === 'idAsc') {
        return list.sort((a, b) => a.id - b.id)
    }

    if (value === 'idDesc') {
        return list.sort((a, b) => b.id - a.id)
    }

    // --- Фильтры по активности ---
    if (value === 'activity') return schedules.filter(s => s.activity)
    if (value === 'inactive') return schedules.filter(s => !s.activity)

    // --- Фильтры по онлайн/офлайн ---
    if (value === 'online') return schedules.filter(s => s.is_online)
    if (value === 'offline') return schedules.filter(s => !s.is_online)

    // --- Сортировка по отношениям (курс, инструктор) ---

    // Курс — по названию курса (course.title) по возрастанию
    if (value === 'courseTitle') {
        return list.sort((a, b) => {
            const av = (a.course?.title || '').toLowerCase()
            const bv = (b.course?.title || '').toLowerCase()
            if (av < bv) return -1
            if (av > bv) return 1
            return 0
        })
    }

    // Инструктор — сначала user.name / user.email, потом instructor.title
    if (value === 'instructorUser') {
        const getInstructorKey = (s) => {
            const userName  = s.instructor?.user?.name || ''
            const userEmail = s.instructor?.user?.email || ''
            const title     = s.instructor?.title || ''

            // приоритет: name → email → title
            return (userName || userEmail || title).toLowerCase()
        }

        return list.sort((a, b) => {
            const av = getInstructorKey(a)
            const bv = getInstructorKey(b)
            if (av < bv) return -1
            if (av > bv) return 1
            return 0
        })
    }

    // --- Даты (новые сверху, null в конец) ---
    if ([
        'starts_at',
        'ends_at',
        'enroll_starts_at',
        'enroll_ends_at'
    ].includes(value)) {
        return list.sort((a, b) => {
            const aTime = a[value] ? new Date(a[value]).getTime() : 0
            const bTime = b[value] ? new Date(b[value]).getTime() : 0
            // от новых к старым
            return bTime - aTime
        })
    }

    // --- Числовые поля — по убыванию (больше → выше) ---
    if (['views', 'capacity'].includes(value)) {
        return list.sort((a, b) => {
            const av = a[value] ?? 0
            const bv = b[value] ?? 0
            return bv - av
        })
    }

    // --- Остальные строковые/числовые поля — по возрастанию ---
    // (status, title, sort и т.д.)
    return list.sort((a, b) => {
        const av = a[value] ?? ''
        const bv = b[value] ?? ''
        if (av < bv) return -1
        if (av > bv) return 1
        return 0
    })
}

/** Фильтрация + сортировка (поиск по title) */
const filteredSchedules = computed(() => {
    let filtered = props.schedules || []

    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase()
        filtered = filtered.filter(s =>
            (s.title || '').toLowerCase().includes(q) ||
            (s.slug || '').toLowerCase().includes(q)
        )
    }

    return sortSchedules(filtered)
})

/** Пагинация */
const paginatedSchedules = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value
    return filteredSchedules.value.slice(start, start + itemsPerPage.value)
})

/**
 * Вычисляемое свойство, возвращающее общее количество страниц пагинации.
 */
const totalPages = computed(() =>
    Math.ceil((filteredSchedules.value.length || 0) / itemsPerPage.value)
)

/** Обновление сортировки (drag&drop) */
const handleSortOrderUpdate = (orderedIds) => {
    const startSort = (currentPage.value - 1) * itemsPerPage.value

    const sortData = orderedIds.map((id, index) => ({
        id,
        sort: startSort + index + 1,
    }))

    router.put(
        route('admin.actions.courseSchedules.updateSortBulk'),
        { schedules: sortData },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.success('Порядок потоков успешно обновлён.'),
            onError: (errors) => {
                console.error('Ошибка обновления сортировки потоков:', errors)
                toast.error(
                    errors?.general ||
                    errors?.schedules ||
                    'Не удалось обновить порядок потоков.'
                )
                router.reload({ only: ['schedules'], preserveScroll: true })
            },
        }
    )
}

/** Массовые действия */
const selectedSchedules = ref([])

/**
 * Логика выбора всех для массовых действий.
 */
const toggleAll = ({ ids, checked }) => {
    if (checked) {
        selectedSchedules.value = [...ids]
    } else {
        selectedSchedules.value = []
    }
}

/**
 * Обрабатывает событие выбора/снятия выбора одной строки.
 */
const toggleSelectSchedule = (id) => {
    const idx = selectedSchedules.value.indexOf(id)
    if (idx > -1) selectedSchedules.value.splice(idx, 1)
    else selectedSchedules.value.push(id)
}

/**
 * Выполняет массовое включение/выключение активности выбранных.
 */
const bulkToggleActivity = (newActivity) => {
    if (!selectedSchedules.value.length) {
        toast.warning('Выберите потоки для активации/деактивации')
        return
    }

    router.put(
        route('admin.actions.courseSchedules.bulkUpdateActivity'),
        { ids: selectedSchedules.value, activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                toast.success('Активность потоков массово обновлена')
                const updatedIds = [...selectedSchedules.value]
                selectedSchedules.value = []

                paginatedSchedules.value.forEach(l => {
                    if (updatedIds.includes(l.id)) l.activity = newActivity
                })
            },
            onError: (errors) => {
                const msg =
                    errors?.ids || errors?.activity || errors?.general ||
                    'Не удалось массово обновить активность потоков'
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
        selectedSchedules.value = paginatedSchedules.value.map(l => l.id)
    } else if (action === 'deselectAll') {
        selectedSchedules.value = []
    } else if (action === 'activate') {
        bulkToggleActivity(true)
    } else if (action === 'deactivate') {
        bulkToggleActivity(false)
    }

    event.target.value = ''
}

/** Переключение активности одного потока */
const toggleActivity = (schedule) => {
    const newActivity = !schedule.activity
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(
        route('admin.actions.courseSchedules.updateActivity', { courseSchedule: schedule.id }),
        { activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                schedule.activity = newActivity
                toast.success(`Поток "${schedule.title}" ${actionText}.`)
            },
            onError: (errors) => {
                toast.error(
                    errors?.activity ||
                    errors?.general ||
                    `Ошибка изменения активности для потока "${schedule.title}".`
                )
            },
        }
    )
}

/** Ссылка для табов локалей */
const localeLink = (locale) => route('admin.courseSchedules.index', { locale })
</script>

<template>
    <AdminLayout :title="t('schedules')">
        <template #header>
            <TitlePage>{{ t('schedules') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <!-- Добавить модуль -->
                    <DefaultButton :href="route('admin.courseSchedules.create')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"
                                />
                            </svg>
                        </template>
                        {{ t('addSchedule') }}
                    </DefaultButton>

                    <BulkActionSelect
                        v-if="schedulesCount"
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
                        <CountTable v-if="schedulesCount">
                            {{ schedulesCount }}
                        </CountTable>

                        <!-- Переключатель вида (таблица / карточки), общий для админки -->
                        <ToggleViewButton v-model:viewMode="viewMode" />
                    </div>

                </div>

                <SearchInput
                    v-if="schedulesCount"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <!-- Таблица -->
                <CourseScheduleTable
                    v-if="viewMode === 'table'"
                    :schedules="paginatedSchedules"
                    :selected-schedules="selectedSchedules"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @clone="cloneSchedule"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectSchedule"
                    @toggle-all="toggleAll"
                />

                <!-- Карточки -->
                <CourseScheduleCardGrid
                    v-else
                    :schedules="paginatedSchedules"
                    :selected-schedules="selectedSchedules"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @clone="cloneSchedule"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectSchedule"
                    @toggle-all="toggleAll"
                />

                <div
                    class="flex justify-between items-center flex-col md:flex-row my-1"
                    v-if="schedulesCount"
                >
                    <ItemsPerPageSelect
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredSchedules.length"
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
            :onConfirm="deleteSchedule"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
