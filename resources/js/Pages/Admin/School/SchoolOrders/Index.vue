<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Список заказов онлайн-школы
 */
import { computed, defineProps, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import SearchInput from '@/Components/Admin/UI/Search/SearchInput.vue'
import CountTable from '@/Components/Admin/UI/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/UI/Select/ItemsPerPageSelect.vue'
import ToggleViewButton from '@/Components/Admin/UI/Buttons/ToggleViewButton.vue'
import Pagination from '@/Components/Admin/UI/Pagination/Pagination.vue'
import DangerModal from '@/Components/Admin/UI/Modal/DangerModal.vue'

import SortSelect from '@/Components/Admin/School/SchoolOrder/Sort/SortSelect.vue'
import OrderTable from '@/Components/Admin/School/SchoolOrder/Table/OrderTable.vue'
import OrderCardGrid from '@/Components/Admin/School/SchoolOrder/View/OrderCardGrid.vue'

// Переводы и уведомления
const { t } = useI18n()
const toast = useToast()

// Входящие данные страницы
const props = defineProps({
    orders: { type: Array, default: () => [] },
    ordersCount: { type: Number, default: 0 },
    filters: { type: Object, default: () => ({}) },
    adminSchoolOrdersPerPage: { type: Number, default: 10 },
    adminSchoolOrdersDefaultSort: { type: String, default: 'date_desc' },
})

// Режим отображения списка заказов
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')

// Сохраняем режим отображения в localStorage
watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode', val)
})

// Количество элементов на странице
const itemsPerPage = ref(props.adminSchoolOrdersPerPage ?? 10)

// Обновление количества элементов в настройках
watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountOrders'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.'),
    })
})

// Параметр сортировки заказов
const sortParam = ref(props.adminSchoolOrdersDefaultSort ?? 'date_desc')

// Обновление сортировки в настройках
watch(sortParam, (newVal) => {
    router.put(route('admin.settings.updateAdminSortOrders'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info('Сортировка успешно изменена'),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления сортировки.'),
    })
})

// Текущая страница пагинации
const currentPage = ref(1)

// Поисковый запрос
const searchQuery = ref(props.filters.search ?? '')

// Модальное окно удаления заказа
const showConfirmDeleteModal = ref(false)

// Заказ для удаления
const orderToDelete = ref(null)

// Открытие модального окна удаления
const confirmDelete = (order) => {
    orderToDelete.value = order
    showConfirmDeleteModal.value = true
}

// Закрытие модального окна удаления
const closeModal = () => {
    showConfirmDeleteModal.value = false
    orderToDelete.value = null
}

// Удаление заказа
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

// Клонирование заказа
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

// Нормализация строки для поиска и сортировки
const normalize = (value) => (value ?? '').toString().trim().toLowerCase()

// Преобразование значения в число
const toNumber = (value) => {
    const number = Number(value)

    return Number.isNaN(number) ? 0 : number
}

// Преобразование даты во временную метку
const toTime = (value) => {
    if (!value) return 0

    const time = new Date(value).getTime()

    return Number.isNaN(time) ? 0 : time
}

// Сортировка заказов
const sortOrders = (orders) => {
    const list = orders.slice()

    switch (sortParam.value) {
        case 'idAsc':
            return list.sort((a, b) => a.id - b.id)

        case 'idDesc':
            return list.sort((a, b) => b.id - a.id)

        case 'date_asc':
        case 'createdAsc':
            return list.sort((a, b) => toTime(a.created_at) - toTime(b.created_at))

        case 'date_desc':
        case 'createdDesc':
            return list.sort((a, b) => toTime(b.created_at) - toTime(a.created_at))

        case 'total_asc':
        case 'totalAsc':
            return list.sort((a, b) => toNumber(a.total) - toNumber(b.total))

        case 'total_desc':
        case 'totalDesc':
            return list.sort((a, b) => toNumber(b.total) - toNumber(a.total))

        case 'paid_asc':
            return list.sort((a, b) => toTime(a.paid_at) - toTime(b.paid_at))

        case 'paid_desc':
            return list.sort((a, b) => toTime(b.paid_at) - toTime(a.paid_at))

        case 'numberAsc':
            return list.sort((a, b) => normalize(a.number).localeCompare(normalize(b.number)))

        case 'numberDesc':
            return list.sort((a, b) => normalize(b.number).localeCompare(normalize(a.number)))

        case 'buyerAsc':
            return list.sort((a, b) =>
                normalize(a.buyer_name || a.user?.name).localeCompare(
                    normalize(b.buyer_name || b.user?.name)
                )
            )

        case 'buyerDesc':
            return list.sort((a, b) =>
                normalize(b.buyer_name || b.user?.name).localeCompare(
                    normalize(a.buyer_name || a.user?.name)
                )
            )

        case 'statusAsc':
            return list.sort((a, b) => normalize(a.status).localeCompare(normalize(b.status)))

        case 'statusDesc':
            return list.sort((a, b) => normalize(b.status).localeCompare(normalize(a.status)))

        case 'paymentStatusAsc':
            return list.sort((a, b) => normalize(a.payment_status).localeCompare(normalize(b.payment_status)))

        case 'paymentStatusDesc':
            return list.sort((a, b) => normalize(b.payment_status).localeCompare(normalize(a.payment_status)))

        case 'paidFirst':
            return list.sort((a, b) => Number(Boolean(b.is_paid)) - Number(Boolean(a.is_paid)))

        case 'paidLast':
            return list.sort((a, b) => Number(Boolean(a.is_paid)) - Number(Boolean(b.is_paid)))

        default:
            return list
    }
}

// Фильтрация и поиск заказов
const filteredOrders = computed(() => {
    let filtered = Array.isArray(props.orders) ? props.orders : []

    if (searchQuery.value) {
        const q = normalize(searchQuery.value)

        filtered = filtered.filter((order) => {
            const values = [
                order.number,
                order.status,
                order.payment_status,

                order.buyer_name || order.user?.name,
                order.buyer_email || order.user?.email,

                order.buyer_phone,

                order.course?.title,
                order.schedule?.title,

                order.payment_reference,
                order.external_id,
            ]

            return values.some(value => normalize(value).includes(q))
        })
    }

    return sortOrders(filtered)
})

// Заказы текущей страницы
const paginatedOrders = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value

    return filteredOrders.value.slice(start, start + itemsPerPage.value)
})

// Общее количество страниц
const totalPages = computed(() => {
    if (!itemsPerPage.value) return 1

    return Math.ceil(filteredOrders.value.length / itemsPerPage.value) || 1
})

// Контроль текущей страницы пагинации
watch([filteredOrders, itemsPerPage], () => {
    if (currentPage.value > totalPages.value) {
        currentPage.value = totalPages.value
    }
})
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
                class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <SearchInput
                    v-if="ordersCount"
                    v-model="searchQuery"
                    :placeholder="t('search')"
                />

                <div
                    v-if="ordersCount"
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
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredOrders.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />
                </div>

                <OrderTable
                    v-if="viewMode === 'table'"
                    :orders="paginatedOrders"
                    @clone="cloneOrder"
                    @delete="confirmDelete"
                />

                <OrderCardGrid
                    v-else
                    :orders="paginatedOrders"
                    @clone="cloneOrder"
                    @delete="confirmDelete"
                />

                <div
                    v-if="ordersCount"
                    class="flex justify-center items-center flex-col md:flex-row mt-3"
                >
                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredOrders.length"
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
            :onConfirm="deleteOrder"
            :cancelText="t('cancel')"
            :confirmText="t('yesDelete')"
        />
    </AdminLayout>
</template>
