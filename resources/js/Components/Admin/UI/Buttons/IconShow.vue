<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
    href: {
        type: String,
        required: true,
    },

    title: {
        type: String,
        default: '',
    },
})

const buttonTitle = computed(() => {
    return props.title || t('view')
})

const buttonClass = computed(() => [
    'group flex items-center justify-center p-1 px-1 rounded',
    'border border-slate-300 dark:border-blue-300',
    'transition-all duration-200 ease-out',
    'hover:border-blue-500 dark:hover:border-blue-100',
    'hover:bg-blue-50 dark:hover:bg-blue-900/30',
    'hover:shadow-md hover:shadow-blue-500/20',
    'hover:-translate-y-0.5',
    'active:translate-y-0 active:scale-90',
    'focus:outline-none focus:ring-2 focus:ring-blue-400/40',
])
</script>

<template>
    <div class="text-center">
        <Link
            :href="href"
            :class="buttonClass"
            :title="buttonTitle"
        >
            <svg
                class="w-4 h-4 shrink-0 fill-current text-blue-500
                       transition-all duration-200 ease-out
                       group-hover:scale-110 group-hover:text-blue-600
                       group-active:scale-90
                       dark:group-hover:text-blue-300"
                viewBox="0 0 16 16"
            >
                <path
                    d="M5 9h11v2H5V9zM0 9h3v2H0V9zm5 4h6v2H5v-2zm-5 0h3v2H0v-2zm5-8h7v2H5V5zM0 5h3v2H0V5zm5-4h11v2H5V1zM0 1h3v2H0V1z"
                />
            </svg>
        </Link>
    </div>
</template>
