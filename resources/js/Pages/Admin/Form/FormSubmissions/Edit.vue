<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Редактирование заявки динамической формы.
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
import InputError from '@/Components/Admin/UI/Input/InputError.vue'

const { t } = useI18n()
const toast = useToast()

/* ===================== Props ===================== */

const props = defineProps({
    submission: {
        type: [Object, null],
        default: null,
    },

    statuses: {
        type: [Array, Object],
        default: () => ({}),
    },

    users: {
        type: Array,
        default: () => [],
    },

    currentLocale: {
        type: String,
        default: '',
    },

    errors: {
        type: Object,
        default: () => ({}),
    },
})

/* ===================== Resource ===================== */

/**
 * Поддерживает как прямой Resource,
 * так и обёртку { data: {...} }.
 */
const submissionData = computed(() => {
    return props.submission?.data
        || props.submission
        || {}
})

/**
 * Получение массива из Laravel ResourceCollection.
 */
const resourceList = (value) => {
    if (Array.isArray(value?.data)) {
        return value.data
    }

    return Array.isArray(value)
        ? value
        : []
}

const values = computed(() => {
    return resourceList(submissionData.value?.values)
})

const files = computed(() => {
    return resourceList(submissionData.value?.files)
})

const statusHistory = computed(() => {
    return resourceList(submissionData.value?.status_history)
})

/* ===================== Form ===================== */

const form = useForm({
    _method: 'put',

    status: submissionData.value.status || 'new',

    assigned_user_id:
        submissionData.value.assigned_user_id
        ?? null,
})

/* ===================== Form data ===================== */

const formTitle = computed(() => {
    return submissionData.value?.form?.translation?.title
        || submissionData.value?.form?.title
        || submissionData.value?.form?.code
        || `ID: ${submissionData.value?.form_id ?? ''}`
})

const pageTitle = computed(() => {
    return `${formTitle.value} [ID: ${submissionData.value?.id ?? ''}]`
})

const senderName = computed(() => {
    return submissionData.value?.user?.name
        || 'Гость'
})

const senderEmail = computed(() => {
    return submissionData.value?.user?.email
        || ''
})

const assignedUser = computed(() => {
    return submissionData.value?.assigned_user
        || submissionData.value?.assignedUser
        || null
})

/* ===================== Status ===================== */

/**
 * Карта переводов статусов заявки.
 */
const statusTranslationMap = {
    new: 'statusNew',
    processing: 'statusProcessing',
    completed: 'statusCompleted',
    cancelled: 'statusCancelled',
    spam: 'statusSpam',
}

/**
 * Получение подписи статуса.
 */
const statusLabel = (status) => {
    const translationKey = statusTranslationMap[status]

    if (translationKey) {
        return t(translationKey)
    }

    const configured = props.statuses?.[status]

    if (typeof configured === 'string') {
        return configured
    }

    if (configured?.label) {
        return configured.label
    }

    return status || '—'
}

/**
 * Цвет статуса.
 */
const statusBadgeClass = (status) => {
    const classes = {
        new: 'bg-blue-100 text-blue-800 border border-blue-300 ' +
            'dark:bg-blue-900/40 dark:text-blue-200 dark:border-blue-500',
        processing: 'bg-amber-100 text-amber-800 border border-amber-300 ' +
            'dark:bg-amber-900/40 dark:text-amber-200 dark:border-amber-500',
        completed: 'bg-teal-100 text-teal-800 border border-teal-300 ' +
            'dark:bg-teal-900/40 dark:text-teal-200 dark:border-teal-500',
        cancelled: 'bg-slate-200 text-slate-700 border border-slate-400 ' +
            'dark:bg-slate-700 dark:text-slate-200 dark:border-slate-500',
        spam: 'bg-red-100 text-red-800 border border-red-300 ' +
            'dark:bg-red-900/40 dark:text-red-200 dark:border-red-500'
    }

    return classes[status]
        || 'bg-slate-100 text-slate-700 border-slate-400 '
        + 'dark:bg-slate-700 dark:text-slate-200 dark:border-slate-600'
}

