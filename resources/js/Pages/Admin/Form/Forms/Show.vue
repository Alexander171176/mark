<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Просмотр динамической формы.
 *
 * Страница отображает:
 * - основные данные формы;
 * - переводы;
 * - настройки поведения;
 * - защиту от спама;
 * - дополнительные настройки;
 * - поля формы;
 * - варианты значений полей;
 * - владельца;
 * - служебную информацию.
 */

import { computed, ref } from 'vue'
import { useI18n } from 'vue-i18n'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'

const { t } = useI18n()

const props = defineProps({
    form: {
        type: Object,
        required: true
    },

    currentLocale: {
        type: String,
        default: ''
    },

    availableLocales: {
        type: Array,
        default: () => []
    },

    statuses: {
        type: Object,
        default: () => ({})
    },

    fieldTypes: {
        type: Object,
        default: () => ({})
    },

    fieldWidths: {
        type: Object,
        default: () => ({})
    }
})

/* ===================== Resource ===================== */

const formData = computed(() => {
    return props.form?.data || props.form || {}
})

const resourceList = (value) => {
    if (Array.isArray(value?.data)) return value.data
    return Array.isArray(value) ? value : []
}

const fields = computed(() => {
    return resourceList(formData.value?.fields)
})

const translations = computed(() => {
    return resourceList(formData.value?.translations)
})

/* ===================== Locale ===================== */

const defaultLocale = props.currentLocale
    || formData.value?.translation?.locale
    || translations.value?.[0]?.locale
    || 'ru'

const activeLocale = ref(defaultLocale)

const localeList = computed(() => {
    const result = new Set()

    props.availableLocales.forEach(locale => result.add(locale))

    translations.value.forEach(translation => {
        if (translation?.locale) {
            result.add(translation.locale)
        }
    })

    return [...result]
})

/* ===================== Translations ===================== */

const findTranslation = (items, localeCode) => {
    return resourceList(items).find(
        translation => translation?.locale === localeCode
    ) || null
}

const currentTranslation = computed(() => {
    return findTranslation(
        formData.value?.translations,
        activeLocale.value
    )
})

const fieldTranslation = (field) => {
    return findTranslation(
        field?.translations,
        activeLocale.value
    ) || field?.translation || null
}

const optionTranslation = (option) => {
    return findTranslation(
        option?.translations,
        activeLocale.value
    ) || option?.translation || null
}

/* ===================== Labels ===================== */

const pageTitle = computed(() => {
    return currentTranslation.value?.title
        || formData.value?.translation?.title
        || formData.value?.code
        || `ID: ${formData.value?.id ?? ''}`
})

const statusTranslationMap = {
    draft: 'statusDraft',
    published: 'statusPublished',
    archived: 'statusArchived'
}

const statusLabel = (status) => {
    const translationKey = statusTranslationMap[status]

    if (translationKey) {
        return t(translationKey)
    }

    return props.statuses?.[status]?.label
        || props.statuses?.[status]
        || status
        || '—'
}

/**
 * Цвет статуса формы.
 */
const statusClass = (status) => {
    const classes = {
        draft: 'bg-amber-100 text-amber-800 border border-amber-300 ' +
            'dark:bg-amber-900/40 dark:text-amber-200 dark:border-amber-500',

        published: 'bg-teal-100 text-teal-800 border border-teal-300 ' +
            'dark:bg-teal-900/40 dark:text-teal-200 dark:border-teal-500',

        archived: 'bg-slate-200 text-slate-700 border border-slate-400 ' +
            'dark:bg-slate-700 dark:text-slate-200 dark:border-slate-500',
    }

    return classes[status]
        || 'bg-slate-100 text-slate-700 border border-slate-300 ' +
        'dark:bg-slate-700 dark:text-slate-200 dark:border-slate-500'
}

const fieldTypeLabel = (type) => {
    return props.fieldTypes?.[type]?.label
        || type
        || '—'
}

