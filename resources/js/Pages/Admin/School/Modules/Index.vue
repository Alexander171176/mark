<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 * Список модулей (паттерн)
 */
import { defineProps, ref, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router, Link } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import SearchInput from '@/Components/Admin/Search/SearchInput.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import BulkActionSelect from '@/Components/Admin/Module/Select/BulkActionSelect.vue'
import CountTable from '@/Components/Admin/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/Select/ItemsPerPageSelect.vue'
import ModuleTable from '@/Components/Admin/Module/Table/ModuleTable.vue'
import ModuleCardGrid from '@/Components/Admin/Module/View/ModuleCardGrid.vue'
import DangerModal from '@/Components/Admin/Modal/DangerModal.vue'
import Pagination from '@/Components/Admin/Pagination/Pagination.vue'
import SortSelect from '@/Components/Admin/Module/Sort/SortSelect.vue'
import ToggleViewButton from '@/Components/Admin/Buttons/ToggleViewButton.vue'

// --- Инициализация экземпляр i18n, toast ---
const { t } = useI18n()
const toast = useToast()

/**
 * Входные свойства компонента.
 */
const props = defineProps({
    modules: Array,
    modulesCount: Number,
    adminCountLearningModules: Number,
    adminSortLearningModules: String,
    currentLocale: String,
    availableLocales: Array,
})

/** Вид: таблица или карточки (общий ключ admin_view_mode) */
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')

watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode', val)
})

/** Кол-во элементов на странице */
const itemsPerPage = ref(props.adminCountLearningModules || 10)

/**
 * Наблюдатель за изменением количества элементов на странице.
 */
watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountLearningModules'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) =>
            toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

/** Параметр сортировки */
const sortParam = ref(props.adminSortLearningModules || 'idDesc')

/**
 * Наблюдатель за изменением параметра сортировки.
 */
