<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 */
import { defineProps, ref, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { router } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import SearchInput from '@/Components/Admin/Search/SearchInput.vue'
import CountTable from '@/Components/Admin/Count/CountTable.vue'
import ItemsPerPageSelect from '@/Components/Admin/Select/ItemsPerPageSelect.vue'
import SortSelect from '@/Components/Admin/Order/Sort/SortSelect.vue'
import LabelInput from '@/Components/Admin/Setting/Input/LabelInput.vue'
import ToggleViewButton from '@/Components/Admin/Buttons/ToggleViewButton.vue'
import OrderTable from '@/Components/Admin/Order/Table/OrderTable.vue'
import OrderCardGrid from '@/Components/Admin/Order/View/OrderCardGrid.vue'
import Pagination from '@/Components/Admin/Pagination/Pagination.vue'
import DangerModal from '@/Components/Admin/Modal/DangerModal.vue'

// --- Инициализация экземпляр i18n, toast ---
const { t } = useI18n()
const toast = useToast()

/**
 * Входные свойства компонента.
 */
const props = defineProps({
    orders: {
        type: Array,
        default: () => []
    },
    ordersCount: {
        type: Number,
        default: 0
    },
    filters: {
        type: Object,
        default: () => ({})
    },
    adminCountOrders: {
        type: Number,
        default: 20
    },
    adminSortOrders: {
        type: String,
        default: 'createdDesc'
    }
})

/**
 * Вид: таблица или карточки.
 * table | cards
 */
// 1. Загружаем состояние из localStorage
const viewMode = ref(localStorage.getItem('admin_view_mode') || 'table')

// 2. Сохраняем при каждом изменении
watch(viewMode, (val) => {
    localStorage.setItem('admin_view_mode', val)
})

/**
 * Реактивная переменная для хранения текущего количества элементов на странице.
 */
const itemsPerPage = ref(props.adminCountOrders)

/**
 * Наблюдатель за изменением количества элементов на странице.
 */
watch(itemsPerPage, (newVal) => {
    router.put(route('admin.settings.updateAdminCountOrders'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления кол-ва элементов.')
    })
})

/**
 * Реактивная переменная для хранения текущего параметра сортировки.
 */
const sortParam = ref(props.adminSortOrders)

/**
 * Наблюдатель за изменением параметра сортировки.
 */
watch(sortParam, (newVal) => {
    router.put(route('admin.settings.updateAdminSortOrders'), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info('Сортировка успешно изменена'),
        onError: (errors) => toast.error(errors.value || 'Ошибка обновления сортировки.')
    })
})

// --- фильтры из контроллера ---
const search = ref(props.filters.search ?? '')
const status = ref(props.filters.status ?? '')
const paymentStatus = ref(props.filters.payment_status ?? '')
const isPaid = ref(props.filters.is_paid ?? '')

// лёгкий debounce для поиска
let searchTimeout = null

const applyFilters = (replace = true) => {
    const query = {
        search: search.value || undefined,
        status: status.value || undefined,
        payment_status: paymentStatus.value || undefined,
        is_paid: isPaid.value !== '' ? isPaid.value : undefined
    }

    router.get(
        route('admin.orders.index'),
        query,
        {
            preserveState: true,
            replace
        }
    )
}

const onSubmitFilters = () => {
    applyFilters(false)
}

watch(search, () => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => applyFilters(), 400)
})

/**
 * Флаг отображения модального окна подтверждения удаления.
 */
const showConfirmDeleteModal = ref(false)

/**
 * ID для удаления.
 */
const orderToDelete = ref(null)

/**
 * Открывает модальное окно подтверждения удаления с входными переменными.
 */
const confirmDelete = (order) => {
    orderToDelete.value = order
    showConfirmDeleteModal.value = true
}

/**
 * Закрывает модальное окно подтверждения и сбрасывает связанные переменные.
 */
const closeModal = () => {
    showConfirmDeleteModal.value = false
    orderToDelete.value = null
}

/**
 * Отправляет запрос на удаление.
 */
const deleteOrder = () => {
    if (!orderToDelete.value?.id) return

    const idToDelete = orderToDelete.value.id
    const numberToDelete = orderToDelete.value.number || `ID: ${idToDelete}`

    router.delete(route('admin.orders.destroy', { order: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            closeModal()
            toast.success(`Заказ "${numberToDelete}" удален.`)
        },
        onError: (errors) => {
            closeModal()
            const errorMsg = errors.general || errors[Object.keys(errors)[0]] || 'Произошла ошибка при удалении.'
            toast.error(`${errorMsg} (Заказ: ${numberToDelete})`)
            console.error('Ошибка удаления:', errors)
        },
        onFinish: () => {
            orderToDelete.value = null
        }
    })
}

