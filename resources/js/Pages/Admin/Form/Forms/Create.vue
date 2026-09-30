<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Создание динамической формы.
 */

import { computed, ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import PrimaryButton from '@/Components/Admin/UI/Buttons/PrimaryButton.vue'

import ActivityCheckbox from '@/Components/Admin/UI/Checkbox/ActivityCheckbox.vue'
import LabelCheckbox from '@/Components/Admin/UI/Checkbox/LabelCheckbox.vue'
import LabelInput from '@/Components/Admin/UI/Input/LabelInput.vue'
import InputText from '@/Components/Admin/UI/Input/InputText.vue'
import InputNumber from '@/Components/Admin/UI/Input/InputNumber.vue'
import InputError from '@/Components/Admin/UI/Input/InputError.vue'
import MetaDescTextarea from '@/Components/Admin/UI/Textarea/MetaDescTextarea.vue'
import TinyEditor from '@/Components/Admin/UI/TinyEditor/TinyEditor.vue'
import TranslationTabs from '@/Components/Admin/UI/Locale/TranslationTabs.vue'
import FormFieldsBuilder from '@/Components/Admin/Form/FormBuilder/FormFieldsBuilder.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },
    statuses: { type: Object, default: () => ({}) },
    fieldTypes: { type: Object, default: () => ({}) },
    fieldWidths: { type: Object, default: () => ({}) },
    defaults: { type: Object, default: () => ({}) },
    errors: { type: Object, default: () => ({}) },
})

/* ===================== Translations ===================== */

const makeTranslation = () => ({
    title: '',
    subtitle: '',
    description: '',
    submit_text: '',
    success_message: '',
    error_message: '',
})

const defaultLocale = props.currentLocale || 'ru'
const activeLocale = ref(defaultLocale)

const form = useForm({
    code: '',
    sort: Number(props.defaults?.sort ?? 100),
    activity: Boolean(props.defaults?.activity ?? true),
    status: props.defaults?.status || 'draft',

    spam_protection: Boolean(props.defaults?.spam_protection ?? true),
    honeypot_enabled: Boolean(props.defaults?.honeypot_enabled ?? true),
    min_submit_seconds: Number(props.defaults?.min_submit_seconds ?? 3),
    rate_limit: Number(props.defaults?.rate_limit ?? 5),
    rate_limit_minutes: Number(props.defaults?.rate_limit_minutes ?? 10),
    captcha_enabled: Boolean(props.defaults?.captcha_enabled ?? false),

    auth_required: Boolean(props.defaults?.auth_required ?? false),
    settings: props.defaults?.settings ?? null,

    translations: {
        [defaultLocale]: makeTranslation(),
    },

    fields: [],
})

const ensureTranslation = (localeCode) => {
    if (!localeCode) return

    if (!form.translations[localeCode]) {
        form.translations[localeCode] = makeTranslation()
    }
}

watch(
    activeLocale,
    (localeCode) => ensureTranslation(localeCode),
    { immediate: true }
)

const currentTranslation = computed(() => form.translations[activeLocale.value])

const getError = (key) => {
    return form.errors[`translations.${activeLocale.value}.${key}`]
}

/* ===================== Fields ===================== */

const supportsOptions = (field) => {
    return Boolean(props.fieldTypes?.[field.type]?.has_options)
}

const supportsDefaultValue = (field) => {
    return field.type !== 'file' && !supportsOptions(field)
}

const prepareOption = (option) => ({
    ...option,
    id: null,
    _delete: false,
    sort: Number(option.sort ?? 0),
    activity: option.activity ? 1 : 0,
    is_default: option.is_default ? 1 : 0,
})

const prepareField = (field) => ({
    ...field,
    id: null,
    _delete: false,
    sort: Number(field.sort ?? 0),
    activity: field.activity ? 1 : 0,
    required: field.required ? 1 : 0,
    readonly: field.readonly ? 1 : 0,
    disabled: field.disabled ? 1 : 0,
    default_value: supportsDefaultValue(field)
        ? (field.default_value || null)
        : null,
    options: supportsOptions(field)
        ? (Array.isArray(field.options)
            ? field.options.map(prepareOption)
            : [])
        : [],
})

/* ===================== Status ===================== */

const statusTranslationMap = {
    draft: 'statusDraft',
    published: 'statusPublished',
    archived: 'statusArchived',
}

const statusLabel = (status) => {
    const translationKey = statusTranslationMap[status]
    return translationKey ? t(translationKey) : status
}

