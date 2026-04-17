<script setup>
import { defineProps, defineEmits } from 'vue'
import { useI18n } from 'vue-i18n'
import IconEdit from '@/Components/Admin/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/Buttons/DeleteIconButton.vue'
import CloneIconButton from '@/Components/Admin/Buttons/CloneIconButton.vue'

const { t } = useI18n()

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
            return t('statusPaidPending')      // "В ожидании"
        case 'paid':
            return t('statusPaid')             // "Оплачен"
        case 'failed':
            return t('statusPaidError')        // "Ошибка"
        case 'partial':
            return t('statusPaidPartial')      // "Частично"
        case 'refunded':
            return t('statusPaidRefunded')     // "Возврат"
        default:
            return status                      // fallback на сырое значение
    }
}

const orderStatusLabel = (status) => {
    if (!status) return ''

    switch (status) {
        case 'new':
            return t('statusOrderNew')         // "Новый"
        case 'processing':
            return t('statusOrderProcessing')  // "В обработке"
        case 'completed':
            return t('statusOrderCompleted')   // "Завершён"
        case 'cancelled':
            return t('statusOrderCancelled')   // "Отменён"
        case 'refunded':
            // в селекте для статус заказа тоже используется t('statusPaidRefunded'),
            // поэтому тут берём тот же ключ, чтобы текст совпадал.
            return t('statusPaidRefunded')     // "Возврат"
        default:
            return status
    }
}

const emits = defineEmits(['delete', 'clone'])

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
    <div class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
                border border-slate-200 dark:border-slate-600 relative">
        <div class="overflow-x-auto">
            <table
                v-if="orders.length > 0"
                class="table-auto w-full text-slate-700 dark:text-slate-100">
                <thead
                    class="text-xs uppercase bg-slate-200 dark:bg-cyan-900
                           border border-solid border-gray-300 dark:border-gray-700">
                <tr>
                    <th class="px-2 py-3 w-px">
                        <div class="font-medium text-center">
                            {{ t('id') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-8 last:pr-8 whitespace-nowrap">
                        <div class="font-semibold text-left">
                            {{ t('numberOrder') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-8 last:pr-8 whitespace-nowrap">
                        <div class="font-semibold text-left">
                            {{ t('buyer') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-8 last:pr-8 whitespace-nowrap">
                        <div class="font-semibold text-left">
                            {{ t('contacts') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-8 last:pr-8 whitespace-nowrap">
                        <div class="font-semibold text-left">
                            {{ t('amount') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-8 last:pr-8 whitespace-nowrap">
                        <div class="font-semibold text-left">
                            {{ t('statuses') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-8 last:pr-8 whitespace-nowrap">
                        <div class="font-semibold text-left">
                            {{ t('date') }}
                        </div>
                    </th>
                    <th class="px-2 py-3 first:pl-8 last:pr-8 whitespace-nowrap">
                        <div class="font-semibold text-end">
                            {{ t('actions') }}
                        </div>
                    </th>
                </tr>
                </thead>

                <tbody>
                <tr
                    v-for="order in orders"
                    :key="order.id"
                    class="text-xs font-semibold border-b-2
                           hover:bg-slate-100 dark:hover:bg-cyan-800">
                    <td class="px-2 py-3 whitespace-nowrap">
                        <div class="text-left text-blue-600 dark:text-blue-200">
                            {{ order.id }}
                        </div>
                    </td>
                    <td class="px-2 py-3 first:pl-8 last:pr-8 whitespace-nowrap
                               text-amber-600 dark:text-amber-200">
                        {{ order.number || `#${order.id}` }}
                    </td>
                    <td class="px-2 py-3 first:pl-8 last:pr-8 whitespace-nowrap
                               text-slate-800 dark:text-slate-100">
                        <div class="text-left font-medium">
                            {{ order.buyer_name || order.user?.name || '—' }}
                        </div>
                        <div
                            v-if="order.user"
                            class="text-xs text-gray-500 dark:text-gray-300">
                            {{ t('buyer') }} ID: {{ order.user.id }}
                        </div>
                    </td>
                    <td class="px-2 py-3 first:pl-8 last:pr-8 whitespace-nowrap
                               text-slate-800 dark:text-slate-100">
                        <div v-if="order.buyer_email">
                            <span class="text-xs text-gray-500 dark:text-gray-300">
                                Email:
                            </span>
                            <span class="ml-1">
                                {{ order.buyer_email }}
                            </span>
                        </div>
                        <div v-if="order.buyer_phone" class="mt-0.5">
                            <span class="text-xs text-gray-500 dark:text-gray-300">
                                {{ t('phone') }}:
                            </span>
                            <span class="ml-1">
                                {{ order.buyer_phone }}
                            </span>
                        </div>
                    </td>
                    <td class="px-2 py-3 first:pl-8 last:pr-8 whitespace-nowrap
                               text-rose-600 dark:text-rose-200">
                        {{ formatMoney(order.total, order.currency || 'USD') }}
                    </td>
                    <td class="px-2 py-3 first:pl-8 last:pr-8 whitespace-nowrap">
                        <div class="flex flex-col gap-1">
                            <span
                                class="inline-flex w-fit items-center rounded-sm
                                       px-2 py-0 text-xs font-medium border-2 border-gray-400"
                                :class="order.is_paid
                        ? 'bg-teal-200 text-teal-800 dark:bg-teal-800 dark:text-teal-200'
                        : 'bg-orange-100 text-orange-800 dark:bg-orange-800 dark:text-orange-200'"
                            >
                                {{ order.is_paid ? t('isPaid') : t('notPaid') }}
                            </span>
                            <span
                                v-if="order.payment_status"
                                class="inline-flex w-fit items-center rounded-sm
           px-2 py-0 text-xs font-medium border-2 border-gray-400"
                                :class="{
        'bg-green-100 text-green-800 dark:bg-green-600/40 dark:text-green-200':
            order.payment_status === 'paid',
        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-200':
            order.payment_status === 'pending',
        'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200':
            order.payment_status === 'failed' || order.payment_status === 'refunded',
        'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200':
            order.payment_status === 'partial',
    }"
                            >
    {{ paymentStatusLabel(order.payment_status) }}
</span>

                            <span
                                v-if="order.status"
                                class="inline-flex w-fit items-center rounded-sm
                                       px-2 py-0 text-xs font-medium
                                       bg-white text-slate-600
                                       dark:bg-slate-800 dark:text-slate-200
                                       border-2 border-gray-400">
                                    {{ orderStatusLabel(order.status) }}
                            </span>
                        </div>
                    </td>
                    <td class="px-2 py-3 first:pl-8 last:pr-8
                               text-blue-800 dark:text-blue-200">
                        {{ formatDateTime(order.created_at) }}
                    </td>
                    <td class="px-2 py-3 first:pl-8 last:pr-8 whitespace-nowrap">
                        <div class="flex justify-end space-x-2">
                            <IconEdit :href="route('admin.orders.edit', order.id)" />
                            <CloneIconButton @clone="$emit('clone', order)" />
                            <DeleteIconButton @delete="$emit('delete', order)" />
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
            <div v-else class="p-5 text-center text-slate-700 dark:text-slate-100">
                {{ t('noData') }}
            </div>
        </div>
    </div>
</template>
