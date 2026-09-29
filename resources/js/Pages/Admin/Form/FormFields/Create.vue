<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Создание поля динамической формы.
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
import TranslationTabs from '@/Components/Admin/UI/Locale/TranslationTabs.vue'

const { t } = useI18n()
const toast = useToast()

const props = defineProps({
    form: { type: [Object, null], default: null },
    forms: { type: [Array, Object], default: () => [] },
    currentLocale: { type: String, default: '' },
    availableLocales: { type: Array, default: () => [] },
    fieldTypes: { type: Object, default: () => ({}) },
    fieldWidths: { type: Object, default: () => ({}) },
    validationRules: { type: Array, default: () => [] },
    fileSettings: { type: Object, default: () => ({}) },
    defaults: { type: Object, default: () => ({}) },
    errors: { type: Object, default: () => ({}) },
})

/* ===================== Resources ===================== */

const selectedForm = computed(() => props.form?.data || props.form || null)

const formsList = computed(() => {
    if (Array.isArray(props.forms)) return props.forms
    if (Array.isArray(props.forms?.data)) return props.forms.data
    if (Array.isArray(props.forms?.data?.data)) return props.forms.data.data
    if (Array.isArray(props.forms?.resource)) return props.forms.resource
    return []
})

const formTitle = (item) => item?.translation?.title || item?.code || `ID: ${item?.id}`

/* ===================== Translations ===================== */

const makeTranslation = () => ({
    label: '',
    placeholder: '',
    description: '',
})

const defaultLocale = props.currentLocale || 'ru'
const activeLocale = ref(defaultLocale)

/* ===================== Form ===================== */

const form = useForm({
    form_id: props.defaults?.form_id ?? selectedForm.value?.id ?? '',
    name: '',
    type: props.defaults?.type || 'text',
    activity: Boolean(props.defaults?.activity ?? true),
    required: Boolean(props.defaults?.required ?? false),
    readonly: Boolean(props.defaults?.readonly ?? false),
    disabled: Boolean(props.defaults?.disabled ?? false),
    sort: Number(props.defaults?.sort ?? 100),
    default_value: props.defaults?.default_value ?? '',
    validation: props.defaults?.validation ?? null,
    width: props.defaults?.width || 'full',
    settings: props.defaults?.settings ?? null,
    translations: {
        [defaultLocale]: makeTranslation(),
    },
})

const validationJson = ref(
    form.validation ? JSON.stringify(form.validation, null, 2) : ''
)

const settingsJson = ref(
    form.settings ? JSON.stringify(form.settings, null, 2) : ''
)

const ensureTranslation = (localeCode) => {
    if (!localeCode) return
    if (!form.translations[localeCode]) form.translations[localeCode] = makeTranslation()
}

watch(activeLocale, ensureTranslation, { immediate: true })

const currentTranslation = computed(() => form.translations[activeLocale.value])

const getTranslationError = (key) => {
    return form.errors[`translations.${activeLocale.value}.${key}`]
}

/* ===================== Type ===================== */

const fieldTypeConfig = computed(() => props.fieldTypes?.[form.type] || {})

const fieldTypeLabel = (type, config) => {
    if (typeof config === 'string') return config
    return config?.label || config?.title || config?.name || type
}

const fieldWidthLabel = (width, config) => {
    if (typeof config === 'string') return config
    return config?.label || config?.title || config?.name || width
}

const supportsOptions = computed(() => Boolean(fieldTypeConfig.value?.has_options))
const supportsMultiple = computed(() => Boolean(fieldTypeConfig.value?.supports_multiple))
const isFileField = computed(() => form.type === 'file')
const isHiddenField = computed(() => form.type === 'hidden')

/* ===================== JSON ===================== */

const validationJsonError = ref('')
const settingsJsonError = ref('')

const parseJsonValue = (value, errorRef, label) => {
    errorRef.value = ''
    const text = String(value || '').trim()
    if (!text) return null

    try {
        const parsed = JSON.parse(text)

        if (parsed === null || typeof parsed !== 'object' || Array.isArray(parsed) === false && typeof parsed !== 'object') {
            errorRef.value = `${label} должны содержать корректный JSON-массив или JSON-объект.`
            return undefined
        }

        return parsed
    } catch {
        errorRef.value = `${label} содержат некорректный JSON.`
        return undefined
    }
}

/* ===================== Submit ===================== */

const submitForm = () => {
    const validation = parseJsonValue(validationJson.value, validationJsonError, 'Правила валидации')
    const settings = parseJsonValue(settingsJson.value, settingsJsonError, 'Настройки поля')

    if (validation === undefined || settings === undefined) {
        toast.error('Проверьте JSON в правилах валидации и настройках поля.')
        return
    }

    form.transform((data) => ({
        ...data,
        form_id: Number(data.form_id),
        sort: Number(data.sort ?? 0),
        activity: data.activity ? 1 : 0,
        required: data.required ? 1 : 0,
        readonly: data.readonly ? 1 : 0,
        disabled: data.disabled ? 1 : 0,
        default_value: data.default_value || null,
        validation,
        settings,
    }))

    form.post(route('admin.formFields.store'), {
        errorBag: 'createFormField',
        preserveScroll: true,
        onSuccess: () => toast.success('Поле формы успешно создано.'),
        onError: (errors) => {
            const firstKey = Object.keys(errors || {})[0]
            toast.error(errors[firstKey] || 'Проверьте корректность заполнения поля формы.')
        },
    })
}
</script>

