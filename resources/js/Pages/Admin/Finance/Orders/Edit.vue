<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов
 * Редактирование заказа (только безопасные поля)
 */
import { computed, ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import PrimaryButton from '@/Components/Admin/Buttons/PrimaryButton.vue'

import LabelInput from '@/Components/Admin/Input/LabelInput.vue'
import InputText from '@/Components/Admin/Input/InputText.vue'
import InputError from '@/Components/Admin/Input/InputError.vue'
import ActivityCheckbox from '@/Components/Admin/Checkbox/ActivityCheckbox.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    order: {
        type: Object,
        required: true
    },
    users: {
        type: Array,
        default: () => []
    }
})

/** Дата YYYY-MM-DD (для delivery_date) */
const formatDate = (dateStr) => {
    if (!dateStr) return ''
    const date = new Date(dateStr)
    if (isNaN(date.getTime())) return ''
    return date.toISOString().split('T')[0]
}

/** Человекочитаемая дата-время: ДД.MM.ГГГГ ЧЧ:ММ (для paid_at / exported_at) */
const formatDateTimeHuman = (dateTimeStr) => {
    if (!dateTimeStr) return ''
    const d = new Date(dateTimeStr)
    if (isNaN(d.getTime())) {
        // если вдруг там уже строка в нужном формате — просто отдадим как есть
        return dateTimeStr
    }

    const pad = (n) => String(n).padStart(2, '0')

    const day = pad(d.getDate())
    const month = pad(d.getMonth() + 1)
    const year = d.getFullYear()
    const hours = pad(d.getHours())
    const minutes = pad(d.getMinutes())

    return `${day}.${month}.${year} ${hours}:${minutes}`
}

/**
 * Форма (только реально редактируемые поля)
 */
const form = useForm({
    _method: 'PUT',

    // Статус заказа и флаг оплаты
    status: props.order.status ?? 'new',
    is_paid: Boolean(props.order.is_paid),

    // Комментарий менеджера
    manager_comment: props.order.manager_comment ?? '',

    // Интеграции
    external_id: props.order.external_id ?? ''
})

/** Человекочитаемые даты */
const formattedPaidAt = computed(() => formatDateTimeHuman(props.order.paid_at))
const formattedExportedAt = computed(() => formatDateTimeHuman(props.order.exported_at))

// --- Мапы "значение из БД" → "ключ перевода" ---
// Статус заказа
const orderStatusLabelKeyMap = {
    new: 'statusOrderNew',
    processing: 'statusOrderProcessing',
    completed: 'statusOrderCompleted',
    cancelled: 'statusOrderCancelled',
    archived: 'statusArchived'
}

// Статус оплаты (для вывода, не редактируем)
const paymentStatusLabelKeyMap = {
    pending: 'statusPaidPending',
    paid: 'statusPaid',
    failed: 'statusPaidError',
    refunded: 'statusPaidRefunded'
}

// Способ оплаты
const paymentMethodLabelKeyMap = {
    card: 'paymentMethodCard',
    cash: 'paymentMethodCash',
    bank_transfer: 'paymentMethodBankTransfer',
    // сюда же добавишь свои остальные способы
}

// Статус подтверждения
const confirmationStatusLabelKeyMap = {
    pending: 'statusSelectPending',
    confirmed: 'statusSelectConfirmed',
    failed: 'statusSelectFailed',
    manual_review: 'statusSelectManualReview'
}

// --- Хелперы для перевода ---
const getOrderStatusLabel = (status) => {
    if (!status) return '—'
    const key = orderStatusLabelKeyMap[status]
    return key ? t(key) : status
}

const getPaymentStatusLabel = (status) => {
    if (!status) return '—'
    const key = paymentStatusLabelKeyMap[status]
    return key ? t(key) : status
}

const getPaymentMethodLabel = (method) => {
    if (!method) return '—'
    const key = paymentMethodLabelKeyMap[method]
    return key ? t(key) : method
}

const getConfirmationStatusLabel = (confirmationStatus) => {
    if (!confirmationStatus) return '—'
    const key = confirmationStatusLabelKeyMap[confirmationStatus]
    return key ? t(key) : confirmationStatus
}

/** Отправка формы */
const submitForm = () => {
    form.post(route('admin.orders.update', props.order.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Заказ успешно обновлён')
        },
        onError: (errors) => {
            console.error('❌ Ошибка при обновлении заказа:', errors)
            const firstKey = Object.keys(errors || {})[0]
            const firstError = firstKey ? errors[firstKey] : null
            toast.error(firstError || 'Проверьте правильность заполнения полей заказа.')
        }
    })
}