/* ===================== Helpers ===================== */

/**
 * Форматирование даты.
 */
const formatDate = (value) => {
    if (!value) {
        return '—'
    }

    const date = new Date(value)

    if (Number.isNaN(date.getTime())) {
        return value
    }

    return new Intl.DateTimeFormat('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
    }).format(date)
}

/**
 * Проверка наличия значения.
 */
const hasValue = (value) => {
    return value !== null
        && value !== undefined
        && value !== ''
}

/**
 * Преобразование значения в строку.
 */
const displayValue = (value) => {
    if (!hasValue(value)) {
        return '—'
    }

    if (Array.isArray(value)) {
        return value.join(', ')
    }

    if (typeof value === 'object') {
        return JSON.stringify(value, null, 2)
    }

    return String(value)
}

/**
 * Значение snapshot-поля.
 */
const submissionValue = (item) => {
    if (hasValue(item?.display_value)) {
        return displayValue(item.display_value)
    }

    if (hasValue(item?.value_json)) {
        return displayValue(item.value_json)
    }

    return displayValue(item?.value)
}

/**
 * Название snapshot-поля.
 */
const submissionValueLabel = (item) => {
    return item?.field_label
        || item?.field_name
        || `Поле #${item?.form_field_id ?? ''}`
}

/**
 * Форматирование размера файла.
 */
const formatFileSize = (bytes) => {
    const size = Number(bytes)

    if (!Number.isFinite(size) || size < 0) {
        return '—'
    }

    if (size < 1024) {
        return `${size} Б`
    }

    if (size < 1024 * 1024) {
        return `${(size / 1024).toFixed(1)} КБ`
    }

    return `${(size / 1024 / 1024).toFixed(2)} МБ`
}

/**
 * Имя пользователя истории.
 */
const historyUserName = (item) => {
    return item?.user?.name
        || (item?.user_id ? `User #${item.user_id}` : 'Система')
}

/**
 * Есть ли UTM-данные.
 */
const hasUtm = computed(() => {
    return [
        submissionData.value?.utm_source,
        submissionData.value?.utm_medium,
        submissionData.value?.utm_campaign,
        submissionData.value?.utm_content,
        submissionData.value?.utm_term,
    ].some(hasValue)
})

/**
 * Есть ли context.
 */
const hasContext = computed(() => {
    const context = submissionData.value?.context

    if (!context) {
        return false
    }

    if (typeof context === 'object') {
        return Object.keys(context).length > 0
    }

    return true
})

/* ===================== Submit ===================== */

const submitForm = () => {
    form.transform((data) => ({
        ...data,

        assigned_user_id:
            data.assigned_user_id === ''
            || data.assigned_user_id === null
                ? null
                : Number(data.assigned_user_id),
    }))

    form.post(
        route('admin.formSubmissions.update', {
            formSubmission: submissionData.value.id,
        }),
        {
            errorBag: 'editFormSubmission',
            preserveScroll: true,

            onSuccess: () => {
                toast.success('Заявка успешно обновлена.')
            },

            onError: (errors) => {
                const firstKey = Object.keys(errors || {})[0]

                toast.error(
                    errors[firstKey]
                    || 'Проверьте корректность заполнения полей.'
                )
            },
        }
    )
}
</script>

