<script setup>
import { defineProps, defineEmits } from 'vue'
import {useI18n} from 'vue-i18n'
import CloneIconButton from '@/Components/Admin/Buttons/CloneIconButton.vue'
import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'

// --- Инициализация экземпляр i18n ---
const {t} = useI18n();

const props = defineProps({
    orders: {
        type: Array,
        default: () => []
    }
})

const paymentStatusLabel = (status) => {
    if (!status) return ''

    switch (status) {
        case 'pending':
            return t('statusPaidPending')
        case 'paid':
            return t('statusPaid')
        case 'failed':
            return t('statusPaidError')
        case 'partial':
            return t('statusPaidPartial')
        case 'refunded':
            return t('statusPaidRefunded')
        default:
            return status
    }
}

const orderStatusLabel = (status) => {
    if (!status) return ''

    switch (status) {
        case 'new':
            return t('statusOrderNew')
        case 'processing':
            return t('statusOrderProcessing')
        case 'completed':
            return t('statusOrderCompleted')
        case 'cancelled':
            return t('statusOrderCancelled')
        case 'refunded':
            return t('statusPaidRefunded')
        default:
            return status
    }
}

const emits = defineEmits(['clone', 'delete'])

const formatDateTime = (value) => {
    if (!value) return '-'
    try {
        return new Date(value).toLocaleString('ru-RU')
    } catch (e) {
        return value
    }
}

const formatMoney = (value, currency = 'USD') => {
    if (value === null || value === undefined) return '-'
    const num = Number(value)
    if (Number.isNaN(num)) return String(value)
    return `${num.toFixed(2)} ${currency}`
}
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-400 dark:border-slate-500 relative"
    >
        <!-- Нет данных -->
        <div
            v-if="orders.length === 0"
            class="p-5 text-center text-slate-700 dark:text-slate-100"
        >
            {{ t('noData') }}
        </div>

        <!-- GRID -->
        <div v-else class="p-3">
            <div class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
                <article
                    v-for="order in orders"
                    :key="order.id"
                    class="relative flex flex-col h-full rounded-md
                           border border-slate-400 dark:border-slate-500
                           bg-slate-50/70 dark:bg-slate-800/80 shadow-sm
                           hover:shadow-md transition-shadow duration-150"
                >
                    <!-- Верхняя панель: ID + дата + быстрый статус оплаты -->
                    <div
                        class="flex items-center justify-between px-2 py-1
                               border-b border-dashed border-slate-400 dark:border-slate-500"
                    >
                        <div class="flex flex-col">

                            <div class="flex flex-col items-start space-y-1 text-[10px]">
                                <!-- Короткий бейдж "оплачен / нет" -->
                                <span
                                    class="inline-flex items-center rounded-sm border border-gray-400
                                       px-2 py-0.5 font-semibold"
                                    :class="order.is_paid
                                    ? 'bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-200'
                                    : 'bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200'"
                                >
                                {{ order.is_paid ? t('isPaid') : t('notPaid') }}
                            </span>
                            </div>

                            <div
                                class="mt-0.5 text-[10px]
                                       text-slate-500 dark:text-slate-300"
                            >
                                {{ formatDateTime(order.created_at) }}
                            </div>
                        </div>

                        <div
                            class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm
                                       border border-gray-400
                                       bg-slate-200 dark:bg-slate-700
                                       text-slate-800 dark:text-blue-100"
                        >
                            ID: {{ order.id }}
                        </div>
                    </div>

                    <!-- Контент -->
                    <div class="flex flex-col flex-1 px-3 py-2 space-y-2 text-sm
                                text-slate-700 dark:text-slate-100">

                        <!-- Номер заказа -->
                        <div
                            class="text-center text-xs font-semibold
                                   text-sky-700 dark:text-sky-200"
                        >
                            {{ order.number || `#${order.id}` }}
                        </div>

                        <!-- Покупатель -->
                        <div class="flex flex-col items-center">
                            <div class="font-medium text-slate-900 dark:text-slate-50">
                                {{ order.buyer_name || order.user?.name || '—' }}
                            </div>
                            <div
                                v-if="order.user"
                                class="text-[11px] text-gray-500 dark:text-gray-300 mt-0.5"
                            >
                                {{ t('buyer') }} ID: {{ order.user.id }}
                            </div>
                        </div>

                        <!-- Контакты -->
                        <div class="space-y-0.5 text-[11px] text-center">
                            <div v-if="order.buyer_email">
                                <span class="text-gray-400">Email: </span>
                                <span class="text-slate-800 dark:text-slate-100">
                                    {{ order.buyer_email }}
                                </span>
                            </div>
                            <div v-if="order.buyer_phone">
                                <span class="text-gray-400">{{ t('phone') }}: </span>
                                <span class="text-slate-800 dark:text-slate-100">
                                    {{ order.buyer_phone }}
                                </span>
                            </div>
                        </div>

                        <!-- Сумма -->
                        <div
                            class="flex justify-center
                                   text-xs font-semibold
                                   text-rose-600 dark:text-rose-200 mt-1"
                        >
                            {{ formatMoney(order.total, order.currency || 'USD') }}
                        </div>

                        <!-- Статусы заказа -->
                        <div
                            class="flex flex-wrap justify-center gap-2 mt-2
                                   text-[11px] font-semibold"
                        >
                            <!-- Статус заказа -->
                            <span
                                v-if="order.status"
                                class="px-2 py-0.5 rounded-sm
                                       bg-sky-100 dark:bg-sky-900
                                       border border-gray-400
                                       text-sky-700 dark:text-sky-200"
                                :title="orderStatusLabel(order.status)"
                            >
                                {{ orderStatusLabel(order.status) }}
                            </span>

                            <!-- Статус оплаты (дублируем компактно, если нужно) -->
                            <span
                                v-if="order.payment_status"
                                class="px-2 py-0.5 rounded-sm
                                       bg-slate-100 dark:bg-slate-900
                                       border border-gray-400
                                       text-slate-700 dark:text-slate-100"
                            >
                                {{ paymentStatusLabel(order.payment_status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Действия -->
                    <div
                        class="flex items-center justify-center px-3 py-2
                               border-t border-dashed border-slate-400 dark:border-slate-500"
                    >
                        <div class="flex items-center space-x-3">
                            <IconEdit :href="route('admin.orders.edit', order.id)" />
                            <CloneIconButton @clone="$emit('clone', order)" />
                            <DeleteIconButton @delete="$emit('delete', order)" />
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </div>
</template>