watch(sortParam, (newVal) => {
    router.put(route('admin.settings.updateAdminSortLearningModules'), { value: newVal }, {
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
const moduleToDeleteId = ref(null)
const moduleToDeleteTitle = ref('')

/**
 * Открывает модальное окно подтверждения удаления с входными переменными.
 */
const confirmDelete = (id, title) => {
    moduleToDeleteId.value = id
    moduleToDeleteTitle.value = title
    showConfirmDeleteModal.value = true
}

/**
 * Закрывает модальное окно подтверждения и сбрасывает связанные переменные.
 */
const closeModal = () => {
    showConfirmDeleteModal.value = false
    moduleToDeleteId.value = null
    moduleToDeleteTitle.value = ''
}

/**
 * Отправляет запрос на удаление.
 */
const deleteModule = () => {
    if (moduleToDeleteId.value === null) return
    const idToDelete = moduleToDeleteId.value
    const titleToDelete = moduleToDeleteTitle.value

    router.delete(route('admin.modules.destroy', { module: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            closeModal()
            toast.success(`Модуль "${titleToDelete || 'ID: ' + idToDelete}" удалён.`)
        },
        onError: (errors) => {
            closeModal()
            const errorMsg =
                errors.general ||
                errors[Object.keys(errors)[0]] ||
                'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Модуль: ${titleToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => {
            moduleToDeleteId.value = null
            moduleToDeleteTitle.value = ''
        },
    })
}

/** Пагинация и поиск */
const currentPage = ref(1)
const searchQuery = ref('')

/** Сортировка массива модулей */
const sortModules = (modules) => {
    const value = sortParam.value
    const list = modules.slice()

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
    if (value === 'activity') return modules.filter(m => m.activity)
    if (value === 'inactive') return modules.filter(m => !m.activity)

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
const filteredModules = computed(() => {
    let filtered = props.modules || []

    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase()
        filtered = filtered.filter(m =>
            (m.title || '').toLowerCase().includes(q) ||
            (m.slug || '').toLowerCase().includes(q)
        )
    }

    return sortModules(filtered)
})

/** Пагинация */
const paginatedModules = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value
    return filteredModules.value.slice(start, start + itemsPerPage.value)
})

/**
 * Вычисляемое свойство, возвращающее общее количество страниц пагинации.
 */
const totalPages = computed(() =>
    Math.ceil((filteredModules.value.length || 0) / itemsPerPage.value)
)

/** Обновление сортировки (drag&drop) */
const handleSortOrderUpdate = (orderedIds) => {
    const startSort = (currentPage.value - 1) * itemsPerPage.value

    const sortData = orderedIds.map((id, index) => ({
        id,
        sort: startSort + index + 1,
    }))

    router.put(
        route('admin.actions.modules.updateSortBulk'),
        { modules: sortData },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.success('Порядок модулей успешно обновлён.'),
            onError: (errors) => {
                console.error('Ошибка обновления сортировки модулей:', errors)
                toast.error(
                    errors?.general ||
                    errors?.modules ||
                    'Не удалось обновить порядок модулей.'
                )
                router.reload({ only: ['modules'], preserveScroll: true })
            },
        }
    )
}

/** Массовые действия */
const selectedModules = ref([])

/**
 * Логика выбора всех для массовых действий.
 */
const toggleAll = ({ ids, checked }) => {
    if (checked) {
        selectedModules.value = [...ids]
    } else {
        selectedModules.value = []
    }
}

/**
 * Обрабатывает событие выбора/снятия выбора одной строки.
 */
const toggleSelectModule = (id) => {
    const idx = selectedModules.value.indexOf(id)
    if (idx > -1) selectedModules.value.splice(idx, 1)
    else selectedModules.value.push(id)
}

/**
 * Выполняет массовое включение/выключение активности выбранных.
 */
const bulkToggleActivity = (newActivity) => {
    if (!selectedModules.value.length) {
        toast.warning('Выберите модули для активации/деактивации')
        return
    }

    router.put(
        route('admin.actions.modules.bulkUpdateActivity'),
        { ids: selectedModules.value, activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                toast.success('Активность модулей массово обновлена')
                const updatedIds = [...selectedModules.value]
                selectedModules.value = []

                paginatedModules.value.forEach(m => {
                    if (updatedIds.includes(m.id)) m.activity = newActivity
                })
            },
            onError: (errors) => {
                const msg =
                    errors?.ids || errors?.activity || errors?.general ||
                    'Не удалось массово обновить активность модулей'
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
        selectedModules.value = paginatedModules.value.map(m => m.id)
    } else if (action === 'deselectAll') {
        selectedModules.value = []
    } else if (action === 'activate') {
        bulkToggleActivity(true)
    } else if (action === 'deactivate') {
        bulkToggleActivity(false)
    }

    event.target.value = ''
}

/** Переключение активности одного модуля */
const toggleActivity = (module) => {
    const newActivity = !module.activity
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(
        route('admin.actions.modules.updateActivity', { module: module.id }),
        { activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                module.activity = newActivity
                toast.success(`Модуль "${module.title}" ${actionText}.`)
            },
            onError: (errors) => {
                toast.error(
                    errors?.activity ||
                    errors?.general ||
                    `Ошибка изменения активности для модуля "${module.title}".`
                )
            },
        }
    )
}

/** Ссылка для табов локалей */
const localeLink = (locale) => route('admin.modules.index', { locale })
</script>

<template>
    <AdminLayout :title="t('modules')">
        <template #header>
            <TitlePage>{{ t('modules') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <!-- Добавить модуль -->
                    <DefaultButton :href="route('admin.modules.create')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"
                                />
                            </svg>
                        </template>
                        {{ t('addModule') }}
                    </DefaultButton>

                    <BulkActionSelect
                        v-if="modulesCount"
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
                        <CountTable v-if="modulesCount">
                            {{ modulesCount }}
                        </CountTable>

                        <ToggleViewButton v-model:viewMode="viewMode" />
                    </div>

                </div>

                <SearchInput
                    v-if="modulesCount"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <!-- Таблица -->
                <ModuleTable
                    v-if="viewMode === 'table'"
                    :modules="paginatedModules"
                    :selected-modules="selectedModules"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectModule"
                    @toggle-all="toggleAll"
                />

                <!-- Карточки -->
                <ModuleCardGrid
                    v-else
                    :modules="paginatedModules"
                    :selected-modules="selectedModules"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectModule"
                    @toggle-all="toggleAll"
                />

                <div
                    class="flex justify-between items-center flex-col md:flex-row my-1"
                    v-if="modulesCount"
                >
                    <ItemsPerPageSelect
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredModules.length"
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
            :onConfirm="deleteModule"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
