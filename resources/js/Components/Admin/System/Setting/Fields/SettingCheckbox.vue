<script setup>
import {
    computed,
    defineEmits,
    defineProps,
} from 'vue'

import SettingsCheckbox from '@/Components/Admin/System/Setting/Input/SettingsCheckbox.vue'

const props = defineProps({
    modelValue: {
        type: String,
        default: 'false',
    },

    id: {
        type: String,
        required: true,
    },
})

const emit = defineEmits([
    'update:modelValue',
])

/**
 * Нормализация значения checkbox.
 *
 * SettingsCheckbox работает
 * со строками "true" / "false".
 */
const checkboxValue = computed({
    get() {
        return props.modelValue === 'true'
            ? 'true'
            : 'false'
    },

    set(value) {
        emit(
            'update:modelValue',
            value === 'true'
                ? 'true'
                : 'false'
        )
    },
})
</script>

<template>
    <div class="flex items-center">
        <SettingsCheckbox
            :id="id"
            v-model="checkboxValue"
        />
    </div>
</template>
