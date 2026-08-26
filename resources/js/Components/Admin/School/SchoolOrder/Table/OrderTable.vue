<script setup>
import { defineEmits, defineProps } from 'vue'
import { useI18n } from 'vue-i18n'

import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'
import CloneIconButton from '@/Components/Admin/UI/Buttons/CloneIconButton.vue'

const { t } = useI18n()

defineProps({
    orders: {
        type: Array,
        default: () => [],
    },
})

const emit = defineEmits([
    'delete',
    'clone',
])

/* ==========================================================
 * STATUS LABELS
 * ========================================================== */

const paymentStatusLabelKeyMap = {
    pending: 'statusPaidPending',
    paid: 'statusPaid',
    succeeded: 'statusPaid',
    failed: 'statusPaidError',
    partial: 'statusPaidPartial',
    partially_refunded: 'statusPaidPartial',
    refunded: 'statusPaidRefunded',
}

const orderStatusLabelKeyMap = {
    new: 'statusOrderNew',
    processing: 'statusOrderProcessing',
    completed: 'statusOrderCompleted',
    cancelled: 'statusOrderCancelled',
    refunded: 'statusPaidRefunded',
}

const getPaymentStatusLabel = (status) => {
    if (!status) return '—'

    const key =
        paymentStatusLabelKeyMap[status]

    return key
        ? t(key)
        : status
}

const getOrderStatusLabel = (status) => {
    if (!status) return '—'

    const key =
        orderStatusLabelKeyMap[status]

    return key
        ? t(key)
        : status
}

/* ==========================================================
 * STATUS CLASSES
 * ========================================================== */

const getPaymentStatusClasses = (status) => {
    switch (status) {
        case 'paid':
        case 'succeeded':
            return 'border-emerald-400 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-100'

        case 'pending':
            return 'border-amber-400 bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-100'

        case 'failed':
            return 'border-rose-400 bg-rose-100 dark:bg-rose-900/40 text-rose-800 dark:text-rose-100'

        case 'partial':
        case 'partially_refunded':
            return 'border-violet-400 bg-violet-100 dark:bg-violet-900/40 text-violet-800 dark:text-violet-100'

        case 'refunded':
            return 'border-slate-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-100'

        default:
            return 'border-blue-400 bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-100'
    }
}

const getOrderStatusClasses = (status) => {
    switch (status) {
        case 'completed':
            return 'border-emerald-400 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-100'

        case 'processing':
            return 'border-sky-400 bg-sky-100 dark:bg-sky-900/40 text-sky-800 dark:text-sky-100'

        case 'new':
            return 'border-amber-400 bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-100'

        case 'cancelled':
        case 'refunded':
            return 'border-rose-400 bg-rose-100 dark:bg-rose-900/40 text-rose-800 dark:text-rose-100'

        default:
            return 'border-slate-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-100'
    }
}

/* ==========================================================
 * FORMATTERS
 * ========================================================== */

const formatDateTime = (value) => {
    if (!value) return '—'

    const date = new Date(value)

    if (Number.isNaN(date.getTime())) {
        return String(value)
    }

    return date.toLocaleString('ru-RU')
}

const formatMoney = (
    value,
    currency = 'USD'
) => {
    if (
        value === null
        || typeof value === 'undefined'
    ) {
        return '—'
    }

    const number = Number(value)

    if (Number.isNaN(number)) {
        return String(value)
    }

    return `${number.toFixed(2)} ${currency}`
}

/* ==========================================================
 * RESOURCE HELPERS
 * ========================================================== */

const getBuyerName = (order) => {
    return order?.buyer_name
        || order?.user?.name
        || '—'
}

const getBuyerEmail = (order) => {
    return order?.buyer_email
        || order?.user?.email
        || ''
}

const getBuyerId = (order) => {
    return order?.user?.id
        ?? order?.user_id
        ?? null
}

/**
 * Новый SchoolOrderSharedResource:
 *
 * course.translation.title
 */
const getCourseTitle = (order) => {
    return order?.course?.translation?.title
        || ''
}

/**
 * Новый SchoolOrderSharedResource:
 *
 * schedule.translation.title
 */
