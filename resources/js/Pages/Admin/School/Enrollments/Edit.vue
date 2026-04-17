<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 * Редактирование зачисления (Enrollment)
 */
import { computed, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/Buttons/DefaultButton.vue'
import PrimaryButton from '@/Components/Admin/Buttons/PrimaryButton.vue'
import LabelInput from '@/Components/Admin/Input/LabelInput.vue'
import InputText from '@/Components/Admin/Input/InputText.vue'
import InputProgress from '@/Components/Admin/Input/InputProgress.vue'
import MetaDescTextarea from '@/Components/Admin/Textarea/MetaDescTextarea.vue'
import InputError from '@/Components/Admin/Input/InputError.vue'
import SelectEntity from '@/Components/Admin/Select/SelectEntity.vue'

const { t } = useI18n()
const toast = useToast()

/**
 * Пропсы из контроллера:
 *  - enrollment (EnrollmentResource)
 *  - users
 *  - courses
 *  - courseSchedules
 *  - orders
 *
 * EnrollmentController::edit()
 */
const props = defineProps({
    enrollment: {
        type: Object,
        required: true,
    },
    users: {
        type: Array,
        default: () => [],
    },
    courses: {
        type: Array,
        default: () => [],
    },
    courseSchedules: {
        type: Array,
        default: () => [],
    },
    orders: {
        type: Array,
        default: () => [],
    },
})

/**
 * Нормализуем строку под формат datetime-local:
 *  "2025-12-27T09:53:00.000000Z" → "2025-12-27T09:53"
 */
const normalizeDateTimeLocal = (value) => {
    if (!value) return ''

    const str = String(value)
    const match = str.match(/^(\d{4}-\d{2}-\d{2}T\d{2}:\d{2})/)
    return match ? match[1] : str
}

/**
 * Форма редактирования зачисления.
 * Берём данные из props.enrollment (ресурс).
 */
const form = useForm({
    user_id: props.enrollment.user_id ?? null,
    course_id: props.enrollment.course_id ?? null,
    course_schedule_id: props.enrollment.course_schedule_id ?? null,
    order_id: props.enrollment.order_id ?? null,

    status: props.enrollment.status ?? 'active',

    started_at: normalizeDateTimeLocal(props.enrollment.started_at),
    expires_at: normalizeDateTimeLocal(props.enrollment.expires_at),
    completed_at: normalizeDateTimeLocal(props.enrollment.completed_at),

    progress_percent: props.enrollment.progress_percent ?? 0,
    notes: props.enrollment.notes ?? '',
})

/**
 * Геттеры/сеттеры для datetime-полей,
 * чтобы в инпут НИКОГДА не попала "сырая" строка с Z и микросекундами.
 */
const startedAtModel = computed({
    get() {
        return normalizeDateTimeLocal(form.started_at)
    },
    set(value) {
        form.started_at = value
    },
})

const expiresAtModel = computed({
    get() {
        return normalizeDateTimeLocal(form.expires_at)
    },
    set(value) {
        form.expires_at = value
    },
})

const completedAtModel = computed({
    get() {
        return normalizeDateTimeLocal(form.completed_at)
    },
    set(value) {
        form.completed_at = value
    },
})

/**
 * Форматируем даты для отображения в описаниях (не для инпутов!)
 */
const formatDateTime = (value) => {
    if (!value) return ''
    try {
        return new Date(value).toLocaleString()
    } catch {
        return value
    }
}

/**
 * Статусы зачисления:
 * active|completed|cancelled|expired|paused
 */
const statusOptions = [
    'active',
    'completed',
    'cancelled',
    'expired',
    'paused',
]

const statusLabelKeyMap = {
    active: 'statusEnrollmentActive',
    completed: 'statusEnrollmentCompleted',
    cancelled: 'statusEnrollmentCancelled',
    expired: 'statusEnrollmentExpired',
    paused: 'statusEnrollmentPaused',
}

const getStatusLabel = (status) => {
    if (!status) return '—'
    const key = statusLabelKeyMap[status] || status
    return t(key)
}

/**
 * Опции селектов пользователя, курса, расписания, заказа
 */
const userOptions = computed(() =>
    props.users.map(u => ({
        id: u.id,
        label: u.name
            ? `${u.name}${u.email ? ' (' + u.email + ')' : ''}`
            : `#${u.id}`,
    })),
)

const courseOptions = computed(() =>
    props.courses.map(c => {
        let label = `[ID: ${c.id}]`

        if (c.locale) {
            label += ` [${c.locale}]`
        }

        if (c.title) {
            label += ` - ${c.title}`
        }

        if (c.slug) {
            label += ` (${c.slug})`
        }

        return {
            id: c.id,
            label,
        }
    }),
)

const scheduleOptions = computed(() =>
    props.courseSchedules.map(s => {
        const localePart =
            s.course?.locale
                ? ` [${s.course.locale}]`
                : (s.locale ? ` [${s.locale}]` : '')

        const starts = formatDateTime(s.starts_at)
        const enrollStart = formatDateTime(s.enroll_starts_at)
        const enrollEnd = formatDateTime(s.enroll_ends_at)

        const coursePart = s.course?.title
            ? `${s.course.title}${s.course.slug ? ' (' + s.course.slug + ')' : ''}`
            : null

        const instructorPart = s.instructor?.title
            ? s.instructor.title
            : null

        const parts = [
            // 👇 локаль прямо в первой строке, как у курсов
            `[ID:${s.id}]${localePart} — ${s.title || 'Без названия'}`,
            coursePart && `курс: ${coursePart}`,
            instructorPart && `инструктор: ${instructorPart}`,
            starts && `старт: ${starts}`,
            (enrollStart || enrollEnd) &&
            `запись: с ${enrollStart || '—'} по ${enrollEnd || '—'}`,
        ].filter(Boolean)

        return {
            id: s.id,
            label: parts.join('\n'),
        }
    }),
)

const orderOptions = computed(() =>
    props.orders.map(o => {
        const date = formatDateTime(o.created_at)

        const numberPart = o.number
            ? `[ID:${o.id}] — №${o.number}`
            : `[ID:${o.id}]`

        const amountPart = o.total != null
            ? `${o.total} ${o.currency || ''}`.trim()
            : null

        const userPart = o.user?.name
            ? `${o.user.name}${o.user.email ? ' (' + o.user.email + ')' : ''}`
            : null

        const parts = [
            numberPart,
            date && `от ${date}`,
            amountPart && `на сумму ${amountPart}`,
            userPart && `покупатель: ${userPart}`,
        ].filter(Boolean)

        return {
            id: o.id,
            label: parts.join('\n'),
        }
    }),
)

/**
 * Выбранный поток
 */
const selectedSchedule = computed(() =>
    props.courseSchedules.find(s => s.id === form.course_schedule_id),
)

const selectedScheduleDetails = computed(() => {
    if (!selectedSchedule.value) return ''

    const s = selectedSchedule.value

    const localePart =
        s.course?.locale
            ? ` [${s.course.locale}]`
            : (s.locale ? ` [${s.locale}]` : '')

    const starts = formatDateTime(s.starts_at)
    const enrollStart = formatDateTime(s.enroll_starts_at)
    const enrollEnd = formatDateTime(s.enroll_ends_at)

    const coursePart = s.course?.title
        ? `${s.course.title}${s.course.slug ? ' (' + s.course.slug + ')' : ''}`
        : null

    const instructorPart = s.instructor?.title || null

    const parts = [
        `#${s.id}${localePart} — ${s.title || 'Без названия'}`,
        coursePart && `Курс: ${coursePart}`,
        instructorPart && `Инструктор: ${instructorPart}`,
        starts && `Старт потока: ${starts}`,
        (enrollStart || enrollEnd) &&
        `Запись: с ${enrollStart || '—'} по ${enrollEnd || '—'}`,
    ].filter(Boolean)

    return parts.join('\n')
})

/**
 * Выбранный заказ
 */
const selectedOrder = computed(() =>
    props.orders.find(o => o.id === form.order_id),
)

const selectedOrderDetails = computed(() => {
    if (!selectedOrder.value) return ''

    const o = selectedOrder.value
    const date = formatDateTime(o.created_at)

    const numberPart = o.number
        ? `#${o.id} — №${o.number}`
        : `#${o.id}`

    const amountPart = o.total != null
        ? `${o.total} ${o.currency || ''}`.trim()
        : null

    const userPart = o.user?.name
        ? `${o.user.name}${o.user.email ? ' (' + o.user.email + ')' : ''}`
        : null

    const parts = [
        numberPart,
        date && `от ${date}`,
        amountPart && `на сумму ${amountPart}`,
        userPart && `Покупатель: ${userPart}`,
    ].filter(Boolean)

    return parts.join('\n')
})

// ✅ 1) При выборе расписания — подставляем курс
watch(
    () => form.course_schedule_id,
    () => {
        const s = selectedSchedule.value

        // если расписание сбросили — ничего не трогаем
        if (!s) return

        // если у расписания есть course_id — подставляем
        if (s.course_id) {
            const newCourseId = Number(s.course_id)

            // если курс не выбран — просто ставим
            if (!form.course_id) {
                form.course_id = newCourseId
                return
            }

            // если курс выбран, но другой — перезапишем и покажем уведомление
            if (Number(form.course_id) !== newCourseId) {
                form.course_id = newCourseId
                toast.info('Курс автоматически подставлен из выбранного расписания')
            }
        }
    }
)

// ✅ 2) При выборе заказа — подставляем пользователя
watch(
    () => form.order_id,
    () => {
        const o = selectedOrder.value

        // если заказ сбросили — ничего не трогаем
        if (!o) return

        if (o.user_id) {
            const newUserId = Number(o.user_id)

            if (!form.user_id) {
                form.user_id = newUserId
                return
            }

            if (Number(form.user_id) !== newUserId) {
                form.user_id = newUserId
                toast.info('Пользователь автоматически подставлен из выбранного заказа')
            }
        }
    }
)

/**
 * Отправка формы (обновление)
 */
const submitForm = () => {
    form.put(route('admin.enrollments.update', props.enrollment.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Зачисление успешно обновлено')
        },
        onError: (errors) => {
            console.error('❌ Ошибка при обновлении зачисления:', errors)
            const firstKey = Object.keys(errors || {})[0]
            const firstError = firstKey ? errors[firstKey] : null
            toast.error(firstError || 'Проверьте правильность заполнения полей')
        },
    })
}
</script>

