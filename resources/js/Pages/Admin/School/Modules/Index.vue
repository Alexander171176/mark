<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список модулей школы
 */
import { computed, defineProps, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import SearchInput from '@/Components/Admin/UI/Search/SearchInput.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import ToggleViewButton from '@/Components/Admin/UI/Buttons/ToggleViewButton.vue'
import CountTable from '@/Components/Admin/UI/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/UI/Select/ItemsPerPageSelect.vue'
import DangerModal from '@/Components/Admin/UI/Modal/DangerModal.vue'
import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue'

import BulkActionSelect from '@/Components/Admin/School/Module/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/School/Module/Sort/SortSelect.vue'
import ModuleTable from '@/Components/Admin/School/Module/Table/ModuleTable.vue'
import ModuleCardGrid from '@/Components/Admin/School/Module/View/ModuleCardGrid.vue'

// Локализация и Toast уведомления
const { t } = useI18n()
const toast = useToast()

// Props из контроллера
const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },
    modules: { type: Array, default: () => [] },
    modulesCount: { type: Number, default: 0 },
    adminSchoolModulesPerPage: { type: Number, default: 10 },
    adminSchoolModulesDefaultSort: { type: String, default: 'idDesc' },
})

// Режим отображения (таблица / карточки)
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')

// Сохраняем режим отображения
watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode', val)
})

// Локальная копия модулей
const localModules = ref([])

// Обновление локального списка модулей
watch(
    () => props.modules,
    (newVal) => {
        localModules.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

// Количество элементов на странице
const itemsPerPage = ref(props.adminSchoolModulesPerPage || 10)

// Сохранение количества элементов
watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountModules'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

// Параметр сортировки
const sortParam = ref(props.adminSchoolModulesDefaultSort || 'idDesc')

// Сохранение параметра сортировки
watch(sortParam, (newVal) => {
    router.put(route('admin.settings.updateAdminSortModules'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info('Сортировка успешно изменена'),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления сортировки.'),
    })
})

// Модальное окно удаления, ID удаляемого модуля, название удаляемого модуля
const showConfirmDeleteModal = ref(false)
const moduleToDeleteId = ref(null)
const moduleToDeleteTitle = ref('')

// Открытие модального окна удаления
const confirmDelete = (moduleOrId, title = null) => {
    if (typeof moduleOrId === 'object') {
        moduleToDeleteId.value = moduleOrId.id
        moduleToDeleteTitle.value = title || moduleOrId.title || `ID: ${moduleOrId.id}`
    } else {
        moduleToDeleteId.value = moduleOrId
        moduleToDeleteTitle.value = title || `ID: ${moduleOrId}`
    }

    showConfirmDeleteModal.value = true
}

// Закрытие модального окна
const closeModal = () => {
    showConfirmDeleteModal.value = false
    moduleToDeleteId.value = null
    moduleToDeleteTitle.value = ''
}

// Удаление модуля
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

// Текущая страница
const currentPage = ref(1)

// Поисковый запрос
const searchQuery = ref('')

// Нормализация строки поиска
const normalize = (value) => (value ?? '').toString().trim().toLowerCase()

// Сортировка модулей
const sortModules = (items) => {
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

    if (sortParam.value === 'activity') return list.filter(item => !!item.activity)
    if (sortParam.value === 'inactive') return list.filter(item => !item.activity)

    if (sortParam.value === 'published_at') {
        return list.sort((a, b) => {
            const aTime = a.published_at ? new Date(a.published_at).getTime() : 0
            const bTime = b.published_at ? new Date(b.published_at).getTime() : 0

            return bTime - aTime
        })
    }

    if ([
        'views',
        'likes',
        'likes_count',
        'popularity',
        'rating_count',
        'rating_avg',
        'difficulty',
        'duration',
        'lessons_count',
        'images_count',
    ].includes(sortParam.value)) {
        return list.sort((a, b) => (b[sortParam.value] ?? 0) - (a[sortParam.value] ?? 0))
    }

    return list
}

// Отфильтрованные модули
const filteredModules = computed(() => {
    let filtered = localModules.value || []
    const q = normalize(searchQuery.value)

    if (!q) {
        return sortModules(filtered)
    }

    filtered = filtered.filter((module) => {
        const title = normalize(module?.title)
        const subtitle = normalize(module?.subtitle)
        const slug = normalize(module?.slug)
        const short = normalize(module?.short)
        const description = normalize(module?.description)
        const courseTitle = normalize(module?.course?.title)
        const courseSlug = normalize(module?.course?.slug)

        return (
            title.includes(q) ||
            subtitle.includes(q) ||
            slug.includes(q) ||
            short.includes(q) ||
            description.includes(q) ||
            courseTitle.includes(q) ||
            courseSlug.includes(q)
        )
    })

    return sortModules(filtered)
})

// Модули текущей страницы
const paginatedModules = computed(() => {
    const per = Number(itemsPerPage.value || 20)
    const start = (currentPage.value - 1) * per

    return filteredModules.value.slice(start, start + per)
})

// Сброс страницы при поиске и изменении лимита
watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

// Локальное обновление модуля
const patchModule = (moduleId, payload) => {
    const index = localModules.value.findIndex(module => module.id === moduleId)

    if (index !== -1) {
        localModules.value[index] = {
            ...localModules.value[index],
            ...payload,
        }
    }
}

// Выбранные модули
const selectedModules = ref([])

// Выбрать / снять выбор со всех модулей
const toggleAll = ({ ids, checked }) => {
    selectedModules.value = checked ? [...ids] : []
}

// Выбор одного модуля
const toggleSelectModule = (id) => {
    const index = selectedModules.value.indexOf(id)

    if (index > -1) {
        selectedModules.value.splice(index, 1)
    } else {
        selectedModules.value.push(id)
    }
}

// Обновление порядка сортировки
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

// Массовое обновление активности
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

// Обработка массовых действий
const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        selectedModules.value = paginatedModules.value.map(module => module.id)
    } else if (action === 'deselectAll') {
        selectedModules.value = []
    } else if (action === 'activate') {
        bulkToggleActivity(true)
    } else if (action === 'deactivate') {
        bulkToggleActivity(false)
    }

    event.target.value = ''
}

// Переключение активности модуля
const toggleActivity = (module) => {
    const newActivity = !module.activity
    const moduleTitle = module.title || `ID: ${module.id}`
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
                <div class="sm:flex sm:justify-between sm:items-center mb-3">
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
                </div>

                <SearchInput
                    v-if="modulesCount"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <div
                    v-if="modulesCount"
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
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredModules.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                </div>

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
                    v-if="modulesCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredModules.length"
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
            :onConfirm="deleteModule"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
