<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Редактирование отдельного поля динамической формы.
 */

import { computed, ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import PrimaryButton from '@/Components/Admin/UI/Buttons/PrimaryButton.vue'
import TranslationTabs from '@/Components/Admin/UI/Locale/TranslationTabs.vue'
import FormFieldEditor from '@/Components/Admin/Form/FormBuilder/FormFieldEditor.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    field: { type: [Object, null], default: null },
    form: { type: [Object, null], default: null },
    forms: { type: [Array, Object], default: () => [] },
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },
    fieldTypes: { type: Object, default: () => ({}) },
    fieldWidths: { type: Object, default: () => ({}) },
    validationRules: { type: Object, default: () => ({}) },
    fileSettings: { type: Object, default: () => ({}) },
    errors: { type: Object, default: () => ({}) }
})

/* ===================== Resources ===================== */

const fieldData = computed(() => props.field?.data || props.field || {})
const parentForm = computed(() => props.form?.data || props.form || {})

const resourceList = value => {
    if (Array.isArray(value?.data)) return value.data
    return Array.isArray(value) ? value : []
}

const formsList = computed(() => resourceList(props.forms))

/* ===================== Helpers ===================== */

const jsonValue = value => {
    if (value === null || value === undefined || value === '') return ''

    return typeof value === 'string'
        ? value
        : JSON.stringify(value, null, 2)
}

const makeFieldTranslation = () => ({
    label: '',
    placeholder: '',
    description: ''
})

const makeOptionTranslation = () => ({
    label: '',
    description: ''
})

const makeFieldTranslations = () => {
    const result = {}

    resourceList(fieldData.value?.translations).forEach(translation => {
        if (!translation?.locale) return

        result[translation.locale] = {
            label: translation.label || '',
            placeholder: translation.placeholder || '',
            description: translation.description || ''
        }
    })

    const localeCode = props.currentLocale
        || fieldData.value?.translation?.locale
        || 'ru'

    if (!result[localeCode]) {
        result[localeCode] = makeFieldTranslation()
    }

    return result
}

const makeOptionTranslations = option => {
    const result = {}

    resourceList(option?.translations).forEach(translation => {
        if (!translation?.locale) return

        result[translation.locale] = {
            label: translation.label || '',
            description: translation.description || ''
        }
    })

    const localeCode = props.currentLocale
        || option?.translation?.locale
        || 'ru'

    if (!result[localeCode]) {
        result[localeCode] = makeOptionTranslation()
    }

    return result
}

const makeOptions = () => {
    return resourceList(fieldData.value?.options).map(option => ({
        id: Number(option.id),
        _delete: false,
        value: option.value || '',
        activity: Boolean(option.activity),
        is_default: Boolean(option.is_default),
        sort: Number(option.sort ?? 100),
        settings: jsonValue(option.settings),
        translations: makeOptionTranslations(option)
    }))
}

/* ===================== Locale ===================== */

const defaultLocale = props.currentLocale
    || fieldData.value?.translation?.locale
    || 'ru'

const activeLocale = ref(defaultLocale)

/* ===================== Form ===================== */

const form = useForm({
    _method: 'put',

    form_id: Number(
        fieldData.value.form_id
        ?? parentForm.value?.id
        ?? 0
    ),

    name: fieldData.value.name || '',
    type: fieldData.value.type || 'text',

    activity: Boolean(fieldData.value.activity),
    required: Boolean(fieldData.value.required),
    readonly: Boolean(fieldData.value.readonly),
    disabled: Boolean(fieldData.value.disabled),

    sort: Number(fieldData.value.sort ?? 100),

    default_value: fieldData.value.default_value ?? '',
    validation: jsonValue(fieldData.value.validation),
    width: fieldData.value.width || 'full',
    settings: jsonValue(fieldData.value.settings),

    translations: makeFieldTranslations(),
    options: makeOptions()
})

