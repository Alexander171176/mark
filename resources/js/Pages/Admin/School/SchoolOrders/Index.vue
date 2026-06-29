<script setup>
/**
 * Список заказов школы
 * - режимы обработки: frontend | server | auto
 * - локальный/серверный поиск
 * - локальная/серверная пагинация
 * - сортировка должна совпадать с SchoolOrder::scopeSortByParam()
 */

import { computed, defineProps, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import SearchInput from '@/Components/Admin/UI/Search/SearchInput.vue'
import ServerSearchInput from '@/Components/Admin/UI/Search/ServerSearchInput.vue'
import CountTable from '@/Components/Admin/UI/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/UI/Select/ItemsPerPageSelect.vue'
import ServerItemsPerPageSelect from '@/Components/Admin/UI/Select/ServerItemsPerPageSelect.vue'
import ToggleViewButton from '@/Components/Admin/UI/Buttons/ToggleViewButton.vue'
import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue'
import AdminServerPagination from '@/Components/Admin/UI/Pagination/AdminServerPagination.vue'
import DangerModal from '@/Components/Admin/UI/Modal/DangerModal.vue'
import ProcessingModeSwitcher from '@/Components/Admin/UI/Processing/ProcessingModeSwitcher.vue'

import SortSelect from '@/Components/Admin/School/SchoolOrder/Sort/SortSelect.vue'
import OrderTable from '@/Components/Admin/School/SchoolOrder/Table/OrderTable.vue'
import OrderCardGrid from '@/Components/Admin/School/SchoolOrder/View/OrderCardGrid.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    currentLocale: { type: String, default: '' },

    useServerProcessing: { type: Boolean, default: false },
    adminSchoolOrdersProcessingMode: { type: String, default: 'frontend' },

    orders: { type: [Array, Object], default: () => [] },
    ordersCount: { type: Number, default: 0 },

    filters: { type: Object, default: () => ({}) },

    adminSchoolOrdersPerPage: { type: Number, default: 10 },
    adminSchoolOrdersDefaultSort: { type: String, default: 'idDesc' },

    sortParam: { type: String, default: '' },
    search: { type: String, default: '' },

    errors: { type: Object, default: () => ({}) },
})

const viewMode = ref(localStorage.getItem('admin_view_mode_orders') || 'table')

watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode_orders', val)
})

const ordersList = computed(() => {
    if (Array.isArray(props.orders)) return props.orders
    if (Array.isArray(props.orders?.data)) return props.orders.data

    return []
})

const localOrders = ref([])

watch(
    ordersList,
    (newVal) => {
        localOrders.value = JSON.parse(JSON.stringify(newVal || []))
    },
    { immediate: true, deep: true }
)

const itemsPerPage = ref(props.adminSchoolOrdersPerPage || 10)

watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountSchoolOrders'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

const sortParam = ref(
    props.sortParam ||
    props.adminSchoolOrdersDefaultSort ||
    'idDesc'
)

watch(sortParam, (newVal) => {
    currentPage.value = 1

    router.put(route('admin.settings.updateAdminSortSchoolOrders'), { value: newVal }, {
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
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления сортировки.'),
    })
})

const currentPage = ref(1)
const searchQuery = ref(props.search || props.filters?.search || '')

const showConfirmDeleteModal = ref(false)
const orderToDelete = ref(null)

const normalize = (value) => (value ?? '').toString().trim().toLowerCase()

const safeNumber = (value) => {
    const number = Number(value)
    return Number.isFinite(number) ? number : 0
}

const safeDate = (value) => {
    const time = new Date(value || 0).getTime()
    return Number.isFinite(time) ? time : 0
}

const getBuyerName = (order) => {
    return order?.buyer_name || order?.user?.name || ''
}

const getBuyerEmail = (order) => {
    return order?.buyer_email || order?.user?.email || ''
}

const getCourseTitle = (order) => {
    return order?.course?.title
        || order?.course?.translation?.title
        || order?.course?.translations?.[0]?.title
        || ''
}

