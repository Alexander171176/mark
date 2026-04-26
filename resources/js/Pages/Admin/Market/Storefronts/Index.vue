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

import BulkActionSelect from '@/Components/Admin/MarketStorefront/Select/BulkActionSelect.vue'
import SortSelect from '@/Components/Admin/MarketStorefront/Sort/SortSelect.vue'
import MarketStorefrontTable from '@/Components/Admin/MarketStorefront/Table/MarketStorefrontTable.vue'
import MarketStorefrontCardGrid from '@/Components/Admin/MarketStorefront/View/MarketStorefrontCardGrid.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    storefronts: {
        type: Array,
        default: () => [],
    },
    storefrontsCount: {
        type: Number,
        default: 0,
    },
    adminCountMarketStorefronts: Number,
    adminSortMarketStorefronts: String,
})

const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')

watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode', val)
})

const itemsPerPage = ref(props.adminCountMarketStorefronts || 15)
watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountMarketStorefronts'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

const sortParam = ref(props.adminSortMarketStorefronts || 'idDesc')
watch(sortParam, (newVal) => {
    router.put(route('admin.settings.updateAdminSortMarketStorefronts'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info('Сортировка успешно изменена'),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления сортировки.'),
    })
})

const showConfirmDeleteModal = ref(false)
const storefrontToDeleteId = ref(null)
const storefrontToDeleteName = ref('')

const confirmDelete = (id, name) => {
    storefrontToDeleteId.value = id
    storefrontToDeleteName.value = name
    showConfirmDeleteModal.value = true
}

const closeModal = () => {
    showConfirmDeleteModal.value = false
    storefrontToDeleteId.value = null
    storefrontToDeleteName.value = ''
}

const deleteStorefront = () => {
    if (storefrontToDeleteId.value === null) return

    const idToDelete = storefrontToDeleteId.value
    const nameToDelete = storefrontToDeleteName.value

    router.delete(route('admin.marketStorefronts.destroy', { marketStorefront: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            closeModal()
            toast.success(`Витрина "${nameToDelete || 'ID: ' + idToDelete}" удалена.`)
        },
        onError: (errors) => {
            closeModal()
            const errorMsg =
                errors.general ||
                errors[Object.keys(errors)[0]] ||
                'Произошла ошибка при удалении витрины.'
            toast.error(errorMsg)
        },
        onFinish: () => {
            storefrontToDeleteId.value = null
            storefrontToDeleteName.value = ''
        },
    })
}

const toggleActivity = (storefront) => {
    const newActivity = !storefront.activity
    const actionText = newActivity ? t('activated') : t('deactivated')

    router.put(
        route('admin.actions.marketStorefronts.updateActivity', { marketStorefront: storefront.id }),
        { activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                storefront.activity = newActivity
                toast.success(`Витрина "${storefront.slug}" ${actionText}.`)
            },
            onError: (errors) => {
                toast.error(
                    errors.activity ||
                    errors.general ||
                    `Ошибка изменения активности для "${storefront.slug}".`
                )
            },
        }
    )
}

const currentPage = ref(1)
const searchQuery = ref('')

const sortStorefronts = (storefronts) => {
    const value = sortParam.value
    const list = storefronts.slice()

    if (value === 'idAsc') return list.sort((a, b) => a.id - b.id)
    if (value === 'idDesc') return list.sort((a, b) => b.id - a.id)
    if (value === 'sortAsc') return list.sort((a, b) => (a.sort ?? 0) - (b.sort ?? 0))
    if (value === 'sortDesc') return list.sort((a, b) => (b.sort ?? 0) - (a.sort ?? 0))
    if (value === 'slugAsc') return list.sort((a, b) => (a.slug || '').localeCompare(b.slug || ''))
    if (value === 'slugDesc') return list.sort((a, b) => (b.slug || '').localeCompare(a.slug || ''))
    if (value === 'activity') return list.filter((item) => item.activity)
    if (value === 'inactive') return list.filter((item) => !item.activity)
    if (value === 'main') return list.filter((item) => item.is_main)
    if (value === 'notMain') return list.filter((item) => !item.is_main)

    return list
}

const filteredStorefronts = computed(() => {
    let filtered = props.storefronts || []

    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase()

        filtered = filtered.filter((storefront) =>
            (storefront.slug || '').toLowerCase().includes(q) ||
            (storefront.domain || '').toLowerCase().includes(q) ||
            (storefront.subdomain || '').toLowerCase().includes(q) ||
            (storefront.primary_host || '').toLowerCase().includes(q) ||
            (storefront.default_locale || '').toLowerCase().includes(q) ||
            (storefront.note || '').toLowerCase().includes(q) ||
            (storefront.company?.name || '').toLowerCase().includes(q) ||
            (storefront.company?.brand_name || '').toLowerCase().includes(q) ||
            (storefront.default_currency?.title || '').toLowerCase().includes(q) ||
            (storefront.default_currency?.code || '').toLowerCase().includes(q)
        )
    }

    return sortStorefronts(filtered)
})

