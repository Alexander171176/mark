<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Просмотр заявки динамической формы.
 */

import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import PrimaryButton from '@/Components/Admin/UI/Buttons/PrimaryButton.vue'

const { t } = useI18n()

/* ===================== Props ===================== */

const props = defineProps({
    submission: {
        type: Object,
        required: true
    },
    statuses: {
        type: [Array, Object],
        default: () => ({})
    }
})

/* ===================== Resource ===================== */

const submissionData = computed(() => props.submission?.data || props.submission || {})

const resourceList = (value) => {
    if (Array.isArray(value?.data)) return value.data
    return Array.isArray(value) ? value : []
}

const values = computed(() => resourceList(submissionData.value?.values))
const files = computed(() => resourceList(submissionData.value?.files))
const statusHistory = computed(() => resourceList(submissionData.value?.status_history))

/* ===================== Labels ===================== */

const formTitle = computed(() => {
    return submissionData.value?.form?.translation?.title
        || submissionData.value?.form?.title
        || submissionData.value?.form?.code
        || `ID: ${submissionData.value?.form_id ?? ''}`
})

const pageTitle = computed(() => `${formTitle.value} [ID: ${submissionData.value?.id ?? ''}]`)
const senderName = computed(() => submissionData.value?.user?.name || 'Гость')
const senderEmail = computed(() => submissionData.value?.user?.email || '')
const assignedUser = computed(() => submissionData.value?.assigned_user || submissionData.value?.assignedUser || null)

/* ===================== Status ===================== */

const statusTranslationMap = {
    new: 'statusNew',
    processing: 'statusProcessing',
    completed: 'statusCompleted',
    cancelled: 'statusCancelled',
    spam: 'statusSpam'
}

const statusLabel = (status) => {
    const translationKey = statusTranslationMap[status]

    if (translationKey) return t(translationKey)

    const configured = props.statuses?.[status]

    if (typeof configured === 'string') return configured
    if (configured?.label) return configured.label

    return status || '—'
}

const statusClass = (status) => {
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
        || 'bg-slate-100 text-slate-700 border border-slate-300 dark:bg-slate-700 dark:text-slate-200 dark:border-slate-500'
}

/* ===================== Helpers ===================== */

const formatDate = (value) => {
    if (!value) return '—'

    const date = new Date(value)

    if (Number.isNaN(date.getTime())) return value

    return new Intl.DateTimeFormat('ru-RU', {
        dateStyle: 'medium',
        timeStyle: 'short'
    }).format(date)
}

const hasValue = (value) => value !== null && value !== undefined && value !== ''

const displayValue = (value) => {
    if (!hasValue(value)) return '—'
    if (Array.isArray(value)) return value.join(', ')
    if (typeof value === 'object') return JSON.stringify(value, null, 2)

    return String(value)
}

const submissionValue = (item) => {
    if (hasValue(item?.display_value)) return displayValue(item.display_value)
    if (hasValue(item?.value_json)) return displayValue(item.value_json)

    return displayValue(item?.value)
}

const submissionValueLabel = (item) => {
    return item?.field_label || item?.field_name || `Поле #${item?.form_field_id ?? ''}`
}

const formatFileSize = (bytes) => {
    const size = Number(bytes)

    if (!Number.isFinite(size) || size < 0) return '—'
    if (size < 1024) return `${size} Б`
    if (size < 1024 * 1024) return `${(size / 1024).toFixed(1)} КБ`

    return `${(size / 1024 / 1024).toFixed(2)} МБ`
}

const historyUserName = (item) => {
    return item?.user?.name || (item?.user_id ? `User #${item.user_id}` : 'Система')
}

const hasUtm = computed(() => [
    submissionData.value?.utm_source,
    submissionData.value?.utm_medium,
    submissionData.value?.utm_campaign,
    submissionData.value?.utm_content,
    submissionData.value?.utm_term
].some(hasValue))

const hasContext = computed(() => {
    const context = submissionData.value?.context

    if (!context) return false
    if (typeof context === 'object') return Object.keys(context).length > 0

    return true
})

/* ===================== Navigation ===================== */

const editUrl = computed(() => route('admin.formSubmissions.edit', {
    formSubmission: submissionData.value.id
}))

