<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список модулей школы
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
import ToggleViewButton from '@/Components/Admin/UI/Buttons/ToggleViewButton.vue'
import CountTable from '@/Components/Admin/UI/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/UI/Select/ItemsPerPageSelect.vue'
import ServerItemsPerPageSelect from '@/Components/Admin/UI/Select/ServerItemsPerPageSelect.vue'
import DangerModal from '@/Components/Admin/UI/Modal/DangerModal.vue'
import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue'
import AdminServerPagination from '@/Components/Admin/UI/Pagination/AdminServerPagination.vue'
import ProcessingModeSwitcher from '@/Components/Admin/UI/Processing/ProcessingModeSwitcher.vue'

import BulkActionSelect from '@/Components/Admin/School/SchoolModule/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/School/SchoolModule/Sort/SortSelect.vue'
import ModuleTable from '@/Components/Admin/School/SchoolModule/Table/ModuleTable.vue'
import ModuleCardGrid from '@/Components/Admin/School/SchoolModule/View/ModuleCardGrid.vue'

/* ==========================================================
 * БАЗОВЫЕ СЕРВИСЫ И PROPS
 * ========================================================== */

/** Локализация интерфейса */
const { t } = useI18n()

/** Уведомления */
const toast = useToast()

/** Данные страницы из Inertia */
const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },

    adminSchoolModulesProcessingMode: { type: String, default: 'frontend' },
    useServerProcessing: { type: Boolean, default: false },

    modules: { type: [Array, Object], default: () => [] },
    modulesCount: { type: Number, default: 0 },

    adminSchoolModulesPerPage: { type: Number, default: 6 },
    adminSchoolModulesDefaultSort: { type: String, default: 'idDesc' },

    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },

    errors: { type: Object, default: () => ({}) },
})

/* ==========================================================
 * РЕЖИМ ОТОБРАЖЕНИЯ
 * ========================================================== */

/** Текущий режим отображения (таблица / карточки) */
const viewMode = ref(localStorage.getItem('admin_view_mode_modules') || 'table')

/** Сохраняем выбранный вид локально */
watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode_modules', val)
})

/* ==========================================================
 * ИСТОЧНИК ДАННЫХ
 * ========================================================== */

/**
 * Унифицированный список модулей:
 * frontend → обычный массив
 * server → modules.data
 */
const modulesList = computed(() => {
    if (Array.isArray(props.modules)) {
        return props.modules
    }

    if (Array.isArray(props.modules?.data)) {
        return props.modules.data
    }

    return []
})

/* ==========================================================
 * ЛОКАЛЬНОЕ ХРАНИЛИЩЕ ДАННЫХ
 * ========================================================== */

/**
 * Локальная копия списка.
 * Используется для:
 * - локального поиска
 * - локальной сортировки
 * - моментального обновления UI
 */
const localModules = ref([])

watch(
    modulesList,
    (newVal) => {
        localModules.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

/* ==========================================================
 * НАСТРОЙКИ ПАГИНАЦИИ И СОРТИРОВКИ
 * ========================================================== */

/** Количество элементов на странице */
const itemsPerPage = ref(props.adminSchoolModulesPerPage || 6)

/** Сохраняем настройку количества элементов */
watch(itemsPerPage, (newVal) => {
    router.put(
        route('admin.settings.updateAdminCountModules'),
        { value: newVal },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
            onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
        }
    )
})

/** Текущий параметр сортировки */
const sortParam = ref(props.sortParam || props.adminSchoolModulesDefaultSort || 'idDesc')

/** Сохраняем сортировку и при server-режиме перезагружаем список */
watch(sortParam, (newVal) => {
    currentPage.value = 1

    router.put(
        route('admin.settings.updateAdminSortModules'),
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

            onError: (errors) => {
                toast.error(errors.value || 'Ошибка обновления сортировки.')
            },
        }
    )
})

/* ==========================================================
 * ПОИСК И ПАГИНАЦИЯ
 * ========================================================== */

