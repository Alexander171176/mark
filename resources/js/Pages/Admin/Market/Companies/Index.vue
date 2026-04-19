<script setup>
import { defineProps, ref, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import SearchInput from '@/Components/Admin/Search/SearchInput.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import CountTable from '@/Components/Admin/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/Select/ItemsPerPageSelect.vue'
import Pagination from '@/Components/Admin/Pagination/Pagination.vue'
import ToggleViewButton from '@/Components/Admin/Buttons/ToggleViewButton.vue'
import DangerModal from '@/Components/Admin/Modal/DangerModal.vue'

// новые компоненты под компании
import BulkActionSelect from '@/Components/Admin/MarketCompany/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/MarketCompany/Sort/SortSelect.vue'
import MarketCompanyTable from '@/Components/Admin/MarketCompany/Table/MarketCompanyTable.vue'
import MarketCompanyCardGrid from '@/Components/Admin/MarketCompany/View/MarketCompanyCardGrid.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    companies: {
        type: Array,
        default: () => [],
    },
    companiesCount: {
        type: Number,
        default: 0,
    },
    adminCountMarketCompanies: Number,
    adminSortMarketCompanies: String,
})

/**
 * Вид отображения
 */
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')

watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode', val)
})

/**
 * Кол-во элементов на странице
 * Пока локально. Позже можно привязать к настройкам, как у assignments.
 */
const itemsPerPage = ref(props.adminCountMarketCompanies)
watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountMarketCompanies'), { value: newVal },
        {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

/**
 * Параметр сортировки
 */
const sortParam = ref(props.adminSortMarketCompanies)
watch(sortParam, (newVal) => {
    router.put(route('admin.settings.updateAdminSortMarketCompanies'), { value: newVal },
        {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info('Сортировка успешно изменена'),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления сортировки.'),
    })
})

/**
 * Удаление
 */
const showConfirmDeleteModal = ref(false)
const companyToDeleteId = ref(null)
const companyToDeleteName = ref('')

const confirmDelete = (id, name) => {
    companyToDeleteId.value = id
    companyToDeleteName.value = name
    showConfirmDeleteModal.value = true
}

const closeModal = () => {
    showConfirmDeleteModal.value = false
    companyToDeleteId.value = null
    companyToDeleteName.value = ''
}

const deleteCompany = () => {
    if (companyToDeleteId.value === null) return

    const idToDelete = companyToDeleteId.value
    const nameToDelete = companyToDeleteName.value

    router.delete(route('admin.marketCompanies.destroy', { marketCompany: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            closeModal()
            toast.success(`Компания "${nameToDelete || 'ID: ' + idToDelete}" удалена.`)
        },
        onError: (errors) => {
            closeModal()
            const errorMsg =
                errors.general ||
                errors[Object.keys(errors)[0]] ||
                'Произошла ошибка при удалении компании.'
            toast.error(errorMsg)
        },
        onFinish: () => {
            companyToDeleteId.value = null
            companyToDeleteName.value = ''
        },
    })
}

/**
 * Переключение активности одной компании
 */
const toggleActivity = (company) => {
    const newActivity = !company.activity
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(
        route('admin.actions.marketCompanies.updateActivity', { marketCompany: company.id }),
        { activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                company.activity = newActivity
                toast.success(`Компания "${company.name}" ${actionText}.`)
            },
            onError: (errors) => {
                toast.error(
                    errors.activity ||
                    errors.general ||
                    `Ошибка изменения активности для "${company.name}".`
                )
            },
        }
    )
}

/**
 * Поиск и пагинация
 */
const currentPage = ref(1)
const searchQuery = ref('')

/**
 * Сортировка массива компаний
 */
const sortCompanies = (companies) => {
    const value = sortParam.value
    const list = companies.slice()

    if (value === 'idAsc') {
        return list.sort((a, b) => a.id - b.id)
    }

    if (value === 'idDesc') {
        return list.sort((a, b) => b.id - a.id)
    }

    if (value === 'sortAsc') {
        return list.sort((a, b) => (a.sort ?? 0) - (b.sort ?? 0))
    }

    if (value === 'sortDesc') {
        return list.sort((a, b) => (b.sort ?? 0) - (a.sort ?? 0))
    }

    if (value === 'nameAsc') {
        return list.sort((a, b) => (a.name || '').localeCompare(b.name || ''))
    }

    if (value === 'nameDesc') {
        return list.sort((a, b) => (b.name || '').localeCompare(a.name || ''))
    }

    if (value === 'activity') {
        return list.filter((company) => company.activity)
    }

    if (value === 'inactive') {
        return list.filter((company) => !company.activity)
    }

    return list
}

/**
 * Фильтрация + сортировка
 */
const filteredCompanies = computed(() => {
    let filtered = props.companies || []

    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase()

        filtered = filtered.filter((company) =>
            (company.name || '').toLowerCase().includes(q) ||
            (company.brand_name || '').toLowerCase().includes(q) ||
            (company.legal_name || '').toLowerCase().includes(q) ||
            (company.slug || '').toLowerCase().includes(q) ||
            (company.email || '').toLowerCase().includes(q) ||
            (company.phone || '').toLowerCase().includes(q) ||
            (company.city || '').toLowerCase().includes(q) ||
            (company.bin_iin || '').toLowerCase().includes(q)
        )
    }

    return sortCompanies(filtered)
})