const getScheduleTitle = (order) => {
    return order?.schedule?.title
        || order?.schedule?.translation?.title
        || order?.schedule?.translations?.[0]?.title
        || ''
}

const byNumberAsc = (field) => (a, b) =>
    safeNumber(a?.[field]) - safeNumber(b?.[field])
    || safeNumber(a?.id) - safeNumber(b?.id)

const byNumberDesc = (field) => (a, b) =>
    safeNumber(b?.[field]) - safeNumber(a?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

const byStringAsc = (field) => (a, b) =>
    normalize(a?.[field]).localeCompare(normalize(b?.[field]), props.currentLocale)
    || safeNumber(a?.id) - safeNumber(b?.id)

const byStringDesc = (field) => (a, b) =>
    normalize(b?.[field]).localeCompare(normalize(a?.[field]), props.currentLocale)
    || safeNumber(b?.id) - safeNumber(a?.id)

const byDateAsc = (field) => (a, b) =>
    safeDate(a?.[field]) - safeDate(b?.[field])
    || safeNumber(a?.id) - safeNumber(b?.id)

const byDateDesc = (field) => (a, b) =>
    safeDate(b?.[field]) - safeDate(a?.[field])
    || safeNumber(b?.id) - safeNumber(a?.id)

const sortOrders = (items) => {
    const list = (items || []).slice()

    const sortMap = {
        idAsc: byNumberAsc('id'),
        idDesc: byNumberDesc('id'),

        numberAsc: byStringAsc('number'),
        numberDesc: byStringDesc('number'),

        createdAsc: byDateAsc('created_at'),
        createdDesc: byDateDesc('created_at'),
        date_asc: byDateAsc('created_at'),
        date_desc: byDateDesc('created_at'),

        totalAsc: byNumberAsc('total'),
        totalDesc: byNumberDesc('total'),
        total_asc: byNumberAsc('total'),
        total_desc: byNumberDesc('total'),

        paidAtAsc: byDateAsc('paid_at'),
        paidAtDesc: byDateDesc('paid_at'),
        paid_asc: byDateAsc('paid_at'),
        paid_desc: byDateDesc('paid_at'),

        buyerAsc: (a, b) =>
            normalize(getBuyerName(a)).localeCompare(normalize(getBuyerName(b)), props.currentLocale)
            || safeNumber(a?.id) - safeNumber(b?.id),

        buyerDesc: (a, b) =>
            normalize(getBuyerName(b)).localeCompare(normalize(getBuyerName(a)), props.currentLocale)
            || safeNumber(b?.id) - safeNumber(a?.id),

        statusAsc: byStringAsc('status'),
        statusDesc: byStringDesc('status'),

        paymentStatusAsc: byStringAsc('payment_status'),
        paymentStatusDesc: byStringDesc('payment_status'),

        paidFirst: (a, b) =>
            Number(Boolean(b?.is_paid)) - Number(Boolean(a?.is_paid))
            || safeNumber(b?.id) - safeNumber(a?.id),

        paidLast: (a, b) =>
            Number(Boolean(a?.is_paid)) - Number(Boolean(b?.is_paid))
            || safeNumber(b?.id) - safeNumber(a?.id),
    }

    return sortMap[sortParam.value]
        ? list.sort(sortMap[sortParam.value])
        : list
}

const filteredOrders = computed(() => {
    let filtered = localOrders.value || []
    const query = normalize(searchQuery.value)

    if (!query) {
        return sortOrders(filtered)
    }

    filtered = filtered.filter((order) => {
        const values = [
            order?.id,
            order?.number,
            order?.status,
            order?.payment_status,

            getBuyerName(order),
            getBuyerEmail(order),
            order?.buyer_phone,

            getCourseTitle(order),
            getScheduleTitle(order),

            order?.payment_method,
            order?.payment_provider,
            order?.payment_reference,
            order?.external_id,
            order?.client_ip,

            order?.total,
            order?.currency,
        ]

        return values.some(value => normalize(value).includes(query))
    })

    return sortOrders(filtered)
})

const paginatedOrders = computed(() => {
    const per = Number(itemsPerPage.value || 10)
    const start = (currentPage.value - 1) * per

    return filteredOrders.value.slice(start, start + per)
})

const displayedOrders = computed(() => {
    return props.useServerProcessing
        ? ordersList.value
        : paginatedOrders.value
})

watch([itemsPerPage, searchQuery], () => {
    currentPage.value = 1
})

const confirmDelete = (order) => {
    orderToDelete.value = order
    showConfirmDeleteModal.value = true
}

const closeModal = () => {
    showConfirmDeleteModal.value = false
    orderToDelete.value = null
}

const deleteOrder = () => {
    if (!orderToDelete.value?.id) return

    const idToDelete = orderToDelete.value.id
    const numberToDelete = orderToDelete.value.number || `ID: ${idToDelete}`

    router.delete(route('admin.schoolOrders.destroy', {
        schoolOrder: idToDelete,
    }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            toast.success(`Заказ "${numberToDelete}" удалён.`)
        },
        onError: (errors) => {
            const firstKey = Object.keys(errors || {})[0]
            const errorMsg = errors?.general || errors?.[firstKey] || 'Ошибка при удалении заказа.'

            toast.error(`${errorMsg} Заказ: ${numberToDelete}`)
        },
        onFinish: () => closeModal(),
    })
}