/** Поисковый запрос */
const searchQuery = ref(props.search || '')

/** Текущая страница frontend-пагинации */
const currentPage = ref(1)

/* ==========================================================
 * ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ
 * ========================================================== */

/** Нормализация строки */
const normalize = (value) => (value ?? '').toString().trim().toLowerCase()

/** Безопасное преобразование в число */
const safeNumber = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

/** Безопасное преобразование даты */
const safeDate = (value) => {
    const time = new Date(value || 0).getTime()
    return Number.isFinite(time) ? time : 0
}

/* ==========================================================
 * ИЗВЛЕЧЕНИЕ ДАННЫХ ИЗ РЕСУРСОВ
 * ========================================================== */

/** Получение заголовка модуля */
const getModuleTitle = (module) => {
    return module?.title
        || module?.translation?.title
        || module?.translations?.[0]?.title
        || `ID: ${module?.id}`
}

/** Получение подзаголовка модуля */
const getModuleSubtitle = (module) => {
    return module?.subtitle
        || module?.translation?.subtitle
        || module?.translations?.[0]?.subtitle
        || ''
}

/** Получение краткого описания модуля */
const getModuleShort = (module) => {
    return module?.short
        || module?.translation?.short
        || module?.translations?.[0]?.short
        || ''
}

/** Получение описания модуля */
const getModuleDescription = (module) => {
    return module?.description
        || module?.translation?.description
        || module?.translations?.[0]?.description
        || ''
}

/** Получение slug модуля */
const getModuleSlug = (module) => {
    return module?.slug
        || module?.translation?.slug
        || module?.translations?.[0]?.slug
        || ''
}

/** Получение заголовка связанной сущности */
const getNestedTitle = (item) => {
    return item?.title
        || item?.name
        || item?.translation?.title
        || item?.translation?.name
        || item?.translations?.[0]?.title
        || item?.translations?.[0]?.name
        || ''
}

/** Получение заголовка курса */
const getCourseTitle = (module) => {
    return getNestedTitle(module?.course)
}

/** Получение slug курса */
const getCourseSlug = (module) => {
    return module?.course?.slug
        || module?.course?.translation?.slug
        || module?.course?.translations?.[0]?.slug
        || ''
}

/* ==========================================================
 * СОРТИРОВКА FRONTEND
 * ========================================================== */

/** Сортировка чисел ↑ */
const byNumberAsc = (field) => (a, b) =>
    safeNumber(a?.[field]) - safeNumber(b?.[field])
    || safeNumber(a?.id) - safeNumber(b?.id)

