<script setup>
import { useI18n } from 'vue-i18n'

import WelcomePageBuilderGrid from '@/Components/Admin/Welcome/Grids/WelcomePageBuilderGrid.vue'
import WelcomePageFinanceGrid from '@/Components/Admin/Welcome/Grids/WelcomePageFinanceGrid.vue'
import WelcomeEducationGrid from '@/Components/Admin/Welcome/Grids/WelcomeEducationGrid.vue'
import WelcomeBlogGrid from '@/Components/Admin/Welcome/Grids/WelcomeBlogGrid.vue'
import WelcomeSystemGrid from '@/Components/Admin/Welcome/Grids/WelcomeSystemGrid.vue'

const { t } = useI18n()
</script>

<template>
    <div>
        <WelcomePageBuilderGrid :t="t" />
        <WelcomePageFinanceGrid :t="t" />
        <WelcomeEducationGrid :t="t" />
        <WelcomeBlogGrid :t="t" />
        <WelcomeSystemGrid :t="t" />
    </div>
</template>
