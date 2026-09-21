<script setup>
import { inject } from 'vue'

import CookieConsentPopup from '@/Components/Privacy/CookieConsentPopup.vue'
import AnalyticsVisitorTracker from '@/Components/Privacy/AnalyticsVisitorTracker.vue'

const isDark = inject('isDark')
</script>

<template>
    <div
        id="app"
        :class="[isDark ? 'is-dark' : '']"
    >
        <slot />

        <CookieConsentPopup />
        <AnalyticsVisitorTracker />
    </div>
</template>