/**
 * Модель одного поля для FormFieldEditor.
 *
 * FormFieldEditor работает по стандартному контракту v-model:
 * получает объект и возвращает новый объект через update:modelValue.
 *
 * Нельзя использовать v-model="form", потому что form — это
 * экземпляр Inertia useForm со служебными методами и состояниями.
 */
const fieldModel = computed({
    get: () => ({
        id: fieldData.value.id,

        form_id: form.form_id,
        name: form.name,
        type: form.type,

        activity: form.activity,
        required: form.required,
        readonly: form.readonly,
        disabled: form.disabled,

        sort: form.sort,

        default_value: form.default_value,
        validation: form.validation,
        width: form.width,
        settings: form.settings,

        translations: form.translations,
        options: form.options
    }),

    set: value => {
        form.form_id = value.form_id
        form.name = value.name
        form.type = value.type

        form.activity = value.activity
        form.required = value.required
        form.readonly = value.readonly
        form.disabled = value.disabled

        form.sort = value.sort

        form.default_value = value.default_value
        form.validation = value.validation
        form.width = value.width
        form.settings = value.settings

        form.translations = value.translations
        form.options = value.options
    }
})

/* ===================== Page ===================== */

const currentTranslation = computed(() => {
    return form.translations?.[activeLocale.value]
})

const pageTitle = computed(() => {
    return currentTranslation.value?.label
        || fieldData.value?.translation?.label
        || form.name
        || `ID: ${fieldData.value?.id ?? ''}`
})

const parentFormTitle = computed(() => {
    return parentForm.value?.translation?.title
        || parentForm.value?.code
        || `ID: ${parentForm.value?.id ?? ''}`
})

/* ===================== Payload ===================== */

const prepareOption = option => {
    if (option.id && option._delete) {
        return {
            id: Number(option.id),
            _delete: true
        }
    }

    return {
        ...option,
        id: option.id ? Number(option.id) : null,
        _delete: false,
        sort: Number(option.sort ?? 0),
        activity: option.activity ? 1 : 0,
        is_default: option.is_default ? 1 : 0
    }
}

const supportsOptions = data => {
    return Boolean(props.fieldTypes?.[data.type]?.has_options)
}

const supportsDefaultValue = data => {
    return data.type !== 'file' && !supportsOptions(data)
}

/* ===================== Submit ===================== */

const submitForm = () => {
    form.transform(data => ({
        ...data,

        form_id: Number(data.form_id),
        sort: Number(data.sort ?? 0),

        activity: data.activity ? 1 : 0,
        required: data.required ? 1 : 0,
        readonly: data.readonly ? 1 : 0,
        disabled: data.disabled ? 1 : 0,

        default_value: supportsDefaultValue(data)
            ? (data.default_value || null)
            : null,

        options: Array.isArray(data.options)
            ? data.options.map(prepareOption)
            : []
    }))

    form.post(
        route('admin.formFields.update', {
            formField: fieldData.value.id
        }),
        {
            errorBag: 'editFormField',
            preserveScroll: true,

            onSuccess: () => {
                toast.success('Поле формы успешно обновлено.')
            },

            onError: errors => {
                const firstKey = Object.keys(errors || {})[0]

                toast.error(
                    errors[firstKey]
                    || 'Проверьте корректность заполнения поля формы.'
                )
            }
        }
    )
}
</script>