/** Опции для select статуса заказа */
const statusOptions = ref([
    'new',
    'processing',
    'completed',
    'cancelled',
    'archived'
])
</script>

<template>
    <AdminLayout :title="t('editOrder')">
        <template #header>
            <TitlePage>
                {{ t('editOrder') }} - {{order.number}} [ID: {{order.id}}]
            </TitlePage>
        </template>

        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       shadow-lg shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <!-- Верхняя панель: назад -->
                <div class="sm:flex sm:justify-between sm:items-center mb-4">
                    <DefaultButton :href="route('admin.orders.index')">
                        <template #icon>
                            <svg
                                class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                                viewBox="0 0 16 16"
                            >
                                <path
                                    d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c-.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2 .8-6.4z"
                                />
                            </svg>
                        </template>
                        {{ t('back') }}
                    </DefaultButton>
                </div>

                <form
                    @submit.prevent="submitForm"
                    class="w-full"
                >
                    <!-- Флаг оплачено (Editable) -->
                    <div class="flex flex-col justify-center mb-4">
                        <div class="flex items-center gap-2">
                            <ActivityCheckbox v-model="form.is_paid" />
                            <span class="text-sm text-slate-800 dark:text-slate-100">
                                {{ t('isPaid') }}
                            </span>
                        </div>
                        <InputError class="mt-1" :message="form.errors.is_paid" />
                    </div>

                    <!-- Блок 1: Основное (read-only) и статусы (editable) -->
                    <div
                        class="mb-4 grid gap-4
                               md:grid-cols-2 lg:grid-cols-3"
                    >
                        <!-- Номер заказа (Только для чтения) -->
                        <div class="flex flex-col">
                            <LabelInput for="number">
                                {{ t('orderNumber') }} (ID: {{ order.id }})
                            </LabelInput>
                            <div
                                class="mt-1 px-2 py-0.5 rounded-sm
                                       border border-slate-300 dark:border-slate-500
                                       bg-slate-100 dark:bg-slate-600 text-sm
                                       text-slate-800 dark:text-slate-100"
                            >
                                {{ order.number }}
                            </div>
                        </div>

                        <!-- Статус заказа (Editable, с переводами) -->
                        <div class="flex flex-col">
                            <LabelInput for="status">
                                {{ t('statusOrder') }}
                            </LabelInput>
                            <select
                                id="status"
                                v-model="form.status"
                                class="mt-1 py-0.5 border-slate-500 rounded-sm shadow-sm
           font-semibold text-sm dark:bg-cyan-800 dark:text-slate-100
           focus:border-indigo-500 focus:ring-indigo-300"
                            >
                                <option
                                    v-for="s in statusOptions"
                                    :key="s"
                                    :value="s"
                                >
                                    {{ getOrderStatusLabel(s) }}
                                </option>
                            </select>
                            <InputError class="mt-1" :message="form.errors.status" />
                        </div>

                        <!-- Статус оплаты (Editable, с переводами) -->
                        <div class="flex flex-col">
                            <LabelInput for="payment_status">
                                {{ t('paymentStatus') }}
                            </LabelInput>
                            <div
                                class="mt-1 px-2 py-0.5 rounded-sm
                                       border border-slate-300 dark:border-slate-500
                                       bg-slate-100 dark:bg-slate-600 text-sm
                                       text-slate-800 dark:text-slate-100"
                            >
                                {{ getPaymentStatusLabel(order.payment_status) }}
                            </div>
                        </div>

                    </div>

                    <!-- Блок 2: Покупатель и биллинг (Только для чтения) -->
                    <div class="mb-4 border-t border-dashed
                                border-slate-500 dark:border-slate-300 pt-4">
                        <h3 class="flex justify-center text-md mb-3
                                   text-teal-600 dark:text-teal-300">
                            <span class="w-fit px-2 border border-gray-400 dark:border-gray-300">
                                {{ t('buyerData') }}
                            </span>
                        </h3>

                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            <div class="flex flex-col">
                                <LabelInput for="buyer_name">
                                    {{ t('buyerName') }}
                                </LabelInput>
                                <div
                                    class="mt-1 px-2 py-0.5 rounded-sm
                                           border border-slate-300 dark:border-slate-500
                                           bg-slate-100 dark:bg-slate-600 text-sm
                                           text-slate-800 dark:text-slate-100"
                                >
                                    {{ order.buyer_name }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="buyer_email">
                                    {{ t('buyerEmail') }}
                                </LabelInput>
                                <div
                                    class="mt-1 px-2 py-0.5 rounded-sm
                                           border border-slate-300 dark:border-slate-500
                                           bg-slate-100 dark:bg-slate-600 text-sm
                                           text-slate-800 dark:text-slate-100"
                                >
                                    {{ order.buyer_email }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="buyer_phone">
                                    {{ t('buyerPhone') }}
                                </LabelInput>
                                <div
                                    class="mt-1 px-2 py-0.5 rounded-sm
                                           border border-slate-300 dark:border-slate-500
                                           bg-slate-100 dark:bg-slate-600 text-sm
                                           text-slate-800 dark:text-slate-100"
                                >
                                    {{ order.buyer_phone }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="billing_company">
                                    {{ t('billingCompany') }}
                                </LabelInput>
                                <div
                                    class="mt-1 px-2 py-0.5 rounded-sm
                                           border border-slate-300 dark:border-slate-500
                                           bg-slate-100 dark:bg-slate-600 text-sm
                                           text-slate-800 dark:text-slate-100"
                                >
                                    {{ order.billing_company }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="billing_tax_id">
                                    {{ t('billingTaxId') }}
                                </LabelInput>
                                <div
                                    class="mt-1 px-2 py-0.5 rounded-sm
                                           border border-slate-300 dark:border-slate-500
                                           bg-slate-100 dark:bg-slate-600 text-sm
                                           text-slate-800 dark:text-slate-100"
                                >
                                    {{ order.billing_tax_id }}
                                </div>
                            </div>

                            <div class="flex flex-col md:col-span-2 lg:col-span-3">
                                <LabelInput for="billing_address">
                                    {{ t('billingAddress') }}
                                </LabelInput>
                                <div
                                    class="mt-1 px-2 py-0.5 rounded-sm
                                           border border-slate-300 dark:border-slate-500
                                           bg-slate-100 dark:bg-slate-600 text-sm
                                           text-slate-800 dark:text-slate-100"
                                >
                                    {{ order.billing_address }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Блок 3: Доставка (Только для чтения) -->
                    <div class="mb-4 border-t border-dashed
                                border-slate-500 dark:border-slate-300 pt-4">
                        <h3 class="flex justify-center text-md mb-3
                                   text-teal-600 dark:text-teal-300">
                            <span class="w-fit px-2 border border-gray-400 dark:border-gray-300">
                                {{ t('delivery') }}
                            </span>
                        </h3>

                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            <div class="flex flex-col md:col-span-2 lg:col-span-3">
                                <LabelInput for="shipping_address">
                                    {{ t('shippingAddress') }}
                                </LabelInput>
                                <div
                                    class="mt-1 px-2 py-0.5 rounded-sm
                                           border border-slate-300 dark:border-slate-500
                                           bg-slate-100 dark:bg-slate-600 text-sm
                                           text-slate-800 dark:text-slate-100"
                                >
                                    {{ order.shipping_address }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="warehouse">
                                    {{ t('warehouse') }}
                                </LabelInput>
                                <div
                                    class="mt-1 px-2 py-0.5 rounded-sm
                                           border border-slate-300 dark:border-slate-500
                                           bg-slate-100 dark:bg-slate-600 text-sm
                                           text-slate-800 dark:text-slate-100"
                                >
                                    {{ order.warehouse }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="delivery_method_id">
                                    {{ t('deliveryMethodId') }}
                                </LabelInput>
                                <div
                                    class="mt-1 px-2 py-0.5 rounded-sm
                                           border border-slate-300 dark:border-slate-500
                                           bg-slate-100 dark:bg-slate-600 text-sm
                                           text-slate-800 dark:text-slate-100"
                                >
                                    {{ order.delivery_method_id }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="delivery_cost">
                                    {{ t('deliveryCost') }}
                                </LabelInput>
                                <div
                                    class="mt-1 px-2 py-0.5 rounded-sm
                                           border border-slate-300 dark:border-slate-500
                                           bg-slate-100 dark:bg-slate-600 text-sm
                                           text-slate-800 dark:text-slate-100"
                                >
                                    {{ order.delivery_cost }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="delivery_date">
                                    {{ t('deliveryDate') }}
                                </LabelInput>
                                <div
                                    class="mt-1 px-2 py-0.5 rounded-sm
                                           border border-slate-300 dark:border-slate-500
                                           bg-slate-100 dark:bg-slate-600 text-sm
                                           text-slate-800 dark:text-slate-100"
                                >
                                    {{ formatDate(order.delivery_date) }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="delivery_interval">
                                    {{ t('deliveryInterval') }}
                                </LabelInput>
                                <div
                                    class="mt-1 px-2 py-0.5 rounded-sm
                                           border border-slate-300 dark:border-slate-500
                                           bg-slate-100 dark:bg-slate-600 text-sm
                                           text-slate-800 dark:text-slate-100"
                                >
                                    {{ order.delivery_interval }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Блок 4: Оплата (Только для чтения, кроме флагов/статусов, которые выше) -->
                    <div class="mb-4 border-t border-dashed
                                border-slate-500 dark:border-slate-300 pt-4">
                        <h3 class="flex justify-center text-md mb-3
                                   text-teal-600 dark:text-teal-300">
                            <span class="w-fit px-2 border border-gray-400 dark:border-gray-300">
                                {{ t('payment') }}
                            </span>
                        </h3>

                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">

                            <div class="flex flex-col">
                                <LabelInput for="payment_provider">
                                    {{ t('paymentProvider') }}
                                </LabelInput>
                                <div
                                    class="mt-1 px-2 py-0.5 rounded-sm
                                           border border-slate-300 dark:border-slate-500
                                           bg-slate-100 dark:bg-slate-600 text-sm
                                           text-slate-800 dark:text-slate-100"
                                >
                                    {{ order.payment_provider }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="payment_method_id">
                                    {{ t('paymentMethodId') }}
                                </LabelInput>
                                <div
                                    class="mt-1 px-2 py-0.5 rounded-sm
                                           border border-slate-300 dark:border-slate-500
                                           bg-slate-100 dark:bg-slate-600 text-sm
                                           text-slate-800 dark:text-slate-100"
                                >
                                    {{ order.payment_method_id }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="payment_method">
                                    {{ t('paymentMethod') }}
                                </LabelInput>
                                <div
                                    class="mt-1 px-2 py-0.5 rounded-sm
               border border-slate-300 dark:border-slate-500
               bg-slate-100 dark:bg-slate-600 text-sm
               text-slate-800 dark:text-slate-100"
                                >
                                    {{ getPaymentMethodLabel(order.payment_method) }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="payment_reference">
                                    {{ t('paymentReference') }}
                                </LabelInput>
                                <div
                                    class="mt-1 px-2 py-0.5 rounded-sm
                                           border border-slate-300 dark:border-slate-500
                                           bg-slate-100 dark:bg-slate-600 text-sm
                                           text-slate-800 dark:text-slate-100"
                                >
                                    {{ order.payment_reference }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="paid_at">
                                    {{ t('paidAt') }}
                                </LabelInput>
                                <div
                                    class="mt-1 px-2 py-0.5 rounded-sm
                                           border border-slate-300 dark:border-slate-500
                                           bg-slate-100 dark:bg-slate-600 text-sm
                                           text-slate-800 dark:text-slate-100"
                                >
                                    {{ formattedPaidAt }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="confirmation_code">
                                    {{ t('confirmationCode') }}
                                </LabelInput>
                                <div
                                    class="mt-1 px-2 py-0.5 rounded-sm
                                           border border-slate-300 dark:border-slate-500
                                           bg-slate-100 dark:bg-slate-600 text-sm
                                           text-slate-800 dark:text-slate-100"
                                >
                                    {{ order.confirmation_code }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="confirmation_status">
                                    {{ t('confirmationStatus') }}
                                </LabelInput>
                                <div
                                    class="mt-1 px-2 py-0.5 rounded-sm
               border border-slate-300 dark:border-slate-500
               bg-slate-100 dark:bg-slate-600 text-sm
               text-slate-800 dark:text-slate-100"
                                >
                                    {{ getConfirmationStatusLabel(order.confirmation_status) }}
                                </div>
                            </div>

                            <div class="flex flex-col md:col-span-2 lg:col-span-3">
                                <LabelInput for="failure_reason">
                                    {{ t('failureReason') }}
                                </LabelInput>
                                <div
                                    class="mt-1 px-2 py-0.5 rounded-sm
                                           border border-slate-300 dark:border-slate-500
                                           bg-slate-100 dark:bg-slate-600 text-sm
                                           text-slate-800 dark:text-slate-100"
                                >
                                    {{ order.failure_reason }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Блок 5: Суммы и валюта (Только для чтения) -->
                    <div class="mb-4 border-t border-dashed
                                border-slate-500 dark:border-slate-300 pt-4">
                        <h3 class="flex justify-center text-md mb-3
                                   text-teal-600 dark:text-teal-300">
                            <span class="w-fit px-2 border border-gray-400 dark:border-gray-300">
                                {{ t('totals') }}
                            </span>
                        </h3>

                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">

                            <div class="flex flex-col">
                                <LabelInput for="subtotal">
                                    {{ t('subtotal') }}
                                </LabelInput>
                                <div
                                    class="mt-1 px-2 py-0.5 rounded-sm
                                           border border-slate-300 dark:border-slate-500
                                           bg-slate-100 dark:bg-slate-600 text-sm
                                           text-slate-800 dark:text-slate-100"
                                >
                                    {{ order.subtotal }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="discount_total">
                                    {{ t('discountTotal') }}
                                </LabelInput>
                                <div
                                    class="mt-1 px-2 py-0.5 rounded-sm
                                           border border-slate-300 dark:border-slate-500
                                           bg-slate-100 dark:bg-slate-600 text-sm
                                           text-slate-800 dark:text-slate-100"
                                >
                                    {{ order.discount_total }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="tax_total">
                                    {{ t('taxTotal') }}
                                </LabelInput>
                                <div
                                    class="mt-1 px-2 py-0.5 rounded-sm
                                           border border-slate-300 dark:border-slate-500
                                           bg-slate-100 dark:bg-slate-600 text-sm
                                           text-slate-800 dark:text-slate-100"
                                >
                                    {{ order.tax_total }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="total">
                                    {{ t('total') }}
                                </LabelInput>
                                <div
                                    class="mt-1 px-2 py-0.5 rounded-sm
                                           border border-slate-300 dark:border-slate-500
                                           bg-slate-100 dark:bg-slate-600 text-sm
                                           text-slate-800 dark:text-slate-100"
                                >
                                    {{ order.total }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="currency">
                                    {{ t('currency') }}
                                </LabelInput>
                                <div
                                    class="mt-1 px-2 py-0.5 rounded-sm
                                           border border-slate-300 dark:border-slate-500
                                           bg-slate-100 dark:bg-slate-600 text-sm
                                           text-slate-800 dark:text-slate-100"
                                >
                                    {{ order.currency }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="total_shop_currency">
                                    {{ t('totalShopCurrency') }}
                                </LabelInput>
                                <div
                                    class="mt-1 px-2 py-0.5 rounded-sm
                                           border border-slate-300 dark:border-slate-500
                                           bg-slate-100 dark:bg-slate-600 text-sm
                                           text-slate-800 dark:text-slate-100"
                                >
                                    {{ order.total_shop_currency }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="delivery_shop_currency">
                                    {{ t('deliveryShopCurrency') }}
                                </LabelInput>
                                <div
                                    class="mt-1 px-2 py-0.5 rounded-sm
                                           border border-slate-300 dark:border-slate-500
                                           bg-slate-100 dark:bg-slate-600 text-sm
                                           text-slate-800 dark:text-slate-100"
                                >
                                    {{ order.delivery_shop_currency }}
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Блок 6: Комментарии (user_comment read-only, manager_comment editable) -->
                    <div class="mb-4 border-t border-dashed
                                border-slate-500 dark:border-slate-300 pt-4">
                        <h3 class="flex justify-center text-md mb-3
                                   text-teal-600 dark:text-teal-300">
                            <span class="w-fit px-2 border border-gray-400 dark:border-gray-300">
                                {{ t('comments') }}
                            </span>
                        </h3>

                        <div class="grid gap-4 md:grid-cols-2">
                            <!-- Комментарий пользователя (только чтение) -->
                            <div class="flex flex-col">
                                <LabelInput for="user_comment">
                                    {{ t('userComment') }}
                                </LabelInput>
                                <div
                                    id="user_comment"
                                    class="mt-1 px-2 py-0.5 rounded-sm
                                           border border-slate-300 dark:border-slate-500
                                           bg-slate-100 dark:bg-slate-600 text-sm
                                           text-slate-800 dark:text-slate-100 whitespace-pre-line"
                                >
                                    {{ order.user_comment }}
                                </div>
                            </div>

                            <!-- Комментарий менеджера (editable) -->
                            <div class="flex flex-col">
                                <LabelInput for="manager_comment">
                                    {{ t('managerComment') }}
                                </LabelInput>
                                <textarea
                                    id="manager_comment"
                                    v-model="form.manager_comment"
                                    rows="8"
                                    class="mt-1 block w-full rounded-sm
                                           border-slate-300 dark:border-slate-500
                                           bg-white dark:bg-slate-800 text-sm
                                           text-slate-800 dark:text-slate-100"
                                />
                                <InputError class="mt-1" :message="form.errors.manager_comment" />
                            </div>
                        </div>
                    </div>

                    <!-- Блок 7: Интеграции и тех.данные -->
                    <div class="mb-4 border-t border-dashed
                                border-slate-500 dark:border-slate-300 pt-4">
                        <h3 class="flex justify-center text-md mb-3
                                   text-teal-600 dark:text-teal-300">
                            <span class="w-fit px-2 border border-gray-400 dark:border-gray-300">
                                {{ t('technicalData') }}
                            </span>
                        </h3>

                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">

                            <!-- Интеграции (editable) -->
                            <div class="flex flex-col">
                                <LabelInput for="external_id">
                                    {{ t('externalId') }}
                                </LabelInput>
                                <InputText
                                    id="external_id"
                                    type="text"
                                    v-model="form.external_id"
                                />
                                <InputError class="mt-1" :message="form.errors.external_id" />
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="exported_at">
                                    {{ t('exportedAt') }}
                                </LabelInput>
                                <div
                                    class="px-2 py-0.5 rounded-sm
                                           border border-slate-300 dark:border-slate-500
                                           bg-slate-100 dark:bg-slate-600 text-sm
                                           text-slate-800 dark:text-slate-100"
                                >
                                    {{ formattedExportedAt }}
                                </div>
                            </div>

                            <!-- client_ip (read-only) -->
                            <div class="flex flex-col">
                                <LabelInput for="client_ip">
                                    {{ t('clientIp') }}
                                </LabelInput>
                                <div
                                    class="px-2 py-0.5 rounded-sm
                                           border border-slate-300 dark:border-slate-500
                                           bg-slate-100 dark:bg-slate-600 text-sm
                                           text-slate-800 dark:text-slate-100"
                                >
                                    {{ order.client_ip }}
                                </div>
                            </div>

                            <!-- user_agent (read-only) -->
                            <div class="flex flex-col md:col-span-2 lg:grid-cols-3">
                                <LabelInput for="user_agent">
                                    {{ t('userAgent') }}
                                </LabelInput>
                                <div
                                    class="mt-1 px-2 py-0.5 rounded-sm
                                           border border-slate-300 dark:border-slate-500
                                           bg-slate-100 dark:bg-slate-600 text-xs
                                           text-slate-800 dark:text-slate-100 break-words"
                                >
                                    {{ order.user_agent }}
                                </div>
                            </div>

                            <!-- public_hash (read-only) -->
                            <div class="flex flex-col md:col-span-2 lg:col-span-3">
                                <LabelInput>
                                    {{ t('publicHash') }}
                                </LabelInput>
                                <div
                                    class="w-full mt-1 px-2 py-0.5 rounded-sm
                                           border border-slate-300 dark:border-slate-500
                                           bg-slate-100 dark:bg-slate-600 text-sm
                                           text-slate-800 dark:text-slate-100 break-all"
                                >
                                    {{ order.public_hash }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Кнопки сохранить/назад -->
                    <div class="flex items-center justify-center mt-4">
                        <DefaultButton :href="route('admin.orders.index')" class="mb-3">
                            <template #icon>
                                <svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                                     viewBox="0 0 16 16">
                                    <path d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c-.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2 .8-6.4z"></path>
                                </svg>
                            </template>
                            {{ t('back') }}
                        </DefaultButton>

                        <PrimaryButton class="ms-4 mb-0"
                                       :class="{ 'opacity-25': form.processing }"
                                       :disabled="form.processing"
                        >
                            <template #icon>
                                <svg
                                    class="w-4 h-4 fill-current text-slate-100"
                                    viewBox="0 0 16 16"
                                >
                                    <path
                                        d="M14.3 2.3L5 11.6 1.7 8.3c-.4-.4-1-.4-1.4 0-.4.4-.4 1 0 1.4l4 4c.2.2.4.3.7.3.3 0 .5-.1.7-.3l10-10c.4-.4.4-1 0-1.4-.4-.4-1-.4-1.4 0z"
                                    />
                                </svg>
                            </template>
                            {{ t('save') }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
