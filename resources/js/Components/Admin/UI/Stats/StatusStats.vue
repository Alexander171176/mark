
<script setup>
/**
 * PulsarCMS 1.0
 *
 * Универсальная статистика по статусам.
 */

import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t, te } = useI18n()

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({}),
    },

    // Статус в БД => ключ перевода
    statuses: {
        type: Object,
        default: () => ({}),
    },

    columns: {
        type: String,
        default: 'grid-cols-2 sm:grid-cols-3 lg:grid-cols-6',
    },
})

const statusEntries = computed(() =>
    Object.entries(props.statuses).map(([key, translationKey]) => ({
        key,

        label: translationKey && te(translationKey)
            ? t(translationKey)
            : key,

        total: Number(props.stats[key] ?? 0),
    }))
)
</script>

<template>
    <div :class="['grid gap-2 mb-3', columns]">
        <div
            v-for="item in statusEntries"
            :key="item.key"
            class="p-3 border border-slate-300 dark:border-slate-500
                   bg-white dark:bg-slate-800 text-center rounded-sm"
        >
            <div class="text-xs text-slate-500 dark:text-slate-300">
                {{ item.label }}
            </div>

            <div class="text-xl font-bold text-slate-800 dark:text-white">
                {{ item.total }}
            </div>
        </div>
    </div>
</template>
