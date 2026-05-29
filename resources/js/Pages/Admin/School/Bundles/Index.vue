<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список наборов курсов школы
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

import BulkActionSelect from '@/Components/Admin/School/Bundle/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/School/Bundle/Sort/SortSelect.vue'
import BundleTable from '@/Components/Admin/School/Bundle/Table/BundleTable.vue'
import BundleCardGrid from '@/Components/Admin/School/Bundle/View/BundleCardGrid.vue'

// Локализация и Toast уведомления
const { t } = useI18n()
const toast = useToast()

// Props из контроллера
const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },

    bundles: { type: Array, default: () => [] },
    bundlesCount: { type: Number, default: 0 },

    adminSchoolBundlesPerPage: { type: Number, default: 10 },
    adminSchoolBundlesDefaultSort: { type: String, default: 'idDesc' },
})

// Режим отображения таблица/карточки
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')

// Сохранение режима отображения
watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode', val)
})

// Локальная копия наборов
const localBundles = ref([])

// Обновление локального списка наборов
watch(
    () => props.bundles,
    (newVal) => {
        localBundles.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

// Количество элементов на странице
const itemsPerPage = ref(props.adminSchoolBundlesPerPage || 10)

// Сохранение количества элементов
watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountBundles'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

// Параметр сортировки
const sortParam = ref(props.adminSchoolBundlesDefaultSort || 'idDesc')

// Сохранение параметра сортировки
watch(sortParam, (newVal) => {
    router.put(route('admin.settings.updateAdminSortBundles'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info('Сортировка успешно изменена'),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления сортировки.'),
    })
})

// Модальное окно удаления
const showConfirmDeleteModal = ref(false)

// ID удаляемого набора
const bundleToDeleteId = ref(null)

// Название удаляемого набора
const bundleToDeleteTitle = ref('')

// Открытие модального окна удаления
const confirmDelete = (bundleOrId, title = null) => {
    if (typeof bundleOrId === 'object') {
        bundleToDeleteId.value = bundleOrId.id
        bundleToDeleteTitle.value = title || bundleOrId.title || `ID: ${bundleOrId.id}`
    } else {
        bundleToDeleteId.value = bundleOrId
        bundleToDeleteTitle.value = title || `ID: ${bundleOrId}`
    }

    showConfirmDeleteModal.value = true
}

// Закрытие модального окна
const closeModal = () => {
    showConfirmDeleteModal.value = false
    bundleToDeleteId.value = null
    bundleToDeleteTitle.value = ''
}

// Удаление набора
const deleteBundle = () => {
    if (bundleToDeleteId.value === null) return

    const idToDelete = bundleToDeleteId.value
    const titleToDelete = bundleToDeleteTitle.value

    router.delete(route('admin.schoolBundles.destroy', {
        schoolBundle: idToDelete,
    }), {
        preserveScroll: true,
        preserveState: false,

        onSuccess: () => {
            toast.success(`Набор курсов "${titleToDelete || 'ID: ' + idToDelete}" удалён.`)
        },

        onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0]
            const errorMsg = errors.general || errors[errorKey] || 'Произошла ошибка при удалении.'

            toast.error(`${errorMsg} (Набор: ${titleToDelete || 'ID: ' + idToDelete})`)
        },

        onFinish: () => closeModal(),
    })
}

// Текущая страница
const currentPage = ref(1)

// Поисковый запрос
const searchQuery = ref('')

// Нормализация строки поиска
const normalize = (value) => {
    return (value ?? '').toString().trim().toLowerCase()
}

// Сортировка наборов
const sortBundles = (items) => {
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

    if (sortParam.value === 'activity') {
        return list.filter(item => !!item.activity)
    }

    if (sortParam.value === 'inactive') {
        return list.filter(item => !item.activity)
    }

    if (sortParam.value === 'views') {
        return list.sort((a, b) => (b.views ?? 0) - (a.views ?? 0))
    }

    if ([
        'likes',
        'courses_count',
        'images_count',
        'prices_count',
        'order_items_count',
    ].includes(sortParam.value)) {
        return list.sort((a, b) => (b[sortParam.value] ?? 0) - (a[sortParam.value] ?? 0))
    }

    if (sortParam.value === 'publishedAtAsc') {
        return list.sort((a, b) => {
            const aTime = a.published_at ? new Date(a.published_at).getTime() : 0
            const bTime = b.published_at ? new Date(b.published_at).getTime() : 0

            return aTime - bTime
        })
    }

    if (sortParam.value === 'publishedAtDesc') {
        return list.sort((a, b) => {
            const aTime = a.published_at ? new Date(a.published_at).getTime() : 0
            const bTime = b.published_at ? new Date(b.published_at).getTime() : 0

            return bTime - aTime
        })
    }

    return list
}

// Отфильтрованные наборы
const filteredBundles = computed(() => {
    let filtered = localBundles.value || []
    const q = normalize(searchQuery.value)

    if (!q) {
        return sortBundles(filtered)
    }

    filtered = filtered.filter((bundle) => {
        const title = normalize(bundle?.title)
        const subtitle = normalize(bundle?.subtitle)
        const slug = normalize(bundle?.slug)
        const short = normalize(bundle?.short)
        const description = normalize(bundle?.description)

        const hasCourse = (bundle?.courses || []).some(course =>
            normalize(course?.title).includes(q) ||
            normalize(course?.slug).includes(q)
        )

        return (
            title.includes(q) ||
            subtitle.includes(q) ||
            slug.includes(q) ||
            short.includes(q) ||
            description.includes(q) ||
            hasCourse
        )
    })

    return sortBundles(filtered)
})