const getScheduleTitle = (order) => {
    return order?.schedule?.translation?.title
        || ''
}
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-200 dark:border-slate-600 relative"
    >
        <div class="overflow-x-auto">
            <table
                v-if="orders.length"
                class="table-auto w-full text-slate-700 dark:text-slate-100"
            >
                <thead
                    class="text-xs uppercase bg-slate-200 dark:bg-cyan-900
                           border border-solid border-gray-300 dark:border-gray-700"
                >
                <tr>
                    <th class="px-2 py-3 w-px">
                        <div class="font-medium text-center">
                            {{ t('id') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-left">
                            {{ t('numberOrder') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-left">
                            {{ t('buyer') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-left">
                            {{ t('course') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-left">
                            {{ t('amount') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-left">
                            {{ t('date') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap">
                        <div class="font-semibold text-left">
                            {{ t('statuses') }}
                        </div>
                    </th>

                    <th class="px-2 py-3 whitespace-nowrap">
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
                               hover:bg-slate-100 dark:hover:bg-cyan-800"
                >
                    <!-- ID -->
                    <td class="px-2 py-3 whitespace-nowrap">
                        <div class="text-left text-blue-600 dark:text-blue-200">
                            {{ order.id }}
                        </div>
                    </td>

                    <!-- Number -->
                    <td class="px-2 py-3 whitespace-nowrap text-amber-600 dark:text-amber-200">
                        <div class="font-semibold">
                            {{ order.number || `#${order.id}` }}
                        </div>

                        <div
                            v-if="order.external_id"
                            class="text-[10px] text-slate-500 dark:text-slate-300"
                        >
                            {{ order.external_id }}
                        </div>

                        <div class="text-[10px] text-blue-600 dark:text-blue-200">
                            {{ formatDateTime(order.updated_at || order.created_at) }}
                        </div>
                    </td>

                    <!-- Buyer -->
                    <td class="px-2 py-3 whitespace-nowrap text-slate-800 dark:text-slate-100">
                        <div
                            v-if="getBuyerId(order)"
                            class="text-[10px] text-gray-500 dark:text-gray-300"
                        >
                            {{ t('buyer') }} ID: {{ getBuyerId(order) }}
                        </div>

                        <div class="text-left font-medium">
                            {{ getBuyerName(order) }}
                        </div>

                        <div v-if="getBuyerEmail(order)">
                                <span class="text-xs text-gray-500 dark:text-gray-300">
                                    Email:
                                </span>

                            <span class="ml-1">
                                    {{ getBuyerEmail(order) }}
                                </span>
                        </div>

                        <div
                            v-if="order.buyer_phone"
                            class="mt-0.5"
                        >
                                <span class="text-xs text-gray-500 dark:text-gray-300">
                                    {{ t('phone') }}:
                                </span>

                            <span class="ml-1">
                                    {{ order.buyer_phone }}
                                </span>
                        </div>

                        <div
                            v-if="!getBuyerEmail(order) && !order.buyer_phone"
                        >
                            —
                        </div>
                    </td>

                    <!-- Course / Schedule -->
                    <td class="px-2 py-3">
                        <div
                            v-if="getCourseTitle(order)"
                            class="text-[11px] text-amber-700 dark:text-amber-200"
                        >
                            {{ getCourseTitle(order) }}
                        </div>

                        <div
                            v-if="getScheduleTitle(order)"
                            class="text-[11px] text-teal-700 dark:text-teal-200"
                        >
                            {{ getScheduleTitle(order) }}
                        </div>

                        <div
                            v-if="!getCourseTitle(order) && !getScheduleTitle(order)"
                        >
                            —
                        </div>
                    </td>

                    <!-- Amount -->
                    <td class="px-2 py-3 text-rose-600 dark:text-rose-200">
                        <div class="font-semibold">
                            {{ formatMoney(order.total, order.currency || 'USD') }}
                        </div>

                        <div class="text-[10px] text-slate-500 dark:text-slate-300">
                            {{ t('subtotal') }}:

                            <span class="font-bold">
                                    {{ formatMoney(order.subtotal, order.currency || 'USD') }}
                                </span>
                        </div>
                    </td>

                    <!-- Dates -->
                    <td class="px-2 py-3 text-blue-800 dark:text-blue-200">
                        <div>
                            {{ formatDateTime(order.created_at) }}
                        </div>

                        <div
                            v-if="order.paid_at"
                            class="text-[10px] text-emerald-700 dark:text-emerald-300"
                        >
                            {{ t('paidAt') }}:
                            {{ formatDateTime(order.paid_at) }}
                        </div>
                    </td>

                    <!-- Statuses -->
                    <td class="px-2 py-3 whitespace-nowrap">
                        <div class="flex flex-col gap-1">
                                <span
                                    :class="[
                                        'inline-flex w-fit items-center rounded-sm px-2 py-0.5 text-[11px] font-semibold border',
                                        order.is_paid
                                            ? 'border-emerald-400 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-100'
                                            : 'border-amber-400 bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-100',
                                    ]"
                                >
                                    {{ order.is_paid ? t('isPaid') : t('notPaid') }}
                                </span>

                            <span
                                :class="[
                                        'inline-flex w-fit items-center rounded-sm px-2 py-0.5 text-[11px] font-semibold border',
                                        getPaymentStatusClasses(order.payment_status),
                                    ]"
                            >
                                    {{ getPaymentStatusLabel(order.payment_status) }}
                                </span>

                            <span
                                :class="[
                                        'inline-flex w-fit items-center rounded-sm px-2 py-0.5 text-[11px] font-semibold border',
                                        getOrderStatusClasses(order.status),
                                    ]"
                            >
                                    {{ getOrderStatusLabel(order.status) }}
                                </span>
                        </div>
                    </td>

                    <!-- Actions -->
                    <td class="px-2 py-3 whitespace-nowrap">
                        <div class="flex justify-end space-x-2">
                            <IconEdit
                                :href="route('admin.schoolOrders.edit', {
                                        schoolOrder: order.id,
                                    })"
                            />

                            <CloneIconButton
                                :title="t('clone')"
                                @clone="emit('clone', order)"
                            />

                            <DeleteIconButton
                                :title="t('delete')"
                                @delete="emit('delete', order)"
                            />
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>

            <div
                v-else
                class="p-5 text-center text-slate-700 dark:text-slate-100"
            >
                {{ t('noData') }}
            </div>
        </div>
    </div>
</template>