<template>
    <AdminLayout :title="t('editEnrollment')">
        <template #header>
            <TitlePage>
                {{ t('editEnrollment') }} [ID: {{enrollment.id}}]
            </TitlePage>
        </template>

        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       shadow-lg shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <!-- Верх: кнопка "Назад" -->
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <DefaultButton class="mb-3" :href="route('admin.enrollments.index')">
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
                    class="p-3 w-full space-y-3"
                >
                    <!-- Статус + прогресс -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Статус -->
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
                                    v-for="s in statusOptions"
                                    :key="s"
                                    :value="s"
                                >
                                    {{ getStatusLabel(s) }}
                                </option>
                            </select>
                            <InputError
                                class="mt-2"
                                :message="form.errors.status"
                            />
                        </div>

                        <!-- Прогресс, % -->
                        <div class="flex flex-col items-end">
                            <LabelInput for="progress_percent">
                                {{ t('progress') }}, %
                            </LabelInput>
                            <InputProgress
                                id="progress_percent"
                                v-model="form.progress_percent"
                                class="w-24 md:w-24"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.progress_percent"
                            />
                        </div>
                    </div>

                    <!-- Даты доступа -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Начало доступа -->
                        <div class="flex flex-col items-start">
                            <LabelInput for="started_at">
                                {{ t('shortStarted') }}
                            </LabelInput>
                            <InputText
                                id="started_at"
                                type="datetime-local"
                                v-model="startedAtModel"
                                autocomplete="off"
                                class="w-full"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.started_at"
                            />
                        </div>

                        <!-- Истечение доступа -->
                        <div class="flex flex-col items-start">
                            <LabelInput for="expires_at">
                                {{ t('shortExpires') }}
                            </LabelInput>
                            <InputText
                                id="expires_at"
                                type="datetime-local"
                                v-model="expiresAtModel"
                                autocomplete="off"
                                class="w-full"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.expires_at"
                            />
                        </div>

                        <!-- Завершение курса -->
                        <div class="flex flex-col items-start">
                            <LabelInput for="completed_at">
                                {{ t('shortCompleted') }}
                            </LabelInput>
                            <InputText
                                id="completed_at"
                                type="datetime-local"
                                v-model="completedAtModel"
                                autocomplete="off"
                                class="w-full"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.completed_at"
                            />
                        </div>
                    </div>

                    <!-- Заказ + Поток / расписание -->
                    <div class="grid grid-cols-1 gap-4">

                        <SelectEntity
                            id="order_id"
                            v-model="form.order_id"
                            :label="t('order')"
                            :options="orderOptions"
                            :error-message="form.errors.order_id"
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
                            id="course_schedule_id"
                            v-model="form.course_schedule_id"
                            :label="t('schedule')"
                            :options="scheduleOptions"
                            :error-message="form.errors.course_schedule_id"
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

                    <!-- Пользователь + Курс -->
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
                            id="course_id"
                            v-model="form.course_id"
                            :label="`${t('course')} (${t('autoCorrect')})`"
                            :required="true"
                            :options="courseOptions"
                            :error-message="form.errors.course_id"
                            :placeholder="t('select')"
                        />
                    </div>

                    <!-- Заметки -->
                    <div class="flex flex-col items-start">
                        <div class="flex justify-between w-full">
                            <LabelInput for="notes">
                                {{ t('notes') }}
                            </LabelInput>
                            <div class="text-md text-gray-900 dark:text-gray-400 mt-1">
                                {{ form.notes.length }} / 255 {{ t('characters') }}
                            </div>
                        </div>
                        <MetaDescTextarea
                            id="notes"
                            v-model="form.notes"
                            class="w-full"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.notes"
                        />
                    </div>

                    <!-- Кнопки сохранить / назад -->
                    <div class="flex items-center justify-center mt-4 gap-3">
                        <DefaultButton class="mb-3" :href="route('admin.enrollments.index')">
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
                            class="mb-0"
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
