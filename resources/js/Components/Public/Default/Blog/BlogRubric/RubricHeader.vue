<script setup>
import { useI18n } from 'vue-i18n'
const { t } = useI18n()

defineProps({
    rubric: { type: Object, required: true },
})
</script>

<template>
    <div class="mx-auto max-w-9 py-14">
        <h3 class="subtitle is-5 is-bold text-center">
            <span class="text-gradient">Наш блог сообщества</span>
        </h3>

        <h1 class="flex items-center justify-center title is-2 is-bold is-leading">
            <span v-if="rubric.icon" class="flex justify-center" v-html="rubric.icon" />
            <span class="text-gray-700 dark:text-gray-300 text-xl sm:text-5xl">
                  {{ rubric.title }}
                </span>
            <span
                class="ml-2 w-6 h-5 flex items-center justify-center rounded-full
                           text-[9px] font-semibold text-black dark:text-white
                           bg-slate-100 dark:bg-slate-700"
                :title="t('views')"
            >
                  {{ rubric.views }}
                </span>
        </h1>

        <div class="mx-auto max-w-6 mt-2">
            <p class="paragraph rem-115">{{ rubric.short }}</p>
        </div>
    </div>
</template>
