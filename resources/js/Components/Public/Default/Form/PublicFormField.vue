<script setup>
import { computed } from 'vue'
import PublicFileDropzone from '@/Components/Public/Default/Form/PublicFileDropzone.vue'

const props = defineProps({
    field: {
        type: Object,
        required: true,
    },

    modelValue: {
        type: [String, Number, Boolean, Array, Object, File],
        default: null,
    },

    error: {
        type: String,
        default: null,
    },
})

const emit = defineEmits([
    'update:modelValue',
])

/**
 * Перевод поля.
 */
const translation = computed(() => {
    return props.field.translation || {}
})

/**
 * Активные варианты выбора.
 */
const options = computed(() => {
    return Array.isArray(props.field.options)
        ? props.field.options
        : []
})

/**
 * CSS-ширина поля.
 */
const widthClass = computed(() => {
    const widths = {
        full: 'md:col-span-2',
        '1/2': 'md:col-span-1',
        '1/3': 'md:col-span-1',
        '2/3': 'md:col-span-2',
    }

    return widths[props.field.width] || 'md:col-span-2'
})

/**
 * Общие классы input/select/textarea.
 */
const controlClass = computed(() => {
    return [
        'block w-full rounded-md border bg-white px-3 py-2',
        'text-sm text-gray-900 shadow-sm transition',
        'placeholder:text-gray-400',
        'focus:outline-none focus:ring-2',
        'disabled:cursor-not-allowed disabled:bg-gray-100 disabled:opacity-70',
        'dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500',
        'dark:disabled:bg-gray-800',

        props.error
            ? 'border-red-400 focus:border-red-400 focus:ring-red-400/80 dark:border-red-400'
            : 'border-gray-400 focus:border-sky-400 focus:ring-sky-400/80 dark:border-gray-500',
    ]
})

/**
 * Обычные HTML input-типы.
 */
const inputTypes = [
    'text',
    'email',
    'tel',
    'number',
    'date',
    'url',
    'range',
]

/**
 * HTML-тип input.
 */
const inputType = computed(() => {
    return inputTypes.includes(props.field.type)
        ? props.field.type
        : 'text'
})

/**
 * Значение обычного поля.
 */
const updateValue = (event) => {
    emit(
        'update:modelValue',
        event.target.value
    )
}

/**
 * Checkbox.
 */
const updateCheckbox = (event) => {
    emit(
        'update:modelValue',
        event.target.checked
    )
}

/**
 * Checkbox group.
 */
const updateCheckboxGroup = (optionValue, checked) => {
    const current = Array.isArray(props.modelValue)
        ? [...props.modelValue]
        : []

    if (checked) {
        if (!current.includes(optionValue)) {
            current.push(optionValue)
        }
    } else {
        const index = current.indexOf(optionValue)

        if (index !== -1) {
            current.splice(index, 1)
        }
    }

    emit(
        'update:modelValue',
        current
    )
}

/**
 * Проверка выбранного checkbox-group option.
 */
const isOptionChecked = (value) => {
    return Array.isArray(props.modelValue) &&
        props.modelValue.includes(value)
}

/**
 * Rows для textarea.
 */
const textareaRows = computed(() => {
    const rows = Number(
        props.field.settings?.rows
    )

    return Number.isFinite(rows) && rows > 0
        ? rows
        : 4
})

/**
 * Maxlength.
 */
const maxlength = computed(() => {
    const value = Number(
        props.field.settings?.maxlength
    )

    return Number.isFinite(value) && value > 0
        ? value
        : undefined
})
</script>

