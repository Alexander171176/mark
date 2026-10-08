<script setup>
import { computed, ref } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    href: {
        type: String,
        default: null
    },
    type: {
        type: String,
        default: 'submit'
    },
    disabled: {
        type: Boolean,
        default: false
    }
})

const isPressed = ref(false)

const componentType = computed(() => {
    return props.href ? Link : 'button'
})

const buttonClasses = computed(() => [
    'flex items-center',
    'btn px-2 py-0.5',
    'bg-teal-500 shadow-md',
    'text-white text-sm font-semibold',
    'transition-colors duration-300 ease-in-out',
    'hover:bg-teal-600 focus:bg-teal-600 focus:outline-none',
    {
        'ring-2 ring-teal-500 ring-offset-2 ring-offset-white':
        isPressed.value,
        'opacity-50 cursor-not-allowed':
        props.disabled
    }
])
</script>

<template>
    <component
        :is="componentType"
        :href="href || undefined"
        :type="href ? undefined : type"
        :disabled="href ? undefined : disabled"
        :aria-disabled="href && disabled ? 'true' : undefined"
        :tabindex="href && disabled ? -1 : undefined"
        :class="buttonClasses"
        @mousedown="isPressed = true"
        @mouseup="isPressed = false"
        @mouseleave="isPressed = false"
        @click="disabled && $event.preventDefault()"
    >
        <span>
            <slot name="icon">
                <svg
                    class="w-4 h-4 fill-current text-slate-100"
                    viewBox="0 0 16 16"
                >
                    <path
                        d="M14.3 2.3L5 11.6 1.7 8.3
                           c-.4-.4-1-.4-1.4 0
                           -.4.4-.4 1 0 1.4l4 4
                           c.2.2.4.3.7.3
                           .3 0 .5-.1.7-.3l10-10
                           c.4-.4.4-1 0-1.4
                           -.4-.4-1-.4-1.4 0z"
                    />
                </svg>
            </slot>
        </span>

        <span class="hidden xs:block ml-2">
            <slot />
        </span>
    </component>
</template>