<template>
    <AdminLayout :title="pageTitle">
        <template #header>
            <TitlePage>
                {{ t('edit') }}: {{ pageTitle }} [ID: {{ fieldData.id }}]
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

                <!-- Навигация -->
                <div class="flex flex-col lg:flex-row lg:items-center
                            lg:justify-between gap-3 mb-4">

                    <DefaultButton
                        :href="route('admin.formFields.index', { form_id: form.form_id })"
                    >
                        <template #icon>
                            <svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                                 viewBox="0 0 16 16">
                                <path
                                    d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z" />
                            </svg>
                        </template>
                        {{ t('back') }}
                    </DefaultButton>

                    <div
                        v-if="parentForm?.id"
                        class="text-md text-slate-500 dark:text-slate-300"
                    >
                        {{ parentFormTitle }}
                        · {{ parentForm.code }}
                    </div>

                    <DefaultButton
                        :href="route('admin.forms.edit', { form: form.form_id })"
                    >
                        <template #icon>
                            <svg
                                class="w-4 h-4 fill-current text-slate-100 shrink-0"
                                viewBox="0 0 16 16">
                                <path
                                    d="M11.7.3c-.4-.4-1-.4-1.4 0l-10 10c-.2.2-.3.4-.3.7v4c0 .6.4 1 1 1h4c.3 0 .5-.1.7-.3l10-10c.4-.4.4-1 0-1.4l-4-4zM4.6 14H2v-2.6l6-6L10.6 8l-6 6zM12 6.6L9.4 4 11 2.4 13.6 5 12 6.6z"></path>
                            </svg>
                        </template>
                        {{ t('editForm') }}
                    </DefaultButton>
                </div>

                <form @submit.prevent="submitForm" class="w-full">

                    <!-- Родительская форма -->
                    <div class="mb-5 p-3 border border-slate-300 dark:border-slate-500
                                bg-white dark:bg-slate-800 rounded-sm">

                        <div class="flex flex-col items-start">
                            <label
                                for="form_id"
                                class="mb-1 text-sm font-medium
                                       text-slate-700 dark:text-slate-200"
                            >
                                {{ t('form') }}
                            </label>

                            <select
                                id="form_id"
                                v-model.number="form.form_id"
                                class="w-full px-2 py-0.5 form-select bg-white text-gray-600
                                       border border-slate-400 dark:border-slate-600 rounded-sm
                                       shadow-sm dark:bg-cyan-800 dark:text-slate-100"
                            >
                                <option
                                    v-for="item in formsList"
                                    :key="item.id"
                                    :value="item.id"
                                >
                                    {{ item.translation?.title || item.code }}
                                    [ID: {{ item.id }}]
                                </option>
                            </select>

                            <div
                                v-if="form.errors.form_id"
                                class="mt-2 text-sm text-red-600 dark:text-red-300"
                            >
                                {{ form.errors.form_id }}
                            </div>
                        </div>
                    </div>

                    <!-- Языки -->
                    <div class="mb-5 p-3 border border-slate-300 dark:border-slate-500
                                bg-white dark:bg-slate-800 rounded-sm">

                        <TranslationTabs
                            v-model="activeLocale"
                            :translations="form.translations"
                            :available-locales="availableLocales"
                            :make-translation="makeFieldTranslation"
                            @update:translations="form.translations = $event"
                            @removed="toast.warning(t('translationRemoved'))"
                            @added="toast.success(t('localeAdded'))"
                        />
                    </div>

                    <!-- Редактор одного поля -->
                    <FormFieldEditor
                        v-model="fieldModel"
                        :active-locale="activeLocale"
                        :field-types="fieldTypes"
                        :field-widths="fieldWidths"
                        :errors="form.errors"
                        :locales="availableLocales"
                    />

                    <!-- Кнопка сохранения и назад -->
                    <div class="flex items-center justify-center mt-5 gap-3">
                        <DefaultButton
                            :href="route('admin.formFields.index', { form_id: form.form_id })"
                        >
                            <template #icon>
                                <svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2"
                                     viewBox="0 0 16 16">
                                    <path
                                        d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z" />
                                </svg>
                            </template>
                            {{ t('back') }}
                        </DefaultButton>

                        <PrimaryButton
                            type="submit"
                            :disabled="form.processing"
                            :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                        >
                            {{ t('save') }}
                        </PrimaryButton>
                    </div>

                </form>
            </div>
        </div>
    </AdminLayout>
</template>
