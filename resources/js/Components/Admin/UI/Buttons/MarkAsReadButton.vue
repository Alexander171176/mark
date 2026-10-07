<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
    title: {
        type: String,
        default: '',
    },

    disabled: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits([
    'read',
])

const buttonTitle = computed(() => {
    return props.title || t('markAsRead')
})

const buttonClass = computed(() => [
    'group flex items-center justify-center p-1 px-1 rounded',
    'border border-slate-300 dark:border-blue-300',
    'transition-colors duration-200 ease-out',
    'hover:border-blue-500 dark:hover:border-blue-100',
    'hover:bg-blue-50 dark:hover:bg-blue-900/30',
    'hover:shadow-sm hover:shadow-blue-500/20',
    'focus:outline-none focus:ring-2 focus:ring-blue-400/40',
])
</script>

<template>
    <div class="text-center">
        <button
            type="button"
            :class="buttonClass"
            :title="buttonTitle"
            :disabled="disabled"
            @click="emit('read')"
        >
            <svg
                class="w-4 h-4 shrink-0 fill-current text-blue-500
                       transition-colors duration-200 ease-out
                       group-hover:text-blue-600
                       dark:group-hover:text-blue-300"
                viewBox="0 0 448 512"
            >
                <path
                    d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.2 0z"
                />
            </svg>
        </button>
    </div>
</template>
