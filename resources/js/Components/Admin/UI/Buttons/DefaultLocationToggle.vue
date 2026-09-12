<script setup>
import { defineEmits, defineProps } from 'vue'

const props = defineProps({
    isDefault: {
        type: Boolean,
        default: false,
    },

    disabled: {
        type: Boolean,
        default: false,
    },

    title: {
        type: String,
        default: '',
    },
})

const emit = defineEmits([
    'make-default',
])

const handleClick = () => {
    if (props.disabled) {
        return
    }

    emit('make-default')
}
</script>

<template>
    <button
        type="button"
        class="relative inline-flex
               h-5 w-9 shrink-0
               rounded-full
               border-2 border-transparent
               transition-colors duration-200
               focus:outline-none"
        :class="[
            isDefault
                ? 'bg-amber-500'
                : 'bg-slate-300 dark:bg-slate-600',

            disabled
                ? 'cursor-not-allowed opacity-70'
                : 'cursor-pointer',
        ]"
        :title="title"
        @click="handleClick"
    >
        <span
            class="pointer-events-none
                   inline-block h-4 w-4
                   transform rounded-full
                   bg-white shadow
                   transition duration-200"
            :class="
                isDefault
                    ? 'translate-x-4'
                    : 'translate-x-0'
            "
        />
    </button>
</template>
