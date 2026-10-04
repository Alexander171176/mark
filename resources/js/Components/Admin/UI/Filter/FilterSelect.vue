<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
    modelValue: {
        type: [String, Number, Boolean],
        default: '',
    },

    label: {
        type: String,
        default: '',
    },

    placeholder: {
        type: String,
        default: '',
    },

    options: {
        type: [Array, Object],
        default: () => [],
    },

    disabled: {
        type: Boolean,
        default: false,
    },
})

const emits = defineEmits([
    'update:modelValue',
])

const resolvedPlaceholder = computed(() => {
    return props.placeholder || t('all')
})

const normalizedOptions = computed(() => {
    if (Array.isArray(props.options)) {
        return props.options.map((option) => {
            if (option !== null && typeof option === 'object') {
                return {
                    value: option.value ?? option.id ?? '',
                    label:
                        option.label
                        ?? option.name
                        ?? option.title
                        ?? option.value
                        ?? option.id
                        ?? '',
                }
            }

            return {
                value: option,
                label: option,
            }
        })
    }

    if (props.options && typeof props.options === 'object') {
        return Object.entries(props.options).map(([value, label]) => ({
            value,
            label,
        }))
    }

    return []
})

const updateValue = (event) => {
    emits('update:modelValue', event.target.value)
}
</script>

<template>
    <div>
        <label
            v-if="label"
            class="mb-1 block text-sm font-medium
                   text-indigo-700 dark:text-indigo-300"
        >
            {{ label }}
        </label>

        <select
            :value="modelValue"
            :disabled="disabled"
            class="px-3 py-1 w-full rounded-sm border-gray-400 text-sm shadow-sm
                   dark:border-gray-500 dark:bg-gray-700 dark:text-gray-100
                   disabled:cursor-not-allowed disabled:opacity-60"
            @change="updateValue"
        >
            <option value="">
                {{ resolvedPlaceholder }}
            </option>

            <option
                v-for="option in normalizedOptions"
                :key="String(option.value)"
                :value="option.value"
            >
                {{ option.label }}
            </option>
        </select>
    </div>
</template>
