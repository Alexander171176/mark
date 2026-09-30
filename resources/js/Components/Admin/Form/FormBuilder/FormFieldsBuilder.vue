<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Конструктор полей динамической формы.
 * Используется в создании и редактировании формы.
 */

import { ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import ActivityCheckbox from '@/Components/Admin/UI/Checkbox/ActivityCheckbox.vue'
import LabelInput from '@/Components/Admin/UI/Input/LabelInput.vue'
import InputText from '@/Components/Admin/UI/Input/InputText.vue'
import InputNumber from '@/Components/Admin/UI/Input/InputNumber.vue'
import InputError from '@/Components/Admin/UI/Input/InputError.vue'
import MetaDescTextarea from '@/Components/Admin/UI/Textarea/MetaDescTextarea.vue'
import FormFieldOptionsBuilder from '@/Components/Admin/Form/FormBuilder/FormFieldOptionsBuilder.vue'

const { t } = useI18n()

const props = defineProps({
    modelValue: { type: Array, default: () => [] },
    activeLocale: { type: String, default: 'ru' },
    fieldTypes: { type: Object, default: () => ({}) },
    fieldWidths: { type: Object, default: () => ({}) },
    errors: { type: Object, default: () => ({}) },
    locales: { type: Array, default: () => [] }
})

const emit = defineEmits(['update:modelValue'])

/* ===================== Collapse ===================== */

const collapsedFields = ref(new Set())

const fieldKey = (field, index) => {
    return field.id ? `id-${field.id}` : `new-${index}`
}

const isCollapsed = (field, index) => {
    return collapsedFields.value.has(fieldKey(field, index))
}

const toggleField = (field, index) => {
    const key = fieldKey(field, index)
    const next = new Set(collapsedFields.value)

    if (next.has(key)) next.delete(key)
    else next.add(key)

    collapsedFields.value = next
}

const collapseAll = () => {
    collapsedFields.value = new Set(
        props.modelValue.map((field, index) => fieldKey(field, index))
    )
}

const expandAll = () => {
    collapsedFields.value = new Set()
}

const makeFieldTranslation = () => ({
    label: '',
    placeholder: '',
    description: ''
})

const makeField = () => ({
    id: null,
    _delete: false,
    name: '',
    type: 'text',
    activity: true,
    required: false,
    readonly: false,
    disabled: false,
    sort: 100,
    default_value: '',
    validation: '',
    width: 'full',
    settings: '',
    translations: {
        [props.activeLocale || 'ru']: makeFieldTranslation()
    },
    options: []
})

const ensureFieldTranslation = (field, localeCode) => {
    if (!field || !localeCode) return

    if (!field.translations || typeof field.translations !== 'object') {
        field.translations = {}
    }

    if (!field.translations[localeCode]) {
        field.translations[localeCode] = makeFieldTranslation()
    }
}

const ensureFieldOptions = (field) => {
    if (!Array.isArray(field.options)) {
        field.options = []
    }
}

watch(
    () => props.modelValue,
    (fields) => {
        fields.forEach(field => {
            ensureFieldTranslation(field, props.activeLocale)
            ensureFieldOptions(field)
        })
    },
    { immediate: true }
)

watch(
    () => props.activeLocale,
    (localeCode) => {
        props.modelValue.forEach(field => {
            ensureFieldTranslation(field, localeCode)
        })
    },
    { immediate: true }
)

const addField = () => {
    const fields = [...props.modelValue]
    const field = makeField()

    props.locales.forEach(localeCode => ensureFieldTranslation(field, localeCode))

    field.sort = fields.length
        ? Math.max(...fields.map(item => Number(item.sort || 0))) + 100
        : 100

    fields.push(field)
    emit('update:modelValue', fields)

    // Новое поле всегда показываем развёрнутым.
    const next = new Set(collapsedFields.value)
    next.delete(fieldKey(field, fields.length - 1))
    collapsedFields.value = next
}

const removeField = (index) => {
    const fields = [...props.modelValue]
    const field = fields[index]
    const key = fieldKey(field, index)

    // Новое, ещё не сохранённое поле просто удаляем из массива.
    if (!field.id) {
        fields.splice(index, 1)

        const next = new Set(collapsedFields.value)
        next.delete(key)
        collapsedFields.value = next

        emit('update:modelValue', fields)
        return
    }

    // Существующее поле оставляем в payload и помечаем на удаление.
    fields[index] = {
        ...field,
        _delete: true
    }

    emit('update:modelValue', fields)
}

const restoreField = (index) => {
    const fields = [...props.modelValue]

    fields[index] = {
        ...fields[index],
        _delete: false
    }

    emit('update:modelValue', fields)
}

const moveField = (index, direction) => {
    const fields = [...props.modelValue]
    const target = index + direction

    if (target < 0 || target >= fields.length) return

    const current = fields[index]
    fields[index] = fields[target]
    fields[target] = current

    fields.forEach((field, fieldIndex) => {
        field.sort = (fieldIndex + 1) * 100
    })

    emit('update:modelValue', fields)
}

const fieldTranslation = (field) => {
    ensureFieldTranslation(field, props.activeLocale)
    return field.translations[props.activeLocale]
}

const fieldError = (index, key) => {
    return props.errors[`fields.${index}.${key}`]
}

const fieldTranslationError = (index, key) => {
    return props.errors[`fields.${index}.translations.${props.activeLocale}.${key}`]
}

const fieldTypeLabel = (type, config) => {
    if (typeof config === 'string') return config
    return config?.label || config?.name || type
}

const fieldWidthLabel = (width, config) => {
    if (typeof config === 'string') return config
    return config?.label || config?.name || width
}

const supportsOptions = (field) => {
    return Boolean(props.fieldTypes?.[field.type]?.has_options)
}

const supportsDefaultValue = (field) => {
    return field.type !== 'file' && !supportsOptions(field)
}
</script>

<template>
    <div class="my-5 p-3 border border-slate-300 dark:border-slate-500
                bg-white dark:bg-slate-800 rounded-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
            <div>
                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                    {{ t('formFields') }}
                </h3>

                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                    {{ t('formFieldsDesc') }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <button
                    v-if="modelValue.length"
                    type="button"
                    @click="expandAll"
                    class="px-3 py-1.5 text-xs font-semibold rounded-sm border
                           border-slate-400 dark:border-slate-500
                           text-slate-700 dark:text-slate-200
                           hover:bg-slate-100 dark:hover:bg-slate-950"
                >
                    {{ t('expandAll') }}
                </button>

                <button
                    v-if="modelValue.length"
                    type="button"
                    @click="collapseAll"
                    class="px-3 py-1.5 text-xs font-semibold rounded-sm border
                           border-slate-400 dark:border-slate-500
                           text-slate-700 dark:text-slate-200
                           hover:bg-slate-100 dark:hover:bg-slate-950"
                >
                    {{ t('collapseAll') }}
                </button>

                <button
                    type="button"
                    @click="addField"
                    class="inline-flex items-center justify-center px-2 py-1
                           text-sm font-semibold rounded-sm text-white bg-blue-600
                           hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600"
                >
                    + {{ t('addFormField') }}
                </button>
            </div>
        </div>

        <InputError class="mb-3" :message="errors.fields" />

        <div
            v-if="!modelValue.length"
            class="py-8 bg-slate-100 dark:bg-slate-900
                   text-center text-sm text-slate-500 dark:text-slate-400
                   border border-dashed border-slate-400 dark:border-slate-600 rounded-sm"
        >
            {{ t('formDescription') }}
        </div>

        <div
            v-for="(field, index) in modelValue"
            :key="field.id ?? `new-${index}`"
            class="mb-4 last:mb-0 border border-slate-400 dark:border-slate-600
                   bg-slate-100 dark:bg-slate-900 rounded-sm"
        >
            <!-- Поле отмечено на удаление -->
            <div
                v-if="field._delete"
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3
                       p-3 bg-red-50 dark:bg-red-950/30"
            >
                <div>
                    <div class="text-sm font-semibold text-red-700 dark:text-red-300">
                        {{ fieldTranslation(field)?.label || field.name || t('newField') }}
                    </div>

                    <div class="mt-1 text-xs text-red-500 dark:text-red-400">
                        {{ field.name }} · {{ field.type }} · ID: {{ field.id }}
                    </div>
                </div>

                <button
                    type="button"
                    @click="restoreField(index)"
                    class="px-3 py-1.5 text-xs font-semibold rounded-sm
                           border border-red-400 dark:border-red-500
                           text-red-700 dark:text-red-300
                           hover:bg-red-100 dark:hover:bg-red-950"
                >
                    {{ t('undoDeletion') }}
                </button>
            </div>

            <template v-else>
                <!-- Заголовок поля -->
                <div class="flex flex-col bg-slate-50 dark:bg-slate-700
                        lg:flex-row lg:items-center lg:justify-between gap-3 p-3
                        border-b border-slate-300 dark:border-slate-600">
                    <button
                        type="button"
                        @click="toggleField(field, index)"
                        class="flex items-center gap-3 text-left min-w-0"
                        :title="isCollapsed(field, index) ? t('expand') : t('collapse')"
                    >
                    <span class="flex items-center justify-center w-7 h-7 rounded-full
                                 bg-slate-200 dark:bg-slate-900 text-xs font-semibold
                                 text-slate-700 dark:text-slate-100 shrink-0">
                        {{ index + 1 }}
                    </span>

                        <div class="min-w-0">
                            <div class="text-sm font-semibold text-slate-800 dark:text-slate-100 truncate">
                                {{ fieldTranslation(field)?.label || field.name || t('newField') }}
                            </div>

                            <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                                {{ field.name || 'system_name' }} · {{ field.type }}
                            </div>
                        </div>
                    </button>

                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            @click="toggleField(field, index)"
                            class="flex items-center justify-center text-xs
                               bg-white dark:bg-slate-800 w-8 h-8
                               border border-slate-400 dark:border-slate-500 rounded-sm
                               text-slate-700 dark:text-slate-200
                               hover:bg-slate-100 dark:hover:bg-slate-950"
                            :title="isCollapsed(field, index) ? t('expand') : t('collapse')"
                        >
                            <svg
                                class="w-4 h-4 fill-current transition-transform duration-200"
                                :class="{ 'rotate-180': !isCollapsed(field, index) }"
                                viewBox="0 0 16 16"
                            >
                                <path d="M8 11 2.5 5.5 3.9 4.1 8 8.2l4.1-4.1 1.4 1.4z" />
                            </svg>
                        </button>
                        <button
                            type="button"
                            @click="moveField(index, -1)"
                            :disabled="index === 0"
                            class="px-3 py-1 text-sm bg-white dark:bg-slate-800
                               border border-slate-400 dark:border-slate-500 rounded-sm
                               hover:bg-slate-100 dark:hover:bg-slate-950
                               disabled:opacity-30 text-slate-700 dark:text-slate-200"
                        >
                            ↑
                        </button>

                        <button
                            type="button"
                            @click="moveField(index, 1)"
                            :disabled="index === modelValue.length - 1"
                            class="px-3 py-1 text-sm bg-white dark:bg-slate-800
                               border border-slate-400 dark:border-slate-500 rounded-sm
                               hover:bg-slate-100 dark:hover:bg-slate-950
                               disabled:opacity-30 text-slate-700 dark:text-slate-200"
                        >
                            ↓
                        </button>

                        <button
                            type="button"
                            @click="removeField(index)"
                            class="px-2 py-1 flex flex-row items-center justify-center gap-1 rounded-sm
                               border border-red-400 dark:border-red-500
                               hover:bg-red-50 dark:hover:bg-red-950"
                        >
                            <svg
                                class="shrink-0 h-3 w-3"
                                viewBox="0 0 448 512">
                                <path
                                    class="fill-current text-red-600 dark:text-red-300"
                                    d="M0 84V56c0-13.3 10.7-24 24-24h112l9.4-18.7c4-8.2 12.3-13.3 21.4-13.3h114.3c9.1 0 17.4 5.1 21.5 13.3L312 32h112c13.3 0 24 10.7 24 24v28c0 6.6-5.4 12-12 12H12C5.4 96 0 90.6 0 84zm416 56v324c0 26.5-21.5 48-48 48H80c-26.5 0-48-21.5-48-48V140c0-6.6 5.4-12 12-12h360c6.6 0 12 5.4 12 12zm-272 68c0-8.8-7.2-16-16-16s-16 7.2-16 16v224c0 8.8 7.2 16 16 16s16-7.2 16-16V208zm96 0c0-8.8-7.2-16-16-16s-16 7.2-16 16v224c0 8.8 7.2 16 16 16s16-7.2 16-16V208zm96 0c0-8.8-7.2-16-16-16s-16 7.2-16 16v224c0 8.8 7.2 16 16 16s16-7.2 16-16V208z" />
                            </svg>
                            <span class="text-sm font-semibold text-red-600 dark:text-red-300">
                            {{ t('remove') }}
                        </span>
                        </button>
                    </div>
                </div>

                <div v-show="!isCollapsed(field, index)" class="p-3">
                    <!-- Основные параметры -->
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3">
                        <div class="flex flex-col items-start">
                            <LabelInput :for="`field_name_${index}`">
                                <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                {{ t('systemName') }}
                            </LabelInput>

                            <InputText
                                :id="`field_name_${index}`"
                                v-model="field.name"
                                type="text"
                                maxlength="100"
                                autocomplete="off"
                                placeholder="email"
                            />

                            <InputError class="mt-2" :message="fieldError(index, 'name')" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput :for="`field_type_${index}`" :value="t('type')" />

                            <select
                                :id="`field_type_${index}`"
                                v-model="field.type"
                                class="w-full px-2 py-0.5 form-select bg-white text-gray-600
                                   border border-slate-400 dark:border-slate-600 rounded-sm
                                   shadow-sm dark:bg-cyan-800 dark:text-slate-100"
                            >
                                <option
                                    v-for="(config, type) in fieldTypes"
                                    :key="type"
                                    :value="type"
                                >
                                    {{ fieldTypeLabel(type, config) }}
                                </option>
                            </select>

                            <InputError class="mt-2" :message="fieldError(index, 'type')" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput :for="`field_width_${index}`" :value="t('width')" />

                            <select
                                :id="`field_width_${index}`"
                                v-model="field.width"
                                class="w-full px-2 py-0.5 form-select bg-white text-gray-600
                                   border border-slate-400 dark:border-slate-600 rounded-sm
                                   shadow-sm dark:bg-cyan-800 dark:text-slate-100"
                            >
                                <option
                                    v-for="(config, width) in fieldWidths"
                                    :key="width"
                                    :value="width"
                                >
                                    {{ fieldWidthLabel(width, config) }}
                                </option>
                            </select>

                            <InputError class="mt-2" :message="fieldError(index, 'width')" />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput :for="`field_sort_${index}`" :value="t('sort')" />

                            <InputNumber
                                :id="`field_sort_${index}`"
                                v-model.number="field.sort"
                                type="number"
                                min="0"
                            />

                            <InputError class="mt-2" :message="fieldError(index, 'sort')" />
                        </div>
                    </div>

                    <!-- Состояния -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 my-4 p-3
                            bg-white dark:bg-slate-800 border border-slate-200
                            dark:border-slate-600 rounded-sm">

                        <label class="flex items-center gap-2 cursor-pointer">
                            <ActivityCheckbox v-model="field.activity" />
                            <span class="text-sm text-slate-700 dark:text-slate-200">
                            {{ t('actively') }}
                        </span>
                        </label>

                        <label class="flex items-center gap-2 cursor-pointer">
                            <ActivityCheckbox v-model="field.required" />
                            <span class="text-sm text-slate-700 dark:text-slate-200">
                            {{ t('required') }}
                        </span>
                        </label>

                        <label class="flex items-center gap-2 cursor-pointer">
                            <ActivityCheckbox v-model="field.readonly" />
                            <span class="text-sm text-slate-700 dark:text-slate-200">
                            {{ t('readOnly') }}
                        </span>
                        </label>

                        <label class="flex items-center gap-2 cursor-pointer">
                            <ActivityCheckbox v-model="field.disabled" />
                            <span class="text-sm text-slate-700 dark:text-slate-200">
                            {{ t('disabled') }}
                        </span>
                        </label>
                    </div>

                    <InputError :message="fieldError(index, 'activity')" />
                    <InputError :message="fieldError(index, 'required')" />
                    <InputError :message="fieldError(index, 'readonly')" />
                    <InputError :message="fieldError(index, 'disabled')" />

                    <!-- Перевод -->
                    <div class="p-3 my-4 border border-blue-200 dark:border-blue-800
                            bg-blue-50/40 dark:bg-slate-800 rounded-sm">
                        <div class="mb-3 text-xs font-semibold text-slate-700 dark:text-slate-200">
                            {{ t('fieldTranslation') }} [{{ activeLocale.toUpperCase() }}]
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                            <div class="flex flex-col items-start">
                                <LabelInput :for="`field_label_${index}`">
                                <span
                                    v-if="field.type !== 'hidden'"
                                    class="text-red-500 dark:text-red-300 font-semibold"
                                >
                                    *
                                </span>
                                    {{ t('label') }}
                                </LabelInput>

                                <InputText
                                    :id="`field_label_${index}`"
                                    v-model="fieldTranslation(field).label"
                                    type="text"
                                    maxlength="255"
                                    autocomplete="off"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="fieldTranslationError(index, 'label')"
                                />
                            </div>

                            <div class="flex flex-col items-start">
                                <LabelInput
                                    :for="`field_placeholder_${index}`"
                                    value="Placeholder"
                                />

                                <InputText
                                    :id="`field_placeholder_${index}`"
                                    v-model="fieldTranslation(field).placeholder"
                                    type="text"
                                    maxlength="255"
                                    autocomplete="off"
                                />

                                <InputError
                                    class="mt-2"
                                    :message="fieldTranslationError(index, 'placeholder')"
                                />
                            </div>
                        </div>

                        <div class="mt-3 flex flex-col items-start">
                            <LabelInput
                                :for="`field_description_${index}`"
                                :value="t('fieldDescription')"
                            />

                            <MetaDescTextarea
                                :id="`field_description_${index}`"
                                v-model="fieldTranslation(field).description"
                                class="w-full"
                            />

                            <InputError
                                class="mt-2"
                                :message="fieldTranslationError(index, 'description')"
                            />
                        </div>
                    </div>

                    <!-- Варианты значений поля -->
                    <FormFieldOptionsBuilder
                        v-if="supportsOptions(field)"
                        v-model="field.options"
                        :field-index="index"
                        :field-type="field.type"
                        :field-type-config="fieldTypes[field.type] || {}"
                        :active-locale="activeLocale"
                        :locales="locales"
                        :errors="errors"
                    />

                    <!-- Значение по умолчанию -->
                    <div
                        v-if="supportsDefaultValue(field)"
                        class="mb-3 flex flex-col items-start"
                    >
                        <LabelInput
                            :for="`field_default_${index}`"
                            :value="t('defaultValue')"
                        />

                        <InputText
                            :id="`field_default_${index}`"
                            v-model="field.default_value"
                            type="text"
                            autocomplete="off"
                        />

                        <InputError
                            class="mt-2"
                            :message="fieldError(index, 'default_value')"
                        />
                    </div>

                    <!-- JSON -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                        <div class="flex flex-col items-start">
                            <LabelInput
                                :for="`field_validation_${index}`"
                                :value="t('validationRulesJSON')"
                            />

                            <MetaDescTextarea
                                :id="`field_validation_${index}`"
                                v-model="field.validation"
                                class="w-full"
                                placeholder='["string","max:255"]'
                            />

                            <InputError
                                class="mt-2"
                                :message="fieldError(index, 'validation')"
                            />
                        </div>

                        <div class="flex flex-col items-start">
                            <LabelInput
                                :for="`field_settings_${index}`"
                                :value="`${t('fieldSettings')} (JSON)`"
                            />

                            <MetaDescTextarea
                                :id="`field_settings_${index}`"
                                v-model="field.settings"
                                class="w-full"
                                placeholder='{"multiple":false}'
                            />

                            <InputError
                                class="mt-2"
                                :message="fieldError(index, 'settings')"
                            />
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>
