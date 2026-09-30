<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * Конструктор вариантов значений поля динамической формы.
 *
 * Используется внутри FormFieldsBuilder для типов полей,
 * поддерживающих варианты выбора:
 * - select;
 * - radio;
 * - checkbox_group;
 * - других типов с has_options=true.
 */

import { ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import ActivityCheckbox from '@/Components/Admin/UI/Checkbox/ActivityCheckbox.vue'
import LabelInput from '@/Components/Admin/UI/Input/LabelInput.vue'
import InputText from '@/Components/Admin/UI/Input/InputText.vue'
import InputNumber from '@/Components/Admin/UI/Input/InputNumber.vue'
import InputError from '@/Components/Admin/UI/Input/InputError.vue'
import MetaDescTextarea from '@/Components/Admin/UI/Textarea/MetaDescTextarea.vue'

const { t } = useI18n()

const props = defineProps({
    modelValue: { type: Array, default: () => [] },
    fieldIndex: { type: Number, required: true },
    fieldType: { type: String, required: true },
    fieldTypeConfig: { type: Object, default: () => ({}) },
    activeLocale: { type: String, default: 'ru' },
    locales: { type: Array, default: () => [] },
    errors: { type: Object, default: () => ({}) }
})

const emit = defineEmits(['update:modelValue'])

/* ===================== Collapse ===================== */

const collapsedOptions = ref(new Set())

const optionKey = (option, index) => {
    return option.id ? `id-${option.id}` : `new-${index}`
}

const isCollapsed = (option, index) => {
    return collapsedOptions.value.has(optionKey(option, index))
}

const toggleOption = (option, index) => {
    const key = optionKey(option, index)
    const next = new Set(collapsedOptions.value)

    if (next.has(key)) next.delete(key)
    else next.add(key)

    collapsedOptions.value = next
}

const collapseAll = () => {
    collapsedOptions.value = new Set(
        props.modelValue.map((option, index) => optionKey(option, index))
    )
}

const expandAll = () => {
    collapsedOptions.value = new Set()
}

/* ===================== Factory ===================== */

const makeOptionTranslation = () => ({
    label: '',
    description: ''
})

const makeOption = () => ({
    id: null,
    _delete: false,
    value: '',
    activity: true,
    is_default: false,
    sort: 100,
    settings: '',
    translations: {
        [props.activeLocale || 'ru']: makeOptionTranslation()
    }
})

/* ===================== Translations ===================== */

const ensureOptionTranslation = (option, localeCode) => {
    if (!option || !localeCode) return

    if (!option.translations || typeof option.translations !== 'object') {
        option.translations = {}
    }

    if (!option.translations[localeCode]) {
        option.translations[localeCode] = makeOptionTranslation()
    }
}

watch(
    () => props.activeLocale,
    (localeCode) => {
        props.modelValue.forEach(option => {
            ensureOptionTranslation(option, localeCode)
        })
    },
    { immediate: true }
)

const optionTranslation = (option) => {
    ensureOptionTranslation(option, props.activeLocale)

    return option.translations[props.activeLocale]
}

/* ===================== CRUD ===================== */

const addOption = () => {
    const options = [...props.modelValue]
    const option = makeOption()

    props.locales.forEach(localeCode => {
        ensureOptionTranslation(option, localeCode)
    })

    option.sort = options.length
        ? Math.max(...options.map(item => Number(item.sort || 0))) + 100
        : 100

    options.push(option)

    emit('update:modelValue', options)

    // Новый вариант всегда показываем развёрнутым.
    const next = new Set(collapsedOptions.value)
    next.delete(optionKey(option, options.length - 1))
    collapsedOptions.value = next
}

const removeOption = (index) => {
    const options = [...props.modelValue]
    const option = options[index]
    const key = optionKey(option, index)

    // Новый вариант ещё отсутствует в БД —
    // просто удаляем его из массива.
    if (!option.id) {
        options.splice(index, 1)

        const next = new Set(collapsedOptions.value)
        next.delete(key)
        collapsedOptions.value = next

        emit('update:modelValue', options)
        return
    }

    // Существующий вариант должен остаться
    // в payload с явной командой удаления.
    options[index] = {
        ...option,
        _delete: true
    }

    emit('update:modelValue', options)
}

const restoreOption = (index) => {
    const options = [...props.modelValue]

    options[index] = {
        ...options[index],
        _delete: false
    }

    emit('update:modelValue', options)
}

/* ===================== Sort ===================== */

const moveOption = (index, direction) => {
    const options = [...props.modelValue]
    const target = index + direction

    if (target < 0 || target >= options.length) return

    const current = options[index]
    options[index] = options[target]
    options[target] = current

    options.forEach((option, optionIndex) => {
        option.sort = (optionIndex + 1) * 100
    })

    emit('update:modelValue', options)
}

/* ===================== Default ===================== */

const supportsMultiple = () => {
    return Boolean(props.fieldTypeConfig?.supports_multiple)
}

const changeDefault = (index) => {
    const options = [...props.modelValue]
    const option = options[index]

    if (!option || option._delete) return

    const newValue = !option.is_default

    if (newValue && !supportsMultiple()) {
        options.forEach((item, itemIndex) => {
            if (itemIndex !== index && !item._delete) {
                item.is_default = false
            }
        })
    }

    option.is_default = newValue

    emit('update:modelValue', options)
}

/* ===================== Errors ===================== */

const optionError = (optionIndex, key) => {
    return props.errors[
        `fields.${props.fieldIndex}.options.${optionIndex}.${key}`
        ]
}

const optionTranslationError = (optionIndex, key) => {
    return props.errors[
        `fields.${props.fieldIndex}.options.${optionIndex}.translations.${props.activeLocale}.${key}`
        ]
}

/* ===================== Labels ===================== */

const optionTitle = (option) => {
    return optionTranslation(option)?.label
        || option.value
        || t('newOption')
}
</script>

<template>
    <div class="my-4 p-3 border border-teal-300 dark:border-teal-700
                bg-teal-50/30 dark:bg-slate-800 rounded-sm">

        <!-- Заголовок -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
            <div>
                <h4 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                    {{ t('fieldOptions') }}
                </h4>

                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                    {{ t('fieldOptionsDesc') }}
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
                    @click="addOption"
                    class="inline-flex items-center justify-center px-2 py-1
                           text-sm font-semibold rounded-sm text-white bg-teal-600
                           hover:bg-teal-700 dark:bg-teal-500
                           dark:hover:bg-teal-600"
                >
                    + {{ t('addFieldOption') }}
                </button>
            </div>
        </div>

        <InputError
            class="mb-3"
            :message="errors[`fields.${fieldIndex}.options`]"
        />

        <!-- Нет вариантов -->
        <div
            v-if="!modelValue.length"
            class="py-6 bg-white dark:bg-slate-900
                   text-center text-sm text-slate-500 dark:text-slate-400
                   border border-dashed border-slate-400 dark:border-slate-600
                   rounded-sm"
        >
            {{ t('fieldOptionsEmpty') }}
        </div>

        <!-- Варианты -->
        <div
            v-for="(option, optionIndex) in modelValue"
            :key="option.id ?? `new-option-${optionIndex}`"
            class="mb-3 last:mb-0 border border-slate-300 dark:border-slate-600
                   bg-white dark:bg-slate-900 rounded-sm"
        >
            <!-- Вариант отмечен на удаление -->
            <div
                v-if="option._delete"
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3
                       p-3 bg-red-50 dark:bg-red-950/30"
            >
                <div>
                    <div class="text-sm font-semibold text-red-700 dark:text-red-300">
                        {{ optionTitle(option) }}
                    </div>

                    <div class="mt-1 text-xs text-red-500 dark:text-red-400">
                        {{ option.value }} · ID: {{ option.id }}
                    </div>
                </div>

                <button
                    type="button"
                    @click="restoreOption(optionIndex)"
                    class="px-3 py-1.5 text-xs font-semibold rounded-sm
                           border border-red-400 dark:border-red-500
                           text-red-700 dark:text-red-300
                           hover:bg-red-100 dark:hover:bg-red-950"
                >
                    {{ t('undoDeletion') }}
                </button>
            </div>

            <template v-else>
                <!-- Заголовок варианта -->
                <div class="flex flex-col lg:flex-row lg:items-center
                            lg:justify-between gap-3 p-3
                            bg-slate-50 dark:bg-slate-700
                            border-b border-slate-300 dark:border-slate-600">

                    <button
                        type="button"
                        @click="toggleOption(option, optionIndex)"
                        class="flex items-center gap-3 text-left min-w-0"
                        :title="isCollapsed(option, optionIndex) ? t('expand') : t('collapse')"
                    >
                        <span class="flex items-center justify-center w-7 h-7 rounded-full
                                     bg-slate-200 dark:bg-slate-900 text-xs font-semibold
                                     text-slate-700 dark:text-slate-100 shrink-0">
                            {{ optionIndex + 1 }}
                        </span>

                        <div class="min-w-0">
                            <div class="text-sm font-semibold
                                        text-slate-800 dark:text-slate-100 truncate">
                                {{ optionTitle(option) }}
                            </div>

                            <div class="text-[11px] text-slate-500
                                        dark:text-slate-400 truncate">
                                {{ option.value || 'value' }}
                                · {{ t('sort') }}: {{ option.sort }}
                            </div>
                        </div>
                    </button>

                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            @click="toggleOption(option, optionIndex)"
                            class="flex items-center justify-center text-xs
                                   bg-white dark:bg-slate-800 w-8 h-8
                                   border border-slate-400 dark:border-slate-500 rounded-sm
                                   text-slate-700 dark:text-slate-200
                                   hover:bg-slate-100 dark:hover:bg-slate-950"
                            :title="isCollapsed(option, optionIndex) ? t('expand') : t('collapse')"
                        >
                            <svg
                                class="w-4 h-4 fill-current transition-transform duration-200"
                                :class="{ 'rotate-180': !isCollapsed(option, optionIndex) }"
                                viewBox="0 0 16 16"
                            >
                                <path d="M8 11 2.5 5.5 3.9 4.1 8 8.2l4.1-4.1 1.4 1.4z" />
                            </svg>
                        </button>

                        <button
                            type="button"
                            @click="moveOption(optionIndex, -1)"
                            :disabled="optionIndex === 0"
                            class="px-3 py-1 text-sm bg-white dark:bg-slate-800
                                   border border-slate-400 dark:border-slate-500 rounded-sm
                                   hover:bg-slate-100 dark:hover:bg-slate-950
                                   disabled:opacity-30 text-slate-700 dark:text-slate-200"
                        >
                            ↑
                        </button>

                        <button
                            type="button"
                            @click="moveOption(optionIndex, 1)"
                            :disabled="optionIndex === modelValue.length - 1"
                            class="px-3 py-1 text-sm bg-white dark:bg-slate-800
                                   border border-slate-400 dark:border-slate-500 rounded-sm
                                   hover:bg-slate-100 dark:hover:bg-slate-950
                                   disabled:opacity-30 text-slate-700 dark:text-slate-200"
                        >
                            ↓
                        </button>

                        <button
                            type="button"
                            @click="removeOption(optionIndex)"
                            class="px-2 py-1 flex flex-row items-center justify-center gap-1
                                   rounded-sm border border-red-400 dark:border-red-500
                                   hover:bg-red-50 dark:hover:bg-red-950"
                        >
                            <svg
                                class="shrink-0 h-3 w-3"
                                viewBox="0 0 448 512"
                            >
                                <path
                                    class="fill-current text-red-600 dark:text-red-300"
                                    d="M0 84V56c0-13.3 10.7-24 24-24h112l9.4-18.7c4-8.2 12.3-13.3 21.4-13.3h114.3c9.1 0 17.4 5.1 21.5 13.3L312 32h112c13.3 0 24 10.7 24 24v28c0 6.6-5.4 12-12 12H12C5.4 96 0 90.6 0 84zm416 56v324c0 26.5-21.5 48-48 48H80c-26.5 0-48-21.5-48-48V140c0-6.6 5.4-12 12-12h360c6.6 0 12 5.4 12 12zm-272 68c0-8.8-7.2-16-16-16s-16 7.2-16 16v224c0 8.8 7.2 16 16 16s16-7.2 16-16V208zm96 0c0-8.8-7.2-16-16-16s-16 7.2-16 16v224c0 8.8 7.2 16 16 16s16-7.2 16-16V208zm96 0c0-8.8-7.2-16-16-16s-16 7.2-16 16v224c0 8.8 7.2 16 16 16s16-7.2 16-16V208z"
                                />
                            </svg>

                            <span class="text-sm font-semibold
                                         text-red-600 dark:text-red-300">
                                {{ t('remove') }}
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Содержимое -->
                <div
                    v-show="!isCollapsed(option, optionIndex)"
                    class="p-3"
                >
                    <!-- Основные параметры -->
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
                        <!-- Value -->
                        <div class="flex flex-col items-start">
                            <LabelInput :for="`field_option_value_${fieldIndex}_${optionIndex}`">
                                <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                {{ t('systemValue') }}
                            </LabelInput>

                            <InputText
                                :id="`field_option_value_${fieldIndex}_${optionIndex}`"
                                v-model="option.value"
                                type="text"
                                maxlength="255"
                                autocomplete="off"
                                placeholder="office"
                            />

                            <InputError
                                class="mt-2"
                                :message="optionError(optionIndex, 'value')"
                            />
                        </div>

                        <!-- Sort -->
                        <div class="flex flex-col items-start">
                            <LabelInput
                                :for="`field_option_sort_${fieldIndex}_${optionIndex}`"
                                :value="t('sort')"
                            />

                            <InputNumber
                                :id="`field_option_sort_${fieldIndex}_${optionIndex}`"
                                v-model.number="option.sort"
                                type="number"
                                min="0"
                            />

                            <InputError
                                class="mt-2"
                                :message="optionError(optionIndex, 'sort')"
                            />
                        </div>

                        <!-- Состояния -->
                        <div class="flex flex-col justify-center gap-3 p-3
                                    bg-slate-50 dark:bg-slate-800
                                    border border-slate-200 dark:border-slate-600
                                    rounded-sm">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <ActivityCheckbox v-model="option.activity" />

                                <span class="text-sm text-slate-700 dark:text-slate-200">
                                    {{ t('actively') }}
                                </span>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer">
                                <ActivityCheckbox
                                    :model-value="option.is_default"
                                    @update:model-value="changeDefault(optionIndex)"
                                />

                                <span class="text-sm text-slate-700 dark:text-slate-200">
                                    {{ t('defaultValue') }}
                                </span>
                            </label>

                            <InputError
                                :message="optionError(optionIndex, 'activity')"
                            />

                            <InputError
                                :message="optionError(optionIndex, 'is_default')"
                            />
                        </div>
                    </div>

                    <!-- Перевод -->
                    <div class="p-3 my-4 border border-teal-200
                                dark:border-teal-800 bg-teal-50/40
                                dark:bg-slate-800 rounded-sm">

                        <div class="mb-3 text-xs font-semibold
                                    text-slate-700 dark:text-slate-200">
                            {{ t('optionTranslation') }}
                            [{{ activeLocale.toUpperCase() }}]
                        </div>

                        <!-- Label -->
                        <div class="flex flex-col items-start">
                            <LabelInput
                                :for="`field_option_label_${fieldIndex}_${optionIndex}`"
                            >
                                <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
                                {{ t('label') }}
                            </LabelInput>

                            <InputText
                                :id="`field_option_label_${fieldIndex}_${optionIndex}`"
                                v-model="optionTranslation(option).label"
                                type="text"
                                maxlength="255"
                                autocomplete="off"
                            />

                            <InputError
                                class="mt-2"
                                :message="optionTranslationError(optionIndex, 'label')"
                            />
                        </div>

                        <!-- Description -->
                        <div class="mt-3 flex flex-col items-start">
                            <LabelInput
                                :for="`field_option_description_${fieldIndex}_${optionIndex}`"
                                :value="t('description')"
                            />

                            <MetaDescTextarea
                                :id="`field_option_description_${fieldIndex}_${optionIndex}`"
                                v-model="optionTranslation(option).description"
                                class="w-full"
                            />

                            <InputError
                                class="mt-2"
                                :message="optionTranslationError(optionIndex, 'description')"
                            />
                        </div>
                    </div>

                    <!-- Settings -->
                    <div class="flex flex-col items-start">
                        <LabelInput
                            :for="`field_option_settings_${fieldIndex}_${optionIndex}`"
                            :value="`${t('settings')} (JSON)`"
                        />

                        <MetaDescTextarea
                            :id="`field_option_settings_${fieldIndex}_${optionIndex}`"
                            v-model="option.settings"
                            class="w-full"
                            placeholder="{}"
                        />

                        <InputError
                            class="mt-2"
                            :message="optionError(optionIndex, 'settings')"
                        />
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>