<template>
    <AdminLayout title="Создание поля формы">
        <template #header>
            <TitlePage>{{ t('addFormField') }}</TitlePage>
        </template>

        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto">
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 shadow-lg shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95">

                <!-- Back -->
                <div class="sm:flex sm:justify-between sm:items-center mb-2">
                    <DefaultButton :href="route('admin.formFields.index')">
                        <template #icon>
                            <svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2" viewBox="0 0 16 16">
                                <path
                                    d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z" />
                            </svg>
                        </template>
                        {{ t('back') }}
                    </DefaultButton>
                </div>

                <form class="p-3 w-full" @submit.prevent="submitForm">

                    <!-- Activity / Sort -->
                    <div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4">
                        <div class="flex flex-row items-center gap-2">
                            <ActivityCheckbox v-model="form.activity" />
                            <LabelCheckbox for="activity" :text="t('activity')" class="text-sm h-8 flex items-center" />
                        </div>

                        <div class="flex flex-row items-center gap-2">
                            <LabelInput for="sort" :value="t('sort')" class="text-sm" />
                            <InputNumber id="sort" v-model.number="form.sort" type="number" min="0"
                                         class="w-full lg:w-28" />
                            <InputError :message="form.errors.sort" />
                        </div>
                    </div>

                    <!-- Form -->
                    <div class="mb-3 flex flex-col items-start">
                        <LabelInput for="form_id">
                            <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                            Форма
                        </LabelInput>

                        <select id="form_id" v-model="form.form_id" required
                                class="w-full px-2 py-1 form-select bg-white text-gray-600 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm dark:bg-cyan-800 dark:text-slate-100">
                            <option value="" disabled>Выберите форму</option>
                            <option v-for="item in formsList" :key="item.id" :value="item.id">
                                {{ formTitle(item) }} [ID: {{ item.id }}]
                            </option>
                        </select>

                        <InputError class="mt-2" :message="form.errors.form_id" />
                    </div>

                    <!-- Name / Type / Width -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 mb-3">
                        <div class="flex flex-col items-start">
                            <LabelInput for="name">
                                <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                Системное имя
                            </LabelInput>

                            <InputText id="name" v-model="form.name" type="text" maxlength="100"
                                       autocomplete="off" placeholder="email" required />

                            <div class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">
                                Строчные латинские буквы, цифры и символ _. Имя должно начинаться с буквы.
                            </div>

                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="type">
                                <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                Тип поля
                            </LabelInput>

                            <select id="type" v-model="form.type" required
                                    class="w-full px-2 py-1 form-select bg-white text-gray-600 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm dark:bg-cyan-800 dark:text-slate-100">
                                <option v-for="(config, type) in fieldTypes" :key="type" :value="type">
                                    {{ fieldTypeLabel(type, config) }} [{{ type }}]
                                </option>
                            </select>

                            <div class="mt-1 flex flex-wrap gap-1">
                                <span v-if="supportsOptions"
                                      class="text-[10px] px-2 py-0.5 rounded-sm border border-indigo-300 bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">
                                    Поддерживает варианты
                                </span>
                                <span v-if="supportsMultiple"
                                      class="text-[10px] px-2 py-0.5 rounded-sm border border-cyan-300 bg-cyan-50 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300">
                                    Множественное значение
                                </span>
                            </div>

                            <InputError class="mt-2" :message="form.errors.type" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput for="width">
                                <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                Ширина
                            </LabelInput>

                            <select id="width" v-model="form.width" required
                                    class="w-full px-2 py-1 form-select bg-white text-gray-600 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm dark:bg-cyan-800 dark:text-slate-100">
                                <option v-for="(config, width) in fieldWidths" :key="width" :value="width">
                                    {{ fieldWidthLabel(width, config) }}
                                </option>
                            </select>

                            <InputError class="mt-2" :message="form.errors.width" />
                        </div>
                    </div>

                    <!-- State -->
                    <div
                        class="mb-4 p-3 border border-slate-300 dark:border-slate-500 bg-white dark:bg-slate-800 rounded-sm">
                        <div class="text-sm font-semibold text-slate-700 dark:text-slate-100 mb-3">
                            Состояние поля
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <label
                                class="flex items-center gap-2 cursor-pointer text-sm text-slate-700 dark:text-slate-200">
                                <input v-model="form.required" type="checkbox" class="rounded border-slate-400" />
                                <span>Обязательное (required)</span>
                            </label>

                            <label
                                class="flex items-center gap-2 cursor-pointer text-sm text-slate-700 dark:text-slate-200">
                                <input v-model="form.readonly" type="checkbox" class="rounded border-slate-400" />
                                <span>Только чтение (readonly)</span>
                            </label>

                            <label
                                class="flex items-center gap-2 cursor-pointer text-sm text-slate-700 dark:text-slate-200">
                                <input v-model="form.disabled" type="checkbox" class="rounded border-slate-400" />
                                <span>Отключено (disabled)</span>
                            </label>
                        </div>

                        <div class="mt-2">
                            <InputError :message="form.errors.required" />
                            <InputError :message="form.errors.readonly" />
                            <InputError :message="form.errors.disabled" />
                        </div>
                    </div>

                    <!-- Translations -->
                    <div
                        class="my-5 p-3 border border-slate-300 dark:border-slate-500 bg-white dark:bg-slate-800 rounded-sm">
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
                            <LabelInput for="label">
                                <span v-if="!isHiddenField"
                                      class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                Название поля [{{ activeLocale.toUpperCase() }}]
                            </LabelInput>

                            <InputText id="label" v-model="currentTranslation.label" type="text" maxlength="255"
                                       autocomplete="off" :required="!isHiddenField" />

                            <InputError class="mt-2" :message="getTranslationError('label')" />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput for="placeholder" :value="`Placeholder [${activeLocale.toUpperCase()}]`" />
                            <InputText id="placeholder" v-model="currentTranslation.placeholder" type="text"
                                       maxlength="255" autocomplete="off" />
                            <InputError class="mt-2" :message="getTranslationError('placeholder')" />
                        </div>

                        <div class="mb-3 flex flex-col items-start">
                            <LabelInput for="description"
                                        :value="`${t('description')} [${activeLocale.toUpperCase()}]`" />
                            <MetaDescTextarea id="description" v-model="currentTranslation.description" />
                            <InputError class="mt-2" :message="getTranslationError('description')" />
                        </div>
                    </div>

                    <!-- Default value -->
                    <div class="mb-4 flex flex-col items-start">
                        <LabelInput for="default_value" value="Значение по умолчанию" />
                        <MetaDescTextarea id="default_value" v-model="form.default_value" />
                        <InputError class="mt-2" :message="form.errors.default_value" />
                    </div>

                    <!-- Validation -->
                    <div
                        class="mb-4 p-3 border border-slate-300 dark:border-slate-500 bg-white dark:bg-slate-800 rounded-sm">
                        <LabelInput for="validation_json" value="Правила валидации" />

                        <textarea id="validation_json" v-model="validationJson" rows="6"
                                  class="w-full mt-1 px-3 py-2 font-mono text-xs bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-100 border border-slate-400 dark:border-slate-500 rounded-sm"
                                  placeholder='["string", "max:255"]'></textarea>

                        <div class="mt-2 text-[11px] text-slate-500 dark:text-slate-400">
                            Разрешённые правила: {{ validationRules.join(', ') || 'не заданы' }}
                        </div>

                        <div v-if="validationJsonError" class="mt-2 text-sm text-red-600 dark:text-red-300">
                            {{ validationJsonError }}
                        </div>

                        <InputError class="mt-2" :message="form.errors.validation" />
                    </div>

                    <!-- Settings -->
                    <div
                        class="mb-4 p-3 border border-slate-300 dark:border-slate-500 bg-white dark:bg-slate-800 rounded-sm">
                        <LabelInput for="settings_json" value="Дополнительные настройки" />

                        <textarea id="settings_json" v-model="settingsJson" rows="6"
                                  class="w-full mt-1 px-3 py-2 font-mono text-xs bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-100 border border-slate-400 dark:border-slate-500 rounded-sm"
                                  :placeholder="isFileField ? '{\n  &quot;multiple&quot;: false,\n  &quot;max_files&quot;: 5\n}' : '{}'"></textarea>

                        <div class="mt-2 text-[11px] text-slate-500 dark:text-slate-400">
                            JSON-настройки зависят от выбранного типа поля.
                            <template v-if="isFileField"> Для типа file backend дополнительно проверяет файловые
                                параметры.
                            </template>
                        </div>

                        <div v-if="settingsJsonError" class="mt-2 text-sm text-red-600 dark:text-red-300">
                            {{ settingsJsonError }}
                        </div>

                        <InputError class="mt-2" :message="form.errors.settings" />
                        <InputError class="mt-1" :message="form.errors['settings.multiple']" />
                        <InputError class="mt-1" :message="form.errors['settings.max_files']" />
                    </div>

                    <!-- Submit -->
                    <div class="flex justify-end mt-5">
                        <PrimaryButton type="submit" :disabled="form.processing"
                                       :class="{ 'opacity-50 cursor-not-allowed': form.processing }">
                            {{ form.processing ? 'Сохранение...' : t('save') }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
