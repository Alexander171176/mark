<script setup>
import { ref, watch } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import ProcessingModeSwitcher from '@/Components/Admin/UI/Processing/ProcessingModeSwitcher.vue'

const { t } = useI18n()

const props = defineProps({
    settingKey: { type: String, required: true },
    mode: { type: String, default: 'server' },
    useServerProcessing: { type: Boolean, default: false },
    total: { type: Number, default: 0 },
    storageKey: { type: String, default: 'public_admin_panel_collapsed' },
})

const getStoredBoolean = (key, defaultValue = false) => {
    const value = localStorage.getItem(key)

    if (value === null) {
        return defaultValue
    }

    return value === 'true'
}

const collapsed = ref(getStoredBoolean(props.storageKey, false))

watch(collapsed, (value) => {
    localStorage.setItem(props.storageKey, String(value))
})

const togglePanel = () => {
    collapsed.value = !collapsed.value
}
</script>

<template>
    <div class="fixed bottom-0 left-0 right-0 z-[9999]">
        <button
            type="button"
            class="absolute left-1/2 -translate-x-1/2
                   flex h-4 w-10 items-center justify-center
                   rounded-t-full border border-b-0
                   border-slate-400/60
                   bg-slate-300/95 dark:bg-slate-700/95
                   text-slate-700 dark:text-slate-300
                   shadow-md backdrop-blur-md
                   hover:text-indigo-600 dark:hover:text-indigo-300"
            :class="collapsed ? 'bottom-0' : 'bottom-12 sm:bottom-8'"
            :title="collapsed ? t('show') : t('hide')"
            @click="togglePanel"
        >
            <svg
                class="h-3 w-3 transition-transform duration-300"
                :class="collapsed ? 'rotate-180' : ''"
                fill="currentColor"
                viewBox="0 0 320 512"
            >
                <path
                    d="M182.6 137.4c-12.5-12.5-32.8-12.5-45.3 0l-128 128c-9.2 9.2-11.9 22.9-6.9 34.9S19.1 320 32 320h256c12.9 0 24.6-7.8 29.6-19.8s2.2-25.7-6.9-34.9l-128-128z"
                />
            </svg>
        </button>

        <div
            class="min-h-10 sm:min-h-8
                   flex flex-col sm:flex-row
                   items-stretch sm:items-center
                   justify-between gap-2
                   px-2 sm:px-3 py-2 sm:py-0
                   border-t border-slate-400/40
                   bg-slate-300/90 dark:bg-slate-700/90
                   backdrop-blur-md text-[11px]
                   transition-transform duration-300"
            :class="collapsed ? 'translate-y-full' : 'translate-y-0'"
        >
            <div class="flex items-center justify-center sm:justify-start gap-2">
                <Link
                    :href="route('admin.index')"
                    :title="t('adminPanel')"
                    class="w-fit"
                >
                    <svg class="shrink-0 h-7 w-7 fill-current text-cyan-600 dark:text-cyan-400"
                         viewBox="0 0 512 512">
                        <path d="M157.52 272h36.96L176 218.78 157.52 272zM352 256c-13.23 0-24 10.77-24 24s10.77 24 24 24 24-10.77 24-24-10.77-24-24-24zM464 64H48C21.5 64 0 85.5 0 112v288c0 26.5 21.5 48 48 48h416c26.5 0 48-21.5 48-48V112c0-26.5-21.5-48-48-48zM250.58 352h-16.94c-6.81 0-12.88-4.32-15.12-10.75L211.15 320h-70.29l-7.38 21.25A16 16 0 0 1 118.36 352h-16.94c-11.01 0-18.73-10.85-15.12-21.25L140 176.12A23.995 23.995 0 0 1 162.67 160h26.66A23.99 23.99 0 0 1 212 176.13l53.69 154.62c3.61 10.4-4.11 21.25-15.11 21.25zM424 336c0 8.84-7.16 16-16 16h-16c-4.85 0-9.04-2.27-11.98-5.68-8.62 3.66-18.09 5.68-28.02 5.68-39.7 0-72-32.3-72-72s32.3-72 72-72c8.46 0 16.46 1.73 24 4.42V176c0-8.84 7.16-16 16-16h16c8.84 0 16 7.16 16 16v160z" />
                    </svg>
                </Link>

                <slot name="left" />
            </div>

            <div class="flex items-center justify-center sm:justify-end gap-2 overflow-x-auto">
                <slot name="right-before" />

                <ProcessingModeSwitcher
                    :setting-key="settingKey"
                    :mode="mode"
                    :use-server-processing="useServerProcessing"
                    :total="total"
                />

                <slot name="right-after" />
            </div>
        </div>
    </div>
</template>