/**
 * Пагинация
 */
const paginatedCompanies = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value
    return filteredCompanies.value.slice(start, start + itemsPerPage.value)
})

const totalPages = computed(() =>
    Math.ceil((filteredCompanies.value.length || 0) / itemsPerPage.value)
)

/**
 * Drag & Drop сортировка
 */
const handleSortOrderUpdate = (orderedIds) => {
    const startSort = (currentPage.value - 1) * itemsPerPage.value

    const sortData = orderedIds.map((id, index) => ({
        id,
        sort: startSort + index,
    }))

    router.put(
        route('admin.actions.marketCompanies.updateSortBulk'),
        { companies: sortData },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                toast.success('Порядок компаний успешно обновлён.')
            },
            onError: (errors) => {
                console.error('Ошибка обновления сортировки компаний:', errors)
                toast.error(
                    errors?.general ||
                    errors?.companies ||
                    'Не удалось обновить порядок компаний.'
                )
                router.reload({ only: ['companies'], preserveScroll: true })
            },
        }
    )
}

/**
 * Массовые действия
 */
const selectedCompanies = ref([])

const toggleAll = ({ ids, checked }) => {
    if (checked) {
        selectedCompanies.value = [...ids]
    } else {
        selectedCompanies.value = []
    }
}

const toggleSelectCompany = (id) => {
    const idx = selectedCompanies.value.indexOf(id)
    if (idx > -1) {
        selectedCompanies.value.splice(idx, 1)
    } else {
        selectedCompanies.value.push(id)
    }
}

const bulkToggleActivity = (newActivity) => {
    if (!selectedCompanies.value.length) {
        toast.warning('Выберите компании для активации/деактивации.')
        return
    }

    router.put(
        route('admin.actions.marketCompanies.bulkUpdateActivity'),
        { ids: selectedCompanies.value, activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                const updatedIds = [...selectedCompanies.value]
                selectedCompanies.value = []

                paginatedCompanies.value.forEach((company) => {
                    if (updatedIds.includes(company.id)) {
                        company.activity = newActivity
                    }
                })

                toast.success('Активность компаний массово обновлена.')
            },
            onError: (errors) => {
                const msg =
                    errors?.ids ||
                    errors?.activity ||
                    errors?.general ||
                    'Не удалось массово обновить активность компаний.'
                toast.error(msg)
            },
        }
    )
}

const bulkDelete = () => {
    if (!selectedCompanies.value.length) {
        toast.warning('Выберите хотя бы одну компанию для удаления.')
        return
    }

    if (!confirm('Вы уверены, что хотите удалить выбранные компании?')) {
        return
    }

    router.delete(route('admin.actions.marketCompanies.bulkDestroy'), {
        data: { ids: selectedCompanies.value },
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            selectedCompanies.value = []
            toast.success('Массовое удаление компаний успешно завершено.')
        },
        onError: (errors) => {
            const errorKey = Object.keys(errors)[0]
            const errorMessage =
                errors[errorKey] || 'Произошла ошибка при удалении компаний.'
            toast.error(errorMessage)
        },
    })
}

const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        selectedCompanies.value = paginatedCompanies.value.map((company) => company.id)
    } else if (action === 'deselectAll') {
        selectedCompanies.value = []
    } else if (action === 'activate') {
        bulkToggleActivity(true)
    } else if (action === 'deactivate') {
        bulkToggleActivity(false)
    } else if (action === 'delete') {
        bulkDelete()
    }

    event.target.value = ''
}
</script>

<template>
    <AdminLayout :title="t('marketCompanies')">
        <template #header>
            <TitlePage>{{ t('marketCompanies') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <DefaultButton :href="route('admin.marketCompanies.create')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"
                                />
                            </svg>
                        </template>
                        {{ t('addMarketCompany') }}
                    </DefaultButton>

                    <BulkActionSelect
                        v-if="companiesCount"
                        @change="handleBulkAction"
                    />
                </div>

                <div class="flex items-center justify-between my-2">
                    <div class="flex items-center space-x-3">
                        <CountTable v-if="companiesCount">
                            {{ companiesCount }}
                        </CountTable>
                    </div>
                    <div class="flex items-center space-x-3">
                        <ToggleViewButton v-model:viewMode="viewMode" />
                    </div>
                </div>

                <SearchInput
                    v-if="companiesCount"
                    v-model="searchQuery"
                    :placeholder="t('searchByName')"
                />

                <MarketCompanyTable
                    v-if="viewMode === 'table'"
                    :companies="paginatedCompanies"
                    :selected-companies="selectedCompanies"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectCompany"
                    @toggle-all="toggleAll"
                />

                <MarketCompanyCardGrid
                    v-else
                    :companies="paginatedCompanies"
                    :selected-companies="selectedCompanies"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectCompany"
                    @toggle-all="toggleAll"
                />

                <div
                    v-if="companiesCount"
                    class="flex justify-between items-center flex-col md:flex-row my-1"
                >
                    <ItemsPerPageSelect
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredCompanies.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="(val) => (sortParam = val)"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            @close="closeModal"
            :onCancel="closeModal"
            :onConfirm="deleteCompany"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