const paginatedStorefronts = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value
    return filteredStorefronts.value.slice(start, start + itemsPerPage.value)
})

const handleSortOrderUpdate = (orderedIds) => {
    const startSort = (currentPage.value - 1) * itemsPerPage.value

    const sortData = orderedIds.map((id, index) => ({
        id,
        sort: startSort + index,
    }))

    router.put(
        route('admin.actions.marketStorefronts.updateSortBulk'),
        { storefronts: sortData },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                toast.success('Порядок витрин успешно обновлён.')
            },
            onError: (errors) => {
                console.error('Ошибка обновления сортировки витрин:', errors)
                toast.error(
                    errors?.general ||
                    errors?.storefronts ||
                    'Не удалось обновить порядок витрин.'
                )
                router.reload({ only: ['storefronts'], preserveScroll: true })
            },
        }
    )
}

const selectedStorefronts = ref([])

const toggleAll = ({ ids, checked }) => {
    selectedStorefronts.value = checked ? [...ids] : []
}

const toggleSelectStorefront = (id) => {
    const idx = selectedStorefronts.value.indexOf(id)
    if (idx > -1) {
        selectedStorefronts.value.splice(idx, 1)
    } else {
        selectedStorefronts.value.push(id)
    }
}

const bulkToggleActivity = (newActivity) => {
    if (!selectedStorefronts.value.length) {
        toast.warning('Выберите витрины для активации/деактивации.')
        return
    }

    router.put(
        route('admin.actions.marketStorefronts.bulkUpdateActivity'),
        { ids: selectedStorefronts.value, activity: newActivity },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                const updatedIds = [...selectedStorefronts.value]
                selectedStorefronts.value = []

                paginatedStorefronts.value.forEach((storefront) => {
                    if (updatedIds.includes(storefront.id)) {
                        storefront.activity = newActivity
                    }
                })

                toast.success('Активность витрин массово обновлена.')
            },
            onError: (errors) => {
                const msg =
                    errors?.ids ||
                    errors?.activity ||
                    errors?.general ||
                    'Не удалось массово обновить активность витрин.'
                toast.error(msg)
            },
        }
    )
}

const bulkDelete = () => {
    if (!selectedStorefronts.value.length) {
        toast.warning('Выберите хотя бы одну витрину для удаления.')
        return
    }

    if (!confirm('Вы уверены, что хотите удалить выбранные витрины?')) {
        return
    }

    router.delete(route('admin.actions.marketStorefronts.bulkDestroy'), {
        data: { ids: selectedStorefronts.value },
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            selectedStorefronts.value = []
            toast.success('Массовое удаление витрин успешно завершено.')
        },
        onError: (errors) => {
            const errorKey = Object.keys(errors)[0]
            const errorMessage =
                errors[errorKey] || 'Произошла ошибка при удалении витрин.'
            toast.error(errorMessage)
        },
    })
}

const handleBulkAction = (event) => {
    const action = event.target.value

    if (action === 'selectAll') {
        selectedStorefronts.value = paginatedStorefronts.value.map((item) => item.id)
    } else if (action === 'deselectAll') {
        selectedStorefronts.value = []
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
    <AdminLayout :title="t('marketStorefronts')">
        <template #header>
            <TitlePage>{{ t('marketStorefronts') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <DefaultButton :href="route('admin.marketStorefronts.create')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"
                                />
                            </svg>
                        </template>
                        {{ t('addMarketStorefront') }}
                    </DefaultButton>

                    <BulkActionSelect
                        v-if="storefrontsCount"
                        @change="handleBulkAction"
                    />
                </div>

                <div class="flex items-center justify-between my-2">
                    <div class="flex items-center space-x-3">
                        <CountTable v-if="storefrontsCount">
                            {{ storefrontsCount }}
                        </CountTable>
                    </div>
                    <div class="flex items-center space-x-3">
                        <ToggleViewButton v-model:viewMode="viewMode" />
                    </div>
                </div>

                <SearchInput
                    v-if="storefrontsCount"
                    v-model="searchQuery"
                    :placeholder="t('searchBySlug')"
                />

                <MarketStorefrontTable
                    v-if="viewMode === 'table'"
                    :storefronts="paginatedStorefronts"
                    :selected-storefronts="selectedStorefronts"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectStorefront"
                    @toggle-all="toggleAll"
                />

                <MarketStorefrontCardGrid
                    v-else
                    :storefronts="paginatedStorefronts"
                    :selected-storefronts="selectedStorefronts"
                    @toggle-activity="toggleActivity"
                    @delete="confirmDelete"
                    @update-sort-order="handleSortOrderUpdate"
                    @toggle-select="toggleSelectStorefront"
                    @toggle-all="toggleAll"
                />

                <div
                    v-if="storefrontsCount"
                    class="flex justify-between items-center flex-col md:flex-row my-1"
                >
                    <ItemsPerPageSelect
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredStorefronts.length"
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
            :onConfirm="deleteStorefront"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
