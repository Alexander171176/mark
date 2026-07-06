<script setup>
import { defineProps, inject } from 'vue'
import CookieConsentPopup from '@/Components/Privacy/CookieConsentPopup.vue'
import AnalyticsVisitorTracker from '@/Components/Privacy/AnalyticsVisitorTracker.vue'

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
        <CookieConsentPopup />
        <AnalyticsVisitorTracker />
    </div>
</template>
