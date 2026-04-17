<script setup>
import { defineProps, defineEmits } from 'vue'
import LabelInput from '@/Components/Admin/Input/LabelInput.vue'
import InputError from '@/Components/Admin/Input/InputError.vue'

const props = defineProps({
    id: {
        type: String,
        required: true,
    },
    modelValue: {
        type: [Number, String, null],
        default: null,
    },
    label: {
        type: String,
        default: '',
    },
    required: {
        type: Boolean,
        default: false,
    },
    options: {
        type: Array,
        default: () => [],
        // [{ id: 1, label: '...' }, ...]
    },
    errorMessage: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: '',
    },
    /**
     * Если true — первый option будет "не выбрано" (value = null)
     */
    nullable: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['update:modelValue'])

const onChange = (event) => {
    const value = event.target.value

    // Если nullable и выбрано пустое значение — отправляем null
    if (props.nullable && (value === '' || value === 'null')) {
        emit('update:modelValue', null)
        return
    }

    // Пробуем преобразовать в число, если это похоже на число
    const num = Number(value)
    if (!Number.isNaN(num) && value !== '') {
        emit('update:modelValue', num)
    } else {
        emit('update:modelValue', value)
    }
}
</script>

<template>
    <div class="flex flex-col items-start">
        <LabelInput :for="id">
            <template v-if="required">
                <span class="text-red-500 dark:text-red-300 font-semibold">*</span>
            </template>
            {{ label }}
        </LabelInput>

        <select
            :id="id"
            :value="modelValue ?? (nullable ? null : '')"
            @change="onChange"
            class="w-full px-3 py-0.5 form-select bg-white dark:bg-cyan-800
                   text-gray-600 dark:text-slate-100 rounded-sm shadow-sm
                   border border-slate-400 dark:border-slate-600 whitespace-pre-line"
        >
            <!-- Опция "не выбрано", если nullable -->
            <option
                v-if="nullable"
                :value="null"
                class="text-xs whitespace-pre-line"
            >
                {{ placeholder || '—' }}
            </option>

            <!-- Если не nullable и задан placeholder — как первая пустая опция -->
            <option
                v-else-if="placeholder"
                value=""
                class="text-xs whitespace-pre-line"
            >
                {{ placeholder }}
            </option>

            <option
                v-for="opt in options"
                :key="opt.id"
                :value="opt.id"
                class="text-xs whitespace-pre-line"
            >
                {{ opt.label }}
            </option>
        </select>

        <InputError
            v-if="errorMessage"
            class="mt-2"
            :message="errorMessage"
        />
    </div>
</template>