/**
 * Отправляет запрос для клонирования.
 */
const cloneOrder = (order) => {
    if (!order?.id) return

    if (!confirm(`Клонировать заказ №${order.number || order.id}?`)) return

    router.post(
        route('admin.actions.orders.clone', order.id),
        {},
        {
            preserveScroll: true
        }
    )
}

/**
 * Текущая страница пагинации.
 */
const currentPage = ref(1)

/**
 * Сортирует массив на основе текущего параметра сортировки.
 */
const sortOrders = (orders) => {
    const list = orders.slice()

    const toNumber = (value) => {
        const n = Number(value)
        return Number.isNaN(n) ? 0 : n
    }

    const toString = (value) => {
        if (value === null || value === undefined) return ''
        return String(value).toLowerCase()
    }

    const getBuyerName = (order) => {
        return order.buyer_name || order.user?.name || ''
    }

    switch (sortParam.value) {
        // ID
        case 'idAsc':
            return list.sort((a, b) => a.id - b.id)
        case 'idDesc':
            return list.sort((a, b) => b.id - a.id)

        // Номер заказа
        case 'numberAsc':
            return list.sort((a, b) => toString(a.number) > toString(b.number) ? 1 : -1)
        case 'numberDesc':
            return list.sort((a, b) => toString(a.number) < toString(b.number) ? 1 : -1)

        // Дата создания
        case 'createdAsc':
            return list.sort(
                (a, b) => new Date(a.created_at) - new Date(b.created_at)
            )
        case 'createdDesc':
            return list.sort(
                (a, b) => new Date(b.created_at) - new Date(a.created_at)
            )

        // Итоговая сумма
        case 'totalAsc':
            return list.sort(
                (a, b) => toNumber(a.total) - toNumber(b.total)
            )
        case 'totalDesc':
            return list.sort(
                (a, b) => toNumber(b.total) - toNumber(a.total)
            )

        // Покупатель (ФИО)
        case 'buyerAsc':
            return list.sort((a, b) => {
                const A = toString(getBuyerName(a))
                const B = toString(getBuyerName(b))
                if (A < B) return -1
                if (A > B) return 1
                return 0
            })
        case 'buyerDesc':
            return list.sort((a, b) => {
                const A = toString(getBuyerName(a))
                const B = toString(getBuyerName(b))
                if (A > B) return -1
                if (A < B) return 1
                return 0
            })

        // Статус заказа
        case 'statusAsc':
            return list.sort((a, b) => {
                const A = toString(a.status)
                const B = toString(b.status)
                if (A < B) return -1
                if (A > B) return 1
                return 0
            })
        case 'statusDesc':
            return list.sort((a, b) => {
                const A = toString(a.status)
                const B = toString(b.status)
                if (A > B) return -1
                if (A < B) return 1
                return 0
            })

        // Статус оплаты
        case 'paymentStatusAsc':
            return list.sort((a, b) => {
                const A = toString(a.payment_status)
                const B = toString(b.payment_status)
                if (A < B) return -1
                if (A > B) return 1
                return 0
            })
        case 'paymentStatusDesc':
            return list.sort((a, b) => {
                const A = toString(a.payment_status)
                const B = toString(b.payment_status)
                if (A > B) return -1
                if (A < B) return 1
                return 0
            })

        // Оплачен? — сначала оплаченные, потом нет
        case 'paidFirst':
            return list.sort((a, b) => {
                const A = a.is_paid ? 0 : 1
                const B = b.is_paid ? 0 : 1
                return A - B
            })

        // Сначала не оплаченные
        case 'paidLast':
            return list.sort((a, b) => {
                const A = a.is_paid ? 1 : 0
                const B = b.is_paid ? 1 : 0
                return A - B
            })

        default:
            // дефолт — как пришло с бэкенда (created_at desc)
            return list
    }
}

/**
 * Вычисляемое свойство, отсортированный список поиска.
 */
const filteredOrders = computed(() => {
    // здесь можно добавить доп. фронтовые фильтры при желании
    return sortOrders(props.orders)
})

/**
 * Вычисляемое свойство пагинации, возвращающее для текущей страницы.
 */
const paginatedOrders = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value
    return filteredOrders.value.slice(start, start + itemsPerPage.value)
})