/** Сортировка чисел ↓ */
const byNumberDesc = (field) => (a, b) =>
    safeNumber(b?.[field]) - safeNumber(a?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

/** Сортировка строк ↑ */
const byStringAsc = (field) => (a, b) =>
    normalize(a?.[field]).localeCompare(normalize(b?.[field]), props.currentLocale)
    || safeNumber(a?.id) - safeNumber(b?.id)

/** Сортировка строк ↓ */
const byStringDesc = (field) => (a, b) =>
    normalize(b?.[field]).localeCompare(normalize(a?.[field]), props.currentLocale)
    || safeNumber(b?.id) - safeNumber(a?.id)

/**
 * Главный обработчик сортировки.
 * Должен совпадать со scopeSortByParam() модели и SortSelect.vue.
 */
const sortModules = (items) => {
    const list = (items || []).slice()

    if (sortParam.value === 'activity') return list.filter(item => !!item.activity)
    if (sortParam.value === 'inactive') return list.filter(item => !item.activity)

    const sortMap = {
        idAsc: byNumberAsc('id'),
        idDesc: byNumberDesc('id'),

        sortAsc: byNumberAsc('sort'),
        sortDesc: byNumberDesc('sort'),

        courseAsc: byNumberAsc('school_course_id'),
        courseDesc: byNumberDesc('school_course_id'),

        titleAsc: (a, b) =>
            normalize(getModuleTitle(a)).localeCompare(normalize(getModuleTitle(b)), props.currentLocale)
            || safeNumber(a?.id) - safeNumber(b?.id),

        titleDesc: (a, b) =>
            normalize(getModuleTitle(b)).localeCompare(normalize(getModuleTitle(a)), props.currentLocale)
            || safeNumber(b?.id) - safeNumber(a?.id),

        slugAsc: (a, b) =>
            normalize(getModuleSlug(a)).localeCompare(normalize(getModuleSlug(b)), props.currentLocale)
            || safeNumber(a?.id) - safeNumber(b?.id),

        slugDesc: (a, b) =>
            normalize(getModuleSlug(b)).localeCompare(normalize(getModuleSlug(a)), props.currentLocale)
            || safeNumber(b?.id) - safeNumber(a?.id),

        statusAsc: byStringAsc('status'),
        statusDesc: byStringDesc('status'),

        availabilityAsc: byStringAsc('availability'),
        availabilityDesc: byStringDesc('availability'),

        difficultyAsc: byNumberAsc('difficulty'),
        difficultyDesc: byNumberDesc('difficulty'),

        durationAsc: byNumberAsc('duration'),
        durationDesc: byNumberDesc('duration'),

        lessonsAsc: byNumberAsc('lessons_count'),
        lessonsDesc: byNumberDesc('lessons_count'),

        imagesAsc: byNumberAsc('images_count'),
        imagesDesc: byNumberDesc('images_count'),

        likesAsc: byNumberAsc('likes'),
        likesDesc: byNumberDesc('likes'),

        likesCountAsc: byNumberAsc('likes_count'),
        likesCountDesc: byNumberDesc('likes_count'),

        viewsAsc: byNumberAsc('views'),
        viewsDesc: byNumberDesc('views'),

        popularityAsc: byNumberAsc('popularity'),
        popularityDesc: byNumberDesc('popularity'),

        ratingCountAsc: byNumberAsc('rating_count'),
        ratingCountDesc: byNumberDesc('rating_count'),

        ratingAvgAsc: byNumberAsc('rating_avg'),
        ratingAvgDesc: byNumberDesc('rating_avg'),

        activityAsc: byNumberAsc('activity'),
        activityDesc: byNumberDesc('activity'),

        publishedAtAsc: (a, b) =>
            safeDate(a?.published_at) - safeDate(b?.published_at)
            || safeNumber(a?.id) - safeNumber(b?.id),

        publishedAtDesc: (a, b) =>
            safeDate(b?.published_at) - safeDate(a?.published_at)
            || safeNumber(b?.id) - safeNumber(a?.id),

        dateAsc: (a, b) =>
            safeDate(a?.published_at) - safeDate(b?.published_at)
            || safeNumber(a?.id) - safeNumber(b?.id),

        dateDesc: (a, b) =>
            safeDate(b?.published_at) - safeDate(a?.published_at)
            || safeNumber(b?.id) - safeNumber(a?.id),

        createdAtAsc: (a, b) =>
            safeDate(a?.created_at) - safeDate(b?.created_at)
            || safeNumber(a?.id) - safeNumber(b?.id),

        createdAtDesc: (a, b) =>
            safeDate(b?.created_at) - safeDate(a?.created_at)
            || safeNumber(b?.id) - safeNumber(a?.id),

        updatedAtAsc: (a, b) =>
            safeDate(a?.updated_at) - safeDate(b?.updated_at)
            || safeNumber(a?.id) - safeNumber(b?.id),

        updatedAtDesc: (a, b) =>
            safeDate(b?.updated_at) - safeDate(a?.updated_at)
            || safeNumber(b?.id) - safeNumber(a?.id),
    }

    return sortMap[sortParam.value]
        ? list.sort(sortMap[sortParam.value])
        : list
}

/* ==========================================================
 * ПОИСК FRONTEND
 * ========================================================== */

/**
 * Фильтрация списка.
 *
 * frontend:
 * поиск выполняется здесь
 *
 * server:
 * поиск выполняется контроллером
 */
const filteredModules = computed(() => {
    let filtered = localModules.value || []
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortModules(filtered)
    }

    filtered = filtered.filter((module) => {
        const title = normalize(getModuleTitle(module))
        const subtitle = normalize(getModuleSubtitle(module))
        const slug = normalize(getModuleSlug(module))
        const short = normalize(getModuleShort(module))
        const description = normalize(getModuleDescription(module))
        const courseTitle = normalize(getCourseTitle(module))
        const courseSlug = normalize(getCourseSlug(module))

        return title.includes(query)
            || subtitle.includes(query)
            || slug.includes(query)
            || short.includes(query)
            || description.includes(query)
            || courseTitle.includes(query)
            || courseSlug.includes(query)
    })

    return sortModules(filtered)
})