const cloneOrder = (order) => {
    if (!order?.id) return

    router.post(route('admin.actions.schoolOrders.clone', {
        schoolOrder: order.id,
    }), {}, {
        preserveScroll: true,
        onSuccess: () => toast.success('Заказ успешно клонирован.'),
        onError: () => toast.error('Ошибка при клонировании заказа.'),
    })
}
</script>

<template>
    <AdminLayout :title="t('orders')">
        <template #header>
            <TitlePage>
                {{ t('orders') }}
            </TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-end sm:items-center mb-3 gap-3">
                    <ProcessingModeSwitcher
                        setting-key="adminSchoolOrdersProcessingMode"
                        :mode="adminSchoolOrdersProcessingMode"
                        :use-server-processing="useServerProcessing"
                        :total="ordersCount"
                    />
                </div>

                <SearchInput
                    v-if="ordersCount && !useServerProcessing"
                    v-model="searchQuery"
                    :placeholder="t('search')"
                />

                <ServerSearchInput
                    v-if="ordersCount && useServerProcessing"
                    v-model="searchQuery"
                />

                <div
                    v-if="ordersCount"
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
                        update-route="admin.settings.updateAdminCountSchoolOrders"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="val => sortParam = val"
                    />
                </div>

                <div
                    v-if="ordersCount"
                    class="flex justify-between items-center flex-col md:flex-row my-3"
                >
                    <CountTable>{{ ordersCount }}</CountTable>

                    <ToggleViewButton v-model:viewMode="viewMode" />
                </div>

                <div
                    v-if="ordersCount"
                    class="flex justify-center items-center flex-col md:flex-row mb-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredOrders.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="orders"
                    />
                </div>

                <OrderTable
                    v-if="viewMode === 'table'"
                    :orders="displayedOrders"
                    @clone="cloneOrder"
                    @delete="confirmDelete"
                />

                <OrderCardGrid
                    v-else
                    :orders="displayedOrders"
                    @clone="cloneOrder"
                    @delete="confirmDelete"
                />

                <div
                    v-if="ordersCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        v-if="!useServerProcessing"
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredOrders.length"
                        @update:currentPage="currentPage = $event"
                    />

                    <AdminServerPagination
                        v-else
                        :pagination="orders"
                    />
                </div>
            </div>
        </div>

        <DangerModal
            :show="showConfirmDeleteModal"
            :onCancel="closeModal"
            :onConfirm="deleteOrder"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
            @close="closeModal"
        />
    </AdminLayout>
</template>
