<script setup>
import { defineProps, defineEmits, computed } from 'vue'

defineProps({
    isActive: Boolean,
    title: String,
})

const emit = defineEmits(['toggle-left'])

const buttonClass = computed(() => [
    'flex items-center',
])
</script>

<template>
    <button :class="buttonClass" :title="title" @click="emit('toggle-left')">
        <svg v-if="isActive"
             class="w-6 h-6 fill-current
                    text-amber-500
                    hover:text-slate-700
                    dark:hover:text-slate-900"
             stroke="currentColor" viewBox="0 0 24 24">
            <path class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16"></path>
            <path class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"></path>
        </svg>
        <svg v-else
             class="w-6 h-6 fill-current
                    text-gray-500
                    hover:text-rose-500
                    dark:hover:text-red-400"
             stroke="currentColor" viewBox="0 0 24 24">
            <path class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16"></path>
            <path class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>
</template>