// Наборы текущей страницы
const paginatedBundles = computed(() => {
    const per = Number(itemsPerPage.value || 20)
    const start = (currentPage.value - 1) * per

    return filteredBundles.value.slice(start, start + per)
})

// Сброс страницы при поиске и изменении лимита
watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

// Локальное обновление набора
const patchBundle = (bundleId, payload) => {
    const index = localBundles.value.findIndex(bundle => bundle.id === bundleId)

    if (index !== -1) {
        localBundles.value[index] = {
            ...localBundles.value[index],
            ...payload,
        }
    }
}

// Выбранные наборы
const selectedBundles = ref([])

// Выбрать / снять выбор со всех наборов
const toggleAll = ({ ids, checked }) => {
    selectedBundles.value = checked ? [...ids] : []
}

// Выбор одного набора
const toggleSelectBundle = (id) => {
    const index = selectedBundles.value.indexOf(id)

    if (index > -1) {
        selectedBundles.value.splice(index, 1)
    } else {
        selectedBundles.value.push(id)
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

    router.put(route('admin.actions.schoolBundles.updateSortBulk'), { items }, {
        preserveScroll: true,
        preserveState: true,

        onSuccess: () => {
            toast.success('Порядок наборов курсов успешно обновлён.')
        },

        onError: (errors) => {
            console.error('Ошибка обновления сортировки наборов курсов:', errors)

            toast.error(
                errors?.message ||
                errors?.general ||
                'Не удалось обновить порядок наборов курсов.'
            )

            router.reload({
                only: ['bundles'],
                preserveScroll: true,
            })
        },
    })
}

// Массовое обновление активности
const bulkToggleActivity = (newActivity) => {
    if (!selectedBundles.value.length) {
        toast.warning('Выберите наборы курсов для активации/деактивации.')
        return
    }

    const idsToUpdate = [...selectedBundles.value]

    router.put(route('admin.actions.schoolBundles.bulkUpdateActivity'), {
        ids: idsToUpdate,
        activity: newActivity,
    }, {
        preserveScroll: true,
        preserveState: true,

        onSuccess: () => {
            idsToUpdate.forEach(id => patchBundle(id, { activity: newActivity }))

            selectedBundles.value = []

            toast.success('Активность выбранных наборов курсов обновлена.')
        },

        onError: (errors) => {
            toast.error(
                errors?.ids ||
                errors?.activity ||
                errors?.general ||
                'Ошибка массового обновления активности.'
            )
        },
    })
}

// Обработка массовых действий
const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        selectedBundles.value = paginatedBundles.value.map(bundle => bundle.id)
    } else if (action === 'deselectAll') {
        selectedBundles.value = []
    } else if (action === 'activate') {
        bulkToggleActivity(true)
    } else if (action === 'deactivate') {
        bulkToggleActivity(false)
    }

    event.target.value = ''
}

// Переключение активности набора
const toggleActivity = (bundle) => {
    const newActivity = !bundle.activity
    const bundleTitle = bundle.title || `ID: ${bundle.id}`
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(route('admin.actions.schoolBundles.updateActivity', {
        schoolBundle: bundle.id,
    }), {
        activity: newActivity,
    }, {
        preserveScroll: true,
        preserveState: true,

        onSuccess: () => {
            patchBundle(bundle.id, { activity: newActivity })
            bundle.activity = newActivity

            toast.success(`Набор курсов "${bundleTitle}" ${actionText}.`)
        },

        onError: (errors) => {
            toast.error(
                errors?.activity ||
                errors?.general ||
                `Ошибка изменения активности для набора "${bundleTitle}".`
            )
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('bundles')">
        <template #header>
            <TitlePage>{{ t('bundles') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-3">
                    <DefaultButton :href="route('admin.schoolBundles.create')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"
                                />
                            </svg>
                        </template>
                        {{ t('addBundle') }}
                    </DefaultButton>
                </div>

                <SearchInput
                    v-if="bundlesCount"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <div
                    v-if="bundlesCount"
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
                    v-if="bundlesCount"
                    class="flex flex-col lg:flex-row items-center justify-between gap-3"
                >
                    <CountTable>{{ bundlesCount }}</CountTable>

                    <BulkActionSelect @change="handleBulkAction" />

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div
                    v-if="bundlesCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredBundles.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                </div>

                <BundleTable
                    v-if="viewMode === 'table'"
                    :bundles="paginatedBundles"
                    :selected-bundles="selectedBundles"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectBundle"
                    @toggle-all="toggleAll"
                />

                <BundleCardGrid
                    v-else
                    :bundles="paginatedBundles"
                    :selected-bundles="selectedBundles"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectBundle"
                    @toggle-all="toggleAll"
                />

                <div
                    v-if="bundlesCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredBundles.length"
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
            :onConfirm="deleteBundle"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
