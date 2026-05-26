<script setup>
import { defineProps, inject } from 'vue'

defineProps({
    title: String,
    canLogin: Boolean,
    canRegister: Boolean,
})

const isDark = inject('isDark')
</script>

<template>
    <div id="app" :class="[isDark ? 'is-dark' : '']">
        <slot />
    </div>
</template>