/* ==========================================================
 * ЛОКАЛЬНАЯ ПАГИНАЦИЯ
 * ========================================================== */

/** Разбиение списка по страницам */
const paginatedModules = computed(() => {
    const per = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * per

    return filteredModules.value.slice(start, start + per)
})

/**
 * Итоговый список:
 * frontend → локальная пагинация
 * server → данные сервера
 */
const displayedModules = computed(() => {
    return props.useServerProcessing
        ? modulesList.value
        : paginatedModules.value
})

watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

/* ==========================================================
 * УДАЛЕНИЕ
 * ========================================================== */

/** Состояние модального окна удаления */
const showConfirmDeleteModal = ref(false)
const moduleToDeleteId = ref(null)
const moduleToDeleteTitle = ref('')

/** Открыть подтверждение удаления */
const confirmDelete = (moduleOrId, title = null) => {
    if (typeof moduleOrId === 'object') {
        moduleToDeleteId.value = moduleOrId.id
        moduleToDeleteTitle.value = title || getModuleTitle(moduleOrId)
    } else {
        moduleToDeleteId.value = moduleOrId
        moduleToDeleteTitle.value = title || `ID: ${moduleOrId}`
    }

    showConfirmDeleteModal.value = true
}

/** Закрыть окно */
const closeModal = () => {
    showConfirmDeleteModal.value = false
    moduleToDeleteId.value = null
    moduleToDeleteTitle.value = ''
}

/** Выполнить удаление */
const deleteModule = () => {
    if (moduleToDeleteId.value === null) return

    const idToDelete = moduleToDeleteId.value
    const titleToDelete = moduleToDeleteTitle.value

    router.delete(route('admin.schoolModules.destroy', { schoolModule: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            toast.success(`Модуль "${titleToDelete || 'ID: ' + idToDelete}" удалён.`)
        },
        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            const errorMsg = errors.general || errors[errorKey] || 'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Модуль: ${titleToDelete || 'ID: ' + idToDelete})`)
        },
        onFinish: () => closeModal(),
    })
}

/* ==========================================================
 * ЛОКАЛЬНОЕ ОБНОВЛЕНИЕ UI
 * ========================================================== */

/**
 * Обновление записи локально
 * без полной перезагрузки страницы
 */
const patchModule = (moduleId, payload) => {
    const index = localModules.value.findIndex(module => module.id === moduleId)

    if (index !== -1) {
        localModules.value[index] = {
            ...localModules.value[index],
            ...payload,
        }
    }
}

/* ==========================================================
 * МАССОВЫЕ ОПЕРАЦИИ
 * ========================================================== */

/** Выбранные элементы */
const selectedModules = ref([])

/** Выбрать все */
const toggleAll = (payload) => {
    const checked = payload?.checked ?? payload?.target?.checked ?? false
    const ids = payload?.ids ?? displayedModules.value.map((module) => module.id)

    if (checked) {
        selectedModules.value = [...new Set([...selectedModules.value, ...ids])]
    } else {
        selectedModules.value = selectedModules.value.filter((id) => !ids.includes(id))
    }
}

