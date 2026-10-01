<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Редактор отдельного поля динамической формы.
 *
 * Используется на странице FormFields/Edit.vue.
 *
 * В отличие от FormFieldsBuilder:
 * - работает с одним полем;
 * - не управляет массивом fields;
 * - не добавляет и не удаляет поля;
 * - не изменяет порядок полей;
 * - ошибки ожидаются напрямую в свойствах поля;
 * - варианты значений редактируются через FormFieldOptionsEditor;
 * - modelValue не мутируется напрямую.
 */

import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import ActivityCheckbox from '@/Components/Admin/UI/Checkbox/ActivityCheckbox.vue'
import LabelInput from '@/Components/Admin/UI/Input/LabelInput.vue'
import InputText from '@/Components/Admin/UI/Input/InputText.vue'
import InputNumber from '@/Components/Admin/UI/Input/InputNumber.vue'
import InputError from '@/Components/Admin/UI/Input/InputError.vue'
import MetaDescTextarea from '@/Components/Admin/UI/Textarea/MetaDescTextarea.vue'
import FormFieldOptionsEditor from '@/Components/Admin/Form/FormBuilder/FormFieldOptionsEditor.vue'

const { t } = useI18n()

const props = defineProps({
    modelValue: { type: Object, required: true },
    activeLocale: { type: String, default: 'ru' },
    fieldTypes: { type: Object, default: () => ({}) },
    fieldWidths: { type: Object, default: () => ({}) },
    errors: { type: Object, default: () => ({}) },
    locales: { type: Array, default: () => [] }
})

const emit = defineEmits(['update:modelValue'])

/* ===================== Field ===================== */

const updateField = (key, value) => {
    emit('update:modelValue', {
        ...props.modelValue,
        [key]: value
    })
}

/* ===================== Translation ===================== */

const makeFieldTranslation = () => ({
    label: '',
    placeholder: '',
    description: ''
})

const fieldTranslation = computed(() => {
    return props.modelValue.translations?.[props.activeLocale]
        || makeFieldTranslation()
})

const updateTranslation = (key, value) => {
    emit('update:modelValue', {
        ...props.modelValue,
        translations: {
            ...(props.modelValue.translations || {}),
            [props.activeLocale]: {
                ...fieldTranslation.value,
                [key]: value
            }
        }
    })
}

/* ===================== Options ===================== */

const fieldOptions = computed({
    get: () => {
        return Array.isArray(props.modelValue.options)
            ? props.modelValue.options
            : []
    },
    set: options => {
        emit('update:modelValue', {
            ...props.modelValue,
            options
        })
    }
})

/* ===================== Errors ===================== */

const fieldError = key => {
    return props.errors[key]
}

const fieldTranslationError = key => {
    return props.errors[
        `translations.${props.activeLocale}.${key}`
        ]
}

/* ===================== Config ===================== */

const fieldTypeLabel = (type, config) => {
    if (typeof config === 'string') return config
    return config?.label || config?.name || type
}

const fieldWidthLabel = (width, config) => {
    if (typeof config === 'string') return config
    return config?.label || config?.name || width
}

const supportsOptions = computed(() => {
    return Boolean(
        props.fieldTypes?.[props.modelValue.type]?.has_options
    )
})

const supportsDefaultValue = computed(() => {
    return props.modelValue.type !== 'file'
        && !supportsOptions.value
})

/* ===================== HTML ID ===================== */

const fieldInputId = key => {
    return `form_field_${props.modelValue.id || 'edit'}_${key}`
}
</script>

