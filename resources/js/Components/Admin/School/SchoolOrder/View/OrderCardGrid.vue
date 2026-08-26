<script setup>
import { defineEmits, defineProps } from 'vue'
import { useI18n } from 'vue-i18n'

import CloneIconButton from '@/Components/Admin/UI/Buttons/CloneIconButton.vue'
import IconEdit from '@/Components/Admin/UI/Buttons/IconEdit.vue'
import DeleteIconButton from '@/Components/Admin/UI/Buttons/DeleteIconButton.vue'

const { t } = useI18n()

defineProps({
    orders: {
        type: Array,
        default: () => [],
    },
})

const emit = defineEmits([
    'clone',
    'delete',
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

const getCourseTitle = (order) => {
    return order?.course?.translation?.title
        || ''
}

const getScheduleTitle = (order) => {
    return order?.schedule?.translation?.title
        || ''
}
</script>

<template>
    <div
        class="bg-white dark:bg-slate-700 shadow-lg rounded-sm
               border border-slate-400 dark:border-slate-500 relative"
    >
        <div
            v-if="orders.length"
            class="p-3"
        >
            <div class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
                <article
                    v-for="order in orders"
                    :key="order.id"
                    class="relative flex flex-col h-full rounded-md
                           border border-slate-400 dark:border-slate-500
                           bg-slate-50/70 dark:bg-slate-800/80 shadow-sm
                           hover:shadow-md transition-shadow duration-150"
                >
                    <!-- Header -->
                    <header
                        class="flex items-center justify-between px-2 py-1
                               border-b border-dashed border-slate-400
                               dark:border-slate-500"
                    >
                        <div class="w-full flex flex-row items-center justify-between gap-2">
                            <div
                                class="text-[10px] font-semibold px-1.5 py-0.5
                                       rounded-sm border border-gray-400
                                       bg-slate-200 dark:bg-slate-700
                                       text-slate-800 dark:text-blue-100"
                            >
                                ID: {{ order.id }}
                            </div>

                            <span
                                :class="[
                                    'inline-flex items-center px-2 py-0.5 rounded-sm border text-[11px] font-semibold',
                                    getPaymentStatusClasses(order.payment_status),
                                ]"
                                :title="t('statusPayment')"
                            >
                                {{ getPaymentStatusLabel(order.payment_status) }}
                            </span>
                        </div>
                    </header>

                    <!-- Body -->
                    <div
                        class="flex flex-col flex-1 px-3 py-2 space-y-2
                               text-sm text-slate-700 dark:text-slate-100"
                    >
                        <!-- Number -->
                        <div class="text-center">
                            <div class="text-xs font-semibold text-amber-600 dark:text-amber-200">
                                {{ order.number || `#${order.id}` }}
                            </div>

                            <div
                                v-if="order.external_id"
                                class="text-[10px] text-slate-500 dark:text-slate-300"
                            >
                                {{ order.external_id }}
                            </div>

                            <div
                                class="mt-0.5 font-semibold text-[10px]
                                       text-blue-600 dark:text-blue-200"
                            >
                                {{ formatDateTime(order.updated_at || order.created_at) }}
                            </div>
                        </div>

                        <!-- Buyer -->
                        <div class="text-center space-y-0.5">
                            <div class="text-[12px] font-medium text-slate-900 dark:text-slate-50">
                                {{ getBuyerName(order) }}

                                <span
                                    v-if="getBuyerId(order)"
                                    class="text-slate-500 dark:text-slate-400"
                                >
                                    [ID: {{ getBuyerId(order) }}]
                                </span>
                            </div>

                            <div
                                v-if="order.buyer_phone"
                                class="text-[11px] text-slate-500 dark:text-slate-300"
                            >
                                {{ order.buyer_phone }}
                            </div>

                            <div
                                v-if="getBuyerEmail(order)"
                                class="text-[11px] text-slate-500 dark:text-slate-300"
                            >
                                {{ getBuyerEmail(order) }}
                            </div>
                        </div>

                        <!-- Course / Schedule -->
                        <div class="text-center space-y-0.5">
                            <div
                                v-if="getCourseTitle(order)"
                                class="text-[11px] font-semibold
                                       text-amber-700 dark:text-amber-200"
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
                                class="text-[11px] text-slate-400"
                            >
                                —
                            </div>
                        </div>

                        <!-- Amount -->
                        <div
                            class="flex justify-center text-xs font-semibold
                                   text-rose-600 dark:text-rose-200"
                        >
                            {{ formatMoney(order.total, order.currency || 'USD') }}
                        </div>

                        <!-- Paid date -->
                        <div
                            v-if="order.paid_at"
                            class="text-center text-[10px]
                                   text-emerald-700 dark:text-emerald-300"
                        >
                            {{ t('paidAt') }}:
                            {{ formatDateTime(order.paid_at) }}
                        </div>

                        <!-- Statuses -->
                        <div
                            class="flex flex-wrap justify-center gap-2 mt-2
                                   text-[11px] font-semibold"
                        >
                            <span
                                :class="[
                                    'px-2 py-0.5 rounded-sm border',
                                    getOrderStatusClasses(order.status),
                                ]"
                                :title="t('statusOrder')"
                            >
                                {{ getOrderStatusLabel(order.status) }}
                            </span>

                            <span
                                :class="[
                                    'px-2 py-0.5 rounded-sm border',
                                    order.is_paid
                                        ? 'border-emerald-400 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-100'
                                        : 'border-amber-400 bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-100',
                                ]"
                                :title="t('statusPaid')"
                            >
                                {{ order.is_paid ? t('isPaid') : t('notPaid') }}
                            </span>
                        </div>
                    </div>

                    <!-- Footer -->
                    <footer
                        class="flex items-center justify-center px-3 py-2
                               border-t border-dashed border-slate-400
                               dark:border-slate-500"
                    >
                        <div class="flex items-center space-x-3">
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
                    </footer>
                </article>
            </div>
        </div>

        <div
            v-else
            class="p-5 text-center text-slate-700 dark:text-slate-100"
        >
            {{ t('noData') }}
        </div>
    </div>
</template>