<template>
    <!-- Hidden -->
    <input
        v-if="field.type === 'hidden'"
        type="hidden"
        :name="field.name"
        :value="modelValue ?? ''"
    />

    <!-- Обычное поле -->
    <div
        v-else
        :class="widthClass"
    >
        <!-- Label -->
        <label
            v-if="
                field.type !== 'checkbox' &&
                translation.label
            "
            :for="`public-form-field-${field.id}`"
            class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200"
        >
            {{ translation.label }}

            <span
                v-if="field.required"
                class="text-red-500"
            >
                *
            </span>
        </label>

        <!-- Text / email / tel / number / date / url / range -->
        <input
            v-if="inputTypes.includes(field.type)"
            :id="`public-form-field-${field.id}`"
            :name="field.name"
            :type="inputType"
            :value="modelValue ?? ''"
            :placeholder="translation.placeholder || undefined"
            :required="field.required"
            :readonly="field.readonly"
            :disabled="field.disabled"
            :maxlength="maxlength"
            :class="controlClass"
            @input="updateValue"
        />

        <!-- Datetime -->
        <input
            v-else-if="field.type === 'datetime'"
            :id="`public-form-field-${field.id}`"
            :name="field.name"
            type="datetime-local"
            :value="modelValue ?? ''"
            :placeholder="translation.placeholder || undefined"
            :required="field.required"
            :readonly="field.readonly"
            :disabled="field.disabled"
            :class="controlClass"
            @input="updateValue"
        />

        <!-- Textarea -->
        <textarea
            v-else-if="field.type === 'textarea'"
            :id="`public-form-field-${field.id}`"
            :name="field.name"
            :value="modelValue ?? ''"
            :rows="textareaRows"
            :maxlength="maxlength"
            :placeholder="translation.placeholder || undefined"
            :required="field.required"
            :readonly="field.readonly"
            :disabled="field.disabled"
            :class="controlClass"
            @input="updateValue"
        />

        <!-- Select -->
        <select
            v-else-if="field.type === 'select'"
            :id="`public-form-field-${field.id}`"
            :name="field.name"
            :value="modelValue ?? ''"
            :required="field.required"
            :disabled="field.disabled"
            :class="controlClass"
            @change="updateValue"
        >
            <option
                v-if="translation.placeholder"
                value=""
                :disabled="field.required"
            >
                {{ translation.placeholder }}
            </option>

            <option
                v-for="option in options"
                :key="option.id ?? option.value"
                :value="option.value"
            >
                {{ option.translation?.label || option.value }}
            </option>
        </select>

        <!-- Radio -->
        <div
            v-else-if="field.type === 'radio'"
            class="space-y-2"
        >
            <label
                v-for="option in options"
                :key="option.id ?? option.value"
                class="flex cursor-pointer items-start gap-3 rounded-xl
                       border border-gray-200 px-4 py-3 transition
                       hover:border-sky-300 hover:bg-sky-50/50
                       dark:border-gray-700 dark:hover:border-sky-700
                       dark:hover:bg-gray-700/40"
            >
                <input
                    type="radio"
                    :name="field.name"
                    :value="option.value"
                    :checked="modelValue === option.value"
                    :required="field.required"
                    :disabled="field.disabled"
                    class="mt-0.5 h-4 w-4 border-gray-300 text-sky-600
                           focus:ring-sky-500 dark:border-gray-600"
                    @change="$emit('update:modelValue', option.value)"
                />

                <span class="min-w-0">
                    <span
                        class="block text-sm font-medium text-gray-800 dark:text-gray-200"
                    >
                        {{ option.translation?.label || option.value }}
                    </span>

                    <span
                        v-if="option.translation?.description"
                        class="mt-0.5 block text-xs leading-5 text-gray-500 dark:text-gray-400"
                    >
                        {{ option.translation.description }}
                    </span>
                </span>
            </label>
        </div>

        <!-- Checkbox -->
        <label
            v-else-if="field.type === 'checkbox'"
            class="flex cursor-pointer items-start gap-3"
        >
            <input
                :id="`public-form-field-${field.id}`"
                :name="field.name"
                type="checkbox"
                :checked="Boolean(modelValue)"
                :required="field.required"
                :disabled="field.disabled"
                class="mt-0.5 h-4 w-4 rounded border-gray-300 text-sky-600
                       focus:ring-sky-500 dark:border-gray-600"
                @change="updateCheckbox"
            />

            <span class="min-w-0">
                <span
                    v-if="translation.label"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-200"
                >
                    {{ translation.label }}

                    <span
                        v-if="field.required"
                        class="text-red-500"
                    >
                        *
                    </span>
                </span>

                <span
                    v-if="translation.description"
                    class="mt-0.5 block text-xs leading-5 text-gray-500 dark:text-gray-400"
                >
                    {{ translation.description }}
                </span>
            </span>
        </label>

        <!-- Checkbox group -->
        <div
            v-else-if="field.type === 'checkbox_group'"
            class="space-y-2"
        >
            <label
                v-for="option in options"
                :key="option.id ?? option.value"
                class="flex cursor-pointer items-start gap-3 rounded-xl
                       border border-gray-200 px-4 py-3 transition
                       hover:border-sky-300 hover:bg-sky-50/50
                       dark:border-gray-700 dark:hover:border-sky-700
                       dark:hover:bg-gray-700/40"
            >
                <input
                    type="checkbox"
                    :name="`${field.name}[]`"
                    :value="option.value"
                    :checked="isOptionChecked(option.value)"
                    :disabled="field.disabled"
                    class="mt-0.5 h-4 w-4 rounded border-gray-300 text-sky-600
                           focus:ring-sky-500 dark:border-gray-600"
                    @change="updateCheckboxGroup(
                        option.value,
                        $event.target.checked
                    )"
                />

                <span class="min-w-0">
                    <span
                        class="block text-sm font-medium text-gray-800 dark:text-gray-200"
                    >
                        {{ option.translation?.label || option.value }}
                    </span>

                    <span
                        v-if="option.translation?.description"
                        class="mt-0.5 block text-xs leading-5 text-gray-500 dark:text-gray-400"
                    >
                        {{ option.translation.description }}
                    </span>
                </span>
            </label>
        </div>

        <!-- File -->
        <PublicFileDropzone
            v-else-if="field.type === 'file'"
            :field="field"
            :model-value="modelValue"
            @update:model-value="$emit('update:modelValue', $event)"
        />

        <!-- Неизвестный тип -->
        <div
            v-else
            class="rounded-xl border border-amber-300 bg-amber-50
                   px-4 py-3 text-sm text-amber-800
                   dark:border-amber-800 dark:bg-amber-950/30
                   dark:text-amber-300"
        >
            Неподдерживаемый тип поля:
            <strong>{{ field.type }}</strong>
        </div>

        <!-- Описание -->
        <p
            v-if="
                translation.description &&
                field.type !== 'checkbox'
            "
            class="mt-1.5 text-center text-xs leading-5 text-gray-600 dark:text-gray-400"
        >
            {{ translation.description }}
        </p>

        <!-- Ошибка -->
        <p
            v-if="error"
            class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400"
        >
            {{ error }}
        </p>
    </div>
</template>
