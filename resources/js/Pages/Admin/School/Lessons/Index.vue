<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 * Список уроков (Lesson)
 */
import { defineProps, ref, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router, Link } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import SearchInput from '@/Components/Admin/Search/SearchInput.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import BulkActionSelect from '@/Components/Admin/Lesson/Select/BulkActionSelect.vue'
import CountTable from '@/Components/Admin/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/Select/ItemsPerPageSelect.vue'
import LessonTable from '@/Components/Admin/Lesson/Table/LessonTable.vue'
import LessonCardGrid from '@/Components/Admin/Lesson/View/LessonCardGrid.vue'
import DangerModal from '@/Components/Admin/Modal/DangerModal.vue'
import Pagination from '@/Components/Admin/Pagination/Pagination.vue'
import SortSelect from '@/Components/Admin/Lesson/Sort/SortSelect.vue'
import ToggleViewButton from '@/Components/Admin/Buttons/ToggleViewButton.vue'

// --- Инициализация экземпляр i18n, toast ---
const { t } = useI18n()
const toast = useToast()

/**
 * Входные свойства компонента.
 */
const props = defineProps({
    lessons: Array,
    lessonsCount: Number,
    adminCountLearningLessons: Number,
    adminSortLearningLessons: String,
    currentLocale: String,
    availableLocales: Array,
})

/** Вид: таблица или карточки (общий ключ для админки) */
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')

watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode', val)
})

/** Кол-во элементов на странице */
const itemsPerPage = ref(props.adminCountLearningLessons || 10)

/**
 * Наблюдатель за изменением количества элементов на странице.
 */
watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountLearningLessons'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) =>
            toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

/** Параметр сортировки */
const sortParam = ref(props.adminSortLearningLessons || 'idDesc')

/**
 * Наблюдатель за изменением параметра сортировки.
 */