<template>
    <div class="my-5 p-3 border border-slate-300 dark:border-slate-500
                bg-white dark:bg-slate-800 rounded-sm">

        <!-- Основные параметры -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3">

            <!-- Системное имя -->
            <div class="flex flex-col items-start">
                <LabelInput :for="fieldInputId('name')">
                    <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                    {{ t('systemName') }}
                </LabelInput>

                <InputText
                    :id="fieldInputId('name')"
                    :model-value="modelValue.name"
                    type="text"
                    maxlength="100"
                    autocomplete="off"
                    placeholder="email"
                    @update:model-value="updateField('name', $event)"
                />

                <InputError class="mt-2" :message="fieldError('name')" />
            </div>

            <!-- Тип -->
            <div class="flex flex-col items-start">
                <LabelInput
                    :for="fieldInputId('type')"
                    :value="t('type')"
                />

                <select
                    :id="fieldInputId('type')"
                    :value="modelValue.type"
                    @change="updateField('type', $event.target.value)"
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

                <InputError class="mt-2" :message="fieldError('type')" />
            </div>

            <!-- Ширина -->
            <div class="flex flex-col items-start">
                <LabelInput
                    :for="fieldInputId('width')"
                    :value="t('width')"
                />

                <select
                    :id="fieldInputId('width')"
                    :value="modelValue.width"
                    @change="updateField('width', $event.target.value)"
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

                <InputError class="mt-2" :message="fieldError('width')" />
            </div>

            <!-- Сортировка -->
            <div class="flex flex-col items-start">
                <LabelInput
                    :for="fieldInputId('sort')"
                    :value="t('sort')"
                />

                <InputNumber
                    :id="fieldInputId('sort')"
                    :model-value="modelValue.sort"
                    type="number"
                    min="0"
                    @update:model-value="updateField('sort', Number($event))"
                />

                <InputError class="mt-2" :message="fieldError('sort')" />
            </div>
        </div>

        <!-- Состояния -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 my-4 p-3
                    bg-white dark:bg-slate-800 border border-slate-200
                    dark:border-slate-600 rounded-sm">

            <label class="flex items-center gap-2 cursor-pointer">
                <ActivityCheckbox
                    :model-value="modelValue.activity"
                    @update:model-value="updateField('activity', $event)"
                />

                <span class="text-sm text-slate-700 dark:text-slate-200">
                    {{ t('actively') }}
                </span>
            </label>

            <label class="flex items-center gap-2 cursor-pointer">
                <ActivityCheckbox
                    :model-value="modelValue.required"
                    @update:model-value="updateField('required', $event)"
                />

                <span class="text-sm text-slate-700 dark:text-slate-200">
                    {{ t('required') }}
                </span>
            </label>

            <label class="flex items-center gap-2 cursor-pointer">
                <ActivityCheckbox
                    :model-value="modelValue.readonly"
                    @update:model-value="updateField('readonly', $event)"
                />

                <span class="text-sm text-slate-700 dark:text-slate-200">
                    {{ t('readOnly') }}
                </span>
            </label>

            <label class="flex items-center gap-2 cursor-pointer">
                <ActivityCheckbox
                    :model-value="modelValue.disabled"
                    @update:model-value="updateField('disabled', $event)"
                />

                <span class="text-sm text-slate-700 dark:text-slate-200">
                    {{ t('disabled') }}
                </span>
            </label>
        </div>

        <InputError :message="fieldError('activity')" />
        <InputError :message="fieldError('required')" />
        <InputError :message="fieldError('readonly')" />
        <InputError :message="fieldError('disabled')" />

        <!-- Перевод -->
        <div class="p-3 my-4 border border-blue-200 dark:border-blue-800
                    bg-blue-50/40 dark:bg-slate-800 rounded-sm">

            <div class="mb-3 text-xs font-semibold text-slate-700 dark:text-slate-200">
                {{ t('fieldTranslation') }}
                [{{ activeLocale.toUpperCase() }}]
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">

                <!-- Label -->
                <div class="flex flex-col items-start">
                    <LabelInput :for="fieldInputId('label')">
                        <span
                            v-if="modelValue.type !== 'hidden'"
                            class="text-red-500 dark:text-red-300 font-semibold"
                        >
                            *
                        </span>

                        {{ t('label') }}
                    </LabelInput>

                    <InputText
                        :id="fieldInputId('label')"
                        :model-value="fieldTranslation.label"
                        type="text"
                        maxlength="255"
                        autocomplete="off"
                        @update:model-value="updateTranslation('label', $event)"
                    />

                    <InputError
                        class="mt-2"
                        :message="fieldTranslationError('label')"
                    />
                </div>

                <!-- Placeholder -->
                <div class="flex flex-col items-start">
                    <LabelInput
                        :for="fieldInputId('placeholder')"
                        value="Placeholder"
                    />

                    <InputText
                        :id="fieldInputId('placeholder')"
                        :model-value="fieldTranslation.placeholder"
                        type="text"
                        maxlength="255"
                        autocomplete="off"
                        @update:model-value="updateTranslation('placeholder', $event)"
                    />

                    <InputError
                        class="mt-2"
                        :message="fieldTranslationError('placeholder')"
                    />
                </div>
            </div>

            <!-- Description -->
            <div class="mt-3 flex flex-col items-start">
                <LabelInput
                    :for="fieldInputId('description')"
                    :value="t('fieldDescription')"
                />

                <MetaDescTextarea
                    :id="fieldInputId('description')"
                    :model-value="fieldTranslation.description"
                    class="w-full"
                    @update:model-value="updateTranslation('description', $event)"
                />

                <InputError
                    class="mt-2"
                    :message="fieldTranslationError('description')"
                />
            </div>
        </div>

        <!-- Варианты значений -->
        <FormFieldOptionsEditor
            v-if="supportsOptions"
            v-model="fieldOptions"
            :field-type="modelValue.type"
            :field-type-config="fieldTypes[modelValue.type] || {}"
            :active-locale="activeLocale"
            :locales="locales"
            :errors="errors"
        />

        <!-- Значение по умолчанию -->
        <div
            v-if="supportsDefaultValue"
            class="mb-3 flex flex-col items-start"
        >
            <LabelInput
                :for="fieldInputId('default')"
                :value="t('defaultValue')"
            />

            <InputText
                :id="fieldInputId('default')"
                :model-value="modelValue.default_value"
                type="text"
                autocomplete="off"
                @update:model-value="updateField('default_value', $event)"
            />

            <InputError
                class="mt-2"
                :message="fieldError('default_value')"
            />
        </div>

        <!-- JSON -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">

            <!-- Validation -->
            <div class="flex flex-col items-start">
                <LabelInput
                    :for="fieldInputId('validation')"
                    :value="t('validationRulesJSON')"
                />

                <MetaDescTextarea
                    :id="fieldInputId('validation')"
                    :model-value="modelValue.validation"
                    class="w-full"
                    placeholder='["string","max:255"]'
                    @update:model-value="updateField('validation', $event)"
                />

                <InputError
                    class="mt-2"
                    :message="fieldError('validation')"
                />
            </div>

            <!-- Settings -->
            <div class="flex flex-col items-start">
                <LabelInput
                    :for="fieldInputId('settings')"
                    :value="`${t('fieldSettings')} (JSON)`"
                />

                <MetaDescTextarea
                    :id="fieldInputId('settings')"
                    :model-value="modelValue.settings"
                    class="w-full"
                    placeholder='{"multiple":false}'
                    @update:model-value="updateField('settings', $event)"
                />

                <InputError
                    class="mt-2"
                    :message="fieldError('settings')"
                />
            </div>
        </div>
    </div>
</template>
