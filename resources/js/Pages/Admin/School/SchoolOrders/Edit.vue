<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов
 *
 * Редактирование заказа (только безопасные поля)
 */
import { computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import PrimaryButton from '@/Components/Admin/UI/Buttons/PrimaryButton.vue'
import LabelInput from '@/Components/Admin/UI/Input/LabelInput.vue'
import InputText from '@/Components/Admin/UI/Input/InputText.vue'
import InputError from '@/Components/Admin/UI/Input/InputError.vue'
import ActivityCheckbox from '@/Components/Admin/UI/Checkbox/ActivityCheckbox.vue'
import MetaDescTextarea from '@/Components/Admin/UI/Textarea/MetaDescTextarea.vue'

// Переводы и уведомления
const { t } = useI18n()
const toast = useToast()

// Входящие данные страницы
const props = defineProps({
    order: { type: Object, required: true },
    users: { type: Array, default: () => [] },
    courses: { type: Array, default: () => [] },
    schedules: { type: Array, default: () => [] },
})

// Форматирование даты и времени
const formatDateTimeHuman = (value) => {
    if (!value) return '—'

    const d = new Date(value)

    if (Number.isNaN(d.getTime())) return value

    return d.toLocaleString('ru-RU')
}

// Форматирование суммы заказа
const formatMoney = (value, currency = 'USD') => {
    if (value === null || value === undefined) return '—'

    const num = Number(value)

    if (Number.isNaN(num)) return String(value)

    return `${num.toFixed(2)} ${currency}`
}

// Форма редактирования заказа
const form = useForm({
    _method: 'PUT',

    user_id: props.order.user_id ?? null,
    school_course_id: props.order.school_course_id ?? null,
    school_course_schedule_id: props.order.school_course_schedule_id ?? null,

    number: props.order.number ?? '',

    buyer_name: props.order.buyer_name ?? '',
    buyer_email: props.order.buyer_email ?? '',
    buyer_phone: props.order.buyer_phone ?? '',

    billing_company: props.order.billing_company ?? '',
    billing_tax_id: props.order.billing_tax_id ?? '',
    billing_address: props.order.billing_address ?? '',

    is_paid: Boolean(props.order.is_paid),
    paid_at: props.order.paid_at ?? '',

    payment_method_id: props.order.payment_method_id ?? null,
    payment_method: props.order.payment_method ?? '',
    payment_provider: props.order.payment_provider ?? '',
    payment_reference: props.order.payment_reference ?? '',
    confirmation_code: props.order.confirmation_code ?? '',
    confirmation_status: props.order.confirmation_status ?? '',
    failure_reason: props.order.failure_reason ?? '',

    currency: props.order.currency ?? 'USD',
    subtotal: props.order.subtotal ?? 0,
    discount_total: props.order.discount_total ?? 0,
    tax_total: props.order.tax_total ?? 0,
    total: props.order.total ?? 0,

    status: props.order.status ?? 'new',
    payment_status: props.order.payment_status ?? 'pending',

    items: props.order.items_snapshot ?? props.order.items ?? null,
    meta: props.order.meta ?? null,

    user_comment: props.order.user_comment ?? '',
    manager_comment: props.order.manager_comment ?? '',

    external_id: props.order.external_id ?? '',
    exported_at: props.order.exported_at ?? '',

    client_ip: props.order.client_ip ?? '',
    user_agent: props.order.user_agent ?? '',
    public_hash: props.order.public_hash ?? '',
})

// Заголовок страницы
const pageTitle = computed(() => props.order.number || `ID: ${props.order.id}`)

// Отформатированная дата оплаты
const formattedPaidAt = computed(() => formatDateTimeHuman(props.order.paid_at))

// Отформатированная дата экспорта
const formattedExportedAt = computed(() => formatDateTimeHuman(props.order.exported_at))

// Карта переводов статусов заказа
const orderStatusLabelKeyMap = {
    new: 'statusOrderNew',
    processing: 'statusOrderProcessing',
    completed: 'statusOrderCompleted',
    cancelled: 'statusOrderCancelled',
    refunded: 'statusPaidRefunded',
    archived: 'statusArchived',
}

// Карта переводов статусов оплаты
const paymentStatusLabelKeyMap = {
    pending: 'statusPaidPending',
    paid: 'statusPaid',
    succeeded: 'statusPaid',
    failed: 'statusPaidError',
    partial: 'statusPaidPartial',
    partially_refunded: 'statusPaidPartial',
    refunded: 'statusPaidRefunded',
}

// Список доступных статусов заказа
const orderStatusOptions = [
    'new',
    'processing',
    'completed',
    'cancelled',
    'refunded',
    'archived',
]

// Список доступных статусов оплаты
const paymentStatusOptions = [
    'pending',
    'paid',
    'succeeded',
    'failed',
    'partial',
    'partially_refunded',
    'refunded',
]

// Получение перевода статуса заказа
const getOrderStatusLabel = (status) => {
    if (!status) return '—'

    const key = orderStatusLabelKeyMap[status]

    return key ? t(key) : status
}

// Получение перевода статуса оплаты
const getPaymentStatusLabel = (status) => {
    if (!status) return '—'

    const key = paymentStatusLabelKeyMap[status]

    return key ? t(key) : status
}

// Отправка формы редактирования заказа
const submitForm = () => {
    form.transform((data) => ({
        ...data,

        user_id: data.user_id || null,
        school_course_id: data.school_course_id || null,
        school_course_schedule_id: data.school_course_schedule_id || null,
        payment_method_id: data.payment_method_id || null,

        is_paid: data.is_paid ? 1 : 0,

        subtotal: Number(data.subtotal || 0),
        discount_total: Number(data.discount_total || 0),
        tax_total: Number(data.tax_total || 0),
        total: Number(data.total || 0),
    }))

    form.post(route('admin.schoolOrders.update', {
        schoolOrder: props.order.id,
    }), {
        preserveScroll: true,
        onSuccess: () => toast.success('Заказ успешно обновлён.'),
        onError: (errors) => {
            const firstKey = Object.keys(errors || {})[0]
            toast.error(errors?.[firstKey] || 'Проверьте правильность заполнения полей заказа.')
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('editOrder')">
        <template #header>
            <TitlePage>
                {{ t('editOrder') }} - {{ pageTitle }} [ID: {{ order.id }}]
            </TitlePage>
        </template>

        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       shadow-lg shadow-gray-500 dark:shadow-slate-400"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-4">
                    <DefaultButton :href="route('admin.schoolOrders.index')">
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

                <form @submit.prevent="submitForm" class="w-full">
                    <div class="mb-4 flex items-center gap-2">
                        <ActivityCheckbox v-model="form.is_paid" />
                        <span class="text-sm text-slate-800 dark:text-slate-100">
                            {{ t('isPaid') }}
                        </span>
                        <InputError :message="form.errors.is_paid" />
                    </div>

                    <div class="mb-4 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        <div class="flex flex-col">
                            <LabelInput for="number">{{ t('orderNumber') }}</LabelInput>
                            <InputText
                                id="number"
                                v-model="form.number"
                                type="text" />
                            <InputError :message="form.errors.number" />
                        </div>

                        <div class="flex flex-col">
                            <LabelInput for="status">{{ t('statusOrder') }}</LabelInput>
                            <select id="status" v-model="form.status"
                                    class="py-0.5 rounded-sm dark:bg-slate-800">
                                <option v-for="status in orderStatusOptions"
                                        :key="status" :value="status">
                                    {{ getOrderStatusLabel(status) }}
                                </option>
                            </select>
                            <InputError :message="form.errors.status" />
                        </div>

                        <div class="flex flex-col">
                            <LabelInput for="payment_status">
                                {{ t('paymentStatus') }}
                            </LabelInput>
                            <select id="payment_status" v-model="form.payment_status"
                                    class="py-0.5 rounded-sm dark:bg-slate-800">
                                <option v-for="status in paymentStatusOptions"
                                        :key="status" :value="status">
                                    {{ getPaymentStatusLabel(status) }}
                                </option>
                            </select>
                            <InputError :message="form.errors.payment_status" />
                        </div>
                    </div>

                    <div class="mb-4 border-t border-dashed border-slate-500 pt-4">
                        <h3 class="flex justify-center text-md mb-3
                                   text-fuchsia-700 dark:text-fuchsia-300">
                            <span class="w-fit px-2 border border-gray-400 rounded-sm">
                                {{ t('buyerData') }}
                            </span>
                        </h3>

                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            <div class="flex flex-col">
                                <LabelInput for="buyer_name">
                                    {{ t('buyerName') }}
                                </LabelInput>
                                <InputText
                                    id="buyer_name"
                                    v-model="form.buyer_name"
                                    type="text" />
                                <InputError :message="form.errors.buyer_name" />
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="buyer_email">
                                    {{ t('buyerEmail') }}
                                </LabelInput>
                                <InputText
                                    id="buyer_email"
                                    v-model="form.buyer_email"
                                    type="email" />
                                <InputError :message="form.errors.buyer_email" />
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="buyer_phone">
                                    {{ t('buyerPhone') }}
                                </LabelInput>
                                <InputText
                                    id="buyer_phone"
                                    v-model="form.buyer_phone"
                                    type="text" />
                                <InputError :message="form.errors.buyer_phone" />
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="billing_company">
                                    {{ t('billingCompany') }}
                                </LabelInput>
                                <InputText
                                    id="billing_company"
                                    v-model="form.billing_company"
                                    type="text" />
                                <InputError :message="form.errors.billing_company" />
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="billing_tax_id">
                                    {{ t('billingTaxId') }}
                                </LabelInput>
                                <InputText
                                    id="billing_tax_id"
                                    v-model="form.billing_tax_id"
                                    type="text" />
                                <InputError :message="form.errors.billing_tax_id" />
                            </div>

                            <div class="flex flex-col md:col-span-2 lg:col-span-3">
                                <LabelInput for="billing_address">
                                    {{ t('billingAddress') }}
                                </LabelInput>
                                <MetaDescTextarea
                                    id="billing_address"
                                    v-model="form.billing_address" />
                                <InputError :message="form.errors.billing_address" />
                            </div>
                        </div>
                    </div>

                    <div class="mb-4 border-t border-dashed border-slate-500 pt-4">
                        <h3 class="flex justify-center text-md mb-3 text-fuchsia-700 dark:text-fuchsia-300">
                            <span class="w-fit px-2 border border-gray-400 rounded-sm">
                                {{ t('course') }}
                            </span>
                        </h3>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="flex flex-col">
                                <LabelInput>{{ t('course') }}</LabelInput>
                                <div class="px-2 py-0.5 rounded-sm border border-gray-400
                                            text-sm bg-slate-200 dark:bg-slate-600">
                                    {{ order.course?.title || '—' }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <LabelInput>{{ t('schedule') }}</LabelInput>
                                <div class="px-2 py-0.5 rounded-sm border border-gray-400
                                            text-sm bg-slate-200 dark:bg-slate-600">
                                    {{ order.schedule?.title || '—' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4 border-t border-dashed border-slate-500 pt-4">
                        <h3 class="flex justify-center text-md mb-3
                                   text-fuchsia-700 dark:text-fuchsia-300">
                            <span class="w-fit px-2 border border-gray-400 rounded-sm">
                                {{ t('payment') }}
                            </span>
                        </h3>

                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            <div class="flex flex-col">
                                <LabelInput for="payment_provider">
                                    {{ t('paymentProvider') }}
                                </LabelInput>
                                <InputText
                                    id="payment_provider"
                                    v-model="form.payment_provider"
                                    type="text" />
                                <InputError :message="form.errors.payment_provider" />
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="payment_method">
                                    {{ t('paymentMethod') }}
                                </LabelInput>
                                <InputText
                                    id="payment_method"
                                    v-model="form.payment_method"
                                    type="text" />
                                <InputError :message="form.errors.payment_method" />
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="payment_reference">
                                    {{ t('paymentReference') }}
                                </LabelInput>
                                <InputText
                                    id="payment_reference"
                                    v-model="form.payment_reference"
                                    type="text" />
                                <InputError :message="form.errors.payment_reference" />
                            </div>

                            <div class="flex flex-col">
                                <LabelInput>{{ t('paidAt') }}</LabelInput>
                                <div class="px-2 py-0.5 rounded-sm border border-gray-400
                                            text-sm bg-slate-200 dark:bg-slate-600">
                                    {{ formattedPaidAt }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="confirmation_code">
                                    {{ t('confirmationCode') }}
                                </LabelInput>
                                <InputText
                                    id="confirmation_code"
                                    v-model="form.confirmation_code"
                                    type="text" />
                                <InputError :message="form.errors.confirmation_code" />
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="confirmation_status">
                                    {{ t('confirmationStatus') }}
                                </LabelInput>
                                <InputText
                                    id="confirmation_status"
                                    v-model="form.confirmation_status"
                                    type="text" />
                                <InputError :message="form.errors.confirmation_status" />
                            </div>

                            <div class="flex flex-col md:col-span-2 lg:col-span-3">
                                <LabelInput for="failure_reason">
                                    {{ t('failureReason') }}
                                </LabelInput>
                                <MetaDescTextarea
                                    id="failure_reason"
                                    v-model="form.failure_reason" />
                                <InputError :message="form.errors.failure_reason" />
                            </div>
                        </div>
                    </div>

                    <div class="mb-4 border-t border-dashed border-slate-500 pt-4">
                        <h3 class="flex justify-center text-md mb-3
                                   text-fuchsia-700 dark:text-fuchsia-300">
                            <span class="w-fit px-2 border border-gray-400 rounded-sm">
                                {{ t('totals') }}
                            </span>
                        </h3>

                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
                            <div class="flex flex-col">
                                <LabelInput for="currency">
                                    {{ t('currency') }}
                                </LabelInput>
                                <InputText
                                    id="currency"
                                    v-model="form.currency"
                                    type="text"
                                    maxlength="3" />
                                <InputError :message="form.errors.currency" />
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="subtotal">
                                    {{ t('subtotal') }}
                                </LabelInput>
                                <InputText
                                    id="subtotal"
                                    v-model="form.subtotal"
                                    type="number"
                                    step="0.01" />
                                <InputError :message="form.errors.subtotal" />
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="discount_total">
                                    {{ t('discountTotal') }}
                                </LabelInput>
                                <InputText
                                    id="discount_total"
                                    v-model="form.discount_total"
                                    type="number"
                                    step="0.01" />
                                <InputError :message="form.errors.discount_total" />
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="tax_total">
                                    {{ t('taxTotal') }}
                                </LabelInput>
                                <InputText
                                    id="tax_total"
                                    v-model="form.tax_total"
                                    type="number"
                                    step="0.01" />
                                <InputError :message="form.errors.tax_total" />
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="total">
                                    {{ t('total') }}
                                </LabelInput>
                                <InputText
                                    id="total"
                                    v-model="form.total"
                                    type="number"
                                    step="0.01" />
                                <InputError :message="form.errors.total" />
                            </div>
                        </div>

                        <div class="mt-2 text-center font-semibold
                                    text-sm text-rose-600 dark:text-rose-200">
                            {{ t('total') }}: {{ formatMoney(form.total, form.currency) }}
                        </div>
                    </div>

                    <div class="mb-4 border-t border-dashed border-slate-500 pt-4">
                        <h3 class="flex justify-center text-md mb-3
                                   text-fuchsia-700 dark:text-fuchsia-300">
                            <span class="w-fit px-2 border border-gray-400 rounded-sm">
                                {{ t('comments') }}
                            </span>
                        </h3>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="flex flex-col">
                                <LabelInput for="user_comment">
                                    {{ t('userComment') }}
                                </LabelInput>
                                <MetaDescTextarea
                                    id="user_comment"
                                    v-model="form.user_comment" />
                                <InputError :message="form.errors.user_comment" />
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="manager_comment">
                                    {{ t('managerComment') }}
                                </LabelInput>
                                <MetaDescTextarea
                                    id="manager_comment"
                                    v-model="form.manager_comment" />
                                <InputError :message="form.errors.manager_comment" />
                            </div>
                        </div>
                    </div>

                    <div class="mb-4 border-t border-dashed border-slate-500 pt-4">
                        <h3 class="flex justify-center text-md mb-3 text-fuchsia-700 dark:text-fuchsia-300">
                            <span class="w-fit px-2 border border-gray-400 rounded-sm">
                                {{ t('technicalData') }}
                            </span>
                        </h3>

                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            <div class="flex flex-col">
                                <LabelInput for="external_id">
                                    {{ t('externalId') }}
                                </LabelInput>
                                <InputText
                                    id="external_id"
                                    v-model="form.external_id"
                                    type="text" />
                                <InputError :message="form.errors.external_id" />
                            </div>

                            <div class="flex flex-col">
                                <LabelInput>{{ t('exportedAt') }}</LabelInput>
                                <div class="px-2 py-0.5 rounded-sm border border-gray-400
                                            text-sm bg-slate-200 dark:bg-slate-600">
                                    {{ formattedExportedAt }}
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="client_ip">
                                    {{ t('clientIp') }}
                                </LabelInput>
                                <InputText
                                    id="client_ip"
                                    v-model="form.client_ip"
                                    type="text" />
                                <InputError :message="form.errors.client_ip" />
                            </div>

                            <div class="flex flex-col md:col-span-2 lg:col-span-3">
                                <LabelInput for="user_agent">
                                    {{ t('userAgent') }}
                                </LabelInput>
                                <MetaDescTextarea
                                    id="user_agent"
                                    v-model="form.user_agent" />
                                <InputError :message="form.errors.user_agent" />
                            </div>

                            <div class="flex flex-col md:col-span-2 lg:col-span-3">
                                <LabelInput for="public_hash">
                                    {{ t('publicHash') }}
                                </LabelInput>
                                <InputText
                                    id="public_hash"
                                    v-model="form.public_hash"
                                    type="text" />
                                <InputError :message="form.errors.public_hash" />
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-center mt-4">
                        <DefaultButton :href="route('admin.schoolOrders.index')">
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
                        <PrimaryButton
                            class="ms-4 mb-0"
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