watch(sortParam, (newVal) => {
    router.put(route('admin.settings.updateAdminSortLearningLessons'), { value: newVal }, {
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
const lessonToDeleteId = ref(null)
const lessonToDeleteTitle = ref('')

/**
 * Открывает модальное окно подтверждения удаления с входными переменными.
 */
const confirmDelete = (id, title) => {
    lessonToDeleteId.value = id
    lessonToDeleteTitle.value = title
    showConfirmDeleteModal.value = true
}

/**
 * Закрывает модальное окно подтверждения и сбрасывает связанные переменные.
 */
const closeModal = () => {
    showConfirmDeleteModal.value = false
    lessonToDeleteId.value = null
    lessonToDeleteTitle.value = ''
}

/**
 * Отправляет запрос на удаление.
 */
const deleteLesson = () => {
    if (lessonToDeleteId.value === null) return
    const idToDelete = lessonToDeleteId.value
    const titleToDelete = lessonToDeleteTitle.value

    router.delete(route('admin.lessons.destroy', { lesson: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            closeModal()
            toast.success(`Урок "${titleToDelete || 'ID: ' + idToDelete}" удалён.`)
        },
        onError: (errors) => {
            closeModal()
            const errorMsg =
                errors.general ||
                errors[Object.keys(errors)[0]] ||
                'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Урок: ${titleToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => {
            lessonToDeleteId.value = null
            lessonToDeleteTitle.value = ''
        },
    })
}

/**
 * Отправляет запрос для клонирования.
 */
const cloneLesson = (lessonObject) => { // Переименовываем параметр для ясности
    // Извлекаем ID из объекта
    const lessonId = lessonObject?.id; // Используем опциональную цепочку на случай undefined/null
    const lessonTitle = lessonObject?.title || `ID: ${lessonId}`; // Пытаемся получить title или используем ID

    // Проверяем, что ID получен
    if (typeof lessonId === 'undefined' || lessonId === null) {
        console.error("Не удалось получить ID урока для клонирования", lessonObject);
        toast.error("Не удалось определить урок для клонирования.");
        return;
    }

    // Используем confirm с извлеченным ID (или title)
    if (!confirm(`Вы уверены, что хотите клонировать урок "${lessonTitle}"?`)) {
        return;
    }

    // В route() передаем именно lessonId
    router.post(route('admin.actions.lessons.clone', {lesson: lessonId}), {}, {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            // Используем lessonTitle или lessonId в сообщении
            toast.success(`Урок "${lessonTitle}" успешно клонирован.`);
        },
        onError: (errors) => {
            const errorKey = Object.keys(errors)[0];
            const errorMessage = errors[errorKey] || `Ошибка клонирования урока "${lessonTitle}".`;
            toast.error(errorMessage);
        }
    });
};

/** Пагинация и поиск */
const currentPage = ref(1)
const searchQuery = ref('')

/** Сортировка массива уроков */
const sortLessons = (lessons) => {
    const value = sortParam.value
    const list = lessons.slice()

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
    if (value === 'activity') return lessons.filter(l => l.activity)
    if (value === 'inactive') return lessons.filter(l => !l.activity)

    // Числовые поля — сортировка по убыванию
    if ([
        'views',
        'likes',
        'popularity',
        'lessons_count',
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
const filteredLessons = computed(() => {
    let filtered = props.lessons || []

    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase()
        filtered = filtered.filter(l =>
            (l.title || '').toLowerCase().includes(q) ||
            (l.slug || '').toLowerCase().includes(q)
        )
    }

    return sortLessons(filtered)
})

/** Пагинация */
const paginatedLessons = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value
    return filteredLessons.value.slice(start, start + itemsPerPage.value)
})

/**
 * Вычисляемое свойство, возвращающее общее количество страниц пагинации.
 */
const totalPages = computed(() =>
    Math.ceil((filteredLessons.value.length || 0) / itemsPerPage.value)
)

/** Обновление сортировки (drag&drop) */
const handleSortOrderUpdate = (orderedIds) => {
    const startSort = (currentPage.value - 1) * itemsPerPage.value

    const sortData = orderedIds.map((id, index) => ({
        id,
        sort: startSort + index + 1,
    }))

    router.put(
        route('admin.actions.lessons.updateSortBulk'),
        { lessons: sortData },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.success('Порядок уроков успешно обновлён.'),
            onError: (errors) => {
                console.error('Ошибка обновления сортировки уроков:', errors)
                toast.error(
                    errors?.general ||
                    errors?.lessons ||
                    'Не удалось обновить порядок уроков.'
                )
                router.reload({ only: ['lessons'], preserveScroll: true })
            },
        }
    )
}

/** Массовые действия */
const selectedLessons = ref([])

/**
 * Логика выбора всех для массовых действий.
 */
const toggleAll = ({ ids, checked }) => {
    if (checked) {
        selectedLessons.value = [...ids]
    } else {
        selectedLessons.value = []
    }
}

/**
 * Обрабатывает событие выбора/снятия выбора одной строки.
 */
const toggleSelectLesson = (id) => {
    const idx = selectedLessons.value.indexOf(id)
    if (idx > -1) selectedLessons.value.splice(idx, 1)
    else selectedLessons.value.push(id)
}

/**
 * Выполняет массовое включение/выключение активности выбранных.
 */
const bulkToggleActivity = (newActivity) => {
    if (!selectedLessons.value.length) {
        toast.warning('Выберите уроки для активации/деактивации')
        return
    }

    router.put(
        route('admin.actions.lessons.bulkUpdateActivity'),
        { ids: selectedLessons.value, activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                toast.success('Активность уроков массово обновлена')
                const updatedIds = [...selectedLessons.value]
                selectedLessons.value = []

                paginatedLessons.value.forEach(l => {
                    if (updatedIds.includes(l.id)) l.activity = newActivity
                })
            },
            onError: (errors) => {
                const msg =
                    errors?.ids || errors?.activity || errors?.general ||
                    'Не удалось массово обновить активность уроков'
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
        selectedLessons.value = paginatedLessons.value.map(l => l.id)
    } else if (action === 'deselectAll') {
        selectedLessons.value = []
    } else if (action === 'activate') {
        bulkToggleActivity(true)
    } else if (action === 'deactivate') {
        bulkToggleActivity(false)
    }

    event.target.value = ''
}

/** Переключение активности одного урока */
const toggleActivity = (lesson) => {
    const newActivity = !lesson.activity
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(
        route('admin.actions.lessons.updateActivity', { lesson: lesson.id }),
        { activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                lesson.activity = newActivity
                toast.success(`Урок "${lesson.title}" ${actionText}.`)
            },
            onError: (errors) => {
                toast.error(
                    errors?.activity ||
                    errors?.general ||
                    `Ошибка изменения активности для урока "${lesson.title}".`
                )
            },
        }
    )
}

/** Ссылка для табов локалей */
const localeLink = (locale) => route('admin.lessons.index', { locale })
</script>

<template>
    <AdminLayout :title="t('lessons')">
        <template #header>
            <TitlePage>{{ t('lessons') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <!-- Добавить модуль -->
                    <DefaultButton :href="route('admin.lessons.create')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"
                                />
                            </svg>
                        </template>
                        {{ t('addLesson') }}
                    </DefaultButton>

                    <BulkActionSelect
                        v-if="lessonsCount"
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
                        <CountTable v-if="lessonsCount">
                            {{ lessonsCount }}
                        </CountTable>

                        <ToggleViewButton v-model:viewMode="viewMode" />
                    </div>
                </div>

                <SearchInput
                    v-if="lessonsCount"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <!-- Табличный вид -->
                <LessonTable
                    v-if="viewMode === 'table'"
                    :lessons="paginatedLessons"
                    :selected-lessons="selectedLessons"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @clone="cloneLesson"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectLesson"
                    @toggle-all="toggleAll"
                />

                <!-- Карточный вид -->
                <LessonCardGrid
                    v-else
                    :lessons="paginatedLessons"
                    :selected-lessons="selectedLessons"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @clone="cloneLesson"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectLesson"
                    @toggle-all="toggleAll"
                />

                <div
                    class="flex justify-between items-center flex-col md:flex-row my-1"
                    v-if="lessonsCount"
                >
                    <ItemsPerPageSelect
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredLessons.length"
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
            :onConfirm="deleteLesson"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