const fieldWidthLabel = (width) => {
    return props.fieldWidths?.[width]?.label
        || props.fieldWidths?.[width]
        || width
        || '—'
}

const yesNo = (value) => {
    return value ? t('yes') : t('no')
}

const formatDate = (value) => {
    if (!value) return '—'

    const date = new Date(value)

    if (Number.isNaN(date.getTime())) {
        return value
    }

    return new Intl.DateTimeFormat('ru-RU', {
        dateStyle: 'medium',
        timeStyle: 'short'
    }).format(date)
}

const jsonValue = (value) => {
    if (
        value === null
        || value === undefined
        || value === ''
    ) {
        return '—'
    }

    if (typeof value === 'string') {
        return value
    }

    return JSON.stringify(value, null, 2)
}

/* ===================== Field state ===================== */

const fieldTitle = (field) => {
    return fieldTranslation(field)?.label
        || field?.name
        || `ID: ${field?.id}`
}

const optionTitle = (option) => {
    return optionTranslation(option)?.label
        || option?.value
        || `ID: ${option?.id}`
}

const fieldOptions = (field) => {
    return resourceList(field?.options)
}

/* ===================== Field collapse ===================== */

const expandedFields = ref(
    new Set(fields.value.map(field => field.id))
)

const isFieldExpanded = (fieldId) => {
    return expandedFields.value.has(fieldId)
}

const toggleField = (fieldId) => {
    const next = new Set(expandedFields.value)

    if (next.has(fieldId)) {
        next.delete(fieldId)
    } else {
        next.add(fieldId)
    }

    expandedFields.value = next
}

const expandAllFields = () => {
    expandedFields.value = new Set(
        fields.value.map(field => field.id)
    )
}

const collapseAllFields = () => {
    expandedFields.value = new Set()
}

const allFieldsExpanded = computed(() => {
    return fields.value.length > 0
        && fields.value.every(field => expandedFields.value.has(field.id))
})

/* ===================== Navigation ===================== */

const editUrl = computed(() => {
    return route('admin.forms.edit', {
        form: formData.value.id
    })
})

const fieldsUrl = computed(() => {
    return route('admin.formFields.index', {
        form_id: formData.value.id
    })
})

const submissionsUrl = computed(() => {
    return route('admin.formSubmissions.index', {
        form_id: formData.value.id
    })
})
</script>

