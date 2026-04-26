<script setup>
import { useI18n } from 'vue-i18n'

import WelcomePageFinanceGrid from '@/Components/Admin/Welcome/Grids/WelcomePageFinanceGrid.vue'
import WelcomeEducationGrid from '@/Components/Admin/Welcome/Grids/WelcomeEducationGrid.vue'
import WelcomeBlogGrid from '@/Components/Admin/Welcome/Grids/WelcomeBlogGrid.vue'
import WelcomeSystemGrid from '@/Components/Admin/Welcome/Grids/WelcomeSystemGrid.vue'

const { t } = useI18n()
</script>

<template>
    <div>
        <WelcomePageFinanceGrid :t="t" />
        <WelcomeEducationGrid :t="t" />
        <WelcomeBlogGrid :t="t" />
        <WelcomeSystemGrid :t="t" />
    </div>
</template>