/** Выбрать элемент */
const toggleSelectModule = (id) => {
    const index = selectedModules.value.indexOf(id)

    if (index > -1) {
        selectedModules.value.splice(index, 1)
    } else {
        selectedModules.value.push(id)
    }
}

/** Изменить порядок */
const handleSortOrderUpdate = (orderedIds) => {
    const startSort = (currentPage.value - 1) * itemsPerPage.value

    const items = orderedIds.map((id, index) => ({
        id,
        sort: startSort + index + 1,
    }))

    if (!items.length) return

    router.put(route('admin.actions.schoolModules.updateSortBulk'), { items }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.success('Порядок модулей успешно обновлён.'),
        onError: (errors) => {
            console.error('Ошибка обновления сортировки модулей:', errors)
            toast.error(errors?.message || errors?.general || 'Не удалось обновить порядок модулей.')

            router.reload({
                only: ['modules'],
                preserveScroll: true,
            })
        },
    })
}

/** Массовая активность */
const bulkToggleActivity = (newActivity) => {
    if (!selectedModules.value.length) {
        toast.warning('Выберите модули для активации/деактивации.')
        return
    }

    const idsToUpdate = [...selectedModules.value]

    router.put(route('admin.actions.schoolModules.bulkUpdateActivity'), {
        ids: idsToUpdate,
        activity: newActivity,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            idsToUpdate.forEach(id => patchModule(id, { activity: newActivity }))
            selectedModules.value = []
            toast.success('Активность выбранных модулей обновлена.')
        },
        onError: (errors) => {
            toast.error(errors?.ids || errors?.activity || errors?.general || 'Ошибка массового обновления активности.')
        },
    })
}

/** Обработчик массовых действий */
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
    }

    event.target.value = ''
}

/* ==========================================================
 * ОПЕРАЦИИ НАД ОДНОЙ ЗАПИСЬЮ
 * ========================================================== */

/** Переключение активности */
const toggleActivity = (module) => {
    const newActivity = !module.activity
    const moduleTitle = getModuleTitle(module)
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(route('admin.actions.schoolModules.updateActivity', {
        schoolModule: module.id,
    }), {
        activity: newActivity,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            patchModule(module.id, { activity: newActivity })
            module.activity = newActivity
            toast.success(`Модуль "${moduleTitle}" ${actionText}.`)
        },
        onError: (errors) => {
            toast.error(errors?.activity || errors?.general || `Ошибка изменения активности для модуля "${moduleTitle}".`)
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('modules')">
        <template #header>
            <TitlePage>{{ t('modules') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3">
                    <DefaultButton :href="route('admin.schoolModules.create')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0"
                                 viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"
                                />
                            </svg>
                        </template>
                        {{ t('addModule') }}
                    </DefaultButton>

                    <ProcessingModeSwitcher
                        setting-key="adminSchoolModulesProcessingMode"
                        :mode="adminSchoolModulesProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="modulesCount"
                    />
                </div>

                <SearchInput
                    v-if="modulesCount && !useServerProcessing"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <ServerSearchInput
                    v-if="modulesCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <div
                    v-if="modulesCount"
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
                        update-route="admin.settings.updateAdminCountModules"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="val => sortParam = val"
                    />
                </div>

                <div
                    v-if="modulesCount"
                    class="flex flex-col lg:flex-row items-center justify-between gap-3"
                >
                    <CountTable>{{ modulesCount }}</CountTable>

                    <BulkActionSelect @change="handleBulkAction" />

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div
                    v-if="modulesCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredModules.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="modules"
                    />
                </div>

                <ModuleTable
                    v-if="viewMode === 'table'"
                    :modules="displayedModules"
                    :selected-modules="selectedModules"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectModule"
                    @toggle-all="toggleAll"
                />

                <ModuleCardGrid
                    v-else
                    :modules="displayedModules"
                    :selected-modules="selectedModules"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectModule"
                    @toggle-all="toggleAll"
                />

                <div
                    v-if="modulesCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredModules.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="modules"
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
