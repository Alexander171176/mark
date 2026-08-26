<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов
 *
 * Создание заказа школы
 */

import { computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import VueMultiselect from 'vue-multiselect'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import PrimaryButton from '@/Components/Admin/UI/Buttons/PrimaryButton.vue'
import LabelInput from '@/Components/Admin/UI/Input/LabelInput.vue'
import InputText from '@/Components/Admin/UI/Input/InputText.vue'
import InputError from '@/Components/Admin/UI/Input/InputError.vue'
import ActivityCheckbox from '@/Components/Admin/UI/Checkbox/ActivityCheckbox.vue'
import MetaDescTextarea from '@/Components/Admin/UI/Textarea/MetaDescTextarea.vue'

/* ==========================================================
 * ЛОКАЛИЗАЦИЯ / УВЕДОМЛЕНИЯ
 * ========================================================== */

const { t } = useI18n()
const toast = useToast()

/* ==========================================================
 * PROPS
 * ========================================================== */

const props = defineProps({
    currentLocale: {
        type: String,
        default: '',
    },

    availableLocales: {
        type: Array,
        default: () => [],
    },

    users: {
        type: Array,
        default: () => [],
    },

    courses: {
        type: Array,
        default: () => [],
    },

    schedules: {
        type: Array,
        default: () => [],
    },
})

/* ==========================================================
 * FORM
 * ========================================================== */

const form = useForm({
    user_id: null,
    school_course_id: null,
    school_course_schedule_id: null,

    number: '',

    buyer_name: '',
    buyer_email: '',
    buyer_phone: '',

    billing_company: '',
    billing_tax_id: '',
    billing_address: '',

    is_paid: false,
    paid_at: '',

    payment_method_id: null,
    payment_method: '',
    payment_provider: '',
    payment_reference: '',
    confirmation_code: '',
    confirmation_status: '',
    failure_reason: '',

    currency: 'USD',

    subtotal: '0.00',
    discount_total: '0.00',
    tax_total: '0.00',
    total: '0.00',

    status: 'new',
    payment_status: 'pending',

    items: null,
    meta: null,

    user_comment: '',
    manager_comment: '',

    external_id: '',
    exported_at: '',

    client_ip: '',
    user_agent: '',
    public_hash: '',
})

/* ==========================================================
 * OPTIONS
 * ========================================================== */

const dynamicOptionsLimit = (items) => {
    return (items?.length || 0) + 10
}

const userOptions = computed(() =>
    props.users.map(user => ({
        ...user,
        label: `[ID: ${user.id}] ${user.name || user.email || `#${user.id}`}`,
    }))
)

const courseOptions = computed(() =>
    props.courses.map(course => ({
        ...course,
        label: `[ID: ${course.id}] ${
            course?.translation?.title
            || course?.slug
            || `#${course.id}`
        }`,
    }))
)

const scheduleOptions = computed(() =>
    props.schedules.map(schedule => {
        const scheduleTitle =
            schedule?.translation?.title
            || `#${schedule.id}`

        const courseTitle =
            schedule?.course?.translation?.title
            || schedule?.course?.slug
            || ''

        return {
            ...schedule,

            label: courseTitle
                ? `[ID: ${schedule.id}] [${courseTitle}] ${scheduleTitle}`
                : `[ID: ${schedule.id}] ${scheduleTitle}`,
        }
    })
)

/* ==========================================================
 * SELECTED ENTITIES
 *
 * Единственное состояние — ID внутри form.
 * ========================================================== */

const selectedUser = computed({
    get: () =>
        userOptions.value.find(
            user => Number(user.id) === Number(form.user_id)
        ) || null,

    set: (user) => {
        form.user_id = user?.id ?? null
    },
})

const selectedCourse = computed({
    get: () =>
        courseOptions.value.find(
            course => Number(course.id) === Number(form.school_course_id)
        ) || null,

    set: (course) => {
        form.school_course_id = course?.id ?? null
    },
})

const selectedSchedule = computed({
    get: () =>
        scheduleOptions.value.find(
            schedule => Number(schedule.id) === Number(form.school_course_schedule_id)
        ) || null,

    set: (schedule) => {
        form.school_course_schedule_id = schedule?.id ?? null
    },
})

/* ==========================================================
 * STATUS
 * ========================================================== */

const orderStatusLabelKeyMap = {
    new: 'statusOrderNew',
    processing: 'statusOrderProcessing',
    completed: 'statusOrderCompleted',
    cancelled: 'statusOrderCancelled',
    refunded: 'statusPaidRefunded',
    archived: 'statusArchived',
}

const paymentStatusLabelKeyMap = {
    pending: 'statusPaidPending',
    paid: 'statusPaid',
    succeeded: 'statusPaid',
    failed: 'statusPaidError',
    partial: 'statusPaidPartial',
    partially_refunded: 'statusPaidPartial',
    refunded: 'statusPaidRefunded',
}

const orderStatusOptions = [
    'new',
    'processing',
    'completed',
    'cancelled',
    'refunded',
    'archived',
]

const paymentStatusOptions = [
    'pending',
    'paid',
    'succeeded',
    'failed',
    'partial',
    'partially_refunded',
    'refunded',
]

const getOrderStatusLabel = (status) => {
    if (!status) return '—'

    const key = orderStatusLabelKeyMap[status]

    return key
        ? t(key)
        : status
}

const getPaymentStatusLabel = (status) => {
    if (!status) return '—'

    const key = paymentStatusLabelKeyMap[status]

    return key
        ? t(key)
        : status
}

/* ==========================================================
 * FORMATTERS
 * ========================================================== */

const formatMoney = (value, currency = 'USD') => {
    if (value === null || value === undefined) return '—'

    const number = Number(value)

    if (Number.isNaN(number)) {
        return String(value)
    }

    return `${number.toFixed(2)} ${currency}`
}

/* ==========================================================
 * SUBMIT
 * ========================================================== */

const submitForm = () => {
    form.transform(data => ({
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

    form.post(route('admin.schoolOrders.store'), {
        preserveScroll: true,

        onSuccess: () => {
            toast.success('Заказ успешно создан.')
        },

        onError: (errors) => {
            const firstKey = Object.keys(errors || {})[0]

            toast.error(
                errors?.[firstKey]
                || 'Проверьте правильность заполнения полей заказа.'
            )
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('createOrder')">
        <template #header>
            <TitlePage>
                {{ t('createOrder') }}
            </TitlePage>
        </template>

        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto">
            <div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 shadow-lg shadow-gray-500 dark:shadow-slate-400">
                <div class="sm:flex sm:justify-between sm:items-center mb-4">
                    <DefaultButton :href="route('admin.schoolOrders.index')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2" viewBox="0 0 16 16">
                                <path d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c-.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2 .8-6.4z" />
                            </svg>
                        </template>
                        {{ t('back') }}
                    </DefaultButton>
                </div>

                <form class="w-full" @submit.prevent="submitForm">
                    <!-- Основные данные -->
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
                            <InputText id="number" v-model="form.number" type="text" />
                            <InputError :message="form.errors.number" />
                        </div>

                        <div class="flex flex-col">
                            <LabelInput for="status">{{ t('statusOrder') }}</LabelInput>
                            <select id="status" v-model="form.status" class="py-0.5 rounded-sm dark:bg-slate-800">
                                <option v-for="status in orderStatusOptions" :key="status" :value="status">
                                    {{ getOrderStatusLabel(status) }}
                                </option>
                            </select>
                            <InputError :message="form.errors.status" />
                        </div>

                        <div class="flex flex-col">
                            <LabelInput for="payment_status">{{ t('paymentStatus') }}</LabelInput>
                            <select id="payment_status" v-model="form.payment_status" class="py-0.5 rounded-sm dark:bg-slate-800">
                                <option v-for="status in paymentStatusOptions" :key="status" :value="status">
                                    {{ getPaymentStatusLabel(status) }}
                                </option>
                            </select>
                            <InputError :message="form.errors.payment_status" />
                        </div>
                    </div>

                    <!-- Связи -->
                    <div class="mb-4 border-t border-dashed border-slate-500 pt-4">
                        <h3 class="flex justify-center text-md mb-3 text-fuchsia-700 dark:text-fuchsia-300">
                            <span class="w-fit px-2 border border-gray-400 rounded-sm">
                                {{ t('relations') }}
                            </span>
                        </h3>

                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            <div class="flex flex-col">
                                <LabelInput>{{ t('user') }}</LabelInput>
                                <VueMultiselect
                                    v-model="selectedUser"
                                    :options="userOptions"
                                    :options-limit="dynamicOptionsLimit(userOptions)"
                                    track-by="id"
                                    label="label"
                                    :searchable="true"
                                    :allow-empty="true"
                                    :show-labels="false"
                                    :placeholder="t('select')"
                                />
                                <InputError :message="form.errors.user_id" />
                            </div>

                            <div class="flex flex-col">
                                <LabelInput>{{ t('course') }}</LabelInput>
                                <VueMultiselect
                                    v-model="selectedCourse"
                                    :options="courseOptions"
                                    :options-limit="dynamicOptionsLimit(courseOptions)"
                                    track-by="id"
                                    label="label"
                                    :searchable="true"
                                    :allow-empty="true"
                                    :show-labels="false"
                                    :placeholder="t('select')"
                                />
                                <InputError :message="form.errors.school_course_id" />
                            </div>

                            <div class="flex flex-col">
                                <LabelInput>{{ t('schedule') }}</LabelInput>
                                <VueMultiselect
                                    v-model="selectedSchedule"
                                    :options="scheduleOptions"
                                    :options-limit="dynamicOptionsLimit(scheduleOptions)"
                                    track-by="id"
                                    label="label"
                                    :searchable="true"
                                    :allow-empty="true"
                                    :show-labels="false"
                                    :placeholder="t('select')"
                                />
                                <InputError :message="form.errors.school_course_schedule_id" />
                            </div>
                        </div>
                    </div>

                    <!-- Покупатель -->
                    <div class="mb-4 border-t border-dashed border-slate-500 pt-4">
                        <h3 class="flex justify-center text-md mb-3 text-fuchsia-700 dark:text-fuchsia-300">
                            <span class="w-fit px-2 border border-gray-400 rounded-sm">
                                {{ t('buyerData') }}
                            </span>
                        </h3>

                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            <div class="flex flex-col">
                                <LabelInput for="buyer_name">{{ t('buyerName') }}</LabelInput>
                                <InputText id="buyer_name" v-model="form.buyer_name" type="text" />
                                <InputError :message="form.errors.buyer_name" />
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="buyer_email">{{ t('buyerEmail') }}</LabelInput>
                                <InputText id="buyer_email" v-model="form.buyer_email" type="email" />
                                <InputError :message="form.errors.buyer_email" />
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="buyer_phone">{{ t('buyerPhone') }}</LabelInput>
                                <InputText id="buyer_phone" v-model="form.buyer_phone" type="text" />
                                <InputError :message="form.errors.buyer_phone" />
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="billing_company">{{ t('billingCompany') }}</LabelInput>
                                <InputText id="billing_company" v-model="form.billing_company" type="text" />
                                <InputError :message="form.errors.billing_company" />
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="billing_tax_id">{{ t('billingTaxId') }}</LabelInput>
                                <InputText id="billing_tax_id" v-model="form.billing_tax_id" type="text" />
                                <InputError :message="form.errors.billing_tax_id" />
                            </div>

                            <div class="flex flex-col md:col-span-2 lg:col-span-3">
                                <LabelInput for="billing_address">{{ t('billingAddress') }}</LabelInput>
                                <MetaDescTextarea id="billing_address" v-model="form.billing_address" />
                                <InputError :message="form.errors.billing_address" />
                            </div>
                        </div>
                    </div>

                    <!-- Оплата -->
                    <div class="mb-4 border-t border-dashed border-slate-500 pt-4">
                        <h3 class="flex justify-center text-md mb-3 text-fuchsia-700 dark:text-fuchsia-300">
                            <span class="w-fit px-2 border border-gray-400 rounded-sm">
                                {{ t('payment') }}
                            </span>
                        </h3>

                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            <div class="flex flex-col">
                                <LabelInput for="payment_provider">{{ t('paymentProvider') }}</LabelInput>
                                <InputText id="payment_provider" v-model="form.payment_provider" type="text" />
                                <InputError :message="form.errors.payment_provider" />
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="payment_method">{{ t('paymentMethod') }}</LabelInput>
                                <InputText id="payment_method" v-model="form.payment_method" type="text" />
                                <InputError :message="form.errors.payment_method" />
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="payment_reference">{{ t('paymentReference') }}</LabelInput>
                                <InputText id="payment_reference" v-model="form.payment_reference" type="text" />
                                <InputError :message="form.errors.payment_reference" />
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="confirmation_code">{{ t('confirmationCode') }}</LabelInput>
                                <InputText id="confirmation_code" v-model="form.confirmation_code" type="text" />
                                <InputError :message="form.errors.confirmation_code" />
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="confirmation_status">{{ t('confirmationStatus') }}</LabelInput>
                                <InputText id="confirmation_status" v-model="form.confirmation_status" type="text" />
                                <InputError :message="form.errors.confirmation_status" />
                            </div>

                            <div class="flex flex-col md:col-span-2 lg:col-span-3">
                                <LabelInput for="failure_reason">{{ t('failureReason') }}</LabelInput>
                                <MetaDescTextarea id="failure_reason" v-model="form.failure_reason" />
                                <InputError :message="form.errors.failure_reason" />
                            </div>
                        </div>
                    </div>

                    <!-- Суммы -->
                    <div class="mb-4 border-t border-dashed border-slate-500 pt-4">
                        <h3 class="flex justify-center text-md mb-3 text-fuchsia-700 dark:text-fuchsia-300">
                            <span class="w-fit px-2 border border-gray-400 rounded-sm">
                                {{ t('totals') }}
                            </span>
                        </h3>

                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
                            <div class="flex flex-col">
                                <LabelInput for="currency">{{ t('currency') }}</LabelInput>
                                <InputText id="currency" v-model="form.currency" type="text" maxlength="3" />
                                <InputError :message="form.errors.currency" />
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="subtotal">{{ t('subtotal') }}</LabelInput>
                                <InputText id="subtotal" v-model="form.subtotal" type="number" step="0.01" />
                                <InputError :message="form.errors.subtotal" />
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="discount_total">{{ t('discountTotal') }}</LabelInput>
                                <InputText id="discount_total" v-model="form.discount_total" type="number" step="0.01" />
                                <InputError :message="form.errors.discount_total" />
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="tax_total">{{ t('taxTotal') }}</LabelInput>
                                <InputText id="tax_total" v-model="form.tax_total" type="number" step="0.01" />
                                <InputError :message="form.errors.tax_total" />
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="total">{{ t('total') }}</LabelInput>
                                <InputText id="total" v-model="form.total" type="number" step="0.01" />
                                <InputError :message="form.errors.total" />
                            </div>
                        </div>

                        <div class="mt-2 text-center font-semibold text-sm text-rose-600 dark:text-rose-200">
                            {{ t('total') }}: {{ formatMoney(form.total, form.currency) }}
                        </div>
                    </div>

                    <!-- Комментарии -->
                    <div class="mb-4 border-t border-dashed border-slate-500 pt-4">
                        <h3 class="flex justify-center text-md mb-3 text-fuchsia-700 dark:text-fuchsia-300">
                            <span class="w-fit px-2 border border-gray-400 rounded-sm">
                                {{ t('comments') }}
                            </span>
                        </h3>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="flex flex-col">
                                <LabelInput for="user_comment">{{ t('userComment') }}</LabelInput>
                                <MetaDescTextarea id="user_comment" v-model="form.user_comment" />
                                <InputError :message="form.errors.user_comment" />
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="manager_comment">{{ t('managerComment') }}</LabelInput>
                                <MetaDescTextarea id="manager_comment" v-model="form.manager_comment" />
                                <InputError :message="form.errors.manager_comment" />
                            </div>
                        </div>
                    </div>

                    <!-- Технические данные -->
                    <div class="mb-4 border-t border-dashed border-slate-500 pt-4">
                        <h3 class="flex justify-center text-md mb-3 text-fuchsia-700 dark:text-fuchsia-300">
                            <span class="w-fit px-2 border border-gray-400 rounded-sm">
                                {{ t('technicalData') }}
                            </span>
                        </h3>

                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            <div class="flex flex-col">
                                <LabelInput for="external_id">{{ t('externalId') }}</LabelInput>
                                <InputText id="external_id" v-model="form.external_id" type="text" />
                                <InputError :message="form.errors.external_id" />
                            </div>

                            <div class="flex flex-col">
                                <LabelInput for="client_ip">{{ t('clientIp') }}</LabelInput>
                                <InputText id="client_ip" v-model="form.client_ip" type="text" />
                                <InputError :message="form.errors.client_ip" />
                            </div>

                            <div class="flex flex-col md:col-span-2 lg:col-span-3">
                                <LabelInput for="user_agent">{{ t('userAgent') }}</LabelInput>
                                <MetaDescTextarea id="user_agent" v-model="form.user_agent" />
                                <InputError :message="form.errors.user_agent" />
                            </div>

                            <div class="flex flex-col md:col-span-2 lg:col-span-3">
                                <LabelInput for="public_hash">{{ t('publicHash') }}</LabelInput>
                                <InputText id="public_hash" v-model="form.public_hash" type="text" />
                                <InputError :message="form.errors.public_hash" />
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-center mt-4">
                        <DefaultButton :href="route('admin.schoolOrders.index')">
                            {{ t('back') }}
                        </DefaultButton>

                        <PrimaryButton
                            class="ms-4 mb-0"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            {{ t('save') }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<style src="/resources/css/vue-multiselect.min.css"></style>