<template>
    <AdminLayout :title="pageTitle">
        <template #header>
            <TitlePage>
                {{ t('viewForm') }}: {{ pageTitle }} [ID: {{ formData.id }}]
            </TitlePage>
        </template>

        <div
            class="px-2 py-2 w-full max-w-12xl mx-auto"
        >
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >
                <!-- Верхняя панель -->
                <div
                    class="sm:flex sm:justify-between sm:items-center mb-3 gap-3"
                >
                    <div class="flex flex-wrap items-center gap-2">
                        <DefaultButton :href="route('admin.forms.index')">
                            <template #icon>
                                <svg class="w-4 h-4 fill-current text-slate-100 shrink-0"
                                     viewBox="0 0 16 16">
                                    <path
                                        d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z" />
                                </svg>
                            </template>
                            {{ t('back') }}
                        </DefaultButton>

                        <DefaultButton :href="editUrl">
                            <template #icon>
                                <svg
                                    class="w-4 h-4 fill-current text-slate-100 shrink-0"
                                    viewBox="0 0 16 16">
                                    <path
                                        d="M11.7.3c-.4-.4-1-.4-1.4 0l-10 10c-.2.2-.3.4-.3.7v4c0 .6.4 1 1 1h4c.3 0 .5-.1.7-.3l10-10c.4-.4.4-1 0-1.4l-4-4zM4.6 14H2v-2.6l6-6L10.6 8l-6 6zM12 6.6L9.4 4 11 2.4 13.6 5 12 6.6z"></path>
                                </svg>
                            </template>
                            {{ t('edit') }}
                        </DefaultButton>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <DefaultButton :href="fieldsUrl">
                            <template #icon>
                                <svg
                                    class="w-4 h-4 fill-current text-slate-100 shrink-0"
                                    viewBox="0 0 448 512">
                                    <path
                                        d="M416 304H32c-17.67 0-32 14.33-32 32v32c0 17.67 14.33 32 32 32h384c17.67 0 32-14.33 32-32v-32c0-17.67-14.33-32-32-32zm0-192H32c-17.67 0-32 14.33-32 32v32c0 17.67 14.33 32 32 32h384c17.67 0 32-14.33 32-32v-32c0-17.67-14.33-32-32-32z"></path>
                                </svg>
                            </template>
                            {{ t('formFields') }} [{{ formData.fields_count ?? fields.length }}]
                        </DefaultButton>

                        <DefaultButton :href="submissionsUrl">
                            <template #icon>
                                <svg
                                    class="w-3 h-3 fill-current text-slate-100 shrink-0"
                                    viewBox="0 0 384 512">
                                    <path
                                        d="M224 136V0H24C10.7 0 0 10.7 0 24v464c0 13.3 10.7 24 24 24h336c13.3 0 24-10.7 24-24V160H248c-13.2 0-24-10.8-24-24zm64 236c0 6.6-5.4 12-12 12H108c-6.6 0-12-5.4-12-12v-8c0-6.6 5.4-12 12-12h168c6.6 0 12 5.4 12 12v8zm0-64c0 6.6-5.4 12-12 12H108c-6.6 0-12-5.4-12-12v-8c0-6.6 5.4-12 12-12h168c6.6 0 12 5.4 12 12v8zm0-72v8c0 6.6-5.4 12-12 12H108c-6.6 0-12-5.4-12-12v-8c0-6.6 5.4-12 12-12h168c6.6 0 12 5.4 12 12zm96-114.1v6.1H256V0h6.1c6.4 0 12.5 2.5 17 7l97.9 98c4.5 4.5 7 10.6 7 16.9z" />
                                </svg>
                            </template>
                            {{ t('submissions') }} [{{ formData.submissions_count ?? 0 }}]
                        </DefaultButton>
                    </div>
                </div>

                <!-- Краткая информация -->
                <section
                    class="mb-5 p-4 rounded-md border border-slate-400
                           dark:border-slate-500 bg-white dark:bg-slate-800"
                >
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-semibold text-slate-900 dark:text-slate-100">
                                {{ pageTitle }}
                            </h2>

                            <div
                                v-if="currentTranslation?.subtitle"
                                class="mt-1 text-sm text-slate-600 dark:text-slate-300"
                            >
                                {{ currentTranslation.subtitle }}
                            </div>

                            <div class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                                {{ t('code') }}:
                                <span class="font-mono text-slate-800 dark:text-slate-200">
                                    {{ formData.code }}
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <span
                                class="px-3 py-1 rounded-full text-xs font-semibold"
                                :class="statusClass(formData.status)"
                            >
                                {{ statusLabel(formData.status) }}
                            </span>

                            <span
                                class="px-3 py-1 rounded-full text-xs font-semibold"
                                :class="
                                    formData.activity
                                        ? 'bg-green-100 text-green-800 dark:bg-green-900/40 ' +
                                         'dark:text-green-200 ' +
                                          'border border-green-300 dark:border-green-400'
                                        : 'bg-red-100 text-red-800 dark:bg-red-900/40 ' +
                                         'dark:text-red-200'
                                "
                            >
                                {{ formData.activity ? t('activated') : t('deactivated') }}
                            </span>
                        </div>
                    </div>
                </section>

                <!-- Основные данные -->
                <section class="mb-5">
                    <h3 class="mb-3 text-lg font-semibold text-slate-900 dark:text-slate-100">
                        {{ t('mainData') }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3">
                        <div class="info-card">
                            <div class="info-label">ID</div>
                            <div class="info-value">{{ formData.id }}</div>
                        </div>

                        <div class="info-card">
                            <div class="info-label">{{ t('systemCode') }}</div>
                            <div class="info-value font-mono">{{ formData.code }}</div>
                        </div>

                        <div class="info-card">
                            <div class="info-label">{{ t('sort') }}</div>
                            <div class="info-value">{{ formData.sort }}</div>
                        </div>

                        <div class="info-card">
                            <div class="info-label">{{ t('status') }}</div>

                            <div class="mt-2">
                                <span
                                    class="inline-flex px-2 py-1 rounded-md text-xs font-semibold"
                                    :class="statusClass(formData.status)"
                                >
                                    {{ statusLabel(formData.status) }}
                                </span>
                            </div>
                        </div>

                        <div class="info-card">
                            <div class="info-label">{{ t('activity') }}</div>
                            <div class="info-value">{{ yesNo(formData.activity) }}</div>
                        </div>

                        <div class="info-card">
                            <div class="info-label">{{ t('authorizationRequired') }}</div>
                            <div class="info-value">{{ yesNo(formData.auth_required) }}</div>
                        </div>

                        <div class="info-card">
                            <div class="info-label">{{ t('fields') }}</div>
                            <div class="info-value">
                                {{ formData.fields_count ?? fields.length }}
                            </div>
                        </div>

                        <div class="info-card">
                            <div class="info-label">{{ t('submissions') }}</div>
                            <div class="info-value">
                                {{ formData.submissions_count ?? 0 }}
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Локали -->
                <section class="mb-5">
                    <div
                        class="flex flex-col sm:flex-row sm:items-center
                               sm:justify-between gap-3 mb-3"
                    >
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">
                            {{ t('translations') }}
                        </h3>

                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="localeCode in localeList"
                                :key="localeCode"
                                type="button"
                                class="px-2 py-1 rounded-md text-sm
                                       font-semibold border transition"
                                :class="
                                    activeLocale === localeCode
                                        ? 'bg-blue-600 border-blue-600 text-white'
                                        : 'bg-white dark:bg-slate-800 border-slate-400 ' +
                                         'dark:border-slate-500 text-slate-700 dark:text-slate-200'
                                "
                                @click="activeLocale = localeCode"
                            >
                                {{ localeCode.toUpperCase() }}
                            </button>
                        </div>
                    </div>

                    <div
                        v-if="currentTranslation"
                        class="p-4 rounded-md border border-slate-400
                               dark:border-slate-500 bg-white dark:bg-slate-800"
                    >
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div>
                                <div class="info-label">{{ t('title') }}</div>
                                <div class="info-value">{{ currentTranslation.title || '—' }}</div>
                            </div>

                            <div>
                                <div class="info-label">{{ t('subtitle') }}</div>
                                <div class="info-value">
                                    {{ currentTranslation.subtitle || '—' }}
                                </div>
                            </div>

                            <div>
                                <div class="info-label">{{ t('submitButtonText') }}</div>
                                <div class="info-value">
                                    {{ currentTranslation.submit_text || '—' }}
                                </div>
                            </div>

                            <div>
                                <div class="info-label">{{ t('messageSuccessful') }}</div>
                                <div class="info-value">
                                    {{ currentTranslation.success_message || '—' }}
                                </div>
                            </div>

                            <div class="lg:col-span-2">
                                <div class="info-label">{{ t('errorMessage') }}</div>
                                <div class="info-value">
                                    {{ currentTranslation.error_message || '—' }}
                                </div>
                            </div>

                            <div class="lg:col-span-2">
                                <div class="info-label">{{ t('description') }}</div>
                                <div
                                    v-if="currentTranslation.description"
                                    class="mt-1 text-sm text-slate-800 dark:text-slate-200
                                           prose dark:prose-invert max-w-none"
                                    v-html="currentTranslation.description"
                                />
                                <div v-else class="info-value">—</div>
                            </div>
                        </div>
                    </div>

                    <div
                        v-else
                        class="p-4 rounded-md border border-amber-300
                               bg-amber-50 dark:bg-amber-900/20
                               text-amber-800 dark:text-amber-200"
                    >
                        {{ activeLocale.toUpperCase() }} - {{ t('isNoTranslation') }}
                    </div>
                </section>

                <!-- Защита и поведение -->
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-5 mb-5">
                    <section>
                        <h3 class="mb-3 text-lg font-semibold text-slate-900 dark:text-slate-100">
                            {{ t('spamProtection') }}
                        </h3>

                        <div
                            class="p-3 rounded-md border border-slate-400
                                   dark:border-slate-500 bg-white dark:bg-slate-800"
                        >
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <div class="info-label">{{ t('protectionEnabled') }}</div>
                                    <div class="info-value">{{ yesNo(formData.spam_protection) }}</div>
                                </div>

                                <div>
                                    <div class="info-label">Honeypot</div>
                                    <div class="info-value">{{ yesNo(formData.honeypot_enabled) }}</div>
                                </div>

                                <div>
                                    <div class="info-label">{{ t('minimumFillTime') }}</div>
                                    <div class="info-value">
                                        {{ formData.min_submit_seconds }}
                                    </div>
                                </div>

                                <div>
                                    <div class="info-label">CAPTCHA</div>
                                    <div class="info-value">
                                        {{ yesNo(formData.captcha_enabled) }}
                                    </div>
                                </div>

                                <div>
                                    <div class="info-label">{{ t('sendingLimit') }}</div>
                                    <div class="info-value">{{ formData.rate_limit }}</div>
                                </div>

                                <div>
                                    <div class="info-label">{{ t('periodMinutes') }}</div>
                                    <div class="info-value">
                                        {{ formData.rate_limit_minutes }} мин.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section>
                        <h3 class="mb-3 text-lg font-semibold text-slate-900 dark:text-slate-100">
                            {{ t('additionalSettings') }}
                        </h3>

                        <pre
                            class="p-4 min-h-52 rounded-md border border-slate-400
                                   dark:border-slate-500 bg-white dark:bg-slate-900
                                   text-xs text-slate-800 dark:text-slate-200
                                   overflow-x-auto whitespace-pre-wrap break-words"
                        >{{ jsonValue(formData.settings) }}</pre>
                    </section>
                </div>

                <!-- Поля формы -->
                <section class="mb-5">
                    <div class="flex items-center justify-between gap-3 px-3 mb-3">

                        <h3 class="flex items-center gap-3 text-lg font-semibold
                                   text-slate-900 dark:text-slate-100">
                            {{ t('formFields') }}

                            <span class="px-3 py-0.5 rounded-full bg-slate-200 dark:bg-slate-900
                                         border border-slate-300 dark:border-slate-600
                                         text-sm text-slate-700 dark:text-slate-300">
                                {{ fields.length }}
                            </span>
                        </h3>

                        <button
                            v-if="fields.length"
                            type="button"
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md
                                   text-xs font-semibold
                                   text-sky-700 dark:text-sky-300
                                   bg-sky-50 dark:bg-sky-900/30
                                   border border-sky-300 dark:border-sky-600
                                   shadow-sm
                                   hover:bg-sky-100 dark:hover:bg-sky-900/50
                                   hover:border-sky-400 dark:hover:border-sky-500
                                   focus:outline-none focus:ring-2 focus:ring-sky-400/50
                                   transition-all duration-200"
                            @click="allFieldsExpanded ? collapseAllFields() : expandAllFields()"
                        >
                            <svg
                                class="w-4 h-4 shrink-0 transition-transform duration-200"
                                :class="{ 'rotate-180': allFieldsExpanded }"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71
                                       a.75.75 0 1 1 1.06 1.06l-4.24 4.24
                                       a.75.75 0 0 1-1.06 0L5.21 8.29
                                       a.75.75 0 0 1 .02-1.08Z"
                                    clip-rule="evenodd"
                                />
                            </svg>

                            <span>
                                {{ allFieldsExpanded ? t('collapseAll') : t('expandAll') }}
                            </span>
                        </button>
                    </div>

                    <div
                        v-if="!fields.length"
                        class="p-6 text-center rounded-md border border-slate-400
                               dark:border-slate-500 text-slate-600 dark:text-slate-400"
                    >
                        {{ t('noData') }}
                    </div>

                    <div v-else class="space-y-4">
                        <article
                            v-for="(field, fieldIndex) in fields"
                            :key="field.id"
                            class="rounded-md border border-slate-400 dark:border-slate-500
                                   bg-white dark:bg-slate-800 overflow-hidden"
                        >
                            <!-- Заголовок поля -->
                            <div
                                class="px-3 py-1 flex flex-col lg:flex-row
                                       lg:items-center lg:justify-between gap-3
                                       bg-slate-100 dark:bg-slate-900
                                       border-b border-dashed border-slate-500
                                       cursor-pointer select-none
                                       hover:bg-slate-200 dark:hover:bg-slate-700
                                       transition-colors"
                                @click="toggleField(field.id)"
                            >
                                <div class="flex items-center gap-3">
                                    <svg
                                        class="w-4 h-4 shrink-0 text-slate-500 dark:text-slate-300
                                               transition-transform duration-200"
                                        :class="{ 'rotate-90': isFieldExpanded(field.id) }"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M7.21 14.77a.75.75 0 0 1 .02-1.06L10.94 10
                                               7.23 6.29a.75.75 0 1 1 1.06-1.06l4.24 4.24
                                               a.75.75 0 0 1 0 1.06l-4.24 4.24
                                               a.75.75 0 0 1-1.08 0Z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>

                                    <div>
                                        <div class="font-semibold text-slate-900 dark:text-slate-100">
                                            {{ fieldIndex + 1 }}. {{ fieldTitle(field) }}
                                        </div>

                                        <div class="mt-1 text-xs text-slate-600 dark:text-slate-400">
                                            {{ field.name }} · {{ fieldTypeLabel(field.type) }}
                                            · ID: {{ field.id }}
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    <!-- Активность -->
                                    <span
                                        class="field-badge"
                                        :class="
                                field.activity
                                    ? 'bg-teal-100 text-teal-800 border border-teal-300 ' +
                                      'dark:bg-teal-900/40 dark:text-teal-200 dark:border-teal-500'
                                    : 'bg-red-100 text-red-800 border border-red-300 ' +
                                      'dark:bg-red-900/40 dark:text-red-200 dark:border-red-500'
                                "
                                    >
                                        {{ field.activity ? t('actively') : t('notActive') }}
                                    </span>

                                    <!-- Обязательное поле -->
                                    <span
                                        v-if="field.required"
                                        class="field-badge bg-blue-100 text-blue-800
                                               border border-blue-300 dark:bg-blue-900/40
                                               dark:text-blue-200 dark:border-blue-500"
                                    >
                                        {{ t('required') }}
                                    </span>

                                    <!-- Только для чтения -->
                                    <span
                                        v-if="field.readonly"
                                        class="field-badge bg-amber-100 text-amber-800
                                               border border-amber-300 dark:bg-amber-900/40
                                               dark:text-amber-200 dark:border-amber-500"
                                    >
                                        {{ t('readOnly') }}
                                    </span>

                                    <!-- Отключённое поле -->
                                    <span
                                        v-if="field.disabled"
                                        class="field-badge bg-purple-100 text-purple-800
                                               border border-purple-300 dark:bg-purple-900/40
                                               dark:text-purple-200 dark:border-purple-500"
                                    >
                                        {{ t('disabled') }}
                                    </span>
                                </div>
                            </div>

                            <div
                                v-show="isFieldExpanded(field.id)"
                                class="p-4"
                            >
                                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                                    <div>
                                        <div class="info-label">{{ t('systemName') }}</div>
                                        <div class="info-value font-mono">{{ field.name }}</div>
                                    </div>

                                    <div>
                                        <div class="info-label">{{ t('type') }}</div>
                                        <div class="info-value">{{ fieldTypeLabel(field.type) }}</div>
                                    </div>

                                    <div>
                                        <div class="info-label">{{ t('width') }}</div>
                                        <div class="info-value">{{ fieldWidthLabel(field.width) }}</div>
                                    </div>

                                    <div>
                                        <div class="info-label">{{ t('sort') }}</div>
                                        <div class="info-value">{{ field.sort }}</div>
                                    </div>

                                    <div>
                                        <div class="info-label">{{ t('title') }}</div>
                                        <div class="info-value">
                                            {{ fieldTranslation(field)?.label || '—' }}
                                        </div>
                                    </div>

                                    <div>
                                        <div class="info-label">Placeholder</div>
                                        <div class="info-value">
                                            {{ fieldTranslation(field)?.placeholder || '—' }}
                                        </div>
                                    </div>

                                    <div>
                                        <div class="info-label">{{ t('defaultValue') }}</div>
                                        <div class="info-value">
                                            {{ field.default_value ?? '—' }}
                                        </div>
                                    </div>

                                    <div>
                                        <div class="info-label">{{ t('variants') }}</div>
                                        <div class="info-value">
                                            {{ fieldOptions(field).length }}
                                        </div>
                                    </div>

                                    <div class="md:col-span-2 xl:col-span-4">
                                        <div class="info-label">{{ t('description') }}</div>
                                        <div class="info-value whitespace-pre-wrap">
                                            {{ fieldTranslation(field)?.description || '—' }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Validation / settings -->
                                <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 mt-4">
                                    <div>
                                        <div class="info-label mb-1">
                                            {{ t('validationRulesJSON') }}
                                        </div>

                                        <pre
                                            class="json-box"
                                        >{{ jsonValue(field.validation) }}</pre>
                                    </div>

                                    <div>
                                        <div class="info-label mb-1">{{ t('fieldSettings') }}</div>

                                        <pre
                                            class="json-box"
                                        >{{ jsonValue(field.settings) }}</pre>
                                    </div>
                                </div>

                                <!-- Варианты -->
                                <div
                                    v-if="fieldOptions(field).length"
                                    class="mt-5"
                                >
                                    <h4
                                        class="mb-2 text-sm font-semibold
                                               text-slate-800 dark:text-slate-100"
                                    >
                                        {{ t('fieldOptions') }}
                                    </h4>

                                    <div class="overflow-x-auto">
                                        <table class="w-full text-sm">
                                            <thead>
                                            <tr
                                                class="border-b border-slate-400
                                                           dark:border-slate-600
                                                           text-left text-slate-500
                                                           dark:text-slate-300"
                                            >
                                                <th class="px-3 py-2">#</th>
                                                <th class="px-3 py-2">{{ t('title') }}</th>
                                                <th class="px-3 py-2">{{ t('value') }}</th>
                                                <th class="px-3 py-2">{{ t('activity') }}</th>
                                                <th class="px-3 py-2">{{ t('default') }}</th>
                                                <th class="px-3 py-2">{{ t('sort') }}</th>
                                            </tr>
                                            </thead>

                                            <tbody>
                                            <tr
                                                v-for="(option, optionIndex) in fieldOptions(field)"
                                                :key="option.id"
                                                class="border-b border-slate-200
                                                           dark:border-slate-700 last:border-0"
                                            >
                                                <td class="px-3 py-2">
                                                    {{ optionIndex + 1 }}
                                                </td>

                                                <td class="px-3 py-2 font-medium">
                                                    {{ optionTitle(option) }}

                                                    <div
                                                        v-if="optionTranslation(option)?.description"
                                                        class="mt-1 text-xs font-normal
                                                               text-slate-500 dark:text-slate-400"
                                                    >
                                                        {{ optionTranslation(option).description }}
                                                    </div>
                                                </td>

                                                <td class="px-3 py-2 font-mono">
                                                    {{ option.value }}
                                                </td>

                                                <td class="px-3 py-2">
                                                    {{ yesNo(option.activity) }}
                                                </td>

                                                <td class="px-3 py-2">
                                                    {{ yesNo(option.is_default) }}
                                                </td>

                                                <td class="px-3 py-2">
                                                    {{ option.sort }}
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div
                                        v-for="option in fieldOptions(field)"
                                        :key="`settings-${option.id}`"
                                        class="mt-3"
                                    >
                                        <template v-if="option.settings">
                                            <div class="info-label mb-1">
                                                {{ t('variantSettings') }}
                                                «{{ optionTitle(option) }}»
                                            </div>

                                        <pre class="json-box">{{ jsonValue(option.settings) }}</pre>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                </section>

                <!-- Владелец и служебная информация -->
                <section>
                    <h3 class="mb-3 text-lg font-semibold text-slate-900 dark:text-slate-100">
                        {{ t('serviceInformation') }}
                    </h3>

                    <div
                        class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3"
                    >
                        <div class="info-card">
                            <div class="info-label">{{ t('owner') }}</div>
                            <div class="info-value">
                                {{ formData.owner?.name || '—' }}
                            </div>
                        </div>

                        <div class="info-card">
                            <div class="info-label">{{ t('owner') }} Email</div>
                            <div class="info-value">
                                {{ formData.owner?.email || '—' }}
                            </div>
                        </div>

                        <div class="info-card">
                            <div class="info-label">{{ t('createdAt') }}</div>
                            <div class="info-value">
                                {{ formatDate(formData.created_at) }}
                            </div>
                        </div>

                        <div class="info-card">
                            <div class="info-label">{{ t('updatedAt') }}</div>
                            <div class="info-value">
                                {{ formatDate(formData.updated_at) }}
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Нижние кнопки -->
                <div class="flex flex-wrap justify-center gap-3 mt-6">
                    <DefaultButton :href="route('admin.forms.index')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                                 viewBox="0 0 16 16">
                                <path
                                    d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z" />
                            </svg>
                        </template>
                        {{ t('back') }}
                    </DefaultButton>

                    <DefaultButton :href="editUrl">
                        <template #icon>
                            <svg
                                class="w-4 h-4 fill-current text-slate-100 shrink-0"
                                viewBox="0 0 16 16">
                                <path
                                    d="M11.7.3c-.4-.4-1-.4-1.4 0l-10 10c-.2.2-.3.4-.3.7v4c0 .6.4 1 1 1h4c.3 0 .5-.1.7-.3l10-10c.4-.4.4-1 0-1.4l-4-4zM4.6 14H2v-2.6l6-6L10.6 8l-6 6zM12 6.6L9.4 4 11 2.4 13.6 5 12 6.6z"></path>
                            </svg>
                        </template>
                        {{ t('edit') }}
                    </DefaultButton>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.info-card {
    @apply p-3 rounded-md border border-slate-400 dark:border-slate-500
    bg-white dark:bg-slate-800;
}

.info-label {
    @apply text-xs font-semibold uppercase tracking-wide
    text-indigo-800 dark:text-indigo-400;
}

.info-value {
    @apply mt-1 font-semibold text-sm text-slate-700 dark:text-slate-300 break-words;
}

.field-badge {
    @apply px-2 py-1 rounded-md text-xs font-semibold;
}

.json-box {
    @apply p-3 rounded-md border border-slate-400 dark:border-slate-600
    bg-slate-50 dark:bg-slate-700
    text-xs text-slate-800 dark:text-slate-200
    overflow-x-auto whitespace-pre-wrap break-words;
}
</style>