/**
 * Вычисляемое свойство, возвращающее общее количество страниц пагинации.
 */
const totalPages = computed(() => Math.ceil(filteredOrders.value.length / itemsPerPage.value))

</script>

<template>
    <AdminLayout :title="t('orders')">
        <template #header>
            <TitlePage>{{ t('orders') }}</TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div class="p-4 bg-slate-50 dark:bg-slate-700 bg-opacity-95 dark:bg-opacity-95
                        border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md
                        shadow-gray-500 dark:shadow-slate-400">

                <!-- Поисковая строка -->
                <SearchInput
                    v-if="ordersCount"
                    v-model="search"
                    placeholder="Номер заказа, имя, email или телефон"
                    @enter="onSubmitFilters"
                />

                <!-- Фильтры (статус, оплата, is_paid) -->
                <div class="mt-1 grid gap-4 md:grid-cols-4">
                    <!-- Статус заказа -->
                    <div class="flex flex-col">
                        <LabelInput class="mr-1">{{ t('statusOrder') }}</LabelInput>
                        <select
                            v-model="status"
                            class="w-auto pl-3 pr-7 py-0.5 form-select bg-white dark:bg-gray-200
                                   text-gray-600 dark:text-gray-900 rounded-sm shadow-sm
                                   border border-slate-400 dark:border-slate-600"
                            @change="applyFilters(false)"
                        >
                            <option value="">{{t('all')}}</option>
                            <option value="new">{{t('statusOrderNew')}}</option>
                            <option value="processing">{{t('statusOrderProcessing')}}</option>
                            <option value="completed">{{t('statusOrderCompleted')}}</option>
                            <option value="cancelled">{{t('statusOrderCancelled')}}</option>
                            <option value="refunded">{{t('statusPaidRefunded')}}</option>
                        </select>
                    </div>

                    <!-- Статус оплаты -->
                    <div class="flex flex-col">
                        <LabelInput class="mr-1">{{ t('statusPayment') }}</LabelInput>
                        <select
                            v-model="paymentStatus"
                            class="w-auto pl-3 pr-7 py-0.5 form-select bg-white dark:bg-gray-200
                                   text-gray-600 dark:text-gray-900 rounded-sm shadow-sm
                                   border border-slate-400 dark:border-slate-600"
                            @change="applyFilters(false)"
                        >
                            <option value="">{{t('all')}}</option>
                            <option value="pending">{{t('statusPaidPending')}}</option>
                            <option value="paid">{{t('statusPaid')}}</option>
                            <option value="failed">{{t('statusPaidError')}}</option>
                            <option value="partial">{{t('statusPaidPartial')}}</option>
                            <option value="refunded">{{t('statusPaidRefunded')}}</option>
                        </select>
                    </div>

                    <!-- is_paid -->
                    <div class="flex flex-col">
                        <LabelInput class="mr-1">{{ t('statusPaid') }}</LabelInput>
                        <select
                            v-model="isPaid"
                            class="w-auto pl-3 pr-7 py-0.5 form-select bg-white dark:bg-gray-200
                                   text-gray-600 dark:text-gray-900 rounded-sm shadow-sm
                                   border border-slate-400 dark:border-slate-600"
                            @change="applyFilters(false)"
                        >
                            <option value="">{{t('all')}}</option>
                            <option value="1">{{t('true')}}</option>
                            <option value="0">{{t('false')}}</option>
                        </select>
                    </div>

                    <!-- Переключатель вида -->
                    <div class="flex justify-end items-end pr-1">
                        <ToggleViewButton v-model:viewMode="viewMode" />
                    </div>
                </div>

                <!-- Количество -->
                <CountTable v-if="ordersCount">
                    {{ ordersCount }}
                </CountTable>

                <!-- Таблица / карточки -->
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

                <!-- Пагинация / сортировка -->
                <div v-if="ordersCount"
                     class="flex justify-between items-center flex-col md:flex-row my-1">

                    <ItemsPerPageSelect
                        :items-per-page="itemsPerPage"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <Pagination
                        :current-page="currentPage"
                        :items-per-page="itemsPerPage"
                        :total-items="filteredOrders.length"
                        @update:currentPage="currentPage = $event"
                        @update:itemsPerPage="itemsPerPage = $event"
                    />

                    <SortSelect
                        :sortParam="sortParam"
                        @update:sortParam="val => sortParam = val"
                    />
                </div>
            </div>
        </div>

        <!-- Модалка удаления -->
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