<template>
    <AdminLayout :title="pageTitle">
        <template #header>
            <TitlePage>
                {{ t('editSubmission') }}: {{ formTitle }} [ID: {{ submissionData.id }}]
            </TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500
                       dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <!-- Верхняя панель -->
                <div
                    class="flex flex-col sm:flex-row sm:justify-between
                           sm:items-center gap-2 mb-3"
                >
                    <DefaultButton :href="route('admin.formSubmissions.index')">
                        <template #icon>
                            <svg
                                class="w-4 h-4 fill-current text-slate-100
                                       shrink-0 mr-2"
                                viewBox="0 0 16 16"
                            >
                                <path
                                    d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z"
                                />
                            </svg>
                        </template>

                        {{ t('back') }}
                    </DefaultButton>

                    <div class="flex flex-wrap items-center gap-2">
                        <span
                            class="font-semibold text-xs
                                       text-indigo-600 dark:text-indigo-200">
                            {{ formatDate(submissionData.submitted_at) }}
                        </span>
                        <span
                            class="inline-flex items-center rounded-full
                                   border px-2.5 py-1 text-xs font-semibold"
                            :class="statusBadgeClass(submissionData.status)"
                        >
                            {{ statusLabel(submissionData.status) }}
                        </span>
                    </div>
                </div>

                <!-- Управление заявкой -->
                <form
                    class="w-full"
                    @submit.prevent="submitForm"
                >
                    <div
                        class="mb-4 p-3 border border-indigo-300
                               dark:border-indigo-600
                               bg-indigo-50/60 dark:bg-slate-800
                               rounded-sm"
                    >
                        <h3
                            class="mb-3 text-md font-semibold
                                   text-slate-800 dark:text-slate-100"
                        >
                            {{ t('requestManagement') }}
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <!-- Статус -->
                            <div class="flex flex-col items-start">
                                <LabelInput
                                    for="status"
                                    :value="t('status')"
                                />

                                <select
                                    id="status"
                                    v-model="form.status"
                                    class="w-full px-2 py-0.5 form-select
                                           bg-white text-gray-600
                                           border border-slate-400
                                           dark:border-slate-600 rounded-sm
                                           shadow-sm dark:bg-cyan-800
                                           dark:text-slate-100"
                                >
                                    <option
                                        v-for="(status, value) in statuses"
                                        :key="value"
                                        :value="value"
                                    >
                                        {{ statusLabel(value) }}
                                    </option>
                                </select>

                                <InputError
                                    class="mt-2"
                                    :message="form.errors.status"
                                />
                            </div>

                            <!-- Ответственный -->
                            <div class="flex flex-col items-start">
                                <LabelInput
                                    for="assigned_user_id"
                                    :value="t('assignedUser')"
                                />

                                <select
                                    id="assigned_user_id"
                                    v-model="form.assigned_user_id"
                                    class="w-full px-2 py-0.5 form-select
                                           bg-white text-gray-600
                                           border border-slate-400
                                           dark:border-slate-600 rounded-sm
                                           shadow-sm dark:bg-cyan-800
                                           dark:text-slate-100"
                                >
                                    <option value="">
                                        {{ t('notAssigned') }}
                                    </option>

                                    <option
                                        v-for="user in users"
                                        :key="user.id"
                                        :value="user.id"
                                    >
                                        {{ user.name }}
                                        <template v-if="user.email">
                                            — {{ user.email }}
                                        </template>
                                    </option>
                                </select>

                                <InputError
                                    class="mt-2"
                                    :message="form.errors.assigned_user_id"
                                />
                            </div>
                        </div>

                        <div
                            v-if="assignedUser"
                            class="mt-3 text-xs text-slate-500
                                   dark:text-slate-400"
                        >
                            {{ t('currentPersonInCharge') }}:
                            <span
                                class="font-medium text-slate-700
                                       dark:text-slate-200"
                            >
                                {{ assignedUser.name }}
                            </span>
                        </div>
                    </div>

                    <!-- Основная информация -->
                    <div
                        class="grid grid-cols-1 lg:grid-cols-2
                               gap-4 mb-4"
                    >
                        <!-- Заявка -->
                        <div
                            class="p-3 border border-slate-400
                                   dark:border-slate-500 bg-white
                                   dark:bg-slate-800 rounded-sm"
                        >
                            <h3
                                class="mb-3 text-md font-semibold
                                       text-slate-800 dark:text-slate-100"
                            >
                                {{ t('submission') }}
                            </h3>

                            <dl class="space-y-2 text-sm">
                                <div
                                    class="flex justify-between gap-4
                                           border-b border-slate-300
                                           dark:border-slate-600 pb-1"
                                >
                                    <dt class="text-slate-600 dark:text-slate-400">
                                        ID
                                    </dt>

                                    <dd
                                        class="font-medium text-indigo-700 dark:text-indigo-300"
                                    >
                                        {{ submissionData.id }}
                                    </dd>
                                </div>

                                <div
                                    class="flex justify-between gap-4
                                           border-b border-slate-300
                                           dark:border-slate-600 pb-1"
                                >
                                    <dt class="text-slate-600 dark:text-slate-400">
                                        {{ t('form') }}
                                    </dt>

                                    <dd
                                        class="text-right font-medium
                                               text-indigo-700 dark:text-indigo-300"
                                    >
                                        {{ formTitle }}
                                    </dd>
                                </div>

                                <div
                                    class="flex justify-between gap-4
                                           border-b border-slate-300
                                           dark:border-slate-600 pb-1"
                                >
                                    <dt class="text-slate-600 dark:text-slate-400">
                                        {{ t('form') }} ID
                                    </dt>

                                    <dd class="font-medium text-indigo-700 dark:text-indigo-300">
                                        {{ submissionData.form_id }}
                                    </dd>
                                </div>

                                <div
                                    class="flex justify-between gap-4
                                           border-b border-slate-300
                                           dark:border-slate-600 pb-1"
                                >
                                    <dt class="text-slate-600 dark:text-slate-400">
                                        {{ t('source') }}
                                    </dt>

                                    <dd class="font-medium text-indigo-700 dark:text-indigo-300">
                                        {{ submissionData.source || '—' }}
                                    </dd>
                                </div>

                                <div
                                    class="flex justify-between gap-4
                                           border-b border-slate-300
                                           dark:border-slate-600 pb-1"
                                >
                                    <dt class="text-slate-600 dark:text-slate-400">
                                        {{ t('locale') }}
                                    </dt>

                                    <dd class="uppercase font-medium
                                               text-indigo-700 dark:text-indigo-300">
                                        {{ submissionData.locale || '—' }}
                                    </dd>
                                </div>

                                <div class="flex justify-between gap-4">
                                    <dt class="text-slate-600 dark:text-slate-400">
                                        {{ t('sent') }}
                                    </dt>

                                    <dd
                                        class="text-right font-medium
                                               text-blue-700 dark:text-blue-300"
                                    >
                                        {{ formatDate(submissionData.submitted_at) }}
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Отправитель -->
                        <div
                            class="p-3 border border-slate-400
                                   dark:border-slate-500 bg-white
                                   dark:bg-slate-800 rounded-sm"
                        >
                            <h3
                                class="mb-3 text-md font-semibold
                                       text-slate-800 dark:text-slate-100"
                            >
                                {{ t('sender') }}
                            </h3>

                            <dl class="space-y-2 text-sm">
                                <div
                                    class="flex justify-between gap-4
                                           border-b border-slate-300
                                           dark:border-slate-600 pb-1"
                                >
                                    <dt class="text-slate-600 dark:text-slate-400">
                                        {{ t('type') }}
                                    </dt>

                                    <dd
                                        class="font-medium text-blue-700 dark:text-blue-300"
                                    >
                                    {{ submissionData.user_id ? t('authorizedUser') : t('guest') }}
                                    </dd>
                                </div>

                                <div
                                    class="flex justify-between gap-4
                                           border-b border-slate-300
                                           dark:border-slate-600 pb-1"
                                >
                                    <dt class="text-slate-600 dark:text-slate-400">
                                        {{ t('name') }}
                                    </dt>

                                    <dd class="font-medium text-blue-700 dark:text-blue-300">
                                        {{ senderName }}
                                    </dd>
                                </div>

                                <div
                                    class="flex justify-between gap-4
                                           border-b border-slate-300
                                           dark:border-slate-600 pb-1"
                                >
                                    <dt class="text-slate-600 dark:text-slate-400">
                                        Email
                                    </dt>

                                    <dd class="font-medium text-blue-700 dark:text-blue-300">
                                        {{ senderEmail || '—' }}
                                    </dd>
                                </div>

                                <div class="flex justify-between gap-4">
                                    <dt class="text-slate-600 dark:text-slate-400">
                                        User ID
                                    </dt>

                                    <dd class="font-medium text-blue-700 dark:text-blue-300">
                                        {{ submissionData.user_id || '—' }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Данные формы -->
                    <div
                        class="mb-4 p-3 border border-slate-400 dark:border-slate-500
                               bg-white dark:bg-slate-800 rounded-sm"
                    >
                        <div
                            class="mb-3 flex items-center
                                   justify-between gap-3"
                        >
                            <h3
                                class="flex items-center justify-center gap-3
                                       text-md font-semibold text-slate-800 dark:text-slate-100"
                            >
                                {{ t('theseForms') }}

                                <span
                                    class="text-xs text-slate-500
                                       dark:text-slate-400"
                                >
                                {{ values.length }}
                            </span>
                            </h3>
                        </div>

                        <div
                            v-if="values.length"
                            class="divide-y divide-slate-200
                                   dark:divide-slate-600"
                        >
                            <div
                                v-for="item in values"
                                :key="item.id"
                                class="grid grid-cols-1 md:grid-cols-3
                                       gap-1 md:gap-4 py-2"
                            >
                                <div
                                    class="text-sm font-medium
                                           text-indigo-700 dark:text-indigo-300"
                                >
                                    {{ submissionValueLabel(item) }}

                                    <div
                                        v-if="item.field_name"
                                        class="text-[11px] font-normal text-slate-400"
                                    >
                                        {{ item.field_name }}
                                    </div>
                                </div>

                                <div
                                    class="md:col-span-2 text-sm font-semibold
                                           whitespace-pre-wrap break-words
                                           text-blue-700 dark:text-blue-300"
                                >
                                    {{ submissionValue(item) }}
                                </div>
                            </div>
                        </div>

                        <div
                            v-else
                            class="py-3 text-center text-sm
                                   text-slate-600 dark:text-slate-400"
                        >
                            {{ t('dataNotAvailable') }}.
                        </div>
                    </div>

                    <!-- Файлы -->
                    <div
                        v-if="files.length"
                        class="mb-4 p-3 border border-slate-400
                               dark:border-slate-500 bg-white
                               dark:bg-slate-800 rounded-sm"
                    >
                        <h3
                            class="mb-3 text-md font-semibold
                                   text-slate-800 dark:text-slate-100"
                        >
                            {{ t('attachedFiles') }}
                        </h3>

                        <div
                            class="divide-y divide-slate-200
                                   dark:divide-slate-600"
                        >
                            <div
                                v-for="file in files"
                                :key="file.id"
                                class="flex flex-col md:flex-row
                                       md:items-center md:justify-between
                                       gap-3 py-2"
                            >
                                <!-- Информация о файле -->
                                <div class="min-w-0">
                                    <div
                                        class="text-xs font-medium
                                               text-blue-700 dark:text-blue-300 break-all"
                                    >
                                        {{ file.original_name || `Файл #${file.id}` }}
                                    </div>

                                    <div
                                        class="mt-1 flex flex-wrap gap-x-3
                                               gap-y-1 text-[11px]
                                               text-slate-500 dark:text-slate-400"
                                                                >
                                        <span v-if="file.field_label">
                                            {{ file.field_label }}
                                        </span>

                                        <span v-if="file.mime_type">
                                            {{ file.mime_type }}
                                        </span>

                                        <span v-if="hasValue(file.size)">
                                            {{ formatFileSize(file.size) }}
                                        </span>

                                        <span v-if="file.extension">
                                            .{{ file.extension }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Скачать -->
                                <a
:href="route('admin.formSubmissions.files.download', { formSubmission: submissionData.id,formSubmissionFile: file.id, })"
                                    class="group inline-flex shrink-0 items-center justify-center
                                           gap-2 rounded-sm border border-blue-400 bg-blue-500
                                           px-3 py-1 text-xs font-medium text-white
                                           transition-all duration-200 ease-out
                                           hover:-translate-y-0.5 hover:border-blue-500
                                           hover:bg-blue-600 hover:shadow-md
                                           hover:shadow-blue-500/20 active:translate-y-0
                                           active:scale-95 dark:border-blue-400 dark:bg-blue-600
                                           dark:hover:bg-blue-500"
                                    :title="`Скачать ${file.original_name || 'файл'}`"
                                >
                                    <svg
                                        class="w-3.5 h-3.5 shrink-0 fill-current
                                               transition-transform duration-200
                                               group-hover:translate-y-0.5"
                                        viewBox="0 0 512 512"
                                    >
                                        <path
                                            d="M480 352h-133.5l-45.25 45.25C289.25 409.25 273 416 256 416s-33.25-6.75-45.25-18.75L165.5 352H32c-17.67 0-32 14.33-32 32v96c0 17.67 14.33 32 32 32h448c17.67 0 32-14.33 32-32v-96c0-17.67-14.33-32-32-32zM256 0c-17.67 0-32 14.33-32 32v196.7l-73.37-73.37c-12.5-12.5-32.76-12.5-45.26 0s-12.5 32.76 0 45.26l128 128c12.5 12.5 32.76 12.5 45.26 0l128-128c12.5-12.5 12.5-32.76 0-45.26s-32.76-12.5-45.26 0L288 228.7V32c0-17.67-14.33-32-32-32z"
                                        />
                                    </svg>

                                    <span>
                                        {{ t('download') }}
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- UTM / Техническая информация -->
                    <div
                        class="grid grid-cols-1 lg:grid-cols-2
                               gap-4 mb-4"
                    >
                        <!-- UTM -->
                        <div
                            class="p-3 border border-slate-400
                                   dark:border-slate-500 bg-white
                                   dark:bg-slate-800 rounded-sm"
                        >
                            <h3
                                class="mb-3 text-md font-semibold
                                       text-slate-800 dark:text-slate-100"
                            >
                                UTM-{{ t('data') }}
                            </h3>

                            <dl
                                v-if="hasUtm"
                                class="space-y-2 text-sm"
                            >
                                <div
                                    v-for="item in [
                                        ['Source', submissionData.utm_source],
                                        ['Medium', submissionData.utm_medium],
                                        ['Campaign', submissionData.utm_campaign],
                                        ['Content', submissionData.utm_content],
                                        ['Term', submissionData.utm_term],
                                    ]"
                                    :key="item[0]"
                                    class="flex justify-between gap-4
                                           border-b border-slate-300
                                           last:border-b-0
                                           dark:border-slate-600 pb-1"
                                >
                                    <dt class="text-slate-600 dark:text-slate-400">
                                        {{ item[0] }}
                                    </dt>

                                    <dd
                                        class="text-right break-all
                                               text-teal-700 dark:text-teal-300"
                                    >
                                        {{ item[1] || '—' }}
                                    </dd>
                                </div>
                            </dl>

                            <div
                                v-else
                                class="text-sm text-slate-500 dark:text-slate-400"
                            >
                                UTM-{{ t('dataNotAvailable') }}.
                            </div>
                        </div>

                        <!-- Технические данные -->
                        <div
                            class="p-3 border border-slate-400
                                   dark:border-slate-500 bg-white
                                   dark:bg-slate-800 rounded-sm"
                        >
                            <h3
                                class="mb-3 text-md font-semibold
                                       text-slate-800 dark:text-slate-100"
                            >
                                {{ t('serviceInformation') }}
                            </h3>

                            <dl class="space-y-2 text-sm">
                                <div
                                    class="flex justify-between gap-4
                                           border-b border-slate-300
                                           dark:border-slate-600 pb-1"
                                >
                                    <dt class="text-slate-600 dark:text-slate-400">
                                        IP
                                    </dt>

                                    <dd class="font-semibold text-teal-700 dark:text-teal-300">
                                        {{ submissionData.ip || '—' }}
                                    </dd>
                                </div>

                                <div
                                    class="flex justify-between gap-4
                                           border-b border-slate-300
                                           dark:border-slate-600 pb-1"
                                >
                                    <dt class="text-slate-600 dark:text-slate-400">
                                        Session ID
                                    </dt>

                                    <dd
                                        class="text-right break-all
                                               font-semibold text-teal-700 dark:text-teal-300"
                                    >
                                        {{ submissionData.session_id || '—' }}
                                    </dd>
                                </div>

                                <div
                                    class="flex flex-col gap-1
                                           border-b border-slate-300
                                           dark:border-slate-600 pb-2"
                                >
                                    <dt class="text-slate-600 dark:text-slate-400">
                                        Page URL
                                    </dt>

                                    <dd
                                        class="break-all font-semibold
                                               text-teal-700 dark:text-teal-300"
                                    >
                                        {{ submissionData.page_url || '—' }}
                                    </dd>
                                </div>

                                <div class="flex flex-col gap-1">
                                    <dt class="text-slate-600 dark:text-slate-400">
                                        User Agent
                                    </dt>

                                    <dd
                                        class="break-words text-xs
                                               font-semibold text-teal-700 dark:text-teal-300"
                                    >
                                        {{ submissionData.user_agent || '—' }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Context -->
                    <div
                        v-if="hasContext"
                        class="mb-4 p-3 border border-slate-400
                               dark:border-slate-500 bg-white
                               dark:bg-slate-800 rounded-sm"
                    >
                        <h3
                            class="mb-3 text-md font-semibold
                                   text-slate-800 dark:text-slate-100"
                        >
                            {{ t('context') }}
                        </h3>

                        <pre
                            class="p-3 overflow-x-auto rounded-sm
                                   border border-slate-300 dark:border-slate-700
                                   bg-slate-100 dark:bg-slate-900
                                   text-xs text-slate-700 dark:text-slate-300
                                   whitespace-pre-wrap break-words"
                        >{{ displayValue(submissionData.context) }}</pre>
                    </div>

                    <!-- Даты обработки -->
                    <div
                        class="mb-4 p-3 border border-slate-400
                               dark:border-slate-500 bg-white
                               dark:bg-slate-800 rounded-sm"
                    >
                        <h3
                            class="mb-3 text-md font-semibold
                                   text-slate-800 dark:text-slate-100"
                        >
                            {{ t('requestProcessing') }}
                        </h3>

                        <div
                            class="grid grid-cols-1 sm:grid-cols-2
                                   xl:grid-cols-5 gap-3"
                        >
                            <div>
                                <div
                                    class="text-[12px] uppercase text-slate-600 dark:text-slate-400"
                                >
                                    {{ t('sent') }}
                                </div>

                                <div
                                    class="mt-1 font-semibold text-xs
                                           text-cyan-700 dark:text-cyan-200"
                                >
                                    {{ formatDate(submissionData.submitted_at) }}
                                </div>
                            </div>

                            <div>
                                <div
                                    class="text-[12px] uppercase text-slate-600 dark:text-slate-400"
                                >
                                    {{ t('takenProcessing') }}
                                </div>

                                <div
                                    class="mt-1 font-semibold text-xs
                                           text-cyan-700 dark:text-cyan-200"
                                >
                                    {{ formatDate(submissionData.processed_at) }}
                                </div>
                            </div>

                            <div>
                                <div
                                    class="text-[12px] uppercase text-slate-600 dark:text-slate-400"
                                >
                                    {{ t('completedAt') }}
                                </div>

                                <div
                                    class="mt-1 font-semibold text-xs
                                           text-cyan-700 dark:text-cyan-200"
                                >
                                    {{ formatDate(submissionData.completed_at) }}
                                </div>
                            </div>

                            <div>
                                <div
                                    class="text-[12px] uppercase text-slate-600 dark:text-slate-400"
                                >
                                    {{ t('createdAt') }}
                                </div>

                                <div
                                    class="mt-1 font-semibold text-xs
                                           text-cyan-700 dark:text-cyan-200"
                                >
                                    {{ formatDate(submissionData.created_at) }}
                                </div>
                            </div>

                            <div>
                                <div
                                    class="text-[12px] uppercase text-slate-600 dark:text-slate-400"
                                >
                                    {{ t('updatedAt') }}
                                </div>

                                <div
                                    class="mt-1 font-semibold text-xs
                                           text-cyan-700 dark:text-cyan-200"
                                >
                                    {{ formatDate(submissionData.updated_at) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- История статусов -->
                    <div
                        class="mb-4 p-3 border border-slate-400
                               dark:border-slate-500 bg-white
                               dark:bg-slate-800 rounded-sm"
                    >
                        <div
                            class="mb-3 flex items-center
                                   justify-between gap-3"
                        >
                            <h3
                                class="flex flex-row items-center justify-center gap-3
                                       text-md font-semibold text-slate-800 dark:text-slate-100"
                            >
                                {{ t('statusHistory') }}

                                <span
                                    class="text-xs text-slate-500 dark:text-slate-400"
                                >
                                    {{ statusHistory.length }}
                                </span>
                            </h3>
                        </div>

                        <div
                            v-if="statusHistory.length"
                            class="overflow-x-auto"
                        >
                            <table
                                class="w-full text-sm text-left
                                       text-slate-600 dark:text-slate-300"
                            >
                                <thead
                                    class="text-xs uppercase
                                           bg-slate-100 dark:bg-slate-700
                                           border border-slate-300 dark:border-slate-600
                                           text-slate-600 dark:text-slate-300"
                                >
                                <tr>
                                    <th class="px-3 py-2">
                                        {{ t('status') }}
                                    </th>

                                    <th class="px-3 py-2">
                                        {{ t('user') }}
                                    </th>

                                    <th class="px-3 py-2">
                                        {{ t('source') }}
                                    </th>

                                    <th class="px-3 py-2">
                                        {{ t('comment') }}
                                    </th>

                                    <th class="px-3 py-2 text-right">
                                        {{ t('date') }}
                                    </th>
                                </tr>
                                </thead>

                                <tbody>
                                <tr
                                    v-for="item in statusHistory"
                                    :key="item.id"
                                    class="border-b border-slate-300
                                               dark:border-slate-600"
                                >
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <div
                                            class="flex items-center
                                                       gap-1.5"
                                        >
                                            <span
                                                v-if="item.from_status"
                                                class="text-xs text-slate-700 dark:text-slate-300"
                                            >
                                                {{ statusLabel(item.from_status) }}
                                            </span>

                                            <span
                                                v-if="item.from_status"
                                                class="text-xs text-slate-700 dark:text-slate-300"
                                            >
                                                →
                                            </span>

                                            <span
                                                class="font-medium text-xs
                                                       text-slate-700 dark:text-slate-300"
                                            >
                                                {{ statusLabel(item.to_status) }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="px-3 py-2 font-semibold text-xs
                                               text-indigo-700 dark:text-indigo-300">
                                        {{ historyUserName(item) }}
                                    </td>

                                    <td class="px-3 py-2 font-semibold text-xs
                                               text-teal-700 dark:text-teal-300">
                                        {{ item.source || '—' }}
                                    </td>

                                    <td
                                        class="px-3 py-2 font-semibold text-xs whitespace-pre-wrap"
                                    >
                                        {{ item.comment || '—' }}
                                    </td>

                                    <td
                                        class="px-3 py-2 font-semibold text-xs text-right
                                               whitespace-nowrap text-blue-700 dark:text-blue-300"
                                    >
                                        {{ formatDate(item.changed_at || item.created_at) }}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div
                            v-else
                            class="py-3 text-center text-sm
                                   text-slate-600 dark:text-slate-400"
                        >
                            {{ t('dataNotAvailable') }}.
                        </div>
                    </div>

                    <!-- Кнопки -->
                    <div
                        class="flex items-center justify-center
                               mt-5 gap-3"
                    >
                        <DefaultButton
                            :href="route('admin.formSubmissions.index')"
                        >
                            <template #icon>
                                <svg
                                    class="w-4 h-4 fill-current
                                           text-slate-100 shrink-0 mr-2"
                                    viewBox="0 0 16 16"
                                >
                                    <path
                                        d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z"
                                    />
                                </svg>
                            </template>

                            {{ t('back') }}
                        </DefaultButton>

                        <PrimaryButton
                            type="submit"
                            :disabled="form.processing"
                            :class="{ 'opacity-25': form.processing }"
                        >
                            {{ t('save') }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
