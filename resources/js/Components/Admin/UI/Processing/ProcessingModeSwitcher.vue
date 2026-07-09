<script setup>
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
    mode: { type: String, default: 'frontend' },
    useServerProcessing: { type: Boolean, default: false },
    total: { type: Number, default: 0 },
    settingKey: { type: String, required: true },
})

const processing = ref(false)

const modes = computed(() => [
    { value: 'frontend', label: t('frontend') },
    { value: 'auto', label: t('auto') },
    { value: 'server', label: t('server') },
])

const actualMode = computed(() => (
    props.useServerProcessing
        ? 'S'
        : 'F'
))

const reloadPage = () => {
    window.location.reload()
}

const updateMode = (mode) => {
    if (mode === props.mode || processing.value) {
        return
    }

    processing.value = true

    router.put(
        route('admin.settings.updateSettingValue'),
        {
            key: props.settingKey,
            value: mode,
        },
        {
            preserveScroll: true,
            preserveState: false,

            onSuccess: () => {
                reloadPage()
            },

            onError: () => {
                processing.value = false
            },

            onFinish: () => {
                processing.value = false
            },
        }
    )
}
</script>

<template>
    <div
        class="inline-flex items-center gap-2 text-[11px] font-semibold"
        :title="`${mode} / ${actualMode} / ${total}`"
    >
        <div
            class="inline-flex overflow-hidden rounded-sm border
                   border-slate-400 bg-white shadow-sm
                   dark:border-slate-300 dark:bg-slate-800"
        >
            <button
                v-for="item in modes"
                :key="item.value"
                type="button"
                :disabled="processing"
                class="px-2.5 py-0.5 transition disabled:opacity-50"
                :class="mode === item.value
                    ? 'bg-blue-600 text-white border-2 border-blue-100 dark:border-blue-900'
                    : 'text-slate-600 hover:bg-slate-100 ' +
                     'dark:text-slate-200 dark:hover:bg-slate-700'"
                @click="updateMode(item.value)"
            >
                {{ item.label }}
            </button>
        </div>

        <div
            class="inline-flex items-center gap-1 rounded-sm border
                   border-slate-400 bg-white px-2.5 py-1 shadow-sm
                   text-slate-600 dark:border-slate-300
                   dark:bg-slate-800 dark:text-slate-200"
        >
            <span
                class="font-bold"
                :class="useServerProcessing
                    ? 'text-blue-600 dark:text-blue-300'
                    : 'text-red-500 dark:text-red-200'"
            >
                {{ actualMode }}
            </span>

            <span class="text-slate-700 dark:text-slate-300">/ {{ total }}</span>
        </div>
    </div>
</template>
