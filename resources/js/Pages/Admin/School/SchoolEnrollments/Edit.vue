<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Редактирование зачисления (Enrollment)
 */
import { computed, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import PrimaryButton from '@/Components/Admin/UI/Buttons/PrimaryButton.vue'
import LabelInput from '@/Components/Admin/UI/Input/LabelInput.vue'
import InputText from '@/Components/Admin/UI/Input/InputText.vue'
import InputProgress from '@/Components/Admin/UI/Input/InputProgress.vue'
import MetaDescTextarea from '@/Components/Admin/UI/Textarea/MetaDescTextarea.vue'
import InputError from '@/Components/Admin/UI/Input/InputError.vue'
import SelectEntity from '@/Components/Admin/UI/Select/SelectEntity.vue'

// Локализация и уведомления
const { t } = useI18n()
const toast = useToast()

// Пропсы страницы редактирования
const props = defineProps({
    enrollment: { type: Object, required: true },
    users: { type: Array, default: () => [] },
    courses: { type: Array, default: () => [] },
    schedules: { type: Array, default: () => [] },
    orders: { type: Array, default: () => [] },
})

// Нормализация даты под datetime-local
const normalizeDateTimeLocal = (value) => {
    if (!value) return ''

    const str = String(value)
    const match = str.match(/^(\d{4}-\d{2}-\d{2}T\d{2}:\d{2})/)

    return match ? match[1] : str
}

// Форма редактирования зачисления
const form = useForm({
    _method: 'PUT',

    user_id: props.enrollment.user_id ?? null,
    school_course_id: props.enrollment.school_course_id ?? null,
    school_course_schedule_id: props.enrollment.school_course_schedule_id ?? null,
    school_order_id: props.enrollment.school_order_id ?? null,

    status: props.enrollment.status ?? 'active',

    started_at: normalizeDateTimeLocal(props.enrollment.started_at),
    expires_at: normalizeDateTimeLocal(props.enrollment.expires_at),
    completed_at: normalizeDateTimeLocal(props.enrollment.completed_at),

    progress_percent: props.enrollment.progress_percent ?? 0,
    notes: props.enrollment.notes ?? '',
    meta: props.enrollment.meta ?? null,
})

// Модель даты начала
const startedAtModel = computed({
    get: () => normalizeDateTimeLocal(form.started_at),
    set: value => form.started_at = value,
})

// Модель даты окончания доступа
const expiresAtModel = computed({
    get: () => normalizeDateTimeLocal(form.expires_at),
    set: value => form.expires_at = value,
})

// Модель даты завершения
const completedAtModel = computed({
    get: () => normalizeDateTimeLocal(form.completed_at),
    set: value => form.completed_at = value,
})

// Форматирование даты для отображения
const formatDateTime = (value) => {
    if (!value) return ''

    try {
        return new Date(value).toLocaleString('ru-RU')
    } catch {
        return value
    }
}

// Список статусов зачисления
const statusOptions = [
    'active',
    'completed',
    'cancelled',
    'expired',
    'paused',
]

// Ключи переводов статусов
const statusLabelKeyMap = {
    active: 'statusEnrollmentActive',
    completed: 'statusEnrollmentCompleted',
    cancelled: 'statusEnrollmentCancelled',
    expired: 'statusEnrollmentExpired',
    paused: 'statusEnrollmentPaused',
}

// Получение локализованного статуса
const getStatusLabel = (status) => {
    if (!status) return '—'

    const key = statusLabelKeyMap[status]

    return key ? t(key) : status
}

// Опции пользователей
const userOptions = computed(() =>
    props.users.map(user => ({
        id: user.id,
        label: user.name
            ? `[ID: ${user.id}] ${user.name}${user.email ? ` (${user.email})` : ''}`
            : `[ID: ${user.id}]`,
    }))
)

// Опции курсов
const courseOptions = computed(() =>
    props.courses.map(course => ({
        id: course.id,
        label: `[ID: ${course.id}] ${course.title || course.slug || `#${course.id}`}`,
    }))
)

// Опции расписаний
const scheduleOptions = computed(() =>
    props.schedules.map(schedule => {
        const starts = formatDateTime(schedule.starts_at)
        const enrollStart = formatDateTime(schedule.enroll_starts_at)
        const enrollEnd = formatDateTime(schedule.enroll_ends_at)

        const coursePart = schedule.course?.title
            ? `курс: ${schedule.course.title}`
            : null

        const parts = [
            `[ID: ${schedule.id}] ${schedule.title || 'Без названия'}`,
            coursePart,
            starts && `старт: ${starts}`,
            (enrollStart || enrollEnd) &&
            `запись: с ${enrollStart || '—'} по ${enrollEnd || '—'}`,
        ].filter(Boolean)

        return {
            id: schedule.id,
            label: parts.join('\n'),
        }
    })
)

// Опции заказов
const orderOptions = computed(() =>
    props.orders.map(order => {
        const date = formatDateTime(order.created_at)

        const numberPart = order.number
            ? `[ID: ${order.id}] №${order.number}`
            : `[ID: ${order.id}]`

        const amountPart = order.total != null
            ? `${order.total} ${order.currency || ''}`.trim()
            : null

        const userPart = order.buyer_name || order.user?.name
        const emailPart = order.buyer_email || order.user?.email

        const parts = [
            numberPart,
            date && `от ${date}`,
            amountPart && `на сумму ${amountPart}`,
            userPart && `покупатель: ${userPart}${emailPart ? ` (${emailPart})` : ''}`,
        ].filter(Boolean)

        return {
            id: order.id,
            label: parts.join('\n'),
        }
    })
)

// Выбранное расписание
const selectedSchedule = computed(() =>
    props.schedules.find(schedule => Number(schedule.id) === Number(form.school_course_schedule_id))
)

// Детали выбранного расписания
const selectedScheduleDetails = computed(() => {
    if (!selectedSchedule.value) return ''

    const schedule = selectedSchedule.value

    const starts = formatDateTime(schedule.starts_at)
    const enrollStart = formatDateTime(schedule.enroll_starts_at)
    const enrollEnd = formatDateTime(schedule.enroll_ends_at)

    const parts = [
        `#${schedule.id} — ${schedule.title || 'Без названия'}`,
        schedule.course?.title && `Курс: ${schedule.course.title}`,
        starts && `Старт потока: ${starts}`,
        (enrollStart || enrollEnd) &&
        `Запись: с ${enrollStart || '—'} по ${enrollEnd || '—'}`,
    ].filter(Boolean)

    return parts.join('\n')
})

// Выбранный заказ
const selectedOrder = computed(() =>
    props.orders.find(order => Number(order.id) === Number(form.school_order_id))
)

// Детали выбранного заказа
const selectedOrderDetails = computed(() => {
    if (!selectedOrder.value) return ''

    const order = selectedOrder.value
    const date = formatDateTime(order.created_at)

    const numberPart = order.number
        ? `#${order.id} — №${order.number}`
        : `#${order.id}`

    const amountPart = order.total != null
        ? `${order.total} ${order.currency || ''}`.trim()
        : null

    const userPart = order.buyer_name || order.user?.name
    const emailPart = order.buyer_email || order.user?.email

    const parts = [
        numberPart,
        date && `от ${date}`,
        amountPart && `на сумму ${amountPart}`,
        userPart && `Покупатель: ${userPart}${emailPart ? ` (${emailPart})` : ''}`,
    ].filter(Boolean)

    return parts.join('\n')
})

// Автоподстановка курса из расписания
watch(
    () => form.school_course_schedule_id,
    () => {
        const schedule = selectedSchedule.value

        if (!schedule) return

        const courseId = schedule.school_course_id || schedule.course?.id

        if (courseId) {
            const newCourseId = Number(courseId)

            if (!form.school_course_id) {
                form.school_course_id = newCourseId
                return
            }

            if (Number(form.school_course_id) !== newCourseId) {
                form.school_course_id = newCourseId
                toast.info('Курс автоматически подставлен из выбранного расписания')
            }
        }
    }
)

// Автоподстановка пользователя и данных из заказа
watch(
    () => form.school_order_id,
    () => {
        const order = selectedOrder.value

        if (!order) return

        if (order.user_id) {
            const newUserId = Number(order.user_id)

            if (!form.user_id) {
                form.user_id = newUserId
                return
            }

            if (Number(form.user_id) !== newUserId) {
                form.user_id = newUserId
                toast.info('Пользователь автоматически подставлен из выбранного заказа')
            }
        }

        if (order.school_course_id && !form.school_course_id) {
            form.school_course_id = Number(order.school_course_id)
        }

        if (order.school_course_schedule_id && !form.school_course_schedule_id) {
            form.school_course_schedule_id = Number(order.school_course_schedule_id)
        }
    }
)

// Отправка формы обновления
const submitForm = () => {
    form.transform((data) => ({
        ...data,

        user_id: data.user_id || null,
        school_course_id: data.school_course_id || null,
        school_course_schedule_id: data.school_course_schedule_id || null,
        school_order_id: data.school_order_id || null,

        progress_percent: Number(data.progress_percent || 0),
    }))

    form.post(route('admin.schoolEnrollments.update', {
        schoolEnrollment: props.enrollment.id,
    }), {
        preserveScroll: true,
        onSuccess: () => toast.success('Зачисление успешно обновлено.'),
        onError: (errors) => {
            const firstKey = Object.keys(errors || {})[0]
            toast.error(errors?.[firstKey] || 'Проверьте правильность заполнения полей.')
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('editEnrollment')">
        <template #header>
            <TitlePage>
                {{ t('editEnrollment') }} [ID: {{ enrollment.id }}]
            </TitlePage>
        </template>

        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       shadow-lg shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <DefaultButton :href="route('admin.schoolEnrollments.index')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                                 viewBox="0 0 16 16">
                                <path
                                    d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z"
                                />
                            </svg>
                        </template>
                        {{ t('back') }}
                    </DefaultButton>
                </div>
                <form @submit.prevent="submitForm" class="p-3 w-full space-y-3">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col items-start">
                            <LabelInput for="status">
                                <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                {{ t('status') }}
                            </LabelInput>
                            <select
                                id="status"
                                v-model="form.status"
                                class="w-full px-3 py-0.5 form-select bg-white dark:bg-cyan-800
                                       text-gray-600 dark:text-slate-100 rounded-sm shadow-sm
                                       border border-slate-400 dark:border-slate-600"
                            >
                                <option
                                    v-for="status in statusOptions"
                                    :key="status"
                                    :value="status"
                                >
                                    {{ getStatusLabel(status) }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.status" />
                        </div>
                        <div class="flex flex-col items-end">
                            <LabelInput for="progress_percent">
                                {{ t('progress') }}, %
                            </LabelInput>
                            <InputProgress
                                id="progress_percent"
                                v-model="form.progress_percent"
                                class="w-24 md:w-24"
                            />
                            <InputError class="mt-2" :message="form.errors.progress_percent" />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex flex-col items-start">
                            <LabelInput for="started_at">{{ t('accessStartDate') }}</LabelInput>
                            <InputText
                                id="started_at"
                                type="datetime-local"
                                v-model="startedAtModel"
                                autocomplete="off"
                                class="w-full"
                            />
                            <InputError class="mt-2" :message="form.errors.started_at" />
                        </div>
                        <div class="flex flex-col items-start">
                            <LabelInput for="expires_at">{{ t('accessEndDate') }}</LabelInput>
                            <InputText
                                id="expires_at"
                                type="datetime-local"
                                v-model="expiresAtModel"
                                autocomplete="off"
                                class="w-full"
                            />
                            <InputError class="mt-2" :message="form.errors.expires_at" />
                        </div>
                        <div class="flex flex-col items-start">
                            <LabelInput for="completed_at">{{ t('shortCompleted') }}</LabelInput>
                            <InputText
                                id="completed_at"
                                type="datetime-local"
                                v-model="completedAtModel"
                                autocomplete="off"
                                class="w-full"
                            />
                            <InputError class="mt-2" :message="form.errors.completed_at" />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-4">
                        <SelectEntity
                            id="school_order_id"
                            v-model="form.school_order_id"
                            :label="t('order')"
                            :options="orderOptions"
                            :error-message="form.errors.school_order_id"
                            :nullable="true"
                            :placeholder="t('notSelected') || t('select')"
                        />
                        <p
                            v-if="selectedOrderDetails"
                            class="mt-1 text-xs text-fuchsia-500 dark:text-fuchsia-200
                                   font-semibold whitespace-pre-line"
                        >
                            {{ selectedOrderDetails }}
                        </p>
                        <SelectEntity
                            id="school_course_schedule_id"
                            v-model="form.school_course_schedule_id"
                            :label="t('schedule')"
                            :options="scheduleOptions"
                            :error-message="form.errors.school_course_schedule_id"
                            :nullable="true"
                            :placeholder="t('notSelected') || t('select')"
                        />
                        <p
                            v-if="selectedScheduleDetails"
                            class="mt-1 text-xs text-fuchsia-500 dark:text-fuchsia-200
                                   font-semibold whitespace-pre-line"
                        >
                            {{ selectedScheduleDetails }}
                        </p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <SelectEntity
                            id="user_id"
                            v-model="form.user_id"
                            :label="`${t('user')} (${t('autoCorrect')})`"
                            :required="true"
                            :options="userOptions"
                            :error-message="form.errors.user_id"
                            :placeholder="t('select')"
                        />
                        <SelectEntity
                            id="school_course_id"
                            v-model="form.school_course_id"
                            :label="`${t('course')} (${t('autoCorrect')})`"
                            :required="true"
                            :options="courseOptions"
                            :error-message="form.errors.school_course_id"
                            :placeholder="t('select')"
                        />
                    </div>
                    <div class="flex flex-col items-start">
                        <div class="flex justify-between w-full">
                            <LabelInput for="notes">{{ t('notes') }}</LabelInput>
                            <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                {{ form.notes.length }} / 255 {{ t('characters') }}
                            </div>
                        </div>
                        <MetaDescTextarea
                            id="notes"
                            v-model="form.notes"
                            class="w-full"
                        />
                        <InputError class="mt-2" :message="form.errors.notes" />
                    </div>
                    <div class="flex items-center justify-center gap-3">
                        <DefaultButton :href="route('admin.schoolEnrollments.index')">
                            <template #icon>
                                <svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                                     viewBox="0 0 16 16">
                                    <path
                                        d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z"
                                    />
                                </svg>
                            </template>
                            {{ t('back') }}
                        </DefaultButton>
                        <PrimaryButton
                            class="mb-0"
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