/* ===================== Submit ===================== */

const submitForm = () => {
    form.transform((data) => ({
        ...data,

        sort: Number(data.sort ?? 0),

        activity: data.activity ? 1 : 0,
        spam_protection: data.spam_protection ? 1 : 0,
        honeypot_enabled: data.honeypot_enabled ? 1 : 0,
        captcha_enabled: data.captcha_enabled ? 1 : 0,
        auth_required: data.auth_required ? 1 : 0,

        min_submit_seconds: Number(data.min_submit_seconds ?? 0),
        rate_limit: Number(data.rate_limit ?? 1),
        rate_limit_minutes: Number(data.rate_limit_minutes ?? 1),

        fields: data.fields.map(prepareField),
    }))

    form.post(route('admin.forms.store'), {
        errorBag: 'createForm',
        preserveScroll: true,

        onSuccess: () => {
            toast.success('Форма успешно создана.')
        },

        onError: (errors) => {
            const firstKey = Object.keys(errors || {})[0]

            toast.error(
                errors[firstKey]
                || 'Проверьте корректность заполнения полей.'
            )
        },
    })
}
</script>

<template>
    <AdminLayout title="Создание формы">
        <template #header>
            <TitlePage>{{ t('addForm') }}</TitlePage>
        </template>

        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto">
            <div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400
                        dark:border-blue-200 shadow-lg shadow-gray-500
                        dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95">

                <div class="sm:flex sm:justify-between sm:items-center mb-2">
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
                </div>

                <form @submit.prevent="submitForm" class="p-3 w-full">

                    <!-- Activity / Sort -->
                    <div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4">
                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox v-model="form.activity" />
                            <LabelCheckbox for="activity" :text="t('activity')"
                                           class="text-sm h-8 flex items-center" />
                        </div>

                        <div class="flex flex-row items-center gap-2">
                            <LabelInput for="sort" :value="t('sort')" class="text-sm" />
                            <InputNumber id="sort" type="number" min="0"
                                         v-model.number="form.sort"
                                         class="w-full lg:w-28" />
                            <InputError :message="form.errors.sort" />
                        </div>
                    </div>

                    <!-- Code / Status -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                        <div class="flex flex-col items-start">
                            <LabelInput for="code">
                                <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                {{ t('systemCode') }}
                            </LabelInput>

                            <InputText id="code" type="text" v-model="form.code"
                                       maxlength="100" autocomplete="off"
                                       placeholder="contact_form" required />

                            <div class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">
                                {{ t('systemCodeText') }}
                            </div>

                            <InputError class="mt-2" :message="form.errors.code" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="status" :value="t('status')" />

                            <select id="status" v-model="form.status"
                                    class="w-full px-2 py-0.5 form-select bg-white text-gray-600
                                           border border-slate-400 dark:border-slate-600 rounded-sm
                                           shadow-sm dark:bg-cyan-800 dark:text-slate-100">
                                <option v-for="(label, value) in statuses"
                                        :key="value" :value="value">
                                    {{ statusLabel(value) }}
                                </option>
                            </select>

                            <InputError class="mt-2" :message="form.errors.status" />
                        </div>
                    </div>

                    <!-- Translations -->
                    <div class="my-5 p-3 border border-slate-300 dark:border-slate-500
                                bg-white dark:bg-slate-800 rounded-sm">

                        <TranslationTabs
                            v-model="activeLocale"
                            :translations="form.translations"
                            :available-locales="availableLocales"
                            :make-translation="makeTranslation"
                            @update:translations="form.translations = $event"
                            @removed="toast.warning(t('translationRemoved'))"
                            @added="toast.success(t('localeAdded'))"
                        />

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput for="title">
                                <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                {{ t('title') }} [{{ activeLocale.toUpperCase() }}]
                            </LabelInput>

                            <InputText id="title" type="text"
                                       v-model="currentTranslation.title"
                                       maxlength="255" autocomplete="off" required />

                            <InputError class="mt-2" :message="getError('title')" />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput for="subtitle"
                                        :value="`${t('subtitle')} [${activeLocale.toUpperCase()}]`" />

                            <InputText id="subtitle" type="text"
                                       v-model="currentTranslation.subtitle"
                                       maxlength="255" autocomplete="off" />

                            <InputError class="mt-2" :message="getError('subtitle')" />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput for="description"
                                        :value="`${t('description')} [${activeLocale.toUpperCase()}]`" />

                            <TinyEditor v-model="currentTranslation.description" :height="350" />
                            <InputError class="mt-2" :message="getError('description')" />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput for="submit_text">
                                {{ t('submitButtonText') }} [{{ activeLocale.toUpperCase() }}]
                            </LabelInput>

                            <InputText id="submit_text" type="text"
                                       v-model="currentTranslation.submit_text"
                                       maxlength="255" autocomplete="off"
                                       placeholder="Отправить" />

                            <InputError class="mt-2" :message="getError('submit_text')" />
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                            <div class="flex flex-col items-start">
                                <LabelInput for="success_message">
                                    {{ t('messageSuccessful') }}
                                    [{{ activeLocale.toUpperCase() }}]
                                </LabelInput>

                                <MetaDescTextarea id="success_message"
                                                  v-model="currentTranslation.success_message"
                                                  class="w-full" />

                                <InputError class="mt-2"
                                            :message="getError('success_message')" />
                            </div>

                            <div class="flex flex-col items-start">
                                <LabelInput for="error_message">
                                    {{ t('errorMessage') }}
                                    [{{ activeLocale.toUpperCase() }}]
                                </LabelInput>

                                <MetaDescTextarea id="error_message"
                                                  v-model="currentTranslation.error_message"
                                                  class="w-full" />

                                <InputError class="mt-2"
                                            :message="getError('error_message')" />
                            </div>
                        </div>
                    </div>

                    <!-- Form fields -->
                    <FormFieldsBuilder
                        v-model="form.fields"
                        :active-locale="activeLocale"
                        :locales="Object.keys(form.translations)"
                        :field-types="fieldTypes"
                        :field-widths="fieldWidths"
                        :errors="form.errors"
                    />

                    <!-- Spam protection -->
                    <div class="my-5 p-3 border border-slate-300 dark:border-slate-500
                                bg-white dark:bg-slate-800 rounded-sm">
                        <h3 class="mb-4 text-sm font-semibold text-slate-800 dark:text-slate-100">
                            {{ t('spamProtectionAndAccess') }}
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <ActivityCheckbox v-model="form.spam_protection" />
                                <span class="text-sm text-slate-700 dark:text-slate-200">
                                    {{ t('spamProtection') }}
                                </span>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer">
                                <ActivityCheckbox v-model="form.honeypot_enabled" />
                                <span class="text-sm text-slate-700 dark:text-slate-200">
                                    Honeypot
                                </span>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer">
                                <ActivityCheckbox v-model="form.captcha_enabled" />
                                <span class="text-sm text-slate-700 dark:text-slate-200">
                                    CAPTCHA
                                </span>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer">
                                <ActivityCheckbox v-model="form.auth_required" />
                                <span class="text-sm text-slate-700 dark:text-slate-200">
                                    {{ t('authorizedUsersOnly') }}
                                </span>
                            </label>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div class="flex flex-col items-start">
                                <LabelInput for="min_submit_seconds"
                                            :value="t('minimumFillTime')" />
                                <InputNumber id="min_submit_seconds" type="number" min="0"
                                             v-model.number="form.min_submit_seconds" />
                                <InputError class="mt-2"
                                            :message="form.errors.min_submit_seconds" />
                            </div>

                            <div class="flex flex-col items-start">
                                <LabelInput for="rate_limit"
                                            :value="t('numberOfShipments')" />
                                <InputNumber id="rate_limit" type="number" min="1"
                                             v-model.number="form.rate_limit" />
                                <InputError class="mt-2"
                                            :message="form.errors.rate_limit" />
                            </div>

                            <div class="flex flex-col items-start">
                                <LabelInput for="rate_limit_minutes"
                                            :value="t('periodMinutes')" />
                                <InputNumber id="rate_limit_minutes" type="number" min="1"
                                             v-model.number="form.rate_limit_minutes" />
                                <InputError class="mt-2"
                                            :message="form.errors.rate_limit_minutes" />
                            </div>
                        </div>

                        <InputError class="mt-2" :message="form.errors.spam_protection" />
                        <InputError class="mt-2" :message="form.errors.honeypot_enabled" />
                        <InputError class="mt-2" :message="form.errors.captcha_enabled" />
                        <InputError class="mt-2" :message="form.errors.auth_required" />
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center justify-center mt-5 gap-3">
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

                        <PrimaryButton type="submit"
                                       :disabled="form.processing"
                                       :class="{ 'opacity-25': form.processing }">
                            {{ t('save') }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