const formUrl = computed(() => {
    if (!submissionData.value?.form_id) return null

    return route('admin.forms.show', {
        form: submissionData.value.form_id
    })
})
</script>

<template>
    <AdminLayout :title="pageTitle">
        <template #header>
            <TitlePage>
                {{ t('viewSubmission') }}: {{ formTitle }} [ID: {{ submissionData.id }}]
            </TitlePage>
        </template>

        <div class="px-2 py-2 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95">

                <!-- Верхняя панель -->
                <div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3">
                    <DefaultButton :href="route('admin.formSubmissions.index')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current text-slate-100 shrink-0"
                                 viewBox="0 0 16 16">
                                <path
                                    d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z" />
                            </svg>
                        </template>
                        {{ t('back') }}
                    </DefaultButton>
                    <PrimaryButton :href="editUrl">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current text-slate-100 shrink-0"
                                 viewBox="0 0 16 16">
                                <path
                                    d="M11.7.3c-.4-.4-1-.4-1.4 0l-10 10c-.2.2-.3.4-.3.7v4c0 .6.4 1 1 1h4c.3 0 .5-.1.7-.3l10-10c.4-.4.4-1 0-1.4l-4-4zM4.6 14H2v-2.6l6-6L10.6 8l-6 6zM12 6.6L9.4 4 11 2.4 13.6 5 12 6.6z" />
                            </svg>
                        </template>
                        {{ t('edit') }}
                    </PrimaryButton>
                </div>

                <!-- Краткая информация -->
                <section
                    class="mb-5 p-4 rounded-md border border-slate-400 dark:border-slate-500
                           bg-white dark:bg-slate-800">
                    <div
                        class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                        <div>
                            <h2
                                class="text-lg font-semibold text-slate-900 dark:text-slate-100">
                                {{ formTitle }}
                            </h2>

                            <div class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                                <span class="font-semibold text-amber-800 dark:text-amber-200">
                                    {{ t('submission') }} #{{ submissionData.id }}
                                </span>
                                <span v-if="submissionData.source">
                                    · {{ submissionData.source }}
                                </span>
                                <span v-if="submissionData.locale">
                                    · {{ submissionData.locale.toUpperCase() }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center flex-wrap gap-2">
                            <span
                                class="font-semibold text-xs
                                       text-indigo-600 dark:text-indigo-200">
                                {{ formatDate(submissionData.submitted_at) }}
                            </span>
                            <span
                                class="px-3 py-1 rounded-full text-xs font-semibold
                                       bg-slate-100 text-slate-700 border
                                       border-slate-300 dark:bg-slate-700
                                       dark:text-slate-200 dark:border-slate-500">
                                {{ assignedUser?.name || t('notAssigned') }}
                            </span>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold"
                                  :class="statusClass(submissionData.status)">
                                {{ statusLabel(submissionData.status) }}
                            </span>
                        </div>
                    </div>
                </section>

                <!-- Основные данные -->
                <section class="mb-5">
                    <h3 class="mb-3 px-1 text-md font-semibold text-slate-900 dark:text-slate-100">
                        {{ t('mainData') }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3">
                        <div
                            class="p-3 rounded-md border border-slate-400 dark:border-slate-500
                                   bg-white dark:bg-slate-800">
                            <div
                                class="text-sm font-semibold uppercase tracking-wide
                                       text-slate-500 dark:text-slate-400">
                                {{ t('submission') }} ID
                            </div>
                            <div
                                class="mt-1 text-sm font-semibold
                                       text-indigo-800 dark:text-indigo-200">
                                {{ submissionData.id }}
                            </div>
                        </div>

                        <div
                            class="p-3 rounded-md border border-slate-400 dark:border-slate-500
                                   bg-white dark:bg-slate-800">
                            <div
                                class="text-sm font-semibold uppercase tracking-wide
                                       text-slate-500 dark:text-slate-400">
                                {{ t('form') }} ID
                            </div>
                            <div
                                class="mt-1 text-sm font-semibold
                                       text-indigo-800 dark:text-indigo-200">
                                {{ submissionData.form_id }}
                            </div>
                        </div>

                        <div
                            class="p-3 rounded-md border border-slate-400 dark:border-slate-500
                                   bg-white dark:bg-slate-800">
                            <div
                                class="text-sm font-semibold uppercase tracking-wide
                                       text-slate-500 dark:text-slate-400">
                                {{ t('form') }}
                            </div>
                            <div
                                class="mt-1 text-sm font-semibold
                                       text-indigo-800 dark:text-indigo-200">
                                {{ formTitle }}
                            </div>
                        </div>

                        <div
                            class="p-3 rounded-md border border-slate-400 dark:border-slate-500
                                   bg-white dark:bg-slate-800">
                            <div
                                class="text-sm font-semibold uppercase tracking-wide
                                       text-slate-500 dark:text-slate-400">
                                {{ t('status') }}
                            </div>
                            <div
                                class="mt-1 text-sm font-semibold
                                       text-indigo-800 dark:text-indigo-200">
                                {{ statusLabel(submissionData.status) }}
                            </div>
                        </div>

                        <div
                            class="p-3 rounded-md border border-slate-400 dark:border-slate-500
                                   bg-white dark:bg-slate-800">
                            <div
                                class="text-sm font-semibold uppercase tracking-wide
                                       text-slate-500 dark:text-slate-400">
                                {{ t('locale') }}
                            </div>
                            <div
                                class="mt-1 text-sm font-semibold
                                       text-indigo-800 dark:text-indigo-200 uppercase">
                                {{ submissionData.locale || '—' }}
                            </div>
                        </div>

                        <div
                            class="p-3 rounded-md border border-slate-400 dark:border-slate-500
                                   bg-white dark:bg-slate-800">
                            <div
                                class="text-sm font-semibold uppercase tracking-wide
                                       text-slate-500 dark:text-slate-400">
                                {{ t('source') }}
                            </div>
                            <div
                                class="mt-1 text-sm font-semibold
                                       text-indigo-800 dark:text-indigo-200">
                                {{ submissionData.source || '—' }}
                            </div>
                        </div>

                        <div
                            class="p-3 rounded-md border border-slate-400 dark:border-slate-500
                                   bg-white dark:bg-slate-800">
                            <div
                                class="text-sm font-semibold uppercase tracking-wide
                                       text-slate-500 dark:text-slate-400">
                                {{ t('fields') }}
                            </div>
                            <div
                                class="mt-1 text-sm font-semibold
                                       text-indigo-800 dark:text-indigo-200">
                                {{ submissionData.values_count ?? values.length }}
                            </div>
                        </div>

                        <div
                            class="p-3 rounded-md border border-slate-400 dark:border-slate-500
                                   bg-white dark:bg-slate-800">
                            <div
                                class="text-sm font-semibold uppercase tracking-wide
                                       text-slate-500 dark:text-slate-400">
                                {{ t('files') }}
                            </div>
                            <div
                                class="mt-1 text-sm font-semibold
                                       text-indigo-800 dark:text-indigo-200">
                                {{ submissionData.files_count ?? files.length }}
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Отправитель / Ответственный -->
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-5 mb-5">
                    <section>
                        <h3
                            class="mb-3 px-1 text-md font-semibold
                                   text-slate-900 dark:text-slate-100">
                            {{ t('sender') }}
                        </h3>

                        <div
                            class="p-4 rounded-md border border-slate-400 dark:border-slate-500
                                   bg-white dark:bg-slate-800">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 font-semibold">
                                <div>
                                    <div
                                        class="text-xs uppercase tracking-wide
                                               text-slate-500 dark:text-slate-400">
                                        {{ t('type') }}
                                    </div>
                                    <div class="mt-1 text-sm text-teal-700 dark:text-teal-300">
                                        {{ submissionData.user_id ? t('authorizedUser') : t('guest') }}
                                    </div>
                                </div>

                                <div>
                                    <div
                                        class="text-xs uppercase tracking-wide
                                               text-slate-500 dark:text-slate-400">
                                        User ID
                                    </div>
                                    <div class="mt-1 text-sm text-teal-700 dark:text-teal-300">
                                        {{ submissionData.user_id || '—' }}
                                    </div>
                                </div>

                                <div>
                                    <div
                                        class="text-xs uppercase tracking-wide
                                               text-slate-500 dark:text-slate-400">
                                        {{ t('name') }}
                                    </div>
                                    <div class="mt-1 text-sm text-teal-700 dark:text-teal-300">
                                        {{ senderName }}
                                    </div>
                                </div>

                                <div>
                                    <div
                                        class="text-xs uppercase tracking-wide
                                               text-slate-500 dark:text-slate-400">
                                        Email
                                    </div>
                                    <div class="mt-1 text-sm text-teal-700 dark:text-teal-300 break-all">
                                        {{ senderEmail || '—' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section>
                        <h3
                            class="mb-3 px-1 text-md font-semibold
                                   text-slate-900 dark:text-slate-100">
                            {{ t('assignedUser') }}
                        </h3>

                        <div
                            class="p-4 rounded-md border border-slate-400 dark:border-slate-500
                                   bg-white dark:bg-slate-800">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 font-semibold">
                                <div>
                                    <div
                                        class="text-xs uppercase tracking-wide
                                               text-slate-500 dark:text-slate-400">
                                        ID
                                    </div>
                                    <div class="mt-1 text-sm text-teal-700 dark:text-teal-300">
                                        {{ assignedUser?.id || submissionData.assigned_user_id || '—' }}
                                    </div>
                                </div>

                                <div>
                                    <div
                                        class="text-xs uppercase tracking-wide
                                               text-slate-500 dark:text-slate-400">
                                        {{ t('state') }}
                                    </div>
                                    <div class="mt-1 text-sm text-teal-700 dark:text-teal-300">
                                        {{ assignedUser ? t('appointed') : t('notAssigned') }}
                                    </div>
                                </div>

                                <div>
                                    <div
                                        class="text-xs uppercase tracking-wide
                                               text-slate-500 dark:text-slate-400">
                                        {{ t('name') }}
                                    </div>
                                    <div class="mt-1 text-sm text-teal-700 dark:text-teal-300">
                                        {{ assignedUser?.name || '—' }}
                                    </div>
                                </div>

                                <div>
                                    <div
                                        class="text-xs uppercase tracking-wide
                                               text-slate-500 dark:text-slate-400">
                                        Email
                                    </div>
                                    <div class="mt-1 text-sm text-teal-700 dark:text-teal-300
                                                break-all">
                                        {{ assignedUser?.email || '—' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Данные формы -->
                <section class="mb-5">
                    <div class="flex items-center justify-between gap-3 mb-3">
                        <h3 class="flex items-center justify-between gap-3
                                   px-1 text-md font-semibold text-slate-900 dark:text-slate-100">
                            {{ t('data') }}
                            <span
                                class="px-2 py-0.5 rounded-full bg-slate-200 dark:bg-slate-900
                                       border border-slate-300 dark:border-slate-600
                                       text-xs text-slate-700 dark:text-slate-300">
                                {{ values.length }}
                            </span>
                        </h3>
                    </div>

                    <div
                        v-if="values.length"
                        class="rounded-md border border-slate-400 dark:border-slate-500
                               bg-white dark:bg-slate-800 overflow-hidden">
                        <div
                            v-for="item in values"
                            :key="item.id"
                            class="grid grid-cols-1 md:grid-cols-3 gap-2
                                   md:gap-4 p-2 border-b border-slate-400 dark:border-slate-600
                                   last:border-b-0"
                        >
                            <div>
                                <div class="font-semibold text-slate-800 dark:text-slate-100">
                                    {{ submissionValueLabel(item) }}
                                </div>
                                <div
                                    v-if="item.field_name"
                                    class="mt-1 text-xs font-mono
                                           text-slate-500 dark:text-slate-400">
                                    {{ item.field_name }}
                                </div>
                                <div
                                    v-if="item.field_type"
                                    class="mt-1 text-xs text-slate-400">
                                    {{ item.field_type }}
                                </div>
                            </div>

                            <div
                                class="md:col-span-2 whitespace-pre-wrap break-words
                                       text-sm font-semibold text-indigo-800 dark:text-indigo-200">
                                {{ submissionValue(item) }}
                            </div>
                        </div>
                    </div>

                    <div
                        v-else
                        class="p-4 text-center rounded-md border border-slate-400
                               dark:border-slate-500 text-slate-600 dark:text-slate-400">
                        {{ t('dataNotAvailable') }}
                    </div>
                </section>

                <!-- Файлы -->
                <section class="mb-5">
                    <div class="flex items-center justify-between gap-3 mb-3">
                        <h3 class="px-1 text-md font-semibold text-slate-900 dark:text-slate-100">
                            {{ t('attachedFiles') }}
                        </h3>
                        <span
                            class="px-3 py-0.5 rounded-full bg-slate-200
                                   dark:bg-slate-900 border border-slate-300 dark:border-slate-600
                                   text-sm text-slate-700 dark:text-slate-300">
                            {{ files.length }}
                        </span>
                    </div>

                    <div
                        v-if="files.length"
                        class="rounded-md border border-slate-400 dark:border-slate-500
                               bg-white dark:bg-slate-800 overflow-hidden">
                        <div
                            v-for="file in files"
                            :key="file.id"
                            class="flex flex-col md:flex-row md:items-center
                                   md:justify-between gap-3 p-2 border-b border-slate-400
                                   dark:border-slate-600 last:border-b-0"
                        >
                            <div class="min-w-0">
                                <div class="font-semibold text-blue-800 dark:text-blue-100
                                            text-sm break-all">
                                    {{ file.original_name || `Файл #${file.id}` }}
                                </div>

                                <div
                                    v-if="file.field_label"
                                    class="mt-1 text-xs text-slate-600 dark:text-slate-300">
                                    {{ file.field_label }}
                                </div>

                                <div class="mt-1 flex flex-wrap gap-x-3 gap-y-1
                                            text-xs text-slate-500 dark:text-slate-400">
                                    <span v-if="file.mime_type">{{ file.mime_type }}</span>
                                    <span v-if="hasValue(file.size)">
                                        {{ formatFileSize(file.size) }}
                                    </span>
                                    <span v-if="file.extension">.{{ file.extension }}</span>
                                </div>
                            </div>

                            <a
                                :href="route('admin.formSubmissions.files.download', {
                                    formSubmission: submissionData.id,
                                    formSubmissionFile: file.id,
                                })"
                                class="group inline-flex shrink-0 items-center justify-center
                                       gap-2 rounded-sm border border-blue-400 bg-blue-500
                                       px-3 py-1 text-xs font-medium text-white
                                       transition-all duration-200 ease-out
                                       hover:-translate-y-0.5 hover:border-blue-500
                                       hover:bg-blue-600 hover:shadow-md hover:shadow-blue-500/20
                                       active:translate-y-0 active:scale-95 dark:border-blue-400
                                       dark:bg-blue-600 dark:hover:bg-blue-500"
                                :title="`Скачать ${file.original_name || 'файл'}`"
                            >
                                <svg
                                    class="w-3.5 h-3.5 shrink-0 fill-current
                                           transition-transform duration-200
                                           group-hover:translate-y-0.5"
                                    viewBox="0 0 512 512">
                                    <path
                                        d="M480 352h-133.5l-45.25 45.25C289.25 409.25 273 416 256 416s-33.25-6.75-45.25-18.75L165.5 352H32c-17.67 0-32 14.33-32 32v96c0 17.67 14.33 32 32 32h448c17.67 0 32-14.33 32-32v-96c0-17.67-14.33-32-32-32zM256 0c-17.67 0-32 14.33-32 32v196.7l-73.37-73.37c-12.5-12.5-32.76-12.5-45.26 0s-12.5 32.76 0 45.26l128 128c12.5 12.5 32.76 12.5 45.26 0l128-128c12.5-12.5 12.5-32.76 0-45.26s-32.76-12.5-45.26 0L288 228.7V32c0-17.67-14.33-32-32-32z" />
                                </svg>
                                <span>{{ t('download') }}</span>
                            </a>
                        </div>
                    </div>

                    <div
                        v-else class="p-4 text-center rounded-md border border-slate-400
                                      dark:border-slate-500 text-slate-600 dark:text-slate-400">
                        {{ t('filesAreMissing') }}.
                    </div>
                </section>

                <!-- UTM / Техническая информация -->
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-5 mb-5">
                    <section>
                        <h3
                            class="mb-3 px-1 text-md font-semibold
                                   text-slate-900 dark:text-slate-100"
                        >
                            UTM-{{ t('data') }}
                        </h3>

                        <div
                            class="p-2 rounded-md border border-slate-400 dark:border-slate-500
                                   bg-white dark:bg-slate-800">
                            <div
                                v-if="hasUtm"
                                class="grid grid-cols-1 sm:grid-cols-2 gap-4 font-semibold">
                                <div>
                                    <div
                                        class="text-sm font-semibold uppercase tracking-wide
                                               text-slate-500 dark:text-slate-400">
                                        {{ t('source') }}
                                    </div>
                                    <div class="mt-1 text-sm text-cyan-700 dark:text-cyan-300
                                                break-all">
                                        {{ submissionData.utm_source || '—' }}
                                    </div>
                                </div>

                                <div>
                                    <div
                                        class="text-sm font-semibold uppercase tracking-wide
                                               text-slate-500 dark:text-slate-400">
                                        Medium
                                    </div>
                                    <div class="mt-1 text-sm text-cyan-700 dark:text-cyan-300
                                                break-all">
                                        {{ submissionData.utm_medium || '—' }}
                                    </div>
                                </div>

                                <div>
                                    <div
                                        class="text-sm font-semibold uppercase tracking-wide
                                               text-slate-500 dark:text-slate-400">
                                        Campaign
                                    </div>
                                    <div class="mt-1 text-sm text-cyan-700 dark:text-cyan-300
                                                break-all">
                                        {{ submissionData.utm_campaign || '—' }}
                                    </div>
                                </div>

                                <div>
                                    <div
                                        class="text-sm font-semibold uppercase tracking-wide
                                               text-slate-500 dark:text-slate-400">
                                        Content
                                    </div>
                                    <div class="mt-1 text-sm text-cyan-700 dark:text-cyan-300
                                                break-all">
                                        {{ submissionData.utm_content || '—' }}
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <div
                                        class="text-sm font-semibold uppercase tracking-wide
                                               text-slate-500 dark:text-slate-400">
                                        Term
                                    </div>
                                    <div class="mt-1 text-sm text-cyan-700 dark:text-cyan-300
                                                break-all">
                                        {{ submissionData.utm_term || '—' }}
                                    </div>
                                </div>
                            </div>

                            <div v-else class="text-sm text-slate-500 dark:text-slate-400">
                                UTM-{{ t('dataNotAvailable') }}.
                            </div>
                        </div>
                    </section>

                    <section>
                        <h3
                            class="mb-3 px-1 text-md font-semibold
                                   text-slate-900 dark:text-slate-100"
                        >
                            {{ t('serviceInformation') }}
                        </h3>

                        <div
                            class="p-2 rounded-md border border-slate-400 dark:border-slate-500
                                   bg-white dark:bg-slate-800">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 font-semibold">
                                <div>
                                    <div
                                        class="text-sm font-semibold uppercase tracking-wide
                                               text-slate-500 dark:text-slate-400">
                                        IP
                                    </div>
                                    <div class="mt-1 text-sm text-cyan-700 dark:text-cyan-300">
                                        {{ submissionData.ip || '—' }}
                                    </div>
                                </div>

                                <div>
                                    <div
                                        class="text-sm font-semibold uppercase tracking-wide
                                               text-slate-500 dark:text-slate-400">
                                        Session ID
                                    </div>
                                    <div
                                        class="mt-1 text-sm text-cyan-700 dark:text-cyan-300
                                               break-all">
                                        {{ submissionData.session_id || '—' }}
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <div
                                        class="text-sm font-semibold uppercase tracking-wide
                                               text-slate-500 dark:text-slate-400">
                                        Page URL
                                    </div>
                                    <div
                                        class="mt-1 text-sm text-cyan-700 dark:text-cyan-300
                                               break-all">
                                        {{ submissionData.page_url || '—' }}
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <div
                                        class="text-sm font-semibold uppercase tracking-wide
                                               text-slate-500 dark:text-slate-400">
                                        User Agent
                                    </div>
                                    <div class="mt-1 text-sm text-cyan-700 dark:text-cyan-300
                                                break-words">
                                        {{ submissionData.user_agent || '—' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Контекст -->
                <section
                    v-if="hasContext"
                    class="mb-5">
                    <h3
                        class="mb-3 px-1 text-md font-semibold text-slate-900 dark:text-slate-100">
                        {{ t('context') }}
                    </h3>
                    <pre
                        class="p-2 rounded-md border border-slate-400 dark:border-slate-500
                               bg-white dark:bg-slate-900 text-sm
                               text-gray-800 dark:text-gray-200 overflow-x-auto
                               whitespace-pre-wrap break-words">{{ displayValue(submissionData.context) }}</pre>
                </section>

                <!-- Обработка -->
                <section class="mb-5">
                    <h3
                        class="mb-3 px-1 text-md font-semibold text-slate-900 dark:text-slate-100">
                        {{ t('requestProcessing') }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5
                                gap-3 font-semibold">
                        <div
                            class="p-3 rounded-md border border-slate-400 dark:border-slate-500
                                   bg-white dark:bg-slate-800">
                            <div
                                class="text-sm uppercase tracking-wide
                                       text-slate-500 dark:text-slate-400">
                                {{ t('sent') }}
                            </div>
                            <div class="mt-1 text-sm text-blue-800 dark:text-blue-200">
                                {{ formatDate(submissionData.submitted_at) }}
                            </div>
                        </div>

                        <div
                            class="p-3 rounded-md border border-slate-400 dark:border-slate-500
                                   bg-white dark:bg-slate-800">
                            <div
                                class="text-sm uppercase tracking-wide
                                       text-slate-500 dark:text-slate-400">
                                {{ t('takenProcessing') }}
                            </div>
                            <div class="mt-1 text-sm text-blue-800 dark:text-blue-200">
                                {{ formatDate(submissionData.processed_at) }}
                            </div>
                        </div>

                        <div
                            class="p-3 rounded-md border border-slate-400 dark:border-slate-500
                                   bg-white dark:bg-slate-800">
                            <div
                                class="text-sm uppercase tracking-wide
                                       text-slate-500 dark:text-slate-400">
                                {{ t('completedAt') }}
                            </div>
                            <div class="mt-1 text-sm text-blue-800 dark:text-blue-200">
                                {{ formatDate(submissionData.completed_at) }}
                            </div>
                        </div>

                        <div
                            class="p-3 rounded-md border border-slate-400 dark:border-slate-500
                                   bg-white dark:bg-slate-800">
                            <div
                                class="text-sm uppercase tracking-wide
                                       text-slate-500 dark:text-slate-400">
                                {{ t('createdAt') }}
                            </div>
                            <div class="mt-1 text-sm text-blue-800 dark:text-blue-200">
                                {{ formatDate(submissionData.created_at) }}
                            </div>
                        </div>

                        <div
                            class="p-3 rounded-md border border-slate-400 dark:border-slate-500
                                   bg-white dark:bg-slate-800">
                            <div
                                class="text-sm uppercase tracking-wide
                                       text-slate-500 dark:text-slate-400">
                                {{ t('updatedAt') }}
                            </div>
                            <div class="mt-1 text-sm text-blue-800 dark:text-blue-200">
                                {{ formatDate(submissionData.updated_at) }}
                            </div>
                        </div>
                    </div>
                </section>

                <!-- История статусов -->
                <section class="mb-5">
                    <div class="flex items-center justify-between gap-3 mb-3">
                        <h3
                            class="flex flex-row items-center justify-center gap-3
                                   px-1 text-md font-semibold text-slate-900 dark:text-slate-100">
                            {{ t('statusHistory') }}
                            <span
                                class="px-2 py-0.5 rounded-full bg-slate-200 dark:bg-slate-900
                                       border border-slate-300 dark:border-slate-600 text-xs
                                       text-slate-700 dark:text-slate-300">
                            {{ statusHistory.length }}
                        </span>
                        </h3>
                    </div>

                    <div
                        v-if="statusHistory.length"
                        class="overflow-x-auto rounded-md border border-slate-400
                               dark:border-slate-500 bg-white dark:bg-slate-800">
                        <table class="w-full text-sm text-left text-slate-600 dark:text-slate-300">
                            <thead
                                class="text-xs uppercase bg-slate-100 dark:bg-slate-900
                                       text-slate-600 dark:text-slate-300">
                                <tr>
                                    <th class="px-3 py-2">{{ t('status') }}</th>
                                    <th class="px-3 py-2">{{ t('user') }}</th>
                                    <th class="px-3 py-2">{{ t('source') }}</th>
                                    <th class="px-3 py-2">{{ t('comment') }}</th>
                                    <th class="px-3 py-2 text-right">{{ t('date') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr
                                    v-for="item in statusHistory"
                                    :key="item.id"
                                    class="border-b border-slate-200
                                           dark:border-slate-700 last:border-0"
                                >
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <div class="flex items-center gap-1.5">
                                                <span
                                                    v-if="item.from_status"
                                                    class="text-slate-500 dark:text-slate-400">
                                                    {{ statusLabel(item.from_status) }}
                                                </span>
                                            <span
                                                v-if="item.from_status"
                                                class="text-slate-400">
                                                →
                                            </span>
                                            <span
                                                class="font-medium text-slate-800 dark:text-slate-100">
                                                {{ statusLabel(item.to_status) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ historyUserName(item) }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ item.source || '—' }}
                                    </td>
                                    <td class="px-3 py-2 whitespace-pre-wrap">
                                        {{ item.comment || '—' }}
                                    </td>
                                    <td class="px-3 py-2 text-right whitespace-nowrap">
                                        {{ formatDate(item.changed_at || item.created_at) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-else
                         class="p-4 text-center rounded-md border border-slate-400
                                dark:border-slate-500 text-slate-600 dark:text-slate-400">
                        {{ t('dataNotAvailable') }}.
                    </div>
                </section>

                <!-- Связанная форма -->
                <section v-if="formUrl" class="mb-5">
                    <h3
                        class="mb-3 px-1 text-md font-semibold text-slate-900 dark:text-slate-100">
                        {{ t('boundForm') }}
                    </h3>

                    <div
                        class="p-2 rounded-md border border-slate-400 dark:border-slate-500
                               bg-white dark:bg-slate-800 flex flex-col sm:flex-row
                               sm:items-center sm:justify-between gap-3">
                        <div>
                            <div class="font-semibold text-slate-900 dark:text-slate-100">
                                {{ formTitle }}
                            </div>
                            <div class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                ID: {{ submissionData.form_id }}
                                <template v-if="submissionData.form?.code">
                                    · {{ submissionData.form.code }}
                                </template>
                            </div>
                        </div>

                        <DefaultButton :href="formUrl">
                            <template #icon>
                                <svg class="w-4 h-4 fill-current text-slate-100 shrink-0"
                                     viewBox="0 0 576 512">
                                    <path
                                        d="M569.354 231.631C512.969 135.949 407.81 72 288 72 168.14 72 63.004 135.994 6.646 231.631a47.999 47.999 0 0 0 0 48.739C63.031 376.051 168.19 440 288 440c119.86 0 224.996-63.994 281.354-159.631a47.997 47.997 0 0 0 0-48.738zM288 392c-75.162 0-136-60.827-136-136 0-75.162 60.826-136 136-136 75.162 0 136 60.826 136 136 0 75.162-60.826 136-136 136zm104-136c0 57.438-46.562 104-104 104s-104-46.562-104-104c0-17.708 4.431-34.379 12.236-48.973l-.001.032c0 23.651 19.173 42.823 42.824 42.823s42.824-19.173 42.824-42.823c0-23.651-19.173-42.824-42.824-42.824l-.032.001C253.621 156.431 270.292 152 288 152c57.438 0 104 46.562 104 104z" />
                                </svg>
                            </template>
                            {{ t('viewForm') }}
                        </DefaultButton>
                    </div>
                </section>

                <!-- Нижние кнопки -->
                <div class="flex flex-wrap justify-center gap-3 mt-6">
                    <DefaultButton :href="route('admin.formSubmissions.index')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current text-slate-100 shrink-0"
                                 viewBox="0 0 16 16">
                                <path
                                    d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z" />
                            </svg>
                        </template>
                        {{ t('back') }}
                    </DefaultButton>
                    <PrimaryButton :href="editUrl">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current text-slate-100 shrink-0"
                                 viewBox="0 0 16 16">
                                <path
                                    d="M11.7.3c-.4-.4-1-.4-1.4 0l-10 10c-.2.2-.3.4-.3.7v4c0 .6.4 1 1 1h4c.3 0 .5-.1.7-.3l10-10c.4-.4.4-1 0-1.4l-4-4zM4.6 14H2v-2.6l6-6L10.6 8l-6 6zM12 6.6L9.4 4 11 2.4 13.6 5 12 6.6z" />
                            </svg>
                        </template>
                        {{ t('edit') }}
                    </PrimaryButton>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
